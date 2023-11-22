<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mutual_funds extends CI_Controller{
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
        if(empty($this->session->userdata['broker_id']) && empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
        //load Fixed_deposit_model, 'mf' is object
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Banks_model', 'bank');
        $this->load->model('Common_model', 'common');
        $this->load->model('Reminders_model', 'rem');
    }

    //Mutual funds list page
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Mutual Funds Master';
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
        $this->load->view('broker/mutual_fund/mf_list');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all mutual fund details from database
    /*public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->mf->get_mutual_funds(array('mf.broker_id' => $brokerID));

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $mf)
        {
            $row = array();
            $num++;
            $row['family_name'] = $mf->family_name;
            $row['client_name'] = $mf->client_name;
            $row['scheme_name'] = $mf->scheme_name;
            $row['scheme_type'] = $mf->scheme_type;
            $row['mutual_fund_type'] = $mf->mutual_fund_type;
            $row['transaction_type'] = $mf->transaction_type;
            $row['folio_number'] = $mf->folio_number;
            $row['purchase_date'] = $mf->purchase_date;
            $row['quantity'] = $mf->quantity;
            $row['nav'] = $mf->nav;
            $row['amount'] = round($mf->amount);
            $row['bal_old'] = round($mf->balance_unit);
            $row['adjustment'] = $mf->adjustment;
            $row['adjustment_ref_number'] = $mf->adjustment_ref_number;
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }*/

    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->ins->get_insurances(array('ins.broker_id' => $brokerID));

        /* get data passed by Datatables and send to Mutual_funds_model */
        $aColumns = array("transaction_id", "fam.name", "c.name", "scheme_name", "mft.scheme_type",
                          "mutual_fund_type", "folio_number", "mf.purchase_date",
                          "quantity", "nav", "mf.amount",
                          //"balance_unit", "adjustment", "adjustment_ref_number", 
                          "orig_trxn_no","orig_trxn_type", "prod_code");
        /*'(select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_option1',
        '(select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 1,1) as fund_option2');*/

        /*
         * Paging
         */
        /*$sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit[] = intval( $_GET['iDisplayStart'] );
            $sLimit[] = intval( $_GET['iDisplayLength'] );
        }*/
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

        //custom conditions - not of datatables
        /*$myWhere = "";
        if($sWhere == "") {
            $sWhere .= " WHERE ";
        } else {
            $sWhere .= " AND ";
        }*/



        $myWhere = "";

        if($_GET['from']!='' || $_GET['to']!='')
        {
            if($_GET['from']!='')
            {
                $from_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["from"]);
                if(is_object($from_date_temp)) {
                    $from_date = $from_date_temp->format('Y-m-d');
                    $myWhere .= "and purchase_date >= '".$from_date."' ";
                } else {
                    $from_date = '';
                }
            }

            if($_GET['to']!='')
            {
                $to_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["to"]);
                if(is_object($to_date_temp)) {
                    $to_date = $to_date_temp->format('Y-m-d');
                    $myWhere .= "and purchase_date <= '".$to_date."'";
                } else {
                    $to_date = '';
                }
            }

            //$list = $this->ins->get_insurances(array('ins.broker_id' => $brokerID));

            /*
            * SQL queries
            * Get data to display
            */
            //var_dump($myWhere); var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->mf->get_mutual_funds_extended("mf.broker_id='".$brokerID."' ".$myWhere, $sWhere, $sOrder, $sLimit );
            //print_r($list);
        }
        else
        {
            //var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->mf->get_mutual_funds_extended("mf.broker_id='".$brokerID."'", $sWhere, $sOrder, $sLimit);
            //print_r($list);
        }
        //print_r($list);
        //var_dump($list);

        /*
         * Output
         */
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list['rResult'] as $mf)
        {
            $row = array();
            $num++;
            $row['family_name'] = $mf->family_name;
            $row['client_name'] = $mf->client_name;
            $row['scheme_name'] = $mf->scheme_name;
            $row['scheme_type'] = $mf->scheme_type;
            $row['mutual_fund_type'] = $mf->mutual_fund_type;
            $row['transaction_type'] = $mf->transaction_type;
            $row['folio_number'] = $mf->folio_number;
            $row['purchase_date'] = $mf->purchase_date;
            $row['quantity'] = $mf->quantity;
            $row['nav'] = $mf->nav;
            $row['amount'] = round($mf->amount);
            //$row['bal_old'] = $mf->balance_unit;
            $row['adjustment'] = $mf->orig_trxn_type;
            $row['prod_code'] = $mf->prod_code;
            $row['adjustment_ref_number'] = $mf->orig_trxn_no;
            $permissions=$this->session->userdata('permissions');
            if($permissions=="3")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
            }
            else if($permissions=="2")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
            }
            else if($permissions=="1")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
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

    //gets all mutual fund valuation records from database
    /*public function ajax_list_valuation()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->mf->get_mutual_funds_valuation(array('v.broker_id' => $brokerID));
		//print_r($list);

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        /*foreach($list as $mf)
        {
            $row = array();
            $num++;
            $row['family_name'] = $mf->family_name;
            $row['client_name'] = $mf->client_name;
            $row['scheme_name'] = $mf->scheme_name;
            $row['mutual_fund_type'] = $mf->mutual_fund_type;
            $row['transaction_type'] = $mf->transaction_type;
            $row['folio_number'] = $mf->folio_number;
            $row['purchase_date'] = $mf->purchase_date;
            $row['quantity'] = $mf->quantity;
            $row['nav'] = $mf->nav;
            $row['amount'] = round($mf->amount);
            $row['adjustment'] = $mf->adjustment;
            $row['adjustment_ref_number'] = $mf->adjustment_ref_number;
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
            $data[] = $row;
        }*/
        /*$output = array(
            "draw"=>1,
            //"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            //"recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$list
        );
        //output to json format
        echo json_encode($output);
    }*/

    public function ajax_list_valuation()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->ins->get_insurances(array('ins.broker_id' => $brokerID));

        /* get data passed by Datatables and send to Mutual_funds_model */
        $aColumns = array("f.name", "c.name", "t.folio_number", "s.scheme_name", "st.scheme_type", "t.purchase_date",
            "t.mutual_fund_type", "t.amount", "v.div_amount", "t.nav", "v.live_unit", "c_nav_date", "v.transaction_day",
            "v.c_nav", "(v.c_nav * v.live_unit)", "v.div_r2", "v.div_payout", "v.mf_cagr", "v.mf_abs","s.prod_code");
        /*'(select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_option1',
        '(select concat(fund_option," - ",ROUND(value)) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 1,1) as fund_option2');*/

        /*
         * Paging
         */
        /*$sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit[] = intval( $_GET['iDisplayStart'] );
            $sLimit[] = intval( $_GET['iDisplayLength'] );
        }*/
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
        $sWhere = "((v.c_nav * v.live_unit) > 3 OR (v.c_nav IS NULL)) ";
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

        //custom conditions - not of datatables
        /*$myWhere = "";
        if($sWhere == "") {
            $sWhere .= " WHERE ";
        } else {
            $sWhere .= " AND ";
        }*/



        $myWhere = "";

        if($_GET['from']!='' || $_GET['to']!='')
        {
            if($_GET['from']!='')
            {
                $from_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["from"]);
                if(is_object($from_date_temp)) {
                    $from_date = $from_date_temp->format('Y-m-d');
                    $myWhere .= "and t.purchase_date >= '".$from_date."' ";
                } else {
                    $from_date = '';
                }
            }

            if($_GET['to']!='')
            {
                $to_date_temp = DateTime::createFromFormat('d/m/Y', $_GET["to"]);
                if(is_object($to_date_temp)) {
                    $to_date = $to_date_temp->format('Y-m-d');
                    $myWhere .= "and t.purchase_date <= '".$to_date."'";
                } else {
                    $to_date = '';
                }
            }
 
            //$list = $this->ins->get_insurances(array('ins.broker_id' => $brokerID));

            /*
            * SQL queries
            * Get data to display
            */
            //var_dump($myWhere); var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->mf->get_mutual_funds_valuation_extended("v.broker_id='".$brokerID."' ".$myWhere, $sWhere, $sOrder, $sLimit );
            //print_r($list);
        }
        else
        {
            //var_dump($sWhere); var_dump($sOrder); var_dump($sLimit);
            $list = $this->mf->get_mutual_funds_valuation_extended("v.broker_id='".$brokerID."'", $sWhere, $sOrder, $sLimit);
            //print_r($list);
        }
        //print_r($list);
        //var_dump($list);

        /*
         * Output
         */
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $list['iTotal'],
            "iTotalDisplayRecords" => $list['iFilteredTotal'],
            "aaData" => $list['rResult']
        );
        //output to json format
        echo json_encode($output);
    }


    //gets all mutual fund purchase from database  --- for dashboard
    public function ajax_list_purchase()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
        //$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type='PIP'");
        $list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type IN('PIP','NFO','IPO','TIN')");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $mf)
        {
            $row = array();
            $num++;
            $row['family_name'] = $mf->family_name;
            $row['client_name'] = $mf->client_name;
            $row['scheme_name'] = $mf->scheme_name;
            $row['mutual_fund_type'] = $mf->mutual_fund_type;
            $row['transaction_type'] = $mf->transaction_type;
            $row['folio_number'] = $mf->folio_number;
            $row['purchase_date'] = $mf->purchase_date;
            $row['quantity'] = $mf->quantity;
            $row['nav'] = $mf->nav;
            $row['amount'] = round($mf->amount);
            $row['adjustment'] = $mf->adjustment;
            $row['adjustment_ref_number'] = $mf->adjustment_ref_number;
            $permissions=$this->session->userdata('permissions');
            if($permissions=="3")
            {
              $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
            }
            else if($permissions="2" || $permissions="1")
            {
              $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
            }


            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //gets all mutual fund redemption from database  --- for dashboard
    public function ajax_list_redemption()
    {
        $brokerID = $this->session->userdata('broker_id');
        //$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
        $list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type='RED'");

        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        $data = array();
        foreach($list as $mf)
        {
            $row = array();
            $num++;
            $row['family_name'] = $mf->family_name;
            $row['client_name'] = $mf->client_name;
            $row['scheme_name'] = $mf->scheme_name;
            $row['mutual_fund_type'] = $mf->mutual_fund_type;
            $row['transaction_type'] = $mf->transaction_type;
            $row['folio_number'] = $mf->folio_number;
            $row['purchase_date'] = $mf->purchase_date;
            $row['quantity'] = $mf->quantity;
            $row['nav'] = $mf->nav;
            $row['amount'] = round($mf->amount);
            $row['adjustment'] = $mf->adjustment;
            $row['adjustment_ref_number'] = $mf->adjustment_ref_number;
            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_mf('."'".$mf->transaction_id."'".')">
                <i class="fa fa-trash-o"></i></a>';

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            "recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //function for mutual fund add form
    function add_form()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Add Mutual Fund';
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
        //get details of mutual fund for the form
        $data = $this->fill_form();
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/mutual_fund/mf_add_form', $data);
        $this->load->view('broker/master/add_family');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function edit_form()
    {
        if(isset($_GET['id']))
        {
            $fd_id = $_GET['id'];
            $broker_id = $this->session->userdata('broker_id');
            $condition = array(
                'transaction_id' => $fd_id,
                'mf.broker_id' => $broker_id
            );
            $data = $this->fill_form();
            //get fixed deposit for the form
            $result_fd = $this->mf->get_mutual_funds($condition);
            $header['title'] = 'Edit Mutual Fund - '.$fd_id;
            if($result_fd)
            {
                $data['mf'] = $result_fd;
                $header['css'] = array(
                    'assets/users/plugins/form-select2/select2.css',
                );
                $header['js'] = array(
                    'assets/users/plugins/form-select2/select2.min.js',
                    'assets/users/plugins/form-parsley/parsley.min.js',
                    'assets/users/plugins/form-validation/jquery.validate.min.js',
                    'assets/users/plugins/bootbox/bootbox.min.js',
                    'assets/users/js/common.js'
                );
                $this->load->view('broker/common/header', $header);
                $this->load->view('broker/mutual_fund/mf_edit_form', $data);
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
    function edit_mf()
    {
        $data = $_POST;
        $userID = $this->session->userdata('user_id');
        $brokerID = $this->session->userdata('broker_id');
        if(!isset($data['adjustment_flag']))
            $mf_data['adjustment_flag'] = 0;
        $transID = $data['transID'];
        $clientID = $data['client_id'];
        unset($data['hbank_id'],$data['hbranch'],$data['haccount_number']);
        if(isset($data['bank_id']) && !empty($data['bank_id'])) {
            $mf_data['bank_id'] = $data['bank_id'];
        } else {
            $mf_data['bank_id'] = null;
        }
        if(isset($data['branch1']) && !empty($data['branch1'])) {
            $mf_data['branch'] = $data['branch1'];
        } else {
            $mf_data['branch'] = null;
        }
        if(isset($data['account_number1']) && !empty($data['account_number1'])) {
            $mf_data['account_number'] = $data['account_number1'];
        } else {
            $mf_data['account_number'] = null;
        }
        if(isset($data['cheque_number1']) && !empty($data['cheque_number1'])) {
            $mf_data['cheque_number'] = $data['cheque_number1'];
        } else {
            $mf_data['cheque_number'] = null;
        }
        $mf_data['adjustment'] = $data['adjustment'];
        $mf_data['adjustment_flag'] = $data['adjustment_flag'];
        $mf_data['adjustment_ref_number'] = $data['adjustment_ref_number'];
        $mf_data['user_id'] = $userID;

        $whereCon = array(
            'transaction_id' => $transID,
            'broker_id' => $brokerID
        );
        try
        {
            $status = $this->mf->update_mutual_fund($mf_data, $whereCon);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Mutual Fund Updated !',
                'text' => 'Mutual Fund Details updated successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    //add mutual fund details to database
    function add_mf()
    {
        $data = $_POST;
        if(!isset($data['adjustment_flag']))
            $data['adjustment_flag'] = 0;
        $data['user_id'] = $this->session->userdata('user_id');
        $data['broker_id'] = $this->session->userdata('broker_id');
        $date = DateTime::createFromFormat('d/m/Y', $data['purchase_date']);
        $data['purchase_date'] = $date->format('Y-m-d');
        $data['transaction_date'] = date('Y-m-d');
        $data['mutual_fund_scheme'] = $data['scheme_id'];
        unset($data['scheme_id']);
        if($data['transaction_type'] == 'Redemption')
            $data['folio_number'] = $data['red_fol_num'];
        unset($data['red_fol_num'], $data['adj_flag']);
        try
        {
            $transID = $this->mf->add_mutual_fund($data);
            //if there is any error
            if(isset($transID['code']))
            {
                var_dump($transID);
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'New Mutual Fund Added!',
                'text' => 'Mutual Fund Details added successfully',
                'transID' => $transID
            );
            //var_dump($transID);
        }
        catch(Custom_exception $e)
        {
            //var_dump($e);
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($transID['code']));
        }
        echo json_encode($message);
    }

    private function fill_form()
    {
        $brokerID = $this->session->userdata('broker_id');
        $cli_condtition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condtition);
        $data['mfTypes'] = $this->mf->get_mf_types_broker_dropdown(array('use_for' => 'Purchase'));
        $data['mfSchemes'] = $this->mf->get_mf_schemes_broker_dropdown('scheme_status = 1');
        $data['bank'] = $this->bank->get_banks("broker_id = ".$brokerID." or broker_id is null and status = 1");
        return $data;
    }

    function get_folio_numbers()
    {
        $client_id = $this->input->post('clientID');
        $brokerID = $this->session->userdata('broker_id');
        $data['folio'] = $this->mf->get_folio_number(array('client_id' => $client_id, 'broker_id' => $brokerID));
        echo json_encode($data);
    }

    function get_mf_types()
    {
        $useFor = $this->input->post('useFor');
        $data['mf_types'] = $this->mf->get_mf_types_broker_dropdown(array('use_for' => $useFor));
        echo json_encode($data);
    }

    function get_mf_scheme()
    {
        $fol_num = $this->input->post('fol_num');
        $type = $this->input->post('type');
        if($type == "All")
        {
            $data['mf_schemes'] = $this->mf->get_mf_schemes_broker_dropdown('scheme_status = 1');
        }
        else
        {
            $data['mf_schemes'] = $this->mf->get_mf_redemption_schemes(array('scheme_status' => 1, 'folio_number' => $fol_num));
        }
        echo json_encode($data);
    }

    //added by Salmaan - 3-5-16
    function get_scheme_nav()
    {
        $schemeID = $this->input->post('scheme_id');
        $purDate = $this->input->post('purchase_date');
        $purDateTemp = DateTime::createFromFormat('d/m/Y',$purDate);
        $schemeDate = $purDateTemp->format('Y-m-d');
        $condition = array('scheme_id'=>$schemeID, 'scheme_date'=>$schemeDate);
        $navData = $this->mf->get_scheme_nav($condition);
        if($navData) {
            echo json_encode($navData->current_nav);
        } else {
            echo json_encode(false);
        }
    }

    function check_folio_number()
    {
        $client_id = $this->input->post('client_id');
        $mf_scheme = $this->input->post('mf_scheme');
        $folio_num = $this->input->post('folio_num');
        $brokerID = $this->session->userdata('broker_id');
        $data = $this->mf->get_folio_number(array('client_id' => $client_id, 'broker_id' => $brokerID, 'folio_number' => $folio_num, 'mutual_fund_scheme' => $mf_scheme));
        $message = '';
        if($data != null)
        {
            $message = array(
                'status' => 'error',
                'title'=> 'Error on Folio Number!',
                'text' => 'Folio Number "'.$folio_num.'" with this scheme already exists for this client'
            );
        }
        echo json_encode($message);
    }

    function save_bank_details()
    {
        $data = $_POST;
        $brokerID = $this->session->userdata('broker_id');
        $condition = array(
            'client_id' => $data['client_id'], 'mutual_fund_type' => $data['mf_type'], 'broker_id' => $brokerID,
            'folio_number'=>$data['folio_number'], 'mutual_fund_scheme' => $data['scheme_id']);
        $update_data = array(
            'bank_id' => $data['bank_id'], 'branch' => $data['branch'], 'account_number' => $data['account_number'],
            'cheque_number' => $data['cheque_number']);
        try
        {
            $status = $this->mf->update_mutual_fund($update_data, $condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $message = array(
                'status' => 'success',
                'title'=> 'Bank Details Added!',
                'text' => 'Bank Details Details with folio number '.$data['folio_number'].' added successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($message);
    }

    function delete_mf()
    {
        $brokerID = $this->session->userdata('broker_id');
        $mf_id = $this->input->post('mf_id');
        $condition = array('transaction_id' => $mf_id, 'broker_id' => $brokerID);
        try
        {
            $status = $this->mf->delete_mutual_fund($condition);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            $success = array(
                'status' => 'success',
                'title'=> 'Mutual Fund Deleted!',
                'text' => 'Mutual Fund Details for deleted successfully'
            );
        }
        catch(Custom_exception $e)
        {
            //display custom message
            $success = array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code']));
        }
        echo json_encode($success);
    }


    //function for valuation after large import
    function mutual_fund_valuation() {
        if(!isset($_POST) || empty($_POST)) {
            alert("Access denied! You are trying to open a page that you shouldn't.");
        } else {
            if(!isset($_POST['brokerID'])) {
                echo json_encode(array("type"=>"error","title"=>"No relevant data!","text"=>"No broker ID found to run valuation for."));
                alert("Access denied! You are trying to open a page that you shouldn't. No Broker ID");
            } else {
                $brokerID = $this->input->post('brokerID');
                if(isset($_POST['transID']) && !empty($_POST['transID'])) {
                    $transID = $this->input->post('transID');
                    if(!is_integer($transID)) {
                        $transID = 0;
                    }
                } else {
                    $transID = 0;
                }
                //now call valuation procedure
                $result = $this->mf->mf_valuation(array("brokerID"=>$brokerID, "transID"=>$transID));
                //var_dump($result);
                if($result === true) {
                    $response = array(
                        'type' => 'success',
                        'title' => 'Mutual Fund Valuation completed!',
                        'text' => 'Valuation of Mutual Funds has been completed successfully'
                    );
                } else {
                    $response = array(
                        'type' => 'error',
                        'title' => 'Error while calculating valuation!',
                        'text' => 'An error occurred in calculating valuation.  Error Code:'.$result['code'].'.  Error msg: '.$result['message']
                    );
                }
                echo json_encode($response);
            }
        }
    }

    ///functions for Mutual Fund Import
    function mutual_fund_import($mf_data = null, $pip_data = null, $val_data = null)
    {
        $header['title'] = 'Mutual Fund Details Import';
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
        $data['import_data'] = $mf_data;
        $data['pip_or_ipo_data'] = $pip_data;
        $data['val_data'] = $val_data;
        $this->load->view('broker/mutual_fund/mf_import', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    /*function mf_import()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');


        $uploadedStatus = 0;
        $message = ""; $mfMessage = ""; $insertRow = true;
        $mf_data = array();
        $pip_data = array();
        $val_data = null;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            //$transactionType = $this->input->post('transaction_type');
            $transactionType = "";
            if (isset($_FILES["import_mf"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_mf"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    try {
                    //get tmp_name of file
                    $file = $_FILES["import_mf"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //var_dump($maxCell);
                    //temp variables to hold values
                    $folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                    $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                    $adj_flag = 0; $tempMFType = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                    //var_dump($excelData);
                    //die();
                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    $dataRows = array(); $remRows = array();
                    $countRow = 0; $countErrRow = 0; $countMF = 0; $countRem = 0; $countTrans = 0;

                    //echo $rem_redemption_amt,'/';
                    $rem_details = $this->rem->get_reminder_days(array('broker_id' => $brokerID));
                    if($rem_details) {
                        //var_dump($rem_details);
                        $rem_redemption_amt = $rem_details[0]->mf_redemption_amount;
                        $rem_dividend_amt = $rem_details[0]->mf_dpo_amount;
                    }
                    else
                    {
                        $rem_redemption_amt =1;
                        $rem_dividend_amt=1 ;
                    }
                    //echo $rem_redemption_amt,'<br/>';
                    $rem_red_amt = $rem_redemption_amt;
                    //echo 'rem',$rem_red_amt;
                    foreach($excelData as $rows)
                    {
                        $countCell = 0;
                        foreach($rows as $cell)
                        {
                            //var_dump($rows);
                            if($countRow == 0)
                            {
                                $cell = str_replace(array('.'), '', $cell);
                                if(strtoupper($cell) == 'SR NO' || strtoupper($cell) == 'TRXNNO' || strtoupper($cell) == 'TD_TRNO' || strtoupper($cell) == 'TRXN_NO' || strtoupper($cell) == 'USRTRXNO' ||
                                    strtoupper($cell) == 'TRANSACTION NUMBER' ||
                                    strtoupper($cell) == 'GROUP NAME' ||
                                    strtoupper($cell) == 'INVESTOR NAME' ||
                                    strtoupper($cell) == 'PAN NO' || strtoupper($cell) == 'PAN' || strtoupper($cell) == 'PAN1' || strtoupper($cell) == 'IT_PAN_NO1' ||
                                    strtoupper($cell) == 'TRN TYPE' || strtoupper($cell) == 'TRXNTYPE' || strtoupper($cell) == 'TD_TRTYPE' || strtoupper($cell) == 'TR_TYPE' || strtoupper($cell) == 'TRANSACTION TYPE' || strtoupper($cell) == 'TRXN_TYPE' ||
                                    strtoupper($cell) == 'DATE' || strtoupper($cell) == 'TRADDATE' || strtoupper($cell) == 'CRDATE' || strtoupper($cell) == 'TRXN_DATE' || strtoupper($cell) == 'NAVDATE' || strtoupper($cell) == 'TRANSACTION DATE' || strtoupper($cell) == 'TD_TRDT' ||
                                    strtoupper($cell) == 'FOLIO NO' || strtoupper($cell) == 'FOLIO_NO' || strtoupper($cell) == 'TD_ACNO' || strtoupper($cell) == 'FOLIO NUMBER' ||
                                    strtoupper($cell) == 'SCHEME NAME' || strtoupper($cell) == 'SCHEME' || strtoupper($cell) == 'FUNDDESC' || strtoupper($cell) == 'PROD_CODE' || strtoupper($cell) == 'FUND DESCRIPTION' || strtoupper($cell) == 'SCHEME_NAME' ||
                                    strtoupper($cell) == 'PRODCODE' || strtoupper($cell) == 'PRODUCT_CODE' || strtoupper($cell) == 'PRODUCT_CO' || strtoupper($cell) == 'FMCODE' || strtoupper($cell) == 'SCHEME_CO0' || strtoupper($cell) == 'PRODUCT CODE' ||
                                    strtoupper($cell) == 'NAV' || strtoupper($cell) == 'PURPRICE' || strtoupper($cell) == 'TD_POP' || strtoupper($cell) == 'PRICE' ||
                                    strtoupper($cell) == 'UNIT' || strtoupper($cell) == 'UNITS' || strtoupper($cell) == 'TD_UNITS' ||
                                    strtoupper($cell) == 'AMOUNT' || strtoupper($cell) == 'TD_AMT' ||
                                    strtoupper($cell) == 'BAL UNIT' ||
                                    strtoupper($cell) == 'DPO PER UNIT' ||
                                    strtoupper($cell) == 'ADJUSTMENT' ||
                                    strtoupper($cell) == 'ADJUSTMENT REF NO')
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    $uploadedStatus = 2;
                                    //echo $countCell-'head';
                                    //var_dump($dataColumns[$countCell]);
                                    continue;
                                }
                                else
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    /*$message = 'Columns Specified in Excel is not in correct format';
                                    $uploadedStatus = 0;
                                    break;*/
                                    /*continue;
                                }
                            }
                            else
                            {
                                //var_dump($countCell);
                                //echo $countCell.'-body';
                                //echo '<br/><br/><br/><br/><br/><br/>Salmaan<br/><br/><br/><br/><br/><br/>';
                                //var_dump($dataColumns[$countCell]);
                                if($insertRow)
                                {
                                    if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER' || strtoupper($dataColumns[$countCell]) == 'USRTRXNO')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $srNo = trim($cell);
                                        }
                                        else
                                        {
                                            $srNo = "";
                                            /*$insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";*/
                                        /*}
                                    }
                                    /*else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                        strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $panNum = trim($cell);
                                            $wherePan = array(
                                                'c.pan_no'=>$panNum,
                                                'f.broker_id'=>$brokerID
                                            );
                                            //checks if policy exists in policy details table
                                            $c_info = $this->client->get_client_family_by_pan($wherePan);
                                            if(count($c_info) == 0)
                                            {
                                                $insertRow = false;
                                                $mfMessage = "Pan No. ".$panNum." Doesn't exists for any client";
                                            }
                                            else
                                            {
                                                $clientId = $c_info->client_id;
                                                $clientName = $c_info->client_name;
                                                $familyId = $c_info->family_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'SCHEME NAME' || strtoupper($dataColumns[$countCell]) == 'SCHEME' ||
                                        strtoupper($dataColumns[$countCell]) == 'FUNDDESC' || strtoupper($dataColumns[$countCell]) == 'PROD_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'FUND DESCRIPTION' || strtoupper($dataColumns[$countCell]) == 'SCHEME_NAME')
                                    {
                                        /*if($cell || $cell != '')
                                        {
                                            $schemeName = trim($cell);
                                            $whereScheme = 'scheme_name = "'.$schemeName.'" AND scheme_status = 1';
                                            $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                            if(count($scheme_details) == 0)
                                            {
                                                /*$mfMessage = 'Scheme '.$schemeName." Doesn't Exists or is Inactive";
                                                $insertRow = false;*/
                                            /*}
                                            else
                                            {
                                                $schemeId = $scheme_details[0]->scheme_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme cannot be empty";
                                        }*/
                                    /*}
                                    else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' ||
                                        strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $prodCode = trim($cell);
                                            $whereScheme = 'prod_code = "'.$prodCode.'" AND scheme_status = 1';
                                            $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                            if(count($scheme_details) == 0)
                                            {
                                                $mfMessage = 'Product Code (scheme) '.$prodCode." Doesn't Exists or is Inactive";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $schemeId = $scheme_details[0]->scheme_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme/Product Code cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $folioNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Folio Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $mfType = trim(strtoupper($cell));
                                            $tempMFType = $mfType;
                                            if($mfType == "DIV" || $mfType == "DR" || $mfType == "DIR" || $mfType == "BNS" || $mfType == "BNSR" || $mfType == "DIRR") {
                                                $transactionType = "Purchase";
                                                $mfType = "DIV";
                                            } elseif($mfType == "NFO") {
                                                $transactionType = "Purchase";
                                                $mfType = "NFO";
                                            } elseif($mfType == "PIP" || $mfType == "SIN" || $mfType == "NEW" || $mfType == "ADD" || $mfType == "P" ||
                                                    $mfType == "ADDPUR" || $mfType == "NEWPUR" || $mfType == "SIP" || $mfType == "ADDR" ||
                                                    $mfType == "ADDRR" || $mfType == "NEWR" || $mfType == "SINR" || $mfType == "SINRR" ||
                                                    $mfType == "ADDPURR" || $mfType == "NEWPURR" || $mfType == "PURR" || $mfType == "SIPR" || 
                                                    $mfType == "CNI" || $mfType == "CNIR" || $mfType == "UPLO" || $mfType == "UPLOR" || $mfType == "BON" || 
                                                    $mfType == "BONR") {
                                                $transactionType = "Purchase";
                                                $mfType = "PIP";
                                            } elseif($mfType == "IPO" || $mfType == "IPOR" || $mfType == "IPORR" || $mfType == "TRFI" || $mfType == "TRFIR") {
                                                $transactionType = "Purchase";
                                                $mfType = "IPO";
                                            } elseif($mfType == "SWI" || $mfType == "SI" || $mfType == "STPA" || $mfType == "LTIA" ||
                                                    $mfType == "STPI" || $mfType == "SWIN" || $mfType == "DSPI" || $mfType == "DTPIN" ||
                                                    $mfType == "LTIN" || $mfType == "STRA" || $mfType == "DSPIR" || $mfType == "LTIAR" ||
                                                    $mfType == "LTIARR" || $mfType == "LTINR" || $mfType == "STPAR" || $mfType == "SWIA" ||
                                                    $mfType == "TMI" || $mfType == "TRMI" || $mfType == "SWINR" || $mfType == "DSPA" || 
                                                    $mfType == "DSPN" || $mfType == "STPN" || $mfType == "STPNR" || $mfType == "STRAR" || 
                                                    $mfType == "STRI" || $mfType == "STRIR" || $mfType == "SWIAR" || $mfType == "TMIR") {
                                                $transactionType = "Purchase";
                                                $mfType = "SWI";
                                            } elseif($mfType == "TIN" || $mfType == "TI") {
                                                $transactionType = "Purchase";
                                                $mfType = "TIN";
                                            } elseif($mfType == "SWO" || $mfType == "SO" || $mfType == "STPO" || $mfType == "LTOP" ||
                                                    $mfType == "DSPO" || $mfType == "LTOF" || $mfType == "STRO" || $mfType == "LTOFR" ||
                                                    $mfType == "SWOF" || $mfType == "TMO" || $mfType == "TRMO" || $mfType == "DSPOR" ||
                                                    $mfType == "SWOFR" || $mfType == "LTOPR" || $mfType == "STROR" || $mfType == "SWOP" || 
                                                    $mfType == "SWOPR" || $mfType == "STPOR" || $mfType == "TRMOR") {
                                                $transactionType = "Redemption";
                                                $mfType = "SWO";
                                            } elseif($mfType == "DP" || $mfType == "DIVIDEND PAYOUT" || $mfType == "DIVR" || $mfType == "DPR") {
                                                $transactionType = "Redemption";
                                                $mfType = "DP";
                                            } elseif($mfType == "RED" || $mfType == "FUL" || $mfType == "FULR" || $mfType == "FULRR" || $mfType == "REDR" ||
                                                $mfType == "R" || $mfType == "TO" || $mfType == "TOCOB" || $mfType == "CNO" || $mfType == "CNOR" || 
                                                $mfType == "SWD" || $mfType == "SWDR" || $mfType == "TRFO" || $mfType == "TRFOR" || $mfType == "TRMO" || 
                                                $mfType == "CFI" || $mfType == "CFIR" || $mfType == "DMT" || $mfType == "DMTR") {
                                                $transactionType = "Redemption";
                                                $mfType = "RED";
                                            } else {
                                                //it might be CAMS file, which has trn_type with numbers, so check that
                                                if(strpos($mfType,"P") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "PIP";
                                                } elseif(strpos($mfType,"DR") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "DIV";
                                                } elseif(strpos($mfType,"SI") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "SWI";
                                                } elseif(strpos($mfType,"TI") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "TIN";
                                                } elseif(strpos($mfType,"R") === 0 || strpos($mfType,"TO") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "RED";
                                                } elseif(strpos($mfType,"DP") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "DP";
                                                } elseif(strpos($mfType,"SO") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "SWO";
                                                } else {
                                                    //maybe not CAMS file, Transaction Type is out-of-this-world, so show error
                                                    $transactionType = "Unknown";
                                                }
                                            }

                                            $where = array('mutual_fund_type' => $mfType, 'use_for' => $transactionType);
                                            $trn_details = $this->mf->get_mf_types_broker_dropdown($where);
                                            if(count($trn_details) == 0)
                                            {
                                                $mfMessage = 'Trn Type '.$mfType." doesn't exist for ".$transactionType;
                                                $insertRow = false;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Trn Type cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'DATE' || strtoupper($dataColumns[$countCell]) === 'TRADDATE' ||
                                        strtoupper($dataColumns[$countCell]) === 'TRXN_DATE' || strtoupper($dataColumns[$countCell]) === 'NAVDATE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRDT' || strtoupper($dataColumns[$countCell]) == 'TRANSACTION DATE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            //var_dump($cell);
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $purDate = $date->format('Y-m-d');
                                            } else {
                                                //check if date is in string format YYYYMMDD
                                                $tempDate = trim($cell);
                                                if(strlen($tempDate) == 8) {
                                                    $tempDate = (string)$tempDate;
                                                    /*$year = substr($tempDate,0,4);
                                                    $month = substr($tempDate,4,2);
                                                    $day = substr($tempDate,6,2);*/
                                                    /*$date = DateTime::createFromFormat('Ymd', $tempDate);
                                                    if(is_object($date)) {
                                                        $purDate = $date->format('Y-m-d');
                                                    } else {
                                                        $insertRow = false;
                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                    }
                                                } else {
                                                    $insertRow = false;
                                                    $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                }
                                            }
                                            /*try {
                                                $date = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $purDate = $date->format('Y-m-d');
                                            } catch(Exception $e) {
                                                $insertRow = false;
                                                $insMessage = "Date format is not proper";
                                            }*/
                                        /*}
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Date cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'NAV' || strtoupper($dataColumns[$countCell]) === 'PURPRICE' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_POP' || strtoupper($dataColumns[$countCell]) === 'PRICE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $nav = $cell;
                                        }
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "NAV cannot be empty";
                                            $nav = 0;
                                        }
                                    }
                                    /*else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $unit = $cell;
                                            //Check if we need to make unit negative (in case of Franklin file)
                                            if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR") && ($unit > 0)) {
                                                $unit = -$unit;
                                            }
                                        }
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "Unit cannot be empty";
                                            $unit = 0;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $amount = $cell;
                                            //Check if we need to make amount negative (in case of Franklin file)
                                            if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR") && ($amount > 0)) {
                                                $amount = -$amount;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Amount cannot be empty";
                                        }
                                    }*/
                                    /*else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $unit = floatval($cell);
                                            //Check if we need to make unit negative (in case of Franklin file)
                                            /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                                $unit = -$unit;
                                            }*/
                                        /*}
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "Unit cannot be empty";
                                            $unit = 0;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $amount = floatval($cell);
                                            //Check if we need to make amount negative (in case of Franklin file)
                                            /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($amount > 0)) {
                                                $amount = -$amount;
                                            }*/
                                        /*}
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Amount cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'BAL UNIT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $balUnit = $cell;
                                        }
                                        else
                                        {
                                            $balUnit = '0';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'DPO PER UNIT')
                                    {
                                        if($cell || $cell != '')
                                            $dpo = $cell;
                                        else
                                            $dpo = 0;

                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT')
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
                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT REF NO')
                                    {
                                        if($cell || $cell != '')
                                            $adjRefNum = $cell;
                                        else
                                            $adjRefNum = "";
                                    }
                                } else {
                                	if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER' || strtoupper($dataColumns[$countCell]) == 'USRTRXNO')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $srNo = trim($cell);
                                        }
                                        else
                                        {
                                            $srNo = "";
                                            /*$insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";*/
                                        /*}
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                        strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $panNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $folioNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Folio Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' || strtoupper($dataColumns[$countCell]) == 'SCHEME CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $prodCode = trim($cell);
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme/Product Code cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $mfType = trim(strtoupper($cell));
                                            $tempMFType = $mfType;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Trn Type cannot be empty";
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
                                $mf_data[$countErrRow][1] = $prodCode;
                                $mf_data[$countErrRow][2] = $folioNum;
                                $mf_data[$countErrRow][3] = $tempMFType;
                                $mf_data[$countErrRow][4] = $srNo;
                                $mf_data[$countErrRow][5] = $panNum;
                                $mf_data[$countErrRow][6] = $mfMessage;
                                $countErrRow++;
                                $insertRow = true;
                                $uploadedStatus = 2;
                                continue;
                            }

                            //check if trn_type was DIV, because Karvy has named DP as DIV, so we'll check its units
                            if($mfType == "DIV" && floatval($unit) == 0) {
                                //change type to DP
                                $mfType = "DP";
                                $transactionType = "Redemption";
                            }

                            if(strtoupper($mfType) == 'PIP' || strtoupper($mfType) == 'IPO')
                            {
                                $pip_data[$countTrans] = array(
                                    /*'action' => '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Add Bank Details"
                                        onclick="add_bank_details('."'".$clientId."', "."'".$folioNum."', "."'".$schemeId."', "."'".$mfType."'".')">
                                        <i class="fa fa-plus"></i> Add Bank Detail</a>',*/
                                    /*'client_name' => $clientName, 'prod_code' => $prodCode, 'folio_number' => $folioNum, 'trans_type' => $mfType,
                                    'pan_no' => $panNum, 'sr_no' => $srNo
                                );
                                $countTrans++;
                            }

                            /*if(strtoupper($mfType) == 'DP' && floatval($dpo) == 0) {
                                $dpo = $amount/$unit;
                            }*/
                            
                            //Check if we need to make amount negative (in case of Franklin file)
                            /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                $unit = -($unit);
                                $amount = -($amount);
                            }

                            if($balUnit == '0' || empty($balUnit)) {
                                $balUnit = $tempMFType;
                            }
                            if(!empty($srNo) && empty($adjRefNum)) {
                                $adjRefNum = $srNo;
                            }


                            $dataRows[$countMF] = array(
                                'client_id' => $clientId, 'family_id' => $familyId,  'transaction_date' => date('Y-m-d'),
                                'mutual_fund_scheme' => $schemeId, 'mutual_fund_type' => $mfType, 'transaction_type' => $transactionType,
                                'folio_number' => $folioNum, 'purchase_date' => $purDate, 'quantity' => $unit, 'nav' => $nav, 'amount' => $amount,
                                'adjustment_flag' => $adj_flag, 'adjustment' => $adj, 'adjustment_ref_number' => $adjRefNum,
                                'balance_unit' => $balUnit, 'DPO_units' => $dpo, 'user_id' => $user_id, 'broker_id' => $brokerID
                            );
                            //var_dump($dataRows[$countMF]);
                            $countMF++;

                            $today = new DateTime();
                            $interval = $date->diff($today);
                            $daydiff = $interval->format('%a');

                            if(strtoupper($mfType) === 'RED' && ($amount >= $rem_red_amt) && $daydiff <= 15)
                            {
                                //echo $rem_red_amt, '----', $amount,'<br/>';
                                $rem_message = 'Redemption Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                $countRem2 = array(
                                    'reminder_type'=>'MF Redemption',
                                    'client_id' => $clientId,
                                    'client_name' => $clientName,
                                    'reminder_date' => $purDate,
                                    'reminder_message' => $rem_message,
                                    'broker_id'=>$brokerID
                                );

                                $this->rem->add_reminder($countRem2);
                            }
                            else if(strtoupper($mfType) === 'DP' && ($amount >= $rem_dividend_amt) && $daydiff <= 15)
                            {
                                $rem_message = 'Dividend Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                $countRem2 = array(
                                    'reminder_type'=>'MF Dividend Payout',
                                    'client_id' => $clientId,
                                    'client_name' => $clientName,
                                    'reminder_date' => $purDate,
                                    'reminder_message' => $rem_message,
                                    'broker_id'=>$brokerID
                                );

                                $this->rem->add_reminder($countRem2);
                            }

                           /* $remRows[$countRem] = array(
                                'reminder_type'=>'MF Redemption',
                                'client_id' => $clientId,
                                'client_name' => $clientName,
                                'reminder_date' => $purDate,
                                'reminder_message' => $rem_message,
                                'broker_id'=>$brokerID
                            );*/

                            //$this->rem->add_reminder($remRows[$countRem]);

                            /*$countRem++;
                            /*var_dump($dataRows);
                            var_dump($remRows);*/

                            /*$folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                            $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                            $adj_flag = 0; $tempMFType = "";
                        }
                        if($uploadedStatus == 0)
                            break;
                        $countRow++;
                    }

                    /*get last transaction_id from mf_transactions*/
                    /*$trans = $this->mf->get_last_trans();
                    if(!($trans) || empty($trans) || empty($trans->transID)) {
                        $transID = 0;
                    } else {
                        $transID = ($trans->transID + 1);
                    }


                    if($dataRows)
                    {
                        $countTrans = count($dataRows);
                        //var_dump($dataRows);
                        /*$result = $this->mf->get_mutual_funds(array('transaction_date' => date('Y-m-d'), 'mf.broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        if($result)
                        {
                            $this->mf->delete_mutual_fund(array('transaction_date' => date('Y-m-d'), 'broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        }*/

                        //we need to sort the data array before inserting - Salmaan - 27/10/2016
                        // Obtain a list of columns
                        /*foreach ($dataRows as $key => $row) {
                            $purchase_date[$key]  = $row['purchase_date'];
                            $trn_type[$key] = $row['transaction_type'];
                            $quantity[$key] = $row['quantity'];
                            $trn_no[$key] = $row['adjustment_ref_number'];
                        }

                        // Sort the data with volume descending, edition ascending
                        array_multisort($purchase_date, SORT_ASC, $trn_type, SORT_ASC, $quantity, SORT_DESC, $trn_no, SORT_ASC, $dataRows);

                        $result = $this->mf->add_import_mutual_funds($dataRows, $transID);
                        if(is_numeric($result)) {
                            $uploadedStatus = 2;
                            $message = "Error in inserting into temp table - transID:".$transID;
                        } else {
                            //var_dump($result);
                            $this->common->last_import('Mutual Fund Details', $brokerID, $_FILES["import_mf"]["name"], $user_id);
                            $uploadedStatus = 1;
                            $message = "Mutual Fund Details Uploaded Successfully";
                        }
                    } else {
                        $countTrans = 0;
                    }
                    unset($dataColumns, $dataRows);
                } catch(Exception $e) {
                        var_dump($e);
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }



            if($uploadedStatus == 1)
            {
                //echo "<br/>Inside 1<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));

                $success = array(
                    "title" => "Success!",
                    "text" => $message
                );
                $this->session->set_userdata('success', $success);
				//call mf_valuation procedure
                if($countTrans < 500) {
				    $this->mf->mf_valuation(array("brokerID"=>$brokerID, "transID"=>$transID));
                } else {
                    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => $transID
                    );
                }
            }
            else if ($uploadedStatus == 2)
            {
                //echo "<br/>Inside 2<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));
                $info = array(
                    "title" => "Info for Import!",
                    "text" => 'Few Records were not imported please check the table below'
                );
                $this->session->set_userdata('info', $info);
				//call mf_valuation procedure
				//$this->mf->mf_valuation(array("brokerID"=>$brokerID));
                if($countTrans < 500) {
                    $this->mf->mf_valuation(array("brokerID"=>$brokerID, "transID"=>$transID));
                } else {
                    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => $transID
                    );
                }
            }
            else
            {
                //echo "<br/>Inside else<br/>";
                $error = array(
                    "title" => "Error on uploading!",
                    "text" => $message
                );
                $this->session->set_userdata('error', $error);
            }
        }
        $this->mutual_fund_import($mf_data, $pip_data, $val_data);
    }

    //function to calculate MF Valuation of all clients of broker
    function mf_valuation() {
        if(!isset($_POST) || empty($_POST)) {
            alert("Access denied! You are trying to open a page that you shouldn't.");
        } else {
            if(!isset($_POST['brokerID'])) {
                echo "No Broker ID found";
                alert("Access denied! You are trying to open a page that you shouldn't. No Broker ID");
            } else {
                $brokerID = $this->input->post('brokerID');
                if(isset($_POST['transID']) && !empty($_POST['transID'])) {
                    $transID = $this->input->post('transID');
                } else {
                    $transID = 0;
                }
                //first get all families of broker
                $families = $this->family->get_families_broker_dropdown($brokerID);

                $where = array(); $valuation = true;
                //delete existing valuation of broker
                $deleted = $this->mf->delete_mf_valuation(array("broker_id",$brokerID));
                if($deleted === true) {
                    //now we'll call mf_valuation_family procedure for each family
                    foreach($families as $family) {
                        //var_dump($family);
                        $where = array(
                            'familyID'=> $family->family_id,
                            'brokerID'=> $brokerID
                        );
                        $result = $this->mf->mf_valuation($where);
                        if(is_array($result) && isset($result['code'])) {
                            $response = array(
                                'type' => 'error',
                                'title' => 'Error while calculating valuation!',
                                'text' => 'An error occurred in calculating a family valuation.  Family:'.$family->name.'  Error Code:'.$result['message']
                            );
                            $valuation = false;
                            break;
                        }
                    }
                } else {
                    $response = array(
                        'type' => 'error',
                        'title' => 'Error while deleting old valuation!',
                        'text' => 'An error occurred in calculating valuation.  Error Code:'.$deleted['message']
                    );
                }

                //check if valuation is complete
                if($valuation) {
                    $response = array(
                        'type' => 'success',
                        'title' => 'Mutual Fund Valuation completed!',
                        'text' => 'Valuation of Mutual Funds has been completed successfully'
                    );
                }

                echo json_encode($response);
            }
        }
    }*/
    
    
    
    
    function mf_import()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');


        $uploadedStatus = 0;
        $message = ""; $mfMessage = ""; $insertRow = true;
        $mf_data = array();
        $pip_data = array();
        $val_data = null;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($_POST["Import"]))
        {
            //$transactionType = $this->input->post('transaction_type');
            $transactionType = "";
            if (isset($_FILES["import_mf"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_mf"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    try {
                    //get tmp_name of file
                    $file = $_FILES["import_mf"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //var_dump($maxCell);
                    //temp variables to hold values
                    $folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                    $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                    $adj_flag = 0; $tempMFType = ""; 
					$amc = ""; $arn = ""; $sub_arn = ""; $cheque_no = ""; $cheque_bank = ""; $account_no = ""; $ref_no = ""; $rej_ref_no = ""; $trxn_mode = "";
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                    //var_dump($excelData);
                    //die();
                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    $dataRows = array(); $remRows = array();
                    $countRow = 0; $countErrRow = 0; $countMF = 0; $countRem = 0; $countTrans = 0;

                    //echo $rem_redemption_amt,'/';
                    $rem_details = $this->rem->get_reminder_days(array('broker_id' => $brokerID));
                    if($rem_details) {
                        //var_dump($rem_details);
                        $rem_redemption_amt = $rem_details[0]->mf_redemption_amount;
                        $rem_dividend_amt = $rem_details[0]->mf_dpo_amount;
                    }
                    else
                    {
                        $rem_redemption_amt =1;
                        $rem_dividend_amt=1 ;
                    }
                    //echo $rem_redemption_amt,'<br/>';
                    $rem_red_amt = $rem_redemption_amt;
                    //echo 'rem',$rem_red_amt;
                    foreach($excelData as $rows)
                    {
                        $countCell = 0;
                        foreach($rows as $cell)
                        {
                            //var_dump($rows);
                            if($countRow == 0)
                            {
                                $cell = str_replace(array('.'), '', $cell);
                                if(strtoupper($cell) == 'SR NO' || strtoupper($cell) == 'TRXNNO' || strtoupper($cell) == 'TD_TRNO' || strtoupper($cell) == 'TRXN_NO' ||
                                    strtoupper($cell) == 'TRANSACTION NUMBER' ||
                                    strtoupper($cell) == 'GROUP NAME' ||
                                    strtoupper($cell) == 'INVESTOR NAME' ||
                                    strtoupper($cell) == 'PAN NO' || strtoupper($cell) == 'PAN' || strtoupper($cell) == 'PAN1' || strtoupper($cell) == 'IT_PAN_NO1' ||
                                    strtoupper($cell) == 'TRN TYPE' || strtoupper($cell) == 'TRXNTYPE' || strtoupper($cell) == 'TD_TRTYPE' || strtoupper($cell) == 'TR_TYPE' || strtoupper($cell) == 'TRANSACTION TYPE' || strtoupper($cell) == 'TRXN_TYPE' ||
                                    strtoupper($cell) == 'DATE' || strtoupper($cell) == 'TRADDATE' || strtoupper($cell) == 'CRDATE' || strtoupper($cell) == 'TRXN_DATE' || strtoupper($cell) == 'NAVDATE' || strtoupper($cell) == 'TRANSACTION DATE' || strtoupper($cell) == 'TD_TRDT' ||
                                    strtoupper($cell) == 'FOLIO NO' || strtoupper($cell) == 'FOLIO_NO' || strtoupper($cell) == 'TD_ACNO' || strtoupper($cell) == 'FOLIO NUMBER' ||
                                    strtoupper($cell) == 'SCHEME NAME' || strtoupper($cell) == 'SCHEME' || strtoupper($cell) == 'FUNDDESC' || strtoupper($cell) == 'PROD_CODE' || strtoupper($cell) == 'FUND DESCRIPTION' || strtoupper($cell) == 'SCHEME_NAME' ||
                                    strtoupper($cell) == 'PRODCODE' || strtoupper($cell) == 'PRODUCT_CODE' || strtoupper($cell) == 'PRODUCT_CO' || strtoupper($cell) == 'FMCODE' || strtoupper($cell) == 'SCHEME_CO0' || strtoupper($cell) == 'PRODUCT CODE' ||
                                    strtoupper($cell) == 'NAV' || strtoupper($cell) == 'PURPRICE' || strtoupper($cell) == 'TD_POP' || strtoupper($cell) == 'PRICE' || strtoupper($cell) == 'POP' || 
                                    strtoupper($cell) == 'UNIT' || strtoupper($cell) == 'UNITS' || strtoupper($cell) == 'TD_UNITS' ||
                                    strtoupper($cell) == 'AMOUNT' || strtoupper($cell) == 'TD_AMT' || 
                                    strtoupper($cell) == 'DPO PER UNIT' ||
                                    strtoupper($cell) == 'ADJUSTMENT' ||
                                    strtoupper($cell) == 'ADJUSTMENT REF NO' 
									|| strtoupper($cell) == 'AMC NAME' || strtoupper($cell) == 'TD_FUND' || strtoupper($cell) == 'FUND' || strtoupper($cell) == 'AMC_CODE' || strtoupper($cell) == 'COMP_CODE' 
									|| strtoupper($cell) == 'ARN' || strtoupper($cell) == 'TD_AGENT' || strtoupper($cell) == 'AGENT CODE' || strtoupper($cell) == 'BROKCODE' || strtoupper($cell) == 'BROK_CODE' 
									|| strtoupper($cell) == 'SUB ARN' || strtoupper($cell) == 'TD_BROKER' || strtoupper($cell) == 'SUB-BROKER CODE' || strtoupper($cell) == 'SUBBROK' || strtoupper($cell) == 'SUB_BROKE5' 
									|| strtoupper($cell) == 'CHEQUE NO' || strtoupper($cell) == 'CHQNO' || strtoupper($cell) == 'INSTRUMENT NUMBER' || strtoupper($cell) == 'MICR_NO' || strtoupper($cell) == 'CHECK_NO' 
									|| strtoupper($cell) == 'CHEQUE BANK' || strtoupper($cell) == 'CHQBANK' || strtoupper($cell) == 'INSTRUMENT BANK' || strtoupper($cell) == 'BANK_NAME' || strtoupper($cell) == 'PBANK_NAME' 
									|| strtoupper($cell) == 'BANK A/C NO' || strtoupper($cell) == 'AC_NO' || strtoupper($cell) == 'PERSONAL23' 
									|| strtoupper($cell) == 'REF NO' || strtoupper($cell) == 'USRTRXNO' 
									|| strtoupper($cell) == 'REJ REF NO' || strtoupper($cell) == 'REJTRNOOR2' || strtoupper($cell) == 'REJTRNOORGNO' 
									|| strtoupper($cell) == 'TRXN MODE' || strtoupper($cell) == 'TRXNMODE' )
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    $uploadedStatus = 2;
                                    //echo $countCell-'head';
                                    //var_dump($dataColumns[$countCell]);
                                    continue;
                                }
                                else
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    /*$message = 'Columns Specified in Excel is not in correct format';
                                    $uploadedStatus = 0;
                                    break;*/
                                    continue;
                                }
                            }
                            else
                            {
                                //var_dump($countCell);
                                //echo $countCell.'-body';
                                //echo '<br/><br/><br/><br/><br/><br/>Salmaan<br/><br/><br/><br/><br/><br/>';
                                //var_dump($dataColumns[$countCell]);
                                if($insertRow)
                                {
                                    if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $srNo = trim($cell);
                                        }
                                        else
                                        {
                                            $srNo = "";
                                            /*$insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";*/
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                        strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                    {
                                        /* Commented by Salmaan - 2017-06-27 - code for non-pan below
                                        if($cell || $cell != '')
                                        {
                                            $panNum = trim($cell);
                                            $wherePan = array(
                                                'c.pan_no'=>$panNum,
                                                'f.broker_id'=>$brokerID
                                            );
                                            //checks if policy exists in policy details table
                                            $c_info = $this->client->get_client_family_by_pan($wherePan);
                                            if(count($c_info) == 0)
                                            {
                                                $insertRow = false;
                                                $mfMessage = "Pan No. ".$panNum." Doesn't exists for any client";
                                            }
                                            else
                                            {
                                                $clientId = $c_info->client_id;
                                                $clientName = $c_info->client_name;
                                                $familyId = $c_info->family_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";
                                        }*/
                                        if($cell || $cell != '')
                                        {
                                            $panNum = trim($cell);
                                            $wherePan = array(
                                                'c.pan_no'=>$panNum,
                                                'f.broker_id'=>$brokerID
                                            );
                                            //checks if pan exists in clients table @pallavi
                                            $c_info = $this->client->get_client_family_by_pan($wherePan);
                                            if(count($c_info) == 0)
                                            {
                                                $insertRow = false;
                                                $mfMessage = "Pan No. ".$panNum." Doesn't exists for any client";
                                            }
                                            else
                                            {
                                                $clientId = $c_info->client_id;
                                                $clientName = $c_info->client_name;
                                                $familyId = $c_info->family_id;
                                            }
                                        }
                                        else
                                        {
                                          //@pallavi//
                                              $wherePan = array(
                                                  'cb.productId'=>$prodCode,
                                                  'cb.folio_number'=>$folioNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                          $c_info1 = $this->client->get_client_family_by_withoutpan($wherePan);

                                          //below code modified by Salmaan - 2017-05-26
                                          if(count($c_info1) == 0)
                                          {
                                            $insertRow = false;
                                            $mfMessage = "Transaction information not related to any Client's History";
                                          }
                                          else
                                          {
                                              //now we have to fetch the ref_client_id if already merged
                                              $c_info2 = $this->client->get_client_family_merge_ref(array('c.client_id'=>$c_info1->client_id));
                                              if(count($c_info2) == 0) {
                                                $clientId = $c_info1->client_id;
                                                $clientName = $c_info1->client_name;
                                                $familyId = $c_info1->family_id;
                                              } else {
                                                if(isset($c_info2) && !empty($c_info2)) {
                                                  $clientId = $c_info2[0]['client_id'];
                                                  $clientName = $c_info2[0]['name'];
                                                  $familyId = $c_info2[0]['family_id'];
                                                }
                                              }
                                          }

                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'SCHEME NAME' || strtoupper($dataColumns[$countCell]) == 'SCHEME' ||
                                        strtoupper($dataColumns[$countCell]) == 'FUNDDESC' || strtoupper($dataColumns[$countCell]) == 'PROD_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'FUND DESCRIPTION' || strtoupper($dataColumns[$countCell]) == 'SCHEME_NAME')
                                    {
                                        /*if($cell || $cell != '')
                                        {
                                            $schemeName = trim($cell);
                                            $whereScheme = 'scheme_name = "'.$schemeName.'" AND scheme_status = 1';
                                            $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                            if(count($scheme_details) == 0)
                                            {
                                                /*$mfMessage = 'Scheme '.$schemeName." Doesn't Exists or is Inactive";
                                                $insertRow = false;*/
                                            /*}
                                            else
                                            {
                                                $schemeId = $scheme_details[0]->scheme_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme cannot be empty";
                                        }*/
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' ||
                                        strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $prodCode = trim($cell);
                                            $whereScheme = 'prod_code = "'.$prodCode.'" AND scheme_status = 1';
                                            $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                            if(count($scheme_details) == 0)
                                            {
                                                $mfMessage = 'Product Code (scheme) '.$prodCode." Doesn't Exists or is Inactive";
                                                $insertRow = false;
                                            }
                                            else
                                            {
                                                $schemeId = $scheme_details[0]->scheme_id;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme/Product Code cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $folioNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Folio Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $mfType = trim(strtoupper($cell));
                                            $tempMFType = $mfType;
                                            if($mfType == "DIV" || $mfType == "DR" || $mfType == "DIR" || $mfType == "BNS" || $mfType == "BNSR" || $mfType == "DIRR") {
                                                $transactionType = "Purchase";
                                                $mfType = "DIV";
                                            } elseif($mfType == "NFO") {
                                                $transactionType = "Purchase";
                                                $mfType = "NFO";
                                            } elseif($mfType == "PIP" || $mfType == "SIN" || $mfType == "NEW" || $mfType == "ADD" || $mfType == "P" ||
                                                    $mfType == "ADDPUR" || $mfType == "NEWPUR" || $mfType == "SIP" || $mfType == "ADDR" ||
                                                    $mfType == "ADDRR" || $mfType == "NEWR" || $mfType == "SINR" || $mfType == "SINRR" ||
                                                    $mfType == "ADDPURR" || $mfType == "NEWPURR" || $mfType == "PURR" || $mfType == "SIPR" || 
                                                    $mfType == "CNI" || $mfType == "CNIR" || $mfType == "UPLO" || $mfType == "UPLOR" || $mfType == "BON" || 
                                                    $mfType == "BONR") {
                                                $transactionType = "Purchase";
                                                $mfType = "PIP";
                                            } elseif($mfType == "IPO" || $mfType == "IPOR" || $mfType == "IPORR" || $mfType == "TRFI" || $mfType == "TRFIR") {
                                                $transactionType = "Purchase";
                                                $mfType = "IPO";
                                            } elseif($mfType == "SWI" || $mfType == "SI" || $mfType == "STPA" || $mfType == "LTIA" ||
                                                    $mfType == "STPI" || $mfType == "SWIN" || $mfType == "DSPI" || $mfType == "DTPIN" ||
                                                    $mfType == "LTIN" || $mfType == "STRA" || $mfType == "DSPIR" || $mfType == "LTIAR" ||
                                                    $mfType == "LTIARR" || $mfType == "LTINR" || $mfType == "STPAR" || $mfType == "SWIA" ||
                                                    $mfType == "TMI" || $mfType == "TRMI" || $mfType == "SWINR" || $mfType == "DSPA" || 
                                                    $mfType == "DSPN" || $mfType == "STPN" || $mfType == "STPNR" || $mfType == "STRAR" || 
                                                    $mfType == "STRI" || $mfType == "STRIR" || $mfType == "SWIAR" || $mfType == "TMIR" || 
                                                    $mfType == "TRMIR") {
                                                $transactionType = "Purchase";
                                                $mfType = "SWI";
                                            } elseif($mfType == "TIN" || $mfType == "TI" || $mfType == "OMTIN" || $mfType == "OMTINR") {
                                                $transactionType = "Purchase";
                                                $mfType = "TIN";
                                            } elseif($mfType == "SWO" || $mfType == "SO" || $mfType == "STPO" || $mfType == "LTOP" ||
                                                    $mfType == "DSPO" || $mfType == "LTOF" || $mfType == "STRO" || $mfType == "LTOFR" ||
                                                    $mfType == "SWOF" || $mfType == "TMO" || $mfType == "TRMO" || $mfType == "DSPOR" ||
                                                    $mfType == "SWOFR" || $mfType == "LTOPR" || $mfType == "STROR" || $mfType == "SWOP" || 
                                                    $mfType == "SWOPR" || $mfType == "STPOR" || $mfType == "TRMOR") {
                                                $transactionType = "Redemption";
                                                $mfType = "SWO";
                                            } elseif($mfType == "DP" || $mfType == "DIVIDEND PAYOUT" || $mfType == "DIVR" || $mfType == "DPR") {
                                                $transactionType = "Redemption";
                                                $mfType = "DP";
                                            } elseif($mfType == "RED" || $mfType == "FUL" || $mfType == "FULR" || $mfType == "FULRR" || $mfType == "REDR" ||
                                                $mfType == "R" || $mfType == "TO" || $mfType == "TOCOB" || $mfType == "CNO" || $mfType == "CNOR" || 
                                                $mfType == "SWD" || $mfType == "SWDR" || $mfType == "TRFO" || $mfType == "TRFOR" || $mfType == "TRMO" || 
                                                $mfType == "CFI" || $mfType == "CFIR" || $mfType == "DMT" || $mfType == "DMTR" || $mfType == "TRG" || 
                                                $mfType == "OMTOUT" || $mfType == "OMTOUTR") {
                                                $transactionType = "Redemption";
                                                $mfType = "RED";
                                            } else {
                                                //it might be CAMS file, which has trn_type with numbers, so check that
                                                if(strpos($mfType,"P") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "PIP";
                                                } elseif(strpos($mfType,"DR") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "DIV";
                                                } elseif(strpos($mfType,"SI") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "SWI";
                                                } elseif(strpos($mfType,"TI") === 0) {
                                                    $transactionType = "Purchase";
                                                    $mfType = "TIN";
                                                } elseif(strpos($mfType,"R") === 0 || strpos($mfType,"TO") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "RED";
                                                } elseif(strpos($mfType,"DP") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "DP";
                                                } elseif(strpos($mfType,"SO") === 0) {
                                                    $transactionType = "Redemption";
                                                    $mfType = "SWO";
                                                } else {
                                                    //maybe not CAMS file, Transaction Type is out-of-this-world, so show error
                                                    $transactionType = "Unknown";
                                                }
                                            }

                                            $where = array('mutual_fund_type' => $mfType, 'use_for' => $transactionType);
                                            $trn_details = $this->mf->get_mf_types_broker_dropdown($where);
                                            if(count($trn_details) == 0)
                                            {
                                                $mfMessage = 'Trn Type '.$mfType." doesn't exist for ".$transactionType;
                                                $insertRow = false;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Trn Type cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'DATE' || strtoupper($dataColumns[$countCell]) === 'TRADDATE' ||
                                        strtoupper($dataColumns[$countCell]) === 'TRXN_DATE' || strtoupper($dataColumns[$countCell]) === 'NAVDATE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRDT' || strtoupper($dataColumns[$countCell]) == 'TRANSACTION DATE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            //var_dump($cell);
                                            $date = DateTime::createFromFormat('m-d-y', $cell);
                                            if(is_object($date)) {
                                                $purDate = $date->format('Y-m-d');
                                            } else {
                                                //check if date is in string format YYYYMMDD
                                                $tempDate = trim($cell);
                                                if(strlen($tempDate) == 8) {
                                                    $tempDate = (string)$tempDate;
                                                    /*$year = substr($tempDate,0,4);
                                                    $month = substr($tempDate,4,2);
                                                    $day = substr($tempDate,6,2);*/
                                                    $date = DateTime::createFromFormat('Ymd', $tempDate);
                                                    if(is_object($date)) {
                                                        $purDate = $date->format('Y-m-d');
                                                    } else {
                                                        $insertRow = false;
                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                    }
                                                } else {
                                                    $insertRow = false;
                                                    $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                }
                                            }
                                            /*try {
                                                $date = new DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell)));
                                                $purDate = $date->format('Y-m-d');
                                            } catch(Exception $e) {
                                                $insertRow = false;
                                                $insMessage = "Date format is not proper";
                                            }*/
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Date cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'NAV' || strtoupper($dataColumns[$countCell]) === 'PURPRICE' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_POP' || strtoupper($dataColumns[$countCell]) === 'PRICE' || 
                                        strtoupper($dataColumns[$countCell]) === 'POP')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $nav = $cell;
                                        }
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "NAV cannot be empty";
                                            $nav = 0;
                                        }
                                    }
                                    /*else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $unit = $cell;
                                            //Check if we need to make unit negative (in case of Franklin file)
                                            if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR") && ($unit > 0)) {
                                                $unit = -$unit;
                                            }
                                        }
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "Unit cannot be empty";
                                            $unit = 0;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $amount = $cell;
                                            //Check if we need to make amount negative (in case of Franklin file)
                                            if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR") && ($amount > 0)) {
                                                $amount = -$amount;
                                            }
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Amount cannot be empty";
                                        }
                                    }*/
                                    else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                        strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $unit = floatval($cell);
                                            //Check if we need to make unit negative (in case of Franklin file)
                                            /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                                $unit = -$unit;
                                            }*/
                                        }
                                        else
                                        {
                                            //$insertRow = false;
                                            //$mfMessage = "Unit cannot be empty";
                                            $unit = 0;
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $amount = floatval($cell);
                                            //Check if we need to make amount negative (in case of Franklin file)
                                            /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($amount > 0)) {
                                                $amount = -$amount;
                                            }*/
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Amount cannot be empty";
                                        }
                                    }
                                    /*else if(strtoupper($dataColumns[$countCell]) === 'BAL UNIT')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $balUnit = $cell;
                                        }
                                        else
                                        {
                                            $balUnit = '0';
                                        }
                                    }*/
                                    else if(strtoupper($dataColumns[$countCell]) === 'DPO PER UNIT')
                                    {
                                        if($cell || $cell != '')
                                            $dpo = $cell;
                                        else
                                            $dpo = 0;

                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT')
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
                                    else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT REF NO')
                                    {
                                        if($cell || $cell != '')
                                            $adjRefNum = $cell;
                                        else
                                            $adjRefNum = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'AMC NAME' || strtoupper($dataColumns[$countCell]) === 'TD_FUND' || strtoupper($dataColumns[$countCell]) === 'FUND' || strtoupper($dataColumns[$countCell]) === 'AMC_CODE' || strtoupper($dataColumns[$countCell]) === 'COMP_CODE')
                                    {
                                        if($cell || $cell != '')
                                            $amc = trim($cell);
                                        else
                                            $amc = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'ARN' || strtoupper($dataColumns[$countCell]) === 'TD_AGENT' || strtoupper($dataColumns[$countCell]) === 'AGENT CODE' || strtoupper($dataColumns[$countCell]) === 'BROKCODE' || strtoupper($dataColumns[$countCell]) === 'BROK_CODE')
                                    {
                                        if($cell || $cell != '')
                                            $arn = trim($cell);
                                        else
                                            $arn = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'SUB ARN' || strtoupper($dataColumns[$countCell]) === 'TD_BROKER' || strtoupper($dataColumns[$countCell]) === 'SUB-BROKER CODE' || strtoupper($dataColumns[$countCell]) === 'SUBBROK' || strtoupper($dataColumns[$countCell]) === 'SUB_BROKE5')
                                    {
                                        if($cell || $cell != '')
                                            $sub_arn = trim($cell);
                                        else
                                            $sub_arn = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE NO' || strtoupper($dataColumns[$countCell]) === 'CHQNO' || strtoupper($dataColumns[$countCell]) === 'INSTRUMENT NUMBER' || strtoupper($dataColumns[$countCell]) === 'MICR_NO' || strtoupper($dataColumns[$countCell]) === 'CHECK_NO')
                                    {
                                        if($cell || $cell != '')
                                            $cheque_no = trim($cell);
                                        else
                                            $cheque_no = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE BANK' || strtoupper($dataColumns[$countCell]) === 'CHQBANK' || strtoupper($dataColumns[$countCell]) === 'INSTRUMENT BANK' || strtoupper($dataColumns[$countCell]) === 'BANK_NAME' || strtoupper($dataColumns[$countCell]) === 'PBANK_NAME')
                                    {
                                        if($cell || $cell != '')
                                            $cheque_bank = trim($cell);
                                        else
                                            $cheque_bank = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'BANK A/C NO' || strtoupper($dataColumns[$countCell]) === 'AC_NO' || strtoupper($dataColumns[$countCell]) === 'PERSONAL23')
                                    {
                                        if($cell || $cell != '')
                                            $account_no = trim($cell);
                                        else
                                            $account_no = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'REF NO' || strtoupper($dataColumns[$countCell]) === 'USRTRXNO')
                                    {
                                        if($cell || $cell != '')
                                            $ref_no = trim($cell);
                                        else
                                            $ref_no = "";
                                    }
					                else if(strtoupper($dataColumns[$countCell]) === 'REJ REF NO' || strtoupper($dataColumns[$countCell]) === 'REJTRNOOR2' || strtoupper($dataColumns[$countCell]) === 'REJTRNOORGNO' || 
					                 strtoupper($dataColumns[$countCell]) === 'TD_PTRNO')
                                    {
                                        if($cell || $cell != '')
                                            $rej_ref_no = trim($cell);
                                        else
                                            $rej_ref_no = "";
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) === 'TRXN MODE' || strtoupper($dataColumns[$countCell]) === 'TRXNMODE')
                                    {
                                        if($cell || $cell != '')
                                            $trxn_mode = trim($cell);
                                        else
                                            $trxn_mode = "";
                                    }
                                } else {
                                	if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $srNo = trim($cell);
                                        }
                                        else
                                        {
                                            $srNo = "";
                                            /*$insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";*/
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                        strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $panNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Pan Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $folioNum = $cell;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Folio Number cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' || strtoupper($dataColumns[$countCell]) == 'SCHEME CODE' ||
                                        strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                        strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $prodCode = trim($cell);
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Scheme/Product Code cannot be empty";
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                        strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                    {
                                        if($cell || $cell != '')
                                        {
                                            $mfType = trim(strtoupper($cell));
                                            $tempMFType = $mfType;
                                        }
                                        else
                                        {
                                            $insertRow = false;
                                            $mfMessage = "Trn Type cannot be empty";
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
                                $mf_data[$countErrRow][1] = $prodCode;
                                $mf_data[$countErrRow][2] = $folioNum;
                                $mf_data[$countErrRow][3] = $tempMFType;
                                $mf_data[$countErrRow][4] = $srNo;
                                $mf_data[$countErrRow][5] = $panNum;
                                $mf_data[$countErrRow][6] = $mfMessage;
                                $countErrRow++;
                                $insertRow = true;
                                $uploadedStatus = 2;
                                continue;
                            }

                            //check if trn_type was DIV, because Karvy has named DP as DIV, so we'll check its units
                            if($mfType == "DIV" && floatval($unit) == 0) {
                                //change type to DP
                                $mfType = "DP";
                                $transactionType = "Redemption";
                            }
                            
                            $pip_data = array();

                            if(strtoupper($mfType) == 'PIP' || strtoupper($mfType) == 'IPO')
                            {
                                /*$pip_data[$countTrans] = array(
                                    /*'action' => '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Add Bank Details"
                                        onclick="add_bank_details('."'".$clientId."', "."'".$folioNum."', "."'".$schemeId."', "."'".$mfType."'".')">
                                        <i class="fa fa-plus"></i> Add Bank Detail</a>',*/ /*
                                    'client_name' => $clientName, 'prod_code' => $prodCode, 'folio_number' => $folioNum, 'trans_type' => $mfType,
                                    'pan_no' => $panNum, 'sr_no' => $srNo
                                );
                                $countTrans++;*/
                            }

                            /*if(strtoupper($mfType) == 'DP' && floatval($dpo) == 0) {
                                $dpo = $amount/$unit;
                            }*/
                            
                            //Check if we need to make amount negative (in case of Franklin file)
                            if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                $unit = -($unit);
                                $amount = -($amount);
                            }

                            /*if($balUnit == '0' || empty($balUnit)) {
                                $balUnit = $tempMFType;
                            }
                            if(!empty($srNo) && empty($adjRefNum)) {
                                $adjRefNum = $srNo;
                            }*/
                            
                            /* in case of Karvy, and maybe FT, if ref_no is not available, use trxn_no($srNo) */
                            if($ref_no == "") {
                            	$ref_no = $srNo;
                            }
							
				// to check whether to add rej_ref_no in table or not
				if($unit < 0) {
					if($rej_ref_no != "") {
						$rej_ref_no = $rej_ref_no;
					} else {
						$rej_ref_no = $ref_no;
					}
				} else {
					$rej_ref_no = "";
				}


                            $dataRows[$countMF] = array(
                                'client_id' => $clientId, 'family_id' => $familyId,  'transaction_date' => date('Y-m-d'),
                                'mutual_fund_scheme' => $schemeId, 'mutual_fund_type' => $mfType, 'transaction_type' => $transactionType,
                                'folio_number' => $folioNum, 'purchase_date' => $purDate, 'quantity' => $unit, 'nav' => $nav, 'amount' => $amount,
                                'adjustment_flag' => $adj_flag, 'adjustment' => $adj, 'adjustment_ref_number' => $adjRefNum, 
                                'orig_trxn_no' => $srNo, 'orig_trxn_type' => $tempMFType, 'trxn_mode' => $trxn_mode, 
                                'ref_no' => $ref_no, 'rej_ref_no' => $rej_ref_no, 'cheque_number' => $cheque_no, 'bank_name' => $cheque_bank, 
                                'account_number' => $account_no, 'amc_name' => $amc, 'arn' => $arn, 'sub_arn' => $sub_arn, 'DPO_units' => $dpo, 
                                'user_id' => $user_id, 'broker_id' => $brokerID
                            );
                            //var_dump($dataRows[$countMF]);
                            $countMF++;

                            $today = new DateTime();
                            $interval = $date->diff($today);
                            $daydiff = $interval->format('%a');

                            if(strtoupper($mfType) === 'RED' && ($amount >= $rem_red_amt) && $daydiff <= 15)
                            {
                                //echo $rem_red_amt, '----', $amount,'<br/>';
                                $rem_message = 'Redemption Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                $countRem2 = array(
                                    'reminder_type'=>'MF Redemption',
                                    'client_id' => $clientId,
                                    'client_name' => $clientName,
                                    'reminder_date' => $purDate,
                                    'reminder_message' => $rem_message,
                                    'broker_id'=>$brokerID
                                );

                                $this->rem->add_reminder($countRem2);
                            }
                            else if(strtoupper($mfType) === 'DP' && ($amount >= $rem_dividend_amt) && $daydiff <= 15)
                            {
                                $rem_message = 'Dividend Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                $countRem2 = array(
                                    'reminder_type'=>'MF Dividend Payout',
                                    'client_id' => $clientId,
                                    'client_name' => $clientName,
                                    'reminder_date' => $purDate,
                                    'reminder_message' => $rem_message,
                                    'broker_id'=>$brokerID
                                );

                                $this->rem->add_reminder($countRem2);
                            }

                           /* $remRows[$countRem] = array(
                                'reminder_type'=>'MF Redemption',
                                'client_id' => $clientId,
                                'client_name' => $clientName,
                                'reminder_date' => $purDate,
                                'reminder_message' => $rem_message,
                                'broker_id'=>$brokerID
                            );*/

                            //$this->rem->add_reminder($remRows[$countRem]);

                            $countRem++;
                            /*var_dump($dataRows);
                            var_dump($remRows);*/

                            $folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                            $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                            $adj_flag = 0; $tempMFType = ""; 
							$amc = ""; $arn = ""; $sub_arn = ""; $cheque_no = ""; $cheque_bank = ""; $account_no = ""; $ref_no = ""; $rej_ref_no = ""; $trxn_mode = "";
                        }
                        if($uploadedStatus == 0)
                            break;
                        $countRow++;
                    }

                    /*get last transaction_id from mf_transactions*/
                    $trans = $this->mf->get_last_trans();
                    if(!($trans) || empty($trans) || empty($trans->transID)) {
                        $transID = 0;
                    } else {
                        $transID = ($trans->transID);
                        //var_dump($transID);
                    }

                    $valuation_done = true;

                    if($dataRows)
                    {
                        $countTrans = count($dataRows);
                        //var_dump($dataRows);
                        /*$result = $this->mf->get_mutual_funds(array('transaction_date' => date('Y-m-d'), 'mf.broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        if($result)
                        {
                            $this->mf->delete_mutual_fund(array('transaction_date' => date('Y-m-d'), 'broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        }*/

                        //we need to sort the data array before inserting - Salmaan - 27/10/2016
                        // Obtain a list of columns
                        foreach ($dataRows as $key => $row) {
                            $purchase_date[$key]  = $row['purchase_date'];
                            $trn_type[$key] = $row['transaction_type'];
                            $trxn_mode[$key] = $row['trxn_mode'];
                            $quantity[$key] = $row['quantity'];
                            $trn_no[$key] = $row['orig_trxn_no'];
                        }

                        // Sort the data with volume descending, edition ascending
                        array_multisort($purchase_date, SORT_ASC, $trn_type, SORT_ASC, $trxn_mode, SORT_ASC, $quantity, SORT_DESC, $trn_no, SORT_ASC, $dataRows);

                        $result = $this->mf->add_import_mutual_funds($dataRows, $transID);
                        if(is_array($result)) {
                            $valuation_done = false;
                            $uploadedStatus = 2;
                            $message = "Mutual Fund Details imported! Please click on the Valuation button to continue processing your records.";
                        } else {
                            $valuation_done = true;
                            //var_dump($result);
                            $this->common->last_import('Mutual Fund Details', $brokerID, $_FILES["import_mf"]["name"], $user_id);
                            $uploadedStatus = 1;
                            $message = "Mutual Fund Details Uploaded Successfully";
                        }
                    } else {
                        $countTrans = 0;
                    }
                    unset($dataColumns, $dataRows);
                } catch(Exception $e) {
                        var_dump($e);
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }



            if($uploadedStatus == 1)
            {
                //echo "<br/>Inside 1<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));

                $success = array(
                    "title" => "Success!",
                    "text" => $message
                );
                $this->session->set_userdata('success', $success);
				//call mf_valuation procedure
                /*if($countTrans > 9999) {
				    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => $transID
                    );
                }*/
            }
            else if ($uploadedStatus == 2)
            {
                //echo "<br/>Inside 2<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));
                $info = array(
                    "title" => "Info for Import!",
                    "text" => 'Few Records were not imported please check the table below'
                );
                $this->session->set_userdata('info', $info);
				//call mf_valuation procedure
				//$this->mf->mf_valuation(array("brokerID"=>$brokerID));
                /*if($valuation_done == false) {
                    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => $transID
                    );
                }*/
            }
            else
            {
                //echo "<br/>Inside else<br/>";
                $error = array(
                    "title" => "Error on uploading!",
                    "text" => $message
                );
                $this->session->set_userdata('error', $error);
            }
        }
        $this->mutual_fund_import($mf_data, $pip_data, $val_data);
    }

    //function to calculate MF Valuation of all clients of broker
    function mf_valuation() {
        if(!isset($_POST) || empty($_POST)) {
            alert("Access denied! You are trying to open a page that you shouldn't.");
        } else {
            if(!isset($_POST['brokerID'])) {
                echo "No Broker ID found";
                alert("Access denied! You are trying to open a page that you shouldn't. No Broker ID");
            } else {
                $brokerID = $this->input->post('brokerID');
                if(isset($_POST['transID']) && !empty($_POST['transID'])) {
                    $transID = $this->input->post('transID');
                } else {
                    $transID = 0;
                }
                
                $valuation = $this->mf->valuation_proc($brokerID, $transID);

                //check if valuation is complete
                if($valuation === true) {
                    $response = array(
                        'type' => 'success',
                        'title' => 'Mutual Fund Valuation completed!',
                        'text' => 'Valuation of Mutual Funds has been completed successfully'
                    );
                } else {
                    $valuation = $this->mf->valuation_proc($brokerID, $transID);
                    if($valuation) {
                        $response = array(
                            'type' => 'success',
                            'title' => 'Mutual Fund Valuation completed!',
                            'text' => 'Valuation of Mutual Funds has been completed successfully'
                        );
                    } else {
                        $response = array(
                            'type' => 'error',
                            'title' => 'Mutual Fund Valuation could not be completed!'.$valuation['message'].' - '.$valuation['code'],
                            'text' => 'Please contact support about this issue.'
                        );  
                        //var_dump($valuation);
                    }
                }

                echo json_encode($response);
            }
        }
    }
    
    

    //Reports
    function mf_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Mutual Funds Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css',
            'assets/users/plugins/charts-morrisjs/morris.css'
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
        $this->load->view('broker/mutual_fund/mf_report', $data);
        $this->load->view('broker/common/footer');
    }

    /*function get_mf_report()
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
        $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
        //var_dump($mf_rep[0]);
        $net_inv = $this->mf->get_net_investment($type, $where);
        $inv_summary = $this->mf->get_investment_summary($type, $where);
        $curr_val_summary = $this->mf->get_current_value_summary($type, $where);

        if(!empty($mf_rep) && !isset($mf_rep['code']))
        {
            unset($_SESSION['mf_report']);
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
            $mf_rep_array = array('mf_rep_data' => $mf_rep, 'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary);
            $this->session->set_userdata('mf_report', $mf_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }*/
    
    // Dipak - 2017-04-22 - New multiple reports
    /*function get_mf_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $report_type = $this->input->post('type');//Schemwisedetail, Clientwise detail,clientwise summamry,Foliowise  detail
        $client_summary=$this->input->post('client_summary');
        $typewise=$this->input->post('typewise');
        $scheme_summary=$this->input->post('scheme_summary');
        $net_investmet=$this->input->post('net_investmet');

        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID

            );
            $where1 = array(
                'familyID'=> '',
                'brokerID'=> $brokerID,
                'clientID'=> $client_id
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );

            $where1 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'clientID'=> ''
            );
        }
        $logo = "";
        $status = false;
        //Comman
        $mf_rep=''; $client_wise_summary=''; $mf_detail_schemewise=''; $folio_summary='';
        $net_inv = $this->mf->get_net_investment($type, $where);
        $inv_summary = $this->mf->get_investment_summary($type, $where);
        
        // for chart called function
        $curr_val_summary = $this->mf->get_current_value_summary($type, $where);
        
        $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
        // if($report_type == 'clientwise_detail')
        // {
        //     //Clientwise detail report
        //     $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
        // }
        if($report_type == 'clientwise_summary')
        {
          //Clientwise Summary report
          $client_wise_summary=$this->mf->get_clientwise_summary($type,$where1);
        }
         if($report_type == 'schemewise_detail')
        {
          //Schemewise Detail Report
          $mf_detail_schemewise = $this->mf->get_mutual_fund_family_detail_schemewise($type,$where1);
        }

        if($report_type == 'foilowise_summary')
        {
          //Foliowise summary
          $folio_summary=$this->mf->folio_wise_summary($type,$where1);

        }
        $mf_summary_client="";$mf_summary_typewise="";$mf_comman_scheme_summary="";$mf_summary_net_investment="";
          if($client_summary)
          {
                //Common client summary
                $mf_summary_client = $this->mf->get_mutual_fund_family_summary_portfolio($type,$where1);
            }

        if($typewise)
        {
        //Common typewise summary (Equity ,Debt,Banlanced)
          $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where1);
        }
        
        if($scheme_summary)
        {
          //Comman scheme summary
          $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where1);
        }

        if($net_investmet)
        {
          //Comman Net Ivestment(Purchase ,Redemption,Dividend Payout,Net Investment)
          $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where1);
        }

        if(!empty($mf_rep) && !isset($mf_rep['code']))
        {
            unset($_SESSION['mf_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type,'report_type_list'=>$report_type);
            $mf_rep_array = array('mf_rep_data' => $mf_rep, 'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary,'mf_summary_client'=>$mf_summary_client,'mf_summary_typewise'=>$mf_summary_typewise,'mf_detail_schemewise'=>$mf_detail_schemewise,'mf_summary_net_investment'=>$mf_summary_net_investment,'client_wise_summary'=>$client_wise_summary,'mf_comman_scheme_summary'=>$mf_comman_scheme_summary,'folio_summary'=>$folio_summary);
            $this->session->set_userdata('mf_report', $mf_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }*/
    
    //Dipak - 2017-05-26
    /*function get_mf_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $report_type = $this->input->post('type');//Schemwisedetail, Clientwise detail,clientwise summamry,Foliowise  detail,SIP
        $client_summary=$this->input->post('client_summary');
        $typewise=$this->input->post('typewise');
        $scheme_summary=$this->input->post('scheme_summary');
        $net_investmet=$this->input->post('net_investmet');

        // echo $client_summary;
        // echo $typewise;
        // echo $scheme_summary;
        // echo $net_investmet;
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID

            );
            $where1 = array(
                'familyID'=> '',
                'brokerID'=> $brokerID,
                'clientID'=> $client_id
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );

            $where1 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'clientID'=> ''
            );
        }
        $logo = "";
        $status = false;
        //Comman
        $mf_rep=''; $client_wise_summary=''; $mf_detail_schemewise=''; $folio_summary='';
        
        
        $net_inv = $this->mf->get_net_investment($type, $where);
        $inv_summary = $this->mf->get_investment_summary($type, $where);
        // for chart called function
        $curr_val_summary = $this->mf->get_current_value_summary($type, $where);
        // echo $main_type;
        // echo $report_type;
        //$mf_rep = $this->mf->get_mutual_fund_report($type, $where);
         /*if($report_type == 'clientwise_detail')
         {
             //Clientwise detail report*/
         /*    $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
         }*/
        /*if($report_type == 'clientwise_summary')
        {
          //Clientwise Summary report
          $client_wise_summary=$this->mf->get_clientwise_summary($type,$where1);
        }
         if($report_type == 'schemewise_detail')
        {
          //Schemewise Detail Report
          $mf_detail_schemewise = $this->mf->get_mutual_fund_family_detail_schemewise($type,$where1);
        }

        if($report_type == 'foilowise_summary')
        {
          //Foliowise summary
          $folio_summary=$this->mf->folio_wise_summary($type,$where1);

        }
        $mf_summary_client="";$mf_summary_typewise="";$mf_comman_scheme_summary="";$mf_summary_net_investment=""; $sip_rep="";
              if($client_summary)
              {
                    //Common client summary
                    $mf_summary_client = $this->mf->get_mutual_fund_family_summary_portfolio($type,$where1);
                }



        if($typewise)
        {
        //Common typewise summary (Equity ,Debt,Banlanced)
          $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where1);
        }
        if($scheme_summary)
        {
          //Comman scheme summary
          $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where1);
        }

        if($net_investmet)
        {
          //Comman Net Ivestment(Purchase ,Redemption,Dividend Payout,Net Investment)
          $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where1);
        }
        if($report_type == 'sip')
        {
          //SIP
          $sip_rep=$this->mf->get_sip_report($type,$where1);
          
          /*if(isset($sip_rep))
              $status=true;
           else
           $status=false;*/
           
          
        /*}

        $comman_cagr_abs_total = $this->mf->mf_comman_scheme_summary($type,$where1);
        if((!empty($mf_rep) && !isset($mf_rep['code'])) || (!empty($client_wise_summary) && !isset($client_wise_summary['code'])) || 
            (!empty($mf_detail_schemewise) && !isset($mf_detail_schemewise['code'])) || (!empty($folio_summary) && !isset($folio_summary['code'])) || 
            (!empty($sip_rep) && !isset($sip_rep['code'])))
        {
            unset($_SESSION['mf_report']);
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
            
            $rep_info = array('logo' => $logo, 'report_type' => $type,'report_type_list'=>$report_type);
            $mf_rep_array = array('mf_rep_data' => $mf_rep,'sip_rep' =>$sip_rep,'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary,'mf_summary_client'=>$mf_summary_client,'mf_summary_typewise'=>$mf_summary_typewise,'mf_detail_schemewise'=>$mf_detail_schemewise,'mf_summary_net_investment'=>$mf_summary_net_investment,'client_wise_summary'=>$client_wise_summary,'mf_comman_scheme_summary'=>$mf_comman_scheme_summary,'folio_summary'=>$folio_summary,'comman_cagr_abs_total'=>$comman_cagr_abs_total);
            $this->session->set_userdata('mf_report', $mf_rep_array);
            $status = true;
        }
        /*else if(!empty($sip_rep) && !isset($sip_rep['code']))
        {
            unset($_SESSION['mf_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type,'report_type_list'=>$report_type);
            $mf_rep_array = array('mf_rep_data' => $mf_rep,'sip_rep' =>$sip_rep,'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary,'mf_summary_client'=>$mf_summary_client,'mf_summary_typewise'=>$mf_summary_typewise,'mf_detail_schemewise'=>$mf_detail_schemewise,'mf_summary_net_investment'=>$mf_summary_net_investment,'client_wise_summary'=>$client_wise_summary,'mf_comman_scheme_summary'=>$mf_comman_scheme_summary,'folio_summary'=>$folio_summary,'comman_cagr_abs_total'=>$comman_cagr_abs_total);
            $this->session->set_userdata('mf_report', $mf_rep_array);
            $status = true;
        }
     */
       /* echo json_encode(array('Status'=> $status));
    }*/
    
    
    //Dipak - 2017-06-12
    function get_mf_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $report_type = $this->input->post('type');//Schemwisedetail, Clientwise detail,clientwise summamry,Foliowise  detail,SIP,Capitalgain
        $client_summary=$this->input->post('client_summary');
        $typewise=$this->input->post('typewise');
        $scheme_summary=$this->input->post('scheme_summary');
        $net_investmet=$this->input->post('net_investmet');

        $nav_date_temp = DateTime::createFromFormat('d/m/Y',$this->input->post('nav_date'));
        if(is_object($nav_date_temp))
        {
           $nav_date = $nav_date_temp->format('Y/m/d');
        }
        

        // $from_date=$this->input->post('from_date');
        // $to_date=$this->input->post('to_date');

        if($this->input->post('to_date')!='')
        {
            $to_date_temp = DateTime::createFromFormat('d/m/Y',$this->input->post('to_date'));
             if(is_object($to_date_temp))
             {
               $to_date = $to_date_temp->format('Y/m/d');
              }
        }
        if($this->input->post('from_date')!='')
        {
            $from_date_temp = DateTime::createFromFormat('d/m/Y', $this->input->post('from_date') );
            if(is_object($from_date_temp))
            {
               $from_date = $from_date_temp->format('Y/m/d');
             }
        } 

        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID

            );
            $where1 = array(
                'familyID'=> '',
                'brokerID'=> $brokerID,
                'clientID'=> $client_id
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );

            $where1 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                  'clientID'=> ''
            );
        }
        $logo = "";
        $status = false;
        //Comman
        $mf_rep=''; $client_wise_summary=''; $mf_detail_schemewise=''; $folio_summary='';$capital_gain='';$sip_rep='';$folio_master_data='';
        $net_inv = $this->mf->get_net_investment($type, $where);
        $inv_summary = $this->mf->get_investment_summary($type, $where);
        // for chart called function
        $curr_val_summary = $this->mf->get_current_value_summary($type, $where);
        // echo $main_type;
        // echo $report_type;
      //  $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
         if($report_type == 'clientwise_detail')
         {
             //Clientwise detail report
             $mf_rep = $this->mf->get_mutual_fund_report($type, $where);
         }
        if($report_type == 'clientwise_summary')
        {
          //Clientwise Summary report
          $client_wise_summary=$this->mf->get_clientwise_summary($type,$where1);
        }
         if($report_type == 'schemewise_detail')
        {
          //Schemewise Detail Report
          $mf_detail_schemewise = $this->mf->get_mutual_fund_family_detail_schemewise($type,$where1);
        }

        if($report_type == 'foilowise_summary')
        {
          //Foliowise summary
          $folio_summary=$this->mf->folio_wise_summary($type,$where1);

        }
        if($report_type == 'sip')
        {
          //SIP
          $sip_rep=$this->mf->get_sip_report($type,$where1);
          // print_r($sip_rep);
        }
        if($report_type == 'folio_master')
        {
          $folio_master_data=$this->mf->follio_master($type,$where1);
        }
        if($report_type == 'capital_gain')
        {
          if($client_id != null && $client_id != '')
          {
            $where_con = array(
              'brokerID'=> $brokerID,
              'familyID'=> '',
              'clientID'=>$client_id[0],
              'startDate'=>$from_date,
              'endDate'=>$to_date );
            }
            else {
              $where_con = array(
                                'brokerID'=>$brokerID,
                                'familyID'=>$family_id[0],
                                'clientID'=>'',
                                'startDate'=>$from_date,
                                'endDate'=>$to_date);
                  }

          $capital_gain=$this->mf->capital_gain($type,$where_con);
          // print_r($capital_gain);

        }
        $mf_summary_client="";$mf_summary_typewise="";$mf_comman_scheme_summary="";$mf_summary_net_investment="";
        if($client_summary)
        {
              //Common client summary
            $mf_summary_client = $this->mf->get_mutual_fund_family_summary_portfolio($type,$where1);
        }
        if($typewise)
        {
        //Common typewise summary (Equity ,Debt,Banlanced)
          $mf_summary_typewise = $this->mf->get_mutual_fund_family_summary_typewise($type,$where1);

        }
        // print_r($mf_summary_typewise);
        if($scheme_summary)
        {
          //Comman scheme summary
          $mf_comman_scheme_summary = $this->mf->mf_comman_scheme_summary($type,$where1);
        }

        if($net_investmet)
        {
          //Comman Net Ivestment(Purchase ,Redemption,Dividend Payout,Net Investment)
          $mf_summary_net_investment = $this->mf->get_mutual_fund_family_summary_schemewise_net_investment($type,$where1);
        }
        
        // print_r($folio_master_data);
        $comman_cagr_abs_total = $this->mf->mf_comman_scheme_summary($type,$where1);
        if(!empty($mf_rep) && !isset($mf_rep['code'])  || !empty($client_wise_summary) && !isset($client_wise_summary['code']) ||
              !empty($mf_detail_schemewise) && !isset($mf_detail_schemewise['code']) || !empty($folio_summary) && !isset($folio_summary['code']) ||
              !empty($sip_rep) && !isset($sip_rep['code'])   || !empty($capital_gain) && !isset($capital_gain['code'])   ||
              !empty($folio_master_data) && !isset($folio_master_data['code']))

        {
            unset($_SESSION['mf_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type,'report_type_list'=>$report_type);
            $mf_rep_array = array('mf_rep_data' => $mf_rep,'folio_master_data'=>$folio_master_data,'sip_rep'=>$sip_rep,'capital_gain'=>$capital_gain,'net_inv_data' => $net_inv, 'report_info'=>$rep_info, 'inv_sum' => $inv_summary, 'cur_val_sum' => $curr_val_summary,'mf_summary_client'=>$mf_summary_client,'mf_summary_typewise'=>$mf_summary_typewise,'mf_detail_schemewise'=>$mf_detail_schemewise,'mf_summary_net_investment'=>$mf_summary_net_investment,'client_wise_summary'=>$client_wise_summary,'mf_comman_scheme_summary'=>$mf_comman_scheme_summary,'folio_summary'=>$folio_summary,'comman_cagr_abs_total'=>$comman_cagr_abs_total);
            $this->session->set_userdata('mf_report', $mf_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }
    
    // Dipak new report 06 June 2017
    function get_mf_broker_report()
    {
    
      $type = $this->input->post('report_type');
      $brokerID = $this->session->userdata('broker_id');
      $mf_sip_report='';
      $mf_aum_report='';
      if($type=='sip_report')
      {
        $mf_sip_report=$this->mf->get_broker_sip_report(array('broker_id' =>$brokerID));
        $month=$mf_sip_report[0]->monthly;
        $quarterly=$mf_sip_report[0]->quarterly;
        $half_yr=$mf_sip_report[0]->half_yearly;
        $yearly=$mf_sip_report[0]->yearly;
      }
      else {
    
           $mf_aum_report = $this->mf->get_aum_report(array('broker_id' =>$brokerID));
           
      }
        $status =false;
     $logo='';
     
    
    //if(!empty($mf_aum_report) || !empty($mf_sip_report)   )
    if(!empty($mf_aum_report) ||  (!empty($month) || !empty($quarterly) || !empty($half_yr) || !empty($yearly)) )
    {
          unset($_SESSION['mf_aum_report']);
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
          $rep_info = array('logo' => $logo,'type'=>$type);
          $mf_rep_array = array('mf_aum_report' => $mf_aum_report,'rep_info'=>$rep_info,'mf_sip_report'=>$mf_sip_report);
          $this->session->set_userdata('mf_aum_report', $mf_rep_array);
          $status = true;
      }
          
      echo json_encode(array('Status'=>$status ));
    }
    

    ///functions for NAV Import
    function nav_import($nav_data = null)
    {
        $header['title'] = 'Mutual Fund NAV Import';
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
        $data['import_data'] = $nav_data;
        $this->load->view('broker/mutual_fund/nav_import', $data);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    function nav_import_file()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $uploadedStatus = 0;
        $message = ""; $mfMessage = ""; $insertRow = true;
        $nav_data = array();
        if (isset($_POST["Import"]))
        {
            if (isset($_FILES["import_nav"]))
            {
                //if there was an error uploading the file
                if ($_FILES["import_nav"]["name"] == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    //get tmp_name of file
                    $file = $_FILES["import_nav"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                    $isin = ""; $nav = ""; $isin2 = "";
                    $scheme_id = ""; $scheme_id2 = "";
                    $scheme_arr = array(); $scheme_arr2 = array();
                    //$scheme_date = date('Y-m-d',strtotime("-1 days")); //if NAV is updated in the morning
                    //$scheme_date = date('Y-m-d'); //if NAV is updated at night
                    $current_time = date('H');
                    //echo $current_time.' -- ';
                    if(intval($current_time) >= 21) {
                    	$scheme_date = date('Y-m-d'); //if NAV is updated at night
                    } else {
                    	$scheme_date = date('Y-m-d',strtotime("-1 days")); //if NAV is updated in the morning
                    }
                    //echo $scheme_date;
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    $dataRows = array();
                    $countRow = 0; $countErrRow = 0; $countNav = 0;
                    $maxCellCount = -1;

                    foreach($excelData as $rows)
                    {
                        $countCell = 0;
                        foreach($rows as $cell)
                        {
                            if($countRow == 0)
                            {
                                $cell = str_replace(array('.'), '', $cell);
                                if(strtoupper($cell) == 'ISIN' || strtoupper($cell) == 'ISIN-DIVR' || strtoupper($cell) == 'NAV' || strtoupper($cell) == 'DATE')
                                /*if(strtoupper($cell) == 'ISIN' || strtoupper($cell) == 'ISIN-DIVR' || strtoupper($cell) == 'NAV')*/
                                {
                                    $dataColumns[$countCell] = $cell;
                                    $countCell++;
                                    $maxCellCount++;
                                    $uploadedStatus = 2;
                                    continue;
                                }
                                else
                                {
                                    if(empty($cell)) {
                                        //$dataColumns[$countCell] = '';
                                        $countCell--;
                                        continue;
                                    }
                                    $message = 'Column/s specified in Excel is not in correct format';
                                    $uploadedStatus = 0;
                                    break;
                                }
                            }
                            else
                            {
                                if($countCell <= $maxCellCount) {
                                    //if($insertRow)
                                    //{
                                        if(strtoupper($dataColumns[$countCell]) == 'ISIN')
                                        {
                                            if($cell && $cell != '')
                                            {
                                                $isin = trim($cell);

                                                if(strlen($isin) > 5) {
	                                                //checks if Scheme Name exists in mutual_fund_schemes table
	                                                $scheme_info = $this->mf->check_scheme_isin_exists($isin);
	                                                if(count($scheme_info) == 0)
	                                                {
	                                                    /*//no scheme name found, so insert it into schemes table and get its id
	                                                    $schemeData = array(
	                                                        'scheme_name' => $isin,
	                                                        'scheme_status' => 1,
	                                                        'added_on' => date('Y-m-d')
	                                                    );
	                                                    //below code shifted in next part, of prod_code/scheme_code
	                                                    /*$schemeInserted = $this->mf->add_scheme($schemeData);
	                                                    if($schemeInserted && !is_array($schemeInserted)) {
	                                                        $scheme_id = $schemeInserted;
	                                                    } else {*/
	                                                        $insertRow = false;
	                                                        $navMessage = "Scheme ISIN does not exist.";
	                                                        $scheme_id = ""; $scheme_arr = array();
	                                                    /*}*/
	                                                }
	                                                elseif(count($scheme_info) > 1)
	                                                {
	                                                    foreach($scheme_info as $s) {
	                                                        $scheme_arr[] = $s->scheme_id;
	                                                    }
	                                                }
	                                                else
	                                                {
	                                                    $scheme_id = $scheme_info[0]->scheme_id;
	                                                }
	                                        }
                                            }
                                            else
                                            {
                                                //$insertRow = false;
                                                //$navMessage = "Scheme ISIN cannot be empty";
                                                $scheme_id = ""; $scheme_arr = array();
                                            }
                                        }
                                        else if(strtoupper($dataColumns[$countCell]) == 'ISIN-DIVR')
                                        {
                                            if($cell || $cell != '')
                                            {
                                                /*$prodCode = trim($cell);
                                                $whereScheme = 'prod_code = "'.$prodCode.'" AND scheme_status = 1';
                                                $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                                if(count($scheme_details) == 0)
                                                {
                                                    $mfMessage = 'Product Code (scheme) '.$prodCode." Doesn't Exists or is Inactive";
                                                    $insertRow = false;
                                                }
                                                else
                                                {
                                                    $schemeId = $scheme_details[0]->scheme_id;
                                                }*/
                                                $isin2 = trim($cell);
                                                
                                                if(strlen($isin2) > 5) {
	                                                //checks if Scheme Name exists in mutual_fund_schemes table
	                                                $scheme_info2 = $this->mf->check_scheme_isin_exists($isin2);
	                                                if(count($scheme_info2) == 0)
	                                                {
	                                                    /*//no scheme name found, so insert it into schemes table and get its id
	                                                    $schemeData = array(
	                                                        'scheme_name' => $scheme_name,
	                                                        'prod_code' => $scheme_code,
	                                                        'scheme_status' => 1,
	                                                        'added_on' => date('Y-m-d')
	                                                    );
	                                                    $schemeInserted = $this->mf->add_scheme($schemeData);
	                                                    if($schemeInserted && !is_array($schemeInserted)) {
	                                                        $scheme_id = $schemeInserted;
	                                                    } else {*/
	                                                        $insertRow = false;
	                                                        $navMessage = "Scheme ISIN-DIVR does not exist.";
	                                                    /*}*/
	                                                }
	                                                elseif(count($scheme_info2) > 1)
	                                                {
	                                                    foreach($scheme_info2 as $s2) {
	                                                        $scheme_arr2[] = $s2->scheme_id;
	                                                    }
	                                                }
	                                                else
	                                                {
	                                                    $scheme_id2 = $scheme_info2[0]->scheme_id;
	                                                }
	                                          } else {
	                                          	$isin2 = "";
	                                          }
                                            }
                                            else
                                            {
                                            	$isin2 = "";
                                            }
                                        }
                                        else if(strtoupper($dataColumns[$countCell]) == 'NAV')
                                        {
                                            if($cell && $cell != '')
                                            {
                                                $nav = $cell;
                                            }
                                            else
                                            {
                                                $insertRow = false;
                                                $navMessage = "NAV cannot be empty";
                                            }
                                        }
                                        else if(strtoupper($dataColumns[$countCell]) == 'DATE')
                                        {
                                            if($cell && $cell != '')
                                            {
                                                $nav = $cell;
                                            }
                                            else
                                            {
                                                $insertRow = false;
                                                $navMessage = "NAV cannot be empty";
                                            }
                                            if($cell || $cell != '')
                                            {
                                                $date = DateTime::createFromFormat('m-d-y', $cell);
                                           	if(is_object($date)) {
                                                    $scheme_date = $date->format('Y-m-d');
                                                } else {
	                                            //check if date is in string format d/m/Y
	                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                    if(is_object($date)) {
                                                        $scheme_date = $date->format('Y-m-d');
                                                    } else {
                                                        $insertRow = false;
                                                        $impMessage = "Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                    }
                                                }
                                             }
                                              else
                                              {
                                                $insertRow = false;
                                                $navMessage = "Date cannot be empty";
                                              }
                                        }
                                        /*else if(strtoupper($dataColumns[$countCell]) == 'SCHEME TYPE')
                                        {
                                            $scheme_type = trim($cell);
                                            /*if($cell && !empty($cell))
                                            {
                                                $scheme_type = trim($cell);
                                                $whereSchemeType = array('scheme_type' => $scheme_type);
                                                //check if Scheme Type exists in mf_scheme_type table or not
                                                $scheme_type_info = $this->mf->get_mf_scheme_types_dropdown($whereSchemeType);
                                                //var_dump($cell);
                                                //var_dump($scheme_type_info);
                                                if(count($scheme_type_info) == 0) {
                                                    //no scheme type found
                                                    $insertRow = false;
                                                    $navMessage = "Scheme Type not found in database. Please check if the spelling is correct";
                                                } else {
                                                    $scheme_type_id = $scheme_type_info[0]->scheme_type_id;
                                                }
                                            }
                                            else
                                            {
                                                if(empty($cell)) {
                                                    $insertRow = true;
                                                    $scheme_type_id = null;
                                                } else {
                                                    $insertRow = false;
                                                    $navMessage = "Scheme Type error";
                                                }
                                            }*/
                                            /*$scheme_type_id = "";
                                        }*/
                                    //}
                                }
                                $countCell++;
                            }
                        }
                        if($countRow != 0)
                        {
                            if(!$insertRow)
                            {
                            	$nav_data[$countErrRow][1] = $isin;
                                $nav_data[$countErrRow][2] = $isin2;
                                $nav_data[$countErrRow][3] = $nav;
                                $nav_data[$countErrRow][4] = $navMessage;
                                $countErrRow++;
                                $countCol = 0;
                                $insertRow = true;
                                $uploadedStatus = 2;
                                if(!empty($scheme_id) || !empty($scheme_arr) || !empty($scheme_id2) || !empty($scheme_arr2)) {
                                    //do nothing, proceed to adding record
                                } else {
                                    //we don't need to add any record, so continue to next row
                                    $isin = ""; $scheme_id = ""; $isin2 = ""; $scheme_id2 = ""; $nav = "";
                            		$scheme_arr = array(); $scheme_arr2 = array();
                                    continue;
                                }
                            }

                            if(!empty($scheme_arr)) {
                                for($i=0; $i<count($scheme_arr); $i++) {
                                    $dataRows[$countNav] = array(
                                        'scheme_id' => $scheme_arr[$i],
                                        'current_nav' => $nav,
                                        //'scheme_type_id' => $scheme_type_id,
                                        'scheme_date' => $scheme_date
                                    );
                                    $countNav++;
                                }
                            }
                            if(!empty($scheme_arr2)) {
                                for($i=0; $i<count($scheme_arr2); $i++) {
                                    $dataRows[$countNav] = array(
                                        'scheme_id' => $scheme_arr2[$i],
                                        'current_nav' => $nav,
                                        //'scheme_type_id' => $scheme_type_id,
                                        'scheme_date' => $scheme_date
                                    );
                                    $countNav++;
                                }
                            }

                            if(isset($scheme_id) && !empty($scheme_id)) {
                                $dataRows[$countNav] = array(
                                    'scheme_id' => $scheme_id,
                                    'current_nav' => $nav,
                                    //'scheme_type_id' => $scheme_type_id,
                                    'scheme_date' => $scheme_date
                                );
                                $countNav++;
                            }
                            //check if there is a different ISIN for DIVR
                            if(isset($scheme_id2) && !empty($scheme_id2)) {
                                $dataRows[$countNav] = array(
                                    'scheme_id' => $scheme_id2,
                                    'current_nav' => $nav,
                                    //'scheme_type_id' => $scheme_type_id,
                                    'scheme_date' => $scheme_date
                                );
				$countNav++;
                            }
                            $isin = ""; $scheme_id = ""; $isin2 = ""; $scheme_id2 = ""; $nav = "";
                            $scheme_arr = array(); $scheme_arr2 = array();
                        }
                        if($uploadedStatus == 0)
                            break;
                        $countRow++;
                    }
                    if($dataRows)
                    {
                        /*echo '<pre>';
                        print_r($dataRows);
                        echo '</pre>';*/
                        //var_dump($dataRows);
                        $todayCondition = array('scheme_date' => $scheme_date);
                        //check if data exists in table for today's date
                        $todayDataExists = $this->mf->check_mf_scheme_hist_today($todayCondition);
                        if($todayDataExists) {
                            //data for today's date exists, so delete the existing data
                            //var_dump('inside');
                            $this->mf->delete_mf_scheme_hist_today($todayCondition);
                        }
                        //insert all data of schemes Nav
                        $imported = $this->mf->addImportNAV($dataRows);
                        if($imported && !is_array($imported)) {
                            $this->common->last_import('Mutual Fund NAV', $brokerID, $_FILES["import_nav"]["name"], $user_id);
                            $uploadedStatus = 1;
                            $message = "Mutual Fund NAVs Uploaded Successfully";
                        } else {
                            $uploadedStatus = 0;
                            $message = "Mutual Fund NAVs not uploaded. Could not add NAV records in database.";
                        }

                    }
                    unset($dataColumns, $dataRows);
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
        $this->nav_import($nav_data);
    }
    
    //Akshay Karde - 2017-05-26
    function export_to_pdf_sip()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
	//error_reporting(E_ERROR);
	//ini_set('error_reporting', E_ERROR);
	//error_reporting(0);

        //echo html_entity_decode($_POST['htmlData']);
        if(!(isset($_POST['htmlData']) && !empty($_POST['htmlData']))) {
            echo "<script type='text/javascript'>
                alert('Unauthorized Access. Get Outta Here!');
                window.top.close();  //close the current tab
              </script>";

        } else {
            $css_data = '<style type="text/css">
                table { width:100%; border:0px solid #fff; }
                table td {font-size: 9px; padding:0px; color:#4d4d4d;}
                .amount { text-align:right; text-indent: 3px; }
                .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                table th { font-size: 9px; padding:0px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse; }
                .noWrap { white-space: nowrap; }
                .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:14px; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 10px; text-align: center; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .normal {font-weight: normal;}
                .no-border {border-width: 0px; border-color: #fff;}
                .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                .client-name2 { text-align: left; font-size: 14px; font-weight: bold; }
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
            $pdf->SetKeywords('mutual fund, report');

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

            //Close and output PDF document
            $pdf->Output($reportName.'.pdf', 'D');
            //$pdf->Output('Equity Portfolio.pdf', 'I');

            //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
        }
    }

    function export_to_pdf()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
	//error_reporting(E_ERROR);
	//ini_set('error_reporting', E_ERROR);
	//error_reporting(0);
        
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
                .info { font-size: 10px; text-align: center; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .normal {font-weight: normal;}
                .no-border {border-width: 0px; border-color: #fff;}
                .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                .client-name2 { text-align: left; font-size: 14px; font-weight: bold; }
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
            $pdf->SetKeywords('mutual fund, report');

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

            //Close and output PDF document
            $pdf->Output($reportName.'.pdf', 'D');
            //$pdf->Output('Equity Portfolio.pdf', 'I');

            //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
        }
    }

    function export_to_excel()
    {
        ini_set('max_execution_time', 0);
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
            header('Content-Disposition: attachment;filename=Mutual Fund Portfolio.xlsx'); // specify the download file name
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
