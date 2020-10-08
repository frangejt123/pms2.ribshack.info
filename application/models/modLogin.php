<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModLogin extends CI_Model {

    public $NAMESPACE = "user";
    private $TABLE = "user",
            $FIELDS = array(
                "id" => "user.id",
                "firstname" => "user.firstname",
                "lastname" => "user.lastname",
                "username" => "user.username",
                "password" => "user.password",
                "access_level" => "user.access_level",
                "branch_id" => "user.branch_id",
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {

        $tablefield = "";
        $this->FIELDS["poscount"] = "branch.pos_count";
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
        $this->db->join("branch", "branch.branch_id=user.branch_id", "left");

        $query = $this->db->get();

        return $query;
    }
}
