<!-- Bootstrap modal -->
<div class="modal fade" id="demat_provider_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="demat_provider_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="demat_provider_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="provider_id" id="providerID" type="hidden">
                            <label class="control-label col-md-3">Demat Provider Name</label>
                            <div class="col-md-9">
                                <input name="demat_provider" id="providerName" placeholder="Demat Provider Name" required class="form-control" type="text" data-rangelength="[3,50]">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_demat_provider()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_demat_provider()
    {
        save_method = 'add';
        //reset form on modals
        $("#demat_provider_form")[0].reset();
        //show bootstrap modal
        $("#demat_provider_modal_form").modal('show');
        //set title to modal
        $("#demat_provider_title").text('Add Demat Provider');
    }

    function edit_demat_provider(id)
    {
        //debugger;
        save_method = 'update';
        $('#demat_provider_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Demats/edit_demat_provider');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="provider_id"]').val(data.provider_id);
                $('[name="demat_provider"]').val(data.demat_provider);
                $('#demat_provider_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#demat_provider_title').text('Edit Demat Provider'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_demat_provider()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Demats/add_demat_provider');?>";
        else
            url = "<?php echo site_url('broker/Demats/update_demat_provider');?>";

        var valid = $('#demat_provider_form').parsley( 'validate' );
        if(valid) {
            var dematName = $("#providerName").val();
            $("#providerName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#demat_provider_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#demat_provider_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-demat_provider").length) {
                            var dematSelect = $("#select-demat_provider");
                            dematSelect.append("<option value='"+data['provider_id']+"' selected >"+dematName+"</option>"); // add it to the list of selections
                            dematSelect.select2("val", data['provider_id']);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_demat_providers_list();
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