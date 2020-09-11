<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Userlist extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_email"])){
	      $this->load->view('userlist');
	    }else{
	      $this->load->view('login');
	    }
	}

	public function getAll(){
		$this->load->model('modUser', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res = $this->modUser->getAll($param)->result_array();

        echo json_encode($res);
	}

	public function insert(){
		$this->load->model('modUser', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $param["firstname"] = ucfirst($param["firstname"]);
        $param["lastname"] = ucfirst($param["lastname"]);
        $param["password"] = md5("1234");
        $result = $this->modUser->insert($param);

        echo json_encode($result);
	}

	public function update(){
		$this->load->model('modUser', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $param["firstname"] = ucfirst($param["firstname"]);
        $param["lastname"] = ucfirst($param["lastname"]);
        $result = $this->modUser->update($param);

        echo json_encode($result);
	}

	public function delete(){
	    $param = $this->input->post(NULL, TRUE);
	    $this->load->model('modUser', "", TRUE);

	    $res = $this->modUser->delete($param);
	    echo json_encode($res);
  	}

  	public function checkpassword(){
  		$param = $this->input->post(NULL, TRUE);
	    $this->load->model('modUser', "", TRUE);

	    $p["id"] = $_SESSION["rgc_id"];
	    $res = $this->modUser->getAll($p)->row_array();

	    if($res["password"] != md5($param["password"])){
	    	echo "error";
	    }else{
	    	echo "success";
	    }

  	}

  	public function updatepassword(){
		$this->load->model('modUser', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $param["password"] = md5($param["password"]);
        $param["id"] = $_SESSION["rgc_id"];
        $result = $this->modUser->updatepassword($param);

        echo json_encode($result);
	}
}
