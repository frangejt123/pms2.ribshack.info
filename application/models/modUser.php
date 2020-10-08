<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModUser extends CI_Model {

    public $NAMESPACE = "user";
    private $TABLE = "user",
            $FIELDS = array(
                "id" => "user.id",
                "username" => "user.username",
                "password" => "user.password",
                "firstname" => "user.firstname",
                "lastname" => "user.lastname",
                "branch_id" => "user.branch_id",
                "access_level" => "user.access_level"
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
        $tablefield = "";
        $this->FIELDS["branch_name"] = "branch.branch_name";

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
        $this->db->from("user");
        $this->db->join("branch", "branch.branch_id=user.branch_id");
        $this->db->order_by("lastname", "asc");

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

        if ($this->db->insert('user', $data)) {
            //$result_row = $this->db->query("SELECT LAST_INSERT_ID() AS `id`")->result_object();
            $result["id"] = $this->db->insert_id();
            $result["firstname"] = $param["firstname"];
            $result["lastname"] = $param["lastname"];
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

        if ($this->db->update('user', $data)) {
            $result["success"] = true;
            $result["firstname"] = $param["firstname"];
            $result["lastname"] = $param["lastname"];
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }
    

    function updatepassword($param) {

        $result = array();
        $data = array();
        $id = $param["id"];

        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param))
                $data[$field] = $param[$alias];
        }

        $this->db->where($this->FIELDS['id'], $id);

        if ($this->db->update('user', $data)) {
            $result["success"] = true;
            session_destroy();
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

        if ($this->db->delete('user')) {
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
