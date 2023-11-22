<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Demat_providers_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // function to get list of demat providers
    function get_demat_providers($condition) {
        $this->db->select('provider_id,demat_provider,broker_id');
        $this->db->from('demat_providers');
        $this->db->where($condition);
        $query = $this->db->get();
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

    /* Demat provider functions START */
    public function add_demat_provider($data)
    {
        if(!($this->db->insert('demat_providers', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_demat_provider($data, $where)
    {
        if(!($this->db->update('demat_providers', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['provider_id'];
        }
    }

    public function delete_demat_provider($id)
    {
        if(!($this->db->delete('demat_providers', array('provider_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Demat provider functions END */
} 