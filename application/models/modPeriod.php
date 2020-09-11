<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModPeriod extends CI_Model {

    public $NAMESPACE = "period";
    private $TABLE = "period",
            $FIELDS = array(
                "id" => "period.id",
                "date" => "period.date",
                "status" => "period.status",
                "branch_id" => "period.branch_id"
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
        $this->db->from("period");
        $this->db->order_by("date", "desc");

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

        if ($this->db->insert('period', $data)) {
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

        if ($this->db->update('period', $data)) {
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

        if ($this->db->delete('period')) {
            $result["id"] = $param["id"];
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

	function getLastID(){
		$this->db->select("id");
		$this->db->from("period");
		$this->db->order_by("id", 'DESC');
		$this->db->limit(1,1);

		$query = $this->db->get();

		return $query;
	}


}
