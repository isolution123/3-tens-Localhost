<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Banks_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // function to get list of all banks / only enabled banks
    /*function get_banks($all=false) {
        if($all) {
            // get list of ALL the banks, even the disabled ones
            $this->db->select('bank_id,bank_name,status,broker_id');
            $this->db->from('banks');
        } else {
            // get list of enabled/available banks
            $this->db->select('bank_id,bank_name,broker_id');
            $this->db->from('banks');
            $this->db->where('status=1');
        }
        $query = $this->db->get();
        return $query->result();

    }*/
    function get_banks($condition) {
        $this->db->select('bank_id,bank_name,status,broker_id');
        $this->db->from('banks');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_banks_of_client($condition) {
        $this->db->select('b.bank_id, bank_name, status, broker_id');
        $this->db->from('banks as b');
        $this->db->join('bank_accounts as acc', 'acc.bank_id = b.bank_id', 'inner');
        $this->db->where($condition);
        $this->db->group_by('b.bank_id');
        $query = $this->db->get();
        return $query->result();
    }

    function get_branches($condition)
    {
        $this->db->distinct();
        $this->db->select('branch');
        $this->db->from('bank_accounts');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_accounts($condition)
    {
        $this->db->select('account_number');
        $this->db->from('bank_accounts');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
    
    
    function get_bank_account_types($condition) {
        $this->db->select('account_type_id,account_type_name,broker_id');
        $this->db->from('bank_account_types');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
    
    /* function to check duplicacy of values */
    function check_duplicate($tableName, $condition) {
        $this->db->select('*');
        $this->db->from($tableName);
        $this->db->where($condition);
        //$this->db->where(' OR broker_id is null');
        $query = $this->db->get();
        //$var = $this->db->queries;
        //echo $var[0];
        return $query->result();
    }

    /* Bank functions START */
    public function add_bank($data)
    {
        if(!($this->db->insert('banks', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_bank($data, $where)
    {
        if(!($this->db->update('banks', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['bank_id'];
        }
    }

    public function delete_bank($id)
    {
        if(!($this->db->delete('banks', array('bank_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Bank functions END */
} 