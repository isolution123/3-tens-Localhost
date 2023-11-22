<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Funds_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    //Add Funds
    var $af_table = 'add_funds as af';
    var $af_column = array("af.add_fund_id, af.client_id, c.name as client_name, c.family_id, fam.name as family_name,
     af.bank_account_id, b.bank_name, ba.branch, ba.account_number, af.trading_broker_id, tb.trading_broker_name, af.client_code,
     Date_Format(af.transaction_date, '%d/%m/%Y') as transaction_date, af.amount, af.cheque_no,
     Date_Format(af.cheque_date, '%d/%m/%Y') as cheque_date, af.shares_app, af.add_notes, af.broker_id");
    var $af_order = array('af.transaction_date' => 'desc');

    //Withdraw Funds
    var $wf_table = 'withdraw_funds as wf';
    var $wf_column = array("wf.withdraw_fund_id, wf.client_id, c.name as client_name, c.family_id, fam.name as family_name,
     wf.bank_account_id, b.bank_name, ba.branch, ba.account_number, wf.trading_broker_id, tb.trading_broker_name, wf.client_code,
     Date_Format(wf.transaction_date, '%d/%m/%Y') as transaction_date, wf.amount, wf.cheque_no,
     Date_Format(wf.cheque_date, '%d/%m/%Y') as cheque_date, wf.withdraw_from, wf.mf_type, wf.add_notes, wf.broker_id");
    var $wf_order = array('wf.transaction_date' => 'desc');

    //Add Funds
    private function get_af_datatables_query()
    {
        $this->db->select($this->af_column);
        $this->db->from($this->af_table);
        $this->db->join('clients as c', 'af.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('bank_accounts as ba', 'ba.account_id = af.bank_account_id', 'left');
        $this->db->join('banks as b', 'b.bank_id = ba.bank_id', 'left');
        $this->db->join('trading_brokers as tb', 'af.trading_broker_id = tb.trading_broker_id', 'left');

        $i=0;
        foreach($this->af_column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) :
                    $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->af_order))
        {
            $order = $this->af_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    //Withdraw Funds
    private function get_wf_datatables_query()
    {
        $this->db->select($this->wf_column);
        $this->db->from($this->wf_table);
        $this->db->join('clients as c', 'wf.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('bank_accounts as ba', 'ba.account_id = wf.bank_account_id', 'left');
        $this->db->join('banks as b', 'b.bank_id = ba.bank_id', 'left');
        $this->db->join('trading_brokers as tb', 'wf.trading_broker_id = tb.trading_broker_id', 'left');

        $i=0;
        foreach($this->wf_column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) :
                    $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->wf_order))
        {
            $order = $this->wf_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    /* Add_funds Functions Start */
    ////Get Add Funds data
    function get_add_funds($condition)
    {
        $this->get_af_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

        /* Add_funds Functions Start */
    ////Get only equity data
    function get_add_funds_new($condition)
    {
        $this->get_af_datatables_query();
        $this->db->where($condition);
        $this->db->order_by("transaction_date desc");
        $this->db->limit(5);
        $query = $this->db->get();
        //return $query = $this->db->queries[0];
        return $query->result();
    }

    //function to add funds
    function add_funds($data)
    {
        if(!($this->db->insert('add_funds', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    //function to update add funds data
    function update_add_funds($data, $where)
    {
        if(!($this->db->update('add_funds', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['add_fund_id'];
        }
    }

    function delete_add_fund($condition)
    {
        $this->db->where($condition);
        $this->db->delete('add_funds');
        return true;
    }
    /* Add_funds Functions End */

    /* Withdraw_funds Functions Start */
    ////Get Withdraw Funds data
    function get_withdraw_funds($condition)
    {
        $this->get_wf_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    ////Get Withdraw Funds data equity only
    function get_withdraw_funds_equity($condition)
    {
        $this->get_wf_datatables_query();
        $this->db->where($condition);
        $this->db->order_by("transaction_date desc");
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();
    }


    //function to withdraw funds
    function withdraw_funds($data)
    {
        if(!($this->db->insert('withdraw_funds', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    //function to update Withdraw funds data
    function update_withdraw_funds($data, $where)
    {
        if(!($this->db->update('withdraw_funds', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['withdraw_fund_id'];
        }
    }

    function delete_withdraw_fund($condition)
    {
        $this->db->where($condition);
        $this->db->delete('withdraw_funds');
        return true;
    }
    /* Withdraw_funds Functions End */

}
