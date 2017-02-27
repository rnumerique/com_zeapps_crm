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



    public function get($id){
        $this->load->model('zeapps_product_stocks', 'product_stocks');
        $this->load->model('zeapps_stock_movements', 'stock_movements');

        $where = array('zeapps_product_stocks.id' => $id);

        if($product_stock = $this->product_stocks->get($where)){
            $product_stock = $product_stock[0];
            $product_stock->avg = $this->stock_movements->avg(array('id_stock' => $product_stock->id));
            if($product_stock->movements = $this->stock_movements->all(array('id_stock' => $product_stock->id))){
                $product_stock->recent_mvts = $this->stock_movements->recent(array('id_stock' => $product_stock->id));
            }
            else {
                $product_stock->movements = array();
                $product_stock->recent_mvmts = array();
            }
        }
        else{
            $product_stock = array();
        }

        echo json_encode($product_stock);
    }

    public function getAll($id_warehouse = null){
        $this->load->model('zeapps_stocks', 'stocks');
        $this->load->model('zeapps_warehouses', 'warehouses');

        if($id_warehouse){
            $where = array('id_warehouse' => $id_warehouse);
        }
        else{
            $where = [];
        }

        $warehouses = $this->warehouses->all();
        $product_stocks = $this->stocks->all($where);

        echo json_encode(array('product_stocks' => $product_stocks, 'warehouses' => $warehouses));
    }

    public function save(){

    }

    public function delete($id){
        $this->load->model('zeapps_product_stocks', 'product_stocks');

        $this->product_stocks->delete($id);

        echo json_encode('OK');
    }
}