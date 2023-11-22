<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Fixed_deposits extends CI_Controller{
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
        $tempMatDate = strtotime($mat_date);
        $amt_inv = $data['amount_invested'];
        $mat_amt = $data['maturity_amount'];
        $int_rate = $data['interest_rate'];
        $tot_int = $data['intAmt'];
        $brokerID = $data['broker_id'];
        $year = date('Y', $tempMatDate);
        $month = date('n', $tempMatDate);
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

            $num_of_days = $tempMatDate - strtotime($int_date);
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
        $tempIssueDate = strtotime($issueDate);
        $month = date('n', $tempIssueDate);
        $year = date('Y', $tempIssueDate);
        try
        {
            if($int_round_off == 1)
            {
                if(date('L', $tempIssueDate))
                    $total_days = 366;

                if($int_mode == "Annually")
                {
                    if($month <= 3)
                        $int_date = $year.'-03-31';
                    else
                        $int_date = ($year+1).'-03-31';

                    if($issueDate != $int_date) {
                        $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
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
                    }
                    $issueDate = $int_date;

                    $int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
                    while($issueDate <= $mat_date)
                    {
                        $issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
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
                        $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
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
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
                    while($issueDate <= $mat_date)
                    {
                        $issueDate0 = new DateTime('@'.strtotime($issueDate));
                        $issueDate0->modify('last day of +6 month');
                        $issueDate = $issueDate0->format('Y-m-d');
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
                        $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
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
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
                    while($issueDate <= $mat_date)
                    {
                        $issueDate0 = new DateTime('@'.strtotime($issueDate));
                        $issueDate0->modify('last day of +3 month');
                        $issueDate = $issueDate0->format('Y-m-d');
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
                        $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
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
                    }
                    $issueDate = $int_date;

                    $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
                    $cnt = 0;
                    while($issueDate <= $mat_date)
                    {
                        $issueDate0 = new DateTime('@'.strtotime($issueDate));
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
                        $issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
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
        $mat_date = DateTime::createFromFormat('d/m/Y', $this->input->post('mat_date'));
        $issue_date = DateTime::createFromFormat('d/m/Y', $this->input->post('issue_date'));

        $temp_mat_date = $mat_date->format('Y-m-d');
        $temp_issue_date = $issue_date->format('Y-m-d');

        $mat_date = new DateTime($mat_date->format('Y-m-d'));
        $issue_date = new DateTime($issue_date->format('Y-m-d'));
        $mat_amt = 0.00;
        $int_amt = 0.00;

        $interval = $mat_date->diff($issue_date);
        $num_of_days = (strtotime($temp_mat_date) - strtotime($temp_issue_date)); ///this give number of days with time
        $total_days = floor($num_of_days/86400); ///86400 seconds in a day
        //$total_days = $interval->format('%a');
        $interest_arr = array();
        if($fd_method == 'Cumulative')
        {
            $mat_amt = round((floatval($amt_inv) * (pow((1 + (floatval($int_rate) / 100)), round($total_days / 365, 2)))), 2);
            $int_amt = round(floatval($mat_amt) - (floatval($amt_inv)));
        }
        else if($fd_method == "Non-Cumulative")
        {
            $duration = floatval($total_days / 365);
            $total_years = $interval->format('%y');
            $int_amt = round(((floatval($amt_inv)) * (floatval($int_rate)/100) * $duration), 2);
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
                $repYear = date('Y', strtotime($int_date. '+ 1 year'));
            $repDesc = $int_date_temp->format('F').' '.$int_date_temp->format('Y').' - '.date('F', strtotime($int_date. '- 1 month')).' '.$repYear;
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
