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
    function userAuth($username, $pwd,$blnRedirect = true)
    {
        $this->db->select(' c.client_id, c.name, c.user_id, f.broker_id, c.client_type, c.head_of_family, c.family_id, c.username,u.EUIN,u.BSCUserId,u.BSCMemberId,u.BSCPassword');
        $this->db->from('clients c');
        $this->db->join('users u', 'u.id = c.user_id', 'inner');
        $this->db->join('families f', 'f.family_id = c.family_id', 'inner');
        $this->db->where('c.username', $username);
        $this->db->where('c.password', sha1($pwd));
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
            if($blnRedirect)
            {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Invalid username and/or password!</div>');
                redirect('client/Clients_users');
            }
            else
            {
                return false;
            }
        }
    }
    
   function get_client_detail($client_id)
    {
        
       $this->db->select('c.client_id, c.name, c.user_id, f.broker_id, c.client_type, c.head_of_family, c.family_id, c.username,u.EUIN,u.BSCUserId,u.BSCMemberId,u.BSCPassword');
        $this->db->from('clients c');
        $this->db->join('users u', 'u.id = c.user_id', 'inner');
        $this->db->join('families f', 'f.family_id = c.family_id', 'inner');
        $this->db->where('c.client_id', $client_id);
        $this->db->where('c.status', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }
    function get_all_family_member($family_id)
    {
        
        $this->db->select('client_id,name');
        $this->db->from('clients');
        $this->db->where('family_id', $family_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
    }
    function get_family_head_detail($family_id)
    {
        
        $this->db->select('client_id,name');
        $this->db->from('clients');
        $this->db->where('family_id', $family_id);
        $this->db->where('head_of_family', 1);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
        
        
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
