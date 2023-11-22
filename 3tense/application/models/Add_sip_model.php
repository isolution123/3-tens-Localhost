<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Model for Insurances, Insurance Policies, Maturity and Premium
class Add_sip_model extends CI_Model{
    var $table = 'insurances as ins';
    var $column = array(
        'ins.policy_num,fam.name, cli.client_id, (cli.name) as client_name, ins.plan_id, plan_name, ins.ins_comp_id, ins_comp_name,
        ins.plan_type_id, plan_type_name, paidup_date, maturity_date, amt_insured, commence_date, ins.mode, mode_name, prem_amt,
        prem_type as prem_type_id, prem_type_name, ins.prem_pay_mode_id, prem_pay_mode, next_prem_due_date, grace_due_date,
        ins.status as status_id, pstat.status, remarks, fund_value, prem_paid_till_date, mat_type, adv_id, adviser_name,
        nominee as nominee_id, nom.name as nominee, adjustment_flag, adjustment, fam.family_id,
        (select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_option1,
        (select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 1,1) as fund_option2');

    var $order = array('ins.commence_date' => 'desc');

    var $client_columns = array(
        'insurance_policies.ins_policy_id', 'ins_companies.ins_comp_name', 'insurance_policies.ins_comp_id', 'ins_plans.plan_name',
        'insurance_policies.plan_id', 'ins_plan_types.plan_type_name','ins_plan_types.plan_type_id', 'insurance_policies.policy_num'
    );
    var $client_order = array('insurance_policies.ins_policy_id' => 'desc');

    function __construct()
    {
        parent:: __construct();
        $this->load->database();
    }

    
    ////Insurance Start
    function get_insurances($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        //return $this->db->queries;
        return $query->result();
    }

    


    /* function to check duplicacy of brokers SIP Rate Of Return */
    function check_duplicate($tableName, $condition) {
        $this->db->select('*');
        $this->db->from($tableName);
        
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function add_sip_rate($data)
    {
        if(!($this->db->insert('sip_rate', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }
     function update_sip_rate($data, $where)
    {
        if(!($this->db->update('sip_rate', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function get_ins_policy_broker_dropdown($condition)
    {
        $this->db->select('ins_policy_id, policy_num');
        $this->db->from('insurance_policies');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
     function get_broker_sip_rate($condition)
     {
        $this->db->select('*');
        $this->db->from('sip_rate');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
         
     }
    function get_advisor_broker_dropdown($broker_id)
    {
        $this->db->select('adviser_id, adviser_name');
        $this->db->from('advisers');
        $this->db->where('broker_id',$broker_id);
        $this->db->or_where('broker_id',null);
        $query = $this->db->get();
        return $query->result();
    }

    
    

   

    function delete_insurance($condition)
    {
        $error = '0';
        if(!($this->db->delete('insurances', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }
    //Insurance End

    

    
}
