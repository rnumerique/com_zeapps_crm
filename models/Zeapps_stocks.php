<?php
class Zeapps_stocks extends ZeModel {
    public function all($where = array(), $limit = 2147483647, $offset = 0){

        $query = "select  s.id_stock as id_stock,
                          s.ref as ref,
                          s.label as label,
                          s.value_ht as value_ht,
                          s.resupply_delay as resupply_delay,
                          s.resupply_unit as resupply_unit,
                          s.total as total,
                          avg(m.qty) as average 
                  from zeapps_stocks s
                  left join zeapps_stock_movements m 
                  on s.id_stock = m.id_stock
                      and m.date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE() + INTERVAL 1 DAY
                      and m.qty < 0
                      and m.ignored = '0'
                      and m.deleted_at is null 
                  where s.deleted_at is null";

        if(isset($where['id_warehouse'])){
            $query .= " and s.id_warehouse = ".$where['id_warehouse'];
        }

        $query .= " group by s.id_stock order by label limit ".$limit." offset ".$offset;

        /* TODO : sql query w/o using the view to improve performances. Crash when resquesting a warehouse != 1
        $query = "select    s.id as id_stock,
                            s.ref as ref,
                            s.label as label,
                            s.value_ht as value_ht,
                            w.resupply_delay as resupply_delay,
                            w.resupply_unit as resupply_unit,
                            sum(m.qty) as total,
                            avg(if((m.date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE() + INTERVAL 1 DAY) and (m.qty < 0) , m.qty, null)) as average
                    from zeapps_product_stocks s
                    left join zeapps_stock_movements m
                            on  s.id = m.id_stock
                            and m.ignored = '0'";

        if(isset($where['id_warehouse'])){
            $query .= " and m.id_warehouse = ".$where['id_warehouse'];
        }

        $query .= " and m.deleted_at is null
                    left join zeapps_warehouses w";

        if(isset($where['id_warehouse'])){
            $query .= " on w.id = ".$where['id_warehouse'];
        }
        else{
            $query .= " on w.id = m.id_warehouse";
        }

        $query .= " and w.deleted_at is null
                    where   s.deleted_at is null
                    group by s.id, m.id_warehouse
                    order by label
                    limit ".$limit." offset ".$offset;
         */

        if($results = parent::query($query)) {
            foreach ($results as $res) {
                if(floatval($res->average) < 0) {
                    $w = array('deleted_at' => null, 'id_stock' => $where['id_stock'], 'qty <' => 0);
                    if (isset($where['id_warehouse'])) {
                        $w['id_warehouse'] = $where['id_warehouse'];
                    }
                    if ($ret = $this->database()->select('date_mvt')->where($w)->table('zeapps_stock_movements')->limit(1)->order_by('date_mvt', 'ASC')->result()) {
                        $first = $ret[0]->date_mvt;
                        $now = time();
                        $first = strtotime($first);
                        $diff = (($now - $first) / 86400) < 90 ? (($now - $first) / 86400) : 90; // 86400 = 60*60*24
                        $diff = $diff < 1 ? 1 : $diff;
                    } else {
                        $diff = 90;
                    }

                    $res->avg = abs($res->average / $diff);
                }
                else{
                    $res->avg = 0;
                }
            }
        }

        return $results;
    }
}