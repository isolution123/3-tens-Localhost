  <!DOCTYPE html>
  <html >
  <head>
    <meta charset="UTF-8">
     <meta charset="utf-8">
      <title><?php echo $title; ?></title>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="Avant">
      <meta name="author" content="The Red Team">
    <title>Login</title>
    <link href="<?php echo base_url();?>assets/users/less/styles.less" rel="stylesheet/less" media="all">
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600' rel='stylesheet' type='text/css'>

        <link href="<?php echo base_url();?>assets/users/css/Client.css" rel="stylesheet">

  </head>
<body class="align">


 <!-- jQuery -->
 <script src="<?php echo base_url(); ?>assets/vendors/jquery/dist/jquery.min.js"></script>


 <div class="verticalcenter" style="top: 35%;" align="center">
     <img src="<?php echo base_url(); ?>assets/users/img/logo-big.png" alt="Logo" class="logo" />
   <div class="panel panel-primary">
     <div class="panel-body">

 <form method="post" action="<?php echo base_url().'broker/users/user_authication'; ?>" name="form1" class="form login" style="margin-bottom: 0px !important;" >

       <div class="form__field">
         <label class="label" for="login__username"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#user"></use></svg><span class="hidden">Authentication Code</span></label>
         <input id="auth_code" type="text" name="auth_code" class="form__input" placeholder="Authentication Code" required>
       </div>

                 <div class="clearfix" style="margin-bottom: 30px;">
                     <div id="infoMessage" class="text-center">
                         <?php #echo isset($message)?$message:'';
                         if(isset($message)){echo '<div class="alert alert-success">
                     <strong>'.$message.'</strong></div>';}
                     if($this->session->flashdata('msg')!=null)
                     {
                       echo '<div style=" color:#a94442; padding: 15px; margin-top: 20px;  margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; background-color:#ebccd1">'.$this->session->flashdata('msg').'</div>';
                     }
                         ?>
                     </div>
                     <?php /*<div class="pull-right"><label><input type="checkbox" style="margin-bottom: 20px" checked=""> Remember Me</label></div>*/ ?>
                 </div>
             <?php #echo $this->session->flashdata('msg'); ?>
                 <div class="panel-footer">

                     <div class="pull-right">
                         <?php //<a href="#" class="btn btn-default">Reset</a> ?>
                         <input type="submit" value="Login" name="btn_authentication" class="btn btn-primary">

                     </div>
                     <br>
                 </div>
             <?php
           #  echo form_close();
             ?>
           </form>
           
     </div>
   </div>
  </div>
  <svg xmlns="http://www.w3.org/2000/svg" class="icons"><symbol id="arrow-right" viewBox="0 0 1792 1792"><path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z"/></symbol><symbol id="lock" viewBox="0 0 1792 1792"><path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z"/></symbol><symbol id="user" viewBox="0 0 1792 1792"><path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z"/></symbol></svg>

 </body>
 </html>
