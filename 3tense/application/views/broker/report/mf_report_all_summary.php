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
  $fperTotal=0;
  //total of schemes for a particular client
  $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
  $sTotal = 0; $sTotalCagr = 0; $sTotalCagrDivide = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
  
  //total of schemes for a particular client
  $fTotalPurAmt = 0; $fTotalDivAmt = 0; $fTotalPurNav = 0; $fTotalPurNavDivide = 0; $fTotalLiveUnit = 0; $fTotalCurValue = 0; $fTotalDivR = 0; $fTotalDivPay = 0;
  $fTotal = 0; $ffTotalCagr = 0; $fTotalCagrDivide = 0; $ffTotal_abs = 0; $ffTotalCagr1 = 0; $ffTotalCagr2 = 0; $fTotalAbs1 = 0; $fLiveUnit = 0;
  
  
  //total of all schemes of a client
  $gTotalPurAmt = 0;  $gTotalDivAmt = 0;  $gTotalPurNav = 0; $gTotalPurNavDivide = 0; $gTotalLiveUnit = 0;  $gTotalCurValue = 0;  $gTotalDivR = 0; $gTotalDivPay = 0;
  $gTotal = 0; $invested_cost_total=0; $fTotalCagr = 0;  $fTotal_abs = 0; $fTotalCagr1 = 0; $fTotalCagr2 = 0; $gTotalAbs1 = 0; $gLiveUnit = 0;
  $sParNavindex=0;$gTotalPurNav=0;$gPurNavIndex=0;$per=0;
  
  if(!empty($mf_rep_data))
        $clientName = $mf_rep_data[0]->client_name;
  
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
  $title = '<div class="title row">Detail AUM Report Mutual Fund Portfolio </div><br/>';
  // add client info to page
  $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
          <tbody>';
  $header = true; $content = false; $footer = false;$schemeFoot = false;$newClient=true;
$familyFoot = false;
  foreach ($mf_rep_data as $rss)
     {
       $invested_cost_total=$invested_cost_total+$rss->purchase_amount;
     }
  $i=0;
  $clientName= $mf_rep_data[$i]->client_name;
  $family_name=$mf_rep_data[$i]->family_name;
   $i=0;
  foreach($mf_rep_data as $row)
  {
        if($i == 0)
        {
            $newClient = true;
            $newScheme = true;
        }
        if($family_name != $row->family_name)
        {
            $familyFoot = true;
        }
        
        if($clientName != $row->client_name)
        {
            $schemeFoot=true;
            $newClient = true;
            $newClient = false;
        }
       /* if($schemeFoot)
        {
          $html .= '<tr nobr="true">
                    <td class="border normal" colspan="6"></td>';
          if(!empty($sTotalPurAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalPurAmt).'</td>';
          else
              $html .= '<td class="border normal"></td>';
          if(!empty($sTotalDivAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivAmt).'</td>';
          else
              $html .= '<td class="border normal"></td>';

          if(!empty($sTotalPurNav))
              $html .= '<td class="dataTotal border"></td>';
          else
              $html .= '<td class="border normal"></td>';

            $html .='<td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>
                  <td class="border amount normal" colspan="3"></td>';
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
     
          $html .= '</tr>
          <tr>
              <td colspan="16" class="no-border info">
                  Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) Rs.'.($sTotal - $sTotalPurAmt).'. Percentage of holding '.$perTotal. ' %
              </td>
          </tr>';
          $schemeFoot = false;
          
          
          $sTotalPurAmt = 0;$sParNavindex=0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
          $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
          $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
          $perTotal=0;
      }
   
       if($familyFoot)
        {
          $html .= '<tr nobr="true">
                    <td class="border normal" colspan="6"></td>';
          if(!empty($fTotalPurAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotalPurAmt).'</td>';
          else
              $html .= '<td class="border normal"></td>';
          if(!empty($fTotalDivAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotalDivAmt).'</td>';
          else
              $html .= '<td class="border normal"></td>';

          if(!empty($fTotalPurNav))
              $html .= '<td class="dataTotal border"></td>';
          else
              $html .= '<td class="border normal"></td>';

            $html .='<td class="dataTotal border amount">'.$fTotalLiveUnit.'</td>
                  <td class="border amount normal" colspan="3"></td>';
          if(!empty($fTotalCurValue))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotalCurValue).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($fTotalDivR))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotalDivR).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($fTotalDivPay))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotalDivPay).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fTotal).'</td>';
          if(!empty($ffTotalCagr))
              $html .= '<td class="dataTotal border amount">'.$ffTotalCagr.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          if(!empty($ffTotal_abs))
              $html .= '<td class="dataTotal border amount">'.$ffTotal_abs.'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
     
          $html .= '</tr>
          <tr>
              <td colspan="16" class="no-border info">
                  Weighted Avg. CAGR: '.$ffTotalCagr.' | Weighted Avg. Abs Return: '.$ffTotal_abs.' | Notional (Gain/Loss) Rs.'.($fTotal - $fTotalPurAmt).'. Percentage of holding '.$fperTotal. ' %
              </td>
          </tr><tr > <td colspan="16" class="no-border"><bR>&nbsp;</td></tr>';
          $familyFoot = false;
          
          $fTotalPurAmt = 0;$fParNavindex=0; $fTotalDivAmt = 0; $fTotalPurNav = 0; $fTotalPurNavDivide = 0;
          $fTotalLiveUnit = 0; $fTotalCurValue = 0; $fTotalDivR = 0; $fTotalDivPay = 0;
          $fTotal = 0; $ffTotalCagr = 0; $ffTotal_abs = 0; $ffTotalCagr1 = 0; $ffTotalCagr2 = 0; $fTotalAbs1 = 0; $fLiveUnit = 0;
          $fperTotal=0;
      }
       */
        if($newClient)
        {
            $newClient=false;
            $html .= '<tr nobr="true" class="head-row">
                            <th width="60">Family Name</th>
                            <th width="70">Client Name</th>
                            <th width="60">Pan No</th>
                            <th width="70">Scheme Name</th>
                            <th width="60">Product Code</th>
                            <th width="60">Folio Number</th>
                            <th width="65">Inception Date</th>
                            <th width="45">Scheme Type</th>
                            <th width="50">Purchase Amount</th>
                            <th width="60">Div Amount</th>
                            <th width="48">Purchase NAV</th>
                            <th width="50">No. of Unit</th>
                            <th width="35">Trans Day</th>
                            <th width="47">Current NAV</th>
                            <th width="75">Current NAV Date</th>
                            <th width="40">Current Value</th>
                            <th width="40">Div Payout</th>
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
             $purAmt=round($row->purchase_amount);
             $divAmt=round($row->div_amount);

             $per=round(($purAmt/$invested_cost_total)*100,2);
             $perTotal=$perTotal+$per;
             $fperTotal+=$fperTotal+$per;
             
            //set total value
           if($row->div_payout)
                $total = round($row->div_payout + $row->current_value);
            else
                $total = round($row->current_value);
                
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
            $fTotalPurAmt= round($purAmt + $fTotalPurAmt);
            $gTotalPurAmt = round($purAmt + $gTotalPurAmt);
            
            $sTotalDivAmt = round($divAmt + $sTotalDivAmt);
            $fTotalDivAmt = round($divAmt + $fTotalDivAmt);
            $gTotalDivAmt = round($divAmt + $gTotalDivAmt);
            
            $sLiveUnit = $row->live_unit + $sLiveUnit;
            $fLiveUnit = $row->live_unit + $fLiveUnit;
            $gLiveUnit = $row->live_unit + $gLiveUnit;

            
            if($row->live_unit != null && $row->live_unit != 0)
            {

              $sTotalPurNav = round((($sTotalPurAmt + $sTotalDivAmt) / $sLiveUnit), 2);
              $fTotalPurNav = round((($fTotalPurAmt + $fTotalDivAmt) / $fLiveUnit), 2);
              $gTotalPurNav = round((($gTotalPurAmt + $gTotalDivAmt) / $gLiveUnit), 2);
            }
            if(empty($row->div_r2)) {
                $row->div_r2 = 0;
            }
            if(empty($row->div_payout)) {
                $row->div_payout = 0;
            }
            $sTotalCagr1 = $cagr1 + $sTotalCagr1;
            $fTotalCagr1 = $cagr1 + $fTotalCagr1;
            $ffTotalCagr1 = $cagr1 + $ffTotalCagr1;
            
            $sTotalCagr2 = $cagr2 + $sTotalCagr2;
            $fTotalCagr2 = $cagr2 + $fTotalCagr2;
            $ffTotalCagr2 = $cagr2 + $ffTotalCagr2;
            
            $sTotalAbs1 = $abs1 + $sTotalAbs1;
            $fTotalAbs1 = $abs1 + $fTotalAbs1;
            $gTotalAbs1 = $abs1 + $gTotalAbs1;
            
            $sTotalLiveUnit = $row->live_unit + $sTotalLiveUnit;
            $fTotalLiveUnit = $row->live_unit + $fTotalLiveUnit;
            $gTotalLiveUnit = $row->live_unit + $gTotalLiveUnit;
            
            $sTotalCurValue = round($row->current_value + $sTotalCurValue);
            $fTotalCurValue = round($row->current_value + $fTotalCurValue);
            $gTotalCurValue = round($row->current_value + $gTotalCurValue);
            
            $sTotalDivR = round($row->div_r2 + $sTotalDivR);
            $fTotalDivR = round($row->div_r2 + $fTotalDivR);
            $gTotalDivR = round($row->div_r2 + $gTotalDivR);
            
            $sTotalDivPay = round($row->div_payout + $sTotalDivPay);
            $fTotalDivPay = round($row->div_payout + $fTotalDivPay);
            $gTotalDivPay = round($row->div_payout + $gTotalDivPay);
            
            $sTotal = round($sTotal + $total);
            $fTotal = round($fTotal + $total);
            $gTotal = round($total + $gTotal);

            if($sTotalCagr2 != 0) {
                $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
            } else {
                $sTotalCagr = round($sTotalCagr1, 2);
            }
            if($ffTotalCagr2 != 0) {
                $ffTotalCagr = round(($ffTotalCagr1 / $ffTotalCagr2), 2);
            } else {
                $ffTotalCagr = round($ffTotalCagr1, 2);
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
            if($fTotalPurAmt!= 0)
            {
                $ffTotal_abs = round(($fTotalAbs1 / ($fTotalPurAmt+$fTotalDivAmt)), 2);
            }
            else {
                $ffTotal_abs = 0;
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

            $html .= '<tr nobr="true">
            <td class="border normal">'.$row->family_name.'</td>
            <td class="border normal">'.$row->client_name.'</td>
            <td class="border normal">'.$row->pan_no.'</td>
            <td class="border normal">'.$row->mf_scheme_name.'</td>
            <td class="border normal">'.$row->prod_code.'</td>
            <td class="border normal">'.$row->folio_number.'</td>
            <td class="border normal">'.$date.'</td>';
            if(!empty($row->scheme_type))
                $html .= '<td class="border normal">'.$row->scheme_type.'</td>';
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
            <td class="border amount normal">'.$row->c_nav.'</td>
            <td class="border normal">'.$date2.'</td>';
            $curval = floatval($row->current_value);
            if(!empty($curval))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->current_value)).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';
            $divr2 = intval($row->div_r2);
            /*if(!empty($divr2))
                $html .= '<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->div_r2)).'</td>';
            else
                $html .= '<td class="border amount normal"></td>';*/
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
        
        $clientName= $mf_rep_data[$i]->client_name;
        $family_name=$mf_rep_data[$i]->family_name;
        $i++;

  }
  //last footer
 /* $html .= '<tr nobr="true">
          <td class="border amount normal" colspan="8"></td>';
          if(!empty($sTotalPurAmt))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalPurAmt).'</td>';
          else
        $html .= '<td class="border amount normal"></td>';
          if(!empty($sTotalDivAmt))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivAmt).'</td>';
        else
            $html .= '<td class="border normal"></td>';

            
          $html .= '<td class="dataTotal border"></td>
          <td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>
          <td class="border amount normal" colspan="3"></td>';
          if(!empty($sTotalCurValue))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalCurValue).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';
          /*if(!empty($sTotalDivR))
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($sTotalDivR).'</td>';
          else
              $html .= '<td class="border amount normal"></td>';*
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

          $html .= '</tr>
          <tr>
              <td colspan="18" class="no-border info">
                  Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($sTotal - $sTotalPurAmt).'. Percentage of holding '.$perTotal.' %
              </td>
          </tr>';
          */
        //footer for all the totals
        //$html .= '<tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>'
        $html .= '  <tr>
              <td class="border dataTotal" colspan = "8">Total</td>';
              if(!empty($gTotalPurAmt))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalPurAmt).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivAmt).'</td>';

           
              $html.='<td class="dataTotal border amount"></td>
                  <td class="dataTotal border amount">'.$gTotalLiveUnit.'</td>
                  <td class="border normal" colspan = "3"></td>';
              if(!empty($gTotalCurValue))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalCurValue).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              /*if(!empty($gTotalDivR))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivR).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';*/
              if(!empty($gTotalDivPay))
                  $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotalDivPay).'</td>';
              else
                  $html .= '<td class="border amount normal"></td>';
              $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($gTotal).'</td>
                        <td class="dataTotal border amount">'.$fTotalCagr.'</td>
                        <td class="dataTotal border amount">'.$fTotal_abs.'</td>


          </tr>';
          // <tr>
          //     <td colspan="15" class="no-border info dataTotal">
          //         Overall Portfolio Weighted Avg. CAGR: '.$fTotalCagr.' | Weighted Avg. Abs Return: '.$fTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($gTotal - $gTotalPurAmt).' Percentage of holding 100% .
          //     </td>
          // </tr>
         $html.= '<tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
          <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
      </tbody>
  </table>';


//$fTotal=$gTotal;
//include 'mf_report_common_family.php';

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
                      <div id="title_data"><?php echo $title; ?></div>
                      <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                      <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                      <input type="hidden" name="name" id="name" value="Detail AUM Report" />
                      <input type="hidden" name="titleData" id="titleData" />
                      <input type="hidden" name="htmlData" id="htmlData" />
                      <!-- <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Mutual Fund Portfolio" /> -->
                  </div>
              </div>
          </form>
      </div>
  </div>
</div>
