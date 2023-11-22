<?php
error_reporting(E_ALL);
if(empty($mf_rep_data))
{
echo "<script type='text/javascript'>
alert('Unauthorized Access. Get Outta Here!');
window.top.close();  //close the current tab
</script>";
} else {
$html = ''; $html = ''; //set html to blank
//temp variable to data
$familyName = ''; $clientName = ''; $purAmt = 0; $divAmt = 0; $total = 0; $cagr1 = 0; $cagr2 = 0; $abs1 = 0;
//total of schemes for a particular client
$sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
$sTotal = 0; $sTotalCagr = 0; $sTotalCagrDivide = 0; $sTotal_abs = 0; $sTotal_absDivide = 0;
$sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
//total of all schemes of a client
$gTotalPurAmt = 0;  $gTotalDivAmt = 0;  $gTotalPurNav = 0; $gTotalPurNavDivide = 0; $gTotalLiveUnit = 0;  $gTotalCurValue = 0;  $gTotalDivR = 0; $gTotalDivPay = 0;
$gTotal = 0;  $gTotalCagr = 0; $gTotalCagrDivide = 0; $gTotal_abs = 0; $gTotal_absDivide = 0;
$gTotalCagr1 = 0; $gTotalCagr2 = 0; $gTotalAbs1 = 0; $gLiveUnit = 0;
//total of all clients for all schemes
$fTotalPurAmt = 0;  $fTotalDivAmt = 0;  $fTotalPurNav = 0; $fTotalPurNavDivide = 0; $fTotalLiveUnit = 0;  $fTotalCurValue = 0;  $fTotalDivR = 0; $fTotalDivPay = 0;
$fTotal = 0;  $fTotalCagr = 0; $fTotalCagrDivide = 0;  $fTotal_abs = 0; $fTotal_absDivide = 0;
$fTotalCagr1 = 0; $fTotalCagr2 = 0; $fTotalAbs1 = 0; $fLiveUnit = 0;$total_amount=0;$sPerTotal=0;

if(!empty($mf_rep_data))
$broker_name = $mf_rep_data[0]->broker_name;
$clientName = $mf_rep_data[0]->client_name;
$schemeName = $mf_rep_data[0]->mf_scheme_name;;
$css = '<style type="text/css">
table { width:100%; color:#000000; }
table td {font-size: 11px; text-align:left; padding:2px}
.amount {text-align:right; padding-right:5px;}
table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
.border {border: 1px solid #4d4d4d;}
.noWrap { white-space: nowrap; }
.title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
.info { font-size: 12px; text-align: center; }
.head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
.normal {font-weight: normal;}
.dataTotal {font-weight: bold; color:#4f8edc;}
.no-border {border-width: 0px; border-color:#fff;}
.client-name { text-align: left; font-size: 14px; font-weight: bold; }
.client-name2 { text-align: left; font-size: 16px; font-weight: bold; }
</style>';
$title = '<div class="title row">AUM Report Of '.$broker_name.'</div><br/>';
if($mf_rep_data)
{
// add client info to page
$html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
  <tbody>';
$newScheme = false; $newClient = false; $schemeFoot = false; $familyFoot = false;
$mfCount = count($mf_rep_data);
  foreach ($mf_rep_data as $rs)
     {
       if($rs->div_payout)
           $total = round($rs->div_payout + $rs->current_value);
       else
           $total = round($rs->current_value);
       $total_amount=$total_amount+$total;
     }
   $html .= '<tr nobr="true">
                 <td class="no-border client-name" colspan="11"><br/></td>
                 </tr>
                 <tr nobr="true" class="head-row">
                     <th width="200">Scheme Name</th>
                     <th width="40">Scheme Type</th>
                     <th width="40">Product Code</th>
                     <th width="40">Market Cap</th>
                     <th width="70">Invest Cost</th>
                     <th width="80">Div Amount</th>
                     <th width="80">No.of Unit</th>
                     <th width="90">Current Value</th>
                     <th width="70">Div R</th>
                     <th width="70">Div Payout</th>
                     <th width="80">Total</th>
                     <th width="45">CAGR</th>
                     <th width="45">ABS</th>
                     <th width="75">Allocation</th>
                 </tr>
             </tbody>
         <tbody>';
     }

for($i = 0; $i < $mfCount; $i++)
{

  $purAmt=round($mf_rep_data[$i]->purchase_amount);
  $divAmt=round($mf_rep_data[$i]->div_amount);
  if($mf_rep_data[$i]->div_payout)
      $total = round($mf_rep_data[$i]->div_payout + $mf_rep_data[$i]->current_value);
  else
      $total = round($mf_rep_data[$i]->current_value);
//  set cagr1(temp)
$per=0;
$per=round(($total/$total_amount)*100,8);
$sPerTotal=$sPerTotal+$per;

  if($mf_rep_data[$i]->cagr1 != null)
      $cagr1 =$mf_rep_data[$i]->cagr1;  // ($purAmt + $divAmt) * $mf_rep_data[$i]->cagr * $mf_rep_data[$i]->transaction_day;
  else
      $cagr1 = 0;
  if($mf_rep_data[$i]->mf_abs != null)
  {
      $cagr2 = $mf_rep_data[$i]->cagr2;//($purAmt + $divAmt) * $mf_rep_data[$i]->transaction_day;
      $abs1 = ($purAmt + $divAmt) * $mf_rep_data[$i]->mf_abs;
  } else {
      $abs1 = 0;
  }

  if(empty($mf_rep_data[$i]->div_r2)) {
      $mf_rep_data[$i]->div_r2 = 0.00;
  }
  if(empty($mf_rep_data[$i]->div_payout)) {
      $mf_rep_data[$i]->div_payout = 0.00;
  }
  //set sub totals
  $sTotalPurAmt = round($purAmt + $sTotalPurAmt);
  $gTotalPurAmt = round($purAmt + $gTotalPurAmt);
  $fTotalPurAmt = round($purAmt + $fTotalPurAmt);
  $sTotalDivAmt = round($divAmt + $sTotalDivAmt);
  $gTotalDivAmt = round($divAmt + $gTotalDivAmt);
  $fTotalDivAmt = round($divAmt + $fTotalDivAmt);
  $sLiveUnit = $mf_rep_data[$i]->live_unit + $sLiveUnit;
  $gLiveUnit = $mf_rep_data[$i]->live_unit + $gLiveUnit;
  $fLiveUnit = $mf_rep_data[$i]->live_unit + $fLiveUnit;
  if($mf_rep_data[$i]->live_unit != null && $mf_rep_data[$i]->live_unit != 0)
  {
      $sTotalPurNav = round((($sTotalPurAmt + $sTotalDivAmt) / $sLiveUnit), 2);
      $gTotalPurNav = round((($gTotalPurAmt + $gTotalDivAmt) / $gLiveUnit), 2);
      $fTotalPurNav = round((($fTotalPurAmt + $fTotalDivAmt) / $fLiveUnit), 2);
      /*$sTotalPurNav = round($sTotalPurNav + $mf_rep_data[$i]->p_nav, 2);
      $gTotalPurNav = round($gTotalPurNav + $mf_rep_data[$i]->p_nav, 2);
      $fTotalPurNav = round($fTotalPurNav + $mf_rep_data[$i]->p_nav, 2);
      $sTotalPurNavDivide++;
      $gTotalPurNavDivide++;
      $fTotalPurNavDivide++;*/
  }
  $sTotalCagr1 = $cagr1 + $sTotalCagr1;
  $gTotalCagr1 = $cagr1 + $gTotalCagr1;
  $fTotalCagr1 = $cagr1 + $fTotalCagr1;
  $sTotalCagr2 = $cagr2 + $sTotalCagr2;
  $gTotalCagr2 = $cagr2 + $gTotalCagr2;
  $fTotalCagr2 = $cagr2 + $fTotalCagr2;
  $sTotalAbs1 = $abs1 + $sTotalAbs1;
  $gTotalAbs1 = $abs1 + $gTotalAbs1;
  $fTotalAbs1 = $abs1 + $fTotalAbs1;
  $sTotalLiveUnit = $mf_rep_data[$i]->live_unit + $sTotalLiveUnit;
  $gTotalLiveUnit = $mf_rep_data[$i]->live_unit + $gTotalLiveUnit;
  $fTotalLiveUnit = $mf_rep_data[$i]->live_unit + $fTotalLiveUnit;
  $sTotalCurValue = round($mf_rep_data[$i]->current_value + $sTotalCurValue);
  $gTotalCurValue = round($mf_rep_data[$i]->current_value + $gTotalCurValue);
  $fTotalCurValue = round($mf_rep_data[$i]->current_value + $fTotalCurValue);
  $sTotalDivR = round($mf_rep_data[$i]->div_r2 + $sTotalDivR);
  $gTotalDivR = round($mf_rep_data[$i]->div_r2 + $gTotalDivR);
  $fTotalDivR = round($mf_rep_data[$i]->div_r2 + $fTotalDivR);
  $sTotalDivPay = round($mf_rep_data[$i]->div_payout + $sTotalDivPay);
  $gTotalDivPay = round($mf_rep_data[$i]->div_payout + $gTotalDivPay);
  $fTotalDivPay = round($mf_rep_data[$i]->div_payout + $fTotalDivPay);
  $sTotal = round($sTotal + $total);
  $gTotal = round($total + $gTotal);
  $fTotal = round($total + $fTotal);

  if($sTotalCagr2 != 0)
      $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
  else
      $sTotalCagr = round($sTotalCagr1, 2);
  if($gTotalCagr2 != 0)
      $gTotalCagr = round(($gTotalCagr1 / $gTotalCagr2), 2);
  else
      $gTotalCagr = round($gTotalCagr1, 2);
  if($fTotalCagr2 != 0)
      $fTotalCagr = round(($fTotalCagr1 / $fTotalCagr2), 2);
  else
      $fTotalCagr = round($fTotalCagr1, 2);

  if($sTotalPurAmt != 0)
  {
      $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt + $sTotalDivAmt)), 2);
  }
  elseif($sTotalDivAmt != 0)
  {
      /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
      $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;
      $fTotal_abs = $fTotalAbs1 + $fTotalDivAmt;*/
      $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt+$sTotalDivAmt)), 2);
  } else {
      $sTotal_abs = 0;
  }
  if($gTotalPurAmt != 0)
  {
      $gTotal_abs = round(($gTotalAbs1 / ($gTotalPurAmt + $gTotalDivAmt)), 2);
  }
  elseif($gTotalDivAmt != 0)
  {
      /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
      $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;
      $fTotal_abs = $fTotalAbs1 + $fTotalDivAmt;*/
      $gTotal_abs = round(($gTotalAbs1 / ($gTotalPurAmt+$gTotalDivAmt)), 2);
  } else {
      $gTotal_abs = 0;
  }
  if($fTotalPurAmt != 0)
  {
      $fTotal_abs = round(($fTotalAbs1 / ($fTotalPurAmt + $fTotalDivAmt)), 2);
  }
  elseif($fTotalDivAmt != 0)
  {
      /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
      $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;
      $fTotal_abs = $fTotalAbs1 + $fTotalDivAmt;*/
      $fTotal_abs = round(($fTotalAbs1 /  ($fTotalPurAmt + $fTotalDivAmt)), 2);
  } else {
      $fTotal_abs = 0;
  }


  $html .= '<tr nobr="true">
      <td class="border normal">'.$mf_rep_data[$i]->mf_scheme_name.'</td>';
      if(!empty($mf_rep_data[$i]->scheme_type))
          $html .= '<td class="border normal" style="text-align:center;">'.$mf_rep_data[$i]->scheme_type.'</td>';
      else
          $html .= '<td class="border normal">General</td>';
        
        $html .= '<td class="border normal" style="text-align:center;">'.$mf_rep_data[$i]->prod_code.'</td>';
      $html .= '<td class="border normal" style="text-align:center;">'.$mf_rep_data[$i]->market_cap.'</td>';
      
          
      if(!empty($purAmt))
          $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($purAmt).'</td>';
      else
          $html .= '<td class="border normal"></td>';
      if(!empty($divAmt))
          $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($divAmt).'</td>';
      else
          $html .= '<td class="border normal"></td>';
  $html .= '<td class="border amount normal">'.sprintf("%.4f",$mf_rep_data[$i]->live_unit).'</td>';
      $curval = floatval($mf_rep_data[$i]->current_value);
      if(!empty($curval))
          $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($mf_rep_data[$i]->current_value)).'</td>';
      else
          $html .= '<td class="border normal"></td>';
      $divr2 = intval($mf_rep_data[$i]->div_r2);
      if(!empty($divr2))
          $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($mf_rep_data[$i]->div_r2)).'</td>';
      else
          $html .= '<td class="border normal"></td>';
      $divp = intval($mf_rep_data[$i]->div_payout);
      if(!empty($divp))
          $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($mf_rep_data[$i]->div_payout)).'</td>';
      else
          $html .= '<td class="border normal"></td>';
      $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($mf_rep_data[$i]->total)).'</td>
                <td class="border amount normal">'.round($mf_rep_data[$i]->cagr,2).'</td>
                <td class="border amount normal">'.round($mf_rep_data[$i]->mf_abs,2).'</td>
                <td class="border amount normal">'.sprintf("%.4f",$per).' % </td>
              </tr>';
      $schemeName = $mf_rep_data[$i]->mf_scheme_name;
  $clientName = $mf_rep_data[$i]->client_name;
}
//last table footer
$html .= '<tr nobr="true">
  <td class="border normal" colspan="4"></td>';
if(!empty($sTotalPurAmt))
  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalPurAmt).'</td>';
else
  $html .= '<td class="border normal"></td>';
if(!empty($sTotalDivAmt))
  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivAmt).'</td>';
else
  $html .= '<td class="border normal"></td>';
$html .= '<td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>';
if(!empty($sTotalCurValue))
  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalCurValue).'</td>';
else
  $html .= '<td class="border normal"></td>';
if(!empty($sTotalDivR))
  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivR).'</td>';
else
  $html .= '<td class="border normal"></td>';
if(!empty($sTotalDivPay))
  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivPay).'</td>';
else
  $html .= '<td class="border normal"></td>';
$html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotal).'</td>';
if(!empty($sTotalCagr))
  $html .= '<td class="dataTotal border amount">'.round($sTotalCagr,2).'</td>';
else
  $html .= '<td class="border amount normal"></td>';
if(!empty($sTotal_abs))
  $html .= '<td class="dataTotal border amount">'.round($sTotal_abs,2).'</td>';
else
  $html .= '<td class="border amount normal"></td>';

$html .= '<td class="dataTotal border amount normal">100 % </td></tr>
<tr>
  <td colspan="11" class="no-border info">
      Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) Rs.'.($sTotal - $sTotalPurAmt).'.
  </td>
</tr>
<tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr></tbody>
  </table>';
//last client total footer
$sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
$sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
$sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;
// include 'mf_report_common_family.php';
$val1=0;
    $val2=0;
    $val3=0;
    $val4=0;
    $val5=0;
    $val6=0;
    $val7=0;
  $sch_type_ids=    array(5,6,7,9);
     if($mf_rep_data_for_chart)
            {
                foreach($mf_rep_data_for_chart as $row)
                {
                    if($row->market_cap=="Multi Cap" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val1+= $row->current_value; 
                    }
                    else if($row->market_cap=="Mid Cap" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val2+= $row->current_value; 
                    }
                    else if($row->market_cap=="Large Cap" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val3+=$row->current_value;
                    }
                    else if($row->market_cap=="Small Cap" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val4+=$row->current_value;
                    }
                    else if($row->market_cap=="Debt" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val5+=$row->current_value;
                    }
                    else if($row->market_cap=="Balanced" && in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val6+=$row->current_value;
                    }
                    else if (in_array($row->scheme_type_id, $sch_type_ids))
                    {
                        $val7+=$row->current_value;
                    }
                    
                }
            }

$s_val1=0;
    $s_val2=0;
    $s_val3=0;
    $s_val4=0;
    $s_val5=0;
    $s_val6=0;
    $s_val7=0;
    $s_val8=0;
    $s_val9=0;
    $s_val10=0;
    $s_val11=0;
    $s_val12=0;
    $s_val13=0;
    $s_val14=0;
    if($mf_rep_data_for_chart)
    {
        foreach($mf_rep_data_for_chart as $row)
                {
                    if($row->scheme_type=="ARBITRAGE")
                    {
                        $s_val1+= $row->current_value; 
                    }
                    else if($row->scheme_type=="BALANCED")
                    {
                        $s_val2+= $row->current_value; 
                    }
                    else if($row->scheme_type=="CAPITAL PROTECTION")
                    {
                        $s_val3+=$row->current_value;
                    }
                    else if($row->scheme_type=="DEBT")
                    {
                        $s_val4+=$row->current_value;
                    }
                    else if($row->scheme_type=="ELSS")
                    {
                        $s_val5+=$row->current_value;
                    }
                    else if($row->scheme_type=="EQUITY")
                    {
                        $s_val6+=$row->current_value;
                    }
                    else if($row->scheme_type=="ETF")
                    {
                        $s_val7+=$row->current_value;
                    }
                    else if($row->scheme_type=="FMP")
                    {
                        $s_val8+=$row->current_value;
                    }
                    else if($row->scheme_type=="FOF")
                    {
                        $s_val9+=$row->current_value;
                    }
                    else if($row->scheme_type=="GOLD FUND")
                    {
                        $s_val10+=$row->current_value;
                    }
                    else if($row->scheme_type=="MIP")
                    {
                        $s_val11+=$row->current_value;
                    }
                    else if($row->scheme_type=="N.A.")
                    {
                        $s_val12+=$row->current_value;
                    }
                    else if($row->scheme_type=="LT Debt")
                    {
                        $s_val13+=$row->current_value;
                    }
                     else if($row->scheme_type=="Liquid")
                    {
                        $s_val14+=$row->current_value;
                    }   
                        
                }
    }


}
?>
<script src="../../assets/vendors/CanvasJS/canvas.js"></script>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<div id='wrap'>
<div id="page-heading">
  <div class="options">
      <div class="btn-toolbar">
          <div class="btn-group">
              <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
              <ul class="dropdown-menu">
                  <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Mutual_funds/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                  <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
              <div class="row" id="report_data" style="overflow-x: auto;"><?php echo $html; ?></div>
              <?php if($mf_rep_data_for_chart){?>
              <div class="row" >
                <div class="col-md-6 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                    <div id="type_wise_chart"  class="chart_css" style="height:385px"></div>
                    
                </div>  
                <div class="col-md-6 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                    <div id="cap_wise_chart"  class="chart_css" style="height:385px"></div>
                </div>  
            </div>
            <input type="hidden" name="chart_1" id="chart_1"  />
            <input type="hidden" name="chart_2" id="chart_2"  />
            <?php }?>
              <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
              <input type="hidden" name="name" id="name" value="<?php echo "AUM Report of".$broker_name;?>" />
              <input type="hidden" name="titleData" id="titleData" />
              <input type="hidden" name="htmlData" id="htmlData" />
              <input type="hidden" name="report_name" id="reportName" value="<?php echo "AUM Reprot of ".$broker_name;?>  " />
          </div>
      </div>
  </form>
</div>
</div>
</div>


<script type="application/javascript">
    $(function() {
        cap_wise(<?php echo isset($val1)?intval($val1):intval(0); ?>,
                                <?php echo isset($val2)?intval($val2):intval(0); ?>,
                                <?php echo isset($val3)?intval($val3):intval(0); ?>,
                                <?php echo isset($val4)?intval($val4):intval(0); ?>,
                                <?php echo isset($val5)?intval($val5):intval(0); ?>,
                                <?php echo isset($val6)?intval($val6):intval(0); ?>,
                                <?php echo isset($val7)?intval($val7):intval(0); ?>);
        
        type_wise_chart(<?php echo isset($s_val1)?intval($s_val1):intval(0); ?>,
                                <?php echo isset($s_val2)?intval($s_val2):intval(0); ?>,
                                <?php echo isset($s_val3)?intval($s_val3):intval(0); ?>,
                                <?php echo isset($s_val4)?intval($s_val4):intval(0); ?>,
                                <?php echo isset($s_val5)?intval($s_val5):intval(0); ?>,
                                <?php echo isset($s_val6)?intval($s_val6):intval(0); ?>,
                                <?php echo isset($s_val7)?intval($s_val7):intval(0); ?>,
                                <?php echo isset($s_val8)?intval($s_val8):intval(0); ?>,
                                <?php echo isset($s_val9)?intval($s_val9):intval(0); ?>,
                                <?php echo isset($s_val10)?intval($s_val10):intval(0); ?>,
                                <?php echo isset($s_val11)?intval($s_val11):intval(0); ?>,
                                <?php echo isset($s_val12)?intval($s_val12):intval(0); ?>,
                                <?php echo isset($s_val13)?intval($s_val13):intval(0); ?>,
                                <?php echo isset($s_val14)?intval($s_val14):intval(0); ?>);
        
    });
    function cap_wise(val1,val2,val3,val4,val5,val6,val7)
    {
        
      
        var total_val=val1+val2+val3+val4+val5+val6+val7;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        var per3=((val3*100)/total_val).toFixed(2);
        var per4=((val4*100)/total_val).toFixed(2);
        var per5=((val5*100)/total_val).toFixed(2);
        var per6=((val6*100)/total_val).toFixed(2);
        var per7=((val7*100)/total_val).toFixed(2);
        var data_points=[];
        if(val1)
        {
            data_points.push({y: val1, label: "Multi Cap",per:per1,color: "#ed3237"});
        }
        if(val2)
        {
            data_points.push({y: val2, label: "Mid Cap",per:per2,color: "#fbb12f"});
        }
        if(val3)
        {
            data_points.push({y: val3, label: "Large Cap",per:per3,color: "#03afcd"});
        }	
        if(val4)
        {
            data_points.push({y: val4, label: "Small Cap",per:per4,color: "#0098da"});
        }	
        if(val5)
        {
            data_points.push({y: val5, label: "Debt",per:per5,color: "#804744"});
        }
        if(val6)
        {
            data_points.push({y: val6, label: "Balanced",per:per6,color: "#804766"});
        }	
        if(val7)
        {
            data_points.push({y: val7, label: "Other",per:per7,color: "#69fd2c"});
        }	
			
			
        
        
    var chart = new CanvasJS.Chart("cap_wise_chart", {
	    animationEnabled: true,
	    borderColor: "red",
	    title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
	    text: "Market Cap Wise"
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
		    startAngle: 0,
			indexLabelMaxWidth: 150,
			indexLabelFontSize:15,
            indexLabelWrap: true ,
		    indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints:data_points
        	}]
        });

        chart.render();

        $('.canvasjs-chart-credit').css('display','none');
    setTimeout(function(){
        
        var canvas = $("#cap_wise_chart").find(".canvasjs-chart-canvas").get(0);
        var dataURL = canvas.toDataURL('image/png');
            $('#chart_2').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        
        },2000);
        
    }
     function type_wise_chart(val1,val2,val3,val4,val5,val6,val7,val8,val9,val10,val11,val12,val13,val14)
    {
        
      
        var total_val=val1+val2+val3+val4+val5+val6+val7+val8+val9+val10+val11+val12+val13+val14;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        var per3=((val3*100)/total_val).toFixed(2);
        var per4=((val4*100)/total_val).toFixed(2);
        var per5=((val5*100)/total_val).toFixed(2);
        var per6=((val6*100)/total_val).toFixed(2);
        var per7=((val7*100)/total_val).toFixed(2);
        var per8=((val8*100)/total_val).toFixed(2);
        var per9=((val9*100)/total_val).toFixed(2);
        var per10=((val10*100)/total_val).toFixed(2);
        var per11=((val11*100)/total_val).toFixed(2);
        var per12=((val12*100)/total_val).toFixed(2);
        var per13=((val13*100)/total_val).toFixed(2);
        var per14=((val14*100)/total_val).toFixed(2);
        var data_points=[];
        
        if(val1)
        {
            data_points.push({y: val1, label: "ARBITRAGE",per:per1,color: "#ed3237"});
        }
        if(val2)
        {
            data_points.push({y: val2, label: "BALANCED",per:per2,color: "#fbb12f"});
        }
        if(val3)
        {
            data_points.push({y: val3, label: "CAPITAL PROTECTION",per:per3,color: "#03afcd"});
        }	
        if(val4)
        {
            data_points.push({y: val4, label: "DEBT",per:per4,color: "#0098da"});
        }	
        if(val5)
        {
            data_points.push({y: val5, label: "ELSS",per:per5,color: "#804744"});
        }
        if(val6)
        {
            data_points.push({y: val6, label: "EQUITY",per:per6,color: "#804766"});
        }	
        if(val7)
        {
            data_points.push({y: val7, label: "ETF",per:per7,color: "#03afcd"});
        }	
        if(val8)
        {
            data_points.push({y: val8, label: "FMP",per:per8,color: "#ed3237"});
        }	
        if(val9)
        {
            data_points.push({y: val9, label: "FOF",per:per9,color: "#fbb12f"});
        }	
        if(val10)
        {
            data_points.push({y: val10, label: "GOLD FUND",per:per10,color: "#03afcd"});
        }	
        if(val11)
        {
            data_points.push({y: val11, label: "MIP",per:per11,color: "#0098da"});
        }	
        if(val12)
        {
            data_points.push({y: val12, label: "N.A.",per:per12,color: "#804744"});
        }	
			
		if(val13)
        {
            data_points.push({y: val13, label: "LT Debt",per:per13,color: "#0098da"});
        }	
        if(val14)
        {
            data_points.push({y: val14, label: "Liquid",per:per14,color: "#fbb12f"});
        }	
    var chart1 = new CanvasJS.Chart("type_wise_chart", {
	    animationEnabled: true,
	    borderColor: "red",
	    title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
	    text: "Scheme Type Wise"
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
		    startAngle: 0,
		    indexLabelFontSize:15,
			indexLabelMaxWidth: 120,
            indexLabelWrap: true ,
		    indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints:data_points
        	}]
        });

        chart1.render();

        $('.canvasjs-chart-credit').css('display','none');
         setTimeout(function(){
        
        var canvas = $("#type_wise_chart").find(".canvasjs-chart-canvas").get(0);
        var dataURL = canvas.toDataURL('image/png');
            $('#chart_1').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        
        },2500);
        
    }
    
</script>