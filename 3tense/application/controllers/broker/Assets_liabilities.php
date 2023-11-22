<?php
if(!defined('BASEPATH')) exit('No direct path access are allowed');

class Assets_liabilities extends CI_Controller{
    function __construct()
    {
        parent :: __construct();
        //load library, helpers
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        $this->load->helper('url');
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }

        //load Assets_liabilities_model, 'al' is object
        $this->load->model('Assets_liabilities_model', 'al');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Al_types_model', 'type');
        $this->load->model('Al_companies_model', 'comp');
        $this->load->model('Al_schemes_model', 'sch');
        $this->load->model('Al_products_model', 'pro');
        $this->load->model('Mutual_funds_model', 'mf');
    }

    //asset liabilities list page
    function index()
    {
        $header['title'] = 'Assets';
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
        $this->load->view('broker/asset_liability/asset_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //asset liabilities list page
    function assets_list()
    {
        $header['title'] = 'Assets';
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
        $this->load->view('broker/asset_liability/asset_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //asset liabilities list page
    function liabilities_list()
    {
        $header['title'] = 'Liabilities';
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
        $this->load->view('broker/asset_liability/liability_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all asset details from database
    public function asset_ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->al->get_asset_list(array('at.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $asset)
        {
            $row = array();
            $num++;
            $row['family_name'] = $asset->family_name;
            $row['client_name'] = $asset->client_name;
            $row['product_name'] = $asset->product_name;
            $row['folio_no'] = $asset->folio_no;
            $row['cease_date'] = $asset->cease_date;
            $row['company_name'] = $asset->company_name;
            $row['scheme_name'] = $asset->scheme_name;
            $row['type_name'] = $asset->type_name;
            $row['goal'] = $asset->goal;
            $row['ref_number'] = $asset->ref_number;
            $row['start_date'] = $asset->start_date;
            $row['end_date'] = $asset->end_date;
            $row['installment_amount'] = round($asset->installment_amount);
            //$row['rate_of_return'] = $asset->rate_of_return.'%';
            //$row['expected_mat_value'] = round($asset->expected_mat_value);
            $row['rate_of_return'] = ($asset->rate_of_return > 0 ) ? $asset->rate_of_return.'%':'';
            $row['expected_mat_value'] = ($asset->expected_mat_value > 0 ) ? round($asset->expected_mat_value):''; 
            $row['narration'] = $asset->narration;
            $permissions=$this->session->userdata('permissions');

                if($permissions == "3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions == "2" || $permissions == "1")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
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

    //access top liabiltiy and asset records
     public function asset_ajax_list_top()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->al->get_asset_list_top("broker_id='".$brokerID."'");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $asset)
        {
            $row = array();
            $num++;
            $row['client_name'] = $asset->name;
            $row['product_name'] = $asset->product_name;
            $row['ref_number'] = $asset->ref_number;
            $row['start_date'] = date("d/m/Y", strtotime($asset->start_date));
            $row['installment_amount'] = round($asset->installment_amount);
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

      //access top liabiltiy and asset records
     public function asset_ajax_list_mat()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->al->get_asset_list_mat("broker_id='".$brokerID."' and maturity_date>=CURRENT_DATE()");
        //echo $list;
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $asset)
        {
            $row = array();
            $num++;
            $row['client_name'] = $asset->name;
            $row['product_name'] = $asset->product_name;
            $row['ref_number'] = $asset->ref_number;
            $row['maturity_date'] = date("d/m/Y", strtotime($asset->maturity_date));
            $row['maturity_amount'] = round($asset->maturity_amount);
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }


//gets all asset details from database - extended search
    public function asset_ajax_list_extended()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->al->get_asset_list_extended('at.broker_id='.$brokerID.' maturity_date<='.$_POST['from'].' and maturity_date>='.$_POST['to']."'");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $asset)
        {
            $row = array();
            $num++;
            $row['family_name'] = $asset->family_name;
            $row['client_name'] = $asset->client_name;
            $row['product_name'] = $asset->product_name;
            //Akshay Karde - 2017-05-26
            $row['folio_no'] = $asset->folio_no;
            $row['cease_date'] = $asset->cease_date;
            $row['company_name'] = $asset->company_name;
            $row['scheme_name'] = $asset->scheme_name;
            $row['type_name'] = $asset->type_name;
            $row['goal'] = $asset->goal;
            $row['ref_number'] = $asset->ref_number;
            $row['start_date'] = $asset->start_date;
            $row['end_date'] = $asset->end_date;
            $row['installment_amount'] = round($asset->installment_amount);
            //$row['rate_of_return'] = $asset->rate_of_return.'%';
            //$row['expected_mat_value'] = round($asset->expected_mat_value);
            //Akshay Karde - 2017-05-26
            $row['rate_of_return'] = ($asset->rate_of_return > 0 ) ? $asset->rate_of_return.'%':'';
            $row['expected_mat_value'] = ($asset->expected_mat_value > 0 ) ? round($asset->expected_mat_value):''; 
            $row['narration'] = $asset->narration;

            $permissions=$this->session->userdata('permissions');

                if($permissions == "3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
                }
                else if($permissions == "2" || $permissions == "1")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_asset('."'".$asset->asset_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
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


    //gets all liability details from database
    public function liability_ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->al->get_liability_list(array('lt.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $liability)
        {
            $row = array();
            $num++;
            $row['family_name'] = $liability->family_name;
            $row['client_name'] = $liability->client_name;
            $row['product_name'] = $liability->product_name;
            $row['company_name'] = $liability->company_name;
            $row['scheme_name'] = $liability->scheme_name;
            $row['type_name'] = $liability->type_name;
            $row['particular'] = $liability->particular;
            $row['ref_number'] = $liability->ref_number;
            $row['start_date'] = $liability->start_date;
            $row['end_date'] = $liability->end_date;
            $row['pre_payment'] = $liability->pre_payment;
            $row['installment_amount'] = round($liability->installment_amount);
            $row['interest_rate'] = $liability->interest_rate.'%';
            $row['total_liability'] = round($liability->total_liability);
            $row['narration'] = $liability->narration;
            $permissions=$this->session->userdata('permissions');

                if($permissions == "3")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_liability('."'".$liability->liability_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_liability('."'".$liability->liability_id."'".')">
                <i class="fa fa-trash-o"></i></a>';

                }
                else if($permissions == "2" || $permissions == "1")
                {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_liability('."'".$liability->liability_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
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

    //function for fixed deposit add form
    function add_form()
    {
        $trans_type = $_GET['trans_type'];
        //data to pass to header view like page title, css, js
        $header['title']='Add Asset or Liability';
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
        //get details of asset or liability for the form
        $data = $this->fill_form();
        $data['transaction_type'] = $trans_type;
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/asset_liability/al_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/add_al_product');
        $this->load->view('broker/master/add_al_scheme');
        $this->load->view('broker/master/add_al_type');
        $this->load->view('broker/master/add_al_company');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit_form()
    {
        if(isset($_GET['id']) && isset($_GET['trans_type'])) {
            $trans_id = $_GET['id'];
            $trans_type = $_GET['trans_type'];
            $brokerID = $this->session->userdata('broker_id');
            $data['action'] = 'edit';
            $asset_liab = array();
            //get details of asset liability for the form
            if($trans_type == 'asset')
            {
                $header['title']='Edit Asset Details - '.$trans_id;
                $condition = array(
                    'asset_id' => $trans_id,
                    'fam.broker_id' => $brokerID
                );
                $asset_liab = $this->al->get_asset_details($condition);
                $asset_liab->pro_transaction_id = $asset_liab->asset_id;
                $asset_liab->transaction_type = 'asset';
            }
            else if($trans_type == 'liability')
            {
                $header['title']='Edit Liability Details - '.$trans_id;
                $condition = array(
                    'liability_id' => $trans_id,
                    'fam.broker_id' => $brokerID
                );
                $asset_liab = $this->al->get_liability_details($condition);
                $asset_liab->pro_transaction_id = $asset_liab->liability_id;
                $asset_liab->transaction_type = 'liability';
            }
            $data = $this->fill_form();

            $data['asset_liab'] = $asset_liab;

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
            $this->load->view('broker/asset_liability/al_edit_form', $data);
            $this->load->view('broker/master/add_family');
            $this->load->view('broker/master/add_al_product');
            $this->load->view('broker/master/add_al_scheme');
            $this->load->view('broker/master/add_al_type');
            $this->load->view('broker/master/add_al_company');
            $this->load->view('broker/common/notif');
            $this->load->view('broker/common/footer');
        } else {
            /* has come to Edit page without ID, so redirect to some other page */
        }
    }

    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $condition = "broker_id = '$brokerID' OR broker_id IS NULL";
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['al_product'] = $this->pro->get_product_broker_dropdown($condition);
        $data['al_company'] = $this->comp->get_company_broker_dropdown($condition);
        //$data['al_scheme'] = $this->sch->get_scheme_broker_dropdown($condition);
        $data['al_scheme'] = $this->mf->get_mf_schemes_broker_dropdown('scheme_status = 1');
        $data['al_type'] = $this->type->get_type_broker_dropdown($condition);
        return $data;
    }

    function add_asset()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        unset($data['family_id'], $data['transaction_type'], $data['pro_transaction_id']);
        $date = DateTime::createFromFormat('d/m/Y', $data['start_date']);
        $data['start_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['end_date']);
        $data['end_date'] = $date->format('Y-m-d');
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');
        
        //Salmaan - 2017-07-17 - for removing compulsory Scheme ID field
        if(!isset($data['scheme_id']) || empty($data['scheme_id'])) {
            $data['scheme_id'] = null;
        } 

        //Akshay Karde 2017-05-31
        if($data['cease_date'] !=='')
        {
            $date = DateTime::createFromFormat('d/m/Y', $data['cease_date']);
            $data['cease_date'] = $date->format('Y-m-d');
        }
        else
        {
            $data['cease_date'] = null;
        }

        $cond = "`ref_number` = '".trim($data['ref_number'])."' AND (`broker_id` = '".$data['broker_id']."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->al->check_duplicate('asset_transactions',$cond);
        if(!$isDuplicate) {
            try
            {
                $asset_id = $this->al->add_asset($data);
                //if there is any error
                if(isset($asset_id['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'New Asset Added!',
                    'transaction_id' => $asset_id,
                    'text' => 'Asset Details added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($asset_id['code']));
            }
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
            if(!isset($asset_id['code']))
            {
                while($start_date <= $end_date)
                {
                    try
                    {
                        $status = $this->al->add_asset_maturity(array('asset_id'=>$asset_id, 'maturity_date' => $start_date, 'maturity_amount' => $data['installment_amount']));
                        //if there is any error
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    catch(Custom_exception $e)
                    {
                        //display custom message
                        $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
                    }
                    $start_date = date('Y-m-d', strtotime($start_date. ' + 1 month'));
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

    function add_liability()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $date = DateTime::createFromFormat('d/m/Y', $data['l_start_date']);
        $data['start_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['l_end_date']);
        $data['end_date'] = $date->format('Y-m-d');
        $data['ref_number'] = $data['l_ref_number'];
        $data['narration'] = $data['l_narration'];
        $data['installment_amount'] = $data['l_installment_amount'];
        unset($data['family_id'], $data['transaction_type'], $data['pro_transaction_id'], $data['l_ref_number'], $data['l_narration'],
        $data['l_installment_amount'], $data['l_end_date'], $data['l_start_date']);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $brokerID;
        $data['added_on'] = date('Y-m-d');
        
        
        //Salmaan - 2018-02-20 - for removing compulsory Scheme ID field
        if(!isset($data['scheme_id']) || empty($data['scheme_id'])) {
            $data['scheme_id'] = null;
        } 

        $cond = "`ref_number` = '".trim($data['ref_number'])."' AND (`broker_id` = '".$data['broker_id']."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->al->check_duplicate('liability_transactions',$cond);
        if(!$isDuplicate) {
            try
            {
                $liability_id = $this->al->add_liability($data);
                //echo json_encode($liability_id);
                //if there is any error
                if(isset($liability_id['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'New Liability Added!',
                    'transaction_id' => $liability_id,
                    'text' => 'Liability Details added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($liability_id['code']));
            }
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
            if($liability_id != null)
            {
                while($start_date <= $end_date)
                {
                    $mat_data = array(
                        'liability_id'=>$liability_id,
                        'maturity_date' => $start_date,
                        'maturity_amount' => $data['installment_amount'],
                        'interest_rate' => $data['interest_rate'],
                    );
                    try
                    {
                        $this->al->add_liability_maturity($mat_data);
                        //if there is any error
                        if(isset($liability_id['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                    catch(Custom_exception $e)
                    {
                        //display custom message
                        $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($liability_id['code']));
                    }
                    $start_date = date('Y-m-d', strtotime($start_date. ' + 1 month'));
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

    function edit_asset()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $asset_id = $data['hAssetLiabilityID'];
        $date = DateTime::createFromFormat('d/m/Y', $data['start_date']);
        $start_date = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['end_date']);
        $end_date = $date->format('Y-m-d');
        $asset_data['end_date'] = $end_date;
        $asset_data['expected_mat_value'] = $data['expected_mat_value'];
        $asset_data['goal'] =$data['goal']; //Akshay Karde - 2017-05-26
        $asset_data['narration'] = $data['narration'];
        $asset_data['user_id'] = $this->session->userdata('user_id');
        
        //Salmaan - 2017-07-17 - for removing compulsory Scheme ID field
        if(!isset($data['scheme_id']) || empty($data['scheme_id'])) {
            $data['scheme_id'] = null;
        }

        //Akshay Karde - 2017-05-31
        if($data['cease_date'] !=='')
        {
            $date = DateTime::createFromFormat('d/m/Y', $data['cease_date']);
            $asset_data['cease_date'] = $date->format('Y-m-d');
        }
        else
        {
            $asset_data['cease_date'] = null;
        }
        
        if($data['folio_no'] !=='')
          $asset_data['folio_no'] = $data['folio_no'];
        else  
          $asset_data['folio_no'] = '';

        /*$cond = "`ref_number` = '".$data['ref_number']."' AND `asset_id` != '".$asset_id."' AND (`broker_id` = '".$brokerID."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->al->check_duplicate('asset_transactions',$cond);*/
        $isDuplicate = false;
        if(!$isDuplicate) {
            $condition = array('asset_id' => $asset_id, 'broker_id' => $brokerID);
            try
            {
                $status = $this->al->update_assets($asset_data, $condition);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $status = $this->al->delete_asset_maturity(array('asset_id' => $asset_id));
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'Asset Updated!',
                    'text' => 'Asset Details updated successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
            }

            while($start_date <= $end_date)
            {
                $mat_data = array(
                    'asset_id'=>$asset_id,
                    'maturity_date' => $start_date,
                    'maturity_amount' => $data['installment_amount']
                );
                try
                {
                    $status = $this->al->add_asset_maturity($mat_data);
                    //if there is any error
                    if(isset($status['code']))
                    {
                        throw new Custom_exception();
                    }
                }
                catch(Custom_exception $e)
                {
                    //display custom message
                    $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
                }
                $start_date = date('Y-m-d', strtotime($start_date. ' + 1 month'));
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

    function edit_liability()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $liability_id = $data['hAssetLiabilityID'];
        $date = DateTime::createFromFormat('d/m/Y', $data['l_end_date']);
        $liab_data['end_date'] = $date->format('Y-m-d');
        $liab_data['installment_amount'] = $data['l_installment_amount'];
        $liab_data['interest_rate'] = $data['interest_rate'];
        $liab_data['pre_payment'] = '0';
        if(isset($data['pre_payment']))
            $liab_data['pre_payment'] = $data['pre_payment'];
        $liab_data['total_liability'] = $data['total_liability'];
        $liab_data['narration'] = $data['l_narration'];
        $liab_data['user_id'] = $this->session->userdata('user_id');

        /*$cond = "`ref_number` = '".$data['ref_number']."' AND `liability_id` != '".$liability_id."' AND (`broker_id` = '".$brokerID."' OR `broker_id` IS NULL)";
        $isDuplicate = $this->al->check_duplicate('liability_transactions',$cond);*/
        $isDuplicate = false;
        if(!$isDuplicate) {
            $condition = array('liability_id' => $liability_id, 'broker_id' => $brokerID);
            try
            {
                $status = $this->al->update_liability($liab_data, $condition);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                } else {
                    //now update liability maturity table
                    $status = $this->al->update_liability_maturity(array('maturity_amount'=>$data['l_installment_amount']), 'liability_id = '.$liability_id.' AND maturity_date > "'.date('Y-m-d').'"');
                    if(isset($status['code'])) {
                        throw new Custom_exception();
                    }
                    $message = array(
                        'status' => 'success',
                        'title'=> 'Liability Updated!',
                        'text' => 'Liability Details updated successfully'
                    );
                }

            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
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

    function delete_asset()
    {
        $asset_id = $this->input->post('asset_id');
        $brokerID = $this->session->userdata('broker_id');
        $mat_condition = array('asset_id'=> $asset_id);
        $trans_condition = array('asset_id'=> $asset_id, 'broker_id' => $brokerID);
        try
        {
            $status = $this->al->delete_asset_details($mat_condition, $trans_condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Asset Deleted!',
                'text' => 'Asset Details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    function delete_liability()
    {
        $liability_id = $this->input->post('liability_id');
        $condition = array('liability_id'=> $liability_id);
        try
        {
            $status = $this->al->delete_liability_details($condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Liability Deleted!',
                'text' => 'Liability Details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    function payment_list()
    {
        $liability_id = $this->input->post('liabID');
        $payData = $this->al->get_payment_list(array('liability_id' => $liability_id));
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = null;
        foreach($payData as $item)
        {
            $num++;
            $row = array();
            $row['liab_hist_id'] = $item->liability_history_id;
            $row['amount']=$item->amount;
            $row['payment_date']=$item->payment_date;
            $row['narration']=$item->narration;
            //add html for action
            $permissions=$this->session->userdata('permissions');
            if($permissions=="3")
            {
            $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_payment('."'".$item->liability_history_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
            }
            else {
              $row['action'] = '<a class="btn btn-sm btn-danger disable_btn">
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

    function add_payment()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $pay_data['liability_id'] = $data['liabilityIDEdit'];
        $pay_data['amount'] = $data['amount'];
        $date = DateTime::createFromFormat('d/m/Y', $data['history_date']);
        $pay_data['payment_date'] = $date->format('Y-m-d');
        $pay_data['narration'] = $data['hist_narration'];
        $status1 = false;
        try
        {
            $status = $this->al->add_liability_payment($pay_data);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $status1 = true;
            /*$message = array(
                'status' => 'success',
                'title'=> 'Payment Added!',
                'text' => 'Payment Details for '.$pay_data['liability_id'].' added successfully'
            );*/
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $status1 = false;
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
        }

        if($status1) {
            $pre_data['liabilityID'] = $pay_data['liability_id'];
            $ppDate = DateTime::createFromFormat('d/m/Y', $data['originalStartDate']);
            $pre_data['prepayDate'] = $ppDate->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $data['hist_end_date']);
            $pre_data['endDate'] = $date->format('Y-m-d');
            $pre_data['amount'] = $data['originalAmt'];
            $pre_data['interest_rate'] = $data['liabilityIntRate'];
            try
            {
                $status = $this->al->liability_pre_payment($pre_data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'Payment Added!',
                    'text' => 'Payment Details for liability added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
            }
        } else {
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => 'Could not add Liability Payment entry.');
        }
        echo json_encode($message);
    }

    function delete_payment()
    {
        $id = $this->input->post('id');
        try
        {
            $status = $this->al->delete_payment(array('liability_history_id' => $id));
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Payment Delete!',
                'text' => 'Payment Details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    function calculate_maturity()
    {
        $rate_of_return = $this->input->post('rate_of_return')/400;
        $install_amt = $this->input->post('install_amount');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('start_date'));
        $start_date = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('end_date'));
        $end_date = $date->format('Y-m-d');
        $mat_value = 0;

        $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
        $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month

        $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
        $exp2 = 1 + $rate_of_return;
        $exp3 = -0.33333;
        $exp4 = 1 - (pow($exp2, $exp3));
        if($exp4 > 0)
            $mat_value = ($install_amt * $exp1) / $exp4;
        echo json_encode(array('mat_mat' => round($mat_value)));
    }

    //Reports
    function al_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Assets and Liability Report';
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

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/asset_liability/al_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_al_report()
    {
        $family_id = $this->input->post('famName');
        $brokerID = $this->session->userdata('broker_id');
        $where = "";
        if($family_id != null && $family_id != '')
        {
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $asset_rep = $this->al->get_asset_report($where);
        $liability_rep = $this->al->get_liability_report($where);

        if(!empty($asset_rep) || !empty($liability_rep))
        {
            unset($_SESSION['al_report']);
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
            $rep_info = array('logo' => $logo);
            $al_rep_array = array('asset_rep_data' => $asset_rep, 'liability_rep_data' => $liability_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('al_report', $al_rep_array);
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
            $al_data = $this->input->post('htmlData');
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

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle('Assets & Liabilities Report');
            $pdf->SetSubject('Assets & Liabilities Report');
            $pdf->SetKeywords('assets, liabilities, report');

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
            $pdf->SetFont('sourcesanspro','', 12);

            $pdf->AddPage();

            // output the HTML content
            $pdf->writeHTML($css_data.$title_data.$al_data, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();
            ob_end_clean();
            //Close and output PDF document
            $pdf->Output($name . ' Family Asset and liabilities Portfolio.pdf', 'D');
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
            header('Content-Disposition: attachment;filename=Asset and liabilities Portfolio.xlsx'); // specify the download file name
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
    
    //Akshay Karde - 2017-06-22
    function get_al_schemes_ajax()
    {
          //$brokerID = $this->session->userdata('broker_id');
          //$condition = "broker_id = '$brokerID' OR broker_id IS NULL";
          //$data = $this->sch->get_scheme_broker_dropdown($condition);
          $data = $this->mf->get_mf_schemes_broker_dropdown_ajax('scheme_status = 1');

          echo json_encode(array('data'=>$data));

    }
}
