  <div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard'); ?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/fixed_deposits');?>">Fixed Deposit</a></li>
                <li class="active">Edit Fixed Deposit</li>
            </ol>
            <h1>Edit Fixed Deposit</h1>
        </div>
        <div class="container">
            <form action="#" id="fd_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <input type="hidden" name="hTransID" id="hTransID" value="<?= $fd[0]->fd_transaction_id; ?>" />
                                <input type="hidden" name="hInvID" id="hInvID" value="<?= $fd[0]->fd_inv_id; ?>" />
                                <input type="hidden" name="hCompID" id="hCompID" value="<?= $fd[0]->fd_comp_id; ?>" />
                                <input type="hidden" name="hInvBankID" id="hInvBankID" value="<?= $fd[0]->inv_bank_id; ?>" />
                                <input type="hidden" name="hInvAcc" id="hInvAcc" value="<?= $fd[0]->inv_account_number; ?>" />
                                <input type="hidden" name="hMatBankID" id="hMatBankID" value="<?= $fd[0]->maturity_bank_id; ?>" />
                                <input type="hidden" name="hMatAcc" id="hMatAcc" value="<?= $fd[0]->maturity_account_number; ?>" />
                                <input type="hidden" name="hPayID" id="hPayID" value="<?= $fd[0]->maturity_payout_id; ?>" />
                                <input type="hidden" name="hNominee" id="hNominee" value="<?= $fd[0]->nominee; ?>" />
                                <input type="hidden" name="hAdviser" id="hAdviser" value="<?= $fd[0]->adv_id; ?>" />
                                <input type="hidden" name="hStatus" id="hStatus" value="<?= $fd[0]->status; ?>" />
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4  no-border">
                                    <input type="hidden" name="family_id" id="family_id" value="<?= $fd[0]->family_id; ?>" />
                                    <input type="text" name="family_name" class="form-control" id="family_name" readonly
                                           value="<?php echo isset($fd[0]->family_name)?$fd[0]->family_name:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Date of Transaction</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="1" class="form-control date" id="transaction_date" readonly
                                           data-inputmask="'alias':'date'" value="<?php echo isset($fd[0]->transaction_date)?$fd[0]->transaction_date:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4  no-border">
                                    <input type="hidden" name="client_id" id="client_id" value="<?= $fd[0]->client_id; ?>" />
                                    <input type="text" name="client_name" class="form-control" id="client_name" readonly
                                           value="<?php echo isset($fd[0]->client_name)?$fd[0]->client_name:'';?>">
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
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="hidden" id="fd_inv_name" name="fd_inv_name">
                                    <select name="fd_inv_id" class="populate" required="required" id="fd_inv_id" style="width: 100%" tabindex="2">
                                        <option disabled selected value="">Select Investment Type</option>
                                        <?php foreach($invTypes as $row):?>
                                            <option value='<?php echo $row->fd_inv_id; ?>'><?php echo $row->fd_inv_type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="3" onclick="add_fd_inv(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Company Name</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="fd_comp_id" class="populate" required="required" id="fd_comp_id" style="width: 100%" tabindex="4">
                                        <option disabled selected value="">Select Company Name</option>
                                        <?php foreach($companies as $row):?>
                                            <option value='<?php echo $row->fd_comp_id; ?>'><?php echo $row->fd_comp_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" class="btn btn-xs btn-inverse-alt" tabindex="5" onclick="add_fd_comp(true)"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Reference Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="ref_number" required="required" class="form-control" id="ref_number" tabindex="6"
                                           value="<?php echo isset($fd[0]->ref_number)?$fd[0]->ref_number:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Fixed Deposit Mode</label>
                                <div class="col-md-4">
                                    <input type="text" name="fd_method" class="form-control" id="fd_method" readonly
                                           value="<?php echo isset($fd[0]->fd_method)?$fd[0]->fd_method:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4  no-border">
                                    <label class="checkbox-inline">
                                        <?php if($fd[0]->int_round_off == 0):?>
                                        <input type="checkbox" value="1" tabindex="11" id="int_round_off" name="int_round_off" />
                                        <?php else:?>
                                        <input type="checkbox" value="1" tabindex="11" checked id="int_round_off" name="int_round_off"/>
                                        <?php endif;?>
                                        NBFC
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                            <?php if($fd[0]->fd_method=="Cumulative")
                              { ?>
                                <div id="cumulative">
                                  <label class="col-sm-2 control-label">Compound Mode</label>
                                  <div class="col-md-4  no-border">
                                      <input type="text" name="interest_mode" class="form-control" id="interest_mode" readonly
                                             value="<?php echo isset($fd[0]->interest_mode)?$fd[0]->interest_mode:'';?>">
                                  </div>
                                </div>
                          <?php }
                          else { ?>
                                <div id="non_cumulative">
                                   <label class="col-sm-2 control-label">Interest Mode</label>
                                   <div class="col-md-4  no-border">
                                       <input type="text" name="interest_mode" class="form-control" id="interest_mode" readonly
                                              value="<?php echo isset($fd[0]->interest_mode)?$fd[0]->interest_mode:'';?>">
                                   </div>
                                 </div>
                                 <?php } ?>
                                <label class="col-sm-2 control-label">Date of Issue</label>
                                <div class="col-md-4">
                                    <input type="text" name="issued_date" class="form-control" id="issued_date" readonly
                                           value="<?php echo isset($fd[0]->issued_date)?$fd[0]->issued_date:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount Invested</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="amount_invested" class="form-control" id="amount_invested" readonly
                                           value="<?php echo isset($fd[0]->amount_invested)?$fd[0]->amount_invested:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Interest Rate</label>
                                <div class="col-md-4">
                                    <input type="text" name="interest_rate" class="form-control" id="interest_rate" readonly
                                           value="<?php echo isset($fd[0]->interest_rate)?$fd[0]->interest_rate:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Maturity Date</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="maturity_date" class="form-control" id="maturity_date" readonly
                                           value="<?php echo isset($fd[0]->maturity_date)?$fd[0]->maturity_date:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Maturity Amount</label>
                                <div class="col-md-4">
                                    <input type="text" name="maturity_amount" required="required" tabindex="7" class="form-control" id="maturity_amount"
                                           value="<?php echo isset($fd[0]->maturity_amount)?$fd[0]->maturity_amount:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Nominee</label>
                                <div class="col-md-4  no-border">
                                  <input type="text" name="nominee"  tabindex="8" class="form-control" id="nominee">
                                </div>
                                <label class="col-sm-2 control-label">Interest Amount</label>
                                <div class="col-md-4">
                                    <input type="text" name="intAmt" readonly class="form-control" id="intAmt"
                                           value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Advisor</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="adv_id" class="populate" required="required" id="adv_id" style="width: 80%" tabindex="9">
                                        <option disabled selected value="">Select Advisor</option>
                                        <?php foreach($adv as $row):?>
                                            <option value='<?php echo $row->adviser_id; ?>'><?php echo $row->adviser_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="11" class="btn btn-xs btn-inverse-alt" onclick="add_adv(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label"></label>
                                <input type="hidden" id="hPreMature" name="hPreMature" value="0">
                                <button type="button" id="btn_pre_mat" class="btn btn-green" tabindex="11" onclick="premature_form()"><i class="fa fa-gavel"></i> PRE-MATURE</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <?php if($fd[0]->adjustment_flag == 1) :?>
                                    <label class="col-sm-2 control-label">Adjustment</label>
                                    <div class="col-sm-4">
                                        <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="12" checked>
                                                <input type="hidden" value="1" name="adjustment_flag" id="adjustment_flag">
                                            </span>
                                            <textarea placeholder="Adjustment" class="form-control" tabindex="13" id="adjustment" name="adjustment"><?php echo isset($fd[0]->adjustment)?$fd[0]->adjustment:'';?></textarea>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="14" class="form-control" value="<?php echo isset($fd[0]->adjustment_ref_number)?$fd[0]->adjustment_ref_number:'';?>">
                                    </div>
                                <?php else :?>
                                    <label class="col-sm-2 control-label">Adjustment</label>
                                    <div class="col-sm-4">
                                        <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="adj_flag" name="adj_flag">
                                                <input type="hidden" value="0" name="adjustment_flag" tabindex="12" id="adjustment_flag">
                                            </span>
                                            <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="13" name="adjustment" readonly><?php echo isset($fd[0]->adjustment)?$fd[0]->adjustment:'';?></textarea>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="14" class="form-control" readonly value="<?php echo isset($fd[0]->adjustment_ref_number)?$fd[0]->adjustment_ref_number:'';?>">
                                    </div>
                                <?php endif;?>
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
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="inv_bank_id" class="populate"  id="inv_bank_id" style="width: 80%" tabindex="15">
                                        <option disabled selected value="">Select Bank</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->bank_id; ?>'><?php echo $row->bank_name; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="34" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4 add-new-btn">
                                    <select class="form-control" name="inv_account_number" id="inv_account_number" tabindex="16" style="width: 80%; float:left;">
                                        <option></option>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="17" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cheque Number</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="inv_cheque_number" class="form-control" tabindex="18" id="inv_cheque_number"
                                        value="<?php echo isset($fd[0]->inv_cheque_number)?$fd[0]->inv_cheque_number:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Cheque Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="inv_cheque_date" class="form-control date" tabindex="19" id="inv_cheque_date"
                                           data-inputmask="'alias':'date'" value="<?php echo isset($fd[0]->inv_cheque_date) && ($fd[0]->inv_cheque_date != '00/00/0000')?$fd[0]->inv_cheque_date:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="inv_amount" class="form-control" tabindex="20" id="inv_amount"
                                           value="<?php echo isset($fd[0]->inv_amount)?$fd[0]->inv_amount:'';?>">
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
                                    <select name="maturity_bank_id" class="populate" id="maturity_bank_id" style="width: 80%" tabindex="21">
                                        <option disabled selected value="">Select Bank</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->bank_id; ?>'><?php echo $row->bank_name; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="34" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4 add-new-btn">
                                    <select class="form-control" name="maturity_account_number" id="maturity_account_number" tabindex="22" style="width: 80%; float:left;">
                                        <option></option>
                                    </select>
                                    <?php /*<a href="javascript:;" tabindex="23" class="btn btn-xs btn-inverse-alt" onclick="add_bank_account(true)"><i class="fa fa-plus"></i></a>*/ ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Payout Option</label>
                                <div class="col-md-4  no-border">
                                    <select name="maturity_payout_id" class="populate" id="maturity_payout_id" style="width: 100%" tabindex="24">
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
            <?php
            if( $this->session->userdata('permissions')=="3"){
              ?>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" id="add" tabindex="25" onclick="addForm('<?php echo base_url("broker/fixed_deposits/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" id="edit" tabindex="26" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" id="delete" tabindex="27" onclick="delete_fd()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" id="save" tabindex="28" onclick="fd_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" id="cancel" tabindex="29" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
                <?php
                }
                else if( $this->session->userdata('permissions')=="2"){
                ?>
                <div class="col-sm-12 bottom-col">
                    <button type="button" id="add" tabindex="25" onclick="addForm('<?php echo base_url("broker/fixed_deposits/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" id="edit" tabindex="26" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" id="delete" tabindex="27" onclick="delete_fd()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" id="save" tabindex="28" onclick="fd_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" id="cancel" tabindex="29" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
                <?php
                }
                else
                { }
                ?>
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
                            <form action="#" id="premature_form" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Pre-Maturity Date</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control date" name="pre_mat_date" id="pre_mat_date" data-inputmask="'alias':'date'" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Maturity Amount</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" data-type="number" name="mat_amt" id="mat_amt" required="required">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="pre_mature()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<style type="text/css">
    .datepicker{z-index:1151 !important;}
</style>
<script type="application/javascript">
var table;
$(function() {
    //initialize tooltip
    $('[data-toggle="tooltip"]').tooltip();

    //load client banks
    get_client_banks($("#client_id").val());

    //initialize select2
    $('#fd_inv_id').val($('#hInvID').val());
    $('#fd_inv_name').val($('#fd_inv_id option:selected').text());
    $('#fd_comp_id').val($('#hCompID').val());
    $('#prem_pay_mode_id').val($('#hPremPayMode').val());
    $('#adv_id').val($('#hAdviser').val());
    $('#nominee').val($('#hNominee').val());
    $('#maturity_payout_id').val($('#hPayID').val());
    $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
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
    $('#fd_inv_id').change(function(){
        $('#fd_inv_name').val($('#fd_inv_id option:selected').text());
    });
    get_inv_account_number($('#hInvBankID').val());
    get_mat_account_number($('#hMatBankID').val());
    //get_nominee();
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
    if($('#hStatus').val() == 'Premature')
    {
        $("#btn_pre_mat").attr('disabled', true);
    }

    $("#edit").click(function(){
        if($('#hStatus').val() == 'Premature')
        {
            $("#btn_pre_mat").attr('disabled', true);
        }
        $("#int_round_off").attr('disabled', true);
    });
    $("#int_round_off").attr("disabled", true);
    calculate_interest();


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
            $('#inv_bank_id').select2("val", $('#hInvBankID').val());
            $('#maturity_bank_id').select2("val", $('#hMatBankID').val());

        },
        error: function(data)
        {
            console.log(data);
        }
    });
}

//on change of selected bank
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

function get_inv_account_number(bank_id)
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
            $('#inv_account_number').html(option).val($('#hInvAcc').val());
        },
        error: function(data)
        {
            console.log(data);
        }
    });
}

//popup to add policy details
function premature_form()
{
    //reset form on modals
    $("#premature_form")[0].reset();
    //show bootstrap modal
    $("#modal_form").modal('show');
    //set title to modal
    $(".modal-title").text('Premature Fixed Deposit');

    $("#pre_mat_date").datepicker("remove");
    $("#pre_mat_date").datepicker({format: 'dd/mm/yyyy', startDate:$("#issued_date").val(), endDate:$("#maturity_date").val()});
}

function get_mat_account_number(bank_id)
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
            $('#maturity_account_number').html(option).val($('#hMatAcc').val());
        },
        error: function(data)
        {
            console.log(data);
        }
    });
}

function get_nominee()
{
    $.ajax({
        url: "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>",
        type:'post',
        data: {familyID: $('#family_id').val()},
        dataType: 'json',
        success:function(data)
        {
            var option = '<option disabled selected value="">Select Client</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].client_id+">"+data[i].name+"</option>";
            });
            $("#nominee").html(option);
            $("#nominee option[value='']").remove();
            $("#nominee").prepend("<option disabled value=''>Select Nominee</option>");
            $('#nominee').val($('#hNominee').val());
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

function calculate_interest()
{
    var fd_method = $('#fd_method').val();
    var amount_invested = $('#amount_invested').val();
    var issued_date = $('#issued_date').val();
    var interest_rate = $('#interest_rate').val();
    var maturity_date = $('#maturity_date').val();
    if((fd_method != null || fd_method != "") && amount_invested != '' && issued_date != '' && interest_rate != '' && maturity_date != '')
    {
        $.ajax({
            url: '<?php echo site_url("broker/Fixed_deposits/calculate_interest")?>',
            type: 'post',
            data: {fd_method: fd_method, amt_inv: amount_invested, issue_date: issued_date, int_rate: interest_rate, mat_date: maturity_date},
            dataType: 'json',
            success: function(data){
                //$('#maturity_amount').val(data['mat_amt']);
                $('#intAmt').val(data['int_amt']);
            },
            error: function(data){
                console.log(data);
            }
        });
    }
}

function pre_mature()
{
    var button = $('#save');
    var l = Ladda.create(button[0]);
    l.start();
    var valid = true;
    valid = $('#premature_form').parsley('validate');

    console.log($("#mat_amt").val());
    if($("#mat_amt").val() == 0) {
        valid = false;
        $.pnotify({
            title: "Invalid Maturity Amount!",
            text: "The Maturity Amount you entered is not proper OR is 0(zero). Please enter a proper Maturity Amount",
            type: 'error',
            hide: true
        });
    }
    if(valid)
    {
        var ci = $("#pre_mat_date").val();
        $("#maturity_date").val(ci);
        $("#maturity_amount").val($("#mat_amt").val());
        $("#hPreMature").val('1');
        $.ajax({
            url: '<?php echo site_url('broker/Fixed_deposits/edit_fd');?>',
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
                    enableBtn();
                    $("#modal_form").modal('hide');
                    $("#hPreMature").val('0');
                    $("#btn_pre_mat").attr('disabled', true);
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
                l.stop();
            }
        });
    }
}

//insert fixed deposit details in database
function fd_submit()
{
    var button = $('#save');
    var l = Ladda.create(button[0]);
    l.start();
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
        l.stop();
        dateValid = false;
    }
    valid = $('#fd_form').parsley('validate');
    if(valid && dateValid)
    {
        $.ajax({
            url: '<?php echo site_url('broker/Fixed_deposits/edit_fd');?>',
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
                l.stop();
            }
        });
    }
    else
        l.stop();
}

function delete_fd()
{
    bootbox.confirm("Are you sure you want to delete this Fixed Deposit?", function(result) {
        if(result)
        {
            var fd_id = $('#hTransID').val();
            $.ajax({
                url: '<?php echo site_url('broker/Fixed_deposits/delete_fd');?>',
                type: 'post',
                data: {fd_id: fd_id},
                dataType: 'json',
                success:function(data)
                {
                    if(data['type'] == 'success')
                    {
                        bootbox.alert("Fixed Deposit is deleted");
                        location.href = '<?php echo base_url();?>broker/Fixed_deposits';
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
</script>
