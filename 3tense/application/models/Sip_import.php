<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Sip_import extends CI_Model{
  function __construct()
  {
      parent::__construct();
      $this->load->database();
  }

  function add_sip($data)
  {

        //print_r($this->db->insert('assets_transactions', $data));
        //$this->db->insert('demoassettrans', $data);
      if(!($this->db->insert('asset_transactions', $data)))
      {

          return $this->db->error();

      } else {
          return $this->db->insert_id();
      }
  }
   function add_sip_monthly_book($data)
  {

        //print_r($this->db->insert('assets_transactions', $data));
        //$this->db->insert('demoassettrans', $data);
      if(!($this->db->insert('MonthlySIPBook', $data)))
      {

          return $this->db->error();

      } else {
          return $this->db->insert_id();
      }
  }

  function add_sip_interest($data)
  {
      if(!($this->db->insert('asset_maturity', $data))) {
          $error = $this->db->error();
          return $error;
      } else {
          return $this->db->insert_id();
      }
  }


function get_sip_report($type,$where)
{

      $query="";
      if($type=='family')
      {
        $query = $this->db->query("call  sp_mf_sip(?,?,?)", $where);
        
      }
      else{
        $query=$this->db->query("call  sp_mf_sip(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
}

  function get_clientid($cell)
    {
        $this->db->select('client_id');
        $this->db->from('clients');
        $this->db->where('pan_no',$cell);
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_type()
      {
          $this->db->select('type_id');
          $this->db->from('al_types');
          $this->db->where('type_name','SIP');
          $query = $this->db->get();
          return $query->row();
      }
        function get_product_id()
        {
            $this->db->select('product_id');
            $this->db->from('al_products');
            $this->db->where('product_name','mutual fund');
            $query = $this->db->get();
            return $query->row();
        }
      function get_sip_rate($condition)
      {
          
         $this->db->select('rate');    
         $this->db->from('sip_rate');
         $this->db->where($condition);
         $query = $this->db->get();
         return $query->row();
                  
          
      }
      function check_duplicate_sip($condition)
        {
          $this->db->select('*');
          $this->db->from('asset_transactions');
          $this->db->where($condition);
          $query = $this->db->get();
         return $query->row();
         
        }
        function check_duplicate_sip_monthly_book($condition)
        {
          $this->db->select('*');
          $this->db->from('MonthlySIPBook');
          $this->db->where($condition);
          $query = $this->db->get();
         return $query->row();
         
        }
        function update_duplicate_sip($data, $where)
        {
             
           
              if(!($this->db->update('asset_transactions', $data,$where))) {
                $error = $this->db->error();
                return $error;
                //$this->db->last_query();
            } else {
                return $this->db->affected_rows();
             }
       }
        function update_duplicate_sip_montly_book($data, $where)
        {
             
           
              if(!($this->db->update('MonthlySIPBook', $data,$where))) {
                $error = $this->db->error();
                return $error;
                //$this->db->last_query();
            } else {
                return $this->db->affected_rows();
             }
       }
       function delete_asset_id($asset_id)
       {
           
           
            $this->db->where($asset_id);
            $this->db->delete('asset_maturity');
              return $this->db->last_query();
            
        }
      function get_bank_id($whereClient)
        {
            $this->db->select('bank_id');
            $this->db->from('banks');
            $this->db->where($whereClient);
            $query = $this->db->get();
            return $query->row();
        }


          function get_scheme_id($product_id)
            {
                $this->db->select('mutual_fund_schemes.scheme_id,mutual_fund_schemes.scheme_type_id,scheme_type');
                $this->db->from('mutual_fund_schemes');
                   $this->db->join('mf_scheme_types', 'mf_scheme_types.scheme_type_id = mutual_fund_schemes.scheme_type_id','inner');
                $this->db->where('prod_code',$product_id);
                $query = $this->db->get();
                return $query->row();
            }
            //SELECT scheme_id  FROM `mutual_fund_schemes` WHERE prod_code = '101ETGP'
            
            //Pallavi:@2017-06-27  //Manual intervention
    function add_auto_import_error_sip($data1){
    //echo"<pre>";print_r($data1);exit;
      $table='sip_auto_import_error_backup';
      $columns    = array();
          $values     = array();
          $upd_values = array();
          //foreach($data1 as $data){
            foreach ($data1 as $key => $val) {
                $columns[]    = $this->db->escape_identifiers($key);
                $val = $this->db->escape($val);
                $values[]     = $val;
                if($key!='client_id'){
                  $upd_values[] = $key.'='.$val;
                }
    
            }
            $sql = "INSERT INTO ". $this->db->dbprefix($table) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
             $this->db->query($sql);
             echo $this->db->last_query();
              unset($columns,$values,$upd_values);
        return true;
    
    }
    
    //@pallavi:2017-07-17 manual intervension  for email
    function get_all_sip_error_record($rta_type){
      $this->db->select('*');
      $this->db->from('sip_auto_import_error_backup');
      $this->db->where('email_status','0');
      $this->db->where('rta_type',$rta_type);
      $query=$this->db->get();
      if($query) {
        $result=$query->result_array();
      } else {
          $result = false;
      }
      return $result;
    }
    //@pallavi:2017-07-17 manual intervension  for update email status
    function update_sip_email_status($rta_type){
      $this->db->set('email_status','1');
      $this->db->where('email_status','0');
      $this->db->where('rta_type',$rta_type);
      $this->db->update('sip_auto_import_error_backup');
      return true;
    }

}

?>
