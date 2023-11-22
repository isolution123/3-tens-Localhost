<?php
//if(!defined('BASEPATH')) exit('No direct script access allowed');
class Doc_model extends CI_Model
  {
    function __construct()
    {
        $this->load->database();
    }
    function get_limit($client_id)
   {
       $this->db->select('Doc_upload_limit');
       $this->db->from('clients');
       $this->db->where('client_id',$client_id);
       $query = $this->db->get();
       return $query->row();
   }

    public function getDocs()
      {
          $this->db->select(' document_type_id,document_type');
          $this->db->from('document_types');
          $this->db->where('broker_id is null');
          $query = $this->db->get();
          // the query mean select cat_id,category from state
          foreach($query->result_array() as $row){
              $data[$row['document_type']]=$row['document_type'];
          }
          // the fetching data from database is return
          return $data;
      }
  }
