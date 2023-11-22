<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="3Tense">
	<meta name="author" content="Aakarsoft">

    <!--<link href="<?php /*echo base_url(); */?>assets/users/less/styles.less" rel="stylesheet/less" media="all">-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/users/css/styles.css?=140">
    <!-- <link rel="stylesheet" href="assets/css/styles.css?=121"> -->
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600' rel='stylesheet' type='text/css'>

	<?php if (isset($_COOKIE["theme"])) {
	    echo "<link href='.base_url().'assets/users/demo/variations/". $_COOKIE["theme"] ." rel='stylesheet' type='text/css' media='all' id='styleswitcher'>";
	} else { ?>
        <link href='<?php echo base_url(); ?>assets/users/demo/variations/default.css' rel='stylesheet' type='text/css' media='all' id='styleswitcher'>
    <?php } ?>

    <?php if (isset($_COOKIE["headerstyle"])) {
        echo "<link href='.base_url().'assets/users/demo/variations/". $_COOKIE["headerstyle"] ."' rel='stylesheet' type='text/css' media='all' id='headerswitcher'>";
    } else { ?>
        <link href='<?php echo base_url(); ?>assets/users/demo/variations/default.css' rel='stylesheet' type='text/css' media='all' id='headerswitcher'>
    <?php } ?>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries. Placeholdr.js enables the placeholder attribute -->
	<!--[if lt IE 9]>
        <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/css/ie8.css">
		<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.1.0/respond.min.js"></script>
	<![endif]-->
    <?php
    if(isset($css))
    {
        foreach($css as $css_item)
        {
            echo "<link rel='stylesheet' href='".base_url().$css_item."'>\n";
        }
    }
    ?>
    <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/codeprettifier/prettify.css"> <!-- Google Code Prettifier -->
    <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/form-toggle/toggles.css"><!-- Toggles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda-themeless.min.css">
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/spin.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/jquery-1.10.2.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/jqueryui-1.10.3.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/common.js'); ?>"></script>
    <link rel='stylesheet' type='text/css' href="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.default.css">
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.min.js"></script>

    <link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/fonts/fontawesome-webfont.ttf"> <!-- Font Awesome -->


    <?php
    if(isset($js))
    {
        foreach($js as $js_item)
        {
            echo "<script type='text/javascript' src='".base_url($js_item)."'></script>\n";
        }
    }
    ?>
</head>
<body class="<?php if (isset($_COOKIE["fixed-header"])) echo ' static-header'; ?>">
    <div id="headerbar">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo site_url('broker/Assets_liabilities/add_form?trans_type=asset')?>" class="shortcut-tiles tiles-midnightblue">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-exchange"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Asset
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo site_url('broker/Commodities/add_form')?>" class="shortcut-tiles tiles-brown">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-diamond"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Commodity
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo base_url('broker/Assets_liabilities/add_form?trans_type=liability'); ?>" class="shortcut-tiles tiles-grape">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-exchange"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Liability
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo site_url('broker/Fixed_deposits/add_form')?>" class="shortcut-tiles tiles-primary">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-money"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Fixed Deposit
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo site_url('broker/Insurances/add_form')?>" class="shortcut-tiles tiles-inverse">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-umbrella"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Insurance
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="<?php echo site_url('broker/Real_estate/add_form')?>" class="shortcut-tiles tiles-orange">
                        <div class="tiles-body">
                            <div class="pull-left"><i class="fa fa-building"></i></div>
                        </div>
                        <div class="tiles-footer">
                            Add Real Estate
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <header class="navbar navbar-inverse <?php if (isset($_COOKIE["fixed-header"])) {echo 'navbar-static-top';} else {echo 'navbar-fixed-top';} ?>" role="banner">
        <a id="leftmenu-trigger" class="tooltips" data-toggle="tooltip" data-placement="right" title="Toggle Sidebar"></a>
        <?php /* We don't need the right sidebar now, so commenting it out - SALMAAN 19/4/16
        <a id="rightmenu-trigger" class="tooltips" data-toggle="tooltip" data-placement="left" title="Toggle Infobar"></a> */ ?>

        <div class="navbar-header pull-left">
            <a class="navbar-brand" href="<?php echo base_url('broker/Dashboard'); ?>">3Tense</a>
        </div>

        <ul class="nav navbar-nav pull-right toolbar">
        	<li class="dropdown">
        		<a href="#" class="dropdown-toggle username" data-toggle="dropdown"><span class="hidden-xs"><?php echo $this->session->userdata('name')?> <i class="fa fa-caret-down"></i></span><!--<img src="<?php echo base_url(); ?>assets/users/demo/avatar/dangerfield.png" alt="Dangerfield" />--></a>
        		<ul class="dropdown-menu userinfo arrow">
        			<li class="username">
                        <a href="#">
        				    <!--<div class="pull-left"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/dangerfield.png" alt="Jeff Dangerfield"/></div>
        				    <div class="pull-right"><h5>Howdy, <?php echo $this->session->userdata('name')?></h5><small>You are Logged in  <span></span></small></div>-->
                            <div><h5>Hi, <?php echo $this->session->userdata('name')?></h5><small>You are Logged in  <span></span></small></div>
                        </a>
        			</li>
        			<li class="userlinks">
        				<ul class="dropdown-menu">
        					<li><a href="<?php echo base_url('broker/Users/profile'); ?>">Edit Profile <i class="pull-right fa fa-pencil"></i></a></li>
        					<?php if($this->session->userdata('user_id') == $this->session->userdata('broker_id')) { ?>
        					    <li><a href="<?php echo base_url('broker/Users/manage_users'); ?>">Manage Users <i class="pull-right fa fa-user"></i></a></li>
                            <?php } ?>
        					<?php //<li><a href="#">Help <i class="pull-right fa fa-question-circle"></i></a></li> ?>
        					<li><a href="#" onclick="eod()">EOD <i class="pull-right fa fa-sign-out "></i></a></li>
        					<li class="divider"></li>
        				 	<li><a href="<?php echo base_url('broker/Users/logout')?>" class="text-right">Sign Out</a></li>
        				</ul>
        			</li>
        		</ul>
        	</li>
        	<li class="dropdown" id="top-notif">
                <?php
                $count_reminder = 0;
                $reminder = null;
                if(isset($this->session->userdata['header']))
                {
                    $count_reminder = $this->session->userdata['header']['count_reminder'];
                    $reminder = $this->session->userdata['header']['reminder'];
                }
                ?>
        		<a href="#" class="hasnotifications dropdown-toggle" data-toggle='dropdown'><i class="fa fa-bell"></i><?php echo !empty($count_reminder)?'<span class="badge">'.$count_reminder.'</span>':''; ?></a>
        		<ul class="dropdown-menu notifications arrow">
        			<li class="dd-header">
                        <div style="float: left;">
                            <button type="button" id="addRemBtn" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
                        </div>
        				<span>You have <b><?php echo $count_reminder;?></b> Personal reminder(s)</span>
        			</li>
                    <div class="scrollthis">
                        <?php foreach($reminder as $rem): ?>
    				    <li>
                            <!--<span class="time">4 mins</span>-->
                            <?php if($rem->reminder_type == 'Birthday Reminder' || $rem->reminder_type == 'Anniversary Reminder'):?>
                                <a href="#" class="notification-success active">
                                    <i class="fa fa-gift"></i>
                                    <span style="font-weight: bold; padding-left: 10px"><?php echo $rem->reminder_type?></span> -
                                    <span style="font-size: smaller; font-style: italic;"><?php echo $rem->client_name?></span>
                                    <br/>
                                    <span class="msg"><?php echo $rem->reminder_message;?></span>
                                </a>
                            <?php elseif($rem->reminder_type == 'Premium Due' || $rem->reminder_type == 'Grace Date' || $rem->reminder_type == 'Insurance Maturity'):?>
                                <a href="#" class="notification-warning">
                                    <i class="fa fa-eye"></i>
                                    <span style="font-weight: bold; padding-left: 10px"><?php echo $rem->reminder_type?></span> -
                                    <span style="font-size: smaller; font-style: italic;"><?php echo $rem->client_name?></span>
                                    <br/>
                                    <span class="msg"><?php echo $rem->reminder_message;?> </span>
                                </a>
                            <?php elseif($rem->reminder_type == 'Personal'):?>
                                <a href="#" class="notification-warning">
                                    <i class="fa fa-eye"></i>
                                    <span style="font-weight: bold; padding-left: 10px"><?php echo $rem->reminder_type?></span> -
                                    <span style="font-size: smaller; font-style: italic;"><?php echo $rem->client_name?></span>
                                    <br/>
                                    <span class="msg"><?php echo $rem->reminder_message;?> </span>
                                </a>
                            <?php else:?>
                                <a href="#" class="notification-danger">
                                    <i class="fa fa-crosshairs"></i>
                                    <span style="font-weight: bold; padding-left: 10px"><?php echo $rem->reminder_type?></span> -
                                    <span style="font-size: smaller; font-style: italic;"><?php echo $rem->client_name?></span>
                                    <br/>
                                    <span class="msg"><?php echo $rem->reminder_message;?> </span>
                                </a>
                            <?php endif;?>
    				    </li>
                        <?php endforeach;?>
                    </div>
        			<li class="dd-footer"><a href="<?php echo base_url('broker/Reminders')?>">View All Reminders Upto Today</a></li>
				</ul>
			</li>
            <li>
                <a href="#" id="headerbardropdown"><span><i class="fa fa-level-down"></i></span></a>
            </li>
		</ul>
    </header>


    <script type="text/javascript">
        // script for sending EOD mails
        function eod() {
            $.ajax({
                url: "<?php echo base_url('broker/Users/eod'); ?>",
                type: 'post',
                data: {send: 1},
                dataType: 'json',
                success:function(data)
                {
                    //console.log(data);
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: data['type'],
                        hide: true
                    });
                },
                error:function(data)
                {
                    console.log(data);
                }
            });
        }
    </script>

    <div id="page-container">
        <!-- BEGIN SIDEBAR -->
        <nav id="page-leftbar" role="navigation">
                <!-- BEGIN SIDEBAR MENU -->
            <ul class="acc-menu" id="sidebar">
                <!--<li id="search">
                    <a href="javascript:;"><i class="fa fa-search opacity-control"></i></a>
                     <form>
                        <input type="text" class="search-query" placeholder="Search...">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
                <li class="divider"></li>-->
                <li><a href="<?php echo base_url('broker/Dashboard'); ?>"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="javascript:;"><i class="fa fa-archive"></i> <span>Masters <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></span></a>
                    <ul class="acc-menu">
                        <li><a href="<?php echo base_url('broker/Families');?>"><span>Family</span></a></li>
                        <li><a href="<?php echo base_url('broker/Add_Sip_Rate');?>"><span>SIP Rate Of Return <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></span></a></li>
                        
                        <li><a href="javascript:;"><span>Insurance</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Premium_types');?>"><span>Asset Allocation</span></a></li>
                                <li><a href="<?php echo base_url('broker/Insurance_companies'); ?>"><span>Insurance Companies</span></a></li>
                                <li><a href="<?php echo base_url('broker/Insurance_plans');?>"><span>Insurance Plans</span></a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Mutual Funds</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Mutual_fund_schemes'); ?>"><span>Mutual Fund Schemes</span></a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Fixed Deposit</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Fd_companies'); ?>"><span>Companies</span></a></li>
                                <li><a href="<?php echo base_url('broker/Fd_investment_types');?>"><span>Investment Types</span></a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Commodity</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Commodity_items'); ?>"><span>Items</span></a></li>
                                <li><a href="<?php echo base_url('broker/Commodity_units');?>"><span>Units</span></a></li>
                                <li><a href="<?php echo base_url('broker/Commodity_rates');?>"><span>Rates</span></a></li>
                            </ul>
                        </li>
						<li><a href="javascript:;"><span>Asset/Liability</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Al_companies');?>"><span>Company</span></a></li>
                                <li><a href="<?php echo base_url('broker/Al_products'); ?>"><span>Product</span></a></li>
                                <li><a href="<?php echo base_url('broker/Al_schemes');?>"><span>Scheme</span></a></li>
                                <li><a href="<?php echo base_url('broker/Al_types');?>"><span>Type</span></a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url('broker/Document_types');?>"><span>Document Types</span></a></li>
                        <li><a href="<?php echo base_url('broker/Banks');?>"><span>Banks</span></a></li>
                        <li><a href="<?php echo base_url('broker/Demats');?>"><span>Demat Providers</span></a></li>
                        <li><a href="<?php echo base_url('broker/Trading_brokers');?>"><span>Trading Brokers</span></a></li>
                        <li><a href="<?php echo base_url('broker/Client_types');?>"><span>Client Types</span></a></li>
                        <li><a href="<?php echo base_url('broker/Occupations');?>"><span>Occupations</span></a></li>
                        <li><a href="<?php echo base_url('broker/Advisers');?>"><span>Advisor</span></a></li>
                        <?php
                        if($this->session->userdata('broker_id') == '0004' || $this->session->userdata('broker_id') == '0009')
                        {
                        ?>
                        <li><a href="<?php echo base_url('broker/Scheme_Trigger');?>"><span>Scheme Trigger</span></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li><a href="<?php echo base_url('broker/Clients');?>"><i class="fa fa-user"></i> <span>Client</span></a></li>
                <li><a href="<?php echo base_url('broker/Insurances'); ?>"><i class="fa fa-umbrella"></i> <span>Insurance</span> </a></li>
                <li><a href="<?php echo base_url('broker/Mutual_funds'); ?>"><i class="fa fa-newspaper-o"></i> <span>Mutual Funds</span> </a></li>
                <li><a href="<?php echo base_url('broker/Equity'); ?>"><i class="fa fa-bar-chart-o"></i> <span>Equity</span> </a></li>
                <li><a href="<?php echo base_url('broker/Fixed_deposits'); ?>"><i class="fa fa-money"></i> <span>Fixed Income</span> </a></li>
                <li><a href="<?php echo base_url('broker/Commodities'); ?>"><i class="fa fa-diamond"></i> <span>Commodities</span> </a></li>
                <!--  For Calculators  -->
                <!--<li><a href="javascript:;"><i class="fa fa-line-chart"></i> <span>Pre Sales</span> </a>-->
                <!--    <ul class="acc-menu">-->
                <!--        <li><a href="<?php echo base_url('broker/Calculators'); ?>"><span>Calculators</span></a></li>-->
                <!--        <li><a href="<?php echo base_url('broker/Assets_liabilities/liabilities_list'); ?>"><span>Financial Planning</span></a></li>-->
                <!--    </ul>-->
                <!--</li>-->
                <li><a href="<?php echo base_url('broker/Calculators'); ?>"><i class="fa fa-line-chart"></i> <span>Calculators</span> </a></li>
                <!--  For Calculators  -->
                <li><a href="javascript:;"><i class="fa fa-exchange"></i> <span>Assets & Liablities</span> </a>
                    <ul class="acc-menu">
                        <li><a href="<?php echo base_url('broker/Assets_liabilities/assets_list'); ?>"><span>Assets</span></a></li>
                        <li><a href="<?php echo base_url('broker/Assets_liabilities/liabilities_list'); ?>"><span>Liabilities</span></a></li>
                    </ul>
                </li>
				<li><a href="<?php echo base_url('broker/Real_estate'); ?>"><i class="fa fa-bank"></i> <span>Real Estate</span> </a></li>
				<li><a href="javascript:;"><i class="fa fa-inr"></i> <span>Finance</span></a>
                    <ul class="acc-menu">
                        <li><a href="<?php echo base_url('broker/Add_funds');?>"><span>Add Funds</span></a></li>
                        <li><a href="<?php echo base_url('broker/Withdraw_funds');?>"><span>Withdraw Funds</span></a></li>
                    </ul>
                </li>
                <li><a href="javascript:;"><i class="fa fa-upload"></i> <span>Imports</span><?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></a>
                    <ul class="acc-menu">
                        <li><a href="<?php echo base_url('broker/Clients/import');?>"><span>Client Details</span></a></li>
                        <li><a href="javascript:;"><span>Insurance</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Insurances/ins_import');?>"><span>Policy Details</span></a></li>
                                <li><a href="<?php echo base_url('broker/Insurances/ins_fund_import');?>"><span>Fund Options</span></a></li>
                                <?php /*<li><a href="<?php echo base_url('broker/Insurances/ins_stake_import');?>"><span>Real Stakes</span></a></li>*/?>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Mutual Fund <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Mutual_funds/mutual_fund_import'); ?>"><span>MF Transaction</span></a></li>
                                <li><a href="<?php echo base_url('broker/SIP/import'); ?>"><span>SIP <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></span></a></li>
                                <?php /*<li><a href="<?php echo base_url('broker/Mutual_funds/nav_import'); ?>"><span>NAV</span></a></li>*/ ?>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Share Equity</span></a>
                            <ul class="acc-menu">
                                <?php /*<li><a href="<?php echo base_url('broker/Equity/import_bhav_copy'); ?>"><span>Bhav Copy</span></a></li>*/ ?>
                                <li><a href="<?php echo base_url('broker/Equity/import_cash_balance'); ?>"><span>Cash Balance</span></a></li>
                                <li><a href="<?php echo base_url('broker/Equity/import_holding'); ?>"><span>Holding</span></a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url('broker/Fixed_deposits/import'); ?>"><span>Fixed Income</span></a></li>
                        <li><a href="<?php echo base_url('broker/Users/last_imports'); ?>"><span>Last Imports</span></a></li>
                    </ul>
                </li>
                <li><a href="javascript:;"><i class="fa fa-file-text"></i> <span>Reports</span> <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></a>
                    <ul class="acc-menu">
                        <li><a href="javascript:;"><span>Fixed Deposit</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Fixed_deposits/fd_report');?>"><span>Fixed Deposit Details</span></a></li>
                                <li><a href="<?php echo base_url('broker/Fixed_deposits/interest_calendar_report');?>"><span>Interest Calender</span></a></li>
                            </ul>
                        </li>
                        <li><a href="javascript:;"><span>Insurance</span></a>
                            <ul class="acc-menu">
                                <li><a href="<?php echo base_url('broker/Insurances/ins_report');?>"><span>Insurance Details</span></a></li>
                                <li><a href="<?php echo base_url('broker/Insurances/premium_calendar_report');?>"><span>Premium Calender</span></a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url('broker/Assets_liabilities/al_report');?>"><span>Asset & Liability Details</span></a></li>
                        <li><a href="<?php echo base_url('broker/Equity/equity_report');?>"><span>Equity/Shares Report</span></a></li>
                        <li><a href="<?php echo base_url('broker/Mutual_funds/mf_report');?>"><span>Mutual Fund Details</span> <?php //<img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/>?></a></li>
						<li><a href="<?php echo base_url('broker/Real_estate/re_report');?>"><span>Real Estate Details</span></a></li>
						<li><a href="<?php echo base_url('broker/Commodities/commodity_report');?>"><span>Commodity Report</span></a></li>
						<li><a href="<?php echo base_url('broker/Final_reports/summary_report');?>"><span>Summary Report</span></a></li>
						<li><a href="<?php echo base_url('broker/Final_reports/cash_flow_report');?>"><span>Cash Flow Report</span></a></li>
						<li><a href="<?php echo base_url('broker/Final_reports/ledger_report');?>"><span>Ledger Report</span></a></li>
                    </ul>
                </li>
                <li><a href="javascript:;"><i class="fa fa-bell"></i> <span>Reminders</span></a>
                    <ul class="acc-menu">
                        <li><a href="<?php echo base_url('broker/Reminders');?>"><span>Your Reminders</span></a></li>
                        <li><a href="<?php echo base_url('broker/Reminder_analyzer');?>"><span>Reminder Analyzer </span></a></li>
                        <li><a href="<?php echo base_url('broker/Reminders/reminder_config');?>"><span>Reminder Config</span></a></li>
                    </ul>
                </li>
            </ul>
            <!-- END SIDEBAR MENU -->
        </nav>
        <!-- BEGIN RIGHTBAR -->
        <div id="page-rightbar">
            <div id="chatarea">
                <div class="chatuser">
                    <span class="pull-right">Jane Smith</span>
                    <a id="hidechatbtn" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                </div>
                <div class="chathistory">
                    <div class="chatmsg">
                        <p>Hey! How's it going?</p>
                        <span class="timestamp">1:20:42 PM</span>
                    </div>
                    <div class="chatmsg sent">
                        <p>Not bad... i guess. What about you? Haven't gotten any updates from you in a long time.</p>
                        <span class="timestamp">1:20:46 PM</span>
                    </div>
                    <div class="chatmsg">
                        <p>Yeah! I've been a bit busy lately. I'll get back to you soon enough.</p>
                        <span class="timestamp">1:20:54 PM</span>
                    </div>
                    <div class="chatmsg sent">
                        <p>Alright, take care then.</p>
                        <span class="timestamp">1:21:01 PM</span>
                    </div>
                </div>
                <div class="chatinput">
                    <textarea name="" rows="2"></textarea>
                </div>
            </div>

            <div id="widgetarea">
                <div class="widget">
                    <div class="widget-heading">
                        <a href="javascript:;" data-toggle="collapse" data-target="#accsummary"><h4>Account Summary</h4></a>
                    </div>
                    <div class="widget-body collapse in" id="accsummary">
                        <div class="widget-block" style="background: #7ccc2e; margin-top:10px;">
                            <div class="pull-left">
                                <small>Current Balance</small>
                                <h5>$71,182</h5>
                            </div>
                            <div class="pull-right"><div id="currentbalance"></div></div>
                        </div>
                        <div class="widget-block" style="background: #595f69;">
                            <div class="pull-left">
                                <small>Account Type</small>
                                <h5>Business Plan A</h5>
                            </div>
                            <div class="pull-right">
                                <small class="text-right">Monthly</small>
                                <h5>$19<small>.99</small></h5>
                            </div>
                        </div>
                        <span class="more"><a href="#">Upgrade Account</a></span>
                    </div>
                </div>


                <div id="chatbar" class="widget">
                    <div class="widget-heading">
                        <a href="javascript:;" data-toggle="collapse" data-target="#chatbody"><h4>Online Contacts <small>(5)</small></h4></a>
                    </div>
                    <div class="widget-body collapse in" id="chatbody">
                        <ul class="chat-users">
                            <li data-stats="online"><a href="javascript:;"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/potter.png" alt=""><span>Jeremy Potter</span></a></li>
                            <li data-stats="online"><a href="javascript:;"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/tennant.png" alt=""><span>David Tennant</span></a></li>
                            <li data-stats="online"><a href="javascript:;"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/johansson.png" alt=""><span>Anna Johansson</span></a></li>
                            <li data-stats="busy"><a href="javascript:;"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/jackson.png" alt=""><span>Eric Jackson</span></a></li>
                            <li data-stats="away"><a href="javascript:;"><img src="<?php echo base_url(); ?>assets/users/demo/avatar/jobs.png" alt=""><span>Howard Jobs</span></a></li>
                            <!--li data-stats="offline"><a href="javascript:;"><img src="assets/users/demo/avatar/watson.png" alt=""><span>Annie Watson</span></a></li>
                            <li data-stats="offline"><a href="javascript:;"><img src="assets/users/demo/avatar/doyle.png" alt=""><span>Alan Doyle</span></a></li>
                            <li data-stats="offline"><a href="javascript:;"><img src="assets/users/demo/avatar/corbett.png" alt=""><span>Simon Corbett</span></a></li>
                            <li data-stats="offline"><a href="javascript:;"><img src="assets/users/demo/avatar/paton.png" alt=""><span>Polly Paton</span></a></li-->
                        </ul>
                        <span class="more"><a href="#">See all</a></span>
                    </div>
                </div>

                <div class="widget">
                    <div class="widget-heading">
                        <a href="javascript:;" data-toggle="collapse" data-target="#taskbody"><h4>Pending Tasks <small>(5)</small></h4></a>
                    </div>
                    <div class="widget-body collapse in" id="taskbody">
                        <div class="contextual-progress" style="margin-top:10px;">
                            <div class="clearfix">
                                <div class="progress-title">Backend Development</div>
                                <div class="progress-percentage"><span class="label label-info">Today</span> 25%</div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-info" style="width: 25%"></div>
                            </div>
                        </div>
                        <div class="contextual-progress">
                            <div class="clearfix">
                                <div class="progress-title">Bug Fix</div>
                                <div class="progress-percentage"><span class="label label-primary">Tomorrow</span> 17%</div>
                            </div>
                            <div class="progress">
                              <div class="progress-bar progress-bar-primary" style="width: 17%"></div>
                            </div>
                        </div>
                        <div class="contextual-progress">
                            <div class="clearfix">
                                <div class="progress-title">Javascript Code</div>
                                <div class="progress-percentage">70%</div>
                            </div>
                            <div class="progress">
                              <div class="progress-bar progress-bar-success" style="width: 70%"></div>
                            </div>
                        </div>
                        <div class="contextual-progress">
                            <div class="clearfix">
                                <div class="progress-title">Preparing Documentation</div>
                                <div class="progress-percentage">6%</div>
                            </div>
                            <div class="progress">
                              <div class="progress-bar progress-bar-danger" style="width: 6%"></div>
                            </div>
                        </div>
                        <div class="contextual-progress">
                            <div class="clearfix">
                                <div class="progress-title">App Development</div>
                                <div class="progress-percentage">20%</div>
                            </div>
                            <div class="progress">
                              <div class="progress-bar progress-bar-orange" style="width: 20%"></div>
                            </div>
                        </div>
                        
                        <span class="more"><a href="ui-progressbars.php">View all Pending</a></span>
                    </div>
                </div>

 

                <div class="widget">
                    <div class="widget-heading">
                        <a href="javascript:;" data-toggle="collapse" data-target="#storagespace"><h4>Storage Space</h4></a>
                    </div>
                    <div class="widget-body collapse in" id="storagespace">
                        <div class="clearfix" style="margin-bottom: 5px;margin-top:10px;">
                            <div class="progress-title pull-left">1.31 GB of 1.50 GB used</div>
                            <div class="progress-percentage pull-right">87.3%</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" style="width: 50%"></div>
                            <div class="progress-bar progress-bar-warning" style="width: 25%"></div>
                            <div class="progress-bar progress-bar-danger" style="width: 12.3%"></div>
                        </div>
                    </div>
                </div>

                <div class="widget">
                    <div class="widget-heading">
                        <a href="javascript:;" data-toggle="collapse" data-target="#serverstatus"><h4>Server Status</h4></a>
                    </div>
                    <div class="widget-body collapse in" id="serverstatus">
                        <div class="clearfix" style="padding: 10px 24px;">
                            <div class="pull-left">
                                <div class="easypiechart" id="serverload" data-percent="67">
                                        <span class="percent"></span>
                                </div>
                                <label for="serverload">Load</label>
                            </div>
                            <div class="pull-right">
                                <div class="easypiechart" id="ramusage" data-percent="20.6">
                                    <span class="percent"></span>
                                </div>
                                <label for="ramusage">RAM: 422MB</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- END RIGHTBAR -->

<?php //include 'application\views\broker\master\add_reminder.php';?>
<?php $this->load->view('broker/master/add_reminder');?>