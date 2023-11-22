<?php
class Forget extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->library('Mail', 'mail');
        $this->load->model('Send_model');
    }

    public function index(){
	$data['title']='Forgot Password | 3tense';
        $this->load->view('broker/user/forget', $data);
    }
    function pass(){
        //$this->load->model('Send_model');
        if(($query = $this->Send_model->reset_member()) === true)
        {
            $data['message'] = '<div style=" color:#3c763d; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#d6e9c6"><strong> Mail sent on email address! </strong></div>';
            //$this->load->view('broker/user/login',$this->data);
            $data['title']='3tense | Login Page';
            $this->load->view('client/Client_login',$data);
        }
        else
        {
            //var_dump($query);
          //  $this->data['message'] = 'Something Went Wrong Please Try Again!';
          //  $this->load->view('broker/user/forget',$this->data);
          $data['message'] = 'Something Went Wrong Please Try Again!';
            $data['title']='3tense | Send Password Link';
            $this->load->view('client/Client_forget',$data);
        }
    }
    function reset(){
        //$this->load->model('send_model');
        $hash = $this->input->get('hash');
        if($query=$this->Send_model->resetpass($hash))
        {
	    $data['title']='Reset Password | 3tense';
            $data['id'] = $query->id;
            $this->load->view('broker/user/reset', $data);
        }
        else{
	         $data['title']='3tense';
           $data['message']="<div style='color:#3c763d;padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#dff0d8'>Password Successfully Reset</div>";
          //  $this->load->view('broker/user/login', $data);
          $this->load->view('client/Client_login',$data);
        }
    }
    function set()
    {
        $client_id=$this->input->post('client_id');
        $pass=$this->input->post('password');
        $cnfmpas=$this->input->post('confirm');
        if($pass!==$cnfmpas){
            $this->data['message'] = 'Passwords Do Not Match. Try Again!';
            //$this->load->view('broker/user/reset',$this->data);
            $data['title']='3tense | Set Password';
          $this->load->view('client/Client_reset',$data);
        }
        else{
            //$this->load->model("Send_model");
            //$this->reset_model->reset_pass();
            if($query = $this->Send_model->reset_pass($client_id,$pass))
            {$data['message'] = '<div style=" color:#3c763d; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#d6e9c6"><strong> Password Is Reset Successfully!</strong></div>';
              //  $this->load->view('broker/user/login',$this->data);
              $data['title']='3tense | Login Page';
              $this->load->view('client/Client_login',$data);
            }
            else
            {
            	$data['message'] = 'Something is wrong. Please Try Again!';
                //$this->load->view('broker/user/reset',$this->data);
                $data['title']='3tense | Set Password';
              $this->load->view('client/Client_reset',$data);
            }
        }
    }

}
