<?php include "header-focused.php"; ?>
<script src="<?php echo base_url(); ?>assets/vendors/jquery/dist/jquery.min.js"></script>
<div class="verticalcenter" style="top: 35%;" align="center">
    <a href="<?php echo base_url('client/Clients_users'); ?>"><img src="<?php echo base_url(); ?>assets/users/img/logo-big.png" alt="Logo" class="logo" /></a>
	<div class="panel panel-primary">
		<div class="panel-body">

            <?php #echo validation_errors();
            #$attributes = array("class" => "form login", "style" => "margin-bottom: 0px !important;");
              #echo form_open('client/Clients_forget/pass', $attributes); ?>
              <form method="post" action="<?php echo base_url().'broker/forget/pass'; ?>" name="form1" class="form login" style="margin-bottom: 0px !important;" >
            <div class="form__field">
              <label for="login__EmailId"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#user"></use></svg><span class="hidden">Username</span></label>
              <input id="login__EmailId" type="text" name="email" id="email" required placeholder="Email Address" value="<?php echo set_value('email');?>">
            </div>

			            <div class="clearfix" style="margin-bottom: 25px;">
                <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
            </div>



            <div class="form__field">
              <label for="login__Username"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#lock"></use></svg><span class="hidden">Password</span></label>
              <input id="login__Username" type="text" name="name" id="name" required placeholder="User Name" class="form__input"  required>
            </div>
            <!--<center><div>
              <input type="radio" name="collection" value="yes" checked>Client
              <input type="radio" name="collection" value="no">Broker
            </div></center>-->
            
            <center style="padding:10px; background-color: rgba(43, 84, 164, 0) !important;">
              <label style="background-color: rgba(43, 84, 164, 0) !important;"><input type="radio" name="collection" value="no" checked>  Advisor</label>
              <label style="background-color: rgba(43, 84, 164, 0) !important;"><input type="radio" name="collection" value="yes" >  Client</label>
            </center>

            <div class="clearfix" style="margin-bottom: 40px;">
                <div id="infoMessage" class="text-center">
                    <?php #echo isset($message)?$message:'';
                    if(isset($message)){echo '<div style=" color:#a94442; padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">'.$message.'</div>' ;}
                    if($this->session->flashdata('msg')!=null)
                    {
                      echo '<div style=" color:#a94442; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">'.$this->session->flashdata('msg').'</div>';
                    }
                    ?>
                </div>
                <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
            </div>

            <div class="panel-footer">

                <div class="pull-right">
                    <?php //<a href="#" class="btn btn-default">Reset</a> ?>
                    <input type="submit" value="Send" name="btn_login" class="btn btn-primary">
                </div>

            </div>
        <?php
        #echo form_close();
        ?>
      </form>
      <script>
      $(document).ready(function($) {
          var form = $('form[name="form1"]'),
              radio = $('input[name="collection"]'),
              choice = '';

          radio.change(function(e) {
              choice = this.value;

              if (choice === 'yes') {
                  form.attr('action', '<?php echo base_url().'client/Clients_forget/pass';?>');
              } else {
                  form.attr('action', '<?php echo base_url().'broker/forget/pass';?>');
              }
          });
      });
      </script>
</div>
</div>
</div>



<svg xmlns="http://www.w3.org/2000/svg" class="icons"><symbol id="arrow-right" viewBox="0 0 1792 1792"><path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z"/></symbol><symbol id="lock" viewBox="0 0 1792 1792"><path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z"/></symbol><symbol id="user" viewBox="0 0 1792 1792"><path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z"/></symbol></svg>

</body>
</html>
