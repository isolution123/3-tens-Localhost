<div id="page-content" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <h1>Equity/Shares Report</h1>
        </div>
        <div class="container">
            <form action="#" id="equity_report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h4>Equity/Shares Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3  no-border">
                                    <select name="famName" class="populate" multiple="multiple" id="famName" style="width: 100%" tabindex="1">
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-3">
                                    <select name="client_id" class="populate" multiple="multiple" id="client_id" style="width: 100%" tabindex="2">
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <label class="col-sm-2 control-label">Broker Code</label>
                                <div class="col-md-3">
                                    <select name="client_code" class="populate" multiple="multiple" id="client_code" style="width: 100%" tabindex="2">

                                    </select>
                                </div>
                                
                                <?php
                                        //show yearwise report option only to iSolutions and V-Financial services
                                if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0204' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174')
                                {?>
                                    <label class="col-sm-2 control-label">Date</label>
                                    <div class="col-md-3">
                                         <input type="text"  name="reportDate"  tabindex="5" 
                                          required="required" class="form-control date mask" 
                                          data-inputmask="'alias':'date'" 
                                        id="reportDate" value="<?php echo date('d/m/Y');?>">
                                    </div>
                                <?php }?>
                            </div>
                            <div class="form-group" style="">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-md-3">
                                    <div class="radio block">
                                        <label>
                                        <input type="radio" tabindex="6" name="cheque" id="without_cheque" value="0" checked>
                                        Without Cheque Details</label>
                                    </div>
                                    <div class="radio block">
                                        <label>
                                        <input type="radio" tabindex="7" name="cheque" id="with_cheque" value="1">
                                        With Cheque Details</label>
                                    </div>
                                </div>
                               
                            <?php
                                        //show yearwise report option only to iSolutions and V-Financial services
                                if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0204' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174')
                                {?>
                                       <div class="col-md-3">
                                        <div class="radio block">
                                        <input type="checkbox" id="apc" name="apc" value="1">
                                        <label for="vehicle1"> With APC</label>
                                </div>
                                <?php }?>
                        
                              
                             
                                </div>
                            </div>
                            <div class="form-group" style="">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-md-6">
                                    <div class="radio block">
                                        <label>
                                            <input type="radio" tabindex="4" name="xirr" id="without_xirr" value="0" checked>
                                            Without XIRR</label>
                                    </div>
                                    <div class="radio block">
                                        <label>
                                            <input type="radio" tabindex="5" name="xirr" id="with_xirr" value="1">
                                            With XIRR</label>
                                    </div>
                                </div>
                               
                            </div>
                            
                            <div class="form-group" style="text-align: center">
                                <button type="button" class="btn btn-success ladda-button" tabindex="8" id="equity_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(function() {
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
          $('#reportDate').datepicker({
            format:"dd/mm/yyyy", 
            endDate:new Date(),
            startDate:"02/04/2021" });
        //disable xirr option on page load
        var xirr = $("[name='xirr']");
        xirr.prop("disabled",true);
        xirr.addClass("disabled");

        //$('.date').datepicker({format: 'dd/mm/yyyy', autoClose: true}).inputmask() ;
        $("#famName").select2({placeholder: 'Select Family', maximumSelectionSize: 1});
        $("#client_id").select2({placeholder: 'Select Client', maximumSelectionSize: 1});
        $("#client_code").select2({placeholder: 'Select Broker Code', maximumSelectionSize: 1});
        $("#equity_submit").click(function(){
            var famName = $('#famName').val();
            var client_id = $('#client_id').val();
            var client_code = $('#client_code').val();
            var reportDate = $('#reportDate').datepicker({ dateFormat: 'dd-mm-yy' }).val();
            var data = []; //for holding the data
            var l = Ladda.create(button);
            l.start();
            if (famName != null || (client_id != null && client_id.length > 0)) {
                $.ajax({
                    url: $("#apc").is(':checked')? "<?php echo site_url('broker/Equity/get_equity_report_with_apc');?>" : "<?php echo site_url('broker/Equity/get_equity_report');?>",
                    type: 'post',
                    data: $("#equity_report_form").serialize(),
                    dataType: 'json',
                    success: function(data)
                    {
                        if(data['Status'])
                        {
                            if($("#apc").is(':checked'))
                            {
                            window.open("<?php echo site_url('broker/Reports/get_equity_report_with_apc');?>", '_blank')    
                            }
                            else
                            {
                            window.open("<?php echo site_url('broker/Reports/get_equity_report');?>", '_blank')
                            }
                        }
                        else
                        {
                            console.log(data);
                            bootbox.alert(data['message']);
                        }
                        l.stop();
                    },
                    error: function(jqXRR, textStatus, errorThrown)
                    {
                        console.log(jqXRR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            else
            {
                bootbox.alert("Please select either family or client to view Reports");
                l.stop();
            }
        });

        //on family change get clients
        $('#famName').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', 'nominee', "", "");
        });

        //on family change get clients
        $('#client_id').change(function()
        {
            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_codes_client');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    $("#client_code").html('<option disabled selected value="">Select Broker Code</option>')
                    $("#client_code").select2("val","");
                    $("#eq_amt").val("");

                    var option = '<option disabled selected value="">Select Broker Code</option>';
                    $.each(data, function(i, item){
                        option = option + "<option value="+data[i].client_code+">"+data[i].client_code+"</option>";
                    });
                    $("#client_code").html(option);
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting details of broker by client',
                        type: 'error',
                        hide: true
                    });
                }
            });
        });

        //enable/disable xirr option on cheque option
        $("[name='cheque']").on("click", function() {
            if(this.value == "0") {
                var xirr = $("[name='xirr']");
                xirr.prop("disabled",true);
                xirr.addClass("disabled");
            } else {
                var xirr = $("[name='xirr']");
                xirr.prop("disabled",false);
                xirr.removeClass("disabled");
            }
        });

    });
</script>