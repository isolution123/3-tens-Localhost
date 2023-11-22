
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class SIP extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More$this->load->library('session');
    $this->load->library('session');
    $this->load->library('Custom_exception');
    $this->load->library('Common_lib');
    $this->load->helper('url');
    $this->load->model('Families_model','family');

    //check if user is logged in by checking his/her session data
    //if user is not logged redirect to login
    if(empty($this->session->userdata['user_id']))
    {
        redirect('broker');
    }

    //load Model
    $this->load->model('Sip_import', 'sp');
    $this->load->model('Clients_model', 'client');
    $this->load->model('Common_model', 'common');
  }


  function import($err_data=null)
  {
    
    if(empty($this->session->userdata['user_id']))
    {
        redirect('broker');
    }  
    $header['title'] = 'SIP Details Import';
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
    $this->load->view('broker/sip_import', $data);
    $this->load->view('broker/common/notif');
    $this->load->view('broker/common/footer');
  }

  function SIP_details_import()
  {

          ini_set('max_execution_time', 0);
          ini_set('memory_limit', '512M');

          $uploadedStatus = 0;
          $message = ""; $impMessage = ""; $insertRow = true;
          $imp_data = array();
          if (isset($_POST['Import']))
          {
              //echo $_FILES["import_Sip"]["type"];
            if(isset($_FILES["import_Sip"]) AND ($_FILES["import_Sip"]["type"]=="application/excel"||$_FILES["import_Sip"]["type"]=="application/vnd.ms-excel"||$_FILES["import_Sip"]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
            {
              if (isset($_FILES["import_Sip"]))
              {

              if($_REQUEST['rta_list'] ==' ')
                 {
                $message = "Select RTA First";
                }
                //if there was an error uploading the file
                else if ($_FILES["import_Sip"]["name"] == '')
                  {
                      $message = "No file selected";
                  }
                  /*------------- Cams SIP IMPort Start here---------*/
                  else if($_REQUEST['rta_list'] =='cams_excel')
                  {
                    //get tmp_name of file
                    $file = $_FILES["import_Sip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";

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
                            //echo $cell;

                              if($countRow == 0)
                              {
                                  $cell = str_replace(array('.'), '', $cell);
                                  if(strtoupper($cell)=='PRODUCT'||strtoupper($cell)=='SCHEME'||strtoupper($cell)=='FOLIO_NO'||
                                  strtoupper($cell)=='INV_NAME'||strtoupper($cell)=='AUT_TRNTYP'||strtoupper($cell)=='AUTO_TRNO'||
                                  strtoupper($cell)=='AUTO_AMOUNT' || strtoupper($cell)=='AUTO_AMOUN' ||strtoupper($cell)=='FROM_DATE'||strtoupper($cell)=='TO_DATE'||
                                  strtoupper($cell)=='CEASE_DATE' || strtoupper($cell)=='PERIODICIT' || strtoupper($cell)=='PERIODICITY'||strtoupper($cell)=='PERIOD_DAY'||
                                  strtoupper($cell)=='INV_IIN' || strtoupper($cell)=='PAYMENT_MO' || strtoupper($cell)=='PAYMENT_MODE' || strtoupper($cell)=='TARGET_SCH' 
                                  || strtoupper($cell)=='TARGET_SCHEME'||
                                  strtoupper($cell)=='REG_DATE'||strtoupper($cell)=='SUBBROKER'|| strtoupper($cell)=='REMARKS' ||
                                  strtoupper($cell)=='TOP_UP_FRQ' ||
                                  strtoupper($cell)=='TOP_UP_AMT' ||
                                  strtoupper($cell)=='AC_TYPE' ||
                                  strtoupper($cell)=='BANK' || strtoupper ($cell)=='BRANCH' || strtoupper($cell)=='INSTRM_NO' 
                                  || strtoupper($cell)=='CHEQ_MICR_' || strtoupper($cell)=='CHEQ_MICR_NO' || strtoupper($cell)=='AC_HOLDER_' 
                                  || strtoupper($cell)=='AC_HOLDER_NAME' || strtoupper($cell)=='PAN'
                                  || strtoupper($cell)=='TOP_UP_PER'
                                  || strtoupper($cell)=='TOP_UP_PERC' || strtoupper($cell)=='EUIN' || strtoupper($cell)=='SUB_ARN_CO' || strtoupper($cell)=='SUB_ARN_CODE'
                                  || strtoupper($cell)=='TER_LOCATI' || strtoupper($cell)=='TER_LOCATION' || strtoupper($cell)=='SCHEME_CODE' || strtoupper($cell)=='TARGET_SCHEME_CODE')
                                        {
                                          $message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;
                                        //die();
                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }
                              }
                              else
                              {

                                    if($insertRow)
                                    {

                                            if(strtoupper($dataColumns[$countCell]) === 'PRODUCT' ||strtoupper($dataColumns[$countCell]) === 'PRODUCT')//product_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                      $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else{
                                                         $scheme_type_id=$scheme_id->scheme_type_id;                                                    
                                                          $sc_type= $scheme_id->scheme_type; 
                                                        if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                         {
                                                             $sc='equity';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                            
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                            
                                                             
                                                         }
                                                         else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                         {
                                                             $sc='debt';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                         {
                                                               $sc='hybrid';
                                                               $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                               $sip_rate=$this->sp->get_sip_rate($condition);
                                                               if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type='')
                                                         {
                                                             $sip_rate='10';
                                                         }
                                                       
                                                         
                                                         
                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");
    

                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }

                                            }

                                            else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')//folio_id
                                            {
                                              //echo $countCell;
                                              //echo 'FOLIO_NO';
                                              //print_r($dataColumns[$countCell]);
                                              if($cell || $cell != '')
                                              {
                                                   //  $dateOfTransaction= trim($cell);
                                                      $folio_id = $cell;
                                                      //$dateOfTransaction = $date->format('Y-m-d');
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";
                                                // $dateOfTransaction=null;
                                                // $dateOfTransaction = 0;
                                              }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'BANK' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM')//bank_id
                                            {

                                                    $bank = $cell;
                                                    $bank_id='';




                                            }

                                            else if(strtoupper($dataColumns[$countCell]) === 'INSTRM_NO' || strtoupper($dataColumns[$countCell]) === 'INSTRM_NO')//installment_amt
                                            {
                                                  if($cell || $cell != '')
                                                  {
                                                       $Bank_AccountNO = $cell;
                                                  }
                                                  else
                                                  {
                                                       $Bank_AccountNO = '';
                                                  }


                                              }
                                              /*------(Cams) Modified by Akshay Karde for Case : - when PAN does not exist ------*/
                                              else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                              {
                                                 if($cell || $cell != '')
                                                {
                                                     $PanNum = trim($cell);
                                                     $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                      $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                     // print_r($c_info);
                                                      if(count($c_info) == 0)
                                                      {

                                                          $insertRow = false;
                                                          $impMessage = "Client does not exist";
                                                       }
                                                       else
                                                      {
                                                          $client_id = $c_info->client_id;
                                                          $familyId = $c_info->family_id;

                                                      }
                                                    }
                                                  else
                                                  {
                                                    $wherePan = array(
                                                      'cb.productId'=>$product_id,
                                                      'cb.folio_number'=>$folio_id,
                                                      'f.broker_id'=>$brokerID
                                                    );
                                                    //var_dump($wherePan);
                                                    if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                    {
                                                       //var_dump($c_info1);
                                                       //$nopan='set';
                                                       $insertRow = false;
                                                       $impMessage = "Client does not exist";
                                                    }
                                                    else
                                                    {
                                                       //var_dump($c_info1);
                                                      $client_id = $c_info1->client_id;
                                                      $familyId = $c_info1->family_id;
                                                    }
                                                    
                                                  }
                                                }
                                                else if(strtoupper($dataColumns[$countCell]) === 'CEASE_DATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                                {
                                                   $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }
                                                   
                                                     /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d-m-Y', $cell);
                                                              if(is_object($date)) {
                                                                    $cease_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $cease_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                           $insertRow = false;
                                                                             $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }*/    
                                                     
                                                  /*if($cell || $cell != '')
                                                  {
                                                        
                                                        $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                            if(is_object($date)) {
                                                                 $cease_date = $date->format('Y-m-d');
                                                            }else {
                                                                   $insertRow = false;
                                                                 $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                            }
                                                  }
                                                  else
                                                  {
                                                    $cease_date=null;
                                                    
                                                  }*/
                                                  /*$cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }*/

                                                  }
                                                  else if(strtoupper($dataColumns[$countCell]) === 'FROM_DATE' || strtoupper($dataColumns[$countCell]) === 'STARTDATE')//start_date
                                                  {
                                                    $cell=trim(str_replace('/','-', $cell));
                                      
                                                         if($cell || $cell != '')
                                                         {
                                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                            
                                                             if(is_object($date))
                                                             {
                                                               $start_date=$date->format('Y-m-d');
                                                             }
                                                             else
                                                             {
                                                               $date = new DateTime($cell);
                                                                if(is_object($date))
                                                                {
                                                                  $start_date=$date->format('Y-m-d');
                    
                                                                }
                                                                else
                                                                {
                    
                                                                       $insertRow = false;
                                                                       $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                }
                    
                                                              }
                    
                                                          } 
                                                          else
                                                          {
                                                             $insertRow = false;
                                                             $impMessage=" From Date cannot be empty";
                                                          }
                                                     /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d-m-Y', $cell);
                                                              if(is_object($date)) {
                                                                    $start_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $start_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }*/    
                                                        
                                                      /*$cell=trim(str_replace('/','-', $cell));
                                      
                                                         if($cell || $cell != '')
                                                         {
                                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                            
                                                             if(is_object($date))
                                                             {
                                                               $start_date=$date->format('Y-m-d');
                                                             }
                                                             else
                                                             {
                                                               $date = new DateTime($cell);
                                                                if(is_object($date))
                                                                {
                                                                  $start_date=$date->format('Y-m-d');
                    
                                                                }
                                                                else
                                                                {
                    
                                                                       $insertRow = false;
                                                                       $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                }
                    
                                                              }
                    
                                                          } 
                                                          else
                                                          {
                                                             $insertRow = false;
                                                             $impMessage=" From Date cannot be empty";
                                                          }*/
                                                       
                                       
  
                                                    
                                                    /*if($cell || $cell != '')
                                                    {
                                                          
                                                          
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                          
                                                              if(is_object($date)) {
                                                                     $start_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $insertRow = false;
                                                                   $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="From date cannot be empty";
                                                      
                                                      
                                                    }*/
                                                  }
                                                  else if(strtoupper($dataColumns[$countCell]) === 'TO_DATE' ||strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                                  {
                                                      $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        
                                                      $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                              $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                              $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                 $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }
                                                     /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d-m-Y', $cell);
                                                              if(is_object($date)) {
                                                                    $end_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $end_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }*/    
                                                    /*$cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        
                                                      $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                              $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                              $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                 $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }*/
                                                    
                                                    /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $End_date = $edate->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                    $end_date = $date->format('Y-m-d');
                                                              } else {
                                                                     $insertRow = false;
                                                                   $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                      
                                                      
                                                    }*/
                                                    
                                    
                                                    
                                                    

                                                  }
                                                

                                            else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUNT' || strtoupper($dataColumns[$countCell])  === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                            {
                                                  if($cell || $cell != '')
                                                  {
                                                       $auto_amoun = $cell;
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Auto Amount cannot be empty";
                                                  }

                                              }
                                            else if(strtoupper($dataColumns[$countCell]) === 'REG_DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                            {

                                                $cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }
                                                    /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d-m-Y', $cell);
                                                              if(is_object($date)) {
                                                                    $reg_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $reg_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "Registration Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Registration date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }*/    
                                              /*$cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }*/
                                              /*if($cell || $cell != '')
                                              {
                                                    
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                              $reg_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Registration date cannot be empty";
                                               
                                              }*/
                                              }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FREQUENCY')//frewuency
                                            {
                                                 if($cell || $cell != '')
                                                 {
                                                             $frequency = trim($cell);
                                                     }
                                                   else
                                                   {
                                                       $insertRow = false;
                                                       $impMessage = "Frequency cannot be empty";
                                                   }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'ACCOUNT_NO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//account_number
                                            {

                                                if($cell || $cell != '')
                                                {
                                                     //  $dateOfTransaction= trim($cell);
                                                        $account_no = $cell;

                                                        //$dateOfTransaction = $date->format('Y-m-d');
                                                }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="Account Number cannot be empty";
                                                  // $dateOfTransaction=null;
                                                  // $dateOfTransaction = 0;
                                                }
                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO' || strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO')//scheme_id
                                            {
                                                      if($cell || $cell != '')
                                                      {
                                                          $auto_trno = $cell;
                                                          //var_dump($cell);
                                                      }
                                                      else
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "AUTO_TRNO cannot be empty";
                                                      }
                                            }
                                             else if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//scheme_id
                                            {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }
                                                      
                                            }
                                            
                                            


                                    $countCell++;
                                    }
                                     else {
                                        if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                           
                                                      }
                                              }
                                
                                        
                                    }
                          }
                        }


                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                             //  $temp_mat_date =new Datetime($maturityDate);
                             //  $temp_issue_date =new DateTime($dateOfIssue);

                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");
                             $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                            if(!empty(trim($cease_date)))
                           {
                             $end_date=$cease_date;//replace end date for maturity calculation
                           }
                           else
                           {
                               
                               $cease_date=NULL;
                           }

                          $rate_of_return = $sip_rate/400;
                          $install_amt = $auto_amoun;
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                          // var_dump($num_of_days);
                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($auto_amoun * $exp1) / $exp4);

  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;
  $added_on = date("Y/m/d");
  $frequency='monthly';//by default
                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                               //'company_id'=>intval($bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $auto_trno,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date_for_insert),
                                                               'frequency'=>$frequency,//default frequency is monthly
                                                               'cease_date'=>$cease_date,
                                                               'installment_amount'=> $auto_amoun,
                                                               'rate_of_return'=>$sip_rate,
                                                               'reg_date'=>$reg_date,
                                                               'Bank_AccountNo'=>$Bank_AccountNO,
                                                               'Bank'=>$bank,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );
                                                              //var_dump($dataRows['add_SIP_list']);
                                                                  $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                     
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {
                                                                 
                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                 $assets_id=$isDuplicateSIP->asset_id;
                                                                 
                                                                 
                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));
                                                                  
                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                               }
                                                               else
                                                               {
                                                                
                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);  
                                                               $assets_id=$inserted;
                                                                
                                                               
                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            $uploadedStatus = 1;
                                                               }


                       
                           

                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                            // var_dump($brokerID);
                            //  var_dump($_FILES["import_Sip"]["name"]);
                            //   var_dump($user_id);
                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  /*------------- Cams SIP IMPort Ends here---------*/
                  /*------------- KARVY SIP IMPort Start here---------*/
                  else if($_REQUEST['rta_list'] =='karvy_excel')
                  {
                    //get tmp_name of file
                    $file = $_FILES["import_Sip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";
                   $pan="";

                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                  //print_r($excelData);

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
                                  if(strtoupper($cell)=='PRODCODE' || strtoupper($cell)=='PRODUCT CODE' ||strtoupper($cell)=='BRANCH'||strtoupper($cell)=='LOCATION'||
                                  strtoupper($cell)=='IHNO'||strtoupper($cell)=='FOLIO'||strtoupper($cell)=='INVNAME'
                                  ||strtoupper($cell)=='INVESTOR NAME'|| strtoupper($cell)=='REGDATE' ||
                                  strtoupper($cell)=='REGISTRATIONDATE' ||strtoupper($cell)=='STARTDATE' ||strtoupper($cell)=='START DATE' || strtoupper($cell)=='ENDDATE'
                                  || strtoupper($cell)=='END DATE'|| strtoupper($cell)=='NOOFINSTAL' || strtoupper($cell)=='NO OF INSTALLMENTS'||strtoupper($cell)=='AMOUNT'||strtoupper($cell)=='SCHEME'||
                                  strtoupper($cell)=='PLAN' || strtoupper($cell)=='AGENTCODE' || strtoupper($cell)=='AGENTNAME'||
                                  strtoupper($cell)=='SUBBROKER' ||strtoupper($cell)=='SCHEMENAME' ||strtoupper($cell)=='SCHEME NAME'|| strtoupper($cell)=='PAN' ||
                                  strtoupper($cell)=='SIPTYPE' || strtoupper($cell)=='SIPMODE' ||
                                  strtoupper($cell)=='SIP MODE' || strtoupper($cell)=='FUNDCODE' ||
                                  strtoupper($cell)=='FUND CODE' || strtoupper($cell)=='ZONE' || strtoupper ($cell)=='FREQUENCY' || strtoupper($cell)=='TRTYPE' ||
                                   strtoupper($cell)=='TOSCHEME' || strtoupper($cell)=='TO SCHEME' || strtoupper($cell)=='PLAN' || strtoupper($cell)=='TOPLAN' || strtoupper($cell)=='TO PLAN' || strtoupper($cell)=='TERMINATEDATE'
                                   || strtoupper($cell)=='TERMINATED'
                                  || strtoupper($cell)=='STATUS' || strtoupper($cell)=='TOPRODUCTC' || strtoupper($cell)=='TOPRODUCTCODE' 
                                  || strtoupper($cell)=='TOSCHEMENA' || strtoupper($cell)=='TOSCHEMENAME'
                                  || strtoupper($cell)=='ECSNO' || strtoupper($cell)=='ECSBANKNAM' || strtoupper($cell)=='ECSBANKNAME' || strtoupper($cell)=='ECSACNO'
                                  || strtoupper($cell)=='ECSHOLDERN' || strtoupper($cell)=='ECSHOLDERNAME' || strtoupper($cell)=='REGSLNO' ||  strtoupper($cell)=='INVDPID' 
                                   || strtoupper($cell)=='INVCLIENTI' || strtoupper($cell)=='INVCLIENTID' || strtoupper($cell)=='DP_INVNAME' || strtoupper($cell)=='MODIFYFLAG' ||
                                     strtoupper($cell)=='UMRNCODE' || strtoupper($cell)=='SIP_UMRNCODE'
                                     /* new format 10-06-2017---*/
                                  || strtoupper($cell)=='PRODUCTCOD' || strtoupper($cell)=='AGENT' || strtoupper($cell)=='FUND' || strtoupper($cell)=='ACNO' || strtoupper($cell)=='SCHCODE'
                                  || strtoupper($cell)=='SCHDESC'
                                  || strtoupper($cell)=='NAME' || strtoupper($cell)=='ADD1_' || strtoupper($cell)=='ADD2_' || strtoupper($cell)=='ADD3' || strtoupper($cell)=='CITY' 
                                  || strtoupper($cell)=='STATE' || strtoupper($cell)=='PIN' || strtoupper($cell)=='EMAIL' || strtoupper($cell)=='PHONE' || strtoupper($cell)=='RPHONE'
                                  || strtoupper($cell)=='FREQ' || strtoupper($cell)=='PAIDINST' || strtoupper($cell)=='PENDINST' || strtoupper($cell)=='INSTALNO' || strtoupper($cell)=='PAYMENTMET'
                                  || strtoupper($cell)=='SBROKER' ||  strtoupper($cell)=='REMARKS' || strtoupper($cell)=='SIPREGDT' || strtoupper($cell)=='STPINSCHEM' || strtoupper($cell)=='STPINPLAN'
                                  || strtoupper($cell)=='STPINPRODC' || strtoupper($cell)=='CITYCATEGO'
                                     
                                  )
                                        {
                                        //$message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;

                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }

                              }
                              else
                              {

                                    if($insertRow)
                                    {
                                      //  print_r($dataColumns);

                                      if(strtoupper($dataColumns[$countCell]) === 'FOLIO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'ACNO')//folio_id
                                      {



                                        if($cell || $cell != '')
                                        {
                                             
                                                $folio_id = $cell;
                                              
                                        }
                                        else
                                        {
                                          $insertRow = false;
                                          $impMessage="Folio Id cannot be empty";
                                         
                                        }

                                      }

                                      else if( strtoupper($dataColumns[$countCell]) === 'PRODCODE' || strtoupper($dataColumns[$countCell]) === 'PRODUCT CODE' || strtoupper($dataColumns[$countCell]) === 'PRODUCTCOD')//product_id
                                            {



                                                  if($cell || $cell != '')
                                                  {
                                                         $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else
                                                      {
                                                           $scheme_type_id=$scheme_id->scheme_type_id;                                                    
                                                              $sc_type= $scheme_id->scheme_type; 
                                                            if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                             {
                                                                 $sc='equity';
                                                                 $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                                  $sip_rate=$this->sp->get_sip_rate($condition);
                                                                
                                                                 if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                                
                                                                 
                                                             }
                                                             else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                             {
                                                                 $sc='debt';
                                                                 $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                                  $sip_rate=$this->sp->get_sip_rate($condition);
                                                                  if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                             }
                                                             else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                             {
                                                                   $sc='hybrid';
                                                                   $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                                   $sip_rate=$this->sp->get_sip_rate($condition);
                                                                   if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                             }
                                                             else if($sc_type='')
                                                             {
                                                                 $sip_rate='10';
                                                             }
                                                           
                                                             
                                                             
                                                              $scheme_id=$scheme_id->scheme_id;
                                                              settype($scheme_id,"integer");
    

                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }


                                            }
                                            else if(strtoupper($dataColumns[$countCell]) == 'REGDATE' || strtoupper($dataColumns[$countCell]) == 'REG_DATE' || strtoupper($dataColumns[$countCell]) == 'REGISTRATIONDATE' || strtoupper($dataColumns[$countCell]) == 'SIPREGDT')//added on date
                                            {
                                                      
                                                 /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d-m-Y', $cell);
                                                              if(is_object($date)) {
                                                                       $reg_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                               $reg_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                               $insertRow = false;
                                                                               $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                           $insertRow = false;
                                                           $impMessage="Registration date cannot be empty";
                                                    }*/
                                             
                                                  $cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }
                                              /*if($cell || $cell != '')
                                              {
                                                    
                                                    
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                              $reg_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                            $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Registration date cannot be empty";
                                                
                                              }*/
                                            }
                                            
                                              else if(strtoupper($dataColumns[$countCell]) === 'START DATE' || strtoupper($dataColumns[$countCell]) === 'STARTDATE' || strtoupper($dataColumns[$countCell]) === 'START_DATE')//start_data
                                              {
                                                 /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                              if(is_object($date)) {
                                                                      $start_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $start_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                               $insertRow = false;
                                                                               $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }*/
                                                  $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $start_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $start_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }
                                                     /*$cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                          
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                         //$date->format('Y-m-d');
                                                 
                                                 
                                                        if(is_object($date)){
                                                          $start_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                           $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                             
                                                             $start_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }*/
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'END DATE' ||strtoupper($dataColumns[$countCell]) === 'END_DATE' || strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                              {
                                                
                                                 /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                              if(is_object($date)) {
                                                                      $end_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $end_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                               $insertRow = false;
                                                                               $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }*/
                                                $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        
                                                         $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                              $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                  $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }
                                                /*if($cell || $cell != '')
                                                {
                                                      
                                                      
                                                      $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                          if(is_object($date)) {
                                                               $end_date = $date->format('Y-m-d');
                                                          } else {
                                                                 $insertRow = false;
                                                               $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                          }
                                                        }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="END cannot be empty";
                                                  
                                                }*/

                                              }
                                              /*------Modified by Akshay Karde for Case : - when PAN does not exist ------*/
                                             else  if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                              {
                                                 if($cell || $cell != '')
                                                 {
                                                         $PanNum = trim($cell);
                                                         $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                          $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                         // print_r($c_info);
                                                          if(count($c_info) == 0)
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                           }
                                                          else
                                                          {
                                                              $client_id = $c_info->client_id;
                                                              $familyId = $c_info->family_id;
                                                          }
                                                  }
                                                  else
                                                  {
                                                    $wherePan = array(
                                                      'cb.productId'=>$product_id,
                                                      'cb.folio_number'=>$folio_id,
                                                      'f.broker_id'=>$brokerID
                                                    );
                                                    //var_dump($wherePan);
                                                    if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                    {
                                                      
                                                       $nopan='set';
                                                      //$insertRow = false;
                                                      //$impMessage = "Client does not exist";
                                                    }
                                                    else
                                                    {
                                                      $client_id = $c_info1->client_id;
                                                      $familyId = $c_info1->family_id;
                                                    }
                                                    //$insertRow = false;
                                                    //$impMessage="PAN Number cannot be empty";
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }

                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'TERMINATEDATE' || strtoupper($dataColumns[$countCell]) === 'TERMINATED')
                                              {
                                                    /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                              if(is_object($date)) {
                                                                       $cease_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                               $cease_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                                $insertRow = false;
                                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                     $cease_date=null;
                                                    }*/
                                                 
                                                  $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        
                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      
                                                       $cease_date=null;
                                                    }

                                                  /*if($cell || $cell != '')
                                                  {
                                                        
                                                        $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                            if(is_object($date)) {
                                                                  $cease_date = $date->format('Y-m-d');
                                                            }else {
                                                                   $insertRow = false;
                                                                 $impMessage = "Terminated Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                            }
                                                  }
                                                  else
                                                  {
                                                    $cease_date='';
                                                    
                                                    
                                                  }*/

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                          $installment_amt = $cell;
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "Amount cannot be empty";
                                                    }

                                              }
                                                      /*else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                              {
                                                if($cell || $cell != '')
                                                {
                                                         $PanNum = trim($cell);
                                                         $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                          $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                         // print_r($c_info);
                                                          if(count($c_info) == 0)
                                                          {
                                                            $insertRow = false;
                                                            //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                            $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                          }
                                                          else
                                                          {
                                                            $client_id = $c_info->client_id;
                                                            $familyId = $c_info->family_id;
                                                          }
                                                    }
                                                  else
                                                  {
                                                    $insertRow = false;
                                                    $impMessage="PAN Number cannot be empty";
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }
                                                }*/

                                                  else if(strtoupper($dataColumns[$countCell]) === 'BANK' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAME')//bank_id
                                                  {

                                                          $bank = $cell;
                                                          $bank_id='';




                                                  }
                                                else if(strtoupper($dataColumns[$countCell]) === 'IHNO' || strtoupper($dataColumns[$countCell]) === 'IHNO')//installment_amt
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                          $ref_number = $cell;
                                                      }
                                                      else
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "IHNO. Cannot be empty";
                                                      }


                                                  }
                                                  else if(strtoupper($dataColumns[$countCell]) === 'ECSACNO' || strtoupper($dataColumns[$countCell]) === 'Bank_AccountNo')//installment_amt
                                                  {
                                                      $cell;
                                                        if($cell || $cell != '')
                                                        {
                                                            $Bank_AccountNo = $cell;
                                                        }
                                                        else
                                                        {
                                                            $Bank_AccountNo='';
                                                        }


                                                    }
                                                else if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVNAME' || strtoupper($dataColumns[$countCell]) === 'NAME')//scheme_id
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }
                                                      
                                                }
                                                else{
                                                    
                                                    
                                                }
                                            


                                    $countCell++;
                                    }
                                    
                                     
                                     else {
                                         
                                         
                                        if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVNAME' || strtoupper($dataColumns[$countCell]) === 'NAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                           
                                                      }
                                              }
                                              
                                
                                        
                                    }
                                    
                                   
                          }
                          
                          
                        }
                        


                          if($countRow != 0)
                          {
                              if($nopan == 'set'){
                                    
                                           
                                                            $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              //var_dump($c_info1);
                                                              //$nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }
                                                         
                                                         
                                    }
                                    else if($pan == '' || empty($pan))
                                    {
                                        $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              //var_dump($c_info1);
                                                              //$nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }

                                        
                                    }
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                          
                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");
        
                             $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                           /*if($cease_date !=='' || $cease_date!==null || !empty($cease_date))*/
                           if(!empty(trim($cease_date)))
                           {
                             $end_date=$cease_date;//replace end date for maturity calculation
                           }
                           else
                           {
                               
                                $cease_date=NULL;
                           }
                           
                           
                          $rate_of_return = $sip_rate/400;
                          $install_amt = $installment_amt;
                          
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                         
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                         
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                          // var_dump($num_of_days);
                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($install_amt * $exp1) / $exp4);
                          //var_dump(round($mat_value));



  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;

  $frequency='monthly';
  $added_on = date("Y/m/d");
//print_r($scheme_id->scheme_id);
                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                               //'company_id'=>intval($bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'reg_date'=> $reg_date,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $ref_number,
                                                               'Bank'=>$bank,
                                                                'Bank_AccountNo'=>$Bank_AccountNo,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date_for_insert),
                                                               'frequency'=>$frequency,
                                                                'cease_date'=>$cease_date,
                                                               'installment_amount'=> $installment_amt,
                                                               'rate_of_return'=>$sip_rate,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );
                                                               //var_dump($dataRows['add_SIP_list']);
                                                                //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                                $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                                //var_dump($assets_record);
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {
                                                                 
                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                  $assets_id=$isDuplicateSIP->asset_id;
                                                                 //var_dump($assets_id);
                                                                 
                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));
                                                                 
                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                                    
                                                               }
                                                               else
                                                               {
                                                                
                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);  
                                                            $assets_id=$inserted;
                                                            //var_dump($assets_id);
                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            
                                                            $uploadedStatus = 1;
                                                               }

                             
                             /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);

                               $assets_id=$inserted;
                             
                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                               $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                            $uploadedStatus = 1;*/

                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          
                          
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                            // var_dump($brokerID);
                            //  var_dump($_FILES["import_Sip"]["name"]);
                            //   var_dump($user_id);
                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  /*------------- KARVY SIP IMPort Ends here---------*/
                  /*------------- Frankly SIP IMPort Starts here---------*/
                  else if($_REQUEST['rta_list'] =='frank_excel')
                  {


                    //get tmp_name of file
                    $file = $_FILES["import_Sip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";

                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                  //print_r($excelData);

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

                             /*--changes in franklin new format--*/
                              if($countRow == 0)
                              {
                                $cell = str_replace(array('.'), '', trim($cell));
                                   /*---old format before 30/05/2017---*/
                                  if(strtoupper($cell)=='SNO' ||strtoupper($cell)=='DISTRIBUT0'||strtoupper($cell)=='ACCOUNT_N1'||
                                  strtoupper($cell)=='FUND_OPTI2'||strtoupper($cell)=='SCHEME_NA3'||strtoupper($cell)=='FOLIO_ID'||
                                  strtoupper($cell)=='START_DATE'||strtoupper($cell)=='END_DATE'||strtoupper($cell)=='FREQUENCY'||
                                      strtoupper($cell)=='PROCESSED4'||strtoupper($cell)=='PENDING_I5'||strtoupper($cell)=='TOTAL_INS6'||
                                  strtoupper($cell)=='AMOUNT' || strtoupper($cell)=='INVESTOR_7' || strtoupper($cell)=='ADDRESS1'||
                                  strtoupper($cell)=='ADDRESS2'||strtoupper($cell)=='ADDRESS3'|| strtoupper($cell)=='ADDRESS4' ||
                                  strtoupper($cell)=='CITY' ||
                                  strtoupper($cell)=='PIN_CODE' ||
                                  strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='RES_PHONE8' || strtoupper ($cell)=='OFF_PHONE9' || strtoupper($cell)=='OFF_PHON10' ||
                                  strtoupper($cell)=='CELL_PHO11' || strtoupper($cell)=='FAX_NO' || strtoupper($cell)=='E_MAIL1'
                                  || strtoupper($cell)=='E_MAIL2' || strtoupper($cell)=='E_MAIL3' || strtoupper($cell)=='SIP_TYPE' || strtoupper($cell)=='DOC_TYPE'
                                  || strtoupper($cell)=='DOC_ID' || strtoupper($cell)=='PRODUCT_12' || strtoupper($cell)=='SIP_TXN_NO'
                                  || strtoupper($cell)=='SIP_CANC13' || strtoupper($cell)=='SIP_REG_14' ||  strtoupper($cell)=='ECS_MICRNO' ||
                                   strtoupper($cell)=='ECS_ACCT15' ||
                                    strtoupper($cell)=='ECS_ACCNO' || strtoupper($cell)=='FUND_CODE' || strtoupper($cell)=='EUIN' ||
                                   strtoupper($cell)=='LOCATION16' || strtoupper($cell)=='SIP_SOURCE' || strtoupper($cell)=='IT_PAN_NO'
                                   ||strtoupper($cell)=='SUB_ARN' ||strtoupper($cell)=='INSTALLM17'
                                  /* new format starts here--*/
                                || strtoupper($cell)=='SL NO' ||strtoupper($cell)=='DIST ID'||strtoupper($cell)=='ACCOUNT NUMBER'||
                               strtoupper($cell)=='FUND_OPTION'||strtoupper($cell)=='SCHEME NAME'||strtoupper($cell)=='FOLIO ID'||
                               strtoupper($cell)=='START DATE'||strtoupper($cell)=='END DATE'||strtoupper($cell)=='FREQUENCY'||
                               strtoupper($cell)=='PROCESSED INSTALLMENTS'||strtoupper($cell)=='PENDING INSTALLMENTS'||strtoupper($cell)=='TOTAL INSTALLMENTS'||
                               strtoupper($cell)=='AMOUNT' || strtoupper($cell)=='INVESTOR NAME' || strtoupper($cell)=='ADDRESS1'||
                               strtoupper($cell)=='ADDRESS2'||strtoupper($cell)=='ADDRESS3'|| strtoupper($cell)=='ADDRESS4' ||
                               strtoupper($cell)=='CITY' ||
                               strtoupper($cell)=='PIN_CODE' ||
                               strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='RES_PHONE_NO' || strtoupper ($cell)=='OFF_PHONE_NO1' || strtoupper($cell)=='OFF_PHONE_NO2' ||
                               strtoupper($cell)=='CELL_PHONE_NO' || strtoupper($cell)=='FAX_NO' || strtoupper($cell)=='E_MAIL1'
                               || strtoupper($cell)=='E_MAIL2' || strtoupper($cell)=='E_MAIL3' || strtoupper($cell)=='SIP TYPE' || strtoupper($cell)=='DOCUMENT TYPE'
                               || strtoupper($cell)=='DOCUMENT NUMBER'
                               || strtoupper($cell)=='PRODUCT CODE' || strtoupper($cell)=='SIP TRANSACTION NUMBER'
                               || strtoupper($cell)=='SIP CANCELLED DATE' || strtoupper($cell)=='SIP REGISTERED DATE' ||  strtoupper($cell)=='ECS MICR NO' ||
                                strtoupper($cell)=='ECS ACCOUNT TYPE' ||
                                 strtoupper($cell)=='ECS ACCOUNT NUMBER' || strtoupper($cell)=='DESTINATION FUND CODE' || strtoupper($cell)=='EUIN' ||
                                strtoupper($cell)=='LOCATION_FLAG' || strtoupper($cell)=='SIP_SOURCE' || strtoupper($cell)=='IT_PAN_NO'
                                ||strtoupper($cell)=='SUB_ARN' ||strtoupper($cell)=='INSTALLMENT_DATE'
                                
                               
                                
                                
                               )
                                        {
                                        //$message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;

                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }

                              }
                              else
                              {


                                    if($insertRow)
                                    {



                                            if(strtoupper($dataColumns[$countCell]) === 'PRODUCT_12' || strtoupper($dataColumns[$countCell]) === 'PRODUCT CODE')//product_id
                                            {



                                                  if($cell || $cell != '')
                                                  {
                                                     $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else{
                                                          
                                                                 $scheme_type_id=$scheme_id->scheme_type_id;                                                    
                                                          $sc_type= $scheme_id->scheme_type; 
                                                        if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                         {
                                                             $sc='equity';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                            
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                            
                                                             
                                                         }
                                                         else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                         {
                                                             $sc='debt';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                         {
                                                               $sc='hybrid';
                                                               $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                               $sip_rate=$this->sp->get_sip_rate($condition);
                                                               if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type='')
                                                         {
                                                             $sip_rate='10';
                                                         }
                                                       
                                                         
                                                         
                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");

                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }


                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'ACCOUNT_N1' || strtoupper($dataColumns[$countCell]) === 'ACCOUNT NUMBER')//modified : take account_n1  as folio no
                                            {



                                              if($cell || $cell != '')
                                              {

                                                       $folio_id = trim($cell);

                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";

                                              }

                                            }
                                            /*else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_ID' || strtoupper($dataColumns[$countCell]) === 'FOLIO ID')//folio_id
                                            {



                                              if($cell || $cell != '')
                                              {

                                                       $folio_id = trim($cell);

                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";

                                              }

                                            }*/
                                            else if(strtoupper($dataColumns[$countCell]) == 'START_DATE' || strtoupper($dataColumns[$countCell]) == 'STARTDATE' || strtoupper($dataColumns[$countCell]) == 'START DATE')//start_data
                                            {

                                              /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $start_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $start_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                               $insertRow = false;
                                                                               $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }*/
                                              $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $start_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $start_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                               $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }   
                                              /*if($cell || $cell != '')
                                              {

                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                             $start_date = $date->format('Y-m-d');

                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Start date cannot be empty";

                                              }*/
                                            }
                                            else if(strtoupper($dataColumns[$countCell]) == 'END_DATE' || strtoupper($dataColumns[$countCell]) == 'ENDDATE' ||strtoupper($dataColumns[$countCell]) == 'END DATE')//last_date
                                            {
                                              
                                              /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $end_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $end_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                              $insertRow = false;
                                                                              $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                   }*/
                                              $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                               $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }
                                              /*if($cell || $cell != '')
                                              {

                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                             $end_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                                      }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="END cannot be empty";

                                              }*/

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'ECS_ACCT15' || strtoupper($dataColumns[$countCell]) === 'ECS ACCOUNT TYPE' || strtoupper($dataColumns[$countCell]) === 'BANK')//bank_id
                                            {
                                                    $bank_name = $cell;
                                                    $bank_id=$bankid->bank_id;

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'ECS_ACCNO' || strtoupper($dataColumns[$countCell]) === 'ECS ACCOUNT NUMBER' || strtoupper($dataColumns[$countCell]) === 'Bank_AccountNo')//bank_id
                                            {
                                                    $Bank_AccountNo = $cell;


                                            }
                                             /*------(franklin SIP)Modified by Akshay Karde for Case : - when PAN does not exist ------*/
                                              else if(strtoupper($dataColumns[$countCell]) === 'IT_PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                              {
                                                 if($cell || $cell != '')
                                                {
                                                         $PanNum = trim($cell);
                                                         $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                          $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                         // print_r($c_info);
                                                          if(count($c_info) == 0)
                                                          {

                                                           
                                                              //var_dump($c_info1);
                                                              $nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                           
                                                         }
                                                         else
                                                          {
                                                              $client_id = $c_info->client_id;
                                                              $familyId = $c_info->family_id;

                                                          }
                                                    }
                                                  else
                                                  {
                                                    $wherePan = array(
                                                      'cb.productId'=>$product_id,
                                                      'cb.folio_number'=>$folio_id,
                                                      'f.broker_id'=>$brokerID
                                                    );
                                                    //var_dump($wherePan);
                                                    if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                    {
                                                      
                                                       $nopan='set';
                                                      $insertRow = false;
                                                      $impMessage = "Client does not exist";
                                                    }
                                                    else
                                                    {
                                                      $client_id = $c_info1->client_id;
                                                      $familyId = $c_info1->family_id;
                                                    }
                                                    //$insertRow = false;
                                                    //$impMessage="PAN Number cannot be empty";
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }

                                                }
                                            /*----old format with pan----
                                            else if(strtoupper($dataColumns[$countCell]) === 'IT_PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                            {

                                              if($cell || $cell != '')
                                              {
                                                       $PanNum = trim($cell);
                                                       $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                        $c_info = $this->client->get_client_family_by_pan($whereClient);

                                                        if(count($c_info) == 0)
                                                        {
                                                          $insertRow = false;

                                                          $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                        }
                                                        else
                                                        {
                                                          $client_id = $c_info->client_id;
                                                          $familyId = $c_info->family_id;
                                                        }
                                                  }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="PAN Number cannot be empty";

                                                }
                                              }-----*/
                                              else if(strtoupper($dataColumns[$countCell]) === 'SIP_REG_14' || strtoupper($dataColumns[$countCell]) === 'SIP REGISTERED DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                              {
                                                /*if($cell || $cell != '')
                                                    {
                                                          
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $reg_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $reg_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Registration date cannot be empty";
                                                    } */   
                                               $cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }
                                            
                                                    /*if($cell || $cell != '')
                                                    {

                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                    $reg_date = $date->format('Y-m-d');
                                                              } else {
                                                                     $insertRow = false;
                                                                  $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Registration date cannot be empty";

                                                    }*/
                                                }
                                                else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                           $installment_amt = $cell;
                                                      }
                                                      else
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Amount cannot be empty";
                                                      }

                                                }
                                                else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_ID' || strtoupper($dataColumns[$countCell]) === 'FOLIO ID')////modified take folio no as ref no
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                          $ref_number = $cell;
                                                      }
                                                      else
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Refernce No. Cannot be empty";
                                                      }


                                                  }
                                                /*else if(strtoupper($dataColumns[$countCell]) === 'SIP TRANSACTION NUMBER' || strtoupper($dataColumns[$countCell]) === 'SIP_TXN_NO')//modified take folio no as ref no
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                          $ref_number = $cell;
                                                      }
                                                      else
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "IHNO. Cannot be empty";
                                                      }


                                                  }*/
                                                    else if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVESTOR_7')
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }
                                                      
                                                }


                                    $countCell++;
                                    }
                                    else {
                                        if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVESTOR_7')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                            $invname = trim($cell);
                                                           
                                                      }
                                              }
                                
                                        
                                    }
                          }
                        }


                          if($countRow != 0)
                          {
                              
                             /*if($nopan == 'set'){
                                    
                                           
                                                            $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              //var_dump($c_info1);
                                                              //$nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }
                                                         
                                                         
                                    }*/    
                              
                              
                              
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                             //  $temp_mat_date =new Datetime($maturityDate);
                             //  $temp_issue_date =new DateTime($dateOfIssue);

                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");



                          $rate_of_return = $sip_rate/400;
                          $install_amt = $installment_amt;
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month

                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($install_amt * $exp1) / $exp4);


  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;
  $cease_date=NULL;
  $frequency='monthly';
  $added_on = date("Y/m/d");


                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                                'cease_date'=>$cease_date,
                                                               //'company_id'=>$bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'reg_date'=> $reg_date,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $ref_number,
                                                               'Bank'=>$bank_name,
                                                               'Bank_AccountNo'=>$Bank_AccountNo,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date),
                                                               'frequency'=>$frequency,
                                                               'installment_amount'=> $installment_amt,
                                                               'rate_of_return'=>$sip_rate,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );
                                                               //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                              $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {
                                                                 
                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                 $assets_id=$isDuplicateSIP->asset_id;
                                                                   
                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));
                                                                 
                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                               }
                                                               else
                                                               {
                                                                
                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);  
                                                              $assets_id=$inserted;
                                                            //var_dump($assets_id);     
                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            
                                                            $uploadedStatus = 1;
                                                               }

                             /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                             $assets_id=$inserted;
                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                             $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                            $uploadedStatus = 1;*/
                            //var_dump($inserted);

                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                            // var_dump($brokerID);
                            //  var_dump($_FILES["import_Sip"]["name"]);
                            //   var_dump($user_id);
                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  /*------------- Frankly SIP IMPort Ends here-----------*/
                  /*------------- Sundaram SIP IMPort Starts here---------*/
                  else if($_REQUEST['rta_list'] =='sundaram_excel')
                  {


                    //get tmp_name of file
                    $file = $_FILES["import_Sip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";

                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                  //print_r($excelData);

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
                                  if(strtoupper($cell)=='PRODUCT' ||strtoupper($cell)=='PRODNAME'||strtoupper($cell)=='FOLIO'||
                                  strtoupper($cell)=='INVNAME'|| strtoupper($cell)=='TRXNTYPE'||
                                  strtoupper($cell)=='TRXNNUMBER'||strtoupper($cell)=='AMOUNT'||
                                  strtoupper($cell)=='FROMDATE'|| strtoupper($cell)=='TODATE'||strtoupper($cell)=='NOOFSI'||
                                  strtoupper($cell)=='CEASEDATE'||strtoupper($cell)=='PERIODCITY'||strtoupper($cell)=='PERIOD'||
                                  strtoupper($cell)=='MIN' || strtoupper($cell)=='PAYMODE' || strtoupper($cell)=='TOSCHEME'||
                                  strtoupper($cell)=='REGDATE'||strtoupper($cell)=='SUBBROK'|| strtoupper($cell)=='BROKCODE' ||
                                  strtoupper($cell)=='ECSBANK' ||
                                  strtoupper($cell)=='ECSACNO' ||
                                  strtoupper($cell)=='SERVPROV' || strtoupper($cell)=='REMARKS' || strtoupper ($cell)=='LOCATION' || strtoupper($cell)=='ADDRESS1' ||
                                  strtoupper($cell)=='ADDRESS2' || strtoupper($cell)=='ADDRESS3' || strtoupper($cell)=='ADDRESS4'
                                  || strtoupper($cell)=='ZIPCODE' || strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='PHONE1' || strtoupper($cell)=='PHONE2'
                                  || strtoupper($cell)=='MOBILE' || strtoupper($cell)=='EMAIL'
                                  )
                                        {
                                        //$message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;

                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }

                              }
                              else
                              {


                                    if($insertRow)
                                    {
                                      //  print_r($dataColumns);


                                            if(strtoupper($dataColumns[$countCell]) ==='PRODUCT' || strtoupper($dataColumns[$countCell]) === 'PRODUCT_CODE')//product_id
                                            {



                                                  if($cell || $cell!='')
                                                  {

                                                     $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else{
                                                                 $scheme_type_id=$scheme_id->scheme_type_id;                                                    
                                                          $sc_type= $scheme_id->scheme_type; 
                                                        if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                         {
                                                             $sc='equity';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                            
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                            
                                                             
                                                         }
                                                         else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                         {
                                                             $sc='debt';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                         {
                                                               $sc='hybrid';
                                                               $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                               $sip_rate=$this->sp->get_sip_rate($condition);
                                                               if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type='')
                                                         {
                                                             $sip_rate='10';
                                                         }
                                                       
                                                         
                                                         
                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");


                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }


                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FOLIO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')//folio_id
                                            {



                                              if($cell || $cell != '')
                                              {
                                                   //  $dateOfTransaction= trim($cell);
                                                        $folio_id = $cell;
                                                      //$dateOfTransaction = $date->format('Y-m-d');
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";
                                                // $dateOfTransaction=null;
                                                // $dateOfTransaction = 0;
                                              }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FROMDATE' || strtoupper($dataColumns[$countCell]) === 'START_DATE')//start_data
                                            {
                                                
                                              /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $start_date=$date->format('Y-m-d');
                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $start_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                           $insertRow = false;
                                                                            $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                     $insertRow = false;
                                                      $impMessage="FROM Date cannot be empty";
                                                    }*/
                                              
                                              $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $start_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $start_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }
                                              /*if($cell || $cell != '')
                                              {
                                                    
                                                    
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                            $start_date = $date->format('Y-m-d');

                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Start date cannot be empty";
                                                
                                                
                                              }*/
                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'TODATE' ||strtoupper($dataColumns[$countCell]) === 'END_DATE')//last_date
                                            {
                                              
                                              /*if($cell || $cell != '')
                                                    {
                                                          
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $end_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $end_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }*/
                                              $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }
                                              /*if($cell || $cell != '')
                                              {
                                                    // $date = new DateTime($cell);
                                                    // $End_date = $edate->format('Y-m-d');
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                             $end_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                                      }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="END cannot be empty";
                                                
                                                
                                              }*/

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                            {
                                                  if($cell || $cell != '')
                                                  {
                                                        $installment_amt = $cell;
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Amount cannot be empty";
                                                  }
                                            }
                                           
                                            else if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                          
                                                           $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              
                                                              $whereClient = array('c.name'=>$invname,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                              $c_info = $this->client->get_client_family_by_inv_name($whereClient);
                                                              
                                                                        if(count($c_info) == 0)
                                                                        {
                                                                          $insertRow = false;
                                                                          $impMessage = " Client  ".$invname." doesn't exist";
                                                                        }
                                                                        else
                                                                        {
                                                                            $client_id = $c_info->client_id;
                                                                            $familyId = $c_info->family_id;
                                                                        }
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }
                                            
                                                           
                                                           
                                                      }
                                                    else
                                                    {
                                                        
                                                         $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }
                                            
                                                        
                                                      //$insertRow = false;
                                                      //$impMessage="Investor Name cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }
                                              }
                                              
                                              
                                              
                                              else if(strtoupper($dataColumns[$countCell]) === 'TRXNNUMBER' || strtoupper($dataColumns[$countCell]) === 'SIP_TXN_NO')//installment_amt
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                        $ref_number = $cell;
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "TRXNNUMBER";
                                                    }


                                                }

                                                else if(strtoupper($dataColumns[$countCell]) === 'REGDATE')
                                                {
                                                    /*if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $reg_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $reg_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Registration date cannot be empty";
                                                    }*/
                                                        $cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }    
                                                        /*if($cell || $cell != '')
                                                        {
                                                               $date = DateTime::createFromFormat('m-d-y', $cell);
                                                       if(is_object($date)) {
                                                              $reg_date = $date->format('Y-m-d');
                                                           } else {
                                                        //check if date is in string format d/m/Y
                                                        $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                            if(is_object($date)) {
                                                                 $reg_date = $date->format('Y-m-d');
                                                            } else {
                                                                   $insertRow = false;
                                                                 $impMessage = "Registration Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                            }
                                                            }
                                                          }
                                                          else
                                                          {
                                                            $reg_date=null;
                                                            $reg_date=0;
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date cannot be empty";
                                                          }*/
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'CEASEDATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                              {
                                                        
                                                      
                                                    /*  if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $cease_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $cease_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $cease_date=null;
                                                    }*/
                                                    $cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        
                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }
                                                      /*if($cell || $cell != '')
                                                      {
                                                            // $date = new DateTime($cell);
                                                            // $Start_date = $date->format('Y-m-d');
                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $cease_date = $date->format('Y-m-d');
                                                                }else {
                                                                       $insertRow = false;
                                                                     $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                      }
                                                      else
                                                      {
                                                        $cease_date='';
                                                      }*/

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSBANK' || strtoupper($dataColumns[$countCell]) === 'BANK')//bank_id
                                              {
                                                  $bank_name = $cell;
                                                  $bank_id='';

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSACNO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//installment_amt
                                              {


                                                         $Bank_AccountNO = $cell;

                                               }
                                             else if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//scheme_id
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }
                                                      
                                                }







                                    $countCell++;
                                    } else {
                                        if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                           
                                                      }
                                              }
                                
                                        
                                    }
                          }
                        }


                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                             //  $temp_mat_date =new Datetime($maturityDate);
                             //  $temp_issue_date =new DateTime($dateOfIssue);

                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");
                         $end_date_for_insert=$end_date;//get the value of end date before replace with cease date   
                           if(!empty(trim($cease_date)))
                           {
                             $end_date=$cease_date;//replace end date for maturity calculation
                           }
                           else
                           {
                               
                               $cease_date=NULL;
                           }


                          $rate_of_return = $sip_rate/400;
                          $install_amt = $installment_amt;
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                          // var_dump($num_of_days);
                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($install_amt * $exp1) / $exp4);
                          //var_dump(round($mat_value));



  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;
  $frequency='monthly';//by default
  $added_on = date("Y/m/d");


                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                               //'company_id'=>intval($bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'Bank'=>$bank_name,
                                                               'Bank_AccountNO'=>$Bank_AccountNO,
                                                               'reg_date'=> $reg_date,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $ref_number,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date_for_insert),
                                                               'frequency'=>$frequency,
                                                               'cease_date'=>$cease_date,
                                                               'installment_amount'=> $installment_amt,
                                                               'rate_of_return'=>$sip_rate,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );


                             /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                             $assets_id=$inserted;
                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                             $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                            $uploadedStatus = 1;*/
                            //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                              $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);                       
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {
                                                                 
                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                 $assets_id=$isDuplicateSIP->asset_id;
                                                                 
                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));
                                                                  
                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                   
                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                               }
                                                               else
                                                               {
                                                                
                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);  
                                                               $assets_id=$inserted;
                                                                
                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            $uploadedStatus = 1;
                                                               }
                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                           
                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  /*------------- Sundaram SIP IMPort Ends here---------*/
                  
                  /*----other import starts here--------*/
                  
                  else if($_REQUEST['rta_list'] =='other_import')
                  {


                    //get tmp_name of file
                    $file = $_FILES["import_Sip"]["tmp_name"];
                    //load the excel library
                    $this->load->library('Excel');
                    //read file from path
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";

                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                  //print_r($excelData);

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
                                   $cell = str_replace(array("'"),'', $cell);
                                  if(strtoupper($cell)=='SR NO' ||strtoupper($cell)=='TRXN MODE'||strtoupper($cell)=='GROUP'||
                                  strtoupper($cell)=='INVESTOR'|| strtoupper($cell)=='SCHEME'|| strtoupper($cell)=='PRODUCT CODE'||
                                  strtoupper($cell)=='PAN'||
                                  strtoupper($cell)=='FOLIO NO / DEMAT A/C' ||  strtoupper($cell)=='TRXNNUMBER' ||
                                  strtoupper($cell)=='START DATE'||strtoupper($cell)=='END DATE' ||strtoupper($cell)=='AMOUNT'||
                                  strtoupper($cell)=='FREQUENCY'  
                                  
                                  )
                                        {
                                        //$message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;

                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }

                              }
                              else
                              {
                                   //die('else');

                                    if($insertRow)
                                    {
                                      //  print_r($dataColumns);


                                            if(strtoupper($dataColumns[$countCell]) ==='PRODUCT CODE' || strtoupper($dataColumns[$countCell]) === 'PRODUCT_CODE')//product_id
                                            {



                                                  if($cell || $cell!='')
                                                  {

                                                     $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else{
                                                                 $scheme_type_id=$scheme_id->scheme_type_id;                                                    
                                                          $sc_type= $scheme_id->scheme_type; 
                                                        if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                         {
                                                             $sc='equity';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                            
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                            
                                                             
                                                         }
                                                         else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                         {
                                                             $sc='debt';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                         {
                                                               $sc='hybrid';
                                                               $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                               $sip_rate=$this->sp->get_sip_rate($condition);
                                                               if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type='')
                                                         {
                                                             $sip_rate='10';
                                                         }
                                                       
                                                         
                                                         
                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");


                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }


                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FOLIO NO / DEMAT A/C' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')//folio_id
                                            {



                                              if($cell || $cell != '')
                                              {
                                                   //  $dateOfTransaction= trim($cell);
                                                      $folio_id = $cell;
                                                      //$dateOfTransaction = $date->format('Y-m-d');
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";
                                                // $dateOfTransaction=null;
                                                // $dateOfTransaction = 0;
                                              }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'START DATE' || strtoupper($dataColumns[$countCell]) === 'START_DATE')//start_data
                                            {
                                                
                                              
                                                    
                                                if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                    $start_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $start_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }    
                                              /*if($cell || $cell != '')
                                              {
                                                    
                                                    
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                            $start_date = $date->format('Y-m-d');

                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Start date cannot be empty";
                                                
                                                
                                              }*/
                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'END DATE' ||strtoupper($dataColumns[$countCell]) === 'END_DATE')//last_date
                                            {
                                              
                                              if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                    $end_date = $date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                            $end_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    } 
                                              /*$cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $end_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }*/
                                              /*if($cell || $cell != '')
                                              {
                                                    // $date = new DateTime($cell);
                                                    // $End_date = $edate->format('Y-m-d');
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                             $end_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                                      }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="END cannot be empty";
                                                
                                                
                                              }*/

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'IT_PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                            {

                                              if($cell || $cell != '')
                                              {
                                                       $PanNum = trim($cell);
                                                       $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                        $c_info = $this->client->get_client_family_by_pan($whereClient);

                                                        if(count($c_info) == 0)
                                                        {
                                                          $insertRow = false;

                                                          $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                        }
                                                        else
                                                        {
                                                          $client_id = $c_info->client_id;
                                                          $familyId = $c_info->family_id;
                                                        }
                                                  }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="PAN Number cannot be empty";

                                                }
                                              }
                                            else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                            {
                                                  if($cell || $cell != '')
                                                  {
                                                        $installment_amt = $cell;
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Amount cannot be empty";
                                                  }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                           
                                                           $whereClient = array('c.name'=>$invname,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                            $c_info = $this->client->get_client_family_by_inv_name($whereClient);
                                                           // print_r($c_info);
                                                            if(count($c_info) == 0)
                                                            {
                                                              $insertRow = false;
                                                              //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                              $impMessage = " Client  ".$invname." doesn't exist";
                                                            }
                                                            else
                                                            {
                                                                $client_id = $c_info->client_id;
                                                                $familyId = $c_info->family_id;
                                                            }
                                                      }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Family cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'TRXNNUMBER' || strtoupper($dataColumns[$countCell]) === 'SIP_TXN_NO')//installment_amt
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                        $ref_number = $cell;
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "TRXNNUMBER";
                                                    }


                                                }

                                                else if(strtoupper($dataColumns[$countCell]) === 'REGDATE' || strtoupper($dataColumns[$countCell]) === 'POST DATE')
                                                {

                                                if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $reg_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $reg_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                
                                                                            $insertRow = false;
                                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                       $insertRow = false;
                                                       $impMessage="Registration date cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }
                                                /*$cell=trim(str_replace('/','-',$cell));                                    
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');
                                                
                                                        }
                                                        else
                                                        {
                                                
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }
                                                
                                                    }
                                                
                                                } 
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }*/    
                                                        /*if($cell || $cell != '')
                                                        {
                                                               $date = DateTime::createFromFormat('m-d-y', $cell);
                                                       if(is_object($date)) {
                                                              $reg_date = $date->format('Y-m-d');
                                                           } else {
                                                        //check if date is in string format d/m/Y
                                                        $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                            if(is_object($date)) {
                                                                 $reg_date = $date->format('Y-m-d');
                                                            } else {
                                                                   $insertRow = false;
                                                                 $impMessage = "Registration Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                            }
                                                            }
                                                          }
                                                          else
                                                          {
                                                            $reg_date=null;
                                                            $reg_date=0;
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date cannot be empty";
                                                          }*/
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'CEASEDATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                              {
                                                        
                                                   
                                                    if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $cease_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $cease_date=$date->format('Y-m-d');
                                                                
                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $cease_date=null;
                                                    }
                                                   
                                                       /*$cell=trim(str_replace('/','-',$cell));                                    
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        
                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');
                                                    
                                                            }
                                                            else
                                                            {
                                                    
                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }
                                                    
                                                        }
                                                    
                                                    } 
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }*/
                                                      /*if($cell || $cell != '')
                                                      {
                                                            // $date = new DateTime($cell);
                                                            // $Start_date = $date->format('Y-m-d');
                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $cease_date = $date->format('Y-m-d');
                                                                }else {
                                                                       $insertRow = false;
                                                                     $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                      }
                                                      else
                                                      {
                                                        $cease_date='';
                                                      }*/

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSBANK' || strtoupper($dataColumns[$countCell]) === 'BANK')//bank_id
                                              {
                                                  $bank_name = $cell;
                                                  $bank_id='';

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSACNO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//installment_amt
                                              {


                                                         $Bank_AccountNO = $cell;

                                               }
                                             else if(strtoupper($dataColumns[$countCell]) === 'INVESTOR')//scheme_id
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }
                                                      
                                                }







                                    $countCell++;
                                    } else {
                                        if(strtoupper($dataColumns[$countCell]) === 'INVESTOR')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);
                                                           
                                                      }
                                              }
                                              
                                        if(strtoupper($dataColumns[$countCell]) === 'FOLIO NO / DEMAT A/C')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $folio_id = trim($cell);
                                                           
                                                      }
                                              }      
                                
                                        
                                    }
                          }
                        }


                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                             //  $temp_mat_date =new Datetime($maturityDate);
                             //  $temp_issue_date =new DateTime($dateOfIssue);

                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");
                         $end_date_for_insert=$end_date;//get the value of end date before replace with cease date   
                           if(!empty(trim($cease_date)))
                           {
                             $end_date=$cease_date;//replace end date for maturity calculation
                           }
                           else
                           {
                               
                                $cease_date=NULL;
                           }


                          $rate_of_return = $sip_rate/400;
                          $install_amt = $installment_amt;
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                          // var_dump($num_of_days);
                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($install_amt * $exp1) / $exp4);
                          //var_dump(round($mat_value));



  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;
  $frequency='monthly';//by default
  $reg_date = date("Y/m/d");    
  $added_on = date("Y/m/d");


                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                               //'company_id'=>intval($bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'Bank'=>$bank_name,
                                                               'Bank_AccountNO'=>$Bank_AccountNO,
                                                               'reg_date'=> $reg_date,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $ref_number,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date_for_insert),
                                                               'frequency'=>$frequency,
                                                               'cease_date'=>$cease_date,
                                                               'installment_amount'=> $installment_amt,
                                                               'rate_of_return'=>$sip_rate,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );

                                //var_dump($dataRows['add_SIP_list']);
                             /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                             $assets_id=$inserted;
                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                             $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                            $uploadedStatus = 1;*/
                            //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                              $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);                       
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {
                                                                 
                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                 $assets_id=$isDuplicateSIP->asset_id;
                                                                 //var_dump($assets_id);
                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));
                                                                  
                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                   
                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                               }
                                                               else
                                                               {
                                                                
                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);  
                                                               $assets_id=$inserted;
                                                                //var_dump($assets_id);
                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            $uploadedStatus = 1;
                                                               }
                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                           
                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  
                  /*---other import ends here---------*/
                  /*------------- Simple SIP IMPort Start here---------*/
                  else
                  {
                      //get tmp_name of file
                      $file = $_FILES["import_Sip"]["tmp_name"];
                      //load the excel library
                      $this->load->library('Excel');
                      //read file from path
                      $objPHPExcel = PHPExcel_IOFactory::load($file);
                      //get only the Cell Collection
                      //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                      $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                      //temp variables to hold values
                     $scheme_name="";
                     $scheme_id="";
                     $folio_id="";
                     $installment_amt="";
                     $added_on="";
                     $Start_date="";
                     $End_date="";
                     $frequency="";
                     $Client_id="";
                     $account_no="";

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
                                    if((strtoupper($cell)=='PRODUCT_CODE'||strtoupper($cell)=='SCHEME_NAME'||strtoupper($cell)=='FOLIO_ID'||
                                    strtoupper($cell)=='INV_NAME'||strtoupper($cell)=='TXN_TYPE'||strtoupper($cell)=='SIP_AMOUNT'||
                                    strtoupper($cell)=='TXN_DATE'||strtoupper($cell)=='START_DATE'||strtoupper($cell)=='END_DATE'||
                                    strtoupper($cell)=='FREQUENCY'||strtoupper($cell)=='SIP_REG_DATE'||strtoupper($cell)=='BANK_NAME'||
                                    strtoupper($cell)=='BANK_ACC_NO'||strtoupper($cell)=='PAN_NO'||strtoupper($cell)=='KYC_STATUS'||
                                    strtoupper($cell)=='MAN_DOCUMENT'||strtoupper($cell)=='DOC_ID_NUM'||strtoupper($cell)=='ACCOUNT_NO'||
                                    strtoupper($cell)=='SIP_DATE')
                                    ||
                                  ( strtoupper($cell)=='ZONE'||
                                    strtoupper($cell)=='BRANCH'||
                                    strtoupper($cell)=='LOCATION'||
                                    strtoupper($cell)=='IHNO'||
                                    strtoupper($cell)=='FOLIO'||
                                    strtoupper($cell)=='INVNAME'||
                                    strtoupper($cell)=='REGDATE'||
                                    strtoupper($cell)=='STARTDATE'||
                                    strtoupper($cell)=='ENDDATE'||
                                    strtoupper($cell)=='NOOFINSTAL'||
                                    strtoupper($cell)=='AMOUNT'||
                                    strtoupper($cell)=='SCHEME'||
                                    strtoupper($cell)=='PLAN'||
                                    strtoupper($cell)=='AGENTCODE'||
                                    strtoupper($cell)=='AGENTNAME'||
                                    strtoupper($cell)=='SUBBROKER'||
                                    strtoupper($cell)=='SCHEMENAME'||
                                    strtoupper($cell)=='PAN'||
                                    strtoupper($cell)=='SIPTYPE (ADD THIS IN NARRATION COLUMN)'||
                                    strtoupper($cell)=='SIPMODE'||
                                    strtoupper($cell)=='FUNDCODE'||
                                    strtoupper($cell)=='PRODCODE'||
                                    strtoupper($cell)=='FREQUENCY'||
                                    strtoupper($cell)=='TRTYPE'||
                                    strtoupper($cell)=='TOSCHEME'||
                                    strtoupper($cell)=='TOPLAN'||
                                    strtoupper($cell)=='TERMINATED'||
                                    strtoupper($cell)=='STATUS'||
                                    strtoupper($cell)=='TOPRODUCTC'||
                                    strtoupper($cell)=='TOSCHEMENA'||
                                    strtoupper($cell)=='ECSNO'||
                                    strtoupper($cell)=='ECSBANKNAM'||
                                    strtoupper($cell)=='ECSACNO'||
                                    strtoupper($cell)=='ECSHOLDERN'||
                                    strtoupper($cell)=='REGSLNO'||
                                    strtoupper($cell)=='INVDPID'||
                                    strtoupper($cell)=='INVCLIENTI'||
                                    strtoupper($cell)=='DP_INVNAME'))
                                    {
                                        $dataColumns[$countCell] = $cell;
                                        $countCell++;
                                        $uploadedStatus = 2;
                                          continue;
                                    }
                                    else
                                    {
                                        $message = 'Columns Specified in Excel is not in correct format';
                                        $uploadedStatus = 0;
                                        break;
                                    }
                                }
                                else
                                {

                                      if($insertRow)
                                      {
                                              if(strtoupper($dataColumns[$countCell]) === 'PRODUCT_CODE' ||strtoupper($dataColumns[$countCell]) === 'PRODCODE')//product_id
                                              {

                                                    if($cell || $cell != '')
                                                    {
                                                        $product_id = $cell;

                                                        if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                          $insertRow = false;

                                                         $impMessage = "Scheme id Is Not Matching";
                                                        }
                                                        else{
                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");


                                                        }
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "Scheme cannot be empty";
                                                    }
                                              }

                                              else if(strtoupper($dataColumns[$countCell]) === 'BANK_NAME' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM')//bank_id
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_name = $cell;
                                                        $whereClient = array('bank_name'=>$bank_name);
                                                        if(!$bankid=$this->sp->get_bank_id($whereClient)) {
                                                          $insertRow = false;
                                                        $impMessage = "Bank ID Is Not Matching ";
                                                        }
                                                        else {
                                                          $bank_id=$bankid->bank_id;
                                                          //print_r($bank_id);
                                                        }
                                                        // if(count($f_info) == 0)
                                                        // {
                                                            // $insertRow = false;
                                                            // $impMessage = $famName." Family doesn't exist";
                                                            //family doesn't exist, so add a new family
                                                        // }
                                                        // else
                                                        // {
                                                        //     $familyId = $f_info[0]->family_id;
                                                        //   }
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "Scheme cannot be empty";
                                                    }
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_ID' || strtoupper($dataColumns[$countCell]) === 'FOLIO')//folio_id
                                              {
                                                if($cell || $cell != '')
                                                {
                                                     //  $dateOfTransaction= trim($cell);
                                                        $folio_id = $cell;
                                                        //$dateOfTransaction = $date->format('Y-m-d');
                                                }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="Folio Id cannot be empty";
                                                  // $dateOfTransaction=null;
                                                  // $dateOfTransaction = 0;
                                                }
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'SIP_AMOUNT' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                        $installment_amt = $cell;
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "Sip Amount cannot be empty";
                                                    }
                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'TXN_DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                              {

                                                  if($cell || $cell != '')
                                                  {
                                                          // $date = new DateTime ($cell);
                                                          // $added_on = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                              if(is_object($date)) {
                                                                   $added_on = $date->format('Y-m-d');
                                                              } else {
                                                                     $insertRow = false;
                                                                   $impMessage = "Date of Issue format is not proper (should be dd/mm/yyyy) (error 2)";
                                                              }
                                                  }
                                                  else
                                                  {
                                                    $added_on = date("Y/m/d");
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }
                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'START_DATE' ||strtoupper($dataColumns[$countCell]) === 'STARTDATE')//start_data
                                              {

                                                if($cell || $cell != '')
                                                {
                                                      // $date = new DateTime($cell);
                                                      // $Start_date = $date->format('Y-m-d');
                                                      $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                          if(is_object($date)) {
                                                               $Start_date = $date->format('Y-m-d');
                                                          } else {
                                                                 $insertRow = false;
                                                               $impMessage = "Start Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                          }
                                                }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="Start date cannot be empty";
                                                  // $dateOfTransaction=null;
                                                  // $dateOfTransaction = 0;
                                                }
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'END_DATE' ||strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                              {
                                                if($cell || $cell != '')
                                                {
                                                      // $date = new DateTime($cell);
                                                      // $End_date = $edate->format('Y-m-d');
                                                      $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                          if(is_object($date)) {
                                                               $End_date = $date->format('Y-m-d');
                                                          } else {
                                                                 $insertRow = false;
                                                               $impMessage = "End Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                          }
                                                }
                                                else
                                                {
                                                  $insertRow = false;
                                                  $impMessage="End Date cannot be empty";
                                                  // $dateOfTransaction=null;
                                                  // $dateOfTransaction = 0;
                                                }
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'FREQUENCY')//frewuency
                                              {
                                                   if($cell || $cell != '')
                                                   {
                                                               $frequency = trim($cell);
                                                       }
                                                     else
                                                     {
                                                         $insertRow = false;
                                                         $impMessage = "Frequency cannot be empty";
                                                     }

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                              {
                                                if($cell || $cell != '')
                                                {
                                                         $PanNum = trim($cell);
                                                         $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                          $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                         // print_r($c_info);
                                                          if(count($c_info) == 0)
                                                          {
                                                            $insertRow = false;
                                                            //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                            $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                          }
                                                          else
                                                          {
                                                              $client_id = $c_info->client_id;
                                                              $familyId = $c_info->family_id;
                                                          }
                                                    }
                                                  else
                                                  {
                                                    $insertRow = false;
                                                    $impMessage="PAN Number cannot be empty";
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }
                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ACCOUNT_NO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//account_number
                                              {

                                                  if($cell || $cell != '')
                                                  {
                                                       //  $dateOfTransaction= trim($cell);
                                                          $account_no = $cell;

                                                          //$dateOfTransaction = $date->format('Y-m-d');
                                                  }
                                                  else
                                                  {
                                                    $insertRow = false;
                                                    $impMessage="Account Number cannot be empty";
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }
                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'SIP_DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//SIP_DATE
                                              {
                                                  if($cell || $cell != '')
                                                  {
                                                       //  $dateOfTransaction= trim($cell);
                                                          //$date = trim($cell);
                                                          $date = DateTime::createFromFormat('d/m/Y',$cell);
                                                          $added_on = $date->format('Y-m-d');
                                                  }
                                                  else
                                                  {
                                                    $added_on = date("Y/m/d");
                                                    // $dateOfTransaction=null;
                                                    // $dateOfTransaction = 0;
                                                  }
                                                }
                                              else if(strtoupper($dataColumns[$countCell]) === 'INV_NAME' || strtoupper($dataColumns[$countCell]) === 'INVNAME')//scheme_id
                                              {
                                                        if($cell || $cell != '')
                                                        {
                                                            $invname = $cell;
                                                            //var_dump($cell);
                                                        }
                                                        else
                                                        {
                                                            $insertRow = false;
                                                            $impMessage = "Investors name cannot be empty";
                                                        }
                                              }
                                           

                                      $countCell++;
                                      }
                            }
                          }


                            if($countRow != 0)
                            {
                                if(!$insertRow)
                                {
                                    $imp_data[$countErrRow][1] = $folio_id;
                                    $imp_data[$countErrRow][2] = $invname;
                                    $imp_data[$countErrRow][3] = $impMessage;

                                    $countErrRow++;
                                    $insertRow = true;
                                     $uploadedStatus = 2;
                                    continue;
                                }
                            
                            $type = $this->sp->get_type();
                            $type=$type->type_id;
                            settype($type,"int");



                            $rate_of_return = 10/400;
                            $install_amt = $installment_amt;
                            $date = new DateTime ($Start_date);
                            $start_date = $date->format('Y-m-d');
                            $date = new DateTime ($End_date);
                            $end_date = $date->format('Y-m-d');
                            $mat_value = 0;

                            $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                            $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                            // var_dump($num_of_days);
                            $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                            $exp2 = 1 + $rate_of_return;
                            $exp3 = -0.33333;
                            $exp4 = 1 - (pow($exp2, $exp3));
                            if($exp4 > 0)
                                $mat_value = round(($install_amt * $exp1) / $exp4);


                            $product_id='14';

                               $dataRows['add_SIP_list'] = array (
                                                                 'client_id'=>$client_id,
                                                                 'product_id'=> intval($product_id),
                                                                 'type_id'=>$type,
                                                                 'company_id'=>intval($bank_id),
                                                                 'scheme_id'=> $scheme_id,
                                                                 'folio_no'=> $folio_id,
                                                                 'ref_number'=> $account_no,
                                                                 'start_date'=> date($Start_date),
                                                                 'end_date'=> date($End_date),
                                                                 'frequency'=>$frequency,
                                                                 'installment_amount'=> $installment_amt,
                                                                 'rate_of_return'=>10.00,
                                                                 'expected_mat_value'=> $mat_value,
                                                                 'broker_id'=> $brokerID,
                                                                 'user_id'=> $user_id,
                                                                 'added_on'=> date($added_on)
                                                                 );
                                                                 //var_dump($dataRows['add_SIP_list']);

                               $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);

                               //if(is_array($inserted)){var_dump($inserted);}
                               //var_dump($inserted);
                                $assets_id=$inserted;
                                //var_dump($assets_id);
                               $AddInterest = array('start_date'=> date($Start_date),'end_date'=> date($End_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                              $uploadedStatus = 1;

                                if(is_array($inserted))
                                {
                                    $uploadedStatus = 0;
                                    $message = 'Error while inserting records. '.$assets_id['message'];
                                    break;
                                }
                                $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                                $End_date="";$frequency="";$Client_id="";$account_no="";
                            }
                            if($uploadedStatus == 0)
                                break;

                            $countRow++;
                        }

                        if($dataRows)
                        {
                            if(is_array($inserted))
                            {
                                $uploadedStatus = 0;
                                $message = 'Error while inserting records';
                            } else {
                              // var_dump($brokerID);
                              //  var_dump($_FILES["import_Sip"]["name"]);
                              //   var_dump($user_id);
                                $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                                if($uploadedStatus != 2) {
                                    $uploadedStatus = 1;
                                    $message = "SIP Details Uploaded Successfully";
                                }
                            }
                        }
                      unset($dataColumns, $dataRows);
                  }
                  /*---------- Simple SIP IMPort ends here-----------*/
                }
               }
               else if(isset($_FILES["import_Sip"]) AND ($_FILES["import_Sip"]["type"]=="application/x-dbf" || $_FILES["import_Sip"]["type"]=="application/octet-stream"))//dbf file import code starts here
               {

                      $file = $_FILES["import_Sip"]["tmp_name"];

                       $rta_list=$_POST['rta_list'];

                          include('dbf_class.php');
                          $dbf = new dbf_class($file);

                          $num_rec=$dbf->dbf_num_rec;

                          $field_num=$dbf->dbf_num_field;


                          $excelData=array(array());

                          if($_REQUEST['rta_list'] ==' ')
                         {
                            $message = "Select RTA First";
                         }
                        //if there was an error uploading the file
                         else if ($_FILES["import_Sip"]["name"] == '')
                          {
                              $message = "No file selected";
                          }
                          /*------------- Cams dbf SIP IMPort Start here---------*/
                          else if($_REQUEST['rta_list'] =='cams_excel')
                          {
                            //get tmp_name of file
                            $file = $_FILES["import_Sip"]["tmp_name"];

                            for($j=0; $j<$field_num; $j++){
                                  array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                            }

                            for($i=0; $i<$num_rec; $i++){
                              $temper=array();
                                if ($row = $dbf->getRow($i)) {

                        	        for($j=0; $j<$field_num; $j++){
                                      array_push($temper,$row[$j]);
                            	    }

                                }
                                array_push($excelData,$temper);//array of all rows
                                //print_r($excelData);
                            }
                            
                           $scheme_name="";
                           $scheme_id="";
                           $folio_id="";
                           $installment_amt="";
                           $added_on="";
                           $Start_date="";
                           $End_date="";
                           $frequency="";
                           $Client_id="";
                           $account_no="";

                            $brokerID = $this->session->userdata('broker_id');
                            $user_id = $this->session->userdata('user_id');
                            //get data from excel using range
                            //$excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
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
                                     $cell;

                                      if($countRow == 0)
                                      {
                                          $cell = str_replace(array('.'), '', $cell);
                                          if(strtoupper($cell)=='PRODUCT'||strtoupper($cell)=='SCHEME'||strtoupper($cell)=='FOLIO_NO'||
                                          strtoupper($cell)=='INV_NAME'||strtoupper($cell)=='AUT_TRNTYP'||strtoupper($cell)=='AUTO_TRNO'||
                                          strtoupper($cell)=='AUTO_AMOUNT' || strtoupper($cell)=='AUTO_AMOUN' ||strtoupper($cell)=='FROM_DATE'||strtoupper($cell)=='TO_DATE'||
                                          strtoupper($cell)=='CEASE_DATE' || strtoupper($cell)=='PERIODICIT' || strtoupper($cell)=='PERIODICITY'||strtoupper($cell)=='PERIOD_DAY'||
                                          strtoupper($cell)=='INV_IIN' || strtoupper($cell)=='PAYMENT_MO' || strtoupper($cell)=='PAYMENT_MODE' || strtoupper($cell)=='TARGET_SCH'
                                          || strtoupper($cell)=='TARGET_SCHEME'||
                                          strtoupper($cell)=='REG_DATE'||strtoupper($cell)=='SUBBROKER'|| strtoupper($cell)=='REMARKS' ||
                                          strtoupper($cell)=='TOP_UP_FRQ' ||
                                          strtoupper($cell)=='TOP_UP_AMT' ||
                                          strtoupper($cell)=='AC_TYPE' ||
                                          strtoupper($cell)=='BANK' || strtoupper ($cell)=='BRANCH' || strtoupper($cell)=='INSTRM_NO'
                                          || strtoupper($cell)=='CHEQ_MICR_' || strtoupper($cell)=='CHEQ_MICR_NO' || strtoupper($cell)=='AC_HOLDER_'
                                          || strtoupper($cell)=='AC_HOLDER_NAME' || strtoupper($cell)=='PAN'
                                          || strtoupper($cell)=='TOP_UP_PER'
                                          || strtoupper($cell)=='TOP_UP_PERC' || strtoupper($cell)=='EUIN' || strtoupper($cell)=='SUB_ARN_CO' || strtoupper($cell)=='SUB_ARN_CODE'
                                          || strtoupper($cell)=='TER_LOCATI' || strtoupper($cell)=='TER_LOCATION' || strtoupper($cell)=='SCHEME_COD' || strtoupper($cell)=='TARGET_SCH')
                                                {
                                                  $message='match';
                                              $dataColumns[$countCell] = $cell;
                                              $countCell++;
                                              $uploadedStatus = 2;
                                                continue;
                                                //die();
                                          }
                                          else
                                          {
                                              $message = 'Columns Specified in Excel is not in correct format';
                                              $uploadedStatus = 0;
                                              break;
                                              //die();
                                          }
                                      }
                                      else
                                      {

                                            if($insertRow)
                                            {

                                                    if(strtoupper($dataColumns[$countCell]) === 'PRODUCT' ||strtoupper($dataColumns[$countCell]) === 'PRODUCT')//product_id
                                                    {

                                                          if($cell || $cell != '')
                                                          {
                                                              $product_id = $cell;

                                                              if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                                $insertRow = false;

                                                               $impMessage = "Scheme id Is Not Matching";
                                                              }
                                                              else{
                                                                 $scheme_type_id=$scheme_id->scheme_type_id;
                                                                  $sc_type= $scheme_id->scheme_type;
                                                                if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                                 {
                                                                     $sc='equity';
                                                                     $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                                      $sip_rate=$this->sp->get_sip_rate($condition);

                                                                      if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }


                                                                 }
                                                                 else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                                 {
                                                                     $sc='debt';
                                                                     $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                                      $sip_rate=$this->sp->get_sip_rate($condition);
                                                                      if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }
                                                                 }
                                                                 else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                                 {
                                                                       $sc='hybrid';
                                                                       $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                                       $sip_rate=$this->sp->get_sip_rate($condition);
                                                                       if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }
                                                                 }
                                                                 else if($sc_type='')
                                                                 {
                                                                     $sip_rate='10';
                                                                 }



                                                                  $scheme_id=$scheme_id->scheme_id;
                                                                  settype($scheme_id,"integer");


                                                              }
                                                          }
                                                          else
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Scheme cannot be empty";
                                                          }

                                                    }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')//folio_id
                                                    {
                                                     
                                                      if($cell || $cell != '')
                                                      {
                                                          
                                                              $folio_id = $cell;
                                                            
                                                      }
                                                      else
                                                      {
                                                        $insertRow = false;
                                                        $impMessage="Folio Id cannot be empty";
                                                      }

                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'BANK' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM')//bank_id
                                                    {

                                                            $bank = $cell;
                                                            $bank_id='';




                                                    }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'INSTRM_NO' || strtoupper($dataColumns[$countCell]) === 'INSTRM_NO')//installment_amt
                                                    {
                                                          if($cell || $cell != '')
                                                          {
                                                               $Bank_AccountNO = $cell;
                                                          }
                                                          else
                                                          {
                                                              $Bank_AccountNO = $cell;
                                                          }


                                                      }
                                                      else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                                      {
                                                        if($cell || $cell != '')
                                                        {
                                                              $PanNum = trim($cell);
                                                              $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                               $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                              // print_r($c_info);
                                                               if(count($c_info) == 0)
                                                               {
                                                                   $insertRow = false;
                                                                   $impMessage = "Client does not exist";
                                                                }
                                                               else
                                                               {
                                                                   $client_id = $c_info->client_id;
                                                                   $familyId = $c_info->family_id;
                                                               }
                                                          }
                                                         else
                                                         {
                                                             $wherePan = array(
                                                               'cb.productId'=>$product_id,
                                                               'cb.folio_number'=>$folio_id,
                                                               'f.broker_id'=>$brokerID
                                                             );
                                                             //var_dump($wherePan);
                                                             if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                             {
        
                                                                //$nopan='set';
                                                               $insertRow = false;
                                                               $impMessage = "Client does not exist";
                                                             }
                                                             else
                                                             {
                                                               $client_id = $c_info1->client_id;
                                                               $familyId = $c_info1->family_id;
                                                             }
                                                    
                                                          }

                                                        }
                                                        else if(strtoupper($dataColumns[$countCell]) === 'CEASE_DATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                                        {

                                                          /*if($cell || $cell != '')
                                                          {

                                                                $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                    if(is_object($date)) {
                                                                         $cease_date = $date->format('Y-m-d');
                                                                    }else {
                                                                           $insertRow = false;
                                                                         $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                    }
                                                          }
                                                          else
                                                          {
                                                            $cease_date=null;

                                                          }*/
                                                          $cell=trim(str_replace('/','-',$cell));
                                                            if($cell || $cell != '')
                                                            {
                                                                $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                //var_dump($cell);exit;
                                                                // $date->format('Y-m-d');
                                                                if(is_object($date)){
                                                                    $cease_date=$date->format('Y-m-d');
                                                                }
                                                                else
                                                                {
                                                                    $date = new DateTime($cell);
                                                                    if(is_object($date))
                                                                    {
                                                                        $cease_date=$date->format('Y-m-d');

                                                                    }
                                                                    else
                                                                    {

                                                                        $insertRow = false;
                                                                        $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                    }

                                                                }

                                                            }
                                                            else
                                                            {
                                                               $cease_date=null;
                                                            }

                                                          }
                                                          else if(strtoupper($dataColumns[$countCell]) === 'FROM_DATE' ||strtoupper($dataColumns[$countCell]) === 'STARTDATE')//start_data
                                                          {
                                                            
                                                             $cell=trim(str_replace('/','-',$cell));

                                                            if($cell || $cell != '')
                                                            {
                                                                $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                //var_dump($cell);exit;
                                                                // $date->format('Y-m-d');
                                                                if(is_object($date)){
                                                                    $start_date=$date->format('Y-m-d');
                                                                    
                                                                }
                                                                else
                                                                {
                                                                    $date = new DateTime($cell);
                                                                    if(is_object($date))
                                                                    {
                                                                       $start_date=$date->format('Y-m-d');


                                                                    }
                                                                    else
                                                                    {

                                                                        $insertRow = false;
                                                                        $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                    }

                                                                }

                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="From Date cannot be empty";
                                                            }
                                                            /*if($cell || $cell != '')
                                                            {


                                                                  $date = DateTime::createFromFormat('d/m/Y', $cell);

                                                                      if(is_object($date)) {
                                                                            $start_date = $date->format('Y-m-d');

                                                                      } else {
                                                                             $insertRow = false;
                                                                           $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                      }
                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="From date cannot be empty";


                                                            }*/
                                                          }
                                                          else if(strtoupper($dataColumns[$countCell]) === 'TO_DATE' ||strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                                          {

                                                           $cell=trim(str_replace('/','-',$cell));
                                                            if($cell || $cell != '')
                                                            {
                                                                $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                //var_dump($cell);exit;
                                                                // $date->format('Y-m-d');
                                                                if(is_object($date)){
                                                                    $end_date=$date->format('Y-m-d');
                                                                }
                                                                else
                                                                {
                                                                    $date = new DateTime($cell);
                                                                    if(is_object($date))
                                                                    {
                                                                        $end_date=$date->format('Y-m-d');

                                                                    }
                                                                    else
                                                                    {

                                                                        $insertRow = false;
                                                                        $$impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                    }

                                                                }

                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="End Date cannot be empty";
                                                            }
                                                           /* if($cell || $cell != '')
                                                            {
                                                                  // $date = new DateTime($cell);
                                                                  // $End_date = $edate->format('Y-m-d');
                                                                  $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                      if(is_object($date)) {
                                                                            $end_date = $date->format('Y-m-d');
                                                                      } else {
                                                                             $insertRow = false;
                                                                           $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                      }
                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="To Date cannot be empty";


                                                            }*/

                                                          }


                                                    else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUNT' || strtoupper($dataColumns[$countCell])  === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                                    {
                                                          if($cell || $cell != '')
                                                          {
                                                               $auto_amoun = $cell;
                                                          }
                                                          else
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Auto Amount cannot be empty";
                                                          }

                                                      }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'REG_DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                                    {

                                                      $cell=trim(str_replace('/','-',$cell));
                                                        if($cell || $cell != '')
                                                        {
                                                            $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                            //var_dump($cell);exit;
                                                            // $date->format('Y-m-d');
                                                            if(is_object($date)){
                                                                $reg_date=$date->format('Y-m-d');
                                                            }
                                                            else
                                                            {
                                                                $date = new DateTime($cell);
                                                                if(is_object($date))
                                                                {
                                                                    $reg_date=$date->format('Y-m-d');

                                                                }
                                                                else
                                                                {

                                                                    $insertRow = false;
                                                                    $$impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                }

                                                            }

                                                        }
                                                        else
                                                        {
                                                           $insertRow = false;
                                                           $impMessage="Registration date cannot be empty";
                                                        }
                                                      /*if($cell || $cell != '')
                                                      {

                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                      $reg_date = $date->format('Y-m-d');
                                                                } else {
                                                                       $insertRow = false;
                                                                     $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                      }
                                                      else
                                                      {
                                                        $insertRow = false;
                                                        $impMessage="Registration date cannot be empty";

                                                      }*/
                                                      }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'FREQUENCY')//frewuency
                                                    {
                                                         if($cell || $cell != '')
                                                         {
                                                                     $frequency = trim($cell);
                                                             }
                                                           else
                                                           {
                                                               $insertRow = false;
                                                               $impMessage = "Frequency cannot be empty";
                                                           }

                                                    }

                                                    /*else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                               $PanNum = trim($cell);
                                                               $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                                $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                               // print_r($c_info);
                                                                if(count($c_info) == 0)
                                                                {
                                                                  $insertRow = false;
                                                                  //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                                  $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                                }
                                                                else
                                                                {
                                                                    $client_id = $c_info->client_id;
                                                                    $familyId = $c_info->family_id;
                                                                }
                                                          }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage="PAN Number cannot be empty";
                                                        
                                                        }
                                                      }*/
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ACCOUNT_NO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//account_number
                                                    {

                                                        if($cell || $cell != '')
                                                        {
                                                             //  $dateOfTransaction= trim($cell);
                                                                $account_no = $cell;

                                                                //$dateOfTransaction = $date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                          $insertRow = false;
                                                          $impMessage="Account Number cannot be empty";
                                                          // $dateOfTransaction=null;
                                                          // $dateOfTransaction = 0;
                                                        }
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO' || strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO')//scheme_id
                                                    {
                                                              if($cell || $cell != '')
                                                              {
                                                                  $auto_trno = $cell;
                                                                  //var_dump($cell);
                                                              }
                                                              else
                                                              {
                                                                  $insertRow = false;
                                                                  $impMessage = "AUTO_TRNO cannot be empty";
                                                              }
                                                    }
                                                     else if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//scheme_id
                                                    {
                                                              if($cell || $cell != '')
                                                              {
                                                                  $invname = trim($cell);
                                                              }

                                                    }




                                            $countCell++;
                                            }
                                             else {
                                                if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//client_id
                                                    {

                                                          if($cell || $cell != '')
                                                          {
                                                                   $invname = trim($cell);

                                                              }
                                                      }


                                            }
                                  }
                                }


                                  if($countRow != 0)
                                  {



                                      if(!$insertRow)
                                      {
                                          $imp_data[$countErrRow][1] = $folio_id;
                                          $imp_data[$countErrRow][2] = $invname;
                                          $imp_data[$countErrRow][3] = $impMessage;

                                          $countErrRow++;
                                          $insertRow = true;
                                           $uploadedStatus = 2;
                                          continue;
                                      }
                                     //  $temp_mat_date =new Datetime($maturityDate);
                                     //  $temp_issue_date =new DateTime($dateOfIssue);

                                  $type = $this->sp->get_type();
                                  $type=$type->type_id;
                                  settype($type,"int");
                                     $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                                    if(!empty(trim($cease_date)))
                                   {
                                     $end_date=$cease_date;//replace end date for maturity calculation
                                   }
                                   else
                                   {

                                       echo $cease_date=NULL;
                                   }

                                  $rate_of_return = $sip_rate/400;
                                  $install_amt = $auto_amoun;
                                  $date = new DateTime ($start_date);
                                  $start_date = $date->format('Y-m-d');
                                  $date = new DateTime ($end_date);
                                  $end_date = $date->format('Y-m-d');
                                  $mat_value = 0;

                                  $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                                  $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                                  // var_dump($num_of_days);
                                  $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                                  $exp2 = 1 + $rate_of_return;
                                  $exp3 = -0.33333;
                                  $exp4 = 1 - (pow($exp2, $exp3));
                                  if($exp4 > 0)
                                      $mat_value = round(($auto_amoun * $exp1) / $exp4);

          $product_id = $this->sp->get_product_id();
          $product_id=$product_id->product_id;
          $added_on = date("Y/m/d");
          $frequency='monthly';//by default
                                     $dataRows['add_SIP_list'] = array (
                                                                       'client_id'=>$client_id,
                                                                       'product_id'=> intval($product_id),
                                                                       'type_id'=>$type,
                                                                       //'company_id'=>intval($bank_id),
                                                                       'scheme_id'=> $scheme_id,
                                                                       'folio_no'=> $folio_id,
                                                                       'ref_number'=> $auto_trno,
                                                                       'start_date'=> date($start_date),
                                                                       'end_date'=> date($end_date_for_insert),
                                                                       'frequency'=>$frequency,//default frequency is monthly
                                                                       'cease_date'=>$cease_date,
                                                                       'installment_amount'=> $auto_amoun,
                                                                       'rate_of_return'=>$sip_rate,
                                                                       'reg_date'=>$reg_date,
                                                                       'Bank_AccountNo'=>$Bank_AccountNO,
                                                                       'Bank'=>$bank,
                                                                       'expected_mat_value'=> $mat_value,
                                                                       'broker_id'=> $brokerID,
                                                                       'user_id'=> $user_id,
                                                                       'added_on'=> date($added_on)
                                                                       );
                                                                      //var_dump($dataRows['add_SIP_list']);
                                                                          $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);

                                                                      if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                                       {

                                                                         $client_id=$isDuplicateSIP->client_id;
                                                                         $assets_id=$isDuplicateSIP->asset_id;


                                                                          $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                           $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));

                                                                            $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                           $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                         $uploadedStatus = 1;
                                                                       }
                                                                       else
                                                                       {

                                                                     $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                                                       $assets_id=$inserted;


                                                                     $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                    $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                    $uploadedStatus = 1;
                                                                       }





                                      if(is_array($inserted))
                                      {
                                          $uploadedStatus = 0;
                                          $message = 'Error while inserting records. '.$assets_id['message'];
                                          break;
                                      }
                                      $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                                      $End_date="";$frequency="";$Client_id="";$account_no="";
                                  }
                                  if($uploadedStatus == 0)
                                      break;

                                  $countRow++;
                              }

                              if($dataRows)
                              {
                                  if(is_array($inserted))
                                  {
                                      $uploadedStatus = 0;
                                      $message = 'Error while inserting records';
                                  } else {
                                    // var_dump($brokerID);
                                    //  var_dump($_FILES["import_Sip"]["name"]);
                                    //   var_dump($user_id);
                                      $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                                      if($uploadedStatus != 2) {
                                          $uploadedStatus = 1;
                                          $message = "SIP Details Uploaded Successfully";
                                      }
                                  }
                              }
                            unset($dataColumns, $dataRows);

                          }
                          /*------------- Cams DBF SIP IMPort Ends here---------*/
                          /*------------- KARVY DBF SIP IMPort Start here---------*/
                          else if($_REQUEST['rta_list'] =='karvy_excel')
                          {
                            //get tmp_name of file
                            $file = $_FILES["import_Sip"]["tmp_name"];

                            for($j=0; $j<$field_num; $j++){
                                  array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                            }

                            for($i=0; $i<$num_rec; $i++){
                              $temper=array();
                                if ($row = $dbf->getRow($i)) {

                                  for($j=0; $j<$field_num; $j++){
                                      array_push($temper,$row[$j]);
                                  }

                                }
                                array_push($excelData,$temper);//array of all rows
                                //print_r($excelData);
                            }
                            //load the excel library
                            //$this->load->library('Excel');
                            //read file from path
                            //$objPHPExcel = PHPExcel_IOFactory::load($file);
                            //get only the Cell Collection
                            //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                            //$maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                            //temp variables to hold values
                           $scheme_name="";
                           $scheme_id="";
                           $folio_id="";
                           $installment_amt="";
                           $added_on="";
                           $Start_date="";
                           $End_date="";
                           $frequency="";
                           $Client_id="";
                           $account_no="";
                           $pan="";

                            $brokerID = $this->session->userdata('broker_id');
                            $user_id = $this->session->userdata('user_id');
                            //get data from excel using range
                            //$excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                          //print_r($excelData);

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
                                   if(strtoupper($cell)=='PRODCODE' || strtoupper($cell)=='PRODUCT CODE' ||strtoupper($cell)=='BRANCH'||strtoupper($cell)=='LOCATION'||
                                   strtoupper($cell)=='IHNO'||strtoupper($cell)=='FOLIO'||strtoupper($cell)=='INVNAME'
                                   ||strtoupper($cell)=='INVESTOR NAME'|| strtoupper($cell)=='REGDATE' ||
                                   strtoupper($cell)=='REGISTRATIONDATE' ||strtoupper($cell)=='STARTDATE' ||strtoupper($cell)=='START DATE' || strtoupper($cell)=='ENDDATE'
                                   || strtoupper($cell)=='END DATE'|| strtoupper($cell)=='NOOFINSTAL' || strtoupper($cell)=='NO OF INSTALLMENTS'||strtoupper($cell)=='AMOUNT'||strtoupper($cell)=='SCHEME'||
                                   strtoupper($cell)=='PLAN' || strtoupper($cell)=='AGENTCODE' || strtoupper($cell)=='AGENTNAME'||
                                   strtoupper($cell)=='SUBBROKER' ||strtoupper($cell)=='SCHEMENAME' ||strtoupper($cell)=='SCHEME NAME'|| strtoupper($cell)=='PAN' ||
                                   strtoupper($cell)=='SIPTYPE' || strtoupper($cell)=='SIPMODE' ||
                                   strtoupper($cell)=='SIP MODE' || strtoupper($cell)=='FUNDCODE' ||
                                   strtoupper($cell)=='FUND CODE' || strtoupper($cell)=='ZONE' || strtoupper ($cell)=='FREQUENCY' || strtoupper($cell)=='TRTYPE' ||
                                    strtoupper($cell)=='TOSCHEME' || strtoupper($cell)=='TO SCHEME' || strtoupper($cell)=='PLAN' || strtoupper($cell)=='TOPLAN' || strtoupper($cell)=='TO PLAN' || strtoupper($cell)=='TERMINATEDATE'
                                    || strtoupper($cell)=='TERMINATED'
                                   || strtoupper($cell)=='STATUS' || strtoupper($cell)=='TOPRODUCTC' || strtoupper($cell)=='TOPRODUCTCODE'
                                   || strtoupper($cell)=='TOSCHEMENA' || strtoupper($cell)=='TOSCHEMENAME'
                                   || strtoupper($cell)=='ECSNO' || strtoupper($cell)=='ECSBANKNAM' || strtoupper($cell)=='ECSBANKNAME' || strtoupper($cell)=='ECSACNO'
                                   || strtoupper($cell)=='ECSHOLDERN' || strtoupper($cell)=='ECSHOLDERNAME' || strtoupper($cell)=='REGSLNO' ||  strtoupper($cell)=='INVDPID'
                                    || strtoupper($cell)=='INVCLIENTI' || strtoupper($cell)=='INVCLIENTID' || strtoupper($cell)=='DP_INVNAME' || strtoupper($cell)=='MODIFYFLAG' ||
                                      strtoupper($cell)=='UMRNCODE' || strtoupper($cell)=='SIP_UMRNCODE'
                                      /* new format 10-06-2017---*/
                                   || strtoupper($cell)=='PRODUCTCOD' || strtoupper($cell)=='AGENT' || strtoupper($cell)=='FUND' || strtoupper($cell)=='ACNO' || strtoupper($cell)=='SCHCODE'
                                   || strtoupper($cell)=='SCHDESC'
                                   || strtoupper($cell)=='NAME' || strtoupper($cell)=='ADD1_' || strtoupper($cell)=='ADD2_' || strtoupper($cell)=='ADD3' || strtoupper($cell)=='CITY'
                                   || strtoupper($cell)=='STATE' || strtoupper($cell)=='PIN' || strtoupper($cell)=='EMAIL' || strtoupper($cell)=='PHONE' || strtoupper($cell)=='RPHONE'
                                   || strtoupper($cell)=='FREQ' || strtoupper($cell)=='PAIDINST' || strtoupper($cell)=='PENDINST' || strtoupper($cell)=='INSTALNO' || strtoupper($cell)=='PAYMENTMET'
                                   || strtoupper($cell)=='SBROKER' ||  strtoupper($cell)=='REMARKS' || strtoupper($cell)=='SIPREGDT' || strtoupper($cell)=='STPINSCHEM' || strtoupper($cell)=='STPINPLAN'
                                   || strtoupper($cell)=='STPINPRODC' || strtoupper($cell)=='CITYCATEGO'

                                   )
                                         {
                                         //$message='match';
                                       $dataColumns[$countCell] = $cell;
                                       $countCell++;
                                       $uploadedStatus = 2;
                                         continue;

                                   }
                                   else
                                   {
                                       $message = 'Columns Specified in Excel is not in correct format';
                                       $uploadedStatus = 0;
                                       break;
                                       //die();
                                   }

                               }
                               else
                               {

                                     if($insertRow)
                                     {
                                       //  print_r($dataColumns);

                                       if(strtoupper($dataColumns[$countCell]) === 'FOLIO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' || strtoupper($dataColumns[$countCell]) === 'ACNO')//folio_id
                                       {



                                         if($cell || $cell != '')
                                         {

                                                 $folio_id = $cell;

                                         }
                                         else
                                         {
                                           $insertRow = false;
                                           $impMessage="Folio Id cannot be empty";

                                         }

                                       }

                                       else if( strtoupper($dataColumns[$countCell]) === 'PRODCODE' || strtoupper($dataColumns[$countCell]) === 'PRODUCT CODE' || strtoupper($dataColumns[$countCell]) === 'PRODUCTCOD')//product_id
                                             {



                                                   if($cell || $cell != '')
                                                   {
                                                          $product_id = $cell;

                                                       if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                         $insertRow = false;

                                                        $impMessage = "Scheme id Is Not Matching";
                                                       }
                                                       else
                                                       {
                                                            $scheme_type_id=$scheme_id->scheme_type_id;
                                                               $sc_type= $scheme_id->scheme_type;
                                                             if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                              {
                                                                  $sc='equity';
                                                                  $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                                   $sip_rate=$this->sp->get_sip_rate($condition);

                                                                  if(isset($sip_rate) && !empty($sip_rate)) {
                                                                     $sip_rate=$sip_rate->rate;
                                                                 } else {
                                                                     $sip_rate=10;
                                                                 }


                                                              }
                                                              else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                              {
                                                                  $sc='debt';
                                                                  $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                                   $sip_rate=$this->sp->get_sip_rate($condition);
                                                                   if(isset($sip_rate) && !empty($sip_rate)) {
                                                                     $sip_rate=$sip_rate->rate;
                                                                 } else {
                                                                     $sip_rate=10;
                                                                 }
                                                              }
                                                              else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                              {
                                                                    $sc='hybrid';
                                                                    $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                                    $sip_rate=$this->sp->get_sip_rate($condition);
                                                                    if(isset($sip_rate) && !empty($sip_rate)) {
                                                                     $sip_rate=$sip_rate->rate;
                                                                 } else {
                                                                     $sip_rate=10;
                                                                 }
                                                              }
                                                              else if($sc_type='')
                                                              {
                                                                  $sip_rate='10';
                                                              }



                                                               $scheme_id=$scheme_id->scheme_id;
                                                               settype($scheme_id,"integer");


                                                       }
                                                   }
                                                   else
                                                   {
                                                       $insertRow = false;
                                                       $impMessage = "Scheme cannot be empty";
                                                   }


                                             }
                                             else if(strtoupper($dataColumns[$countCell]) == 'REGDATE' || strtoupper($dataColumns[$countCell]) == 'REG_DATE' || strtoupper($dataColumns[$countCell]) == 'REGISTRATIONDATE' || strtoupper($dataColumns[$countCell]) == 'SIPREGDT')//added on date
                                             {
                                                  if($cell || $cell != '')
                                                     {
                                                           // $date = new DateTime($cell);
                                                           // $Start_date = $date->format('Y-m-d');
                                                           $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                               if(is_object($date)) {
                                                                        $reg_date=$date->format('Y-m-d');

                                                               } else {
                                                                      $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                                $reg_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {
                                                                                $insertRow = false;
                                                                                $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                         }
                                                               }
                                                     }
                                                     else
                                                     {
                                                            $insertRow = false;
                                                            $impMessage="Registration date cannot be empty";
                                                     }

                                                   /*$cell=trim(str_replace('/','-',$cell));
                                                 if($cell || $cell != '')
                                                 {
                                                     $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                     //var_dump($cell);exit;
                                                     // $date->format('Y-m-d');
                                                     if(is_object($date)){
                                                         $reg_date=$date->format('Y-m-d');
                                                     }
                                                     else
                                                     {
                                                         $date = new DateTime($cell);
                                                         if(is_object($date))
                                                         {
                                                             $reg_date=$date->format('Y-m-d');

                                                         }
                                                         else
                                                         {

                                                             $insertRow = false;
                                                             $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                         }

                                                     }

                                                 }
                                                 else
                                                 {
                                                    $insertRow = false;
                                                    $impMessage="Registration date cannot be empty";
                                                 }*/
                                               /*if($cell || $cell != '')
                                               {


                                                     $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                         if(is_object($date)) {
                                                               $reg_date = $date->format('Y-m-d');
                                                         } else {
                                                                $insertRow = false;
                                                             $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                         }
                                               }
                                               else
                                               {
                                                 $insertRow = false;
                                                 $impMessage="Registration date cannot be empty";

                                               }*/
                                             }

                                               else if(strtoupper($dataColumns[$countCell]) === 'START DATE' || strtoupper($dataColumns[$countCell]) === 'STARTDATE' || strtoupper($dataColumns[$countCell]) === 'START_DATE')//start_data
                                               {
                                                  if($cell || $cell != '')
                                                     {
                                                           // $date = new DateTime($cell);
                                                           // $Start_date = $date->format('Y-m-d');
                                                           $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                               if(is_object($date)) {
                                                                       $start_date=$date->format('Y-m-d');

                                                               } else {
                                                                      $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                               $start_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {
                                                                                $insertRow = false;
                                                                                $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                                         }
                                                               }
                                                     }
                                                     else
                                                     {
                                                       $insertRow = false;
                                                       $impMessage="Start Date cannot be empty";
                                                     }
                                                  /* $cell=trim(str_replace('/','-',$cell));
                                                     if($cell || $cell != '')
                                                     {
                                                         $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                         //var_dump($cell);exit;
                                                         // $date->format('Y-m-d');
                                                         if(is_object($date)){
                                                             $start_date=$date->format('Y-m-d');
                                                         }
                                                         else
                                                         {
                                                             $date = new DateTime($cell);
                                                             if(is_object($date))
                                                             {
                                                                 $start_date=$date->format('Y-m-d');

                                                             }
                                                             else
                                                             {

                                                                 $insertRow = false;
                                                                 $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                             }

                                                         }

                                                     }
                                                     else
                                                     {
                                                       $insertRow = false;
                                                       $impMessage="Start Date cannot be empty";
                                                     }*/
                                                      /*$cell=trim(str_replace('/','-',$cell));
                                                     if($cell || $cell != '')
                                                     {

                                                         $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                         //var_dump($cell);exit;
                                                          //$date->format('Y-m-d');


                                                         if(is_object($date)){
                                                           $start_date=$date->format('Y-m-d');
                                                         }
                                                         else
                                                         {
                                                            $date = new DateTime($cell);
                                                             if(is_object($date))
                                                             {

                                                              $start_date=$date->format('Y-m-d');

                                                             }
                                                             else
                                                             {

                                                                 $insertRow = false;
                                                                 $impMessage = "Start Date format is not proper (should be dd/mm/yyyy)";
                                                             }

                                                         }

                                                     }
                                                     else
                                                     {
                                                       $insertRow = false;
                                                       $impMessage="Start Date cannot be empty";
                                                     }*/
                                               }
                                               else if(strtoupper($dataColumns[$countCell]) === 'END DATE' ||strtoupper($dataColumns[$countCell]) === 'END_DATE' || strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                               {

                                                  if($cell || $cell != '')
                                                     {
                                                           // $date = new DateTime($cell);
                                                           // $Start_date = $date->format('Y-m-d');
                                                           $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                               if(is_object($date)) {
                                                                       $end_date=$date->format('Y-m-d');

                                                               } else {
                                                                      $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                               $end_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {
                                                                                $insertRow = false;
                                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                         }
                                                               }
                                                     }
                                                     else
                                                     {
                                                       $insertRow = false;
                                                       $impMessage="End Date cannot be empty";
                                                     }
                                                 /*$cell=trim(str_replace('/','-',$cell));
                                                     if($cell || $cell != '')
                                                     {

                                                          $date =DateTime::createFromFormat('m-d-Y',$cell);

                                                         //var_dump($cell);exit;
                                                         // $date->format('Y-m-d');
                                                         if(is_object($date)){
                                                               $end_date=$date->format('Y-m-d');
                                                         }
                                                         else
                                                         {
                                                             $date = new DateTime($cell);
                                                             if(is_object($date))
                                                             {
                                                                   $end_date=$date->format('Y-m-d');

                                                             }
                                                             else
                                                             {

                                                                 $insertRow = false;
                                                                 $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                             }

                                                         }

                                                     }
                                                     else
                                                     {
                                                       $insertRow = false;
                                                       $impMessage="End Date cannot be empty";
                                                     }*/
                                                 /*if($cell || $cell != '')
                                                 {


                                                       $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                           if(is_object($date)) {
                                                                $end_date = $date->format('Y-m-d');
                                                           } else {
                                                                  $insertRow = false;
                                                                $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                           }
                                                         }
                                                 else
                                                 {
                                                   $insertRow = false;
                                                   $impMessage="END cannot be empty";

                                                 }*/

                                               }
                                               /*------Modified by Akshay Karde for Case : - when PAN does not exist ------*/
                                              else  if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                               {
                                                  if($cell || $cell != '')
                                                  {
                                                          $PanNum = trim($cell);
                                                          $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                           $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                          // print_r($c_info);
                                                           if(count($c_info) == 0)
                                                           {
                                                               $insertRow = false;
                                                               $impMessage = "Client does not exist";
                                                            }
                                                           else
                                                           {
                                                               $client_id = $c_info->client_id;
                                                               $familyId = $c_info->family_id;
                                                           }
                                                   }
                                                   else
                                                   {
                                                     $wherePan = array(
                                                       'cb.productId'=>$product_id,
                                                       'cb.folio_number'=>$folio_id,
                                                       'f.broker_id'=>$brokerID
                                                     );
                                                     //var_dump($wherePan);
                                                     if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                     {

                                                        $nopan='set';
                                                       //$insertRow = false;
                                                       //$impMessage = "Client does not exist";
                                                     }
                                                     else
                                                     {
                                                       $client_id = $c_info1->client_id;
                                                       $familyId = $c_info1->family_id;
                                                     }
                                                    
                                                   }

                                                 }
                                               else if(strtoupper($dataColumns[$countCell]) === 'TERMINATEDATE' || strtoupper($dataColumns[$countCell]) === 'TERMINATED')
                                               {
                                                     if($cell || $cell != '')
                                                     {
                                                           // $date = new DateTime($cell);
                                                           // $Start_date = $date->format('Y-m-d');
                                                           $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                               if(is_object($date)) {
                                                                        $cease_date=$date->format('Y-m-d');

                                                               } else {
                                                                      $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                                $cease_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {
                                                                                 $insertRow = false;
                                                                                 $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                         }
                                                               }
                                                     }
                                                     else
                                                     {
                                                      $cease_date=null;
                                                     }

                                                   /*$cell=trim(str_replace('/','-',$cell));
                                                     if($cell || $cell != '')
                                                     {
                                                         $date =DateTime::createFromFormat('m-d-Y',$cell);

                                                         if(is_object($date)){
                                                             $cease_date=$date->format('Y-m-d');
                                                         }
                                                         else
                                                         {
                                                             $date = new DateTime($cell);
                                                             if(is_object($date))
                                                             {
                                                                 $cease_date=$date->format('Y-m-d');

                                                             }
                                                             else
                                                             {

                                                                 $insertRow = false;
                                                                 $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                             }

                                                         }

                                                     }
                                                     else
                                                     {

                                                        $cease_date=null;
                                                     }*/

                                                   /*if($cell || $cell != '')
                                                   {

                                                         $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                             if(is_object($date)) {
                                                                   $cease_date = $date->format('Y-m-d');
                                                             }else {
                                                                    $insertRow = false;
                                                                  $impMessage = "Terminated Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                             }
                                                   }
                                                   else
                                                   {
                                                     $cease_date='';


                                                   }*/

                                               }
                                               else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                               {
                                                     if($cell || $cell != '')
                                                     {
                                                           $installment_amt = $cell;
                                                     }
                                                     else
                                                     {
                                                         $insertRow = false;
                                                         $impMessage = "Amount cannot be empty";
                                                     }

                                               }
                                                       /*else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                               {
                                                 if($cell || $cell != '')
                                                 {
                                                          $PanNum = trim($cell);
                                                          $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                           $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                          // print_r($c_info);
                                                           if(count($c_info) == 0)
                                                           {
                                                             $insertRow = false;
                                                             //$impMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                             $impMessage = " PAN No ".$PanNum." doesn't exist";
                                                           }
                                                           else
                                                           {
                                                             $client_id = $c_info->client_id;
                                                             $familyId = $c_info->family_id;
                                                           }
                                                     }
                                                   else
                                                   {
                                                     $insertRow = false;
                                                     $impMessage="PAN Number cannot be empty";
                                                     // $dateOfTransaction=null;
                                                     // $dateOfTransaction = 0;
                                                   }
                                                 }*/

                                                   else if(strtoupper($dataColumns[$countCell]) === 'BANK' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAME')//bank_id
                                                   {

                                                           $bank = $cell;
                                                           $bank_id='';




                                                   }
                                                 else if(strtoupper($dataColumns[$countCell]) === 'IHNO' || strtoupper($dataColumns[$countCell]) === 'IHNO')//installment_amt
                                                 {
                                                       if($cell || $cell != '')
                                                       {
                                                           $ref_number = $cell;
                                                       }
                                                       else
                                                       {
                                                           $insertRow = false;
                                                           $impMessage = "IHNO. Cannot be empty";
                                                       }


                                                   }
                                                   else if(strtoupper($dataColumns[$countCell]) === 'ECSACNO' || strtoupper($dataColumns[$countCell]) === 'Bank_AccountNo')//installment_amt
                                                   {
                                                       $cell;
                                                         if($cell || $cell != '')
                                                         {
                                                             $Bank_AccountNo = $cell;
                                                         }
                                                         else
                                                         {
                                                             $Bank_AccountNo='';
                                                         }


                                                     }
                                                 else if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVNAME' || strtoupper($dataColumns[$countCell]) === 'NAME')//scheme_id
                                                 {
                                                       if($cell || $cell != '')
                                                       {
                                                           $invname = trim($cell);
                                                       }

                                                 }
                                                 else{


                                                 }



                                     $countCell++;
                                     }


                                      else {


                                         if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVNAME' || strtoupper($dataColumns[$countCell]) === 'NAME')//client_id
                                             {

                                                   if($cell || $cell != '')
                                                   {
                                                            $invname = trim($cell);

                                                       }
                                               }



                                     }


                           }


                         }


                                  if($countRow != 0)
                                  {

                                                    if($nopan == 'set'){


                                                                         $wherePan = array(
                                                                           'cb.productId'=>$product_id,
                                                                           'cb.folio_number'=>$folio_id,
                                                                           'f.broker_id'=>$brokerID
                                                                         );
                                                                         //var_dump($wherePan);
                                                                         if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                                         {
                                                                           //var_dump($c_info1);
                                                                           //$nopan='set';
                                                                           $insertRow = false;
                                                                           $impMessage = "Client does not exist";
                                                                         }
                                                                         else
                                                                         {
                                                                           $client_id = $c_info1->client_id;
                                                                           $familyId = $c_info1->family_id;
                                                                         }


                                                 }
                                                 else if($pan == '' || empty($pan))
                                                 {
                                                     $wherePan = array(
                                                                           'cb.productId'=>$product_id,
                                                                           'cb.folio_number'=>$folio_id,
                                                                           'f.broker_id'=>$brokerID
                                                                         );
                                                                         //var_dump($wherePan);
                                                                         if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                                         {
                                                                           //var_dump($c_info1);
                                                                           //$nopan='set';
                                                                           $insertRow = false;
                                                                           $impMessage = "Client does not exist";
                                                                         }
                                                                         else
                                                                         {
                                                                           $client_id = $c_info1->client_id;
                                                                           $familyId = $c_info1->family_id;
                                                                         }


                                                 }
                                      if(!$insertRow)
                                      {
                                          $imp_data[$countErrRow][1] = $folio_id;
                                          $imp_data[$countErrRow][2] = $invname;
                                          $imp_data[$countErrRow][3] = $impMessage;

                                          $countErrRow++;
                                          $insertRow = true;
                                           $uploadedStatus = 2;
                                          continue;
                                      }

                                  $type = $this->sp->get_type();
                                  $type=$type->type_id;
                                  settype($type,"int");

                                     $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                                   /*if($cease_date !=='' || $cease_date!==null || !empty($cease_date))*/
                                   if(!empty(trim($cease_date)))
                                   {
                                     $end_date=$cease_date;//replace end date for maturity calculation
                                   }
                                   else
                                   {

                                        $cease_date=NULL;
                                   }


                                  $rate_of_return = $sip_rate/400;
                                  $install_amt = $installment_amt;
                                  $date = new DateTime ($start_date);
                                  $start_date = $date->format('Y-m-d');
                                  $date = new DateTime ($end_date);
                                  $end_date = $date->format('Y-m-d');
                                  $mat_value = 0;

                                  $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                                  $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                                  // var_dump($num_of_days);
                                  $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                                  $exp2 = 1 + $rate_of_return;
                                  $exp3 = -0.33333;
                                  $exp4 = 1 - (pow($exp2, $exp3));
                                  if($exp4 > 0)
                                      $mat_value = round(($install_amt * $exp1) / $exp4);
                                  //var_dump(round($mat_value));



          $product_id = $this->sp->get_product_id();
          $product_id=$product_id->product_id;

          $frequency='monthly';
          $added_on = date("Y/m/d");
        //print_r($scheme_id->scheme_id);
                                     $dataRows['add_SIP_list'] = array (
                                                                       'client_id'=>$client_id,
                                                                       'product_id'=> intval($product_id),
                                                                       'type_id'=>$type,
                                                                       //'company_id'=>intval($bank_id),
                                                                       'scheme_id'=> $scheme_id,
                                                                       'reg_date'=> $reg_date,
                                                                       'folio_no'=> $folio_id,
                                                                       'ref_number'=> $ref_number,
                                                                       'Bank'=>$bank,
                                                                        'Bank_AccountNo'=>$Bank_AccountNo,
                                                                       'start_date'=> date($start_date),
                                                                       'end_date'=> date($end_date_for_insert),
                                                                       'frequency'=>$frequency,
                                                                        'cease_date'=>$cease_date,
                                                                       'installment_amount'=> $installment_amt,
                                                                       'rate_of_return'=>$sip_rate,
                                                                       'expected_mat_value'=> $mat_value,
                                                                       'broker_id'=> $brokerID,
                                                                       'user_id'=> $user_id,
                                                                       'added_on'=> date($added_on)
                                                                       );
                                                                       //var_dump($dataRows['add_SIP_list']);
                                                                        //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                                        $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                                      if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                                       {

                                                                         $client_id=$isDuplicateSIP->client_id;
                                                                          $assets_id=$isDuplicateSIP->asset_id;
                                                                         //var_dump($assets_id);

                                                                          $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                           $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));

                                                                            $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                           $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                         $uploadedStatus = 1;

                                                                       }
                                                                       else
                                                                       {

                                                                     $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                                                    $assets_id=$inserted;
                                                                   //var_dump($assets_id);
                                                                     $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                    $status=$this->add_sipinterest_details($AddInterest, $assets_id);

                                                                    $uploadedStatus = 1;
                                                                       }


                                     /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);

                                       $assets_id=$inserted;

                                     $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                       $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                    $uploadedStatus = 1;*/

                                      if(is_array($inserted))
                                      {
                                          $uploadedStatus = 0;
                                          $message = 'Error while inserting records. '.$assets_id['message'];
                                          break;
                                      }
                                      $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                                      $End_date="";$frequency="";$Client_id="";$account_no="";
                                  }
                                  if($uploadedStatus == 0)
                                      break;

                                  $countRow++;
                              }

                              if($dataRows)
                              {
                                  if(is_array($inserted))
                                  {
                                      $uploadedStatus = 0;
                                      $message = 'Error while inserting records';
                                  } else {
                                    // var_dump($brokerID);
                                    //  var_dump($_FILES["import_Sip"]["name"]);
                                    //   var_dump($user_id);
                                      $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                                      if($uploadedStatus != 2) {
                                          $uploadedStatus = 1;
                                          $message = "SIP Details Uploaded Successfully";
                                      }
                                  }
                              }
                            unset($dataColumns, $dataRows);

                          }
                          /*------------- KARVY DBF SIP IMPort Ends here---------*/
                          /*------------- Frankly DBF SIP IMPort Starts here---------*/
                          else if($_REQUEST['rta_list'] =='frank_excel')
                          {


                            $file = $_FILES["import_Sip"]["tmp_name"];

                            for($j=0; $j<$field_num; $j++){
                                  array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                            }

                            for($i=0; $i<$num_rec; $i++){
                              $temper=array();
                                if ($row = $dbf->getRow($i)) {

                                  for($j=0; $j<$field_num; $j++){
                                      array_push($temper,$row[$j]);
                                  }

                                }
                                array_push($excelData,$temper);//array of all rows
                                //print_r($excelData);
                            }
                            
                          
                           $scheme_name="";
                           $scheme_id="";
                           $folio_id="";
                           $installment_amt="";
                           $added_on="";
                           $Start_date="";
                           $End_date="";
                           $frequency="";
                           $Client_id="";
                           $account_no="";

                            $brokerID = $this->session->userdata('broker_id');
                            $user_id = $this->session->userdata('user_id');
                            

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

                                     /*--changes in franklin new format--*/
                                      if($countRow == 0)
                                      {
                                        $cell = str_replace(array('.'), '', trim($cell));
                                           /*---old format before 30/05/2017---*/
                                          if(strtoupper($cell)=='SNO' ||strtoupper($cell)=='DISTRIBUT0'||strtoupper($cell)=='ACCOUNT_N1'||
                                          strtoupper($cell)=='FUND_OPTI2'||strtoupper($cell)=='SCHEME_NA3'||strtoupper($cell)=='FOLIO_ID'||
                                          strtoupper($cell)=='START_DATE'||strtoupper($cell)=='END_DATE'||strtoupper($cell)=='FREQUENCY'||
                                              strtoupper($cell)=='PROCESSED4'||strtoupper($cell)=='PENDING_I5'||strtoupper($cell)=='TOTAL_INS6'||
                                          strtoupper($cell)=='AMOUNT' || strtoupper($cell)=='INVESTOR_7' || strtoupper($cell)=='ADDRESS1'||
                                          strtoupper($cell)=='ADDRESS2'||strtoupper($cell)=='ADDRESS3'|| strtoupper($cell)=='ADDRESS4' ||
                                          strtoupper($cell)=='CITY' ||
                                          strtoupper($cell)=='PIN_CODE' ||
                                          strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='RES_PHONE8' || strtoupper ($cell)=='OFF_PHONE9' || strtoupper($cell)=='OFF_PHON10' ||
                                          strtoupper($cell)=='CELL_PHO11' || strtoupper($cell)=='FAX_NO' || strtoupper($cell)=='E_MAIL1'
                                          || strtoupper($cell)=='E_MAIL2' || strtoupper($cell)=='E_MAIL3' || strtoupper($cell)=='SIP_TYPE' || strtoupper($cell)=='DOC_TYPE'
                                          || strtoupper($cell)=='DOC_ID' || strtoupper($cell)=='PRODUCT_12' || strtoupper($cell)=='SIP_TXN_NO'
                                          || strtoupper($cell)=='SIP_CANC13' || strtoupper($cell)=='SIP_REG_14' ||  strtoupper($cell)=='ECS_MICRNO' ||
                                           strtoupper($cell)=='ECS_ACCT15' ||
                                            strtoupper($cell)=='ECS_ACCNO' || strtoupper($cell)=='FUND_CODE' || strtoupper($cell)=='EUIN' ||
                                           strtoupper($cell)=='LOCATION16' || strtoupper($cell)=='SIP_SOURCE' || strtoupper($cell)=='IT_PAN_NO'
                                           ||strtoupper($cell)=='SUB_ARN' ||strtoupper($cell)=='INSTALLM17'
                                          /* new format starts here--*/
                                        || strtoupper($cell)=='SL NO' ||strtoupper($cell)=='DIST ID'||strtoupper($cell)=='ACCOUNT NUMBER'||
                                       strtoupper($cell)=='FUND_OPTION'||strtoupper($cell)=='SCHEME NAME'||strtoupper($cell)=='FOLIO ID'||
                                       strtoupper($cell)=='START DATE'||strtoupper($cell)=='END DATE'||strtoupper($cell)=='FREQUENCY'||
                                       strtoupper($cell)=='PROCESSED INSTALLMENTS'||strtoupper($cell)=='PENDING INSTALLMENTS'||strtoupper($cell)=='TOTAL INSTALLMENTS'||
                                       strtoupper($cell)=='AMOUNT' || strtoupper($cell)=='INVESTOR NAME' || strtoupper($cell)=='ADDRESS1'||
                                       strtoupper($cell)=='ADDRESS2'||strtoupper($cell)=='ADDRESS3'|| strtoupper($cell)=='ADDRESS4' ||
                                       strtoupper($cell)=='CITY' ||
                                       strtoupper($cell)=='PIN_CODE' ||
                                       strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='RES_PHONE_NO' || strtoupper ($cell)=='OFF_PHONE_NO1' || strtoupper($cell)=='OFF_PHONE_NO2' ||
                                       strtoupper($cell)=='CELL_PHONE_NO' || strtoupper($cell)=='FAX_NO' || strtoupper($cell)=='E_MAIL1'
                                       || strtoupper($cell)=='E_MAIL2' || strtoupper($cell)=='E_MAIL3' || strtoupper($cell)=='SIP TYPE' || strtoupper($cell)=='DOCUMENT TYPE'
                                       || strtoupper($cell)=='DOCUMENT NUMBER'
                                       || strtoupper($cell)=='PRODUCT CODE' || strtoupper($cell)=='SIP TRANSACTION NUMBER'
                                       || strtoupper($cell)=='SIP CANCELLED DATE' || strtoupper($cell)=='SIP REGISTERED DATE' ||  strtoupper($cell)=='ECS MICR NO' ||
                                        strtoupper($cell)=='ECS ACCOUNT TYPE' ||
                                         strtoupper($cell)=='ECS ACCOUNT NUMBER' || strtoupper($cell)=='DESTINATION FUND CODE' || strtoupper($cell)=='EUIN' ||
                                        strtoupper($cell)=='LOCATION_FLAG' || strtoupper($cell)=='SIP_SOURCE' || strtoupper($cell)=='IT_PAN_NO'
                                        ||strtoupper($cell)=='SUB_ARN' ||strtoupper($cell)=='INSTALLMENT_DATE'
                                       )
                                                {
                                                //$message='match';
                                              $dataColumns[$countCell] = $cell;
                                              $countCell++;
                                              $uploadedStatus = 2;
                                                continue;

                                          }
                                          else
                                          {
                                              $message = 'Columns Specified in Excel is not in correct format';
                                              $uploadedStatus = 0;
                                              break;
                                              //die();
                                          }

                                      }
                                      else
                                      {


                                            if($insertRow)
                                            {



                                                    if(strtoupper($dataColumns[$countCell]) === 'PRODUCT_12' || strtoupper($dataColumns[$countCell]) === 'PRODUCT CODE')//product_id
                                                    {



                                                          if($cell || $cell != '')
                                                          {
                                                             $product_id = $cell;

                                                              if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                                $insertRow = false;

                                                               $impMessage = "Scheme id Is Not Matching";
                                                              }
                                                              else{

                                                                         $scheme_type_id=$scheme_id->scheme_type_id;
                                                                  $sc_type= $scheme_id->scheme_type;
                                                                if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                                 {
                                                                     $sc='equity';
                                                                     $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                                      $sip_rate=$this->sp->get_sip_rate($condition);

                                                                      if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }


                                                                 }
                                                                 else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                                 {
                                                                     $sc='debt';
                                                                     $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                                      $sip_rate=$this->sp->get_sip_rate($condition);
                                                                      if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }
                                                                 }
                                                                 else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                                 {
                                                                       $sc='hybrid';
                                                                       $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                                       $sip_rate=$this->sp->get_sip_rate($condition);
                                                                       if(isset($sip_rate) && !empty($sip_rate)) {
                                                                            $sip_rate=$sip_rate->rate;
                                                                        } else {
                                                                            $sip_rate=10;
                                                                        }
                                                                 }
                                                                 else if($sc_type='')
                                                                 {
                                                                     $sip_rate='10';
                                                                 }



                                                                  $scheme_id=$scheme_id->scheme_id;
                                                                  settype($scheme_id,"integer");

                                                              }
                                                          }
                                                          else
                                                          {
                                                              $insertRow = false;
                                                              $impMessage = "Scheme cannot be empty";
                                                          }


                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'FOLIO_ID' || strtoupper($dataColumns[$countCell]) === 'FOLIO ID')//folio_id
                                                    {



                                                      if($cell || $cell != '')
                                                      {

                                                               $folio_id = trim($cell);

                                                      }
                                                      else
                                                      {
                                                        $insertRow = false;
                                                        $impMessage="Folio Id cannot be empty";

                                                      }

                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) == 'START_DATE' || strtoupper($dataColumns[$countCell]) == 'STARTDATE' || strtoupper($dataColumns[$countCell]) == 'START DATE')//start_data
                                                    {

                                                      $cell=trim(str_replace('/','-',$cell));
                                                            if($cell || $cell != '')
                                                            {
                                                                $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                //var_dump($cell);exit;
                                                                // $date->format('Y-m-d');
                                                                if(is_object($date)){
                                                                    $start_date=$date->format('Y-m-d');
                                                                }
                                                                else
                                                                {
                                                                    $date = new DateTime($cell);
                                                                    if(is_object($date))
                                                                    {
                                                                        $start_date=$date->format('Y-m-d');

                                                                    }
                                                                    else
                                                                    {

                                                                        $insertRow = false;
                                                                        $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                    }

                                                                }

                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="Start Date cannot be empty";
                                                            }
                                                      /*if($cell || $cell != '')
                                                      {

                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $start_date = $date->format('Y-m-d');

                                                                } else {
                                                                       $insertRow = false;
                                                                     $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                      }
                                                      else
                                                      {
                                                        $insertRow = false;
                                                        $impMessage="Start date cannot be empty";

                                                      }*/
                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) == 'END_DATE' || strtoupper($dataColumns[$countCell]) == 'ENDDATE' ||strtoupper($dataColumns[$countCell]) == 'END DATE')//last_date
                                                    {
                                                      $cell=trim(str_replace('/','-',$cell));
                                                            if($cell || $cell != '')
                                                            {
                                                                $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                
                                                                if(is_object($date)){
                                                                    $end_date=$date->format('Y-m-d');
                                                                }
                                                                else
                                                                {
                                                                    $date = new DateTime($cell);
                                                                    if(is_object($date))
                                                                    {
                                                                        $end_date=$date->format('Y-m-d');

                                                                    }
                                                                    else
                                                                    {

                                                                        $insertRow = false;
                                                                        $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                    }

                                                                }

                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="End Date cannot be empty";
                                                            }
                                                      /*if($cell || $cell != '')
                                                      {

                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $end_date = $date->format('Y-m-d');
                                                                } else {
                                                                       $insertRow = false;
                                                                     $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                              }
                                                      else
                                                      {
                                                        $insertRow = false;
                                                        $impMessage="END cannot be empty";

                                                      }*/

                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ECS_ACCT15' || strtoupper($dataColumns[$countCell]) === 'ECS ACCOUNT TYPE' || strtoupper($dataColumns[$countCell]) === 'BANK')//bank_id
                                                    {
                                                            $bank_name = $cell;
                                                            $bank_id=$bankid->bank_id;

                                                    }
                                                    else if(strtoupper($dataColumns[$countCell]) === 'ECS_ACCNO' || strtoupper($dataColumns[$countCell]) === 'ECS ACCOUNT NUMBER' || strtoupper($dataColumns[$countCell]) === 'Bank_AccountNo')//bank_id
                                                    {
                                                            $Bank_AccountNo = $cell;


                                                    }

                                                    else if(strtoupper($dataColumns[$countCell]) === 'IT_PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                                    {

                                                      if($cell || $cell != '')
                                                      {
                                                         $PanNum = trim($cell);
                                                         $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                          $c_info = $this->client->get_client_family_by_pan($whereClient);
                                                         // print_r($c_info);
                                                          if(count($c_info) == 0)
                                                          {

                                                           
                                                              //var_dump($c_info1);
                                                              $nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                           
                                                          }
                                                         else
                                                          {
                                                              $client_id = $c_info->client_id;
                                                              $familyId = $c_info->family_id;

                                                          }
                                                        }
                                                        else
                                                        {
                                                            $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              
                                                               //$nopan='set';
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }
                                                           
                                                           
                                                         }
                                                      }
                                                      else if(strtoupper($dataColumns[$countCell]) === 'SIP_REG_14' || strtoupper($dataColumns[$countCell]) === 'SIP REGISTERED DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                                      {

                                                            $cell=trim(str_replace('/','-',$cell));
                                                        if($cell || $cell != '')
                                                        {
                                                            $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                            //var_dump($cell);exit;
                                                            // $date->format('Y-m-d');
                                                            if(is_object($date)){
                                                                $reg_date=$date->format('Y-m-d');
                                                            }
                                                            else
                                                            {
                                                                $date = new DateTime($cell);
                                                                if(is_object($date))
                                                                {
                                                                    $reg_date=$date->format('Y-m-d');

                                                                }
                                                                else
                                                                {

                                                                    $insertRow = false;
                                                                    $$impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                }

                                                            }

                                                        }
                                                        else
                                                        {
                                                           $insertRow = false;
                                                           $impMessage="Registration date cannot be empty";
                                                        }

                                                            /*if($cell || $cell != '')
                                                            {

                                                                  $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                      if(is_object($date)) {
                                                                            $reg_date = $date->format('Y-m-d');
                                                                      } else {
                                                                             $insertRow = false;
                                                                          $impMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                      }
                                                            }
                                                            else
                                                            {
                                                              $insertRow = false;
                                                              $impMessage="Registration date cannot be empty";

                                                            }*/
                                                        }
                                                        else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                                        {
                                                              if($cell || $cell != '')
                                                              {
                                                                   $installment_amt = $cell;
                                                              }
                                                              else
                                                              {
                                                                  $insertRow = false;
                                                                  $impMessage = "Amount cannot be empty";
                                                              }

                                                        }
                                                        else if(strtoupper($dataColumns[$countCell]) === 'SIP TRANSACTION NUMBER' || strtoupper($dataColumns[$countCell]) === 'SIP_TXN_NO')//installment_amt
                                                        {
                                                              if($cell || $cell != '')
                                                              {
                                                                  $ref_number = $cell;
                                                              }
                                                              else
                                                              {
                                                                  $insertRow = false;
                                                                  $impMessage = "IHNO. Cannot be empty";
                                                              }


                                                          }
                                                            else if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVESTOR_7')
                                                            {
                                                              if($cell || $cell != '')
                                                              {
                                                                  $invname = trim($cell);
                                                              }

                                                        }


                                            $countCell++;
                                            }
                                            else {
                                                if(strtoupper($dataColumns[$countCell]) === 'INVESTOR NAME' || strtoupper($dataColumns[$countCell]) === 'INVESTOR_7')//client_id
                                                    {

                                                          if($cell || $cell != '')
                                                          {
                                                                    $invname = trim($cell);

                                                              }
                                                      }


                                            }
                                  }
                                }


                                  if($countRow != 0)
                                  {
                                      if(!$insertRow)
                                      {
                                          $imp_data[$countErrRow][1] = $folio_id;
                                          $imp_data[$countErrRow][2] = $invname;
                                          $imp_data[$countErrRow][3] = $impMessage;

                                          $countErrRow++;
                                          $insertRow = true;
                                           $uploadedStatus = 2;
                                          continue;
                                      }
                                     //  $temp_mat_date =new Datetime($maturityDate);
                                     //  $temp_issue_date =new DateTime($dateOfIssue);

                                  $type = $this->sp->get_type();
                                  $type=$type->type_id;
                                  settype($type,"int");



                                  $rate_of_return = $sip_rate/400;
                                  $install_amt = $installment_amt;
                                  $date = new DateTime ($start_date);
                                  $start_date = $date->format('Y-m-d');
                                  $date = new DateTime ($end_date);
                                  $end_date = $date->format('Y-m-d');
                                  $mat_value = 0;

                                  $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                                  $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month

                                  $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                                  $exp2 = 1 + $rate_of_return;
                                  $exp3 = -0.33333;
                                  $exp4 = 1 - (pow($exp2, $exp3));
                                  if($exp4 > 0)
                                      $mat_value = round(($install_amt * $exp1) / $exp4);


          $product_id = $this->sp->get_product_id();
          $product_id=$product_id->product_id;
          $cease_date=NULL;
          $frequency='monthly';
          $added_on = date("Y/m/d");


                                     $dataRows['add_SIP_list'] = array (
                                                                       'client_id'=>$client_id,
                                                                       'product_id'=> intval($product_id),
                                                                       'type_id'=>$type,
                                                                        'cease_date'=>$cease_date,
                                                                       //'company_id'=>$bank_id),
                                                                       'scheme_id'=> $scheme_id,
                                                                       'reg_date'=> $reg_date,
                                                                       'folio_no'=> $folio_id,
                                                                       'ref_number'=> $ref_number,
                                                                       'Bank'=>$bank_name,
                                                                       'Bank_AccountNo'=>$Bank_AccountNo,
                                                                       'start_date'=> date($start_date),
                                                                       'end_date'=> date($end_date),
                                                                       'frequency'=>$frequency,
                                                                       'installment_amount'=> $installment_amt,
                                                                       'rate_of_return'=>$sip_rate,
                                                                       'expected_mat_value'=> $mat_value,
                                                                       'broker_id'=> $brokerID,
                                                                       'user_id'=> $user_id,
                                                                       'added_on'=> date($added_on)
                                                                       );
                                                                       //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                             $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                                      if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                                       {

                                                                         $client_id=$isDuplicateSIP->client_id;
                                                                         $assets_id=$isDuplicateSIP->asset_id;

                                                                          $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                           $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));

                                                                            $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                           $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                         $uploadedStatus = 1;
                                                                       }
                                                                       else
                                                                       {

                                                                     $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                                                      $assets_id=$inserted;

                                                                     $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                    $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                    $uploadedStatus = 1;
                                                                       }

                                     /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                     $assets_id=$inserted;
                                     $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                     $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                    $uploadedStatus = 1;*/

                                      if(is_array($inserted))
                                      {
                                          $uploadedStatus = 0;
                                          $message = 'Error while inserting records. '.$assets_id['message'];
                                          break;
                                      }
                                      $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                                      $End_date="";$frequency="";$Client_id="";$account_no="";
                                  }
                                  if($uploadedStatus == 0)
                                      break;

                                  $countRow++;
                              }

                              if($dataRows)
                              {
                                  if(is_array($inserted))
                                  {
                                      $uploadedStatus = 0;
                                      $message = 'Error while inserting records';
                                  } else {
                                    // var_dump($brokerID);
                                    //  var_dump($_FILES["import_Sip"]["name"]);
                                    //   var_dump($user_id);
                                      $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                                      if($uploadedStatus != 2) {
                                          $uploadedStatus = 1;
                                          $message = "SIP Details Uploaded Successfully";
                                      }
                                  }
                              }
                            unset($dataColumns, $dataRows);

                          }
                          /*------------- Frankly DBF SIP IMPort Ends here-----------*/
                          /*------------- Sundaram DBF SIP IMPort Starts here---------*/
                  else if($_REQUEST['rta_list'] =='sundaram_excel')
                  {


                    $file = $_FILES["import_Sip"]["tmp_name"];

                    for($j=0; $j<$field_num; $j++){
                          array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                    }

                    for($i=0; $i<$num_rec; $i++){
                      $temper=array();
                        if ($row = $dbf->getRow($i)) {

                          for($j=0; $j<$field_num; $j++){
                              array_push($temper,$row[$j]);
                          }

                        }
                        array_push($excelData,$temper);//array of all rows
                        //print_r($excelData);
                    }
                    //load the excel library
                    //$this->load->library('Excel');
                    //read file from path
                    //$objPHPExcel = PHPExcel_IOFactory::load($file);
                    //get only the Cell Collection
                    //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                    //$maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                    //temp variables to hold values
                   $scheme_name="";
                   $scheme_id="";
                   $folio_id="";
                   $installment_amt="";
                   $added_on="";
                   $Start_date="";
                   $End_date="";
                   $frequency="";
                   $Client_id="";
                   $account_no="";

                    $brokerID = $this->session->userdata('broker_id');
                    $user_id = $this->session->userdata('user_id');
                    //get data from excel using range
                    //$excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                  //print_r($excelData);

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
                                  if(strtoupper($cell)=='PRODUCT' ||strtoupper($cell)=='PRODNAME'||strtoupper($cell)=='FOLIO'||
                                  strtoupper($cell)=='INVNAME'|| strtoupper($cell)=='TRXNTYPE'||
                                  strtoupper($cell)=='TRXNNUMBER'||strtoupper($cell)=='AMOUNT'||
                                  strtoupper($cell)=='FROMDATE'|| strtoupper($cell)=='TODATE'||strtoupper($cell)=='NOOFSI'||
                                  strtoupper($cell)=='CEASEDATE'||strtoupper($cell)=='PERIODCITY'||strtoupper($cell)=='PERIOD'||
                                  strtoupper($cell)=='MIN' || strtoupper($cell)=='PAYMODE' || strtoupper($cell)=='TOSCHEME'||
                                  strtoupper($cell)=='REGDATE'||strtoupper($cell)=='SUBBROK'|| strtoupper($cell)=='BROKCODE' ||
                                  strtoupper($cell)=='ECSBANK' ||
                                  strtoupper($cell)=='ECSACNO' ||
                                  strtoupper($cell)=='SERVPROV' || strtoupper($cell)=='REMARKS' || strtoupper ($cell)=='LOCATION' || strtoupper($cell)=='ADDRESS1' ||
                                  strtoupper($cell)=='ADDRESS2' || strtoupper($cell)=='ADDRESS3' || strtoupper($cell)=='ADDRESS4'
                                  || strtoupper($cell)=='ZIPCODE' || strtoupper($cell)=='COUNTRY' || strtoupper($cell)=='PHONE1' || strtoupper($cell)=='PHONE2'
                                  || strtoupper($cell)=='MOBILE' || strtoupper($cell)=='EMAIL'
                                  )
                                        {
                                        //$message='match';
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                        continue;

                                  }
                                  else
                                  {
                                      $message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;
                                      //die();
                                  }

                              }
                              else
                              {


                                    if($insertRow)
                                    {
                                      //  print_r($dataColumns);


                                            if(strtoupper($dataColumns[$countCell]) ==='PRODUCT' || strtoupper($dataColumns[$countCell]) === 'PRODUCT_CODE')//product_id
                                            {



                                                  if($cell || $cell!='')
                                                  {

                                                     $product_id = $cell;

                                                      if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                        $insertRow = false;

                                                       $impMessage = "Scheme id Is Not Matching";
                                                      }
                                                      else{
                                                                 $scheme_type_id=$scheme_id->scheme_type_id;
                                                          $sc_type= $scheme_id->scheme_type;
                                                        if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                         {
                                                             $sc='equity';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);

                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }


                                                         }
                                                         else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                         {
                                                             $sc='debt';
                                                             $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                              $sip_rate=$this->sp->get_sip_rate($condition);
                                                              if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                         {
                                                               $sc='hybrid';
                                                               $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                               $sip_rate=$this->sp->get_sip_rate($condition);
                                                               if(isset($sip_rate) && !empty($sip_rate)) {
                                                                    $sip_rate=$sip_rate->rate;
                                                                } else {
                                                                    $sip_rate=10;
                                                                }
                                                         }
                                                         else if($sc_type='')
                                                         {
                                                             $sip_rate='10';
                                                         }



                                                          $scheme_id=$scheme_id->scheme_id;
                                                          settype($scheme_id,"integer");


                                                      }
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Scheme cannot be empty";
                                                  }


                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FOLIO' || strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')//folio_id
                                            {



                                              if($cell || $cell != '')
                                              {
                                                   //  $dateOfTransaction= trim($cell);
                                                        $folio_id = $cell;
                                                      //$dateOfTransaction = $date->format('Y-m-d');
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Folio Id cannot be empty";
                                                // $dateOfTransaction=null;
                                                // $dateOfTransaction = 0;
                                              }

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'FROMDATE' || strtoupper($dataColumns[$countCell]) === 'START_DATE')//start_data
                                            {

                                              if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $start_date=$date->format('Y-m-d');
                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $start_date=$date->format('Y-m-d');

                                                                        }
                                                                        else
                                                                        {
                                                                           $insertRow = false;
                                                                            $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                     $insertRow = false;
                                                      $impMessage="FROM Date cannot be empty";
                                                    }

                                              /*$cell=trim(str_replace('/','-',$cell));
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $start_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $start_date=$date->format('Y-m-d');

                                                            }
                                                            else
                                                            {

                                                                $insertRow = false;
                                                                $impMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                            }

                                                        }

                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Start Date cannot be empty";
                                                    }*/
                                              /*if($cell || $cell != '')
                                              {


                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                            $start_date = $date->format('Y-m-d');

                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                              }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="Start date cannot be empty";


                                              }*/
                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'TODATE' ||strtoupper($dataColumns[$countCell]) === 'END_DATE')//last_date
                                            {

                                              if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $end_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $end_date=$date->format('Y-m-d');

                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }
                                              /*$cell=trim(str_replace('/','-',$cell));
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                        //var_dump($cell);exit;
                                                        // $date->format('Y-m-d');
                                                        if(is_object($date)){
                                                            $end_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $end_date=$date->format('Y-m-d');

                                                            }
                                                            else
                                                            {

                                                                $insertRow = false;
                                                                $impMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                            }

                                                        }

                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="End Date cannot be empty";
                                                    }*/
                                              /*if($cell || $cell != '')
                                              {
                                                    // $date = new DateTime($cell);
                                                    // $End_date = $edate->format('Y-m-d');
                                                    $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                        if(is_object($date)) {
                                                             $end_date = $date->format('Y-m-d');
                                                        } else {
                                                               $insertRow = false;
                                                             $impMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                        }
                                                      }
                                              else
                                              {
                                                $insertRow = false;
                                                $impMessage="END cannot be empty";


                                              }*/

                                            }
                                            else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                            {
                                                  if($cell || $cell != '')
                                                  {
                                                        $installment_amt = $cell;
                                                  }
                                                  else
                                                  {
                                                      $insertRow = false;
                                                      $impMessage = "Amount cannot be empty";
                                                  }
                                            }

                                            else if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);

                                                           $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);

                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {

                                                              $whereClient = array('c.name'=>$invname,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                              $c_info = $this->client->get_client_family_by_inv_name($whereClient);

                                                                        if(count($c_info) == 0)
                                                                        {
                                                                          $insertRow = false;
                                                                          $impMessage = " Client  ".$invname." doesn't exist";
                                                                        }
                                                                        else
                                                                        {
                                                                            $client_id = $c_info->client_id;
                                                                            $familyId = $c_info->family_id;
                                                                        }
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }



                                                      }
                                                    else
                                                    {

                                                         $wherePan = array(
                                                              'cb.productId'=>$product_id,
                                                              'cb.folio_number'=>$folio_id,
                                                              'f.broker_id'=>$brokerID
                                                            );
                                                            //var_dump($wherePan);
                                                            if(!$c_info1 = $this->client->get_client_family_by_withoutpan($wherePan))
                                                            {
                                                              $insertRow = false;
                                                              $impMessage = "Client does not exist";
                                                            }
                                                            else
                                                            {
                                                              $client_id = $c_info1->client_id;
                                                              $familyId = $c_info1->family_id;
                                                            }


                                                      //$insertRow = false;
                                                      //$impMessage="Investor Name cannot be empty";
                                                      // $dateOfTransaction=null;
                                                      // $dateOfTransaction = 0;
                                                    }
                                              }



                                              else if(strtoupper($dataColumns[$countCell]) === 'TRXNNUMBER' || strtoupper($dataColumns[$countCell]) === 'SIP_TXN_NO')//installment_amt
                                              {
                                                    if($cell || $cell != '')
                                                    {
                                                        $ref_number = $cell;
                                                    }
                                                    else
                                                    {
                                                        $insertRow = false;
                                                        $impMessage = "TRXNNUMBER";
                                                    }


                                                }

                                                else if(strtoupper($dataColumns[$countCell]) === 'REGDATE')
                                                {
                                                    if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $reg_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $reg_date=$date->format('Y-m-d');

                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $insertRow = false;
                                                      $impMessage="Registration date cannot be empty";
                                                    }
                                                         /*$cell=trim(str_replace('/','-',$cell));
                                                if($cell || $cell != '')
                                                {
                                                    $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                    //var_dump($cell);exit;
                                                    // $date->format('Y-m-d');
                                                    if(is_object($date)){
                                                        $reg_date=$date->format('Y-m-d');
                                                    }
                                                    else
                                                    {
                                                        $date = new DateTime($cell);
                                                        if(is_object($date))
                                                        {
                                                            $reg_date=$date->format('Y-m-d');

                                                        }
                                                        else
                                                        {

                                                            $insertRow = false;
                                                            $impMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                        }

                                                    }

                                                }
                                                else
                                                {
                                                   $insertRow = false;
                                                   $impMessage="Registration date cannot be empty";
                                                }*/
                                                        /*if($cell || $cell != '')
                                                        {
                                                               $date = DateTime::createFromFormat('m-d-y', $cell);
                                                       if(is_object($date)) {
                                                              $reg_date = $date->format('Y-m-d');
                                                           } else {
                                                        //check if date is in string format d/m/Y
                                                        $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                            if(is_object($date)) {
                                                                 $reg_date = $date->format('Y-m-d');
                                                            } else {
                                                                   $insertRow = false;
                                                                 $impMessage = "Registration Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                            }
                                                            }
                                                          }
                                                          else
                                                          {
                                                            $reg_date=null;
                                                            $reg_date=0;
                                                            $insertRow = false;
                                                            $impMessage = "Registration Date cannot be empty";
                                                          }*/
                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'CEASEDATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                              {


                                                      if($cell || $cell != '')
                                                    {
                                                          // $date = new DateTime($cell);
                                                          // $Start_date = $date->format('Y-m-d');
                                                          $date = DateTime::createFromFormat('m/d/Y', $cell);
                                                              if(is_object($date)) {
                                                                      $cease_date=$date->format('Y-m-d');

                                                              } else {
                                                                     $date = new DateTime($cell);
                                                                        if(is_object($date))
                                                                        {
                                                                              $cease_date=$date->format('Y-m-d');

                                                                        }
                                                                        else
                                                                        {
                                                                            $insertRow = false;
                                                                            $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                        }
                                                              }
                                                    }
                                                    else
                                                    {
                                                      $cease_date=null;
                                                    }
                                                       /*$cell=trim(str_replace('/','-',$cell));
                                                    if($cell || $cell != '')
                                                    {
                                                        $date =DateTime::createFromFormat('m-d-Y',$cell);

                                                        if(is_object($date)){
                                                            $cease_date=$date->format('Y-m-d');
                                                        }
                                                        else
                                                        {
                                                            $date = new DateTime($cell);
                                                            if(is_object($date))
                                                            {
                                                                $cease_date=$date->format('Y-m-d');

                                                            }
                                                            else
                                                            {

                                                                $insertRow = false;
                                                                $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                            }

                                                        }

                                                    }
                                                    else
                                                    {
                                                       $cease_date=null;
                                                    }*/
                                                      /*if($cell || $cell != '')
                                                      {
                                                            // $date = new DateTime($cell);
                                                            // $Start_date = $date->format('Y-m-d');
                                                            $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                if(is_object($date)) {
                                                                     $cease_date = $date->format('Y-m-d');
                                                                }else {
                                                                       $insertRow = false;
                                                                     $impMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                }
                                                      }
                                                      else
                                                      {
                                                        $cease_date='';
                                                      }*/

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSBANK' || strtoupper($dataColumns[$countCell]) === 'BANK')//bank_id
                                              {
                                                  $bank_name = $cell;
                                                  $bank_id='';

                                              }
                                              else if(strtoupper($dataColumns[$countCell]) === 'ECSACNO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//installment_amt
                                              {


                                                         $Bank_AccountNO = $cell;

                                               }
                                             else if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//scheme_id
                                                    {
                                                      if($cell || $cell != '')
                                                      {
                                                          $invname = trim($cell);
                                                      }

                                                }







                                    $countCell++;
                                    } else {
                                        if(strtoupper($dataColumns[$countCell]) === 'INVNAME')//client_id
                                            {

                                                  if($cell || $cell != '')
                                                  {
                                                           $invname = trim($cell);

                                                      }
                                              }


                                    }
                          }
                        }


                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  $imp_data[$countErrRow][1] = $folio_id;
                                  $imp_data[$countErrRow][2] = $invname;
                                  $imp_data[$countErrRow][3] = $impMessage;

                                  $countErrRow++;
                                  $insertRow = true;
                                   $uploadedStatus = 2;
                                  continue;
                              }
                             //  $temp_mat_date =new Datetime($maturityDate);
                             //  $temp_issue_date =new DateTime($dateOfIssue);

                          $type = $this->sp->get_type();
                          $type=$type->type_id;
                          settype($type,"int");
                         $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                           if(!empty(trim($cease_date)))
                           {
                             $end_date=$cease_date;//replace end date for maturity calculation
                           }
                           else
                           {

                               $cease_date=NULL;
                           }


                          $rate_of_return = $sip_rate/400;
                          $install_amt = $installment_amt;
                          $date = new DateTime ($start_date);
                          $start_date = $date->format('Y-m-d');
                          $date = new DateTime ($end_date);
                          $end_date = $date->format('Y-m-d');
                          $mat_value = 0;

                          $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                          $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                          // var_dump($num_of_days);
                          $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                          $exp2 = 1 + $rate_of_return;
                          $exp3 = -0.33333;
                          $exp4 = 1 - (pow($exp2, $exp3));
                          if($exp4 > 0)
                              $mat_value = round(($install_amt * $exp1) / $exp4);
                          //var_dump(round($mat_value));



  $product_id = $this->sp->get_product_id();
  $product_id=$product_id->product_id;
  $frequency='monthly';//by default
  $added_on = date("Y/m/d");


                             $dataRows['add_SIP_list'] = array (
                                                               'client_id'=>$client_id,
                                                               'product_id'=> intval($product_id),
                                                               'type_id'=>$type,
                                                               //'company_id'=>intval($bank_id),
                                                               'scheme_id'=> $scheme_id,
                                                               'Bank'=>$bank_name,
                                                               'Bank_AccountNO'=>$Bank_AccountNO,
                                                               'reg_date'=> $reg_date,
                                                               'folio_no'=> $folio_id,
                                                               'ref_number'=> $ref_number,
                                                               'start_date'=> date($start_date),
                                                               'end_date'=> date($end_date_for_insert),
                                                               'frequency'=>$frequency,
                                                               'cease_date'=>$cease_date,
                                                               'installment_amount'=> $installment_amt,
                                                               'rate_of_return'=>$sip_rate,
                                                               'expected_mat_value'=> $mat_value,
                                                               'broker_id'=> $brokerID,
                                                               'user_id'=> $user_id,
                                                               'added_on'=> date($added_on)
                                                               );


                             /*$inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                             $assets_id=$inserted;
                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                             $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                            $uploadedStatus = 1;*/
                            //$assets_record=array('ref_number'=>$ref_number,'folio_no'=>$folio_id,'client_id'=>$client_id);
                              $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);
                                                              if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                               {

                                                                 $client_id=$isDuplicateSIP->client_id;
                                                                 $assets_id=$isDuplicateSIP->asset_id;

                                                                  $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                   $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));

                                                                    $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);

                                                                   $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                 $uploadedStatus = 1;
                                                               }
                                                               else
                                                               {

                                                             $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                                               $assets_id=$inserted;

                                                             $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $installment_amt,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                            $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                            $uploadedStatus = 1;
                                                               }
                              if(is_array($inserted))
                              {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$assets_id['message'];
                                  break;
                              }
                              $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                              $End_date="";$frequency="";$Client_id="";$account_no="";
                          }
                          if($uploadedStatus == 0)
                              break;

                          $countRow++;
                      }

                      if($dataRows)
                      {
                          if(is_array($inserted))
                          {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {

                              $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = "SIP Details Uploaded Successfully";
                              }
                          }
                      }
                    unset($dataColumns, $dataRows);

                  }
                  /*------------- Sundaram DBF SIP IMPort Ends here---------*/



               }//file selction ends here(dbf) 
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

        }//form Submit if end
        else
        {
             //echo "Not Form Submit";
        }
         $this->import($imp_data);
  }

  private function add_sipinterest_details($data, $transID)
  {

 $Start_date = $data['start_date'];
 $End_date = $data['end_date'];
 $installment_amt = $data['installment_amt'];
  $brokerID = $data['broker_id'];
 $assets_id = $data['asset_id'];
$frequency=$data['frequency']; //by default monthly


      // $issueDate = $data['issued_date'];
      // $mat_date = $data['maturity_date'];
      // $int_mode = $data['interest_mode'];
      // $amt_inv = $data['amount_invested'];
      // $int_rate = $data['interest_rate'];
      // $int_round_off = $data['int_round_off'];
      $total_days = 365;
      //$tempIssueDate = strtotime($issueDate);
      $tempIssueDate = $Start_date;
      $issueDate=$Start_date;
      // $month = date('n', $tempIssueDate);
      // $year = date('Y', $tempIssueDate);
      $Start_date = new DateTime ($Start_date);
      $year = $Start_date->format("Y");
      $month = $Start_date->format("m");
      $tempIssueDate0 = $Start_date->format("m");
      $tempIssueDat = $Start_date->format("Y-m-d");


      if($frequency == "Annually")
      {
          if($month <= 3)
              $int_date = $year.'-03-31';
          else
              $int_date = ($year+1).'-03-31';
          if($Start_date != $int_date) {
              //$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
              $tempindate = new DateTime($int_date);
              $tempisdate = new DateTime($tempIssueDat);
              $diff=$tempindate->diff($tempisdate);

              $num_of_days = $diff->days;
              // $num_of_days = ($int_date - $tempIssueDat); ///this give number of days with time
              // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
              //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
              $data_int = array(
                  'asset_id' => $assets_id,
                  'maturity_date' => $int_date,
                  'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }
          }
          $issueDate = $int_date;
          //$int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
          //var_dump($issueDate);
          //var_dump($mat_date);
          while($issueDate <= $End_date)
          {
            $issueDate = new DateTime($issueDate);
            $issueDate->modify('+1 year');
            $issueDate = $issueDate->format('Y-m-d');
              //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
              //var_dump($issueDate);
              if($issueDate >= $End_date)
              {
                  
                  
                   $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
                );
                $status = $this->sp->add_sip_interest($data_int);
                  break;
              }
              $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $issueDate,
                'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }

          }
      }
      else if($frequency == "Half-yearly")
      {
          if($month <= 6)
              $int_date = $year.'-06-30';
          else
              $int_date = ($year).'-12-31';

          if($issueDate != $int_date) {
              // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
              // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day

              $tempindate = new DateTime($int_date);
              $tempisdate = new DateTime($tempIssueDat);
              $diff=$tempindate->diff($tempisdate);

              //$num_of_days = $diff->days;
              //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
              $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $issueDate,
                'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }
          }
          $issueDate = $int_date;

          //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
          while($issueDate <= $End_date)
          {
              // $issueDate0 = new DateTime('@'.strtotime($issueDate));
              // $issueDate0->modify('last day of +6 month');
              // $issueDate = $issueDate0->format('Y-m-d');
              $issueDate = new DateTime($issueDate);
              $issueDate->modify('last day of +6 month');
              $issueDate = $issueDate->format('Y-m-d');
              //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
              if($issueDate >= $End_date)
              {
                  
                  
                   $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
                );
                $status = $this->sp->add_sip_interest($data_int);
                  break;
              }
              $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $issueDate,
                'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }
          }

      }

      else if($frequency == "Quarterly")
      {
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
              // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
              // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
              $tempindate = new DateTime($int_date);
              $tempisdate = new DateTime($tempIssueDat);
              $diff=$tempindate->diff($tempisdate);

            //  $num_of_days = $diff->days;

            //  $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
              $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $issueDate,
                'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }
          }
          $issueDate = $int_date;

          //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
          while($issueDate <= $End_date)
          {
              // $issueDate0 = new DateTime('@'.strtotime($issueDate));
              // $issueDate0->modify('last day of +3 month');
              // $issueDate = $issueDate0->format('Y-m-d');
              $issueDate = new DateTime($issueDate);
              $issueDate->modify('last day of +3 month');
              $issueDate = $issueDate->format('Y-m-d');
              //$issueDate = date('Y-m-d', $issueDate0);
              //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
               if($issueDate >= $End_date)
              {
                  
                  
                   $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
                );
                $status = $this->sp->add_sip_interest($data_int);
                  break;
              }
              $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $issueDate,
                'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
              //if there is any error
              if(isset($status['code']))
              {
                  throw new Custom_exception();
              }
          }

      }

      else if($frequency == "Monthly" || $frequency == "monthly" ) {

         $int_date_temp = new DateTime($year.'-'.$month.'-1');
         $int_date_temp->modify('last day of this month');
         $int_date = $int_date_temp->format('Y-m-d');

         if($issueDate != $int_date) {
             // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
             // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
             $tempindate = new DateTime($int_date);
             $tempisdate = new DateTime($tempIssueDat);
             $diff=$tempindate->diff($tempisdate);

             //$num_of_days = $diff->days;

             //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
             $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
             );
             $status = $this->sp->add_sip_interest($data_int);
             //if there is any error
             if(isset($status['code']))
             {
                 throw new Custom_exception();
             }
         }
         $issueDate = $int_date;

         //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
         $cnt = 0;
         while($issueDate <= $End_date)
         {
             // $issueDate0 = new DateTime('@'.strtotime($issueDate));
             // $issueDate0->modify('last day of next month');
             // $issueDate = $issueDate0->format('Y-m-d');

             $issueDate0 = new DateTime($issueDate);
             $issueDate0->modify('last day of next month');
             $issueDate = $issueDate0->format('Y-m-d');

             
            
           
             if($issueDate >= $End_date)
              {
                  
                  
                 //echo $issueDate;
                  //echo $assets_id;
                   $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
                );
                $status = $this->sp->add_sip_interest($data_int);
                  break;
              }
             $data_int = array(
               'asset_id' => $assets_id,
               'maturity_date' => $issueDate,
               'maturity_amount' => $installment_amt
             );
             
             
             $status = $this->sp->add_sip_interest($data_int);
             //if there is any error
             if(isset($status['code']))
             {
                 throw new Custom_exception();
             }
         }
     }

      // try
      // {
      //     if($frequency == "Annually")
      //     {
      //
      //         if(date('L', $tempIssueDate0))
      //             $total_days = 366;
      //
      //         if($frequency == "Annually")
      //         {
      //             if($month <= 3)
      //                 $int_date = $year.'-03-31';
      //             else
      //                 $int_date = ($year+1).'-03-31';
      //             if($issueDate != $int_date) {
      //                 //$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //                 $tempindate = new DateTime($int_date);
      //                 $tempisdate = new DateTime($tempIssueDat);
      //                 $diff=$tempindate->diff($tempisdate);
      //
      //                 $num_of_days = $diff->days;
      //                 // $num_of_days = ($int_date - $tempIssueDat); ///this give number of days with time
      //                 // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //                 $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $int_date,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->sp->add_sip_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //             $issueDate = $int_date;
      //             $int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
      //             //var_dump($issueDate);
      //             //var_dump($mat_date);
      //             while($issueDate <= $mat_date)
      //             {
      //               $issueDate = new DateTime($issueDate);
      //               $issueDate->modify('+1 year');
      //               $issueDate = $issueDate->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
      //                 //var_dump($issueDate);
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //
      //         } elseif($int_mode == "Half-yearly") {
      //             if($month <= 6)
      //                 $int_date = $year.'-06-30';
      //             else
      //                 $int_date = ($year).'-12-31';
      //
      //             if($issueDate != $int_date) {
      //                 // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //                 // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //
      //                 $tempindate = new DateTime($int_date);
      //                 $tempisdate = new DateTime($tempIssueDat);
      //                 $diff=$tempindate->diff($tempisdate);
      //
      //                 $num_of_days = $diff->days;
      //                 $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $int_date,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //             $issueDate = $int_date;
      //
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
      //             while($issueDate <= $mat_date)
      //             {
      //                 // $issueDate0 = new DateTime('@'.strtotime($issueDate));
      //                 // $issueDate0->modify('last day of +6 month');
      //                 // $issueDate = $issueDate0->format('Y-m-d');
      //                 $issueDate = new DateTime($issueDate);
      //                 $issueDate->modify('last day of +6 month');
      //                 $issueDate = $issueDate->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //         } elseif($int_mode == "Quarterly") {
      //             if($month <= 3)
      //             {
      //                 $int_date = $year.'-03-31';
      //             }
      //             else if ($month <= 6)
      //             {
      //                 $int_date = $year.'-06-30';
      //             }
      //             else if($month <= 9)
      //             {
      //                 $int_date = $year.'-09-30';
      //             }
      //             else
      //             {
      //                 $int_date = $year.'-12-31';
      //             }
      //
      //             if($issueDate != $int_date) {
      //                 // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //                 // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //                 $tempindate = new DateTime($int_date);
      //                 $tempisdate = new DateTime($tempIssueDat);
      //                 $diff=$tempindate->diff($tempisdate);
      //
      //                 $num_of_days = $diff->days;
      //
      //                 $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $int_date,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //             $issueDate = $int_date;
      //
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
      //             while($issueDate <= $mat_date)
      //             {
      //                 // $issueDate0 = new DateTime('@'.strtotime($issueDate));
      //                 // $issueDate0->modify('last day of +3 month');
      //                 // $issueDate = $issueDate0->format('Y-m-d');
      //                 $issueDate = new DateTime($issueDate);
      //                 $issueDate->modify('last day of +3 month');
      //                 $issueDate = $issueDate->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', $issueDate0);
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' =>  $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //         } elseif($int_mode == "Monthly") {
      //             $int_date_temp = new DateTime($year.'-'.$month.'-1');
      //             $int_date_temp->modify('last day of this month');
      //             $int_date = $int_date_temp->format('Y-m-d');
      //
      //             if($issueDate != $int_date) {
      //                 // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //                 // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //                 $tempindate = new DateTime($int_date);
      //                 $tempisdate = new DateTime($tempIssueDat);
      //                 $diff=$tempindate->diff($tempisdate);
      //
      //                 $num_of_days = $diff->days;
      //
      //                 $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $int_date,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //             $issueDate = $int_date;
      //
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
      //             $cnt = 0;
      //             while($issueDate <= $mat_date)
      //             {
      //                 // $issueDate0 = new DateTime('@'.strtotime($issueDate));
      //                 // $issueDate0->modify('last day of next month');
      //                 // $issueDate = $issueDate0->format('Y-m-d');
      //
      //                 $issueDate0 = new DateTime($issueDate);
      //                 $issueDate0->modify('last day of next month');
      //                 $issueDate = $issueDate0->format('Y-m-d');
      //
      //                 //$issueDate = date('Y-m-d', $issueDate0);
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 month'));
      //                 //echo $issueDate.'<br/>';
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //         }
      //
      //         /*$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //         $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //         $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //         $data_int = array(
      //             'fd_transaction_id' => $transID,
      //             'interest_date' => $int_date,
      //             'interest_amount' => $int_amt
      //         );
      //         $status = $this->fd->add_fd_interest($data_int);
      //         //if there is any error
      //         if(isset($status['code']))
      //         {
      //             throw new Custom_exception();
      //         }
      //         $issueDate = $int_date;*/
      //
      //     } else {
      //         $issueDateFmt = DateTime::createFromFormat('Y-m-d', $issueDate);
      //         $issueDate = $issueDateFmt->format('Y-m-d');
      //         $ogiIssueDate = $issueDateFmt;
      //         //$ogiIssueDate->modify('+1 day');
      //         $ogiDay = $ogiIssueDate->format('j');
      //         $ogiMonth = $ogiIssueDate->format('n');
      //
      //         if($int_mode == "Annually")
      //         {
      //             $int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
      //             while($issueDate <= $mat_date)
      //             {
      //               $issueDate0 = new DateTime($issueDate);
      //               $issueDate0->modify(' +1 year');
      //               $issueDate = $issueDate0->format('Y-m-d');
      //
      //                 // $issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //
      //         } elseif($int_mode == "Half-yearly") {
      //             $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
      //             while($issueDate <= $mat_date)
      //             {
      //                 $day = $issueDateFmt->format('j');
      //                 $month = $issueDateFmt->format('n');
      //                 if($ogiDay == 31) {
      //                     $issueDateFmt->modify('last day of +6 month');
      //                 } else {
      //                     $issueDateFmt->modify('+6 month');
      //                     if($resetDay && ($day != $ogiDay)) {
      //                         $day = $ogiDay;
      //                         $tempDate = $issueDateFmt->format('Y-m');
      //                         $issueDate = $tempDate.'-'.$ogiDay;
      //                         $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
      //                         $resetDay = false;
      //                     }
      //                     if($month == 8 && $day > 28 && $ogiDay > 28) {
      //                         $resetDay = true;
      //                         $issueDateFmt->modify('last day of previous month');
      //                     }
      //                 }
      //                 $issueDate = $issueDateFmt->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //         } elseif($int_mode == "Quarterly") {
      //             $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
      //             while($issueDate <= $mat_date)
      //             {
      //                 $day = $issueDateFmt->format('j');
      //                 $month = $issueDateFmt->format('n');
      //                 if($ogiDay == 31) {
      //                     $issueDateFmt->modify('last day of +3 month');
      //                 } else {
      //                     $issueDateFmt->modify('+3 month');
      //                     if($resetDay && ($day != $ogiDay)) {
      //                         $day = $ogiDay;
      //                         $tempDate = $issueDateFmt->format('Y-m');
      //                         $issueDate = $tempDate.'-'.$ogiDay;
      //                         $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
      //                         $resetDay = false;
      //                     }
      //                     if($month == 11 && $day > 28 && $ogiDay > 28) {
      //                         $resetDay = true;
      //                         $issueDateFmt->modify('last day of previous month');
      //                     }
      //                 }
      //                 $issueDate = $issueDateFmt->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', $issueDate0);
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' =>  $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //
      //         } elseif($int_mode == "Monthly") {
      //             $resetDay = false; //to reset the day back to ogi day i.e 30 or 31
      //             $int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
      //             while($issueDate <= $mat_date)
      //             {
      //                 $day = $issueDateFmt->format('j');
      //                 $month = $issueDateFmt->format('n');
      //                 if($ogiDay == 31) {
      //                     $issueDateFmt->modify('last day of next month');
      //                 } else {
      //                     $issueDateFmt->modify('+1 month');
      //                     if($resetDay && ($day != $ogiDay)) {
      //                         $day = $ogiDay;
      //                         $tempDate = $issueDateFmt->format('Y-m');
      //                         $issueDate = $tempDate.'-'.$ogiDay;
      //                         $issueDateFmt = DateTime::createFromFormat('Y-m-d',$issueDate);
      //                         $resetDay = false;
      //                     }
      //                     if($month == 1 && $day > 28 && $ogiDay > 28) {
      //                         $resetDay = true;
      //                         $issueDateFmt->modify('last day of previous month');
      //                     }
      //                 }
      //                 //if($resetDay) { $issueDateFmt->modify('last day of previous month'); }
      //                 $issueDate = $issueDateFmt->format('Y-m-d');
      //                 //$issueDate = date('Y-m-d', $issueDate0);
      //                 //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 month'));
      //                 //echo $issueDate.'<br/>';
      //                 if($issueDate >= $mat_date)
      //                     break;
      //                 $data_int = array(
      //                     'fd_transaction_id' => $transID,
      //                     'interest_date' => $issueDate,
      //                     'interest_amount' => $int_amt
      //                 );
      //                 $status = $this->fd->add_fd_interest($data_int);
      //                 //if there is any error
      //                 if(isset($status['code']))
      //                 {
      //                     throw new Custom_exception();
      //                 }
      //             }
      //         }
      //         /*$int_date_temp = new DateTime($year.'-'.$month.'-1');
      //         $int_date_temp->modify('last day of this month');
      //         $int_date = $int_date_temp->format('Y-m-d');
      //         $num_of_days = abs(strtotime($int_date) - $tempIssueDate); ///this give number of days with time
      //         $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
      //         $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
      //         $data_int = array(
      //             'fd_transaction_id' => $transID,
      //             'interest_date' => $int_date,
      //             'interest_amount' => $int_amt
      //         );
      //         $status = $this->fd->add_fd_interest($data_int);
      //         //if there is any error
      //         if(isset($status['code']))
      //         {
      //             throw new Custom_exception();
      //         }
      //         $issueDate = $int_date;*/
      //     }
      // }
      // catch(Custom_exception $e)
      // {
      //     //display custom message
      //     $message = array("status" => 0, 'title' => 'Error while adding', 'text' => $e->errorMessage($status['code']));
      // }
  }
  function sip_report()
  {
    //data to pass to header view like page title, css, js
    $header['title']='SIP Report';
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
    $this->load->view('broker/sip/sip_report', $data);
    $this->load->view('broker/common/footer');
  }

  function get_sip_report()
  {
              $family_id = $this->input->post('famName');
              $client_id = $this->input->post('client_id');
              $brokerID = $this->session->userdata('broker_id');
              $type = 'client';
              $where = "";
              if($client_id != null && $client_id != '')
              {
                  $where = array(
                      'brokerID'=> $brokerID,
                      'clientID'=> $client_id,
                      'familyID'=> ''
                      
                  );
              }
              else
              {
            
                  $type = 'family';
                  $where = array(
                      'brokerID'=> $brokerID,
                      'clientID'=> '',
                      'familyID'=> $family_id
                      
                  );
                  //print_r($where);
              }
              $logo = "";
              $status = false;

              //$sip_rep=array('name'=>'Dipak');
              $sip_rep=$this->sp->get_sip_report($type,$where);
             // print_r($sip_rep);

        if(!empty($sip_rep) && !isset($sip_rep['code']))
          {
              unset($_SESSION['sip_report']);
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
              $sip_rep_array = array('report_info' => $rep_info,'sip_data'=>$sip_rep);
              $this->session->set_userdata('sip_report', $sip_rep_array);
              $status = true;
          }
          echo json_encode(array('Status'=> $status));
    }


}
