<?php
if(empty($mf_rep_data))
{
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
}
else
{
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 11px; padding:2px; text-align:center; }
        table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right; text-indent: 5px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; text-align: center; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
$clientName=$mf_rep_data['0']->client_name;
$title = '<div class="title row">Scheme Wise Detail Mutual Fund Portfolio Of '.$clientName.'</div><br/>';
    // add client info to page
$html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;"><tbody>';
// $heading='<div class="title row">Scheme Summary of '.$clientName.' </div><br>';
$data='<br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';
// $data.='<tr><td colspan="10"  >'.$heading.'</td></tr>';
// $data.='<tr  nobr="true" class="head-row">
//                         <th width="180">Scheme Name</th>
//                         <th width="80">Folio Number </th>
//                         <th width="80">Invest Cost</th>
//                         <th width="80">Div Amount</th>
//                         <th width="80">Live Unit</th>
//                         <th width="80">Current Value</th>
//                         <th width="80">Dividend/B</th>
//                         <th width="80">Div Payout</th>
//                         <th width="80">Total</th>
//                         <th width="80">CAGR</th>
//                         <th width="80">Abs. Rtn</th>
//                     </tr>';

            $data.='<tr nobr="true" class="head-row">';
            
            if($hide_nav_date!='hide_nav_date')    
                        { 
                            $data.='<th width="100">Scheme Name</th>';
                        }
                        else
                        {
                            $data.='<th width="155">Scheme Name</th>';
                        }
                        $data.='<th width="65">Folio Number</th>
                        <th width="65">Inception Date</th>
                        <th width="55">Scheme Type</th>
                        <th width="60">Purchase Amount</th>
                        <th width="55">Div Amount</th>
                        <th width="55">Purchase NAV</th>
                        <th width="60">No. of Unit</th>
                        <th width="55">Trans Day</th>
                        <th width="55">Current NAV</th>';
                        if($hide_nav_date!='hide_nav_date')    
                        {
                            $data.='<th width="65">Current NAV Date</th>';
                        }
                        $data.='<th width="75">Current Value</th>
                        <th width="45">Div R</th>
                        <th width="45">Div Payout</th>
                        <th width="60">Total</th>
                        <th width="35">CAGR</th>
                        <th width="35">ABS</th>
                    </tr>';
      if($mf_rep_data)
      {
        $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
        $purAmt=0;$divAmt=0;$cagr1=0;$cagr2=0;$abs1=0;
        $sTotalPurAmt=0;$sTotalDivAmt=0;$sTotalCagr1=0;$sTotalCagr2=0;$sTotalCagr=0;$sTotal_abs=0;$sTotalAbs1=0;
        foreach ($mf_rep_data as $rs)
        {
          $purAmt=$rs->purchase_amount;
          $divAmt=$rs->div_amount;
          //set cagr1(temp)
          if($rs->cagr1 != null)
              $cagr1 = $rs->cagr1;//($purAmt + $divAmt) * $rs->cagr * $rs->transaction_day;
          else
              $cagr1 = 0;
          if($rs->mf_abs != null)
          {
              $cagr2 =  $rs->cagr2;//($purAmt + $divAmt) * $rs->transaction_day;
              $abs1 = ($purAmt + $divAmt) * $rs->mf_abs;
          } else {
              $abs1 = 0;
              $cagr2 = 0;
          }
          //set sub totals
          $sTotalPurAmt = round($purAmt + $sTotalPurAmt);
          $sTotalDivAmt = round($divAmt + $sTotalDivAmt);
          $sTotalCagr1 = $cagr1 + $sTotalCagr1;
          $sTotalCagr2 = $cagr2 + $sTotalCagr2;
          $sTotalAbs1 = $abs1 + $sTotalAbs1;
          if($sTotalCagr2 != 0) {
              $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
          } else {
              $sTotalCagr = round($sTotalCagr1, 2);
          }

          if($sTotalPurAmt!= 0)
          {
              $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt + $sTotalDivAmt)), 2);
          }
          elseif($sTotalDivAmt!=0)
          {
              $sTotal_abs = round(($sTotalAbs1 / $sTotalDivAmt), 2);
          } else {
              $sTotal_abs = 0;
          }

          if($rs->purchase_date) {
              $date = DateTime::createFromFormat('d/m/Y',$rs->purchase_date);
              $date = $date->format('d-M-Y');
          } else {
              $date = "";
          }
          if($rs->c_nav_date) {
              $date2 = DateTime::createFromFormat('d/m/Y',$rs->c_nav_date);
              $date2 = $date2->format('d-M-Y');
          } else {
              $date2 = "";
          }

          $data.='<tr>
                      <td class="border ">'.$rs->mf_scheme_name.'</td>
                      <td class="border ">'.$rs->folio_number.'</td>
                      <td class="border">'.$date.'</td>';
                        if($cap_detail=="cap_detail")
                    {
                        
                        $data .= '<td class="border normal">';
                        if($rs->market_cap==''){
                        $data .='Other</td>';}
                        else{
                        $data .=$rs->market_cap.'</td>';
                        }
                    }
                    else
                    {
                  $data.='    <td class="border">'.$rs->scheme_type.'</td>';
                    }
        
                  
                      if(empty($rs->purchase_amount) || $rs->purchase_amount==0)
                        $data.= '<td class="border normal"></td>';
                      else
                        $data.= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase_amount)).'</td>';
                      if($rs->div_amount==0 ||empty($rs->div_amount))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_amount)).'</td>';
                      }
                    $data.='<td class="border amount">'.sprintf("%.4f",$rs->p_nav).'</td>
                            <td class="border amount">'.sprintf("%.4f",$rs->live_unit).'</td>';
                        
                            $data.='<td class="border amount">'.$rs->transaction_day.'</td>';
                        
                            $data.='<td class="border amount">'.sprintf("%.4f",$rs->c_nav).'</td>';
                            if($hide_nav_date!='hide_nav_date')    
                            {
                                $data.='<td class="border">'.$date2.'</td>';
                            }
                            $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->current_value)).'</td>';
                      if($rs->div_r2==0 ||empty($rs->div_r2))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_r2)).'</td>';
                      }

                      if($rs->div_payout==0 ||empty($rs->div_payout))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_payout)).'</td>';
                      }
                      $total=0;
                      if($rs->div_payout)
                          $total = round($rs->div_payout + $rs->current_value, 2);
                      else
                          $total = round($rs->current_value, 2);
         $data.='<td class=" border amount ">'.$this->common_lib->moneyFormatIndia(round($total )).'</td>
                      <td class=" border amount ">'.sprintf("%.2f",$rs->cagr).'</td>
                      <td class=" border amount ">'.sprintf("%.2f",$rs->mf_abs).'</td>
                      </tr>';

                  $f_pur_amt=$f_pur_amt+$rs->purchase_amount;
                  $f_div_amount=$f_div_amount+$rs->div_amount;
                  $f_live_unit=$f_live_unit+$rs->live_unit;
                  $f_current_value=$f_current_value+$rs->current_value;
                  $f_div_r2=$f_div_r2+$rs->div_r2;
                  $f_div_payout=$f_div_payout+$rs->div_payout;
                  $f_total=$f_total+$total;
                  $f_TotalPurNav = round((($f_pur_amt + $f_div_amount) / $f_live_unit), 2);
            }
            $data.='<tr>
                        <td class="border dataTotal" colspan="4">Total</td>
                        <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_pur_amt)).'</td>';
                        if($f_div_amount==0 ||empty($f_div_amount))
                        {
                          $data.='<td class="border amount"></td>';
                        }else {
                          $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_amount)).'</td>';
                        }
                        $data.='<td class="border amount dataTotal"></td>
                                <td class="border amount dataTotal">'.sprintf("%.4f",$f_live_unit).'</td>
                                <td class="border dataTotal"></td>';
                         if($hide_nav_date!='hide_nav_date')    
                        {       
                                $data.='<td class="border dataTotal"></td>';
                        }
                                $data.='<td class="border dataTotal"></td>
                                <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_current_value)).'</td>';
                          if($f_div_r2==0 ||empty($f_div_r2))
                          {
                            $data.='<td class="border"></td>';
                          }else {
                            $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_r2)).'</td>';
                          }

                          if($f_div_payout==0 ||empty($f_div_payout))
                          {
                            $data.='<td class="border amount"></td>';
                          }else {
                            $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_payout)).'</td>';
                          }
                        $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_total )).'</td>
                        <td class="border amount dataTotal">'.round($sTotalCagr,2).'</td>
                        <td class="border amount dataTotal">'.round($sTotal_abs,2).'</td>
                        </tr></table><br>';
        }

        $html.=$data;
        $fTotal=$f_total;
        $TotalCagr=$sTotalCagr;
        $TotalCagrForXirr=$TotalCagr;
        $Total_abs=$sTotal_abs;
         include 'mf_report_common_client.php';

$val1=0;
    $val2=0;
    $val3=0;
    $val4=0;
    $val5=0;
    $val6=0;
    $val7=0;
  $sch_type_ids=    array(5,6,7,9);
     if($mf_comman_cap_detail_1)
            {
                foreach($mf_comman_cap_detail_1 as $row)
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
    if($mf_comman_cap_detail_2)
    {
        foreach($mf_comman_cap_detail_2 as $row)
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

        // $typewise='<br><div class="title row">Type Wise Summary of '.$clientName.' </div><br>';
        // $summary_typewise='<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
        // $summary_typewise.='<tr><td colspan="9"  >'.$typewise.'</td></tr>';
        // $summary_typewise.='<tr  nobr="true" class="head-row">
        //                         <th width="90">Fund Types</th>
        //                         <th width="90">Invest Cost</th>
        //                         <th width="90">Current Value</th>
        //                         <th width="90">Dividend/B</th>
        //                         <th width="90">Dividend Payout</th>
        //                         <th width="90">Gain/Loss</th>
        //                         <th width="90">CAGR</th>
        //                         <th width="90">Abs. Rtn</th>
        //                         <th width="90">Allocation</th>
        //                     </tr>';
        //       if($mf_summary_typewise)
        //       {
        //         $nums_rows=sizeof ($mf_summary_typewise);
        //         $j=0;$inv_cost_total=0;$cur_value_total=0;$cagr_total=0;$abs_total=0;
        //         $div_payout_total=0;$div_r2_total=0;$profit_total=0;$total=0;$per=0;
        //         foreach ($mf_summary_typewise as $rss)
        //            {
        //              $inv_cost_total=$inv_cost_total+$rss->purchase_amount;
        //            }
        //      foreach ($mf_summary_typewise as $rows)
        //         {
        //           $j=$j+1;
        //           $total=round($rows->current_value+$rows->payout,2);
        //           $purchase=$rows->purchase_amount;
        //           // echo $purchase;
        //           $profit=$total-$purchase;
        //           $per=($purchase/$inv_cost_total)*100;
        //           $profit_total=round($profit_total+$profit,2);
        //           $div_r2_total=$div_r2_total+$rows->div_r2;
        //           $div_payout_total=$div_payout_total+$rows->payout;
        //           $cur_value_total=$cur_value_total+$rows->current_value;
        //           $cagr_total=$cagr_total+$rows->cagr;
        //           $abs_total=$abs_total+$rows->abs;
        //           $summary_typewise.='<tr>
        //                       <td class=" border ">'.$rows->scheme_type.'</td>';
        //                       // $payoutt=$rows->payout;
        //                       if($purchase==0)
        //                       {
        //                         $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else{
        //                         $summary_typewise.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$purchase)).'</td>';
        //                       }
        //                       if($rows->current_value==0)
        //                       {
        //                         $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else{
        //                         $summary_typewise.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->current_value)).'</td>';
        //                       }
        //                       if($rows->div_r2==0)
        //                       {
        //                         $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else{
        //                         $summary_typewise.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->div_r2 )).'</td>';
        //                       }
        //
        //                       if($rows->payout==0)
        //                       {
        //                         $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else{
        //                         $summary_typewise.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->payout)).'</td>';
        //                       }
        //                       if($profit==0)
        //                       {
        //                         $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else {
        //                           $summary_typewise.='<td class=" border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$profit)).'</td>';
        //                       }
        //                       if($rows->cagr==0)
        //                       {
        //                           $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else {
        //                           $summary_typewise.='<td class=" border ">'.sprintf("%.2f",$rows->cagr).'</td>';
        //                       }
        //                       if($rows->abs==0)
        //                       {
        //                           $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else {
        //                           $summary_typewise.='<td class=" border ">'.sprintf("%.2f",$rows->abs).'</td>';
        //                       }
        //                       if($per==0)
        //                       {
        //                           $summary_typewise.='<td class="border"></td>';
        //                       }
        //                       else {
        //                         $summary_typewise.='<td class=" border ">'.sprintf("%.2f",$per).' %'.'</td>';
        //                       }
        //                     $summary_typewise.='</tr>';
        //                       // echo $i;
        //               if($j==$nums_rows)
        //               {
        // $summary_typewise.='<tr>
        //                         <td class=" dataTotal border ">Total :-</td>
        //                         <td class=" dataTotal border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$inv_cost_total)).'</td>
        //                         <td class=" dataTotal border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$cur_value_total)).'</td>
        //                         <td class=" dataTotal border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$div_r2_total)).'</td>
        //                         <td class=" dataTotal border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$div_payout_total)).'</td>
        //                         <td class=" dataTotal border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$profit_total)).'</td>
        //                         <td class=" dataTotal border ">'.round($cagr_total,2).'</td>
        //                         <td class=" dataTotal border ">'.round($abs_total,2).'</td>
        //                         <td class=" dataTotal border ">100 %</td>
        //                     </tr>
        //                   </table>';
        //                     }
        //          }
        //       }
        //       $html.=$summary_typewise;
        //
        //       // print_r($mf_comman_scheme_summary);
        //       $heading='<br><div class="title row">Scheme Summary of '.$clientName.' </div><br>';
        //       $common_scheme_summ='<br><br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';
        //       $common_scheme_summ.='<tr><td colspan="10"  >'.$heading.'</td></tr>';
        //       $common_scheme_summ.='<tr  nobr="true" class="head-row">
        //                               <th width="150">Scheme Name</th>
        //                               <th width="90">Invest Cost</th>
        //                               <th width="90">Div Amount</th>
        //                               <th width="90">Live Unit</th>
        //                               <th width="90">Current Value</th>
        //                               <th width="90">Dividend/B</th>
        //                               <th width="90">Div Payout</th>
        //                               <th width="90">Total</th>
        //                               <th width="90">CAGR</th>
        //                               <th width="90">Abs. Rtn</th>
        //                           </tr>';
        //             if($mf_comman_scheme_summary)
        //             {
        //               $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
        //               foreach ($mf_comman_scheme_summary as $rs)
        //               {
        //                 $common_scheme_summ.='<tr>
        //                             <td class=" border ">'.$rs->mf_scheme_name.'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->purchase_amount)).'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_amount)).'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->live_unit)).'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->current_value)).'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_r2)).'</td>
        //                             <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_payout)).'</td>';
        //                             $total=0;
        //                             if($rs->div_payout)
        //                                 $total = round($rs->div_payout + $rs->current_value, 2);
        //                             else
        //                                 $total = round($rs->current_value, 2);
        //                $common_scheme_summ.='<td class=" border ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$total )).'</td>
        //                             <td class=" border ">'.sprintf("%.2f",$rs->cagr).'</td>
        //                             <td class=" border ">'.sprintf("%.2f",$rs->abs).'</td>
        //                             </tr>';
        //
        //                         $f_pur_amt=$f_pur_amt+$rs->purchase_amount;
        //                         $f_div_amount=$f_div_amount+$rs->div_amount;
        //                         $f_live_unit=$f_live_unit+$rs->live_unit;
        //                         $f_current_value=$f_current_value+$rs->current_value;
        //                         $f_div_r2=$f_div_r2+$rs->div_r2;
        //                         $f_div_payout=$f_div_payout+$rs->div_payout;
        //                         $f_total=$f_total+$total;
        //                   }
        //                   $common_scheme_summ.='<tr>
        //                               <td class="border dataTotal">Total</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_pur_amt)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_div_amount)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_live_unit)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_current_value)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_div_r2)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_div_payout)).'</td>
        //                               <td class="border dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_total )).'</td>
        //                               <td class="border dataTotal"></td>
        //                               <td class="border dataTotal"></td>
        //                               </tr></table>';
        //               }
        //               $html.=$common_scheme_summ;
        //
        //       // print_r($mf_summary_net_investment);
        //       $heading_netinvestment='<br><br><div class="title row">Net investment of '.$clientName.' </div><br>';
        //       $net_investmet='<table width="100%"><tr><td colspan="10"  >'.$heading_netinvestment.'</td></tr>';
        //       $net_investmet.='<tr  nobr="true" class="head-row">
        //                               <th width="50%">Scheme Name</th>
        //                               <th width="50%">Invest Cost</th></tr> ';
        //
        //       foreach ($mf_summary_net_investment as $rs)
        //       {
        //         $net_investmet.='<tr>
        //                             <td   class="dataTotal border">Total Investment </td>
        //                             <td   class=" dataTotal border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->purchase)).'</td>
        //                           </tr>
        //                           <tr>
        //                               <td  class="dataTotal border" >Total Redemption </td>
        //                               <td  class="dataTotal border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->redemption)).'</td>
        //                             </tr>
        //
        //                             <tr>
        //                               <td  class="dataTotal border">Dividend Payout </td>
        //                               <td  class="dataTotal border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->payout_divr2)).'</td>
        //                             </tr>
        //                             <tr>
        //                               <td  class="dataTotal border">Net Investment </td>
        //                               <td  class=" dataTotal border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",($rs->purchase+$rs->payout_divr2)-$rs->redemption)).'</td>
        //                             </tr>
        //                         </table>';
        //       }
        //     $html.=$net_investmet;
        // $html .= '<table><tbody>
        //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
        //     <tr nobr="true">
        //         <td colspan="17" class="no-border amount">
        //             <p class="client-name">Net Investment: '.$this->common_lib->moneyFormatIndia(round($net_inv_data[0]->net_Investment)).'</p>
        //         </td>
        //     </tr>
        //      <tr nobr="true">
        //         <td colspan="17" class="no-border amount">
        //             <p class="client-name">Current Value of Total Investment is: '.$this->common_lib->moneyFormatIndia(round($f_total)).'</p>
        //         </td>
        //     </tr>
        //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
        //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
        //     <tr nobr="true">
        //         <td colspan="17" class="no-border amount">
        //             <p>Note:</p>
        //             <p>1) Annualized Return is calculated on an Absolute basis for < 1 Year And on CAGR basis for >= 1 Year.</p>
        //             <p>2) This Report is Strictly Private and confidential only for clients.</p>
        //         </td>
        //     </tr>';
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
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf_with_three_chart');?>');">PDF File (*.pdf)</a></li>
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
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                        <?php if($mf_comman_cap_detail_1){?>
                          <div class="row" >
                            <div class="col-md-4 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                                <div id="type_wise_chart"  class="chart_css" style="height:230px"></div>
                                
                            </div>  
                            <div class="col-md-4 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                                <div id="cap_wise_chart"  class="chart_css" style="height:230px"></div>
                            </div>  
                               <div class="col-md-4 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                                <div id="mf_summary_chart"  class="chart_css" style="height:230px"></div>
                            </div> 
                        </div>
                        <input type="hidden" name="chart_1" id="chart_1"  />
                        <input type="hidden" name="chart_2" id="chart_2"  />
                        <input type="hidden" name="chart_3" id="chart_3"  />
                        <?php }?>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $mf_rep_data[0]->client_name;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $mf_rep_data[0]->client_name;?> Mutual Fund Portfolio" />
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
                                
        var arr_val = <?php echo json_encode($mf_summary_detail_for_chart) ?>;
    var arr_purchase=[];
    var arr_current_value=[];
    $.each(arr_val, function(key, value) {
        arr_purchase.push({
            x:new Date(value.SummaryDate),
            y:(+value.Purchase_Value)
        });
        arr_current_value.push({
            x:new Date(value.SummaryDate),
            y: (+value.value)
        });
    });
        mf_summary_chart(arr_purchase,arr_current_value);              
             
        
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
		    startAngle: 140,
			indexLabelMaxWidth: 150,
			indexLabelFontSize:12,
		    indexLabelFontWeight: "bold",
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
		    startAngle: 140,
			indexLabelMaxWidth: 150,
            indexLabelWrap: true ,
            indexLabelFontSize:12,
		    indexLabelFontWeight: "bold",
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
      function mf_summary_chart(arr_purchase,arr_current_value)
    {
     
             
        var chart2 = new CanvasJS.Chart("mf_summary_chart", {
            animationEnabled: true,
	        theme: "light2",
        	title:{
        		text: "AUM Growth",
        		 fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20
	    
        	},
        	axisX:{
        		valueFormatString: "MMM-YYYY",
        		crosshair: {
        			enabled: true,
        			snapToDataPoint: true
        		}
        		,labelFontColor: "black",
        		labelFontWeight: "bold"
        	},
        	axisY: {
        		title: "",
        		 valueFormatString: "##,##,##,###.#",
        		includeZero: true,
        		crosshair: {
        			enabled: true
        		}
        		,labelFontColor: "black",
        		labelFontWeight: "bold"
        	},
        	toolTip:{
        		shared:true,
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
        	legend:{
        		cursor:"pointer",
        		verticalAlign: "bottom",
        		horizontalAlign: "left",
        		dockInsidePlotArea: true,
        		itemclick: toogleDataSeries
        	},
        	data: [
        	    /*{
        		type: "line",
        		showInLegend: true,
        		name: "Purchase Value",
        		markerType: "square",
        		xValueFormatString: "MMM-YYYY",
        		color: "#F08080",
        		dataPoints:arr_purchase
        	}
        	,*/
        	{
        		type: "line",
        		showInLegend: true,
        		markerType: "square",
        		name: "Live Value",
        		xValueFormatString: "MMM-YYYY",
        		color: "#814766",
        		dataPoints: arr_current_value
        	}]
        });
        chart2.render();
        
        function toogleDataSeries(e){
        	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        		e.dataSeries.visible = false;
        	} else{
        		e.dataSeries.visible = true;
        	}
        	chart2.render();
        }
        
        $('.canvasjs-chart-credit').css('display','none');
        
        setTimeout(function(){
            var canvas = $("#mf_summary_chart").find(".canvasjs-chart-canvas").get(0);
            var dataURL = canvas.toDataURL('image/png');
            $('#chart_3').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        },2500);
        
    }
    
    
</script>