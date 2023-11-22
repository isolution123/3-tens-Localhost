<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Fixed_deposits_model extends CI_Model{
    var $table = 'fd_transactions as fdt';
    var $column = array("fd_transaction_id, fdt.client_id, c.name as client_name, c.family_id, fam.name as family_name, interest_mode,
    Date_Format(transaction_date, '%d/%m/%Y') as transaction_date, fdt.fd_inv_id, fd_inv_type, fdt.fd_comp_id, fd_comp_name,
    fd_method, ref_number, date_format(issued_date, '%d/%m/%Y') as issued_date, amount_invested, interest_rate, maturity_date as ogi_mat_date,
    Date_Format(maturity_date, '%d/%m/%Y') as maturity_date,maturity_amount,
    nom.name as nominee, nominee as nominee_id, fdt.status, adv_id, adviser_name, adjustment, maturity_payout_id, adjustment_flag,
    payout_mode, inv_bank_id, inv_account_number, inv_cheque_number, date_format(inv_cheque_date, '%d/%m/%Y') as inv_cheque_date, inv_amount,
    maturity_bank_id, maturity_account_number, maturity_payout_id, adjustment_ref_number, int_round_off, fdt.broker_id");
    var $order = array('fdt.transaction_date' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'fdt.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('clients as nom', 'fdt.nominee = nom.client_id', 'inner');
        $this->db->join('fd_investment_types as fdi', 'fdt.fd_inv_id = fdi.fd_inv_id', 'inner');
        $this->db->join('fd_companies as fdc', 'fdt.fd_comp_id = fdc.fd_comp_id', 'inner');
        $this->db->join('advisers as adv', 'fdt.adv_id = adv.adviser_id', 'inner');
        $this->db->join('fd_payout_modes as fpm', 'fdt.maturity_payout_id = fpm.payout_mode_id', 'inner');

        $i=0;
        foreach($this->column  as $col_item)
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
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
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
        $this->db->join('clients as c', 'fdt.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('clients as nom', 'fdt.nominee = nom.client_id', 'inner');
        $this->db->join('fd_investment_types as fdi', 'fdt.fd_inv_id = fdi.fd_inv_id', 'inner');
        $this->db->join('fd_companies as fdc', 'fdt.fd_comp_id = fdc.fd_comp_id', 'inner');
        $this->db->join('advisers as adv', 'fdt.adv_id = adv.adviser_id', 'inner');
        $this->db->join('fd_payout_modes as fpm', 'fdt.maturity_payout_id = fpm.payout_mode_id', 'inner');
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    ////Fixed Deposit
    function get_fixed_deposit($condition)
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

        ////Fixed Deposit
    function get_fixed_deposit_top($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        //$this->db->order_by("fdt.transaction_date desc");
        $this->db->limit(5);
        $query = $this->db->get();
        //return  $this->db->queries[0];
        return $query->result();
    }


    ////Fixed Deposit
  function get_fixed_deposit_mat($condition)
  {
      $this->get_datatables_query();
      $this->db->where($condition);
      $this->db->order_by("fdt.maturity_date asc");
      $this->db->limit(5);
      $query = $this->db->get();
      //return  $this->db->queries[0];
      return $query->result();
  }



//top 5 Fixed Deposit interest
    function get_fixed_deposit_int($condition)
    {
        $this->db->select('c.name,fdi.interest_date,fdc.fd_comp_name,fdt.ref_number,fdi.interest_amount');
        $this->db->from('fd_transactions as fdt');
        $this->db->join('fd_companies as fdc', 'fdt.fd_comp_id = fdc.fd_comp_id', 'inner');
        $this->db->join('clients as c', 'fdt.client_id = c.client_id', 'inner');
        $this->db->join('fd_interests as fdi', 'fdi.fd_transaction_id =fdt.fd_transaction_id', 'inner');
        $this->db->where($condition);
        $this->db->order_by("fdi.interest_date asc");
        $this->db->limit(5);
        $query = $this->db->get();
        //return  $this->db->queries;
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

    function add_fixed_deposit($data)
    {
        if(!($this->db->insert('fd_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_fixed_deposit($data, $condition, $escape = true)
    {
        if(!($this->db->update('fd_transactions', $data, $condition, $escape))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function update_fixed_deposit_batch($data, $key)
    {
        if(!($this->db->update_batch('fd_transactions', $data, $key))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }

    function delete_fixed_deposit($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete('fd_transactions', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function add_fd_interest($data)
    {
        if(!($this->db->insert('fd_interests', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function delete_fd_interest($condition)
    {
        $error = 0;
        $this->db->where($condition);
        if(!($this->db->delete('fd_interests', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_interest_details($conditions)
    {
        $this->db->select("sum(fi.interest_amount) as 'interest_amount'");
        $this->db->from('fd_interests as fi');
        $this->db->join('fd_transactions as fd', 'fi.fd_transaction_id = fd.fd_transaction_id', 'inner');
        $this->db->where($conditions);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;

        }
    }

    function get_fd_interest($condition, $order='fi.interest_id asc') {
        $this->db->select("*");
        $this->db->from('fd_interests as fi');
        $this->db->join('fd_transactions as fd', 'fi.fd_transaction_id = fd.fd_transaction_id', 'inner');
        $this->db->where($condition);
        $this->db->order_by($order);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    ////Payout Mode
    function get_payout_mode_broker_dropdown()
    {
        $this->db->select('payout_mode_id, payout_mode');
        $this->db->from('fd_payout_modes');
        $query = $this->db->get();
        return $query->result();
    }

    ////Function for Fixed Deposit Related Reports
    function get_fixed_deposit_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_fd_by_client_report(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_fd_by_family_report(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }

    //Report for premium calender
    function get_interest_calendar_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_interest_calender_client (?, ?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_interest_calender_family (?, ?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        return $res;
    }
}
