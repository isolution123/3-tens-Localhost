<?php
error_reporting(0);
class DocsList extends Ci_Controller{

  function __construct()
  {
    //ajinkya 5-12-2016
      parent :: __construct();
      $this->load->library('session');
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->helper('html');
        $this->load->model('Doc_model');
      $this->load->database();
  }
  public function index() // Dipak 25/01/2016 changed for document upload limit ristriction
  {
     /*$client_id = $this->session->userdata('client_id');
     $limit = $this->Doc_model->get_limit($client_id);
     $limit=intval($limit->Doc_upload_limit);
     if (!file_exists('uploads/temp_docs/'.$this->session->userdata('client_id').'/'))
       {
         mkdir('uploads/temp_docs/'.$this->session->userdata('client_id').'/', 0755, true);
       }

       /*$f='uploads/temp_docs/'.$client_id.'/';
       $io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
       $size = fgets ( $io, 4096);
       $size = substr ( $size, 0, strpos ( $size, "\t" ) );
       pclose ( $io );
       //echo 'Directory: ' . $f . ' => Size: ' . $size;

$size = 0;
      if($size>=$limit)// by default 3 gb ..or 3000000kb
      {
        $error = array(
            "title" => "Users Limit Reached!",
            "text" => "You cannot add any more users. Please contact support if you want to increase your limit."
        );
        $data['DocsDrop'] = $this->Doc_model->getDocs();
        $data['limit']='Document upload size limit is exceed ';
        $this->load->view('client/common/header');
        $this->load->view('client/UploadDocs',$data,array('error'=>''));
        $this->load->view('client/common/footer');
      }
      else
      {*/
        $data['DocsDrop'] = $this->Doc_model->getDocs();
        $this->load->view('client/common/header');
        $this->load->view('client/UploadDocs',$data,array('error'=>''));
        $this->load->view('client/common/footer');
//      }

  }
  public function upload()
  {
    $doctype=$this->input->post('DocsDrop');
    if (!file_exists('uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype))
      {
        mkdir('uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype, 0755, true);
      }
      $config['upload_path']='uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype;
      //$config['upload_path']="./images/";
      $config['allowed_types']="jpg|jpeg|gif|png|pdf|doc|docx|pptx|xls|xlsx|csv|ppt|TIFF|bmp|svg|odt|dos|odp";
      $config['max_size']=50000;
      $this->load->library('upload',$config);



    if($this->upload->do_upload()){
      // if($upload['file_size']>50000){
      //     $data['msg']='<div class="panel alert-danger">You Selected Large File</div>';
      // }

            $this->db->insert('temp_docs', $data);

            $this->db->where('id',$this->session->userdata('user_id'));
            $query=$this->db->get('users');
            $result = $query->row();
            $user = $result->username;
            $file_data = $this->upload->data();
            //var_dump($file_data);
            $reminder = array(
              'reminder_type'=> 'Client',
               'client_id' => $this->session->userdata('client_id') ,
               'client_name' => $this->session->userdata('client_name') ,
               'reminder_date' => date('Y-m-d'),
               'reminder_message'=> 'Document approval - '.$doctype.' - '.$file_data['orig_name'],
               'concern_user'=>$user,
               'broker_id' => $this->session->userdata('user_id')
            );
            $this->db->insert('today_reminders', $reminder);
            $data['msg']='<div class="panel alert-success"><p>Your document has been successfully uploaded</p></div>';
            //$data['msg']='<div class="panel alert-danger"></div>';
              $data['DocsDrop'] = $this->Doc_model->getDocs();
            $this->load->view('client/common/header');
          $this->load->view('client/UploadDocs',$data,array('error'=>''));
            $this->load->view('client/common/footer');
    }
    else {

        //var_dump($this->upload->data());
            $data['msg']='<div class="panel alert-danger">'.$this->upload->display_errors().'</div>';
            $data['DocsDrop'] = $this->Doc_model->getDocs();
            $error = array("error"=>$this->upload->display_errors());
            $this->load->view('client/common/header');
          $this->load->view('client/UploadDocs',$data);
            $this->load->view('client/common/footer');

    }
  }
}
