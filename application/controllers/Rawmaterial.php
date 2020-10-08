<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Rawmaterial extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_username"])){
			$this->load->view('rawmaterial');
		}else{
			$this->load->view('login');
		}
	}

	public function getAll(){
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res = $this->modRawmaterial->getAll($param)->result_array();

		// foreach($res as $ind => $row){
		// 	$res[$ind]["parent_description"] = "";
		// 	if(!is_null($row["parent_id"])){
		// 		$parent_id["id"] = $row["parent_id"];
		// 		$desc = $this->modUom->getAll($parent_id)->row_array();
		// 		$res[$ind]["parent_description"] = $desc["description"];
		// 	}else{
		// 		$res[$ind]["parent_id"] = "";
		// 	}
		// }

		echo json_encode($res);
	}

	public function insert(){
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modRawmaterial->insert($param);

		echo json_encode($result);
	}

	public function update(){
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modRawmaterial->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modRawmaterial', "", TRUE);

		$res = $this->modRawmaterial->delete($param);
		echo json_encode($res);
	}

	public function checkcode(){
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modRawmaterial', "", TRUE);
		$param["itemcode"] = ucwords($param["itemcode"]);

		$res = $this->modRawmaterial->checkcode($param)->num_rows();
		echo $res;
	}
}
