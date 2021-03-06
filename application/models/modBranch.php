<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModBranch extends CI_Model {

    public $NAMESPACE = "branch";
    private $TABLE = "branch",
            $FIELDS = array(
                "id" => "branch.branch_id",
				"branch_code" => "branch.branch_code",
                "branch_name" => "branch.branch_name",
                "address" => "branch.address",
                "tin" => "branch.tin",
                "operated_by" => "branch.operated_by",
                "pos_count" => "branch.pos_count"
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
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
        $this->db->from("branch");
        $this->db->order_by("branch_name", "asc");

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

        if ($this->db->insert('branch', $data)) {
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

        if ($this->db->update('branch', $data)) {
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

        if ($this->db->delete('branch')) {
            $result["id"] = $param["id"];
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

	function checkcode($param) {
		$this->db->select("branch_id");
		$this->db->from("branch");
		if(isset($param["id"])){
			$this->db->where('branch.branch_id !=', $param["id"]);
		}
		$this->db->where('UCASE(branch.branch_code) =', $param["branchcode"]);
		$query = $this->db->get();

		return $query;
	}


}
