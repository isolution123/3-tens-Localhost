<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Fd_investment_types_model extends CI_Model{
    var $column = array('fd_inv_id, fd_inv_type, broker_id');
    var $order = array('fd_inv_id' => 'desc');
    var $table = 'fd_investment_types';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->from($this->table);
        $i=0;
        foreach($this->column as $col_item)
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

    function get_fd_inv_types($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['start'])
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function add_comp($data)
    {
        if(!($this->db->insert($this->table, $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function get_fd_inv_by_id($condition)
    {
        $this->db->select('fd_inv_id, fd_inv_type');
        $this->db->from($this->table);
        $this->db->where($condition);
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

    public function delete_fd_comp($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete($this->table))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_fd_inv_broker_dropdown($broker_id)
    {
        $this->db->select('fd_inv_type, fd_inv_id');
        $this->db->from($this->table);
        $this->db->where('broker_id = "'.$broker_id.'" or broker_id is null');
        $query = $this->db->get();
        return $query->result();
    }
} 