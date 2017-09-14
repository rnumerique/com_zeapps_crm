<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm_commons extends ZeCtrl
{
    public function transform_modal()
    {
        $this->load->view('commons/transform_modal');
    }
    public function transformed_modal()
    {
        $this->load->view('commons/transformed_modal');
    }
    public function form_comment()
    {
        $this->load->view('commons/form_comment');
    }
    public function form_document()
    {
        $this->load->view('commons/form_document');
    }
    public function form_activity()
    {
        $this->load->view('commons/form_activity');
    }
}