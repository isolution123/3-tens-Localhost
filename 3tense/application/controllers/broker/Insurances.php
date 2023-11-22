<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Insurances extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->library('Common_lib');
        $this->load->helper('url');

        //load models with objects
        $this->load->model('Insurance_model', 'ins');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Premium_types_model', 'prem_type');
        $this->load->model('Insurance_plans_model', 'ins_plans');
        $this->load->model('Insurance_companies_model', 'ins_comp');
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Banks_model', 'bank');
        $this->load->model('Common_model', 'common');
        $this->load->model('Advisers_model', 'adv');
        $this->load->model('Scheme_model', 'scheme');
        
        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    //function for insurance list page
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Insurance Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css',
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css'
        );
        $header['js'] = array(
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/js/common.js'
        );

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/ins_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all insurance details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->ins->get_insurances(array('ins.broker_id' => $brokerID));

        /* get data passed by Datatables and send to Reminders_model */
        $aColumns = array('1',
        'fam.name', 'cli.name', 'commence_date', 'ins_comp_name', 'plan_name', 'plan_type_name', 'amt_insured', 'ins.policy_num', 'prem_amt',
        'next_prem_due_date', 'grace_due_date', 'mode_name', 'prem_pay_mode', 'pstat.status', 'remarks', 'adviser_name', 'maturity_date',
        'prem_paid_till_date', 'fv1.fund_option1','fv1.fund_value1','fv2.fund_option2','fv2.fund_value2');
     
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }


        /*
         * Ordering
         */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            //$sOrder = "ORDER BY  ";
            $sOrder = "";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                        ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
        }


        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "";
            for ( $i=0 ; $i<(count($aColumns)) ; $i++ )
            {
                if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
                {
                    $sWhere .= $aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
                }
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            //$sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<(count($aColumns)) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "";
                }
                else
                {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }
        //echo $_POST['from'];
        $type = $_GET['type'];
        if($_GET['from']!='' and $_GET['to']!='null')
        {
            if($type == 'Premium') {
                $myWhere = "ins.policy_num in( select policy_num from premium_paying_details
                where pstat.status IN('In Force','Grace','Lapsed','Paid Up Cancellation') ";
            } else {
                $myWhere = "ins.policy_num in( select policy_num from premium_maturities where 1 ";
            }

            $from_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["from"]);
            if(is_object($from_date_temp)) {
                $from_date = $from_date_temp->format('Y-m-d');
                if($type == 'Premium') {
                    //$myWhere .= "and date_of_payment>='".$from_date."' ";
                    $myWhere .= "and next_prem_due_date>='".$from_date."' ";
                } else {
                    $myWhere .= "and maturity_date>='".$from_date."' ";
                }
            } else {
                $from_date = '';
            }

            $to_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["to"]);
            if(is_object($to_date_temp)) {
                $to_date = $to_date_temp->format('Y-m-d');
                if($type == 'Premium') {
                    //$myWhere .= "and date_of_payment<='".$to_date."'";
                    $myWhere .= "and next_prem_due_date<='".$to_date."'";
                } else {
                    $myWhere .= "and maturity_date<='".$to_date."'";
                }
            } else {
                $to_date = '';
            }

            $myWhere .= ' ) ';

             /*
             * SQL queries
             * Get data to display
             */
            //var_dump($myWhere); var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->ins->get_insurances_extended("ins.broker_id='".$brokerID."' and ".$myWhere."", $sWhere, $sOrder, $sLimit );
            //print_r($list);
        }
        else
        {
            //var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->ins->get_insurances_extended("ins.broker_id='".$brokerID."'", $sWhere, $sOrder, $sLimit);
            //print_r($list);
        }
   

        /*
         * Output
         */
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list['rResult'] as $ins)
        {
            //$fmt = new NumberFormatter( 'en_IN', NumberFormatter::CURRENCY );
            //echo $fmt->formatCurrency(1234567.891234567890000, "EUR")."\n";

            $row = array();
            $num++;
            $row['family_name'] = $ins->name;
            $row['client_name'] = $ins->client_name;
            $commence_date_temp = DateTime::createFromFormat('Y-m-d', $ins->commence_date);
            $commence_date_temp = $commence_date_temp->format('d/m/Y');
            $row['commence_date'] = $commence_date_temp;
            $row['plan_name'] = $ins->plan_name;
            $row['ins_comp_name'] = $ins->ins_comp_name;
            $row['plan_type_name'] = $ins->plan_type_name;
            $row['amt_insured'] = round($ins->amt_insured);
            //$row['amt_insured'] = $fmt->formatCurrency($ins->amt_insured, "INR");
            $row['policy_num'] = $ins->policy_num;
            $row['prem_amt'] = round($ins->prem_amt);
            //$row['prem_amt'] = $fmt->formatCurrency($ins->prem_amt, "INR");
            $row['prem_type_name'] = $ins->prem_type_name;
            $next_prem_due_date_temp = DateTime::createFromFormat('Y-m-d', $ins->next_prem_due_date);
            $next_prem_due_date_temp = $next_prem_due_date_temp->format('d/m/Y');
            $row['next_prem_due_date'] = $next_prem_due_date_temp;
            $grace_due_date_temp = DateTime::createFromFormat('Y-m-d', $ins->grace_due_date);
            $grace_due_date_temp = $grace_due_date_temp->format('d/m/Y');
            $row['grace_due_date'] = $grace_due_date_temp;
            $row['mode_name'] = $ins->mode_name;
            $row['prem_pay_mode'] = $ins->prem_pay_mode;
            $row['status'] = $ins->status;
            $row['remarks'] = $ins->remarks;
            $row['fund_value'] = $ins->fund_value;
            $row['adviser_name'] = $ins->adviser_name;
            $maturity_date_temp = DateTime::createFromFormat('Y-m-d', $ins->maturity_date);
            $maturity_date_temp = $maturity_date_temp->format('d/m/Y');
            $row['maturity_date'] = $maturity_date_temp;
            $row['prem_paid_till_date'] = round($ins->prem_paid_till_date);
            $row['mat_type'] = $ins->mat_type;
            $row['fund_option1'] = $ins->fund_option1;
            $row['fund_option2'] = $ins->fund_option2;
            $row['fund_value1'] = $ins->fund_value1;
            $row['fund_value2'] = $ins->fund_value2;
            
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {

            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_ins('."'".$ins->policy_num."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="del_ins('."'".$ins->policy_num."'".', '."'".$ins->client_id."'".', '."'".$ins->ins_comp_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_ins('."'".$ins->policy_num."'".')">
                    <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_ins('."'".$ins->policy_num."'".')">
                    <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }

            $data[] = $row;
        }
        /*$output = array(
            "draw"=>1,
            "recordsTotal"=>$this->ins->count_all(array('fam.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->ins->count_filtered(),
            "data"=>$data
        );*/
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $list['iTotal'],
            "iTotalDisplayRecords" => $list['iFilteredTotal'],
            "aaData" => $data
        );
        //output to json format
        echo json_encode($output);
    }

    //get top 5 new insurance
    public function ajax_ins_list_new()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
        $list = $this->ins->get_new_top_new("ins.broker_id='".$brokerID."' and ins.status IN (1,2,3,4)");
        //echo $list;
        $data = array();

        foreach($list as $ins1)
        {
            $row = array();
            //$num++;
            $row['client_name'] = $ins1->client_name;
            $row['commence_date'] = date("d/m/Y", strtotime($ins1->commence_date));
            $row['plan_name'] = $ins1->plan_name;
            $row['policy_num'] = $ins1->policy_num;
            //$row['amt_insured'] = $ins1->amt_insured;
	    $row['prem_amt'] = round($ins1->prem_amt);
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$ins1->policy_num."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$ins1->policy_num."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_mf('."'".$ins1->policy_num."'".')">
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
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //get top 5 maturity
    public function ajax_ins_list_mat()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
        //$list = $this->ins->get_new_top_mat("insurances.broker_id='".$brokerID."' and insurances.policy_num in (select pm.policy_num from premium_maturities pm where pm.maturity_date>CURRENT_DATE()) ");
        $list = $this->ins->get_new_top_mat("insurances.broker_id='".$brokerID."' and premium_maturities.maturity_date>CURRENT_DATE() ");
        //print_r($list);
        $data = array();
        foreach($list as $ins1)
        {
            $row = array();
            //$num++;
            $row['client_name'] = $ins1->name;
            $row['maturity_date'] = $ins1->maturity_date;
            $row['plan_name'] = $ins1->plan_name;
            $row['policy_num'] = $ins1->policy_num;
            $row['amount'] = $ins1->amount;
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$ins1->policy_num."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$ins1->policy_num."'".')">
                <i class="fa fa-trash-o"></i></a>';

            $data[] = $row;
        }
        $output = array(
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }



    //function for insurance add form
    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Add Insurance';
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
        //get details of insurance for the form
        $data = $this->fill_form();
        $this->session->unset_userdata('maturity');
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/ins_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/master/add_adviser');
        $this->load->view('broker/master/add_premium_type');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //add insurance details to database
    function add_ins()
    {
        $data = $_POST;
        //var_dump($data);
        if(!isset($data['adjustment_flag']))
            $data['adjustment_flag'] = 0;
        $planCat = $data['planCategory'];
        $client_name = $data['client_name'];
        $comp_name = $data['compName'];
        $plan_name = $data['planName'];
        unset($data['family_id'], $data['compName'], $data['planName'], $data['planCategory'],
        $data['firstMatAmt'], $data['matDate'], $data['matAmt'], $data['matYear'],
        $data['table_length'], $data['id'], $data['srNoEdit'], $data['matPolicyEdit'],
        $data['matDateEdit'], $data['matAmtEdit'], $data['pol_policy_num'], $data['comp_name'],
        $data['policy_name'], $data['adj_flag'], $data['importFund'], $data['importStake'],
        $data['famID'], $data['famName'], $data['famStatus'], $data['advID'], $data['advName'],
        $data['advCompName'], $data['advProduct'], $data['advAgcCode'], $data['advConPerson'],
        $data['advConNumber'], $data['premTypeID'], $data['premTypeName'], $data['client_name'], $data['ppt'], $data['bt']);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $this->session->userdata('broker_id');
        $where = array(
            'insurances.policy_num'=>$data['policy_num'],
            'insurances.client_id'=>$data['client_id'],
            'insurances.broker_id'=>$data['broker_id']
        );
        $policy = $this->ins->get_insurance_policy_details($where);

        if(empty($policy))
        {
            $pddate = DateTime::createFromFormat('d/m/Y', $data['paidup_date']);
            $data['paidup_date'] = $pddate->format('Y-m-d');
            $mtdate = DateTime::createFromFormat('d/m/Y', $data['maturity_date']);
            $data['maturity_date'] = $mtdate->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $data['commence_date']);
            $data['commence_date'] = $date->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $data['next_prem_due_date']);
            $data['next_prem_due_date'] = $date->format('Y-m-d');
            $date = DateTime::createFromFormat('d/m/Y', $data['grace_due_date']);
            $data['grace_due_date'] = $date->format('Y-m-d');

            //minus 1 installment of premium
            if($data['mode'] == 1) {
                $pddate->modify('-1 year');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 2) {
                $pddate->modify('-6 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 3) {
                $pddate->modify('-3 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 4) {
                $pddate->modify('-1 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            }

            $mat_data = null;
            if(isset($this->session->userdata['maturity']))
            {
                $mat_data = $this->session->userdata['maturity'];
            }
            //var_dump($mat_data);
            //var_dump(count($mat_data));
            if(count($mat_data) > 1)
                $data['mat_type'] = "Regular";
            else
                $data['mat_type'] = "Single";
            try
            {
                
                $insert_id = $this->ins->add_insurance($data);
                //if there is any error
                if(isset($insert_id['code']))
                {
                    throw new Custom_exception();
                }
                if($mat_data != null)
                {
                    $userID = $data['user_id'];
                    $client_id = $data['client_id'];
                    //add maturity and
                    foreach($mat_data as $item)
                    {
                        $date = DateTime::createFromFormat('d/m/Y', $item['matDate']);
                        $item['matDate'] = $date->format('Y-m-d');
                        if(strtoupper($planCat) == "GENERAL INSURANCE") {
                            $row = array(
                                'policy_num' => $item['matPolicy'],
                                'maturity_date' => $item['matDate'],
                                'amount' => 0,
                                'user_id'=> $userID,
                                'client_id' => $client_id
                            );
                        } else {
                            $row = array(
                                'policy_num' => $item['matPolicy'],
                                'maturity_date' => $item['matDate'],
                                'amount' => $item['matAmt'],
                                'user_id'=> $userID,
                                'client_id' => $client_id
                            );
                        }

                        $status = $this->ins->add_maturity($row);
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }

                        $remData = array(
                            'reminder_type'=>'Maturity Reminder',
                            'client_id' => $data['client_id'],
                            'client_name' => $client_name,
                            'broker_id' => $this->session->userdata('broker_id'),
                            'reminder_date' => $item['matDate'],
                            'reminder_message' => $comp_name ." ".$plan_name
                        );
                        //$status = $this->rem->add_reminder($remData);
                        //commented for time-being, maybe its not required - Salmaan 09-08-16
                        if(isset($status['code']))
                        {
                            throw new Custom_exception();
                        }
                    }
                }
                $remData = array(
                    'reminder_type'=>'Premium Reminder',
                    'client_id' => $data['client_id'],
                    'client_name' => $client_name,
                    'broker_id' => $this->session->userdata('broker_id'),
                    'reminder_date' => $data['next_prem_due_date'],
                    'reminder_message' => $comp_name ." ".$plan_name
                );
                //$status = $this->rem->add_reminder($remData);
                //commented for time-being, maybe its not required - Salmaan 23-08-16
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'status' => 'success',
                    'title'=> 'New Insurance Added!',
                    'text' => 'Insurance Details added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($insert_id['code']));
            }
        }
        else
        {
            $message = array(
                'type' => 'error',
                'title'=> 'Error on Adding Insurance!',
                'text' => 'Policy Number for '.$data["policy_num"].' already exists'
            );
        }
        echo json_encode($message);
    }

    //edit insurance details to database
    function edit_ins()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        if(!isset($data['adjustment_flag']))
            $data['adjustment_flag'] = 0;
        $policyNum = $data['policy_num'];
        $planCat = $data['planCategory'];
        $hIsUpdateMaturity = $data['hIsUpdateMaturity'];
        
        
        
        

        //for adding reminder on status change
        $where="status_id=".$data['hStatus']."";
        $oldStatusArr=$this->ins->get_premium_status_for($where);
        $oldStatus=$oldStatusArr[0]->status;

        $where="status_id=".$data['status']."";
        $newStatusArr=$this->ins->get_premium_status_for($where);
        $newStatus=$newStatusArr[0]->status;

        $CLIENT_name_list=$this->client->get_client_info($data['hClientID']);
        $client_name=$CLIENT_name_list->name;

        if($newStatus =='Surrender' && $oldStatus!='Surrender')
        {
            $var2 = array(
                'reminder_type'=>'Insurance Status',
                'client_id' => $data['hClientID'],
                'client_name' => $client_name,
                'reminder_date' => date('Y-m-d'),
                'reminder_message' =>$data['planName']." Policy No.: ".$data['policy_num']." is Surrendered now.",
                'broker_id'=>$brokerID
            );
            $this->rem->add_reminder($var2);
        }
        else if($newStatus =='Paid Up Cancellation' && $oldStatus!='Paid Up Cancellation')
        {
            $var2 = array(
                'reminder_type'=>'Insurance Status',
                'client_id' => $data['hClientID'],
                'client_name' => $client_name,
                'reminder_date' => date('Y-m-d'),
                'reminder_message' =>$data['planName']." Policy No.: ".$data['policy_num']." is Paid Up Cancellation now.",
                'broker_id'=>$brokerID
            );
            $this->rem->add_reminder($var2);
        }

        //get old paidupDate and new paidupDate before unset
        $oldPdup = $data['hPaidupDate'];
        $newPdup = $data['paidup_date'];
        $fupDate = $data['next_prem_due_date'];
        $fupDate0 = $data['next_prem_due_date'];

        $premAmt = $data['prem_amt'];
        $mode = $data['hMode'];

        $firstMatAmt = $data['firstMatAmt'];
        $amtIns = $data['amt_insured'];
        $planName = $data['planName'];

        unset($data['family_id'], $data['compName'], $data['planName'], $data['planCategory'],
        $data['firstMatAmt'], $data['matDate'], $data['matAmt'], $data['matYear'],
        $data['table_length'], $data['id'], $data['srNoEdit'], $data['matPolicyEdit'],
        $data['matDateEdit'], $data['matAmtEdit'], $data['adj_flag'], $data['policy_num'],
        $data['hFamilyID'], $data['hClientID'], $data['hPolicyNum'], $data['hStatus'], $data['hMode'],
        $data['hPremType'], $data['hPremPayMode'], $data['hNominee'], $data['hAdviser'],
        $data['premClientID'], $data['policy_number'], $data['cheque_number'],
        $data['cheque_date'], $data['bank_id'], $data['branch'], $data['account_number'],
        $data['premium_amount'], $data['advisers'], $data['premAdjust'],
        $data['adjustment_ref_number'], $data['narration'], $data['next_premium_due_date'],
        $data['premium_mode'], $data['premBranch'], $data['importFund'], $data['importStake'],
        $data['ppt'], $data['bt'], $data['matDate'],$data['matAmt'],$data['matYear'],
        $data['hPaidupDate'], $data['hMatDate'],$data['hIsUpdateMaturity']);

        $pddate = DateTime::createFromFormat('d/m/Y', $data['paidup_date']);
        $date = DateTime::createFromFormat('d/m/Y', $data['paidup_date']);
        $data['paidup_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['maturity_date']);
        $data['maturity_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['commence_date']);
        $data['commence_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['next_prem_due_date']);
        $data['next_prem_due_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $data['grace_due_date']);
        $data['grace_due_date'] = $date->format('Y-m-d');


        $sDate =  $sDate = $data['commence_date'];
        $eDate = $data['paidup_date'];
        $matDate = $data['maturity_date'];
        $fupDateTemp = DateTime::createFromFormat('d/m/Y', $fupDate);
        $fupDate = $fupDateTemp->format('Y-m-d');


        //if paidup date is changed, minus 1 installment of premium
        if($newPdup != $oldPdup) {
            if($data['mode'] == 1) {
                $pddate->modify('-1 year');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 2) {
                $pddate->modify('-6 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 3) {
                $pddate->modify('-3 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            } elseif($data['mode'] == 4) {
                $pddate->modify('-1 month');
                $data['paidup_date'] = $pddate->format('Y-m-d');
            }


            $sDate = $data['commence_date'];
            $eDate = $data['paidup_date'];
            $matDate = $data['maturity_date'];
            $brokerID = $this->session->userdata('broker_id');
            /*$fupDateTemp = DateTime::createFromFormat('d/m/Y', $fupDate);
            $fupDate = $fupDateTemp->format('Y-m-d');*/

            /*$data['policyNumber'] = $postData['policy_num'];
            $comDate = DateTime::createFromFormat('d/m/Y', $postData['commence_date']);
            $data['comDate'] = $comDate->format('Y-m-d');
            $paidUpDate = DateTime::createFromFormat('d/m/Y', $postData['paidup_date']);
            $data['paidUpDate'] = $paidUpDate->format('Y-m-d');
            $fupDate = DateTime::createFromFormat('d/m/Y', $postData['next_prem_due_date']);
            $data['fupDate'] = $fupDate->format('Y-m-d');
            $data['premiumAmount'] = $postData['prem_amt'];
            $data['premiumPayingMode'] = $postData['mode'];
            $data['brokerID'] = $brokerID;*/

            $condition = array(
                'policyNumber' => $policyNum,
                'comDate' => $sDate,
                'paidUpDate' => $eDate,
                'fupDate' => $fupDate,
                'premiumAmount' => $premAmt,
                'premiumPayingMode' => $mode,
                'brokerID' => $brokerID
            );
            $updated = $this->ins->update_prem_paying($condition);
        }

        $mat_data = $this->session->userdata['maturity'];
        if(count($mat_data) > 1)
            $data['mat_type'] = "Regular";

        $data['user_id'] = $this->session->userdata('user_id');
        $whereCon = array(
            'policy_num' => $policyNum,
            'client_id' => $data['client_id'],
            'broker_id' => $brokerID
        );

        $firstMatAmt = 0;
        $commenceDate = new DateTime($sDate);
        $paidUpDate = new DateTime($eDate);
        $maturityDate = new DateTime($matDate);
        $modeData = $this->ins->get_premium_modes(array("mode_id"=>$mode));
        $mode = $modeData[0]->mode_name;
        $firstMatData = $this->get_mat_amt($amtIns, $mode, $premAmt, $commenceDate, $paidUpDate, $planName, $maturityDate, $brokerID, $firstMatAmt, $fupDate0);
        //var_dump($firstMatData);
        $firstMatAmt = $firstMatData['firstMatAmt'];
        if($hIsUpdateMaturity == "1")
        {
            $mat_update = $this->ins->update_maturity_amt($firstMatAmt,$matDate,$policyNum);
        }

        try
        {
            $row_affected = $this->ins->update_insurance($data, $whereCon);
            //if there is any error
            if(isset($row_affected['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Insurance '.$policyNum.' Updated !',
                'text' => 'Insurance Details updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($row_affected['code']));
        }
        echo json_encode($message);
    }

    function edit_form()
    {
        if(isset($_GET['id'])) {
            $policyNum = $_GET['id'];
            $brokerID = $this->session->userdata('broker_id');
            $data['action'] = 'edit';
            
            $condition = array(
                'policy_num' => $policyNum,
                'fam.broker_id' => $brokerID
            );
            //get details of insurance for the form
            $data = $this->fill_form();
            
            // get insurance info from database by policy number
            $ins_data = $this->ins->get_insurances($condition);
            
            //print_r($ins_data);die;
            
            if($ins_data) {
                
                $mat_data = $this->ins->get_maturity_details(array('policy_num' => $ins_data[0]->policy_num, 'client_id' => $ins_data[0]->client_id));
                $matArr = array();
                $countArr = 0;
                foreach($mat_data as $mat)
                {
                    $matArr[$countArr]['srNo'] = $countArr + 1;
                    $matArr[$countArr]['matID'] = $mat->maturity_id;
                    $matArr[$countArr]['matPolicy'] = $mat->policy_num;
                    $matArr[$countArr]['matAmt'] = $mat->amount;
                    $mat_temp = DateTime::createFromFormat('Y-m-d', $mat->maturity_date);
                    $matArr[$countArr]['matDate'] = $mat_temp->format('d/m/Y');
                    $countArr++;
                }

                $this->session->set_userdata('maturity', $matArr);

                // change/add values as required (eg. dates)
                if(isset($ins_data[0]->paidup_date)) {
                    $paidUp_temp = DateTime::createFromFormat('Y-m-d', $ins_data[0]->paidup_date);
                    $ins_data[0]->paidup_date = $paidUp_temp->format('d/m/Y');
                }
                if(isset($ins_data[0]->maturity_date)) {
                    $mat_temp = DateTime::createFromFormat('Y-m-d', $ins_data[0]->maturity_date);
                    $ins_data[0]->maturity_date = $mat_temp->format('d/m/Y');
                }
                if(isset($ins_data[0]->commence_date)) {
                    $comm_temp = DateTime::createFromFormat('Y-m-d', $ins_data[0]->commence_date);
                    $ins_data[0]->commence_date = $comm_temp->format('d/m/Y');
                }
                if(isset($ins_data[0]->next_prem_due_date)) {
                    $premDue_temp = DateTime::createFromFormat('Y-m-d', $ins_data[0]->next_prem_due_date);
                    $ins_data[0]->next_prem_due_date = $premDue_temp->format('d/m/Y');
                }
                if(isset($ins_data[0]->grace_due_date)) {
                    $grace_temp = DateTime::createFromFormat('Y-m-d', $ins_data[0]->grace_due_date);
                    $ins_data[0]->grace_due_date = $grace_temp->format('d/m/Y');
                }

                $data['insurance'] = $ins_data;

                $header['title']='Edit Insurance - '.$policyNum;
                $header['css'] = array(
                    'assets/users/plugins/form-select2/select2.css',
                    'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
                );
                $header['js'] = array(
                    'assets/users/plugins/form-parsley/parsley.min.js',
                    'assets/users/demo/demo-formvalidation.js',
                    'assets/users/plugins/form-validation/jquery.validate.min.js',
                    'assets/users/plugins/form-select2/select2.min.js',
                    'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
                    'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
                    'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
                    'assets/users/plugins/bootbox/bootbox.min.js',
                    'assets/users/js/common.js'
                );
                
                $this->load->view('broker/common/header', $header);
                $this->load->view('broker/insurance/ins_edit', $data);
                $this->load->view('broker/master/add_family');
                $this->load->view('broker/master/add_adviser');
                $this->load->view('broker/master/add_premium_type');
                $this->load->view('broker/common/notif');
                $this->load->view('broker/common/footer');
            } else {
                //invalid policy_num, redirect back to Insurances
                
                header('Location: '.base_url('broker/Insurances'));
            }
        } else {
            
            /* has come to Edit page without ID, so redirect to some other page */
            header('Location: '.base_url('broker/Insurances'));
        }
    }

    function del_ins()
    {
        $brokerID = $this->session->userdata('broker_id');
        $policy_num = $this->input->post('policy_num');
        $client_id = $this->input->post('client_id');
        $comp_id = $this->input->post('comp_id');
        $condition = array('policy_num' => $policy_num, 'client_id'=>$client_id, 'broker_id' => $brokerID);
        $where = array(
            'policy_number' => $policy_num,
            'broker_id' => $brokerID
        );
        try
        {
            $status = $this->ins->delete_premium($where);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $status = $this->ins->delete_insurance($condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

            $condition_pol = array('policy_num' => $policy_num, 'client_id' => $client_id, 'ins_comp_id' => $comp_id);
            $status = $this->ins->delete_policy($condition_pol);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

            $success = array(
                'status' => 'success',
                'title'=> 'Insurance `'.$policy_num.'` Deleted!',
                'text' => 'Insurance Details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $success = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($success);
    }

    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown_wise($cli_condtition);
        
        $data['prem_status'] = $this->ins->get_premium_status();
        $data['prem_mode'] = $this->ins->get_premium_modes();
        $data['prem_pay_mode'] = $this->ins->get_premium_pay_modes();
        $data['prem_types'] = $this->prem_type->get_prem_type_broker_dropdown($brokerID);
        $data['ins_comp'] = $this->ins_comp->get_ins_companies_broker_dropdown($brokerID);
        $data['adv'] = $this->ins->get_advisor_broker_dropdown($brokerID);
        return $data;
    }

    //function for insurance policies
    //gets insurance policies of a client
    function get_policies($client = null)
    {
        if($client == null)
            $client = $this->input->post('client_id');
        $result = $this->ins->get_ins_policy_broker_dropdown(array('client_id' => $client));
        echo json_encode($result);
    }

    function get_policy_name()
    {
        $compID = $this->input->post('comp_id');
        $brokerID = $this->session->userdata('broker_id');
        $condition = array('ins_comp_id' => $compID, 'broker_id' => $brokerID);
        $data['ins_plans'] = $this->ins_plans->get_ins_plans_broker_dropdown($condition);
        echo json_encode($data['ins_plans']);
    }

    //save policy in database
    function save_policy()
    {
        $comp_name = $this->input->post('comp_name');
        $pol_name = $this->input->post('policy_name');
        $pol_num = $this->input->post('policy_num');
        $client = $this->input->post('client_id');
        $brokerID = $this->session->userdata('broker_id');

        $data['ins_comp_id'] = $comp_name;
        $data['client_id'] = $client;
        $data['plan_id'] = $pol_name;
        $data['policy_num'] = $pol_num;
        try
        {
            //$duplicateCondition = array("policy_num" => $pol_num, "ins_comp_id" => $comp_name);
            $duplicateCondition = array("policy_num" => trim($pol_num));
            $isDuplicate = $this->ins->check_duplicate("insurance_policies", $duplicateCondition);
            if($isDuplicate) {
                $message = array("status" => 0, 'title' => 'Policy number already exists!', 'text' => 'The Policy Number you entered is already present in our database. Please check whether the Policy Number is correct, as it should be unique.');
                echo json_encode($message);
            } else {
                $status = $this->ins->add_policy($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                } else {
                    $this->get_policies($client);
                }
            }
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
            echo json_encode($message);
        }
    }

    //if policy change clear maturity session and get details of the policy
    function policy_change()
    {
        $policy_num = $this->input->post('pol_num');
        $client_id = $this->input->post('client_id');
        $this->session->unset_userdata('maturity');
        $where = array(
            'insurance_policies.policy_num'=>$policy_num,
            'insurance_policies.client_id'=>$client_id
        );
        $data["pol_details"]=$this->ins->get_policy_details($where);
        echo json_encode($data);
    }

    //function for insurance maturity
    //function to calculate first maturity amount
    private function get_mat_amt($amtIns, $mode, $premAmt, $commenceDate, $paidUpDate, $planName, $maturityDate, $brokerID, $firstMatAmt, $nextPremDueDate, $type = 'single')
    {
        if($mode === "Annually" || $mode === "Single")
            $firstMatAmt = $premAmt;
        else if($mode === "Half-Yearly")
            $firstMatAmt = $premAmt * 2;
        else if($mode === "Quarterly")
            $firstMatAmt = $premAmt * 4;
        else if($mode === "Monthly")
            $firstMatAmt = $premAmt * 12;

        $premPayingTerm = $paidUpDate->diff($commenceDate)->format('%y');
        $premPayingTerm++;
        $benefitTerm = $maturityDate->diff($commenceDate)->format('%y');
        $bonus = 0.00;
        $finalBonus = 0.00;
        $returnCul = 0.00;
        $rateOfInt = 0.00;
        $graceDays = 0;
        $planTypeName = '';

        $data = $this->ins_plans->get_ins_plans_broker(array('plan_name' => $planName, 'ins_plans.broker_id' => $brokerID));
        foreach($data as $item)
        {
            $bonus = $item->annual_cumm / 100;
            $rateOfInt = $item->annual_cumm_one;
            $planTypeName = $item->plan_type;
            $graceDays = $item->grace_period;
            if($item->return_cumm != null)
                $returnCul = $item->return_cumm;
        }
        if($type != 'import')
        {
            $date = DateTime::createFromFormat('d/m/Y', $nextPremDueDate);
            $tempDueDate = $date->format('Y-m-d');
        }
        else
        {
            $tempDueDate = $nextPremDueDate;
        }
        $graceDate = date('d/m/Y', strtotime($tempDueDate.  ' + '.$graceDays.' days'));
        $isUL = false;
        if($planTypeName != null && $planTypeName === "Traditional")
        {
            $finalBonus = ($amtIns * $rateOfInt) / 100;
            $res = ($amtIns * $bonus) * $benefitTerm;
            $firstMatAmt = round(($res + $finalBonus + $amtIns), 2);
        }
        else if($planTypeName != null && $planTypeName === "Unit Linked")
        {
            $result = 0.00;
            $principal_amt = 0.00;
            for ($principal_amt = $firstMatAmt, $i = 1; $i <= $benefitTerm; $i++)
            {
                $result = ($principal_amt * $returnCul) / 100;
                $result += $principal_amt;
                if ($i < $premPayingTerm)
                {
                    $principal_amt = $result + $firstMatAmt;
                }
                else
                {
                    $principal_amt = $result;
                }

            }
            $firstMatAmt = round($result, 2);
            $isUL = true;
        }
        $data = array(
            'firstMatAmt' => $firstMatAmt,
            'nextPremDueDate' => $nextPremDueDate,
            'graceDueDate' => $graceDate,
            'isUL' => $isUL
        );
        return $data;
    }

    function get_first_mat_amt()
    {
        $amtIns = $this->input->post('amt_ins');
        $mode = $this->input->post('mode');
        $premAmt = $this->input->post('amt_prem');
        $commenceDate = $this->input->post('commense_date');
        $paidUpDate = $this->input->post('paidup_date');
        $planName = $this->input->post('plan_name');
        $maturityDate = $this->input->post('maturity_date');
        $brokerID = $this->session->userdata('broker_id');
        $firstMatAmt = 0.00;

        $nextPremDueDate = $commenceDate;
        $date = DateTime::createFromFormat('d/m/Y', $commenceDate);
        $commenceDate = new DateTime($date->format('Y-m-d'));
        $date = DateTime::createFromFormat('d/m/Y', $paidUpDate);
        $paidUpDate = new DateTime($date->format('Y-m-d'));
        $date = DateTime::createFromFormat('d/m/Y', $maturityDate);
        $maturityDate = new DateTime($date->format('Y-m-d'));
        $data = $this->get_mat_amt($amtIns, $mode, $premAmt, $commenceDate, $paidUpDate, $planName, $maturityDate, $brokerID, $firstMatAmt, $nextPremDueDate);
        echo json_encode($data);
    }

    function get_last_premium() {
        $data = $this->input->post();
        $brokerID = $this->session->userdata('broker_id');
        $condition = array('prem.policy_number'=>$data['policy_num'], 'prem.broker_id'=>$brokerID);
        $list = $this->ins->get_premium_details($condition);
        if(isset($list[0]) && !empty($list[0]->next_premium_due_date)) {
            $npd = $list[0]->next_premium_due_date;
            $npdTemp = DateTime::createFromFormat('d/m/Y',$npd);
            $last_premium = (array)$list[0]; //get last premium row in an array
        } else {
            $npdTemp = new DateTime();
            $last_premium = array();
        }


        //get days diff in next_due_date & grace_due_date
        $old_npd = DateTime::createFromFormat('d/m/Y', $data['next_prem_due_date']);
        $old_gdd = DateTime::createFromFormat('d/m/Y', $data['grace_due_date']);
        $diff = $old_npd->diff($old_gdd)->format("%a");

        //check which mode user has selected and get next_premium_due_date
        /*if($data['mode'] == "1") {
            $next_prem_due = $npdTemp->modify('+1 year');
        } elseif($data['mode'] == "2") {
            $next_prem_due = $npdTemp->modify('+6 month');
        } elseif($data['mode'] == "3") {
            $next_prem_due = $npdTemp->modify('+3 month');
        } elseif($data['mode'] == "4") {
            $next_prem_due = $npdTemp->modify('+1 month');
        } else {
            $next_prem_due = $npdTemp;
        }*/
        $next_prem_due = $npdTemp;

        $last_premium['next_premium_due_date'] = $next_prem_due->format('d/m/Y');

        //calculate the new grace_due_date
        $diff_str = '+'.$diff.' day';
        $grace_due = $npdTemp->modify($diff_str);
        $last_premium['grace_due_date'] = $grace_due->format('d/m/Y');

        echo json_encode($last_premium);
    }


    //get maturity details if exists via session
    public function mat_list()
    {
        $output = array();
        $isEdit = $this->input->post('isEdit');
        if(isset($this->session->userdata['maturity']))
        {
            $data1 = $this->session->userdata['maturity'];
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            $data = null;
            foreach($data1 as $item)
            {
                $num++;
                $row = array();
                $row['srNo']=$item['srNo'];
                $row['matID']=$item['matID'];
                $row['matPolicy']=$item['matPolicy'];
                $row['matDate']=$item['matDate'];
                $row['matAmt']=$item['matAmt'];
                //add html for action
                if($isEdit == 'true')
                {
                  $permissions=$this->session->userdata('permissions');
                    if($permissions=="3")
                    {
                    $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_mat('."'".$item['matID']."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                    }
                    else {
                      $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                      <i class="fa fa-trash-o"></i></a>';
                    }
                }
                else
                {
                  $permissions=$this->session->userdata('permissions');
                    if($permissions=="3")
                    {
                    $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_mat('."'".$item['srNo']."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                    }
                    else {
                      $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                      <i class="fa fa-trash-o"></i></a>';
                    }
                }

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "data"=>$data
            );
        }
        //output to json format
        echo json_encode($output);
    }

    //get maturity details from dataBASE
    public function mat_list_db()
    {
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $output = array();
        if(isset($_POST['polNum'])) {
            $policyNum = $this->input->post('polNum');
        }

        if(isset($policyNum)) {
            $mat_data = $this->ins->get_maturity_details(array('policy_num' => $policyNum));
            if(!$mat_data) {
                echo json_encode(array("draw"=>0, "data"=>null));
                die();
            }
            $matArr = array();
            $countArr = 0;
            foreach($mat_data as $mat)
            {
                $matArr[$countArr]['srNo'] = $countArr + 1;
                $matArr[$countArr]['matID'] = $mat->maturity_id;
                $matArr[$countArr]['matPolicy'] = $mat->policy_num;
                $matArr[$countArr]['matAmt'] = $mat->amount;
                $mat_temp = DateTime::createFromFormat('Y-m-d', $mat->maturity_date);
                $matArr[$countArr]['matDate'] = $mat_temp->format('d/m/Y');
                $countArr++;
            }
            //var_dump($matArr);

            $this->session->unset_userdata('maturity');

            $this->session->set_userdata('maturity', $matArr);

            $data1 = $this->session->userdata['maturity'];
            $num = 10;
            if(isset ($_POST['start']))
                $num = $_POST['start'];
            $data = null;
            foreach($data1 as $item)
            {
                $num++;
                $row = array();
                $row['srNo']=$item['srNo'];
                $row['matID']=$item['matID'];
                $row['matPolicy']=$item['matPolicy'];
                $row['matDate']=$item['matDate'];
                $row['matAmt']=$item['matAmt'];
                //add html for action
                $permissions=$this->session->userdata('permissions');
                if($permissions=="3")
                {
                    $row['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_mat('."'".$item['matID']."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else {
                    $row['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                      <i class="fa fa-trash-o"></i></a>';
                }
                //var_dump($data);

                $data[] = $row;
            }
            $output = array(
                "draw"=>1,
                "data"=>$data
            );

            //output to json format
            echo json_encode($output);
        } else {
            //echo json_encode(false);
        }
    }

    //add maturity details in session
    function add_mat()
    {
        $matDate = $this->input->post('matDate');
        $policy = $this->input->post('policy_num');
        $matAmt = $this->input->post('matAmt');
        $matYear = $this->input->post('matYear');
        $isEdit = $this->input->post('isEdit');
        $brokerID = $this->session->userdata('broker_id');
        $matArr = array();
        $message = '';
        $countArr = 0;
        if($this->session->userdata('maturity'))
        {
            $matArr = $this->session->userdata('maturity');
            $countArr = count($matArr);
        }
        try
        {
            for($i=1; $i<=$matYear; $i++)
            {
                $date = DateTime::createFromFormat('d/m/Y', $matDate);
                $tempMatDate = $date->format('Y-m-d');
                $mat_id = 0;
                if($isEdit == 'true')
                {
                    $client_id = $this->input->post('client_id');
                    $client_name = $this->input->post('client_name');
                    $comp_name = $this->input->post('comp_name');
                    $plan_name = $this->input->post('plan_name');
                    $user_id = $this->session->userdata('user_id');
                    $row = array(
                        'policy_num' => $policy,
                        'maturity_date' => $tempMatDate,
                        'amount' => $matAmt,
                        'user_id'=> $user_id,
                        'client_id' => $client_id
                    );

                    $mat_id = $this->ins->add_maturity($row);
                    //if there is any error
                    if(isset($mat_id['code']))
                    {
                        throw new Custom_exception();
                    }
                    $remData = array(
                        'reminder_type'=>'Maturity Reminder',
                        'client_id' => $client_id,
                        'client_name' => $client_name,
                        'broker_id' => $brokerID,
                        'reminder_date' => $tempMatDate,
                        'reminder_message' => $comp_name ." ".$plan_name
                    );
                    //$status = $this->rem->add_reminder($remData);
                    //commented for time-being, maybe its not required - Salmaan 23-08-16
                    if(isset($status['code']))
                    {
                        throw new Custom_exception();
                    }

                }
                $matArr[$countArr]['srNo'] = $countArr;
                $matArr[$countArr]['matID'] = $mat_id;
                $matArr[$countArr]['matPolicy'] = $policy;
                $matArr[$countArr]['matAmt'] = $matAmt;
                $matArr[$countArr]['matDate'] = $matDate;
                $matDate = date('d/m/Y', strtotime($tempMatDate.  ' + 1 year'));
                $countArr++;
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Maturity Added!',
                'text' => 'Maturity Details added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($mat_id['code']), 'error'=>$e);
        }
        $this->session->set_userdata('maturity', $matArr);
        echo json_encode($message);
    }

    //get details of selected maturity from session and display on popup
    function edit_mat()
    {
        $id = $this->input->post('id');
        if($this->session->userdata('maturity'))
            $matArr = $this->session->userdata('maturity');
        $key = array_search($id, array_column($matArr, 'srNo'));
        $data = $matArr[$key];
        echo json_encode($data);
    }

    //edit details of maturity in session
    function update_mat()
    {
        $matSrNo = $this->input->post('srNoEdit');
        $matDate = $this->input->post('matDateEdit');
        $matAmt = $this->input->post('matAmtEdit');

        if($this->session->userdata('maturity'))
            $matArr = $this->session->userdata('maturity');

        foreach ($matArr as &$element) {
            if ($element['srNo'] == $matSrNo) {
                // update some data
                $element['matDate'] = $matDate;
                $element['matAmt'] = $matAmt;
                break;
            }
        }

        unset($_SESSION['maturity']);
        $this->session->set_userdata('maturity', $matArr);
        echo json_encode(array('status'=>TRUE));
    }

    //delete maturity from session
    function delete_mat()
    {
        $id = $this->input->post('id');
        $isEdit = $this->input->post('isEdit');
        if($this->session->userdata('maturity'))
            $matArr = $this->session->userdata('maturity');
        try
        {
            if($isEdit == 'true')
            {
                $key = array_search($id, array_column($matArr, 'matID'));
                $row = array(
                    'maturity_id' => $id
                );

                $status = $this->ins->delete_maturity($row);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
            }
            else
                $key = array_search($id, array_column($matArr, 'srNo'));
            unset($matArr[$key]);

            $countArr = 0;
            $tempArr = null;
            if(count($matArr) > 0)
            {
                foreach($matArr as $item)
                {
                    $tempArr[$countArr]['srNo'] = $countArr + 1;
                    $tempArr[$countArr]['matID'] = $item['matID'];
                    $tempArr[$countArr]['matPolicy'] = $item['matPolicy'];
                    $tempArr[$countArr]['matAmt'] = $item['matAmt'];
                    $tempArr[$countArr]['matDate'] = $item['matDate'];
                    $countArr++;
                }
            }
            unset($_SESSION['maturity']);
            $this->session->set_userdata('maturity', $tempArr);
            $message = array(
                'status' => 'success',
                'title' => 'Maturity Deleted',
                'text' => 'Maturity details deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //function for premium details
    //get premium details from datatable
    function premium_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $clientID = $this->input->post('prem_client_id');
        $policyNumber = $this->input->post('prem_policy_num');
        $condition = array(
            'fam.broker_id' => $brokerID,
            'prem.client_id' => $clientID,
            'policy_number' => $policyNumber
        );
        $list = $this->ins->get_premium_details($condition);

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $ins)
        {
            $row = array();
            $num++;
            $row['premium_id'] = $ins->premium_id;
            $row['bank_name'] = $ins->bank_name;
            $row['branch'] = $ins->branch;
            $row['account_number'] = $ins->account_number;
            $row['cheque_number'] = $ins->cheque_number;
            $row['cheque_date'] = $ins->cheque_date;
            $row['premium_amount'] = $ins->premium_amount;
            $row['adjustment'] = $ins->adjustment;
            $row['adjustment_ref_number'] = $ins->adjustment_ref_number;
            $row['advisers'] = $ins->advisers;
            $row['narration'] = $ins->narration;
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_premium('."'".$ins->premium_id."'".')">
                <i class="fa fa-pencil"></i></a>';
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    function get_premium()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data['premium'] = $this->ins->get_premium_details(array('premium_id' => $this->input->post("id"), 'prem.broker_id' => $brokerID));
        $clientID = $data['premium'][0]->client_id;
        $bankID = $data['premium'][0]->bank_id;
        $branch = $data['premium'][0]->branch;
        $data['banks'] = $this->bank->get_banks_of_client(array("client_id" => $clientID));
        $data['branch'] = $this->bank->get_branches(array("bank_id" => $bankID, "client_id" => $clientID));
        $data['account'] = $this->bank->get_accounts(array('client_id' => $clientID, 'bank_id' => $bankID, 'branch' => $branch));
        echo json_encode($data);
    }

    function update_premium()
    {
        $postData = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $postData['adjustment'] = $postData['premAdjust'];
        $postData['user_id'] = $this->session->userdata('user_id');
        $postData['client_id'] = $postData['premClientID'];
        $date = DateTime::createFromFormat('d/m/Y', $postData['cheque_date']);
        $postData['cheque_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $postData['next_premium_due_date']);
        $postData['next_premium_due_date'] = $date->format('Y-m-d');
        $postData['branch'] = $postData['premBranch'];
        $postData['broker_id'] = $brokerID;
        $premium_id = $postData['premium_id'];
        unset($postData['premAdjust'], $postData['premClientID'], $postData['id'],
        $postData['premBranch'], $postData['amount_insured'], $postData['premium_id']);

        $condition = array('premium_id' => $premium_id, 'broker_id' => $brokerID);
        try
        {
            $insertId = $this->ins->update_premium($postData, $condition);
            //if there is any error
            if(isset($insertId['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status'=> 'success',
                'title'=> 'Premium Updated!',
                'text' => 'Premium Details updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($insertId['code']));
        }
        echo json_encode($message);
    }

    //if premium amount or premium mode has changed while Editing, we need to change premium_paying_details
    function update_prem_paying_details()
    {
        $postData = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $data['policyNumber'] = $postData['policy_num'];
        $comDate = DateTime::createFromFormat('d/m/Y', $postData['commence_date']);
        $data['comDate'] = $comDate->format('Y-m-d');
        $paidUpDate = DateTime::createFromFormat('d/m/Y', $postData['paidup_date']);
        $data['paidUpDate'] = $paidUpDate->format('Y-m-d');
        $fupDate = DateTime::createFromFormat('d/m/Y', $postData['next_prem_due_date']);
        $data['fupDate'] = $fupDate->format('Y-m-d');
        $data['premiumAmount'] = $postData['prem_amt'];
        $data['premiumPayingMode'] = $postData['mode'];
        $data['brokerID'] = $brokerID;

        unset($postData);

        try
        {
            $updated = $this->ins->update_prem_paying($data);
            //if there is any error
            if(isset($updated['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status'=> 'success',
                'title'=> 'Premium Paying Details Updated!',
                'text' => 'Premium Paying Details for Policy No. '.$data["policyNumber"].' updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($updated['code']));
        }
        echo json_encode($message);
    }

    function check_insurance_status()
    {
        $policyNumber = $this->input->post('prem_policy_num');
        $clientID = $this->input->post('prem_client_id');
        $brokerID = $this->session->userdata('broker_id');
        $where = array(
            'insurances.policy_num'=>$policyNumber,
            'insurances.client_id'=>$clientID,
            'insurances.broker_id'=>$brokerID
        );
        $policy = $this->ins->get_insurance_policy_details($where);
        if(count($policy) <= 0)
        {
            $data = array('status'=>'FALSE', 'message'=>'Policy does not exists. Please first save and then add premium.');
        }
        else
        {
            $data = array('status'=>'TRUE');
            $data['banks'] = $this->bank->get_banks_of_client(array("client_id" => $clientID));
        }
        echo json_encode($data);
    }

    function save_premium()
    {
        $postData = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $postData['adjustment'] = $postData['premAdjust'];
        $postData['user_id'] = $this->session->userdata('user_id');
        $postData['client_id'] = $postData['premClientID'];
        $date = DateTime::createFromFormat('d/m/Y', $postData['cheque_date']);
        $postData['cheque_date'] = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $postData['next_premium_due_date']);
        $postData['next_premium_due_date'] = $date->format('Y-m-d');
        $postData['broker_id'] = $brokerID;
        if(isset($postData['premBranch'])) {
            $postData['branch'] = $postData['premBranch'];
        }
        unset($postData['premAdjust'], $postData['premClientID'], $postData['id'],
        $postData['premBranch'], $postData['amount_insured'], $postData['premium_id']);
        try
        {
            $insertId = $this->ins->add_premium($postData);
            //if there is any error
            if(isset($insertId['code']))
            {
                throw new Custom_exception();
            }
            $success = array(
                'status'=> 'success',
                'title'=> 'New Premium Added!',
                'text' => 'Premium Details added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($insertId['code']));
        }
        echo json_encode($success);
    }

    function delete_prem()
    {
        $brokerID = $this->session->userdata('broker_id');
        $clientID = $this->input->post('prem_client_id');
        $policyNumber = $this->input->post('prem_policy_num');
        $condition = array(
            'fam.broker_id' => $brokerID,
            'prem.client_id' => $clientID,
            'policy_number' => $policyNumber
        );
        $premium_id = $this->ins->get_last_premium_id($condition);
        $where = array(
            'premium_id' => $premium_id[0]->premium_id,
            'broker_id' => $brokerID
        );
        try
        {
            $status = $this->ins->delete_premium($where);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $success = array(
                'status' => 'success',
                'title'=> 'Premium Deleted!',
                'text' => 'Premium Detail deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $success = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($success);
    }

    //temp function
    function generate_premium()
    {
        $header['title'] = 'Insurance Premium';
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/generatePremium');
        $this->load->view('broker/common/footer');
    }
    function temp_premium_add()
    {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('s_date'));
        $sDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('e_date'));
        $eDate = $date->format('Y-m-d');
        $brokerID = $this->session->userdata('broker_id');
        $condition = array(
            'p1' => $this->input->post('pol_num'),
            'p2' => $this->input->post('pAmt'),
            'p3' => $sDate,
            'p4' => $eDate,
            'p5' => $this->input->post('pMode'),
            'brokerID' => $brokerID
        );
        $query = $this->ins->temp_generate_premium($condition);
        echo json_encode(array('status' => true));
    }

    ///functions for fund options
    function ins_fund_import($fund_data = null)
    {
        $header['title'] = 'Insurance Fund Options';
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
        $data['fund_data'] = $fund_data;
        $this->load->view('broker/insurance/ins_fund_option', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function import_fund()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database. Cause no policy number exists in insurance
        $fund_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_fund"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_fund"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_fund"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

                    //temp variables to hold values
                    $policy_num = "";
                    $fund_option = "";
                    $fund_value = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                        $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                        $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'POLICY NUMBER' || strtoupper($data_value) == 'FUND OPTION' || strtoupper($data_value) == 'VALUE')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            }
                        }
                        else if($countExcel < 3)
                        {
                            echo $countExcel;
                            $message = 'Column Specified in Excel is not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'POLICY NUMBER')
                                    $policy_num = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'FUND OPTION')
                                    $fund_option = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'VALUE')
                                    $fund_value = $data_value;

                                        
                                $where = array(
                                    'insurances.policy_num'=>$policy_num,
                                    'families.broker_id'=>$brokerID
                                );
                                
                                if($countCol == 3)
                                {
                                    
                                    $policy = $this->ins->get_insurance_policy_details($where);
                                    
                                    if(count($policy) == 0)
                                    {
                                        $policy_num=+$policy_num;
                                        $where = array(
                                            'insurances.policy_num'=>+$policy_num,
                                            'families.broker_id'=>$brokerID
                                        );
                                        $policy = $this->ins->get_insurance_policy_details($where);    
                                        if(count($policy) == 0)     
                                        {    
                                            $fund_data[$row][1] = $policy_num;
                                            $fund_data[$row][2] = $fund_option;
                                            $fund_data[$row][3] = $fund_value;
                                            $countCol = 0;
                                            $uploadedStatus = 2;
                                            continue;
                                            
                                        }
                                    }
                                    
                                    $where = array(
                                        'policy_number'=>$policy_num,
                                       // 'fund_option'=>$fund_option,
                                        'broker_id'=>$brokerID
                                    );
                                    $this->ins->delete_fund_value_data($where);
                                    
                                    if(substr($policy_num,0,1)=="0")
                                    {
                                        $where = array(
                                            'policy_number'=>+$policy_num,
                                            'fund_option'=>$fund_option,
                                            'broker_id'=>$brokerID
                                        );
                                        $this->ins->delete_fund_value_data($where);
                                    }
                                    $data[] = array(
                                        'policy_number' => $policy_num,
                                        'fund_option' => $fund_option,
                                        'value' => $fund_value,
                                        'broker_id' => $brokerID
                                    );
                                    
                                    $countCol = 0;
                                  
                                }
                            }
                        }
                    }
                      
                    
                    $this->ins->add_fund_value_import($data);
                    $this->common->last_import('Insurance Fund Options', $brokerID, $_FILES["import_fund"]["name"], $user_id);
                    $uploadedStatus = 1;
                    $message = "Fund Uploaded Successfully";
                }
            }
            else
            {
                $message = "No file selected";
            }
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
        $this->ins_fund_import($fund_data);
    }

    //function to display fund options details in fund option tab of ins details
    function get_fund_options()
    {
        $brokerID = $this->session->userdata('broker_id');
        $policyNumber = $this->input->post('prem_policy_num');
        $condition = array(
            'policy_num' => $policyNumber,
            'brokerID' => $brokerID
        );
        
        $list = $this->ins->get_fund_option_details($condition);

        //output to json format
        echo json_encode($list);
    }

    function get_import_values()
    {
        $brokerID = $this->session->userdata('broker_id');
        $policyNumber = $this->input->post('policy_num');
        $condition = array(
            'policy_num' => $policyNumber,
            'brokerID' => $brokerID
        );
        $value['fund'] = $this->ins->get_total_fund_value($condition);
        $value['stake'] = $this->ins->get_total_stake_value($condition);

        //output to json format
        echo json_encode($value);
    }

    //function to get system fund value - Salmaan 10/08/16
    function get_fund_value() {
        $brokerID = $this->session->userdata('broker_id');
        $policyNumber = $this->input->post('policy_num');
        $condition = array(
            'policy_num' => $policyNumber,
            'broker_id' => $brokerID
        );
        $result = $this->ins->get_system_fund_value($condition);
        if($result) {
            $fund_value = $result[0]->fund_value;
            echo json_encode($fund_value);
        } else {
            echo json_encode(false);
        }

    }

    ///functions for Real Stakes
    function ins_stake_import($stake_data = null)
    {
        $header['title'] = 'Insurance Real Stake';
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
        $data['stake_data'] = $stake_data;
        $this->load->view('broker/insurance/ins_real_stake', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function real_stake()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $uploadedStatus = 0;
        $message = "";
        //Array for Excel Header/Columns
        $excelHeader = array();
        //Excel data which is not imported in database. Cause no policy number exists in insurance
        $stake_data = array();
        //Count for number of columns entered
        $countExcel = 0;
        $countCol = 0;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_stake"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_stake"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_stake"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

                    //temp variables to hold values
                    $policy_num = "";
                    $year = "";
                    $bonus = "";
                    $amount = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //extract to a PHP readable array format
                    foreach ($cell_collection as $cell)
                    {
                        $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                        $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                        $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                        //header will/should be in row 1 only. of course this can be modified to suit your need.
                        if ($row == 1)
                        {
                            if(strtoupper($data_value) == 'POLICY NUMBER' || strtoupper($data_value) == 'YEAR' || strtoupper($data_value) == 'BONUS' || strtoupper($data_value) == 'AMOUNT')
                            {
                                $excelHeader[$row][$column] = $data_value;
                                $countExcel++;
                            }
                        }
                        else if($countExcel < 4)
                        {
                            $message = 'Column Specified in Excel is not in correct format';
                            $uploadedStatus = 0;
                            break;
                        }
                        else
                        {
                            if(isset($excelHeader[1][$column]))
                            {
                                $countCol++;
                                if(strtoupper($excelHeader[1][$column]) == 'POLICY NUMBER')
                                    $policy_num = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'YEAR')
                                    $year = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'BONUS')
                                    $bonus = $data_value;
                                else if(strtoupper($excelHeader[1][$column]) == 'AMOUNT')
                                    $amount = $data_value;

                                $where = array(
                                    'insurances.policy_num'=>$policy_num,
                                    'families.broker_id'=>$brokerID
                                );
                                if($countCol == 4)
                                {
                                    $policy = $this->ins->get_insurance_policy_details($where);
                                    if(count($policy) == 0)
                                    {
                                        $stake_data[$row][1] = $policy_num;
                                        $stake_data[$row][2] = $year;
                                        $stake_data[$row][3] = $bonus;
                                        $stake_data[$row][4] = $amount;
                                        $countCol = 0;
                                        $uploadedStatus = 2;
                                        continue;
                                    }

                                    $where = array(
                                        'policy_number'=>$policy_num,
                                        'stake_year'=>$year,
                                        'broker_id'=>$brokerID
                                    );
                                    $this->ins->delete_stake_value_data($where);
                                    $data = array(
                                        'policy_number' => $policy_num,
                                        'stake_year' => $year,
                                        'bonus' => $bonus,
                                        'amount' => $amount,
                                        'broker_id' => $brokerID
                                    );
                                    $this->ins->add_stake_value($data);
                                    $this->common->last_import('Insurance Real Stake', $brokerID, $_FILES["import_stake"]["name"], $user_id);
                                    $uploadedStatus = 1;
                                    $message = "Real Stake Uploaded Successfully";
                                    $countCol = 0;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }
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
        $this->ins_stake_import($stake_data);
    }

    ///functions for Real Stakes
    function ins_details_import($ins_data = null)
    {
        $header['title'] = 'Insurance Details Import';
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
        $data['import_data'] = $ins_data;
        $this->load->view('broker/insurance/ins_import', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function ins_import()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->session->unset_userdata('success');
        $this->session->unset_userdata('info');
        $this->session->unset_userdata('error');

        $uploadedStatus = 0;
        $message = ""; $insMessage = ""; $insertRow = true;
        //Excel data which is not imported in database. Cause no policy number exists in insurance
        $ins_data = array();
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_ins"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_ins"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_ins"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                    $compName = ""; $policyName = ""; $policyNum = ""; $clientName = ""; $paidUpDate = ""; $amtIns = "";
                    $commenceDate = ""; $fupDate = ""; $panNo = "";
                    $premAmt = ""; $mode = ""; $remarks = ""; $matDate = ""; $adj = ""; $clientId = "";
                    $modeId = ""; $premPayId = ""; $nomineeId = ""; $advId = ""; $planID = ""; $insCompID = "";
                    $planTypeID = ""; $planName = ""; $adj_flag = 0; $premTypeId = ""; $insCompName = "";
                    $tempComDate = ""; $tempComDate2 = ""; $tempPaidDate =""; $tempMatDate = "";
                    $planType = ""; $ppt = 0; $bt = 0;
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    $dataRows = array(); $matRows = array(); $remRows = array(); $dataPrem = array();
                    $countRow = 0; $countErrRow = 0; $countIns = 0; $countMat = 0; $countRem = 0; $countPrem = 0;
                    $new_policy_data = array(); $insertRow = true;

                    //get all status and assign it as per premium dates - Salmaan - 02-05-16
                    $prem_status = $this->ins->get_premium_status();
                    $statuses = array();
                    foreach($prem_status as $stat) {
                        $temp['status_id'] = $stat->status_id;
                        $temp['status'] = $stat->status;
                        $statuses[] = $temp;
                    }

                    foreach($excelData as $rows)
                    {
                        $new_policy = array();
                        $ins_plans = null;
                        $countCell = 0;
                        foreach($rows as $cell)
                        {
                            if($countRow == 0)
                            {
                                if(strtoupper($cell) == 'COMPANY NAME' || strtoupper($cell) == 'PLAN NAME' ||
                                    strtoupper($cell) == 'POLICY NUMBER' || strtoupper($cell) == 'CLIENT NAME' ||
                                    strtoupper($cell) == 'PAN NO' || strtoupper($cell) == 'PREMIUM PAYING TERM' ||
                                    strtoupper($cell) == 'BENEFIT TERM' || strtoupper($cell) == 'SUM ASSURED' ||
                                    strtoupper($cell) == 'COMMENCEMENT DATE' || strtoupper($cell) == 'FIRST UNPAID PREMIUM DATE' ||
                                    strtoupper($cell) == 'PREMIUM AMOUNT' || strtoupper($cell) == 'ASSET ALLOCATION' ||
                                    strtoupper($cell) == 'PAYMENT OPTION' || strtoupper($cell) == 'REMARK' ||
                                    strtoupper($cell) == 'NOMINEE' || strtoupper($cell) == 'ADVISOR' ||
                                    strtoupper($cell) == 'ADJUSTMENT' || strtoupper($cell) == 'MODE')
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    $uploadedStatus = 1;
                                    continue;
                                }
                                else
                                {
                                    $message = 'Column Specified in Excel is not in correct format';
                                    $uploadedStatus = 0;
                                    break;
                                }
                            }
                            else
                            {
                                
                                if($insertRow)
                                {
                                    //below Company Name and Policy Name added by Salmaan
                                    if(strtoupper($dataColumns[$countCell]) == 'COMPANY NAME')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $compName = $cell;
                                            $condition = "ins_comp_name = '$compName' AND (ins_companies.broker_id = '$brokerID' OR ins_companies.broker_id IS NULL)";
                                            //checks if company name exists
                                            $ins_plans = $this->ins_plans->get_ins_plans_broker($condition);
                                            if(count($ins_plans) == 0)
                                            {
                                                $ins_plans = null;
                                                $insertRow = false;
                                                $insMessage = "Company Name ".$compName." doesn't exist. Unable to add or check policy";
                                            }
                                            else
                                            {
                                                //$new_policy['comp_name'] = $ins_plans[0]->ins_companies; //set new_policy company name
                                                $new_policy['ins_comp_id'] = $ins_plans[0]->comp_id; //set new_policy company name
                                            }
                                        } else {
                                            $countCell++;
                                            continue;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PLAN NAME')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $policyName = strtoupper($cell);
                                            if($ins_plans != null) {
                                                $flag = false;
                                                foreach($ins_plans as $plan) {
                                                    if(strtoupper($plan->plan_name) != $policyName)
                                                    {
                                                        continue;
                                                    }
                                                    else
                                                    {
                                                        //$new_policy['plan_name'] = $plan->plan_name; //set new_plan name
                                                        $new_policy['plan_id'] = $plan->plan_id; //set new_plan id
                                                        //set required variables
                                                        $planID = $plan->plan_id;
                                                        $insCompID = $plan->comp_id;
                                                        $planTypeID = $plan->plan_type_id;
                                                        $planName = $plan->plan_name;
                                                        $insCompName = $plan->ins_companies;
                                                        $planType = $plan->plan_type;

                                                        $flag = true;
                                                    }
                                                }
                                                if(!$flag) {
                                                    $ins_plans = null;
                                                    $insertRow = false;
                                                    $insMessage = "Plan Name '".$policyName."' doesn't exist for the Company '".$plan->ins_companies."'";
                                                }
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Invalid Company Name or No Company Name specified for Policy Name ".$policyName;
                                            }
                                        } else {
                                            $countCell++;
                                            continue;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'POLICY NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $policyNum = $cell;
                                            /*$wherePol = array(
                                                'insurance_policies.policy_num'=>$policyNum,
                                                //'ins_plans.broker_id'=>$brokerID
                                            );*/
                                            $wherePol = 'insurance_policies.policy_num = "'.$policyNum.'"';
                                            //checks if policy exists in policy details table
                                            $policy = $this->ins->get_policy_details($wherePol);
                                            if(count($policy) == 0)
                                            {
                                                /*if policy number doesn't exist, try to add a new one before inserting ins data
                                                get required data to add policy from each cell and insert it later - Salmaan*/
                                                $new_policy['policy_num'] = $policyNum;

                                            }
                                            else
                                            {
                                                $new_policy = null; //set new_policy to null if it already exists(required for client_id part)
                                                $whereIns = array(
                                                    'insurances.policy_num'=>$policyNum,
                                                    //'insurances.broker_id'=>$brokerID
                                                );
                                                //checks if policy exists in insurance of this broker
                                                $policyIns = $this->ins->get_insurance_policy_details($whereIns);
                                                if(count($policyIns) > 0)
                                                {
                                                    $insertRow = false;
                                                    $insMessage = $policyNum." Already exists";
                                                }
                                                else
                                                {
                                                    $planID = $policy[0]->plan_id;
                                                    $insCompID = $policy[0]->ins_comp_id;
                                                    $planTypeID = $policy[0]->plan_type_id;
                                                    $planName = $policy[0]->plan_name;
                                                    $insCompName = $policy[0]->ins_comp_name;
                                                    $planType = $policy[0]->plan_type_name;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Policy Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'CLIENT NAME')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $clientName = $cell;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PAN NO')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $panNo = $cell;
                                            $whereIns = array(
                                                'c.pan_no'=>$panNo,
                                                'fam.broker_id'=>$brokerID
                                            );
                                           // print_r($whereIns);die();
                                            $clients = $this->client->get_clients_broker_dropdown($whereIns);
                                           
                                            if(count($clients) == 0)
                                            {
                                              
                                                $insertRow = false;
                                                $insMessage = 'Pan No. '.$panNo." doesn't exist";
                                            }
                                            else
                                            {
                                                $clientId = $clients[0]->client_id;
                                                if($new_policy != null) {
                                                    $new_policy['client_id'] = $clientId;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Pan No. cannot be empty".$countCell;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'MODE')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $mode = $cell;
                                            $where = "mode_name = '$mode'";
                                            $mode_details = $this->ins->get_premium_modes($where);
                                            if(count($mode_details) == 0)
                                            {
                                                $insMessage = 'Mode '.$mode." Doesn't Exists";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $modeId = $mode_details[0]->mode_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Mode cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'ASSET ALLOCATION')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $premType = $cell;
                                            $wherePremType = "prem_type_name = '$premType' AND (broker_id = '$brokerID' OR broker_id IS NULL)";
                                            $prem_details = $this->prem_type->get_prem_type_details($wherePremType);
                                            if(count($prem_details) == 0)
                                            {
                                                $insMessage = 'Asset Allocation '.$premType." Doesn't Exists";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $premTypeId = $prem_details->prem_type_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Asset Allocation cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PAYMENT OPTION')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $premPayMode = $cell;
                                            $wherePremPay = "prem_pay_mode = '$premPayMode'";
                                            $prem_details = $this->ins->get_premium_pay_modes($wherePremPay);
                                            if(count($prem_details) == 0)
                                            {
                                                $insMessage = 'Payment Option '.$premPayMode." Doesn't Exists";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $premPayId = $prem_details[0]->prem_pay_mode_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Payment Option cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'NOMINEE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $nominee = $cell;
                                            $whereIns = array(
                                                'c.name'=>$nominee,
                                                'fam.broker_id'=>$brokerID
                                            );
                                            $nominee_details = $this->client->get_clients_broker_dropdown($whereIns);
                                            if(count($nominee_details) == 0)
                                            {
                                                //$insMessage = 'Nominee '.$nominee." Doesn't Exists";
                                                //$insertRow = false;
                                                $nomineeId = $clientId;
                                            }
                                            else
                                            {
                                                $nomineeId = $nominee_details[0]->client_id;
                                            }

                                        }
                                        else
                                        {
                                            $nomineeId = $clientId;
                                            //$insertRow = false;
                                            //$insMessage = "Nominee cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'ADVISOR')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $adviser = $cell;
                                            $where = "adviser_name = '$adviser' AND (broker_id = '$brokerID' OR broker_id IS NULL)";
                                            $adv_details = $this->adv->get_adviser_details($where);
                                            if(count($adv_details) == 0)
                                            {
                                                $insMessage = 'Advisor '.$adviser." Doesn't Exists";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $advId = $adv_details[0]->adviser_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Advisor cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'COMMENCEMENT DATE')
                                    {
                                        //echo $cell,'<br/>';
                                        if($cell || $cell != '')
                                        {
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            /*try {
                                                $tempComDate = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $tempComDate1 = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $tempComDate2 = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $commenceDate = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell));
                                            } catch(Exception $e) {
                                                $insertRow = false;
                                                $insMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                            }*/
                                            //echo $date,'<br/><br/>';
                                            /*$InvDate= $cell->getValue();
                                            if(PHPExcel_Shared_Date::isDateTime($cell)) {
                                                $date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($InvDate));
                                            }*/
                                            if(is_object($date)) {
                                                $tempComDate = new DateTime($date->format('Y-m-d'));
                                                $tempComDate1 = new DateTime($date->format('Y-m-d'));
                                                $tempComDate2 = new DateTime($date->format('Y-m-d'));
                                            } else {
                                                $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                if(is_object($date)) {
                                                    $tempComDate = new DateTime($date->format('Y-m-d'));
                                                    $tempComDate1 = new DateTime($date->format('Y-m-d'));
                                                    $tempComDate2 = new DateTime($date->format('Y-m-d'));
                                                } else {
                                                    $insertRow = false;
                                                    $insMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                }
                                            }

                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $commenceDate = $date->format('Y-m-d');
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Commencement Date cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PREMIUM PAYING TERM')
                                    {
                                        if(($cell  || $cell != '') && is_numeric($cell))
                                        {
                                            /*$date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $paidUpDate = $date->format('Y-m-d');
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Paid up Date format is not proper (should be dd/mm/yyyy)";
                                            }
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $tempPaidDate = new DateTime($date->format('Y-m-d'));
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Paid up Date format is not proper (should be dd/mm/yyyy)";
                                            }*/
                                            $ppt = trim($cell);
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Premium Paying Term cannot be empty AND should be a number";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'BENEFIT TERM')
                                    {
                                        if(($cell  || $cell != '') && is_numeric($cell))
                                        {
                                            /*$date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $tempMatDate = new DateTime($date->format('Y-m-d'));
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Maturity Date format is not proper (should be dd/mm/yyyy)";
                                            }
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $matDate = $date->format('Y-m-d');
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "Maturity Date format is not proper (should be dd/mm/yyyy)";
                                            }*/
                                            $bt = trim($cell);
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Benefit Term cannot be empty AND should be a number";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'FIRST UNPAID PREMIUM DATE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $tempFupDate = new DateTime($date->format('Y-m-d'));
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "First Unpaid Premium Date format is not proper";
                                            }
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $fupDate = $date->format('Y-m-d');
                                            } else {
                                                $insertRow = false;
                                                $insMessage = "First Unpaid Premium Date format is not proper";
                                            }
                                            /*try {
                                                $date = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $tempFupDate = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $fupDate = $date->format('Y-m-d');
                                            } catch(Exception $e) {
                                                $insertRow = false;
                                                $insMessage = "First Unpaid Premium Date format is not proper";
                                            }*/
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "First Unpaid Premium Date cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'ADJUSTMENT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $adj = $cell;
                                            $adj_flag = 1;
                                        }
                                        else
                                        {
                                            $adj = "";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'SUM ASSURED')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $amtIns = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Sum Assured cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PREMIUM AMOUNT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $premAmt = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Premium Amount cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'REMARK')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $remarks = $cell;
                                        }
                                        else
                                        {
                                            $remarks = "";
                                        }
                                    }
                                } else {
                                    if(strtoupper($dataColumns[$countCell]) == 'POLICY NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $policyNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Policy Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'CLIENT NAME')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $clientName = $cell;
                                        }
                                    } else if(strtoupper($dataColumns[$countCell]) == 'PAN NO')
                                    {
                                        if($cell  || $cell != '')
                                        {
                                            $panNo = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $insMessage = "Pan No. cannot be empty";
                                        }
                                    }
                                }
                                
                                $countCell++;
                            }
                        }
                        if($countRow != 0)
                        {
                            if(!$insertRow)
                            {
                                $ins_data[$countErrRow][1] = $policyNum;
                                $ins_data[$countErrRow][2] = $clientName;
                                $ins_data[$countErrRow][3] = $panNo;
                                $ins_data[$countErrRow][4] = $insMessage;
                                $countErrRow++;
                                $countCol = 0;
                                $insertRow = true;
                                $uploadedStatus = 2;
                                continue;
                            }

                            //new policy data - Salmaan - 28-04-16
                            if($new_policy != null) {
                                $new_policy_data[] = $new_policy;
                            }

                            //get paidup & maturity dates from ppt and bt
                            $tempPaidDate = $tempComDate1->modify('+'.(int)$ppt.' year');
                            if($mode === "Annually" || $mode === "Single")
                                $tempPaidDate2 = $tempPaidDate->modify('-1 year');
                            else if($mode === "Half-Yearly")
                                $tempPaidDate2 = $tempPaidDate->modify('-6 month');
                            else if($mode === "Quarterly")
                                $tempPaidDate2 = $tempPaidDate->modify('-3 month');
                            else if($mode === "Monthly")
                                $tempPaidDate2 = $tempPaidDate->modify('-1 month');
                            else
                                $tempPaidDate2 = $tempPaidDate;

                            $paidUpDate = $tempPaidDate2->format('Y-m-d');
                            $tempMatDate = $tempComDate2->modify('+'.(int)$bt.' year');
                            $matDate = $tempMatDate->format('Y-m-d');

                            $firstMatAmt = 0.00;
                            $dataMatAmt = $this->get_mat_amt($amtIns, $mode, $premAmt, $tempComDate, $tempPaidDate, $planName, $tempMatDate, $brokerID, $firstMatAmt, $commenceDate, 'import');
                            $date = DateTime::createFromFormat('d/m/Y', $dataMatAmt['graceDueDate']);
                            $graceDueDate = $date->format('Y-m-d');
                            $graceDueDateTemp = strtotime($graceDueDate);
                            
                            $dataRows[$countIns] = array(
                                'policy_num' => $policyNum, 'client_id' => $clientId,  'plan_id' => $planID, 'ins_comp_id' => $insCompID,
                                'plan_type_id' => $planTypeID, 'paidup_date' => $paidUpDate, 'maturity_date' => $matDate,
                                'amt_insured' => $amtIns, 'commence_date' => $commenceDate, 'mode' => $modeId, 'prem_amt' => $premAmt,
                                'prem_type' => $premTypeId,
                                'prem_pay_mode_id' => $premPayId, 'next_prem_due_date' => $dataMatAmt['nextPremDueDate'],
                                'grace_due_date' => $graceDueDate, 'status' => 1, 'remarks' => $remarks,
                                'mat_type' => 'Single', 'adv_id' => $advId, 'nominee' => $nomineeId, 'adjustment_flag' => $adj_flag,
                                'adjustment' => $adj, 'user_id' => $user_id, 'broker_id' => $brokerID
                            );
                            
                            $dataPrem[$countPrem] = array(
                                'policyNum' => $policyNum,
                                'premAmt' => $premAmt,
                                'startDate' => $commenceDate,
                                'endDate' => $fupDate,
                                'premMode' => $mode,
                                'brokerID' => $brokerID
                            );
                            $countIns++;
                            $countPrem++;
                            if(strtoupper($planType) != "GENERAL INSURANCE")
                            {
                                $matRows[$countMat] = array(
                                    'policy_num' => $policyNum,
                                    'maturity_date' => $matDate,
                                    'amount' => $dataMatAmt['firstMatAmt'],
                                    'user_id'=> $user_id,
                                    'client_id' => $clientId
                                );
                            } else {
                                $matRows[$countMat] = array(
                                    'policy_num' => $policyNum,
                                    'maturity_date' => $matDate,
                                    'amount' => 0,
                                    'user_id'=> $user_id,
                                    'client_id' => $clientId
                                );
                            }
                            $countMat++;
                            $remRows[$countRem] = array(
                                'reminder_type'=>'Maturity Reminder',
                                'client_id' => $clientId,
                                'client_name' => $clientName,
                                'reminder_date' => $matDate,
                                'reminder_message' => $insCompName ." ".$planType
                            );
                            $countRem++;

                            /*var_dump($dataPrem);
                            var_dump($dataRows);
                            var_dump($matRows);
                            var_dump($remRows);*/

                            $policyNum = ""; $clientName = ""; $paidUpDate = ""; $amtIns = ""; $commenceDate = "";
                            $fupDate = ""; $premAmt = ""; $mode = ""; $remarks = ""; $matDate = ""; $adj = ""; $clientId = "";
                            $modeId = ""; $premPayId = ""; $nomineeId = ""; $advId = ""; $planID = ""; $insCompID = "";
                            $planTypeID = ""; $planName = ""; $adj_flag = 0; $premTypeId = ""; $insCompName = "";
                            $planType = ""; $key = ""; $status = ""; $bt = 0; $ppt = 0;
                        }
                        if($uploadedStatus == 0)
                            break;
                        $countRow++;
                    }
                    if($dataRows)
                    {
                        //bulk insert new policies - Salmaan - 28-04-16
                        if(count($new_policy_data) > 0) {
                            $inserted = $this->ins->addImportPolicy($new_policy_data);
                            if($inserted) {
                                $this->ins->addImportIns($dataRows, $matRows, $remRows, $dataPrem);
                                $this->common->last_import('Insurance Details', $brokerID, $_FILES["import_ins"]["name"], $user_id);
                                if($uploadedStatus != 2)
                                    $uploadedStatus = 1;
                                $message = "Insurance Details Uploaded Successfully".$new_policy_data;
                            } else {
                                $uploadedStatus = 0;
                                $message = "Could not add new policy data, hence unable to import Insurances";
                            }
                        } else {
                            
                            $this->ins->addImportIns($dataRows, $matRows, $remRows, $dataPrem);
                            $this->common->last_import('Insurance Details', $brokerID, $_FILES["import_ins"]["name"], $user_id);
                            if($uploadedStatus != 2)
                                $uploadedStatus = 1;
                            $message = "Insurance Details Uploaded Successfully";
                        }
                    }
                    unset($dataColumns, $dataRows, $matRows, $remRows, $new_policy_data);
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
        $this->ins_details_import($ins_data);
    }

    ////Insurance Reports
    function ins_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Insurance Policy Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/insurance/ins_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_ins_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $ins_type_value = $this->input->post('ins_type_value');
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
        $ins_rep = $this->ins->get_insurance_report($type, $where);
        $gen_ins_rep = $this->ins->get_general_insurance_report($type, $where);
        if(!empty($ins_rep) || !empty($gen_ins_rep))
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
            $rep_info = array('logo' => $logo, 'ins_type'=>$ins_type_value, 'report_type' => $type);
            $ins_rep_array = array('ins_rep_data' => $ins_rep, 'gen_ins_data' => $gen_ins_rep, 'report_info'=>$rep_info);
            $this->session->set_userdata('ins_report', $ins_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }

    function premium_calendar_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Premium Calendar Report';
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
        $this->load->view('broker/insurance/premium_calendar_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_prem_cal_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $prem_date = $this->input->post('prem_date');
        //$prem_date = date('d').'/'.$prem_date;
        $prem_date = '01'.'/'.$prem_date;
        $prem_date_temp = DateTime::createFromFormat('d/m/Y', $prem_date);
        $rep_date_temp = $prem_date_temp;
        $prem_date = $prem_date_temp->format('Y-m-d');
        $prem_date_temp = $prem_date_temp->format('M');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'clientID'=> $client_id
            );
            $whereLapse = array('clientID' => $client_id);
        }
        else
        {
            $type = 'family';
            $where = array(
                'month'=> $prem_date_temp,
                'start_date'=> $prem_date,
                'familyID'=> $family_id
            );
            $whereLapse = array('familyID' => $family_id);
        }
        $logo = "";
        $status = false;
        $prem_rep = $this->ins->get_premium_calendar_report($type, $where);
        $lapse_rep = $this->ins->get_lapse_policy_report($type, $whereLapse);
        if(!empty($prem_rep) || !empty($lapse_rep))
        {
            unset($_SESSION['prem_report']);
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

            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $prem_rep_array = array('prem_rep_data' => $prem_rep, 'report_info'=>$rep_info, 'lapse_rep_data' => $lapse_rep,
                                    'rep_date_start'=>$rep_date, 'rep_date_end'=>$rep_date_end);
            $this->session->set_userdata('prem_report', $prem_rep_array);
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
                .amount { text-align:left; padding:10px; text-indent: 3px; }
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
            $pdf->SetTitle($reportName);
            $pdf->SetSubject($reportName);
            $pdf->SetKeywords('insurance, premium, report');

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
            $pdf->SetFont('sourcesanspro', '', 9);

            $pdf->AddPage();

            // output the HTML content
            $pdf->writeHTML($css_data.$title_data.$eq_data, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();
ob_end_clean();
            //Close and output PDF document
            $pdf->Output($reportName.'.pdf', 'D');
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
            header('Content-Disposition: attachment;filename=Insurance Portfolio.xlsx'); // specify the download file name
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
    
    ///functions for Real Stakes
    function scheme_import()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->session->unset_userdata('success');
        $this->session->unset_userdata('info');
        $this->session->unset_userdata('error');

        $data;
        
        $data['scheme_data'] = null;
        
        $uploadedStatus = 0;
        $message = ""; 
        $insMessage = ""; 
        $insertRow = true;
        
        $blnError = false;
        //Excel data which is not imported in database. Cause no policy number exists in insurance
        $scheme_data = array();
        //variable to delete previous policy records for fund options
        
        //column name and whether its required or not
        $arrCellLabels = array('Scheme Name'=>'R','Scheme Type'=>'R','Product Code'=>'R','Other'=>'N','Other2'=>'N','ISIN'=>'R','market cap'=>'R');
        //echo '<pre>';print_r($arrCellLabels);die;
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_scheme"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_scheme"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_scheme"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                    
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    
                    $dataRows = array(); $matRows = array(); $remRows = array();
                    $countRow = 0; $countErrRow = 0; $countIns = 0; $countMat = 0; $countRem = 0;
                    $insertRow = true;

                    $skipProcessing = false;
                    
                    foreach($excelData as $rows)
                    {
                        $blnProcessRow = true;
                        $new_policy = array();
                        $ins_plans = null;
                        $countCell = 0;
                        
                        //echo '<pre>';print_r($rows);die;
                        
                        if(!$skipProcessing)
                        {
                        
                            if($countRow == 0)
                            {
                                foreach ($rows as $cell)
                                {
                                    //echo $cell;
                                    if(!in_array($cell,array_keys($arrCellLabels)))
                                    {
                                        $message = 'Column Specified in Excel is not in correct format';
                                        $uploadedStatus = 0;
                                        $skipProcessing = true;
                                        $blnError = true;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                //start importing schemes
                                $arrScheme = array_combine(array_keys($arrCellLabels), $rows);
                                //echo '<pre>';print_r($arrScheme);die;

                                $product_code = $arrScheme['Product Code'];
                                $isin_num = $arrScheme['ISIN'];
                                $scheme_name = $arrScheme['Scheme Name'];
                                $type = $arrScheme['Scheme Type'];
                                $other = $arrScheme['Other'];
                                $other2 = $arrScheme['Other2'];
                                $market_cap = $arrScheme['market cap'];

                                $blnProcessRow = true;

                                foreach($arrScheme as $key => $val)
                                {
                                    //if a field is required and not provided in excel throw error
                                    if($arrCellLabels[$key] == 'R' && (is_null($val) || empty($val) || $val == 'NULL') && $key!='market cap')
                                    {
                                        $scheme_data[$countErrRow][0] = $arrScheme['Scheme Name'];
                                        $scheme_data[$countErrRow][1] = $arrScheme['Product Code'];
                                        $scheme_data[$countErrRow][2] = $arrScheme['ISIN'];
                                        $scheme_data[$countErrRow][3] = $key . ' cannot be empty';
                                        $countErrRow++;
                                        $uploadedStatus = 2;
                                        $blnProcessRow = false;
                                        $blnError = true;
                                        break;
                                    }
                                }

                                if($blnProcessRow)
                                {
                                    if($product_code !='' && $isin_num !='')
                                    {
                                        //first check if scheme exists already by product code
                                         $condition = array(
                                            'prod_code' => $product_code,
                                        );

                                        //echo '<pre>';print_r($this->scheme->getMatchingScheme($condition));die;

                                        $schemes = $this->scheme->getMatchingScheme($condition);

                                        if($schemes)
                                        {
                                            $scheme_data[$countErrRow][0] = $arrScheme['Scheme Name'];
                                            $scheme_data[$countErrRow][1] = $arrScheme['Product Code'];
                                            $scheme_data[$countErrRow][2] = $arrScheme['ISIN'];
                                            //$scheme_data[$countErrRow][3] = 'Scheme already exists in system with Product Code '.$product_code.' and ISIN '.$isin_num;
                                            $scheme_data[$countErrRow][3] = 'Scheme already exists in system with Product Code '.$product_code;
                                            $countErrRow++;
                                            $uploadedStatus = 2;
                                            $blnError = true;
                                        }
                                        else
                                        {
                                            //then check by product_code and ISIN
                                            //first check if scheme exists already by product code
                                            $condition = array(
                                               'prod_code' => $product_code,
                                                'isin'=>$isin_num
                                           );

                                           //echo '<pre>';print_r($this->scheme->getMatchingScheme($condition));die;

                                           $schemes = $this->scheme->getMatchingScheme($condition);

                                           if($schemes)
                                           {
                                               $scheme_data[$countErrRow][0] = $arrScheme['Scheme Name'];
                                               $scheme_data[$countErrRow][1] = $arrScheme['Product Code'];
                                               $scheme_data[$countErrRow][2] = $arrScheme['ISIN'];
                                               $scheme_data[$countErrRow][3] = 'Scheme already exists in system with Product Code '.$product_code.' and ISIN '.$isin_num;
                                               //$scheme_data[$countErrRow][3] = 'Scheme already exists in system with Product Code '.$product_code;
                                               $countErrRow++;
                                               $uploadedStatus = 2;
                                               $blnError = true;
                                           }
                                           else
                                           {
                                               //insert scheme and isin number 
                                                $data = array('scheme_name'=>$scheme_name,'scheme_type_id'=>$type,'prod_code'=>$product_code,'isin'=>$isin_num,'other'=>$other,'other2'=>$other2,'market_cap'=>$market_cap);
                                                $arrReturn = $this->scheme->addScheme($data);
                                                if($arrReturn['result'])
                                                {
                                                    $this->scheme->addSchemeISIN(array('scheme_id'=>$arrReturn['id'],'isin'=>$isin_num));
                                                }
                                                else
                                                {
                                                    $scheme_data[$countErrRow][0] = $arrScheme['Scheme Name'];
                                                    $scheme_data[$countErrRow][1] = $arrScheme['Product Code'];
                                                    $scheme_data[$countErrRow][2] = $arrScheme['ISIN'];
                                                    $scheme_data[$countErrRow][3] = 'Error while inserting scheme into database';
                                                    $countErrRow++;
                                                    $uploadedStatus = 2;
                                                    $blnError = true;
                                                }
                                           }
                                            
                                        }
                                    }
                                }
                            }
                            //die;
                           
                        }
                         $countRow++;
                    }
                    //echo $countRow;die;
                    //echo '<pre>';print_r($scheme_data);die;
                    //var_dump($uploadedStatus);die;
                    if(!$blnError)
                    {
                        $success = array(
                            "title" => "Success!",
                            "text" => 'Scheme Details Uploaded Successfully'
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
                    
                     $this->common->last_import('Scheme Details', $brokerID, $_FILES["import_scheme"]["name"], $user_id);
                }
            }
            
            $data['scheme_data'] = $scheme_data;
        }
            
            
        
        
        $header['title'] = 'Scheme Details Import';
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
        $this->load->view('broker/insurance/scheme_import',$data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    
}
