<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ul class="breadcrumb">
                <li><a href="<?php echo base_url('broker/dashboard');?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Clients');?>">Client Master</a></li>
                <li class="active">Add Client</li>
            </ul>

            <h1>Add Client</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-primary control-form">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#personal" data-toggle="tab">Personal info</a></li>
                                <!-- <li><?php if($action=='add') { ?>
                                    <a href="#">Financial details</a>
                                    <?php } else { ?>
                                        <a href="#others" data-toggle="tab">Financial details</a>
                                    <?php } ?>
                                </li> -->
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active form-no-border" id="personal">
                                    <form action="<?php echo base_url('broker/Clients/save')?>" class="form-horizontal row-border" method="post" data-validate="parsley" id="validate-form" enctype="multipart/form-data">
                                        <input type="hidden" name="action" id="action" value="<?=$action; ?>" />
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Family Name</label>
                                                <div class="col-sm-6 add-new-btn">
                                                    <select id="select-family" name="family_id" style="width:80%" class="select2" required="required">
                                                        <option value="0" selected disabled>Please select a family</option>
                                                        <?php foreach($families as $row):?>
                                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="name" required="required" class="form-control" id="name">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Type</label>
                                                <div class="col-sm-6 add-new-btn">
                                                    <select id="select-clientType" name="client_type" style="width:80%" class="select2" required="required">
                                                        <option value="0" selected disabled>Please select a client type</option>
                                                        <?php foreach($client_types as $row):?>
                                                            <option value='<?php echo $row->client_type_id; ?>'><?php echo $row->client_type_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_client_type()"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Occupation</label>
                                                <div class="col-sm-6 add-new-btn">
                                                    <select id="select-occupation" name="occupation_id" style="width:80%" class="select2" required="required">
                                                        <option value="0" selected disabled>Please select an occupation</option>
                                                        <?php foreach($occupations as $row):?>
                                                            <option value='<?php echo $row->occupation_id; ?>'><?php echo $row->occupation_name; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_occupation()"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="radio" class="col-sm-3 control-label">Head of Family?</label>
                                                <div class="col-sm-6">
                                                    <div class="radio block">
                                                        <label><input type="radio" name="head_of_family" value="1"> Yes</label><span style="margin-right: 30px;"></span>
                                                        <label><input type="radio" name="head_of_family" value="0" checked="checked"> No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Relation w/ HOF</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="relation_HOF" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Report Order</label>
                                                <div class="col-sm-3">
                                                    <input type="text" name="report_order" data-type="number" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Date of birth</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <input type="checkbox" name="dob_app" id="dob-app">
                                                        </span>
                                                        <span class="input-group date">
                                                            <input type="text" id="dob" name="dob" class="form-control mask dob-datepicker" data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Anniversary</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <input type="checkbox" name="anv_app" id="doa-app">
                                                        </span>
                                                        <span class="input-group date">
                                                            <input type="text" id="doa" name="anv_date" class="form-control mask dob-datepicker" data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Spouse Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="spouse_name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Children Name/s</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="children_name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client ID</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="client_id" class="form-control" id="clientID" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">House/Flat No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_flat" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Street</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_street" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Area</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_area" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">City</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_city" class="form-control" data-maxlength="50" required="required">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">State</label>
                                                <div class="col-sm-6">
                                                    <select id="select-state" name="add_state" style="width:100%" class="select2" required="required">
                                                        <option value="0" selected disabled>Please select a state</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Pincode</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_pin" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Mobile</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="mobile" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Telephone</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="telephone" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email ID</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="email_id" data-type="email"  class="form-control" id="email_id">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Username</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="username" data-maxlength="30" data-minlength="5" required="required" class="form-control" id="username">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                               <label class="col-sm-3 control-label">Password</label>
                                               <div class="col-sm-6">
                                                   <input type="text" name="password" data-maxlength="30" data-minlength="5"  class="form-control" id="password">
                                               </div>
                                           </div>
                                             
                                        
                                           
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">PAN Card No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="pan_no" data-regexp="[A-Z]{5}[0-9]{4}[A-Z]{1}" required="required" class="form-control mask tooltips" data-inputmask="'mask':'AAAAA9999A'" data-trigger="hover" data-original-title="PAN No. format : ABCDE1234A" id="pan_no">
                                                </div>
                                            </div>
                                            <!--<div class="form-group">-->
                                            <!--    <label class="col-sm-3 control-label">Passport No.</label>-->
                                            <!--    <div class="col-sm-6">-->
                                            <!--        <input type="text" name="passport_no" data-regexp="[A-Z]{1}[0-9]{7}" class="form-control mask tooltips"  data-inputmask="'mask':'A9999999'" data-trigger="hover" data-original-title="Passport No. format : A1234567">-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Aadhar No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="passport_no" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Commencement Date</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group date">
                                                            <input type="text" name="date_of_comm" id="doc" class="form-control mask datepicker" value="<?php echo date('d/m/Y');?>" data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Category</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="client_category" class="form-control" data-maxlength="50">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Status</label>
                                                <div class="col-sm-6">
                                                    <select class="select2" id="select-status" name="status">
                                                        <option value="0">Inactive</option>
                                                        <option value="1" selected="selected">Active</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Photo</label>
                                                <div class="col-sm-6">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 150px; height: 150px;" id="photoPreview"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Upload photo</span><span class="fileinput-exists">Change</span><input type="file" id="photo" name="photo"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists btn-danger" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Signature</label>
                                                <div class="col-sm-6">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 150px; height: 100px;" id="signPreview"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Upload signature</span><span class="fileinput-exists">Change</span><input type="file" id="sign" name="sign"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists btn-danger" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="panel-footer">-->
                                            <!--<div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <div class="btn-toolbar">
                                                        <button type="submit" class="btn-primary btn" onclick="javascript:$('#validate-form').parsley( 'validate' );">Submit</button>
                                                        <button type="button" class="btn-default btn" onclick="javascript:window.location.href='<?php echo base_url();?>broker/Clients';">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <!--</div>-->
                                    </form>
                                </div>
                                <div class="tab-pane" id="others">
                                    <div class="row">
                                        <div class="col-md-6">

                                        </div>
                                    </div>
                                </div>
                                <div class="bottom-row navbar-fixed-bottom">
                                    <div class="col-sm-12 bottom-col">
                                        <button type="button" id="add" onclick="errorSaveFirst()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                                        <button type="button" id="edit" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" id="delete" onclick="errorSaveFirst()"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                                        <button type="button" id="save" onclick="submit_form_info()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                                        <button type="button" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
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
    /*********************************/
    /* AJAX functions - loading data */
    /*********************************/

    /* load family dropdown data */
    /* now we are loading data directly by passing it to the view
    $.ajax({
        url: "<?php //echo base_url('broker/Clients/families_list_dropdown')?>",
        type: "post",
        dataType: "json",
        success: function(json) {
            $.each(json, function(){
                $('#select-family').append('<option value="' + this.family_id + '">' + this.name + '</option>');
            });
        },
        error: function(json) {
            bootbox.alert("Could not load family data! Please check console log.");
            console.log(json);
        }
    }); */

    /* load client types data */
    /* now we are loading data directly by passing it to the view */
    function ajax_load_client_types(id)
    {
        $.ajax({
            url: "<?php echo base_url('broker/Clients/client_types_dropdown')?>",
            type: "post",
            dataType: "json",
            success: function(json) {
                $.each(json, function(){
                    $('#select-clientType').append('<option value="' + this.client_type_id + '">' + this.client_type_name + '</option>');
                });
                $("#select-clientType").select2("val", id);
            },
            error: function(json) {
                bootbox.alert("Could not load client types data! Please check console log.");
                console.log(json);
            }
        });
    }

    /* load occupation data */
    /* now we are loading data directly by passing it to the view */
    function ajax_load_occupations(id)
    {
        $.ajax({
            url: "<?php echo base_url('broker/Clients/occupations_dropdown')?>",
            type: "post",
            dataType: "json",
            success: function(json) {
                $.each(json, function(){
                    $('#select-occupation').append('<option value="' + this.occupation_id + '">' + this.occupation_name + '</option>');
                });
                $("#select-occupation").select2("val", id);
            },
            error: function(json) {
                bootbox.alert("Could not load occupations data! Please check console log.");
                console.log(json);
            }
        });
    }

    /* get generated new client ID */
    $.ajax({
        url: "<?php echo base_url('broker/Clients/new_client_id')?>",
        type: "post",
        dataType: "json",
        success: function(json) {
            $("#clientID").val(json.client_id);
            /* call function to show signature, if it exists */
            getSignature();
            getPhoto();
        },
        error: function(json) {
            bootbox.alert("Could not load client types data! Please check console log.");
            console.log(json);
        }
    });

    // load the states file into select box */
    var states_file = "<?php echo base_url('assets/states.json'); ?>";
    $.getJSON(states_file, function(json) {
        //console.log(json);
        $.each(json, function(key, val){
            //console.log(val);
            $('#select-state').append('<option value="' + val + '">' + val + '</option>');
        });
    }).fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ', ' + error;
            console.log( "Request Failed: " + err);
    });

    /*********************************/
        /* AJAX functions END */
    /*********************************/


    $('.select2').select2();

    //$('.dob-datepicker').datepicker({format:"dd/mm/yyyy", startView: 2});
    $('.dob-datepicker').datepicker({format:"dd/mm/yyyy"});
    $('.datepicker').datepicker({format:"dd/mm/yyyy"});

    /* disable/enable DOB/DOA applicable on checkbox */
    $('#dob-app').on("change", function() {
        if($(this).prop("checked")) {
            $('#dob').prop("disabled", false);
            $("#dob").removeClass("disabled");
        } else {
            $("#dob").val("");
            $("#dob").addClass("disabled");
            $('#dob').prop("disabled", true);
        }
    }).change();
    $('#doa-app').on("change", function() {
        if($(this).prop("checked")) {
            $('#doa').prop("disabled", false);
            $("#doa").removeClass("disabled");
        } else {
            $("#doa").val("");
            $("#doa").addClass("disabled");
            $('#doa').prop("disabled", true);
        }
    }).change();


    /* apply masks to inputs */
    $('.mask').inputmask();

    $('.tooltips').tooltip(); //bootstrap's tooltip



    $(document).ready(function() {
        disableBtn();
        $('#dob').prop("disabled", true);
        $('#dob').prop("required", false);
        $('#doa').prop("disabled", true);
        $('#doa').prop("required", false);
        $('.date').datepicker('remove');
        $('.date').datepicker({format: 'dd/mm/yyyy', endDate:'<?=date('d-m-Y');?>'}).inputmask();
    });

    /* function to get signature from the server file, if exists */
    function getSignature() {
        //debugger;
        var clientID = $("#clientID").val();
        var path = "uploads/"+clientID+"/Signature";
        $.ajax({
            url: "<?php echo base_url('broker/Clients/getSignatureFile')?>",
            data: {"clientID":clientID},
            type: "POST",
            success: function(data) {
                //alert(data);
                if(data) {
                    $("#signPreview").attr("src","<?php echo base_url();?>"+data);
                }
            },
            error: function(data) {
                bootbox.alert("Error while getting signature!   "+data);
                console.log(data);
            }
        });
    }

    /* function to get client photo from the server file, if exists */
    function getPhoto() {
        //debugger;
        var clientID = $("#clientID").val();
        var path = "uploads/"+clientID+"";
        $.ajax({
            url: "<?php echo base_url('broker/Clients/getPhotoFile')?>",
            data: {"clientID":clientID},
            type: "POST",
            success: function(data) {
                //alert(data);
                if(data) {
                    $("#photoPreview").html("<img src='<?php echo base_url();?>"+data+"'");
                }
            },
            error: function(data) {
                bootbox.alert("Error while getting photo!   "+data);
                console.log(data);
            }
        });
    }

    function submit_form_info()
    {
        $("#validate-form").parsley("destroy");
        //check for dob and doa
        if($("#dob-app").prop("checked")) {
            $("#dob").prop("required",true);
            $('#validate-form').parsley('addItem', '#dob');
            $("#dob").parsley('addConstraint',{required: true});
        } else {
            $("#dob").parsley('removeConstraint','required');
            //$('#validate-form').parsley('removeItem', '#dob');
        }
        if($("#doa-app").prop("checked")) {
            $("#doa").prop("required",true);
            $('#validate-form').parsley('addItem', '#doa');
            $("#doa").parsley('addConstraint',{required: true});
        } else {
            $("#doa").parsley('removeConstraint','required');
            //$('#validate-form').parsley('removeItem', '#doa');
        }

        var valid = $("#validate-form").parsley("validate");
        if(valid) {
            var family_id = $("#select-family").val();
            var name = $("#name").val();
            var client_id = $("#clientID").val();
            var email_id = $("#email_id").val();
            var username = $("#username").val();
            var pan_no = $("#pan_no").val();
            //send data to check duplicacy
            $.ajax({
                url: "<?php echo base_url('broker/Clients/check_duplicate_values')?>",
                data: {"family_id":family_id, "name":name, "email_id":email_id, "username":username, "pan_no":pan_no, "client_id":client_id},
                type: "POST",
                dataType: "json",
                success: function(data) {
                    //alert(data);
                    console.log(data);
                    if(data == 'ok') {
                        $("#validate-form").submit();
                    } else {
                        $.each(data, function(i, item){
                            $.pnotify({
                                title: data[i].title,
                                text: data[i].text,
                                type: data[i].type
                            });
                        });
                    }
                },
                error: function(data) {
                    bootbox.alert("Error while checking for duplicate values!   "+data);
                    console.log(data);
                }
            });

        }
    }

</script>
