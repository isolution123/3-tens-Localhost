<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Equity</li>
            </ol>
            <h1>Equity Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    
                            <a class="btn btn-default ladda-button" href="<?php echo base_url('broker/Equity/equity_report');?>">Equity/Shares</a>
                        
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Equity Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Equity or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Equity/add_form'); ?>';"><i class="fa fa-plus"></i> Add Equity</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Equity/add_form'); ?>';"><i class="fa fa-plus"></i> Add Equity</button>
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
                                        <th>Broker Name</th>
                                        <th>Client Code</th>
                                        <th>Transaction Date</th>
                                        <th>Scrip Name</th>
                                        <th>Scrip Code</th>
                                        <th>Quantity</th>
                                        <?php if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0204')
                                        {?>
                                        <th>Purchase Price</th>
                                        <th>Purchase Value</th>
                                        <th>Current Price</th>
                                        <th>Current Value</th>
                                        <?php } ?>
                                        <th>Acquiring Rate</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Broker Name</th>
                                        <th>Client Code</th>
                                        <th>Transaction Date</th>
                                        <th>Scrip Name</th>
                                        <th>Scrip Code</th>
                                        <th>Quantity</th>
                                        <?php if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0204')
                                        {?>
                                      <th>Purchase Price</th>
                                        <th>Purchase Value</th>
                                        <th>Current Price</th>
                                        <th>Current Value</th>
                                        <?php } ?>
                                        <th>Acquiring Rate</th>
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
    var broker_Id='<?php echo $this->session->userdata('broker_id');?>';
    $(document).ready(function(){
        table = ajax_equity_list();
        table_initialize();
    });

    function ajax_equity_list() {
        var col=[
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "broker_name" },
                { "data": "client_code" },
                { "data": "transaction_date" },
                { "data": "scrip_name" },
                { "data": "scrip_code" },
                { "data": "quantity" }
            ];
        if(broker_Id=='0004' || broker_Id=='0204')
        {
            col.push({ "data": "apc" });
            col.push({ "data": "purchase_value" });
            col.push({ "data": "close_rate" });
            col.push({ "data": "current_value" });
                
        }
        col.push({ "data": "acquiring_rate" });
        return $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/equity/ajax_list');?>',
                "type": 'post'
            },
            "columns":col

        });
    }

    function edit_equity(id)
    {
        location.href = '<?php echo base_url();?>broker/equity/edit_form?id='+id;
    }

    function delete_equity(id)
    {
        bootbox.confirm("Are you sure you want to delete this equity?", function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Equity/delete_equity');?>",
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
                        table = ajax_equity_list();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
</script>
