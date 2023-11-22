<?php
if(!defined('BASEPATH')) exit('No direct path access allowed');
class Withdraw_funds extends CI_Controller{
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
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Common_model', 'common');
        $this->load->model('Tradings_model', 'trading');
        $this->load->model('Funds_model', 'fund');
        $this->load->model('Bank_accounts_model', 'bank_account');
        $this->load->model('Banks_model', 'bank');
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Withdraw Funds Master';
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
        $this->load->view('broker/withdraw_funds/funds_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
        /*$error_data['heading'] = 'Nothing here!';
        $error_data['message'] = 'There is nothing on this page. Please open links/pages through proper navigation. Do not open pages by changing the URLs.
        <br/><br/>
        <a href="'.base_url('broker/Dashboard').'">Click here to go back to the Homepage/Dashboard</a>';
        $this->load->view('errors/html/error_general.php', $error_data);*/
    }

    //gets all withdraw fund details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fund->get_withdraw_funds(array('wf.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $fund)
        {
            $row = array();
            $num++;
            $row['family_name'] = $fund->family_name;
            $row['client_name'] = $fund->client_name;
            $row['transaction_date'] = $fund->transaction_date;
            $row['amount'] = round($fund->amount);
            $row['cheque_no'] = $fund->cheque_no;
            $row['cheque_date'] = $fund->cheque_date;
            $row['bank_name'] = $fund->bank_name;
            $row['branch'] = $fund->branch;
            $row['account_number'] = $fund->account_number;
            $row['withdraw_from'] = $fund->withdraw_from;
            $row['broker_name'] = $fund->trading_broker_name;
            $row['client_code'] = $fund->client_code;
            $row['mf_type'] = $fund->mf_type;
            $row['add_notes'] = $fund->add_notes;
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
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


    //gets all withdraw only equity fund details from database
    public function ajax_list_equity()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fund->get_withdraw_funds_equity("wf.broker_id='".$brokerID."' and withdraw_from='equity'");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $fund)
        {
            $row = array();
            $num++;
            $row['family_name'] = $fund->family_name;
            $row['client_name'] = $fund->client_name;
            $row['transaction_date'] = $fund->transaction_date;
            $row['amount'] = round($fund->amount);
            $row['cheque_no'] = $fund->cheque_no;
            $row['cheque_date'] = $fund->cheque_date;
            $row['bank_name'] = $fund->bank_name;
            $row['branch'] = $fund->branch;
            $row['account_number'] = $fund->account_number;
            $row['withdraw_from'] = $fund->withdraw_from;
            $row['broker_name'] = $fund->trading_broker_name;
            $row['client_code'] = $fund->client_code;
            $row['mf_type'] = $fund->mf_type;
            $row['add_notes'] = $fund->add_notes;
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                              <a class="btn btn-sm btn-danger disable_btn">
                              <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_withdraw_fund('."'".$fund->withdraw_fund_id."'".')">
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
    //function for withdraw funds add form
    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Withdraw Funds';
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
        //get details of withdraw_funds for the form
        $data = $this->fill_form_add();
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/withdraw_funds/add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/client_bank_account_add', $data);
        $this->load->view('broker/master/client_trading_add', $data);
        $this->load->view('broker/master/bank_add');
        $this->load->view('broker/master/trading_broker_add');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //function for withdraw_funds edit form
    function edit_form()
    {
        if(isset($_GET['id'])) {
            //get details of withdraw_funds for the form
            $data = $this->fill_form_edit($this->input->get('id'));
            if(isset($data['fund_data']) && !empty($data['fund_data'])) {
                //data to pass to header view like page title, css, js
                $header['title']='Edit Withdraw Funds';
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

                //load views
                $this->load->view('broker/common/header', $header);
                $this->load->view('broker/withdraw_funds/edit_form', $data);
                $this->load->view('broker/master/add_family');
                $this->load->view('broker/master/client_bank_account_add', $data);
                $this->load->view('broker/master/client_trading_add', $data);
                $this->load->view('broker/master/bank_add');
                $this->load->view('broker/master/trading_broker_add');
                $this->load->view('broker/common/notif');
                $this->load->view('broker/common/footer');
            } else {
                // the user has come with an id that does not exist
                $data['heading'] = 'Oops! looks like you are lost!';
                $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which does not exist. So please go back.';
                $this->load->view('errors/html/error_404', $data);
            }
        } else {
            // the user has come without an id
            $data['heading'] = 'Oops! looks like you are lost!';
            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    private function fill_form_add()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        // commented out as they are not needed. If needed, please uncomment
        //$data['scrip'] = $this->common->get_scrips();
        $condition = 'broker_id is null or broker_id = "'.$brokerID.'"';
        $data['banks'] = $this->bank->get_banks("(".$condition.") and status = 1"); // get list of all available banks
        $data['bank_account_types'] = $this->bank->get_bank_account_types($condition);
        $data['trading_brokers'] = $this->trading->get_trading_brokers_dropdown($condition);
        //$data['clientCodes'] = $this->trading->get_client_codes_dropdown('user_id = "'.$brokerID.'"');
        return $data;
    }

    private function fill_form_edit($fund_id)
    {
        $brokerID = $this->session->userdata('broker_id');
        $fund_data = $this->fund->get_withdraw_funds('wf.withdraw_fund_id = "'.$fund_id.'" AND wf.broker_id = "'.$brokerID.'"');
        $data['fund_data'] = $fund_data[0];
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);
        $condition = 'broker_id is null or broker_id = "'.$brokerID.'"';
        $data['banks'] = $this->bank->get_banks("(".$condition.") and status = 1"); // get list of all available banks
        $data['bank_account_types'] = $this->bank->get_bank_account_types($condition);
        $data['bank_accounts'] = $this->bank_account->get_client_bank_accounts('b.broker_id is null or b.broker_id = "'.$brokerID.'"');
        $data['trading_brokers'] = $this->trading->get_trading_brokers_dropdown($condition);
        $data['clientCodes'] = $this->trading->get_client_codes_dropdown('user_id = "'.$brokerID.'"');
        // commented out as they are not needed. If needed, please uncomment
        return $data;
    }


    // function to add funds
    function withdraw_funds()
    {
        $data = $this->input->post();
        if(!empty($data)) {
            //first of all, use and remove all the items which are not needed
            $data['added_on'] = date('Y-m-d');

            $trans_date = $data['transaction_date'];
            $trans_temp = DateTime::createFromFormat('d/m/Y', $trans_date);
            $data['transaction_date'] = $trans_temp->format('Y-m-d');

            if(!empty($data['cheque_date'])) {
                $cheque_date = $data['cheque_date'];
                $cheque_date_temp = DateTime::createFromFormat('d/m/Y', $cheque_date);
                $data['cheque_date'] = $cheque_date_temp->format('Y-m-d');
            }

            $data['broker_id'] = $this->session->userdata('broker_id');
            $data['user_id'] = $this->session->userdata('user_id');

            if(empty($data['cheque_no'])) { unset($data['cheque_no']); }
            if(empty($data['cheque_date'])) { unset($data['cheque_date']); }
            if(empty($data['add_notes'])) { unset($data['add_notes']); }
            if(empty($data['mf_type'])) { unset($data['mf_type']); }
            if(empty($data['trading_broker_id']) || empty($data['client_code'])) {
                unset($data['trading_broker_id']);
                unset($data['client_code']);
            }

            if($data['withdraw_from'] == 'Equity') {
                unset($data['mf_type']);
            } elseif($data['withdraw_from'] == 'Mutual Fund') {
                unset($data['trading_broker_id']);
                unset($data['client_code']);
            } else {
                unset($data['trading_broker_id']);
                unset($data['client_code']);
                unset($data['mf_type']);
            }

            //now we will send the data to insert into add_funds
            try
            {
                $inserted = $this->fund->withdraw_funds($data);
                if($inserted) {
                    if(is_array($inserted)) {
                        throw new Custom_exception();
                    } else {
                        $response = array(
                            "title" => "Fund withdraw entry added!",
                            "text" => "Fund withdraw details added successfully.",
                            "type" => "success",
                            "id" => $inserted
                        );
                    }
                } else {
                    $response = array(
                        "title" => "Could not add fund withdraw entry!",
                        "text" => "Fund withdraw details could not be added. ERROR: No row ID fetched back.",
                        "type" => "error"
                    );
                }
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $response = array(
                    "title" => "Could not add fund withdraw entry!",
                    "text" => $e->errorMessage($inserted['code']),
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

    // function to update withdraw_funds
    function update_funds()
    {
        $data = $this->input->post();

        if(!empty($data)) {
            if(isset($data['withdraw_fund_id']) && !empty($data['withdraw_fund_id'])) {
                //first of all, use and remove all the items which are not needed
                $trans_date = $data['transaction_date'];
                $trans_temp = DateTime::createFromFormat('d/m/Y', $trans_date);
                $data['transaction_date'] = $trans_temp->format('Y-m-d');

                if(!empty($data['cheque_date'])) {
                    $cheque_date = $data['cheque_date'];
                    $cheque_date_temp = DateTime::createFromFormat('d/m/Y', $cheque_date);
                    $data['cheque_date'] = $cheque_date_temp->format('Y-m-d');
                }

                if($data['withdraw_from'] == 'Equity') {
                    unset($data['mf_type']);
                } elseif($data['withdraw_from'] == 'Mutual Funds') {
                    unset($data['trading_broker_id']);
                    unset($data['client_code']);
                    $data['client_code']=null;
                    $data['trading_broker_id']=null;
                } else {
                    unset($data['trading_broker_id']);
                    unset($data['client_code']);
                    unset($data['mf_type']);
                    $data['client_code']=null;
                    $data['trading_broker_id']=null;
                    $data['mf_type']=null;
                }
                //var_dump($data);

                if(!isset($data['add_notes']) || empty($data['add_notes'])) {
                    $data['add_notes'] = null;
                }

                $data['broker_id'] = $this->session->userdata('broker_id');
                $data['user_id'] = $this->session->userdata('user_id');
                $data['added_on'] = date('Y-m-d');

                $withdraw_fund_id = $data['withdraw_fund_id'];
                //unset($data['add_fund_id']);

                //now we will send the data to update into withdraw_funds
                try
                {

                    $updated = $this->fund->update_withdraw_funds($data, 'withdraw_fund_id = "'.$withdraw_fund_id.'"');
                    if($updated) {
                        if(is_array($updated)) {
                            throw new Custom_exception();
                        } else {
                            $response = array(
                                "title" => "Withdraw Funds entry updated!",
                                "text" => "Fund withdraw entry details updated successfully.",
                                "type" => "success",
                                "id" => $updated
                            );
                        }
                    } else {
                        $response = array(
                            "title" => "Could not update Withdraw Funds entry!",
                            "text" => "Fund withdraw entry details could not be updated. ERROR: No row ID fetched back.",
                            "type" => "error"
                        );
                    }
                }
                catch(Custom_exception $e)
                {
                    //display custom message
                    $response = array(
                        "title" => "Could not update Withdraw Funds entry!",
                        "text" => $e->errorMessage($updated['code']),
                        "type" => "error"
                    );
                }
            } else {
                $response = array(
                    "title" => "Could not update Withdraw Fund entry!",
                    "text" => "Withdraw Fund ID to be updated was not sent. Kindly refresh the page and try again.",
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

    public function delete_fund()
    {
        try
        {
            $deleted = $this->fund->delete_withdraw_fund(array('withdraw_fund_id' => $this->input->post("id")));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Withdraw Fund entry deleted!",
                    "text" => "The Withdraw Fund entry you selected has been deleted."
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
                "title" => "Could not delete Withdraw Fund entry!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }

}
