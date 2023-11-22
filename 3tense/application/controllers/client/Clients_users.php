<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients_users extends CI_Controller {
    function __construct()
    {
      //ajinkya 5-12-2016
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->library('Custom_exception');
        $this->load->library('Mail', 'mail');
        //load adminUsers model
        $this->load->model('client/Clientlogin_model');
    }

    public function index()
    {
        if($this->session->userdata('client_id') && $this->session->userdata('user_id')) {
            redirect('client/Dashboard');
        } else {
            $data['title']='3tense | Client Login';
            $this->load->view('client/Client_login', $data);
        }
    }


    public function logout()
    {

        $this->session->sess_destroy();
        if($this->session->userdata('url')!=''){
          redirect($this->session->userdata('url'));
        }
        else{
          redirect('client/Clients_users');
	       }
    }

    public function change_password()
    {
    $this->load->view('client/common/header');
    $this->load->view('client/change_pwd');
    $this->load->view('client/common/footer');
    }

    public function change()
    {
      $client_id = $this->session->userdata('client_id');
      $oldpass=$this->input->post('oldpass');
      $newpass=$this->input->post('newpass');
      $cnfmpass=$this->input->post('cnfmpass');
      $this->load->model("client/Clientlogin_model");
      //$this->reset_model->reset_pass();
      if(!empty($oldpass) && !empty($newpass) && !empty($cnfmpass))
      {
        if($newpass==$cnfmpass)
        {
                  $rs=$this->Clientlogin_model->checkPassword($client_id,$oldpass);
                  if($rs)
                  {
                    if($query = $this->Clientlogin_model->change($client_id,$newpass))
                    {
                        $data['message'] = '<div   style="color:#3c763d;padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#dff0d8">Password Is Changed Successfully!</div>';
                        $data['title']='3tense | Change Password';
                        $this->load->view('client/common/header');
                        $this->load->view('client/change_pwd',$data);
                    }
                    else
                    {
                      $data['message'] = '<div style=" color:#a94442; padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">Something is wrong. Please Try Again!</div>';
                        $data['title']='3tense | Change Password';
                        $this->load->view('client/common/header');
                        $this->load->view('client/change_pwd',$data);
                        $this->load->view('client/common/footer');
                    }

                  }
                  else {
                    $data['message'] = '<div style=" color:#a94442; padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">Old Password Does Not Match. Please Try Again!</div>';
                      $data['title']='3tense | Change Password';
                      $this->load->view('client/common/header');
                      $this->load->view('client/change_pwd',$data);
                      $this->load->view('client/common/footer');
                  }

        }
        else {
          $data['message'] = '<div style=" color:#a94442; padding: 15px; margin-top: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">New Password and Confirm Password do not match. Please try again!</div>';
          $data['title']='3tense | Change Password';
          $this->load->view('client/common/header');
          $this->load->view('client/change_pwd',$data);
          $this->load->view('client/common/footer');
        }
      }
      else{
          $data['message'] = '<div style=" color:#a94442; padding: 15px;  margin-top: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">Password fields cannot be blank</div>';
          $data['title']='3tense | Change Password';
          $this->load->view('client/common/header');
          $this->load->view('client/change_pwd',$data);
          $this->load->view('client/common/footer');
        }
    }


    public function login()
    {

          $data['title']='3tense | Client Login';
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if($this->form_validation->run() == FALSE)
        {
            //validation fails
              $data['title']='3tense | Client Login';
              $this->load->view('common/header',$data);
            $this->load->view('client/Client_login');
        }
        else
        {
            //validation succeeds
            if ($this->input->post('btn_login') == "Login")
            {

                //check if username and password is correct
                $username = $this->input->post('username');
                $pwd = $this->input->post('password');
                $Client_Type=$this->input->post('Client_Type');
                $result = $this->Clientlogin_model->userAuth($username, $pwd);

                if($result)
                {
                    
                    
                    
                    $result1 = $this->Clientlogin_model->get_all_family_member($result->family_id);
                    
                    $user_id = $result->broker_id;
                    if($result->broker_id == null)
                        $user_id = $result->id;
                    $sess_user = array(
                        'client_id'=>$result->client_id,
                        'client_name'=>$result->name,
                        'family_client_id'=>$result->client_id,
                        'family_client_name'=>$result->name,
                        'user_id'=>$user_id,
                        'family'=>$result->head_of_family,
                        'family_id'=>$result->family_id,
                        'clients_list'=>$result1,
                        'EUIN'=>$result->EUIN,
                        'BSCUserId'=>$result->BSCUserId,
                        'BSCMemberId'=>$result->BSCMemberId,
                        'BSCPassword'=>$result->BSCPassword
                    );
                    $sess_user['head']='';
                    if($result->head_of_family==1)
                    {
                      $sess_user['head']='yes';
                      $sess_user['type']='head';
                    }
                    $this->session->set_userdata($sess_user);
                    redirect('client/Dashboard');
                  }

                }
                else
                {
                    $header['message']='<div class="box" style="text-center">Invalid username  password!</div>';
                    $this->session->set_flashdata('msg', '<div class="box" style="text-center">Invalid username  password!</div>');
                    $data['title']='3tense | Client Login';
                    $this->load->view('common/header',$data);
                      $this->load->view('client/Client_login');
                }
            }
        }
      public  function Sethead()
       {
            $sess_user['type']='head';
            $sess_user['client_id']=$this->session->userdata('family_client_id');
            $sess_user['client_name']=$this->session->userdata('family_client_name');
            
             $this->session->set_userdata($sess_user);
             redirect ('client/dashboard');

       }
       public  function Unsethead()
        {

              $this->session->unset_userdata('type');

              redirect ('client/dashboard');
        }
        
            
         public function clientChange()
    {
        $clientID = isset($_GET['id'])?$_GET['id'] : $_POST['id'];
        
        $result = $this->Clientlogin_model->get_client_detail($clientID);
        $sess_user['client_id']=$result->client_id;
        $sess_user['client_name']=$result->name;
      
        $this->session->set_userdata($sess_user);
        $this->session->unset_userdata('type');
          //   redirect ('client/dashboard');
            return $sess_user;
        
    }
        
    }
