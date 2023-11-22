<?php include "header-focused.php"; ?>
      <div class="container body">
          <!-- page content -->
          <div class="right_col" role="main">
            <!-- top tiles -->
          <div class="verticalcenter" style="top: 35%;" align="center">
              <h1> Upload Documents</h1>
          	<div class="panel panel-primary">
          		<div class="panel-body">
                      <?php echo validation_errors();
                      $attributes = array("class" => "form login", "style" => "margin-bottom: 0px !important;");
                      echo form_open_multipart('client/DocsList/upload');?>
                <div class="form__field">
                  <input type="file" name="userfile" />
                </div>
                <div class="form__field">
                  <?php echo form_dropdown('DocsDrop',$DocsDrop,'class="populate" id="DocsDrp"'); ?>
                </div>

		<div style="text-align: left;">
                  <br/>
                  <h3>Demo files for importing data </h3>
                  <h4>
                  <ul>
                    <li><a href="<?php echo base_url();?>uploads/imports/Insurance-import.xlsx" class="alert-link">Insurance Policy Details</a></li>
                    <li><a href="<?php echo base_url();?>uploads/imports/Fund-value-import.xlsx" class="alert-link">Insurance Fund Options</a></li>
                    <li><a href="<?php echo base_url();?>uploads/imports/Mutual-fund-import.xlsx" class="alert-link">Mutual Fund Transactions </a></li>
                    <li><a href="<?php echo base_url();?>uploads/imports/Ledger-Balance-import.xlsx" class="alert-link">Shares Cash Balance</a></li>
                    <li><a href="<?php echo base_url();?>uploads/imports/Holding-import.xlsx" class="alert-link">Shares Holding</a> </li>
                  </ul>
                </h4>
				<br/>
                </div>

                <div class="clearfix" sty le="margin-bottom: 40px;"> </div>
                          <div class="panel-footer">
                            <?php if(isset($limit))
                            {
                              ?>
                              <div class="alert alert-danger">
                                You cannot add any more Document. Please contact support if you want to increase your limit.
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                              </div>
                              <?php
                            }
                            else {
                              ?>
                              <input type="submit" name="submit" value="Upload Files" class="btn btn-primary">
                              <?php
                            }
                             ?>
                          </div>
                      <?php
                      echo form_close();
                      ?>
          		</div>

                <h4><?php if(!empty($error)){ echo $error; } ?></h4>
                <h4><?php if(!empty($msg)){ echo $msg; } ?></h4>

          	</div>
           </div>
          </div>

        </div>
          <?php /* <svg xmlns="http://www.w3.org/2000/svg" class="icons"><symbol id="arrow-right" viewBox="0 0 1792 1792"><path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z"/></symbol><symbol id="lock" viewBox="0 0 1792 1792"><path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z"/></symbol><symbol id="user" viewBox="0 0 1792 1792"><path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z"/></symbol></svg> */ ?>
