<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Real_estate extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load libraries, helpers
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        $this->load->helper('url');

        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }

        //load model
        $this->load->model('Real_estate_model', 're');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Advisers_model', 'adv');
    }

    //Real Estate List
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Real Estate Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/real_estate/re_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all real estate details from database
    function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->re->get_real_estate(array('proTrans.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $re)
        {
            $row = array();
            $num++;
            $row['family_name'] = $re->family_name;
            $row['client_name'] = $re->client_name;
            $row['transaction_date'] = $re->transaction_date;
            $row['transaction_type'] = $re->transaction_type;
            $row['property_name'] = $re->property_name;
            $row['property_type_name'] = $re->property_type_name;
            $row['property_location'] = $re->property_location;
            $row['property_area'] = $re->property_area;
            $row['transaction_rate'] = round($re->transaction_rate);
            $row['amount'] = round($re->amount);
            $row['market_value'] = round($re->market_value);
            $row['rent_applicable'] = $re->rent_applicable;
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_re('."'".$re->pro_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_re('."'".$re->pro_transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_re('."'".$re->pro_transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_re('."'".$re->pro_transaction_id."'".')">
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

    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Add or Sell Real Estate';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/js/common.js'
        );
        //get details of real estate for the form
        $data = $this->fill_form();
        unset($_SESSION['rent']);
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/real_estate/re_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/add_adviser');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit_form()
    {
        if(isset($_GET['id'])) {
            $trans_id = $_GET['id'];
            $sold = 'false';
            $brokerID = $this->session->userdata('broker_id');
            $data['action'] = 'edit';

            $condition = array(
                'pro_transaction_id' => $trans_id,
                'fam.broker_id' => $brokerID
            );
            //get details of insurance for the form
            $data = $this->fill_form();
            // get insurance info from database by policy number
            $re_data = $this->re->get_real_estate_details($condition);
            $rent_data = $this->re->get_rent_details(array('pro_transaction_id' => $re_data->pro_transaction_id));
            $check_re = $this->re->get_real_estate_details(array('property_name'=> $re_data->property_name, 'transaction_type'=> 'Sale', 'proTrans.client_id' => $re_data->client_id));
            if($check_re)
                $sold = 'true';
            $rentArr = array();
            $countArr = 0;
            foreach($rent_data as $rent)
            {
                $rentArr[$countArr]['srNo'] = $countArr + 1;
                $rentArr[$countArr]['proRentID'] = $rent->pro_rent_id;
                $rent_temp = DateTime::createFromFormat('Y-m-d', $rent->from_date);
                $rentArr[$countArr]['fromDate'] = $rent_temp->format('d/m/Y');
                $rent_temp = DateTime::createFromFormat('Y-m-d', $rent->to_date);
                $rentArr[$countArr]['toDate'] = $rent_temp->format('d/m/Y');
                $rentArr[$countArr]['amount'] = $rent->amount;
                $countArr++;
            }

            $this->session->set_userdata('rent', $rentArr);

            $data['real_estate'] = $re_data;
            $data['isSold'] = $sold;

            $header['title']='Edit Real Estate - '.$trans_id;
            $header['css'] = array(
                'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
                'assets/users/plugins/form-select2/select2.css',
                'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
                'assets/users/plugins/pines-notify/jquery.pnotify.default.css'
            );
            $header['js'] = array(
                'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
                'assets/users/plugins/form-select2/select2.min.js',
                'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
                'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
                'assets/users/plugins/form-parsley/parsley.min.js',
                'assets/users/plugins/bootbox/bootbox.min.js',
                'assets/users/js/common.js'
            );
            $this->load->view('broker/common/header', $header);
            $this->load->view('broker/real_estate/re_edit_form', $data);
            $this->load->view('broker/master/add_family');
            $this->load->view('broker/master/add_adviser');
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            /* has come to Edit page without ID, so redirect to some other page */
        }
    }

    function add_real_estate()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        unset($data['family_id'], $data['pro_transaction_id']);
        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['property_updated_on']);
        $data['property_updated_on'] = $date->format('Y-m-d');
        $proID = $this->re->get_property_id(array('brokerID' => $brokerID));
        $data['pro_transaction_id'] = $proID->property_id;


        //Check dupicate
        $condition = "`property_name` = '".trim($data['property_name'])."' AND (`broker_id` = '".$brokerID."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->re->check_duplicate('property_transactions',$condition);

        if($isDuplicate) {
             $error = array(
                "type" => "error",
                "title" => "Real Easte name already exists!.",
                "text" => "The Real Easte name you are trying to add already exists. Please change its name."
            );
            echo json_encode($error);
        }
        else
        {


        $rent_data = null;
        if(isset($this->session->userdata['rent']))
        {
            $rent_data = $this->session->userdata['rent'];
        }
        $rent_arr = array();
        if(count($rent_data) >= 1)
        {
            $data['rent_applicable'] = "1";
            $countRent = 0;
            foreach($rent_data as $item)
            {
                $date = DateTime::createFromFormat('d/m/Y', $item['fromDate']);
                $item['fromDate'] = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $item['toDate']);
                $item['toDate'] = $date->format('Y-m-d');
                $rent_arr[$countRent] = array(
                    'pro_transaction_id' => $proID->property_id,
                    'from_date' => $item['fromDate'],
                    'to_date' => $item['toDate'],
                    'amount' => $item['amount']
                );
                $countRent++;
            }
        }
        else
            $data['rent_applicable'] = "0";
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');
        try
        {
            $status = $this->re->add_real_estate($data);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            if($rent_arr)
            {
                $status = $this->re->add_rent_details($rent_arr, true);
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
            }

            $message = array(
                'status' => 'success',
                'title'=> 'New Real Estate Added!',
                'transaction_id' => $proID->property_id,
                'text' => 'Real Estate Details added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }
    }

    function sell_real_estate()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $property_id = $data['sell_property_name'];
        $data['property_name'] = $data['h_property_name'];
        $data['property_type_id'] = $data['h_pro_type'];
        $data['property_location'] = $data['sell_pro_location'];
        $data['property_area'] = $data['sell_pro_area'];
        $data['property_unit_id'] = $data['h_unit_id'];
        $data['transaction_rate'] = $data['sell_trans_rate'];
        $data['amount'] = $data['sell_amount'];
        $data['current_rate'] = $data['sell_curr_rate'];
        $data['property_updated_on'] = date('Y-m-d');
        $data['remarks'] = $data['sell_remarks'];
        $data['adviser_id'] = $data['sell_adviser_id'];
        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');

        unset($data['family_id'], $data['pro_transaction_id'], $data['sell_property_name'], $data['h_property_name'], $data['h_pro_type'],
        $data['sell_pro_location'], $data['sell_pro_area'], $data['h_unit_id'], $data['sell_trans_rate'], $data['sell_amount'],
        $data['sell_curr_rate'], $data['sell_pro_upd_on'], $data['sell_remarks'], $data['sell_adviser_id'], $data['sell_pro_type'],
        $data['sell_unit_id']);

        $proID = $this->re->get_property_id(array('brokerID' => $brokerID));
        $data['pro_transaction_id'] = $proID->property_id;
        try
        {
            $status = $this->re->add_real_estate($data);;
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $whereRent = array('pro_transaction_id' => $property_id, 'rent_date >=' => $data['transaction_date']);
            $status = $this->re->delete_rent_details($whereRent);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

            $message = array(
                'status' => 'success',
                'title'=> 'Real Estate is Sold!',
                'transaction_id' => $proID->property_id,
                'text' => 'Real Estate Selling Details added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //edit real estate purchase details to database
    function edit_real_estate()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $property_id = $data['hPropertyID'];
        unset($data['family_id'], $data['pro_transaction_id'], $data['hFamilyID'], $data['hClientID'], $data['hPropertyID'],
        $data['hPropertyType'], $data['hUnit'], $data['hAdviser']);
        if(isset($data['transaction_date'])) {
            $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
            $data['transaction_date'] = $date->format('Y-m-d');
        }
        $date = DateTime::createFromFormat('d/m/Y', $data['property_updated_on']);
        $data['property_updated_on'] = $date->format('Y-m-d');

        $rent_data = null;
        if(isset($this->session->userdata['rent']))
        {
            $rent_data = $this->session->userdata['rent'];
        }
        if(count($rent_data) >= 1)
            $data['rent_applicable'] = "1";
        else
            $data['rent_applicable'] = "0";
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $condition = array('pro_transaction_id' => $property_id, 'broker_id' => $brokerID);
        try
        {
            $status = $this->re->update_real_estate($data, $condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Real Estate Updated!',
                'text' => 'Real Estate Details for updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //edit real estate purchase details to database
    function edit_sell_real_estate()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $property_id = $data['hPropertyID'];
        if(isset($data['transaction_date'])) {
            $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
            $data['transaction_date'] = $date->format('Y-m-d');
        }
        $data['transaction_rate'] = $data['sell_trans_rate'];
        $data['amount'] = $data['sell_amount'];
        $data['remarks'] = $data['sell_remarks'];
        $data['adviser_id'] = $data['sell_adviser_id'];
        unset($data['family_id'], $data['pro_transaction_id'], $data['hFamilyID'], $data['hClientID'], $data['hPropertyID'],
        $data['hPropertyType'], $data['hUnit'], $data['hAdviser'], $data['sell_property_name'], $data['h_property_name'], $data['h_pro_type'],
        $data['sell_pro_location'], $data['sell_pro_area'], $data['h_unit_id'], $data['sell_trans_rate'], $data['sell_amount'],
        $data['sell_curr_rate'], $data['sell_pro_upd_on'], $data['sell_remarks'], $data['sell_adviser_id']);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;

        $condition = array('pro_transaction_id' => $property_id, 'broker_id' => $brokerID);
        try
        {
            $status = $this->re->update_real_estate($data, $condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Real Estate Updated!',
                'text' => 'Real Estate Details updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    function delete_real_estate()
    {
        $broker_id = $this->session->userdata('broker_id');
        $real_estate_id = $this->input->post('re_id');
        $condition = array('pro_transaction_id'=> $real_estate_id, 'broker_id' => $broker_id);
        $result = $this->re->get_rent_details(array('pro_transaction_id' => $real_estate_id));
        if($result)
        {
            $message = array(
                'status' => 'error',
                'title'=> 'Real Estate Error on Delete!',
                'text' => 'This record cannot be deleted, please remove all the rent first'
            );
        }
        else
        {
            try
            {
                $status = $this->re->delete_real_estate($condition);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'Real Estate Deleted!',
                    'text' => 'Real Estate Details deleted successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
            }
        }
        echo json_encode($message);
    }

    function realised_calculation()
    {
        $curr_rate = $this->input->post('currRate');
        $trans_id = $this->input->post('pro_trans_id');
        $trans_rate = $this->input->post('transRate');
        $prop_area = $this->input->post('propArea');
        $trans_date = $this->input->post('transDate');
        $updated_date = $this->input->post('updDate');
        $date = DateTime::createFromFormat('d/m/Y', $trans_date);
        $trans_date = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $updated_date);
        $updated_date = $date->format('Y-m-d');
        $amount = $this->input->post('amount');
        $result = $this->real_estate_calculation($trans_id, $curr_rate, $trans_rate, $prop_area, $trans_date, $updated_date, $amount);
        echo json_encode($result);
    }

    private function real_estate_calculation($trans_id, $curr_rate, $trans_rate, $prop_area, $trans_date, $updated_date, $amount)
    {
        $todaysDate=date("y-m-d");
        $appreciation = 0.00;
        $cagr = 0.00;
        if($trans_id != "")
        {
            $whereRent = array('rent_date <= ' => $todaysDate, 'pro_transaction_id' => $trans_id);
            $rent_arr = $this->re->getRentAmount($whereRent);
            if($rent_arr)
            {
                if($rent_arr->amount != "")
                    $appreciation = $rent_arr->amount;
            }


        }
        $final_appreciation = ($curr_rate - $trans_rate) * $prop_area;
        $gain=$final_appreciation;
        //echo $todaysDate, $appreciation, $final_appreciation, $updated_date;
        $final_appreciation += $appreciation;
        $tot_gain = $final_appreciation;
        $tempo = 100 * $tot_gain;
        $principal_amt = $amount;
        $abs_res = $tempo/$principal_amt;
        $abs = round($abs_res, 2);
        $num_days = floor((strtotime($todaysDate) - strtotime($trans_date))/(60*60*24));
        /*$date_diff = $num_days /(365.25 / 12);
        if($date_diff != 0)
        {
            $cagr = round(($abs * 12) / $date_diff, 2);
        }*/
        if($num_days!=0)
        {
            $cagr=round(((365*$abs)/$num_days),2);
        }

        //echo $num_days;

        $data = array('gain' => $gain, 'total_gain' => $tot_gain, 'abs' => $abs, 'cagr' => $cagr);

        $broker_id = $this->session->userdata('broker_id');
        $condition="pro_transaction_id='".$trans_id."' and broker_id='".$broker_id."'";
        $result=$this->re->update_real_estate($data,$condition);

        //var_dump($result);

        return $data;
    }

    function get_real_estate_details()
    {
        $transID = $this->input->post('proTransID');
        $brokerID = $this->session->userdata('broker_id');
        $where = array('pro_transaction_id' => $transID, 'proTrans.broker_id' => $brokerID);
        $result = $this->re->get_real_estate_details($where);
        echo json_encode($result);
    }

    //get rent details if exists via session
    function rent_list()
    {
        $output = array();
        $isEdit = $this->input->post('isEdit');
        if(isset($this->session->userdata['rent']))
        {
            $rentData = $this->session->userdata['rent'];
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            $data = null;
            foreach($rentData as $item)
            {
                $num++;
                $row = array();
                $row['srNo']=$item['srNo'];
                $row['proRentID']=$item['proRentID'];
                $row['fromDate']=$item['fromDate'];
                $row['toDate']=$item['toDate'];
                $row['amount']=$item['amount'];
                if($isEdit == 'true')
                {
                  $permissions=$this->session->userdata('permissions');
                  if($permissions=="3")
                  {
                    //add html for action
                    $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_rent('."'".$item['proRentID']."'".')">
                    <i class="fa fa-trash-o"></i> Edit</a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_rent('."'".$item['proRentID']."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                  }
                  else if($permissions=="2")
                  {
                    //add html for action
                    $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_rent('."'".$item['proRentID']."'".')">
                    <i class="fa fa-trash-o"></i> Edit</a>
                    <a class="btn btn-sm btn-danger disable_btn">
                    <i class="fa fa-trash-o"></i></a>';
                  }
                  else if($permissions=="1"){
                    $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger disable_btn">
                    <i class="fa fa-trash-o"></i></a>';
                  }
                }
                else
                {
                    //add html for action
                    $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                        onclick="edit_rent('."'".$item['srNo']."'".')">
                        <i class="fa fa-trash-o"></i> Edit</a>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                        onclick="delete_rent('."'".$item['srNo']."'".')">
                        <i class="fa fa-trash-o"></i></a>';
                }

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "data"=>$data
            );
        }
        //var_dump($output);
        //output to json format
        echo json_encode($output);
    }

    //add rent details in session
    function add_rent()
    {
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $amount = $this->input->post('rentAmt');
        $isEdit = $this->input->post('isEdit');
        $rentArr = array();
        $countArr = 0;
        if($this->session->userdata('rent'))
        {
            $rentArr = $this->session->userdata('rent');
            $countArr = count($rentArr);
        }

        $pro_rent_id = 0;
        try
        {
            if($isEdit == true)
            {
                $pro_trans_id = $this->input->post('proTransIDEdit');
                $date = DateTime::createFromFormat('d/m/Y', $fromDate);
                $tempFromDate = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $toDate);
                $tempToDate = $date->format('Y-m-d');
                $row = array(
                    'pro_transaction_id' => $pro_trans_id,
                    'from_date' => $tempFromDate,
                    'to_date' => $tempToDate,
                    'amount'=> $amount
                );
                $status = $pro_rent_id = $this->re->add_rent_details($row);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
            }
            $rentArr[$countArr]['srNo'] = $countArr;
            $rentArr[$countArr]['proRentID'] = $pro_rent_id;
            $rentArr[$countArr]['fromDate'] = $fromDate;
            $rentArr[$countArr]['toDate'] = $toDate;
            $rentArr[$countArr]['amount'] = $amount;
            $this->session->set_userdata('rent', $rentArr);
            $message = array(
                'status' => 'success',
                'title'=> 'New Rent Added!',
                'text' => 'Rent Added for this property'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //get details of selected rent from session and display on popup
    function edit_rent()
    {
        $id = $this->input->post('id');
        $isEdit = $this->input->post('isEdit');
        if($this->session->userdata('rent'))
            $rentArr = $this->session->userdata('rent');
        if($isEdit == 'true')
            $key = array_search($id, array_column($rentArr, 'proRentID'));
        else
            $key = array_search($id, array_column($rentArr, 'srNo'));
        $data = $rentArr[$key];
        echo json_encode($data);
    }

    //edit details of rent in session
    function update_rent()
    {
        $rentSrNo = $this->input->post('srNoEdit');
        $proRentID = $this->input->post('proRentIDEdit');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $rentAmt = $this->input->post('rentAmt');
        $isEdit = $this->input->post('isEdit');

        if($this->session->userdata('rent'))
            $rentArr = $this->session->userdata('rent');

        foreach ($rentArr as $key => $element) {
            if ($element['srNo'] == $rentSrNo) {
                // update some data
                $rentArr[$key]['fromDate'] = $fromDate;
                $rentArr[$key]['toDate'] = $toDate;
                $rentArr[$key]['amount'] = $rentAmt;
                break;
            }
        }
        try
        {
            if($isEdit == 'true')
            {
                $date = DateTime::createFromFormat('d/m/Y', $fromDate);
                $tempFromDate = $date->format('Y-m-d');
                $date = DateTime::createFromFormat('d/m/Y', $toDate);
                $tempToDate = $date->format('Y-m-d');
                $row = array(
                    'from_date' => $tempFromDate,
                    'to_date' => $tempToDate,
                    'amount'=> $rentAmt
                );

                $status = $this->re->update_rent_details($row, array('pro_rent_id' => $proRentID));
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                unset($_SESSION['rent']);
                $this->session->set_userdata('rent', $rentArr);
                $message = array(
                    'status' => 'success',
                    'title'=> 'Rent Updated!',
                    'text' => 'Insurance Company "'.$this->input->post('insCompName').'" updated successfully'
                );
            }
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }

        echo json_encode($message);
    }

    //delete rent from session
    function delete_rent()
    {
        $id = $this->input->post('id');
        $isEdit = $this->input->post('isEdit');
        if($this->session->userdata('rent'))
            $rentArr = $this->session->userdata('rent');
        try
        {
            if($isEdit == 'true')
            {
                $key = array_search($id, array_column($rentArr, 'proRentID'));
                $row = array(
                    'pro_rent_id' => $id
                );
                $status = $this->re->delete_rent($row);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
            }
            else
                $key = array_search($id, array_column($rentArr, 'srNo'));
            unset($rentArr[$key]);

            $countArr = 0;
            $tempArr = null;
            if(count($rentArr) > 0)
            {
                foreach($rentArr as $item)
                {
                    $tempArr[$countArr]['srNo'] = $countArr + 1;
                    $tempArr[$countArr]['proRentID'] = $item['proRentID'];
                    $tempArr[$countArr]['fromDate'] = $item['fromDate'];
                    $tempArr[$countArr]['toDate'] = $item['toDate'];
                    $tempArr[$countArr]['amount'] = $item['amount'];
                    $countArr++;
                }
            }
            unset($_SESSION['rent']);
            $this->session->set_userdata('rent', $tempArr);
            $message = array('status' => 'success', 'title' => 'Rent Deleted', 'text' => 'Rent deleted successfully for this property');
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //function for properties
    //gets properties of a client
    function get_properties()
    {
        $client = $this->input->post('client_id');
        $result = $this->re->get_properties_dropdown(array('client_id' => $client, 'transaction_type' => 'Purchase'));
        echo json_encode($result);
    }

    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['adv'] = $this->adv->get_adviser_broker_dropdown($brokerID);
        $data['prop_types'] = $this->re->get_property_types();
        $data['units'] = $this->re->get_property_units();
        return $data;
    }

    //Reports
    function re_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Real Estate Report';
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
        $this->load->view('broker/real_estate/re_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_re_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
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
        $re_rep = $this->re->get_real_estate_report($type, $where);
        if(!empty($re_rep))
        {
            unset($_SESSION['re_report']);
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
            $re_rep_array = array('re_rep_data' => $re_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('re_report', $re_rep_array);
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
                .amount { text-align:left; padding:10px; text-indent: 5px; font-weight: bold; }
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
            $eq_data = $this->input->post('htmlData');
            $logo = $this->input->post('logo');
            $name = $this->input->post('name');

            /** Error reporting */
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/tcpdf/tcpdf.php');

            // create new PDF document
            $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            //$fontname = TCPDF_FONTS::addTTFfont('application/third_party/tcpdf/fonts/sourcesanspro.ttf', 'TrueTypeUnicode', '', 32);
            //$fontname2 = TCPDF_FONTS::addTTFfont('application/third_party/tcpdf/fonts/sourcesansprob.ttf', 'TrueTypeUnicode', '', 32);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle('Real Estate Report');
            $pdf->SetSubject('Real Estate Report');
            $pdf->SetKeywords('Real Estate, report');

            $title = '';
            // set default header data
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
            $footerLogo = base_url('assets/users/img/logo-footer.jpg');
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
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // ---------------------------------------------------------

            // set font
            //$pdf->SetFont('dejavusans', '', 9);
            $pdf->SetFont('sourcesanspro','', 12);

            $pdf->AddPage();

            // output the HTML content
            $pdf->writeHTML($css_data.$title_data.$eq_data, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();
  ob_end_clean();
            //Close and output PDF document
            $pdf->Output($name.' Real Estate Portfolio.pdf', 'D');
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
            header('Content-Disposition: attachment;filename=Real Estate Portfolio.xlsx'); // specify the download file name
            header('Cache-Control: max-age=0');

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
