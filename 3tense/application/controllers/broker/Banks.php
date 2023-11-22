<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Banks extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load models with objects
        $this->load->model('Banks_model', 'bank');

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
        $header['title']='Banks Master';
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
        $this->load->view('broker/master/banks');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function get_branch()
    {
        $bankID = $this->input->post('bankID');
        $clientID = $this->input->post('clientID');
        $condition = array(
            'client_id' => $clientID,
            'bank_id' => $bankID
        );
        $data['branches'] = $this->bank->get_branches($condition);
        echo json_encode($data);
    }

    function get_client_banks()
    {
        $clientID = $this->input->post('clientID');
        $condition = array(
            'client_id' => $clientID,
        );
        $data['banks'] = $this->bank->get_banks_of_client($condition);
        echo json_encode($data);
    }

    function get_account_num()
    {
        $bankID = $this->input->post('bankID');
        $clientID = $this->input->post('clientID');
        $branch = $this->input->post('branch');
        $condition = array(
            'client_id' => $clientID,
            'bank_id' => $bankID,
            'branch' => $branch
        );
        $data['acc_num'] = $this->bank->get_accounts($condition);
        echo json_encode($data);
    }

    function get_account_num_by_bank()
    {
        $bankID = $this->input->post('bankID');
        $clientID = $this->input->post('clientID');
        $condition = array(
            'client_id' => $clientID,
            'bank_id' => $bankID
        );
        $data['acc_num'] = $this->bank->get_accounts($condition);
        echo json_encode($data);
    }


    //gets all banks of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->bank->get_banks("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $bank)
        {
            $num++;
            $row = array();
            $row['bank_id']=$bank->bank_id;
            $row['bank_name']=$bank->bank_name;

            //add html for action
            if(!($bank->broker_id == null || $bank->broker_id == '')) {
                $permissions=$this->session->userdata('permissions');
                if($permissions == "3")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_bank('."'".$bank->bank_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_bank('."'".$bank->bank_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
		            else if($permissions == "2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_bank('."'".$bank->bank_id."'".')">
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


    /* Bank Modal Functions START */
    // function to add new bank - called by ajax
    public function add_bank() {
        $broker_id = $this->session->userdata('broker_id');
        $bank_data = $_POST;
        $bank_data['broker_id'] = $broker_id;

        $condition = "`bank_name` = '".trim($bank_data['bank_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->bank->check_duplicate('banks',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->bank->add_bank($bank_data);
                if($inserted) {
                    if(is_array($inserted)) {
                        throw new Custom_exception();
                    } else {
                        $success = array(
                            "type" => "success",
                            "title" => "New Bank added!",
                            "text" => "Bank `".$bank_data['bank_name']."` added successfully.",
                            "bank_id" => $inserted
                        );
                        echo json_encode($success);
                    }
                } else {
                    $error = array(
                        "type" => "error",
                        "title" => "Could not add bank!",
                        "text" => "Unable to add bank details due to some error."
                    );
                    echo json_encode($error);
                }
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not add bank!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Bank already exists!",
                "text" => "The bank you are trying to add already exists. Please change the name of the bank or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get banks by account_id
    public function edit_bank()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->bank->get_banks("bank_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_bank()
    {
        $broker_id = $this->session->userdata('broker_id');
        $bank_data = $_POST;
        $bank_data['broker_id'] = $broker_id;
        $condition = "`bank_name` = '".trim($bank_data['bank_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->bank->check_duplicate('banks',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->bank->update_bank($bank_data, "bank_id = '".$bank_data['bank_id']."'");
                if($updated) {
                    if(is_array($updated)) {
                        throw new Custom_exception();
                    } else {
                        $success = array(
                            "type" => "success",
                            "title" => "Bank name updated!",
                            "text" => "Bank name updated successfully.",
                            "bank_id" => $updated
                        );
                        echo json_encode($success);
                    }
                } else {
                    $error = array(
                        "type" => "error",
                        "title" => "Could not update bank!",
                        "text" => "Unable to update bank due to some error"
                    );
                    echo json_encode($error);
                }
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not update bank!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Bank name already exists!",
                "text" => "The bank name you entered already exists. Please change the name of the bank or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_bank()
    {
        try
        {
            $deleted = $this->bank->delete_bank($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Bank deleted!",
                    "text" => "The bank you selected has been deleted."
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
                "title" => "Could not delete Bank!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Bank Modal FUNCTIONS - END */
}
