<?php
ini_set('memory_limit','2048M');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scripts extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        //load model reminder_model rem is the object
        $this->load->model('Clients_model');
        $this->load->model('Scripts_model', 'rem');
        $this->load->model('Reminders_model', 'rem_m');
        $this->load->model('Common_model', 'common');
        $this->load->model('Fixed_deposits_model', 'fd');
        $this->load->library('Mail', 'mail');
    }
    function fd_data()
    {
              //FD reminders
                //-----------------------
                $fd_condition = array(
                    'fdt.maturity_date <= ' => date('Y-m-d'),
                    'fdt.status' => 'Active'
                );
                $fd_rows = $this->fd->get_fixed_deposit($fd_condition);
                if($fd_rows) {
                    $fdTransUpd = array(); //array for status update
                    $fdRemInsert = "values "; //string for insert into reminders
                    //read all rows and create an array for updation
                    foreach($fd_rows as $row) {
                        $fdTransUpd[] = array(
                            'fd_transaction_id' => $row->fd_transaction_id,
                            'status' => 'Matured'
                        );
                        
                        $fdtd = array(
                            'status' => 'Matured'
                        );
                        $fdtdwhere = array(
                            'fd_transaction_id' => $row->fd_transaction_id
                        );
                        
                        $status = $this->fd->update_fixed_deposit($fdtd, $fdtdwhere, FALSE);
                        
                        $fdRemInsert .= "('Fixed Deposit Matured','".$row->client_id."','".$row->client_name."','".
                            $row->ogi_mat_date."','Rs. ".$row->maturity_amount." will be credited to your bank towards maturity of ".$row->fd_comp_name.", Ref. No.:".$row->ref_number." (Tax will be deducted as applicable.)','".$row->broker_id."'),";
                    }

                    try {
                        //send data for updation
                        /*$updated = $this->fd->update_fixed_deposit_batch($fdTransUpd,'fd_transaction_id');
                        if(isset($updated['code'])) {
                            throw new Exception;
                        }*/
                        //insert into today_reminders table
                        $fdRemInsert = rtrim($fdRemInsert,','); //remove trailing comma from string
                        $fdRemInsert .= ";"; //add semicolon to end the query
                        $insertRem = $this->rem->insert_reminders($fdRemInsert);

                    } catch(Exception $e) {
                        $error_array['error_on'] = 'Updating FD status';
                        $error_array['error_msg'] = $e->getMessage();
                        $error_array['broker_id'] = 'All';
                        $this->common->error_logs($error_array);
                    }
                }
    }

    ///////////////////////////////////////
    //Reminder Script Start
    //////////////////////////////////////
    function reminder_script()
    {
        $script_Date = "";
        try
        {
            $result_counter = $this->rem->reminder_counter();
            $script_Date = date('Y-m-d', strtotime($result_counter->script_date. ' + 1 days'));
            //$script_Date = date('Y-m-d', strtotime($result_counter->script_date));
        }
        catch(Exception $e)
        {
            $error_array['error_on'] = 'Reminder Counter';
            $error_array['error_msg'] = $e->getMessage();
            $this->common->error_log($error_array);
        }
        /*if($script_Date <= date('Y-m-d'))
        {*/
            /*try
            {
                $this->rem->delete_complete_reminder(array('reminder_status' => 'Complete'));
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Deleting Complete Reminder';
                $error_array['error_msg'] = $e->getMessage();
                $this->common->error_log($error_array);
            }*/
            try
            {
                $result2 = $this->rem->get_reminder_days_0174();
                $result1 = $this->rem->get_reminder_days_Without_0174();
                $result = array();
                  foreach($result2 as $item)
                  {
                    $result[]=$item;
                  }
                  foreach($result1 as $item)
                  {
                    $result[]=$item;
                  }
                
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Getting Reminder Days';
                $error_array['error_msg'] = $e->getMessage();
                $this->common->error_log($error_array);
            }
            
            foreach($result as $item)
            {
                $brokerID =  $item->broker_id;
                $remParam = array(
                    'brokerID' => $brokerID, 'scriptDate' => $script_Date,
                    'personalRem' => $item->personal_reminder, 'insPremiumRem'=>$item->ins_premium_reminder,
                    'insPremiumAmt'=>$item->ins_premium_amount, 'insGraceRem'=>$item->ins_grace_reminder,
                    'insGraceAmt'=>$item->ins_grace_amount, 'insMaturityRem'=>$item->ins_maturity_reminder,
                    'insMaturityAmt'=>$item->ins_maturity_amount,
                    'fdIntRem'=>$item->fd_interest, 'fdIntAmt'=>$item->fd_interest_amount,
                    'fdMaturityRem'=>$item->fd_maturity_reminder, 'fdMaturityAmt'=>$item->fd_maturity_amount,
                    'assetRem'=>$item->assets_reminder,'assetAmount'=>$item->assets_amount

                );
                try
                {
                    $this->rem->update_reminder($remParam);
                    //var_dump($remParam);
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'On Updating Reminders of '.$brokerID;
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $this->common->error_logs($error_array);
                }

                /*$this->check_insurance_grace($script_Date, $brokerID);
                $this->check_insurance_lapse($script_Date, $brokerID);
                $this->check_insurance_paidUp($script_Date, $brokerID);
                $this->check_insurance_complete($script_Date, $brokerID);*/

                $this->check_insurance_complete($script_Date, $brokerID);
                //$this->check_insurance_grace($script_Date, $brokerID);
                $this->check_insurance_paidUp($script_Date, $brokerID);
                $this->check_insurance_grace($script_Date, $brokerID);
                $this->check_insurance_lapse($script_Date, $brokerID);


                //FD reminders
                //-----------------------
                $fd_condition = array(
                    'fdt.maturity_date <= ' => date('Y-m-d'),
                    'fdt.status' => 'Active'
                );
                $fd_rows = $this->fd->get_fixed_deposit($fd_condition);
                if($fd_rows) {
                    $fdTransUpd = array(); //array for status update
                    $fdRemInsert = "values "; //string for insert into reminders
                    //read all rows and create an array for updation
                    foreach($fd_rows as $row) {
                        $fdTransUpd[] = array(
                            'fd_transaction_id' => $row->fd_transaction_id,
                            'status' => 'Matured'
                        );
                        
                        $fdtd = array(
                            'status' => 'Matured'
                        );
                        $fdtdwhere = array(
                            'fd_transaction_id' => $row->fd_transaction_id
                        );
                        
                        $status = $this->fd->update_fixed_deposit($fdtd, $fdtdwhere, FALSE);
                        
                        $fdRemInsert .= "('Fixed Deposit Matured','".$row->client_id."','".$row->client_name."','".
                            $row->ogi_mat_date."','Rs. ".$row->maturity_amount." will be credited to your bank towards maturity of ".$row->fd_comp_name.", Ref. No.:".$row->ref_number." (Tax will be deducted as applicable.)','".$row->broker_id."'),";
                    }

                    try {
                        //send data for updation
                        /*$updated = $this->fd->update_fixed_deposit_batch($fdTransUpd,'fd_transaction_id');
                        if(isset($updated['code'])) {
                            throw new Exception;
                        }*/
                        //insert into today_reminders table
                        $fdRemInsert = rtrim($fdRemInsert,','); //remove trailing comma from string
                        $fdRemInsert .= ";"; //add semicolon to end the query
                        $insertRem = $this->rem->insert_reminders($fdRemInsert);

                    } catch(Exception $e) {
                        $error_array['error_on'] = 'Updating FD status';
                        $error_array['error_msg'] = $e->getMessage();
                        $error_array['broker_id'] = 'All';
                        $this->common->error_logs($error_array);
                    }
                }


                // for sending reminder emails - not sure
                try
                {
                    if($script_Date == date('Y-m-d'))
                    {
                        $result = $this->sendReminders($brokerID);
                        //echo $result;
                    }
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'Sending Mail of '.$brokerID;
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $this->common->error_logs($error_array);
                }
                $this->rem->update_reminder_counter(date('Y-m-d'));

                echo " "+$brokerID+" Reminder updated successfully for " . date('d-m-Y');
                //select ReminderType='Fixed Income Maturity',c1.clientId,c1.cName,getDate(),'Rs. '+ ltrim(str(f.expMaturityAmount)) +  ' Maturity amount is getting matured from '+ f.issuingAthority +', Ref. No.: '+  f.refNo+' on ' + convert(varchar(10),  f.expMaturityDate,103) from fdtransaction f inner join client c1 on f.clientId=c1.clientID where  datediff(dd,f.expMaturityDate,dateadd(dd," + fdMaturityReminder + ",'" + dt1 + "'))=0 and f.expMaturityAmount>=" + fdMatAmt + "   union all    select ReminderType='Fixed Income Payout',c1.clientId,c1.cName,convert(varchar(10),fi.intDate,103),'Rs. '+ ltrim(str(fi.amount)) +', '  +f.interestmode +' interest for '+ f.toi + ' in  ' + f.issuingAthority +', Ref. No.: '+f.refNo+' on ' +   convert(varchar(10),fi.intDate,103) from fdtransaction f inner join fdInterest fi on fi.fdTransId=f.transId inner join client c1   on f.clientId=c1.clientID where datediff(dd,fi.intDate,dateadd(dd," + fdIntrest + ",'" + dt1 + "'))=0 and   fi.amount>=" + fdIntrestAmt + "   union all  select ReminderType='Rent',c.clientId,c.cName,rentDate,'Rental due of Rs. '+ ltrim(str(prd.amount))+ ' of ' + propertyname  +   ' today.' from proprentDetails prd inner join propertyTransaction pt on pt.porpTransId=prd.propTransId inner join client c on   c.clientId=pt.ClientId where  dateDiff(dd,rentDate,'" + dt1 + "')=0  and prd.amount>=" + rentAmt + "  union all  select ReminderType='Assets',c1.clientId,c1.cName,pam.date,pm2.proName+' '+ pat.type + ' of ' + ' '+pat.company + ' ' + pat.scheme +  ' Rs. ' + convert(varchar,pam.amount) + ' is due on ' +  convert(varchar(10),pam.date,103)  + ', Ref. No.:'+pat.refNo+'' from  proAssetsMaturity pam inner join dbo.proAssetTrans pat on pam.assetsId=pat.assetsId inner join client c1 on pat.clientId=c1.clientID  inner join ProductMaster pm2 on pm2.productId=pat.productId where datediff(dd,pam.date,dateadd(dd," + assetsReminder + ",'" + dt1 + "'))=0  and pam.amount>=" + assetsAmt + "   union all select ReminderType='Liability',c1.clientId,c1.cName,plm.date,pm2.proName+' '+ plt.type + '  of ' + plt.company + ' ' + plt.scheme + ' of Rs. ' + convert(varchar,plm.amount) + ' is due on ' +  convert(varchar(10),plm.date,103)  + ', Ref. No.:'+plt.refNo+'' from proLiabMaturity plm inner join dbo.proLiabTrans plt on plm.liabilityId=plt.liabilityId inner join client c1 on plt.clientId=c1.clientID inner join ProductMaster pm2 on pm2.productId=plt.productId where datediff(dd,plm.date,dateadd(dd," + assetsReminder + ",'" + dt1 + "'))=0 and plm.amount>=" + assetsAmt + ""
            }
        /*}
        else
        {
            echo "Reminder already updated for today";
        }*/
    }
    
    function sendPushNotification()
    {
        $date2 = new DateTime(date('d-m-Y'));
        $date2->modify('-15 day');
        $date = new DateTime(date('d-m-Y'));
        $date->modify('+15 day');
        $condition = 'IsSendNotification = 0 and reminder_date between "'.$date2->format('Y-m-d').'" and "'.$date->format('Y-m-d').'" AND reminder_type != "Client" AND reminder_type != "Notification" AND client_view = 0' ;
        //print_r($condition);die();
        $rem_data = $this->rem_m->dash_reminder_list_for_api($condition, 50);
        
      //  print_r($rem_data);die();
         //$isolution_android_key = 'AAAAciMUDRI:APA91bEer5iM4O1FkXrPPyetys1edRLN6m8As60kaG2lLq5v546Edvx_da-KhtbSa-Yk5KIPxtHs3u9LDYDm36DZ87cnqH7uQXBo_l0wGC5o2JA2PeEobK84dN6MZP6I_W6kOCN0kFzh';
        $isolution_android_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $isolution_ios_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $vfinancial_android_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $vfinancial_ios_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        
        $clients = $this->Clients_model->get_clients_with_device_token_1();
        
        foreach ($clients as $client)
        {
            $brokerID = ($client->broker_id == null ? $client->user_id : $client->broker_id);
            
            foreach ($rem_data as $rem)
            {
                if($client->client_id==$rem->client_id)
                {
                  //  $client->device_token='fHLc0Mf4TEyRUtrm8FF4w8:APA91bFKK0WPv_wGvlyLpMzEB8hkc82wQJz-C8sy-YR-JT7DcgbB6Wd6UfKIinNIdPFm2DGxzJcqJaQ6OkXBrIzLgR643TjJSfxXrQ2ubrc9LdVYhULPXtiD-f_X1Wylo9mZ-sOdbL0R';
                    if($brokerID == '0009')
                    { 
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            $vfinancial_ios_devices = array();
                            array_push($vfinancial_ios_devices, $client->device_token);
                            $this->sendPushNotificationIOS($vfinancial_ios_devices,$rem->reminder_type,$rem->reminder_message,@$img_link,$vfinancial_ios_key);
                        }
                        else
                        {
                            $vfinancial_android_devices = array();
                            array_push($vfinancial_android_devices, $client->device_token);
                            $this->sendPushNotificationAndriod($vfinancial_android_devices,$rem->reminder_type,$rem->reminder_message,@$img_link,$vfinancial_android_key);
                        }
                    }
                    else
                    {
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            $isolution_ios_devices = array();
                            array_push($isolution_ios_devices, $client->device_token);
                            $this->sendPushNotificationIOS($isolution_ios_devices,$rem->reminder_type,$rem->reminder_message,@$img_link,$isolution_ios_key);
                        }
                        else
                        {
                            $isolution_android_devices = array();   
                            array_push($isolution_android_devices, $client->device_token);
                            $this->sendPushNotificationAndriod($isolution_android_devices,$rem->reminder_type,$rem->reminder_message,@$img_link,$isolution_android_key);
                            
                        }
                    }
                    
                     $data = array('IsSendNotification' => 1);
                    $condition = array('reminder_id'=>$rem->reminder_id);
                    $this->rem_m->update_reminder_days($data, $condition);
                    break;
                }
            }
        }
        
        return true;
        
    }
    
     
    public function sendPushNotificationAndriod($arrDevices,$title,$msg,$link,$accessKey)
    {
        $ndata = array('title'=>$title,'body'=>$msg);
        
       /* if($link != '')
        {
            $ndata['image'] = $link;
            $ndata['action'] = 'url';
            $ndata['action_destination'] = $link;
        }*/
        
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array();
        $fields['data'] = $ndata;
        $fields['notification'] = array('title'=>$title,'body'=>$msg,'image' => $link,  'sound'=>'default', 'click_action' => 'FCM_PLUGIN_ACTIVITY');
        $fields['registration_ids'] = $arrDevices;
        $fields['priority'] = 'high';
        $fields['ttl'] = '3600';
        
        $headers = array(
            'Content-Type:application/json',
          'Authorization:key='.$accessKey
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
        echo '<pre>';print_r($result);
    }
    public function sendPushNotificationIOS($arrDevices,$title,$msg,$link,$accessKey)
    {
        $ndata = array('title'=>$title,'body'=>$msg);
        
       /* if($link != '')
        {
            $ndata['image'] = $link;
            $ndata['action'] = 'url';
            $ndata['action_destination'] = $link;
        }*/
        
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array();
       // $fields['data'] = $ndata;
        $fields['notification'] = array('title'=>$title,'body'=>$msg,'image' => $link,  'sound'=>'default', 'click_action' => 'FCM_PLUGIN_ACTIVITY');
        $fields['registration_ids'] = $arrDevices;
        $fields['priority'] = 'high';
        $fields['ttl'] = '3600';
        
        $headers = array(
            'Content-Type:application/json',
          'Authorization:key='.$accessKey
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
        echo '<pre>';print_r($result);
    }
    function sendReminders1()
    {
       $this->sendReminders('0174');
    }
    function sendReminders($brokerID)
    {
        $resultBroker = $this->rem->get_reminder_details($brokerID);
        $emailBroker = $this->rem->broker_email($brokerID);
        if($emailBroker !== false && isset($emailBroker->email_id) && !empty($emailBroker->email_id)) {
            $result = $this->mail->send_mail($emailBroker->email_id, $resultBroker, $brokerID, 'Broker');
            echo $emailBroker->email_id +"<br/>";
            return $result;
        } else {
            return false;
        }
    }

    function check_insurance_lapse($script_Date, $brokerID)
    {
        $resultLapse = null;
        try
        {
            $resultLapse = $this->rem->get_policy_lapse($script_Date, $brokerID);
        }
        catch(Exception $e)
        {
            $error_array['error_on'] = 'Getting policy details of '.$brokerID;
            $error_array['error_msg'] = $e->getMessage();
            $error_array['broker_id'] = $brokerID;
            $this->common->error_log($error_array);
        }

        //echo '<br/>'.$brokerID; var_dump($resultLapse);

        foreach($resultLapse as $ins)
        {
            $polNum = $ins->policy_num;

            $status_id = $this->rem->get_status_id('Lapsed');
            if(!isset($status_id->status_id)) { var_dump($status_id); }
            $data = array('`status`' => $status_id->status_id);
            $condition = array('policy_num' => $polNum, 'broker_id' => $brokerID);
            try
            {
                $this->rem->update_insurance($data, $condition);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Updating Lapse Insurance reminder of '.$polNum;
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $this->common->error_log($error_array);
            }

            $query = "select 'Insurance Status', im.client_id, name, '".$script_Date."', ".
                "Concat(plan_name, ', Policy No.:', policy_num, ' is LAPSED now. Please pay premium soon to reinstate policy.'), im.broker_id ".
                "from insurances im inner join ins_plans ipm on ipm.plan_id = im.plan_id ".
                "inner join clients c on im.client_id = c.client_id where im.policy_num = '".$polNum."'";
            try
            {
                $this->rem->insert_reminders($query);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Inserting Reminder for Insurance Lapse';
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $this->common->error_log($error_array);
            }
        }
    }

    function check_insurance_grace($script_Date, $brokerID)
    {
        $resultGrace = null;
        try
        {
            $resultGrace = $this->rem->get_policy_grace($script_Date, $brokerID);
        }
        catch(Exception $e)
        {
            $error_array['error_on'] = 'Getting policy details of '.$brokerID;
            $error_array['error_msg'] = $e->getMessage();
            $error_array['broker_id'] = $brokerID;
            $this->common->error_log($error_array);
        }

        foreach($resultGrace as $ins)
        {
            $polNum = $ins->policy_num;

            $status_id = $this->rem->get_status_id('Grace');
            if(!isset($status_id->status_id)) { var_dump($status_id); }
            $data = array('`status`' => $status_id->status_id);
            $condition = array('policy_num' => $polNum, 'broker_id' => $brokerID);
            try
            {
                $this->rem->update_insurance($data, $condition);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Updating Grace Insurance reminder of '.$polNum;
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $this->common->error_log($error_array);
            }

            $query = "select 'Insurance Status', im.client_id, name, '".$script_Date."', ".
                "Concat(plan_name, ', Policy No.:', policy_num, ' is GRACE now.'), im.broker_id ".
                "from insurances im inner join ins_plans ipm on ipm.plan_id = im.plan_id ".
                "inner join clients c on im.client_id = c.client_id where im.policy_num = '".$polNum."'";
            try
            {
                $this->rem->insert_reminders($query);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Inserting Reminder for Insurance Grace';
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $this->common->error_log($error_array);
            }
        }
    }

    function check_insurance_paidUp($script_Date, $brokerID)
    {
        $resultPolicy = null;
        try
        {
            $resultPolicy = $this->rem->get_policy_paidUp($script_Date, $brokerID);
        }
        catch(Exception $e)
        {
            $error_array['error_on'] = 'Getting policy details of '.$brokerID;
            $error_array['error_msg'] = $e->getMessage();
            $error_array['broker_id'] = $brokerID;
            $this->common->error_log($error_array);
        }

        foreach($resultPolicy as $ins)
        {
            $polNum = $ins->policy_num;
            $mode = $ins->mode_name;
            $paidUp_date = $ins->paidup_date;
            $update = false;
            if($mode == 'Annually' || $mode == 'Single')
            {
                //$scriptDate2 = date('Y-m-d', strtotime($paidUp_date. '+ 1 year'));
                if(strtotime($script_Date) >= strtotime($paidUp_date))
                    $update = true;
            }
            else if($mode == 'Half-Yearly')
            {
                //$scriptDate2 = date('Y-m-d', strtotime($paidUp_date. '+ 6 months'));
                if(strtotime($script_Date) >= strtotime($paidUp_date))
                    $update = true;
            }
            else if($mode == 'Quarterly')
            {
                //$scriptDate2 = date('Y-m-d', strtotime($paidUp_date. '+ 3 months'));
                if(strtotime($script_Date) >= strtotime($paidUp_date))
                    $update = true;
            }
            else if($mode == 'Monthly')
            {
                //$scriptDate2 = date('Y-m-d', strtotime($paidUp_date. '+ 1 months'));
                if(strtotime($script_Date) >= strtotime($paidUp_date))
                    $update = true;
            }

            //for next year paidup reminder
            $scriptDate2 = date('Y-m-d', strtotime($paidUp_date. '+ 1 year'));

            $status_id = $this->rem->get_status_id('Paid up');
            if(!isset($status_id->status_id)) { var_dump($status_id); }
            $data = array('`status`' => $status_id->status_id);
            $condition = array('policy_num' => $polNum, 'broker_id' => $brokerID);
            try
            {
                $this->rem->update_insurance($data, $condition);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Updating Paid Up Insurance reminder of '.$polNum;
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $this->common->error_log($error_array);
            }
            if($update)
            {
                $query = "select 'Insurance Status', im.client_id, name, '".$script_Date."', ".
                    "Concat(plan_name, ', Policy No.:', policy_num, ' is now PAID UP.'), im.broker_id ".
                    "from insurances im inner join ins_plans ipm on ipm.plan_id = im.plan_id ".
                    "inner join clients c on im.client_id = c.client_id where im.policy_num = '".$polNum."'";
                try
                {
                    $this->rem->insert_reminders($query);
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'Inserting Reminder for Insurance Paid up';
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $this->common->error_log($error_array);
                }

                $query = "select 'Insurance Status', im.client_id, name, '".$scriptDate2."', ".
                    "Concat(plan_name, ', Policy No.:', policy_num, ' is now PAID UP (R).'), im.broker_id ".
                    "from insurances im inner join ins_plans ipm on ipm.plan_id = im.plan_id ".
                    "inner join clients c on im.client_id = c.client_id where im.policy_num = '".$polNum."'";
                try
                {
                    $this->rem->insert_reminders($query);
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'Inserting Reminder for Insurance Paid up + 1 year (R)';
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $this->common->error_log($error_array);
                }
            }
        }
    }

    function check_insurance_complete($script_Date, $brokerID)
    {
        $resultIns = null;
        try
        {
            $resultIns = $this->rem->get_maturity_details_for_reminder($script_Date, $brokerID);
        }
        catch(Exception $e)
        {
            $error_array['error_on'] = 'Get Insurance Details for reminder of '.$brokerID;
            $error_array['error_msg'] = $e->getMessage();
            $error_array['broker_id'] = $brokerID;
            $this->common->error_log($error_array);
        }

        foreach($resultIns as $ins)
        {
            $polNum = $ins->policy_num;
            $matDate = $ins->maturity_date;
            $matType = $ins->mat_type;
            $planName = $ins->plan_name;

            $status_id = $this->rem->get_status_id('Matured');
            if(!isset($status_id->status_id)) { var_dump($status_id); }
            $data = array('`status`' => $status_id->status_id);
            $condition = array('policy_num' => $polNum, 'broker_id' => $brokerID);
            try
            {
                $this->rem->update_insurance($data, $condition);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Updating Matured Insurance reminder of '.$brokerID;
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $error_array['remarks'] = 'policy Number = '.$polNum.' and maturity type = '.$matType;
                $this->common->error_log($error_array);
            }

            // Set lower maturity amount after comparing maturityAmount and fundValue
            if($matType === "Single")
            {
                $fundV = 0;
                $matV = 0;
                try
                {
                    $resFund = $this->rem->get_fund_mat_value($polNum, $brokerID);
                    if(isset($resFund['code'])) {
                        var_dump($resFund);
                    } else {
                        if(isset($resFund[0]->fund_value))
                            $fundV = $resFund[0]->fund_value;
                        if(isset($resFund[0]->mat_amt))
                        $matV = $resFund[0]->mat_amt;
                    }
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'Getting Fund and Maturity Value of '.$brokerID;
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $error_array['remarks'] = 'policy Number = '.$polNum.' and maturity type = '.$matType;
                    $this->common->error_log($error_array);
                }

                if($fundV < $matV)
                {
                    $dataPrem = $fundV;
                    $conditionPrem = 'policy_num = "'.$polNum.'" AND user_id = "'.$brokerID.'"';
                    try
                    {
                        $updated = $this->rem->update_prem_maturity($dataPrem, $conditionPrem);
                        if(isset($updated['code'])) {
                            var_dump($updated);
                        }
                    }
                    catch(Exception $e)
                    {
                        $error_array['error_on'] = 'Updating of Premium Maturity';
                        $error_array['error_msg'] = $e->getMessage();
                        $error_array['broker_id'] = $brokerID;
                        $error_array['remarks'] = 'policy Number = '.$polNum.', maturity type = '.$matType.', fund value = '.$fundV;
                        $this->common->error_log($error_array);
                    }
                }
                $reminderMsg = $planName.' Policy No.: '.$polNum.' is MATURED now.';
            }
            else
            {
                $reminderMsg = $planName.' Policy No.: '.$polNum.' is MATURED now. Please check the details for maturity amount.';
            }

            $query = "select 'Insurance Status', im.client_id, name, '".$script_Date."', ".
                "'".$reminderMsg."', im.broker_id from insurances im inner join clients c on ".
                "im.client_id = c.client_id where im.policy_num = '".$polNum."'";
            try
            {
                $this->rem->insert_reminders($query);
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Inserting Reminder for Insurance matured';
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $error_array['remarks'] = 'policy Number = '.$polNum.', maturity type = '.$matType;
                $this->common->error_log($error_array);
            }

            try
            {
                $this->rem->complete_insurance(array('policyNum'=>$polNum));
            }
            catch(Exception $e)
            {
                $error_array['error_on'] = 'Calling Complete Insurance Procedure';
                $error_array['error_msg'] = $e->getMessage();
                $error_array['broker_id'] = $brokerID;
                $error_array['remarks'] = 'policy Number = '.$polNum.', maturity type = '.$matType;
                $this->common->error_log($error_array);
            }
        }
    }

    ///////////////////////////////////////
    //Reminder Script END
    //////////////////////////////////////



} 