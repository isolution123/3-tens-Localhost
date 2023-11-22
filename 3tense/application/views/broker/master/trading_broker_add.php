<!-- Bootstrap modal -->
<div class="modal fade" id="trading_broker_add_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="trading_broker_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="trading_broker_add_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="trading_broker_id" id="trading_brokerID" type="hidden">
                            <label class="control-label col-md-3">Trading Broker Name</label>
                            <div class="col-md-9">
                                <input name="trading_broker_name" id="trading_brokerName" placeholder="Trading Broker Name" required class="form-control" type="text" data-rangelength="[5,50]">
                            </div>
                        </div>
                         <!--<div class="form-group">
                            <label  class="control-label col-md-3">Ownership:</label>
                            <div class="radio-block" style="margin-top:9px;">
                                    <label><input type="radio" name="held_type" id="Held" value="Held" checked="checked"> Held</label><span style="margin-right: 30px;"></span>
                                    <label><input type="radio" name="held_type" id="Non-Held" value="Non-Held"> Non-Held</label>
                            </div>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_trading_broker()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_trading_broker()
    {
        save_method = 'add';
        //reset form on modals
        $("#trading_broker_add_form")[0].reset();
        //show bootstrap modal
        $("#trading_broker_add_modal_form").modal('show');
        //set title to modal
        $("#trading_broker_title").text('Add Trading Broker');
    }

    function edit_trading_broker(id)
    {
        //debugger;
        save_method = 'update';
        $('#trading_broker_add_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Trading_brokers/edit_trading_broker');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="trading_broker_id"]').val(data.trading_broker_id);
                $('[name="trading_broker_name"]').val(data.trading_broker_name);
                $('#'+data.held_type).prop("checked",true);
                $('#trading_broker_add_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#trading_broker_title').text('Edit Trading Broker'); // Set title to Bootstrap modal title


            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_trading_broker()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Trading_brokers/add_trading_broker');?>";
        else
            url = "<?php echo site_url('broker/Trading_brokers/update_trading_broker');?>";

        var valid = $('#trading_broker_add_form').parsley( 'validate' );
        if(valid) {
            var TradingBrokerName = $("#trading_brokerName").val();
            $("#trading_brokerName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#trading_broker_add_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#trading_broker_add_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-tradingBroker").length) {
                            var tradingBrokerSelect = $("#select-tradingBroker");
                            tradingBrokerSelect.append("<option value='"+data['trading_broker_id']+"' selected >"+TradingBrokerName+"</option>"); // add it to the list of selections
                            tradingBrokerSelect.select2("val", data['trading_broker_id']);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_trading_brokers_list();
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert('Error adding / updating data');
                }
            });
        }
        /*else
        {
            alert('Please add proper details');
            $('[name="occupation_name"]').parent().parent().addClass('has-error').focus();
        }*/
    }
</script>
