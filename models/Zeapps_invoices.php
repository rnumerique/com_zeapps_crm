<?php
class Zeapps_invoices extends ZeModel {
    public function get_numerotation(){
        $query = 'SELECT * FROM zeapps_invoices';
        return sizeof($this->database()->customQuery($query)->result()) + 1;
    }

    public function getDueOf($type, $id = null){
        $total = 0;
        $invoices = $this->all(array('id_'.$type => $id, 'due >' => 0));

        if($invoices) {
            foreach ($invoices as $invoice){
                $total += floatval($invoice->due);
            }
        }

        return array('due' => $total, 'due_lines' => $invoices);
    }

    public function getByMonth($year = 0, $month = 0){
        $query = "SELECT * FROM zeapps_invoices WHERE YEAR(date_limit) = ".$year." AND MONTH(date_limit) = ".$month." AND finalized = 1 AND deleted_at IS NULL";

        return $this->database()->customQuery($query)->result();
    }

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_invoice_lines", "invoice_lines");
        $this->_pLoad->model("Zeapps_invoice_line_details", "invoice_line_details");
        $this->_pLoad->model("Zeapps_modalities", "modalities");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $src->date_creation = date('Y-m-d');
        $src->finalized = 0;

        if(isset($src->id_modality)) {
            if($modality = $this->_pLoad->ctrl->modalities->get($src->id_modality)){
                if($modality->settlement_type === '0'){
                    $src->date_limit = date("Y-m-d", strtotime("+".$modality->settlement_delay." day", time()));
                }
                elseif($modality->settlement_type === '1'){
                    $year = date("Y", strtotime("+".$modality->settlement_delay." day", time()));
                    $month = date("m", strtotime("+".$modality->settlement_delay." day", time()));
                    $day = date("d", strtotime("+".$modality->settlement_delay." day", time()));
                    if(intval($day) <= $modality->settlement_date){
                        $src->date_limit = $year."-".$month."-".$modality->settlement_date;
                    }
                    else{
                        $date = date("Y-m", strtotime("+1 month", strtotime("+".$modality->settlement_delay." day", time())));
                        $src->date_limit = $date."-".$modality->settlement_date;
                    }
                }
            }
        }

        $id = parent::insert($src);

        $new_id_lines = [];

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_invoice = $id;

                $new_id = $this->_pLoad->ctrl->invoice_lines->insert($line);

                $new_id_lines[$old_id] = $new_id;
            }
        }

        if(isset($src->line_details) && is_array($src->line_details)){
            foreach($src->line_details as $line){
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_invoice = $id;
                $line->id_line = $new_id_lines[$line->id_line];

                $this->_pLoad->ctrl->invoice_line_details->insert($line);
            }
        }

        return $id;
    }

    public function parseFormat($result = null, $num = null)
    {
        if ($result && $num){
            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(x+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                },
                $result);

            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(X+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    if (strlen($matches[1]) > strlen($num)) {
                        return str_replace($matches[1], str_pad($num, strlen($matches[1]), '0', STR_PAD_LEFT), $matches[0]);
                    } else {
                        return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                    }
                },
                $result);

            $timestamp = time();

            $result = preg_replace_callback('/[[xX0-9\-_]*([dDjzmMnyYgGhH]+)[xX0-9\-_]*[]\/\-_]/',
                function ($matches) use ($timestamp) {
                    foreach ($matches as $match) {
                        return date($match, $timestamp);
                    }
                    return true;
                },
                $result);

            $result = str_replace(array('[', ']'), '', $result);

            return $result;
        }
        return false;
    }
}