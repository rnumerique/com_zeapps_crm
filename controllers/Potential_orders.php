<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Potential_orders extends ZeCtrl
{

    public function view()
    {
        $this->load->view('potential_order/view');
    }

    public function all($limit = 15, $offset = 0, $context = false){
        $this->load->model('Zeapps_orders', 'orders');

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($result = $this->orders->getPotentialOrders($limit, $offset)){
            $orders = $result["orders"];
            $total = $result["total"];
        }
        else{
            $orders = [];
            $total = 0;
        }

        if($context) {
        }
        else{
        }

        echo json_encode(array(
            'orders' => $orders,
            'total' => $total
        ));
    }
}