<?php
class Zeapps_invoices extends ZeModel {
    public function get_numerotation(){
        $query = 'SELECT * FROM zeapps_invoices';
        return sizeof($this->database()->customQuery($query)->result()) + 1;
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