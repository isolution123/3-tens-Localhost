<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Client_types extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Clients_model', 'Client_types');

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
        $header['title']='Client Types Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/master/client_types');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all client types of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->Client_types->get_client_types("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $client_types)
        {
            $num++;
            $row = array();
            $row['client_type_id']=$client_types->client_type_id;
            $row['client_type_name']=$client_types->client_type_name;

            //add html for action
            if(!($client_types->broker_id == null || $client_types->broker_id == '')) {
                $permissions=$this->session->userdata('permissions');
              if($permissions == "3")
               {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_client_type('."'".$client_types->client_type_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_client_type('."'".$client_types->client_type_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions == "2")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_client_type('."'".$client_types->client_type_id."'".')">
                      <i class="fa fa-pencil"></i></a>
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

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            //"recordsTotal"=>$this->family->count_all($brokerID),
            //"recordsFiltered"=>$this->family->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    /* Client Type Functions START */
    // function to add new Client Type - called by ajax
    public function add_client_type() {
        $broker_id = $this->session->userdata('broker_id');
        $client_type_data = $_POST;
        $client_type_data['broker_id'] = $broker_id;
        $condition = "`client_type_name` = '".trim($client_type_data['client_type_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->Client_types->check_duplicate('client_types',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->Client_types->add_client_type($client_type_data);
                if($inserted) {
                    if(is_array($inserted)) {
                        throw new Custom_exception();
                    } else {
                        $success = array(
                            "type" => "success",
                            "title" => "New Client Type added!",
                            "text" => "Client Type `".$client_type_data['client_type_name']."` added successfully.",
                            "client_type_id" => $inserted
                        );
                        echo json_encode($success);
                    }
                } else {
                    $error = array(
                        "type" => "error",
                        "title" => "Could not add Client Type!",
                        "text" => "Unable to add Client Type due to some error"
                    );
                    echo json_encode($error);
                }
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not add Client Type!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Client Type already exists!",
                "text" => "The Client Type you are trying to add already exists. Please change the name of the Client Type or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get client type by account_id
    public function edit_client_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->Client_types->get_client_types("client_type_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_client_type()
    {
        $broker_id = $this->session->userdata('broker_id');
        $client_type_data = $_POST;
        $client_type_data['broker_id'] = $broker_id;
        $condition = "`client_type_name` = '".trim($client_type_data['client_type_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->Client_types->check_duplicate('client_types',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->Client_types->update_client_type($client_type_data, "client_type_id = '".$client_type_data['client_type_id']."'");
                if($updated) {
                    if(is_array($updated)) {
                        throw new Custom_exception();
                    } else {
                        $success = array(
                            "type" => "success",
                            "title" => "Client Type name updated!",
                            "text" => "Client Type name updated successfully.",
                            "client_type_id" => $updated
                        );
                        echo json_encode($success);
                    }
                } else {
                    $error = array(
                        "type" => "error",
                        "title" => "Could not update Client Type!",
                        "text" => "Unable to add Client Type due to some error"
                    );
                    echo json_encode($error);
                }
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not update Client Type!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Client Type name already exists!",
                "text" => "The Client Type name you entered already exists. Please change the name of the Client Type or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_client_type()
    {
        try
        {
            $deleted = $this->Client_types->delete_client_type($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Client Type deleted!",
                    "text" => "The Client Type you selected has been deleted."
                );
                echo json_encode($success);
            } else {
                throw new Custom_exception();
            }
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $response = array(
                "title" => "Could not delete Client Type!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Client Type FUNCTIONS - END */
}
