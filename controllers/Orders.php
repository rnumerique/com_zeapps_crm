<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends ZeCtrl
{
    public function view()
    {
        $this->load->view('orders/view');
    }

    public function form()
    {
        $this->load->view('orders/form');
    }

    public function form_modal()
    {
        $this->load->view('orders/form_modal');
    }

    public function form_line(){
        $this->load->view('orders/form_line');
    }

    public function lists()
    {
        $this->load->view('orders/lists');
    }

    public function lists_partial()
    {
        $this->load->view('orders/lists_partial');
    }

    public function config()
    {
        $this->load->view('orders/config');
    }



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_orders", "orders");
        $this->load->model("Zeapps_order_lines", "order_lines");
        $this->load->model("Zeapps_order_line_details", "order_line_details");

        $data = [];

        $data['order'] = $this->orders->get($id);
        $data['lines'] = $this->order_lines->order_by('sort')->all(array('id_order'=>$id));
        $line_details = $this->order_line_details->all(array('id_order'=>$id));

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
        $html = $this->load->view('orders/PDF', $data, true);

        $nomPDF = $data['order']->name_company.'_'.$data['order']->numerotation.'_'.$data['order']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/orders/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/orders/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Commande €<br>n° : '.$data['order']->numerotation.'|C. Compta : '.$data['order']->accounting_number.'|{DATE d/m/Y}');

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
        $file_url = FCPATH . 'tmp/com_zeapps_crm/orders/'.$nomPDF.'.pdf';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
        unlink($file_url);
    }

    public function testFormat(){
        $this->load->model("Zeapps_orders", "orders");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $format = $data['format'];
        $num = $data['numerotation'];

        $result = $this->orders->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_orders", "orders");
            $this->load->model("Zeapps_order_lines", "order_lines");
            $this->load->model("Zeapps_order_line_details", "order_line_details");

            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($src = $this->orders->get($id)){
                $src->lines = $this->order_lines->all(array('id_order' => $id));
                $src->line_details = $this->order_line_details->all(array('id_order' => $id));

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
        $this->load->model("Zeapps_orders", "orders");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if(!$orders = $this->orders->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
            $orders = [];
        }

        $total = $this->orders->count($filters);

        $ids = [];
        if($total < 500) {
            if ($rows = $this->orders->get_ids($filters)) {
                foreach ($rows as $row) {
                    array_push($ids, $row->id);
                }
            }
        }

        if($context){

        }

        echo json_encode(array(
            'orders' => $orders,
            'total' => $total,
            'ids' => $ids
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_orders", "orders");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$orders = $this->orders->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
            $orders = [];
        }
        $total = $this->orders->count($filters);

        echo json_encode(array(
            'data' => $orders,
            'total' => $total
        ));

    }

    public function get($id) {
        $this->load->model("Zeapps_orders", "orders");
        $this->load->model("Zeapps_order_lines", "order_lines");
        $this->load->model("Zeapps_order_line_details", "order_line_details");
        $this->load->model("Zeapps_order_documents", "order_documents");
        $this->load->model("Zeapps_order_activities", "order_activities");
        $this->load->model("Zeapps_invoices", "invoices");

        $data = new stdClass();

        $data->order = $this->orders->get($id);

        $data->lines = $this->order_lines->order_by('sort')->all(array('id_order'=>$id));
        $data->line_details = $this->order_line_details->all(array('id_order'=>$id));
        $data->documents = $this->order_documents->all(array('id_order'=>$id));
        $data->activities = $this->order_activities->all(array('id_order'=>$id));

        $res = $this->orders->getDueOf('company', $data->order->id_company);
        $data->company_due = $res['due'];
        $data->company_due_lines = $res['due_lines'];

        $res = $this->orders->getDueOf('contact', $data->order->id_contact);
        $data->contact_due = $res['due'];
        $data->contact_due_lines = $res['due_lines'];

        echo json_encode($data);
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_orders", "orders");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $id = $data["id"];
            $this->orders->update($data, array('id' => $data["id"]));
        } else {

            $format = $this->configs->get(array('id'=>'crm_order_format'))->value;
            $num = $this->orders->get_numerotation();
            $data['numerotation'] = $this->orders->parseFormat($format, $num);

            $id = $this->orders->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_orders", "orders");
        $this->load->model("Zeapps_order_lines", "order_lines");
        $this->load->model("Zeapps_order_line_details", "order_line_details");
        $this->load->model("Zeapps_order_documents", "order_documents");

        $this->orders->delete($id);

        $this->order_lines->delete(array('id_order' => $id));
        $this->order_line_details->delete(array('id_order' => $id));

        $documents = $this->order_documents->all(array('id_order' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                unlink($path . $documents[$i]->path);
            }
        }

        $this->order_documents->delete(array('id_order' => $id));

        echo json_encode("OK");
    }

    public function saveLine(){
        $this->load->model("Zeapps_order_lines", "order_lines");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->order_lines->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->order_lines->insert($data);
        }

        echo json_encode($id);
    }

    public function updateLinePosition(){
        $this->load->model("Zeapps_order_lines", "order_lines");

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
            $this->load->model("Zeapps_order_lines", "order_lines");
            $this->load->model("Zeapps_order_line_details", "order_line_details");

            $line = $this->order_lines->get($id);

            $this->order_lines->updateOldTable($line->id_order, $line->sort);

            $this->order_line_details->delete(array('id_order' => $line->id_order, 'id_line' => $id));

            echo json_encode($this->order_lines->delete($id));

        }
    }

    public function saveLineDetail(){
        $this->load->model("Zeapps_order_line_details", "order_line_details");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->order_line_details->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->order_line_details->insert($data);
        }

        echo json_encode($id);
    }

    public function activity(){
        $this->load->model("Zeapps_order_activities", "order_activities");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if($data['id']){
            $this->order_activities->update($data, $data['id']);
            $id = $data['id'];
        }
        else{
            $id = $this->order_activities->insert($data);
        }

        $order_activities = $this->order_activities->get($id);

        echo json_encode($order_activities);
    }

    public function del_activity($id){
        $this->load->model("Zeapps_order_activities", "order_activities");

        echo json_encode($this->order_activities->delete($id));
    }

    public function uploadDocuments($id_order = null){
        if($id_order) {
            $this->load->model("Zeapps_order_documents", "order_documents");

            $data = $_POST;
            $files = $_FILES['files'];
            if($files) {
                if($data['path']){
                    unlink($data['path']);
                }

                $data['id_order'] = $id_order;

                $path = '/assets/upload/crm/orders/';

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
                    $this->order_documents->update($data, $data['id']);
                } else {
                    $data['id'] = $this->order_documents->insert($data);
                }
                $data['date'] = date('Y-m-d H:i:s');

                echo json_encode($data);
            }
            else{
                if ($data['id']) {
                    $this->order_documents->update($data, $data['id']);

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
        $this->load->model("Zeapps_order_documents", "order_documents");

        if($document = $this->order_documents->get($id)){
            unlink($document->path);

            $this->order_documents->delete($id);
        }

        echo 'OK';
    }
}