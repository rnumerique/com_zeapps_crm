<?php
class Zeapps_invoice_lines extends ZeModel {

    public function __construct()
    {
        parent::__construct();

        $this->soft_deletes = TRUE;
    }

    public function updateOldTable($id_invoice, $sort) {
        $this->db->query('UPDATE zeapps_invoice_lines SET sort = (sort-1) WHERE id_invoice = ' . $id_invoice . ' AND sort > ' . $sort);
    }

    public function updateNewTable($id_invoice, $sort) {
        $this->db->query('UPDATE zeapps_invoice_lines SET sort = (sort+1) WHERE id_invoice = ' . $id_invoice . ' AND sort >= ' . $sort);
    }
}