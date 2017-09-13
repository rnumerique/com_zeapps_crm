<?php
class Zeapps_product_products extends ZeModel {

    public function insert($data = array()){
        $this->_pLoad->model("Zeapps_product_categories", "categories");

        //$this->_pLoad->ctrl->categories->newProductIn($data['id_cat']);

        return parent::insert($data);
    }

    public function update($data = array(), $where = array()){
        $this->_pLoad->model("Zeapps_product_categories", "categories");

        $product = parent::get($where);

        if($product->id_cat !== $data['id_cat']) {
            //$this->_pLoad->ctrl->categories->newProductIn($data['id_cat']);
            //$this->_pLoad->ctrl->categories->removeProductIn($product->id_cat);
        }

        return parent::update($data, $where);
    }

        }
        else{
            return 'no data sent';
        }
    }

    public function archive_products($id_arr = NULL){
        if($id_arr){
            foreach($id_arr as $id){
                $this->update(array('id_cat' => -1), array('id_cat' => $id));
            }
        }
        return;
    }
}