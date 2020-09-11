<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Branch extends CI_Controller {
	public function index()
	{
		$this->load->view('Branch');
	}

	public function getAll(){
		$this->load->model('modBranch', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $res = $this->modBranch->getAll($param)->result_array();
        echo json_encode($res);
	}
}
