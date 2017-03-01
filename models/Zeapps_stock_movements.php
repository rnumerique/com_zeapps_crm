<?php
class Zeapps_stock_movements extends ZeModel{

    public function avg($where = array()){
        $query = "select sum(zeapps_stock_movements.qty) as average 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and qty < 0
                  and ignored = '0'
                  and date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE()
                  and id_stock = " . $where['id_stock'];

        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        if($ret = $this->database()->customQuery($query))
            $res = $ret->result();

        if($res){
            $w = array('deleted_at' => null, 'id_stock' => $where['id_stock']);
            if(isset($where['id_warehouse'])){
                $w['id_warehouse'] = $where['id_warehouse'];
            }
            if($ret = $this->database()->select('date_mvt')->where($w)->table('zeapps_stock_movements')->result()){
                $first = $ret[0]->date_mvt;
                $now = time();
                $first = strtotime($first);
                $diff = (($now - $first) / 60 / 24 ) < 90 ? (($now - $first) / 60 / 24 ) : 90;
            }
            else{
                $diff = 90;
            }

            return abs($res[0]->average / $diff);
        }
        else
            return 0;
    }

    public function last_year($where = array()){
        $query = "select * 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'];
        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        return $this->database()->customQuery($query)->result();
    }

    public function last_months($where = array()){
        $query = "select * 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 90 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'];
        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        return $this->database()->customQuery($query)->result();
    }

    public function last_month($where = array()){
        $query = "select * 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 30 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'];
        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        return $this->database()->customQuery($query)->result();
    }

    public function last_week($where = array()){
        $query = "select * 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 7 DAY) 
                  and deleted_at is null 
                  and id_stock = " . $where['id_stock'];
        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        return $this->database()->customQuery($query)->result();
    }

}