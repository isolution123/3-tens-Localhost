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
                                <input type="hidden" name="hFamilyID" id="hFamilyID" value="<?= $real_estate->family_id; ?>" />
                                <input type="hidden" name="hClientID" id="hClientID" value="<?= $real_estate->client_id; ?>" />
                                <input type="hidden" name="hPropertyID" id="hPropertyID" value="<?= $real_estate->pro_transaction_id; ?>" />
                                <input type="hidden" name="hPropertyType" id="hPropertyType" value="<?= $real_estate->property_type_id; ?>" />
                                <input type="hidden" name="hUnit" id="hUnit" value="<?= $real_estate->property_unit_id; ?>" />
                                <input type="hidden" name="hAdviser" id="hAdviser" value="<?= $real_estate->adviser_id; ?>" />
                                <input type="hidden" id="transaction_type" value="<?= $real_estate->transaction_type; ?>">
                                <input type="hidden" id="isSold" value="<?= $isSold; ?>">
                                <label class="col-sm-2 control-label" for="family_id">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="family_id" class="populate" required="required" id="family_id" style="width: 80%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label" for="transaction_date">Date of Transaction</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="4"
                                           value="<?php echo isset($real_estate->transaction_date)?$real_estate->transaction_date:'';?>"
                                           class="form-control date disabled" id="transaction_date" data-inputmask="'alias':'date'" disabled >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="client_id">Client Name</label>
                                <div class="col-md-4">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 100%" tabindex="3">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-primary">
                <div class="panel-heading" style="height: auto">
                    <ul class="nav nav-tabs">
                        <?php if($real_estate->transaction_type == 'Sale'):?>
                        <li>
                            <a href="javascript:void(0)" tabindex="5">Purchase</a>
                        </li>
                        <li class="active">
                            <a href="#sell" tabindex="6" data-toggle="tab">Sell</a>
                        </li>
                        <?php else: ?>
                        <li class="active">
                            <a href="#purchase" tabindex="5" data-toggle="tab">Purchase</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" tabindex="6">Sell</a>
                        </li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <?php if($real_estate->transaction_type == 'Purchase'):?>
                    <div class="tab-pane active no-border control-form" id="purchase">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="re_purchase_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Real Estate Purchase Details</h4>
                                    <div class="options">
                                        <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="property_name">Property Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_name" required="required" class="form-control" id="property_name"
                                                    tabindex="7" value="<?php echo isset($real_estate->property_name)?$real_estate->property_name:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="property_type_id">Property Type</label>
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
                                            <label class="col-sm-4 control-label" for="property_location">Property Location</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_location" required="required" class="form-control" id="property_location"
                                                    tabindex="9" value="<?php echo isset($real_estate->property_location)?$real_estate->property_location:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="property_area">Property Area</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_area" class="form-control" id="property_area" required="required" tabindex="10" style="width: 40%; display: inline-block" value="<?php echo isset($real_estate->property_area)?$real_estate->property_area:'';?>">
                                                <select name="property_unit_id" class="populate" required="required" id="property_unit_id" tabindex="11" style="width: 50%; vertical-align: bottom">
                                                    <option disabled selected>Select Unit</option>
                                                    <?php foreach($units as $row):?>
                                                        <option value='<?php echo $row->unit_id; ?>'><?php echo $row->unit_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="transaction_rate">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="transaction_rate" required="required" class="form-control" id="transaction_rate"
                                                    tabindex="12" value="<?php echo isset($real_estate->transaction_rate)?$real_estate->transaction_rate:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="amount">Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="amount" required="required" class="form-control" id="amount" tabindex="13"
                                                    value="<?php echo isset($real_estate->amount)?$real_estate->amount:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="current_rate">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="current_rate" required="required" class="form-control" id="current_rate"
                                                       tabindex="14" value="<?php echo isset($real_estate->current_rate)?$real_estate->current_rate:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="property_updated_on">Updated On</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="property_updated_on" required="required" class="form-control date"
                                                       data-inputmask="'alias':'date'" id="property_updated_on" tabindex="15"
                                                       value="<?php echo isset($real_estate->property_updated_on)?$real_estate->property_updated_on:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="remarks">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="remarks" class="form-control" id="remarks" tabindex="16" style="height: 109px"><?php echo isset($real_estate->remarks)?$real_estate->remarks:'';?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="adviser_id">Advisor</label>
                                            <div class="col-sm-8">
                                                <select name="adviser_id" class="populate" required="required" id="adviser_id" tabindex="17" style="width: 100%">
                                                    <option disabled selected>Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <a href="javascript:void(0);" class="btn btn-xs btn-inverse-alt" tabindex="18" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="deposit_amount">Deposit</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="deposit_amount" class="form-control" id="deposit_amount" tabindex="19" value="<?php echo isset($real_estate->deposit_amount)?$real_estate->deposit_amount:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="panel-heading">
                                    <h4>Rent Details</h4>
                                    <div class="options">
                                        <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
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
                                                        <th>Rent ID</th>
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
                    <?php else:?>
                    <div class="tab-pane no-border active" id="sell">
                        <div class="panel panel-midnightblue">
                            <form action="#" id="re_sell_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                <div class="panel-heading">
                                    <h4>Real Estate Sell Details</h4>
                                    <div class="options">
                                        <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_property_name">Property Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_property_name" class="form-control" id="sell_property_name" readonly
                                                       value="<?php echo isset($real_estate->property_name)?$real_estate->property_name:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_pro_type">Property Type</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_type" class="form-control" id="sell_pro_type" disabled
                                                    value="<?php echo isset($real_estate->property_type_name)?$real_estate->property_type_name:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_pro_location">Property Location</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_location" class="form-control" id="sell_pro_location" disabled
                                                    value="<?php echo isset($real_estate->property_location)?$real_estate->property_location:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_pro_area">Property Area</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_area" class="form-control" id="sell_pro_area"
                                                    style="width: 40%; display: inline-block" disabled
                                                    value="<?php echo isset($real_estate->property_area)?$real_estate->property_area:'';?>">
                                                <input type="text" name="sell_unit_id" class="form-control" id="sell_unit_id"
                                                    style="width: 40%; display: inline-block" disabled
                                                    value="<?php echo isset($real_estate->unit_name)?$real_estate->unit_name:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_trans_rate">Transaction Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_trans_rate" required="required" class="form-control"
                                                    id="sell_trans_rate" tabindex="8" value="<?php echo isset($real_estate->transaction_rate)?$real_estate->transaction_rate:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_amount">Amount</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_amount" required="required" class="form-control" id="sell_amount"
                                                    tabindex="9" value="<?php echo isset($real_estate->amount)?$real_estate->amount:'';?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_curr_rate">Current Rate</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_curr_rate" class="form-control" id="sell_curr_rate" disabled
                                                    value="<?php echo isset($real_estate->current_rate)?$real_estate->current_rate:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_pro_upd_on">Updated On</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="sell_pro_upd_on" class="form-control" id="sell_pro_upd_on" disabled
                                                    value="<?php echo isset($real_estate->property_updated_on)?$real_estate->property_updated_on:'';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_remarks">Remarks</label>
                                            <div class="col-sm-8">
                                                <textarea name="sell_remarks" class="form-control" id="sell_remarks" tabindex="10" style="height: 109px"><?php echo isset($real_estate->remarks)?$real_estate->remarks:'';?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label" for="sell_adviser_id">Advisor</label>
                                            <div class="col-sm-8">
                                                <select name="sell_adviser_id" class="populate" required="required" id="sell_adviser_id" tabindex="11" style="width: 80%">
                                                    <option disabled selected>Select Advisor</option>
                                                    <?php foreach($adv as $row):?>
                                                        <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <a href="javascript:void(0);" class="btn btn-xs btn-inverse-alt" tabindex="18" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
                <div class="panel panel-midnightblue no-border">
                    <form action="#" id="re_gain_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                        <div class="panel-heading">
                            <h4>Realised or Unrealised Gain</h4>
                            <div class="options">
                                <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                        <div class="panel-body collapse in">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="gain">Gain</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="gain" class="form-control" id="gain" readonly
                                                   value="<?php echo isset($real_estate->gain)?$real_estate->gain:'';?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="total_gain">Total Gain</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="total_gain" class="form-control" id="total_gain" readonly
                                                   value="<?php echo isset($real_estate->total_gain)?$real_estate->total_gain:'';?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="abs">ABS %</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="abs" class="form-control" id="abs" readonly
                                               value="<?php echo isset($real_estate->abs)?$real_estate->abs:'';?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label" for="cagr">CAGR %</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="cagr" class="form-control" id="cagr" readonly
                                                   value="<?php echo isset($real_estate->cagr)?$real_estate->cagr:'';?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bottom-row navbar-fixed-bottom">
              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/real_estate/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="delete_re()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="real_estate_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="19" onclick="addForm('<?php echo base_url("broker/real_estate/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="20" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="delete_re()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="real_estate_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
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
                            <input name="proRentIDEdit" id="proRentIDEdit" type="hidden">
                            <input name="proTransIDEdit" id="proTransIDEdit" type="hidden">
                            <input name="isEdit" id="isEdit" type="hidden" value="true">
                            <label class="control-label col-md-3" for="fromDate">From Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="fromDate" data-inputmask="'alias':'date'" required="required" id="fromDate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="toDate">To Date</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control date" name="toDate" data-inputmask="'alias':'date'" required="required" id="toDate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="rentAmt">Amount</label>
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


    var oTable, family_id;
    var save_method, family_control;
    $(function() {
        var cnt=0;
        $( document ).ajaxComplete(function() {
            cnt++;
            if(cnt == 2) {
                $("#cancel").attr("disabled", false);
                if($("#isSold").val() == 'true' && $("#transaction_type").val() == 'Purchase')
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
        family_id = $('#hFamilyID').val();
        family_control = $('#family_id');
        debugger;
        //initialize select2
        family_control.val(family_id);
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, family_id, 'client_id', 'nominee', $('#hClientID').val(), $('#hNominee').val());
        $('#property_type_id').val($('#hPropertyType').val());
        $('#property_unit_id').val($('#hUnit').val());
        $('#adviser_id, #sell_adviser_id').val($('#hAdviser').val());
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();

        //on family change get clients
        family_control.change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', '', "", "");
        });

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
                        $("#gain").val(data['gain']);
                        $("#total_gain").val(data['total_gain']);
                        $("#abs").val(data['abs']);
                        $("#cagr").val(data['cagr']);
                    },
                    error: function(data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        });
        rent_list();
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
                    url: '<?php echo site_url('broker/real_estate/edit_real_estate');?>',
                    type: 'post',
                    data: $('#client_form, #re_purchase_form, #re_gain_form').serialize(),
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
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
                    url: '<?php echo site_url('broker/real_estate/edit_sell_real_estate');?>',
                    type: 'post',
                    data: $('#client_form, #re_sell_form').serialize(),
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
    }


   function calculate_cagr_abs()
    {
         //calculate cagr and abs
             //alert( $('#pro_transaction_id').val());
             console.log($('#hPropertyID').val());
             console.log("gone inside");
             var propTranId=$('[name="hPropertyID"]').val();
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

    function delete_re()
    {
        bootbox.confirm("Are you sure you want to delete this Real Estate?", function(result) {
            if(result)
            {
                var real_estate_id = $('#hPropertyID').val();
                $.ajax({
                    url: '<?php echo site_url('broker/Real_estate/delete_real_estate');?>',
                    type: 'post',
                    data: {re_id: real_estate_id},
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: 'error',
                                hide: true
                            });
                            bootbox.alert("Real Estate is deleted");
                            location.href = '<?php echo base_url();?>broker/Real_estate';
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
                    option = option + "<option value="+data[i].property_name+">"+data[i].property_name+"</option>";
                });
                $("#sell_property_name").html(option);
                $("#sell_pro_type, #sell_pro_location, #sell_pro_area, #sell_unit_id, #sell_curr_rate, #sell_pro_upd_on").val('');
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting policy details from client',
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
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, -1 ] },
                { "bSearchable": false, "aTargets": [ 0, 1, -1 ] },
                { "bVisible": false, "aTargets": [ 0, 1 ] }],
            "bPaginate": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/real_estate/rent_list');?>',
                "type": 'post',
                "data": {isEdit: true},
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
                { "data": "proRentID" },
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
        $('[name="proTransIDEdit"]').val($('#hPropertyID').val());
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
            data:{id: id, isEdit: true},
            dataType: "JSON",
            success: function(data)
            {
                //debugger;
                $('[name="srNoEdit"]').val(data.srNo);
                $('[name="proRentIDEdit"]').val(id);
                $('[name="proTransIDEdit"]').val($('#hPropertyID').val());
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
                    data:{id: id, isEdit: true},
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
