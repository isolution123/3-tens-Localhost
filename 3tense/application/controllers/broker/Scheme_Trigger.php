<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scheme_Trigger extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        
        $this->load->model('Scheme_Type_model', 'schemetype');
        $this->load->model('Common_model', 'com');

        
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Scheme Type';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/scheme_trigger/index');
        $this->load->view('broker/master/edit_scheme_type');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all families details from database
    public function ajax_list()
    {
        // $brokerID = $this->session->userdata('broker_id');
         $permissions=$this->session->userdata('permissions');
        //echo $permissions;
        
        $list = $this->schemetype->get_scheme_type();
        
        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $schemetype)
        {
            $num++;
            $row = array();
            $row['scheme_type_id']=$schemetype->scheme_type_id;
            $row['scheme_type']=$schemetype->scheme_type;
            $row['scheme_target_value']=$schemetype->scheme_target_value;

             if($permissions == "3")
             {
             //add html for action
             $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                 onclick="edit_family('."'".$schemetype->scheme_type_id."'".')">
                 <i class="fa fa-pencil"></i></a>';
             }
             else if($permissions == "2")
             {
                 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                 onclick="edit_family('."'".$schemetype->scheme_type_id."'".')">
                 <i class="fa fa-pencil"></i></a>
                 <a class="btn btn-sm btn-danger disable_btn">
                 <i class="fa fa-trash-o"></i></a>';   
             }
             else if($permissions == "1")
             {
                 $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
                 <i class="fa fa-pencil"></i></a>
                 <a class="btn btn-sm btn-danger disable_btn disable_btn">
                 <i class="fa fa-trash-o"></i></a>';   
             }



            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->schemetype->count_all(),
            "recordsFiltered"=>$this->schemetype->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //get family detail by their id
    public function edit_family()
    {
        $data = $this->schemetype->get_scheme_type_by_id($this->input->post("id"));
        echo json_encode($data);
    }

     public function update_scheme_type()
    {
        $brokerId = $this->session->userdata('broker_id');
        
            $data = array(
                //'scheme_type' => $this->input->post('scheme_type'),
                'scheme_target_value' => $this->input->post('scheme_target_value')
                
            );
            try
            {
                $status = $this->schemetype->update(array('scheme_type_id' => $this->input->post('scheme_type_id')), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Scheme Trigger Updated!',
                    'status' => 'new',
                    'text' => 'Scheme Trigger updated successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
            }
        
        echo json_encode($message);
    }


    //add family in database
    /*public function add_family()
    {
        $condition = 'name = "'.trim($this->input->post('famName')).'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('name', 'families', $condition);
        if($isDuplicate == null)
        {
            $data = array(
                'name' => $this->input->post('famName'),
                'status' => $this->input->post('famStatus'),
                'broker_id' => $this->session->userdata('broker_id'),
                'user_id' => $this->session->userdata('user_id')
            );

            try
            {
                $status = $this->family->add($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Family Added!',
                    'status' => 'new',
                    'text' => 'Family "'.$this->input->post('famName').'" added successfully'
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
                'title'=> 'Error on Adding Family!',
                'status' => 'duplicate',
                'text' => 'Family "'.$this->input->post('famName').'" already exists'
            );
        }
        echo json_encode($message);
    }

   

    //update family
    public function update_family()
    {
        $brokerId = $this->session->userdata('broker_id');
        $condition = 'name = "'.trim($this->input->post('famName')).'"
            AND family_id != "'.$this->input->post('famID').'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        $isDuplicate =  $this->com->check_duplicate('name', 'families', $condition);
        if($isDuplicate == null)
        {
            $data = array(
                'name' => $this->input->post('famName'),
                'status' => $this->input->post('famStatus'),
                'user_id' => $this->session->userdata('user_id')
            );
            try
            {
                $status = $this->family->update(array('family_id' => $this->input->post('famID'), 'broker_id' => $brokerId), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Family Updated!',
                    'status' => 'new',
                    'text' => 'Family name "'.$this->input->post('famName').'" updated successfully'
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
                'title'=> 'Error on updating Family!',
                'status' => 'duplicate',
                'text' => 'Family name "'.$this->input->post('famName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function delete_family()
    {
        $brokerId = $this->session->userdata('broker_id');
        try
        {
            $status = $this->family->delete(array('family_id' => $this->input->post("id"), 'broker_id' => $brokerId));
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

    function get_families_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->family->get_families_broker_dropdown($brokerID);
        echo json_encode($result);
    }*/
} 