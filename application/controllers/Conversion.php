<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Conversion extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_email"])){
			$this->load->view('conversion');
		}else{
			$this->load->view('login');
		}
	}

	public function getAll(){
		$this->load->model('modConversion', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res = $this->modConversion->getAll($param)->result_array();

		echo json_encode($res);
	}

	public function insert(){
		$this->load->model('modConversion', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modConversion->insert($param);

		echo json_encode($result);
	}

	public function update(){
		$this->load->model('modConversion', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modConversion->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modConversion', "", TRUE);

		$res = $this->modConversion->delete($param);
		echo json_encode($res);
	}
}
