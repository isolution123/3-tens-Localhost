<div class="container body">
    <!-- page content -->
    <div class="right_col" role="main">
      <!-- top tiles -->
    <div class="verticalcenter" style="top: 35%;" align="center">

<div class="container body">
<div class="container bootstrap snippet">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <span class="glyphicon glyphicon-th"></span>
                        Change password
                    </h3>
                </div>
                <div class="panel-body " text-center>
                  <?php echo validation_errors();
                  $attributes = array("class" => "form login", "style" => "margin-bottom: 0px !important;");
                    echo form_open('client/Clients_users/change', $attributes); ?>
                        <div style="margin-top:20px;width:100%" class="col-xs-6 col-sm-6 col-md-6 login-box">
                         <div class="form-group">
                            <div class="input-group">
                              <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                              <input class="form-control" type="password" placeholder="Current Password" name="oldpass">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="input-group">
                              <div class="input-group-addon"><span class="glyphicon glyphicon-log-in" ></span></div>
                              <input class="form-control" type="password" placeholder="New Password" name="newpass">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="input-group">
                              <div class="input-group-addon"><span class="glyphicon glyphicon-log-in" ></span></div>
                              <input class="form-control" type="password" placeholder="Confirm Password" name="cnfmpass">
                            </div>
                          </div>
                        </div>
                        <div class="clearfix" style="margin-bottom: 40px;">
                            <div id="infoMessage" class="text-center">
                                <?php #echo isset($message)?$message:'';
                                if(isset($message)){echo $message;}
                                ?>
                            </div>
                            <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
                        </div>
                </div>
                <div class="panel-footer ">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6"></div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <button class="btn icon-btn-save btn-success" type="submit">
                            <span class="btn-save-label"><i class="glyphicon glyphicon-floppy-disk"></i></span> Save</button>
                        </div>
                    </div>
                </div>
                <?php
                echo form_close();
                ?>
            </div>

        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
