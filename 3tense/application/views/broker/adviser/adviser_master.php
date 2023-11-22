<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">Advisor</li>
            </ol>
            <h1>Advisor Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Advisor Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Advisor or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
                            <button class="btn btn-success" onclick="add_adv(false)" tabindex="1"><i class="fa fa-plus"></i> Add Advisor</button>
<?php
}
else{
?>
                          <button class="btn btn-success disable_btn" onclick="add_adv(false)" tabindex="1"><i class="fa fa-plus"></i> Add Advisor</button>
<?php
}
?>

                            <br /><br />
                            <div>
                                <table cellpadding="0" cellspacing="0" border="0" tabindex="2" class="table table-striped table-bordered" id="table" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th class="action-col action-col-2">Action</th>
                                        <th>Advisor ID</th>
                                        <th>Advisor Name</th>
                                        <th>Company Name</th>
                                        <th>Product</th>
                                        <th>Agency Code</th>
                                        <th>Contact Person</th>
                                        <th>Contact Number</th>
                                        <th>Held Type</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col action-col-2"></th>
                                        <th>Advisor ID</th>
                                        <th>Advisor Name</th>
                                        <th>Company Name</th>
                                        <th>Product</th>
                                        <th>Agency Code</th>
                                        <th>Contact Person</th>
                                        <th>Contact Number</th>
                                        <th>Held Type</th>
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
    $(document).ready(function(){
        table = ajax_adv_list();
        table_initialize();
    });

    function ajax_adv_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 1 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/advisers/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "adviser_id" },
                { "data": "adviser_name" },
                { "data": "company_name" },
                { "data": "product" },
                { "data": "agency_code" },
                { "data": "contact_person" },
                { "data": "contact_number" },
                { "data": "held_type"}
            ]

        });
        return oTable;
    }

    function edit_adv(id)
    {
        save_method = 'update';
        $('#adviser_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/advisers/edit_adviser');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="advID"]').val(data.adviser_id);
                $('[name="advName"]').val(data.adviser_name);
                $('[name="advCompName"]').val(data.company_name);
                $('[name="advProduct"]').val(data.product);
                $('[name="advAgcCode"]').val(data.agency_code);
                $('[name="advConPerson"]').val(data.contact_person);
                $('[name="advConNumber"]').val(data.contact_number);

                $('#'+data.held_type).prop("checked",true);
                $('#modal_adviser_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Advisor'); // Set title to Bootstrap modal title


            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }
</script>
