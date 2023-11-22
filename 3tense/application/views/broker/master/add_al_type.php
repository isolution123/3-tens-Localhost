<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_al_type" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal_al_type_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="al_type_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="type_name">Type Name</label>
                            <input name="type_id" id="type_id" type="hidden">
                            <div class="col-md-9">
                                <input name="type_name" id="type_name" placeholder="Type Name" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnProSave" onclick="save_al_type()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    var newMaster = false;
    function add_al_type(master)
    {
        newMaster = master;
        al_type_save_method = 'add';
        $("#al_type_form")[0].reset();
        $("#modal_form_al_type").modal('show');
        $("#modal_al_type_title").text('Add Asset and Liability Type');
    }

    function save_al_type()
    {
        var url, button, l, al_pro_form;
        button = $("#btnProSave");
        l = Ladda.create(button[0]);
        l.start();
        al_pro_form = $('#al_type_form');
        if(al_type_save_method == 'add')
            url = "<?php echo site_url('broker/Al_types/add_type'); ?>";
        else
            url = "<?php echo site_url('broker/Al_types/update_type'); ?>";
        if(al_pro_form.parsley('validate'))
        {
            $.ajax({
                url: url,
                type: 'post',
                data: al_pro_form.serialize(),
                dataType: 'json',
                success: function(data){
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
                        $('#modal_form_al_type').modal('hide');
                        if(newMaster)
                        {
                            getAlType("<?php echo site_url('broker/Al_types/get_type_dropdown'); ?>");
                        }
                        if(typeof al_type_table != 'undefined')
                        {
                            al_type_table.destroy();
                            al_type_table = ajax_al_list();
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
                error: function(data){
                    l.stop();
                    console.log(data);
                    bootbox.alert("Something went wrong");
                }
            });
        }
        else
        {
            l.stop();
        }
    }
    function getAlType(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Type</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].type_id+">"+data[i].type_name+"</option>";
                });
                $("#al_type").html(option).select2("val", $("#al_type option:last-child").val()).change();
                $("#type_id").html(option).select2("val", $("#type_id option:last-child").val()).change();
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from asset and liability type',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

</script>