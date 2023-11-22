<?php include "header-focused.php" ?>

<div class="verticalcenter" style="top: 35%;">
    <a href="<?php echo base_url('broker'); ?>"><img src="<?php echo base_url(); ?>assets/users/img/logo-big.png" alt="Logo" class="brand" /></a>
    <div class="panel panel-primary">

        <div class="panel-body">
            <h4 class="text-center" style="margin-bottom: 25px;">Send Mail to Reset Password</h4>

            <?php echo validation_errors();
            $attributes = array("class" => "form-horizontal", "style" => "margin-bottom: 0px !important;");
            echo form_open('broker/forget/pass'); ?>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" name="email" id="email" required placeholder="Email Address" value="<?php echo set_value('email');?>">
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
                        <input type="text" class="form-control" name="name" id="name" required placeholder="User Name">
                        <span class="text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="clearfix" style="margin-bottom: 40px;">
                <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
            </div>

            <?php #echo isset($message)?$message:'';
            if(isset($message)){echo '<div class="alert alert-warning">
                    <strong>'.$message.'</strong> </div>';}
            ?>

            <div class="panel-footer">
                <div class="pull-right">
                    <?php //<a href="#" class="btn btn-default">Reset</a> ?>
                    <input type="submit" value="Send" name="btn_login" class="btn btn-primary">
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