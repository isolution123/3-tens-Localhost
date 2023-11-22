<!-- Bootstrap modal -->
<div class="modal fade" id="bank_add_modal_form" role="dialog" style="z-index: 2000">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="bank_add_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="bank_add_form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="bank_id" id="bankID" type="hidden">
                            <label class="control-label col-md-3">Bank Name</label>
                            <div class="col-md-9">
                                <input name="bank_name" id="bankName" placeholder="Bank Name" required class="form-control" type="text" data-rangelength="[5,50]">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_bank()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_bank()
    {
        save_method = 'add';
        //reset form on modals
        $("#bank_add_form")[0].reset();
        //show bootstrap modal
        $("#bank_add_modal_form").modal('show');
        //set title to modal
        $("#bank_add_title").text('Add Bank');
    }

    function edit_bank(id)
    {
        //debugger;
        save_method = 'update';
        $('#bank_add_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Banks/edit_bank');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="bank_id"]').val(data.bank_id);
                $('[name="bank_name"]').val(data.bank_name);
                $('#bank_add_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#bank_add_title').text('Edit Bank'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_bank()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Banks/add_bank');?>";
        else
            url = "<?php echo site_url('broker/Banks/update_bank');?>";

        var valid = $('#bank_add_form').parsley( 'validate' );
        if(valid) {
            var bankName = $("#bankName").val();
            $("#bankName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#bank_add_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#bank_add_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#select-banks").length) {
                            var bankSelect = $("#select-banks");
                            bankSelect.append("<option value='"+data['bank_id']+"' selected >"+bankName+"</option>"); // add it to the list of selections
                            bankSelect.select2("val", data['bank_id']);
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_banks_list();
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