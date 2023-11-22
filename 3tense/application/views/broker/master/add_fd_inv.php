<!-- Bootstrap modal -->
<div class="modal fade" id="inv_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="inv_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Investment Type</label>
                            <input name="fdInvID" id="fdInvID" type="hidden">
                            <div class="col-md-9">
                                <input name="fdInvType" id="fdInvType" placeholder="Fixed Deposit Investment Type" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_fd_inv()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    newMaster = false;
    function add_fd_inv(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#inv_form")[0].reset();
        //show bootstrap modal
        $("#inv_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Fixed Deposit Investment');
    }
    function save_fd_inv()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Fd_investment_types/add_fd_inv');?>";
        else
            url = "<?php echo site_url('broker/Fd_investment_types/update_fd_inv');?>";
        if($("#inv_form").parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#inv_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'duplicate' || data['status'] == 0)
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'error',
                            hide: true
                        });
                    }
                    else
                    {
                        $("#inv_modal_form").modal('hide');
                        if(newMaster)
                        {
                            getInv("<?php echo site_url('broker/Fd_investment_types/get_fd_inv_dropdown');?>")
                        }
                        if(typeof table != 'undefined')
                        {
                            table.destroy();
                            table = ajax_fd_inv_list();
                            table_initialize();
                        }
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                    }
                    l.stop();
                },
                error: function (data)
                {
                    l.stop();
                    console.log(data);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
        else
        {
            l.stop();
        }
    }

    function getInv(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Investment Type</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].fd_inv_id+">"+data[i].fd_inv_type+"</option>";
                });
                $("#fd_inv_id").html(option).select2("val", "");
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from advisers',
                    type: 'error',
                    hide: true
                });
            }
        });
    }
</script>