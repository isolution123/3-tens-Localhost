
<?php include "header-focused.php" ?>

<div class="verticalcenter" style="top: 35%;">
    <a href="<?php echo base_url('broker'); ?>"><img src="<?php echo base_url(); ?>assets/users/img/logo-big.png" alt="Logo" class="brand" /></a>
    <div class="panel panel-primary">
        <div class="panel-body">
            <h4 class="text-center" style="margin-bottom: 25px;">Reset Your Password </h4>
            <div id="infoMessage" class="text-center">
                <?php #echo isset($message)?$message:'';
                if(isset($message)){echo '<div class="alert alert-danger">
                    <strong>'.$message.'</strong>
                </div>';}
                ?>

            </div>
            <?php echo validation_errors();
            $attributes = array("class" => "form-horizontal", "style" => "margin-bottom: 0px !important;");
            echo form_open('broker/forget/set'); ?>
			<input type="hidden" name="client_id" value="<?php echo isset($id)?$id:''?>" />
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="password" class="form-control" name="password" id="password" required placeholder="Password" value="<?php echo set_value('password');?>">
                        <span class="text-danger"></span>
                    </div>
                </div>
            </div>
			            <div class="clearfix" style="margin-bottom: 25px;">
                <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" name="confirm" id="confirm" required placeholder="Confirm Password">
                        <span class="text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="clearfix" style="margin-bottom: 40px;">
                <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
            </div>

            <div class="panel-footer">
                <div class="pull-right">
                    <?php //<a href="#" class="btn btn-default">Reset</a> ?>
                    <input type="submit" value="Reset Password" name="btn_login" class="btn btn-primary">
                </div>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
</body>
</html>