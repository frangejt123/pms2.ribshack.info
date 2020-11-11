<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Productmovement extends CI_Controller {
	public function index()
	{
	    if(isset($_SESSION["rgc_username"])){
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
        $param = $this->input->post(NULL, "true");
        $this->load->model('modProductmovement', "", TRUE);
        $this->load->model('modProduct', "", TRUE);
		$this->load->model('modPeriod', "", TRUE);
		$this->load->model('modKit', "", TRUE);

        $param = $this->input->post(NULL, "true");
		$periodparam["date"] = date('Y-m-d', strtotime($param["periodate"]));
		$periodparam["branch_id"] = $param["branch_id"];
		$periodres = $this->modPeriod->getAll($periodparam)->row_array();

		$raw_kit_composition = $this->modKit->getAll(null)->result_array();
		$kit_composition = array();

		foreach($raw_kit_composition as $ind => $row){
			$pdata = array(
				"parent_id" => $row["parent_id"]
			);
			if(array_key_exists($row["product_id"], $kit_composition)){
				array_push($kit_composition[$row["product_id"]], $pdata);
			}else{
				$kit_composition[$row["product_id"]] = array($pdata);
			}
		}

		if(!isset($periodres["id"])){
			echo "nodata";
			return;
		}

		$pmsparam["period_id"] = $periodres["id"];

        $res = $this->modProductmovement->getAll($pmsparam)->result_array();
        $product = array();

        $parent_product = $this->modKit->getParent(null)->result_array();

        foreach($parent_product as $ind => $row){
            $product[$row["id"]] = array();
            $product[$row["id"]]["child"] = array();
        }

        $data = array();
        foreach($res as $ind => $row){
			$parent_id = null;
			if(array_key_exists($row["product_id"], $kit_composition)) {
				foreach($kit_composition[$row["product_id"]] as $i => $r){
					$parent_id = $r["parent_id"];
					$childrow["product_id"] = $row["product_id"];
					$childrow["pos1"] = $row["pos1"];
					$childrow["pos2"] = $row["pos2"];
					$childrow["pos3"] = $row["pos3"];
					$childrow["pos4"] = $row["pos4"];
					$childrow["pos5"] = $row["pos5"];
					$childrow["pos_total"] = $row["pos_total"];
					$childrow["description"] = $row["description"];
					$childrow["uom"] = $row["uom_abbr"];
					$childrow["pid"] = $parent_id;

					array_push($product[$parent_id]["child"], $childrow);
				}
			}else{
                // $product[$row["product_id"]] = $row;
                $product[$row["product_id"]]["id"] = $row["id"];
                $product[$row["product_id"]]["period_id"] = $row["period_id"];
                $product[$row["product_id"]]["product_id"] = $row["product_id"];
                $product[$row["product_id"]]["pos1"] = $row["pos1"];
				$product[$row["product_id"]]["pos2"] = $row["pos2"];
				$product[$row["product_id"]]["pos3"] = $row["pos3"];
				$product[$row["product_id"]]["pos4"] = $row["pos4"];
				$product[$row["product_id"]]["pos5"] = $row["pos5"];
				$product[$row["product_id"]]["pos_total"] = $row["pos_total"];
                $product[$row["product_id"]]["beginning"] = is_null($row["beginning"]) ? 0 : $row["beginning"];
                $product[$row["product_id"]]["ending"] = is_null($row["ending"]) ? 0 : $row["ending"];
                $product[$row["product_id"]]["delivery"] = $row["delivery"];
                $product[$row["product_id"]]["actual"] = is_null($row["actual"]) ? 0 : $row["actual"];
                $product[$row["product_id"]]["trans_in"] = $row["trans_in"];
                $product[$row["product_id"]]["trans_out"] = $row["trans_out"];
                $product[$row["product_id"]]["return_stock"] = $row["return_stock"];
                $product[$row["product_id"]]["discrepancy"] = is_null($row["discrepancy"]) ? 0 : $row["discrepancy"];
                $product[$row["product_id"]]["description"] = $row["description"];
                $product[$row["product_id"]]["parent_id"] = $parent_id;
                $product[$row["product_id"]]["uom_abbr"] = $row["uom_abbr"];
            }
        }

        $response = array();
		$response["id"] = $periodres["id"];
        $response["sales"] = number_format($periodres["sales"], 2);
        $response["product"] = array_values($product);

        echo json_encode($response);
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
		$file3 = isset($_FILES["csvfile3"]) ? $_FILES["csvfile3"] : null;
		$file4 = isset($_FILES["csvfile4"]) ? $_FILES["csvfile4"] : null;
    	$csv_data = $this->mergeCSV($file0, $file1, $file2, $file3, $file4);//

        $productList = $this->modProduct->getAll($param)->result_array();

        //insert period
        $periodParam = array();
        $periodParam["date"] = date("Y-m-d", strtotime($param["pms_date"]));
        $periodParam["status"] = 0;
		$periodParam["sales"] = $param["sales"];
        $periodParam["branch_id"] = $_SESSION["rgc_access_level"] == 0 ? $param["branch_id"] : $_SESSION["rgc_branch_id"];
        $periodres = $this->modPeriod->insert($periodParam);

        $lastid = $this->modPeriod->getLastID()->row_array();
        $data = array();

        //ACTUAL - POS SOLD = DISCREPANCY
        foreach($productList as $ind => $row) {
        	//print_r($csv_data);
			if (array_key_exists($row["id"], $csv_data)) {
				$csv_data[$row["id"]]["qty"];
			}
			$product_param["product_id"] = $row["id"];
			$product_param["period_id"] = isset($lastid["id"]) ? $lastid["id"] : null;
			$lastactual = $this->modProductmovement->getActual($product_param)->row_array();

			$r["period_id"] = $periodres["id"];
			$r["product_id"] = $row["id"];
			$r["pos1"] = 0;
			$r["pos2"] = 0;
			$r["pos3"] = 0;
			$r["pos4"] = 0;
			$r["pos5"] = 0;
			$r["pos_total"] = 0;
			$r["beginning"] = isset($lastactual["actual"]) ? $lastactual["actual"] : 0;
			$r["ending"] = 0;
			$r["delivery"] = 0;
			$r["actual"] = isset($lastactual["actual"]) ? $lastactual["actual"] : 0;
			$r["trans_in"] = 0;
			$r["trans_out"] = 0;
			$r["return"] = 0;
			$r["discrepancy"] = 0;
			$r["pos_sold"] = 0;
			$r["discrepancy"] = isset($lastactual["actual"]) ? $lastactual["actual"] : 0;

			if (!is_null($file0) || !is_null($file1) || !is_null($file2) || !is_null($file3) || !is_null($file4)){
				if (array_key_exists($row["id"], $csv_data)) {
					$r["pos1"] = isset($csv_data[$row["id"]]["pos1"]) ? $csv_data[$row["id"]]["pos1"] : 0;
					$r["pos2"] = isset($csv_data[$row["id"]]["pos2"]) ? $csv_data[$row["id"]]["pos2"] : 0;
					$r["pos3"] = isset($csv_data[$row["id"]]["pos3"]) ? $csv_data[$row["id"]]["pos3"] : 0;
					$r["pos4"] = isset($csv_data[$row["id"]]["pos4"]) ? $csv_data[$row["id"]]["pos4"] : 0;
					$r["pos5"] = isset($csv_data[$row["id"]]["pos5"]) ? $csv_data[$row["id"]]["pos5"] : 0;
					$r["pos_total"] = $csv_data[$row["id"]]["qty"];
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

    public function checkperiod(){
		$this->load->model('modPeriod', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$param["date"] = date("Y-m-d", strtotime($param["date"]));
		$res = $this->modPeriod->getAll($param)->num_rows();

		print_r($res);
	}

    private function mergeCSV($file0, $file1, $file2, $file3, $file4) {//
        $csv_data0 = !is_null($file0) ? $this->parse_data($file0["tmp_name"]) : null;
        $csv_data1 = !is_null($file1) ? $this->parse_data($file1["tmp_name"]) : null;
		$csv_data2 = !is_null($file2) ? $this->parse_data($file2["tmp_name"]) : null;
		$csv_data3 = !is_null($file3) ? $this->parse_data($file3["tmp_name"]) : null;
		$csv_data4 = !is_null($file4) ? $this->parse_data($file4["tmp_name"]) : null;

        $merge = array();

        if(!is_null($csv_data0)){
            foreach ($csv_data0["inventory"] as $ind => $row) {
                if (!array_key_exists($row["product_code"], $merge)) {
					$row["pos1"] = $row["qty"];
					$merge[$row["product_code"]] = $row;
				}else {
					$merge[$row["product_code"]]["pos1"] = $row["qty"];
                    $merge[$row["product_code"]]["qty"] += $row["qty"];
                }
            }
        }

        if(!is_null($csv_data1)){
            foreach ($csv_data1["inventory"] as $ind => $row) {
                if (!array_key_exists($row["product_code"], $merge)) {
					$row["pos2"] = $row["qty"];
					$merge[$row["product_code"]] = $row;
				}else {
					$merge[$row["product_code"]]["pos2"] = $row["qty"];
                    $merge[$row["product_code"]]["qty"] += $row["qty"];
                }
            }
        }

		if(!is_null($csv_data2)){
			foreach ($csv_data2["inventory"] as $ind => $row) {
				if (!array_key_exists($row["product_code"], $merge)) {
					$row["pos3"] = $row["qty"];
					$merge[$row["product_code"]] = $row;
				}else {
					$merge[$row["product_code"]]["pos3"] = $row["qty"];
					$merge[$row["product_code"]]["qty"] += $row["qty"];
				}
			}
		}

		if(!is_null($csv_data3)){
			foreach ($csv_data3["inventory"] as $ind => $row) {
				if (!array_key_exists($row["product_code"], $merge)) {
					$row["pos4"] = $row["qty"];
					$merge[$row["product_code"]] = $row;
				}else {
					$merge[$row["product_code"]]["pos4"] = $row["qty"];
					$merge[$row["product_code"]]["qty"] += $row["qty"];
				}
			}
		}


		if(!is_null($csv_data4)){
			foreach ($csv_data4["inventory"] as $ind => $row) {
				if (!array_key_exists($row["product_code"], $merge)) {
					$row["pos5"] = $row["qty"];
					$merge[$row["product_code"]] = $row;
				}else {
					$merge[$row["product_code"]]["pos5"] = $row["qty"];
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

                if(isset($csv[0])){
					if ($csv[0] != "doctype") {
						$row_data[$csv[1]]["product_code"] = $csv[1];
						$row_data[$csv[1]]["description"] = $csv[3];
						$row_data[$csv[1]]["uom_code"] = $csv[2];
						$row_data[$csv[1]]["qty"] = $csv[6];
					}
                }
            }

            //array_pop($row_data);
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
		$file3 = isset($_FILES["csvfile3"]) ? $_FILES["csvfile3"] : null;
		$file4 = isset($_FILES["csvfile4"]) ? $_FILES["csvfile4"] : null;
		$csv_data = $this->mergeCSV($file0, $file1, $file2, $file3, $file4);//

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
            if(array_key_exists($row["id"], $csv_data)){

				if (!is_null($file0) || !is_null($file1) || !is_null($file2) || !is_null($file3) || !is_null($file4)){
					if (array_key_exists($row["id"], $csv_data)) {
						$r["id"] = $pmsrow["id"];
						$r["pos1"] = isset($csv_data[$row["id"]]["pos1"]) ? $csv_data[$row["id"]]["pos1"] : 0;
						$r["pos2"] = isset($csv_data[$row["id"]]["pos2"]) ? $csv_data[$row["id"]]["pos2"] : 0;
						$r["pos3"] = isset($csv_data[$row["id"]]["pos3"]) ? $csv_data[$row["id"]]["pos3"] : 0;
						$r["pos4"] = isset($csv_data[$row["id"]]["pos4"]) ? $csv_data[$row["id"]]["pos4"] : 0;
						$r["pos5"] = isset($csv_data[$row["id"]]["pos5"]) ? $csv_data[$row["id"]]["pos5"] : 0;
						$r["pos_total"] = $csv_data[$row["id"]]["qty"];
					}
				}


                if(is_null($row["parent_id"]))
                    $r["discrepancy"] = $actual - abs($r["pos_sold"]);
                else
                    $r["discrepancy"] = 0;
            }

            $data[$row["id"]] = $r;
        }

        $result = array();
        $error = 0;

//         print("<pre>");
//         print_r($data);
//         print("</pre>");
//
//		$del_param["period_id"] = $param["period_id"];
//		$del = $this->modProductmovement->deletebyperiod($del_param);

//		if($del["success"]){
			foreach($data as $ind => $row){
				//$row["period_id"] = $param["period_id"];
				//$res = $this->modProductmovement->insert($row);
				$res = $this->modProductmovement->update($row);
				if(!$res["success"]){
					$error++;
					$result["error_id"] = $res["id"];
				}
			}
//		}else
//			$error = 1;

        if($error == 0) {
			$result["success"] = true;
		}
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
        $discrepancy = $actual - abs($row["pos_total"]);

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

	public function updateSales(){
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modPeriod', "", TRUE);
		$result = $this->modPeriod->update($param);

		print_r(json_encode($result));
	}
}
