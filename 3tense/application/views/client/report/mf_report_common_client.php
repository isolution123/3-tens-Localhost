<?php
if($mf_summary_typewise)
{
  $typewise='<div class="title">Fund Type Wise Summary of '.$clientName.' </div><br>';
  $summary_typewise='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;width:99.9%">';
  $summary_typewise.='<tr><td colspan="17"  >'.$typewise.'</td></tr>';
  $summary_typewise.='<tr  nobr="true" class="head-row">
                        <th width="180">Fund Types</th>
                          <th width="88">Purchase Amount</th>
                          <th width="88">Current Value</th>
                          <th width="88">Div R</th>
                          <th width="88">Div Payout</th>
                          <th width="88">Total</th>
                          <th width="88">Gain / Loss</th>
                          <th width="88">CAGR</th>
                          <th width="88">ABS</th>
                          <th width="88">Allocation</th>
                      </tr>';

      $nums_rows=sizeof ($mf_summary_typewise);
      $j=0;$inv_cost_total=0;$cur_value_total=0;$cagr_total=0;$abs_total=0;
      $div_payout_total=0;$div_r2_total=0;$profit_total=0;$total=0;$per=0;
      $total_total=0;$pur_total=0;
   foreach ($mf_summary_typewise as $rows)
      {
        $j=$j+1;
        $total=$rows->current_value+$rows->payout;
        $purchase=$rows->purchase_amount;
        // echo $purchase;
        $profit=$total-$purchase;
        $per=($total/$fTotal)*100;
        $profit_total=$profit_total+$profit;
        $div_r2_total=$div_r2_total+$rows->div_r2;
        $div_payout_total=$div_payout_total+$rows->payout;
        $cur_value_total=$cur_value_total+$rows->current_value;
        $total_total=$total_total+$total;
        $pur_total=$pur_total+$purchase;
        // $cagr_total=$cagr_total+$rows->cagr;
        // $abs_total=$abs_total+$rows->abs;
        $summary_typewise.='<tr>
                    <td class=" border" style="text-align:center;">'.$rows->scheme_type.'</td>';
                    // $payoutt=$rows->payout;
                    if($purchase==0)
                    {
                      $summary_typewise.='<td class="border" ></td>';
                    }
                    else{
                      $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($purchase)).'</td>';
                    }
                    if($rows->current_value==0)
                    {
                      $summary_typewise.='<td class="border amount"></td>';
                    }
                    else{
                      $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->current_value)).'</td>';
                    }
                    if($rows->div_r2==0)
                    {
                      $summary_typewise.='<td class="border amount"></td>';
                    }
                    else{
                      $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2 )).'</td>';
                    }

                    if($rows->payout==0)
                    {
                      $summary_typewise.='<td class="border amount"></td>';
                    }
                    else{
                      $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rows->payout)).'</td>';
                    }

                    if(!empty($total))
                    {
                    $summary_typewise.='<td class=" border amount ">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>';
                    } else {
                      $summary_typewise.='<td class=" border amount "></td>';
                    }
                    if($profit==0)
                    {
                      $summary_typewise.='<td class="border amount"></td>';
                    }
                    else {
                        $summary_typewise.='<td class=" border amount ">'.$this->common_lib->moneyFormatIndia(round($profit)).'</td>';
                    }
                    if($rows->cagr==0)
                    {
                        $summary_typewise.='<td class="border amount"></td>';
                    }
                    else {
                        $summary_typewise.='<td class=" border amount ">'.sprintf("%.2f",$rows->cagr).'</td>';
                    }
                    if($rows->abs==0)
                    {
                        $summary_typewise.='<td class="border amount"></td>';
                    }
                    else {
                        $summary_typewise.='<td class=" border amount ">'.sprintf("%.2f",$rows->abs).'</td>';
                    }
                    if($per==0 || empty($per))
                    {
                        $summary_typewise.='<td class="border amount"></td>';
                    }
                    else {
                      $summary_typewise.='<td class=" border amount ">'.sprintf("%.2f",$per).' %'.'</td>';
                    }
                  $summary_typewise.='</tr>';
                    // echo $i;
            if($j==$nums_rows)
            {
              $summary_typewise.='<tr>
                      <td class=" dataTotal border" style="text-align:center;"> Total :-</td>
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($pur_total)).'</td>
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($cur_value_total)).'</td>';
                      if($div_r2_total==0 || empty($div_r2_total))
                      {
                        $summary_typewise.='<td class=" dataTotal border amount "></td>';
                      }else {
                      $summary_typewise.='<td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($div_r2_total)).'</td>';
                      }
                      if($div_payout_total==0 || empty($div_payout_total))
                      {
                        $summary_typewise.='<td class=" dataTotal border amount "></td>';
                      }else {
                      $summary_typewise.='<td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($div_payout_total)).'</td>';
                      }
                      $summary_typewise.='
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($total_total)).'</td>
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($profit_total)).'</td>
                      <td class=" dataTotal border amount ">'.round($TotalCagr,2).'</td>
                      <td class=" dataTotal border amount ">'.round($Total_abs,2).'</td>
                      <td class=" dataTotal border amount ">100 %</td>
                  </tr>
                </table><br>';
                  }
       }
    $html.=$summary_typewise;
 }


if($mf_summary_net_investment)
{
    try
    {
                  $net_investmet='';
                  $heading_netinvestment='';
        
                

            foreach ($mf_summary_net_investment as $rs)
            {
                
                    if(!$clientName)
                    {
                        $clientName=$mf_rep_data[0]->client_name;
                    }
                
                    $heading_netinvestment='<div class="title col-md-12" >Net investment of '.$clientName.'</div><br><br>';
                    $net_investmet=$heading_netinvestment;
                    $net_investmet.='<table border="1" cellpadding="4" class="col-md-12"  >';
                $net_investmet.='<tr>
                  <td class="red_color" style="border:2px solid white;background-color: green;color: white;text-align: center;font-weight: bolder;padding: 10px;" > Investment<br>'.$this->common_lib->moneyFormatIndia(round($rs->purchase)).'
                  </td>
                  
                  <td class="amount" style="border:2px solid white;background-color:green;color: white;text-align: center;font-weight: bolder;padding: 10px;">Switch In <br>'.$this->common_lib->moneyFormatIndia(round($rs->swi)).'
                  </td>
                  
                  <td class="amount" style="border:2px solid white;background-color: red;color: white;text-align: center;font-weight: bolder;padding: 10px;">  Redemption <br>'.$this->common_lib->moneyFormatIndia(round($rs->redemption)).'
                  </td>
                  
                  <td  style="border:2px solid white;background-color: red;color: white;text-align: center;font-weight: bolder;padding: 10px;">Switch Out<br>'.$this->common_lib->moneyFormatIndia(round($rs->swo)).'
                  </td>
                  <td  style="border:2px solid white;background-color: red;color: white;text-align: center;font-weight: bolder;padding: 10px;">Div. Payout<br>';
                   if($rs->payout==0||empty($rs->payout))
                                {
                                      $net_investmet.='';
                                }else {
                                      $net_investmet.=$this->common_lib->moneyFormatIndia(round($rs->payout));
                                }
                  $net_investmet.='</td>
                  <td class="amount" style="border:2px solid white;background-color: #4f8edc;color: white;text-align: center;font-weight: bolder;padding: 10px;">Net Investment<br>'.$this->common_lib->moneyFormatIndia(round(($rs->purchase+$rs->swi)- ($rs->payout + $rs->redemption+$rs->swo))).'
                  </td>
                  <td class="amount" style="border:2px solid white;background-color: #4f8edc;color: white;text-align: center;font-weight: bolder;padding: 10px;">Current Value<br>'.$this->common_lib->moneyFormatIndia(round($rs->current_value)).'
                  </td>
                  <td class="amount" style="min-width: 100px;width: 100px;border:2px solid white;background-color: #4f8edc;color: white;text-align: center;font-weight: bolder;padding: 10px;">Net Gain<br>'.$this->common_lib->moneyFormatIndia(round($rs->current_value-(($rs->purchase+$rs->swi)- ($rs->payout + $rs->redemption+$rs->swo)))).'
                  </td>';
                  if($TotalCagrForXirr>$rs->xirr_value)
                  {
                      $rs->xirr_value=$TotalCagrForXirr;
                  }
                   $net_investmet.='<td class="amount" style="border:2px solid white;background-color: #4f8edc;color: white;text-align: center;font-weight: bolder;padding: 10px;">XIRR<br>'.$rs->xirr_value.'
                  </td>
                  </tr>';
                
           $html.=$net_investmet;
         }
    }
    catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
   }


$html .= '</tbody>
</table>';
?>
