<?php
class Zeapps_stocks extends ZeModel {
    public function all($where = array(), $limit = 2147483647, $offset = 0){

        $where['deleted_at'] = null;

        return $this->database()->select('*,
                                        sum(total) as total')
                                ->where($where)
                                ->limit($limit, $offset)
                                ->group_by('id_stock')
                                ->table('zeapps_stocks')
                                ->result();

    }
}