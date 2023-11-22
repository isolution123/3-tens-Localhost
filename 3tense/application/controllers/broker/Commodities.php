<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Commodities extends CI_Controller{
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
        $this->load->model('Commodities_model', 'comm');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Advisers_model', 'adv');
        $this->load->model('Funds_model', 'fund');
    }

    //Real Estate List
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Commodities Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
        );
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/commodity/commodity_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all commodities details from database
    function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->comm->get_commodities(array('ct.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $comm)
        {
            $row = array();
            $num++;
            $row['family_name'] = $comm->family_name;
            $row['client_name'] = $comm->client_name;
            $row['transaction_date'] = $comm->transaction_date;
            $row['transaction_type'] = $comm->transaction_type;
            $row['item_name'] = $comm->item_name;
            $row['current_rate'] = round($comm->current_rate);
            $row['transaction_rate']=round($comm->transaction_rate);
            $row['quantity'] = $comm->quantity;
            $row['unit_name'] = $comm->unit_name;
            $row['quality'] = $comm->quality;
            $row['adviser_name'] = $comm->adviser_name;
            $row['total_amount'] = round($comm->total_amount);

            $permissions=$this->session->userdata('permissions');

            if($permissions=="3")
            {
              $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_commodity('."'".$comm->commodity_trans_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_commodity('."'".$comm->commodity_trans_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
            }
            else if($permissions == "2")
            {
              $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_commodity('."'".$comm->commodity_trans_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
            }
            else if($permissions == "1")
            {
              $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_commodity('."'".$comm->commodity_trans_id."'".')">
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

    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['adv'] = $this->adv->get_adviser_broker_dropdown($brokerID);
        $broker_condition = 'broker_id is null or broker_id="'.$brokerID.'"';
        $data['items'] = $this->comm->get_commodity_items($broker_condition);
        $data['units'] = $this->comm->get_commodity_units_list($broker_condition);
        //$data['units'] = $this->comm->get_commodity_units('cu.broker_id is null or cu.broker_id="'.$brokerID.'"');
        return $data;
    }

    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Purchase or Sell Commodity';
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
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/commodity/commodity_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/commodity_item_add');
        $this->load->view('broker/master/commodity_unit_add');
        $this->load->view('broker/master/commodity_rate_add', $data);
        $this->load->view('broker/master/add_adviser');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit_form()
    {
        if(isset($_GET['id'])) {
            $trans_id = $_GET['id'];
            $sold = 0;
            $brokerID = $this->session->userdata('broker_id');
            $data['action'] = 'edit';

            $condition = array(
                'ct.commodity_trans_id' => $trans_id,
                'ct.broker_id' => $brokerID
            );
            //get details of insurance for the form
            $data = $this->fill_form();
            // get insurance info from database by policy number
            $comm_data = $this->comm->get_commodities($condition);
            $data['commodity'] = $comm_data[0];
            if($comm_data[0]->transaction_type == "Sale") { $sold=1; }
            $data['isSold'] = $sold;

            $header['title']='Edit Commodity - '.$trans_id;
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
            $this->load->view('broker/commodity/commodity_edit_form', $data);
            $this->load->view('broker/master/add_family');
            $this->load->view('broker/master/commodity_item_add');
            $this->load->view('broker/master/commodity_unit_add');
            $this->load->view('broker/master/commodity_rate_add', $data);
            $this->load->view('broker/master/add_adviser');
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            /* has come to Edit page without ID, so redirect to some other page */
        }
    }

    //get the units of selected commodity item
    function get_commodity_item_units()
    {
        if(isset($_POST['item_id'])) {
            $brokerID = $this->session->userdata('broker_id');
            $itemID = $this->input->post('item_id');
            $condition = 'ci.item_id = "'.$itemID.'" and (cu.broker_id is null or cu.broker_id = "'.$brokerID.'")';
            $units = $this->comm->get_commodity_units($condition);
            if($units) {
                echo json_encode($units);
            } else {
                echo 'Data error';
            }
        } else {
            echo 'You are not allowed to access this';
        }
    }

    //get the rate of selected commodity item and unit
    function get_commodity_rate()
    {
        
        if(isset($_POST['item_id']) && isset($_POST['unit_id'])) {
            $brokerID = $this->session->userdata('broker_id');
            $itemID = $this->input->post('item_id');
            $unitID = $this->input->post('unit_id');
            $condition = 'item_id = "'.$itemID.'" and unit_id = "'.$unitID.'" and broker_id = "'.$brokerID.'"';
            $units = $this->comm->get_commodity_rates($condition);
            if($units) {
                echo json_encode($units[0]);
            } else {
                echo 'Data error';
            }
        } else {
            echo 'You are not allowed to access this';
        }
    }

    //get list of commodities of client - for Selling part
    function get_commodities()
    {
        if(isset($_POST['client_id'])) {
            $brokerID = $this->session->userdata('broker_id');
            $clientID = $this->input->post('client_id');
            $condition = 'ct.client_id = "'.$clientID.'" AND ct.transaction_type = "Purchase" AND (ct.broker_id is null or ct.broker_id="'.$brokerID.'")';
            $items = $this->comm->get_commodities($condition);
            echo json_encode($items);
        } elseif($_POST['commTransID']) {
            $brokerID = $this->session->userdata('broker_id');
            $commTransID = $this->input->post('commTransID');
            $condition = 'ct.commodity_trans_id = "'.$commTransID.'" AND ct.transaction_type = "Purchase" AND (ct.broker_id is null or ct.broker_id="'.$brokerID.'")';
            $items = $this->comm->get_commodities($condition);
            echo json_encode($items);
        } else {
            //echo 'You are not allowed to access this';
        }
    }

    function add_commodity()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        unset($data['family_id'], $data['commodity_trans_id']);
        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');

        if(!(isset($data['transaction_rate']) && !empty($data['transaction_rate'])) && isset($data['current_rate']))
        {
            $data['transaction_rate'] = $data['current_rate'];
        }
        unset($data['current_rate']);

        $commID = $this->comm->get_commodity_trans_id(array('brokerID' => $brokerID));
        $data['commodity_trans_id'] = $commID->commodity_trans_id;

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');
        //echo json_encode(var_dump($data));
        try
        {
            $inserted = $this->comm->add_commodity($data);
            if(is_array($inserted)) {
                throw new Custom_exception();
            } else {
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Commodity Added!',
                    'text' => 'Commodity Details added successfully',
                    'commodity_trans_id' => $commID->commodity_trans_id
                );
            }
            echo json_encode($message);
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $response = array(
                "title" => "Could not add Commodity details!",
                "text" => $e->errorMessage($inserted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }

    function sell_commodity()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');

        $data['sale_ref'] = $data['sell_commodity_trans_id'];
        $data['commodity_item_id'] = $data['sell_commodity_item_id'];
        $data['commodity_unit_id'] = $data['sell_commodity_unit_id'];
        $data['quantity'] = $data['sell_quantity'];

        if(!(isset($data['sell_transaction_rate']) && !empty($data['sell_transaction_rate'])) && isset($data['sell_current_rate']))
        {
            $data['transaction_rate'] = $data['sell_current_rate'];
        } else {
            $data['transaction_rate'] = $data['sell_transaction_rate'];
        }
        $data['total_amount'] = $data['sell_total_amount'];
        $data['quality'] = $data['sell_quality'];
        $data['adviser_id'] = $data['sell_adviser_id'];
        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');

        unset($data['family_id'], $data['sell_commodity_trans_id'], $data['sell_commodity_item_id'], $data['sell_item_name'],
        $data['sell_commodity_unit_id'], $data['sell_unit_name'], $data['sell_quantity'], $data['sell_transaction_rate'],
        $data['sell_total_amount'], $data['sell_current_rate'], $data['sell_quality'], $data['sell_adviser_id']);

        $commID = $this->comm->get_commodity_trans_id(array('brokerID' => $brokerID));
        $data['commodity_trans_id'] = $commID->commodity_trans_id;

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');
        //echo json_encode(var_dump($data));
        try
        {
            $inserted = $this->comm->add_commodity($data);
            if(is_array($inserted)) {
                throw new Custom_exception();
            } else {
                $message = array(
                    'type' => 'success',
                    'title'=> 'Commodity Sold!',
                    'text' => 'Selling Commodity Details added successfully',
                    'commodity_trans_id' => $commID->commodity_trans_id
                );
            }
            echo json_encode($message);
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $response = array(
                "title" => "Could not add Commodity details for Selling!",
                "text" => $e->errorMessage($inserted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }


    function update_purchase_commodity()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $commTransID = $data['commodity_trans_id'];
        unset($data['family_id'], $data['commodity_trans_id'], $data['hFamilyID'], $data['hClientID'], $data['hItemID'],
        $data['hUnitID'], $data['hAdviserID']);
        $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
        $data['transaction_date'] = $date->format('Y-m-d');

        if(!(isset($data['transaction_rate']) && !empty($data['transaction_rate'])) && isset($data['current_rate']))
        {
            $data['transaction_rate'] = $data['current_rate'];
        }
        unset($data['current_rate']);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        //echo json_encode(var_dump($data));
        $condition = array('commodity_trans_id' => $commTransID, 'broker_id' => $brokerID);
        try
        {
            $updated = $this->comm->update_commodity($data, $condition);
            if(is_array($updated)) {
                throw new Custom_exception();
            } else {
                $message = array(
                    'type' => 'success',
                    'title'=> 'Commodity Details Updated!',
                    'text' => 'Commodity Details updated successfully',
                    'commodity_trans_id' => $commTransID
                );
            }
            echo json_encode($message);
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $response = array(
                "title" => "Could not update Commodity details!",
                "text" => $e->errorMessage($updated['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }

    function update_sell_commodity()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $commTransID = $data['commodity_trans_id'];

        $data['sale_ref'] = $data['sell_commodity_trans_id'];
        $data['commodity_item_id'] = $data['sell_commodity_item_id'];
        $data['commodity_unit_id'] = $data['sell_commodity_unit_id'];
        $data['quantity'] = $data['sell_quantity'];

        if(!(isset($data['sell_transaction_rate']) && !empty($data['sell_transaction_rate'])) && isset($data['sell_current_rate']))
        {
            $data['transaction_rate'] = $data['sell_current_rate'];
        } else {
            $data['transaction_rate'] = $data['sell_transaction_rate'];
        }
        $data['total_amount'] = $data['sell_total_amount'];
        $data['quality'] = $data['sell_quality'];
        $data['adviser_id'] = $data['sell_adviser_id'];
        if(isset($data['transaction_date']) && !empty($data['transaction_date'])) {
            $date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
            $data['transaction_date'] = $date->format('Y-m-d');
        }

        unset($data['family_id'], $data['commodity_trans_id'], $data['sell_commodity_trans_id'], $data['sell_commodity_item_id'], $data['sell_item_name'],
        $data['sell_commodity_unit_id'], $data['sell_unit_name'], $data['sell_quantity'], $data['sell_transaction_rate'],
        $data['sell_total_amount'], $data['sell_current_rate'], $data['sell_quality'], $data['sell_adviser_id'],
        $data['hFamilyID'], $data['hClientID'], $data['hItemID'], $data['hUnitID'], $data['hAdviserID']);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        //echo json_encode(var_dump($data));
        $condition = array('commodity_trans_id' => $commTransID, 'broker_id' => $brokerID);
        try
        {
            $updated = $this->comm->update_commodity($data, $condition);
            if(is_array($updated)) {
                throw new Custom_exception();
            } else {
                $message = array(
                    'type' => 'success',
                    'title'=> 'Commodity Details Updated!',
                    'text' => 'Selling Commodity Details updated successfully',
                    'commodity_trans_id' => $commTransID
                );
            }
            echo json_encode($message);
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $response = array(
                "title" => "Could not update Commodity details for Selling!",
                "text" => $e->errorMessage($updated['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }

    function delete_commodity()
    {
        $commID = $this->input->post("id");
        try
        {
            $deleted = $this->comm->delete_commodity(array('commodity_trans_id' => $this->input->post("id")));
            if($deleted === true) {
                $success = array(
                    "type" => "info",
                    "title" => "Commodity transaction deleted!",
                    "text" => "The Commodity transaction you selected has been deleted.",
                    "deleted" => true
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
                "title" => "Could not delete Commodity transaction!",
                "text" => $e->errorMessage($deleted['code']),
                "type" => "error"
            );
            echo json_encode($response);
        }
    }



    //Reports
    function commodity_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Commodity Report';
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
        $this->load->view('broker/commodity/commodity_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_commodity_report()
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
        $comm_rep = $this->comm->get_commodity_report($type, $where);
        if(!empty($comm_rep))
        {
            unset($_SESSION['commodity_report']);
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
            $comm_rep_array = array('comm_rep_data' => $comm_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('commodity_report', $comm_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }



    /* Export Functions */
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
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                    </style>';
            $title_data = $this->input->post('titleData');
            $eq_data = $this->input->post('htmlData');
            $logo = $this->input->post('logo');
            $report_name = $this->input->post('report_name');

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
            $pdf->SetTitle('Commodity Report');
            $pdf->SetSubject('Commodity Report');
            $pdf->SetKeywords('commodity, report');

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
            $pdf->writeHTML($css_data.$title_data.$eq_data, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();

              ob_end_clean();
            //Close and output PDF document
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
            //header('Content-Disposition: attachment;filename=Commodity Portfolio.xlsx'); // specify the download file name
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
                        <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
