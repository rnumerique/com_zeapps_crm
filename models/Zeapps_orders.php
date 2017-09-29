<?php
class Zeapps_orders extends ZeModel {
    public function get_numerotation($test = false){
        $this->_pLoad->model("Zeapps_configs", "configs");
        if($numerotation = $this->_pLoad->ctrl->configs->get('crm_order_numerotation')) {
            if(!$test) $this->_pLoad->ctrl->configs->update(array('value' => $numerotation->value + 1), 'crm_order_numerotation');
            return $numerotation->value;
        }
        else{
            if(!$test) $this->_pLoad->ctrl->configs->insert(array('id' => 'crm_order_numerotation', 'value' => 2));
            return 1;
        }
    }

    public function get_ids($where = array()){

        $where['deleted_at'] = null;

        return $this->database()->select('id')
            ->where($where)
            ->order_by(array('date_creation', 'id'), 'DESC')
            ->table('zeapps_orders')
            ->result();
    }

    public function getDueOf($type, $id = null){
        $total = 0;
        $orders = $this->all(array('id_'.$type => $id, 'due >' => 0));

        if($orders) {
            foreach ($orders as $order){
                $total += floatval($order->due);
            }
        }

        return array('due' => $total, 'due_lines' => $orders);
    }

    public function createFrom($src){
        $this->_pLoad->model("Zeapps_configs", "configs");
        $this->_pLoad->model("Zeapps_order_lines", "order_lines");
        $this->_pLoad->model("Zeapps_order_line_details", "order_line_details");

        unset($src->id);
        unset($src->numerotation);
        unset($src->created_at);
        unset($src->updated_at);
        unset($src->deleted_at);

        $format = $this->_pLoad->ctrl->configs->get(array('id'=>'crm_order_format'))->value;
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

                $line->id_order = $id;

                $new_id = $this->_pLoad->ctrl->order_lines->insert($line);

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

                $this->_pLoad->ctrl->order_line_details->insert($line);
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

    public function frequencyOf($id = 0, $src = 'contact'){
        if($orders = $this->database()->select('TIMESTAMPDIFF(DAY, MIN(date_creation),MAX(date_creation)) as total_time, COUNT(id) as nb_orders')->where(array('id_'.$src => $id, 'date_creation !=' => '0000-00-00 00:00:00', 'deleted_at' => null))->table('zeapps_orders')->result()) {
            if(intval($orders[0]->nb_orders) > 0) {
                return intval($orders[0]->total_time) / intval($orders[0]->nb_orders);
            }
            else{
                return '--';
            }
        }
        else{
            return '--';
        }
    }

    public function getPotentialOrders($limit = 2147483647, $offset = 0){
        $result = false;

        $orders = $this->database()
            ->select('id_contact, id_company, TIMESTAMPDIFF(DAY, MIN(date_creation),MAX(date_creation)) as total_time, COUNT(id) as nb_orders')
            ->where(array('deleted_at' => null, "id_company !=" => 0))
            ->group_by('id_company, id_contact')
            ->table('zeapps_orders')
            ->result();

        if($orders) {
            $result = [];

            $start = microtime(true);
            foreach($orders as $order){
                if($order->id_company !== "0" || $order->id_contact !== "0") {
                    if ($avg = intval(intval($order->total_time) / intval($order->nb_orders))) {

                        $query = "
                        SELECT  id,
                                id_contact, 
                                id_company, 
                                name_contact, 
                                name_company, 
                                libelle, 
                                numerotation,
                                date_creation,
                                DATE_ADD(date_creation, INTERVAL ".$avg." DAY) as date_next
                        FROM zeapps_invoices
                        WHERE deleted_at is null";

                        if ($order->id_company !== "0") $query .= " AND id_company = " . $order->id_company;
                        elseif ($order->id_contact !== "0") $query .= " AND id_contact = " . $order->id_contact;

                        $query .= " 
                            AND date_creation > DATE_SUB(CURDATE(), INTERVAL " . ($avg + 15) . " DAY)
                        GROUP BY ";

                        if ($order->id_company !== "0") $query .= " id_company";
                        elseif ($order->id_contact !== "0") $query .= " id_contact";

                        if ($rows = $this->database()->customQuery($query)->result()) {
                            $row = $rows[0];
                            $row->avg = $avg;
                            $result[] = $row;
                        }
                    }
                }
            }

            $total = sizeof($result);

            usort($result, function($a, $b){ $at = strtotime($a->date_next); $bt = strtotime($b->date_next); return $at > $bt ? 1 : ($at < $bt ? -1 : 0); });
            $result = array(
                "orders" => array_splice($result, $offset, $limit),
                "total" => $total
            );
        }

        return $result;
    }
}