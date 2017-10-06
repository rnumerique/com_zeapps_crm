<?php
class Zeapps_deliveries extends ZeModel {
    public function get_numerotation($test = false){
        $this->_pLoad->model("Zeapps_configs", "configs");
        if($numerotation = $this->_pLoad->ctrl->configs->get('crm_delivery_numerotation')) {
            if(!$test) $this->_pLoad->ctrl->configs->update(array('value' => $numerotation->value + 1), 'crm_delivery_numerotation');
            return $numerotation->value;
        }
        else{
            if(!$test) $this->_pLoad->ctrl->configs->insert(array('id' => 'crm_delivery_numerotation', 'value' => 2));
            return 1;
        }
    }

    public function get_ids($where = array()){

        $where['deleted_at'] = null;

        return $this->database()->select('id')
            ->where($where)
            ->order_by(array('date_creation', 'id'), 'DESC')
            ->table('zeapps_deliveries')
            ->result();
    }

    public function getDueOf($type, $id = null){
        $total = 0;
        $deliveries = $this->all(array('id_'.$type => $id, 'due >' => 0));

        if($deliveries) {
            foreach ($deliveries as $delivery){
                $total += floatval($delivery->due);
            }
        }

        return array('due' => $total, 'due_lines' => $deliveries);
    }

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_delivery_lines", "delivery_lines");
        $this->_pLoad->model("Zeapps_delivery_line_details", "delivery_line_details");
        $this->_pLoad->model("Zeapps_stock_movements", "stock_movements");
        $this->_pLoad->model("Zeapps_product_products", "product");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = $this->_pLoad->ctrl->configs->get(array('id'=>'crm_delivery_format'))->value;
        $num = $this->get_numerotation();
        $src->numerotation = $this->parseFormat($format, $num);
        $src->date_creation = date('Y-m-d');

        $id = parent::insert($src);

        $new_id_lines = [];

        if(isset($src->lines) && is_array($src->lines)){
            foreach($src->lines as $line){
                $old_id = $line->id;

                unset($line->id);
                unset($line->created_at);
                unset($line->updated_at);
                unset($line->deleted_at);

                $line->id_delivery = $id;

                $new_id = $this->_pLoad->ctrl->delivery_lines->insert($line);

                $new_id_lines[$old_id] = $new_id;

                if($line->type === 'product'){
                    $product = $this->_pLoad->ctrl->product->get($line->id_product);
                    $this->_pLoad->ctrl->stock_movements->write(array(
                        "id_warehouse" => $src->id_warehouse,
                        "id_stock" => $product->id_stock,
                        "label" => "Bon de livraison n°".$src->numerotation,
                        "qty" => -1 * floatval($line->qty),
                        "id_table" => $src->id,
                        "name_table" => "zeapps_deliveries",
                        "date_mvt" => $src->date_creation,
                        "ignored" => 0
                    ));
                }
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

                $this->_pLoad->ctrl->delivery_line_details->insert($line);

                if($line->type === 'product'){
                    $product = $this->_pLoad->ctrl->product->get($line->id_product);
                    $this->_pLoad->ctrl->stock_movements->write(array(
                        "id_warehouse" => $src->id_warehouse,
                        "id_stock" => $product->id_stock,
                        "label" => "Bon de livraison n°".$src->numerotation,
                        "qty" => -1 * floatval($line->qty),
                        "id_table" => $src->id,
                        "name_table" => "zeapps_deliveries",
                        "date_mvt" => $src->date_creation,
                        "ignored" => 0
                    ));
                }
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
        $query = "SELECT * FROM zeapps_deliveries WHERE (1 ";

        foreach($terms as $term){
            $query .= "AND (libelle LIKE '%".$term."%') ";
        }

        $query .= ") AND deleted_at IS NULL LIMIT 10";

        return $this->database()->customQuery($query)->result();
    }
}