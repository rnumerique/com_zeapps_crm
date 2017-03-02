<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends ZeCtrl
{
    public function view()
    {
        $this->load->view('delivery/view');
    }

    public function form()
    {
        $this->load->view('delivery/form');
    }

    public function lists()
    {
        $this->load->view('delivery/lists');
    }

    public function lists_partial()
    {
        $this->load->view('delivery/lists_partial');
    }

    public function config()
    {
        $this->load->view('delivery/config');
    }



    public function makePDF($id, $echo = true){

        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        $data = [];

        $data['delivery'] = $this->deliveries->get($id);

        $data['company'] = $this->delivery_companies->get(array('id_delivery'=>$id));
        $data['contact'] = $this->delivery_contacts->get(array('id_delivery'=>$id));
        $data['lines'] = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$id));

        $data['showDiscount'] = false;
        foreach($data['lines'] as $line){
            if(floatval($line->discount) > 0)
                $data['showDiscount'] = true;
        }


        //load the view and saved it into $html variable
        $html = $this->load->view('delivery/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['delivery']->numerotation.'_'.$data['delivery']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/deliveries/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/deliveries/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Livraison €n° : '.$data['delivery']->numerotation.'|C. Compta : '.$data['delivery']->accounting_number.'|Fait le {DATE d/m/Y}');

        //set the PDF footer
        $this->M_pdf->pdf->SetFooter('{PAGENO}/{nb}');

        //generate the PDF from the given html
        $this->M_pdf->pdf->WriteHTML($html);

        //download it.
        $this->M_pdf->pdf->Output($pdfFilePath, "F");

        if($echo)
            echo json_encode($nomPDF);

        return $nomPDF;
    }

    public function getPDF($nomPDF){
        $file_url = FCPATH . 'tmp/com_zeapps_crm/deliveries/'.$nomPDF.'.pdf';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
        unlink($file_url);
    }

    public function testFormat(){
        $this->load->model("Zeapps_deliveries", "deliveries");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $frequency = $data['frequency'];
        $num = $this->deliveries->get_numerotation($frequency);

        $result = $this->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function finalize($id) {
        if($id) {
            $this->load->model("Zeapps_deliveries", "deliveries");
            $this->load->model("Zeapps_delivery_lines", "delivery_lines");
            $this->load->model("Zeapps_stock_movements", "stock_movements");
            $this->load->model("Zeapps_product_products", "products");
            $this->load->model("Zeapps_product_lines", "product_lines");

            $nomPDF = $this->makePDF($id, false);

            if($delivery = $this->deliveries->get($id)) {

                $company = $this->delivery_companies->get(array('id_delivery'=>$id));
                $contact = $this->delivery_contacts->get(array('id_delivery'=>$id));

                $this->stock_movements->delete(array('name_table' => 'zeapps_deliveries','id_table' => $id));

                if ($lines = $this->delivery_lines->all(array('id_delivery' => $id))) {
                    foreach ($lines as $line) {
                        if ($line->id_product > 0) {
                            if ($product = $this->products->get($line->id_product)) {
                                if($product->id_stock > 0) {
                                    $now = date('Y-m-d H:i:s');
                                    $data = [
                                        'id_warehouse' => $delivery->id_warehouse,
                                        'id_stock' => $product->id_stock,
                                        'label' => 'Bon de livraison n°' . $delivery->numerotation,
                                        'qty' => -1 * floatval($line->qty),
                                        'id_table' => $id,
                                        'name_table' => 'zeapps_deliveries',
                                        'date_mvt' => $now
                                    ];

                                    $this->stock_movements->insert($data);
                                }
                                elseif($product->compose > 0){
                                    if($product_lines = $this->product_lines->all(array('id_product' => $product->id))){
                                        foreach($product_lines as $product_line){
                                            if($part = $this->products->get($product_line->id_part)){
                                                if($part->id_stock > 0){

                                                    $company_name = $company ? $company->company_name . ' - ' : '';
                                                    $contact_name = $contact ? $contact->last_name . ' ' . $contact->first_name : '';

                                                    $name = 'BL n°' . $delivery->numerotation . ' ('.$company_name..')';

                                                    $now = date('Y-m-d H:i:s');
                                                    $data = [
                                                        'id_warehouse' => $delivery->id_warehouse,
                                                        'id_stock' => $part->id_stock,
                                                        'label' => $name,
                                                        'qty' => -1 * floatval($line->qty) * floatval($product_line->quantite),
                                                        'id_table' => $id,
                                                        'name_table' => 'zeapps_deliveries',
                                                        'date_mvt' => $now
                                                    ];

                                                    $this->stock_movements->insert($data);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->deliveries->update(array('finalized' => true, 'final_pdf' => $nomPDF), $id);

                echo json_encode(array('nomPDF' => $nomPDF));
            }
            else{
                echo json_encode(false);
            }
        }
        else{
            echo json_encode(false);
        }
    }


    public function getAll($id_company = null, $type = 'company') {
        $this->load->model("Zeapps_users", "users");
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        if($id_company)
            $deliveries = $this->deliveries->all(array('id_'.$type=>$id_company));
        else
            $deliveries = $this->deliveries->all();

        if($deliveries && is_array($deliveries)){
            for($i=0;$i<sizeof($deliveries);$i++){
                $user = $this->users->get($deliveries[$i]->id_user);
                if($user) {
                    $deliveries[$i]->user_name = $user->firstname[0] . '. ' . $user->lastname;
                }
                $deliveries[$i]->company = $this->delivery_companies->get(array('id_delivery'=>$deliveries[$i]->id));
                $deliveries[$i]->contact = $this->delivery_contacts->get(array('id_delivery'=>$deliveries[$i]->id));
                $deliveries[$i]->lines = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$deliveries[$i]->id));
            }
        }

        if ($deliveries == false) {
            echo json_encode(array());
        } else {
            echo json_encode($deliveries);
        }

    }

    public function get($id) {
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_delivery_documents", "delivery_documents");
        $this->load->model("Zeapps_delivery_activities", "delivery_activities");
        $this->load->model("Zeapps_invoices", "invoices");

        $data = new stdClass();

        $data->delivery = $this->deliveries->get($id);

        $data->company = $this->delivery_companies->get(array('id_delivery'=>$id));
        $data->contact = $this->delivery_contacts->get(array('id_delivery'=>$id));
        $data->lines = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$id));
        $data->documents = $this->delivery_documents->all(array('id_delivery'=>$id));
        $data->activities = $this->delivery_activities->all(array('id_delivery'=>$id));

        if($data->company){
            $res = $this->invoices->getDueOf('company', $data->delivery->id_company);
            $data->company->due = $res['due'];
            $data->company->due_lines = $res['due_lines'];
        }
        elseif($data->contact){
            $res = $this->invoices->getDueOf('contact', $data->delivery->id_contact);
            $data->contact->due = $res['due'];
            $data->contact->due_lines = $res['due_lines'];
        }

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_companies", "companies", "com_zeapps_contact");
        $this->load->model("Zeapps_contacts", "contacts", "com_zeapps_contact");
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->deliveries->update($data, $data["id"]);
        } else {

            $format = $this->configs->get(array('id'=>'crm_delivery_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_delivery_frequency'))->value;
            $num = $this->deliveries->get_numerotation($frequency);
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
            $id = $this->deliveries->insert($data);
            if($id) {
                if($company){
                    $company->id_company = $company->id;
                    unset($company->id);
                    unset($company->created_at);
                    unset($company->updated_at);
                    unset($company->deleted_at);
                    $company->id_delivery = $id;
                    $this->delivery_companies->insert($company);
                }
                if($contact){
                    $contact->id_contact = $contact->id;
                    unset($contact->id);
                    unset($contact->created_at);
                    unset($contact->updated_at);
                    unset($contact->deleted_at);
                    $contact->id_delivery = $id;
                    $this->delivery_contacts->insert($contact);
                }
            }
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_delivery_documents", "delivery_documents");

        $this->deliveries->delete($id);

        $companies = $this->delivery_companies->all(array('id_delivery' => $id));

        if($companies && is_array($companies)){
            for($i=0;$i<sizeof($companies);$i++){
                $this->delivery_companies->delete($companies[$i]->id);
            }
        }

        $contacts = $this->delivery_contacts->all(array('id_delivery' => $id));

        if($contacts && is_array($contacts)){
            for($i=0;$i<sizeof($contacts);$i++){
                $this->delivery_contacts->delete($contacts[$i]->id);
            }
        }

        $lines = $this->delivery_lines->all(array('id_delivery' => $id));

        if($lines && is_array($lines)){
            for($i=0;$i<sizeof($lines);$i++){
                $this->delivery_lines->delete($lines[$i]->id);
            }
        }

        $documents = $this->delivery_documents->all(array('id_delivery' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                $this->delivery_documents->delete($documents[$i]->id);
                unlink($path . $documents[$i]->path);
            }
        }

        echo json_encode("OK");
    }

    public function saveLine(){
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $this->delivery_lines->update($data, $data["id"]);
        } else {
            $id = $this->delivery_lines->insert($data);
        }

        echo json_encode($id);
    }

    public function updateLinePosition(){
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = $this->delivery_lines->get($data['id']);

            $this->delivery_lines->updateOldTable($line->id_delivery, $data['oldSort']);
            $this->delivery_lines->updateNewTable($line->id_delivery, $data['sort']);

            $id = $this->delivery_lines->update(array('sort'=>$data['sort']), $data["id"]);
        }

        echo json_encode($id);
    }

    public function deleteLine($id = null){
        if($id){
            $this->load->model("Zeapps_delivery_lines", "delivery_lines");

            $line = $this->delivery_lines->get($id);

            $this->delivery_lines->updateOldTable($line->id_delivery, $line->sort);

            echo json_encode($this->delivery_lines->delete($id));

        }
    }

    public function uploadDocuments($id_delivery = null){
        if($id_delivery) {
            $this->load->model("Zeapps_delivery_documents", "delivery_documents");

            $data = [];
            $res = [];

            $data['id_delivery'] = $id_delivery;

            $files = $_FILES['files'];

            $path = '/assets/upload/crm/deliveries/';

            $time = time();

            $year = date('Y', $time);
            $month = date('m', $time);
            $day = date('d', $time);
            $hour = date('H', $time);

            $data['created_at'] = $year . '-' . $month . '-' . $day;

            $path .= $year . '/' . $month . '/' . $day . '/' . $hour . '/';

            recursive_mkdir(FCPATH . $path);

            for ($i = 0; $i < sizeof($files['name']); $i++) {
                $arr = explode(".", $files["name"][$i]);
                $extension = end($arr);

                $data['name'] = implode('.', array_slice($arr, 0, -1)); // entire name except the extension

                $data['path'] = $path . ltrim(str_replace(' ', '', microtime()), '0.') . "." . $extension;

                move_uploaded_file($files["tmp_name"][$i], FCPATH . $data['path']);

                $data['id'] = $this->delivery_documents->insert($data);

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
            $this->load->model("Zeapps_delivery_documents", "delivery_documents");

            $document = $this->delivery_documents->get($id);

            $path = FCPATH;

            if(unlink($path . $document->path))
                echo json_encode($this->delivery_documents->delete($id));
            else
                echo json_encode(false);

        }
        else{
            echo json_encode(false);
        }
        return true;
    }

    public function saveActivity(){
        $this->load->model("Zeapps_delivery_activities", "delivery_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->delivery_activities->update($data, $data["id"]);
        } else {
            $id = $this->delivery_activities->insert($data);
            $data['id'] = $id;
        }

        echo json_encode($data);
    }

    public function deleteActivity($id = null){
        if($id){
            $this->load->model("Zeapps_delivery_activities", "delivery_activities");

            echo json_encode($this->delivery_activities->delete($id));

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