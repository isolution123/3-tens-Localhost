<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //function for adminUsers/broker authentication
    function userAuth($username, $password)
    {
        $this->db->select('id, name, username, broker_id, permissions');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $this->db->where('status', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1)
        {
            $this->db->set('last_login', date('Y-m-d H:i:s'));
            $this->db->where('username', $username);
            $this->db->where('password', sha1($password));
            $this->db->update('users');
            //return $this->db->queries;
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    function get_limit($brokerID)
   {
       $this->db->select('user_limit');
       $this->db->from('users');
       $this->db->where('id',$brokerID);
       $query = $this->db->get();
       return $query->row();
   }

   function count_user($brokerID)
    {
      $this->db->select('count(id) as count');
      $this->db->from('users');
      $this->db->where('broker_id',$brokerID);
      $query = $this->db->get();
      return $query->row();
    }

    //function for admin authentication
    function adminAuth($username, $password, $type)
    {
        $this->db->select('id, name');
        if($type == "Admin")
        {
            $this->db->from('admins');
            $this->db->where('status', 1);
        }
        else
        {
            $this->db->from('super_admins');
        }
        $this->db->where('name', $username);
        $this->db->where('password', sha1($password));
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1)
        {
            $this->db->set('last_login', date('Y-m-d H:i:s'));
            $this->db->where('name', $username);
            $this->db->where('password', sha1($password));
            if($type == "Admin")
                $this->db->update('admins');
            else
                $this->db->update('super_admins');
            return $query->result();
        }
        else
        {
            return false;
        }
    }


    //Salmaan - 4/3/2016
    /*function get_users($condition) {
        $this->db->select('id, name, mobile, email_id, username, status, DATE_FORMAT(last_login,"%d/%m/%Y %h:%i:%s %p") as last_login, add_info, user_type, broker_id, admin_id, permissions');
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->order_by('id','asc');
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return $this->db->error();
        }
    }*/
    function get_users($condition) {
          $this->db->select('id, name, mobile, email_id, username, status, DATE_FORMAT(last_login,"%d/%m/%Y %h:%i:%s %p") as last_login, add_info, user_type, broker_id, admin_id, permissions,cams_rta_password,karvy_rta_password,mailback_mail'); //@ Pallavi 06-sep-2017
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->order_by('id','asc');
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return $this->db->error();
        }
    }

    public function add_user($data)
    {
        $this->db->set('id', "brokerID()", FALSE);
        if(!($this->db->insert('users', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }

    public function update_user($data, $where)
    {
        if(!($this->db->update('users', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['id'];
        }
    }

    public function delete_user($id)
    {
        if(!($this->db->delete('users', array('id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }

    public function get_admin_by_broker($condition) {
        $this->db->select('a.*');
        $this->db->from('admins a');
        $this->db->join('users u','u.admin_id = a.id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }
}
