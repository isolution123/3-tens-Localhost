<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Assets List</li>
            </ol>
            <h1>Assets Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Assets_liabilities/al_report');?>">Assets Details</a></li>
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
                            <h4>Assets Table</h4>
                        </div>

                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Assets or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
                            if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
                            ?>
                            <button class="btn btn-success" tabindex="1" onclick="location.href='<?php echo base_url('broker/Assets_liabilities/add_form?trans_type=asset'); ?>';"><i class="fa fa-plus"></i> Add Assets or Liabilities</button>
                            <?php
                            }
                            else{
                            ?>
                            <button class="btn btn-success disable_btn" tabindex="1" onclick="location.href='<?php echo base_url('broker/Assets_liabilities/add_form?trans_type=asset'); ?>';"><i class="fa fa-plus"></i> Add Assets or Liabilities</button>
                            <?php
                            }
                            ?>


                            <br /><br />

                                <div class="form-group" style="float:right;">
                                <label><b>Commitment Search between</b></label>
                                <div>
                                    <!--<div class="radio block">
                                        <label><input type="radio" name="type" id="stDate" value="StartDate" checked="checked"> Start Date</label><span style="margin-right: 30px;"></span>
                                        <label><input type="radio" name="type" id="endDate" value="EndDate"> End Date</label>
                                    </div><br/> -->
                                    <input type="text" id="from" name="from" class="datepicker from mask" data-inputmask="'alias': 'date'" placeholder="Date From" />
                                    &nbsp;&nbsp;
                                    <input type="text" id="to" name="to" class="datepicker to mask" data-inputmask="'alias': 'date'" placeholder="Date To" />
                                </div>
                            </div><br/>




                            <div class="table-responsive" style="float: left; width: 100%;">
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
                                        <?php //<th>Advisor Feeds</th>?>
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
                                        <?php //<th>Advisor Feeds</th>?>
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
        table = ajax_asset_list();
        table_initialize();

                /* apply masks to inputs */
        $('.mask').inputmask();
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
    });

    function ajax_asset_list() {
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
                <?php //{ "data": "narration" }?>
            ]

        });
        return oTable;
    }




    //date between search
    // Event listener to the two range filtering inputs to redraw on input
    $('#from, #to').change( function() {
       table.draw();
    });
    $('#from, #to').keyup( function() {
        table.draw();
    });
    $("input:radio[name=type]").on('click', function() {
       //table.draw();
       //ajax_asset_list_extended();
    });

    /* Custom filtering function which will search data in column 10 between two values */
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var f = $('#from').val().split('/');
            var t = $('#to').val().split('/');
            var from = new Date(f[2],f[1]-1,f[0]);
            var to = new Date(t[2],t[1]-1,t[0]);

            //check if premium is selected or maturity (for search)
            /*if($("#stDate").prop("checked") == true) {
                var d1 = data[9].split('/');
                var d2 = data[10].split('/');
            } else {
                var d = data[10].split('/');
            }*/

            var d1 = data[9].split('/');
            var d2 = data[10].split('/');

            var date1 = new Date(d1[2],d1[1]-1,d1[0]); // use data for the premium date column
            var date2 = new Date(d2[2],d2[1]-1,d2[0]); // use data for the premium date column

             if( (from == "" && to == "" ) ||
                ( isNaN( from ) || isNaN( to ) ) )
            {
                return true
            }
            else if(from<date1 && from<date2 && date1<to && date2<to)
            {
                return true;
            }
            else if(from>date1 && from<date2 && date1<to && date2<to)
            {
                return true;
            }
            else if(from>date1 && from>date2 && date1<to && date2<to)
            {
                return false;
            }
            else if(from>date1 && from<date2 && date1<to && date2>to)
            {
                return true;
            }
            else if(from<date1 && from<date2 && date1<to && date2>to)
            {
                return true;
            }
            else if(from<date1 && from<date2 && date1>to && date2>to)
            {
                return false;
            }
            else
            {
            return false;
            }
        }
    );

    function ajax_asset_list_extended() {
        alert("hello");

        $('#asset_table').dataTable({
            destroy: true,
            aaData: response.data
        });

        oTable = $("#asset_table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Assets_liabilities/asset_ajax_list_extended');?>',
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
                { "data": "from"},
                { "data": "to"}
            ]

        });
        return oTable;
    }


    function edit_asset(id)
    {
        location.href = '<?php echo base_url();?>broker/Assets_liabilities/edit_form?id='+id+'&trans_type=asset';
    }

    function delete_asset(al_id)
    {
        if(al_id != null)
        {
            bootbox.confirm("Are you sure you want to delete this Asset?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/Assets_liabilities/delete_asset');?>',
                        type: 'post',
                        data: {asset_id: al_id},
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
                                oTable = ajax_asset_list();
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
