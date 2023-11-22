<!-- Bootstrap modal -->
<div class="modal fade" id="modal_scheme_type_form" role="dialog">
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
                            <input name="scheme_type_id" id="scheme_type_id" type="hidden">
                            <label class="control-label col-md-3">Scheme Trigger</label>
                            <div class="col-md-9">
                                <input name="scheme_type" id="scheme_type" placeholder="Scheme Type" required class="form-control" type="text" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="famStatus" class="col-sm-3 control-label">Scheme Target Value</label>
                            <div class="col-sm-9">
                                  <input name="scheme_target_value" id="scheme_target_value" placeholder="Scheme Target Value" required class="form-control" type="number" >
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

    function save_fam()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        
            url = "<?php echo site_url('broker/Scheme_Trigger/update_scheme_type');?>";
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
                        $("#modal_scheme_type_form").modal('hide');
                        // if(newMaster)
                        // {
                        //     getFamilies("<?php echo site_url('broker/Families/get_families_dropdown');?>")
                        // }
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