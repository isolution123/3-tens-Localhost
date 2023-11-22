<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller{
    function __construct()
    {
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('html');
        //load adminDashboard model
        $this->load->model('Dashboard_model', 'dash');
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Clients_model', 'clients');
        $this->load->model('Insurance_model', 'ins');
        $this->load->model('Fixed_deposits_model', 'fd');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }

    }

    function index()
    {
        $this->get_reminder_notify();
        $header['title']='Dashboard';
        $data['get_info'] = $this->get_info();
        $header['js'] = array(
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.orderBars.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            //'assets/users/plugins/charts-chartjs/Chart.min.js',
            //'assets/users/demo/demo-chartjs.js',
        );
        $header['css'] = array(
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
            'assets/users/plugins/charts-morrisjs/morris.css',
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/user/dashboard', $data);
        $this->load->view('broker/common/footer');

        //var_dump($this->session->userdata('permissions'));
    }


    function get_reminder_notify()
       {
           $brokerID = $this->session->userdata('broker_id');
           $userName = $this->session->userdata('username');

           $condition = 'reminder_date = "'.date('Y-m-d').'" AND (reminder_type = "Personal" OR reminder_type = "Client")
                       AND broker_id = "'.$brokerID.'" AND
                       (concern_user = "'.$userName.'" OR concern_user = "all")';
           $rem_data = $this->rem->dash_reminder_list($condition, 50);
           $count_rem = count($rem_data);
           $header_data['reminder'] = $rem_data;
           $header_data['count_reminder'] = $count_rem;
           $this->session->set_userdata('header', $header_data);
       }



    function update_notif() {
        $count_reminder = 0;
        $reminder = null;
        if(isset($this->session->userdata['header']))
        {
            $count_reminder = $this->session->userdata['header']['count_reminder'];
            $reminder = $this->session->userdata['header']['reminder'];

            $html = '<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i>';
            !empty($count_reminder)?$html .= '<span class="badge">'.$count_reminder.'</span>':$html .= '';
            $html .= '</a>
            <ul class="dropdown-menu notifications arrow">
                <li class="dd-header">
                    <div style="float: left;">
                        <button type="button" id="addRemBtn1" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
                    </div>
                    <span>You have <b>'.$count_reminder.'</b> Personal reminder(s)</span>
                </li>
                <div class="scrollthis">';

                foreach($reminder as $rem) {
                    $html .= '<li>
                        <!--<span class="time">4 mins</span>-->';
                    if($rem->reminder_type == 'Personal') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                            <i class="fa fa-eye"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    }elseif($rem->reminder_type == 'Client') {
                          $html .= '<a href="javascript:void(0)" onclick="Client_view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                              <i class="fa fa-eye"></i>
                              <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                              <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                              <br/>
                              <span class="msg">'.$rem->reminder_message.'</span>
                          </a>';
                      }
                     elseif($rem->reminder_type == 'Premium Due' || $rem->reminder_type == 'Grace Date' || $rem->reminder_type == 'Insurance Maturity') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                            <i class="fa fa-eye"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    } elseif($rem->reminder_type == 'Birthday Reminder' || $rem->reminder_type == 'Anniversary Reminder') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-success active">
                            <i class="fa fa-gift"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    } else {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-danger">
                            <i class="fa fa-crosshairs"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    }
                    $html .= '</li>';
                }
                $html .= '</div>
                <li class="dd-footer"><a href="'.base_url('broker/Reminders').'">View All Reminders Upto Today</a></li>
            </ul>';
        } else {
            $html = '<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i></a>
            <ul class="dropdown-menu notifications arrow">
                <li class="dd-header">
                    <div style="float: left;">
                        <button type="button" id="addRemBtn" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
                    </div>
                    <span>You have <b>0</b> Personal reminder(s)</span>
                </li>
                <div class="scrollthis">Nothing to show...</div>';
        }

        echo $html;
    }

    function get_info()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data['num_clients'] = $this->clients->count_all($brokerID);
        $data['num_ins'] = $this->ins->count_all(array('fam.broker_id' => $brokerID, 'pstat.status'=>'Matured'));
        $data['num_ins_lapsed'] = $this->ins->count_all(array('fam.broker_id' => $brokerID, 'pstat.status'=>'Lapsed'));
        $data['num_fd'] = $this->fd->count_all(array('fdt.broker_id' => $brokerID, 'fdt.status'=>'Active'));
        $data['rem_list'] = $this->rem->dash_reminder_list(array('reminder_date <= ' => date('Y-m-d'),'broker_id' => $brokerID), 5);

        //get accordions data - Salmaan
        //$data['acc']['mf']['pur'] = $this->dash->get_top_mf("mft.broker_id = $brokerID AND mft.mutual_fund_type IN ('PIP','IPO')");
        //$data['acc']['mf']['red'] = $this->dash->get_top_mf("mft.broker_id = $brokerID AND mft.mutual_fund_type IN ('RED')");
        //var_dump($data['acc']['mf']);
        return $data;
    }

    function get_dash_chart()
    {
        $brokerID = $this->session->userdata('broker_id');
        $ins_result = $this->dash->get_insurance_dashboard(array('broker_id' => $brokerID));
        $ins_inv_result = $this->dash->get_top_investors_ins(array('broker_id' => $brokerID));
        $fd_inv_result = $this->dash->get_top_investors_fd(array('broker_id' => $brokerID));
        $commodity_result = $this->dash->get_commodity_dashboard(array('ci.broker_id' => $brokerID, 'unit_name'=> 'Grams'));
        $ins_data = array(); $com_data = array();
        $count = 0;
        foreach($ins_result as $item)
        {
            $ins_data[$count] = array(
                'label' => $item->status,
                'value' => $item->sum_policy
            );
            $count++;
        }
        $count = 0;
        foreach($ins_inv_result as $item)
        {
            $ins_inv_result[$count] = array(
                'label' => $item->name,
                'value' => $item->num_ins
            );
            $count++;
        }
        $count = 0;
        foreach($fd_inv_result as $item)
        {
            $fd_inv_result[$count] = array(
                'label' => $item->name,
                'value' => $item->num_fd
            );
            $count++;
        }
        $count = 0;
        foreach($commodity_result as $item)
        {
            $com_data[$count] = array(
                'y' => $item->item_name,
                'a' => $item->current_rate
            );
            $count++;
        }
        echo json_encode(array('ins' => $ins_data, 'com'=>$com_data, 'ins_inv' => $ins_inv_result, 'fd_inv'=>$fd_inv_result));
    }

    function get_pie_chart()
    {

         $brokerID = $this->session->userdata('broker_id');
                try
                {
                    $result=$this->dash->get_pie_chart_data($brokerID);
                    echo json_encode($result);
                }
                catch(Exception $e)
                {
                    $error_array['error_on'] = 'On getting data for dashboard '.$brokerID;
                    $error_array['error_msg'] = $e->getMessage();
                    $error_array['broker_id'] = $brokerID;
                    $this->common->error_logs($error_array);
                }
    }
}
