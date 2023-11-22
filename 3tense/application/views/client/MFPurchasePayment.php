<?php include "header-focused.php";
header("X-Frame-Options: SAMEORIGIN");?>
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
                        <a href="<?php echo base_url();?>client/PurchaseMutualFund/index" id="btn_back" class="btn btn-success">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 text-center">
                        <div class="panel panel-default">
                            <div class="panel-body form-horizontal payment-form">
                                <h4>Make Payment:</h4>
                                
                                    <?php
                                    if(isset($massage)){
                                    ?>
                                    <div class="form-group">
                                        <label for="concept" class="col-sm-12 control-label" style="text-align: center;color: #ff0000;"><?php echo $massage; ?></label>
                                     </div>   
                                    <?php } ?>
                                    
                                    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
                                    <div class="form-group">
                                        <label for="concept" class="col-sm-6 control-label">Mode Of Payment </label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="mode" id="mode" required>
                                                <option value="">Select Mode Of Payment</option>
                                                <option value="DIRECT">DIRECT</option>
                                                <option value="NODAL">NODAL</option>
                                                <option value="NEFT">NEFT</option>
                                                
                                            </select>
                                            <span class="mode_error error_msg hide">Mode of payment is required.</span>
                                        </div>
                                    </div>
                                    <div class="form-group bank_id">
                                        <label for="description" class="col-sm-6 control-label">Select Bank</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="bank_id" id="bank_id" >
                                                <option value="">Select Bank</option>
                                                <option value="ACB">ABHYUDAYA COOPERATIVE BANK LIMITED</option>
                                                <option value="ABPB">ADITYA BIRLA IDEA PAYMENTS BANK LTD </option>
                                                <option value="AIRP">AIRTEL PAYMENTS BANK LIMITED </option>
                                                <option value="ALD">ALLAHABAD BANK</option>
                                                <option value="ALB">Allahabad Bank - Retail Net Banking</option>
                                                <option value="ALC">ALLAHABAD BANK CORPORATE</option>
                                                <option value="ADB">Andhra Bank</option>
                                                <option value="APGX">ANDHRA PRADESH GRAMEENA VIKAS BANK </option>
                                                <option value="APG">ANDHRA PRAGATHI GRAMEENA BANK</option>
                                                <option value="ASB">APNA SAHAKARI BANK LIMITED</option>
                                                <option value="AUB">AU SMALL FINANCE BANK</option>
                                                <option value="UTI">Axis Bank</option>
                                                <option value="BDB">BANDHAN BANK LIMITED</option>
                                                <option value="BBC">Bank of Baroda - Corporate Banking</option>
                                                <option value="BBR">Bank of Baroda - Retail Net Banking</option>
                                                <option value="BOI">Bank Of India</option>
                                                <option value="BOM">Bank of Maharashtra</option>
                                                <option value="BAC">BASSEIN CATHOLIC COOPERATIVE BANK LIMITED</option>
                                                <option value="CNB">Canara Bank</option>
                                                <option value="CSB">Catholic Syrian Bank</option>
                                                <option value="CBI">Central Bank of India</option>
                                                <option value="CIT">CITI BANK</option>
                                                <option value="CUB">CITY UNION BANK LIMITED</option>
                                                <option value="CRP">Corporation Bank</option>
                                                <option value="DCB">DCB BANK LIMITED</option>
                                                <option value="DEN">Dena Bank</option>
                                                <option value="DBS">DEVELOPMENT BANK OF SINGAPORE</option>
                                                <option value="DLB">Dhanlakshmi Bank</option>
                                                <option value="DNS">DOMBIVLI NAGARI SAHAKARI BANK LIMITED</option>
                                                <option value="EQB">Equitas Small Finance Bank Ltd</option>
                                                <option value="ESF">Equitas Small Finance Bank Ltd</option>
                                                <option value="FBK">Federal Bank</option>
                                                <option value="FINO">FINO PAYMENTS BANK LTD </option>
                                                <option value="PJS">G P PARSIK BANK</option>
                                                <option value="HDF">HDFC BANK</option>
                                                <option value="HSB">HSBC BANK</option>
                                                <option value="ICI">ICICI Bank - Retail Net Banking</option>
                                                <option value="IDB">IDBI Bank - Retail Net Banking</option>
                                                <option value="IDF">IDFC BANK LIMITED</option>
                                                <option value="INB">Indian Bank</option>
                                                <option value="IOB">Indian Overseas Bank</option>
                                                <option value="IDS">IndusInd Bank</option>
                                                <option value="JJS">JALGAON JANATA SAHAKARI BANK LIMITED</option>
                                                <option value="JKB">Jammu & Kashmir Bank</option>
                                                <option value="JSB">Janata Sahakari Bank</option>
                                                <option value="JIOP">JIO PAYMENTS BANK LIMITED </option>
                                                <option value="KAI">KALLAPPANNA AWADE ICHALKARANJI JANATA SAHAKARI BANK LIMITED</option>
                                                <option value="KJS">KALYAN JANATA SAHAKARI BANK</option>
                                                <option value="KCB">KAPOL COOPERATIVE BANK LIMITED</option>
                                                <option value="KBL">Karnataka Bank Ltd</option>
                                                <option value="KVG">KARNATAKA VIKAS GRAMEENA BANK</option>
                                                <option value="KVB">Karur Vysya Bank</option>
                                                <option value="KLG">KERALA GRAMIN BANK</option>
                                                <option value="162">Kotak Bank</option>
                                                <option value="LVB">Lakshmi Vilas Bank</option>
                                                <option value="LVC">Laxmi Vilas Bank - Corporate Net Banking</option>
                                                <option value="LVR">Laxmi Vilas Bank - Retail Net Banking</option>
                                                <option value="MCB">MAHANAGAR COOPERATIVE BANK</option>
                                                <option value="MGBX">MAHARASHTRA GRAMIN BANK </option>
                                                <option value="NKB">NKGSB BANK</option>
                                                <option value="OBC">Oriental Bank of Commerce</option>
                                                <option value="PYTM">PAYTM PAYMENTS BANK LTD </option>
                                                <option value="PKG">PRAGATHI KRISHNA GRAMIN BANK</option>
                                                <option value="PRT">PRATHAMA BANK</option>
                                                <option value="PMC">Punjab & Maharastra Coop Bank</option>
                                                <option value="PSB">Punjab & Sind Bank</option>
                                                <option value="CPN">Punjab National Bank - Corporate Banking</option>
                                                <option value="PNB">Punjab National Bank - Retail Net Banking</option>
                                                <option value="RNS">RAJKOT NAGRIK SAHAKARI BANK LIMITED</option>
                                                <option value="RBL">Ratnakar Bank - Retail Net Banking</option>
                                                <option value="RAT">RBL Bank Limited</option>
                                                <option value="SWB">Saraswat Bank</option>
                                                <option value="SV2">SHAMRAO VITHAL BANK CORPORATE</option>
                                                <option value="SIB">South Indian Bank</option>
                                                <option value="SCB">Standard Chartered Bank</option>
                                                <option value="SBI">State Bank of India</option>
                                                <option value="SUT">SUTEX COOPERATIVE BANK LIMITED</option>
                                                <option value="SYD">Syndicate Bank</option>
                                                <option value="TMB">Tamilnad Mercantile Bank Ltd.</option>
                                                <option value="TSA">TELANGANA STATE COOP APEX BANK</option>
                                                <option value="COS">THE COSMOS CO OPERATIVE BANK LIMITED</option>
                                                <option value="GSC">THE GUJARAT STATE COOPERATIVE BANK LIMITED</option>
                                                <option value="HCB">THE HASTI COOP BANK LTD</option>
                                                <option value="MSN">THE MEHSANA URBAN COOPERATIVE BANK</option>
                                                <option value="NTB">THE NAINITAL BANK LIMITED</option>
                                                <option value="NJCX">THE NAV JEEVAN CO-OP BANK LTD. </option>
                                                <option value="SVC">THE SHAMRAO VITHAL COOPERATIVE BANK</option>
                                                <option value="SPC">THE SURAT PEOPLES COOPERATIVE BANK LIMITED</option>
                                                <option value="TNS">THE TAMIL NADU STATE APEX COOPERATIVE BANK</option>
                                                <option value="TBS">THE THANE BHARAT SAHAKARI BANK LIMITED</option>
                                                <option value="VAR">THE VARACHHA COOPERATIVE BANK LIMITED</option>
                                                <option value="VIJX">THE VIJAY CO OPERATIVE BANK LTD. </option>
                                                <option value="VSB">THE VISHWESHWAR SAHAKARI BANK LIMITED</option>
                                                <option value="TJB">TJSB Bank</option>
                                                <option value="TJS">TJSB SAHAKARI BANK LTD</option>
                                                <option value="UCO">UCO Bank</option>
                                                <option value="UJV">Ujjivan Small Finance Bank Limited</option>
                                                <option value="UBI">Union Bank of India</option>
                                                <option value="UNI">United Bank of India</option>
                                                <option value="VVS">VASAI VIKAS SAHAKARI BANK LIMITED</option>
                                                <option value="VJB">Vijaya Bank</option>
                                            </select>
                                            <span class="bank_id_error error_msg hide">Bank is required.</span>
                                        </div>
                                    </div> 
                                    <div class="form-group account_Id">
                                        <label for="amount" class="col-sm-6 control-label">Account Number</label>
                                        <div class="col-sm-6">
                                            
                                            <select class="form-control" name="account_number" id="account_number" >
	                                            <option value="">Select Account</option>
	                                        </select>
                                            <span class="account_number_error error_msg hide">Account Number is required.</span>
                                        </div>
                                    </div>
                                    <div class="form-group ifsc_code">
                                        <label for="status" class="col-sm-6 control-label">IFSC</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="ifsc" name="ifsc" >
                                            <span class="ifsc_error error_msg hide">IFSC is required.</span>
                                        </div>
                                    </div> 
                                    <div class="form-group neft_reference">
                                        <label for="date" class="col-sm-6 control-label">NEFT Reference</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="neft_reference" name="neft_reference">
                                            <span class="neft_reference_error error_msg hide">NEFT Reference is required.</span>
                                        </div>
                                    </div>   
                                    <div class="form-group vpa_id">
                                        <label for="date" class="col-sm-6 control-label">VPA ID</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="vpa_id" name="vpa_id">
                                            <span class="vpa_id_error error_msg hide">VIP ID is required.</span>
                                        </div>
                                    </div> 
                                    <div class="form-group mandateid">
                                        <label for="date" class="col-sm-6 control-label">MandateID</label>
                                        <div class="col-sm-6">
                                            
                                            <select class="form-control" name="mandateid" id="mandateid" >
                                                <option value="">Select MandateId</option>
                                            </select>
                                            <span class="mandateid_error error_msg hide">MandateID is required.</span>
                                        </div>
                                    </div> 
                                    <!-- <div class="form-group">
                                        <label for="date" class="col-sm-6 control-label">Logout Url/ Loopback URL</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="loopback_url" name="loopback_url">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label for="concept" class="col-sm-6 control-label">Allow LoopBack Success/Failure</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="mode" id="mode" required>
                                                <option value="Y">Yes</option>
                                                <option value="N">No</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success ladda-button" tabindex="4" id="submit"><span class="ladda-label">Submit</span>
                                            </button>
                                        </div>
                                    </div> 
                                </form>        
                            </div>
                        </div>            
                    </div> 
                </div><br/><br/>
             
                </div>
       </div>
    </div>
</div>

<script type="application/javascript">

    $(function()
    {
           $(".bank_id").hide();
        $(".vpa_id").hide();
        $(".neft_reference").hide();
        $(".mandateid").hide();
        $(".account_Id").hide();
        $(".ifsc_code").hide();
        $(".vpa_id").hide();
         $("#submit").click(function()
        {
              var Iserror=0;
              
            if(!$('#mode').val())
            {
                $('.mode_error').removeClass('hide');
                Iserror=1;
            }
            else
            {
                if($('.mode_error').hasClass('hide')==false)
                {
                    $('.mode_error').addClass('hide');
                }
            }
          
            if($( "#mode option:selected" ).val() == 'DIRECT'){
                 if(!$('#bank_id').val())
                {
                    $('.bank_id_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.bank_id_error').hasClass('hide')==false)
                    {
                        $('.bank_id_error').addClass('hide');
                    }
                }
                if(!$('#account_number').val())
                {
                    $('.account_number_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.account_number_error').hasClass('hide')==false)
                    {
                        $('.account_number_error').addClass('hide');
                    }
                }
          }
            else if($( "#mode option:selected" ).val() == 'NODAL'){
              if(!$('#bank_id').val())
                {
                    $('.bank_id_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.bank_id_error').hasClass('hide')==false)
                    {
                        $('.bank_id_error').addClass('hide');
                    }
                }
                if(!$('#account_number').val())
                {
                    $('.account_number_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.account_number_error').hasClass('hide')==false)
                    {
                        $('.account_number_error').addClass('hide');
                    }
                }
          }
            else if($( "#mode option:selected" ).val() == 'OneTimeMandate'){
               if(!$('#bank_id').val())
                {
                    $('.bank_id_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.bank_id_error').hasClass('hide')==false)
                    {
                        $('.bank_id_error').addClass('hide');
                    }
                }
                if(!$('#account_number').val())
                {
                    $('.account_number_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.account_number_error').hasClass('hide')==false)
                    {
                        $('.account_number_error').addClass('hide');
                    }
                }
                 if(!$('#mandateid').val())
                {
                    $('.mandateid_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.mandateid_error').hasClass('hide')==false)
                    {
                        $('.mandateid_error').addClass('hide');
                    }
                }
            
          }
            else if($( "#mode option:selected" ).val() == 'UPI'){
                if(!$('#bank_id').val())
                {
                    $('.bank_id_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.bank_id_error').hasClass('hide')==false)
                    {
                        $('.bank_id_error').addClass('hide');
                    }
                }
                if(!$('#account_number').val())
                {
                    $('.account_number_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.account_number_error').hasClass('hide')==false)
                    {
                        $('.account_number_error').addClass('hide');
                    }
                }
                if(!$('#account_number').val())
                {
                    $('.account_number_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.account_number_error').hasClass('hide')==false)
                    {
                        $('.account_number_error').addClass('hide');
                    }
                }
                if(!$('#vpa_id').val())
                {
                    $('.vpa_id_error').removeClass('hide');
                    Iserror=1;
                }
                else
                {
                    if($('.vpa_id_error').hasClass('hide')==false)
                    {
                        $('.vpa_id_error').addClass('hide');
                    }
                }
             
          }
          
            if( Iserror!=1)
            {
                 var data={
                    mode:$('#mode').val(),
                    bankid:$('#bank_id').val(),
                    accountnumber:$('#account_number').val(),
                    neft_reference:$('#neft_reference').val(),
                    mandateid:$('#mandateid').val(),
                    vpaid:  $('#vpa_id').val(),
                    order_id:$('#order_id').val()
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
                   
                  },
                  error: function(jqXRR, textStatus, errorThrown)
                  {
                      console.log(jqXRR);
                      console.log(textStatus);
                      console.log(errorThrown);
                   
                  }
              });
           
            }
           
        });
          var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
        $("#bank_id").change(function(){
              $('#account_number').empty().append('<option selected="selected" value="">Select Account</option>');
        
             $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/GetBankDetail');?>",
                  type: 'post',
                  data: {
                        order_id:$('#order_id').val()
                        },
                  dataType: 'json',
                  success: function(data)
                  { 
                      if(data['Status'])
                      {
                         if(data['Message'].account_list)
                         {
                             if(data['Message'].account_list[0].BANKNAME1==  $("#bank_id").val() && data['Message'].account_list[0].ACCNO1!='')
                             {
                                $('#account_number').append($("<option></option>").attr("value", data['Message'].account_list[0].ACCNO1).text(data['Message'].account_list[0].ACCNO1)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME2==  $("#bank_id").val() && data['Message'].account_list[0].ACCNO2!='')
                             {
                                $('#account_number').append($("<option></option>").attr("value", data['Message'].account_list[0].ACCNO2).text(data['Message'].account_list[0].ACCNO2)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME3==  $("#bank_id").val() && data['Message'].account_list[0].ACCNO3!='')
                             {
                                $('#account_number').append($("<option></option>").attr("value", data['Message'].account_list[0].ACCNO3).text(data['Message'].account_list[0].ACCNO3)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME3==  $("#bank_id").val() && data['Message'].account_list[0].ACCNO4!='')
                             {
                                $('#account_number').append($("<option></option>").attr("value", data['Message'].account_list[0].ACCNO4).text(data['Message'].account_list[0].ACCNO4)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME5==  $("#bank_id").val() && data['Message'].account_list[0].ACCNO5!='')
                             {
                                $('#account_number').append($("<option></option>").attr("value", data['Message'].account_list[0].ACCNO5).text(data['Message'].account_list[0].ACCNO5)); 
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
                
        });
        
        $("#mode").change(function(){
          $(".bank_id").hide();
          $(".vpa_id").hide();
          $(".neft_reference").hide();
          $(".mandateid").hide();
          $(".account_Id").hide();
          $(".ifsc_code").hide();
          $(".vpa_id").hide();
          
          if($( "#mode option:selected" ).val() == 'DIRECT'){
            $(".bank_id").show();
            $("#bank_id").attr('required','required');
            $(".account_Id").show();
            $("#account_number").attr('required','required');
            
            
          }
           if($( "#mode option:selected" ).val() == 'NODAL'){
            $(".bank_id").show();
            $("#bank_id").attr('required','required');
            $(".account_Id").show();
            $("#account_number").attr('required','required');
          }
          
          
          if($( "#mode option:selected" ).val() == 'OneTimeMandate'){
              $(".bank_id").show();
            $("#bank_id").attr('required','required');
            $(".account_Id").show();
            $("#account_number").attr('required','required');
            $(".mandateid").show();
            $("#mandateid").attr('required','required');
            
          }
          if($( "#mode option:selected" ).val() == 'UPI'){
              $(".bank_id").show();
            $("#bank_id").attr('required','required');
            $(".account_Id").show();
            $("#account_number").attr('required','required');
            $("#vpa_id").attr('required','required');
            $(".vpa_id").show();
          }
           if($( "#mode option:selected" ).val() == 'NEFT'){
              $(".bank_id").show();
            $("#bank_id").attr('required','required');
            $(".account_Id").show();
            $("#account_number").attr('required','required');
            $(".neft_reference").show();
            $("#neft_reference").attr('required','required');
            
          
          }
            $('#bank_id').empty().append('<option selected="selected" value="">Select Bank</option>');
            $('#account_number').empty().append('<option selected="selected" value="">Select Account</option>');

              $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/GetBankDetail');?>",
                  type: 'post',
                  data: {
                        order_id:$('#order_id').val()
                        },
                  dataType: 'json',
                  success: function(data)
                  { 
                      if(data['Status'])
                      {
                         if(data['Message'].account_list)
                         {
                             if(data['Message'].account_list[0].BANKNAME1!="")
                             {
                                $('#bank_id').append($("<option></option>").attr("value", data['Message'].account_list[0].BANKNAME1).text(data['Message'].account_list[0].BANKNAME1)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME2!="")
                             {
                                $('#bank_id').append($("<option></option>").attr("value", data['Message'].account_list[0].BANKNAME2).text(data['Message'].account_list[0].BANKNAME2)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME3!="")
                             {
                                $('#bank_id').append($("<option></option>").attr("value", data['Message'].account_list[0].BANKNAME3).text(data['Message'].account_list[0].BANKNAME3)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME3!="")
                             {
                                $('#bank_id').append($("<option></option>").attr("value", data['Message'].account_list[0].BANKNAME4).text(data['Message'].account_list[0].BANKNAME4)); 
                             }
                             if(data['Message'].account_list[0].BANKNAME5!="")
                             {
                                $('#bank_id').append($("<option></option>").attr("value", data['Message'].account_list[0].BANKNAME5).text(data['Message'].account_list[0].BANKNAME5)); 
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
                
          
          
        });
    });
    
</script>

          