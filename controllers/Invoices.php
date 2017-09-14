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

    public function lists()
    {
        $this->load->view('invoices/lists');
    }

    public function lists_partial()
    {
        $this->load->view('invoices/lists_partial');
    }

    public function config()
    {
        $this->load->view('invoices/config');
    }



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");

        $data = [];

        $data['invoice'] = $this->invoices->get($id);
        $data['lines'] = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));
        $line_details = $this->invoice_line_details->all(array('id_order'=>$id));

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
        $html = $this->load->view('invoices/PDF', $data, true);

        $nomPDF = $data['invoice']->name_company.'_'.$data['invoice']->numerotation.'_'.$data['invoice']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/invoices/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/invoices/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Facture €<br>n° : '.$data['invoice']->numerotation.'|C. Compta : '.$data['invoice']->accounting_number.'|{DATE d/m/Y}');

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
        $num = $data['numerotation'];

        $result = $this->invoices->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_invoices", "invoices");
            $this->load->model("Zeapps_invoice_lines", "invoice_lines");
            $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");

            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($src = $this->invoices->get($id)){
                $src->lines = $this->invoice_lines->all(array('id_invoice' => $id));
                $src->line_details = $this->invoice_line_details->all(array('id_invoice' => $id));

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

    public function finalize($id) {
        if($id) {
            $this->load->model("Zeapps_configs", "configs");
            $this->load->model("Zeapps_invoices", "invoices");
            $this->load->model("Zeapps_invoice_lines", "invoice_lines");
            $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");
            $this->load->model("Zeapps_accounting_entries", "accounting_entries");
            $this->load->model("Zeapps_taxes", "taxes");

            if($invoice = $this->invoices->get($id)){
                if($invoice->id_modality === '0'){
                    echo json_encode(array('error' => 'Modalité de paiement non renseignée'));
                    return;
                }


                $lines = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));
                $line_details = $this->invoice_line_details->all(array('id_invoice'=>$id));

                $format = $this->configs->get(array('id'=>'crm_invoice_format'))->value;
                $num = $this->invoices->get_numerotation();

                $numerotation = $this->invoices->parseFormat($format, $num);

                $pdf = $this->makePDF($id, false);

                if($this->invoices->update(array(
                    'finalized' => 1,
                    'numerotation' => $numerotation,
                    'final_pdf' => $pdf
                ), $id)) {

                    $label_entry = $numerotation . ' - ';
                    $label_entry .= $invoice->name_company ?: ($invoice->name_contact ?: "");

                    $entries = [];
                    $tvas = [];
                    foreach ($lines as $line) {
                        if ($line->has_detail === '0') {
                            if (!isset($products[$line->accounting_number])) {
                                $products[$line->accounting_number] = 0;
                            }

                            $products[$line->accounting_number] += floatval($line->total_ht);

                            if ($line->id_taxe !== '0') {
                                if (!isset($tvas[$line->id_taxe])) {
                                    $tvas[$line->id_taxe] = array(
                                        'ht' => 0,
                                        'value_taxe' => floatval($line->value_taxe)
                                    );
                                }

                                $tvas[$line->id_taxe]['ht'] += floatval($line->total_ht);
                                $tvas[$line->id_taxe]['value'] = round(floatval($tvas[$line->id_taxe]['ht']) * ($tvas[$line->id_taxe]['value_taxe'] / 100), 2);
                            }
                        }
                    }
                    foreach ($line_details as $line) {
                        if (!isset($entries[$line->accounting_number])) {
                            $entries[$line->accounting_number] = 0;
                        }

                        $entries[$line->accounting_number] += floatval($line->total_ht);

                        if ($line->id_taxe !== '0') {
                            if (!isset($tvas[$line->id_taxe])) {
                                $tvas[$line->id_taxe] = array(
                                    'ht' => 0,
                                    'value_taxe' => floatval($line->value_taxe)
                                );
                            }

                            $tvas[$line->id_taxe]['ht'] += floatval($line->total_ht);
                            $tvas[$line->id_taxe]['value'] = round(floatval($tvas[$line->id_taxe]['ht']) * ($tvas[$line->id_taxe]['value_taxe'] / 100), 2);
                        }
                    }

                    foreach ($tvas as $id_taxe => $tva) {
                        $taxe = $this->taxes->get($id_taxe);

                        if (!isset($entries[$taxe->accounting_number])) {
                            $entries[$taxe->accounting_number] = 0;
                        }

                        $entries[$taxe->accounting_number] += floatval($tva['value']);
                    }

                    foreach ($entries as $accounting_number => $sum) {
                        $entry = array(
                            'id_invoice' => $id,
                            'accounting_number' => $accounting_number,
                            'label' => $label_entry,
                            'credit' => $sum,
                            'code' => 'VE',
                            'date_writing' => $invoice->date_creation,
                            'date_limit' => $invoice->date_limit
                        );

                        $this->accounting_entries->insert($entry);
                    }

                    $entry = array(
                        'id_invoice' => $id,
                        'accounting_number' => $invoice->accounting_number,
                        'label' => $label_entry,
                        'debit' => $invoice->total_ttc,
                        'code' => 'VE',
                        'date_writing' => $invoice->date_creation,
                        'date_limit' => $invoice->date_limit
                    );

                    $this->accounting_entries->insert($entry);

                    echo json_encode(array(
                        'numerotation' => $numerotation,
                        'final_pdf' => $pdf
                    ));
                }
            }
            else{
                echo json_encode(false);
            }
        }
        else{
            echo json_encode(false);
        }
    }

    public function getAll($id = '0', $type = 'company', $limit = 15, $offset = 0, $context = false) {
        $this->load->model("Zeapps_invoices", "invoices");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if(!$invoices = $this->invoices->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
            $invoices = [];
        }
        $total = $this->invoices->count($filters);

        $ids = [];
        if($total < 500) {
            if ($rows = $this->invoices->get_ids($filters)) {
                foreach ($rows as $row) {
                    array_push($ids, $row->id);
                }
            }
        }

        if($context){

        }

        echo json_encode(array(
            'invoices' => $invoices,
            'total' => $total,
            'ids' => $ids
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_invoices", "invoices");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$invoices = $this->invoices->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
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
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");
        $this->load->model("Zeapps_invoice_documents", "invoice_documents");
        $this->load->model("Zeapps_invoice_activities", "invoice_activities");
        $this->load->model("Zeapps_crm_origins", "crm_origins");

        $data = new stdClass();

        $data->invoice = $this->invoices->get($id);

        $data->lines = $this->invoice_lines->order_by('sort')->all(array('id_invoice'=>$id));
        $data->line_details = $this->invoice_line_details->all(array('id_invoice'=>$id));
        $data->documents = $this->invoice_documents->all(array('id_invoice'=>$id));
        $data->activities = $this->invoice_activities->all(array('id_invoice'=>$id));

        $res = $this->invoices->getDueOf('company', $data->invoice->id_company);
        $data->company_due = $res['due'];
        $data->company_due_lines = $res['due_lines'];

        $res = $this->invoices->getDueOf('contact', $data->invoice->id_contact);
        $data->contact_due = $res['due'];
        $data->contact_due_lines = $res['due_lines'];

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_invoices", "invoices");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->invoices->update($data, $data["id"]);
            $id = $data["id"];
        } else {
            $id = $this->invoices->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_invoices", "invoices");
        $this->load->model("Zeapps_invoice_lines", "invoice_lines");
        $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");
        $this->load->model("Zeapps_invoice_documents", "invoice_documents");

        $this->invoices->delete($id);

        $this->invoice_lines->delete(array('id_invoice' => $id));
        $this->invoice_line_details->delete(array('id_invoice' => $id));

        $documents = $this->invoice_documents->all(array('id_invoice' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                unlink($path . $documents[$i]->path);
            }
        }

        $this->invoice_documents->delete(array('id_invoice' => $id));

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
            $this->invoice_lines->update($data, $data["id"]);
            $id = $data['id'];
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
            $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");

            $line = $this->invoice_lines->get($id);

            $this->invoice_lines->updateOldTable($line->id_invoice, $line->sort);

            $this->invoice_line_details->delete(array('id_invoice' => $line->id_invoice, 'id_line' => $id));

            echo json_encode($this->invoice_lines->delete($id));

        }
    }

    public function saveLineDetail(){
        $this->load->model("Zeapps_invoice_line_details", "invoice_line_details");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->invoice_line_details->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->invoice_line_details->insert($data);
        }

        echo json_encode($id);
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
}