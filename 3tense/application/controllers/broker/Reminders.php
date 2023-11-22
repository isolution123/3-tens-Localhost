<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reminders extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Users_model', 'user');
        $this->load->library('Mail', 'mail');
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/js/dataTables.js'
        );
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css'
        );
        $header['title'] = 'Reminders';
        $broker_id = $this->session->userdata('broker_id');
        $username = $this->session->userdata('username');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $broker_id);
        $rem_data = $this->rem->get_reminders(array('broker_id' => $broker_id));
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['users'] = $this->user->get_users("broker_id = '".$broker_id."' AND username <> '".$username."'");
        $data['reminder'] = $rem_data;

        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/reminder/reminders', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function reminder_config()
    {
        $header['title'] = 'Reminder Config';
        $brokerID = $this->session->userdata('broker_id');
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        $rem_data = $this->rem->get_reminder_days(array('broker_id' => $brokerID));
        $data['reminder'] = $rem_data[0];
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/reminder/reminder_config', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit_reminder_days()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $condition = array('reminder_days_id' => $data['reminder_days_id'], 'broker_id' => $brokerID);
        unset($data['reminder_days_id']);
        $this->rem->update_reminder_days($data, $condition);
        $message = array(
            'status' => 'success',
            'title'=> 'Reminder Config Updated !',
            'text' => 'Reminder Config Details updated successfully'
        );
        echo json_encode($message);
    }

    //gets all reminder details from database
    public function reminder_list()
    {
        ini_set('memory_limit', '-1');
        $brokerID = $this->session->userdata('broker_id');
        $rem_condition = "broker_id = '$brokerID' AND reminder_date <= '".date('Y-m-d').
            "' AND (next_date <= '".date('Y-m-d')."' OR next_date IS NULL)";
        $list = $this->rem->get_reminders($rem_condition);
        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $rem)
        {
            $num++;
            $row = array();
            $row['checkBox'] = '<center><input type="checkbox" class="boxCheck" name="checkThis" value="'.$rem->reminder_id.'" /></center>'; //edited - Akshay R - 2017-08-23
            $row['xyz']=$rem->reminder_id; //edited - Akshay R - 2017-08-23
            $row['reminder_id']=$rem->reminder_id;
            $row['reminder_date']=$rem->reminder_date;
            $row['reminder_type']=$rem->reminder_type;
            $row['client_name']=$rem->client_name;
            $row['reminder_message']=$rem->reminder_message;
            /*edited - Akshay R - 2017-08-23*/
            if($rem->mail_sent_status == 1)
            {

                $row['mail_sent_status']="<span style='color:green;'><b>Mail Sent</b></span>"; //edited - Akshay R - 2017-08-23 WIProgress

            }
            elseif($rem->mail_sent_status == 0)
            {
                $row['mail_sent_status']="<span style='color:red;'><b>Mail not Sent</b></span>"; //edited - Akshay R - 2017-08-23 WIProgress
            }
            /*edited - Akshay R - 2017-08-23*/
            if($rem->remark != null && $rem->remark != '') {
                $row['reminder_message'].='  [REMARKS:'.$rem->remark.']';
            }
            $row['reminder_status']=$rem->reminder_status;

            //add html for action
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="View Details"
                onclick="view_reminder('."'".$rem->reminder_id."'".')">
                <i class="fa fa-pencil"></i></a>';

            $data[] = $row;
        }
     
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    function get_reminder_notify()
    {
        $brokerID = $this->session->userdata('broker_id');
        $userName = $this->session->userdata('username');
        $condition = 'reminder_date = "'.date('Y-m-d').'" AND reminder_type = "Personal"
                    AND broker_id = "'.$brokerID.'" AND
                    (concern_user = "'.$userName.'" OR concern_user = "all")';
        $rem_data = $this->rem->dash_reminder_list($condition, 50);
        $count_rem = count($rem_data);
        $header_data['reminder'] = $rem_data;
        $header_data['count_reminder'] = $count_rem;
        $this->session->set_userdata('header', $header_data);
    }

    function get_reminder_details()
    {
        $brokerID = $this->session->userdata('broker_id');
        $condition = array(
            'reminder_id'=>$this->input->post("id"),
            'broker_id' => $brokerID
        );
        $data = $this->rem->get_rem_details($condition);
        echo json_encode($data);
    }

    function add_reminder()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data['client_id'] = $this->input->post('clientID');
        $data['client_name'] = $this->input->post('clientName');
        $data['reminder_message'] = $this->input->post('msg');
        $remDate = DateTime::createFromFormat('d/m/Y', $this->input->post('remDate'));
        $data['reminder_date'] = $remDate->format('Y-m-d');
        $data['broker_id'] = $brokerID;
        $data['reminder_type'] = 'Personal';

        try
        {
            $remStatus = $this->rem->add_reminder($data);
            if(isset($remStatus['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status'=>'success',
                'title'=>'Reminder Added!!',
                'text' => 'Your Reminder for date'.$this->input->post('remDate').' has been added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($remStatus['code']));
        }
        echo json_encode($message);
    }

    function snooze()
    {
        $snoozeDate = DateTime::createFromFormat('d/m/Y', $this->input->post("snoozeDate"));
        $snoozeDate = $snoozeDate->format('Y-m-d');
        $data = array(
            'remark' => $this->input->post("remarks").' (Snoozed For: '.$this->input->post("snoozeDate").')',
            'next_date' => $snoozeDate,
            'concern_user' => $this->input->post("concern_user"),
            'reminder_status' => 'Snooze'
        );
        $condition = array('reminder_id'=>$this->input->post("remID"));
        $this->rem->snooze_reminder($data, $condition);
        $message = array(
            'status' => 'success',
            'title' => 'Reminder status is snoozed',
            'text' => 'Your Reminder is now snoozed till '.$this->input->post("snoozeDate")
        );
        echo json_encode($message);
        $this->get_reminder_notify();
    }

    function complete()
    {
        $data = array(
            'completed_on' => date('Y-m-d'),
            'reminder_status' => 'Complete',
            'next_date' => null
        );
        $condition = array('reminder_id'=>$this->input->post("remID"));
        $this->rem->complete_reminder($data, $condition);
        $message = array(
            'status' => 'success',
            'title' => 'Reminder Status',
            'text' => 'Your Reminder is now completed'
        );
        echo json_encode($message);
    }

    function approve()
     {
       $from = strpos($this->input->post('type'),'-');
       $to = strpos($this->input->post('type'),'-',$from+1);
       $index=$to-$from;
       //$pos = strpos($newstring, 'a', 1);
       // var_dump($index);
       $doctype = substr($this->input->post('type'),20,$index-3);
       $filename = substr($this->input->post('type'),$to+2);
       // var_dump($this->input->post('type'));
       // var_dump($doctype);
       // var_dump($from);
       // var_dump($to);
       // var_dump($index);
       // var_dump($filename);
       $client_id=$this->input->post('client_id');

       // Get array of all source files
       $files = scandir('uploads/temp_docs/'.$client_id.'/'.$doctype.'/');
       // Identify directories
       $source = 'uploads/temp_docs/'.$client_id.'/'.$doctype.'/'.$filename;
     //  $destination = 'uploads/clients/'.$this->session->userdata('client_id').'/'.$doctype;
       if (!file_exists('uploads/clients/'.$client_id.'/'.$doctype)) {
           mkdir('uploads/clients/'.$client_id.'/'.$doctype, 0777, true);
         }
         $destination = 'uploads/clients/'.$client_id.'/'.$doctype.'/'.$filename;
       // Cycle through all source files
       // foreach ($files as $file) {
       //   if (in_array($file, array(".",".."))) continue;
       //   // If we copied this successfully, mark it for deletion
       //   if (
           copy($source, $destination);
       //   ) {
       //     $delete[] = $source.$file;
       //   }
       // }
       // Delete all successfully-copied files
       //foreach ($delete as $file) {
         unlink('uploads/temp_docs/'.$client_id.'/'.$doctype.'/'.$filename);

        //}

   //     // Function to remove folders and files
   //     $dir = 'uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype;
   //     $dst = 'uploads/clients/'.$this->session->userdata('client_id').'/'.$doctype;
   //  function rrmdir($dir) {
   //      if (is_dir($dir)) {
   //          $files = scandir($dir);
   //          foreach ($files as $file)
   //              if ($file != "." && $file != "..") rrmdir("$dir/$file");
   //          rmdir($dir);
   //      }
   //      else if (file_exists($dir)) unlink($dir);
   //  }
    //
   //  // Function to Copy folders and files
   //  function rcopy($src, $dst) {
   //      if (file_exists ( $dst ))
   //          rrmdir ( $dst );
   //      if (is_dir ( $src )) {
   //          mkdir ( $dst );
   //          $files = scandir ( $src );
   //          foreach ( $files as $file )
   //              if ($file != "." && $file != "..")
   //                  rcopy ( "$src/$file", "$dst/$file" );
   //      } else if (file_exists ( $src ))
   //          copy ( $src, $dst );
   //  }

       $client_name = $this->input->post('client_name');
       $data = array(
           'completed_on' => date('Y-m-d'),
           'reminder_status' => 'Complete',
           'next_date' => null
       );
       //var_dump($this->input->post("remIDc"));
       $condition = array('reminder_id'=>$this->input->post("remIDc"));
       $this->rem->complete_reminder($data, $condition);
       $message = array(
           'status' => 'success',
           'title' => 'Reminder Status',
           'text' => 'Document has been Approved.<br> Please check the approved documents in Client Master Documents area of <b>'.$client_name.'</b>'
       );
       echo json_encode($message);
     }

     function delete(){
         $client_id = $this->input->post("client_id");
         $doctype = substr($this->input->post('type'),20);
         $dirPath ='uploads/temp_docs/'.$client_id.'/'.$doctype;

         if (! is_dir($dirPath)) {
             //  throw new InvalidArgumentException("$dirPath must be a directory");
             $condition = array('reminder_id'=>$this->input->post("remIDc"));
             $this->rem->delete($condition);
             $message = array(
                 'status' => 'success',
                 'title' => 'Reminder Status',
                 'text' => 'Document '.$doctype.' is already Declined'
             );

             echo json_encode($message);
          }
          else{
          if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
              $dirPath .= '/';
          }
          $files = glob($dirPath . '*', GLOB_MARK);
          foreach ($files as $file) {
              if (is_dir($file)) {
                  self::deleteDir($file);
              } else {
                  unlink($file);
              }
          }

          rmdir($dirPath);

           $condition = array('reminder_id'=>$this->input->post("remIDc"));
           $this->rem->delete($condition);
           $message = array(
               'status' => 'success',
               'title' => 'Reminder Status',
               'text' => 'Document is Declined'
           );

           echo json_encode($message);

 }
     }


    function send_mail()
    {
        $reminder_id = $this->input->post("remID");
        $result = $this->rem->get_client_reminder($reminder_id);
        $brokerID = $this->session->userdata('broker_id');
        $broker_result=$this->user->get_users("id='".$brokerID."'");
        if(isset($result))
        {
            /*            $msg = $this->mail->send_mail2($result[0]->email_id, $result, $brokerID, 'Client',$broker_result[0]);*/ //edited - Akshay R - 2017-08-23 AkshayR
            $msg = $this->mail->sendMailTemplates($result[0]->email_id, $result, $brokerID, 'Client', $broker_result[0]);
            if($msg == 'success')
            {
                $message = array(
                    'status' => 'success',
                    'title' => 'Mail Sent',
                    'text' => 'Mail sent successfully'
                );
                $updtWhereArray = array('reminder_id' => $reminder_id);
                $replaceWithData = array('mail_sent_status' => "1" );
                $this->db->where($updtWhereArray);
                $this->db->update('today_reminders', $replaceWithData);
            }
            else
            {
                $message = array(
                    'status' => 'error',
                    'title' => 'Error while sending Mail',
                    'text' => $msg
                );
            }
        }
        else
        {
            $message = array(
                'status' => 'error',
                'title' => 'Error while sending Mail',
                'text' => 'EmailID of the client is not valid'
            );
        }
        echo json_encode($message);
    }
    
    
    
    /*edited - Akshay R - 2017-08-23*/
    function sendMultiMails()
    {

        $sentToArray = $this->input->post("idSendTo");
        // explode(",", $sentToArray);
/*        json_encode($sentToArray);
        print_r ($sentToArray);*/

        $counterVar=0;
        if(isset($sentToArray) && !empty($sentToArray)){
            foreach ($sentToArray as $receiverRemID => $sentToRemValue) {
                $result = $this->rem->get_client_reminder($sentToRemValue);
                $brokerID = $this->session->userdata('broker_id');
                $broker_result=$this->user->get_users("id='".$brokerID."'");
                if(isset($result) && isset($result[0]->email_id))
                {
        /*            $msg = $this->mail->send_mail2($result[0]->email_id, $result, $brokerID, 'Client',$broker_result[0]);*/
                    $msg = $this->mail->sendMailTemplates($result[0]->email_id, $result, $brokerID, 'Client', $broker_result[0]);
        
                    if($msg == 'success')
                    {
                        $counterVar++;
                        $message = array(
                            'status' => 'success',
                            'title' => 'Mail(s) Sent',
                            'text' => $counterVar.' Mail(s) sent successfully'
                        );
                        $updtWhereArray = array('reminder_id' => $sentToRemValue);
                        $replaceWithData = array('mail_sent_status' => "1" );
                        $this->db->where($updtWhereArray);
                        $this->db->update('today_reminders', $replaceWithData);                
                        echo  $counterVar." Mail(s) sent .";
                        //echo json_encode($message);
                    }
                    else
                    {
                        $message = array(
                            'status' => 'error',
                            'title' => 'Error while sending Mail',
                            'text' => $msg.'. Please check if row(s) are selected or not'
                        );
                        echo "No Mail(s) selected.";
                        //echo json_encode($message);
                    }
                }
                else
                {
                    $message = array(
                        'status' => 'error',
                        'title' => 'Error while sending Mail End',
                        'text' => 'EmailID of the client is not valid'
                    );
                    //echo "No row(s) checked.";
                    echo json_encode($message);
                }
            }
        }
    }
    
}
