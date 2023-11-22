<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Broker_pdf_model extends CI_Model {
   

    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }

    function get_broker_info($ID)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $ID);
        $query = $this->db->get();
        return $query->row();
    }

}
