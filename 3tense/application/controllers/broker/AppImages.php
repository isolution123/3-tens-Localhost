<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class AppImages extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Tradings_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        $this->load->model('Common_model');
        $this->load->model('Doc_model');
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //get all dashboard images
        $path    = 'assets/app/dashboard_images/';
        $files = scandir($path);
        $images = array();
        
        //$index = 0;
        
        for($i=0;$i<count($files);$i++)
        {
            if($files[$i]!='.' && $files[$i]!='..')
            {
                $images[$i]['filename'] = $files[$i];
                $images[$i]['path'] = base_url().$path.$files[$i];
            }
        }
        
        //echo '<pre>';print_r($images);die;
        
        $data['images'] = $images;
        
        //data to pass to header view like page title, css, js
        $header['title']='App Images';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/app/images',$data);
        $this->load->view('broker/common/footer');

    }
    
    function delete()
    {
        $path    = 'assets/app/dashboard_images/';
         
        if($_GET['filename'] != '')
        {
            unlink($path.$_GET['filename']);
            unset($_GET['filename']);
        }
        
        redirect('/broker/AppImages', 'refresh');
    }
    
    function add()
    {
        if(isset($_FILES['image']))
        {
            $file = $_FILES["image"]["tmp_name"];
            $destination = $path    = 'assets/app/dashboard_images/'.$_FILES['image']['name'];
            move_uploaded_file($file, $destination);
            redirect('/broker/AppImages', 'refresh');
        }
        else
        {
            $header['title']='Add App Image';
            $header['css'] = array(
                'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
            );
             $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js',
            'assets/users/js/common.js'
        );
            //load views
            $this->load->view('broker/common/header', $header);
            $this->load->view('broker/common/notif');
            $this->load->view('broker/app/add_image');
            $this->load->view('broker/common/footer');
        }
    }
}
?>