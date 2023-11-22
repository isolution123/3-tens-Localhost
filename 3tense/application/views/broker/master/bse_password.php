<!-- Bootstrap modal -->
<div class="modal fade" id="modal_bsc_password_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="bsc_password_form" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">New BSE Password</label>
                            <div class="col-md-6">
                                <input name="BSEPassword" id="BSEPassword" placeholder="BSE Password" class="form-control" required="required" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_bsc_password()" data-style="expand-left" class="btn btn-primary ladda-button">Update</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="application/javascript">
    newMaster = false;
    function add_bsc_password(master)
    {
        newMaster = master;
        $("#bsc_password_form")[0].reset();
        //show bootstrap modal
        $("#modal_bsc_password_form").modal('show');
        //set title to modal
        $(".modal-title").text('Edit BSE Password');
    }
    function save_bsc_password()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        url = "<?php echo site_url('broker/Bse_Password/edit_bse_password');?>";
        if($('#bsc_password_form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#bsc_password_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data.status == '1'){
                        $("#modal_bsc_password_form").modal('hide');
                        $.pnotify({
                            title: data.title,
                            text: data.text,
                            type: 'success',
                            hide: true
                        });
                         window.setTimeout(function(){
                            // Move to a new location or you can do something else
                            window.location.href = '<?php echo site_url('broker/Bse_Password');?>';
                        }, 2000);
                        l.stop();
                    }else{
                        bootbox.alert('Error update data');    
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    bootbox.alert('Error adding / update data');
                }
            });
        }
        else
        {
            l.stop();
        }
    }
</script>
