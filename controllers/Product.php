<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends ZeCtrl
{

    public function view()
    {
        $data = array() ;

        $this->load->view('product/view', $data);
    }

    public function details()
    {
        $data = array() ;

        $this->load->view('product/details', $data);
    }

    public function form($compose = false)
    {
        $data = array() ;

        if($compose)
            $this->load->view('product/form_compose', $data);
        else
            $this->load->view('product/form', $data);
    }

    public function modal_search_product(){
        $data = array() ;

        $this->load->view('product/modal_search_product', $data);
    }

    public function config()
    {
        $data = array() ;

        $this->load->view('product/config', $data);
    }







    public function get($id = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_products", "products");
            $this->load->model("Zeapps_product_lines", "lines");

            $product = $this->products->get($id);

            if($product && $product->compose == 1){
                $lines = $this->lines->all(array('id_product'=>$product->id));
                $product->lines = [];
                if($lines && is_array($lines)){
                    foreach ($lines as $line){
                        if($part = $this->products->get($line->id_part)){
                            $line->product = $part;
                        }
                        array_push($product->lines, $line);
                    }
                }
            }

            echo json_encode($product);
        }
        return;
    }

    public function getAll(){
        $this->load->model("Zeapps_product_products", "products");

        $products = $this->products->all();

        echo json_encode($products);
    }

    public function getProductsOf($id = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_products", "products");

            $products = $this->products->all(array('id_cat' => intval($id)));

            echo json_encode($products);
        }
        return;
    }

    public function save() {
        $this->load->model("Zeapps_product_products", "products");
        $this->load->model("Zeapps_product_categories", "categories");
        $this->load->model("Zeapps_product_lines", "lines");

        $error = NULL;

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);


            if(isset($data['lines'])) {
                $lines = $data['lines'];
                unset($data['lines']);
            }
            else{
                $lines = false;
            }

            if (isset($data["id"])) {
                $legacy = $this->products->get($data["id"]);
                $this->products->update($data, $data["id"]);

                if($data["id_cat"] != $legacy->id_cat) {
                    $this->categories->newProductIn($data['id_cat']);
                    if($legacy->id_cat > 0)
                        $this->categories->removeProductIn($legacy->id_cat);
                }
            } else {
                $data['id'] = $this->products->insert($data);
                $this->categories->newProductIn($data['id_cat']);
            }

            if(isset($data['compose']) && $data['compose'] == '1' && $lines){
                foreach ($lines as $line){
                    $line['id_product'] = $data['id'];
                    $line['auto'] = $data['auto'];
                    unset($line['product']);
                    if($line['id'] == 0){
                        unset($line['id']);
                        $this->lines->insert($line);
                    }
                    else{
                        $this->lines->update($line, $line['id']);
                    }
                }
            }
            if($lines = $this->lines->all(array('id_part'=>$data['id'], 'auto'=>true))){
                foreach ($lines as $line){
                    $this->_updatePriceOf($line->id_product);
                }
            }
        }

        echo json_encode("OK");
        return;

    }

    public function delete($id = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_products", "products");
            $this->load->model("Zeapps_product_categories", "categories");
            $this->load->model("Zeapps_product_lines", "lines");

            $product = $this->products->get($id);

            if($product->compose){
                if($lines = $this->lines->all(array('id_product' => $id))){
                    foreach($lines as $line){
                        $this->lines->delete($line->id);
                    }
                }
            }

            $this->products->delete($id);

            if($product->id_cat > 0)
                $this->categories->removeProductIn($product->id_cat);

        }
        return;
    }

    private function _updatePriceOf($id = null){
        if($id){
            if($lines = $this->lines->all(array('id_product'=>$id))){
                $price_ht = 0;
                $price_ttc = 0;
                foreach($lines as $line){
                    $part = $this->products->get($line->id_part);
                    $price_ht += ( floatval($part->price_ttc) * floatval($line->quantite) );
                    $price_ttc += ( floatval($part->price_ttc) * floatval($line->quantite) );
                }
                $this->products->update(array('price_ht'=>$price_ht, 'price_ttc'=>$price_ttc), $id);
            }
        }
    }
}