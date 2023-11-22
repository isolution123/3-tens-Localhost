<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Commodity_units extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Commodities_model', 'comm');

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
        $header['title']='Commodity Units Master';
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
        $this->load->view('broker/master/commodity_units');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    /* Commodity-related Masters functions */
    //gets all commodity units of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->comm->get_commodity_units_list("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $commodity_unit)
        {
            $num++;
            $row = array();
            $row['unit_id']=$commodity_unit->unit_id;
            $row['unit_name']=$commodity_unit->unit_name;

            //add html for action
            if(!($commodity_unit->broker_id == null || $commodity_unit->broker_id == '')) {
                $permissions=$this->session->userdata('permissions');
                if($permissions=="3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_commodity_unit('."'".$commodity_unit->unit_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_commodity_unit('."'".$commodity_unit->unit_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions=="2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_commodity_unit('."'".$commodity_unit->unit_id."'".')">
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

    /* Commodity Units Functions START */
    // function to add new commodity_unit - called by ajax
    public function add_commodity_unit() {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_unit_data = $_POST;
        $commodity_unit_data['broker_id'] = $broker_id;

        $condition = "`unit_name` = '".trim($commodity_unit_data['unit_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->comm->check_duplicate('commodity_units',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->comm->add_commodity_unit($commodity_unit_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Commodity Unit added!",
                        "text" => "Commodity Unit `".$commodity_unit_data['unit_name']."` added successfully.",
                        "unit_id" => $inserted
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
                    "title" => "Could not add Commodity Unit!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Unit already exists!",
                "text" => "The Commodity Unit you are trying to add already exists. Please change the name of the Commodity Unit or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get commodity_unit by item_id
    public function edit_commodity_unit()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->comm->get_commodity_units_list("unit_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_commodity_unit()
    {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_unit_data = $_POST;
        $commodity_unit_data['broker_id'] = $broker_id;
        $condition = "`unit_name` = '".trim($commodity_unit_data['unit_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->comm->check_duplicate('commodity_units',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->comm->update_commodity_unit($commodity_unit_data, "unit_id = '".$commodity_unit_data['unit_id']."'");
                if($updated && !is_array($updated)) {
                    $success = array(
                        "type" => "success",
                        "title" => "Commodity Unit name updated!",
                        "text" => "Commodity Unit name updated successfully.",
                        "unit_id" => $updated
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
                    "title" => "Could not update Commodity Unit!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Unit name already exists!",
                "text" => "The Commodity Unit name you entered already exists. Please change the name of the Commodity Unit or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_commodity_unit()
    {
        try
        {
            $deleted = $this->comm->delete_commodity_unit($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Commodity Unit deleted!",
                    "text" => "The Commodity Unit you selected has been deleted."
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
                "title" => "Could not delete Commodity Unit!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Commodity unit FUNCTIONS - END */
}
