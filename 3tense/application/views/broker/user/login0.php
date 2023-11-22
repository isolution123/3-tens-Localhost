<?php include "header-focused.php" ?>

<div class="verticalcenter" style="top: 35%;">
    <a href="<?php echo base_url('broker'); ?>"><img src="<?php echo base_url(); ?>assets/users/img/logo-big.png" alt="Logo" class="brand" /></a>
	<div class="panel panel-primary">
		<div class="panel-body">
			<h4 class="text-center" style="margin-bottom: 25px;">Log in to get started or <a href="extras-signupform.php">Sign Up</a></h4>
            <?php echo validation_errors();
            $attributes = array("class" => "form-horizontal", "style" => "margin-bottom: 0px !important;");
            echo form_open('broker/users/login', $attributes); ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" name="username" id="username" required placeholder="Username" value="<?php echo set_value('username');?>">
                            <span class="text-danger"><?php echo form_error('username'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="password" required placeholder="Password">
                            <span class="text-danger"><?php echo form_error('password'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="clearfix" style="margin-bottom: 40px;">
                    <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
                </div>
            <?php echo $this->session->flashdata('msg'); ?>
                <div class="panel-footer">
                    <a href="#" class="pull-left btn btn-link" style="padding-left:0">Forgot password?</a>
                    <div class="pull-right">
                        <?php //<a href="#" class="btn btn-default">Reset</a> ?>
                        <input type="submit" value="Login" name="btn_login" class="btn btn-primary">
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