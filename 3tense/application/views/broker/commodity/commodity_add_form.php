<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Commodities');?>">Commodities</a></li>
                <li class="active">Add Commodity</li>
            </ol>
            <h1>Add Commodity</h1>
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
                                <input type="hidden" value="" id="commodity_trans_id" name="commodity_trans_id">
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
                                    <input type="text" name="transaction_date" required="required" style="width: 80%;" tabindex="5" value="<?php echo date('d/m/Y')?>" class="form-control date" id="transaction_date" data-inputmask="'alias':'date'" >
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
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="4" class="btn btn-xs btn-inverse-alt" onclick="javascript:location.href='<?php echo base_url('broker/Clients/add'); ?>';"><i class="fa fa-plus"></i></a>
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
                            <a href="#purchase" tabindex="6" data-toggle="tab">Purchase</a>
                        </li>
                        <li>
                            <a href="#sell" tabindex="7" data-toggle="tab">Sell</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel-body control-form">
                <div class="tab-content">
                    <div class="tab-pane active no-border" id="purchase">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="comm_purchase_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Commodity Purchase Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Item</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <select name="commodity_item_id" class="populate" required="required" id="commodity_item_id" tabindex="8" style="width: 80%">
                                                    <option disabled selected value="">Select Commodity Item</option>
                                                    <?php foreach($items as $row):?>
                                                        <option value='<?php echo $row->item_id; ?>'><?php echo $row->item_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" title="Add Commodity Item" tabindex="9" onclick="add_commodity_item(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Quantity</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <input type="number" step="any" min="0.00001" name="quantity" class="form-control" id="quantity" required="required" tabindex="10" value="0.00001" style="width: 30%; display: inline-block">
                                                <select name="commodity_unit_id" class="populate" required="required" id="commodity_unit_id" tabindex="11" style="width: 40%; vertical-align: bottom">
                                                    <option disabled selected value="">Select Unit</option>
                                                    <?php /*foreach($units as $row):?>
                                                        <option value='<?php echo $row->unit_id; ?>'><?php echo $row->unit_name; ?></option>
                                                    <?php endforeach;*/ ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" title="Add Commodity Unit" tabindex="12" onclick="add_commodity_unit(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particulars/Quality</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="quality" class="form-control" id="quality" tabindex="13" data-maxlength="50">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="transaction_rate" required="required" class="form-control" id="transaction_rate" tabindex="14" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <input type="text" readonly name="current_rate" required="required" class="form-control" id="current_rate" value="" style="width: 80%; float: left;" />
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" title="Add Rate" tabindex="15" onclick="add_commodity_rate(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" readonly name="total_amount" required="required" class="form-control" id="total_amount" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Advisor</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <select name="adviser_id" class="populate" required="required" id="adviser_id" tabindex="17" style="width: 80%">
                                                    <option disabled selected value="">Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="18" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"></label>
                                            <div class="col-sm-8 no-border">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="1" tabindex="19" id="initial_investment" name="initial_investment">
                                                    Initial Investment
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane no-border" id="sell">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="comm_sell_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Commodity Sell Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Transaction</label>
                                            <div class="col-sm-8">
                                                <select name="sell_commodity_trans_id" class="populate" required="required" id="sell_commodity_trans_id" tabindex="8" style="width: 100%">
                                                    <option disabled selected value="">Select Commodity Transaction</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Item</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_item_name" class="form-control" id="sell_item_name" readonly>
                                                <input type="hidden" name="sell_commodity_item_id" id="sell_commodity_item_id">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Quantity</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_quantity" class="form-control" id="sell_quantity" style="width: 40%; display: inline-block" readonly>
                                                <input type="text" name="sell_unit_name" class="form-control" id="sell_unit_name" style="width: 40%; display: inline-block" readonly>
                                                <input type="hidden" name="sell_commodity_unit_id" id="sell_commodity_unit_id">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particulars/Remarks</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_quality" class="form-control" id="sell_quality" tabindex="9" data-maxlength="50" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_transaction_rate" required="required" class="form-control" id="sell_transaction_rate" tabindex="10">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_current_rate" class="form-control" id="sell_current_rate" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_total_amount" required="required" class="form-control" id="sell_total_amount" tabindex="11">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Advisor</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <select name="sell_adviser_id" class="populate" required="required" id="sell_adviser_id" tabindex="12" style="width: 80%">
                                                    <option disabled selected>Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="13" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <input type="hidden" value="" id="purDateSell"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php /* Uncomment the below part if required in future
                    <!--<div class="panel panel-midnightblue no-border">
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
                    </div>-->
                    */ ?>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" tabindex="21" id="add" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" tabindex="22" id="edit" onclick="editForm('<?php echo base_url("broker/commodities/edit_form");?>', $('#commodity_trans_id').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" tabindex="23" id="delete" onclick="delete_commodity()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" tabindex="24" id="save" onclick="commodity_form_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" tabindex="25" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>

<script type="text/javascript">
    var oTable;
    var save_method;
    $(function() {
        disableBtn();
        //$('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();

        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', '', "", "");
        });

        //on client change get commodity details and remove previous details
        $('#client_id').change(function(){
            get_commodities(this.value);

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

        //on commodity item change get units
        $('#commodity_item_id').change(function(){
            get_item_units(this.value);
        });
        //on commodity unit change get current_rate
        $('#commodity_unit_id').change(function(){
            var item_id = $("#commodity_item_id").val();
            if(item_id && this.value) {
                get_current_rate(item_id,this.value);
            } else {
                console.log("could not get current rate");
            }
        });

        //calculate Purchase total amount based on rate and quantity
        $('#current_rate, #transaction_rate, #quantity').change(function(){
            var trans_rate = $('[name="transaction_rate"]').val();
            if(trans_rate == "")
                var trans_rate = $('[name="current_rate"]').val();
            var qty = $('[name="quantity"]').val();
            if(trans_rate!= "" && qty != "")
            {
                $("#total_amount").val(qty * trans_rate);
                //$("#current_rate").val(trans_rate);
            }
        });

        //calculate Selling total amount based on rate and quantity
        $('#sell_current_rate, #sell_transaction_rate, #sell_quantity').change(function(){
            var trans_rate = $('[name="sell_transaction_rate"]').val();
            if(trans_rate == "")
                var trans_rate = $('[name="sell_current_rate"]').val();
            var qty = $('[name="sell_quantity"]').val();
            if(trans_rate!= "" && qty != "")
            {
                $("#sell_total_amount").val(qty * trans_rate);
                //$("#current_rate").val(trans_rate);
            }
        });

        <?php /* uncomment for ABS, CAGR unrealised gain
        //on rate change calculate and set gain, abs, cagr amount
        $('#current_rate, #transaction_date, #property_updated_on, #amount').change(function()
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
        });
        */ ?>

        $("#sell_commodity_trans_id").change(function(){
            if(this.value != "") {
                $.ajax({
                    url: '<?php echo site_url('broker/Commodities/get_commodities'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {commTransID: this.value},
                    success: function(data){
                        data = data[0];
                        $("#sell_item_name").val(data['item_name']);
                        $("#sell_commodity_item_id").val(data['commodity_item_id']);
                        $("#sell_quantity").val(data['quantity']).change();
                        $("#sell_current_rate").val(data['current_rate']).change();
                        $("#sell_total_amount").val(data['total_amount']);
                        $("#sell_unit_name").val(data['unit_name']);
                        $("#sell_commodity_unit_id").val(data['commodity_unit_id']);
                        $("#sell_quality").val(data['quality']);
                        $("#purDateSell").val(data['transaction_date']);
                        var purDate0 = process(data['transaction_date']);
                        var sellDate0 = process($("#transaction_date").val());

                        //first remove datepicker from transaction date
                        ($("#transaction_date")).datepicker("remove");
                        $("#transaction_date").datepicker({format:'dd/mm/yyyy', startDate:data['transaction_date']});
                        if(purDate0 > sellDate0) {
                            $.pnotify({
                                title: 'Incorrect Date selected!',
                                text: 'Date of Transaction you entered was less than the Purchase date of the item.',
                                type: 'error',
                                hide: true
                            });
                            $("#transaction_date").val(data['transaction_date']);
                        }
                    },
                    error: function(data){
                        console.log(data);
                        bootbox.alert("Something went wrong");
                    }
                });
            } else {
                $("#sell_item_name").val("");
                $("#sell_commodity_item_id").val("");
                $("#sell_quantity").val("").change();
                $("#sell_current_rate").val("").change();
                $("#sell_total_amount").val("");
                $("#sell_unit_name").val("");
                $("#sell_commodity_unit_id").val("");
                $("#purDateSell").val("");
            }
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
    function commodity_form_submit()
    {
        var valid = true;
        var trans_type = $("#transaction_type").val();
        valid = $('#client_form').parsley('validate');
        if(trans_type == 'Purchase')
        {
            valid = $('#comm_purchase_form').parsley('validate');
            if(valid)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/commodities/add_commodity');?>',
                    type: 'post',
                    data: $('#client_form, #comm_purchase_form').serialize(),
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
                            $("#commodity_trans_id").val(data['commodity_trans_id']);
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
            valid = $('#comm_sell_form').parsley('validate');
            if(valid)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/commodities/sell_commodity');?>',
                    type: 'post',
                    data: $('#client_form, #comm_sell_form').serialize(),
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
                            $("#commodity_trans_id").val(data['commodity_trans_id']);
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

    function get_commodities(client_id)
    {
        $.ajax({
            url: "<?php echo site_url('broker/Commodities/get_commodities');?>",
            type:'post',
            data: {client_id: client_id},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Commodity Transaction</option>';
                $.each(data, function(i, item){
                    option = option + "<option value='"+data[i].commodity_trans_id+"'>"+data[i].item_name+" - "+data[i].quantity+" "+data[i].unit_name+" @ "+data[i].total_amount+"</option>";
                });
                $("#sell_commodity_trans_id").html(option).change();
                //$("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting commodity details from client',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

    //get units of selected item
    function get_item_units(item_id)
    {
        $.ajax({
            url: "<?php echo site_url('broker/Commodities/get_commodity_item_units');?>",
            type:'post',
            data: {item_id: item_id},
            dataType: 'json',
            success:function(data)
            {
                $("#commodity_unit_id").select2('val','');
                var option = '<option disabled selected value="">Select Unit</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].unit_id+">"+data[i].unit_name+"</option>";
                });
                $("#commodity_unit_id").html(option);
                $("#current_rate").val('').change();
                $("#transaction_rate").val('').change();
                $("#total_amount").val('').change();
                //$("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                var option = '<option disabled selected value="">Select Unit</option>';
                $("#commodity_unit_id").html(option);
                $("#commodity_unit_id").select2('val','').change();
                $.pnotify({
                    title: 'No units for selected item!',
                    text: 'Error getting commodity units/rate from commodity item. Check whether Commodity Rate is defined for the selected Item',
                    type: 'info',
                    hide: true
                });
            }
        });
    }

    function get_current_rate(item_id, unit_id)
    {
        $.ajax({
            url: "<?php echo site_url('broker/Commodities/get_commodity_rate');?>",
            type:'post',
            data: {item_id: item_id, unit_id: unit_id},
            dataType: 'json',
            success:function(data)
            {
                $("#current_rate").val(data.current_rate).change();
                $("#transaction_rate").val(data.current_rate).change();
                //$("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting commodity rates from commodity item.',
                    type: 'error',
                    hide: true
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
