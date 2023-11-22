<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Premium_types extends CI_Controller{
    //Also known as Asset Allocation
    function __construct()
    {
        parent:: __construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model Insurance Asset model
        $this->load->model('Premium_types_model', 'premium');
        $this->load->model('Common_model', 'com');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Asset Allocation';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/premium_type');
        $this->load->view('broker/master/add_premium_type');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all insurance companies details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->premium->get_prem_types("broker_id = '$brokerID' OR broker_id IS NULL");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $prem)
        {
            $num++;
            $row = array();
            $row['prem_type_id']=$prem->prem_type_id;
            $row['prem_type_name']=$prem->prem_type_name;

            //add html for action, if added by broker
            if($prem->broker_id != null) {
              $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_prem('."'".$prem->prem_type_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_prem('."'".$prem->prem_type_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_prem('."'".$prem->prem_type_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }

            } else {
                $row['action'] = '';
            }

            array_push($data, $row);
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->premium->count_all(array('broker_id' => $brokerID)),
            "recordsFiltered"=>$this->premium->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    public function add_prem_type()
    {
        $data = array(
            'prem_type_name' => trim($this->input->post('premTypeName')),
            'broker_id' => $this->session->userdata('broker_id')
        );
        $condition = "`prem_type_name` = '".trim($this->input->post('premTypeName'))."' AND (`broker_id` = '".$this->session->userdata('broker_id')."' OR `broker_id` IS NULL)";
        $isDuplicate =  $this->com->check_duplicate('prem_type_name', 'premium_types', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->premium->add($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Asset Allocation Added!',
                    'status' => 'new',
                    'text' => 'Asset Allocation "'.$this->input->post('premTypeName').'" added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
            }
        }
        else
        {
            $message = array(
                'type' => 'error',
                'title'=> 'Error on Adding Asset Allocation!',
                'status' => 'duplicate',
                'text' => 'Asset Allocation "'.$this->input->post('premTypeName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function edit_prem_type()
    {
        $data = $this->premium->get_prem_type_details(array('prem_type_id' => $this->input->post("id")));
        echo json_encode($data);
    }

    public function update_prem_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array(
            'prem_type_name' => trim($this->input->post('premTypeName')),
            'broker_id' => $brokerID
        );
        $condition = "`prem_type_name` = '".trim($this->input->post('premTypeName'))."'
        AND `prem_type_id` != '".$this->input->post('premTypeID')."'
        AND (`broker_id` = '".$this->session->userdata('broker_id')."' OR `broker_id` IS NULL)";
        $isDuplicate =  $this->com->check_duplicate('prem_type_name', 'premium_types', $data);
        if(!$isDuplicate)
        {
            try
            {
                $status = $this->premium->update(array('prem_type_id' => $this->input->post('premTypeID'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Asset Allocation Updated!',
                    'status' => 'new',
                    'text' => 'Asset Allocation "'.$this->input->post('premTypeName').'" updated successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
            }
        }
        else
        {
            $message = array(
                'type' => 'error',
                'title'=> 'Error on updating Asset Allocation!',
                'status' => 'duplicate',
                'text' => 'Asset Allocation "'.$this->input->post('premTypeName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function delete_prem_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        try
        {
            $status = $this->premium->delete(array('prem_type_id' => $this->input->post("id"), 'broker_id' => $brokerID));
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            echo json_encode(array("status" => TRUE));
        }
        catch(Custom_exception $e)
        {
            //display custom message
            echo json_encode(array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code'])));
        }
    }

    function get_prem_type_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->premium->get_prem_type_broker_dropdown($brokerID);
        echo json_encode($result);
    }
}
