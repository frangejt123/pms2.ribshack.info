<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModConversion extends CI_Model {

	public $NAMESPACE = "conversion";
	private $TABLE = "conversion",
		$FIELDS = array(
		"id" => "conversion.id",
		"raw_material_id" => "conversion.raw_material_id",
		"product_code" => "conversion.product_code",
		"conversion" => "conversion.conversion"
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$this->FIELDS["raw_material"] = "raw_material.description";
		$this->FIELDS["product_description"] = "product.description";
		$this->FIELDS["uom_abbr"] = "uom.abbreviation";
		$this->FIELDS["type"] = "raw_material.type";

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
		$this->db->from("conversion");
		$this->db->join('raw_material', 'raw_material.id = conversion.raw_material_id');
		$this->db->join('product', 'conversion.product_code = product.id');
		$this->db->join('uom', 'uom.id = raw_material.uom');
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

		if ($this->db->insert('conversion', $data)) {
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

		if ($this->db->update('conversion', $data)) {
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

		if ($this->db->delete('conversion')) {
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
