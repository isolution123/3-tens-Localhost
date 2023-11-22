<!-- Bootstrap modal -->
<div class="modal fade" id="modal_family_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="family_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <input name="famID" id="famID" type="hidden">
                            <label class="control-label col-md-3">Family Name</label>
                            <div class="col-md-9">
                                <input name="famName" id="familyName" placeholder="Family Name" required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="famStatus" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select name="famStatus" id="famStatus" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_fam()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="text/javascript">
    newMaster = false;
    function add_family(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#family_form")[0].reset();
        //show bootstrap modal
        $("#modal_family_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Family');
    }

    function save_fam()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Families/add_family');?>";
        else
            url = "<?php echo site_url('broker/Families/update_family');?>";
        if($('#family_form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#family_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'duplicate')
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
                        $("#modal_family_form").modal('hide');
                        if(newMaster)
                        {
                            getFamilies("<?php echo site_url('broker/Families/get_families_dropdown');?>")
                        }
                        if(typeof table != 'undefined')
                        {
                            table.destroy();
                            table = ajax_family_list();
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