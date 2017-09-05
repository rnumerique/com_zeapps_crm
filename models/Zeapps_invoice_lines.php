<?php
class Zeapps_invoice_lines extends ZeModel {
    public function updateOldTable($id_invoice, $sort) {
        $this->database()->query('UPDATE zeapps_invoice_lines SET sort = (sort-1) WHERE id_invoice = ' . $id_invoice . ' AND sort > ' . $sort);
    }

    public function updateNewTable($id_invoice, $sort) {
        $this->database()->query('UPDATE zeapps_invoice_lines SET sort = (sort+1) WHERE id_invoice = ' . $id_invoice . ' AND sort >= ' . $sort);
    }

    public function getByMonth($year = 0, $month = 0, $id_product = 0){
        $query = "SELECT * FROM zeapps_invoice_lines 
                  LEFT JOIN zeapps_invoices ON zeapps_invoices.id = zeapps_invoice_lines.id_invoice
                  WHERE YEAR(zeapps_invoices.date_limit) = ".$year." AND MONTH(zeapps_invoices.date_limit) = ".$month." 
                  AND zeapps_invoice_lines.type = 'product' AND zeapps_invoice_lines.id_product = ".$id_product."
                  AND zeapps_invoices.finalized = 1 AND zeapps_invoice_lines.deleted_at IS NULL";

        return $this->database()->customQuery($query)->result();
    }
}