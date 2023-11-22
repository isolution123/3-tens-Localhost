<!-- Bootstrap modal -->
<div class="modal fade" id="premium_master_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="premium_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Asset Allocation</label>
                            <input name="premTypeID" id="premTypeID" type="hidden">
                            <div class="col-md-9">
                                <input name="premTypeName" id="premTypeName" placeholder="Premium Name" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_prem_type()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="application/javascript">
    newMaster = false;
    function add_premium_type(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#premium_form")[0].reset();
        //show bootstrap modal
        $("#premium_master_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Premium Type');
    }
    function save_prem_type()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Premium_types/add_prem_type');?>";
        else
            url = "<?php echo site_url('broker/Premium_types/update_prem_type');?>";
        if($("#premium_form").parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#premium_form").serialize(),
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
                        $("#premium_master_modal_form").modal('hide');
                        if(newMaster)
                        {
                            getPremTypes("<?php echo site_url('broker/Premium_types/get_prem_type_dropdown');?>")
                        }
                        if(typeof prem_type_table != 'undefined')
                        {
                            prem_type_table.destroy();
                            prem_type_table = ajax_premium_type_list();
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
                    console.log(data);
                    l.stop();
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
        else
        {
            l.stop();
        }
    }
</script>