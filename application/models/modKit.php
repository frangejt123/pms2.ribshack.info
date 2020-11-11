<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModKit extends CI_Model {

	public $NAMESPACE = "kit_composition";
	private $TABLE = "kit_composition",
		$FIELDS = array(
		"id" => "kit_composition.id",
		"product_id" => "kit_composition.product_id",
		"parent_id" => "kit_composition.parent_id",
		"quantity" => "kit_composition.quantity",
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";
		$this->FIELDS["description"] = "product.description";

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
		$this->db->from("kit_composition");
		$this->db->join('product', 'product.id = kit_composition.parent_id');
		$this->db->order_by('product.description', 'ASC');

		$query = $this->db->get();

		return $query;
	}

	function getParent($param) {

		$tablefield = "";

		foreach ($this->FIELDS as $alias => $field) {
			if ($tablefield != "") {
				$tablefield .= ",";
			}
			//Construct table field selection
			$tablefield .= $field . " AS `" . $alias . "`";
		}

		$this->db->select("product.description, product.id");
		$this->db->from("product");

		$this->db->where('NOT EXISTS (SELECT id
                   FROM kit_composition
                   WHERE product.id = kit_composition.product_id)', null, FALSE);
		if(isset($param["product_id"])){
            $this->db->where('product.id !=', $param["product_id"]);
        }
		$this->db->order_by('product.description', 'ASC');
		$query = $this->db->get();

		return $query;
	}

	function getChild($param) {

		$tablefield = "";

		foreach ($this->FIELDS as $alias => $field) {
			if ($tablefield != "") {
				$tablefield .= ",";
			}
			//Construct table field selection
			$tablefield .= $field . " AS `" . $alias . "`";
		}

		$this->db->select($tablefield);
		$this->db->from("kit_composition");
		$this->db->where('kit_composition.parent_id', $param["kit_composition_id"]);

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

		if ($this->db->insert('kit_composition', $data)) {
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

		if ($this->db->update('kit_composition', $data)) {
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

		if ($this->db->delete('kit_composition')) {
			$result["id"] = $param["id"];
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}




}
