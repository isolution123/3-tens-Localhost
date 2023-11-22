<?php
if(!defined('BASEPATH'))exit('No direct script access allowed');

class Al_types extends CI_Controller{
    function __construct()
    {
        parent:: __construct();
        //load libraries and helper class
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load models with objects
        $this->load->model('Al_types_model', 'type');
        $this->load->model('Common_model', 'com');

        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //data to pass to header view like page title, css and js path
        $header['css']=array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['title'] = 'Asset or Liability Type Master';
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/asset_liability/al_type');
        $this->load->view('broker/master/add_al_type');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function ajax_al_list()
    {
        $broker_id = $this->session->userdata('broker_id');
        $where = "broker_id = '$broker_id' OR broker_id IS NULL";
        $list = $this->type->get_type($where);
        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $type)
        {
            $num++;
            $row['type_id'] = $type->type_id;
            $row['type_name'] = $type->type_name;
            if($type->broker_id != null) {
                $permissions=$this->session->userdata('permissions');

                if($permissions == "3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript: void(0)" title="Edit"
                onclick="edit_al_type('."'".$type->type_id."'".')"><i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript: void(0)" title="Delete"
                onclick="delete_al_type('."'".$type->type_id."'".')"><i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions == "2")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript: void(0)" title="Edit"
                onclick="edit_al_type('."'".$type->type_id."'".')"><i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
                <i class="fa fa-trash-o"></i></a>';  
                }
                else if($permissions == "1")
                {
                                $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
                    <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
                <i class="fa fa-trash-o"></i></a>';  
                }

            } else {
                $row['action'] = '';
            }
            array_push($data, $row);
        }
        $output = array(
            "draw" => 1,
            "recordsTotal" => $this->type->count_all($where),
            "recordsFiltered" => $this->type->count_filtered(),
            "data" => $data
        );
        echo json_encode($output);
    }

    function add_type()
    {
        $data = array(
            'type_name' => trim($this->input->post('type_name')),
            'broker_id' => $this->session->userdata('broker_id')
        );
        $condition = 'type_name = "'.trim($this->input->post('type_name')).'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate = $this->com->check_duplicate('type_name', 'al_types', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->type->add_type($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=>'New Type Added!',
                    'status' => 'new',
                    'text' => 'Asset and Liability new type "'.$this->input->post('type_name').'" added successfully'
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
                'title'=>'Error on Adding Type!',
                'status' => 'duplicate',
                'text' => 'Asset and Liability type "'.$this->input->post('type_name').'" already exists'
            );
        }
        echo json_encode($message);
    }

    function edit_type()
    {
        $condition = array('type_id' => $this->input->post('id'), 'broker_id' => $this->session->userdata('broker_id'));
        $data = $this->type->get_type_details($condition);
        echo json_encode($data);
    }

    function update_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array(
            'type_name' => trim($this->input->post('type_name')),
            'broker_id' => $brokerID
        );
        $condition = 'type_name = "'.trim($this->input->post('type_name')).'"
            AND type_id != "'.$this->input->post('type_id').'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate = $this->com->check_duplicate('type_name', 'al_types', $condition);
        if(!$isDuplicate)
        {
            try
            {
                $status = $this->type->update_type(array('type_id' => $this->input->post('type_id'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Type Updated!',
                    'status' => 'new',
                    'text' => 'Asset and Liability type "'.$this->input->post('type_name').'" updated successfully'
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
                'title'=> 'Error on updating Type!',
                'status' => 'duplicate',
                'text' => 'Asset and Liability type "'.$this->input->post('type_name').'" already exists'
            );
        }
        echo json_encode($message);
    }

    function delete_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        try
        {
            $status = $this->type->delete_type(array('type_id' => $this->input->post("id"), 'broker_id' =>$brokerID));
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

    function get_type_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->type->get_type_broker_dropdown('broker_id = "'.$brokerID.'" OR broker_id IS NULL');
        echo json_encode($result);
    }
} 