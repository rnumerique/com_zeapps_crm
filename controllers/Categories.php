<?php

/**
 * Created by PhpStorm.
 * User: developpeur
 * Date: 16/12/2016
 * Time: 10:09
 */
class Categories extends ZeCtrl
{

    public function form()
    {
        $data = array() ;

        $this->load->view('product/form_category', $data);
    }

    public function get_tree(){
        $this->load->model("Zeapps_product_categories", "categories");

        $categories = $this->categories->order_by('sort')->all();

        if ($categories == false) {
            echo json_encode(array());
        } else {
            $result = $this->_build_tree($categories);
            echo json_encode($result);
        }
    }

    public function get($id = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_categories", "categories");

            $category = $this->categories->get($id);

            echo json_encode($category);
        }

        return;
    }

    public function update_order(){
        $this->load->model("Zeapps_product_categories", "categories");


        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);

                if (count($data['categories']) > 1) {
                    foreach ($data['categories'] as $category) {
                        $this->categories->update(array('sort' => intval($category['sort'])), array('id' => intval($category['id'])));
                    }
                }
        }

        echo json_encode('OK');

        return;
    }

    public function getSubCategoriesOf($id = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_categories", "categories");

            $categories = $this->categories->all("id_parent", $id);

            echo json_encode($categories);
        }

        return;
    }

    public function save() {
        $this->load->model("Zeapps_product_categories", "categories");

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') === 0 && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE) {
            // POST is actually in json format, do an internal translation
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data["id"])) {
                $this->categories->update($data, $data["id"]);
            } else {
                $id = $this->categories->insert($data);
                $this->categories->newProductIn($data['id_parent']);
            }
        }

        echo json_encode($id);
    }

    public function delete($id = NULL, $force_delete = NULL){
        if(isset($id)){
            $this->load->model("Zeapps_product_categories", "categories");
            $category = $this->categories->get($id);
            if( (intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0) && !isset($force_delete) ){
                echo json_encode(array('hasProducts' => true));
            }
            else {
                if ((intval($category->nb_products_r) == 0 && intval($category->nb_products) == 0) || (isset($force_delete) && $force_delete === "true")) {
                    $this->_force_delete($id);
                } else if (isset($force_delete) && $force_delete === "false") {
                    $this->_safe_delete($id);
                }
                $parent = $this->categories->get($category->id_parent);
                echo json_encode($parent);
            }
        }

        return;
    }

    private function _build_tree($categories, $id = -2){
        $result = array();

        foreach($categories as $category){
            if($category->id_parent == $id){

                $tmp = $category;
                $res = $this->_build_tree($categories, $category->id);
                if(!empty($res)) {
                    $tmp->branches = $res;
                }
                $tmp->open = false;
                $result[] = $tmp;
            }
        }

        return $result;
    }



    private function _force_delete($id = NULL){
        if($id){
            $this->load->model("Zeapps_product_products", "products");
            $this->load->model("Zeapps_product_categories", "categories");
            $category = $this->categories->get($id);
            $id_arr = $this->categories->delete_r($id);
            if( intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0 ) {
                $this->categories->removeProductIn($category->id_parent, true, intval($category->nb_products_r) + intval($category->nb_products));
                foreach($id_arr as $id) {
                    $this->products->delete(array('category' => $id));
                }
            }
            return;
        }
        return;
    }

    private function _safe_delete($id = NULL){
        if($id){
            $this->load->model("Zeapps_product_products", "products");
            $this->load->model("Zeapps_product_categories", "categories");
            $category = $this->categories->get($id);
            $id_arr = $this->categories->delete_r($id);
            if( intval($category->nb_products_r) > 0 || intval($category->nb_products) > 0 ) {
                $this->categories->removeProductIn($category->id_parent, true, intval($category->nb_products_r) + intval($category->nb_products));
                $this->products->archive_products($id_arr);
            }
            return;
        }
        return;
    }

}