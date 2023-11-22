

<?php include "header-focused.php"; ?>
<br/><br/>
<style>
    #tblschemelist_length
    {
    display:none;   
    }
   

html {
    height: 100%
}

#grad1 {
    background-color: : #9C27B0;
    background-image: linear-gradient(120deg, #FF4081, #81D4FA)
}

#msform {
    text-align: center;
    position: relative;
    margin-top: 20px
}

#msform fieldset .form-card {
    background: white;
    border: 0 none;
    border-radius: 0px;
    box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
    padding: 20px 40px 30px 40px;
    box-sizing: border-box;
    
    margin: 0 1% 20px 1%;
    position: relative
}

#msform fieldset {
    
    border: 0 none;
    border-radius: 0.5rem;
    box-sizing: border-box;
    width: 100%;
    margin: 0;
    padding-bottom: 20px;
    position: relative
}

#msform fieldset:not(:first-of-type) {
    display: none
}

#msform fieldset .form-card {
    text-align: left;
    color: #9E9E9E
}

#msform input,
#msform textarea {
    padding: 5px 8px 4px 8px;
    border: none;
    
    margin-top: 2px;
    width: 100%;
    box-sizing: border-box;
    
    color: #2C3E50;
    font-size: 16px;
    letter-spacing: 1px
}

#msform input:focus,
#msform textarea:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: none;
    font-weight: bold;
    
    outline-width: 0
}

#msform .action-button {
    width: 100px;
    background: #fbb12f;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 0px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px
}

#msform .nextaccount{
    background: #fbb12f;
}
#msform .nextscheme{
    background: #0099d9;
    
}

#msform .nextverifyscheme{
    background: #ed3337;
    
}


#msform .action-button:hover,
#msform .action-button:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px skyblue
}

#msform .action-button-previous {
    width: 100px;
    background: #616161;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 0px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px
}


#msform .action-button-previous:hover,
#msform .action-button-previous:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #616161
}

select.list-dt {
    border: none;
    outline: 0;
    border-bottom: 1px solid #ccc;
    padding: 2px 5px 3px 5px;
    margin: 2px
}

select.list-dt:focus {
    border-bottom: 2px solid #fbb337
}

.card {
    z-index: 0;
    border: none;
    border-radius: 0.5rem;
    position: relative
}

.fs-title {
    font-size: 25px;
    color: #2C3E50;
    margin-bottom: 10px;
    font-weight: bold;
    text-align: left
}

#progressbar {
    margin: 23px 43px 19px -1px;
    overflow: hidden;
    color: lightgrey
}

#progressbar .active {
    color: #fbb12f;
}

#progressbar #BscAccount.active{
    color: #fbb12f;
}
#progressbar #personal.active {
    color: #0099d9;
}

#progressbar #confirm.active{
    color: #ed3337;
}

#progressbar li {
    list-style-type: none;
    font-size: 12px;
    width: 25%;
    float: left;
    position: relative
}

#progressbar #BscAccount:before {
    font-family: FontAwesome;
    content: "\f023"
}

#progressbar #personal:before {
    font-family: FontAwesome;
    content: "\f007"
}

#progressbar #payment:before {
    font-family: FontAwesome;
    content: "\f09d"
}

#progressbar #confirm:before {
    font-family: FontAwesome;
    content: "\f00c"
}

#progressbar li:before {
    width: 50px;
    height: 50px;
    line-height: 45px;
    display: block;
    font-size: 18px;
    color: #ffffff;
    background: lightgray;
    border-radius: 50%;
    margin: 0 auto 10px auto;
    padding: 2px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 2px;
    background: lightgray;
    position: absolute;
    left: 0;
    top: 25px;
    z-index: -1
}


#progressbar li.active:before,
#progressbar li.active:after {
    background: #fbb12f;
}
#progressbar #BscAccount.active:before,
#progressbar #BscAccount.active:after {
    background: #fbb12f;
}
#progressbar #personal.active:before,
#progressbar #personal.active:after {
    background: #0099d9;
}

#progressbar #confirm.active:before,
#progressbar #confirm.active:after {
    background: #ed3337;
}


.radio-group {
    position: relative;
    margin-bottom: 25px
}

.radio {
    display: inline-block;
    width: 204;
    height: 104;
    border-radius: 0;
    background: lightblue;
    box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
    cursor: pointer;
    margin: 8px 2px
}

.radio:hover {
    box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.3)
}

.radio.selected {
    box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1)
}

.fit-image {
    width: 100%;
    object-fit: cover
}
.label
{
    
}
table th,table td
{
    color:black;
    background:white;
}

</style>

<div class="container body">
    <!-- MultiStep Form -->

                        
                
    <div  class="row right_col" role="main" style="margin-top:15px;padding:10px 10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center p-0 mt-3 mb-2">
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4  >SIP Purchase <a class="btn btn-default" href="<?php echo site_url('client/Purchase')?>"  style='float:right;margin-top:-7px;'>Back</a></h4>
                    
                    

                </div>
                  </div>
            
                <div class="row">
                    <div class="col-md-12 mx-0">
                        <form id="msform">
                            <!-- progressbar -->
                            <ul id="progressbar">
                                <li class="active" id="BscAccount"><strong>Select Account</strong></li>
                                <li id="personal"><strong>Select Scheme</strong></li>
                                <li id="confirm"><strong>Verify Scheme</strong></li>
                                <li id="payment"><strong>Make Payment</strong></li>
                            </ul> <!-- fieldsets -->
                            <fieldset>
                                <div class="form-card">
                                    <h2 class="fs-title">Account Information</h2> 
                                      
                                        <select name="account" class=" form-control" multiple="multiple" id="account"  style="width: 100%"  tabindex="1">
                                        <?php foreach($account_list as $row):?>
                                            <option data-val='<?php echo json_encode($row) ?>' value='<?php echo $row->CLIENTCODE; ?>'><?php echo $row->FIRSTAPPLICANTNAME1; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                        <div id="AccountDetail"></div>
                                    
                                </div> 
                                <input type="button" name="next" class="next action-button nextaccount"  value="Next Step" />
                            </fieldset>
                            <fieldset>
                                <div class="form-card">
                                    <h2 class="fs-title">Select Scheme</h2> 
                                     <div class="row">
                                        
                                        <div class="form-group col-md-3">
                                            <label>Scheme Type</label>
                                            <select name="schemetype" class=" form-control" multiple="multiple" id="schemetype" style="width: 100%" tabindex="3">
                                                <?php foreach($bsc_schmeme_Type_list as $row):?>
                                                    <option value='<?php echo $row->SchemeType; ?>'><?php echo $row->SchemeType; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Select AMC</label>
                                            <select name="amc" class=" form-control" multiple="multiple" id="amc" style="width: 100%" tabindex="3">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">    
                                            <label >Scheme Name</label>
                                            <input type="hidden" id="schemename" style="width:100%" class="input-xlarge" />
                                       
                                        </div>
                                        <div class="form-group col-md-3">    
                                            <label >Folio Number</label>
                                            <select name="folionumber" class=" form-control"  id="folionumber" style="width: 100%" tabindex="3">
                                            </select>

                                        </div>
                                    </div>
                                </div> 
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" /> 
                                <input type="button" name="next" class="next action-button nextscheme" value="Next Step" />
                            </fieldset>
                            <fieldset>
                                <div class="form-card">
                                    <h2 class="fs-title">Verify Scheme</h2>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table id="tblschemelist" class="display table table-striped table-bordered table-full-width dataTable" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th style='width: 50%;color:white;background:#1b2845'>Scheme Name</th>
                                                        <th style='width: 20%;color:white;background:#1b2845'>Scheme Type</th>
                                                        <th style='color:white;background:#1b2845'>Amount</td>
                                                    
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>SIP Start Date</label>
                                            <input type="date" id="SIPDate" class="form-control" name="SIPDate" style="padding: 0px 10px 0px 10px;width: 95%;border: 3px solid #9e9e9e;height: 50px;">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>FREQUENCY TYPE</label>
                                              <select class="form-control" name="frequency" id="frequency" style="padding: 0px 10px 0px 10px;width: 95%;border: 3px solid #9e9e9e;height: 50px;">
                                                <option value="MONTHLY">MONTHLY</option>
                                                <option value="QUARTELY">QUARTELY</option>
                                                <option value="WEEKLY">WEEKLY</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Select Bank </label>
                                            
                                         
                                            <select class="form-control" name="bank_id" id="bank_id" style="padding: 0px 10px 0px 10px;width: 95%;border: 3px solid #9e9e9e;height: 50px;">
                                                <option value="">Select Bank</option>
                                            </select>
                                              
                                        </div>
                                    </div><Br>
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-1" style="width:25px">
                                            <input type="checkbox" name="purchaseconsent" id="purchaseconsent" style="width:unset;" />    
                                            </div>
                                            <div class="col-md-11" style="color:black"> I Agree to 
                                                <a style="color: blue;text-decoration: underline;cursor: pointer;" onclick="teamscondition()">Terms & Conditions</a>
                                                <br/><span class="termsandcondition_error error_msg hide">Terms & conditions is required.</span>
                                            </div>
                                        </div>
                                    </div><Br>
                                  
                                </div> 
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" /> 
                                <input type="button" name="next" class="next action-button nextverifyscheme" value="Confirm" />
                            </fieldset>
                            
                        </form>
                    </div>
              
        </div>
    </div>
    </div>
          </div>
  </div>  
<div id="TeamAndCondition" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Terms & Conditions</h4>
            </div>
            <div class="modal-body">
                <p>Mutual fund investments are subject to market risks, read all scheme related documents carefully before investing. Past performance may or may not be sustained in future and should not be used as a basis for investments. The use of this website is at your sole risk. We shall be not responsible or liable for any loss, shortfall, tax, cess, damages, AMC charges or fees, ECS return charges, ECS processing charges, bank charges etc incurred by you due to any reasons including but not limited to delay in processing of orders, orders not being processed or rejected, any mistake or omission on part of the RTA, AMC, Bank or Exchange.</p>
                <p>For transactions through Isolutions/BSE Star mode, redemption amount will be credited to your bank account. For transactions through RTA mode, redemption amount will be credited to your bank account through fund transfer or will be sent by cheque at your correspondence address as per AMC records. ELSS (Equity Linked Tax Saving Schemes) have a lock in period of 3 years and would be eligible for Tax benefits as per the prevailing IT laws. Redemption of ELSS or closed ended fund is subject to lock-in period.</p>
                <br>
                <p>Cut off time for all online RTA transactions is 3 pm.</p>
                <p>Cut off time for Liquid Funds in RTA Mode will be 1.30 pm. </p>
                <p>For NAV consideration, day when the funds reach AMC will be considered. </p>
                <Br>
                <p>TERMS AND CONDITIONS FOR INVESTING IN ONLINE MUTUAL FUNDS.</p>
                <p>The terms and conditions for online investing in Mutual Funds given below are in addition to and binding on the existing Power of Attorney and the Client Agreement that has been signed by you at the time of your becoming our client.</p>
                <p>Definitions:</p>
                <p>
                <ol>
                  <li>"UNITS" shall mean the interest of the investor in an undivided share in the net assets of a Scheme of the Fund.</li>
                  <li>"NAV" shall mean the net asset value of the Units of the Scheme and the plans and options therein, calculated on every business day, in the manner provided in the Offer Document of the respective schemes or as may be prescribed by SEBI Regulations from time to time.</li>
                  <li>"PURCHASE" shall mean subscription to the Units of any of the Schemes of the Mutual Fund.</li>
                  <li>"REDEMPTION" shall mean sale of the units of the various schemes of the Mutual Fund.</li>
                  <li>"SWITCH" shall mean an option to the investor to shift his existing investment in any of the schemes of the Mutual Fund to another scheme of the same Mutual Fund.</li>
                  <li>"SYSTEMATIC INVESTMENT PLAN (SIP)" "Systematic Investment Plan (SIP)" shall mean an option available with the Customer for investing, at a specified frequency, in a specified Scheme of the Fund, a fixed amount of Rupees for purchasing additional units at the applicable NAV on a specified date, provided that the provisions of the Offer Document of the respective Scheme shall always be applicable for SIP transactions.</li>
                  <li>"SYSTEMATIC WITHDRAWAL PLAN (SWP)" shall mean an option available with the customer who holds Units to withdraw a predetermined amount or a variable amount subject to deduction of tax, if any, at a specified frequency, from a specified Scheme of the Fund at a specified period thereby reducing the unit balance to his credit by the units so withdrawn at the applicable NAV on the specified date, provided that the provisions of the Offer Document of the respective scheme shall always be applicable for SWP transactions.</li>
                  <li>"SYSTEMATIC TRANSFER PLAN (STP)" shall mean an option available with the customer who holds Units to transfer a predetermined amount or a variable amount subject to deduction of tax, if any, at a specified frequency, from a specified Scheme of the Fund to another specific scheme of the Fund at a specified period at a specified frequency at the applicable NAV on a specified date, provided that the provisions of the Offer Document of the respective Schemes shall always be applicable for STP transactions.</li>
                  <li>"LOAD" shall mean a charge, which the Asset Management Company (AMC) may collect on entry and/or exit from a scheme.</li>
                  <li>The client is requested to check up the personal and bank related details provided by him/her. Neither I-Solutions nor any of the Mutual Funds chosen shall accept any liability which may arise as a consequence of the erroneous information provided by the client.</li>
                  <li>The units of the schemes shall be allotted, redeemed or switched, as the case may be, at the Net Asset Value (NAV) prevalent on the date of the application on a best effort basis, if the application for purchase, redemption or switch is received by I-Solutions before the cut-off time as specified on the website and consistent with the terms of the Scheme. However, I-Solutions undertakes to provide NAV of T+7 days to all of its customers. I-Solutions shall not be liable for any loss that may arise to the client as a result of the correct NAV not being allotted to the client's transactions on the website.</li>
                  <li>Any request falling due on a holiday would be processed on the next business day and respective NAV would be applicable as per the Mutual Fund's offer document.</li>
                  <li>No offline transaction requests will be entertained by I-Solutions or the concerned Mutual Fund or their respective registrars in respect of the units allotted through I-Solutions's online portal.</li>
                  <li>The client agrees and understands that in case he is to close his broking account, he shall have to either redeem or convert to physical units for all his mutual fund holdings. The client agrees that I-Solutions shall not be responsible for any loss or implication for in such cases. In case the client fails to do so and thereby cannot transact in such units not converted or redeemed after his account is closed, the client understands that I-Solutions shall not be liable.</li>
                  <li>In the case of Systematic Investment Plan, in the absence of sufficient allocated funds, unallocated funds to the extent available shall also be utilised. In the absence of sufficient funds, both allocated and unallocated, no investment shall be made for that particular period.
                    <ol style="list-style-type:lower-alpha">
                        <li>The customer confirms that he/she/they is/are aware that for Flexi- SIP's /SIP's he/she/they need to transfer funds before the cut-off time specified by I-Solutions in the website against each scheme. I-Solutions will not forward the orders to the AMC for processing in case sufficient amount is not available in the pool account before the cut-off time.</li>
                        <li>The SIP (as per the dates specified by different AMC's) orders will be forwarded/executed only subject to the acceptance of the ECS arrangement made by I-Solutions with the bankers.</li>
                    </ol>
                  </li>
                  <li>For change of address or any personal details of the client, the client shall send "Master Changes Form" to I-Solutions duly signed by all the co-holders.</li>
                  <li>The details of transactions made and the allotment details will be provided in the website (order details / portfolio) No physical statements will be sent to the clients either by I-Solutions or by AMC.</li>
                  <li>The Customer Service Dept of I-Solutions shall handle all Mutual Fund related queries of the clients.</li>
                  <li>The client undertakes to read all the relevant offer documents and addendum(S) / regulatory clauses published by regulatory authorities and understand the terms and conditions of all schemes of all mutual funds offered through I-Solutions, BEFORE entering into any transactions through I-Solutions.</li>
                  <li>The Customer(s) have understood the details of the Scheme and have not received nor been induced by any rebate or gifts, directly or indirectly, in making this investment.</li>
                  <li>The Customer(s) confirm that the details provided by the Customer(s) are true and correct.</li>
                  
                  <li>The Customer(s) hereby declare that the amount being invested by the Customer(s) in the Scheme(s) of all mutual funds is derived through legitimate sources and is not held or designed for the purpose of contravention of any Act, Rules, Regulations or any statute or legislation or any other applicable laws or any Notifications, Directions issued by any governmental or statutory authority from time to time.</li>
                  <li>The Customer(s) confirm that in the event the Customer(s) have mentioned "Not Applicable" against Permanent Account Number (PAN), such Customer(s) are not required to obtain a PAN under the provisions of the Income Tax Act, 1961.</li>
                  <li>It is explicitly stated herein that the Mutual Fund schemes offered online, have not been recommended by I-Solutions, nor have been sponsored by it, or its affiliates or its sponsors.</li>
                  <li>Neither I-Solutions, nor any of the Mutual Funds shall be liable for any failure to perform its obligations, to the extent that such performance has been delayed, hindered or prevented by systems failures, network errors, delay or loss of data due to the above and in circumstances of acts of God, floods, epidemics, quarantine, riot or civil commotion and war.</li>
                  <li>I-Solutions shall provide its services on a best effort basis. However I-Solutions shall not be liable for failure of the system or for any loss, damage or other costs arising in any way out of:
                  <ol style="list-style-type:lower-alpha">
                        <li>System failure including failure of ancillary or associated systems, or fluctuation of power, or other environmental conditions; or</li>
                        <li>Accident, transportation, neglect, misuse, errors, frauds of the clients or agents or any third party; or</li>
                        <li>Any fault in any attachments or associated equipment of the clients; or</li>
                        <li>Any incidental, special or consequential damages including without limitation of loss of profit.</li>
                    </ol>
                    </li>
                  <li>For all correspondences with I-Solutions, the client shall quote his e-broking account number / Customer ID.</li>
                  <li>I/We agree not to approach the AMC/registrar directly for any transaction or request or services under the folio created under solely POA mode.</li>
                  <li>The customer confirm that he/she/they is/are aware that the funds from his/her/their account will first move into BSE pool account and later the same will be remitted to the AMC collection accounts whenever the customer makes investment in to the schemes offered online,</li>
                  <li>In case, any purchase order is rejected by the AMC due to any reason, the amount will be refunded by the AMC to the customer’s bank account immediately.</li>
                  <li>The request for change of broker codes from a non-I-Solutions broker to I-Solutions online brokercode will be entertained by I-Solutions only when the following condition are met and provided the schemes of the AMC are available in the online portal of I-Solutions.
                  <ol style="list-style-type:lower-alpha">
                        <li>The pattern of holdings should be same i.e if in I-Solutions the customer is holding in single name the folio under request for broker code change also must have holdings in single mode.</li>
                        <li>The Status of the individual should be same.( ie, Resident Individual, NRI, HUF etc )</li>
                        <li>Signature of the customer and Bank details in online records should be the same.</li>
                        <li>The customer must give AMC wise requests in original for broker code changes</li>
                        <li>Copy of account statement should be provided with the request.</li>
                    </ol>
                  </li>
                  <li>The customer is aware that the mutual fund units will be allotted in the same name of the customer as it appears in his BSE Client Code with I-Solutions. However the customer may approach I-Solutions for appointing a nominee. The Nominee will be the same for all the mutual fund units he/she is holding under the Client Code given by I-Solutions irrespective of the AMC ( Mutual Fund Companies)</li>
                  <li>Any change in nomination, will have the effect on all the schemes applied through I-Solutions BSE Client Code. The customer is aware that he will not be able to nominate different individuals for different mutual fund schemes subscribed through I-Solutions BSE Client Code</li>
                  <li>The customer is also aware that his investment details will be registered with the AMCs/registrars as per the customer profile displayed in the website under online mutual fund investment section</li>
                  <li>In case any client is desirous of obtaining the client code of BSE, he may register with I-Solutions on fulfilling the following conditions
                    <ol style="list-style-type:lower-alpha">
                        <li>The holding is in single name as it appears in the trading account of the customer with I-Solutions.</li>
                        <li>The Status of the individual should be same. ie, Resident Individual, NRI, HUF et</li>
                        <li>Signature of the customer and Bank details in online records should be the same.</li>
                        <li>The customer must give AMC wise requests in original for broker code changes.</li>
                        <li>Copy of account statement should be provided with the request.</li>
                        <li>The AMC for which the customer had subscribed is/ are available on the online portal of I-Solutions.</li>
                    </ol>
                    </li>
                    <li>The customer confirms that he has read the disclosures on commissions (including trail commissions) payable to I-Solutions for various categories of mutual funds schemes.</li>
                </ol>
                </p>
                <p></p>I/we have gone through the offer document/key information memorandum / regulatory clauses published by regulatory authorities before deciding to make this investment in the mutual fund scheme.</p>
                <p>I-Solutions reserves the right to change the terms and conditions without any prior notice.</p>
                <p>Copyright © 2021 I-Solutions All Rights Reserved.</p>
            </div>
            <div class="modal-footer">
                <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    


<?php echo form_open('PurchaseMutualFund/MFPurchaseDetail'); ?>
<input type="hidden" name="id" id="id">
<input type="submit" id="purchasesubmit" style="display:none">

<?php echo form_close(); ?>
<script type="application/javascript">
     var banklist=[
                { id:"ACB",name:'ABHYUDAYA COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"ABPB",name:'ADITYA BIRLA IDEA PAYMENTS BANK LTD',isdirect:0},
                {id:"AIRP",name:'AIRTEL PAYMENTS BANK LIMITED',isdirect:0},
                {id:"ALD" ,name :'ALLAHABAD BANK',isdirect:0},
                {id:"ALB",name :'Allahabad Bank - Retail Net Banking',isdirect:0},
                {id:"ALC",name :'ALLAHABAD BANK CORPORATE',isdirect:0},
                {id:"ADB",name :'Andhra Bank',isdirect:0},
                {id:"APGX",name :'ANDHRA PRADESH GRAMEENA VIKAS BANK',isdirect:0},
                {id:"APG",name :'ANDHRA PRAGATHI GRAMEENA BANK',isdirect:0},
                {id:"ASB",name :'APNA SAHAKARI BANK LIMITED',isdirect:0},
                {id:"AUB",name :'AU SMALL FINANCE BANK',isdirect:0},
                {id:"UTI",name :'Axis Bank',isdirect:1},
                {id:"BDB",name :'BANDHAN BANK LIMITED',isdirect:0},
                {id:"BBC",name :'Bank of Baroda - Corporate Banking',isdirect:0},
                {id:"BBR",name :'Bank of Baroda - Retail Net Banking',isdirect:0},
                {id:"BOI",name :'Bank Of India',isdirect:0},
                {id:"BOM",name :'Bank of Maharashtra',isdirect:0},
                {id:"BAC",name :'BASSEIN CATHOLIC COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"CNB",name :'Canara Bank',isdirect:0},
                {id:"CSB",name :'Catholic Syrian Bank',isdirect:0},
                {id:"CBI",name :'Central Bank of India',isdirect:0},
                {id:"CIT",name :'CITI BANK' ,isdirect:0},
                {id:"CUB",name :'CITY UNION BANK LIMITED',isdirect:0},
                {id:"CRP",name :'Corporation Bank',isdirect:0},
                {id:"DCB",name :'DCB BANK LIMITED',isdirect:0},
                {id:"DEN",name :'Dena Bank',isdirect:0},
                {id:"DBS",name :'DEVELOPMENT BANK OF SINGAPORE',isdirect:0},
                {id:"DLB",name :'Dhanlakshmi Bank',isdirect:0},
                {id:"DNS",name :'DOMBIVLI NAGARI SAHAKARI BANK LIMITED',isdirect:0},
                {id:"EQB",name :'Equitas Small Finance Bank Ltd',isdirect:0},
                {id:"ESF",name :'Equitas Small Finance Bank Ltd',isdirect:0},
                {id:"FBK",name :'Federal Bank',isdirect:0},
                {id:"FINO",name :'FINO PAYMENTS BANK LTD',isdirect:0},
                {id:"PJS",name :'G P PARSIK BANK',isdirect:0},
                {id:"HDF",name :'HDFC BANK',isdirect:1},
                {id:"HSB",name :'HSBC BANK',isdirect:0},
                {id:"ICI",name :'ICICI BANK LIMITED',isdirect:1},
                {id:"IDB",name :'IDBI Bank - Retail Net Banking',isdirect:1},
                {id:"IDF",name :'IDFC BANK LIMITED',isdirect:0},
                {id:"INB",name :'Indian Bank',isdirect:0},
                {id:"IOB",name :'Indian Overseas Bank',isdirect:0},
                {id:"IDS",name :'IndusInd Bank',isdirect:0},
                {id:"JJS",name :'JALGAON JANATA SAHAKARI BANK LIMITED',isdirect:0},
                {id:"JKB",name :'Jammu & Kashmir Bank',isdirect:0},
                {id:"JSB",name :'Janata Sahakari Bank',isdirect:0},
                {id:"JIOP",name :'JIO PAYMENTS BANK LIMITED',isdirect:0},
                {id:"KAI",name :'KALLAPPANNA AWADE ICHALKARANJI JANATA SAHAKARI BANK LIMITED',isdirect:0},
                {id:"KJS",name :'KALYAN JANATA SAHAKARI BANK',isdirect:0},
                {id:"KCB",name :'KAPOL COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"KBL",name :'Karnataka Bank Ltd',isdirect:0},
                {id:"KVG",name :'KARNATAKA VIKAS GRAMEENA BANK',isdirect:0},
                {id:"KVB",name :'Karur Vysya Bank',isdirect:0},
                {id:"KLG",name :'KERALA GRAMIN BANK',isdirect:0},
                {id:"162",name :'Kotak Bank',isdirect:1},
                {id:"LVB",name :'Lakshmi Vilas Bank',isdirect:0},
                {id:"LVC",name :'Laxmi Vilas Bank - Corporate Net Banking',isdirect:0},
                {id:"LVR",name :'Laxmi Vilas Bank - Retail Net Banking',isdirect:0},
                {id:"MCB",name :'MAHANAGAR COOPERATIVE BANK',isdirect:0},
                {id:"MGBX",name :'MAHARASHTRA GRAMIN BANK',isdirect:0},
                {id:"NKB",name :'NKGSB BANK',isdirect:0},
                {id:"OBC",name :'Oriental Bank of Commerce',isdirect:0},
                {id:"PYTM",name :'PAYTM PAYMENTS BANK LTD',isdirect:0},
                {id:"PKG",name :'PRAGATHI KRISHNA GRAMIN BANK',isdirect:0},
                {id:"PRT",name :'PRATHAMA BANK',isdirect:0},
                {id:"PMC",name :'Punjab & Maharastra Coop Bank',isdirect:0},
                {id:"PSB",name :'Punjab & Sind Bank',isdirect:0},
                {id:"CPN",name :'Punjab National Bank - Corporate Banking',isdirect:0},
                {id:"PNB",name :'Punjab National Bank - Retail Net Banking',isdirect:0},
                {id:"RNS",name :'RAJKOT NAGRIK SAHAKARI BANK LIMITED',isdirect:0},
                {id:"RBL",name :'Ratnakar Bank - Retail Net Banking',isdirect:0},
                {id:"RAT",name :'RBL Bank Limited',isdirect:0},
                {id:"SWB",name :'Saraswat Bank',isdirect:0},
                {id:"SV2",name :'SHAMRAO VITHAL BANK CORPORATE',isdirect:0},
                {id:"SIB",name :'South Indian Bank',isdirect:0},
                {id:"SCB",name :'Standard Chartered Bank',isdirect:0},
                {id:"SBI",name :'State Bank of India',isdirect:1},
                {id:"SUT",name :'SUTEX COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"SYD",name :'Syndicate Bank',isdirect:0},
                {id:"TMB",name :'Tamilnad Mercantile Bank Ltd.',isdirect:0},
                {id:"TSA",name :'TELANGANA STATE COOP APEX BANK',isdirect:0},
                {id:"COS",name :'THE COSMOS CO OPERATIVE BANK LIMITED',isdirect:0},
                {id:"GSC",name :'THE GUJARAT STATE COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"HCB",name :'THE HASTI COOP BANK LTD',isdirect:0},
                {id:"MSN",name :'THE MEHSANA URBAN COOPERATIVE BANK',isdirect:0},
                {id:"NTB",name :'THE NAINITAL BANK LIMITED',isdirect:0},
                {id:"NJCX",name :'THE NAV JEEVAN CO-OP BANK LTD.',isdirect:0},
                {id:"SVC",name :'THE SHAMRAO VITHAL COOPERATIVE BANK',isdirect:0},
                {id:"SPC",name :'THE SURAT PEOPLES COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"TNS",name :'THE TAMIL NADU STATE APEX COOPERATIVE BANK',isdirect:0},
                {id:"TBS",name :'THE THANE BHARAT SAHAKARI BANK LIMITED',isdirect:0},
                {id:"VAR",name :'THE VARACHHA COOPERATIVE BANK LIMITED',isdirect:0},
                {id:"VIJX",name :'THE VIJAY CO OPERATIVE BANK LTD.',isdirect:0},
                {id:"VSB",name :'THE VISHWESHWAR SAHAKARI BANK LIMITED',isdirect:0},
                {id:"TJB",name :'TJSB Bank',isdirect:0},
                {id:"TJS",name :'TJSB SAHAKARI BANK LTD',isdirect:0},
                {id:"UCO",name :'UCO Bank',isdirect:0},
                {id:"UJV",name :'Ujjivan Small Finance Bank Limited',isdirect:0},
                {id:"UBI",name :'Union Bank of India',isdirect:0},
                {id:"UNI",name :'United Bank of India',isdirect:0},
                {id:"VVS",name :'VASAI VIKAS SAHAKARI BANK LIMITED',isdirect:0},
                {id:"VJB",name :'Vijaya Bank',isdirect:0}]; 	
    var datatable;
    $(function(){
        
 eraseCookie('s_data_sip');
 
    var dt= new Date();
    var numberOfDaysToAdd = 7;
    dt.setDate(dt.getDate() + numberOfDaysToAdd); 
    var yyyy = dt.getFullYear().toString();
    var mm = (dt.getMonth()+1).toString(); // getMonth() is zero-based
    var dd  = dt.getDate().toString();
    var min = yyyy +'-'+ (mm[1]?mm:"0"+mm[0]) +'-'+ (dd[1]?dd:"0"+dd[0]); // padding
    $('#SIPDate').prop('min',min);


        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        
        $("#account").select2({placeholder: 'Select Account', maximumSelectionSize: 1});
          $('#account').on('change', function() {
               
             $("#schemename").select2("val", "");
              $("#folionumber").select2("val", "");
              $("#amc").select2("val", "");
              $("#schemetype").select2("val", "");
              
              
              $("#folionumber").html('<option value="">Select Folio</option>');
              $("#amc").html('<option value="">Select AMC</option>');
            

            var trancationId=$('#trancationId').val() ;
                eraseCookie('s_data_sip');
                
                bindAccountDetail($("#account :selected").data('val'));
           
        });
        $("#schemename").select2({
            placeholder: 'Select Scheme Name',
            minimumInputLength: 2,
            tags: [],
            ajax: {
                url: "<?php echo site_url('client/PurchaseMutualFund/get_bsc_scheme_list_sip'); ?>",
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                        schemetype:$('#schemetype').val()  ? $('#schemetype').val()[0]:'',
                        amc:$('#amc').val()  ? $('#amc').val()[0]:''
                    };
                },
                results: function (data) {
                    
                    return {
                        
                        results: $.map(data, function (item) {
                            return {
                                text: item.SchemeName,
                                id: item.SchemeCode
                            }
                        })
                    };
                }
            }
        });
        $('#schemetype').on('change', function() {
          $("#amc").select2("val", "");
          $("#schemename").select2("val", "");
          bindAMC(this.value);
        });
        $('#amc').on('change', function() {
          
          
          $("#schemename").select2("val", "");
          
        });
        
        
        $('#schemename').on('change', function() {
          bindFolio(this.value);
        });
        
        $("#schemetype").select2({placeholder: 'Select Scheme Type', maximumSelectionSize: 1});
        $("#amc").select2({placeholder: 'Select AMC', maximumSelectionSize: 1});
        $("#folionumber").select2({placeholder: 'Select Folio Number', maximumSelectionSize: 1});
        bindAMC('');
        setTimeout(function(){ bind_value_from_cookies(); }, 500);
        
        
        $(".next").click(function(){
            var isValidClick=false;
            current_fs = $(this).parent();
            next_fs = $(this).parent().next();
            
            if($(this).hasClass('nextaccount'))
            {
                if(!$('#account').val())
                {
                    bootbox.alert("Please select Account.");
                    
                }
                else
                {
                        set_cookie();
                        isValidClick=true;
                }
            }
            else if($(this).hasClass('nextscheme'))
            {
                if(!$('#schemename').val() )
                {
                    bootbox.alert("Please select Scheme Name.");
                    
                }
                else
                {
                    set_cookie();
                    bindVerifyScheme();
                    BindMendateBank();
                    isValidClick=true;
                }
            }
            else if($(this).hasClass('nextverifyscheme'))
            {
                if(!$('#amount').val())
                {
                    bootbox.alert("Please Enter Amount.");
                }
                else if(!$('#SIPDate').val())
                {
                    bootbox.alert("Please select SIP Start date.");
                }
                else if(!$('#frequency').val())
                {
                    bootbox.alert("Please select frequency type.");
                }
                 else if(!$('#bank_id').val())
                {
                    bootbox.alert("Please select Bank.");
                }
                 else if(!$('#purchaseconsent').prop("checked"))
                {
                    bootbox.alert("Please verify Team & Condition.");
                }
                else
                {
                    var minvalue=$('#minamount').val();
                    var maxvalue=$('#maxamount').val();
                    if(+$('#amount').val()<minvalue)
                    {
                        bootbox.alert("please enter more than "+minvalue+" value.");    
                    }
                    else
                    {
                        var orderno=placeorder();
                        isValidClick=false;   
                    }
                    
                    
                    //}
                }
            }
            
            if(isValidClick==true)
            {
                movenextstage(isValidClick,next_fs,current_fs);
            }
            next_fs.find('input[name="previous"]').removeAttr('disabled');
            setTimeout(function(){ current_fs.find('input[name="next"]').removeAttr('disabled'); }, 500);
            
        });
        
        $(".previous").click(function(){

            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            //Remove class active
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();
            previous_fs.find('input[name="next"]').removeAttr('disabled');
            //hide the current fieldset with style
            current_fs.animate({opacity: 0}, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;
    
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({'opacity': opacity});
                },
                duration: 600
            });
        });
        $('.radio-group .radio').click(function(){
            $(this).parent().find('.radio').removeClass('selected');
            $(this).addClass('selected');
        });
        $('.isnumeric').keypress(function (e) {
                var regex = new RegExp("^[0-9 ]+$");
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);

                if (regex.test(str)) {
                    if ($('.amount_error').hasClass('hide') == false) {
                        $('.amount_error').addClass('hide');
                    }
                    return true;

                }

                e.preventDefault();
                return false;
            });
            
        function bindAccountDetail(obj)
        {
            var str='';
            if(obj)
            {
                
                str='<h3 class="label-primary" style="color:white;padding:5px;background:">Seleted Account Details</h3>';
                if(obj.FIRSTAPPLICANTNAME)
                {
                    str+="<span class='label label-default'><b>First Applicant Name:</b> &nbsp;&nbsp;"+obj.FIRSTAPPLICANTNAME+ "</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                if(obj.SECONDAPPLICANTNAME)
                {
                    str+="<span class='label label-default'><b>Second Applicant Name:</b> &nbsp;&nbsp;"+obj.SECONDAPPLICANTNAME+ "</span> &nbsp;&nbsp;&nbsp;&nbsp;";
                }
                if(obj.THIRDAPPLICANTNAME)
                {
                    str+="<span class='label label-default'><b>Third Applicant Name:</b> &nbsp;&nbsp;"+obj.THIRDAPPLICANTNAME+ "</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    
                }
                if(obj.CLIENTNOMINEE)
                {
                    str+="<span class='label label-default'><b>Client Nominee:</b> &nbsp;&nbsp;"+obj.CLIENTNOMINEE+ "</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    
                }
                if(obj.CLIENTHOLDING)
                {
                    str+="<span class='label label-default'><b>Client Holding:</b> &nbsp;&nbsp;"+obj.CLIENTHOLDING+ "</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    
                }
                
                
                str+='<br/><br/><table class="table table-striped table-bordered table-full-width dataTable" width="100%"><tr><th>Bank Name</th><th>Branch</th><th>Account No</th></tr>';
                if(obj.ACCNO1)
                {
                    str+="<tr><td>"+obj.BANKNAME1+ "</td><td>"+obj.BANKBRANCH1+ "</td><td>"+obj.ACCNO1+ "</td></tr>";
                }
                str+='</table>';
                
                
            }
            $('#AccountDetail').html(str);
        }
        
        function movenextstage(isValidClick,next_fs,current_fs)
        {
             
          
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
            next_fs.find('input[name="previous"]').removeAttr('disabled');
            next_fs.show();
            current_fs.animate({opacity: 0}, {
                step: function(now) {
                    opacity = 1 - now;
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                next_fs.css({'opacity': opacity});
                },
                duration: 600
            });
            
        }
        function bindAMC(schemetype)
        {
            
            $.ajax({
                url: "<?php echo site_url('client/PurchaseMutualFund/get_bsc_amc_list_sip'); ?>",
                dataType: 'html',
                type: "POST",
                data: {'schemetype':schemetype},
                success: function (response) {
                    $("#amc").html(response);
                /*    var c_data=JSON.parse(readCookie('s_data_sip'));
                    if(c_data&& c_data.amcId && c_data.amcId>0)
                    {
                    $("#amc").val(c_data.amcId).trigger('change');
                    setTimeout(function(){ $("#schemename").val(c_data.schemeId.split(',')[0]).trigger('change'); }, 500);
                    }
                  */  
                }
            }); 
        } 
        
        function bindFolio(schemeid)
        {
             var account= $('#account').val()[0];
                $.ajax({
                url: "<?php echo site_url('client/PurchaseMutualFund/get_client_folionumber'); ?>",
                dataType: 'html',
                type: "POST",
                data: {'schemeid':schemeid,
                    'account':account
                },
                success: function (response) {
                    $("#folionumber").html(response);
                  /*  var c_data=JSON.parse(readCookie('s_data_sip'));
                    if(c_data.folionumber && c_data.folionumber!='')
                    {
                        $("#folionumber").val(c_data.folionumber);
                    }
                    */
                }
            }); 
            
        } 
        
        function delete_cookie(name) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        };
        function set_cookie() {
            var acc=$('#account').val();
            var stid=$('#schemetype').val();
            var sid=$('#schemename').val() ;
            var folionumber=$('#folionumber').val() ;
            var amcid=$('#amc').val() ;
            var trancationId=$('#trancationId').val() ;
            eraseCookie('s_data_sip');
            var s_data={
                account:acc.length>0 ?acc[0]:0,
                schemeId:sid,
                schemeTypeId:stid,
                amcId:amcid,
                trancationId:trancationId,
                folionumber:folionumber
            }
            createCookie('s_data_sip',JSON.stringify(s_data),1);
                
            isValidClick=true;
        };
        function bind_value_from_cookies()
        {
            
            var c_data=JSON.parse(readCookie('s_data_sip'));
            if(c_data && c_data.account)
            {
            $("#account").select2("val", c_data.account).change();
            //$("#schemetype").val(c_data.schemeTypeId).trigger('change');
            //$('#trancationId').val(c_data.trancationId);
            //$('#folionumber').val(c_data.folionumber) ;
            }
        }
        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        function createCookie(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            }
            else var expires = "";               
        
            document.cookie = name + "=" + value + expires + "; path=/";
        }
        function eraseCookie(name) {
            createCookie(name, "", -1);
        }
        function bindVerifyScheme()
        {
            var c_data=JSON.parse(readCookie('s_data_sip'));
             
            $.ajax({
                url: "<?php echo site_url('client/PurchaseMutualFund/MFPurchaseDetail'); ?>",
                dataType: 'html',
                type: "POST",
                data: {'sid':c_data.schemeId.split(',')[0]},
                success: function (response) {
                    var res=JSON.parse(response);
                    var data=res.data[0];
                    
                    var tblString='<tr>'+
                        '<td>'+data.SchemeName+'</td>'+
                        '<td>'+data.SchemeType+'</td><input type="hidden" id="minamount" value="'+parseInt(data.SIPMINIMUMINSTALLMENTAMOUNT)+'"><input type="hidden" id="maxamount" value="'+parseInt(data.SIPMAXIMUMINSTALLMENTAMOUNT)+'">'+
                        '<td><input class="form-control isnumeric" min="'+parseInt(data.SIPMINIMUMINSTALLMENTAMOUNT)+'" style="padding: 0px 10px 0px 10px;width:95%;border: 3px solid #9e9e9e;height:50px" id="amount" name="amount" type="number" placeholder="Amount" >'+
                        '<span><b>Min Value:</b> <span id="schememinvalue">'+parseInt(data.SIPMINIMUMINSTALLMENTAMOUNT)+'</span></span>'+
                        '<span class="amount_error error_msg hide">Amount is required.</span></td><tr>';
                    
                    
                        $('#tblschemelist').find('tbody').html(tblString);
                                                       
                }
            }); 
            
        }
   
        
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });

        function placeorder()
       {
            var detail=null;
            var summart=null;
          //  var l = Ladda.create(button);
          //  l.start();
            var c_data=JSON.parse(readCookie('s_data_sip'));
            var SchemeCode = $('#schemename').val().split(',')[0];
            var amount= $('#amount').val();
            var account =$('#account').val()[0];
            var fNumber =$('#folionumber').val();
            var frequency =$('#frequency').val();
            var SIPDate =$('#SIPDate').val();
            var mendateId =$('#bank_id').val();
            
              $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/placesiporder');?>",
                  type: 'post',
                  data: {'SchemeCode': SchemeCode, 
                        'account':account,
                        'amount':amount,
                        'folionumber':fNumber,
                        'SIPDate':SIPDate,
                        'frequency':frequency,
                        'mendateId':mendateId
                        },
                  dataType: 'json',
                  success: function(data)
                  { 
                        if(data['Status'])
                          {
                                if(data['OrderId']>0)
                                  {
                                    window.location.href = "https://3tense.com/client/PurchaseMutualFund/MFSummarySIP?OrderNumber="+data['OrderId'];
                                  }
                                  else
                                  {
                                    bootbox.alert(data['Message']);
                                  }
                             
                          }
                          else
                          {
                              bootbox.alert("No Records Found");
                          }
                    
                  },
                  error: function(jqXRR, textStatus, errorThrown)
                  {
                      console.log(jqXRR);
                      console.log(textStatus);
                      console.log(errorThrown);
                   //   l.stop();
                  }
              });
              
        }
        
        
        
        /// payment
        function BindMendateBank()
        {
            $("#bank_id").attr('required','required');
            $('#bank_id').empty().append('<option selected="selected" value="">Select Bank</option>');
            var account =$('#account').val()[0];
            if(account)
            {
                $.ajax({
                url: "<?php echo site_url('client/PurchaseMutualFund/GetMendateBankDetail');?>",
                type: 'post',
                data: {
                    ClientCode:account
                },
                dataType: 'json',
                success: function(data)
                { 
                    if(data['Status'])
                    {
                        if(data['Message'].account_list)
                        {
                            var banklistobj=data['Message'].account_list;
                            if(banklistobj.length>0)
                            {
                                for(var i=0;i<banklistobj.length;i++)
                                {
                                    $('#bank_id').append($("<option></option>").attr("value", banklistobj[i].MANDATECODE).text(banklistobj[i].BANKNAME)); 
                                }
                            }
                        }
                    }
                    else
                    {
                        bootbox.alert("Your bank is not registered. Please registered your bank first.");
                    }
                   
                },
                error: function(jqXRR, textStatus, errorThrown)
                {
                    console.log(jqXRR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            }
        }
      
        $(".MakePayment").click(function(e){
            current_fs = $(this).parent();
           makePayment(current_fs);
           
        });
        
        function makePayment(current_fs){
              var Iserror=0;
              
            if(!$('#bank_id').val())
            {
                bootbox.alert("Please select bank.");
                
            }
            else if(!$('#account_number').val())
            {
                bootbox.alert("Please select account number.");
            }
            else
            {
                
                var c_data=JSON.parse(readCookie('s_data_sip'));    
                var data={
                    mode:$("#bank_id :selected").attr('mode').trim()=='0'?'NODAL':'DIRECT',
                    bankid:$('#bank_id').val().trim(),
                    accountnumber:$('#account_number').val().trim(),
                    ifsc:$("#account_number option:selected").attr("ifsccode").trim(),
                    order_id:c_data.trancationId
                };
                $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/MakePayment');?>",
                  type: 'post',
                  data: data,
                  dataType: 'json',
                  success: function(Responsedata)
                  { 
                      debugger;
                      if(Responsedata['Status']!=101)
                      {
                          if(Responsedata['OrderId']>0)
                          {
                              
                              if($('#mode').val()=='NEFT')
                              {
                                   
                                   bootbox.alert(Responsedata['data'].responsestring, function(){
                                          window.location="<?php echo base_url();?>client/PurchaseMutualFund/index";
                                    });
                              }
                              else
                              {
                                    var win1 = window.open("", "myWindow", "width=800,height=800");
                                    win1.document.open();
                                    win1.document.write(Responsedata['data'].responsestring);
                                    win1.document.close();
                              }
                            
                          }
                          else
                          {
                            bootbox.alert(Responsedata['Message'].responsestring);
                          }
                          
                      }
                      else
                      {
                            bootbox.alert(Responsedata['Message'].responsestring);;
                      }
                      
                            setTimeout(function(){ current_fs.find('input[name="MakePayment"]').removeAttr('disabled'); }, 500);
                   
                  },
                  error: function(jqXRR, textStatus, errorThrown)
                  {
                      console.log(jqXRR);
                      console.log(textStatus);
                      console.log(errorThrown);
                   
                  }
              });
           
            }
           
        }
        /// payment
      
    });
    
    function teamscondition(id)
    {
        $('#TeamAndCondition').modal('show');
    }

</script>
          
