<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Mutual_funds');?>">Mutual Fund</a></li>
                <li class="active">Edit Mutual Fund</li>
            </ol>
            <h1>Edit Mutual Fund</h1>
        </div>
        <div class="container">
            <form action="#" id="mf_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <input type="hidden" id="transID" name="transID" value="<?= $mf[0]->transaction_id; ?>">
                <input type="hidden" id="client_id" name="client_id" value="<?= $mf[0]->client_id; ?>">
                <input type="hidden" id="hbank_id" name="hbank_id" value="<?php echo isset($mf[0]->bank_id) && !empty($mf[0]->bank_id)?$mf[0]->bank_id:''; ?>">
                <input type="hidden" id="hbranch" name="hbranch" value="<?php echo isset($mf[0]->branch) && !empty($mf[0]->branch)?$mf[0]->branch:''; ?>">
                <input type="hidden" id="haccount_number" name="haccount_number" value="<?php echo isset($mf[0]->account_number) && !empty($mf[0]->account_number)?$mf[0]->account_number:''; ?>">
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="family_name" value="<?php echo isset($mf[0]->family_name)?$mf[0]->family_name:'';?>"
                                        class="form-control" id="family_name" readonly>
                                </div>
                                <label class="col-sm-2 control-label">Folio Number</label>
                                <div class="col-md-4  no-border">
                                    <div id="divPur">
                                        <input type="text" name="folio_number" value="<?php echo isset($mf[0]->folio_number)?$mf[0]->folio_number:'';?>"
                                            class="form-control" id="folio_number" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4">
                                    <input type="text" name="client_name" value="<?php echo isset($mf[0]->client_name)?$mf[0]->client_name:'';?>"
                                        class="form-control" id="client_name" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Mutual Fund Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Type of Transaction</label>
                                <?php if($mf[0]->transaction_type == 'Purchase'):?>
                                <div class="radio col-md-4 no-border">
                                    <label style="margin-right: 50px">
                                        <input type="radio" name="transaction_type" readonly class="disabled" id="transaction_type1" value="Purchase" checked style="padding-right: 20px">
                                        Purchase
                                    </label>
                                    <label>
                                        <input type="radio" name="transaction_type" readonly class="disabled" id="transaction_type2" value="Redemption">
                                        Redemption
                                    </label>
                                </div>
                                <?php else:?>
                                <div class="radio col-md-4 no-border">
                                    <label style="margin-right: 50px">
                                        <input type="radio" name="transaction_type" readonly class="disabled" id="transaction_type1" value="Purchase" style="padding-right: 20px">
                                        Purchase
                                    </label>
                                    <label>
                                        <input type="radio" name="transaction_type" readonly class="disabled" id="transaction_type2" value="Redemption" checked>
                                        Redemption
                                    </label>
                                </div>
                                <?php endif; ?>
                                <label class="col-sm-2 control-label">Purchase/Redemption Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="purchase_date" class="form-control" id="purchase_date" readonly
                                        value="<?php echo isset($mf[0]->purchase_date)?$mf[0]->purchase_date:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mutual Fund Type</label>
                                <div class="col-md-4">
                                    <input type="text" name="mutual_fund_type" class="form-control" id="mutual_fund_type" readonly
                                        value="<?php echo isset($mf[0]->mutual_fund_type)?$mf[0]->mutual_fund_type:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">Number of Units</label>
                                <div class="col-md-2 no-border">
                                    <input type="number" min="1" name="quantity" class="form-control" id="quantity" readonly
                                        value="<?php echo isset($mf[0]->quantity)?$mf[0]->quantity:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mutual Fund Scheme</label>
                                <div class="col-md-4">
                                    <input type="text" name="mutual_fund_scheme" class="form-control" id="mutual_fund_scheme" readonly
                                        value="<?php echo isset($mf[0]->scheme_name)?$mf[0]->scheme_name:'';?>">
                                </div>
                                <label class="col-sm-2 control-label">NAV</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="nav" class="form-control" id="nav" readonly
                                        value="<?php echo isset($mf[0]->nav)?$mf[0]->nav:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="amount" readonly class="form-control" id="amount"
                                        value="<?php echo isset($mf[0]->amount)?$mf[0]->amount:'';?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <?php if($mf[0]->adjustment_flag == 1) :?>
                                    <label class="col-sm-2 control-label">Adjustment</label>
                                    <div class="col-sm-4">
                                        <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="1" checked >
                                                <input type="hidden" name="adjustment_flag" id="adjustment_flag">
                                            </span>
                                            <textarea placeholder="Adjustment" class="form-control" tabindex="2" id="adjustment" name="adjustment"><?php echo isset($mf[0]->adjustment)?$mf[0]->adjustment:'';?></textarea>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="3" class="form-control" value="<?php echo isset($mf[0]->adjustment_ref_number)?$mf[0]->adjustment_ref_number:'';?>">
                                    </div>
                                <?php else :?>
                                    <label class="col-sm-2 control-label">Adjustment</label>
                                    <div class="col-sm-4">
                                        <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                            <span class="input-group-addon">
                                                <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="1">
                                                <input type="hidden" name="adjustment_flag" id="adjustment_flag">
                                            </span>
                                            <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="2" name="adjustment" readonly><?php echo isset($mf[0]->adjustment)?$mf[0]->adjustment:'';?></textarea>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="3" class="form-control" readonly value="<?php echo isset($mf[0]->adjustment_ref_number)?$mf[0]->adjustment_ref_number:'';?>">
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Mutual Fund Bank Details</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <?php if(isset($mf[0]->mutual_fund_type) && ($mf[0]->mutual_fund_type == 'PIP' || $mf[0]->mutual_fund_type == 'IPO')):?>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Bank Name</label>
                                <div class="col-md-4  no-border">
                                    <select name="bank_id" class="populate" id="bank_id" style="width: 100%" tabindex="15">
                                        <option disabled selected value="">Select Bank</option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="account_number1" id="account_number" tabindex="17">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Branch Name</label>
                                <div class="col-md-4  no-border">
                                    <select name="branch1" class="populate" id="branch" style="width: 100%" tabindex="16">
                                        <option disabled selected value="">Select Branch</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->branch; ?>'><?php echo $row->branch; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Cheque Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="cheque_number1" class="form-control" tabindex="18" id="cheque_number" value="<?php echo isset($mf[0]->cheque_number) && !empty($mf[0]->cheque_number)?$mf[0]->cheque_number:''; ?>">
                                </div>
                            </div>
                        </div>
                        <?php else :?>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Bank Name</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" readonly name="bank_name" class="form-control" id="bank_name"
                                               value="<?php echo isset($mf[0]->bank_name)?$mf[0]->bank_name:'';?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Account Number</label>
                                    <div class="col-md-4">
                                        <input type="text" readonly name="account_number" class="form-control" id="account_number"
                                               value="<?php echo isset($mf[0]->account_number)?$mf[0]->account_number:'';?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Branch Name</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" readonly name="branch" class="form-control" id="branch"
                                               value="<?php echo isset($mf[0]->branch)?$mf[0]->branch:'';?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Cheque Number</label>
                                    <div class="col-md-4  no-border">
                                        <input type="text" readonly name="cheque_number" class="form-control" id="cheque_number"
                                               value="<?php echo isset($mf[0]->cheque_number)?$mf[0]->cheque_number:'';?>">
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </form>
            <div class="bottom-row navbar-fixed-bottom">
              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="4" onclick="addForm('<?php echo base_url("broker/mutual_funds/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="5" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="6" onclick="delete_mf()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="7" onclick="mf_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="8" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>

<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" tabindex="4" onclick="addForm('<?php echo base_url("broker/mutual_funds/add_form");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" tabindex="5" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" tabindex="6" onclick="delete_mf()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" tabindex="7" onclick="mf_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" tabindex="8" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else
{ }
?>

            </div>
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
    $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
    $('[data-toggle="tooltip"]').tooltip();
    $('[name="transaction_type"]').attr('disabled', true);
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

    $('#bank_id').change(function(){
        $.ajax({
            url: '<?php echo site_url("broker/Banks/get_branch");?>',
            type: 'post',
            data: {bankID: this.value, clientID: $('#client_id').val()},
            dataType: 'json',
            success:function(data)
            {
                var branch = $("#hbranch").val()
                var option = '<option disabled selected value="">Select Branch</option>';
                $.each(data['branches'], function(i, item){
                    if(item.branch == branch) {
                        option = option + "<option selected value='"+item.branch+"'>"+item.branch+"</option>";
                    } else {
                        option = option + "<option value='"+item.branch+"'>"+item.branch+"</option>";
                    }
                });
                $("#branch").html(option);
                $("#branch").select2("val", branch).change();
            },
            error:function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });

    $('#branch').change(function(){
        $.ajax({
            url: '<?php echo site_url("broker/Banks/get_account_num");?>',
            type: 'post',
            data: {bankID: $('#bank_id').val(), clientID: $('#client_id').val(), branch: $('#branch').val()},
            dataType: 'json',
            success:function(data)
            {
                var acc = $("#haccount_number").val();
                var option = '<option disabled selected value="">Select Account Number</option>';
                $.each(data['acc_num'], function(i, item){
                    if(item.account_number == acc) {
                        option = option + "<option selected value='"+item.account_number+"'>"+item.account_number+"</option>";
                    } else {
                        option = option + "<option value='"+item.account_number+"'>"+item.account_number+"</option>";
                    }
                });
                $("#account_number").html(option);
                //$("#account_number").val();
            },
            error:function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });

    //for loading bank/other details
    load_data($("#client_id").val());
});


function load_data(client_id) {
    $.ajax({
        url: '<?php echo site_url('broker/Banks/get_client_banks'); ?>',
        dataType: 'json',
        type: 'post',
        data: {clientID: client_id},
        success: function(data)
        {
            var option = '<option selected disabled value="">Select Bank</option>';
            $.each(data['banks'], function(i, item){
                option = option + "<option value = " + item.bank_id + ">" + item.bank_name + "</option>";
            });
            $('#bank_id').html(option);
            $("#bank_id").select2("val", $("#hbank_id").val()).change();
        },
        error: function(data)
        {
            console.log(data);
        }
    });
}

//insert fixed deposit details in database
function mf_submit()
{
    var button = $('#save');
    var l = Ladda.create(button[0]);
    l.start();
    $.ajax({
        url: '<?php echo site_url('broker/Mutual_funds/edit_mf');?>',
        type: 'post',
        data: $('#mf_form').serialize(),
        dataType: 'json',
        success:function(data)
        {
            $.pnotify({
                title: data['title'],
                text: data['text'],
                type: 'success',
                hide: true
            });
            l.stop();
            enableBtn();
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

function delete_mf()
{
    bootbox.confirm("Are you sure you want to delete this Mutual Fund?", function(result) {
        if(result)
        {
            var mf_id = $('#transID').val();
            $.ajax({
                url: '<?php echo site_url('broker/Mutual_funds/delete_mf');?>',
                type: 'post',
                data: {mf_id: mf_id},
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'success')
                    {
                        bootbox.alert("Mutual Fund is deleted");
                        location.href = '<?php echo base_url();?>broker/Mutual_funds';
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
