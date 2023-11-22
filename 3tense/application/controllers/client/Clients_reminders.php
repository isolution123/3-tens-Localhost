<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients_reminders extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Client_reminders_model', 'rem');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Users_model', 'user');
        $this->load->library('Mail', 'mail');
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['client_id']))
        {
            redirect('client');
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
        $client_id = $this->session->userdata('client_id');
        $username = $this->session->userdata('name');
        $cli_condtition = array('c.status' => '1', 'fam.client_id' => $client_id);
        $rem_data = $this->rem->get_reminders(array('client_id' => $client_id));
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['users'] = $this->user->get_users("client_id = '".$client_id."' AND client_name <> '".$username."'");
        $data['reminder'] = $rem_data;

        $this->load->view('client/common/header', $header);
        $this->load->view('client/reminders', $data);
        $this->load->view('client/common/notif');
        $this->load->view('client/common/footer');
    }

    function reminder_config()
    {
        $header['title'] = 'Reminder Config';
        $client_id = $this->session->userdata('client_id');
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        $rem_data = $this->rem->get_reminder_days(array('client_id' => $client_id));
        $data['reminder'] = $rem_data[0];
        $this->load->view('client/common/header', $header);
        $this->load->view('client/reminder/reminder_config', $data);
        $this->load->view('client/common/notif');
        $this->load->view('client/common/footer');
    }


    //gets all reminder details from database
    public function reminder_list()
    {
        $client_id = $this->session->userdata('client_id');
        $rem_condition = "client_id = '$client_id' AND reminder_date <= '".date('Y-m-d').
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
            $row['reminder_id']=$rem->reminder_id;
            $row['reminder_date']=$rem->reminder_date;
            $row['reminder_type']=$rem->reminder_type;
            $row['client_name']=$rem->client_name;
            $row['reminder_message']=$rem->reminder_message;
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
        $client_id = $this->session->userdata('client_id');
        $userName = $this->session->userdata('username');

        $condition = 'reminder_date = "'.date('Y-m-d').'" AND reminder_type = "Personal"
                    AND client_id = "'.$client_id.'"';
        $rem_data = $this->rem->dash_reminder_list($condition, 50);
        $count_rem = count($rem_data);
        $header_data['reminder'] = $rem_data;
        $header_data['count_reminder'] = $count_rem;
        $this->session->set_userdata('header', $header_data);
    }

    function get_reminder_details()
    {
        $client_id = $this->session->userdata('client_id');
        $condition = array(
            'reminder_id'=>$this->input->post("id"),
            'client_id' => $client_id
        );

        $data = $this->rem->get_rem_details($condition);
        echo json_encode($data);
    }

    function add_reminder()
    {
        $client_id = $this->session->userdata('client_id');
        $data['client_id'] = $client_id;
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
            'client_view' => 1
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


    function send_mail()
    {
        $reminder_id = $this->input->post("remID");
        $result = $this->rem->get_client_reminder($reminder_id);
        $brokerID = $this->session->userdata('broker_id');
        $broker_result=$this->user->get_users("id='".$brokerID."'");
        if(isset($result))
        {
            $msg = $this->mail->send_mail2($result[0]->email_id, $result, $brokerID, 'Client',$broker_result[0]);
            if($msg == 'success')
            {
                $message = array(
                    'status' => 'success',
                    'title' => 'Mail Sent',
                    'text' => 'Mail sent successfully'
                );
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
}
