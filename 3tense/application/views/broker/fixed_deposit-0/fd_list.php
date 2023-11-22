<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Fixed Deposit</li>
            </ol>
            <h1>Fixed Income Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Fixed_deposits/fd_report');?>">Fixed Income Details</a></li>
                            <li><a href="<?php echo base_url('broker/Fixed_deposits/interest_calendar_report');?>">Interest Calender</a></li>
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
                            <h4>Fixed Income Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Fixed Deposit or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Fixed_deposits/add_form'); ?>';"><i class="fa fa-plus"></i> Add Fixed Income</button>
<?php
}
else{
?>
  <button class="btn btn-success disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Fixed_deposits/add_form'); ?>';"><i class="fa fa-plus"></i> Add Fixed Income</button>
<?php
}
?>


                            <br /><br />
                            <div class="form-group" style="float:right;">
                                <label>Search Maturity Date</label>
                                <div>
                                    <input type="text" id="from" class="datepicker from mask" data-inputmask="'alias': 'date'" placeholder="Date From" />
                                    &nbsp;&nbsp;
                                    <input type="text" id="to" class="datepicker to mask" data-inputmask="'alias': 'date'" placeholder="Date To" />
                                </div>
                            </div><br/>
                            <div class="table-responsive" style="float: left; width: 100%;">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-2">Action</th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Investment Type</th>
                                        <th>Company Name</th>
                                        <?php /*<th>Interest Frequency</th>*/ ?>
                                        <th>Interest Mode</th>
                                        <th>Reference No.</th>
                                        <th>Date of Issue</th>
                                        <th>Amount Invested</th>
                                        <th>Interest Rate</th>
                                        <th>Maturity Date</th>
                                        <th>Maturity Amount</th>
                                        <th>Nominee</th>
                                        <th>Status</th>
                                        <th>Advisor Name</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Transaction Date</th>
                                        <th>Investment Type</th>
                                        <th>Company Name</th>
                                        <?php /*<th>Interest Frequency</th>*/ ?>
                                        <th>Interest Mode</th>
                                        <th>Reference No.</th>
                                        <th>Date of Issue</th>
                                        <th>Amount Invested</th>
                                        <th>Interest Rate</th>
                                        <th>Maturity Date</th>
                                        <th>Maturity Amount</th>
                                        <th>Nominee</th>
                                        <th>Status</th>
                                        <th>Advisor Name</th>
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
        table = ajax_fd_list();
        table_initialize();

        /* apply masks to inputs */
        $('.mask').inputmask();
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
    });

    function ajax_fd_list() {
        oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/fixed_deposits/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk" },
                { "data": "fd_inv_type" },
                { "data": "fd_comp_name" },
                { "data": "fd_method" },
                { "data": "ref_number" },
                { "data": "issued_date", "type": "date-uk" },
                { "data": "amount_invested" },
                { "data": "interest_rate" },
                { "data": "maturity_date", "type": "date-uk" },
                { "data": "maturity_amount" },
                { "data": "nominee" },
                { "data": "status" },
                { "data": "adviser_name" },
                //{ "data": "adjustment" }
            ]

        });
        return oTable;
    }

    //date between search
    // stop column from sorting when focused on datebox
    $('th #from, th #to').on('focus', function() {
        $(this).parent().unbind('click.DT');
    });
    // add listener again to column for sorting on unfocus
    $('th #from, th #to').on('blur', function() {
        table.order.listener($(this).parent(), $(this).parent().index());
    });
    // Event listener to the two range filtering inputs to redraw on input
    $('#from, #to').change( function() {
        table.draw();
    });
    $('#from, #to').keyup( function() {
        table.draw();
    });

    /* Custom filtering function which will search data in column between two values */
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var f = $('#from').val().split('/');
            var t = $('#to').val().split('/');
            var from = new Date(f[2],f[1]-1,f[0]);
            var to = new Date(t[2],t[1]-1,t[0]);

            //check if premium is selected or maturity (for search)
            var d = data[11].split('/');

            var date = new Date(d[2],d[1]-1,d[0]); // use data for the date column

            if( (from == "" && to == "" ) ||
                ( isNaN( from ) && isNaN( to ) ) ||
                ( isNaN( from ) && date <= to ) ||
                ( from <= date   && isNaN( to ) ) ||
                ( from <= date   && date <= to ) )
            {
                return true;
            }

            return false;
        }
    );




    function edit_fd(id)
    {
        location.href = '<?php echo base_url();?>broker/fixed_deposits/edit_form?id='+id;
    }

    function delete_fd(fd_id)
    {
        if(fd_id != null)
        {
            bootbox.confirm("Are you sure you want to delete this Fixed Deposit?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/fixed_deposits/delete_fd');?>',
                        type: 'post',
                        data: {fd_id: fd_id},
                        dataType: 'json',
                        success:function(data)
                        {
                            if(data['status'] == 'success')
                            {
                                $.pnotify({
                                    title: data['title'],
                                    text: data['text'],
                                    type: 'success',
                                    hide: true
                                });
                                oTable.destroy();
                                oTable = ajax_fd_list();
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
        else
            bootbox.alert("Please add Fixed Deposit details first");
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
