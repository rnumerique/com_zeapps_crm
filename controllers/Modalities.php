<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modalities extends ZeCtrl
{
    public function config()
    {
        $this->load->view('modalities/config');
    }

    public function form_modal()
    {
        $this->load->view('modalities/form_modal');
    }


    public function getAll() {
        $this->load->model("Zeapps_modalities", "modalities");

        $modalities = $this->modalities->all();
        
        echo json_encode($modalities);
    }

    public function get($id) {
        $this->load->model("Zeapps_modalities", "modalities");

        $modality = $this->modalities->get($id);

        echo json_encode($modality);
    }

    public function save() {
        $this->load->model("Zeapps_modalities", "modalities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->modalities->update($data, $data["id"]);
        } else {
            $id = $this->modalities->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_modalities", "modalities");

        echo json_encode($this->modalities->delete($id));
    }
}