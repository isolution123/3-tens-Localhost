    <!-- <div class="container body"> -->
        <!-- page content -->
        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">

              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count count">
                  <span class="count_top "><i class="fa fa-user"></i> Total Portfolio</span>
                  <div class="count">
                      <?php echo isset($varTotal_portfolio)?$this->common_lib->moneyFormatIndiaClient(intval($varTotal_portfolio)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                 </div>


                </div>
              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top "><i class="fa fa-clock-o"></i> Liability</span>
                <div class="count">
                  <?php echo isset($varLiability)?$this->common_lib->moneyFormatIndiaClient(intval($varLiability)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                   </div>

                </div>
              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i>   Net Worth</span>
                <div class="NetWorth count">
                  <?php echo isset($varNetWorth)?$this->common_lib->moneyFormatIndiaClient(intval($varNetWorth)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                  </div>

              </div>
            <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Life Cover
                <?php if($this->session->userdata('type')=='head')
                {
                  echo " (HOF)";
                }?>
                 </span>

            <div class="count">
                <?php echo isset($varTotal_life_cover)?$this->common_lib->moneyFormatIndiaClient(intval($varTotal_life_cover)):$this->common_lib->moneyFormatIndiaClient(0); ?>
            </div>

            </div>
          </div>
          <!-- /top tiles -->

      <!-- Pie Charts -->
          <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Insurance</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <br>
                  <table class="" style="width:100%">
                      <tr>
                      <td>
                        <canvas id="canvas1" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                      </td>
                      <td colspan="4">
                        <table class="tile_info">
                          <tr>
                            <td>
                              <p class="labelSize" ><i class="fa fa-square blue  " ></i>General
                                 <span>
                                   <?php
                                     $GeneralPaid = isset($varGeneralPaid)?intval($varGeneralPaid):0;
                                     $TraditionalPaid = isset($varTraditionalPaid)?intval($varTraditionalPaid):0;
                                     $UnitLikedPaid = isset($varUnitLikedPaid)?intval($varUnitLikedPaid):0;
                                      $pertotal=intval($GeneralPaid+$TraditionalPaid+$UnitLikedPaid);
                                      if(!empty($pertotal))
                                      {
                                        $PerGen=($GeneralPaid/$pertotal)*100;
                                        $PerTrad=($TraditionalPaid/$pertotal)*100;
                                        $PerUnit=($UnitLikedPaid/$pertotal)*100;
                                        echo " (".sprintf("%.2f",$PerGen)."%)";
                                      }
                                      else
                                      {
                                          $PerGen=0;
                                          $PerTrad=0;
                                          $PerUnit=0;
                                          echo " (".sprintf("%.2f",$PerGen)."%)";
                                      }

                                        ?>
                                 </span> </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"><i class="fa fa-square purple " ></i>Traditional
                                 <span>
                                    <?php  echo " (".sprintf("%.2f",$PerTrad)."%)";  ?>
                                 </span></p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"><i class="fa fa-square red " ></i>Unit-Linked
                                <span>
                                  <?php
                                    echo " (".sprintf("%.2f",$PerUnit)."%)";
                                       ?>

                                </span></p>
                            </td>
                          </tr>

                        </table>
                      </td>
                    </tr>

                  </table>
                  <br>

                  <table style="width:100%">
                    <tr>
                      <td><b>Total Life Cover</td><td><b>Premium Paid Till Date</td>
                    </tr>
                    <tr>
                      <td> <div>
                          <?php
                                 if(isset($varTotal_life_cover))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($varTotal_life_cover));
                                  if($this->session->userdata('type')=='head')
				   {
				     echo " (HOF)";
 				   }
                                }
                                else
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(0);
                                  }
                               ?>
                            </div></td>
                    <td><div>
                          <?php
                          	if(isset($varInsuranceTotal))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($varInsuranceTotal));
                                }
                                else
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(0);
                                }

                                  /*if(isset($varPrem_paid_till_date))
                                  {
                                    echo $this->common_lib->moneyFormatIndiaClient(intval($varPrem_paid_till_date));
                                  }
                                  else
                                  {
                                    echo $this->common_lib->moneyFormatIndiaClient(0);
                                  }*/
                               ?>
                      </div></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Mutual Funds</h2>

                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <br>
                  <table class="" style="width:100%">
                    <tr>
                      <td >
                        <canvas id="canvas2" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                      </td>
                      <td >
                        <table class="tile_info">
                          <tr>
                            <td>
                              <p class="labelSize"><i class="fa fa-square green"></i>Hybrid
                                 <span>
                                   <?php
                                          $Debt = isset($varDebt)?intval($varDebt):0;
                                          $Equity = isset($varEquity)?intval($varEquity):0;
                                          $Hybrid = isset($varHybrid)?intval($varHybrid):0;
                                          $perMF=($Debt+$Equity+$Hybrid);

                                           if(!empty($perMF))
                                           {
                                             $varPerDebt=(($Debt/$perMF)*100);
                                             $varPerEquity=(($Equity/$perMF)*100);
                                             $varPerHybrid=(($Hybrid/$perMF)*100);

                                          }
                                           else {
                                             $varPerDebt=0;
                                             $varPerEquity=0;
                                             $varPerHybrid=0;
                                             }
                                            echo " (".sprintf("%.2f",$varPerHybrid)."%)";
                                        ?>
                                 </span> </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"><i class="fa fa-square purple"></i>Debt
                                <span><?php    echo " (".sprintf("%.2f",$varPerDebt)."%)"; ?></span></p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"><i class="fa fa-square red"></i>Equity
                                 <span><?php    echo " (".sprintf("%.2f",$varPerEquity)."%)"; ?></span> </p>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table style="width:100%">
                    <tr>
                      <td style="padding-left:10px"><b>Investment</b></td>
                      <td style="padding-left:10px"><b>Current Value</b></td>
                      <td style="padding-left:10px"><b>Profit</b></td>
                   </tr>
                    <tr>
                        <td  style="padding-left:10px">
                                 <?php echo isset($varPurchase_Amount)?$this->common_lib->moneyFormatIndiaClient(intval($varPurchase_Amount)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                          </td>
                        <td  style="padding-left:10px">
                        <?php echo isset($varCurrent_Amount)?$this->common_lib->moneyFormatIndiaClient(intval($varCurrent_Amount)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                      </td>
                        <!-- below rows added for MF Profit - Salmaan - 2016-12-27 -->
                       <td  style="padding-left:10px" class="   noWrap border">
                           <?php
                          $mfCurrAmt = isset($varCurrent_Amount)?intval($varCurrent_Amount):0;
                          $mfPurAmt = isset($varPurchase_Amount)?intval($varPurchase_Amount):0;
                           $mfProfit = $mfCurrAmt - $mfPurAmt;
                           echo $this->common_lib->moneyFormatIndiaClient($mfProfit);
                           ?>
                          </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Equity</h2>

                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="" style="width:100%">
                    <tr>
                      <th>
                      </th>
                      <th>
                          <span>Top 5 Holdings</span>
                      </th>
                    </tr>
                    <tr>
                      <td>
                        <canvas id="canvas3" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                      </td>
                      <td>
                        <?php  $EQTop1=isset($varTopQty1)?intval($varTopQty1): 0; ?>
                        <?php  $EQTop2=isset($varTopQty2)?intval($varTopQty2): 0; ?>
                        <?php  $EQTop3=isset($varTopQty3)?intval($varTopQty3): 0; ?>
                        <?php  $EQTop4=isset($varTopQty4)?intval($varTopQty4): 0; ?>
                        <?php  $EQTop5=isset($varTopQty5)?intval($varTopQty5): 0; ?>

                        <?php $EQTotal = isset($varTotalEquityPortfolio) ? round($varTotalEquityPortfolio) : $EQTop1+$EQTop2+$EQTop3+$EQTop4+$EQTop5;
                          if(!empty($EQTotal)) {
                            $EQPerTop1=(($EQTop1/$EQTotal)*100);
                            $EQPerTop2=(($EQTop2/$EQTotal)*100);
                            $EQPerTop3=(($EQTop3/$EQTotal)*100);
                            $EQPerTop4=(($EQTop4/$EQTotal)*100);
                            $EQPerTop5=(($EQTop5/$EQTotal)*100);
                          }

                        ?>
                        <table class="tile_info">
                          <tr>
                            <td>
                            <p class="labelSize">
                              <!-- <i class="fa fa-square blue " ></i></p> -->
                              <?php echo (isset($varTop1share) && !empty($EQTop1))?'<i class="fa fa-square blue " ></i>'.$varTop1share.'('.sprintf("%.2f",$EQPerTop1).'%)':'';?>
                            </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize ">
                                <?php echo (isset($varTop2share) && !empty($EQTop2))?'<i class="fa fa-square green " ></i>'.$varTop2share.'('.sprintf("%.2f",$EQPerTop2).'%)':'';?>
                              </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"> <?php echo (isset($varTop3share) && !empty($EQTop3))?'<i class="fa fa-square purple " ></i>'.$varTop3share.'('.sprintf("%.2f",$EQPerTop3).'%)':'';?></p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize">  <?php echo (isset($varTop4share) && !empty($EQTop4))?'<i class="fa fa-square aero " ></i>'.$varTop4share.'('.sprintf("%.2f",$EQPerTop4).'%)':'';?> </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"> <?php echo (isset($varTop5share) && !empty($EQTop5))?'<i class="fa fa-square red " ></i>'.$varTop5share.'('.sprintf("%.2f",$EQPerTop5).'%)':'';?>  </p>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>


                  </table>
                  <br>
                  <table style="width:100%">
                    <tr>
                      <td><b>Equity Portfolio</td>
                      </tr>
                      <tr>
                      <td>
                          <?php echo isset($varTotalEquityPortfolio)?$this->common_lib->moneyFormatIndiaClient(round($varTotalEquityPortfolio)):$this->common_lib->moneyFormatIndiaClient(0); ?>
                       </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
        </div>
          <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Portfolio</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="x_content">
                     <div class="panel panel-success">
                      <div class="panel-heading " style="padding:20px 10px;"><b>FD Portfolio <p style="float:right">  <?php echo isset($varFDTotal)?$this->common_lib->moneyFormatIndiaClient($varFDTotal):$this->common_lib->moneyFormatIndiaClient(0); ?> </p></b></div>
                     </div>
                     <div class="panel panel-info">
                       <div class="panel-heading " style="padding:20px 10px;"><b>Real Estate Portfolio <p style="float:right"> <?php echo isset($varRETotal)?$this->common_lib->moneyFormatIndiaClient($varRETotal):$this->common_lib->moneyFormatIndiaClient(0); ?></p></b></div>
                     </div>
                     <div class="panel panel-danger">
                       <div class="panel-heading " style="padding:20px 10px;"><b>Commodity Portfolio <p style="float:right"><?php echo isset($varCommodityTotal)?$this->common_lib->moneyFormatIndiaClient($varCommodityTotal):$this->common_lib->moneyFormatIndiaClient(0); ?></p></b></div>
                     </div>
                 </div>
             </div>
           </div>
         </div>

                <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="x_panel tile fixed_height_320">
                    <div class="x_title">
                      <h2 id="switchID">Asset Allocation</h2>

                  <ul class="nav navbar-right panel_toolbox">
                    <?php if($this->session->userdata('type')=='head')
                    {?>
                      <li><button type="button" class="btn btn-primary" id="btnClick"  title="Switch To Client Allocation"><i class="fa fa-exchange"></i></button></li>
                      <?php } ?>
                  </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div>
                      <div class="x_content" id="ClientWise">

                        <table   style="width:100%">
                          <tr>
                            <td>
                              <canvas id="canvas4" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                            </td>
                            <td>
                              <table class="tile_info">
                                <tr>
                                  <td>
                                  <p class="labelSize"><i class="fa fa-square blue"></i>Insurance <span>
                                    <?php
                                        $TotalEQ=isset($varTotalEquityPortfolio)?intval($varTotalEquityPortfolio):intval(0);
                                        $TotalFD=isset($varFDTotal)?intval($varFDTotal):intval(0);
                                        $TotalComma=isset($varCommodityTotal)?intval($varCommodityTotal):intval(0);
                                        $TotalMF=isset($varMFTotal)?intval($varMFTotal):intval(0);
                                        $TotalIns=isset($varInsuranceTotal)?intval($varInsuranceTotal):intval(0);
                                        $TotalRE=isset($varPropertyCurrent)?intval($varPropertyCurrent):intval(0);
                                        $PerProductTotal=$TotalEQ+$TotalFD+$TotalMF+$TotalRE+$TotalComma+$TotalIns;
                                        if(!empty($PerProductTotal))
                                        {
                                            $perEQ= ($TotalEQ/$PerProductTotal)*100;
                                            $perFD= ($TotalFD/$PerProductTotal)*100;
                                            $perComma= ($TotalComma/$PerProductTotal)*100;
                                            $perMF= ($TotalMF/$PerProductTotal)*100;
                                            $perIns= ($TotalIns/$PerProductTotal)*100;
                                            $perRE= ($TotalRE/$PerProductTotal)*100;
                                            echo '('.sprintf("%.2f",$perIns).'%)';
                                        }
                                        else {
                                          $perEQ=0;
                                          $perFD=0;
                                          $perComma=0;
                                          $perMF=0;
                                          $perRE=0;
                                          $perIns=0;
                                          echo '('.sprintf("%.2f",$perIns).'%)';
                                        }
                                     ?>
                                  </span> </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <p class="labelSize"><i class="fa fa-square green "></i>Mutual Funds <span><?php echo '('.sprintf("%.2f",$perMF).'%)';?></span></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <p class="labelSize"><i class="fa fa-square purple"></i>Fixed Deposit <span><?php echo '('.sprintf("%.2f",$perFD).'%)';?></span></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <p class="labelSize"><i class="fa fa-square aero"></i>Equity <span><?php echo '('.sprintf("%.2f",$perEQ).'%)';?></span> </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <p class="labelSize"><i class="fa fa-square red"></i>Commodity <span><?php echo '('.sprintf("%.2f",$perComma).'%)';?></span></p>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <p class="labelSize"><i class="fa fa-square" style="color:pink"></i>Real Estate <span><?php echo '('.sprintf("%.2f",$perRE).'%)';?></span></p>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                </div>
                <div class="x_content"  id="FamilyWise" style="display:none;">
                  <div style="width:50%; float:left;">
                      <canvas id="canvas5" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                  </div>
                  <div style=" overflow:auto;height:225px;">
                    <table id="TotalPortfolioFamily" class="tile_info">
                    </table>
                  </div>
                    </div>
                  </div>
                  <script>
                $('#btnClick').on('click',function(){

                              if($('#ClientWise').css('display')!='none')
                              {
                                $('#FamilyWise').show().siblings('div').hide();
                              }
                              else if($('#FamilyWise').css('display')!='none')
                              {
                                $('#ClientWise').show().siblings('div').hide();
                              }

                  if (document.getElementById("btnClick").title == "Switch To Client Allocation")
                  {
                    document.getElementById("btnClick").title = "Switch To Asset Allocation";
                    $('#switchID').html('Client Allocation');
                  // document.getElementById("switchID").title = "Client Allocation";
                  } else {
                  document.getElementById("btnClick").title = "Switch To Client Allocation";
                    $('#switchID').html('Asset Allocation');
               }
                                            });
                  </script>


                </div>
              </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel2 tile fixed_height_320 overflow_hidden">




                <div class="container">

                  <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="15000">
                    <!-- Indicators -->
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">

                      <div class="item active" align="center">
                        <!-- <img src="<?php //echo base_url()."banners/banner-1.png";?>" alt="logo" class="logo_head"/> -->
                        <img src="<?php echo base_url()."assets/users/images/banner-new-1.jpg";?>" alt="banner-1" class="logo_head"/>
                        <div class="carousel-caption">

                        </div>
                      </div>

                      <div class="item" align="center">
                        <!-- <img src="<?php //echo base_url()."banners/banner-2.png";?>" alt="logo" class="logo_head"/> -->
                        <img src="<?php echo base_url()."assets/users/images/banner-new-2.jpg";?>" alt="banner-2" class="logo_head"/>
                        <div class="carousel-caption">

                        </div>
                      </div>

                      <div class="item" align="center">
                      <!-- <a href="<?php echo base_url('client/Dashboard/view');?>" target="_blank">  <img src="<?php //echo base_url()."banners/banner-3.png";?>" alt="logo" class="logo_head"/></a> -->
                      <a href="<?php echo base_url('client/Dashboard/view');?>" target="_blank">  <img src="<?php echo base_url()."assets/users/images/banner-new-3.jpg";?>" alt="banner-3" class="logo_head"/></a>
                        <div class="carousel-caption">

                        </div>
                      </div>
                    </div>

                    <!-- Left and right controls -->
                    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                      <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                      <span class="sr-only">Next</span>
                    </a>
                  </div>
                </div>

                  </div>
                </div>
              </div>

          <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 style="font-size:250%  "><i class="fa fa-bullseye"></i> Focus 5</h4>
                </div>
                <div class="panel-body">
                        <div id="accordioninpanel" class="accordion-group">
                        <div class="accordion-item panel panel-info">
                          <div class="panel-heading panel-info">
                             <span>
                               <a class="accordion-title arrow-toggle"  data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinOne">
                                 <div class="row">
                                   <!-- class .acc-center added to below divs for center align - Salmaan - 2016-12-27 -->
                                   <div class="col-md-4 col-sm-12"><b style="font-size:140%">Mutual Funds</b></div>
                                   <div class="col-md-4 col-sm-12 acc-center"><span> <b>Recent Purchase : <?php echo isset($varMFLastPurhase)?$this->common_lib->moneyFormatIndiaClient(intval($varMFLastPurhase)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                   <div class="col-md-4 col-sm-12 acc-center"><span><b>Recent Redemption & Div : <?php echo isset($varMFLastRed)?$this->common_lib->moneyFormatIndiaClient(intval($varMFLastRed)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                 </div>
                               </a>
                                <!-- <span><b><a class="accordion-title arrow-toggle"  data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinOne"> <b style="font-size:140%">Mutual Funds</b></span>
                                <span style="padding-left:15%" class="varMFLastPurhase "> <b>Last 5 Purchase : </b></span>
                                <span  style="padding-left:17%;"class="varMFLastRed "><b>Last 5  Redemption : </b></span> -->
                              </span>
                            </div>
                              <div id="collapseinOne" class="collapse">
                               <div class="panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Purchase</h4></div>
                                    <!--<div class=" panel-body">  Commented by Salmaan and below line added, just removed .panel-body class  -->
                                    <div class="">
                                      <table class="table table-striped" id="mf_pur" style="width:100%" >
                                        <thead>
                                            <tr class="info">
                                               <!-- <th>Action</th> -->
                                                <th>Client Name</th>
                                                <th>Scheme Name</th>
                                                <th>Folio No.</th>
                                                <th>Trans. Date</th>
                                                <th>Amount</th>
                                <!-- <th>Adjustment</th>
                                <th>Adjustment Ref. No.</th> -->
                                             </tr>
                                        </thead>
                                    </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Redemption</h4></div>
                                    <div class=" panel-warning">
                                      <table class="table table-striped" id="mf_red" style="width:100%">
                                        <thead>
                                          <tr class="info">
                                                <th>Client Name</th>
                                                <th>Scheme Name</th>
                                                <th>Folio Number</th>
                                                <th>Transaction Date</th>
                                                <th>Amount</th>
                                          </tr>
                                        </thead>
                                        </table>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                        </div>
                    </div>




                    <div class="accordion-item panel panel-info">
                      <div  class="panel-heading panel-info">
                         <span><a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinTwo">
                           <div class="row">
                             <div class="col-md-4 col-sm-12"><b style="font-size:140%">Insurance Policies</b></div>
                            <div class="col-md-4 col-sm-12 acc-center"><span><b>Premium Dues : <?php echo isset($varUpcomingPremDue)?$this->common_lib->moneyFormatIndiaClient(intval($varUpcomingPremDue)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                          <div class="col-md-4 col-sm-12 acc-center"> <span ><b>Upcoming Maturities : <?php echo isset($varUpcomingMat)?$this->common_lib->moneyFormatIndiaClient(intval($varUpcomingMat)):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                        </a></span>
                      </div>
                        </div>
                            <div id="collapseinTwo" class="collapse">
                                 <div class="panel panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Premium</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped" id="ins_new">
                                        <thead>
                                             <tr class="info">
                                            <th>Client Name</th>
                                            <th>Due Date</th>
                                            <th>Plan Name</th>
                                            <th>Policy Number</th>
                                            <th>Installment Amt</th>

                                             </tr>
                                        </thead>

                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Maturity</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped" id="ins_mat">
                                       <thead >
                                             <tr class="info">
                                            <th>Client Name</th>
                                            <th>Maturity Date</th>
                                            <th>Plan Name</th>
                                            <th>Policy Number</th>
                                            <th>Maturity Amt</th>
                                             </tr>
                                        </thead>
                                      </table>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item panel panel-info">
                          <div  class="panel-heading panel-info">
                             <span>
                                <span><a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinThree">
                                  <div class="row">
                                    <div class="col-md-4 col-sm-12"><b  style="font-size:140%">Fixed Deposit</b></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span ><b>Upcoming Maturity : <?php echo isset($varUpcomingFDMat)?$this->common_lib->moneyFormatIndiaClient(intval($varUpcomingFDMat)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span ><b>Upcoming Interest : <?php echo isset($varUcompingFDInterest)?$this->common_lib->moneyFormatIndiaClient(intval($varUcompingFDInterest)):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                                  </div>
                              </a></span>
                            </div>
                            <div id="collapseinThree" class="collapse">
                                 <div class="panel panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Maturities</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped"  id="fd_new">
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Maturity Date</th>
                                            <th>Company Name</th>
                                            <th>Ref No.</th>
                                            <th>Amount</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Interest</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped" id="fd_mat">
                                        <thead>
                                           <tr class="info">
                                            <th>Client Name</th>
                                            <th>Interest Date</th>
                                            <th>Company Name</th>
                                            <th>Ref No.</th>
                                            <th>Interest Amount</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="accordioninpanel2" class="accordion-group">
                      <div class="accordion-item panel panel-info">
                        <div  class="panel-heading panel-info">
                           <span>
                              <span><a  class="accordion-title" data-toggle="collapse" data-parent="#accordioninpane2" href="#collapseinFour">
                                <div class="row">
                                  <div class="col-md-4 col-sm-12"><b style="font-size:140%">Asset & Liability</b></span></div>
                                  <div class="col-md-4 col-sm-12 acc-center"><span><b>Upcoming Installment Due : <?php echo isset($varUpcomingAssetsAndLiaDue)?$this->common_lib->moneyFormatIndiaClient(intval($varUpcomingAssetsAndLiaDue)):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                                  <div class="col-md-4 col-sm-12 acc-center"><span ><b>Installment About To Close : <?php echo isset($varUpcomingAssetsAndLia)?$this->common_lib->moneyFormatIndiaClient(intval($varUpcomingAssetsAndLia)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                </div>
                                </a>
                            </span>
                          </div>
                            <div id="collapseinFour" class="collapse">
                                 <div class="panel panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Dues</h4></div>
                                    <div class="   ">
                                      <table class="table table-striped" id="al_new">
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Installment Date</th>
                                            <th>Product Name</th>
                                            <th>Ref No</th>
                                            <th>Maturity Amount</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>About To Close</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped" id="al_mat">
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Closure Date</th>
                                            <th>Product Name</th>
                                            <th>Ref No</th>
                                            <th>Amount</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="accordion-item panel panel-info">
                          <div  class="panel-heading panel-info">
                             <span>
                                <span>
                                  <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel2" href="#collapseinFive">
                                  <div class="row">
                                    <div class="col-md-4 col-sm-12"><b style="font-size:140%">Equity</b></span></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span><b>Total Equity Portfolio : <?php echo isset($varTotalEquityPortfolio)?$this->common_lib->moneyFormatIndiaClient(intval($varTotalEquityPortfolio)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span class="set"><b> Debit Balance : <?php echo isset($varDebitBal)?$this->common_lib->moneyFormatIndiaClient(intval($varDebitBal)):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                  </div>
                                  </a>
                              </span>

                            </div>
                </div>
                </div>
                </div>
          </div>
     <!-- Pie Chart End -->
<!-- Page content End -->
</body>
<!-- Top Tiles Script -->
<script type="text/javascript">
function ajax_mf_list_purchase()
{
    oTable = $("#mf_pur").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_list_purchase');?>',
            "type": 'post'
        },
        "columns": [
            /*{ "data": "action" },*/
            { "data": "client_name" },
            { "data": "scheme_name" },
            { "data": "folio_number" },
            { "data": "purchase_date", "type": "date-uk" },
            { "data": "amount" },
            /*{ "data": "adjustment" },
            { "data": "adjustment_ref_number" }*/
        ]

    });
    return oTable;
}
 function ajax_mf_list_redemption()
  {
    oTable = $("#mf_red").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_list_redemption');?>',
            "type": 'post'
        },
        "columns": [
            /*{ "data": "action" },*/
            { "data": "client_name" },
            { "data": "scheme_name" },
            { "data": "folio_number" },
            { "data": "purchase_date", "type": "date-uk" },
            { "data": "amount" },
            /*{ "data": "adjustment" },
            { "data": "adjustment_ref_number" }*/
        ]

    });
    return oTable;
}
function ajax_ins_list_new()
 {
        var oTable = $("#ins_new").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_ins_list_new');?>',
            "type": 'post'

        },
        "columns": [
            { "data": "client_name" },
            { "data": "next_prem_due_date", "type": "date-uk"},
            { "data": "plan_name" },
            { "data": "policy_num" },
            { "data": "prem_amt"},
        ]


    });
    return oTable;
}
function ajax_ins_list_mat()
 {
        var oTable = $("#ins_mat").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_ins_list_mat');?>',
            "type": 'post'
            /*success: function(data){

                console.log(data);
            },
            error: function(data){
                console.log(data);
            }*/
        },
        "columns": [
            { "data": "client_name" },
            { "data": "maturity_date", "type": "date-uk"},
            { "data": "plan_name" },
            { "data":  "policy_num"},
            { "data": "amount" },
        ]
        /*drawCallback: function () {
            var api = this.api();
            $( "#amt-ins" ).val(api.column( 6, {search:'applied'} ).data().sum());
            $( "#prem-amt" ).val(api.column( 8, {search:'applied'} ).data().sum());
            $( "#fund-val" ).val(api.column( 16, {search:'applied'} ).data().sum());
        }*/

    });
    return oTable;
  }

function ajax_fd_list_new() {
    oTable = $("#fd_new").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_list_top_new');?>',
            "type": 'post'
        },
        "columns": [
            { "data": "client_name" },
            { "data": "maturity_date", "type": "date-uk" },
            { "data": "fd_comp_name" },
            { "data": "ref_number" },
            { "data": "maturity_amount" }
            //{ "data": "adjustment" }
        ]

    });
    return oTable;
}

function ajax_fd_list_mat() {
    oTable = $("#fd_mat").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/ajax_list_top_int');?>',
            "type": 'post'
            /*success: function(data){

                console.log(data);
            },
            error: function(data){
                console.log(data);
            }*/
        },
        "columns": [
            { "data": "client_name" },
            { "data": "interest_date", "type": "date-uk" },
            { "data": "fd_comp_name" },
            { "data": "ref_number" },
            { "data": "interest_amount" }
            //{ "data": "adjustment" }
        ]

    });
    return oTable;
}

function ajax_al_list_new() {

    oTable = $("#al_new").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/asset_ajax_list_top');?>',
            "type": 'post'
        },
        "columns": [
            { "data": "client_name" },
            { "data": "maturity_date", "type": "date-uk" },
            { "data": "product_name"},
            { "data": "ref_number" },
            { "data": "maturity_amount" }
        ]

    });
    return oTable;
}

function ajax_al_list_mat() {

    oTable = $("#al_mat").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "bPaginate": false,
        "bFilter": false,
        "bInfo": false,
        "aaSorting": [[1,'asc']],
        "bAutoWidth": false,
        "ajax": {
            //Load data for the table through ajax
            "url": '<?php echo site_url('client/Dashboard/asset_ajax_list_mat');?>',
            "type": 'post'
            /*success: function(data){

                console.log(data);
            },
            error: function(data){
                console.log(data);
            }*/
        },
        "columns": [
            { "data": "client_name" },
            { "data": "end_date", "type": "date-uk" },
            { "data": "product_name"},
            { "data": "ref_number" },
            { "data": "installment_amount" }
        ]

    });
    return oTable;
}

	$(document).ready(function()
		{
//   $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
          mf_pur1 = ajax_mf_list_purchase();
          mf_red1 = ajax_mf_list_redemption();
          ins_new1=ajax_ins_list_new();
          ins_mat1=ajax_ins_list_mat();
           fd_new1=ajax_fd_list_new();
           fd_mat1=ajax_fd_list_mat();
           al_new1=ajax_al_list_new();
           al_mat1=ajax_al_list_mat();
          // ajax_equity_funds_new();
           //ajax_equity_withdraw_funds_new();
           //ajax_equity_withdraw_negative();
      var options = {
        legend: false,
        responsive: false
            };
              //Pie chart 1
            new Chart(document.getElementById("canvas1"), {
              type: 'doughnut',
              tooltipFillColor: "rgba(51, 51, 51, 0.55)",
              data: {
                labels: [
                  "General",
                  "Traditional",
                  "Unit Linked"
                ],
                datasets: [{
                  data: [<?php echo isset($varGeneralPaid)?intval($varGeneralPaid):intval(0); ?>,
                         <?php echo isset($varTraditionalPaid)?intval($varTraditionalPaid):intval(0); ?>,
                         <?php echo isset($varUnitLikedPaid)?intval($varUnitLikedPaid):intval(0); ?>],
                 backgroundColor: [
                    "#3498DB",
                    "#9B59B6",
                    "#E74C3C"
                  ],
                  hoverBackgroundColor: [
                    "#3498DB",
                    "#9B59B6",
                    "#E74C3C"
                  ]
                }]
              },
              options: options
            });

            new Chart(document.getElementById("canvas2"), {
              type: 'doughnut',
              tooltipFillColor: "rgba(51, 51, 51, 0.55)",
              data: {
                labels:[
                  "Debt",
                  "Equity",
                  "Hybrid"
                ],
                datasets: [{
                  data: [ <?php echo isset($varDebt)?intval($varDebt):intval(0); ?>,
                        <?php echo isset($varEquity)?intval($varEquity):intval(0); ?>,
                        <?php echo isset($varHybrid)?intval($varHybrid):intval(0); ?>],
                  backgroundColor: [
                    "#9B59B6",
                    "#E74C3C",
                    "#26B99A"
                  ],
                  hoverBackgroundColor: [
                    "#B370CF",
                    "#E95E4F",
                    "#36CAAB"
                  ]
                }]
              },
              options: options
            });

            new Chart(document.getElementById("canvas3"),  {
              type: 'doughnut',
              tooltipFillColor: "rgba(51, 51, 51, 0.55)",
              data: {
                labels: [
                         "<?php echo isset($varTop1share)?$varTop1share:'';?>",
                         "<?php echo isset($varTop2share)?$varTop2share:'';?>",
                         "<?php echo isset($varTop3share)?$varTop3share:'';?>",
                         "<?php echo isset($varTop4share)?$varTop4share:'';?>",
                         "<?php echo isset($varTop5share)?$varTop5share:'';?>"],
                datasets: [{
                  data: [
                         <?php echo isset($varTopQty1)?intval($varTopQty1):intval(0);?>,
                         <?php echo isset($varTopQty2)?intval($varTopQty2):intval(0);?>,
                         <?php echo isset($varTopQty3)?intval($varTopQty3):intval(0);?>,
                         <?php echo isset($varTopQty4)?intval($varTopQty4):intval(0);?>,
                         <?php echo isset($varTopQty5)?intval($varTopQty5):intval(0);?> ],
                  backgroundColor: [
                    "#3498DB",
                    "#26B99A",
                    "#9B59B6",
                    "#BDC3C7",
                    "#E74C3C"


                  ],
                  hoverBackgroundColor: [
                    "#49A9EA",
                    "#36CAAB",
                    "#B370CF",
                    "#CFD4D8",
                    "#E95E4F"


                  ]
                }]
              },
              options: options
              });


             new Chart(document.getElementById("canvas4"),
                   {
               type: 'doughnut',
               tooltipFillColor: "rgba(51, 51, 51, 0.55,57)",
               data: {
                 labels: [
                   "Equity",
                    "FD Total",
                   "Commodity",
                   "Mutul Funds",
                   "Insurance",
                   "Real Estate",
                 ],
                 datasets: [{
                   data: [
                        <?php echo isset($varTotalEquityPortfolio)?intval($varTotalEquityPortfolio):intval(0);?>,
                        <?php echo isset($fdtot)?intval($fdtot):intval(0);?>,
                        <?php echo isset($varCommodityTotal)?intval($varCommodityTotal):intval(0);?>,
                        <?php echo isset($varMFTotal)?intval($varMFTotal):intval(0);?>,
                        <?php echo isset($varInsuranceTotal)?intval($varInsuranceTotal):intval(0);?>,
                        <?php echo isset($varPropertyCurrent)?intval($varPropertyCurrent):intval(0);?>],
                   backgroundColor: [
                     "#BDC3C7",
                     "#9B59B6",
                     "#E74C3C",
                     "#26B99A",
                     "#3498DB",
                     "pink"

                   ],
                   hoverBackgroundColor: [
                     "#CFD4D8",
                     "#B370CF",
                     "#E95E4F",
                     "#36CAAB",
                     "#49A9EA",
                     "pink"
                   ]
                 }]
               },
               options: options
               });

            $.ajax({

                  url:'<?php echo site_url('client/Dashboard/getTotalPortFolio')?>',
                  type:"GET",
                  dataType:'JSON',
                  success:function(result)
                  {
                   //console.log(result);
                    var label = []; var dataset = [];var PerClientWise=[];var PerClientTotal=0
                    var i,j,trHTML='';
                    var colorCodeArray =[
                                "#9B59B6",
                                "#E74C3C",
                                "#26B99A",
                                "#3498DB",
                                "#CFD4D8",
                                "#FFC0CB",
                                "#DEBC97",
                                "#6D75A6",
                                "#FBF2BA",
                                "#80A41C",
                                "#C0C0C0",
                                "#808080",
                                "#000000",
                                "#FF0000",
                                "#800000",
                                "#FFFF00",
                                "#808000",
                                "#00FF00",
                                "#008000",
                                "#00FFFF",
                                "#008080",
                                "#0000FF",
                                "#000080",
                                "#FF00FF",
                                "#800080",
                                "#F0F8FF",
                                "#FAEBD7",
                                "#00FFFF",
                                "#7FFFD4",
                                "#F5F5DC",
                                "#000000",
                                "#0000FF",
                                "#8A2BE2",
                                "#A52A2A",
                                "#DEB887",
                                "#5F9EA0",
                                "#7FFF00",
                                "#D2691E",
                                "#FF7F50",
                                "#6495ED",
                                "#FFF8DC",
                                "#DC143C",
                                "#00FFFF",
                                "#00008B",
                                "#008B8B",
                                "#B8860B",
                                "#A9A9A9",
                                "#006400",
                                "#BDB76B",
                                "#556B2F",
                                "#FF8C00",
                                "#8B0000",
                                "#E9967A",
                                "#8FBC8F",
                                "#483D8B",
                                "#2F4F4F",
                                "#00CED1",
                                "#FF1493",
                                "#696969",
                                "#1E90FF",
                                "#FF69B4",
                                "#D3D3D3",
                                "#000080",
                                "#808000",
                                "#FF4500",
                                "#DA70D6",
                                "#DB7093",
                                "#DDA0DD",
                                "#CD853F",
                                "#FF0000",
                                "#BC8F8F"
                              ] ;
                    for(i=0;i<result.length;i++ )
                        {
                          label.push(result[i].client_name);
                          dataset.push(parseInt(result[i].TotalPortfolio));
                          PerClientTotal += result[i].TotalPortfolio<< 0;
                          console.log(result[i].TotalPortfolio);
                      }
                      for(j=0;j<result.length;j++)
                      {
                        PerClientWise.push( (result[j].TotalPortfolio/PerClientTotal)*100);
                      }
                      for(k=0;k<result.length;k++)
                      {
                       trHTML += '<tr><td><p class="labelSize" ><i class="fa fa-square" style="color:'+colorCodeArray[k]+'"></i>   '+  result[k].client_name   +" ("+ PerClientWise[k].toFixed(2) + "%)" + '</p>  </td></tr>';
                      }

                      //console.log(PerClientWise[j],PerClientTotal);
                      //console.log(PerClientTotal);
                      //  console.log(PerClientTotal);
                      //  console.log(dataset);
                            new Chart(document.getElementById("canvas5"),{
                              type: 'doughnut',
                              tooltipFillColor: "rgba(51, 51, 51, 0.55,57)",
                              data: {

                                labels: label,
                                datasets: [{

                                  data: dataset,
                                backgroundColor:colorCodeArray,
                                hoverBackgroundColor: colorCodeArray
                                }]
                              },
                              options: options
                              });

                         //for loop end
                     $('#TotalPortfolioFamily').append(trHTML);

                  }, //success end
                }); //Ajax end


	});



</script>
<!-- Pie charts Script -->
      <script>
          $(document).ready(function()
          {
            var data1 = [
              [gd(2012, 1, 1), 17],
              [gd(2012, 1, 2), 74],
              [gd(2012, 1, 3), 6],
              [gd(2012, 1, 4), 39],
              [gd(2012, 1, 5), 20],
              [gd(2012, 1, 6), 85],
              [gd(2012, 1, 7), 7]
            ];

            var data2 = [
              [gd(2012, 1, 1), 82],
              [gd(2012, 1, 2), 23],
              [gd(2012, 1, 3), 66],
              [gd(2012, 1, 4), 9],
              [gd(2012, 1, 5), 119],
              [gd(2012, 1, 6), 6],
              [gd(2012, 1, 7), 9]
            ];
            $("#canvas_dahs").length && $.plot($("#canvas_dahs"), [
              data1, data2
            ], {
              series: {
                lines: {
                  show: false,
                  fill: true
                },
                splines: {
                  show: true,
                  tension: 0.4,
                  lineWidth: 1,
                  fill: 0.4
                },
                points: {
                  radius: 0,
                  show: true
                },
                shadowSize: 2
              },
              grid: {
                verticalLines: true,
                hoverable: true,
                clickable: true,
                tickColor: "#d5d5d5",
                borderWidth: 1,
                color: '#fff'
              },
              colors: ["rgba(38, 185, 154, 0.38)", "rgba(3, 88, 106, 0.38)"],
              xaxis: {
                tickColor: "rgba(51, 51, 51, 0.06)",
                mode: "time",
                tickSize: [1, "day"],
                //tickLength: 10,
                axisLabel: "Date",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 10
              },
              yaxis: {
                ticks: 8,
                tickColor: "rgba(51, 51, 51, 0.06)",
              },
              tooltip: false
            });

            function gd(year, month, day)
             {
              return new Date(year, month - 1, day).getTime();
            }
          });
      </script>
        <!-- /Flot -->

        <!-- JQVMap -->
        <script>
          $(document).ready(function()
          {
            $('#world-map-gdp').vectorMap({
                map: 'world_en',
                backgroundColor: null,
                color: '#ffffff',
                hoverOpacity: 0.7,
                selectedColor: '#666666',
                enableZoom: true,
                showTooltip: true,
                values: sample_data,
                scaleColors: ['#E6F2F0', '#149B7E'],
                normalizeFunction: 'polynomial'
            });
          });
        </script>
        <!-- /JQVMap -->

        <!-- Skycons -->
        <script>
          $(document).ready(function() {
            var icons = new Skycons({
                "color": "#73879C"
              }),
              list = [
                "clear-day", "clear-night", "partly-cloudy-day",
                "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
                "fog"
              ],
              i;

            for (i = list.length; i--;)
              icons.set(list[i], list[i]);

            icons.play();
          });
        </script>
        <!-- /Skycons -->



        <!-- bootstrap-daterangepicker -->
        <script>
          $(document).ready(function() {

            var cb = function(start, end, label) {
            //  console.log(start.toISOString(), end.toISOString(), label);
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            };

            var optionSet1 = {
              startDate: moment().subtract(29, 'days'),
              endDate: moment(),
              minDate: '01/01/2012',
              maxDate: '12/31/2015',
              dateLimit: {
                days: 60
              },
              showDropdowns: true,
              showWeekNumbers: true,
              timePicker: false,
              timePickerIncrement: 1,
              timePicker12Hour: true,
              ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              },
              opens: 'left',
              buttonClasses: ['btn btn-default'],
              applyClass: 'btn-small btn-primary',
              cancelClass: 'btn-small',
              format: 'MM/DD/YYYY',
              separator: ' to ',
              locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
              }
            };
            $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
            $('#reportrange').daterangepicker(optionSet1, cb);
            $('#reportrange').on('show.daterangepicker', function() {
              console.log("show event fired");
            });
            $('#reportrange').on('hide.daterangepicker', function() {
              console.log("hide event fired");
            });
            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
              console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
            });
            $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
              console.log("cancel event fired");
            });
            $('#options1').click(function() {
              $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
            });
            $('#options2').click(function() {
              $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
            });
            $('#destroy').click(function() {
              $('#reportrange').data('daterangepicker').remove();
            });
          });

          jQuery.extend( jQuery.fn.dataTableExt.oSort, {
              "date-uk-pre": function ( a ) {
                  var ukDatea = a.split('/');
                  return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
              },

              "date-uk-asc": function ( a, b ) {
                  return ((a < b) ? -1 : ((a > b) ? 1 : 0));
              },

              "date-uk-desc": function ( a, b ) {
                  return ((a < b) ? 1 : ((a > b) ? -1 : 0));
              }
          });

  </script>
</body></html>
