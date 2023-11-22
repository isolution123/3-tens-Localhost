<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li>Insurances</li>
                <li class="active">Insurance Companies</li>
            </ol>
            <h1>Insurance Company Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Insurance Company Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Insurance Company or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button class="btn btn-success" tabindex="1"  onclick="add_ins_comp()"><i class="fa fa-plus"></i> Add Insurance Company</button>
                            <br /><br />

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="prem_type_table">
                                <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Company ID</th>
                                        <th>Company Name</th>
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
    var ins_comp_table;
    $(document).ready(function(){
        ins_comp_table = ajax_ins_comp_list();
    });

    function ajax_ins_comp_list() {
        var oTable = $("#prem_type_table").DataTable({
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
                "url": '<?php echo site_url('broker/Insurance_companies/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "ins_comp_id" },
                { "data": "ins_comp_name" }
            ]

        });
        return oTable;
    }

    function edit_ins_comp(id)
    {
        save_method = 'update';
        $('#ins_comp_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Insurance_companies/edit_ins_comp');?>",
            type: "POST",
            data:{id: id},
            dataType: "json",
            success: function(data)
            {
                $('[name="insCompID"]').val(data.ins_comp_id);
                $('[name="insCompName"]').val(data.ins_comp_name);
                $('#modal_form_ins_comp').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Insurance Company'); // Set title to Bootstrap modal title

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    function delete_ins_comp(id)
    {
        bootbox.confirm("Are you sure you want to delete this Insurance Company?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Insurance_companies/delete_ins_comp');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Company Deleted!',
                                text: 'Company Deleted from Insurance',
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            ins_comp_table.destroy();
                            ins_comp_table = ajax_ins_comp_list();
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