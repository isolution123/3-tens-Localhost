<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Insurance_companies extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model 'ins_comp' is a object of insuranceCompanyNames
        $this->load->model('Insurance_companies_model', 'ins_comp');
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
        $header['title']='Insurance Companies';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/ins_comp');
        $this->load->view('broker/master/add_ins_comp');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all insurance companies details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->ins_comp->get_ins_companies_list("broker_id = '$brokerID' OR broker_id IS NULL");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];

        $data = array();
        foreach($list as $ins_comp)
        {
            $num++;
            $row = array();
            $row['ins_comp_id'] = $ins_comp->ins_comp_id;
            $row['ins_comp_name'] = $ins_comp->ins_comp_name;

            if($ins_comp->broker_id != null) {
              $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_ins_comp('."'".$ins_comp->ins_comp_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_ins_comp('."'".$ins_comp->ins_comp_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
               }
               else if($permissions=="2")
               {
                 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                     onclick="edit_ins_comp('."'".$ins_comp->ins_comp_id."'".')">
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
            "recordsTotal"=>$this->ins_comp->count_all($brokerID),
            "recordsFiltered"=>$this->ins_comp->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    public function add_ins_comp()
    {
        $data = array(
            'ins_comp_name' => trim($this->input->post('insCompName')),
            'broker_id' => $this->session->userdata('broker_id')
        );
        $condition = 'ins_comp_name = "'.trim($this->input->post('insCompName')).'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('ins_comp_name', 'ins_companies', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->ins_comp->add($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Insurance Company Added!',
                    'status' => 'new',
                    'text' => 'Insurance Company "'.$this->input->post('insCompName').'" added successfully'
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
                'title'=> 'Error on Adding Insurance Company!',
                'status' => 'duplicate',
                'text' => 'Insurance Company "'.$this->input->post('insCompName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function edit_ins_comp()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->ins_comp->get_ins_comp_details(array('ins_comp_id' => $this->input->post("id"), 'broker_id' => $brokerID));
        echo json_encode($data);
    }

    public function update_ins_comp()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array(
            'ins_comp_name' => trim($this->input->post('insCompName')),
            'broker_id' => $brokerID
        );
        $condition = 'ins_comp_name = "'.trim($this->input->post('insCompName')).'"
            AND ins_comp_id != "'.$this->input->post('insCompID').'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('ins_comp_name', 'ins_companies', $condition);
        if(!$isDuplicate)
        {
            try
            {
                $status = $this->ins_comp->update(array('ins_comp_id' => $this->input->post('insCompID'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Insurance Company Updated!',
                    'status' => 'new',
                    'text' => 'Insurance Company "'.$this->input->post('insCompName').'" updated successfully'
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
                'title'=> 'Error on updating Insurance Company!',
                'status' => 'duplicate',
                'text' => 'Insurance Company "'.$this->input->post('insCompName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function delete_ins_comp()
    {
        try
        {
            $status = $this->ins_comp->delete_ins_comp(array('ins_comp_id' => $this->input->post("id"), 'broker_id' => $this->session->userdata('broker_id')));
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            echo json_encode(array("status" => 1));
        }
        catch(Custom_exception $e)
        {
            //display custom message
            echo json_encode(array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code'])));
        }
    }

    function get_ins_comp_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->ins_comp->get_ins_companies_broker_dropdown($brokerID);
        echo json_encode($result);
    }
}
