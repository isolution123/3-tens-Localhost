<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Commodities</li>
            </ol>
            <h1>Commodities Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Commodities/commodity_report');?>">Commodities Details</a></li>
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
                            <h4>Commodities Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Add a new Commodity for client or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Commodities/add_form'); ?>';"><i class="fa fa-plus"></i> Add Commodity</button>
<?php
}
else{
?>
<button class="btn btn-success disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Commodities/add_form'); ?>';"><i class="fa fa-plus"></i> Add Commodity</button>
<?php
}
?>


                            <br /><br />

                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="commodity_table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Type</th>
                                        <th>Commodity Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Transaction Rate</th>
                                        <th>Current Rate</th>
                                        <th>Particulars</th>
                                        <th>Advisor Name</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Type</th>
                                        <th>Commodity Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Transaction Rate</th>
                                        <th>Current Rate</th>
                                        <th>Particulars</th>
                                        <th>Advisor Name</th>
                                        <th>Total Amount</th>
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
    var table;
    $(document).ready(function(){
        table = ajax_commodity_list();
        table_initialize();
    });

    function ajax_commodity_list() {
        var oTable = $("#commodity_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[3,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Commodities/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk" },
                { "data": "transaction_type" },
                { "data": "item_name" },
                { "data": "quantity" },
                { "data": "unit_name" },
                { "data": "transaction_rate" },
                { "data": "current_rate" },
                { "data": "quality" },
                { "data": "adviser_name" },
                { "data": "total_amount" }
            ]

        });
        return oTable;
    }

    function edit_commodity(id)
    {
        location.href = '<?php echo base_url();?>broker/Commodities/edit_form?id='+id;
    }

    function delete_commodity(id)
    {
        bootbox.confirm("Are you sure you want to delete this commodity?", function(result) {
            //debugger;
            if(result) {
                $.ajax({
                    url: "<?php echo site_url('broker/Commodities/delete_commodity');?>",
                    type:'post',
                    data: {id: id},
                    dataType: 'json',
                    success:function(data)
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            hide: true
                        });
                        if(data['deleted'] == true) {
                            window.location.href="<?php echo base_url('broker/Commodities');?>";
                        }
                    },
                    error: function (data)
                    {
                        console.log(data);
                        $.pnotify({
                            title: 'Error!',
                            text: 'Error deleting commodity item',
                            type: 'error',
                            hide: true
                        });
                    }
                });
            }
        });
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function ( a ) {
            var ukDatea = a.split('/');
            return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        },

        "date-uk-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "date-uk-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
</script>
