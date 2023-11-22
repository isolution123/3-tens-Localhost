<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class AppUsage_model extends CI_Model 
{
    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }
    
    function getAppUsageData($condition)
    {
        $this->db->select("log.log_id,client.name,client.email_id,client.client_id,log.operation,log.ip_address,log.created_datetime");
        $this->db->from('api_logs as log');
        $this->db->join('clients as client', 'log.user_id = client.client_id', 'inner');
        $this->db->where($condition);
        $this->db->order_by('log_id desc');
        $query = $this->db->get();
        return $query->result();
    }

}
