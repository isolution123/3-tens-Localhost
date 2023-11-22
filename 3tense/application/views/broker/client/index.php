<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">Client Master</li>
            </ol>
            <h1>Client Master</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Client Table</h4>

                        </div>
                        <div class="panel-body collapse in table-responsive">
                            <div class="alert alert-info">
                                Create a new Client or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
                            $brokerId=$this->session->userdata('broker_id');
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
                            <button class="btn btn-success" onclick="javascript:location.href='<?php echo base_url(); ?>broker/clients/add';"><i class="fa fa-plus"></i> Add Client</button>
                            <button class="btn btn-success" onclick="javascript:location.href='<?php echo base_url(); ?>broker/clients/merge';"><i class="fa fa-plus"></i> Merge Client</button>
                            <button class="btn btn-success" onclick="javascript:location.href='<?php echo base_url(); ?>broker/clients/family_transfer';"><i class="fa fa-plus"></i> Transfer Clients Family</button>
<?php
}
else{
?>
                            <button class="btn btn-success disable_btn" onclick="javascript:location.href='<?php echo base_url(); ?>broker/clients/add';"><i class="fa fa-plus"></i> Add Client</button>
<?php
}
?>


                            <br /><br />

                            <? // create an empty form for print client report ?>
                            <form id="report_form" method="get"></form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" id="table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-3">Action</th>
                                        <!--<th>Client ID</th> -->
                                        <th>Family</th>
                                        <th>Name</th>
                                        <th>Date of Birth </th>
                                        <th>PAN No</th>
                                        <th>Mobile No</th>
                                        <th>Email ID</th>
                                        <th>Aadhar Card</th>
                                        <th>Commence</th>
                                        <?php if($brokerId=='0004')
                                        {
                                            ?>
                                        
                                        <th>App Rights</th>
                                        <?php
                                        }?>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <!--<th>Client ID</th> -->
                                        <th>Family</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>PAN No</th>
                                        <th>Mobile No</th>
                                        <th>Email ID</th>
                                        <th>Aadhar Card</th>
                                        <th>Commence</th>
                                          <?php if($brokerId=='0004')
                                        {
                                            ?>
                                        
                                        <th>App Rights</th>
                                        <?php
                                        }?>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                                <div class="loading-data"><img src="<?php echo base_url('assets/users/img/load.gif');?>"><span>Please wait, loading data...</span></div>
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
    var brokerId='<?php echo $brokerId;?>';
    $(document).ready(function(){

        table = ajax_list();
        table_initialize();
    });
    function ajax_list()
    {
        /*$.ajax({
            //Load data for the table through ajax
            url: '<?php //echo site_url('broker/clients/ajax_list');?>',
            type: 'post',
            dataType: 'json',
            success:function(data)
            {
                $('.loading-data').remove();
                table.fnClearTable();
                for(var i = 0; i < data['data'].length; i++) {table.fnAddData([ data['data'][i]]); }
            },
            error:function(e)
            {
                $('.loading-data').remove();
                alert("Could not load data! Please refresh your page to try again.");
                console.log(e.responseText);
            }
        });*/
        var columnlist=[   { "data": "action" },
                { "data": "f_name" },
                { "data": "c_name" },
                { "data": "dob" , "type": "date-uk"},
                { "data": "pan_no" },
                { "data": "mobile" },
                { "data": "email_id" },
                { "data": "passport_no" },
                { "data": "date_of_comm" }];
        if(brokerId=='0004')
        {
            columnlist.push({
                "data" : "app_access"
            });
        }
        
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],

             /*//set column definitions
             "aoColumns":[
             null,
             null,
             null,
             null,
             null,
             null,
             null,
             null,
             null,
             null,
             {bSortable:false, bSearchable:false}
             ]*/
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_list');?>',
                "type": 'post'
            },
            "columns":columnlist
        });
        return oTable;
    }

    function edit_client(id)
    {
        //debugger;
        location.href = '<?php echo base_url();?>clients/edit?id='+id;
    }

    function delete_client(id)
    {
        bootbox.confirm('Are you sure you want to delete this client? Please note that client cannot be deleted if there is any data linked to the client.', function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Clients/delete_Client');?>",
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
                        //if success reload ajax table
                        $('#modal_form').modal('hide');
                        table.destroy();
                        table = ajax_list();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        bootbox.alert('Error deleting data');
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        });
    }

    function print_client(id)
    {
        var form = $('#report_form');
        form.attr('action', '<?php echo base_url();?>broker/clients/report');
        form.attr('target', '_blank');
        form.attr('method', 'post');
    
        form.html('<input type="hidden" name="id" value="'+id+'" />');
        form.submit();
        form.attr('action', '#'); //remove href of form
        form.html('');   //remove html content of form
    }
</script>
