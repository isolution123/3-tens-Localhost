<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li>Fixed Deposit</li>
                <li class="active">Companies</li>
            </ol>
            <h1>Fixed Deposit Company Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Fixed Deposit Company Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Fixed Deposit Company or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button class="btn btn-success" tabindex="1" onclick="add_fd_comp(false)"><i class="fa fa-plus"></i> Add Fixed Deposit Company</button>
                            <br /><br />

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                <thead>
                                <tr>
                                    <th class="action-col action-col-2">Action</th>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th class="action-col action-col-2"></th>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                </tr>
                                </tfoot>
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
    var table;
    $(document).ready(function(){
        table = ajax_fd_comp_list();
        table_initialize();
    });

    function ajax_fd_comp_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 1 ] }
            ],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Fd_companies/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "fd_comp_id" },
                { "data": "fd_comp_name" }
            ]
        });
        return oTable;
    }

    function edit_fd_comp(id)
    {
        save_method = 'update';
        $('#comp_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Fd_companies/edit_fd_comp');?>",
            type: "POST",
            data:{id: id},
            dataType: "json",
            success: function(data)
            {
                $('[name="fdCompID"]').val(data.fd_comp_id);
                $('[name="fdCompName"]').val(data.fd_comp_name);
                $('#comp_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Fixed Deposit Company'); // Set title to Bootstrap modal title

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    function delete_fd_comp(id)
    {
        bootbox.confirm("Are you sure you want to delete this Fixed Deposit Company?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Fd_companies/delete_fd_comp');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Company Deleted!',
                                text: 'Company Deleted from Fixed Deposit',
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            $('#comp_modal_form').modal('hide');
                            table.destroy();
                            table = ajax_fd_comp_list();
                            table_initialize();
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