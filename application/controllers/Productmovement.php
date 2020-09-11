<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Productmovement extends CI_Controller {
	public function index()
	{
	    if(isset($_SESSION["rgc_email"])){
            $data["poscount"] = $_SESSION["rgc_poscount"];
	        $this->load->view('productmovement', $data);
	    }else{
	      $this->load->view('login');
	    }
	}

    public function getperiod(){
        $this->load->model('modPeriod', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $param["branch_id"] = $_SESSION["rgc_access_level"] == 0 ? $param["branch_id"] : $_SESSION["rgc_branch_id"];
        $res = $this->modPeriod->getAll($param)->result_array();
        foreach($res as $ind => $row){
            $res[$ind]["date"] = date("M d, Y", strtotime($row["date"]));
        }

        print_r(json_encode($res));
    }

    public function getproductmovement(){
        $this->load->model('modProductmovement', "", TRUE);
        $this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $res = $this->modProductmovement->getAll($param)->result_array();
        $parent_product = $this->modProduct->getParent(null)->result_array();

        $product = array();
        foreach($parent_product as $ind => $row){
            $product[$row["id"]] = array();
            $product[$row["id"]]["child"] = array();
        }

        $data = array();
        foreach($res as $ind => $row){
            if(!is_null($row["parent_id"])){
                $childrow["product_id"] = $row["product_id"];
                $childrow["pos_sold"] = $row["pos_sold"];
                $childrow["description"] = $row["description"];
                $childrow["uom"] = $row["uom_abbr"];
                $childrow["pid"] = $row["parent_id"];

                array_push($product[$row["parent_id"]]["child"], $childrow);
            }else{
                // $product[$row["product_id"]] = $row;
                $product[$row["product_id"]]["id"] = $row["id"];
                $product[$row["product_id"]]["period_id"] = $row["period_id"];
                $product[$row["product_id"]]["product_id"] = $row["product_id"];
                $product[$row["product_id"]]["pos_sold"] = $row["pos_sold"];
                $product[$row["product_id"]]["beginning"] = is_null($row["beginning"]) ? 0 : $row["beginning"];
                $product[$row["product_id"]]["ending"] = is_null($row["ending"]) ? 0 : $row["ending"];
                $product[$row["product_id"]]["delivery"] = $row["delivery"];
                $product[$row["product_id"]]["actual"] = is_null($row["actual"]) ? 0 : $row["actual"];
                $product[$row["product_id"]]["trans_in"] = $row["trans_in"];
                $product[$row["product_id"]]["trans_out"] = $row["trans_out"];
                $product[$row["product_id"]]["return_stock"] = $row["return_stock"];
                $product[$row["product_id"]]["discrepancy"] = is_null($row["discrepancy"]) ? 0 : $row["discrepancy"];
                $product[$row["product_id"]]["description"] = $row["description"];
                $product[$row["product_id"]]["parent_id"] = $row["parent_id"];
                $product[$row["product_id"]]["uom_abbr"] = $row["uom_abbr"];
            }
        }

        echo json_encode(array_values($product));
    }

	public function mergeData() {
        $this->load->model('modProduct', "", TRUE);
        $this->load->model('modProductmovement', "", TRUE);
        $this->load->model('modPeriod', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $post_data = array();

        $file0 = isset($_FILES["csvfile0"]) ? $_FILES["csvfile0"] : null;
        $file1 = isset($_FILES["csvfile1"]) ? $_FILES["csvfile1"] : null;
        $file2 = isset($_FILES["csvfile2"]) ? $_FILES["csvfile2"] : null;
    	$csv_data = $this->mergeCSV($file0, $file1, $file2);//

        $productList = $this->modProduct->getAll($param)->result_array();

        //insert period
        $periodParam = array();
        $periodParam["date"] = date("Y-m-d", strtotime($param["pms_date"]));
        $periodParam["status"] = 0;
        $periodParam["branch_id"] = $_SESSION["rgc_access_level"] == 0 ? $param["branch_id"] : $_SESSION["rgc_branch_id"];
        $periodres = $this->modPeriod->insert($periodParam);

        $lastid = $this->modPeriod->getLastID()->row_array();
        $data = array();

        //ACTUAL - POS SOLD = DISCREPANCY
        foreach($productList as $ind => $row) {
			$product_param["product_id"] = $row["id"];
			$product_param["period_id"] = $lastid["id"];
			$lastactual = $this->modProductmovement->getActual($product_param)->row_array();

			$r["period_id"] = $periodres["id"];
			$r["product_id"] = $row["id"];
			$r["pos_sold"] = 0;
			$r["beginning"] = $lastactual["actual"];
			$r["ending"] = 0;
			$r["delivery"] = 0;
			$r["actual"] = $lastactual["actual"];;
			$r["trans_in"] = 0;
			$r["trans_out"] = 0;
			$r["return"] = 0;
			$r["discrepancy"] = 0;
			$r["pos_sold"] = 0;
			$r["discrepancy"] = $lastactual["actual"];

			if (!is_null($file0) || !is_null($file1) || !is_null($file2)){
				if (array_key_exists($row["id"], $csv_data)) {
					$r["pos_sold"] = $csv_data[$row["id"]]["qty"];
					$r["discrepancy"] = $r["beginning"] - floatval($r["pos_sold"]);
				}
			}

            $data[$row["id"]] = $r;
        }

        $result = array();
        $error = 0;

        foreach($data as $ind => $row){
            $res = $this->modProductmovement->insert($row);
            if(!$res["success"]){
                $error++;
                $result["error_id"] = $res["id"];
            }
        }

        if($error == 0){
            $result["success"] = true;
            $result["period_id"] = $periodres["id"];
            $result["period_date"] = date("M d, Y", strtotime($param["pms_date"]));
        }
        echo json_encode($result);

    }

    private function mergeCSV($file0, $file1) {//
        $csv_data0 = !is_null($file0) ? $this->parse_data($file0["tmp_name"]) : null;
        $csv_data1 = !is_null($file1) ? $this->parse_data($file1["tmp_name"]) : null;

        $merge = array();
        if(!is_null($csv_data0)){
            foreach ($csv_data0["inventory"] as $ind => $row) {
                if (!array_key_exists($row["product_code"], $merge))
                    $merge[$row["product_code"]] = $row;
                else {
                    $merge[$row["product_code"]]["qty"] += $row["qty"];
                }
            }
        }

        if(!is_null($csv_data1)){
            foreach ($csv_data1["inventory"] as $ind => $row) {
                if (!array_key_exists($row["product_code"], $merge))
                    $merge[$row["product_code"]] = $row;
                else {
                    $merge[$row["product_code"]]["qty"] += $row["qty"];
                }
            }
        }

        $res = array();
        foreach ($merge as $ind => $row) {
            $res[$row["product_code"]] = $row;
        }

        return $res;
    }

	public function parse_data($path){
        if(!is_null($path)){
            $file = fopen($path, "r");
            $row_data = array();

            while (!feof($file)) {
                $csv = fgetcsv($file);

                if(isset($csv[0]))
					if ($csv[0] != "doctype") {
						$row_data[$csv[1]]["product_code"] = $csv[1];
						$row_data[$csv[1]]["description"] = $csv[3];
						$row_data[$csv[1]]["uom_code"] = $csv[2];
						$row_data[$csv[1]]["qty"] = $csv[6];
					}
            }
            array_pop($row_data);

            fclose($file);
            $data = array(
                "inventory" => $row_data,
            );
            return $data;
        }
	}

    public function uploadUpdate() {   
        $this->load->model('modProduct', "", TRUE);
        $this->load->model('modProductmovement', "", TRUE);
        $this->load->model('modPeriod', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $post_data = array();

        $file0 = isset($_FILES["csvfile0"]) ? $_FILES["csvfile0"] : null;
        $file1 = isset($_FILES["csvfile1"]) ? $_FILES["csvfile1"] : null;
        $file2 = isset($_FILES["csvfile2"]) ? $_FILES["csvfile2"] : null;
        $csv_data = $this->mergeCSV($file0, $file1, $file2);//
 
        $productList = $this->modProduct->getAll($param)->result_array();

        $data = array();
        //ACTUAL - POS SOLD = DISCREPANCY
        
        foreach($productList as $ind => $row){
            $p["period_id"] = $param["period_id"];
            $p["product_id"] = $row["id"];
            $pmsrow = $this->modProductmovement->getAll($p)->row_array();

            $in = (floatval($pmsrow["beginning"]) + floatval($pmsrow["delivery"]) + floatval($pmsrow["trans_in"]));
            $out =  (floatval($pmsrow["ending"]) + floatval($pmsrow["return_stock"]) + floatval($pmsrow["trans_out"]));
            $actual = $in - $out;

            $r["pos_sold"] = 0;
            $r["discrepancy"] = 0;
            $r["id"] = $pmsrow["id"];
            if(array_key_exists($row["id"], $csv_data)){
                $r["pos_sold"] = $csv_data[$row["id"]]["qty"];
                if(is_null($row["parent_id"]))
                    $r["discrepancy"] = $actual - abs($r["pos_sold"]);
                else
                    $r["discrepancy"] = 0;
            }

            $data[$row["id"]] = $r;
        }

        $result = array();
        $error = 0;

        // print("<pre>");
        // print_r($data);
        // print("</pre>");

        foreach($data as $ind => $row){
            $res = $this->modProductmovement->update($row);
            if(!$res["success"]){
                $error++;
                $result["error_id"] = $res["id"];
            }
        }

        if($error == 0)
            $result["success"] = true;
        echo json_encode($result);

    }

    public function update(){
        $this->load->model('modProductmovement', "", TRUE);
        $param = $this->input->post(NULL, "true");
        $result = $this->modProductmovement->update($param);

        //update discrepancy
        $p["id"] = $param["id"];
        $row = $this->modProductmovement->getAll($p)->row_array();
        $in = (floatval($row["beginning"]) + floatval($row["delivery"]) + floatval($row["trans_in"]));
        $out =  (floatval($row["ending"]) + floatval($row["return_stock"]) + floatval($row["trans_out"]));
        $actual = $in - $out;
        $discrepancy = $actual - abs($row["pos_sold"]);

        $p["discrepancy"] = $discrepancy;
        $p["actual"] = $actual;
        $res = $this->modProductmovement->update($p);

        $result["discrepancy"] = $p["discrepancy"];
        $result["actual"] = $actual;
        echo json_encode($result);
    }

    public function deletePeriod(){
        $param = $this->input->post(NULL, TRUE);
        $this->load->model('modPeriod', "", TRUE);
        $result = $this->modPeriod->delete($param);

        print_r(json_encode($result));
    }

    public function completePeriod(){
        $param = $this->input->post(NULL, TRUE);
        $param["status"] = 1;
        $this->load->model('modPeriod', "", TRUE);
        $result = $this->modPeriod->update($param);

        print_r(json_encode($result));
    }

    public function report_productmovement(){
        $this->load->library('Pdf');
        $this->load->model('modProductmovement', "", TRUE);
        $this->load->model('modProduct', "", TRUE);
        $this->load->model('modBranch', "", TRUE);
        $this->load->model('modPeriod', "", TRUE);
        $param = $this->input->get(NULL, "true");
        $param["period_id"] = $param["q"];
        $res = $this->modProductmovement->getAll($param)->result_array();
        $parent_product = $this->modProduct->getParent(null)->result_array();
        $periodparam["id"] = $param["q"];
        $periodData = $this->modPeriod->getAll($periodparam)->row_array();
        $branchParam["id"] = $periodData["branch_id"];
        $branchData = $this->modBranch->getAll($branchParam)->row_array();

        $period_date = $periodData["date"];

        $product = array();
        foreach($parent_product as $ind => $row){
            $product[$row["id"]] = array();
            $product[$row["id"]]["child"] = array();
        }

        $data = array();
        foreach($res as $ind => $row){
            if(!is_null($row["parent_id"])){
                $childrow["product_id"] = $row["product_id"];
                $childrow["pos_sold"] = $row["pos_sold"];
                $childrow["description"] = $row["description"];
                $childrow["uom"] = $row["uom_abbr"];
                $childrow["pid"] = $row["parent_id"];

                array_push($product[$row["parent_id"]]["child"], $childrow);
            }else{
                // $product[$row["product_id"]] = $row;
                $product[$row["product_id"]]["id"] = $row["id"];
                $product[$row["product_id"]]["period_id"] = $row["period_id"];
                $product[$row["product_id"]]["product_id"] = $row["product_id"];
                $product[$row["product_id"]]["pos_sold"] = $row["pos_sold"];
                $product[$row["product_id"]]["beginning"] = is_null($row["beginning"]) ? 0 : $row["beginning"];
                $product[$row["product_id"]]["ending"] = is_null($row["ending"]) ? 0 : $row["ending"];
                $product[$row["product_id"]]["delivery"] = $row["delivery"];
                $product[$row["product_id"]]["actual"] = is_null($row["actual"]) ? 0 : $row["actual"];
                $product[$row["product_id"]]["trans_in"] = $row["trans_in"];
                $product[$row["product_id"]]["trans_out"] = $row["trans_out"];
                $product[$row["product_id"]]["return_stock"] = $row["return_stock"];
                $product[$row["product_id"]]["discrepancy"] = is_null($row["discrepancy"]) ? 0 : $row["discrepancy"];
                $product[$row["product_id"]]["description"] = $row["description"];
                $product[$row["product_id"]]["parent_id"] = $row["parent_id"];
                $product[$row["product_id"]]["uom_abbr"] = $row["uom_abbr"];
            }
        }

        $d["report_data"] = $product;
        $d["period_date"] = date("F d, Y", strtotime($period_date));
        $d["address"] = $branchData["address"];
        $d["operated_by"] = $branchData["operated_by"];

        $this->load->view('report/product_movement_report', $d);
    }
}
