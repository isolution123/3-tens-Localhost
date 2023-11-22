<div id="page-content">

    <div id='wrap'>

        <div id="page-heading">

            <ol class="breadcrumb">

                <li><a href="index.php">Dashboard</a></li>

                <li>Masters</li>

                <li class="active">Scheme Trigger</li>

            </ol>

            <h1>Scheme Trigger</h1>



        </div>

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-indigo">

                        <div class="panel-heading">

                            <h4>Scheme Trigger</h4>



                        </div>

                        <div class="panel-body collapse in">

                            <div class="alert alert-info">

                                 simply select a row to edit the data.

                                <button type="button" class="close" data-dismiss="alert">&times;</button>

                            </div>


                            <br /><br />



                            <div class="table-responsive">

                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">

                                    <thead>

                                    <tr>

                                        <th class="action-col">Action</th>

                                        <th>Scheme Trigger</th>

                                        <th>Scheme Target Value</th>

                                    </tr>

                                    </thead>

                                    <tfoot>

                                        <tr>

                                            <th class="action-col"></th>

                                            <th>Scheme Trigger</th>

                                            <th>Scheme Target Value</th>

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

                "url": '<?php echo site_url('broker/Scheme_Trigger/ajax_list');?>',

                "type": 'post'

            },

            "columns": [

                { "data": "action" },

                { "data": "scheme_type" },

                { "data": "scheme_target_value" }

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

            url : "<?php echo site_url('broker/Scheme_Trigger/edit_family');?>",

            type: "POST",

            data:{id: id},

            dataType: "JSON",

            success: function(data)

            {
                
                //debugger;

                $('[name="scheme_type_id"]').val(data.scheme_type_id);

                $('[name="scheme_type"]').val(data.scheme_type);

                $('[name="scheme_target_value"]').val(data.scheme_target_value);

                $('#modal_scheme_type_form').modal('show'); // show bootstrap modal when complete loaded

                $('.modal-title').text('Edit Scheme Trigger'); // Set title to Bootstrap modal title



            },

            error: function (data)

            {

                console.log(data);

                bootbox.alert("Something went terribly wrong");

            }

        });

    }



</script>

