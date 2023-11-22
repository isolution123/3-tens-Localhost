<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Withdraw Funds</li>
            </ol>
            <h1>Withdraw Funds Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Withdraw Funds Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Withdraw Funds or edit previous withdraw records.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Withdraw_funds/add_form'); ?>';"><i class="fa fa-plus"></i> Withdraw Funds</button>
<?php
}
else{
?>
<button class="btn btn-success disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Withdraw_funds/add_form'); ?>';"><i class="fa fa-plus"></i> Withdraw Funds</button>
<?php
}
?>


                            <br /><br />

                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Amount</th>
                                        <th>Cheque No.</th>
                                        <th>Cheque Date</th>
                                        <th>Bank Name</th>
                                        <th>Branch</th>
                                        <th>Account No.</th>
                                        <th>Withdraw from</th>
                                        <th>Broker Name</th>
                                        <th>Client Code</th>
                                        <th>MF Type</th>
                                        <th>Notes</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Amount</th>
                                        <th>Cheque No.</th>
                                        <th>Cheque Date</th>
                                        <th>Bank Name</th>
                                        <th>Branch</th>
                                        <th>Account No.</th>
                                        <th>Withdraw from</th>
                                        <th>Broker Name</th>
                                        <th>Client Code</th>
                                        <th>MF Type</th>
                                        <th>Notes</th>
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
        table = ajax_withdraw_funds_list();
        table_initialize();
    });

    function ajax_withdraw_funds_list() {
        return $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Withdraw_funds/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk" },
                { "data": "amount" },
                { "data": "cheque_no" },
                { "data": "cheque_date" },
                { "data": "bank_name" },
                { "data": "branch" },
                { "data": "account_number" },
                { "data": "withdraw_from" },
                { "data": "broker_name" },
                { "data": "client_code" },
                { "data": "mf_type" },
                { "data": "add_notes" }
            ]

        });
    }

    function edit_withdraw_fund(id)
    {
        location.href = '<?php echo base_url();?>broker/Withdraw_funds/edit_form?id='+id;
    }

    function delete_withdraw_fund(id)
    {
        bootbox.confirm("Are you sure you want to delete this Withdraw Fund entry?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Withdraw_funds/delete_fund');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });

                        table.destroy();
                        table = ajax_withdraw_funds_list();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        bootbox.alert('Error deleting data');
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
