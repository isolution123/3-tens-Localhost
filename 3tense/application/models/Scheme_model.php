<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scheme_model extends CI_Model
{
    var $column = array('scheme_id','scheme_name','scheme_status','scheme_type_id','prod_code','isin','other','other2');
    var $order = array('scheme_id' => 'desc');
    var $tbl_name = 'mutual_fund_schemes';
    
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function getMatchingScheme($condition)
    {
        $this->db->select($this->column);
        $this->db->from($this->tbl_name);
        $this->db->where($condition);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        //echo '<pre>';print_r($query);die;
        return $query->result();
    }
    
    public function addScheme($data)
    {
        $result = $this->db->insert($this->tbl_name,$data);
        
        if($this->db->affected_rows() > 0 ) 
        {
            $arrReturn['result'] = true;
            $arrReturn['id'] = $this->db->insert_id();
        }
        else
        {
            $arrReturn['result'] = false;
        }
        
        return $arrReturn;
    } 
    
    public function addSchemeISIN($data)
    {
        $result = $this->db->insert('mutual_fund_schemes_isin',$data);
        return $result;
    } 
}
    