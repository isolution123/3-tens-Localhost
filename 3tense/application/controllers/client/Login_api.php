<?php

class Login_api extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->library('session');
        $this->load->database();
        $this->load->model('client/Clientlogin_model');
    }

    public function index(){
        if(isset($_POST['username'])&&isset($_POST['password']) && isset($_POST['url'])){
            $url=$this->input->post('url');
            if(isset($_POST['username'])&&isset($_POST['password'])&&!empty($_POST['username'])&&!empty($_POST['password'])){
                $user=$this->input->post('username');
                $pass=$this->input->post('password');
        		
                // var_dump($this->input->post());
                // die();
                $this->db->select('c.*, u.broker_id,u.EUIN,u.BSCUserId,u.BSCMemberId,u.BSCPassword');
                $this->db->from('clients c');
                $this->db->join('users u', 'u.id = c.user_id', 'inner');
                $this->db->where('c.username',$user);
                $this->db->where('c.password',sha1($pass));
                $query=$this->db->get();
        
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
                    if(!empty($result->broker_id)) {
                        $user_id = $result->broker_id;
                    } else {
                        $user_id = $result->user_id;
                    }
                    
                    $result1 = $this->Clientlogin_model->get_all_family_member($result->family_id);
                    
                    $sess_user = array(
                      'client_id'=>$result->client_id,
                      'client_name'=>$result->name,
                       'family_client_id'=>$result->client_id,
                        'family_client_name'=>$result->name,
                      'user_id'=>$user_id,
                      'family'=>$result->head_of_family,
                      'family_id'=>$result->family_id,
                      'url'=>$this->input->post('url'),
                      'EUIN'=>$result->EUIN,
                      'BSCUserId'=>$result->BSCUserId,
                      'BSCMemberId'=>$result->BSCMemberId,
                      'BSCPassword'=>$result->BSCPassword,
                        'clients_list'=>$result1,
                      
                      
                      
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
                    if(isset($url) && !empty($url)) {
                        redirect($url.'?status='.$status.'&message='.$message);
                    } else {
                        $data['title']='3tense';
                        $this->load->view('client/Client_login', $data);          
                    }
            }
        }
        else{
      	      $data['title']='3tense';
              $this->load->view('client/Client_login', $data);
        }
    }


}
