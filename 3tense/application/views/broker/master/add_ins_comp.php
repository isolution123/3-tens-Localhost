<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_ins_comp" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal_title_ins_comp"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="ins_comp_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Insurance Company</label>
                            <input name="insCompID" id="insCompID" type="hidden">
                            <div class="col-md-9">
                                <input name="insCompName" id="insCompName" placeholder="Insurance Company" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_ins_comp()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    newMaster = false;
    function add_ins_comp(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#ins_comp_form")[0].reset();
        //show bootstrap modal
        $("#modal_form_ins_comp").modal('show');
        //set title to modal
        $("#modal_title_ins_comp").text('Add Insurance Company');
    }
    function save_ins_comp()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Insurance_companies/add_ins_comp');?>";
        else
            url = "<?php echo site_url('broker/Insurance_companies/update_ins_comp');?>";
        if($('#ins_comp_form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#ins_comp_form").serialize(),
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
                        $("#modal_form_ins_comp").modal('hide');
                        if(newMaster)
                        {
                            getInsCompanies("<?php echo site_url('broker/insurance_companies/get_ins_comp_dropdown');?>")
                        }
                        if(typeof ins_comp_table != 'undefined')
                        {
                            ins_comp_table.destroy();
                            ins_comp_table = ajax_ins_comp_list();
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

    function getInsCompanies(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Insurance Company</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].ins_comp_id+">"+data[i].ins_comp_name+"</option>";
                });
                $("#insComp").html(option).select2("val", "");
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from insurance company',
                    type: 'error',
                    hide: true
                });
            }
        });
    }
</script>