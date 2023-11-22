<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li class="active">Manage Users</li>
            </ol>
            <h1>Manage Users</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Users List Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Add a new User or Edit details of existing Users.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button class="btn btn-success" onclick="add_user()"><i class="fa fa-plus"></i> Add New User</button>
                            <br /><br />

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="users_table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Email ID</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Additional Info</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Email ID</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Additional Info</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'user_add.php'; ?>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var table;

    $(document).ready(function(){
        table = ajax_users_list();
        table_initialize();
    });

    function ajax_users_list() {
        var oTable = $("#users_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[0,'desc']],

            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Users/ajax_list_users');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "name" },
                { "data": "mobile" },
                { "data": "email_id" },
                { "data": "username" },
                { "data": "status" },
                { "data": "last_login" },
                { "data": "add_info" }
            ]

        });
        return oTable;
    }
</script>