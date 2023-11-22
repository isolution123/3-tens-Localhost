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
       $query_client= $this->db->query("call sp_Client_dashboard_data(?, ?)", array('clientID' => $clientId, 'brokerID' => $brokerID));
       if($query_client)
       {
           //To execute multiple queries
           $res = $query_client->row_array();
           // changes in system/database/drivers/mysqli/mysqli_result.php
           // added a new functon called next_result()
           $query_client->next_result();
           $query_client->free_result();
           //var_dump($res);
           return $res;
        }
         else
          {
           return $this->db->error();
          }


   }

   public function getTotalPortFolioModel($family_id,$brokerID)
   {
     $client_list=$this->db->query("call Sp_Total_Portfoilo_family('{$family_id}','{$brokerID}')");
     if($client_list)
     {
         //To execute multiple queries
         $res = $client_list->result_array();
         // changes in system/database/drivers/mysqli/mysqli_result.php
         // added a new functon called next_result()
         $client_list->next_result();
         $client_list->free_result();
         return $res;
      }
       else
        {
         return $this->db->error();
        }
   }

   public function get_summary_Dashboard_HOF($family_id,$brokerID,$client_id)
   {
     $query_HOF = $this->db->query("call sp_HOF_dashboard_data(?,?,?)", array('familyID' => $family_id, 'brokerID' => $brokerID, 'clientID' => $client_id));
     if($query_HOF)
     {
         //To execute multiple queries
         //var_dump($query_HOF->result());
         $res = $query_HOF->row_array();
         // changes in system/database/drivers/mysqli/mysqli_result.php
         // added a new functon called next_result()
         $query_HOF->next_result();
         $query_HOF->free_result();
         //var_dump($res);
         return $res;
      }
       else
        {
         return $this->db->error();
        }
   }



}
