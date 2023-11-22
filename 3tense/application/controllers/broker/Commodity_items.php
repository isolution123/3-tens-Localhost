<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Commodity_items extends CI_Controller {
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
        $header['title']='Commodity Items Master';
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
        $this->load->view('broker/master/commodity_items');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    /* Commodity-related Masters functions */
    //gets all commodity items of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->comm->get_commodity_items("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $commodity_item)
        {
            $num++;
            $row = array();
            $row['item_id']=$commodity_item->item_id;
            $row['item_name']=$commodity_item->item_name;

            //add html for action
            if(!($commodity_item->broker_id == null || $commodity_item->broker_id == '')) {
                $permissions=$this->session->userdata('permissions');
                if($permissions =="3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_commodity_item('."'".$commodity_item->item_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_commodity_item('."'".$commodity_item->item_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions=="2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_commodity_item('."'".$commodity_item->item_id."'".')">
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

    /* Commodity Items Functions START */
    // function to add new commodity_item - called by ajax
    public function add_commodity_item() {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_item_data = $_POST;
        $commodity_item_data['broker_id'] = $broker_id;

        $condition = "`item_name` = '".trim($commodity_item_data['item_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->comm->check_duplicate('commodity_items',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->comm->add_commodity_item($commodity_item_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Commodity Item added!",
                        "text" => "Commodity Item `".$commodity_item_data['item_name']."` added successfully.",
                        "item_id" => $inserted
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
                    "title" => "Could not add Commodity Item!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Item already exists!",
                "text" => "The Commodity Item you are trying to add already exists. Please change the name of the Commodity Item or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get commodity_item by item_id
    public function edit_commodity_item()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->comm->get_commodity_items("item_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_commodity_item()
    {
        $broker_id = $this->session->userdata('broker_id');
        $commodity_item_data = $_POST;
        $commodity_item_data['broker_id'] = $broker_id;
        $condition = "`item_name` = '".trim($commodity_item_data['item_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->comm->check_duplicate('commodity_items',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->comm->update_commodity_item($commodity_item_data, "item_id = '".$commodity_item_data['item_id']."'");
                if($updated && !is_array($updated)) {
                    $success = array(
                        "type" => "success",
                        "title" => "Commodity Item name updated!",
                        "text" => "Commodity Item name updated successfully.",
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
                    "title" => "Could not update Commodity Item!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Commodity Item name already exists!",
                "text" => "The Commodity Item name you entered already exists. Please change the name of the Commodity Item or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_commodity_item()
    {
        try
        {
            $deleted = $this->comm->delete_commodity_item($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Commodity Item deleted!",
                    "text" => "The Commodity Item you selected has been deleted."
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
                "title" => "Could not delete Commodity Item!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Commodity Item FUNCTIONS - END */
}
