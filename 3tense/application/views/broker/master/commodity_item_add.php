<!-- Bootstrap modal -->
<div class="modal fade" id="commodity_item_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="commodity_item_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="commodity_item_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="item_id" id="itemID" type="hidden">
                            <label class="control-label col-md-3">Commodity Item Name</label>
                            <div class="col-md-9">
                                <input name="item_name" id="itemName" placeholder="Commodity Item" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_commodity_item()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_commodity_item()
    {
        save_method = 'add';
        //reset form on modals
        $("#commodity_item_form")[0].reset();
        //show bootstrap modal
        $("#commodity_item_modal_form").modal('show');
        //set title to modal
        $("#commodity_item_title").text('Add Commodity Item');
    }

    function edit_commodity_item(id)
    {
        //debugger;
        save_method = 'update';
        $('#commodity_item_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Commodity_items/edit_commodity_item');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="item_id"]').val(data.item_id);
                $('[name="item_name"]').val(data.item_name);
                $('#commodity_item_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#commodity_item_title').text('Edit Commodity Item'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_commodity_item()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Commodity_items/add_commodity_item');?>";
        else
            url = "<?php echo site_url('broker/Commodity_items/update_commodity_item');?>";

        var valid = $('#commodity_item_form').parsley( 'validate' );
        if(valid) {
            var itemName = $("#itemName").val();
            $("#itemName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#commodity_item_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#commodity_item_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#commodity_item_id").length) {
                            $("#commodity_item_id").append('<option value="'+data['item_id']+'">'+itemName+'</option>').trigger('change');
                            if($("#commodity_item_id").is("select")) {
                                $("#commodity_item_id").select2('val',data['item_id']);
                            }
                        }
                        if($("[name='item_id']").length) {
                            $("[name='item_id']").append('<option value="'+data['item_id']+'">'+itemName+'</option>').trigger('change');
                            if($("[name='item_id']").is("select")) {
                                $("[name='item_id']").select2('val',data['item_id']);
                            }
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined' && !$("#rate").length) {
                            table.destroy();
                            table = ajax_commodity_items_list();
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