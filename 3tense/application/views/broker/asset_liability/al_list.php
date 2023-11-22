<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Assets and Liabilities</li>
            </ol>
            <h1>Assets and Liabilities Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Assets_liabilities/al_report');?>">Assets and Liabilities Details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Assets and Liabilities Table</h4>
                            <div class="options">
                                <a href="javascript:void(0);" class="panel-collapse"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Assets and Liabilities or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="location.href='<?php echo base_url('broker/Assets_liabilities/add_form'); ?>';"><i class="fa fa-plus"></i> Add Assets or Liabilities</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" tabindex="1" onclick="location.href='<?php echo base_url('broker/Assets_liabilities/add_form'); ?>';"><i class="fa fa-plus"></i> Add Assets or Liabilities</button>
<?php
}
?>

                            <br /><br />


                            <div class="table-responsive">
                                <table border="0" class="table table-striped table-bordered" id="asset_table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Product Name</th>
                                        <th>Company Name</th>
                                        <th>Scheme Name</th>
                                        <th>Type</th>
                                        <th>Goal</th>
                                        <th>Ref. Number</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Installment Amount</th>
                                        <th>Rate of Return</th>
                                        <th>Expected Maturity value</th>
                                        <th>Advisor Feeds</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Product Name</th>
                                        <th>Company Name</th>
                                        <th>Scheme Name</th>
                                        <th>Type</th>
                                        <th>Goal</th>
                                        <th>Ref. Number</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Installment Amount</th>
                                        <th>Rate of Return</th>
                                        <th>Expected Maturity value</th>
                                        <th>Advisor Feeds</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var save_method; //for save method string
    var oTable;
    $(document).ready(function(){
        table = ajax_al_list();
        table_initialize();
    });

    function ajax_al_list() {
        oTable = $("#asset_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Assets_liabilities/asset_ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "product_name"},
                { "data": "company_name" },
                { "data": "scheme_name" },
                { "data": "type_name" },
                { "data": "goal" },
                { "data": "ref_number" },
                { "data": "start_date", "type": "date-uk" },
                { "data": "end_date", "type": "date-uk" },
                { "data": "installment_amount" },
                { "data": "rate_of_return" },
                { "data": "expected_mat_value" },
                { "data": "narration" }
            ]

        });
        return oTable;
    }

    function edit_re(id)
    {
        location.href = '<?php echo base_url();?>broker/Assets_liabilities/edit_form?id='+id;
    }

    function delete_asset(al_id)
    {
        if(al_id != null)
        {
            bootbox.confirm("Are you sure you want to delete this Asset?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/Assets_liabilities/delete_asset_liability');?>',
                        type: 'post',
                        data: {al_id: al_id},
                        dataType: 'json',
                        success:function(data)
                        {
                            if(data['type'] == 'success')
                            {
                                $.pnotify({
                                    title: data['title'],
                                    text: data['text'],
                                    type: 'success',
                                    hide: true
                                });
                                oTable.destroy();
                                oTable = ajax_re_list();
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
                        error:function(data)
                        {
                            console.log(data);
                        }
                    });
                }
            });
        }
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function ( a ) {
            var ukDatea = a.split('/');
            return (ukDatea[2] + ukDatea[1] + ukDatea[0]);
        },

        "date-uk-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "date-uk-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
</script>
