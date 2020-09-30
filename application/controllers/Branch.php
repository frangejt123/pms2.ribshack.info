<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Branch extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_email"])){
			$this->load->view('branch');
		}else{
			$this->load->view('login');
		}
	}

	public function getAll(){
		$this->load->model('modBranch', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $res = $this->modBranch->getAll($param)->result_array();
        echo json_encode($res);
	}

	public function insert(){
		$this->load->model('modBranch', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modBranch->insert($param);

		echo json_encode($result);
	}

	function update(){
		$this->load->model('modBranch', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modBranch->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$this->load->model('modBranch', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$res = $this->modBranch->delete($param);
		echo json_encode($res);
	}

	public function checkcode(){
	$param = $this->input->post(NULL, TRUE);
	$this->load->model('modBranch', "", TRUE);
	$param["branchcode"] = ucwords($param["branch_code"]);

	$res = $this->modBranch->checkcode($param)->num_rows();
	echo $res;
}
}
