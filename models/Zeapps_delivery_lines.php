<?php
class Zeapps_delivery_lines extends ZeModel {
    public function updateOldTable($id_delivery, $sort) {
        $this->database()->query('UPDATE zeapps_delivery_lines SET sort = (sort-1) WHERE id_delivery = ' . $id_delivery . ' AND sort > ' . $sort);
    }

    public function updateNewTable($id_delivery, $sort) {
        $this->database()->query('UPDATE zeapps_delivery_lines SET sort = (sort+1) WHERE id_delivery = ' . $id_delivery . ' AND sort >= ' . $sort);
    }
}