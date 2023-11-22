<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li class="active">Your Reminders</li>
            </ol>
            <h1>Your Reminders</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Pending Reminders Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Note: All Pending Reminders upto today are shown which are not completed.<br/> For Further details click on View Details.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            
                            <!-- edited - Akshay R - 2017-08-23 -->
                            <div id="events">
                                <text> 0 rows are checked<text/>
                            </div>
                            <span>
                                <button id="btn_Mail" tabindex="0" value="buttonMail" height="300%" style="width:15%;height:30%;shadow:100px" class ="paginate_button" aria-controls="table" data-dt-idx="1" ><center>Send Mail(s)</center></button>
                            </span>
                             <div id="selectsDone">
                            </div>
                            
                            <?php //remove add reminder option from here. That option is provided in Reminder Analyzer - Salmaan - 15/04/16
                                //<button type="button" id="matBtn" onclick="add_reminder_dialog()" tabindex="27" class="btn btn-success">Add Reminder</button> ?>
                            <br /><br />
                            <!--<div class="table-responsive">-->
                            <!--    <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">-->
                            <!--        <thead>-->
                            <!--        <tr>-->
                            <!--            <th class="action-col">Action</th>-->
                            <!--            <th>Reminder ID</th>-->
                            <!--            <th>Reminder Date</th>-->
                            <!--            <th>Subject</th>-->
                            <!--            <th>Client Name</th>-->
                            <!--            <th>Message</th>-->
                            <!--            <th>Status</th>-->
                            <!--        </tr>-->
                            <!--        </thead>-->
                            <!--        <tfoot>-->
                            <!--        <tr>-->
                            <!--            <th class="action-col"></th>-->
                            <!--            <th>Reminder ID</th>-->
                            <!--            <th>Reminder Date</th>-->
                            <!--            <th>Subject</th>-->
                            <!--            <th>Client Name</th>-->
                            <!--            <th>Message</th>-->
                            <!--            <th>Status</th>-->
                            <!--        </tr>-->
                            <!--        </tfoot>-->
                            <!--    </table><!--end table-->
                            <!--</div>-->
                            
                            <!--Akshay R - 2017-08-23-->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">
                                    <thead>
                                    <tr>
                                        <th> <center><span style="float:left">Check/Uncheck All</span> <input name="select_all" value="1" id="select-all" type="checkbox" /></center></th>
                                        <th></th>
                                        <th class="action-col" readonly="readonly" disabled="true">Action</th>
                                        <th>Reminder ID</th>
                                        <th>Reminder Date</th>
                                        <th>Subject</th>
                                        <th>Mail sent status</th> <!-- edited WIProgress -->
                                        <th>Client Name</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>                                    
                                    <tr>
                                        <th class="th1" > <center><span style="float:left">Check/Uncheck All</span> <input name="select_all" value="1" id="select-all" type="checkbox" /></center></th>
                                        <th class="th1"  ></th>
                                        <th class="th1" >Action</th>
                                        <th >Reminder ID</th>
                                        <th >Reminder Date</th>
                                        <th >Subject</th>
                                        <th class="th1">Mail sent status</th> <!-- edited WIProgress -->
                                        <th >Client Name</th>
                                        <th >Message</th>
                                        <th >Status</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th></th>           
                                        <th></th>                       
                                        <th class="action-col"></th>
                                        <th>Reminder ID</th>
                                        <th>Reminder Date</th>
                                        <th>Subject</th>
                                        <th>Mail sent status</th> <!-- edited WIProgress -->
                                        <th>Client Name</th>
                                        <th>Message</th>
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
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Previous Remark</label>
                                        <div class="col-md-9">
                                            <textarea disabled class="form-control" id="remark"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSnooze" onclick="snooze()" class="btn btn-primary"> <i class="fa fa-clock-o"></i> Snooze/Assign</button>
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
                                            <select name="client_id" class="populate" required="required" id="client_id" style="width: 100%" tabindex="3">
                                                <option disabled selected value="">Select Client</option>
                                                <?php /*foreach($clients as $row):*/?>
                                                    <option value='<?php /*echo $row->client_id; */?>'><?php /*echo $row->name; */?></option>
                                                <?php /*endforeach; */?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Reminder Date</label>
                                        <div class="col-md-9">
                                            <input type="text" id="reminder_date" class="form-control date" data-inputmask="'alias':'date'"  required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Message</label>
                                        <div class="col-md-9">
                                            <textarea id="message" class="form-control" required="required"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnRemSave" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>-->
                    <!--</div><!-- /.modal-content -->
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
                                            <input type="text" id="remSnoozeDate" class="form-control date"  data-inputmask="'alias':'date'"  required="required">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Assign to</label>
                                        <div class="col-md-9">
                                            <select name="concern_user" class="populate" required="required" id="concern_user" style="width: 100%" tabindex="6">
                                                <option disabled selected value="">Select User</option>
                                                <option value="<?/*=$this->session->userdata('username');*/?>">SELF</option>
                                                <option value="all">All Users</option>
                                                <?php /*foreach($users as $row):?>
                                                    <option value='<?php echo $row->username; ?>'><?php echo $row->name; ?></option>
                                                <?php endforeach;*/ ?>
                                            </select>
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
            <!--</div>
            <!-- End Bootstrap modal -->
        </div>
    </div>
</div>
<style type="text/css">
    .datepicker{z-index: 9999 !important;}
</style>
<script type="text/javascript">
    var save_method; //for save method string
    var table_rem_p;
    var events = $('#events');
    var table = undefined;

    function ajax_reminder_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[2,'desc']],
            
            /*edited for dropdown datatable filter - Akshay R - 2017-08-23 */
            initComplete : function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select ><option value=""></option></select>')
                    .appendTo( $(column.header()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    } );
                } );
            },            
			/*edited for dropdown datatable filter */

            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 1 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Reminders/reminder_list');?>',
                "type": 'post'
            },
            "columns": [
                /* edited - Akshay R - 2017-08-23 */                
                { "data": "checkBox"},
                { "data": "xyz" },
                /* edited */
                { "data": "action" },
                { "data": "reminder_id" },
                { "data": "reminder_date", "type": "date-uk" },
                { "data": "reminder_type" },
                { "data": "mail_sent_status" }, //edited WIProgress
                { "data": "client_name" },
                { "data": "reminder_message" },
                { "data": "reminder_status" }
            ]

        });
        return oTable;
    }
    $(document).ready(function(){
        table_rem_p = ajax_reminder_list();
        table_initialize();
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy', startDate:'<?=date('d-m-Y');?>'}).inputmask();
        
        
        /*edited - Akshay R - 2017-08-23 */        
        /*$('[name="table_length"]').*/
        $("table").on("click",function(){
        $(".th1").html('');
        $(".th1").prop('disabled', true);
        });

        var limitDD="";
        limitDD = $("#table_length select option").val();


        table_rem_p.on('click','tr td',function()
        {
            $(this).find('.boxCheck').prop('checked', !($(this).find('.boxCheck').prop("checked"))); //edited WIProgress
        });

        table_rem_p.on('click','.boxCheck',function()
        {
            $(this).prop('checked',!$(this).prop("checked"));
        });

        $('#select-all').on('click', function()
        {
            // Check/uncheck all checkboxes in the table
            var rows = table_rem_p.rows({ 'search': 'applied' }).nodes();            
            $('input[type="checkbox"]').prop('checked', this.checked);
            countChecked(allMinus1=1);
        });

        table_rem_p.on('click', 'tr td', function(){
          // If checkbox is not checked
            if(!this.checked){
                var el = $('#select-all').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control 
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
        });

        $("#btn_Mail").on('click',function()
        {
            var idArray = [];
            $.each($("input[class='boxCheck']:checked"), function(){            
                idArray.push($(this).val());
            });            
            var arrSendTo = idArray;
/*            console.log(idArray);*/

            $.ajax({
                url: "<?php echo base_url('broker/reminders/sendMultiMails');?>",
                type: "POST",
                data: {idSendTo:arrSendTo},
                success: function(sentConfirmation)
                {
                    if(sentConfirmation.indexOf("sent")!== -1){
                        alert(sentConfirmation);
                        /*$.pnotify({
                            title: sentConfirmation['title'],
                            text: sentConfirmation['text'],
                            type: 'success',
                            hide: true
                        });*/
                        $("#table").DataTable().ajax.reload();
                        events.html(' 0 rows are checked');
                    }
                    else{
                        /*$.pnotify({
                            title: sentConfirmation['title'],
                            text: sentConfirmation['text'],
                            type: 'error',
                            hide: true
                        });*/
                        console.log(sentConfirmation);
                        alert("No Mails sent.");
                    }
                },
                error: function(data) {
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'error',
                        hide: true
                    });
                    console.log(data);
                }

            });
        });

        var allMinus1=0;

        var countChecked = function(allMinus1 = 0)
            {   
                var checkCount;
                if(($("#select-all").prop('checked'))==true){
                   checkCount = $( "input:checked" ).length-1;
                }
                else{

                     checkCount = $( "input:checked" ).length/*-allMinus1*/;
    /*
                    if($("#select-all").prop('checked')==false){
                        checkCount= checkCount+1;
                    }*/
                    
                    if (checkCount > limitDD){
                        checkCount = limitDD;
                        alert(checkCount+"greater than equals limitDD")
                    }
    /*                else if(checkCount< limitDD){
                        alert(checkCount+" less than limitDD")
                    }*/
                    if (checkCount<0) {
                        checkCount = 0;
                        alert(checkCount+" less than 0 error")
                    };
                }

                events.text( checkCount + (checkCount === 1 ? " row is" : " rows are") + " checked" );
            };



            table_rem_p.on("click","tr td",function()
            {
                countChecked();
                $('tr').removeClass('selected');
                $(this).closest('tr').toggleClass('selected');
            });
        // });

        /*edited*/
        
        /*$('#btnSave').click(function(){
            if($('#form_assign').parsley('validate'))
            {
                var remarks = $("#remark").val();
                if(remarks != null && remarks != '') {
                    remarks += ' ----- '+$("#remRemark").val();
                } else {
                    remarks = $("#remRemark").val();
                }
                $.ajax({
                    url: '<?php //echo site_url('broker/Reminders/snooze')?>',
                    type:'post',
                    data: {'remID': $('#remAssignID').val(), 'remarks': remarks, 'snoozeDate': $("#remSnoozeDate").val(), 'remDate': $("#remDate").val(), 'concern_user': $("#concern_user").val()},
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
                            $("#assign_form").modal('hide');
                            $("#remark").val(remarks);
                            table_rem.destroy();
                            table_rem = ajax_reminder_list();
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
                    url: '<?php //echo site_url('broker/Reminders/add_reminder')?>',
                    type:'post',
                    data: {'clientID': $('#client_id').val(), 'clientName': $("#client_id option:selected").text(), 'msg': $("#message").val(), 'remDate': $("#reminder_date").val()},
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
                            table_rem.destroy();
                            table_rem = ajax_reminder_list();
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
                        url: '<?php //echo site_url('broker/Reminders/complete')?>',
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
                                table_rem.destroy();
                                table_rem = ajax_reminder_list();
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
                        url: '<?php //echo site_url('broker/Reminders/send_mail')?>',
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
        $('#remSnoozeDate').datepicker("setDate", new Date());
        $('#remSnoozeDate').datepicker("update");
        $('#remAssignID').val($('#remID').val());
        $('#concern_user').select2('val','');
        //show bootstrap modal
        $("#assign_form").modal('show');
        //set title to modal
        $("#assign_modal_title").text('Snooze/Assign Window');

    }

    function add_reminder_dialog(id)
    {
        $('#form_add_rem')[0].reset(); // reset form on modals
        $('#client_id').select2("val","");
        $('#reminder_date').val("");
        $('#message').val("");
        $('#add_rem_modal').modal('show'); // show bootstrap modal when complete loaded
        $('#add_rem_title').text('Add Reminder'); // Set title to Bootstrap modal title
    }

    function view_reminder(id)
    {
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php //echo site_url('broker/Reminders/get_reminder_details');?>",
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