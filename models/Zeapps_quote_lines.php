<?php
class Zeapps_quote_lines extends ZeModel {

    public function updateOldTable($id_quote, $sort) {
        $this->database()->query('UPDATE zeapps_quote_lines SET sort = (sort-1) WHERE id_quote = ' . $id_quote . ' AND sort > ' . $sort);
    }

    public function updateNewTable($id_quote, $sort) {
        $this->database()->query('UPDATE zeapps_quote_lines SET sort = (sort+1) WHERE id_quote = ' . $id_quote . ' AND sort >= ' . $sort);
    }
}