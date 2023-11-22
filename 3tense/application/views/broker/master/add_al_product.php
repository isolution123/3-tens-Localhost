<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_al_product" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="modal_al_product_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="al_product_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="product_name">Product Name</label>
                            <input name="product_id" id="product_id" type="hidden">
                            <div class="col-md-9">
                                <input name="product_name" id="product_name" placeholder="Product Name" required class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnProSave" onclick="save_al_product()" data-style="expand-left" class="btn btn-primary ladda-button">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="application/javascript">
    var newMaster = false;
    function add_al_product(master)
    {
        newMaster = master;
        al_product_save_method = 'add';
        $("#al_product_form")[0].reset();
        $("#modal_form_al_product").modal('show');
        $("#modal_al_product_title").text('Add Asset and Liability Product');
    }

    function save_al_product()
    {
        var url, button, l, al_pro_form;
        button = $("#btnProSave");
        l = Ladda.create(button[0]);
        l.start();
        al_pro_form = $('#al_product_form');
        if(al_product_save_method == 'add')
            url = "<?php echo site_url('broker/Al_products/add_product'); ?>";
        else
            url = "<?php echo site_url('broker/Al_products/update_product'); ?>";
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
                        $('#modal_form_al_product').modal('hide');
                        if(newMaster)
                        {
                            getAlProduct("<?php echo site_url('broker/Al_products/get_product_dropdown'); ?>");
                        }
                        if(typeof al_product_table != 'undefined')
                        {
                            al_product_table.destroy();
                            al_product_table = ajax_al_list();
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
    function getAlProduct(url)
    {
        $.ajax({
            url: url,
            type:'post',
            data: {},
            dataType: 'json',
            success:function(data)
            {
                var option = '<option disabled selected value="">Select Product</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].product_id+">"+data[i].product_name+"</option>";
                });
                $("#al_product").html(option).select2("val", $("#al_product option:last-child").val()).change();
                $("#product_id").html(option).select2("val", $("#product_id option:last-child").val()).change();
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from asset and liability product',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

</script>