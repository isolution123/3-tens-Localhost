<?php
if(!defined('BASEPATH')) exit('No direct path access allowed');
class Equity extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load libraries, helpers
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }

        //load Fixed_deposit_model, 'fd' is object
        $this->load->model('Equity_model', 'eq');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Common_model', 'common');
        $this->load->model('Tradings_model', 'trading');
        $this->load->model('Funds_model', 'fund');
        $this->load->model('Reminders_model', 'reminder');
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Equity Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/equity/equity_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all fixed deposit details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->eq->get_equity(array('eq.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $equity)
        {
            $row = array();
            $num++;
            $row['family_name'] = $equity->family_name;
            $row['client_name'] = $equity->client_name;
            $row['broker_name'] = $equity->trading_broker_name;
            $row['client_code'] = $equity->client_code;
            $row['transaction_date'] = $equity->transaction_date;
            $row['scrip_name'] = $equity->scrip_name;
            $row['scrip_code'] = $equity->scrip_code;
            $row['quantity'] = $equity->quantity;
            $row['purchase_value'] = $equity->purchase_value;
            $row['current_value'] = $equity->current_value;
            $row['apc'] = $equity->apc;
            $row['close_rate'] = $equity->close_rate;
            $row['acquiring_rate'] = round($equity->acquiring_rate);
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {

            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_equity('."'".$equity->equity_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_equity('."'".$equity->equity_transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_equity('."'".$equity->equity_transaction_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger disable_btn">
                    <i class="fa fa-trash-o"></i></a>';
              }
                else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_equity('."'".$equity->equity_transaction_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //gets top 5 negative equity balance
    public function get_negative_equity()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->eq->get_equity_negative("f.broker_id='".$brokerID."' and cb.balance<0");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $equity)
        {
            $row = array();
            $num++;
            $row['client_name'] = $equity->name;
            $row['broker_name'] = $equity->trading_broker_name;
            $row['client_code'] = $equity->client_code;
            $row['balance'] = $equity->balance;

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //function for equity add form
    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Add Equity';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/js/common.js'
        );
        //get details of equity for the form
        $data = $this->fill_form_add();
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/equity/equity_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/client_trading_add');
        $this->load->view('broker/master/trading_broker_add');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //function for equity edit form
    function edit_form()
    {
        if(isset($_GET['id'])) {
            //data to pass to header view like page title, css, js
            $header['title']='Edit Equity';
            $header['css'] = array(
                'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
                'assets/users/plugins/form-select2/select2.css',
                'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
                'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
            );
            $header['js'] = array(
                'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
                'assets/users/js/dataTables.js',
                'assets/users/plugins/form-select2/select2.min.js',
                'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
                'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
                'assets/users/plugins/form-parsley/parsley.min.js',
                'assets/users/plugins/bootbox/bootbox.min.js',
                'assets/users/js/common.js'
            );
            //get details of equity for the form
            $data = $this->fill_form_edit($this->input->get('id'));
            //var_dump($data['eq_data']);
            //load views
            $this->load->view('broker/common/header', $header);
            $this->load->view('broker/equity/equity_edit_form', $data);
            $this->load->view('broker/master/add_family');
            $this->load->view('broker/master/client_trading_add');
            $this->load->view('broker/master/trading_broker_add');
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            // the user has come without an equity id
            $data['heading'] = 'Oops! looks like you are lost';
            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    private function fill_form_add()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);

        // commented out as they are not needed. If needed, please uncomment
        //$data['scrip'] = $this->common->get_scrips();
        $condition = 'broker_id is null or broker_id = "'.$brokerID.'"';
        $data['trading_brokers'] = $this->trading->get_trading_brokers_dropdown($condition);
        //$data['clientCodes'] = $this->trading->get_client_codes_dropdown('user_id = "'.$brokerID.'"');
        return $data;
    }

    private function fill_form_edit($eq_id)
    {
        $brokerID = $this->session->userdata('broker_id');
        $equity_data = $this->eq->get_equity('equity_transaction_id = "'.$eq_id.'"');
        $data['eq_data'] = $equity_data[0];
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['scrip'] = $this->common->get_scrips();
        $condition = 'broker_id is null or broker_id = "'.$brokerID.'"';
        $data['trading_brokers'] = $this->trading->get_trading_brokers_dropdown($condition);
        $data['clientCodes'] = $this->trading->get_client_codes_dropdown('user_id = "'.$brokerID.'"');
        // commented out as they are not needed. If needed, please uncomment
        return $data;
    }

    function get_scrips()
    {
        echo json_encode($this->common->get_scrips());
    }

    // function to get scrip_code by scrip_name and vice versa
    function match_scrips()
    {
        if(isset($_POST['scrip_name'])) {
            $scrip_name = $this->input->post('scrip_name');
            $condition = array('scrip_name'=>$scrip_name);
        } elseif(isset($_POST['scrip_code'])) {
            $scrip_code = $this->input->post('scrip_code');
            $condition = array('scrip_code'=>$scrip_code);
        }
        $scrip = $this->common->get_scrip_detail($condition);
        echo json_encode($scrip);
    }


    // function to add funds - when initial investment is checked
    function add_funds()
    {
        $data = $this->input->post();
        if(!empty($data)) {
            //first of all, use and remove all the items which are not needed
            if(!empty($data['acquiring_rate'])) {
                $rate = $data['acquiring_rate'];
                $data['amount'] = $data['acquiring_rate'] * $data['quantity'];
            } else {
                $rate = $data['eq_curr_rate'];
                $data['amount'] = $data['eq_curr_rate'] * $data['quantity'];
            }
            $data['add_notes'] = $data['scrip_name'].', Quantity: '.$data['quantity'].' @ '.$rate;
            $data['added_on'] = date('Y-m-d');

            $trans_date = $data['transaction_date'];
            $trans_temp = DateTime::createFromFormat('d/m/Y', $trans_date);
            $data['transaction_date'] = $trans_temp->format('Y-m-d');

            $data['shares_app'] = 1;
            $data['broker_id'] = $this->session->userdata('broker_id');
            $data['user_id'] = $this->session->userdata('user_id');

            unset($data['scrip_name']);
            unset($data['scrip_code']);
            unset($data['trading_broker_name']);
            unset($data['eq_amt']);
            unset($data['acquiring_rate']);
            unset($data['eq_curr_rate']);
            unset($data['quantity']);
            unset($data['eq_curr_amt']);
            unset($data['initial_inv']);
            unset($data['initial_investment']);
            unset($data['eq_track']);
            if(isset($data['equity_transaction_id'])) {
                unset($data['equity_transaction_id']);
            }

            //now we will send the data to insert into add_funds
            try
            {
                $inserted = $this->fund->add_funds($data);
                if($inserted) {
                    if(is_array($inserted)) {
                        throw new Custom_exception();
                    } else {
                        $response = array(
                            "title" => "New Fund added!",
                            "text" => "New Fund details added successfully.",
                            "type" => "success"
                        );
                    }
                } else {
                    $response = array(
                        "title" => "Could not add fund!",
                        "text" => "Fund details could not be added. ERROR: No row ID fetched back.",
                        "type" => "error"
                    );
                }
                echo json_encode($response);
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not add fund!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $data['heading'] = 'Oops looks like you are lost';
            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    // function to add equity
    function add_equity()
    {
        $data = $this->input->post();
        if(!empty($data)) {
            //first of all, use and remove all the items which are not needed
            if(empty($data['acquiring_rate'])) {
                $data['acquiring_rate'] = $data['eq_curr_rate'];
            }

            $trans_date = $data['transaction_date'];
            $trans_temp = DateTime::createFromFormat('d/m/Y', $trans_date);
            $data['transaction_date'] = $trans_temp->format('Y-m-d');

            $data['broker_id'] = $this->session->userdata('broker_id');
            $data['user_id'] = $this->session->userdata('user_id');
            $data['added_on'] = date('Y-m-d');

            //change proper data for tracking & initial_investment
            if(isset($data['initial_investment']))
                $data['tracking'] = $data['initial_investment'];
            else
                $data['tracking'] = 0;
            if(isset($data['initial_inv']))
                $data['initial_investment'] = $data['initial_inv'];
            else
                $data['initial_investment'] = 0;

            unset($data['trading_broker_name']);
            unset($data['eq_amt']);
            unset($data['eq_curr_rate']);
            unset($data['eq_curr_amt']);
            unset($data['initial_inv']);
            unset($data['eq_track']);

            //now we will send the data to insert into equities
            try
            {
                $inserted = $this->eq->add_equity_form($data);
                if($inserted) {
                    if(is_array($inserted)) {
                        throw new Custom_exception();
                    } else {
                        $response = array(
                            "title" => "New Equity added!",
                            "text" => "New Equity details added successfully.",
                            "type" => "success",
                            "id" => $inserted
                        );
                    }
                } else {
                    $response = array(
                        "title" => "Could not add equity!",
                        "text" => "Equity details could not be added. ERROR: No row ID fetched back.",
                        "type" => "error"
                    );
                }
                echo json_encode($response);
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not add equity!",
                    "text" => $e->errorMessage($inserted['code']),
                    "type" => "error"
                );
                echo json_encode($response);
            }
        } else {
            $data['heading'] = 'Oops looks like you are lost';
            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    // function to update equity
    function update_equity()
    {
        $data = $this->input->post();
        //var_dump($data);
        if(!empty($data)) {
            if(isset($data['equity_transaction_id']) && !empty($data['equity_transaction_id'])) {
                //first of all, use and remove all the items which are not needed
                if(empty($data['acquiring_rate'])) {
                    $data['acquiring_rate'] = $data['eq_curr_rate'];
                }

                $trans_date = $data['transaction_date'];
                $trans_temp = DateTime::createFromFormat('d/m/Y', $trans_date);
                $data['transaction_date'] = $trans_temp->format('Y-m-d');

                $data['broker_id'] = $this->session->userdata('broker_id');
                $data['user_id'] = $this->session->userdata('user_id');
                $data['added_on'] = date('Y-m-d');

                //change proper data for tracking & initial_investment
                if(isset($data['initial_investment']))
                    $data['tracking'] = $data['initial_investment'];
                else
                    $data['tracking'] = 0;
                if(isset($data['initial_inv']))
                {
                    $data['initial_investment'] = $data['initial_inv'];
                  unset($data['acquiring_rate']);
              }
                else
                    unset($data['initial_investment']);

                unset($data['trading_broker_name']);
                unset($data['eq_amt']);
                unset($data['eq_curr_rate']);
                unset($data['eq_curr_amt']);
                unset($data['initial_inv']);
                unset($data['eq_track']);

                //take equity_transaction_id in another variable
                $eq_trans_id = $data['equity_transaction_id'];

                //now we will send the data to update into equity
                try
                {
                    $updated = $this->eq->update_equity($data, 'equity_transaction_id = "'.$eq_trans_id.'"');
                    if($updated) {
                        if(is_array($updated)) {
                            throw new Custom_exception();
                        } else {
                            $response = array(
                                "title" => "Equity updated!",
                                "text" => "Equity details updated successfully.",
                                "type" => "success",
                                "id" => $updated
                            );
                        }
                    } else {
                        $response = array(
                            "title" => "Could not update equity!",
                            "text" => "Equity details could not be updated. ERROR: No row ID fetched back.",
                            "type" => "error"
                        );
                    }
                }
                catch(Custom_exception $e)
                {
                    //display custom message
                    $response = array(
                        "title" => "Could not update equity!",
                        "text" => $e->errorMessage($updated['code']),
                        "type" => "error"
                    );
                    //echo json_encode($response);
                }
            } else {
                $response = array(
                    "title" => "Could not add equity!",
                    "text" => "Equity ID to be updated was not sent. Kindly refresh the page and try again.",
                    "type" => "error"
                );
            }
            echo json_encode($response);
        } else {
            $data['heading'] = 'Oops looks like you are lost';
            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    public function delete_equity()
    {
        try
        {
            $deleted = $this->eq->delete_equity(array('equity_transaction_id' => $this->input->post("id")));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Equity deleted!",
                    "text" => "The Equity you selected has been deleted."
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
                "title" => "Could not delete Equity!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }



    //function for importing Bhav Copy
    function import_bhav_copy()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database.
        $scrip_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        //main array to hold all values
        $all_data = array();
        //variable to delete previous policy records scrip_rates
        $delScripCode = "";
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_scrip"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_scrip"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    $uploadedStatus = 1; //set default status to 1
                    //get tmp_name of file
                    $file = $_FILES["import_scrip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);

                    //get sheet
                    $sheet = $objPHPExcel->getActiveSheet();
                    //get only the Cell Collection
                    $cell_collection = $sheet->getCellCollection();

                    //temp variables to hold values
                    $scrip_code = "";
                    $scrip_name = "";
                    $close_rate = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        $data_value = $sheet->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'SC_CODE' || strtoupper($data_value) == 'SC_NAME' || strtoupper($data_value) == 'CLOSE')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            } else {
                                $message = 'Columns Specified in Excel are not in correct format. Please check if the spelling of column names are correct.';
                                $uploadedStatus = 0;
                                break;
                            }
                        }
                        else if($countExcel < 3)
                        {
                            echo $countExcel;
                            $message = 'Columns Specified in Excel are not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'SC_CODE')
                                    $scrip_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'SC_NAME')
                                    $scrip_name = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'CLOSE')
                                    $close_rate = $data_value;

                                if($countCol == 3)
                                {
                                    /*$where = array(
                                        'scrip_code'=>$scrip_code,
                                        'scrip_name'=>$scrip_name
                                    );
                                    $this->eq->delete_scrip_data($where);*/
                                    $data = array(
                                        'scrip_code'=>$scrip_code,
                                        'scrip_name'=>$scrip_name,
                                        'close_rate'=>$close_rate
                                    );
                                    $all_data[] = $data;
                                    $countCol = 0;
                                    /*if($this->eq->add_scrip_data($data)) {
                                        continue;
                                    } else {
                                        $scrip_data[$row][1] = $scrip_code;
                                        $scrip_data[$row][2] = $scrip_name;
                                        $scrip_data[$row][3] = $close_rate;
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                    }*/
                                }
                            }
                        }
                    }

                    if($uploadedStatus !== 0) {
                        //$deleted = $this->eq->delete_scrip_data('1=1');
                        $deleted = true;
                        if($deleted === true) {
                            $inserted = $this->eq->add_scrip_data($all_data);
                            if(is_array($inserted)) {
                                $uploadedStatus = 2;
                                $message = $inserted['code'].' - '.$inserted['message'];
                            } else {
                                $this->common->last_import('Bhav Copy', $brokerID , $_FILES["import_scrip"]["name"], $user_id);
                                $uploadedStatus = 1;
                                $message = "Bhav Copy Uploaded Successfully";
                                $countCol = 0;
                            }
                        } else {
                            $uploadedStatus = 0;
                            $message = 'Could not delete and insert records';
                        }
                    }
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
                    //"text" => 'Few Records were not imported please check the table below'
                    "text" => $message
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

        //call the view
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
        $header['title'] = 'Import Bhav Copy';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js'
        );
        $this->load->view('broker/common/header', $header);
        $data['scrip_data'] = $scrip_data;
        $this->load->view('broker/equity/bhav_copy', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //function for importing Cash Balance
    function import_cash_balance()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database.
        $scrip_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        $cash_bal_data = null;

        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_cash_bal"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_cash_bal"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    $uploadedStatus = 1;
                    //get tmp_name of file
                    $file = $_FILES["import_cash_bal"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get sheet
                    $sheet = $objPHPExcel->getActiveSheet();
                    //get only the Cell Collection
                    $cell_collection = $sheet->getCellCollection();

                    //temp variables to hold values
                    $client_code = "";
                    $ledger = "";
                    $party_name = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');

                    //get the share_negative value from db
                    $negative = $this->trading->get_share_negative();
                    $negative = $negative->share_negative;
                    if($negative > 0) {
                        $negative=-$negative;
                    }

                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        $data_value = $sheet->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'CLIENT CODE' || strtoupper($data_value) == 'LEDGER DR/CR' || strtoupper($data_value) == 'PARTY NAME')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            } else {
                                $message = 'Columns Specified in Excel are not in correct format. Please check if the spelling of column names are correct.';
                                $uploadedStatus = 0;
                                break;
                            }
                        }
                        else if($countExcel < 3)
                        {
                            //echo $countExcel;
                            $message = 'Columns Specified in Excel are not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'CLIENT CODE')
                                    $client_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'LEDGER DR/CR')
                                    $ledger = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'PARTY NAME')
                                    $party_name = $data_value;

                                if($countCol == 3)
                                {
                                    //check if cash_balance exists for client
                                    $cash_bal_where = 'client_code = "'.$client_code.'" AND (broker_id = "'.$brokerID.'" OR broker_id is NULL)';
                                    $cash_bal_exists = $this->trading->check_client_broker_exists($cash_bal_where);
                                    if(!$cash_bal_exists) {
                                        $cash_bal_data[$row][1] = $client_code;
                                        $cash_bal_data[$row][2] = $ledger;
                                        $cash_bal_data[$row][3] = $party_name;
                                        $cash_bal_data[$row][4] = 'Client Code does not exist';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    if($negative<=0 && $ledger<=$negative)
                                    {
                                        $clientName = null;
                                        $clientID = null;
                                        //get client id and name from db
                                        $client_info_where = 'client_code = "'.$client_code.'" AND (u.broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';
                                        $client_info = $this->client->get_client_family_info_by_code($client_info_where);
                                        if($client_info) {
                                            $clientName = $client_info->name;
                                            $clientID = $client_info->client_id;
                                        }

                                        //insert data into reminder table
                                        $reminder_data = array(
                                            'reminder_type'=> "Shares Negative Balance",
                                            'client_id' => $clientID,
                                            'client_name' => $clientName,
                                            'reminder_date' => date('Y-m-d'),
                                            'reminder_message' => "Negative Balance of Rs. " . $ledger . " in equity trading account, CLIENT CODE:" . $client_code,
                                            'broker_id' => $brokerID
                                        );
                                        $this->reminder->add_reminder($reminder_data);
                                    }

                                    //now update balance amt(ledger) in client_brokers table
                                    $data = array('balance'=>$ledger);
                                    $where = 'client_code = "'.$client_code.'" AND (u.broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';

                                    $updated = $this->trading->update_client_brokers($data, $where);
                                    if($updated === true) {
                                        $countCol = 0;
                                        continue;
                                    } else {
                                        $cash_bal_data[$row][1] = $client_code;
                                        $cash_bal_data[$row][2] = $ledger;
                                        $cash_bal_data[$row][3] = $party_name;
                                        $cash_bal_data[$row][4] = $updated['message'];
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                    }
                                }
                            }
                        }
                    }

                    if($uploadedStatus !== 0) {
                        $this->common->last_import('Cash Balance', $brokerID , $_FILES["import_cash_bal"]["name"], $user_id);

                        $message = "Cash Balance Details Uploaded Successfully";
                        $countCol = 0;
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }

            //set the notification for success or error
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
                    "text" => 'Few Records were not imported please check the table below'
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

        $header['title'] = 'Import Cash Balance';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js'
        );
        $this->load->view('broker/common/header', $header);
        $data['cash_bal_data'] = $cash_bal_data;
        $this->load->view('broker/equity/import_cash_balance', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }


    //function for importing Holdings
    function import_holding()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database.
        $scrip_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        $holding_data = null;
        $bulk_data = array();

        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_holding"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_holding"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    $uploadedStatus = 1;
                    //get tmp_name of file
                    $file = $_FILES["import_holding"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get sheet
                    $sheet = $objPHPExcel->getActiveSheet();
                    //get only the Cell Collection
                    $cell_collection = $sheet->getCellCollection();

                    //temp variables to hold values
                    $party_code = "";
                    $scrip_code = "";
                    $scrip_name = "";
                    $tot_qty = "";
                    $party_name = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');

                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        $data_value = $sheet->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'PARTY CODE' || strtoupper($data_value) == 'SCRIPT CODE' || strtoupper($data_value) == 'SCRIPT NAME' || strtoupper($data_value) == 'TOTAL QUANTITY' || strtoupper($data_value) == 'PARTY NAME')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            } else {
                                $message = 'Columns Specified in Excel are not in correct format. Please check if the spelling of column names are correct.';
                                $uploadedStatus = 0;
                                break;
                            }
                        }
                        else if($countExcel < 5)
                        {
                            //echo $countExcel;
                            $message = 'Columns Specified in Excel are not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'PARTY CODE')
                                    $party_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'SCRIPT CODE')
                                    $scrip_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'SCRIPT NAME')
                                    $scrip_name = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'TOTAL QUANTITY')
                                    $tot_qty = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'PARTY NAME')
                                    $party_name = $data_value;

                                if($countCol == 5)
                                {
                                    //check if broker code exists for client
                                    $broker_where = 'client_code = "'.$party_code.'" AND (broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';
                                    $broker_exists = $this->trading->check_client_broker_exists($broker_where);
                                    $trading_broker_id = null;
                                    if(!$broker_exists) {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $scrip_name;
                                        $holding_data[$row][4] = $tot_qty;
                                        $holding_data[$row][5] = $party_name;
                                        $holding_data[$row][6] = 'Client Code does not exist';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    } else {
                                        /*if($broker_exists->broker_id == NULL || $broker_exists->broker_id == "") {
                                            $trading_broker_id = $broker_exists->user_id;
                                        } else {
                                            $trading_broker_id = $broker_exists->broker_id;
                                        }*/
                                        $trading_broker_id = $broker_exists->broker;
                                    }

                                    $info_where = 'client_code = "'.$party_code.'" AND (u.broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';
                                    $info = $this->client->get_client_family_info_by_code($info_where);
                                    if($info) {
                                        $clientID = $info->client_id;
                                        $familyID = $info->family_id;
                                    } else {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $scrip_name;
                                        $holding_data[$row][4] = $tot_qty;
                                        $holding_data[$row][5] = $party_name;
                                        $holding_data[$row][6] = 'Client/Family info by Client Code not found';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    $scrip_where = 'scrip_code = "'.trim($scrip_code).'"';
                                    $scrip_info = $this->common->get_scrip_detail($scrip_where);
                                    if($scrip_info) {
                                        $acquiring_rate = $scrip_info->close_rate;
                                    } else {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $scrip_name;
                                        $holding_data[$row][4] = $tot_qty;
                                        $holding_data[$row][5] = $party_name;
                                        $holding_data[$row][6] = 'Script Code not found.';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    $delete_where = 'client_code = "'.$party_code.'" AND broker_id = "'.$brokerID.'" AND tracking != "1"';
                                    $deleted = $this->eq->delete_equity($delete_where);

                                    //now insert equity data into Equities table
                                    $data = array(
                                        'client_id' => $clientID,
                                        'family_id' => $familyID,
                                        'trading_broker_id' => $trading_broker_id,
                                        'client_code' =>$party_code,
                                        'scrip_code' => $scrip_code,
                                        'scrip_name' => $scrip_name,
                                        'quantity' => $tot_qty,
                                        'initial_investment' => 0,
                                        //'acquiring_rate' => $acquiring_rate, not needed
                                        'user_id' => $user_id,
                                        'broker_id' => $brokerID,
                                        'transaction_date' => date('Y-m-d'),
                                        'added_on' => date('Y-m-d')
                                    );

                                    $bulk_data[] = $data;

                                    $countCol = 0;
                                    continue;
                                }
                            }
                        }
                    }

                    if($uploadedStatus !== 0) {
                        $inserted = $this->eq->add_equity($bulk_data);
                        if(!is_array($inserted)) {
                            $uploadedStatus == 1;
                        } else {
                            $message = $inserted['message'];
                            $uploadedStatus = 2;
                        }
                        $this->common->last_import('Holding Transactions', $brokerID , $_FILES["import_holding"]["name"], $user_id);

                        $message = "Holding Details Uploaded Successfully";
                        $countCol = 0;
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }

            //set notification for success or error
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
                    "text" => 'Few Records were not imported please check the table below'
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

        //call the view
        $header['title'] = 'Import Holding';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js'
        );
        $this->load->view('broker/common/header', $header);
        $data['holding_data'] = $holding_data;
        $this->load->view('broker/equity/import_holding', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    
     function import_holding_apc()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database.
        $scrip_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        $holding_data = null;
        $bulk_data = array();
        $bulk_update_data= array();

        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_holding_apc"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_holding_apc"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    $uploadedStatus = 1;
                    //get tmp_name of file
                    $file = $_FILES["import_holding_apc"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get sheet
                    $sheet = $objPHPExcel->getActiveSheet();
                    //get only the Cell Collection
                    $cell_collection = $sheet->getCellCollection();

                    //temp variables to hold values
                    $party_code = "";
                    $scrip_code = "";
                    $tot_qty = "";
                    $tot_apc = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');

                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $sheet->getCell($cell)->getColumn();
                        $row = $sheet->getCell($cell)->getRow();
                        $data_value = $sheet->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'PARTY CODE' || strtoupper($data_value) == 'SCRIPT CODE' || strtoupper($data_value) == 'TOTAL QUANTITY' || strtoupper($data_value) == 'TOTAL APC')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            } else {
                                $message = 'Columns Specified in Excel are not in correct format. Please check if the spelling of column names are correct.'.$data_value;
                                $uploadedStatus = 0;
                                break;
                            }
                        }
                        else if($countExcel < 4)
                        {
                            //echo $countExcel;
                            $message = 'Columns Specified in Excel are not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'PARTY CODE')
                                    $party_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'SCRIPT CODE')
                                    $scrip_code = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'TOTAL QUANTITY')
                                    $tot_qty = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'TOTAL APC')
                                    $tot_apc = $data_value;

                                if($countCol == 4)
                                {
                                    //check if broker code exists for client
                                    $broker_where = 'client_code = "'.$party_code.'" AND (broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';
                                    $broker_exists = $this->trading->check_client_broker_exists($broker_where);
                                    $trading_broker_id = null;
                                    if(!$broker_exists) {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $tot_qty;
                                        $holding_data[$row][4] = $tot_apc;
                                        $holding_data[$row][5] = 'Client Code does not exist';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    } else {
                                        /*if($broker_exists->broker_id == NULL || $broker_exists->broker_id == "") {
                                            $trading_broker_id = $broker_exists->user_id;
                                        } else {
                                            $trading_broker_id = $broker_exists->broker_id;
                                        }*/
                                        $trading_broker_id = $broker_exists->broker;
                                    }

                                    $info_where = 'client_code = "'.$party_code.'" AND (u.broker_id = "'.$brokerID.'" OR u.id = "'.$brokerID.'")';
                                    $info = $this->client->get_client_family_info_by_code($info_where);
                                    if($info) {
                                        $clientID = $info->client_id;
                                        $familyID = $info->family_id;
                                    } else {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $tot_qty;
                                        $holding_data[$row][4] = $tot_apc;
                                        $holding_data[$row][5] = 'Client/Family info by Client Code not found';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    $scrip_where = 'scrip_code = "'.trim($scrip_code).'"';
                                    $scrip_info = $this->common->get_scrip_detail($scrip_where);
                                    if($scrip_info) {
                                        $acquiring_rate = $scrip_info->close_rate;
                                    } else {
                                        $holding_data[$row][1] = $party_code;
                                        $holding_data[$row][2] = $scrip_code;
                                        $holding_data[$row][3] = $tot_qty;
                                        $holding_data[$row][4] = $tot_apc;
                                        $holding_data[$row][5] = 'Script Code not found.';
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    $broker_where = 'client_code = "'.$party_code.'" AND broker_id = "'.$brokerID.'" and scrip_code = "'.$scrip_code.'"';
                                    
                                     $broker_exists = $this->eq->check_equity_apc($broker_where);
                                    $equity_apc_id = null;
                                    if($broker_exists){
                                        $equity_apc_id = $broker_exists->id;
                                        //now insert equity data into Equities table
                                        $data = array(
                                            'id'=>$equity_apc_id,
                                            'client_id' => $clientID,
                                            'client_code' =>$party_code,
                                            'scrip_code' => $scrip_code,
                                            'quantity' => $tot_qty,
                                            'apc' => $tot_apc,
                                            'added_by' => $user_id,
                                            'broker_id' => $brokerID,
                                        );
                                        $bulk_update_data[] = $data;
                                    }
                                    else
                                    {
                                          $data = array(
                                            'client_id' => $clientID,
                                            'client_code' =>$party_code,
                                            'scrip_code' => $scrip_code,
                                            'quantity' => $tot_qty,
                                            'apc' => $tot_apc,
                                            'added_by' => $user_id,
                                            'broker_id' => $brokerID,
                                        );
                                        $bulk_data[] = $data;
                                    }
                                    

                                    $countCol = 0;
                                    continue;
                                }
                            }
                        }
                    }

                    if($uploadedStatus !== 0) {
                        
                        if(count($bulk_data)>0)
                        {
                            
                            $inserted = $this->eq->add_equity_apc($bulk_data);
                            if(!is_array($inserted)) {
                                $uploadedStatus == 1;
                            } else {
                                $message = $inserted['message'];
                                $uploadedStatus = 2;
                            }  
                        }
                        try
                        {  
                         
                            
                            foreach($bulk_update_data as $data1)
                            {
                                
                                  $data = array(
                                            'client_id' => $data1['client_id'],
                                            'client_code' =>$data1['client_code'],
                                            'scrip_code' => $data1['scrip_code'],
                                            'quantity' => $data1['quantity'],
                                            'apc' => $data1['apc'],
                                            'updated_by' => $user_id,
                                            'broker_id' => $brokerID,
                                        );
                                        
                                $updated = $this->eq->update_equity_apc($data, 'id = "'.$data1['id'].'"');
                                if($updated) {
                                   $uploadedStatus == 1;
                                } else {
                                    $message = 'Could not update equity apc!';
                                    $uploadedStatus = 2;
                                }
                            }
                            
                        }
                        catch(Custom_exception $e)
                        {
                           
                        }
                       
                        $message = "Holding APC Details Uploaded Successfully";
                        $countCol = 0;
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }

            //set notification for success or error
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
                    "text" => 'Few Records were not imported please check the table below'
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

        //call the view
        $header['title'] = 'Import Holding APC';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-jasnyupload/fileinput.js'
        );
        $this->load->view('broker/common/header', $header);
        $data['holding_data'] = $holding_data;
        $this->load->view('broker/equity/import_apc', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }


    /* Reports part */
    function equity_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Equity/Shares Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/equity/equity_report', $data);
        $this->load->view('broker/common/footer');
    }
    
    function equity_report_with_apc()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Equity/Shares Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/equity/equity_report_with_apc', $data);
        $this->load->view('broker/common/footer');
    }

    function get_equity_report()
    {
        // Include the main Financial class (search for installation path).
        require_once('application/third_party/Financial.php');
        $financial = new Financial();

        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $client_code = $this->input->post('client_code');
        $xirr = $this->input->post('xirr');
        $cheque = $this->input->post('cheque');
        $brokerID = $this->session->userdata('broker_id');
        $userID = $this->session->userdata('user_id');
        $flag=0;
        $chart_where='';
         if($this->input->post('reportDate')!='')
        {

            $reportDate_temp = DateTime::createFromFormat('d/m/Y',$this->input->post('reportDate'));
             if(is_object($reportDate_temp)){$reportDate = $reportDate_temp->format('Y/m/d');}
             if($reportDate!=date('Y/m/d'))
             {
                $flag=1;
             }
        }
        else
        {
            
            $reportDate=date('Y/m/d');
        }   
        
        $type = 'client';
        $where = "";
        $values_where2='';
        if($client_id != null && $client_id != '')
        {
            $where = array('clientID'=> $client_id);
            $values_where = "where c.client_id = '".$client_id."'";
            $chart_where="where c.client_id = '".$client_id."'";
            $values_where2=$values_where;
            if($client_code != null && $client_code != '')
            {
                $where['clientCode'] = $client_code;
                $type = 'clientCode';
                
                $values_where .= " and e.client_code = '".$client_code."'";
                $values_where2 .= " and client_code = '".$client_code."'";
            } else {
                $where['clientCode'] = '';
                $type = 'clientCode';
            }
            $where['famID'] = '';
            $where['user_id'] = $userID;

            $xirr_where = array(
                'clientID'=>$client_id,
                'famID'=>'',
                'type'=>'client_code'
            );

            //get clientName
            $clientInfo = $this->client->get_client_info($client_id);
            if($clientInfo)
            {
                $clientName = $clientInfo->name;
            }
        }
        else
        {
            $where = array(
                'clientID'=> $client_id,
                'clientCode'=> $client_code,
                'famID'=> $family_id,
                'user_id'=> $userID
            );

            $xirr_where = array(
                'clientID'=>'',
                'famID'=>$family_id,
                'type'=>'family'
            );

            $values_where = "where c.client_id IN (select client_id from clients where family_id = '".$family_id."')";
            $values_where2=$values_where;
            $chart_where=" where c.family_id = '".$family_id."'";

            //get family Name and client Names
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $family_id);
            $familyName = $this->family->get_family_by_id($family_id);
            $familyName = $familyName->name;
            $clients = $this->client->get_clients_broker_dropdown($cli_condition);

            $type = 'family';
        }
        $logo = "";
        $status = false;
        
        //get the equity/scrip values
      //  print_r($values_where);die();
    if( $flag==0)
    {
        
        $eq_values = $this->eq->get_equity_values($values_where);
        
        $eq_values_cap = $this->eq->get_equity_values_cap_wise($values_where);
        
        $eq_values_industry = $this->eq->get_equity_values_industry_wise($values_where);
        
        $eq_balance = $this->eq->get_equity_broker_balance($values_where2);
     //   print_r($values_where2);die();
    }
    else
    {
         
        $chart_where=$chart_where. " and date(e.CreatedDTStamp) <='".$reportDate."'";
        $values_where1=$values_where. " and date(DTStamp) = '".$reportDate."'";
        $values_where2=$values_where. " and date(DTStamp) = '".$reportDate."'";
        
        $eq_values = $this->eq->get_equity_values_history($values_where1);
        
        $eq_values_cap = $this->eq->get_equity_values_cap_wise_history($values_where1);
        
        $eq_values_industry = $this->eq->get_equity_values_industry_wise_history($values_where1);
        
        $eq_balance = $this->eq->get_equity_broker_balance_history($values_where2);
    }
    
    $eq_chart_history = $this->eq->equities_monthly_summary_for_chart($chart_where);

       // echo json_encode(var_dump($eq_values));die();
        //get the sum of all the values (to calculate %)
        $value_total = 0;
        foreach($eq_values as $value) {
            $value_total += $value->value;
        }
        
        //get values of all funds, if cheque option selected
      //  print_r($where);die();
        if($cheque === 1 || $cheque === '1') {
            if( $flag==0)
            {
            $eq_rep = $this->eq->get_equity_report($where);
            }
            else
            {
                    $where['reportDate'] = $reportDate;
            $eq_rep = $this->eq->get_equity_report_history($where);
            }
        }
        //var_dump($eq_rep);
    //print_r($eq_rep);die();
        
        
        //var_dump($eq_balance);

        if($eq_values !== false && !empty($eq_values)) {
            if($xirr === 1 || $xirr === '1') {
                $cash_flows = array();
                $date_stamps = array();
                $day_diffs = array();
                $xirr_data = $this->eq->get_xirr_data($xirr_where);
                if($xirr_data !== false) {
                    foreach($xirr_data as $row) {
                        $cash_flows[] = $row->cash_flows;
                        $temp = DateTime::createFromFormat('Y-m-d',$row->data_date);
                        $date_stamps[] = $temp->getTimestamp();
                        $day_diffs[] = $row->day_diff;
                    }
                    $xirr_value = $financial->XIRR($cash_flows, $date_stamps, 0.1);
                }
            }

            unset($_SESSION['eq_report']);
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
            if($type == 'client')
            {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'client_id' => $client_id, 'client_name' => $clientName);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values,'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            } else if($type == 'clientCode') {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'client_id' => $client_id, 'client_name' => $clientName, 'clientCode' => $client_code);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            } else if($type == 'family') {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'family_id' => $family_id, 'family_name' => $familyName, 'clients' => $clients);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry,'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                    
                }
            } else {
                die(json_encode(array('Status'=> $status, 'message'=> 'Error in type of report!')));
            }
            
            $eq_rep_array['broker_id'] = $brokerID;
            $eq_rep_array['eq_chart_history'] = $eq_chart_history;
            
            
            $this->session->set_userdata('eq_report', $eq_rep_array);
            $status = true;
            echo json_encode(array('Status'=> $status));
        } else {
            echo json_encode(array('Status'=> $status, 'message'=> 'No equity records found!'));
        }
    }
    
    function get_equity_report_with_apc()
    {
        // Include the main Financial class (search for installation path).
        require_once('application/third_party/Financial.php');
        $financial = new Financial();

        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $client_code = $this->input->post('client_code');
        $xirr = $this->input->post('xirr');
        $cheque = $this->input->post('cheque');
        $brokerID = $this->session->userdata('broker_id');
        $userID = $this->session->userdata('user_id');
        $flag=0;
        $chart_where='';
         if($this->input->post('reportDate')!='')
        {

            $reportDate_temp = DateTime::createFromFormat('d/m/Y',$this->input->post('reportDate'));
             if(is_object($reportDate_temp)){$reportDate = $reportDate_temp->format('Y/m/d');}
             if($reportDate!=date('Y/m/d'))
             {
                $flag=1;
             }
        }
        else
        {
            
            $reportDate=date('Y/m/d');
        }   
        
        $type = 'client';
        $where = "";
        $values_where2='';
        if($client_id != null && $client_id != '')
        {
            $where = array('clientID'=> $client_id);
            $values_where = "where c.client_id = '".$client_id."'";
            $values_where2=$values_where;
            $chart_where="where c.client_id = '".$client_id."'";
            if($client_code != null && $client_code != '')
            {
                $where['clientCode'] = $client_code;
                $type = 'clientCode';
                
                $values_where .= " and e.client_code = '".$client_code."'";
                $values_where2 .= " and client_code = '".$client_code."'";
            } else {
                $where['clientCode'] = '';
                $type = 'clientCode';
            }
            $where['famID'] = '';
            $where['user_id'] = $userID;

            $xirr_where = array(
                'clientID'=>$client_id,
                'famID'=>'',
                'type'=>'client_code'
            );

            //get clientName
            $clientInfo = $this->client->get_client_info($client_id);
            if($clientInfo)
            {
                $clientName = $clientInfo->name;
            }
        }
        else
        {
            $where = array(
                'clientID'=> $client_id,
                'clientCode'=> $client_code,
                'famID'=> $family_id,
                'user_id'=> $userID
            );

            $xirr_where = array(
                'clientID'=>'',
                'famID'=>$family_id,
                'type'=>'family'
            );

            $values_where = "where c.client_id IN (select client_id from clients where family_id = '".$family_id."')";
            $chart_where=" where c.family_id = '".$family_id."'";
            $values_where2 = $values_where;
            //get family Name and client Names
            $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $family_id);
            $familyName = $this->family->get_family_by_id($family_id);
            $familyName = $familyName->name;
            $clients = $this->client->get_clients_broker_dropdown($cli_condition);

            $type = 'family';
        }
        $logo = "";
        $status = false;
        
        //get the equity/scrip values
      //  print_r($values_where);die();
    if( $flag==0)
    {
        $eq_values = $this->eq->get_equity_values($values_where);
        
        $eq_values_cap = $this->eq->get_equity_values_cap_wise($values_where);
        
        $eq_values_industry = $this->eq->get_equity_values_industry_wise($values_where);
        
        $eq_balance = $this->eq->get_equity_broker_balance($values_where2);
    }
    else
    {
         
        $chart_where=$chart_where. " and date(e.CreatedDTStamp) <='".$reportDate."'";
        $values_where1=$values_where. " and date(DTStamp) = '".$reportDate."'";
        $values_where2=$values_where. " and date(DTStamp) = '".$reportDate."'";
        
        $eq_values = $this->eq->get_equity_values_history($values_where1);
        
        $eq_values_cap = $this->eq->get_equity_values_cap_wise_history($values_where1);
        
        $eq_values_industry = $this->eq->get_equity_values_industry_wise_history($values_where1);
        
        $eq_balance = $this->eq->get_equity_broker_balance_history($values_where2);
    }
    // print_r($values_where2);die();
    $eq_chart_history = $this->eq->equities_monthly_summary_for_chart($chart_where);
    
       // echo json_encode(var_dump($eq_values));die();
        //get the sum of all the values (to calculate %)
        $value_total = 0;
        foreach($eq_values as $value) {
            $value_total += $value->value;
        }
        
        //get values of all funds, if cheque option selected
      //  print_r($where);die();
        if($cheque === 1 || $cheque === '1') {
            if( $flag==0)
            {
            $eq_rep = $this->eq->get_equity_report($where);
            }
            else
            {
                    $where['reportDate'] = $reportDate;
            $eq_rep = $this->eq->get_equity_report_history($where);
            }
        }
        //var_dump($eq_rep);
    //print_r($eq_rep);die();
        
        
        //var_dump($eq_balance);

        if($eq_values !== false && !empty($eq_values)) {
            if($xirr === 1 || $xirr === '1') {
                
                
                $cash_flows = array();
                $date_stamps = array();
                $day_diffs = array();
                $xirr_data = $this->eq->get_xirr_data($xirr_where);
                
                if($xirr_data !== false) {
                    foreach($xirr_data as $row) {
                        $cash_flows[] = $row->cash_flows;
                        $temp = DateTime::createFromFormat('Y-m-d',$row->data_date);
                        $date_stamps[] = $temp->getTimestamp();
                        $day_diffs[] = $row->day_diff;
                    }
                    $xirr_value = $financial->XIRR($cash_flows, $date_stamps, 0.1);
                    
                }
                
            }

            unset($_SESSION['eq_report']);
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
            
            if($type == 'client')
            {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'client_id' => $client_id, 'client_name' => $clientName);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values,'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            } else if($type == 'clientCode') {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'client_id' => $client_id, 'client_name' => $clientName, 'clientCode' => $client_code);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry, 'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                }
            } else if($type == 'family') {
                $eq_info = array('logo' => $logo, 'report_type' => $type, 'family_id' => $family_id, 'family_name' => $familyName, 'clients' => $clients);
                $eq_rep_array = array('report_info'=>$eq_info, 'eq_values_data'=>$eq_values, 'eq_values_cap_data'=>$eq_values_cap,'eq_values_industry_data'=>$eq_values_industry,'balance'=>$eq_balance, 'total_value'=>$value_total);
                if(isset($xirr_value)) {
                    $eq_rep_array['xirr'] = $xirr_value;
                }
                if(isset($eq_rep)) {
                    $eq_rep_array['eq_rep_data'] = $eq_rep;
                    
                }
            } else {
                die(json_encode(array('Status'=> $status, 'message'=> 'Error in type of report!')));
            }
            
            $eq_rep_array['broker_id'] = $brokerID;
            $eq_rep_array['eq_chart_history'] = $eq_chart_history;
            
            
            $this->session->set_userdata('eq_report', $eq_rep_array);
            $status = true;
            echo json_encode(array('Status'=> $status));
        } else {
            echo json_encode(array('Status'=> $status, 'message'=> 'No equity records found!'));
        }
    }

    function export_to_pdf()
    {
        ini_set('memory_limit', '512M');
        //echo html_entity_decode($_POST['htmlData']);
        if(!(isset($_POST['htmlData']) && !empty($_POST['htmlData']))) {
            echo "<script type='text/javascript'>
                alert('Unauthorized Access. Get Outta Here!');
                window.top.close();  //close the current tab
              </script>";

        } else {
            /*$css_data = '<style type="text/css">
                    table td, table th {font-size: 8px;}
                    .title { width:100%; line-height:28px; font-size:15px; font-weight:bold; text-align:center; border:2px double black; }
                    .info { font-size: 12px; font-weight: lighter; border:none; }
                    .head-row { background-color: #003F7D; color: #fff; font-weight:bold}
                    .dataTotal {font-weight: bold}
                    .no-border {border-width: 0px;}
                </style>';*/
            $css_data = '<style type="text/css">
                        table { width:100% }
                        table td {font-size: 12px; padding:2px; text-align:center; color:#4d4d4d; }
                        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                        .border {border: 1px solid #4d4d4d;}
                        .amount { text-align:left; text-indent: 20px; }
                        .noWrap { white-space: nowrap; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:15px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 12px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                        .normal {font-weight: normal;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .no-border {border-width: 0px; border-color:#fff;}
                        .client-code { text-align: left; font-size: 14px; }
                        .client-name { text-align: left; font-size: 16px; }
                    </style>';
            $title_data = $this->input->post('titleData');
            $eq_data = $this->input->post('htmlData');
            $logo = $this->input->post('logo');
            $report_name = $this->input->post('report_name');
            $industry_data= $this->input->post('industry_data');
            /** Error reporting */
          
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/tcpdf/tcpdf.php');

            // create new PDF document
            $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle('Equity Report');
            $pdf->SetSubject('Equity Report');
            $pdf->SetKeywords('equity, shares, report');

            $title = '';
            // set default header data
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
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

            $pdf->AddPage();

            // output the HTML content
           $pdf->writeHTML($css_data.$title_data, true, false, true, false, '');
            
             // reset pointer to the last page
            $x=10;$y=50;
            if(isset($_POST['line_chart']) && $_POST['line_chart']!='')
            {
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                $imgdata = base64_decode($_POST['line_chart']);    
             
            $pdf->SetXY($x, $y);
            $pdf->Image('@'.$imgdata, '', '', 130, 80, '', '', 'T', false, 300, '', false, false, 2, false, false, false);
            
            }
            if(isset($_POST['pie_chart']) && $_POST['pie_chart']!='' )
            {
                if(isset($_POST['line_chart']))
                {
                    $x+=160;    
                }
            $imgdata = base64_decode($_POST['pie_chart']);    
            $pdf->SetXY($x, $y+2);
            $pdf->Image('@'.$imgdata, '', '', 120, 80, '', '', 'T', false, 300, '', false, false, 2, false, false, false);
            $y+=85;
            }
            
            
            // output the HTML content
            $pdf->SetXY(10, $y);
            $pdf->writeHTML($css_data.$industry_data.$eq_data, true, false, true, false, '');
            
            // reset pointer to the last page
            $pdf->lastPage();
ob_end_clean();
            //Close and output PDF document
            //$pdf->Output('Equity Portfolio.pdf', 'D');
            $pdf->Output($report_name.'.pdf', 'D');
            //$pdf->Output('Equity Portfolio.pdf', 'I');

            //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
        }
    }

    function export_to_excel()
    {
        ini_set('memory_limit', '512M');
        if(!(isset($_POST['htmlData']) && !empty($_POST['htmlData']))) {
            echo "<script type='text/javascript'>
                alert('Unauthorized Access. Get Outta Here!');
                window.top.close();  //close the current tab
              </script>";

        } else {
            ob_start();
            $htmlData = $this->input->post('htmlData');
            $sheetName = $this->input->post('name');
            $report_name = $this->input->post('report_name');

            //remove all rupee symbols from data, as it does not display properly in Excel
            $htmlData = str_replace("₹","",$htmlData);

            //load the excel library
            $this->load->library('Excel');

            // Load the table view into a variable
            //$html = $this->load->view('broker/report/equity_report_view_family', $htmlData, true);

            // Put the html into a temporary file
            $tmpfile = time().'.html';
            file_put_contents($tmpfile, $htmlData);

            // Read the contents of the file into PHPExcel Reader class
            $objPHPExcel = new PHPExcel();
            $reader = PHPExcel_IOFactory::createReader('HTML');
            $reader->loadIntoExisting($tmpfile, $objPHPExcel);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName); // Change sheet's title if you want

            // Auto size columns for each worksheet
            for($col = 'A'; $col !== 'Z'; $col++) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }

            // Set headers for Excel file type
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            //header('Content-Type: application/vnd.ms-excel'); // header for .xlxs file
            //header('Content-Disposition: attachment;filename=Equity Portfolio.xlsx'); // specify the download file name
            header('Content-Disposition: attachment;filename='.$report_name.'.xlsx'); // specify the download file name
            header('Cache-Control: max-age=0');

            // Pass to writer and output as needed
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();

            $objWriter->save('php://output');
            //$objWriter->save('Equity Portfolio.xlsx');

            // Delete temporary file
            unlink($tmpfile);
            //ob_clean();
            //ob_flush();
            exit;


            /*$htmlData = $this->input->post('htmlData');
            $sheetName = $this->input->post('name');
            $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        
                        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
                        <title>TESTING</title>
                        </head>
                        <style type="text/css">
                            body {
                                font-family:Verdana, Arial, Helvetica, sans-serif;
                                font-size:12px;
                                margin:0px;
                                padding:0px;
                            }
                        </style>
                        <html>
                        <body>';
                            $content .= $htmlData;
                        $content .= '</body>
                        </html>';
            //header("Content-type: application/x-msdownload");
            //header('Content-Disposition: attachment; filename="filename.xls"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            header('Content-Disposition: attachment;filename=Equity Portfolio.xlsx'); // specify the download file name
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $content;*/
        }
    }
}
