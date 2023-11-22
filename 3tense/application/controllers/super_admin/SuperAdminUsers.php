<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class SuperAdminUsers extends CI_Controller {
    function __construct()
    {
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('form_validation');
        //load adminUsers model
        $this->load->model('Users_model');
    }

    public function index()
    {
        $data['title']='Login';
        $this->load->view('super_admin/SuperAdminUser/login', $data);
    }

    public function login()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if($this->form_validation->run() == FALSE)
        {
            //validation fails
            $this->load->view('super_admin/SuperAdminUser/login');
        }
        else
        {
            //validation succeeds
            if ($this->input->post('btn_login') == "Login")
            {
                //check if username and password is correct
                $username = $this->input->post('username');
                $pwd = $this->input->post('password');
                $result = $this->Users_model->adminAuth($username, $pwd, 'superAdmin');

                if($result)
                {
                    foreach($result as $res)
                    {
                        $sess_user = array(
                            'user_id'=>$res->id,
                            'name'=>$res->name,
                        );
                        $this->session->set_userdata('userSession', $sess_user);
                    }
                    redirect('super_admin/SuperAdminUser/dashboard');
                }
                else
                {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Invalid username and password!</div>');
                    redirect('super_admin/SuperAdminUsers/index');
                }
            }
        }
    }
} 