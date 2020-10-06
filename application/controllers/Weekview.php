<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Weekview extends CI_Controller {
	public function index()
	{
		if(isset($_SESSION["rgc_email"])){
			$data["poscount"] = $_SESSION["rgc_poscount"];
			$this->load->view('weekview', $data);
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

	public function getTotalpms(){
		$this->load->model('modConversion', "", TRUE);
		$this->load->model('modPeriod', "", TRUE);
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$draw = $param['draw'];
		$param["datefrom"] = $param["startDate"];
		$param["dateto"] = $param["endDate"];

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

		$datedataarray = [];

		$childsum = [];

		$children = [];

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

		$datatotal = [];
		$salestotal = [];

		$datecount = 0;
		foreach ($period as $date) {
			$datecount++;
		}

		$sortedperiod = [];
		foreach($perioddata as $ind => $row){
			$dateformat = date('Ymd', strtotime($row["date"]));
			$sortedperiod[$dateformat] = $row["sales"];
		}

//		foreach($datedataarray as $ind => $row){
//			print_r($row['sales']);
//		}

		foreach($datedataarray as $ind => $row){
			foreach ($row['date'] as $ind2 => $row2) {
				if (isset($datatotal[$ind]))
					$datatotal[$ind] += $row2;
				else
					$datatotal[$ind] = $row2;

			}

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
					$sortedperiod[$dateStr] = 0;
					$datedataarray[$ind]['date'][$dateStr] = 0;
				}
			}
		}

		foreach($datedataarray as $ind => $row){
			ksort($row['date']);
			$datedataarray[$ind]['date'] = $row['date'];
		}

		$data = [];
		foreach($datedataarray as $ind => $row){
			if(is_null($row["parent_id"]) || $row["parent_id"] == ""){
				$data[$ind] = [];

				$data[$ind]['desc'] = $row['desc'];
				$data[$ind]['week_total'] = number_format($datatotal[$ind], 2);
				$data[$ind]['week_avg'] = number_format($datatotal[$ind] / $datecount, 2);

				foreach($row['date'] as $ind2 => $row2){
					$data[$ind][$ind2] = number_format($row2, 2);
				}

				foreach($children[$row["id"]] as $cind => $crow){
					$data[$ind]['child'][$cind] = $crow;
				}
			}
		}

		ksort($sortedperiod);

		if(count($data) > 0) {
			$weeksales = 0;
			foreach ($salestotal as $ind => $row) {
				$weeksales += $row;
			}
			ksort($salestotal);

			$salesdata["desc"] = 'POS Sales';
			$salesdata["week_total"] =  number_format($weeksales, 2);
			$salesdata["week_avg"] = number_format(($weeksales / $datecount), 2);
			foreach ($salestotal as $ind => $row) {
				$row = number_format($row, 2);
				$salesdata[$ind] = $row;
			}

			array_unshift($data, $salesdata);

			$actualweeksales = 0;
			foreach ($sortedperiod as $ind => $row) {
				$actualweeksales += $row;
			}

			$newperioddata["desc"] = 'Actual Sales';
			$newperioddata["week_total"] =  number_format($actualweeksales, 2);
			$newperioddata["week_avg"] = number_format(($actualweeksales / $datecount), 2);
			foreach ($sortedperiod as $ind => $row) {
				$row = number_format($row, 2);
				$newperioddata[$ind] = $row;
			}

			array_unshift($data, $newperioddata);

		}

		$aadata = [];
		foreach($data as $ind => $row){
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
}
