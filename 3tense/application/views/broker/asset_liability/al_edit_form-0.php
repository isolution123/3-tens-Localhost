<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <?php if($asset_liab->transaction_type == 'asset'):?>
                <li><a href="<?php echo base_url('broker/Assets_liabilities/assets_list');?>">Assets</a></li>
                <?php else: ?>
                <li><a href="<?php echo base_url('broker/Assets_liabilities/liabilities_list');?>">Liabilities</a></li>
                <?php endif;?>
                <li class="active">Edit Assets or Liabilities</li>
            </ol>
            <h1>Edit Assets or Liabilities</h1>
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
                                <input type="hidden" name="hFamilyID" id="hFamilyID" value="<?= $asset_liab->family_id; ?>" />
                                <input type="hidden" name="hClientID" id="hClientID" value="<?= $asset_liab->client_id; ?>" />
                                <input type="hidden" name="hAssetLiabilityID" id="hAssetLiabilityID" value="<?= $asset_liab->pro_transaction_id; ?>" />
                                <input type="hidden" name="hType" id="hType" value="<?= $asset_liab->type_id; ?>" />
                                <input type="hidden" name="hProduct" id="hProduct" value="<?= $asset_liab->product_id; ?>" />
                                <input type="hidden" name="hCompany" id="hCompany" value="<?= $asset_liab->company_id; ?>" />
                                <input type="hidden" name="hScheme" id="hScheme" value="<?= $asset_liab->scheme_id; ?>" />
                                <input type="hidden" id="transaction_type" value="<?= $asset_liab->transaction_type; ?>">
                                <label class="col-sm-2 control-label" for="family_name">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="family_name" readonly class="form-control" id="family_name"
                                           value="<?php echo isset($asset_liab->family_name)?$asset_liab->family_name:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Type</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="type_name" readonly class="form-control" id="type_name"
                                           value="<?php echo isset($asset_liab->type_name)?$asset_liab->type_name:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="client_id">Client Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="client_name" readonly class="form-control" id="client_name"
                                           value="<?php echo isset($asset_liab->client_name)?$asset_liab->client_name:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Company</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="company_name" readonly class="form-control" id="company_name"
                                           value="<?php echo isset($asset_liab->company_name)?$asset_liab->company_name:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Product</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="product_name" readonly class="form-control" id="product_name"
                                           value="<?php echo isset($asset_liab->product_name)?$asset_liab->product_name:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Scheme</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="scheme_name" readonly class="form-control" id="scheme_name"
                                           value="<?php echo isset($asset_liab->scheme_name)?$asset_liab->scheme_name:'';?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-primary">
                <div class="panel-heading" style="height: auto">
                    <ul class="nav nav-tabs">
                        <?php if($asset_liab->transaction_type == 'asset'):?>
                            <li class="active">
                                <a href="#asset" tabindex="5" data-toggle="tab">Asset</a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" tabindex="6">Liability</a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="javascript:void(0)" tabindex="5">Asset</a>
                            </li>
                            <li class="active">
                                <a href="#liability" tabindex="6" data-toggle="tab">Liability</a>
                            </li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                <?php if($asset_liab->transaction_type == 'asset'):?>
                    <div class="tab-pane active no-border control-form" id="asset">
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
                                                <input type="text" name="goal" class="form-control" id="goal" tabindex="12"
                                                    value="<?php echo isset($asset_liab->goal)?$asset_liab->goal:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Ref. No.</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ref_number" readonly class="form-control" id="ref_number"
                                                    tabindex="13" value="<?php echo isset($asset_liab->ref_number)?$asset_liab->ref_number:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Start Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="start_date" readonly class="form-control"
                                                    data-inputmask="'alias':'date'" id="start_date" tabindex="14"
                                                    value="<?php echo isset($asset_liab->start_date)?$asset_liab->start_date:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">End Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="end_date" class="form-control date" required="required"
                                                    data-inputmask="'alias':'date'" id="end_date" tabindex="15"
                                                    value="<?php echo isset($asset_liab->end_date)?$asset_liab->end_date:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="narration" class="form-control" id="narration" tabindex="16" style="height: 109px"><?php echo isset($asset_liab->narration)?$asset_liab->narration:'';?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Installment Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="installment_amount" readonly class="form-control"
                                                    id="installment_amount" tabindex="17"
                                                    value="<?php echo isset($asset_liab->installment_amount)?$asset_liab->installment_amount:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Rate of Return</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="text" name="rate_of_return" readonly class="form-control" id="rate_of_return"
                                                    tabindex="18" value="<?php echo isset($asset_liab->rate_of_return)?$asset_liab->rate_of_return:'';?>">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Expected Maturity Value</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="expected_mat_value" readonly class="form-control"
                                                    id="expected_mat_value" tabindex="19"
                                                    value="<?php echo isset($asset_liab->expected_mat_value)?$asset_liab->expected_mat_value:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else:?>
                    <div class="tab-pane no-border active control-form" id="liability">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="liability_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Liabilities Details</h4>
                                    <div class="options">
                                        <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Particular</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="particular" readonly class="form-control" id="particular" disabled
                                                    tabindex="12" value="<?php echo isset($asset_liab->particular)?$asset_liab->particular:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Ref. No.</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_ref_number" readonly class="form-control" id="l_ref_number" disabled
                                                    tabindex="13" value="<?php echo isset($asset_liab->ref_number)?$asset_liab->ref_number:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Start Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_start_date" readonly class="form-control"
                                                    data-inputmask="'alias':'date'" id="l_start_date" tabindex="14" disabled
                                                    value="<?php echo isset($asset_liab->start_date)?$asset_liab->start_date:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">End Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_end_date" class="form-control" readonly
                                                    data-inputmask="'alias':'date'" id="l_end_date" tabindex="15"
                                                    value="<?php echo isset($asset_liab->end_date)?$asset_liab->end_date:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="l_narration" class="form-control" id="l_narration" tabindex="16" style="height: 109px"><?php echo isset($asset_liab->narration)?$asset_liab->narration:'';?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Installment Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_installment_amount" required="required" class="form-control"
                                                   id="l_installment_amount" tabindex="17" readonly
                                                   value="<?php echo isset($asset_liab->installment_amount)?$asset_liab->installment_amount:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label"></label>
                                            <div class="col-sm-4">
                                                <?php if ($asset_liab->pre_payment == 1):?>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="pre_payment" value="1" id="pre_payment" tabindex="17" checked>
                                                    Pre - Payment
                                                </label>
                                                <?php else:?>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="pre_payment" value="0" id="pre_payment" tabindex="17">
                                                    Pre - Payment
                                                </label>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Interest Rate</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="text" name="interest_rate" required="required" class="form-control" id="interest_rate"
                                                    tabindex="18" readonly
                                                    value="<?php echo isset($asset_liab->interest_rate)?$asset_liab->interest_rate:'';?>">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Total Liability</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="total_liability" class="form-control" id="total_liability"
                                                    tabindex="19" readonly
                                                    value="<?php echo isset($asset_liab->total_liability)?$asset_liab->total_liability:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-heading">
                                    <h4>Payment History</h4>
                                    <div class="options">
                                        <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div>
                                              <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2" ){
?>
   <a class="btn btn-success" id="btn_payment" tabindex="20" href="javascript:void(0)" onclick="open_payment_modal()"><i class="fa fa-plus"></i> Add Payment</a>
<?php
}
else
{
?>
  <!--<a class="btn btn-success" id="btn_payment disable_btn" tabindex="20" href="javascript:void(0)" onclick="open_payment_modal()" ><i class="fa fa-plus"></i> Add Payment</a>-->
<?php
}
?>

                                                <br /><br />
                                                <table border="0" class="table table-striped table-bordered" id="paymentTable">
                                                    <thead>
                                                    <tr>
                                                        <th>History Liability ID</th>
                                                        <th>Amount</th>
                                                        <th>Date of Payment</th>
                                                        <th>Narration</th>
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
                <?php endif;?>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <?php if($asset_liab->transaction_type == 'asset'):?>
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Assets_liabilities/add_form?trans_type=asset");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <?php else: ?>
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Assets_liabilities/add_form?trans_type=liability");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <?php endif; ?>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn_1()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="del_asset_liability()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="asset_liability_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <?php if($asset_liab->transaction_type == 'asset'):?>
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Assets_liabilities/add_form?trans_type=asset");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <?php else: ?>
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/Assets_liabilities/add_form?trans_type=liability");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <?php endif; ?>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn_1()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="del_asset_liability()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="asset_liability_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
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
        </div>
    </div>
</div>
<!-- Bootstrap modal -->
<div class="modal fade" id="payment_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="payment_modal" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <input type="hidden" value="" name="liabilityIDEdit"/>
                    <input type="hidden" value="" name="liabilityIntRate"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="amount">Amount</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="amount" required="required" id="amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="history_date">Pre-Payment Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="history_date" data-inputmask="'alias':'date'" required="required"
                                    id="history_date" value="<?php echo date('d/m/Y');?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="hist_end_date">End Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="hist_end_date" data-inputmask="'alias':'date'" required="required" id="hist_end_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="hist_narration">Narration</label>
                            <div class="col-md-8">
                                <textarea name="hist_narration" class="form-control" id="hist_narration" style="height: 109px"></textarea>
                            </div>
                        </div>
                        <?php //original installment amount ?>
                        <input type="hidden" id="originalAmt" name="originalAmt" value="" />
                        <input type="hidden" id="originalStartDate" name="originalStartDate" value="" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_payment()" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">
var oTable, family_id, payInfo;
var save_method, family_control;
$(function() {
    disableBtn_1();
    $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
    payment_list();
    check_pre_payment($('#pre_payment').prop('checked'));

    $('#pre_payment').change(function(){
        check_pre_payment(this.checked)
    });
});

function disableBtn_1() {
    disableBtn();
    $("#l_installment_amount").attr('readonly', true);
    $("#interest_rate").attr('readonly', true);
}

function check_pre_payment(check)
{
    if(check)
    {
        $('#pre_payment').val(1);
        $('#btn_payment').attr('disabled', false);
    }
    else
    {
        $('#pre_payment').val(0);
        /*$("#l_installment_amount").attr('readonly', true);
        $("#interest_rate").attr('readonly', true);*/
        $('#btn_payment').attr('disabled', true);
    }
}

$("#end_date").change(function(){
    var ror = $('[name="rate_of_return"]').val();
    var install_amount = $('[name="installment_amount"]').val();
    var start_date = $('[name="start_date"]').val();
    var end_date = $('[name="end_date"]').val();
    var tempEDate = process($('#end_date').val());
    var tempSDate = process($('#start_date').val());
    if(start_date != '' && end_date != '')
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
            $("#expected_mat_value").val("");
            $.pnotify({
                title: 'Error in Date',
                text: 'End Date must be greater then Start Date',
                type: 'error',
                hide: true
            });
        }
    }
});

//edit real estate in database
function asset_liability_submit()
{
    var valid = '';
    var trans_type = $("#transaction_type").val();
    if(trans_type == 'asset')
    {
        valid = $('#asset_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/Assets_liabilities/edit_asset');?>',
                type: 'post',
                data: $('#client_form, #asset_form').serialize(),
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
    else if(trans_type == 'liability')
    {
        valid = $('#liability_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/Assets_liabilities/edit_liability');?>',
                type: 'post',
                data: $('#client_form, #liability_form').serialize(),
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
                        enableBtn();
                        $("#l_installment_amount").attr('readonly', false);
                        $("#interest_rate").attr('readonly', false);
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

function del_asset_liability()
{
    if($("#transaction_type").val() == 'asset')
    {
        bootbox.confirm("Are you sure you want to delete this asset?", function(result) {
            if(result)
            {
                var asset_id = $('#hAssetLiabilityID').val();
                $.ajax({
                    url: '<?php echo site_url('broker/Assets_liabilities/delete_asset');?>',
                    type: 'post',
                    data: {asset_id: asset_id},
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            bootbox.alert("Asset is deleted");
                            location.href = '<?php echo base_url();?>broker/Assets_liabilities/assets_list';
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
        });
    }
    else if($("#transaction_type").val() == 'liability')
    {
        bootbox.confirm("Are you sure you want to delete this liability?", function(result) {
            if(result)
            {
                var liability_id = $('#hAssetLiabilityID').val();
                $.ajax({
                    url: '<?php echo site_url('broker/Assets_liabilities/delete_liability');?>',
                    type: 'post',
                    data: {liability_id: liability_id},
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            bootbox.alert("Liability is deleted");
                            location.href = '<?php echo base_url();?>broker/Assets_liabilities/liabilities_list';
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
        });
    }
}

//get payment details in datatable
function payment_list() {
    oTable = $("#paymentTable").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "scrollY": "200px",
        "scrollCollapse": true,
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, -1 ] },
            { "bSearchable": false, "aTargets": [ 0, 1, -1 ] },
            { "bVisible": false, "aTargets": [ 0 ] }],
        "bPaginate": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('broker/Assets_liabilities/payment_list');?>',
            "type": 'post',
            "data": {liabID: $('#hAssetLiabilityID').val()},
            error: function(data)
            {
                console.log(data);
            },
            "dataSrc": function (json) {
                if(!json.data){
                    json.data = [];
                }
                return json.data;
            }
        },
        "columns": [
            { "data": "liab_hist_id" },
            { "data": "amount" },
            { "data": "payment_date", "type": "date-uk" },
            { "data": "narration"},
            { "data": "action" }
        ],
        "initComplete": function(settings, json){
            payInfo = this.api().page.info();
            console.log(payInfo.recordsTotal);
            if(payInfo.recordsTotal > 0) {
                //make pre-payment checked
                $('#pre_payment').val(1);
                $('#pre_payment').prop("checked",true).change();
                $('#pre_payment').prop("disabled",true);
            } else {
                $('#pre_payment').val(0);
                $('#pre_payment').prop("checked",false).change();
                $('#pre_payment').prop("disabled",false);
            }
            //console.log('Total records', info.recordsTotal);
            //console.log('Displayed records', info.recordsDisplay);
        }
    });
    return oTable;
}

//popup to add payment details
function open_payment_modal()
{
    //reset form on modals
    //show bootstrap modal
    $("#payment_modal_form").modal('show');
    var dt = new Date();
    $('[name="liabilityIDEdit"]').val($('#hAssetLiabilityID').val());
    $('[name="liabilityIntRate"]').val($('#interest_rate').val());
    $('[name="hist_end_date"]').datepicker("setDate", $('#l_end_date').val());
    $('[name="history_date"]').datepicker("remove");
    $('[name="history_date"]').datepicker({format: 'dd/mm/yyyy', startDate: $('#l_start_date').val()});

    $('#originalAmt').val($('#l_installment_amount').val());
    $('#originalStartDate').val($('#l_start_date').val());
    //set title to modal
    $(".modal-title").text('Add Payment');
    save_method = 'add';
}

//add payment details in datatable
function save_payment()
{
    var payment_form = $('#payment_modal');
    var endDate = $("#hist_end_date").val();
    if(payment_form.parsley('validate'))
    {
        $.ajax({
            url: "<?php echo site_url('broker/Assets_liabilities/add_payment');?>",
            type:'post',
            data: payment_form.serialize(),
            dataType: 'json',
            success:function(data)
            {
                //hide bootstrap modal
                $("#payment_modal_form").modal('hide');

                if(data['status'] == 'success') {
                    if(oTable != null)
                    {
                        $('#paymentTable').empty();
                        oTable.destroy();
                    }
                    oTable = payment_list();

                    $("#l_end_date").val(endDate);
                    $("#l_installment_amount").attr('readonly', false);
                    $("#l_installment_amount").css({"border":"3px solid cornflowerblue"});
                    $("#l_installment_amount").focus();
                    $("#interest_rate").attr('readonly', false);
                    $("#interest_rate").css({"border":"3px solid cornflowerblue"});

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'success',
                        hide: true
                    });
                } else {
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

//delete payment details in datatable
function delete_payment(id)
{
    bootbox.confirm('Are you sure you want to delete this payment details?', function(result){
        if(result)
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('broker/Assets_liabilities/delete_payment');?>",
                type: "POST",
                data:{id: id},
                dataType: "JSON",
                success: function(data)
                {
                    if(data['status'])
                    {
                        //if success reload ajax table
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                        oTable.destroy();
                        oTable = payment_list();
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


$("#l_installment_amount, #interest_rate").on("blur change keyup", function() {
    $(this).removeAttr("style");
});

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
