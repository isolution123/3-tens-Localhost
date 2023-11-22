<?php
if($mf_summary_client)
{
    $family_portfolio='<div class="title">Client Wise Summary of '.$familyName.' Family</div><br/>';
    $summary_portfolio='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
    $total=0;$profit=0;$amt_totals=0;$units_totals=0;$cur_value_total=0;$div_red_total=0;$div_payout_total=0;$profit_total=0;$cagr_total=0;$abs_total=0;
            $summary_portfolio.='<tr><td colspan="16"  >'.$family_portfolio.'</td></tr>';
        $summary_portfolio.='<tr nobr="true" class="head-row">
                                <th width="297">Client Name</th>
                                <th width="97">Purchase Amount</th>
                                <th width="97">Current Value</th>
                                <th width="97">Div R</th>
                                <th width="97">Div Payout</th>
                                <th width="97">Gain / Loss</th>
                                <th width="97">CAGR</th>
                                <th width="97">ABS</th>
                            </tr>';

                $num_rows=sizeof ($mf_summary_client);
                $i=0;
                // for($i=0;$i<)
             foreach ($mf_summary_client as $rows)
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
                              <td class="border" style="text-align:left" style="padding-left:5px;" >'.$rows->client_name.'</td>
                              <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->Amount)).'</td>
                              <td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->current_value)).'</td>';
                              if(empty($rows->div_r2) || $rows->div_r2=="0")
                              {
                                $summary_portfolio.='<td class="border"></td>';
                              }
                            else
                              {
                                $summary_portfolio.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2)).'</td>';

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
                              <td class=" border dataTotal" style="text-align:left" >Family`s Grand Total :-</td>
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
                              <td class=" border dataTotal amount">'.sprintf("%.2f",$fTotalCagr).'</td>
                              <td class=" border dataTotal amount">'.sprintf("%.2f",$fTotal_abs).'</td>
                            </tr>
                          </table><br>';
                            }
                 }
                 $html.=$summary_portfolio;
 }

if($mf_summary_typewise)
{
 $typewise='<div class="title">Fund Type Wise Summary of '.$familyName.' Family </div><br>';
 $summary_typewise='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
    $summary_typewise.='<tr><td colspan="18"  >'.$typewise.'</td></tr>';
    $summary_typewise.='<tr  nobr="true" class="head-row">
                            <th width="194">Fund Types</th>
                            <th width="108">Purchase Amount</th>
                            <th width="108">Current Value</th>
                            <th width="88">Div R</th>
                            <th width="88">Div Payout</th>
                            <th width="108">Total</th>
                            <th width="88">Gain / Loss</th>
                            <th width="66">CAGR</th>
                            <th width="66">ABS</th>
                            <th width="66">Allocation</th>
                        </tr>';

            $nums_rows=sizeof ($mf_summary_typewise);
            $j=0;$inv_cost_total=0;$cur_value_total=0;$cagr_total=0;$abs_total=0;
            $div_payout_total=0;$div_r2_total=0;$profit_total=0;$total=0;$per=0;
            $pur_total=0;
            $total_total=0;
         foreach ($mf_summary_typewise as $rows)
            {
              $j=$j+1;
              $purchase=0;
              $total=$rows->current_value+$rows->payout;
              $total_total=$total_total+$total;
              $purchase=$rows->purchase_amount;
              $profit=$total-$purchase;
              $per=($total/$fTotal)*100;
              $profit_total=$profit_total+$profit;
              $div_r2_total=$div_r2_total+$rows->div_r2;
              $div_payout_total=$div_payout_total+$rows->payout;
              $cur_value_total=$cur_value_total+$rows->current_value;
              $cagr_total=$cagr_total+$rows->cagr;
              $abs_total=$abs_total+$rows->abs;
              $pur_total=$pur_total+$purchase;
              $summary_typewise.='<tr>
                          <td class=" border ">'.$rows->scheme_type.'</td>';
                          // $payoutt=$rows->payout;
                          if($purchase==0)
                          {
                            $summary_typewise.='<td class="border"></td>';
                          }
                          else{
                            $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($purchase)).'</td>';
                          }
                          if($rows->current_value==0)
                          {
                            $summary_typewise.='<td class="border amount"></td>';
                          }
                          else{
                            $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->current_value)).'</td>';
                          }
                          if($rows->div_r2==0)
                          {
                            $summary_typewise.='<td class="border "></td>';
                          }
                          else{
                            $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2 )).'</td>';
                          }

                          if($rows->payout==0)
                          {
                            $summary_typewise.='<td class="border"></td>';
                          }
                          else{
                            $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->payout)).'</td>';
                          }

                          if(empty($total))
                          {
                            $summary_typewise.='<td class="border"></td>';
                           }else {
                            $summary_typewise.='<td class=" border amount">'.$this->common_lib->moneyFormatIndia(round($total )).'</td>';
                          }
                          if($profit==0)
                          {
                            $summary_typewise.='<td class="border"></td>';
                          }
                          else {
                              $summary_typewise.='<td class=" border amount">'.$this->common_lib->moneyFormatIndia(round($profit)).'</td>';
                          }
                          if($rows->cagr==0||empty($rows->cagr))
                          {
                              $summary_typewise.='<td class="border"></td>';
                          }
                          else {
                              $summary_typewise.='<td class=" border amount">'.sprintf("%.2f",$rows->cagr).'</td>';
                          }
                          if($rows->abs==0 ||empty($rows->abs))
                          {
                              $summary_typewise.='<td class="border"></td>';
                          }else {
                              $summary_typewise.='<td class=" border amount">'.sprintf("%.2f",$rows->abs).'</td>';
                          }
                          if(empty($per) || $per==0)
                          {
                              $summary_typewise.='<td class="border"></td>';
                          }
                          else {
                            $summary_typewise.='<td class="border amount">'.sprintf("%.2f",$per).' %'.'</td>';
                          }
                        $summary_typewise.='</tr>';
                          // echo $i;

                  if($j==$nums_rows)
                  {
    $summary_typewise.='<tr>
                            <td class=" dataTotal border ">Total :-</td>
                            <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($pur_total)).'</td>
                            <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($cur_value_total)).'</td>';
                            if($div_r2_total==0|| empty($div_r2_total))
                            {
                              $summary_typewise.='<td class="border"></td>';
                            } else{
                              $summary_typewise.='<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($div_r2_total)).'</td>';
                            }
                            if($div_payout_total==0|| empty($div_payout_total))
                            {
                              $summary_typewise.='<td class="border"></td>';
                            } else{
                                $summary_typewise.='<td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($div_payout_total)).'</td>';
                            }
                      $summary_typewise.='
                            <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_total)).'</td>
                            <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($profit_total)).'</td>
                            <td class=" dataTotal border amount">'.round($fTotalCagr,2).'</td>
                            <td class=" dataTotal border amount">'.round($fTotal_abs,2).'</td>
                            <td class=" dataTotal border amount">100 %</td>
                        </tr>
                      </table><br>';
                        }
             }
          $html.=$summary_typewise;
}
          // echo "<pre>";
          // print_r($mf_comman_scheme_summary);
          // echo "</pre>";
if($mf_comman_scheme_summary)
{

      $title1='<div class="title">Scheme Wise Summary of '.$familyName.' family'.' </div><br>';
      $common_scheme_summ='<br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';
      $common_scheme_summ.='<tr><td colspan="17"  >'.$title1.'</td></tr>';
      $common_scheme_summ.='<tr  nobr="true" class="head-row">
                              <th width="240">Scheme Name</th>
                              <th width="100">Purchase Amount</th>
                              <th width="100">Div Amount</th>
                              <th width="80">Live Unit</th>
                              <th width="80">Current Value</th>
                              <th width="80">Div R</th>
                              <th width="80">Div Payout</th>
                              <th width="80">Total</th>
                              <th width="40">CAGR</th>
                              <th width="40">Abs. Rtn</th>
                              <th width="60">Allocation</th>
                          </tr>';
          $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
              // $client=$mf_comman_scheme_summary[$i]->client_name;
              foreach ($mf_comman_scheme_summary as $rs)
              {
                // echo $inv_cost_total;
                $total=0;
                $total=$rs->current_value+$rs->div_payout;
                $per=0;
                $per=round(($total/$fTotal)*100,2);

                // $client=$mf_comman_scheme_summary[$i]->client_name;
                 $common_scheme_summ.='<tr>
                             <td class=" border "  style="text-align:left">'.$rs->mf_scheme_name.'</td>';
                            if(empty($rs->purchase_amount) || $rs->purchase_amount==0)
                            {
                              $common_scheme_summ.='<td class="border"></td>';
                             } else {
                              $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase_amount)).'</td>';
                             }
                             if(empty($rs->div_amount) || $rs->div_amount==0) {
                               $common_scheme_summ.='<td class="border"></td>';
                              } else {
                               $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_amount)).'</td>';
                              }
                              $common_scheme_summ.='<td class="border amount">'.sprintf("%.4f",$rs->live_unit).'</td>
                                                    <td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->current_value)).'</td>';
                             if(empty($rs->div_r2) || $rs->div_r2==0)
                             {
                               $common_scheme_summ.='<td class="border"></td>';
                              //  $common_scheme_summ.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_payout)).'</td>';
                             }
                             else {
                               $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_r2)).'</td>';
                                // $common_scheme_summ.='<td class="border"></td>';
                             }
                            //  <td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_r2)).'</td>';
                             if(empty($rs->div_payout) || $rs->div_payout==0)
                             {
                               $common_scheme_summ.='<td class="border"></td>';
                              //  $common_scheme_summ.='<td class="border">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->div_payout)).'</td>';
                             }
                             else {
                               $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_payout)).'</td>';
                                // $common_scheme_summ.='<td class="border"></td>';
                             }
                            //  $total=0;
                            //  if($rs->div_payout)
                            //      $total = round($rs->div_payout + $rs->current_value, 2);
                            //  else
                            //      $total = round($rs->current_value, 2);
                             $common_scheme_summ.='<td class=" border amount">'.$this->common_lib->moneyFormatIndia(round($total )).'</td>
                             <td class="border amount">'.sprintf("%.2f",$rs->cagr).'</td>
                             <td class="border amount">'.sprintf("%.2f",$rs->abs).'</td>';
                            //  if($per==0 || empty($per))
                            //  {
                            //      $common_scheme_summ.='<td class="border amount"></td></tr>';
                            //  }
                            //  else {
                               $common_scheme_summ.='<td class=" border amount ">'.sprintf("%.2f",$per).' %'.'</td></tr>';
                             //}
                         $f_pur_amt=$f_pur_amt+$rs->purchase_amount;
                         $f_div_amount=$f_div_amount+$rs->div_amount;
                         $f_live_unit=$f_live_unit+$rs->live_unit;
                         $f_current_value=$f_current_value+$rs->current_value;
                         $f_div_r2=$f_div_r2+$rs->div_r2;
                         $f_div_payout=$f_div_payout+$rs->div_payout;
                         $f_total=$f_total+$total;
                  }
                  $common_scheme_summ.='<tr>
                              <td class="border dataTotal"  colspan="">Total</td>
                              <td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_pur_amt)).'</td>';

                              if(empty($f_div_amount)|| $f_div_amount==0)
                              {
                                $common_scheme_summ.='<td class="border dataTotal"></td>';
                              }else {
                                $common_scheme_summ.='<td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_div_amount)).'</td>';
                              }
                              $common_scheme_summ.='<td class="border dataTotal amount">'.$f_live_unit.'</td>
                                                    <td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_current_value)).'</td>';
                              if(empty($f_div_r2)|| $f_div_r2==0)
                              {
                                $common_scheme_summ.='<td class="border dataTotal"></td>';
                              }else {
                                $common_scheme_summ.='<td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_div_r2)).'</td>';
                              }


                              if(empty($f_div_payout)|| $f_div_payout==0)
                              {
                                $common_scheme_summ.='<td class="border dataTotal"></td>';
                              }else {
                                $common_scheme_summ.='<td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_div_payout)).'</td>';
                              }

                    $common_scheme_summ.='<td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($f_total )).'</td>
                              <td class="dataTotal border amount">'.round($fTotalCagr,2).'</td>
                              <td class="dataTotal border amount">'.round($fTotal_abs,2).'</td>
                              <td class="dataTotal border amount">100%</td>
                              </tr></table><br>';
      $html.=$common_scheme_summ;
}

// print_r($mf_summary_net_investment);
if($mf_summary_net_investment)
{

          $heading_netinvestment='<br><div class="title">Net investment of '.$mf_rep_data[0]->family_name.' Family</div>';
          $net_investmet='';
            $net_investmet.='<table  border="0" cellpadding="4" width="600px"  style="text-align:left" class="col-md-6" >
                          <tr style="border:0"><td  colspan="2">'.$heading_netinvestment.'</td></tr><tr><td colspan="2"><br></td></tr>      ';
          $net_investmet.='<tr  nobr="true" class="head-row">
                                  <th class="border">Particular</th>
                                  <th class="border">Amount</th></tr> ';

            foreach ($mf_summary_net_investment as $rs)
            {
              $net_investmet.='<tr>
                                  <td class="text-left border" style="padding-left:5px"> A) Total Investment </td>
                                  <td class="border amount" style="margin-left:10px;">'.$this->common_lib->moneyFormatIndia(round($rs->purchase)).'</td>
                              </tr>
                              <tr>
                                  <td class="text-left border" style="padding-left:5px"> B) Total Redemption </td>
                                  <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->redemption)).'</td>
                            </tr>
                            <tr>
                                <td class="text-left border" style="padding-left:5px"> C) Dividend Payout </td>';
                                if($rs->payout==0||empty($rs->payout))
                                {
                                      $net_investmet.='<td class="border amount"></td>';
                                }else {
                                      $net_investmet.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->payout)).'</td>';
                                }

                  $net_investmet.='</tr>
                              <tr>
                                <td class="text-left border" style="padding-left:5px"> D) Net Investment  (A-B-C) </td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase-$rs->payout-$rs->redemption)).'</td>
                             </tr>
                              <tr>
                                <td class="text-left border" style="padding-left:5px"> E) Current Value</td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->current_value)).'</td>
                              </tr>
                              <tr>
                                <td class="text-left border" style="padding-left:5px">  F) Net Gain (E-D)</td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round( ($rs->current_value-(($rs->purchase-$rs->payout)-$rs->redemption)))).'</td>
                              </tr>
                          </table></br></br>';
           $html.=$net_investmet;
         }
   }

?>
