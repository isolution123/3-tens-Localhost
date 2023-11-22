<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tradings_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // dropdown data functions
    function get_trading_brokers_dropdown($condition='1')
    {
        $this->db->select('trading_broker_id, trading_broker_name');
        $this->db->from('trading_brokers');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_client_codes_dropdown($condition = '1')
    {
        $this->db->select('client_code');
        $this->db->from('client_brokers');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }


    // function to get list of brokers
    function get_trading_brokers($condition) {
        $this->db->select('trading_broker_id,trading_broker_name,broker_id');
        $this->db->from('trading_brokers');
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

    /* Trading broker functions START */
    public function add_trading_broker($data)
    {
        if(!($this->db->insert('trading_brokers', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_trading_broker($data, $where)
    {
        if(!($this->db->update('trading_brokers', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['trading_broker_id'];
        }
    }

    public function delete_trading_broker($id)
    {
        if(!($this->db->delete('trading_brokers', array('trading_broker_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Trading broker functions END */

    /* below functions for Cash Balance import */
    public function get_share_negative()
    {
        $this->db->select('share_negative');
        $this->db->where('broker_id = "'.$this->session->userdata('broker_id').'"');
        $query = $this->db->get('reminder_days');
        return $query->row();
    }

    public function check_client_broker_exists($condition)
    {
        $this->db->select('*');
        $this->db->from('client_brokers cb');
        $this->db->join('users u','u.id = cb.user_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_client_brokers($data,$condition)
    {
        /*if(!($this->db->update('client_brokers cb', $data, $condition))) {
            return $this->db->queries;
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }*/
        if(!($this->db->query('UPDATE client_brokers cb INNER JOIN users u ON u.id = cb.user_id SET `balance`='.$this->db->escape($data['balance']).' WHERE '. $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }
}
