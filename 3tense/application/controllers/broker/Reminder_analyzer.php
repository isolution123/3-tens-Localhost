<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reminder_analyzer extends CI_Controller{
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
        $header['title'] = 'Reminder Analyzer';
        $broker_id = $this->session->userdata('broker_id');
        $username = $this->session->userdata('username');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $broker_id);
        $rem_data = $this->rem->get_reminders(array('broker_id' => $broker_id));
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['users'] = $this->user->get_users("broker_id = '".$broker_id."' AND username <> '".$username."'");
        $data['reminder'] = $rem_data;

        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/reminder/reminder_analyzer', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all reminder details from database
    public function reminder_list()
    {
        //var_dump($_GET);
        $aColumns = array('r.reminder_date','r.reminder_type','r.client_name','r.reminder_message','r.remark','r.next_date','u.name','r.reminder_status');

        $brokerID = $this->session->userdata('broker_id');

        //get values of selection i.e. Reminder type & reminder status from requested url
        if(isset($_GET['type']) && !empty($_GET['type']))
            $type = $this->input->get('type');
        else
            $type = 'personal';
        if(isset($_GET['status']) && !empty($_GET['status']))
            $status = $this->input->get('status');
        else
            $status = 'pending';
        if(isset($_GET['noOfDays']) && !empty($_GET['noOfDays']))
            $noOfDays = $this->input->get('noOfDays');
        else
            $noOfDays = '';


        /* get data passed by Datatables and send to Reminders_model */
        /*
         * Paging
         */
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }


        /*
         * Ordering
         */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                        ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }


        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
                {
                    $sWhere .= $aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
                }
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        //custom conditions - not of datatables
        $myWhere = "";
        if($sWhere == "") {
            $sWhere .= " WHERE ";
        } else {
            $sWhere .= " AND ";
        }

        // add condition for broker_id
        $myWhere .= "r.broker_id = '".$brokerID."'";

        if($noOfDays!='' and $noOfDays !=null)
        {
            if($status == 'pending')
            {
                //$noOfDays=-$noOfDays;
                $myWhere .= "AND (DATE_ADD(r.reminder_date,interval ".$noOfDays." Day) >=CURDATE() OR DATE_ADD(r.next_date,interval ".$noOfDays." Day) >=CURDATE())";
            }
            else if($status == 'completed')
            {
                //$noOfDays=-$noOfDays;
                $myWhere .= "AND completed_on IS NOT NULL AND DATE_ADD(completed_on, interval ".$noOfDays." Day) >= CURDATE()";
            }
        }

        //add conditions for selections - reminder type
        if($type == 'personal') {
            //$myWhere .= " AND r.reminder_date IS NOT NULL AND r.reminder_type NOT IN('Birthday Reminder','Anniversary Reminder','Premium Due','Insurance Maturity','Grace Date','Insurance Status','Shares Negative Balance','Fixed Income Maturity','Fixed Deposit Matured','Fixed Income PayOut','Pre Closed Fixed Income','MF Dividend','MF Redemptions','Shares Pay out','Rent','Anniversary','Commodity Sold','Assets','Liability')";
            $myWhere .= " AND r.reminder_date IS NOT NULL AND r.reminder_type IN('Personal')
                AND r.reminder_date <= '".date('Y-m-d').
                "' AND (r.next_date <= '".date('Y-m-d')."' OR next_date IS NULL)";
        } elseif($type == 'system') {
            //$myWhere .= " AND r.reminder_date IS NOT NULL AND r.reminder_type IN('Birthday Reminder','Anniversary Reminder','Premium Due','Insurance Maturity','Grace Date','Insurance Status','Shares Negative Balance','Fixed Income Maturity','Fixed Deposit Matured','Fixed Income PayOut','Pre Closed Fixed Income','MF Dividend','MF Redemptions','Shares Pay out','Rent','Anniversary','Commodity Sold','Assets','Liability')";
            $myWhere .= " AND r.reminder_date IS NOT NULL AND r.reminder_type NOT IN('Personal')
                AND r.reminder_date <= '".date('Y-m-d').
                "' AND (r.completed_on <= '".date('Y-m-d')."' OR completed_on IS NULL)";
                //"' AND (r.completed_on <= '".date('Y-m-d')."')";
        }

        //pick only reminders for today and previous days, not future ones
        $myWhere .= "AND r.reminder_date <= '".date('Y-m-d').
            "' AND (r.next_date <= '".date('Y-m-d')."' OR next_date IS NULL)";

        //set table name for selections - reminder type
        if($status == 'pending') {
            $sTable = 'today_reminders';
        } elseif($status == 'completed') {
            $sTable = 'complete_reminders';
        }

        //echo $myWhere;


            /*
             * SQL queries
             * Get data to display
             */

        $arr = $this->rem->get_all_reminders($sWhere, $myWhere, $sOrder, $sLimit, $sTable);
        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $arr['iTotal'],
            "iTotalDisplayRecords" => $arr['iFilteredTotal'],
            "aaData" => array()
        );

        if($sTable == 'complete_reminders') {
            foreach($arr['rResult'] as $rem)
            {
                $row = array();
                $row['reminder_date']=$rem->reminder_date;
                $row['reminder_type']=$rem->reminder_type;
                $row['client_name']=$rem->client_name;
                $row['reminder_message']=$rem->reminder_message;
                $row['remark']=$rem->remark;
                $row['completed_on']=$rem->completed_on;
                if($rem->concern_user === 'all') {
                    $row['name']='All Users';
                } elseif($rem->concern_user === $this->session->userdata('username')) {
                    $row['name']='SELF';
                } else {
                    $row['name']=$rem->name;
                }
                $row['reminder_status']=$rem->reminder_status;
                $row['turnaround_time']=$rem->turnaround_time;

                //add html for action
                /*$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="View Details"
                onclick="view_reminder('."'".$rem->reminder_id."'".','."'completed'".')">
                <i class="fa fa-pencil"></i></a>';*/

                $output['aaData'][] = $row;
            }
        } else {
            foreach($arr['rResult'] as $rem)
            {
                $row = array();
                $row['reminder_date']=$rem->reminder_date;
                $row['reminder_type']=$rem->reminder_type;
                $row['client_name']=$rem->client_name;
                $row['reminder_message']=$rem->reminder_message;
                $row['remark']=$rem->remark;
                $row['next_date']=$rem->next_date;
                if($rem->concern_user === 'all') {
                    $row['name']='All Users';
                } elseif($rem->concern_user === $this->session->userdata('username')) {
                    $row['name']='SELF';
                } else {
                    $row['name']=$rem->name;
                }
                $row['reminder_status']=$rem->reminder_status;

                //add html for action
                /*$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="View Details"
                    onclick="view_reminder('."'".$rem->reminder_id."'".','."'pending'".')">
                    <i class="fa fa-pencil"></i></a>';*/

                $output['aaData'][] = $row;
            }
        }
        /*while ( $aRow = $arr['rResult'] )
        {
            $row = array();
            $output['aaData'][] = ($aRow);
        }*/





        /*$data = array();
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
        );*/
        //output to json format
        echo json_encode($output);
    }

    function get_reminder_users() {
        $broker_id = $this->session->userdata('broker_id');
        $username = $this->session->userdata('username');
        $users = $this->user->get_users("broker_id = '".$broker_id."' AND username <> '".$username."'");
        if(!empty($users))
            echo json_encode($users);
        else
            echo json_encode(false);
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

    function add_reminder()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data['client_id'] = $this->input->post('clientID');
        $data['client_name'] = $this->input->post('clientName');
        $data['reminder_message'] = $this->input->post('msg');
        $remDate = DateTime::createFromFormat('d/m/Y', $this->input->post('remDate'));
        $data['reminder_date'] = $remDate->format('Y-m-d');
        $data['concern_user'] = $this->input->post('concern_user');
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
                'text' => 'Your Reminder for date '.$this->input->post('remDate').' has been added successfully'
            );
            $this->get_reminder_notify();
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
            'remark' => $this->input->post("remarks").' Start Date: '.$this->input->post("remDate"),
            'next_date' => $snoozeDate,
            'reminder_status' => 'Snooze'
        );
        $condition = array('reminder_id'=>$this->input->post("remID"));
        $this->rem->snooze_reminder($data, $condition);
        $message = array(
            'status' => 'success',
            'title' => 'Reminder status is snoozed',
            'text' => 'Your Reminder is now snoozed till '.$this->input->post("snoozeDate")
        );
        $this->get_reminder_notify();
        echo json_encode($message);
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
        $this->get_reminder_notify();
        echo json_encode($message);
    }

    function send_mail()
    {
        $reminder_id = $this->input->post("remID");
        $result = $this->rem->get_client_reminder($reminder_id);
        $brokerID = $this->session->userdata('broker_id');
        if(isset($result))
        {
            $msg = $this->mail->send_mail($result[0]->email_id, $result, $brokerID, 'Client');
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
                'text' => 'Email ID of the client is not valid'
            );
        }
        echo json_encode($message);
    }
} 