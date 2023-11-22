<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Tradings_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        $this->load->model('Common_model');
        $this->load->model('Doc_model');
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
        $this->load->view('broker/common/notif');
        $this->load->view('broker/client/index');
        $this->load->view('broker/common/footer');

    }

    function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array();
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID); //Pallavi - 2017-06-21
        $data['families'] = $this->Families_model->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->Clients_model->get_clients_broker_dropdown($cli_condtition); //Pallavi - 2017-06-12
        $data['client_types'] = $this->Clients_model->get_client_types_dropdown("broker_id = '".$brokerID."' or broker_id is null");
        $data['occupations'] = $this->Clients_model->get_occupations_dropdown("broker_id = '".$brokerID."' or broker_id is null");
        return $data;
    }

    function add()
    {
    /* Add a new client */

    $brokerID = $this->session->userdata('user_id');
    $data = $this->Clients_model->get_limit($brokerID);
    $count = $this->Clients_model->count_client($brokerID);
    $data=intval($data->client_limit);
    $count = intval($count->count);

    //$data['families'] = $this->Families_model->get_families_broker_dropdown($brokerID);
    //$data['client_types'] = $this->Clients_model->get_client_types_dropdown();
    if($count >= $data){
      $error = array(
          "title" => "Client Limit Reached!",
          "text" => "You cannot add any more clients. Please contact support if you want to increase your limit."
      );
      $this->session->set_userdata('error', $error);
      redirect('broker/clients');
    }
    else{
    $data = $this->fill_form();
    $brokerID = $this->session->userdata('user_id');
    /*below lines commented by Salmaan - 2017-06-13
    $limit = $this->Clients_model->get_access_limit($brokerID);
    $count = $this->Clients_model->count_access_client($brokerID);
    if(intval($limit->client_access)>intval($count->count))
    {
      $data['access']="Yes";
    }*/
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
    $this->load->view('broker/master/add_family');
    $this->load->view('broker/master/occupation_add');
    $this->load->view('broker/master/client_type_add');
    $this->load->view('broker/common/notif');
    $this->load->view('broker/common/footer');
  }
    }


    function edit()
    {
        /* Edit existing client info */

        if(isset($_GET['id'])) {
            $clientID = $_GET['id'];
            $brokerID = $this->session->userdata('broker_id');

            $data = $this->fill_form();
            $data['action'] = 'edit';

            // get all client info from database
            $client_data = $this->Clients_model->get_client_info($clientID);

            $banks = $this->Banks_model->get_banks("broker_id = '".$brokerID."' or broker_id is null and status = 1"); // get list of all available banks
            $bank_account_types = $this->Banks_model->get_bank_account_types("broker_id = '".$brokerID."' or broker_id is null");
            $demat_providers = $this->Demat_providers_model->get_demat_providers("broker_id = '".$brokerID."' or broker_id is null");
            $trading_brokers = $this->Tradings_model->get_trading_brokers("broker_id = '".$brokerID."' or broker_id is null");
            $ins_companies = $this->Insurance_companies_model->get_ins_companies_broker_dropdown($brokerID);
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
            $data['bank_account_types'] = $bank_account_types;
            $data['demat_providers'] = $demat_providers;
            $data['trading_brokers'] = $trading_brokers;
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
            
            //Pradip - 2017-06-12
            /*$output=$this->files_size();
          //print_r($output);
            $limit=$output['limit'];
            $size=$output['total-size'];
             if($limit<=$size)// by default 3 gb ..or 3000000kb
              {
                      $data['limit']='Document upload size limit is exceed ';
              }*/
              
            $this->load->view('broker/common/header', $header);
            $this->load->view('broker/client/edit_form', $data);
            $this->load->view('broker/master/add_family');
            $this->load->view('broker/master/occupation_add');
            $this->load->view('broker/master/client_type_add');
            $this->load->view('broker/master/bank_add');
            $this->load->view('broker/master/demat_provider_add');
            $this->load->view('broker/master/client_bank_account_add', $data);
            $this->load->view('broker/master/client_trading_add', $data);
            $this->load->view('broker/master/trading_broker_add');
            $this->load->view('broker/master/document_type_add');
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            /* has come to Edit page without ID, so redirect to some other page */
        }
    }

    function check_duplicate_values() {
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->input->post();
        $duplicate = false; //set duplicate value to false, will set to true if there is any duplicacy
        $response = array();

        //check duplicate client name
        $duplicateConditionName = 'c.name = "'.trim($data['name']).'" AND c.family_id = "'.$data['family_id'].'" AND c.client_id <> "'.$data['client_id'].'" AND f.broker_id = "'.$brokerID.'"';
        $join = array(
            'table' => 'families f',
            'on' => 'c.family_id = f.family_id',
            'type' => 'inner'
        );
        $isNameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionName, $join);
        
        if($isNameDuplicate) {
            $duplicate = true;
            $res = array(
                "title" => "Client Name in this Family already exists!",
                "text" => "Please change the Client Name if you want to add a new client.",
                "type" => "error"
            );
            $response[] = $res;
        
        }
        
        //check duplicate email ID
        /*$duplicateConditionEmail = 'c.email_id = "'.trim($data['email_id']).'" AND c.client_id <> "'.$data['client_id'].'" AND f.broker_id = "'.$brokerID.'"';
        $join = array(
            'table' => 'families f',
            'on' => 'c.family_id = f.family_id',
            'type' => 'inner'
        );
        $isEmailDuplicate = $this->Clients_model->check_duplicate('clients c', $duplicateConditionEmail, $join);

        if($isEmailDuplicate) {
            $duplicate = true;
            $res = array(
                "title" => "Client Email ID already exists!",
                "text" => "Please change the Client Email ID as it should be unique to each client.",
                "type" => "error"
            );
            $response[] = $res;
        }*/
        //check duplicate username
        $duplicateConditionUsername = 'c.username = "'.trim($data['username']).'" AND c.client_id <> "'.$data['client_id'].'" AND f.broker_id = "'.$brokerID.'"';
        $join = array(
            'table' => 'families f',
            'on' => 'c.family_id = f.family_id',
            'type' => 'inner'
        );
        $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

        if($isUsernameDuplicate) {
            $duplicate = true;
            $res = array(
                "title" => "Client Username already exists!",
                "text" => "Please change the Client Username as it should be unique to each client.",
                "type" => "error"
            );
            $response[] = $res;
        }
        //check duplicate pan
        $duplicateConditionPan = 'c.pan_no = "'.trim($data['pan_no']).'" AND c.client_id <> "'.$data['client_id'].'" AND f.broker_id = "'.$brokerID.'"';
        $join = array(
            'table' => 'families f',
            'on' => 'c.family_id = f.family_id',
            'type' => 'inner'
        );
        $isPanDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionPan, $join);

        if($isPanDuplicate) {
            $duplicate = true;
            $res = array(
                "title" => "Client PAN already exists!",
                "text" => "Please change the Client PAN as it already exists for another client.",
                "type" => "error"
            );
            $response[] = $res;
        }

        if(!$duplicate) {
            echo json_encode("ok");
        } else {
            echo json_encode($response);
        }
    }

    function save()
    {
        //echo 'inside save';

        if(isset($_POST['action']) && !empty($_POST['action']))
        {
        // Process the POST data and make it ready for use (for either ADD or EDIT)
        $data = $_POST;
        
        // change date format of date values
        if(isset($data['dob_app'])) {
            if($data['dob_app']== 'on') {
                $dob_user = $data['dob'];
                if(!empty($dob_user)) {
                    $dob_temp = DateTime::createFromFormat('d/m/Y', $dob_user);
                    $data['dob'] = $dob_temp->format('Y-m-d');
                    $data['dob_app'] = 1;
                } else {
                    $data['dob'] = null;
                    $data['dob'] = null;
                }
            } else {
                $data['dob'] = null;
                $data['dob_app'] = null;
            }
        } else {
            $data['dob'] = null;
            $data['dob'] = null;
        }
        if(isset($data['anv_app'])) {
            if($data['anv_app'] == 'on') {
                $anv_user = $data['anv_date'];
                if(!empty($anv_user)) {
                    $anv_temp = DateTime::createFromFormat('d/m/Y', $anv_user);
                    $data['anv_date'] = $anv_temp->format('Y-m-d');
                    $data['anv_app'] = 1;
                } else {
                    $data['anv_date'] = null;
                    $data['anv_app'] = null;
                }
            } else {
                $data['anv_date'] = null;
                $data['anv_app'] = null;
            }
        } else {
            $data['anv_date'] = null;
            $data['anv_app'] = null;
        }
        if(isset($data['date_of_comm'])) {
            $comm_user = $data['date_of_comm'];
            $comm_temp = DateTime::createFromFormat('d/m/Y', $comm_user);
            $data['date_of_comm'] = $comm_temp->format('Y-m-d');
        }

        //Check Password
        if(isset($data['password']) && !empty($data['password'])) {
          $data['password']=sha1($data['password']);

        } else {
              unset($data['password']);
        }
        // also remove 'action' value from the array
        unset($data['action']);

        // add user_id/broker_id of the client
        $data['user_id'] = $this->session->userdata('user_id');

        $brokerID = $this->session->userdata('broker_id');

        //if HOF is 1, update all family members HOF
        if($data['head_of_family'] == 1) {
            $updateHof = $this->Clients_model->update_hof(array('family_id'=>$data['family_id']), array('head_of_family' => 0));
        } else {
            $updateHof = array();
        }
        if(!is_array($updateHof)) {
            $hof = 1;
        } else {
            $hof = 0;
        }


        // Now check if new client or existing client
        if($this->input->post('action') == 'add') {
            // insert data into clients table
            
            $inserted = $this->Clients_model->add_client_save($data);
            
            $photoUploaded = true; // photo assumed to be uploaded by default
            
            // call function to upload photo, if selected
            if(isset($_FILES["photo"]["name"]) && !empty($_FILES["photo"]["name"])) {
                //var_dump($_FILES['photo']);
                $photoUploaded = $this->uploadPhoto();
            }
            $signUploaded = true; // sign assumed to be uploaded by default
            
            // call function to upload photo, if selected
            if(isset($_FILES["sign"]["name"]) && !empty($_FILES["sign"]["name"])) {
                //var_dump($_FILES['photo']);
                $signUploaded = $this->uploadSign();
            }

            if($inserted && !is_array($inserted) && $photoUploaded && $signUploaded) {
                $success = array(
                    "title" => "New Client added!",
                    "text" => "Client details for ".$_POST['name']." added successfully.",
                    "hof" => $hof
                );
                $this->session->set_userdata('success', $success);
            } elseif(is_array($inserted)) {
            	$error = array(
            	    "title" => "Error in adding New Client!",
                    "text" => "Error message: ".$inserted['message']." , Error Code: ".$inserted['code']
                );
                $this->session->set_userdata('error', $error);
            }

            // now redirect to the client form page again (with all data)
            redirect('broker/clients/edit?id='.$_POST['client_id']);

        } elseif($this->input->post('action') == 'edit') {
            $_SESSION['files'] = $_FILES;
            // remove hidden elements/photo data
            unset($data['hidden_family']);
            unset($data['hidden_client_type']);
            unset($data['hidden_occupation']);
            unset($data['hidden_state']);
            unset($data['photo']);
            unset($data['sign']);

            // update data in the clients table
            $updated = $this->Clients_model->update_client($data);

            // get existing photo, if exists
            if((glob("uploads/clients/".$data['client_id']."/*.*"))) {
                $photoFilename = basename(glob("uploads/clients/".$data['client_id']."/*.*")[0]);
            } else {
                $photoFilename = '';
            }
            // get existing sign, if exists
            if((glob("uploads/clients/".$data['client_id']."/Signature/*.*"))) {
                $signFilename = basename(glob("uploads/clients/".$data['client_id']."/Signature/*.*")[0]);
            } else {
                $signFilename = '';
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
            $signUploaded = true; // sign assumed to be uploaded by default
            if (isset($_POST['sign']) && $_POST['sign'] == '') {
                // Delete file
                unlink(glob("uploads/clients/".$data['client_id']."/Signature/*.*")[0]);
            } elseif ($_FILES['sign']['error'] == 0)  {
                // Save uploaded file
                unlink(glob("uploads/clients/".$data['client_id']."/Signature/*.*")[0]);
                $signUploaded = $this->uploadSign();
            } else {
                // sign is the same, so don't do anything
            }


            if($updated && $photoUploaded && $signUploaded) {
                $success = array(
                    "title" => "Client updated!",
                    "text" => "Client details for ".$_POST['name']." updated successfully.",
                    "hof" => $hof
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
            //redirect('broker/clients/edit?id='.$_POST['client_id']);
        }
    }

    public function delete_client()
    {
        $deleted = $this->Clients_model->delete_client($this->input->post("id"));
        if($deleted) {
            $success = array(
                "title" => "Client deleted!",
                "text" => "Selected Client has been deleted successfully.",
                "type" => "success"
            );
            echo json_encode($success);
        } else {
            $error = array(
                "title" => "Client could not be deleted!",
                "text" => "Could not delete Client. Please check if there are any records linked to this client.",
                "type" => "error"
            );
            echo json_encode($error);
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
            $row['dob']=$client->dob;
            $row['pan_no']=$client->pan_no;
            $row['email_id']=$client->email_id;
            $row['username']=$client->username;
            $row['date_of_comm']=$client->date_of_comm;
            $row['client_category']=$client->client_category;
            $row['passport_no']=$client->passport_no;
            $row['mobile']=$client->mobile;
			$row['app_access']=$client->app_access;			
            $permissions=$this->session->userdata('permissions');
            //add html for action
            if($permissions == "3")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_client('."'".$client->client_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_client('."'".$client->client_id."'".')">
                <i class="fa fa-trash-o"></i></a>
                <a class="btn btn-sm btn-print" href="javascript:void(0)" title="Print"
                onclick="print_client('."'".$client->client_id."'".')">
                <i class="fa fa-file-text-o"></i></a>';
              }
                else if($permissions == "2")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_client('."'".$client->client_id."'".')">
                      <i class="fa fa-pencil"></i></a>
                      <a class="btn btn-sm btn-danger disable_btn">
                      <i class="fa fa-trash-o"></i></a>
                      <a class="btn btn-sm btn-print" href="javascript:void(0)" title="Print"
                      onclick="print_client('."'".$client->client_id."'".')">
                      <i class="fa fa-file-text-o"></i></a>';
                }
                else if($permissions == "1")
                {
                  $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                      onclick="edit_client('."'".$client->client_id."'".')">
                      <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
                <i class="fa fa-trash-o"></i></a>
                <a class="btn btn-sm btn-print" href="javascript:void(0)" title="Print"
                onclick="print_client('."'".$client->client_id."'".')">
                <i class="fa fa-file-text-o"></i></a>';
                }

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
        $brokerID = $this->session->userdata('broker_id');
        $families = $this->Families_model->get_families_broker_dropdown($brokerID);
        echo json_encode($families);
    }

    public function client_types_dropdown() {
        $brokerID = $this->session->userdata('broker_id');
        $client_types = $this->Clients_model->get_client_types_dropdown("broker_id = ".$brokerID." or broker_id is null");
        echo json_encode($client_types);
    }

    public function occupations_dropdown() {
        $brokerID = $this->session->userdata('broker_id');
        $occupations = $this->Clients_model->get_occupations_dropdown("broker_id = ".$brokerID." or broker_id is null");
        echo json_encode($occupations);
    }

    public function document_types_dropdown() {
        $brokerID = $this->session->userdata('broker_id');
        $doc_types = $this->Clients_model->get_document_types_dropdown("broker_id = ".$brokerID." or broker_id is null");
        echo json_encode($doc_types);
    }

    public function get_client_family() {
        $clientID = $this->input->post('clientID');
        if($clientID != '') {
            $familyID = $this->Clients_model->get_client_family($clientID);
            echo json_encode($familyID);
        } else {
            echo json_encode('error');
        }
    }

    public function get_client_bank_accounts_dropdown() {
        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        if($clientID != '') {
            $cli_condition = array('(b.broker_id = "'.$brokerID.'" OR b.broker_id IS NULL)', 'ba.client_id' => $clientID);
        } else {
            $cli_condition = '(b.broker_id = "'.$brokerID.'" OR b.broker_id IS NULL)';
        }
        $account_data = $this->Bank_accounts_model->get_client_bank_accounts($cli_condition);
        echo json_encode($account_data);
    }

    public function get_clients_broker_dropdown() {
        $familyID = $this->input->post('familyID');
        $brokerID = $this->session->userdata('broker_id');
        if($familyID != '') {
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $familyID);
        } else {
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        }
        $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
        echo json_encode($clientID);
    }
    
    public function get_clients_broker_dropdown_wise() 
    {
        
        $brokerID = $this->session->userdata('broker_id');
        
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        
        $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
        echo json_encode($clientID);
    }

    public function get_client_trading_brokers() {
        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        if($clientID != '') {
            $cli_condition = array('cb.user_id' => $brokerID, 'cb.client_id' => $clientID);
        } else {
            $cli_condition = array('cb.user_id' => $brokerID);
        }
        $clientID = $this->Clients_model->get_client_trading_brokers($cli_condition);
        echo json_encode($clientID);
    }

    public function get_trading_broker_client_code() {
        $trading_brokerID = $this->input->post('trading_brokerID');
        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        if($trading_brokerID != '') {
            $cli_condition = array('user_id' => $brokerID, 'broker' => $trading_brokerID, 'client_id' => $clientID);
        } else {
            $cli_condition = array('user_id' => $brokerID);
        }
        $clientCode = $this->Clients_model->get_trading_broker_client_code($cli_condition);
        echo json_encode($clientCode);
    }

    public function get_client_code_balance() {
        $clientCode = $this->input->post('clientCode');
        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        if($clientCode != '') {
            $cli_condition = array('user_id' => $brokerID, 'client_code' => $clientCode, 'client_id' => $clientID);
        } else {
            $cli_condition = array('user_id' => $brokerID);
        }
        $balance = $this->Clients_model->get_client_code_balance($cli_condition);
        echo json_encode($balance);
    }

    public function get_client_codes_client()
    {
        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        $where = array('client_id'=>$clientID);
        $client_codes = $this->Tradings_model->get_client_codes_dropdown($where);
        echo json_encode($client_codes);
    }

    public function new_client_id() {
        $clientID = $this->Clients_model->get_new_client_id();
        echo json_encode($clientID);
    }

    public function uploadPhoto() {
        //var_dump($_FILES);
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

    public function uploadSign() {
        //var_dump($_FILES);
        if ($_FILES["sign"]["error"] > 0)
        {
            // if there is error in file uploading
            $error = array(
                "title" => "Error uploading signature!",
                "text" => "Failed to upload! Return Code: " . $_FILES["sign"]["error"]
            );
            $this->session->set_userdata('error', $error);
            return false;
        }
        else
        {
            $clientID = $_POST['client_id'];
            $path = "uploads/clients/".$clientID."/Signature";

            // check if file already exit in "uploads/clients/clientID" folder.
            if (file_exists($path."/".$_FILES["sign"]["name"]))
            {
                $error = array(
                    "title" => "Already exists!",
                    "text" => "Filename '" . $_FILES["sign"]["name"] . "' already exists."
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
                if(move_uploaded_file($_FILES["sign"]["tmp_name"], $path."/".$_FILES["sign"]["name"]))
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

                    /*if($docType == "Signature" && is_dir($path)) {
                        unlink(glob("uploads/clients/".$clientID."/Signature/*.*")[0]);
                    }*/

                    //check if file size is greater than 5MB
                    $docSize = $_FILES["docFile"]["size"];
                    if($docSize > 8242880) {
                        $error = array(
                            "title" => "File size exceeds 5MB!",
                            "text" => "The file you are trying to upload is greater than the allowed limit of 5MB. Please select a file with lower size.",
                            "type" => "error"
                        );
                        echo json_encode($error);
                    } else {

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
            }
        } else {
            // file array was not set, so there's some problem. Most probably no file was uploaded.
            //echo "No file selected to upload!";
            $error = array(
                "title" => "No file selected or file size is greater than 5MB!",
                "text" => "You may have not selected any file to upload, or else your file might be greater than 5MB. Please select a file within the maximum limit.",
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
            $list = $this->Bank_accounts_model->get_client_bank_accounts('ba.client_id = "'.$clientID.'"');

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
                $row['account_type_name']=$bank_account->account_type_name;
                $row['account_number']=$bank_account->account_number;

                //add html for action
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_bank_account('."'".$bank_account->account_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else {
                $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                <i class="fa fa-trash-o"></i></a>';
              }

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
        $duplicateCondition = 'ba.bank_id = "'.$bank_account_data['bank_id'].'"'
                            .' AND ba.account_number = "'.trim($bank_account_data['account_number']).'"'
                            .' AND ba.client_id = "'.$bank_account_data['client_id'].'"'
                            .'AND (u.broker_id = "'.$this->session->userdata('broker_id').'" OR u.id = "'.$this->session->userdata('broker_id').'")';
        $isDuplicate = $this->Bank_accounts_model->check_duplicate('bank_accounts ba', $duplicateCondition, 'users u', 'ba.user_id = u.id');
        //echo json_encode(var_dump($isDuplicate));
        if(!$isDuplicate) {
            $inserted = $this->Bank_accounts_model->add_bank_account($bank_account_data);
            if($inserted === true) {
                $success = array(
                    "title" => "New Bank Account added!",
                    "text" => "Bank Account details added successfully.",
                    "type" => "success"
                );
                echo json_encode($success);
            }
        } else {
            $fail = array(
                "title" => "Could not add Bank Account!",
                "text" => "Same Bank Account already exists. Please check the Bank Name and Account Number once again.",
                "type" => "error"
            );
            echo json_encode($fail);
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
            if($updated === true) {
                $success = array(
                    "title" => "New Bank Account added!",
                    "text" => "Bank Account details added successfully."
                );
                echo json_encode($success);
            }
        } else {
            $fail = array(
                "title" => "Could not update!",
                "text" => "Bank Account details could not be updated (No AccountID variable found)."
            );
            echo json_encode($fail);
        }
    }

    public function delete_bank_account()
    {
        $deleted = $this->Bank_accounts_model->delete_bank_account($this->input->post("account_id"));
        if($deleted) {
            $success = array(
                "type" => "info",
                "title" => "Bank Account deleted!",
                "text" => "Selected Bank Account has been deleted successfully."
            );
            echo json_encode($success);
        } else {
            $fail = array(
                "type" => "error",
                "title" => "Could not delete Bank Account!",
                "text" => "Selected record is in use. Please delete the referenced data first."
            );
            echo json_encode($fail);
        }
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
                $permissions=$this->session->userdata('permissions');

                if($permissions == "3")
                {
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_demat_account('."'".$demat_account->id."'".')">
                <i class="fa fa-trash-o"></i></a>';
                }
                else {
                  $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                  <i class="fa fa-trash-o"></i></a>';
                }
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
        $brokerID = $this->session->userdata('broker_id');
        $demat_account_data = $_POST;

        //Check dupicate
        $condition = "`account_number` = '".trim($demat_account_data['account_number'])."' AND (`clients`.`user_id` = '".$brokerID."' OR `clients`.`user_id` IS NULL)";
        $isDuplicate = $this->Demat_accounts_model->check_duplicate('demat_accounts',$condition);

        if($isDuplicate) {
             $error = array(
                "type" => "error",
                "title" => "Demat Account number already exists!.",
                "text" => "Demat account number you are trying to add already exists. Please check."
            );
            echo json_encode($error);
        }
        else
        {
            $inserted = $this->Demat_accounts_model->add_demat_account($demat_account_data);
            if($inserted === true) {
            $success = array(
                "title" => "New Demat Account added!",
                "text" => "Demat Account details added successfully."
            );
            echo json_encode($success);
        }
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
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_policy('."'".$policy->ins_policy_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else {
                $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                <i class="fa fa-trash-o"></i></a>';
              }
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
        //$duplicateCondition = array("policy_num" => $policy_data['policy_num'], "ins_comp_id" => $policy_data['ins_comp_id']);
        $duplicateCondition = array("policy_num" => trim($policy_data['policy_num']));
        $isDuplicate = $this->Insurance_model->check_duplicate("insurance_policies", $duplicateCondition);
        if($isDuplicate) {
            $message = array(
                'title' => 'Policy number already exists!',
                'text' => 'The Policy Number you entered is already present in our database. Please check whether the Policy Number is correct, as it should be unique.',
                'type' => 'error'
            );
            echo json_encode($message);
        } else {
            try {
                $inserted = $this->Insurance_model->add_policy($policy_data);
                if($inserted && !is_array($inserted)) {
                    $success = array(
                        "title" => "New Policy added!",
                        "text" => "Policy details added successfully.",
                        "type" => "success"
                    );
                    echo json_encode($success);
                } else {
                    throw new Custom_exception();
                }
            }
            catch(Custom_exception $e) {
                $message = array(
                    'title' => 'Error while adding Policy Details!',
                    'text' => $inserted['code'],
                    'type' => 'error'
                );
                echo json_encode($message);
            }
        }
    }

    public function delete_policy()
    {
        $brokerID = $this->session->userdata('broker_id');
        $check_condition = array('ip.ins_policy_id' => trim($this->input->post("id")));
        $join = array(
            'table' => 'insurances i',
            'on' => 'i.policy_num = ip.policy_num',
            'type' => 'inner'
        );
        $ins_exists = $this->Insurance_model->check_duplicate('insurance_policies ip',$check_condition,$join);
        if($ins_exists) {
            /*$error = array(
                "type" => "error",
                "title" => "Cannot delete Policy!",
                "text" => "Selected Policy cannot be deleted as it has Insurance linked to it. Please delete its Insurance record first."
            );
            echo json_encode($error);*/
            $where = array(
                'policy_number' => $ins_exists[0]->policy_num,
                'broker_id' => $brokerID
            );
            $status = $this->Insurance_model->delete_premium($where);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

            $where2 = array(
                'policy_num' => $ins_exists[0]->policy_num,
                'broker_id' => $brokerID
            );
            $status = $this->Insurance_model->delete_insurance($where2);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        } else {
            //do nothing for now as insurance doesn't exist for policy_num
        }
        $condition = array('ins_policy_id' => $this->input->post("id"));
        $this->Insurance_model->delete_policy($condition);
        $success = array(
            "type" => "info",
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
            $list = $this->Clients_model->get_client_tradings($clientID);

            $data = array();
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            //var_dump($list);
            foreach($list as $trading)
            {
                $num++;
                $row = array();
                $row['broker']=$trading->trading_broker_name;
                $row['client_code']=$trading->client_code;
                $row['balance']=$trading->balance;
                $row['held_type']=$trading->held_type;
                //add html for action
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_trading('."'".$trading->id."'".')">
                <i class="fa fa-trash-o"></i></a>';
                }
                else {
                  $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                  <i class="fa fa-trash-o"></i></a>';
                }

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
      //check duplicate

        $brokerID = $this->session->userdata('broker_id');
        //Check dupicate
        $condition = "`client_code` = '".trim($policy_data['client_code'])."' AND (`clients`.`user_id` = '".$brokerID."' OR `clients`.`user_id` IS NULL)";
        $isDuplicate = $this->Clients_model->check_duplicate_trading('client_brokers',$condition);

        if($isDuplicate) {
             $error = array(
                "type" => "error",
                "title" => "Broker Client Code already exists!.",
                "text" => "Broker Client Code you are trying to add already exists. Please check."
            );
            echo json_encode($error);
        }
        else
        {
            $inserted = $this->Clients_model->add_trading($policy_data);
            if($inserted == true) {
            $success = array(
                "title" => "New Trading detail added!",
                "text" => "Trading details added successfully.",
                "type" => "success",
                "data" => $inserted
            );
            echo json_encode($success);
            } else {
            $error = array(
                "title" => "Could not add new Trading detail!",
                "text" => "Unable to add Trading details. Please select a Client if not selected.",
                "type" => "error"
            );
            echo json_encode($error);
            }
        }
    }

    public function delete_trading()
    {
        $brokerID = $this->session->userdata('broker_id');
        //get client_code
        $client_code = false;
        $code_condition = "id = '".$this->input->post("id")."'";
        $row = $this->Clients_model->get_trading_broker_client_code($code_condition);
        if($row) {
            $client_code = $row[0]->client_code;
        }
        //Check existing record for selected trading broker client_code
        $condition = "`client_code` = '".$client_code."' AND `broker_id` = '".$brokerID."'";
        $isDuplicate = $this->Clients_model->check_duplicate('equities',$condition);
        if($isDuplicate) {
            $error = array(
                "type" => "error",
                "title" => "Could not delete Trading detail!",
                "text" => "Selected record is in use. Please delete the referenced data first."
            );
            echo json_encode($error);
        } else {
            $this->Clients_model->delete_trading($this->input->post("id"));
            $success = array(
                "type" => "info",
                "title" => "Trading detail deleted!",
                "text" => "Selected Trading detail has been deleted successfully."
            );
            echo json_encode($success);
        }
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
                unset($data['path']);
                //$data['path'] = $data['filename'];
                //unset($data['filename']);
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
                //$files[$i]['filename'] = $fileinfo->getFilename();
                $files[$i]['filename'] = '<a target="_blank" title="Click to view/download" href="'.base_url(str_replace('\\','/',$pathname)).'">'.$fileinfo->getFilename().'</a>';

                //add delete button for action
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $files[$i]['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_document('."'".str_replace('\\','/',$pathname)."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else {
                  $files[$i]['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                      <i class="fa fa-trash-o"></i></a>';
                }
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
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_contact('."'".$contact->client_contact_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else {
                $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                <i class="fa fa-trash-o"></i></a>';
              }
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

    // function to add new contact details of client - called by ajax
    public function add_contact() {
        $contact_data = $_POST;
        $inserted = $this->Clients_model->add_contact($contact_data);
        if($inserted === true) {
            $success = array(
                "title" => "New Contact detail added!",
                "text" => "Contact details added successfully."
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
    /* CONTACT FUNCTIONS - END */
    public function report()
    {
        if((!(isset($_GET['id'])) || (empty($_GET['id']))) && (!(isset($_POST['id'])) || (empty($_POST['id'])))) {
            echo "<script type='text/javascript'>
                    bootbox.alert('No client ID to show report!');
                    window.top.close();  //close the current tab
                  </script>";
        } else {
            /** Error reporting */
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            
            $clientID =isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
            //get broker logo
            $brokerID = $this->session->userdata('broker_id');
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }

            $res=$this->Clients_model->get_clients_broker_dropdown("fam.broker_id='".$brokerID."' and c.client_id='".$clientID."'");
            //var_dump($res);
            if(!is_array($res) || empty($res))
            {
                echo "<script type='text/javascript'>
                    bootbox.alert('No client ID to show report!');
                    window.top.close();  //close the current tab
                  </script>";
                  die();
            }


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
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, '');
            $pdf->SetHeaderData($logo, 40, $title, '');

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
            $pdf->SetFont('sourcesanspro', '', 12);

            //get all client info
            
            $info = $this->Clients_model->get_client_info($clientID);
            $bank_accounts = $this->Bank_accounts_model->get_client_bank_accounts('ba.client_id = "'.$clientID.'"');
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
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 10px; padding:2px; color:#4d4d4d; text-align:center; color:#4d4d4d; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 10px; padding:2px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse; }
                        .amount { text-align:left; padding:10px; text-indent: 5px; font-weight: bold; }
                        .noWrap { white-space: nowrap; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:14px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .normal2 {font-weight: normal; text-align:left;}
                        .bold2 {font-weight: bold; font-size:10px; text-align:left;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                    </style>

                <div class="title">Client Report</div>

                <table class="info" cols="30">
                    <tr>
                        <td colspan="3">
                            <p class="client-name">Name: </label>
                        </td>
                        <td colspan="7">
                            <p class="client-name">'.$info->name.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Pan No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->pan_no.'</span>
                        </td>
                        <td colspan="6">
                            <p class="client-name">Relation with HOF: </label>
                        </td>
                        <td colspan="4">
                            <p class="client-name">'.$info->relation_HOF.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p class="client-name">DOB: </label>
                        </td>
                        <td colspan="7">
                            <p class="client-name">'.$dob.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Aadhar No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->passport_no.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Mobile No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->mobile.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p class="client-name">Address: </label>
                        </td>
                        <td colspan="11">
                            <p class="client-name">'.$info->add_flat.' '.$info->add_street.' '.$info->add_area.' '.$info->add_city.' '.$info->add_state.' - '.$info->add_pin.'</span>
                        </td>
                    </tr>
                </table>
                <br/><br/>
                <div style="border-top:1px solid black;"></div>';

                if($bank_accounts)
                {
                    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="5">Bank Account Details</td>
                            </tr>
                            <tr nobr="true" class="head-row">
                                <th>Bank Name</th>
                                <th>Branch</th>
                                <th>IFSC</th>
                                <th>Account Number</th>
                                <th>Account Type</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($bank_accounts as $account) {
                        $html .= '<tr>
                                <td class="border normal">'.$account->bank_name.'</td>
                                <td class="border normal">'.$account->branch.'</td>
                                <td class="border normal">'.$account->IFSC.'</td>
                                <td class="border normal">'.$account->account_number.'</td>
                                <td class="border normal">'.$account->account_type_name.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($demat_accounts)
                {
                    $html .= '<br/><br/>
                    <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="4">Demat Account Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>DP Name</th>
                                <th>Type of Account</th>
                                <th>DP ID</th>
                                <th>Account Number</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($demat_accounts as $account) {
                        $html .= '<tr>
                                <td class="border normal">'.$account->demat_provider.'</td>
                                <td class="border normal">'.$account->type_of_account.'</td>
                                <td class="border normal">'.$account->demat_id.'</td>
                                <td class="border normal">'.$account->account_number.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($policies)
                {
                    /*$html .= '<br/><br/>
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
                    </table>';*/
                }

                if($tradings)
                {
                    $html .= '<br/><br/>
                    <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="3">Trading Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>Broker</th>
                                <th>Client Code</th>
                                <th>Balance</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($tradings as $trading) {
                        $html .= '<tr>
                                <td class="border normal">'.$trading->trading_broker_name.'</td>
                                <td class="border normal">'.$trading->client_code.'</td>
                                <td class="border normal">'.$trading->balance.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($documents)
                {
                    $html .= '<br/><br/>
                    <table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="1">Document Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>Document</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($documents as $document) {
                        $html .= '<tr>
                                <td class="border normal">'.$document['type'].' for '.$document['filename'].'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }


                if($contacts)
                {
                    $html .= '<table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td>';

                    /*foreach($contacts as $contact) {
                        $html .= '<tr>
                                <td class="border normal">'.$contact->contact_category_name.'</td>
                                <td class="border normal">'.$contact->flat.'</td>
                                <td class="border normal">'.$contact->street.'</td>
                                <td class="border normal">'.$contact->area.'</td>
                                <td class="border normal">'.$contact->city.'</td>
                                <td class="border normal">'.$contact->state.'</td>
                                <td class="border normal">'.$contact->pin.'</td>
                                <td class="border normal">'.$contact->mobile.'</td>
                                <td class="border normal">'.$contact->telephone.'</td>
                                <td class="border normal">'.$contact->email_id.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';*/

                    $html .= '<br/><br/><br/>
                    <table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="8">Additional Contact Details</td>
                            </tr>
                        </tbody>
                    </table>';

                    foreach($contacts as $contact) {
                        $html .= '<table class="info" cols="30">
                            <tr>
                                <td colspan="3">
                                    <p class="bold2">Category: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->contact_category_name.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Mobile No.: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->mobile.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Telephone: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->telephone.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Email ID: </p>
                                </td>
                                <td colspan="6">
                                    <p class="normal2">'.$contact->email_id.'</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p class="bold2">Address: </p>
                                </td>
                                <td colspan="25">
                                    <p class="normal2">'.
                            $contact->flat.' '.$contact->street.' '.$contact->area.' '.
                            $contact->city.' '.$contact->state.' - '.$contact->pin.'</p>
                                </td>
                            </tr>
                        </table>
                        <br/>';
                    }
                    $html .= '</td>
                            </tr>
                        </tbody>
                    </table>';
                }
            }

            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();
            ob_end_clean();
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
    ///functions for Clients Import
    function import($err_data = null)
    {
    	ini_set('max_execution_time', 0);
    	ini_set('memory_limit', '2048M');
    	ini_set('upload_max_filesize', '15M');
    	ini_set('post_max_size', '20M');
    	
        $header['title'] = 'Client Details Import';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js',
            'assets/users/js/common.js'
        );
        $this->load->view('broker/common/header', $header);
        $data['import_data'] = $err_data;
        $this->load->view('broker/client/import', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    
    function clients_import()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '150M');
        ini_set('post_max_size', '150M');//50M
        $uploadedStatus = 0;
        $message = ""; $impMessage = ""; $insertRow = true;
        $imp_data = array();
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_clients"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_clients"]["name"] == '')
                {
                    $message = "No file selected";
                }elseif($_POST['rta_list']==' '){
                    $message.="  Also please select Client Data type from dropdown";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_clients"]["tmp_name"];
                    $rta_list=$_POST['rta_list'];
                
                    //below if condition is for dbf file which is not recognized by windows machine : @pallavi
                    if($_FILES["import_clients"]["type"]=="application/octet-stream"){
                      $_FILES["import_clients"]["type"]= "application/x-dbf";
                    }
                    //check first the extension whether the file is  excel or dbf
                    if($_FILES["import_clients"]["type"]=="application/excel"||$_FILES["import_clients"]["type"]=="application/vnd.ms-excel"||$_FILES["import_clients"]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                    {      

                        //load the excel library
                        $this->load->library('Excel');
                        //read file from path
                        $objPHPExcel = PHPExcel_IOFactory::load($file);
                        //get only the Cell Collection
                        $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                        //temp variables to hold values  client info
                        $famName = ""; $clientName = ""; $panNum = ""; $clientType = ""; $comDate = "";
                        $occ = ""; $hof = ""; $rHof = ""; $report = ""; $dob = ""; $ann =null;
                        $spouse = ""; $child = ""; $clientId = ""; $familyId = ""; $category = "";
                        $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
                        $mobile = ""; $tel = ""; $email = ""; $username = "";
                        $password = ""; $pan = ""; $passport = "";
                        //@pallavi :variables holds the Clients bank details
                        $bankName="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";$bank_address_road="";$bank_address_area="";
                        $bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$folio_number="";
                        //additional details in client bank details table
                        $productId="";$jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";
                        $broker_code="";$sub_boroker="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";
                        $guardian_name="";$folioDate="";


                        $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
                        $clientAlready=0;$panAlready=0;$notFolio=0;$fresh=0; //for checking client avalability and if flag set to 1 then bypass client insert repeat and based on client Id insert its folio and other details.

                      
                        if($this->session->userdata('broker_id'))
                        {
                            $brokerID = $this->session->userdata('broker_id'); 
                        }
                        else
                        {
                            $brokerID = $this->session->userdata('user_id');  
                        }
                        $user_id = $this->session->userdata('user_id');
                        //for maintaing leading zeros in folio_number  specially for franklin files  //@pallavi:2017-06-14
                        if($rta_list=='frank_excel'){
                             $objPHPExcel->getActiveSheet()->getStyle('B2:B'.$maxCell['row'])->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        }
                        //get data from excel using range
                        $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                        //stores column names
                        $dataColumns = array();
                        //stores row data
                        $dataRows = array();
                        $dataRowsBank=array();//@pallavi
                        $countRow = 0; $countErrRow = 0; $countClient = 0; $countRem = 0; $countTrans = 0;
                        //check max row for client import limit
                        $data = $this->Clients_model->get_limit($brokerID);
                        $count = $this->Clients_model->count_client($brokerID);
                        $data=intval($data->client_limit);                        
                        $count = intval($count->count);
                        $remaining = $data - $count;
                        
                      
                        if(($maxCell['row']-1)>$remaining)
                        {
                            $message = "Client data exceeds your client limit! Please contact support if you want to increase your limit.";
                        }
                        else{
                    
                            foreach($excelData as $rows)
                            { 
                                foreach($rows as $key=>$val)
                                {
                                    $temp[$val]=$key;
                                } break;
                            }
                
                            foreach($excelData as $rows)
                            {
                                $countCell = 0;                          
                                foreach($rows as $key=>$val)
                                {                            
                                    if(rtrim(ltrim($rows[0])) || rtrim(ltrim($rows[1])) || rtrim(ltrim($rows[2])))
                                    {
                                        $cell=$rows[$key];
                                      if($countRow == 0)
                                      {
                                        $cell = str_replace(array('.'), '', $cell);
                                        if($rta_list=='cl_excel'){
                                            if( strtoupper($cell) == 'FAMILY NAME' || 
                                              strtoupper($cell) == 'CLIENT NAME' || 
                                              strtoupper($cell) == 'PAN NO' ||
                                              strtoupper($cell) == 'CLIENT TYPE' ||
                                              strtoupper($cell) == 'OCCUPATION' || 
                                              strtoupper($cell) == 'HEAD OF FAMILY' || 
                                              strtoupper($cell) == 'RELATION W/ HOF'|| 
                                              strtoupper($cell) == 'RELATION W/HOF' || 
                                              strtoupper($cell) == 'REPORT ORDER' ||
                                              strtoupper($cell) == 'DATE OF BIRTH' || 
                                              strtoupper($cell) == 'ANNIVERSARY' || strtoupper($cell) == 'SPOUSE NAME' ||
                                              strtoupper($cell) == 'CHILDREN NAME' || strtoupper($cell) == 'HOUSE/FLAT NO' || strtoupper($cell) == 'STREET' ||
                                              strtoupper($cell) == 'AREA' || strtoupper($cell) == 'CITY' || strtoupper($cell) == 'STATE' ||
                                              strtoupper($cell) == 'PINCODE' || strtoupper($cell) == 'MOBILE' || strtoupper($cell) == 'TELEPHONE' ||
                                              strtoupper($cell) == 'EMAIL ID' || strtoupper($cell) == 'USERNAME' ||
                                              strtoupper($cell) == 'PASSPORT NO' ||strtoupper($cell) == 'COMMENCEMENT DATE' || strtoupper($cell) == 'CLIENT CATEGORY')
                                          {
                                              
                                              $dataColumns[$countCell] = $cell;
                                              $countCell++;
                                              $uploadedStatus = 1;
                                              continue;
                                          }
                                          else
                                          {
                                              echo  $cell;exit;
                                              $message = 'Columns Specified in Excel is not in correct format';
                                              $uploadedStatus = 0;
                                              break;
                                          }
                                        }elseif($rta_list=='nj_excel'){ // NJ Excel sheet   pallavi--2017-06-15
                                            if(strtoupper($cell) == 'AMC'){
                                                  $vals=$temp[$cell];
                                                  $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;

                                            }else if(strtoupper($cell) == 'GROUP'){
                                                $vals=$temp[$cell];
                                                $cell='Family Name';
                                                $dataColumns[$vals] = $cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                         }elseif(strtoupper($cell) == 'INVESTOR'){
                                           $vals=$temp[$cell];
                                               $cell='Client Name';
                                               $dataColumns[$vals] = $cell;
                                               $countCell++;
                                               $uploadedStatus = 1;
                                               continue;
                                             }elseif(strtoupper($cell) == 'FIRST HOLDER PAN'){
                                               $vals=$temp[$cell];
                                                   $cell='Pan No';
                                                   $dataColumns[$vals] = $cell;
                                                   $countCell++;
                                                   $uploadedStatus = 1;
                                                   continue;
                                             }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                   $vals=$temp[$cell];
                                                       $cell='Occupation';
                                                       $dataColumns[$vals] = $cell;
                                                       $countCell++;
                                                       $uploadedStatus = 1;
                                                       continue;
                                             }elseif(strtoupper($cell) == 'FIRST HOLDER DOB'){
                                               $vals=$temp[$cell];
                                                   $cell='Date Of Birth';
                                                   $dataColumns[$vals] = $cell;
                                                   $countCell++;
                                                   $uploadedStatus = 1;
                                                   continue;
                                          }elseif(strtoupper($cell) == 'ADDRESS'){
                                               $vals=$temp[$cell];
                                                $cell='House/Flat No';
                                                $dataColumns[$vals] = $cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                           }elseif(strtoupper($cell) == 'CITY'){
                                             $vals=$temp[$cell];
                                                 $cell='City';
                                                 $dataColumns[$vals] = $cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                           }elseif(strtoupper($cell) == 'PIN CODE'){
                                                 $vals=$temp[$cell];
                                                     $cell='Pincode';
                                                     $dataColumns[$vals] = $cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                         }elseif(strtoupper($cell) == 'STATE'){
                                           $vals=$temp[$cell];
                                               $cell='State';
                                               $dataColumns[$vals] = $cell;
                                               $countCell++;
                                               $uploadedStatus = 1;
                                               continue;
                                             }elseif(strtoupper($cell) == 'MOBILE NO'){
                                               $vals=$temp[$cell];
                                                   $cell='Mobile';
                                                   $dataColumns[$vals] = $cell;
                                                   $countCell++;
                                                   $uploadedStatus = 1;
                                                   continue;
                                                 }elseif(strtoupper($cell) == 'PHONE_RES'){
                                                   $vals=$temp[$cell];
                                                       $cell='Telephone';
                                                       $dataColumns[$vals] = $cell;
                                                       $countCell++;
                                                       $uploadedStatus = 1;
                                                       continue;
                                                     }elseif(strtoupper($cell) == 'EMAIL'){
                                                       $vals=$temp[$cell];
                                                           $cell='Email ID';
                                                           $dataColumns[$vals] = $cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'FOLIO NO'){ // not found
                                                           $vals=$temp[$cell];
                                                           $cell='Folio_Number';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Bank_Name';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'BANK BRANCH'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Bank_branch';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'IFSC CODE'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Bank_ifsc';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'BANK AC NO'){  // bank details headers  //@pallavi 01 Sep 2017 dot removed
                                                            $vals=$temp[$cell.'.'];  //@pallavi 01 Sep 2017 dot added
                                                           $cell='Bank_acc_no';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'ACCOUNT TYPE'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Bank_acc_type';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) === 'BANK CITY'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Bank_address_city';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'PRODUCT CODE'){ // new column added
                                                           $vals=$temp[$cell];
                                                           $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'JOINT HOLDER 1 NAME'){
                                                           $vals=$temp[$cell];
                                                           $cell='Joint_name1';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'JOINT HOLDER 2 NAME'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Joint_name2';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'GUARDIAN NAME'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Guardian Name';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'JOINT HOLDER1 PAN'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Joint 1 pan';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'JOINT HOLDER2 PAN'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Joint 2 pan';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'GUARDIAN PAN'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Guardian pan';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'MODE OF HOLDING'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Mode Of Holding';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'NOMINEE NAME'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Nominee1';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'RELATION WITH INVESTOR'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Nominee1_Relation';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == '2ND  NOMINEE NAME'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Nominee2';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == '2ND NOMINEE RELATION WITH INVESTOR'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Nominee2_Relation';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                           $vals=$temp[$cell];
                                                           $cell='Folio Date';
                                                           $dataColumns[$vals]=$cell;
                                                           $countCell++;
                                                           $uploadedStatus = 1;
                                                           continue;
                                                         }
                                        
                                        }elseif($rta_list=='cams_excel'){ //for CAMS RTA for fetching columns
                                     
                                           if(strtoupper($cell) == 'INV_NAME'){
                                             $vals=$temp[$cell];
                                                 $cell='Client Name';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                            }elseif(strtoupper($cell) == 'PAN_NO'){
                                              $vals=$temp[$cell];
                                                  $cell='Pan No';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'TAX_STATUS'){
                                                  $vals=$temp[$cell];
                                                  $cell='Client Type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                  $vals=$temp[$cell];
                                                  $cell='Occupation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='INV_DOB'){
                                                  $vals=$temp[$cell];
                                                  $cell='Date Of Birth';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                              }elseif(strtoupper($cell) ==='ADDRESS1'){
                                                  $vals=$temp[$cell];
                                                  $cell='House/Flat No';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='ADDRESS2'){
                                                  $vals=$temp[$cell];
                                                  $cell='Street';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='ADDRESS3'){
                                                  $vals=$temp[$cell];
                                                  $cell='Area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='CITY'){
                                                  $vals=$temp[$cell];
                                                  $cell='City';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                // }elseif(strtoupper($cell) == 'NOM_STATE'){ //not found reffered NOM_STATE col from excel
                                                //   $vals=$temp[$cell];
                                                //   $cell='State';
                                                //   $dataColumns[$vals]=$cell;
                                                //   $countCell++;
                                                //   $uploadedStatus = 2;
                                                //   continue;
                                              }elseif(strtoupper($cell) === 'PINCODE'){
                                                  $vals=$temp[$cell];
                                                  $cell='Pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'MOBILE_NO'){
                                                  $vals=$temp[$cell];
                                                  $cell='Mobile';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'PHONE_RES'){
                                                  $vals=$temp[$cell];
                                                  $cell='Telephone';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'EMAIL'){
                                                  $vals=$temp[$cell];
                                                  $cell='Email ID';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLIOCHK'){ // not found
                                                  $vals=$temp[$cell];
                                                  $cell='Folio_Number';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                              // }elseif(strtoupper($cell) === 'BROKER_COD'){
                                              //     $vals=$temp[$cell];
                                              //     $cell='Passport No';
                                              //     $dataColumns[$vals]=$cell;
                                              //     $countCell++;
                                              //     $uploadedStatus = 1;
                                              //     continue;
                                                }elseif(strtoupper($cell) === 'REP_DATE'){  //not confirmed
                                                  $vals=$temp[$cell];
                                                  $cell='Commencement Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_branch';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_ifsc';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'AC_NO'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_no';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_building';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_road';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_city';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }
                                                elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_state';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_country';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PRODUCT'){
                                                  $vals=$temp[$cell];
                                                  $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JNT_NAME1'){
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JNT_NAME2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARD_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT1_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 1 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT2_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 2 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Sub Broker';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'HOLDING_NA'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Mode Of Holding';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM2_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Folio Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }
                                        }elseif($rta_list=='karvy_excel'){
                                            if(strtoupper($cell) == 'INVESTOR NAME'||strtoupper($cell) == 'INVNAME'){
                                            $vals=$temp[$cell];
                                        
                                               $cell='Client Name';

                                               $dataColumns[$vals] = $cell;
                                               $countCell++;
                                               $uploadedStatus = 1;
                                               continue;
                                            }elseif(strtoupper($cell) == 'PAN NUMBER'||strtoupper($cell) == 'PANGNO'){
                                            $vals=$temp[$cell];
                                                $cell='Pan No';
                                                $dataColumns[$vals] = $cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'TAX STATUS'||strtoupper($cell) == 'STATUS'){
                                                $vals=$temp[$cell];
                                                $cell='Client Type';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'OCCUPATION DESCRIPTION'|| strtoupper($cell) == 'OCCP_DESC'){
                                                $vals=$temp[$cell];
                                                $cell='Occupation';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                            }elseif(strtoupper($cell) == 'DATE OF BIRTH'||strtoupper($cell) == 'DOB'){
                                                $vals=$temp[$cell];
                                                $cell='Date Of Birth';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                            }elseif(strtoupper($cell) == 'CATEGORYDESC'){
                                                $vals=$temp[$cell];
                                                $cell='Client Category';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'ADDRESS #1'||strtoupper($cell) == 'ADD1'){
                                                $vals=$temp[$cell];
                                                $cell='House/Flat No';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'ADDRESS #2'||strtoupper($cell) == 'ADD2'){
                                                $vals=$temp[$cell];
                                                $cell='Street';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'ADDRESS #3'||strtoupper($cell) == 'ADD3'){
                                                $vals=$temp[$cell];
                                                $cell='Area';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'CITY'){
                                                $vals=$temp[$cell];
                                                $cell='City';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'STATE'){
                                                $vals=$temp[$cell];
                                                $cell='State';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'PINCODE'||strtoupper($cell) == 'PIN'){
                                                $vals=$temp[$cell];
                                                $cell='Pincode';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'MOBILE NUMBER'|| strtoupper($cell) == 'MOBILE'){
                                                $vals=$temp[$cell];
                                                $cell='Mobile';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'PHONE RESIDENCE'||strtoupper($cell) == 'RPHONE'){
                                                $vals=$temp[$cell];
                                                $cell='Telephone';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'EMAIL'){
                                                $vals=$temp[$cell];
                                                $cell='Email ID';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              // }elseif(strtoupper($cell) == 'BROKER CODE'||strtoupper($cell) == 'BROKCODE'){
                                              //   $vals=$temp[$cell];
                                              //   $cell='Passport No';
                                              //   $dataColumns[$vals]=$cell;
                                              //   $countCell++;
                                              //   $uploadedStatus = 1;
                                              //   continue;
                                              }elseif(strtoupper($cell) == 'FOLIO'||strtoupper($cell) == 'ACNO'){
                                                $vals=$temp[$cell];
                                                $cell='Folio_Number';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'REPORT DATE'|| strtoupper($cell) === 'CRDATE'){  //not confirmed
                                                $vals=$temp[$cell];
                                                $cell='Commencement Date';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK NAME'||strtoupper($cell) === 'BNAME'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_Name';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_branch';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'IFSC CODE'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_ifsc';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANKACCNO'|| strtoupper($cell) === 'BNKACNO' ){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_acc_no';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'ACCOUNT TYPE'|| strtoupper($cell) === 'BNKACTYPE'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_acc_type';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK ADDRESS #1'||strtoupper($cell) === 'BADD1'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_building';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK ADDRESS #2'|| strtoupper($cell) === 'BADD2'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_road';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK ADDRESS #3'||strtoupper($cell) === 'BADD3'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_area';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK CITY'||strtoupper($cell) === 'BCITY'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_city';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) === 'BANK PINCODE'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_pincode';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }
                                              elseif(strtoupper($cell) == 'BANK STATE'||strtoupper($cell) == 'BSTATE'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_state';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'BANK COUNTRY'||strtoupper($cell) == 'BCOUNTRY'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Bank_address_country';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'Product Code'||strtoupper($cell) == 'PRCODE'){
                                                $vals=$temp[$cell];
                                                $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'Joint Name 1'||strtoupper($cell) == 'JTNAME1'){
                                                $vals=$temp[$cell];
                                                $cell='Joint_name1';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'Joint Name 2'||strtoupper($cell) == 'JTNAME2'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Joint_name2';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'GuardianName'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Guardian Name';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'PAN2'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Joint 1 pan';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'PAN3'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Joint 2 pan';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Guardian pan';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Sub Broker';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'MODEOFHOLD'||strtoupper($cell) == 'Mode of Holding Description'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Mode Of Holding';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'NOMINEE1'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Nominee1';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Nominee1_Relation';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'NOMINEE2'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Nominee2';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Nominee2_Relation';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                $vals=$temp[$cell];
                                                $cell='Folio Date';
                                                $dataColumns[$vals]=$cell;
                                                $countCell++;
                                                $uploadedStatus = 1;
                                                continue;
                                              }
                                        }elseif($rta_list=='sundaram_excel'){  //sundaram excel column headings

                                            if(strtoupper($cell) == 'INVNAME'){
                                            $vals=$temp[$cell];
                                                 $cell='Client Name';
                                                 $dataColumns[$vals] = $cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;

                                            }elseif(strtoupper($cell) == 'PAN'){
                                              $vals=$temp[$cell];
                                                  $cell='Pan No';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'TAXSTAT'){
                                                  $vals=$temp[$cell];
                                                  $cell='Client Type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                  $vals=$temp[$cell];
                                                  $cell='Occupation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;

                                                }elseif(strtoupper($cell) == 'INVDOB'){
                                                  $vals=$temp[$cell];
                                                  $cell='Date Of Birth';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS1'){
                                                  $vals=$temp[$cell];
                                                  $cell='House/Flat No';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS2'){
                                                  $vals=$temp[$cell];
                                                  $cell='Street';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS3'){
                                                  $vals=$temp[$cell];
                                                  $cell='Area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'CITY'){
                                                  $vals=$temp[$cell];
                                                  $cell='City';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'CSTATE'){
                                                  $vals=$temp[$cell];
                                                  $cell='State';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PINCODE'){
                                                  $vals=$temp[$cell];
                                                  $cell='Pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'INVMOBIL'){
                                                  $vals=$temp[$cell];
                                                  $cell='Mobile';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PHOFF'){
                                                  $vals=$temp[$cell];
                                                  $cell='Telephone';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'EMAIL'){
                                                  $vals=$temp[$cell];
                                                  $cell='Email ID';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                // }elseif(strtoupper($cell) == 'BROKCODE'){
                                                //   $vals=$temp[$cell];
                                                //   $cell='Passport No';
                                                //   $dataColumns[$vals]=$cell;
                                                //   $countCell++;
                                                //   $uploadedStatus = 1;
                                                //   continue;
                                                }elseif(strtoupper($cell) == 'SBFSFOLIO'){ // not found
                                                  $vals=$temp[$cell];
                                                  //echo $vals;exit;
                                                  $cell='Folio_Number';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;

                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLCRTDT'){
                                                  $vals=$temp[$cell];

                                                  $cell='Commencement Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                 }elseif(strtoupper($cell) === 'BANKNAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANKBRA'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_branch';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'IFSCCODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_ifsc';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANKACNO'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_no';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANKACT'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK ADDRESS #1'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_building';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK ADDRESS #2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_road';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK ADDRESS #3'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK CITY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_city';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK PINCODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }
                                                elseif(strtoupper($cell) == 'BANK STATE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_state';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'BANK COUNTRY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_country';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PRODCODE'){
                                                  $vals=$temp[$cell];
                                                  $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINTNAME1'){
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINTNAME2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARNAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT1PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 1 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT2PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 2 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARPAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'SUBBRKCOD'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Sub Broker';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'HOLNAT'||strtoupper($cell) == 'Mode of Holding Description'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Mode Of Holding';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOMNAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOMNAM2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLCRTDT'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Folio Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }
                                        }elseif($rta_list=='frank_excel'){

                                            if(strtoupper($cell) == 'INV_NAME'|| strtoupper($cell) == 'INVNAME'){
                                                $vals=$temp[$cell];
                                                 $cell='Client Name';

                                                 $dataColumns[$vals] = $cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;

                                            }elseif(strtoupper($cell) == 'PANNO1'){
                                              $vals=$temp[$cell];
                                                  $cell='Pan No';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'TAX_STATUS'){
                                                  $vals=$temp[$cell];
                                                  $cell='Client Type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;

                                                }elseif(strtoupper($cell) == 'D_BIRTH'){
                                                  $vals=$temp[$cell];
                                                  $cell='Date Of Birth';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS1'){
                                                  $vals=$temp[$cell];
                                                  $cell='House/Flat No';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS2'){
                                                  $vals=$temp[$cell];
                                                  $cell='Street';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'ADDRESS3'){
                                                  $vals=$temp[$cell];
                                                  $cell='Area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'CITY'){
                                                  $vals=$temp[$cell];
                                                  $cell='City';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'STATE'){
                                                  $vals=$temp[$cell];
                                                  $cell='State';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PINCODE'){
                                                  $vals=$temp[$cell];
                                                  $cell='Pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;

                                              }elseif(strtoupper($cell) == 'PHONE_RES'){
                                                  $vals=$temp[$cell];
                                                  $cell='Telephone';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'EMAIL'){
                                                  $vals=$temp[$cell];
                                                  $cell='Email ID';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLIO_NO'){ // not found
                                                  $vals=$temp[$cell];
                                                  $cell='Folio_Number';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                // }elseif(strtoupper($cell) == 'BROK_CODE'){
                                                //   $vals=$temp[$cell];
                                                //   $cell='Passport No';
                                                //   $dataColumns[$vals]=$cell;
                                                //   $countCell++;
                                                //   $uploadedStatus = 1;
                                                //   continue;
                                                }elseif(strtoupper($cell) == 'CREA_DATE'){
                                                  $vals=$temp[$cell];

                                                  $cell='Commencement Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_Name';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_branch';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_ifsc';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'ACCNT_NO'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_acc_no';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_acc_type';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_building';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_road';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_area';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_city';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_pincode';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }
                                               elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_state';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Bank_address_country';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                              //  }elseif(strtoupper($cell) == 'INVEST_ID'|| strtoupper($cell) == 'PRODCODE'){
                                              //    $vals=$temp[$cell];
                                              //    $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                              //    $dataColumns[$vals]=$cell;
                                              //    $countCell++;
                                              //    $uploadedStatus = 1;
                                              //    continue;
                                               }elseif(strtoupper($cell) == 'JOINT_NAM1'){
                                                 $vals=$temp[$cell];
                                                 $cell='Joint_name1';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'JOINT_NAM2'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Joint_name2';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'GUARNAME'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Guardian Name';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'PANNO2'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Joint 1 pan';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'PANNO3'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Joint 2 pan';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'GUARPAN'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Guardian pan';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'SUB_BROK18'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Sub Broker';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'HOLDING_T6'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Mode Of Holding';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'NOMINEE1'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Nominee1';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Nominee1_Relation';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'NOMINEE2'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Nominee2';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Nominee2_Relation';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }elseif(strtoupper($cell) == 'FOLCRTDT'){  // bank details headers
                                                 $vals=$temp[$cell];
                                                 $cell='Folio Date';
                                                 $dataColumns[$vals]=$cell;
                                                 $countCell++;
                                                 $uploadedStatus = 1;
                                                 continue;
                                               }

                                      }
                                      }
                              else
                              {
                                 if($rta_list=='cl_excel'){
                                  if($insertRow)
                                  {
                                   
                                      if(strtoupper($dataColumns[$countCell]) === 'FAMILY NAME')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $famName = trim($cell);

                                              //checks if family exists in Families table
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              if(count($f_info) == 0)
                                              {
                                                  //$insertRow = false;
                                                  //$impMessage = $famName." Family doesn't exist";
                                                  //family doesn't exist, so add a new family
                                                  $data = array(
                                                      'name' => $famName,
                                                      'status' => 1,
                                                      'broker_id' => $brokerID,
                                                      'user_id' => $user_id
                                                  );
                                                  $status = $this->Families_model->add($data);
                                                  if(isset($status['code'])) {
                                                      $insertRow = false;
                                                      $impMessage = "Could not add new Family for this client";
                                                  } else {
                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                      if(count($f_info) == 0)
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Could not find Family for this client";
                                                      }
                                                      else
                                                      {
                                                          $familyId = $f_info[0]->family_id;
                                                      }
                                                  }
                                              }
                                              else
                                              {
                                                  $familyId = $f_info[0]->family_id;
                                              }
                                          }
                                      }

                                      if(strtoupper($dataColumns[$countCell]) === 'CLIENT NAME')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                              $whereClient = array(
                                                  'c.name'=>$clientName,
                                                  'fam.name'=>$famName,
                                                  'fam.broker_id'=>$brokerID
                                              );
                                              //checks if client exists in Clients table
                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                              if(count($c_info) == 0)
                                              {
                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                  $clientId = $clientId_obj->client_id;
                                              }
                                              else
                                              {
                                                $clientAlready=1;

                                                  // $insertRow = false;
                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";
                                          }


                                      }

                                      if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if client  exists in policy details table
                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($p_info) == 0)
                                              {
                                                  //proceed normally
                                              }
                                              else
                                              {
                                                $panAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";
                                                  //$clientId = $c_info->client_id;
                                                  //$clientName = $c_info->client_name;
                                                  //$familyId = $c_info->family_id;
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Pan Number cannot be empty";
                                          // }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'CLIENT TYPE')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $clientType = trim($cell);
                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                              if(count($type_details) == 0)
                                              {
                                              //  $clientTypeId=40; //for others type of client Type
                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                  // $insertRow = false;
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                              else
                                              {
                                                  $clientTypeId = $type_details[0]->client_type_id;
                                              }
                                          }
                                          else
                                          {
                                              //$clientTypeId=40; //for others type of client Type
                                              // $insertRow = false;
                                              // $impMessage = "Client Type cannot be empty";
                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'OCCUPATION')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $occ = trim($cell);
                                              $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                              if(count($occ_details) == 0)
                                              {
                                                $occId=null; //for others type of occupation

                                                  // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                  // $insertRow = false;
                                              }
                                              else
                                              {
                                                  $occId = $occ_details[0]->occupation_id;
                                              }
                                          }
                                          else
                                          {
                                            $occId=null;
                                              // $insertRow = false;
                                              // $impMessage = "Occupation cannot be empty";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'HEAD OF FAMILY')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $hof = trim($cell);
                                              if(strtoupper($hof) == "YES") {
                                                  $hof = 1;
                                              } else {
                                                  $hof = 0;
                                              }
                                          }
                                          else
                                          {
                                              $hof = 0;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'RELATION W/ HOF')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $rHof = trim($cell);
                                          }
                                          else
                                          {
                                              $rHof = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'REPORT ORDER')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $report = trim($cell);
                                          }
                                          else
                                          {
                                              $report = 0;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'DATE OF BIRTH')
                                      {
                                          if($cell || $cell != '')
                                          {
                                                              
                                            $date = date_create_from_format('d-m-Y', $cell);
                                            if($date=='')
                                            {
                                              $date=date_create_from_format('d/m/Y',$cell);
                                            }
										                        if(is_object($date)) {
                                                $today = new DateTime();
                                                if($today < $date) {
                                                    date_modify($date,"-100 Year");
                                                    $dob = date_format($date,"Y-m-d");
                                                    //$dob = $date->modify('-100 year')->format('Y-m-d');
                                                } else {
                                                    $dob = date_format($date,"Y-m-d");
											
                                                }
                                                //var_dump($dob);
                                                $dob_app = 1;
                                            } else {
                                                $insertRow = false;
                                                $impMessage = "Date of Birth format is not proper (should be dd/mm/yyyy)";
                                            }
                                          }
                                          else
                                          {
                                              $dob = null;
                                              $dob_app = 0;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'ANNIVERSARY')
                                      {
                                        if($cell || $cell != '')
                                        {
                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                          //  $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                              $ann=$date->format('Y-m-d');
                                                $anv_app = 1;
                                            } else {
                                              $date = new DateTime($cell);
                                               if(is_object($date)){
                                                 $ann=$date->format('Y-m-d');

                                               }else{
                                                $insertRow = false;
                                                $impMessage = "Anniversary date format is not proper (should be dd/mm/yyyy)";
                                            }
                                        }
                                      } else
                                        {
                                            $ann = null;
                                            $anv_app = 0;
                                        }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'SPOUSE NAME')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $spouse = trim($cell);
                                          }
                                          else
                                          {
                                              $spouse = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'CHILDREN NAME')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $child = trim($cell);
                                          }
                                          else
                                          {
                                              $child = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'HOUSE/FLAT NO')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $flat = trim($cell);
                                          }
                                          else
                                          {
                                              $flat = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'STREET')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $street = trim($cell);
                                          }
                                          else
                                          {
                                              $street = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'AREA')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $area = trim($cell);
                                          }
                                          else
                                          {
                                              $area = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'CITY')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $city = trim($cell);
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "City cannot be empty";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'STATE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $state = trim($cell);
                                          }
                                          else
                                          {
                                            $state="";
                                              // $insertRow = false;
                                              // $impMessage = "State cannot be empty";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'PINCODE')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $pin = trim($cell);
                                          }
                                          else
                                          {
                                              $pin = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'MOBILE')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $mobile = trim($cell);
                                          }
                                          else
                                          {
                                              $mobile = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'TELEPHONE')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $tel = trim($cell);
                                          }
                                          else
                                          {
                                              $tel = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'EMAIL ID')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $email = trim($cell);
                                          }
                                          else
                                          {
                                            $email="";
                                              // $insertRow = false;
                                              // $impMessage = "Email ID cannot be empty";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'USERNAME')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $username = trim($cell);
                                              //check duplicate username
                                              //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                              $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                              $join = array(
                                                  'table' => 'families f',
                                                  'on' => 'c.family_id = f.family_id',
                                                  'type' => 'inner'
                                              );
                                              $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                              if($isUsernameDuplicate) {
                                                  $insertRow = false;
                                                  $impMessage = "Username already exists for another client";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Username cannot be empty";
                                          }
                                      }
                                      /*else if(strtoupper($dataColumns[$countCell]) === 'PASSWORD')
                                      {
                                          if($cell  || $cell != '')
                                          {
                                              $password = trim($cell);
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Password cannot be empty";
                                          }
                                      }*/
                                      else if(strtoupper($dataColumns[$countCell]) === 'PASSPORT NO')
                                      {
                                          if($cell || $cell != '')
                                              $passport = trim($cell);
                                          else
                                              $passport = "";
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'COMMENCEMENT DATE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $date = new DateTime();
                                              //$date = DateTime::createFromFormat('m-d-y', $cell);
                                              if(is_object($date)) {
                                                  $today = new DateTime();
                                                  if($today < $date) {
                                                      $comDate = $date->modify('-100 year')->format('Y-m-d');
                                                  } else {
                                                      $comDate = $date->format('Y-m-d');
                                                  }
                                              } else {
                                                  $insertRow = false;
                                                  $impMessage = "Commencement date format is not proper (should be dd/mm/yyyy)";
                                              }
                                          }
                                          else
                                          {
                                              $comDate = date('Y-m-d');
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'CLIENT CATEGORY')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $category = trim($cell);
                                          }
                                          else
                                          {
                                              $category = "";
                                          }
                                      }
                                    } else {

                                    if(strtoupper($dataColumns[$countCell]) === 'CLIENT NAME')
                                    {
                                         $clientName = trim($cell);
                                     
                                    }
                                    if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                    {
                                         $panNum = trim($cell);
                                      }
                                      if(strtoupper($dataColumns[$countCell]) === 'FOLIO_NUMBER')
                                      {
                                        $folio_number = trim($cell);
                                          // if($cell || $cell != '')
                                          // {
                                          //     $folio_number = trim($cell);
                                          // }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Folio Number cannot be empty";
                                          // }
                                      }

                                  }
                                  $countCell++;
                                }
                                elseif($rta_list=='karvy_excel')
                                { 
                                    unset($bankBranch);
                                  if($insertRow)
                                  {
                                   
                                    if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $famName = trim($cell);
                                          //   echo $famName;//exit;
                                              //checks if family exists in Families table
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              if(count($f_info) == 0)
                                              {
                                                  //$insertRow = false;
                                                  //$impMessage = $famName." Family doesn't exist";
                                                  //family doesn't exist, so add a new family
                                                  $data = array(
                                                      'name' => $famName,
                                                      'status' => 1,
                                                      'broker_id' => $brokerID,
                                                      'user_id' => $user_id
                                                  );
                                                  $status = $this->Families_model->add($data);
                                                  if(isset($status['code'])) {
                                                      $insertRow = false;
                                                      $impMessage = "Could not add new Family for this client";
                                                  } else {
                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                      if(count($f_info) == 0)
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Could not find Family for this client";
                                                      }
                                                      else
                                                      {
                                                          $familyId = $f_info[0]->family_id;
                                                      }
                                                  }
                                              }
                                              else
                                              {
                                                  $familyId = $f_info[0]->family_id;
                                              }
                                          }
                                          else
                                          {
                                          //  $clientAlready=1;
                                              // $insertRow = false;
                                              // $impMessage = "Family Name cannot be empty";
                                          }
                                        }else{ // If family name is not found in excel then drop the client in default family

                                              $famName ="Default family"; //default family for non family client
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                          //    print_r($f_info );
                                              $familyId = $f_info[0]->family_id;
                                              //echo $familyId;exit;
                                        }

                                    if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];

                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          //   echo $clientName; //exit;
                                              $whereClient = array(
                                                  'c.name'=>$clientName,
                                                  'fam.name'=>$famName,
                                                  'fam.broker_id'=>$brokerID
                                              );
                                              //checks if client exists in Clients table
                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                              if(count($c_info) == 0)
                                              {
                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                  $clientId = $clientId_obj->client_id;
                                              }
                                              else
                                              {
                                                  $clientAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";
                                          }
                                        }else{
                                          $insertRow = false;
                                          $impMessage = "Client Name cannot be empty";

                                        }


                                    if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                        //   echo $panNum;//exit;
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if policy exists in policy details table
                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($p_info) == 0)
                                              {
                                                  //proceed normally
                                              }
                                              else
                                              {
                                                $panAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";
                                              }
                                          }
                                          // else
                                          // {
                                          //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                          //    $dummypan = $clientId_obj->client_id;
                                          //    //C20160001
                                          //
                                          //   $panNum =str_replace('C','P',$dummypan);
                                          // //  echo "dummy pan".$panNum;exit;
                                          // }
                                          $pan_no=$panNum;//for client bank details
                                      }

                                  if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                    {
                                      $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $clientType = trim($cell);
                                          //    echo $clientType;
                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                              if(count($type_details) == 0)
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                  // $insertRow = false;
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                              else
                                              {
                                                  $clientTypeId = $type_details[0]->client_type_id;
                                              }
                                          }
                                          else
                                          {
                                            $defaultClientType='Resident Individual';
                                              $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                          $ctype= $this->Clients_model->get_client_types($whereCType);
                                          $clientTypeId =  $ctype[0]->client_type_id;
                                              // $insertRow = false;
                                              // $impMessage = "Client Type cannot be empty";
                                          }
                                          $tax_status=$clientType;//for client's bank details
                                      }else{

                                        $defaultClientType='Resident Individual';
                                          $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                      $ctype= $this->Clients_model->get_client_types($whereCType);
                                      $clientTypeId =  $ctype[0]->client_type_id;

                                      $tax_status="";//for client's bank details
                                      $clientType=$clientTypeId;
                                      }
                                      if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $occ = trim($cell);
                                             //  echo $occ;exit;
                                               $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                               $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                               if(count($occ_details) == 0)
                                               {
                                                   $occId=null; //for others type of occupation
                                                  //  $impMessage = 'Occupation '.$occ." doesn't exist";
                                                  //  $insertRow = false;
                                               }
                                               else
                                               {
                                                   $occId = $occ_details[0]->occupation_id;
                                               }
                                           }
                                           else
                                           {
                                               $occId=null;
                                              //  $insertRow = false;
                                              //  $impMessage = "Occupation cannot be empty";
                                           }
                                          $occ_name=$occ;//for client bank details
                                       }

                                 if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
                                   {
                                     $cell=$rows[$findkey];
                                       if($cell || $cell != '')
                                           {
                                               $hof = trim($cell);

                                               if(strtoupper($hof) == "YES") {
                                                   $hof = 1;
                                               } else {
                                                   $hof = 0;
                                               }
                                           }
                                           else
                                           {
                                               $hof = 0;
                                           }
                                           //  echo $hof;exit;
                                       }

                                      if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $rHof = trim($cell);
                                               //echo $rHof;
                                           }
                                           else
                                           {
                                               $rHof = "";
                                           }

                                       }


                                       if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             //echo $cell;
                                           // $cell=trim(str_replace('/','-', $cell));
                                       //  echo $cell; //exit; //date checked
                                          if($cell || $cell != '')
                                          {
                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                              //var_dump($cell);exit;
                                              //$date->format('Y-m-d');
                                              if(is_object($date)){
                                                $dob=$date->format('Y-m-d');
                                              }else{
                                                $date = new DateTime($cell);
                                                 if(is_object($date)){
                                                   $dob=$date->format('Y-m-d');
                                                 }else{

                                                         $insertRow = false;
                                                         $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                     }

                                                 }

                                              } else
                                               {
                                                   $dob = null;
                                                   $dob_app = 0;
                                               }

                                          }else
                                          {
                                              $dob = null;
                                              $dob_app = 0;
                                          }

                                       if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $flat = trim($cell);
                                           }
                                           else
                                           {
                                               $flat = "";
                                           }

                                       }

                                        if(!empty($findkey=array_search('Street',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $street = trim($cell);
                                           }
                                           else
                                           {
                                               $street = "";
                                           }

                                       }

                                        if(!empty($findkey=array_search('Area',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $area = trim($cell);
                                           }
                                           else
                                           {
                                               $area = "";
                                           }

                                       }
                                        if(!empty($findkey=array_search('City',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $city = trim($cell);
                                           }
                                           else
                                           {
                                             $city="";
                                              //  $insertRow = false;
                                              //  $impMessage = "City cannot be empty";
                                           }

                                       }

                                       if(!empty($findkey=array_search('State',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $state = trim($cell);
                                           }
                                           else
                                           {
                                               $state="";
                                              //  $insertRow = false;
                                              //  $impMessage = "State cannot be empty";
                                           }
                                       }

                                        if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                           if($cell  || $cell != '')
                                           {
                                               $pin = trim($cell);
                                           }
                                           else
                                           {
                                               $pin = "";
                                           }

                                       }

                                        if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                           if($cell  || $cell != '')
                                           {
                                               $mobile = trim($cell);
                                           }
                                           else
                                           {
                                               $mobile = "";
                                           }

                                       }

                                       if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                           if($cell  || $cell != '')
                                           {
                                               $tel = trim($cell);
                                           }
                                           else
                                           {
                                               $tel = "";
                                           }

                                       }

                                     if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                           if($cell  || $cell != '')
                                           {
                                               $email = trim($cell);
                                           }
                                           else
                                           {
                                             $email="";
                                              //  $insertRow = false;
                                              //  $impMessage = "Email ID cannot be empty";
                                           }

                                       }

                                       if(!empty($findkey=array_search('Username',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                           if($cell  || $cell != '')
                                           {
                                               $username = trim($cell);
                                               //check duplicate username
                                               //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                               $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                               $join = array(
                                                   'table' => 'families f',
                                                   'on' => 'c.family_id = f.family_id',
                                                   'type' => 'inner'
                                               );
                                               $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                               if($isUsernameDuplicate) {
                                                 //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                 $username=date("Ymd").time().$username.rand(99,99999);
                                               }
                                           }
                                           // else
                                           // {
                                           //     $insertRow = false;
                                           //     $impMessage = "Username cannot be empty";
                                           // }
                                       }else{ //if username is not in excel sheet create default username with client name and random number
                                          $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                       }


                                       if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                           if($cell || $cell != ''){
                                               $passport = trim($cell);
                                             }
                                           else{
                                               $passport = "";
                                             }
                                          //$broker_code = $passport; //for client bank details
                                       }

                                       if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             $cell=trim(str_replace('/','-', $cell));
                                           if($cell || $cell != '')
                                           {
                                            $date = DateTime::createFromFormat('m-d-Y', $cell);
                                               //var_dump($cell);exit;
                                              // $date->format('Y-m-d');
                                               if(is_object($date)){
                                                 $comDate=$date->format('Y-m-d');
                                               }else{
                                                 $date = new DateTime($cell);
                                                  if(is_object($date)){
                                                    $comDate=$date->format('Y-m-d');

                                                  }else{

                                                          // $insertRow = false;
                                                          // $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                      }

                                                  }

                                               } else
                                                {
                                                    $comDate = null;
                                                    $dob_app = 0;
                                                }
                                                 // echo $dob;//exit;
                                           }else
                                           {
                                               $comDate = null;
                                               $dob_app = 0;
                                           }

                                       if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             if($cell || $cell != '')
                                             {
                                                 //$folio_number = trim($cell);
                                                 $folio_number = stripcslashes(trim($cell));
                                                 $folio_number = str_replace("'",'',$folio_number);

                                             }
                                         }elseif($findkey==0){
                                           $cell=$rows[$findkey];
                                           $folio_number = stripcslashes(trim($cell));
                                           $folio_number = str_replace("'",'',$folio_number);

                                         }
                                       if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             if($cell || $cell != '')
                                             {
                                                 $bankName = trim($cell);

                                             }
                                         }
                                         if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $bankBranch = trim($cell);

                                               }
                                           }else{
                                             $bankBranch="";
                                           }
                                           if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 if($cell || $cell != '')
                                                 {
                                                     $bank_account_types = trim($cell);

                                                 }
                                             }
                                             if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                               {
                                                 $cell=$rows[$findkey];
                                                   if($cell || $cell != '')
                                                   {
                                                       $bankAccNo = trim($cell);

                                                   }
                                               }
                                               if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                 {
                                                   $cell=$rows[$findkey];
                                                     if($cell || $cell != '')
                                                     {
                                                         $bank_address_building = trim($cell);

                                                     }

                                                 }
                                                 if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                   {

                                                     $cell=$rows[$findkey];
                                                       if($cell || $cell != '')
                                                       {
                                                           $bank_address_road = trim($cell);

                                                       }
                                                        //echo $bank_address_road;
                                                   }
                                                   if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                     {

                                                       $cell=$rows[$findkey];
                                                         if($cell || $cell != '')
                                                         {
                                                             $bank_address_area = trim($cell);

                                                         }
                                                      //   echo $bank_address_area;
                                                     }
                                                     if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                       {
                                                         $cell=$rows[$findkey];
                                                           if($cell || $cell != '')
                                                           {
                                                               $bank_address_city = trim($cell);

                                                           }
                                                       }
                                                       if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                         {
                                                           $cell=$rows[$findkey];
                                                             if($cell || $cell != '')
                                                             {
                                                                 $bank_pincode = trim($cell);

                                                             }
                                                         }
                                                         if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                           {
                                                             $cell=$rows[$findkey];
                                                               if($cell || $cell != '')
                                                               {
                                                                   $bank_state = trim($cell);

                                                               }
                                                           }
                                                           if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                             {
                                                               $cell=$rows[$findkey];
                                                                 if($cell || $cell != '')
                                                                 {
                                                                     $bank_country = trim($cell);

                                                                 }
                                                             }

                                      if(!empty($findkey=array_search('Client Category',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $category = trim($cell);
                                          }
                                          else
                                          {
                                              $category = "";
                                          }
                                      }
                                      //for other details of bank
                                      if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                        {
                                          // $cell=$rows[$findkey];
                                          //   if($cell || $cell != '')
                                          //   {
                                          //     $productId = trim($cell);
                                          //
                                          //   }else{
                                          //     $productId="";
                                          //   }

                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                              $productId = trim($cell);
                                                    $data=array();

                                                   $data['broker_id']=$brokerID;
                                                   if($productId){
                                                    $data['productId']=$productId;
                                                   }
                                                   if($folio_number){
                                                      $data['folio_number']=$folio_number;
                                                   }
                                                   if($clientName){
                                                          $data['clientName']=$clientName;
                                                   }
                                                   if($pan_no){
                                                     $data['pan_no']=$pan_no;
                                                   }

                                                    //check product scheme  already available
                                                   $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                  //   print_r($scheme_info);exit;
                                                   if(count($scheme_info) == 0)
                                                   {
                                                       //proceed normally
                                                      // $productId=$productId;
                                                      $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                   }
                                                   else
                                                   {
                                                      $notFolio=1;
                                                      //  $insertRow = false;
                                                      //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                   }
                                            }

                                        }elseif($findkey==0){
                                        //  echo"key is zero";//exit;
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                              $productId = trim($cell);
                                                    $data=array();
                                                   $data['productId']=$productId;
                                                   $data['broker_id']=$brokerID;
                                                   if($folio_number){
                                                      $data['folio_number']=$folio_number;
                                                   }
                                                   if($clientName){
                                                          $data['clientName']=$clientName;
                                                   }

                                                   if($pan_no){
                                                     $data['pan_no']=$pan_no;
                                                   }

                                                    //check product scheme  already available
                                                   $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                            //   print_r($scheme_info);exit;
                                                   if(count($scheme_info) == 0)
                                                   {
                                                       //proceed normally
                                                      // $productId=$productId;
                                                      $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                   }
                                                   else
                                                   {
                                                      $notFolio=1;
                                                      //  $insertRow = false;
                                                      //    $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                   }

                                            }
                                        }
                                        if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                $jointName1 = trim($cell);

                                              }else{
                                                $jointName1="";
                                              }
                                          }
                                          if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                  $jointName2 = trim($cell);

                                                }else{
                                                  $jointName2="";
                                                }
                                            }
                                            if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                    $joint1_pan = trim($cell);

                                                  }else{
                                                    $joint1_pan="";
                                                  }
                                              }
                                              if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                      $joint2_pan = trim($cell);

                                                    }else{
                                                      $joint2_pan="";
                                                    }
                                                }
                                                if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                        $guard_pan = trim($cell);

                                                      }else{
                                                        $guard_pan="";
                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                          $sub_boroker = trim($cell);

                                                        }else{
                                                          $sub_boroker="";
                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                            $mode_holding = trim($cell);

                                                          }else{
                                                            $mode_holding="";
                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                              $nominee_name1 = trim($cell);

                                                            }else{
                                                              $nominee_name1="";
                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                $nom1_relation = trim($cell);

                                                              }else{
                                                                $nom1_relation ="";
                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                  $nominee_name2 = trim($cell);

                                                                }else{
                                                                  $nominee_name2="";
                                                                }
                                                            }
                                                            if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $nom2_relation = trim($cell);

                                                                  }else{
                                                                    $nom2_relation ="";
                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                      $guardian_name= trim($cell);

                                                                    }else{
                                                                      $guardian_name="";
                                                                    }
                                                                }
                                                                if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                    $cell=trim(str_replace('/','-', $cell));
                                                                    if($cell || $cell != '')
                                                                    {
                                                                     $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                        //var_dump($cell);exit;
                                                                       // $date->format('Y-m-d');
                                                                        if(is_object($date)){
                                                                          $folioDate=$date->format('Y-m-d');
                                                                        }else{
                                                                          $date = new DateTime($cell);
                                                                           if(is_object($date)){
                                                                             $folioDate=$date->format('Y-m-d');

                                                                           }else{

                                                                                   // $insertRow = false;
                                                                                   // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                               }

                                                                           }

                                                                        } else
                                                                         {
                                                                             $folioDate = null;
                                                                             $dob_app = 0;
                                                                         }
                                                                          // echo $dob;//exit;
                                                                    }else
                                                                    {
                                                                        $folioDate = null;
                                                                        $dob_app = 0;
                                                                    }



                                  } else {

                                      if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Client Name cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Pan Number cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $folio_number = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Folio Number cannot be empty";
                                          // }
                                      }

                                  }
                                  $countCell++;

                                }elseif($rta_list=='cams_excel'){  // for CAMS RTA

                                  if($insertRow)
                                  {
                                    // echo $countCell;
                              
                                    if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $famName = trim($cell);
                                      //echo $famName;exit;
                                              //checks if family exists in Families table
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              if(count($f_info) == 0)
                                              {
                                                  //$insertRow = false;
                                                  //$impMessage = $famName." Family doesn't exist";
                                                  //family doesn't exist, so add a new family
                                                  $data = array(
                                                      'name' => $famName,
                                                      'status' => 1,
                                                      'broker_id' => $brokerID,
                                                      'user_id' => $user_id
                                                  );
                                                  $status = $this->Families_model->add($data);
                                                  if(isset($status['code'])) {
                                                      $insertRow = false;
                                                      $impMessage = "Could not add new Family for this client";
                                                  } else {
                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                      if(count($f_info) == 0)
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Could not find Family for this client";
                                                      }
                                                      else
                                                      {
                                                          $familyId = $f_info[0]->family_id;
                                                      }
                                                  }
                                              }
                                              else
                                              {
                                                  $familyId = $f_info[0]->family_id;
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Family Name cannot be empty";
                                          // }
                                      }else{ // If family name is not found in excel then drop the client in default family

                                            $famName ="Default family"; //default family for non family client
                                            $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                            $familyId = $f_info[0]->family_id;
                                      }


                                    if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];

                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                            //echo $clientName; exit;
                                              $whereClient = array(
                                                  'c.name'=>$clientName,
                                                  'fam.name'=>$famName,
                                                  'fam.broker_id'=>$brokerID
                                              );
                                              //checks if client exists in Clients table
                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                              if(count($c_info) == 0)
                                              {
                                                  $clientAlready=0;
                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                  $clientId = $clientId_obj->client_id;
                                              }
                                              else
                                              {
                                                $clientAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";
                                          }
                                      }else{
                                        $insertRow = false;
                                        $impMessage = "Client Name cannot be empty";

                                      }


                                    if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          //  echo $panNum;exit;
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if policy exists in policy details table
                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($p_info) == 0)
                                              {
                                                  //proceed normally
                                                  $panAlready=0;
                                              }
                                              else
                                              {
                                                $panAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                              }
                                          }
                                          // else
                                          // {
                                          //
                                          //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                          //    $dummypan = $clientId_obj->client_id;
                                          //    //C20160001
                                          //
                                          //   $panNum =str_replace('C','P',$dummypan);
                                          // //  echo "dummy pan".$panNum;exit;
                                          // }
                                          $pan_no=$panNum; //for client Bank  details
                                      }else{
                                        $panNum=$pan_no="";

                                      }

                                  if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                    {
                                      $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $clientType = trim($cell);
                                              //  echo $clientType;
                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                              if(count($type_details) == 0)
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                  // $insertRow = false;
                                              }
                                              else
                                              {
                                                  $clientTypeId = $type_details[0]->client_type_id;
                                              }
                                          }
                                          else
                                          {
                                            //  $clientTypeId=40; //for others type of client Type
                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;
                                              // $insertRow = false;
                                              // $impMessage = "Client Type cannot be empty";
                                          }
                                            $tax_status=$clientType; //for client bank details
                                            //  echo   $clientTypeId;exit;
                                      }else{
                                        // $clientTypeId=40; //for  file not having client type col.
                                        //     $tax_status=""; //for client bank details
                                        //       $clientType=40;

                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;

                                          //  echo   $clientTypeId;exit;
                                      }

                                     if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $occ = trim($cell);
                                            //  echo $occ;exit;
                                              $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                              if(count($occ_details) == 0)
                                              {
                                                  $occId=null; //for others type of occupation
                                                  // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                  // $insertRow = false;
                                              }
                                              else
                                              {
                                                  $occId = $occ_details[0]->occupation_id;
                                              }
                                          }
                                          else
                                          {
                                              $occId=null;
                                              // $insertRow = false;
                                              // $impMessage = "Occupation cannot be empty";
                                          }
                                           $occ_name=$occ; //for bank details
                                      }

                                    if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
                                    {
                                    $cell=$rows[$findkey];
                                      if($cell || $cell != '')
                                          {
                                              $hof = trim($cell);

                                              if(strtoupper($hof) == "YES") {
                                                  $hof = 1;
                                              } else {
                                                  $hof = 0;
                                              }
                                          }
                                          else
                                          {
                                              $hof = 0;
                                          }
                                          //  echo $hof;exit;
                                      }

                                     if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $rHof = trim($cell);
                                              //echo $rHof;
                                          }
                                          else
                                          {
                                              $rHof = "";
                                          }

                                      }


                                      if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            //echo $cell;
                                          $cell=trim(str_replace('/','-', $cell));
                                      //  echo $cell; //exit; //date checked
                                         if($cell || $cell != '')
                                         {
                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                             //var_dump($cell);exit;
                                            // $date->format('Y-m-d');
                                             if(is_object($date)){
                                               $dob=$date->format('Y-m-d');
                                             }else{
                                               $date = new DateTime($cell);
                                                if(is_object($date)){
                                                  $dob=$date->format('Y-m-d');

                                                }else{

                                                        $insertRow = false;
                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                    }

                                                }

                                             } else
                                              {
                                                  $dob = null;
                                                  $dob_app = 0;
                                              }
                                               // echo $dob;//exit;
                                         }else
                                         {
                                             $dob = null;
                                             $dob_app = 0;
                                         }

                                      if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $flat = trim($cell);
                                          }
                                          else
                                          {
                                              $flat = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Street',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $street = trim($cell);
                                          }
                                          else
                                          {
                                              $street = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Area',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $area = trim($cell);
                                          }
                                          else
                                          {
                                              $area = "";
                                          }

                                      }
                                       if(!empty($findkey=array_search('City',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $city = trim($cell);
                                          }
                                          else
                                          {
                                            $city="";
                                              // $insertRow = false;
                                              // $impMessage = "City cannot be empty";
                                          }

                                      }

                                      if(!empty($findkey=array_search('State',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $state = trim($cell);
                                          }
                                          else
                                          {
                                              $state="";
                                              // $insertRow = false;
                                              // $impMessage = "State cannot be empty";
                                          }
                                      }

                                       if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $pin = trim($cell);
                                          }
                                          else
                                          {
                                              $pin = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $mobile = trim($cell);
                                          }
                                          else
                                          {
                                              $mobile = "";
                                          }

                                      }

                                      if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $tel = trim($cell);
                                          }
                                          else
                                          {
                                              $tel = "";
                                          }

                                      }

                                    if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $email = trim($cell);
                                          }
                                          else
                                          {
                                            $email="";
                                              // $insertRow = false;
                                              // $impMessage = "Email ID cannot be empty";
                                          }

                                      }

                                      if(!empty($findkey=array_search('Username',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $username = trim($cell);
                                              //check duplicate username
                                              //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                              $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                              $join = array(
                                                  'table' => 'families f',
                                                  'on' => 'c.family_id = f.family_id',
                                                  'type' => 'inner'
                                              );
                                              $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                              if($isUsernameDuplicate) {
                                                //  $insertRow = false; $impMessage = "Username already exists for another client";
                                               $username=date("Ymd").time().$username.rand(99,99999);
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Username cannot be empty";
                                          // }
                                      }else{ //if username is not in excel sheet create default username with client name and random number
                                        $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                      }


                                      if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != ''){
                                              $passport = trim($cell);
                                            }else{
                                              $passport = "";
                                            }
                                          //$broker_code=$passport; //for bank details
                                       }
                                       if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             $cell=trim(str_replace('/','-', $cell));
                                           if($cell || $cell != '')
                                           {
                                            $date = DateTime::createFromFormat('m-d-Y', $cell);
                                               //var_dump($cell);exit;
                                              // $date->format('Y-m-d');
                                               if(is_object($date)){
                                                 $comDate=$date->format('Y-m-d');
                                               }else{
                                                 $date = new DateTime($cell);
                                                  if(is_object($date)){
                                                    $comDate=$date->format('Y-m-d');

                                                  }else{

                                                          // $insertRow = false;
                                                          // $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                      }

                                                  }

                                               } else
                                                {
                                                    $comDate = null;
                                                    $dob_app = 0;
                                                }
                                                 // echo $dob;//exit;
                                           }else
                                           {
                                               $comDate = null;
                                               $dob_app = 0;
                                           }

                                      if(($findkey=array_search('Folio_Number',$dataColumns))==0)
                                        {
                                          $cell=$rows[$findkey];
                                          //echo $cell;
                                            if($cell || $cell != '')
                                            {
                                                $folio_number = stripcslashes(trim($cell));
                                                $folio_number = str_replace("'",'',$folio_number);

                                            }

                                       }else{

                                          //echo "inhellllll";
                                          $cell=$rows[$findkey];
                                          $folio_number = stripcslashes(trim($cell));
                                          $folio_number = str_replace("'",'',$folio_number);

                                        }
                                      if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                                $bankName = trim($cell);

                                            }
                                        }
                                        if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $bankBranch = trim($cell);

                                              }
                                          }
                                          if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $bank_account_types = trim($cell);

                                                }
                                            }
                                            if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                      $bankAccNo = trim($cell);

                                                  }
                                              }
                                              if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_address_building = trim($cell);

                                                    }
                                                }
                                                if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                          $bank_address_road = trim($cell);

                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                            $bank_address_area = trim($cell);

                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                              $bank_address_city = trim($cell);

                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                                $bank_pincode = trim($cell);

                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                  $bank_state = trim($cell);

                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                    $bank_country = trim($cell);

                                                                }
                                                            }
                                                          //for other details of bank
                                                          if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                            {
                                                              // $cell=$rows[$findkey];
                                                              //   if($cell || $cell != '')
                                                              //   {
                                                              //     $productId = trim($cell);
                                                              //
                                                              //   }else{
                                                              //     $productId="";
                                                              //   }
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $productId = trim($cell);
                                                                          $data=array();
                                                                          $data['broker_id']=$brokerID;
                                                                          if($productId){
                                                                           $data['productId']=$productId;
                                                                          }

                                                                         if($folio_number){
                                                                            $data['folio_number']=$folio_number;
                                                                         }
                                                                         if($clientName){
                                                                                $data['clientName']=$clientName;
                                                                         }
                                                                         if($pan_no){
                                                                           $data['pan_no']=$pan_no;
                                                                         }

                                                                          //check product scheme  already available
                                                                         $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                  //   print_r($scheme_info);exit;
                                                                         if(count($scheme_info) == 0)
                                                                         {
                                                                             //proceed normally
                                                                            // $productId=$productId;
                                                                            $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                         }
                                                                         else
                                                                         {
                                                                            $notFolio=1;
                                                                            //  $insertRow = false;
                                                                            //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                         }

                                                                  }

                                                             }

                                                            if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $jointName1 = trim($cell);

                                                                  }else{
                                                                    $jointName1="";
                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                      $jointName2 = trim($cell);

                                                                    }else{
                                                                      $jointName2="";
                                                                    }
                                                                }
                                                                if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $joint1_pan = trim($cell);

                                                                      }else{
                                                                        $joint1_pan="";
                                                                      }
                                                                  }
                                                                  if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $joint2_pan = trim($cell);

                                                                        }else{
                                                                          $joint2_pan="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $guard_pan = trim($cell);

                                                                          }else{
                                                                            $guard_pan="";
                                                                          }
                                                                      }
                                                                      if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $sub_boroker = trim($cell);

                                                                            }else{
                                                                              $sub_boroker="";
                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                $mode_holding = trim($cell);

                                                                              }else{
                                                                                $mode_holding="";
                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $nominee_name1 = trim($cell);

                                                                                }else{
                                                                                  $nominee_name1="";
                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                    $nom1_relation = trim($cell);

                                                                                  }else{
                                                                                    $nom1_relation ="";
                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                      $nominee_name2 = trim($cell);

                                                                                    }else{
                                                                                      $nominee_name2="";
                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                        $nom2_relation = trim($cell);

                                                                                      }else{
                                                                                        $nom2_relation ="";
                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                          $guardian_name= trim($cell);

                                                                                        }else{
                                                                                          $guardian_name="";
                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                        $cell=trim(str_replace('/','-', $cell));
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                            //var_dump($cell);exit;
                                                                                           // $date->format('Y-m-d');
                                                                                            if(is_object($date)){
                                                                                              $folioDate=$date->format('Y-m-d');
                                                                                            }else{
                                                                                              $date = new DateTime($cell);
                                                                                               if(is_object($date)){
                                                                                                 $folioDate=$date->format('Y-m-d');

                                                                                               }else{

                                                                                                       // $insertRow = false;
                                                                                                       // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                   }

                                                                                               }

                                                                                            } else
                                                                                             {
                                                                                                 $folioDate = null;
                                                                                                 $dob_app = 0;
                                                                                             }
                                                                                              // echo $dob;//exit;
                                                                                        }else
                                                                                        {
                                                                                            $folioDate = null;
                                                                                            $dob_app = 0;
                                                                                        }




                                  } else {


                                      if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Client Name cannot be empty";
                                          // }
                                      }

                                      if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Pan Number cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $folio_number = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Folio Number cannot be empty";
                                          // }
                                      }


                                  }
                                  $countCell++;
                                }elseif($rta_list=='nj_excel'){  // for NJ     @pallavi:2017-06-15
                                    // echo "NJ Excel";
                                    if($insertRow)
                                    {
                                  
                                       
                                        if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                        {
                                        $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $famName = trim($cell);
                            
                                             //checks if family exists in Families table
                                             $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                             if(count($f_info) == 0)
                                             {
                                                 //$insertRow = false;
                                                 //$impMessage = $famName." Family doesn't exist";
                                                 //family doesn't exist, so add a new family
                                                 $data = array(
                                                     'name' => $famName,
                                                     'status' => 1,
                                                     'broker_id' => $brokerID,
                                                     'user_id' => $user_id
                                                 );
                                                 $status = $this->Families_model->add($data);
                                                 if(isset($status['code'])) {
                                                     $insertRow = false;
                                                     $impMessage = "Could not add new Family for this client";
                                                 } else {
                                                     $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                     if(count($f_info) == 0)
                                                     {
                                                         $insertRow = false;
                                                         $impMessage = "Could not find Family for this client";
                                                     }
                                                     else
                                                     {
                                                         $familyId = $f_info[0]->family_id;
                                                     }
                                                 }
                                             }
                                             else
                                             {
                                                 $familyId = $f_info[0]->family_id;
                                             }
                                         }
                                         
                                        }else{ 

                                           $famName ="Default family"; //default family for non family client
                                           $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                           $familyId = $f_info[0]->family_id;
                                        }


                                   if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                     {
                                       $cell=$rows[$findkey];

                                         if($cell || $cell != '')
                                         {
                                             $clientName = trim($cell);
                                             $whereClient = array(
                                                 'c.name'=>$clientName,
                                                 'fam.name'=>$famName,
                                                 'fam.broker_id'=>$brokerID
                                             );
                                             //checks if client exists in Clients table
                                             $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                             if(count($c_info) == 0)
                                             {
                                                 $clientAlready=0;
                                                 $clientId_obj = $this->Clients_model->get_new_client_id();
                                                 $clientId = $clientId_obj->client_id;
                                             }
                                             else
                                             {
                                               $clientAlready=1;
                                             }
                                         }
                                         else
                                         {
                                             $insertRow = false;
                                             $impMessage = "Client Name cannot be empty";
                                         }
                                     }else{
                                       $insertRow = false;
                                       $impMessage = "Client Name cannot be empty";

                                     }


                                   if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                     {
                                       $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $panNum = trim($cell);
                                         //  echo $panNum;exit;
                                             $wherePan = array(
                                                 'c.pan_no'=>$panNum,
                                                 'f.broker_id'=>$brokerID
                                             );
                                             //checks if policy exists in policy details table
                                             $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                             if(count($p_info) == 0)
                                             {
                                                 //proceed normally
                                                 $panAlready=0;
                                             }
                                             else
                                             {
                                               $panAlready=1;
                                                 // $insertRow = false;
                                                 // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                             }
                                         }
                                         // else
                                         // {
                                         //
                                         //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                         //    $dummypan = $clientId_obj->client_id;
                                         //    //C20160001
                                         //
                                         //   $panNum =str_replace('C','P',$dummypan);
                                         // //  echo "dummy pan".$panNum;exit;
                                         // }
                                         $pan_no=$panNum; //for client Bank  details
                                     }else{
                                       $panNum=$pan_no="";

                                     }

                                 if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                   {
                                     $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $clientType = trim($cell);
                                             //  echo $clientType;
                                             $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                             $type_details = $this->Clients_model->get_client_types($whereCType);
                                             if(count($type_details) == 0)
                                             {
                                                 //$clientTypeId=40; //for others type of client Type
                                                 $defaultClientType='Resident Individual';
                                                   $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                               $ctype= $this->Clients_model->get_client_types($whereCType);
                                               $clientTypeId =  $ctype[0]->client_type_id;
                                                 // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                 // $insertRow = false;
                                             }
                                             else
                                             {
                                                 $clientTypeId = $type_details[0]->client_type_id;
                                             }
                                         }
                                         else
                                         {
                                           //  $clientTypeId=40; //for others type of client Type
                                             $defaultClientType='Resident Individual';
                                               $whereCType = 'client_type_name like "'.$defaultClientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                           $ctype= $this->Clients_model->get_client_types($whereCType);
                                           $clientTypeId =  $ctype[0]->client_type_id;
                                             // $insertRow = false;
                                             // $impMessage = "Client Type cannot be empty";
                                         }
                                           $tax_status=$clientType; //for client bank details
                                           //  echo   $clientTypeId;exit;
                                     }else{
                                       // $clientTypeId=40; //for  file not having client type col.
                                       //     $tax_status=""; //for client bank details
                                       //       $clientType=40;

                                             $defaultClientType='Resident Individual';
                                               $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                           $ctype= $this->Clients_model->get_client_types($whereCType);
                                           $clientTypeId =  $ctype[0]->client_type_id;

                                         //  echo   $clientTypeId;exit;
                                     }

                                    if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $occ = trim($cell);
                                           //  echo $occ;exit;
                                             $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                             $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                             if(count($occ_details) == 0)
                                             {
                                                 $occId=null; //for others type of occupation
                                                 // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                 // $insertRow = false;
                                             }
                                             else
                                             {
                                                 $occId = $occ_details[0]->occupation_id;
                                             }
                                         }
                                         else
                                         {
                                           $occId=null;
                                             // $insertRow = false;
                                             // $impMessage = "Occupation cannot be empty";
                                         }
                                          $occ_name=$occ; //for bank details
                                     }




                                     if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                        //   echo $cell;//exit;
                                        //  $cell=trim(str_replace('/','-', $cell));
                                     //  echo $cell; //exit; //date checked
                                        if($cell || $cell != '')
                                        {
                                              if($cell=='-'){
                                            $dob = null;
                                            $dob_app = 0;

                                          }else{
                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
                                            //var_dump($date);//exit;
                                        //   $date->format('Y-m-d');
                                            if(is_object($date)){

                                              $dob=$date->format('Y-m-d');
                                            }else{

                                              $date = new DateTime($cell);

                                               if(is_object($date)){
                                                 $dob=$date->format('Y-m-d');

                                               }else{

                                                       $insertRow = false;
                                                       $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                   }

                                               }
                                        }
                                            } else
                                             {
                                                 $dob = null;
                                                 $dob_app = 0;
                                             }
                                               //echo $dob;exit;
                                        }else
                                        {
                                            $dob = null;
                                            $dob_app = 0;
                                        }

                                     if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $flat = trim($cell);
                                         }
                                         else
                                         {
                                             $flat = "";
                                         }

                                     }

                                      if(!empty($findkey=array_search('City',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $city = trim($cell);
                                         }
                                         else
                                         {
                                           $city="";
                                             // $insertRow = false;
                                             // $impMessage = "City cannot be empty";
                                         }

                                     }

                                     if(!empty($findkey=array_search('State',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $state = trim($cell);
                                         }
                                         else
                                         {
                                             $state="";
                                             // $insertRow = false;
                                             // $impMessage = "State cannot be empty";
                                         }
                                     }

                                      if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $pin = trim($cell);
                                         }
                                         else
                                         {
                                             $pin = "";
                                         }

                                     }

                                      if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $mobile = trim($cell);
                                         }
                                         else
                                         {
                                             $mobile = "";
                                         }

                                     }

                                     if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $tel = trim($cell);
                                         }
                                         else
                                         {
                                             $tel = "";
                                         }

                                     }

                                   if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                     {
                                       $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $email = trim($cell);
                                         }
                                         else
                                         {
                                           $email="";
                                             // $insertRow = false;
                                             // $impMessage = "Email ID cannot be empty";
                                         }

                                     }

                                     if(!empty($findkey=array_search('Username',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell  || $cell != '')
                                         {
                                             $username = trim($cell);
                                             //check duplicate username
                                             //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                             $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                             $join = array(
                                                 'table' => 'families f',
                                                 'on' => 'c.family_id = f.family_id',
                                                 'type' => 'inner'
                                             );
                                             $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                             if($isUsernameDuplicate) {
                                               //  $insertRow = false; $impMessage = "Username already exists for another client";
                                              $username=date("Ymd").time().$username.rand(99,99999);
                                             }
                                         }
                                         // else
                                         // {
                                         //     $insertRow = false;
                                         //     $impMessage = "Username cannot be empty";
                                         // }
                                     }else{ //if username is not in excel sheet create default username with client name and random number
                                       $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                     }


                                     if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != ''){
                                             $passport = trim($cell);
                                           }else{
                                             $passport = "";
                                           }
                                         //$broker_code=$passport; //for bank details
                                      }

                                      if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            //$cell=trim(str_replace('/','-', $cell));
                                          if($cell || $cell != '')
                                          {
                                                if($cell=='-'){
                                            $dob = null;
                                            $dob_app = 0;

                                          }else{
                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                              //var_dump($cell);exit;
                                             // $date->format('Y-m-d');
                                              if(is_object($date)){
                                                $comDate=$date->format('Y-m-d');
                                              }else{
                                                $date = new DateTime($cell);
                                                 if(is_object($date)){
                                                   $comDate=$date->format('Y-m-d');

                                                 }else{

                                                         $insertRow = false;
                                                         $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                     }

                                                 }

                                          }
                                              } else
                                               {
                                                   $comDate = null;
                                                   $dob_app = 0;
                                               }
                                                // echo $dob;//exit;
                                          }else
                                          {
                                              $comDate = null;
                                              $dob_app = 0;
                                          }


                                     if(($findkey=array_search('Folio_Number',$dataColumns))==0)
                                       {
                                         $cell=$rows[$findkey];
                                         //echo $cell;
                                           if($cell || $cell != '')
                                           {
                                               $folio_number = stripcslashes(trim($cell));
                                               $folio_number = str_replace("'",'',$folio_number);

                                           }

                                      }else{

                                         //echo "inhellllll";
                                         $cell=$rows[$findkey];
                                         $folio_number = stripcslashes(trim($cell));
                                         $folio_number = str_replace("'",'',$folio_number);

                                       }
                                     if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                           {
                                               $bankName = trim($cell);

                                           }
                                       }
                                       if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                             if($cell || $cell != '')
                                             {
                                                 $bankBranch = trim($cell);

                                             }
                                         }
                                         if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $bank_account_types = trim($cell);

                                               }
                                           }
                                           if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 if($cell || $cell != '')
                                                 {
                                                     $bankAccNo = trim($cell);

                                                 }
                                             }

                                                 if(!empty($findkey=array_search('Bank_ifsc',$dataColumns)))
                                                   {
                                                     $cell=$rows[$findkey];
                                                       if($cell || $cell != '')
                                                       {
                                                           $bank_address_area = trim($cell);

                                                       }
                                                   }
                                                   if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                     {
                                                       $cell=$rows[$findkey];
                                                         if($cell || $cell != '')
                                                         {
                                                             $bank_address_city = trim($cell);

                                                         }
                                                     }

                                                         //for other details of bank
                                                         if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                           {
                                                             //echo"in scheme";exit;
                                                             // $cell=$rows[$findkey];
                                                             //   if($cell || $cell != '')
                                                             //   {
                                                             //     $productId = trim($cell);
                                                             //
                                                             //   }else{
                                                             //     $productId="";
                                                             //   }
                                                               $cell=$rows[$findkey];
                                                                 if($cell || $cell != '')
                                                                 {
                                                                   $productId = trim($cell);
                                                                         $data=array();
                                                                         $data['broker_id']=$brokerID;
                                                                         if($productId){
                                                                          $data['productId']=$productId;
                                                                         }

                                                                        if($folio_number){
                                                                           $data['folio_number']=$folio_number;
                                                                        }
                                                                        if($clientName){
                                                                               $data['clientName']=$clientName;
                                                                        }
                                                                        if($pan_no){
                                                                          $data['pan_no']=$pan_no;
                                                                        }

                                                                         //check product scheme  already available
                                                                        $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                 //   print_r($scheme_info);exit;
                                                                        if(count($scheme_info) == 0)
                                                                        {
                                                                            //proceed normally
                                                                           // $productId=$productId;
                                                                           $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                        }
                                                                        else
                                                                        {
                                                                           $notFolio=1;
                                                                           //  $insertRow = false;
                                                                           //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                        }

                                                                      }else{       //pallavi @:2017/06/21
                                                                        //  echo"inner else scheme";exit;
                                                                        //  $insertRow = false;
                                                                        //  $impMessage = "Product Id cannot be empty";

                                                                       }

                                                            }else{      //pallavi @:2017/06/21
                                                              //  echo"in else scheme";exit;
                                                              // $insertRow = false;
                                                              // $impMessage = "Product Id cannot be empty";

                                                             }

                                                           if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                             {
                                                               $cell=$rows[$findkey];
                                                                 if($cell || $cell != '')
                                                                 {
                                                                   $jointName1 = trim($cell);

                                                                 }else{
                                                                   $jointName1="";
                                                                 }
                                                             }
                                                             if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                               {
                                                                 $cell=$rows[$findkey];
                                                                   if($cell || $cell != '')
                                                                   {
                                                                     $jointName2 = trim($cell);

                                                                   }else{
                                                                     $jointName2="";
                                                                   }
                                                               }
                                                               if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                 {
                                                                   $cell=$rows[$findkey];
                                                                     if($cell || $cell != '')
                                                                     {
                                                                       $joint1_pan = trim($cell);

                                                                     }else{
                                                                       $joint1_pan="";
                                                                     }
                                                                 }
                                                                 if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                   {
                                                                     $cell=$rows[$findkey];
                                                                       if($cell || $cell != '')
                                                                       {
                                                                         $joint2_pan = trim($cell);

                                                                       }else{
                                                                         $joint2_pan="";
                                                                       }
                                                                   }
                                                                   if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                     {
                                                                       $cell=$rows[$findkey];
                                                                         if($cell || $cell != '')
                                                                         {
                                                                           $guard_pan = trim($cell);

                                                                         }else{
                                                                           $guard_pan="";
                                                                         }
                                                                     }

                                                                       if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                             if($cell || $cell != '')
                                                                             {
                                                                               $mode_holding = trim($cell);

                                                                             }else{
                                                                               $mode_holding="";
                                                                             }
                                                                         }
                                                                         if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                           {
                                                                             $cell=$rows[$findkey];
                                                                               if($cell || $cell != '')
                                                                               {
                                                                                 $nominee_name1 = trim($cell);

                                                                               }else{
                                                                                 $nominee_name1="";
                                                                               }
                                                                           }
                                                                           if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                                 if($cell || $cell != '')
                                                                                 {
                                                                                   $nom1_relation = trim($cell);

                                                                                 }else{
                                                                                   $nom1_relation ="";
                                                                                 }
                                                                             }
                                                                             if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                               {
                                                                                 $cell=$rows[$findkey];
                                                                                   if($cell || $cell != '')
                                                                                   {
                                                                                     $nominee_name2 = trim($cell);

                                                                                   }else{
                                                                                     $nominee_name2="";
                                                                                   }
                                                                               }
                                                                               if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                 {
                                                                                   $cell=$rows[$findkey];
                                                                                     if($cell || $cell != '')
                                                                                     {
                                                                                       $nom2_relation = trim($cell);

                                                                                     }else{
                                                                                       $nom2_relation ="";
                                                                                     }
                                                                                 }
                                                                                 if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                   {
                                                                                     $cell=$rows[$findkey];
                                                                                       if($cell || $cell != '')
                                                                                       {
                                                                                         $guardian_name= trim($cell);

                                                                                       }else{
                                                                                         $guardian_name="";
                                                                                       }
                                                                                   }
                                                                                   if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                     {
                                                                                       $cell=$rows[$findkey];
                                                                                       //$cell=trim(str_replace('/','-', $cell));
                                                                                       if($cell || $cell != '')
                                                                                       {
                                                                                              if($cell=='-'){
                                                                                                 $dob = null;
                                                                                                $dob_app = 0;

                                                                                          }else{      
                                                                                        $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                           //var_dump($cell);exit;
                                                                                          // $date->format('Y-m-d');
                                                                                           if(is_object($date)){
                                                                                             $folioDate=$date->format('Y-m-d');
                                                                                           }else{
                                                                                             $date = new DateTime($cell);
                                                                                              if(is_object($date)){
                                                                                                $folioDate=$date->format('Y-m-d');

                                                                                              }else{

                                                                                                      // $insertRow = false;
                                                                                                      // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                  }

                                                                                              }
                                                                                       }  

                                                                                           } else
                                                                                            {
                                                                                                $folioDate = null;
                                                                                                $dob_app = 0;
                                                                                            }
                                                                                             // echo $dob;//exit;
                                                                                       }else
                                                                                       {
                                                                                           $folioDate = null;
                                                                                           $dob_app = 0;
                                                                                       }

                                 } else {


                                     if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $clientName = trim($cell);
                                         }
                                         // else
                                         // {
                                         //     $insertRow = false;
                                         //     $impMessage = "Client Name cannot be empty";
                                         // }
                                     }

                                     if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $panNum = trim($cell);
                                         }
                                         
                                     }
                                     if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                         if($cell || $cell != '')
                                         {
                                             $folio_number = trim($cell);
                                         }
                                        
                                     }



                                 }
                                  if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                        {
                                            
                                            $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                                $schem_name = trim($cell);
                                                
                                                
                                                    //check product scheme  already available
                                                   $scheme_info = $this->Clients_model->get_scheme_by_scheme_name($schem_name);
                                                  if($scheme_info && $scheme_info->prod_code){
                                                   $productId=$scheme_info->prod_code;
                                                    }
                                                   //echo $productId;
                                                   //print_r($scheme_info);
                                                  //exit();
                                            }

                                        }
                                 $countCell++;

                                }elseif($rta_list=='sundaram_excel'){ //for Sundram RTA

                                                                  if($insertRow)
                                                                  {

                                                                    if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $famName = trim($cell);
                                                                        //  echo $famName;//exit;
                                                                              //checks if family exists in Families table
                                                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                              if(count($f_info) == 0)
                                                                              {
                                                                                  //$insertRow = false;
                                                                                  //$impMessage = $famName." Family doesn't exist";
                                                                                  //family doesn't exist, so add a new family
                                                                                  $data = array(
                                                                                      'name' => $famName,
                                                                                      'status' => 1,
                                                                                      'broker_id' => $brokerID,
                                                                                      'user_id' => $user_id
                                                                                  );
                                                                                  $status = $this->Families_model->add($data);
                                                                                  if(isset($status['code'])) {
                                                                                      $insertRow = false;
                                                                                      $impMessage = "Could not add new Family for this client";
                                                                                  } else {
                                                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                                      if(count($f_info) == 0)
                                                                                      {
                                                                                          $insertRow = false;
                                                                                          $impMessage = "Could not find Family for this client";
                                                                                      }
                                                                                      else
                                                                                      {
                                                                                          $familyId = $f_info[0]->family_id;
                                                                                      }
                                                                                  }
                                                                              }
                                                                              else
                                                                              {
                                                                                  $familyId = $f_info[0]->family_id;
                                                                              }
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //     $insertRow = false;
                                                                          //     $impMessage = "Family Name cannot be empty";
                                                                          // }
                                                                      }else{ // If family name is not found in excel then drop the client in default family

                                                                            $famName ="Default family"; //default family for non family client
                                                                            $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                            $familyId = $f_info[0]->family_id;
                                                                      }

                                                                    if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];

                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $clientName = trim($cell);
                                                                          //  echo $clientName; exit;
                                                                              $whereClient = array(
                                                                                  'c.name'=>$clientName,
                                                                                  'fam.name'=>$famName,
                                                                                  'fam.broker_id'=>$brokerID
                                                                              );
                                                                              //checks if client exists in Clients table
                                                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                                              if(count($c_info) == 0)
                                                                              {
                                                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                                                  $clientId = $clientId_obj->client_id;
                                                                              }
                                                                              else
                                                                              {
                                                                                $clientAlready=1;
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                                              }
                                                                          }
                                                                          else
                                                                          {
                                                                              $insertRow = false;
                                                                              $impMessage = "Client Name cannot be empty";
                                                                          }
                                                                      }
                                                                    if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $panNum = trim($cell);
                                                                        // echo $panNum;exit;
                                                                              $wherePan = array(
                                                                                  'c.pan_no'=>$panNum,
                                                                                  'f.broker_id'=>$brokerID
                                                                              );
                                                                              //checks if policy exists in policy details table
                                                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                                                              if(count($p_info) == 0)
                                                                              {
                                                                                  //proceed normally
                                                                              }
                                                                              else
                                                                              {
                                                                                $panAlready=1;
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";
                                                                                  //$clientId = $c_info->client_id;
                                                                                  //$clientName = $c_info->client_name;
                                                                                  //$familyId = $c_info->family_id;
                                                                              }
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //
                                                                          //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                                                          //    $dummypan = $clientId_obj->client_id;
                                                                          //    //C20160001
                                                                          //
                                                                          //   $panNum =str_replace('C','P',$dummypan);
                                                                          //   $pan_no=$panNum;//for client's bank details
                                                                          // //  echo "dummy pan".$panNum;exit;
                                                                          // }
                                                                        $pan_no=$panNum;//for client's bank details
                                                                      }
                                                                  if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $clientType = trim($cell);
                                                                              // echo $clientType;exit;
                                                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                                                              if(count($type_details) == 0)
                                                                              {
                                                                                //  $clientTypeId=40; //for others type of client Type
                                                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                                                  // $insertRow = false;
                                                                                  $defaultClientType='Resident Individual';
                                                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                                $clientTypeId =  $ctype[0]->client_type_id;
                                                                              }
                                                                              else
                                                                              {
                                                                                  $clientTypeId = $type_details[0]->client_type_id;
                                                                              }

                                                                          $tax_status=$clientType; //for clients bank details
                                                                          }
                                                                          else
                                                                          {
                                                                            //  $clientTypeId=40; //for others type of client Type
                                                                              $tax_status=""; //for clients bank details
                                                                              // $insertRow = false;
                                                                              // $impMessage = "Client Type cannot be empty";
                                                                              $defaultClientType='Resident Individual';
                                                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                            $clientTypeId =  $ctype[0]->client_type_id;
                                                                          }
                                                                      }else{
                                                                        // $clientTypeId=40; //for  file not having client type col.
                                                                        //   $tax_status=""; //for clients bank details
                                                                        //     $clientType=40;
                                                                            $defaultClientType='Resident Individual';
                                                                              $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                          $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                          $clientTypeId =  $ctype[0]->client_type_id;
                                                                          $clientType=$clientTypeId;

                                                                      }
                                                                     if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                                                       {
                                                                         $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $occ = trim($cell);
                                                                            // echo $occ;exit;
                                                                              $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                              $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                                              if(count($occ_details) == 0)
                                                                              {
                                                                                  $occId=null; //for others type of occupation
                                                                                  // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                                                  // $insertRow = false;
                                                                              }
                                                                              else
                                                                              {
                                                                                  $occId = $occ_details[0]->occupation_id;
                                                                              }
                                                                          }
                                                                          else
                                                                          {
                                                                              $occId=null;
                                                                              // $insertRow = false;
                                                                              // $impMessage = "Occupation cannot be empty";
                                                                          }
                                                                          $occ_name=$occ;//for client bank details
                                                                      }

                                                                      if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            //echo $cell;
                                                                         //  $cell=trim(str_replace('/','-', $cell));
                                                                      //  echo $cell; //exit; //date checked
                                                                         if($cell || $cell != '')
                                                                         {
                                                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                             //var_dump($cell);exit;
                                                                          //   $date->format('Y-m-d');
                                                                             if(is_object($date)){
                                                                               $dob=$date->format('Y-m-d');
                                                                             }else{
                                                                               $date = new DateTime($cell);
                                                                                if(is_object($date)){
                                                                                  $dob=$date->format('Y-m-d');
                                                                                }else{

                                                                                        $insertRow = false;
                                                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                                                    }

                                                                                }

                                                                             } else
                                                                              {
                                                                                  $dob = null;
                                                                                  $dob_app = 0;
                                                                              }

                                                                         }else
                                                                         {
                                                                             $dob = null;
                                                                             $dob_app = 0;
                                                                         }

                                                                      if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $flat = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $flat = "";
                                                                          }

                                                                      }

                                                                       if(!empty($findkey=array_search('Street',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $street = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $street = "";
                                                                          }

                                                                      }
                                                                       if(!empty($findkey=array_search('Area',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $area = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $area = "";
                                                                          }

                                                                      }

                                                                       if(!empty($findkey=array_search('City',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $city = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $insertRow = false;
                                                                              $impMessage = "City cannot be empty";
                                                                          }

                                                                      }

                                                                      if(!empty($findkey=array_search('State',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $state = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $state="";
                                                                              // $insertRow = false;
                                                                              // $impMessage = "State cannot be empty";
                                                                          }
                                                                      }

                                                                       if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $pin = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $pin = "";
                                                                          }

                                                                      }

                                                                       if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                                                         {
                                                                           $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $mobile = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $mobile = "";
                                                                          }

                                                                      }
                                                                      if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $tel = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                              $tel = "";
                                                                          }

                                                                      }

                                                                    if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $email = trim($cell);
                                                                          }
                                                                          else
                                                                          {
                                                                            $email="";
                                                                              // $insertRow = false;
                                                                              // $impMessage = "Email ID cannot be empty";
                                                                          }

                                                                      }

                                                                      if(!empty($findkey=array_search('Username',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell  || $cell != '')
                                                                          {
                                                                              $username = trim($cell);
                                                                              //check duplicate username
                                                                              //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                                              $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                                              $join = array(
                                                                                  'table' => 'families f',
                                                                                  'on' => 'c.family_id = f.family_id',
                                                                                  'type' => 'inner'
                                                                              );
                                                                              $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                                              if($isUsernameDuplicate) {
                                                                                //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                                               $username=date("Ymd").time().$username.rand(99,99999);
                                                                              }
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //     $insertRow = false;
                                                                          //     $impMessage = "Username cannot be empty";
                                                                          // }
                                                                      }else{ //if username is not in excel sheet create default username with client name and random number
                                                                     $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);


                                                                      }

                                                                      if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != ''){
                                                                              $passport = trim($cell);
                                                                            }
                                                                          else{
                                                                              $passport = "";
                                                                            }
                                                                          //  $broker_code=$passport;//for client bank details
                                                                      }
                                                                      if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            $cell=trim(str_replace('/','-', $cell));
                                                                          if($cell || $cell != '')
                                                                          {
                                                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                              //var_dump($cell);exit;
                                                                             // $date->format('Y-m-d');
                                                                              if(is_object($date)){
                                                                                $comDate=$date->format('Y-m-d');
                                                                              }else{
                                                                                $date = new DateTime($cell);
                                                                                 if(is_object($date)){
                                                                                   $comDate=$date->format('Y-m-d');

                                                                                 }else{

                                                                                        //  $insertRow = false;
                                                                                        //  $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                                                     }

                                                                                 }

                                                                              } else
                                                                               {
                                                                                   $comDate = null;
                                                                                   $dob_app = 0;
                                                                               }
                                                                                // echo $dob;//exit;
                                                                          }else
                                                                          {
                                                                              $comDate = null;
                                                                              $dob_app = 0;
                                                                          }



                                                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $folio_number = trim($cell);
                                                                             $folio_number = stripcslashes(trim($cell));
                                                                             $folio_number = str_replace("'",'',$folio_number);
                                                                            }

                                                                        }elseif($findkey==0){
                                                                          $cell=$rows[$findkey];
                                                                          $folio_number = trim($cell);
                                                                         $folio_number = stripcslashes(trim($cell));
                                                                        $folio_number = str_replace("'",'',$folio_number);
                                                                      //  echo $folio_number;exit;
                                                                        }
                                                                      if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];

                                                                            if($cell || $cell != '')
                                                                            {
                                                                                $bankName = trim($cell);

                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $bankBranch = trim($cell);

                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                    $bank_account_types = trim($cell);

                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                      $bankAccNo = trim($cell);

                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                        $bank_address_building = trim($cell);

                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                          $bank_address_road = trim($cell);

                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                            $bank_address_area = trim($cell);

                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                              $bank_address_city = trim($cell);

                                                                                          }
                                                                                      }
                                                                                      if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                                                        {
                                                                                          $cell=$rows[$findkey];
                                                                                            if($cell || $cell != '')
                                                                                            {
                                                                                                $bank_pincode = trim($cell);

                                                                                            }
                                                                                        }
                                                                                        if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                                                          {
                                                                                            $cell=$rows[$findkey];
                                                                                              if($cell || $cell != '')
                                                                                              {
                                                                                                  $bank_state = trim($cell);

                                                                                              }
                                                                                          }
                                                                                          if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                                                            {
                                                                                              $cell=$rows[$findkey];
                                                                                                if($cell || $cell != '')
                                                                                                {
                                                                                                    $bank_country = trim($cell);

                                                                                                }
                                                                                            }
                                                                                            //for other details of bank
                                                                                            if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                                                              {
                                                                                                // $cell=$rows[$findkey];
                                                                                                //   if($cell || $cell != '')
                                                                                                //   {
                                                                                                //     $productId = trim($cell);
                                                                                                //
                                                                                                //   }else{
                                                                                                //     $productId="";
                                                                                                //   }
                                                                                                $cell=$rows[$findkey];
                                                                                                  if($cell || $cell != '')
                                                                                                  {
                                                                                                    $productId = trim($cell);
                                                                                                          $data=array();
                                                                                                          if($productId){
                                                                                                           $data['productId']=$productId;
                                                                                                          }
                                                                                                         $data['broker_id']=$brokerID;
                                                                                                         if($folio_number){
                                                                                                            $data['folio_number']=$folio_number;
                                                                                                         }
                                                                                                         if($clientName){
                                                                                                                $data['clientName']=$clientName;
                                                                                                         }

                                                                                                         if($pan_no){
                                                                                                           $data['pan_no']=$pan_no;
                                                                                                         }

                                                                                                          //check product scheme  already available
                                                                                                         $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                                                  //   print_r($scheme_info);exit;
                                                                                                         if(count($scheme_info) == 0)
                                                                                                         {
                                                                                                             //proceed normally
                                                                                                            // $productId=$productId;
                                                                                                            $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                                                         }
                                                                                                         else
                                                                                                         {
                                                                                                            $notFolio=1;
                                                                                                            //  $insertRow = false;
                                                                                                            //    $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                                                         }

                                                                                                  }
                                                                                              }
                                                                                              if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                                                                {
                                                                                                  $cell=$rows[$findkey];
                                                                                                    if($cell || $cell != '')
                                                                                                    {
                                                                                                      $jointName1 = trim($cell);

                                                                                                    }else{
                                                                                                      $jointName1="";
                                                                                                    }
                                                                                                }
                                                                                                if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                                                  {
                                                                                                    $cell=$rows[$findkey];
                                                                                                      if($cell || $cell != '')
                                                                                                      {
                                                                                                        $jointName2 = trim($cell);

                                                                                                      }else{
                                                                                                        $jointName2="";
                                                                                                      }
                                                                                                  }
                                                                                                  if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                                                    {
                                                                                                      $cell=$rows[$findkey];
                                                                                                        if($cell || $cell != '')
                                                                                                        {
                                                                                                          $joint1_pan = trim($cell);

                                                                                                        }else{
                                                                                                          $joint1_pan="";
                                                                                                        }
                                                                                                    }
                                                                                                    if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                                                      {
                                                                                                        $cell=$rows[$findkey];
                                                                                                          if($cell || $cell != '')
                                                                                                          {
                                                                                                            $joint2_pan = trim($cell);

                                                                                                          }else{
                                                                                                            $joint2_pan="";
                                                                                                          }
                                                                                                      }
                                                                                                      if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                                                        {
                                                                                                          $cell=$rows[$findkey];
                                                                                                            if($cell || $cell != '')
                                                                                                            {
                                                                                                              $guard_pan = trim($cell);

                                                                                                            }else{
                                                                                                              $guard_pan="";
                                                                                                            }
                                                                                                        }
                                                                                                        if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                                                          {
                                                                                                            $cell=$rows[$findkey];
                                                                                                              if($cell || $cell != '')
                                                                                                              {
                                                                                                                $sub_boroker = trim($cell);

                                                                                                              }else{
                                                                                                                $sub_boroker="";
                                                                                                              }
                                                                                                          }
                                                                                                          if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                                                            {
                                                                                                              $cell=$rows[$findkey];
                                                                                                                if($cell || $cell != '')
                                                                                                                {
                                                                                                                  $mode_holding = trim($cell);

                                                                                                                }else{
                                                                                                                  $mode_holding="";
                                                                                                                }
                                                                                                            }
                                                                                                            if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                                                              {
                                                                                                                $cell=$rows[$findkey];
                                                                                                                  if($cell || $cell != '')
                                                                                                                  {
                                                                                                                    $nominee_name1 = trim($cell);

                                                                                                                  }else{
                                                                                                                    $nominee_name1="";
                                                                                                                  }
                                                                                                              }
                                                                                                              if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                                                                {
                                                                                                                  $cell=$rows[$findkey];
                                                                                                                    if($cell || $cell != '')
                                                                                                                    {
                                                                                                                      $nom1_relation = trim($cell);

                                                                                                                    }else{
                                                                                                                      $nom1_relation ="";
                                                                                                                    }
                                                                                                                }
                                                                                                                if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                                                  {
                                                                                                                    $cell=$rows[$findkey];
                                                                                                                      if($cell || $cell != '')
                                                                                                                      {
                                                                                                                        $nominee_name2 = trim($cell);

                                                                                                                      }else{
                                                                                                                        $nominee_name2="";
                                                                                                                      }
                                                                                                                  }
                                                                                                                  if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                                                    {
                                                                                                                      $cell=$rows[$findkey];
                                                                                                                        if($cell || $cell != '')
                                                                                                                        {
                                                                                                                          $nom2_relation = trim($cell);

                                                                                                                        }else{
                                                                                                                          $nom2_relation ="";
                                                                                                                        }
                                                                                                                    }
                                                                                                                    if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                                                      {
                                                                                                                        $cell=$rows[$findkey];
                                                                                                                          if($cell || $cell != '')
                                                                                                                          {
                                                                                                                            $guardian_name= trim($cell);

                                                                                                                          }else{
                                                                                                                            $guardian_name="";
                                                                                                                          }
                                                                                                                      }
                                                                                                                      if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                                                        {
                                                                                                                          $cell=$rows[$findkey];
                                                                                                                          $cell=trim(str_replace('/','-', $cell));
                                                                                                                          if($cell || $cell != '')
                                                                                                                          {
                                                                                                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                                                              //var_dump($cell);exit;
                                                                                                                             // $date->format('Y-m-d');
                                                                                                                              if(is_object($date)){
                                                                                                                                $folioDate=$date->format('Y-m-d');
                                                                                                                              }else{
                                                                                                                                $date = new DateTime($cell);
                                                                                                                                 if(is_object($date)){
                                                                                                                                   $folioDate=$date->format('Y-m-d');

                                                                                                                                 }else{

                                                                                                                                         // $insertRow = false;
                                                                                                                                         // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                                                     }

                                                                                                                                 }

                                                                                                                              } else
                                                                                                                               {
                                                                                                                                   $folioDate = null;
                                                                                                                                   $dob_app = 0;
                                                                                                                               }
                                                                                                                                // echo $dob;//exit;
                                                                                                                          }else
                                                                                                                          {
                                                                                                                              $folioDate = null;
                                                                                                                              $dob_app = 0;
                                                                                                                          }


                                                                  } else {

                                                                      if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $clientName = trim($cell);
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //     $insertRow = false;
                                                                          //     $impMessage = "Client Name cannot be empty";
                                                                          // }
                                                                      }
                                                                      if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $panNum = trim($cell);
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //     $insertRow = false;
                                                                          //     $impMessage = "Pan Number cannot be empty";
                                                                          // }
                                                                      }
                                                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                              $folio_number = trim($cell);
                                                                          }
                                                                          // else
                                                                          // {
                                                                          //     $insertRow = false;
                                                                          //     $impMessage = "Folio Number cannot be empty";
                                                                          // }
                                                                      }

                                                                  }
                                                                  $countCell++;


                                }elseif($rta_list=='frank_excel'){   //for Franklin RTA
                                    unset($fpart,$folio_number,$productId);
                                    if($insertRow)
                                    {

                                    // print_r($dataColumns[$countCell]);//exit;

                                    if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $famName = trim($cell);

                                              //checks if family exists in Families table
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              if(count($f_info) == 0)
                                              {
                                                  $data = array(
                                                      'name' => $famName,
                                                      'status' => 1,
                                                      'broker_id' => $brokerID,
                                                      'user_id' => $user_id
                                                  );
                                                  $status = $this->Families_model->add($data);
                                                  if(isset($status['code'])) {
                                                      $insertRow = false;
                                                      $impMessage = "Could not add new Family for this client";
                                                  } else {
                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                      if(count($f_info) == 0)
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Could not find Family for this client";
                                                      }
                                                      else
                                                      {
                                                          $familyId = $f_info[0]->family_id;
                                                      }
                                                  }
                                              }
                                              else
                                              {
                                                  $familyId = $f_info[0]->family_id;
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Family Name cannot be empty";
                                          // }
                                        }else{ // If family name is not found in excel then drop the client in default family

                                              $famName ="Default family"; //default family for non family client
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              $familyId = $f_info[0]->family_id;
                                        }


                                    if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];

                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          //  echo $clientName; //exit;
                                              $whereClient = array(
                                                  'c.name'=>$clientName,
                                                  'fam.name'=>$famName,
                                                  'fam.broker_id'=>$brokerID
                                              );
                                              //checks if client exists in Clients table
                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                              if(count($c_info) == 0)
                                              {
                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                  $clientId = $clientId_obj->client_id;
                                              }
                                              else
                                              {
                                                $clientAlready=1;
                                                //   echo $clientAlready;
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";
                                          }
                                    }else
                                    {
                                        $insertRow = false;
                                        $impMessage = "Client Name cannot be empty";
                                    }

                                    if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          //  echo $panNum;exit;
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if policy exists in policy details table
                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($p_info) == 0)
                                              {
                                                  //proceed normally
                                              }
                                              else
                                              {
                                                $panAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                              }
                                                $pan_no=$panNum; //for client bank's details
                                          }
                                          // else
                                          // {
                                          //
                                          //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                          //    $dummypan = $clientId_obj->client_id;
                                          //    //C20160001
                                          //
                                          //   $panNum =str_replace('C','P',$dummypan);
                                          //
                                          // //  echo "dummy pan".$panNum;exit;
                                          // }

                                      }

                                  if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                    {
                                      $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $clientType = trim($cell);
                                              //  echo $clientType;
                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                              if(count($type_details) == 0)
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                  // $insertRow = false;
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                              else
                                              {
                                                  $clientTypeId = $type_details[0]->client_type_id;
                                              }
                                             $tax_status=$clientType;//for clients bank details

                                          }
                                          else
                                          {
                                              //$clientTypeId=40; //for others type of client Type
                                               $tax_status="";
                                              // $insertRow = false;
                                              // $impMessage = "Client Type cannot be empty";
                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;
                                          }

                                      }else{

                                          $defaultClientType='Resident Individual';
                                            $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                        $ctype= $this->Clients_model->get_client_types($whereCType);
                                        $clientTypeId =  $ctype[0]->client_type_id;
                                          $clientType=  $clientTypeId;//for  file not having client type col.
                                      }

                                     if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $occ = trim($cell);
                                            //  echo $occ;exit;
                                              $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                              if(count($occ_details) == 0)
                                              {
                                                  $occId=null; //for others type of occupation
                                                  // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                  // $insertRow = false;
                                              }
                                              else
                                              {
                                                  $occId = $occ_details[0]->occupation_id;
                                              }
                                          }
                                          else
                                          {
                                              $occId=null;
                                              // $insertRow = false;
                                              // $impMessage = "Occupation cannot be empty";
                                          }
                                           $occ_name=$occ; //for client's bank details
                                      }


                                      if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            //echo $cell;
                                          // $cell=trim(str_replace('/','-', $cell));
                                      //  echo $cell; //exit; //date checked
                                         if($cell || $cell != '')
                                         {
                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                             //var_dump($cell);exit;
                                            // $date->format('Y-m-d');
                                             if(is_object($date)){
                                               $dob=$date->format('Y-m-d');
                                             }else{
                                               $date = new DateTime($cell);
                                                if(is_object($date)){
                                                  $dob=$date->format('Y-m-d');
                                                }else{

                                                        $insertRow = false;
                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                    }

                                                }

                                             } else
                                              {
                                                  $dob = null;
                                                  $dob_app = 0;
                                              }

                                         }else
                                         {
                                             $dob = null;
                                             $dob_app = 0;
                                         }



                                      if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $flat = trim($cell);
                                          }
                                          else
                                          {
                                              $flat = "";
                                          }


                                      }

                                       if(!empty($findkey=array_search('Street',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $street = trim($cell);
                                          }
                                          else
                                          {
                                              $street = "";
                                          }


                                      }

                                       if(!empty($findkey=array_search('Area',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $area = trim($cell);
                                          }
                                          else
                                          {
                                              $area = "";
                                          }


                                      }

                                       if(!empty($findkey=array_search('City',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $city = trim($cell);
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "City cannot be empty";
                                          }


                                      }

                                      if(!empty($findkey=array_search('State',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $state = trim($cell);
                                          }
                                          else
                                          {
                                            $state ='';
                                              // $insertRow = false;
                                              // $impMessage = "State cannot be empty";
                                          }

                                      }
                                       if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $pin = trim($cell);
                                          }
                                          else
                                          {
                                              $pin = "";
                                          }


                                      }
                                       //if(strtoupper($dataColumns[45]) === 'MOBILE')
                                      //  if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                      //    {
                                      //      $cell=$rows[$findkey];
                                      //     if($cell  || $cell != '')
                                      //     {
                                      //         $mobile = trim($cell);
                                      //     }
                                      //     else
                                      //     {
                                      //         $mobile = "";
                                      //     }
                                      //
                                      // }

                                      if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $tel = trim($cell);
                                          }
                                          else
                                          {
                                              $tel = "";
                                          }


                                      }
                                    if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $email = trim($cell);
                                          }
                                          else
                                          {
                                            $email="";
                                              // $insertRow = false;
                                              // $impMessage = "Email ID cannot be empty";
                                          }


                                      }

                                      if(!empty($findkey=array_search('Username',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $username = trim($cell);
                                              //check duplicate username
                                              //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                              $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                              $join = array(
                                                  'table' => 'families f',
                                                  'on' => 'c.family_id = f.family_id',
                                                  'type' => 'inner'
                                              );
                                              $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                              if($isUsernameDuplicate) {
                                                //  $insertRow = false; $impMessage = "Username already exists for another client";
                                               $username=date("Ymd").time().$username.rand(99,99999);
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Username cannot be empty";
                                          // }
                                      }else{ //if username is not in excel sheet create default username with client name and random number
                                       $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                      }

                                      if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != ''){
                                              $passport = trim($cell);
                                            }
                                          else{
                                            $passport = "";
                                          }
                                        //  $broker_code=$passport; //for clients bank details

                                      }

                                      if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            $cell=trim(str_replace('/','-', $cell));
                                          if($cell || $cell != '')
                                          {
                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                              //var_dump($cell);exit;
                                             // $date->format('Y-m-d');
                                              if(is_object($date)){
                                                $comDate=$date->format('Y-m-d');
                                              }else{
                                                $date = new DateTime($cell);
                                                 if(is_object($date)){
                                                   $comDate=$date->format('Y-m-d');

                                                 }else{

                                                        //  $insertRow = false;
                                                        //  $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                     }

                                                 }

                                              } else
                                               {
                                                   $comDate = null;
                                                   $dob_app = 0;
                                               }
                                                // echo $dob;//exit;
                                          }else
                                          {
                                              $comDate = null;
                                              $dob_app = 0;
                                          }

                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                               $folio_number = trim($cell);
                                              $folio_number = stripcslashes(trim($cell));
                                              $folio_number = str_replace("'",'',$folio_number);

                                            }
                                        }elseif($findkey==0){
                                          $cell=$rows[$findkey];
                                          $folio_number = stripcslashes(trim($cell));
                                          $folio_number = str_replace("'",'',$folio_number);

                                        }
                                      if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                                $bankName = trim($cell);

                                            }
                                        }
                                        if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $bankBranch = trim($cell);

                                              }
                                          }
                                          if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $bank_account_types = trim($cell);

                                                }
                                            }
                                            if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                      $bankAccNo = trim($cell);

                                                  }
                                              }
                                              if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_address_building = trim($cell);

                                                    }
                                                }
                                                if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                          $bank_address_road = trim($cell);

                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                            $bank_address_area = trim($cell);

                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                              $bank_address_city = trim($cell);

                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                                $bank_pincode = trim($cell);

                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                  $bank_state = trim($cell);

                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                    $bank_country = trim($cell);

                                                                }
                                                            }
                                                            //for other details of bank
                                                            if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                              {

                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $productId = trim($cell);
                                                                          $data=array();
                                                                          if($productId){
                                                                           $data['productId']=$productId;
                                                                          }
                                                                         $data['broker_id']=$brokerID;
                                                                         if($folio_number){
                                                                            $data['folio_number']=$folio_number;
                                                                         }
                                                                         if($clientName){
                                                                                $data['clientName']=$clientName;
                                                                         }

                                                                         if($pan_no){
                                                                           $data['pan_no']=$pan_no;
                                                                         }

                                                                          //check product scheme  already available
                                                                         $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                  //   print_r($scheme_info);exit;
                                                                         if(count($scheme_info) == 0)
                                                                         {
                                                                             //proceed normally
                                                                            // $productId=$productId;
                                                                            $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                         }
                                                                         else
                                                                         {
                                                                            $notFolio=1;
                                                                            //  $insertRow = false;
                                                                            //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                         }

                                                                  }

                                                                }else{
                                                                    //if product Id is not available-->specially for franklin file
                                                                    /*code to get productId from folionumber for frankilin file*/
                                                                  //  echo $folio_number;
                                                                    $fpart=substr($folio_number,0, 3);
                                                                  // echo $fpart;
                                                                   $productId_fromScheme=$this->Clients_model->get_product_code_from_mf_scheme($fpart);
                                                                  //  print_r($productId_fromScheme);
                                                                     if(count($productId_fromScheme)==0){
                                                                       //if at first level for one leading zero product id is blank then check for two leading zip_entry_close
                                                                       $folio_number='0'.$folio_number;
                                                                       $fpart=substr($folio_number,0, 3);
                                                                        $productId_fromScheme=$this->Clients_model->get_product_code_from_mf_scheme($fpart);
                                                                        if(count($productId_fromScheme)==0){
                                                                          $productId=" ";
                                                                        }else{
                                                                        $productId=$productId_fromScheme[0]['prod_code'];
                                                                        }
                                                                     }else{
                                                                       $productId=$productId_fromScheme[0]['prod_code'];
                                                                     }
                                                                    //   echo "product Id=".$productId;    exit;
                                                                    $data=array();
                                                                    if($productId){
                                                                     $data['productId']=$productId;
                                                                    }
                                                                   $data['broker_id']=$brokerID;
                                                                   if($folio_number){
                                                                      $data['folio_number']=$folio_number;
                                                                   }
                                                                   if($clientName){
                                                                          $data['clientName']=$clientName;
                                                                   }

                                                                   if($pan_no){
                                                                     $data['pan_no']=$pan_no;
                                                                   }

                                                                    //check product scheme  already available
                                                                   $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                            //   print_r($scheme_info);exit;
                                                                   if(count($scheme_info) == 0)
                                                                   {
                                                                       //proceed normally
                                                                      // $productId=$productId;
                                                                      $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                   }
                                                                   else
                                                                   {
                                                                      $notFolio=1;
                                                                      //  $insertRow = false;
                                                                      //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                   }


                                                                }
                                                              if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                      $jointName1 = trim($cell);

                                                                    }else{
                                                                      $jointName1="";
                                                                    }
                                                                }
                                                                if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $jointName2 = trim($cell);

                                                                      }else{
                                                                        $jointName2="";
                                                                      }
                                                                  }
                                                                  if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $joint1_pan = trim($cell);

                                                                        }else{
                                                                          $joint1_pan="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $joint2_pan = trim($cell);

                                                                          }else{
                                                                            $joint2_pan="";
                                                                          }
                                                                      }
                                                                      if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $guard_pan = trim($cell);

                                                                            }else{
                                                                              $guard_pan="";
                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                $sub_boroker = trim($cell);

                                                                              }else{
                                                                                $sub_boroker="";
                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $mode_holding = trim($cell);

                                                                                }else{
                                                                                  $mode_holding="";
                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                    $nominee_name1 = trim($cell);

                                                                                  }else{
                                                                                    $nominee_name1="";
                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                      $nom1_relation = trim($cell);

                                                                                    }else{
                                                                                      $nom1_relation ="";
                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                        $nominee_name2 = trim($cell);

                                                                                      }else{
                                                                                        $nominee_name2="";
                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                          $nom2_relation = trim($cell);

                                                                                        }else{
                                                                                          $nom2_relation ="";
                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                            $guardian_name= trim($cell);

                                                                                          }else{
                                                                                            $guardian_name="";
                                                                                          }
                                                                                      }
                                                                                      if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                        {
                                                                                          $cell=$rows[$findkey];
                                                                                          $cell=trim(str_replace('/','-', $cell));
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                           $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                              //var_dump($cell);exit;
                                                                                             // $date->format('Y-m-d');
                                                                                              if(is_object($date)){
                                                                                                $folioDate=$date->format('Y-m-d');
                                                                                              }else{
                                                                                                $date = new DateTime($cell);
                                                                                                 if(is_object($date)){
                                                                                                   $folioDate=$date->format('Y-m-d');

                                                                                                 }else{

                                                                                                         // $insertRow = false;
                                                                                                         // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                     }

                                                                                                 }

                                                                                              } else
                                                                                               {
                                                                                                   $folioDate = null;
                                                                                                   $dob_app = 0;
                                                                                               }
                                                                                                // echo $dob;//exit;
                                                                                          }else
                                                                                          {
                                                                                              $folioDate = null;
                                                                                              $dob_app = 0;
                                                                                          }

                                  } else {

                                      if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Client Name cannot be empty";
                                          // }
                                      }

                                      if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Pan Number cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $folio_number = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Folio Number cannot be empty";
                                          // }
                                      }
                                  }
                                  $countCell++;

                                }
                              }
                            }
                          }
                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $famName;
                                  $imp_data[$countErrRow][2] = $clientName;
                                  $imp_data[$countErrRow][3] = $panNum;
                                  $imp_data[$countErrRow][4] = $impMessage;
                                  $countErrRow++;
                                  $insertRow = true;
                                  $uploadedStatus = 2;
                                  continue;
                              }

                              $dataRows[$countClient] = array(
                                  'name' => $clientName, 'family_id' => $familyId,
                                  'email_id' => $email, 'dob' => $dob, 'dob_app' => $dob_app,
                                  //'password' => sha1($password),
                                  'occupation_id' => $occId, 'head_of_family' => $hof, 'relation_HOF' => $rHof,
                                  'client_type' => $clientTypeId, 'spouse_name' => $spouse,
                                  'anv_date' => $ann, 'anv_app' => $anv_app, 'pan_no' => $panNum,
                                  'passport_no' => $passport, 'add_flat' => $flat, 'add_street' => $street,
                                  'add_area' => $area, 'add_city' => $city, 'add_state' => $state,
                                  'add_pin' => $pin, 'telephone' => $tel, 'mobile' => $mobile,
                                  'date_of_comm' => $comDate, 'children_name' => $child, 'report_order' => 99,
                                  'user_id' => $user_id, 'status' => 1, 'username' => $username,
                                  'client_category' => $category
                              );
                              ////for bank details


                      
                            unset($inserted,$getdata);
             if(empty($clientAlready) && empty($panAlready)){  //both not available       @pallavi: 2017-06-16  all if else for $inserted       
                

                if(empty($notFolio)){  // if client name not available but pan not available also folio,and product id not available
               // echo"fresh client".$clientName.$productId.$folio_number.$pan_no;//exit;
                $dataRows[$countClient]['client_id']=$clientId;//@pallavi 2018-26 march
                  $inserted = $this->Clients_model->add_client_import($dataRows[$countClient]);
                  $fresh++;
                }else{
                // echo"in  folio"."but no client no pan  but folio and product id avalilable";
                  $getdata['folio_number']=$folio_number;
                  $getdata['productId']=$productId;
                  $getdata['broker_id']=$brokerID;
                  $getClientId=$this->Clients_model->getClientId($getdata);
                  $inserted=$getClientId;
                }


                  }elseif(empty($clientAlready) && !empty($panAlready)){ // if pan available and client name not available
                 //  echo"no client but pan available".$clientName;
                  //  $getdata['cname']=$clientName;
                    $getdata['pan']=$panNum;
                    $getdata['productId']=$productId;
                    $getdata['broker_id']=$brokerID;
                    $getClientId=$this->Clients_model->getClientId($getdata);
        //       print_r($getClientId);exit;
                  $inserted=$getClientId;
                  }elseif(!empty($clientAlready) && empty($panAlready)){   // if client name available and pan not available

                    if(empty($notFolio)){  /// if client name available but pan not available also folio,and product id also not available
                  //   echo"in not folio".$clientName.$productId.$folio_number.$pan_no;//exit;
                     $dataRows[$countClient]['client_id']=$clientId;//@pallavi 2018-26 march
                      $inserted = $this->Clients_model->add_client_import($dataRows[$countClient]);
                      $fresh++;
                    }else{                             // if client name available but pan not available also folio,and product same
                    // echo"in  folio".$clientName;
                      $getdata['cname']=$clientName;
                      $getdata['folio_number']=$folio_number;
                      $getdata['productId']=$productId;
                      $getdata['broker_id']=$brokerID;
                      $getClientId=$this->Clients_model->getClientId($getdata);
                      $inserted=$getClientId; 
                    }

        //       print_r($getClientId);exit;

                  }elseif(!empty($clientAlready) && !empty($panAlready)){ // if both available
                //echo"Client Already available".$clientName;
                  $getdata['cname']=$clientName;
                  $getdata['pan']=$panNum;
                  $getdata['folio_number']=$folio_number;
                  $getdata['productId']=$productId;
                  $getdata['broker_id']=$brokerID;
                  $getClientId=$this->Clients_model->getClientId($getdata);
      //       print_r($getClientId);exit;
                  $inserted=$getClientId;
                }
                          

                              if(is_array($inserted)) {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$inserted['message'];
                                 // echo $folio_number;
                                  break;
                              }else{  // insert clients bank details according to its last inserted id.
                                      if($rta_list=='karvy_excel'){
                                        if(!empty($bankBranch)){
                                          $bankBranch=$bankBranch.','.$bank_address_building;
                                        }else{
                                            $bankBranch=$bank_address_building;
                                        }
                                      }

                                $dataRowsBank[$countClient]=array(
                                  'bank_name'=>$bankName,'folio_number'=>$folio_number,'bank_branch'=>$bankBranch,'bank_acc_no'=>$bankAccNo,
                                  'bank_ifsc'=>$bankIFSC,'bank_account_types'=>$bank_account_types,'bank_address_building'=>$bank_address_building,
                                  'bank_address_road'=>$bank_address_road,'bank_address_area'=>$bank_address_area,
                                  'bank_address_city'=>$bank_address_city,'bank_pincode'=>$bank_pincode,
                                  'bank_state'=>$bank_state,'bank_country'=>$bank_country,'productId'=>$productId,'jointName1'=>$jointName1,
                                  'jointName2'=>$jointName2,'pan_no'=>$pan_no,'joint1_pan'=>$joint1_pan,'joint2_pan'=>$joint2_pan,'guard_pan'=>$guard_pan,
                                  'tax_status'=>$tax_status,'broker_code'=>$broker_code,'sub_boroker'=>$sub_boroker,'client_family_broker_id'=>$brokerID,'occ_name'=>$occ_name,
                                  'mode_holding'=>$mode_holding,'nominee_name1'=>$nominee_name1,'nom1_relation'=>$nom1_relation,'nominee_name2'=>$nominee_name2,
                                  'nom2_relation'=>$nom2_relation,'guardian_name'=>$guardian_name,'folioDate'=>$folioDate
                                  );
                               //   var_dump($dataRowsBank[$countClient]);

                                  $dataRowsBank[$countClient]['client_id']=$inserted;
                                
                                    $inserted = $this->Clients_model->add_client_bank_details($dataRowsBank[$countClient]);
                                //  }

                                  // print_r($inserted);die();



                              }

                              $countClient++;

                              $famName = ""; $clientName = ""; $panNum = ""; $clientType = "";
                              $comDate = ""; $occ = ""; $hof = ""; $rHof = ""; $report = "";
                              $dob = ""; $ann = null; $spouse = ""; $child = ""; $clientId = "";
                              $familyId = ""; $category = "";
                              $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
                              $mobile = ""; $tel = ""; $email = ""; $username = "";
                              $password = ""; $pan = ""; $passport = "";
                              $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
                              $clientAlready=0;$panAlready=0;$notFolio=0;
                              // //bank details variables
                              // $bankName="";$folio_number="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";
                              // $bank_address_road="";$bank_address_area="";$bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$productId="";
                              // $jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";$broker_code="";$sub_boroker="";
                              // $brokerID="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";$guardian_name="";$folioDate="";
                          }
                          if($uploadedStatus == 0)
                              break;
                          $countRow++;
                      }

                      if($dataRows)
                      {
                        
                          if(is_array($inserted) && $inserted['code']!=0) {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                              $this->Common_model->last_import('Client Details', $brokerID, $_FILES["import_clients"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = $fresh++." Client Details Uploaded Successfully";
                              }
                          }
                      }
                    
                      unset($dataColumns, $dataRows,$dataRowsBank,$inserted,$getClientId,$productId,$bankBranch,$bank_address_building);//exit;
                    }


                    }elseif($_FILES["import_clients"]["type"]=="application/x-dbf"){  //for DBF
                            
                      $file = $_FILES["import_clients"]["tmp_name"];
                      $rta_list=$_POST['rta_list'];
                    //   echo $file ;exit;
                         include('dbf_class.php');;
                          $dbf = new dbf_class($file );
                          $num_rec=$dbf->dbf_num_rec;
                          $field_num=$dbf->dbf_num_field;

                        //  echo $num_rec;exit;
                          $excelData=array(array());
                          for($j=0; $j<$field_num; $j++){
                                array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                          }
                          for($i=0; $i<$num_rec; $i++){
                            $temper=array();
                              if ($row = $dbf->getRow($i)) {

                                for($j=0; $j<$field_num; $j++){
                                    array_push($temper,$row[$j]);
                                }

                              }
                              array_push($excelData, $temper);//array of all rows
                          }
                    //      echo"<pre>";print_r($excelData);
                            //echo '<pre>';print_r($excelData);die; 
                          //temp variables to hold values  client info
                          $famName = ""; $clientName = ""; $panNum = ""; $clientType = ""; $comDate = "";
                          $occ = ""; $hof = ""; $rHof = ""; $report = ""; $dob = ""; $ann = null;
                          $spouse = ""; $child = ""; $clientId = ""; $familyId = ""; $category = "";
                          $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
                          $mobile = ""; $tel = ""; $email = ""; $username = "";
                          $password = ""; $pan = ""; $passport = "";
                          //@pallavi :variables holds the Clients bank details
                          $bankName="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";$bank_address_road="";$bank_address_area="";
                          $bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$folio_number="";
                          //additional details in client bank details table
                          $productId="";$jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";
                          $broker_code="";$sub_boroker="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";
                          $guardian_name="";$folioDate="";


                          $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
                          $fresh=0;
                          $brokerID = $this->session->userdata('broker_id');
                          $user_id = $this->session->userdata('user_id');

                          $dataColumns = array();
                          //stores row data
                          $dataRows = array();
                          $dataRowsBank=array();//@pallavi
                          $countRow = 0; $countErrRow = 0; $countClient = 0; $countRem = 0; $countTrans = 0;


                          //check max row for client import limit
                        //  var_dump($maxCell['row']);
                        
                        if($this->session->userdata('broker_id'))
                        {
                            $brokerID = $this->session->userdata('broker_id'); 
                        }
                        else
                        {
                            $brokerID = $this->session->userdata('user_id');  
                        }
                          
                          
                          $data = $this->Clients_model->get_limit($brokerID);
                          $count = $this->Clients_model->count_client($brokerID);
                          $data=intval($data->client_limit);
                        //  var_dump($data);exit;
                          $count = intval($count->count);
                          $remaining = $data - $count;
                          // var_dump($remaining);
                          // echo $maxCell['row'];
                          if($num_rec>$remaining)
                          {
                              $message = "Client data exceeds your client limit! Please contact support if you want to increase your limit.";
                          }
                          else{
                        //  echo"<pre>";print_r($excelData);
                          //$tempo=$excelData;
                          foreach($excelData as $rows)
                          {
                            foreach($rows as $key=>$val)
                            {
                              //echo $key;
                              $temp[$val]=$key;
                            } break;
                          }
                  
                          foreach($excelData as $rows)
                          {
                                    // print_r($rows);
                              $countCell = 0;
                              foreach($rows as $key=>$val)
                              {

                                  //echo  $rows[$key];exit;
                                  $cell=$rows[$key];
                                  if($countRow == 0)
                                  {

                                     $cell = str_replace(array('.'), '', $cell);
                                  //    echo $cell;
                                        /*@pallavi code for RTA Column matching*/
                                          if($rta_list=='cl_excel'){

                                                                            if(strtoupper($cell) == 'FAMILY NAME' || strtoupper($cell) == 'CLIENT NAME' || strtoupper($cell) == 'CLIENT TYPE' ||
                                                                                strtoupper($cell) == 'PAN NO' || strtoupper($cell) == 'OCCUPATION' || strtoupper($cell) == 'COMMENCEMENT DATE' ||
                                                                                strtoupper($cell) == 'HEAD OF FAMILY' || strtoupper($cell) == 'RELATION W/ HOF' || strtoupper($cell) == 'REPORT ORDER' ||
                                                                                strtoupper($cell) == 'DATE OF BIRTH' || strtoupper($cell) == 'ANNIVERSARY' || strtoupper($cell) == 'SPOUSE NAME' ||
                                                                                strtoupper($cell) == 'CHILDREN NAME' || strtoupper($cell) == 'HOUSE/FLAT NO' || strtoupper($cell) == 'STREET' ||
                                                                                strtoupper($cell) == 'AREA' || strtoupper($cell) == 'CITY' || strtoupper($cell) == 'STATE' ||
                                                                                strtoupper($cell) == 'PINCODE' || strtoupper($cell) == 'MOBILE' || strtoupper($cell) == 'TELEPHONE' ||
                                                                                strtoupper($cell) == 'EMAIL ID' || strtoupper($cell) == 'USERNAME' ||
                                                                                strtoupper($cell) == 'PASSPORT NO' || strtoupper($cell) == 'CLIENT CATEGORY')
                                                                            {
                                                                                $dataColumns[$countCell] = $cell;
                                                                                $countCell++;
                                                                                $uploadedStatus = 1;
                                                                                continue;
                                                                            }
                                                                            else
                                                                            {
                                                                                $message = 'Columns Specified in Excel is not in correct format';
                                                                                $uploadedStatus = 0;
                                                                                break;
                                                                            }
                                          }elseif($rta_list=='cams_excel'){ //for CAMS RTA for fetching columns
                                          //  echo"hello";
                                          //  echo strtoupper($cell);exit;
                                            // if(strtoupper($cell) == 'INV_NAME'){
                                            //   $vals=$temp[$cell];
                                            // //  echo $vals;
                                            //          $cell='Family Name';
                                            //
                                            //          $dataColumns[$vals] = $cell;
                                            //          $countCell++;
                                            //          $uploadedStatus = 2;
                                            //          continue;
                                            //    }else
                                               if(strtoupper($cell) == 'INV_NAME'){
                                                 $vals=$temp[$cell];
                                                     $cell='Client Name';
                                                      $dataColumns[$vals] = $cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                }elseif(strtoupper($cell) == 'PAN_NO'){
                                                  $vals=$temp[$cell];
                                                      $cell='Pan No';
                                                      $dataColumns[$vals] = $cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'TAX_STATUS'){
                                                      $vals=$temp[$cell];
                                                      $cell='Client Type';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                      $vals=$temp[$cell];
                                                      $cell='Occupation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) ==='INV_DOB'){
                                                      $vals=$temp[$cell];
                                                      $cell='Date Of Birth';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                  }elseif(strtoupper($cell) ==='ADDRESS1'){
                                                      $vals=$temp[$cell];
                                                      $cell='House/Flat No';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) ==='ADDRESS2'){
                                                      $vals=$temp[$cell];
                                                      $cell='Street';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) ==='ADDRESS3'){
                                                      $vals=$temp[$cell];
                                                      $cell='Area';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) ==='CITY'){
                                                      $vals=$temp[$cell];
                                                      $cell='City';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    // }elseif(strtoupper($cell) == 'NOM_STATE'){ //not found reffered NOM_STATE col from excel
                                                    //   $vals=$temp[$cell];
                                                    //   $cell='State';
                                                    //   $dataColumns[$vals]=$cell;
                                                    //   $countCell++;
                                                    //   $uploadedStatus = 2;
                                                    //   continue;
                                                  }elseif(strtoupper($cell) === 'PINCODE'){
                                                      $vals=$temp[$cell];
                                                      $cell='Pincode';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'MOBILE_NO'){
                                                      $vals=$temp[$cell];
                                                      $cell='Mobile';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'PHONE_RES'){
                                                      $vals=$temp[$cell];
                                                      $cell='Telephone';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'EMAIL'){
                                                      $vals=$temp[$cell];
                                                      $cell='Email ID';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'FOLIOCHK'){ // not found
                                                      $vals=$temp[$cell];
                                                      $cell='Folio_Number';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                  // }elseif(strtoupper($cell) === 'BROKER_COD'){
                                                  //     $vals=$temp[$cell];
                                                  //     $cell='Passport No';
                                                  //     $dataColumns[$vals]=$cell;
                                                  //     $countCell++;
                                                  //     $uploadedStatus = 1;
                                                  //     continue;
                                                    }elseif(strtoupper($cell) === 'REP_DATE'){  //not confirmed
                                                      $vals=$temp[$cell];
                                                      $cell='Commencement Date';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_Name';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_branch';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_ifsc';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'AC_NO'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_acc_no';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_acc_type';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_building';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_road';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_area';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_city';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_pincode';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }
                                                    elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_state';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_country';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'PRODUCT'){
                                                      $vals=$temp[$cell];
                                                      $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JNT_NAME1'){
                                                      $vals=$temp[$cell];
                                                      $cell='Joint_name1';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JNT_NAME2'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint_name2';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'GUARD_NAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Guardian Name';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINT1_PAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint 1 pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINT2_PAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint 2 pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Guardian pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Sub Broker';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'HOLDING_NA'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Mode Of Holding';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOM_NAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee1';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee1_Relation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOM2_NAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee2';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee2_Relation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Folio Date';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }





                                          }elseif($rta_list=='karvy_excel'){

                                          if(strtoupper($cell) == 'INVESTOR NAME'||strtoupper($cell) == 'INVNAME'){
                                            $vals=$temp[$cell];
                                          
                                                   $cell='Client Name';

                                                   $dataColumns[$vals] = $cell;
                                                   $countCell++;
                                                   $uploadedStatus = 1;
                                                   continue;

                                              }elseif(strtoupper($cell) == 'PAN NUMBER'||strtoupper($cell) == 'PANGNO'){
                                                $vals=$temp[$cell];
                                                    $cell='Pan No';
                                                    $dataColumns[$vals] = $cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'TAX STATUS'||strtoupper($cell) == 'STATUS'){
                                                    $vals=$temp[$cell];
                                                    $cell='Client Type';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'OCCUPATION DESCRIPTION'|| strtoupper($cell) == 'OCCP_DESC'){
                                                    $vals=$temp[$cell];
                                                    $cell='Occupation';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                }elseif(strtoupper($cell) == 'DATE OF BIRTH'||strtoupper($cell) == 'DOB'){
                                                    $vals=$temp[$cell];
                                                    $cell='Date Of Birth';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                }elseif(strtoupper($cell) == 'CATEGORYDESC'){
                                                    $vals=$temp[$cell];
                                                    $cell='Client Category';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'ADDRESS #1'||strtoupper($cell) == 'ADD1'){
                                                    $vals=$temp[$cell];
                                                    $cell='House/Flat No';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'ADDRESS #2'||strtoupper($cell) == 'ADD2'){
                                                    $vals=$temp[$cell];
                                                    $cell='Street';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'ADDRESS #3'||strtoupper($cell) == 'ADD3'){
                                                    $vals=$temp[$cell];
                                                    $cell='Area';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'CITY'){
                                                    $vals=$temp[$cell];
                                                    $cell='City';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'STATE'){
                                                    $vals=$temp[$cell];
                                                    $cell='State';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'PINCODE'||strtoupper($cell) == 'PIN'){
                                                    $vals=$temp[$cell];
                                                    $cell='Pincode';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'MOBILE NUMBER'|| strtoupper($cell) == 'MOBILE'){
                                                    $vals=$temp[$cell];
                                                    $cell='Mobile';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'PHONE RESIDENCE'||strtoupper($cell) == 'RPHONE'){
                                                    $vals=$temp[$cell];
                                                    $cell='Telephone';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'EMAIL'){
                                                    $vals=$temp[$cell];
                                                    $cell='Email ID';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  // }elseif(strtoupper($cell) == 'BROKER CODE'||strtoupper($cell) == 'BROKCODE'){
                                                  //   $vals=$temp[$cell];
                                                  //   $cell='Passport No';
                                                  //   $dataColumns[$vals]=$cell;
                                                  //   $countCell++;
                                                  //   $uploadedStatus = 1;
                                                  //   continue;
                                                  }elseif(strtoupper($cell) == 'FOLIO'||strtoupper($cell) == 'ACNO'){
                                                    $vals=$temp[$cell];
                                                    $cell='Folio_Number';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'REPORT DATE'|| strtoupper($cell) === 'CRDATE'){  //not confirmed
                                                    $vals=$temp[$cell];
                                                    $cell='Commencement Date';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK NAME'||strtoupper($cell) === 'BNAME'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_Name';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_branch';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'IFSC CODE'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_ifsc';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANKACCNO'|| strtoupper($cell) === 'BNKACNO' ){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_acc_no';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'ACCOUNT TYPE'|| strtoupper($cell) === 'BNKACTYPE'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_acc_type';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK ADDRESS #1'||strtoupper($cell) === 'BADD1'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_building';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK ADDRESS #2'|| strtoupper($cell) === 'BADD2'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_road';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK ADDRESS #3'||strtoupper($cell) === 'BADD3'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_area';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK CITY'||strtoupper($cell) === 'BCITY'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_city';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'BANK PINCODE'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_pincode';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }
                                                  elseif(strtoupper($cell) == 'BANK STATE'||strtoupper($cell) == 'BSTATE'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_state';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'BANK COUNTRY'||strtoupper($cell) == 'BCOUNTRY'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Bank_address_country';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) === 'Product Code'||strtoupper($cell) === 'PRCODE'){
                                                    $vals=$temp[$cell];
                                                    $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'Joint Name 1'||strtoupper($cell) == 'JTNAME1'){
                                                    $vals=$temp[$cell];
                                                    $cell='Joint_name1';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'Joint Name 2'||strtoupper($cell) == 'JTNAME2'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Joint_name2';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'GuardianName'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Guardian Name';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'PAN2'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Joint 1 pan';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'PAN3'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Joint 2 pan';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Guardian pan';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Sub Broker';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'MODEOFHOLD'||strtoupper($cell) == 'Mode of Holding Description'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Mode Of Holding';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'NOMINEE1'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Nominee1';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Nominee1_Relation';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'NOMINEE2'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Nominee2';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Nominee2_Relation';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                    $vals=$temp[$cell];
                                                    $cell='Folio Date';
                                                    $dataColumns[$vals]=$cell;
                                                    $countCell++;
                                                    $uploadedStatus = 1;
                                                    continue;
                                                  }

                                          }elseif($rta_list=='sundaram_excel'){  //sundaram excel column headings
                                            // echo"hello";
                                            // echo strtoupper($cell);

                                            if(strtoupper($cell) == 'INVNAME'){
                                              $vals=$temp[$cell];
                                            //  echo $vals;
                                                     $cell='Client Name';

                                                     $dataColumns[$vals] = $cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;

                                                }elseif(strtoupper($cell) == 'PAN'){
                                                  $vals=$temp[$cell];
                                                      $cell='Pan No';
                                                      $dataColumns[$vals] = $cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'TAXSTAT'){
                                                      $vals=$temp[$cell];
                                                      $cell='Client Type';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                      $vals=$temp[$cell];
                                                      $cell='Occupation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;

                                                    }elseif(strtoupper($cell) == 'INVDOB'){
                                                      $vals=$temp[$cell];
                                                      $cell='Date Of Birth';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS1'){
                                                      $vals=$temp[$cell];
                                                      $cell='House/Flat No';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS2'){
                                                      $vals=$temp[$cell];
                                                      $cell='Street';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS3'){
                                                      $vals=$temp[$cell];
                                                      $cell='Area';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'CITY'){
                                                      $vals=$temp[$cell];
                                                      $cell='City';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'CSTATE'){
                                                      $vals=$temp[$cell];
                                                      $cell='State';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'PINCODE'){
                                                      $vals=$temp[$cell];
                                                      $cell='Pincode';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'INVMOBIL'){
                                                      $vals=$temp[$cell];
                                                      $cell='Mobile';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'PHOFF'){
                                                      $vals=$temp[$cell];
                                                      $cell='Telephone';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'EMAIL'){
                                                      $vals=$temp[$cell];
                                                      $cell='Email ID';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    // }elseif(strtoupper($cell) == 'BROKCODE'){
                                                    //   $vals=$temp[$cell];
                                                    //   $cell='Passport No';
                                                    //   $dataColumns[$vals]=$cell;
                                                    //   $countCell++;
                                                    //   $uploadedStatus = 1;
                                                    //   continue;
                                                    }elseif(strtoupper($cell) == 'SBFSFOLIO'){ // not found
                                                      $vals=$temp[$cell];
                                                      //echo $vals;exit;
                                                      $cell='Folio_Number';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;

                                                      continue;
                                                    }elseif(strtoupper($cell) == 'FOLCRTDT'){
                                                      $vals=$temp[$cell];

                                                      $cell='Commencement Date';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                     }elseif(strtoupper($cell) === 'BANKNAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_Name';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANKBRA'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_branch';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'IFSCCODE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_ifsc';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANKACNO'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_acc_no';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANKACT'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_acc_type';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK ADDRESS #1'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_building';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK ADDRESS #2'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_road';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK ADDRESS #3'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_area';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK CITY'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_city';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK PINCODE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_pincode';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }
                                                    elseif(strtoupper($cell) == 'BANK STATE'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_state';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'BANK COUNTRY'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Bank_address_country';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'PRODCODE'){
                                                      $vals=$temp[$cell];
                                                      $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINTNAME1'){
                                                      $vals=$temp[$cell];
                                                      $cell='Joint_name1';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINTNAME2'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint_name2';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'GUARNAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Guardian Name';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINT1PAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint 1 pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'JOINT2PAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Joint 2 pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'GUARPAN'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Guardian pan';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'SUBBRKCOD'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Sub Broker';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'HOLNAT'||strtoupper($cell) == 'Mode of Holding Description'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Mode Of Holding';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOMNAME'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee1';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee1_Relation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOMNAM2'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee2';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Nominee2_Relation';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'FOLCRTDT'){  // bank details headers
                                                      $vals=$temp[$cell];
                                                      $cell='Folio Date';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }



                                          }elseif($rta_list=='frank_excel'){

                                            if(strtoupper($cell) == 'INV_NAME'|| strtoupper($cell) == 'INVNAME'){
                                              $vals=$temp[$cell];
                                            //  echo $vals;
                                                     $cell='Client Name';

                                                     $dataColumns[$vals] = $cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;

                                                }elseif(strtoupper($cell) == 'PANNO1'){
                                                  $vals=$temp[$cell];
                                                      $cell='Pan No';
                                                      $dataColumns[$vals] = $cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'TAX_STATUS'){
                                                      $vals=$temp[$cell];
                                                      $cell='Client Type';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;

                                                    }elseif(strtoupper($cell) == 'D_BIRTH'){
                                                      $vals=$temp[$cell];
                                                      $cell='Date Of Birth';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS1'){
                                                      $vals=$temp[$cell];
                                                      $cell='House/Flat No';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS2'){
                                                      $vals=$temp[$cell];
                                                      $cell='Street';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'ADDRESS3'){
                                                      $vals=$temp[$cell];
                                                      $cell='Area';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'CITY'){
                                                      $vals=$temp[$cell];
                                                      $cell='City';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'STATE'){
                                                      $vals=$temp[$cell];
                                                      $cell='State';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'PINCODE'){
                                                      $vals=$temp[$cell];
                                                      $cell='Pincode';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;

                                                  }elseif(strtoupper($cell) == 'PHONE_RES'){
                                                      $vals=$temp[$cell];
                                                      $cell='Telephone';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'EMAIL'){
                                                      $vals=$temp[$cell];
                                                      $cell='Email ID';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) == 'FOLIO_NO'){ // not found
                                                      $vals=$temp[$cell];
                                                      $cell='Folio_Number';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    // }elseif(strtoupper($cell) == 'BROK_CODE'){
                                                    //   $vals=$temp[$cell];
                                                    //   $cell='Passport No';
                                                    //   $dataColumns[$vals]=$cell;
                                                    //   $countCell++;
                                                    //   $uploadedStatus = 1;
                                                    //   continue;
                                                    }elseif(strtoupper($cell) == 'CREA_DATE'){
                                                      $vals=$temp[$cell];

                                                      $cell='Commencement Date';
                                                      $dataColumns[$vals]=$cell;
                                                      $countCell++;
                                                      $uploadedStatus = 1;
                                                      continue;
                                                    }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_Name';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_branch';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_ifsc';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'ACCNT_NO'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_acc_no';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_acc_type';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_building';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_road';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_area';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_city';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_pincode';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }
                                                   elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_state';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Bank_address_country';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                  //  }elseif(strtoupper($cell) == 'INVEST_ID'|| strtoupper($cell) == 'PRODCODE'){
                                                  //    $vals=$temp[$cell];
                                                  //    $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                  //    $dataColumns[$vals]=$cell;
                                                  //    $countCell++;
                                                  //    $uploadedStatus = 1;
                                                  //    continue;
                                                   }elseif(strtoupper($cell) == 'JOINT_NAM1'){
                                                     $vals=$temp[$cell];
                                                     $cell='Joint_name1';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'JOINT_NAM2'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Joint_name2';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'GUARNAME'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Guardian Name';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'PANNO2'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Joint 1 pan';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'PANNO3'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Joint 2 pan';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'GUARPAN'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Guardian pan';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'SUB_BROK18'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Sub Broker';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'HOLDING_T6'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Mode Of Holding';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'NOMINEE1'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Nominee1';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Nominee1_Relation';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'NOMINEE2'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Nominee2';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Nominee2_Relation';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }elseif(strtoupper($cell) == 'FOLCRTDT'){  // bank details headers
                                                     $vals=$temp[$cell];
                                                     $cell='Folio Date';
                                                     $dataColumns[$vals]=$cell;
                                                     $countCell++;
                                                     $uploadedStatus = 1;
                                                     continue;
                                                   }

                                          }

                                        /*@pallavi end*/




                                  }
                                  else
                                  {
                                    

                                     if($rta_list=='cl_excel'){
                                      if($insertRow)
                                      {
                                        // echo $countCell;
                                        // print_r($dataColumns[$countCell]);//exit;

                                        if(strtoupper($dataColumns[$countCell]) === 'FAMILY NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $famName = trim($cell);

                                                  //checks if family exists in Families table
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                  if(count($f_info) == 0)
                                                  {
                                                      //$insertRow = false;
                                                      //$impMessage = $famName." Family doesn't exist";
                                                      //family doesn't exist, so add a new family
                                                      $data = array(
                                                          'name' => $famName,
                                                          'status' => 1,
                                                          'broker_id' => $brokerID,
                                                          'user_id' => $user_id
                                                      );
                                                      $status = $this->Families_model->add($data);
                                                      if(isset($status['code'])) {
                                                          $insertRow = false;
                                                          $impMessage = "Could not add new Family for this client";
                                                      } else {
                                                          $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                          if(count($f_info) == 0)
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Could not find Family for this client";
                                                          }
                                                          else
                                                          {
                                                              $familyId = $f_info[0]->family_id;
                                                          }
                                                      }
                                                  }
                                                  else
                                                  {
                                                      $familyId = $f_info[0]->family_id;
                                                  }
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Family Name cannot be empty";
                                              }
                                          }

                                          if(strtoupper($dataColumns[$countCell]) === 'CLIENT NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                                  $whereClient = array(
                                                      'c.name'=>$clientName,
                                                      'fam.name'=>$famName,
                                                      'fam.broker_id'=>$brokerID
                                                  );
                                                  //checks if client exists in Clients table
                                                  $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                  if(count($c_info) == 0)
                                                  {
                                                      $clientId_obj = $this->Clients_model->get_new_client_id();
                                                      $clientId = $clientId_obj->client_id;
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Client Name cannot be empty";
                                              // }


                                          }

                                          if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                                  $wherePan = array(
                                                      'c.pan_no'=>$panNum,
                                                      'f.broker_id'=>$brokerID
                                                  );
                                                  //checks if policy exists in policy details table
                                                  $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                                  if(count($p_info) == 0)
                                                  {
                                                      //proceed normally
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Pan No. ".$panNum." already exists for another client";
                                                      //$clientId = $c_info->client_id;
                                                      //$clientName = $c_info->client_name;
                                                      //$familyId = $c_info->family_id;
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Pan Number cannot be empty";
                                              // }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'CLIENT TYPE')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $clientType = trim($cell);
                                                  $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $type_details = $this->Clients_model->get_client_types($whereCType);
                                                  if(count($type_details) == 0)
                                                  {
                                                    //$clientTypeId=40; //for others type of client Type
                                                      // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                      // $insertRow = false;
                                                      $defaultClientType='Resident Individual';
                                                        $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                    $ctype= $this->Clients_model->get_client_types($whereCType);
                                                    $clientTypeId =  $ctype[0]->client_type_id;
                                                  }
                                                  else
                                                  {
                                                      $clientTypeId = $type_details[0]->client_type_id;
                                                  }
                                              }
                                              else
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Type cannot be empty";
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) == 'OCCUPATION')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $occ = trim($cell);
                                                  $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                  if(count($occ_details) == 0)
                                                  {
                                                    $occId=null; //for others type of occupation

                                                      // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                      // $insertRow = false;
                                                  }
                                                  else
                                                  {
                                                      $occId = $occ_details[0]->occupation_id;
                                                  }
                                              }
                                              else
                                              {
                                                $occId=null;
                                                  // $insertRow = false;
                                                  // $impMessage = "Occupation cannot be empty";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'HEAD OF FAMILY')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $hof = trim($cell);
                                                  if(strtoupper($hof) == "YES") {
                                                      $hof = 1;
                                                  } else {
                                                      $hof = 0;
                                                  }
                                              }
                                              else
                                              {
                                                  $hof = 0;
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'RELATION W/ HOF')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $rHof = trim($cell);
                                              }
                                              else
                                              {
                                                  $rHof = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'REPORT ORDER')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $report = trim($cell);
                                              }
                                              else
                                              {
                                                  $report = 0;
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'DATE OF BIRTH')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  //var_dump($cell);exit;

                                                //  $date = DateTime::createFromFormat('m-d-y', $cell);
                                                  $date = new DateTime();
                                                //   $date=date_create($cell);
                                                //  echo date_format($date,"Y/m/d");exit;
                                                  if(is_object($date)) {
                                                      $today = new DateTime();
                                                      if($today < $date) {
                                                          $dob = $date->modify('-100 year')->format('Y-m-d');
                                                      } else {
                                                          $dob = $date->format('Y-m-d');
                                                      }
                                                      //var_dump($dob);
                                                      $dob_app = 1;
                                                  } else {
                                                      $insertRow = false;
                                                      $impMessage = "Date of Birth format is not proper (should be dd/mm/yyyy)";
                                                  }
                                              }
                                              else
                                              {
                                                  $dob = null;
                                                  $dob_app = 0;
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'ANNIVERSARY')
                                          {
                                            if($cell || $cell != '')
                                            {
                                              $date = DateTime::createFromFormat('m-d-Y', $cell);
                                              //  $date = DateTime::createFromFormat('m-d-y', $cell);
                                                if(is_object($date)) {
                                                  $ann=$date->format('Y-m-d');
                                                    $anv_app = 1;
                                                } else {
                                                  $date = new DateTime($cell);
                                                   if(is_object($date)){
                                                     $ann=$date->format('Y-m-d');

                                                   }else{
                                                    $insertRow = false;
                                                    $impMessage = "Anniversary date format is not proper (should be dd/mm/yyyy)";
                                                }
                                            }
                                          } else
                                            {
                                                $ann = null;
                                                $anv_app = 0;
                                            }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'SPOUSE NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $spouse = trim($cell);
                                              }
                                              else
                                              {
                                                  $spouse = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'CHILDREN NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $child = trim($cell);
                                              }
                                              else
                                              {
                                                  $child = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'HOUSE/FLAT NO')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $flat = trim($cell);
                                              }
                                              else
                                              {
                                                  $flat = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'STREET')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $street = trim($cell);
                                              }
                                              else
                                              {
                                                  $street = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'AREA')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $area = trim($cell);
                                              }
                                              else
                                              {
                                                  $area = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'CITY')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $city = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "City cannot be empty";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'STATE')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $state = trim($cell);
                                              }
                                              else
                                              {
                                                $state="";
                                                  // $insertRow = false;
                                                  // $impMessage = "State cannot be empty";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'PINCODE')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $pin = trim($cell);
                                              }
                                              else
                                              {
                                                  $pin = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'MOBILE')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $mobile = trim($cell);
                                              }
                                              else
                                              {
                                                  $mobile = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'TELEPHONE')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $tel = trim($cell);
                                              }
                                              else
                                              {
                                                  $tel = "";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'EMAIL ID')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $email = trim($cell);
                                              }
                                              else
                                              {
                                                $email="";
                                                  // $insertRow = false;
                                                  // $impMessage = "Email ID cannot be empty";
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'USERNAME')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $username = trim($cell);
                                                  //check duplicate username
                                                  //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                  $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                  $join = array(
                                                      'table' => 'families f',
                                                      'on' => 'c.family_id = f.family_id',
                                                      'type' => 'inner'
                                                  );
                                                  $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                  if($isUsernameDuplicate) {
                                                      $insertRow = false;
                                                      $impMessage = "Username already exists for another client";
                                                  }
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Username cannot be empty";
                                              }
                                          }
                                          /*else if(strtoupper($dataColumns[$countCell]) === 'PASSWORD')
                                          {
                                              if($cell  || $cell != '')
                                              {
                                                  $password = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Password cannot be empty";
                                              }
                                          }*/
                                          else if(strtoupper($dataColumns[$countCell]) === 'PASSPORT NO')
                                          {
                                              if($cell || $cell != '')
                                                  $passport = trim($cell);
                                              else
                                                  $passport = "";
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'COMMENCEMENT DATE')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $date = new DateTime();
                                                  //$date = DateTime::createFromFormat('m-d-y', $cell);
                                                  if(is_object($date)) {
                                                      $today = new DateTime();
                                                      if($today < $date) {
                                                          $comDate = $date->modify('-100 year')->format('Y-m-d');
                                                      } else {
                                                          $comDate = $date->format('Y-m-d');
                                                      }
                                                  } else {
                                                      $insertRow = false;
                                                      $impMessage = "Commencement date format is not proper (should be dd/mm/yyyy)";
                                                  }
                                              }
                                              else
                                              {
                                                  $comDate = date('Y-m-d');
                                              }
                                          }
                                          else if(strtoupper($dataColumns[$countCell]) === 'CLIENT CATEGORY')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $category = trim($cell);
                                              }
                                              else
                                              {
                                                  $category = "";
                                              }
                                          }
                                      } else {
                                          if(strtoupper($dataColumns[$countCell]) === 'FAMILY NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $famName = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Family Name cannot be empty";
                                              }
                                          }
                                          if(strtoupper($dataColumns[$countCell]) === 'CLIENT NAME')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Client Name cannot be empty";
                                              }
                                          }
                                          if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Pan Number cannot be empty";
                                              }
                                          }
                                          if(strtoupper($dataColumns[$countCell]) === 'FOLIO NUMBER')
                                          {
                                              if($cell || $cell != '')
                                              {
                                                  $folio_number= trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Pan Number cannot be empty";
                                              }
                                          }
                                      }
                                      $countCell++;
                                    }elseif($rta_list=='karvy_excel'){  // for karvy RTA
                                           unset($bankBranch);
                                           
                                      if($insertRow)
                                      {
                                        if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $famName = trim($cell);
                                              //   echo $famName;//exit;
                                                  //checks if family exists in Families table
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                  if(count($f_info) == 0)
                                                  {
                                                      //$insertRow = false;
                                                      //$impMessage = $famName." Family doesn't exist";
                                                      //family doesn't exist, so add a new family
                                                      $data = array(
                                                          'name' => $famName,
                                                          'status' => 1,
                                                          'broker_id' => $brokerID,
                                                          'user_id' => $user_id
                                                      );
                                                      $status = $this->Families_model->add($data);
                                                      if(isset($status['code'])) {
                                                          $insertRow = false;
                                                          $impMessage = "Could not add new Family for this client";
                                                      } else {
                                                          $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                          if(count($f_info) == 0)
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Could not find Family for this client";
                                                          }
                                                          else
                                                          {
                                                              $familyId = $f_info[0]->family_id;
                                                          }
                                                      }
                                                  }
                                                  else
                                                  {
                                                      $familyId = $f_info[0]->family_id;
                                                  }
                                              }
                                              else
                                              {
                                              //  $clientAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Family Name cannot be empty";
                                              }
                                            }else{ // If family name is not found in excel then drop the client in default family

                                                  $famName ="Default family"; //default family for non family client
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              //    print_r($f_info );
                                                  $familyId = $f_info[0]->family_id;
                                                  //echo $familyId;exit;
                                            }
                                        if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                          {
                                              
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              //   echo $clientName; //exit;
                                                  $whereClient = array(
                                                      'c.name'=>$clientName,
                                                      'fam.name'=>$famName,
                                                      'fam.broker_id'=>$brokerID
                                                  );
                                                  //checks if client exists in Clients table
                                                  $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                  //echo '<pre>';print_r($c_info);//die;
                                                  if(count($c_info) == 0)
                                                  {
                                                      $clientId_obj = $this->Clients_model->get_new_client_id();
                                                      $clientId = $clientId_obj->client_id;
                                                  }
                                                  else
                                                  {
                                                      
                                                      $clientAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                  }
                                              }
                                             /* else
                                              {
                                                  echo 2;
                                                  print_r($dataColumns);die();
                                                  $insertRow = false;
                                                  $impMessage = "Client Name cannot be empty";
                                              }*/
                                            }
                                            else{
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";

                                            }


                                        if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                            //   echo $panNum;//exit;
                                                 // echo $brokerID;die;
                                                  $wherePan = array(
                                                      'c.pan_no'=>$panNum,
                                                      'f.broker_id'=>$brokerID
                                                  );
                                                  //checks if policy exists in policy details table
                                                  $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);//echo '<pre>';print_r($p_info);die;
                                                  if(count($p_info) == 0)
                                                  {
                                                      //proceed normally
                                                  }
                                                  else
                                                  {
                                                    $panAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Pan No. ".$panNum." already exists for another client";
                                                  }
                                              }
                                              // else
                                              // {
                                              //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                              //    $dummypan = $clientId_obj->client_id;
                                              //    //C20160001
                                              //
                                              //   $panNum =str_replace('C','P',$dummypan);
                                              // //  echo "dummy pan".$panNum;exit;
                                              // }
                                              $pan_no=$panNum;//for client bank details
                                          }//echo $clientName;

                                      if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $clientType = trim($cell);
                                              //    echo $clientType;
                                                  $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $type_details = $this->Clients_model->get_client_types($whereCType);
                                                  if(count($type_details) == 0)
                                                  {
                                                      //$clientTypeId=40; //for others type of client Type
                                                      // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                      // $insertRow = false;
                                                      $defaultClientType='Resident Individual';
                                                        $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                    $ctype= $this->Clients_model->get_client_types($whereCType);
                                                    $clientTypeId =  $ctype[0]->client_type_id;
                                                  }
                                                  else
                                                  {
                                                      $clientTypeId = $type_details[0]->client_type_id;
                                                  }
                                              }
                                              else
                                              {
                                                $defaultClientType='Resident Individual';
                                                  $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $ctype= $this->Clients_model->get_client_types($whereCType);
                                              $clientTypeId =  $ctype[0]->client_type_id;
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Type cannot be empty";
                                              }
                                              $tax_status=$clientType;//for client's bank details
                                          }else{

                                            $defaultClientType='Resident Individual';
                                              $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                          $ctype= $this->Clients_model->get_client_types($whereCType);
                                          $clientTypeId =  $ctype[0]->client_type_id;

                                          $tax_status="";//for client's bank details
                                          $clientType=$clientTypeId;
                                          }
                                          if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $occ = trim($cell);
                                                 //  echo $occ;exit;
                                                   $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                   $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                   if(count($occ_details) == 0)
                                                   {
                                                       $occId=null; //for others type of occupation
                                                      //  $impMessage = 'Occupation '.$occ." doesn't exist";
                                                      //  $insertRow = false;
                                                   }
                                                   else
                                                   {
                                                       $occId = $occ_details[0]->occupation_id;
                                                   }
                                               }
                                               else
                                               {
                                                   $occId=null;
                                                  //  $insertRow = false;
                                                  //  $impMessage = "Occupation cannot be empty";
                                               }
                                              $occ_name=$occ;//for client bank details
                                           }

                                     if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                           if($cell || $cell != '')
                                               {
                                                   $hof = trim($cell);

                                                   if(strtoupper($hof) == "YES") {
                                                       $hof = 1;
                                                   } else {
                                                       $hof = 0;
                                                   }
                                               }
                                               else
                                               {
                                                   $hof = 0;
                                               }
                                               //  echo $hof;exit;
                                           }

                                          if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $rHof = trim($cell);
                                                   //echo $rHof;
                                               }
                                               else
                                               {
                                                   $rHof = "";
                                               }

                                           }

                                           if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 //echo $cell;
                                              //  $cell=trim(str_replace('/','-', $cell));
                                           //  echo $cell; //exit; //date checked
                                              if($cell || $cell != '')
                                              {
                                               $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                  //var_dump($cell);exit;
                                                //  $date->format('Y-m-d');
                                                  if(is_object($date)){
                                                    $dob=$date->format('Y-m-d');
                                                  }else{
                                                    $date = new DateTime($cell);
                                                     if(is_object($date)){
                                                       $dob=$date->format('Y-m-d');
                                                     }else{

                                                             $insertRow = false;
                                                             $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                         }

                                                     }

                                                  } else
                                                   {
                                                       $dob = null;
                                                       $dob_app = 0;
                                                   }

                                              }else
                                              {
                                                  $dob = null;
                                                  $dob_app = 0;
                                              }

                                           if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $flat = trim($cell);
                                               }
                                               else
                                               {
                                                   $flat = "";
                                               }

                                           }

                                            if(!empty($findkey=array_search('Street',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $street = trim($cell);
                                               }
                                               else
                                               {
                                                   $street = "";
                                               }

                                           }

                                            if(!empty($findkey=array_search('Area',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $area = trim($cell);
                                               }
                                               else
                                               {
                                                   $area = "";
                                               }

                                           }
                                            if(!empty($findkey=array_search('City',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $city = trim($cell);
                                               }
                                               else
                                               {
                                                 $city="";
                                                  //  $insertRow = false;
                                                  //  $impMessage = "City cannot be empty";
                                               }

                                           }

                                           if(!empty($findkey=array_search('State',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                               if($cell || $cell != '')
                                               {
                                                   $state = trim($cell);
                                               }
                                               else
                                               {
                                                   $state="";
                                                  //  $insertRow = false;
                                                  //  $impMessage = "State cannot be empty";
                                               }
                                           }

                                            if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                               if($cell  || $cell != '')
                                               {
                                                   $pin = trim($cell);
                                               }
                                               else
                                               {
                                                   $pin = "";
                                               }

                                           }

                                            if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                               if($cell  || $cell != '')
                                               {
                                                   $mobile = trim($cell);
                                               }
                                               else
                                               {
                                                   $mobile = "";
                                               }

                                           }

                                           if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                               if($cell  || $cell != '')
                                               {
                                                   $tel = trim($cell);
                                               }
                                               else
                                               {
                                                   $tel = "";
                                               }

                                           }

                                         if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                               if($cell  || $cell != '')
                                               {
                                                   $email = trim($cell);
                                               }
                                               else
                                               {
                                                 $email="";
                                                  //  $insertRow = false;
                                                  //  $impMessage = "Email ID cannot be empty";
                                               }

                                           }

                                           if(!empty($findkey=array_search('Username',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                               if($cell  || $cell != '')
                                               {
                                                   $username = trim($cell);
                                                   //check duplicate username
                                                   //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                   $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                   $join = array(
                                                       'table' => 'families f',
                                                       'on' => 'c.family_id = f.family_id',
                                                       'type' => 'inner'
                                                   );
                                                   $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                   if($isUsernameDuplicate) {
                                                     //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                  $username=date("Ymd").time().$username.rand(99,99999);
                                                   }
                                               }
                                               // else
                                               // {
                                               //     $insertRow = false;
                                               //     $impMessage = "Username cannot be empty";
                                               // }
                                           }else{ //if username is not in excel sheet create default username with client name and random number
                                              $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                           }


                                           if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                               if($cell || $cell != ''){
                                                   $passport = trim($cell);
                                                 }
                                               else{
                                                   $passport = "";
                                                 }
                                            //  $broker_code = $passport; //for client bank details
                                           }

                                           if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 $cell=trim(str_replace('/','-', $cell));
                                               if($cell || $cell != '')
                                               {
                                                $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                   //var_dump($cell);exit;
                                                  // $date->format('Y-m-d');
                                                   if(is_object($date)){
                                                     $comDate=$date->format('Y-m-d');
                                                   }else{
                                                     $date = new DateTime($cell);
                                                      if(is_object($date)){
                                                        $comDate=$date->format('Y-m-d');

                                                      }else{

                                                              // $insertRow = false;
                                                              // $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                          }

                                                      }

                                                   } else
                                                    {
                                                        $comDate = null;
                                                        $dob_app = 0;
                                                    }
                                                     // echo $dob;//exit;
                                               }else
                                               {
                                                   $comDate = null;
                                                   $dob_app = 0;
                                               }

                                           if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 if($cell || $cell != '')
                                                 {
                                                     //$folio_number = trim($cell);
                                                     $folio_number = stripcslashes(trim($cell));
                                                     $folio_number = str_replace("'",'',$folio_number);

                                                 }
                                             }elseif($findkey==0){
                                               $cell=$rows[$findkey];
                                               $folio_number = stripcslashes(trim($cell));
                                               $folio_number = str_replace("'",'',$folio_number);

                                             }//echo $folio_number;die;
                                           if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 if($cell || $cell != '')
                                                 {
                                                     $bankName = trim($cell);

                                                 }
                                             }
                                             if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                               {
                                                 $cell=$rows[$findkey];
                                                   if($cell || $cell != '')
                                                   {
                                                       $bankBranch = trim($cell);

                                                   }
                                                 }else{
                                                   $bankBranch="";
                                                 }
                                               if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                                 {
                                                   $cell=$rows[$findkey];
                                                     if($cell || $cell != '')
                                                     {
                                                         $bank_account_types = trim($cell);

                                                     }
                                                 }
                                                 if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                                   {
                                                     $cell=$rows[$findkey];
                                                       if($cell || $cell != '')
                                                       {
                                                           $bankAccNo = trim($cell);

                                                       }
                                                   }
                                                   if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                     {
                                                       $cell=$rows[$findkey];
                                                         if($cell || $cell != '')
                                                         {
                                                             $bank_address_building = trim($cell);

                                                         }

                                                     }
                                                     if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                       {

                                                         $cell=$rows[$findkey];
                                                           if($cell || $cell != '')
                                                           {
                                                               $bank_address_road = trim($cell);

                                                           }
                                                            //echo $bank_address_road;
                                                       }
                                                       if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                         {

                                                           $cell=$rows[$findkey];
                                                             if($cell || $cell != '')
                                                             {
                                                                 $bank_address_area = trim($cell);

                                                             }
                                                          //   echo $bank_address_area;
                                                         }
                                                         if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                           {
                                                             $cell=$rows[$findkey];
                                                               if($cell || $cell != '')
                                                               {
                                                                   $bank_address_city = trim($cell);

                                                               }
                                                           }
                                                           if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                             {
                                                               $cell=$rows[$findkey];
                                                                 if($cell || $cell != '')
                                                                 {
                                                                     $bank_pincode = trim($cell);

                                                                 }
                                                             }
                                                             if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                               {
                                                                 $cell=$rows[$findkey];
                                                                   if($cell || $cell != '')
                                                                   {
                                                                       $bank_state = trim($cell);

                                                                   }
                                                               }
                                                               if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                                 {
                                                                   $cell=$rows[$findkey];
                                                                     if($cell || $cell != '')
                                                                     {
                                                                         $bank_country = trim($cell);

                                                                     }
                                                                 }

                                          if(!empty($findkey=array_search('Client Category',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $category = trim($cell);
                                              }
                                              else
                                              {
                                                  $category = "";
                                              }
                                          }
                                          //for other details of bank
                                          if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                            {
                                              // $cell=$rows[$findkey];
                                              //   if($cell || $cell != '')
                                              //   {
                                              //     $productId = trim($cell);
                                              //
                                              //   }else{
                                              //     $productId="";
                                              //   }

                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                  $productId = trim($cell);
                                                        $data=array();
                                                        if($productId){
                                                         $data['productId']=$productId;
                                                        }
                                                       $data['broker_id']=$brokerID;
                                                       if($folio_number){
                                                          $data['folio_number']=$folio_number;
                                                       }
                                                       if($clientName){
                                                              $data['clientName']=$clientName;
                                                       }

                                                       if($pan_no){
                                                         $data['pan_no']=$pan_no;
                                                       }

                                                        //check product scheme  already available
                                                       $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                        //print_r($scheme_info);die;
                                                       if(count($scheme_info) == 0)
                                                       {
                                                           //proceed normally
                                                          // $productId=$productId;
                                                          $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                       }
                                                       else
                                                       {
                                                          $notFolio=1;
                                                          //  $insertRow = false;
                                                          //     $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client < ".$clientName." >";

                                                       }

                                                }

                                            }elseif($findkey==0){
                                            //  echo"key is zero";//exit;
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                  $productId = trim($cell);
                                                        $data=array();
                                                       $data['productId']=$productId;
                                                       $data['broker_id']=$brokerID;
                                                       if($folio_number){
                                                          $data['folio_number']=$folio_number;
                                                       }
                                                       if($clientName){
                                                              $data['clientName']=$clientName;
                                                       }

                                                       if($pan_no){
                                                         $data['pan_no']=$pan_no;
                                                       }

                                                        //check product scheme  already available
                                                       $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                //   print_r($scheme_info);exit;
                                                       if(count($scheme_info) == 0)
                                                       {
                                                           //proceed normally
                                                          // $productId=$productId;
                                                          $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                       }
                                                       else
                                                       {
                                                          $notFolio=1;
                                                        //    $insertRow = false;
                                                        //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                       }

                                                }
                                            }//echo $notFolio;die;
                                            if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                    $jointName1 = trim($cell);

                                                  }else{
                                                    $jointName1="";
                                                  }
                                              }
                                              if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                      $jointName2 = trim($cell);

                                                    }else{
                                                      $jointName2="";
                                                    }
                                                }
                                                if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                        $joint1_pan = trim($cell);

                                                      }else{
                                                        $joint1_pan="";
                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                          $joint2_pan = trim($cell);

                                                        }else{
                                                          $joint2_pan="";
                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                            $guard_pan = trim($cell);

                                                          }else{
                                                            $guard_pan="";
                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                              $sub_boroker = trim($cell);

                                                            }else{
                                                              $sub_boroker="";
                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                $mode_holding = trim($cell);

                                                              }else{
                                                                $mode_holding="";
                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                  $nominee_name1 = trim($cell);

                                                                }else{
                                                                  $nominee_name1="";
                                                                }
                                                            }
                                                            if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $nom1_relation = trim($cell);

                                                                  }else{
                                                                    $nom1_relation ="";
                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                      $nominee_name2 = trim($cell);

                                                                    }else{
                                                                      $nominee_name2="";
                                                                    }
                                                                }
                                                                if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $nom2_relation = trim($cell);

                                                                      }else{
                                                                        $nom2_relation ="";
                                                                      }
                                                                  }
                                                                  if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $guardian_name= trim($cell);

                                                                        }else{
                                                                          $guardian_name="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                        $cell=trim(str_replace('/','-', $cell));
                                                                        if($cell || $cell != '')
                                                                        {
                                                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                            //var_dump($cell);exit;
                                                                           // $date->format('Y-m-d');
                                                                            if(is_object($date)){
                                                                              $folioDate=$date->format('Y-m-d');
                                                                            }else{
                                                                              $date = new DateTime($cell);
                                                                               if(is_object($date)){
                                                                                 $folioDate=$date->format('Y-m-d');

                                                                               }else{

                                                                                       // $insertRow = false;
                                                                                       // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                   }

                                                                               }

                                                                            } else
                                                                             {
                                                                                 $folioDate = null;
                                                                                 $dob_app = 0;
                                                                             }
                                                                              // echo $dob;//exit;
                                                                        }else
                                                                        {
                                                                            $folioDate = null;
                                                                            $dob_app = 0;
                                                                        }


                                      } else {

                                          if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Client Name cannot be empty";
                                              // }
                                          }
                                          if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Pan Number cannot be empty";
                                              // }
                                          }
                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $folio_number = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Folio Number cannot be empty";
                                              // }
                                          }

                                      }
                                      $countCell++;


                                    }elseif($rta_list=='cams_excel'){  // for CAMS RTA

                                      if($insertRow)
                                      {
                                        // echo $countCell;
                                    //    print_r($dataColumns[$countCell]);//exit;
                                        //  $findkey=array_search('Family Name',$dataColumns);
                                      //    echo $findkey;exit;
                                        //if(strtoupper($dataColumns[4]) === 'FAMILY NAME')
                                        if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $famName = trim($cell);
                                          //echo $famName;exit;
                                                  //checks if family exists in Families table
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                  if(count($f_info) == 0)
                                                  {
                                                      //$insertRow = false;
                                                      //$impMessage = $famName." Family doesn't exist";
                                                      //family doesn't exist, so add a new family
                                                      $data = array(
                                                          'name' => $famName,
                                                          'status' => 1,
                                                          'broker_id' => $brokerID,
                                                          'user_id' => $user_id
                                                      );
                                                      $status = $this->Families_model->add($data);
                                                      if(isset($status['code'])) {
                                                          $insertRow = false;
                                                          $impMessage = "Could not add new Family for this client";
                                                      } else {
                                                          $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                          if(count($f_info) == 0)
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Could not find Family for this client";
                                                          }
                                                          else
                                                          {
                                                              $familyId = $f_info[0]->family_id;
                                                          }
                                                      }
                                                  }
                                                  else
                                                  {
                                                      $familyId = $f_info[0]->family_id;
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Family Name cannot be empty";
                                              // }
                                          }else{ // If family name is not found in excel then drop the client in default family

                                                $famName ="Default family"; //default family for non family client
                                                $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                $familyId = $f_info[0]->family_id;
                                          }


                                        if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];

                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                                //echo $clientName; exit;
                                                  $whereClient = array(
                                                      'c.name'=>$clientName,
                                                      'fam.name'=>$famName,
                                                      'fam.broker_id'=>$brokerID
                                                  );
                                                  //checks if client exists in Clients table
                                                  $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                  if(count($c_info) == 0)
                                                  {
                                                      $clientId_obj = $this->Clients_model->get_new_client_id();
                                                      $clientId = $clientId_obj->client_id;
                                                  }
                                                  else
                                                  {
                                                    $clientAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                  }
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Client Name cannot be empty";
                                              }
                                          }


                                        if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              //  echo $panNum;exit;
                                                  $wherePan = array(
                                                      'c.pan_no'=>$panNum,
                                                      'f.broker_id'=>$brokerID
                                                  );
                                                  //checks if policy exists in policy details table
                                                  $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                                  if(count($p_info) == 0)
                                                  {
                                                      //proceed normally
                                                  }
                                                  else
                                                  {
                                                    $panAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                                  }
                                              }
                                              // else
                                              // {
                                              //
                                              //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                              //    $dummypan = $clientId_obj->client_id;
                                              //    //C20160001
                                              //
                                              //   $panNum =str_replace('C','P',$dummypan);
                                              // //  echo "dummy pan".$panNum;exit;
                                              // }
                                              $pan_no=$panNum; //for client Bank  details
                                          }

                                      if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $clientType = trim($cell);
                                                  //  echo $clientType;
                                                  $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $type_details = $this->Clients_model->get_client_types($whereCType);
                                                  if(count($type_details) == 0)
                                                  {
                                                      //$clientTypeId=40; //for others type of client Type
                                                      // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                      // $insertRow = false;
                                                      $defaultClientType='Resident Individual';
                                                        $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                    $ctype= $this->Clients_model->get_client_types($whereCType);
                                                    $clientTypeId =  $ctype[0]->client_type_id;
                                                  }
                                                  else
                                                  {
                                                      $clientTypeId = $type_details[0]->client_type_id;
                                                  }
                                              }
                                              else
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Type cannot be empty";
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                                $tax_status=$clientType; //for client bank details
                                          }else{

                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;

                                                    $tax_status=""; //for client bank details
                                                      $clientType=$clientTypeId;
                                          }

                                         if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $occ = trim($cell);
                                                //  echo $occ;exit;
                                                  $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                  if(count($occ_details) == 0)
                                                  {
                                                      $occId=null; //for others type of occupation
                                                      // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                      // $insertRow = false;
                                                  }
                                                  else
                                                  {
                                                      $occId = $occ_details[0]->occupation_id;
                                                  }
                                              }
                                              else
                                              {
                                                  $occId=null;
                                                  // $insertRow = false;
                                                  // $impMessage = "Occupation cannot be empty";
                                              }
                                               $occ_name=$occ; //for bank details
                                          }

                                    if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                              {
                                                  $hof = trim($cell);

                                                  if(strtoupper($hof) == "YES") {
                                                      $hof = 1;
                                                  } else {
                                                      $hof = 0;
                                                  }
                                              }
                                              else
                                              {
                                                  $hof = 0;
                                              }
                                              //  echo $hof;exit;
                                          }

                                         if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $rHof = trim($cell);
                                                  //echo $rHof;
                                              }
                                              else
                                              {
                                                  $rHof = "";
                                              }

                                          }


                                          if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                //echo $cell;
                                            //   $cell=trim(str_replace('/','-', $cell));
                                          //  echo $cell; //exit; //date checked
                                             if($cell || $cell != '')
                                             {
                                              $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                 //var_dump($cell);exit;
                                                // $date->format('Y-m-d');
                                                 if(is_object($date)){
                                                   $dob=$date->format('Y-m-d');
                                                 }else{
                                                   $date = new DateTime($cell);
                                                    if(is_object($date)){
                                                      $dob=$date->format('Y-m-d');
                                                    }else{

                                                            $insertRow = false;
                                                            $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                        }

                                                    }

                                                 } else
                                                  {
                                                      $dob = null;
                                                      $dob_app = 0;
                                                  }

                                             }else
                                             {
                                                 $dob = null;
                                                 $dob_app = 0;
                                             }

                                          if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $flat = trim($cell);
                                              }
                                              else
                                              {
                                                  $flat = "";
                                              }

                                          }

                                           if(!empty($findkey=array_search('Street',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $street = trim($cell);
                                              }
                                              else
                                              {
                                                  $street = "";
                                              }

                                          }

                                           if(!empty($findkey=array_search('Area',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $area = trim($cell);
                                              }
                                              else
                                              {
                                                  $area = "";
                                              }

                                          }
                                           if(!empty($findkey=array_search('City',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $city = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "City cannot be empty";
                                              }

                                          }

                                          if(!empty($findkey=array_search('State',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $state = trim($cell);
                                              }
                                              else
                                              {
                                                  $state="";
                                                  // $insertRow = false;
                                                  // $impMessage = "State cannot be empty";
                                              }
                                          }

                                           if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $pin = trim($cell);
                                              }
                                              else
                                              {
                                                  $pin = "";
                                              }

                                          }

                                           if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $mobile = trim($cell);
                                              }
                                              else
                                              {
                                                  $mobile = "";
                                              }

                                          }

                                          if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $tel = trim($cell);
                                              }
                                              else
                                              {
                                                  $tel = "";
                                              }

                                          }

                                        if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $email = trim($cell);
                                              }
                                              else
                                              {
                                                $email="";
                                                  // $insertRow = false;
                                                  // $impMessage = "Email ID cannot be empty";
                                              }

                                          }

                                          if(!empty($findkey=array_search('Username',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $username = trim($cell);
                                                  //check duplicate username
                                                  //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                  $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                  $join = array(
                                                      'table' => 'families f',
                                                      'on' => 'c.family_id = f.family_id',
                                                      'type' => 'inner'
                                                  );
                                                  $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                  if($isUsernameDuplicate) {
                                                    //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                  $username=date("Ymd").time().$username.rand(99,99999);
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Username cannot be empty";
                                              // }
                                          }else{ //if username is not in excel sheet create default username with client name and random number
                                            $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                          }


                                          if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != ''){
                                                  $passport = trim($cell);
                                                }else{
                                                  $passport = "";
                                                }
                                            //  $broker_code=$passport; //for bank details
                                           }

                                           if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                                 $cell=trim(str_replace('/','-', $cell));
                                               if($cell || $cell != '')
                                               {
                                                $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                   //var_dump($cell);exit;
                                                  // $date->format('Y-m-d');
                                                   if(is_object($date)){
                                                     $comDate=$date->format('Y-m-d');
                                                   }else{
                                                     $date = new DateTime($cell);
                                                      if(is_object($date)){
                                                        $comDate=$date->format('Y-m-d');

                                                      }else{

                                                              // $insertRow = false;
                                                              // $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                          }

                                                      }

                                                   } else
                                                    {
                                                        $comDate = null;
                                                        $dob_app = 0;
                                                    }
                                                     // echo $dob;//exit;
                                               }else
                                               {
                                                   $comDate = null;
                                                   $dob_app = 0;
                                               }

                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $folio_number = stripcslashes(trim($cell));
                                                    $folio_number = str_replace("'",'',$folio_number);

                                                }
                                            }elseif($findkey==0){
                                              $cell=$rows[$findkey];
                                              $folio_number = stripcslashes(trim($cell));
                                              $folio_number = str_replace("'",'',$folio_number);

                                            }
                                          if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $bankName = trim($cell);

                                                }
                                            }
                                            if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                      $bankBranch = trim($cell);

                                                  }
                                              }
                                              if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_account_types = trim($cell);

                                                    }
                                                }
                                                if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                          $bankAccNo = trim($cell);

                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                            $bank_address_building = trim($cell);

                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                              $bank_address_road = trim($cell);

                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                                $bank_address_area = trim($cell);

                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                  $bank_address_city = trim($cell);

                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                    $bank_pincode = trim($cell);

                                                                }
                                                            }
                                                            if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                      $bank_state = trim($cell);

                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                        $bank_country = trim($cell);

                                                                    }
                                                                }
                                                              //for other details of bank
                                                              if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                                {
                                                                  // $cell=$rows[$findkey];
                                                                  //   if($cell || $cell != '')
                                                                  //   {
                                                                  //     $productId = trim($cell);
                                                                  //
                                                                  //   }else{
                                                                  //     $productId="";
                                                                  //   }

                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $productId = trim($cell);
                                                                                  $data=array();
                                                                                  if($productId){
                                                                                   $data['productId']=$productId;
                                                                                  }
                                                                                 $data['broker_id']=$brokerID;
                                                                                 if($folio_number){
                                                                                    $data['folio_number']=$folio_number;
                                                                                 }
                                                                                 if($clientName){
                                                                                        $data['clientName']=$clientName;
                                                                                 }

                                                                                 if($pan_no){
                                                                                   $data['pan_no']=$pan_no;
                                                                                 }

                                                                                  //check product scheme  already available
                                                                                 $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                          //   print_r($scheme_info);exit;
                                                                                 if(count($scheme_info) == 0)
                                                                                 {
                                                                                     //proceed normally
                                                                                    // $productId=$productId;
                                                                                    $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                                 }
                                                                                 else
                                                                                 {
                                                                                    $notFolio=1;
                                                                                    //  $insertRow = false;
                                                                                    //    $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                                 }
                                                                          }

                                                                }
                                                                if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $jointName1 = trim($cell);

                                                                      }else{
                                                                        $jointName1="";
                                                                      }
                                                                  }
                                                                  if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $jointName2 = trim($cell);

                                                                        }else{
                                                                          $jointName2="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $joint1_pan = trim($cell);

                                                                          }else{
                                                                            $joint1_pan="";
                                                                          }
                                                                      }
                                                                      if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $joint2_pan = trim($cell);

                                                                            }else{
                                                                              $joint2_pan="";
                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                $guard_pan = trim($cell);

                                                                              }else{
                                                                                $guard_pan="";
                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $sub_boroker = trim($cell);

                                                                                }else{
                                                                                  $sub_boroker="";
                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                    $mode_holding = trim($cell);

                                                                                  }else{
                                                                                    $mode_holding="";
                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                      $nominee_name1 = trim($cell);

                                                                                    }else{
                                                                                      $nominee_name1="";
                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                        $nom1_relation = trim($cell);

                                                                                      }else{
                                                                                        $nom1_relation ="";
                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                          $nominee_name2 = trim($cell);

                                                                                        }else{
                                                                                          $nominee_name2="";
                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                            $nom2_relation = trim($cell);

                                                                                          }else{
                                                                                            $nom2_relation ="";
                                                                                          }
                                                                                      }
                                                                                      if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                        {
                                                                                          $cell=$rows[$findkey];
                                                                                            if($cell || $cell != '')
                                                                                            {
                                                                                              $guardian_name= trim($cell);

                                                                                            }else{
                                                                                              $guardian_name="";
                                                                                            }
                                                                                        }
                                                                                        if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                          {
                                                                                            $cell=$rows[$findkey];
                                                                                            $cell=trim(str_replace('/','-', $cell));
                                                                                            if($cell || $cell != '')
                                                                                            {
                                                                                             $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                                //var_dump($cell);exit;
                                                                                               // $date->format('Y-m-d');
                                                                                                if(is_object($date)){
                                                                                                  $folioDate=$date->format('Y-m-d');
                                                                                                }else{
                                                                                                  $date = new DateTime($cell);
                                                                                                   if(is_object($date)){
                                                                                                     $folioDate=$date->format('Y-m-d');

                                                                                                   }else{

                                                                                                           // $insertRow = false;
                                                                                                           // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                       }

                                                                                                   }

                                                                                                } else
                                                                                                 {
                                                                                                     $folioDate = null;
                                                                                                     $dob_app = 0;
                                                                                                 }
                                                                                                  // echo $dob;//exit;
                                                                                            }else
                                                                                            {
                                                                                                $folioDate = null;
                                                                                                $dob_app = 0;
                                                                                            }




                                      } else {


                                          if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Client Name cannot be empty";
                                              // }
                                          }
                                          if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Pan Number cannot be empty";
                                              // }
                                          }
                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $folio_number = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Folio Number cannot be empty";
                                              // }
                                          }


                                      }
                                      $countCell++;

                                    }elseif($rta_list=='sundaram_excel'){ //for Sundram RTA

                                                                      if($insertRow)
                                                                      {

                                                                        if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $famName = trim($cell);
                                                                            //  echo $famName;//exit;
                                                                                  //checks if family exists in Families table
                                                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                                  if(count($f_info) == 0)
                                                                                  {
                                                                                      //$insertRow = false;
                                                                                      //$impMessage = $famName." Family doesn't exist";
                                                                                      //family doesn't exist, so add a new family
                                                                                      $data = array(
                                                                                          'name' => $famName,
                                                                                          'status' => 1,
                                                                                          'broker_id' => $brokerID,
                                                                                          'user_id' => $user_id
                                                                                      );
                                                                                      $status = $this->Families_model->add($data);
                                                                                      if(isset($status['code'])) {
                                                                                          $insertRow = false;
                                                                                          $impMessage = "Could not add new Family for this client";
                                                                                      } else {
                                                                                          $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                                          if(count($f_info) == 0)
                                                                                          {
                                                                                              $insertRow = false;
                                                                                              $impMessage = "Could not find Family for this client";
                                                                                          }
                                                                                          else
                                                                                          {
                                                                                              $familyId = $f_info[0]->family_id;
                                                                                          }
                                                                                      }
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                      $familyId = $f_info[0]->family_id;
                                                                                  }
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //     $insertRow = false;
                                                                              //     $impMessage = "Family Name cannot be empty";
                                                                              // }
                                                                          }else{ // If family name is not found in excel then drop the client in default family

                                                                                $famName ="Default family"; //default family for non family client
                                                                                $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                                                $familyId = $f_info[0]->family_id;
                                                                          }

                                                                        if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];

                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $clientName = trim($cell);
                                                                              //  echo $clientName; exit;
                                                                                  $whereClient = array(
                                                                                      'c.name'=>$clientName,
                                                                                      'fam.name'=>$famName,
                                                                                      'fam.broker_id'=>$brokerID
                                                                                  );
                                                                                  //checks if client exists in Clients table
                                                                                  $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                                                  if(count($c_info) == 0)
                                                                                  {
                                                                                      $clientId_obj = $this->Clients_model->get_new_client_id();
                                                                                      $clientId = $clientId_obj->client_id;
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                    $clientAlready=1;
                                                                                      // $insertRow = false;
                                                                                      // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                                                  }
                                                                              }
                                                                              else
                                                                              {
                                                                                  $insertRow = false;
                                                                                  $impMessage = "Client Name cannot be empty";
                                                                              }
                                                                          }
                                                                        if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $panNum = trim($cell);
                                                                            // echo $panNum;exit;
                                                                                  $wherePan = array(
                                                                                      'c.pan_no'=>$panNum,
                                                                                      'f.broker_id'=>$brokerID
                                                                                  );
                                                                                  //checks if policy exists in policy details table
                                                                                  $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                                                                  if(count($p_info) == 0)
                                                                                  {
                                                                                      //proceed normally
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                    $panAlready=1;
                                                                                      // $insertRow = false;
                                                                                      // $impMessage = "Pan No. ".$panNum." already exists for another client";
                                                                                      //$clientId = $c_info->client_id;
                                                                                      //$clientName = $c_info->client_name;
                                                                                      //$familyId = $c_info->family_id;
                                                                                  }
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //
                                                                              //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                                                              //    $dummypan = $clientId_obj->client_id;
                                                                              //    //C20160001
                                                                              //
                                                                              //   $panNum =str_replace('C','P',$dummypan);
                                                                              //   $pan_no=$panNum;//for client's bank details
                                                                              // //  echo "dummy pan".$panNum;exit;
                                                                              // }
                                                                            $pan_no=$panNum;//for client's bank details
                                                                          }
                                                                      if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $clientType = trim($cell);
                                                                                  // echo $clientType;exit;
                                                                                  $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                                  $type_details = $this->Clients_model->get_client_types($whereCType);
                                                                                  if(count($type_details) == 0)
                                                                                  {
                                                                                    //  $clientTypeId=40; //for others type of client Type
                                                                                      // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                                                      // $insertRow = false;
                                                                                      $defaultClientType='Resident Individual';
                                                                                        $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                                    $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                                    $clientTypeId =  $ctype[0]->client_type_id;
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                      $clientTypeId = $type_details[0]->client_type_id;
                                                                                  }

                                                                              $tax_status=$clientType; //for clients bank details
                                                                              }
                                                                              else
                                                                              {
                                                                                //  $clientTypeId=40; //for others type of client Type
                                                                                  $tax_status=""; //for clients bank details
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "Client Type cannot be empty";
                                                                                  $defaultClientType='Resident Individual';
                                                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                                $clientTypeId =  $ctype[0]->client_type_id;
                                                                              }
                                                                          }else{

                                                                                $defaultClientType='Resident Individual';
                                                                                  $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                              $ctype= $this->Clients_model->get_client_types($whereCType);
                                                                              $clientTypeId =  $ctype[0]->client_type_id;
                                                                              //$clientTypeId=40; //for  file not having client type col.
                                                                                $tax_status=""; //for clients bank details
                                                                                  $clientType=$clientTypeId;
                                                                          }
                                                                         if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                                                           {
                                                                             $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $occ = trim($cell);
                                                                                // echo $occ;exit;
                                                                                  $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                                                  $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                                                  if(count($occ_details) == 0)
                                                                                  {
                                                                                      $occId=null; //for others type of occupation
                                                                                      // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                                                      // $insertRow = false;
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                      $occId = $occ_details[0]->occupation_id;
                                                                                  }
                                                                              }
                                                                              else
                                                                              {
                                                                                  $occId=null;
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "Occupation cannot be empty";
                                                                              }
                                                                              $occ_name=$occ;//for client bank details
                                                                          }

                                                                          if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                //echo $cell;
                                                                             //  $cell=trim(str_replace('/','-', $cell));
                                                                          //  echo $cell; //exit; //date checked
                                                                             if($cell || $cell != '')
                                                                             {
                                                                              $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                 //var_dump($cell);exit;
                                                                                // $date->format('Y-m-d');
                                                                                 if(is_object($date)){
                                                                                   $dob=$date->format('Y-m-d');
                                                                                 }else{
                                                                                   $date = new DateTime($cell);
                                                                                    if(is_object($date)){
                                                                                      $dob=$date->format('Y-m-d');
                                                                                    }else{

                                                                                            $insertRow = false;
                                                                                            $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                                                        }

                                                                                    }

                                                                                 } else
                                                                                  {
                                                                                      $dob = null;
                                                                                      $dob_app = 0;
                                                                                  }

                                                                             }else
                                                                             {
                                                                                 $dob = null;
                                                                                 $dob_app = 0;
                                                                             }

                                                                          if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $flat = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $flat = "";
                                                                              }

                                                                          }

                                                                           if(!empty($findkey=array_search('Street',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $street = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $street = "";
                                                                              }

                                                                          }
                                                                           if(!empty($findkey=array_search('Area',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $area = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $area = "";
                                                                              }

                                                                          }

                                                                           if(!empty($findkey=array_search('City',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $city = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $insertRow = false;
                                                                                  $impMessage = "City cannot be empty";
                                                                              }

                                                                          }

                                                                          if(!empty($findkey=array_search('State',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $state = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $state="";
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "State cannot be empty";
                                                                              }
                                                                          }

                                                                           if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $pin = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $pin = "";
                                                                              }

                                                                          }

                                                                           if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                                                             {
                                                                               $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $mobile = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $mobile = "";
                                                                              }

                                                                          }
                                                                          if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $tel = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                  $tel = "";
                                                                              }

                                                                          }

                                                                        if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $email = trim($cell);
                                                                              }
                                                                              else
                                                                              {
                                                                                $email="";
                                                                                  // $insertRow = false;
                                                                                  // $impMessage = "Email ID cannot be empty";
                                                                              }

                                                                          }

                                                                          if(!empty($findkey=array_search('Username',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell  || $cell != '')
                                                                              {
                                                                                  $username = trim($cell);
                                                                                  //check duplicate username
                                                                                  //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                                                  $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                                                  $join = array(
                                                                                      'table' => 'families f',
                                                                                      'on' => 'c.family_id = f.family_id',
                                                                                      'type' => 'inner'
                                                                                  );
                                                                                  $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                                                  if($isUsernameDuplicate) {
                                                                                    //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                                                   $username=date("Ymd").time().$username.rand(99,99999);
                                                                                  }
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //     $insertRow = false;
                                                                              //     $impMessage = "Username cannot be empty";
                                                                              // }
                                                                          }else{ //if username is not in excel sheet create default username with client name and random number
                                                                              $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                                                          }

                                                                          if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != ''){
                                                                                  $passport = trim($cell);
                                                                                }
                                                                              else{
                                                                                  $passport = "";
                                                                                }
                                                                            //    $broker_code=$passport;//for client bank details
                                                                          }
                                                                          if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                $cell=trim(str_replace('/','-', $cell));
                                                                              if($cell || $cell != '')
                                                                              {
                                                                               $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                  //var_dump($cell);exit;
                                                                                 // $date->format('Y-m-d');
                                                                                  if(is_object($date)){
                                                                                    $comDate=$date->format('Y-m-d');
                                                                                  }else{
                                                                                    $date = new DateTime($cell);
                                                                                     if(is_object($date)){
                                                                                       $comDate=$date->format('Y-m-d');

                                                                                     }else{

                                                                                            //  $insertRow = false;
                                                                                            //  $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                                                         }

                                                                                     }

                                                                                  } else
                                                                                   {
                                                                                       $comDate = null;
                                                                                       $dob_app = 0;
                                                                                   }
                                                                                    // echo $dob;//exit;
                                                                              }else
                                                                              {
                                                                                  $comDate = null;
                                                                                  $dob_app = 0;
                                                                              }



                                                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $folio_number = trim($cell);
                                                                                 $folio_number = stripcslashes(trim($cell));
                                                                                 $folio_number = str_replace("'",'',$folio_number);
                                                                                }

                                                                            }elseif($findkey==0){
                                                                              $cell=$rows[$findkey];
                                                                              $folio_number = trim($cell);
                                                                             $folio_number = stripcslashes(trim($cell));
                                                                            $folio_number = str_replace("'",'',$folio_number);
                                                                          //  echo $folio_number;exit;
                                                                            }
                                                                          if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];

                                                                                if($cell || $cell != '')
                                                                                {
                                                                                    $bankName = trim($cell);

                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                      $bankBranch = trim($cell);

                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                        $bank_account_types = trim($cell);

                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                          $bankAccNo = trim($cell);

                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                            $bank_address_building = trim($cell);

                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                              $bank_address_road = trim($cell);

                                                                                          }
                                                                                      }
                                                                                      if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                                                        {
                                                                                          $cell=$rows[$findkey];
                                                                                            if($cell || $cell != '')
                                                                                            {
                                                                                                $bank_address_area = trim($cell);

                                                                                            }
                                                                                        }
                                                                                        if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                                                          {
                                                                                            $cell=$rows[$findkey];
                                                                                              if($cell || $cell != '')
                                                                                              {
                                                                                                  $bank_address_city = trim($cell);

                                                                                              }
                                                                                          }
                                                                                          if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                                                            {
                                                                                              $cell=$rows[$findkey];
                                                                                                if($cell || $cell != '')
                                                                                                {
                                                                                                    $bank_pincode = trim($cell);

                                                                                                }
                                                                                            }
                                                                                            if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                                                              {
                                                                                                $cell=$rows[$findkey];
                                                                                                  if($cell || $cell != '')
                                                                                                  {
                                                                                                      $bank_state = trim($cell);

                                                                                                  }
                                                                                              }
                                                                                              if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                                                                {
                                                                                                  $cell=$rows[$findkey];
                                                                                                    if($cell || $cell != '')
                                                                                                    {
                                                                                                        $bank_country = trim($cell);

                                                                                                    }
                                                                                                }
                                                                                                //for other details of bank
                                                                                                if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                                                                  {
                                                                                                    // $cell=$rows[$findkey];
                                                                                                    //   if($cell || $cell != '')
                                                                                                    //   {
                                                                                                    //     $productId = trim($cell);
                                                                                                    //
                                                                                                    //   }else{
                                                                                                    //     $productId="";
                                                                                                    //   }

                                                                                                    $cell=$rows[$findkey];
                                                                                                      if($cell || $cell != '')
                                                                                                      {
                                                                                                        $productId = trim($cell);
                                                                                                              $data=array();
                                                                                                              if($productId){
                                                                                                               $data['productId']=$productId;
                                                                                                              }
                                                                                                             $data['broker_id']=$brokerID;
                                                                                                             if($folio_number){
                                                                                                                $data['folio_number']=$folio_number;
                                                                                                             }
                                                                                                             if($clientName){
                                                                                                                    $data['clientName']=$clientName;
                                                                                                             }

                                                                                                             if($pan_no){
                                                                                                               $data['pan_no']=$pan_no;
                                                                                                             }

                                                                                                              //check product scheme  already available
                                                                                                             $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                                                      //   print_r($scheme_info);exit;
                                                                                                             if(count($scheme_info) == 0)
                                                                                                             {
                                                                                                                 //proceed normally
                                                                                                                // $productId=$productId;
                                                                                                                $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                                                             }
                                                                                                             else
                                                                                                             {
                                                                                                                $notFolio=1;
                                                                                                                //  $insertRow = false;
                                                                                                                //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                                                             }
                                                                                                      }
                                                                                                  }
                                                                                                  if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                                                                    {
                                                                                                      $cell=$rows[$findkey];
                                                                                                        if($cell || $cell != '')
                                                                                                        {
                                                                                                          $jointName1 = trim($cell);

                                                                                                        }else{
                                                                                                          $jointName1="";
                                                                                                        }
                                                                                                    }
                                                                                                    if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                                                      {
                                                                                                        $cell=$rows[$findkey];
                                                                                                          if($cell || $cell != '')
                                                                                                          {
                                                                                                            $jointName2 = trim($cell);

                                                                                                          }else{
                                                                                                            $jointName2="";
                                                                                                          }
                                                                                                      }
                                                                                                      if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                                                        {
                                                                                                          $cell=$rows[$findkey];
                                                                                                            if($cell || $cell != '')
                                                                                                            {
                                                                                                              $joint1_pan = trim($cell);

                                                                                                            }else{
                                                                                                              $joint1_pan="";
                                                                                                            }
                                                                                                        }
                                                                                                        if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                                                          {
                                                                                                            $cell=$rows[$findkey];
                                                                                                              if($cell || $cell != '')
                                                                                                              {
                                                                                                                $joint2_pan = trim($cell);

                                                                                                              }else{
                                                                                                                $joint2_pan="";
                                                                                                              }
                                                                                                          }
                                                                                                          if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                                                            {
                                                                                                              $cell=$rows[$findkey];
                                                                                                                if($cell || $cell != '')
                                                                                                                {
                                                                                                                  $guard_pan = trim($cell);

                                                                                                                }else{
                                                                                                                  $guard_pan="";
                                                                                                                }
                                                                                                            }
                                                                                                            if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                                                              {
                                                                                                                $cell=$rows[$findkey];
                                                                                                                  if($cell || $cell != '')
                                                                                                                  {
                                                                                                                    $sub_boroker = trim($cell);

                                                                                                                  }else{
                                                                                                                    $sub_boroker="";
                                                                                                                  }
                                                                                                              }
                                                                                                              if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                                                                {
                                                                                                                  $cell=$rows[$findkey];
                                                                                                                    if($cell || $cell != '')
                                                                                                                    {
                                                                                                                      $mode_holding = trim($cell);

                                                                                                                    }else{
                                                                                                                      $mode_holding="";
                                                                                                                    }
                                                                                                                }
                                                                                                                if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                                                                  {
                                                                                                                    $cell=$rows[$findkey];
                                                                                                                      if($cell || $cell != '')
                                                                                                                      {
                                                                                                                        $nominee_name1 = trim($cell);

                                                                                                                      }else{
                                                                                                                        $nominee_name1="";
                                                                                                                      }
                                                                                                                  }
                                                                                                                  if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                                                                    {
                                                                                                                      $cell=$rows[$findkey];
                                                                                                                        if($cell || $cell != '')
                                                                                                                        {
                                                                                                                          $nom1_relation = trim($cell);

                                                                                                                        }else{
                                                                                                                          $nom1_relation ="";
                                                                                                                        }
                                                                                                                    }
                                                                                                                    if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                                                      {
                                                                                                                        $cell=$rows[$findkey];
                                                                                                                          if($cell || $cell != '')
                                                                                                                          {
                                                                                                                            $nominee_name2 = trim($cell);

                                                                                                                          }else{
                                                                                                                            $nominee_name2="";
                                                                                                                          }
                                                                                                                      }
                                                                                                                      if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                                                        {
                                                                                                                          $cell=$rows[$findkey];
                                                                                                                            if($cell || $cell != '')
                                                                                                                            {
                                                                                                                              $nom2_relation = trim($cell);

                                                                                                                            }else{
                                                                                                                              $nom2_relation ="";
                                                                                                                            }
                                                                                                                        }
                                                                                                                        if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                                                          {
                                                                                                                            $cell=$rows[$findkey];
                                                                                                                              if($cell || $cell != '')
                                                                                                                              {
                                                                                                                                $guardian_name= trim($cell);

                                                                                                                              }else{
                                                                                                                                $guardian_name="";
                                                                                                                              }
                                                                                                                          }
                                                                                                                          if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                                                            {
                                                                                                                              $cell=$rows[$findkey];
                                                                                                                              $cell=trim(str_replace('/','-', $cell));
                                                                                                                              if($cell || $cell != '')
                                                                                                                              {
                                                                                                                               $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                                                                  //var_dump($cell);exit;
                                                                                                                                 // $date->format('Y-m-d');
                                                                                                                                  if(is_object($date)){
                                                                                                                                    $folioDate=$date->format('Y-m-d');
                                                                                                                                  }else{
                                                                                                                                    $date = new DateTime($cell);
                                                                                                                                     if(is_object($date)){
                                                                                                                                       $folioDate=$date->format('Y-m-d');

                                                                                                                                     }else{

                                                                                                                                             // $insertRow = false;
                                                                                                                                             // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                                                         }

                                                                                                                                     }

                                                                                                                                  } else
                                                                                                                                   {
                                                                                                                                       $folioDate = null;
                                                                                                                                       $dob_app = 0;
                                                                                                                                   }
                                                                                                                                    // echo $dob;//exit;
                                                                                                                              }else
                                                                                                                              {
                                                                                                                                  $folioDate = null;
                                                                                                                                  $dob_app = 0;
                                                                                                                              }


                                                                      } else {

                                                                          if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $clientName = trim($cell);
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //     $insertRow = false;
                                                                              //     $impMessage = "Client Name cannot be empty";
                                                                              // }
                                                                          }
                                                                          if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $panNum = trim($cell);
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //     $insertRow = false;
                                                                              //     $impMessage = "Pan Number cannot be empty";
                                                                              // }
                                                                          }
                                                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                  $folio_number = trim($cell);
                                                                              }
                                                                              // else
                                                                              // {
                                                                              //     $insertRow = false;
                                                                              //     $impMessage = "Folio Number cannot be empty";
                                                                              // }
                                                                          }

                                                                      }
                                                                      $countCell++;


                                    }elseif($rta_list=='frank_excel'){   //for Franklin RTA
                                          unset($fpart,$folio_number,$productId);
                                      if($insertRow)
                                      {

                                        // print_r($dataColumns[$countCell]);//exit;

                                        if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $famName = trim($cell);

                                                  //checks if family exists in Families table
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                  if(count($f_info) == 0)
                                                  {
                                                      $data = array(
                                                          'name' => $famName,
                                                          'status' => 1,
                                                          'broker_id' => $brokerID,
                                                          'user_id' => $user_id
                                                      );
                                                      $status = $this->Families_model->add($data);
                                                      if(isset($status['code'])) {
                                                          $insertRow = false;
                                                          $impMessage = "Could not add new Family for this client";
                                                      } else {
                                                          $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                          if(count($f_info) == 0)
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Could not find Family for this client";
                                                          }
                                                          else
                                                          {
                                                              $familyId = $f_info[0]->family_id;
                                                          }
                                                      }
                                                  }
                                                  else
                                                  {
                                                      $familyId = $f_info[0]->family_id;
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Family Name cannot be empty";
                                              // }
                                            }else{ // If family name is not found in excel then drop the client in default family

                                                  $famName ="Default family"; //default family for non family client
                                                  $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                  $familyId = $f_info[0]->family_id;
                                            }


                                        if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];

                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              //   echo $clientName; exit;
                                                  $whereClient = array(
                                                      'c.name'=>$clientName,
                                                      'fam.name'=>$famName,
                                                      'fam.broker_id'=>$brokerID
                                                  );
                                                  //checks if client exists in Clients table
                                                  $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                                  if(count($c_info) == 0)
                                                  {
                                                      $clientId_obj = $this->Clients_model->get_new_client_id();
                                                      $clientId = $clientId_obj->client_id;
                                                  }
                                                  else
                                                  {
                                                    $clientAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                                  }
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "Client Name cannot be empty";
                                              }
                                        }

                                        if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              //  echo $panNum;exit;
                                                  $wherePan = array(
                                                      'c.pan_no'=>$panNum,
                                                      'f.broker_id'=>$brokerID
                                                  );
                                                  //checks if policy exists in policy details table
                                                  $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                                  if(count($p_info) == 0)
                                                  {
                                                      //proceed normally
                                                  }
                                                  else
                                                  {
                                                    $panAlready=1;
                                                      // $insertRow = false;
                                                      // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                                  }
                                                    $pan_no=$panNum; //for client bank's details
                                              }
                                              // else
                                              // {
                                              //
                                              //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                              //    $dummypan = $clientId_obj->client_id;
                                              //    //C20160001
                                              //
                                              //   $panNum =str_replace('C','P',$dummypan);
                                              //   $pan_no=$panNum;//for client bank's details
                                              // //  echo "dummy pan".$panNum;exit;
                                              // }

                                          }

                                      if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $clientType = trim($cell);
                                                  //  echo $clientType;
                                                  $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $type_details = $this->Clients_model->get_client_types($whereCType);
                                                  if(count($type_details) == 0)
                                                  {
                                                      //$clientTypeId=40; //for others type of client Type
                                                      // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                      // $insertRow = false;
                                                      $defaultClientType='Resident Individual';
                                                        $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                    $ctype= $this->Clients_model->get_client_types($whereCType);
                                                    $clientTypeId =  $ctype[0]->client_type_id;
                                                  }
                                                  else
                                                  {
                                                      $clientTypeId = $type_details[0]->client_type_id;
                                                  }
                                                 $tax_status=$clientType;//for clients bank details

                                              }
                                              else
                                              {
                                                //  $clientTypeId=40; //for others type of client Type
                                                   $tax_status="";
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Type cannot be empty";
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }

                                          }else{

                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;

                                              $clientType=$clientTypeId;
                                          }

                                         if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                           {
                                             $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $occ = trim($cell);
                                                //  echo $occ;exit;
                                                  $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                  $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                                  if(count($occ_details) == 0)
                                                  {
                                                      $occId=null; //for others type of occupation
                                                      // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                      // $insertRow = false;
                                                  }
                                                  else
                                                  {
                                                      $occId = $occ_details[0]->occupation_id;
                                                  }
                                              }
                                              else
                                              {
                                                  $occId=null;
                                                  // $insertRow = false;
                                                  // $impMessage = "Occupation cannot be empty";
                                              }
                                               $occ_name=$occ; //for client's bank details
                                          }


                                          if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                //echo $cell;
                                             //  $cell=trim(str_replace('/','-', $cell));
                                          //  echo $cell; //exit; //date checked
                                             if($cell || $cell != '')
                                             {
                                              $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                 //var_dump($cell);exit;
                                              //   $date->format('Y-m-d');
                                                 if(is_object($date)){
                                                   $dob=$date->format('Y-m-d');
                                                 }else{
                                                   $date = new DateTime($cell);
                                                    if(is_object($date)){
                                                      $dob=$date->format('Y-m-d');
                                                    }else{

                                                            $insertRow = false;
                                                            $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                        }

                                                    }

                                                 } else
                                                  {
                                                      $dob = null;
                                                      $dob_app = 0;
                                                  }

                                             }else
                                             {
                                                 $dob = null;
                                                 $dob_app = 0;
                                             }

                                          if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $flat = trim($cell);
                                              }
                                              else
                                              {
                                                  $flat = "";
                                              }


                                          }

                                           if(!empty($findkey=array_search('Street',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $street = trim($cell);
                                              }
                                              else
                                              {
                                                  $street = "";
                                              }


                                          }

                                           if(!empty($findkey=array_search('Area',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $area = trim($cell);
                                              }
                                              else
                                              {
                                                  $area = "";
                                              }


                                          }

                                           if(!empty($findkey=array_search('City',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $city = trim($cell);
                                              }
                                              else
                                              {
                                                  $insertRow = false;
                                                  $impMessage = "City cannot be empty";
                                              }


                                          }

                                          if(!empty($findkey=array_search('State',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $state = trim($cell);
                                              }
                                              else
                                              {
                                                $state ='';
                                                  // $insertRow = false;
                                                  // $impMessage = "State cannot be empty";
                                              }

                                          }
                                           if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                             {
                                               $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $pin = trim($cell);
                                              }
                                              else
                                              {
                                                  $pin = "";
                                              }


                                          }
                                           //if(strtoupper($dataColumns[45]) === 'MOBILE')
                                          //  if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                          //    {
                                          //      $cell=$rows[$findkey];
                                          //     if($cell  || $cell != '')
                                          //     {
                                          //         $mobile = trim($cell);
                                          //     }
                                          //     else
                                          //     {
                                          //         $mobile = "";
                                          //     }
                                          //
                                          // }

                                          if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $tel = trim($cell);
                                              }
                                              else
                                              {
                                                  $tel = "";
                                              }


                                          }
                                        if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $email = trim($cell);
                                              }
                                              else
                                              {
                                                $email="";
                                                  // $insertRow = false;
                                                  // $impMessage = "Email ID cannot be empty";
                                              }


                                          }

                                          if(!empty($findkey=array_search('Username',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell  || $cell != '')
                                              {
                                                  $username = trim($cell);
                                                  //check duplicate username
                                                  //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                                  $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                                  $join = array(
                                                      'table' => 'families f',
                                                      'on' => 'c.family_id = f.family_id',
                                                      'type' => 'inner'
                                                  );
                                                  $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                                  if($isUsernameDuplicate) {
                                                    //  $insertRow = false; $impMessage = "Username already exists for another client";
                                                   $username=date("Ymd").time().$username.rand(99,99999);
                                                  }
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Username cannot be empty";
                                              // }
                                          }else{ //if username is not in excel sheet create default username with client name and random number
                                             $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                          }

                                          if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != ''){
                                                  $passport = trim($cell);
                                                }
                                              else{
                                                $passport = "";
                                              }
                                            //  $broker_code=$passport; //for clients bank details

                                          }

                                          if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                $cell=trim(str_replace('/','-', $cell));
                                              if($cell || $cell != '')
                                              {
                                               $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                  //var_dump($cell);exit;
                                                 // $date->format('Y-m-d');
                                                  if(is_object($date)){
                                                    $comDate=$date->format('Y-m-d');
                                                  }else{
                                                    $date = new DateTime($cell);
                                                     if(is_object($date)){
                                                       $comDate=$date->format('Y-m-d');

                                                     }else{

                                                            //  $insertRow = false;
                                                            //  $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                         }

                                                     }

                                                  } else
                                                   {
                                                       $comDate = null;
                                                       $dob_app = 0;
                                                   }
                                                    // echo $dob;//exit;
                                              }else
                                              {
                                                  $comDate = null;
                                                  $dob_app = 0;
                                              }

                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                   $folio_number = trim($cell);
                                                  $folio_number = stripcslashes(trim($cell));
                                                  $folio_number = str_replace("'",'',$folio_number);

                                                }
                                            }elseif($findkey==0){
                                              $cell=$rows[$findkey];
                                              $folio_number = stripcslashes(trim($cell));
                                              $folio_number = str_replace("'",'',$folio_number);

                                            }
                                          if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $bankName = trim($cell);

                                                }
                                            }
                                            if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                      $bankBranch = trim($cell);

                                                  }
                                              }
                                              if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_account_types = trim($cell);

                                                    }
                                                }
                                                if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                          $bankAccNo = trim($cell);

                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                            $bank_address_building = trim($cell);

                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                              $bank_address_road = trim($cell);

                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                                $bank_address_area = trim($cell);

                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                  $bank_address_city = trim($cell);

                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                    $bank_pincode = trim($cell);

                                                                }
                                                            }
                                                            if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                      $bank_state = trim($cell);

                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                        $bank_country = trim($cell);

                                                                    }
                                                                }
                                                                //for other details of bank
                                                                if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $productId = trim($cell);
                                                                              $data=array();
                                                                              if($productId){
                                                                               $data['productId']=$productId;
                                                                              }
                                                                             $data['broker_id']=$brokerID;
                                                                             if($folio_number){
                                                                                $data['folio_number']=$folio_number;
                                                                             }
                                                                             if($clientName){
                                                                                    $data['clientName']=$clientName;
                                                                             }

                                                                             if($pan_no){
                                                                               $data['pan_no']=$pan_no;
                                                                             }

                                                                              //check product scheme  already available
                                                                             $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                      //   print_r($scheme_info);exit;
                                                                             if(count($scheme_info) == 0)
                                                                             {
                                                                                 //proceed normally
                                                                                // $productId=$productId;
                                                                                $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                             }
                                                                             else
                                                                             {
                                                                                $notFolio=1;
                                                                                //  $insertRow = false;
                                                                                //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                             }

                                                                      }

                                                                    }else{
                                                                        //if product Id is not available-->specially for franklin file
                                                                        /*code to get productId from folionumber for frankilin file*/
                                                                      //  echo $folio_number;
                                                                        $fpart=substr($folio_number,0, 3);
                                                                      // echo $fpart;
                                                                       $productId_fromScheme=$this->Clients_model->get_product_code_from_mf_scheme($fpart);
                                                                      //  print_r($productId_fromScheme);
                                                                         if(count($productId_fromScheme)==0){
                                                                           //if at first level for one leading zero product id is blank then check for two leading zip_entry_close
                                                                           $folio_number='0'.$folio_number;
                                                                           $fpart=substr($folio_number,0, 3);
                                                                            $productId_fromScheme=$this->Clients_model->get_product_code_from_mf_scheme($fpart);
                                                                            if(count($productId_fromScheme)==0){
                                                                              $productId=" ";
                                                                            }else{
                                                                            $productId=$productId_fromScheme[0]['prod_code'];
                                                                            }
                                                                         }else{
                                                                           $productId=$productId_fromScheme[0]['prod_code'];
                                                                         }
                                                                        //   echo "product Id=".$productId;    exit;
                                                                        $data=array();
                                                                        if($productId){
                                                                         $data['productId']=$productId;
                                                                        }
                                                                       $data['broker_id']=$brokerID;
                                                                       if($folio_number){
                                                                          $data['folio_number']=$folio_number;
                                                                       }
                                                                       if($clientName){
                                                                              $data['clientName']=$clientName;
                                                                       }

                                                                       if($pan_no){
                                                                         $data['pan_no']=$pan_no;
                                                                       }

                                                                        //check product scheme  already available
                                                                       $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                //   print_r($scheme_info);exit;
                                                                       if(count($scheme_info) == 0)
                                                                       {
                                                                           //proceed normally
                                                                          // $productId=$productId;
                                                                          $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                       }
                                                                       else
                                                                       {
                                                                          $notFolio=1;
                                                                          //  $insertRow = false;
                                                                          //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                       }


                                                                    }
                                                                  if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $jointName1 = trim($cell);

                                                                        }else{
                                                                          $jointName1="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $jointName2 = trim($cell);

                                                                          }else{
                                                                            $jointName2="";
                                                                          }
                                                                      }
                                                                      if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $joint1_pan = trim($cell);

                                                                            }else{
                                                                              $joint1_pan="";
                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                $joint2_pan = trim($cell);

                                                                              }else{
                                                                                $joint2_pan="";
                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $guard_pan = trim($cell);

                                                                                }else{
                                                                                  $guard_pan="";
                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                    $sub_boroker = trim($cell);

                                                                                  }else{
                                                                                    $sub_boroker="";
                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                      $mode_holding = trim($cell);

                                                                                    }else{
                                                                                      $mode_holding="";
                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                        $nominee_name1 = trim($cell);

                                                                                      }else{
                                                                                        $nominee_name1="";
                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                          $nom1_relation = trim($cell);

                                                                                        }else{
                                                                                          $nom1_relation ="";
                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                      {
                                                                                        $cell=$rows[$findkey];
                                                                                          if($cell || $cell != '')
                                                                                          {
                                                                                            $nominee_name2 = trim($cell);

                                                                                          }else{
                                                                                            $nominee_name2="";
                                                                                          }
                                                                                      }
                                                                                      if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                        {
                                                                                          $cell=$rows[$findkey];
                                                                                            if($cell || $cell != '')
                                                                                            {
                                                                                              $nom2_relation = trim($cell);

                                                                                            }else{
                                                                                              $nom2_relation ="";
                                                                                            }
                                                                                        }
                                                                                        if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                          {
                                                                                            $cell=$rows[$findkey];
                                                                                              if($cell || $cell != '')
                                                                                              {
                                                                                                $guardian_name= trim($cell);

                                                                                              }else{
                                                                                                $guardian_name="";
                                                                                              }
                                                                                          }
                                                                                          if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                            {
                                                                                              $cell=$rows[$findkey];
                                                                                              $cell=trim(str_replace('/','-', $cell));
                                                                                              if($cell || $cell != '')
                                                                                              {
                                                                                               $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                                  //var_dump($cell);exit;
                                                                                                 // $date->format('Y-m-d');
                                                                                                  if(is_object($date)){
                                                                                                    $folioDate=$date->format('Y-m-d');
                                                                                                  }else{
                                                                                                    $date = new DateTime($cell);
                                                                                                     if(is_object($date)){
                                                                                                       $folioDate=$date->format('Y-m-d');

                                                                                                     }else{

                                                                                                             // $insertRow = false;
                                                                                                             // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                         }

                                                                                                     }

                                                                                                  } else
                                                                                                   {
                                                                                                       $folioDate = null;
                                                                                                       $dob_app = 0;
                                                                                                   }
                                                                                                    // echo $dob;//exit;
                                                                                              }else
                                                                                              {
                                                                                                  $folioDate = null;
                                                                                                  $dob_app = 0;
                                                                                              }
                                      } else {

                                          if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $clientName = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Client Name cannot be empty";
                                              // }
                                          }

                                          if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $panNum = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Pan Number cannot be empty";
                                              // }
                                          }
                                          if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $folio_number = trim($cell);
                                              }
                                              // else
                                              // {
                                              //     $insertRow = false;
                                              //     $impMessage = "Folio Number cannot be empty";
                                              // }
                                          }


                                      }
                                      $countCell++;

                                    }
                                  }

                              }
                              
                              if($countRow != 0)
                              {
                                  if(!$insertRow)
                                  {
                                      $imp_data[$countErrRow][1] = $famName;
                                      $imp_data[$countErrRow][2] = $clientName;
                                      $imp_data[$countErrRow][3] = $panNum;
                                      $imp_data[$countErrRow][4] = $impMessage;
                                      $countErrRow++;
                                      $insertRow = true;
                                      $uploadedStatus = 2;
                                      continue;
                                  }

                                    $dataRows[$countClient] = array(
                                      'name' => $clientName, 'family_id' => $familyId,
                                      'email_id' => $email, 'dob' => $dob, 'dob_app' => $dob_app,
                                      //'password' => sha1($password),
                                      'occupation_id' => $occId, 'head_of_family' => $hof, 'relation_HOF' => $rHof,
                                      'client_type' => $clientTypeId, 'spouse_name' => $spouse,
                                      'anv_date' => $ann, 'anv_app' => $anv_app, 'pan_no' => $panNum,
                                      'passport_no' => $passport, 'add_flat' => $flat, 'add_street' => $street,
                                      'add_area' => $area, 'add_city' => $city, 'add_state' => $state,
                                      'add_pin' => $pin, 'telephone' => $tel, 'mobile' => $mobile,
                                      'date_of_comm' => $comDate, 'children_name' => $child, 'report_order' => 99,
                                      'user_id' => $user_id, 'status' => 1, 'username' => $username,
                                      'client_category' => $category
                                  );
                                    unset($inserted,$getdata);
                                    if(empty($clientAlready) && empty($panAlready)){  //both not available     
                                        if(empty($notFolio)){  // if client name not available but pan not available also folio,and product id not available
                                          echo"fresh client".$clientName.$productId.$folio_number.$pan_no;//exit;
                                        $dataRows[$countClient]['client_id']=$clientId;//@pallavi 2018-26 march
                                          $inserted = $this->Clients_model->add_client_import($dataRows[$countClient]);
                                          $fresh++;
                                    }else{
                                    // echo"in  folio"."but no client no pan  but folio and product id avalilable";
                                      $getdata['folio_number']=$folio_number;
                                      $getdata['productId']=$productId;
                                      $getdata['broker_id']=$brokerID;
                                      $getClientId=$this->Clients_model->getClientId($getdata);
                                      $inserted=$getClientId;
                                    }


                                }
                                elseif(empty($clientAlready) && !empty($panAlready)){ // if pan available and client name not available
                                    //     echo"no client but pan available".$clientName;
                                    //  $getdata['cname']=$clientName;
                                    $getdata['pan']=$panNum;
                                    $getdata['productId']=$productId;
                                    $getdata['broker_id']=$brokerID;
                                    $getClientId=$this->Clients_model->getClientId($getdata);
                                    //       print_r($getClientId);exit;
                                    $inserted=$getClientId;
                                }elseif(!empty($clientAlready) && empty($panAlready)){   // if client name available and pan not available

                                    if(empty($notFolio)){  /// if client name available but pan not available also folio,and product id also not available
                                        //  echo"in not folio".$clientName.$productId.$folio_number.$pan_no;//exit;
                                        $dataRows[$countClient]['client_id']=$clientId;//@pallavi 2018-26 march
                                          $inserted = $this->Clients_model->add_client_import($dataRows[$countClient]);
                                          $fresh++;
                                     }else{                             // if client name available but pan not available also folio,and product same
                                       //  echo"in  folio".$clientName;
                                          $getdata['cname']=$clientName;
                                          $getdata['folio_number']=$folio_number;
                                          $getdata['productId']=$productId;
                                          $getdata['broker_id']=$brokerID;
                                          $getClientId=$this->Clients_model->getClientId($getdata);
                                          $inserted=$getClientId;
                                        }
                                }
                                elseif(!empty($clientAlready) && !empty($panAlready)){ // if both available  
                                    //  echo"Client Already available".$clientName;
                                  $getdata['cname']=$clientName;
                                  $getdata['pan']=$panNum;
                                  $getdata['folio_number']=$folio_number;
                                  $getdata['productId']=$productId;
                                  $getdata['broker_id']=$brokerID;
                                  $getClientId=$this->Clients_model->getClientId($getdata);
                                  //print_r($getClientId);die;
                                  $inserted=$getClientId;
                                }

                                  if(is_array($inserted)) {
                                      $uploadedStatus = 0;
                                      $message = 'Error while inserting records. '.$inserted['message'];
                                      break;
                                  }else{  // insert clients bank details according to its last inserted id.

                                    if($rta_list=='karvy_excel'){
                                      if(!empty($bankBranch)){
                                        $bankBranch=$bankBranch.','.$bank_address_building;
                                      }else{
                                          $bankBranch=$bank_address_building;
                                      }
                                    }

                                    $dataRowsBank[$countClient]=array(
                                      'bank_name'=>$bankName,'folio_number'=>$folio_number,'bank_branch'=>$bankBranch,'bank_acc_no'=>$bankAccNo,
                                      'bank_ifsc'=>$bankIFSC,'bank_account_types'=>$bank_account_types,'bank_address_building'=>$bank_address_building,
                                      'bank_address_road'=>$bank_address_road,'bank_address_area'=>$bank_address_area,
                                      'bank_address_city'=>$bank_address_city,'bank_pincode'=>$bank_pincode,
                                      'bank_state'=>$bank_state,'bank_country'=>$bank_country,'productId'=>$productId,'jointName1'=>$jointName1,
                                      'jointName2'=>$jointName2,'pan_no'=>$pan_no,'joint1_pan'=>$joint1_pan,'joint2_pan'=>$joint2_pan,'guard_pan'=>$guard_pan,
                                      'tax_status'=>$tax_status,'broker_code'=>$broker_code,'sub_boroker'=>$sub_boroker,'client_family_broker_id'=>$brokerID,'occ_name'=>$occ_name,
                                      'mode_holding'=>$mode_holding,'nominee_name1'=>$nominee_name1,'nom1_relation'=>$nom1_relation,'nominee_name2'=>$nominee_name2,
                                      'nom2_relation'=>$nom2_relation,'guardian_name'=>$guardian_name,'folioDate'=>$folioDate
                                      );

                                      $dataRowsBank[$countClient]['client_id']=$inserted;
                                    //  echo $dataRowsBank[$countClient]['client_id'];echo"<br>";
                                      // before inserting client's bank details first check the folio number already exist or not.

                                    //  if($dataRowsBank[$countClient]['client_id']!=0){
                                        $inserted = $this->Clients_model->add_client_bank_details($dataRowsBank[$countClient]);
                                    //  }

                                      // print_r($inserted);die();
                                     // var_dump($inserted);



                                  }

                                  $countClient++;

                                  $famName = ""; $clientName = ""; $panNum = ""; $clientType = "";
                                  $comDate = ""; $occ = ""; $hof = ""; $rHof = ""; $report = "";
                                  $dob = ""; $ann = null; $spouse = ""; $child = ""; $clientId = "";
                                  $familyId = ""; $category = "";
                                  $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
                                  $mobile = ""; $tel = ""; $email = ""; $username = "";
                                  $password = ""; $pan = ""; $passport = "";
                                  $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
                                    $clientAlready=0;$panAlready=0;
                              //  //bank details variables
                              //  $bankName="";$folio_number="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";
                              //  $bank_address_road="";$bank_address_area="";$bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$productId="";
                              //  $jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";$broker_code="";$sub_boroker="";
                              //  $brokerID="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";$guardian_name="";$folioDate="";

                              }
                              if($uploadedStatus == 0)
                                  break;
                              $countRow++;
                          }
                        
                          if($dataRows)
                          {
                            
                              if(is_array($inserted)) {
                                 // var_dump($dataRows);
                           // var_dump($inserted);
                                // echo 'Hi ';
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                              } else {
                                  $this->Common_model->last_import('Client Details', $brokerID, $_FILES["import_clients"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $uploadedStatus = 1;
                                      $message = $fresh++." Client Details Uploaded Successfully";
                                  }
                              }
                          }
                          unset($dataColumns, $dataRows,$dataRowsBank,$inserted,$productId);//exit;
                        }

                    }//end of DBF FILE



                }
            }
            else
            {
                $message = "No file selected";
            }
            if($uploadedStatus == 1)
            {
                $success = array(
                    "title" => "Success!",
                    "text" => $message
                );
                $this->session->set_userdata('success', $success);
            }
            else if ($uploadedStatus == 2)
            {
                $info = array(
                    "title" => "Info for Import!",
                    "text" => 'Few Records were not imported. Please check the table below'
                );
                $this->session->set_userdata('info', $info);
            }
            else
            {
                $error = array(
                    "title" => "Error on uploading!",
                    "text" => $message
                );
                $this->session->set_userdata('error', $error);
            }
        }
        $this->import($imp_data);
    }
     
   
    
    
    //Pallavi - 2017-06-12 - new functions
    function files_size()
    {
        $file_list = array();
        $data=$this->listFolderFiles();
        //print_r($data);
        $total=0;
        $user_id = $this->session->userdata('user_id');
       for($i=1;$i<sizeof($data);$i++)
       {
         $condition=array('client_id' =>$data[$i] ,'user_id'=>$user_id );
         //print_r($condition);
         $client_aar=$this->Clients_model->check_client_id($condition);
        // print_r($client_aar);
         if($client_aar)
         {
                $f='uploads/clients/'.$data[$i].'/';
                $io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
                $size = fgets ( $io, 4096);
                $size = substr ( $size, 0, strpos ( $size, "\t" ) );
                $total=$total+$size;
                pclose ( $io );
         }
       }
       $limit = $this->Doc_model->get_limit_broker($user_id);
       $limit=intval($limit->Doc_upload_limit);
       $output=array('limit' => $limit,'total-size'=> $total);
       return $output;
    }
function listFolderFiles()
{
  $file_list = array();
    error_reporting(0);
      $f='uploads/clients/';

      $ffs = scandir($f);

      foreach($ffs as $ff)
      {
          if($ff != '.' && $ff != '..'){
              if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff);
              array_push($file_list,$ff );
          }
      }
      return($file_list) ;
}
    
    
    function merge(){
  $brokerID = $this->session->userdata('user_id');
//  $data = $this->Clients_model->get_limit($brokerID);
//  $count = $this->Clients_model->count_client($brokerID);
//  $data=intval($data->client_limit);
//  $count = intval($count->count);
 // $data['families'] = $this->Families_model->get_families_broker_dropdown($brokerID);
  $data = $this->fill_form();
  $brokerID = $this->session->userdata('broker_id');
  /*getting only original clients having merge_ref_id=NULL*/
  $cli_condtition = array('c.status' => '1','c.merge_ref_id' =>NULL, 'fam.broker_id' => $brokerID);
  $data['merging_clients'] = $this->Clients_model->get_clients_broker_dropdown_for_merge($cli_condtition);
    /* end getting only original clients having merge_ref_id=NULL*/
  $header['title']='Merge Client';
  $header['css'] = array(
      'assets/users/plugins/form-select2/select2.css',
    'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
     'assets/users/css/bootstrap-multiselect.css',

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
      'assets/users/js/common.js',
     'assets/users/js/jquery.selectlistactions.js',
     'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
     'assets/users/js/dataTables.js',
     'assets/users/js/bootstrap-multiselect.js',
     'assets/users/js/mousetrap.min.js'
  );


  $this->load->view('broker/common/header', $header);
  $this->load->view('broker/client/merge_client_form', $data);
  $this->load->view('broker/master/add_family');
  $this->load->view('broker/master/occupation_add');
  $this->load->view('broker/master/client_type_add');
  $this->load->view('broker/common/notif');
  $this->load->view('broker/common/footer');


}
function clients_merge(){
  $brokerID = $this->session->userdata('user_id');
$chkclient=array();
   $data = json_decode($_POST['checkedclient']);
   foreach($data as $d){
  array_push($chkclient,$d);
  }

  $unchkclient=array();
     $data = json_decode($_POST['uncheckedclient']);
     foreach($data as $d){
    array_push($unchkclient,$d);
    }
//print_r($chkclient);
//print_r($unchkclient);
 $res=$this->Clients_model->merge_client_name($chkclient,$unchkclient,$brokerID);
// if($res==1){
//   $res['suc']='success';
// }
//  echo json_encode($res);
 if($res) {
     $merged = true;
     $res = array(
         "title" => "Client Merged",
         "text" => "Clients merged successfully!",
         "type" => "error"
     );
     $response[] = $res;
 }

 if(!$merged) {
     echo json_encode("ok");
 } else {
     echo json_encode($response);
 }


}

function family_transfer(){

  $brokerID = $this->session->userdata('user_id');
//  $data = $this->Clients_model->get_limit($brokerID);
//  $count = $this->Clients_model->count_client($brokerID);
//  $data=intval($data->client_limit);
//  $count = intval($count->count);
 // $data['families'] = $this->Families_model->get_families_broker_dropdown($brokerID);
  $data = $this->fill_form();
   // $data['families1'] = $this->Families_model->get_families_broker_dropdown($brokerID);
  $brokerID = $this->session->userdata('broker_id');

  /*getting only original clients having merge_ref_id=NULL*/
  $cli_condtition = array('c.status' => '1','c.merge_ref_id' =>NULL, 'fam.broker_id' => $brokerID);
  $data['merging_clients'] = $this->Clients_model->get_clients_broker_dropdown_for_merge($cli_condtition);
    /* end getting only original clients having merge_ref_id=NULL*/
  $header['title']='Merge Client';
  $header['css'] = array(
      'assets/users/plugins/form-select2/select2.css',
    'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
     //'assets/users/css/bootstrap-multiselect.css',
     'assets/users/css/styles.css'
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
      'assets/users/js/common.js',
     'assets/users/js/jquery.selectlistactions.js',
     'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
     'assets/users/js/dataTables.js',
    // 'assets/users/js/bootstrap-multiselect.js'
  );


  $this->load->view('broker/common/header', $header);
  $this->load->view('broker/client/family_transfer_form', $data);
  $this->load->view('broker/master/add_family');
  $this->load->view('broker/master/occupation_add');
  $this->load->view('broker/master/client_type_add');
  $this->load->view('broker/common/notif');
  $this->load->view('broker/common/footer');

}

function client_to_family_transfer(){
  if(isset($_POST)){
  //print_r($_POST);//die();
    $res=$this->Clients_model->update_client_family($_POST);
    if($res==1){
      $success = array(
          "title" => "Family Transfered!",
          "text" => "Selected Clients family has been changed successfully.",
          "type" => "success"
      );
    //  echo json_encode($success);
      $this->session->set_userdata('success', $success);
      redirect('broker/clients/family_transfer');
    }else{
      $error = array(
          "title" => "Client could not be merged!",
          "text" => "could not change Clients family. Please check if there are any records linked to this client.",
          "type" => "error"
      );
    //  echo json_encode($error);
      $this->session->set_userdata('error', $error);
      redirect('broker/clients/family_transfer');
    }

  }

}
function check_family_head(){
  $clientID = $this->input->post('clientID');
    $head = $this->Clients_model->check_family_head($clientID);
      echo json_encode($head);

}

//get familywise clients List
     public function get_clients_familywise(){
    //   $type = $_GET['fnameflag'];
       //echo $type;exit;
       $familyID = $this->input->post('familyID');
       $brokerID = $this->session->userdata('broker_id');
       if($familyID != '') {
           $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $familyID);
           $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
           echo json_encode($clientID);
       } else {
           $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
           $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
           $data = array();
           $num = 10;
           if(isset ($_POST['start']))
               $num = $_POST['start'];
           foreach($clientID as $client)
           {
               $num++;
               $row = array();
            //  $row['client_id']=$client->client_id;
              // $row['client_id'] = '<input type="checkbox" name="cid[]" value="' + $client->client_id + '">';
               $row['client_id'] = '<input type="checkbox" name="cid[]" class="call-checkbox" value="'.$client->client_id.'" id="'.$client->client_id.'">';
               $row['name']=$client->name;
               $row['dob']=$client->dob2;
               $row['pan_no']=$client->pan_no;
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
     }
     function get_clients_familywise_summery(){
       $familyID = $this->input->post('familyID');
       $brokerID = $this->session->userdata('broker_id');
       if($familyID != '') {
         $data['family_id']=$familyID;
       }else{
         $data['family_id']='';
       }
       $data['broker_id']=$brokerID;
       $data['cstatus']=1;
         $clientID = $this->Clients_model->get_clients_familywise_summary($data);
         foreach($clientID as $client)             //pallavi-2017-06-13 //added foreach loop
         {
            $dobTemp = DateTime::createFromFormat('Y-m-d',$client->dob);
            $client->dob = $dobTemp->format('d/m/Y');
           }
         echo json_encode($clientID);

     }
     function get_details_clients(){
       $brokerID = $this->session->userdata('broker_id');
       $arrclient=array();
          $data = json_decode($_POST['clientIDs']);
          foreach($data as $d){
         array_push($arrclient,$d);
         }

       $list = $this->Clients_model->get_selected_client_info($arrclient,$brokerID);
        echo json_encode($list);
     }

    //gets merged client list along with their main client details from database
    public function ajax_list_merged_clients()
    {
      $brokerID = $this->session->userdata('broker_id');
      $list = $this->Clients_model->get_merged_clients_details($brokerID);
       //print_r($list);
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
          $row['dob']=$client->dob;
          $row['pan_no']=$client->pan_no;
          $row['email_id']=$client->email_id;
          $row['username']=$client->username;
          $row['date_of_comm']=$client->date_of_comm;
          $row['client_category']=$client->client_category;
          $row['mobile']=$client->mobile;
          $merging_client_name=$this->Clients_model->fetch_client_nameByID($client->merge_ref_id);
          $row['merge_ref_id']=$merging_client_name;

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
    
    public function get_client_family_mergd_names() {
        $clientID = $this->input->post('clientID');
        if($clientID != '') {
            $familyID = $this->Clients_model->get_client_family_mergd_names($clientID);
            echo json_encode($familyID);
        } else {
            echo json_encode('error');
        }
    }
    
    /*added : pallavi */
    public function get_clients_broker_dropdown_refered() {
        $familyID = $this->input->post('familyID');
        $brokerID = $this->session->userdata('broker_id');
        $mr = $this->input->post('merge');
        //echo $mr;die();
        $brokerID = $this->session->userdata('broker_id');
        if($familyID != '') {
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $familyID);
        } else {
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        }
      //  $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
       $clientID = $this->Clients_model->get_referd_client_family_list($cli_condition);
        echo json_encode($clientID);
    }
    public function get_clients_broker_dropdown_refered_clientwise() { //getting merged clients list based on main client id

        $clientID = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        $mr = $this->input->post('merge');
      //echo $clientID;die();

        if($clientID != '') {
             $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.client_id' => $clientID);
         } else {
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        }
      //  $clientID = $this->Clients_model->get_clients_broker_dropdown($cli_condition);
       $clientID = $this->Clients_model->get_referd_client_duplicate_list($cli_condition);
        echo json_encode($clientID);
    }
    /* end */

}
