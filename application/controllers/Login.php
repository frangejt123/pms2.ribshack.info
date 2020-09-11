<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

        public function index(){

        $this->load->model('modLogin', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $param["password"] = md5($param["password"]);

        $res = $this->modLogin->getAll($param)->row_array();
        $rescount = $this->modLogin->getAll($param)->num_rows();
        $response["success"] = false;
        if($rescount > 0){
        	session_start();
        	$_SESSION["rgc_id"] = $res["id"];
        	$_SESSION["rgc_email"] = $res["email"];
        	$_SESSION["rgc_firstname"] = $res["firstname"];
        	$_SESSION["rgc_lastname"] = $res["lastname"];
                $_SESSION["rgc_poscount"] = $res["poscount"];
                $_SESSION["rgc_branch_id"] = $res["branch_id"];
                $_SESSION["rgc_access_level"] = $res["access_level"];
        	$response["success"] = true;
        }

        echo json_encode($response);
        }
}
