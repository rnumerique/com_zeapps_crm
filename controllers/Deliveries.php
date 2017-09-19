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

    public function config()
    {
        $this->load->view('deliveries/config');
    }



    public function makePDF($id, $echo = true){
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");

        $data = [];

        $data['delivery'] = $this->deliveries->get($id);
        $data['lines'] = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$id));
        $line_details = $this->delivery_line_details->all(array('id_order'=>$id));

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
        $html = $this->load->view('deliveries/PDF', $data, true);

        $nomPDF = $data['delivery']->name_company.'_'.$data['delivery']->numerotation.'_'.$data['delivery']->libelle;
        $nomPDF = preg_replace('/\W+/', '_', $nomPDF);
        $nomPDF = trim($nomPDF, '_');

        recursive_mkdir(FCPATH . 'tmp/com_zeapps_crm/deliveries/');

        //this the the PDF filename that user will get to download
        $pdfFilePath = FCPATH . 'tmp/com_zeapps_crm/deliveries/'.$nomPDF.'.pdf';

        //set the PDF header
        $this->M_pdf->pdf->SetHeader('Bon de livraison €<br>n° : '.$data['delivery']->numerotation.'|C. Compta : '.$data['delivery']->accounting_number.'|{DATE d/m/Y}');

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
        $num = $data['numerotation'];

        $result = $this->deliveries->parseFormat($format, $num);

        echo json_encode($result);
    }

    public function transform($id) {
        if($id) {
            $this->load->model("Zeapps_deliveries", "deliveries");
            $this->load->model("Zeapps_delivery_lines", "delivery_lines");
            $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");

            // constitution du tableau
            $data = array() ;

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
                // POST is actually in json format, do an internal translation
                $data = json_decode(file_get_contents('php://input'), true);
            }

            $return = [];

            if($src = $this->deliveries->get($id)){
                $src->lines = $this->delivery_lines->all(array('id_delivery' => $id));
                $src->line_details = $this->delivery_line_details->all(array('id_delivery' => $id));

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
        $this->load->model("Zeapps_deliveries", "deliveries");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if($id !== '0') {
            $filters['id_' . $type] = $id;
        }

        if(!$deliveries = $this->deliveries->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
            $deliveries = [];
        }

        $total = $this->deliveries->count($filters);

        $ids = [];
        if($total < 500) {
            if ($rows = $this->deliveries->get_ids($filters)) {
                foreach ($rows as $row) {
                    array_push($ids, $row->id);
                }
            }
        }

        if($context){

        }

        echo json_encode(array(
            'deliveries' => $deliveries,
            'total' => $total,
            'ids' => $ids
        ));

    }

    public function modal($limit = 15, $offset = 0) {
        $this->load->model("Zeapps_deliveries", "deliveries");

        $filters = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $filters = json_decode(file_get_contents('php://input'), true);
        }

        if(!$deliveries = $this->deliveries->limit($limit, $offset)->order_by(array('date_creation', 'id'), 'DESC')->all($filters)){
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
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");
        $this->load->model("Zeapps_delivery_documents", "delivery_documents");
        $this->load->model("Zeapps_delivery_activities", "delivery_activities");
        $this->load->model("Zeapps_credit_balances", "credit_balances");

        $delivery = $this->deliveries->get($id);

        $lines = $this->delivery_lines->order_by('sort')->all(array('id_delivery'=>$id));
        $line_details = $this->delivery_line_details->all(array('id_delivery'=>$id));
        $documents = $this->delivery_documents->all(array('id_delivery'=>$id));
        $activities = $this->delivery_activities->all(array('id_delivery'=>$id));

        if($delivery->id_company) {
            $credits = $this->credit_balances->all(array('id_company' => $delivery->id_company));
        }
        else {
            $credits = $this->credit_balances->all(array('id_contact' => $delivery->id_contact));
        }

        echo json_encode(array(
            'delivery' => $delivery,
            'lines' => $lines,
            'line_details' => $line_details,
            'documents' => $documents,
            'activities' => $activities,
            'credits' => $credits
        ));
    }

    public function save() {
        $this->load->model("Zeapps_configs", "configs");
        $this->load->model("Zeapps_deliveries", "deliveries");

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
            $num = $this->deliveries->get_numerotation();
            $data['numerotation'] = $this->deliveries->parseFormat($format, $num);

            $id = $this->deliveries->insert($data);
        }

        echo json_encode($id);
    }

    public function delete($id) {
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");
        $this->load->model("Zeapps_delivery_documents", "delivery_documents");

        $this->deliveries->delete($id);

        $this->delivery_lines->delete(array('id_delivery' => $id));
        $this->delivery_line_details->delete(array('id_delivery' => $id));

        $documents = $this->delivery_documents->all(array('id_delivery' => $id));

        $path = FCPATH;

        if($documents && is_array($documents)){
            for($i=0;$i<sizeof($documents);$i++){
                unlink($path . $documents[$i]->path);
            }
        }

        $this->delivery_documents->delete(array('id_delivery' => $id));

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
            $this->delivery_lines->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->delivery_lines->insert($data);
        }

        $this->_updateStocks($id);

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
            $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");

            $line = $this->delivery_lines->get($id);

            $this->delivery_lines->updateOldTable($line->id_delivery, $line->sort);

            $this->delivery_line_details->delete(array('id_delivery' => $line->id_delivery, 'id_line' => $id));

            echo json_encode($this->delivery_lines->delete($id));

        }
    }

    public function saveLineDetail(){
        $this->load->model("Zeapps_delivery_line_details", "delivery_line_details");

        // constitution du tableau
        $data = array() ;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if (isset($data["id"]) && is_numeric($data["id"])) {
            $this->delivery_line_details->update($data, $data["id"]);
            $id = $data['id'];
        } else {
            $id = $this->delivery_line_details->insert($data);
        }

        echo json_encode($id);
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

    public function _updateStocks($id){
        $this->load->model("Zeapps_deliveries", "deliveries");
        $this->load->model("Zeapps_delivery_lines", "delivery_lines");
        $this->load->model("Zeapps_product_products", "product");
        $this->load->model("Zeapps_stock_movements", "stock_movements");

        if($line = $this->delivery_lines->get($id)){
            if($line->type === 'product'){
                $delivery = $this->deliveries->get($line->id_delivery);
                $product = $this->product->get($line->id_product);
                $this->stock_movements->write(array(
                    "id_warehouse" => $delivery->id_warehouse,
                    "id_stock" => $product->id_stock,
                    "label" => "Bon de livraison n°".$delivery->numerotation,
                    "qty" => -1 * floatval($line->qty),
                    "id_table" => $delivery->id,
                    "name_table" => "zeapps_deliveries",
                    "date_mvt" => $delivery->date_creation,
                    "ignored" => 0
                ));
            }
        }
    }
}