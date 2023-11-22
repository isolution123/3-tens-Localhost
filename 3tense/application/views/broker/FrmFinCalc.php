<?php

//echo base_url();

//require_once 'welcome_message.php';

?>





<style>

    .top{

        margin-top: 5%;

    }

    #page-content{
            min-height: 1525px;
    }

    .val {

        font-family: rupee_foradianregular;

        margin-right: 4px;

    }    

    .h3_text{

        color:#fff;

        padding: 5px;

    }

    .result_h3{

        font-size: 18px !important;

        padding: 5px !important;

        color:#fff;

    }

    .hedgTxt02 {

        display: block;

        font-size: 16px;

        color: #585858;

        font-weight: normal;

        padding-bottom: 8px;

        clear: both;

    }

    .futureVal .value, .futureVal .value02 {

        font-size: 30px;

        font-weight: bold;

    }

    .futureVal .value span, .futureVal .value02 span {

        font-family: rupee_foradianregular;

        margin-right: 4px;

    }

    .lineHt {

        line-height: 16px;

        padding-right: 10px;

        width: 150px;

    }

    .fl {

        float: left;

    }

    .colr01 {

        color: #72cc51;

    }

    .colr02 {

        color: #00ad82;

    }    

    #TblFutureVal th

    {

       background-color: #4f8edc;

       color:white;

    }    

    .center{

        

        text-align: center;

    }    

    .subheader{

        

        background-color: white !important;

        color:black !important;

    }    

    /* Dileep */

    .head_center{

        text-align: center;

        margin-top: 22%;

        font-weight: bold;

    }    

    .hr_line{

        color: #D9D9D9;

        border: 1px solid;

    }    

    .text-box{

        margin-top: 15px;

    }    

    .label-name{

        margin-top: 25px;

    }
    .pdf_img{             
           background-color: transparent;    
           background-image: url("../extras/img/pdf_icon.png");
           background-repeat: no-repeat;
           border: medium none;
           height: 50px;
           width: 50px;
    }
    .modal-backdrop {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        background-color: black;
    }

    .modal-backdrop.in {
        opacity: 0.9;
        filter: alpha(opacity=50);
    }

</style>









<script>

   // $=jQuery.noConflict();

</script>

 <!-- new added today-->

<!--<script src="<?php echo base_url(); ?>extras/js/jquery.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>-->

<!--ends here -->

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

 <script src="<?php echo base_url(); ?>extras/js/SIP-O-Meter.js" type="text/javascript"></script>





<script src="<?php echo base_url(); ?>extras/js/pv_calc.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/sip_delay.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/spi_need.js" type="text/javascript"></script>



<script src="<?php echo base_url(); ?>extras/js/hlv_calc.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/marriage.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/education.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/car_calc.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/vacation.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/js/home_calc.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-flot/jquery.flot.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-flot/jquery.flot.resize.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-flot/jquery.flot.orderBars.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-morrisjs/morris.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-flot/jquery.flot.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/charts-morrisjs/raphael.min.js" type="text/javascript"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcCardsLayout.css" rel="stylesheet" type="text/css"/>





<!--<script src="<?php echo base_url(); ?>extras/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>-->

<!--<script src="<?php echo base_url(); ?>extras/js/bootstrap-datepicker.min.js" type="text/javascript"></script>-->

<!--<link href="<?php echo base_url(); ?>extras/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>-->

<!--<link href="<?php echo base_url(); ?>extras/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>-->





<!--<link href="<?php echo base_url(); ?>extras/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>-->

<script src="<?php echo base_url(); ?>extras/js/FilipEffect.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>extras/ion.rangeSlider-2.1.7/js/ion.rangeSlider.min.js"></script>

<!--<link href="<?php echo base_url(); ?>extras/bootstrap/css/font-awesome.css" rel="stylesheet" type="text/css"/>-->

<link href="<?php echo base_url(); ?>extras/ion.rangeSlider-2.1.7/css/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/ion.rangeSlider-2.1.7/css/ion.rangeSlider.skinHTML5.css" rel="stylesheet" type="text/css"/>

<script src="<?php echo base_url(); ?>extras/js/SIP-Delay.js" type="text/javascript"></script>



<!--<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDevice.css" rel="stylesheet" media="only screen and (max-device-width: 360px)" type="text/css"/>-->

<link href="<?php echo base_url(); ?>extras/Css/HLVSmallDevice.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/MarriageSmallDevice.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/EducationSmallDevice.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceSIP-O-Meter.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceSIP-Delay.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceCar.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceHome.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceVacation.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDevicePV.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/delayPreSmallDevice.css" rel="stylesheet" media="" type="text/css"/>

<link href="<?php echo base_url(); ?>extras/Css/FinCalcStyleSmallDeviceSIPNeed.css" rel="stylesheet" media="" type="text/css"/>

<!-- <script src="<?php echo base_url(); ?>extras/js/JS4SmallDevice.js" type="text/javascript"></script>-->

    <script type="text/javascript">

                   

            $( function() {

                $( ".datepicker" ).datepicker({

                     format: 'dd-mm-yyyy',

                });

            } );

        </script>



<div id="page-content" style="min-height:630px !important;">

    <div id='wrap'>

        <div id="page-heading">

            <ol class="breadcrumb">

                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>

                <li class="active">Financial Calculators</li>

            </ol>

            <h1>Financial Calculators</h1>

            <div class="options">

                <div class="btn-toolbar">

                    <div class="btn-group">

<!--                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-book"></i><span class="hidden-xs">  Reports of  </span><span class="caret"></span></a>-->

                        <ul class="dropdown-menu">

                            <li><a href="<?php echo base_url('broker/Assets_liabilities/al_report');?>">Assets Details</a></li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

        

<div class="container">

   

<div class="row">

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/Humman-life.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Human Life Value</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">The Human Life Value (HLV) Calculator helps you identify your life insurance needs on basis of income expenses, liabilities and investments and secure your family’s future.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Human Life Value</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">
                                As parents you wish the best for your children. This among other facilities and amenities includes providing world-class education so that they can achieve their career goals.
                                A sum invested regularly in the plan can help accumulate a corpus that will secure your child’s financial future. The amount of contribution will depend on how much you plan to save.
</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="HLVCalc">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/marriage-planning.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Marriage Planning</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Marriage Planning</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="marriage_calc">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/investment-delay.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Investment Delay</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much you need to invest monthly to accumulate a target amount in future.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Investment Delay</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                <button type="button" class="btn btn-primary" id="BtnSIPDelay">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/education-planning.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Education Planning</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Education Planning</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="education_calc">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

  </div>    

 

 <div class="row">

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/SIP-O-Meter.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">SIP-O-Meter</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">SIP-O-Meter</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="SipCalNw">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

<!--                            <img src="<?php echo base_url(); ?>extras/img/Sipp.jpg"/>-->

                            <img src="<?php echo base_url(); ?>extras/img/SIP-delay.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">SIP-Delay</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">SIP-Delay</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary"  id='btn_calcSIPdelay'>Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/present-value.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Present Value</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Present Value</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="calpv" >Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/SIP-Need.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">SIP- Need </h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">SIP- Need </h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="calNeed">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

  </div> 







<div class="row">

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/vacation-planning.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Vacation Planning</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Vacation Planning</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="vacation">Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/car-planning.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Car Planning</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Car Planning</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary"  id='carCalc'>Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

    <div class="col-md-3 col-sm-6">

             <div class="card-container">

                <div class="card">

                    <div class="front" style=" height:370px">

                        <div class="cover" style="height:196px !important">

                            <img src="<?php echo base_url(); ?>extras/img/house-planning.png"/>

                        </div>

<!--                        <div class="user">

                            <img class="img-circle" src="<?php echo base_url(); ?>extras/img/rotating_card_profile.png"/>

                        </div>-->

                        <div class="content">

                            <div class="main" style="min-height: 115px !important;">

                                <h3 class="name">Home Planning</h3>

                                

                                <p class="text-center" style="margin: 0 0 0 !important;">This tool can help you how much wealth you can accumulate in future with a monthly SIP.</p>

                            </div>

                            <div class="footer" style=" margin: 5px 0 0 !important;">

                                <i class="fa fa-mail-forward"></i> Auto Rotation

                            </div>

                        </div>

                    </div> <!-- end front panel -->

                    <div class="back" style=" height:370px">

                        <div class="header" style="padding: 15px 20px; height: 80px;">

                            <h3 class="name" style="border-bottom: 1px solid #EEEEEE;">Home Planning</h3>

                        </div>

                        <div class="content" style="padding:10px 20px 10px !important;">

                            <div class="main" style="min-height: 115px !important;">

                               <!-- <h4 class="text-center">Job Description</h4>-->

                                <p class="text-center">SIP or Systematic Investment Plan is a very popular and effective way of investing 

                                    regularly into a mutual fund scheme. 

                                    A monthly SIP in a mutual fund equity scheme can be very good tool for

                                    planning for your future long term goals in life and also as an effective tool to save regularly</p>                              

                            </div>

                        </div>

                        <div class="footer" style="margin: 5px 0 0;">

                            <div class="social-links text-center">

                                 <button type="button" class="btn btn-primary" id="homeCalc" >Calculate Now</button>

                            </div>

                        </div>

                    </div> <!-- end back panel -->

                </div> <!-- end card -->

            </div> <!-- end card-container -->

        </div>

   

  </div> 





  

    

    

 <div class="col-md-8">

     <!-- Modal -->

    <div class="modal fade" id="SIPoMeter" role="dialog" >

      <div class="modal-dialog modal-lg" >    

       <!-- Modal content-->

       <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">SIP-O-Meter</h4>

        </div>

        <div class="modal-body" >

          <div class="panel panel-primary">

                <div class="panel-heading"> 

                    <h3 class="panel-title result_h3">SIP Calculator</h3> 

                </div>
                <form action="<?php echo base_url(); ?>broker/calculators/pdf_calculatorSIPO" name="form_DelaySIPO" id="form_DelaySIPO" method="post">
                     <div class="panel-body">

                    <div class="col-sm-12 top">

                        <div class="form-group">

                            <div class="col-sm-3 mblSize mblsize768 mblsize1024" style="padding-top:15px;">

                                <label for="deposit_amt">Monthly Installment:</label>

                            </div>

                            <div class="col-sm-6 hide4Mobile">

                                <input id="ageslider" name="ageslider" type="text"/>    

                            </div>

                            <div class="col-sm-3 mblsm2 mblsize768 mblsize1024" style="padding-top:15px;">

                               <div class="form-elem">

                                    <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                

                                <input id="MonthSIP" name="MonthSIP" class="input txtboxAlgn-1024 sip-o-meter-768 sip-o-meter-736 sip-o-meter-1024 sip-o-meter-640 sip-o-meter-684 sip-o-meter-732 sip-o-meter-568 sip-o-meter-375 sip-o-meter-667 sip-o-meter-414" style="width:70% !important;margin-left:48px;margin-top:-61px" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                 &nbsp;

                               </div>

                                 

                                <a href="javascript:void(0)" class="tooltipFilter a-sip-o-meter-cor-1024 a-sip-o-meter-month-768 a-sip-o-meter-month-736 a-sip-o-meter-month-10241 a-sip-o-meter-month-360 a-sip-o-meter-month-640 a-sip-o-meter-month-412 a-sip-o-meter-month-684 a-sip-o-meter-month-732 a-sip-o-meter-month-320 a-sip-o-meter-month-568 a-sip-o-meter-month-375 a-sip-o-meter-month-667 a-sip-o-meter-month-414 a-sip-o-meter-month-736" tabindex="-1" style="margin-left: 195px;margin-top: -34px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify the desired amount.</span>

                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-12 top">

                        <div class="form-group">

                            <div class="col-sm-3 mblSize mblsize768 mblsize1024" style="padding-top:15px;">

                                <label for="interest_rate">Expected interest rate(%):</label>

                            </div>

                            <div class="col-sm-6 hide4Mobile">

                                <input type="text" class="form-control" name="income" id="income">

                            </div>

                            <div class="col-sm-3 mblsm2 mblsize768 mblsize1024" style="padding-top:15px;">

                               

                              <div class="form-elem" style="padding-left:8px; ">

                                 

                                  <input id="rateOfReturn" name="rateOfReturn" style="width:70% !important;margin-top:1px" class="input" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" />   

                                    &nbsp;

                                 

                                     <span class="right-badge right-badge-sip-o-1024 right-badge-sip-o-meter-768 right-badge-sip-o-meter-736 right-badge-sip-o-meter-1024 right-badge-sip-o-meter-736 right-badge-sip-o-metere-360 right-badge-o-meter-month-640 right-badge-sip-o-meter-412 right-badge-sip-o-meter-684 right-badge-sip-o-meter-732 right-badge-sip-o-meter-320 right-badge-sip-o-meter-568 right-badge-sip-o-meter-375 right-badge-sip-o-meter-667 right-badge-sip-o-meter-414 tb-sip-320"  style="padding-top:10px;padding-right:12px;" ><i class="fa fa-percent" style="padding-top:4px;" aria-hidden="true"></i></span>

                             

                               <a href="javascript:void(0)" class="tooltipFilter a-sip-o-meter-cor-1024 a-sip-o-meter-expt-sip-o-meter-768 a-sip-o-meter-month-10242 a-sip-o-meter-expt-sip-o-meter-736 a-sip-o-meter-expt-sip-o-meter-736 a-sip-o-meter-expt-360 a-sip-o-meter-expt-640 a-sip-o-meter-expt-sip-412 a-sip-o-meter-expt-sip-o-meter-684 a-sip-o-meter-expt-sip-o-meter-732 a-sip-o-meter-expt-sip-o-meter-320 a-sip-o-meter-expt-sip-o-meter-568 a-sip-o-meter-expt-sip-o-meter-375 a-sip-o-meter-expt-sip-o-meter-667 a-sip-o-meter-expt-sip-o-meter-414" tabindex="-1" style="margin-left: 187px;margin-top: -50px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify the expected rate of returns.</span>

                                </a>

                              

                              </div>

                               

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-12 top">

                        <div class="form-group">

                            <div class="col-sm-3 mblSize mblsize768 mblsize1024" style="padding-top:15px;">

                                <label for="year">Number of Installment(Month):</label>

                            </div>

                            <div class="col-sm-6 hide4Mobile">

                                <input type="text" name="balance" id="balance" class="form-control">

                            </div>

                            <div class="col-sm-3 mblsm2 mblsize768 mblsize1024" style="padding-top:15px;">

                              

                                 <div class="form-elem">

                                

                                        <input type="text" id="installment" name="installment" class="input" style="width:70% !important;margin-top:1px" value="1" size="10%" onkeypress="return isNumberKey(event)"  />    

                                         &nbsp;

                                         <span class="right-badge right-badge-sip-o-1024 mob-padding-sip-o-meter-768 right-badge-sip-o-meter-768 right-badge-sip-o-meter-1024 mob-padding-sip-o-meter-736 right-badge-sip-o-meter-736 right-badge-sip-o-meter-736 right-badge-sip-o-metere-360 right-badge-o-meter-month-640 mob-padding-sip-o-meter-360 right-badge-sip-o-meter-412 mob-padding-o-meter-month-640 mob-padding-sip-o-meter-412 mob-padding-sip-o-meter-684 right-badge-sip-o-meter-684 mob-padding-sip-o-meter-732 right-badge-sip-o-meter-732 right-badge-sip-o-meter-320 mob-padding-sip-o-meter-320 mob-padding-sip-o-meter-568 right-badge-sip-o-meter-568 mob-padding-sip-o-meter-375 right-badge-sip-o-meter-375 mob-padding-sip-o-meter-667 right-badge-sip-o-meter-667 mob-padding-sip-o-meter-414 right-badge-sip-o-meter-414" style="padding-top:10px;">Mts.</span>

                                        <a href="javascript:void(0)" class="tooltipFilter a-sip-o-meter-cor-1024 a-sip-o-meter-expt-sip-o-meter-768 a-sip-o-meter-month-10243 a-sip-o-meter-expt-sip-o-meter-736 aMnt-sip-o-meter-360 aMnt-o-meter-month-640 aMnt-sip-o-meter-412 a-sip-o-meter-expt-sip-o-meter-684 a-sip-o-meter-expt-sip-o-meter-732 a-sip-o-meter-expt-sip-o-meter-320 a-sip-o-meter-expt-sip-o-meter-568 a-sip-o-meter-expt-sip-o-meter-375 a-sip-o-meter-expt-sip-o-meter-667 a-sip-o-meter-expt-sip-o-meter-414" tabindex="-1" style="margin-left: 195px;margin-top: -50px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                <span>Specify the total months for investment.</span>

                                        </a>

                                 

                                 </div>

                                 

                            </div>

                        </div>

                    </div>     



                    <div class="col-sm-9">

<!--                        <button type="button" class="btn btn-danger calculate_btn" id="btn_calcSIP" style="margin-left: 50%;margin-top: 3%;">Calculate</button>

                        <button type="button" class="btn btn-danger reset_btn" onClick="ResetSlider()" style="margin-top: 8%;margin-bottom: 10%;">Reset</button>-->

                        

                        <button type="button" class="btn btn-danger  calculate_btn-sip-o-1024 calculate_btn-sip-o-meter-768 calculate_btn-sip-o-meter-736 calculate_btn calculate_btn-sip-o-meter-360 calculate_btn-sip-o-meter-640  calculate_btn-sip-o-meter-412 calculate_btn-sip-o-meter-684 calculate_btn-sip-o-meter-732 calculate_btn-sip-o-meter-320 calculate_btn-sip-o-meter-568 calculate_btn-sip-o-meter-375 calculate_btn-sip-o-meter-667 calculate_btn-sip-o-meter-414" id="btn_calcSIP" style="margin-left: 50%;margin-top: 5%;width: 20%;">Calculate</button>

                        <button type="button" class="btn btn-danger reset_btn-sip-o-meter-568 reset_btn-sip-o-meter-768 reset_btn reset_btn-sip-o-meter-736  reset_btn-sip-o-meter-360 reset_btn-sip-o-meter-640 reset_btn-sip-o-meter-412 reset_btn-sip-o-meter-684 reset_btn-sip-o-meter-732 reset_btn-sip-o-meter-320 reset_btn-sip-o-meter-375 reset_btn-sip-o-meter-667 reset_btn-sip-o-meter-414" onclick="ResetSlider()" style="margin-top: 60px;margin-bottom: 5%;">Reset</button>

                            

                    </div>



                    <div class="col-sm-7">

                        <span id="total" style="font-weight: bold;color:red;font-size: 30px;margin-left:20%"></span>

                    </div>



                    <div class="col-md-12 resultCntrAlgn-1024 rsltAlgnCntr768 sip_568 sip_667 sip_684 sip_732 sip_736" id ="galaxys640" style="padding-left: 0px;" >

                        <div class="panel panel-primary" id="result" style="display:none !important;">

                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">SIP Returns</h3> 

                            </div>

                            <div class="panel-body">



                                <div class="col-md-12" >

                                    <div class="col-md-6" id="chrtPi">

                                        <div class="panel panel-body">

                                            <div class="text-center"><h4></h4></div>

                                            <canvas id="mf-chart"></canvas>

                                        </div>

                                    </div>

                                    <div class="col-md-6" style="padding-top:10px;">

                                        <div class="futurevalue mblsz">

                                            <div class="hedgTxt02">

                                                The future value of your SIP  @ <label id="lblROI"></label>   will be

                                            </div>

                                            <div class="value sip_1024" id="fturvalue" style="padding-left:120px;">

                                                <span></span> 

                                                <button class="btn btn-info btn-lg FV" id='tbl_full' type="button" style="width:200px ;"></button>
                                                <input type="hidden" name="hidden_FV" id="hidden_FV" value="">
                                            </div>

                                        </div>

                                        <hr>

                                        <div class="futurevalue mblsz">

                                            <div class="hedgTxt02 lineHt fl" style="padding-top:8px;">

                                                You have invested

                                            </div>

                                            <div class="value sip_1024 colr01">

                                                <span></span>

                                                <button class="btn btn-success btn-lg investment" id='emi' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" name="hidden_investment" id="hidden_investment" value="">
                                            </div>

                                        </div>

                                        <hr>

                                        <div class="futurevalue mblsz">

                                            <div class="hedgTxt02 lineHt fl" style="padding-top:8px;">

                                                Your Profit

                                            </div>

                                            <div class="value sip_1024 colr01">

                                                <span></span>

                                                <button class="btn btn-warning btn-lg earning" id='tbl_int' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" name="hidden_earning" id="hidden_earning" value="">
                                            </div>

                                        </div>

                                    </div>

                                </div>



                                <!--      grid start here -->

                                <div class="col-md-12" style="margin-top:5px;">

                                    <table class="table table-bordered" id="TblFutureVal">

                                        <thead>

                                            <tr>

                                                <th colspan="9" class="center" style="border-left-width:2px;border-right-width:2px;">Expected Maturity Value of Your Investments</th>

                                            </tr>

                                            <tr> 

                                                <th rowspan="2" class="center subheader" style="width:150px;">Expected<br> Returns</th>

                                                <th colspan="7" class="center subheader">Investment Period (in Months)</th>

                                            </tr>

                                            <tr>

                                                <th class="center subheader" style="width:15px;">60(5Yrs)</th>

                                                <th class="center subheader" style="width:15px;">120(10Yrs)</th>

                                                <th class="center subheader" style="width:15px;">180(15Yrs)</th>

                                                <th class="center subheader" style="width:15px;">240(20Yrs)</th>

                                                <th class="center subheader" style="width:15px;">300(25Yrs)</th>

                                                <th class="center subheader" style="width:15px;">360(30Yrs)</th>

<!--                                                <th class="center" style="background-color: white !important;"> -->

                                                <th class="center" style="background-color: white !important;padding-top: 11px;width: 108px;"> 

                                                  <input type="text" class="cal_textboxsmall" style="color: black;width: 90px;text-align: center;" name="cPeriod" id="CPeriod" onblur="calculateSipReturnCustom();" maxlength="10" onkeypress="return isNumberKey(event)" >

                                                  &nbsp;

                                                   <a href="javascript:void(0)" class="tooltipFilter ResultGrdtooltipFilter" tabindex="-1" style="margin-left:50px;top:95"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                       <span style="font-size: 15px;">Value Should be in months.</span></a>

                                                </th>



                                            </tr>

                                        </thead>

                                        

                                      </table>
                                    <input type="hidden" name="hidden_sip_table" id="hidden_sip_table" value="">
                                </div>
                                <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                            </div>

                        </div>

                    </div>

                </div>
                </form>
            </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        </div>

      </div>     

     </div>

  </div>

    </div>

    

    

    

<!-- sip delay calculator-->
    <div class="col-md-8">
     <!-- Modal -->
    <div class="modal fade" id="SIPDelay" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-lg" style="/*width:900px*/">    
       <!-- Modal content-->
       <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
        </div>
        <div class="modal-body">
          <div class="panel panel-primary">
                <div class="panel-heading"> 
                    <h3 class="panel-title">Investment Delay</h3> 
                </div>
               <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorDelayRev" name="form_DelayRev" id="form_DelayRev" method="post">
                <div class="panel-body">
                    <div class="col-sm-12 top">
                        <div class="form-group">
                            <div class="col-sm-3 mblSize" style="padding-top:15px;">
                                <label for="deposit_amt">Target Corpus:</label>
                            </div>
                            <div class="col-sm-6 hide4Mobile" >
                                <input id="TargetCorpslider" name="TargetCorpslider" type="text"/>    
                            </div>
                            <div class="col-sm-3 mblsm2" style="padding-top:15px;">
                                
                                <div class="form-elem">
                                    <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  
                                    <input id="TxtTargetCorp" name="TxtTargetCorp" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input sip-Delay-1024 sip-Delay-736 sip-Delay-414   sip-Delay-667 sip-Delay-375 sip-Delay-568 sip-Delay-320 sip-Delay-732 sip-Delay-684 sip-Delay-640 sip-Delay-412" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 
                                </div>
                                <a href="javascript:void(0)" class="tooltipFilter a-sip-Delay-cor-1024 a-sip-Delay-cor-736 a-sip-Delay-cor-414 a-sip-Delay-cor-667 a-sip-Delay-cor-375 a-sip-Delay-cor-568 a-sip-Delay-cor-320 a-sip-Delay-cor-684 a-sip-Delay-cor-360 a-sip-Delay-cor-640 a-sip-Delay-cor-412 a-sip-Delay-cor-732" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">
                                    <span>Specify the amount you would need in future.</span>
                                </a>
                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 top">
                        <div class="form-group">
                            <div class="col-sm-3 mblSize" style="padding-top:15px;">
                                <label for="interest_rate">Time horizon (Years):</label>
                            </div>
                            <div class="col-sm-6 hide4Mobile">
                                <input type="text" class="form-control" name="TimeHorizon" id="TimeHorizon">
                            </div>
                            <div class="col-sm-3 mblsm2" style="padding-top:15px;">
                                
                             <div class="form-elem">
                                <input id="txtHorizon" name="txtHorizon" class="input" value="1" type="text" style="width:70% !important;margin-top:1px" size="10%" onkeypress="return isNumberKey(event)" />    
                                 &nbsp;
                                 <span class="right-badge right-badge-sip-Delay-1024 right-badge-sip-Delay-736 right-badge-sip-Delay-414 right-badge-sip-Delay-667 right-badge-sip-Delay-375 right-badge-sip-Delay-568 right-badge-sip-Delay-320 right-badge-sip-Delay-684  right-badge-sip-Delay-360 right-badge-sip-Delay-640 right-badge-sip-Delay-412 right-badge-sip-Delay-732" style="padding-top:10px;padding-right: 11px;">Yrs.</span>
                                 
                                    <a href="javascript:void(0)" class="tooltipFilter a-sip-Delay-cor-1024 a-sip-Delay-736 a-sip-Delay-414 a-sip-Delay-667 a-sip-Delay-375 a-sip-Delay-568 a-sip-Delay-320 a-sip-Delay-684   a-sip-Delay-360 a-sip-Delay-640 a-sip-Delay-412 a-sip-Delay-732" tabindex="-1" style="margin-left:187px;margin-top:-50px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">
                                        <span>Specify the no. of year you want to invest in order to get target corpus.</span>
                                   </a>
                             </div>
                            </div>
                        </div>
                    </div>
                       <div class="col-sm-12 top">
                        <div class="form-group">
                            <div class="col-sm-3 mblSize" style="padding-top:15px;">
                                <label for="interest_rate">Expected Rate of Retuns (Years):</label>
                            </div>
                            <div class="col-sm-6 hide4Mobile">
                                <input type="text" class="form-control" name="TimeHorizon" id="ROISlider">
                            </div>
                            <div class="col-sm-3 mblsm2" style="padding-top:15px;">
                              <div class="form-elem" style="padding-left:5px;">
                                   <input id="TxtRoi" name="TxtRoi" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    
                                   &nbsp;
                                   <span class="right-badge right-badge-sip-Delay-1024 tbiphn right-badge-sip-Delay-736 right-badge-sip-Delay-414 right-badge-sip-Delay-667 right-badge-sip-Delay-375 right-badge-sip-Delay-568 right-badge-sip-Delay-320 right-badge-sip-Delay-360 right-badge-sip-Delay-684 right-badge-sip-Delay-640 right-badge-sip-Delay-412 right-badge-sip-Delay-732" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>
                              </div>
                                   <a href="javascript:void(0)" class="tooltipFilter a-sip-Delay-cor-1024 a-sip-Delay-736 a-sip-Delay-414 a-sip-Delay-667 a-sip-Delay-375 a-sip-Delay-568 a-sip-Delay-320 a-sip-Delay-684 a-sip-Delay-360 a-sip-Delay-640 a-sip-Delay-412 a-sip-Delay-732" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">
                                        <span>Specify the expected rate of returns.</span>
                                    </a>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 top">
                        <div class="form-group">
                            <div class="col-sm-3 mblSize" style="padding-top:15px;">
                                <label for="year">Delay in starting SIP from Today(Months):</label>
                            </div>
                            <div class="col-sm-6 hide4Mobile">
                                <input type="text" name="balance" id="delaySlider" class="form-control">
                            </div>
                            <div class="col-sm-3 mblsm2" style="padding-top:15px;">
                                
                               <div class="form-elem">
                                 <input type="text" id="txtDelay" name="txtDelay" style="width:70% !important;margin-top:1px" class="input" value="0" size="10%" onkeypress="return isNumberKey(event)"/>    
                                 &nbsp;
                                  <span class="right-badge right-badge-sip-Delay-1024 tbiphn right-badge-sip-Delay-736 right-badge-sip-Delay-414 right-badge-sip-Delay-667 right-badge-sip-Delay-375 right-badge-sip-Delay-568 right-badge-sip-Delay-320 right-badge-sip-Delay-684 right-badge-sip-Delay-360 right-badge-sip-Delay-640 right-badge-sip-Delay-412 right-badge-sip-Delay-732 " style="padding-top:10px;">Mts.</span>
                                </div>
                                 <a href="javascript:void(0)" class="tooltipFilter a-sip-Delay-cor-1024 a-sip-Delay-736 a-sip-Delay-414 a-sip-Delay-667 a-sip-Delay-375 a-sip-Delay-568 a-sip-Delay-320 a-sip-Delay-684 a-sip-Delay-360 a-sip-Delay-640 a-sip-Delay-412 a-sip-Delay-732" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">
                                        <span>Specify the months after which you would invest.</span>
                                </a>
                               
                            </div>
                        </div>
                    </div>     

                    <div class="col-sm-9">
                        <button type="button" class="btn  btn-danger calculate_btn-sip-delay-1024 calculate_btn-sip-Delay-736 calculate_btn-sip-Delay-414 calculate_btn-sip-Delay-667 calculate_btn-sip-Delay-375 calculate_btn-sip-Delay-568 calculate_btn-sip-Delay-320 calculate_btn-sip-Delay-684 calculate_btn-sip-Delay-360 calculate_btn-sip-Delay-640 calculate_btn-sip-Delay-412 calculate_btn-sip-Delay-732" id="btn_calcSIP_Delay" style="margin-left: 50%;margin-top: 8%;margin-bottom: 10%;">Calculate</button>
                        <button type="button" class="btn btn-danger reset_btn-sip-Delay-736 reset_btn-sip-Delay-414 reset_btn-sip-Delay-667 reset_btn-sip-Delay-375 reset_btn-sip-Delay-568 reset_btn-sip-Delay-320 reset_btn-sip-Delay-684 reset_btn-sip-Delay-360 reset_btn-sip-Delay-640 reset_btn-sip-Delay-412 reset_btn-sip-Delay-732" onClick="ResetSliderSIPDelay()" style="margin-top: 8%;margin-bottom: 10%;">Reset</button>
                    </div>

                    <div class="col-sm-7">
                        <span id="total" style="font-weight: bold;color:red;font-size: 30px;margin-left:20%"></span>
                    </div>

                    <div class="col-md-12">
                        <div class="panel panel-primary resultSIP_Delay-1024" id="resultSIP_Delay" style="display:none;">
                            <div class="panel-heading"> 
                                <h3 class="panel-title">Results</h3> 
                            </div>
                            <div class="panel-body">

                                <div class="col-md-12" >
                                  
                                    <div class="col-md-10" ID="DIVresult" style="padding-top:10px;margin-left:40px;">
                                       
                                        
                                        
                                       <!--  <div class="row">
                                            <div class="col-md-9">
                                                 Lumpsum amount need to be save to achive Target Corpus
                                            </div>
                                            <div class="col-md-3">
                                                 <button class="btn btn-info btn-lg FV" id='LumpsumPv' type="button" style="width:150px !important;"></button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row" style="padding-top:10px;">
                                            <div class="col-md-9">
                                                  Monthly saving required to achive Target Corpus `
                                            </div>
                                            <div class="col-md-3">
                                                  <button class="btn btn-success btn-lg investment" id='MonthlySIPInvest' type="button" style="width:150px !important;"></button>
                                            </div>
                                         </div>
                                         <hr>
                                        <div class="row" style="padding-top:10px;">
                                            <div class="col-md-9">
                                                 If you delay SIP investment by <label for="delayMnth" id="delayMnth"></label> Months , you have to save monthy  of below amount .
                                            </div>
                                            <div class="col-md-3">
                                                 <button class="btn btn-warning btn-lg earning" id='sipDelayAmntLoose' type="button" style="width:150px !important;"></button>
                                            </div>
                                        </div>
                                          <hr>
                                        <div class="row" style="padding-top:10px;">
                                            <div class="col-md-9">
                                                  If you delay Lumpsum by <label for="delayMnth" id="delayMnthLumpsum"></label> Months , you stand to loose below amount from Target Corpus.
                                            </div>
                                            <div class="col-md-3">
                                                <button class="btn btn-warning btn-lg earning" id='LumpsumDelayAmntLoose' type="button" style="width:150px !important;"></button>
                                            </div>
                                        </div> -->
                                        
                                        
                                        
                                        
                                        
                                      <div class="futurevalue">
                                            <div class="hedgTxt02" style="padding-left:100px;">
                                                Lumpsum amount need to be save to achive Target Corpus
                                            </div>
                                            <div class="value id_1024" style="padding-left:190px;">
                                                <span></span> 
                                                <button class="btn btn-info btn-lg FV" id='LumpsumPv' type="button" style="width:200px;"></button>
                                                <input type="hidden" name="hidden_LumpsumPv" id="hidden_LumpsumPv" value="">
                                            </div>
                                        </div>
                                       
                                       <div class="br1024" style="text-align:center;padding-top:20px;"><b>OR</b></div>
                                        <div class="futurevalue">
                                            <div class="hedgTxt02 " style="padding-top:8px;padding-left:100px;">
                                                Monthly saving required to achive Target Corpus
                                            </div>
                                            <div class="value id_1024 colr01" style="padding-left:215px;">
                                                <span></span>
                                                <button class="btn btn-success btn-lg investment" id='MonthlySIPInvest' type="button" style="width:150px ;"></button>
                                                <input type="hidden" name="hidden_MonthlySIPInvest" id="hidden_MonthlySIPInvest" value="">
                                            </div>
                                        </div>
                                       <hr class="hr1024">
                                        <div class="futurevalue">
                                            <div class="hedgTxt02" style="padding-top:8px;padding-left:100px;">
                                                If you delay SIP investment by <label for="delayMnth" id="delayMnth"></label> Months , you have to save monthy  of below amount .
                                            </div>
                                            <div class="value id_1024 colr01" style="padding-left:215px;">
                                                <span></span>
                                                <button class="btn btn-warning btn-lg earning" id='sipDelayAmntLoose' type="button" style="width:150px;"></button>
                                                <input type="hidden" name="hidden_sipDelayAmntLoose" id="hidden_sipDelayAmntLoose" value="">
                                            </div>
                                        </div>
                                         <div style="text-align:center;padding-top:20px;"><b></b></div>
                                        <div class="futurevalue">
                                           <div class="hedgTxt02" style="padding-top:8px;padding-left:100px;">
                                                If you delay Lumpsum investment by <label for="delayMnth" id="delayMnthLumpsum"></label> Months , than you have to invest below amount as  Lumpsum Investment to achive Target Corpus.
                                            </div>
                                            <div class="value id_1024 colr01" style="padding-left:215px;">
                                                <span></span>
                                                <button class="btn btn-warning btn-lg earning" id='LumpsumDelayAmntLoose' type="button" style="width:150px ;"></button>
                                                <input type="hidden" name="hidden_LumpsumDelayAmntLoose" id="hidden_LumpsumDelayAmntLoose" value="">
                                            </div>
                                        </div>
                                         
                                         
                                         
                                    </div>
                                </div>
                            </div>
                            <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                        </div>
                    </div>
                </div>
               </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>     
     </div>
  </div>
    </div>



    

<!--  delay start -->

       <div class="col-md-8">

        <!-- Modal -->

        <div class="modal fade" id="delay_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">

            <div class="modal-dialog modal-lg" style="/*width:900px*/">



                <!-- Modal content-->

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        

                    </div>

                    <div class="modal-body">

                        <div class="panel panel-primary">

                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">SIP-Delay</h3> 

                            </div>
                            <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorDelayPre" name="form_DelayPre" id="form_DelayPre" method="post">
                                <div class="panel-body">

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize delaymblsize768" style="padding-top:15px;">

                                            <label for="deposit_amt"> Monthly Investment :</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input id="delayAgeslider" name="delayAgeslider" type="text"/>    

                                        </div>

                                        <div class="col-sm-3 mblsm2 delaytextbox" style="padding-top:15px;">

                                            <!--<input id="delaySIPValue" name="delaySIPValue" class="form-control" value="18" type="text" size="10%" onkeypress="return isNumberKey(event)"/>    -->

                                             <div class="form-elem">

                                                    <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                    <input id="delaySIPValue" name="delaySIPValue" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input delaytextboxVal" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                            </div>

                                            &nbsp;

                                                <a href="javascript:void(0)" class="tooltipFilter delayTooltipFilter360px" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                        <span>Specify the monthly investment amount.</span>

                                                </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize delaymblsize768" style="padding-top:15px;">

                                            <label for="interest_rate">Expected interest rate(%):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" class="form-control" name="delayIncome" id="delayIncome">

                                        </div>

                                        <div class="col-sm-3 mblsm2 delaytextbox" style="padding-top:15px;">

                                            <!--<input id="delayRateOfInterest" name="delayRateOfInterest" class="form-control" value="1" type="text" onkeypress="return isNumberKey(event)" size="10%"/>   -->

                                             <div class="form-elem" style="padding-left:5px;">

                                                    <input id="delayRateOfInterest" name="delayRateOfInterest" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                    &nbsp;<span class="right-badge delayright-badge360" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                            </div>&nbsp;

                                            <a href="javascript:void(0)" class="tooltipFilter delayTooltipFilter360px" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the expected rate of returns.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize delaymblsize768" style="padding-top:15px;">

                                            <label for="year">Number of Installment(year):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" name="delayBalance" id="delayBalance" class="form-control">

                                        </div>

                                        <div class="col-sm-3 mblsm2 delaytextbox" style="padding-top:15px;">

                                            <!--<input type="text" id="delayInstallment" name="delayInstallment" class="form-control" value="1" size="10%" onkeypress="return isNumberKey(event)"/>   -->

                                            <div class="form-elem" style="padding-left:5px;">

                                                <input type="text" id="delayInstallment" name="delayInstallment" style="width:70% !important;margin-top:1px" class="input" value="0" size="10%" onkeypress="return isNumberKey(event)"/>    

                                                &nbsp;

                                                 <span class="right-badge delayright-badge360" style="padding-top:10px;">Yrs.</span>

                                            </div>&nbsp;

                                            <a href="javascript:void(0)" class="tooltipFilter delayTooltipFilter360px" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the total year for investment.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>   

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize delaymblsize768" style="padding-top:15px;">

                                            <label for="year">Delay in starting SIP from today(Enter in Months):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" name="delay" id="delay" class="form-control">

                                        </div>

                                        <div class="col-sm-3 mblsm2 delaytextbox" style="padding-top:15px;">

                                            <!--<input type="text" id="s_delay" name="s_delay" class="form-control" value="1" size="10%" onkeypress="return isNumberKey(event)"/>    -->

                                             <div class="form-elem">

                                                <input type="text" id="s_delay" name="s_delay" style="width:70% !important;margin-top:1px" class="input" value="0" size="10%" onkeypress="return isNumberKey(event)"/>    

                                                &nbsp;

                                                 <span class="right-badge delayright-badge360" style="padding-top:10px;">Mts.</span>

                                            </div>&nbsp;

                                            <a href="javascript:void(0)" class="tooltipFilter delayTooltipFilter360px" tabindex="-1" style="margin-left:187px;margin-top:-34px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the  months for delay SIP investment.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div> 



                                <div class="col-sm-9">

                                    <button type="button" class="btn btn-danger delayButton" id="btn_calcDelay" style="margin-left: 50%;margin-top: 8%;margin-bottom: 10%;">Calculate</button>

                                    <button type="button" class="btn btn-danger" id="btn_ResetDelay" style="margin-bottom: 2%;">Reset</button>

                                </div>



                                <div class="col-sm-7">

                                    <span id="total" style="font-weight: bold;color:red;font-size: 30px;margin-left:20%"></span>

                                    

                                </div>





                                <div class="col-md-12">

                                    <div class="panel panel-primary" id="resultdelay" style="display:none !important;width: 100%;">

                                        <div class="panel-heading"> 

                                            <h3 class="panel-title result_h3">SIP Delay Returns</h3> 

                                        </div>

                                        

                                        <div class="panel-body">



                                            <div class="col-md-12" >

                                                

                                                <div class="col-md-8 needResponsiveOuter" style="padding-top:10px;margin-left: 50px;width:90%">

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02" style="padding-top:8px;padding-left:120px;">

                                                            Maturity Value, If you had started MF SIP Today

                                                        </div>

                                                        <div class="value colr01 needResponsive1" style="padding-left:240px;">

                                                            <span></span> 

                   <!--                                         <span class="FV"></span>-->

                                                            <button class="btn btn-info btn-lg invvaluetoday delayBtnVal" id='tbl_full' type="button" style="width:auto !important;"></button>
                                                            <input id="hidden_invvaluetoday" name="hidden_invvaluetoday" value="" type="hidden">
                                                        </div>

                                                    </div>

                                                    <hr>



                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02 needResponsivetext" style="padding-top:8px;padding-left:110px;">

                                                           Maturity Value, If you Delay your Investments

                                                        </div>

                                                        <div class="value colr01 needResponsive1" style="padding-left:240px;">

                                                            <span></span>

                <!--                                            <span class="earning"></span>-->

                                                            <button class="btn btn-warning btn-lg invvaluedelay delayBtnInv" id='tbl_int' type="button" style="width:auto !important;"></button>
                                                            <input id="hidden_invvaluedelay" name="hidden_invvaluedelay" value="" type="hidden">
                                                        </div>

                                                    </div>

                                                    <hr>

                                                    <div class="futurevalue">

                                                        <!--<div class="hedgTxt02" style="padding-top:8px;padding-left:190px;">

                                                            You will be making total payments of

                                                        </div>

                                                        <div class="value colr01" style="padding-left:230px;">

                                                            <span></span>

                <!--                                            <span class="earning"></span>

                                                            <button class="tn btn-success btn-lg delaycost" id='tbl_int' type="button" style="width:150px !important;"></button>

                                                            <br>

                                                        

                                                        </div> -->

                                                        <div class="hedgTxt02 delay_1024" style="padding-top:8px;">

                                                            <span></span>

                                                            If you delay investment by <strong><span id="lostMonths"> </span> Months</strong>, you stand to loose <strong> Rs.<span id="lostmoney"> </span>/- </strong>in Maturity Value of your Investment !! 

                                                        </div>
                                                        <input id="hidden_lostmoney" name="hidden_lostmoney" value="" type="hidden">            
                                                    </div>

                                                </div>

                                            </div>



                                        </div>
                                        <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div> 
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

<!-- end delay -->



<!--  pv start-->

     <div class="col-md-8">

        <!-- Modal -->

        <div class="modal fade" id="pv_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">

            <div class="modal-dialog modal-lg" style="/*width:900px*/">



                <!-- Modal content-->

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        <h4 class="modal-title">Present Value</h4>

                    </div>

                    <div class="modal-body">

                        <div class="panel panel-primary">

                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">Present Value</h3> 

                            </div>
                            <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorPV" name="form_pv" id="form_pv" method="post">
                                <div class="panel-body">

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="deposit_amt">Amount you are going to get in future:</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input id="pvageslider" name="pvageslider" type="text"/>    

                                        </div>

                                        

                                        

                                        <div class="col-sm-3 mblsm2" style="padding-top:15px;">

                                           <!-- <input id="MonthSIPPV" name="" class="form-control" value="18" type="text" size="10%" onkeypress="return isNumberKey(event)"/>--> 

                                            <div class="form-elem">

                                                <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                <input id="MonthSIPPV" name="MonthSIPPV" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input pvFAmnt-1024 pvFAmnt-320 pvFAmnt-360  pvFAmnt-768 pvFAmnt-736 pvFAmnt-640 pvFAmnt-684 pvFAmnt-732 pvFAmnt-568 pvFAmnt-375 pvFAmnt-667 pvFAmnt-414 pvFAmnt-412" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                            </div>

                                            &nbsp;

                                                <a href="javascript:void(0)" class="tooltipFilterPV a-PV-month-1024 a-PV-month-768 a-PV-month-736 a-PV-month-360 a-PV-month-640 a-PV-month-412 a-PV-month-684 a-PV-month-732 a-PV-month-320 a-PV-month-568 a-PV-month-375 a-PV-month-667 a-PV-month-414 a-PV-month-736" tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                        <span>Specify the monthly investment amount.</span>

                                                </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="interest_rate">Rate of interest per annum (%):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" class="form-control" name="pv_income" id="pv_income">

                                        </div>

                                        <div class="col-sm-3 mblsm2" style="padding-top:15px;">

                                            <!--<input id="pv_rateOfReturn" name="s_pvincome" class="form-control" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)"/> -->   

                                            <div class="form-elem" style="padding-left:5px;">

                                                <input id="pv_rateOfReturn" name="pv_rateOfReturn" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge right-badge-PV-1024 right-badge-PV-768 right-badge-PV-736 right-badge-PV-736 right-badge-PV-360 right-badge-PV-640 right-badge-PV-412 right-badge-PV-684 right-badge-PV-732 right-badge-PV-320 right-badge-PV-568 right-badge-PV-375 right-badge-PV-667 right-badge-PV-414 tb-PV-360 tb-PV-412 tb-PV-640 tb-PV-684 tb-PV-732 tb-PV-320 tb-PV-568 tb-PV-375 tb-PV-667 tb-PV-414  tb-PV-736 tb-PV-768 tb-PV-1024" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                            </div>

                                            &nbsp;

                                                <a href="javascript:void(0)" class="tooltipFilterPV a-PV-month-1024 a-PV-month-768 a-PV-month-736 a-PV-month-360 a-PV-month-640 a-PV-month-412 a-PV-month-684 a-PV-month-732 a-PV-month-320 a-PV-month-568 a-PV-month-375 a-PV-month-667 a-PV-month-414 a-PV-month-736" tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                        <span>Specify the expected rate of returns.</span>

                                                </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="year">No. of year after which you will receive the amount:</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" name="pv_balance" id="pv_balance" class="form-control">

                                        </div>

                                        <div class="col-sm-3 mblsm2" style="padding-top:15px;">

                                            <!--<input type="text" id="pv_installment" name="s_pvbalance" class="form-control" value="1" size="10%" onkeypress="return isNumberKey(event)"/>    -->

                                            <div class="form-elem" style="padding-left:5px;">

                                                <input id="pv_installment" name="pv_installment" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge right-badge-PV-1024 right-badge-PV-768 right-badge-PV-736 right-badge-PV-736 right-badge-PV-360 right-badge-PV-640 right-badge-PV-412 right-badge-PV-684 right-badge-PV-732 right-badge-PV-320 right-badge-PV-568 right-badge-PV-375 right-badge-PV-667 right-badge-PV-414" style="padding-top:10px;">Yrs.</span>

                                        </div>

                                            &nbsp;

                                            <a href="javascript:void(0)" class="tooltipFilterPV a-PV-month-1024 a-PV-month-768 a-PV-month-736 a-PV-month-360 a-PV-month-640 a-PV-month-412 a-PV-month-684 a-PV-month-732 a-PV-month-320 a-PV-month-568 a-PV-month-375 a-PV-month-667 a-PV-month-414 a-PV-month-736" tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the total year for investment.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>     

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize">



                                        </div>

                                        <div class="col-sm-4" style="display:none">

                                            <input type="radio" name="freq" class="freq" value="1" checked="true" ><label for="year">Monthly</label> &nbsp;

                                            <input type="radio" name="freq" class="freq" value="2"  ><label for="year">Quaterly</label>



                                        </div>

                                    </div>

                                </div>



                                <div class="col-sm-9">

                                    <button type="button" class="btn btn-danger calculate_btn-PV-1024 calculate_btn-PV-768 calculate_btn-sip-o-meter-736 calculate_btn calculate_btn-PV-360 calculate_btn-PV-640 calculate_btn-PV-412 calculate_btn-PV-684 calculate_btn-PV-732 calculate_btn-PV-320 calculate_btn-PV-568 calculate_btn-PV-375 calculate_btn-PV-667 calculate_btn-PV-414" id="btn_calcPV" style="margin-left: 50%;margin-top: 8%;margin-bottom: 10%;">Calculate</button>

                                    <button type="button" class="btn btn-danger reset_btn-PV-1024 reset_btn-PV-568 reset_btn-PV-768 reset_btn reset_btn-PV-736  reset_btn-PV-360 reset_btn-PV-640 reset_btn-PV-412 reset_btn-PV-684 reset_btn-PV-732 reset_btn-PV-320 reset_btn-PV-375 reset_btn-PV-667 reset_btn-PV-414 " id="btn_ResetPV" style="margin-bottom: 2%;">Reset</button>

                                </div>



                                <div class="col-sm-7">

                                    <span id="total" style="font-weight: bold;color:red;font-size: 30px;margin-left:20%"></span>

                                </div>





                                <div class="col-md-12">

                                    <div class="panel panel-primary" id="pvresult" style="display:none !important;">

                                        <div class="panel-heading"> 

                                            <h3 class="panel-title result_h3">PV Returns</h3> 

                                        </div>

                                        <div class="panel-body">



                                            <div class="col-md-12" style="text-align:center">

                                               <!-- <div class="col-md-6">

                                                    <div class="panel panel-body">

                                                        <div class="text-center"><h4>Breakdown</h4></div>

                                                        <canvas id="mf-chart_pv"></canvas>

                                                    </div>

                                                </div>-->

                                               <div class="col-md-6">

                                                    <div class="panel">

                                                        <div class="hedgTxt02" style="margin-top: 20px;" >

                                                           The present value of the future amount is 

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-md-6" style="padding-top:10px;padding-bottom: 20px;">

                                                    <div class="futurevalue">                                                        

                                                        <div class="value" >

                                                            <span></span> 

                   <!--                                         <span class="FV"></span>-->

                                                            <button class="btn btn-info btn-lg FVPV" id='tbl_full' type="button" style="width:auto !important;"></button>
                                                            <input type="hidden" name="PV_hidden" id="PV_hidden" value="">
                                                        </div>

                                                    </div>

                                                    

                                                  <!-- 

                                                    <hr>

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02 lineHt fl" style="padding-top:8px;">

                                                            Your future value

                                                        </div>

                                                        <div class="value colr01">

                                                            <span></span>

                                                          <span class="investment"></span>



                                                            <button class="btn btn-success btn-lg pvinvestment" id='emi' type="button" style="width:150px !important;"></button>

                                                        </div>

                                                    </div>

                                                    <hr>

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02 lineHt fl" style="padding-top:8px;">

                                                            Total Principle

                                                        </div>

                                                        <div class="value colr01">

                                                            <span></span>

                                                           <span class="earning"></span>

                                                            <button class="btn btn-warning btn-lg pvprinciple" id='tbl_int' type="button" style="width:150px !important;"></button>

                                                        </div>

                                                    </div>

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02 lineHt fl" style="padding-top:8px;">

                                                            Total Interest

                                                        </div>

                                                        <div class="value colr01">

                                                            <span></span>

                                                           <span class="earning"></span>

                                                            <button class="btn btn-warning btn-lg pvinterest" id='tbl_int' type="button" style="width:150px !important;"></button>

                                                        </div>

                                                    </div> -->

                                                </div>

                                            </div>



                                        </div>
                                        <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                                    </div>



                                </div>





                            </div>
                            </form>        
                        </div>                       

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>

                </div>



            </div>

        </div>

    </div>

    <!-- end pv -->



    <!--  need start-->

    <div class="col-md-8">

        <!-- Modal -->

        <div class="modal fade" id="SIPneed" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">

            <div class="modal-dialog modal-lg" style="/*width:900px*/">



                <!-- Modal content-->

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        <h4 class="modal-title">SIP-Need</h4>

                    </div>

                    <div class="modal-body">

                        <div class="panel panel-primary">

                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">SIP Need</h3> 

                            </div>
                            <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorNeed" name="form_Need" id="form_Need" method="post">    
                                 <div class="panel-body">

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="deposit_amt"> Target Amount Needed :</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input id="need_ageslider" name="need_ageslider" type="text"/>  

                                        </div>

                                        <div class="col-sm-3 mbsmsize" style="padding-top:15px;">                                           

                                            <!--<input id="needSIPValue" name="s_need_ageslider" class="form-control" value="18" type="text" size="10%" onkeypress="return isNumberKey(event)"/> -->

                                            <div class="form-elem">

                                                <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                <input id="needSIPValue" name="needSIPValue" style="width:70% !important;" class="input NeedFAmnt-1024 NeedFAmnt-320 NeedFAmnt-412 NeedFAmnt-360 NeedFAmnt-768 NeedFAmnt-736 NeedFAmnt-640 NeedFAmnt-684 NeedFAmnt-732 NeedFAmnt-568 NeedFAmnt-375 NeedFAmnt-667 NeedFAmnt-414" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                            </div>

                                            &nbsp;

                                            <a href="javascript:void(0)" class="tooltipFilterPV  a-Need-month-1024 a-Need-month-768 a-Need-month-736 a-Need-month-360 a-Need-month-640 a-Need-month-412 a-Need-month-684 a-Need-month-732 a-Need-month-320 a-Need-month-568 a-Need-month-375 a-Need-month-667 a-Need-month-414 a-Need-month-736" tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the target amount for investment.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="interest_rate">Expected interest rate(%):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" class="form-control" name="need_income" id="need_income">

                                        </div>

                                        <div class="col-sm-3 mbsmsize" style="padding-top:15px;">                                        

                                            <!--<input id="needrateOfInterest" name="s_need_income" class="form-control" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)"/>-->

                                            <div class="form-elem" style="padding-left:5px;">

                                                <input id="needrateOfInterest" name="needrateOfInterest" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge  tb-Need  right-badge-Need-1024 right-badge-Need-768 right-badge-Need-736 right-badge-Need-736 right-badge-Need-360 right-badge-Need-640 right-badge-Need-412 right-badge-Need-684 right-badge-Need-732 right-badge-Need-320 right-badge-Need-568 right-badge-Need-375 right-badge-Need-667 right-badge-Need-414 tb-Need-360 tb-Need-412 tb-Need-640 tb-Need-684 tb-Need-732 tb-Need-320 tb-Need-568 tb-Need-375 tb-Need-667 tb-Need-414  tb-Need-736 tb-Need-768 tb-Need-1024 " ><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                            </div>

                                            &nbsp;

                                            <a href="javascript:void(0)" class=" tooltipFilterPV a-Need-month-1024 a-Need-month-768 a-Need-month-736 a-Need-month-360 a-Need-month-640 a-Need-month-412 a-Need-month-684 a-Need-month-732 a-Need-month-320 a-Need-month-568 a-Need-month-375 a-Need-month-667 a-Need-month-414 a-Need-month-736 " tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the expected rate of returns.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-sm-12 top">

                                    <div class="form-group">

                                        <div class="col-sm-3 mblSize" style="padding-top:15px;">

                                            <label for="year">Number of Installment(Months):</label>

                                        </div>

                                        <div class="col-sm-6 hide4Mobile">

                                            <input type="text" name="need_balance" id="need_balance" class="form-control">

                                        </div>

                                        <div class="col-sm-3 mbsmsize" style="padding-top:15px;">                                        

                                            <!--<input type="text" id="needinstallment" name="s_need_balance" class="form-control" value="1" size="10%" onkeypress="return isNumberKey(event)"/>-->

                                            <div class="form-elem" style="padding-left:5px;">

                                                <input id="needinstallment" name="needinstallment" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge right-badge-Need right-badge-Need-1024 right-badge-Need-768 right-badge-Need-736 right-badge-Need-736 right-badge-Need-360 right-badge-Need-640 right-badge-Need-412 right-badge-Need-684 right-badge-Need-732 right-badge-Need-320 right-badge-Need-568 right-badge-Need-375 right-badge-Need-667 right-badge-Need-414" style="padding-top:10px;">Mts.</span>

                                            </div>

                                            &nbsp;

                                            <a href="javascript:void(0)" class=" tooltipFilterPV a-Need-month-1024 a-Need-month-768 a-Need-month-736 a-Need-month-360 a-Need-month-640 a-Need-month-412 a-Need-month-684 a-Need-month-732 a-Need-month-320 a-Need-month-568 a-Need-month-375 a-Need-month-667 a-Need-month-414 a-Need-month-736" tabindex="-1" style="margin-left:175px;top:17"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                                    <span>Specify the total months for investment.</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>    





                                <div class="col-sm-9">

                                     <button type="button" class="btn btn-danger calculate_btn-Need-1024 calculate_btn-Need-768 calculate_btn-Need-736 calculate_btn calculate_btn-Need-360 calculate_btn-Need-640 calculate_btn-Need-412 calculate_btn-Need-684 calculate_btn-Need-732 calculate_btn-Need-320 calculate_btn-Need-568 calculate_btn-Need-375 calculate_btn-Need-667 calculate_btn-Need-414 " id="btn_calcSIPNeed" style="margin-left: 50%;margin-top: 9%;margin-bottom: 10%;">Calculate</button>

                                     <button type="button" class="btn btn-danger reset_btn-Need-1024 reset_btn-Need-568 reset_btn-Need-768 reset_btn reset_btn-Need-736  reset_btn-Need-360 reset_btn-Need-640 reset_btn-Need-412 reset_btn-Need-684 reset_btn-Need-732 reset_btn-Need-320 reset_btn-Need-375 reset_btn-Need-667 reset_btn-Need-414" id="btn_ResetSIPNeed" style="margin-bottom: 1%;">Reset</button>

                                </div>



                                <div class="col-sm-7">

                                    <span id="total" style="font-weight: bold;color:red;font-size: 30px;margin-left:20%"></span>

                                </div>



                                <div class="col-md-12">

                                    <div class="panel panel-primary" id="needresult" style="display:none !important; width: 100%;">

                                        <div class="panel-heading"> 

                                            <h3 class="panel-title result_h3">SIP Need Returns</h3> 

                                        </div>

                                        <div class="panel-body">



                                            <div class="col-md-12" >

                                                <div class="col-md-6" id="Pichart">

                                                    <div class="panel panel-body">

                                                        <div class="text-center"><h4></h4></div>

                                                        <canvas id="need_mf-chart"></canvas>

                                                    </div>

                                                </div>

                                                <div class="col-md-6" style="padding-top:10px;">

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02" style="padding-left:30px;">

                                                            <strong>Monthly SIP </strong>Amount Required To reach target amount of Rs.<strong><span id="needtargetValue"> </span></strong>/- after <strong><span id="needSIPValueYear"> </span></strong> Months

                                                        </div>

                                                        <div class="value resultSIPNeedBtn" style="padding-left:135px;">

                                                            <span></span> 

                   <!--                                         <span class="FV"></span>-->

                                                            <button class="btn btn-info btn-lg needFV" id='tbl_full' type="button" style="width:auto !important;"></button><br>
                                                            <input type="hidden" name="hidden_needFV" id="hidden_needFV" value="">
                                                            

                                                        </div>

                                                        

                                                    </div>

                                                    <hr>



                                                    <!--<div class="futurevalue">

                                                        <div class="hedgTxt02" style="padding-top:8px;padding-left:30px;">

                                                            Total number of SIP installments

                                                        </div>

                                                        <div class="value" style="padding-left:75px;">

                                                            <span></span>

                <!--                                            <span class="earning"></span>

                                                            <button class="btn btn-warning btn-lg needprinciple" id='tbl_int' type="button" style="width:200px !important;"></button>

                                                        </div>

                                                    </div>

                                                    <hr> -->

                                                    <div class="futurevalue">

                                                        <div class="hedgTxt02" style="padding-top:8px;padding-left:30px;">

                                                            You will be making total payments of

                                                        </div>

                                                        <div class="value resultSIPNeedBtn" style="padding-left:135px;">

                                                            <span></span>

                <!--                                            <span class="earning"></span>-->

                                                            <button class="tn btn-success btn-lg needinterest" id='tbl_int' type="button" style="width:auto !important;"></button>
                                                                <input type="hidden" name="hidden_needinterest" id="hidden_needinterest" value="">
                                                                <input type="hidden" name="hidden_chartX" id="hidden_chartX" value="">
                                                                <input type="hidden" name="hidden_chartY" id="hidden_chartY" value="">
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>



                                        </div>
                                       <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div> 
                                    </div>
                                </div>
                                     <input type="hidden" name="hidden_showTablePV" id="hidden_showTablePV" value="">
                            </div>
                                
                            </form>   
                        </div>

                        <div id="showTable">                

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>

                </div>



            </div>

        </div>

    </div>

    <!-- end need -->

    

    

    <!----------------------------- Dileep Code ---------------------------------->

    

    <!-- HLV Calculator -->

  

<div class="modal fade" id="HLV-Calc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Human Life Cover Calculator</h4>

            </div>
            <form action="<?php echo base_url(); ?>broker/calculators/pdf_calculatorHLV" name="form_HLV" id="form_HLV" method="post"> 
                 <div class="modal-body">

                <div class="panel panel-primary">

                    <div class="panel-heading"> 
                        <h3 class="panel-title hlv_h3_414 hlv_h3_360 h3_text">Human Life Cover Calculator</h3> 
                    </div>
                    <div class="panel-body panel_hlv_360 panel_hlv_768">
                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="deposit_amt">Tell us your Age:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="hlv_age" name="hlv_age" type="text"/>    

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">                                    

                                    <div class="form-elem">

                                        <input type="text" id="hlvs_age" name="hlvs_age" style="width:70% !important;margin-top:1px" class="input" value="0" size="10%" onkeypress="return isNumberKey(event)"/>    

                                        &nbsp;<span class="right-badge right-badge-hlv-414 hlv_pad_360 hlv_pad_375 hlv_pad_414 hlv_pad_640 hlv_pad_768 hlv_pad_736 hlv_pad_732 hlv_pad_684 hlv_pad_568 hlv_pad_667 hlv_pad_320" style="padding-top:10px;padding-right:11px;">Yrs.</span>

                                    </div>    

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="hret_age">When do you wish to Retire:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="hret_age" name="hret_age" type="text"/>    

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">                                    

                                    <div class="form-elem">

                                        <input type="text" id="s_hret_age" name="s_hret_age" style="width:70% !important;margin-top:1px" class="input" value="0" size="10%" onkeypress="return isNumberKey(event)"/>    

                                        &nbsp;<span class="right-badge right-badge-hlv-414 hlv_pad_360 hlv_pad_375 hlv_pad_414 hlv_pad_640 hlv_pad_768 hlv_pad_736 hlv_pad_732 hlv_pad_684 hlv_pad_568 hlv_pad_667 hlv_pad_320" style="padding-top:10px;padding-right:11px;">Yrs.</span>

                                    </div>                                    

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="interest_rate">Annual Income :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" class="form-control" name="hincome" id="hincome">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">                                    

                                    <div class="form-elem">

                                        <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                        <input id="hs_income" name="hs_income" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input hlv_box_768 hlv_box_640 hlv_box_736 hlv_box_732 hlv_box_684 hlv_box_568 hlv_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>  

                                    <a href="javascript:void(0)" class="tooltipFilter ttf_360 ttf_414 ttf_375 ttf_768 ttf_640 ttf_736 ttf_732 ttf_684 ttf_568 ttf_667 ttf_1024" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify your actual Annual Income post tax deductions.</span>

                                    </a>

                                    

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="year">Total Outstanding Liabilities :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="hbalance" id="hbalance" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">                                    

                                    <div class="form-elem">

                                        <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                        <input id="hs_balance" name="hs_balance" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input hlv_box_768 hlv_box_640 hlv_box_736 hlv_box_732 hlv_box_684 hlv_box_568 hlv_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div> 

                                    <a href="javascript:void(0)" class="tooltipFilter ttf_360 ttf_414 ttf_375 ttf_768 ttf_640 ttf_736 ttf_732 ttf_684 ttf_568 ttf_667 ttf_1024" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify amounts that are pending for payment(eg. loans).</span>

                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="year">Total Family Expenses (Monthly):</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="expense" id="expense" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">

                                    <div class="form-elem">

                                        <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                        <input id="s_expense" name="s_expense" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input hlv_box_768 hlv_box_640 hlv_box_736 hlv_box_732 hlv_box_684 hlv_box_568 hlv_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>

                                    <a href="javascript:void(0)" class="tooltipFilter ttf_360 ttf_414 ttf_375 ttf_768 ttf_640 ttf_736 ttf_732 ttf_684 ttf_568 ttf_667 ttf_1024" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify amounts that you spend on your family.Eg If you are earning 1 Lac per month and you spend 30,000 on your family and yourself,then specify.</span>

                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="year">Total Savings as of Today:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="overall_savings" id="overall_savings" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">

                                    <div class="form-elem">

                                        <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                        <input id="s_overall_savings" name="s_overall_savings" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input hlv_box_768 hlv_box_640 hlv_box_736 hlv_box_732 hlv_box_684 hlv_box_568 hlv_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>                                         

                                    <a href="javascript:void(0)" class="tooltipFilter ttf_360 ttf_414 ttf_375 ttf_768 ttf_640 ttf_736 ttf_732 ttf_684 ttf_568 ttf_667 ttf_1024" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify any assets that can be converted to cash quickly.</span>

                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblSize_768 mblsize1024 label-name">

                                    <label for="year">Existing Insurance Cover (Personal) :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="existinInsuranceCover" id="existinInsuranceCover" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsm2_768 mblsize1024 text-box">

                                    <div class="form-elem">

                                        <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                        <input id="s_existinInsuranceCover" name="s_existinInsuranceCover" style="width:70% ;margin-left:48px;margin-top:-61px" class="input hlv_box_768 hlv_box_640 hlv_box_736 hlv_box_732 hlv_box_684 hlv_box_568 hlv_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>                                        

                                    <a href="javascript:void(0)" class="tooltipFilter ttf_360 ttf_414 ttf_375 ttf_768 ttf_640 ttf_736 ttf_732 ttf_684 ttf_568 ttf_667 ttf_1024 " tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                        <span>Specify your insurance cover amount .</span>

                                    </a>

                                </div>

                            </div>

                        </div>



                        <div class="col-sm-9">

                            <button type="button" class="btn btn-danger calculate_btn hc_btn_360 hc_btn_640 hc_btn_732 hc_btn_768 hc_btn_1024 hc_btn_375 hc_btn_320 hc_btn_667 hc_btn_684 hc_btn_568 hc_btn_736 hc_btn_414 hc_btn_412" id="hlv_calc" style="margin-left: 45%;margin-top: 8%;margin-bottom: 5%;width: 28%">Calculate</button>

                            <button type="button" class="btn btn-danger reset_btn hc_rbtn_732 hc_rbtn_768 hc_rbtn_1024 hc_rbtn_375 hc_rbtn_320 hc_rbtn_667 hc_rbtn_684 hc_rbtn_568 hc_rbtn_736 hc_rbtn_414 hc_rbtn_412 hc_rbtn_640 hc_rbtn_375" onClick="HLVresetSlider()" style="margin-top: 8%;margin-bottom: 5%;">Reset</button>

                        </div>



                    </div>

                </div>





                    <div class="col-md-12">

                        <div class="panel panel-primary hlv_result"  style="display:none !important;">

                            <div class="panel-heading"> 

                                <h3 class="panel-title h3_text">Results</h3> 

                            </div>

                            <div class="panel-body">



                                <div class="col-md-12" >

                                  

                                    <div class="col-md-10 hlv_op_360 hlv_768_result hlv_640_result hlv_736_result hlv_732_result hlv_684_result hlv_568_result hlv_667_result" style="padding-top:10px;margin-left:70px;">

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 hlv_360_result1 hlv_768_result1 hlv_640_result1 hlv_736_result1 hlv_732_result1 hlv_684_result1 hlv_568_result1 hlv_667_result1" style="padding-left:29%;">

                                                Years Remaining for Retirement

                                            </div>

                                            <div class="value hlv_360_result1 hlv_768_btn hlv_1024_btn hlv_640_btn hlv_736_btn hlv_732_btn hlv_684_btn hlv_667_btn hlv_568_btn" style="padding-left:216px;">

                                                <span></span> 

                                                <button class="btn btn-info btn-lg FV" id='years' type="button" style="width:150px !important;"></button>
                                                  <input type="hidden" name="hidden_yearsHLV" id="hidden_yearsHLV" value="">
                                            </div>

                                        </div>
                                        <hr>

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 hlv_360_result2 hlv_768_result2 hlv_1024_btn hlv_640_result2 hlv_736_result2 hlv_732_result2 hlv_684_result2 hlv_568_result2 hlv_667_result2" style="padding-top:8px;padding-left: 18%">

                                                To protect your family,You need to have a Life Cover of

                                            </div>

                                            <div class="value colr01 hlv_360_result2 hlv_768_btn hlv_1024_btn hlv_640_btn hlv_736_btn hlv_732_btn hlv_684_btn hlv_667_btn hlv_568_btn" style="padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-success btn-lg investment" id='hlv_value' type="button" style="width:150px !important;"></button>
                                                 <input type="hidden" name="hidden_hlv_valueHLV" id="hidden_hlv_valueHLV" value="">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="futurevalue">

                                            <div class="hedgTxt02 hlv_360_result3 hlv_768_result3 hlv_640_result3 hlv_736_result3 hlv_732_result3 hlv_684_result3 hlv_568_result3 hlv_667_result3" style="padding-top:8px;padding-left:21px;">

                                                Considering the existing Insurance and Savings,You need to have a further insurance of 

                                            </div>

                                            <div class="value colr01 hlv_360_result3 hlv_1024_btn hlv_768_btn hlv_640_btn hlv_736_btn hlv_732_btn hlv_684_btn hlv_667_btn hlv_568_btn" style="padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-warning btn-lg earning" id='extraincome' type="button" style="width:150px !important;"></button>
                                                 <input type="hidden" name="hidden_extraincomeHLV" id="hidden_extraincomeHLV" value="">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                             <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                        </div>

                    </div>

            </div>
            </form>
            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>



   

  <!-- Marriage Calculator -->

  

<div class="modal fade" id="Marriage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header ">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Marriage Planning Calculator</h4>

            </div>

            <div class="modal-body">
            <form action="<?php echo base_url(); ?>broker/calculators/pdf_calculatorMarriage" name="form_Marriage" id="form_Marriage" method="post">
                <div class="panel panel-primary">

                    <div class="panel-heading"> 

                        <h3 class="panel-title mrg_h3_414 h3_text">Marriage Planning Calculator</h3> 

                    </div>
                   
                    <div class="panel-body">                    



                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="age">Current Age of Child:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="m_age" id="m_age" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_m_age" name="s_m_age" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mr_320_yrs mrg_pad_414 mr_input mr_640_yrs mr_684_yrs mr_732_yrs mr_568_yrs mr_667_yrs mr_736_yrs" style="padding-right:15px;">Yrs</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="year">Age of Child at the time of Marriage :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="marg_age" name="marg_age" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_marg_age" name="s_marg_age" class="input " value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mr_320_yrs mrg_pad_414 mr_input mr_640_yrs mr_684_yrs mr_732_yrs mr_568_yrs mr_667_yrs mr_736_yrs" style="padding-right:15px;">Yrs</span>

                                    </div>                                        

                                </div>     

                            </div>

                        </div>                    

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="year">Cost of Marriage as of today:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="m_cost" name="m_cost" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                        <div class="form-elem">

                                                <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                <input id="s_m_cost" name="s_m_cost" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input mr_box_640 mr_box_732 mr_box_684 mr_box_736 mr_box_568 mr_box_667 mr_box_1024" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                        </div>                                        

                                        <a href="javascript:void(0)" class="tooltipFilter mr_ttf_320 mr_ttf_360 mr_ttf_414 mr_ttf_375 mr_ttf_768 mr_ttf_640 mr_ttf_684 mr_ttf_732 mr_ttf_568 mr_ttf_667 mr_ttf_1024 mr_ttf_568 mr_ttf_736" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                            <span>The amount as of today you wish to spent on your child's marriage</span>

                                        </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="year">Total Savings :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="m_savings" name="m_savings" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                        <div class="form-elem">

                                                <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                <input id="s_m_savings" name="s_m_savings" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input mr_box_640 mr_box_684 mr_box_732 mr_box_736 mr_box_1024 mr_box_568 mr_box_667" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                        </div>

                                        <a href="javascript:void(0)" class="tooltipFilter mr_ttf_320 mr_ttf_360 mr_ttf_414 mr_ttf_375 mr_ttf_768 mr_ttf_640 mr_ttf_684 mr_ttf_732 mr_ttf_568 mr_ttf_667  mr_ttf_1024 mr_ttf_568 mr_ttf_736" tabindex="-1" style="margin-left:180px;top:6px"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                            <span>Savings you have in terms of cash or gold or silver which can be converted to cash later on.</span>

                                        </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="m_inflation">Expected Inflation rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="m_inflation" name="m_inflation" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_m_inflation" name="s_m_inflation" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mrg_pad_320 mrg_pad_360 mrg_pad_414 mrg_pad_375 mr_input mrg_pad_640 mrg_pad_684 mrg_pad_732 mrg_pad_736 mrg_pad_568 mrg_pad_667" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>                                    

                                </div>

                            </div>

                        </div>                    

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mr_mblSize_768  mr_mblSize_1024 label-name">

                                    <label for="return">Expected Return rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="m_return" name="m_return" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mr_mblsm2_768  mr_mblsm2_1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_m_return" name="s_m_return" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mrg_pad_320 mrg_pad_360 mrg_pad_414 mrg_pad_375 mr_input mrg_pad_640 mrg_pad_684 mrg_pad_732 mrg_pad_736 mrg_pad_568 mrg_pad_667" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>                                        

                                </div>

                            </div>

                        </div>                    

                        <div class="col-sm-9">

                            <button type="button" class="btn btn-danger calculate_btn mr_btn_1024 mr_btn_640 mr_btn_414 mr_btn_684 mr_btn_375 mr_btn_568 mr_btn_732 mr_btn_736 mr_btn_768 mr_btn_412 mr_btn_320 mr_btn_360 mr_btn_667" id="marg_calc" style="margin-left: 50%;margin-top: 3%;width: 20%;">Calculate</button>

                            <button type="button" class="btn btn-danger reset_btn mr_rbtn_1024 mr_rbtn_320 mr_rbtn_375 mr_rbtn_640 mr_rbtn_768 mr_rbtn_736 mr_rbtn_667 mr_rbtn_732 mr_rbtn_568 mr_rbtn_684 mr_rbtn_414 mr_rbtn_360" onClick="MarraigeresetSlider()" style="margin-top: 48px;margin-bottom: 5%;">Reset</button>

                        </div>

                    </div>
                  
                </div>

                <div class="col-md-12">

                    <div class="panel panel-primary mrg_result"  style="display:none !important;">

                        <div class="panel-heading"> 

                            <h3 class="panel-title h3_text">Results</h3> 

                        </div>

                        <div class="panel-body">



                            <div class="col-md-12" >



                                <div class="col-md-10 mrg_output mrg_op_360 mrg_768_result mrg_640_result mrg_568_result mrg_684_result mrg_732_result mrg_667_result mrg_736_result" style="padding-top:10px;margin-left:70px;">

                                    <div class="futurevalue">

                                        <div class="hedgTxt02 mrg_360_result1 mrg_1024_result1 mrg_768_result1 mrg_640_result1 mrg_568_result1 mrg_684_result1 mrg_732_result1" style="padding-top:8px;padding-left:20%;">

                                            The Amount required in Future for Marriage is :

                                        </div>

                                        <div class="value colr01 mrg_360_btn mrg_414_btn mrg_768_btn mrg_640_btn mrg_568_btn mrg_684_btn mrg_732_btn mrg_667_btn mrg_736_btn" style="padding-left:215px;">

                                            <span></span>

                                            <button class="btn btn-warning btn-lg earning" id='mrfuture_amount' type="button" style="width:150px !important;"></button>
                                            <input type="hidden" name="hidden_mrfuture_amount" id="hidden_mrfuture_amount" value="">

                                        </div>

                                    </div>

                                    <hr>

                                    <div class="futurevalue">

                                        <div class="hedgTxt02 mrg_360_result2 mrg_1024_result2 mrg_768_result2 mrg_640_result2 mrg_568_result2 mrg_684_result2 mrg_732_result2 mrg_736_result2" style="padding-left:28%;">

                                            Your Monthly Savings should be :

                                        </div>

                                        <div class="value mrg_360_btn mrg_414_btn mrg_768_btn mrg_640_btn mrg_568_btn mrg_684_btn mrg_732_btn mrg_667_btn mrg_736_btn" style="padding-left:215px;">

                                            <span></span> 

                                            <button class="btn btn-info btn-lg FV" id='mrmonthly_amount' type="button" style="width:150px !important;"></button>
                                             <input type="hidden" name="hidden_mrmonthly_amount" id="hidden_mrmonthly_amount" value="">

                                        </div>

                                    </div>

                                    <br>

                                    <p class="mrg_360_hr mrg_414_hr mrg_768_hr mrg_568_hr mrg_1024_hr mrg_736_hr" style="color: #D9D9D9;margin-left: 200px">------------------ <b style="color: black;">OR</b> ------------------</p>

                                    <div class="futurevalue">

                                        <div class="hedgTxt02 mrg_360_result3 mrg_1024_result3 mrg_768_result3 mrg_640_result3 mrg_568_result3 mrg_684_result3 mrg_732_result3" style="padding-top:8px;padding-left:29%;">

                                            One Time Lumpsum Investment :

                                        </div>

                                        <div class="value colr01 mrg_360_btn mrg_414_btn mrg_768_btn mrg_640_btn mrg_568_btn mrg_684_btn mrg_732_btn mrg_736_btn mrg_667_btn" style="padding-left:215px;">

                                            <span></span>

                                            <button class="btn btn-success btn-lg investment" id='mryearly_amount' type="button" style="width:150px !important;"></button>
                                             <input type="hidden" name="hidden_mryearly_amount" id="hidden_mryearly_amount" value="">

                                        </div>

                                    </div>
                                    

                                </div>

                            </div>

                        </div>
                         <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                    </div>

                </div>

            </form>

            </div>



            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

  

  

    <!-- Education Calculator -->

  

<div class="modal fade" id="Education" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span style="color:black" aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Child Education Planning Calculator</h4>

            </div>

            <div class="modal-body">
             <form action="<?php echo base_url(); ?>broker/calculators/pdf_calculatorEducation" name="form_Education" id="form_Education" method="post">
                <div class="panel panel-primary">

                    <div class="panel-heading"> 

                        <h3 class="panel-title h3_text edu_h3">Child Education Planning Calculator</h3> 

                    </div>

                    <div class="panel-body">                    



                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3" style="padding_top:6px">

                                    <label for="ec_age">Child's DOB:</label>

                                </div>

                                <div class="col-sm-3">

                                    <input type="text" name="ec_age" class="form-control datepicker" id="ec_age" required>

                                </div>

                                <div class="col-sm-2">

                                        <input id="s_ec_age" name="s_ec_age" class="form-control" aria-describedby="basic-addon1"  type="text" readonly>                                        

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-6 top">

                            <div class="form-group">

                                <div class="col-sm-6" style="padding_top:6px">

                                    <label for="e_inflation">Expected Inflation rate:</label>

                                </div>

                                <div class="col-sm-6">

                                        <input id="e_inflation" name="e_inflation" class="form-control" aria-describedby="basic-addon1" value="6" onkeypress="return isNumberKey(event)" type="text" >

                                </div>

                            </div>

                        </div>                    

                        <div class="col-sm-6 top">

                            <div class="form-group">

                                <div class="col-sm-6" style="padding_top:6px">

                                    <label for="return">Expected Return rate:</label>

                                </div>

                                <div class="col-sm-6">

                                        <input id="e_return" name="e_return" class="form-control" aria-describedby="basic-addon1" onkeypress="return isNumberKey(event)" value="8" type="text" >

                                </div>

                            </div>

                        </div>                  

                        <div>

                            <p class='head_center'>Schooling Info </p>

                            <hr class="hr_line">

                        </div>

                                                

                        <div class="col-sm-12 ">

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Years Remaining</label>

                                </div>

                                <div class="col-sm-9">

                                    <input type="text" class="form-control" id="sch_year" onkeypress="return isNumberKey(event)" name="sch_year">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Present Annual Cost</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="sch_cost" onkeypress="return isNumberKey(event)" name="sch_cost">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-8">

                                  <label for="ec_cost_opt">Total Cost for Schooling</label>

                                </div>

                                <div class="col-sm-9">

                                    <input type="text" class="form-control" id="sch_fut_cost" name="sch_fut_cost" readonly>

                                </div>

                                <a href="javascript:void(0)" class="tooltipFilter edu_ttf_360 edu_ttf_640 edu_ttf_414 edu_ttf_375 edu_ttf_320 edu_ttf_736 edu_ttf_667 edu_ttf_568 edu_ttf_684 edu_ttf_732 edu_ttf_768 edu_ttf_1024" tabindex="-1" style="margin-left:50px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                    <span>Specify the years left for your child to complete his schooling along with today's school fees.</span>

                                </a>

                            </div>

                        </div>

                        

                        <div>

                            <p class="head_center">Graduation Info </p>

                            <hr class="hr_line">

                        </div>

                        

                        

                        <div class="col-sm-12 ">

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">At What Age</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control" id="grd_year" onkeypress="return isNumberKey(event)" name="grd_year">

                              </div>

                            </div>

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">Present Cost</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control"id="grd_cost" onkeypress="return isNumberKey(event)" name="grd_cost">

                              </div>

                            </div>

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">Future Cost</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control" id="grd_fut_cost" name="grd_fut_cost" readonly>

                              </div>

                              <a href="javascript:void(0)" class="tooltipFilter edu_ttf_360 edu_ttf_640 edu_ttf_414 edu_ttf_375 edu_ttf_320 edu_ttf_736 edu_ttf_667 edu_ttf_568 edu_ttf_684 edu_ttf_732 edu_ttf_1024" tabindex="-1" style="margin-left:70px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                <span>Specify the year when your child will start his graduation course (Engineering,Doctor,Law,BscIT,Bcom etc.) .Also specify the costing as per today's cost.</span>

                              </a>

                            </div>

                            

                        </div>

                        

                        <div>

                            <p class="head_center">Post Graduation Info </p>

                            <hr class="hr_line">

                        </div>

                        

                        

                        <div class="col-sm-12 ">

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">At What Age</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control"id="pgrd_year" onkeypress="return isNumberKey(event)" name="grd_year">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Present Cost</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="pgrd_cost" onkeypress="return isNumberKey(event)" name="pgrd_cost">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Future Cost</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="pgrd_fut_cost" name="pgrd_fut_cost" readonly>

                                </div>

                                <a href="javascript:void(0)" class="tooltipFilter edu_ttf_360 edu_ttf_640 edu_ttf_414 edu_ttf_375 edu_ttf_320 edu_ttf_736 edu_ttf_667 edu_ttf_568 edu_ttf_684 edu_ttf_732 edu_ttf_1024" tabindex="-1" style="margin-left:70px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                    <span>Specify the year when your child will start his post-graduation course (MBA,Mcom,ME,MS etc.) .Also specify the costing as per today's cost.</span>

                                </a>

                            </div>

                        </div>

                        

                        <div>

                            <p class="head_center">Career </p>

                            <hr class="hr_line">

                        </div>

                        

                        

                        <div class="col-sm-12 ">

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">At What Age</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="carr_year" onkeypress="return isNumberKey(event)" name="carr_year">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Present Cost</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="carr_cost" onkeypress="return isNumberKey(event)" name="carr_cost">

                                </div>

                            </div>

                            <div class="col-sm-4">

                                <div class="col-sm-7">

                                  <label for="ec_cost_opt">Future Cost</label>

                                </div>

                                <div class="col-sm-9">

                                  <input type="text" class="form-control" id="carr_fut_cost" name="carr_fut_cost" readonly>

                                </div>

                                <a href="javascript:void(0)" class="tooltipFilter edu_ttf_360 edu_ttf_640 edu_ttf_414 edu_ttf_375 edu_ttf_320 edu_ttf_736 edu_ttf_667 edu_ttf_568 edu_ttf_684 edu_ttf_732 edu_ttf_1024" tabindex="-1" style="margin-left:70px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                    <span>Specify the amount you are going to invest for your child's business dreams</span>

                                </a>

                            </div>

                        </div>

                        

                        

                        <div>

                            <p class="head_center">Marriage</p>

                            <hr class="hr_line">

                        </div>

                        

                        

                        <div class="col-sm-12 ">

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">At What Age</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control" id="marg_year" onkeypress="return isNumberKey(event)" name="marg_year">

                              </div>

                            </div>

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">Present Cost</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control" id="marg_cost" onkeypress="return isNumberKey(event)" name=marg_cost">

                              </div>

                            </div>

                            <div class="col-sm-4">

                              <div class="col-sm-7">

                                <label for="ec_cost_opt">Future Cost</label>

                              </div>

                              <div class="col-sm-9">

                                <input type="text" class="form-control" id="marg_fut_cost" name="marg_fut_cost" readonly>

                              </div>

                              <a href="javascript:void(0)" class="tooltipFilter edu_ttf_360 edu_ttf_640 edu_ttf_414 edu_ttf_375 edu_ttf_320 edu_ttf_736 edu_ttf_667 edu_ttf_568 edu_ttf_684 edu_ttf_732 edu_ttf_1024" tabindex="-1" style="margin-left:70px;"><img class="child-rupee" src="<?php echo base_url(); ?>extras/img/question.png">

                                <span>Specify the amount you are going to invest for your child's marriage.</span>

                              </a>

                            </div>

                        </div>

                                           

                        <div class="col-sm-9">

                            <button type="button" class="btn btn-danger calculate_btn ed_btn_375 ed_btn_414 ed_btn_667 ed_btn_684 ed_btn_412 ed_btn_1024 ed_btn_736 ed_btn_360 ed_btn_568 ed_btn_640 ed_btn_732 ed_btn_320 ed_btn_768" id="edu_calc" style="margin-left: 42%;margin-top: 8%;width: 28%;">Calculate</button>

                            <button type="button" class="btn btn-danger reset_btn ed_rbtn_320 ed_rbtn_568 ed_rbtn_684 ed_rbtn_375 ed_rbtn_667 ed_rbtn_360 ed_rbtn_640 ed_rbtn_414 ed_rbtn_736 ed_rbtn_732 ed_rbtn_768 ed_rbtn_1024" onClick="educationResetSlider()" style="margin-top: 8%;">Reset</button>

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <center>

                        <div class="panel panel-primary edu_result"  style="display:none !important;">

                            <div class="panel-heading"> 

                                <h3 class="panel-title h3_text">Results</h3> 

                            </div>

                            <div class="panel-body">

                                <div class="col-md-12" >

                                    <div class="col-md-10 edu_360_op" style="padding-top:10px;margin-left:65px;">

                                        <div class="futurevalue">

                                            <div class="hedgTxt02" style="padding-top:8px/*padding-left:29%;*/">

                                                Total Cost of Upbringing your child is :

                                            </div>

                                            <div class="value mr_res_1024 colr01" style="//padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-warning btn-lg earning" id='edfuture_amount' type="button" style="width:150px !important;"></button>

                                            </div>

                                        </div>

                                        <hr>

                                        <div class="futurevalue">

                                            <div class="hedgTxt02" style="padding-top:8px/*padding-left:29%;*/">

                                                Life Cover needed to protect your child's future

                                            </div>

                                            <div class="value mr_res_1024 colr01" style="//padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-warning btn-lg earning" id='edhlv_amount' type="button" style="width:150px !important;"></button>

                                            </div>

                                        </div>

                                        <br>

                                        <hr>                                        

                                        <div class="futurevalue">

                                            <div class="hedgTxt02" style="//padding-left:28%;">

                                                Your Monthly Savings should be :

                                            </div>

                                            <div class="value mr_res_1024" style="//padding-left:190px;">

                                                <span></span> 

                                                <button class="btn btn-info btn-lg FV" id='edmonthly_amount' type="button" style="width:150px !important;"></button>

                                            </div>

                                        </div>

                                        <br> 

                                        <p style="color: #D9D9D9;">------------------ <b style="color: black;">OR</b> ------------------</p>

                                        

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 " style="padding-top:8px;/*padding-left:29%;*/">

                                                One Time Lumpsum Investment :

                                            </div>

                                            <div class="value mr_res_1024 colr01" style="//padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-success btn-lg investment" id='edyearly_amount' type="button" style="width:150px !important;"></button>

                                            </div>

                                        </div>

                                        <br>

                                        

                                    </div>

                                </div>

                            </div>

                        </div>

                    </center>                    

                </div>
            </form>




            </div>



            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

    

    

     <!-- Car Calculator -->

  

<div class="modal fade" id="Car_calc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header ">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span style="color:black" aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Car Planning Calculator</h4>

            </div>

            <div class="modal-body">
                <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorCar" name="form_Car" id="form_Car" method="post"> 
                    <div class="panel panel-primary">

                    <div class="panel-heading"> 

                        <h3 class="panel-title result_h3">Car Planning Calculator</h3> 

                    </div>

                    <div class="panel-body">                    



                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="age">Present Cost of Car :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="c_amount" id="c_amount" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem">

                                            <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                            <input id="s_c_amount" name="s_c_amount" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input Car-1024 Car-768 Car-414 Car-736 Car-375  Car-667 Car-360 Car-640 Car-412 Car-732 Car-684 Car-320  Car-568" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>                                        

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="age">Years to purchase Car :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="c_period" id="c_period" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_c_period" name="s_c_period" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mrgnleftYrPurchase right-badge-Car-1024 right-badge-Car-768 right-badge-Car-414 right-badge-Car-736  right-badge-Car-375 right-badge-Car-667 right-badge-Car-360 right-badge-Car-640 right-badge-Car-412 right-badge-Car-732 right-badge-Car-684 right-badge-Car-320 right-badge-Car-568" style="padding-right:17px;">Yrs</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name ">

                                    <label for="c_inflation">Expected Inflation Rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="c_inflation" name="c_inflation" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_c_inflation" name="s_c_inflation" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge right-badge-Car-1024 right-badge-Car-768 right-badge-Car-414 right-badge-Car-736 right-badge-Car-375 right-badge-Car-667 right-badge-Car-360 right-badge-Car-640 right-badge-Car-412 right-badge-Car-732 right-badge-Car-684 right-badge-Car-320 right-badge-Car-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>

                                </div>

                            </div>

                        </div>                    

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="return">Expected Return Rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="c_return" name="c_return" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_c_return" name="s_c_return" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge right-badge-Car-1024 right-badge-Car-768 right-badge-Car-414 right-badge-Car-736 right-badge-Car-360 right-badge-Car-375 right-badge-Car-667 right-badge-Car-640 right-badge-Car-412 right-badge-Car-732 right-badge-Car-684 right-badge-Car-320 right-badge-Car-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>

                                </div>

                            </div>

                        </div>                                          

                        <div class="col-sm-9">

                            <button type="button" class="btn btn-danger calculate_btn-Car-1024 calculate_btn-Car-768 calculate_btn-Car-414 calculate_btn-Car-736  calculate_btn-Car-667 calculate_btn-Car-375 calculate_btn-Car-360 calculate_btn-Car-640 calculate_btn-Car-412 calculate_btn-Car-732 calculate_btn-Car-684 calculate_btn-Car-320 calculate_btn-Car-568" id="car_calc" style="margin-left: 50%;margin-top: 5%;width: 28%;">Calculate</button>

                            <button type="button" class="btn btn-danger reset_btn-Car-1024  reset_btn-Car-768 reset_btn-Car-414 reset_btn-Car-736 reset_btn-Car-360 reset_btn-Car-375 reset_btn-Car-667 reset_btn-Car-412 reset_btn-Car-640 reset_btn-Car-732 reset_btn-Car-684 reset_btn-Car-320 reset_btn-Car-568" onclick="CarresetSlider()" style="margin-top: 60px;margin-bottom: 5%;">Reset</button>

                        </div>

                    </div>

                     </div>
              
                     <div class="col-md-12">

                    

                        <div class="panel panel-primary car_result"  style="display:none !important;">
                            <center>
                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">Results</h3> 

                            </div>

                            <div class="panel-body">

                                <div class="col-md-12" >

                                    <div class="col-md-10 car_op" style="padding-top:10px;margin-left:65px;">

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 cphdtxt1024" style="//padding-left:28%;">

                                                The Future Value for your Car will be

                                            </div>

                                            <div class="value cpvalue1024" style="//padding-left:190px;">

                                                <span></span> 

                                                <button class="btn btn-warning btn-lg FV" id='crfuture_amount' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" id="hidden_crfuture_amount" name="hidden_crfuture_amount" value="">   
                                            </div>

                                        </div>

                                        <div class="futurevalue top">

                                            <div class="hedgTxt02 cphdtxt1024" style="//padding-left:28%;">

                                                Your Monthly Savings should be :

                                            </div>

                                            <div class="value cpvalue1024" style="//padding-left:190px;">

                                                <span></span> 

                                                <button class="btn btn-info btn-lg FV" id='crmonthly_amount' type="button" style="width:150px !important;"></button>
                                                 <input type="hidden" id="hidden_crmonthly_amount" name="hidden_crmonthly_amount" value="">

                                            </div>

                                        </div>

                                        <br>

                                        <p style="color: #D9D9D9;">------------------ <b style="color: black;">OR</b> ------------------</p>

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 cphdtxt1024" style="padding-top:8px;/*padding-left:29%;*/">

                                                One Time Lumpsum Investment :

                                            </div>

                                            <div class="value colr01 cpvalue1024" style="//padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-success btn-lg investment" id='crlumpsum_amount' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" id="hidden_crlumpsum_amount" name="hidden_crlumpsum_amount" value="">
                                            </div>

                                        </div>

                                        <hr>

                                    </div>

                                </div>

                            </div>
                                </center>    
                            <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                        </div>

                                    

                </div>
                </form>
            </div>



            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

  

    

        <!-- Vacation Calculator -->

  

<div class="modal fade" id="vacation_calc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header ">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span style="color:black" aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel"> Vacation Planning Calculator</h4>

            </div>

            <div class="modal-body">
                 <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorVacation" name="form_Vacation" id="form_Vacation" method="post">
                   <div class="panel panel-primary">

                    <div class="panel-heading"> 

                        <h3 class="panel-title result_h3">Vacation Planning Calculator</h3> 

                    </div>

                    <div class="panel-body">                    



                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="age">Present Cost of Vacation :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="v_amount" id="v_amount" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem">

                                            <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                            <input id="s_v_amount" name="s_v_amount" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input vacation-1024 vacation-768 vacation-414 vacation-736 vacation-375  vacation-667 vacation-360 vacation-640 vacation-412 vacation-732 vacation-684 vacation-320  vacation-568" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="age">Years left for trip :</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input type="text" name="v_period" id="v_period" class="form-control">

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_v_period" name="s_v_period" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge mrgnleftYrPurchase right-badge-vacation-1024 right-badge-vacation-768 right-badge-vacation-414 right-badge-vacation-736  right-badge-vacation-375 right-badge-vacation-667 right-badge-vacation-360 right-badge-vacation-640 right-badge-vacation-412 right-badge-vacation-732 right-badge-vacation-684 right-badge-vacation-320 right-badge-vacation-568" style="padding-right:15px;">Yrs</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="c_inflation">Expected Inflation Rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="v_inflation" name="v_inflation" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_v_inflation" name="s_v_inflation" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge right-badge-vacation-1024 right-badge-vacation-768 right-badge-vacation-414 right-badge-vacation-736 right-badge-vacation-375 right-badge-vacation-667 right-badge-vacation-360 right-badge-vacation-640 right-badge-vacation-412 right-badge-vacation-732 right-badge-vacation-684 right-badge-vacation-320 right-badge-vacation-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>

                                </div>

                            </div>

                        </div>                    

                        <div class="col-sm-12 top">

                            <div class="form-group">

                                <div class="col-sm-3 mblSize mblsize1024 label-name">

                                    <label for="return">Expected Return Rate:</label>

                                </div>

                                <div class="col-sm-6 hide4Mobile">

                                    <input id="v_return" name="v_return" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                </div>

                                <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                    <div class="form-elem" style="padding-left:5px;">

                                            <input id="s_v_return" name="s_v_return" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                            &nbsp;<span class="right-badge right-badge-vacation-1024 right-badge-vacation-768 right-badge-vacation-414 right-badge-vacation-736 right-badge-vacation-375 right-badge-vacation-667 right-badge-vacation-360 right-badge-vacation-640 right-badge-vacation-412 right-badge-vacation-732 right-badge-vacation-684 right-badge-vacation-320 right-badge-vacation-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                    </div>

                                </div>

                            </div>

                        </div>                                          

                        <div class="col-sm-9">

                            <button type="button" class="btn btn-danger calculate_btn-vacation-1024 calculate_btn-vacation-768 calculate_btn-vacation-414 calculate_btn-vacation-736  calculate_btn-vacation-667 calculate_btn-vacation-375 calculate_btn-vacation-360 calculate_btn-vacation-640 calculate_btn-vacation-412 calculate_btn-vacation-732 calculate_btn-vacation-684 calculate_btn-vacation-320 calculate_btn-vacation-568" id="vac_calc" style="margin-left: 47%;margin-top: 5%;width: 28%;">Calculate</button>

                            <button type="button" class="btn btn-danger reset_btn-vacation-1024  reset_btn-vacation-768 reset_btn-vacation-414 reset_btn-vacation-736 reset_btn-vacation-360 reset_btn-vacation-375 reset_btn-vacation-667 reset_btn-vacation-412 reset_btn-vacation-640 reset_btn-vacation-732 reset_btn-vacation-684 reset_btn-vacation-320 reset_btn-vacation-568" onclick="vacationresetSlider()" style="margin-top: 60px;margin-bottom: 5%;">Reset</button>

                        </div>

                    </div>

                </div>
               
                   <div class="col-md-12">

                   

                        <div class="panel panel-primary vac_result"  style="display:none !important;">
                        <center>
                            <div class="panel-heading"> 

                                <h3 class="panel-title result_h3">Results</h3> 

                            </div>

                            <div class="panel-body">

                                <div class="col-md-12" >

                                    <div class="col-md-10 vac_op" style="padding-top:10px;margin-left:65px;">

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 vphdtxt1024" style="//padding-left:28%;">

                                                The Future Value for your Vacation will be

                                            </div>

                                            <div class="value vpvalue1024" style="//padding-left:190px;">

                                                <span></span> 

                                                <button class="btn btn-warning btn-lg FV" id='vcfuture_amount' type="button" style="width:150px !important;"></button> 
                                                   <input type="hidden" name="hidden_vcfuture_amount" id="hidden_vcfuture_amount" value="">                                            

                                            </div>

                                        </div>

                                        <div class="futurevalue top">

                                            <div class="hedgTxt02 vphdtxt1024" style="//padding-left:28%;">

                                                Your Monthly Savings should be :

                                            </div>

                                            <div class="value vpvalue1024" style="//padding-left:190px;">

                                                <span></span> 

                                                <button class="btn btn-info btn-lg FV" id='vcmonthly_amount' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" name="hidden_vcmonthly_amount" id="hidden_vcmonthly_amount" value="">

                                            </div>

                                        </div>

                                        <br>

                                        <p style="color: #D9D9D9;">------------------ <b style="color: black;">OR</b> ------------------</p>

                                        <div class="futurevalue">

                                            <div class="hedgTxt02 vphdtxt1024" style="padding-top:8px;/*padding-left:29%;*/">

                                                One Time Lumpsum Investment :

                                            </div>

                                            <div class="value colr01 vpvalue1024" style="//padding-left:215px;">

                                                <span></span>

                                                <button class="btn btn-success btn-lg investment" id='vclumpsum_amount' type="button" style="width:150px !important;"></button>
                                                <input type="hidden" name="hidden_vclumpsum_amount" id="hidden_vclumpsum_amount" value="">

                                            </div>

                                        </div>

                                        <hr>

                                    </div>

                                </div>

                            </div>
                          </center>        
                             <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                        </div>

                                     

                </div>
                 </form>
            </div>



            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

    

        

    <!-- Home Calculator -->

  

<div class="modal fade" id="home_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" >

    <div class="modal-dialog modal-lg" style="/*width:900px*/">

        <div class="modal-content">

            <div class="modal-header ">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span style="color:black" aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Home Planning Calculator</h4>

            </div>

            <div class="modal-body">
                <form action="<?php echo base_url(); ?>broker/Calculators/pdf_calculatorHome" name="form_Home" id="form_Home" method="post">
                    <div class="panel panel-primary">

                        <div class="panel-heading"> 

                            <h3 class="panel-title result_h3">Home Planning Calculator</h3> 

                        </div>

                        <div class="panel-body">                    



                            <div class="col-sm-12 top">

                                <div class="form-group">

                                    <div class="col-sm-3 mblSize mblsize1024 label-name">

                                        <label for="age">Present Cost of Home :</label>

                                    </div>

                                    <div class="col-sm-6 hide4Mobile">

                                        <input type="text" name="h_amount" id="h_amount" class="form-control">

                                    </div>

                                    <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                        <div class="form-elem">

                                                <span class="left-badge" style="top:-2;"> <i class="fa fa-inr fa-2" style="padding-top:5px;" aria-hidden="true"></i></span> &nbsp;  

                                                <input id="s_h_amount" name="s_h_amount" style="width:70% !important;margin-left:48px;margin-top:-61px" class="input home-1024 home-768 home-414 home-736 home-375  home-667 home-360 home-640 home-412 home-732 home-684 home-320  home-568" value="1" type="text" size="10%" onkeypress="return isNumberKey(event)" /> 

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-sm-12 top">

                                <div class="form-group">

                                    <div class="col-sm-3 mblSize mblsize1024 label-name">

                                        <label for="age">Years left to buy Home :</label>

                                    </div>

                                    <div class="col-sm-6 hide4Mobile">

                                        <input type="text" name="h_period" id="h_period" class="form-control">

                                    </div>

                                    <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                        <div class="form-elem" style="padding-left:5px;">

                                                <input id="s_h_period" name="s_h_period" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge mrgnleftYrPurchase right-badge-home-1024 right-badge-home-768 right-badge-home-414 right-badge-home-736  right-badge-home-375 right-badge-home-667 right-badge-home-360 right-badge-home-640 right-badge-home-412 right-badge-home-732 right-badge-home-684 right-badge-home-320 right-badge-home-568" style="padding-right:15px;">Yrs</span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-sm-12 top">

                                <div class="form-group">

                                    <div class="col-sm-3 mblSize mblsize1024 label-name">

                                        <label for="c_inflation">Expected Inflation Rate:</label>

                                    </div>

                                    <div class="col-sm-6 hide4Mobile">

                                        <input id="h_inflation" name="h_inflation" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                    </div>

                                    <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                        <div class="form-elem" style="padding-left:5px;">

                                                <input id="s_h_inflation" name="s_h_inflation" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge right-badge-home-1024 right-badge-home-768 right-badge-home-414 right-badge-home-736 right-badge-home-375 right-badge-home-667 right-badge-home-360 right-badge-home-640 right-badge-home-412 right-badge-home-732 right-badge-home-684 right-badge-home-320 right-badge-home-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                        </div>

                                    </div>

                                </div>

                            </div>                    

                            <div class="col-sm-12 top">

                                <div class="form-group">

                                    <div class="col-sm-3 mblSize mblsize1024 label-name">

                                        <label for="return">Expected Return Rate:</label>

                                    </div>

                                    <div class="col-sm-6 hide4Mobile">

                                        <input id="h_return" name="h_return" class="form-control" aria-describedby="basic-addon1" value="" type="text" >

                                    </div>

                                    <div class="col-sm-3 mblsm2 mblsize1024 text-box">

                                        <div class="form-elem" style="padding-left:5px;">

                                                <input id="s_h_return" name="s_h_return" class="input" value="1" style="width:70% !important;margin-top:1px" type="text" size="10%" onkeypress="return isNumberKey(event)" />    

                                                &nbsp;<span class="right-badge right-badge-home-1024 right-badge-home-768 right-badge-home-414 right-badge-home-736 right-badge-home-375 right-badge-home-667 right-badge-home-360 right-badge-home-640 right-badge-home-412 right-badge-home-732 right-badge-home-684 right-badge-home-320 right-badge-home-568" style="padding-right:15px;"><i class="fa fa-percent" style="padding-top:5px;" aria-hidden="true"></i></span>

                                        </div>

                                    </div>

                                </div>

                            </div>                                          

                            <div class="col-sm-9">

                                <button type="button" class="btn btn-danger calculate_btn-home-1024 calculate_btn-home-768 calculate_btn-home-414 calculate_btn-home-736  calculate_btn-home-667 calculate_btn-home-375 calculate_btn-home-360 calculate_btn-home-640 calculate_btn-home-412 calculate_btn-home-732 calculate_btn-home-684 calculate_btn-home-320 calculate_btn-home-568" id="home_calc" style="margin-left: 45%;margin-top: 5%;width: 28%;">Calculate</button>

                                <button type="button" class="btn btn-danger reset_btn-home-1024  reset_btn-home-768 reset_btn-home-414 reset_btn-home-736 reset_btn-home-360 reset_btn-home-375 reset_btn-home-667 reset_btn-home-412 reset_btn-home-640 reset_btn-home-732 reset_btn-home-684 reset_btn-home-320 reset_btn-home-568" onclick="homeresetSlider()" style="margin-top: 60px;margin-bottom: 5%;">Reset</button>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-12">

                       

                            <div class="panel panel-primary home_result"  style="display:none !important;">
                            <center>
                                <div class="panel-heading"> 

                                    <h3 class="panel-title result_h3">Results</h3> 

                                </div>

                                <div class="panel-body">

                                    <div class="col-md-12" >

                                        <div class="col-md-10 home_op" style="padding-top:10px;margin-left:65px;">

                                            <div class="futurevalue">

                                                <div class="hedgTxt02 hphdtxt1024" style="//padding-left:28%;">

                                                    The Future Value for your Home will be

                                                </div>

                                                <div class="value hpvalue1024" style="//padding-left:190px;">

                                                    <span></span> 

                                                    <button class="btn btn-warning btn-lg FV" id='hcfuture_amount' type="button" style="width:150px !important;"></button>
                                                    <input type="hidden" name="hidden_hcfuture_amount" id="hidden_hcfuture_amount" value="">
                                                </div>

                                            </div>

                                            <div class="futurevalue top">

                                                <div class="hedgTxt02 hphdtxt1024" style="//padding-left:28%;">

                                                    Your Monthly Savings should be :

                                                </div>

                                                <div class="value hpvalue1024" style="//padding-left:190px;">

                                                    <span></span> 

                                                    <button class="btn btn-info btn-lg FV" id='hcmonthly_amount' type="button" style="width:150px !important;"></button>
                                                    <input type="hidden" name="hidden_hcmonthly_amount" id="hidden_hcmonthly_amount" value="">

                                                </div>

                                            </div>

                                            <br>

                                            <p style="color: #D9D9D9;">------------------ <b style="color: black;">OR</b> ------------------</p>

                                            <div class="futurevalue">

                                                <div class="hedgTxt02 hphdtxt1024" style="padding-top:8px;/*padding-left:29%;*/">

                                                    One Time Lumpsum Investment :

                                                </div>

                                                <div class="value colr01 hpvalue1024" style="//padding-left:215px;">

                                                    <span></span>

                                                    <button class="btn btn-success btn-lg investment" id='hclumpsum_amount' type="button" style="width:150px !important;"></button>
                                                    <input type="hidden" name="hidden_hclumpsum_amount" id="hidden_hclumpsum_amount" value="">

                                                </div>

                                            </div>

                                            <hr>

                                        </div>

                                    </div>

                                </div>
                               </center>
                                <div>  <input type="submit" name="pv_submit" id="pv_submit" value="" class="pdf_img" ></div>
                            </div>

                                            

                    </div>
                </form>
            </div>



            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

    

    

    

    

    

    <!----------------------------------------------------------------------------->

    

    

</div>

    </div>

</div>