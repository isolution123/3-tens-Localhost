<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mutual_fund_schemes extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model families_model family is the object
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Common_model', 'common');
        $this->load->model('Mutual_fund_purchase_model','mfp');

        $this->load->database();

        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Mutual Fund Schemes Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/mutual_fund/mf_schemes');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    

    //gets all occupations of admin & current broker from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $lastImportData = $this->common->get_last_imports("li.import_type = 'Mutual Fund NAV' AND (li.broker_id = '".$brokerID."' or li.broker_id is null)");
        /* changed the below code to show only last imported data - Salmaan - 12-05-2016
        if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d");
        }

        $lastSeventhDay = date('Y-m-d', strtotime($lastDay.' - 7 days'));
        $list = $this->mf->get_mf_schemes_hist('sh.scheme_date BETWEEN "'.$lastSeventhDay.'" AND "'.$lastDay.'"');*/
        //echo '<pre>';print_r($lastImportData);die;
        if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastImportDay->modify('-1 days');
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d", strtotime('-1 days'));
        }
        $list = $this->mf->get_mf_schemes_list();

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $scheme)
        {
            $num++;
            $row = array();
            //$row['scheme_history_id']=$scheme->scheme_history_id;  use this if needed
            $row['scheme_id']=$scheme->scheme_id;
            $row['scheme_name']=$scheme->scheme_name;
            $row['prod_code']=$scheme->prod_code;
            $row['isin']=$scheme->isin;
            $row['scheme_type']=$scheme->scheme_type;
            $row['market_cap']=$scheme->market_cap;
            //$row['current_nav']=$scheme->current_nav;
            //$row['scheme_date']=$scheme->scheme_date;

            //add html for action
            /*if(!($occupation->broker_id == null || $occupation->broker_id == '')) {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
            } else {
                $row['action'] = '';
            }*/

            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            //"recordsTotal"=>$this->family->count_all($brokerID),
            //"recordsFiltered"=>$this->family->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }
    
    function import($err_data=null)
    {
    		ini_set('max_execution_time', 0);
	        ini_set('memory_limit', '2048M');
	        ini_set('upload_max_filesize', '15M');
	        ini_set('post_max_size', '20M');
        
               $header['title'] = 'BSE Scheme Master';
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
               $this->load->view('client/bscimport', $data);
               $this->load->view('broker/common/notif');
               $this->load->view('broker/common/footer');
         }
         
    function BSCScheme_import()
    {

        ini_set('max_execution_time', 0);
    	ini_set('memory_limit', '2048M');
    	ini_set('upload_max_filesize', '15M');
    	ini_set('post_max_size', '20M');

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
                           
                    $UniqueNo="";$SchemeCode="";$RTASchemeCode="";$AMCSchemeCode="";$AMCName="";$ISIN="";$AMCCode="";$SchemeType="";$SchemePlan="";
                    $SchemeName="";$PurchaseAllowed="";$PurchaseTransactionmode="";$MinimumPurchaseAmount="";$AdditionalPurchaseAmount="";$MaximumPurchaseAmount="";
                    $PurchaseAmountMultiplier="";$PurchaseCutoffTime="";$RedemptionAllowed="";$RedemptionTransactionMode="";$MinimumRedemptionQty="";$RedemptionQtyMultiplier="";
                    $MaximumRedemptionQty="";$RedemptionAmountMinimum="";$RedemptionAmountMaximum="";$RedemptionAmountMultiple="";$RedemptionCutoffTime="";$RTAAgentCode="";
                    $AMCActiveFlag="";$DividendReinvestmentFlag="";$SIPFLAG="";$STPFLAG="";$SWPFlag="";$SwitchFLAG="";$SETTLEMENTTYPE="";$AMC_IND="";
                    $FaceValue="";$StartDate="";$EndDate="";$ExitLoadFlag="";$ExitLoad="";$LockInPeriodFlag="";$LockInPeriod="";$ChannelPartnerCode="";
                    $inserted="";
                    $tenseSchemeId="";
                    $SIPDATES="";
                    $SIPMINIMUMINSTALLMENTAMOUNT="";
                    $SIPMAXIMUMINSTALLMENTAMOUNT="";

                    
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
                                          							
                                    if(strtoupper($cell)==strtoupper('UNIQUE NO') || strtoupper($cell)==strtoupper('Scheme Code') ||strtoupper($cell)==strtoupper('Scheme Code') ||
                                        strtoupper($cell)==strtoupper('RTA Scheme Code') || strtoupper($cell)==strtoupper('AMC Scheme Code') ||strtoupper($cell)==strtoupper('ISIN') ||
                                        strtoupper($cell)==strtoupper('AMC Code') ||strtoupper($cell)==strtoupper('AMC Name') ||strtoupper($cell)==strtoupper('Scheme Type') ||strtoupper($cell)==strtoupper('Scheme Plan') ||
                                        strtoupper($cell)==strtoupper('Scheme Name') ||strtoupper($cell)==strtoupper('Purchase Allowed') ||
                                        strtoupper($cell)==strtoupper('Purchase Transaction mode') ||
                                        strtoupper($cell)==strtoupper('Minimum Purchase Amount') ||strtoupper($cell)==strtoupper('Additional Purchase Amount') 
                                        ||strtoupper($cell)==strtoupper('Maximum Purchase Amount') ||
                                        strtoupper($cell)==strtoupper('Purchase Amount Multiplier') ||strtoupper($cell)==strtoupper('Purchase Cutoff Time') 
                                        ||strtoupper($cell)==strtoupper('Redemption Allowed') ||
                                        strtoupper($cell)==strtoupper('Redemption Transaction Mode') ||strtoupper($cell)==strtoupper('Minimum Redemption Qty') 
                                        ||strtoupper($cell)==strtoupper('Redemption Qty Multiplier') ||
                                        strtoupper($cell)==strtoupper('Maximum Redemption Qty') ||strtoupper($cell)==strtoupper('Redemption Amount - Minimum') ||
                                        strtoupper($cell)==strtoupper('Redemption Amount â€“ Maximum') || strtoupper($cell)==strtoupper('Redemption Amount – Maximum')  
                                        ||strtoupper($cell)==strtoupper('Redemption Amount Multiple') ||
                                        strtoupper($cell)==strtoupper('Redemption Cut off Time') ||strtoupper($cell)==strtoupper('RTA Agent Code') ||
                                        strtoupper($cell)==strtoupper('AMC Active Flag') ||strtoupper($cell)==strtoupper('Dividend Reinvestment Flag') ||
                                        strtoupper($cell)==strtoupper('SIP FLAG') ||strtoupper($cell)==strtoupper('STP FLAG') ||
                                        strtoupper($cell)==strtoupper('SWP Flag') ||strtoupper($cell)==strtoupper('Switch FLAG') ||
                                        strtoupper($cell)==strtoupper('SETTLEMENT TYPE') ||strtoupper($cell)==strtoupper('AMC_IND') ||
                                        strtoupper($cell)==strtoupper('Face Value') ||strtoupper($cell)==strtoupper('Start Date') ||
                                        strtoupper($cell)==strtoupper('End Date') ||strtoupper($cell)==strtoupper('Exit Load Flag') ||
                                        strtoupper($cell)==strtoupper('Exit Load') ||strtoupper($cell)==strtoupper('Lock-in Period Flag') ||
                                        strtoupper($cell)==strtoupper('Lock-in Period') ||strtoupper($cell)==strtoupper('Channel Partner Code') 
                                        || strtoupper($cell)==strtoupper('3tenseSchemeId')
                                        || strtoupper($cell)==strtoupper('SIP DATES')
                                        || strtoupper($cell)==strtoupper('SIP MINIMUM INSTALLMENT AMOUNT')
                                        || strtoupper($cell)==strtoupper('SIP MAXIMUM INSTALLMENT AMOUNT'))
                                    {
                                        $dataColumns[$countCell] = $cell;
                                        $countCell++;
                                        $uploadedStatus = 2;
                                        continue;
                                    }
                                    else if($cell!="")
                                    {
                                        
                                        var_dump($cell);
                                        echo $cell;
                                        echo 'resr';
                                        exit();
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                  
                                     if($cell=='')
                                     {
                                        
                                     }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('UNIQUE NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $UniqueNo = trim($cell);
                                        }
                                        else{
                                            $UniqueNo ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('3tenseSchemeId'))
                                    {
                                        if($cell || $cell != ''){
                                            $tenseSchemeId = trim($cell);
                                        }
                                        else{
                                            $tenseSchemeId ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Scheme Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $SchemeCode = trim($cell);
                                        }
                                        else{
                                            $SchemeCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RTA Scheme Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $RTASchemeCode = trim($cell);
                                        }
                                        else{
                                            $RTASchemeCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC Scheme Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMCSchemeCode = trim($cell);
                                        }
                                        else{
                                            $AMCSchemeCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC Name'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMCName = trim($cell);
                                        }
                                        else{
                                            $AMCName ='';
                                        }
                                    }
                                    
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ISIN'))
                                    {
                                        if($cell || $cell != ''){
                                            $ISIN = trim($cell);
                                        }
                                        else{
                                            $ISIN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMCCode = trim($cell);
                                        }
                                        else{
                                            $AMCCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Scheme Type'))
                                    {
                                        if($cell || $cell != ''){
                                            $SchemeType = trim($cell);
                                        }
                                        else{
                                            $SchemeType ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Scheme Plan'))
                                    {
                                        if($cell || $cell != ''){
                                            $SchemePlan = trim($cell);
                                        }
                                        else{
                                            $SchemePlan ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Scheme Name'))
                                    {
                                        if($cell || $cell != ''){
                                            $SchemeName = trim($cell);
                                        }
                                        else{
                                            $SchemeName ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Purchase Allowed'))
                                    {
                                        if($cell || $cell != ''){
                                            $PurchaseAllowed = trim($cell);
                                        }
                                        else{
                                            $PurchaseAllowed ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Purchase Transaction mode'))
                                    {
                                        if($cell || $cell != ''){
                                            $PurchaseTransactionmode = trim($cell);
                                        }
                                        else{
                                            $PurchaseTransactionmode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Minimum Purchase Amount'))
                                    {
                                        if($cell || $cell != ''){
                                            $MinimumPurchaseAmount = trim($cell);
                                        }
                                        else{
                                            $MinimumPurchaseAmount ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Additional Purchase Amount'))
                                    {
                                        if($cell || $cell != ''){
                                            $AdditionalPurchaseAmount = trim($cell);
                                        }
                                        else{
                                            $AdditionalPurchaseAmount ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Maximum Purchase Amount'))
                                    {
                                        if($cell || $cell != ''){
                                            $MaximumPurchaseAmount = trim($cell);
                                        }
                                        else{
                                            $MaximumPurchaseAmount ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Purchase Amount Multiplier'))
                                    {
                                        if($cell || $cell != ''){
                                            $PurchaseAmountMultiplier = trim($cell);
                                        }
                                        else{
                                            $PurchaseAmountMultiplier ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Purchase Cutoff Time'))
                                    {
                                        if($cell || $cell != ''){
                                            $PurchaseCutoffTime = trim($cell);
                                        }
                                        else{
                                            $PurchaseCutoffTime ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Allowed'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionAllowed = trim($cell);
                                        }
                                        else{
                                            $RedemptionAllowed ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Transaction Mode'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionTransactionMode = trim($cell);
                                        }
                                        else{
                                            $RedemptionTransactionMode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Minimum Redemption Qty'))
                                    {
                                        if($cell || $cell != ''){
                                            $MinimumRedemptionQty = trim($cell);
                                        }
                                        else{
                                            $MinimumRedemptionQty ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Qty Multiplier'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionQtyMultiplier = trim($cell);
                                        }
                                        else{
                                            $RedemptionQtyMultiplier ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Maximum Redemption Qty'))
                                    {
                                        if($cell || $cell != ''){
                                            $MaximumRedemptionQty = trim($cell);
                                        }
                                        else{
                                            $MaximumRedemptionQty ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Amount - Minimum'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionAmountMinimum = trim($cell);
                                        }
                                        else{
                                            $RedemptionAmountMinimum ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Amount â€“ Maximum') || strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Amount – Maximum'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionAmountMaximum = trim($cell);
                                        }
                                        else{
                                            $RedemptionAmountMaximum ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Amount Multiple'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionAmountMultiple = trim($cell);
                                        }
                                        else{
                                            $RedemptionAmountMultiple ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Cut off Time'))
                                    {
                                        if($cell || $cell != ''){
                                            $RedemptionCutoffTime = trim($cell);
                                        }
                                        else{
                                            $RedemptionCutoffTime ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RTA Agent Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $RTAAgentCode = trim($cell);
                                        }
                                        else{
                                            $RTAAgentCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC Active Flag'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMCActiveFlag = trim($cell);
                                        }
                                        else{
                                            $AMCActiveFlag ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Dividend Reinvestment Flag'))
                                    {
                                        if($cell || $cell != ''){
                                            $DividendReinvestmentFlag = trim($cell);
                                        }
                                        else{
                                            $DividendReinvestmentFlag ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SIP FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $SIPFLAG = trim($cell);
                                        }
                                        else{
                                            $SIPFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('STP FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $STPFLAG = trim($cell);
                                        }
                                        else{
                                            $STPFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SWP Flag'))
                                    {
                                        if($cell || $cell != ''){
                                            $SWPFlag = trim($cell);
                                        }
                                        else{
                                            $SWPFlag ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Switch FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $SwitchFLAG = trim($cell);
                                        }
                                        else{
                                            $SwitchFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SETTLEMENT TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $SETTLEMENTTYPE = trim($cell);
                                        }
                                        else{
                                            $SETTLEMENTTYPE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC_IND'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMC_IND = trim($cell);
                                        }
                                        else{
                                            $AMC_IND ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Face Value'))
                                    {
                                        if($cell || $cell != ''){
                                            $FaceValue = trim($cell);
                                        }
                                        else{
                                            $FaceValue ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Start Date'))
                                    {
                                        if($cell || $cell != ''){
                                            $StartDate = trim($cell);
                                        }
                                        else{
                                            $StartDate ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('End Date'))
                                    {
                                     
                                        if($cell || $cell != ''){
                                            $EndDate = trim($cell);
                                        }
                                        else{
                                            $EndDate ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Exit Load Flag'))
                                    {
                                        if($cell || $cell != ''){
                                            $ExitLoadFlag = trim($cell);
                                        }
                                        else{
                                            $ExitLoadFlag ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Exit Load'))
                                    {
                                        if($cell || $cell != ''){
                                            $ExitLoad = trim($cell);
                                        }
                                        else{
                                            $ExitLoad ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Lock-in Period Flag'))
                                    {
                                        if($cell || $cell != ''){
                                            $LockInPeriodFlag = trim($cell);
                                        }
                                        else{
                                            $LockInPeriodFlag ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Lock-in Period'))
                                    {
                                        if($cell || $cell != ''){
                                            $LockInPeriod = trim($cell);
                                        }
                                        else{
                                            $LockInPeriod ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Channel Partner Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $ChannelPartnerCode = trim($cell);
                                        }
                                        else{
                                            $ChannelPartnerCode ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SIP DATES'))
                                    {
                                        if($cell || $cell != ''){
                                            $SIPDATES = trim($cell);
                                        }
                                        else{
                                            $SIPDATES ='';
                                        }
                                    }
                                       else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SIP MINIMUM INSTALLMENT AMOUNT'))
                                    {
                                        if($cell || $cell != ''){
                                            $SIPMINIMUMINSTALLMENTAMOUNT = trim($cell);
                                        }
                                        else{
                                            $SIPMINIMUMINSTALLMENTAMOUNT ='';
                                        }
                                    }
                                       else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SIP MAXIMUM INSTALLMENT AMOUNT'))
                                    {
                                        if($cell || $cell != ''){
                                            $SIPMAXIMUMINSTALLMENTAMOUNT = trim($cell);
                                        }
                                        else{
                                            $SIPMAXIMUMINSTALLMENTAMOUNT ='';
                                        }
                                    }
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                            if($countRow != 0)
                            {
                                if($UniqueNo==0 || $UniqueNo=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $SchemeId = 0;
                                    $res = $this->mfp->check_bsc_scheme_by_unique_no($UniqueNo);
                                    
                                    if(!empty($res))
                                    {
                                        $SchemeId = $res[0]->id;
                                    }
                                    
                                   
                                    
                                    
                                    if($tenseSchemeId!='' && $tenseSchemeId!='0')
                                    {
                                        $dataRows[$add_FD_list] =array('id'=>$SchemeId,'UniqueNo'=>$UniqueNo,'SchemeCode'=>$SchemeCode,'RTASchemeCode'=>$RTASchemeCode,'AMCSchemeCode'=>$AMCSchemeCode,
                                                            'AMCName'=>$AMCName,
                                                            'ISIN'=>$ISIN,'AMCCode'=>$AMCCode,'SchemeType'=>$SchemeType,'SchemePlan'=>$SchemePlan,'SchemeName'=>$SchemeName,'PurchaseAllowed'=>$PurchaseAllowed,
                                                            'PurchaseTransactionmode'=>$PurchaseTransactionmode,'MinimumPurchaseAmount'=>$MinimumPurchaseAmount,'AdditionalPurchaseAmount'=>$AdditionalPurchaseAmount,
                                                            'MaximumPurchaseAmount'=>$MaximumPurchaseAmount, 'PurchaseAmountMultiplier'=>$PurchaseAmountMultiplier,'PurchaseCutoffTime'=>$PurchaseCutoffTime,
                                                            'RedemptionAllowed'=>$RedemptionAllowed,'RedemptionTransactionMode'=>$RedemptionTransactionMode,'MinimumRedemptionQty'=>$MinimumRedemptionQty,
                                                            'RedemptionQtyMultiplier'=>$RedemptionQtyMultiplier,'MaximumRedemptionQty'=>$MaximumRedemptionQty,'RedemptionAmountMinimum'=>$RedemptionAmountMinimum,
                                                            'RedemptionAmountOtherMaximum'=>$RedemptionAmountMaximum,'RedemptionAmountMultiple'=>$RedemptionAmountMultiple,'RedemptionCutoffTime'=>$RedemptionCutoffTime,
                                                            'RTAAgentCode'=>$RTAAgentCode,'AMCActiveFlag'=>$AMCActiveFlag,'DividendReinvestmentFlag'=>$DividendReinvestmentFlag,'SIPFLAG'=>$SIPFLAG,'STPFLAG'=>$STPFLAG,
                                                            'SWPFlag'=>$SWPFlag,'SwitchFLAG'=>$SwitchFLAG,'SETTLEMENTTYPE'=>$SETTLEMENTTYPE,'AMC_IND'=>$AMC_IND,'FaceValue'=>$FaceValue,'StartDate'=>$StartDate,'EndDate'=>$EndDate,
                                                            'ExitLoadFlag'=>$ExitLoadFlag,'ExitLoad'=>$ExitLoad,'LockInPeriodFlag'=>$LockInPeriodFlag,'LockInPeriod'=>$LockInPeriod,
                                                            'ChannelPartnerCode'=>$ChannelPartnerCode,
                                                            'tenseSchemeId'=>$tenseSchemeId,
                                                            'SIPDATES'=>$SIPDATES,
                                                            'SIPMINIMUMINSTALLMENTAMOUNT'=>$SIPMINIMUMINSTALLMENTAMOUNT,
                                                            'SIPMAXIMUMINSTALLMENTAMOUNT'=>$SIPMAXIMUMINSTALLMENTAMOUNT);
                                    }                       
                                    else
                                    {
                                        $dataRows[$add_FD_list] =array('id'=>$SchemeId,'UniqueNo'=>$UniqueNo,'SchemeCode'=>$SchemeCode,'RTASchemeCode'=>$RTASchemeCode,'AMCSchemeCode'=>$AMCSchemeCode,
                                                            'AMCName'=>$AMCName,
                                                            'ISIN'=>$ISIN,'AMCCode'=>$AMCCode,'SchemeType'=>$SchemeType,'SchemePlan'=>$SchemePlan,'SchemeName'=>$SchemeName,'PurchaseAllowed'=>$PurchaseAllowed,
                                                            'PurchaseTransactionmode'=>$PurchaseTransactionmode,'MinimumPurchaseAmount'=>$MinimumPurchaseAmount,'AdditionalPurchaseAmount'=>$AdditionalPurchaseAmount,
                                                            'MaximumPurchaseAmount'=>$MaximumPurchaseAmount, 'PurchaseAmountMultiplier'=>$PurchaseAmountMultiplier,'PurchaseCutoffTime'=>$PurchaseCutoffTime,
                                                            'RedemptionAllowed'=>$RedemptionAllowed,'RedemptionTransactionMode'=>$RedemptionTransactionMode,'MinimumRedemptionQty'=>$MinimumRedemptionQty,
                                                            'RedemptionQtyMultiplier'=>$RedemptionQtyMultiplier,'MaximumRedemptionQty'=>$MaximumRedemptionQty,'RedemptionAmountMinimum'=>$RedemptionAmountMinimum,
                                                            'RedemptionAmountOtherMaximum'=>$RedemptionAmountMaximum,'RedemptionAmountMultiple'=>$RedemptionAmountMultiple,'RedemptionCutoffTime'=>$RedemptionCutoffTime,
                                                            'RTAAgentCode'=>$RTAAgentCode,'AMCActiveFlag'=>$AMCActiveFlag,'DividendReinvestmentFlag'=>$DividendReinvestmentFlag,'SIPFLAG'=>$SIPFLAG,'STPFLAG'=>$STPFLAG,
                                                            'SWPFlag'=>$SWPFlag,'SwitchFLAG'=>$SwitchFLAG,'SETTLEMENTTYPE'=>$SETTLEMENTTYPE,'AMC_IND'=>$AMC_IND,'FaceValue'=>$FaceValue,'StartDate'=>$StartDate,'EndDate'=>$EndDate,
                                                            'ExitLoadFlag'=>$ExitLoadFlag,'ExitLoad'=>$ExitLoad,'LockInPeriodFlag'=>$LockInPeriodFlag,'LockInPeriod'=>$LockInPeriod,'ChannelPartnerCode'=>$ChannelPartnerCode,
                                                             'SIPDATES'=>$SIPDATES,
                                                            'SIPMINIMUMINSTALLMENTAMOUNT'=>$SIPMINIMUMINSTALLMENTAMOUNT,
                                                            'SIPMAXIMUMINSTALLMENTAMOUNT'=>$SIPMAXIMUMINSTALLMENTAMOUNT);
                                    }
                                   
                            
                                    if($SchemeId>0)
                                    {
                                      $this->mfp->update_bsc_scheme( $dataRows[$add_FD_list]);
                                    }
                                    else
                                    {
                                       $inserted = $this->mfp->add_bsc_scheme( $dataRows[$add_FD_list]);
                                    }
                                    
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                    
                                    
                                    $UniqueNo="";$SchemeCode="";$RTASchemeCode="";$AMCSchemeCode="";$ISIN="";$AMCCode="";$AMCName="";$SchemeType="";$SchemePlan="";
                                    $SchemeName="";$PurchaseAllowed="";$PurchaseTransactionmode="";$MinimumPurchaseAmount="";$AdditionalPurchaseAmount="";$MaximumPurchaseAmount="";
                                    $PurchaseAmountMultiplier="";$PurchaseCutoffTime="";$RedemptionAllowed="";$RedemptionTransactionMode="";$MinimumRedemptionQty="";$RedemptionQtyMultiplier="";
                                    $MaximumRedemptionQty="";$RedemptionAmountMinimum="";$RedemptionAmountMaximum="";$RedemptionAmountMultiple="";$RedemptionCutoffTime="";$RTAAgentCode="";
                                    $AMCActiveFlag="";$DividendReinvestmentFlag="";$SIPFLAG="";$STPFLAG="";$SWPFlag="";$SwitchFLAG="";$SETTLEMENTTYPE="";$AMC_IND="";
                                    $FaceValue="";$StartDate="";$EndDate="";$ExitLoadFlag="";$ExitLoad="";$LockInPeriodFlag="";$LockInPeriod="";$ChannelPartnerCode="";
                                    $inserted="";$tenseSchemeId="";$SIPDATES="";$SIPMINIMUMINSTALLMENTAMOUNT="";$SIPMAXIMUMINSTALLMENTAMOUNT="";
                                }
                            }
                            
                            
                            $countRow++;
                        }
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('Bsc Scheme Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "Scheme Details Uploaded Successfully";
                                  }
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
            $this->import($imp_data);
        }
    }

    function import_client($err_data=null)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
    
           $header['title'] = 'BSE Client Master';
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
           $this->load->view('client/bscclientimport', $data);
           $this->load->view('broker/common/notif');
           $this->load->view('broker/common/footer');
    }

    function BSCClient_import()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');

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
                           
                $CLIENTCODE="";$CLIENTHOLDING="";$TAXSTATUS="";$OCCUPATION="";$FIRSTAPPLICANTNAME="";$SECONDAPPLICANTNAME="";$THIRDAPPLICANTNAME="";$FIRSTAPPLICANTDOB="";$FIRSTAPPGENDER="";$CLIENTGUARDIAN="";$FIRSTAPPLICANTPAN="";$CLIENTNOMINEE="";$CLIENTNOMINEERELATION="";$GUARDIANPAN="";$CLIENTTYPE="";$CLIENTDEFAULTDP="";$CDSLDPID="";$CDSLCLTID="";$NSDLDPID="";$NSDLCLTID="";$ACCTYPE1="";$ACCNO1="";$CLIENTMICRNO1="";$NEFTIFSCCODE1="";$BANKNAME1="";$BANKBRANCH1="";$DEFAULTBANKFLAG1="";$ACCTYPE2="";$ACCNO2="";$CLIENTMICRNO2="";$NEFTIFSCCODE2="";$DEFAULTBANKFLAG2="";$BANKNAME2="";$BANKBRANCH2="";$ACCTYPE3="";$ACCNO3="";$CLIENTMICRNO3="";$NEFTIFSCCODE3="";$DefaultBankFlag3="";$BANKNAME3="";$BankBranch3="";$ACCTYPE4="";$ACCNO4="";$CLIENTMICRNO4="";$NEFTIFSCCODE4="";$DEFAULTBANKFLAG4="";$BANKNAME4="";$BANKBRANCH4="";$ACCTYPE5="";$ACCNO5="";$CLIENTMICRNO5="";$NEFTIFSCCODE5="";$BANKNAME5="";$BANKBRANCH5="";$DEFAULTBANKFLAG5="";$CLIENTCHEQUENAME5="";$ADD1="";$ADD2="";$ADD3="";$CITY="";$CLIENTSTATE="";$PINCODE="";$COUNTRY="";$RESIPHONE="";$RESIFAX="";$OFFICEPHONE="";$CLIENTOFFICEFAX="";$CLIENTEMAIL="";$COMMMODE="";$DIVPAYMODE="";$SECONDAPPPAN="";$THIRDAPPPAN="";$MAPINNO="";$FORADD1="";$FORADD2="";$FORADD3="";$FORCITY="";$FORPINCODE="";$FORSTATE="";$FORCOUNTRY="";$FORRESIPHONE="";$FORRESIFAX="";$FOROFFPHONE="";$FOROFFFAX="";$MOBILE="";$CKYC="";$KYCTYPE1stHOLDER="";$KYCTYPE2ndHOLDER="";$KYCTYPE3rdHOLDER="";$KYCTYPEGUARDIAN="";$FirstHolderCKYCNumber="";$SecondHolderCKYCNumber="";$ThirdHolderCKYCNumber="";$GuardianCKYCNumber="";$JointHolder1DOB="";$JointHolder2DOB="";$GuardianCKYCDOB="";$DEALER="";$BRANCH="";$CREATEDBY="";$CREATEDAT="";$LASTMODIFIEDBY="";$LASTMODIFIEDAT="";

                    
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                          
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);


                    // Following comment code will use for generating code for multiple columns
                    /*echo "<pre>";
                    print_r($excelData);

                    $k='';$m='';
                    for($i=0;$i<count($excelData[0]);$i++){
                        $j = "strtoupper(cell)==strtoupper('".$excelData[0][$i]."') || ";
                        $k = $k.$j;

                        $l = '$'.str_replace(" ", "", $excelData[0][$i]).'="";';
                        $l = '"'.str_replace(" ", "", $excelData[0][$i]).'"=>$'.str_replace(" ", "", $excelData[0][$i]).',';
                        $m = $m.$l;

                        echo "else if(strtoupper($dataColumns[$countCell]) ===strtoupper('".$excelData[0][$i]."'))
                        {
                            if($cell || $cell != ''){
                                $".str_replace(' ', '', $excelData[0][$i])." = trim($cell);
                            }
                            else{
                                $".str_replace(' ', '', $excelData[0][$i])." ='';
                            }
                        }";      
                    }
                    echo $k;
                    echo "<br>";
                    echo $m;
                    die();  */


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
                                                                    
                                    if(strtoupper($cell)==strtoupper('CLIENT CODE') || strtoupper($cell)==strtoupper('CLIENT HOLDING') || strtoupper($cell)==strtoupper('TAX STATUS') || strtoupper($cell)==strtoupper('OCCUPATION') || strtoupper($cell)==strtoupper('FIRST APPLICANT NAME') || strtoupper($cell)==strtoupper('SECOND APPLICANT NAME') || strtoupper($cell)==strtoupper('THIRD APPLICANT NAME') || strtoupper($cell)==strtoupper('FIRST APPLICANT DOB') || strtoupper($cell)==strtoupper('FIRST APP GENDER') || strtoupper($cell)==strtoupper('CLIENT GUARDIAN') || strtoupper($cell)==strtoupper('FIRST APPLICANT PAN') || strtoupper($cell)==strtoupper('CLIENT NOMINEE') || strtoupper($cell)==strtoupper('CLIENT NOMINEE RELATION') || strtoupper($cell)==strtoupper('GUARDIAN PAN') || strtoupper($cell)==strtoupper('CLIENT TYPE') || strtoupper($cell)==strtoupper('CLIENT DEFAULT DP') || strtoupper($cell)==strtoupper('CDSLDPID') || strtoupper($cell)==strtoupper('CDSLCLTID') || strtoupper($cell)==strtoupper('NSDLDPID') || strtoupper($cell)==strtoupper('NSDLCLTID') || strtoupper($cell)==strtoupper('ACCTYPE 1') || strtoupper($cell)==strtoupper('ACC NO 1') || strtoupper($cell)==strtoupper('CLIENT MICR NO 1') || strtoupper($cell)==strtoupper('NEFT IFSC CODE1') || strtoupper($cell)==strtoupper('BANK NAME 1') || strtoupper($cell)==strtoupper('BANK BRANCH 1') || strtoupper($cell)==strtoupper('DEFAULT BANK FLAG1') || strtoupper($cell)==strtoupper('ACC TYPE 2') || strtoupper($cell)==strtoupper('ACC NO 2') || strtoupper($cell)==strtoupper('CLIENT MICR NO 2') || strtoupper($cell)==strtoupper('NEFT IFSC CODE2') || strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 2') || strtoupper($cell)==strtoupper('BANK NAME 2') || strtoupper($cell)==strtoupper('BANK BRANCH 2') || strtoupper($cell)==strtoupper('ACC TYPE 3') || strtoupper($cell)==strtoupper('ACCNO 3') || strtoupper($cell)==strtoupper('CLIENT MICR NO3') || strtoupper($cell)==strtoupper('NEFT IFSC CODE3') || strtoupper($cell)==strtoupper('DefaultBankFlag3') || strtoupper($cell)==strtoupper('BANK NAME 3') || strtoupper($cell)==strtoupper('Bank Branch 3') || strtoupper($cell)==strtoupper('ACC TYPE 4') || strtoupper($cell)==strtoupper('ACCNO 4') || strtoupper($cell)==strtoupper('CLIENT MICRNO 4') || strtoupper($cell)==strtoupper('NEFT IFSC CODE4') || strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 4') || strtoupper($cell)==strtoupper('BANK NAME 4') || strtoupper($cell)==strtoupper('BANK BRANCH 4') || strtoupper($cell)==strtoupper('ACC TYPE 5') || strtoupper($cell)==strtoupper('ACCNO5') || strtoupper($cell)==strtoupper('CLIENT MICRNO 5') || strtoupper($cell)==strtoupper('NEFT IFSC CODE5') || strtoupper($cell)==strtoupper('BANK NAME 5') || strtoupper($cell)==strtoupper('BANK BRANCH 5') || strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 5') || strtoupper($cell)==strtoupper('CLIENT CHEQUE NAME 5') || strtoupper($cell)==strtoupper('ADD1') || strtoupper($cell)==strtoupper('ADD2') || strtoupper($cell)==strtoupper('ADD3') || strtoupper($cell)==strtoupper('CITY') || strtoupper($cell)==strtoupper('CLIENT STATE') || strtoupper($cell)==strtoupper('PIN CODE') || strtoupper($cell)==strtoupper('COUNTRY') || strtoupper($cell)==strtoupper('RESIPHONE') || strtoupper($cell)==strtoupper('RESIFAX') || strtoupper($cell)==strtoupper('OFFICE PHONE') || strtoupper($cell)==strtoupper('CLIENT OFFICE FAX') || strtoupper($cell)==strtoupper('CLIENT EMAIL') || strtoupper($cell)==strtoupper('COMMMODE') || strtoupper($cell)==strtoupper('DIVPAYMODE') || strtoupper($cell)==strtoupper('SECOND APP PAN') || strtoupper($cell)==strtoupper('THIRD APP PAN') || strtoupper($cell)==strtoupper('MAPIN NO') || strtoupper($cell)==strtoupper('FOR ADD1') || strtoupper($cell)==strtoupper('FOR ADD2') || strtoupper($cell)==strtoupper('FOR ADD3') || strtoupper($cell)==strtoupper('FOR CITY') || strtoupper($cell)==strtoupper('FOR PINCODE') || strtoupper($cell)==strtoupper('FOR STATE') || strtoupper($cell)==strtoupper('FOR COUNTRY') || strtoupper($cell)==strtoupper('FOR RESIPHONE') || strtoupper($cell)==strtoupper('FOR RESIFAX') || strtoupper($cell)==strtoupper('FOR OFFPHONE') || strtoupper($cell)==strtoupper('FOR OFFFAX') || strtoupper($cell)==strtoupper('MOBILE') || strtoupper($cell)==strtoupper('CKYC') || strtoupper($cell)==strtoupper('KYC TYPE 1st HOLDER') || strtoupper($cell)==strtoupper('KYC TYPE 2nd HOLDER') || strtoupper($cell)==strtoupper('KYC TYPE 3rd HOLDER') || strtoupper($cell)==strtoupper('KYC TYPE GUARDIAN') || strtoupper($cell)==strtoupper('First Holder CKYC Number') || strtoupper($cell)==strtoupper('Second Holder CKYC Number') || strtoupper($cell)==strtoupper('Third Holder CKYC Number') || strtoupper($cell)==strtoupper('Guardian CKYC Number') || strtoupper($cell)==strtoupper('Joint Holder 1 DOB') || strtoupper($cell)==strtoupper('Joint Holder 2 DOB') || strtoupper($cell)==strtoupper('Guardian CKYC DOB') || strtoupper($cell)==strtoupper('DEALER') || strtoupper($cell)==strtoupper('BRANCH') || strtoupper($cell)==strtoupper('CREATED BY') || strtoupper($cell)==strtoupper('CREATED AT') || strtoupper($cell)==strtoupper('LAST MODIFIED BY') || strtoupper($cell)==strtoupper('LAST MODIFIED AT'))
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
                                        //exit();
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {

                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTCODE = trim($cell);
                                        }
                                        else{
                                            $CLIENTCODE ='';
                                        }
                                    }

                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT HOLDING'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTHOLDING = trim($cell);
                                        }
                                        else{
                                            $CLIENTHOLDING ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('TAX STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $TAXSTATUS = trim($cell);
                                        }
                                        else{
                                            $TAXSTATUS ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('OCCUPATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $OCCUPATION = trim($cell);
                                        }
                                        else{
                                            $OCCUPATION ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FIRST APPLICANT NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $FIRSTAPPLICANTNAME = trim($cell);
                                        }
                                        else{
                                            $FIRSTAPPLICANTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND APPLICANT NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDAPPLICANTNAME = trim($cell);
                                        }
                                        else{
                                            $SECONDAPPLICANTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD APPLICANT NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDAPPLICANTNAME = trim($cell);
                                        }
                                        else{
                                            $THIRDAPPLICANTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FIRST APPLICANT DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $FIRSTAPPLICANTDOB = trim($cell);
                                        }
                                        else{
                                            $FIRSTAPPLICANTDOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FIRST APP GENDER'))
                                    {
                                        if($cell || $cell != ''){
                                            $FIRSTAPPGENDER = trim($cell);
                                        }
                                        else{
                                            $FIRSTAPPGENDER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT GUARDIAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTGUARDIAN = trim($cell);
                                        }
                                        else{
                                            $CLIENTGUARDIAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FIRST APPLICANT PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $FIRSTAPPLICANTPAN = trim($cell);
                                        }
                                        else{
                                            $FIRSTAPPLICANTPAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT NOMINEE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTNOMINEE = trim($cell);
                                        }
                                        else{
                                            $CLIENTNOMINEE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT NOMINEE RELATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTNOMINEERELATION = trim($cell);
                                        }
                                        else{
                                            $CLIENTNOMINEERELATION ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $GUARDIANPAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTTYPE = trim($cell);
                                        }
                                        else{
                                            $CLIENTTYPE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT DEFAULT DP'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTDEFAULTDP = trim($cell);
                                        }
                                        else{
                                            $CLIENTDEFAULTDP ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CDSLDPID'))
                                    {
                                        if($cell || $cell != ''){
                                            $CDSLDPID = trim($cell);
                                        }
                                        else{
                                            $CDSLDPID ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CDSLCLTID'))
                                    {
                                        if($cell || $cell != ''){
                                            $CDSLCLTID = trim($cell);
                                        }
                                        else{
                                            $CDSLCLTID ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NSDLDPID'))
                                    {
                                        if($cell || $cell != ''){
                                            $NSDLDPID = trim($cell);
                                        }
                                        else{
                                            $NSDLDPID ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NSDLCLTID'))
                                    {
                                        if($cell || $cell != ''){
                                            $NSDLCLTID = trim($cell);
                                        }
                                        else{
                                            $NSDLCLTID ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCTYPE 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCTYPE1 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC NO 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCNO1 = trim($cell);
                                        }
                                        else{
                                            $ACCNO1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT MICR NO 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTMICRNO1 = trim($cell);
                                        }
                                        else{
                                            $CLIENTMICRNO1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NEFT IFSC CODE1'))
                                    {
                                        if($cell || $cell != ''){
                                            $NEFTIFSCCODE1 = trim($cell);
                                        }
                                        else{
                                            $NEFTIFSCCODE1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME1 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH1 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG1'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG1 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC TYPE 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCTYPE2 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC NO 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCNO2 = trim($cell);
                                        }
                                        else{
                                            $ACCNO2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT MICR NO 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTMICRNO2 = trim($cell);
                                        }
                                        else{
                                            $CLIENTMICRNO2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NEFT IFSC CODE2'))
                                    {
                                        if($cell || $cell != ''){
                                            $NEFTIFSCCODE2 = trim($cell);
                                        }
                                        else{
                                            $NEFTIFSCCODE2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG2 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME2 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH2 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC TYPE 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCTYPE3 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCNO 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCNO3 = trim($cell);
                                        }
                                        else{
                                            $ACCNO3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT MICR NO3'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTMICRNO3 = trim($cell);
                                        }
                                        else{
                                            $CLIENTMICRNO3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NEFT IFSC CODE3'))
                                    {
                                        if($cell || $cell != ''){
                                            $NEFTIFSCCODE3 = trim($cell);
                                        }
                                        else{
                                            $NEFTIFSCCODE3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DefaultBankFlag3'))
                                    {
                                        if($cell || $cell != ''){
                                            $DefaultBankFlag3 = trim($cell);
                                        }
                                        else{
                                            $DefaultBankFlag3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME3 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Bank Branch 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $BankBranch3 = trim($cell);
                                        }
                                        else{
                                            $BankBranch3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC TYPE 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCTYPE4 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCNO 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCNO4 = trim($cell);
                                        }
                                        else{
                                            $ACCNO4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT MICRNO 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTMICRNO4 = trim($cell);
                                        }
                                        else{
                                            $CLIENTMICRNO4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NEFT IFSC CODE4'))
                                    {
                                        if($cell || $cell != ''){
                                            $NEFTIFSCCODE4 = trim($cell);
                                        }
                                        else{
                                            $NEFTIFSCCODE4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG4 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME4 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH4 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH4 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACC TYPE 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCTYPE5 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCNO5'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCNO5 = trim($cell);
                                        }
                                        else{
                                            $ACCNO5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT MICRNO 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTMICRNO5 = trim($cell);
                                        }
                                        else{
                                            $CLIENTMICRNO5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NEFT IFSC CODE5'))
                                    {
                                        if($cell || $cell != ''){
                                            $NEFTIFSCCODE5 = trim($cell);
                                        }
                                        else{
                                            $NEFTIFSCCODE5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME5 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH5 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG5 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT CHEQUE NAME 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTCHEQUENAME5 = trim($cell);
                                        }
                                        else{
                                            $CLIENTCHEQUENAME5 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADD1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADD1 = trim($cell);
                                        }
                                        else{
                                            $ADD1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADD2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADD2 = trim($cell);
                                        }
                                        else{
                                            $ADD2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADD3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADD3 = trim($cell);
                                        }
                                        else{
                                            $ADD3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CITY'))
                                    {
                                        if($cell || $cell != ''){
                                            $CITY = trim($cell);
                                        }
                                        else{
                                            $CITY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT STATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTSTATE = trim($cell);
                                        }
                                        else{
                                            $CLIENTSTATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PIN CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $PINCODE = trim($cell);
                                        }
                                        else{
                                            $PINCODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('COUNTRY'))
                                    {
                                        if($cell || $cell != ''){
                                            $COUNTRY = trim($cell);
                                        }
                                        else{
                                            $COUNTRY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RESIPHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $RESIPHONE = trim($cell);
                                        }
                                        else{
                                            $RESIPHONE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RESIFAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $RESIFAX = trim($cell);
                                        }
                                        else{
                                            $RESIFAX ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('OFFICE PHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $OFFICEPHONE = trim($cell);
                                        }
                                        else{
                                            $OFFICEPHONE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT OFFICE FAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTOFFICEFAX = trim($cell);
                                        }
                                        else{
                                            $CLIENTOFFICEFAX ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT EMAIL'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTEMAIL = trim($cell);
                                        }
                                        else{
                                            $CLIENTEMAIL ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('COMMMODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $COMMMODE = trim($cell);
                                        }
                                        else{
                                            $COMMMODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DIVPAYMODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $DIVPAYMODE = trim($cell);
                                        }
                                        else{
                                            $DIVPAYMODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND APP PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDAPPPAN = trim($cell);
                                        }
                                        else{
                                            $SECONDAPPPAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD APP PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDAPPPAN = trim($cell);
                                        }
                                        else{
                                            $THIRDAPPPAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MAPIN NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $MAPINNO = trim($cell);
                                        }
                                        else{
                                            $MAPINNO ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR ADD1'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORADD1 = trim($cell);
                                        }
                                        else{
                                            $FORADD1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR ADD2'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORADD2 = trim($cell);
                                        }
                                        else{
                                            $FORADD2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR ADD3'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORADD3 = trim($cell);
                                        }
                                        else{
                                            $FORADD3 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR CITY'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORCITY = trim($cell);
                                        }
                                        else{
                                            $FORCITY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR PINCODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORPINCODE = trim($cell);
                                        }
                                        else{
                                            $FORPINCODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR STATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORSTATE = trim($cell);
                                        }
                                        else{
                                            $FORSTATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR COUNTRY'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORCOUNTRY = trim($cell);
                                        }
                                        else{
                                            $FORCOUNTRY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR RESIPHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORRESIPHONE = trim($cell);
                                        }
                                        else{
                                            $FORRESIPHONE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR RESIFAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $FORRESIFAX = trim($cell);
                                        }
                                        else{
                                            $FORRESIFAX ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR OFFPHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOROFFPHONE = trim($cell);
                                        }
                                        else{
                                            $FOROFFPHONE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOR OFFFAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOROFFFAX = trim($cell);
                                        }
                                        else{
                                            $FOROFFFAX ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MOBILE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MOBILE = trim($cell);
                                        }
                                        else{
                                            $MOBILE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CKYC'))
                                    {
                                        if($cell || $cell != ''){
                                            $CKYC = trim($cell);
                                        }
                                        else{
                                            $CKYC ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('KYC TYPE 1st HOLDER'))
                                    {
                                        if($cell || $cell != ''){
                                            $KYCTYPE1stHOLDER = trim($cell);
                                        }
                                        else{
                                            $KYCTYPE1stHOLDER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('KYC TYPE 2nd HOLDER'))
                                    {
                                        if($cell || $cell != ''){
                                            $KYCTYPE2ndHOLDER = trim($cell);
                                        }
                                        else{
                                            $KYCTYPE2ndHOLDER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('KYC TYPE 3rd HOLDER'))
                                    {
                                        if($cell || $cell != ''){
                                            $KYCTYPE3rdHOLDER = trim($cell);
                                        }
                                        else{
                                            $KYCTYPE3rdHOLDER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('KYC TYPE GUARDIAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $KYCTYPEGUARDIAN = trim($cell);
                                        }
                                        else{
                                            $KYCTYPEGUARDIAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('First Holder CKYC Number'))
                                    {
                                        if($cell || $cell != ''){
                                            $FirstHolderCKYCNumber = trim($cell);
                                        }
                                        else{
                                            $FirstHolderCKYCNumber ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Second Holder CKYC Number'))
                                    {
                                        if($cell || $cell != ''){
                                            $SecondHolderCKYCNumber = trim($cell);
                                        }
                                        else{
                                            $SecondHolderCKYCNumber ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Third Holder CKYC Number'))
                                    {
                                        if($cell || $cell != ''){
                                            $ThirdHolderCKYCNumber = trim($cell);
                                        }
                                        else{
                                            $ThirdHolderCKYCNumber ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Guardian CKYC Number'))
                                    {
                                        if($cell || $cell != ''){
                                            $GuardianCKYCNumber = trim($cell);
                                        }
                                        else{
                                            $GuardianCKYCNumber ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Joint Holder 1 DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $JointHolder1DOB = trim($cell);
                                        }
                                        else{
                                            $JointHolder1DOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Joint Holder 2 DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $JointHolder2DOB = trim($cell);
                                        }
                                        else{
                                            $JointHolder2DOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Guardian CKYC DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $GuardianCKYCDOB = trim($cell);
                                        }
                                        else{
                                            $GuardianCKYCDOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEALER'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEALER = trim($cell);
                                        }
                                        else{
                                            $DEALER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BRANCH'))
                                    {
                                        if($cell || $cell != ''){
                                            $BRANCH = trim($cell);
                                        }
                                        else{
                                            $BRANCH ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CREATED BY'))
                                    {
                                        if($cell || $cell != ''){
                                            $CREATEDBY = trim($cell);
                                        }
                                        else{
                                            $CREATEDBY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $CREATEDAT ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LAST MODIFIED BY'))
                                    {
                                        if($cell || $cell != ''){
                                            $LASTMODIFIEDBY = trim($cell);
                                        }
                                        else{
                                            $LASTMODIFIEDBY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $LASTMODIFIEDAT ='';
                                        }
                                    }
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                            if($countRow != 0)
                            {
                                if($CLIENTCODE=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $clientId = 0;
                                    $res = $this->mfp->check_bsc_client_by_client_code($CLIENTCODE);
                                    
                                    if(!empty($res))
                                    {
                                        $clientId = $res[0]->Id;
                                    }
                                    
                                    
                                    $dataRows[$add_FD_list] =array("CLIENTCODE"=>$CLIENTCODE,"CLIENTHOLDING"=>$CLIENTHOLDING,"TAXSTATUS"=>$TAXSTATUS,"OCCUPATION"=>$OCCUPATION,"FIRSTAPPLICANTNAME"=>$FIRSTAPPLICANTNAME,"SECONDAPPLICANTNAME"=>$SECONDAPPLICANTNAME,"THIRDAPPLICANTNAME"=>$THIRDAPPLICANTNAME,"FIRSTAPPLICANTDOB"=>$FIRSTAPPLICANTDOB,"FIRSTAPPGENDER"=>$FIRSTAPPGENDER,"CLIENTGUARDIAN"=>$CLIENTGUARDIAN,"FIRSTAPPLICANTPAN"=>$FIRSTAPPLICANTPAN,"CLIENTNOMINEE"=>$CLIENTNOMINEE,"CLIENTNOMINEERELATION"=>$CLIENTNOMINEERELATION,"GUARDIANPAN"=>$GUARDIANPAN,"CLIENTTYPE"=>$CLIENTTYPE,"CLIENTDEFAULTDP"=>$CLIENTDEFAULTDP,"CDSLDPID"=>$CDSLDPID,"CDSLCLTID"=>$CDSLCLTID,"NSDLDPID"=>$NSDLDPID,"NSDLCLTID"=>$NSDLCLTID,"ACCTYPE1"=>$ACCTYPE1,"ACCNO1"=>$ACCNO1,"CLIENTMICRNO1"=>$CLIENTMICRNO1,"NEFTIFSCCODE1"=>$NEFTIFSCCODE1,"BANKNAME1"=>$BANKNAME1,"BANKBRANCH1"=>$BANKBRANCH1,"DEFAULTBANKFLAG1"=>$DEFAULTBANKFLAG1,"ACCTYPE2"=>$ACCTYPE2,"ACCNO2"=>$ACCNO2,"CLIENTMICRNO2"=>$CLIENTMICRNO2,"NEFTIFSCCODE2"=>$NEFTIFSCCODE2,"DEFAULTBANKFLAG2"=>$DEFAULTBANKFLAG2,"BANKNAME2"=>$BANKNAME2,"BANKBRANCH2"=>$BANKBRANCH2,"ACCTYPE3"=>$ACCTYPE3,"ACCNO3"=>$ACCNO3,"CLIENTMICRNO3"=>$CLIENTMICRNO3,"NEFTIFSCCODE3"=>$NEFTIFSCCODE3,"DefaultBankFlag3"=>$DefaultBankFlag3,"BANKNAME3"=>$BANKNAME3,"BankBranch3"=>$BankBranch3,"ACCTYPE4"=>$ACCTYPE4,"ACCNO4"=>$ACCNO4,"CLIENTMICRNO4"=>$CLIENTMICRNO4,"NEFTIFSCCODE4"=>$NEFTIFSCCODE4,"DEFAULTBANKFLAG4"=>$DEFAULTBANKFLAG4,"BANKNAME4"=>$BANKNAME4,"BANKBRANCH4"=>$BANKBRANCH4,"ACCTYPE5"=>$ACCTYPE5,"ACCNO5"=>$ACCNO5,"CLIENTMICRNO5"=>$CLIENTMICRNO5,"NEFTIFSCCODE5"=>$NEFTIFSCCODE5,"BANKNAME5"=>$BANKNAME5,"BANKBRANCH5"=>$BANKBRANCH5,"DEFAULTBANKFLAG5"=>$DEFAULTBANKFLAG5,"CLIENTCHEQUENAME5"=>$CLIENTCHEQUENAME5,"ADD1"=>$ADD1,"ADD2"=>$ADD2,"ADD3"=>$ADD3,"CITY"=>$CITY,"CLIENTSTATE"=>$CLIENTSTATE,"PINCODE"=>$PINCODE,"COUNTRY"=>$COUNTRY,"RESIPHONE"=>$RESIPHONE,"RESIFAX"=>$RESIFAX,"OFFICEPHONE"=>$OFFICEPHONE,"CLIENTOFFICEFAX"=>$CLIENTOFFICEFAX,"CLIENTEMAIL"=>$CLIENTEMAIL,"COMMMODE"=>$COMMMODE,"DIVPAYMODE"=>$DIVPAYMODE,"SECONDAPPPAN"=>$SECONDAPPPAN,"THIRDAPPPAN"=>$THIRDAPPPAN,"MAPINNO"=>$MAPINNO,"FORADD1"=>$FORADD1,"FORADD2"=>$FORADD2,"FORADD3"=>$FORADD3,"FORCITY"=>$FORCITY,"FORPINCODE"=>$FORPINCODE,"FORSTATE"=>$FORSTATE,"FORCOUNTRY"=>$FORCOUNTRY,"FORRESIPHONE"=>$FORRESIPHONE,"FORRESIFAX"=>$FORRESIFAX,"FOROFFPHONE"=>$FOROFFPHONE,"FOROFFFAX"=>$FOROFFFAX,"MOBILE"=>$MOBILE,"CKYC"=>$CKYC,"KYCTYPE1stHOLDER"=>$KYCTYPE1stHOLDER,"KYCTYPE2ndHOLDER"=>$KYCTYPE2ndHOLDER,"KYCTYPE3rdHOLDER"=>$KYCTYPE3rdHOLDER,"KYCTYPEGUARDIAN"=>$KYCTYPEGUARDIAN,"FirstHolderCKYCNumber"=>$FirstHolderCKYCNumber,"SecondHolderCKYCNumber"=>$SecondHolderCKYCNumber,"ThirdHolderCKYCNumber"=>$ThirdHolderCKYCNumber,"GuardianCKYCNumber"=>$GuardianCKYCNumber,"JointHolder1DOB"=>$JointHolder1DOB,"JointHolder2DOB"=>$JointHolder2DOB,"GuardianCKYCDOB"=>$GuardianCKYCDOB,"DEALER"=>$DEALER,"BRANCH"=>$BRANCH,"CREATEDBY"=>$CREATEDBY,"CREATEDAT"=>$CREATEDAT,"LASTMODIFIEDBY"=>$LASTMODIFIEDBY,"LASTMODIFIEDAT"=>$LASTMODIFIEDAT);
                                    
                                    if($clientId>0)
                                    {
                                        $inserted = $this->mfp->update_bsc_client( $dataRows[$add_FD_list],$clientId);
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_bsc_client( $dataRows[$add_FD_list]);
                                    }
                                    
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                }
                            }
                            
                            
                            $countRow++;
                        }
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('Bsc Client Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "Client Details Uploaded Successfully";
                                  }
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
            redirect('broker/Mutual_fund_schemes/import_client/');
            // $this->import_client($imp_data);
        }
    }
    
    function BSCClient_import_new()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');

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
                           
                
                    $MEMBERCODE="";
                    $CLIENTCODE="";$PRIMARYHOLDERFIRSTNAME="";$PRIMARYHOLDERMIDDLENAME="";$PRIMARYHOLDERLASTNAME="";$TAXSTATUS="";$GENDER="";$PRIMARYHOLDERDOBINCORPORATION="";
                    $OCCUPATIONCODE="";$HOLDINGNATURE="";$SECONDHOLDERFIRSTNAME="";$SECONDHOLDERMIDDLENAME="";$SECONDHOLDERLASTNAME="";$THIRDHOLDERFIRSTNAME="";
                    $THIRDHOLDERMIDDLENAME="";$THIRDHOLDERLASTNAME="";$SECONDHOLDERDOB="";$THIRDHOLDERDOB="";$GUARDIANFIRSTNAME="";$GUARDIANMIDDLENAME="";
                    $GUARDIANLASTNAME="";$GUARDIANDOB="";$PRIMARYHOLDERPANEXEMPT="";$SECONDHOLDERPANEXEMPT="";$THIRDHOLDERPANEXEMPT="";$GUARDIANPANEXEMPT="";
                    $PRIMARYHOLDERPAN="";$SECONDHOLDERPAN="";$THIRDHOLDERPAN="";$GUARDIANPAN="";$PRIMARYHOLDEREXEMPTCATEGORY="";$SECONDHOLDEREXEMPTCATEGORY="";
                    $THIRDHOLDEREXEMPTCATEGORY="";$GUARDIANEXEMPTCATEGORY="";$CLIENTTYPE="";$PMS="";$DEFAULTDP="";$CDSLDPID="";$CDSLCLTID="";$CMBPID="";$NSDLDPID="";
                    $NSDLCLTID="";$ACCOUNTTYPE1="";$ACCOUNTNO1="";$MICRNO1="";$IFSCCODE1="";$BANKNAME1="";$BANKBRANCH1="";$DEFAULTBANKFLAG1="";$BANK1CREATEDAT="";
                    $BANK1LASTMODIFIEDAT="";$BANK1STATUS="";$ACCOUNTTYPE2="";$ACCOUNTNO2="";$MICRNO2="";$IFSCCODE2="";$BANKNAME2="";$BANKBRANCH2="";$DEFAULTBANKFLAG2="";
                    $BANK2CREATEDAT="";$BANK2LASTMODIFIEDAT="";$BANK2STATUS="";$ACCOUNTTYPE3="";$ACCOUNTNO3="";$MICRNO3="";$IFSCCODE3="";$BANKNAME3="";$BANKBRANCH3="";
                    $DEFAULTBANKFLAG3="";$BANK3CREATEDAT="";$BANK3LASTMODIFIEDAT="";$BANK3STATUS="";$ACCOUNTTYPE4="";$ACCOUNTNO4="";$MICRNO4="";$IFSCCODE4="";$BANKNAME4="";
                    $BANKBRANCH4="";$DEFAULTBANKFLAG4="";$BANK4CREATEDAT="";$BANK4LASTMODIFIEDAT="";$BANK4STATUS="";$ACCOUNTTYPE5="";$ACCOUNTNO5="";$MICRNO5="";$IFSCCODE5="";
                    $BANKNAME5="";$BANKBRANCH5="";$DEFAULTBANKFLAG5="";$BANK5CREATEDAT="";$BANK5LASTMODIFIEDAT="";$BANK5STATUS="";$CHEQUENAME="";$DIVPAYMODE="";$ADDRESS1="";
                    $ADDRESS2="";$ADDRESS3="";$CITY="";$STATE="";$PINCODE="";$COUNTRY="";$RESIPHONE="";$RESIFAX="";$OFFICEPHONE="";$OFFICEFAX="";$EMAIL="";$COMMUNICATIONMODE="";
                    $FOREIGNADDRESS1="";$FOREIGNADDRESS2="";$FOREIGNADDRESS3="";$FOREIGNADDRESSCITY="";$FOREIGNADDRESSPINCODE="";$FOREIGNADDRESSSTATE="";$FOREIGNADDRESSCOUNTRY="";
                    $FOREIGNADDRESSRESIPHONE="";$FOREIGNADDRESSFAX="";$FOREIGNADDRESSOFFPHONE="";$FOREIGNADDRESSOFFFAX="";$INDIANMOBILENO="";$NOMINEE1NAME="";
                    $NOMINEE1RELATIONSHIP="";$NOMINEE1APPLICABLE="";$NOMINEE1MINORFLAG="";$NOMINEE1DOB="";$NOMINEE1GUARDIAN="";$NOMINEE2NAME="";$NOMINEE2RELATIONSHIP="";
                    $NOMINEE2APPLICABLE="";$NOMINEE2DOB="";$NOMINEE2MINORFLAG="";$NOMINEE2GUARDIAN="";$NOMINEE3NAME="";$NOMINEE3RELATIONSHIP="";$NOMINEE3APPLICABLE="";
                    $NOMINEE3DOB="";$NOMINEE3MINORFLAG="";$NOMINEE3GUARDIAN="";$PRIMARYHOLDERKYCTYPE="";$PRIMARYHOLDERCKYCNUMBER="";$SECONDHOLDERKYCTYPE="";$SECONDHOLDERCKYCNUMBER="";
                    $THIRDHOLDERKYCTYPE="";$THIRDHOLDERCKYCNUMBER="";$GUARDIANKYCTYPE="";$GUARDIANCKYCNUMBER="";$PRIMARYHOLDERKRAEXEMPTREFNO="";$SECONDHOLDERKRAEXEMPTREFNO="";
                    $THIRDHOLDERKRAEXEMPTREFNO="";$GUARDIANEXEMPTREFNO="";$AADHAARUPDATED="";$MAPINID="";$PAPERLESSFLAG="";$LEINO="";$LEIVALIDITY="";$EMAILDECLARATIONFLAG="";
                    $MOBILEDECLARATIONFLAG="";$BRANCH="";$DEALER="";$NOMINATIONOPT="";$NOMINATIONAUTHENTICATIONMODE="";$NOMINEE1PAN="";$NOMINEE1GUARDIANPAN="";$NOMINEE2PAN="";
                    $NOMINEE2GUARDIANPAN="";$NOMINEE3PAN="";$NOMINEE3GUARDIANPAN="";$SECONDHOLDEREMAIL="";$SECONDHOLDEREMAILDECLARATION="";$SECONDHOLDERMOBILE="";$SECONDHOLDERMOBILEDECLARATION="";
                    $THIRDHOLDEREMAIL="";$THIRDHOLDEREMAILDECLARATION="";$THIRDHOLDERMOBILE="";$THIRDHOLDERMOBILEDECLARATION="";$NOMINATIONFLAG="";
                    $NOMINATIONAUTHENTICATIONDATE="";$CREATEDBY="";$CREATEDAT="";$LASTMODIFIEDBY="";$LASTMODIFIEDAT="";

                    
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                          
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);


                    // Following comment code will use for generating code for multiple columns
                    /*echo "<pre>";
                    print_r($excelData);

                    $k='';$m='';
                    for($i=0;$i<count($excelData[0]);$i++){
                        $j = "strtoupper(cell)==strtoupper('".$excelData[0][$i]."') || ";
                        $k = $k.$j;

                        $l = '$'.str_replace(" ", "", $excelData[0][$i]).'="";';
                        $l = '"'.str_replace(" ", "", $excelData[0][$i]).'"=>$'.str_replace(" ", "", $excelData[0][$i]).',';
                        $m = $m.$l;

                        echo "else if(strtoupper($dataColumns[$countCell]) ===strtoupper('".$excelData[0][$i]."'))
                        {
                            if($cell || $cell != ''){
                                $".str_replace(' ', '', $excelData[0][$i])." = trim($cell);
                            }
                            else{
                                $".str_replace(' ', '', $excelData[0][$i])." ='';
                            }
                        }";      
                    }
                    echo $k;
                    echo "<br>";
                    echo $m;
                    die();  */


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

                                    																													
                                    if(strtoupper($cell)==strtoupper('MEMBER CODE') || strtoupper($cell)==strtoupper('CLIENT CODE') || 
                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER FIRST NAME') || strtoupper($cell)==strtoupper('PRIMARY HOLDER MIDDLE NAME') || 
                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER LAST NAME') || strtoupper($cell)==strtoupper('TAX STATUS') || 
                                       strtoupper($cell)==strtoupper('GENDER') || strtoupper($cell)==strtoupper('PRIMARY HOLDER DOB/INCORPORATION') || 
                                       strtoupper($cell)==strtoupper('OCCUPATION CODE') || strtoupper($cell)==strtoupper('HOLDING NATURE') || 
                                       strtoupper($cell)==strtoupper('SECOND HOLDER FIRST NAME') || strtoupper($cell)==strtoupper('SECOND HOLDER MIDDLE NAME') || 
                                       strtoupper($cell)==strtoupper('SECOND HOLDER LAST NAME') || strtoupper($cell)==strtoupper('THIRD HOLDER FIRST NAME') || 
                                       strtoupper($cell)==strtoupper('THIRD HOLDER MIDDLE NAME') || strtoupper($cell)==strtoupper('THIRD HOLDER LAST NAME') || 
                                       strtoupper($cell)==strtoupper('SECOND HOLDER DOB') || strtoupper($cell)==strtoupper('THIRD HOLDER DOB') || 
                                       strtoupper($cell)==strtoupper('GUARDIAN FIRST NAME') || strtoupper($cell)==strtoupper('GUARDIAN MIDDLE NAME') || 
                                       strtoupper($cell)==strtoupper('GUARDIAN LAST NAME') || strtoupper($cell)==strtoupper('GUARDIAN DOB') || 
                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER PAN EXEMPT') || strtoupper($cell)==strtoupper('SECOND HOLDER PAN EXEMPT') || 
                                       strtoupper($cell)==strtoupper('THIRD HOLDER PAN EXEMPT') || strtoupper($cell)==strtoupper('GUARDIAN PAN EXEMPT') || 
                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER PAN') || strtoupper($cell)==strtoupper('SECOND HOLDER PAN') || 
                                       strtoupper($cell)==strtoupper('THIRD HOLDER PAN') || strtoupper($cell)==strtoupper('GUARDIAN PAN') || 
                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER-EXEMPT CATEGORY') || strtoupper($cell)==strtoupper('SECOND HOLDER EXEMPT CATEGORY') || 
                                       strtoupper($cell)==strtoupper('THIRD HOLDER EXEMPT CATEGORY') || strtoupper($cell)==strtoupper('GUARDIAN EXEMPT CATEGORY') || 
                                       strtoupper($cell)==strtoupper('CLIENT TYPE') || strtoupper($cell)==strtoupper('PMS') || strtoupper($cell)==strtoupper('DEFAULT DP') || 
                                       strtoupper($cell)==strtoupper('CDSL DPID') || strtoupper($cell)==strtoupper('CDSLCLTID') || strtoupper($cell)==strtoupper('CMBP ID') || 
                                       strtoupper($cell)==strtoupper('NSDLDPID') || strtoupper($cell)==strtoupper('NSDLCLTID') || 
                                       strtoupper($cell)==strtoupper('ACCOUNT TYPE 1') || strtoupper($cell)==strtoupper('ACCOUNT NO 1') || strtoupper($cell)==strtoupper('MICR NO 1') || 
                                       strtoupper($cell)==strtoupper('IFSC CODE 1') ||strtoupper($cell)==strtoupper('BANK NAME 1') || strtoupper($cell)==strtoupper('BANK BRANCH 1') || 
                                       strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 1') || strtoupper($cell)==strtoupper('BANK1 CREATED AT') || 
                                       strtoupper($cell)==strtoupper('BANK1 LAST MODIFIED AT') ||strtoupper($cell)==strtoupper('BANK1 STATUS') || 

                                       strtoupper($cell)==strtoupper('ACCOUNT TYPE 2') || strtoupper($cell)==strtoupper('ACCOUNT NO 2') || strtoupper($cell)==strtoupper('MICR NO 2') || 
                                       strtoupper($cell)==strtoupper('IFSC CODE 2') ||strtoupper($cell)==strtoupper('BANK NAME 2') || strtoupper($cell)==strtoupper('BANK BRANCH 2') || 
                                       strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 2') || strtoupper($cell)==strtoupper('BANK2 CREATED AT') || 
                                       strtoupper($cell)==strtoupper('BANK2 LAST MODIFIED AT') ||strtoupper($cell)==strtoupper('BANK2 STATUS') || 

                                       strtoupper($cell)==strtoupper('ACCOUNT TYPE 3') || strtoupper($cell)==strtoupper('ACCOUNT NO 3') || strtoupper($cell)==strtoupper('MICR NO 3') || 
                                       strtoupper($cell)==strtoupper('IFSC CODE 3') ||strtoupper($cell)==strtoupper('BANK NAME 3') || strtoupper($cell)==strtoupper('BANK BRANCH 3') || 
                                       strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 3') || strtoupper($cell)==strtoupper('BANK3 CREATED AT') || 
                                       strtoupper($cell)==strtoupper('BANK3 LAST MODIFIED AT') ||strtoupper($cell)==strtoupper('BANK3 STATUS') || 

                                       strtoupper($cell)==strtoupper('ACCOUNT TYPE 4') || strtoupper($cell)==strtoupper('ACCOUNT NO 4') || strtoupper($cell)==strtoupper('MICR NO 4') || 
                                       strtoupper($cell)==strtoupper('IFSC CODE 4') ||strtoupper($cell)==strtoupper('BANK NAME 4') || strtoupper($cell)==strtoupper('BANK BRANCH 4') || 
                                       strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 4') || strtoupper($cell)==strtoupper('BANK4 CREATED AT') || 
                                       strtoupper($cell)==strtoupper('BANK4 LAST MODIFIED AT') ||strtoupper($cell)==strtoupper('BANK4 STATUS') || 

                                       strtoupper($cell)==strtoupper('ACCOUNT TYPE 5') || strtoupper($cell)==strtoupper('ACCOUNT NO 5') || strtoupper($cell)==strtoupper('MICR NO 5') || 
                                       strtoupper($cell)==strtoupper('IFSC CODE 5') ||strtoupper($cell)==strtoupper('BANK NAME 5') || strtoupper($cell)==strtoupper('BANK BRANCH 5') || 
                                       strtoupper($cell)==strtoupper('DEFAULT BANK FLAG 5') || strtoupper($cell)==strtoupper('BANK5 CREATED AT') || 
                                       strtoupper($cell)==strtoupper('BANK5 LAST MODIFIED AT') ||strtoupper($cell)==strtoupper('BANK5 STATUS') || 
                                      
                                       strtoupper($cell)==strtoupper('CHEQUE NAME') || strtoupper($cell)==strtoupper('DIV PAY MODE') || 
                                       strtoupper($cell)==strtoupper('ADDRESS 1') || strtoupper($cell)==strtoupper('ADDRESS 2') || 
                                       strtoupper($cell)==strtoupper('ADDRESS 3') || strtoupper($cell)==strtoupper('CITY') || 
                                       strtoupper($cell)==strtoupper('STATE') || strtoupper($cell)==strtoupper('PINCODE') || 
                                       strtoupper($cell)==strtoupper('COUNTRY') || strtoupper($cell)==strtoupper('RESI PHONE') || 
                                       strtoupper($cell)==strtoupper('RESI FAX') || strtoupper($cell)==strtoupper('OFFICE PHONE') || 
                                       strtoupper($cell)==strtoupper('OFFICE FAX') || strtoupper($cell)==strtoupper('EMAIL') ||
                                       strtoupper($cell)==strtoupper('COMMUNICATION MODE') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS 1') || 
                                       strtoupper($cell)==strtoupper('FOREIGN ADDRESS 2') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS 3') ||
                                       strtoupper($cell)==strtoupper('FOREIGN ADDRESS CITY') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS PINCODE') || 
                                       strtoupper($cell)==strtoupper('FOREIGN ADDRESS STATE') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS COUNTRY') || 
                                       strtoupper($cell)==strtoupper('FOREIGN ADDRESS RESI PHONE') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS FAX') || 
                                       strtoupper($cell)==strtoupper('FOREIGN ADDRESS OFF PHONE') || strtoupper($cell)==strtoupper('FOREIGN ADDRESS OFF FAX') || 
                                       strtoupper($cell)==strtoupper('INDIAN MOBILE NO') || strtoupper($cell)==strtoupper('NOMINEE 1 NAME') || 

                                       strtoupper($cell)==strtoupper('NOMINEE 1 RELATIONSHIP') || strtoupper($cell)==strtoupper('NOMINEE 1 APPLICABLE (%)') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 1 MINOR FLAG') || strtoupper($cell)==strtoupper('NOMINEE 1 DOB') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 1 GUARDIAN') || strtoupper($cell)==strtoupper('NOMINEE 2 NAME') || 

                                       strtoupper($cell)==strtoupper('NOMINEE 2 RELATIONSHIP') || strtoupper($cell)==strtoupper('NOMINEE 2 APPLICABLE (%)') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 2 MINOR FLAG') || strtoupper($cell)==strtoupper('NOMINEE 2 DOB') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 2 GUARDIAN') || strtoupper($cell)==strtoupper('NOMINEE 3 NAME') || 

                                       strtoupper($cell)==strtoupper('NOMINEE 3 RELATIONSHIP') || strtoupper($cell)==strtoupper('NOMINEE 3 APPLICABLE (%)') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 3 MINOR FLAG') || strtoupper($cell)==strtoupper('NOMINEE 3 DOB') || 
                                       strtoupper($cell)==strtoupper('NOMINEE 3 GUARDIAN') || strtoupper($cell)==strtoupper('PRIMARY HOLDER KYC TYPE') || 

                                       strtoupper($cell)==strtoupper('PRIMARY HOLDER CKYC NUMBER') || strtoupper($cell)==strtoupper('SECOND HOLDER KYC TYPE') || 
                                       strtoupper($cell)==strtoupper('SECOND HOLDER CKYC NUMBER') || strtoupper($cell)==strtoupper('THIRD HOLDER KYC TYPE') || 
                                       strtoupper($cell)==strtoupper('THIRD HOLDER CKYC NUMBER') || strtoupper($cell)==strtoupper('GUARDIAN KYC TYPE') || 
                                       strtoupper($cell)==strtoupper('GUARDIAN CKYC NUMBER') || strtoupper($cell)==strtoupper('PRIMARY HOLDER KRA EXEMPT REF NO') || 
                                       strtoupper($cell)==strtoupper('SECOND HOLDER KRA EXEMPT REF NO') || strtoupper($cell)==strtoupper('THIRD HOLDER KRA EXEMPT REF NO') || 
                                       strtoupper($cell)==strtoupper('GUARDIAN EXEMPT REF NO') || strtoupper($cell)==strtoupper('AADHAAR UPDATED') || 
                                       	strtoupper($cell)==strtoupper('MAPIN ID') || 
                                       strtoupper($cell)==strtoupper('PAPERLESS_FLAG') || strtoupper($cell)==strtoupper('LEI NO') || 
                                       strtoupper($cell)==strtoupper('LEI VALIDITY') || strtoupper($cell)==strtoupper('EMAIL DECLARATION FLAG') || 
                                       strtoupper($cell)==strtoupper('MOBILE DECLARATION FLAG') || strtoupper($cell)==strtoupper('BRANCH') ||
                                       strtoupper($cell)==strtoupper('DEALER') || strtoupper($cell)==strtoupper('NOMINATION OPT') ||
                                       strtoupper($cell)==strtoupper('NOMINATION AUTHENTICATION MODE') || strtoupper($cell)==strtoupper('NOMINEE 1 PAN') ||
                                       strtoupper($cell)==strtoupper('NOMINEE 1 GUARDIAN PAN') || strtoupper($cell)==strtoupper('NOMINEE 2 PAN') ||
                                       strtoupper($cell)==strtoupper('NOMINEE 2 GUARDIAN PAN') || strtoupper($cell)==strtoupper('NOMINEE 3 PAN') ||
                                       strtoupper($cell)==strtoupper('NOMINEE 3 GUARDIAN PAN') || strtoupper($cell)==strtoupper('SECOND HOLDER EMAIL') ||
                                       strtoupper($cell)==strtoupper('SECOND HOLDER EMAIL DECLARATION') || strtoupper($cell)==strtoupper('SECOND HOLDER MOBILE') ||
                                       strtoupper($cell)==strtoupper('SECOND HOLDER MOBILE DECLARATION') || strtoupper($cell)==strtoupper('THIRD HOLDER EMAIL') ||
                                       strtoupper($cell)==strtoupper('THIRD HOLDER EMAIL DECLARATION') || strtoupper($cell)==strtoupper('THIRD HOLDER MOBILE') ||
                                       strtoupper($cell)==strtoupper('THIRD HOLDER MOBILE DECLARATION') || strtoupper($cell)==strtoupper('NOMINATION FLAG') ||
                                       strtoupper($cell)==strtoupper('NOMINATION AUTHENTICATION DATE') || strtoupper($cell)==strtoupper('CREATED BY') ||
                                       strtoupper($cell)==strtoupper('CREATED AT') || strtoupper($cell)==strtoupper('LAST MODIFIED BY') ||
                                       strtoupper($cell)==strtoupper('LAST MODIFIED AT'))

                                       																														                                
                                    {
                                        $dataColumns[$countCell] = $cell;
                                        $countCell++;
                                        $uploadedStatus = 2;
                                        continue;
                                    }
                                    else
                                    {
                                      //  var_dump($dataColumns);
                                        //var_dump($cell);
                                        //exit();
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                    
                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('MEMBER CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MEMBERCODE = trim($cell);
                                        }
                                        else{
                                            $MEMBERCODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTCODE = trim($cell);
                                        }
                                        else{
                                            $CLIENTCODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER FIRST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERFIRSTNAME = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERFIRSTNAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER MIDDLE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERMIDDLENAME = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERMIDDLENAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER LAST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERLASTNAME = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERLASTNAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('TAX STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $TAXSTATUS = trim($cell);
                                        }
                                        else{
                                            $TAXSTATUS ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GENDER'))
                                    {
                                        if($cell || $cell != ''){
                                            $GENDER = trim($cell);
                                        }
                                        else{
                                            $GENDER ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER DOB/INCORPORATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERDOBINCORPORATION = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERDOBINCORPORATION ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('OCCUPATION CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $OCCUPATIONCODE = trim($cell);
                                        }
                                        else{
                                            $OCCUPATIONCODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('HOLDING NATURE'))
                                    {
                                        if($cell || $cell != ''){
                                            $HOLDINGNATURE = trim($cell);
                                        }
                                        else{
                                            $HOLDINGNATURE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER FIRST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERFIRSTNAME = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERFIRSTNAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER MIDDLE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERMIDDLENAME = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERMIDDLENAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER LAST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERLASTNAME = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERLASTNAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER FIRST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERFIRSTNAME = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERFIRSTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER MIDDLE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERMIDDLENAME = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERMIDDLENAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER LAST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERLASTNAME = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERLASTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERDOB = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERDOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERDOB = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERDOB ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN FIRST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANFIRSTNAME = trim($cell);
                                        }
                                        else{
                                            $GUARDIANFIRSTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN MIDDLE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANMIDDLENAME = trim($cell);
                                        }
                                        else{
                                            $GUARDIANMIDDLENAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN LAST NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANLASTNAME = trim($cell);
                                        }
                                        else{
                                            $GUARDIANLASTNAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANDOB = trim($cell);
                                        }
                                        else{
                                            $GUARDIANDOB ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER PAN EXEMPT'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERPANEXEMPT = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERPANEXEMPT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER PAN EXEMPT'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERPANEXEMPT = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERPANEXEMPT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER PAN EXEMPT'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERPANEXEMPT = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERPANEXEMPT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN PAN EXEMPT'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANPANEXEMPT = trim($cell);
                                        }
                                        else{
                                            $GUARDIANPANEXEMPT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERPAN = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERPAN = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERPAN ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERPAN = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $GUARDIANPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER-EXEMPT CATEGORY'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDEREXEMPTCATEGORY = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDEREXEMPTCATEGORY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER EXEMPT CATEGORY'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDEREXEMPTCATEGORY = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDEREXEMPTCATEGORY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER EXEMPT CATEGORY'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDEREXEMPTCATEGORY = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDEREXEMPTCATEGORY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN EXEMPT CATEGORY'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANEXEMPTCATEGORY = trim($cell);
                                        }
                                        else{
                                            $GUARDIANEXEMPTCATEGORY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT TYPE'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTTYPE = trim($cell);
                                        }
                                        else{
                                            $CLIENTTYPE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PMS'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $PMS = trim($cell);
                                        }
                                        else{
                                            $PMS ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT DP'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTDP = trim($cell);
                                        }
                                        else{
                                            $DEFAULTDP ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CDSL DPID'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $CDSLDPID = trim($cell);
                                        }
                                        else{
                                            $CDSLDPID ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CDSLCLTID'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $CDSLCLTID = trim($cell);
                                        }
                                        else{
                                            $CDSLCLTID ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CMBP ID'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $CMBPID = trim($cell);
                                        }
                                        else{
                                            $CMBPID ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NSDLDPID'))                                     
                                    {
                                        if($cell || $cell != ''){
                                            $NSDLDPID = trim($cell);
                                        }
                                        else{
                                            $NSDLDPID ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NSDLCLTID'))
                                    {
                                        if($cell || $cell != ''){
                                            $NSDLCLTID = trim($cell);
                                        }
                                        else{
                                            $NSDLCLTID ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT TYPE 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTTYPE1 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT NO 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTNO1 = trim($cell);
                                        }
                                        else{
                                            $ACCOUNTNO1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MICR NO 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $MICRNO1 = trim($cell);
                                        }
                                        else{
                                            $MICRNO1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IFSC CODE 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $IFSCCODE1 = trim($cell);
                                        }
                                        else{
                                            $IFSCCODE1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME1 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME1 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH1 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH1 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG1 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK1 CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK1CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK1CREATEDAT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK1 LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK1LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK1LASTMODIFIEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK1 STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK1STATUS = trim($cell);
                                        }
                                        else{
                                            $BANK1STATUS ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT TYPE 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTTYPE2 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT NO 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTNO2 = trim($cell);
                                        }
                                        else{
                                            $ACCOUNTNO2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MICR NO 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $MICRNO2 = trim($cell);
                                        }
                                        else{
                                            $MICRNO2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IFSC CODE 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $IFSCCODE2 = trim($cell);
                                        }
                                        else{
                                            $IFSCCODE2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME2 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME2 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH2 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH2 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG2 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK2 CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK2CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK2CREATEDAT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK2 LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK2LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK2LASTMODIFIEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK2 STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK2STATUS = trim($cell);
                                        }
                                        else{
                                            $BANK2STATUS ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT TYPE 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTTYPE3 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT NO 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTNO3 = trim($cell);
                                        }
                                        else{
                                            $ACCOUNTNO3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MICR NO 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $MICRNO3 = trim($cell);
                                        }
                                        else{
                                            $MICRNO3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IFSC CODE 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $IFSCCODE3 = trim($cell);
                                        }
                                        else{
                                            $IFSCCODE3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME3 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME3 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH3 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH3 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG3 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK3 CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK3CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK3CREATEDAT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK3 LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK3LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK3LASTMODIFIEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK3 STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK3STATUS = trim($cell);
                                        }
                                        else{
                                            $BANK3STATUS ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT TYPE 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTTYPE4 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE4 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT NO 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTNO4 = trim($cell);
                                        }
                                        else{
                                            $ACCOUNTNO4 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MICR NO 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $MICRNO4 = trim($cell);
                                        }
                                        else{
                                            $MICRNO4 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IFSC CODE 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $IFSCCODE4 = trim($cell);
                                        }
                                        else{
                                            $IFSCCODE4 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME4 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME4 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH4 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH4 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 4'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG4 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG4 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK4 CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK4CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK4CREATEDAT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK4 LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK4LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK4LASTMODIFIEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK4 STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK4STATUS = trim($cell);
                                        }
                                        else{
                                            $BANK4STATUS ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT TYPE 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTTYPE5 = trim($cell);
                                        }
                                        else{
                                            $ACCTYPE5 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ACCOUNT NO 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $ACCOUNTNO5 = trim($cell);
                                        }
                                        else{
                                            $ACCOUNTNO5 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MICR NO 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $MICRNO5 = trim($cell);
                                        }
                                        else{
                                            $MICRNO5 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IFSC CODE 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $IFSCCODE5 = trim($cell);
                                        }
                                        else{
                                            $IFSCCODE5 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME5 = trim($cell);
                                        }
                                        else{
                                            $BANKNAME5 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH5 = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH5 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEFAULT BANK FLAG 5'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEFAULTBANKFLAG5 = trim($cell);
                                        }
                                        else{
                                            $DEFAULTBANKFLAG5 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK5 CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK5CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK5CREATEDAT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK5 LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK5LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $BANK5LASTMODIFIEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK5 STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANK5STATUS = trim($cell);
                                        }
                                        else{
                                            $BANK5STATUS ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CHEQUE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $CHEQUENAME = trim($cell);
                                        }
                                        else{
                                            $CHEQUENAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DIV PAY MODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $DIVPAYMODE = trim($cell);
                                        }
                                        else{
                                            $DIVPAYMODE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADDRESS 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADDRESS1 = trim($cell);
                                        }
                                        else{
                                            $ADDRESS1 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADDRESS 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADDRESS2 = trim($cell);
                                        }
                                        else{
                                            $ADDRESS2 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('ADDRESS 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $ADDRESS3 = trim($cell);
                                        }
                                        else{
                                            $ADDRESS3 ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CITY'))
                                    {
                                        if($cell || $cell != ''){
                                            $CITY = trim($cell);
                                        }
                                        else{
                                            $CITY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('STATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $STATE = trim($cell);
                                        }
                                        else{
                                            $STATE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PINCODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $PINCODE = trim($cell);
                                        }
                                        else{
                                            $PINCODE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('COUNTRY'))
                                    {
                                        if($cell || $cell != ''){
                                            $COUNTRY = trim($cell);
                                        }
                                        else{
                                            $COUNTRY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RESI PHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $RESIPHONE = trim($cell);
                                        }
                                        else{
                                            $RESIPHONE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('OFFICE PHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $OFFICEPHONE = trim($cell);
                                        }
                                        else{
                                            $OFFICEPHONE ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('OFFICE FAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $OFFICEFAX = trim($cell);
                                        }
                                        else{
                                            $OFFICEFAX ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('EMAIL'))
                                    {
                                        if($cell || $cell != ''){
                                            $EMAIL = trim($cell);
                                        }
                                        else{
                                            $EMAIL ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('COMMUNICATION MODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $COMMUNICATIONMODE = trim($cell);
                                        }
                                        else{
                                            $COMMUNICATIONMODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESS1 = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESS1 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESS2 = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESS2 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS 3'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESS3 = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESS3 ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS CITY'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSCITY = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSCITY ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS PINCODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSPINCODE = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSPINCODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS STATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSSTATE = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSSTATE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS COUNTRY'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSCOUNTRY = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSCOUNTRY ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS RESI PHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSRESIPHONE = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSRESIPHONE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS FAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSFAX = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSFAX ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS OFF PHONE'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSOFFPHONE = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSOFFPHONE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOREIGN ADDRESS OFF FAX'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOREIGNADDRESSOFFFAX = trim($cell);
                                        }
                                        else{
                                            $FOREIGNADDRESSOFFFAX ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('INDIAN MOBILE NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $INDIANMOBILENO = trim($cell);
                                        }
                                        else{
                                            $INDIANMOBILENO ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1NAME = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1NAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 RELATIONSHIP'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1RELATIONSHIP = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1RELATIONSHIP ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 APPLICABLE (%)'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1APPLICABLE = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1APPLICABLE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 MINOR FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1MINORFLAG = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1MINORFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1DOB = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1DOB ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 GUARDIAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1GUARDIAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1GUARDIAN ='';
                                        }
                                    }                                      
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2NAME = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2NAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 RELATIONSHIP'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2RELATIONSHIP = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2RELATIONSHIP ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 APPLICABLE (%)'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2APPLICABLE = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2APPLICABLE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 MINOR FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2MINORFLAG = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2MINORFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2DOB = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2DOB ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 GUARDIAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2GUARDIAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2GUARDIAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3NAME = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3NAME ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 RELATIONSHIP'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3RELATIONSHIP = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3RELATIONSHIP ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 APPLICABLE (%)'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3APPLICABLE = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3APPLICABLE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 MINOR FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3MINORFLAG = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3MINORFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 DOB'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3DOB = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3DOB ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 GUARDIAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3GUARDIAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3GUARDIAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER KYC TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERKYCTYPE = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERKYCTYPE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER KYC NUMBER'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERKYCNUMBER = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERKYCNUMBER ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER KYC TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERKYCTYPE = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERKYCTYPE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER KYC NUMBER'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERKYCNUMBER = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERKYCNUMBER ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER KYC TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERKYCTYPE = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERKYCTYPE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER KYC NUMBER'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERKYCNUMBER = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERKYCNUMBER ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN KYC TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANKYCTYPE = trim($cell);
                                        }
                                        else{
                                            $GUARDIANKYCTYPE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN KYC NUMBER'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANKYCNUMBER = trim($cell);
                                        }
                                        else{
                                            $GUARDIANKYCNUMBER ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PRIMARY HOLDER KRA EXEMPT REF NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $PRIMARYHOLDERKRAEXEMPTREFNO = trim($cell);
                                        }
                                        else{
                                            $PRIMARYHOLDERKRAEXEMPTREFNO ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER KRA EXEMPT REF NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERKRAEXEMPTREFNO = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERKRAEXEMPTREFNO ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER KRA EXEMPT REF NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERKRAEXEMPTREFNO = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERKRAEXEMPTREFNO ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN EXEMPT REF NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANEXEMPTREFNO = trim($cell);
                                        }
                                        else{
                                            $GUARDIANEXEMPTREFNO ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AADHAAR UPDATED'))
                                    {
                                        if($cell || $cell != ''){
                                            $AADHAARUPDATED= trim($cell);
                                        }
                                        else{
                                            $AADHAARUPDATED ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MAPIN ID'))
                                    {
                                        if($cell || $cell != ''){
                                            $MAPINID = trim($cell);
                                        }
                                        else{
                                            $MAPINID ='';
                                        }
                                    }
                                    
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PAPERLESS_FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $PAPERLESSFLAG = trim($cell);
                                        }
                                        else{
                                            $PAPERLESSFLAG ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LEI NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $LEINO = trim($cell);
                                        }
                                        else{
                                            $LEINO ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LEI VALIDITY'))
                                    {
                                        if($cell || $cell != ''){
                                            $LEIVALIDITY = trim($cell);
                                        }
                                        else{
                                            $LEIVALIDITY ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('EMAIL DECLARATION FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $EMAILDECLARATIONFLAG = trim($cell);
                                        }
                                        else{
                                            $EMAILDECLARATIONFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MOBILE DECLARATION FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $MOBILEDECLARATIONFLAG = trim($cell);
                                        }
                                        else{
                                            $MOBILEDECLARATIONFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BRANCH'))
                                    {
                                        if($cell || $cell != ''){
                                            $BRANCH = trim($cell);
                                        }
                                        else{
                                            $BRANCH ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DEALER'))
                                    {
                                        if($cell || $cell != ''){
                                            $DEALER = trim($cell);
                                        }
                                        else{
                                            $DEALER ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINATION OPT'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINATIONOPT = trim($cell);
                                        }
                                        else{
                                            $NOMINATIONOPT ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINATION AUTHENTICATION MODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINATIONAUTHENTICATIONMODE = trim($cell);
                                        }
                                        else{
                                            $NOMINATIONAUTHENTICATIONMODE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1PAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1PAN ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 1 GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE1GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE1GUARDIANPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2PAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2PAN ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 2 GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE2GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE2GUARDIANPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3PAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3PAN ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINEE 3 GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINEE3GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $NOMINEE3GUARDIANPAN ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER EMAIL'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDEREMAIL = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDEREMAIL ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER EMAIL DECLARATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDEREMAILDECLARATION = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDEREMAILDECLARATION ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER MOBILE'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERMOBILE = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERMOBILE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('SECOND HOLDER MOBILE DECLARATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $SECONDHOLDERMOBILEDECLARATION = trim($cell);
                                        }
                                        else{
                                            $SECONDHOLDERMOBILEDECLARATION ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER EMAIL'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDEREMAIL = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDEREMAIL ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER EMAIL DECLARATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDEREMAILDECLARATION = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDEREMAILDECLARATION ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER MOBILE'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERMOBILE = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERMOBILE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('THIRD HOLDER MOBILE DECLARATION'))
                                    {
                                        if($cell || $cell != ''){
                                            $THIRDHOLDERMOBILEDECLARATION = trim($cell);
                                        }
                                        else{
                                            $THIRDHOLDERMOBILEDECLARATION ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINATION FLAG'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINATIONFLAG = trim($cell);
                                        }
                                        else{
                                            $NOMINATIONFLAG ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NOMINATION AUTHENTICATION DATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $NOMINATIONAUTHENTICATIONDATE = trim($cell);
                                        }
                                        else{
                                            $NOMINATIONAUTHENTICATIONDATE ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CREATED BY'))
                                    {
                                        if($cell || $cell != ''){
                                            $CREATEDBY = trim($cell);
                                        }
                                        else{
                                            $CREATEDBY ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CREATED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $CREATEDAT = trim($cell);
                                        }
                                        else{
                                            $CREATEDAT ='';
                                        }
                                    }
                                     else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LAST MODIFIED BY'))
                                    {
                                        if($cell || $cell != ''){
                                            $LASTMODIFIEDBY = trim($cell);
                                        }
                                        else{
                                            $LASTMODIFIEDBY ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('LAST MODIFIED AT'))
                                    {
                                        if($cell || $cell != ''){
                                            $LASTMODIFIEDAT = trim($cell);
                                        }
                                        else{
                                            $LASTMODIFIEDAT ='';
                                        }
                                    }
                                     
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                        
                            if($countRow != 0)
                            {
                                if($CLIENTCODE=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $clientId = 0;
                                    $res = $this->mfp->check_bsc_client_by_client_code_new($CLIENTCODE);
                                    
                                    if(!empty($res))
                                    {
                                        $clientId = $res[0]->Id;
                                    }
                                
                                echo $MEMBERCODE;

                                     $dataRows[$add_FD_list] =array("MemberCode"=>$MEMBERCODE,"ClientCode"=>$CLIENTCODE,"PrimaryHolderFirstName"=>$PRIMARYHOLDERFIRSTNAME
                                                                ,"PrimaryHolderMiddleName"=>$PRIMARYHOLDERMIDDLENAME,"PrimaryHolderLastName"=>$PRIMARYHOLDERLASTNAME
                                                                ,"TaxStatus"=>$TAXSTATUS,"Gender"=>$GENDER,"PrimaryHolderDOBIncorporation"=>$PRIMARYHOLDERDOBINCORPORATION
                                                            ,"OccupationCode"=>$OCCUPATIONCODE,"HoldingNature"=>$HOLDINGNATURE,"SecondHolderFirstName"=>$SECONDHOLDERFIRSTNAME
                                                            ,"SecondHolderMiddleName"=>$SECONDHOLDERMIDDLENAME,"SecondHolderLastName"=>$SECONDHOLDERLASTNAME
                                                            ,"ThirdHolderFirstName"=>$THIRDHOLDERFIRSTNAME,"ThirdHolderMiddleName"=>$THIRDHOLDERMIDDLENAME
                                                            ,"ThirdHolderLastName"=>$THIRDHOLDERLASTNAME,"SecondHolderDOB"=>$SECONDHOLDERDOB
                                                            ,"ThirdHolderDOB"=>$THIRDHOLDERDOB,"GuardianFirstName"=>$GUARDIANFIRSTNAME
                                                            ,"GuardianMiddleName"=>$GUARDIANMIDDLENAME,"GuardianLastName"=>$GUARDIANLASTNAME
                                                            ,"GuardianDOB"=>$GUARDIANDOB,"PrimaryHolderPANExempt"=>$PRIMARYHOLDERPANEXEMPT
                                                            ,"SecondHolderPANExempt"=>$SECONDHOLDERPANEXEMPT,"ThirdHolderPANExempt"=>$THIRDHOLDERPANEXEMPT
                                                            ,"GuardianPANExempt"=>$GUARDIANPANEXEMPT,"PrimaryHolderPAN"=>$PRIMARYHOLDERPAN
                                                            ,"SecondHolderPAN"=>$SECONDHOLDERPAN,"ThirdHolderPAN"=>$THIRDHOLDERPAN
                                                            ,"GuardianPAN"=>$GUARDIANPAN,"PrimaryHolderExemptCategory"=>$PRIMARYHOLDEREXEMPTCATEGORY
                                                            ,"SecondHolderExemptCategory"=>$SECONDHOLDEREXEMPTCATEGORY,"ThirdHolderExemptCategory"=>$THIRDHOLDEREXEMPTCATEGORY
                                                            ,"GuardianExemptCategory"=>$GUARDIANEXEMPTCATEGORY,"ClientType"=>$CLIENTTYPE
                                                            ,"PMS"=>$PMS,"DefaultDP"=>$DEFAULTDP,"CDSLDPID"=>$CDSLDPID,"CDSLCLTID"=>$CDSLCLTID
                                                            ,"CMBPId"=>$CMBPID,"NSDLDPID"=>$NSDLDPID,"NSDLCLTID"=>$NSDLCLTID,"AccountType1"=>$ACCOUNTTYPE1
                                                            ,"AccountNo1"=>$ACCOUNTNO1,"MICRNo1"=>$MICRNO1,"IFSCCode1"=>$IFSCCODE1
                                                            ,"BankName1"=>$BANKNAME1,"BankBranch1"=>$BANKBRANCH1,"DefaultBankFlag1"=>$DEFAULTBANKFLAG1
                                                            ,"Bank1CreatedAt"=>$BANK1CREATEDAT,"Bank1LastModifiedAt"=>$BANK1LASTMODIFIEDAT
                                                            ,"Bank1Status"=>$BANK1STATUS,"AccountType2"=>$ACCOUNTTYPE2,"AccountNo2"=>$ACCOUNTNO2
                                                            ,"MICRNo2"=>$MICRNO2,"IFSCCode2"=>$IFSCCODE2,"BankName2"=>$BANKNAME2,"BankBranch2"=>$BANKBRANCH2
                                                            ,"DefaultBankFlag2"=>$DEFAULTBANKFLAG2,"Bank2CreatedAt"=>$BANK2CREATEDAT,"Bank2LastModifiedAt"=>$BANK2LASTMODIFIEDAT
                                                            ,"Bank2Status"=>$BANK2STATUS,"Accounttype3"=>$ACCOUNTTYPE3,"AccountNo3"=>$ACCOUNTNO3
                                                            ,"MICRNo3"=>$MICRNO3,"IFSCCode3"=>$IFSCCODE3,"BankName3"=>$BANKNAME3
                                                            ,"BankBranch3"=>$BANKBRANCH3,"DefaultBankFlag3"=>$DEFAULTBANKFLAG3
                                                            ,"Bank3CreatedAt"=>$BANK3CREATEDAT,"Bank3LastModifiedAt"=>$BANK3LASTMODIFIEDAT
                                                            ,"Bank3Status"=>$BANK3STATUS,"Accounttype4"=>$ACCOUNTTYPE4,"AccountNo4"=>$ACCOUNTNO4
                                                            ,"MICRNo4"=>$MICRNO4,"IFSCCode4"=>$IFSCCODE4,"BankName4"=>$BANKNAME4
                                                            ,"BankBranch4"=>$BANKBRANCH4,"DefaultBankFlag4"=>$DEFAULTBANKFLAG4,"Bank4CreatedAt"=>$BANK4CREATEDAT
                                                            ,"Bank4LastModifiedAt"=>$BANK4LASTMODIFIEDAT,"Bank4Status"=>$BANK4STATUS,"Accounttype5"=>$ACCOUNTTYPE5
                                                            ,"AccountNo5"=>$ACCOUNTNO5,"MICRNo5"=>$MICRNO5,"IFSCCode5"=>$IFSCCODE5,"BankName5"=>$BANKNAME5
                                                            ,"BankBranch5"=>$BANKBRANCH5,"DefaultBankFlag5"=>$DEFAULTBANKFLAG5,"Bank5CreatedAt"=>$BANK5CREATEDAT
                                                            ,"Bank5LastModifiedAt"=>$BANK5LASTMODIFIEDAT,"Bank5Status"=>$BANK5STATUS
                                                            ,"ChequeName"=>$CHEQUENAME,"Divpaymode"=>$DIVPAYMODE,"Address1"=>$ADDRESS1,"Address2"=>$ADDRESS2
                                                            ,"Address3"=>$ADDRESS3,"City"=>$CITY,"State"=>$STATE,"Pincode"=>$PINCODE
                                                            ,"Country"=>$COUNTRY,"ResiPhone"=>$RESIPHONE,"ResiFax"=>$RESIFAX,"OfficePhone"=>$OFFICEPHONE
                                                            ,"OfficeFax"=>$OFFICEFAX,"Email"=>$EMAIL,"CommunicationMode"=>$COMMUNICATIONMODE
                                                            ,"ForeignAddress1"=>$FOREIGNADDRESS1,"ForeignAddress2"=>$FOREIGNADDRESS2,"ForeignAddress3"=>$FOREIGNADDRESS3
                                                            ,"ForeignAddressCity"=>$FOREIGNADDRESSCITY,"ForeignAddressPincode"=>$FOREIGNADDRESSPINCODE
                                                            ,"ForeignAddressState"=>$FOREIGNADDRESSSTATE,"ForeignAddressCountry"=>$FOREIGNADDRESSCOUNTRY
                                                            ,"ForeignAddressResiPhone"=>$FOREIGNADDRESSRESIPHONE,"ForeignAddressFax"=>$FOREIGNADDRESSFAX
                                                            ,"ForeignAddressOffPhone"=>$FOREIGNADDRESSOFFPHONE,"ForeignAddressOffFax"=>$FOREIGNADDRESSOFFFAX
                                                            ,"IndianMobileNo"=>$INDIANMOBILENO,"Nominee1Name"=>$NOMINEE1NAME,"Nominee1Relationship"=>$NOMINEE1RELATIONSHIP
                                                            ,"Nominee1ApplicablePer"=>$NOMINEE1APPLICABLE,"Nominee1MinorFlag"=>$NOMINEE1MINORFLAG
                                                            ,"Nominee1DOB"=>$NOMINEE1DOB,"Nominee1Guardian"=>$NOMINEE1GUARDIAN,"Nominee2Name"=>$NOMINEE2NAME
                                                            ,"Nominee2Relationship"=>$NOMINEE2RELATIONSHIP,"Nominee2ApplicablePer"=>$NOMINEE2APPLICABLE
                                                            ,"Nominee2DOB"=>$NOMINEE2DOB,"Nominee2MinorFlag"=>$NOMINEE2MINORFLAG,"Nominee2Guardian"=>$NOMINEE2GUARDIAN
                                                            ,"Nominee3Name"=>$NOMINEE3NAME,"Nominee3Relationship"=>$NOMINEE3RELATIONSHIP
                                                            ,"Nominee3ApplicablePer"=>$NOMINEE3APPLICABLE,"Nominee3DOB"=>$NOMINEE3DOB,"Nominee3MinorFlag"=>$NOMINEE3MINORFLAG
                                                            ,"Nominee3Guardian"=>$NOMINEE3GUARDIAN,"PrimaryHolderKYCType"=>$PRIMARYHOLDERKYCTYPE,"PrimaryHolderCKYCNumber"=>$PRIMARYHOLDERCKYCNUMBER
                                                            ,"SecondHolderKYCType"=>$SECONDHOLDERKYCTYPE,"SecondHolderCKYCNumber"=>$SECONDHOLDERCKYCNUMBER,"ThirdHolderKYCType"=>$THIRDHOLDERKYCTYPE
                                                            ,"ThirdHolderCKYCNumber"=>$THIRDHOLDERCKYCNUMBER,"GuardianKYCType"=>$GUARDIANKYCTYPE,"GuardianCKYCNumber"=>$GUARDIANCKYCNUMBER
                                                            ,"PrimaryHolderKRAExemptRefNo"=>$PRIMARYHOLDERKRAEXEMPTREFNO,"SecondHolderKRAExemptRefNo"=>$SECONDHOLDERKRAEXEMPTREFNO
                                                            ,"ThirdHolderKRAExemptRefNo"=>$THIRDHOLDERKRAEXEMPTREFNO,"GuardianExemptRefNo"=>$GUARDIANEXEMPTREFNO,"AadhaarUpdated"=>$AADHAARUPDATED
                                                            ,"MapinId"=>$MAPINID,"Paperlessflag"=>$PAPERLESSFLAG,"LEINo"=>$LEINO,"LEIValidity"=>$LEIVALIDITY,"EmailDeclarationFlag"=>$EMAILDECLARATIONFLAG
                                                            ,"MobileDeclarationFlag"=>$MOBILEDECLARATIONFLAG,"Branch"=>$BRANCH,"Dealer"=>$DEALER,"NominationOpt"=>$NOMINATIONOPT,"NominationAuthenticationMode"=>$NOMINATIONAUTHENTICATIONMODE
                                                            ,"Nominee1PAN"=>$NOMINEE1PAN,"Nominee1GuardianPAN"=>$NOMINEE1GUARDIANPAN,"Nominee2PAN"=>$NOMINEE2PAN,"Nominee2GuardianPAN"=>$NOMINEE2GUARDIANPAN
                                                            ,"Nominee3PAN"=>$NOMINEE3PAN,"Nominee3GuardianPAN"=>$NOMINEE3GUARDIANPAN,"SecondHolderEmail"=>$SECONDHOLDEREMAIL
                                                            ,"SecondholderEmailDeclaration"=>$SECONDHOLDEREMAILDECLARATION,"SecondholderMobile"=>$SECONDHOLDERMOBILE,"SecondholderMobileDeclaration"=>$SECONDHOLDERMOBILEDECLARATION
                                                            ,"ThirdHolderEmail"=>$THIRDHOLDEREMAIL,"ThirdholderEmailDeclaration"=>$THIRDHOLDEREMAILDECLARATION
                                                            ,"ThirdholderMobile"=>$THIRDHOLDERMOBILE,"ThirdholderMobileDeclaration"=>$THIRDHOLDERMOBILEDECLARATION
                                                            ,"NominationFlag"=>$NOMINATIONFLAG,"NominationAuthenticationDate"=>$NOMINATIONAUTHENTICATIONDATE
                                                            ,"CreatedBy"=>$CREATEDBY,"CreatedAt"=>$CREATEDAT,"LastModifiedBy"=>$LASTMODIFIEDBY,"LastModifiedAt"=>$LASTMODIFIEDAT);


                                    
                                   //print_r($dataRows);die();
                                    
                                    if($clientId>0)
                                    {
                                        $inserted = $this->mfp->update_bsc_client_new( $dataRows[$add_FD_list],$clientId);
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_bsc_client_new( $dataRows[$add_FD_list]);
                                        
                                    }
                                    
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                }
                            }
                            
                            
                            $countRow++;
                        }
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('Bsc Client Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "Client Details Uploaded Successfully";
                                  }
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
            redirect('broker/Mutual_fund_schemes/import_client/');
            // $this->import_client($imp_data);
        }
    }
    
    function import_client_mandate($err_data=null)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
    
           $header['title'] = 'BSE Client Master';
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
           $this->load->view('client/bscclientmandateimport', $data);
           $this->load->view('broker/common/notif');
           $this->load->view('broker/common/footer');
    }

    function BSCClient_mandate_import()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');

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
                           
                    $MANDATECODE="";$CLIENTCODE="";$CLIENTNAME="";$MEMBERCODE="";$BANKNAME="";$BANKBRANCH="";$AMOUNT="";$REGNDATE="";$STATUS="";$UMRNNO="";$REMARKS="";$APPROVEDDATE="";$BANKACCOUNTNUMBER="";$MANDATECOLLECTIONTYPE="";$MANDATETYPE="";$DATEOFUPLOAD="";$STARTDATE="";$ENDDATE="";$DATEOFREUPLOAD="";

                    
                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                          
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);


                    // Following comment code will use for generating code for multiple columns
                    /* echo "<pre>";
                    print_r($excelData);

                    $k='';$m='';
                    for($i=0;$i<count($excelData[0]);$i++){
                        $j = "strtoupper(cell)==strtoupper('".$excelData[0][$i]."') || ";
                        $k = $k.$j;

                        $l = '$'.str_replace(" ", "", $excelData[0][$i]).'="";';
                        $l = '"'.str_replace(" ", "", $excelData[0][$i]).'"=>$'.str_replace(" ", "", $excelData[0][$i]).',';
                        $m = $m.$l;

                        echo "else if(strtoupper($dataColumns[$countCell]) ===strtoupper('".$excelData[0][$i]."'))
                        {
                            if($cell || $cell != ''){
                                $".str_replace(' ', '', $excelData[0][$i])." = trim($cell);
                            }
                            else{
                                $".str_replace(' ', '', $excelData[0][$i])." ='';
                            }
                        }";      
                    }
                    echo $k;
                    echo "<br>";
                    echo $m;
                    die();  */
    

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
                                                                    
                                    if(strtoupper($cell)==strtoupper('MANDATE CODE') || strtoupper($cell)==strtoupper('CLIENT CODE') || strtoupper($cell)==strtoupper('CLIENT NAME') || strtoupper($cell)==strtoupper('MEMBER CODE') || strtoupper($cell)==strtoupper('BANK NAME') || strtoupper($cell)==strtoupper('BANK BRANCH') || strtoupper($cell)==strtoupper('AMOUNT') || strtoupper($cell)==strtoupper('REGN DATE') || strtoupper($cell)==strtoupper('STATUS') || strtoupper($cell)==strtoupper('UMRN NO') || strtoupper($cell)==strtoupper('REMARKS') || strtoupper($cell)==strtoupper('APPROVED DATE') || strtoupper($cell)==strtoupper('BANK ACCOUNT NUMBER') || strtoupper($cell)==strtoupper('MANDATE COLLECTION TYPE') || strtoupper($cell)==strtoupper('MANDATE TYPE') || strtoupper($cell)==strtoupper('DATE OF UPLOAD') || strtoupper($cell)==strtoupper('START DATE') || strtoupper($cell)==strtoupper('END DATE') || strtoupper($cell)==strtoupper('DATE OF RE-UPLOAD'))
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
                                        //exit();
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {

                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('MANDATE CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MANDATECODE = trim($cell);
                                        }
                                        else{
                                            $MANDATECODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTCODE = trim($cell);
                                        }
                                        else{
                                            $CLIENTCODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTNAME = trim($cell);
                                        }
                                        else{
                                            $CLIENTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MEMBER CODE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MEMBERCODE = trim($cell);
                                        }
                                        else{
                                            $MEMBERCODE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKNAME = trim($cell);
                                        }
                                        else{
                                            $BANKNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK BRANCH'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKBRANCH = trim($cell);
                                        }
                                        else{
                                            $BANKBRANCH ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMOUNT'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMOUNT = trim($cell);
                                        }
                                        else{
                                            $AMOUNT ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('REGN DATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $REGNDATE = trim($cell);
                                        }
                                        else{
                                            $REGNDATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('STATUS'))
                                    {
                                        if($cell || $cell != ''){
                                            $STATUS = trim($cell);
                                        }
                                        else{
                                            $STATUS ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('UMRN NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $UMRNNO = trim($cell);
                                        }
                                        else{
                                            $UMRNNO ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('REMARKS'))
                                    {
                                        if($cell || $cell != ''){
                                            $REMARKS = trim($cell);
                                        }
                                        else{
                                            $REMARKS ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('APPROVED DATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $APPROVEDDATE = trim($cell);
                                        }
                                        else{
                                            $APPROVEDDATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('BANK ACCOUNT NUMBER'))
                                    {
                                        if($cell || $cell != ''){
                                            $BANKACCOUNTNUMBER = trim($cell);
                                        }
                                        else{
                                            $BANKACCOUNTNUMBER ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MANDATE COLLECTION TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MANDATECOLLECTIONTYPE = trim($cell);
                                        }
                                        else{
                                            $MANDATECOLLECTIONTYPE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('MANDATE TYPE'))
                                    {
                                        if($cell || $cell != ''){
                                            $MANDATETYPE = trim($cell);
                                        }
                                        else{
                                            $MANDATETYPE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DATE OF UPLOAD'))
                                    {
                                        if($cell || $cell != ''){
                                            $DATEOFUPLOAD = trim($cell);
                                        }
                                        else{
                                            $DATEOFUPLOAD ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('START DATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $STARTDATE = trim($cell);
                                        }
                                        else{
                                            $STARTDATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('END DATE'))
                                    {
                                        if($cell || $cell != ''){
                                            $ENDDATE = trim($cell);
                                        }
                                        else{
                                            $ENDDATE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('DATE OF RE-UPLOAD'))
                                    {
                                        if($cell || $cell != ''){
                                            $DATEOFREUPLOAD = trim($cell);
                                        }
                                        else{
                                            $DATEOFREUPLOAD ='';
                                        }
                                    }
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                            if($countRow != 0)
                            {
                                if($MANDATECODE=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $clientId = 0;
                                    $res = $this->mfp->check_bsc_client_mandate_by_mandate_code($MANDATECODE);
                                    
                                    if(!empty($res))
                                    {
                                        $clientId = $res[0]->Id;
                                    }
                                    
                                    $dataRows[$add_FD_list] =array("MANDATECODE"=>$MANDATECODE,"CLIENTCODE"=>$CLIENTCODE,"CLIENTNAME"=>$CLIENTNAME,"MEMBERCODE"=>$MEMBERCODE,"BANKNAME"=>$BANKNAME,"BANKBRANCH"=>$BANKBRANCH,"AMOUNT"=>$AMOUNT,"REGNDATE"=>$REGNDATE,"STATUS"=>$STATUS,"UMRNNO"=>$UMRNNO,"REMARKS"=>$REMARKS,"APPROVEDDATE"=>$APPROVEDDATE,"BANKACCOUNTNUMBER"=>$BANKACCOUNTNUMBER,"MANDATECOLLECTIONTYPE"=>$MANDATECOLLECTIONTYPE,"MANDATETYPE"=>$MANDATETYPE,"DATEOFUPLOAD"=>$DATEOFUPLOAD,"STARTDATE"=>$STARTDATE,"ENDDATE"=>$ENDDATE,"DATEOFREUPLOAD"=>$DATEOFREUPLOAD);
                                    
                                    if($clientId>0)
                                    {
                                        $inserted = $this->mfp->update_bsc_client_mandate( $dataRows[$add_FD_list],$clientId);
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_bsc_client_mandate( $dataRows[$add_FD_list]);
                                    }
                                    // echo $this->db->last_query();
                                    // die();
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                }
                            }
                            
                            
                            $countRow++;
                        }
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('Bsc Client Account Mandate Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "Client Account Mandate Details Uploaded Successfully";
                                  }
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
            redirect('broker/Mutual_fund_schemes/import_client_mandate/');
            // $this->import_client($imp_data);
        }
    }
    
    
     function import_client_folio($err_data=null)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
    
           $header['title'] = 'Client MF Folio';
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
           $this->load->view('client/BscClientFolioMaster', $data);
           $this->load->view('broker/common/notif');
           $this->load->view('broker/common/footer');
    }
    
    
    function Client_mf_folio_import()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');

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
                           
                    $RTANAME="";$FILENAME="";$FOLIO="";$CLIENTNAME="";$HOLDINGNATURE="";$JOINTHOLDER1="";$JOINTHOLDER2="";$PANNO="";$JOINTHOLDER1PAN="";$JOINTHOLDER2PAN="";$GUARDIANPAN="";
                    $ChannelPartnerCode="";
                    
                    
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
                                                                    
                                    if(strtoupper($cell)==strtoupper('RTA NAME') 
                                    || strtoupper($cell)==strtoupper('FILE NAME') 
                                    || strtoupper($cell)==strtoupper('FOLIO') 
                                    || strtoupper($cell)==strtoupper('CLIENT NAME') 
                                    || strtoupper($cell)==strtoupper('HOLDING NATURE') 
                                    || strtoupper($cell)==strtoupper('JOINT HOLDER 1') 
                                    || strtoupper($cell)==strtoupper('JOINT HOLDER 2') 
                                    || strtoupper($cell)==strtoupper('PAN NO') 
                                    || strtoupper($cell)==strtoupper('JOINT HOLDER 1 PAN') 
                                    || strtoupper($cell)==strtoupper('JOINT HOLDER 2 PAN') 
                                    || strtoupper($cell)==strtoupper('GUARDIAN PAN')
                                    || strtoupper($cell)==strtoupper('Channel Partner Code'))
                                    {
                                        $dataColumns[$countCell] = $cell;
                                        $countCell++;
                                        $uploadedStatus = 2;
                                        continue;
                                    }
                                    else if($cell!='')
                                    {
                                        var_dump($dataColumns);
                                        var_dump($cell);
                                        //exit();
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        echo $message;
                                        break;
                                    }
                                }
                                else
                                {
                                    if($cell=='')
                                    {
                                        
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('RTA NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $RTANAME = trim($cell);
                                        }
                                        else{
                                            $RTANAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FILE NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $FILENAME = trim($cell);
                                        }
                                        else{
                                            $FILENAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('FOLIO'))
                                    {
                                        if($cell || $cell != ''){
                                            $FOLIO = trim($cell);
                                        }
                                        else{
                                            $FOLIO ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CLIENT NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $CLIENTNAME = trim($cell);
                                        }
                                        else{
                                            $CLIENTNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('HOLDING NATURE'))
                                    {
                                        if($cell || $cell != ''){
                                            $HOLDINGNATURE = trim($cell);
                                        }
                                        else{
                                            $HOLDINGNATURE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('JOINT HOLDER 1'))
                                    {
                                        if($cell || $cell != ''){
                                            $JOINTHOLDER1 = trim($cell);
                                        }
                                        else{
                                            $JOINTHOLDER1 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('JOINT HOLDER 2'))
                                    {
                                        if($cell || $cell != ''){
                                            $JOINTHOLDER2 = trim($cell);
                                        }
                                        else{
                                            $JOINTHOLDER2 ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PAN NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $PANNO = trim($cell);
                                        }
                                        else{
                                            $PANNO ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('JOINT HOLDER 1 PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $JOINTHOLDER1PAN = trim($cell);
                                        }
                                        else{
                                            $JOINTHOLDER1PAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('JOINT HOLDER 2 PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $JOINTHOLDER2PAN = trim($cell);
                                        }
                                        else{
                                            $JOINTHOLDER2PAN ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('GUARDIAN PAN'))
                                    {
                                        if($cell || $cell != ''){
                                            $GUARDIANPAN = trim($cell);
                                        }
                                        else{
                                            $GUARDIANPAN ='';
                                        }
                                    }
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Channel Partner Code'))
                                    {
                                        if($cell || $cell != ''){
                                            $ChannelPartnerCode = trim($cell);
                                        }
                                        else{
                                            $ChannelPartnerCode ='';
                                        }
                                    }
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                            if($countRow != 0)
                            {
                                if($FOLIO=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $Id = 0;
                                    $res = $this->mfp->check_bsc_client_folio($FOLIO);
                                    
                                    if(!empty($res))
                                    {
                                        $Id = $res[0]->Id;
                                    }
                                    
                                    $dataRows[$add_FD_list] =array(
                                                        "RtaName"=>$RTANAME,"FileName"=>$FILENAME,"Folio"=>$FOLIO,
                                                        "ClientName"=>$CLIENTNAME,"HoldingNature"=>$HOLDINGNATURE,"JointHolder1"=>$JOINTHOLDER1,
                                                        "JointHolder2"=>$JOINTHOLDER2,"PanNo"=>$PANNO,"JointHolder1PanNo"=>$JOINTHOLDER1PAN,"JointHolder2PanNo"=>$JOINTHOLDER2PAN,
                                                        "GuardianPanNo"=>$GUARDIANPAN,"ChannelPartnerCode"=>$ChannelPartnerCode,
                                                        "UpdatedBy"=>$user_id);
                                    //print_r($dataRows[$add_FD_list] );die;
                                    
                                    if($Id>0)
                                    {
                                        $inserted = $this->mfp->update_client_mf_folio_master( $dataRows[$add_FD_list],$Id);
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_client_mf_folio_master( $dataRows[$add_FD_list]);
                                    }
                                    
                                   
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                     $RTANAME="";$FILENAME="";$FOLIO="";$CLIENTNAME="";$HOLDINGNATURE="";$JOINTHOLDER1="";$JOINTHOLDER2="";$PANNO="";$JOINTHOLDER1PAN="";$JOINTHOLDER2PAN="";$GUARDIANPAN="";
                                      $ChannelPartnerCode="";
                                }
                            }
                            
                            
                            $countRow++;
                        }
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('Client MF Folio Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "Client MF Folio Master Details Uploaded Successfully";
                                  }
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
            redirect('broker/Mutual_fund_schemes/import_client_folio/');
            // $this->import_client($imp_data);
        }
    }
    
     function Import_FD_Rate($err_data=null)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');
    
           $header['title'] = 'FD Rate Master';
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
           $this->load->view('client/FDRateMaster', $data);
           $this->load->view('broker/common/notif');
           $this->load->view('broker/common/footer');
    }
    
     function fd_rate_import()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '15M');
        ini_set('post_max_size', '20M');

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
                           
                    $COMPANYNAME="";$PERIOD="";$NONCUMULATIVEMONTHLY="";$NONCUMULATIVEQUARTERLY="";$NONCUMULATIVEHALFYEARLY="";$NONCUMULATIVEYEARLY="";$CUMULATIVE="";$IsSeniorcitizen="";

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
                                                                    
                                    if(strtoupper($cell)==strtoupper('COMPANY NAME') 
                                    || strtoupper($cell)==strtoupper('PERIOD') 
                                    || strtoupper($cell)==strtoupper('NON CUMULATIVE MONTHLY') 
                                    || strtoupper($cell)==strtoupper('NON CUMULATIVE QUARTERLY') 
                                    || strtoupper($cell)==strtoupper('NON CUMULATIVE HALF YEARLY') 
                                    || strtoupper($cell)==strtoupper('NON CUMULATIVE YEARLY') 
                                    || strtoupper($cell)==strtoupper('CUMULATIVE') 
                                    || strtoupper($cell)==strtoupper('IsSenior citizen') )
                                    {
                                        $dataColumns[$countCell] = $cell;
                                        $countCell++;
                                        $uploadedStatus = 2;
                                        continue;
                                    }
                                    else
                                    {
                                        $message = 'Columns Specified in Excel is not in correct format1'. $countCell ;
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                    
                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('COMPANY NAME'))
                                    {
                                        if($cell || $cell != ''){
                                            $COMPANYNAME = trim($cell);
                                        }
                                        else{
                                            $COMPANYNAME ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('PERIOD'))
                                    {
                                        if($cell || $cell != ''){
                                            $PERIOD = trim($cell);
                                        }
                                        else{
                                            $PERIOD ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NON CUMULATIVE MONTHLY'))
                                    {
                                        if($cell || $cell != ''){
                                            $NONCUMULATIVEMONTHLY = trim($cell);
                                        }
                                        else{
                                            $NONCUMULATIVEMONTHLY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NON CUMULATIVE QUARTERLY'))
                                    {
                                        if($cell || $cell != ''){
                                            $NONCUMULATIVEQUARTERLY = trim($cell);
                                        }
                                        else{
                                            $NONCUMULATIVEQUARTERLY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NON CUMULATIVE HALF YEARLY'))
                                    {
                                        if($cell || $cell != ''){
                                            $NONCUMULATIVEHALFYEARLY = trim($cell);
                                        }
                                        else{
                                            $NONCUMULATIVEHALFYEARLY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('NON CUMULATIVE YEARLY'))
                                    {
                                        if($cell || $cell != ''){
                                            $NONCUMULATIVEYEARLY = trim($cell);
                                        }
                                        else{
                                            $NONCUMULATIVEYEARLY ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('CUMULATIVE'))
                                    {
                                        if($cell || $cell != ''){
                                            $CUMULATIVE = trim($cell);
                                        }
                                        else{
                                            $CUMULATIVE ='';
                                        }
                                    }else if(strtoupper($dataColumns[$countCell]) ===strtoupper('IsSenior citizen'))
                                    {
                                        if($cell || $cell != ''){
                                            $IsSeniorcitizen = trim($cell);
                                        }
                                        else{
                                            $IsSeniorcitizen ='';
                                        }
                                    }
                                    
                                    $status='Active';
                                    $countCell++;
                                }
                            }
                       
                            if($countRow != 0)
                            {
                               
                                if($COMPANYNAME=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $Id = 0;
                                   
                                      
                                    $res = $this->mfp->check_fd_rate($PERIOD,$COMPANYNAME,$IsSeniorcitizen);
                                    
                                    if(!empty($res))
                                    {
                                        $Id = $res[0]->Id;
                                    }
                                        $dataRows[$add_FD_list] =array(
                                                        "CompanyName"=>$COMPANYNAME,"Period"=>$PERIOD,"NonCumulativeMonthly"=>$NONCUMULATIVEMONTHLY,
                                                        "NonCumulativeQuatuerly"=>$NONCUMULATIVEQUARTERLY,"NonCumulativeHalfYearly"=>$NONCUMULATIVEHALFYEARLY,"NonCumulativeYearly"=>$NONCUMULATIVEYEARLY,
                                                        "Cumulative"=>$CUMULATIVE,"IsSeniorCitizen"=>$IsSeniorcitizen,
                                                        "UpdatedBy"=>$user_id);
                                        
                                    
                                    if($Id>0)
                                    {
                                        $inserted = $this->mfp->update_fd_rate_master( $dataRows[$add_FD_list],$Id);
                                        
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_fd_rate_master( $dataRows[$add_FD_list]);
                                    }
                                   
                                    $transID = $inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records.';
                                        break;
                                    }
                                    $COMPANYNAME="";$PERIOD="";$NONCUMULATIVEMONTHLY="";$NONCUMULATIVEQUARTERLY="";$NONCUMULATIVEHALFYEARLY="";$NONCUMULATIVEYEARLY="";$CUMULATIVE="";$IsSeniorcitizen="";
                                    $Id=0;
                                }
                            }
                            
                         $countRow++;
                        }
                         
                        if($dataRows)
                        {
                            if(is_array($transID))
                            {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records';
                            } else {
                                  $this->common->last_import('FD Rate Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
                                  if($uploadedStatus != 2) {
                                      $message = "FD Rate Master Uploaded Successfully";
                                  }
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
                            "text" => 'Some Recoreds are not imported please review your excelsheet as per format'
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
            redirect('broker/Mutual_fund_schemes/Import_FD_Rate');
            // $this->import_client($imp_data);
        }
    }
    
    function updatenav()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Update NAV';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/mutual_fund/mf_nav');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }
    
} 