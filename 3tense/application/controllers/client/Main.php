<?php
class Main extends Ci_Controller{

  function __construct()
  {

      parent :: __construct();
      $this->load->library('session');
      $this->load->helper('form');
      $this->load->helper('url');
      $this->load->helper('html');
  }

  public function index(){

    $this->load->view('client/import');
  }
  public function upload()
  {
          $config['upload_path']="./images/".$this->session->userdata('client_id');
          if (!file_exists('./images/'.$this->session->userdata('client_id')))
           {
              mkdir('./images/'.$this->session->userdata('client_id'), 0777, true);
            }
            //$config['upload_path']="./images/";
          $config['allowed_types']="jpg|jpeg|gif|png|pdf|docx";
          $this->load->library('upload',$config);
          if(!$this->upload->do_upload())
          {
            $error = array("error"=>$this->upload->display_errors());
            $this->load->view("client/import",$error);
          }
          else
          {
            $file_data = $this->upload->data();
            $data['img']= base_url().'/images/'.$file_data['file_name'];
            $this->load->view('client/success_msg',$data);
           }
  }
  // public function create(){
  //   if(!is_dir($config['./upload_path'])) mkdir($config['upload_path'], 0777, TRUE);
  // }
}
