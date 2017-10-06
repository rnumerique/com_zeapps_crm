<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class com_zeapps_crm_get_context extends ZeCtrl
{
    public function execute($data = array()){
        $this->load->model('Zeapps_modalities', 'modalities');
        $this->load->model('Zeapps_taxes', 'taxes');

        $return = [];

        if($return['modalities'] = $this->modalities->all()){
            foreach($return['modalities'] as $modality){
                $modality->sort = intval($modality->sort);
                $modality->settlement_date = intval($modality->settlement_date);
                $modality->settlement_delay = intval($modality->settlement_delay);
                $modality->sort = intval($modality->sort);
            }
        }
        else{
            $return['modalities'] = [];
        }

        if($return['taxes'] = $this->taxes->all()){
            foreach($return['taxes'] as $tax){
                $tax->value = floatval($tax->value);
            }
        }
        else{
            $return['taxes'] = [];
        }

        return $return;
    }
}
