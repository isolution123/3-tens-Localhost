<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Bank_accounts_model extends CI_Model{
    var $column = array('account_id','b.bank_id','b.bank_name','client_id','bat.account_type_id','bat.account_type_name','branch','IFSC','account_number');
    var $order = array('account_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // function to get info of a single bank account
    function get_bank_account($accountID) {
        // get info of specific bank account of a client, by its account_id
        $this->db->select($this->column);
        $this->db->from('bank_accounts');
        $this->db->join('banks','banks.bank_id = bank_accounts.bank_id','inner');
        $this->db->where('account_id',$accountID);
        $query = $this->db->get();
        return $query->row();

    }

    /* below 3 functions for Client Bank Account details */
    function get_client_bank_accounts($condition) {
        $this->db->select($this->column);
        $this->db->from('bank_accounts ba');
        $this->db->join('banks b','b.bank_id = ba.bank_id','inner');
        $this->db->join('bank_account_types bat','bat.account_type_id = ba.account_type','inner');
        $this->db->where($condition);
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

        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function bank_accounts_count_filtered($clientID) {
        $this->db->select($this->column);
        $this->db->from('bank_accounts ba');
        $this->db->join('banks b','b.bank_id = ba.bank_id','inner');
        $this->db->join('bank_account_types bat','bat.account_type_id = ba.account_type','inner');
        $this->db->where('client_id', $clientID);
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

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function bank_accounts_count_all($clientID)
    {
        $this->db->from('bank_accounts');
        $this->db->join('banks','banks.bank_id = bank_accounts.bank_id','inner');
        $this->db->where('client_id', $clientID);
        return $this->db->count_all_results();
    }

    /* function to check duplicacy of values */
    function check_duplicate($tableName, $condition, $joinTable, $joinOn) {
        $this->db->select('*');
        $this->db->from($tableName);
        $this->db->join($joinTable, $joinOn, 'inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_bank_account($data)
    {
        $this->db->insert('bank_accounts',$data);
        return true;
    }

    function update_bank_account($data, $accountID)
    {
        $this->db->where('account_id', $accountID);
        $this->db->update('bank_accounts', $data);
        return true;
    }

    function delete_bank_account($accountID)
    {
        $this->db->where('account_id', $accountID);
        if($this->db->delete('bank_accounts')) {
            return true;
        } else {
            return false;
        }
    }

}