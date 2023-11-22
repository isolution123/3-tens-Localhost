<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li class="active">Insurances</li>
            </ol>
            <h1>Insurance Master</h1>
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url('broker/Insurances/ins_report');?>">Insurance Details</a></li>
                            <li><a href="<?php echo base_url('broker/Insurances/premium_calendar_report');?>">Premium Calender</a></li>
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
                            <h4>Insurance Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Insurance or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <?php
if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
?>
   <button class="btn btn-success" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Insurances/add_form?1'); ?>';"><i class="fa fa-plus"></i> Add Insurance</button>
<?php
}
else{
?>
  <button class="btn btn-success  disable_btn" tabindex="1" onclick="javascript:location.href='<?php echo base_url('broker/Insurances/add_form?1'); ?>';"><i class="fa fa-plus"></i> Add Insurance</button>
<?php
}
?>


                            <br /><br />
                            <div class="form-group" style="float:right;">
                                <label><b>Search for</b></label>
                                <div>
                                    <div class="radio block">
                                        <label><input type="radio" name="type" id="prem" value="Premium" checked="checked"> Premium Date</label><span style="margin-right: 30px;"></span>
                                        <label><input type="radio" name="type" id="mat" value="Maturity"> Maturity Date</label>
                                    </div><br/>
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
                                        <th>Start Date</th>
                                        <th>Company Name</th>
                                        <th>Plan Name</th>
                                        <th>Plan Type</th>
                                        <th>Sum Assured</th>
                                        <th>Policy Number</th>
                                        <th>Installment Amt</th>
                                      <!--  <th>Asset Allocation</th> -->
                                        <th>First Unpaid Premium</th>
                                        <th>Grace Due Date</th>
                                        <th>Premium Mode</th>
                                        <th>Premium Payment Option</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                         <!--<th>System Fund Value</th> -->
                                        <th>Advisor Name</th>
                                        <th>Maturity Date</th>
                                        <th>Premium Paid Till Date</th>
                                         <!--<th>Maturity Mode</th>-->
                                        <th>Fund Option 1</th>
                                        <th>Fund value 1</th>
                                        <th>Fund Option 2</th>
                                        <th>Fund value 2</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col"></th>
                                        <th>Family Name</th>
                                        <th>Client Name</th>
                                        <th>Start Date</th>
                                        <th>Company Name</th>
                                        <th>Plan Name</th>
                                        <th>Plan Type</th>
                                        <th>Sum Assured</th>
                                        <th>Policy Number</th>
                                        <th>Installment Amt</th>

                                        <th>First Unpaid Premium</th>
                                        <th>Grace Due Date</th>
                                        <th>Premium Mode</th>
                                        <th>Premium Payment Option</th>
                                        <th>Status</th>
                                        <th>Remarks</th>

                                        <th>Advisor Name</th>
                                        <th>Maturity Date</th>
                                        <th>Premium Paid Till Date</th>

                                        <th>Fund Option 1</th>
                                        <th>Fund value 1</th>
                                        <th>Fund Option 2</th>
                                        <th>Fund value 2</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                        <?php /*<div style="padding-top: 3%; padding-bottom: 3%;">
                            <div class="col-sm-6" >
                                <div class="col-sm-4"><label for="amt-ins"><b>Total Amount Insured: </b></label></div>
                                <div class="col-sm-8">&#8377;&nbsp;<input id="amt-ins" disabled></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-4"><label for="prem-amt"><b>Total Premium Amt: </b></label></div>
                                <div class="col-sm-8">&#8377;&nbsp;<input id="prem-amt" disabled></div>
                            </div>
                            <br/><br/>
                            <div class="col-sm-6">
                                <div class="col-sm-4"><label for="fund-val"><b>Total Fund Value: </b></label></div>
                                <div class="col-sm-8">&#8377;&nbsp;<input id="fund-val" disabled></div>
                            </div>
                        </div> */ ?>
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
        table = ajax_ins_list_extended();
        table_initialize();


        /* apply masks to inputs */
        $('.mask').inputmask();
        $('.datepicker').datepicker({format:"dd/mm/yyyy", onSelect: function() {
                                                                        $(this).change();
                                                                    }
        });
    });

    function ajax_ins_list() {
            var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/insurances/ajax_list');?>',
                "type": 'post'
                /*success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "action" },
                { "data": "family_name"},
                { "data": "client_name" },
                { "data": "commence_date", "type": "date-uk"},
                { "data": "ins_comp_name" },
                { "data": "plan_name" },
                { "data": "plan_type_name" },
                { "data": "amt_insured" },
                { "data": "policy_num" },
                { "data": "prem_amt" },
                { "data": "next_prem_due_date", "type": "date-uk" },
                { "data": "grace_due_date", "type": "date-uk" },
                { "data": "mode_name" },
                { "data": "prem_pay_mode" },
                { "data": "status" },
                { "data": "remarks" },
                { "data": "adviser_name" },
                { "data": "maturity_date" },
                { "data": "prem_paid_till_date"},
                { "data": "fund_option1" },
                { "data": "fund_value1" },
                { "data": "fund_option2" },
                { "data": "fund_value2" }
            ]
            /*drawCallback: function () {
                var api = this.api();
                $( "#amt-ins" ).val(api.column( 6, {search:'applied'} ).data().sum());
                $( "#prem-amt" ).val(api.column( 8, {search:'applied'} ).data().sum());
                $( "#fund-val" ).val(api.column( 16, {search:'applied'} ).data().sum());
            }*/

        });
        return oTable;
    };

//extended search
function ajax_ins_list_extended() {
        // alert("hello");
        var from = $("#from").val();
        var to = $("#to").val();
        var type = $("input:radio[name=type]:checked").val();
        var oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "iDisplayStart":0,
            "iDisplayLength": 10,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            //"ajax": {
                //Load data for the table through ajax
                "sAjaxSource": '<?php echo site_url('broker/insurances/ajax_list');?>?from='+from+'&to='+to+'&type='+type,
                /*"type": 'post'
                "data": {from:$("#from").val(), to:$("#to").val()},
                success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            //},
            "columns": [
                { "data": "action" },
                { "data": "family_name"},
                { "data": "client_name" },
                { "data": "commence_date", "type": "date-uk"},
                { "data": "ins_comp_name" },
                { "data": "plan_name" },
                { "data": "plan_type_name" },
                { "data": "amt_insured" },
                { "data": "policy_num" },
                { "data": "prem_amt" },
                { "data": "next_prem_due_date", "type": "date-uk" },
                { "data": "grace_due_date", "type": "date-uk" },
                { "data": "mode_name" },
                { "data": "prem_pay_mode" },
                { "data": "status" },
                { "data": "remarks" },
                { "data": "adviser_name" },
                { "data": "maturity_date" },
                { "data": "prem_paid_till_date"},
                { "data": "fund_option1" },
                { "data": "fund_value1" },
                { "data": "fund_option2" },
                { "data": "fund_value2" }
            ]
            /*drawCallback: function () {
                var api = this.api();
                $( "#amt-ins" ).val(api.column( 6, {search:'applied'} ).data().sum());
                $( "#prem-amt" ).val(api.column( 8, {search:'applied'} ).data().sum());
                $( "#fund-val" ).val(api.column( 16, {search:'applied'} ).data().sum());
            }*/

        });
        return oTable;
    }

    //date between search
    // Event listener to the two range filtering inputs to redraw on input
    $("#from, #to").change( function() {
        table=ajax_ins_list_extended();
    });
    $("#from, #to").keyup( function() {
        table=ajax_ins_list_extended();
    });
    $("input:radio[name=type]").on('click', function() {
        table=ajax_ins_list_extended();
    });

    /* Custom filtering function which will search data in column 10 between two values
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var f = $('#from').val().split('/');
            var t = $('#to').val().split('/');
            var from = new Date(f[2],f[1]-1,f[0]);
            var to = new Date(t[2],t[1]-1,t[0]);

            //check if premium is selected or maturity (for search)
            if($("#prem").prop("checked") == true) {
                var d = data[10].split('/');
            } else {
                var d = data[18].split('/');
            }

            var date = new Date(d[2],d[1]-1,d[0]); // use data for the premium date column

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


    function edit_ins(id)
    {
        location.href = '<?php echo base_url();?>broker/Insurances/edit_form?id='+id;
    }

    function del_ins(pol_num, client_id, comp_id)
    {
        if(pol_num != null)
        {
            bootbox.confirm("Are you sure you want to delete this Insurance?", function(result) {
                if(result) {
                    $.ajax({
                        url: '<?php echo site_url('broker/Insurances/del_ins');?>',
                        type: 'post',
                        data: {policy_num: pol_num, client_id: client_id, comp_id: comp_id},
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
                                table.destroy();
                                table = ajax_ins_list_extended();
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
            bootbox.alert("Please add Insurance details first");
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
