<!-- Add Trading Bootstrap modal -->
<div class="modal fade" id="trading_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="client_trading_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="trading_form" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Broker</label>
                            <div class="col-md-6 add-new-btn">
                                <select name="broker" id="select-tradingBroker" required="required"  style="width: 80%">
                                    <option></option>
                                    <?php foreach($trading_brokers as $broker) { ?>
                                        <option value="<?=$broker->trading_broker_id;?>"><?=$broker->trading_broker_name;?></option>
                                    <?php } ?>
                                </select>
                                <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_trading_broker()"><i class="fa fa-plus"></i></a>
                            </div>
                            <!--<div class="col-md-6">
                                <input name="broker" data-maxlength="80" required class="form-control" type="text">
                            </div>-->
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Client Code</label>
                            <div class="col-md-6">
                                <input name="client_code" data-type="alphanum" data-maxlength="80" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Balance</label>
                            <div class="col-md-6">
                                <input name="balance" data-type="number" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="control-label col-md-3">Ownership:</label>
                           <div class="radio-block" style="margin-top:9px;">
                                   <label><input type="radio" name="held_type" id="Held" value="Held" checked="checked"> Held</label><span style="margin-right: 30px;"></span>
                                   <label><input type="radio" name="held_type" id="Non-Held" value="Non-Held"> Non-Held</label>
                           </div>
                       </div>
                        <div class="form-group">
                            <input id="trading_client_id" name="client_id" value="<?=isset($client_info->client_id)?$client_info->client_id:'';?>" type="hidden">
                            <input name="user_id" value="<?=$_SESSION['broker_id'];?>" type="hidden">
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_trading()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Trading Bootstrap modal -->

<script type="text/javascript">
    /* trading functions START - Load data/Add/Delete */
    function add_trading()
    {
        var clientID1 = $("#client_id").val();
        var clientID2 = $("input:hidden[name=client_id]").val();
        if((clientID1 != "" && clientID1 != null) || (clientID2 != "" && clientID2 != null)) {
            save_method = 'add';
            //reset form on modals
            $("#trading_form")[0].reset();
            //show bootstrap modal
            $("#trading_modal_form").modal('show');
            //set title to modal
            $("#client_trading_title").text('Add Trading');

            //initialize broker name in select2
            $('#select-tradingBroker').select2({
                width: 'resolve',
                placeholder:"Please select a Broker"
            });
        } else {
            bootbox.alert("Please select a client to add new trading details");
        }
    }
    function save_trading()
    {
        var proceed = false;
        var clientID = $("#trading_client_id").val();
        console.log(clientID);
        if(clientID == '') {
            if($("#client_id").length) {
                clientID = $("#client_id").val();
                $("#trading_client_id").val(clientID);
                proceed = true;
            } else {
                $("#trading_modal_form").modal('hide');
                bootbox.alert("Could not get client ID for which to add/update Trading Broker");
            }
        } else {
            proceed = true;
        }
        //debugger;
        var valid = $('#trading_form').parsley( 'validate' );
        if(proceed && valid) {
            var url = "<?php echo base_url('broker/clients/add_trading');?>";
            $.ajax({
                url: url,
                type:'post',
                data: $("#trading_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#trading_modal_form").modal('hide');
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] == 'success') {
                        if($("#trading_broker_id").length) {
                            selectBox = $("#trading_broker_id");
                            selectBox.append("<option value='"+data['data']['trading_broker_id']+"' selected >"+data['data']['trading_broker_name']+"</option>"); // add it to the list of selections
                            selectBox.select2("val", data['data']['trading_broker_id']);
                        }
                    }
                    if(typeof trading_table != 'undefined') {
                        trading_table.destroy();
                        trading_table = ajax_tradings();
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
    function delete_trading(id)
    {
        bootbox.confirm('Are you sure you want to delete this data?', function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_trading');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#trading_modal_form').modal('hide');
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });
                        trading_table.destroy();
                        trading_table = ajax_tradings();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
    /* trading functions END */
</script>
