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

    public function form_modal(){
        $this->load->view('stock/form_modal');
    }

    public function form_transfert(){
        $this->load->view('stock/form_transfert');
    }

    public function form_mvt(){
        $this->load->view('stock/form_mvt');
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

            if($product_stock->movements = $this->stock_movements->limit(15,0)->order_by('date_mvt', 'DESC')->all($w)){
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
            $total = $this->stock_movements->count($w);
        }
        else{
            $product_stock = array();
        }

        $warehouses = $this->warehouses->all();

        echo json_encode(array(
            'product_stock' => $product_stock,
            'warehouses' => $warehouses,
            'total' => $total
        ));
    }

    public function get_movements($id_stock, $id_warehouse = null, $limit = 15, $offset = 0){
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        $w = array('id_stock' => $id_stock);
        if($id_warehouse)
            $w['id_warehouse'] = $id_warehouse;

        if(!$stock_movements = $this->stock_movements->limit($limit, $offset)->order_by('date_mvt', 'DESC')->all($w)){
            $stock_movements = [];
        }
        $total = $this->stock_movements->count($w);

        echo json_encode(array(
            "stock_movements" => $stock_movements,
            'total' => $total
        ));
    }

    public function getAll($limit = 15, $offset = 0, $context = false){
        $this->load->model('zeapps_stocks', 'stocks');
        $this->load->model('zeapps_warehouses', 'warehouses');

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($context) {
            $warehouses = $this->warehouses->all();
        }
        else{
            $warehouses = null;
        }

        if(!$product_stocks = $this->stocks->all($filters, $limit, $offset)){
            $product_stocks = [];
        }

        $total = $this->stocks->group_by('id_warehouse')->count($filters);

        echo json_encode(array(
            'product_stocks' => $product_stocks,
            'warehouses' => $warehouses,
            'total' => $total
        ));
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
            echo json_encode($this->product_stocks->update($data, $data["id"]));
        } else {
            $id = $this->product_stocks->insert($data);
            $this->get($id, $id_warehouse);
        }
    }

    public function delete($id){
        $this->load->model('zeapps_product_stocks', 'product_stocks');

        echo json_encode($this->product_stocks->delete($id));
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

    public function add_transfert() {
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $data['id_warehouse'] = $data['src'];
        $data['qty'] = - $data['qty'];

        $id = $this->stock_movements->insert($data);

        $data['id_warehouse'] = $data['trgt'];
        $data['qty'] = - $data['qty'];

        $this->stock_movements->insert($data);

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