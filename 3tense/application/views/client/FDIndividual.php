

<?php include "header-focused.php"; ?>
<br/><br/>

<style>
    label 
    {
        text-align:left;
    }
</style>
<div class="container body">
    <div  class="row right_col" role="main" style="margin-top:15px;padding:10px 10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center p-0 mt-3 mb-2">
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 style="">Individual Fixed Deposit on <b><?php print_r($fd_detail[0]->CompanyName); ?> </b> with Rate <?php print_r($fd_detail[0]->$RateType);?>%</h4>
                    <input type='hidden' id='companyName' value='<?php print_r($fd_detail[0]->CompanyName); ?>' />
                    <input type='hidden' id='rate' value='<?php print_r($fd_detail[0]->$RateType); ?>' />
                    <input type='hidden' id='RateType' value='<?php echo $RateType; ?>' />
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        
                    
                    <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Investor Name</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <select name="InvesterName" class="form-control" id="InvesterName" tabindex="1">
                                <option value=''>---Select---</option>
                                <?php foreach($account_list as $row):?>
                                    <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Date Of Birth</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="date" id="DOB"  class=" form-control " tabindex="2" />
                        </div>
                    </div>
                    </div>
                     <div class="row">
                      <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Pancard</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" id="Pancard"  class=" form-control" tabindex="3" />
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Address</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <textarea id="Address"  class=" form-control" tabindex="4"> </textarea>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                   <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Email Id</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="email" id="EmailId"  class=" form-control" tabindex="5" />
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Mobile No</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" id="MobileNo"  class=" form-control" tabindex="6" />
                        </div>
                    </div>
                    </div>
                    <div class="row"> 
                   <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Holding</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <select name="Holding" class="form-control" id="Holding" tabindex="7">
                                <option value=''>---Select---</option>
                                <option value='Single'>Single</option>
                                <option value='Joint'>Joint</option>
                                <option value='Anyone or Survivor'>Anyone or Survivor</option>
                                
                            </select>
                        </div>
                    </div>
                    </div>
                    <div id='IsJointHolder' style='display:none'>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >2nd Holder Name</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select name="2ndHolderName" class="form-control" id="2ndHolderName">
                                        <option value=''>---Select---</option>
                                        <?php foreach($account_list as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                   <span style='font-size: 12px'> <b>Edit second holder details by changes name in below box.</b></span>
                                    <input type="text" id="2ndHolderNametext"  class=" form-control "/>
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >2nd Holder DOB</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="date" id="2ndHolderDOB"  class=" form-control "/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >2nd Holder Pancard</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" id="2ndHolderPancard"  class=" form-control" tabindex="3" />
                                </div>
                            </div>
                              <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >2nd Holder Address</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <textarea id="2ndHolderAddress"  class=" form-control" tabindex="4"> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >3rd Holder Name</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select name="3rdHolderName" class="form-control" id="3rdHolderName" tabindex="8">
                                        <option value=''>---Select---</option>
                                        <?php foreach($account_list as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                      <span style='font-size: 12px'> <b>Edit 3rd holder details by changes name in below box.</b></span>
                                    <input type="text" id="3rdHolderNametext"  class=" form-control "/>
                                </div>
                            </div>
                             <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >3rd Holder DOB</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="date" id="3rdHolderDOB"  class=" form-control " tabindex="2" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >3rd Holder Pancard</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" id="3rdHolderPancard"  class=" form-control" tabindex="3" />
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >3rd Holder Address</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <textarea id="3rdHolderAddress"  class=" form-control" tabindex="4"> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Nominee Name</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <select name="NomineeName" class="form-control" id="NomineeName">
                                    <option value=''>---Select---</option>
                                    <?php foreach($account_list as $row):?>
                                        <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                  <span style='font-size: 12px'> <b>Edit Nominee details by changes name in below box.</b></span>
                                    <input type="text" id="NomineeNametext"  class=" form-control "/>
                            </div>
                        </div>
                         <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Nominee DOB</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="date" id="NomineeDOB"  class=" form-control "/>
                            </div>
                        </div>
                    </div>
                    <div id='IfNomineeMinor' style='display:none'>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Gaurdian Name</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select name="GaurdianName" class="form-control" id="GaurdianName">
                                        <option value=''>---Select---</option>
                                        <?php foreach($account_list as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                          <span style='font-size: 12px'> <b>Edit Gaurdian details by changes name in below box.</b></span>
                                    <input type="text" id="GaurdianNametext"  class=" form-control "/>
                                </div>
                            </div>
                             <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Gaurdian DOB</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="date" id="GaurdianNameDOB"  class=" form-control "/>
                                </div>
                            </div>
                        </div>
                    </div>
                      <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Nominee Relation</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                  <input type="text" id="Relation" class=" form-control "/>
                                </div>
                            </div>
                        
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Tenure(Months)</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="number" id="Tenure" min="1" value="<?php echo $fd_detail[0]->Period ; ?>"  class=" form-control " disabled/>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Scheme</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <select name="scheme" class="form-control" id="scheme">
                                    <option value=''>---Select---</option>
                                    <option value='Cumulative'>Cumulative</option>
                                    <option value='Non Cumulative'>Non Cumulative</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 col-xs-12" id="divInterestFrequency" style='display:none'>
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Interest Frequency</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <select name="InterestFrequency" class="form-control" id="InterestFrequency">
                                    <option value=''>---Select---</option>
                                    <option value='Monthly'>Monthly</option>
                                    <option value='Quarterly'>Quarterly</option>
                                    <option value='Half-Yearly'>Half-Yearly</option>
                                    <option value='Yearly'>Yearly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Auto Renewal/Refund</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <select name="renewalType" class="form-control" id="renewalType">
                                    <option value=''>---Select---</option>
                                    <option value='Auto Renewal'>Auto Renewal</option>
                                    <option value='Refund'>Refund</option>
                                </select>
                            </div>
                        </div>
                        
                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Preferred Date and Time for Cheque Collection</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="datetime-local" id="chequeCollectiondt"  class=" form-control "/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-sm-4  col-sm-4 col-xs-12 control-label" >&nbsp;</label>
                        <div class="col-md-4  col-sm-4 col-xs-12">
                              <input type="button" id="FDsubmit" value="Submit" onclick="submitfd()" class="btn btn-primary">
                              <a  class="btn btn-default" href='<?php echo site_url('client/PurchaseMutualFund/FixDeposit');?>'>Back</a>
                        </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<script type="application/javascript">
    $(function(){
        var dt= new Date();
        var numberOfDaysToAdd = 2;
        dt.setDate(dt.getDate() + numberOfDaysToAdd); 
        var yyyy = dt.getFullYear().toString();
        var mm = (dt.getMonth()+1).toString(); // getMonth() is zero-based
        var dd  = dt.getDate().toString();
        var min = yyyy +'-'+ (mm[1]?mm:"0"+mm[0]) +'-'+ (dd[1]?dd:"0"+dd[0]); // padding
        $('#chequeCollectiondt').prop('min', min+'T00:00');
        
        
        setTimeout(function(){
            if($('#RateType').val()=='Cumulative')
            {
                
                $('#scheme option[value=Cumulative]').attr('selected','selected');
            }
            else
            {
              $('#scheme option[value="Non Cumulative"]').attr('selected','selected');
            }
            $('#scheme option[value="Non Cumulative"]').change();
        },
        100)


$('#DOB').on('change', function() {
    
      var Ndate=$(this).val();
              var dt= new Date();
           if(calcDate(dt,new Date(Ndate))<18)
            {
                $('#IfNomineeMinor').show();
            }
            else
            {
                    $('#IfNomineeMinor').hide();
            }
        });
        $('#NomineeDOB').on('change', function() {
         
            
           var Ndate=$(this).val();
              var dt= new Date();
           if(calcDate(dt,new Date(Ndate))<18)
            {
                $('#IfNomineeMinor').show();
            }
            else
            {
                    $('#IfNomineeMinor').hide();
            }
        });
        
        $('#scheme').on('change', function() {
        
            if($(this).val()=='Non Cumulative')
            {
                $('#divInterestFrequency').show();
            }
            else
            {
                $('#divInterestFrequency').hide();
            }
        });
        
        $('#Holding').on('change', function() {
           
            if($(this).val()=='Single')
            {
                $('#IsJointHolder').hide();
            }
            else
            {
                $('#IsJointHolder').show();
            }
        })
       
        $('#InvesterName').on('change', function() {
            if($(this).val()!='')
            {
                 getclientdetail($(this).val(),1);
            }
            else
            {
                  $('#DOB').val('');
                   $('#Pancard').val('');
                   $('#Address').val('');
                   $('#EmailId').val('');
                   $('#MobileNo').val('');
            }
        });
      
        $('#2ndHolderName').on('change', function() {
            
            if($(this).val()!='')
            {
                 getclientdetail($(this).val(),2);
            }
            else
            {
                $('#2ndHolderDOB').val('');
                $('#2ndHolderPancard').val('');
                $('#2ndHolderAddress').val('');
            }
             $("#2ndHolderNametext").val($("#2ndHolderName").find('option:selected').text());
        });
        $("#2ndHolderNametext").change(function(){
              $('#2ndHolderDOB').val('');
                $('#2ndHolderPancard').val('');
                $('#2ndHolderAddress').val('');
        });
       
        $('#3rdHolderName').on('change', function() {
            if($(this).val()!='')
            {
                 getclientdetail($(this).val(),3);
            }
            else
            {
                $('#3rdHolderDOB').val('');
                $('#3rdHolderPancard').val('');
                $('#3rdHolderAddress').val('');
            }
             $("#3rdHolderNametext").val($("#3rdHolderName").find('option:selected').text());
        });
        $("#3rdHolderNametext").change(function(){
                $('#3rdHolderDOB').val('');
                $('#3rdHolderPancard').val('');
                $('#3rdHolderAddress').val('');
        });
       
        $('#NomineeName').on('change', function() {
            if($(this).val()!='')
            {
                 getclientdetail($(this).val(),4);
            }
            else
            {
                $('#NomineeDOB').val('');
                
            }
             $("#NomineeNametext").val($("#NomineeName").find('option:selected').text());
        });
           $("#NomineeNametext").change(function(){
                $('#NomineeDOB').val('');
               
        });
       
      
        $('#GaurdianName').on('change', function() {
            if($(this).val()!='')
            {
                 getclientdetail($(this).val(),5);
            }
            else
            {
                $('#GaurdianNameDOB').val('');
                
            }
            $("#GaurdianNametext").val($("#GaurdianName").find('option:selected').text());
        });
         $("#GaurdianNametext").change(function(){
                $('#GaurdianNameDOB').val('');
               
        });
        
        
        function getclientdetail(clientid,flag)
        {
            $.ajax({
                url: "<?php echo site_url('client/PurchaseMutualFund/GeClientDetail'); ?>",
                dataType: 'html',
                type: "POST",
                data: {'client_id':clientid},
                success: function (response) {
                    var data=JSON.parse(response);
                    if(data.Status==1)
                    {
                        var obj=data.Message;
                        if(obj.length>0)
                        {
                            var obj1=obj[0];
                           if(flag==1)
                           {
                               
                               $('#DOB').val(obj1.dob);
                               $('#DOB').change();
                               
                               $('#Pancard').val(obj1.pan_no);
                               $('#Address').val(obj1.add_flat+' '+obj1.add_street+' '+obj1.add_area+' '+obj1.add_city);
                               $('#EmailId').val(obj1.email_id);
                               $('#MobileNo').val(obj1.mobile);
                               
                           }
                           else if(flag==2)
                           {
                                   $('#2ndHolderDOB').val(obj1.dob);
                               $('#2ndHolderPancard').val(obj1.pan_no);
                               $('#2ndHolderAddress').val(obj1.add_flat+' '+obj1.add_street+' '+obj1.add_area+' '+obj1.add_city);
                               
                           }
                            else if(flag==3)
                           {
                                   $('#3rdHolderDOB').val(obj1.dob);
                               $('#3rdHolderPancard').val(obj1.pan_no);
                               $('#3rdHolderAddress').val(obj1.add_flat+' '+obj1.add_street+' '+obj1.add_area+' '+obj1.add_city);
                               
                           }
                            else if(flag==4)
                           {
                                   $('#NomineeDOB').val(obj1.dob);
                               
                               
                           }
                              else if(flag==5)
                           {
                                   $('#GaurdianNameDOB').val(obj1.dob);
                               
                               
                           }
                           
                        }
                    }
                }
            }); 
        } 
        
        function calcDate(date1,date2) {
            var diff = Math.floor(date1.getTime() - date2.getTime());
            var day = 1000 * 60 * 60 * 24;
        
            var days = Math.floor(diff/day);
            var months = Math.floor(days/31);
            var years = Math.floor(months/12);
            return years
        }

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
    
    });
    function submitfd(){
            var Iserror=0;
              
            if(!$('#InvesterName').val())
            {
                bootbox.alert("Please select Invester name.");
                
            }
            else if(!$('#DOB').val())
            {
                bootbox.alert("Please select DOB.");
            }
            else if(!$('#Pancard').val())
            {
                bootbox.alert("Please enter Pancard.");
            }
            else if(!$('#Address').val())
            {
                bootbox.alert("Please enter Address.");
            }
            else if(!$('#MobileNo').val())
            {
                bootbox.alert("Please enter MobileNo.");
            }
               else if(!$('#Holding').val())
            {
                bootbox.alert("Please select Holding.");
            }
            else if(!$('#Tenure').val())
            {
                bootbox.alert("Please enter Tenure.");
            }
            else if(!$('#scheme').val())
            {
                bootbox.alert("Please select scheme.");
            }
            else if($('#scheme').val()=='Non Cumulative' && !$('#InterestFrequency').val())
            {
                bootbox.alert("Please select Interest Frequency.");
            }
            else if(!$('#renewalType').val())
            {
                bootbox.alert("Please select Renewal Type.");
            }
            else if(!$('#chequeCollectiondt').val())
            {
                bootbox.alert("Please select Preferred Date and Time for Cheque Collection.");
            }
            else
            {
                var data={
                    InvesterName:$("#InvesterName").find('option:selected').text(),
                    DOB:$('#DOB').val(),
                    Pancard:$('#Pancard').val().trim(),
                    Address:$("#Address").val().trim(),
                    EmailId:$("#EmailId").val().trim(),
                    MobileNo:$("#MobileNo").val().trim(),
                    Holding:$("#Holding").val().trim(),
                    HolderName2:$("#2ndHolderNametext").val(),
                    HolderDOB2:$("#2ndHolderDOB").val().trim(),
                    HolderPancard2:$("#2ndHolderPancard").val().trim(),
                    HolderAddress2:$("#2ndHolderAddress").val().trim(),
                    HolderName3:$("#3rdHolderNametext").val(),
                    HolderDOB3:$("#3rdHolderDOB").val().trim(),
                    HolderPancard3:$("#3rdHolderPancard").val().trim(),
                    HolderAddress3:$("#3rdHolderAddress").val().trim(),
                    NomineeName:$("#NomineeNametext").val(),
                    NomineeDOB:$("#NomineeDOB").val().trim(),
                    GaurdianName:$("#GaurdianNametext").val(),
                    GaurdianNameDOB:$("#GaurdianNameDOB").val().trim(),
                    Relation:$("#Relation").val().trim(),
                    Tenure:$("#Tenure").val().trim(),
                    scheme:$("#scheme").val().trim(),
                    InterestFrequency:$("#InterestFrequency").val().trim(),
                    renewalType:$("#renewalType").val().trim(),
                    chequeCollectiondt:new Date($("#chequeCollectiondt").val().trim()),
                    companyName:$("#companyName").val(),
                    rate:$("#rate").val(),
                    RateType:$("#RateType").val()
                };
                $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/SubmitFD');?>",
                  type: 'post',
                  data: data,
                  dataType: 'json',
                  success: function(Responsedata)
                  { 
                      debugger;
                      if(Responsedata['Status']==1)
                      {
                        bootbox.alert(Responsedata['Message']);
                        clearForm();
                      }
                      else
                      {
                        bootbox.alert(Responsedata['Message']);;
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
    function clearForm()
    {
        $("#InvesterName").val('');
        $('#DOB').val('');
        $('#Pancard').val('');
        $("#Address").val('');
        $("#EmailId").val('');
        $("#MobileNo").val('')
        $("#Holding").val('');
        $("#2ndHolderName").val('');
        $("#2ndHolderDOB").val('');
        $("#2ndHolderPancard").val('');
        $("#2ndHolderAddress").val('');
        $("#3rdHolderName").val('');
        $("#3rdHolderDOB").val('');
        $("#3rdHolderPancard").val('');
        $("#3rdHolderAddress").val('');
        $("#NomineeName").val('');
        $("#NomineeDOB").val('');
        $("#GaurdianName").val('');
        $("#GaurdianNameDOB").val('');
        $("#Relation").val('');
        $("#Tenure").val('');
        $("#scheme").val('');
        
        
        $("#InterestFrequency").val('');
        $("#renewalType").val('');
        $("#chequeCollectiondt").val('');
        
        $("#FDsubmit").removeAttr('disabled');
    }
</script>
          
