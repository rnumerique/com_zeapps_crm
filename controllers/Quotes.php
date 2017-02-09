<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotes extends ZeCtrl
{
    public function view()
    {
        $data = array() ;

        $this->load->view('quotes/view', $data);
    }

    public function form()
    {
        $data = array() ;

        $this->load->view('quotes/form', $data);
    }

    public function lists()
    {
        $data = array() ;

        $this->load->view('quotes/lists', $data);
    }

    public function config()
    {
        $data = array() ;

        $this->load->view('quotes/config', $data);
    }



    public function makePDF($id){

        $this->load->model("zeapps_quotes", "quotes");
        $this->load->model("zeapps_quote_companies", "quote_companies");
        $this->load->model("zeapps_quote_contacts", "quote_contacts");
        $this->load->model("zeapps_quote_lines", "quote_lines");

        $data = [];

        $data['quote'] = $this->quotes->get($id);

        $data['company'] = $this->quote_companies->get(array('id_quote'=>$id));
        $data['contact'] = $this->quote_contacts->get(array('id_quote'=>$id));
        $data['lines'] = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));

        //load the view and saved it into $html variable
        $html = $this->load->view('quotes/PDF', $data, true);

        $nomPDF = $data['company']->company_name.'_'.$data['quote']->numerotation.'_'.$data['quote']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/quotes/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/quotes/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Devis n° : '.$data['quote']->numerotation.'|Compte Comptable : '.$data['quote']->accounting_number.'|{DATE d/m/Y}');

        //set the PDF footer
        $this->M_pdf->pdf->SetFooter('{PAGENO}/{nb}');

        //generate the PDF from the given html
        $this->M_pdf->pdf->WriteHTML($html);

        //download it.
        $this->M_pdf->pdf->Output($pdfFilePath, "F");

        echo json_encode($nomPDF);
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
        $this->load->model("zeapps_quotes", "quotes");

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

    public function createOrderFrom($id){
        if($id){
            $this->load->model("zeapps_configs", "configs");
            $this->load->model("zeapps_quotes", "quotes");
            $this->load->model("zeapps_quote_companies", "quote_companies");
            $this->load->model("zeapps_quote_contacts", "quote_contacts");
            $this->load->model("zeapps_quote_lines", "quote_lines");
            $this->load->model("zeapps_orders", "orders");
            $this->load->model("zeapps_order_companies", "order_companies");
            $this->load->model("zeapps_order_contacts", "order_contacts");
            $this->load->model("zeapps_order_lines", "order_lines");

            $quote = $this->quotes->get($id);

            unset($quote->id);
            unset($quote->numerotation);
            unset($quote->created_at);
            unset($quote->updated_at);


            $format = $this->configs->get(array('id'=>'crm_order_format'))->value;
            $frequency = $this->configs->get(array('id'=>'crm_order_frequency'))->value;
            $num = $this->orders->get_numerotation($frequency);
            $quote->numerotation = $this->parseFormat($format, $num);

            $id_order = $this->orders->insert($quote);

            if($companies = $this->quote_companies->all(array('id_quote'=>$id))){
                foreach($companies as $company){
                    unset($company->id);
                    unset($company->id_quote);
                    unset($company->created_at);
                    unset($company->updated_at);

                    $company->id_order = $id_order;

                    $this->order_companies->insert($company);
                }
            }

            if($contacts = $this->quote_contacts->all(array('id_quote'=>$id))){
                foreach($contacts as $contact){
                    unset($contact->id);
                    unset($contact->id_quote);
                    unset($contact->created_at);
                    unset($contact->updated_at);

                    $contact->id_order = $id_order;

                    $this->order_contacts->insert($contact);
                }
            }

            if($lines = $this->quote_lines->all(array('id_quote'=>$id))){
                foreach($lines as $line){
                    unset($line->id);
                    unset($line->id_quote);
                    unset($line->created_at);
                    unset($line->updated_at);

                    $line->id_order = $id_order;

                    $this->order_lines->insert($line);
                }
            }

        }

        echo json_encode($id_order);
    }

    public function getAll($id_company = '0') {
        $this->load->model("zeapps_users", "users");
        $this->load->model("zeapps_quotes", "quotes");
        $this->load->model("zeapps_quote_companies", "quote_companies");
        $this->load->model("zeapps_quote_contacts", "quote_contacts");
        $this->load->model("zeapps_quote_lines", "quote_lines");

        if($id_company !== '0')
            $quotes = $this->quotes->all(array('id_company'=>$id_company));
        else
            $quotes = $this->quotes->all();

        if($quotes && is_array($quotes)){
            for($i=0;$i<sizeof($quotes);$i++){
                $user = $this->users->get($quotes[$i]->id_user);
                if($user) {
                    $quotes[$i]->user_name = $user->firstname[0] . '. ' . $user->lastname;
                }
                $quotes[$i]->company = $this->quote_companies->get(array('id_quote'=>$quotes[$i]->id));
                $quotes[$i]->contact = $this->quote_contacts->get(array('id_quote'=>$quotes[$i]->id));
                $quotes[$i]->lines = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$quotes[$i]->id));
            }
        }

        if ($quotes == false) {
            echo json_encode(array());
        } else {
            echo json_encode($quotes);
        }

    }

    public function get($id) {
        $this->load->model("zeapps_quotes", "quotes");
        $this->load->model("zeapps_quote_companies", "quote_companies");
        $this->load->model("zeapps_quote_contacts", "quote_contacts");
        $this->load->model("zeapps_quote_lines", "quote_lines");
        $this->load->model("zeapps_quote_documents", "quote_documents");
        $this->load->model("zeapps_quote_activities", "quote_activities");

        $data = new stdClass();

        $data->quote = $this->quotes->get($id);

        $data->company = $this->quote_companies->get(array('id_quote'=>$id));
        $data->contact = $this->quote_contacts->get(array('id_quote'=>$id));
        $data->lines = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));
        $data->documents = $this->quote_documents->all(array('id_quote'=>$id));
        $data->activities = $this->quote_activities->all(array('id_quote'=>$id));

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("zeapps_configs", "configs");
        $this->load->model("zeapps_companies", "companies");
        $this->load->model("zeapps_contacts", "contacts");
        $this->load->model("zeapps_quotes", "quotes");
        $this->load->model("zeapps_quote_companies", "quote_companies");
        $this->load->model("zeapps_quote_contacts", "quote_contacts");
        $this->load->model("zeapps_quote_lines", "quote_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->quotes->update($data, $data["id"]);
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
                    unset($company->id);
                    unset($company->created_at);
                    unset($company->updated_at);
                    unset($company->deleted_at);
                    $company->id_quote = $id;
                    $this->quote_companies->insert($company);
                }
                if($contact){
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
        $this->load->model("zeapps_quotes", "quotes");
        $this->load->model("zeapps_quote_companies", "quote_companies");
        $this->load->model("zeapps_quote_contacts", "quote_contacts");
        $this->load->model("zeapps_quote_lines", "quote_lines");
        $this->load->model("zeapps_quote_documents", "quote_documents");

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
        $this->load->model("zeapps_quote_lines", "quote_lines");

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
        $this->load->model("zeapps_quote_lines", "quote_lines");

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
            $this->load->model("zeapps_quote_lines", "quote_lines");

            $line = $this->quote_lines->get($id);

            $this->quote_lines->updateOldTable($line->id_quote, $line->sort);

            echo json_encode($this->quote_lines->delete($id));

        }
    }

    public function uploadDocuments($id_quote = null){
        if($id_quote) {
            $this->load->model("zeapps_quote_documents", "quote_documents");

            $data = [];
            $res = [];

            $data['id_quote'] = $id_quote;

            $files = $_FILES['files'];

            $path = '/assets/upload/crm/quotes/';

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

                $data['id'] = $this->quote_documents->insert($data);

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
            $this->load->model("zeapps_quote_documents", "quote_documents");

            $document = $this->quote_documents->get($id);

            $path = FCPATH;

            if(unlink($path . $document->path))
                echo json_encode($this->quote_documents->delete($id));
            else
                echo json_encode(false);

        }
        else{
            echo json_encode(false);
        }
        return true;
    }

    public function saveActivity(){
        $this->load->model("zeapps_quote_activities", "quote_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->quote_activities->update($data, $data["id"]);
        } else {
            $id = $this->quote_activities->insert($data);
            $data['id'] = $id;
        }

        echo json_encode($data);
    }

    public function deleteActivity($id = null){
        if($id){
            $this->load->model("zeapps_quote_activities", "quote_activities");

            echo json_encode($this->quote_activities->delete($id));

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