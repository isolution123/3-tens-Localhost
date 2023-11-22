<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Summary_report_data_model extends CI_Model {
    var $table = 'clients';
    var $column = array('id,client_id,insurance,fixed_income,mutual_funds,equity,real_estate,commodity,life_cover,added_on,broker_id');
    var $order = array('id' => 'desc');

    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }

    function add_Summary_report_data($data)
    {
        
        if(!($this->db->insert('summary_report_data', $data)))
      {

          return $this->db->error();

      } else {
          return $this->db->insert_id();
      }
    }

} 