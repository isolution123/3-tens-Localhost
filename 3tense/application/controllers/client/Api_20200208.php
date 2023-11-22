<?php
error_reporting(E_ALL & ~E_NOTICE);
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: token, Content-Type');
        header('Access-Control-Max-Age: 1728000');
        header("Access-Control-Allow-Credentials: true");
        header('Content-Length: 0');
        header('Content-Type: text/plain');
        die();
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: token, Content-Type");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Access-Control-Allow-Credentials");
//header("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS");
set_time_limit(0);

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Api extends CI_Controller
{
    function __construct()
    {
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->library('Custom_exception');
        $this->load->library('Mail', 'mail');
        $this->load->library('Common_lib');
        
        
        $this->load->model('client/Clientlogin_model');
        $this->load->model('Clients_model','client');
        $this->load->model('Common_model', 'common');
        $this->load->model('Client_reminders_model', 'rem');
        $this->load->model('client/Dashboard_models', 'dash');
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Insurance_model', 'ins');
        $this->load->model('Fixed_deposits_model', 'fd');
        $this->load->model('Assets_liabilities_model','al');
        $this->load->model('Reports_model', 'report');
        $this->load->model('Doc_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Families_model','family');
        $this->load->model('Equity_model', 'eq');
        
        //define('CAROUSEL_IMAGES',array(base_url()."assets/users/images/banner-new-1.jpg",base_url()."assets/users/images/banner-new-2.jpg"));
        
        /*$path    = 'assets/app/dashboard_images/';
        $files = scandir($path);
        $images = array();
        
        //$index = 0;
        
        $arrImages = array();
        
        for($i=0;$i<count($files);$i++)
        {
            if($files[$i]!='.' && $files[$i]!='..')
            {
                $arrImages[$i] = base_url().$path.$files[$i];
            }
        }*/
        //$arrImages = array(base_url()."assets/users/images/banner-new-1.jpg",base_url()."assets/users/images/banner-new-2.jpg");
        //echo '<pre>';print_r($arrImages);die;
    }

    function index()
    {
        //get request payload
        $request = file_get_contents('php://input');
        
        $objRequest = json_decode($request);
        
        //echo '<pre>';print_r($objRequest);die;
        
        $response;
        $objResponse;
        
        switch($objRequest->operation)
        {
            case 'login':
                $arrResponse = $this->login($objRequest);
                break;
            
            case 'resetPassword':
                $arrResponse = $this->resetPassword($objRequest);
                break;
            
            case 'getReminders':
                $arrResponse = $this->getReminders($objRequest);
                break;
            
            case 'markReminderAsRead':
                $arrResponse = $this->markReminderAsRead($objRequest);
                break;
            
            case 'getSummary':
                
                /*if(isset($objRequest->full_refresh) && $objRequest->full_refresh)
                {
                    $arrResponse = $this->getSummary($objRequest);
                }
                else
                {
                    $hof_flag = $objRequest->hof ? '1' : '0';
                    $sql = "select * from api_logs as a where a.operation='getSummary' and a.user_id='$objRequest->client_id' and a.hof='$hof_flag' and a.created_datetime > DATE_SUB(NOW(), INTERVAL 24 HOUR) order by a.log_id desc limit 1";
                    $query = $this->db->query($sql);

                    if(count($query->result()) > 0)
                    {
                        //echo '<pre>';print_r($query->result());die;
                        $data = $query->result();
                        $response = $data[0]->response_payload;
                    }
                    else
                    {
                        $arrResponse = $this->getSummary($objRequest);
                    }

                }*/
               
                $arrResponse = $this->getSummary($objRequest);
                
                break;
            
            case 'getSummaryReport':
                $arrResponse = $this->getSummaryReport($objRequest);
                break;
            
            case 'getMFReport':
                $arrResponse = $this->getMFReport($objRequest);
                break;
            
            case 'getEquityReport':
                $arrResponse = $this->getEquityReport($objRequest);
                break;
            
            case 'getDocTypes':
                $arrResponse = $this->getDocTypes($objRequest);
                break;
            
            case 'uploadDoc':
                $arrResponse = $this->uploadDoc($objRequest);
                break;
            
            case 'getInsuranceReport':
                $arrResponse = $this->getInsuranceReport($objRequest);
                break;
            
            case 'getInsPremCal':
                $arrResponse = $this->getInsPremCal($objRequest);
                break;
            
            case 'getFDReport':
                $arrResponse = $this->getFDReport($objRequest);
                break;
            
            case 'getClientReport':
                $arrResponse = $this->getClientReport($objRequest);
                break;

            case 'getRealEstateReport':
                $arrResponse = $this->getRealEstateReport($objRequest);
                break;
            
            case 'getCommodityReport':
                $arrResponse = $this->getCommodityReport($objRequest);
                break;
            
            case 'getFDPremCal':
                $arrResponse = $this->getFDPremCal($objRequest);
                break;
            
            case 'getLedgerReport':
                $arrResponse = $this->getLedgerReport($objRequest);
                break;
            
            case 'getCashflowReport':
                $arrResponse = $this->getCashflowReport($objRequest);
                break;
            
             case 'sendPushNotification':
                $arrResponse = $this->sendPushNotification($objRequest);
                break;
            
            case 'getAssetLiabilityReport':
                $arrResponse = $this->getAssetLiabilityReport($objRequest);
                break;
            
            default :
                $arrResponse = array('code'=>-1,'msg'=>'Operation not supported');
                break;
        }
        
        if($response == '')
        {
            $response = json_encode($arrResponse);
        }
        
        
        $hof = '';
        if(isset($objRequest->hof))
        {
            $hof = $objRequest->hof ? '1' : '0';
        }
            
        $this->common->logApiOperation(array(
                                                'request_payload'=>$request,
                                                'response_payload'=>$response,
                                                'entity'=>'Client',
                                                'user_id'=>@$objRequest->client_id,
                                                'hof'=>$hof,
                                                'operation'=>$objRequest->operation,
                                                'device_type'=>$objRequest->device_type,
                                                'device_os'=>$objRequest->device_os,
                                                'device_os_version'=>$objRequest->device_os_version,
                                                'app_version'=>$objRequest->app_version,
                                                'response_code'=>$arrResponse['code'],
                                                'ip_address'=>$_SERVER['REMOTE_ADDR']
                ));
        
        if($objRequest->client_id != null && $objRequest->device_token !=null)
        {
            //update device details for client
            $this->client->setDeviceDetails($objRequest->client_id,array(
                'device_id'=>$objRequest->device_id,
                'device_token'=>$objRequest->device_token,
                'device_type'=>$objRequest->device_type,
                'device_os'=>$objRequest->device_os,
                'device_os_version'=>$objRequest->device_os_version,
                'app_version'=>$objRequest->app_version
               )
            );
        }
       

        //echo '<pre>';print_r($a);die;
        header("Content-Type: application/json");
        
        echo $response;
    }
    
    private function login($objRequest)
    {
        $result = $this->Clientlogin_model->userAuth($objRequest->username, $objRequest->password,false);
        //echo '<pre>';print_r($result);die;
        if(!$result)
        {
            return array('code'=>-1,'msg'=>'Invalid username and/or password!');
        }
        else
        {
            //get client details
            $client = $this->client->get_client_info($result->client_id);
            
            //echo '<pre>';print_r($client);die; 
            
            if($client->app_access == '0')
            {
                return array('code'=>-1,'msg'=>'Please contact system admin for app access.');
            } 
            else
            {
                if($objRequest->device_token != null)
                {
                    //update device details for client
                    $this->client->setDeviceDetails($result->client_id,array(
                        'device_id'=>$objRequest->device_id,
                        'device_token'=>$objRequest->device_token,
                        'device_type'=>$objRequest->device_type,
                        'device_os'=>$objRequest->device_os,
                        'device_os_version'=>$objRequest->device_os_version,
                        'app_version'=>$objRequest->app_version
                       )
                    );
                }
                
                
                if($result->broker_id == null)
                {
                    $user_id = $result->id;
                }
                else
                {
                    $user_id = $result->broker_id;
                }

                //get broker logo
                $logo_url = $this->common->getBrokerLogo($user_id);
                $logo_url = base_url().$logo_url;
                $client->logo = $logo_url;
                unset($client->password);

                return array('code'=>1,'client'=>$client);
            }
           
        }
    }
    
    private function resetPassword($objRequest)
    {
        if($objRequest->secret_key!='')
        {
            //check current password
            $result =  $this->Clientlogin_model->checkPassword($objRequest->client_id,$objRequest->old_pwd);
            //echo '<pre>';print_r($result);die;
            if($result)
            {
                 //validate secret key
                $strHash = md5($result[0]->client_id.$result[0]->email_id.$result[0]->pan_no.$result[0]->mobile);
                
                if($strHash != $objRequest->secret_key)
                {
                    return array('code'=>-1,'msg'=>'Invalid secret key');
                }
                else
                {
                    //reset password
                    if($query = $this->Clientlogin_model->change($objRequest->client_id,$objRequest->new_pwd))
                    {
                        return array('code'=>1,'msg'=>'Password changed successfully.');
                    }
                    else
                    {
                        return array('code'=>-1,'msg'=>'Something went wrong.Please try again.');
                    }
                }
            }
            else
            {
                return array('code'=>-1,'msg'=>'Current password does not match.');
            }
        }
        else
        {
            return array('code'=>-1,'msg'=>'Invalid secret key');
        }
    }
    
    private function getReminders($objRequest)
    {
        $date2 = new DateTime(date('d-m-Y'));
        $date2->modify('-15 day');
        $date = new DateTime(date('d-m-Y'));
        $date->modify('+15 day');
        $condition = 'reminder_date between "'.$date2->format('Y-m-d').'" and "'.$date->format('Y-m-d').'" AND reminder_type != "Client" AND reminder_type != "Notification" AND client_id = "'.$objRequest->client_id.'" AND client_view = 0' ;
        $rem_data = $this->rem->dash_reminder_list($condition, 50);
        
        $today = new DateTime(date('d-m-Y'));
        $today1 = new DateTime(date('d-m-Y'));
        $today1->modify('-1 day');
        $condition = 'reminder_date between "'.$today1->format('Y-m-d').'" and "'.$today->format('Y-m-d').'" AND reminder_type != "Client" AND reminder_type = "Notification" AND client_id = "'.$objRequest->client_id.'" AND client_view = 0' ;
        $notifications = $this->rem->dash_reminder_list($condition, 50);
        //echo '<pre>';print_r($notifications);die;
        //echo count($notifications);die;
        $arrReminders = array_merge($rem_data,$notifications);
        
        
        //echo '<pre>';print_r($arrReminders);die;
        return array('code'=>1,'reminders'=>$arrReminders,'reminder_count'=>count($arrReminders));
    }
    
    private function markReminderAsRead($objRequest)
    {
        $data = array('client_view' => 1);
        $condition = array('reminder_id'=>$objRequest->reminder_id);
        $this->rem->complete_reminder($data, $condition);
        
        //get reminders
        $rem_data = $this->getReminders($objRequest);
        return array('code'=>1,'reminders'=>$rem_data['reminders'],'reminder_count'=>count($rem_data['reminders']));
    }
    
    private function getSummary($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        $family = $this->client->get_client_family($objRequest->client_id);
        
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
       
        //echo $brokerID.'  '.$family_id;die;
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $summary = $this->dash->get_summary_Dashboard_HOF($family_id,$brokerID,$client_id); 
            $client_list = $this->dash->getTotalPortFolioModel($family_id,$brokerID);
            
            //echo '<pre>';print_r($summary);die;
            $data['members'] = array();
            
            $member_count = 0;
            
            $j=0;
            $total_cl_portfolio=0;
            $percentClient=array();
            $len=sizeof($client_list);
            
            foreach ($client_list as $rs)
            {
              $total_cl_portfolio=$total_cl_portfolio+intval($rs['TotalPortfolio']);
            }

            foreach ($client_list as $rs)
            {
                array_push($percentClient,((intval($rs['TotalPortfolio'])/$total_cl_portfolio)*100));
            }
            
            foreach ($client_list as $rs)
            {
                $member[$member_count] = array('name'=>$rs['client_name'],'portfolio_value'=>isset($rs['TotalPortfolio'])?intval($rs['TotalPortfolio']):0,'allocation'=>sprintf("%.2f",$percentClient[$member_count])."%");
                $member_count++;
            }
            
            $data['members'] = $member;
            
            //focus 5 start
            $data['focus']['mutual_fund']['recent_purchase']= isset($summary['varMFLastPurhase'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varMFLastPurhase'])):$this->common_lib->moneyFormatIndiaClient(0);
            $data['focus']['mutual_fund']['recent_redemption']= isset($summary['varMFLastRed'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varMFLastRed'])):$this->common_lib->moneyFormatIndiaClient(0);
            
            
            $mf_list_pur = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type IN('PIP','NFO','IPO','TIN') and c.family_id='".$family_id."'");  
            foreach ($mf_list_pur as $k=>$v)
            {
                //echo '<pre>';print_r($mf_list_pur[$k]);die;
                $mf_list_pur[$k]->amount = round($mf_list_pur[$k]->amount);
            }
            $data['focus']['mutual_fund']['purchase'] = $mf_list_pur;
            //echo '<pre>';print_r($mf_list_pur);die;
            //$data['focus']['mutual_fund']['purchase'] = new stdClass();
            
            $mf_list_redm = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type in('RED','DP') and c.family_id='".$family_id."'");
            //$mf_list_redm = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type in('RED') and c.family_id='".$family_id."'");
            
            foreach ($mf_list_redm as $k=>$v)
            {
                //echo '<pre>';print_r($mf_list_redm[$k]);die;
                $mf_list_redm[$k]->amount = round($mf_list_redm[$k]->amount);
            }
            $data['focus']['mutual_fund']['redemption'] = $mf_list_redm;
            
            
            
            $data['focus']['insurance_policy']['premium_dues']= isset($summary['varUpcomingPremDue'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUpcomingPremDue'])):$this->common_lib->moneyFormatIndiaClient(0);
            $data['focus']['insurance_policy']['upcoming_maturity']= isset($summary['varUpcomingMat'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUpcomingMat'])):$this->common_lib->moneyFormatIndiaClient(0);
            
            
            $ins_new_list = $this->ins->get_new_top_new_client("insurances.broker_id='".$brokerID."' and insurances.next_prem_due_date >=curdate() and insurances.status in(1,2,3,4) and cli.family_id='".$family_id."'");
            
            foreach ($ins_new_list as $k=>$v)
            {
                //echo '<pre>';print_r($ins_new_list[$k]);die;
                $ins_new_list[$k]->prem_amt = round($ins_new_list[$k]->prem_amt);
            }
            
            $data['focus']['insurance_policy']['premium'] = $ins_new_list;
            
            $ins_mat_list = $this->ins->get_new_top_mat_client("insurances.broker_id='".$brokerID."' and premium_maturities.maturity_date>=CURRENT_DATE() and insurances.status in(1,2,3,4) and clients.family_id='".$family_id."'");
            
            foreach ($ins_mat_list as $k=>$v)
            {
                //echo '<pre>';print_r($ins_mat_list[$k]);die;
                $ins_mat_list[$k]->amount = round($ins_mat_list[$k]->amount);
            }
            
            $data['focus']['insurance_policy']['maturity'] = $ins_mat_list;
            
            
            
            $data['focus']['fixed_deposit']['upcoming_maturity']= isset($summary['varUpcomingFDMat'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUpcomingFDMat'])):$this->common_lib->moneyFormatIndiaClient(0);
            $data['focus']['fixed_deposit']['upcoming_interest']= isset($summary['varUcompingFDInterest'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUcompingFDInterest'])):$this->common_lib->moneyFormatIndiaClient(0);
            
            
            $fd_maturity_list = $this->fd->get_fixed_deposit_mat("fdt.broker_id ='".$brokerID."' and fdt.maturity_date>=curdate() and c.family_id='". $family_id."' ");
            
            foreach ($fd_maturity_list as $k=>$v)
            {
                //echo '<pre>';print_r($fd_maturity_list[$k]);die;
                $fd_maturity_list[$k]->maturity_amount = round($fd_maturity_list[$k]->maturity_amount);
            }
            
            $data['focus']['fixed_deposit']['maturity'] = $fd_maturity_list;
            
            $fd_interest_list = $this->fd->get_fixed_deposit_int("fdt.user_id = '".$brokerID."' and fdi.interest_date >= curdate() and fdt.fd_method ='Non-Cumulative' and c.family_id ='". $family_id."'");
            
            foreach ($fd_interest_list as $k=>$v)
            {
                //echo '<pre>';print_r($fd_maturity_list[$k]);die;
                $fd_interest_list[$k]->interest_amount = round($fd_interest_list[$k]->interest_amount);
            }
            
            $data['focus']['fixed_deposit']['interest'] = $fd_interest_list;
            
            
            
            $data['focus']['asset_liability']['upcoming_installment']= isset($summary['varUpcomingAssetsAndLiaDue'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUpcomingAssetsAndLiaDue'])):$this->common_lib->moneyFormatIndiaClient(0);
            $data['focus']['asset_liability']['installment_to_close']= isset($summary['varUpcomingAssetsAndLia'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varUpcomingAssetsAndLia'])):$this->common_lib->moneyFormatIndiaClient(0);
            
            
            $due_list = $this->al->get_asset_list_top_client("c.user_id='".$brokerID."' and c.family_id='".$family_id."' and maturity_date>= curdate()");
            
            foreach ($due_list as $k=>$v)
            {
                //echo '<pre>';print_r($due_list[$k]);die;
                $due_list[$k]->maturity_amount = round($due_list[$k]->maturity_amount);
            }
            
            $data['focus']['asset_liability']['dues'] = $due_list;
            
            $to_close_list = $this->al->get_asset_list_mat_client("c.user_id='".$brokerID."' and end_date>= curdate() and c.family_id='".$family_id."'");
            
            foreach ($to_close_list as $k=>$v)
            {
                //echo '<pre>';print_r($to_close_list[$k]);die;
                $to_close_list[$k]->installment_amount = round($to_close_list[$k]->installment_amount);
            }
            
            $data['focus']['asset_liability']['about_to_close'] = $to_close_list;
            
            
            $data['focus']['equity']['portfolio_value']= isset($summary['varTotalEquityPortfolio'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varTotalEquityPortfolio'])):$this->common_lib->moneyFormatIndiaClient(0);
            $data['focus']['equity']['debt_bal']= isset($summary['varDebitBal'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varDebitBal'])):$this->common_lib->moneyFormatIndiaClient(0);
            
        }
        else
        {
            $summary = $this->dash->get_summary_Dashboard_client($client_id,$brokerID);
        }
        
        
        /*$header['cl_list']=$this->dash->getTotalPortFolioModel($family_id,$brokerID);
        $data['mf_list_pur']=$this->ajax_list_purchase();
        $data['mf_list_redm']=$this->ajax_list_redemption();
        $data['ins_new_list']=$this->ajax_ins_list_new();
        $data['ins_mat_list']=$this->ajax_ins_list_mat();
        $data['fd_new_list']=$this->ajax_list_top_new();
        $data['fd_mat_list']=$this->ajax_list_top_int();
        $data['al_new_list']=$this->asset_ajax_list_top();
        $data['al_mat_list']=$this->asset_ajax_list_mat();*/

        $data['total_portfolio'] =  isset($summary['varTotal_portfolio'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varTotal_portfolio'])):$this->common_lib->moneyFormatIndiaClient(0);
        $data['liability'] =  isset($summary['varLiability'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varLiability'])):$this->common_lib->moneyFormatIndiaClient(0);
        $data['net_worth'] =  isset($summary['varNetWorth'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varNetWorth'])):$this->common_lib->moneyFormatIndiaClient(0);
        $data['total_life_cover'] =  isset($summary['varTotal_life_cover'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varTotal_life_cover'])):$this->common_lib->moneyFormatIndiaClient(0);
        
        $data['insurance']['total_life_cover'] = isset($summary['varTotal_life_cover'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varTotal_life_cover'])):$this->common_lib->moneyFormatIndiaClient(0);
        $data['insurance']['total_premium_paid'] = isset($summary['varInsuranceTotal'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varInsuranceTotal'])):$this->common_lib->moneyFormatIndiaClient(0);
        
        $data['insurance']['categories'] = array();
        
        $GeneralPaid = isset($summary['varGeneralPaid'])?intval($summary['varGeneralPaid']):0;
        $TraditionalPaid = isset($summary['varTraditionalPaid'])?intval($summary['varTraditionalPaid']):0;
        $UnitLikedPaid = isset($summary['varUnitLikedPaid'])?intval($summary['varUnitLikedPaid']):0;
        $pertotal=intval($GeneralPaid+$TraditionalPaid+$UnitLikedPaid);
        
        if(!empty($pertotal))
        {
          $PerGen=($GeneralPaid/$pertotal)*100;
          $PerTrad=($TraditionalPaid/$pertotal)*100;
          $PerUnit=($UnitLikedPaid/$pertotal)*100;
        }
        else
        {
            $PerGen=0;
            $PerTrad=0;
            $PerUnit=0;
        }
        $ins_categories[0] = array('label'=>'General','total_paid'=>$GeneralPaid,'allocation'=>sprintf("%.2f",$PerGen).'%');
        $ins_categories[1] = array('label'=>'Traditional ','total_paid'=>$TraditionalPaid,'allocation'=>sprintf("%.2f",$PerTrad).'%');
        $ins_categories[2] = array('label'=>'Unit-Linked','total_paid'=>$UnitLikedPaid,'allocation'=>sprintf("%.2f",$PerUnit).'%');
        
        $data['insurance']['categories'] =$ins_categories;
        //echo '<pre>';print_r($summary);die;
        $data['mutual_fund']['total_investment'] = isset($summary['varPurchase_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varPurchase_Amount'])):$this->common_lib->moneyFormatIndiaClient(0);
        $data['mutual_fund']['current_value'] = isset($summary['varCurrent_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['varCurrent_Amount'])):$this->common_lib->moneyFormatIndiaClient(0);
        $mfCurrAmt = isset($summary['varCurrent_Amount'])?intval($summary['varCurrent_Amount']):0;
        $mfPurAmt = isset($summary['varPurchase_Amount'])?intval($summary['varPurchase_Amount']):0;
        $mfProfit = $mfCurrAmt - $mfPurAmt;
        $data['mutual_fund']['profit'] = $this->common_lib->moneyFormatIndiaClient($mfProfit);
        $data['mutual_fund']['div_payout'] = isset($summary['div_payout_total_amount'])?$this->common_lib->moneyFormatIndiaClient(intval($summary['div_payout_total_amount'])):$this->common_lib->moneyFormatIndiaClient(0);
      
        $data['mutual_fund']['categories'] = array();
        
        $Debt = isset($summary['varDebt'])?intval($summary['varDebt']):0;
        $Equity = isset($summary['varEquity'])?intval($summary['varEquity']):0;
        $Hybrid = isset($summary['varHybrid'])?intval($summary['varHybrid']):0;
        $perMF=($Debt+$Equity+$Hybrid);

         if(!empty($perMF))
         {
           $varPerDebt=(($Debt/$perMF)*100);
           $varPerEquity=(($Equity/$perMF)*100);
           $varPerHybrid=(($Hybrid/$perMF)*100);

        }
        else 
        {
           $varPerDebt=0;
           $varPerEquity=0;
           $varPerHybrid=0;
        }
        
        $mf_category[0] = array('label'=>'Hybrid','total_amount'=>$Hybrid,'allocation'=>sprintf("%.2f",$varPerHybrid).'%');
        $mf_category[1] = array('label'=>'Debt','total_amount'=>$Debt,'allocation'=>sprintf("%.2f",$varPerDebt).'%');
        $mf_category[2] = array('label'=>'Equity','total_amount'=>$Equity,'allocation'=>sprintf("%.2f",$varPerEquity).'%');
        
        $data['mutual_fund']['categories'] = $mf_category;
        
        $data['portfolio']['fixed_deposit'] = isset($summary['varFDTotal'])?$this->common_lib->moneyFormatIndiaClient($summary['varFDTotal']):$this->common_lib->moneyFormatIndiaClient(0);
        $data['portfolio']['real_estate'] = isset($summary['varRETotal'])?$this->common_lib->moneyFormatIndiaClient($summary['varRETotal']):$this->common_lib->moneyFormatIndiaClient(0);
        $data['portfolio']['commodity'] = isset($summary['varCommodityTotal'])?$this->common_lib->moneyFormatIndiaClient($summary['varCommodityTotal']):$this->common_lib->moneyFormatIndiaClient(0);
        
        $data['assets']['categories'] = array();
        
        $TotalEQ=isset($summary['varTotalEquityPortfolio'])?intval($summary['varTotalEquityPortfolio']):intval(0);
        $TotalFD=isset($summary['varFDTotal'])?intval($summary['varFDTotal']):intval(0);
        $TotalComma=isset($summary['varCommodityTotal'])?intval($summary['varCommodityTotal']):intval(0);
        $TotalMF=isset($summary['varMFTotal'])?intval($summary['varMFTotal']):intval(0);
        $TotalIns=isset($summary['varInsuranceTotal'])?intval($summary['varInsuranceTotal']):intval(0);
        $TotalRE=isset($summary['varPropertyCurrent'])?intval($summary['varPropertyCurrent']):intval(0);
        $PerProductTotal=$TotalEQ+$TotalFD+$TotalMF+$TotalRE+$TotalComma+$TotalIns;
        
        if(!empty($PerProductTotal))
        {
            $perEQ= ($TotalEQ/$PerProductTotal)*100;
            $perFD= ($TotalFD/$PerProductTotal)*100;
            $perComma= ($TotalComma/$PerProductTotal)*100;
            $perMF= ($TotalMF/$PerProductTotal)*100;
            $perIns= ($TotalIns/$PerProductTotal)*100;
            $perRE= ($TotalRE/$PerProductTotal)*100;
            //echo '('.sprintf("%.2f",$perIns).'%)';
        }
        else 
        {
          $perEQ=0;
          $perFD=0;
          $perComma=0;
          $perMF=0;
          $perRE=0;
          $perIns=0;
         
        }
        
        $asset_category[0] = array('label'=>'Insurance','total_amount'=>$TotalIns,'allocation'=>sprintf("%.2f",$perIns).'%');
        $asset_category[1] = array('label'=>'Mutual Funds','total_amount'=>$TotalMF,'allocation'=>sprintf("%.2f",$perMF).'%');
        $asset_category[2] = array('label'=>'Fixed Deposit','total_amount'=>$TotalFD,'allocation'=>sprintf("%.2f",$perFD).'%');
        $asset_category[3] = array('label'=>'Equity','total_amount'=>$TotalEQ,'allocation'=>sprintf("%.2f",$perEQ).'%');
        $asset_category[4] = array('label'=>'Commodity','total_amount'=>$TotalComma,'allocation'=>sprintf("%.2f",$perComma).'%');
        $asset_category[5] = array('label'=>'Real Estate','total_amount'=>$TotalRE,'allocation'=>sprintf("%.2f",$perRE).'%');
        //$asset_category[6] = array('label'=>'Insurance','total_amount'=>$TotalIns,'allocation'=>sprintf("%.2f",$perIns).'%');
        
        $data['assets']['categories'] = $asset_category;
        
        $data['equity']['portfolio_value'] = isset($summary['varTotalEquityPortfolio'])?$this->common_lib->moneyFormatIndiaClient(round($summary['varTotalEquityPortfolio'])):$this->common_lib->moneyFormatIndiaClient(0);
        
        $data['equity']['holdings'] = array();
        //echo $summary['varTop1share'];die;
        $EQTop1=isset($summary['varTopQty1'])?intval($summary['varTopQty1']): 0; 
        $EQTop2=isset($summary['varTopQty2'])?intval($summary['varTopQty2']): 0; 
        $EQTop3=isset($summary['varTopQty3'])?intval($summary['varTopQty3']): 0; 
        $EQTop4=isset($summary['varTopQty4'])?intval($summary['varTopQty4']): 0; 
        $EQTop5=isset($summary['varTopQty5'])?intval($summary['varTopQty5']): 0;
        
        $EQTotal = isset($summary['varTotalEquityPortfolio']) ? round($summary['varTotalEquityPortfolio']) : $EQTop1+$EQTop2+$EQTop3+$EQTop4+$EQTop5;
        
        if(!empty($EQTotal)) 
        {
            $EQPerTop1=(($EQTop1/$EQTotal)*100);
            $EQPerTop2=(($EQTop2/$EQTotal)*100);
            $EQPerTop3=(($EQTop3/$EQTotal)*100);
            $EQPerTop4=(($EQTop4/$EQTotal)*100);
            $EQPerTop5=(($EQTop5/$EQTotal)*100);
        }
        
        $holding_counter = 0;
        
        if(isset($summary['varTop1share']) && !empty($EQTop1) && !empty($EQPerTop1))
        {
            $holdings[$holding_counter] = array('label'=>trim($summary['varTop1share']),'total_amount'=>$EQTop1,'allocation'=>sprintf("%.2f",$EQPerTop1).'%');
            $holding_counter++;
        }
        
        if(isset($summary['varTop2share']) && !empty($EQTop2) && !empty($EQPerTop2))
        {
            $holdings[$holding_counter] = array('label'=>trim($summary['varTop2share']),'total_amount'=>$EQTop2,'allocation'=>sprintf("%.2f",$EQPerTop2).'%');
            $holding_counter++;
        }
        
        if(isset($summary['varTop3share']) && !empty($EQTop3) && !empty($EQPerTop3))
        {
            $holdings[$holding_counter] = array('label'=>trim($summary['varTop3share']),'total_amount'=>$EQTop3,'allocation'=>sprintf("%.2f",$EQPerTop3).'%');
            $holding_counter++;
        }
        
        if(isset($summary['varTop4share']) && !empty($EQTop4) && !empty($EQPerTop4))
        {
            $holdings[$holding_counter] = array('label'=>trim($summary['varTop4share']),'total_amount'=>$EQTop4,'allocation'=>sprintf("%.2f",$EQPerTop4).'%');
            $holding_counter++;
        }
        
        if(isset($summary['varTop5share']) && !empty($EQTop5) && !empty($EQPerTop5))
        {
            $holdings[$holding_counter] = array('label'=>trim($summary['varTop5share']),'total_amount'=>$EQTop5,'allocation'=>sprintf("%.2f",$EQPerTop5).'%');
            $holding_counter++;
        }
        
        if($holding_counter > 0)
        {
            $data['equity']['holdings'] = $holdings;
        }
        else
        {
            $data['equity']['holdings'] = array();
        }
        
        //$arrImages = array(base_url()."assets/users/images/banner-new-1.jpg",base_url()."assets/users/images/banner-new-2.jpg",base_url()."assets/users/images/test.png");
        //return array('code'=>1,'client'=>$client,'carousel_images'=>$arrImages);
        
        $path    = 'assets/app/dashboard_images/';
        $files = scandir($path);
        
        $index = 0;
        $arrImages = array();
        
        for($i=0;$i<count($files);$i++)
        {
            if($files[$i]!='.' && $files[$i]!='..')
            {
                $arrImages[$index] = base_url().$path.$files[$i];
                $index++;
            }
        }
        return array('code'=>1,'summary'=>$data,'carousel_images'=>$arrImages);
    }
    
    private function getSummaryReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        //$family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'client';
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
             
        }
        $summary_rep = $this->report->get_summary_report($type, $where);
        //echo '<pre>';print_r($where);die;
        //$data['summary'] = $summary_rep;
        $summary_by_client = array();
        $summary_by_category = array();
        $total_portfolio=  0;
        $ins_total = 0;
        $fd_total=0;
        $mf_total=0;
        $eq_total=0;
        $re_total=0;
        $com_total=0;

        //$data['summary'] = array();
        $counter = 0;

        foreach ($summary_rep as $row)
        {
            //$data[$counter]  = $row;
            $ins = intval($row->insurance_inv);
            $ins_total = $ins_total + $ins;
            //$data[$counter]->insurance_inv = !empty($ins)?$this->common_lib->moneyFormatIndiaClient(round($row->insurance_inv,0)):'';

            $ins_fund = intval($row->insurance_fund);
            //$data[$counter]->insurance_fund = !empty($ins_fund)?$this->common_lib->moneyFormatIndiaClient(round($row->insurance_fund,0)):'';

            $fd = intval($row->fixed_deposit);
            $fd_total+= $fd;
            //$data[$counter]->fixed_deposit = !empty($fd)?$this->common_lib->moneyFormatIndiaClient(round($row->fixed_deposit,0)):'';

            $mf = intval($row->mutual_fund);
            $mf_total = $mf_total + $mf;
            //$data[$counter]->mutual_fund = !empty($mf)?$this->common_lib->moneyFormatIndiaClient(round($row->mutual_fund,0)):'';

            $eq = intval($row->equity);
            $eq_total = $eq_total + $eq;
            //$data[$counter]->equity = !empty($eq)?$this->common_lib->moneyFormatIndiaClient(round($row->equity,0)):'';

            $re = intval($row->property);
            $re_total+= $re;
            //$data[$counter]->property = !empty($re)?$this->common_lib->moneyFormatIndiaClient(round($row->property,0)):'';

            $commodity = intval($row->commodity);
            $com_total+= $commodity;
            //$data[$counter]->commodity = !empty($commodity)?$this->common_lib->moneyFormatIndiaClient(round($row->commodity,0)):'';

            $life_cover = intval($row->life_cover);
            //$data[$counter]->life_cover = !empty($life_cover)?$this->common_lib->moneyFormatIndiaClient(round($row->life_cover,0)):'';

            $client_total = $ins+$fd+$mf+$eq+$commodity+$re;

            //$summary_by_client[$row->client_name] = $client_total;

            //$data[$counter]->total_portfolio_formatted = $this->common_lib->moneyFormatIndiaClient(round($client_total),0);
            //$data[$counter]->total_portfolio = round($client_total,0);

            $total_portfolio = $total_portfolio + $client_total;

            //$counter++;
        }

        $categories = array();
        //echo $ins_total;die;
        //client wise % allocation
        $counter = 0;
        $client_allocation;
        foreach ($summary_rep as $row)
        {
            $data[$counter]  = $row;
            $ins = intval($row->insurance_inv);
            $data[$counter]->insurance_inv = !empty($ins)?$this->common_lib->moneyFormatIndiaClient(round($row->insurance_inv,0)):'';

            $ins_fund = intval($row->insurance_fund);
            $data[$counter]->insurance_fund = !empty($ins_fund)?$this->common_lib->moneyFormatIndiaClient(round($row->insurance_fund,0)):'';

            $fd = intval($row->fixed_deposit);
            $data[$counter]->fixed_deposit = !empty($fd)?$this->common_lib->moneyFormatIndiaClient(round($row->fixed_deposit,0)):'';

            $mf = intval($row->mutual_fund);
            $data[$counter]->mutual_fund = !empty($mf)?$this->common_lib->moneyFormatIndiaClient(round($row->mutual_fund,0)):'';

            $eq = intval($row->equity);
            $data[$counter]->equity = !empty($eq)?$this->common_lib->moneyFormatIndiaClient(round($row->equity,0)):'';

            $re = intval($row->property);
            $data[$counter]->property = !empty($re)?$this->common_lib->moneyFormatIndiaClient(round($row->property,0)):'';

            $commodity = intval($row->commodity);
            $data[$counter]->commodity = !empty($commodity)?$this->common_lib->moneyFormatIndiaClient(round($row->commodity,0)):'';

            $life_cover = intval($row->life_cover);
            $data[$counter]->life_cover = !empty($life_cover)?$this->common_lib->moneyFormatIndiaClient(round($row->life_cover,0)):'';

            $client_total = $ins+$fd+$mf+$eq+$commodity+$re;

            $summary_by_client[$row->client_name] = $client_total;

            $data[$counter]->total_portfolio_formatted = $this->common_lib->moneyFormatIndiaClient(round($client_total),0);
            $data[$counter]->total_portfolio = round($client_total,0);

            //$total_portfolio = $total_portfolio + $client_total;

            $allocation = ($client_total/$total_portfolio)*100;
            $data[$counter]->allocation = sprintf("%.2f",$allocation).'%';
            $counter++;
        }

        $counter = 0;

        $categories[$counter]['name'] = 'Insurance';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($ins_total),0);
        //echo $total_portfolio;die;
        $total_portfolio_val = intval($total_portfolio);
        
        if($total_portfolio_val)
        {
            $ins_per = ($ins_total/$total_portfolio)*100;
        }
        else
        {
            $ins_per = 0;
        }
        
        $categories[$counter]['allocation'] = sprintf("%.2f",$ins_per).'%';
        $counter++;

        $categories[$counter]['name'] = 'Fixed Income';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($fd_total),0);
        
        if($total_portfolio_val)
        {
             $fd_per = ($fd_total/$total_portfolio)*100;
        }
        else
        {
            $fd_per = 0;
        }
        
        //$fd_per = ($fd_total/$total_portfolio)*100;
        $categories[$counter]['allocation'] = sprintf("%.2f",$fd_per).'%';
        $counter++;

        $categories[$counter]['name'] = 'Mutual Funds';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($mf_total),0);
        
        if($total_portfolio_val)
        {
             $mf_per = ($mf_total/$total_portfolio)*100;
        }
        else
        {
            $mf_per = 0;
        }
        
        //$mf_per = ($mf_total/$total_portfolio)*100;
        $categories[$counter]['allocation'] = sprintf("%.2f",$mf_per).'%';
        $counter++;

        $categories[$counter]['name'] = 'Equity';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($eq_total),0);
        //$eq_per = ($eq_total/$total_portfolio)*100;
        if($total_portfolio_val)
        {
             $eq_per = ($eq_total/$total_portfolio)*100;
        }
        else
        {
            $eq_per = 0;
        }
        $categories[$counter]['allocation'] = sprintf("%.2f",$eq_per).'%';
        $counter++;

        $categories[$counter]['name'] = 'Real Estate';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($re_total),0);
        //$re_per = ($re_total/$total_portfolio)*100;
        if($total_portfolio_val)
        {
             $re_per = ($re_total/$total_portfolio)*100;
        }
        else
        {
            $re_per = 0;
        }
        $categories[$counter]['allocation'] = sprintf("%.2f",$re_per).'%';
        $counter++;

        $categories[$counter]['name'] = 'Commodity';
        $categories[$counter]['total'] = $this->common_lib->moneyFormatIndiaClient(round($com_total),0);
        //$com_per = ($com_total/$total_portfolio)*100;
        if($total_portfolio_val)
        {
             $com_per = ($com_total/$total_portfolio)*100;
        }
        else
        {
            $com_per = 0;
        }
        $categories[$counter]['allocation'] = sprintf("%.2f",$com_per).'%';
        $counter++;

        $summary['members'] = $data;
        $summary['categories'] = $categories;
        //$summary['client_allocation'] = $client_allocation;
        $summary['total_portfolio'] = $total_portfolio;
        $summary['total_portfolio_formatted'] = $this->common_lib->moneyFormatIndiaClient(round($total_portfolio),0);
        
        
        return array('code'=>1,'summary'=>$summary);
    }
    
    private function getMFReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'clientID'=> ''
            );
        }
        else
        {
            $type = 'client';
            $where = "";
            
            $where = array(
                'familyID'=> '',
                'brokerID'=> $brokerID,
                'clientID'=> $client_id
              );

        }
        
        $mf_rep_data=$this->mf->get_clientwise_details($type,$where);
        $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where);
        $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where);
        $total = array('investment'=>0,'current_value'=>0,'cagr'=>0);
        
        //echo '<pre>';print_r($mf_comman_scheme_summary);die;
        $client_total = array();
        $client_scheme_total = array();
        $client_id_name = array();
        
        $counter = 0;
        
        $cagr1 = 0; $cagr2 = 0; $abs1 = 0;$fTotalCagr1 = 0; $fTotalCagr2 = 0;$fTotal_abs = 0; $fTotal_absDivide = 0;
        
        if(count($mf_rep_data) > 0)
        {
            foreach ($mf_rep_data as $row)
            {
                //echo '<pre>';print_r($row);die;
                if(!empty($row->p_amount))
                {
                    //if($row->scheme_type == 'EQUITY') $equity_data['current_value']+= $row->p_amount;
                    $total['investment']+= $row->p_amount;
                }
                if(!empty($row->current_value))
                {
                    //if($row->scheme_type == 'EQUITY') $equity_data['current_value']+= $row->current_value;
                    $total['current_value']+= $row->current_value;
                }
                /*if(!empty($row->cagr))
                {
                    $total['cagr']+= $row->cagr;
                }*/

                if($row->mf_scheme_type != 'DIV')
                {
                    $purAmt = $row->live_unit * $row->p_nav;
                    $divAmt = 0;
                }
                else {
                    $divAmt = $row->live_unit * $row->p_nav;
                    $purAmt = 0;
                }
                if($row->cagr != null)
                    $cagr1 = ($purAmt + $divAmt) * $row->cagr * $row->transaction_day;
                else
                    $cagr1 = 0;

                if($row->mf_abs != null)
                {
                    $cagr2 = ($purAmt + $divAmt) * $row->transaction_day;
                    $abs1 = ($purAmt + $divAmt) * $row->mf_abs;
                } else {
                    $abs1 = 0;
                }

                $fTotalCagr1 = $cagr1 + $fTotalCagr1;
                $fTotalCagr2 = $cagr2 + $fTotalCagr2;
                $client_id_name[$row->client_id] = $row->client_name;

                if(array_key_exists($row->client_id, $client_total))
                {
                   $client_total[$row->client_id]['investment']+= $row->p_amount; 
                   $client_total[$row->client_id]['current_value']+= $row->current_value; 
                   $client_total[$row->client_id]['cagr1']+= $cagr1; 
                   $client_total[$row->client_id]['cagr2']+= $cagr2; 
                   $client_total[$row->client_id]['abs_return']+= $row->cagr; 
                }
                else
                {
                    $client_total[$row->client_id]['investment']= $row->p_amount; 
                    $client_total[$row->client_id]['current_value']= $row->current_value; 
                    $client_total[$row->client_id]['cagr1']= $cagr1;
                    $client_total[$row->client_id]['cagr2']= $cagr2;
                    $client_total[$row->client_id]['abs_return']= $row->cagr; 
                }

                $units = $row->live_unit;
                if(array_key_exists($row->client_id.$row->mf_scheme_name, $client_scheme_total))
                {
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['investment']+= $row->p_amount; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['current_value']+= $row->current_value; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['cagr1']+= $cagr1;
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['cagr2']+= $cagr2;
                   //$client_scheme_total[$row->client_id.$row->mf_scheme_name]['abs_return']+= $row->cagr; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['purchase_amount']+= $purAmt; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_amount']+= $divAmt; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_r2_amount']+= $row->div_r2; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_payout']+= $row->div_payout; 
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['abs1']+= $abs1;
                   //$units = intval($row->live_unit);
                   $client_scheme_total[$row->client_id.$row->mf_scheme_name]['bal_units']+= $units;
                   if(!in_array($row->folio_number, $client_scheme_total[$row->client_id.$row->mf_scheme_name]['folio_numbers']))
                   {
                       array_push($client_scheme_total[$row->client_id.$row->mf_scheme_name]['folio_numbers'], $row->folio_number);
                   }
                   
                   //$client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_amount']+= $divAmt;
                   //$client_scheme_total[$row->client_id.$row->mf_scheme_name]['scheme_name']+= $row->mf_scheme_name; 
                }
                else
                {
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['investment']= $row->p_amount; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['current_value']= $row->current_value; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['cagr1']= $cagr1; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['cagr2']= $cagr2; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['scheme_name']= $row->mf_scheme_name; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['client_id']= $row->client_id; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['purchase_amount']= $purAmt; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_amount']= $divAmt; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['abs1']= $abs1; 
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['bal_units']= $units;
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['scheme_type']= $row->scheme_type;
                    //$client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_amount']= $divAmt;
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_r2_amount']= $row->div_r2;
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['div_payout']= $row->div_payout;
                    $client_scheme_total[$row->client_id.$row->mf_scheme_name]['folio_numbers'][0]= $row->folio_number;

                }

                $counter++;

            }
            
            
            $data['fundwise_summary'] = $mf_summary_typewise;
            $data['schemewise_summary'] = $mf_comman_scheme_summary;
            //echo '<pre>';print_r($mf_summary_typewise);die;
            if($fTotalCagr2 != 0)
                    $fTotalCagr = round(($fTotalCagr1 / $fTotalCagr2), 2);
                else
                    $fTotalCagr = round($fTotalCagr1, 2);

            $total['cagr'] = sprintf("%.2f",$fTotalCagr).'%';

            //$total['cagr'] = $total['cagr'] / $counter;

            //echo '<pre>';print_r($total);die;
            //echo '<pre>';print_r($client_total);die;
            //echo '<pre>';print_r($mf_rep_data);die;

            $data['total_investment'] = $this->common_lib->moneyFormatIndiaClient(round($total['investment']));
            $data['total_current_value'] = $this->common_lib->moneyFormatIndiaClient(round($total['current_value']));
            $data['cagr'] = $total['cagr'];
            
            foreach ($mf_summary_typewise as $key=>$val)
            {
                //echo $key;die;
                if(!empty($val->current_value))
                {
                    $per = round((100 * $val->current_value) / $total['current_value'],2);
                    $mf_summary_typewise[$key]->current_value = $this->common_lib->moneyFormatIndiaClient(round($val->current_value));
                    $mf_summary_typewise[$key]->allocation = sprintf("%.2f",$per).'%';
                }
                else
                {
                    $mf_summary_typewise[$key]->current_value = '';
                    $mf_summary_typewise[$key]->allocation = '';
                }
            }
            
            foreach ($mf_comman_scheme_summary as $key=>$val)
            {
                //echo $key;die;
                if(!empty($val->purchase_amount))
                {
                    //$per = round((100 * $val->current_value) / $total['current_value'],2);
                    $mf_comman_scheme_summary[$key]->purchase_amount = $this->common_lib->moneyFormatIndiaClient(round($val->purchase_amount));
                    //$mf_summary_typewise[$key]->allocation = sprintf("%.2f",$per).'%';
                }
                else
                {
                    $mf_comman_scheme_summary[$key]->purchase_amount = '';
                    //$mf_summary_typewise[$key]->allocation = '';
                }
                
                if(!empty($val->current_value))
                {
                    $per = round((100 * $val->current_value) / $total['current_value'],2);
                    $mf_comman_scheme_summary[$key]->current_value = $this->common_lib->moneyFormatIndiaClient(round($val->current_value));
                    $mf_comman_scheme_summary[$key]->allocation = sprintf("%.2f",$per).'%';
                }
                else
                {
                    $mf_comman_scheme_summary[$key]->current_value = '';
                    $mf_comman_scheme_summary[$key]->allocation = '';
                }
                
                if(!empty($val->cagr))
                {
                    //$per = round((100 * $val->current_value) / $total['current_value'],2);
                    $mf_comman_scheme_summary[$key]->cagr = sprintf("%.2f",$val->cagr);
                    //$mf_summary_typewise[$key]->allocation = sprintf("%.2f",$per).'%';
                }
                else
                {
                    $mf_comman_scheme_summary[$key]->cagr = '';
                    //$mf_summary_typewise[$key]->allocation = '';
                }
                
                if(!empty($val->abs))
                {
                    //$per = round((100 * $val->current_value) / $total['current_value'],2);
                    $mf_comman_scheme_summary[$key]->abs = sprintf("%.2f",$val->abs);
                    //$mf_summary_typewise[$key]->allocation = sprintf("%.2f",$per).'%';
                }
                else
                {
                    $mf_comman_scheme_summary[$key]->abs = '';
                    //$mf_summary_typewise[$key]->allocation = '';
                }
            }
            
            
            $counter = 0;

            $client_data = array();
            foreach ($client_total as $key=>$val)
            {
                $client_data[$counter] = array(
                                                'client_id'=>$key,
                                                'name'=>$client_id_name[$key],
                                                'investment'=>$this->common_lib->moneyFormatIndiaClient(round($client_total[$key]['investment'])),
                                                'current_value'=>$this->common_lib->moneyFormatIndiaClient(round($client_total[$key]['current_value']))
                                               );

                if($client_total[$key]['cagr2'] != 0)
                    $cagr = round(($client_total[$key]['cagr1'] / $client_total[$key]['cagr2']), 2);
                else
                    $cagr = round($client_total[$key]['cagr1'], 2);

                $client_data[$counter]['cagr'] = sprintf("%.2f",$cagr).'%';

                $scheme_counter = 0;
                foreach ($client_scheme_total as $scheme_key=>$scheme_val)
                {
                    if($client_scheme_total[$scheme_key]['client_id'] == $key)
                    {
                        if(!empty($client_scheme_total[$scheme_key]['div_amount']))
                        {
                            $divTot = $this->common_lib->moneyFormatIndiaClient(round($client_scheme_total[$scheme_key]['div_amount']));
                        }
                        else
                        {
                            $divTot ='';
                        }
                        
                        if(!empty($client_scheme_total[$scheme_key]['div_r2_amount']))
                        {
                            $divR2 = $this->common_lib->moneyFormatIndiaClient(round($client_scheme_total[$scheme_key]['div_r2_amount']));
                        }
                        else
                        {
                            $divR2 ='';
                        }
                        
                        if(!empty($client_scheme_total[$scheme_key]['div_payout']))
                        {
                            $divPayout = $this->common_lib->moneyFormatIndiaClient(round($client_scheme_total[$scheme_key]['div_payout']));
                        }
                        else
                        {
                            $divPayout ='';
                        }
                        
                        $client_data[$counter]['schemes'][$scheme_counter] = array(
                                                'name'=>$client_scheme_total[$scheme_key]['scheme_name'],
                                                'investment'=>$this->common_lib->moneyFormatIndiaClient(round($client_scheme_total[$scheme_key]['investment'])),
                                                'current_value'=>$this->common_lib->moneyFormatIndiaClient(round($client_scheme_total[$scheme_key]['current_value'])),
                                                'type'=>$client_scheme_total[$scheme_key]['scheme_type'],
                                                'div_total'=>$divTot,
                                                'div_r2'=>$divR2,
                                                'div_payout'=>$divPayout,
                                                'folio_numbers'=> implode(', ',$client_scheme_total[$scheme_key]['folio_numbers'])
                                               );

                        
                        if($client_scheme_total[$scheme_key] != 0)
                            $scheme_cagr = round(($client_scheme_total[$scheme_key]['cagr1'] / $client_scheme_total[$scheme_key]['cagr2']), 2);
                        else
                            $scheme_cagr = round($client_scheme_total[$scheme_key]['cagr1'], 2);

                        $client_data[$counter]['schemes'][$scheme_counter]['cagr'] = sprintf("%.2f",$scheme_cagr).'%';

                        if($client_scheme_total[$scheme_key]['purchase_amount'] != 0)
                        {
                            $fTotal_abs = round(($client_scheme_total[$scheme_key]['abs1'] / ($client_scheme_total[$scheme_key]['purchase_amount'] + $client_scheme_total[$scheme_key]['div_amount'])), 2);
                        }
                        elseif($client_scheme_total[$scheme_key]['div_amount'] != 0)
                        {
                            /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
                            $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;
                            $fTotal_abs = $fTotalAbs1 + $fTotalDivAmt;*/
                            $fTotal_abs = round(($client_scheme_total[$scheme_key]['abs1'] / $client_scheme_total[$scheme_key]['div_amount']), 2);
                        } else {
                            $fTotal_abs = 0;
                        }

                        $client_data[$counter]['schemes'][$scheme_counter]['abs_return'] = sprintf("%.2f",$fTotal_abs).'%';
                        $client_data[$counter]['schemes'][$scheme_counter]['bal_units'] = round($client_scheme_total[$scheme_key]['bal_units'],2);
                        $scheme_counter++;
                    }
                }
                $counter++;
            }
            
            $data['members'] = $client_data;
   
            return array('code'=>1,'summary'=>$data);

        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }        
    }
    
    private function getDocTypes($objRequest)
    {
        $types = $this->Doc_model->getDocs();
        $allowed_types = 'jpg|jpeg|gif|png|pdf|doc|docx|pptx|xls|xlsx|csv|ppt|TIFF|bmp|svg|odt|dos|odp';
        $arrAllowed = explode('|',$allowed_types);
        return array('code'=>1,'types'=> array_keys($types),'allowed_filetypes'=>$arrAllowed);
    }
    
    private function uploadDoc($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        
        $category = $objRequest->category;
        
        if (!file_exists('uploads/clients/'.$client->client_id.'/'.$category))
        {
            mkdir('uploads/clients/'.$client->client_id.'/'.$category, 0755, true);
        }
        
        $filepath = 'uploads/clients/'.$client->client_id.'/'.$category.'/';
        
        $allowed_types = 'jpg|jpeg|gif|png|pdf|doc|docx|pptx|xls|xlsx|csv|ppt|TIFF|bmp|svg|odt|dos|odp';
        $arrAllowed = explode('|',$allowed_types);
        
        $fileExtension = pathinfo($objRequest->filename, PATHINFO_EXTENSION);
        
        if(!in_array($fileExtension, $arrAllowed))
        {
            return array('code'=>-1,'msg'=>'Invalid file type.');
        }
        else
        {
            $result = file_put_contents($filepath.$objRequest->filename, base64_decode($objRequest->content));
            
            if($result == FALSE)
            {
               return array('code'=>-1,'msg'=>'Document upload error.Please try again.'); 
            }
            else
            {
                //add reminder
                $reminder = array(
                  'reminder_type'=> 'Client',
                   'client_id' => $client->client_id ,
                   'client_name' => $client->name ,
                   'reminder_date' => date('Y-m-d'),
                   'reminder_message'=> 'Document approval - '.$category.' - '.$objRequest->filename,
                   'concern_user'=>$client->username,
                   'broker_id' => $client->user_id
                );
                $this->db->insert('today_reminders', $reminder);
                return array('code'=>1);
            }
        }
    }
    
    private function getInsuranceReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
            );
        }
        else
        {
            $type = 'client';
            //$where = "";
            
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
              );//echo '<pre>';print_r($where);die;

        }
        
        $ins_rep = $this->ins->get_insurance_report($type, $where);
        $gen_ins_rep = $this->ins->get_general_insurance_report($type, $where);
        //echo '<pre>';print_r($gen_ins_rep);die;
        /*if(count($gen_ins_rep) > 0)
        {
            foreach ($gen_ins_rep as $res)
            {
                array_push($ins_rep, $res);
            }
        }*/
        //$result = array_merge($ins_rep,array($gen_ins_rep));
        if(count($ins_rep) > 0)
        {
            //echo '<pre>';print_r($ins_rep);die;
            $total = array('total_sum_assured'=>0,'total_premium_paid'=>0);
            $client_total = array();
            $client_ins_records = array();
            foreach ($ins_rep as $row)
            {
                if(!empty($row->amt_insured))
                {
                    $total['total_sum_assured']+= $row->amt_insured;
                }
                if(!empty($row->prem_paid_till_date))
                {
                    $total['total_premium_paid']+= $row->prem_paid_till_date;
                }

                if(array_key_exists($row->name, $client_total))
                {
                    $client_total[$row->name]['total_sum_assured'] += $row->amt_insured;
                    $client_total[$row->name]['total_premium_paid'] += $row->prem_paid_till_date;
                }
                else
                {
                    $client_total[$row->name]['total_sum_assured'] = $row->amt_insured;
                    $client_total[$row->name]['total_premium_paid'] = $row->prem_paid_till_date;
                }
                
                $ins_data = array();
                
                $client_ins_data['company'] = $row->ins_comp_name;
                $client_ins_data['plan'] = $row->plan_name;
                if(!empty($row->prem_amt))
                {
                    $client_ins_data['prem_amt'] = $this->common_lib->moneyFormatIndiaClient(round($row->prem_amt));
                }
                else
                {
                    $client_ins_data['prem_amt'] = '';
                }
                
                if(!empty($row->amt_insured))
                {
                    $client_ins_data['amt_insured'] = $this->common_lib->moneyFormatIndiaClient(round($row->amt_insured));
                }
                else
                {
                    $client_ins_data['amt_insured'] = '';
                }
                
                if(!empty($row->prem_paid_till_date))
                {
                    $client_ins_data['prem_paid'] = $this->common_lib->moneyFormatIndiaClient(round($row->prem_paid_till_date));
                }
                else
                {
                    $client_ins_data['prem_paid'] = '';
                }
                
                //$client_ins_data['amt_insured'] = $row->plan_name;
                $client_ins_data['commence_date'] = date('d-M-Y',strtotime($row->commence_date));
                $client_ins_data['policy_num'] = $row->policy_num;
                $client_ins_data['next_prem_due_date'] = date('d-M-Y',strtotime($row->next_prem_due_date));
                $client_ins_data['maturity_date'] = date('d-M-Y',strtotime($row->maturity_date));
                $client_ins_data['benefit_term'] = $row->benefit_term;
                $client_ins_data['PPT'] = $row->PPT;
                $client_ins_data['mode']=$row->mode_name;
                $client_ins_data['mode_abbr']='('.substr($row->mode_name, 0,1).')';
                if($row->remaining_PPT > 0)
                {
                    $client_ins_data['remaining_PPT'] = $row->remaining_PPT;
                }
                else
                {
                    $client_ins_data['remaining_PPT'] = '';
                }
                $client_ins_data['status'] = $row->status;
                $client_ins_data['nominee'] = $row->nominee;
                $client_ins_data['adjustment'] = $row->adjustment;
                //$client_ins_data['prem_paid'] = $row->adjustment;
                
                if(array_key_exists($row->name, $client_ins_records))
                {
                    array_push($client_ins_records[$row->name], $client_ins_data);
                }
                else
                {
                    $client_ins_records[$row->name][0] = $client_ins_data;
                }
            }

             $data['total_sum_assured'] = $this->common_lib->moneyFormatIndiaClient(round($total['total_sum_assured']));
             $data['total_premium_paid'] = $this->common_lib->moneyFormatIndiaClient(round($total['total_premium_paid']));

             $counter = 0;
             
             //echo '<pre>';print_r($client_ins_records);die;
             $client_data = array();
             foreach($client_total as $key=>$val)
             {
                 $client_data[$counter]['name'] = $key;
                 $client_data[$counter]['total_sum_assured'] = $this->common_lib->moneyFormatIndiaClient(round($val['total_sum_assured']));
                 $client_data[$counter]['total_premium_paid'] = $this->common_lib->moneyFormatIndiaClient(round($val['total_premium_paid'])); 
                 
                 
                 $j = 0;
                 foreach($client_ins_records[$key] as $ins)
                 {
                    // echo '<pre>';print_r($ins);
                     
                     $client_data[$counter]['plans'][$j] = $ins;
                     $j++;
                 }
                 
                 $counter++;
             }
             
            $data['members'] = $client_data;
            
            if(count($gen_ins_rep) > 0)
            {
                foreach ($gen_ins_rep as $k=>$v)
                {
                    //echo '<pre>';print_r($v);die;
                    $gen_ins_rep[$k]->company = $v->ins_comp_name;
                    $gen_ins_rep[$k]->plan = $v->plan_name;
                    if(!empty($v->prem_amt))
                    {
                        $gen_ins_rep[$k]->prem_amt = $this->common_lib->moneyFormatIndiaClient(round($v->prem_amt));
                    }
                    else
                    {
                        $gen_ins_rep[$k]->prem_amt = '';
                    }

                    if(!empty($v->amt_insured))
                    {
                       $gen_ins_rep[$k]->amt_insured = $this->common_lib->moneyFormatIndiaClient(round($v->amt_insured));
                    }
                    else
                    {
                        $gen_ins_rep[$k]->amt_insured = '';
                    }

                    if(!empty($v->prem_paid_till_date))
                    {
                        $gen_ins_rep[$k]->prem_paid = $this->common_lib->moneyFormatIndiaClient(round($v->prem_paid_till_date));
                    }
                    else
                    {
                        $gen_ins_rep[$k]->prem_paid = '';
                    }

                    //$client_ins_data['amt_insured'] = $row->plan_name;
                    $gen_ins_rep[$k]->commence_date = date('d-M-Y',strtotime($v->commence_date));
                    $gen_ins_rep[$k]->policy_num = $v->policy_num;
                    $gen_ins_rep[$k]->next_prem_due_date = date('d-M-Y',strtotime($v->next_prem_due_date));
                    $gen_ins_rep[$k]->maturity_date = date('d-M-Y',strtotime($v->maturity_date));
                    $gen_ins_rep[$k]->benefit_term = $v->benefit_term;
                    $gen_ins_rep[$k]->PPT = $v->PPT;
                    $gen_ins_rep[$k]->mode=$v->mode_name;
                    $gen_ins_rep[$k]->mode_abbr='('.substr($v->mode_name, 0,1).')';
                    if($v->remaining_PPT > 0)
                    {
                        $gen_ins_rep[$k]->remaining_PPT = $v->remaining_PPT;
                    }
                    else
                    {
                       $gen_ins_rep[$k]->remaining_PPT = '';
                    }
                    $gen_ins_rep[$k]->status = $v->status;
                    $gen_ins_rep[$k]->nominee = $v->nominee;
                    $gen_ins_rep[$k]->adjustment = $v->adjustment;
                }
                
                
            }
            
            $data['general_insurance_list'] = $gen_ins_rep;
          
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getInsPremCal($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        $prem_date = date('m/Y');//$this->input->post('prem_date');
        //$prem_date = date('d').'/'.$prem_date;
        $prem_date = '01'.'/'.$prem_date;
        $prem_date_temp = DateTime::createFromFormat('d/m/Y', $prem_date);
        $rep_date_temp = $prem_date_temp;
        $prem_date = $prem_date_temp->format('Y-m-d');
        $prem_date_temp = $prem_date_temp->format('M');

        //echo date('M',strtotime($prem_date));die;
        $where = "";
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            
            $type = 'family';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'familyID'=> $family_id
            );
            $whereLapse = array('familyID' => $family_id);
            
        }
        else
        {
            $type = 'client';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'clientID'=> $client_id
            );
            $whereLapse = array('clientID' => $client_id);
        }
      
        $prem_rep = $this->ins->get_premium_calendar_report($type, $where);
        $lapse_rep = $this->ins->get_lapse_policy_report($type, $whereLapse);
        
        //echo '<pre>';print_R($prem_rep);die;
        //echo '<pre>';print_R($lapse_rep);die;
        
        
        $arrMonthKeys = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        
        $data['calendar'] = array();
        $total = 0;
        
        if(count($prem_rep) > 0)
        {
            $arrPolicyNumbers = array();
            foreach ($prem_rep as $k=>$v)
            {
                if(!in_array($v->policy_num, $arrPolicyNumbers))
                {
                    $arrPolicyNumbers[] = $v->policy_num;
                }
            }
            
            //echo '<pre>';print_r($arrPolicyNumbers);die;
            
            //get plan names by policy numbers
            $this->db->select('policy_num,plan_name');
            $this->db->from('insurance_policies');
            $this->db->join('ins_plans', 'insurance_policies.plan_id = ins_plans.plan_id', 'inner');
            $this->db->where_in('insurance_policies.policy_num',$arrPolicyNumbers);
            //$this->db->order_by("premium_maturities.maturity_date asc");
            //$this->db->limit(5);
            $query = $this->db->get();
            $plans = array();
            foreach ($query->result() as $k=>$v)
            {
                $plans[$v->policy_num] = $v->plan_name;
            }
            //echo '<pre>';print_R($plans);die;
            for($i=0;$i<12;$i++)
            {
                $month = date('M',strtotime($prem_date));
                foreach ($prem_rep as $row)
                {
                    $arrRow = (array)$row;
                    
                    foreach ($arrRow as $key => $val)
                    {
                        if($month == $key)
                        {
                            $prem_amt = intval($val);

                            if(!empty($prem_amt))
                            {
                                $total+= $val;

                                if(array_key_exists($key, $data['calendar']))
                                {
                                     $data['calendar'][$key]['total']+= $prem_amt;
                                     $data['calendar'][$key]['total_formatted'] = $this->common_lib->moneyFormatIndiaClient(round($data['calendar'][$key]['total']));
                                }
                                else
                                {
                                    $data['calendar'][$key]['total'] = $prem_amt;
                                    $data['calendar'][$key]['total_formatted'] = $this->common_lib->moneyFormatIndiaClient(round($prem_amt));
                                }
                                
                                if($arrRow['adjustment'] == '1')
                                {
                                    $amt = $this->common_lib->moneyFormatIndiaClient(round($prem_amt)) . ' (ADJ)';
                                }
                                else
                                {
                                    $amt = $this->common_lib->moneyFormatIndiaClient(round($prem_amt));
                                }
                                if(array_key_exists($arrRow['policy_num'],$plans))
                                {
                                    $plan_name = $plans[$arrRow['policy_num']];
                                }
                                else
                                {
                                    $plan_name='';
                                }
                                $data['calendar'][$key]['data'][] = array('member'=>$arrRow['name'],'company'=>$arrRow['ins_comp_name'],'policy_no'=>$arrRow['policy_num'],'amount'=>$amt,'plan'=>$plan_name);
                                
                                
                            }
                        }
                    }
                }
                $prem_date = date('Y-m-d',strtotime("+1 months", strtotime($prem_date)));
            }
            
            
            foreach ($data['calendar'] as $k => $v)
            {
                $v['month'] = $k;
                $data['premium_calendar'][] = $v;
            }
            
            unset($data['calendar']);
        }
        
        if(count($lapse_rep) > 0)
        {
            $total_lapse = 0;
            //echo '<pre>';print_r($lapse_rep);die;
            
            foreach ($lapse_rep as $lapse)
            {
                $amt_insured = floatval($lapse->amt_insured);
                $prem_amount = floatval($lapse->prem_amt);
                if(!empty($amt_insured))
                {
                    $total_lapse+= $lapse->amt_insured;
                    $amt_insured = $this->common_lib->moneyFormatIndiaClient(round($amt_insured));
                }
                else
                {
                    $amt_insured = '';
                }
                
                if(!empty($prem_amount))
                {
                    $prem_amount = $this->common_lib->moneyFormatIndiaClient(round($prem_amount));
                }
                else
                {
                    $prem_amount = '';
                }
                
                $data['lapse_policy']['data'][] = array(
                                                        'member'=>$lapse->name,
                                                        'company'=>$lapse->ins_comp_name,
                                                        'plan'=>$lapse->plan_name,
                                                        'sum_assured'=>$amt_insured,
                                                        'prem_amount'=>$prem_amount,
                                                        'nominee'=>'',
                                                        'start_date'=>date('d-M-Y',strtotime($lapse->commence_date)),
                                                        'prem_pending_from'=>date('d-M-Y',strtotime($lapse->next_prem_due_date)),
                                                        'mode'=>$lapse->mode_name,
                                                        'adj'=>$lapse->adjustment,
                                                        'policy_num' => $lapse->policy_num
                                                    );
            }
            
            $data['lapse_policy']['total'] = $this->common_lib->moneyFormatIndiaClient(round($total_lapse));
        }
        
        $data['calendar']['total'] = $this->common_lib->moneyFormatIndiaClient(round($total));
        //echo '<pre>';print_r($data);
        return array('code'=>1,'summary'=>$data);
        
    }
    
    private function getFDReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
            
        }
        else
        {
            $type = 'client';
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        
        $fd_rep = $this->fd->get_fixed_deposit_report($type, $where);
        
        //echo '<pre>';print_r($fd_rep);die;
        if(count($fd_rep) > 0)
        {
            $total_invested = 0;
            $total_maturity = 0;
            
            $client_total = array();
            $client_data = array();
            $client_id_name = array();

            $counter = 0;
            foreach($fd_rep as $row)
            {
                $invested = intval($row->amount_invested);
                $maturity = intval($row->maturity_amount);
                
                if(!empty($invested))
                {
                    $total_invested+= $invested;
                }
                
                if(!empty($maturity))
                {
                    $total_maturity+= $maturity;
                }
                
                //$client_id_name[$row->client_id] = $row->client_name;

                if(array_key_exists($row->client_name, $client_total))
                {
                   $client_total[$row->client_name]['investment']+= $invested;
                   $client_total[$row->client_name]['maturity']+= $maturity;
                }
                else
                {
                   $client_total[$row->client_name]['investment']= $invested;
                   $client_total[$row->client_name]['maturity']= $maturity; 
                }
                
                $tempDate = DateTime::createFromFormat('d/m/Y',$row->issued_date);
                $row->issued_date = $tempDate->format('d-M-Y');
                $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->maturity_date);
                $row->maturity_date = $tempDate2->format('d-M-Y');
                
                $row->amount_invested = $this->common_lib->moneyFormatIndiaClient(round($row->amount_invested));
                $row->maturity_amount = $this->common_lib->moneyFormatIndiaClient(round($row->maturity_amount));
                if(array_key_exists($row->client_name, $client_data))
                {
                    array_push($client_data[$row->client_name], $row);
                }
                else
                {
                    $client_data[$row->client_name][0] = $row;
                }
            }
            
            $members = array();
            
            foreach ($client_data as $k=>$val)
            {
                $member = array();
                $member['name'] = $k;
                $member['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['investment']));
                $member['total_maturity'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['maturity']));
                $member['deposits'] = $val;
                $members[] = $member;
            }
            
            $data['members'] = $members;
            //echo '<pre>';print_r($client_data);die;
            
            $data['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($total_invested));
            $data['total_maturity'] = $this->common_lib->moneyFormatIndiaClient(round($total_maturity));
            
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
        
    }
    
    private function getClientReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        
        $clientID = $objRequest->client_id;
        
        $bank_accounts = $this->Bank_accounts_model->get_client_bank_accounts('ba.client_id = "'.$clientID.'"');
        $demat_accounts = $this->Demat_accounts_model->get_client_demat_accounts($clientID);
        $policies = $this->ins->get_client_policies($clientID);
        $tradings = $this->client->get_client_tradings($clientID);
        $contacts = $this->client->get_client_contacts($clientID);
        $documents = $this->list_all_files('uploads/clients/'.$clientID);
        
        $data['name'] = $client->name;
        $data['pan'] = $client->pan_no;
        $data['hof_relation'] = $client->relation_HOF;
        if($client->dob) {
            $dobTemp = DateTime::createFromFormat('Y-m-d',$client->dob); $dob = $dobTemp->format('d/m/Y');
        } else {
            $dob = '';
        }
        $data['dob'] = $dob;
        $data['passport_no'] = $client->passport_no;
        $data['mobile'] = $client->mobile;
        $data['address'] = $client->add_flat.' '.$client->add_street.' '.$client->add_area.' '.$client->add_city.' '.$client->add_state.' - '.$client->add_pin;
       
        $data['bank_accounts'] = $bank_accounts;
        $data['demat_accounts'] = $demat_accounts;
        //$data['policies'] = $policies;
        $data['trading_accounts'] = $tradings;
        $data['contacts'] = $contacts;
        if(is_array($documents))
        {
            $data['documents'] = (array)$documents;
        }
        else
        {
            $data['documents'] = array();
        }
        
        //echo '<pre>';print_r(count($documents));die;
        return array('code'=>1,'info'=>$data);
    }
    
    private function list_all_files($path) {
        if(file_exists($path)) {
            $files = array();
            $files_new = array();
            $i = 0;
            $fileinfos = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );
            foreach($fileinfos as $pathname => $fileinfo) {
                if (!$fileinfo->isFile()) continue;
                //$files[$i][] = $fileinfo->getFilename();
                $type = str_replace('/'.$fileinfo->getFilename(),'', str_replace($path.'/','', str_replace('\\','/',$pathname)));
                $files[$i]['type'] = $type;
                $files[$i]['path'] = base_url(str_replace('\\','/',$pathname));
                //$files[$i]['filename'] = $fileinfo->getFilename();
                $files[$i]['filename'] = $fileinfo->getFilename();

                $files_new[] = $files[$i];
               
                if($type == $fileinfo->getFilename()) { unset($files[$i]); }   /* if the names are same, then its a photo, so delete the array */

                $i++;
            }
        } else {
            // client folder does not exist OR path not found
            return 0;
        }
        return $files_new;
    }
    
    private function getRealEstateReport($objRequest)
    {
        $this->load->model('Real_estate_model', 're');
        
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
            
        }
        else
        {
            $type = 'client';
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        
        $re_rep = $this->re->get_real_estate_report($type, $where);
        
        //echo '<pre>';print_r($re_rep);die;
        if(count($re_rep) > 0)
        {
            $total_invested = 0;
            $total_maturity = 0;
            
            $client_total = array();
            $client_data = array();
            $client_id_name = array();

            $counter = 0;
            foreach($re_rep as $row)
            {
                //echo '<pre>';print_r($row);die;
                $invested = intval($row->amount);
                $maturity = intval($row->current_rate * $row->property_area);
                
                if(!empty($invested))
                {
                    $total_invested+= $invested;
                }
                
                if(!empty($maturity))
                {
                    $total_maturity+= $maturity;
                }
                
                //$client_id_name[$row->client_id] = $row->client_name;

                if(array_key_exists($row->client_name, $client_total))
                {
                   $client_total[$row->client_name]['investment']+= $invested;
                   $client_total[$row->client_name]['maturity']+= $maturity;
                }
                else
                {
                   $client_total[$row->client_name]['investment']= $invested;
                   $client_total[$row->client_name]['maturity']= $maturity; 
                }
                
                $tempDate = DateTime::createFromFormat('d/m/Y',$row->transaction_date);
                $date = $tempDate->format('d-M-Y');
                $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->property_updated_on);
                $date2 = $tempDate2->format('d-M-Y');
                
                $row->transaction_date = $date;
                $row->property_updated_on = $date2;
                $row->prop_area = round($row->property_area, 0).' '.$row->unit_name;
                $row->amount_invested = $this->common_lib->moneyFormatIndiaClient(round($invested));
                $row->market_value = $this->common_lib->moneyFormatIndiaClient(round($maturity));
                
                $rent = intval($row->rent_amount);
                if(!empty($rent)) {
                    $row->rent_amount = $this->common_lib->moneyFormatIndiaClient(round($row->rent_amount));
                } else {
                   $row->rent_amount = '';
                }
                if(array_key_exists($row->client_name, $client_data))
                {
                    array_push($client_data[$row->client_name], $row);
                }
                else
                {
                    $client_data[$row->client_name][0] = $row;
                }
            }
            
            $members = array();
            
            foreach ($client_data as $k=>$val)
            {
                $member = array();
                $member['name'] = $k;
                $member['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['investment']));
                $member['total_market_value'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['maturity']));
                $member['properties'] = $val;
                $members[] = $member;
            }
            
            $data['members'] = $members;
            //echo '<pre>';print_r($client_data);die;
            
            $data['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($total_invested));
            $data['total_market_value'] = $this->common_lib->moneyFormatIndiaClient(round($total_maturity));
            
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getCommodityReport($objRequest)
    {
        $this->load->model('Commodities_model', 'comm');
        
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
            
        }
        else
        {
            $type = 'client';
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        
        $commodity_rep = $this->comm->get_commodity_report($type, $where);
        
        //echo '<pre>';print_r($commodity_rep);die;
        if(count($commodity_rep) > 0)
        {
            $total_invested = 0;
            $total_maturity = 0;
            $total_unrealized_gain = 0;
            
            $client_total = array();
            $client_data = array();
            $client_id_name = array();

            $counter = 0;
            foreach($commodity_rep as $row)
            {
                $invested = intval($row->total_amount);
                $maturity = intval($row->market_value);
                $profit = intval($row->unrealised_gain);
                
                if(!empty($invested))
                {
                    $total_invested+= $invested;
                }
                
                if(!empty($maturity))
                {
                    $total_maturity+= $maturity;
                }
                
                if(!empty($profit))
                {
                    $total_unrealized_gain+= $profit;
                }
                
                //$client_id_name[$row->client_id] = $row->client_name;

                if(array_key_exists($row->client_name, $client_total))
                {
                   $client_total[$row->client_name]['investment']+= $invested;
                   $client_total[$row->client_name]['market_value']+= $maturity;
                   $client_total[$row->client_name]['unrealized_gain']+= $profit;
                }
                else
                {
                   $client_total[$row->client_name]['investment']= $invested;
                   $client_total[$row->client_name]['market_value']= $maturity; 
                   $client_total[$row->client_name]['unrealized_gain']= $profit; 
                }
                
                $tempDate = DateTime::createFromFormat('d/m/Y',$row->transaction_date);
                $date = $tempDate->format('d-M-Y');
                //$tempDate2 = DateTime::createFromFormat('d/m/Y',$row->property_updated_on);
                //$date2 = $tempDate2->format('d-M-Y');
                
                $row->transaction_date = $date;
                //$row->property_updated_on = $date2;
                $row->quantity = $row->quantity.' '.$row->unit_name;
                
                $tr = intval($row->transaction_rate);
                
                if(!empty($tr))
                {
                    $this->common_lib->moneyFormatIndiaClient(round($row->transaction_rate));
                }
                else
                {
                    $row->transaction_rate = '';
                }
                $row->total_amount = $this->common_lib->moneyFormatIndiaClient(round($invested));
                $row->market_value = $this->common_lib->moneyFormatIndiaClient(round($maturity));
                
                $cr = intval($row->current_rate);
                if(!empty($cr)) {
                    $row->current_rate = $this->common_lib->moneyFormatIndiaClient(round($cr));
                } else {
                   $row->current_rate = '';
                }
                
                $row->unrealised_gain = $this->common_lib->moneyFormatIndiaClient(round($profit));
                $cagr = intval($row->cagr);
                $abs = intval($row->abs);
                if(!empty($cagr))
                {
                    $row->cagr = round($row->cagr,2);
                }
                else
                {
                    $row->cagr = '';
                }
                if(!empty($abs))
                {
                    $row->abs = round($row->abs,2);
                }
                else
                {
                    $row->abs = '';
                }
                //$row->cagr = round($row->cagr,2);
                //$row->abs = round($row->abs,2);
                if(array_key_exists($row->client_name, $client_data))
                {
                    array_push($client_data[$row->client_name], $row);
                }
                else
                {
                    $client_data[$row->client_name][0] = $row;
                }
            }
            
            $members = array();
            
            foreach ($client_data as $k=>$val)
            {
                $member = array();
                $member['name'] = $k;
                $member['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['investment']));
                $member['total_market_value'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['market_value']));
                $member['total_unrealized_gain'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['unrealized_gain']));
                $member['commodities'] = $val;
                $members[] = $member;
            }
            
            $data['members'] = $members;
            //echo '<pre>';print_r($client_data);die;
            
            $data['total_invested'] = $this->common_lib->moneyFormatIndiaClient(round($total_invested));
            $data['total_market_value'] = $this->common_lib->moneyFormatIndiaClient(round($total_maturity));
            $data['total_unrealized_gain'] = $this->common_lib->moneyFormatIndiaClient(round($total_unrealized_gain));
            
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getFDPremCal($objRequest)
    {
        $this->load->model('Premium_types_model', 'prem_type');
        
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        $prem_date = date('m/Y');//$this->input->post('prem_date');
        //$prem_date = date('d').'/'.$prem_date;
        $prem_date = '01'.'/'.$prem_date;
        $prem_date_temp = DateTime::createFromFormat('d/m/Y', $prem_date);
        $rep_date_temp = $prem_date_temp;
        $prem_date = $prem_date_temp->format('Y-m-d');
        $prem_date_temp = $prem_date_temp->format('M');

        //echo date('M',strtotime($prem_date));die;
        $where = "";
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            
            $type = 'family';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'familyID'=> $family_id
            );
            //$whereLapse = array('familyID' => $family_id);
            
        }
        else
        {
            $type = 'client';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'clientID'=> $client_id
            );
            //$whereLapse = array('clientID' => $client_id);
        }
      
        $prem_rep = $this->fd->get_interest_calendar_report($type, $where);
        //$lapse_rep = $this->ins->get_lapse_policy_report($type, $whereLapse);
        
        //echo '<pre>';print_R($prem_rep);die;
        //echo '<pre>';print_R($lapse_rep);die;
        
        $arrMonthKeys = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        
        $data['calendar'] = array();
        $total = 0;
        
        if(count($prem_rep) > 0)
        {
            for($i=0;$i<12;$i++)
            {
                $month = date('M',strtotime($prem_date));
                foreach ($prem_rep as $row)
                {
                    $arrRow = (array)$row;
                    
                    foreach ($arrRow as $key => $val)
                    {
                        if($month == $key)
                        {
                            $prem_amt = intval($val);

                            if(!empty($prem_amt))
                            {
                                $total+= $val;

                                if(array_key_exists($key, $data['calendar']))
                                {
                                     $data['calendar'][$key]['total']+= $val;
                                     $data['calendar'][$key]['total_formatted'] = $this->common_lib->moneyFormatIndiaClient(round($data['calendar'][$key]['total']));
                                }
                                else
                                {
                                    $data['calendar'][$key]['total'] = $val;
                                    $data['calendar'][$key]['total_formatted'] = $this->common_lib->moneyFormatIndiaClient(round($prem_amt));
                                }
                                
                               
                                $amt = $this->common_lib->moneyFormatIndiaClient(round($prem_amt));
                                $investment = $this->common_lib->moneyFormatIndiaClient(round($arrRow['amount_invested']));
                                $data['calendar'][$key]['data'][] = array('member'=>$arrRow['client_name'],'company'=>$arrRow['fd_comp_name'],'maturity_account_number'=>$arrRow['maturity_account_number'],'amount'=>$amt,'ref_number'=>$arrRow['ref_number'],'investment'=>$investment);
                                
                                
                            }
                        }
                    }
                }
                $prem_date = date('Y-m-d',strtotime("+1 months", strtotime($prem_date)));
            }
            
            
            foreach ($data['calendar'] as $k => $v)
            {
                $v['month'] = $k;
                $data['fd_calendar'][] = $v;
            }
            
            unset($data['calendar']);
            
            $data['calendar']['total'] = $this->common_lib->moneyFormatIndiaClient(round($total));
            //echo '<pre>';print_r($data);
            return array('code'=>1,'summary'=>$data);
        
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getLedgerReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        $from_date = (date('Y')-1).date('/m/d');
        $to_date=date('Y/m/d');
        $type = 'client';
        $where = "";
        
        if(@$objRequest->hof && $client->head_of_family == '1')
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
            

        }
        else
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
        }
     
        $ledger_rep_inflow = $this->report->get_ledger_report_inflow($type, $where);
        $ledger_rep_outflow = $this->report->get_ledger_report_outflow($type, $where);
        $ledger_rep_dividend = $this->report->get_ledger_report_dividend($type, $where);
        
        rsort($ledger_rep_inflow);
        rsort($ledger_rep_outflow);
        rsort($ledger_rep_dividend);
        if(!empty($ledger_rep_inflow) || !empty($ledger_rep_outflow))
        {
            $inflow_total = 0;
            $outflow_total = 0;
            
            foreach ($ledger_rep_inflow as $row)
            {
                $amount = intval($row->amount);
                
                if(!empty($amount))
                {
                    $inflow_total = round($inflow_total+ $row->amount);
                    $row->amount = $this->common_lib->moneyFormatIndiaClient(round($row->amount));
                }
                else
                {
                    $row->amount = '';
                }
                
                //$tempDate = DateTime::createFromFormat('d/m/Y',$row->comp_date);
                //echo '<pre>';print_r($tempDate);die;
                $row->comp_date = date('d-M-Y',strtotime($row->comp_date));
                
                $data['inflow'][] = $row;
            }
            
            foreach ($ledger_rep_outflow as $row)
            {
                $amount = intval($row->amount);
                
                if(!empty($amount))
                {
                    $outflow_total= round($outflow_total+ $row->amount);
                    $row->amount = $this->common_lib->moneyFormatIndiaClient(round($row->amount));
                }
                else
                {
                    $row->amount = '';
                }
                
                //$tempDate = DateTime::createFromFormat('d/m/Y',$row->comp_date);
                //echo '<pre>';print_r($tempDate);die;
                $row->comp_date = date('d-M-Y',strtotime($row->comp_date));
                
                $data['outflow'][] = $row;
            }
            
            $data['inflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($inflow_total));
            $data['outflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($outflow_total));
            $data['net_investment'] = $this->common_lib->moneyFormatIndiaClient(round($inflow_total-$outflow_total));
            
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getCashflowReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        //echo '<pre>';print_r($client);die;
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        $from_date = date('Y/m/d');
        $to_date=(date('Y')+50).date('/m/d');
        $type = 'client';
        $where = "";
        
        if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $where = array(
              'clientID'=> $client_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
        }
        
        $family_id= $client->family_id;
        $fam_info = $this->family->get_family_by_id($family_id);
        $cash_flow_rep = $this->report->get_cash_flow_report($type, $where);
        
        if(!empty($cash_flow_rep))
        {
            //echo '<pre>';print_r($cash_flow_rep);die;
            $total_inflow = 0;
            $total_outflow = 0;
            $total_net_outflow = 0;
            
            $client_total = array();
            $client_data = array();
            $client_id_name = array();

            $counter = 0;
            
            foreach ($cash_flow_rep as $row)
            {
                     
                $inflow = 0;
                $outflow = 0;
                $net_outflow = 0;
                
                if($row->FD !='') 
                {
                    $total_inflow+= $row->FD;
                    $inflow+= $row->FD;
                    $row->FD = $this->common_lib->moneyFormatIndiaClient(round($row->FD),2);
                }
                
                if($row->FD_Maturity !='') 
                {
                    $total_inflow+= $row->FD_Maturity;
                    $inflow+= $row->FD_Maturity;
                    $row->FD_Maturity = $this->common_lib->moneyFormatIndiaClient(round($row->FD_Maturity),2);
                }
                
                if($row->Rent_Amount !='') 
                {
                    $total_inflow+= $row->Rent_Amount;
                    $inflow+= $row->Rent_Amount;
                    $row->Rent_Amount = $this->common_lib->moneyFormatIndiaClient(round($row->Rent_Amount),2);
                }
                
                if($row->Insurance !='') 
                {
                    $total_inflow+= $row->Insurance;
                    $inflow+= $row->Insurance;
                    $row->Insurance = $this->common_lib->moneyFormatIndiaClient(round($row->Insurance),2);
                }
                
                if($row->Commitments !='') 
                {
                    $total_outflow+= $row->Commitments;
                    $outflow+= $row->Commitments;
                    $row->Commitments = $this->common_lib->moneyFormatIndiaClient(round($row->Commitments),2);
                }
                
                if($row->Insurance_Premium !='') 
                {
                    $total_outflow+= $row->Insurance_Premium;
                    $outflow+= $row->Insurance_Premium;
                    $row->Insurance_Premium = $this->common_lib->moneyFormatIndiaClient(round($row->Insurance_Premium),2);
                }
               
                //echo '<pre>';print_R($row);die;
                $life_cover = intval($row->Life_Cover);
                
                if(!empty($life_cover))
                {
                    $row->Life_Cover = $this->common_lib->moneyFormatIndiaClient(round($row->Life_Cover),2);
                }
            
                if(array_key_exists($row->year, $client_total))
                {
                   $client_total[$row->year]['inflow']+= $inflow;
                   $client_total[$row->year]['outflow']+= $outflow;
                   $client_total[$row->year]['net_outflow']+= ($inflow-$outflow);
                }
                else
                {
                   $client_total[$row->year]['inflow']= $inflow;
                   $client_total[$row->year]['outflow']= $outflow; 
                   $client_total[$row->year]['net_outflow']= ($inflow-$outflow); 
                }
                
                if(array_key_exists($row->year, $client_data))
                {
                    array_push($client_data[$row->year], $row);
                }
                else
                {
                    $client_data[$row->year][0] = $row;
                }
            }
            
            $data['inflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($total_inflow),2);
            $data['outflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($total_outflow),2);
            $data['net_outflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($total_inflow-$total_outflow),2);
            
            foreach ($client_data as $k=>$val)
            {
                $member = array();
                $member['year'] = $k;
                $member['inflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['inflow']));
                $member['outflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['outflow']));
                $member['net_outflow_total'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$k]['net_outflow']));
                $member['data'] = $val;
                $members[] = $member;
            }
            
            $data['projection'] = $members;
            
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function getEquityReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
        
        // Include the main Financial class (search for installation path).
        require_once('application/third_party/Financial.php');
        $financial = new Financial();
        $xirr = 1;
        $cheque = 1;

        if(@$objRequest->hof && $client->head_of_family == '1')
        {
          $family_id= $client->family_id;
          //$brokerID= $client->broker_id;
        }
        else 
        {
            $client_id= $client->client_id;
            //$brokerID = $client->broker_id;
        }

        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        // $xirr = 1;
        // $cheque = 1;

        $this->db->where('client_id',$client->client_id);
        $query=$this->db->get('client_brokers');
        $result=$query->row();
        $clientCode=$result->client_code;

        $userID = $client->user_id;

        $type = 'client';
        $where = "";
        //echo $client_id;die;
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

            $this->db->where('client_id',$client->client_id);
            $query=$this->db->get('clients');
            $result=$query->row();
            $client_name=$result->name;
          
            //$clientName=$this->session->userdata('client_name');
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

        
            $this->db->where('family_id',$client->family_code);
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
        //echo '<pre>';print_r($eq_values);die;
        //echo json_encode(var_dump($eq_values));
        //get the sum of all the values (to calculate %)
        $value_total = 0;
        foreach($eq_values as $value) {
            $value_total += round($value->value);
        }
        //get values of all funds, if cheque option selected
        if($cheque === 1 || $cheque === '1') {
            $eq_rep = $this->eq->get_equity_report($where);
        }
        
       
        //echo json_encode(var_dump($eq_rep));
        $eq_balance = $this->eq->get_equity_broker_balance($values_where);
        //echo '<pre>';print_r($eq_rep);die;
        if(count($eq_values) > 0)
        {
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
          //echo $xirr_value;die;
            $client_total = array();
            $clients = array();
            $client_code = array();
            $client_code_scrip = array();
            $client_code_scrip_total = array();
            $client_code_scrip_allocation = array();
            foreach ($eq_values as $row)
            {
                $row->market_value = $this->common_lib->moneyFormatIndiaClient(round($row->value),2);
                $row->market_price = $this->common_lib->moneyFormatIndiaClient(round($row->close_rate),2);
                
                $allocation = round(($row->value * 100) / $value_total,2);
                if(array_key_exists($row->name, $client_total))
                {
                    $client_total[$row->name]['market_value'] += round($row->value);
                }
                else
                {
                    $client_total[$row->name]['market_value'] = round($row->value);
                }
                
                if(array_key_exists($row->name, $client_code))
                {
                    if(!in_array($row->client_code, $client_code[$row->name]))
                    {
                        $client_code[$row->name][] = $row->client_code;
                    }
                }
                else
                {
                    $client_code[$row->name][0] = $row->client_code;
                }
                
                if(array_key_exists($row->client_code, $client_code_scrip))
                {
                    $client_code_scrip[$row->client_code][] = $row;
                    $client_code_scrip_total[$row->client_code] += round($row->value);
                    $client_code_scrip_allocation[$row->client_code] += $allocation;
                }
                else
                {
                    $client_code_scrip[$row->client_code][0] = $row;
                    $client_code_scrip_total[$row->client_code] = round($row->value);
                    $client_code_scrip_allocation[$row->client_code] = $allocation;
                }
                
                $clients[$row->name] = $row->name;
                $row->allocation = $allocation. '%';
                
                //$value_total += round($row->value);
                
                //$client_code_scrip[$row->name][$row->code][] = $row;
            }
            
            $members = array();
            foreach ($clients as $key=>$val)
            {
                $member = array();
                $member['name'] = $key;
                $member['market_value'] = $this->common_lib->moneyFormatIndiaClient(round($client_total[$key]['market_value']),2);
                
                //echo '<pre>';print_r($client_code[$key]);die;
                foreach ($client_code[$key] as $cKey=>$cVal)
                {
                    $code_details = array();
                    $code_details['code'] = $cVal;
                    $code_details['market_value'] = $this->common_lib->moneyFormatIndiaClient(round($client_code_scrip_total[$cVal]),2);
                    $code_details['allocation'] = round($client_code_scrip_allocation[$cVal]).'%';
                    $code_details['scrips'] = $client_code_scrip[$cVal];
                    $member['codes'][] = $code_details;
                }
                $members[] = $member;
            }
            
            //echo '<pre>';print_r($client_code_scrip_total);die;
            
            $data['total_market_value'] = $this->common_lib->moneyFormatIndiaClient(round($value_total),2);
            $data['members'] = $members;
            //$data['client_codes'] = $client_code_scrip;
            
            if(count($eq_rep) > 0)
            {
                $ledger_bal = 0;
                if(count(get_object_vars($eq_balance[0])) > 0) 
                {
                    $ledger_bal = $eq_balance[0]->balance;
                }
                $ledger = array();
                $ledger_total_investment = 0;
                $ledger_total_withdrawl = 0;
                foreach ($eq_rep as $rep)
                {
                    $led = array();
                    $temp_date = DateTime::createFromFormat('Y-m-d',$rep->transaction_date);
                    $transaction_date = $temp_date->format('d-M-Y');
                    
                    $led['tran_date'] = $transaction_date;
                    $add = intval($rep->add);
                    if(!empty($add))
                    {
                        $led['investment'] = $this->common_lib->moneyFormatIndiaClient(round($rep->add));
                    }
                    else
                    {
                        $led['investment'] = '';
                    }
                    
                    $withdraw = intval($rep->withdraw);
                    
                    if(!empty($withdraw))
                    {
                        $led['withdrawl'] = $this->common_lib->moneyFormatIndiaClient(round($rep->withdraw));
                    }
                    else
                    {
                        $led['withdrawl'] = '';
                    }
                    
                    $led['current_val'] = '';
                    $led['ledger_bal'] = '';
                    $led['total_val'] = '';
                    $ledger[] = $led;
                    
                    $ledger_total_investment = round($ledger_total_investment + $rep->add);
                    $ledger_total_withdrawl = round($ledger_total_withdrawl + $rep->withdraw);
                    
                }
                
                $data['ledger_total_investment'] = $this->common_lib->moneyFormatIndiaClient(round($ledger_total_investment),2);
                
                if(!empty($ledger_total_withdrawl))
                {
                    $data['ledger_total_withdrawl'] = $this->common_lib->moneyFormatIndiaClient(round($ledger_total_withdrawl),2);
                }
                else
                {
                    $data['ledger_total_withdrawl'] = '';
                }
                
                $data['ledger_current_value'] = $this->common_lib->moneyFormatIndiaClient(round($value_total),2);
                $data['ledger_balance'] = $this->common_lib->moneyFormatIndiaClient(round($ledger_bal),2);
                
                if(empty($ledger_total_withdrawl)) 
                {
                    if(!empty($ledger_bal)) {
                        $totalVal = round($value_total + $ledger_bal);
                    } else {
                        $totalVal = round($value_total);
                    }
                } 
                else 
                {
                    if(!empty($ledger_bal)) {
                        $totalVal = round($value_total + $ledger_total_withdrawl + $ledger_bal);
                    } else {
                        $totalVal = round($value_total + $ledger_total_withdrawl);
                    }
                }
                
                if(empty($ledger_total_investment)) 
                {
                   $netGain = $totalVal;
                   $absGain = 0;
                } 
                else 
                {
                   $netGain = $totalVal - $ledger_total_investment;
                   $absGain = ($netGain/$ledger_total_investment)*100;
                }
                
                if(!empty($netGain))
                {
                    $data['net_gain'] = $this->common_lib->moneyFormatIndiaClient(round($netGain),2);
                }
                else
                {
                    $data['net_gain'] = '';
                }
                
                if(!empty($absGain))
                {
                    $data['abs_gain'] = round($absGain,2).' %';
                }
                else
                {
                    $data['abs_gain'] = '';
                }
                
                if(!empty($xirr_value))
                {
                    $data['xirr'] = round($xirr_value*100,2).' %';
                    //$data['xirr'] = sprintf("%.0f",$data['xirr']);
                }
                else
                {
                    $data['xirr'] = '';
                }
                $data['ledger_total_value'] = $this->common_lib->moneyFormatIndiaClient(round($totalVal),2);
                $data['ledger'] = $ledger;
            }
            return array('code'=>1,'summary'=>$data);
        }
        else
        {
            return array('code'=>1,'summary'=>[],'msg'=>'No data found.');
        }
    }
    
    private function sendPushNotification($objRequest)
    {
        //$server_key="AAAAciMUDRI:APA91bEer5iM4O1FkXrPPyetys1edRLN6m8As60kaG2lLq5v546Edvx_da-KhtbSa-Yk5KIPxtHs3u9LDYDm36DZ87cnqH7uQXBo_l0wGC5o2JA2PeEobK84dN6MZP6I_W6kOCN0kFzh"; // get this from Firebase project settings->Cloud Messaging
        $server_key = 'AAAAciMUDRI:APA91bEer5iM4O1FkXrPPyetys1edRLN6m8As60kaG2lLq5v546Edvx_da-KhtbSa-Yk5KIPxtHs3u9LDYDm36DZ87cnqH7uQXBo_l0wGC5o2JA2PeEobK84dN6MZP6I_W6kOCN0kFzh';
        //$server_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        // // $user_token="d-eDBIPQQXQ:APA91bFuPgtpcwqmzcS6upTs6HHmNpEiKpv7h2W0j10Ag7K80RqmcmlRyTwG1VrL42U1BD8-Q9lhkMCkXwV6v475wnDS5TVC-Er_ByfLC0IoShBM_ZnXh7f5Xu_x1S2Jv1fxWjCrhFx9";
        //$user_token="eyFbUsgO9tI:APA91bF_iXHnGELLoMuIJtvC0Pc9PwOGSfWDTEVkbnMNoE4rKVLFOAHOcdpr8ZEbFgXKLHXhh4jYt2LRyhi1OgkZFflgcewC9elS4_u5hsQfpDjAp4TN8jkjs3_NFCVjZxWWFLEfnPLu";
        $user_token = $objRequest->device_token;
        $title="New Message from Isolutions ".time();
        $n_msg=time(). " Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

        $ndata = array('title'=>$title,'body'=>$n_msg,'image'=>'https://3tense.com/assets/users/img/logo-big.png','action'=>'url','action_destination'=>'https://3tense.com');
        //$ndata = array('title'=>$title,'body'=>$n_msg);

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array();
        $fields['data'] = $ndata;
        $fields['notification'] = array('title'=>$title,'body'=>$n_msg);

        $fields['to'] = $user_token;
        $headers = array(
            'Content-Type:application/json',
          'Authorization:key='.$server_key
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //echo '<pre>';print_r($result);die;
        if ($result === FALSE) {
            //die('FCM Send Error: ' . curl_error($ch));
            return array('code'=>-1,'msg'=>'Error while sending push notification','desc'=> curl_error($ch));
        }
        else
        {
            //echo '<pre>';print_r($result);die;
            return array('code'=>1,'payload'=>$fields);
        }
        curl_close($ch);
    }
    
    private function getAssetLiabilityReport($objRequest)
    {
        $client = $this->client->get_client_info($objRequest->client_id);
       
        $client_id = $objRequest->client_id;
        $family_id= $client->family_id;
        //$brokerID= $client->user_id;
        
        $family = $this->client->get_client_family($objRequest->client_id);
        $brokerID = ($family->broker_id == null ? $client->user_id : $family->broker_id);
        
        $where = "";
        
        /*if(@$objRequest->hof && $client->head_of_family == '1')
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $where = array(
              'clientID'=> $client_id,
              'fromDate'=> $from_date,
              'toDate'=> $to_date,
              'brokerID'=> $brokerID
          );
        }*/
        
        $family_id= $client->family_id;
        $fam_info = $this->family->get_family_by_id($family_id);
        
        $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        
        
        $asset_rep = $this->al->get_asset_report($where);
        $liability_rep = $this->al->get_liability_report($where);
        
        foreach ($asset_rep as $k=>$v)
        {
            //echo '<pre>';print_r($v);die;
            
            $tempDate = DateTime::createFromFormat('d/m/Y',$v->start_date);
            $date = $tempDate->format('d-M-Y');
            $asset_rep[$k]->start_date = $date;
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$v->end_date);
            $date2 = $tempDate2->format('d-M-Y');
            $asset_rep[$k]->end_date = $date2;
            
            $ins_amount = intval($v->installment_amount);
                
            if(!empty($ins_amount))
            {
                $v->installment_amount = $this->common_lib->moneyFormatIndiaClient(round($v->installment_amount));
            }
            else
            {
                $v->installment_amount = '';
            }
            
            $mat_amount = intval($v->expected_mat_value);
                
            if(!empty($mat_amount))
            {
                $v->expected_mat_value = $this->common_lib->moneyFormatIndiaClient(round($v->expected_mat_value));
            }
            else
            {
                $v->expected_mat_value = '';
            }
            
            if(empty($v->company_name)) $asset_rep[$k]->company_name = '';
            if(empty($v->goal)) $asset_rep[$k]->goal = '';
        }
        
        foreach ($liability_rep as $k=>$v)
        {
            //echo '<pre>';print_r($v);die;
            
            $tempDate = DateTime::createFromFormat('d/m/Y',$v->start_date);
            $date = $tempDate->format('d-M-Y');
            $liability_rep[$k]->start_date = $date;
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$v->end_date);
            $date2 = $tempDate2->format('d-M-Y');
            $liability_rep[$k]->end_date = $date2;
            
            $ins_amount = intval($v->installment_amount);
                
            if(!empty($ins_amount))
            {
                $v->installment_amount = $this->common_lib->moneyFormatIndiaClient(round($v->installment_amount));
            }
            else
            {
                $v->installment_amount = '';
            }
            
            $mat_amount = intval($v->total_liability);
                
            if(!empty($mat_amount))
            {
                $v->total_liability = $this->common_lib->moneyFormatIndiaClient(round($v->total_liability));
            }
            else
            {
                $v->total_liability = '';
            }
            
            if(!empty($v->interest_rate)) $liability_rep[$k]->interest_rate .= '%';
            if(empty($v->company_name)) $liability_rep[$k]->company_name = '';
            $liability_rep[$k]->goal = '';
            
            if($liability_rep[$k]->particular !='') $liability_rep[$k]->goal = $liability_rep[$k]->particular;
            
        }
        $data['assets'] = $asset_rep;
        $data['liabilities'] = $liability_rep;
        return array('code'=>1,'summary'=>$data);
    }
}
?>