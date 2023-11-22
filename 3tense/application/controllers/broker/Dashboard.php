<?php
//error_reporting(0);
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller {
    function __construct()
    {
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('html');
        //load adminDashboard model
        $this->load->model('Dashboard_model', 'dash');
        $this->load->model('Equity_model', 'eq');
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Clients_model', 'clients');
        $this->load->model('Assets_liabilities_model', 'al');
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Funds_model', 'fund');
        $this->load->model('Insurance_model', 'ins');
        $this->load->model('Fixed_deposits_model', 'fd');
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
  }

    function index()
    {
        $this->get_reminder_notify();
        $header['js'] = array(
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.orderBars.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            //'assets/users/plugins/charts-chartjs/Chart.min.js',
            //'assets/users/demo/demo-chartjs.js',
        );
        $header['css'] = array(
            'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
            'assets/users/plugins/charts-morrisjs/morris.css',
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
         $header['title']='Dashboard';
          $data['get_info'] = $this->get_info();
          $data['mf_pur_list']=$this->ajax_list_purchase();
          $data['mf_redm_list']=$this->ajax_list_redemption();
          $data['ins_new_list']=$this->ajax_ins_list_new();
          $data['ins_mat_list']=$this->ajax_ins_list_mat(); 
          $data['fd_new_list']=$this->ajax_list_top_new();
          $data['fd_mat_list']=$this->ajax_list_top_mat();
          $data['al_new_list']=$this->asset_ajax_list_top();
          $data['al_mat_list']=$this->asset_ajax_list_mat();
          $data['eq_fund_added']=$this->ajax_list_new();
          $data['eq_fund_withdraw']=$this->ajax_list_equity();
          $data['high_neg_balance']=$this->get_negative_equity();
          $data['ins_data']=$this->get_dash_chart();
          $data['sip_book_chart']=$this->get_sip_book_chart();
         $brokerID = $this->session->userdata('broker_id');
        $data['pie_chart']=$this->dash->get_pie_chart_data($brokerID);
        $data['brokerId']=$brokerID;
        
        //print_r($data);
        //print_r($data['pie_chart']);
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/user/dashboard', $data);
        $this->load->view('broker/common/footer');

        //var_dump($this->session->userdata('permissions'));
    }
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
        return $output;
    }

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
        return $output;
    }
    public function ajax_list_new()
    {
        $permissions=$this->session->userdata('permissions');
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fund->get_add_funds_new("af.broker_id='".$brokerID."' and af.shares_app=1");
        //echo $list;

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
            $row['broker_name'] = $fund->trading_broker_name;
            $row['client_code'] = $fund->client_code;
            $row['add_notes'] = $fund->add_notes;


            if($permissions == "3")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_add_fund('."'".$fund->add_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_add_fund('."'".$fund->add_fund_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
            }
            if($permissions == "2")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_add_fund('."'".$fund->add_fund_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn disable_btn">
                <i class="fa fa-trash-o"></i></a>';   ;
            }
            if($permissions == "1")
            {
                $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
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
        return $output;
    }

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
       return $output;
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
       return $output;
   }
    public function ajax_list_top_new()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID,'adv.held_type' => 'Held'));
        //$list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID));
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
            //"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
            //"recordsFiltered"=>$this->fd->count_filtered(),
            "data"=>$data
        );
        //output to json format
        return $output;
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
            //"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
            //"recordsFiltered"=>$this->fd->count_filtered(),
            "data"=>$data
        );
        //output to json format
         return $output;
    }

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
         return $output;
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
        return $output;
    }


    public function ajax_list_purchase()
    {
        $brokerID = $this->session->userdata('broker_id');
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
            //"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            //"recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$data
        );
        //output to json format
          return $output;
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
            //"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
            //"recordsFiltered"=>$this->mf->count_filtered(),
            "data"=>$data
        );
        //output to json format
        return $output;
    }


    function get_reminder_notify()
       {
           $brokerID = $this->session->userdata('broker_id');
           $userName = $this->session->userdata('username');

           $condition = 'reminder_date = "'.date('Y-m-d').'" AND (reminder_type = "Personal" OR reminder_type = "Client")
                       AND broker_id = "'.$brokerID.'" AND
                       (concern_user = "'.$userName.'" OR concern_user = "all")';
           $rem_data = $this->rem->dash_reminder_list($condition, 50);
           $count_rem = count($rem_data);
           $header_data['reminder'] = $rem_data;
           $header_data['count_reminder'] = $count_rem;
           $this->session->set_userdata('header', $header_data);
       }



    function update_notif() {
        $count_reminder = 0;
        $reminder = null;
        if(isset($this->session->userdata['header']))
        {
            $count_reminder = $this->session->userdata['header']['count_reminder'];
            $reminder = $this->session->userdata['header']['reminder'];

            $html = '<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i>';
            !empty($count_reminder)?$html .= '<span class="badge">'.$count_reminder.'</span>':$html .= '';
            $html .= '</a>
            <ul class="dropdown-menu notifications arrow">
                <li class="dd-header">
                    <div style="float: left;">
                        <button type="button" id="addRemBtn1" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
                    </div>
                    <span>You have <b>'.$count_reminder.'</b> Personal reminder(s)</span>
                </li>
                <div class="scrollthis">';

                foreach($reminder as $rem) {
                    $html .= '<li>
                        <!--<span class="time">4 mins</span>-->';
                    if($rem->reminder_type == 'Personal') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                            <i class="fa fa-eye"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    }elseif($rem->reminder_type == 'Client') {
                          $html .= '<a href="javascript:void(0)" onclick="Client_view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                              <i class="fa fa-eye"></i>
                              <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                              <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                              <br/>
                              <span class="msg">'.$rem->reminder_message.'</span>
                          </a>';
                      }
                     elseif($rem->reminder_type == 'Premium Due' || $rem->reminder_type == 'Grace Date' || $rem->reminder_type == 'Insurance Maturity') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
                            <i class="fa fa-eye"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    } elseif($rem->reminder_type == 'Birthday Reminder' || $rem->reminder_type == 'Anniversary Reminder') {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-success active">
                            <i class="fa fa-gift"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    } else {
                        $html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-danger">
                            <i class="fa fa-crosshairs"></i>
                            <span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
                            <span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
                            <br/>
                            <span class="msg">'.$rem->reminder_message.'</span>
                        </a>';
                    }
                    $html .= '</li>';
                }
                $html .= '</div>
                <li class="dd-footer"><a href="'.base_url('broker/Reminders').'">View All Reminders Upto Today</a></li>
            </ul>';
        } else {
            $html = '<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i></a>
            <ul class="dropdown-menu notifications arrow">
                <li class="dd-header">
                    <div style="float: left;">
                        <button type="button" id="addRemBtn" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
                    </div>
                    <span>You have <b>0</b> Personal reminder(s)</span>
                </li>
                <div class="scrollthis">Nothing to show...</div>';
        }

        echo $html; die();
        
    }

    function get_info()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data['num_clients'] = $this->clients->count_all($brokerID);
        $data['num_ins'] = $this->ins->count_all(array('fam.broker_id' => $brokerID, 'pstat.status'=>'Matured'));
        $data['num_ins_lapsed'] = $this->ins->count_all(array('fam.broker_id' => $brokerID, 'pstat.status'=>'Lapsed'));
        $data['num_fd'] = $this->fd->count_all(array('fdt.broker_id' => $brokerID, 'fdt.status'=>'Active'));
        $data['rem_list'] = $this->rem->dash_reminder_list(array('reminder_date <= ' => date('Y-m-d'),'broker_id' => $brokerID), 5);

        //get accordions data - Salmaan
        //$data['acc']['mf']['pur'] = $this->dash->get_top_mf("mft.broker_id = $brokerID AND mft.mutual_fund_type IN ('PIP','IPO')");
        //$data['acc']['mf']['red'] = $this->dash->get_top_mf("mft.broker_id = $brokerID AND mft.mutual_fund_type IN ('RED')");
        //var_dump($data['acc']['mf']);
        return $data;
    }

    function get_dash_chart()
    {
        $brokerID = $this->session->userdata('broker_id');
        $ins_result = $this->dash->get_insurance_dashboard(array('broker_id' => $brokerID));
        $ins_data = array();
        $count = 0;
        foreach($ins_result as $item)
        {
            $ins_data[$count] = array(
                'label' => $item->status,
                'value' => $item->sum_policy
            );
            $count++;
        }
          //print_r($ins_data);
          // print_r($ins_inv_result);
          // print_r($fd_inv_result);
          return $ins_data;
         //echo json_encode(array('ins' => $ins_data, 'com'=>$com_data, 'ins_inv' => $ins_inv_result, 'fd_inv'=>$fd_inv_result));
    }
     function get_sip_book_chart()
    {
        $brokerID = $this->session->userdata('broker_id');
        $ins_result = $this->dash->get_sip_book_dashboard(array('broker_id' => $brokerID));
          return $ins_result;
     
    }
  }
