<!-- Bootstrap modal -->
<div class="modal fade" id="commodity_rate_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="commodity_rate_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="commodity_rate_form" class="form-horizontal">
                    <div class="form-body">
                        <input name="commodity_rate_id" id="rateID" type="hidden">
                        <div class="form-group">
                            <label class="control-label col-md-3">Commodity Item</label>
                            <div class="col-md-9 add-new-btn">
                                <select name="item_id" class="populate" required="required" id="rateItemID" tabindex="1" style="width: 80%">
                                    <option disabled selected value="">Select Commodity Item</option>
                                    <?php foreach($items as $row):?>
                                        <option value='<?php echo $row->item_id; ?>'><?php echo $row->item_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" title="Add Commodity Item" tabindex="2" onclick="add_commodity_item(true)"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Commodity Unit</label>
                            <div class="col-md-9 add-new-btn">
                                <select name="unit_id" class="populate" required="required" id="rateUnitID" tabindex="3" style="width: 40%;">
                                    <option disabled selected value="">Select Unit</option>
                                    <?php foreach($units as $row):?>
                                        <option value='<?php echo $row->unit_id; ?>'><?php echo $row->unit_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="javascript:;" class="btn btn-xs btn-inverse-alt" title="Add Commodity Unit" tabindex="4" onclick="add_commodity_unit(true)"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Commodity Rate</label>
                            <div class="col-md-9">
                                <input name="current_rate" id="rate" placeholder="Commodity Rate" required class="form-control" type="text" tabindex="5">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_commodity_rate()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    /*$("#rateItemID").select2();
    $("#rateUnitID").select2();*/
    $(".populate").select2();

    function add_commodity_rate()
    {
        if($("#rateItemID").length || $("#rateUnitID").length) {
            $("#rateItemID").select2('val','');
            $("#rateUnitID").select2('val','');
        }

        save_method = 'add';
        //reset form on modals
        $("#commodity_rate_form")[0].reset();
        //show bootstrap modal
        $("#commodity_rate_modal_form").modal('show');
        //set title to modal
        $("#commodity_rate_title").text('Add Commodity Rate');
    }

    function edit_commodity_rate(id)
    {
        //debugger;
        save_method = 'update';
        $('#commodity_rate_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Commodity_rates/edit_commodity_rate');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $("#rateItemID").select2();
                $("#rateUnitID").select2();
                $("#rateID").val(data.commodity_rate_id);
                $('[name="item_id"]').select2("val", data.item_id);
                $('[name="unit_id"]').select2("val", data.unit_id);
                $('[name="current_rate"]').val(data.current_rate);
                $('#commodity_rate_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#commodity_rate_title').text('Edit Commodity Rate'); // Set title to Bootstrap modal title


            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error getting data from ajax');
            }
        });
    }

    function save_commodity_rate()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Commodity_rates/add_commodity_rate');?>";
        else
            url = "<?php echo site_url('broker/Commodity_rates/update_commodity_rate');?>";

        var valid = $('#commodity_rate_form').parsley( 'validate' );
        if(valid) {
            var itemID = $("#rateItemID").val();
            $("#rateItemID").removeClass('has-error');
            var unitID = $("#rateUnitID").val();
            $("#rateUnitID").removeClass('has-error');
            var rate = $("#rate").val();
            $("#rate").removeClass('has-error');

            $.ajax({
                url: url,
                type:'post',
                data: $("#commodity_rate_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#commodity_rate_modal_form").modal('hide');

                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    if(data['type'] ==  "success") {
                        // if there is select box and it needs refreshing
                        if($("#current_rate").length) {
                            $("#current_rate").empty();
                            $("#current_rate").val(data['comm_data'].current_rate);
                        }
                        if($("#commodity_unit_id").length) {
                            get_item_units(data['comm_data'].item_id);
                            //$("#commodity_unit_id").select2("val",data['comm_data'].unit_id).change();
                        }
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_commodity_rates_list();
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert('Error adding / updating data');
                }
            });
        }
        /*else
        {
            alert('Please add proper details');
        }*/
    }
</script>

<?php include 'commodity_item_add.php'; ?>
<?php include 'commodity_unit_add.php'; ?>