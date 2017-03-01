<?php
class Zeapps_deliveries extends ZeModel {
    public function get_numerotation($frequency = null){
        if($frequency){
            $query = 'SELECT * FROM zeapps_deliveries WHERE';
            switch ($frequency){
                case 'week':
                    $query .= ' week(created_at) = '.date('W').' AND';
                case 'month':
                    $query .= ' month(created_at) = '.date('m').' AND';
                case 'year':
                    $query .= ' year(created_at) = '.date('Y').' AND';
                case 'lifetime':
                    $query .= ' 1';
                    break;
                default:
                    $query .= ' 0';
            }
            if($ret = $this->database()->customQuery($query)->result())
                return sizeof($ret) + 1;
            else{
                return 1;
            }
        }
        else{
            return false;
        }
    }
}