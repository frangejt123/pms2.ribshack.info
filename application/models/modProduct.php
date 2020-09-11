<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModProduct extends CI_Model {

    public $NAMESPACE = "product";
    private $TABLE = "product",
            $FIELDS = array(
                "id" => "product.id",
                "parent_id" => "product.parent_id",
                "description" => "product.description",
                "uom" => "product.uom",
                "price" => "product.price",
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
    	$this->FIELDS["uom_description"] = "uom.description";
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
        $this->db->from("product");
        $this->db->join('uom', 'uom.id = product.uom');
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

        $this->db->select($tablefield);
        $this->db->from("product");
        $this->db->where('product.parent_id IS NULL', null, false);
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
        $this->db->from("product");
        $this->db->where('product.parent_id', $param["product_id"]);

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

        if ($this->db->insert('product', $data)) {
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
        if($param["parent_id"] == "")
        	$param["parent_id"] = null;
        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param))
                $data[$field] = $param[$alias];
        }

        $this->db->where($this->FIELDS['id'], $id);

        if ($this->db->update('product', $data)) {
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

        if ($this->db->delete('product')) {
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
