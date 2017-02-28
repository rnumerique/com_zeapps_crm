<?php
class Zeapps_product_stocks extends ZeModel {

    public function get($where = array()){
        $where['zeapps_product_stocks.deleted_at'] = null;

        $this->database()->clearSql();

        return $this->database()->select('*,
                                        zeapps_product_stocks.id as id,
                                        zeapps_product_stocks.label as label,
                                        sum(zeapps_stocks.total) as total')
                                ->join('zeapps_stocks', 'zeapps_stocks.id_stock = zeapps_product_stocks.id', 'left')
                                ->where($where)
                                ->table('zeapps_product_stocks')
                                ->result();
    }

}