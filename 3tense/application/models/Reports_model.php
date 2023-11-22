<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends CI_Model{

    ////Function for Summary Report
     function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_summary_report($type, $where)
    {

        $query = "";

        if($type == 'client')

        {

            $query = $this->db->query("call sp_summary_report_client(?, ?)", $where);

        }

        else

        {

            $query = $this->db->query("call sp_summary_report_family(?, ?)", $where);

        }

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }
    
    function get_summary_report_previous($type, $where)
    {

        $query = "";

        if($type == 'client')
        {
            $query = $this->db->query("call sp_summary_report_client_previous(?, ?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_summary_report_family_previous(?, ?, ?)", $where);
        }
        if($query) {

            $res = $query->result();
            $query->next_result();
            $query->free_result();
            return $res;

        } else {

            return false;

        }

    }

    function get_summary_report_client_import($where)
    {

        $query = "";
        $query = $this->db->query("call sp_summary_report_client_import(?)", $where);

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }
    
    function import_equity_summary_report_client_wise($where)
    {

        $query = "";
        $query = $this->db->query("call sp_equity_summary_report_client_import(?)", $where);

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }

    function get_cash_flow_report_Commitments($type, $where)
    {

        $query = "";

        if($type == 'client')

        {

            $query = $this->db->query("call sp_cash_flow_report_client_Commitments(?, ?, ?, ?)", $where);

        }

        else

        {

            $query = $this->db->query("call sp_cash_flow_report_family_Commitments(?, ?, ?, ?)", $where);

        }



        if($query) {

            //To execute multiple queries

            $res = $query->result();

            
            $query->next_result();

            $query->free_result();

            return $res;

        } else {
            return false;
        }
    }

    ////Function for Cash Flow Report

    function get_cash_flow_report($type, $where)
    {
        $res1=$this->get_cash_flow_report_Commitments($type, $where);
       
        $query = "";
        $res = "";
        if($type == 'client')

        {

            $query = $this->db->query("call sp_cash_flow_report_client(?, ?, ?, ?)", $where);

        }

        else

        {

            $query = $this->db->query("call sp_cash_flow_report_family(?, ?, ?, ?)", $where);

        }



        if($query) {

            //To execute multiple queries

            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            
        }
         else {

            $res=false;

        }
        
        if($res1 && $res){
            foreach ($res as $key => $value) {
                foreach ($res1 as $key1 => $value1) {
                    if($value->client_id == $value1->client_id){
                        $value->Commitments = $value1->Commitments;
                    }
                }
            }
            $flag=0;
            foreach ($res1 as $key1 => $value1) {
                $flag=0;
                foreach ($res as $key => $value) {
                    if($value->client_id == $value1->client_id){
                        $flag=1;
                        break;
                    }
                }
                if($flag==0)
                {
                    $res[]= $res1[$key1];
                }

            }
            return $res;
        }
        else if(!$res1 && $res){
            return $res;
        } 
        else if($res1 && !$res){
            return $res1;
        } 
        else {

            return false;

        }



        /*if($query) {

            $result = $query->result();

            //To execute multiple queries

            //$res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();



            $sql = $result[0]->sql;

            if(!empty($sql)) {

                return $sql;

                $q2 = $this->db->query($sql);

                if($q2) {

                    return $this->db->queries;

                    $res = $q2->result();

                    $q2->next_result();

                    $q2->free_result();

                } else {

                    $res = false;

                }

            } else {

                $res = false;

            }

        } else {

            $res = false;

        }



        return $res;*/

    }

   

    /* Functions for Ledger Report */

    /*******************************/

    function get_ledger_report_inflow($type, $where)
    {

        $query = "";

        if($type == 'client')

        {
        
            $query = $this->db->query("call sp_ledger_report_inflow_client(?, ?, ?, ?, ?)", $where);
            
        }

        else

        {

            $query = $this->db->query("call sp_ledger_report_inflow_family(?, ?, ?, ?, ?)", $where);

        }

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();
    
            return $res;

        } else {
        
            return false;

        }

    }

    function get_ledger_report_outflow($type, $where)
    {

        $query = "";

        if($type == 'client')

        {

            $query = $this->db->query("call sp_ledger_report_outflow_client(?, ?, ?, ?, ?)", $where);

        }

        else

        {

            $query = $this->db->query("call sp_ledger_report_outflow_family(?, ?, ?, ?, ?)", $where);

        }

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }


    function get_ledger_report_dividend($type, $where)
    {

        $query = "";

        if($type == 'client')

        {

            $query = $this->db->query("call sp_ledger_report_dividend_client(?, ?, ?, ?, ?)", $where);

        }

        else

        {

            $query = $this->db->query("call sp_ledger_report_dividend_family(?, ?, ?, ?)", $where);

        }

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }
    
    
    function import_mutual_fund_summary_report_client_wise($where)
    {

        $query = "";
        $query = $this->db->query("call sp_mutual_fund_summary_client_wise_import(?)", $where);

        if($query) {

            //To execute multiple queries

            $res = $query->result();

            // changes in system/database/drivers/mysqli/mysqli_result.php

            // added a new functon called next_result()

            $query->next_result();

            $query->free_result();

            return $res;

        } else {

            return false;

        }

    }
    
    

} 