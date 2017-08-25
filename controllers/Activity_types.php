<?php

/**
 * Created by PhpStorm.
 * User: developpeur
 * Date: 16/12/2016
 * Time: 10:09
 */
class Activity_types extends ZeCtrl
{
    public function all(){
        $this->load->model("Zeapps_crm_activities", "activity_types");

        $activity_types = $this->activity_types->all();

        echo json_encode(array("activity_types" => $activity_types));
    }
}