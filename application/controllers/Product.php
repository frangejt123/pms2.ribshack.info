<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {
	public function index()
	{
		
		if(isset($_SESSION["rgc_username"])){
           $data["poscount"] = $_SESSION["rgc_poscount"];
	      $this->load->view('product');
	    }else{
	      $this->load->view('login');
	    }
	}

	public function getAll(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res = $this->modProduct->getAll($param)->result_array();

        echo json_encode($res);
	}

	public function getParent(){
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res["product"] = $this->modKit->getParent($param)->result_array();
        //$res["uom"] = $this->modUom->getAll($param)->result_array();
        //if(isset($param["product_id"]))
        	//$res["child"] = $this->modProduct->getChild($param)->num_rows();

        echo json_encode($res);
	}

	public function checkProductExists(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $count = $this->modProduct->getAll($param)->num_rows();
        echo $count;
	}

	public function saveProduct(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

		$result = $this->modProduct->insert($param);

        if(isset($param["product_kit"]))
			if(count($param["product_kit"]) > 0){
				foreach($param["product_kit"] as $ind => $row){
					$row["parent_id"] = $result["id"];
					$this->modKit->insert($row);
				}
			}

        echo json_encode($result);
	}

	public function updateProduct(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

		if(isset($param["product_kit"]))
			if(count($param["product_kit"]) > 0){
				foreach($param["product_kit"] as $ind => $row){
					if($row["mode"] == "new"){
						unset($row["id"]);
						$this->modKit->insert($row);
					}
					if($row["mode"] == "edited"){
						$this->modKit->update($row);
					}
					if($row["mode"] == "deleted"){
						$this->modKit->delete($row);
					}
				}
			}

        $result = $this->modProduct->update($param);

        echo json_encode($result);
	}

	public function getKit(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$result = $this->modKit->getAll($param)->result_array();
		echo json_encode($result);
	}

	public function delete(){
	    $param = $this->input->post(NULL, TRUE);
	    $this->load->model('modProduct', "", TRUE);

	    $res = $this->modProduct->delete($param);
	    echo json_encode($res);
  	}
}
