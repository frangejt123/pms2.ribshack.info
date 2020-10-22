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
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$dateparam["datefrom"] = $param["date"][0];
		$dateparam["dateto"] = $param["date"][6];

		$pms = $this->modProductMovement->getTotal($dateparam)->result_array();

		$datedataarray = [];
		$description = [];
		$productprice = [];

		$children = [];
		$realqty = [];

		$not_allowedview = [];

		foreach($pms as $ind => $row){
			$price = $row["price"];
			if(strpos($row["product_id"], 'SC')) {
				$price = number_format(($row["price"] * 0.8) / 1.12, 3)	;
			};
			$productprice[$row["product_id"]] = $price;
			$description[$row["product_id"]] = $row["description"];
			$pos_total = $row["pos_total"] * $row["cq"];

			if(!is_null($row["parent_id"]) || $row["parent_id"] != "") {
				if(array_key_exists($row["parent_id"], $children)){
					if(array_key_exists($row["product_id"], $children[$row["parent_id"]])){
						$children[$row["parent_id"]][$row["product_id"]] += $pos_total;
					}else{
						$children[$row["parent_id"]][$row["product_id"]] = $pos_total;
					}
				}else{
					$children[$row["parent_id"]][$row["product_id"]] = $pos_total;
				}
			}else{
				if (array_key_exists($row["product_id"], $datedataarray)) {
					$datedataarray[$row["product_id"]] += $pos_total;
				} else {
					$datedataarray[$row["product_id"]] = $pos_total;
				}
			}

			if(!$row["allow_weekview"]) {
				array_push($not_allowedview, $row["product_id"]);
			}

			if (array_key_exists($row["product_id"], $realqty)) {
				$realqty[$row["product_id"]] += $row["pos_total"];
			} else {
				$realqty[$row["product_id"]] = $row["pos_total"];
			}
		}

		foreach($datedataarray as $ind => $row){
			$totqty = $row;
			foreach($children[$ind] as $i => $r){
				$totqty -= $r;
			}

			if(in_array($ind, $not_allowedview)) {
				$totqty = 0;
			}

			$datedataarray[$ind] = ($totqty * $productprice[$ind]);
		}

		foreach($datedataarray as $ind => $row){
			foreach($children[$ind] as $i => $r){
				$datedataarray[$ind] += ($realqty[$i] * $productprice[$i]);
			}
		}


		$totalsales = 0;
		foreach($datedataarray as $ind => $row){
			$totalsales += $row;
		}



		$response = [];
		$response["product_cont"] = [];
		$response["total_sales"] = number_format($totalsales, 2);
		$response["ave_sales"] = number_format(($totalsales / 7), 2);
		$response["datefrom"] = date('F d, Y', strtotime($dateparam["datefrom"]));
		$response["dateto"] = date('F d, Y', strtotime($dateparam["dateto"]));

		foreach($datedataarray as $ind => $row){
			$contribution =  number_format((($row / $totalsales ) * 100), 2);

			if($contribution > 0){
//				$response[$ind]["name"] = $description[$ind];
//				$response[$ind]["y"] = $contribution;

				$res = array(
					"name" => $description[$ind],
					"y" => floatval($contribution)
				);

				array_push($response["product_cont"], $res);

			}
		}

		echo json_encode($response);
	}

	public function daily_sales(){
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$dateparam["datefrom"] = $param["date"][0];
		$dateparam["dateto"] = $param["date"][6];

		$pms = $this->modProductMovement->getTotal($dateparam)->result_array();

		$datedataarray = [];
		$childsum = [];
		$children = [];

		$period = new DatePeriod(
			new DateTime($dateparam["datefrom"]),
			new DateInterval('P1D'),
			new DateTime($dateparam["dateto"]. ' 23:59:59')
		);

		foreach($pms as $ind => $row){
			$datedataarray[$row["product_id"]]['desc'] = $row["description"];
			$datedataarray[$row["product_id"]]['parent_id'] = $row["parent_id"];
			$datedataarray[$row["product_id"]]['id'] = $row["product_id"];
			$dateformat = date('Ymd', strtotime($row["date"]));

			$datedataarray[$row["product_id"]]['date'][$dateformat] = $row["pos_total"];

			if(!is_null($row["parent_id"]) || $row["parent_id"] != ""){
				if(array_key_exists($row["parent_id"].$dateformat, $childsum)){
					$childsum[$row["parent_id"].$dateformat] += ($row["pos_total"] * $row["cq"]);
				}else{
					$childsum[$row["parent_id"].$dateformat] = ($row["pos_total"] * $row["cq"]);
				}

				$children[$row["parent_id"]][$row["product_id"]]['desc'] = $row["description"];
				$children[$row["parent_id"]][$row["product_id"]][$dateformat] = $row["pos_total"];
			}
		}

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
				$total = $total - $childsum[$row["product_id"].$dateformat];

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

		$salestotal = [];

		foreach($datedataarray as $ind => $row){
			foreach ($row['sales'] as $ind2 => $row2) {
				if (isset($salestotal[$ind2]))
					$salestotal[$ind2] += $row2;
				else
					$salestotal[$ind2] = $row2;

			}

			foreach ($period as $date) {
				$dateStr = $date->format('Ymd');
				if(!array_key_exists($dateStr, $row['date'])){
					$salestotal[$dateStr] = 0;
				}
			}
		}

		$salesdata = [];

		foreach($salestotal as $ind => $row){
			$arr = array(
				"date" => date("D - F j", strtotime($ind)),
				"sales" => number_format($row, 2)
			);

			$salesdata[$ind] = $arr;
		}

		echo json_encode($salesdata);

	}

	public function top_sales(){
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$_param["datefrom"] = $param["date"][0];
		$_param["dateto"] = $param["date"][6];

		$top_sales_product = $this->modProductMovement->getTopSales($_param)->result_array();
		print_r($top_sales_product);
	}
}
