<?php include "header-focused.php"; ?>
<br/><br/>
<style>
    #tblschemelist_length
    {
    display:none;   
    }
    .error_msg
    {
        color:red;
    }
    .hide
    {
        display:none;
    }
</style>
<div class="container body">
    <div class="right_col" role="main" style="margin-top:15px;padding:10px 10px">
        <div class="panel panel-primary">
  		    <div class="panel-body">
  		        <div class="row">
                    <div class="col-md-12" style="text-align:right">
                        <a href="<?php echo base_url();?>client/PurchaseMutualFund/index" class="btn btn-success">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblschemelist" class="display table table-striped table-bordered table-full-width dataTable" width="100%">
                            <thead>
                                <tr>
                                    <td style='width: 5%;'>Sr.No</td>
                                    <td style='width: 50%;'>Scheme Name</td>
                                    <td style='width: 10%;'>Scheme Type</td>
                                    <td>Amount</td>
                                
                                </tr>
                            </thead>
                            <tbody>
                                  <?php 
                                  $i=1;
                                  foreach($schmeme_detail as $row):?>
                                <tr>
                                    <td>
                                        <input type="hidden" value="<?php echo $row->SchemeCode ?>"  name="hidden_row" id ="hidden_row"/>
                                        <input type="hidden" value="<?php echo $account->account ?>"  name="hidden_account" id ="hidden_account"/>
                                        <?php echo $i++; ?>
                                    </td>
                                    <td>
                                        <?php echo $row->SchemeName; ?>
                                    </td>
                                    <td>
                                        <?php echo $row->SchemeType; ?>
                                    </td>
                                    <td>
                                        <input class="form-control isnumeric" maxlength="10" style="padding: 0px 10px 0px 10px;width:95%" id="amount" name="amount" type="text" placeholder="Amount" >
                                        <span class="amount_error error_msg hide">Amount is required.</span>
                                    </td>
                                </tr>  
                                  
                            <?php endforeach; ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div><br/><br/>
                <div class="row">
                    <div class="col-md-12">
                        <input type="checkbox" name="purchaseconsent" id="purchaseconsent" />
                        I Agree to <a style="color: blue;text-decoration: underline;cursor: pointer;" onclick="teamscondition()">Terms & Conditions</a>
                        <Br>
                        <span class="termsandcondition_error error_msg hide">Terms & conditions is required.</span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="button" class="btn btn-success ladda-button" tabindex="4" id="mf_submit" style="expand-right;margin-top:25px">
                             <span class="ladda-label">Make Payment</span></button>
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
                <div class="modal-body" id="">
                    <p>Mutual fund investments are subject to market risks, read all scheme related documents carefully before investing. Past performance may or may not be sustained in future and should not be used as a basis for investments. The use of this website is at your sole risk. We shall be not responsible or liable for any loss, shortfall, tax, cess, damages, AMC charges or fees, ECS return charges, ECS processing charges, bank charges etc incurred by you due to any reasons including but not limited to delay in processing of orders, orders not being processed or rejected, any mistake or omission on part of the RTA, AMC, Bank or Exchange.</p>
                    <p>For transactions through MF-IN-DEMAT (NSE MFSS) mode, redemption amount will be credited to your trading account. For transactions through RTA mode, redemption amount will be credited to your bank account through fund transfer or will be sent by cheque at your correspondence address as per AMC records. ELSS (Equity Linked Tax Saving Schemes) have a lock in period of 3 years and would be eligible for Tax benefits as per the prevailing IT laws. Redemption of ELSS or closed ended fund is subject to lock-in period.</p>
                    <br>
                    <p>Cut off time for all online RTA transactions is 1 pm.</p>
                    <p>Cut off time for Liquid Funds in RTA Mode will be 12.30 pm for previous day NAV. Order placed on Monday to Thursday between 12.30 pm to 1.00 pm will be considered for same day NAV & Order placed on Friday after 12.30pm will be considered for Sunday's NAV.</p>
                    <p>Cut off time for MF-IN-DEMAT (NSE MFSS) is 2 pm for transactions of less than Rs. 2 lakhs and 12.00 noon for transactions of Rs. 2 Lakhs or more (single transaction). Orders placed after the cut-off time will be processed on next working day. Our cut-off timings may be different from Exchange and RTA cut off timings to ensure smooth processing of your orders. For MF-IN-DEMAT transactions, units will be transferred to mapped demat account as per exchange settlement schedule.</p>
                    <Br>
                    <p>TERMS AND CONDITIONS FOR INVESTING IN ONLINE MUTUAL FUNDS.</p>
                    
                    <p>The terms and conditions for online investing in Mutual Funds given below are in addition to and binding on the existing Power of Attorney and the Client Agreement that has been signed by you at the time of your becoming our client.</p>
                    
                    <p>Definitions:</p>
                    <ol>
                      <li>"UNITS" shall mean the interest of the investor in an undivided share in the net assets of a Scheme of the Fund.</li>
                      <li>"NAV" shall mean the net asset value of the Units of the Scheme and the plans and options therein, calculated on every business day, in the manner provided in the Offer Document of the respective schemes or as may be prescribed by SEBI Regulations from time to time.</li>
                      <li>"PURCHASE" shall mean subscription to the Units of any of the Schemes of the Mutual Fund.</li>
                      <li>"REDEMPTION" shall mean sale of the units of the various schemes of the Mutual Fund.</li>
                      <li>"SWITCH" shall mean an option to the investor to shift his existing investment in any of the schemes of the Mutual Fund to another scheme of the same Mutual Fund.</li>
                      <li>"SYSTEMATIC INVESTMENT PLAN (SIP)" "Automatic Investment Plan (AIP)" shall mean an option available with the Customer for investing, at a specified frequency, in a specified Scheme of the Fund, a fixed amount of Rupees for purchasing additional units at the applicable NAV on a specified date, provided that the provisions of the Offer Document of the respective Scheme shall always be applicable for SIP transactions.</li>
                      <li>"SYSTEMATIC WITHDRAWAL PLAN (SWP)" shall mean an option available with the customer who holds Units to withdraw a predetermined amount or a variable amount subject to deduction of tax, if any, at a specified frequency, from a specified Scheme of the Fund at a specified period thereby reducing the unit balance to his credit by the units so withdrawn at the applicable NAV on the specified date, provided that the provisions of the Offer Document of the respective scheme shall always be applicable for SWP transactions.</li>
                      <li>"SYSTEMATIC TRANSFER PLAN (STP)" shall mean an option available with the customer who holds Units to transfer a predetermined amount or a variable amount subject to deduction of tax, if any, at a specified frequency, from a specified Scheme of the Fund to another specific scheme of the Fund at a specified period at a specified frequency at the applicable NAV on a specified date, provided that the provisions of the Offer Document of the respective Schemes shall always be applicable for STP transactions.</li>
                      <li>"LOAD" shall mean a charge, which the Asset Management Company (AMC) may collect on entry and/or exit from a scheme.</li>
                      <li>The client is requested to check up the personal and bank related details provided by him/her. Neither I-Solutions nor any of the Mutual Funds chosen shall accept any liability which may arise as a consequence of the erroneous information provided by the client.</li>
                      <li>The units of the schemes shall be allotted, redeemed or switched, as the case may be, at the Net Asset Value (NAV) prevalent on the date of the application on a best effort basis , if the application for purchase, redemption or switch is received by I-Solutions before the cut-off time as specified on the website and consistent with the terms of the Scheme. However, I-Solutions undertakes to provide NAV of T+7 days to all of its customers. I-Solutions shall not be liable for any loss that may arise to the client as a result of the correct NAV not being allotted to the client's transactions on the website.</li>
                      <li>Any request falling due on a holiday would be processed on the next business day and respective NAV would be applicable as per the Mutual Fund's offer document.</li>
                      <li>No offline transaction requests will be entertained by I-Solutions or the concerned Mutual Fund or their respective registrars in respect of the units allotted through I-Solutions's online portal.</li>
                      <li>The client agrees and understands that in case he is to close his broking account, he shall have to either redeem or convert to physical units for all his mutual fund holdings. The client agrees that I-Solutions shall not be responsible for any loss or implication for in such cases. In case the client fails to do so and thereby cannot transact in such units not converted or redeemed after his account is closed, the client understands that I-Solutions shall not be liable.</li>
                      <li>In the case of Automatic Investment Plan, in the absence of sufficient allocated funds, unallocated funds to the extent available shall also be utilised. In the absence of sufficient funds, both allocated and unallocated, no investment shall be made for that particular period.
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
                      <li>The customer confirm that he/she/they is/are aware that the funds from his/her/their account will first move into a pool account maintained by I-Solutions in the bank account opened for this purpose and later the same will be remitted to the AMC collection accounts whenever the customer makes investment in to the schemes offered online,</li>
                      <li>In case any purchase order is rejected by the AMC due to any reason, the amount will be refunded by the AMC to the I-Solutions's Pool account and I-Solutions will credit the respective customers ledger account immediately. However, JM Financial Mutual Fund will transfer the refund amount in respect of rejected purchase orders directly to the customer's bank account.</li>
                      <li>The request for change of broker codes from a non-I-Solutions broker to I-Solutions online brokercode will be entertained by I-Solutions only when the following condition are met and provided the schemes of the AMC are available in the online portal of I-Solutions.
                      <ol style="list-style-type:lower-alpha">
                            <li>The pattern of holdings should be same i.e if in I-Solutions the customer is holding in single name the folio under request for broker code change also must have holdings in single mode.</li>
                            <li>The Status of the individual should be same.( ie, Resident Individual, NRI, HUF etc )</li>
                            <li>Signature of the customer and Bank details in online records should be the same.</li>
                            <li>The customer must give AMC wise requests in original for broker code changes</li>
                            <li>Copy of account statement should be provided with the request.</li>
                        </ol>
                      </li>
                      <li>The customer is aware that the mutual fund units will be allotted in the single name of the customer as it appears in his trading account with I-Solutions and that the facility of joint holders and Nominee is not available for the transactions made through the online mutual fund portal of I-Solutions Limited. However the customer may approach I-Solutions for appointing a nominee. The Nominee will be the same for all the mutual fund units he/she is holding under the Customer ID given by I-Solutions Limited irrespective of the AMC ( Mutual Fund Companies)</li>
                      <li>Any change in nomination, will have the effect on all the schemes applied through I-Solutions. The customer is aware that he will not be able to nominate different individuals for different mutual fund schemes subscribed through I-Solutions.</li>
                      <li>The customer is also aware that his investment details will be registered with the AMCs/registrars as per the customer profile displayed in the website under online mutual fund investment section</li>
                        <li>In case any client is desirous of obtaining the broker code of I-Solutions he may register with I-Solutions on fulfilling the following conditions
                        <ol style="list-style-type:lower-alpha">
                            <li>The holding is in single name as it appears in the trading account of the customer with I-Solutions.</li>
                            <li>The Status of the individual should be same. ie, Resident Individual, NRI, HUF etc</li>
                            <li>Signature of the customer and Bank details in online records should be the same.</li>
                            <li>The customer must give AMC wise requests in original for broker code changes.</li>
                            <li>Copy of account statement should be provided with the request.</li>
                            <li>The AMC for which the customer had subscribed is/ are available on the online portal of I-Solutions.</li>
                        </ol>
                        </li>
                        <li>The customer confirms that he has read the disclosures on commissions (including trail commissions) payable to I-Solutions for various categories of mutual funds schemes. Please click here</li>
                    </ol>
                    <p>I/we have gone through the offer document/key information memorandum / regulatory clauses published by regulatory authorities before deciding to make this investment in the mutual fund scheme.</p>
                    <p>I-Solutions reserves the right to change the terms and conditions without any prior notice.</p>
                    <p>Copyright © 2020 I-Solutions All Rights Reserved.       </p>
                </div>
                <div class="modal-footer">
                    <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<script type="application/javascript">

    var datatable;
    $(function()
    {
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
    
    
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });

        $("#mf_submit").click(function()
        {
            var error=0;
            if(!$('#amount').val())
            {
                $('.amount_error').removeClass('hide');
                error=1;
            }
            else
            {
                if($('.amount_error').hasClass('hide')==false)
                {
                    $('.amount_error').addClass('hide');
                }
            }
            if(!$('#purchaseconsent').prop("checked"))
            {
                $('.termsandcondition_error').removeClass('hide');
                error=1;
            }
            else
            {
                if($('.termsandcondition_error').hasClass('hide')==false)
                {
                    $('.termsandcondition_error').addClass('hide');
                }
            }
            if(error)
            {
                return 0;
            
            }
            
            var detail=null;
            var summart=null;
            var l = Ladda.create(button);
            l.start();
            var SchemeCode = $('#hidden_row').val();
            var amount= $('#amount').val();
            var account =$('#hidden_account').val() ;
              $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/placeorder');?>",
                  type: 'post',
                  data: {'SchemeCode': SchemeCode, 
                        'account':account,
                        'amount':amount
                        },
                  dataType: 'json',
                  success: function(data)
                  { 
                      if(data['Status'])
                      {
                          if(data['OrderId']>0)
                          {
                        
                        window.location.replace("/client/PurchaseMutualFund/MakePayment/"+data['OrderId']);
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
                      l.stop();
                  },
                  error: function(jqXRR, textStatus, errorThrown)
                  {
                      console.log(jqXRR);
                      console.log(textStatus);
                      console.log(errorThrown);
                      l.stop();
                  }
              });
              
        });
        
      





    });
      function teamscondition(id)
        {
            
            $('#TeamAndCondition').modal('show');
        }
</script>
          