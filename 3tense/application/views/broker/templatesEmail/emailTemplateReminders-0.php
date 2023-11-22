
<?php 
	// error_reporting(0);

	if (!isset($result[0]->next_date)) {
		$result[0]->next_date="";
	}


	// $type = 'Broker';
	if($type == 'Broker'){  $appendForBroker = '
												<tr>
														<!-- START OF HEADING-->
																<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
													<td align="left" width="15%" class="center"  valign = "center" style = "border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px; padding-left: 5px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;">
																													
																<span style="letter-spacing:1px";><center>Next Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</center> </span>
															
													</td>
														<!-- END OF HEADING-->
														<!-- START OF RIGHT COLUMN-->
													<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500; font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
													
																<span>'.$result[0]->next_date.'</span>
															<!-- END OF TEXT-->
													</td>
														<!-- END OF RIGHT COLUMN-->
													<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
												</tr>';
						 }
	else
	{
											$appendForBroker = "";
	}											




$emailTemplateReminderOut=
'<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" name="viewport">
	<title>Responsive Email Template</title>
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" rel="stylesheet" type="text/css">
	<style type="text/css">
	html{width:100%}::-moz-selection{background:#0079ff;color:#fff}::selection{background:#0079ff;color:#fff}body{background-color:#f5f7fa;margin:0;padding:0}.ExternalClass,.ReadMsgBody{width:100%;background-color:#f5f7fa}a{color:#0079ff;text-decoration:none;font-weight:300;font-style:normal}a:hover{color:#adb2bb;text-decoration:underline;font-weight:300;font-style:normal}div,p{margin:0!important}table{border-collapse:collapse}@media only screen and (max-width:640px){table table,td[class=full_width]{width:100%!important}div[class=div_scale],table[class=table_scale],td[class=td_scale]{width:440px!important;margin:0 auto!important}img[class=img_scale]{width:100%!important;height:auto!important}table[class=spacer],td[class=spacer]{display:none!important}td[class=center]{text-align:center!important}table[class=full]{width:400px!important;margin-left:20px!important;margin-right:20px!important}img[class=divider]{width:400px!important;height:1px!important}}@media only screen and (max-width:479px){table table,td[class=full_width]{width:100%!important}div[class=div_scale],table[class=table_scale],td[class=td_scale]{width:280px!important;margin:0 auto!important}img[class=img_scale]{width:100%!important;height:auto!important}table[class=spacer],td[class=spacer]{display:none!important}td[class=center]{text-align:center!important}table[class=full]{width:240px!important;margin-left:20px!important;margin-right:20px!important}img[class=divider]{width:240px!important;height:1px!important}}
	</style>
</head>
<body bgcolor="#F5F7FA">
	<!-- START OF PRE-HEADER BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="center" valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<!-- START OF VERTICAL SPACER
							<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td height="30" width="100%">&nbsp;</td>
								</tr>
							</table> END OF VERTICAL SPACER-->
							<table bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td width="100%">
										<table border="0" cellpadding="0" cellspacing="0" width="600">
											<tr>
												<td width="540">
													<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="540">
														<tr>
															<td align="center" style="padding: 0px;" valign="top">
																<table align="center" bgcolor="#4184F3" border="0" cellpadding="0" cellspacing="0" style="margin: 0;">
																	<tr>
																		<td align="center" bgcolor="#F5F7FA" style="padding: 0px; font-size:13px ; font-style:normal; color:#adb2bb; font-family: "Open Sans", Helvetica, Arial, sans-serif; line-height: 23px; mso-line-height-rule: exactly;" valign="middle">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table><!-- END OF VERTICAL SPACER-->
							<table bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td width="100%">
										<table border="0" cellpadding="0" cellspacing="0" width="600">
											<tr>
												<td width="540">
													<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="540">
														<tr>
															<td align="center" style="padding: 0px;" valign="top">
																<table align="center" bgcolor="#4184F3" border="0" cellpadding="0" cellspacing="0" style="margin: 0;">
																	<tr>
																		<td align="center" bgcolor="#F5F7FA" style="padding: 0px; font-size:13px ; font-style:normal; color:#adb2bb; font-family: "Open Sans", Helvetica, Arial, sans-serif; line-height: 23px; mso-line-height-rule: exactly;" valign="middle">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table><!-- START OF VERTICAL SPACER
							<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td height="20" width="100%">&nbsp;</td>
								</tr>
							</table> END OF VERTICAL SPACER-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF PRE-HEADER BLOCK--><!-- START OF HEADER BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td height="30" width="100%">&nbsp;</td>
								</tr>
							</table><!-- END OF VERTICAL SPACER-->
							
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF HEADER BLOCK-->
<!-- START OF 2/3 AND 1/3 COL RIGHT IMAGE BLOCK-->



	<!-- START OF FEATURED AREA BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600"> <!-- bgcolor="#757f83" -->
								<tr width="100%"> <td width="100%">Hi <span style="font-weight: 300;font-family: Times, sans-serif; color:#757f83; font-size:150%; line-height:34px; mso-line-height-rule: exactly; margin-left:2px; text-transform: uppercase;"><i>' . $result[0]->client_name . '</i></span><b>,</b></td></tr>								
								<tr><td >&nbsp;</td></tr>
<!--								<tr> <td width="30px"> </td> <td>Please check the following REMINDER...  </td></tr> -->
								<tr><td >&nbsp;</td></tr>
								<tr><td >&nbsp;</td></tr>
								<tr><td >&nbsp;</td></tr>

							</table>
							<table bgcolor="#4184F3" border="0" style=" border-top-right-radius: 45px ;   border-top-left-radius: 45px ; background: linear-gradient(  #66adff 15% , #0040ff 85% ); text-shadow: 1px 1px 1px #2b54a4, -2px 0 0 #2b54a4, 0 2px 0 #2b54a4, 0 -2px 0 #2b54a4, 1px 1px #2b54a4, -1px -1px 0 #2b54a4, 1px -1px 0 #2b54a4, -1px 1px 0 #2b54a4; " cellpadding="0" cellspacing="0" class="table_scale" width="600"> <!-- bgcolor="#757f83" -->
								<tr>

									<td align="center" width="100%">
										<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
											<!--[if gte mso 9]> <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:290px;"> <v:fill type="tile" src="http://i.imgur.com/XM6EYbh.png" color="#0079ff" /> <v:textbox inset="0,0,0,0"> <![endif]-->
											<tr>
												<td class="spacer" width="20"></td>
												<td width="540">
													<!-- START OF LEFT COLUMN-->
													<table align="center" border="0" cellpadding="0" cellspacing="0" class="full" style=" border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="540">
														<tr>
															<td height="0">&nbsp;</td>
														</tr><!-- START OF HEADING-->
														<!-- <tr>
															<td align="center" class="center" style="padding-bottom: 5px; font-weight: 300; font-family: Open Sans, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px; line-height:34px; mso-line-height-rule: exactly;"><span>Holiday Marketing For A Nonprofit? Don"t Neglect Your Online Presence</span></td>
														</tr> --><!-- END OF HEADING--><!-- START OF TEXT-->
														<tr>
															<td align="center" class="center" style="margin: 0; font-weight: 300; font-size:250% ; color:#adb2bb; font-family:  Times; line-height: 26px;mso-line-height-rule: exactly;"><span style="color:#ffffff;">Reminder !<!-- '.$result[0]->reminder_type.' --> </span></td>
														</tr><!-- END OF TEXT--><!-- START BUTTON-->
														<tr>
															
														</tr><!-- END BUTTON-->
														<tr>
															<td height="0">&nbsp;</td>
														</tr>
													</table><!-- END LEFT COLUMN-->
												</td>
												<td class="spacer" width="20"></td>
											</tr><!--[if gte mso 9]> </v:textbox> </v:rect> <![endif]-->
										</table>
									</td>
								</tr>
							</table><!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<!-- <td height="40" width="100%">&nbsp;</td> -->
								</tr>
							</table><!-- END OF VERTICAL SPACER-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF FEATURED AREA BLOCK--><!-- START OF 1/3 AND 2/3 COL LEFT IMAGE BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td width="100%">
										<table border="0" cellpadding="0" cellspacing="0" width="600">
											<tbody>
											<!-- START OF SPACER block -->
											<tr>
												<td align="left" width="15%" class="center" style="border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px;padding-left: 5px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;"><span>
												</td>
												<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
												</td>
											</tr>
											<!-- END OF SPACER block -->											
											<tr>
														<!-- START OF HEADING-->
															<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
												<td align="left" width="15%" class="center" valign = "center" style="border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px; padding-top: 2px; padding-left: 7px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;"><span>
												
															<span><center>Reminder Title &nbsp;&nbsp;&nbsp;&nbsp; :</center> </span>
														
												</td>
													<!-- END OF HEADING-->
													<!-- START OF RIGHT COLUMN-->
												<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
												
															<span>'.$result[0]->reminder_type.'</span>
														<!-- END OF TEXT-->
												</td>
													<!-- END OF RIGHT COLUMN-->
												<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
											</tr>
											<tr>
													<!-- START OF HEADING-->
															<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
												<td align="left" width="15%" class="center" valign = "center" style="border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px;padding-left: 7px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;"><span>
													
																<span><center>Reminder Date &nbsp;&nbsp;&nbsp; :</center> </span>
															
												</td>
													<!-- END OF HEADING-->
													<!-- START OF RIGHT COLUMN-->
												<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
												
															<span>'.$result[0]->date_of_reminder.'</span>
														<!-- END OF TEXT-->
												</td>
													<!-- END OF RIGHT COLUMN-->
													<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
											</tr>
											<tr>
													<!-- START OF HEADING-->
															<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
												<td align="left" width="15%" class="center"  valign = "center" style = "border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px; padding-left: 5px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;"><span>
												
															<span><center>Reminder Details :</center> </span>
														
												</td>
													<!-- END OF HEADING-->
													<!-- START OF RIGHT COLUMN-->
												<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
												
															<span>'.$result[0]->reminder_message.'</span>
														<!-- END OF TEXT-->
												</td>
													<!-- END OF RIGHT COLUMN-->
												<!-- <td class="spacer" width="30"></td> --> <!-- edited commented -->
											</tr>
												
											'.$appendForBroker.'
											<!-- START OF SPACER block -->
											<tr>
												<td align="left" width="15%" class="center" style="border-left:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding-bottom: 2px; padding-top: 2px;padding-left: 5px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#757f83; font-size:100%; line-height:34px; mso-line-height-rule: exactly;"><span>
												</td>
												<td align="left"  width="45%" class="center" style="border-right:solid 1px; border-top:solid 1px; border-color:#d9d9d9; padding: 10px; font-weight: 500;font-family:  Helvetica, Arial, sans-serif; color:#404040; font-size:14px; line-height:34px; mso-line-height-rule: exactly;">  <span>
												</td>
											</tr>
											<!-- END OF SPACER block -->											
										</tbody>
								
						</table><!-- START OF VERTICAL SPACER-->
							<table class="table_scale" cellspacing="0" cellpadding="0" width="600" style="border-top:solid 1px;" bgcolor="#FFFFFF" align="center">
								<tbody><tr>
									<td width="100%" height="40">&nbsp;</td>
								</tr>
							</tbody></table><!-- END OF VERTICAL SPACER-->
					
													<!-- END OF RIGHT COLUMN-->
									
							<!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
<!-- 									<td height="40" width="100%">&nbsp;</td>
 -->								</tr>
							</table><!-- END OF VERTICAL SPACER-->
			<!-- END OF 1/3 AND 2/3 COL LEFT IMAGE BLOCK--><!-- START OF DIVIDER IMAGE BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td width="100%">
										<table align="center" border="0" cellpadding="0" cellspacing="0" width="540">
											<tr><!-- 
												<td align="center" style="margin: 0; font-size:14px ; color:#adb2bb; font-family:  Helvetica, Arial, sans-serif; line-height: 0;"><span><img alt="divider image" border="0" class="divider" height="1" src="images/divider-image.png" style="display: inline-block;" width="540"></span></td>
											 --></tr>
										</table>
									</td>
								</tr>
							</table><!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
<!-- 									<td height="40" width="100%">&nbsp;</td>
 -->								</tr>
							</table><!-- END OF VERTICAL SPACER-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF DIVIDER IMAGE BLOCK-->
												</td>
												<td class="spacer" width="30"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table><!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
<!-- 									<td height="40" width="100%">&nbsp;</td>
 -->								</tr>
							</table><!-- END OF VERTICAL SPACER-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF 2/3 AND 1/3 COL RIGHT IMAGE BLOCK--><!-- START OF 1/3 COLUMN FEATURED PRODUCTS BLOCK-->
	
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<table bgcolor="#EFEFEF" border="0" cellpadding="0" cellspacing="0" class="table_scale" style="border-top: 1px solid #f7f7f7;" width="600">
								<tr>
									<td width="100%">
										<table border="0" cellpadding="0" cellspacing="0" width="600">
											<tr>
												<td class="spacer" width="30"></td>
												<td width="540">
													<!-- START OF LEFT COLUMN-->
													<table align="left" border="0" cellpadding="0" cellspacing="0" class="full" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="255">
														<!-- START OF TEXT-->
														<tr>
															<td align="left" class="center" style="margin: 0; padding-top: 10px; font-weight:300; font-size:13px ; color:#adb2bb; font-family: Open Sans, Helvetica, Arial, sans-serif; line-height: 23px;mso-line-height-rule: exactly;"><span>Copyright &#169; '.date("Y").'</span></td>
														</tr><!-- END OF TEXT-->
													</table><!-- END OF LEFT COLUMN--><!-- START OF SPACER-->
													<table align="left" border="0" cellpadding="0" cellspacing="0" class="spacer" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="20">
														<tr>
															<td height="10" width="100%"></td>
														</tr>
													</table><!-- END OF SPACER--><!-- START OF RIGHT COLUMN-->
													<table align="right" border="0" cellpadding="0" cellspacing="0" class="full" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="255">
														<!-- START OF TEXT-->
														<tr>
															<!-- <td align="right" class="center" style="margin: 0; padding-top: 10px; padding-bottom: 5px; font-weight:300; font-size:13px ; color:#757f83; font-family: Open Sans, Helvetica, Arial, sans-serif; line-height: 23px;mso-line-height-rule: exactly;"><span><a href="#" style="font-weight: normal; font-style: 300; color:#757f83;">Home</a> &nbsp;&nbsp; <a href="#" style="font-weight: normal; font-style: 300; color:#757f83;">About</a> &nbsp;&nbsp; <a href="#" style="font-weight: normal; font-style: 300; color:#757f83;">Contact</a></span></td> -->
															<span style="margin: 0; float:right; padding-top: 10px; padding-bottom: 5px; font-weight:300; font-size:13px ; color:#757f83; font-family: Open Sans, Helvetica, Arial, sans-serif; line-height: 23px;mso-line-height-rule: exactly;"> - </span>
														</tr><!-- END OF TEXT-->
													</table><!-- END OF RIGHT COLUMN-->
												</td>
												<td class="spacer" width="30"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><!-- END OF SUB-FOOTER BLOCK-->
	<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="center" valign="top" width="100%">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="100%">
							<table bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td width="100%">
										<table border="0" cellpadding="0" cellspacing="0" width="600">
											<tr>
												<td width="540">
													<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="540">
														<tr>
															<td align="center" style="padding: 0px;" valign="top">
																<!-- <table align="center" bgcolor="#0079FF" border="0" cellpadding="0" cellspacing="0" style="margin: 0;">
																	<tr>
																		<td align="center" bgcolor="#F5F7FA" style="padding: 0px; font-size:13px ; font-style:normal; color:#adb2bb; font-family: Open Sans,Helvetica,Arial,sans-serif;line-height:0;" valign="middle"><img alt="img 600 290" border="0" class="img_scale" height="10" src="images/footer-radius.png" style="display:block;" width="600"></td>
																	</tr>
																</table> -->
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table><!-- START OF VERTICAL SPACER-->
							<table align="center" bgcolor="#F5F7FA" border="0" cellpadding="0" cellspacing="0" class="table_scale" width="600">
								<tr>
									<td style="bgcolor:#FFFFFF; !important;" height="40" width="100%">&nbsp;</td>
								</tr>
							</table><!-- END OF VERTICAL SPACER-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>';

/*	echo $emailTemplateReminderOut;*/




?>