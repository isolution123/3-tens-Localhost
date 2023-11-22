<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">Family Master</li>
            </ol>
            <h1>Family Master</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Family Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Family or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
                              if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
                                ?>
                                <button class="btn btn-success" onclick="add_family(false)" tabindex="1"><i class="fa fa-plus"></i> Add Family</button>
                                <?php
                              }
                                else{
                                  ?>
                                  <button class="btn btn-success disable_btn" onclick="add_family(false)" tabindex="1"><i class="fa fa-plus"></i> Add Family</button>
                                  <?php
                                }
                                ?>
                            <br /><br />

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">
                                    <thead>
                                    <tr>
                                        <th class="action-col">Action</th>
                                        <th>Family ID</th>
                                        <th>Family Name</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="action-col"></th>
                                            <th>Family ID</th>
                                            <th>Family Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function ajax_family_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],

            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                            { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/families/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "family_id" },
                { "data": "name" },
                { "data": "status" }
            ]

        });
        return oTable;
    }
    $(document).ready(function(){

        table = ajax_family_list();
        table_initialize();
    });

    function edit_family(id)
    {
        //debugger;
        save_method = 'update';
        $('#family_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Families/edit_family');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                //debugger;
                $('[name="famID"]').val(data.family_id);
                $('[name="famName"]').val(data.name);
                $('[name="famStatus"]').val(data.status);
                $('#modal_family_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Family'); // Set title to Bootstrap modal title

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    function delete_family(id)
    {
        bootbox.confirm("Are you sure you want to delete this Family?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Families/delete_family');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Family Deleted!',
                                text: 'Family is deleted',
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            table.destroy();
                            table = ajax_family_list();
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
