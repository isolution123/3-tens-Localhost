    <!-- <div class="container body"> -->
        <!-- page content -->
        <?php //var_dump($dash_data); ?>
        <script>
       
        $(document).ready(function(){
        // Open modal on page load
       // $("#nfo_model").modal('show');
 
        // Close modal on button click
        $("#btnclose").click(function(){
            $("#nfo_model").modal('hide');
        });
    });
        </script>
        <?php
        
        if($this->session->userdata('user_id')==0004 || $this->session->userdata('user_id')=='0004')
        {
         
         if (empty($nfo_detail)) {   
         echo "<script type='text/javascript'> alert('Please share your latest eCAS of CDSL or NSDL Statements to track your other broker investments on our website')</script>"; 
         }
         else
         {
             
         echo "<script type='text/javascript'> $(document).ready(function(){ $('#nfo_model').modal('show'); });</script>";   
        
    ?>   
    
<div id="nfo_model" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      
      <div class="modal-body">
          <button type="button" id="btnclose" style="    color: black;
    background: white;
    position: fixed;
    right: 10px;
    border: 2px solid black;
    opacity: 1;
    padding: 0px 5px 5px 6px;" class="close" data-dismiss="modal">&times;</button>
        <img src="<?php echo base_url(). $nfo_detail[0]['nfo_image_path'] ?>" alt="Girl in a jacket" width="100%" height="400">
        <br><br>
        <div style="color:<?php echo base_url(). $nfo_detail[0]['desc_color'] ?>"><?php echo $nfo_detail[0]['nfo_description'] ?></div>
      </div>
      
    </div>

  </div>
</div>
        
     <?php  } }
        ?>
        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">

              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count count">
                  <span class="count_top "><i class="fa fa-user"></i> Total Portfolio</span>
                  <div class="count">
                      <?php echo isset($dash_data['varTotal_portfolio'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varTotal_portfolio'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                 </div>


                </div>
              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top "><i class="fa fa-clock-o"></i> Liability</span>
                <div class="count">
                  <?php echo isset($dash_data['varLiability'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varLiability'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                   </div>

                </div>
              <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i>   Net Worth</span>
                <div class="NetWorth count">
                  <?php echo isset($dash_data['varNetWorth'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varNetWorth'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
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
                <?php echo isset($dash_data['varTotal_life_cover'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varTotal_life_cover'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
            </div>

            </div>
          </div>
          <!-- /top tiles -->

      <!-- Pie Charts -->
          <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-12">
                <?php 
            
            if($this->session->userdata('user_id')!=0004 && $this->session->userdata('user_id')!='0009' && $this->session->userdata('user_id')!='0174' && $this->session->userdata('user_id')!='0196' )
            {
            ?>
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
                                     $GeneralPaid = isset($dash_data['varGeneralPaid'])?intval($dash_data['varGeneralPaid']):0;
                                     $TraditionalPaid = isset($dash_data['varTraditionalPaid'])?intval($dash_data['varTraditionalPaid']):0;
                                     $UnitLikedPaid = isset($dash_data['varUnitLikedPaid'])?intval($dash_data['varUnitLikedPaid']):0;
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
                                 if(isset($dash_data['varTotal_life_cover']))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($dash_data['varTotal_life_cover']));
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
                          	if(isset($dash_data['varInsuranceTotal']))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($dash_data['varInsuranceTotal']));
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
               <?php }else{?>      
               <div class="x_panel tile fixed_height_320 overflow_hidden">
                         <div id="canvas1" style="height:250px;width:330px" ></div>
                         
                           <table style="width:100%">
                    <tr>
                      <td><b>Total Life Cover</td><td><b>Premium Paid Till Date</td>
                    </tr>
                    <tr>
                      <td> <div>
                          <?php
                                 if(isset($dash_data['varTotal_life_cover']))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($dash_data['varTotal_life_cover']));
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
                          	if(isset($dash_data['varInsuranceTotal']))
                                {
                                  echo $this->common_lib->moneyFormatIndiaClient(intval($dash_data['varInsuranceTotal']));
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
                         <?php } ?>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                  <?php 
            if($this->session->userdata('user_id')!=0004 && $this->session->userdata('user_id')!='0009' && $this->session->userdata('user_id')!='0174' && $this->session->userdata('user_id')!='0196' )
            {
            ?>
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
                              <p class="labelSize"><i class="fa fa-square green"></i> Hybrid
                                 <span>
                                   <?php
                                          $Debt = isset($dash_data['varDebt'])?intval($dash_data['varDebt']):0;
                                          $Equity = isset($dash_data['varEquity'])?intval($dash_data['varEquity']):0;
                                          $Hybrid = isset($dash_data['varHybrid'])?intval($dash_data['varHybrid']):0;
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
                                 <?php echo isset($dash_data['varPurchase_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varPurchase_Amount'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                          </td>
                        <td  style="padding-left:10px">
                        <?php echo isset($dash_data['varCurrent_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varCurrent_Amount'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                      </td>
                        <!-- below rows added for MF Profit - Salmaan - 2016-12-27 -->
                       <td  style="padding-left:10px" class="   noWrap border">
                           <?php
                          $mfCurrAmt = isset($dash_data['varCurrent_Amount'])?intval($dash_data['varCurrent_Amount']):0;
                          $mfPurAmt = isset($dash_data['varPurchase_Amount'])?intval($dash_data['varPurchase_Amount']):0;
                           $mfProfit = $mfCurrAmt - $mfPurAmt;
                           echo $this->common_lib->moneyFormatIndiaClient($mfProfit);
                           ?>
                          </td>
                    </tr>
                  </table>
                </div>
              </div>
               <?php }else{?>      
               <div class="x_panel tile fixed_height_320 overflow_hidden">
                
               <div id="canvas2" style="height:245px;width:330px" ></div>
               <br>
                  <table style="width:100%">
                    <tr>
                      <td ><b>Investment:</b></td>
                         <td  style="padding-left:5px">
                                 <?php echo isset($dash_data['varPurchase_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varPurchase_Amount'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                          </td>
                      <td style="padding-left:5px"><b>Cur. Value: </b></td>
                      <td  style="padding-left:5px">
                        <?php echo isset($dash_data['varCurrent_Amount'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varCurrent_Amount'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
                      </td>
                      
                      
                      
                   </tr>
                    <tr>
                     
                      <td ><b>Profit: </b></td>  
                        <!-- below rows added for MF Profit - Salmaan - 2016-12-27 -->
                       <td  style="padding-left:5px" class="   noWrap border">
                           <?php
                          $mfCurrAmt = isset($dash_data['varCurrent_Amount'])?intval($dash_data['varCurrent_Amount']):0;
                          $mfPurAmt = isset($dash_data['varPurchase_Amount'])?intval($dash_data['varPurchase_Amount']):0;
                           $mfProfit = $mfCurrAmt - $mfPurAmt;
                           echo $this->common_lib->moneyFormatIndiaClient($mfProfit);
                           ?>
                          </td>
                          <td style="padding-left:5px"><b>Div.Payout: </b></td>
                            <td style="padding-left:5px" >
                        
                          
                        <?php echo isset($dash_data['div_payout_total_amount'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['div_payout_total_amount'])):$this->common_lib->moneyFormatIndiaClient(0); ?> 
                        
                        
                          </td>
                    </tr>
                  </table>
                </div>
              
                <?php } ?>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2 style="font-weight: bold;">Equity</h2>

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
                        <?php  $EQTop1=isset($dash_data['varTopQty1'])?intval($dash_data['varTopQty1']): 0; ?>
                        <?php  $EQTop2=isset($dash_data['varTopQty2'])?intval($dash_data['varTopQty2']): 0; ?>
                        <?php  $EQTop3=isset($dash_data['varTopQty3'])?intval($dash_data['varTopQty3']): 0; ?>
                        <?php  $EQTop4=isset($dash_data['varTopQty4'])?intval($dash_data['varTopQty4']): 0; ?>
                        <?php  $EQTop5=isset($dash_data['varTopQty5'])?intval($dash_data['varTopQty5']): 0; ?>

                        <?php $EQTotal = isset($dash_data['varTotalEquityPortfolio']) ? round($dash_data['varTotalEquityPortfolio']) : $EQTop1+$EQTop2+$EQTop3+$EQTop4+$EQTop5;
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
                              <?php echo (isset($dash_data['varTop1share']) && !empty($EQTop1) && !empty($EQPerTop1))?'<i class="fa fa-square blue " ></i>'.$dash_data['varTop1share'].'('.sprintf("%.2f",$EQPerTop1).'%)':'';?>
                            </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize ">
                                <?php echo (isset($dash_data['varTop2share']) && !empty($EQTop2) && !empty($EQPerTop2))?'<i class="fa fa-square green " ></i>'.$dash_data['varTop2share'].'('.sprintf("%.2f",$EQPerTop2).'%)':'';?>
                              </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"> <?php echo (isset($dash_data['varTop3share']) && !empty($EQTop3) && !empty($EQPerTop3))?'<i class="fa fa-square purple " ></i>'.$dash_data['varTop3share'].'('.sprintf("%.2f",$EQPerTop3).'%)':'';?></p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize">  <?php echo (isset($dash_data['varTop4share']) && !empty($EQTop4) && !empty($EQPerTop4))?'<i class="fa fa-square aero " ></i>'.$dash_data['varTop4share'].'('.sprintf("%.2f",$EQPerTop4).'%)':'';?> </p>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <p class="labelSize"> <?php echo (isset($dash_data['varTop5share']) && !empty($EQTop5) && !empty($EQPerTop5))?'<i class="fa fa-square red " ></i>'.$dash_data['varTop5share'].'('.sprintf("%.2f",$EQPerTop5).'%)':'';?>  </p>
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
                          <?php echo isset($dash_data['varTotalEquityPortfolio'])?$this->common_lib->moneyFormatIndiaClient(round($dash_data['varTotalEquityPortfolio'])):$this->common_lib->moneyFormatIndiaClient(0); ?>
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
                  <h2 style="font-weight: bold;">Portfolio</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="x_content">
                     <div class="panel panel-success">
                      <div class="panel-heading " style="padding:20px 10px;"><b>FD Portfolio <p style="float:right">  <?php echo isset($dash_data['varFDTotal'])?$this->common_lib->moneyFormatIndiaClient($dash_data['varFDTotal']):$this->common_lib->moneyFormatIndiaClient(0); ?> </p></b></div>
                     </div>
                     <div class="panel panel-info">
                       <div class="panel-heading " style="padding:20px 10px;"><b>Real Estate Portfolio <p style="float:right"> <?php echo isset($dash_data['varRETotal'])?$this->common_lib->moneyFormatIndiaClient($dash_data['varRETotal']):$this->common_lib->moneyFormatIndiaClient(0); ?></p></b></div>
                     </div>
                     <div class="panel panel-danger">
                       <div class="panel-heading " style="padding:20px 10px;"><b>Commodity Portfolio <p style="float:right"><?php echo isset($dash_data['varCommodityTotal'])?$this->common_lib->moneyFormatIndiaClient($dash_data['varCommodityTotal']):$this->common_lib->moneyFormatIndiaClient(0); ?></p></b></div>
                     </div>
                 </div>
             </div>
           </div>
         </div>

                <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="x_panel tile fixed_height_320">
                    <div class="x_title">
                      <h2 id="switchID" style="font-weight: bold;">Asset Allocation</h2>

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
                                        $TotalEQ=isset($dash_data['varTotalEquityPortfolio'])?intval($dash_data['varTotalEquityPortfolio']):intval(0);
                                        $TotalFD=isset($dash_data['varFDTotal'])?intval($dash_data['varFDTotal']):intval(0);
                                        $TotalComma=isset($dash_data['varCommodityTotal'])?intval($dash_data['varCommodityTotal']):intval(0);
                                        $TotalMF=isset($dash_data['varMFTotal'])?intval($dash_data['varMFTotal']):intval(0);
                                        $TotalIns=isset($dash_data['varInsuranceTotal'])?intval($dash_data['varInsuranceTotal']):intval(0);
                                        $TotalRE=isset($dash_data['varPropertyCurrent'])?intval($dash_data['varPropertyCurrent']):intval(0);
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
                  <?php
                  if($this->session->userdata('type')=='head')
                  {
                    ?>
                  <div style="width:50%; float:left;">
                      <canvas id="canvas5" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
                  </div>
                  <div style=" overflow:auto;height:225px;">
                    <table id="TotalPortfolioFamily" class="tile_info">
                      <?php
                                if(isset($client_list))
                                {
                                      $colorCodeArray =array(
                                                "#9B59B6",
                                                "#E74C3C",
                                                "#26B99A",
                                                "#3498DB",
                                                "#CFD4D8",
                                                "#B370CF",
                                                "#E95E4F",
                                                "#36CAAB",
                                                "#49A9EA",
                                                "#FFFFFF",
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
                                                "#BC8F8F");
                                          $i=0;
                                          $j=0;
                                          $total_cl_portfolio=0;
                                          $percentClient=array();
                                          $len=sizeof($client_list);
                                      foreach ($client_list as $rs)
                                      {
                                        $total_cl_portfolio=$total_cl_portfolio+intval($rs['TotalPortfolio']);
                                      }

                                      foreach ($client_list as $rs)
                                      {
                                          array_push($percentClient,((intval($rs['TotalPortfolio'])/$total_cl_portfolio)*100));

                                      }
                                     foreach ($client_list as $rs)
                                      {
                                              echo "<tr><td><p class='labelSize'><i class='fa fa-square' style='color:$colorCodeArray[$j]'></i>".$rs['client_name']," (".sprintf("%.2f",$percentClient[$i])."%)</p></td></tr>";
                                              $i++;
                                              $j++;
                                       }



                                       $labels = array();
                                       $datasets=array();
                                      foreach ($client_list as $rs)
                                      {
                                          array_push($labels,$rs['client_name']);
                                      }
                                      foreach ($client_list as $rs)
                                      {
                                          array_push($datasets,intval($rs['TotalPortfolio']));
                                      }

                              }
                             ?>
                    </table>
                  </div>
                  <? }
                  else {
                    $labels = array();
                    $datasets=array();
                  }
                  ?>
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
                                   <div class="col-md-4 col-sm-12 acc-center"><span> <b>Recent Purchase : <?php echo isset($dash_data['varMFLastPurhase'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varMFLastPurhase'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                   <div class="col-md-4 col-sm-12 acc-center"><span><b>Recent Redemption & Div : <?php echo isset($dash_data['varMFLastRed'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varMFLastRed'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                 </div>
                               </a>
                                
                              </span>
                            </div>
                              <div id="collapseinOne" class="collapse">
                               <div class="panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Purchase</h4></div>
                                    <!--<div class=" panel-body">  Commented by Salmaan and below line added, just removed .panel-body class  -->
                                    <div class="">
                                      <table class="table table-striped data_table_sort" style="width:100%" >
                                        <thead>
                                            <tr class="info">
                                               <!-- <th>Action</th> -->
                                                <th>Client Name</th>
                                                <th>Scheme Name</th>
                                                <th>Folio No.</th>
                                                <th>Trans. Date</th>
                                                <th>Amount</th>
                                             </tr>
                                        </thead>
                                        <?php
                                          foreach ($mf_list_pur['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['scheme_name']."</td><td>".$rs['folio_number']."</td><td>".$rs['purchase_date']."</td><td>".$rs['amount']."</td></tr>" ;
                                          }
                                         ?>
                                    </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Redemption</h4></div>
                                    <div class=" panel-warning">
                                      <table class="table table-striped data_table_sort" style="width:100%">
                                        <thead>
                                          <tr class="info">
                                                <th>Client Name</th>
                                                <th>Scheme Name</th>
                                                <th>Folio Number</th>
                                                <th>Transaction Date</th>
                                                <th>Amount</th>
                                          </tr>
                                        </thead>
                                      <?php
                                        foreach ($mf_list_redm['data'] as $rs)
                                        {
                                          echo "<tr><td>".$rs['client_name']."</td><td>".$rs['scheme_name']."</td><td>".$rs['folio_number']."</td><td>".$rs['purchase_date']."</td><td>".$rs['amount']."</td></tr>" ;
                                        }
                                       ?>
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
                            <div class="col-md-4 col-sm-12 acc-center"><span><b>Premium Dues : <?php echo isset($dash_data['varUpcomingPremDue'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUpcomingPremDue'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                          <div class="col-md-4 col-sm-12 acc-center"> <span ><b>Upcoming Maturities : <?php echo isset($dash_data['varUpcomingMat'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUpcomingMat'])):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                        </a></span>
                      </div>
                        </div>
                            <div id="collapseinTwo" class="collapse">
                                 <div class="panel panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Premium</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped data_table_sort" style="width:100%">
                                        <thead>
                                           <tr class="info">
                                            <th>Client Name</th>
                                            <th>Due Date</th>
                                            <th>Plan Name</th>
                                            <th>Policy Number</th>
                                            <th>Installment Amt</th>
                                           </tr>
                                        </thead>

                                        <?php
                                          foreach ($ins_new_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['next_prem_due_date']."</td><td>".$rs['plan_name']."</td><td>".$rs['policy_num']."</td><td>".$rs['prem_amt']."</td></tr>" ;
                                          }
                                         ?>

                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Maturity</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped data_table_sort" style="width:100%">
                                       <thead>
                                             <tr class="info">
                                            <th>Client Name</th>
                                            <th>Maturity Date</th>
                                            <th>Plan Name</th>
                                            <th>Policy Number</th>
                                            <th>Maturity Amt</th>
                                             </tr>
                                        </thead>
                                        <?php
                                          foreach ($ins_mat_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['plan_name']."</td><td>".$rs['policy_num']."</td><td>".$rs['amount']."</td></tr>" ;
                                          }
                                         ?>
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
                                    <div class="col-md-4 col-sm-12 acc-center"><span ><b>Upcoming Maturity : <?php echo isset($dash_data['varUpcomingFDMat'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUpcomingFDMat'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span ><b>Upcoming Interest : <?php echo isset($dash_data['varUcompingFDInterest'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUcompingFDInterest'])):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                                  </div>
                              </a></span>
                            </div>
                            <div id="collapseinThree" class="collapse">
                                 <div class="panel panel panel-body">
                                    <div class="accordion-body">
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Maturities</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped data_table_sort"  style="width:100%" >
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Maturity Date</th>
                                            <th>Company Name</th>
                                            <th>Ref No.</th>
                                            <th>Amount</th>
                                          </tr>
                                        </thead>
                                        <?php
                                          foreach ($fd_new_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['fd_comp_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['maturity_amount']."</td></tr>";
                                          }
                                         ?>
                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>Interest</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped data_table_sort" style="width:100%" >
                                        <thead>
                                           <tr class="info">
                                            <th>Client Name</th>
                                            <th>Interest Date</th>
                                            <th>Company Name</th>
                                            <th>Ref No.</th>
                                            <th>Interest Amount</th>
                                          </tr>
                                        </thead>
                                        <?php
                                          foreach ($fd_mat_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['interest_date']."</td><td>".$rs['fd_comp_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['interest_amount']."</td><td></tr>" ;
                                          }
                                         ?>
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
                                  <div class="col-md-4 col-sm-12 acc-center"><span><b>Upcoming Installment Due : <?php echo isset($dash_data['varUpcomingAssetsAndLiaDue'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUpcomingAssetsAndLiaDue'])):$this->common_lib->moneyFormatIndiaClient(0); ?></b></span></div>
                                  <div class="col-md-4 col-sm-12 acc-center"><span ><b>Installment About To Close : <?php echo isset($dash_data['varUpcomingAssetsAndLia'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varUpcomingAssetsAndLia'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
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
                                      <table class="table table-striped data_table_sort" style="width:100%" >
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Installment Date</th>
                                            <th>Product Name</th>
                                            <th>Ref No</th>
                                            <th>Maturity Amount</th>
                                          </tr>
                                        </thead>
                                        <?php
                                          foreach ($al_new_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['product_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['maturity_amount']."</td><td></tr>" ;
                                          }
                                         ?>
                                      </table>
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div><h4>About To Close</h4></div>
                                    <div class=" ">
                                      <table class="table table-striped data_table_sort" style="width:100%" >
                                        <thead>
                                          <tr class="info">
                                            <th>Client Name</th>
                                            <th>Closure Date</th>
                                            <th>Product Name</th>
                                            <th>Ref No</th>
                                            <th>Amount</th>
                                          </tr>
                                        </thead>
                                        <?php
                                          foreach ($al_mat_list['data'] as $rs)
                                          {
                                            echo "<tr><td>".$rs['client_name']."</td><td>".$rs['end_date']."</td><td>".$rs['product_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['installment_amount']."</td><td></tr>" ;
                                          }
                                         ?>
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
                                    <div class="col-md-4 col-sm-12 acc-center"><span><b>Total Equity Portfolio : <?php echo isset($dash_data['varTotalEquityPortfolio'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varTotalEquityPortfolio'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
                                    <div class="col-md-4 col-sm-12 acc-center"><span class="set"><b> Debit Balance : <?php echo isset($dash_data['varDebitBal'])?$this->common_lib->moneyFormatIndiaClient(intval($dash_data['varDebitBal'])):$this->common_lib->moneyFormatIndiaClient(0); ?> </b></span></div>
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
<script>
$('.data_table_sort').DataTable({
  "processing":true,
  "serverSide":false,
  "bPaginate": false,
  "bFilter": false,
  "bInfo": false,
  "aaSorting": [1,'asc'],
  "bAutoWidth": false
});

</script>
<script src="../assets/vendors/CanvasJS/canvas.js"></script>

<script type="text/javascript">

$(document).ready(function(){
 $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();


      var options = {
        legend: false,
        responsive: false
            };
            <?php 
            if($this->session->userdata('user_id')!='0004' && $this->session->userdata('user_id')!='0009' && $this->session->userdata('user_id')!='0174' && $this->session->userdata('user_id')!='0196' )
            {
            ?>
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
                  data: [<?php echo isset($dash_data['varGeneralPaid'])?intval($dash_data['varGeneralPaid']):intval(0); ?>,
                         <?php echo isset($dash_data['varTraditionalPaid'])?intval($dash_data['varTraditionalPaid']):intval(0); ?>,
                         <?php echo isset($dash_data['varUnitLikedPaid'])?intval($dash_data['varUnitLikedPaid']):intval(0); ?>],
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
                  data: [ <?php echo isset($dash_data['varDebt'])?intval($dash_data['varDebt']):intval(0); ?>,
                        <?php echo isset($dash_data['varEquity'])?intval($dash_data['varEquity']):intval(0); ?>,
                        <?php echo isset($dash_data['varHybrid'])?intval($dash_data['varHybrid']):intval(0); ?>],
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
            
            <?php }
            else
            {
            ?>
            var val1=<?php echo isset($dash_data['varGeneralPaid'])?intval($dash_data['varGeneralPaid']):intval(0); ?>;
            var val2=<?php echo isset($dash_data['varTraditionalPaid'])?intval($dash_data['varTraditionalPaid']):intval(0); ?>;
            var val3=<?php echo isset($dash_data['varUnitLikedPaid'])?intval($dash_data['varUnitLikedPaid']):intval(0); ?>;
            var total_val=val1+val2+val3;
            var per1=((val1*100)/total_val).toFixed(2);
            var per2=((val2*100)/total_val).toFixed(2);
            var per3=((val3*100)/total_val).toFixed(2);
            var chart = new CanvasJS.Chart("canvas1", {
                	animationEnabled: true,
                	backgroundColor: "#ffffff",
                	title: {
                	    fontFamily: "Arial",
                        fontWeight: "bolder",
                        fontSize: 20,
                        fontColor:"#2b54a4",
                		text: "Insurance"
                	},
                		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
                	data: [{
                		type: "pie",
                		startAngle: 100,
                		
                		 indexLabelFormatter: function(e){		
                		    var x=e.dataPoint.y.toString();
                            var lastThree = x.substring(x.length-3);
                            var otherNumbers = x.substring(0,x.length-3);
                            if(otherNumbers != '')
                                lastThree = ',' + lastThree;
                            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
                		},
                		dataPoints: [
                			{y: val1, label: "General",per:per1,color: "#3498DB"},
                			{y: val2, label: "Traditional",per:per2,color: "#9B59B6"},
                			{y: val3, label: "Unit Linked",per:per3,color: "#E74C3C"}
                	
                		]
                		 
                	}]
                });

                chart.render();

                $('.canvasjs-chart-credit').css('display','none');
                setTimeout(function(){ $('.canvasjs-chart-credit').css('display','none'); }, 500);
            
                var val11=<?php echo isset($dash_data['varDebt'])?intval($dash_data['varDebt']):intval(0); ?>;
                var val21=<?php echo isset($dash_data['varEquity'])?intval($dash_data['varEquity']):intval(0); ?>;
                var val31=<?php echo isset($dash_data['varHybrid'])?intval($dash_data['varHybrid']):intval(0); ?>;
                
                var total_val1=val11+val21+val31;
                var per11=((val11*100)/total_val1).toFixed(2);
                var per21=((val21*100)/total_val1).toFixed(2);
                var per31=((val31*100)/total_val1).toFixed(2);
            
            var chart1 = new CanvasJS.Chart("canvas2", {
                	animationEnabled: true,
                	backgroundColor: "#ffffff",
                	title: {
                	    fontFamily: "Arial",
                        fontWeight: "bolder",
                        fontSize: 20,
                        fontColor:"#2b54a4",
                		text: "Mutual Funds"
                	},
                		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
                	data: [{
                		type: "pie",
                		startAngle: 80,
                		
                		 indexLabelFormatter: function(e){		
                		    var x=e.dataPoint.y.toString();
                            var lastThree = x.substring(x.length-3);
                            var otherNumbers = x.substring(0,x.length-3);
                            if(otherNumbers != '')
                                lastThree = ',' + lastThree;
                            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
                		},
                		dataPoints: [
                			{y: val31, label: "Hybrid",per:per11,color: "#9B59B6"},
                			{y: val21, label: "Equity",per:per21,color: "#E74C3C"},
                			{y: val11, label: "Debt",per:per31,color: "#26B99A"}
                	
                		]
                		 
                	}]
                });

                chart1.render();

                $('.canvasjs-chart-credit').css('display','none');
                setTimeout(function(){ $('.canvasjs-chart-credit').css('display','none'); }, 500);
                    
            <?php }?>
            



            new Chart(document.getElementById("canvas3"),  {
              type: 'doughnut',
              tooltipFillColor: "rgba(51, 51, 51, 0.55)",
              data: {
                labels: [
                         "<?php echo isset($dash_data['varTop1share'])?$dash_data['varTop1share']:'';?>",
                         "<?php echo isset($dash_data['varTop2share'])?$dash_data['varTop2share']:'';?>",
                         "<?php echo isset($dash_data['varTop3share'])?$dash_data['varTop3share']:'';?>",
                         "<?php echo isset($dash_data['varTop4share'])?$dash_data['varTop4share']:'';?>",
                         "<?php echo isset($dash_data['varTop5share'])?$dash_data['varTop5share']:'';?>"],
                datasets: [{
                  data: [
                         <?php echo isset($dash_data['varTopQty1'])?intval($dash_data['varTopQty1']):intval(0);?>,
                         <?php echo isset($dash_data['varTopQty2'])?intval($dash_data['varTopQty2']):intval(0);?>,
                         <?php echo isset($dash_data['varTopQty3'])?intval($dash_data['varTopQty3']):intval(0);?>,
                         <?php echo isset($dash_data['varTopQty4'])?intval($dash_data['varTopQty4']):intval(0);?>,
                         <?php echo isset($dash_data['varTopQty5'])?intval($dash_data['varTopQty5']):intval(0);?> ],
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
                        <?php echo isset($dash_data['varTotalEquityPortfolio'])?intval($dash_data['varTotalEquityPortfolio']):intval(0);?>,
                        <?php echo isset($dash_data['varFDTotal'])?intval($dash_data['varFDTotal']):intval(0);?>,
                        <?php echo isset($dash_data['varCommodityTotal'])?intval($dash_data['varCommodityTotal']):intval(0);?>,
                        <?php echo isset($dash_data['varMFTotal'])?intval($dash_data['varMFTotal']):intval(0);?>,
                        <?php echo isset($dash_data['varInsuranceTotal'])?intval($dash_data['varInsuranceTotal']):intval(0);?>,
                        <?php echo isset($dash_data['varPropertyCurrent'])?intval($dash_data['varPropertyCurrent']):intval(0);?>],
                   backgroundColor: [
                     "#9CC2CB",
                     "#9B59B6",
                     "#E74C3C",
                     "#1ABB9C",
                     "#3498DB",
                     "pink"

                   ],
                   hoverBackgroundColor: [
                     "#9CC2CB",
                     "#9B59B6",
                     "#E74C3C",
                     "#1ABB9C",
                     "#3498DB",
                     "pink"
                   ]
                 }]
               },
               options: options
               });
               
               
               <?php
               if($this->session->userdata('type')=='head')
               {
                 ?>
               //  var labelss = [];
                 //  var datasetss = [];
                 var colorCodeArray =[
                           "#9B59B6",
                           "#E74C3C",
                           "#26B99A",
                           "#3498DB",
                           "#CFD4D8",
                           "#B370CF",
                           "#E95E4F",
                           "#36CAAB",
                           "#49A9EA",
                           "#FFFFFF",
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
                           "#BC8F8F"];
                  var labels =<?php echo json_encode($labels);?>;
                  var datasets =<?php echo json_encode($datasets); ?>;
                  //var datasets=[241756709,2607936,8459023,0] ;
                  //console.log(labels);
                  //console.log(datasets);
                         new Chart(document.getElementById("canvas5"),{
                            type: 'doughnut',
                            tooltipFillColor: "rgba(51, 51, 51, 0.55,57)",
                            data:{
                              labels:labels,
                              datasets:[{
                                data:datasets,
                              backgroundColor:colorCodeArray,
                              hoverBackgroundColor:colorCodeArray
                              }]
                            },
                            options: options
                  });
                  <?php } ?>

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
            /*$('#world-map-gdp').vectorMap({
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
            });*/
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
