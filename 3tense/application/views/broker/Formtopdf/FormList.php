<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Insurances');?>">Form To PDF</a></li>
                <li class="active">Form To PDF</li>
            </ol>
            <h1>Form To PDF</h1>

        </div>
        <div class="container ">
            <form action="#" id="client_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Form Detail</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Form</label>
                                <div class="col-md-6">
                                    <select name="select_form" class="populate form-control" id="select_form" style="width: 100%">
                                        <option selected value="">Select Form</option>
                                        <option value='1'>From ISR-1</option>
                                        <option value='2'>From ISR-2</option>
                                        <option value='4'>From ISR-4</option>
                                        <option value='6'>PRE-FILLED - FORM A - AFFIDAVIT - 100</option>
                                        <option value='5'>PRE-FILLED - FORM B - INDEMNITY - 300</option>
                                        <option value='7'>Form No. SH - 13</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-primary control-form" id="form_isr_1" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr1');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 1</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                     <div class="col-sm-12">
                                         <div class="form-group">
                                            <label class="col-sm-2 control-label">Adjustment</label>
                                            <div class="col-sm-10">
                                        
                                                    <input type="checkbox" id="IsPanCard" name="IsPanCard" value="true"> PAN &nbsp;&nbsp;
                                                    <input type="checkbox" id="IsSignture" name="IsSignture" value="true">Signature&nbsp;&nbsp;
                                                    <input type="checkbox" id="IsMobileNumber" name="IsMobileNumber" value="true"> Mobile Number&nbsp;&nbsp;
                                                    <input type="checkbox" id="IsBankdetail" name="IsBankdetail" value="true"> Bank Detail&nbsp;&nbsp;
                                                    <input type="checkbox" id="IsRegisterdAddress" name="IsRegisterdAddress" value="true"> Registered Address&nbsp;&nbsp;
                                                    <input type="checkbox" id="IsEmailAddress" name="IsEmailAddress" value="true"> Email address&nbsp;&nbsp;
                                                        
                                                
                                            </div>
                                        </div>
                                        </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr1_date" tabindex="6"  class="form-control date" data-inputmask="'alias':'date'" id="sr1_date">
                                            </div>
                                        </div>
                                        
                                            
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Company Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameofCompany" class="form-control" id="NameofCompany">
                                            </div>
                                        </div>
                                  
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Folio Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="FolioNumber" class="form-control" id="FolioNumber">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Face value</label>
                                            <div class="col-sm-8">
                                                
                                                <input type="text" name="facevalue" class="form-control" id="facevalue">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Number of Securities</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NoOfSecurity" class="form-control" id="NoOfSecurity" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Destinative Number of Securities From</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="DestinativeSecurityFrom" class="form-control" id="DestinativeSecurityFrom" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Destinative Number of Securities To</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="DestinativeSecurityTo" class="form-control" id="DestinativeSecurityTo" >
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Email Address</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="emailAddress" class="form-control" id="emailAddress">
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Mobile Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="mobilenumber" id="mobilenumber">
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder1</label>
                                            <div class="col-sm-8">
                                                <input id="NameofSecurityHolder1" name="NameofSecurityHolder1"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 1 Pancard</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="NameofSecurityHolderPancard1" id="NameofSecurityHolderPancard1">
                                            </div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Is Pan Linked With Addhar 1</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="IsPanLinkedWithAddhar1Yes" name="IsPanLinkedWithAddhar1" checked value="Yes">
                                                 <label for="html">Yes</label>
                                                 <input type="radio" id="IsPanLinkedWithAddhar1No" name="IsPanLinkedWithAddhar1" value="No">
                                                 <label for="css">No</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 2</label>
                                            <div class="col-sm-8">
                                                <input id="NameofSecurityHolder2" name="NameofSecurityHolder2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 2 Pancard</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="NameofSecurityHolderPancard2" id="NameofSecurityHolderPancard2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Is Pan Linked With Addhar 2</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="IsPanLinkedWithAddhar2Yes" name="IsPanLinkedWithAddhar2" checked value="Yes">
                                                 <label for="html">Yes</label>
                                                 <input type="radio" id="IsPanLinkedWithAddhar2No" name="IsPanLinkedWithAddhar2" value="No">
                                                 <label for="css">No</label>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 3</label>
                                            <div class="col-sm-8">
                                                <input id="NameofSecurityHolder3" name="NameofSecurityHolder3"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 3 Pancard</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="NameofSecurityHolderPancard3" id="NameofSecurityHolderPancard3">
                                            </div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Is Pan Linked With Addhar 3</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="IsPanLinkedWithAddhar3Yes" name="IsPanLinkedWithAddhar3" checked value="Yes">
                                                 <label for="html">Yes</label>
                                                 <input type="radio" id="IsPanLinkedWithAddhar3No" name="IsPanLinkedWithAddhar3" value="No">
                                                 <label for="css">No</label>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 4</label>
                                            <div class="col-sm-8">
                                                <input id="NameofSecurityHolder4" name="NameofSecurityHolder4"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Security Holder 4 Pancard</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="NameofSecurityHolderPancard4" id="NameofSecurityHolderPancard4">
                                            </div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Is Pan Linked With Addhar 4</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="IsPanLinkedWithAddhar4Yes" name="IsPanLinkedWithAddhar4" checked value="Yes">
                                                 <label for="html">Yes</label>
                                                 <input type="radio" id="IsPanLinkedWithAddhar4No" name="IsPanLinkedWithAddhar4" value="No">
                                                 <label for="css">No</label>
                                            </div>
                                        </div>                                            
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label"> Name Of Bank </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="NameOfBank" id="NameOfBank">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">BankBranch</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="BankBranch" id="BankBranch">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">IFSC Code</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="IFSCCode" id="IFSCCode">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Account Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="BankAccountNumber" id="BankAccountNumber">
                                            </div>
                                        </div>
                                        
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Acc Type</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="AccTypeSaving" name="AccType" value="1">
                                                 <label for="html">Saving</label>
                                                 <input type="radio" id="AccTypeCurrent" name="AccType" value="2">
                                                 <label for="css">Current</label>
                                                 <input type="radio" id="AccTypeNRO" name="AccType" value="3">
                                                 <label for="css">NRO</label>
                                                 <input type="radio" id="AccTypeNRE" name="AccType" value="4">
                                                 <label for="css">NRE</label>
                                                 <input type="radio" id="AccTypeOther" name="AccType" value="5">
                                                 <label for="css">OTHER</label>
                                            </div>
                                        </div> 
                                        </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">DementAccountNo</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="DementAccountNo" id="DementAccountNo">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">first Holder Name line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderNameline1" id="firstHolderNameline1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">first Holder Name line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderNameLine2" id="firstHolderNameLine2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">First Holder Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderAddressLine1" id="firstHolderAddressLine1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">First Holder Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderAddressLine2" id="firstHolderAddressLine2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">First Holder Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderAddressLine3" id="firstHolderAddressLine3">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">First Holder Pin Code</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="firstHolderPinCode" id="firstHolderPinCode">
                                            </div>
                                        </div>
                                       
                                       <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder Name line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderNameline1" id="SecondHolderNameline1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder Name Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderNameline2" id="SecondHolderNameline2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderAddressLine1" id="SecondHolderAddressLine1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder Address Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderAddressLine2" id="SecondHolderAddressLine2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder Address Line3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderAddressLine3" id="SecondHolderAddressLine3">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Second Holder PinCode</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="SecondHolderPinCode" id="SecondHolderPinCode">
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder Name line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderNameline1" id="thirdHolderNameline1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder Name Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderNameline2" id="thirdHolderNameline2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderAddressLine1" id="thirdHolderAddressLine1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder Address Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderAddressLine2" id="thirdHolderAddressLine2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder Address Line3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderAddressLine3" id="thirdHolderAddressLine3">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Third Holder PinCode</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="thirdHolderPinCode" id="thirdHolderPinCode">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">fourth Holder Name line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderNameline1" id="FourHolderNameline1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Fourth Holder Name Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderNameline2" id="FourHolderNameline2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Fourth Holder Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderAddressLine1" id="FourHolderAddressLine1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Fourth Holder Address Line2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderAddressLine2" id="FourHolderAddressLine2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Fourth Holder Address Line3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderAddressLine3" id="FourHolderAddressLine3">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Fourth Holder PinCode</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="FourHolderPinCode" id="FourHolderPinCode">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                       
                                    </div>
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            <div class="panel panel-primary control-form" id="form_isr_2" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr2');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 2</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                     
                                    <div class="col-sm-6">
                                        
                                        
                                            
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankName" class="form-control" id="BankName">
                                            </div>
                                        </div>
                                  
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Branch</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankBranch" class="form-control" id="BankBranch">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Address</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankAddress" class="form-control" id="BankAddress">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Phone Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankPhoneNumber" class="form-control" id="BankPhoneNumber" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Bank Email</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankEmail" class="form-control" id="BankEmail" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Bank Account No</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="BankAccountNo" class="form-control" id="BankAccountNo" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account Opening Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AccountOpeningDate" tabindex="6"  class="form-control date" data-inputmask="'alias':'date'" id="AccountOpeningDate">
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account Holder Name1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AccountHolderName1" class="form-control" id="AccountHolderName1">
                                            </div>
                                        </div>
                                        </div>
                                       <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account Holder Name2</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="AccountHolderName2" id="AccountHolderName2">
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account Holder Name3</label>
                                            <div class="col-sm-8">
                                                <input id="AccountHolderName3" type="text" name="AccountHolderName3"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">AddressLine1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="AddressLine1" id="AddressLine1">
                                            </div>
                                        </div>
                                      
                                      
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">AddressLine2</label>
                                            <div class="col-sm-8">
                                                <input id="AddressLine2" type="text" name="AddressLine2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">AddressLine3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="AddressLine3" id="AddressLine3">
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">PhoneNumber</label>
                                            <div class="col-sm-8">
                                                <input id="PhoneNumber" type="text" name="PhoneNumber"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Email Adress</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="EmailAdress" id="EmailAdress">
                                            </div>
                                        </div>
                                      
                                    </div>
                                    <div class="row">
                                       
                                    </div>
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            <div class="panel panel-primary control-form" id="form_isr_4" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr4');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 4</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr1_date" tabindex="6"  class="form-control date" data-inputmask="'alias':'date'" id="sr1_date">
                                            </div>
                                        </div>
                                        
                                            
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Dement Account Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="DementAccountNumber" class="form-control" id="DementAccountNumber">
                                            </div>
                                        </div>
                                  
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">PAN Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="PanNo" class="form-control" id="PanNo">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Issue Of Duplicate Certificate</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="IssueOfDuplicateCertificate" name="IssueOfDuplicateCertificate" value="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label"> Claim From Unclaim Suspense Account</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ClaimFromUnclaimSuspenseAccount" name="ClaimFromUnclaimSuspenseAccount" value="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Replacement / Renewal / Exchange Of Security Certificate</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ReplacementRenewalExchangeOfSecurityCertificate" name="ReplacementRenewalExchangeOfSecurityCertificate" value="true">
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Endorsement</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Endorsement" name="Endorsement" value="true">
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Sub-division / Slitting Of Security Certificate</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="SubdivisionSlittingOfSecurityCertificate" name="SubdivisionSlittingOfSecurityCertificate" value="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Consolidation Of Folios</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ConsolidationOfFolios" name="ConsolidationOfFolios" value="true">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Consolidation Of Security Certificate</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ConsolidationOfSecurityCertificate" name="ConsolidationOfSecurityCertificate" value="true">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Transmission</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Transmission" name="Transmission" value="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transposition</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Transposition" name="Transposition" value="true">
                                            </div>
                                        </div>
                                        
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Name Of The Issuer Company</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameOfTheIssuerCompany" class="form-control" id="NameOfTheIssuerCompany" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Folio Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="FolioNumber" class="form-control" id="FolioNumber" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Name Of Security Holder1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameOfSecurityHolder1" class="form-control" id="NameOfSecurityHolder1" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                           <label class="col-sm-4 control-label">Name Of Security Holder2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameOfSecurityHolder2" class="form-control" id="NameOfSecurityHolder2" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                           <label class="col-sm-4 control-label">Name Of Security Holder3</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameOfSecurityHolder3" class="form-control" id="NameOfSecurityHolder3" >
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Certificate Numbers</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="CertificateNumber" class="form-control" id="CertificateNumber">
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive Number</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="DistinctiveNumber" id="DistinctiveNumber">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Number And Face Value Of Securities</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="NumberAndFaceValueOfSecurities" id="NumberAndFaceValueOfSecurities">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Duplicate Securities Certificate</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="DuplicateSecuritiesCertificate" name="DuplicateSecuritiesCertificate" value="true">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label"> Claim From Unclaim Suspense Account(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ClaimFromUnclaimSuspenseAccount_1" name="ClaimFromUnclaimSuspenseAccount_1" value="true">
                                            </div>
                                        </div>
                                         </div>
                                       <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Replacement / Renewal / Exchange Of Security Certificate(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ReplacementRenewalExchangeOfSecurityCertificate_1" name="ReplacementRenewalExchangeOfSecurityCertificate_1" value="true">
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Endorsement(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Endorsement_1" name="Endorsement_1" value="true">
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Sub-division / Slitting Of Security Certificate(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="SubdivisionSlittingOfSecurityCertificate_1" name="SubdivisionSlittingOfSecurityCertificate_1" value="true">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Consolidation Of Security Certificate(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="ConsolidationOfSecurityCertificate_1" name="ConsolidationOfSecurityCertificate_1" value="true">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Transmission(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Transmission_1" name="Transmission_1" value="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transposition(second)</label>
                                            <div class="col-sm-8">
                                                <input type="checkbox" id="Transposition_1" name="Transposition_1" value="true">
                                            </div>
                                        </div>
                                        
                                        
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder1</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder1" name="SecurityHolder1"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 1 Address Line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder1AddressLine1" id="SecurityHolder1AddressLine1">
                                            </div>
                                        </div>
                                      
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 1 Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder1AddressLine2" type="text" name="SecurityHolder1AddressLine2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 1 Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder1AddressLine3" id="SecurityHolder1AddressLine3">
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 1 Pincode</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder1Pincode" type="text" name="SecurityHolder1Pincode"  class="form-control">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder2</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder2" name="SecurityHolder2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 2 Address Line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder2AddressLine1" id="SecurityHolder2AddressLine1">
                                            </div>
                                        </div>
                                      
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 2 Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder2AddressLine2" type="text" name="SecurityHolder2AddressLine2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 2 Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder2AddressLine3" id="SecurityHolder2AddressLine3">
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 2 Pincode</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder2Pincode" type="text" name="SecurityHolder2Pincode"  class="form-control">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder3</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder3" name="SecurityHolder3"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 3 Address Line1</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder3AddressLine1" id="SecurityHolder3AddressLine1">
                                            </div>
                                        </div>
                                      
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 3 Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder3AddressLine2" type="text" name="SecurityHolder3AddressLine2"  class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 3 Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="SecurityHolder3AddressLine3" id="SecurityHolder3AddressLine3">
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Security Holder 3 Pincode</label>
                                            <div class="col-sm-8">
                                                <input id="SecurityHolder3Pincode" type="text" name="SecurityHolder3Pincode"  class="form-control">
                                            </div>
                                        </div>
                                        
                                    </div>
                                   
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            <div class="panel panel-primary control-form" id="form_isr_5" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr5');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 4</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        
                                            
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Name5" class="form-control" id="Name5">
                                            </div>
                                        </div>
                                  
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Son/Daugher/Spouse</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="son5" class="form-control" id="son5">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine15" name="AddressLine15"  class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine25" name="AddressLine25" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AccountNo5" name="AccountNo5" class="form-control" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Securities Under</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="SecuritiesUnder5" name="SecuritiesUnder5" class="form-control" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Folio No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="FolioNo5" name="FolioNo5" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Certificate No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="CertificateNo5" name="CertificateNo5" class="form-control" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive From</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveFrom5" name="DistinctiveFrom5" class="form-control" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive To</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveTo5" name="DistinctiveTo5" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">No of Securities Hold</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="NoofSecuritiesHold5" name="NoofSecuritiesHold5" class="form-control"  >
                                            </div>
                                        </div>
                                         </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Signature Name 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="SignatureName15" class="form-control" id="SignatureName15" >
                                            </div>
                                        </div>
                                       
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Signature Address 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="SignatureAddress15" class="form-control" id="SignatureAddress15" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Signature Name 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="SignatureName25" class="form-control" id="SignatureName25" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Signature Address 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="SignatureAddress25" class="form-control" id="SignatureAddress25" >
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                           <label class="col-sm-4 control-label">Address First Holder Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AddressFirstHolderLine1" class="form-control" id="AddressFirstHolderLine1" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                           <label class="col-sm-4 control-label">Address First Holder Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AddressFirstHolderLine2" class="form-control" id="AddressFirstHolderLine2" >
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address First Holder Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AddressFirstHolderLine3" class="form-control" id="AddressFirstHolderLine3">
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Pincode</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="Pincode5" id="Pincode5">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Tel. No</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  name="TelNo5" id="TelNo5">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Email5" name="Email5" class="form-control" >
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr5_date"  class="form-control date" data-inputmask="'alias':'date'" id="sr5_date">
                                            </div>
                                        </div>
                                        
                                         
                                       
                                        
                                    </div>
                                   
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            <div class="panel panel-primary control-form" id="form_isr_6" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr6');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 4</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        
                                            
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Name6" class="form-control" id="Name6">
                                            </div>
                                        </div>
                                  
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Son/Daugher/Spouse</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="son6" class="form-control" id="son6">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line z</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine16" name="AddressLine16"  class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine26" name="AddressLine26" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Account No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AccountNo6" name="AccountNo6" class="form-control" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Securities Under</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="SecuritiesUnder6" name="SecuritiesUnder6" class="form-control" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Folio No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="FolioNo6" name="FolioNo6" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Certificate No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="CertificateNo6" name="CertificateNo6" class="form-control" >
                                            </div>
                                        </div>
                                         </div>
                                       <div class="col-sm-6">
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive From</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveFrom6" name="DistinctiveFrom6" class="form-control" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive To</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveTo6" name="DistinctiveTo6" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">No of Securities Hold</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="NoofSecuritiesHold6" name="NoofSecuritiesHold6" class="form-control"  >
                                            </div>
                                        </div>
                                        
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Deponent</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Deponent6" class="form-control" id="Deponent6" >
                                            </div>
                                        </div>
                                       
                                        
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Solemnly Affirmed at</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="SolemnlyAffirmedAt6" class="form-control" id="SolemnlyAffirmedAt6" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Place</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Place6" class="form-control" id="Place6" >
                                            </div>
                                        </div>
                                        
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr6_date"  class="form-control date" data-inputmask="'alias':'date'" id="sr6_date">
                                            </div>
                                        </div>
                                        
                                         
                                       
                                        
                                    </div>
                                   
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            <div class="panel panel-primary control-form" id="form_isr_7" style="display:none">
                <div class="tab-content">
                    <form action="<?php echo site_url('broker/Formtopdf/formisr7');?>" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="tab-pane active no-border" id="insurance">
                        
                        <div class="panel panel-midnightblue">
                            
                                <div class="panel-heading">
                                    <h4>Form ISR - 4</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr7_date"  class="form-control date" data-inputmask="'alias':'date'" id="sr7_date">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Companyname7" class="form-control" id="Companyname7">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine17" name="AddressLine17"  class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="AddressLine27" name="AddressLine27" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Nature Of Securities</label>
                                            <div class="col-sm-8">
                                                <input type="radio" id="NatureOfSecuritiesE" name="NatureOfSecurities" checked value="E">
                                                 <label for="html">Equity</label>
                                                 <input type="radio" id="NatureOfSecuritiesD" name="NatureOfSecurities" value="D">
                                                 <label for="css">Debs</label>
                                                 <input type="radio" id="NatureOfSecuritiesB" name="NatureOfSecurities" value="B">
                                                 <label for="css">Bounds</label>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Folio No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="FolioNo7" name="FolioNo7" class="form-control" >
                                            </div>
                                        </div>
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">No of Securities</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="NoOfSecurities7" name="NoOfSecurities7" class="form-control" >
                                            </div>
                                        </div>
                                           
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Certificate No</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="CertificateNo7" name="CertificateNo7" class="form-control" >
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive From</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveFrom7" name="DistinctiveFrom7" class="form-control" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Distinctive To</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="DistinctiveTo7" name="DistinctiveTo7" class="form-control" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Nominee</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="NameofNominee7" name="NameofNominee7" class="form-control"  >
                                            </div>
                                        </div>
                                        
                                          <div class="form-group">
                                            <label class="col-sm-4 control-label">Nomiee Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NomineeAddressLine17" class="form-control" id="NomineeAddressLine17" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Nomiee Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NomineeAddressLine27" class="form-control" id="NomineeAddressLine27" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Nomiee Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NomineeAddressLine37" class="form-control" id="NomineeAddressLine37" >
                                            </div>
                                        </div>
                                       
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Date of birth</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr7_date_of_birth"  class="form-control date" data-inputmask="'alias':'date'" id="sr7_date_of_birth">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                           <label class="col-sm-4 control-label">Father Name/Mother Name/Sponue's Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="FatherName7" class="form-control" id="FatherName7" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Occupation</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="occupation7" class="form-control" id="occupation7" >
                                            </div>
                                        </div>
                                         </div>
                                       <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Relationship with the security holder</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Relationship7" class="form-control" id="Relationship7" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Nationality</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="Nationality7" class="form-control" id="Nationality7" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Email Id</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="EmailId7" class="form-control" id="EmailId7" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Mobile No</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="MobileNo7" class="form-control" id="MobileNo7" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of guardian</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameofGuardian7" class="form-control" id="NameofGuardian7" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Date of Guardian</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr6_date_of_guadian"  class="form-control date" data-inputmask="'alias':'date'" id="sr6_date_of_guadian">
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Address of guardian</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="AddressofGuardian7" class="form-control" id="AddressofGuardian7" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Date of Attaining Majority</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sr6_date_of_Attaining7"  class="form-control date" data-inputmask="'alias':'date'" id="sr6_date_of_Attaining7">
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">First Holder Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="FirstHolderName7" class="form-control" id="FirstHolderName7" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Joint Holder 1 Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="JointHolder1Name7" class="form-control" id="JointHolder1Name7" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Joint Holder 2 Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="JointHolder2Name7" class="form-control" id="JointHolder2Name7" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Joint Holder 3 Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="JointHolder3Name7" class="form-control" id="JointHolder3Name7" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Name of Witness</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="NameofWitness7" class="form-control" id="NameofWitness7" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 control-label">Witness Address Line 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="WitnessAddressLine17" class="form-control" id="WitnessAddressLine17" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Witness Address Line 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="WitnessAddressLine27" class="form-control" id="WitnessAddressLine27" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Witness Address Line 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="WitnessAddressLine37" class="form-control" id="WitnessAddressLine37" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Witness Pincoce</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="WitnessPincode7" class="form-control" id="WitnessPincode7" >
                                            </div>
                                        </div>
                                       
                                         
                                       
                                        
                                    </div>
                                   
                                </div>
                           
                        </div>
                        <div class="bottom-row navbar-fixed-bottom">
                            <div class="col-sm-12 bottom-col">
                                <button type="submit" id="save"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                     </form>
                    <div class="tab-pane" id="premium">Lorem ...</div>
                    <div class="tab-pane" id="fund">Lorem ...</div>
                </div>
            </div>
            
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">
var save_method; //for save method string
 $(function() {
        //initialize tooltip
        $('[data-toggle="tooltip"]').tooltip();
        //initialize select2
     
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
       
       
        $('#select_form').change(function()
        {
            $('#form_isr_1').css('display','none');
            $('#form_isr_2').css('display','none');
            $('#form_isr_4').css('display','none');
            $('#form_isr_5').css('display','none');
            $('#form_isr_6').css('display','none');
            $('#form_isr_7').css('display','none');
            if($('#select_form').val()==1)
            {
                $('#form_isr_1').css('display','block');
            }
            if($('#select_form').val()==2)
            {
                $('#form_isr_2').css('display','block');
            }
            if($('#select_form').val()==4)
            {
                $('#form_isr_4').css('display','block');
            }
            if($('#select_form').val()==5)
            {
                $('#form_isr_5').css('display','block');
            }
            if($('#select_form').val()==6)
            {
                $('#form_isr_6').css('display','block');
            }
            if($('#select_form').val()==7)
            {
                $('#form_isr_7').css('display','block');
            }
            
        });
    });


//insert insurance details in database
function ins_submit()
{
    $.ajax({
            url: '<?php echo site_url('broker/Formtopdf/createpdf');?>',
            type: 'post',
            data: $('#client_form, #ins_form').serialize(),
            dataType: 'json',
            success:function(data)
            {
                if(data['status'] == 'success')
                {
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'success',
                        hide: true
                    });
                    enableBtn();
                }
                else
                {
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'error',
                        hide: true
                    });
                }
            },
            error:function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                bootbox.alert("Something went terribly wrong");
            }
        });
}

</script>