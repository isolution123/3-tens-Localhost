<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Mutual Funds</li>
                <li class="active">Mutual Fund Schemes</li>
            </ol>
            <h1>Mutual Fund Schemes</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Mutual Fund Schemes Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="mf_schemes_table">
                                    <thead>
                                    <tr>
                                        <th>Scheme Name</th>
                                        <th>NAV</th>
                                        <th>Scheme Type</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Scheme Name</th>
                                        <th>NAV</th>
                                        <th>Scheme Type</th>
                                        <th>Date</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php //include 'occupation_add.php'; ?>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var table;

    $(document).ready(function(){
        table = ajax_mf_schemes_list();
        table_initialize();
    });

    function ajax_mf_schemes_list() {
        var oTable = $("#mf_schemes_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[0,'asc']],

            /*"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],*/
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Mutual_fund_schemes/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "scheme_name" },
                { "data": "current_nav" },
                { "data": "scheme_type" },
                { "data": "scheme_date" }
            ]

        });
        return oTable;
    }

</script>