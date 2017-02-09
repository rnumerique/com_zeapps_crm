<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends ZeCtrl
{
    public function view()
    {
        $data = array() ;

        $this->load->view('orders/view', $data);
    }

    public function form()
    {
        $data = array() ;

        $this->load->view('orders/form', $data);
    }

    public function lists()
    {
        $data = array() ;

        $this->load->view('orders/lists', $data);
    }

    public function config()
    {
        $data = array() ;

        $this->load->view('orders/config', $data);
    }



    public function makePDF($id){

        $this->load->model("zeapps_orders", "orders");
        $this->load->model("zeapps_order_companies", "order_companies");
        $this->load->model("zeapps_order_contacts", "order_contacts");
        $this->load->model("zeapps_order_lines", "order_lines");

        $data = [];

        $data['order'] = $this->orders->get($id);

        $data['company'] = $this->order_companies->get(array('id_order'=>$id));
        $data['contact'] = $this->order_contacts->get(array('id_order'=>$id));
        $data['lines'] = $this->order_lines->order_by('sort')->all(array('id_order'=>$id));

        //load the view and saved it into $html variable
        $html = $this->load->view('orders/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['order']->numerotation.'_'.$data['order']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/orders/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/orders/'.$nomPDF.'.pdf';

        //load mPDF library
        $this->load->library('m_pdf');

        //set the PDF header
        $this->m_pdf->pdf->SetHeader('Commande n° : '.$data['order']->numerotation.'|Compte Comptable : '.$data['order']->accounting_number.'|{DATE d/m/Y}');

        //set the PDF footer
        $this->m_pdf->pdf->SetFooter('{PAGENO}/{nb}');

        //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);

        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "F");

        echo json_encode($nomPDF);
    }

    public function getPDF($nomPDF){
        $file_url = FCPATH . 'tmp/com_zeapps_crm/orders/'.$nomPDF.'.pdf';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
        unlink($file_url);
    }

    public function testFormat(){
        $this->load->model("zeapps_orders", "orders");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $frequency = $data['frequency'];
        $num = $this->orders->get_numerotation($frequency);

        $result = $this->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function createInvoiceFrom($id){
        if($id){
            $this->load->model("zeapps_orders", "orders");
            $this->load->model("zeapps_order_companies", "order_companies");
            $this->load->model("zeapps_order_contacts", "order_contacts");
            $this->load->model("zeapps_order_lines", "order_lines");
            $this->load->model("zeapps_invoices", "invoices");
            $this->load->model("zeapps_invoice_companies", "invoice_companies");
            $this->load->model("zeapps_invoice_contacts", "invoice_contacts");
            $this->load->model("zeapps_invoice_lines", "invoice_lines");

            $order = $this->orders->get($id);

            unset($order->id);
            unset($order->numerotation);
            unset($order->created_at);
            unset($order->updated_at);

            $id_invoice = $this->invoices->insert($order);

            if($companies = $this->order_companies->all(array('id_order'=>$id))){
                foreach($companies as $company){
                    unset($company->id);
                    unset($company->id_order);
                    unset($company->created_at);
                    unset($company->updated_at);

                    $company->id_invoice = $id_invoice;

                    $this->invoice_companies->insert($company);
                }
            }

            if($contacts = $this->order_contacts->all(array('id_order'=>$id))){
                foreach($contacts as $contact){
                    unset($contact->id);
                    unset($contact->id_order);
                    unset($contact->created_at);
                    unset($contact->updated_at);

                    $contact->id_invoice = $id_invoice;

                    $this->invoice_contacts->insert($contact);
                }
            }

            if($lines = $this->order_lines->all(array('id_order'=>$id))){
                foreach($lines as $line){
                    unset($line->id);
                    unset($line->id_order);
                    unset($line->created_at);
                    unset($line->updated_at);

                    $line->id_invoice = $id_invoice;

                    $this->invoice_lines->insert($line);
                }
            }

        }

        echo json_encode($id_invoice);
    }


    public function getAll($id_company = null) {
        $this->load->model("zeapps_users", "users");
        $this->load->model("zeapps_orders", "orders");
        $this->load->model("zeapps_order_companies", "order_companies");
        $this->load->model("zeapps_order_contacts", "order_contacts");
        $this->load->model("zeapps_order_lines", "order_lines");

        if($id_company)
            $orders = $this->orders->all(array('id_company'=>$id_company));
        else
            $orders = $this->orders->all();

        if($orders && is_array($orders)){
            for($i=0;$i<sizeof($orders);$i++){
                $user = $this->users->get($orders[$i]->id_user);
                if($user) {
                    $orders[$i]->user_name = $user->firstname[0] . '. ' . $user->lastname;
                }
                $orders[$i]->company = $this->order_companies->get(array('id_order'=>$orders[$i]->id));
                $orders[$i]->contact = $this->order_contacts->get(array('id_order'=>$orders[$i]->id));
                $orders[$i]->lines = $this->order_lines->order_by('sort')->all(array('id_order'=>$orders[$i]->id));
            }
        }

        if ($orders == false) {
            echo json_encode(array());
        } else {
            echo json_encode($orders);
        }

    }

    public function get($id) {
        $this->load->model("zeapps_orders", "orders");
        $this->load->model("zeapps_order_companies", "order_companies");
        $this->load->model("zeapps_order_contacts", "order_contacts");
        $this->load->model("zeapps_order_lines", "order_lines");
        $this->load->model("zeapps_order_documents", "order_documents");
        $this->load->model("zeapps_order_activities", "order_activities");

        $data = new stdClass();

        $data->order = $this->orders->get($id);

        $data->company = $this->order_companies->get(array('id_order'=>$id));
        $data->contact = $this->order_contacts->get(array('id_order'=>$id));
        $data->lines = $this->order_lines->order_by('sort')->all(array('id_order'=>$id));
        $data->documents = $this->order_documents->all(array('id_order'=>$id));
        $data->activities = $this->order_activities->all(array('id_order'=>$id));

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("zeapps_configs", "configs");
        $this->load->model("zeapps_companies", "companies");
        $this->load->model("zeapps_contacts", "contacts");
        $this->load->model("zeapps_orders", "orders");
        $this->load->model("zeapps_order_companies", "order_companies");
        $this->load->model("zeapps_order_contacts", "order_contacts");
        $this->load->model("zeapps_order_lines", "order_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->orders->update($data, $data["id"]);
        } else {

            $format = $this->configs->get(array('id'=>'crm_order_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_order_frequency'))->value;
            $num = $this->orders->get_numerotation($frequency);
            $data['numerotation'] = $this->parseFormat($format, $num);

            if($data['id_company'] && $data['id_company'] > 0){
                $company = $this->companies->get($data['id_company']);
            }
            if($data['id_contact'] && $data['id_contact'] > 0){
                $contact = $this->contacts->get($data['id_contact']);
            }
            if($company){
                if($company->delivery_address_1){
                    $data['delivery_address_1'] = $company->delivery_address_1;
                    $data['delivery_address_2'] = $company->delivery_address_2;
                    $data['delivery_address_3'] = $company->delivery_address_3;
                    $data['delivery_city'] = $company->delivery_city;
                    $data['delivery_zipcode'] = $company->delivery_zipcode;
                    $data['delivery_state'] = $company->delivery_state;
                    $data['delivery_country_id'] = $company->delivery_country_id;
                    $data['delivery_country_name'] = $company->delivery_country_name;
                }
                if($company->billing_address_1){
                    $data['billing_address_1'] = $company->billing_address_1;
                    $data['billing_address_2'] = $company->billing_address_2;
                    $data['billing_address_3'] = $company->billing_address_3;
                    $data['billing_city'] = $company->billing_city;
                    $data['billing_zipcode'] = $company->billing_zipcode;
                    $data['billing_state'] = $company->billing_state;
                    $data['billing_country_id'] = $company->billing_country_id;
                    $data['billing_country_name'] = $company->billing_country_name;

                    if(!isset($data['delivery_address_1'])) {
                        $data['delivery_address_1'] = $company->billing_address_1;
                        $data['delivery_address_2'] = $company->billing_address_2;
                        $data['delivery_address_3'] = $company->billing_address_3;
                        $data['delivery_city'] = $company->billing_city;
                        $data['delivery_zipcode'] = $company->billing_zipcode;
                        $data['delivery_state'] = $company->billing_state;
                        $data['delivery_country_id'] = $company->billing_country_id;
                        $data['delivery_country_name'] = $company->billing_country_name;
                    }
                }
            }

            if($contact){
                if($contact->address_1 && !isset($data['billing_address_1'])) {
                    $data['billing_address_1'] = $contact->address_1;
                    $data['billing_address_2'] = $contact->address_2;
                    $data['billing_address_3'] = $contact->address_3;
                    $data['billing_city'] = $contact->city;
                    $data['billing_zipcode'] = $contact->zipcode;
                    $data['billing_state'] = $contact->state;
                    $data['billing_country_id'] = $contact->country_id;
                    $data['billing_country_name'] = $contact->country_name;
                }
                if($contact->address_1 && !isset($data['delivery_address_1'])){
                    $data['delivery_address_1'] = $contact->address_1;
                    $data['delivery_address_2'] = $contact->address_2;
                    $data['delivery_address_3'] = $contact->address_3;
                    $data['delivery_city'] = $contact->city;
                    $data['delivery_zipcode'] = $contact->zipcode;
                    $data['delivery_state'] = $contact->state;
                    $data['delivery_country_id'] = $contact->country_id;
                    $data['delivery_country_name'] = $contact->country_name;
                }
            }
            $id = $this->orders->insert($data);
            if($id) {
                if($company){
                    unset($company->id);
                    unset($company->created_at);
                    unset($company->updated_at);
                    unset($company->deleted_at);
                    $company->id_order = $id;
                    $this->order_companies->insert($company);
                }
                if($contact){
                    unset($contact->id);
                    unset($contact->created_at);
                    unset($contact->updated_at);
                    unset($contact->deleted_at);
                    $contact->id_order = $id;
                    $this->order_contacts->insert($contact);
                }
            }
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("zeapps_orders", "orders");
        $this->load->model("zeapps_order_companies", "order_companies");
        $this->load->model("zeapps_order_contacts", "order_contacts");
        $this->load->model("zeapps_order_lines", "order_lines");
        $this->load->model("zeapps_order_documents", "order_documents");

        $this->orders->delete($id);

        $companies = $this->order_companies->all(array('id_order' => $id));

        if($companies && is_array($companies)){
            for($i=0;$i<sizeof($companies);$i++){
                $this->order_companies->delete($companies[$i]->id);
            }
        }

        $contacts = $this->order_contacts->all(array('id_order' => $id));

        if($contacts && is_array($contacts)){
            for($i=0;$i<sizeof($contacts);$i++){
                $this->order_contacts->delete($contacts[$i]->id);
            }
        }

        $lines = $this->order_lines->all(array('id_order' => $id));

        if($lines && is_array($lines)){
            for($i=0;$i<sizeof($lines);$i++){
                $this->order_lines->delete($lines[$i]->id);
            }
        }

        $documents = $this->order_documents->all(array('id_order' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                $this->order_documents->delete($documents[$i]->id);
                unlink($path . $documents[$i]->path);
            }
        }

        echo json_encode("OK");
    }

    public function saveLine(){
        $this->load->model("zeapps_order_lines", "order_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $this->order_lines->update($data, $data["id"]);
        } else {
            $id = $this->order_lines->insert($data);
        }

        echo json_encode($id);
    }

    public function updateLinePosition(){
        $this->load->model("zeapps_order_lines", "order_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = $this->order_lines->get($data['id']);

            $this->order_lines->updateOldTable($line->id_order, $data['oldSort']);
            $this->order_lines->updateNewTable($line->id_order, $data['sort']);

            $id = $this->order_lines->update(array('sort'=>$data['sort']), $data["id"]);
        }

        echo json_encode($id);
    }

    public function deleteLine($id = null){
        if($id){
            $this->load->model("zeapps_order_lines", "order_lines");

            $line = $this->order_lines->get($id);

            $this->order_lines->updateOldTable($line->id_order, $line->sort);

            echo json_encode($this->order_lines->delete($id));

        }
    }

    public function uploadDocuments($id_order = null){
        if($id_order) {
            $this->load->model("zeapps_order_documents", "order_documents");

            $data = [];
            $res = [];

            $data['id_order'] = $id_order;

            $files = $_FILES['files'];

            $path = '/assets/upload/crm/orders/';

            $time = time();

            $year = date('Y', $time);
            $month = date('m', $time);
            $day = date('d', $time);
            $hour = date('H', $time);

            $path .= $year . '/' . $month . '/' . $day . '/' . $hour . '/';

            recursive_mkdir(FCPATH . $path);

            for ($i = 0; $i < sizeof($files['name']); $i++) {
                $arr = explode(".", $files["name"][$i]);
                $extension = end($arr);

                $data['name'] = implode('.', array_slice($arr, 0, -1)); // entire name except the extension

                $data['path'] = $path . ltrim(str_replace(' ', '', microtime()), '0.') . "." . $extension;

                move_uploaded_file($files["tmp_name"][$i], FCPATH . $data['path']);

                $data['id'] = $this->order_documents->insert($data);

                array_push($res, $data);

                unset($data['id']);
            }

            echo json_encode($res);
        }
        else {
            echo json_encode('false');
        }
    }

    public function deleteDocument($id = null){
        if($id){
            $this->load->model("zeapps_order_documents", "order_documents");

            $document = $this->order_documents->get($id);

            $path = FCPATH;

            if(unlink($path . $document->path))
                echo json_encode($this->order_documents->delete($id));
            else
                echo json_encode(false);

        }
        else{
            echo json_encode(false);
        }
        return true;
    }

    public function saveActivity(){
        $this->load->model("zeapps_order_activities", "order_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->order_activities->update($data, $data["id"]);
        } else {
            $id = $this->order_activities->insert($data);
            $data['id'] = $id;
        }

        echo json_encode($data);
    }

    public function deleteActivity($id = null){
        if($id){
            $this->load->model("zeapps_order_activities", "order_activities");

            echo json_encode($this->order_activities->delete($id));

        }
    }

    private function parseFormat($result = null, $num = null)
    {
        if ($result && $num){
            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(x+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                },
                $result);

            $result = preg_replace_callback('/[[dDjzmMnyYgGhH\-_]*(X+)[dDjzmMnyYgGhH\-_]*]/',
                function ($matches) use ($num) {
                    if (strlen($matches[1]) > strlen($num)) {
                        return str_replace($matches[1], str_pad($num, strlen($matches[1]), '0', STR_PAD_LEFT), $matches[0]);
                    } else {
                        return str_replace($matches[1], substr($num, -strlen($matches[1])), $matches[0]);
                    }
                },
                $result);

            $timestamp = time();

            $result = preg_replace_callback('/[[xX0-9\-_]*([dDjzmMnyYgGhH]+)[xX0-9\-_]*[]\/\-_]/',
                function ($matches) use ($timestamp) {
                    foreach ($matches as $match) {
                        return date($match, $timestamp);
                    }
                    return true;
                },
                $result);

            $result = str_replace(array('[', ']'), '', $result);

            return $result;
        }
        return false;
    }
}