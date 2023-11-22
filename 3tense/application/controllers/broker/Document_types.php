<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Document_types extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Clients_model', 'document_types');

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
        $header['title']='Document Types Master';
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
        $this->load->view('broker/master/document_types');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all documents of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->document_types->get_document_types("broker_id = ".$brokerID." or broker_id is null");

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $document_type)
        {
            $num++;
            $row = array();
            $row['document_type_id']=$document_type->document_type_id;
            $row['document_type']=$document_type->document_type;

            //add html for action
            if(!($document_type->broker_id == null || $document_type->broker_id == '')) {
              $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                    $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_document_type('."'".$document_type->document_type_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_document_type('."'".$document_type->document_type_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_document_type('."'".$document_type->document_type_id."'".')">
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

    /* Document type Functions START */
    // function to add new doc type - called by ajax
    public function add_document_type() {
        $broker_id = $this->session->userdata('broker_id');
        $document_type_data = $_POST;
        $document_type_data['broker_id'] = $broker_id;

        $condition = "`document_type` = '".trim($document_type_data['document_type'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->document_types->check_duplicate('document_types',$condition);

        if(!$isDuplicate) {
            try {
                $inserted = $this->document_types->add_document_type($document_type_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "type" => "success",
                        "title" => "New Document Type added!",
                        "text" => "Document Type `".$document_type_data['document_type']."` added successfully.",
                        "document_type_id" => $inserted
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
                    "title" => "Could not add Document Type!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Document Type already exists!",
                "text" => "The Document Type you are trying to add already exists. Please change the name of the Document Type or use the existing one."
            );
            echo json_encode($error);
        }
    }

    //get document_types by account_id
    public function edit_document_type()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->document_types->get_document_types("document_type_id = '".$this->input->post('id')."'");
        echo json_encode($data[0]);
    }

    public function update_document_type()
    {
        $broker_id = $this->session->userdata('broker_id');
        $document_type_data = $_POST;
        $document_type_data['broker_id'] = $broker_id;
        $condition = "`document_type` = '".trim($document_type_data['document_type'])."' AND (`broker_id` = '".$broker_id."' OR `broker_id` IS NULL)";

        $isDuplicate = $this->document_types->check_duplicate('document_types',$condition);

        if(!$isDuplicate) {
            try
            {
                $updated = $this->document_types->update_document_type($document_type_data, "document_type_id = '".$document_type_data['document_type_id']."'");
                if($updated && !is_array($updated)) {
                    $success = array(
                        "type" => "success",
                        "title" => "Document Type name updated!",
                        "text" => "Document Type name updated successfully.",
                        "document_type_id" => $updated
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
                    "title" => "Could not update Document Type!",
                    "text" => $e->errorMessage($updated['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $error = array(
                "type" => "error",
                "title" => "Document Type name already exists!",
                "text" => "The Document Type name you entered already exists. Please change the name of the Document Type or use the existing one."
            );
            echo json_encode($error);
        }
    }

    public function delete_document_type()
    {
        try
        {
            $deleted = $this->document_types->delete_document_type($this->input->post("id"));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Document Type deleted!",
                    "text" => "The Document Type you selected has been deleted."
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
                "title" => "Could not delete Document Type!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }
    /* Document type FUNCTIONS - END */
}
