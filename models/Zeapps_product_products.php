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

    public function top10($year = null, $where = array()){
        $query = "SELECT SUM(l.total_ht) as total_ht,
                         p.name as name
                  FROM zeapps_product_categories ca
                  LEFT JOIN zeapps_product_products p ON p.id_cat = ca.id
                  LEFT JOIN zeapps_invoice_lines l ON l.id_product = p.id
                  LEFT JOIN zeapps_invoices i ON i.id = l.id_invoice
                  WHERE i.finalized = '1'
                        AND l.type = 'product'
                        AND i.deleted_at IS NULL
                        AND l.deleted_at IS NULL
                        AND YEAR(i.date_limit) = ".$year;

        if(isset($where['id_origin'])){
            $query .= " AND i.id_origin = ".$where['id_origin'];
        }
        if(isset($where['id_cat'])){
            $query .= " AND ca.id IN (".implode(',', $where['id_cat']).")";
        }
        if(isset($where['country_id'])){
            $query .= " AND i.country_id IN (".implode(',', $where['country_id']).")";
        }

        $query .= " GROUP BY p.id ORDER BY total_ht DESC LIMIT 10";

        return $this->database()->customQuery($query)->result();
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