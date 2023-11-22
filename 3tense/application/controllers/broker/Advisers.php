<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Advisers extends CI_Controller{
    function __construct()
    {
        parent:: __construct();
        //load library, helper
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('Custom_exception');

        //load model 'fd_comp' is a object of Fd_companies_model
        $this->load->model('Advisers_model', 'adv');
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
        $header['title']='Advisor';
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
        $this->load->view('broker/adviser/adviser_master');
        $this->load->view('broker/master/add_adviser');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all Advisor details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->adv->get_adviser($brokerID);
        //var_dump($list);
        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];

        $data = array();
        foreach($list as $adv)
        {
            $num++;
            $row['adviser_id'] = $adv->adviser_id;
            $row['adviser_name'] = $adv->adviser_name;
            $row['company_name'] = $adv->company_name;
            $row['product'] = $adv->product;
            $row['agency_code'] = $adv->agency_code;
            $row['contact_person'] = $adv->contact_person;
            $row['contact_number'] = $adv->contact_number;
            $row['held_type'] = $adv->held_type;
            if($adv->broker_id != null) {

                $permissions=$this->session->userdata('permissions');

                 if($permissions == "3" || $permissions == "2")
                    {

                        $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                        onclick="edit_adv('."'".$adv->adviser_id."'".')">
                        <i class="fa fa-pencil"></i></a>';
                    }
                    else
                    {
                        $row['action'] = '';        
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

    function add_adviser()
    {
        $condition = 'adviser_name = "'.trim($this->input->post('advName')).'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        //$isDuplicate =  $this->com->check_duplicate('adviser_name', 'advisers', $condition);
        $isDuplicate = null;
        if($isDuplicate == null)
        {
            $data = array(
                'adviser_name'=>$this->input->post('advName'),
                'company_name'=>$this->input->post('advCompName'),
                'product'=>$this->input->post('advProduct'),
                'agency_code'=>$this->input->post('advAgcCode'),
                'contact_person'=>$this->input->post('advConPerson'),
                'contact_number'=>$this->input->post('advConNumber'),
                'held_type'=>$this->input->post('held_type'),
                'broker_id' => $this->session->userdata('broker_id')
            );
            try
            {
                $status = $this->adv->add_adviser($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Advisor Added!',
                    'status' => 'new',
                    'text' => 'Advisor "'.$this->input->post('advName').'" added successfully'
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
                'title'=> 'Error on Adding Advisor!',
                'status' => 'duplicate',
                'text' => 'Advisor "'.$this->input->post('advName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    public function edit_adviser()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->adv->get_adviser_details(array('adviser_id' => $this->input->post("id"), 'broker_id' => $brokerID));
        echo json_encode($data[0]);
    }

    public function update_adviser()
    {
        $brokerID = $this->session->userdata('broker_id');
        $condition = 'adviser_name = "'.trim($this->input->post('advName')).'"
            AND adviser_id != "'.$this->input->post('advID').'"
            AND (broker_id = "'.$this->session->userdata('broker_id').'" OR broker_id IS NULL)';
        //$isDuplicate =  $this->com->check_duplicate('adviser_name', 'advisers', $condition);
        $isDuplicate = null;
        if(!$isDuplicate)
        {
            $data = array(
                'adviser_name'=>$this->input->post('advName'),
                'company_name'=>$this->input->post('advCompName'),
                'product'=>$this->input->post('advProduct'),
                'agency_code'=>$this->input->post('advAgcCode'),
                'contact_person'=>$this->input->post('advConPerson'),
                'contact_number'=>$this->input->post('advConNumber'),
                'held_type'=>$this->input->post('held_type'),
            );
            try
            {
                $status = $this->adv->update_adviser(array('adviser_id' => $this->input->post('advID'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Advisor Updated!',
                    'status' => 'new',
                    'text' => 'Advisor "'.$this->input->post('advName').'" updated successfully'
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
                'title'=> 'Error on updating Advisor!',
                'status' => 'duplicate',
                'text' => 'Advisor "'.$this->input->post('advName').'" already exists'
            );
        }
        echo json_encode($message);
    }

    function get_advisers_dropdown()
    {
        $brokerID = $this->session->userdata('broker_id');
        $result = $this->adv->get_adviser_broker_dropdown($brokerID);
        echo json_encode($result);
    }
} 