<?php
class Zeapps_invoices extends ZeModel {

    public function __construct()
    {
        parent::__construct();

        $this->soft_deletes = TRUE;
    }

    public function get_numerotation($frequency = null){
        if($frequency){
            $query = 'SELECT * FROM zeapps_invoices WHERE';
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
            return sizeof($this->db->query($query)->result()) + 1;
        }
        else{
            return false;
        }
    }
}