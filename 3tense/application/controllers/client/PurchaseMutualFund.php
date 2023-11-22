<?php
error_reporting(0);
class PurchaseMutualFund extends Ci_Controller{
   var $soapUrl = "https://www.bsestarmf.in/MFOrderEntry/MFOrder.svc/Secure"; // asmx URL of WSDL
    var $soapAdditionServiceUrl= "https://www.bsestarmf.in/MFUploadService/MFUploadService.svc"; // asmx URL of WSDL
    
    //var $Paymenturl = 'https://bsestarmfdemo.bseindia.com/StarMFSinglePaymentAPI/Single/Payment';
    var $Paymenturl = 'https://bsestarmf.in/StarMFSinglePaymentAPI/Single/Payment';
    
    var $passkey='12345';
    var $soapUrlUAT = "https://bsestarmfdemo.bseindia.com/MFOrderEntry/MFOrder.svc/Secure"; // asmx URL of WSDL//
    //var $soapAdditionServiceUrl= "https://bsestarmfdemo.bseindia.com/MFUploadService/MFUploadService.svc"; // asmx URL of WSDL
    function __construct()
    {
        //ajinkya 5-12-2016
        parent :: __construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->model('Mutual_fund_purchase_model','mfp');
        $this->load->database();
        $this->Passkey='123456';
        
    }
    
    public function index() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        $client_Id = $this->session->userdata['client_id'];
        $broker_Id =$this->session->userdata('user_id');
        
        $FamilyData = $this->mfp->find_client_familyId($client_Id,$broker_Id);
        
        
        $family_id='';
        if($FamilyData)
        {
            if($FamilyData[0]->head_of_family=='1')
            {
                $family_id= $FamilyData[0]->family_id;
            }
        }
    
        $data['account_list'] = $this->mfp->bsc_account_list($client_Id,$family_id,$broker_Id);
        
        
        $condition = array('bs.PurchaseAllowed' => 'Y');
        $data['bsc_schmeme_Type_list'] = $this->mfp->bsc_scheme_type_list($condition);
        
        $this->load->view('client/common/header');
        $this->load->view('client/MFPurchase',$data);
        $this->load->view('client/common/footer');
    }
    
    public function get_bsc_scheme_list()
    {
         $search='';
        if(!isset($_POST['term'])){ 
          $search = '';
        }else{ 
          $search = $_POST['term'];   
        } 
        if(!isset($_POST['amc'])){ 
          $amc = '';
        }else{ 
          $amc = $_POST['amc'];   
        } 
        if(!isset($_POST['schemetype'])){ 
          $schemetype = '';
        }else{ 
          $schemetype = $_POST['schemetype'];   
        } 
        
        $data= $this->mfp->bsc_scheme_list($search,$amc,$schemetype);

        echo json_encode($data);
    }
    
    
    
    public function get_bsc_amc_list()
    {
         $schemetype='';
        if(!isset($_POST['schemetype'])){ 
          $schemetype = '';
        }else{ 
          $schemetype = $_POST['schemetype'];   
        } 
        
        $data= $this->mfp->bsc_scheme_amc_list($schemetype);
        $html = '<option value="">Select AMC</option>';
        foreach($data as $value){
            $html .= '<option value="'.$value->AMCCode.'">'.$value->AMCName.'</option>';
        }
        
        echo $html;
    }
    
    public function get_client_folionumber()
    {
         $schemeid='';
         $html='';
        if(!isset($_POST['schemeid'])){ 
          $html = '<option value="">Select Folio</option>';
        }else{ 
            
            
            $account=$_POST['account'];
            
            $broker_Id =$this->session->userdata('user_id');
            
            $data = $this->mfp->get_clientid_by_bsc_account($account,$broker_Id);
            
            if($data)
            {
             
                $client_Id =  $data[0]->client_id;
                $schemeid = $_POST['schemeid'];   
                
                
                $data= $this->mfp->get_client_folionumber($schemeid,$account);
                
                $html = '<option value="">Select Folio</option>';
                foreach($data as $value){
                    $html .= '<option value="'.$value->folio_number.'">'.$value->folio_number.'</option>';
                }
            }
        } 
        echo $html;
    }
    
    public function find_scheme_detail()
    {
        
        
        $schemename = $this->input->post('schemename');
        $schemetype = $this->input->post('schemetype');
        $account=$this->input->post('account');
       
        $data= $this->mfp->find_scheme_detail($schemename,$schemetype,$account);
        
        echo json_encode(array('Status'=> true,'data'=>$data));
    }
    
    function mungXML($xml) 
    {
        $obj = SimpleXML_Load_String($xml);
        if ($obj === FALSE)
            return $xml;
    
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss))
            return $xml;
    
        $nsm = array_keys($nss);
        foreach ($nsm as $key) {
            $rgx = '#'
                    . '('
                    . '\<'
                    . '/?'
                    . preg_quote($key)
                    . ')'
                    . '('
                    . ':{1}'
                    . ')'
                    . '#'
            ;
            $rep = '$1'
                    . '_'
            ;
            $xml = preg_replace($rgx, $rep, $xml);
        }
    
        return $xml;
    }
   
    public function MFPurchaseDetail()
    {
        $id = $this->input->post('sid');
        
        $condition = array('bs.schemecode' => $id);
        echo json_encode(array('Status'=> true,'data'=>$this->mfp->bsc_scheme_detail($condition)));
        
        
    
    }
    
    public function MFPurchaseDetail_old()
    {
        $id=json_decode($this->input->cookie('s_data'), false);
        
        $condition = array('bs.id' => $id->id);
        $data['schmeme_detail'] = $this->mfp->bsc_scheme_detail($condition);
        $data['account']=$id;
        $this->load->view('client/common/header');
        $this->load->view('client/MFPurchaseDetail',$data);
        $this->load->view('client/common/footer');
        
    
    }
  
    public function placeorder()
    {
         
        
        $auth=$this->getAuthenticationToken();
       // print_r($auth);die();
        if($auth=='error')
        {
            echo json_encode(array('Status'=> 1,'Message'=>'Bse Login failed.')); die();
        }
        
         
        $userId= $this->session->userdata['user_id'];
        
        
        $password=$auth;
        $SchemeCode = $this->input->post('SchemeCode');
        $clientaccount = $this->input->post('account');
        $amount = $this->input->post('amount');
        
        $condition = array('bs.schemecode' => $SchemeCode);
        $schemedetail=$this->mfp->bsc_scheme_detail($condition);
        
        if($schemedetail)
        {
            foreach($schemedetail as $val)
            {
                if($amount>199999 && $val->SchemeType!='LIQUID')
                {
                    $SchemeCode=$SchemeCode.'-L1';    
                }
            }
        }
        $uniquerefno= date("Ymdhis").'1' ;
        $BuySellType='FRESH';
        $FolioNo= $this->input->post('folionumber');
        $clientKYCStatus='Y';
        $passkey=$this->passkey;
        $remarks='Purchase by i solutions';
        $RefNo='000111';
        $BSCUserId=$this->session->userdata['BSCUserId'];
        $BSCMemberId=$this->session->userdata['BSCMemberId'];
        $EUIN=$this->session->userdata['EUIN'];
        
        $RequestData= array();
        $RequestData['ServiceURL']=$this->soapUrl;
        $RequestData['TranscationType']='Purchase';
        $RequestData['TransactionCode']='NEW';
        $RequestData['TransNo']=$uniquerefno;
        $RequestData['OrderId']='';
        $RequestData['UserID']=$BSCUserId;
        $RequestData['MemberId']=$BSCMemberId;
        $RequestData['ClientCode']=$clientaccount;
        $RequestData['SchemeCd']=$SchemeCode;
        $RequestData['BuySell']='P';
        $RequestData['BuySellType']=$BuySellType;
        $RequestData['DPTxn']='P';
        $RequestData['Amount']=$amount;
        $RequestData['Qty']='';
        $RequestData['AllRedeem']='N';
        $RequestData['FolioNo']=$FolioNo;
        $RequestData['Remarks']=$remarks;
        $RequestData['KYCStatus']=$clientKYCStatus;
        $RequestData['SubBrCode']='';
        $RequestData['EUIN']=$EUIN;
        $RequestData['EUINVal']='Y';
        $RequestData['MinRedeem']='N';
        $RequestData['DPC']='Y';
        $RequestData['IPAdd']='';
        $RequestData['Password']=$password;
        $RequestData['PassKey']=$passkey;
        $RequestData['Param1']='';
        $RequestData['Param2']='';
        $RequestData['Param3']='';
        
        $RequestData['SIPStartDate']='';
        $RequestData['SIPFrequency']='';
        $RequestData['Type']='Lumpsum';
        
        $RequestData['CreatedBy']=$userId;
        
        $RefNo = $this->mfp->add_transcation_request($RequestData);
        
        
        $RequestString='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:bses="http://bsestarmf.in/">
           <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://bsestarmf.in/MFOrderEntry/orderEntryParam</wsa:Action><wsa:To>https://bsestarmfdemo.bseindia.com/MFOrderEntry/MFOrder.svc/Secure</wsa:To></soap:Header>
           <soap:Body>
              <bses:orderEntryParam>
                 <!--Optional:-->
                 <bses:TransCode>NEW</bses:TransCode>
                 <!--Optional:-->
                 <bses:TransNo>'.$uniquerefno.'</bses:TransNo>
                 <!--Optional:-->
                 <bses:OrderId></bses:OrderId>
                 <!--Optional:-->
                 <bses:UserID>'.$BSCUserId.'</bses:UserID>
                 <!--Optional:-->
                 <bses:MemberId>'.$BSCMemberId.'</bses:MemberId>
                 <!--Optional:-->
                 <bses:ClientCode>'.$clientaccount.'</bses:ClientCode>
                 <!--Optional:-->
                 <bses:SchemeCd>'.$SchemeCode.'</bses:SchemeCd>
                 <!--Optional:-->
                 <bses:BuySell>P</bses:BuySell>
                 <!--Optional:-->
                 <bses:BuySellType>'.$BuySellType.'</bses:BuySellType>
                 <!--Optional:-->
                 <bses:DPTxn>P</bses:DPTxn>
                 <!--Optional:-->
                 <bses:OrderVal>'.$amount.'</bses:OrderVal>
                 <!--Optional:-->
                 <bses:Qty></bses:Qty>
                 <!--Optional:-->
                 <bses:AllRedeem>N</bses:AllRedeem>
                 <!--Optional:-->
                 <bses:FolioNo>'.$FolioNo.'</bses:FolioNo>
                 <!--Optional:-->
                 <bses:Remarks>'.$remarks.'</bses:Remarks>
                 <!--Optional:-->
                 <bses:KYCStatus>'.$clientKYCStatus.'</bses:KYCStatus>
                 <!--Optional:-->
                 <bses:RefNo>'.$RefNo.'</bses:RefNo>
                 <!--Optional:-->
                 <bses:SubBrCode></bses:SubBrCode>
                 <!--Optional:-->
                 <bses:EUIN>'.$this->session->userdata['EUIN'].'</bses:EUIN>
                 <!--Optional:-->
                 <bses:EUINVal>Y</bses:EUINVal>
                 <!--Optional:-->
                 <bses:MinRedeem>N</bses:MinRedeem>
                 <!--Optional:-->
                 <bses:DPC>Y</bses:DPC>
                 <!--Optional:-->
                 <bses:IPAdd></bses:IPAdd>
                 <!--Optional:-->
                 <bses:Password>'.$password.'</bses:Password>
                 <!--Optional:-->
                 <bses:PassKey>'.$passkey.'</bses:PassKey>
                 <!--Optional:-->
                 <bses:Parma1></bses:Parma1>
                 <!--Optional:-->
                 <bses:Param2></bses:Param2>
                 <!--Optional:-->
                 <bses:Param3></bses:Param3>
              </bses:orderEntryParam>
           </soap:Body>
        </soap:Envelope>';

        
        $arrayResult= $this->callapi($RequestString,$this->soapUrl);
        
        $Result=$arrayResult['s_Body']['orderEntryParamResponse']['orderEntryParamResult'];
        $ResultArray=explode("|", $Result);
        
        $responsData=array();
        $responsData['TransactionCode']=$ResultArray[0];
        $responsData['UniqueReferenceNumber']=$ResultArray[1];
        $responsData['OrderId']=$ResultArray[2];
        $responsData['UserID']=$ResultArray[3];
        $responsData['MemberId']=$ResultArray[4];
        $responsData['ClientCode']=$ResultArray[5];
        $responsData['BSCRemarks']=$ResultArray[6];
        $responsData['SuccessFlag']=$ResultArray[7];
        $responsData['SIP_REG_ID']='';
        $responsData['XSIP_REG_ID']='';
        $responsData['CreatedBy']=$userId;
        
        $RefNo = $this->mfp->add_transcation_response($responsData);
        
        echo json_encode(array('Status'=> $RefNo,'Message'=>$ResultArray[6], 'OrderId' => $responsData['OrderId']));
        
    }
    function getAuthenticationTokenUAT()
    {
        
        $BSCUserId = $this->session->userdata['BSCUserId'];
        $BSCPassword =$this->session->userdata['BSCPassword'];
        $passkey=$this->passkey;
        
        $RequestString='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:bses="http://bsestarmf.in/">
                           <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://bsestarmf.in/MFOrderEntry/getPassword</wsa:Action><wsa:To>https://bsestarmf.in/MFOrderEntry/MFOrder.svc/Secure</wsa:To></soap:Header>
                           <soap:Body>
                              <bses:getPassword xmlns="http://bsestarmf.in/">
                                 <!--Optional:-->
                                 <bses:UserId>'.$BSCUserId.'</bses:UserId>
                                 <!--Optional:-->
                                 <bses:Password>'.$BSCPassword.'</bses:Password>
                                 <!--Optional:-->
                                 <bses:PassKey>'.$passkey.'</bses:PassKey>
                              </bses:getPassword>
                           </soap:Body>
                        </soap:Envelope>';
         
                        
         $arrayResult= $this->callapi($RequestString,$this->soapUrlUAT);
         
         //print_r($arrayResult);die();
        $token=explode("|", $arrayResult['s_Body']['getPasswordResponse']['getPasswordResult']);
            
            if($token[0]=='100')
            {
                return $token[1];
            }
            else
            {
                return 'error';
            }
    
    } 
    function getAuthenticationToken()
    {
        
        $BSCUserId = $this->session->userdata['BSCUserId'];
        $BSCPassword =$this->session->userdata['BSCPassword'];
        $passkey=$this->passkey;
        
        $RequestString='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:bses="http://bsestarmf.in/">
                           <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://bsestarmf.in/MFOrderEntry/getPassword</wsa:Action><wsa:To>https://bsestarmf.in/MFOrderEntry/MFOrder.svc/Secure</wsa:To></soap:Header>
                           <soap:Body>
                              <bses:getPassword xmlns="http://bsestarmf.in/">
                                 <!--Optional:-->
                                 <bses:UserId>'.$BSCUserId.'</bses:UserId>
                                 <!--Optional:-->
                                 <bses:Password>'.$BSCPassword.'</bses:Password>
                                 <!--Optional:-->
                                 <bses:PassKey>'.$passkey.'</bses:PassKey>
                              </bses:getPassword>
                           </soap:Body>
                        </soap:Envelope>';
         
                        
         $arrayResult= $this->callapi($RequestString,$this->soapUrl);
         //print_r($arrayResult);die();
        $token=explode("|", $arrayResult['s_Body']['getPasswordResponse']['getPasswordResult']);
            
            if($token[0]=='100')
            {
                return $token[1];
            }
            else
            {
                return 'error';
            }
    
    }
   
    function callapi($RequestString,$url)
    {
          $curl = curl_init();
            
        curl_setopt_array($curl, array(
            CURLOPT_URL =>$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$RequestString,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/soap+xml"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
            
        curl_close($curl);
          
        if ($err) {
           return $err;
        } else {
            $plainXML = $this->mungXML(trim($response));
            return json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        }
    }
    
    function MakePayment($RequestData)
    {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST'){
            $order_id = $this->input->post('order_id');
            $transcation_data = $this->mfp->get_transcation_response($order_id);
            
            if(!empty($transcation_data)){
                
                $RequestData = array();
                $RequestData['membercode'] =$this->session->userdata['BSCMemberId'];//'28761';
                $RequestData['clientcode'] =  $transcation_data[0]->ClientCode;
                $RequestData['modeofpayment'] =$this->input->post('mode');//'NODAL';
                $RequestData['bankid'] = $this->input->post('bankid');
                $RequestData['accountnumber'] = $this->input->post('accountnumber');
                $RequestData['ifsc'] =$this->input->post('ifsc');
                $RequestData['ordernumber'] = $order_id;
                $RequestData['totalamount'] = $transcation_data[0]->Amount;
                $RequestData['internalrefno'] = $transcation_data[0]->UniqueReferenceNumber;
                $RequestData['NEFTreference'] = '';//$this->input->post('neft_reference');
                $RequestData['mandateid'] = '';//$this->input->post('mandateid')
                $RequestData['vpaid'] = '';
                $RequestData['loopbackURL'] = 'https://3tense.com/client/PurchaseMutualFund/MFSummary?OrderNumber='.$order_id;
                $RequestData['allowloopBack'] = 'Y';
                $RequestData['filler1'] = '';
                $RequestData['filler2'] = '';
                $RequestData['filler3'] = '';
                $RequestData['filler4'] = '';
                $RequestData['filler5'] = '';
                $req = $this->mfp->add_payment_request($RequestData);
                $RequestData['LoginId'] = $this->session->userdata['BSCUserId'];
                $RequestData['Password'] = $this->session->userdata['BSCPassword'];

               // $url = 'https://bsestarmfdemo.bseindia.com/StarMFSinglePaymentAPI/Single/Payment';
                // Create a new cURL resource
                
                $ch = curl_init($this->Paymenturl);
                $payload = json_encode($RequestData);
                
                //print_r($payload);die();
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $result = curl_exec($ch);
                curl_close($ch);
                
                $res_data = json_decode($result);
                $response['responsestring'] = $res_data->responsestring;
                $response['statuscode'] = $res_data->statuscode;
                $response['internalrefno'] = $res_data->internalrefno;
                $response['filler1'] = $res_data->filler1;
                $response['filler2'] = $res_data->filler2;
                $response['filler3'] = $res_data->filler3;
                $response['filler4'] = $res_data->filler4;
                $response['CreatedBy'] = $this->session->userdata['user_id'];
                
                $res = $this->mfp->add_payment_response($response);
                
                if($response['statuscode'] == "101"){
                     echo json_encode(array('Status'=> $response['statuscode'],'Message'=>$res_data, 'OrderId' => $transcation_data[0]->OrderId,'request'=>$RequestData,'res'=>$res_data));
                    
                    /*$data['order_id'] = $transcation_data[0]->OrderId;
                    $data['massage'] = $response['responsestring'];
                    $this->load->view('client/common/header');
                    $this->load->view('client/MFPurchasePayment',$data);
                    $this->load->view('client/common/footer');*/
                }else{
                     echo json_encode(array('Status'=> $response['statuscode'],'Message'=>'Success','data'=>$res_data, 'OrderId' => $transcation_data[0]->OrderId,'request'=>$RequestData,'res'=>$res_data));
                }
            }else{
                 echo json_encode(array('Status'=> 101,'Message'=>'Invalid Transcation.', 'OrderId' =>0,'request'=>$RequestData,'res'=>$res_data));
            }
            
        }else{
        	
        	if(isset($RequestData) && $RequestData != '' && $RequestData != 0){
            	$data[] = array();
            	$transcation_data = $this->mfp->get_transcation_response($RequestData);
            	if(!empty($transcation_data)){
            		$data['order_id'] = $RequestData;
            		
            		$this->load->view('client/common/header');
            		$this->load->view('client/MFPurchasePayment',$data);
            		$this->load->view('client/common/footer');
            	}else{
            		redirect('client/PurchaseMutualFund');
            	}
            }else{
        		redirect('client/PurchaseMutualFund');
        	}
        }
    }
    
    
    public function GetBankDetail()
    {
        
        $order_id = $this->input->post('order_id');
        $transcation_data = $this->mfp->get_transcation_response($order_id);
        if(!empty($transcation_data)){
        
            $data['account_list'] = $this->mfp->bsc_bank_list($transcation_data[0]->ClientCode);
        
            echo json_encode(array('Status'=> 1,'Message'=>$data));        
        }
        else
        {
            echo json_encode(array('Status'=> 0,'Message'=>'Account Not found. Please contact administrator.'));
        }
      
        
    }
    
    public function GetMendateBankDetail()
    {
        
        $ClientCode = $this->input->post('ClientCode');
        
        $data['account_list'] = $this->mfp->bsc_mendate_bank_list($ClientCode);
    
        echo json_encode(array('Status'=> 1,'Message'=>$data));        
        
        
    }
    
    function getAdditionServiceAuthenticationToken()
    {
        
        $BSCUserId = $this->session->userdata['BSCUserId'];
        $BSCPassword =$this->session->userdata['BSCPassword'];
        $BSCMemberId =$this->session->userdata['BSCMemberId'];
        $passkey=$this->passkey;
        $RequestString='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:bses="http://bsestarmf.in/">
                           <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://bsestarmf.in/MFUploadService/getPassword</wsa:Action>
                           <wsa:To>https://bsestarmf.in/MFUploadService/MFOrder.svc/Secure</wsa:To></soap:Header>
                           <soap:Body>
                              <bses:getPassword xmlns="http://bsestarmf.in/">
                                 <bses:UserId>'.$BSCUserId.'</bses:UserId>
                                 <bses:MemberId>'.$BSCMemberId.'</bses:MemberId>
                                 <bses:Password>'.$BSCPassword.'</bses:Password>
                                 <bses:PassKey>'.$passkey.'</bses:PassKey>
                              </bses:getPassword>
                           </soap:Body>
                        </soap:Envelope>';
         $arrayResult= $this->callapi($RequestString,$this->soapAdditionServiceUrl);
         
        $token=explode("|", $arrayResult['s_Body']['getPasswordResponse']['getPasswordResult']);
            
            if($token[0]=='100')
            {
                return $token[1];
            }
            else
            {
                return 'error';
            }
    
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
                           
                    $UniqueNo="";$SchemeCode="";$RTASchemeCode="";$AMCSchemeCode="";$ISIN="";$AMCCode="";$AMCName="";$SchemeType="";$SchemePlan="";
                    $SchemeName="";$PurchaseAllowed="";$PurchaseTransactionmode="";$MinimumPurchaseAmount="";$AdditionalPurchaseAmount="";$MaximumPurchaseAmount="";
                    $PurchaseAmountMultiplier="";$PurchaseCutoffTime="";$RedemptionAllowed="";$RedemptionTransactionMode="";$MinimumRedemptionQty="";$RedemptionQtyMultiplier="";
                    $MaximumRedemptionQty="";$RedemptionAmountMinimum="";$RedemptionAmountMaximum="";$RedemptionAmountMultiple="";$RedemptionCutoffTime="";$RTAAgentCode="";
                    $AMCActiveFlag="";$DividendReinvestmentFlag="";$SIPFLAG="";$STPFLAG="";$SWPFlag="";$SwitchFLAG="";$SETTLEMENTTYPE="";$AMC_IND="";
                    $FaceValue="";$StartDate="";$EndDate="";$ExitLoadFlag="";$ExitLoad="";$LockInPeriodFlag="";$LockinPeriod="";$ChannelPartnerCode="";


                    
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
                                        strtoupper($cell)==strtoupper('Purchase Amount Multiplier') ||strtoupper($cell)==strtoupper('Purchase Cutoff Time') ||strtoupper($cell)==strtoupper('Redemption Allowed') ||
                                        strtoupper($cell)==strtoupper('Redemption Transaction Mode') ||strtoupper($cell)==strtoupper('Minimum Redemption Qty') ||strtoupper($cell)==strtoupper('Redemption Qty Multiplier') ||
                                        strtoupper($cell)==strtoupper('Maximum Redemption Qty') ||strtoupper($cell)==strtoupper('Redemption Amount - Minimum') ||
                                        strtoupper($cell)==strtoupper('Redemption Amount â€“ Maximum') ||strtoupper($cell)==strtoupper('Redemption Amount Multiple') ||
                                        strtoupper($cell)==strtoupper('Redemption Cut off Time') ||strtoupper($cell)==strtoupper('RTA Agent Code') ||
                                        strtoupper($cell)==strtoupper('AMC Active Flag') ||strtoupper($cell)==strtoupper('Dividend Reinvestment Flag') ||
                                        strtoupper($cell)==strtoupper('SIP FLAG') ||strtoupper($cell)==strtoupper('STP FLAG') ||
                                        strtoupper($cell)==strtoupper('SWP Flag') ||strtoupper($cell)==strtoupper('Switch FLAG') ||
                                        strtoupper($cell)==strtoupper('SETTLEMENT TYPE') ||strtoupper($cell)==strtoupper('AMC_IND') ||
                                        strtoupper($cell)==strtoupper('Face Value') ||strtoupper($cell)==strtoupper('Start Date') ||
                                        strtoupper($cell)==strtoupper('End Date') ||strtoupper($cell)==strtoupper('Exit Load Flag') ||
                                        strtoupper($cell)==strtoupper('Exit Load') ||strtoupper($cell)==strtoupper('Lock-in Period Flag') ||
                                        strtoupper($cell)==strtoupper('Lock-in Period') ||strtoupper($cell)==strtoupper('Channel Partner Code'))
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
                                   
                                    if(strtoupper($dataColumns[$countCell]) ===strtoupper('UNIQUE NO'))
                                    {
                                        if($cell || $cell != ''){
                                            $UniqueNo = trim($cell);
                                        }
                                        else{
                                            $UniqueNo ='';
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
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('AMC Name'))
                                    {
                                        if($cell || $cell != ''){
                                            $AMCName = trim($cell);
                                        }
                                        else{
                                            $AMCName ='';
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
                                    else if(strtoupper($dataColumns[$countCell]) ===strtoupper('Redemption Amount â€“ Maximum'))
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
                                            $LockinPeriod = trim($cell);
                                        }
                                        else{
                                            $LockinPeriod ='';
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
                                if($UniqueNo==0 || $UniqueNo=='')
                                {
                                    break;
                                }
                                else
                                {
                                    $id=0;
                                    $res = $this->mfp->check_bsc_scheme_by_unique_no($UniqueNo);
                                    if($res[0] &&  $res[0]->id>0)
                                    {
                                        $id=$res[0]->id;
                                    }
                                    
                                    $dataRows[$add_FD_list] = array('Id'=>$id,'UniqueNo'=>$UniqueNo,'SchemeCode'=>$SchemeCode,'RTASchemeCode'=>$RTASchemeCode,'AMCSchemeCode'=>$AMCSchemeCode,
                                                            'ISIN'=>$ISIN,'AMCCode'=>$AMCCode,'AMCName'=>$AMCName,'SchemeType'=>$SchemeType,'SchemePlan'=>$SchemePlan,'SchemeName'=>$SchemeName,'PurchaseAllowed'=>$PurchaseAllowed,
                                                            'PurchaseTransactionmode'=>$PurchaseTransactionmode,'MinimumPurchaseAmount'=>$MinimumPurchaseAmount,'AdditionalPurchaseAmount'=>$AdditionalPurchaseAmount,
                                                            'MaximumPurchaseAmount'=>$MaximumPurchaseAmount, 'PurchaseAmountMultiplier'=>$PurchaseAmountMultiplier,'PurchaseCutoffTime'=>$PurchaseCutoffTime,
                                                            'RedemptionAllowed'=>$RedemptionAllowed,'RedemptionTransactionMode'=>$RedemptionTransactionMode,'MinimumRedemptionQty'=>$MinimumRedemptionQty,
                                                            'RedemptionQtyMultiplier'=>$RedemptionQtyMultiplier,'MaximumRedemptionQty'=>$MaximumRedemptionQty,'RedemptionAmountMinimum'=>$RedemptionAmountMinimum,
                                                            'RedemptionAmountOtherMaximum'=>$RedemptionAmountOtherMaximum,'RedemptionAmountMultiple'=>$RedemptionAmountMultiple,'RedemptionCutoffTime'=>$RedemptionCutoffTime,
                                                            'RTAAgentCode'=>$RTAAgentCode,'AMCActiveFlag'=>$AMCActiveFlag,'DividendReinvestmentFlag'=>$DividendReinvestmentFlag,'SIPFLAG'=>$SIPFLAG,'STPFLAG'=>$STPFLAG,
                                                            'SWPFlag'=>$SWPFlag,'SwitchFLAG'=>$SwitchFLAG,'SETTLEMENTTYPE'=>$SETTLEMENTTYPE,'AMC_IND'=>$AMC_IND,'FaceValue'=>$FaceValue,'StartDate'=>$StartDate,'EndDate'=>$EndDate,
                                                            'ExitLoadFlag'=>$ExitLoadFlag,'ExitLoad'=>$ExitLoad,'LockInPeriodFlag'=>$LockInPeriodFlag,'LockInPeriod'=>$LockInPeriod,'ChannelPartnerCode'=>$ChannelPartnerCode);
                                    
                                    if($id!=0)
                                    {
                                        $this->mfp->update_bsc_scheme( $dataRows[$add_FD_list]);
                                    }
                                    else
                                    {
                                        $inserted = $this->mfp->add_bsc_scheme( $dataRows[$add_FD_list]);
                                    }
                                    $transID=$inserted;
                                    $uploadedStatus = 1;
                                    if(is_array($inserted))
                                    {
                                        $uploadedStatus = 0;
                                        $message = 'Error while inserting records. '.$trans_id['message'];
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
                                  $this->common->last_import('Bse Scheme Master', $brokerID, $_FILES["import_FDs"]["name"], $user_id);
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
    
    public function sip() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        $client_Id = $this->session->userdata['client_id'];
        $broker_Id =$this->session->userdata('user_id');
        
        $FamilyData = $this->mfp->find_client_familyId($client_Id,$broker_Id);
        
        
        
        $family_id='';
        if($FamilyData)
        {
            if($FamilyData[0]->head_of_family=='1')
            {
                $family_id= $FamilyData[0]->family_id;
            }
        }
    
        $data['account_list'] = $this->mfp->bsc_account_list_SIP($client_Id,$family_id,$broker_Id);
        
        
        $condition = array('bs.SIPFLAG' => 'Y');
        $data['bsc_schmeme_Type_list'] = $this->mfp->bsc_scheme_type_list($condition);
        
        $this->load->view('client/common/header');
        $this->load->view('client/MFPurchaseSIP',$data);
        $this->load->view('client/common/footer');
    }
    
    public function get_bsc_amc_list_sip()
    {
         $schemetype='';
        if(!isset($_POST['schemetype'])){ 
          $schemetype = '';
        }else{ 
          $schemetype = $_POST['schemetype'];   
        } 
        
        $data= $this->mfp->bsc_scheme_amc_list($schemetype);
        $html = '<option value="">Select AMC</option>';
        foreach($data as $value){
            $html .= '<option value="'.$value->AMCCode.'">'.$value->AMCName.'</option>';
        }
        
        echo $html;
    }
    
    public function get_bsc_scheme_list_sip()
    {
         $search='';
        if(!isset($_POST['term'])){ 
          $search = '';
        }else{ 
          $search = $_POST['term'];   
        } 
        if(!isset($_POST['amc'])){ 
          $amc = '';
        }else{ 
          $amc = $_POST['amc'];   
        } 
        if(!isset($_POST['schemetype'])){ 
          $schemetype = '';
        }else{ 
          $schemetype = $_POST['schemetype'];   
        } 
        
        $data= $this->mfp->bsc_scheme_list_sip($search,$amc,$schemetype);

        echo json_encode($data);
    }
    
    public function placesiporder()
    {
        $auth=$this->getAuthenticationToken();
        
        if($auth=='error')
        {
            echo json_encode(array('Status'=> 1,'Message'=>'Bse Login failed.')); die();
        }
        
       $userId= $this->session->userdata['user_id'];
        
        $password=$auth;
        $SchemeCode = $this->input->post('SchemeCode');
        $clientaccount = $this->input->post('account');
        $amount = $this->input->post('amount');
        
        $SIPDate = $this->input->post('SIPDate');
        $frequency = $this->input->post('frequency');
        $mendateId = $this->input->post('mendateId');
        
        $date=date_create($SIPDate);
        $SIPDate=date_format($date,"d/m/Y");  
        
        
        $condition = array('bs.schemecode' => $SchemeCode);
        $schemedetail=$this->mfp->bsc_scheme_detail($condition);
        
        if($schemedetail)
        {
            foreach($schemedetail as $val)
            {
                if($amount>199999 && $val->SchemeType!='LIQUID')
                {
                    $SchemeCode=$SchemeCode.'-L1';    
                }
            }
        }
        
       
        
        $uniquerefno= date("Ymdhis").'1' ;
        $BuySellType='FRESH';
        $FolioNo= $this->input->post('folionumber');
        $clientKYCStatus='Y';
        $passkey=$this->passkey;
        $remarks='Purchase by i solutions';
        $RefNo='000111';
        $BSCUserId=$this->session->userdata['BSCUserId'];
        $BSCMemberId=$this->session->userdata['BSCMemberId'];
        $EUIN=$this->session->userdata['EUIN'];
        
        $transcation_data = $this->mfp->get_today_trancation_detail($clientaccount);
        if($transcation_data)
        {
            $FirstOrderFlag='N';
        }
        else
        {
            $FirstOrderFlag='Y';
        }
    
   
        $RequestData= array();
        $RequestData['ServiceURL']=$this->soapUrl;
        $RequestData['TranscationType']='Purchase';
        $RequestData['TransactionCode']='NEW';
        $RequestData['TransNo']=$uniquerefno;
        $RequestData['OrderId']='';
        $RequestData['UserID']=$BSCUserId;
        $RequestData['MemberId']=$BSCMemberId;
        $RequestData['ClientCode']=$clientaccount;
        $RequestData['SchemeCd']=$SchemeCode;
        $RequestData['BuySell']='P';
        $RequestData['BuySellType']=$BuySellType;
        $RequestData['DPTxn']='P';
        $RequestData['Amount']=$amount;
        $RequestData['Qty']='';
        $RequestData['AllRedeem']='N';
        $RequestData['FolioNo']=$FolioNo;
        $RequestData['Remarks']=$remarks;
        $RequestData['KYCStatus']=$clientKYCStatus;
        $RequestData['SubBrCode']='';
        $RequestData['EUIN']=$EUIN;
        $RequestData['EUINVal']='Y';
        $RequestData['MinRedeem']='N';
        $RequestData['DPC']='Y';
        $RequestData['IPAdd']='';
        $RequestData['Password']=$password;
        $RequestData['PassKey']=$passkey;
        $RequestData['Param1']='';
        $RequestData['Param2']='';
        $RequestData['Param3']='';
        $RequestData['SIPStartDate']=$SIPDate;
        $RequestData['SIPFrequency']=$frequency;
        $RequestData['Type']='SIP';
        $RequestData['MendateId']=$mendateId;
        
        $RequestData['CreatedBy']=$userId;
        
        $RefNo = $this->mfp->add_transcation_request($RequestData);
          
       
        $RequestString='<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:bses="http://bsestarmf.in/">
   <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://bsestarmf.in/MFOrderEntry/xsipOrderEntryParam</wsa:Action><wsa:To>https://www.bsestarmf.in/MFOrderEntry/MFOrder.svc/Secure</wsa:To></soap:Header>
   <soap:Body>
      <bses:xsipOrderEntryParam>
         <!--Optional:-->
         <bses:TransactionCode>NEW</bses:TransactionCode>
         <!--Optional:-->
         <bses:UniqueRefNo>'.$uniquerefno.'</bses:UniqueRefNo>
         <!--Optional:-->
         <bses:SchemeCode>'.$SchemeCode.'</bses:SchemeCode>
         <!--Optional:-->
         <bses:MemberCode>'.$BSCMemberId.'</bses:MemberCode>
         <!--Optional:-->
         <bses:ClientCode>'.$clientaccount.'</bses:ClientCode>
         <!--Optional:-->
         <bses:UserId>'.$BSCUserId.'</bses:UserId>
         <!--Optional:-->
         <bses:InternalRefNo>'.$RefNo.'</bses:InternalRefNo>
         <!--Optional:-->
         <bses:TransMode>P</bses:TransMode>
         <!--Optional:-->
         <bses:DpTxnMode>P</bses:DpTxnMode>
         <!--Optional:-->
         <bses:StartDate>'.$SIPDate.'</bses:StartDate>
         <!--Optional:-->
         <bses:FrequencyType>'.$frequency.'</bses:FrequencyType>
         <!--Optional:-->
         <bses:FrequencyAllowed>1</bses:FrequencyAllowed>
         <!--Optional:-->
         <bses:InstallmentAmount>'.$amount.'</bses:InstallmentAmount>
         <!--Optional:-->
         <bses:NoOfInstallment>999</bses:NoOfInstallment>
         <!--Optional:-->
         <bses:Remarks>By ISolution</bses:Remarks>
         <!--Optional:-->
         <bses:FolioNo>'.$FolioNo.'</bses:FolioNo>
         <!--Optional:-->
         <bses:FirstOrderFlag>'.$FirstOrderFlag.'</bses:FirstOrderFlag>
         <!--Optional:-->
         <bses:Brokerage></bses:Brokerage>
         <!--Optional:-->
         <bses:MandateID>'.$mendateId.'</bses:MandateID>
         <!--Optional:-->
         <bses:SubberCode></bses:SubberCode>
         <!--Optional:-->
         <bses:Euin>'.$this->session->userdata['EUIN'].'</bses:Euin>
         <!--Optional:-->
         <bses:EuinVal>Y</bses:EuinVal>
         <!--Optional:-->
         <bses:DPC>N</bses:DPC>
         <!--Optional:-->
         <bses:XsipRegID></bses:XsipRegID>
         <!--Optional:-->
         <bses:IPAdd></bses:IPAdd>
         <!--Optional:-->
         <bses:Password>'.$password.'</bses:Password>
         <!--Optional:-->
         <bses:PassKey>'.$passkey.'</bses:PassKey>
         <!--Optional:-->
         <bses:Param1></bses:Param1>
         <!--Optional:-->
         <bses:Param2></bses:Param2>
         <!--Optional:-->
         <bses:Param3></bses:Param3>
      </bses:xsipOrderEntryParam>
   </soap:Body>
</soap:Envelope>';
     
        $arrayResult= $this->callapi($RequestString,$this->soapUrl);
        
        
         
        $Result=$arrayResult['s_Body']['xsipOrderEntryParamResponse']['xsipOrderEntryParamResult'];
        $ResultArray=explode("|", $Result);
     
        $responsData=array();
        $responsData['TransactionCode']=$ResultArray[0];
        $responsData['UniqueReferenceNumber']=$ResultArray[1];
        $responsData['MemberId']=$ResultArray[2];
        $responsData['ClientCode']=$ResultArray[3];
        $responsData['UserID']=$ResultArray[4];
        $responsData['OrderId']=$ResultArray[5];
        $responsData['BSCRemarks']=$ResultArray[6];
        $responsData['SuccessFlag']=$ResultArray[7];
        $responsData['CreatedBy']=$userId;
        
        $RefNo = $this->mfp->add_transcation_response($responsData);
        
        echo json_encode(array('Status'=> $RefNo,'Message'=>$ResultArray[6], 'OrderId' => $responsData['OrderId']));
      
        
    }
    
    public function MFSummarySIP() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        if($_GET['OrderNumber'])
        {
            $client_Id = $this->session->userdata['client_id'];
            $broker_Id =$this->session->userdata('user_id');
            $order_id= $_GET['OrderNumber'];
            
            $transcation_data = $this->mfp->get_transcation_response($order_id);
            
            $data['transcation_data'] = $transcation_data;
            
            $this->load->view('client/common/header');
            $this->load->view('client/MFSummarySIP',$data);
            $this->load->view('client/common/footer');
        }
    }  
    
    public function MFSummary() 
    {
        if($_GET['OrderNumber'])
        {
            $client_Id = $this->session->userdata['client_id'];
            $broker_Id =$this->session->userdata('user_id');
            $order_id= $_GET['OrderNumber'];
            
            $transcation_data = $this->mfp->get_transcation_response($order_id);
            
            $data['transcation_data'] = $transcation_data;
            
            $this->load->view('client/common/header');
            $this->load->view('client/MFSummary',$data);
            $this->load->view('client/common/footer');
        }
    }   
    
    
    public function FixDeposit()
    {
        $client_Id = $this->session->userdata['client_id'];
        $broker_Id =$this->session->userdata('user_id');
        
        
        $SeniorCity = $this->mfp->get_fd_rate('Y');
        
        $NonSeniorCity = $this->mfp->get_fd_rate('N');
        
        
        
    
        $data['SeniorCity'] = $SeniorCity;
        
        $data['NonSeniorCity'] = $NonSeniorCity;
        
        $data['client_Id'] = $client_Id;
        
        $data['broker_Id'] = $broker_Id;
        
        $this->load->view('client/common/header');
        $this->load->view('client/FixDeposit',$data);
        $this->load->view('client/common/footer');
    }
    
    public function FDIndivisual() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        if($_GET['Id'])
        {
            $client_Id = $this->session->userdata['client_id'];
            $broker_Id =$this->session->userdata('user_id');
            $Id= $_GET['Id'];
            $RateType= $_GET['RateType'];
        
            $FamilyData = $this->mfp->find_client_familyId($client_Id,$broker_Id);
        
        
            $family_id='';
            if($FamilyData)
            {
                if($FamilyData[0]->head_of_family=='1')
                {
                    $family_id= $FamilyData[0]->family_id;
                }
            }
        
            $data['account_list'] = $this->mfp->fd_account_list($client_Id,$family_id,$broker_Id);
        
        
            
            $transcation_data = $this->mfp->get_fd_rate_detail($Id);
            
            $data['fd_detail'] = $transcation_data;
            $data['RateType'] = $RateType;
            $data['client_id'] = $client_Id;
            $data['broker_Id'] = $broker_Id;
            
            $this->load->view('client/common/header');
            $this->load->view('client/FDIndividual',$data);
            $this->load->view('client/common/footer');
        }
        else
        {
            echo 'Invalid request';
            
        }
    }   
    
    public function GeClientDetail()
    {
        
        $client_Id = $this->input->post('client_id');
        $broker_Id =$this->session->userdata('user_id');
        
        $data = $this->mfp->client_detail($client_Id,$broker_Id);
    
        echo json_encode(array('Status'=> 1,'Message'=>$data));        
        
        
    }
    
    public function SubmitFD()
    {
        
        $userId= $this->session->userdata['user_id'];
        $InvesterName = $this->input->post('InvesterName');
        $DOB = $this->input->post('DOB');
        $Pancard = $this->input->post('Pancard');
        $Address = $this->input->post('Address');
        $EmailId = $this->input->post('EmailId');
        $MobileNo = $this->input->post('MobileNo');
        $Holding = $this->input->post('Holding');
        $HolderName2 = $this->input->post('HolderName2');
        $HolderPancard2 = $this->input->post('HolderPancard2');
        $HolderDOB2 = $this->input->post('HolderDOB2');
        
        $HolderAddress2 = $this->input->post('HolderAddress2');
        $HolderName3 = $this->input->post('HolderName3');
        
        $HolderDOB3 = $this->input->post('HolderDOB3');
        $HolderPancard3 = $this->input->post('HolderPancard3');
        $HolderAddress3 = $this->input->post('HolderAddress3');
        $NomineeName = $this->input->post('NomineeName');
        $NomineeDOB = $this->input->post('NomineeDOB');
        
        $GaurdianName = $this->input->post('GaurdianName');
        $GaurdianNameDOB = $this->input->post('GaurdianNameDOB');
        $Relation = $this->input->post('Relation');
        $Tenure = $this->input->post('Tenure');
        $scheme = $this->input->post('scheme');
        $InterestFrequency = $this->input->post('InterestFrequency');
        
        $renewalType = $this->input->post('renewalType');
        $chequeCollectiondt = $this->input->post('chequeCollectiondt');
        $companyName= $this->input->post('companyName');
        $rate = $this->input->post('rate');
        $RateType = $this->input->post('RateType');
        
     
        $responsData=array();
        $responsData['InvesterName']=$InvesterName;
        $responsData['DOB']=$DOB;
        $responsData['Pancard']=$Pancard;
        $responsData['Address']=$Address;
        $responsData['EmailId']=$EmailId;
        $responsData['MobileNo']=$MobileNo;
        $responsData['Holding']=$Holding;
        $responsData['HolderName2']=$HolderName2;
        $responsData['HolderPancard2']=$HolderPancard2;
        $responsData['HolderDOB2']=$HolderDOB2;
        $responsData['HolderAddress2']=$HolderAddress2;
        
        $responsData['HolderName3']=$HolderName3;
        $responsData['HolderPancard3']=$HolderPancard3;
        $responsData['HolderAddress3']=$HolderAddress3;
        $responsData['NomineeName']=$NomineeName;
        $responsData['NomineeDOB']=$NomineeDOB;
        $responsData['GaurdianName']=$GaurdianName;
        $responsData['GaurdianNameDOB']=$GaurdianNameDOB;
        
        $responsData['Relation']=$Relation;
        $responsData['Tenure']=$Tenure;
        $responsData['scheme']=$scheme;
        $responsData['InterestFrequency']=$InterestFrequency;
        
        $responsData['renewalType']=$renewalType;
        $responsData['chequeCollectiondt']=$chequeCollectiondt;
        
        $responsData['companyName']=$companyName;
        $responsData['rate']=$rate;
        $responsData['RateType']=$RateType;
        $res['records'] = $responsData;
    
        $this->load->library('email');
        
        $this->email->from('no-reply@3tense.com');
        
        $mail_data = $this->load->view('fd_submit_view',$res,true);
        $message = $mail_data;
        
        $this->email->set_mailtype("html");
        
        $this->email->to('info@isolutionsadvisor.com');
           
        $this->email->subject('Individual FD Submission');
        $this->email->message($message);
             
        $this->email->send();
        
        echo json_encode(array('Status'=> 1,'Message'=>'Thank You for applying Fixed Deposit. Our representative will come for cheque collection at the given time'));
      
        
    }
    
    public function FDNonIndividual() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        if($_GET['Id'])
        {
            $client_Id = $this->session->userdata['client_id'];
            $broker_Id =$this->session->userdata('user_id');
            $Id= $_GET['Id'];
            $RateType= $_GET['RateType'];
            $FamilyData = $this->mfp->find_client_familyId($client_Id,$broker_Id);
        
        
            $family_id='';
            if($FamilyData)
            {
                if($FamilyData[0]->head_of_family=='1')
                {
                    $family_id= $FamilyData[0]->family_id;
                }
            }
        
            $data['account_list'] = $this->mfp->fd_account_list($client_Id,$family_id,$broker_Id);
        
        
            
            $transcation_data = $this->mfp->get_fd_rate_detail($Id);
            
            $data['fd_detail'] = $transcation_data;
            $data['RateType'] = $RateType;
            $data['client_id'] = $client_Id;
            $data['broker_Id'] = $broker_Id;
            
            $this->load->view('client/common/header');
            $this->load->view('client/FDNonIndividual',$data);
            $this->load->view('client/common/footer');
        }
        else
        {
            echo 'Invalid request';
            
        }
    }
    public function SubmitFDNonIndividual()
    {
                    
        $userId= $this->session->userdata['user_id'];
        $InvesterName = $this->input->post('InvesterName');
        $DOB = $this->input->post('DOB');
        $Pancard = $this->input->post('Pancard');
        $Address = $this->input->post('Address');
        $EmailId = $this->input->post('EmailId');
        $MobileNo = $this->input->post('MobileNo');
        
        $Partners1Name = $this->input->post('Partners1Name');
        $Partners1DOB = $this->input->post('Partners1DOB');
        $Partners1Pancard = $this->input->post('Partners1Pancard');
        $Partners1Address = $this->input->post('Partners1Address');
        $Partners1EmailId = $this->input->post('Partners1EmailId');
        $Partners1MobileNo = $this->input->post('Partners1MobileNo');
        
        $Partners2Name = $this->input->post('Partners2Name');
        $Partners2DOB = $this->input->post('Partners2DOB');
        $Partners2Pancard = $this->input->post('Partners2Pancard');
        $Partners2Address = $this->input->post('Partners2Address');
        $Partners2EmailId = $this->input->post('Partners2EmailId');
        $Partners2MobileNo = $this->input->post('Partners2MobileNo');
        
        $Partners3Name = $this->input->post('Partners3Name');
        $Partners3DOB = $this->input->post('Partners3DOB');
        $Partners3Pancard = $this->input->post('Partners3Pancard');
        $Partners3Address = $this->input->post('Partners3Address');
        $Partners3EmailId = $this->input->post('Partners3EmailId');
        $Partners3MobileNo = $this->input->post('Partners3MobileNo');
       
        $AnnualTurnover = $this->input->post('AnnualTurnover');
        $Tenure = $this->input->post('Tenure');
        $scheme = $this->input->post('scheme');
        $InterestFrequency = $this->input->post('InterestFrequency');
        $S15G = $this->input->post('S15G');
        $renewalType = $this->input->post('renewalType');
        $chequeCollectiondt = $this->input->post('chequeCollectiondt');
        $companyName= $this->input->post('companyName');
        $rate = $this->input->post('rate');
        $RateType = $this->input->post('RateType');
        
     
        $responsData=array();
        $responsData['InvesterName']=$InvesterName;
        $responsData['DOB']=$DOB;
        $responsData['Pancard']=$Pancard;
        $responsData['Address']=$Address;
        $responsData['EmailId']=$EmailId;
        $responsData['MobileNo']=$MobileNo;
        
        $responsData['Partners1Name']=$Partners1Name;
        $responsData['Partners1DOB']=$Partners1DOB;
        $responsData['Partners1Pancard']=$Partners1Pancard;
        $responsData['Partners1Address']=$Partners1Address;
        $responsData['Partners1EmailId']=$Partners1EmailId;
        $responsData['Partners1MobileNo']=$Partners1MobileNo;
        
        $responsData['Partners2Name']=$Partners2Name;
        $responsData['Partners2DOB']=$Partners2DOB;
        $responsData['Partners2Pancard']=$Partners2Pancard;
        $responsData['Partners2Address']=$Partners2Address;
        $responsData['Partners2EmailId']=$Partners2EmailId;
        $responsData['Partners2MobileNo']=$Partners2MobileNo;
        
        
        $responsData['Partners3Name']=$Partners3Name;
        $responsData['Partners3DOB']=$Partners3DOB;
        $responsData['Partners3Pancard']=$Partners3Pancard;
        $responsData['Partners3Address']=$Partners3Address;
        $responsData['Partners3EmailId']=$Partners3EmailId;
        $responsData['Partners3MobileNo']=$Partners3MobileNo;
        
        
     
        
        $responsData['AnnualTurnover']=$AnnualTurnover;
        $responsData['Tenure']=$Tenure;
        $responsData['scheme']=$scheme;
        $responsData['InterestFrequency']=$InterestFrequency;
        $responsData['S15G']=$S15G;
        $responsData['renewalType']=$renewalType;
        $responsData['chequeCollectiondt']=$chequeCollectiondt;
        $responsData['companyName']=$companyName;
        $responsData['rate']=$rate;
        $responsData['RateType']=$RateType;
        
        
        $res['records'] = $responsData;
        
        
        $this->load->library('email');
        
        $this->email->from('no-reply@3tense.com');
        
        $mail_data = $this->load->view('Non_Indi_fd_submit_view',$res,true);
        $message = $mail_data;
        
        $this->email->set_mailtype("html");
        
        $this->email->to('info@isolutionsadvisor.com');
           
        $this->email->subject('Non- Individual FD Submission');
        $this->email->message($message);
             
        $this->email->send();
        
        echo json_encode(array('Status'=> 1,'Message'=>'Thank You for applying Fixed Deposit. Our representative will come for cheque collection at the given time.'));
      
        
    }
        public function MFPuchaseSummaryForMobile() 
    {
         if($_GET['OrderNumber'])
        {
            $order_id= $_GET['OrderNumber'];
            
            $transcation_data = $this->mfp->get_transcation_response($order_id);
            
            $data['transcation_data'] = $transcation_data;
            
            
            $this->load->view('client/MFPuchaseSummaryForMobile',$data);
            
        }
            
        
    }   
}
