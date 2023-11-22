<!-- Bootstrap modal -->
<div class="modal fade" id="client_type_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="client_type_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="client_type_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="client_type_id" id="client_type_ID" type="hidden">
                            <label class="control-label col-md-3">Client Type Name</label>
                            <div class="col-md-9">
                                <input name="client_type_name" id="client_type_Name" placeholder="Client Type" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_client_type()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_client_type()
    {
        save_method = 'add';
        //reset form on modals
        $("#client_type_form")[0].reset();
        //show bootstrap modal
        $("#client_type_modal_form").modal('show');
        //set title to modal
        $("#client_type_title").text('Add Client Type');
    }

    function edit_client_type(id)
    {
        //debugger;
        save_method = 'update';
        $('#client_type_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Client_types/edit_client_type');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="client_type_id"]').val(data.client_type_id);
                $('[name="client_type_name"]').val(data.client_type_name);
                $('#client_type_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#client_type_title').text('Edit Client Type'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_client_type()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Client_types/add_client_type');?>";
        else
            url = "<?php echo site_url('broker/Client_types/update_client_type');?>";

        var valid = $('#client_type_form').parsley( 'validate' );
        if(valid) {
            var client_type_Name = $("#client_type_Name").val();
            $("#client_type_Name").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#client_type_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#client_type_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-clientType").length) {
                            $("#select-clientType").empty();
                            ajax_load_client_types(data['client_type_id']);
                            $("#select-clientType").select2("val", client_type_Name);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_client_types_list();
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
    }
</script>