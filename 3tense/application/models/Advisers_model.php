<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Advisers_model extends CI_Model{
    var $column = array('adviser_id, adviser_name, company_name, product, agency_code, contact_person, contact_number, held_type');
    var $order = array('adviser_id' => 'desc');
    var $table = 'advisers';

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

    function get_adviser($brokerID)
    {
        $this->get_datatables_query();
        $this->db->where('broker_id = "'.$brokerID.'" or broker_id is null');
        if(isset($_POST['length']) && $_POST['start'])
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function add_adviser($data)
    {
        if(!($this->db->insert($this->table, $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function get_adviser_details($where = null)
    {
        $this->db->select('adviser_id, adviser_name, company_name, product, agency_code, contact_person, contact_number, held_type');
        $this->db->from($this->table);
        if($where)
            $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    public function update_adviser($where, $data)
    {
        if(!($this->db->update($this->table, $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function get_adviser_broker_dropdown($broker_id)
    {
        $this->db->select('adviser_id, adviser_name');
        $this->db->from($this->table);
        $this->db->where('broker_id = "'.$broker_id.'" or broker_id is null');
        $query = $this->db->get();
        return $query->result();
    }
} 