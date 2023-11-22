<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Add_funds');?>">Add Funds</a></li>
                <li class="active">Edit Added Fund</li>
            </ol>
            <h1>Edit Added Fund</h1>
        </div>
        <div class="container">
            <form action="#" id="fund_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <input type="hidden" name="add_fund_id" id="add_fund_id" value="<?php echo $fund_data->add_fund_id; ?>" />
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="family_id" class="populate" required="required" id="family_id" style="width: 80%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>' <?php echo ($fund_data->family_id == $row->family_id)?'selected="selected"':''; ?>>
                                                <?php echo $row->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Date of Transaction</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="4" value="<?php echo isset($fund_data->transaction_date)?$fund_data->transaction_date:'';?>" class="form-control mask datepicker" id="transaction_date" data-inputmask="'alias':'date'" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 80%" tabindex="3">
                                        <option disabled selected value="">Select Client</option>
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>' <?php echo ($fund_data->client_id == $row->client_id)?'selected="selected"':''; ?>>
                                                <?php echo $row->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="<?php echo base_url("broker/Clients/add");?>" tabindex="2" class="btn btn-xs btn-inverse-alt"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Funds Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="amount" required="required" class="form-control" id="amount" tabindex="6"
                                           data-type="number" data-maxlength="16" value="<?php echo isset($fund_data->amount)?$fund_data->amount:'';?>"/>
                                </div>
                                <label class="col-sm-2 control-label">Bank Account</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="bank_account_id" class="populate" id="bank_account_id" style="width: 80%" tabindex="9">
                                        <option disabled selected value="">Select Account</option>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="10" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cheque No.</label>
                                <div class="col-md-4">
                                    <input type="text" name="cheque_no" class="form-control" id="cheque_no" tabindex="7"
                                           data-type="number" data-maxlength="10" value="<?php echo isset($fund_data->cheque_no)?$fund_data->cheque_no:'';?>"/>
                                </div>
                                <label class="col-sm-2 control-label">Bank Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="bank_name" class="form-control disabled" disabled id="bank_name" style="width: 80%;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cheque Date</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="cheque_date" tabindex="8" class="form-control mask datepicker"
                                           id="cheque_date" data-inputmask="'alias':'date'" value="<?php echo isset($fund_data->cheque_date)?$fund_data->cheque_date:'';?>"/>
                                </div>
                                <label class="col-sm-2 control-label">Branch Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="branch_name" class="form-control disabled" disabled id="branch_name" style="width: 80%;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4">
                                </div>
                                <label class="col-sm-2 control-label">Account No.</label>
                                <div class="col-md-4">
                                    <input type="text" name="account_no" class="form-control disabled" disabled id="account_no" style="width: 80%;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Broker Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4 no-border">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="1" tabindex="11" id="invest" name="shares_app">
                                        Invest in Shares
                                    </label>
                                </div>
                                <label class="col-sm-2 control-label">Broker Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="trading_broker_id" class="populate invest_enable" required="required" id="trading_broker_id" style="width: 80%" tabindex="12">
                                        <option disabled selected value="">Select Broker Name</option>
                                    </select>
                                    <a href="javascript:;" tabindex="13" class="btn btn-xs btn-inverse-alt invest_enable" onclick="add_trading(true)"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4 no-border">
                                </div>
                                <label class="col-sm-2 control-label">Client Code</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="client_code" required="required" id="client_code" class="form-control invest_enable" style="width: 80%" tabindex="14">
                                        <option disabled selected value="">Select Client Code</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Additional Notes</label>
                                <div class="col-md-8 no-border">
                                    <input type="hidden" name="add_notes" id="addNotesH">
                                    <textarea id="add_notes" style="width: 100%;" rows="3"><?php echo isset($fund_data->add_notes)?$fund_data->add_notes:'';?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="bottom-row navbar-fixed-bottom">
              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="34" onclick="addForm('<?php echo base_url("broker/Add_funds/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="35" onclick="disableBtn();"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="36" onclick="delete_add_fund();" data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="37" onclick="fund_submit();"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <?php //<button type="button" id="cancel" tabindex="38" onclick="window.location.href = '<?php echo base_url('broker/Add_funds');'"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>; ?>
    <button type="button" id="cancel" tabindex="38" onclick="enableBtn();"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="34" onclick="addForm('<?php echo base_url("broker/Add_funds/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="35" onclick="disableBtn();"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="36" onclick="delete_add_fund();" data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="37" onclick="fund_submit();"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <?php //<button type="button" id="cancel" tabindex="38" onclick="window.location.href = '<?php echo base_url('broker/Add_funds');'"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>; ?>
    <button type="button" id="cancel" tabindex="38" onclick="enableBtn();"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else
{
?>

<?php
}
?>


            </div>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<script type="text/javascript">
    $(function() {
        disableBtn();
        <?php if( $this->session->userdata('permissions')=="1" || $this->session->userdata('permissions')=="2") {?>
           enableBtn();
        <?php
          }
          else
          {
          ?>
            disableBtn();
          <?php
          }
          ?>
        $('.populate').select2({width: 'resolve'});
        $('#transaction_date').datepicker({format:"dd/mm/yyyy", endDate:'+0d'});
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
        $('.mask').inputmask();
        //trigger change events on select boxes/checkboxes to load data
        <?php echo isset($fund_data->client_id)?'$("#client_id").trigger("change");':'';?>
        <?php echo (isset($fund_data->shares_app) && $fund_data->shares_app == 1)?'$("#invest").attr("checked","checked").change();
            $(".invest_enable").attr("disabled",false);
            $(".invest_enable").removeClass("disabled");' : '$(".disabled").attr("disabled","disabled");
            $(".invest_enable").attr("disabled",true);
            $(".invest_enable").addClass("disabled");';?>

        //invest_toggle($('#invest'));

        //on invest checkbox click
        $('#invest').click(function() {
            invest_toggle(this);
        });

    });



    //on family change get clients
    $('#family_id').change(function()
    {
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, this.value, 'client_id', 'nominee', "", "");

        $("#client_code").html('<option disabled selected value="">Select Client Code</option>');
        $("#trading_broker_id").html('<option disabled selected value="">Select Broker Name</option>');
        $("#trading_broker_id").select2("val","");
        $("#client_id").select2("val","");
        $("#eq_amt").val("");
    });

    //on client change get trading_brokers
    $('#client_id').change(function()
    {
        if(!(this.value == '' || this.value == 0)) {
            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_family');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    if(data != 'fail') {
                        $("#family_id").select2("val",data['family_id']);
                    } else {
                        console.log("Unable to load family data! No clientID passed");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting family name by client',
                        type: 'error',
                        hide: true
                    });
                }
            });

            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_bank_accounts_dropdown');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    bank_accounts_data = data;
                    $("#bank_account_id").html('<option disabled selected value="">Select Account</option>')
                    var option = '<option disabled selected value="">Select Account</option>';
                    $.each(data, function(i, item){
                        option = option + "<option value="+data[i].account_id+">" +
                            ""+data[i].bank_name+" - "+data[i].branch+" - "+data[i].account_number +
                            "</option>";
                    });
                    $("#bank_account_id").html(option).change();
                    <?php //echo isset($fund_data->bank_account_id)?'$("#bank_account_id option[value='.$fund_data->bank_account_id.']").attr("selected","selected");':'';?>
                    <?php echo isset($fund_data->bank_account_id)?'$("#bank_account_id").select2("val","'.$fund_data->bank_account_id.'");':'';?>
                    <?php echo isset($fund_data->bank_account_id)?'$("#bank_account_id").trigger("change");':'';?>
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting details of bank account by client',
                        type: 'error',
                        hide: true
                    });
                }
            });

            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_trading_brokers');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    $("#client_code").html('<option disabled selected value="">Select Client Code</option>')
                    $("#trading_broker_id").select2("val","");
                    $("#eq_amt").val("");

                    var option = '<option disabled selected value="">Select Broker Name</option>';
                    $.each(data, function(i, item){
                        option = option + "<option value="+data[i].trading_broker_id+">"+data[i].trading_broker_name+"</option>";
                    });
                    $("#trading_broker_id").html(option).change();
                    <?php //echo isset($eq_data->trading_broker_id)?'$("#trading_broker_id").select2("val","'.$eq_data->trading_broker_id.'");':'';?>
                    <?php echo isset($fund_data->trading_broker_id)?'$("#trading_broker_id").select2("val","'.$fund_data->trading_broker_id.'");':'';?>
                    <?php echo isset($fund_data->trading_broker_id)?'$("#trading_broker_id").trigger("change");':'';?>
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
        }
    });

    //on trading_broker change get client codes
    $('#trading_broker_id').change(function()
    {
        var clientID = $("#client_id").val();
        if(!(this.value == '' || this.value == 0)) {
            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_trading_broker_client_code');?>',
                type: 'post',
                data: {trading_brokerID: this.value, clientID: clientID},
                dataType: 'json',
                success: function(data) {
                    $("#client_code").html('<option disabled selected value="">Select Client Code</option>');
                    $("#eq_amt").val("");

                    var option = '<option disabled selected value="">Select Client Code</option>';
                    $.each(data, function(i, item){
                        option = option + "<option value="+data[i].client_code+">"+data[i].client_code+"</option>";
                    });
                    $("#client_code").html(option);
                    <?php echo isset($fund_data->client_code)?'$("#client_code option[value='.$fund_data->client_code.']").attr("selected","selected");':'';?>
                    <?php echo isset($fund_data->client_code)?'$("#client_code").trigger("change");':'';?>
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting details from broker',
                        type: 'error',
                        hide: true
                    });
                }
            });
        }
    });

    //on bank account selection, show its details
    $("#bank_account_id").change(function() {
        if(this.value != "" && this.value != null)
        {
            currentAccountID = this.value;
            $.each(bank_accounts_data, function(i, item){
                if(currentAccountID == bank_accounts_data[i].account_id)
                {
                    $("#bank_name").val(bank_accounts_data[i].bank_name);
                    $("#branch_name").val(bank_accounts_data[i].branch);
                    $("#account_no").val(bank_accounts_data[i].account_number);
                }
            });

            <?php echo isset($fund_data->bank_account_id)?'$("#bank_account_id").select2("val","'.$fund_data->bank_account_id.'");':'';?>
            <?php //echo isset($fund_data->bank_account_id)?'$("#bank_account_id").trigger("change");':'';?>
        }
        else
        {
            $("#bank_name").val('');
            $("#branch_name").val('');
            $("#account_no").val('');
        }
    });

    //function to toggle enable/disable on checkbox
    function invest_toggle(chkbox) {
        if(chkbox.checked) {
            $('.invest_enable').attr('disabled',false);
            $('.invest_enable').removeClass('disabled');
            $('#fund_form').parsley('addItem', '#trading_broker_id');
            $('#fund_form').parsley('addItem', '#client_code');
            $("#trading_broker_id").parsley('addConstraint',{required: true});
            $("#client_code").parsley('addConstraint',{required: true});
        } else {
            $('.invest_enable').attr('disabled',true);
            $('.invest_enable').addClass('disabled');
            $("#trading_broker_id").parsley('removeConstraint','required');
            $("#client_code").parsley('removeConstraint','required');
        }
    }

    //update fund details in database
    function fund_submit()
    {
        var dateValid = true;
        var transDate = process($('#transaction_date').val());
        var chqDate = process($('#cheque_date').val());
        if(transDate < chqDate)
        {
            $.pnotify({
                title: 'Error!',
                text: "Please check the Date of Transaction and Cheque Date. Cheque Date can't be greater than Date of Transaction",
                type: 'error',
                hide: true
            });
            dateValid = false;
        }
        $("#fund_form").parsley("destroy");
        var valid = $("#fund_form").parsley("validate");
        if(valid && dateValid)
        {
            var notes = $("#add_notes").val();
            if(notes != "" && notes != null) {
                $("#addNotesH").val($("#add_notes").val());
            }
            var form_data = $('#fund_form').serialize();
            $.ajax({
                url: '<?php echo site_url('broker/Add_funds/update_funds');?>',
                type: 'post',
                data: form_data,
                dataType: 'json',
                success:function(data)
                {
                    if(data['type'] == 'success')
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
                }
            });
        }
    }

    function delete_add_fund()
    {
        var id = $("#add_fund_id").val();
        if(id != '' && id != null & id != 0) {
            bootbox.confirm("Are you sure you want to delete this Fund entry?", function(result) {
                if(result)
                {
                    // ajax delete data to database
                    $.ajax({
                        url : "<?php echo site_url('broker/Add_funds/delete_add_fund');?>",
                        type: "POST",
                        data:{id: id},
                        dataType: "JSON",
                        success: function(data)
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                history: true
                            });

                            window.location.href = '<?php echo base_url('broker/Add_funds');?>'
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            bootbox.alert('Error deleting data');
                        }
                    });
                }
            });
        } else {
            bootbox.alert("No ID found! Cannot delete this entry.");
        }
    }

</script>
