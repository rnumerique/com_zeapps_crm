<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm_origins extends ZeCtrl
{
    public function config()
    {
        $this->load->view('taxes/config');
    }


    public function getAll() {
        $this->load->model("Zeapps_crm_origins", "crm_origins");

        $crm_origins = $this->crm_origins->all();
        
        echo json_encode($crm_origins);
    }

    public function get($id) {
        $this->load->model("Zeapps_crm_origins", "crm_origins");

        $crm_origin = $this->crm_origins->get($id);

        echo json_encode($crm_origin);
    }

    public function save() {
        $this->load->model("Zeapps_crm_origins", "crm_origins");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->crm_origins->update($data, $data["id"]);
        } else {
            $id = $this->crm_origins->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_crm_origins", "crm_origins");

        $this->crm_origins->delete($id);

        echo json_encode("OK");
    }
}