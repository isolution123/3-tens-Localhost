<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_al_company" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal_al_company_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="al_company_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="company_name">Company Name</label>
                            <input name="company_id" id="company_id" type="hidden">
                            <div class="col-md-9">
                                <input name="company_name" id="company_name" placeholder="Company Name" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnProSave" onclick="save_al_company()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    var newMaster = false;
    function add_al_company(master)
    {
        newMaster = master;
        al_comp_save_method = 'add';
        $("#al_company_form")[0].reset();
        $("#modal_form_al_company").modal('show');
        $("#modal_al_company_title").text('Add Asset and Liability Company');
    }

    function save_al_company()
    {
        var url, button, l, al_pro_form;
        button = $("#btnProSave");
        l = Ladda.create(button[0]);
        l.start();
        al_pro_form = $('#al_company_form');
        if(al_comp_save_method == 'add')
            url = "<?php echo site_url('broker/Al_companies/add_company'); ?>";
        else
            url = "<?php echo site_url('broker/Al_companies/update_company'); ?>";
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
                        $('#modal_form_al_company').modal('hide');
                        if(newMaster)
                        {
                            getAlCompany("<?php echo site_url('broker/Al_companies/get_company_dropdown'); ?>");
                        }
                        if(typeof al_comp_table != 'undefined')
                        {
                            al_comp_table.destroy();
                            al_comp_table = ajax_al_list();
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
    function getAlCompany(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Company</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].company_id+">"+data[i].company_name+"</option>";
                });
                $("#al_company").html(option).select2("val", $("#al_company option:last-child").val()).change();
                $("#company_id").html(option).select2("val", $("#company_id option:last-child").val()).change();
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from asset and liability Company',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

</script>