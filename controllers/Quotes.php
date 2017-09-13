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

    public function form_comment(){
        $this->load->view('quotes/form_comment');
    }

    public function form_activity()
    {
        $this->load->view('quotes/form_activity');
    }

    public function form_document()
    {
        $this->load->view('quotes/form_document');
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



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_lines", "quote_lines");
        $this->load->model("Zeapps_quote_line_details", "quote_line_details");

        $data = [];

        $data['quote'] = $this->quotes->get($id);
        $data['lines'] = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));
        $line_details = $this->quote_line_details->all(array('id_order'=>$id));

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
        foreach($line_details as $line){
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

        $nomPDF = $data['quote']->name_company.'_'.$data['quote']->numerotation.'_'.$data['quote']->libelle;
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

        $result = $this->quotes->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_quotes", "quotes");
            $this->load->model("Zeapps_quote_lines", "quote_lines");
            $this->load->model("Zeapps_quote_line_details", "quote_line_details");

            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($src = $this->quotes->get($id)){
                $src->lines = $this->quote_lines->all(array('id_quote' => $id));
                $src->line_details = $this->quote_line_details->all(array('id_quote' => $id));

                if($data){
                    foreach($data as $document => $value){
                        if($value == 'true'){
                            $this->load->model("Zeapps_".$document, $document);
                            $return[$document] = $this->$document->createFrom($src);
                        }
                    }
                }
            }

            echo json_encode($return);
        }
        else{
            echo json_encode(false);
        }
    }

    public function getAll($id = '0', $type = 'company', $limit = 15, $offset = 0, $context = false) {
        $this->load->model("Zeapps_quotes", "quotes");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if(!$quotes = $this->quotes->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
            $quotes = [];
        }
        $total = $this->quotes->count($filters);

        $ids = [];
        if($total < 500) {
            if ($rows = $this->quotes->get_ids($filters)) {
                foreach ($rows as $row) {
                    array_push($ids, $row->id);
                }
            }
        }

        if($context){

        }

        echo json_encode(array(
            'quotes' => $quotes,
            'total' => $total,
            'ids' => $ids
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_quotes", "quotes");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$quotes = $this->quotes->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
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
        $this->load->model("Zeapps_quote_lines", "quote_lines");
        $this->load->model("Zeapps_quote_line_details", "quote_line_details");
        $this->load->model("Zeapps_quote_documents", "quote_documents");
        $this->load->model("Zeapps_quote_activities", "quote_activities");
        $this->load->model("Zeapps_invoices", "invoices");

        $data = new stdClass();

        $data->quote = $this->quotes->get($id);

        $data->lines = $this->quote_lines->order_by('sort')->all(array('id_quote'=>$id));
        $data->line_details = $this->quote_line_details->all(array('id_quote'=>$id));
        $data->documents = $this->quote_documents->all(array('id_quote'=>$id));
        $data->activities = $this->quote_activities->all(array('id_quote'=>$id));

        $res = $this->quotes->getDueOf('company', $data->quote->id_company);
        $data->company_due = $res['due'];
        $data->company_due_lines = $res['due_lines'];

        $res = $this->quotes->getDueOf('contact', $data->quote->id_contact);
        $data->contact_due = $res['due'];
        $data->contact_due_lines = $res['due_lines'];

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_quotes", "quotes");

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
            $data['numerotation'] = $this->quotes->parseFormat($format, $num);

            $id = $this->quotes->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_quotes", "quotes");
        $this->load->model("Zeapps_quote_lines", "quote_lines");
        $this->load->model("Zeapps_quote_line_details", "quote_line_details");
        $this->load->model("Zeapps_quote_documents", "quote_documents");

        $this->quotes->delete($id);

        $this->quote_lines->delete(array('id_quote' => $id));
        $this->quote_line_details->delete(array('id_quote' => $id));

        $documents = $this->quote_documents->all(array('id_quote' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                unlink($path . $documents[$i]->path);
            }
        }

        $this->quote_documents->delete(array('id_quote' => $id));

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
            $this->quote_lines->update($data, $data["id"]);
            $id = $data['id'];
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
            $this->load->model("Zeapps_quote_line_details", "quote_line_details");

            $line = $this->quote_lines->get($id);

            $this->quote_lines->updateOldTable($line->id_quote, $line->sort);

            $this->quote_line_details->delete(array('id_quote' => $line->id_quote, 'id_line' => $id));

            echo json_encode($this->quote_lines->delete($id));

        }
    }

    public function saveLineDetail(){
        $this->load->model("Zeapps_quote_line_details", "quote_line_details");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->quote_line_details->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->quote_line_details->insert($data);
        }

        echo json_encode($id);
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
}