<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Demat_accounts_model extends CI_Model{
    var $column = array('id','demat_accounts.provider_id','demat_providers.demat_provider','client_id','type_of_account','demat_id','account_number');
    var $order = array('id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // function to get info of a single demat account
    function get_demat_account($accountID) {
        // get info of specific demat account of a client, by its id
        $this->db->select($this->column);
        $this->db->from('demat_accounts');
        $this->db->join('demat_providers','demat_accounts.provider_id = demat_providers.provider_id','inner');
        $this->db->where('account_id',$accountID);
        $query = $this->db->get();
        return $query->row();
    }

    function check_duplicate($tableName, $condition) {
        $this->db->select('*');
        $this->db->from($tableName);
        $this->db->join('clients','demat_accounts.client_id = clients.client_id','inner');      
        $this->db->where($condition);
        //$this->db->where(' OR broker_id is null');
        $query = $this->db->get();
        //$var = $this->db->queries;
        //echo $var[0];
        return $query->result();
    }

    /* below 3 functions for Client Demat Account details */
    function get_client_demat_accounts($clientID) {
        $this->db->select($this->column);
        $this->db->from('demat_accounts');
        $this->db->join('demat_providers','demat_accounts.provider_id = demat_providers.provider_id','inner');
        $this->db->where('client_id', $clientID);
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

    function demat_accounts_count_filtered($clientID) {
        $this->db->select($this->column);
        $this->db->from('demat_accounts');
        $this->db->join('demat_providers','demat_accounts.provider_id = demat_providers.provider_id','inner');
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

    public function demat_accounts_count_all($clientID)
    {
        $this->db->from('demat_accounts');
        $this->db->join('demat_providers','demat_accounts.provider_id = demat_providers.provider_id','inner');
        $this->db->where('client_id', $clientID);
        return $this->db->count_all_results();
    }

    public function add_demat_account($data)
    {
        $this->db->insert('demat_accounts',$data);
        return true;
    }

    function update_demat_account($data, $accountID)
    {
        $this->db->where('account_id', $accountID);
        $this->db->update('demat_accounts', $data);
        return true;
    }

    function delete_demat_account($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('demat_accounts');
        return true;
    }

}