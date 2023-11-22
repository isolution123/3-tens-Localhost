<!-- Add Bank Account Bootstrap modal -->
<div class="modal fade" id="bank_account_modal_form" role="dialog" style="z-index: 1500">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="bank_account_form" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Bank Name</label>
                            <div class="col-md-6 add-new-btn">
                                <select name="bank_id" id="select-banks" required="required"  style="width: 80%">
                                    <option></option>
                                    <?php foreach($banks as $bank) { ?>
                                        <option value="<?=$bank->bank_id;?>"><?=$bank->bank_name;?></option>
                                    <?php } ?>
                                </select>
                                <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_bank()"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Account Type</label>
                            <div class="col-md-6 add-new-btn">
                                <select name="account_type" id="select-bankAccType" required="required" style="width: 80%">
                                    <option></option>
                                    <?php foreach($bank_account_types as $account_type) { ?>
                                        <option value="<?=$account_type->account_type_id;?>"><?=$account_type->account_type_name;?></option>
                                    <?php } ?>
                                </select>
                                <!--<a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_bank()"><i class="fa fa-plus"></i></a>-->
                            </div>
                            <!--<div class="col-md-6">
                                <input name="account_type" data-maxlength="30" required class="form-control" type="text">
                            </div>-->
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Branch</label>
                            <div class="col-md-6">
                                <input name="branch" data-maxlength="80" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">IFSC</label>
                            <div class="col-md-6">
                                <input name="IFSC" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Account Number</label>
                            <div class="col-md-6">
                                <input id="acc_num1" name="account_number" data-rangelength="[1,30]" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <input id="hClient_id" name="client_id" required="required" value="<?=isset($client_info->client_id)?$client_info->client_id:'';?>" type="hidden">
                            <input name="user_id" value="<?=$_SESSION['broker_id'];?>" type="hidden">
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_bank_account()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bank Account Bootstrap modal -->

<script type="text/javascript">
    /* bank account functions START - Load data/Add/Delete */
    function add_bank_account()
    {
        var clientID1 = $("#client_id").val();
        var clientID2 = $("input:hidden[name=client_id]").val();
        if((clientID1 != "" && clientID1 != null) || (clientID2 != "" && clientID2 != null)) {
            save_method = 'add';
            //reset form on modals
            $("#bank_account_form")[0].reset();
            //show bootstrap modal
            $("#bank_account_modal_form").modal('show');
            //set title to modal
            $(".modal-title").text('Add Bank Account');

            //initialize bank name in select2
            $('#select-banks').select2({
                width: 'resolve',
                placeholder:"Please select a Bank"
            }).on('change', function(e){
                    //on change of plan name update text in insPlanRename
                    var data = $('#select-banks').select2('data');
                    $("#bank-id-hidden").val(data.value);
                });
            //initialize bank account type in select2
            $('#select-bankAccType').select2({
                width: 'resolve',
                placeholder:"Please select an Account Type"
            });
        } else {
            bootbox.alert("Please select a client to add new bank account details");
        }
    }
    function save_bank_account()
    {
        //debugger;
        if($("#hClient_id").val() == "") {
            if($("#client_id").length) {
                $("#hClient_id").val($("#client_id").val());
            }
        }
        var valid = $('#bank_account_form').parsley( 'validate' );
        if(valid) {
            var url;
            if(save_method == 'add')
                url = "<?php echo base_url('broker/clients/add_bank_account');?>";
            else
                url = "<?php echo base_url('broker/clients/update_bank_account');?>?account_id="+bank_accountID;

            $.ajax({
                url: url,
                type:'post',
                data: $("#bank_account_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    //debugger;
                    $("#bank_account_modal_form").modal('hide');
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });
                    //if bank_table exists, refresh its list
                    if(typeof bank_table != 'undefined') {
                        bank_table.destroy();
                        bank_table = ajax_bank_accounts();
                    }

                    var bank_id1 = $("#select-banks").val();
                    var bank_data = $("#select-banks").select2("data");
                    var bank_name1 = bank_data.text;
                    var acc_num1 = $("#acc_num1").val();

                    //if bank_selectbox exists, then append our bank into that
                    if($("[name='inv_bank_id']").length) {
                        $("[name='inv_bank_id']").append('<option value="'+bank_id1+'">'+bank_name1+'</option>').trigger('change');
                        if($("[name='inv_bank_id']").is("select")) {
                            $("[name='inv_bank_id']").select2('val',bank_id1).change();
                        }
                    }
                    if($("[name='inv_account_number']").length) {
                        $("[name='inv_account_number']").append('<option value="'+acc_num1+'">'+acc_num1+'</option>').trigger('change');
                        if($("[name='inv_account_number']").is("select")) {
                            $("[name='inv_account_number']").val(acc_num1).change();
                            //$("[name='inv_account_number']").select2('val',acc_num1);
                        }
                    }

                    //if bank_selectbox exists, then append our bank into that
                    if($("[name='bank_id']").length) {
                        $("[name='bank_id']").append('<option value="'+bank_id1+'">'+bank_name1+'</option>').trigger('change');
                        if($("[name='bank_id']").is("select")) {
                            $("[name='bank_id']").select2('val',bank_id1).change();
                        }
                    }
                    if($("[name='account_number']").length) {
                        $("[name='account_number']").append('<option value="'+acc_num1+'">'+acc_num1+'</option>').trigger('change');
                        if($("[name='account_number']").is("select")) {
                            $("[name='account_number']").val(acc_num1);
                            //$("[name='account_number']").select2('val',acc_num1);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    bootbox.alert('Error adding / update data');
                }
            });
        }
    }
    function delete_bank_account(id)
    {
        bootbox.confirm('Are you sure you want to delete this data?', function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_bank_account');?>",
                    type: "POST",
                    data:{account_id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#bank_account_modal_form').modal('hide');
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });
                        bank_table.destroy();
                        bank_table = ajax_bank_accounts();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
    /* bank account functions END */
</script>
