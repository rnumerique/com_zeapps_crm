<?php

/**
 * Created by PhpStorm.
 * User: developpeur
 * Date: 16/12/2016
 * Time: 10:09
 */
class Accounting_numbers extends ZeCtrl
{
    public function form_modal()
    {
        $this->load->view('accounting_numbers/form_modal');
    }

    public function modal($limit = 15, $offset = 0){
        $this->load->model("Zeapps_accounting_numbers", "accounting_numbers");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        $total = $this->accounting_numbers->count($filters);

        if(!$accounting_numbers = $this->accounting_numbers->limit($limit, $offset)->order_by('number', 'ASC')->all($filters)){
            $accounting_numbers = [];
        }

        echo json_encode(array("data" => $accounting_numbers, "total" => $total));
    }

    public function save(){
        $this->load->model("Zeapps_accounting_numbers", "accounting_numbers");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->accounting_numbers->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->accounting_numbers->insert($data);
        }

        echo $id;
    }
}