<?php
class Zeapps_stocks extends ZeModel {
    public function all($where = array()){

        $where['deleted_at'] = null;

        return $this->database()->select('*,
                                        sum(total) as total')
                                ->where($where)
                                ->group_by('id_stock')
                                ->table('zeapps_stocks')
                                ->result();

    }
}