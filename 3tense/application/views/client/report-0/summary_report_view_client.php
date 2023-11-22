
<?php
//error_reporting(E_All);
if(empty($summary_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    if(!empty($summary_rep_data)) {
        $clientID = $summary_rep_data[0]->client_id;
        $clientName = $summary_rep_data[0]->client_name;
    } else {
        $clientID = "";
        $clientName = "";
    }
    $css = '<style type="text/css">
                table { width:100% }
                table td {font-size: 12px; padding:2px; text-align:center; }
                table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                .border {border: 1px solid #4d4d4d;}
                .amount { text-align:left; text-indent: 15px; }
                .noWrap { white-space: nowrap; }
                .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 12px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .normal {font-weight: normal;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .no-border {border-width: 0px; border-color:#fff;}
                .client-name { text-align: left; font-size: 14px; font-weight: bold; }
            </style>';
    $title = '<div class="title row">Summary Report of '.$clientName.'</div>
                    <br/>';
    if($summary_rep_data)
    {
         $htmlVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $clientTotal = 0; //overall amount

        foreach($summary_rep_data as $row)
        {
            $total = round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity);
            if($header) {
                $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true" class="head-row">
                            <th width="120">Insurance</th>
                            <th width="120">Fixed Income</th>
                            <th width="120">Mutual Funds</th>
                            <th width="120">Equity</th>
                            <th width="120">Real Estate</th>
                            <th width="120">Commodity</th>
                            <th width="140">Total Portfolio</th>
                            <th width="120">Life Cover</th>
                        </tr>
                    </tbody>
                    <tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $htmlVal .= '<tr nobr="true">';
                        $ins = intval($row->insurance_inv);
                        $htmlVal .= !empty($ins)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->insurance_inv,0)).'</td>':'<td class="border normal"></td>';
                       // $htmlVal .= !empty($row->insurance_fund)?'<td>'.$this->common_lib->moneyFormatIndia(round($row->insurance_fund,2)).'</td>':'<td></td>';
                        $fd = intval($row->fixed_deposit);
                        $htmlVal .= !empty($fd)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->fixed_deposit,0)).'</td>':'<td class="border normal"></td>';
                        $mf = intval($row->mutual_fund);
                        $htmlVal .= !empty($mf)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->mutual_fund,0)).'</td>':'<td class="border normal"></td>';
                        $eq = intval($row->equity);
                        $htmlVal .= !empty($eq)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->equity,0)).'</td>':'<td class="border normal"></td>';
                        $prop = intval($row->property);
                        $htmlVal .= !empty($prop)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->property,0)).'</td>':'<td class="border normal"></td>';
                        $com = intval($row->commodity);
                        $htmlVal .= !empty($com)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->commodity,0)).'</td>':'<td class="border normal"></td>';

                        $htmlVal .= '<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia(round($total,0)).'</td>';

                        $lc = intval($row->life_cover);
                        $htmlVal .= !empty($lc)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->life_cover,0)).'</td>':'<td class="border normal"></td>';
                    $htmlVal .= '</tr>';
            }
        }
        $htmlVal .= '</tbody>
                </table>';
    }

    $html .= $css;
    $html .= $htmlVal;
}
?>

<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Final_reports/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Final_reports/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <form action="#" id="report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <div class="panel-gray panel-body collapse in panel-noborder">
                        <div id="css_data"><?php echo $css; ?></div>
                        <div class="rep-logo" style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data"  style="overflow-x: auto;">
                          <?php echo $htmlVal; ?>
                          <div class="col-md-4 col-sm-4 col-xs-12" style="padding-top:20px">
                           <div class="x_panel tile fixed_height_320">
                             <div class="x_title">
                               <h2 id="switchID">Asset Allocation</h2>
                               <div class="clearfix"></div>
                             </div>
                             <div>
                               <div class="x_content" id="ClientWise">
                                 <table   style="width:100%">
                                   <tr>
                                     <td>
                                       <canvas id="canvas1" height="110" width="110" style="margin: 15px 10px 10px 0"></canvas>
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
                             </div>
                           </div>
                         </div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Summary Report" />

                        </div>
                      </div>
              </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
$(document).ready(function()
{

  $(function() {
      var button;
      $('.ladda-button').click(function(e){
          button = this;
      });
  });


  var options = {
    legend: false,
    responsive: false
        };
        new Chart(document.getElementById("canvas1"),
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


});

    </script>
