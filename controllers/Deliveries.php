<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deliveries extends ZeCtrl
{
    public function view()
    {
        $this->load->view('deliveries/view');
    }

    public function form()
    {
        $this->load->view('deliveries/form');
    }

    public function form_modal()
    {
        $this->load->view('deliveries/form_modal');
    }

    public function form_line(){
        $this->load->view('deliveries/form_line');
    }

    public function lists()
    {
        $this->load->view('deliveries/lists');
    }

    public function lists_partial()
    {
        $this->load->view('deliveries/lists_partial');
    }

    public function transform_modal()
    {
        $this->load->view('deliveries/transform_modal');
    }

    public function config()
    {
        $this->load->view('deliveries/config');
    }

    public function modal_activity()
    {
        $this->load->view('deliveries/modal_activity');
    }

    public function modal_document()
    {
        $this->load->view('deliveries/modal_document');
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
        $data['tvas'] = [];
        foreach($data['lines'] as $line){
            if(floatval($line->discount) > 0)
                $data['showDiscount'] = true;

            if($line->id_taxe !== '0'){
                if(!isset($data['tvas'][$line->id_taxe])){
                    $data['tvas'][$line->id_taxe] = array(
                        'ht' => 0,
                        'value_taxe' => floatval($line->value_taxe)
                    );
                }

                $data['tvas'][$line->id_taxe]['ht'] += floatval($line->total_ht);
                $data['tvas'][$line->id_taxe]['value'] = round(floatval($data['tvas'][$line->id_taxe]['ht']) * ($data['tvas'][$line->id_taxe]['value_taxe'] / 100), 2);
            }
        }

        //load the view and saved it into $html variable
        $html = $this->load->view('deliveries/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['delivery']->numerotation.'_'.$data['delivery']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/deliveries/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/deliveries/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Devis €<br>n° : '.$data['delivery']->numerotation.'|C. Compta : '.$data['delivery']->accounting_number.'|{DATE d/m/Y}');

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

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_deliveries", "deliveries");

            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($data){
                foreach($data as $document => $value){
                    if($value == 'true'){
                        $return[$document] = $this->createFrom($document, $id);
                    }
                }
            }

            echo json_encode($return);
        }
        else{
            echo json_encode(false);
        }
    }

    public function createFrom($type, $id){
        if($id){
            $this->load->model("Zeapps_configs", "configs");
            $this->load->model("Zeapps_deliveries", "deliveries");
            $this->load->model("Zeapps_delivery_companies", "delivery_companies");
            $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
            $this->load->model("Zeapps_delivery_lines", "delivery_lines");
            $type_model = $type."_model";
            $this->load->model("Zeapps_".$type."s", $type_model);
            $type_companies = $type."_companies";
            $this->load->model("Zeapps_".$type."_companies", $type_companies);
            $type_contacts = $type."_contacts";
            $this->load->model("Zeapps_".$type."_contacts", $type_contacts);
            $type_lines = $type."_lines";
            $this->load->model("Zeapps_".$type."_lines", $type_lines);

            $delivery = $this->deliveries->get($id);

            unset($delivery->id);
            unset($delivery->numerotation);
            unset($delivery->created_at);
            unset($delivery->updated_at);

            $format = $this->configs->get(array('id'=>'crm_'.$type.'_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_'.$type.'_frequency'))->value;
            $num = $this->$type_model->get_numerotation($frequency);
            $delivery->numerotation = $this->parseFormat($format, $num);

            $new_id = $this->$type_model->insert($delivery);
            $id_key = "id_".$type;

            if($companies = $this->$type_companies->all(array('id_delivery'=>$id))){
                foreach($companies as $company){
                    unset($company->id);
                    unset($company->id_delivery);
                    unset($company->created_at);
                    unset($company->updated_at);

                    $company->$id_key = $new_id;

                    $this->$type_companies->insert($company);
                }
            }

            if($contacts = $this->$type_contacts->all(array('id_delivery'=>$id))){
                foreach($contacts as $contact){
                    unset($contact->id);
                    unset($contact->id_delivery);
                    unset($contact->created_at);
                    unset($contact->updated_at);

                    $contact->$id_key = $new_id;

                    $this->$type_contacts->insert($contact);
                }
            }

            if($lines = $this->$type_lines->all(array('id_delivery'=>$id))){
                foreach($lines as $line){
                    unset($line->id);
                    unset($line->id_delivery);
                    unset($line->created_at);
                    unset($line->updated_at);

                    $line->$id_key = $new_id;

                    $this->$type_lines->insert($line);
                }
            }

        }

        return $new_id;
    }

    public function getAll($id = '0', $type = 'company', $limit = 15, $offset = 0, $context = false) {
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_companies", "delivery_companies");
        $this->load->model("Zeapps_delivery_contacts", "delivery_contacts");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if($deliveries = $this->deliveries->limit($limit, $offset)->all($filters)){
            for($i=0;$i<sizeof($deliveries);$i++){
                $deliveries[$i]->company = $this->delivery_companies->get(array('id_delivery'=>$deliveries[$i]->id));
                $deliveries[$i]->contact = $this->delivery_contacts->get(array('id_delivery'=>$deliveries[$i]->id));
                $deliveries[$i]->lines = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$deliveries[$i]->id));
            }
        }
        else{
            $deliveries = [];
        }
        $total = $this->deliveries->count($filters);

        if($context){

        }

        echo json_encode(array(
            'deliveries' => $deliveries,
            'total' => $total
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_deliveries", "deliveries");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$deliveries = $this->deliveries->limit($limit, $offset)->all($filters)){
            $deliveries = [];
        }
        $total = $this->deliveries->count($filters);

        echo json_encode(array(
            'data' => $deliveries,
            'total' => $total
        ));

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
            $this->deliveries->update($data, array('id' => $data["id"]));
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

    public function activity(){
        $this->load->model("Zeapps_delivery_activities", "delivery_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['id']){
            $this->delivery_activities->update($data, $data['id']);
            $id = $data['id'];
        }
        else{
            $id = $this->delivery_activities->insert($data);
        }

        $delivery_activities = $this->delivery_activities->get($id);

        echo json_encode($delivery_activities);
    }

    public function del_activity($id){
        $this->load->model("Zeapps_delivery_activities", "delivery_activities");

        echo json_encode($this->delivery_activities->delete($id));
    }

    public function uploadDocuments($id_delivery = null){
        if($id_delivery) {
            $this->load->model("Zeapps_delivery_documents", "delivery_documents");

            $data = $_POST;
            $files = $_FILES['files'];
            if($files) {
                if($data['path']){
                    unlink($data['path']);
                }

                $data['id_delivery'] = $id_delivery;

                $path = '/assets/upload/crm/deliveries/';

                $time = time();

                $year = date('Y', $time);
                $month = date('m', $time);
                $day = date('d', $time);
                $hour = date('H', $time);

                $data['created_at'] = $year . '-' . $month . '-' . $day;

                $path .= $year . '/' . $month . '/' . $day . '/' . $hour . '/';

                recursive_mkdir(FCPATH . $path);

                $arr = explode(".", $files["name"][0]);
                $extension = end($arr);

                $data['path'] = $path . ltrim(str_replace(' ', '', microtime()), '0.') . "." . $extension;

                move_uploaded_file($files["tmp_name"][0], FCPATH . $data['path']);

                if ($data['id']) {
                    $this->delivery_documents->update($data, $data['id']);
                } else {
                    $data['id'] = $this->delivery_documents->insert($data);
                }
                $data['date'] = date('Y-m-d H:i:s');

                echo json_encode($data);
            }
            else{
                if ($data['id']) {
                    $this->delivery_documents->update($data, $data['id']);

                    $data['date'] = date('Y-m-d H:i:s');

                    echo json_encode($data);
                }
                else {
                    echo json_encode(false);
                }
            }
        }
        else {
            echo json_encode(false);
        }
    }

    public function del_document($id){
        $this->load->model("Zeapps_delivery_documents", "delivery_documents");

        if($document = $this->delivery_documents->get($id)){
            unlink($document->path);

            $this->delivery_documents->delete($id);
        }

        echo 'OK';
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