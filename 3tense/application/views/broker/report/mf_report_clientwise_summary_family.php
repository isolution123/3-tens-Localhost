<!--Dipak -->

<?php
error_reporting(E_ALL);
if(empty($mf_rep_data))
{
  echo "<script type='text/javascript'>
      alert('Unauthorized Access. Get Outta Here!');
      window.top.close();  //close the current tab
    </script>";
} else {
  $clientName = '';$perTotal=0; $purAmt = 0; $divAmt = 0; $total = 0; $cagr1 = 0; $cagr2 = 0; $abs1 = 0;
  //total of schemes for a particular client
  $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
  $sTotal = 0; $sTotalCagr = 0; $sTotalCagrDivide = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;$TotalCagrForXirr=0;
  //total of all schemes of a client
  $gTotalPurAmt = 0;  $gTotalDivAmt = 0;  $gTotalPurNav = 0; $gTotalPurNavDivide = 0; $gTotalLiveUnit = 0;  $gTotalCurValue = 0;  $gTotalDivR = 0; $gTotalDivPay = 0;
  $gTotal = 0; $invested_cost_total=0; $fTotalCagr = 0;  $fTotal_abs = 0; $fTotalCagr1 = 0; $fTotalCagr2 = 0; $gTotalAbs1 = 0; $gLiveUnit = 0;
  $sParNavindex=0;$gTotalPurNav=0;$gPurNavIndex=0;$per=0;
  // echo"<pre>" ;
  // print_r($mf_rep_data);
  if(!empty($mf_rep_data))
        $clientName = $mf_rep_data[0]->client_name;
        $familyName=$mf_rep_data[0]->family_name;
  // $schemeName = $mf_rep_data[0]->mf_scheme_name;
  $inv_chart = array();
  $cur_chart = array();
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
  $title = '<div class="title row">Client Wise Summary Mutual Fund Portfolio of '.$familyName.' Family</div><br/>';
  // add client info to page
  $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
          <tbody>';
  $header = true; $content = false; $footer = false;$schemeFoot = false;$newClient=true;

  foreach ($mf_rep_data as $rss)
     {
       $invested_cost_total=$invested_cost_total+$rss->purchase_amount;
     }
  $i=0;
  $clientName= $mf_rep_data[$i]->client_name;
  // $i=0;
  foreach($mf_rep_data as $row)
  {

      // if($clientName == $row->mf_scheme_name)
      //  {
      //    $footer = false;
      //    $content=false;
      //  }
      // else
      // {
      //    $footer = true;
      //  }


      if($clientName != $mf_rep_data[$i]->client_name)
      {
          $schemeFoot = true;
          $newClient  = true;
          $familyFoot = true;
          $header=true;
          $content=false;
          $footer=false;
      }
      if($schemeFoot)
      {
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

              // echo $sParNavindex."<br>";
              // // echo $gTotalPurNav."<br>";
              // $gPurNavIndex=$gPurNavIndex+1;
              // // echo $gPurNavIndex;
              // // echo "nav before".$sTotalPurNav."<br>";
              // $sTotalPurNav=$sTotalPurNav/$sParNavindex;
              // $gTotalPurNav=$gTotalPurNav+$sTotalPurNav;
              // echo "nav ".$sTotalPurNav."<br>";
              // echo $sTotalPurNav."__".$gTotalPurNav."<br>";
          if(!empty($sTotalPurNav))
              $html .= '<td class="dataTotal border"></td>';
          else
              $html .= '<td class="border normal"></td>';

              $html .='<td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>';
               if($hide_nav_date!='hide_nav_date')    
                    {
                  $html .='<td class="border amount normal" colspan="3"></td>';
                  }
                  else
                  {
                      $html .='<td class="border amount normal" colspan="2"></td>';
                  }
          if(!empty($sTotalCurValue))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalCurValue).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivR))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivR).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivPay))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivPay).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotal).'</td>';
          if(!empty($sTotalCagr))
              $html .= '<td class="dataTotal border amount">'.$sTotalCagr.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotal_abs))
              $html .= '<td class="dataTotal border amount">'.$sTotal_abs.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
              // $html .= '<td class="border amount dataTotal">'.$perTotal.' %'.'</td>';
              // $html .= '<td class="border amount normal"></td>';
          $html .= '</tr>
          <tr>
              <td colspan="16" class="no-border info">
                  Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) Rs.'.$this->common_lib->moneyFormatIndia($sTotal - $sTotalPurAmt).'. Percentage of holding '.$perTotal. ' %
              </td>
          </tr>';
          $schemeFoot = false;
          $sTotalPurAmt = 0;$sParNavindex=0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
          $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
          $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
          $perTotal=0;
      }
      if($newClient)
      {
        $html .= '<tr nobr="true">
                    <td class="no-border client-name2" colspan="4"><h4>'.$mf_rep_data[$i]->client_name.'</h4></td>
                </tr>';
        $newClient = false;
        }
       if($header)
       {
            $html .= '<tr nobr="true" class="head-row">';
            if($hide_nav_date!='hide_nav_date')    
                    {
                      $html .= '      <th width="70">Scheme Name</th>';
                    }
                    else
                    {
                          $html .= '      <th width="135">Scheme Name</th>';
                    }
                    
                            $html .= '<th width="60">Folio Number</th>
                            <th width="65">Inception Date</th>
                            <th width="45">Scheme Type</th>
                            <th width="70">Purchase Amount</th>
                            <th width="70">Div Amount</th>
                            <th width="48">Purchase NAV</th>
                            <th width="60">No. of Unit</th>
                            <th width="35">Trans Day</th>
                            <th width="47">Current NAV</th>';
                            if($hide_nav_date!='hide_nav_date')    
                    {
                            $html .= '<th width="65">Current NAV Date</th>';
                    }
                            $html .= '<th width="70">Current Value</th>
                            <th width="60">Div R</th>
                            <th width="60">Div Payout</th>
                            <th width="75">Total</th>
                            <th width="40">CAGR</th>
                            <th width="40">ABS</th>
                        </tr>
                    </tbody>
                    <tbody>';
            $header = false;
            $content = true;
        }
      if($content)
        {

            //set purchase amount
            // if($row->mf_scheme_type != 'DIV')
            // {
            //     $purAmt = round($row->live_unit * $row->p_nav);
            //     $divAmt = 0;
            // }
            // else {
            //     $purAmt = 0;
            //     $divAmt = round($row->live_unit * $row->p_nav);
            // }


             $purAmt=round($row->purchase_amount);
             $divAmt=round($row->div_amount);

             $per=round(($purAmt/$invested_cost_total)*100,2);
             $perTotal=$perTotal+$per;
            //  $divAmt=0;
            //set total value

            if($row->div_payout)
                $total = round($row->div_payout + $row->current_value, 2);
            else
                $total = round($row->current_value, 2);
            //set cagr1(temp)
            if($row->cagr1 != null)
                $cagr1 = $row->cagr1;//(($purAmt+$divAmt) * $row->cagr) * $row->transaction_day;
            else
                $cagr1 = 0;
            if($row->mf_abs != null)
            {
                $cagr2 =$row->cagr2;//($purAmt+$divAmt)  * $row->transaction_day;
                $abs1 = ($purAmt+$divAmt)  * $row->mf_abs;
            } else {
                $abs1 = 0;
                $cagr2 = 0;
            }
            //set sub totals
            $sTotalPurAmt = round($purAmt + $sTotalPurAmt);
            $gTotalPurAmt = round($purAmt + $gTotalPurAmt);
            $sTotalDivAmt = round($divAmt + $sTotalDivAmt);
            $gTotalDivAmt = round($divAmt + $gTotalDivAmt);
            $sLiveUnit = $row->live_unit + $sLiveUnit;
            $gLiveUnit = $row->live_unit + $gLiveUnit;

            // $sTotalPurNav=$sTotalPurNav+$row->p_nav;
            // $sParNavindex=$sParNavindex+1;
            if($row->live_unit != null && $row->live_unit != 0)
            {

              $sTotalPurNav = round((($sTotalPurAmt + $sTotalDivAmt) / $sLiveUnit), 2);
              $gTotalPurNav = round((($gTotalPurAmt + $gTotalDivAmt) / $gLiveUnit), 2);
              // $fTotalPurNav = round(($fTotalPurAmt / $fLiveUnit), 2);
              // $sTotalPurNav = round($mf_rep_data[$i]->p_nav, 2);
              // $gTotalPurNav = round($gTotalPurNav + $mf_rep_data[$i]->p_nav, 2);
              // $fTotalPurNav = round($fTotalPurNav + $mf_rep_data[$i]->p_nav, 2);
              // $sTotalPurNavDivide++;
              // $gTotalPurNavDivide++;

            }
            if(empty($row->div_r2)) {
                $row->div_r2 = 0;
            }
            if(empty($row->div_payout)) {
                $row->div_payout = 0;
            }
            $sTotalCagr1 = $cagr1 + $sTotalCagr1;
            $fTotalCagr1 = $cagr1 + $fTotalCagr1;
            $sTotalCagr2 = $cagr2 + $sTotalCagr2;
            $fTotalCagr2 = $cagr2 + $fTotalCagr2;
            $sTotalAbs1 = $abs1 + $sTotalAbs1;
            $gTotalAbs1 = $abs1 + $gTotalAbs1;
            $sTotalLiveUnit = $row->live_unit + $sTotalLiveUnit;
            $gTotalLiveUnit = $row->live_unit + $gTotalLiveUnit;
            $sTotalCurValue = round($row->current_value + $sTotalCurValue, 2);
            $gTotalCurValue = round($row->current_value + $gTotalCurValue, 2);
            $sTotalDivR = round($row->div_r2 + $sTotalDivR);
            $gTotalDivR = round($row->div_r2 + $gTotalDivR);
            $sTotalDivPay = round($row->div_payout + $sTotalDivPay);
            $gTotalDivPay = round($row->div_payout + $gTotalDivPay);
            $sTotal = round($sTotal + $total);
            $gTotal = round($total + $gTotal);

            if($sTotalCagr2 != 0) {
                $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
            } else {
                $sTotalCagr = round($sTotalCagr1, 2);
            }
            if($fTotalCagr2 != 0) {
                $fTotalCagr = round(($fTotalCagr1 / $fTotalCagr2), 2);
            } else {
                $fTotalCagr = round($fTotalCagr1, 2);
            }

            if($sTotalPurAmt!= 0)
            {
                $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt+$sTotalDivAmt)), 2);
            }
           else {
                $sTotal_abs = 0;
            }
            if($gTotalPurAmt!= 0)
            {
                $fTotal_abs = round(($gTotalAbs1 / ($gTotalPurAmt+$gTotalDivAmt)), 2);
            }
             else {
                $fTotal_abs = 0;
            }

            if($row->purchase_date) {
                $date = DateTime::createFromFormat('d/m/Y',$row->purchase_date);
                $date = $date->format('d-M-Y');
            } else {
                $date = "";
            }
            if($row->c_nav_date) {
                $date2 = DateTime::createFromFormat('d/m/Y',$row->c_nav_date);
                $date2 = $date2->format('d-M-Y');
            } else {
                $date2 = "";
            }



            //
            // $cagr1 = ($purAmt+$divAmt) * $row->cagr * $row->transaction_day;
            // $cagr2 =($purAmt+$divAmt) * $row->transaction_day;
            // $sTotalCagr1 = $cagr1 + $sTotalCagr1;
            // $sTotalCagr2 = $cagr2 + $sTotalCagr2;
            // if($sTotalCagr2 != 0) {
            // $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
            // } else {
            // $sTotalCagr = round($sTotalCagr1, 2);
            // }

            // $abs1 = $purAmt  * $row->mf_abs;
            // $sTotalAbs1 = $abs1 + $sTotalAbs1;
            // $sTotal_abs = round(($sTotalAbs1 / $sTotalPurAmt ), 2);


            $html .= '<tr nobr="true">
            <td class="border normal">'.$row->mf_scheme_name.'</td>
            <td class="border normal">'.$row->folio_number.'</td>
            <td class="border normal">'.$date.'</td>';
            if(!empty($row->scheme_type))
            {
             if($cap_detail=="cap_detail")
                    {
                        
                          $html .= '<td class="border normal">';
                        if($row->market_cap==''){
                        $html .='Other</td>';}
                        else{
                        $html .=$row->market_cap.'</td>';
                        }
                        
                    }
                    else
                    {
                            $html .= '<td class="border normal">'.$row->scheme_type.'</td>';
                    }
        }
         
            else
                $html .= '<td class="border normal">General</td>';
            if(!empty($purAmt))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia($purAmt).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';
          if(!empty($divAmt))
              $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia($divAmt).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';

            $html .= '<td class="border amount normal">'.sprintf("%.4f",$row->p_nav).'</td>
            <td class="border amount normal">'.$row->live_unit.'</td>
            <td class="border amount normal">'.$row->transaction_day.'</td>
            <td class="border amount normal">'.$row->c_nav.'</td>';
             if($hide_nav_date!='hide_nav_date')    
                    {
            $html .= '<td class="border normal">'.$date2.'</td>';
                    }
            $curval = floatval($row->current_value);
            if(!empty($curval))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->current_value, 2)).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';
            $divr2 = intval($row->div_r2);
            if(!empty($divr2))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->div_r2)).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';
            $divp = intval($row->div_payout);
            if(!empty($divp))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->div_payout)).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';
            $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>
                      <td class="border amount normal">'.round($row->cagr,2).'</td>
                      <td class="border amount normal">'.round($row->mf_abs,2).'</td>
                  </tr>';
        }
        // $schemeName = $row->mf_scheme_name;
          $clientName= $mf_rep_data[$i]->client_name;
          $i++;

  }
  //last footer
  $html .= '<tr nobr="true">
          <td class="border amount normal" colspan="4"></td>';
          if(!empty($sTotalPurAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalPurAmt).'</td>';
          else
        $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivAmt))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivAmt).'</td>';
        else
            $html .= '<td class="border normal"></td>';

            // $gPurNavIndex=$gPurNavIndex+1;
          // echo $sParNavindex."<br>";
            // echo $gTotalPurNav."<br>";
            // echo "Nav".$sTotalPurNav.'hhhh';
          // $sTotalPurNav=$sTotalPurNav/$sParNavindex;
          // $gTotalPurNav=$gTotalPurNav+$sTotalPurNav;
          // echo $sTotalPurNav;
          // echo $gTotalPurNav.
          $html .= '<td class="dataTotal border"></td>
          <td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>';
           if($hide_nav_date!='hide_nav_date')    
                    {
          $html .= '<td class="border amount normal" colspan="3"></td>';
            }
            else{
                $html .= '<td class="border amount normal" colspan="2"></td>';
            }
          if(!empty($sTotalCurValue))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalCurValue).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivR))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivR).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivPay))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivPay).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotal).'</td>';
          if(!empty($sTotalCagr))
              $html .= '<td class="dataTotal border amount">'.$sTotalCagr.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotal_abs))
              $html .= '<td class="dataTotal border amount">'.$sTotal_abs.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';

              // $html .= '<td class="border amount dataTotal">'.$perTotal.' %'.'</td>
              //           <td class="border amount dataTotal"></td>';
      $html .= '</tr>
          <tr>
              <td colspan="18" class="no-border info">
                  Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($sTotal - $sTotalPurAmt).'. Percentage of holding '.$perTotal.' %
              </td>
          </tr>';
  //footer for all the totals
  $html .= '<tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
          <tr>
              <td class="border dataTotal" colspan = "4">Total</td>';
              if(!empty($gTotalPurAmt))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalPurAmt).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivAmt).'</td>';

              // echo $sTotalPurNav."<br>";
              //  echo $gTotalPurNav;
              //  $gPurNavIndex=$gPurNavIndex+1;
              // $gTotalPurNav=$gTotalPurNav+$sTotalPurNav;
              // echo $gPurNavIndex."<br>";
                  //  $gTotalPurNav=$gTotalPurNav/$gPurNavIndex;
          $html.='<td class="dataTotal border amount"></td>
                  <td class="dataTotal border amount">'.$gTotalLiveUnit.'</td>';
                  if($hide_nav_date!='hide_nav_date')    
                    {
                  $html.='<td class="border normal" colspan = "3"></td>';
                    }
                    else
                  {
                      $html.='<td class="border normal" colspan = "2"></td>';
                  }  
              if(!empty($gTotalCurValue))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalCurValue).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              if(!empty($gTotalDivR))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivR).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              if(!empty($gTotalDivPay))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivPay).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotal).'</td>
                        <td class="dataTotal border amount">'.$fTotalCagr.'</td>
                        <td class="dataTotal border amount">'.$fTotal_abs.'</td>


          </tr>
          <tr>
              <td colspan="13" class="no-border info dataTotal">
                  Overall Portfolio Weighted Avg. CAGR: '.$fTotalCagr.' | Weighted Avg. Abs Return: '.$fTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($gTotal - $gTotalPurAmt).' Percentage of holding 100% .
              </td>
          </tr>
          <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
          <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
      </tbody>
  </table>';

$TotalCagrForXirr=$fTotalCagr;
$fTotal=$gTotal;
include 'mf_report_common_family.php';

$sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
$sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;
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



  // $html .= '<table><tbody>
  //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
  //     <tr nobr="true">
  //         <td colspan="" class="no-border">
  //                             <p class="client-name">Net Investment: '.$this->common_lib->moneyFormatIndia(round(($gTotalPurAmt+$div_payout_total)-$rs->redemption)).'</p>
  //         </td>
  //     </tr>
  //      <tr nobr="true">
  //         <td colspan="" class="no-border">
  //             <p class="client-name">Current Value of Total Investment is: '.$this->common_lib->moneyFormatIndia(round($gTotal)).'</p>
  //         </td>
  //     </tr>
  //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
  //     <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
  //     <tr nobr="true">
  //     </tbody></table>';

      // $html.='<p>Note:</p>
      //         <p>1) Annualized Return is calculated on an Absolute basis for < 1 Year And on CAGR basis for >= 1 Year.</p>
      //         <p>2) This Report is Strictly Private and confidential only for clients.</p>';
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
                      <input type="hidden" name="name" id="name" value="<?php echo $familyName ?>" />
                      <input type="hidden" name="titleData" id="titleData" />
                      <input type="hidden" name="htmlData" id="htmlData" />
                      <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Mutual Fund Portfolio" />
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
		    indexLabelFontSize:12,
		    indexLabelFontWeight: "bold",
			indexLabelMaxWidth: 150,
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
        		  titleFontWeight: "bold",
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