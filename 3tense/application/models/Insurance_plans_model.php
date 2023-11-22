<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Insurance_plans_model extends CI_Model{

    var $table = 'ins_plans';
    var $column = array(
        'ins_plans.plan_id, ins_plans.plan_name, ins_plans.grace_period, (ins_companies.ins_comp_id) as comp_id,
        (ins_companies.ins_comp_name) as ins_companies, ins_plans.plan_type_id,
        (ins_plan_types.plan_type_name) as plan_type, ins_plans.annual_cumm_one, ins_plans.annual_cumm,
        ins_plans.return_cumm');
    var $order = array('ins_plans.broker_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('ins_companies', 'ins_plans.ins_comp_id = ins_companies.ins_comp_id', 'inner');
        $this->db->join('ins_plan_types', 'ins_plans.plan_type_id = ins_plan_types.plan_type_id', 'inner');

        $i=0;
        foreach($this->column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_ins_plans_broker($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($brokerID)
    {
        $this->db->from($this->table);
        $this->db->where('ins_plans.broker_id', $brokerID);
        return $this->db->count_all_results();
    }

    public function add($data)
    {
        if(!($this->db->insert($this->table, $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function get_ins_plan_by_condition($where_con)
    {
        $this->db->select('plan_id, plan_name, grace_period, ins_comp_id, plan_type_id, annual_cumm_one, annual_cumm, return_cumm, policy_id');
        $this->db->from('ins_plans');
        $this->db->where($where_con);
        $query = $this->db->get();
        return $query->row();
    }

    public function update($where, $data)
    {
        if(!($this->db->update($this->table, $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    public function delete($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete($this->table))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_ins_plans_broker_dropdown($condition)
    {
        $this->db->select('plan_name, plan_id');
        $this->db->from('ins_plans');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    ////Insurance Types
    function get_ins_plan_types()
    {
        $this->db->select('plan_type_id, plan_type_name');
        $this->db->from('ins_plan_types');
        $query = $this->db->get();
        return $query->result();
    }
} 