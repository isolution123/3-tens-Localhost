<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clientlogin_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
//ajinkya 6-12-2016
    //function for adminUsers/broker authentication
    function userAuth($username, $pwd)
    {
        $this->db->select('client_id, c.name, c.user_id, broker_id, client_type, head_of_family, c.family_id, username');
        $this->db->from('clients c');
        $this->db->join('families f', 'f.family_id = c.family_id', 'inner');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($pwd));
        $this->db->where('c.status', 1);
        // $this->db->where('port_access', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1)
        {
            $this->db->set('last_login', date('Y-m-d H:i:s'));
            $this->db->where('username', $username);
            $this->db->where('password', sha1($pwd));
            $this->db->update('clients');
            //return $this->db->queries;
            return $query->row();
        }
        else
        {
          $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Invalid username and/or password!</div>');
          redirect('client/Clients_users');
        }
    }


    function checkPassword($client_id,$oldpass)
    {
      $this->db->where('client_id',$client_id);
      $this->db->where('password',sha1($oldpass));
       $rs=$this->db->get('clients');
      return $rs->result();
    }
    function change($client_id,$newpass)
    {
      $value=array('password'=>sha1($newpass));
      $this->db->where('client_id',$client_id);
      if($this->db->update('clients',$value))
      {
          return true;
      }
      else{
          return false;
      }
    }
}
