<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notifications extends CI_Controller{
    function __construct()
    { 
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Tradings_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        $this->load->model('Common_model');
        $this->load->model('Doc_model');
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //get available client categories
        $categories = $this->Clients_model->get_client_category_dropdown();
        
        //echo '<pre>';print_R($categories);die;
        $data['categories'] = $categories;

        $clients = $this->Clients_model->get_clients_with_device_token();
        
        $data['clients'] = $clients;
        
        //echo '<pre>';print_r($clients);die;
        
        //data to pass to header view like page title, css, js
        $header['title']='Send Push Notification';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/app/send_notification',$data);
        $this->load->view('broker/common/footer');

    }
    
    function send()
    {
        //$isolution_android_key = 'AAAAciMUDRI:APA91bEer5iM4O1FkXrPPyetys1edRLN6m8As60kaG2lLq5v546Edvx_da-KhtbSa-Yk5KIPxtHs3u9LDYDm36DZ87cnqH7uQXBo_l0wGC5o2JA2PeEobK84dN6MZP6I_W6kOCN0kFzh';
        $isolution_android_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $isolution_ios_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $vfinancial_android_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        $vfinancial_ios_key = 'AAAAciMUDRI:APA91bHhJKf2NmBrN48LAjVYRSLnZTdcYH4Md6lrlayYgieNDcKJ_G1XelTPc8OzqAM01Fz5CLqzapP2eGOAdWo2IG0I3tb2PBr1k1rL8daAjhjjjpiFlykTkUPL5mJUhEkeFs4UiQIM';
        
        $send_to = $_POST['send_to'];
        $title = $_POST['title'];
        $body = $_POST['body'];
        $sel_cats = @$_POST['categories'];
        $sel_clients = @$_POST['clients'];
        
        $clients = $this->Clients_model->get_clients_with_device_token_1();
        
        $isolution_android_devices = array();
        $isolution_ios_devices = array();
        $vfinancial_android_devices = array();
        $vfinancial_ios_devices = array();
        $FinalClientList = array();
        
        $path    = 'assets/app/notifications/';
        
        if(isset($_FILES['image']) && $_FILES['image']['name']!='')
        {
            $file = $_FILES["image"]["tmp_name"];
            $destination = $path.$_FILES['image']['name'];
            move_uploaded_file($file, $destination);
            $img_link = base_url().$destination;
        }
        
        
                
        
        if($send_to == 'All Clients')
        {
            foreach ($clients as $client)
            {
                $brokerID = ($client->broker_id == null ? $client->user_id : $client->broker_id);
                
                if($brokerID == '0009')
                {
                    if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                    {
                        array_push($vfinancial_ios_devices, $client->device_token);
                    }
                    else
                    {
                        array_push($vfinancial_android_devices, $client->device_token);
                    }
                }
                else
                {
                    if($client->device_type == 'iOS'  || $client->device_os == 'Apple')
                    {
                        array_push($isolution_ios_devices, $client->device_token);
                    }
                    else
                    {
                        array_push($isolution_android_devices, $client->device_token);
                    }
                }
				
				//add reminder
				
                    if ( ! in_array($client->client_id, $FinalClientList)) {
                        $FinalClientList[] = $client->client_id;
                    }
                
				 
			//	$this->addReminder($title,$body,$client->client_id,$img_link);
            }
        }
        if($send_to == 'Selected Categories')
        {
            foreach ($clients as $client)
            {
                $brokerID = ($client->broker_id == null ? $client->user_id : $client->broker_id);
                
                if(in_array($client->client_category, $sel_cats))
                {
                    if($brokerID == '0009')
                    {
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            array_push($vfinancial_ios_devices, $client->device_token);
                        }
                        else
                        {
                            array_push($vfinancial_android_devices, $client->device_token);
                        }
                    }
                    else
                    {
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            array_push($isolution_ios_devices, $client->device_token);
                        }
                        else
                        {
                            array_push($isolution_android_devices, $client->device_token);
                        }
                    }
					if ( ! in_array($client->client_id, $FinalClientList)) {
                        $FinalClientList[] = $client->client_id;
                    }
					//add reminder
				//	$this->addReminder($title,$body,$client->client_id,$img_link);
                }
            }
        }
        if($send_to == 'Selected Clients')
        {
            foreach ($clients as $client)
            {
                $brokerID = ($client->broker_id == null ? $client->user_id : $client->broker_id);
                
                if(in_array($client->client_id, $sel_clients))
                {
                    if($brokerID == '0009')
                    { 
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            array_push($vfinancial_ios_devices, $client->device_token);
                        }
                        else
                        {
                            array_push($vfinancial_android_devices, $client->device_token);
                        }
                    }
                    else
                    {
                        if($client->device_type == 'iOS' || $client->device_os == 'Apple')
                        {
                            array_push($isolution_ios_devices, $client->device_token);
                        }
                        else
                        {
                            array_push($isolution_android_devices, $client->device_token);
                        }
                    }

				    //add reminder
					//$this->addReminder($title,$body,$client->client_id,$img_link);
					if ( ! in_array($client->client_id, $FinalClientList)) {
                        $FinalClientList[] = $client->client_id;
                    }
                }
            }
        }
        
        foreach ($FinalClientList as $key => $value) {
            
        $this->addReminder($title,$body,$value,$img_link);
    }
        if(count($isolution_android_devices) > 0) $this->sendPushNotificationAndriod($isolution_android_devices,$title,$body,@$img_link,$isolution_android_key);
        if(count($isolution_ios_devices) > 0) $this->sendPushNotificationIOS($isolution_ios_devices,$title,$body,@$img_link,$isolution_ios_key);
        if(count($vfinancial_android_devices) > 0) $this->sendPushNotificationAndriod($vfinancial_android_devices,$title,$body,@$img_link,$vfinancial_android_key);
        if(count($vfinancial_ios_devices) > 0) $this->sendPushNotificationIOS($vfinancial_ios_devices,$title,$body,@$img_link,$vfinancial_ios_key);
        
        return true;
    }
    
    
    private function sendPushNotificationAndriod($arrDevices,$title,$msg,$link,$accessKey)
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
    private function sendPushNotificationIOS($arrDevices,$title,$msg,$link,$accessKey)
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
	
	private function addReminder($title,$body,$client_id,$img_link )
	{
		$data = array(
        'reminder_type'=>'Notification',
        'client_id'=>$client_id,
		'reminder_message' => $body,
		'reminder_date'=> date('Y-m-d'),
		'title' => $title,
		'attachment_url'=>$img_link 
    );

		$this->db->insert('today_reminders',$data);
	}
}
?>