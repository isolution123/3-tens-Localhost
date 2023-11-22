<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class AppUsage extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Clients_model','client');
        $this->load->model('AppUsage_model','appusage');
        $this->load->model('Common_model');
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        $arrOperations = array('resetPassword','markReminderAsRead','getSummary','getSummaryReport',
            'getMFReport','getEquityReport','uploadDoc','getInsuranceReport',
            'getInsPremCal','getFDReport','getClientReport','getRealEstateReport',
            'getCommodityReport','getFDPremCal','getLedgerReport','getCashflowReport','getAssetLiabilityReport');
        
        $selected_operation = '';
        $sel_client = '';
        
        $where = "created_datetime BETWEEN NOW() - INTERVAL 45 DAY AND NOW() AND operation!='getReminders' AND operation!='getDocTypes'";
        
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        
        if(@$_REQUEST['client_id'] !='')
        {
            $where = $where . " AND client.client_id='".$_REQUEST['client_id']."'";
            $sel_client = $_REQUEST['client_id'];
        }
        
        if(@$_REQUEST['operation'] !='')
        {
            $where = $where . " AND log.operation='".$_REQUEST['operation']."'";
            $selected_operation = $_REQUEST['operation'];
        }
        
        
        $data['usage'] = $this->appusage->getAppUsageData($where);
        $data['operations'] = $arrOperations;
        $data['sel_operation'] = $selected_operation;
        $data['sel_client'] = $sel_client;
        
        //echo '<pre>';print_r($data['usage']);
        
        //data to pass to header view like page title, css, js
        $header['title']='App Usage';
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
        $this->load->view('broker/app_usage/index',$data);
        $this->load->view('broker/common/footer');

    }
}
?>