<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Policies_model extends CI_Model{
    var $table = 'policies';
    var $column = array('policy_id', 'policy_name');
    var $order = array('policy_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_database_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->where('status', 1);

        $i=0;
        foreach($this->column as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i==0) ? $this->db->like($col_item, $_POST['search']['value']):$this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->order))
        {
            $order=$this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_policy()
    {
        $this->get_database_query();
        if(isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->get_database_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
} 