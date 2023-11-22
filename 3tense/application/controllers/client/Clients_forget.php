<?php
class Clients_forget extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->library('Mail', 'mail');
        $this->load->model('client/Clientsend_e_model');
    }

    public function index()
    {
    $data['title']='3tense | Forgot Password';
        $this->load->view('client/Client_forget',$data);
    }

    function pass(){
        $this->load->model('client/Clientsend_e_model');
        if($query = $this->Clientsend_e_model->reset_member())
        {
            $data['message'] = '<div style=" color:#3c763d; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#d6e9c6"><strong> Mail sent on email address! ';
              $data['title']='3tense | Send Password Link';
            $this->load->view('client/Client_login',$data);
        }
        else
        {
              $data['message'] = 'Something Went Wrong Please Try Again!';
              $data['title']='3tense | Send Password Link';
            $this->load->view('client/Client_forget',$data);
        }
    }

    function reset(){
        $this->load->model('client/Clientsend_e_model');
        $this->load->model('send_model');
        $hash = $this->input->get('hash');
        if($query=$this->Clientsend_e_model->resetpass($hash))
        {
          $data['title']='3tense | Reset Password';
            $data['id'] = $query->client_id;
            $this->load->view('client/Client_reset', $data);
        }
        elseif($query=$this->send_model->resetpass($hash)){
          $data['title']='Reset Password | 3tense';
            $data['id'] = $query->id;
            $this->load->view('client/Client_reset', $data);
        }
        else{
            $data['title']='3tense | Reset Password';
            $this->load->view('client/Client_login', $data);
        }
    }
    function set()
    {
        $client_id=$this->input->post('client_id');
        $pass=$this->input->post('password');
        $cnfmpas=$this->input->post('confirm');
        if($pass!==$cnfmpas){
            $data['message'] = 'Passwords Do Not Match. Try Again!';
              $data['title']='3tense | Set Password';
            $this->load->view('client/Client_reset',$data);
        }
        else{
            $this->load->model("client/Clientsend_e_model");
            #$this->reset_model->reset_pass();
            if($query = $this->Clientsend_e_model->reset_pass($client_id,$pass))
            {
              $data['message'] = '<div style=" color:#3c763d; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#d6e9c6"><strong> Password Is Reset Successfully!</strong></div>';
                $data['title']='3tense | Login Page';
                $this->load->view('client/Client_login',$data);
            }
            else
            {
              $data['message'] = 'Something is wrong. Please Try Again!';
                $data['title']='3tense | Set Password';
                $this->load->view('client/Client_reset',$data);
            }
        }
    }

}
