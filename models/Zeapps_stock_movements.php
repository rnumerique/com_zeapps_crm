<?php
class Zeapps_stock_movements extends ZeModel{

    public function avg($where = array()){
        $query = "select sum(zeapps_stock_movements.qty) as average 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and qty < 0
                  and date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE()
                  and id_stock = " . $where['id_stock'];

        $res = $this->database()->customQuery($query)->result();

        if($res){

            if($ret = $this->database()->select('date_mvt')->where(array('deleted_at' => null, 'id_stock' => $where['id_stock']))->table('zeapps_stock_movements')->result()){
                $first = $ret[0]->date_mvt;
                $now = time(); // or your date as well
                $first = strtotime($first);
                $diff = ($now - $first) < 90 ?: 90;
            }
            else{
                $diff = 90;
            }

            return abs($res[0]->average / $diff);
        }
        else
            return 0;
    }

    public function recent($where = array()){
        $query = "select * 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'];

        return $this->database()->customQuery($query)->result();
    }

}