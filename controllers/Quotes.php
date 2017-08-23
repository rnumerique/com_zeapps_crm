<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotes extends ZeCtrl
{
    public function view()
    {
        $this->load->view('quotes/view');
    }

    public function form()
    {
        $this->load->view('quotes/form');
    }

    public function form_modal()
    {
        $this->load->view('quotes/form_modal');
    }

    public function form_line(){
        $this->load->view('quotes/form_line');
    }

    public function lists()
    {
        $this->load->view('quotes/lists');
    }

    public function lists_partial()
    {
        $this->load->view('quotes/lists_partial');
    }

    public function transform_modal()
    {
        $this->load->view('quotes/transform_modal');
    }

    public function config()
    {
        $this->load->view('quotes/config');
    }

    public function modal_activity()
    {
        $this->load->view('quotes/modal_activity');
    }

    public function modal_document()
    {
        $this->load->view('quotes/modal_document');
    }



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_companies", "quote_companies");
        $this->load->model("Zeapps_quote_contacts", "quote_contacts");
        $this->load->model("Zeapps_quote_lines", "quote_lines");

        $data = [];

        $data['quote'] = $this->quotes->get($id);

        $data['company'] = $this->quote_companies->get(array('id_quote'=>$id));
        $data['contact'] = $this->quote_contacts->get(array('id_quote'=>$id));
        $data['lines'] = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));

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
        $html = $this->load->view('quotes/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['quote']->numerotation.'_'.$data['quote']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/quotes/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/quotes/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Devis €<br>n° : '.$data['quote']->numerotation.'|C. Compta : '.$data['quote']->accounting_number.'|{DATE d/m/Y}');

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
        $file_url = FCPATH . 'tmp/com_zeapps_crm/quotes/'.$nomPDF.'.pdf';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
        unlink($file_url);
    }

    public function testFormat(){
        $this->load->model("Zeapps_quotes", "quotes");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $frequency = $data['frequency'];
        $num = $this->quotes->get_numerotation($frequency);

        $result = $this->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_quotes", "quotes");

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
            $this->load->model("Zeapps_quotes", "quotes");
            $this->load->model("Zeapps_quote_companies", "quote_companies");
            $this->load->model("Zeapps_quote_contacts", "quote_contacts");
            $this->load->model("Zeapps_quote_lines", "quote_lines");
            $type_model = $type."_model";
            $this->load->model("Zeapps_".$type."s", $type_model);
            $type_companies = $type."_companies";
            $this->load->model("Zeapps_".$type."_companies", $type_companies);
            $type_contacts = $type."_contacts";
            $this->load->model("Zeapps_".$type."_contacts", $type_contacts);
            $type_lines = $type."_lines";
            $this->load->model("Zeapps_".$type."_lines", $type_lines);

            $quote = $this->quotes->get($id);

            unset($quote->id);
            unset($quote->numerotation);
            unset($quote->created_at);
            unset($quote->updated_at);

            $format = $this->configs->get(array('id'=>'crm_'.$type.'_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_'.$type.'_frequency'))->value;
            $num = $this->$type_model->get_numerotation($frequency);
            $quote->numerotation = $this->parseFormat($format, $num);

            $new_id = $this->$type_model->insert($quote);
            $id_key = "id_".$type;

            if($companies = $this->$type_companies->all(array('id_quote'=>$id))){
                foreach($companies as $company){
                    unset($company->id);
                    unset($company->id_quote);
                    unset($company->created_at);
                    unset($company->updated_at);

                    $company->$id_key = $new_id;

                    $this->$type_companies->insert($company);
                }
            }

            if($contacts = $this->$type_contacts->all(array('id_quote'=>$id))){
                foreach($contacts as $contact){
                    unset($contact->id);
                    unset($contact->id_quote);
                    unset($contact->created_at);
                    unset($contact->updated_at);

                    $contact->$id_key = $new_id;

                    $this->$type_contacts->insert($contact);
                }
            }

            if($lines = $this->$type_lines->all(array('id_quote'=>$id))){
                foreach($lines as $line){
                    unset($line->id);
                    unset($line->id_quote);
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
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_companies", "quote_companies");
        $this->load->model("Zeapps_quote_contacts", "quote_contacts");
        $this->load->model("Zeapps_quote_lines", "quote_lines");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if($quotes = $this->quotes->limit($limit, $offset)->all($filters)){
            for($i=0;$i<sizeof($quotes);$i++){
                $quotes[$i]->company = $this->quote_companies->get(array('id_quote'=>$quotes[$i]->id));
                $quotes[$i]->contact = $this->quote_contacts->get(array('id_quote'=>$quotes[$i]->id));
                $quotes[$i]->lines = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$quotes[$i]->id));
            }
        }
        else{
            $quotes = [];
        }
        $total = $this->quotes->count($filters);

        if($context){

        }

        echo json_encode(array(
            'quotes' => $quotes,
            'total' => $total
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_quotes", "quotes");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$quotes = $this->quotes->limit($limit, $offset)->all($filters)){
            $quotes = [];
        }
        $total = $this->quotes->count($filters);

        echo json_encode(array(
            'data' => $quotes,
            'total' => $total
        ));

    }

    public function get($id) {
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_companies", "quote_companies");
        $this->load->model("Zeapps_quote_contacts", "quote_contacts");
        $this->load->model("Zeapps_quote_lines", "quote_lines");
        $this->load->model("Zeapps_quote_documents", "quote_documents");
        $this->load->model("Zeapps_quote_activities", "quote_activities");
        $this->load->model("Zeapps_invoices", "invoices");

        $data = new stdClass();

        $data->quote = $this->quotes->get($id);

        $data->company = $this->quote_companies->get(array('id_quote'=>$id));
        $data->contact = $this->quote_contacts->get(array('id_quote'=>$id));
        $data->lines = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));
        $data->documents = $this->quote_documents->all(array('id_quote'=>$id));
        $data->activities = $this->quote_activities->all(array('id_quote'=>$id));

        if($data->company){
            $res = $this->invoices->getDueOf('company', $data->quote->id_company);
            $data->company->due = $res['due'];
            $data->company->due_lines = $res['due_lines'];
        }
        elseif($data->contact){
            $res = $this->invoices->getDueOf('contact', $data->quote->id_contact);
            $data->contact->due = $res['due'];
            $data->contact->due_lines = $res['due_lines'];
        }

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_companies", "companies", "com_zeapps_contact");
        $this->load->model("Zeapps_contacts", "contacts", "com_zeapps_contact");
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_companies", "quote_companies");
        $this->load->model("Zeapps_quote_contacts", "quote_contacts");
        $this->load->model("Zeapps_quote_lines", "quote_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->quotes->update($data, array('id' => $data["id"]));
        } else {

            $format = $this->configs->get(array('id'=>'crm_quote_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_quote_frequency'))->value;
            $num = $this->quotes->get_numerotation($frequency);
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
            $id = $this->quotes->insert($data);
            if($id) {
                if($company){
                    $company->id_company = $company->id;
                    unset($company->id);
                    unset($company->created_at);
                    unset($company->updated_at);
                    unset($company->deleted_at);
                    $company->id_quote = $id;
                    $this->quote_companies->insert($company);
                }
                if($contact){
                    $contact->id_contact = $contact->id;
                    unset($contact->id);
                    unset($contact->created_at);
                    unset($contact->updated_at);
                    unset($contact->deleted_at);
                    $contact->id_quote = $id;
                    $this->quote_contacts->insert($contact);
                }
            }
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_companies", "quote_companies");
        $this->load->model("Zeapps_quote_contacts", "quote_contacts");
        $this->load->model("Zeapps_quote_lines", "quote_lines");
        $this->load->model("Zeapps_quote_documents", "quote_documents");

        $this->quotes->delete($id);

        $companies = $this->quote_companies->all(array('id_quote' => $id));

        if($companies && is_array($companies)){
            for($i=0;$i<sizeof($companies);$i++){
                $this->quote_companies->delete($companies[$i]->id);
            }
        }

        $contacts = $this->quote_contacts->all(array('id_quote' => $id));

        if($contacts && is_array($contacts)){
            for($i=0;$i<sizeof($contacts);$i++){
                $this->quote_contacts->delete($contacts[$i]->id);
            }
        }

        $lines = $this->quote_lines->all(array('id_quote' => $id));

        if($lines && is_array($lines)){
            for($i=0;$i<sizeof($lines);$i++){
                $this->quote_lines->delete($lines[$i]->id);
            }
        }

        $documents = $this->quote_documents->all(array('id_quote' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                $this->quote_documents->delete($documents[$i]->id);
                unlink($path . $documents[$i]->path);
            }
        }

        echo json_encode("OK");
    }

    public function saveLine(){
        $this->load->model("Zeapps_quote_lines", "quote_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $this->quote_lines->update($data, $data["id"]);
        } else {
            $id = $this->quote_lines->insert($data);
        }

        echo json_encode($id);
    }

    public function updateLinePosition(){
        $this->load->model("Zeapps_quote_lines", "quote_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data)) {
            $line = $this->quote_lines->get($data['id']);

            $this->quote_lines->updateOldTable($line->id_quote, $data['oldSort']);
            $this->quote_lines->updateNewTable($line->id_quote, $data['sort']);

            $id = $this->quote_lines->update(array('sort'=>$data['sort']), $data["id"]);
        }

        echo json_encode($id);
    }

    public function deleteLine($id = null){
        if($id){
            $this->load->model("Zeapps_quote_lines", "quote_lines");

            $line = $this->quote_lines->get($id);

            $this->quote_lines->updateOldTable($line->id_quote, $line->sort);

            echo json_encode($this->quote_lines->delete($id));

        }
    }

    public function activity(){
        $this->load->model("Zeapps_quote_activities", "quote_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['id']){
            $this->quote_activities->update($data, $data['id']);
            $id = $data['id'];
        }
        else{
            $id = $this->quote_activities->insert($data);
        }

        $quote_activities = $this->quote_activities->get($id);

        echo json_encode($quote_activities);
    }

    public function del_activity($id){
        $this->load->model("Zeapps_quote_activities", "quote_activities");

        echo json_encode($this->quote_activities->delete($id));
    }

    public function uploadDocuments($id_quote = null){
        if($id_quote) {
            $this->load->model("Zeapps_quote_documents", "quote_documents");

            $data = $_POST;
            $files = $_FILES['files'];
            if($files) {
                if($data['path']){
                    unlink($data['path']);
                }

                $data['id_quote'] = $id_quote;

                $path = '/assets/upload/crm/quotes/';

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
                    $this->quote_documents->update($data, $data['id']);
                } else {
                    $data['id'] = $this->quote_documents->insert($data);
                }
                $data['date'] = date('Y-m-d H:i:s');

                echo json_encode($data);
            }
            else{
                if ($data['id']) {
                    $this->quote_documents->update($data, $data['id']);

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
        $this->load->model("Zeapps_quote_documents", "quote_documents");

        if($document = $this->quote_documents->get($id)){
            unlink($document->path);

            $this->quote_documents->delete($id);
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