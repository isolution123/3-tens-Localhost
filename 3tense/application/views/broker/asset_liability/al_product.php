<div class="page-content">
    <div id="wrap">
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard'); ?>">Dashboard</a></li>
                <li>Masters</li>
                <li>Asset and Liability</li>
                <li class="active">Product</li>
            </ol>
            <h1>Asset and Liability Product</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Asset and Liability Product Table</h4>
                            <div class="options">
                                <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Product or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1"  onclick="add_al_product(false)"><i class="fa fa-plus"></i> Add Asset and Liability Product</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" tabindex="1"  onclick="add_al_product(false)"><i class="fa fa-plus"></i> Add Asset and Liability Product</button>
<?php
}
?>

                            <br /><br />

                            <table border="0" class="table table-striped table-bordered" id="al_product_table">
                                <thead>
                                <tr>
                                    <th class="action-col-2">Action</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                </tr>
                                </thead>
                            </table><!--end table-->
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->

<script type="application/javascript">
    var al_product_save_method;
    var al_product_table;
    $(document).ready(function(){
        al_product_table = ajax_al_list();
    });

    function ajax_al_list()
    {
        al_product_table = $('#al_product_table').DataTable({
            "processing": true,
            "serverSide": false,
            "aaSorting": [[1, 'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "aTargets": [0]},
                {"bVisible": false, "aTargets": [1]}
            ],
            "ajax": {
                "url": '<?php echo site_url('broker/Al_products/ajax_al_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": 'action' },
                { "data": 'product_id' },
                { "data": 'product_name' }
            ]
        });
        return al_product_table;
    }

    function edit_al_product(id)
    {
        al_product_save_method = 'update';
        $("#al_product_form")[0].reset();

        $.ajax({
            url: "<?php echo site_url('broker/Al_products/edit_product');?>",
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function(data){
                $('#product_id').val(data.product_id);
                $('#product_name').val(data.product_name);
                $('#modal_form_al_product').modal('show');
                $("#modal_al_product_title").text('Edit Asset and Liability Product');
            },
            error: function(data){
                bootbox.alert("Something went terribly wrong");
                console.log(data);
            }
        });
    }

    function delete_al_product(id)
    {
        bootbox.confirm("Are you sure you want to delete this product?", function(result){
            if(result)
            {
                $.ajax({
                    url:"<?php echo site_url('broker/Al_products/delete_product');?>",
                    type: 'post',
                    data: {id: id},
                    dataType: 'json',
                    success: function(data){
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Product Deleted!',
                                text: 'Product Deleted from asset and liability',
                                type: 'success',
                                hide: true
                            });
                            al_product_table.destroy();
                            al_product_table = ajax_al_list();
                        }
                        else
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: 'error',
                                hide: true
                            });
                        }
                    },
                    error: function(data){
                        bootbox.alert("Something went terribly wrong");
                        console.log(data);
                    }
                });
            }
        });
    }
</script>
