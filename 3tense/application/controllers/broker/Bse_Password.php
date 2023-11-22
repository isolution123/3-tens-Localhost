<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Bse_Password extends CI_Controller{
    function __construct()
    {
        parent:: __construct();
        //load library, helper
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('Custom_exception');

        //load model 'fd_comp' is a object of Fd_companies_model
        $this->load->model('Bse_password_model', 'bsep');
        $this->load->model('Common_model', 'com');
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
        $header['title']='BSE Password';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        //load views
        $brokerID = $this->session->userdata('broker_id');
        $userID = $this->session->userdata('user_id');
        $bseData['data'] = $this->bsep->get_bse_password($brokerID,$userID);
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/bse_password/bse_password_master',$bseData);
        $this->load->view('broker/master/bse_password');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    
    public function edit_bse_password()
    {
        $brokerID = $this->session->userdata('broker_id');
        $bsepassword = $this->input->post('BSEPassword');
        $data = $this->bsep->edit_bse_password($brokerID,array('BSCPassword'=>$bsepassword));
        // echo $this->db->last_query(); die();
        if($data){
            $result['title'] = 'Update BSE Password';
            $result['text'] = 'BSE Password updated successfully';
            $result['status'] = '1';
        }else{
            $result['status'] = '0';
        }
        echo json_encode($result);
    }
} 