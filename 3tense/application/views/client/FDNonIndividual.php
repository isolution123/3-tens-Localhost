

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
                    <h4 style="">Non-Individual Fixed Deposit on <b><?php print_r($fd_detail[0]->CompanyName); ?> </b> with Rate <?php print_r($fd_detail[0]->$RateType);?>%</h4>
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
                            <select name="InvesterName" class="form-control" id="InvesterName" >
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
                            <input type="date" id="DOB"  class=" form-control " />
                        </div>
                    </div>
                    </div>
                     <div class="row">
                     <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Pancard</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" id="Pancard"  class=" form-control" />
                        </div>
                    </div>
                       <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Address</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <textarea id="Address"  class=" form-control"> </textarea>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                     <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Email Id</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="email" id="EmailId"  class=" form-control" />
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-sm-12 col-xs-12">
                        <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Mobile No</label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="text" id="MobileNo"  class=" form-control"  />
                        </div>
                    </div>
                    </div>
                <div class="row">
                      <div id="accordioninpanel" class="accordion-group">
                        <div class="accordion-item panel panel-info">
                          <div class="panel-heading panel-info"  style='text-align:left'>
                             
                               <a class="accordion-title arrow-toggle"  data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinOne">
                                  Coparceners/Partners 1
                                </a>
                                
                             
                            </div>
                            <div id="collapseinOne" class="collapse">
                                <div class="panel panel-body">
                                    <div class="accordion-body">
                                        <div class="row">
                                           <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Name</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                      <input type="text" id="Partners1Name"  class=" form-control "/>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >DOB</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="date" id="Partners1DOB"  class=" form-control "/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Pancard</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners1Pancard"  class=" form-control"  />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label">Address</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <textarea id="Partners1Address"  class=" form-control" > </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Email Id</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners1EmailId"  class=" form-control"  />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Mobile No</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    
                                                         <input type="text" id="Partners1MobileNo"  class=" form-control"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item panel panel-info">
                          <div class="panel-heading panel-info"  style='text-align:left'>
                             <span>
                               <a class="accordion-title arrow-toggle"  data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseintwo">
                                  Coparceners/Partners 2
                                </a>
                                
                              </span>
                            </div>
                            <div id="collapseintwo" class="collapse">
                                <div class="panel panel-body">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Name</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                      <input type="text" id="Partners2Name"  class=" form-control "/>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >DOB</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="date" id="Partners2DOB"  class=" form-control "/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Pancard</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners2Pancard"  class=" form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Address</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <textarea id="Partners2Address"  class=" form-control" > </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Email Id</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners2EmailId"  class=" form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Mobile No</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    
                                                        <input type="text" id="Partners2MobileNo"  class=" form-control"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item panel panel-info">
                          <div class="panel-heading panel-info"  style='text-align:left'>
                             <span>
                               <a class="accordion-title arrow-toggle"  data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinThree">
                                  Coparceners/Partners 3
                                </a>
                                
                              </span>
                            </div>
                            <div id="collapseinThree" class="collapse">
                                <div class="panel panel-body">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Name</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                      <input type="text" id="Partners3Name"  class=" form-control "/>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >DOB</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="date" id="Partners3DOB"  class=" form-control "/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Pancard</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners3Pancard"  class=" form-control"  />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Address</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <textarea id="Partners3Address"  class=" form-control"> </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Email Id</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners3EmailId"  class=" form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                                <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Mobile No</label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" id="Partners3MobileNo"  class=" form-control"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Annual Turnover as of 31st March (not less than a year)</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="number" id="AnnualTurnover"  class=" form-control " />
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Tenure(Months)</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="number" id="Tenure"  value='<?php echo $fd_detail[0]->Period ; ?>' class=" form-control "  disabled/>
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
                
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
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
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >15G</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <select name="S15G" class="form-control" id="S15G">
                                    <option value=''>---Select---</option>
                                  
                                    <option value='Yes'>Yes</option>
                                    <option value='No'>No</option>
                                  
                                </select>
                            </div>
                        </div>
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
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >Preferred Date and Time for Cheque Collection</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="datetime-local" id="chequeCollectiondt"  class=" form-control "/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label class="col-md-4 col-sm-4 col-xs-12  control-label" >&nbsp;</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
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
                
                $('#scheme option[value=Cumulative]').attr('selected','selected').change();
                
            }
            else
            {
              $('#scheme option[value="Non Cumulative"]').attr('selected','selected').change();
            }
              $('#scheme option[value="Non Cumulative"]').change();
        },
        100)
        
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
               else if(!$('#AnnualTurnover').val())
            {
                bootbox.alert("Please enter Annual Turnover.");
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
            else if(!$('#S15G').val())
            {
                bootbox.alert("Please select 15G.");
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
                   
                    Partners1Name:$("#Partners1Name").val().trim(),
                    Partners1DOB:$("#Partners1DOB").val().trim(),
                    Partners1Pancard:$("#Partners1Pancard").val().trim(),
                    Partners1Address:$("#Partners1Address").val().trim(),
                    Partners1EmailId:$("#Partners1EmailId").val().trim(),
                    Partners1MobileNo:$("#Partners1MobileNo").val().trim(),
                    
                    Partners2Name:$("#Partners2Name").val().trim(),
                    Partners2DOB:$("#Partners2DOB").val().trim(),
                    Partners2Pancard:$("#Partners2Pancard").val().trim(),
                    Partners2Address:$("#Partners2Address").val().trim(),
                    Partners2EmailId:$("#Partners2EmailId").val().trim(),
                    Partners2MobileNo:$("#Partners2MobileNo").val().trim(),
                    
                    Partners3Name:$("#Partners3Name").val().trim(),
                    Partners3DOB:$("#Partners3DOB").val().trim(),
                    Partners3Pancard:$("#Partners3Pancard").val().trim(),
                    Partners3Address:$("#Partners3Address").val().trim(),
                    Partners3EmailId:$("#Partners3EmailId").val().trim(),
                    Partners3MobileNo:$("#Partners3MobileNo").val().trim(),
                    
                    AnnualTurnover:$("#AnnualTurnover").val().trim(),
                    Tenure:$("#Tenure").val().trim(),
                    scheme:$("#scheme").val().trim(),
                    InterestFrequency:$("#InterestFrequency").val().trim(),
                    S15G:$("#S15G").val().trim(),
                    renewalType:$("#renewalType").val().trim(),
                    chequeCollectiondt:new Date($("#chequeCollectiondt").val().trim()),
                    companyName:$("#companyName").val(),
                    rate:$("#rate").val(),
                    RateType:$("#RateType").val()
                    
                };
                $.ajax({
                  url: "<?php echo site_url('client/PurchaseMutualFund/SubmitFDNonIndividual');?>",
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
        $("#MobileNo").val('');
        
        
        $("#Partners1Name").val('');
        $("#Partners1DOB").val('');
        $("#Partners1Pancard").val('');
        $("#Partners1Address").val('');
        $("#Partners1EmailId").val('');
        $("#Partners1MobileNo").val('');
        
        $("#Partners2Name").val('');
        $("#Partners2DOB").val('');
        $("#Partners2Pancard").val('');
        $("#Partners2Address").val('');
        $("#Partners2EmailId").val('');
        $("#Partners2MobileNo").val('');
        
        $("#Partners3Name").val('');
        $("#Partners3DOB").val('');
        $("#Partners3Pancard").val('');
        $("#Partners3Address").val('');
        $("#Partners3EmailId").val('');
        $("#Partners3MobileNo").val('');
          
        
        $("#AnnualTurnover").val('');
        $("#Tenure").val('');
        $("#scheme").val('');
        $("#InterestFrequency").val('');
        $("#S15G").val('');
        $("#renewalType").val('');
        $("#chequeCollectiondt").val('');
        $("#companyName").val('');
        $("#rate").val('');
        
        $("#FDsubmit").removeAttr('disabled');
    }
</script>
          
