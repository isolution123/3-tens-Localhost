<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Masters</li>
                <li>Commodity</li>
                <li class="active">Rates</li>
            </ol>
            <h1>Commodity Rates Master</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Commodity Rates Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Add a new Commodity Rate or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" onclick="add_commodity_rate()"><i class="fa fa-plus"></i> Add Commodity Rate</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" onclick="add_commodity_rate()"><i class="fa fa-plus"></i> Add Commodity Rate</button>
<?php
}
?>


                            <br /><br />

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="commodity_rate_table">
                                    <thead>
                                    <tr>
                                        <th class="action-col action-col-2">Action</th>
                                        <th>Commodity Item Name</th>
                                        <th>Commodity Unit</th>
                                        <th>Commodity Rate</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col action-col-2"></th>
                                        <th>Commodity Item Name</th>
                                        <th>Commodity Unit</th>
                                        <th>Commodity Rate</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'commodity_rate_add.php'; ?>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var table;

    $(document).ready(function(){
        table = ajax_commodity_rates_list();
        table_initialize();
    });

    function ajax_commodity_rates_list() {
        var oTable = $("#commodity_rate_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[0,'desc']],

            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Commodity_rates/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "item_name" },
                { "data": "unit_name" },
                { "data": "current_rate" }
            ]

        });
        return oTable;
    }

    function delete_commodity_rate(id)
    {
        bootbox.confirm("Are you sure you want to delete this Commodity Item Rate?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Commodity_rates/delete_commodity_rate');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#commodity_rate_modal_form').modal('hide');

                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });

                        table.destroy();
                        table = ajax_commodity_rates_list();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
</script>
