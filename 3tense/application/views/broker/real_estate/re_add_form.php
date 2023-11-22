<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Real_estate');?>">Real Estate</a></li>
                <li class="active">Add Real Estate</li>
            </ol>
            <h1>Add Real Estate</h1>
        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="#" id="client_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" value="Purchase" id="transaction_type" name="transaction_type">
                                <input type="hidden" value="" id="pro_transaction_id" name="pro_transaction_id">
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
                                    <input type="text" name="transaction_date" required="required" tabindex="4" value="<?php echo date('d/m/Y')?>" class="form-control date" id="transaction_date" data-inputmask="'alias':'date'" disabled>
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
                </div>
            </form>
            <div class="panel panel-primary control-form">
                <div class="panel-heading" style="height: auto">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#purchase" tabindex="5" data-toggle="tab">Purchase</a>
                        </li>
                        <li>
                            <a href="#sell" tabindex="6" data-toggle="tab">Sell</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel-body control-form">
                <div class="tab-content">
                    <div class="tab-pane active no-border" id="purchase">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="re_purchase_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Real Estate Purchase Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_name" required="required" class="form-control" id="property_name" tabindex="7" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Type</label>
                                            <div class="col-sm-8">
                                                <select name="property_type_id" class="populate" required="required" id="property_type_id" tabindex="8" style="width: 100%">
                                                    <option disabled selected>Select Property Type</option>
                                                    <?php foreach($prop_types as $row):?>
                                                        <option value='<?php echo $row->property_type_id; ?>'><?php echo $row->property_type_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Location</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_location" required="required" class="form-control" id="property_location" tabindex="9">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Area</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_area" class="form-control" id="property_area" required="required" tabindex="10" style="width: 40%; display: inline-block">
                                                <select name="property_unit_id" class="populate" required="required" id="property_unit_id" tabindex="11" style="width: 50%; vertical-align: bottom">
                                                    <option disabled selected>Select Unit</option>
                                                    <?php foreach($units as $row):?>
                                                        <option value='<?php echo $row->unit_id; ?>'><?php echo $row->unit_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="transaction_rate" required="required" class="form-control" id="transaction_rate" tabindex="12" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="amount" required="required" class="form-control" id="amount" tabindex="13" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="current_rate" required="required" class="form-control" id="current_rate" tabindex="14" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Updated On</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_updated_on" required="required" class="form-control date" data-inputmask="'alias':'date'" id="property_updated_on" tabindex="15" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="remarks" class="form-control" id="remarks" tabindex="16" style="height: 109px"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Advisor</label>
                                            <div class="col-sm-8">
                                                <select name="adviser_id" class="populate" required="required" id="adviser_id" tabindex="17" style="width: 100%">
                                                    <option disabled selected>Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="18" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Deposit</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="deposit_amount" class="form-control" id="deposit_amount" tabindex="19" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="panel-heading">
                                    <h4>Rent Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div>
                                                <a class="btn btn-success" tabindex="20" href="javascript:void(0)" onclick="open_rent_modal()"><i class="fa fa-plus"></i> Add Rent</a>
                                                <br /><br />
                                                <table border="0" class="table table-striped table-bordered" id="rentTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No.</th>
                                                            <th>From Date</th>
                                                            <th>To Date</th>
                                                            <th>Amount</th>
                                                            <th style="width:125px;">Action</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane no-border" id="sell">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="re_sell_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Real Estate Sell Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Name</label>
                                            <div class="col-sm-8">
                                                <input type="hidden" value="" name="h_property_name" id="h_property_name">
                                                <select name="sell_property_name" class="populate" required="required" id="sell_property_name" tabindex="7" style="width: 100%">
                                                    <option disabled selected>Select Property</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Type</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_type" class="form-control" id="sell_pro_type" readonly>
                                                <input type="hidden" name="h_pro_type" id="h_pro_type">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Location</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_location" class="form-control" id="sell_pro_location" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Property Area</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_area" class="form-control" id="sell_pro_area" style="width: 40%; display: inline-block" readonly>
                                                <input type="text" name="sell_unit_id" class="form-control" id="sell_unit_id" style="width: 40%; display: inline-block" readonly>
                                                <input type="hidden" name="h_unit_id" id="h_unit_id">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_trans_rate" required="required" class="form-control" id="sell_trans_rate" tabindex="8">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_amount" required="required" class="form-control" id="sell_amount" tabindex="9">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_curr_rate" class="form-control" id="sell_curr_rate" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Updated On</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_upd_on" class="form-control" id="sell_pro_upd_on" readonly value="<?php echo date('d/m/Y')?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="sell_remarks" class="form-control" id="sell_remarks" tabindex="10" style="height: 109px"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Advisor</label>
                                            <div class="col-sm-8">
                                                <select name="sell_adviser_id" class="populate" required="required" id="sell_adviser_id" tabindex="11" style="width: 80%">
                                                    <option disabled selected>Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="18" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="panel panel-midnightblue no-border">
                        <form action="#" id="re_gain_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                            <div class="panel-heading">
                                <h4>Realised or Unrealised Gain</h4>
                                <div class="options">
                                    <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                </div>
                            </div>
                            <div class="panel-body collapse in">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Gain</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="gain" class="form-control" id="gain" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Gain</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="total_gain" class="form-control" id="total_gain" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">ABS %</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="abs" class="form-control" id="abs" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">CAGR %</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="cagr" class="form-control" id="cagr" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" tabindex="21" id="add" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" tabindex="22" id="edit" onclick="editForm('<?php echo base_url("broker/real_estate/edit_form");?>', $('#pro_transaction_id').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" tabindex="23" id="delete" onclick="del_real_estate()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" tabindex="24" id="save" onclick="real_estate_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" tabindex="25" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap modal -->
<div class="modal fade" id="rent_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="rent_modal" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <input name="srNoEdit" id="srNoEdit" type="hidden">
                            <label class="control-label col-md-3">From Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="fromDate" data-inputmask="'alias':'date'" required="required" id="fromDate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">To Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="toDate" data-inputmask="'alias':'date'" required="required" id="toDate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Amount</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="rentAmt" id="rentAmt" required="required">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_rent()" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">

    $(document).ready(function() {
        $("#transaction_date").bind("change",restrictDate);
        $('#property_updated_on').datepicker('remove');
        $('#property_updated_on').datepicker({format: 'dd/mm/yyyy', startDate:$("#transaction_date").val()}).inputmask();
    });

    function restrictDate() {
        $('#property_updated_on').datepicker('remove');
        $('#property_updated_on').datepicker({format: 'dd/mm/yyyy', startDate:$("#transaction_date").val()}).inputmask();

    }



    var oTable;
    var save_method;
    $(function() {
        disableBtn();
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();

        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', '', "", "");
        });

        //on client change get policy details and remove previous details
        $('#client_id').change(function(){
            get_properties(this.value);

            //on client change get family
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
        });
        rent_list();

        //end date of rent should not be less than start date
        $("#fromDate").change(function() {
            $("#toDate").datepicker("remove");
            $("#toDate").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
        });

        $('#transaction_rate, #property_area').change(function(){
            var trans_rate = $('[name="transaction_rate"]').val();
            var property_area = $('[name="property_area"]').val();
            if(trans_rate != "" && property_area != "")
            {
                $("#amount").val(property_area * trans_rate);
                $("#current_rate").val(trans_rate);
            }
        });

        $('#sell_trans_rate').change(function(){
            var trans_rate = $('[name="sell_trans_rate"]').val();
            var property_area = $('[name="sell_pro_area"]').val();
            if(trans_rate != "" && property_area != "")
            {
                $("#sell_amount").val(property_area * trans_rate);
                $("#sell_curr_rate").val(trans_rate);
            }
        });

        //on rate change calculate and set gain, abs, cagr amount
        /*$('#current_rate, #transaction_date, #property_updated_on, #amount').change(function()
        {
            var current_rate = $('[name="current_rate"]').val();
            var transaction_rate = $('[name="transaction_rate"]').val();
            var property_area = $('[name="property_area"]').val();
            var transaction_date = $('[name="transaction_date"]').val();
            var property_updated_on = $('[name="property_updated_on"]').val();
            var amount = $('[name="amount"]').val();

            if(current_rate != "" && transaction_rate != "" && property_area != "" && transaction_date != "" && property_updated_on != "" && amount != "")
            {
                $.ajax({
                    url:'<?php echo site_url("broker/real_estate/realised_calculation"); ?>',
                    type: 'post',
                    dataType: 'json',
                    data:{trans_id: "", currRate: current_rate, transRate: transaction_rate, propArea: property_area, transDate: transaction_date,
                        updDate: property_updated_on, amount: amount},
                    success: function(data)
                    {
                        $("#gain, #sell_gain").val(data['gain']);
                        $("#total_gain, #sell_tot_gain").val(data['total_gain']);
                        $("#abs, #sell_abs").val(data['abs']);
                        $("#cagr, #sell_cagr").val(data['cagr']);
                    },
                    error: function(data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        });*/

        $("#sell_property_name").change(function(){
            $('#h_property_name').val($("#sell_property_name option:selected").text());
            $.ajax({
                url: '<?php echo site_url('broker/real_estate/get_real_estate_details'); ?>',
                type: 'post',
                dataType: 'json',
                data: {proTransID: this.value},
                success: function(data){
                    $("#sell_pro_type").val(data['property_type_name']);
                    $("#h_pro_type").val(data['property_type_id']);
                    $("#sell_pro_location").val(data['property_location']);
                    $("#sell_pro_area").val(data['property_area']);
                    $("#sell_unit_id").val(data['unit_name']);
                    $("#h_unit_id").val(data['property_unit_id']);
                    $("#gain").val(data['gain']);
                    $("#total_gain").val(data['total_gain']);
                    $("#abs").val(data['abs']);
                    $("#cagr").val(data['cagr']);
                },
                error: function(data){
                    console.log(data);
                    bootbox.alert("Something went wrong");
                }
            });
        });
    });

    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if(target == '#purchase')
        {
            $("#transaction_type").val('Purchase');
        }
        else if(target == '#sell')
        {
            $("#transaction_type").val('Sale');
        }
    });

    //insert real estate in database
    function real_estate_submit()
    {
        var trans_type = $("#transaction_type").val();
        var valid = $('#client_form').parsley('validate');
        if(trans_type == 'Purchase')
        {
            valid = $('#re_purchase_form').parsley('validate');
            if(valid)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/real_estate/add_real_estate');?>',
                    type: 'post',
                    data: $('#client_form, #re_purchase_form, #re_gain_form').serialize(),
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            $('#pro_transaction_id').val(data['transaction_id']);
                              calculate_cagr_abs();
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
                    error:function(data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        }
        else if(trans_type == 'Sale')
        {
            valid = $('#re_sell_form').parsley('validate');
            if(valid)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/real_estate/sell_real_estate');?>',
                    type: 'post',
                    data: $('#client_form, #re_sell_form').serialize(),
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
                
                            $('#pro_transaction_id').val(data['transaction_id']);
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
                    error:function(data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        }
    }

    function calculate_cagr_abs()
    {
         //calculate cagr and abs
             //alert( $('#pro_transaction_id').val());
             //console.log($('#pro_transaction_id').val());
             //console.log("gone inside");
             var propTranId=$('[name="pro_transaction_id"]').val();
              var current_rate = $('[name="current_rate"]').val();
                            var transaction_rate = $('[name="transaction_rate"]').val();
                            var property_area = $('[name="property_area"]').val();
                            var transaction_date = $('[name="transaction_date"]').val();
                             var property_updated_on = $('[name="property_updated_on"]').val();
                            var amount = $('[name="amount"]').val();

            if(current_rate != "" && transaction_rate != "" && property_area != "" && transaction_date != "" && property_updated_on != "" && amount != "")
            {
                //alert("checking abs");
                $.ajax({
                    url:'<?php echo site_url("broker/real_estate/realised_calculation"); ?>',
                    type: 'post',
                    dataType: 'json',
                    data:{pro_trans_id: propTranId, currRate: current_rate, transRate: transaction_rate, propArea: property_area, transDate: transaction_date,
                        updDate: property_updated_on, amount: amount},
                    success: function(data)
                    {
                        $("#gain, #sell_gain").val(data['gain']);
                        $("#total_gain, #sell_tot_gain").val(data['total_gain']);
                        $("#abs, #sell_abs").val(data['abs']);
                        $("#cagr, #sell_cagr").val(data['cagr']);
                    },
                    error: function(data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
    }

    function get_properties(client_id)
    {
        $.ajax({
            url: "<?php echo site_url('broker/Real_estate/get_properties');?>",
            type:'post',
            data: {client_id: client_id},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Property</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].pro_transaction_id+">"+data[i].property_name+"</option>";
                });
                $("#sell_property_name").html(option);
                $("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting property details from client',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

    //get rent details in datatable
    function rent_list() {
        oTable = $("#rentTable").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "scrollY": "200px",
            "scrollCollapse": true,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, -1 ] },
                { "bSearchable": false, "aTargets": [ 0, -1 ] },
                { "bVisible": false, "aTargets": [ 0 ] }],
            "bPaginate": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/real_estate/rent_list');?>',
                "type": 'post',
                "data": {isEdit: false},
                "dataSrc": function (json) {
                    if(!json.data){
                        $('#rentTable').html('No Records Available');
                        json.data = [];
                    }
                    return json.data;
                }
            },
            "columns": [
                { "data": "srNo" },
                { "data": "fromDate", "type": "date-uk" },
                { "data": "toDate", "type": "date-uk" },
                { "data": "amount" },
                { "data": "action" }
            ]
        });
        return oTable;
    }

    //popup to add rent details
    function open_rent_modal()
    {
        //reset form on modals
        $("#rent_modal")[0].reset();
        //show bootstrap modal
        $("#rent_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Rent');
        save_method = 'add';

        $("#fromDate").datepicker("remove");
        $("#fromDate").datepicker({format: 'dd/mm/yyyy', startDate: $("#transaction_date").val()});
    }

    function edit_rent(id)
    {
        //debugger;
        save_method = 'update';
        $('#rent_modal')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Real_estate/edit_rent');?>",
            type: "POST",
            data:{id: id, isEdit: false},
            dataType: "JSON",
            success: function(data)
            {
                //debugger;
                $('[name="srNoEdit"]').val(id);
                $('[name="fromDate"]').val(data.fromDate);
                $('[name="toDate"]').val(data.toDate);
                $('[name="rentAmt"]').val(data.amount);
                $('#rent_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Rent'); // Set title to Bootstrap modal title

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    //add rent details in datatable
    function save_rent()
    {
        var rent_form = $('#rent_modal');
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/real_estate/add_rent');?>";
        else
            url = "<?php echo site_url('broker/real_estate/update_rent');?>";
        if(rent_form.parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: rent_form.serialize(),
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
                        if(oTable != null)
                        {
                            $('#rentTable').empty();
                            oTable.destroy();
                        }
                        oTable = rent_list();
                        //hide bootstrap modal
                        $("#rent_modal_form").modal('hide');
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
                error: function (data)
                {
                    console.log(data);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
    }
    //delete rent details in datatable
    function delete_rent(id)
    {
        bootbox.confirm('Are you sure you want to delete this rent details?', function(result){
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/real_estate/delete_rent');?>",
                    type: "POST",
                    data:{id: id, isEdit: false},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            oTable.destroy();
                            oTable = rent_list();
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
                    error: function (data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        });
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function ( a ) {
            var ukDatea = a.split('/');
            return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        },

        "date-uk-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "date-uk-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
</script>