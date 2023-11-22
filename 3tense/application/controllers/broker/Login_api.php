<?php

class Login_api extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->model('Send_model');
        $this->load->library('Mail', 'mail');
        $this->load->database();
    }
    public function authentication()
    {
            
            if(empty($this->session->userdata['auth_code']))
            {
              redirect('broker/users/index');
            }
            else
            {
                 
              $data['title']='3tense | User Authentication';
              $this->load->view('broker/user/authentication', $data);  
            }
            
    }
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function index(){
        if(isset($_POST['url']) && !empty($_POST['url'])) {
            $url=$this->input->post('url');
        } else {
            redirect('http://localhost/3tense/');
        }
        
        if(isset($_POST['username'])&&isset($_POST['password'])&&!empty($_POST['username'])&&!empty($_POST['password'])){
            
            $user=$this->input->post('username');
            $pass=$this->input->post('password');
    		
    
            $this->db->where('username',$user);
            $this->db->where('password',sha1($pass));
            $query=$this->db->get('users');
    
            if($query->num_rows()>0)
            {
    			/*echo '<pre>';
    			var_dump($query->result());
    			var_dump($query->row());
    				echo '</pre>';*/
    				
    				
    				
    			$result = $query->row();
                $a = array(
                    'status'=>"success",
                    'message'=>"login details are correct"
                );
                $broker_id = $result->broker_id;
                if($result->broker_id == null)
                {
                    $broker_id = $result->id;   
                }
                
                
                if($broker_id=='0004'  && $user!='amit' && $user!='dhaval' &&  $user!='isolutions1')
                {
                     $auth_code=$this->generateRandomString();
                     $sess_user = array(
                    'username'=>$user,
                    'password'=>$pass,
                    'auth_code'=>$auth_code
                    );
                     

                    if(($query = $this->Send_model->authentication_mail('lb@isolutionsadvisor.com',$result->name,$auth_code,$broker_id)) === true)
                    {
                      $this->session->set_userdata($sess_user);
                        $data['message'] = '<div style=" color:#3c763d; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#d6e9c6"><strong> Mail sent on email address! </strong></div>';
                        //$this->load->view('broker/user/login',$this->data);
                        $data['title']='3tense | Authentication Page';
                        redirect('broker/users/authentication',$data);
                        
                    }
                    else
                    {

                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$query.'</div>');
                        redirect('broker/users/index');
                    }

                }
                else
                {
                    $sess_user = array(
                        'user_id'=>$result->id,
                        'name'=>$result->name,
                        'username'=>$result->username,
                        'broker_id'=>$broker_id,
                        'permissions'=>$result->permissions,
                        'url'=>$url
                    );
                    $this->session->set_userdata($sess_user);
                    redirect('broker/Dashboard');
                }
            }
            else{
                $status="failed";
                $message="Invalid Credentials";
                
    			//redirect($url.'?status='.$status.'&message='.$message.'&name='.$user.'&pass='.$pass);
    			redirect($url.'?status='.$status.'&message='.$message);
    
            }
        }
        else{
            //$a =array(
                $status="failed";
                $message="Invalid Credentials";
            //);
			//$dir=$url.'?status='.array_values($a['status']).'&message='.array_values($a['message']);

            redirect($url.'?status='.$status.'&message='.$message);

        }
    }

  
}
