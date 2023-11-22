<!-- Bootstrap modal -->
<div class="modal fade" id="comp_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="comp_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Company Name</label>
                            <input name="fdCompID" id="fdCompID" type="hidden">
                            <div class="col-md-9">
                                <input name="fdCompName" id="fdCompName" placeholder="Fixed Deposit Company" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_fd_comp()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    newMaster = false;
    function add_fd_comp(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#comp_form")[0].reset();
        //show bootstrap modal
        $("#comp_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Fixed Deposit Company');
    }
    function save_fd_comp()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Fd_companies/add_fd_comp');?>";
        else
            url = "<?php echo site_url('broker/Fd_companies/update_fd_comp');?>";
        if($('#comp_form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#comp_form").serialize(),
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
                        $("#comp_modal_form").modal('hide');
                        if(newMaster)
                        {
                            getComp("<?php echo site_url('broker/Fd_companies/get_fd_comp_dropdown');?>")
                        }
                        if(typeof table != 'undefined')
                        {
                            table.destroy();
                            table = ajax_fd_comp_list();
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

    function getComp(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Company Name</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].fd_comp_id+">"+data[i].fd_comp_name+"</option>";
                });
                $("#fd_comp_id").html(option).select2("val", "");
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