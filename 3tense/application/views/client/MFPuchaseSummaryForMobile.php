
<!DOCTYPE html>
<html lang="en">
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <!-- Meta, title, CSS, favicons, etc. -->
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
         <title>Client Dashboard</title>

     <link href="<?php echo base_url(); ?>assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url(); ?>assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <!-- <link href="<?php //echo base_url(); ?>assets/vendors/nprogress/nprogress.css" rel="stylesheet"> -->
    <!-- iCheck -->
    <link href="<?php echo base_url(); ?>assets/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="<?php echo base_url(); ?>assets/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="<?php echo base_url(); ?>assets/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="<?php echo base_url(); ?>assets/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo base_url(); ?>assets/build/css/custom.css" rel="stylesheet">

    <!--Ladda-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda-themeless.min.css">
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/spin.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda.min.js"></script>

        <!-- jQuery -->
        <script src="<?php echo base_url(); ?>assets/vendors/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?php echo base_url(); ?>assets/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- FastClick -->
       <script src="<?php echo base_url(); ?>assets/vendors/fastclick/lib/fastclick.js"></script>
        <!-- NProgress -->
        <!-- <script src="<?php //echo base_url(); ?>assets/vendors/nprogress/nprogress.js"></script> -->
        <!-- Chart.js -->
        <script src="<?php echo base_url(); ?>assets/vendors/Chart.js/dist/Chart.min.js"></script>
        <!-- gauge.js -->
        <script src="<?php echo base_url(); ?>assets/vendors/gauge.js/dist/gauge.min.js"></script>
        <!-- bootstrap-progressbar -->
        <script src="<?php echo base_url(); ?>assets/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
        <!-- iCheck -->
        <script src="<?php echo base_url(); ?>assets/vendors/iCheck/icheck.min.js"></script>
        <!-- Skycons -->
        <script src="<?php echo base_url(); ?>assets/vendors/skycons/skycons.js"></script>

        <!-- Flot -->
        <script src="<?php echo base_url(); ?>assets/vendors/Flot/jquery.flot.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/Flot/jquery.flot.pie.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/Flot/jquery.flot.time.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/Flot/jquery.flot.stack.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/Flot/jquery.flot.resize.js"></script>
        <!-- Flot plugins -->
        <script src="<?php echo base_url(); ?>assets/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/flot.curvedlines/curvedLines.js"></script>
        <!-- DateJS -->
        <script src="<?php echo base_url(); ?>assets/vendors/DateJS/build/date.js"></script>
        <!-- JQVMap -->
        <script src="<?php echo base_url(); ?>assets/vendors/jqvmap/dist/jquery.vmap.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?php echo base_url(); ?>assets/vendors/moment/min/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

        <!-- Flot -->
        <link rel='stylesheet' type='text/css' href="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.default.css">
        <script type='text/javascript' src="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.min.js"></script>

        <!-- Bootstrap -->
        <script src="<?php echo base_url(); ?>assets/users/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <link href="<?php echo base_url(); ?>assets/users/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- /gauge.js -->


       


<style>
    table tr th
    {
        text-align:center;
    }
</style>
  </head>
 
<body style="background:none;"> 
<div class="container body">
    <div  class="row right_col" role="main" style="margin-top:15px;padding:10px 10px;background:none;">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center p-0 mt-3 mb-2">
            <div class="row col-md-12">
                     <img src="<?php echo site_url('uploads/thankyou.png"'); ?>" />
                </div>
               <table  class="display table table-striped table-bordered table-full-width dataTable" width="100%">
                    <tr>
                        <th>Order Number</th>
                        <th>Order Ref No</th>
                        <th>Order Amount</th>
                    </tr>
                    <tr>
                        <td>
                            <?php print_r($transcation_data[0]->OrderId); ?> 
                        </td>
                        <td>
                            <?php print_r($transcation_data[0]->UniqueReferenceNumber); ?>
                        </td>
                        <td>
                            <?php print_r($transcation_data[0]->Amount); ?>
                        </td>
                    </tr>
                </table>
            <br>
            <br>
                    
            <a   class="btn btn-primary" id="CloseBrower" >Close</a>
                 
                 
        </div>
    </div>
</div>  
<script>
    document.getElementById("CloseBrower").onclick = function() {
        window.postMessage("exit", "*");
        window.parent.postMessage("exit", "*");
        window.history.go(-1);
$(document.body).hide()
        window.close();
    }
</script>
</body>
</html>

