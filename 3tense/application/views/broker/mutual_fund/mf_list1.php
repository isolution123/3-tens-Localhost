<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Mutual Fund</li>
            </ol>
            <h1>Mutual Fund Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Mutual_funds/mf_report');?>">Mutual Fund Valuation</a></li>
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
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#valuation" data-toggle="tab">Mutual Fund Valuations</a></li>
                                <li><a href="#transaction" data-toggle="tab">Mutual Fund Transactions</a></li>
                            </ul>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="tab-content">
                                <div class="tab-pane form-no-border" id="transaction">
                            <div class="alert alert-info">
                                Create a new Mutual Fund or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Mutual_funds/add_form'); ?>';"><i class="fa fa-plus"></i> Add Mutual Fund</button>
<?php
}
else{
?>
<button class="btn btn-success disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Mutual_funds/add_form'); ?>';"><i class="fa fa-plus"></i> Add Mutual Fund</button>
<?php
}
?>


                            <br /><br />
                            <div class="form-group" style="float:right;">
                                <label>Search Transaction Date</label>
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
                                        <th>Scheme Name</th>
                                        <th>Scheme Type</th>
                                        <!--<th>Mutual Fund Type</th>-->
                                        <th>Transaction Type</th>
                                        <th>Folio Number</th>
                                        <th>Transaction Date</th>
                                        <th>Units</th>
                                        <th>NAV</th>
                                        <th>Amount</th>
                                        <?php /*<th>Orig. TR Type</th>*/ ?>
                                        <th>Product Code</th>
                                        <th>Adj Ref. No.</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Scheme Name</th>
                                        <th>Scheme Type</th>
                                        <!--<th>Mutual Fund Type</th>-->
                                        <th>Transaction Type</th>
                                        <th>Folio Number</th>
                                        <th>Transaction Date</th>
                                        <th>Units</th>
                                        <th>NAV</th>
                                        <th>Amount</th>
                                        <?php /*<th>Orig. TR Type</th>*/ ?>
                                        <th>Product Code</th>
                                        <th>Adj Ref. No.</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                        <div class="tab-pane active form-no-border" id="valuation">
                            <br /><br />
                            <div class="form-group" style="float:right;">
                                <label>Search Transaction Date</label>
                                <div>
                                    <input type="text" id="vfrom" class="datepicker from mask" data-inputmask="'alias': 'date'" placeholder="Date From" />
                                    &nbsp;&nbsp;
                                    <input type="text" id="vto" class="datepicker to mask" data-inputmask="'alias': 'date'" placeholder="Date To" />
                                </div>
                            </div><br/>
                            <div class="table-responsive" style="float: left; width: 100%;">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table_val">
                                    <thead>
                                    <tr>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Folio Number</th>
                                        <th>Scheme Name</th>
                                        <th>Scheme Type</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Div Amount</th>
                                        <th>Pur NAV</th>
                                        <th>Live Units</th>
                                        <th>Cur NAV Date</th>
                                        <th>Trans Days</th>
                                        <th>Cur NAV</th>
                                        <th>Current Value</th>
                                        <th>Div R</th>
                                        <th>Div Payout</th>
                                        <th>CAGR</th>
                                        <th>ABS</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Folio Number</th>
                                        <th>Scheme Name</th>
                                        <th>Scheme Type</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Div Amount</th>
                                        <th>Pur NAV</th>
                                        <th>Live Units</th>
                                        <th>Cur NAV Date</th>
                                        <th>Trans Days</th>
                                        <th>Cur NAV</th>
                                        <th>Current Value</th>
                                        <th>Div R</th>
                                        <th>Div Payout</th>
                                        <th>CAGR</th>
                                        <th>ABS</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
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
        table = ajax_mf_list();
        //table_initialize();
        table_val = ajax_mf_list_val();
        table_initialize();

        /* apply masks to inputs */
        $('.mask').inputmask();
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
    });

    function ajax_mf_list() {
        var from = $("#from").val();
        var to = $("#to").val();
        oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "lengthMenu": [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]],
            "searchDelay": 500,
            //"aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            /*"ajax": {
                //Load data for the table through ajax
                "url": '<?php //echo site_url('broker/mutual_funds/ajax_list');?>',
                "type": 'post'
            },*/
            "sAjaxSource": '<?php echo site_url('broker/mutual_funds/ajax_list');?>?from='+from+'&to='+to,
            "columns": [
                { "data": "action" },
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "scheme_name" },
                { "data": "scheme_type" },
                { "data": "mutual_fund_type" },
                /*{ "data": "transaction_type" },*/
                { "data": "folio_number" },
                { "data": "purchase_date", "type": "date-uk" },
                { "data": "quantity" },
                { "data": "nav" },
                { "data": "amount" },
                //{ "data": "bal_old" },
                { "data": "prod_code" },
                { "data": "adjustment_ref_number" }
            ]

        });
        return oTable;
    }

    //date between search
    // Event listener to the two range filtering inputs to redraw on input
    $("#from, #to").change( function() {
        table=ajax_mf_list();
    });
    $("#from, #to").keyup( function() {
        table=ajax_mf_list();
    });

    function ajax_mf_list_val() {
        var vfrom = $("#vfrom").val();
        var vto = $("#vto").val();
        oTable = $("#table_val").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "lengthMenu": [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]],
            "searchDelay": 500,
            //"aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            //"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
            //                  { "bSearchable": false, "aTargets": [ 0 ] }],
            //    ],
            /*"ajax": {
                //Load data for the table through ajax
                "url": '<?php //echo site_url('broker/mutual_funds/ajax_list_valuation');?>',
                "type": 'post'
				/*"success": function(data) {
					console.log(data);
				},
				"error": function(data) {
					console.log("error");
					console.log(data);
				}*/
            /*},*/
            "sAjaxSource": '<?php echo site_url('broker/mutual_funds/ajax_list_valuation');?>?from='+vfrom+'&to='+vto,
            "columns": [
                { "data": "family_name" },
                { "data": "client_name" },
                { "data": "folio_number" },
                { "data": "mf_scheme_name" },
                { "data": "scheme_type" },
                { "data": "purchase_date", "type": "date-uk" },
                { "data": "trn_type" },
                { "data": "p_amount" },
                { "data": "div_amount" },
                { "data": "p_nav" },
                { "data": "live_unit" },
                { "data": "c_nav_date", "type": "date-uk" },
                { "data": "transaction_day" },
                { "data": "c_nav" },
                { "data": "current_value" },
                { "data": "div_r2" },
                { "data": "div_payout" },
                { "data": "cagr" },
                { "data": "mf_abs" }


            ]
        });
        return oTable;
    }

    //date between search
    // Event listener to the two range filtering inputs to redraw on input
    $("#vfrom, #vto").change( function() {
        table_val=ajax_mf_list_val();
    });
    $("#vfrom, #vto").keyup( function() {
        table_val=ajax_mf_list_val();
    });

    //date between search - for Transactions table
    // stop column from sorting when focused on datebox
    $('th #from, th #to').on('focus', function() {
        $(this).parent().unbind('click.DT');
    });
    // add listener again to column for sorting on unfocus
    $('th #from, th #to').on('blur', function() {
        table.order.listener($(this).parent(), $(this).parent().index());
    });
    /*// Event listener to the two range filtering inputs to redraw on input
    $('#from, #to').change( function() {
       table.draw();
    });
    $('#from, #to').keyup( function() {
        table.draw();
    });*/


    //date between search - for Transactions table
    // stop column from sorting when focused on datebox
    $('th #vfrom, th #vto').on('focus', function() {
        $(this).parent().unbind('click.DT');
    });
    // add listener again to column for sorting on unfocus
    $('th #vfrom, th #vto').on('blur', function() {
        table_val.order.listener($(this).parent(), $(this).parent().index());
    });
    /*// Event listener to the two range filtering inputs to redraw on input
    $('#vfrom, #vto').change( function() {
        table_val.draw();
    });
    $('#vfrom, #vto').keyup( function() {
        table_val.draw();
    });*/


    /* Custom filtering function which will search data in column between two values */
    /*$.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            if(settings['sInstance'] == "table_val") {
                var f = $('#vfrom').val().split('/');
                var t = $('#vto').val().split('/');
                var d = data[5].split('/');
            } else {
                var f = $('#from').val().split('/');
                var t = $('#to').val().split('/');
                var d = data[7].split('/');
            }
            /*if($('#from').val() != "") {
                var f = $('#from').val().split('/');
            } else if($('#vfrom').val() != "") {
                var f = $('#vfrom').val().split('/');
            } else {
                var f = "";
            }
            if($('#to').val() != "") {
                var t = $('#to').val().split('/');
            } else if($('#vto').val() != "") {
                var t = $('#vto').val().split('/');
            } else {
                var t = "";
            }*/
            /*var from = new Date(f[2],f[1]-1,f[0]);
            var to = new Date(t[2],t[1]-1,t[0]);

            //check if premium is selected or maturity (for search)
            //var d = data[7].split('/');

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
    );*/



    function edit_mf(id)
    {
        location.href = '<?php echo base_url();?>broker/mutual_funds/edit_form?id='+id;
    }

    function delete_mf(mf_id)
    {
        if(mf_id != null)
        {
            bootbox.confirm("Are you sure you want to delete this Mutual Fund?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/mutual_funds/delete_mf');?>',
                        type: 'post',
                        data: {mf_id: mf_id},
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
                                oTable = ajax_mf_list();
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
            bootbox.alert("Please add Mutual Fund details first");
    }

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function ( a ) {
            if(a != '' && a != null) {
                var ukDatea = a.split('/');
                return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
            }
        },

        "date-uk-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "date-uk-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
</script>
