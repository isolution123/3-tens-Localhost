<!-- Bootstrap modal -->
<div class="modal fade" id="modal_adviser_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="adviser_form" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="advID" id="advID" type="hidden">
                            <label class="control-label col-md-3">Advisor Name</label>
                            <div class="col-md-6">
                                <input name="advName" id="advName" placeholder="Advisor Name" class="form-control" required="required" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Company Name</label>
                            <div class="col-md-6">
                                <input name="advCompName" id="advCompName" placeholder="Company Name" class="form-control" required="required" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Product</label>
                            <div class="col-md-6">
                                <input name="advProduct" id="advProduct" placeholder="Product" class="form-control" required="required" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Agency Code</label>
                            <div class="col-md-6">
                                <input name="advAgcCode" id="advAgcCode" placeholder="Agency Code" class="form-control" required="required" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Contact Person</label>
                            <div class="col-md-6">
                                <input name="advConPerson" id="advConPerson" placeholder="Contact Person" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Contact Number</label>
                            <div class="col-md-6">
                                <input name="advConNumber" id="advConNumber" maxlength="50" placeholder="Mobile/Off No./Email" class="form-control" type="text">
                            </div>
                        </div>
                        <?php /*<div class="form-group">
                            <label class="control-label col-md-3">Contact Number 2</label>
                            <div class="col-md-6">
                                <input name="advConNumber2" id="advConNumber2" maxlength="10" placeholder="Mobile/Off No./Email" class="form-control" type="text">
                            </div>
                        </div>*/ ?>
                        <div class="form-group">
                            <label  class="control-label col-md-3">Ownership:</label>
                            <div class="radio-block" style="margin-top:9px;">
                                    <label><input type="radio" name="held_type" id="Held" value="Held" checked="checked"> Held</label><span style="margin-right: 30px;"></span>
                                    <label><input type="radio" name="held_type" id="Non-Held" value="Non-Held"> Non-Held</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_adv()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="application/javascript">
    newMaster = false;
    function add_adv(master)
    {
        newMaster = master;
        save_method = 'add';
        //reset form on modals
        $("#adviser_form")[0].reset();
        //show bootstrap modal
        $("#modal_adviser_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Advisor');
    }
    function save_adv()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/advisers/add_adviser');?>";
        else
            url = "<?php echo site_url('broker/advisers/update_adviser');?>";
        if($('#adviser_form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#adviser_form").serialize(),
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
                        $("#modal_adviser_form").modal('hide');
                        if(newMaster)
                        {
                            getAdvisers("<?php echo site_url('broker/Advisers/get_advisers_dropdown');?>")
                        }
                        if(typeof table != 'undefined')
                        {
                            table.destroy();
                            table = ajax_adv_list();
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
