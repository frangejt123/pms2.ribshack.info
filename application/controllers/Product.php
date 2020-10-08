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

		foreach($res as $ind => $row){
			$res[$ind]["parent_description"] = "";
			if(!is_null($row["parent_id"])){
				$parent_id["id"] = $row["parent_id"];
				$desc = $this->modProduct->getAll($parent_id)->row_array();
				$res[$ind]["parent_description"] = $desc["description"];
			}else{
				$res[$ind]["parent_id"] = "";
			}
		}

        echo json_encode($res);
	}

	public function getParent(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modUom', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res["product"] = $this->modProduct->getParent($param)->result_array();
        $res["uom"] = $this->modUom->getAll($param)->result_array();
        if(isset($param["product_id"]))
        	$res["child"] = $this->modProduct->getChild($param)->num_rows();
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
        $param = $this->input->post(NULL, "true");
        $result = $this->modProduct->insert($param);

        echo json_encode($result);
	}

	public function updateProduct(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $result = $this->modProduct->update($param);

        echo json_encode($result);
	}

	public function delete(){
	    $param = $this->input->post(NULL, TRUE);
	    $this->load->model('modProduct', "", TRUE);

	    $res = $this->modProduct->delete($param);
	    echo json_encode($res);
  	}
}
