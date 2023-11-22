<!-- Bootstrap modal -->
<div class="modal fade" id="document_type_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="document_type_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="document_type_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="document_type_id" id="docTypeID" type="hidden">
                            <label class="control-label col-md-3">Document Type Name</label>
                            <div class="col-md-9">
                                <input name="document_type" id="docTypeName" placeholder="Document Type" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_document_type()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_document_type()
    {
        save_method = 'add';
        //reset form on modals
        $("#document_type_form")[0].reset();
        //show bootstrap modal
        $("#document_type_modal_form").modal('show');
        //set title to modal
        $("#document_type_title").text('Add Document Type');
    }

    function edit_document_type(id)
    {
        //debugger;
        save_method = 'update';
        $('#document_type_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Document_types/edit_document_type');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="document_type_id"]').val(data.document_type_id);
                $('[name="document_type"]').val(data.document_type);
                $('#document_type_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#document_type_title').text('Edit Document Type'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_document_type()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Document_types/add_document_type');?>";
        else
            url = "<?php echo site_url('broker/Document_types/update_document_type');?>";

        var valid = $('#document_type_form').parsley( 'validate' );
        if(valid) {
            var docName = $("#docTypeName").val();
            $("#docTypeName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#document_type_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#document_type_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-docType").length) {
                            $("#select-docType").empty();
                            ajax_load_document_types(data['document_type_id']);
                            //$("#select-docType").select2("val", docName);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_document_types_list();
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