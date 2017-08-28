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

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_invoice_lines", "invoice_lines");
        $this->_pLoad->model("Zeapps_modalities", "modalities");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        var_dump($src->id_modality);
        var_dump($src->date_creation);
        var_dump($src->date_limit);

        $src->date_creation = date('Y-m-d');

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

        var_dump($src->id_modality);
        var_dump($src->date_creation);
        var_dump($src->date_limit);

        $id = parent::insert($src);

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_invoice = $id;

                $this->_pLoad->ctrl->invoice_lines->insert($line);
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