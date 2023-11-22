<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <?php if($_GET['trans_type'] == 'asset'):?>
                <li><a href="<?php echo base_url('broker/Assets_liabilities/assets_list');?>">Assets</a></li>
                <?php else: ?>
                <li><a href="<?php echo base_url('broker/Assets_liabilities/liabilities_list');?>">Liabilities</a></li>
                <?php endif; ?>
                <li class="active">Add Assets or Liabilities</li>
            </ol>
            <h1>Add Assets or Liabilities</h1>
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
                                <?php if($_GET['trans_type'] == 'asset'):?>
                                    <input type="hidden" value="asset" id="transaction_type" name="transaction_type">
                                <?php else: ?>
                                    <input type="hidden" value="liability" id="transaction_type" name="transaction_type">
                                <?php endif; ?>
                                <input type="hidden" value="" id="pro_transaction_id" name="pro_transaction_id">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="family_id" class="populate" required="required" id="family_id" style="width: 80%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0);" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Type</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="type_id" class="populate" required="required" id="type_id" style="width: 80%" tabindex="6">
                                        <option disabled selected value="">Select Type</option>
                                        <?php foreach($al_type as $row):?>
                                            <option value='<?php echo $row->type_id; ?>'><?php echo $row->type_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0);" tabindex="7" class="btn btn-xs btn-inverse-alt" onclick="add_al_type(true)"><i class="fa fa-plus"></i></a>
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
                                <label class="col-sm-2 control-label">Company</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="company_id" class="populate" required="required" id="company_id" style="width: 80%" tabindex="8">
                                        <option disabled selected value="">Select Company</option>
                                        <?php foreach($al_company as $row):?>
                                            <option value='<?php echo $row->company_id; ?>'><?php echo $row->company_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0);" tabindex="9" class="btn btn-xs btn-inverse-alt" onclick="add_al_company(true)"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Product</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="product_id" class="populate" required="required" id="product_id" style="width: 80%" tabindex="4">
                                        <option disabled selected value="">Select Product</option>
                                        <?php foreach($al_product as $row):?>
                                            <option value='<?php echo $row->product_id; ?>'><?php echo $row->product_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0);" tabindex="5" class="btn btn-xs btn-inverse-alt" onclick="add_al_product(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Scheme</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="scheme_id" class="populate" required="required" id="scheme_id" style="width: 80%" tabindex="10">
                                        <option disabled selected value="">Select Scheme</option>
                                        <?php foreach($al_scheme as $row):?>
                                            <option value='<?php echo $row->scheme_id; ?>'><?php echo $row->scheme_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0);" tabindex="11" class="btn btn-xs btn-inverse-alt" onclick="add_al_scheme(true)"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-primary control-form">
                <div class="panel-heading" style="height: auto">
                    <ul class="nav nav-tabs">
                        <?php if($_GET['trans_type'] == 'asset'):?>
                        <li class="active">
                            <a href="#asset" tabindex="12" data-toggle="tab">Asset</a>
                        </li>
                        <li>
                            <a href="#liability" tabindex="13" data-toggle="tab">Liability</a>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="#asset" tabindex="12" data-toggle="tab">Asset</a>
                        </li>
                        <li class="active">
                            <a href="#liability" tabindex="13" data-toggle="tab">Liability</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="panel-body control-form">
                <div class="tab-content">
                    <?php if($_GET['trans_type'] == 'asset'):?>
                        <div class="tab-pane active no-border" id="asset">
                    <?php else: ?>
                        <div class="tab-pane no-border" id="asset">
                    <?php endif; ?>
                        <div class="panel panel-midnightblue">
                            <form action="#" id="asset_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Asset Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Goal</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="goal" required="required" class="form-control" id="goal" tabindex="14" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Ref. No.</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ref_number" required="required" class="form-control" id="ref_number" tabindex="15" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Start Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="start_date" required="required" class="form-control date" data-inputmask="'alias':'date'" id="start_date" tabindex="16" value="<?php date('d/m/Y');?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">End Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="end_date" required="required" class="form-control date" data-inputmask="'alias':'date'" id="end_date" tabindex="17" value="<?php date('d/m/Y');?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="narration" class="form-control" id="narration" tabindex="18" style="height: 109px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Installment Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="installment_amount" required="required" class="form-control" id="installment_amount" tabindex="19" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Rate of Return</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="text" name="rate_of_return" required="required" class="form-control" id="rate_of_return" tabindex="20" value="">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Expected Maturity Value</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="expected_mat_value" required="required" class="form-control" id="expected_mat_value" tabindex="21" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if($_GET['trans_type'] == 'asset'):?>
                            <div class="tab-pane no-border" id="liability">
                    <?php else: ?>
                            <div class="tab-pane active no-border" id="liability">
                    <?php endif; ?>
                        <div class="panel panel-midnightblue">
                            <form action="#" id="liability_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Liabilities Details</h4>
                                    <div class="options">
                                        <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particular</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="particular" required="required" class="form-control" id="particular" tabindex="12" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Ref. No.</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_ref_number" required="required" class="form-control" id="l_ref_number" tabindex="13" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Start Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_start_date" required="required" class="form-control date" data-inputmask="'alias':'date'" id="l_start_date" tabindex="14" value="<?php date('d/m/Y');?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">End Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_end_date" required="required" class="form-control date" data-inputmask="'alias':'date'" id="l_end_date" tabindex="15" value="<?php date('d/m/Y');?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="l_narration" class="form-control" id="l_narration" tabindex="16" style="height: 109px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Installment Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_installment_amount" required="required" class="form-control" id="l_installment_amount" tabindex="17" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label"></label>
                                            <div class="col-sm-4">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="pre_payment" required="required" value="1" id="pre_payment" tabindex="17">
                                                    Pre - Payment
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Interest Rate</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="text" name="interest_rate" required="required" class="form-control" id="interest_rate" tabindex="18" value="">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Liability</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="total_liability" required="required" class="form-control" id="total_liability" tabindex="19" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" tabindex="21" id="add" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <a tabindex="22" id="edit" href="javascript:void(0)"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</a>
                    <button type="button" tabindex="23" id="delete" onclick="del_asset_liability()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" tabindex="24" id="save" onclick="asset_liability_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" tabindex="25" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var oTable;
var save_method;
$(function() {
    disableBtn();
    $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
    $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
    $('#pre_payment').attr('disabled', true);
    //on family change get clients
    $('#family_id').change(function()
    {
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, this.value, 'client_id', '', "", "");
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

    $("#add").click(function(){
        $('#pre_payment').attr('disabled', true);
    });

    //end date should not be less than start date
    $("#start_date").change(function() {
        $("#end_date").datepicker("remove");
        $("#end_date").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
    });
    $("#l_start_date").change(function() {
        $("#l_end_date").datepicker("remove");
        $("#l_end_date").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
    });

    $("#rate_of_return, #installment_amount, #start_date, #end_date").change(function(){
        var ror = $('[name="rate_of_return"]').val();
        var install_amount = $('[name="installment_amount"]').val();
        var start_date = $('[name="start_date"]').val();
        var end_date = $('[name="end_date"]').val();
        var tempEDate = process($('#end_date').val());
        var tempSDate = process($('#start_date').val());
        if(ror != '' && install_amount != '' && start_date != '' && end_date != '')
        {
            if(tempSDate < tempEDate)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/Assets_liabilities/calculate_maturity')?>',
                    type: 'post',
                    data: {rate_of_return: ror, install_amount: install_amount, start_date: start_date, end_date: end_date},
                    dataType: 'json',
                    success: function(data){
                        $("#expected_mat_value").val(data['mat_mat']);
                    },
                    error: function(data){
                        console.log(data);
                        bootbox.alert('Something went wrong');
                    }
                });
            }
            else
            {
                $("#rate_of_return").val('');
                $.pnotify({
                    title: 'Error in Date',
                    text: 'End Date must be greater then Start Date',
                    type: 'error',
                    hide: true
                });
            }
        }
    });
});

$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var target = $(e.target).attr("href"); // activated tab
    if(target == '#asset')
    {
        $("#transaction_type").val('asset');
    }
    else if(target == '#liability')
    {
        $("#transaction_type").val('liability');
    }
});

//insert real estate in database
function asset_liability_submit()
{
    var trans_type = $("#transaction_type").val();
    var valid = $('#client_form').parsley('validate');
    if(trans_type == 'asset' && valid)
    {
        //debugger;
        valid = $('#asset_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/Assets_liabilities/add_asset');?>',
                type: 'post',
                data: $('#client_form, #asset_form').serialize(),
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
                        $('#edit').attr('href', '<?php echo base_url();?>broker/Assets_liabilities/edit_form?id='+data['transaction_id']+'&trans_type='+$("#transaction_type").val());
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
    else if(trans_type == 'liability' && valid)
    {
        valid = $('#liability_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/Assets_liabilities/add_liability');?>',
                type: 'post',
                data: $('#client_form, #liability_form').serialize(),
                dataType: 'json',
                success:function(data)
                {
                    //debugger;
                    if(data['status'] == 'success')
                    {
                        $('#pro_transaction_id').val(data['transaction_id']);
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                        $('#edit').attr('href', '<?php echo base_url();?>broker/Assets_liabilities/edit_form?id='+data['transaction_id']+'&trans_type='+$("#transaction_type").val());
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
</script>