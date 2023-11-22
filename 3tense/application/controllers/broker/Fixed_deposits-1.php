<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Fixed_deposits extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helpers
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        $this->load->helper('url');

        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['broker_id']))
        {
            redirect('broker');
        }
        //load Fixed_deposit_model, 'fd' is object
        $this->load->model('Fixed_deposits_model', 'fd');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Fd_investment_types_model', 'inv');
        $this->load->model('Fd_companies_model', 'comp');
        $this->load->model('Advisers_model', 'adv');
        $this->load->model('Banks_model', 'bank');
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Common_model', 'common');
    }
    //fixed deposit list page
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Fixed Deposit Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
        );

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/fixed_deposit/fd_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    //gets all fixed deposit details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fd->get_fixed_deposit(array('fdt.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $fd)
        {
            $row = array();
            $num++;
            $row['family_name'] = $fd->family_name;
            $row['client_name'] = $fd->client_name;
            $row['transaction_date'] = $fd->transaction_date;
            $row['fd_inv_type'] = $fd->fd_inv_type;
            $row['fd_comp_name'] = $fd->fd_comp_name;
            $row['fd_method'] = $fd->fd_method;
            $row['ref_number'] = $fd->ref_number;
            $row['issued_date'] = $fd->issued_date;
            $row['amount_invested'] = round($fd->amount_invested);
            $row['interest_rate'] = $fd->interest_rate.'%';
            $row['maturity_date'] = $fd->maturity_date;
            $row['maturity_amount'] = round($fd->maturity_amount);
            $row['nominee'] = $fd->nominee;
            $row['status'] = $fd->status;
            $row['adviser_name'] = $fd->adviser_name;
            $row['adjustment'] = $fd->adjustment;

            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->fd->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }
     //gets all fixed deposit details from database
    public function ajax_list_top_new()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID,'adv.held_type' => 'Held'));
        $list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID));
        //print_r($list);

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $fd)
        {
            $row = array();
            $num++;
            $row['family_name'] = $fd->family_name;
            $row['client_name'] = $fd->client_name;
            $row['transaction_date'] = $fd->transaction_date;
            $row['fd_inv_type'] = $fd->fd_inv_type;
            $row['fd_comp_name'] = $fd->fd_comp_name;
            $row['fd_method'] = $fd->fd_method;
            $row['ref_number'] = $fd->ref_number;
            $row['issued_date'] = $fd->issued_date;
            $row['amount_invested'] = round($fd->amount_invested);
            $row['interest_rate'] = $fd->interest_rate.'%';
            $row['maturity_date'] = $fd->maturity_date;
            $row['maturity_amount'] = round($fd->maturity_amount);
            $row['nominee'] = $fd->nominee;
            $row['status'] = $fd->status;
            $row['adviser_name'] = $fd->adviser_name;
            $row['adjustment'] = $fd->adjustment;

            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
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
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->fd->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }
    //gets top 5 maturity fixed deposit details from database
    public function ajax_list_top_mat()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fd->get_fixed_deposit_mat(array('fdt.broker_id' => $brokerID,'fdt.maturity_date>=' => date('Y-m-d'), 'fdt.status' =>'Active'));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $fd)
        {
            $row = array();
            $num++;
            $row['family_name'] = $fd->family_name;
            $row['client_name'] = $fd->client_name;
            $row['transaction_date'] = $fd->transaction_date;
            $row['fd_inv_type'] = $fd->fd_inv_type;
            $row['fd_comp_name'] = $fd->fd_comp_name;
            $row['fd_method'] = $fd->fd_method;
            $row['ref_number'] = $fd->ref_number;
            $row['issued_date'] = $fd->issued_date;
            $row['amount_invested'] = round($fd->amount_invested);
            $row['interest_rate'] = $fd->interest_rate.'%';
            $row['maturity_date'] = $fd->maturity_date;
            $row['maturity_amount'] = round($fd->maturity_amount);
            $row['nominee'] = $fd->nominee;
            $row['status'] = $fd->status;
            $row['adviser_name'] = $fd->adviser_name;
            $row['adjustment'] = $fd->adjustment;

            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
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
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->fd->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }
    //function for fixed deposit add form
    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Add Fixed Deposit';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/js/common.js'
        );
        //get details of fixed deposit for the form
        $data = $this->fill_form();
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/fixed_deposit/fd_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/add_adviser');
        $this->load->view('broker/master/add_fd_inv');
        $this->load->view('broker/master/add_fd_comp');
        $this->load->view('broker/master/bank_add');
        $this->load->view('broker/master/client_bank_account_add');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    //add fd details to database
    function add_fd()
    {
        //echo 'Sudarsh';
        $data = $_POST;
        if(!isset($data['adjustment_flag']))
            $data['adjustment_flag'] = 0;
        if(!isset($data['int_round_off']))
            $data['int_round_off'] = 0;
        $compName = $data['fd_comp_name'];
        $invName = $data['fd_inv_name'];
        $intAmt = $data['intAmt'];
        unset($data['intAmt'], $data['int_mode'], $data['adj_flag'], $data['fd_comp_name'], $data['fd_inv_name']);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $this->session->userdata('broker_id');
        $data['status'] = 'Active';

        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['issued_date']);
        $data['issued_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['maturity_date']);
        $data['maturity_date'] = $date->format('Y-m-d');
        if(isset($data['inv_cheque_date']) && !empty($data['inv_cheque_date'])) {
            $date = DateTime::createFromFormat('d/m/Y', $data['inv_cheque_date']);
            $data['inv_cheque_date'] = $date->format('Y-m-d');
        }

        $client_id = $data['client_id'];
        $result_client = $this->client->get_client_info($client_id);
        $client_name = $result_client->name;

        $cond = "`ref_number` = '".trim($data['ref_number'])."' AND (`broker_id` = '".$data['broker_id']."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->fd->check_duplicate('fd_transactions',$cond);
        if(!$isDuplicate) {
            try
            {
                $transID = $this->fd->add_fixed_deposit($data);
                //if there is any error
                if(isset($transID['code']))
                {
                    throw new Custom_exception();
                }
                $rem_data = array(
                    'reminder_type'=>'Fixed Deposit Reminder',
                    'client_id' => $client_id,
                    'client_name' => $client_name,
                    'reminder_date' => $data['maturity_date'],
                    'broker_id' => $this->session->userdata('broker_id'),
                    'reminder_message' => $invName.' '.$compName
                );
                //don't need to add reminder on adding FD, uncomment below 1 line if required
                //$remStatus = $this->rem->add_reminder($rem_data);
                //if there is any error
                if(isset($remStatus['code']))
                {
                    throw new Custom_exception();
                }

                $message = array(
                    'status' => 'success',
                    'title'=> 'New Fixed Deposit Added!',
                    'text' => 'Fixed Deposit Details added successfully',
                    'transID' => $transID
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($transID['code']));
            }
            if($data['fd_method'] === 'Non-Cumulative')
            {
                $this->add_interest_details($data, $transID);
                $data['intAmt'] = $intAmt;
                $newMat = $this->additional_interest($data, $transID);

                //calculate extra interest amount, and add it to Maturity amount
                /*$new_mat_amt = $this->add_remaining_interest_amount($data['ref_number'],$data['broker_id'],$intAmt,$data['maturity_amount']);
                $data_int = array('maturity_amount'=> $new_mat_amt);
                $whereCon = array(
                    'fd_transaction_id' => $transID,
                    'broker_id' => $data['broker_id']
                );
                try
                {
                    $status = $this->fd->update_fixed_deposit($data_int, $whereCon, FALSE);
                    //if there is any error
                    if(isset($status['code']))
                    {
                        throw new Custom_exception();
                    }*/
                //add new maturity amount to array for sending it to FD View
                $message["newMatAmt"] = $newMat;
                /*}
                catch(Custom_exception $e)
                {
                    //display custom message
                    $message = array("status" => 0, 'title' => 'Error while updating', 'text' => 'Error in updating New Maturity Amount... '.$e->errorMessage($status['code']));
                }*/
            } else {
                $tot_mat = $intAmt + $data['amount_invested'];
                $data_int = array(
                    'maturity_amount'=> $tot_mat,
                );
                $whereCon = array(
                    'fd_transaction_id' => $transID,
                    'broker_id' => $data['broker_id']
                );
                try
                {
                    $status = $this->fd->update_fixed_deposit($data_int, $whereCon, FALSE);;
                    //if there is any error
                    if(isset($status['code']))
                    {
                        throw new Custom_exception();
                    }
                    $message["newMatAmt"] = $tot_mat;
                    //echo $tot_mat.' - '.$intAmt;
                }
                catch(Custom_exception $e)
                {
                    //display custom message
                    $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
                }
            }
        } else {
            $message = array(
                "type" => "error",
                "title" => "Reference Number already exists!",
                "text" => "The Reference Number you entered already exists. Please change it."
            );
        }

        echo json_encode($message);
    }
    function edit_form()
    {
        if(isset($_GET['id']))
        {
            $fd_id = $_GET['id'];
            $broker_id = $this->session->userdata('broker_id');
            $condition = array(
                'fd_transaction_id' => $fd_id,
                'fdt.broker_id' => $broker_id
            );
            $data = $this->fill_form();
            //get fixed deposit for the form
            $result_fd = $this->fd->get_fixed_deposit($condition);
            $header['title'] = 'Edit Fixed Deposit - '.$fd_id;
            if($result_fd)
            {
                $data['fd'] = $result_fd;
                $header['css'] = array(
                    'assets/users/plugins/form-select2/select2.css'
                );
                $header['js'] = array(
                    'assets/users/plugins/form-parsley/parsley.min.js',
                    'assets/users/demo/demo-formvalidation.js',
                    'assets/users/plugins/form-validation/jquery.validate.min.js',
                    'assets/users/plugins/form-select2/select2.min.js',
                    'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
                    'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
                    'assets/users/plugins/bootbox/bootbox.min.js',
                    'assets/users/js/common.js'
                );
                $this->load->view('broker/common/header', $header);
                $this->load->view('broker/fixed_deposit/fd_edit_form', $data);
                $this->load->view('broker/master/add_family');
                $this->load->view('broker/master/add_adviser');
                $this->load->view('broker/master/add_fd_inv');
                $this->load->view('broker/master/add_fd_comp');
                $this->load->view('broker/master/bank_add');
                $this->load->view('broker/master/client_bank_account_add');
                $this->load->view('broker/common/notif');
                $this->load->view('broker/common/footer');
            }
            else
            {
                $data['heading'] = 'Oops looks like you are lost';
                $data['message'] = 'Get Outta here';
                $this->load->view('errors/html/error_404', $data);
            }
        }
    }
    //edit fixed deposit details to database
    function edit_fd()
    {
        $data = $_POST;
        $userID = $this->session->userdata('user_id');
        $brokerID = $this->session->userdata('broker_id');
        if(!isset($data['adjustment_flag']))
            $data['adjustment_flag'] = 0;
        if(!isset($data['int_round_off']))
            $data['int_round_off'] = 0;
        $transID = $data['hTransID'];
        $clientID = $data['client_id'];
        $invName = $data['fd_inv_name'];
        $client_id = $data['client_id'];
        $client_name = $data['client_name'];
        $data['user_id'] = $userID;
        unset($data['hTransID'], $data['hInvID'], $data['hCompID'], $data['hInvBankID'], $data['hInvAcc'], $data['hMatBankID'], $data['hMatAcc'],
        $data['hPayID'], $data['hNominee'], $data['hAdviser'], $data['family_name'], $data['family_id'], $data['client_id'], $data['client_name'],
        $data['fd_method'], $data['interest_mode'], $data['issued_date'], $data['amount_invested'], $data['interest_rate'], $data['intAmt'],
        $data['fd_comp_name'], $data['fd_inv_name'], $data['hStatus'], $data['adj_flag']);

        $cond = "`ref_number` = '".trim($data['ref_number'])."' AND `fd_transaction_id` != '$transID' AND (`broker_id` = '".$brokerID."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->fd->check_duplicate('fd_transactions',$cond);
        if(!$isDuplicate) {
            try
            {
                if($data['hPreMature'] == "1")
                {
                    $date = DateTime::createFromFormat('d/m/Y', $data['maturity_date']);
                    $data['maturity_date'] = $date->format('Y-m-d');
                    $data['status'] = 'Premature';

                    $delCon = array('interest_date >=' => $data['maturity_date'], 'fd_transaction_id'=>$transID);
                    $status_ex = $this->fd->delete_fd_interest($delCon);
                    //if there is any error
                    if(isset($status_ex['code']))
                    {
                        throw new Custom_exception();
                    }
                    $remData = array(
                        'reminder_type'=>'Pre Closed Fixed Deposit',
                        'client_id' => $client_id,
                        'client_name' => $client_name,
                        'reminder_date' => date('Y-m-d'),
                        'broker_id' => $brokerID,
                        'reminder_message' => $invName.', Ref. No. '.$data['ref_number'].' is PRE-MATURED on '.$data['maturity_date'].', Amounting Rs. '.$data['maturity_amount']
                    );
                    $status_ex = $this->rem->add_reminder($remData);
                    //if there is any error
                    if(isset($status_ex['code']))
                    {
                        throw new Custom_exception();
                    }
                }
                else
                {
                    unset($data['maturity_date']);
                }
                unset($data['hPreMature']);
                $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
                $data['transaction_date'] = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $data['inv_cheque_date']);
                $data['inv_cheque_date'] = $date->format('Y-m-d');

                $whereCon = array(
                    'fd_transaction_id' => $transID,
                    'broker_id' => $brokerID
                );
                $status_ex = $this->fd->update_fixed_deposit($data, $whereCon);
                //if there is any error
                if(isset($status_ex['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'Fixed Deposit Updated !',
                    'text' => 'Fixed Deposit Details updated successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status_ex['code']));
            }
        } else {
            $message = array(
                "type" => "error",
                "title" => "Reference Number already exists!",
                "text" => "The Reference Number you entered already exists. Please change it."
            );
        }
        echo json_encode($message);
    }
    function delete_fd()
    {
        $fd_id = $this->input->post('fd_id');
        $broker_id = $this->session->userdata('broker_id');
        $where = array('fd_transaction_id' => $fd_id, 'broker_id' => $broker_id);
        try
        {
            $status = $this->fd->delete_fd_interest(array('fd_transaction_id'=> $fd_id));
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $status = $this->fd->delete_fixed_deposit($where);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Fixed Deposit Deleted!',
                'text' => 'Fixed Deposit Details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }
    private function additional_interest($data, $transID)
    {
        $mat_date = $data['maturity_date'];
        $int_round_off = $data['int_round_off'];
        $int_mode = $data['interest_mode'];
        //$tempMatDate = strtotime($mat_date);
        $tempMatDate = $mat_date;
        //var_dump($mat_date);
        $amt_inv = $data['amount_invested'];
        $mat_amt = $data['maturity_amount'];
        $int_rate = $data['interest_rate'];
        $tot_int = $data['intAmt'];
        $brokerID = $data['broker_id'];
        $tempMatDate = DateTime::createFromFormat("Y-m-d",$tempMatDate );
        $year = $tempMatDate->format("Y");
        $month = $tempMatDate->format("m");
        //$year = date('Y', $tempMatDate);
        //$month = date('n', $tempMatDate);
        // var_dump($year);
        // var_dump($month);
        $total_days = 365;
        /*if($int_round_off == 1)
        {
            if(date('L', strtotime($year.'-03-31')))
                $total_days = 366;
            if($tempMatDate != strtotime($year.'-03-31') && $tempMatDate != strtotime($year.'-06-30') && $tempMatDate != strtotime($year.'-09-30') && $tempMatDate != strtotime($year.'-12-31'))
            {
                /*if($month <= 3)
                {
                    $year = date('Y', strtotime($mat_date. ' - 1 year'));
                    $int_date = $year.'-12-31';
                }
                else if ($month <= 6)
                {
                    $int_date = $year.'-03-31';
                }
                else if($month <= 9)
                {
                    $int_date = $year.'-06-30';
                }
                else
                {
                    $int_date = $year.'-09-30';
                }*/

                /*if($int_mode == "Annually")
                {
                    if($month <= 3)
                        $int_date = $year.'-03-31';
                    else
                        $int_date = ($year+1).'-03-31';
                } elseif($int_mode == "Half-yearly") {
                    if($month <= 6)
                        $int_date = $year.'-06-30';
                    else
                        $int_date = ($year).'-12-31';
                } elseif($int_mode == "Quarterly") {
                    if($month <= 3)
                    {
                        $int_date = $year.'-03-31';
                    }
                    else if ($month <= 6)
                    {
                        $int_date = $year.'-06-30';
                    }
                    else if($month <= 9)
                    {
                        $int_date = $year.'-09-30';
                    }
                    else
                    {
                        $int_date = $year.'-12-31';
                    }
                } elseif($int_mode == "Monthly") {
                    $int_date_temp = new DateTime($year.'-'.$month.'-1');
                    $int_date_temp->modify('last day of last month');
                    $int_date = $int_date_temp->format('Y-m-d');
                }
            }
            $num_of_days = $tempMatDate - strtotime($int_date);
            $num_of_days = floor($num_of_days/86400);
            $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);


        } else {

        }*/

        //if we get sum of interests, we can calculate remaining interest
        $last_interest = $this->fd->get_interest_details(array('fi.fd_transaction_id'=>$transID));
        if($last_interest) {
            $curr_int_amt = $last_interest[0]->interest_amount;
            $int_amt = $tot_int - $curr_int_amt;
            //else we get the last interest date and calculate using remaining days
        } else {
            $last_int_date = $this->fd->get_fd_interest(array('fi.fd_transaction_id'=>$transID), 'fi.interest_date desc');
            if($last_int_date) {
                $int_date = $last_int_date[0]->interest_date;
            } else {
                $int_date = date('Y-m-d', $tempMatDate);
            }

            //$num_of_days = $tempMatDate - strtotime($int_date);
            $num_of_days = $tempMatDate - $int_date;
            //var_dump($num_of_days);
            $num_of_days = floor($num_of_days/86400);
            $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
        }


        $tot_mat = $mat_amt + $int_amt;
        if($tot_mat < $mat_amt) {
            $tot_mat = $mat_amt;
        }
        $data_int = array(
            'maturity_amount'=> $tot_mat,
        );
        $whereCon = array(
            'fd_transaction_id' => $transID,
            'broker_id' => $brokerID
        );
        try
        {
            $status = $this->fd->update_fixed_deposit($data_int, $whereCon, FALSE);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

            //get updated mat_amt from db
            $fd = $this->fd->get_fixed_deposit(array('fdt.fd_transaction_id'=>$transID, 'fdt.broker_id'=>$brokerID));
            if($fd) {
                $tot_mat = $fd[0]->maturity_amount;
            } else {
                $tot_mat = round($tot_mat, 2);
            }
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        return $tot_mat;
    }
    private function add_interest_details($data, $transID)
    {
        //echo 'interest';
        $issueDate = $data['issued_date'];
        $mat_date = $data['maturity_date'];
        $int_mode = $data['interest_mode'];
        $amt_inv = $data['amount_invested'];
        $int_rate = $data['interest_rate'];
        $int_round_off = $data['int_round_off'];
        $total_days = 365;
        //$tempIssueDate = strtotime($issueDate);
        $tempIssueDate = $issueDate;
        // $month = date('n', $tempIssueDate);
        // $year = date('Y', $tempIssueDate);
        $tempIssueDate = DateTime::createFromFormat("Y-m-d",$tempIssueDate );
        $year = $tempIssueDate->format("Y");
        $month = $tempIssueDate->format("m");
        $tempIssueDate0 = $tempIssueDate->format("m");
        $tempIssueDat = $tempIssueDate->format("Y-m-d");
        try
        {
            if($int_round_off == 1)
            {

                if(date('L', $tempIssueDate0))
                    $total_days = 366;

                if($int_mode == "Annually")
                {
                    if($month <= 3)
                        $int_date = $year.'-03-31';
                    else
                        $int_date = ($year+1).'-03-31';
                    if($issueDate != $int_date) {
                        //$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                        $tempindate = new DateTime($int_date);
                        $tempisdate = new DateTime($tempIssueDat);
                        $diff=$tempindate->diff($tempisdate);

                        $num_of_days = $diff->days;
                        // $num_of_days = ($int_date - $tempIssueDat); ///this give number of days with time
                        // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
                        $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $int_date,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    $issueDate = $int_date;
                    $int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
                    //var_dump($issueDate);
                    //var_dump($mat_date);
                    while($issueDate <= $mat_date)
                    {
                      $issueDate = new DateTime($issueDate);
                      $issueDate->modify('+1 year');
                      $issueDate = $issueDate->format('Y-m-d');
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
                        //var_dump($issueDate);
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }


                } elseif($int_mode == "Half-yearly") {
                    if($month <= 6)
                        $int_date = $year.'-06-30';
                    else
                        $int_date = ($year).'-12-31';

                    if($issueDate != $int_date) {
                        // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                        // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day

                        $tempindate = new DateTime($int_date);
                        $tempisdate = new DateTime($tempIssueDat);
                        $diff=$tempindate->diff($tempisdate);

                        $num_of_days = $diff->days;
                        $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $int_date,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
                    while($issueDate <= $mat_date)
                    {
                        // $issueDate0 = new DateTime('@'.strtotime($issueDate));
                        // $issueDate0->modify('last day of +6 month');
                        // $issueDate = $issueDate0->format('Y-m-d');
                        $issueDate = new DateTime($issueDate);
                        $issueDate->modify('last day of +6 month');
                        $issueDate = $issueDate->format('Y-m-d');
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }

                } elseif($int_mode == "Quarterly") {
                    if($month <= 3)
                    {
                        $int_date = $year.'-03-31';
                    }
                    else if ($month <= 6)
                    {
                        $int_date = $year.'-06-30';
                    }
                    else if($month <= 9)
                    {
                        $int_date = $year.'-09-30';
                    }
                    else
                    {
                        $int_date = $year.'-12-31';
                    }

                    if($issueDate != $int_date) {
                        // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                        // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
                        $tempindate = new DateTime($int_date);
                        $tempisdate = new DateTime($tempIssueDat);
                        $diff=$tempindate->diff($tempisdate);

                        $num_of_days = $diff->days;

                        $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $int_date,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
                    while($issueDate <= $mat_date)
                    {
                        // $issueDate0 = new DateTime('@'.strtotime($issueDate));
                        // $issueDate0->modify('last day of +3 month');
                        // $issueDate = $issueDate0->format('Y-m-d');
                        $issueDate = new DateTime($issueDate);
                        $issueDate->modify('last day of +3 month');
                        $issueDate = $issueDate->format('Y-m-d');
                        //$issueDate = date('Y-m-d', $issueDate0);
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' =>  $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }

                } elseif($int_mode == "Monthly") {
                    $int_date_temp = new DateTime($year.'-'.$month.'-1');
                    $int_date_temp->modify('last day of this month');
                    $int_date = $int_date_temp->format('Y-m-d');

                    if($issueDate != $int_date) {
                        // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                        // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
                        $tempindate = new DateTime($int_date);
                        $tempisdate = new DateTime($tempIssueDat);
                        $diff=$tempindate->diff($tempisdate);

                        $num_of_days = $diff->days;

                        $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $int_date,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
                    $cnt = 0;
                    while($issueDate <= $mat_date)
                    {
                        // $issueDate0 = new DateTime('@'.strtotime($issueDate));
                        // $issueDate0->modify('last day of next month');
                        // $issueDate = $issueDate0->format('Y-m-d');

                        $issueDate0 = new DateTime($issueDate);
                        $issueDate0->modify('last day of next month');
                        $issueDate = $issueDate0->format('Y-m-d');

                        //$issueDate = date('Y-m-d', $issueDate0);
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 month'));
                        //echo $issueDate.'<br/>';
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                }

                /*$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
                $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                $data_int = array(
                    'fd_transaction_id' => $transID,
                    'interest_date' => $int_date,
                    'interest_amount' => $int_amt
                );
                $status = $this->fd->add_fd_interest($data_int);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $issueDate = $int_date;*/

            } else {
                $issueDateFmt = DateTime::createFromFormat('Y-m-d', $issueDate);
                $issueDate = $issueDateFmt->format('Y-m-d');
                $ogiIssueDate = $issueDateFmt;
                //$ogiIssueDate->modify('+1 day');
                $ogiDay = $ogiIssueDate->format('j');
                $ogiMonth = $ogiIssueDate->format('n');

                if($int_mode == "Annually")
                {
                    $int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
                    while($issueDate <= $mat_date)
                    {
                      $issueDate0 = new DateTime($issueDate);
                      $issueDate0->modify(' +1 year');
                      $issueDate = $issueDate0->format('Y-m-d');

                        // $issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }


                } elseif($int_mode == "Half-yearly") {
                    $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
                    while($issueDate <= $mat_date)
                    {
                        $day = $issueDateFmt->format('j');
                        $month = $issueDateFmt->format('n');
                        if($ogiDay == 31) {
                            $issueDateFmt->modify('last day of +6 month');
                        } else {
                            $issueDateFmt->modify('+6 month');
                            if($resetDay && ($day != $ogiDay)) {
                                $day = $ogiDay;
                                $tempDate = $issueDateFmt->format('Y-m');
                                $issueDate = $tempDate.'-'.$ogiDay;
                                $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
                                $resetDay = false;
                            }
                            if($month == 8 && $day > 28 && $ogiDay > 28) {
                                $resetDay = true;
                                $issueDateFmt->modify('last day of previous month');
                            }
                        }
                        $issueDate = $issueDateFmt->format('Y-m-d');
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }

                } elseif($int_mode == "Quarterly") {
                    $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
                    while($issueDate <= $mat_date)
                    {
                        $day = $issueDateFmt->format('j');
                        $month = $issueDateFmt->format('n');
                        if($ogiDay == 31) {
                            $issueDateFmt->modify('last day of +3 month');
                        } else {
                            $issueDateFmt->modify('+3 month');
                            if($resetDay && ($day != $ogiDay)) {
                                $day = $ogiDay;
                                $tempDate = $issueDateFmt->format('Y-m');
                                $issueDate = $tempDate.'-'.$ogiDay;
                                $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
                                $resetDay = false;
                            }
                            if($month == 11 && $day > 28 && $ogiDay > 28) {
                                $resetDay = true;
                                $issueDateFmt->modify('last day of previous month');
                            }
                        }
                        $issueDate = $issueDateFmt->format('Y-m-d');
                        //$issueDate = date('Y-m-d', $issueDate0);
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' =>  $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }

                } elseif($int_mode == "Monthly") {
                    $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
                    while($issueDate <= $mat_date)
                    {
                        $day = $issueDateFmt->format('j');
                        $month = $issueDateFmt->format('n');
                        if($ogiDay == 31) {
                            $issueDateFmt->modify('last day of next month');
                        } else {
                            $issueDateFmt->modify('+1 month');
                            if($resetDay && ($day != $ogiDay)) {
                                $day = $ogiDay;
                                $tempDate = $issueDateFmt->format('Y-m');
                                $issueDate = $tempDate.'-'.$ogiDay;
                                $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
                                $resetDay = false;
                            }
                            if($month == 1 && $day > 28 && $ogiDay > 28) {
                                $resetDay = true;
                                $issueDateFmt->modify('last day of previous month');
                            }
                        }
                        //if($resetDay) { $issueDateFmt->modify('last day of previous month'); }
                        $issueDate = $issueDateFmt->format('Y-m-d');
                        //$issueDate = date('Y-m-d', $issueDate0);
                        //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 month'));
                        //echo $issueDate.'<br/>';
                        if($issueDate >= $mat_date)
                            break;
                        $data_int = array(
                            'fd_transaction_id' => $transID,
                            'interest_date' => $issueDate,
                            'interest_amount' => $int_amt
                        );
                        $status = $this->fd->add_fd_interest($data_int);
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                }
                /*$int_date_temp = new DateTime($year.'-'.$month.'-1');
                $int_date_temp->modify('last day of this month');
                $int_date = $int_date_temp->format('Y-m-d');
                $num_of_days = abs(strtotime($int_date) - $tempIssueDate); ///this give number of days with time
                $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
                $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
                $data_int = array(
                    'fd_transaction_id' => $transID,
                    'interest_date' => $int_date,
                    'interest_amount' => $int_amt
                );
                $status = $this->fd->add_fd_interest($data_int);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $issueDate = $int_date;*/
            }
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
        }
    }
    function calculate_interest()
    {
        $fd_method = $this->input->post('fd_method');
        $amt_inv = $this->input->post('amt_inv');
        $int_rate = $this->input->post('int_rate');
        $int_mode=$this->input->post('int_mode');
        //$set = $this->input->post('mat_date');
        $m_date= DateTime::createFromFormat('d/m/Y',$this->input->post('mat_date'));
        //$int_date_temp = DateTime::createFromFormat('d/m/Y', $int_date);
        $mat_date=$m_date->format('Y-m-d');
        $i_date = DateTime::createFromFormat('d/m/Y',$this->input->post('issue_date'));
        $issue_date=$i_date->format('Y-m-d');
        $mat_amt = 0.00;
        $int_amt = 0.00;
       $diff=date_diff(date_create($mat_date),date_create($issue_date));
       $total_days=$diff->days;
        $interest_arr = array();
        if($fd_method == 'Cumulative')
        {
          if($int_mode=='Annually')
          {
            $mat_amt = (floatval($amt_inv) * (pow((1 + (floatval($int_rate) / 100)), ($total_days / 365))));
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }
          else if($int_mode == 'Half-yearly')
          {

            $mat_amt=$amt_inv*pow(1+(($int_rate/2)/100),($total_days/365)*2);
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }
          else if($int_mode == 'Quarterly')
          {
            $mat_amt=$amt_inv*pow(1+(($int_rate/4)/100),($total_days/365)*4);
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }
          else if($int_mode == 'Monthly')
          {
            $mat_amt=$amt_inv*pow(1+(($int_rate/12)/100),($total_days/365)*12);
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }
          else if($int_mode == 'Weekly')
          {
            $mat_amt=$amt_inv*pow(1+(($int_rate/52)/100),($total_days/7));
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }
          else if($int_mode == 'Daily')
          {
            $mat_amt=$amt_inv*pow(1+(($int_rate/365)/100),$total_days);
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
          }

        }
        else if($fd_method == "Non-Cumulative")
        {
            $total_years = floatval($total_days / 365);
          //  $total_years = $duration->format('%y');
            $int_amt = round(((floatval($amt_inv)) * (floatval($int_rate)/100) * $total_years), 2);
            //$this->add_remaining_interest_amount();
            $mat_amt = round((floatval($amt_inv) * (pow((1 + (floatval($int_rate) / 100)), round(($total_days-(floatval($total_years) * 365)) / 365, 2)))), 2);
        }
        $interest_arr['mat_amt'] = $mat_amt;
        $interest_arr['int_amt'] = $int_amt;
        echo json_encode($interest_arr);
    }
    private function add_remaining_interest_amount($ref_number, $brokerID, $int_amt, $mat_amt)
    {
        $result = $this->fd->get_interest_details(array('fd.ref_number' => $ref_number, 'broker_id' => $brokerID));
        $db_int_amt = 0.00;
        $new_int_amt = 0.00;
        if(isset($result[0]->interest_amount))
        {
            $db_int_amt = $result[0]->interest_amount;
            $cal = $int_amt - $db_int_amt;
            if($cal < 0) {$cal = -$cal;}
            $new_int_amt = $cal + $mat_amt;
        }
        return $new_int_amt;
    }
    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['invTypes'] = $this->inv->get_fd_inv_broker_dropdown($brokerID);
        $data['companies'] = $this->comp->get_fd_companies_broker_dropdown($brokerID);
        $data['adv'] = $this->adv->get_adviser_broker_dropdown($brokerID);
        $data['payout'] = $this->fd->get_payout_mode_broker_dropdown();
        //$data['bank'] = $this->bank->get_banks("broker_id = ".$brokerID." or broker_id is null and status = 1");
        $condition = 'broker_id is null or broker_id = "'.$brokerID.'"';
        $data['banks'] = $this->bank->get_banks("(".$condition.") and status = 1"); // get list of all available banks
        $data['bank_account_types'] = $this->bank->get_bank_account_types($condition);
        return $data;
    }
    //Reports
    function fd_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Fixed Deposit Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/fixed_deposit/fd_report', $data);
        $this->load->view('broker/common/footer');
    }
    function interest_calendar_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Fixed Deposit Interest Calendar Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/fixed_deposit/fd_interest_calender_report', $data);
        $this->load->view('broker/common/footer');
    }
    function get_fd_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $fd_rep = $this->fd->get_fixed_deposit_report($type, $where);
        if(!empty($fd_rep))
        {
            unset($_SESSION['ins_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $fd_rep_array = array('fd_rep_data' => $fd_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('fd_report', $fd_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }
    function get_int_cal_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $int_date = $this->input->post('int_date');
        $int_date = '01'.'/'.$int_date;
        $int_date_temp = DateTime::createFromFormat('d/m/Y', $int_date);
        $rep_date_temp = $int_date_temp;
        $int_date = $int_date_temp->format('Y-m-d');
        $int_month_temp = $int_date_temp->format('M');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'month'=> $int_month_temp,
                'start_date'=> $int_date,
                'clientID'=> $client_id
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'month'=> $int_month_temp,
                'start_date'=> $int_date,
                'familyID'=> $family_id
            );
        }
        $logo = "";
        $status = false;
        $int_rep = $this->fd->get_interest_calendar_report($type, $where);
        if(!empty($int_rep))
        {
            unset($_SESSION['int_report']);
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

            $rep_date = $rep_date_temp->format('M-y');
            $rep_date_end0 = $rep_date_temp->modify('+ 11 month');
            $rep_date_end = $rep_date_end0->format('M-y');

            $repYear = $int_date_temp->format('Y');
            if($int_month_temp != 'Jan')

            $repYear = new DateTime($int_date);
            $repYear->modify('+ 1 year');
            $repYear = $repYear->format('Y');

            $month = new DateTime($int_date);
            $month->modify('-1 month');
            $month = $month->format('F');

              //  $repYear = date('Y', strtotime($int_date. '+ 1 year'));
            //$month = date('F', strtotime($int_date. '- 1 month'));
            $repDesc = $int_date_temp->format('F').' '.$int_date_temp->format('Y').' - '.$month.' '.$repYear;
            $rep_info = array('logo' => $logo, 'report_type' => $type, 'report_desc' =>  $repDesc);
            $int_rep_array = array('int_rep_data' => $int_rep, 'report_info'=>$rep_info,
                                'rep_date_start'=>$rep_date, 'rep_date_end'=>$rep_date_end);
            $this->session->set_userdata('int_report', $int_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
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
            $css_data = '<style type="text/css">
                table { width:100%; border:0px solid #fff; }
                table td {font-size: 10px; padding:2px; color:#4d4d4d; text-align:center; }
                .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                table th { font-size: 10px; padding:2px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse; }
                .amount { text-align:left; padding:10px; text-indent: 10px; }
                .amount-cal { text-align:left; text-indent: 3px; }
                .noWrap { white-space: nowrap; }
                .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:14px; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 10px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .normal {font-weight: normal;}
                .no-border {border-width: 0px; border-color: #fff;}
                .client-name { text-align: left; font-size: 12px; font-weight: bold; }
            </style>';
            $title_data = $this->input->post('titleData');
            $fd_data = $this->input->post('htmlData');
            $logo = $this->input->post('logo');
            $reportName = $this->input->post('report_name');

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
            $pdf->SetTitle('Fixed Deposit Report');
            $pdf->SetSubject('Fixed Deposit Report');
            $pdf->SetKeywords('fixed deposit, report');

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
            $pdf->writeHTML($css_data.$title_data.$fd_data, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();

            //Close and output PDF document
            $pdf->Output($reportName.'.pdf', 'D');
        }
    }
    function import($err_data=null)
    {
                   $header['title'] = 'FD Details Import';
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
                   $this->load->view('broker/fixed_deposit/import', $data);
                   $this->load->view('broker/common/notif');
                   $this->load->view('broker/common/footer');
         }
    function FD_import()
    {

                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');

                $uploadedStatus = 0;
                $message = ""; $impMessage = ""; $insertRow = true;
                $imp_data = array();
                if (isset($_POST['Import']))
                {
                    if (isset($_FILES["import_FDs"]))
                    {
                        //if there was an error uploading the file
                        if ($_FILES["import_FDs"]["name"] == '')
                        {
                            $message = "No file selected";
                        }
                        else
                        {
                            //get tmp_name of file
                            $file = $_FILES["import_FDs"]["tmp_name"];
                            //load the excel library
                            $this->load->library('Excel');
                            //read file from path
                            $objPHPExcel = PHPExcel_IOFactory::load($file);
                            //get only the Cell Collection
                            //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                            $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                            //temp variables to hold values
                            $famName="";$familyId="";$clientName="";$client_id="";$dateOfTransaction="";$typeOfInv="";$fd_inv_id="";$referenceNumber="";
                            $companyName="";$fd_comp_id="";$fd_method="";$dateOfIssue="";$amt_inv="";$interestRate="";$interestMode="";$maturityDate="";$mat_amt="";
                            $nominee="";$fdNomID="";$adviser="";$fd_adv_id="";$adjustment="";$adjustment_ref_no="";$invBankName="";$invAccountNumber="";$status="";
                            $invChequeNumber="";$invChequeDate="";$amt_inv="";$matBankName="";$fd_mat_bank_id="";$matAccountNumber="";$payoutOption="";$fd_bank_id="";
                            $fd_payout_id="";$fd_adv_id="";$brokerID;$transID="";$int_round_off=0;$PanNum="";$int_mode="";
                            $brokerID = $this->session->userdata('broker_id');
                            $user_id = $this->session->userdata('user_id');
                            //get data from excel using range
                            $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                            //stores column names
                            $dataColumns = array();
                            //stores row data
                            $dataRows = array();
                            $countRow = 0; $countErrRow = 0; $add_FD_list = 0; $countRem = 0; $countTrans = 0;

                            //check max row for client import limit
                              foreach($excelData as $rows)
                              {
                                  $countCell = 0;
                                  foreach($rows as $cell)
                                  {
                                      if($countRow == 0)
                                      {
                                          $cell = str_replace(array('.'), '', $cell);
                                          if(strtoupper($cell)=='FAMILY NAME' || strtoupper($cell)=='CLIENT NAME' ||strtoupper($cell)=='PAN NO' || strtoupper($cell)=='INVESTMENT TYPE' ||
                                             strtoupper($cell)=='COMPANY' || strtoupper($cell)=='REFERENCE NO' ||strtoupper($cell)=='INTEREST MODE' ||  strtoupper($cell)=='NBFC' ||
                                             strtoupper($cell)=='INTEREST FREQUENCY' || strtoupper($cell)=='COMPOUNDING FREQUENCY' || strtoupper($cell)=='DATE OF ISSUE' ||
                                             strtoupper($cell)=='AMOUNT INVESTED' || strtoupper($cell)=='INTEREST RATE' || strtoupper($cell)=='MATURITY DATE' ||
                                             strtoupper($cell)=='NOMINEE'  || strtoupper($cell)=='ADVISOR' ||strtoupper($cell)=='ADJUSTMENT'||strtoupper($cell)=='ADJUSTMENT NO' ||
                                             strtoupper($cell)=='INVESTMENT BANK NAME' || strtoupper($cell)=='INVESTMENT BANK ACCOUNT NO' || strtoupper($cell)=='CHEQUE NO' ||
                                             strtoupper($cell)=='CHEQUE DATE'|| strtoupper($cell)=='MATURITY BANK NAME' ||strtoupper($cell)=='MATURITY BANK ACCOUNT NO' ||
                                             strtoupper($cell)=='PAYOUT OPTION')
                                           {
                                              $dataColumns[$countCell] = $cell;
                                              $countCell++;
                                              $uploadedStatus = 2;
                                                continue;
                                          }
                                          else
                                          {
                                            //var_dump($dataColumns);
                                            //var_dump($cell);
                                              $message = 'Columns Specified in Excel is not in correct format';
                                              $uploadedStatus = 0;
                                              break;
                                          }
                                      }
                                      else
                                      {
                                            if($insertRow)
                                            {
                                                    if(strtoupper($dataColumns[$countCell]) === 'FAMILY NAME')
                                                    {
                                                              $famName = trim($cell);
                                                              //checks if family exists in Families table
                                                              $f_info = $this->family->get_families_broker($brokerID,$famName);
                                                              if(count($f_info) == 0)
                                                              {
                                                                  // $insertRow = false;
                                                                  // $impMessage = $famName." Family doesn't exist";
                                                                 //  $familyId = '';
                                                                  //family doesn't exist, so add a new family
                                                              }
                                                              else
                                                              {
                                                                  $familyId = $f_info[0]->family_id;
                                                              }
                                                      }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'CLIENT NAME')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                           $clientName = trim($cell);
                                                         }
                                                         else
                                                         {
                                                            $clientName ='';
                                                         }
                                                       }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                                    {
                                                           if($cell || $cell != '')
                                                           {
                                                                    $PanNum = trim($cell);
                                                                    $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                                     $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                                    // print_r($c_info);
                                                                     if(count($c_info) == 0)
                                                                     {
                                                                       $insertRow = false;
                                                                       //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                                       $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                                     }
                                                                     else
                                                                     {
                                                                         $client_id = $c_info->client_id;
                                                                         $familyId = $c_info->family_id;
                                                                     }
                                                               }
                                                             else
                                                             {
                                                                 $insertRow = false;
                                                                 $impMessage = "Pan Number cannot be empty";
                                                             }
                                                        }
                                                        else if(strtoupper($dataColumns[$countCell]) === 'REFERENCE NO')
                                                        {
                                                              if($cell || $cell != '')
                                                              {
                                                                $referenceNumber = trim($cell);
                                                                $cond = "`ref_number` = '".$referenceNumber."' AND (`broker_id` = '".$brokerID."' OR `broker_id` IS NULL)";
                                                                $isDuplicate = $this->fd->check_duplicate('fd_transactions',$cond);
                                                                if($isDuplicate){
                                                                  $insertRow = false;
                                                                  $impMessage = "Reference no ".$referenceNumber." already exists";
                                                                }

                                                              }
                                                              else
                                                              {
                                                               $insertRow = false;
                                                               $impMessage = "Reference no cannot be empty";
                                                              }
                                                        }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'INVESTMENT TYPE')
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                              $typeOfInv = trim($cell);
                                                              $whereTpye = "fd_inv_type = '$typeOfInv' and (broker_id = '$brokerID' or broker_id is null)";
                                                            $inv_id = $this->fd->get_fd_inv_id($whereTpye);
                                                           // print_r($inv_id);
                                                            if(count($inv_id) == 0)
                                                            {
                                                                $insertRow = false;
                                                                $impMessage = "Investment type ".$typeOfInv." doesn't exist";
                                                            }
                                                            else
                                                            {
                                                                $fd_inv_id = $inv_id[0]->fd_inv_id;
                                                            }
                                                         }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Investment type cannot be empty";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'COMPANY')
                                                    {
                                                          if($cell || $cell != '')
                                                          {

                                                              $companyName = trim($cell);
                                                              $whereComp = "fd_comp_name = '$companyName' and (broker_id = '$brokerID' or broker_id is null)";
                                                             //checks if client exists in Clients table
                                                             $comp_id = $this->fd->get_fd_comp_id($whereComp);
                                                             //print_r($comp_id);
                                                             if(count($comp_id) == 0)
                                                             {

                                                                $insertRow = false;
                                                                $impMessage = "Could not find Company Name ".$companyName;
                                                             }
                                                             else
                                                             {
                                                                $fd_comp_id = $comp_id[0]->fd_comp_id;
                                                             }
                                                          }
                                                          else
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Company Name cannot be empty";
                                                            }
                                                    }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'INTEREST MODE')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                              if(strtoupper($cell)=='NON-CUMULATIVE' || strtoupper($cell)=='CUMULATIVE')
                                                              {
                                                                $fd_method = trim($cell);
                                                              }
                                                              else
                                                               {
                                                                 $insertRow = false;
                                                                 $impMessage = "Interest Mode is incorrect ";
                                                               }
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Interest Mode cannot be empty";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'NBFC')
                                                    {
                                                           if($cell || $cell != '')
                                                           {
                                                               $nbfc = trim(strtoupper($cell));
                                                               if($nbfc==="YES")
                                                               {
                                                                 $int_round_off=1;
                                                               }
                                                               else
                                                               {
                                                                 $int_round_off=0;
                                                               }
                                                           }
                                                           else
                                                           {
                                                             //$insertRow = false;
                                                             //$impMessage = "Interest Rate cannot be empty";
                                                             $int_round_off=0;
                                                            }

                                                    }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'INTEREST FREQUENCY')
                                                    {
                                                      if($fd_method=='Non-Cumulative')
                                                      {
                                                           if($cell || $cell != '')
                                                           {
                                                                 if(strtoupper($cell)=='ANNUALLY' || strtoupper($cell)=='HALF-YEARLY' || strtoupper($cell)=='QUARTERLY' || strtoupper($cell)=='MONTHLY')
                                                                 {
                                                                   $int_mode = trim($cell);
                                                                 }
                                                                 else {
                                                                   $insertRow = false;
                                                                   $impMessage = "Interest frequency should be Annually/Half-Yearly/Quarterly/Monthly";
                                                                 }
                                                           }
                                                           else
                                                           {
                                                             $insertRow = false;
                                                             $impMessage = "Interest frequency is required in Non-Cumulative method";
                                                            }
                                                        } else {
                                                          if($cell || $cell != '')
                                                          {
                                                            $insertRow = false;
                                                            $impMessage = "Interest frequency should be blank for Cumulative method";
                                                          }
                                                        }
                                                      }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'COMPOUNDING FREQUENCY')
                                                    {
                                                        if($fd_method=='Cumulative')
                                                        {
                                                               if($cell || $cell != '')
                                                               {
                                                                 if(strtoupper($cell)=='ANNUALLY' || strtoupper($cell)=='HALF-YEARLY' || strtoupper($cell)=='QUARTERLY' || strtoupper($cell)=='MONTHLY' || strtoupper($cell)=='WEEKLY' || strtoupper($cell)=='DAILY')
                                                                 {
                                                                   $int_mode = trim($cell);
                                                                 }
                                                                 else {
                                                                   $insertRow = false;
                                                                   $impMessage = "Coumpounding frequency should be Annually/Half-Yearly/Quarterly/Monthly/Weekly/Daily";
                                                                 }

                                                               }
                                                               else
                                                               {
                                                                  $int_mode = 'Quarterly';
                                                                }
                                                          } else {
                                                            if($cell || $cell != '')
                                                            {
                                                              $insertRow = false;
                                                              $impMessage = "Coumpounding frequency should be blank for Non-Cumulative method";
                                                            }
                                                          }
                                                      }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'DATE OF ISSUE')
                                                    {

                                                            if($cell || $cell != '')
                                                            {
                                                                   $date = DateTime::createFromFormat('m-d-y', $cell);
                                                           if(is_object($date)) {
                                                                  $dateOfIssue = $date->format('Y-m-d');
                                                               } else {
                                                            //check if date is in string format d/m/Y
                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $dateOfIssue = $date->format('Y-m-d');
                                                                } else {
                                                                       $insertRow = false;
                                                                     $impMessage = "Date of Issue format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                                }
                                                              }
                                                              else
                                                              {
                                                                $dateOfIssue=null;
                                                                $dateOfIssue=0;
                                                                $insertRow = false;
                                                                $impMessage = "Date of Issue cannot be empty";
                                                              }

                                                          // if($cell || $cell != '')
                                                          // {
                                                          //   // $date = DateTime::createFromFormat($cell);
                                                          //   // $dateOfIssue = $date->format('Y-m-d');
                                                          //   $dateOfIssue = DateTime::createFromFormat('d/m/Y',$cell);
                                                          //    // $date = new DateTime($cell);
                                                          //      $dateOfIssue = $dateOfIssue->format('Y-m-d');
                                                          //      $dateOfTransaction=$dateOfIssue;
                                                          // }
                                                          // else
                                                          // {
                                                          //   $dateOfIssue=null;
                                                          //   $dateOfIssue=0;
                                                          //   $impMessage = "Date of issue cannot be empty";
                                                          // }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT INVESTED')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $amt_inv = trim($cell);
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Amount Invested Deposit Name cannot be empty";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'INTEREST RATE')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $interestRate = trim($cell);
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Interest Rate cannot be empty";
                                                         }
                                                       }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'MATURITY DATE')
                                                    {

                                                          if($cell || $cell != '')
                                                          {
                                                          $date = DateTime::createFromFormat('m-d-y', $cell);
                                                          if(is_object($date))
                                                          {
                                                              $maturityDate = $date->format('Y-m-d');
                                                              if($dateOfIssue>$maturityDate)
                                                              {
                                                                       $insertRow = false;
                                                                        $impMessage = "Issue Date Should Not Be Greater Than Maturity Date";
                                                              }
                                                            } else {
                                                        //check if date is in string format d/m/Y
                                                              $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                  if(is_object($date))
                                                                  {
                                                                       $maturityDate = $date->format('Y-m-d');
                                                                       if($dateOfIssue>$maturityDate){
                                                                          $insertRow = false;
                                                                          $impMessage = "Issue Date Should Not Be Greater Than Maturity Date";
                                                                       }
                                                                  } else {
                                                                         $insertRow = false;
                                                                       $impMessage = "Maturity Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                  }
                                                            }
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Maturity Date cannot be empty";
                                                          }
                                                                // if($cell || $cell != '')
                                                                // {
                                                                //   $maturityDate = DateTime::createFromFormat('d/m/Y',$cell);
                                                                //   $maturityDate = $maturityDate->format('Y-m-d');
                                                                //   if($dateOfIssue>$maturityDate){
                                                                //        $insertRow = false;
                                                                //        $impMessage = "Issue Date Should Not Be Greater Than Maturity Date";
                                                                //      }
                                                                // }
                                                                // else
                                                                // {
                                                                //   $insertRow = false;
                                                                //   $impMessage = "Maturity Date  cannot be empty";
                                                                //   }
                                                         }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'NOMINEE')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {
                                                            $nominee = trim($cell);
                                                            $whereComp = array('f.family_id'=>$familyId,'c.name'=>$nominee);
                                                           //checks if client exists in Clients table
                                                            $nomineeID= $this->fd->getNominee($whereComp);
                                                           //print_r($nomineeID);
                                                           if(count($nomineeID) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "You add nominee from your family";
                                                              $fdNomID='';
                                                           }
                                                           else
                                                           {
                                                            $fdNomID=$nomineeID[0]->client_id;
                                                           }
                                                        // }
                                                        // else
                                                        // {
                                                        //   $insertRow = false;
                                                        //   $impMessage = "Nominee cannot be empty";
                                                        // }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ADVISOR')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $adviser = trim($cell);
                                                            $whereAdviser = "adviser_name = '$adviser' and (broker_id = '$brokerID' or broker_id is null)";
                                                           //checks if client exists in Clients table
                                                           $adv_id = $this->fd->get_adviser_id($whereAdviser);
                                                           //print_r($adv_id);
                                                           if(count($adv_id) == 0)
                                                           {

                                                              $insertRow = false;
                                                              $impMessage = "Could not find  Advisor Name ".$adviser;
                                                           }
                                                           else
                                                           {
                                                              $fd_adv_id = $adv_id[0]->adviser_id;
                                                           }
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage = "Advisor cannot be empty";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $adjustment = trim($cell);
                                                        }
                                                        else
                                                        {
                                                          $adjustment="";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT NO')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $adjustment_ref_no = trim($cell);
                                                        }
                                                        else
                                                        {
                                                          $adjustment_ref_no="";
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'INVESTMENT BANK NAME')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {

                                                            $invBankName = trim($cell);
                                                            $wherebank = "bank_name = '$invBankName' and (broker_id = '$brokerID' or broker_id is null)";
                                                           //checks if client exists in Clients table
                                                           $bank_id = $this->fd->get_bank_id($wherebank);
                                                         //  print_r($bank_id);
                                                           if(count($bank_id) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "Could not find Bank Name ";
                                                              $invBankName = '';
                                                           }
                                                           else
                                                           {
                                                              $fd_bank_id = $bank_id[0]->bank_id;
                                                           }
                                                        // }
                                                        // else
                                                        // {
                                                        //   $insertRow = false;
                                                        //   $impMessage = "Investment Bank Name cannot be empty";
                                                        // }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'INVESTMENT BANK ACCOUNT NO')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {
                                                            $invAccountNumber = trim($cell);
                                                            $whereAccount = array(
                                                              'account_number'=>$invAccountNumber,
                                                              'bank_id'=>$fd_bank_id,
                                                              'client_id'=>$client_id
                                                           );
                                                           //checks if client exists in Clients table
                                                           $bank_account = $this->fd->confirm_ac_no($whereAccount);
                                                         //  print_r($bank_account);
                                                           if(count($bank_account) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "Investment bank  account number not correct";
                                                              $invAccountNumber= '';
                                                           }
                                                           else
                                                           {
                                                             //$invAccountNumber="0";
                                                              $invAccountNumber= $bank_account[0]->account_number;
                                                           }
                                                        // }
                                                        // else
                                                        // {
                                                        //
                                                        //    $insertRow = false;
                                                        //    $impMessage = "Investment Account Number cannot be empty";
                                                        // }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE NO')
                                                    {
                                                        if($cell || $cell != '')
                                                        {
                                                            $invChequeNumber = trim($cell);
                                                        }
                                                        else
                                                        {
                                                          // $insertRow = false;
                                                          // $impMessage = "Investment Cheque  Number cannot be empty";
                                                          $invChequeNumber = '';
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE DATE')
                                                    {
                                                         //$date = new DateTime($cell);
                                                         if($cell || $cell != '')
                                                         {
                                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                                            if(is_object($date))
                                                            {
                                                                $invChequeDate = $date->format('Y-m-d');

                                                              }
                                                             else
                                                              {
                                                                $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                    if(is_object($date))
                                                                    {
                                                                         $invChequeDate = $date->format('Y-m-d');
                                                                    }
                                                                     else
                                                                     {
                                                                           $insertRow = false;
                                                                         $impMessage = "Cheque  Date format is not proper (should be dd/mm/yyyy)";
                                                                     }
                                                               }
                                                           }
                                                          else
                                                          {
                                                            $invChequeDate=$dateOfIssue;
                                                          }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'MATURITY BANK NAME')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {
                                                            $matBankName = trim($cell);
                                                            $wherebank = "bank_name = '$matBankName' and (broker_id = '$brokerID' or broker_id is null)";
                                                           //checks if client exists in Clients table
                                                           $bank_id = $this->fd->get_bank_id($wherebank);
                                                           //print_r($bank_id);
                                                           if(count($bank_id) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "Could not find Bank Name ";
                                                              $matBankName = '';
                                                           }
                                                           else
                                                           {
                                                              $fd_mat_bank_id = $bank_id[0]->bank_id;
                                                           }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'MATURITY BANK ACCOUNT NO')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {
                                                            $matAccountNumber = trim($cell);
                                                            $whereAccount = array(
                                                              'account_number'=>$matAccountNumber,
                                                              'bank_id'=>$fd_bank_id,
                                                              'client_id'=>$client_id
                                                           );
                                                           //checks if client exists in Clients table
                                                           $bank_account = $this->fd->confirm_ac_no($whereAccount);
                                                         //  print_r($bank_account);
                                                           if(count($bank_account) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "IMaturity Account Number not correct";
                                                              $matAccountNumber= '';
                                                           }
                                                           else
                                                           {
                                                             //$invAccountNumber="0";
                                                              $matAccountNumber= $bank_account[0]->account_number;
                                                           }
                                                        // }
                                                        // else
                                                        // {
                                                        //   $insertRow = false;
                                                        //   $impMessage = "Maturity Account Number cannot be empty";
                                                        // }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'PAYOUT OPTION')
                                                    {
                                                        // if($cell || $cell != '')
                                                        // {
                                                            $payoutOption = trim($cell);
                                                            $wherepayout = array('payout_mode'=>$payoutOption);
                                                           //checks if client exists in Clients table
                                                           $payout_id = $this->fd->get_payout_id($wherepayout);
                                                         //  print_r($payout_id);
                                                           if(count($payout_id) == 0)
                                                           {

                                                              // $insertRow = false;
                                                              // $impMessage = "Could not find FD Payout ID ";
                                                              $fd_payout_id=3;
                                                           }
                                                           else
                                                           {
                                                              $fd_payout_id = $payout_id[0]->payout_mode_id;
                                                           }
                                                    }

                                                    $status='Active';
                                            $countCell++;
                                            }
                                            else
                                            {
                                              //echo "Called";
                                               if(strtoupper($dataColumns[$countCell]) === 'REFERENCE NO')
                                               {
                                                       $referenceNumber = trim($cell);
                                                      // echo $referenceNumber;
                                                      // echo "refereence no";
                                                }
                                               if(strtoupper($dataColumns[$countCell]) === 'PAN NO')
                                                {
                                                      $PanNum = trim($cell);
                                                                //echo $PanNum;
                                                  //              echo "Pan";
                                                  }
                                                  $countCell++;
                                            } //else end
                                      }
                                    }
                                  if($countRow != 0)
                                  {
                                      if(!$insertRow)
                                      {
                                          $imp_data[$countErrRow][0] = $clientName;
                                          $imp_data[$countErrRow][1] = $PanNum;
                                          $imp_data[$countErrRow][2] = $referenceNumber;
                                          $imp_data[$countErrRow][3] = $impMessage;

                                          $countErrRow++;
                                          $insertRow = true;
                                          $uploadedStatus = 2;
                                          continue;
                                      }
                                     //  $temp_mat_date =new Datetime($maturityDate);
                                     //  $temp_issue_date =new DateTime($dateOfIssue);
                                     $mat_date =new DateTime($maturityDate);
                                     $issue_date =new DateTime($dateOfIssue);
                                     $temp_mat_date = $mat_date;
                                     $temp_issue_date = $issue_date;
                                     $mat_amt = 0.00;
                                     $int_amt = 0.00;
                                     $interval = $mat_date->diff($issue_date);
                                      //$num_of_days = (strtotime($temp_mat_date) - strtotime($temp_issue_date)); ///this give number of days with time
                                      $total_days = $interval->format('%a');//floor($num_of_days/86400); ///86400 seconds in a day

                                      //if there is no date of transaction it will take dateofransaction same as dateofissue
                                      if($dateOfTransaction=='')
                                        {$dateOfTransaction = $dateOfIssue;}

                                      if($fd_method == 'Cumulative')
                                      {
                                        if(strtoupper($int_mode)=='ANNUALLY')
                                        {
                                          $mat_amt = round((floatval($amt_inv) * (pow((1 + (floatval($interestRate) / 100)), round($total_days / 365, 2)))), 2);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }
                                        else if(strtoupper($int_mode) == 'HALF-YEARLY')
                                        {

                                          $mat_amt=$amt_inv*pow(1+(($interestRate/2)/100),($total_days/365)*2);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }
                                        else if(strtoupper($int_mode) == 'QUARTERLY')
                                        {
                                          $mat_amt=$amt_inv*pow(1+(($interestRate/4)/100),($total_days/365)*4);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }
                                        else if(strtoupper($int_mode) == 'MONTHLY')
                                        {
                                          $mat_amt=$amt_inv*pow(1+(($interestRate/12)/100),($total_days/365)*12);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }
                                        else if(strtoupper($int_mode) == 'WEEKLY')
                                        {
                                          $mat_amt=$amt_inv*pow(1+(($interestRate/52.143)/100),($total_days/365)*52.143);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }
                                        else if(strtoupper($int_mode) == 'DAILY')
                                        {
                                          $mat_amt=$amt_inv*pow(1+(($interestRate/365)/100),$total_days);
                                          $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
                                        }

                                      }
                                      else if($fd_method == "Non-Cumulative")
                                      {
                                          $duration = floatval($total_days / 365);
                                          $total_years = $interval->format('%y');
                                          $int_amt = round(((floatval($amt_inv)) * (floatval($interestRate)/100) * $duration), 2);
                                          //$this->add_remaining_interest_amount();
                                          $mat_amt = round((floatval($amt_inv) * (pow((1 + (floatval($interestRate) / 100)), round(($total_days-(floatval($total_years) * 365)) / 365, 2)))), 2);
                                      }
                                      // echo "total interest amount ".$int_amt;
                                      // echo "total maturity amount ".$mat_amt;
                                      $dataRows[$add_FD_list] = array('family_id'=>$familyId,'client_id'=>$client_id,'transaction_date'=>$dateOfTransaction,'fd_inv_id'=>$fd_inv_id,'fd_comp_id'=>$fd_comp_id,'fd_method'=>$fd_method,'ref_number'=>$referenceNumber,'issued_date'=>$dateOfIssue,'amount_invested'=>$amt_inv,'interest_rate'=>$interestRate,'int_round_off'=>$int_round_off,'interest_mode'=>$int_mode,'maturity_date'=>$maturityDate,
                                                                           'maturity_amount'=>$mat_amt, 'nominee'=>$fdNomID,'status'=>$status,'adjustment'=>$adjustment,'adjustment_ref_number'=>$adjustment_ref_no,'inv_bank_id'=>$fd_bank_id,'inv_cheque_number'=>$invChequeNumber,'inv_account_number'=>$invAccountNumber,'inv_cheque_date'=>$invChequeNumber,'inv_cheque_date'=>$invChequeDate,'inv_amount'=>$amt_inv,'maturity_bank_id'=>$fd_mat_bank_id,'maturity_account_number'=>$matAccountNumber,'maturity_payout_id'=>$fd_payout_id,'adv_id'=>$fd_adv_id,'broker_id'=>$brokerID,'user_id'=>$brokerID);
                                      //print_r($dataRows[$add_FD_list]);
                                      $inserted = $this->fd->add_FD($dataRows[$add_FD_list]);
                                       //print_r($inserted);
                                       //$transID = $this->fd->get_last_trans_id();
                                       $trans_id=$inserted;
                                      $uploadedStatus = 1;

                                      if($fd_method == "Non-Cumulative")
                                      {

                                        $AddInterest = array('issued_date'=>$dateOfIssue,'maturity_date'=>$maturityDate,'interest_mode'=>$int_mode,'amount_invested'=>$amt_inv,'interest_rate'=>$interestRate,'int_round_off'=>$int_round_off,'maturity_amount'=>$amt_inv,'broker_id'=>$brokerID,'intAmt'=>$int_amt);
                                      //  print_r($data);
                                         $status=$this->add_interest_details($AddInterest, $trans_id);
                                      //   print_r($status);
                                        // $AddInterest['intAmt'] = $int_amt;
                                         $mat_amt = $this->additional_interest($AddInterest, $trans_id);
                                         //print_r($mat_amt);
                                         $message["newMatAmt"] = $mat_amt;
                                      }
                                    else
                                     {
                                          $tot_mat = $int_amt+$amt_inv;
                                          $data_int = array('maturity_amount'=> $tot_mat);
                                          $whereCon = array('fd_transaction_id' => $trans_id,'broker_id' => $brokerID);
                                          try
                                          {
                                              $status = $this->fd->update_fixed_deposit($data_int, $whereCon, FALSE);
                                              //if there is any error
                                              if(isset($status['code']))
                                              {
                                                  throw new Custom_exception();
                                              }
                                              $message["newMatAmt"] = $tot_mat;
                                              //echo $tot_mat.' - '.$intAmt;
                                          }
                                          catch(Custom_exception $e)
                                          {
                                              //display custom message
                                              $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
                                          }
                                      }
                                     //print_r($inserted);
                                      if(is_array($inserted))
                                      {
                                          $uploadedStatus = 0;
                                          $message = 'Error while inserting records. '.$trans_id['message'];
                                          break;
                                      }

                                      $famName="";$familyId="";$clientName="";$client_id="";$dateOfTransaction="";$typeOfInv="";$fd_inv_id="";$referenceNumber="";
                                      $companyName="";$fd_comp_id="";$fd_method="";$dateOfIssue="";$amt_inv="";$interestRate="";$maturityDate="";$mat_amt="";
                                      $nominee="";$adviser="";$fd_adv_id="";$adjustment="";$adjustment_flag="";$adjustment_ref_no="";$invBankName="";$invAccountNumber="";
                                      $invChequeNumber="";$invChequeDate="";$amt_inv="";$matBankName="";$fd_mat_bank_id="";$matAccountNumber="";$payoutOption="";
                                      $fd_payout_id="";$fd_adv_id="";$PanNum="";$int_mode="";$brokerID;
                                  }
                                  if($uploadedStatus == 0)
                                      break;

                              //}
                              $countRow++;
                            }
                              if($dataRows)
                              {
                                  if(is_array($transID))
                                  {
                                      $uploadedStatus = 0;
                                      $message = 'Error while inserting records';
                                  } else {
                                      $this->common->last_import('Fixed Deposit Details', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                      if($uploadedStatus != 2) {
                                          $uploadedStatus = 1;
                                          $message = "FD Details Uploaded Successfully";
                                      }
                                  }
                              }
                            unset($dataColumns, $dataRows);
                        }
                    } // File selection if End
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


            //form Submit if end
                // else
                // {
                //      //echo "Not Form Submit";
                // }
               $this->import($imp_data);
        }}
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

            //remove all rupee symbols from data, as it does not display properly in Excel
            $htmlData = str_replace("₹","",$htmlData);

            //load the excel library
            $this->load->library('Excel');

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
            header('Content-Disposition:attachment;filename=Fixed Deposit Portfolio.xlsx'); // specify the download file name
            header('Cache-Control:max-age=0');

            // Pass to writer and output as needed
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();

            $objWriter->save('php://output');

            // Delete temporary file
            unlink($tmpfile);
            exit;
        }
    }
}
