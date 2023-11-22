<?php
error_reporting(0);
class Purchase extends Ci_Controller{
    var $soapUrl = "https://www.bsestarmf.in/MFOrderEntry/MFOrder.svc/Secure"; // asmx URL of WSDL
    var $soapAdditionServiceUrl= "https://www.bsestarmf.in/MFUploadService/MFUploadService.svc"; // asmx URL of WSDL
    
    //var $Paymenturl = 'https://bsestarmfdemo.bseindia.com/StarMFSinglePaymentAPI/Single/Payment';
    var $Paymenturl = 'https://bsestarmf.in/StarMFSinglePaymentAPI/Single/Payment';
    
    var $passkey='123456';
    //var $soapUrl = "https://bsestarmfdemo.bseindia.com/MFOrderEntry/MFOrder.svc/Secure"; // asmx URL of WSDL//
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
        $data['broker_Id'] = $broker_Id;
        $data['client_Id'] = $client_Id;
        
        $this->load->view('client/common/header');
        $this->load->view('client/Purchase',$data);
        $this->load->view('client/common/footer');
    }
    public function PurchaseDetail() // Dipak 25/01/2016 changed for document upload limit ristriction
    {
        
         $schemetype='';
        if(isset($_GET['siteURL'])){ 
        
          $schemetype = $_GET['siteURL'];   
        
        $client_Id = $this->session->userdata['client_id'];
        $broker_Id =$this->session->userdata('user_id');
        $data['broker_Id'] = $broker_Id;
        $data['client_Id'] = $client_Id;
        $data['siteUrl'] = $schemetype;
        
        $this->load->view('client/common/header');
        $this->load->view('client/PurchaseDetail',$data);
        $this->load->view('client/common/footer');
         
        } 
        else
        {
            
        $client_Id = $this->session->userdata['client_id'];
        $broker_Id =$this->session->userdata('user_id');
        $data['broker_Id'] = $broker_Id;
        $data['client_Id'] = $client_Id;
        
        $this->load->view('client/common/header');
        $this->load->view('client/Purchase',$data);
        $this->load->view('client/common/footer');
        }
    }

}
