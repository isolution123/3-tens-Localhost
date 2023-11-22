<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Insurances');?>">Insurance</a></li>
                <li class="active">Add Insurance</li>
            </ol>
            <h1>Add Insurance</h1>

        </div>
        <div class="container ">
            <form action="#" id="client_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3  no-border add-new-btn">
                                    <select name="family_id" class="populate" required="required" id="family_id" style="width: 80%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Policy Number</label>
                                <div class="col-md-5">
                                    <select name="policy_num" class="form-control" required="required" id="policy_num" style="width: 48%; display: inline-block; margin-right: 5px;" tabindex="4">
                                        <option disabled selected value="">Select Policy Number</option>
                                    </select>
                                    <a href="javascript:;" tabindex="5" class="btn btn-xs btn-inverse-alt" onclick="add_ins_policy()"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-3">
                                    <input type="hidden" value="" name="client_name" id="client_name">
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
                            <a href="#insurance" data-toggle="tab">Insurance</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tab">Premium</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tab">Fund Options</a>
                        </li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane active no-border" id="insurance">
                            <div class="panel panel-midnightblue">
                                <form action="#" id="ins_form" method="post" class="form-horizontal row-border" data-validate="parsley">
                                    <div class="panel-heading">
                                        <h4>Insurance Details</h4>
                                        <div class="options">
                                            <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                        </div>
                                    </div>
                                    <div class="panel-body collapse in">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Company Name</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="ins_comp_id" class="form-control" id="ins_comp_id">
                                                    <input type="text" name="compName" class="form-control" id="compName" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Plan Name</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="plan_id" class="form-control" id="plan_id">
                                                    <input type="text" name="planName" class="form-control" id="planName" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Plan Category</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="plan_type_id" class="form-control" id="plan_type_id">
                                                    <input type="text" name="planCategory" class="form-control" id="planCategory" readonly>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-4 control-label">Mode</label>
                                                <div class="col-sm-8">
                                                    <select name="mode" class="populate" required="required" tabindex="10" id="mode" style="width: 100%">
                                                        <option disabled selected>Select Premium Mode</option>
                                                        <?php foreach($prem_mode as $row):?>
                                                            <option value='<?php echo $row->mode_id; ?>'><?php echo $row->mode_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Commence Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="commence_date" tabindex="6" required="required" class="form-control date" data-inputmask="'alias':'date'" id="commence_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Premium Paying Term</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="paidup_date" tabindex="7" required="required" class="form-control date" data-inputmask="'alias':'date'" id="paidup_date">
                                                    <div class="col-sm-8">
                                                        <input type="number" name="ppt" tabindex="7" required="required" class="form-control" id="ppt" min="0">
                                                    </div>
                                                    <label class="control-label">years</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Benefit Term</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="maturity_date" tabindex="8" required="required" class="form-control date" data-inputmask="'alias':'date'" id="maturity_date">
                                                    <div class="col-sm-8">
                                                        <input type="number" name="bt" tabindex="7" required="required" class="form-control" id="bt" min="0">
                                                    </div>
                                                    <label class="control-label">years</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Sum Assured</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="amt_insured" tabindex="9" required="required" class="form-control" id="amt_insured">
                                                </div>
                                            </div>
                                           
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Installment Premium Amt</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" required="required" tabindex="11" name="prem_amt" id="prem_amt">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Asset Allocation</label>
                                                <div class="col-sm-8 add-new-btn">
                                                    <select name="prem_type" class="populate" required="required" tabindex="12" id="prem_type" style="width: 80%">
                                                        <option disabled selected>Select Asset/Premium</option>
                                                        <?php foreach($prem_types as $row):?>
                                                            <option value='<?php echo $row->prem_type_id; ?>'><?php echo $row->prem_type_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:;" tabindex="13" class="btn btn-xs btn-inverse-alt" onclick="add_premium_type(true)"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Payment Option</label>
                                                <div class="col-sm-8">
                                                    <select name="prem_pay_mode_id" class="populate" tabindex="14" required="required" id="prem_pay_mode_id" style="width: 100%">
                                                        <option disabled selected>Select Payment Option</option>
                                                        <?php foreach($prem_pay_mode as $row):?>
                                                            <option value='<?php echo $row->prem_pay_mode_id; ?>'><?php echo $row->prem_pay_mode; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">First Unpaid Premium Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="next_prem_due_date" id="next_prem_due_date" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Grace Due Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="grace_due_date" id="grace_due_date" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select name="status" class="populate" id="status" tabindex="15" required="required" style="width: 100%">
                                                        <option disabled selected>Select Status</option>
                                                        <?php foreach($prem_status as $row):?>
                                                            <option value='<?php echo $row->status_id; ?>'><?php echo $row->status; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Remark</label>
                                                <div class="col-sm-8">
                                                    <textarea id="remarks" name="remarks" tabindex="16" class="form-control" style="height: 109px"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Imported Fund Value</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" readonly name="importFund" id="importFund">
                                                </div>
                                            </div>
                                            <?php /*<div class="form-group">
                                                <label class="col-sm-4 control-label">Imported Real Stake Value</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" readonly name="importStake" id="importStake">
                                                </div>
                                            </div> */ ?>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">System Fund Value</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="fund_value" readonly value="0" id="fund_value">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Premium Paid </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="prem_paid_till_date" readonly id="prem_paid_till_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Nominee</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="nominee" tabindex="17" id="nominee" required="required">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Advisor</label>
                                                <div class="col-sm-8 add-new-btn">
                                                    <select class="populate" name="adv_id" id="adv_id" required="required" tabindex="18" style="width: 100%">
                                                        <option>Select Advisor</option>
                                                        <?php foreach($adv as $row):?>
                                                            <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="19" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">First Maturity Amt</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" readonly name="firstMatAmt" id="firstMatAmt">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="col-sm-2 control-label">Adjustment</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                                            <span class="input-group-addon">
                                                                <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="20">
                                                            </span>
                                                        <input type="hidden" value="0" name="adjustment_flag" id="adjustment_flag">
                                                        <textarea placeholder="Adjustment" class="form-control" tabindex="21" id="adjustment" name="adjustment" readonly></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-midnightblue">
                                        <form action="#" class="form-horizontal row-border" id="add_form" data-validate="parsley">
                                            <div class="panel-heading">
                                                <h4>Maturity Details</h4>
                                                <div class="options">
                                                    <a href="javascript:;" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                                                </div>
                                            </div>
                                            <div class="panel-body collapse in">
                                                <div class="col-sm-4">
                                                    <fieldset>
                                                        <legend>Maturity Type</legend>
                                                        <div class="radio">
                                                            <label style="margin-right: 50px">
                                                                <input type="radio" name="mat_type" id="mat_type1" tabindex="22" value="Single" checked style="padding-right: 20px">
                                                                Single
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="mat_type" id="mat_type2" tabindex="23" value="Regular">
                                                                Regular
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-6 control-label">Maturity Date</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control date" name="matDate" tabindex="24" value="<?php echo date('d/m/Y');?>" required="required" data-inputmask="'alias':'date'" id="matDate">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-6 control-label">Amount</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control" name="matAmt" tabindex="25" id="matAmt" value="0" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-6 control-label">No. of Years</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control" name="matYear" tabindex="26" id="matYear" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-6">
                                                                <button type="button" id="matBtn" onclick="add()" tabindex="27" class="btn btn-primary">Add Maturity</button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <div>
                                                            <table border="0" class="table table-striped table-bordered" id="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sr.No.</th>
                                                                        <th>Policy Number</th>
                                                                        <th>Maturity Date</th>
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
                            </div>
                            <div class="bottom-row navbar-fixed-bottom">
                                <div class="col-sm-12 bottom-col">
                                    <button type="button" tabindex="28" id="add" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                                    <button type="button" tabindex="29" id="edit" onclick="editForm('<?php echo base_url("broker/insurances/edit_form");?>', $('#policy_num option:selected').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                                    <button type="button" tabindex="30" id="delete" onclick="del_ins()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                                    <button type="button" tabindex="31" id="save" onclick="ins_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                                    <button type="button" tabindex="32" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="premium">Lorem ...</div>
                        <div class="tab-pane" id="fund">Lorem ...</div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap modal -->
            <div class="modal fade" id="modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="edit_form" class="form-horizontal" data-validate="parsley">
                                <input type="hidden" value="" name="id"/>
                                <div class="form-body">
                                    <div class="form-group">
                                        <input name="srNoEdit" id="srNoEdit" type="hidden">
                                        <label class="control-label col-md-3">Policy Number</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="matPolicyEdit" id="matPolicyEdit" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Maturity Date</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control date" name="matDateEdit" data-inputmask="'alias':'date'" required="required" id="matDateEdit">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Amount</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="matAmtEdit" id="matAmtEdit" required="required">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="update()" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade" id="policy_modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="add_policy_form" class="form-horizontal" data-validate="parsley">
                                <input type="hidden" value="" name="id"/>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Company Name</label>
                                        <div class="col-md-8">
                                            <select class="populate" name="comp_name" id="comp_name" style="width:100%" required="required">
                                                <option disabled selected value="">Select Insurance Company</option>
                                                <?php foreach($ins_comp as $row):?>
                                                    <option value='<?php echo $row->ins_comp_id; ?>'><?php echo $row->ins_comp_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Policy Name</label>
                                        <div class="col-md-8">
                                            <select class="populate" name="policy_name" id="policy_name" style="width: 100%" required="required">
                                                <option disabled selected value="">Select Policy Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Policy Number</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="pol_policy_num" id="pol_policy_num" required="required">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="save_policy()" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- End Bootstrap modal -->
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">
var save_method; //for save method string
var table;
var matAdded = false;
$(function() {
    //initialize tooltip
    $('[data-toggle="tooltip"]').tooltip();
    //initialize select2
    $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
    $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
    $("#adj_flag").change(function(){
        if(this.checked)
        {
            $("#adjustment_flag").val("1");
            $("#adjustment").attr('readonly',false);
        }
        else
        {
            $("#adjustment_flag").val("0");
            $("#adjustment").attr('readonly',true);
        }
    });

    //on family change get clients
    $('#family_id').change(function()
    {
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, this.value, 'client_id', 'nominee', "", "");
    });

    //on client change get policy details and remove previous details
    $('#client_id').change(function()
    {
        $.ajax({
            url: "<?php echo site_url('broker/Insurances/get_policies');?>",
            type:'post',
            data: {client_id: this.value},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Policy Number</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].policy_num+">"+data[i].policy_num+"</option>";
                });
                $("#client_name").val($("#client_id option:selected").text());
                $("#policy_num").html(option);
                $("#compName, #ins_comp_id, #planName, #plan_id, #planCategory, #plan_type_id").val('');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting policy details from client',
                    type: 'error',
                    hide: true
                });
            }
        });

        //get family of client
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

    //get policy details and sets to respective controls
    $('#policy_num').change(function(){
        getPolicyDetails($('#policy_num option:selected').text());
    });

    $("#comp_name").change(function(){
        $.ajax({
            url: "<?php echo site_url('broker/Insurances/get_policy_name');?>",
            type:'post',
            data: {comp_id: this.value},
            dataType: 'json',
            success:function(data)
            {
                $("#policy_name").select2('val', '');
                var option = '<option disabled selected value="">Select Policy Name</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].plan_id+">"+data[i].plan_name+"</option>";
                });
                $("#policy_name").html(option);
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from family',
                    type: 'error',
                    hide: true
                });
            }
        });
    });

    //get paidup_date from ppt years
    $("#commence_date, #ppt, #bt").change( function () {
        if(this.id == "ppt") {
            /*var paidupDate = addYears($("#commence_date").val(), $("#ppt").val(), "ppt");
            if(paidupDate != false) {
                $("#paidup_date").val(paidupDate);
                //console.log($("#paidup_date").val());
            } else {
                console.log('Paidup Date not calculated');
            }*/
            var commense_date = $('[name="commence_date"]').val();
            var mode = $('[name="mode"] option:selected').val();
            if(+mode==5)
            {
                $('[name="paidup_date"]').val(commense_date);
            }
            else if(paidupDate != false) {
                var paidupDate = addYears($("#commence_date").val(), $("#ppt").val(), "ppt");
                $("#paidup_date").val(paidupDate);
                //console.log($("#paidup_date").val());
            } else {
                console.log('Paidup Date not calculated');
            }
        } else if(this.id == "bt") {
            var matDate = addYears($("#commence_date").val(), $("#bt").val(), "bt");
            if(matDate != false) {
                $("#maturity_date").val(matDate);
                //console.log($("#maturity_date").val());
            } else {
                console.log('Maturity Date not calculated');
            }
        }
    });


    //regular mat date should be less than commence date
    $("#commence_date").change(function() {
        $("#matDate").datepicker("remove");
        $("#matDate").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
    });

    //on sum assured change calculate and set first maturity amount
    $('#amt_insured, #mode, #prem_amt, #commence_date, #ppt, #bt, #planName').change(function()
    {
        var amt_ins = $('[name="amt_insured"]').val();
        var mode = $('[name="mode"] option:selected').text();
        var amt_prem = $('[name="prem_amt"]').val();
        var commense_date = $('[name="commence_date"]').val();
        var paidup_date = $('[name="paidup_date"]').val();
        var plan_name = $('[name="planName"]').val();
        var maturity_date = $('[name="maturity_date"]').val();
        if(amt_ins != "" && mode != "" && amt_prem != "" && commense_date != "" && paidup_date != "" && plan_name != "" && maturity_date != "")
        {
            console.log("inside calc");
            $.ajax({
                url: "<?php echo site_url('broker/Insurances/get_first_mat_amt');?>",
                type:'post',
                data: {amt_ins: amt_ins, mode: mode, amt_prem:amt_prem,
                    commense_date:commense_date, paidup_date:paidup_date, plan_name: plan_name, maturity_date: maturity_date},
                dataType: 'json',
                success:function(data)
                {
                    if($('[name="planCategory"]').val().toUpperCase() != "GENERAL INSURANCE")
                    {
                        $('[name="firstMatAmt"]').val(data['firstMatAmt']);
                        if(!matAdded) {
                            addULMaturity(data['firstMatAmt'], $('#policy_num option:selected').text(), maturity_date);
                        }

                        $("#matDate").datepicker({ dateFormat: 'dd-mm-yy'}).datepicker("setDate", maturity_date);
                    }
                    else
                    {
                        $('[name="firstMatAmt"]').val(0);
                    }
                    $('[name="next_prem_due_date"]').val(data['nextPremDueDate']);
                    $('[name="grace_due_date"]').val(data['graceDueDate']);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting details from family',
                        type: 'error',
                        hide: true
                    });
                }
            });
        }
    });

    $("#status").change(function() {
        var status = $("#status option:selected").text();
        if(status == "Matured" || status == "Surrender" || status == "Paid Up Cancellation") {
            $.pnotify({
                title: "Check Maturity Amount and Maturity Date!",
                text: "Please make sure to change Maturity Amount and Maturity Date to today's date before saving",
                type: 'info',
                hide: true
            });
        }
    });

    $("#status").select2('val', '1');
    //on load disable controls
    disableBtn();
});

function getPolicyDetails(polNumber)
{
    $.ajax({
        url: "<?php echo site_url('broker/Insurances/policy_change');?>",
        type:'post',
        data:{client_id: $('[name="client_id"]').val(), pol_num: polNumber},
        dataType: 'json',
        success:function(data)
        {
            $("#compName").val(data['pol_details'][0]['ins_comp_name']);
            $("#ins_comp_id").val(data['pol_details'][0]['ins_comp_id']);
            $("#planName").val(data['pol_details'][0]['plan_name']);
            $("#plan_id").val(data['pol_details'][0]['plan_id']);
            $("#planCategory").val(data['pol_details'][0]['plan_type_name']);
            $("#plan_type_id").val(data['pol_details'][0]['plan_type_id']);
            if((data['pol_details'][0]['plan_type_name']).toUpperCase() == 'GENERAL INSURANCE')
            {
                $('#matBtn').attr('disabled', true);
                $('[name="firstMatAmt"]').val(0);
                $('[name="fund_value"]').val(0);
            }
            else
                $('#matBtn').attr('disabled', false);
            if(table != null)
            {
                table.destroy();
                table = mat_list();
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details of policy number',
                type: 'error',
                hide: true
            });
        }
    });
}

function addULMaturity(matAmt, polNum, matDate)
{
    var valid = true;
    var comDate = process($('#commence_date').val());
    var matDateTemp = process(matDate);
    //console.log(comDate,matDateTemp, matDate);
    if(comDate > matDateTemp)
    {
        $.pnotify({
            title: 'Error!',
            text: 'Maturity Date cannot be less than Commence Date',
            type: 'error',
            hide: true
        });
        l.stop();
        valid = false;
    }

    if(valid) {
        $.ajax({
            url: "<?php echo site_url('broker/Insurances/add_mat');?>",
            type:'post',
            data: {policy_num: polNum, matDate: matDate, matYear: 1, matAmt: matAmt, isUL: true, isEdit: false},
            dataType: 'json',
            success:function(data)
            {
                matAdded = true;
                if(table != null)
                    table.destroy();
                table = mat_list();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }
}

//insert insurance details in database
function ins_submit()
{
    var valid = true, dateValid = true;
    var paidDate = process($('#paidup_date').val());
    var matDate = process($('#maturity_date').val());
    if(paidDate > matDate)
    {
        $.pnotify({
            title: 'Error!',
            text: 'Paid-Up Date cannot be greater then Maturity Date',
            type: 'error',
            hide: true
        });
        dateValid = false;
    }
    valid = $('#client_form').parsley('validate');
    valid = $('#ins_form').parsley('validate');
    if(valid && dateValid)
    {
        $.ajax({
            url: '<?php echo site_url('broker/Insurances/add_ins');?>',
            type: 'post',
            data: $('#client_form, #ins_form').serialize(),
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
            error:function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }
}

function del_ins()
{
    var pol_num = $("#policy_num").val();
    if(pol_num != null)
    {
        bootbox.confirm("Are you sure you want to delete this Insurance?", function(result) {
            $.ajax({
                url: '<?php echo site_url('broker/Insurances/del_ins');?>',
                type: 'post',
                data: {policy_num: pol_num, client_id: $("#client_id").val()},
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'success')
                    {
                        bootbox.alert("Insurance Details");
                        location.href = '<?php echo base_url();?>broker/Insurances';
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
        });
    }
    else
        bootbox.alert("Please add Insurance details first");
}

//popup to add policy details
function add_ins_policy()
{
    //reset form on modals
    $("#add_policy_form")[0].reset();
    $("#comp_name").select2("val","");
    $("#policy_name").select2("val","");
    //reset parsley errors
    $("#add_policy_form").parsley().reset();
    //show bootstrap modal
    $("#policy_modal_form").modal('show');
    //set title to modal
    $(".modal-title").text('Add Insurance Policy');
}

//save policy details in database and resets policy dropdownlist
function save_policy()
{
    if($('#add_policy_form').parsley('validate'))
    {
        client = $('[name="client_id"]').val();
        if(client != null && client != '')
        {
            var policyNumber = $('[name="pol_policy_num"]').val();
            $.ajax({
                url: "<?php echo site_url('broker/Insurances/save_policy');?>",
                type:'post',
                data: {client_id: $('[name="client_id"]').val(), comp_name: $('[name="comp_name"]').val(),
                    policy_name: $('[name="policy_name"]').val(), policy_num: policyNumber},
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] === 0) {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'error',
                            hide: true
                        });
                    } else {
                        var option = '<option disabled selected>Select Policy Number</option>';
                        $.each(data, function(i, item){
                            option = option + "<option value="+data[i].policy_num+">"+data[i].policy_num+"</option>";
                        });
                        $("#policy_num").html(option);
                        $("#policy_num").val(policyNumber).focus();
                        getPolicyDetails(policyNumber);
                    }
                    $("#policy_modal_form").modal('hide');
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
        else
        {
            $.pnotify({
                title: 'Error!',
                text: 'Select Client to add policy number',
                type: 'error',
                hide: true
            });
        }
    }
}

//get maturity details in datatable
function mat_list() {
    var oTable = $("#table").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "aaSorting": [[2,'asc']],
        "bAutoWidth": false,
        "scrollY": "200px",
        "scrollCollapse": true,
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
            { "bSearchable": false, "aTargets": [ 0 ] },
            { "bVisible": false, "aTargets": [ 0 ] }],
        "bPaginate": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('broker/Insurances/mat_list');?>',
            "type": 'post',
            "data": {isEdit: false},
            "dataSrc": function (json) {
                if(!json.data){
                    $('#table').html('No Records Available');
                    json.data = [];
                }
                return json.data;
            }
        },
        "columns": [
            { "data": "srNo" },
            { "data": "matPolicy" },
            { "data": "matDate", "type": "date-uk" },
            { "data": "matAmt" },
            { "data": "action" }
        ],
        "oLanguage": {
            "sEmptyTable": "No Records Available"
        }
    });
    return oTable;
}

//add maturity details in datatable
function add()
{
    if($('#add_form').parsley('validate'))
    {
        $('[name="matDate"], [name="matYear"], [name="matAmt"]').removeClass('has-error');
        policyVal = $('#policy_num').val();
        if(policyVal != null && policyVal != "")
        {
            var valid = true;
            var comDate = process($('#commence_date').val());
            var matDateTemp = process($("#matDate").val());
            if(comDate > matDateTemp)
            {
                $.pnotify({
                    title: 'Error!',
                    text: 'Maturity Date cannot be less than Commencement Date',
                    type: 'error',
                    hide: true
                });
                l.stop();
                valid = false;
            }

            if(valid) {
                $.ajax({
                    url: "<?php echo site_url('broker/Insurances/add_mat');?>",
                    type:'post',
                    data: {policy_num: $('#policy_num option:selected').text(), matDate: $('[name="matDate"]').val(),
                        matYear: $('[name="matYear"]').val(), matAmt: $('[name="matAmt"]').val(), isUL: false, isEdit: false},
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
                            //disable Single Maturity
                            $("#mat_type2").attr("checked","checked");
                            $("#mat_type1").prop("disabled",true);
                            $("#mat_type1").addClass("disabled");
                            if(table != null)
                                table.destroy();
                            table = mat_list();
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
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        }
        else
        {
            $.pnotify({
                title: 'Error!',
                text: 'Please Select Policy Number',
                type: 'error',
                hide: true
            });
        }
    }
}

//delete maturity details in datatable
function delete_mat(id)
{
    bootbox.confirm('Are you sure you want to delete this maturity details?', function(result){
        if(result)
        {
            // ajax delete data to database
            $.ajax({
                url : "<?php echo site_url('broker/Insurances/delete_mat');?>",
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
                        table.destroy();
                        table = mat_list();
                        if(table.data().count() <= 1) {
                            //enable Single Maturity
                            $("#mat_type1").prop("disabled",false);
                            $("#mat_type1").removeClass("disabled");
                        }
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
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
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