<?php
class Zeapps_stock_movements extends ZeModel{

    public function write($data = array()){
        if($mvt = parent::get(array(
            "id_table" => $data['id_table'],
            "name_table" => $data['name_table'],
            "id_stock" => $data['id_stock'],
        ))){
            return parent::update($data, $mvt->id);
        }
        else{
            return parent::insert($data);
        }
    }

    public function avg($where = array()){
        $query = "select sum(zeapps_stock_movements.qty) as average 
                  from zeapps_stock_movements 
                  where date_mvt > date_sub(CURDATE(),INTERVAL 12 MONTH) 
                  and deleted_at is null 
                  and qty < 0
                  and ignored = '0'
                  and date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE() + INTERVAL 1 DAY
                  and id_stock = " . $where['id_stock'];

        if(isset($where['id_warehouse'])){
            $query .= ' and id_warehouse = '.$where['id_warehouse'];
        }

        if($ret = $this->database()->customQuery($query))
            $res = $ret->result();

        if($res){
            $w = array('deleted_at' => null, 'id_stock' => $where['id_stock'], 'qty <' => 0);
            if(isset($where['id_warehouse'])){
                $w['id_warehouse'] = $where['id_warehouse'];
            }
            if($ret = $this->database()->select('date_mvt')->where($w)->table('zeapps_stock_movements')->result()){
                $first = $ret[0]->date_mvt;
                $now = time();
                $first = strtotime($first);
                $diff = (($now - $first) / 86400 ) < 90 ? (($now - $first) / 86400 ) : 90; // 86400 = 60*60*24
                $diff = $diff < 1 ? 1 : $diff;
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