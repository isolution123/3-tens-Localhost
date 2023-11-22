<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Commodities');?>">Commodities</a></li>
                <li class="active">Edit Commodity</li>
            </ol>
            <h1>Edit Commodity</h1>
        </div>
        <div class="container">
            <input type="hidden" id="transID" value="">
            <form action="#" id="client_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" name="hFamilyID" id="hFamilyID" value="<?= $commodity->family_id; ?>" />
                                <input type="hidden" name="hClientID" id="hClientID" value="<?= $commodity->client_id; ?>" />
                                <input type="hidden" name="hItemID" id="hItemID" value="<?= $commodity->commodity_item_id; ?>" />
                                <input type="hidden" name="hUnitID" id="hUnitID" value="<?= $commodity->commodity_unit_id; ?>" />
                                <input type="hidden" name="hAdviserID" id="hAdviserID" value="<?= $commodity->adviser_id; ?>" />
                                <input type="hidden" id="transaction_type" value="<?= $commodity->transaction_type; ?>">
                                <input type="hidden" id="commodity_trans_id" name="commodity_trans_id" value="<?= $commodity->commodity_trans_id; ?>">
                                <input type="hidden" id="isSold" value="<?= $isSold; ?>">
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
                                <div class="col-md-4 no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="5" style="width: 80%;"
                                           value="<?php echo isset($commodity->transaction_date)?$commodity->transaction_date:'';?>"
                                           class="form-control date" readonly="readonly" id="transaction_date" data-inputmask="'alias':'date'" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 80%" tabindex="3">
                                        <option disabled selected value="">Select Family</option>
                                    </select>
                                    <a href="javascript:;" tabindex="4" class="btn btn-xs btn-inverse-alt" onclick="javascript:location.href='<?php echo base_url('broker/Clients/add'); ?>';"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-primary">
                <div class="panel-heading" style="height: auto">
                    <ul class="nav nav-tabs">
                        <?php if($commodity->transaction_type == 'Sale'):?>
                        <li>
                            <a href="javascript:void(0)" tabindex="6">Purchase</a>
                        </li>
                        <li class="active">
                            <a href="#sell" tabindex="7" data-toggle="tab">Sell</a>
                        </li>
                        <?php else: ?>
                        <li class="active">
                            <a href="#purchase" tabindex="6" data-toggle="tab">Purchase</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" tabindex="7">Sell</a>
                        </li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <?php if($commodity->transaction_type == 'Purchase'):?>
                    <div class="tab-pane active no-border control-form" id="purchase">
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

                                                  <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="13" onclick="add_commodity_item(true)"><i class="fa fa-plus"></i></a>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Quantity</label>
                                            <div class="col-sm-8">
                                                <input type="number" step="any" min="0.00001" name="quantity" class="form-control" id="quantity" required="required"
                                                       tabindex="10" style="width: 40%; display: inline-block"
                                                       value="<?php echo isset($commodity->quantity)?$commodity->quantity:0.00001;?>">
                                                <select name="commodity_unit_id" class="populate" required="required" id="commodity_unit_id" tabindex="11" style="width: 50%; vertical-align: bottom">
                                                    <option disabled selected value="">Select Unit</option>
                                                    <?php /*foreach($units as $row):?>
                                                        <option value='<?php echo $row->unit_id; ?>'><?php echo $row->unit_name; ?></option>
                                                    <?php endforeach;*/ ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="13" onclick="add_commodity_unit(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particulars/Quality</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="quality" class="form-control" id="quality" tabindex="13"
                                                   data-maxlength="50" value="<?php echo isset($commodity->quality)?$commodity->quality:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="transaction_rate" required="required" class="form-control"
                                                   id="transaction_rate" tabindex="14"
                                                   value="<?php echo isset($commodity->transaction_rate)?$commodity->transaction_rate:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" readonly name="current_rate" required="required" class="form-control"
                                                   id="current_rate" value="<?php echo isset($commodity->current_rate)?$commodity->current_rate:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" readonly name="total_amount" required="required" class="form-control"
                                                   id="total_amount" tabindex="15"
                                                   value="<?php echo isset($commodity->total_amount)?$commodity->total_amount:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Advisor</label>
                                            <div class="col-sm-8 add-new-btn">
                                                <select name="adviser_id" class="populate" required="required" id="adviser_id" tabindex="16" style="width: 80%">
                                                    <option disabled selected value="">Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="17" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"></label>
                                            <div class="col-md-4 no-border">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="1" tabindex="18" id="initial_investment"
                                                       name="initial_investment" <?php echo isset($commodity->initial_investment) && ($commodity->initial_investment == '1')?'checked="checked"':'';?>">
                                                    Initial Investment
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php else:?>
                    <div class="tab-pane no-border active" id="sell">
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
                                            <label class="col-sm-4 control-label">Commodity Item</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_item_name" class="form-control" id="sell_item_name" readonly
                                                    value="<?php echo isset($commodity->item_name)?$commodity->item_name:''; ?>">
                                                <input type="hidden" name="sell_commodity_item_id" id="sell_commodity_item_id"
                                                       value="<?php echo isset($commodity->commodity_item_id)?$commodity->commodity_item_id:''; ?>">
                                                <input type="hidden" name="sell_commodity_trans_id" id="sell_commodity_trans_id"
                                                       value="<?php echo isset($commodity->sale_ref)?$commodity->sale_ref:''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Commodity Quantity</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_quantity" class="form-control" id="sell_quantity" style="width: 40%; display: inline-block" readonly
                                                       value="<?php echo isset($commodity->quantity)?$commodity->quantity:''; ?>">
                                                <input type="text" name="sell_unit_name" class="form-control" id="sell_unit_name" style="width: 40%; display: inline-block" readonly
                                                       value="<?php echo isset($commodity->unit_name)?$commodity->unit_name:''; ?>">
                                                <input type="hidden" name="sell_commodity_unit_id" id="sell_commodity_unit_id"
                                                       value="<?php echo isset($commodity->commodity_unit_id)?$commodity->commodity_unit_id:''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particulars/Remarks</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_quality" class="form-control" id="sell_quality" tabindex="9" data-maxlength="50"
                                                       readonly value="<?php echo isset($commodity->quality)?$commodity->quality:''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_transaction_rate" required="required" class="form-control" id="sell_transaction_rate" tabindex="10"
                                                       value="<?php echo isset($commodity->transaction_rate)?$commodity->transaction_rate:''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_current_rate" class="form-control" id="sell_current_rate" readonly
                                                       value="<?php echo isset($commodity->current_rate)?$commodity->current_rate:''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_total_amount" required="required" class="form-control" id="sell_total_amount" tabindex="11"
                                                       value="<?php echo isset($commodity->total_amount)?$commodity->total_amount:''; ?>">
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
                                                <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2" ){
?>
   <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="13" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
<?php
}
else
{
?>
<a href="javascript:;" class="btn btn-xs btn-inverse-alt disable_btn" tabindex="13" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
<?php
}
?>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Commodities/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="delete_commodity('<?php echo $commodity->commodity_trans_id; ?>')"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="commodity_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Commodities/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="delete_commodity('<?php echo $commodity->commodity_trans_id; ?>')"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="commodity_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else
{ }
?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        var cnt=0;
        $( document ).ajaxComplete(function( event,request, settings ) {
            cnt++;
            if(cnt == 2) {
                $("#cancel").attr("disabled", false);
                if($("#isSold").val() == '1' && $("#transaction_type").val() == 'Purchase')
                {
                    enableBtn();
                    $('#edit').attr('disabled', true);
                    $.pnotify({
                        title: 'Info on property',
                        text: "You can't edit this property as it is already sold",
                        type: 'info',
                        hide: true
                    });
                    $(".dataTables_wrapper .btn").attr('disabled', true);
                }
            }
        });
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

        //initialize select2
        $('#family_id').select2("val",($('#hFamilyID').val()));
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, $('#hFamilyID').val(), 'client_id', 'nominee', $('#hClientID').val(), $('#hNominee').val());
        $('#commodity_item_id').select2("val",($('#hItemID').val()));
        get_item_units($('#hItemID').val());
        $('#commodity_unit_id').val($('#hUnitID').val()).change();
        $('#adviser_id, #sell_adviser_id').val($('#hAdviserID').val());
        //$('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();

        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', '', "", "");
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
                bootbox.alert("Could not get Commodity Item ID and/or Unit ID");
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

    //edit real estate in database
    function commodity_submit()
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
                    url: '<?php echo site_url('broker/commodities/update_purchase_commodity');?>',
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
                    url: '<?php echo site_url('broker/commodities/update_sell_commodity');?>',
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

    function delete_commodity(id)
    {
        bootbox.confirm("Are you sure you want to delete this commodity?", function(result) {
            if(result) {
                $.ajax({
                    url: "<?php echo site_url('broker/Commodities/delete_commodity');?>",
                    type:'post',
                    data: {id: id},
                    dataType: 'json',
                    success:function(data)
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type']
                        });
                        if(data['deleted'] == true) {
                            window.location.href="<?php echo base_url('broker/Commodities');?>";
                        }
                    },
                    error: function (data)
                    {
                        console.log(data);
                        $.pnotify({
                            title: 'Error!',
                            text: 'Error deleting commodity item',
                            type: 'error'
                        });
                    }
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
                var unitID = $("#hUnitID").val();
                var itemID = $("#hItemID").val();
                var selectedItemID = $("#commodity_item_id").val();
                if(unitID != "" && unitID != null && typeof unitID != 'undefined' && selectedItemID == itemID)
                {
                    $("#commodity_unit_id").select2('val', unitID);
                    //$("#commodity_unit_id").change();
                } else {
                    $("#current_rate").val('').change();
                    $("#transaction_rate").val('').change();
                    $("#total_amount").val('').change();
                }
                //$("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting property details from commodity item',
                    type: 'error',
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
                //$("#transaction_rate").val(data.current_rate).change();
                //$("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting property details from commodity item',
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
