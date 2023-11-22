<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_al_scheme" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal_al_scheme_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="al_scheme_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="scheme_name">Scheme Name</label>
                            <input name="scheme_id" id="scheme_id" type="hidden">
                            <div class="col-md-9">
                                <input name="scheme_name" id="scheme_name" placeholder="Scheme Name" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnProSave" onclick="save_al_scheme()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    var newMaster = false;
    function add_al_scheme(master)
    {
        newMaster = master;
        al_scheme_save_method = 'add';
        $("#al_scheme_form")[0].reset();
        $("#modal_form_al_scheme").modal('show');
        $("#modal_al_scheme_title").text('Add Asset and Liability Scheme');
    }

    function save_al_scheme()
    {
        var url, button, l, al_pro_form;
        button = $("#btnProSave");
        l = Ladda.create(button[0]);
        l.start();
        al_pro_form = $('#al_scheme_form');
        if(al_scheme_save_method == 'add')
            url = "<?php echo site_url('broker/Al_schemes/add_scheme'); ?>";
        else
            url = "<?php echo site_url('broker/Al_schemes/update_scheme'); ?>";
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
                        $('#modal_form_al_scheme').modal('hide');
                        if(newMaster)
                        {
                            getAlScheme("<?php echo site_url('broker/Al_schemes/get_scheme_dropdown'); ?>");
                        }
                        if(typeof al_scheme_table != 'undefined')
                        {
                            al_scheme_table.destroy();
                            al_scheme_table = ajax_al_list();
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
    function getAlScheme(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Scheme</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].scheme_id+">"+data[i].scheme_name+"</option>";
                });
                $("#al_scheme").html(option).select2("val", $("#al_scheme option:last-child").val()).change();
                $("#scheme_id").html(option).select2("val", $("#scheme_id option:last-child").val()).change();
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from asset and liability scheme',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

</script>