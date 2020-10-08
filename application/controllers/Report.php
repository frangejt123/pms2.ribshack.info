<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {
        public function index(){
            if(isset($_SESSION["rgc_username"])){
                $data["poscount"] = $_SESSION["rgc_poscount"];
                $this->config->load('reportname', TRUE);
                $this->load->model('modBranch', "", TRUE);
                $branch = $this->modBranch->getAll(null)->result_array();
                $branchArray = array();
                foreach($branch as $ind => $row){
                    array_push($branchArray, array("id"=>$row["id"],"text"=>$row["branch_name"]));
                }

                $data["branch"] = json_encode($branchArray);
                $data["report"] = json_encode($this->config->item('report', 'reportname'));
                $this->load->view('report', $data);
            }else{
              $this->load->view('login');
            }
        }

        public function productmovement(){
            $this->load->library('Pdf');
            $this->load->model('modReport', "", TRUE);
            $this->load->model('modProduct', "", TRUE);
            $this->load->model('modBranch', "", TRUE);
            $this->load->model('modPeriod', "", TRUE);
            $param = $this->input->get(NULL, "true");
            // $param["period_id"] = $param["q"];
            // $periodparam["id"] = $param["q"];
            $branchParam["id"] = $param["branch"];

            $res = $this->modReport->getProductMovementReport($param)->result_array();
            $parent_product = $this->modProduct->getParent(null)->result_array();
            // $res = $this->modProductmovement->getAll($param)->result_array();
            $branchData = $this->modBranch->getAll($branchParam)->row_array();

            $period_date_from = $param["datefrom"];
            $period_date_to = $param["dateto"];

            $product = array();
            foreach($parent_product as $ind => $row){
                $product[$row["id"]] = array();
                $product[$row["id"]]["child"] = array();
            }

            $data = array();
            foreach($res as $ind => $row){
                if(!is_null($row["parent_id"])){
                    $childrow["product_id"] = $row["product_id"];
					$childrow["pos1"] = $row["pos1"];
					$childrow["pos2"] = $row["pos2"];
					$childrow["pos3"] = $row["pos3"];
					$childrow["pos4"] = $row["pos4"];
					$childrow["pos5"] = $row["pos5"];
					$childrow["pos_total"] = $row["pos_total"];
                    $childrow["description"] = $row["description"];
                    $childrow["uom"] = $row["uom_abbr"];
                    $childrow["pid"] = $row["parent_id"];

                    array_push($product[$row["parent_id"]]["child"], $childrow);
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
                    $product[$row["product_id"]]["parent_id"] = $row["parent_id"];
                    $product[$row["product_id"]]["uom_abbr"] = $row["uom_abbr"];
                }
            }

            $d["report_data"] = $product;
            $period_date;
            if($period_date_from == $period_date_to){
                $period_date = date("F d, Y", strtotime($period_date_from));
            }else{
                $period_date = date("F d, Y", strtotime($period_date_from))." - ".date("F d, Y", strtotime($period_date_to));
            }

            $d["period_date"] = $period_date;
            $d["address"] = $branchData["address"];
            $d["operated_by"] = $branchData["operated_by"];
            $this->load->view('report/product_movement_report', $d);
        }

        public function drinkpercentage(){
            $this->load->library('Pdf');
            $this->load->model('modReport', "", TRUE);
            $this->load->model('modProduct', "", TRUE);
            $this->load->model('modBranch', "", TRUE);
            $this->load->model('modPeriod', "", TRUE);
            $param = $this->input->get(NULL, "true");
            $branchParam["id"] = $param["branch"];
            $branchData = $this->modBranch->getAll($branchParam)->row_array();

            $period_date_from = $param["datefrom"];
            $period_date_to = $param["dateto"];


            $meals_beverage = $this->modReport->getMealBeverage($param)->result_array();
            $seafoodmeal = $this->modReport->getSeafoodMeal($param)->result_array();
            
            $mainmeal = array();
            //extract main meal
            foreach($meals_beverage as $ind => $row){
                if (strpos($row["product_id"], 'SC') == false) {
                    $mainmeal[$row["product_id"]] = $row;
                }
            }
            //add sc to its parent
            foreach($meals_beverage as $ind => $row){
                if (strpos($row["product_id"], 'SC') !== false) {
                    $product_id = str_replace("SC", "", $row["product_id"]);
                    $mainmeal[$product_id]["pos_total"] += $row["pos_total"];
                }
            }

            //add seafood to mainmeal
            foreach($seafoodmeal as $ind => $row){
                $row["description"] = "Seafood Meal";
                $mainmeal[$row["product_id"]] = $row;
            }

            //seperate meal and beverage
            $meal = array();
            $beverage = array();
            foreach($mainmeal as $ind => $row){
            	if($row["pos_total"] > 0){
					if($row["product_type"] == 1){//product is meal
						$meal[$row["product_id"]] = $row;
					}else{//product is beverage
						$beverage[$row["product_id"]] = $row;
					}
				}
            }


            $d["period_date"] = date("F d, Y", strtotime($period_date_from))." - ".date("F d, Y", strtotime($period_date_to));
            $d["address"] = $branchData["address"];
            $d["operated_by"] = $branchData["operated_by"];
            $d["meal"] = $meal;
            $d["beverage"] = $beverage;
            $this->load->view('report/drinkpercentage_report', $d);

        }
}
