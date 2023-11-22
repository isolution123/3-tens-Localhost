<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Trading_brokers extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load models with objects
        $this->load->model('Tradings_model', 'tradings');

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
        $header['title']='Trading Brokers Master';
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
        $this->load->view('broker/master/trading_brokers');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }



    //gets all trading_brokers of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->tradings->get_trading_brokers("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $trading_broker)
        {
            $num++;
            $row = array();
            $row['trading_broker_id']=$trading_broker->trading_broker_id;
            $row['trading_broker_name']=$trading_broker->trading_broker_name;
            //$row['held_type']=$trading_broker->held_type;

            //add html for action
            if(!($trading_broker->broker_id == null || $trading_broker->broker_id == '')) {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_trading_broker('."'".$trading_broker->trading_broker_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_trading_broker('."'".$trading_broker->trading_broker_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
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



    /* Trading Broker Modal Functions START */
    // function to add new broker - called by ajax
    public function add_trading_broker() {
        $broker_id = $this->session->userdata('broker_id');
        $trading_broker_data = $_POST;
        $trading_broker_data['broker_id'] = $broker_id;

        $condition = "`trading_broker_name` = '".trim($trading_broker_data['trading_broker_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->tradings->check_duplicate('trading_brokers',$condition);

        if(!$isDuplicate) {
            try
            {
                
                $inserted = $this->tradings->add_trading_broker($trading_broker_data);
                print_r($inserted); die();
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Trading Broker added!",
                        "text" => "Trading Broker `".$trading_broker_data['trading_broker_name']."` added successfully.",
                        "trading_broker_id" => $inserted
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
                    "title" => "Could not add Trading Broker!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Trading Broker already exists!",
                "text" => "The Trading Broker you are trying to add already exists. Please change its name or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get trading broker by account_id
    public function edit_trading_broker()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->tradings->get_trading_brokers("trading_broker_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_trading_broker()
    {
        $broker_id = $this->session->userdata('broker_id');
        $trading_broker_data = $_POST;
        $trading_broker_data['broker_id'] = $broker_id;
        $condition = "`trading_broker_name` = '".trim($trading_broker_data['trading_broker_name'])."' and trading_broker_id != '".$trading_broker_data['trading_broker_id']."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->tradings->check_duplicate('trading_brokers',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->tradings->update_trading_broker($trading_broker_data, "trading_broker_id = '".$trading_broker_data['trading_broker_id']."'");
                if($updated) {
                    $success = array(
                        "type" => "success",
                        "title" => "Trading Broker name updated!",
                        "text" => "Trading Broker name updated successfully.",
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
                    "title" => "Could not update Trading Broker!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Trading Broker name already exists!",
                "text" => "The Trading Broker name you entered already exists. Please change its name or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_trading_broker()
    {
        try
        {
            $deleted = $this->tradings->delete_trading_broker($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Trading Broker deleted!",
                    "text" => "The Trading Broker you selected has been deleted."
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
                "title" => "Could not delete Trading Broker!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Trading Broker Modal FUNCTIONS - END */
}
