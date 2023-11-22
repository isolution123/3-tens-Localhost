<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Insurance_companies_model extends CI_Model {
    var $table = 'ins_companies';
    var $column = array('ins_comp_id, ins_comp_name, broker_id');
    var $order = array('ins_comp_id' => 'desc');

    function __construct()
    {
        parent :: __construct();
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

    function get_ins_companies_list($condition)
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

    public function count_all($condition)
    {
        $this->db->from($this->table);
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function get_ins_comp_details($condition)
    {
        $this->db->select('ins_comp_id, ins_comp_name');
        $this->db->from($this->table);
        $this->db->where($condition);
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

    public function delete_ins_comp($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete($this->table))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_ins_companies_broker_dropdown($broker_id)
    {
        $this->db->select('ins_comp_name, ins_comp_id');
        $this->db->from($this->table);
        $this->db->where('broker_id = "'.$broker_id.'" or broker_id is null');
        $query = $this->db->get();
        return $query->result();
    }

    /*function check_companies($condition)
    {
        $this->db->select('ins_comp_name');
        $this->db->from($this->table);
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }*/
}