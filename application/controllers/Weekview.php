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

		$pmstotal = [];

		foreach($pms as $ind => $row){
			$pmstotal[$row["product_id"]] = $row["pos_total"];
		}

		$data = [];

		if(count($pms) > 0)
			foreach($convertion as $ind => $row){
				$week_total = $row["conversion"] * $pmstotal[$row["product_code"]];
				$week_avg =  $week_total / 7;

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
		$this->load->model('modProductMovement', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$draw = $param['draw'];
		$param["datefrom"] = $param["startDate"];
		$param["dateto"] = $param["endDate"];

		$convertion = $this->modConversion->getAll($param)->result_array();
		$pms = $this->modProductMovement->getTotal($param)->result_array();

		$totalRecords = $this->modConversion->getAll($param)->num_rows();
		$totalRecordwithFilter = $totalRecords;

		$pmstotal = [];

		$datedataarray = [];

		$period = new DatePeriod(
			new DateTime($param["datefrom"]),
			new DateInterval('P1D'),
			new DateTime($param["dateto"]. ' 23:59:59')
		);

		foreach($pms as $ind => $row){
			$datedataarray[$row["product_id"]]['desc'] = $row["description"];
			$datedataarray[$row["product_id"]]['date'][$row["date"]] = $row["pos_total"];
		}

		$datatotal = [];
		foreach($datedataarray as $ind => $row){
			foreach ($row['date'] as $ind2 => $row2) {
				if (isset($datatotal[$ind]))
					$datatotal[$ind] += $row2;
				else
					$datatotal[$ind] = $row2;
			}

			foreach ($period as $date) {
				$dateStr = $date->format('Y-m-d');
				if(!array_key_exists($dateStr, $row['date'])){
					$datedataarray[$ind]['date'][$dateStr] = 0;
				}
			}
		}

		$data = [];
		foreach($datedataarray as $ind => $row){
			$data[$ind] = [];
			array_push($data[$ind], $row['desc']);
			array_push($data[$ind], $datatotal[$ind]);
			array_push($data[$ind], number_format($datatotal[$ind] / 7, 2));

			foreach($row['date'] as $ind2 => $row2){
				array_push($data[$ind], $row2);
			}

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
