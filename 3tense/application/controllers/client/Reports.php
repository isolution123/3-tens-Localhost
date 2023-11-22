<?php
error_reporting(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reports extends CI_Controller{

    function __construct()
    {
        parent:: __construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Common_lib');
        $this->load->helper('url');
        $this->load->helper('date'); 
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Common_model', 'common');
        $this->load->model('Tradings_model', 'trading');
        $this->load->model('Funds_model', 'fund');
        $this->load->model('Reminders_model', 'reminder');
        $this->load->model('Equity_model', 'eq');
        $this->load->model('client/Dashboard_models', 'dash');
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Insurance_model', 'ins');

    }
    
    
    function get_mf_clientwise_report()
{
    
     if(isset( $_COOKIE['client_select_id_clientwiserpt']) && !empty($_COOKIE['client_select_id_clientwiserpt']))
    {
    
        $client_id=$_COOKIE['client_select_id_clientwiserpt'];
        setcookie("client_select_id_clientwiserpt", "", time() - 3600);
        
     
    }
    else
    {
      if($this->session->userdata('type')=='head')
      {
          $family_id = $this->session->userdata('family_id');
      }
      else
       {
         $client_id = $this->session->userdata('client_id');
     
       }
    }
    
         $brokerID = $this->session->userdata('user_id');
         $type = 'client';
        $where = "";
          if($client_id != null && $client_id != '')
          {
            $where = array(
                'familyID'=> '',
                'brokerID'=> $brokerID,
                'clientID'=> $client_id
              );
          }
          else
          {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'clientID'=> ''
            );
          }
          
        //unset($data['$mf_rep_data']);   
          $mf_rep_data=$this->mf->get_clientwise_details($type,$where);

          $mf_summary_client = $this->mf->get_mutual_fund_family_summary_portfolio($type,$where);
          $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where);
          $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where);
          $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where);

          //print_r($mf_rep_data);
          //echo "Dipak";
          if(!empty($mf_rep_data))
          {
              // unset($_SESSION['ins_report']);
              if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } else {
                  $logo = "";
              }
              $status = true;
              $data['page_title']='Mutual Fund Clientwise Detail report';
              $data['mf_rep_data'] = $mf_rep_data;
              $data['net_inv_data']=$net_inv;
              $data['mf_summary_typewise']=$mf_summary_typewise;
              $data['mf_comman_scheme_summary']=$mf_comman_scheme_summary;
              $data['mf_summary_net_investment']=$mf_summary_net_investment;
              $data['logo'] = $logo;
              $header['title']='View Mutual Fund Clientwise Detail report';
              $header['js'] = array('assets/users/js/common.js');
              $this->load->view('client/common/header-report', $header);
              
              
              
              if($type=='client')
              {
                   $this->load->view('client/report/mf_clientwise_detail_client',$data);
                }
              else
              {
                 $this->load->view('client/report/mf_clientwise_detail_family', $data);
            }
          }
          else
          {
            echo"No Data Found";
          }
}
function set_current_client_id_into_session()
{
    
            $sess_user['client_id_2']=isset($_GET['id'])?$_GET['id'] : $_POST['id'];;
            
             $this->session->set_userdata($sess_user);
          //   redirect ('client/dashboard');
          get_3d_report();

}
function get_3d_report()
{
    
    if(isset( $_COOKIE['client_select_id']) && !empty($_COOKIE['client_select_id']))
    {
    
        $client_id=$_COOKIE['client_select_id'];
        setcookie("client_select_id", "", time() - 3600);
        
     
    }
    else
    {
      if($this->session->userdata('type')=='head')
      {
          $family_id = $this->session->userdata('family_id');
          
      }
      else
       {
         $client_id = $this->session->userdata('client_id');
         
       }
    }
    
       $brokerID = $this->session->userdata('user_id');
        $type = 'client';
        $where = "";
        $reportDate=date('Y/m/d');
          if($client_id != null && $client_id != '')
          {
          
                $where = array(
                    'clientID'=> $client_id,
                    'brokerID'=> $brokerID,
                    

                );
                $where1 = array(
                    'familyID'=> '',
                    'brokerID'=> $brokerID,
                    'clientID'=> $client_id
                );
                $where11 = array(
                    'familyID'=> '',
                    'brokerID'=> $brokerID,
                    'clientID'=> $client_id,
                    'reportDate'=>$reportDate
                );
              
          }
          else
          {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
                
            );
             $where1 = array(
                    'familyID'=>  $family_id,
                    'brokerID'=> $brokerID,
                    'clientID'=>''
                );
                $where11 = array(
                    'familyID'=>  $family_id,
                    'brokerID'=> $brokerID,
                    'clientID'=> '',
                    'reportDate'=>$reportDate
                );
          }
            $net_inv = $this->mf->get_net_investment($type, $where);
            
            $inv_summary = $this->mf->get_investment_summary($type, $where);
            
            
            
            $curr_val_summary = $this->mf->get_current_value_summary($type, $where);
           // $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
              $client_wise_summary=$this->mf->get_clientwise_summary($type,$where1);
              
            
            $mf_comman_cap_detail_1 = $this->mf->mf_comman_cap_detail_1($type,$where1);
            $mf_comman_cap_detail_2 = $mf_comman_cap_detail_1;
            
            $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where1);
          
              //Comman Net Ivestment(Purchase ,Redemption,Dividend Payout,Net Investment)
                $mf_tran_amount_date = $this->mf->get_mutual_funds_trancation_amount($type,$where11);
                $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where1);
                foreach($mf_tran_amount_date as $s2) 
                {
	                $values[] = $s2->amount;
	                //$dates[] = DateTime::createFromFormat("Y-m-d",$s2->purchase_date)->format("m-d-Y");
	                $temp = DateTime::createFromFormat("Y-m-d",$s2->purchase_date);
	                  $date_stamps[] = $temp;
	                  //->getTimestamp();
	            }
	            
	            //$reportDate=date('Y-m-d', strtotime('-1 days', strtotime($reportDate)));
	            $temp = DateTime::createFromFormat("Y/m/d",$reportDate);
	            $date_stamps[] = $temp;
	            //->getTimestamp();
	            $values[] =round($mf_summary_net_investment[0]->current_value);

	            //load the excel library
                $this->load->library('Excel');
                $objPHPExcel = new PHPExcel_Calculation_Financial();
                
                //print_r($values);
                //print_r($date_stamps);die();
        
                
                $mf_summary_net_investment[0]->xirr_value=round(($objPHPExcel->XIRR($values,$date_stamps,0.1)*100),3);
             
            
            $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where1);
            $mf_summary_detail_for_chart = $this->mf->mf_summary_detail($type,$where1);

          //print_r($mf_rep_data);
          //echo "Dipak";
          if(!empty($client_wise_summary))
          {
              // unset($_SESSION['ins_report']);
              if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } else {
                  $logo = "";
              }
              $status = true;
              $data['page_title']='Mutual Fund 3D report';
              $data['mf_rep_data'] = $client_wise_summary;
              $data['mf_summary_client']=$mf_summary_client;
              $data['mf_summary_typewise']=$mf_summary_typewise;
              
              
              $data['mf_comman_scheme_summary']=$mf_comman_scheme_summary;
              $data['mf_summary_net_investment']=$mf_summary_net_investment;
              $data['mf_comman_cap_detail_1']=$mf_comman_cap_detail_1;
              $data['mf_comman_cap_detail_2']=$mf_comman_cap_detail_2;
              $data['mf_summary_detail_for_chart']=$mf_summary_detail_for_chart;
              
              $data['mf_summary_net_investment']=$mf_summary_net_investment;
              
              
                                
              $data['logo'] = $logo;
              $header['title']='View Mutual Fund 3D report';
              $header['js'] = array('assets/users/js/common.js');
              $this->load->view('client/common/header-report', $header);
              if($type=='client')
              {
                   $this->load->view('client/report/mf_report_clientwise_detail_client',$data);
              }
              else
              {
                 $this->load->view('client/report/mf_report_clientwise_detail_family', $data);
              }
          }
          else
          {
            echo"No Data Found";
          }
}

function get_group_typewise_report()
{
    
    if(isset( $_COOKIE['client_select_id']) && !empty($_COOKIE['client_select_id']))
    {
    
        $client_id=$_COOKIE['client_select_id'];
        setcookie("client_select_id", "", time() - 3600);
        
     
    }
    else
    {
      if($this->session->userdata('type')=='head')
      {
          $family_id = $this->session->userdata('family_id');
          
      }
      else
       {
         $client_id = $this->session->userdata('client_id');
         
       }
    }
    
       $brokerID = $this->session->userdata('user_id');
        $type = 'client';
        $where = "";
        $reportDate=date('Y/m/d');
          if($client_id != null && $client_id != '')
          {
          
                $where = array(
                    'clientID'=> $client_id,
                    'brokerID'=> $brokerID,
                    

                );
                $where1 = array(
                    'familyID'=> '',
                    'brokerID'=> $brokerID,
                    'clientID'=> $client_id
                );
                $where11 = array(
                    'familyID'=> '',
                    'brokerID'=> $brokerID,
                    'clientID'=> $client_id,
                    'reportDate'=>$reportDate
                );
              
          }
          else
          {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
                
            );
             $where1 = array(
                    'familyID'=>  $family_id,
                    'brokerID'=> $brokerID,
                    'clientID'=>''
                );
                $where11 = array(
                    'familyID'=>  $family_id,
                    'brokerID'=> $brokerID,
                    'clientID'=> '',
                    'reportDate'=>$reportDate
                );
          }
            $net_inv = $this->mf->get_net_investment($type, $where);
            
            $inv_summary = $this->mf->get_investment_summary($type, $where);
            
            
            
            $curr_val_summary = $this->mf->get_current_value_summary($type, $where);
           // $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
              $client_wise_summary=$this->mf->get_clientwise_summary($type,$where1);
              
            
            $mf_comman_cap_detail_1 = $this->mf->mf_comman_cap_detail_1($type,$where1);
            $mf_comman_cap_detail_2 = $mf_comman_cap_detail_1;
            
            $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where1);
          
              //Comman Net Ivestment(Purchase ,Redemption,Dividend Payout,Net Investment)
                $mf_tran_amount_date = $this->mf->get_mutual_funds_trancation_amount($type,$where11);
                $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where1);
                foreach($mf_tran_amount_date as $s2) 
                {
	                $values[] = $s2->amount;
	                //$dates[] = DateTime::createFromFormat("Y-m-d",$s2->purchase_date)->format("m-d-Y");
	                $temp = DateTime::createFromFormat("Y-m-d",$s2->purchase_date);
	                  $date_stamps[] = $temp;
	                  //->getTimestamp();
	            }
	            
	            //$reportDate=date('Y-m-d', strtotime('-1 days', strtotime($reportDate)));
	            $temp = DateTime::createFromFormat("Y/m/d",$reportDate);
	            $date_stamps[] = $temp;
	            //->getTimestamp();
	            $values[] =round($mf_summary_net_investment[0]->current_value);

	            //load the excel library
                $this->load->library('Excel');
                $objPHPExcel = new PHPExcel_Calculation_Financial();
                
                //print_r($values);
                //print_r($date_stamps);die();
        
                
                $mf_summary_net_investment[0]->xirr_value=round(($objPHPExcel->XIRR($values,$date_stamps,0.1)*100),3);
             
            
            $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where1);
            $mf_summary_detail_for_chart = $this->mf->mf_summary_detail($type,$where1);

          //print_r($mf_rep_data);
          //echo "Dipak";
          if(!empty($client_wise_summary))
          {
              // unset($_SESSION['ins_report']);
              if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                  $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                  $logo = $brokerID.'/'.$logo;
              } else {
                  $logo = "";
              }
              $status = true;
              $data['page_title']='Mutual Fund Type wise report';
              $data['mf_rep_data'] = $client_wise_summary;
              $data['mf_summary_client']=$mf_summary_client;
              $data['mf_summary_typewise']=$mf_summary_typewise;
              
              
              $data['mf_comman_scheme_summary']=$mf_comman_scheme_summary;
              $data['mf_summary_net_investment']=$mf_summary_net_investment;
              $data['mf_comman_cap_detail_1']=$mf_comman_cap_detail_1;
              $data['mf_comman_cap_detail_2']=$mf_comman_cap_detail_2;
              $data['mf_summary_detail_for_chart']=$mf_summary_detail_for_chart;
              
              $data['mf_summary_net_investment']=$mf_summary_net_investment;
              
              
                                
              $data['logo'] = $logo;
              $header['title']='View Mutual Fund 3D report';
              $header['js'] = array('assets/users/js/common.js');
              $this->load->view('client/common/header-report', $header);
              if($type=='client')
              {
                   $this->load->view('client/report/mf_report_group_typewise_client',$data);
              }
              else
              {
                 $this->load->view('client/report/mf_report_group_typewise_family', $data);
              }
          }
          else
          {
            echo"No Data Found";
          }
}

function  get_mf_schemewise_report()
{
     if(isset( $_COOKIE['client_select_id_schemewise_report']) && !empty($_COOKIE['client_select_id_schemewise_report']))
    {
    
        $client_id=$_COOKIE['client_select_id_schemewise_report'];
        setcookie("client_select_id_schemewise_report", "", time() - 3600);
        
     
    }
    else
    {
  if($this->session->userdata('type')=='head')
  {
      $family_id = $this->session->userdata('family_id');
      
  }
  else
   {
     $client_id = $this->session->userdata('client_id');
     
   }
    }
    $brokerID = $this->session->userdata('user_id');
    $type = 'client';
    $where = "";
      if($client_id != null && $client_id != '')
      {
        $where = array(
            'familyID'=> '',
            'brokerID'=> $brokerID,
            'clientID'=> $client_id
          );
      }
      else
      {
        $type = 'family';
        $where = array(
            'familyID'=> $family_id,
            'brokerID'=> $brokerID,
            'clientID'=> ''
        );
      }
      // print_r($type);
      $mf_rep_data=$this->mf->get_mutual_fund_family_detail_schemewise($type,$where);
      $mf_summary_client = $this->mf->get_mutual_fund_family_summary_portfolio($type,$where);
      $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where);
      $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where);
      $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where);
      // print_r($mf_rep_data);
      if(!empty($mf_rep_data))
      {
          // unset($_SESSION['ins_report']);
          if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } else {
              $logo = "";
          }
          // $rep_info = array('logo' => $logo, 'ins_type'=>$ins_type_value, 'report_type' => $type);
          // $ins_rep_array = array('ins_rep_data' => $ins_rep, 'gen_ins_data' => $gen_ins_rep, 'report_info'=>$rep_info);
          // $this->session->set_userdata('ins_report', $ins_rep_array);
          $status = true;
          $data['page_title']='Mutual Fund Clientwise Detail report';
          $data['mf_summary_client']=$mf_summary_client;
          $data['mf_summary_typewise']=$mf_summary_typewise;
          $data['mf_comman_scheme_summary']=$mf_comman_scheme_summary;
          $data['mf_summary_net_investment']=$mf_summary_net_investment;
          $data['mf_rep_data'] = $mf_rep_data;
          $data['logo'] = $logo;
          $header['title']='View Mutual Fund Clientwise Detail report';
          $header['js'] = array('assets/users/js/common.js');
          $this->load->view('broker/common/header-report', $header);
          if($type == 'client')
          {
              $this->load->view('client/report/mf_schemewise_detail_client', $data);
            }
          else
          {
              $this->load->view('client/report/mf_schemewise_detail_family', $data);
            }
      }
      //echo json_encode(array('Status'=> $status));
      else
      {
        echo"No Data Found";
      }
}
    

    function get_ins_report()
    {
        $this->load->model('Insurance_model', 'ins');
        $ins_type_value = 0;
      if($this->session->userdata('type')=='head')
      {
          $family_id = $this->session->userdata('family_id');
          $brokerID = $this->session->userdata('user_id');
      }
      else
       {
         $client_id = $this->session->userdata('client_id');
         $brokerID = $this->session->userdata('user_id');
       }
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $ins_rep = $this->ins->get_insurance_report($type, $where);
        $gen_ins_rep = $this->ins->get_general_insurance_report($type, $where);
        if(!empty($ins_rep) || !empty($gen_ins_rep))
        {
            unset($_SESSION['ins_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            // $rep_info = array('logo' => $logo, 'ins_type'=>$ins_type_value, 'report_type' => $type);
            // $ins_rep_array = array('ins_rep_data' => $ins_rep, 'gen_ins_data' => $gen_ins_rep, 'report_info'=>$rep_info);
            // $this->session->set_userdata('ins_report', $ins_rep_array);
             $status = true;

            $data['page_title']='Insurance Policy Report';
            $data['ins_rep_data'] = $ins_rep;
            $data['report_info'] = $rep_info;
            $data['gen_ins_data'] =$gen_ins_rep;
            $data['logo'] = $logo;
            $header['title']='View Insurance Policy Report';
            $header['js'] = array('assets/users/js/common.js');

            $this->load->view('broker/common/header-report', $header);
            if($type == 'client')
                $this->load->view('client/report/ins_report_client', $data);
            else
                $this->load->view('client/report/ins_report_family', $data);
            //$this->load->view('broker/common/footer');
        }
        //echo json_encode(array('Status'=> $status));
        else
        {
          echo"No Data Found";
        }
    }

    function get_prem_cal_report()
    {
        $this->load->model('Insurance_model', 'ins');


      if($this->session->userdata('type')=='head')
      {
          $family_id = $this->session->userdata('family_id');
          $brokerID = $this->session->userdata('user_id');
      }
      else
       {
         $client_id = $this->session->userdata('client_id');
         $brokerID = $this->session->userdata('user_id');
       }
        $prem_date = date('m/Y');//$this->input->post('prem_date');
        //$prem_date = date('d').'/'.$prem_date;
        $prem_date = '01'.'/'.$prem_date;
        $prem_date_temp = DateTime::createFromFormat('d/m/Y', $prem_date);
        $rep_date_temp = $prem_date_temp;
        $prem_date = $prem_date_temp->format('Y-m-d');
        $prem_date_temp = $prem_date_temp->format('M');

        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'clientID'=> $client_id
            );
            $whereLapse = array('clientID' => $client_id);
        }
        else
        {
            $type = 'family';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'familyID'=> $family_id
            );
            $whereLapse = array('familyID' => $family_id);
        }
        $logo = "";
        $status = false;
        $prem_rep = $this->ins->get_premium_calendar_report($type, $where);
        $lapse_rep = $this->ins->get_lapse_policy_report($type, $whereLapse);
        if(!empty($prem_rep) || !empty($lapse_rep))
        {
            unset($_SESSION['prem_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }

            $rep_date = $rep_date_temp->format('M-y');
            $rep_date_end0 = $rep_date_temp->modify('+ 11 month');
            $rep_date_end = $rep_date_end0->format('M-y');

            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $prem_rep_array = array('prem_rep_data' => $prem_rep, 'report_info'=>$rep_info, 'lapse_rep_data' => $lapse_rep,
                                    'rep_date_start'=>$rep_date, 'rep_date_end'=>$rep_date_end);
            $this->session->set_userdata('prem_report', $prem_rep_array);
            $status = true;


        $data['page_title']='Premium Calendar Report';
        $data['prem_data'] = $prem_rep;
        $data['report_info'] = $rep_info;
        $data['lapse_rep_data'] = $lapse_rep;
        $data['rep_date']['rep_date_start'] = $rep_date;
        $data['rep_date']['rep_date_end'] = $rep_date_end;
        $data['logo'] = $logo;
        $header['title']='View Premium Calendar Report';
        $header['js'] = array('assets/users/js/common.js');

        $this->load->view('client/common/header-report', $header);
        if($type == 'client')
            $this->load->view('client/report/prem_cal_client', $data);
        else
            $this->load->view('client/report/prem_cal_family', $data);
        //$this->load->view('broker/common/footer');
    }
      //echo json_encode(array('Status'=> $status));
      else
      {
        echo"No Data Found";
      }
  }





    function get_fd_report()
    {
        $this->load->model('Fixed_deposits_model', 'fd');

        if($this->session->userdata('type')=='head')
        {
            $family_id = $this->session->userdata('family_id');
            $brokerID = $this->session->userdata('user_id');
        }
        else
         {
           $client_id = $this->session->userdata('client_id');
           $brokerID = $this->session->userdata('user_id');
         }

        $type = 'client';
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
            
            $list = $this->fd->get_fixed_deposit_mat("fdt.broker_id ='".$brokerID."' and fdt.maturity_date>=curdate() and  fdt.client_id='". $client_id."' ");
        }
        else
         {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
            
            $list = $this->fd->get_fixed_deposit_mat("fdt.broker_id ='".$brokerID."' and fdt.maturity_date>=curdate() and c.family_id='". $family_id ."' ");
        }
        
        $logo = "";
        $status = false;
        $fd_rep = $this->fd->get_fixed_deposit_report($type, $where);
        $data1=[];
          if($list!='')
        {
            $num = 10;
            foreach($list as $fd)
    		 {
    				 $row = array();
    				 $num++;
    				 $row['family_name'] = $fd->family_name;
    				 $row['client_name'] = $fd->client_name;
    				 $row['transaction_date'] = $fd->transaction_date;
    				 $row['fd_inv_type'] = $fd->fd_inv_type;
    				 $row['fd_comp_name'] = $fd->fd_comp_name;
    				 $row['fd_method'] = $fd->fd_method;
    				 $row['ref_number'] = $fd->ref_number;
    				 $row['issued_date'] = $fd->issued_date;
    				 $row['amount_invested'] = round($fd->amount_invested);
    				 $row['interest_rate'] = $fd->interest_rate.'%';
    				 $row['maturity_date'] = $fd->maturity_date;
    				 $row['maturity_amount'] = round($fd->maturity_amount);
    				 $row['nominee'] = $fd->nominee;
    				 $row['status'] = $fd->status;
    				 $row['adviser_name'] = $fd->adviser_name;
    				 $row['adjustment'] = $fd->adjustment;
    
    				 $data1[] = $row;
    		 }
        }
        
        if(!empty($fd_rep))
        {
            unset($_SESSION['ins_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
             $rep_info = array('logo' => $logo, 'report_type' => $type);
             $fd_rep_array = array('fd_rep_data' => $fd_rep, 'report_info'=>$rep_info);
             $this->session->set_userdata('fd_report', $fd_rep_array);
             $status = true;

            $data['page_title']='Fixed Deposit Report';
            $data['fd_rep_data'] = $fd_rep;
            $data['report_info'] = $type;
            $data['top5_maturity'] = $data1;
            $data['logo'] = $logo;
            $data['brokerID'] = $brokerID;
            

                    $header['title']='View Fixed Deposit Report';
                    $header['js'] = array('assets/users/js/common.js');

                    $this->load->view('client/common/header-report', $header);
                    if($type == 'client')
                        $this->load->view('client/report/fd_report_client', $data);
                    else
                        $this->load->view('client/report/fd_report_family', $data);


        }
      //  echo json_encode(array('Status'=> $status));
      else
      {
        echo"No Data Found";
      }
    }

    function get_re_report()
    {

        $this->load->model('Real_estate_model', 're');

      if($this->session->userdata('type')=='head')
      {
        $family_id= $this->session->userdata('family_id');
        $brokerID= $this->session->userdata('user_id');
      }
      else {
          $client_id= $this->session->userdata('client_id');
          $brokerID= $this->session->userdata('user_id');
      }

        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $re_rep = $this->re->get_real_estate_report($type, $where);
        if(!empty($re_rep))
        {
            unset($_SESSION['re_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            // $rep_info = array('logo' => $logo, 'report_type' => $type);
            // $re_rep_array = array('re_rep_data' => $re_rep, 'report_info'=>$rep_info);
            // $this->session->set_userdata('re_report', $re_rep_array);
            // $status = true;

            $data['page_title']='Real Estate Report';
            $data['re_rep_data'] = $re_rep;
            $data['report_info'] = $type;
            $data['logo'] = $logo;
            $header['title']='View Real Estate Report';
            $header['js'] = array('assets/users/js/common.js');

            $this->load->view('client/common/header-report', $header);
            if($type == 'client')
                $this->load->view('client/report/real_estate_report_client', $data);
            else
                $this->load->view('client/report/real_estate_report_family', $data);

        }
        //echo json_encode(array('Status'=> $status));
        else
        {
          echo"No Data Found";
        }
    }

    function get_int_cal_report()
    {
        $this->load->model('Premium_types_model', 'prem_type');
        $this->load->model('Fixed_deposits_model', 'fd');


      if($this->session->userdata('type')=='head')
      {
        $family_id= $this->session->userdata('family_id');
        $brokerID= $this->session->userdata('user_id');
      }
      else {
          $client_id= $this->session->userdata('client_id');
          $brokerID= $this->session->userdata('user_id');
      }

        $int_date = date('m/Y');//$this->input->post('int_date');
        $int_date = '01'.'/'.$int_date;
        $int_date_temp = DateTime::createFromFormat('d/m/Y', $int_date);
        $rep_date_temp = $int_date_temp;
        $int_date = $int_date_temp->format('Y-m-d');
        $int_month_temp = $int_date_temp->format('M');

        $type = 'client';
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'month'=> $int_month_temp,
                'start_date'=> $int_date,
                'clientID'=> $client_id
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'month'=> $int_month_temp,
                'start_date'=> $int_date,
                'familyID'=> $family_id
            );
        }
        $logo = "";
        $status = false;
        $int_rep = $this->fd->get_interest_calendar_report($type, $where);
        if(!empty($int_rep))
        {
            unset($_SESSION['int_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }

            $rep_date = $rep_date_temp->format('M-y');
            $rep_date_end0 = $rep_date_temp->modify('+ 11 month');
            $rep_date_end = $rep_date_end0->format('M-y');

            $repYear = $int_date_temp->format('Y');
            if($int_month_temp != 'Jan')
            //$repYear = date("Y-m-d", strtotime(date("Y-m-d", strtotime($repYear)) . " + 1 year"));
            $repDesc = $int_date_temp->format('F').' '.$int_date_temp->format('Y').' - '.date('F').' '.$repYear ;
            $rep_info = array('logo' => $logo, 'report_type' => $type, 'report_desc' =>  $repDesc);
            $int_rep_array = array('int_rep_data' => $int_rep, 'report_info'=>$rep_info,
                                'rep_date_start'=>$rep_date, 'rep_date_end'=>$rep_date_end);
            $this->session->set_userdata('int_report', $int_rep_array);
            $status = true;

            $data['page_title']='Interest Calendar Report';
            $data['int_data'] = $int_rep;
            $data['report_info'] = $rep_info;
            $data['logo'] = $logo;
            $data['rep_date']['rep_date_start'] = $rep_date;
            $data['rep_date']['rep_date_end'] = $rep_date_end;

            $header['title']='View Interest Calendar Report';
            $header['js'] = array('assets/users/js/common.js');
            $this->load->view('client/common/header-report', $header);
            if($type == 'client')
                $this->load->view('client/report/fd_int_cal_client', $data);
            else
                $this->load->view('client/report/fd_int_cal_family', $data);
        }
      //  echo json_encode(array('Status'=> $status));
      else {
          echo"No Data Found";
      }
    }

    function get_equity_report()
    {


        // Include the main Financial class (search for installation path).
        require_once('application/third_party/Financial.php');
        $financial = new Financial();
        $xirr = 1;
        $cheque = 1;

        if($this->session->userdata('type')=='head')
        {
          $family_id= $this->session->userdata('family_id');
          $brokerID= $this->session->userdata('user_id');
        }
        else {
            $client_id= $this->session->userdata('client_id');
            $brokerID= $this->session->userdata('user_id');
        }

        // $xirr = 1;
        // $cheque = 1;
        
        
       
        $this->db->where('client_id',$this->session->userdata('client_id'));
        $query=$this->db->get('client_brokers');
           $res = $query->result();
        $clientCode=$result->client_code;
        
       
        
        
        $userID = $this->session->userdata('user_id');

        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array('clientID'=> $client_id);
            $values_where = "where c.client_id = '".$client_id."'";
            $where['clientCode'] = '';
            $where['famID'] = '';
            $where['user_id'] = $userID;

            $xirr_where = array(
                'clientID'=>$client_id,
                'famID'=>'',
                'type'=>'client_code'
            );

            $this->db->where('client_id',$this->session->userdata('client_id'));
            $query=$this->db->get('clients');
            $result=$query->row();
            $client_name=$result->name;
            //get clientName
            // $clientInfo = $this->client->get_client_info($client_id);
            // if($clientInfo)
            // {
            //     $clientName = $clientInfo->name;
            // }
            $clientName=$this->session->userdata('client_name');
        }
        else
        {
          $where = array(
              'clientID'=> $client_id,
              'clientCode'=> $client_code,
              'famID'=> $family_id,
              'user_id'=> $userID
          );

          $xirr_where = array(
              'clientID'=>'',
              'famID'=>$family_id,
              'type'=>'family'
          );


            $values_where = "where c.client_id IN (select client_id from clients where family_id = '".$family_id."')";

            //get family Name and client Names
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $family_id);

            // $this->db->select('name');
            // $this->db->from('families');
            // $this->db->where('family_id',$this->session->userdata('family_id'));
            // $query = $this->db->get();
            // //$familyName = $this->family->get_family_by_id($family_id);
            // $familyName = $query->name;



            $this->db->where('family_id',$this->session->userdata('family_id'));
            $query=$this->db->get('families');
            $result=$query->row();
            $familyName=$result->name;
            //var_dump($familyName);
            //$clients = $this->client->get_clients_broker_dropdown($cli_condition);

            $type = 'family';
        }
        $logo = "";
        $status = false;
        //get the equity/scrip values
        $eq_values = $this->eq->get_equity_values($values_where);
        //echo json_encode(var_dump($eq_values));
        //get the sum of all the values (to calculate %)
        $value_total = 0;
        $IsAPCValueAvailable=0;
        foreach($eq_values as $value) {
            $value_total += $value->value;
               if( $value->apc>0)
                {
                    $IsAPCValueAvailable = $value->apc;
                    
                }
        }
        //get values of all funds, if cheque option selected
        if($cheque === 1 || $cheque === '1') {
            $eq_rep = $this->eq->get_equity_report($where);
        }
        //echo json_encode(var_dump($eq_rep));
        $eq_balance = $this->eq->get_equity_broker_balance($values_where);
        //var_dump($eq_balance);

        if($eq_values !== false && !empty($eq_values)) {
          if($xirr === 1 || $xirr === '1') {
              $cash_flows = array();
              $date_stamps = array();
              $day_diffs = array();
              $xirr_data = $this->eq->get_xirr_data($xirr_where);
          //  var_dump($xirr_data);
              if($xirr_data !== false) {
                  foreach($xirr_data as $row) {
                      $cash_flows[] = $row->cash_flows;
                      $temp = DateTime::createFromFormat('Y-m-d',$row->data_date);
                      $date_stamps[] = $temp->getTimestamp();
                      $day_diffs[] = $row->day_diff;
                  }
                  $xirr_value = $financial->XIRR($cash_flows, $date_stamps, 0.1);
              }
          }

            unset($_SESSION['eq_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            if($type == 'client')
            {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'client_id' => $client_id, 'client_name' => $clientName);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            } else   {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'family_id' => $family_id, 'family_name' => $familyName, 'clients' => $clients);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            }

            $this->session->set_userdata('eq_report', $eq_rep_array);
            $status = true;
            //echo json_encode(array('Status'=> $status));



        $header['title']='View Equity/Shares Report';
        //$data['all_data'] = $_SESSION['eq_report'];
        $data['report_info'] =$eq_info;
        //var_dump($data['report_info']);
        if(isset($eq_rep)) {
            $data['eq_rep_data'] = $eq_rep;
        } else {
            $data['eq_rep_data'] = false;
        }
        $data['eq_values_data'] = $eq_values;
        $data['eq_balance'] = $eq_balance;
        if(isset($xirr_value)) {
            $data['xirr'] = $xirr_value;
        }
        $data['total_value'] = $value_total;
        $data['logo'] = $logo;
        $data['IsAPCValueAvailable'] = $IsAPCValueAvailable;
        $data['brokerID'] = $brokerID;
        

        $header['js'] = array('assets/users/js/common.js');
        if($type == 'family') {
            $header['title'] = 'Equity Report for Family';
            $this->load->view('client/common/header-report', $header);
            $this->load->view('client/report/equity_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $header['title'] = 'Equity Report for Client';
            $this->load->view('client/common/header-report', $header);
            $this->load->view('client/report/equity_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        }
      }
      else {
          echo"No Data Found";
      }
    }

    function get_commodity_report()
    {

        $this->load->model('Commodities_model', 'comm');

      if($this->session->userdata('type')=='head')
      {
        $family_id= $this->session->userdata('family_id');
        $brokerID= $this->session->userdata('user_id');
      }
      else {
          $client_id= $this->session->userdata('client_id');
          $brokerID= $this->session->userdata('user_id');
      }
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $comm_rep = $this->comm->get_commodity_report($type, $where);
        if(!empty($comm_rep))
        {
            unset($_SESSION['commodity_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            //$rep_info = array('logo' => $logo, 'report_type' => $type);
          // $comm_rep_array = array('comm_rep_data' => $comm_rep, 'report_info'=>$rep_info);
          //  $this->session->set_userdata('commodity_report', $comm_rep_array);
        //    $status = true;

            $header['title']='View Commodity Report';
            $data['commodity_rep_data'] = $this->comm->get_commodity_report($type, $where);;
            $data['report_info'] = $type;
            $data['logo'] = $logo;

            $header['js'] = array('assets/users/js/common.js');
            if($type == 'client') {
                $this->load->view('broker/common/header-report', $header);
                $this->load->view('client/report/commodity_report_view_client', $data);
                //$this->load->view('broker/common/footer');
            } else {
                $this->load->view('broker/common/header-report', $header);
                $this->load->view('client/report/commodity_report_view_family', $data);
                //$this->load->view('broker/common/footer');
            }
        } else {
            echo"No Data Found";
        }

    }

    function get_al_report()
    {
        $this->load->model('Assets_liabilities_model', 'al');

      $family_id = $this->session->userdata('family_id');
      $brokerID = $this->session->userdata('user_id');
        $where = "";
        if($family_id != null && $family_id != '')
        {
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $asset_rep = $this->al->get_asset_report($where);
        $liability_rep = $this->al->get_liability_report($where);

        if(!empty($asset_rep) || !empty($liability_rep))
        {
            unset($_SESSION['al_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            $rep_info = array('logo' => $logo);
            $al_rep_array = array('asset_rep_data' => $asset_rep, 'liability_rep_data' => $liability_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('al_report', $al_rep_array);
            $status = true;


            $data['page_title']='Asset and Liabilities Report';
            $data['asset_rep_data'] = $asset_rep;
            $data['liability_rep_data'] = $liability_rep;
            $data['report_info'] = $rep_info;
            $data['logo'] = $logo;

            $header['title']='View Asset and Liabilities Report';
            $header['js'] = array('assets/users/js/common.js');
            $this->load->view('client/common/header-report', $header);
            $this->load->view('client/report/asset_liability_report', $data);
        }

        else {
          echo "No Data Found";
        }
      //  echo json_encode(array('Status'=> $status));
    }


        function get_mf_report()
        {

            $this->load->model('Mutual_funds_model', 'mf');

          if($this->session->userdata('type')=='head')
          {
            $family_id= $this->session->userdata('family_id');
            $brokerID= $this->session->userdata('user_id');
          }
          else {
              $client_id= $this->session->userdata('client_id');
              $brokerID= $this->session->userdata('user_id');
          }


            $type = 'client';
            $where = "";
            if($client_id != null && $client_id != '')
            {
                $where = array(
                    'clientID'=> $client_id,
                    'brokerID'=> $brokerID
                );
            }
            else
            {
                $type = 'family';
                $where = array(
                    'familyID'=> $family_id,
                    'brokerID'=> $brokerID
                );
            }
            $logo = "";
            $status = false;
            $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
            //var_dump($mf_rep[0]);
            $net_inv = $this->mf->get_net_investment($type, $where);
            $inv_summary = $this->mf->get_investment_summary($type, $where);
            $curr_val_summary = $this->mf->get_current_value_summary($type, $where);

            if(!empty($mf_rep) && !isset($mf_rep['code']))
            {
                unset($_SESSION['mf_report']);
                if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                    $logo = $brokerID.'/'.$logo;
                } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                    $logo = $brokerID.'/'.$logo;
                } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                    $logo = $brokerID.'/'.$logo;
                } else {
                    $logo = "";
                }
                // $rep_info = array('logo' => $logo, 'report_type' => $type);
                // $mf_rep_array = array('mf_rep_data' => $mf_rep, 'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary);
                // $this->session->set_userdata('mf_report', $mf_rep_array);
                 $status = true;


                $data['page_title']='Mutual Fund Report';
                $data['mf_rep_data'] = $mf_rep;
                $data['report_info'] = $rep_info;
                $data['inv_sum'] = $inv_summary;
                $data['cur_val_sum'] = $curr_val_summary;
                $data['net_inv_data'] = $net_inv;
                $data['logo'] = $logo;

                $header['title']='View Mutual Fund Report';

                $header['css'] = array(
                    /*'assets/users/plugins/charts-flot/excanvas.min.js'*/
                    'assets/users/plugins/charts-morrisjs/morris.css'
                );
                $header['js'] = array(
                    /*'assets/users/plugins/charts-flot/jquery.flot.min.js',
                    'assets/users/plugins/charts-flot/jquery.flot.pie.min.js',
                    'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',*/
                    'assets/users/plugins/charts-morrisjs/morris.min.js',
                    'assets/users/plugins/charts-morrisjs/raphael.min.js',
                    'assets/users/js/common.js'
                );
                if($type == 'client') {
                    $this->load->view('client/common/header-report', $header);
                    $this->load->view('client/report/mf_report_client', $data);
                    //$this->load->view('broker/common/footer');
                } else {
                    $this->load->view('client/common/header-report', $header);
                    $this->load->view('client/report/mf_report_family', $data);
                    //$this->load->view('broker/common/footer');
                }
            }
            else {

              echo "No Data Found";
            }
          //  echo json_encode(array('Status'=> $status));
        }

    function get_cash_flow_report()
    {
      $this->load->model('Reports_model', 'report');
      $this->load->model('Families_model', 'family');


            if($this->session->userdata('type')=='head')
            {
              $family_id= $this->session->userdata('family_id');
              $brokerID= $this->session->userdata('user_id');
            }
            else {
                $client_id= $this->session->userdata('client_id');
                $brokerID= $this->session->userdata('user_id');
            }

            $from_date = date('Y/m/d');
            $to_date=(date('Y')+50).date('/m/d');
            $type = 'client';
            $where = "";
      if($client_id != null && $client_id != '')
      {
          $where = array(
              'clientID'=> $client_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
      }
      else
      {
          $type = 'family';
          $where = array(
              'familyID'=> $family_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
      }
      $logo = "";
      $status = false;
      $family_id= $this->session->userdata('family_id');
      $fam_info = $this->family->get_family_by_id($family_id);
      $cash_flow_rep = $this->report->get_cash_flow_report($type, $where);
      //print_r($cash_flow_rep);
      if(!empty($cash_flow_rep))
      {
          unset($_SESSION['cash_flow_report']);
          if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } else {
              $logo = "";
          }
          // $rep_info = array('logo' => $logo, 'report_type' => $type);
          // $cash_flow_rep_array = array('cash_flow_rep_data' => $cash_flow_rep, 'report_info'=>$rep_info, 'fam_info'=>$fam_info);
          // $this->session->set_userdata('cash_flow_report', $cash_flow_rep_array);
          // $status = true;

        $header['title']='View Cash Flow Report';
        $data['cash_flow_rep_data'] = $cash_flow_rep;
        $data['fam_info'] = $fam_info;
        $data['report_info'] = $rep_info;
        $data['logo'] = $logo;

        $header['js'] = array('assets/users/js/common.js');
        if($type == 'client') {
            $this->load->view('client/common/header-report', $header);
            $this->load->view('client/report/cash_flow_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('client/common/header-report', $header);
            $this->load->view('client/report/cash_flow_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        }
      }

        else
        {
            echo"No Data Found";
        }
    }


    function get_ledger_report()
    {
      $this->load->model('Reports_model', 'report');
      $this->load->model('Families_model', 'family');

      if($this->session->userdata('type')=='head')
        {
            $family_id= $this->session->userdata('family_id');
            $brokerID= $this->session->userdata('user_id');
        }
      else {
              $client_id= $this->session->userdata('client_id');
              $brokerID= $this->session->userdata('user_id');
            }

      $from_date = (date('Y')-1).date('/m/d');
      $to_date=date('Y/m/d');
      $type = 'client';
      $where = "";
      if($client_id != null && $client_id != '' && !empty($client_id))
      {
          $where = array(
              'clientID'=> $client_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
          $id = $client_id;
          $nameRes = $this->client->get_client_info($client_id);
          $name = $nameRes->name;
          
           $where = array(
              'clientID'=> $client_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID,
                'InvestmentType'=>0
          );

      }
      else
      {
          $type = 'family';
          $where = array(
              'familyID'=> $family_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
          $id = $family_id;
          $famRes = $this->family->get_family_by_id($family_id);
          $name = $famRes->name;
          $where = array(
              'familyID'=> $family_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID,
                'InvestmentType'=>0
          );
          
      }
      $logo = "";
      $status = false;
      
    
      $ledger_rep_inflow = $this->report->get_ledger_report_inflow($type, $where);
    
      $ledger_rep_outflow = $this->report->get_ledger_report_outflow($type, $where);
      
      $ledger_rep_dividend = $this->report->get_ledger_report_dividend($type, $where);
    
        
      if(!empty($ledger_rep_inflow) || !empty($ledger_rep_outflow))
      {
          unset($_SESSION['ledger_report']);
          if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
              $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
              $logo = $brokerID.'/'.$logo;
          } else {
              $logo = "";
          }
          $rep_info = array('logo' => $logo, 'report_type' => $type, 'id' => $id, 'name' => $name);
          $status = true;

          /*reverse sort on all arrays - because we want it date desc order - Salmaan - 2017-01-04 */
          rsort($ledger_rep_inflow);
          rsort($ledger_rep_outflow);
          rsort($ledger_rep_dividend);

          $header['title']='View Ledger Report';
          $data['ledger_rep_inflow_data'] = $ledger_rep_inflow;
          $data['ledger_rep_outflow_data'] = $ledger_rep_outflow;
          $data['ledger_rep_dividend_data'] = $ledger_rep_dividend;
          $data['report_info'] = $rep_info;
          $data['logo'] = $logo;


          $header['js'] = array('assets/users/js/common.js');
          if($type == 'client') {
              $this->load->view('client/common/header-report', $header);
              $this->load->view('client/report/ledger_report_view_client', $data);
              //$this->load->view('broker/common/footer');
          } else {
              $this->load->view('client/common/header-report', $header);
              $this->load->view('client/report/ledger_report_view_family', $data);
              //$this->load->view('broker/common/footer');
          }
      }
      else{
        echo "No data Found";
      }
    }

    function get_summary_report()
    {

      $this->load->model('Reports_model', 'report');
      if($this->session->userdata('type')=='head')
      {
          $type = 'family';
        //$client_id= $this->session->userdata('client_id');
        $family_id= $this->session->userdata('family_id');
        $brokerID= $this->session->userdata('user_id');
        $data['dash_data']=$this->dash->get_summary_Dashboard_HOF($family_id,$brokerID,$client_id);
        $data['client_list']=$this->dash->getTotalPortFolioModel($family_id,$brokerID);
         $where1 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-1 days"))
            );
            $where2 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-2 days"))
            );
        
      }
      else {
          $type = 'client';
          $client_id= $this->session->userdata('client_id');
          $brokerID= $this->session->userdata('user_id');
          $data['dash_data']= $this->dash->get_summary_Dashboard_client($client_id,$brokerID);
          
           $where1 = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-1 days"))
            );
            $where2 = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-2 days"))
            );
      }

        $summary_rep_previous_1 = $this->report->get_summary_report_previous($type, $where1);
        $summary_rep_previous_2 = $this->report->get_summary_report_previous($type, $where2);
        
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $summary_rep = $this->report->get_summary_report($type, $where);
        if(!empty($summary_rep))
        {
            unset($_SESSION['summary_report']);
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $summary_rep_array = array('summary_rep_data' => $summary_rep, 
                                        'summary_rep_previous_1'=>$summary_rep_previous_1,
                                        'summary_rep_previous_2'=>$summary_rep_previous_2,
                                        'brokerID'=>$brokerID, 
                                        'report_info'=>$rep_info);
            $this->session->set_userdata('summary_report', $summary_rep_array);
            $status = true;

            $header['title']='View Summary Report';
            $data['summary_rep_data'] = $summary_rep;
            $data['summary_rep_previous_1'] = $summary_rep_previous_1;
            $data['summary_rep_previous_2'] = $summary_rep_previous_2;
            $data['brokerID'] = $brokerID;
            $data['report_info'] = $rep_info;
            $data['logo'] = $logo;

            $header['js'] = array('assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.orderBars.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js');

            $header['css'] = array(
                'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
                'assets/users/plugins/charts-morrisjs/morris.css',
                'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
                'assets/build/css/custom.css'

            );

            if($type == 'client') {

                $this->load->view('client/common/header-report', $header);

                $this->load->view('client/report/summary_report_view_client', $data);
              //  //$this->load->view('broker/common/footer');
            } else {
                $this->load->view('client/common/header-report', $header);
                $this->load->view('client/report/summary_report_view_family', $data);
              //  //$this->load->view('broker/common/footer');
            }

        }
        else {
          echo"No Data Found.";
        }
        //echo json_encode(array('Status'=> $status));
    }
}
