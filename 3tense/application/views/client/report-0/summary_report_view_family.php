<?php
if(empty($summary_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    if(!empty($summary_rep_data)) {
        $familyID = $summary_rep_data[0]->family_id;
        $familyName = $summary_rep_data[0]->family_name;
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
                .bigger {font-weight: bold; font-size:14px;}
            </style>';
    $title = '<div class="title row">Summary Report of '.$familyName.'</div>
                    <br/>';
    if($summary_rep_data)
    {
        $htmlVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $clientTotal = 0; //overall amount
        //family totals
        $famInsTotal = 0;
        $famFDTotal = 0;
        $famMFTotal = 0;
        $famEquityTotal = 0;
        $famRETotal = 0;
        $famCommodityTotal = 0;
        $familyTotal = 0;


        foreach($summary_rep_data as $row)
        {
            $clientTotal = round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity);
            if($header) {
                $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true" class="head-row">
                            <th width="90">Client Name</th>
                            <th width="110">Insurance</th>
                            <th width="110">Fixed Income</th>
                            <th width="110">Mutual Funds</th>
                            <th width="110">Equity</th>
                            <th width="110">Real Estate</th>
                            <th width="110">Commodity</th>
                            <th width="120">Total Portfolio</th>
                            <th width="110">Life Cover</th>
                        </tr>
                    </tbody>
                    <tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $htmlVal .= '<tr nobr="true">';
                        $htmlVal .= '<td class="border normal">'.$row->client_name.'</td>';

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

                        $htmlVal .= '<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($clientTotal).'</td>';

                $lc = intval($row->life_cover);
                $htmlVal .= !empty($lc)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->life_cover,0)).'</td>':'<td class="border normal"></td>';
                    $htmlVal .= '</tr>';

                $famInsTotal = round($famInsTotal + $row->insurance_inv);
                $famFDTotal = round($famFDTotal + $row->fixed_deposit);
                $famMFTotal = round($famMFTotal + $row->mutual_fund);
                $famEquityTotal = round($famEquityTotal + $row->equity);
                $famRETotal = round($famRETotal + $row->property);
                $famCommodityTotal = round($famCommodityTotal + $row->commodity);
                $familyTotal = round($familyTotal + $clientTotal);
            }
        }
        //footer with totals
        $htmlVal .= '<tr nobr="true">';
            $htmlVal .= '<td class="dataTotal border">Total</td>';
            $htmlVal .= !empty($famInsTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famInsTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famFDTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famFDTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famMFTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famMFTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famEquityTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famEquityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famRETotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famRETotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famCommodityTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famCommodityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= '<td class="dataTotal border bigger">'.$this->common_lib->moneyFormatIndia($familyTotal).'</td>';
            $htmlVal .= '<td class="border"></td>
                </tr>
            </tbody>
        </table>';
    }

    $html .= $css;
    $html .= $htmlVal;
}
?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<div class="container" id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
  <div>
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
                        <div class="row" id="report_data"  style="overflow-x : auto;"><?php echo $htmlVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Summary Report" />
                    </div>
                </div>
            </form>
          </div>
      <!-- </div> -->


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
      <div class="x_content"  id="FamilyWise" style="display:none;">
        <div style="width:50%; float:left;">
            <canvas id="canvas2" height="150" width="110" style="margin: 15px 10px 10px 0"></canvas>
        </div>
        <div style=" overflow:auto;height:225px;">
          <table id="TotalPortfolioFamily" class="tile_info">
            <?php
                      if(isset($client_list))
                      {
                      $colorCodeArray =array(
                                "#9B59B6","#E74C3C","#26B99A","#3498DB","#CFD4D8","#B370CF","#E95E4F","#36CAAB","#49A9EA","#FFFFFF",
                                "#C0C0C0","#808080","#000000","#FF0000","#800000","#FFFF00","#808000","#00FF00","#008000","#00FFFF",
                                "#008080","#0000FF","#000080","#FF00FF","#800080","#F0F8FF","#FAEBD7","#00FFFF","#7FFFD4","#F5F5DC",
                                "#000000","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E","#FF7F50","#6495ED",
                                "#FFF8DC","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#006400","#BDB76B","#556B2F",
                                "#FF8C00","#8B0000","#E9967A","#8FBC8F","#483D8B","#2F4F4F","#00CED1","#FF1493","#696969","#1E90FF",
                                "#FF69B4","#D3D3D3","#000080","#808000","#FF4500","#DA70D6","#DB7093","#DDA0DD","#CD853F","#FF0000",
                                "#BC8F8F");
                                $i=0;
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
                                  echo "<tr><td><p class='labelSize'><i class='fa fa-square' style='color:$colorCodeArray[$i]'></i>".$rs['client_name']," (".sprintf("%.2f",$percentClient[$i])."%)</p></td></tr>";
                                  $i++;

                             }
                             $labels = array();
                             $datasets=array();
                            foreach ($client_list as $rs)
                            {
                                array_push($labels,$rs['client_name']);
                                array_push($datasets,intval($rs['TotalPortfolio']));
                            }
                    }
                   ?>
          </table>
        </div>
          </div>
        </div>
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

                          var colorCodeArray =<?php echo json_encode($colorCodeArray);?> ;
                          var labels =<?php echo json_encode($labels);?>;
                          var datasets =<?php echo json_encode($datasets); ?>;
                          //var datasets=[241756709,2607936,8459023,0] ;
                          //console.log(labels);
                          //console.log(datasets);
                                 new Chart(document.getElementById("canvas2"),{
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

        	});
    </script>
