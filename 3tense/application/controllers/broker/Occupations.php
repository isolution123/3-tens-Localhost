<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Occupations extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Clients_model', 'occupations');

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
        $header['title']='Occupations Master';
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
        $this->load->view('broker/master/occupations');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all occupations of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->occupations->get_occupations("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $occupation)
        {
            $num++;
            $row = array();
            $row['occupation_id']=$occupation->occupation_id;
            $row['occupation_name']=$occupation->occupation_name;

            //add html for action
            if(!($occupation->broker_id == null || $occupation->broker_id == '')) {
              $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
              else if($permissions=="2")
              {
                    $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_occupation('."'".$occupation->occupation_id."'".')">
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

    /* Occupation Functions START */
    // function to add new occupation - called by ajax
    public function add_occupation() {
        $broker_id = $this->session->userdata('broker_id');
        $occupation_data = $_POST;
        $occupation_data['broker_id'] = $broker_id;

        $condition = "`occupation_name` = '".trim($occupation_data['occupation_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->occupations->check_duplicate('occupations',$condition);

        if(!$isDuplicate) {
            try
            {
                $inserted = $this->occupations->add_occupation($occupation_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Occupation added!",
                        "text" => "Occupation `".$occupation_data['occupation_name']."` added successfully.",
                        "occupation_id" => $inserted
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
                    "title" => "Could not add occupation!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Occupation already exists!",
                "text" => "The occupation you are trying to add already exists. Please change the name of the occupation or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get occupations by account_id
    public function edit_occupation()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->occupations->get_occupations("occupation_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_occupation()
    {
        $broker_id = $this->session->userdata('broker_id');
        $occupation_data = $_POST;
        $occupation_data['broker_id'] = $broker_id;
        $condition = "`occupation_name` = '".trim($occupation_data['occupation_name'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->occupations->check_duplicate('occupations',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->occupations->update_occupation($occupation_data, "occupation_id = '".$occupation_data['occupation_id']."'");
                if($updated && !is_array($updated)) {
                    $success = array(
                        "type" => "success",
                        "title" => "Occupation name updated!",
                        "text" => "Occupation name updated successfully.",
                        "occupation_id" => $updated
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
                    "title" => "Could not update occupation!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Occupation name already exists!",
                "text" => "The occupation name you entered already exists. Please change the name of the occupation or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_occupation()
    {
        try
        {
            $deleted = $this->occupations->delete_occupation($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Occupation deleted!",
                    "text" => "The occupation you selected has been deleted."
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
                "title" => "Could not delete occupation!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Occupation FUNCTIONS - END */
}
