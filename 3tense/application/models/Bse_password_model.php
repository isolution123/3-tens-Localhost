<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bse_password_model extends CI_Model{
    var $column = array('BSCPassword');
    // var $order = array('adviser_id' => 'desc');
    var $table = 'users';

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

    function get_bse_password($brokerID,$userID)
    {
        $this->get_datatables_query();
        $this->db->where('broker_id = "'.$brokerID.'"');
        $this->db->where('id = "'.$userID.'"');
        if(isset($_POST['length']) && $_POST['start'])
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->row();
    }

    public function edit_bse_password($brokerId, $data)
    {
        $this->db->where('id',$brokerId);
        $this->db->or_where('broker_id',$brokerId);
        if(!($this->db->update($this->table, $data))) {
            $error = $this->db->error();
            return false;
        } else {
            return true;
        }
    }
} 