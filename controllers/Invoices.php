<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends ZeCtrl
{
    public function view()
    {
        $this->load->view('invoices/view');
    }

    public function form()
    {
        $this->load->view('invoices/form');
    }

    public function form_modal()
    {
        $this->load->view('invoices/form_modal');
    }

    public function form_line(){
        $this->load->view('invoices/form_line');
    }

    public function form_comment(){
        $this->load->view('invoices/form_comment');
    }

    public function form_activity()
    {
        $this->load->view('invoices/form_activity');
    }

    public function form_document()
    {
        $this->load->view('invoices/form_document');
    }

    public function lists()
    {
        $this->load->view('invoices/lists');
    }

    public function lists_partial()
    {
        $this->load->view('invoices/lists_partial');
    }

    public function transform_modal()
    {
        $this->load->view('invoices/transform_modal');
    }

    public function config()
    {
        $this->load->view('invoices/config');
    }



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_companies", "invoice_companies");
        $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");

        $data = [];

        $data['invoice'] = $this->invoices->get($id);

        $data['company'] = $this->invoice_companies->get(array('id_invoice'=>$id));
        $data['contact'] = $this->invoice_contacts->get(array('id_invoice'=>$id));
        $data['lines'] = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));

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
        $html = $this->load->view('invoices/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['invoice']->numerotation.'_'.$data['invoice']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/invoices/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/invoices/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Devis €<br>n° : '.$data['invoice']->numerotation.'|C. Compta : '.$data['invoice']->accounting_number.'|{DATE d/m/Y}');

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
        $file_url = FCPATH . 'tmp/com_zeapps_crm/invoices/'.$nomPDF.'.pdf';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
        unlink($file_url);
    }

    public function testFormat(){
        $this->load->model("Zeapps_invoices", "invoices");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $num = $this->invoices->get_numerotation();

        $result = $this->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_invoices", "invoices");

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
            $this->load->model("Zeapps_invoices", "invoices");
            $this->load->model("Zeapps_invoice_companies", "invoice_companies");
            $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
            $this->load->model("Zeapps_invoice_lines", "invoice_lines");
            $type_model = $type."_model";
            $this->load->model("Zeapps_".$type."s", $type_model);
            $type_companies = $type."_companies";
            $this->load->model("Zeapps_".$type."_companies", $type_companies);
            $type_contacts = $type."_contacts";
            $this->load->model("Zeapps_".$type."_contacts", $type_contacts);
            $type_lines = $type."_lines";
            $this->load->model("Zeapps_".$type."_lines", $type_lines);

            $invoice = $this->invoices->get($id);

            unset($invoice->id);
            unset($invoice->numerotation);
            unset($invoice->created_at);
            unset($invoice->updated_at);

            $format = $this->configs->get(array('id'=>'crm_'.$type.'_format'))->value;
            $num = $this->$type_model->get_numerotation();
            $invoice->numerotation = $this->parseFormat($format, $num);

            $new_id = $this->$type_model->insert($invoice);
            $id_key = "id_".$type;

            if($companies = $this->$type_companies->all(array('id_invoice'=>$id))){
                foreach($companies as $company){
                    unset($company->id);
                    unset($company->id_invoice);
                    unset($company->created_at);
                    unset($company->updated_at);

                    $company->$id_key = $new_id;

                    $this->$type_companies->insert($company);
                }
            }

            if($contacts = $this->$type_contacts->all(array('id_invoice'=>$id))){
                foreach($contacts as $contact){
                    unset($contact->id);
                    unset($contact->id_invoice);
                    unset($contact->created_at);
                    unset($contact->updated_at);

                    $contact->$id_key = $new_id;

                    $this->$type_contacts->insert($contact);
                }
            }

            if($lines = $this->$type_lines->all(array('id_invoice'=>$id))){
                foreach($lines as $line){
                    unset($line->id);
                    unset($line->id_invoice);
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
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_companies", "invoice_companies");
        $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if($invoices = $this->invoices->limit($limit, $offset)->all($filters)){
            for($i=0;$i<sizeof($invoices);$i++){
                $invoices[$i]->company = $this->invoice_companies->get(array('id_invoice'=>$invoices[$i]->id));
                $invoices[$i]->contact = $this->invoice_contacts->get(array('id_invoice'=>$invoices[$i]->id));
                $invoices[$i]->lines = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$invoices[$i]->id));
            }
        }
        else{
            $invoices = [];
        }
        $total = $this->invoices->count($filters);

        if($context){

        }

        echo json_encode(array(
            'invoices' => $invoices,
            'total' => $total
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_invoices", "invoices");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$invoices = $this->invoices->limit($limit, $offset)->all($filters)){
            $invoices = [];
        }
        $total = $this->invoices->count($filters);

        echo json_encode(array(
            'data' => $invoices,
            'total' => $total
        ));

    }

    public function get($id) {
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_companies", "invoice_companies");
        $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_documents", "invoice_documents");
        $this->load->model("Zeapps_invoice_activities", "invoice_activities");
        $this->load->model("Zeapps_invoices", "invoices");

        $data = new stdClass();

        $data->invoice = $this->invoices->get($id);

        $data->company = $this->invoice_companies->get(array('id_invoice'=>$id));
        $data->contact = $this->invoice_contacts->get(array('id_invoice'=>$id));
        $data->lines = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));
        $data->documents = $this->invoice_documents->all(array('id_invoice'=>$id));
        $data->activities = $this->invoice_activities->all(array('id_invoice'=>$id));

        if($data->company){
            $res = $this->invoices->getDueOf('company', $data->invoice->id_company);
            $data->company->due = $res['due'];
            $data->company->due_lines = $res['due_lines'];
        }
        elseif($data->contact){
            $res = $this->invoices->getDueOf('contact', $data->invoice->id_contact);
            $data->contact->due = $res['due'];
            $data->contact->due_lines = $res['due_lines'];
        }

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_companies", "companies", "com_zeapps_contact");
        $this->load->model("Zeapps_contacts", "contacts", "com_zeapps_contact");
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_companies", "invoice_companies");
        $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->invoices->update($data, array('id' => $data["id"]));
        } else {

            $format = $this->configs->get(array('id'=>'crm_invoice_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_invoice_frequency'))->value;
            $num = $this->invoices->get_numerotation($frequency);
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
            $id = $this->invoices->insert($data);
            if($id) {
                if($company){
                    $company->id_company = $company->id;
                    unset($company->id);
                    unset($company->created_at);
                    unset($company->updated_at);
                    unset($company->deleted_at);
                    $company->id_invoice = $id;
                    $this->invoice_companies->insert($company);
                }
                if($contact){
                    $contact->id_contact = $contact->id;
                    unset($contact->id);
                    unset($contact->created_at);
                    unset($contact->updated_at);
                    unset($contact->deleted_at);
                    $contact->id_invoice = $id;
                    $this->invoice_contacts->insert($contact);
                }
            }
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_companies", "invoice_companies");
        $this->load->model("Zeapps_invoice_contacts", "invoice_contacts");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_documents", "invoice_documents");

        $this->invoices->delete($id);

        $companies = $this->invoice_companies->all(array('id_invoice' => $id));

        if($companies && is_array($companies)){
            for($i=0;$i<sizeof($companies);$i++){
                $this->invoice_companies->delete($companies[$i]->id);
            }
        }

        $contacts = $this->invoice_contacts->all(array('id_invoice' => $id));

        if($contacts && is_array($contacts)){
            for($i=0;$i<sizeof($contacts);$i++){
                $this->invoice_contacts->delete($contacts[$i]->id);
            }
        }

        $lines = $this->invoice_lines->all(array('id_invoice' => $id));

        if($lines && is_array($lines)){
            for($i=0;$i<sizeof($lines);$i++){
                $this->invoice_lines->delete($lines[$i]->id);
            }
        }

        $documents = $this->invoice_documents->all(array('id_invoice' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                $this->invoice_documents->delete($documents[$i]->id);
                unlink($path . $documents[$i]->path);
            }
        }

        echo json_encode("OK");
    }

    public function saveLine(){
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $this->invoice_lines->update($data, $data["id"]);
        } else {
            $id = $this->invoice_lines->insert($data);
        }

        echo json_encode($id);
    }

    public function updateLinePosition(){
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = $this->invoice_lines->get($data['id']);

            $this->invoice_lines->updateOldTable($line->id_invoice, $data['oldSort']);
            $this->invoice_lines->updateNewTable($line->id_invoice, $data['sort']);

            $id = $this->invoice_lines->update(array('sort'=>$data['sort']), $data["id"]);
        }

        echo json_encode($id);
    }

    public function deleteLine($id = null){
        if($id){
            $this->load->model("Zeapps_invoice_lines", "invoice_lines");

            $line = $this->invoice_lines->get($id);

            $this->invoice_lines->updateOldTable($line->id_invoice, $line->sort);

            echo json_encode($this->invoice_lines->delete($id));

        }
    }

    public function activity(){
        $this->load->model("Zeapps_invoice_activities", "invoice_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['id']){
            $this->invoice_activities->update($data, $data['id']);
            $id = $data['id'];
        }
        else{
            $id = $this->invoice_activities->insert($data);
        }

        $invoice_activities = $this->invoice_activities->get($id);

        echo json_encode($invoice_activities);
    }

    public function del_activity($id){
        $this->load->model("Zeapps_invoice_activities", "invoice_activities");

        echo json_encode($this->invoice_activities->delete($id));
    }

    public function uploadDocuments($id_invoice = null){
        if($id_invoice) {
            $this->load->model("Zeapps_invoice_documents", "invoice_documents");

            $data = $_POST;
            $files = $_FILES['files'];
            if($files) {
                if($data['path']){
                    unlink($data['path']);
                }

                $data['id_invoice'] = $id_invoice;

                $path = '/assets/upload/crm/invoices/';

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
                    $this->invoice_documents->update($data, $data['id']);
                } else {
                    $data['id'] = $this->invoice_documents->insert($data);
                }
                $data['date'] = date('Y-m-d H:i:s');

                echo json_encode($data);
            }
            else{
                if ($data['id']) {
                    $this->invoice_documents->update($data, $data['id']);

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
        $this->load->model("Zeapps_invoice_documents", "invoice_documents");

        if($document = $this->invoice_documents->get($id)){
            unlink($document->path);

            $this->invoice_documents->delete($id);
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