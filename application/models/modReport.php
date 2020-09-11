<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModReport extends CI_Model {

    function getProductMovementReport($param){
        $tablefield = "";

        $query = $this->db->query("Select
                            product.parent_id,
                            product.description,
                            product.uom,
                            product.price,
                            period.id as period_id,
                            period.date,
                            period.status,
                            uom.abbreviation as uom_abbr,
                            uom.description As uomdescription,
                            branch.branch_name,
                            branch.address,
                            branch.tin,
                            branch.operated_by,
                            product_movement.id as id,
                            product_movement.product_id as product_id,
                            Sum(product_movement.pos_sold) as pos_sold,
                            Sum(product_movement.beginning) as beginning,
                            Sum(product_movement.ending) as ending,
                            Sum(product_movement.delivery) as delivery,
                            Sum(product_movement.actual) as actual,
                            Sum(product_movement.trans_in) as trans_in,
                            Sum(product_movement.trans_out) as trans_out,
                            Sum(product_movement.return_stock) as return_stock,
                            Sum(product_movement.discrepancy) as discrepancy
                        From
                            product Inner Join
                            product_movement On product_movement.product_id = product.id Inner Join
                            period On product_movement.period_id = period.id Inner Join
                            uom On product.uom = uom.id Left Join
                            branch On period.branch_id = branch.branch_id
                        Where
                            (period.date >= '".date("Y-m-d", strtotime($param["datefrom"]))."' And
                            period.date <= '".date("Y-m-d", strtotime($param["dateto"]))."') And
                            branch.branch_id = '".$param["branch"]."' And
                            period.status = '1'
                        Group By product_movement.product_id
                        Order By
                            product.description ASC");

        return $query;
    }

    function getMealBeverage($param){
        $tablefield = "";

        $query = $this->db->query("Select
                            SUM(product_movement.pos_sold) as pos_sold,
                            product_movement.period_id,
                            product.description,
                            product.product_type,
                            branch.branch_name,
                            branch.address,
                            branch.operated_by,
                            product.id as product_id
                        From
                            product_movement Inner Join
                            product On product_movement.product_id = product.id Inner Join
                            period On product_movement.period_id = period.id Left Join
                            branch On period.branch_id = branch.branch_id
                         Where (product.product_type = '1' OR product.product_type = '2') AND branch.branch_id = '".$param["branch"]."'
                         And (period.date >= '".date("Y-m-d", strtotime($param["datefrom"]))."' And
                            period.date <= '".date("Y-m-d", strtotime($param["dateto"]))."') And
                            period.status = '1'
                         Group by product.id
                         Order By product.description ASC");

        return $query;
    }

    function getSeafoodMeal($param){
        $tablefield = "";

        $query = $this->db->query("Select
                            SUM(product_movement.pos_sold) as pos_sold,
                            product_movement.period_id,
                            product.description,
                            '1' as product_type,
                            branch.branch_name,
                            branch.address,
                            branch.operated_by,
                            product.id as product_id
                        From
                            product_movement Inner Join
                            product On product_movement.product_id = product.id Inner Join
                            period On product_movement.period_id = period.id Left Join
                            branch On period.branch_id = branch.branch_id
                         Where (product.id = '51002SF' OR product.id = '51002SFSC') AND branch.branch_id = '".$param["branch"]."'
                         And (period.date >= '".date("Y-m-d", strtotime($param["datefrom"]))."' And
                            period.date <= '".date("Y-m-d", strtotime($param["dateto"]))."') And
                            period.status = '1'
                         Group by product.parent_id
                         Order By product.description ASC");

        return $query;
    }
}
