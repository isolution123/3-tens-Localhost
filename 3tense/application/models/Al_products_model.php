<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Al_products_model extends CI_Model{
    var $table = 'al_products';
    var $column = array('product_id, product_name, broker_id');
    var $order = array('product_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_database_query()
    {
        $this->db->from($this->table);
        $i = 0;
        foreach($this->column as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i == 0)? $this->db->like($col_item, $_POST['search']['value']): $this->db->or_like($col_item, $_POST['search']['value']);
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

    function get_products($condition)
    {
        $this->get_database_query();
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
        $this->get_database_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all($condition)
    {
        $this->db->from($this->table);
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    function get_product_details($condition)
    {
        $this->db->select('product_id, product_name');
        $this->db->from($this->table);
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    function add_product($data)
    {
        if(!($this->db->insert($this->table, $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_product($condition, $data)
    {
        if(!($this->db->update($this->table, $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    public function delete_product($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete($this->table))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_product_broker_dropdown($condition)
    {
        $this->db->select('product_id, product_name');
        $this->db->from($this->table);
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
} 