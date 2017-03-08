<?php
class Zeapps_invoices extends ZeModel {
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
            return sizeof($this->database()->customQuery($query)->result()) + 1;
        }
        else{
            return false;
        }
    }

    public function getDueOf($type, $id = null){
        $total = 0;
        $invoices = $this->all(array('id_'.$type => $id, 'due >' => 0));

        if($invoices) {
            foreach ($invoices as $invoice){
                $total += floatval($invoice->due);
            }
        }

        return array('due' => $total, 'due_lines' => $invoices);
    }
}