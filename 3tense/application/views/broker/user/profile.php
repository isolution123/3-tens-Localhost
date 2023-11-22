<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li class="active">Your Profile</li>
            </ol>
            <h1>Your Profile</h1>
        </div>
        <div class="container">
            <input type="hidden" id="withdrawFundID" value="0">
            <form action="#" id="profile_form" method="post" class="form-horizontal row-border" data-validate="parsley" enctype="multipart/form-data">
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Profile Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <input name="id" id="userID" type="hidden" value="<?php echo isset($info->id)?$info->id:'';?>">
                        <?php /*<input name="user_type" id="userType" value="user" type="hidden">*/ ?>
                        <div class="form-group">
                            <label class="control-label col-md-3">Name</label>
                            <div class="col-md-6">
                                <input name="name" id="userName" placeholder="Full Name" required class="form-control" type="text" data-maxlength="40"
                                    value="<?php echo isset($info->name)?$info->name:'';?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Mobile No.</label>
                            <div class="col-md-6">
                                <input name="mobile" id="userMobile" placeholder="Mobile No." required class="form-control" type="text"
                                       value="<?php echo isset($info->mobile)?$info->mobile:'';?>">
                            </div>
                        </div>
                        <?php if($this->session->userdata('user_id') == $this->session->userdata('broker_id')) { ?>
                            <div class="form-group">
                                <label class="control-label col-md-3">Email ID</label>
                                <div class="col-md-6">
                                    <input name="email_id" id="userEmailID" placeholder="Email ID" required class="form-control" type="text" data-type="email"
                                           value="<?php echo isset($info->email_id)?$info->email_id:'';?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Username</label>
                                <div class="col-md-6">
                                    <input name="username" id="userUsername" placeholder="Username for login" required class="form-control" type="text" data-minlength="4" data-maxlength="40"
                                           value="<?php echo isset($info->username)?$info->username:'';?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="control-label col-md-9">If you want to change your password, please fill the Password fields below.</label><br/><br/>
                            <div class="row">
                                <label class="control-label col-md-3">Old Password</label>
                                <div class="col-md-6">
                                    <input name="old_password" id="userOldPass" placeholder="Your Existing Password" class="form-control" type="password" data-minlength="5">
                                </div>
                            </div><br/>
                            <div class="row">
                                <label class="control-label col-md-3">New Password</label>
                                <div class="col-md-6">
                                    <input name="password" id="userPass" placeholder="New Password" class="form-control" type="password" data-minlength="5">
                                </div>
                            </div><br/>
                            <div class="row">
                                <label class="control-label col-md-3">Confirm Password</label>
                                <div class="col-md-6">
                                    <input name="password2" id="userPass2" placeholder="Confirm New Password" class="form-control" type="password" data-minlength="5" data-equalto="#userPass">
                                </div>
                            </div>
                        </div>
                        <?php if($this->session->userdata('user_id') == $this->session->userdata('broker_id')) { ?>
                            <!--<div class="form-group">
                                <label class="control-label col-md-3">Status</label>
                                <div class="col-md-6">
                                    <select name="status" id="select-status" class="populate" required="required" style="width: 100%">
                                        <option value="1" selected="selected">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <label class="col-md-3 control-label">Reports Image (Logo)</label>
                                <div class="col-md-6">
                                    <?php if(isset($info->image)) { ?>
                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                        <input type="hidden" value name />
                                        <div class="fileinput-preview fileinout-exists thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;" id="imagePreview">
                                            <img src="<?=base_url().$info->image;?>"/>
                                        </div>
                                        <?php } else { ?>
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;" id="imagePreview"></div>
                                            <?php } ?>
                                            <div>
                                                <span class="btn btn-default btn-file"><span class="fileinput-new">Upload image</span><span class="fileinput-exists">Change</span><input type="file" id="image" name="image" value="<?php if(isset($client_info->photo)) { echo basename($info->image); } ?>"></span>
                                                <a href="#" class="btn btn-default fileinput-exists btn-danger" data-dismiss="fileinput">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--@pallavi 06-sep-2017-->
                                <div class="row">
                                    <label class="control-label col-md-3">CAMS RTA Password</label>
                                    <div class="col-md-6">
                                        <input type="text" name="cams_rta_password" id="cams_rta_password" placeholder="CAMS RTA password" class="form-control"   value="<?php echo isset($info->cams_rta_password)?$info->cams_rta_password:'';?>">
                                    </div>
                                </div><br/>
                                  <div class="row">
                                    <label class="control-label col-md-3">KARVY RTA Password</label>
                                    <div class="col-md-6">
                                        <input type="text" name="karvy_rta_password" id="karvy_rta_password" placeholder="KARVY RTA password" class="form-control"  value="<?php echo isset($info->karvy_rta_password)?$info->karvy_rta_password:'';?>">
                                    </div>
                                </div><br/>
                                <div class="row">
                                    <label class="control-label col-md-3">Mail Id For mailback Service</label>
                                    <div class="col-md-6">
                                        <input type="text" name="mailback_mail" id="mailback_mail" placeholder="Mailback Mail Id" data-type="email" class="form-control"  value="<?php echo isset($info->mailback_mail)?$info->mailback_mail:'';?>">
                                    </div>
                                </div><br/>
                                  <!-- end @pallavi 06-sep-2017-->
                            </div>
                        <?php } ?>
                        <br/><br/>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-16" style="text-align: center">
                                    <button type="button" id="save" onclick="profile_submit()"  data-style="expand-left" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Save</button>
                                    <button type="button" id="cancel" onclick="window.location.href = '<?=base_url('broker/Dashboard');?>';" class="btn btn-danger" style="margin-left: 5%;"><i class="fa fa-ban"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<script type="text/javascript">
   $(function() {
        disableBtn();
        $('.disabled').attr('disabled','disabled');
        $('.populate').select2({width: 'resolve'});
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
        $('.mask').inputmask();
    });

    //update user/broker details in database
    function profile_submit()
    {
        var valid = $("#profile_form").parsley("validate");
        var password = true;
        if($("#userOldPass").val() != "" && ($("#userPass").val() == "" || $("#userPass2").val() == "")) {
            bootbox.alert("Please enter a new password if you have to change your old password, or else keep Old Password field blank");
            password = false;
        }
        if($("#userOldPass").val() == "" && ($("#userPass").val() != "" || $("#userPass2").val() != "")) {
            bootbox.alert("Please enter your old password in the required field if you wish to change your password, or else keep Password fields blank");
            password = false;
        }
        if(valid && password)
        {
            var form_data = new FormData($('#profile_form')[0]);
            $.ajax({
                url: '<?php echo site_url('broker/Users/update_profile');?>',
                type:'post',
                data: form_data,
                dataType: 'json',
                contentType: false,
                processData: false,
                success:function(data)
                {
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });

                    /* below lines commented, uncomment if required - used for redirecting to dashboard
                    if(data['type'] ==  "success") {
                        // if there is master table and it needs refreshing
                        window.location.href = "<?php echo base_url('broker/Dashboard')?>";
                    } */
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