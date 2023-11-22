<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scheme_Type_model extends CI_Model {
    var $table = 'mf_scheme_types';
    var $column = array('*');
    var $order = array('scheme_type_id' => 'desc');

    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $i=0;
        foreach($this->column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_families_broker_dropdown($brokerID)
    {
        $this->db->select('family_id, name');
        $this->db->from($this->table);
        $this->db->where('broker_id',$brokerID);
        $this->db->where('status', 1);
        $this->db->order_by('name','asc');
        $query = $this->db->get();
        return $query->result();
    }

    
      function get_scheme_type()
    {
        $this->get_datatables_query();
        //$this->db->where('families.broker_id', $brokerID);
         if(isset($_POST['length']) && $_POST['length'] != -1)
         {
             $this->db->limit($_POST['length'], $_POST['start']);
         }
        
        
        $query = $this->db->get();
        return $query->result();

    }
    function add_default_family(){

          $data = array(
           // 'name' => $famName,
           //    'status' => 1,
           // 'broker_id' => $brokerID,
           // 'user_id' =>$brokerID                 //only broker is responsible to add default family initially so userid is same broker id
         );
          //$this->db->set('family_id', "familyID('".$brokerID."')", FALSE);
        //   if(!($this->db->insert($this->table, $data))) {
        //       $error = $this->db->error();
        // //     echo $this->db->last_query();
        //      return $error;
        //  } else {
         //     $fam_id= $this->db->insert_id();
           //   echo $fam_id;
        //}
          //repeat call get_families_broker function again for getting now default family info as it is added now
        //  $this->get_families_broker($brokerID,$name);
           $this->get_datatables_query();
           // $this->db->where('broker_id', $brokerID);
           // if($famName != "") {
           //     $this->db->where('name', $famName);
           // }
           if(isset($_POST['length']) && $_POST['length'] != -1)
           {
              $this->db->limit($_POST['length'], $_POST['start']);
          }
          $query1 = $this->db->get();
      //  echo $this->db->last_query();
           // $res1=$query->result_array();
          // $res= $res1;
           return $query1->result();

    }
  
//
    function count_filtered()
    {
        $this->get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
//
    public function count_all()
    {
        $this->db->from($this->table);
        //$this->db->where('broker_id', $brokerID);
        return $this->db->count_all_results();
    }
//
    public function get_scheme_type_by_id($id)
    {
        $this->db->select('scheme_type_id, scheme_type, scheme_target_value');
        $this->db->from($this->table);
        $this->db->where('scheme_type_id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function add($data)
    {
        $this->db->set('family_id', "familyID('".$data['broker_id']."')", FALSE);
        if(!($this->db->insert($this->table, $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update($where, $data)
    {
        if(!($this->db->update($this->table, $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    public function delete($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete($this->table))) {
            $error = $this->db->error();
        }
        return $error;
    }
} 