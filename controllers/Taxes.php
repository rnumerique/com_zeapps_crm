<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxes extends ZeCtrl
{
    public function config()
    {
        $this->load->view('taxes/config');
    }

    public function form_modal(){
        $this->load->view('taxes/form_modal');
    }


    public function getAll() {
        $this->load->model("Zeapps_taxes", "taxes");

        $taxes = $this->taxes->all();
        
        echo json_encode($taxes);
    }

    public function get($id) {
        $this->load->model("Zeapps_taxes", "taxes");

        $taxe = $this->taxes->get($id);

        echo json_encode($taxe);
    }

    public function save() {
        $this->load->model("Zeapps_taxes", "taxes");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->taxes->update($data, $data["id"]);
        } else {
            $id = $this->taxes->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_taxes", "taxes");

        echo json_encode($this->taxes->delete($id));
    }
}