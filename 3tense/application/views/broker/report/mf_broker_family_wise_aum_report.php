<?php
error_reporting(E_ALL);

if(empty($mf_rep_data))
{
  echo "<script type='text/javascript'>
      alert('Unauthorized Access. Get Outta Here!');
      window.top.close();  //close the current tab
    </script>";
} else {
    $css = '<style type="text/css">
      table { width:100%; color:#000000; }
      table td {font-size: 11px; padding:2px; text-align:center; }
      table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
      .border {border: 1px solid #4d4d4d;}
      .noWrap { white-space: nowrap; }
      .amount { text-align:right; padding-right:5px; text-indent: 5px;}
      .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
      .info { font-size: 12px; text-align: center; }
      .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
      .normal {font-weight: normal;}
      .dataTotal {font-weight: bold; color:#4f8edc;text-align:right;}
      .no-border {border-width: 0px; border-color:#fff;}
      .client-name { text-align: left; font-size: 14px; font-weight: bold; }
      .client-name2 { text-align: left; font-size: 16px; font-weight: bold; }
  </style>';
    if($mf_rep_data)
    {

        $family_portfolio='<div class="title">Family AUM Report</div><br/>';
        $summary_portfolio='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
        $total=0;$profit=0;$amt_totals=0;$units_totals=0;$cur_value_total=0;
        $div_red_total=0;$div_payout_total=0;$profit_total=0;$cagr_total=0;$abs_total=0;

        $summary_portfolio.='<tr><td colspan="16"  >'.$family_portfolio.'</td></tr>';
        $summary_portfolio.='<tr nobr="true" class="head-row">
                            <th width="297">Family Name</th>
                            <th width="97">Purchase Amount</th>
                            <th width="97">Current Value</th>
                            <th width="97">Div R</th>
                            <th width="97">Div Payout</th>
                            <th width="97">Gain / Loss</th>
                            <th width="97">CAGR</th>
                            <th width="97">ABS</th>
                            </tr>';

        $num_rows=sizeof ($mf_rep_data);
        $i=0;
        
        foreach ($mf_rep_data as $rows)
        {
            $i=$i+1;
            $total=$rows->current_value+$rows->payout;
            $profit=$total-$rows->Amount;
            $amt_totals=$amt_totals+$rows->Amount;
            $units_totals=$units_totals+$rows->Units;
            $cur_value_total=$cur_value_total+$rows->current_value;
            $div_red_total= $div_red_total+$rows->div_r2  ;
            $div_payout_total=$div_payout_total + $rows->payout;
            $profit_total=$profit_total+$profit;
            // $cagr_total=$cagr_total+$rows->cagr;
            // $abs_total=$abs_total+$rows->abs;
            $summary_portfolio.='<tr>
                        <td class="border" style="text-align:left" >'.$rows->family_name.'</td>
                        <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->Amount)).'</td>
                        <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->current_value)).'</td>';
            if(!empty($rows->div_r2))
            {
            $summary_portfolio.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2)).'</td>';
            }
            else{
            $summary_portfolio.='<td class="border"></td>';
            }
            $payoutt=$rows->payout;
            if($payoutt==0)
            {
            $payoutt='';
            }
            if(!empty($payoutt))
            {
            $summary_portfolio.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($payoutt)).'</td>';
            }
            else{
            $summary_portfolio.='<td class="border"></td>';
            }

            $summary_portfolio.='<td class=" border amount">'.$this->common_lib->moneyFormatIndia(round($profit)).'</td>
                                <td class=" border amount">'.sprintf("%.2f",$rows->cagr).'</td>
                                <td class=" border amount">'.sprintf("%.2f",$rows->abs).'</td>
                                </tr>';
                                // echo $i;
            if($i==$num_rows)
            {
                $summary_portfolio.='<tr>
                <td class=" border dataTotal"  style="text-align:left">Family`s Grand Total :-</td>
                                
                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($amt_totals)).'</td>

                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($cur_value_total)).'</td>';
                if(empty($div_red_total)||$div_red_total==0)
                {
                $summary_portfolio.='<td class=" border dataTotal"></td>';
                }else {
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_red_total)).'</td>';
                }
                if(empty($div_payout_total)||$div_payout_total==0)
                {
                $summary_portfolio.='<td class=" border dataTotal"></td>';
                }else {
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_payout_total)).'</td>';
                }
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($profit_total)).'</td>
                                <td class=" border dataTotal amount"></td>
                                <td class=" border dataTotal amount"></td>
                                </tr>
                            </table><br>';
            }
            //.sprintf("%.2f",$fTotalCagr).
            //sprintf("%.2f",$fTotal_abs)
        }
        $html=$summary_portfolio;
    }

    $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
    $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;
}
?>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
  <div id='wrap'>
      <div id="page-heading">
          <div class="options">
              <div class="btn-toolbar">
                  <div class="btn-group">
                      <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                      <ul class="dropdown-menu">
                          <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Mutual_funds/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                          <!-- <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf');?>');">PDF File (*.pdf)</a></li> -->
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
                      <!-- <div id="title_data"><?php echo $title; ?></div> -->
                      <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                      <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                      <input type="hidden" name="name" id="name" value="Family wise aum report" />
                      <input type="hidden" name="titleData" id="titleData" />
                      <input type="hidden" name="htmlData" id="htmlData" />
                      <!-- <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Mutual Fund Portfolio" /> -->
                  </div>
              </div>
          </form>
      </div>
  </div>
</div>
