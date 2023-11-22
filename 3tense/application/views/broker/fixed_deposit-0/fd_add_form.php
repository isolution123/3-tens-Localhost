<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Fixed_deposits');?>">Fixed Deposit</a></li>
                <li class="active">Add Fixed Deposit</li>
            </ol>
            <h1>Add Fixed Deposit</h1>
        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="#" id="fd_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
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
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Date of Transaction</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="4" value="<?php echo date('d/m/Y')?>" class="form-control date" id="transaction_date" data-inputmask="'alias':'date'" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 100%" tabindex="3">
                                        <option disabled selected value="">Select Client</option>
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Investment Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Type of Investment</label>
                                <div class="col-md-4  no-border add-new-btn">
                                    <input type="hidden" id="fd_inv_name" name="fd_inv_name">
                                    <select name="fd_inv_id" class="populate" required="required" id="fd_inv_id" style="width: 80%" tabindex="5">
                                        <option disabled selected value="">Select Investment Type</option>
                                        <?php foreach($invTypes as $row):?>
                                            <option value='<?php echo $row->fd_inv_id; ?>'><?php echo $row->fd_inv_type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="6" class="btn btn-xs btn-inverse-alt" onclick="add_fd_inv(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Company Name</label>
                                <div class="col-md-4 add-new-btn">
                                    <input type="hidden" id="fd_comp_name" name="fd_comp_name">
                                    <select name="fd_comp_id" class="populate" required="required" id="fd_comp_id" style="width: 80%" tabindex="7">
                                        <option disabled selected value="">Select Company Name</option>
                                        <?php foreach($companies as $row):?>
                                            <option value='<?php echo $row->fd_comp_id; ?>'><?php echo $row->fd_comp_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="8" class="btn btn-xs btn-inverse-alt" onclick="add_fd_comp(true)"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Reference Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="ref_number" required="required" class="form-control" id="ref_number" tabindex="9">
                                </div>
                                <label class="col-sm-2 control-label">Fixed Deposit Mode</label>
                                <div class="col-md-4">
                                    <select name="fd_method" required="required" id="fd_method" class="form-control" tabindex="10">
                                        <option selected disabled value="">Select Deposit Mode</option>
                                        <option value="Cumulative">Cumulative</option>
                                        <option value="Non-Cumulative">Non-Cumulative</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4  no-border">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="1" tabindex="11" id="int_round_off" name="int_round_off">
                                        NBFC
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <!-- <div id="cumulative">
                                  <label class="col-sm-2 control-label">Compound Mode</label>
                                  <div class="col-md-4  no-border">
                                    <input type="hidden" id="interest_mode" name="interest_mode">
                                    <select name="int_mode" required="required" id="int_mode" class="form-control" tabindex="12">
                                        <option value="" disabled selected>Compound Mode</option>
                                        <option value="Annually">Annually</option>
                                        <option value="Half-yearly">Half-yearly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Monthly">Weekly</option>
                                        <option value="Monthly">Daily</option>
                                    </select>
                                  </div>
                                </div> -->
                                <div id="non_cumulative">
                                  <label class="col-sm-2 control-label" id="int_mode_lebel">Interest Mode</label>
                                  <div class="col-md-4  no-border">
                                    <input type="hidden" id="interest_mode" name="interest_mode">
                                    <select name="int_mode" required="required" id="int_mode" class="form-control" tabindex="12">
                                        <option value="" id="int_mode_value" disabled selected>Interest Mode</option>
                                        <option value="Annually">Annually</option>
                                        <option value="Half-yearly">Half-yearly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Monthly">Monthly</option>
                                    </select>
                                  </div>
                                </div>

                                  <label class="col-sm-2 control-label">Date of Issue</label>
                                  <div class="col-md-4">
                                    <input type="text" name="issued_date" required="required" tabindex="13" class="form-control date" id="issued_date" data-inputmask="'alias':'date'" value="<?php echo date('d/m/Y')?>" >
                                  </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount Invested</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="amount_invested" required="required" tabindex="14" class="form-control" id="amount_invested">
                                </div>
                                <label class="col-sm-2 control-label">Interest Rate</label>
                                <div class="col-md-4">
                                    <input type="text" name="interest_rate" required="required" tabindex="15" class="form-control" id="interest_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Maturity Date</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="maturity_date" required="required" tabindex="16" class="form-control date" id="maturity_date" data-inputmask="'alias':'date'" >
                                </div>
                                <label class="col-sm-2 control-label">Maturity Amount</label>
                                <div class="col-md-4">
                                    <input type="text" name="maturity_amount" required="required" tabindex="17" class="form-control" id="maturity_amount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nominee</label>
                                <div class="col-md-4  no-border">
                                    <select class="form-control" name="nominee" id="nominee" tabindex="18" required="required">
                                        <option disabled selected value='' selected>Select Nominee</option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Interest Amount</label>
                                <div class="col-md-4">
                                    <input type="text" name="intAmt" readonly class="form-control" tabindex="19" id="intAmt" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Advisor</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="adv_id" class="populate" required="required" id="adv_id" style="width: 80%" tabindex="20">
                                        <option disabled selected value="">Select Advisor</option>
                                        <?php foreach($adv as $row):?>
                                            <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="21" class="btn btn-xs btn-inverse-alt" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label"></label>
                                <button type="button" class="btn btn-green" tabindex="22"><i class="fa fa-gavel"></i> PRE-MATURE</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Adjustment</label>
                                <div class="col-sm-4">
                                    <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                        <span class="input-group-addon">
                                            <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="23">
                                        </span>
                                        <input type="hidden" value="0" name="adjustment_flag" id="adjustment_flag">
                                        <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="24" name="adjustment" readonly></textarea>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" value="" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="25" readonly class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Investment Bank Details</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Bank Name</label>
                                <div class="col-md-4  no-border add-new-btn">
                                    <select name="inv_bank_id" class="populate" required="required" id="inv_bank_id" style="width: 80%" tabindex="26">
                                        <option disabled selected value="">Select Bank</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->bank_id; ?>'><?php echo $row->bank_name; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="34" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4 add-new-btn">
                                    <select class="form-control" name="inv_account_number" id="inv_account_number" tabindex="27" style="width: 80%; float:left;">
                                        <option></option>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="28" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cheque Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="inv_cheque_number" class="form-control" tabindex="29" id="inv_cheque_number">
                                </div>
                                <label class="col-sm-2 control-label">Cheque Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="inv_cheque_date" class="form-control date" tabindex="30" id="inv_cheque_date" data-inputmask="'alias':'date'" value="<?php echo date('d/m/Y')?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="inv_amount" class="form-control" tabindex="31" id="inv_amount">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Maturity Bank Details</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Bank Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="maturity_bank_id" class="populate" required="required" id="maturity_bank_id" style="width: 80%" tabindex="32">
                                        <option disabled selected value="">Select Bank</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->bank_id; ?>'><?php echo $row->bank_name; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                <?php /*<a href="javascript:;" tabindex="34" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4 add-new-btn">
                                    <select class="form-control" name="maturity_account_number" id="maturity_account_number" tabindex="33" style="width: 80%; float:left;">
                                        <option></option>
                                    </select>
                                <?php /*<a href="javascript:;" tabindex="34" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Payout Option</label>
                                <div class="col-md-4  no-border">
                                    <select name="maturity_payout_id" class="populate" required="required" id="maturity_payout_id" style="width: 100%" tabindex="35">
                                        <option disabled selected value="">Select Payout</option>
                                        <?php foreach($payout as $row):?>
                                            <option value='<?php echo $row->payout_mode_id; ?>'><?php echo $row->payout_mode; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" id="add" tabindex="36" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" id="edit" tabindex="37" onclick="editForm('<?php echo base_url("broker/fixed_deposits/edit_form");?>', $('#transID').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" id="delete" tabindex="38" onclick="" data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" id="save" tabindex="39" onclick="fd_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" id="cancel" tabindex="40" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    $(function() {
        //on load disable controls
        disableBtn();


        //disable interest mode and nbfc on load
        // $('#int_mode').prepend('<option value="None">None</option>').attr('disabled',false);
        $('#interest_mode').val('None');
        $("#int_round_off").attr('disabled',true);
        $("#int_round_off").addClass('disabled');

        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $("#maturity_date").datepicker({format: 'dd/mm/yyyy', startDate:'<?php echo date('d/m/Y')?>'})
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
        $("#adj_flag").change(function(){
            if(this.checked)
            {
                $("#adjustment_flag").val("1");
                $("#adjustment").attr('readonly',false);
                $("#adjustment_ref_number").attr('readonly',false);
            }
            else
            {
                $("#adjustment_flag").val("0");
                $("#adjustment").attr('readonly',true);
                $("#adjustment_ref_number").attr('readonly',true);
            }
        });
        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', 'nominee', "", "");
        });

        //on client change get family
        $('#client_id').change(function()
        {
            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_family');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    if(data != 'fail') {
                        $("#family_id").select2("val",data['family_id']);
                        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
                        getClients(url, data['family_id'], '', 'nominee', "", "");
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
        });

        $('#fd_method').change(function(){
            if(this.value == 'Cumulative')
            {


              $('#int_mode').append('<option value="Weekly">Weekly</option>');
              $('#int_mode').append('<option value="Daily">Daily</option>');
              $('#int_mode_lebel').text('Compound Mode')
              $('#int_mode_value').text('Compound Mode')
              $('#int_mode').attr('disabled', false);
              $('#int_mode').val('');
              $('#interest_mode').val('None');
              $("#int_round_off").attr('disabled',true);
              $("#int_round_off").addClass('disabled');
            }
            else
            {
                $('#int_mode option[value="None"]').remove();
                $('#int_mode option[value="Weekly"]').remove();
                $('#int_mode option[value="Daily"]').remove();
                $("#select_id option[value='foo']").remove();
                $('#int_mode').attr('disabled', false);
                $('#int_mode').val('');
                $('#interest_mode').val('');
                $('#int_mode_lebel').text('Interest Mode')
                $('#int_mode_value').text('Interest Mode')
                $("#int_round_off").attr('disabled',false);
                $("#int_round_off").removeClass('disabled');


            }
        });

        $('#int_mode').change(function(){
            $('#interest_mode').val(this.value);
        });

        $('#fd_inv_id').change(function(){
            $('#fd_inv_name').val($('#fd_inv_id option:selected').text());
        });

        $('#fd_comp_id').change(function(){
            $('#fd_comp_name').val($('#fd_comp_id option:selected').text());
        });

        $('#fd_method, #amount_invested, #issued_date, #interest_rate, #maturity_date').change(function(){
            var fd_method = $('#fd_method').val();
            var amount_invested = $('#amount_invested').val();
            var issued_date = $('#issued_date').val();
            var interest_rate = $('#interest_rate').val();
            var maturity_date = $('#maturity_date').val();
            var int_mode = $('#interest_mode').val();
            if((fd_method != null || fd_method != "") && amount_invested != '' && issued_date != '' && interest_rate != '' && maturity_date != '')
            {
                $.ajax({
                    url: '<?php echo site_url("broker/Fixed_deposits/calculate_interest")?>',
                    type: 'post',
                    data: {fd_method: fd_method, amt_inv: amount_invested, issue_date: issued_date, int_rate: interest_rate, mat_date: maturity_date,int_mode:int_mode},
                    dataType: 'json',
                    success: function(data){
                        //$('#maturity_amount').val(data['mat_amt']);
                        $('#maturity_amount').val($('#amount_invested').val());
                        $('#intAmt').val(data['int_amt']);
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            }
        });

        //auto value of cheque date and issue date on Transaction Date
        $("#transaction_date").change(function() {
            //$("#issued_date").val(this.value);
            $('#issued_date').datepicker('setDate', this.value)
            //$("#inv_cheque_date").val(this.value);
            $("#inv_cheque_date").datepicker('setDate', this.value)
        });

        $("#issued_date").change(function() {
            $("#maturity_date").datepicker("remove");
            $("#maturity_date").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
        });

        //auto value of amount paid - near bank details
        $("#amount_invested").change(function() {
            $('#inv_amount').val($('#amount_invested').val());
        });

        $("#client_id").change(function() {
            get_client_banks(this.value);
        });

        $("#inv_bank_id").change(function(){
            get_account_number('investment', this.value);
        });
        $("#maturity_bank_id").change(function(){
            get_account_number('maturity', this.value);
        });

        //auto select maturity account number
        $("#inv_account_number").change(function() {
            $("#maturity_account_number").val(this.value);
        });

    });

    function get_client_banks(clientID) {
        $.ajax({
            url: "<?php echo site_url('broker/Banks/get_client_banks'); ?>",
            type: 'post',
            data: {clientID: clientID},
            dataType: 'json',
            success: function(data)
            {
                var option = '<option selected disabled>Select Bank</option>';
                $.each(data['banks'], function(i, item){
                    option = option + "<option value = " + item.bank_id + ">" + item.bank_name + "</option>";
                });
                $('#inv_bank_id').html(option);
                $('#maturity_bank_id').html(option);

            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    function get_account_number(type, bank_id)
    {
        $.ajax({
            url: "<?php echo site_url('broker/Banks/get_account_num_by_bank'); ?>",
            type: 'post',
            data: {bankID: bank_id, clientID: $('#client_id').val()},
            dataType: 'json',
            success: function(data)
            {
                var option = '<option selected disabled>Select Account Number</option>';
                $.each(data['acc_num'], function(i, item){
                    option = option + "<option value = " + item.account_number + ">" + item.account_number + "</option>";
                });
                if(type == 'investment') {
                    $('#inv_account_number').html(option);
                    $('#maturity_account_number').html(option);
                    //auto select bank
                    $('#maturity_bank_id').select2("val",bank_id);
                } else {
                    $('#maturity_account_number').html(option);
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    //insert fixed deposits details in database
    function fd_submit()
    {
        var valid = true, dateValid = true;
        var transDate = process($('#transaction_date').val());
        var matDate = process($('#maturity_date').val());
        var issueDate = process($('#issued_date').val());
        var chqDate = process($('#inv_cheque_date').val());
        if(transDate > matDate || chqDate > issueDate)
        {
            $.pnotify({
                title: 'Error!',
                text: "Please check the Date of Transaction and Cheque Date. It can't be greater than Date Of Issue",
                type: 'error',
                hide: true
            });
            dateValid = false;
        }
        valid = $('#fd_form').parsley('validate');
        if(valid && dateValid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/Fixed_deposits/add_fd');?>',
                type: 'post',
                data: $('#fd_form').serialize(),
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
                        if(data['newMatAmt'] != 0) {
                            $('#maturity_amount').val(data['newMatAmt']);
                        }
                        enableBtn();
                        $('#transID').val(data['transID']);
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
                error:function(data)
                {
                    console.log(data);
                }
            });
        }
    }
</script>
