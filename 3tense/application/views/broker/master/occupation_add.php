<!-- Bootstrap modal -->
<div class="modal fade" id="occupation_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="occupation_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="occupation_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="occupation_id" id="occID" type="hidden">
                            <label class="control-label col-md-3">Occupation Name</label>
                            <div class="col-md-9">
                                <input name="occupation_name" id="occName" placeholder="Occupation" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_occupation()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_occupation()
    {
        save_method = 'add';
        //reset form on modals
        $("#occupation_form")[0].reset();
        //show bootstrap modal
        $("#occupation_modal_form").modal('show');
        //set title to modal
        $("#occupation_title").text('Add Occupation');
    }

    function edit_occupation(id)
    {
        //debugger;
        save_method = 'update';
        $('#occupation_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Occupations/edit_occupation');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="occupation_id"]').val(data.occupation_id);
                $('[name="occupation_name"]').val(data.occupation_name);
                $('#occupation_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#occupation_title').text('Edit Occupation'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_occupation()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Occupations/add_occupation');?>";
        else
            url = "<?php echo site_url('broker/Occupations/update_occupation');?>";

        var valid = $('#occupation_form').parsley( 'validate' );
        if(valid) {
            var occName = $("#occName").val();
            $("#occName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#occupation_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#occupation_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-occupation").length) {
                            $("#select-occupation").empty();
                            ajax_load_occupations(data['occupation_id']);
                            $("#select-occupation").select2("val", occName);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_occupations_list();
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