<div id="page-content">
    <div id='wrap'>
        
         <div id="page-heading">

            <ol class="breadcrumb">

                <li><a href="index.php">Dashboard</a></li>

                <li>Mutual Funds</li>

                <li class="active">Mutual Fund Duplicate Transaction</li>

            </ol>

            <h1>Mutual Fund Duplicate Transaction</h1>

        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                         <div class="panel-heading">
                            <h4>Mutual Fund Duplicate Transaction</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="tab-content">
                                <div class="tab-pane active form-no-border" id="transaction">
                            
                            
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
                                        <th>Transcation No.</th>
                                        <th>Source</th>
                                        <th>Import Date</th>
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
                                        <th>Transcation No.</th>
                                        <th>Source</th>
                                        <th>Import Date</th>
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
    var broker_id;
    $(document).ready(function(){
        table = ajax_mf_list();
        broker_id='<?php echo $this->session->userdata('broker_id');?>';
        table_initialize();

        /* apply masks to inputs */
        $('.mask').inputmask();
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
       
       $('#transaction .dataTables_filter input')
        .unbind('keyup')
        .bind('keyup', function(e){
            if (e.which == 13 ) {    
                $('#table').dataTable().fnFilter($(this).val());
            }
        });
       
      
         $('#valuation .dataTables_filter input')
        .unbind('keyup')
        .bind('keyup', function(e){
            if (e.which == 13) {
                $('#table_val').dataTable().fnFilter($(this).val());
            }
        });
        
        
    });

    function ajax_mf_list() {
        var from = $("#from").val();
        var to = $("#to").val();
        oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "lengthMenu": [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]],
            "searchDelay": 5000,
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "sAjaxSource": '<?php echo site_url('broker/mutual_funds/ajax_duplicate_list');?>?from='+from+'&to='+to,
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
                { "data": "adjustment_ref_number" },
                { "data": "from_file" },
                { "data": "added_on" , "type": "date-uk" },
                
                
                
                
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

   

    //date between search - for Transactions table
    // stop column from sorting when focused on datebox
    $('th #from, th #to').on('focus', function() {
        $(this).parent().unbind('click.DT');
    });
    // add listener again to column for sorting on unfocus
    $('th #from, th #to').on('blur', function() {
        table.order.listener($(this).parent(), $(this).parent().index());
    });
   

    //date between search - for Transactions table
    // stop column from sorting when focused on datebox
    $('th #vfrom, th #vto').on('focus', function() {
        $(this).parent().unbind('click.DT');
    });
    // add listener again to column for sorting on unfocus
    $('th #vfrom, th #vto').on('blur', function() {
        table_val.order.listener($(this).parent(), $(this).parent().index());
    });
  



    function edit_mf(id)
    {
        location.href = '<?php echo base_url();?>broker/mutual_funds/edit_form?id='+id;
    }

    function delete_mf(mf_id,client_id)
    {
        if(mf_id != null)
        {
            bootbox.confirm("Are you sure you want to delete this Mutual Fund?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/mutual_funds/delete_mf');?>',
                        type: 'post',
                        data: {mf_id: mf_id,client_id:client_id},
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
    
    function ok_mf(mf_id)
    {
        if(mf_id != null)
        {
            bootbox.confirm("Are you sure , this trancation is right?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/mutual_funds/update_right_trancation_flag');?>',
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
