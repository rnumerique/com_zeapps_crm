<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends ZeCtrl
{
    public function user_hook(){
        $this->load->view('stock/warehouse_user_hook');
    }

    public function config()
    {
        $this->load->view('stock/warehouse_config');
    }

    public function form_modal()
    {
        $this->load->view('stock/warehouse_form_modal');
    }



    public function get($id){
        $this->load->model('zeapps_warehouses', 'warehouses');

        $warehouse = $this->warehouses->get($id);

        echo json_encode($warehouse);
    }

    public function getAll(){
        $this->load->model('zeapps_warehouses', 'warehouses');

        $warehouses = $this->warehouses->all();

        echo json_encode($warehouses);
    }

    public function save() {
        $this->load->model('zeapps_warehouses', 'warehouses');

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"])) {
            $id = $data["id"];
            $this->warehouses->update($data, $data["id"]);
        } else {
            $id = $this->warehouses->insert($data);
        }

        echo $id;
    }

    public function save_all(){
        $this->load->model('zeapps_warehouses', 'warehouses');

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data && is_array($data)){
            foreach($data as $warehouse){
                $this->warehouses->update($warehouse, $warehouse['id']);
            }
            echo json_encode(true);
        }
        else{
            echo json_encode(false);
        }
    }

    public function delete($id){
        $this->load->model('zeapps_warehouses', 'warehouses');

        echo json_encode($this->warehouses->delete($id));
    }
}