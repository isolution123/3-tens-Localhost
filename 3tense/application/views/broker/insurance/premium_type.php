<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li>Insurances</li>
                <li class="active">Asset Allocation</li>
            </ol>
            <h1>Asset Allocation Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Asset Allocation Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Asset Allocation or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button class="btn btn-success" tabindex="1" onclick="add_premium_type(false)"><i class="fa fa-plus"></i> Add Asset Allocation</button>
                            <br /><br />

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                <thead>
                                <tr>
                                    <th class="action-col-2">Action</th>
                                    <th>Premium ID</th>
                                    <th>Premium Name</th>
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


<script type="text/javascript">
    var save_method; //for save method string
    var prem_type_table;
    $(document).ready(function(){
        prem_type_table = ajax_premium_type_list();
    });

    function ajax_premium_type_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 1 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Premium_types/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "prem_type_id" },
                { "data": "prem_type_name" }
            ]

        });
        return oTable;
    }

    function edit_prem(id)
    {
        save_method = 'update';
        $('#premium_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Premium_types/edit_prem_type');?>",
            type: "POST",
            data:{id: id},
            dataType: "json",
            success: function(data)
            {
                $('[name="premTypeID"]').val(data.prem_type_id);
                $('[name="premTypeName"]').val(data.prem_type_name);
                $('#premium_master_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Premium Type'); // Set title to Bootstrap modal title

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    function delete_prem(id)
    {
        bootbox.confirm('Are you sure you want to delete this Premium Type?', function(result){
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Premium_types/delete_prem_type');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Asset Allocation Deleted!',
                                text: 'Asset Allocation Deleted from Insurance',
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            $('#premium_modal_form').modal('hide');
                            prem_type_table.destroy();
                            prem_type_table = ajax_premium_type_list();
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
                    error: function (data)
                    {
                        console.log(data);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        });
    }
</script>