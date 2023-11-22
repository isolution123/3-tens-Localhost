<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Premium_types_model extends CI_Model{
    var $table = 'premium_types';
    var $column = array('prem_type_id', 'prem_type_name', 'broker_id');
    var $order = array('prem_type_id' => 'desc');

    function __construct()
    {
        $this->load->database();
    }

    function get_datatable_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
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

    function get_prem_types($condition)
    {
        $this->get_datatable_query();
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->get_datatable_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($condition)
    {
        $this->db->from($this->table);
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function get_prem_type_details($where = null)
    {
        $this->db->select('prem_type_id, prem_type_name');
        $this->db->from($this->table);
        if($where)
            $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
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

    function get_prem_type_broker_dropdown($broker_id)
    {
        $this->db->select('prem_type_id, prem_type_name');
        $this->db->from($this->table);
        $this->db->where('broker_id = "'.$broker_id.'" or broker_id IS NULL');
        $query = $this->db->get();
        return $query->result();
    }
} 