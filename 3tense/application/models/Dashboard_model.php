<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_insurance_dashboard($condition)
    {
        $this->db->select('s.status, count(policy_num) as sum_policy');
        $this->db->group_by('i.status');
        $this->db->where($condition);
        $this->db->from('insurances as i');
        $this->db->join('premium_status as s', 'i.status = s.status_id', 'inner');
        $query = $this->db->get();
        return $query->result();
    }
    
     function get_sip_book_dashboard($condition)
    {
        $this->db->select('*');
        $this->db->from('MonthlySIPBook as i');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_commodity_dashboard($condition)
    {
        $this->db->select('item_name, current_rate');
        $this->db->from('commodity_items ci');
        $this->db->where($condition);
        $this->db->join('commodity_rates as cr', 'ci.item_id = cr.item_id', 'inner');
        $this->db->join('commodity_units as cu', 'cr.unit_id = cu.unit_id', 'inner');
        $query = $this->db->get();
        return $query->result();
    }

    function get_top_investors_ins($condition)
    {
        $this->db->select('c.name, count(c.name) as num_ins');
        $this->db->from('insurances i');
        $this->db->where($condition);
        $this->db->join('clients as c', 'i.client_id = c.client_id', 'inner');
        $this->db->group_by('i.client_id');
        $this->db->order_by('num_ins', 'desc');
        $this->db->limit(3);
        $query = $this->db->get();
        return $query->result();
    }

    function get_top_investors_fd($condition)
    {
        $this->db->select('c.name, count(c.name) as num_fd');
        $this->db->from('fd_transactions fd');
        $this->db->where($condition);
        $this->db->join('clients as c', 'fd.client_id = c.client_id', 'inner');
        $this->db->group_by('fd.client_id');
        $this->db->order_by('num_fd', 'desc');
        $this->db->limit(3);
        $query = $this->db->get();
        return $query->result();
    }

    function get_top_mf($condition) {
        $this->db->select('mft.transaction_date, mfs.scheme_name, mft.folio_number, c.name');
        $this->db->from('mutual_fund_transactions mft');
        $this->db->join('mutual_fund_schemes mfs','mfs.scheme_id=mft.mutual_fund_scheme','inner');
        $this->db->join('clients c','c.client_id=mft.client_id','inner');
        $this->db->where($condition);
        $this->db->order_by('mft.transaction_date','desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }


    function get_pie_chart_data($brokerID)
     {
        $result=$this->db->query("call sp_dashboard_data('{$brokerID}') ");
        if($result)
        {
            //To execute multiple queries
            $res = $result->row_array();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $result->next_result();
            $result->free_result();
            return $res;
         }
          else
           {
            return $this->db->error();
           }
    }

}
