<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mutual_fund_schemes extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Common_model', 'common');

        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Mutual Fund Schemes Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/mutual_fund/mf_schemes');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all occupations of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $lastImportData = $this->common->get_last_imports("li.import_type = 'Mutual Fund NAV' AND (li.broker_id = '".$brokerID."' or li.broker_id is null)");
        /* changed the below code to show only last imported data - Salmaan - 12-05-2016
        if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d");
        }

        $lastSeventhDay = date('Y-m-d', strtotime($lastDay.' - 7 days'));
        $list = $this->mf->get_mf_schemes_hist('sh.scheme_date BETWEEN "'.$lastSeventhDay.'" AND "'.$lastDay.'"');*/

        if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastImportDay->modify('-1 days');
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d", strtotime('-1 days'));
        }
        $list = $this->mf->get_mf_schemes_hist('sh.scheme_date = "'.$lastDay.'"');

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $scheme)
        {
            $num++;
            $row = array();
            //$row['scheme_history_id']=$scheme->scheme_history_id;  use this if needed
            $row['scheme_id']=$scheme->scheme_id;
            $row['scheme_name']=$scheme->scheme_name;
            $row['scheme_type_id']=$scheme->scheme_type_id;
            $row['scheme_type']=$scheme->scheme_type;
            $row['current_nav']=$scheme->current_nav;
            $row['scheme_date']=$scheme->scheme_date;

            //add html for action
            /*if(!($occupation->broker_id == null || $occupation->broker_id == '')) {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
            } else {
                $row['action'] = '';
            }*/

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            //"recordsTotal"=>$this->family->count_all($brokerID),
            //"recordsFiltered"=>$this->family->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }
} 