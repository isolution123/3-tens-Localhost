<!-- Bootstrap modal -->
<div class="modal fade" id="user_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 id="user_title" class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="user_form" class="form-horizontal">
                    <div class="form-body">
                        <input name="id" id="userID" type="hidden">
                        <input name="user_type" id="userType" value="user" type="hidden">
                        <div class="form-group">
                            <label class="control-label col-md-3">Name</label>
                            <div class="col-md-6">
                                <input name="name" id="userName" placeholder="Full Name" required class="form-control" type="text" data-maxlength="40">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Mobile No.</label>
                            <div class="col-md-6">
                                <input name="mobile" id="userMobile" placeholder="Mobile No." required class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Email ID</label>
                            <div class="col-md-6">
                                <input name="email_id" id="userEmailID" placeholder="Email ID" required class="form-control" type="text" data-type="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Username</label>
                            <div class="col-md-6">
                                <input name="username" id="userUsername" placeholder="Username for login" required class="form-control" type="text" data-minlength="4" data-maxlength="40">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Password</label>
                            <div class="col-md-6">
                                <input name="password" id="userPass" placeholder="Password" required class="form-control" type="password" data-minlength="5">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Confirm Password</label>
                            <div class="col-md-6">
                                <input name="password2" id="userPass2" placeholder="Password" required class="form-control" type="password" data-minlength="5" data-equalto="#userPass">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Additional Info</label>
                            <div class="col-md-6">
                                <input name="add_info" id="add_info" placeholder="Additional Information" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Status</label>
                            <div class="col-md-6">
                                <select name="status" id="select-status" required="required" style="width: 100%">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Permission</label>
                            <div class="col-md-6">
                               <div class="radio-block" style="margin-top:9px;">
                                    <label><input type="radio" name="permissions" id="1" value="1" checked="checked">Read</label><span style="margin-right: 30px;"></span>
                                    <label><input type="radio" name="permissions" id="2" value="2">Read Write</label><span style="margin-right: 30px;"></span>
                                    <label><input type="radio" name="permissions" id="3" value="3">All</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_user()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
    var save_method; //for save method string

    function add_user()
    {
        //initialize status in select2
        $('#select-status').select2({
            width: 'resolve',
            placeholder:"Please select Status"
        });

        save_method = 'add';
        //reset form on modals
        $("#user_form")[0].reset();
        //show bootstrap modal
        $("#user_modal_form").modal('show');
        //set title to modal
        $("#user_title").text('Add New User');

        //disable password fields
        $('[name="password"]').prop("disabled",false);
        $('[name="password2"]').prop("disabled",false);
    }

    function edit_user(id)
    {
        //debugger;
        save_method = 'update';
        $('#user_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Users/edit_user');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id"]').val(data.id);
                $('[name="name"]').val(data.name);
                $('[name="mobile"]').val(data.mobile);
                $('[name="email_id"]').val(data.email_id);
                $('[name="username"]').val(data.username);
                $('[name="status"]').val(data.status);
                $('[name="add_info"]').val(data.add_info);
                $('[name="password"]').val('*********');
                $('[name="password2"]').val('*********');
                $("[name='permissions'").prop("checked",false);
                $('#'+data.permissions).prop('checked',true);
                //disable password fields
                $('[name="password"]').prop("disabled",true);
                $('[name="password2"]').prop("disabled",true);

                //initialize status in select2
                $('#select-status').select2({
                    width: 'resolve',
                    placeholder:"Please select Status"
                });


                $('#user_modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#user_title').text('Edit User'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function save_user()
    {
        var url;
        if(save_method == 'add')
            url = "<?php echo site_url('broker/Users/add_user');?>";
        else
            url = "<?php echo site_url('broker/Users/update_user');?>";

        var valid = $('#user_form').parsley( 'validate' );
        if(valid) {
            var userName = $("#userName").val();
            $("#userName").removeClass('has-error');
            $.ajax({
                url: url,
                type:'post',
                data: $("#user_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#user_modal_form").modal('hide');

                    var count = Object.keys(data).length;
                    console.log(count);
                    console.log(data);
                    if(count < 3) {
                        $.each(data, function(i, item) {
                            $.pnotify({
                                title: data[i]['title'],
                                text: data[i]['text'],
                                type: data[i]['type'],
                                history: true
                            });
                        });
                    } else {
                        $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                history: true
                            });
                    }

                    if(data['type'] ==  "success" || data[0]['type'] == "success") {
                        // if there is master table and it needs refreshing
                        if(typeof table != 'undefined') {
                            table.destroy();
                            table = ajax_users_list();
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert('Error adding / updating data');
                }
            });
        }
    }
</script>
