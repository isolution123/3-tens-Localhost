<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Nfo_Detail extends CI_Controller{
    function __construct()
    { 
        parent::__construct();
        //load library, helpers
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        $this->load->helper('url');

        if(empty($this->session->userdata['broker_id']) && empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
        $this->load->model('Common_model', 'common');
        
    }

    //Mutual funds list page
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Mutual Funds Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
        );

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/nfo_detail/index');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    
    function nfo_import()
    {
       // ini_set('max_execution_time', 0);
        //ini_set('memory_limit', '2048M');
        //ini_set('upload_max_filesize', '15M');
        //ini_set('post_max_size', '20M');

        $uploadedStatus = 0;
        $message = ""; $mfMessage = ""; $insertRow = true;
        $mf_data = array();
        $pip_data = array();
        $val_data = null;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            $transactionType = "";
            if (isset($_FILES["import_mf"]))
            {
                $brokerID = $this->session->userdata('broker_id');
                if ($_FILES["import_mf"]["name"] == '')
                {
                    $message = "No file selected";
                    
                }
                else
                {
                    
                    try {
                    
                    
                        if(isset($_POST["nfo_description"]))
                        {
                            $nfo_description=$_POST["nfo_description"];
                        }
                        
                        $user_id = $this->session->userdata('user_id');
                        $path="uploads/NFODetails";
                        $info = pathinfo($_FILES['import_mf']['name']);
                        $ext = $info['extension']; // get the extension of the file
                        $newname = "NFODetail.".$ext; 
                        
                        move_uploaded_file($_FILES["import_mf"]["tmp_name"], $path."/".$newname);

                                                
                        
                        
                        
                        
                        $data = array(
                             "nfo_description"=>$_POST["nfo_description"],
                             "desc_color"=>$_POST["desc_color"],
                             "nfo_image_path"=>$path."/".$newname,
                             'broker_Id'=>$this->session->userdata['broker_id'],
                             "created_by"=>$user_id
                            
                        );
                        $navData = $this->common->createNFODetail($data);
                        
                        $message = "Uploaded Successfully.";
                        $success = array(
                            "title" => "Success!",
                            "text" => $message
                        );
                        $this->session->set_userdata('success', $success);
                    } 
                    catch(Exception $e) {
                            var_dump($e);
                    }
                }
            }
            else
            {
                $message = "No file selected";
                $error = array(
                    "title" => "Error on uploading!",
                    "text" => $message
                );
                $this->session->set_userdata('error', $error);
            }
        }
        redirect('/broker/Nfo_detail/index', 'refresh');
    }
    
    function nfo_delete()
    {
       $navData = $this->common->deleteNFODetail($this->session->userdata['broker_id']);
                        
        $message = "Deleted Successfully.";
        $success = array(
            "title" => "Success!",
            "text" => $message
        );
        $this->session->set_userdata('success', $success);
                   
                
          redirect('/broker/Nfo_detail/index', 'refresh');
    }
  
}
