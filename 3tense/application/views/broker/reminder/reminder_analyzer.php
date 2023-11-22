<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li>Reminders</li>
                <li class="active">Reminder Analyzer</li>
            </ol>
            <h1>Reminder Analyzer</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Reminders Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Note: All your reminders are shown. You may select whether you want to see Pending reminders or Completed reminders.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button type="button" id="addRemBtn" onclick="add_reminder_dialog()" tabindex="27" class="btn btn-success">Add Reminder</button>
                            <br /><br /><br/>
                            <div class="form-group" style="">
                                <label style="font-weight: bold; font-size: larger;">Selection of reminders to show</label>
                                <div class="col-9">
                                    <div class="radio block">
                                        <div class="col-sm-3">
                                            <label style="font-weight: bold; cursor: default;">Reminder Type:</label>
                                        </div>
                                        <div>
                                            <label><input type="radio" name="radio-type" id="personal" value="personal" checked="checked"> Personal Reminders</label><span style="margin-right: 30px;"></span>
                                            <label><input type="radio" name="radio-type" id="system" value="system"> System Reminders</label>
                                        </div>
                                    </div>
                                    <div class="radio block">
                                        <div class="col-sm-3">
                                            <label style="font-weight: bold; cursor: default;">Reminder Status:</label>
                                        </div>
                                        <label><input type="radio" name="radio-status" id="pending" value="pending" checked="checked"> Pending</label><span style="margin-right: 30px;"></span>
                                        <label><input type="radio" name="radio-status" id="completed" value="completed"> Completed</label>
                                    </div>
                                    <div class="radio block">
                                        <div class="col-sm-3">
                                            <label style="font-weight: bold; cursor: default;">Number of Days:</label>
                                        </div>
                                        <input type="number" name="noOfDays" min="0" id="numberOfDays"/></label>
                                    </div>
                                </div>
                            </div><br/><br/>
                            <div class="table-responsive" style="float: left; width: 100%;">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">
                                    <thead>
                                    <tr id="head">
                                        <?php //<th class="action-col action-col-1">Action</th> ?>
                                        <th>Reminder Date</th>
                                        <th>Subject</th>
                                        <th>Client Name</th>
                                        <th>Message</th>
                                        <th>Remark</th>
                                        <th class="next_date">Snooze Date</th>
                                        <th>Concerned User</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr id="foot">
                                        <?php //<th class="action-col action-col-1"></th> ?>
                                        <th>Reminder Date</th>
                                        <th>Subject</th>
                                        <th>Client Name</th>
                                        <th>Message</th>
                                        <th>Remark</th>
                                        <th class="next_date">Snooze Date</th>
                                        <th>Concerned User</th>
                                        <th>Status</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bootstrap modal -->
            <!--<div class="modal fade" id="modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="rem_modal_title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="form" class="form-horizontal">
                                <div class="form-body">
                                    <div class="form-group">
                                        <input name="remID" id="remID" type="hidden">
                                        <label class="control-label col-md-3">Date</label>
                                        <div class="col-md-9">
                                            <input type="text" disabled class="form-control" id="remDate">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Subject</label>
                                        <div class="col-md-9">
                                            <input type="text" disabled class="form-control" id="remSub">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Client Name</label>
                                        <div class="col-md-9">
                                            <input type="text" disabled class="form-control" id="remClientName">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Message</label>
                                        <div class="col-md-9">
                                            <textarea disabled class="form-control" id="remMsg"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSnooze" onclick="snooze()" class="btn btn-primary"> <i class="fa fa-clock-o"></i> Snooze</button>
                            <button type="button" id="btnComp" class="btn btn-success"><i class="fa fa-check"></i> Complete</button>
                            <button type="button" id="btnMail" class="btn btn-sky"><i class="fa fa-envelope"></i> Send Mail</button>
                            <button type="button" id="btnSMS" class="btn btn-midnightblue"><i class="fa fa-mobile"></i> Send SMS</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban"></i> Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                <!--</div><!-- /.modal-dialog -->
            <!--</div><!-- /.modal -->
            <!-- Bootstrap modal -->
            <!--<div class="modal fade" id="add_rem_modal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="add_rem_title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="form_add_rem" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Client Name</label>
                                        <div class="col-md-9">
                                            <?php /*<select name="client_id" class="populate" required="required" id="client_id" style="width: 100%" tabindex="3">
                                                <option disabled selected value="">Select Client</option>
                                                <?php foreach($clients as $row):?>
                                                    <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                                <?php endforeach; ?>
                                            </select> */?>
                                            <input type="text" class="form-control" id="client_name" name="client_name" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Reminder Date</label>
                                        <div class="col-md-9">
                                            <input type="text" id="reminder_date" class="form-control date" data-inputmask="'alias':'date'" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Message</label>
                                        <div class="col-md-9">
                                            <textarea id="message" class="form-control" required="required"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Reminder for</label>
                                        <div class="col-md-9">
                                            <select name="concern_user" class="populate" required="required" id="concern_user" style="width: 100%" tabindex="6">
                                                <option disabled selected value="">Select User</option>
                                                <option value="<?/*=$this->session->userdata('username');*/?>">SELF</option>
                                                <option value="all">All Users</option>
                                                <?php /*foreach($users as $row):*/?>
                                                    <option value='<?php /*echo $row->username; */?>'><?php /*echo $row->name; */?></option>
                                                <?php /*endforeach; */?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnRemSave" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                <!--</div><!-- /.modal-dialog -->
            <!--</div>
            <div class="modal fade" id="assign_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="assign_modal_title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="form_assign" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <input name="remAssignID" id="remAssignID" type="hidden">
                                        <label class="control-label col-md-3">Remark</label>
                                        <div class="col-md-9">
                                            <textarea id="remRemark" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Snooze Date</label>
                                        <div class="col-md-9">
                                            <input type="text" id="remSnoozeDate" class="form-control date" data-inputmask="'alias':'date'"  required="required">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                <!--</div><!-- /.modal-dialog -->
            <!--</div>-->
            <!-- End Bootstrap modal -->
        </div>
    </div>
</div>
<style type="text/css">
    .datepicker{z-index: 9999 !important;}
</style>
<script type="text/javascript">
    var save_method; //for save method string
    var table_rem;
    var table = undefined;
    var radio_type = 'personal';
    var radio_status = 'pending';

    function ajax_reminder_list_pending() {
        $(".turnaround").remove();
        $(".next_date").html("Snooze Date");

        var oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "iDisplayStart":0,
            "iDisplayLength": 25,
            "aaSorting": [[0,'asc']],
            /*"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],*/
            "sAjaxSource": "<?php echo site_url('broker/reminder_analyzer/reminder_list');?>?type="+radio_type+"&status="+radio_status,
            "columns": [
                //{ "data": "action" },
                { "data": "reminder_date", "type": "date-uk" },
                { "data": "reminder_type" },
                { "data": "client_name" },
                { "data": "reminder_message" },
                { "data": "remark" },
                { "data": "next_date", "type": "date-uk" },
                { "data": "name" },
                { "data": "reminder_status" }
            ]

        });
        return oTable;
    }
// for number of Days pending
    function ajax_reminder_list_pending_noOfDays() {
        $(".turnaround").remove();
        $(".next_date").html("Snooze Date");

        var oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "iDisplayStart":0,
            "iDisplayLength": 25,
            "aaSorting": [[0,'asc']],
            /*"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],*/
            "sAjaxSource": "<?php echo site_url('broker/reminder_analyzer/reminder_list');?>?type="+radio_type+"&status="+radio_status+"&noOfDays="+noOfDays,
            "columns": [
                //{ "data": "action" },
                { "data": "reminder_date", "type": "date-uk" },
                { "data": "reminder_type" },
                { "data": "client_name" },
                { "data": "reminder_message" },
                { "data": "remark" },
                { "data": "next_date", "type": "date-uk" },
                { "data": "name" },
                { "data": "reminder_status" }
            ]

        });
        return oTable;
    }

// for number of Days completed
 function ajax_reminder_list_complete_noOfDays() {
        if($(".turnaround").length) {
            //do nothing, as turnaround column is already present
        } else {
            $("#head").append("<th class='turnaround'>Turnaround Time</th>");
            $("#foot").append("<th class='turnaround action-col'>Turnaround Time</th>");
            $(".next_date").html("Completed On");
        }

        var oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "iDisplayStart":0,
            "iDisplayLength": 25,
            "aaSorting": [[0,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ -1 ] }],
            "sAjaxSource": "<?php echo site_url('broker/reminder_analyzer/reminder_list');?>?type="+radio_type+"&status="+radio_status+"&noOfDays="+noOfDays,
            "columns": [
                //{ "data": "action" },
                { "data": "reminder_date", "type": "date-uk" },
                { "data": "reminder_type" },
                { "data": "client_name" },
                { "data": "reminder_message" },
                { "data": "remark" },
                { "data": "completed_on", "type": "date-eu" },
                { "data": "name" },
                { "data": "reminder_status" },
                { "data": "turnaround_time" }
            ]

        });
        
        return oTable;
    }



    function ajax_reminder_list_complete() {
        if($(".turnaround").length) {
            //do nothing, as turnaround column is already present
        } else {
            $("#head").append("<th class='turnaround'>Turnaround Time</th>");
            $("#foot").append("<th class='turnaround action-col'>Turnaround Time</th>");
            $(".next_date").html("Completed On");
        }

        var oTable = $("#table").DataTable({
            "destroy": true,
            "processing":true,    //Control the processing indicator
            "serverSide":true,    //Control DataTable server process
            "iDisplayStart":0,
            "iDisplayLength": 25,
            "aaSorting": [[0,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ -1 ] }],
            "sAjaxSource": "<?php echo site_url('broker/reminder_analyzer/reminder_list');?>?type="+radio_type+"&status="+radio_status,
            "columns": [
                //{ "data": "action" },
                { "data": "reminder_date", "type": "date-uk" },
                { "data": "reminder_type" },
                { "data": "client_name" },
                { "data": "reminder_message" },
                { "data": "remark" },
                { "data": "completed_on", "type": "date-eu" },
                { "data": "name" },
                { "data": "reminder_status" },
                { "data": "turnaround_time" }
            ]

        });
        return oTable;
    }

    //on change of selected radio type - personal & system
    $("[name='radio-type']").on('click', function() {
        $("#numberOfDays").val('');
        if(radio_type != this.value) {
            radio_type = this.value;
            table_rem.clear();
            if(radio_status == 'completed') {
                table_rem = ajax_reminder_list_complete();
            } else if(radio_status == 'pending') {
                table_rem = ajax_reminder_list_pending();
            }
            table_initialize();
        }
    });
    //on change of selected radio status - pending & completed
    $("[name='radio-status']").on('click', function() {
        $("#numberOfDays").val('');
        if(radio_status != this.value) {
            radio_status = this.value;
            table_rem.clear();
            if(radio_status == 'completed') {
                table_rem = ajax_reminder_list_complete();
            } else if(radio_status == 'pending') {
                table_rem = ajax_reminder_list_pending();
            }
            table_initialize();
        }
    });
  //on change of Number of Days
  $("[name='noOfDays']").on('input', function() {
    //alert(this.value);
            noOfDays = this.value;
            table_rem.clear();
    
            if(radio_status == 'completed') {
                table_rem = ajax_reminder_list_complete_noOfDays();
            } else if(radio_status == 'pending') {
                //alert('inside pending');
                table_rem = ajax_reminder_list_pending_noOfDays();
            }            
            table_initialize();
        
    });

    $(document).ready(function(){
        table_rem = ajax_reminder_list_pending();
        table_initialize();

        /* apply masks to inputs */
        $('.mask').inputmask();

    

        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy', startDate:'<?=date('d-m-Y');?>'}).inputmask();

        /*$('#btnSave').click(function(){
            if($('#form_assign').parsley('validate'))
            {
                $.ajax({
                    url: '<?php /*echo site_url('broker/Reminder_analyzer/snooze')*/?>',
                    type:'post',
                    data: {'remID': $('#remAssignID').val(), 'remarks': $("#remRemark").val(), 'snoozeDate': $("#remSnoozeDate").val(), 'remDate': $("#remDate").val()},
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
                            $(".modal").modal('hide');
                            table.clear();
                            if(radio_status == 'completed') {
                                table = ajax_reminder_list_complete();
                            } else {
                                table = ajax_reminder_list_pending();
                            }
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
                    error: function (data)
                    {
                        console.log(data);
                        bootbox.alert('Error while snoozing reminder!!');
                    }
                });
            }
        });

        $('#btnRemSave').click(function(){
            if($('#form_add_rem').parsley('validate'))
            {
                $.ajax({
                    url: '<?php /*echo site_url('broker/Reminder_analyzer/add_reminder')*/?>',
                    type:'post',
                    //data: {'clientID': $('#client_id').val(), 'clientName': $("#client_id option:selected").text(), 'msg': $("#message").val(), 'remDate': $("#reminder_date").val(), 'concern_user': $("#concern_user").val()},
                    data: {'clientID': '', 'clientName': $("#client_name").val(), 'msg': $("#message").val(), 'remDate': $("#reminder_date").val(), 'concern_user': $("#concern_user").val()},
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
                            $(".modal").modal('hide');
                            table.clear();
                            if(radio_status == 'completed') {
                                table = ajax_reminder_list_complete();
                            } else {
                                table = ajax_reminder_list_pending();
                            }
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
                    error: function (data)
                    {
                        console.log(data);
                        bootbox.alert('Error while adding reminder!!');
                    }
                });
            }
        });

        $('#btnComp').click(function(){
            bootbox.confirm('Are you sure you want to complete this reminder?', function(result){
                if(result)
                {
                    $.ajax({
                        url: '<?php /*echo site_url('broker/Reminder_analyzer/complete')*/?>',
                        type:'post',
                        data: {'remID': $('#remID').val()},
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
                                $(".modal").modal('hide');
                                table.clear();
                                if(radio_status == 'completed') {
                                    table = ajax_reminder_list_complete();
                                } else {
                                    table = ajax_reminder_list_pending();
                                }
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
                        error: function (data)
                        {
                            console.log(data);
                            bootbox.alert('Error while completing reminder!!');
                        }
                    });
                }
            });
        });

        $('#btnMail').click(function(){
            bootbox.confirm('Are you sure you want to send mail to the client?', function(result){
                if(result)
                {
                    $.ajax({
                        url: '<?php /*echo site_url('broker/Reminder_analyzer/send_mail')*/?>',
                        type:'post',
                        data: {'remID': $('#remID').val()},
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
                                $(".modal").modal('hide');
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
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            console.log(jqXHR);
                            bootbox.alert('Error while sending mail');
                        }
                    });
                }
            });
        });

        $('#btnSMS').click(function(){
            bootbox.alert('Not Integrated Yet?');
        });*/
    });

    /*function snooze()
    {
        //reset form on modals
        $("#form_assign")[0].reset();
        $('#remAssignID').val($('#remID').val());
        //show bootstrap modal
        $("#assign_form").modal('show');
        //set title to modal
        $("#assign_modal_title").text('Snooze Window');
    }

    function add_reminder_dialog(id)
    {
        $('#form_add_rem')[0].reset(); // reset form on modals
        $('#client_id').select2("val","");
        //$('#reminder_date').val("");
        $('.date').datepicker("setDate", new Date());
        $('.date').datepicker('update');
        $('#message').val("");
        $('#remark').val("");
        $("#concern_user").select2("val","<?/*=$_SESSION['username']?>");
        $('#add_rem_modal').modal('show'); // show bootstrap modal when complete loaded
        $('#add_rem_title').text('Add Reminder'); // Set title to Bootstrap modal title
    }

    function view_reminder(id, status)
    {
        $('#form')[0].reset(); // reset form on modals

        if(status == 'pending') {
            //Ajax Load data from ajax
            $.ajax({
                url : "<?php /*echo site_url('broker/Reminders/get_reminder_details');*/?>",
                type: "POST",
                data:{id: id},
                dataType: "JSON",
                success: function(data)
                {
                    $('#remID').val(data.reminder_id);
                    $('#remDate').val(data.reminder_date);
                    $('#remSub').val(data.reminder_type);
                    $('#remClientName').val(data.client_name);
                    $('#remMsg').val(data.reminder_message);
                    $('#remark').val(data.remark);
                    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    $('#rem_modal_title').text('View '); // Set title to Bootstrap modal title

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    bootbox.alert("Something went terribly wrong");
                }
            });
        } else if(status == 'completed') {
            //Ajax Load data from ajax
            $.ajax({
                url : "<?php /*echo site_url('broker/Reminders/get_reminder_details');*/?>",
                type: "POST",
                data:{id: id},
                dataType: "JSON",
                success: function(data)
                {
                    $('#remID').val(data.reminder_id);
                    $('#remDate').val(data.reminder_date);
                    $('#remSub').val(data.reminder_type);
                    $('#remClientName').val(data.client_name);
                    $('#remMsg').val(data.reminder_message);
                    $('#remark').val(data.remark);
                    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    $('#rem_modal_title').text('View '); // Set title to Bootstrap modal title

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
    }*/
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