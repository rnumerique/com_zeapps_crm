<?php
class Zeapps_product_products extends ZeModel {

    public function test($data = NULL){
        if($data){
            //Tests to see if the data we receive fits what we want
            if(!isset($data['name']) || empty($data['name']) || !is_string($data['name']) || strlen($data['name']) > 255){
                echo 'There\'s an error with the name of the product, make sure it\'s a string of characters shorter than 256 characters.';
                return;
            }
            else if(!isset($data['desc_short']) || empty($data['desc_short']) || !is_string($data['desc_short']) || strlen($data['desc_short']) > 140){
                echo 'There\'s an error with the short description, make sure it\'s a string of characters shorter than 140 characters.';
                return;
            }
            else if(!isset($data['category']) || empty($data['category']) || !is_numeric($data['category']) || strlen($data['category']) > 11){
                echo 'There\'s an error with the category, make sure to select a category from the list.';
                return;
            }
            else if(!isset($data['price']) || empty($data['price']) || !is_numeric($data['price']) || strlen($data['price']) > 10){
                echo 'There\'s an error with the price, make sure it\'s a numeric value with only 2 numbers after the separator (format: 7,2).';
                return;
            }
            else if(!isset($data['account']) || empty($data['account']) || !is_numeric($data['account']) || strlen($data['account']) > 10){
                echo 'There\'s an error with the account, make sure to select an account from the list.';
                return;
            }
            else if(strlen($data['desc_long']) > 1000){
                echo 'There\'s an error with the long description, make sure it\'s a string of characters shorter than 1000 characters.';
                return;
            }
            else{
                return NULL;
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