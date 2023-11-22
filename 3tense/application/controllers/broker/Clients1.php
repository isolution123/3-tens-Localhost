<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        /* List of all clients */

        //data to pass to header view like page title, css, js
        $header['title']='Client Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/client/index');
        $this->load->view('broker/common/footer');

    }

    function add()
    {
        /* Add a new client */

        $brokerID = $this->session->userdata('user_id');
        //$data['families'] = $this->Families_model->get_families_broker_dropdown($brokerID);
        //$data['client_types'] = $this->Clients_model->get_client_types_dropdown();
        $data['action'] = 'add';

        $header['title']='Add Client';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/demo/demo-formvalidation.js',
            'assets/users/plugins/form-validation/jquery.validate.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/js/common.js'
        );
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/client/add_form', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit()
    {
        /* Edit existing client info */

        if(isset($_GET['id'])) {
            $clientID = $_GET['id'];
            $brokerID = $this->session->userdata('user_id');
            $data['action'] = 'edit';

            // get all client info from database
            $client_data = $this->Clients_model->get_client_info($clientID);

            $banks = $this->Banks_model->get_banks("broker_id = ".$brokerID." or broker_id is null and status = 1"); // get list of all available banks
            $demat_providers = $this->Demat_providers_model->get_demat_providers($brokerID);
            $ins_companies = $this->Insurance_companies_model->get_ins_companies($brokerID);
            $ins_plans = $this->Insurance_plans_model->get_ins_plans_broker(array('ins_plans.broker_id' => $brokerID));
            $contact_categories = $this->Clients_model->get_contact_categories();

            // change/add values as required (eg. dates, photo, etc)
            if(!empty($client_data->dob)) {
                $dob_temp = DateTime::createFromFormat('Y-m-d', $client_data->dob);
                $client_data->dob = $dob_temp->format('d/m/Y');
            }
            if(!empty($client_data->anv_date)) {
                $anv_temp = DateTime::createFromFormat('Y-m-d', $client_data->anv_date);
                $client_data->anv_date = $anv_temp->format('d/m/Y');
            }
            if(!empty($client_data->date_of_comm)) {
                $comm_temp = DateTime::createFromFormat('Y-m-d', $client_data->date_of_comm);
                $client_data->date_of_comm = $comm_temp->format('d/m/Y');
            }

            if((glob("uploads/clients/".$clientID."/*.*"))) {
                $client_data->photo = glob("uploads/clients/".$clientID."/*.*")[0];
            }
            if((glob("uploads/clients/".$clientID."/Signature/*.*"))) {
                $client_data->sign = glob("uploads/clients/".$clientID."/Signature/*.*")[0];
            }

            $data['client_info'] = $client_data;
            $data['banks'] = $banks;
            $data['demat_providers'] = $demat_providers;
            $data['ins_companies'] = $ins_companies;
            $data['ins_plans'] = $ins_plans;
            $data['contact_categories'] = $contact_categories;

            $header['title']='Edit Client - '.$clientID;
            $header['css'] = array(
                'assets/users/plugins/form-select2/select2.css',
                'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
            );
            $header['js'] = array(
                'assets/users/plugins/form-parsley/parsley.min.js',
                'assets/users/demo/demo-formvalidation.js',
                'assets/users/plugins/form-validation/jquery.validate.min.js',
                'assets/users/plugins/form-select2/select2.min.js',
                'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
                'assets/users/plugins/form-jasnyupload/fileinput.js',
                'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
                'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
                'assets/users/plugins/bootbox/bootbox.min.js',
                'assets/users/js/common.js'
            );
            $this->load->view('broker/common/header', $header);
            $this->load->view('broker/client/edit_form', $data);
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            /* has come to Edit page without ID, so redirect to some other page */
        }


    }

    function save()
    {
        echo 'inside save';

        if(isset($_POST['action']) && !empty($_POST['action']))
        {
        // Process the POST data and make it ready for use (for either ADD or EDIT)
        $data = $_POST;

        // change date format of date values
        if(isset($data['dob_app'])) {
            if($data['dob_app']== 'on') {
                $dob_user = $data['dob'];
                $dob_temp = DateTime::createFromFormat('d/m/Y', $dob_user);
                $data['dob'] = $dob_temp->format('Y-m-d');
                $data['dob_app'] = 1;
            } else {
                $data['dob'] = '';
                $data['dob_app'] = 0;
            }
        }
        if(isset($data['anv_app'])) {
            if($data['anv_app'] == 'on') {
                $anv_user = $data['anv_date'];
                $anv_temp = DateTime::createFromFormat('d/m/Y', $anv_user);
                $data['anv_date'] = $anv_temp->format('Y-m-d');
                $data['anv_app'] = 1;
            } else {
                $data['anv_date'] = '';
                $data['anv_app'] = 0;
            }
        }
        if(isset($data['date_of_comm'])) {
            $comm_user = $data['date_of_comm'];
            $comm_temp = DateTime::createFromFormat('d/m/Y', $comm_user);
            $data['date_of_comm'] = $comm_temp->format('Y-m-d');
        }

        // also remove 'action' value from the array
        unset($data['action']);

        // add user_id/broker_id of the client
        $data['user_id'] = $this->session->userdata('user_id');


        // Now check if new client or existing client

        if($this->input->post('action') == 'add') {
            // insert data into clients table
            $inserted = $this->Clients_model->add_client($data);

            $photoUploaded = true; // photo assumed to be uploaded by default
            // call function to upload photo, if selected
            if(isset($_FILES["photo"]["name"])) {
                //var_dump($_FILES['photo']);
                $photoUploaded = $this->uploadPhoto();
            }

            if($inserted && $photoUploaded) {
                $success = array(
                    "title" => "New Client added!",
                    "text" => "Client details for ID ".$_POST['client_id']." added successfully."
                );
                $this->session->set_userdata('success', $success);
            }

            // now redirect to the client form page again (with all data)
            redirect('broker/clients/edit?id='.$_POST['client_id']);

        } elseif($this->input->post('action') == 'edit') {
            $_SESSION['files'] = $_FILES;
            // remove hidden elements/photo data
            unset($data['hidden_family']);
            unset($data['hidden_client_type']);
            unset($data['hidden_state']);
            unset($data['photo']);

            // update data in the clients table
            $updated = $this->Clients_model->update_client($data);

            // get existing photo, if exists
            if((glob("uploads/clients/".$data['client_id']."/*.*"))) {
                $photoFilename = basename(glob("uploads/clients/".$data['client_id']."/*.*")[0]);
            } else {
                $photoFilename = '';
            }

            $photoUploaded = true; // photo assumed to be uploaded by default
            if (isset($_POST['photo']) && $_POST['photo'] == '') {
                // Delete file
                unlink(glob("uploads/clients/".$data['client_id']."/*.*")[0]);
            } elseif ($_FILES['photo']['error'] == 0)  {
                // Save uploaded file
                unlink(glob("uploads/clients/".$data['client_id']."/*.*")[0]);
                $photoUploaded = $this->uploadPhoto();
            } else {
                // photo is the same, so don't do anything
            }


            if($updated && $photoUploaded) {
                $success = array(
                    "title" => "Client updated!",
                    "text" => "Client details for ID ".$_POST['client_id']." updated successfully."
                );
                $this->session->set_userdata('success', $success);
            }

            // now redirect to the client Edit page again (with all data)
            redirect('broker/clients/edit?id='.$_POST['client_id']);
        }
        } else {
            $error = array(
                "title" => "Data error!",
                "text" => "No data was passed to the server."
            );
            $this->session->set_userdata('error', $error);
            // now redirect to the client Edit page again (with all data)
            redirect('broker/clients/edit?id='.$_POST['client_id']);
        }
    }

    //gets client list details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->Clients_model->get_clients_broker($brokerID);

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $client)
        {
            $num++;
            $row = array();
            $row['client_id']=$client->client_id;
            $row['c_name']=$client->c_name;
            $row['f_name']=$client->f_name;
            $row['relation_HOF']=$client->relation_HOF;
            $row['client_type']=$client->client_type_name;
            $row['email_id']=$client->email_id;
            $row['username']=$client->username;
            $row['date_of_comm']=$client->date_of_comm;
            $row['client_category']=$client->client_category;
            $row['status']=$client->status;

            //add html for action
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_client('."'".$client->client_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_client('."'".$client->client_id."'".')">
                <i class="fa fa-trash-o"></i></a>
                <a class="btn btn-sm btn-print" href="javascript:void(0)" title="Print"
                onclick="print_client('."'".$client->client_id."'".')">
                <i class="fa fa-file-text-o"></i></a>';

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->Clients_model->count_all($brokerID),
            "recordsFiltered"=>$this->Clients_model->count_filtered($brokerID),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    public function families_list_dropdown() {
        $brokerID = $this->session->userdata('user_id');
        $families = $this->Families_model->get_families_broker_dropdown($brokerID);
        echo json_encode($families);
    }

    public function client_types_dropdown() {
        $client_types = $this->Clients_model->get_client_types_dropdown();
        echo json_encode($client_types);
    }

    public function document_types_dropdown() {
        $doc_types = $this->Clients_model->get_document_types_dropdown();
        echo json_encode($doc_types);
    }

    public function get_clients_broker_dropdown() {
        $familyID = $this->input->post('familyID');
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id'=> $familyID);
        $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condtition);
        echo json_encode($clientID);
    }

    public function new_client_id() {
        $clientID = $this->Clients_model->get_new_client_id();
        echo json_encode($clientID);
    }

    public function uploadPhoto() {
        var_dump($_FILES);
        if ($_FILES["photo"]["error"] > 0)
        {
            // if there is error in file uploading
            $error = array(
                "title" => "Error uploading photo!",
                "text" => "Failed to upload! Return Code: " . $_FILES["photo"]["error"]
            );
            $this->session->set_userdata('error', $error);
            return false;
        }
        else
        {
            $clientID = $_POST['client_id'];
            $path = "uploads/clients/".$clientID;

            // check if file already exit in "uploads/clients/clientID" folder.
            if (file_exists($path."/".$_FILES["photo"]["name"]))
            {
                $error = array(
                    "title" => "Already exists!",
                    "text" => "Filename '" . $_FILES["photo"]["name"] . "' already exists."
                );
                $this->session->set_userdata('error', $error);
                return false;
            }
            else
            {   // create client directory if not exists
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                //move_uploaded_file function will upload your image.
                if(move_uploaded_file($_FILES["photo"]["tmp_name"], $path."/".$_FILES["photo"]["name"]))
                {
                    // If file has uploaded successfully, return TRUE
                    return true;
                }
            }
        }
    }

    public function upload_documents() {
        //var_dump($_POST);
        //echo $_FILES['docFile']['name'];
        if(isset($_FILES['docFile'])) {
            if ($_FILES["docFile"]["error"] > 0)
            {
                // if there is error in file uploading
                //echo "Failed to upload! Return Code: " . $_FILES["docFile"]["error"] . "<br />";
                $error = array(
                    "title" => "Failed to upload!",
                    "text" => "The file you selected could not be uploaded.<br/>Return Code: " . $_FILES["docFile"]["error"],
                    "type" => "error"
                );
                echo json_encode($error);
            }
            else
            {
                if($this->input->post()) {
                    // get the required values via Post
                    $clientID = $this->input->post("clientID");
                    $docTypeID = $this->input->post("docTypeID");
                    $docType = $this->input->post("docType");

                    $path = "uploads/clients/".$clientID."/".$docType;

                    if($docType == "Signature" && is_dir($path)) {
                        unlink(glob("uploads/clients/".$clientID."/Signature/*.*")[0]);
                    }

                    // check if file already exit in "images" folder.
                    if (file_exists($path."/".$_FILES["docFile"]["name"]))
                    {
                        //echo "Filename '" . $_FILES["docFile"]["name"] . "' already exists.";
                        $error = array(
                            "title" => "File/filename already exists!",
                            "text" => "The filename of uploaded file (" . $_FILES["docFile"]["name"] . ") already exists. Please upload a different file or change the filename.",
                            "type" => "error"
                        );
                        echo json_encode($error);
                    }
                    else
                    {   // create client directory and subdirectories if not exists
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        //move_uploaded_file function will upload your image.
                        if(move_uploaded_file($_FILES["docFile"]["tmp_name"], $path."/".$_FILES["docFile"]["name"]))
                        {
                            // If file has uploaded successfully, show the success message
                            //echo "Successfully uploaded file : " . $_FILES["docFile"]["name"];
                            $success = array(
                                "title" => "New ".$docType." Document added!",
                                "text" => "Successfully uploaded file : " . $_FILES["docFile"]["name"],
                                "type" => "success"
                            );
                            echo json_encode($success);
                        }
                    }
                }
            }
        } else {
            // file array was not set, so there's some problem. Most probably no file was uploaded.
            //echo "No file selected to upload!";
            $error = array(
                "title" => "No file selected!",
                "text" => "You have not selected any file to upload. Please select a file and the try again.",
                "type" => "error"
            );
            echo json_encode($error);
        }
    }

    public function getSignatureFile() {
        $clientID = $this->input->post('clientID');
        if((glob("uploads/clients/".$clientID."/Signature/*.*"))) {
            echo glob("uploads/clients/".$clientID."/Signature/*.*")[0];
        } else {
            echo false;
        }
    }

    public function getPhotoFile() {
        $clientID = $this->input->post('clientID');
        if((glob("uploads/clients/".$clientID."/*.*"))) {
            echo glob("uploads/clients/".$clientID."/*.*")[0];
        } else {
            echo false;
        }
    }

    /* BANK ACCOUNT FUNCTIONS - START */
    // get list of all bank account details of client
    public function ajax_bank_accounts()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $brokerID = $this->session->userdata('broker_id');
            $list = $this->Bank_accounts_model->get_client_bank_accounts($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            foreach($list as $bank_account)
            {
                $num++;
                $row = array();
                $row['bank_name']=$bank_account->bank_name;
                $row['branch']=$bank_account->branch;
                $row['IFSC']=$bank_account->IFSC;
                $row['account_type']=$bank_account->account_type;
                $row['account_number']=$bank_account->account_number;

                //add html for action
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_bank_account('."'".$bank_account->account_id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "recordsTotal"=>$this->Bank_accounts_model->bank_accounts_count_all($clientID),
                "recordsFiltered"=>$this->Bank_accounts_model->bank_accounts_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    // function to add new bank account details of client - called by ajax
    public function add_bank_account() {
        $bank_account_data = $_POST;
        $inserted = $this->Bank_accounts_model->add_bank_account($bank_account_data);
        if($inserted == true) {
            $success = array(
                "title" => "New Bank Account added!",
                "text" => "Bank Account details for ID ".$_POST['client_id']." added successfully."
            );
            echo json_encode($success);
        }
    }

    //get bank_account detail by account_id
    public function edit_bank_account()
    {
        $data = $this->Bank_accounts_model->get_bank_account($this->input->post("account_id"));
        echo json_encode($data);
    }

    public function update_bank_account()
    {
        $bank_account_data = $_POST;
        $accountID = $_GET['account_id'];
        if(!empty($accountID)) {
            $updated = $this->Bank_accounts_model->update_bank_account($bank_account_data, $accountID);
            if($updated == true) {
                $success = array(
                    "title" => "New Bank Account added!",
                    "text" => "Bank Account details for ID ".$_POST['client_id']." added successfully."
                );
                echo json_encode($success);
            }
        } else {
            $fail = array(
                "title" => "Could not update!",
                "text" => "Bank Account details for ID ".$_POST['client_id']." could not be updated (No AccountID variable found)."
            );
            echo json_encode($fail);
        }
    }

    public function delete_bank_account()
    {
        $this->Bank_accounts_model->delete_bank_account($this->input->post("account_id"));
        $success = array(
            "title" => "Bank Account deleted!",
            "text" => "Selected Bank Account has been deleted successfully."
        );
        echo json_encode($success);
    }
    /* BANK ACCOUNT FUNCTIONS - END */

    /* DEMAT ACCOUNT FUNCTIONS - START */
    // get list of all demat account details of client
    public function ajax_demat_accounts()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $brokerID = $this->session->userdata('broker_id');
            $list = $this->Demat_accounts_model->get_client_demat_accounts($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            foreach($list as $demat_account)
            {
                $num++;
                $row = array();
                $row['demat_provider']=$demat_account->demat_provider;
                $row['type_of_account']=$demat_account->type_of_account;
                $row['demat_id']=$demat_account->demat_id;
                $row['account_number']=$demat_account->account_number;

                //add html for action
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_demat_account('."'".$demat_account->id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "recordsTotal"=>$this->Demat_accounts_model->demat_accounts_count_all($clientID),
                "recordsFiltered"=>$this->Demat_accounts_model->demat_accounts_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    // function to add new demat account details of client - called by ajax
    public function add_demat_account() {
        $demat_account_data = $_POST;
        $inserted = $this->Demat_accounts_model->add_demat_account($demat_account_data);
        if($inserted == true) {
            $success = array(
                "title" => "New Demat Account added!",
                "text" => "Demat Account details for ID ".$_POST['client_id']." added successfully."
            );
            echo json_encode($success);
        }
    }

    public function delete_demat_account()
    {
        $this->Demat_accounts_model->delete_demat_account($this->input->post("id"));
        $success = array(
            "title" => "Demat Account deleted!",
            "text" => "Selected Demat Account has been deleted successfully."
        );
        echo json_encode($success);
    }
    /* DEMAT ACCOUNT FUNCTIONS - END */

    /* POLICY FUNCTIONS - START */
    // get list of all policy details of client
    public function ajax_policies()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $brokerID = $this->session->userdata('broker_id');
            $list = $this->Insurance_model->get_client_policies($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            foreach($list as $policy)
            {
                $num++;
                $row = array();
                $row['ins_comp_name']=$policy->ins_comp_name;
                $row['plan_name']=$policy->plan_name;
                $row['policy_num']=$policy->policy_num;

                //add html for action
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_policy('."'".$policy->ins_policy_id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "recordsTotal"=>$this->Insurance_model->client_policies_count_all($clientID),
                "recordsFiltered"=>$this->Insurance_model->client_policies_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    // function to add new demat account details of client - called by ajax
    public function add_policy() {
        $policy_data = $_POST;
        $inserted = $this->Insurance_model->add_policy($policy_data);
        if($inserted == true) {
            $success = array(
                "title" => "New Policy added!",
                "text" => "Policy details for ID ".$_POST['client_id']." added successfully."
            );
            echo json_encode($success);
        }
    }

    public function delete_policy()
    {
        $this->Insurance_model->delete_policy($this->input->post("id"));
        $success = array(
            "title" => "Policy deleted!",
            "text" => "Selected Policy has been deleted successfully."
        );
        echo json_encode($success);
    }
    /* BANK ACCOUNT FUNCTIONS - END */

    /* TRADING FUNCTIONS - START */
    // get list of all trading details of client
    public function ajax_tradings()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $brokerID = $this->session->userdata('broker_id');
            $list = $this->Clients_model->get_client_tradings($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            foreach($list as $trading)
            {
                $num++;
                $row = array();
                $row['broker']=$trading->broker;
                $row['client_code']=$trading->client_code;
                $row['balance']=$trading->balance;

                //add html for action
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_trading('."'".$trading->id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "recordsTotal"=>$this->Clients_model->client_tradings_count_all($clientID),
                "recordsFiltered"=>$this->Clients_model->client_tradings_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    // function to add new trading details of client - called by ajax
    public function add_trading() {
        $policy_data = $_POST;
        $inserted = $this->Clients_model->add_trading($policy_data);
        if($inserted == true) {
            $success = array(
                "title" => "New Trading detail added!",
                "text" => "Trading details for ID ".$_POST['client_id']." added successfully."
            );
            echo json_encode($success);
        }
    }

    public function delete_trading()
    {
        $this->Clients_model->delete_trading($this->input->post("id"));
        $success = array(
            "title" => "Trading detail deleted!",
            "text" => "Selected Trading detail has been deleted successfully."
        );
        echo json_encode($success);
    }
    /* TRADING FUNCTIONS - END */


    // get list of all document details of client
    public function ajax_documents()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $list = $this->list_all_files('uploads/clients/'.$clientID);
            $data = array();
            if($list == 0)
            {

            }
            else
            {
                foreach($list as $doc) {
                    $data[] = $doc;
                }
            }
            $output = array(
                "draw"=>1,
                "recordsTotal"=>sizeof($list),
                //"recordsFiltered"=>$this->Clients_model->client_tradings_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    function list_all_files($path) {
        if(file_exists($path)) {
            $files = array();
            $i = 0;
            $fileinfos = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );
            foreach($fileinfos as $pathname => $fileinfo) {
                if (!$fileinfo->isFile()) continue;
                //$files[$i][] = $fileinfo->getFilename();
                $type = str_replace('/'.$fileinfo->getFilename(),'', str_replace($path.'/','', str_replace('\\','/',$pathname)));
                $files[$i]['type'] = $type;
                $files[$i]['path'] = '<a target="_blank" title="Click to view/download" href="'.base_url(str_replace('\\','/',$pathname)).'">'.str_replace('\\','/',$pathname).'</a>';

                //add delete button for action
                $files[$i]['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_document('."'".str_replace('\\','/',$pathname)."'".')">
                    <i class="fa fa-trash-o"></i></a>';

                if($type == $fileinfo->getFilename()) { unset($files[$i]); }   /* if the names are same, then its a photo, so delete the array */

                $i++;
            }
        } else {
            // client folder does not exist OR path not found
            return 0;
        }
        return $files;
    }

    public function delete_document() {
        unlink(glob($this->input->post('file'))[0]);
        $success = array(
            "title" => "Document deleted!",
            "text" => "Selected document file <b>".$this->input->post('file')."</b> has been deleted successfully."
        );
        echo json_encode($success);
    }


    /* CONTACT DETAILS FUNCTIONS - START */
    // get list of all contact details of client
    public function ajax_contacts()
    {
        if($this->input->post('clientID')) {
            $clientID = $this->input->post('clientID');
            $brokerID = $this->session->userdata('broker_id');
            $list = $this->Clients_model->get_client_contacts($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            foreach($list as $contact)
            {
                $num++;
                $row = array();
                $row['contact_category_name']=$contact->contact_category_name;
                $row['flat']=$contact->flat;
                $row['street']=$contact->street;
                $row['area']=$contact->area;
                $row['city']=$contact->city;
                $row['state']=$contact->state;
                $row['pin']=$contact->pin;
                $row['telephone']=$contact->telephone;
                $row['mobile']=$contact->mobile;
                $row['email_id']=$contact->email_id;

                //add html for action
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_contact('."'".$contact->client_contact_id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                //"recordsTotal"=>$this->Clients_model->client_contacts_count_all($clientID),
                //"recordsFiltered"=>$this->Clients_model->client_contacts_count_filtered($clientID),
                "data"=>$data
            );
            //output to json format
            echo json_encode($output);

        } else {
            echo 'Error! No clientID passed to ajax function';
            return false;
        }
    }

    // function to add new trading details of client - called by ajax
    public function add_contact() {
        $contact_data = $_POST;
        $inserted = $this->Clients_model->add_contact($contact_data);
        if($inserted == true) {
            $success = array(
                "title" => "New Contact detail added!",
                "text" => "Contact details for ID ".$_POST['client_id']." added successfully."
            );
            echo json_encode($success);
        }
    }

    public function delete_contact()
    {
        $this->Clients_model->delete_contact($this->input->post("id"));
        $success = array(
            "title" => "Contact detail deleted!",
            "text" => "Selected Contact detail has been deleted successfully."
        );
        echo json_encode($success);
    }
    /* TRADING FUNCTIONS - END */

    public function report()
    {
        if(!(isset($_GET['id'])) || (empty($_GET['id']))) {
            echo "<script type='text/javascript'>
                    alert('No client ID to show report!');
                    window.top.close();  //close the current tab
                  </script>";
        } else {
            /** Error reporting */
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/tcpdf/tcpdf.php');

            // create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle('Client Report');
            $pdf->SetSubject('Client Report');
            $pdf->SetKeywords('client, report');

            $title = '';
            // set default header data
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, '');

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-5);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('helvetica', '', 9);

            //get all client info
            $clientID = $_GET['id'];
            $info = $this->Clients_model->get_client_info($clientID);
            $bank_accounts = $this->Bank_accounts_model->get_client_bank_accounts($clientID);
            $demat_accounts = $this->Demat_accounts_model->get_client_demat_accounts($clientID);
            $policies = $this->Insurance_model->get_client_policies($clientID);
            $tradings = $this->Clients_model->get_client_tradings($clientID);
            $contacts = $this->Clients_model->get_client_contacts($clientID);
            $documents = $this->list_all_files('uploads/clients/'.$clientID);

            $html = ''; //set html to blank

            if($info)
            {
                if($info->dob) {
                    $dobTemp = DateTime::createFromFormat('Y-m-d',$info->dob); $dob = $dobTemp->format('d/m/Y');
                } else {
                    $dob = '';
                }

                $pdf->AddPage();
                // add client info to page
                $html .= '<style type="text/css">
                    .title { width:100%; line-height:28px; font-size:20px; font-weight:bold; text-align:center; border:2px double black; }
                    .info { font-size: 12px; font-weight: lighter; border:none; }
                    .head-row { background-color: #ccc;}
                </style>

                <div class="title">Client Report</div>

                <table class="info" cols="30">
                    <tr>
                        <td colspan="3">
                            <p>Name: </label>
                        </td>
                        <td colspan="7">
                            <p>'.$info->name.'</span>
                        </td>
                        <td colspan="4">
                            <p>Pan No.: </label>
                        </td>
                        <td colspan="6">
                            <p>'.$info->pan_no.'</span>
                        </td>
                        <td colspan="6">
                            <p>Relation w/ HOF: </label>
                        </td>
                        <td colspan="4">
                            <p>'.$info->relation_HOF.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p>DOB: </label>
                        </td>
                        <td colspan="7">
                            <p>'.$dob.'</span>
                        </td>
                        <td colspan="4">
                            <p>Passport No.: </label>
                        </td>
                        <td colspan="6">
                            <p>'.$info->passport_no.'</span>
                        </td>
                        <td colspan="4">
                            <p>Mobile No.: </label>
                        </td>
                        <td colspan="6">
                            <p>'.$info->mobile.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p>Address: </label>
                        </td>
                        <td colspan="11">
                            <p>'.$info->add_flat.' '.$info->add_street.' '.$info->add_area.' '.$info->add_city.' '.$info->add_state.' - '.$info->add_pin.'</span>
                        </td>
                    </tr>
                </table>
                <br/><br/>
                <div style="border-top:1px solid black;"></div>';

                if($bank_accounts)
                {
                    $html .= '<h3>Bank Account Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Bank Name</th>
                                <th>Branch</th>
                                <th>IFSC</th>
                                <th>Account Number</th>
                                <th>Account Type</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($bank_accounts as $account) {
                        $html .= '<tr>
                                <td>'.$account->bank_name.'</td>
                                <td>'.$account->branch.'</td>
                                <td>'.$account->IFSC.'</td>
                                <td>'.$account->account_number.'</td>
                                <td>'.$account->account_type.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($demat_accounts)
                {
                    $html .= '<br/><br/>
                    <h3>Demat Account Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>DP Name</th>
                                <th>DP ID</th>
                                <th>Account Number</th>
                                <th>Type of Account</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($demat_accounts as $account) {
                        $html .= '<tr>
                                <td>'.$account->demat_provider.'</td>
                                <td>'.$account->demat_id.'</td>
                                <td>'.$account->account_number.'</td>
                                <td>'.$account->type_of_account.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($policies)
                {
                    $html .= '<br/><br/>
                    <h3>Policy Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Company Name</th>
                                <th>Plan Name</th>
                                <th>Policy No.</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($policies as $policy) {
                        $html .= '<tr>
                                <td>'.$policy->ins_comp_name.'</td>
                                <td>'.$policy->plan_name.'</td>
                                <td>'.$policy->policy_num.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($tradings)
                {
                    $html .= '<br/><br/>
                    <h3>Trading Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Broker</th>
                                <th>Client Code</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($tradings as $trading) {
                        $html .= '<tr>
                                <td>'.$trading->broker.'</td>
                                <td>'.$trading->client_code.'</td>
                                <td>'.$trading->balance.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($contacts)
                {
                    $html .= '<br/><br/>
                    <h3>Additional Contact Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Category</th>
                                <th>House</th>
                                <th>Street</th>
                                <th>Area</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Pin</th>
                                <th>Mobile</th>
                                <th>Telephone</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($contacts as $contact) {
                        $html .= '<tr>
                                <td>'.$contact->contact_category_name.'</td>
                                <td>'.$contact->flat.'</td>
                                <td>'.$contact->street.'</td>
                                <td>'.$contact->area.'</td>
                                <td>'.$contact->city.'</td>
                                <td>'.$contact->state.'</td>
                                <td>'.$contact->pin.'</td>
                                <td>'.$contact->telephone.'</td>
                                <td>'.$contact->mobile.'</td>
                                <td>'.$contact->email_id.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($documents)
                {
                    $html .= '<br/><br/>
                    <h3>Document Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Document Type</th>
                                <th>File Path</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($documents as $document) {
                        $html .= '<tr>
                                <td>'.$document['type'].'</td>
                                <td>'.$document['path'].'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }
            }

            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();

            // Footer
            /* $html .= '<br/><br/><span style="text-align:center;">All Rights Reserved.
                        <a href=="http://freebzaar.com">freeBZaar.com</a></span>'; */

            //Close and output PDF document
            $pdf->Output('Flog Engagement Report - Admin.pdf', 'I');


            //============================================================+
            // END OF FILE
            //============================================================+
        }
    }
} 