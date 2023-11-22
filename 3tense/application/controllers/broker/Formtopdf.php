<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Formtopdf extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
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
    }

    //function for insurance list page
    function index()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Form To PDF';
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
           
         $this->load->view('broker/common/header', $header);
        $this->load->view('broker/Formtopdf/FormList');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
        
        
        
    }
    function formisr1()
    {
        
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/ISR - 1.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
            
            $Date =$_POST['sr1_date'] ;
            $IsPanCard=false;
            if(isset($_POST['IsPanCard']))
            {
                $IsPanCard=$_POST['IsPanCard'];
            }
            $isSignture=false;
            if(isset($_POST['IsSignture']))
            {
                $isSignture=$_POST['IsSignture'];
            }
            $isMobileNumber=false;
            if(isset($_POST['IsMobileNumber']))
            {
            $isMobileNumber=$_POST['IsMobileNumber'];
            }
            $isBankdetail=false;
            if(isset($_POST['IsBankdetail']))
            {
            $isBankdetail=$_POST['IsBankdetail'];
            }
            
            $isRegisterdAddress=false;
            if(isset($_POST['IsRegisterdAddress']))
            {
            $isRegisterdAddress=$_POST['IsRegisterdAddress'];
            }
            
            $isEmailAddress=false;
            if(isset($_POST['IsEmailAddress']))
            {
            $isEmailAddress=$_POST['IsEmailAddress'];
            }
            
            $NameofCompany = $_POST['NameofCompany'];
            $FolioNumber =  $_POST['FolioNumber'];
            $facevalue =  $_POST['facevalue'];
            $NoOfSecurity =  $_POST['NoOfSecurity'];
            $DestinativeSecurityFrom = $_POST['DestinativeSecurityFrom'];
            $DestinativeSecurityTo =$_POST['DestinativeSecurityTo'];
            $emailAddress = $_POST['emailAddress'];
            $mobilenumber = $_POST['mobilenumber'];
            $NameofSecurityHolder1 = $_POST['NameofSecurityHolder1'];
            $NameofSecurityHolderPancard1 = $_POST['NameofSecurityHolderPancard1'];
            if( $_POST['IsPanLinkedWithAddhar1']=='Yes')
            {
            $IsPanLinkedWithAddhar1=true;
            }
            else
            {
            $IsPanLinkedWithAddhar1=false;    
            }
            $NameofSecurityHolder2 = $_POST['NameofSecurityHolder2'];
            $NameofSecurityHolderPancard2 = $_POST['NameofSecurityHolderPancard2'];
            if( $_POST['IsPanLinkedWithAddhar2']=='Yes')
            {
                $IsPanLinkedWithAddhar2=true;
            }
            else
            {
                $IsPanLinkedWithAddhar2=false;    
            }
            $NameofSecurityHolder3 = $_POST['NameofSecurityHolder3'];
            $NameofSecurityHolderPancard3 = $_POST['NameofSecurityHolderPancard3'];
            if( $_POST['IsPanLinkedWithAddhar3']=='Yes')
            {
                $IsPanLinkedWithAddhar3=true;
            }
            else
            {
                $IsPanLinkedWithAddhar3=false;    
            }
            
            $NameofSecurityHolder4 = $_POST['NameofSecurityHolder4'];
            $NameofSecurityHolderPancard4 = $_POST['NameofSecurityHolderPancard4'];
            if( $_POST['IsPanLinkedWithAddhar4']=='Yes')
            {
                $IsPanLinkedWithAddhar4=true;
            }
            else
            {
                $IsPanLinkedWithAddhar4=false;    
            }
            
            $NameOfBank = $_POST['NameOfBank'];
            $BankBranch = $_POST['BankBranch'];
            $IFSCCode = $_POST['IFSCCode'];
            $BankAccountNumber = $_POST['BankAccountNumber'];
            if(isset($_POST['AccType']))
            {
                $AccType=$_POST['AccType'];
            }
            else
            {
                $AccType=0;    
            }
            
            
            $DementAccountNo = $_POST['DementAccountNo'];
            $firstHolderNameline1 = $_POST['firstHolderNameline1'];
            $firstHolderNameLine2 = $_POST['firstHolderNameLine2'];
            $firstHolderAddressLine1 =  $_POST['firstHolderAddressLine1'];
            $firstHolderAddressLine2 =  $_POST['firstHolderAddressLine2'];
            $firstHolderAddressLine3 =  $_POST['firstHolderAddressLine3'];
            $firstHolderPinCode =  $_POST['firstHolderPinCode'];
            $SecondHolderNameline1 =  $_POST['SecondHolderNameline1'];
            $SecondHolderNameLine2 = $_POST['SecondHolderNameline2'];
            $SecondHolderAddressLine1 = $_POST['SecondHolderAddressLine1'];
            $SecondHolderAddressLine2 = $_POST['SecondHolderAddressLine2'];
            $SecondHolderAddressLine3 = $_POST['SecondHolderAddressLine3'];
            $SecondHolderPinCode = $_POST['SecondHolderPinCode'];
            $thirdHolderNameline1 =  $_POST['thirdHolderNameline1'];
            $thirdHolderNameLine2 = $_POST['thirdHolderNameline2'];
            $thirdHolderAddressLine1 =  $_POST['thirdHolderAddressLine1'];
            $thirdHolderAddressLine2 =  $_POST['thirdHolderAddressLine2'];
            $thirdHolderAddressLine3 =  $_POST['thirdHolderAddressLine3'];
            $thirdHolderPinCode =  $_POST['thirdHolderPinCode'];
            $FourHolderNameline1 = $_POST['FourHolderNameline1'];
            $FourHolderNameLine2 =$_POST['FourHolderNameline2'];
            $FourHolderAddressLine1 = $_POST['FourHolderAddressLine1'];
            $FourHolderAddressLine2 = $_POST['FourHolderAddressLine2'];
            $FourHolderAddressLine3 = $_POST['FourHolderAddressLine3'];
            $FourHolderPinCode = $_POST['FourHolderPinCode'];

            
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(173, 42);
            $pdf->MultiCell(0, 10, $Date, 0, 'L');
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IsPanCard==true)
            {
            $pdf->SetXY(14, 52);
            $pdf->Write(0, chr(51));
            }
            if($isSignture==true)
            {
            $pdf->SetXY(70, 52);
            $pdf->Write(0, chr(51));
            }
            if($isMobileNumber==true)
            {
            $pdf->SetXY(128, 52);
            $pdf->Write(0, chr(51));
            }
            
            if($isBankdetail==true)
            {
            $pdf->SetXY(14, 57);
            $pdf->Write(0, chr(51));
            }
            
            if($isRegisterdAddress==true)
            {
            $pdf->SetXY(70, 57);
            $pdf->Write(0, chr(51));
            }
            
            if($isEmailAddress==true)
            {
            $pdf->SetXY(128, 57);
            $pdf->Write(0, chr(51));
            }
            
            
            
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(70, 66);
            $pdf->MultiCell(0, 10, $NameofCompany, 0, 'L');
            
            $pdf->SetXY(170, 66);
            $pdf->MultiCell(0, 10, $FolioNumber, 0, 'L');
            
            $pdf->SetXY(70, 71);
            $pdf->MultiCell(0, 10, $facevalue, 0, 'L');
            
            $pdf->SetXY(170, 71);
            $pdf->MultiCell(0, 10, $NoOfSecurity, 0, 'L');
            
            $pdf->SetXY(70,81);
            $pdf->MultiCell(0, 10, $DestinativeSecurityFrom, 0, 'L');
            
            $pdf->SetXY(140, 81);
            $pdf->MultiCell(0, 10, $DestinativeSecurityTo, 0, 'L');
            
            $pdf->SetXY(70, 86);
            $pdf->MultiCell(0, 10, $emailAddress, 0, 'L');
            
            $pdf->SetXY(70, 93);
            $pdf->MultiCell(0, 10, $mobilenumber, 0, 'L');
            
            $pdf->SetXY(20, 120);
            $pdf->MultiCell(0, 10, $NameofSecurityHolder1, 0, 'L');
            
            $pdf->SetXY(129, 120);
            $pdf->MultiCell(0, 10, $NameofSecurityHolderPancard1, 0, 'L');
            
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IsPanLinkedWithAddhar1==true){
            
            $pdf->SetXY(178, 125);
            $pdf->Write(0, chr(51));
            }
            else
            {
            $pdf->SetXY(185, 125);
            $pdf->Write(0, chr(51));
            }
            
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(20, 127);
            $pdf->MultiCell(0, 10, $NameofSecurityHolder2, 0, 'L');
            
            $pdf->SetXY(129, 127);
            $pdf->MultiCell(0, 10, $NameofSecurityHolderPancard1, 0, 'L');
            
            
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IsPanLinkedWithAddhar2==true){
                $pdf->SetXY(178, 132);
                $pdf->Write(0, chr(51));
            }
            else
            {
                $pdf->SetXY(185, 132);
                $pdf->Write(0, chr(51));
            }
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(20, 133);
            $pdf->MultiCell(0, 10, $NameofSecurityHolder3, 0, 'L');
            
            $pdf->SetXY(129, 133);
            $pdf->MultiCell(0, 10, $NameofSecurityHolderPancard3, 0, 'L');
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IsPanLinkedWithAddhar3==true){
                $pdf->SetXY(178, 139);
            }
            else
            {
            $pdf->SetXY(185, 139);
            }
            $pdf->Write(0, chr(51));
            
            
            
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(20, 139);
            $pdf->MultiCell(0, 10, $NameofSecurityHolder4, 0, 'L');
            
            $pdf->SetXY(129, 139);
            $pdf->MultiCell(0, 10, $NameofSecurityHolderPancard4, 0, 'L');
            
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IsPanLinkedWithAddhar4==true){
                $pdf->SetXY(178, 144);
            }
            else
            {
                $pdf->SetXY(185, 144);
            }
            $pdf->Write(0, chr(51));
            
            
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(48, 161);
            $pdf->MultiCell(0, 10, $NameOfBank, 0, 'L');
            
            $pdf->SetXY(48, 165);
            $pdf->MultiCell(0, 10, $BankBranch, 0, 'L');
            
            $pdf->SetXY(158, 164);
            $pdf->MultiCell(0, 10, $IFSCCode, 0, 'L');
            
            $pdf->SetXY(48, 172);
            $pdf->MultiCell(0, 10, $BankAccountNumber, 0, 'L');
            
            
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($AccType==1){
                $pdf->SetXY(168, 176);
            }
            else if($AccType==2)
            {
                $pdf->SetXY(183, 176);
            }
            else if($AccType==3)
            {
                $pdf->SetXY(125, 180);
            }
            else if($AccType==4)
            {
                $pdf->SetXY(138, 180);
            }
            else if($AccType==5)
            {
                $pdf->SetXY(152, 180);
            }
            $pdf->Write(0, chr(51));
            
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(110, 192);
            $pdf->MultiCell(0, 10, $DementAccountNo, 0, 'L');
            
            $pdf->SetXY(20, 235);
            $pdf->MultiCell(0, 10, $firstHolderNameline1, 0, 'L');
            
            $pdf->SetXY(20, 239);
            $pdf->MultiCell(0, 10, $firstHolderNameLine2, 0, 'L');
            
            $pdf->SetXY(20, 249);
            $pdf->MultiCell(0, 10, $firstHolderAddressLine1, 0, 'L');
            
            $pdf->SetXY(20, 254);
            $pdf->MultiCell(0, 10, $firstHolderAddressLine2, 0, 'L');
            
            $pdf->SetXY(20, 259);
            $pdf->MultiCell(0, 10, $firstHolderAddressLine3, 0, 'L');
            
            $pdf->SetXY(24, 266);
            $pdf->MultiCell(0, 10, $firstHolderPinCode, 0, 'L');
            
            $pdf->SetXY(65, 235);
            $pdf->MultiCell(0, 10, $SecondHolderNameline1, 0, 'L');
            
            $pdf->SetXY(65, 239);
            $pdf->MultiCell(0, 10, $SecondHolderNameLine2, 0, 'L');
            
            $pdf->SetXY(65, 249);
            $pdf->MultiCell(0, 10, $SecondHolderAddressLine1, 0, 'L');
            
            $pdf->SetXY(65, 254);
            $pdf->MultiCell(0, 10, $SecondHolderAddressLine2, 0, 'L');
            
            $pdf->SetXY(65, 259);
            $pdf->MultiCell(0, 10, $SecondHolderAddressLine3, 0, 'L');
            
            $pdf->SetXY(68, 266);
            $pdf->MultiCell(0, 10, $SecondHolderPinCode, 0, 'L');
                        
            $pdf->SetXY(112, 235);
            $pdf->MultiCell(0, 10, $thirdHolderNameline1, 0, 'L');
            
            $pdf->SetXY(112, 239);
            $pdf->MultiCell(0, 10, $thirdHolderNameLine2, 0, 'L');
            
            $pdf->SetXY(112, 249);
            $pdf->MultiCell(0, 10, $thirdHolderAddressLine1, 0, 'L');
            
            $pdf->SetXY(112, 254);
            $pdf->MultiCell(0, 10, $thirdHolderAddressLine2, 0, 'L');
            
            $pdf->SetXY(112, 259);
            $pdf->MultiCell(0, 10, $thirdHolderAddressLine3, 0, 'L');
            
            $pdf->SetXY(115, 266);
            $pdf->MultiCell(0, 10, $thirdHolderPinCode, 0, 'L');
            
            $pdf->SetXY(156, 235);
            $pdf->MultiCell(0, 10, $FourHolderNameline1, 0, 'L');
            
            $pdf->SetXY(156, 239);
            $pdf->MultiCell(0, 10, $FourHolderNameLine2, 0, 'L');
            
            $pdf->SetXY(156, 249);
            $pdf->MultiCell(0, 10, $FourHolderAddressLine1, 0, 'L');
            
            $pdf->SetXY(156, 254);
            $pdf->MultiCell(0, 10, $FourHolderAddressLine2, 0, 'L');
            
            $pdf->SetXY(112, 259);
            $pdf->MultiCell(0, 10, $FourHolderAddressLine3, 0, 'L');
            
            $pdf->SetXY(158, 266);
            $pdf->MultiCell(0, 10, $FourHolderPinCode, 0, 'L');
                        
            
            // render PDF to browser
            $pdf->Output('Form ISR 1.pdf', 'D');
            
            
    }
    
    function formisr2()
    {
        
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/ISR - 2.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        
            $BankName=$_POST['BankName'];
            $BankBranch=$_POST['BankBranch'];
            $BankAddress=$_POST['BankAddress'];
            $BankPhoneNumber=$_POST['BankPhoneNumber'];
            $BankEmail=$_POST['BankEmail'];
            $BankAccountNo=$_POST['BankAccountNo'];
            $AccountOpeningDate=$_POST['AccountOpeningDate'];
            $AccountHolderName1=$_POST['AccountHolderName1'];
            $AccountHolderName2=$_POST['AccountHolderName2'];
            $AccountHolderName3=$_POST['AccountHolderName3'];
            $AddressLine1=$_POST['AddressLine1'];
            $AddressLine2=$_POST['AddressLine2'];
            $AddressLine3=$_POST['AddressLine3'];
            $PhoneNumber=$_POST['PhoneNumber'];
            $EmailAdress=$_POST['EmailAdress'];
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(108, 54);
            $pdf->MultiCell(0, 10, $BankName, 0, 'L');
            
            $pdf->SetXY(108, 58);
            $pdf->MultiCell(0, 10, $BankBranch, 0, 'L');
            
            $pdf->SetXY(108, 63);
            $pdf->MultiCell(0, 10, $BankAddress, 0, 'L');
            
            $pdf->SetXY(108, 69);
            $pdf->MultiCell(0, 10, $BankPhoneNumber, 0, 'L');
            
            $pdf->SetXY(108, 74);
            $pdf->MultiCell(0, 10, $BankEmail, 0, 'L');
            
            $pdf->SetXY(108, 84);
            $pdf->MultiCell(0, 10, $BankAccountNo, 0, 'L');
            
            $pdf->SetXY(108, 93);
            $pdf->MultiCell(0, 10, $AccountOpeningDate, 0, 'L');
            
            $pdf->SetXY(111, 102);
            $pdf->MultiCell(0, 10, $AccountHolderName1, 0, 'L');
            
            $pdf->SetXY(111, 111);
            $pdf->MultiCell(0, 10, $AccountHolderName2, 0, 'L');
            
            $pdf->SetXY(111, 119);
            $pdf->MultiCell(0, 10, $AccountHolderName3, 0, 'L');
            
            $pdf->SetXY(111, 179);
            $pdf->MultiCell(0, 10, $AddressLine1, 0, 'L');
            
            $pdf->SetXY(111, 183);
            $pdf->MultiCell(0, 10, $AddressLine2, 0, 'L');
            
            $pdf->SetXY(111, 187);
            $pdf->MultiCell(0, 10, $AddressLine3, 0, 'L');
            
            $pdf->SetXY(111, 195);
            $pdf->MultiCell(0, 10, $PhoneNumber, 0, 'L');
            
            $pdf->SetXY(111, 200);
            $pdf->MultiCell(0, 10, $EmailAdress, 0, 'L');
            
            // render PDF to browser
            $pdf->Output('Form ISR 2.pdf', 'D');
            //$pdf->Output('Equity Portfolio.pdf', 'D');
            
            
    }
    
    function formisr4()
    {
        
            $Date=$_POST['sr1_date'];
            $DementAccountNumber=$_POST['DementAccountNumber'];//' I   N   3  0   0  2  1   4     6  0   1   3  6  8   8   0';
            $PanNo=$_POST['PanNo'];
            
             $IssueOfDuplicateCertificate=false;
            if(isset($_POST['IssueOfDuplicateCertificate']))
            {
                $IssueOfDuplicateCertificate=$_POST['IssueOfDuplicateCertificate'];
            }
            
             $ClaimFromUnclaimSuspenseAccount=false;
            if(isset($_POST['ClaimFromUnclaimSuspenseAccount']))
            {
                $ClaimFromUnclaimSuspenseAccount=$_POST['ClaimFromUnclaimSuspenseAccount'];
            }
            
             $ReplacementRenewalExchangeOfSecurityCertificate=false;
            if(isset($_POST['ReplacementRenewalExchangeOfSecurityCertificate']))
            {
                $ReplacementRenewalExchangeOfSecurityCertificate=$_POST['ReplacementRenewalExchangeOfSecurityCertificate'];
            }
            
             $Endorsement=false;
            if(isset($_POST['Endorsement']))
            {
                $Endorsement=$_POST['Endorsement'];
            }
            
             $SubdivisionSlittingOfSecurityCertificate=false;
            if(isset($_POST['SubdivisionSlittingOfSecurityCertificate']))
            {
                $SubdivisionSlittingOfSecurityCertificate=$_POST['SubdivisionSlittingOfSecurityCertificate'];
            }
            
             $ConsolidationOfFolios=false;
            if(isset($_POST['ConsolidationOfFolios']))
            {
                $ConsolidationOfFolios=$_POST['ConsolidationOfFolios'];
            }
            
             $ConsolidationOfSecurityCertificate=false;
            if(isset($_POST['ConsolidationOfSecurityCertificate']))
            {
                $ConsolidationOfSecurityCertificate=$_POST['ConsolidationOfSecurityCertificate'];
            }
            
             $Transmission=false;
            if(isset($_POST['Transmission']))
            {
                $Transmission=$_POST['Transmission'];
            }
            
             $Transposition=false;
            if(isset($_POST['Transposition']))
            {
                $Transposition=$_POST['Transposition'];
            }
            $NameOfTheIssuerCompany=$_POST['NameOfTheIssuerCompany'];
            $FolioNumber=$_POST['FolioNumber'];
            $NameOfSecurityHolder1=$_POST['NameOfSecurityHolder1'];
            $NameOfSecurityHolder2=$_POST['NameOfSecurityHolder2'];
            $NameOfSecurityHolder3=$_POST['NameOfSecurityHolder3'];
            $CertificateNumber=$_POST['CertificateNumber'];
            $DistinctiveNumber=$_POST['DistinctiveNumber'];
            $NumberAndFaceValueOfSecurities=$_POST['NumberAndFaceValueOfSecurities'];
            $DuplicateSecuritiesCertificate=false;
            if(isset($_POST['DuplicateSecuritiesCertificate']))
            {
                $DuplicateSecuritiesCertificate=$_POST['DuplicateSecuritiesCertificate'];
            }
            
            $ClaimFromUnclaimSuspenseAccount_1=false;
            if(isset($_POST['ClaimFromUnclaimSuspenseAccount_1']))
            {
                $ClaimFromUnclaimSuspenseAccount_1=$_POST['ClaimFromUnclaimSuspenseAccount_1'];
            }
            
            $ReplacementRenewalExchangeOfSecurityCertificate_1=false;
            if(isset($_POST['ReplacementRenewalExchangeOfSecurityCertificate_1']))
            {
                $ReplacementRenewalExchangeOfSecurityCertificate_1=$_POST['ReplacementRenewalExchangeOfSecurityCertificate_1'];
            }
            
            $Endorsement_1=false;
            if(isset($_POST['Endorsement_1']))
            {
                $Endorsement_1=$_POST['Endorsement_1'];
            }
            $SubdivisionSlittingOfSecurityCertificate_1=false;
            if(isset($_POST['SubdivisionSlittingOfSecurityCertificate_1']))
            {
                $SubdivisionSlittingOfSecurityCertificate_1=$_POST['SubdivisionSlittingOfSecurityCertificate_1'];
            }
            
            $ConsolidationOfSecurityCertificate_1=false;
            if(isset($_POST['ConsolidationOfSecurityCertificate_1']))
            {
                $ConsolidationOfSecurityCertificate_1=$_POST['ConsolidationOfSecurityCertificate_1'];
            }
            
            $Transmission_1=false;
            if(isset($_POST['Transmission_1']))
            {
                $Transmission_1=$_POST['Transmission_1'];
            }
            
            $Transposition_1=false;
            if(isset($_POST['Transposition_1']))
            {
                $Transposition_1=$_POST['Transposition_1'];
            }
             $SecurityHolder1=$_POST['SecurityHolder1'];
            $SecurityHolder1AddressLine1=$_POST['SecurityHolder1AddressLine1'];
            $SecurityHolder1AddressLine2=$_POST['SecurityHolder1AddressLine2'];
             $SecurityHolder1AddressLine3=$_POST['SecurityHolder1AddressLine3'];
            $SecurityHolder1Pincode=$_POST['SecurityHolder1Pincode'];//'2   2  3   4   5  6';
            $SecurityHolder2=$_POST['SecurityHolder2'];
            $SecurityHolder2AddressLine1=$_POST['SecurityHolder2AddressLine1'];
            $SecurityHolder2AddressLine2=$_POST['SecurityHolder2AddressLine2'];
            $SecurityHolder2AddressLine3=$_POST['SecurityHolder2AddressLine3'];
            $SecurityHolder2Pincode=$_POST['SecurityHolder2Pincode'];//'2   2  3   4   5  6';
            $SecurityHolder3=$_POST['SecurityHolder3'];
            $SecurityHolder3AddressLine1=$_POST['SecurityHolder3AddressLine1'];
            $SecurityHolder3AddressLine2=$_POST['SecurityHolder3AddressLine2'];
            $SecurityHolder3AddressLine3=$_POST['SecurityHolder3AddressLine3'];
            $SecurityHolder3Pincode=$_POST['SecurityHolder3Pincode'];//'2   2  3   4   5  6';
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/ISR - 4.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(159, 42);
            $pdf->MultiCell(0, 10, $Date, 0, 'L');
            
            $pdf->SetXY(92, 80);
            $pdf->MultiCell(0, 10, $DementAccountNumber, 0, 'L');
            
            $pdf->SetXY(40, 108);
            $pdf->MultiCell(0, 10, $PanNo, 0, 'L');
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            if($IssueOfDuplicateCertificate==true)
            {
                $pdf->SetXY(26, 166);
                $pdf->Write(0, chr(51));
            }
            if($ClaimFromUnclaimSuspenseAccount==true)
            {
                $pdf->SetXY(107, 166);
                $pdf->Write(0, chr(51));
            }
            if($ReplacementRenewalExchangeOfSecurityCertificate==true)
            {
                $pdf->SetXY(26, 180);
                $pdf->Write(0, chr(51));
            }
            if($Endorsement==true)
            {
                $pdf->SetXY(107, 180);
                $pdf->Write(0, chr(51));
            }
            if($SubdivisionSlittingOfSecurityCertificate==true)
            {
                $pdf->SetXY(26, 195);
                $pdf->Write(0, chr(51));
            }
            if($ConsolidationOfFolios==true)
            {
                $pdf->SetXY(107, 195);
                $pdf->Write(0, chr(51));
            }
             if($ConsolidationOfSecurityCertificate==true)
            {
                $pdf->SetXY(26, 208);
                $pdf->Write(0, chr(51));
            }
            if($Transmission==true)
            {
                $pdf->SetXY(107, 208);
                $pdf->Write(0, chr(51));
            }
            if($Transposition==true)
            {
                $pdf->SetXY(26, 217);
                $pdf->Write(0, chr(51));
            }
            
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(85, 232);
            $pdf->MultiCell(0, 10, $NameOfTheIssuerCompany, 0, 'L');
            
            
            $pdf->SetXY(85, 241);
            $pdf->MultiCell(0, 10, $FolioNumber, 0, 'L');
            
            
            $pdf->SetXY(90, 247);
            $pdf->MultiCell(0, 10, $NameOfSecurityHolder1, 0, 'L');
            
            
            $pdf->SetXY(90, 254);
            $pdf->MultiCell(0, 10, $NameOfSecurityHolder2, 0, 'L');
            
            
            $pdf->SetXY(90, 262);
            $pdf->MultiCell(0, 10, $NameOfSecurityHolder3, 0, 'L');
            
            $tplIdx1 = $pdf->importPage(2, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx1);
            
            
            $pdf->SetXY(90, 7);
            $pdf->MultiCell(0, 10, $CertificateNumber, 0, 'L');
            
            
            $pdf->SetXY(90, 14);
            $pdf->MultiCell(0, 10, $DistinctiveNumber, 0, 'L');
            
            
            $pdf->SetXY(90, 23);
            $pdf->MultiCell(0, 10, $NumberAndFaceValueOfSecurities, 0, 'L');
            
            $pdf->SetFont('ZapfDingbats', '', 12);
            
            if($DuplicateSecuritiesCertificate==true)
            {
                $pdf->SetXY(39, 57);
                $pdf->Write(0, chr(51));
            }
            
            if($ClaimFromUnclaimSuspenseAccount_1==true)
            {
                $pdf->SetXY(39, 68);
                $pdf->Write(0, chr(51));
            }
            
            if($ReplacementRenewalExchangeOfSecurityCertificate_1==true)
            {
                $pdf->SetXY(39, 97);
                $pdf->Write(0, chr(51));
            }
            
            if($Endorsement_1==true)
            {
                $pdf->SetXY(39, 113);
                $pdf->Write(0, chr(51));
            }
            
            if($SubdivisionSlittingOfSecurityCertificate_1==true)
            {
                $pdf->SetXY(39, 119);
                $pdf->Write(0, chr(51));
            }
            
            if($ConsolidationOfSecurityCertificate_1==true)
            {
                $pdf->SetXY(39, 125);
                $pdf->Write(0, chr(51));
            }
            
            if($Transmission_1==true)
            {
                $pdf->SetXY(39, 131);
                $pdf->Write(0, chr(51));
            }
            
            if($Transposition_1==true)
            {
                $pdf->SetXY(39, 137);
                $pdf->Write(0, chr(51));
            }
            
             $pdf->SetFont('Arial', '', 12);
           
            $pdf->SetXY(55, 189);
            $pdf->MultiCell(0, 10, $SecurityHolder1, 0, 'L');
            
            
            $pdf->SetXY(55, 195);
            $pdf->MultiCell(0, 10, $SecurityHolder1AddressLine1, 0, 'L');
            
            
            $pdf->SetXY(55, 200);
            $pdf->MultiCell(0, 10, $SecurityHolder1AddressLine2, 0, 'L');
            
            $pdf->SetXY(55, 205);
            $pdf->MultiCell(0, 10, $SecurityHolder1AddressLine3, 0, 'L');
            
            $pdf->SetXY(63, 211);
            $pdf->MultiCell(0, 10, $SecurityHolder1Pincode, 0, 'L');
            
            $pdf->SetXY(121, 189);
            $pdf->MultiCell(0, 10, $SecurityHolder2, 0, 'L');
            
            $pdf->SetXY(121, 195);
            $pdf->MultiCell(0, 10, $SecurityHolder2AddressLine1, 0, 'L');
            
            $pdf->SetXY(121, 200);
            $pdf->MultiCell(0, 10, $SecurityHolder2AddressLine2, 0, 'L');
            
            $pdf->SetXY(121, 205);
            $pdf->MultiCell(0, 10, $SecurityHolder2AddressLine3, 0, 'L');
            
            
            $pdf->SetXY(123, 211);
            $pdf->MultiCell(0, 10, $SecurityHolder2Pincode, 0, 'L');
            
            $pdf->SetXY(164, 189);
            $pdf->MultiCell(0, 10, $SecurityHolder3, 0, 'L');
            
            $pdf->SetXY(164, 195);
            $pdf->MultiCell(0, 10, $SecurityHolder3AddressLine1, 0, 'L');
            
            $pdf->SetXY(164, 200);
            $pdf->MultiCell(0, 10, $SecurityHolder3AddressLine2, 0, 'L');
            
            $pdf->SetXY(164, 205);
            $pdf->MultiCell(0, 10, $SecurityHolder3AddressLine3, 0, 'L');
            
            
            $pdf->SetXY(166, 211);
            $pdf->MultiCell(0, 10, $SecurityHolder3Pincode, 0, 'L');
            
            // render PDF to browser
            //$pdf->Output();
            $pdf->Output('Form ISR - 4.pdf', 'D');
            
            
    }
    
    function formisr5()
    {
        
            $Name5=$_POST['Name5'];
            $son5=$_POST['son5'];
            $AddressLine15=$_POST['AddressLine15'];
            $AddressLine25=$_POST['AddressLine25'];
            $AccountNo5=$_POST['AccountNo5'];
            $SecuritiesUnder5=$_POST['SecuritiesUnder5'];
            $FolioNo5=$_POST['FolioNo5'];
            $CertificateNo5=$_POST['CertificateNo5'];
            $DistinctiveFrom5=$_POST['DistinctiveFrom5'];
            $DistinctiveTo5=$_POST['DistinctiveTo5'];
            $NoofSecuritiesHold5=$_POST['NoofSecuritiesHold5'];
            $SignatureName15=$_POST['SignatureName15'];
            $SignatureAddress15=$_POST['SignatureAddress15'];
            $SignatureName25=$_POST['SignatureName25'];
            $SignatureAddress25=$_POST['SignatureAddress25'];
            $AddressFirstHolderLine1=$_POST['AddressFirstHolderLine1'];
            $AddressFirstHolderLine2=$_POST['AddressFirstHolderLine2'];
            $AddressFirstHolderLine3=$_POST['AddressFirstHolderLine3'];
            $Pincode5=$_POST['Pincode5'];
            $TelNo5=$_POST['TelNo5'];
            $Email5=$_POST['Email5'];
            $sr5_date=$_POST['sr5_date'];
            
            
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/PRE - FILLED - FORM  B - INDEMNITY - 300.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(50, 82);
            $pdf->MultiCell(0, 10, $Name5, 0, 'L');
            
            $pdf->SetXY(46, 87);
            $pdf->MultiCell(0, 10, $son5, 0, 'L');
            
            $pdf->SetXY(140, 87);
            $pdf->MultiCell(0, 10, $AddressLine15, 0, 'L');
            $pdf->SetXY(37, 92);
            $pdf->MultiCell(0, 10, $AddressLine25, 0, 'L');
            
            $pdf->SetXY(114, 97);
            $pdf->MultiCell(0, 10, $AccountNo5, 0, 'L');
            
            $pdf->SetXY(147, 114);
            $pdf->MultiCell(0, 10, $SecuritiesUnder5, 0, 'L');
            
            $pdf->SetXY(56, 119);
            $pdf->MultiCell(0, 10, $FolioNo5, 0, 'L');
            
            $pdf->SetXY(36, 163);
            $pdf->MultiCell(0, 10, $FolioNo5, 0, 'L');
            
            $pdf->SetXY(59, 163);
            $pdf->MultiCell(0, 10, $CertificateNo5, 0, 'L');
            
            $pdf->SetXY(84, 163);
            $pdf->MultiCell(0, 10, $DistinctiveFrom5, 0, 'L');
            
            $pdf->SetXY(112, 163);
            $pdf->MultiCell(0, 10, $DistinctiveTo5, 0, 'L');
            
            $pdf->SetXY(143, 163);
            $pdf->MultiCell(0, 10, $NoofSecuritiesHold5, 0, 'L');
            
            
            $pdf->SetXY(65, 255);
            $pdf->MultiCell(0, 10, $SignatureName15, 0, 'L');
            
            $pdf->SetXY(65, 262);
            $pdf->MultiCell(0, 10, $SignatureAddress15, 0, 'L');
            
           $tplIdx1 = $pdf->importPage(2, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx1);
            
            $pdf->SetXY(65, 29);
            $pdf->MultiCell(0, 10, $SignatureName25, 0, 'L');
            
            $pdf->SetXY(65, 36);
            $pdf->MultiCell(0, 10, $SignatureAddress25, 0, 'L');
            
            $pdf->SetXY(20, 73);
            $pdf->MultiCell(0, 10, $AddressFirstHolderLine1, 0, 'L');
            
            $pdf->SetXY(20, 78);
            $pdf->MultiCell(0, 10, $AddressFirstHolderLine2, 0, 'L');
            
            $pdf->SetXY(20, 84);
            $pdf->MultiCell(0, 10, $AddressFirstHolderLine3, 0, 'L');
            
            $pdf->SetXY(40, 90);
            $pdf->MultiCell(0, 10, $Pincode5, 0, 'L');
            
            $pdf->SetXY(44, 104);
            $pdf->MultiCell(0, 10, $TelNo5, 0, 'L');
            
            $pdf->SetXY(44, 112);
            $pdf->MultiCell(0, 10, $Email5, 0, 'L');
            
            $pdf->SetXY(44, 121);
            $pdf->MultiCell(0, 10, $sr5_date, 0, 'L');
            
            
            // render PDF to browser
            //$pdf->Output();
            $pdf->Output('PRE - FILLED - FORM  B - INDEMNITY - 300.pdf', 'D');
            
            
    }
    
    function formisr6()
    {
        
            $Name6=$_POST['Name6'];
            $son6=$_POST['son6'];
            $AddressLine16=$_POST['AddressLine16'];
            $AddressLine26=$_POST['AddressLine26'];
            $AccountNo6=$_POST['AccountNo6'];
            $SecuritiesUnder6=$_POST['SecuritiesUnder6'];
            $FolioNo6=$_POST['FolioNo6'];
            $CertificateNo6=$_POST['CertificateNo6'];
            $DistinctiveFrom6=$_POST['DistinctiveFrom6'];
            $DistinctiveTo6=$_POST['DistinctiveTo6'];
            
            $NoofSecuritiesHold6=$_POST['NoofSecuritiesHold6'];
            
            $Deponent6=$_POST['Deponent6'];
            $SolemnlyAffirmedAt6=$_POST['SolemnlyAffirmedAt6'];
            
            $Place6=$_POST['Place6'];
            $sr6_date=$_POST['sr6_date'];
            
            
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/PRE-FILLED - FORM A - AFFIDAVIT - 100.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(40, 82);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
            $pdf->SetXY(33, 87);
            $pdf->MultiCell(0, 10, $son6, 0, 'L');
            
            $pdf->SetXY(120, 87);
            $pdf->MultiCell(0, 10, $AddressLine16, 0, 'L');
           $pdf->SetXY(23, 92);
            $pdf->MultiCell(0, 10, $AddressLine26, 0, 'L');
            
            $pdf->SetXY(23, 97);
            $pdf->MultiCell(0, 10, $AccountNo6, 0, 'L');
            
            $pdf->SetXY(50, 114);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
            $pdf->SetXY(173, 114);
            $pdf->MultiCell(0, 10, $SecuritiesUnder6, 0, 'L');
            
             $pdf->SetXY(71, 119);
            $pdf->MultiCell(0, 10, $FolioNo6, 0, 'L');
            
            $pdf->SetXY(24, 156);
            $pdf->MultiCell(0, 10, $FolioNo6, 0, 'L');
            
            $pdf->SetXY(49, 156);
            $pdf->MultiCell(0, 10, $CertificateNo6, 0, 'L');
            
            $pdf->SetXY(77, 156);
            $pdf->MultiCell(0, 10, $DistinctiveFrom6, 0, 'L');
            
            $pdf->SetXY(105, 156);
            $pdf->MultiCell(0, 10, $DistinctiveTo6, 0, 'L');
            
           $pdf->SetXY(135, 156);
            $pdf->MultiCell(0, 10, $NoofSecuritiesHold6, 0, 'L');
            
            
             $pdf->SetXY(35, 167);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
                $pdf->SetXY(35, 192);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
                $pdf->SetXY(35, 217);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
                $pdf->SetXY(35, 257);
            $pdf->MultiCell(0, 10, $Name6, 0, 'L');
            
            $tplIdx1 = $pdf->importPage(2, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx1);
            
            $pdf->SetXY(50, 125);
            $pdf->MultiCell(0, 10,$Deponent6, 0, 'L');
            
            $pdf->SetXY(73, 156);
            $pdf->MultiCell(0, 10, $SolemnlyAffirmedAt6, 0, 'L');
            
            $pdf->SetXY(39, 225);
            $pdf->MultiCell(0, 10, $Place6, 0, 'L');
            
            $pdf->SetXY(39, 233);
            $pdf->MultiCell(0, 10, $sr6_date, 0, 'L');
            
            
            // render PDF to browser
           // $pdf->Output();
            $pdf->Output('PRE-FILLED - FORM A - AFFIDAVIT - 100.pdf', 'D');
            
            
    }
    
    function formisr7()
    {
        
            $sr7_date=$_POST['sr7_date'];
            $Companyname7=$_POST['Companyname7'];
            $AddressLine17=$_POST['AddressLine17'];
            $AddressLine27=$_POST['AddressLine27'];
            $NatureOfSecurities=$_POST['NatureOfSecurities'];
            
            
            $FolioNo7=$_POST['FolioNo7'];
            $NoOfSecurities7=$_POST['NoOfSecurities7'];
            
            $CertificateNo7=$_POST['CertificateNo7'];
            $DistinctiveFrom7=$_POST['DistinctiveFrom7'];
            $DistinctiveTo7=$_POST['DistinctiveTo7'];
            
            $NameofNominee7=$_POST['NameofNominee7'];
            
            $NomineeAddressLine17=$_POST['NomineeAddressLine17'];
            $NomineeAddressLine27=$_POST['NomineeAddressLine27'];
            $NomineeAddressLine37=$_POST['NomineeAddressLine37'];
            
            $sr7_date_of_birth=$_POST['sr7_date_of_birth'];
            $FatherName7=$_POST['FatherName7'];
            $occupation7=$_POST['occupation7'];
            
            
            $Relationship7=$_POST['Relationship7'];
            $Nationality7=$_POST['Nationality7'];
            $EmailId7=$_POST['EmailId7'];
            $MobileNo7=$_POST['MobileNo7'];
            $NameofGuardian7=$_POST['NameofGuardian7'];
            $sr6_date_of_guadian=$_POST['sr6_date_of_guadian'];
            $AddressofGuardian7=$_POST['AddressofGuardian7'];
            $sr6_date_of_Attaining7=$_POST['sr6_date_of_Attaining7'];
            $FirstHolderName7=$_POST['FirstHolderName7'];
            $JointHolder1Name7=$_POST['JointHolder1Name7'];
            $JointHolder2Name7=$_POST['JointHolder2Name7'];
            $JointHolder3Name7=$_POST['JointHolder3Name7'];
            $NameofWitness7=$_POST['NameofWitness7'];
            $WitnessAddressLine17=$_POST['WitnessAddressLine17'];
            $WitnessAddressLine27=$_POST['WitnessAddressLine27'];
            $WitnessAddressLine37=$_POST['WitnessAddressLine37'];
            $WitnessPincode7=$_POST['WitnessPincode7'];
            
            
            
            
            
            
            
            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/fpdf/fpdf.php');
            //require_once('application/third_party/fpdi/src/Fpdi.php');
            require_once('application/third_party/fpdi/src/autoload.php');
            
            $pdf = new \setasign\Fpdi\Fpdi();
            $pages_count = $pdf->setSourceFile('application/third_party/fpdf/SH - 13.pdf');
            $tplIdx = $pdf->importPage(1, '/MediaBox');
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        
            $pdf->SetFont('Arial', '', 12);
            
            $pdf->SetXY(172, 41);
            $pdf->MultiCell(0, 10, $sr7_date, 0, 'L');
            
            $pdf->SetXY(50, 51);
            $pdf->MultiCell(0, 10, $Companyname7, 0, 'L');
            
            $pdf->SetXY(50, 56);
            $pdf->MultiCell(0, 10, $AddressLine17, 0, 'L');
            
            $pdf->SetXY(50, 60);
            $pdf->MultiCell(0, 10, $AddressLine27, 0, 'L');
            
            
             $pdf->SetFont('ZapfDingbats', '', 12);
            if($NatureOfSecurities=='E')
            {
                $pdf->SetXY(16, 102);
                $pdf->Write(0, chr(51));
            }
            if($NatureOfSecurities=='D')
            {
                $pdf->SetXY(28, 102);
                $pdf->Write(0, chr(51));
            }
            if($NatureOfSecurities=='B')
            {
                $pdf->SetXY(16, 106);
                $pdf->Write(0, chr(51));
            }
            $pdf->SetFont('Arial', '', 12);
           $pdf->SetXY(46, 103);
            $pdf->MultiCell(0, 10, $FolioNo7, 0, 'L');
            
            $pdf->SetXY(72, 103);
            $pdf->MultiCell(0, 10, $NoOfSecurities7, 0, 'L');
            
             $pdf->SetXY(104, 103);
            $pdf->MultiCell(0, 10, $CertificateNo7, 0, 'L');
            
             $pdf->SetXY(140, 97);
            $pdf->MultiCell(0, 10, $DistinctiveFrom7, 0, 'L');
            
            
             $pdf->SetXY(140, 102);
            $pdf->MultiCell(0, 10, 'To', 0, 'L');
            
             $pdf->SetXY(140, 106);
            $pdf->MultiCell(0, 10, $DistinctiveTo7, 0, 'L');
            
            
             $pdf->SetXY(50, 119);
            $pdf->MultiCell(0, 10, $NameofNominee7, 0, 'L');
            
             $pdf->SetXY(50, 125);
            $pdf->MultiCell(0, 10, $NomineeAddressLine17, 0, 'L');
            
             $pdf->SetXY(50, 130);
            $pdf->MultiCell(0, 10, $NomineeAddressLine27, 0, 'L');
            
             $pdf->SetXY(50, 135);
            $pdf->MultiCell(0, 10, $NomineeAddressLine37, 0, 'L');
            
            $pdf->SetXY(163, 130);
            $pdf->MultiCell(0, 10, $sr7_date_of_birth, 0, 'L');
            
            $pdf->SetXY(50, 144);
            $pdf->MultiCell(0, 10, $FatherName7, 0, 'L');
            
            $pdf->SetXY(163, 142);
            $pdf->MultiCell(0, 10, $occupation7, 0, 'L');
            
            $pdf->SetXY(50, 154);
            $pdf->MultiCell(0, 10, $Relationship7, 0, 'L');
            
            $pdf->SetXY(163, 154);
            $pdf->MultiCell(0, 10, $Nationality7, 0, 'L');
            
            $pdf->SetXY(50, 163);
            $pdf->MultiCell(0, 10, $EmailId7, 0, 'L');
            $pdf->SetXY(163, 163);
            $pdf->MultiCell(0, 10, $MobileNo7, 0, 'L');
            
            $pdf->SetXY(40, 175);
            $pdf->MultiCell(0, 10, $NameofGuardian7, 0, 'L');
            
            $pdf->SetXY(168, 175);
            $pdf->MultiCell(0, 10, $sr6_date_of_guadian, 0, 'L');
            
            $pdf->SetXY(40, 185);
            $pdf->MultiCell(0, 10, $AddressofGuardian7, 0, 'L');
            
            $pdf->SetXY(168, 185);
            $pdf->MultiCell(0, 10, $sr6_date_of_Attaining7, 0, 'L');
            
            
            $pdf->SetXY(20, 223);
            $pdf->MultiCell(0, 10, $FirstHolderName7, 0, 'L');
            
            $pdf->SetXY(75, 223);
            $pdf->MultiCell(0, 10, $JointHolder1Name7, 0, 'L');
            
            $pdf->SetXY(120, 223);
            $pdf->MultiCell(0, 10, $JointHolder2Name7, 0, 'L');
            
            $pdf->SetXY(163, 223);
            $pdf->MultiCell(0, 10, $JointHolder3Name7, 0, 'L');
            
            $pdf->SetXY(46, 237);
            $pdf->MultiCell(0, 10, $NameofWitness7, 0, 'L');
            
            $pdf->SetXY(46, 242);
            $pdf->MultiCell(0, 10, $WitnessAddressLine17, 0, 'L');
            
            $pdf->SetXY(46, 247);
            $pdf->MultiCell(0, 10, $WitnessAddressLine27, 0, 'L');
            
            $pdf->SetXY(46, 251);
            $pdf->MultiCell(0, 10, $WitnessAddressLine37, 0, 'L');
            
            $pdf->SetXY(114, 251);
            $pdf->MultiCell(0, 10, $WitnessPincode7, 0, 'L');
            
            $pdf->SetXY(160, 256);
            $pdf->MultiCell(0, 10, $sr7_date, 0, 'L');
            // render PDF to browser
           // $pdf->Output();
            $pdf->Output('SH - 13.pdf', 'D');
            
            
    }

}
