<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModProductmovement extends CI_Model {

    public $NAMESPACE = "product_movement";
    private $TABLE = "product_movement",
            $FIELDS = array(
                "id" => "product_movement.id",
                "period_id" => "product_movement.period_id",
                "product_id" => "product_movement.product_id",
                "pos1" => "product_movement.pos1",
				"pos2" => "product_movement.pos2",
				"pos3" => "product_movement.pos3",
				"pos4" => "product_movement.pos4",
				"pos5" => "product_movement.pos5",
				"pos_total" => "product_movement.pos_total",
                "beginning" => "product_movement.beginning",
                "ending" => "product_movement.ending",
                "delivery" => "product_movement.delivery",
                "actual" => "product_movement.actual",
                "trans_in" => "product_movement.trans_in",
                "trans_out" => "product_movement.trans_out",
                "return_stock" => "product_movement.return_stock",
                "discrepancy" => "product_movement.discrepancy",
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
        $this->FIELDS["description"] = "product.description";
        $this->FIELDS["uom_abbr"] = "uom.abbreviation";
        $this->FIELDS["period_date"] = "period.date";

        $tablefield = "";

        foreach ($this->FIELDS as $alias => $field) {
            if ($tablefield != "") {
                $tablefield .= ",";
            }
            //Construct table field selection
            $tablefield .= $field . " AS `" . $alias . "`";
            if($param)
            if (array_key_exists($alias, $param)) {
                $this->db->where($field, $param[$alias]);
            }
        }

        $this->db->select($tablefield);
        $this->db->from("product_movement");
        $this->db->join('product', 'product.id = product_movement.product_id');
        $this->db->join('uom', 'product.uom = uom.id');
        $this->db->join('period', 'period.id = product_movement.period_id');
        $this->db->order_by('product.description', 'ASC');

        $query = $this->db->get();

        return $query;
    }

    function insert($param) {
        $result = array();
        $data = array();
        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param)) {
                if ($param[$alias] != "") {
                    $data[$field] = $param[$alias];
                }
            }
        }

        if ($this->db->insert('product_movement', $data)) {
            //$result_row = $this->db->query("SELECT LAST_INSERT_ID() AS `id`")->result_object();
            $result["id"] = $this->db->insert_id();
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

    function update($param) {

        $result = array();
        $data = array();
//        $param["id"] = $param["_server_id"];
        $id = $param["id"];
        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param))
                $data[$field] = $param[$alias];
        }

        $this->db->where($this->FIELDS['id'], $id);

        if ($this->db->update('product_movement', $data)) {
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

    function delete($param) {

        $result = array();
        $this->db->where($this->FIELDS['id'], $param["id"]);

        if ($this->db->delete('product_movement')) {
            $result["id"] = $param["id"];
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

	function deletebyperiod($param) {

		$result = array();
		$this->db->where($this->FIELDS['period_id'], $param["period_id"]);

		if ($this->db->delete('product_movement')) {
			$result["period_id"] = $param["period_id"];
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

    function getActual($param){
		$this->db->select("actual");
		$this->db->from("product_movement");
		$this->db->where("period_id", $param["period_id"]);
		$this->db->where("product_id", $param["product_id"]);

		$query = $this->db->get();

		return $query;
	}

	function getTotal($param){
		$this->db->select("product_id, price, SUM(pos_total) as pos_total, 
		period.date, product.description, product.allow_weekview");
		$this->db->from("product_movement");
		$this->db->join('period', 'period.id = product_movement.period_id');
		$this->db->join('product', 'product.id = product_movement.product_id');
		$this->db->group_by("product_id");
		if(!isset($param["datemerge"])){
			$this->db->group_by("period.date");
		}
		if(isset($param["branch_id"])){
			$this->db->where('period.branch_id =', $param["branch_id"]);
		}
		//$this->db->where('product.parent_id =', NULL);
//		$this->db->where('product.allow_weekview =', '1');
		$this->db->where('period.date >=', $param["datefrom"]);
		$this->db->where('period.date <=', $param["dateto"]);
//		$this->db->where('product_movement.product_id LIKE', '%60014%');
		$this->db->order_by('product.description', 'ASC');
		$query = $this->db->get();

		return $query;
	}

	function getParent($product_id){
    	$this->db->select("`kit_composition`.`parent_id`");
		$this->db->from("kit_composition");
		$this->db->where('`kit_composition`.`product_id` =', $product_id);
		$query = $this->db->get();
		return $query;
	}

	function getTopSales($param){
		$this->db->select("product_id, price, SUM(pos_total) as pos_total, period.date, product.description, parent_id, product.allow_weekview, product.composition_qty as cq");
		$this->db->from("product_movement");
		$this->db->join('period', 'period.id = product_movement.period_id');
		$this->db->join('product', 'product.id = product_movement.product_id');
		$this->db->group_by("product_id");
		if(!isset($param["datemerge"])){
			$this->db->group_by("period.date");
		}
		if(isset($param["branch_id"])){
			$this->db->where('period.branch_id =', $param["branch_id"]);
		}
		//$this->db->where('product.parent_id =', NULL);
//		$this->db->where('product.allow_weekview =', '1');
		$this->db->where('period.date >=', $param["datefrom"]);
		$this->db->where('period.date <=', $param["dateto"]);
		$this->db->order_by('product.description', 'ASC');
		$query = $this->db->get();

		return $query;
	}
    
}
