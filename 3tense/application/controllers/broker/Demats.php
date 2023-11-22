<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Demats extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load models with objects
        $this->load->model('Demat_providers_model', 'demats');

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
        $header['title']='Demat Providers Master';
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
        $this->load->view('broker/master/demat_providers');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }



    //gets all demat providers of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->demats->get_demat_providers("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $demat_provider)
        {
            $num++;
            $row = array();
            $row['provider_id']=$demat_provider->provider_id;
            $row['demat_provider']=$demat_provider->demat_provider;

            //add html for action
            if(!($demat_provider->broker_id == null || $demat_provider->broker_id == '')) {
                $permissions=$this->session->userdata('permissions');
                if($permissions="3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_demat_provider('."'".$demat_provider->provider_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_demat_provider('."'".$demat_provider->provider_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions=="2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_demat_provider('."'".$demat_provider->provider_id."'".')">
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


    /* Demat Provider Modal Functions START */
    // function to add new provider - called by ajax
    public function add_demat_provider() {
        $broker_id = $this->session->userdata('broker_id');
        $demat_provider_data = $_POST;
        $demat_provider_data['broker_id'] = $broker_id;

        $condition = "`demat_provider` = '".trim($demat_provider_data['demat_provider'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->demats->check_duplicate('demat_providers',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->demats->add_demat_provider($demat_provider_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Demat Provider added!",
                        "text" => "Demat provider `".$demat_provider_data['demat_provider']."` added successfully.",
                        "provider_id" => $inserted
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
                    "title" => "Could not add Demat provider!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Demat provider already exists!",
                "text" => "The Demat provider you are trying to add already exists. Please change its name or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get demat provider by account_id
    public function edit_demat_provider()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->demats->get_demat_providers("provider_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_demat_provider()
    {
        $broker_id = $this->session->userdata('broker_id');
        $demat_provider_data = $_POST;
        $demat_provider_data['broker_id'] = $broker_id;
        $condition = "`demat_provider` = '".trim($demat_provider_data['demat_provider'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->demats->check_duplicate('demat_providers',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->demats->update_demat_provider($demat_provider_data, "provider_id = '".$demat_provider_data['provider_id']."'");
                if($updated && !is_array($updated)) {
                    $success = array(
                        "type" => "success",
                        "title" => "Demat Provider name updated!",
                        "text" => "Demat provider name updated successfully.",
                        "bank_id" => $updated
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
                    "title" => "Could not update Demat provider!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Demat provider name already exists!",
                "text" => "The Demat provider name you entered already exists. Please change its name or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_demat_provider()
    {
        try
        {
            $deleted = $this->demats->delete_demat_provider($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Demat provider deleted!",
                    "text" => "The Demat provider you selected has been deleted."
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
                "title" => "Could not delete Demat provider!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Demat Provider Modal FUNCTIONS - END */
}
