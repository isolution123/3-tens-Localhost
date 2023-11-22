<?php
if($mf_summary_typewise)
{
    
    if(isset($familyName))
    {$clientName=$familyName;}
    
  $typewise='<div class="title">Fund Type Wise Summary of '.$clientName.' </div><br>';
  $summary_typewise='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
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
        $total=round($rows->current_value+$rows->payout,2);
        $purchase=$rows->purchase_amount;
        // echo $purchase;
        $profit=$total-$purchase;
        $per=($total/$fTotal)*100;
        $profit_total=round($profit_total+$profit,2);
        $div_r2_total=$div_r2_total+$rows->div_r2;
        $div_payout_total=$div_payout_total+$rows->payout;
        $cur_value_total=$cur_value_total+$rows->current_value;
        $total_total=$total_total+$total;
        $pur_total=$pur_total+$purchase;
        // $cagr_total=$cagr_total+$rows->cagr;
        // $abs_total=$abs_total+$rows->abs;
        $summary_typewise.='<tr>
                    <td class=" border">'.$rows->scheme_type.'</td>';
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
                      $summary_typewise.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rows->current_value)).'</td>';
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
                      <td class=" dataTotal border ">Total :-</td>
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(round($pur_total)).'</td>
                      <td class=" dataTotal border amount ">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$cur_value_total)).'</td>';
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
                      <td class=" dataTotal border ">100 %</td>
                  </tr>
                </table><br>';
                  }
       }
    $html.=$summary_typewise;
 }

if($mf_comman_scheme_summary)
{
  // print_r($mf_comman_scheme_summary);
  $heading='<div class="title">Scheme Wise Summary of '.$clientName.' </div><br>';
  $common_scheme_summ='<br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';
  $common_scheme_summ.='<tr><td colspan="17"  >'.$heading.'</td></tr>';
  $common_scheme_summ.='<tr  nobr="true" class="head-row">
                          <th width="180">Scheme Name</th>
                          <th width="80">Purchase Amount</th>
                          <th width="80">Div Amount</th>
                          <th width="80">Live Unit</th>
                          <th width="80">Current Value</th>
                          <th width="80">Div R</th>
                          <th width="80">Div Payout</th>
                          <th width="80">Total</th>
                          <th width="80">CAGR</th>
                          <th width="80">ABS</th>
                          <th width="80">Allocation</th>
                      </tr>';

          $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
          foreach ($mf_comman_scheme_summary as $rs)
          {
            $per=0;
            $total=0;
            $total=round($rs->current_value+$rs->div_payout,2);
            $per=round(($total/$fTotal)*100,2);
            $common_scheme_summ.='<tr>
                        <td class=" border"  style="text-align:left">'.$rs->mf_scheme_name.'</td>';
                        if(empty($rs->purchase_amount) || $rs->purchase_amount==0)
                       {
                         $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase_amount)).'</td>';
                        }
                        if($rs->div_amount==0 || empty($rs->div_amount))
                        {
                          $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_amount)).'</td>';
                        }
                        $common_scheme_summ.='
                        <td class="border amount">'.sprintf("%.4f",$rs->live_unit).'</td>
                        <td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->current_value)).'</td>';
                        if($rs->div_r2==0 || empty($rs->div_r2))
                        {
                          $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_r2)).'</td>';
                        }
                        if($rs->div_payout==0 || empty($rs->div_payout))
                        {
                          $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_payout)).'</td>';
                        }
                $common_scheme_summ.='<td class=" border amount ">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>
                        <td class=" border amount ">'.sprintf("%.2f",$rs->cagr).'</td>
                        <td class=" border amount ">'.sprintf("%.2f",$rs->abs).'</td>';
                        if($per==0 || empty($per))
                        {
                            $common_scheme_summ.='<td class="border amount"></td></tr>';
                        }
                        else {
                          $common_scheme_summ.='<td class=" border amount ">'.sprintf("%.2f",$per).' %'.'</td></tr>';
                        }

                    $f_pur_amt=$f_pur_amt+$rs->purchase_amount;
                    $f_div_amount=$f_div_amount+$rs->div_amount;
                    $f_live_unit=$f_live_unit+$rs->live_unit;
                    $f_current_value=$f_current_value+$rs->current_value;
                    $f_div_r2=$f_div_r2+$rs->div_r2;
                    $f_div_payout=$f_div_payout+$rs->div_payout;
                    $f_total=$f_total+$total;
              }
              $common_scheme_summ.='<tr>
                          <td class="border dataTotal">Total</td>
                          <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_pur_amt)).'</td>';
                          if($f_div_amount==0 ||empty($f_div_amount))
                          {
                          $common_scheme_summ.='<td class="border amount dataTotal"></td>';
                          }else {
                          $common_scheme_summ.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_amount)).'</td>';
                          }

                          $common_scheme_summ.='<td class="border amount dataTotal">'.sprintf("%.4f",$f_live_unit).'</td>
                                                <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$f_current_value)).'</td>';
                          if($f_div_r2==0 ||empty($f_div_r2))
                          {
                          $common_scheme_summ.='<td class="border amount dataTotal"></td>';
                          }else {
                          $common_scheme_summ.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_r2)).'</td>';
                          }
                          if($f_div_payout==0 ||empty($f_div_payout))
                          {
                          $common_scheme_summ.='<td class="border amount dataTotal"></td>';
                          }else {
                          $common_scheme_summ.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_payout)).'</td>';
                          }
                  $common_scheme_summ.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_total )).'</td>
                          <td class=" dataTotal border amount ">'.round($TotalCagr,2).'</td>
                          <td class=" dataTotal border amount ">'.round($Total_abs,2).'</td>
                          <td class=" dataTotal border amount ">100 %</td>
                          </tr></table><br>';

          $html.=$common_scheme_summ;
}


if($mf_summary_net_investment)
{
    try
    {
                  $net_investmet='';
                  $heading_netinvestment='';
        
                

            foreach ($mf_summary_net_investment as $rs)
            {
                    if($rs->brokerID=='0004' || $rs->brokerID=='0009' || $rs->brokerID=='0174' || $rs->brokerID=='0196')
                {
                    if(!$clientName)
                    {
                        $clientName=$mf_rep_data[0]->client_name;
                    }
                
                    $heading_netinvestment='<div class="title col-md-12" >Net investment of '.$clientName.'</div><br><br>';
                    $net_investmet=$heading_netinvestment;
                    $net_investmet.='<table border="1" cellpadding="4" class="col-md-12" >';
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
                }
                else
                {
                    $heading_netinvestment='<div class="title col-md-4" width="300px">Net investment of '.$mf_rep_data[0]->client_name.'</div><br>';
                    $net_investmet=$heading_netinvestment;
                    $net_investmet.='<br><table border="1" cellpadding="4" width="300px" style="" class="col-md-4" >';
    
                    $net_investmet.='<tr  nobr="true" class="head-row">
                                          <th class="border">Particular</th>
                                          <th class="border">Amount</th></tr> ';
                    $net_investmet.='<tr>
                                  <td class=text-left border> A) Total Investment </td>
                                  <td class="border amount" style="margin-left:10px;">'.$this->common_lib->moneyFormatIndia(round($rs->purchase)).'</td>
                              </tr>
                              <tr>
                                  <td class=text-left border > B) Total Redemption </td>
                                  <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->redemption)).'</td>
                            </tr>
                            <tr>
                                <td class=text-left border> C) Dividend Payout </td>';
                                if($rs->payout==0||empty($rs->payout))
                                {
                                      $net_investmet.='<td ></td>';
                                }else {
                                      $net_investmet.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->payout)).'</td>';
                                }

                  $net_investmet.='</tr>
                              <tr>
                                <td class=text-left border> D) Net Investment  (A-B-C) </td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase - $rs->payout - $rs->redemption)).'</td>
                             </tr>
                              <tr>
                                <td class=text-left border> E) Current Value</td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->current_value)).'</td>
                              </tr>
                              <tr>
                                <td class=text-left border> F) Net Gain (E-D)</td>
                                <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->current_value-($rs->purchase - $rs->payout - $rs->redemption))).'</td>
                              </tr>
                          </table></br></br>';
                }
           $html.=$net_investmet;
         }
    }
    catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
  die();
}
   }

if($mf_comman_scheme_summary)
{
   $amc_wise_summary = array();
   
   foreach ($mf_comman_scheme_summary as $rs)
   {
       $scheme_parts = explode(' ',$rs->mf_scheme_name);
       
       $scheme_group = strtoupper($scheme_parts[0]);
       $scheme = strtoupper($rs->mf_scheme_name);
       
       if($scheme_group == 'ADITYA' || $scheme_group == 'BSR' || $scheme_group == 'BSL' || $scheme_group == 'ABSL')
       {
           $scheme_group = 'BIRLA';
       }
       
       if($scheme == 'L&T' || $scheme == 'L & T' || strpos($scheme, 'L & T') !== false || strpos($scheme, 'L&T') !== false)
       {
           $scheme_group = 'L&T';
       }
       
       if($scheme == 'FIXED TENURE' || $scheme == 'FT FIXED' || $scheme_group == 'TEMPLETON')
       {
           $scheme_group = 'FRANKLIN';
       }
       
       //var_dump(array_key_exists($scheme_group,$amc_wise_summary));
       if(array_key_exists($scheme_group,$amc_wise_summary))
       {
           if($rs->purchase_amount != '')
           {
               $amc_wise_summary[$scheme_group]['purchase_amount'] = $amc_wise_summary[$scheme_group]['purchase_amount'] + $rs->purchase_amount;
           }
           if($rs->current_value != '')
           {
               $amc_wise_summary[$scheme_group]['current_value'] = $amc_wise_summary[$scheme_group]['current_value'] + $rs->current_value;
           }
           
           $total=round($rs->current_value+$rs->div_payout,2);
           $per=round(($total/$fTotal)*100,2);
           
           if($per != '')
           {
                $amc_wise_summary[$scheme_group]['allocation'] = $amc_wise_summary[$scheme_group]['allocation'] + $per;
           }
       }
       else
       {
           
           $purchase_amount =  $rs->purchase_amount;
           $current_value = $rs->current_value;
           
           $total=round($rs->current_value+$rs->div_payout,2);
           $per=round(($total/$fTotal)*100,2);
           //$amc_wise_summary[$scheme_group]['purchase_amount'] = $rs->purchase_amount;
           //$amc_wise_summary[$scheme_group]['current_value'] = $rs->current_value;
           
           $amc_wise_summary[$scheme_group] = array('purchase_amount'=>$purchase_amount,'current_value'=>$current_value,'allocation'=>$per);
           
         //  echo '<pre>';print_r($amc_wise_summary);
       }
   }
   //echo '<pre>';print_r($amc_wise_summary);die;
  $heading='<div class="title" style="margin-top:20px;">AMC Wise Summary of '.$clientName.' </div><br>';
  $common_scheme_summ='<br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';
  $common_scheme_summ.='<tr><td colspan="10"  >'.$heading.'</td></tr>';
  /*$common_scheme_summ.='<tr  nobr="true" class="head-row">
                          <th width="50">AMC Name</th>
                          <th width="100">Purchase Amount</th>
                          <th width="100">Current Value</th>
                          <th width="80">Allocation</th>
                      </tr>';*/
  $common_scheme_summ.='<tr  nobr="true" class="head-row">
                          <th width="50">AMC Name</th>
                          <th width="100">Current Value</th>
                          <th width="80">Allocation</th>
                      </tr>';

          $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
          $total_current_value = 0;
          $total_allocation = 0;
          foreach ($amc_wise_summary as $key => $rs)
          {
              //echo '<pre>';print_r($rs);die;
            $per=0;
            $total=0;
            //$total=round($rs->current_value+$rs->div_payout,2);
            //echo $fTotal;
            //$per=round(($total/$fTotal)*100,2);
            $common_scheme_summ.='<tr>
                        <td class=" border"  style="text-align:left">'.$key.'</td>';
                       /*if(empty($rs['purchase_amount']) || $rs['purchase_amount']==0)
                       {
                         $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['purchase_amount'])).'</td>';
                        }*/
                        
                       if(empty($rs['current_value']) || $rs['current_value']==0)
                       {
                         $common_scheme_summ.='<td class="border amount"></td>';
                        } else {
                          $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['current_value'])).'</td>';
                          $total_current_value = $total_current_value + round($rs['current_value']);
                        }
                        
                       if($rs['allocation']==0 || empty($rs['allocation']))
                        {
                            $common_scheme_summ.='<td class="border amount"></td></tr>';
                        }
                        else {
                          $common_scheme_summ.='<td class=" border amount ">'.sprintf("%.2f",$rs['allocation']).' %'.'</td></tr>';
                        }
                        
                        //$common_scheme_summ.='<td class="border amount"></td>';
                        //$common_scheme_summ.='<td class="border amount"></td>';

                   
              }
              
              //amc wise summary total
              $common_scheme_summ.='<tr>
                        <td class=" border dataTotal" >Total</td>
                        <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$total_current_value)).'</td>'
                      . '<td class=" dataTotal border amount" >100%</td></tr></table>';
             
          $html.=$common_scheme_summ;
}



$html .= '</tbody>
</table>';
?>
