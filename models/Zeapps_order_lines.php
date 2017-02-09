<?php
class Zeapps_order_lines extends ZeModel {

    public function __construct()
    {
        parent::__construct();

        $this->soft_deletes = TRUE;
    }

    public function updateOldTable($id_order, $sort) {
        $this->db->query('UPDATE zeapps_order_lines SET sort = (sort-1) WHERE id_order = ' . $id_order . ' AND sort > ' . $sort);
    }

    public function updateNewTable($id_order, $sort) {
        $this->db->query('UPDATE zeapps_order_lines SET sort = (sort+1) WHERE id_order = ' . $id_order . ' AND sort >= ' . $sort);
    }
}