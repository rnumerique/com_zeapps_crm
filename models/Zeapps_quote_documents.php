<?php
class Zeapps_quote_documents extends ZeModel {

    public function insert($objData = null){
        $this->_pLoad->model('Zeapps_users', 'users');

        if($user = $this->_pLoad->ctrl->users->getUserByToken($this->_pLoad->ctrl->session->get('token'))){
            $objData['id_user'] = $user->id;
            $objData['name_user'] = $user->firstname[0] . '. ' . $user->lastname;
        }

        $objData['date'] = date('Y-m-d H:i:s');

        return parent::insert($objData);
    }

    public function update($objData = null, $where = null){
        $objData['date'] = date('Y-m-d H:i:s');

        return parent::update($objData, $where);
    }

}