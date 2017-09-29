<?php

/**
 * Created by PhpStorm.
 * User: developpeur
 * Date: 16/12/2016
 * Time: 10:09
 */
class Credit_balances extends ZeCtrl
{
    public function lists(){
        $this->load->view('credit_balances/lists');
    }
    public function lists_partial(){
        $this->load->view('credit_balances/lists_partial');
    }
    public function view(){
        $this->load->view('credit_balances/view');
    }
    public function form_modal(){
        $this->load->view('credit_balances/form_modal');
    }
    public function form_multiple_modal(){
        $this->load->view('credit_balances/form_multiple_modal');
    }



    public function get($id){
        $this->load->model('Zeapps_credit_balances', 'credits');
        $this->load->model('Zeapps_credit_balance_details', 'credit_details');

        $credit = $this->credits->get(array('id_invoice' => $id));

        if(!$details = $this->credit_details->all(array('id_invoice' => $id))){
            $details = [];
        }

        echo json_encode(array(
            "credit" => $credit,
            "details" => $details
        ));
    }

    public function all($src_id = 0, $src = null, $limit = 15, $offset = 0){
        $this->load->model('Zeapps_credit_balances', 'credits');

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        $filters['left_to_pay >='] = 0.01;

        if($src !== null && $src !== "credits"){
            $filters['id_'.$src] = $src_id;
        }

        if(!$credits = $this->credits->order_by('due_date')->limit($limit, $offset)->all($filters)){
            $credits = [];
        }

        $total = $this->credits->count($filters);

        echo json_encode(array(
            "credits" => $credits,
            "total" => $total
        ));
    }

    public function save(){
        $this->load->model('Zeapps_credit_balance_details', 'credit_details');

        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['id']){
            $id = $data['id'];
            $this->credit_details->update($data, $data['id']);
        }
        else{
            $id = $this->credit_details->insert($data);
        }

        echo $id;
    }

    public function save_multiples(){
        $this->load->model('Zeapps_credit_balance_details', 'credit_details');

        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['lines']){
            foreach($data['lines'] as $id_invoice => $paid){
                $detail = array(
                    "id_invoice" => $id_invoice,
                    "paid" => $paid,
                    "id_modality" => $data['id_modality'],
                    "label_modality" => $data['label_modality'],
                    "date_payment" => $data['date_payment']
                );
                $this->credit_details->insert($detail);
            }
        }

        echo true;
    }

    public function delete($id){
        $this->load->model('Zeapps_credit_balance_details', 'credit_details');

        echo json_encode($this->credit_details->delete($id));
    }
}