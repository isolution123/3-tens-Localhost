<?php

class Login_api extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->library('session');
        $this->load->database();
    }

    public function index(){
   if(isset($_POST['username'])&&isset($_POST['password'])&&!empty($_POST['username'])&&!empty($_POST['password'])){
        $user=$this->input->post('username');
        $pass=$this->input->post('password');
		    $url=$this->input->post('url');
        // var_dump($this->input->post());
        // die();
        $this->db->where('username',$user);
        $this->db->where('password',sha1($pass));
        $query=$this->db->get('clients');

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
            $user_id = $result->user_id;
            $sess_user = array(
              'client_id'=>$result->client_id,
              'client_name'=>$result->name,
              'user_id'=>$user_id,
              'family'=>$result->head_of_family,
              'family_id'=>$result->family_id,
              'url'=>$this->input->post('url')
            );
            $sess_user['head']='';
              if($result->head_of_family==1)
            { $sess_user['head']='yes'; }
              $this->session->set_userdata($sess_user);
              redirect('client/Dashboard');
            }
        else{
              $status="failed";
              $message="Invalid Credentials";
			        redirect($url.'?status='.$status.'&message='.$message.'&name='.$user.'&pass='.$pass);
            }
        }

        else{
            //$a =array(
                 $status="failed";
                 $message="Invalid Credentials";
            //);
			//$dir=$url.'?status='.array_values($a['status']).'&message='.array_values($a['message']);

            redirect($url.'?status='.$status.'&message='.$message.'&name='.$user.'&pass='.$pass);

        }
    }


}
