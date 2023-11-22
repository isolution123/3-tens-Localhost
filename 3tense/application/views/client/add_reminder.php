<script src="<?php echo base_url('assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/users/plugins/form-parsley/parsley.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/users/plugins/form-select2/select2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/users/plugins/bootbox/bootbox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/users/js/common.js'); ?>"></script>

<link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/form-select2/select2.css">
<link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css">
<style>
    .zoom:hover {
  -ms-transform: scale(2); /* IE 9 */
  -webkit-transform: scale(2); /* Safari 3-8 */
  transform: scale(2); 
}
</style>
<!-- Bootstrap modal - ADD REMINDER -->
<div class="modal fade" id="add_rem_modal1" role="dialog" style="z-index: 10">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="add_rem_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_add_rem1" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Client Name</label>
                            <div class="col-md-9">
                                <?php /*<select name="client_id" class="selection" required="required" id="client_id" style="width: 100%" tabindex="3">
                                    <option disabled selected value="">Select Client</option>
                                    <?php foreach($clients as $row):?>
                                        <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select> */ ?>
                                <input type="text" class="form-control" id="client_name" name="client_name" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Reminder Date</label>
                            <div class="col-md-9">
                                <input type="text" id="reminder_date" class="form-control remdate" data-inputmask="'alias':'date'" required="required">
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
                                <select name="concern_user" class="selection" required="required" id="concern_user" style="width: 100%">
                                    <option disabled selected value="">Select User</option>
                                    <option value="<?=$this->session->userdata('username');?>">SELF</option>
                                    <?php /*if($this->session->userdata('user_id') != $this->session->userdata('broker_id')) { ?>
                                        <option value="<?=$this->session->userdata('username');?>">SELF</option>
                                    <?php }*/ ?>
                                    <option value="all">All Users</option>
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
    </div><!-- /.modal-dialog -->
</div>

<!-- Bootstrap modal - VIEW REMINDER -->
<div class="modal fade" id="modal_form1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="rem_modal_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form1" class="form-horizontal">
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
                            <label class="control-label col-md-3">Attachment</label>
                            <div class="col-md-9">
                                 <img class="zoom" id="remAttachment" style="width: 50%;">
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="control-label col-md-3">Previous Remark</label>
                            <div class="col-md-9">
                                <textarea disabled class="form-control" id="remark"></textarea>
                            </div>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" id="btnSnooze" onclick="snooze()" class="btn btn-primary"> <i class="fa fa-clock-o"></i> Snooze/Assign</button> -->
                <button type="button" id="btnComp" class="btn btn-success"><i class="fa fa-check"></i> Complete</button>
                <!-- <button type="button" id="btnMail" class="btn btn-sky"><i class="fa fa-envelope"></i> Send Mail</button>
                <button type="button" id="btnSMS" class="btn btn-midnightblue"><i class="fa fa-mobile"></i> Send SMS</button> -->
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Bootstrap modal - VIEW CLIENT REMINDER -->
<div class="modal fade" id="modal_form2" role="dialog" style="z-index: 9">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="rem_modal_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form2" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="remID" id="remIDc" type="hidden">
                            <label class="control-label col-md-3">Date</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remDatec">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Subject</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remSubc">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Client Name</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remClientNamec">
                            </div>
                        </div>

                                <input type="hidden" disabled class="form-control" id="client_idc">

                        <div class="form-group">
                            <label class="control-label col-md-3">Message</label>
                            <div class="col-md-9">
                                <textarea disabled class="form-control" id="remMsgc"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Previous Remark</label>
                            <div class="col-md-9">
                                <textarea disabled class="form-control" id="remarkc"></textarea>
                            </div>
                        </div>
                      
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" id="btnSnooze" onclick="snooze()" class="btn btn-primary"> <i class="fa fa-clock-o"></i> Snooze/Assign</button> -->
                <button type="button" id="btnCompclient" class="btn btn-success"><i class="fa fa-check"></i> Approve</button>
                <!-- <button type="button" id="btnMail" class="btn btn-sky"><i class="fa fa-envelope"></i> Send Mail</button>
                <button type="button" id="btnSMS" class="btn btn-midnightblue"><i class="fa fa-mobile"></i> Send SMS</button> -->
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Decline</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Bootstrap modal - Snooze REMINDER -->
<div class="modal fade" id="assign_form1" role="dialog" style="z-index: 10">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="assign_modal_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_assign1" class="form-horizontal" data-validate="parsley">
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
                                <input type="text" id="remSnoozeDate" class="form-control remdate"  data-inputmask="'alias':'date'"  required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Assign to</label>
                            <div class="col-md-9">
                                <select name="concern_user" class="selection" required="required" id="concern_user_assign" style="width: 100%" tabindex="6">
                                    <option disabled selected value="">Select User</option>
                                    <option value="<?=$this->session->userdata('username');?>">SELF</option>
                                    <option value="all">All Users</option>
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
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    /* apply masks to inputs */
    $(document).ready(function() {
        //debugger;
        $('.remdate').datepicker({format: 'dd/mm/yyyy', startDate:'<?=date('d-m-Y');?>'}).inputmask();
        //$('.mask').inputmask();
        $('.selection').select2({width: 'resolve', maximumSelectionSize: 1});
        load_notif();
        get_rem_users();
    });

    function add_reminder_dialog(id)
    {
        $('#form_add_rem1')[0].reset(); // reset form on modals
        $('#client_id').select2("val","");
        $('#reminder_date').val("<?=date('d-m-Y');?>");
        $('#reminder_date').datepicker("setDate", "<?=date('d-m-Y');?>");
        $('#reminder_date').datepicker('update');
        $('#message').val("");
        $('#remark').val("");
        $('#add_rem_modal1').modal('show'); // show bootstrap modal when complete loaded
        $('#add_rem_title').text('Add Reminder'); // Set title to Bootstrap modal title
    }

    //add reminder Save
    $('#btnRemSave').click(function(){
        if($('#form_add_rem1').parsley('validate'))
        {
            $.ajax({
                url: '<?php echo site_url('broker/Reminder_analyzer/add_reminder')?>',
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
                        load_notif();

                        if(typeof table_rem != 'undefined') {
                            if(radio_status == 'completed') {
                                table_rem = ajax_reminder_list_complete();
                            } else if(radio_status == 'pending') {
                                table_rem = ajax_reminder_list_pending();
                            }
                            //table_initialize();
                        }
                        if(typeof table_rem_p != 'undefined') {
                            table_rem_p.destroy();
                            table_rem_p = ajax_reminder_list();
                        }
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

    //snooze reminder Save
    $('#btnSave').click(function(){
        if($('#form_assign1').parsley('validate'))
        {
            var remarks = $("#remark").val();
            if(remarks != null && remarks != '') {
                remarks += ' ----- '+$("#remRemark").val();
            } else {
                remarks = $("#remRemark").val();
            }
            $.ajax({
                url: '<?php echo site_url('broker/Clients_reminders/snooze')?>',
                type:'post',
                data: {'remID': $('#remAssignID').val(), 'remarks': remarks, 'snoozeDate': $("#remSnoozeDate").val(), 'remDate': $("#remDate").val(), 'concern_user': $("#concern_user_assign").val()},
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
                        $("#assign_form1").modal('hide');
                        $("#remark").val(remarks);
                        load_notif();

                        if(typeof table_rem != 'undefined') {
                            if(radio_status == 'completed') {
                                table_rem = ajax_reminder_list_complete();
                            } else if(radio_status == 'pending') {
                                table_rem = ajax_reminder_list_pending();
                            }
                            //table_initialize();
                        }
                        if(typeof table_rem_p != 'undefined') {
                            table_rem_p.destroy();
                            table_rem_p = ajax_reminder_list();
                        }
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

    //complete reminder
    $('#btnComp').click(function(){
        bootbox.confirm('Are you sure you want to complete this reminder?', function(result){
            if(result)
            {
                $.ajax({
                    url: '<?php echo site_url('client/Clients_reminders/complete')?>',
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
                            load_notif();

                            if(typeof table_rem != 'undefined') {
                                if(radio_status == 'completed') {
                                    table_rem = ajax_reminder_list_complete();
                                } else if(radio_status == 'pending') {
                                    table_rem = ajax_reminder_list_pending();
                                }
                                //table_initialize();
                            }
                            if(typeof table_rem_p != 'undefined') {
                                table_rem_p.destroy();
                                table_rem_p = ajax_reminder_list();
                            }
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


        //Approve reminder
        $('#btnCompclient').click(function(){

            bootbox.confirm('Are you sure you want to Approve This Document?', function(result){
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/Clients_reminders/approve')?>',
                        type:'post',
                        data: {'remIDc': $('#remIDc').val(),'client_id':$('#client_idc').val(),'type':$('#remMsgc').val()},
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
                                load_notif();

                                if(typeof table_rem != 'undefined') {
                                    if(radio_status == 'completed') {
                                        table_rem = ajax_reminder_list_complete();
                                    } else if(radio_status == 'pending') {
                                        table_rem = ajax_reminder_list_pending();
                                    }
                                    //table_initialize();
                                }
                                if(typeof table_rem_p != 'undefined') {
                                    table_rem_p.destroy();
                                    table_rem_p = ajax_reminder_list();
                                }
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
                            // setTimeout(function(){location.reload();},1000);
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


    //send mail
    $('#btnMail').click(function(){
        bootbox.confirm('Are you sure you want to send mail to the client?', function(result){
            if(result)
            {
                $.ajax({
                    url: '<?php echo site_url('broker/clients_reminders/send_mail')?>',
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

                            if(typeof table_rem != 'undefined') {
                                if(radio_status == 'completed') {
                                    table_rem = ajax_reminder_list_complete();
                                } else if(radio_status == 'pending') {
                                    table_rem = ajax_reminder_list_pending();
                                }
                                //table_initialize();
                            }
                            if(typeof table_rem_p != 'undefined') {
                                table_rem_p.destroy();
                                table_rem_p = ajax_reminder_list();
                            }
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

    //send SMS
    $('#btnSMS').click(function(){
        bootbox.alert('Not Integrated Yet?');
    });

    function snooze()
    {
        $("#assign_form1").modal('show');
        //reset form on modals
        $("#form_assign1")[0].reset();
        $('#remSnoozeDate').val("<?=date('d-m-Y');?>");
        $('#remSnoozeDate').datepicker("setDate", "<?=date('d-m-Y');?>");
        $('#remSnoozeDate').datepicker("update");
        $('#remAssignID').val($('#remID').val());
        $('#concern_user').select2('val','');
        //show bootstrap modal
        $("#assign_form1").modal('show');
        //set title to modal
        $("#assign_modal_title").text('Snooze/Assign Window');
    }

    function view_reminder(id)
    {
        $('#form1')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('client/Clients_reminders/get_reminder_details');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                debugger;
                $('#remID').val(data.reminder_id);
                $('#remDate').val(data.reminder_date);
                $('#remSub').val(data.reminder_type);
                $('#remClientName').val(data.client_name);
                $('#remMsg').val(data.reminder_message);
                $('#remark').val(data.remark);
                $('#remAttachment').attr("src",data.attachment_url);
                
                $('#modal_form1').modal('show'); // show bootstrap modal when complete loaded
                $('#rem_modal_title').text('View '); // Set title to Bootstrap modal title
                $('#modal_form1').on('shown.bs.modal', function (e) {
                  $(body).removeClass('modal-open');
                  $('#modal_form1').addClass('modal-open');
                });

            },
            error: function (data)
            {
              console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }



    //refresh the header notifications/reminders
    function load_notif() {
        $.ajax({
            url: '<?php echo site_url('client/Dashboard/update_notif')?>',
            //type:'post',
            //data: {'clientID': $('#client_id').val(), 'clientName': $("#client_id option:selected").text(), 'msg': $("#message").val(), 'remDate': $("#reminder_date").val(), 'concern_user': $("#concern_user").val()},
            dataType: 'html',
            success:function(data)
            {
                $("#top-notif").empty();
                $("#top-notif").html(data);
            },
            error: function (data)
            {
                console.log('Error while fetching top reminder!!')
                console.log(data);
                //bootbox.alert('Error while fetching top reminder!!');
            }
        });
    }

    //get users list for adding/assigning concern_user
    function get_rem_users() {
        $.ajax({
            url: '<?php echo site_url('broker/Reminder_analyzer/get_reminder_users')?>',
            //type:'post',
            //data: {'clientID': $('#client_id').val(), 'clientName': $("#client_id option:selected").text(), 'msg': $("#message").val(), 'remDate': $("#reminder_date").val(), 'concern_user': $("#concern_user").val()},
            dataType: 'json',
            success:function(data)
            {
                if(data != false) {
                    var option = "";
                        $.each(data, function(i, item){
                        option = option + "<option value="+data[i].username+">"+data[i].name+"</option>";
                    });
                    $("[name=concern_user]").append(option);
                }
            },
            error: function (data)
            {
                console.log('Error while getting reminder users!!')
                console.log(data);
                //bootbox.alert('Error while fetching top reminder!!');
            }
        });
    }
</script>
