<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Uom extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_username"])){
	      $this->load->view('measurement');
	    }else{
	      $this->load->view('login');
	    }
	}

	public function getAll(){
		$this->load->model('modUom', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res = $this->modUom->getAll($param)->result_array();

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
		$this->load->model('modUom', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $result = $this->modUom->insert($param);

        echo json_encode($result);
	}

	public function update(){
		$this->load->model('modUom', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $result = $this->modUom->update($param);

        echo json_encode($result);
	}

	public function delete(){
	    $param = $this->input->post(NULL, TRUE);
	    $this->load->model('modUom', "", TRUE);

	    $res = $this->modUom->delete($param);
	    echo json_encode($res);
  	}
}
