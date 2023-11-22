<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Commodity_rates extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
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
        $header['title']='Commodity Rates Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );

        //get commodity items and units data
        $data = array();
        $brokerID = $this->session->userdata('broker_id');
        $data['items'] = $this->comm->get_commodity_items("broker_id = '".$brokerID."' OR broker_id IS NULL");
        $data['units'] = $this->comm->get_commodity_units_list("broker_id = '".$brokerID."' OR broker_id IS NULL");

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/master/commodity_rates', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    /* Commodity-related Masters functions */
    //gets all commodity rates of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->comm->get_commodity_rates_list("cr.broker_id = '".$brokerID."' OR cr.broker_id IS NULL");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $commodity_rate)
        {
            $num++;
            $row = array();
            $row['item_name']=$commodity_rate->item_name;
            $row['unit_name']=$commodity_rate->unit_name;
            $row['current_rate']=$commodity_rate->current_rate;

            //add html for action
            //if(!($commodity_rate->broker_id == null || $commodity_rate->broker_id == '')) {

                $permissions=$this->session->userdata('permissions');
                if($permissions=="3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_commodity_rate('."'".$commodity_rate->commodity_rate_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_commodity_rate('."'".$commodity_rate->commodity_rate_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions=="2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_commodity_rate('."'".$commodity_rate->commodity_rate_id."'".')">
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

            /*} else {
                $row['action'] = '';
            }*/

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

    /* Commodity Rates Functions START */
    // function to add new commodity_rate - called by ajax
    public function add_commodity_rate() {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_rate_data = $_POST;
        $commodity_rate_data['broker_id'] = $broker_id;

        $condition = "`item_id` = '".$commodity_rate_data['item_id']."'
        AND `unit_id` = '".$commodity_rate_data['unit_id']."'
        AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->comm->check_duplicate('commodity_rates',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->comm->add_commodity_rate($commodity_rate_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Commodity Rate added!",
                        "text" => "Commodity Rate for selected item and unit added successfully.",
                        "item_id" => $inserted,
                        "comm_data" => $commodity_rate_data
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
                    "title" => "Could not add Commodity Rate!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Rate for selected item and unit already exists!",
                "text" => "The Commodity Rate you are trying to add already exists. Please change the Rate of the Commodity Item using Edit if you want to update."
            );
            echo json_encode($error);
        }
    }

    //get commodity_rate by item_id
    public function edit_commodity_rate()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->comm->get_commodity_rates_list("cr.commodity_rate_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_commodity_rate()
    {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_rate_data = $_POST;
        $commodity_rate_data['broker_id'] = $broker_id;
        //$condition = "`item_name` = '".$commodity_rate_data['item_name']."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        //$isDuplicate = $this->comm->check_duplicate('commodity_items',$condition);
        $isDuplicate = false;
        if(!$isDuplicate) {
            try
            {
                $updated = $this->comm->update_commodity_rate($commodity_rate_data, "commodity_rate_id = '".$commodity_rate_data['commodity_rate_id']."'");
                if($updated) {
                    $success = array(
                        "type" => "success",
                        "title" => "Commodity Rate updated!",
                        "text" => "Commodity Rate updated successfully.",
                        "item_id" => $updated
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
                    "title" => "Could not update Commodity Rate!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Rate for selected Item and Unit already exists!",
                "text" => "The Commodity Rate already exists. Please change the name of the Commodity Item or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_commodity_rate()
    {
        $id = $this->input->post("id");
        $broker_id = $this->session->userdata('broker_id');

        //get the item_id and unit_id of selected rate record
        $rate = $this->comm->get_commodity_rates_list("cr.commodity_rate_id = '$id'");
        $rate_item = $rate[0]->item_id;
        $rate_unit = $rate[0]->unit_id;

        $condition = array("commodity_item_id"=>$rate_item, "commodity_unit_id"=>$rate_unit, "broker_id"=>$broker_id);
        //check if this rate has been used in any transaction or not
        $exists = $this->comm->check_duplicate('commodity_transactions',$condition);
        if($exists) {
            $error = array(
                "type" => "error",
                "title" => "Commodity Rate could not be deleted!",
                "text" => "Commodity Rate has been used in one or more Commodity transactions."
            );
            echo json_encode($error);
        } else {
            try
            {
                $deleted = $this->comm->delete_commodity_rate($id);
                if($deleted === true) {
                    $success = array(
                        "type" => "info",
                        "title" => "Commodity Rate deleted!",
                        "text" => "The Commodity Rate for selected Item and Unit has been deleted."
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
                    "title" => "Could not delete Commodity Rate!",
                    "text" => $e->errorMessage($deleted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        }
    }
    /* Commodity Rate FUNCTIONS - END */
}
