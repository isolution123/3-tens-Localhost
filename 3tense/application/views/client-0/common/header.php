<?php error_reporting(E_ALL);
ini_set('display_errors',1);?>
<!DOCTYPE html>
<html lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
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
		#clientList a:hover {
		text-decoration: underline !important;
	}
	</style>


  </head>

  <body class="nav-md">
    <div class="container body">

    <!-- Modal -->
      <div class="modal fade" id="myModal" role="dialog" style="z-index:1050">
        <div class="modal-dialog modal-sm">

          <!-- Modal content-->
          <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <center><h4 class="modal-title">Select Client </h4></center>
            </div>
            <div class="modal-body">
             <table id="clientList">
             </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>


      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">

              <?php
                    $brokerID = $this->session->userdata('user_id');
                      if(glob("uploads/brokers/".$brokerID."/*.png*")) {
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                    $logo = "uploads/brokers/".$brokerID.'/'.$logo;
                  }
                  elseif(glob("uploads/brokers/".$brokerID."/*.jpg*")){
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                    $logo = "uploads/brokers/".$brokerID.'/'.$logo;
                  }
                  elseif(glob("uploads/brokers/".$brokerID."/*.jpeg*")){
                    $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                    $logo = "uploads/brokers/".$brokerID.'/'.$logo;
                  }
                  else {
                      $logo = "assets/users/img/logo.png";
                  }
                    ?>

              <a href="" class="site_title">
                <!-- <img src="<?php echo base_url()?>assets/build/images/3-tense.jpg" alt="logo"/> -->
                  <img src="<?php echo base_url().$logo; ?>" alt="logo" class="logo_head" />
              </a>
            </div>

            <div class="clearfix"></div>

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">

                <ul class="nav side-menu">
                  <li><a href="<?php echo site_url('client/Dashboard')?> "><i class="fa fa-home"></i> Dashboard</a></li>

                  <li><a><i class="fa fa-umbrella"></i> Insurance <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo site_url('client/Reports/get_ins_report')?> " target="_blank">Insurance Details</a></li>
                      <li><a href="<?php echo site_url('client/Reports/get_prem_cal_report')?> " target="_blank">Premium Calendar</a></li>
                    </ul>
                  </li>
                  <li><a href="<?php echo site_url('client/Reports/get_mf_report')?> " target="_blank"><i class="fa fa-newspaper-o"></i> Mutual Funds</a></li>
                  <li><a href="<?php echo site_url('client/Reports/get_equity_report')?> " target="_blank"><i class="fa fa-bar-chart-o"></i> Equity</a></li>
                  <li><a><i class="fa fa-money"></i> Fixed Deposit <span class="fa fa-chevron-down" target="_blank"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo site_url('client/Reports/get_fd_report')?> " target="_blank">Fixed Deposit Details</a></li>
                      <li><a href="<?php echo site_url('client/Reports/get_int_cal_report')?> " target="_blank">Interest Calendar</a></li>
                    </ul>
                  </li>
                  <li><a href="<?php echo site_url('client/Reports/get_commodity_report')?> " target="_blank"> <i class="fa fa-diamond"></i> Commodity</a></li>
                  <li><a href="<?php echo site_url('client/Reports/get_re_report')?> " target="_blank"><i class="fa fa-bank"></i> Real Estate</a></li>

                  <li><a href="<?php echo site_url('client/Reports/get_al_report')?> " target="_blank"><i class="fa fa-exchange"></i> Asset & Liabilities</a></li>

                  <li><a href="<?php echo site_url('client/Reports/get_summary_report')?> " target="_blank"><i class="fa fa-calendar-check-o"></i> Summary</a></li>
                 <li><a href="<?php echo site_url('client/Reports/get_cash_flow_report')?> " target="_blank"><i class="fa fa-map-o"></i> Cash Flow</a></li>
                 <li><a href="<?php echo site_url('client/Reports/get_ledger_report')?> " target="_blank" ><i class="fa fa-folder-open"></i> Ledger</a></li>


                 <li>

                   <?php

                           if($this->session->userdata('type')=='head')
                           {
                                 $client_id = $this->session->userdata('client_id');
                                 $family_id= $this->session->userdata('family_id');
                                 $brokerID= $this->session->userdata('user_id');
                                 ?>
                                   <a href="javascript:void(0)" onclick="print_client_list()"><i class="fa fa-user"></i> Client Report</a>

                                 <?php

                            } else  {

                               $client_id = $this->session->userdata('client_id');
                               $brokerID = $this->session->userdata('user_id');
                               ?>
                               <a  href="javascript:void(0)"  onclick="print_client('<?php echo $client_id?>')"><i class="fa fa-user"></i> Client Report</a>
                               <?php
                           }
                       ?>
                 </li>
                 <li><a href="<?php echo site_url('client/DocsList')?> "  ><i class="fa fa-file"></i><p style="margin: -20px 30px 0 10px; text-align:center;"> Upload Documents & Portfolio Data</p></a></li>
                 <br/><br/><br/>
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

          </div>
        </div>

     <img src="<?php echo base_url().'assets/users/images/3tense-powered.png';?>" style="max-width:100%; left:10px; bottom:0; position:fixed; z-index:9999999;">

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <span ><?php echo $this->session->userdata('client_name')?>
                    <?php if($this->session->userdata('type')=='head')
                    {
                      echo "(Family Head)";
                    }?>
                    <?php //echo $this->session->userdata('client_id')?>
                    <?php //echo $this->session->userdata('family_id')?>
                    <?php // echo $this->session->userdata('user_id')?>
                    <?php //echo $this->session->userdata('head')?>
                    <?php //echo $this->session->userdata('family')?>
                    <?php //echo $this->session->userdata('type')?>
                    <span class=" fa fa-angle-down"></span></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <?php if($this->session->userdata('head')=='yes'&&$this->session->userdata('type')==''){ ?>
                      <li><a href="<?php echo base_url('client/Clients_users/Sethead'); ?>">You as Family Head</a></li>
                    <?php }
                     if($this->session->userdata('head')=='yes'&&$this->session->userdata('type')=='head'){ ?>
                      <li><a href="<?php echo base_url('client/Clients_users/Unsethead'); ?>">You as Clent</a></li>
                    <?php } ?>
                    <li><a href="<?php echo base_url('client/Clients_users/change_password'); ?>"> Change Password</a></li>    
                    <li><a href="<?php echo base_url('client/Clients_users/logout'); ?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <!-- <li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="text-center">
                        <a>  redirect('');
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li> -->
                <li role="presentation" id="top-notif" >

                  <?php //echo !empty($count_reminder)?'<span class="badge bg-green">'.$count_reminder.'</span>':''; ?>

                </li>




              </ul>
            </nav>
            <?php $this->load->view('client/add_reminder');?>
          </div>
        </div>
        <!-- /top navigation -->
      </div>

<script type="text/javascript">

$(document).ready(function() {
$.ajax({

        url:'<?php echo site_url('client/Dashboard/getTotalPortFolio')?>',
        type:"GET",
        dataType:'JSON',
        success:function(result)
        {
	  var k,trHTML='';
          for(k=0;k<result.length;k++)
          {
             trHTML += '<tr><td><a href="javascript:void(0)" onclick="print_client(\''+result[k].client_id+'\')"><h4>'+result[k].client_name+'<span style="font-size:14px; color:#379adb;"> (View Report)</span></h4></a></td></tr>';
          }
           $('#clientList').html(trHTML);
        }
      });
});

function print_client(id)
{
  //window.location.href="<?php echo base_url();?>broker/clients/report?id="+id,'_blank';
  window.open("<?php echo base_url();?>client/Clients/report?id="+id, '_blank');
}


function print_client_list()
{

   /*$.ajax({

        url:'<?php echo site_url('client/Dashboard/getTotalPortFolio')?>',
        type:"GET",
        dataType:'JSON',
        success:function(result)
        {
          var k,trHTML='';
          for(k=0;k<result.length;k++)
          {
             trHTML += '<tr><td><a href="javascript:void(0)" onclick="print_client(\''+result[k].client_id+'\')"><h4>'+result[k].client_name+'<span style="font-size:14px; color:#379adb;"> (View Report)</span></h4></a></td></tr>';
          }
           $('#clientList').html(trHTML);
            console.log(trHTML);*/

          $('#myModal').modal('show');
          $('#myModal').on('shown.bs.modal', function (e) {
          $('.nav-md').removeClass('modal-open');
          $('#myModal').addClass('modal-open');

           });


        /*}
      }); */
}
</script>
