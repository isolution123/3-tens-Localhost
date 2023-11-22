<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Model for Insurances, Insurance Policies, Maturity and Premium
class Dashboard_models extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_summary_Dashboard_client($clientId,$brokerID)
  {
       $query_client= $this->db->query("call sp_Client_dashboard_data('{$clientId}','{$brokerID}')");
       return $query_client->row();
   }

   public function getTotalPortFolioModel($family_id,$brokerID)
   {
     $query_HOF=$this->db->query("call Sp_Total_Portfoilo_family('{$family_id}','{$brokerID}')");
      return $query_HOF->result_array();
   }

   public function get_summary_Dashboard_HOF($family_id,$brokerID,$client_id)
   {
      $query_HOF=$this->db->query("call sp_HOF_dashboard_data('{$family_id}','{$brokerID}','{$client_id}' )");
     return $query_HOF->row();
   }

}
