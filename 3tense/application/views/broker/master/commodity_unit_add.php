<!-- Bootstrap modal -->
<div class="modal fade" id="commodity_unit_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="commodity_unit_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="commodity_unit_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="unit_id" id="unitID" type="hidden">
                            <label class="control-label col-md-3">Commodity Unit Name</label>
                            <div class="col-md-9">
                                <input name="unit_name" id="unitName" placeholder="Commodity Unit" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_commodity_unit()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_commodity_unit()
    {
        save_method = 'add';
        //reset form on modals
        $("#commodity_unit_form")[0].reset();
        //show bootstrap modal
        $("#commodity_unit_modal_form").modal('show');
        //set title to modal
        $("#commodity_unit_title").text('Add Commodity Unit');
    }

    function edit_commodity_unit(id)
    {
        //debugger;
        save_method = 'update';
        $('#commodity_unit_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Commodity_units/edit_commodity_unit');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="unit_id"]').val(data.unit_id);
                $('[name="unit_name"]').val(data.unit_name);
                $('#commodity_unit_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#commodity_unit_title').text('Edit Commodity Unit'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_commodity_unit()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Commodity_units/add_commodity_unit');?>";
        else
            url = "<?php echo site_url('broker/Commodity_units/update_commodity_unit');?>";

        var valid = $('#commodity_unit_form').parsley( 'validate' );
        if(valid) {
            var unitName = $("#unitName").val();
            $("#unitName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#commodity_unit_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#commodity_unit_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#commodity_unit_id").length) {
                            $("#commodity_unit_id").append('<option value="'+data['unit_id']+'">'+unitName+'</option>').trigger('change');
                            if($("#commodity_unit_id").is("select")) {
                                $("#commodity_unit_id").select2("val", data['unit_id']);
                            }
                        }
                        if($("[name='unit_id']").length) {
                            $("[name='unit_id']").append('<option value="'+data['unit_id']+'">'+unitName+'</option>').trigger('change');
                            if($("[name='unit_id']").is("select")) {
                                $("[name='unit_id']").select2('val',data['unit_id']);
                            }
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined' && !$("#rate").length) {
                            table.destroy();
                            table = ajax_commodity_units_list();
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
            $('[name="unit_name"]').parent().parent().addClass('has-error').focus();
        }*/
    }
</script>