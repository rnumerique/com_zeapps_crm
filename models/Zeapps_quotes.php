<?php
class Zeapps_quotes extends ZeModel {
    public function get_numerotation($test = false){
        $this->_pLoad->model("Zeapps_configs", "configs");
        if($numerotation = $this->_pLoad->ctrl->configs->get('crm_quote_numerotation')) {
            if(!$test) $this->_pLoad->ctrl->configs->update(array('value' => $numerotation->value + 1), 'crm_quote_numerotation');
            return $numerotation->value;
        }
        else{
            if(!$test) $this->_pLoad->ctrl->configs->insert(array('id' => 'crm_quote_numerotation', 'value' => 2));
            return 1;
        }
    }

    public function get_ids($where = array()){

        $where['deleted_at'] = null;

        return $this->database()->select('id')
            ->where($where)
            ->order_by(array('date_creation', 'id'), 'DESC')
            ->table('zeapps_quotes')
            ->result();
    }

    public function getDueOf($type, $id = null){
        $total = 0;
        $quotes = $this->all(array('id_'.$type => $id, 'due >' => 0));

        if($quotes) {
            foreach ($quotes as $quote){
                $total += floatval($quote->due);
            }
        }

        return array('due' => $total, 'due_lines' => $quotes);
    }

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_quote_lines", "quote_lines");
        $this->_pLoad->model("Zeapps_quote_line_details", "quote_line_details");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = $this->_pLoad->ctrl->configs->get(array('id'=>'crm_quote_format'))->value;
        $num = $this->get_numerotation();
        $src->numerotation = $this->parseFormat($format, $num);
        $src->date_creation = date('Y-m-d');
        $src->date_limit = date("Y-m-d", strtotime("+1 month", time()));

        $id = parent::insert($src);

        $new_id_lines = [];

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_quote = $id;

                $new_id = $this->_pLoad->ctrl->quote_lines->insert($line);

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

                $this->_pLoad->ctrl->quote_line_details->insert($line);
            }
        }

        return array(
            "id" =>$id,
            "numerotation" => $src->numerotation
        );
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

    public function searchFor($terms = array()){
        $query = "SELECT * FROM zeapps_quotes WHERE (1 ";

        foreach($terms as $term){
            $query .= "AND (libelle LIKE '%".$term."%') ";
        }

        $query .= ") AND deleted_at IS NULL LIMIT 10";

        return $this->database()->customQuery($query)->result();
    }
}