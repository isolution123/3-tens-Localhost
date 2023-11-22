<?php error_reporting(0);
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
  public function index(){

    $data['DocsDrop'] = $this->Doc_model->getDocs();
    //     $this->load->view('client/common/header');
    //     $this->load->view('client/main_view', $data,array('error'=>''));
    //     $this->load->view('client/common/footer');

      $this->load->view('client/common/header');
    $this->load->view('client/UploadDocs',$data,array('error'=>''));
    	$this->load->view('client/common/footer');
  }
  public function upload(){
$doctype=$this->input->post('DocsDrop');

    if (!file_exists('uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype)) {
        mkdir('uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype, 0777, true);
      }
      $data = array(
         'client_id' => $this->session->userdata('client_id') ,
         'user_id' => $this->session->userdata('user_id') ,
         'doc_type' => $doctype ,
         'approval' => 1
      );
      $this->db->insert('temp_docs', $data);

      $this->db->where('id',$this->session->userdata('user_id'));
      $query=$this->db->get('users');
      $result = $query->row();
      $user = $result->username;

      $reminder = array(
        'reminder_type'=> 'Client',
         'client_id' => $this->session->userdata('client_id') ,
         'client_name' => $this->session->userdata('name') ,
         'reminder_date' => date('Y-m-d'),
         'reminder_message'=> 'Document approval - '.$doctype,
         'concern_user'=>$user,
         'broker_id' => $this->session->userdata('user_id')
      );
      $this->db->insert('today_reminders', $reminder);


      $config['upload_path']='uploads/temp_docs/'.$this->session->userdata('client_id').'/'.$doctype;

      //$config['upload_path']="./images/";
    $config['allowed_types']="jpg|jpeg|gif|png|pdf|docx|pptx|xls|ppt";

    $this->load->library('upload',$config);
    if(!$this->upload->do_upload()){
      $data['DocsDrop'] = $this->Doc_model->getDocs();
      $error = array("error"=>$this->upload->display_errors());
      $this->load->view('client/common/header');
    $this->load->view('client/UploadDocs',$data,$error);
      $this->load->view('client/common/footer');
    }
    else {
      $file_data = $this->upload->data();
      $data['msg']='Your document has been successfully uploaded';
        $data['DocsDrop'] = $this->Doc_model->getDocs();
      $this->load->view('client/common/header');
    $this->load->view('client/UploadDocs',$data);
      $this->load->view('client/common/footer');
    }
  }
}
