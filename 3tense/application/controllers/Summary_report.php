<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Summary_report extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        //load models
        $this->load->model('Reports_model', 'report');
        $this->load->model('Summary_report_data_model', 'summary_report');
    }
    
    ///////////////////////////////////////
    //MF Script Start
    //////////////////////////////////////
    function import_summary($brokerID)
    {
        $summary_rep = $this->report->get_summary_report_client_import(array('brokerID'=> $brokerID));
        foreach($summary_rep as $summary)
        {
            $summary = $this->summary_report->add_Summary_report_data($summary);
        }
    }

    function Summary_report_import()
    {
        
        $date = new DateTime('now');
        $date1 = new DateTime('now');
        $date->modify('last day of this month');

        if($date->format('Y-m-d') ==$date1->format('Y-m-d'))
        {
            $this->import_summary('0004');// isolution
            $this->import_summary('0009');// vfinatial
            $this->import_summary('0174');// isolution_gi
            $this->import_summary('0196');// ZM Investments
        }
    }
     function import_equity_summary($brokerID)
    {
        $summary_rep = $this->report->import_equity_summary_report_client_wise(array('brokerID'=> $brokerID));
        print_r($summary_rep);
        
    }
    function equity_Summary_report_import()
    {
        
        $date = new DateTime('now');
        $date1 = new DateTime('now');
        $date->modify('last day of this month');

        if($date->format('Y-m-d') ==$date1->format('Y-m-d'))
        {
            $this->import_equity_summary('0004');// isolution
            $this->import_equity_summary('0009');// vfinatial
            $this->import_equity_summary('0174');// isolution_gi
            $this->import_equity_summary('0196');// ZM Investments
        }
        
    }
      function import_mutual_fund_summary($brokerID)
    {
        $summary_rep = $this->report->import_mutual_fund_summary_report_client_wise(array('brokerID'=> $brokerID));
        print_r($summary_rep);
        
    }
    function mutual_fund_Summary_report_import()
    {
        
        $date = new DateTime('now');
        $date1 = new DateTime('now');
        $date->modify('last day of this month');

        if($date->format('Y-m-d') ==$date1->format('Y-m-d'))
        {
            $this->import_mutual_fund_summary('0004');// isolution
            $this->import_mutual_fund_summary('0009');// vfinatial
            $this->import_mutual_fund_summary('0174');// isolution_gi
        }
        
    }
    
    ///////////////////////////////////////
    //MF Script END
    //////////////////////////////////////
} 