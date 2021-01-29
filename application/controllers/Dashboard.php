<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_username"])){
			$data["poscount"] = $_SESSION["rgc_poscount"];
			$this->load->view('dashboard', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function getAll(){
		$this->load->model('modConversion', "", TRUE);
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$draw = $param['draw'];
		$param["datefrom"] = $param["startDate"];
		$param["dateto"] = $param["endDate"];
		$param["datemerge"] = true;

		$convertion = $this->modConversion->getAll($param)->result_array();
		$pms = $this->modProductMovement->getTotal($param)->result_array();
		$totalRecords = $this->modConversion->getAll($param)->num_rows();
		$totalRecordwithFilter = $totalRecords;

		$period = new DatePeriod(
			new DateTime($param["datefrom"]),
			new DateInterval('P1D'),
			new DateTime($param["dateto"]. ' 23:59:59')
		);

		$datecount = 0;
		foreach ($period as $date) {
			$datecount++;
		}

		$pmstotal = [];

		foreach($pms as $ind => $row){
			$pmstotal[$row["product_id"]] = $row["pos_total"];
		}

		$data = [];

		if(count($pms) > 0)
			foreach($convertion as $ind => $row){
				$week_total = $row["conversion"] * $pmstotal[$row["product_code"]];
				$week_avg =  $week_total / $datecount;

				if(array_key_exists($row['raw_material_id'], $data)){
					$data[$row["raw_material_id"]]["week_total"] += $week_total;
					$data[$row["raw_material_id"]]["week_avg"] += $week_avg;
				}else{
					$data[$row['raw_material_id']] = [];
					$data[$row['raw_material_id']]["raw_material"] = $row["raw_material"];
					$data[$row["raw_material_id"]]["uom_abbr"] = $row["uom_abbr"];
					$data[$row["raw_material_id"]]["week_total"] = $week_total;
					$data[$row["raw_material_id"]]["week_avg"] = $week_avg;
				}
			}

		$aadata = [];
		foreach($data as $ind => $row){
			$row["week_total"] = number_format($row["week_total"], 2);
			$row["week_avg"] = number_format($row["week_avg"], 2);
			array_push($aadata, $row);
		}

		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $aadata
		);


		echo json_encode($response);

	}

	public function productContribution(){
		$this->load->model('modConversion', "", TRUE);
		$this->load->model('modPeriod', "", TRUE);
		$this->load->model('modProductMovement', "", TRUE);
		$this->load->model('modKit', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$param["datefrom"] = $param["date"][0];
		$param["dateto"] = $param["date"][6];

//		$param["datefrom"] = "2021-01-01";
//		$param["dateto"] = "2021-01-07";

		//$convertion = $this->modConversion->getAll($param)->result_array();
		$pms = $this->modProductMovement->getTotal($param)->result_array();
		$perioddata = $this->modPeriod->getSales($param)->result_array();

		$period = new DatePeriod(
			new DateTime($param["datefrom"]),
			new DateInterval('P1D'),
			new DateTime($param["dateto"]. ' 23:59:59')
		);

		$header = [];

		$totalRecords = $this->modConversion->getAll($param)->num_rows();
		$totalRecordwithFilter = $totalRecords;

		$raw_kit_composition = $this->modKit->getAll(null)->result_array();
		$kit_composition = array();

		foreach($raw_kit_composition as $ind => $row){
//			$kit_composition[$row["product_id"]]["parent_id"] = $row["parent_id"];
//			$kit_composition[$row["product_id"]]["cq"] = $row["quantity"];
			$pdata = array(
				"parent_id" => $row["parent_id"],
				"parent_desc" => $row["description"],
				"cq" => $row["quantity"]
			);
			if(array_key_exists($row["product_id"], $kit_composition)){
				array_push($kit_composition[$row["product_id"]], $pdata);
			}else{
				$kit_composition[$row["product_id"]] = array($pdata);
			}
		}

		$datedataarray = [];

		$childsum = [];
		$children = [];

		$datecount = 0;
		foreach ($period as $date) {
			$datecount++;
		}

		$sortedperiod = [];
		foreach($perioddata as $ind => $row){
			$dateformat = date('Ymd', strtotime($row["date"]));
			$sortedperiod[$dateformat] = $row["sales"];
		}

		foreach($pms as $ind => $row){
			$datedataarray[$row["product_id"]]['desc'] = $row["description"];

			$parent_id = null;
			$dateformat = date('Ymd', strtotime($row["date"]));
			$datedataarray[$row["product_id"]]['type'] = $row["type"];

			if(array_key_exists($row["product_id"], $kit_composition)){
				foreach($kit_composition[$row["product_id"]] as $i => $r){
					$parent_id = $r["parent_id"];
					$parent_desc = $r["parent_desc"];
					$cq = $r["cq"];

					$datedataarray[$row["product_id"]]['parent_id'] = $parent_id;
					$datedataarray[$row["product_id"]]['parent_desc'] = $parent_desc;
					$pms[$ind]['parent_id'] = $parent_id;

					if(array_key_exists($parent_id.$dateformat, $childsum)){
						$childsum[$parent_id.$dateformat] += ($row["pos_total"] * $cq);
					}else{
						$childsum[$parent_id.$dateformat] = ($row["pos_total"] * $cq);
					}

					$children[$parent_id][$row["product_id"]]['desc'] = $row["description"];
					$children[$parent_id][$row["product_id"]][$dateformat] = $row["pos_total"];
				}
			}else{
				$pms[$ind]['parent_id'] = null;
				$datedataarray[$row["product_id"]]['parent_id'] = null;
				$datedataarray[$row["product_id"]]['parent_desc'] = null;
			}

			$datedataarray[$row["product_id"]]['id'] = $row["product_id"];
			$datedataarray[$row["product_id"]]['date'][$dateformat] = $row["pos_total"];
		}

		foreach($datedataarray as $ind => $row){
			foreach($sortedperiod as $si => $sr){
				if(!array_key_exists($si, $row["date"])){
					$datedataarray[$ind]["date"][$si] = 0;
				}
			}
		};

		foreach($children as $ind => $row){
			foreach($row as $ind2 => $row2){
				foreach ($period as $date) {
					$dateStr = $date->format('Ymd');
					if(!array_key_exists($dateStr, $row2)){
						$children[$ind][$ind2][$dateStr] = 0;
					}
				}
			}
		}

		foreach($pms as $ind => $row){
			$total = $row["pos_total"];
			$price = $row["price"];
			$dateformat = date('Ymd', strtotime($row["date"]));

			if(is_null($row["parent_id"]) || $row["parent_id"] == "") {
				$childsumindex = $row["product_id"].$dateformat;
				if(array_key_exists($childsumindex, $childsum)){
					$total = $total - $childsum[$childsumindex];
				}

				if(!$row["allow_weekview"]){
					$total = 0;
				}
			}

			if(strpos($row["product_id"], 'SC')) {
				$price = number_format(($row["price"] * 0.8) / 1.12, 3)	;
			};

			$datedataarray[$row["product_id"]]['sales'][$dateformat] = $total * $price;
			//$datedataarray[$row["product_id"]]['sales'][$row["product_id"]] = $total * $price;
		}

		$datatotal = [];
		$salestotal = [];
		$drilldown = [];

		foreach($datedataarray as $ind => $row){
			foreach ($row['date'] as $ind2 => $row2) {
				if (isset($datatotal[$ind]))
					$datatotal[$ind] += $row2;
				else
					$datatotal[$ind] = $row2;

			}

			foreach ($row['sales'] as $ind2 => $row2) {
				if (isset($salestotal[$ind]))
					$salestotal[$ind] += $row2;
				else
					$salestotal[$ind] = $row2;
			}

			ksort($row["sales"]);
			if($row["parent_id"] == ""){
				$drilldown[$ind] = array(
					"name" => $row["desc"],
					"id" => $row["id"],
					"value" => $row["sales"]
				);
			}
		}

		foreach($datedataarray as $ind => $row){
			if($row["parent_id"] !== ""){
				foreach ($row['sales'] as $ind2 => $row2) {
					if(array_key_exists($row["parent_id"], $drilldown)){
						$drilldown[$row["parent_id"]]["value"][$ind2] += $row2;
					}
				}
			}
		}

		$drilldowndata = [];
		foreach($drilldown as $ind => $row){
			$d = [];
			foreach($row["value"] as $ind2 => $row2){
				$fdate = date('M d', strtotime($ind2));
				array_push($d, array($fdate, $row2));
			}
			array_push($drilldowndata, array(
				"name" => $row["name"],
				"id" => $row["id"],
				"data" => $d
			));
		}

		$data = [];

		foreach($datedataarray as $ind => $row){
			if(is_null($row["parent_id"]) || $row["parent_id"] == ""){
//				$data[$ind] = [];
//				$data[$ind]['desc'] = $row['desc'];
//				$data[$ind]['week_total'] = $salestotal[$ind];
				if(!array_key_exists($ind, $data)){
					$data[$ind] = [];
					$data[$ind]['week_total'] = $salestotal[$ind];
				}else{
					$data[$ind]['week_total'] += $salestotal[$ind];
				}
				$data[$ind]['desc'] = $row['desc'];
				$data[$ind]['type'] = $row['type'];
			}
			else{
				if(!array_key_exists($row["parent_id"], $data)){
					$data[$row["parent_id"]] = [];
					$data[$row["parent_id"]]['week_total'] = $salestotal[$ind];
				}else {
					$data[$row["parent_id"]]['week_total'] += $salestotal[$ind];
				}
				$data[$row["parent_id"]]['desc'] = $row['parent_desc'];
				$data[$row["parent_id"]]['type'] = $row['type'];
			}
		}

		ksort($sortedperiod);

		$totalsales = 0;
		$salesarr = [];

		if(count($data) > 0) {
			foreach ($sortedperiod as $ind => $row) {
				$salesarr[$ind] = array(
					"date" => date("D - F j", strtotime($ind)),
					"sales" => number_format($row, 2)
				);

				$totalsales += $row;
			}
		}

		ksort($salesarr);

		$response = [];
		$response["product_cont"] = [];
		$response["total_sales"] = number_format($totalsales, 2);
		$response["ave_sales"] = number_format(($totalsales / 7), 2);
		$response["datefrom"] = date('F d, Y', strtotime($param["datefrom"]));
		$response["dateto"] = date('F d, Y', strtotime($param["dateto"]));

		$product_meal_arr = [];
		$product_drinks_arr = [];

		foreach($data as $ind => $row){
			$contribution = 0;

			if($totalsales > 0){
//				if(preg_match('/\b50016\b/', $ind)){
//					$row["week_total"] /= 250;
//				}
				$contribution = $row["week_total"];
				//$contribution =  number_format((($row["week_total"] / $totalsales ) * 100), 2);
			}

			if(preg_match('/\bRICE\b/', strtoupper($row["desc"]))){
				$contribution = 0;
			}

//			print_r($row["desc"]."==".$contribution."<br />");

			if($contribution > 0){
//				$response[$ind]["name"] = $description[$ind];
//				$response[$ind]["y"] = $contribution;

				$res = array(
					"name" => $row["desc"],
					"y" => $contribution,
					//"color" => '#'.$this->random_color_part().$this->random_color_part().$this->random_color_part() // generate random color
				);

				if($row["type"] == 0){
					$product_meal_arr[intval($contribution)] = array(
						"name" => $row["desc"],
						"y" => $contribution,
						"drilldown" => strval($ind)
					);
				}else{
					$product_drinks_arr[intval($contribution)] = array(
						"name" => $row["desc"],
						"y" => $contribution,
						"drilldown" => strval($ind)
					);
				}

				array_push($response["product_cont"], $res);

			}
		}

		krsort($product_meal_arr);
		krsort($product_drinks_arr);

		$sortedmeal = [];
		$sorteddrinks = [];

		$mealcount = 0;
		foreach($product_meal_arr as $ind => $row){
			if($mealcount < 5)
				array_push($sortedmeal, $row);
			$mealcount++;
		}

		$drinkcount = 0;
		foreach($product_drinks_arr as $ind => $row){
			if($drinkcount < 5)
				array_push($sorteddrinks, $row);
			$drinkcount++;
		}

		$response["mealsales"] = $sortedmeal;
		$response["drinksales"] = $sorteddrinks;

		$response["salesarr"] = $salesarr;
		$response["drilldown"] = $drilldowndata;

		echo json_encode($response);
	}

	private function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

//	public function top_sales(){
//		$this->load->model('modProductMovement', "", TRUE);
//		$param = $this->input->post(NULL, "true");
//
//		$_param["datefrom"] = $param["date"][0];
//		$_param["dateto"] = $param["date"][6];
//
//		$_param["datefrom"] = "2021-01-01";
//		$_param["dateto"] = "2021-01-07";
//
//		$top_sales_product = $this->modProductMovement->getTopSales($_param)->result_array();
//		print_r($top_sales_product);
//	}
}
