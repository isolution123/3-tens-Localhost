<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Last Imports</li>
            </ol>
            <h1>Last Imports</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Last Imports Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                View the details of the latest imported files.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <br /><br />

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="last_import_table">
                                    <thead>
                                    <tr>
                                        <th>Import Type</th>
                                        <th>Last Import Date/Time</th>
                                        <th>Imported File Name</th>
                                        <th>By User</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Import Type</th>
                                        <th>Last Import Date/Time</th>
                                        <th>Imported File Name</th>
                                        <th>By User</th>
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
    var table;

    $(document).ready(function(){
        table = ajax_last_imports_list();
        table_initialize();
    });

    function ajax_last_imports_list() {
        var oTable = $("#last_import_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],

            /*"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],*/
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Users/ajax_list_last_imports');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "import_type" },
                { "data": "last_import_date", "type": "date-euro" },
                { "data": "file_name" },
                { "data": "name" }
            ]

        });
        return oTable;
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-euro-pre": function ( a ) {
            var x;

            if ( $.trim(a) !== '' ) {
                var frDatea = $.trim(a).split(' ');
                var frTimea = (undefined != frDatea[1]) ? frDatea[1].split(':') : [00,00,00];
                var frDatea2 = frDatea[0].split('/');
                x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
            }
            else {
                x = Infinity;
            }

            return x;
        },

        "date-euro-asc": function ( a, b ) {
            return a - b;
        },

        "date-euro-desc": function ( a, b ) {
            return b - a;
        }
    } );
</script>