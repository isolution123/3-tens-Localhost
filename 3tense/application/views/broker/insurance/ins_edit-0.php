<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard'); ?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Insurances');?>">Insurance</a></li>
                <li class="active">Edit Insurance</li>
            </ol>
            <h1>Edit Insurance</h1>

        </div>
        <div class="container">
            <form action="#" id="client_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" name="hFamilyID" id="hFamilyID" value="<?= $insurance[0]->family_id; ?>" />
                                <input type="hidden" name="family_id" id="hFamilyID1" value="<?= $insurance[0]->family_id; ?>" />
                                <input type="hidden" name="hClientID" id="hClientID" value="<?= $insurance[0]->client_id; ?>" />
                                <input type="hidden" name="client_id" id="hClientID1" value="<?= $insurance[0]->client_id; ?>" />
                                <input type="hidden" name="hPolicyNum" id="hPolicyNum" value="<?= $insurance[0]->policy_num; ?>" />
                                <input type="hidden" name="hStatus" id="hStatus" value="<?= $insurance[0]->status_id; ?>" />
                                <input type="hidden" name="hMode" id="hMode" value="<?= $insurance[0]->mode; ?>" />
                                <input type="hidden" name="hPremType" id="hPremType" value="<?= $insurance[0]->prem_type_id; ?>" />
                                <input type="hidden" name="hPremPayMode" id="hPremPayMode" value="<?= $insurance[0]->prem_pay_mode_id; ?>" />
                                <input type="hidden" name="hNominee" id="hNominee" value="<?= $insurance[0]->nominee_id; ?>" />
                                <input type="hidden" name="hAdviser" id="hAdviser" value="<?= $insurance[0]->adv_id; ?>" />
                                <input type="hidden" name="hPaidupDate" id="hPaidupDate" value="<?= $insurance[0]->paidup_date; ?>" />
                                <input type="hidden" name="hMatDate" id="hMatDate" value="<?= $insurance[0]->maturity_date; ?>" />
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4  no-border add-new-btn">
                                    <select name="family_id0" class="populate disabled" required="required" id="family_id" style="width: 80%" tabindex="1" disabled>
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt disabled" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Policy Number</label>
                                <div class="col-md-4">
                                    <input type="text" name="policy_num" class="form-control" readonly
                                           id="policy_num" style="width: 48%; margin-right: 5px" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4">
                                    <select name="client_id0" class="populate disabled" required="required" id="client_id" style="width: 100%" tabindex="3" disabled>
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
                            <a href="#insurance" tabindex="4" data-toggle="tab">Insurance</a>
                        </li>
                        <li>
                            <?php /*<a href="#premium" tabindex="5" data-toggle="tab">Premium</a>*/ ?>
                            <a href="javascript:void(0);" id="premium_tab" tabindex="5" data-toggle="tab">Premium</a>
                        </li>
                        <li>
                            <?php /*<a href="#fund" tabindex="6" data-toggle="tab">Fund Options</a>*/ ?>
                            <a href="javascript:void(0);" id="fund_tab" tabindex="6" data-toggle="tab">Fund Options</a>
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
                                                    <input type="hidden" name="ins_comp_id" class="form-control" id="ins_comp_id" value="<?php echo isset($insurance[0]->ins_comp_id)?$insurance[0]->ins_comp_id:'';?>">
                                                    <input type="text" name="compName" class="form-control" id="compName" readonly value="<?php echo isset($insurance[0]->ins_comp_name)?$insurance[0]->ins_comp_name:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Plan Name</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="plan_id" class="form-control" id="plan_id" value="<?php echo isset($insurance[0]->plan_id)?$insurance[0]->plan_id:'';?>">
                                                    <input type="text" name="planName" class="form-control" id="planName" readonly value="<?php echo isset($insurance[0]->plan_name)?$insurance[0]->plan_name:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Plan Category</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="plan_type_id" class="form-control" id="plan_type_id" value="<?php echo isset($insurance[0]->plan_type_id)?$insurance[0]->plan_type_id:'';?>">
                                                    <input type="text" name="planCategory" class="form-control" id="planCategory" readonly value="<?php echo isset($insurance[0]->plan_type_name)?$insurance[0]->plan_type_name:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Commence Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="commence_date" required="required" tabindex="7" class="form-control date" data-inputmask="'alias':'date'" id="commence_date" value="<?php echo isset($insurance[0]->commence_date)?$insurance[0]->commence_date:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Premium Paying Term</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="paidup_date" required="required" tabindex="8" class="form-control date" data-inputmask="'alias':'date'" id="paidup_date" value="<?php echo isset($insurance[0]->paidup_date)?$insurance[0]->paidup_date:'';?>">
                                                    <div class="col-sm-8">
                                                        <input type="number" name="ppt" tabindex="7" required="required" class="form-control" id="ppt" min="0">
                                                    </div>
                                                    <label class="control-label">years</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Benefit Term</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" name="maturity_date" required="required" tabindex="9" readonly class="form-control date" data-inputmask="'alias':'date'" id="maturity_date" value="<?php echo isset($insurance[0]->maturity_date)?$insurance[0]->maturity_date:'';?>">
                                                    <div class="col-sm-8">
                                                        <input type="number" name="bt" tabindex="7" required="required" class="form-control" id="bt" min="0">
                                                    </div>
                                                    <label class="control-label">years</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Sum Assured</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="amt_insured" required="required" class="form-control" tabindex="10" id="amt_insured" value="<?php echo isset($insurance[0]->amt_insured)?round($insurance[0]->amt_insured):'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Mode</label>
                                                <div class="col-sm-8">
                                                    <select name="mode" class="populate" required="required" id="mode" style="width: 100%" tabindex="11" >
                                                        <option disabled selected>Select Premium Mode</option>
                                                        <?php foreach($prem_mode as $row):?>
                                                            <option value='<?php echo $row->mode_id; ?>'><?php echo $row->mode_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Installment Premium Amt</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" required="required" name="prem_amt" tabindex="12" id="prem_amt" value="<?php echo isset($insurance[0]->prem_amt)?round($insurance[0]->prem_amt):'';?>">
                                                    <input type="hidden" id="prem_amt_old" value="<?php echo isset($insurance[0]->prem_amt)?$insurance[0]->prem_amt:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Asset Allocation</label>
                                                <div class="col-sm-8 add-new-btn">
                                                    <select name="prem_type" class="populate" required="required" id="prem_type" tabindex="13" style="width: 80%">
                                                        <option disabled selected>Select Asset/Premium</option>
                                                        <?php foreach($prem_types as $row):?>
                                                            <option value='<?php echo $row->prem_type_id; ?>'><?php echo $row->prem_type_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:;" tabindex="15" class="btn btn-xs btn-inverse-alt" onclick="add_premium_type(true)"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Payment Option</label>
                                                <div class="col-sm-8">
                                                    <select name="prem_pay_mode_id" class="populate" required="required" tabindex="14" id="prem_pay_mode_id" style="width: 100%">
                                                        <option disabled selected>Select Payment Option</option>
                                                        <?php foreach($prem_pay_mode as $row):?>
                                                            <option value='<?php echo $row->prem_pay_mode_id; ?>'><?php echo $row->prem_pay_mode; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Premium Paid </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="prem_paid_till_date" readonly id="prem_paid_till_date" value="<?php echo isset($insurance[0]->prem_paid_till_date)?round($insurance[0]->prem_paid_till_date):'';?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select name="status" class="populate" id="status" required="required" tabindex="15" style="width: 100%">
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
                                                    <textarea id="remarks" name="remarks" tabindex="16" class="form-control" style="height: 109px"><?php echo isset($insurance[0]->remarks)?$insurance[0]->remarks:'';?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Imported Fund Value</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="importFund" id="importFund" readonly value="<?php echo isset($insurance[0]->value)?round($insurance[0]->value):'';?>">
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
                                                    <input type="text" class="form-control" name="fund_value" readonly id="fund_value" value="<?php echo isset($insurance[0]->fund_value)?round($insurance[0]->fund_value):'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Nominee</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="nominee" id="nominee" tabindex="17" required="required">
                                                        <option disabled selected>Select Nominee</option>
                                                        <?php foreach($clients as $row):?>
                                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Advisor</label>
                                                <div class="col-sm-8 add-new-btn">
                                                    <select class="populate" name="adv_id" id="adv_id" required="required" tabindex="18" style="width: 80%">
                                                        <option disabled selected>Select Advisor</option>
                                                        <?php foreach($adv as $row):?>
                                                            <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:;" tabindex="19" class="btn btn-xs btn-inverse-alt" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">First Unpaid Premium Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="next_prem_due_date" id="next_prem_due_date" readonly value="<?php echo isset($insurance[0]->next_prem_due_date)?$insurance[0]->next_prem_due_date:'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">First Maturity Amt</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" readonly name="firstMatAmt" id="firstMatAmt" value="<?php echo isset($insurance[0]->firstMatAmt)?round($insurance[0]->firstMatAmt):'';?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Grace Due Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="grace_due_date" id="grace_due_date" readonly value="<?php echo isset($insurance[0]->grace_due_date)?$insurance[0]->grace_due_date:'';?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="col-sm-2 control-label">Adjustment</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                                        <?php if($insurance[0]->adjustment_flag == 1) :?>
                                                            <span class="input-group-addon">
                                                                <input type="checkbox" id="adj_flag" name="adj_flag" checked tabindex="20">
                                                                <input type="hidden" value="1" name="adjustment_flag" id="adjustment_flag">
                                                            </span>
                                                            <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="21" name="adjustment"><?php echo isset($insurance[0]->adjustment)?$insurance[0]->adjustment:'';?></textarea>
                                                        <?php else :?>
                                                            <span class="input-group-addon">
                                                                <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="20">
                                                                <input type="hidden" value="0" name="adjustment_flag" id="adjustment_flag">
                                                            </span>
                                                            <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="21" name="adjustment" readonly><?php echo isset($insurance[0]->adjustment)?$insurance[0]->adjustment:'';?></textarea>
                                                        <?php endif; ?>
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
                                                            <?php if($insurance[0]->mat_type == 'Single') :?>
                                                            <label style="margin-right: 50px">
                                                                <input type="radio" name="mat_type" id="mat_type1" value="Single" tabindex="22" checked style="padding-right: 20px">
                                                                Single
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="mat_type" id="mat_type2" tabindex="23" value="Regular">
                                                                Regular
                                                            </label>
                                                            <?php else:?>
                                                            <label style="margin-right: 50px">
                                                                <input type="radio" name="mat_type" id="mat_type1" tabindex="22" value="Single" class="disabled" disabled="disabled" style="padding-right: 20px">
                                                                Single
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="mat_type" id="mat_type2" tabindex="23" value="Regular" checked>
                                                                Regular
                                                            </label>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-6 control-label">Maturity Date</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control date" name="matDate" tabindex="24" required="required" value="<?php echo date('d/m/Y');?>" data-inputmask="'alias':'date'" id="matDate">
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
                                                                    <th>Maturity ID</th>
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
                        </div>
                        <div class="tab-pane" id="premium">
                            <?php include 'premium_form.php'; ?>
                        </div>
                        <div class="tab-pane" id="fund">
                            <?php include 'fund_option.php'; ?>
                        </div>
                    </div>
                    <div class="bottom-row navbar-fixed-bottom">
                      <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="28" onclick="addForm('<?php echo base_url("broker/insurances/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="29" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="del_ins()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="ins_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="32" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>

<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="28" onclick="addForm('<?php echo base_url("broker/insurances/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="29" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="30" onclick="del_ins()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="31" onclick="ins_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
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
            <!-- Bootstrap modal -->
            <!--<div class="modal fade" id="modal_form" role="dialog">
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
                    </div>
                </div>
            </div>-->
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="application/javascript">
var table; var matInfo;
    $(function() {
        //initialize tooltip
        $('[data-toggle="tooltip"]').tooltip();
        //initialize select2
        $('#family_id').val($('#hFamilyID').val());
        $('#policy_num').val($('#hPolicyNum').val());
        $('#mode').val($('#hMode').val());
        $('#prem_type').val($('#hPremType').val());
        //console.log($("#hPremType").val());
        $('#prem_pay_mode_id').val($('#hPremPayMode').val());
        $('#status').val($('#hStatus').val());
        $('#adv_id').val($('#hAdviser').val());
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $("#matDate").datepicker({format: 'dd/mm/yyyy', startDate:$("#commence_date").val()});
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
        if($('[name="planCategory"]').val().toUpperCase() != "GENERAL INSURANCE")
        {
            getMatFirstAmt();
        }
        else
        {
            $('[name="firstMatAmt"]').val(0);
        }

        //get ppt from commence_date and paidup_date
        var diff = getYearDiff($("#commence_date").val(),$("#paidup_date").val(),"ppt");
        if($("#mode option:selected").text() == "Monthly") { diff--; }
        //else { diff--; }
        if(diff != false) {
            $("#ppt").val(diff);
        } else {
            $("#ppt").val('0');
            console.log("could not get ppt years diff");
        }
        //get benefit term from commence_date and maturity_date
        var mdiff = getYearDiff($("#commence_date").val(),$("#maturity_date").val(),"bt");
        if(mdiff != false) {
            $("#bt").val(mdiff);
        } else {
            $("#bt").val('0');
            console.log("could not get bt years diff");
        }

        getImportValues();
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
        table = mat_list();
        $("#cancel").attr("disabled", true);
        $( document ).ajaxComplete(function( event,request, settings ) {
            $("#cancel").attr("disabled", false);
            $("#premium_tab").attr("href","#premium");
            $("#fund_tab").attr("href","#fund");
        });
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, $('#hFamilyID').val(), 'client_id', 'nominee', $('#hClientID').val(), $('#hNominee').val());

        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', 'nominee', "", "");
        });

        //regular mat date should be less than commence date
        $("#commence_date").change(function() {
            $("#matDate").datepicker("remove");
            $("#matDate").datepicker({format: 'dd/mm/yyyy', startDate:this.value});
        });

        //get paidup_date from ppt years
        $("#commence_date, #ppt, #bt").change( function () {
            if(this.id == "ppt") {
                var paidupDate = addYears($("#commence_date").val(), $("#ppt").val(), "ppt");
                if(paidupDate != false) {
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


        //get new First unpaid date and Grace due date on Mode change
        $("#mode").change(function() {
            if($("#hMode").val() != $(this).val()) {
                $.pnotify({
                    title: 'Check Policy Year completion!',
                    text: 'Please see that you are changing Mode on POLICY ANNIVERSARY only',
                    type: 'info',
                    hide: true
                });
            }

            $.ajax({
                url: "<?php echo site_url('broker/Insurances/get_last_premium');?>",
                type:'post',
                data: {policy_num: $("#policy_num").val(), mode: this.value,
                    next_prem_due_date: $("#next_prem_due_date").val(), grace_due_date:$("#grace_due_date").val()},
                dataType: 'json',
                success:function(data)
                {
                    $('[name="next_prem_due_date"]').val(data['next_premium_due_date']);
                    $('[name="grace_due_date"]').val(data['grace_due_date']);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting last premium details',
                        type: 'error',
                        hide: true
                    });
                }
            });
        })

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

        //on sum assured change calculate and set first maturity amount
        $('#amt_insured, #mode, #prem_amt, #commence_date, #ppt, #bt, #planName').change(function()
        {
            //console.log("calculate maturity");
            var amt_ins = $('[name="amt_insured"]').val();
            var mode = $('[name="mode"] option:selected').text();
            var amt_prem = $('[name="prem_amt"]').val();
            var commense_date = $('[name="commence_date"]').val();
            var paidup_date = $('[name="paidup_date"]').val();
            var plan_name = $('[name="planName"]').val();
            var maturity_date = $('[name="maturity_date"]').val();
            if(amt_ins != "" && mode != "" && amt_prem != "" && commense_date != "" && paidup_date != "" && plan_name != "" && maturity_date != "")
            {
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
                        }
                        else
                        {
                            $('[name="firstMatAmt"]').val(0);
                        }
                        //$('[name="next_prem_due_date"]').val(data['nextPremDueDate']);
                        //$('[name="grace_due_date"]').val(data['graceDueDate']);
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


        $('[name="mat_type"]').on("click", function() {
            $('[name="mat_type"]').removeAttr("checked");
            $(this).prop("checked",true);
        });
    });

    //insert insurance details in database
    function ins_submit()
    {
        var button = $('#save');
        var l = Ladda.create(button[0]);
        l.start();
        var valid = true;
        valid = $('#ins_form').parsley('validate');
        valid = $('#client_form').parsley('validate');
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
            l.stop();
            valid = false;
        }

        if(valid)
        {
            //check if premium amount or premium mode has changed
            //if yes, we need to recreate premium_paying_details entries
            if($("#prem_amt").val() != $("#prem_amt_old").val() || $("#mode").val() != $("#hMode").val()) {
                $.ajax({
                    url: '<?php echo site_url('broker/Insurances/update_prem_paying_details');?>',
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
                            $("#prem_amt_old").val($("#prem_amt").val());
                            $("#hMode").val($("#mode").val());
                            table.destroy();
                            table = mat_list($("#policy_num").val());
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
                        l.stop();
                    },
                    error:function(jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }

            $.ajax({
                url: '<?php echo site_url('broker/Insurances/edit_ins');?>',
                type: 'post',
                data: $('#client_form, #ins_form, #add_form').serialize(),
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
                    l.stop();
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

function addULMaturity(matAmt, polNum, matDate)
{
    var valid = true;
    var comDate = process($('#commence_date').val());
    var matDateTemp = process(matDate);
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
            data: {policy_num: polNum, matDate: matDate, matYear: 1, matAmt: matAmt, isUL: true, isEdit: false},
            dataType: 'json',
            success:function(data)
            {
                if(table != null)
                    table.destroy();
                table = mat_list();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                bootbox.alert('Error adding / update data');
            }
        });
    }
}

    function getImportValues()
    {
        var policyVal = $('#policy_num').val();
        $.ajax({
            url: "<?php echo site_url('broker/Insurances/get_import_values');?>",
            type:'post',
            data: {policy_num: policyVal},
            dataType: 'json',
            success:function(data)
            {
                $('[name="importFund"]').val(data['fund'][0]['fundValue']);
                $('[name="importStake"]').val(data['stake'][0]['stakeValue']);
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

    function getMatFirstAmt()
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
            $.ajax({
                url: "<?php echo site_url('broker/Insurances/get_first_mat_amt');?>",
                type:'post',
                data: {amt_ins: amt_ins, mode: mode, amt_prem:amt_prem,
                    commense_date:commense_date, paidup_date:paidup_date, plan_name: plan_name, maturity_date: maturity_date},
                dataType: 'json',
                success:function(data)
                {
                    $('[name="firstMatAmt"]').val(data['firstMatAmt']);
                    if(data['isUL'])
                    {
                        $("#matDate").val(maturity_date);
                    }
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
    }

    //get maturity details in datatable
    function mat_list(policyNum = "") {
        //var policyNum = $('#policy_num').val();
        if(policyNum == "") {
            var oTable = $("#table").DataTable({
                "processing":true,    //Control the processing indicator
                "serverSide":false,    //Control DataTable server process
                "aaSorting": [[2,'asc']],
                "bAutoWidth": false,
                "scrollY": "200px",
                "scrollCollapse": true,
                "bFilter": false,
                "pageLength": 100,
                "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1 ] },
                    { "bSearchable": false, "aTargets": [ 0, 1 ] },
                    { "bVisible": false, "aTargets": [ 0, 1 ] }],
                "ajax": {
                    //Load data for the table through ajax
                    "url": '<?php echo site_url('broker/Insurances/mat_list');?>',
                    "type": 'post',
                    "data": {isEdit: true},
                    "dataSrc": function (json) {
                        if(!json.data){
                            $('#table tbody tr td').html('No Records Available');
                            json.data = [];
                        }
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "srNo" },
                    { "data": "matID" },
                    { "data": "matPolicy" },
                    { "data": "matDate", "type": "date-uk" },
                    { "data": "matAmt" },
                    { "data": "action" }
                ],
                "oLanguage": {
                    "sEmptyTable": "No Records Available"
                },
                "initComplete": function(settings, json){
                    matInfo = this.api().page.info();
                    //console.log('Total records', info.recordsTotal);
                    //console.log('Displayed records', info.recordsDisplay);
                }
            });
        } else {
            var oTable = $("#table").DataTable({
                "processing":true,    //Control the processing indicator
                "serverSide":false,    //Control DataTable server process
                "aaSorting": [[2,'asc']],
                "bAutoWidth": false,
                "scrollY": "200px",
                "scrollCollapse": true,
                "bFilter": false,
                "pageLength": 100,
                "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1 ] },
                    { "bSearchable": false, "aTargets": [ 0, 1 ] },
                    { "bVisible": false, "aTargets": [ 0, 1 ] }],
                "ajax": {
                    //Load data for the table through ajax
                    "url": '<?php echo site_url('broker/Insurances/mat_list_db');?>',
                    "type": 'post',
                    "data": {polNum: policyNum},
                    "dataSrc": function (json) {
                        if(!json.data){
                            $('#table tbody tr td').html('No Records Available');
                            json.data = [];
                        }
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "srNo" },
                    { "data": "matID" },
                    { "data": "matPolicy" },
                    { "data": "matDate", "type": "date-uk" },
                    { "data": "matAmt" },
                    { "data": "action" }
                ],
                "oLanguage": {
                    "sEmptyTable": "No Records Available"
                },
                "initComplete": function(settings, json){
                    matInfo = this.api().page.info();
                    //console.log('Total records', info.recordsTotal);
                    //console.log('Displayed records', info.recordsDisplay);
                }
            });
        }

        /*if($('[name="planCategory"]').val().toUpperCase() == "GENERAL INSURANCE")
        {
            $("#matBtn").attr('disabled', true);
        }*/
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
                        data: {policy_num: policyVal, matDate: $('[name="matDate"]').val(),
                            matYear: $('[name="matYear"]').val(), matAmt: $('[name="matAmt"]').val(),
                            isUL: false, isEdit: true, client_id: $('#client_id').val(),
                            client_name: $('#client_id option:selected').text(),
                            comp_name:$("#compName").val(), plan_name: $("#planName").val()},
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

                                table.destroy();
                                table = mat_list();

                                //disable Single Maturity
                                console.log(matInfo.recordsTotal);
                                if(matInfo.recordsTotal >= 1) {
                                    $("#mat_type2").prop("checked",true);
                                    $("#mat_type1").prop("disabled",true);
                                    $("#mat_type1").addClass("disabled");
                                }
                            }
                            else
                            {
                                console.log(data);
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
                            bootbox.alert('Error adding / update data');
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
                            table.destroy();
                            table = mat_list();
                            //console.log(matInfo.recordsTotal);
                            if(matInfo.recordsTotal <= 2) {
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
                    }
                });
            }
        });
    }
    function del_ins()
    {
        var policyVal = $('#policy_num').val();
        var client_id = $('#hClientID').val();
        var comp_id = $('#ins_comp_id').val();
        if(policyVal != null)
        {
            bootbox.confirm("Are you sure you want to delete this Insurance?", function(result) {
                if (result) {
                    $.ajax({
                        url: '<?php echo site_url('broker/Insurances/del_ins');?>',
                        type: 'post',
                        data: {policy_num: policyVal, client_id: client_id, comp_id: comp_id},
                        dataType: 'json',
                        success:function(data)
                        {
                            if(data['status'] == 'success')
                            {
                                bootbox.alert("Insurance of Policy Number "+policyVal+" is deleted");
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
                        }
                    });
                }
            });
        }
        else
            bootbox.alert("Please add Insurance details first");
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
