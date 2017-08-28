<?php
class Zeapps_quotes extends ZeModel {

    public function get_numerotation($frequency = null){
        if($frequency){
            $query = 'SELECT * FROM zeapps_quotes WHERE';
            switch ($frequency){
                case 'week':
                    $query .= ' week(created_at) = '.date('W').' AND';
                case 'month':
                    $query .= ' month(created_at) = '.date('m').' AND';
                case 'year':
                    $query .= ' year(created_at) = '.date('Y').' AND';
                case 'lifetime':
                    $query .= ' 1';
                    break;
                default:
                    $query .= ' 0';
            }
            return sizeof($this->database()->customQuery($query)->result()) + 1;
        }
        else{
            return false;
        }
    }

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_quote_lines", "quote_lines");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = $this->_pLoad->ctrl->configs->get(array('id'=>'crm_quote_format'))->value;
        $frequency = $this->_pLoad->ctrl->configs->get(array('id'=>'crm_quote_frequency'))->value;
        $num = $this->get_numerotation($frequency);
        $src->numerotation = $this->parseFormat($format, $num);
        $src->date_creation = date('Y-m-d');
        $src->date_limit = date("Y-m-d", strtotime("+1 month", time()));

        $id = parent::insert($src);

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_quote = $id;

                $this->_pLoad->ctrl->quote_lines->insert($line);
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