<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Fd_companies extends CI_Controller{
    function __construct()
    {
        parent:: __construct();
        //load library, helper
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('Custom_exception');

        //load model 'fd_comp' is a object of Fd_companies_model
        $this->load->model('Fd_companies_model', 'fd_comp');
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
        $header['title']='Fixed Deposit Companies';
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
        $this->load->view('broker/fixed_deposit/fd_comp');
        $this->load->view('broker/master/add_fd_comp');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all fixed deposit companies details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fd_comp->get_fd_companies("broker_id = '$brokerID' OR broker_id IS NULL");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];

        $data = array();
        foreach($list as $fd_comp)
        {
            $num++;
            $row['fd_comp_id'] = $fd_comp->fd_comp_id;
            $row['fd_comp_name'] = $fd_comp->fd_comp_name;

            if($fd_comp->broker_id != null) {
              $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_fd_comp('."'".$fd_comp->fd_comp_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_fd_comp('."'".$fd_comp->fd_comp_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_fd_comp('."'".$fd_comp->fd_comp_id."'".')">
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
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    function add_fd_comp()
    {
        $data = array(
            'fd_comp_name'=>trim($this->input->post('fdCompName')),
            'broker_id' => $this->session->userdata('broker_id')
        );
        $condition = 'fd_comp_name = "'.trim($this->input->post('fdCompName')).'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('fd_comp_name', 'fd_companies', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->fd_comp->add_comp($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Fixed Deposit Company Added!',
                    'status' => 'new',
                    'text' => 'Fixed Deposit Company "'.$this->input->post('fdCompName').'" added successfully'
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
                'title'=> 'Error on Adding Fixed Deposit Company!',
                'status' => 'duplicate',
                'text' => 'Fixed Deposit Company "'.$this->input->post('fdCompName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function edit_fd_comp()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->fd_comp->get_fd_comp_by_id(array('fd_comp_id' => $this->input->post("id"),'broker_id' => $brokerID));
        echo json_encode($data);
    }

    public function update_fd_comp()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array(
            'fd_comp_name' => trim($this->input->post('fdCompName')),
            'broker_id' => $brokerID
        );
        $condition = 'fd_comp_name = "'.trim($this->input->post('fdCompName')).'"
            AND fd_comp_id != "'.$this->input->post('fdCompID').'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('fd_comp_name', 'fd_companies', $condition);
        if(!$isDuplicate)
        {
            try
            {
                $status = $this->fd_comp->update(array('fd_comp_id' => $this->input->post('fdCompID'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Fixed Deposit Company Updated!',
                    'status' => 'new',
                    'text' => 'Fixed Deposit Company "'.$this->input->post('fdCompName').'" updated successfully'
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
                'title'=> 'Error on updating Fixed Deposit Company!',
                'status' => 'duplicate',
                'text' => 'Fixed Deposit Company "'.$this->input->post('fdCompName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function delete_fd_comp()
    {
        $brokerID = $this->session->userdata('broker_id');
        try
        {
            $status = $this->fd_comp->delete_fd_comp(array('fd_comp_id' => $this->input->post("id"), 'broker_id' => $brokerID));
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

    function get_fd_comp_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->fd_comp->get_fd_companies_broker_dropdown($brokerID);
        echo json_encode($result);
    }
}
