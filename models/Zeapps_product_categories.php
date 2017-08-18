<?php
class Zeapps_product_categories extends ZeModel {

    private $root;
    private $archive;

    public function __construct()
    {
        $this->init_default_categories();

        parent::__construct();
    }

    private function init_default_categories(){
        $this->root = new stdClass();
        $this->root->name = 'racine';
        $this->root->id = '0';
        $this->root->id_parent = '-2';
        $this->root->open = false;

        $this->archive = new stdClass();
        $this->archive->name = 'archive';
        $this->archive->id = '-1';
        $this->archive->id_parent = '-2';
        $this->archive->open = false;

        return;
    }

    public function get($where = array()){
        if($where == 0){
            return $this->root;
        }
        else if($where == -1){
            return $this->archive;
        }
        else{
            return parent::get($where);
        }
        return;
    }

    public function get_select(){
        $where = array('deleted_at' => null);

        return $this->database()->select('zeapps_product_categories.name as label,
                                        zeapps_product_categories.id as value')
            ->where($where)
            ->table('zeapps_product_categories')
            ->result();
    }

    public function all($where = array()){

        $res = parent::all($where);
        if($res)
            array_unshift($res, $this->root, $this->archive);
        else
            $res = array($this->root, $this->archive);

        return $res;
    }

    public function newProductIn($id = array(), $parent = false){
        if($id) {
            $res = $this->get($id);
            if(!$parent) {
                $data = Array('nb_products' => ($res->nb_products + 1));
                $this->update($data, $id);
            }
            else{
                $data = Array('nb_products_r' => ($res->nb_products_r + 1));
                $this->update($data, $id);
            }
            if($res->id_parent > 0){
                $this->newProductIn($res->id_parent, true);
            }
        }
        return;
    }

    public function removeProductIn($id = array(), $parent = false, $qty = 1){
        if($id) {
            $res = $this->get($id);
            if(!$parent) {
                $data = Array('nb_products' => ($res->nb_products - $qty));
                $this->update($data, $id);
            }
            else{
                $data = Array('nb_products_r' => ($res->nb_products_r - $qty));
                $this->update($data, $id);
            }
            if($res->id_parent > 0){
                $this->removeProductIn($res->id_parent, true, $qty);
            }
        }
        return;
    }

    public function delete_r($id = NULL, $categories = NULL){
        if($id){
            if(!$categories){
                $categories = $this->all();
            }
            $id_arr = array($id);
            foreach($categories as $category){
                if($category->id_parent == $id){
                    $res = $this->delete_r($category->id, $categories);
                    foreach($res as $entry){
                        array_push($id_arr, $entry);
                    }
                }
            }
            $this->delete($id);
            return $id_arr;
        }
        return false;
    }
}