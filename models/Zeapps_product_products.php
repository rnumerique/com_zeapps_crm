<?php
class Zeapps_product_products extends ZeModel {

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