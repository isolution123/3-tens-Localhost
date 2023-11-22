<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reports extends CI_Controller{

    function __construct()
    {
        parent:: __construct();
        //load library, helperget_equity_report
        $this->load->library('session');
        $this->load->library('Common_lib');
        $this->load->helper('url');
    }

    function get_ins_report()
    {
        $data['page_title']='Insurance Policy Report';
        $data['ins_rep_data'] = $_SESSION['ins_report']['ins_rep_data'];
        $data['report_info'] = $_SESSION['ins_report']['report_info'];
        $data['gen_ins_data'] = $_SESSION['ins_report']['gen_ins_data'];
        $data['logo'] = $_SESSION['ins_report']['report_info']['logo'];
        $header['title']='View Insurance Policy Report';
        $header['js'] = array('assets/users/js/common.js');

        $this->load->view('broker/common/header-report', $header);
        if($data['report_info']['report_type'] == 'client')
            $this->load->view('broker/report/ins_report_client', $data);
        else
            $this->load->view('broker/report/ins_report_family', $data);
        //$this->load->view('broker/common/footer');
    }

    function get_premium_calendar_report()
    {
        $data['page_title']='Premium Calendar Report';
        $data['prem_data'] = $_SESSION['prem_report']['prem_rep_data'];
        $data['report_info'] = $_SESSION['prem_report']['report_info'];
        $data['lapse_rep_data'] = $_SESSION['prem_report']['lapse_rep_data'];
        $data['rep_date']['rep_date_start'] = $_SESSION['prem_report']['rep_date_start'];
        $data['rep_date']['rep_date_end'] = $_SESSION['prem_report']['rep_date_end'];
        $data['logo'] = $_SESSION['prem_report']['report_info']['logo'];
        $header['title']='View Premium Calendar Report';
        $header['js'] = array('assets/users/js/common.js');

        $this->load->view('broker/common/header-report', $header);
        if($data['report_info']['report_type'] == 'client')
            $this->load->view('broker/report/prem_cal_client', $data);
        else
            $this->load->view('broker/report/prem_cal_family', $data);
        //$this->load->view('broker/common/footer');
    }

    function get_fd_report()
    {
        $data['page_title']='Fixed Deposit Report';
        $data['fd_rep_data'] = $_SESSION['fd_report']['fd_rep_data'];
        $data['report_info'] = $_SESSION['fd_report']['report_info'];
        $data['top5_maturity'] = $_SESSION['fd_report']['top5_maturity'];
        $data['brokerID'] = $_SESSION['fd_report']['brokerID'];
        
        $data['logo'] = $_SESSION['fd_report']['report_info']['logo'];

        $header['title']='View Fixed Deposit Report';
        $header['js'] = array('assets/users/js/common.js');

        $this->load->view('broker/common/header-report', $header);
        if($data['report_info']['report_type'] == 'client')
            $this->load->view('broker/report/fd_report_client', $data);
        else
            $this->load->view('broker/report/fd_report_family', $data);
        //$this->load->view('broker/common/footer');
    }
	
	function get_re_report()
    {
        $data['page_title']='Real Estate Report';
        $data['re_rep_data'] = $_SESSION['re_report']['re_rep_data'];
        $data['report_info'] = $_SESSION['re_report']['report_info'];
        $data['logo'] = $_SESSION['re_report']['report_info']['logo'];
        $header['title']='View Fixed Deposit Report';
        $header['js'] = array('assets/users/js/common.js');

        $this->load->view('broker/common/header-report', $header);
        if($data['report_info']['report_type'] == 'client')
            $this->load->view('broker/report/real_estate_report_client', $data);
        else
            $this->load->view('broker/report/real_estate_report_family', $data);
        //$this->load->view('broker/common/footer');
    }

    function get_interest_calendar_report()
    {
        $data['page_title']='Interest Calendar Report';
        $data['int_data'] = $_SESSION['int_report']['int_rep_data'];
        $data['report_info'] = $_SESSION['int_report']['report_info'];
        $data['logo'] = $_SESSION['int_report']['report_info']['logo'];
        $data['rep_date']['rep_date_start'] = $_SESSION['int_report']['rep_date_start'];
        $data['rep_date']['rep_date_end'] = $_SESSION['int_report']['rep_date_end'];

        $header['title']='View Interest Calendar Report';
        $header['js'] = array('assets/users/js/common.js');
        $this->load->view('broker/common/header-report', $header);
        if($data['report_info']['report_type'] == 'client')
            $this->load->view('broker/report/fd_int_cal_client', $data);
        else
            $this->load->view('broker/report/fd_int_cal_family', $data);
        //$this->load->view('broker/common/footer');
    }

    function get_equity_report()
    {
        $header['title']='View Equity/Shares Report';
        $data['all_data'] = $_SESSION['eq_report'];
        
        $data['report_info'] = $_SESSION['eq_report']['report_info'];
        if(isset($_SESSION['eq_report']['eq_rep_data'])) {
            $data['eq_rep_data'] = $_SESSION['eq_report']['eq_rep_data'];
        } else {
            $data['eq_rep_data'] = false;
        }
        $data['eq_values_data'] = $_SESSION['eq_report']['eq_values_data'];
        if(isset($_SESSION['eq_report']['xirr']))
        {
        $data['xirr'] = $_SESSION['eq_report']['xirr'];
        }
        $data['eq_balance'] = $_SESSION['eq_report']['balance'];
        $data['total_value'] = $_SESSION['eq_report']['total_value'];
        $data['logo'] = $_SESSION['eq_report']['report_info']['logo'];
        $data['broker_id'] = $_SESSION['eq_report']['broker_id'];
        $data['eq_values_cap_data'] = $_SESSION['eq_report']['eq_values_cap_data'];
        $data['eq_values_industry_data'] = $_SESSION['eq_report']['eq_values_industry_data'];
        $data['eq_chart_history'] = $_SESSION['eq_report']['eq_chart_history'];
        
        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'family') {
            $header['title'] = 'Equity Report for Family';
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/equity_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $header['title'] = 'Equity Report for Client';
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/equity_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        }
    }
    
     function get_equity_report_with_apc()
    {
        $header['title']='View Equity/Shares Report';
        $data['all_data'] = $_SESSION['eq_report'];
        
        $data['report_info'] = $_SESSION['eq_report']['report_info'];
        if(isset($_SESSION['eq_report']['eq_rep_data'])) {
            $data['eq_rep_data'] = $_SESSION['eq_report']['eq_rep_data'];
        } else {
            $data['eq_rep_data'] = false;
        }
        $data['eq_values_data'] = $_SESSION['eq_report']['eq_values_data'];
        if(isset($_SESSION['eq_report']['xirr']))
        {
        $data['xirr'] = $_SESSION['eq_report']['xirr'];
        }
        $data['eq_balance'] = $_SESSION['eq_report']['balance'];
        $data['total_value'] = $_SESSION['eq_report']['total_value'];
        $data['logo'] = $_SESSION['eq_report']['report_info']['logo'];
        $data['broker_id'] = $_SESSION['eq_report']['broker_id'];
        $data['eq_values_cap_data'] = $_SESSION['eq_report']['eq_values_cap_data'];
        $data['eq_values_industry_data'] = $_SESSION['eq_report']['eq_values_industry_data'];
        $data['eq_chart_history'] = $_SESSION['eq_report']['eq_chart_history'];
        
        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'family') {
            $header['title'] = 'Equity Report for Family';
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/equity_report_view_family_with_apc', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $header['title'] = 'Equity Report for Client';
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/equity_report_view_client_with_apc', $data);
            //$this->load->view('broker/common/footer');
        }
    }

    function get_commodity_report()
    {
        $header['title']='View Commodity Report';
        $data['commodity_rep_data'] = $_SESSION['commodity_report']['comm_rep_data'];
        $data['report_info'] = $_SESSION['commodity_report']['report_info'];
        $data['logo'] = $_SESSION['commodity_report']['report_info']['logo'];

        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'client') {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/commodity_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/commodity_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        }
    }
	
	function get_al_report()
    {
        $data['page_title']='Asset and Liabilities Report';
        $data['asset_rep_data'] = $_SESSION['al_report']['asset_rep_data'];
        $data['liability_rep_data'] = $_SESSION['al_report']['liability_rep_data'];
        $data['report_info'] = $_SESSION['al_report']['report_info'];
        $data['logo'] = $_SESSION['al_report']['report_info']['logo'];

        $header['title']='View Asset and Liabilities Report';
        $header['js'] = array('assets/users/js/common.js');
        $this->load->view('broker/common/header-report', $header);
        $this->load->view('broker/report/asset_liability_report', $data);
        //$this->load->view('broker/common/footer');
    }

    /*function get_mf_report()
    {
        $data['page_title']='Mutual Fund Report';
        $data['mf_rep_data'] = $_SESSION['mf_report']['mf_rep_data'];
        $data['report_info'] = $_SESSION['mf_report']['report_info'];
        $data['inv_sum'] = $_SESSION['mf_report']['inv_sum'];
        $data['cur_val_sum'] = $_SESSION['mf_report']['cur_val_sum'];
        $data['net_inv_data'] = $_SESSION['mf_report']['net_inv_data'];
        $data['logo'] = $_SESSION['mf_report']['report_info']['logo'];

        $header['title']='View Mutual Fund Report';

        $header['css'] = array(
            /*'assets/users/plugins/charts-flot/excanvas.min.js'*/
            /*'assets/users/plugins/charts-morrisjs/morris.css'
        );
        $header['js'] = array(
            /*'assets/users/plugins/charts-flot/jquery.flot.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.pie.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',*/
            /*'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js'
        );
        if($data['report_info']['report_type'] == 'client') {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_family', $data);
            //$this->load->view('broker/common/footer');
        }
    }*/
    
    // Done by Dipak
    // Changes 2017-04-22 - multiple reports
    
    /*function get_mf_report()
    {
        $data['page_title']='Mutual Fund Report';
        $data['mf_rep_data'] = $_SESSION['mf_report']['mf_rep_data'];
        $data['report_info'] = $_SESSION['mf_report']['report_info'];
        $data['inv_sum'] = $_SESSION['mf_report']['inv_sum'];
        $data['cur_val_sum'] = $_SESSION['mf_report']['cur_val_sum'];
        $data['net_inv_data'] = $_SESSION['mf_report']['net_inv_data'];
        $data['logo'] = $_SESSION['mf_report']['report_info']['logo'];
        $data['mf_summary_client'] = $_SESSION['mf_report']['mf_summary_client'];
        $data['mf_summary_typewise'] = $_SESSION['mf_report']['mf_summary_typewise'];
        $data['mf_summary_net_investment'] = $_SESSION['mf_report']['mf_summary_net_investment'];
        $data['mf_comman_scheme_summary']=$_SESSION['mf_report']['mf_comman_scheme_summary'];
        $data['folio_summary']=$_SESSION['mf_report']['folio_summary'];
        
        $header['title']='View Mutual Fund Report';
        $header['css'] = array(
            'assets/users/plugins/charts-morrisjs/morris.css'
        );
        $header['js'] = array(
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js'
        );
        if($data['report_info']['report_type_list'] == 'clientwise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise details client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_client', $data);
          }
          else {
           // echo "Client wise details family";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'clientwise_summary')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise summary client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_client', $data);
          }
          else {
           // echo "Client wise Summary family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'schemewise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "scheme wise Details client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_client', $data);
          }
          else
          {
            //echo "scheme wise detail family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_family', $data);
          }
        }
        else
        {
          if($data['report_info']['report_type'] == 'client')
          {
           // echo "foilowise_summary  Client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_folio_wise_summary_client',$data);
          }
          else {
            //  echo "foilowise_summary  family";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_report_folio_wise_summary_family',$data);
          }
        }
    }*/
    
    
    //Dipak - 2017-05-26 
    /*function get_mf_report()
    {
        $data['page_title']='Mutual Fund Report';
        $data['mf_rep_data'] = $_SESSION['mf_report']['mf_rep_data'];
        $data['report_info'] = $_SESSION['mf_report']['report_info'];
        $data['inv_sum'] = $_SESSION['mf_report']['inv_sum'];
        $data['cur_val_sum'] = $_SESSION['mf_report']['cur_val_sum'];
        $data['net_inv_data'] = $_SESSION['mf_report']['net_inv_data'];
        $data['logo'] = $_SESSION['mf_report']['report_info']['logo'];
        $data['mf_summary_client'] = $_SESSION['mf_report']['mf_summary_client'];
        $data['mf_summary_typewise'] = $_SESSION['mf_report']['mf_summary_typewise'];
        $data['mf_summary_net_investment'] = $_SESSION['mf_report']['mf_summary_net_investment'];
        $data['mf_comman_scheme_summary']=$_SESSION['mf_report']['mf_comman_scheme_summary'];
        $data['folio_summary']=$_SESSION['mf_report']['folio_summary'];
        $data['sip_data']=$_SESSION['mf_report']['sip_rep'];
        $data['comman_cagr_abs_total']=$_SESSION['mf_report']['comman_cagr_abs_total'];
       
        $header['title']='View Mutual Fund Report';
        $header['css'] = array(
            'assets/users/plugins/charts-morrisjs/morris.css'
        );
        $header['js'] = array(
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js'
        );
        if($data['report_info']['report_type_list'] == 'clientwise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise details client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_client', $data);
          }
          else {
           // echo "Client wise details family";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'clientwise_summary')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise summary client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_client', $data);
          }
          else {
           // echo "Client wise Summary family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'schemewise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "scheme wise Details client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_client', $data);
          }
          else
          {
            //echo "scheme wise detail family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'sip')
          {
            if($data['report_info']['report_type'] == 'client')
            {
              //echo "SIP  Client";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_sip_report_client',$data);
            }
            else {
              // echo "SIP  family";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_sip_report_family',$data);
            }
          }
        else
        {
          if($data['report_info']['report_type'] == 'client')
          {
           // echo "foilowise_summary  Client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_folio_wise_summary_client',$data);
          }
          else {
            //  echo "foilowise_summary  family";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_report_folio_wise_summary_family',$data);
          }
        
  }*/
  
  
  //Dipak - 2017-06-12
  function get_mf_report()
    {
        $data['page_title']='Mutual Fund Report';
        $data['mf_rep_data'] = $_SESSION['mf_report']['mf_rep_data'];
        $data['report_info'] = $_SESSION['mf_report']['report_info'];
        $data['inv_sum'] = $_SESSION['mf_report']['inv_sum'];
        $data['cur_val_sum'] = $_SESSION['mf_report']['cur_val_sum'];
        $data['net_inv_data'] = $_SESSION['mf_report']['net_inv_data'];
        $data['logo'] = $_SESSION['mf_report']['report_info']['logo'];
        $data['mf_summary_client'] = $_SESSION['mf_report']['mf_summary_client'];
        $data['mf_summary_typewise'] = $_SESSION['mf_report']['mf_summary_typewise'];
        $data['mf_summary_net_investment'] = $_SESSION['mf_report']['mf_summary_net_investment'];
        $data['mf_comman_scheme_summary']=$_SESSION['mf_report']['mf_comman_scheme_summary'];
        $data['folio_summary']=$_SESSION['mf_report']['folio_summary'];
        $data['capital_gain']=$_SESSION['mf_report']['capital_gain'];
        $data['sip_data']=$_SESSION['mf_report']['sip_rep'];
        $data['comman_cagr_abs_total']=$_SESSION['mf_report']['comman_cagr_abs_total'];
        $data['folio_master_data']=$_SESSION['mf_report']['folio_master_data'];
        $data['hide_nav_date']=$_SESSION['mf_report']['hide_nav_date'];
        $data['cap_detail']=$_SESSION['mf_report']['cap_detail'];
        $data['mf_comman_cap_detail_2']=$_SESSION['mf_report']['mf_comman_cap_detail_2'];
        $data['mf_comman_cap_detail_1']=$_SESSION['mf_report']['mf_comman_cap_detail_1'];
        $data['mf_summary_detail_for_chart']=$_SESSION['mf_report']['mf_summary_detail_for_chart'];
       
        $header['title']='View Mutual Fund Report';
        $header['css'] = array(
            /*'assets/users/plugins/charts-flot/excanvas.min.js'*/
            'assets/users/plugins/charts-morrisjs/morris.css'
        );
        $header['js'] = array(
            /*'assets/users/plugins/charts-flot/jquery.flot.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.pie.min.js',
            'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',*/
            'assets/users/plugins/charts-morrisjs/morris.min.js',
            'assets/users/plugins/charts-morrisjs/raphael.min.js',
            'assets/users/js/common.js'
        );
        if($data['report_info']['report_type_list'] == 'clientwise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise details client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_client', $data);
          }
          else {
           // echo "Client wise details family";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_detail_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'clientwise_summary')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise summary client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_client', $data);
          }
          else {
           // echo "Client wise Summary family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_family', $data);
          }
        }
         else if($data['report_info']['report_type_list'] == 'scheme_group_type_wise')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise summary client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_scheme_group_type_wise_client', $data);
          }
          else {
           // echo "Client wise Summary family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_scheme_group_type_wise_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'clientwise_summary_by_year')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "Client wise summary client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary_by_year'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_by_year_client', $data);
          }
          else {
           // echo "Client wise Summary family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['client_wise_summary_by_year'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_clientwise_summary_by_year_family', $data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'schemewise_detail')
        {
          if($data['report_info']['report_type'] == 'client')
          {
            //echo "scheme wise Details client";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_client', $data);
          }
          else
          {
            //echo "scheme wise detail family";
            $data['mf_rep_data'] = $_SESSION['mf_report']['mf_detail_schemewise'];
            // echo "<pre>";
            
             
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_schemewise_detail_family', $data);
          }
        }
      else if($data['report_info']['report_type_list'] == 'foilowise_summary')
        {
          if($data['report_info']['report_type'] == 'client')
          {
           // echo "foilowise_summary  Client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_folio_wise_summary_client',$data);
          }
          else {
            //  echo "foilowise_summary  family";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_report_folio_wise_summary_family',$data);
          }
        }
        else if($data['report_info']['report_type_list'] == 'sip')
          {
            if($data['report_info']['report_type'] == 'client')
            {
            //  echo "SIP  Client";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_sip_report_client',$data);
            }
            else {
              // echo "SIP  family";
               $this->load->view('broker/common/header-report', $header);
               $this->load->view('broker/report/mf_sip_report_family',$data);
            }
          }

    else if($data['report_info']['report_type_list'] == 'capital_gain')
     {
          if($data['report_info']['report_type'] == 'client')
          {
          //   echo "capital_gain  Client";
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/mf_report_capital_gain_client',$data);
          }
          else {
              // echo "capital_gain  family";
              $this->load->view('broker/common/header-report', $header);
              $this->load->view('broker/report/mf_report_capital_gain_family',$data);

          }
        }
        else
         {
              if($data['report_info']['report_type'] == 'client')
              {
                // echo "capital_gain  Client";
                // print_r($data['folio_master_data']);
               $this->load->view('broker/common/header-report', $header);
               $this->load->view('broker/report/mf_report_folio_master_client',$data);
              }
              else {
                  // echo "capital_gain  family";
                  // print_r($data['folio_master_data']);
                 $this->load->view('broker/common/header-report', $header);
                 $this->load->view('broker/report/mf_report_folio_master_family',$data);

              }
            }

  }

 
// Dipak 7 June 2017
function get_mf_broker_report()
    {
      $data['page_title']='Mutual Fund Report';
      $data['logo'] = $_SESSION['mf_aum_report']['rep_info']['logo'];
      $type=$_SESSION['mf_aum_report']['rep_info']['type'];
      $header['title']='View Mutual Fund Report';
      $header['css'] = array(
          /*'assets/users/plugins/charts-flot/excanvas.min.js'*/
          'assets/users/plugins/charts-morrisjs/morris.css'
      );
      
      $header['js'] = array(
          /*'assets/users/plugins/charts-flot/jquery.flot.min.js',
          'assets/users/plugins/charts-flot/jquery.flot.pie.min.js',
          'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',*/
          'assets/users/plugins/charts-morrisjs/morris.min.js',
          'assets/users/plugins/charts-morrisjs/raphael.min.js',
          'assets/users/js/common.js'
      );
      if($type=='aum_report')
      {
        $data['mf_rep_data'] = $_SESSION['mf_aum_report']['mf_aum_report'];
        $data['mf_rep_data_for_chart'] = $_SESSION['mf_aum_report']['mf_aum_report_for_chart'];
        $this->load->view('broker/common/header-report', $header);
        $this->load->view('broker/report/mf_broker_aum_report', $data);
      }
      else if($type=='all_summary_report'){
       $data['mf_rep_data'] = $_SESSION['mf_aum_report']['all_wise_summary'];
        $this->load->view('broker/common/header-report', $header);
        $this->load->view('broker/report/mf_report_all_summary', $data); 
      }
      else if($type=='family_aum_report'){
        $data['mf_rep_data'] = $_SESSION['mf_aum_report']['family_wise_summary'];
         $this->load->view('broker/common/header-report', $header);
         $this->load->view('broker/report/mf_broker_family_wise_aum_report', $data); 
       }
       else if($type=='detail_aum_report'){
           
        $data['mf_rep_data'] = $_SESSION['mf_aum_report']['detail_aum_report'];
        
         $this->load->view('broker/common/header-report', $header);
         $this->load->view('broker/report/mf_broker_detail_aum_report', $data); 
       }
       
      else {
        $data['mf_rep_data'] = $_SESSION['mf_aum_report']['mf_sip_report'];
        $this->load->view('broker/common/header-report', $header);
        $this->load->view('broker/report/mf_broker_sip_report', $data);
      }
    }
  

    // Final reports //
    function get_summary_report()
    {
        $header['title']='View Summary Report';
        $data['summary_rep_data'] = $_SESSION['summary_report']['summary_rep_data'];
        
        $data['summary_rep_previous_1'] = $_SESSION['summary_report']['summary_rep_previous_1'];
        $data['summary_rep_previous_2'] = $_SESSION['summary_report']['summary_rep_previous_2'];
        $data['brokerID'] = $_SESSION['summary_report']['brokerID'];
        $data['report_info'] = $_SESSION['summary_report']['report_info'];
        $data['logo'] = $_SESSION['summary_report']['report_info']['logo'];

        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'client') {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/summary_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/summary_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        }
    }

    function get_cash_flow_report()
    {
        $header['title']='View Cash Flow Report';
        $data['cash_flow_rep_data'] = $_SESSION['cash_flow_report']['cash_flow_rep_data'];
        $data['fam_info'] = $_SESSION['cash_flow_report']['fam_info'];
        $data['report_info'] = $_SESSION['cash_flow_report']['report_info'];
        $data['logo'] = $_SESSION['cash_flow_report']['report_info']['logo'];

        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'client') {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/cash_flow_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/cash_flow_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        }
    }

    function get_ledger_report()
    {
        $header['title']='View Ledger Report';
        $data['ledger_rep_inflow_data'] = $_SESSION['ledger_report']['ledger_rep_inflow_data'];
        $data['ledger_rep_outflow_data'] = $_SESSION['ledger_report']['ledger_rep_outflow_data'];
        $data['ledger_rep_dividend_data'] = $_SESSION['ledger_report']['ledger_rep_dividend_data'];
        $data['report_info'] = $_SESSION['ledger_report']['report_info'];
        $data['logo'] = $_SESSION['ledger_report']['report_info']['logo'];

        $header['js'] = array('assets/users/js/common.js');
        if($data['report_info']['report_type'] == 'client') {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/ledger_report_view_client', $data);
            //$this->load->view('broker/common/footer');
        } else {
            $this->load->view('broker/common/header-report', $header);
            $this->load->view('broker/report/ledger_report_view_family', $data);
            //$this->load->view('broker/common/footer');
        }
    }
} 