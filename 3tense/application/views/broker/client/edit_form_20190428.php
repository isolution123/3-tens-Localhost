<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ul class="breadcrumb">
                <li><a href="<?php echo base_url('broker/dashboard');?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Clients');?>">Client Master</a></li>
                <li class="active">Edit Client</li>
            </ul>

            <h1>Edit Client</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-primary control-form">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#personal" data-toggle="tab">Personal info</a></li>
                                <li><?php if($action=='add') { ?>
                                    <a href="#">Financial details</a>
                                    <?php } else { ?>
                                        <a href="#others" data-toggle="tab">Financial details</a>
                                    <?php } ?>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active form-no-border" id="personal">
                                    <form action="<?php echo base_url('broker/Clients/save')?>" class="form-horizontal row-border" method="post" data-validate="parsley" id="validate-form" enctype="multipart/form-data">
                                        <input type="hidden" name="action" id="action" value="<?=$action; ?>" />
                                        <input type="hidden" name="hidden_family" id="hidden_family" value="<?=$client_info->family_id; ?>" />
                                        <input type="hidden" name="hidden_client_type" id="hidden_client_type" value="<?=$client_info->client_type; ?>" />
                                        <input type="hidden" name="hidden_occupation" id="hidden_occupation" value="<?=$client_info->occupation_id; ?>" />
                                        <input type="hidden" name="hidden_state" id="hidden_state" value="<?=$client_info->add_state; ?>" />
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
                                                    <input type="text" name="name" required="required" value="<?php echo isset($client_info->name)?$client_info->name:'';?>" class="form-control" id="name">
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
                                                        <label><input type="radio" name="head_of_family" value="1" <?php if(isset($client_info->head_of_family) && ($client_info->head_of_family === '1')) { ?> checked="checked" <?php } ?>> Yes</label><span style="margin-right: 30px;"></span>
                                                        <label><input type="radio" name="head_of_family" value="0" <?php if(isset($client_info->head_of_family) && ($client_info->head_of_family === '0')) { ?> checked="checked" <?php } ?>> No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Relation w/ HOF</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="relation_HOF" value="<?php echo isset($client_info->relation_HOF)?$client_info->relation_HOF:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Report Order</label>
                                                <div class="col-sm-3">
                                                    <input type="text" name="report_order" data-type="number" value="<?php echo isset($client_info->report_order)?$client_info->report_order:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Date of birth</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <input type="checkbox" name="dob_app" id="dob-app" <?php if(isset($client_info->dob) && !empty($client_info->dob)) { ?> checked="checked" <?php } ?>>
                                                        </span>
                                                        <span class="input-group date">
                                                            <input type="text" id="dob" name="dob" class="form-control mask dob-datepicker" <?php echo isset($client_info->dob)?'value="'.$client_info->dob.'"':'disabled="disabled"';?> data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Anniversary</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <input type="checkbox" name="anv_app" id="doa-app" <?php if(isset($client_info->anv_date) && !empty($client_info->anv_date)) { ?> checked="checked" <?php } ?>>
                                                        </span>
                                                        <span class="input-group date">
                                                            <input type="text" id="doa" name="anv_date" class="form-control mask dob-datepicker" <?php echo isset($client_info->anv_date) && !empty($client_info->anv_date)?'value="'.$client_info->anv_date.'"':'disabled="disabled"';?> data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Spouse Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="spouse_name" value="<?php echo isset($client_info->spouse_name)?$client_info->spouse_name:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Children Name/s</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="children_name" value="<?php echo isset($client_info->children_name)?$client_info->children_name:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client ID</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="client_id" value="<?php echo isset($client_info->client_id)?$client_info->client_id:'';?>" class="form-control" id="clientID" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">House/Flat No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_flat" value="<?php echo isset($client_info->add_flat)?$client_info->add_flat:'';?>" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Street</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_street" value="<?php echo isset($client_info->add_street)?$client_info->add_street:'';?>" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Area</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_area" value="<?php echo isset($client_info->add_area)?$client_info->add_area:'';?>" class="form-control" data-maxlength="150">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">City</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="add_city" value="<?php echo isset($client_info->add_city)?$client_info->add_city:'';?>" class="form-control" data-maxlength="50" required="required">
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
                                                    <input type="text" name="add_pin" value="<?php echo isset($client_info->add_pin)?$client_info->add_pin:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Mobile</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="mobile"value="<?php echo isset($client_info->mobile)?$client_info->mobile:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Telephone</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="telephone" value="<?php echo isset($client_info->telephone)?$client_info->telephone:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email ID</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="email_id" value="<?php echo isset($client_info->email_id)?$client_info->email_id:'';?>" data-type="email" class="form-control" id="email_id">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Username</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="username" value="<?php echo isset($client_info->username)?$client_info->username:'';?>" data-maxlength="30" data-minlength="5" required="required" class="form-control" id="username">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Password</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="password" value="" data-maxlength="30" data-minlength="5"  class="form-control" id="password" placeholder="<?php if($client_info->password!=""){echo "**********";}else{echo "";}?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">PAN Card No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="pan_no" value="<?php echo isset($client_info->pan_no)?$client_info->pan_no:'';?>" data-regexp="[A-Z]{5}[0-9]{4}[A-Z]{1}" required="required" class="form-control mask tooltips" data-inputmask="'mask':'AAAAA9999A'" data-trigger="hover" data-original-title="PAN No. format : ABCDE1234A" id="pan_no">
                                                </div>
                                            </div>
                                            <!--<div class="form-group">-->
                                            <!--    <label class="col-sm-3 control-label">Passport No.</label>-->
                                            <!--    <div class="col-sm-6">-->
                                            <!--        <input type="text" name="passport_no" value="<?php echo isset($client_info->passport_no)?$client_info->passport_no:'';?>" data-regexp="[A-Z]{1}[0-9]{7}" class="form-control mask tooltips"  data-inputmask="'mask':'A9999999'" data-trigger="hover" data-original-title="Passport No. format : A1234567">-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Passport/Aadhar No.</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="passport_no" value="<?php echo isset($client_info->passport_no)?$client_info->passport_no:'';?>" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Commencement Date</label>
                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <span class="input-group date">
                                                            <input type="text" name="date_of_comm" id="doc" class="form-control mask datepicker" value="<?php echo isset($client_info->date_of_comm) && ($client_info->date_of_comm)!=0?$client_info->date_of_comm:date('d/m/Y');?>" data-inputmask="'alias': 'date'">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Category</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="client_category" value="<?php echo isset($client_info->client_category)?$client_info->client_category:'';?>" class="form-control" data-maxlength="50">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Status</label>
                                                <div class="col-sm-6">
                                                    <select class="select2" id="select-status" name="status">
                                                        <option value="0" <?php echo isset($client_info->status) && empty($client_info->status)?"selected='selected'":"";?>>Inactive</option>
                                                        <option value="1" <?php echo isset($client_info->status) && !empty($client_info->status)?"selected='selected'":"";?>>Active</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Client Photo</label>
                                                <div class="col-sm-6">
                                                    <?php if(isset($client_info->photo)) { ?>
                                                        <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                            <input type="hidden" value name />
                                                            <div class="fileinput-preview fileinout-exists thumbnail" data-trigger="fileinput" style="width: 150px; height: 150px;" id="photoPreview">
                                                                <img src="<?=base_url().$client_info->photo;?>"/>
                                                            </div>
                                                    <?php } else { ?>
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 150px; height: 150px;" id="photoPreview"></div>
                                                    <?php } ?>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span class="fileinput-new">Upload photo</span><span class="fileinput-exists">Change</span><input type="file" id="photo" name="photo" value="<?php if(isset($client_info->photo)) { echo basename($client_info->photo); } ?>"></span>
                                                            <a href="#" class="btn btn-default fileinput-exists btn-danger" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php /*<label class="col-sm-3 control-label">Signature</label>
                                                <div class="col-sm-6">
                                                    <img class="img-thumbnail" id="signImg" <?php if(isset($client_info->sign)) { ?> src="<?=base_url().$client_info->sign;?>" <?php } ?> style="width: 200px; height: 100px;">
                                                </div>*/ ?>
                                                <label class="col-sm-3 control-label">Client Signature</label>
                                                <div class="col-sm-6">
                                                    <?php if(isset($client_info->sign)) { ?>
                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                        <input type="hidden" value name />
                                                        <div class="fileinput-preview fileinout-exists thumbnail" data-trigger="fileinput" style="width: 150px; height: 100px;" id="signPreview">
                                                            <img src="<?=base_url().$client_info->sign;?>"/>
                                                        </div>
                                                        <?php } else { ?>
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 150px; height: 100px;" id="signPreview"></div>
                                                            <?php } ?>
                                                            <div>
                                                                <span class="btn btn-default btn-file"><span class="fileinput-new">Upload signature</span><span class="fileinput-exists">Change</span><input type="file" id="sign" name="sign" value="<?php if(isset($client_info->sign)) { echo basename($client_info->sign); } ?>"></span>
                                                                <a href="#" class="btn btn-default fileinput-exists btn-danger" data-dismiss="fileinput">Remove</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!--<div class="panel-footer">-->
                                        <!--<div class="row">
                                            <div class="btn-toolbar">
                                                <button type="submit" class="btn-primary btn" onclick="javascript:$('#validate-form').parsley( 'validate' );">Submit</button>
                                                <button type="button" class="btn-default btn" onclick="javascript:window.location.href='<?php echo base_url();?>broker/Clients';">Cancel</button>
                                            </div>
                                        </div>-->
                                        <!--</div>-->
                                    </form>
                                </div>
                                <div class="tab-pane" id="others">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-container tab-left tab-danger">
                                                <ul class="nav nav-tabs">
                                                    <li class="active"><a href="#bank" data-toggle="tab">Bank Details</a></li>
                                                    <li><a href="#demat" data-toggle="tab">Demat Accounts</a></li>
                                                    <li><a href="#policy" data-toggle="tab">Policy Details</a></li>
                                                    <li><a href="#trading" data-toggle="tab">Trading Details</a></li>
                                                    <li><a href="#documents" data-toggle="tab">Document Details</a></li>
                                                    <li><a href="#contact" data-toggle="tab">Additional Contact Details</a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="bank">
                                                        <div class="alert alert-info">
                                                            Add a new Bank Account detail or Delete existing record.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <button class="btn btn-success" onclick="add_bank_account()"><i class="fa fa-plus"></i> Add Bank Account</button>
                                                        <br /><br />
                                                        <div class="table-responsive table-flipscroll">
                                                            <table class="table table-striped table-bordered table-full-width" id="bank_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>Bank Name</th>
                                                                    <th>Branch</th>
                                                                    <th>IFSC</th>
                                                                    <th>Account Type</th>
                                                                    <th>Account No.</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="demat">
                                                        <div class="alert alert-info">
                                                            Add a new Demat Account detail or Delete existing record.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <button class="btn btn-success" onclick="add_demat_account()"><i class="fa fa-plus"></i> Add Demat Account</button>
                                                        <br /><br />
                                                        <div class="table-responsive table-flipscroll">
                                                            <table class="table table-striped table-bordered table-full-width" id="demat_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>DP Name</th>
                                                                    <th>Type of account</th>
                                                                    <th>DP ID</th>
                                                                    <th>Account No.</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="policy">
                                                        <div class="alert alert-info">
                                                            View Policy details or Delete existing Policy details.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <?php //<button class="btn btn-success" onclick="add_policy()"><i class="fa fa-plus"></i> Add Policy</button>?>
                                                        <br /><br />
                                                        <div class="table-responsive table-flipscroll">
                                                            <table class="table table-striped table-bordered table-full-width" id="policy_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>Company Name</th>
                                                                    <th>Plan Name</th>
                                                                    <th>Policy No.</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="trading">
                                                        <div class="alert alert-info">
                                                            Add a new Trading detail or Delete existing record.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <button class="btn btn-success" onclick="add_trading()"><i class="fa fa-plus"></i> Add Trading</button>
                                                        <br /><br />
                                                        <div class="table-responsive table-flipscroll">
                                                            <table class="table table-striped table-bordered table-full-width" id="trading_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>Broker</th>
                                                                    <th>Client Code</th>
                                                                    <th>Balance</th>
                                                                    <th>Ownership</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="documents">
                                                        <div class="alert alert-info">
                                                            Upload a new Document, View or Delete existing Documents.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label">Upload New Document</label>
                                                            <div class="col-sm-6 add-new-btn">
                                                                <select id="select-docType" style="width:80%" class="select2">
                                                                    <option value="0" selected disabled>Please select a document type</option>
                                                                </select>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_document_type()"><i class="fa fa-plus"></i></a>
                                                                <br/><br/>
                                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn btn-default btn-file">
                                                            <span class="fileinput-new">Select file</span>
                                                            <span class="fileinput-exists">Change</span>
                                                            <input type="file" name="..." id="docFile">
                                                        </span>
                                                                    <a href="#" class="btn btn-default fileinput-exists btn-success" id="docUpload" data-dismiss="fileinput">Upload</a>
                                                                    <span class="fileinput-filename"></span>
                                                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>

                                                                </div>

                                                            </div>
                                                        </div>
                                                        <!--button class="btn btn-success" onclick="add_trading()"><i class="fa fa-plus"></i> Add Trading</button-->
                                                        <br /><br /><br /><br /><br/><br/>
                                                        <div class="" style="padding-top:40px; border-top:4px double #CCC;">
                                                            <table class="table table-striped table-bordered table-full-width" id="document_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>Document Type</th>
                                                                    <th>Document Detail</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="contact">
                                                        <div class="alert alert-info">
                                                            Add a new Contact detail or Delete existing record.
                                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        </div>
                                                        <button class="btn btn-success" onclick="add_contact()"><i class="fa fa-plus"></i> Add Contact Details</button>
                                                        <br /><br />
                                                        <div class="table-responsive table-flipscroll">
                                                            <table class="table table-striped table-bordered table-full-width" id="contact_table">
                                                                <thead>
                                                                <tr>
                                                                    <th class="action-col-1">Action</th>
                                                                    <th>Category</th>
                                                                    <th>House No.</th>
                                                                    <th>Street</th>
                                                                    <th>Area</th>
                                                                    <th>City</th>
                                                                    <th>State</th>
                                                                    <th>Pincode</th>
                                                                    <th>Telephone</th>
                                                                    <th>Mobile</th>
                                                                    <th>Email ID</th>
                                                                </tr>
                                                                </thead>
                                                            </table><!--end table-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bottom-row navbar-fixed-bottom">
                              <?php
if( $this->session->userdata('permissions')=="3"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" onclick="addForm('<?php echo base_url("broker/clients/add");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" onclick="delete_client('<?php echo isset($client_info->client_id)?$client_info->client_id:'';?>')"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" onclick="submit_form_info()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>
<?php
}
else if( $this->session->userdata('permissions')=="2"){
?>
<div class="col-sm-12 bottom-col">
    <button type="button" id="add" onclick="addForm('<?php echo base_url("broker/clients/add");?>')" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
    <button type="button" id="edit" onclick="disableBtn()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" id="delete" onclick="delete_client('<?php echo isset($client_info->client_id)?$client_info->client_id:'';?>')"  data-style="expand-left" class="btn btn-danger bottom-btn ladda-button disable_btn"><i class="fa fa-trash-o"></i> Delete</button>
    <button type="button" id="save" onclick="submit_form_info()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
    <button type="button" id="cancel" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
</div>

<?php
}
else
{ }
?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Demat Account Bootstrap modal -->
            <div class="modal fade" id="demat_account_modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="demat_account_form" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Demat Provider</label>
                                        <div class="col-md-6 add-new-btn">
                                            <select name="provider_id" id="select-demat_provider" required="required"  style="width: 80%">
                                                <option></option>
                                                <?php foreach($demat_providers as $provider) { ?>
                                                    <option value="<?=$provider->provider_id;?>"><?=$provider->demat_provider;?></option>
                                                <?php } ?>
                                            </select>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-inverse-alt" onclick="add_demat_provider()"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Type of account</label>
                                        <div class="col-md-6">
                                            <div class="radio-block">
                                                <label>
                                                    <input type="radio" name="type_of_account" value="CDSL"/>
                                                    CDSL
                                                </label>
                                                <label>
                                                    <input type="radio" name="type_of_account" required value="NSDL"/>
                                                    NSDL
                                                </label>
                                            </div>
                                            <div class="radio-block">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Account Number</label>
                                        <div class="col-md-6">
                                            <input name="account_number" data-rangelength="[5,20]" data-type="digits" required class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">DP ID</label>
                                        <div class="col-md-6">
                                            <input name="demat_id" data-maxlength="80" required class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input name="client_id" value="<?=$client_info->client_id;?>" type="hidden">
                                        <input name="user_id" value="<?=$_SESSION['broker_id'];?>" type="hidden">
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="save_demat_account()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- End Bank Account Bootstrap modal -->

            <!-- Add Policy Bootstrap modal -->
            <div class="modal fade" id="policy_modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="policy_form" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Company Name</label>
                                        <div class="col-md-6">
                                            <select name="ins_comp_id" id="select-company_name" required="required"  style="width: 100%">
                                                <option></option>
                                                <?php foreach($ins_companies as $company) { ?>
                                                    <option value="<?=$company->ins_comp_id;?>"><?=$company->ins_comp_name;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Plan Name</label>
                                        <div class="col-md-6">
                                            <select name="plan_id" id="select-plan_name" required="required"  style="width: 100%">
                                                <option></option>
                                                <?php foreach($ins_plans as $plan) { ?>
                                                    <option value="<?=$plan->plan_id;?>"><?=$plan->plan_name;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Policy Number</label>
                                        <div class="col-md-6">
                                            <input name="policy_num" data-rangelength="[5,30]" required class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input name="client_id" value="<?=$client_info->client_id;?>" type="hidden">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="save_policy()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- End Policy Bootstrap modal -->



            <!-- Add Contact Bootstrap modal -->
            <div class="modal fade" id="contact_modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="contact_form" class="form-horizontal" data-validate="parsley">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Category</label>
                                        <div class="col-md-6">
                                            <select name="contact_category_id" id="select-categories" required="required"  style="width: 100%">
                                                <option></option>
                                                <?php foreach($contact_categories as $category) { ?>
                                                    <option value="<?=$category->contact_category_id;?>"><?=$category->contact_category_name;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">House/Flat No.</label>
                                        <div class="col-md-6">
                                            <input name="flat" data-maxlength="150" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Street</label>
                                        <div class="col-md-6">
                                            <input name="street" data-maxlength="150" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Area</label>
                                        <div class="col-md-6">
                                            <input name="area" data-maxlength="150" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">City</label>
                                        <div class="col-md-6">
                                            <input name="city" data-maxlength="150" required class="form-control" type="text">
                                        </div>
                                    </div><div class="form-group">
                                        <label class="control-label col-md-3">State</label>
                                        <div class="col-md-6">
                                            <select id="select-state-contact" name="state" style="width:100%" class="select2" required="required">
                                                <option value="0" selected disabled>Please select a state</option>
                                            </select>
                                        </div>
                                    </div><div class="form-group">
                                        <label class="control-label col-md-3">Pincode</label>
                                        <div class="col-md-6">
                                            <input name="pin" data-maxlength="10" class="form-control" type="text">
                                        </div>
                                    </div><div class="form-group">
                                        <label class="control-label col-md-3">Telephone</label>
                                        <div class="col-md-6">
                                            <input name="telephone" data-maxlength="40" class="form-control" type="text">
                                        </div>
                                    </div><div class="form-group">
                                        <label class="control-label col-md-3">Mobile</label>
                                        <div class="col-md-6">
                                            <input name="mobile" data-maxlength="15" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Email ID</label>
                                        <div class="col-md-6">
                                            <input name="email_id" data-type="email" data-maxlength="50" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input name="client_id" value="<?=$client_info->client_id;?>" type="hidden">
                                        <input name="user_id" value="<?=$_SESSION['broker_id'];?>" type="hidden">
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="save_contact()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- End Contact Bootstrap modal -->

            <?//php include base_url('application/views/master/occupation_add.php'); ?>

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
            $('#select-family').select2('val', $("#hidden_family").val());
        },
        error: function(json) {
            alert("Could not load family data! Please check console log.");
            console.log(json);
        }
    }); */

    /* load client types data */
    /* now we are loading data directly by passing it to the view
    ajax_load_client_types(); */
    function ajax_load_client_types(client_type_id)
    {
        $.ajax({
            url: "<?php echo base_url('broker/Clients/client_types_dropdown')?>",
            type: "post",
            dataType: "json",
            success: function(json) {
                $.each(json, function(){
                    $('#select-clientType').append('<option value="' + this.client_type_id + '">' + this.client_type_name + '</option>');
                });

                if(client_type_id) {
                    $('#select-clientType').select2('val', client_type_id);
                    $('#hidden_client_type').val(client_type_id);
                } else {
                    $('#select-clientType').select2('val', $("#hidden_client_type").val());
                }
            },
            error: function(json) {
                alert("Could not load client types data! Please check console log.");
                console.log(json);
            }
        });
    }

    /* load occupation data */
    /* now we are loading data directly by passing it to the view
    ajax_load_occupations(); */
    function ajax_load_occupations(occupation_id)
    {
        $.ajax({
            url: "<?php echo base_url('broker/Clients/occupations_dropdown')?>",
            type: "post",
            dataType: "json",
            success: function(json) {
                $.each(json, function(){
                    $('#select-occupation').append('<option value="' + this.occupation_id + '">' + this.occupation_name + '</option>');
                });

                if(occupation_id) {
                    $('#select-occupation').select2('val', occupation_id);
                    $('#hidden_client_type').val(occupation_id);
                } else {
                    $('#select-occupation').select2('val', $("#hidden_occupation").val());
                }
            },
            error: function(json) {
                alert("Could not load occupations data! Please check console log.");
                console.log(json);
            }
        });
    }

    /* get generated new client ID */
    /*$.ajax({
        url: "<?php //echo base_url('broker/Clients/new_client_id')?>",
        type: "post",
        dataType: "json",
        success: function(json) {
            $("#clientID").val(json.client_id);
            /* call function to show signature, if it exists */
            /*getSignature();
            getPhoto();
        },
        error: function(json) {
            alert("Could not load client types data! Please check console log.");
            console.log(json);
        }
    });*/


    /* load document types data */
    ajax_load_document_types();
    function ajax_load_document_types(doc_type_id) {
        $.ajax({
            url: "<?php echo base_url('broker/Clients/document_types_dropdown')?>",
            type: "post",
            dataType: "json",
            success: function(json) {
                $.each(json, function(){
                    $('#select-docType').append('<option value="' + this.document_type_id + '">' + this.document_type + '</option>');
                });

                if(doc_type_id) {
                    $('#select-docType').select2('val', doc_type_id);
                }
            },
            error: function(json) {
                bootbox.alert("Could not load client types data! Please check console log.");
                console.log(json);
            }
        });
    }

    // load the states file into select box */
    var states_file = "<?php echo base_url('assets/states.json'); ?>";
    $.getJSON(states_file, function(json) {
        //console.log(json);
        $.each(json, function(key, val){
            //console.log(val);
            $('#select-state').append('<option value="' + val + '">' + val + '</option>');
            $('#select-state-contact').append('<option value="' + val + '">' + val + '</option>');
        });
        $('#select-state').select2('val', $("#hidden_state").val());
    }).fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ', ' + error;
            console.log( "Request Failed: " + err);
    });

    /*********************************/
        /* AJAX functions END */
    /*********************************/


    $('.select2').select2();
    // remove additional form-control class from select2 to fix its view
    $('.select2-container').removeClass('form-control');

    //$('.dob-datepicker').datepicker({format:"dd/mm/yyyy", startView: 2});
    $('.dob-datepicker').datepicker({format:"dd/mm/yyyy", endDate:'<?=date('d-m-Y');?>'});
    $('.datepicker').datepicker({format:"dd/mm/yyyy", endDate:'<?=date('d-m-Y');?>'});

    /* disable/enable DOB/DOA applicable on checkbox */
    $('#dob-app').on("change", function() {
        //debugger;
        if($(this).prop("checked")) {
            $('#dob').prop("disabled", false);
            $("#dob").removeClass("disabled");
        } else {
            $('#dob').val("");
            $("#dob").addClass("disabled");
            $('#dob').prop("disabled", true);
        }
    }).change();
    $('#doa-app').on("change", function() {
        if($(this).prop("checked")) {
            $('#doa').prop("disabled", false);
            $("#doa").removeClass("disabled");
        } else {
            $('#doa').val("");
            $("#doa").addClass("disabled");
            $('#doa').prop("disabled", true);
        }
    }).change();


    /* apply masks to inputs */
    $('.mask').inputmask();

    $('.tooltips').tooltip(); //bootstrap's tooltip


    /* On documents upload */
    $("#docUpload").on("click", function() {
        //debugger;
        var selectDoc = $("#select-docType");
        if(selectDoc[0].value == "0") {
            selectDoc.closest('.form-group').addClass("has-error");
            bootbox.alert("Please select a document type before uploading file!");
        } else {
            var fileInput = $("#docFile");
            var formData = new FormData();
            // HTML file input user's choice...
            formData.append("docFile", fileInput[0].files[0]);
            formData.append("docTypeID", selectDoc[0].value);
            formData.append("docType", selectDoc.select2("data").text);
            formData.append("clientID", $("#clientID").val());
            //console.log(fileInput[0].files[0]);
            $.ajax({
                url: "<?php echo base_url('broker/Clients/upload_documents')?>",
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend: function() {
                    $("#docUpload").after("<br/><span id='wait' style='color:green'>Please wait while your file uploads...</span>");
                },
                success: function(jsonData){
                    //alert(data);
                    var data = $.parseJSON(jsonData);
                    //console.log(data);
                    $("#wait").remove();
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });
                    document_table.destroy();
                    document_table = ajax_documents();

                    if(selectDoc.select2("data").text == "Signature") {
                        getSignature();
                    }
                },
                error: function(data) {
                    bootbox.alert("Oops! There was some error!   ERROR DETAILS: "+data);
                    console.log(data);
                }
            });
        }
        /*var request = new XMLHttpRequest();
        request.open("POST", "http://foo.com/submitform.php");
        request.send(formData);*/

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
                    $("#signImg").attr("src","<?php echo base_url();?>"+data);
                }
            },
            error: function(data) {
                bootbox.alert("Error!   "+data);
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
                console.log(data);
                //alert(data);
                if(data) {
                    $("#photoPreview").html("<img src='<?php echo base_url();?>"+data+"'");
                }
            },
            error: function(data) {
                bootbox.alert("Error!   "+data);
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
            //$('#validate-form').parsley('removeItem', '#dob');
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

    function delete_client(id)
    {
        if(id == '' || id == null) { bootbox.alert("No clientID to delete!"); }
        else {
            bootbox.confirm('Are you sure you want to delete this client? Please note that client cannot be deleted if there is any data linked to the client.', function(result) {
                if(result)
                {
                    // ajax delete data to database
                    $.ajax({
                        url : "<?php echo site_url('broker/Clients/delete_Client');?>",
                        type: "POST",
                        data:{id: id},
                        dataType: "JSON",
                        success: function(data)
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                history: true
                            });
                            setTimeout(function(){
                                    window.location.href="<?php echo base_url('clients');?>"; },
                                2000);

                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            bootbox.alert('Error deleting data');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        }
                    });
                }
            });
        }
    }

    /*function delete_client(id)
    {
        //debugger;
        if(id == '' || id == null) { bootbox.alert("No clientID to delete!"); }
        else {
            bootbox.confirm('Are you sure you want to delete this data?', function(result) {
                if(result)
                {
                    // ajax delete data to database
                    $.ajax({
                        url : "<?php echo site_url('broker/Families/delete_Client');?>",
                        type: "POST",
                        data:{id: id},
                        dataType: "JSON",
                        success: function(data)
                        {
                            $.pnotify({
                                title: "Client deleted!",
                                text: "Selected client with ID "+id+" has been deleted successfully.",
                                type: 'info',
                                history: true
                            });
                            window.location.href="<?php echo base_url('clients');?>";
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            bootbox.alert('Error deleting data');
                        }
                    });
                }
            });
        }
    }*/

    // select the saved data in select-boxes
    $('#select-family').select2('val', $("#hidden_family").val());
    $('#select-clientType').select2('val', $("#hidden_client_type").val());
    $('#select-occupation').select2('val', $("#hidden_occupation").val());


    var clientID = $("#clientID").val(); // a global clientID variable, can be used everywhere in this page's script
    var save_method; //for save method string
    var bank_accountID;

    var bank_table, demat_table, policy_table, trading_table, document_table, contact_table;


    $(document).ready(function(){

        /* Load all datatables - Client Financial Details tabs */
        bank_table = ajax_bank_accounts();
        demat_table = ajax_demat_accounts();
        policy_table = ajax_policies();
        trading_table = ajax_tradings();
        document_table = ajax_documents();
        contact_table = ajax_contacts();

        disableBtn(); //enable editing on page load
        <?php if( $this->session->userdata('permissions')=="1" || $this->session->userdata('permissions')=="2") {?>
           enableBtn();
        <?php
          }
          else
          {
          ?>
            disableBtn();
          <?php
          }
          ?>


        var cnt=0;
        $("#cancel").attr("disabled", true);
        $("#save").attr("disabled", true);
        $(document).ajaxComplete(function(event, request, settings) {
            cnt++;
            if(cnt==6) {
                $("#cancel").attr("disabled", false);
                $("#save").attr("disabled", false);
            }
        });
    });


    /* bank_account functions START - Load data/Add/Delete */
    // get all bank_accounts data into datatables
    function ajax_bank_accounts()
    {
        var oTable = $("#bank_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_bank_accounts');?>',
                "type": 'post',
                "data": {"clientID":clientID}
            },
            "columns": [
                { "data": "action" },
                { "data": "bank_name" },
                { "data": "branch" },
                { "data": "IFSC" },
                { "data": "account_type_name" },
                { "data": "account_number" }
            ]
        });
        return oTable;
    }

    /*function edit_bank_account(id)
     {
     save_method = 'update';
     bank_accountID = id;
     $('#bank_account_form')[0].reset(); // reset form on modals

     //Ajax Load data from ajax
     $.ajax({
     url : "<?//php echo site_url('broker/clients/edit_bank_account');?>",
     type: "POST",
     data:{account_id: id},
     dataType: "JSON",
     success: function(data)
     {
     //initialize bank name in select2
     $('#select-banks').select2({
     width: 'resolve',
     placeholder:"Please select a Bank"
     }).on('change', function(e){
     //on change of plan name update text in insPlanRename
     var data = $('#select-banks').select2('data');
     $("#bank-id-hidden").val(data.value);
     });
     // assign values to elements on the modal edit form
     $('#select-banks').select2('val', data.bank_id);
     $('[name="account_type"]').val(data.account_type);
     $('[name="branch"]').val(data.branch);
     $('[name="IFSC"]').val(data.IFSC);
     $('[name="account_number"]').val(data.account_number);
     $('#bank_account_modal_form').modal('show'); // show bootstrap modal when complete loaded
     $('.modal-title').text('Edit Bank Account'); // Set title to Bootstrap modal title

     },
     error: function (jqXHR, textStatus, errorThrown)
     {
     alert('Error get data from ajax');
     }
     });
     }*/
    /* bank_account functions END */

    /* demat_account functions START - Load data/Add/Delete */
    // get all demat data into datatables
    function ajax_demat_accounts()
    {
        var oTable = $("#demat_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_demat_accounts');?>',
                "type": 'post',
                "data": {"clientID":clientID}
            },
            "columns": [
                { "data": "action" },
                { "data": "demat_provider" },
                { "data": "type_of_account" },
                { "data": "demat_id" },
                { "data": "account_number" }
            ]
        });
        return oTable;
    }
    function add_demat_account()
    {
        save_method = 'add';
        //reset form on modals
        $("#demat_account_form")[0].reset();
        //show bootstrap modal
        $("#demat_account_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Demat Account');

        //initialize demat provider name in select2
        $('#select-demat_provider').select2({
            width: 'resolve',
            placeholder:"Please select a Demat Provider"
        });
    }
    function save_demat_account()
    {
        //debugger;
        var valid = $('#demat_account_form').parsley( 'validate' );
        if(valid) {
            var url;
            if(save_method == 'add')
                url = "<?php echo base_url('broker/clients/add_demat_account');?>";
            else
                url = "<?php echo base_url('broker/clients/update_demat_account');?>?account_id="+bank_accountID;

            $.ajax({
                url: url,
                type:'post',
                data: $("#demat_account_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#demat_account_modal_form").modal('hide');
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'success',
                        history: true
                    });
                    demat_table.destroy();
                    demat_table = ajax_demat_accounts();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    bootbox.alert('Error adding / update data');
                }
            });
        }
    }
    function delete_demat_account(id)
    {
        bootbox.confirm('Are you sure you want to delete this data?', function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_demat_account');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#demat_account_modal_form').modal('hide');
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'info',
                            history: true
                        });
                        demat_table.destroy();
                        demat_table = ajax_demat_accounts();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
    /* demat_account functions END */

    /* policy functions START - Load data/Add/Delete */
    // get all policies data into datatables
    function ajax_policies()
    {
        var oTable = $("#policy_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_policies');?>',
                "type": 'post',
                "data": {"clientID":clientID}
            },
            "columns": [
                { "data": "action" },
                { "data": "ins_comp_name" },
                { "data": "plan_name" },
                { "data": "policy_num" }
            ]
        });
        return oTable;
    }
    function add_policy()
    {
        save_method = 'add';
        //reset form on modals
        $("#policy_form")[0].reset();
        //show bootstrap modal
        $("#policy_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Policy');

        //initialize company name & plan name in select2
        $('#select-company_name').select2({
            width: 'resolve',
            placeholder:"Please select a Company"
        });
        $('#select-plan_name').select2({
            width: 'resolve',
            placeholder:"Please select Policy Name"
        });
    }
    function save_policy()
    {
        //debugger;
        var valid = $('#policy_form').parsley( 'validate' );
        if(valid) {
            var url = "<?php echo base_url('broker/clients/add_policy');?>";
            $.ajax({
                url: url,
                type:'post',
                data: $("#policy_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#policy_modal_form").modal('hide');
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        history: true
                    });
                    policy_table.destroy();
                    policy_table = ajax_policies();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    bootbox.alert('Error adding / update data');
                }
            });
        }
    }
    function delete_policy(id)
    {
        bootbox.confirm('Are you sure you want to delete this data? Please note that this will also delete all Insurance data of this policy. Proceed only if you want to delete all related data of this policy.', function(result) {
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_policy');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#policy_modal_form').modal('hide');
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            history: true
                        });
                        if(data['type'] != 'error') {
                            policy_table.destroy();
                            policy_table = ajax_policies();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }

    $("#select-company_name").change(function(){
        $.ajax({
            url: "<?php echo site_url('broker/Insurances/get_policy_name');?>",
            type:'post',
            data: {comp_id: this.value},
            dataType: 'json',
            success:function(data)
            {
                $("#select-plan_name").select2('val', '');
                var option = '<option disabled selected value="">Please select Policy Name</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].plan_id+">"+data[i].plan_name+"</option>";
                });
                $("#select-plan_name").html(option);
            },
            error: function (data)
            {
                console.log(data);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from family',
                    type: 'error',
                    hide: true
                });
            }
        });
    });
    /* policy functions END */

    // get all tradings data into datatables
    function ajax_tradings()
    {
        var oTable = $("#trading_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_tradings');?>',
                "type": 'post',
                "data": {"clientID":clientID}/*,
                success:function(data)
                {
                    console.log(data);
                },
                error: function (data)
                {
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "action" },
                { "data": "broker" },
                { "data": "client_code" },
                { "data": "balance" },
                { "data": "held_type" }
            ]
        });
        return oTable;
    }

    // get all documents data into datatables
    function ajax_documents()
    {
        var oTable = $("#document_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_documents');?>',
                "type": 'post',
                "data": {"clientID":clientID}
            },
            "columns": [
                { "data": "action" },
                { "data": "type" },
                { "data": "filename" }
            ]
        });
        return oTable;
    }
    function delete_document(file)
    {
        bootbox.confirm('Are you sure you want to delete this document?', function(result) {
            if(result)
            {
                // ajax delete file from server
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_document');?>",
                    type: "POST",
                    data:{"file": file},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'info',
                            history: true
                        });
                        document_table.destroy();
                        document_table = ajax_documents();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
    /* document functions END */

    /* contact functions START - Load data/Add/Delete */
    // get all contacts data into datatables
    function ajax_contacts()
    {
        var oTable = $("#contact_table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'asc']],
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/ajax_contacts');?>',
                "type": 'post',
                "data": {"clientID":clientID}
            },
            "columns": [
                { "data": "action" },
                { "data": "contact_category_name" },
                { "data": "flat" },
                { "data": "street" },
                { "data": "area" },
                { "data": "city" },
                { "data": "state" },
                { "data": "pin" },
                { "data": "telephone" },
                { "data": "mobile" },
                { "data": "email_id" }
            ]
        });
        return oTable;
    }
    function add_contact()
    {
        save_method = 'add';
        //reset form on modals
        $("#contact_form")[0].reset();
        //show bootstrap modal
        $("#contact_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Contact Details');

        //initialize contact category names in select2
        $('#select-categories').select2({
            width: 'resolve',
            placeholder:"Please select a Category"
        }).on('change', function(e){
                //on change of plan name update text in insPlanRename
                var data = $('#select-categories').select2('data');
                $("#category-id-hidden").val(data.value);
            });
    }
    function save_contact()
    {
        //debugger;
        var valid = $('#contact_form').parsley( 'validate' );
        if(valid) {
            var url = "<?php echo base_url('broker/clients/add_contact');?>";
            $.ajax({
                url: url,
                type:'post',
                data: $("#contact_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    $("#contact_modal_form").modal('hide');
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'success',
                        history: true
                    });
                    contact_table.destroy();
                    contact_table = ajax_contacts();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    bootbox.alert('Error adding / update data');
                }
            });
        }
    }
    function delete_contact(id)
    {
        bootbox.confirm('Are you sure you want to delete this data?', function(result) {
            if(result)
            {
                // ajax delete data from database
                $.ajax({
                    url : "<?php echo site_url('broker/clients/delete_contact');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        //if success reload ajax table
                        $('#contact_modal_form').modal('hide');
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'info',
                            history: true
                        });
                        contact_table.destroy();
                        contact_table = ajax_contacts();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(errorThrown);
                        bootbox.alert('Error deleting data');
                    }
                });
            }
        });
    }
    /* contact functions END */

</script>
