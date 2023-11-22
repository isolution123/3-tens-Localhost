<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">Banks Master</li>
            </ol>
            <h1>Banks Master</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Banks Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Add a new Bank or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
  <button class="btn btn-success" onclick="add_bank()"><i class="fa fa-plus"></i> Add Bank</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" onclick="add_bank()"><i class="fa fa-plus"></i> Add Bank</button>
<?php
}
?>


                            <br /><br />

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="bank_table">
                                    <thead>
                                    <tr>
                                        <th class="action-col action-col-2">Action</th>
                                        <th>Bank Name</th>
                                    </tr>
                                    </thead>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'bank_add.php'; ?>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var table;

    $(document).ready(function(){
        table = ajax_banks_list();
        //table_initialize();
    });

    function ajax_banks_list() {
        var oTable = $("#bank_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[0,'desc']],

            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Banks/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "bank_name" }
            ]

        });
        return oTable;
    }

    function delete_bank(id)
    {
        bootbox.confirm("Are you sure you want to delete this Bank?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Banks/delete_bank');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#bank_modal_form').modal('hide');

                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });

                        table.destroy();
                        table = ajax_banks_list();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
</script>
