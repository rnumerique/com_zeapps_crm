<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends ZeCtrl
{

    public function view()
    {
        $this->load->view('stock/view');
    }

    public function details()
    {
        $this->load->view('stock/details');
    }

    public function chart()
    {
        $this->load->view('stock/chart');
    }

    public function history()
    {
        $this->load->view('stock/history');
    }

    public function modal()
    {
        $this->load->view('stock/modal');
    }



    public function get($id_stock, $id_warehouse = null){
        $this->load->model('zeapps_product_stocks', 'product_stocks');
        $this->load->model('zeapps_stock_movements', 'stock_movements');
        $this->load->model('zeapps_warehouses', 'warehouses');

        $where = array('zeapps_product_stocks.id' => $id_stock);
        if($id_warehouse)
            $where['zeapps_stocks.id_warehouse'] = $id_warehouse;

        if($product_stock = $this->product_stocks->get($where)){
            $product_stock = $product_stock[0];

            $w = array('id_stock' => $product_stock->id);
            if($id_warehouse)
                $w['id_warehouse'] = $id_warehouse;

            $product_stock->avg = $this->stock_movements->avg($w);

            if($product_stock->movements = $this->stock_movements->all($w)){
                $product_stock->last = [];
                $product_stock->last['month'] = $this->stock_movements->last_year($w);
                $product_stock->last['dates'] = $this->stock_movements->last_months($w);
                $product_stock->last['date'] = $this->stock_movements->last_month($w);
                $product_stock->last['days'] = $this->stock_movements->last_week($w);
            }
            else {
                $product_stock->movements = array();
                $product_stock->recent_mvmts = array();
                $product_stock->last = array(
                    'month' => [],
                    'dates' => [],
                    'date' => [],
                    'days' => []
                );
            }
        }
        else{
            $product_stock = array();
        }

        $warehouses = $this->warehouses->all();

        echo json_encode(array('product_stock' => $product_stock, 'warehouses' => $warehouses));
    }

    public function getAll($id_warehouse = null){
        $this->load->model('zeapps_stocks', 'stocks');
        $this->load->model('zeapps_warehouses', 'warehouses');
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        if($id_warehouse){
            $where = array('id_warehouse' => $id_warehouse);
        }
        else{
            $where = [];
        }

        $warehouses = $this->warehouses->all();
        if($product_stocks = $this->stocks->all($where)){
            foreach($product_stocks as $product_stock){
                $where['id_stock'] = $product_stock->id_stock;
                $product_stock->avg = $this->stock_movements->avg($where);
            }
        }

        echo json_encode(array('product_stocks' => $product_stocks, 'warehouses' => $warehouses));
    }

    public function save($id_warehouse = null) {
        $this->load->model('zeapps_product_stocks', 'product_stocks');

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"])) {
            $this->product_stocks->update($data, $data["id"]);
            echo json_encode('OK');
        } else {
            $id = $this->product_stocks->insert($data);
            $this->get($id, $id_warehouse);
        }
    }

    public function delete($id){
        $this->load->model('zeapps_product_stocks', 'product_stocks');

        $this->product_stocks->delete($id);

        echo json_encode('OK');
    }


    public function add_mvt() {
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $id = $this->stock_movements->insert($data);

        echo $id;
    }

    public function ignore_mvt($id, $value, $id_stock, $id_warehouse){
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        $this->stock_movements->update(array('ignored' => $value), $id);

        $w = array('id_stock' => $id_stock);
        if($id_warehouse)
            $w['id_warehouse'] = $id_warehouse;

        $avg = $this->stock_movements->avg($w);

        echo $avg;
    }
}