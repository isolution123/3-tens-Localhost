<?php
if(empty($mf_rep_data)) {
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
    $fTotalCagr1 = 0; $fTotalCagr2 = 0; $fTotalAbs1 = 0; $fLiveUnit = 0;

    if(!empty($mf_rep_data))
        $familyName = $mf_rep_data[0]->family_name;
    $clientName = $mf_rep_data[0]->client_name;
    $schemeName = $mf_rep_data[0]->mf_scheme_name;;
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 11px; padding:2px}
        .amount{text-align:right;}
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
    $title = '<div class="title row">Client Wise Detail Mutual Fund Portfolio of '.$familyName.' Family</div><br/>';
    if($mf_rep_data)
    {
        // add client info to page
        $html = '<table border="0" cellpadding="4" style="text-align:right; border-width:0px;">
            <tbody>';
        $newScheme = false; $newClient = false; $schemeFoot = false; $familyFoot = false;
        $mfCount = count($mf_rep_data);
        for($i = 0; $i < $mfCount; $i++)
        {
            if($i == 0)
            {
                $newClient = true;
                $newScheme = true;
            }
            if($schemeName != $mf_rep_data[$i]->mf_scheme_name)
            {
                $newScheme = true;
                $schemeFoot = true;
            }
            if($clientName != $mf_rep_data[$i]->client_name)
            {
                $schemeFoot = true;
                $newClient = true;
                $familyFoot = true;
            }
            if($schemeFoot)
            {
                $html .= '<tr nobr="true">
                        <td class="border normal" colspan="4"></td>';
                if(!empty($sTotalPurAmt))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalPurAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                if(!empty($sTotalDivAmt))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                $html .= '<td class="dataTotal border amount">'.round($sTotalPurNav,2).'</td>
                        <td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>
                        <td class="border normal" colspan="3"></td>';
                if(!empty($sTotalCurValue))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalCurValue)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                if(!empty($sTotalDivR))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivR)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                if(!empty($sTotalDivPay))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivPay)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotal)).'</td>';
                if(!empty($sTotalCagr))
                    $html .= '<td class="dataTotal border amount">'.$sTotalCagr.'</td>';
                else
                    $html .= '<td class="border normal amount"></td>';
                if(!empty($sTotal_abs))
                    $html .= '<td class="dataTotal border amount">'.$sTotal_abs.'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                $html .= '</tr>
                <tr>
                    <td colspan="17" class="no-border info">
                        Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia(round($sTotal - $sTotalPurAmt)).'.
                    </td>
                </tr>';
                $schemeFoot = false;
                $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
                $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
                $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
            }
            if($familyFoot)
            {
                $html .= '<tr nobr="true">
                            <td class="border normal" colspan="4"></td>
                            <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalPurAmt)).'</td>';
                            if(empty($gTotalDivAmt) || $gTotalDivAmt==0)
                            {
                              $html.='<td class="dataTotal border amount"></td>';
                            }else {
                              $html.='<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivAmt)).'</td>';
                            }

                            $html.='<td class="dataTotal border amount">'.round($gTotalPurNav,2).'</td>
                            <td class="dataTotal border amount">'.$gTotalLiveUnit.'</td>
                            <td class="border normal" colspan="3"></td>';
                            if(!empty($gTotalCurValue))
                                $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalCurValue)).'</td>';
                            else
                                $html .= '<td class="border normal"></td>';
                            if(!empty($gTotalDivR))
                                $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivR)).'</td>';
                            else
                                $html .= '<td class="border normal"></td>';
                            if(!empty($gTotalDivPay))
                                $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivPay)).'</td>';
                            else
                                $html .= '<td class="border normal"></td>';
                            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotal)).'</td>
                            <td class="dataTotal border amount">'.$gTotalCagr.'</td>
                            <td class="dataTotal border amount">'.$gTotal_abs.'</td>
                        </tr>';
                $familyFoot = false;
                $html .= '<tr>
                        <td colspan="17" class="no-border info">
                            Weighted Avg. CAGR: '.$gTotalCagr.' | Weighted Avg. Abs Return: '.$gTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia(round($gTotal - $gTotalPurAmt)).'.
                        </td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';
                $gTotalPurAmt = 0;  $gTotalDivAmt = 0;  $gTotalPurNav = 0; $gTotalPurNavDivide = 0;
                $gTotalLiveUnit = 0;  $gTotalCurValue = 0;  $gTotalDivR = 0; $gTotalDivPay = 0;
                $gTotal = 0;  $gTotalCagr = 0;  $gTotal_abs = 0; $gTotalCagr1 = 0; $gTotalCagr2 = 0; $gTotalAbs1 = 0; $gLiveUnit = 0;
                $newClient = true; $newScheme = true;
            }
            if($newClient)
            {
                $html .= '<tr nobr="true">
                            <td class="no-border client-name2" colspan="12">'.$mf_rep_data[$i]->client_name.'</td>
                        </tr>';
                $newClient = false;
            }
            if($newScheme)
            {
                $html .= '<tr nobr="true">
                            <td class="no-border client-name" colspan="17"><br/>'.$mf_rep_data[$i]->mf_scheme_name.'</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="70">Client Name</th>
                            <th width="60">Folio Number</th>
                            <th width="65">Purchase Date</th>
                            <th width="45">Scheme Type</th>
                            <th width="70">Purchase Amount</th>
                            <th width="70">Div Amount</th>
                            <th width="48">Purchase NAV</th>
                            <th width="60">No. of Unit</th>
                            <th width="35">Trans Day</th>
                            <th width="47">Current NAV</th>
                            <th width="65">Current NAV Date</th>
                            <th width="70">Current Value</th>
                            <th width="60">Div R</th>
                            <th width="60">Div Payout</th>
                            <th width="75">Total</th>
                            <th width="40">CAGR</th>
                            <th width="40">ABS</th>
                        </tr>
                    </tbody>
                <tbody>';
                $newScheme = false;
            }
            //set purchase amount
            if($mf_rep_data[$i]->mf_scheme_type != 'DIV')
            {
                $purAmt = $mf_rep_data[$i]->live_unit * $mf_rep_data[$i]->p_nav;
                $divAmt = 0;
            }
            else {
                $divAmt = $mf_rep_data[$i]->live_unit * $mf_rep_data[$i]->p_nav;
                $purAmt = 0;
            }
            //set total value
            if($mf_rep_data[$i]->div_payout)
                $total = $mf_rep_data[$i]->div_payout + $mf_rep_data[$i]->current_value;
            else
                $total = $mf_rep_data[$i]->current_value;
            //set cagr1(temp)
            if($mf_rep_data[$i]->cagr != null)
                $cagr1 = ($purAmt + $divAmt) * $mf_rep_data[$i]->cagr * $mf_rep_data[$i]->transaction_day;
            else
                $cagr1 = 0;
            if($mf_rep_data[$i]->mf_abs != null)
            {
                $cagr2 = ($purAmt + $divAmt) * $mf_rep_data[$i]->transaction_day;
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
            $sTotalPurAmt = ($purAmt + $sTotalPurAmt);
            $gTotalPurAmt = ($purAmt + $gTotalPurAmt);
            $fTotalPurAmt = ($purAmt + $fTotalPurAmt);
            $sTotalDivAmt = ($divAmt + $sTotalDivAmt);
            $gTotalDivAmt = ($divAmt + $gTotalDivAmt);
            $fTotalDivAmt = ($divAmt + $fTotalDivAmt);
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
            $sTotalCurValue = $mf_rep_data[$i]->current_value + $sTotalCurValue;
            $gTotalCurValue = $mf_rep_data[$i]->current_value + $gTotalCurValue;
            $fTotalCurValue = $mf_rep_data[$i]->current_value + $fTotalCurValue;
            $sTotalDivR = $mf_rep_data[$i]->div_r2 + $sTotalDivR;
            $gTotalDivR = $mf_rep_data[$i]->div_r2 + $gTotalDivR;
            $fTotalDivR = $mf_rep_data[$i]->div_r2 + $fTotalDivR;
            $sTotalDivPay = $mf_rep_data[$i]->div_payout + $sTotalDivPay;
            $gTotalDivPay = $mf_rep_data[$i]->div_payout + $gTotalDivPay;
            $fTotalDivPay = $mf_rep_data[$i]->div_payout + $fTotalDivPay;
            $sTotal = $sTotal + $total;
            $gTotal = $total + $gTotal;
            $fTotal = $total + $fTotal;

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
                $sTotal_abs = round(($sTotalAbs1 / $sTotalDivAmt), 2);
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
                $gTotal_abs = round(($gTotalAbs1 / $gTotalDivAmt), 2);
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
                $fTotal_abs = round(($fTotalAbs1 / $fTotalDivAmt), 2);
            } else {
                $fTotal_abs = 0;
            }

            if($mf_rep_data[$i]->purchase_date) {
                $date = DateTime::createFromFormat('d/m/Y',$mf_rep_data[$i]->purchase_date);
                $date = $date->format('d-M-Y');
            } else {
                $date = "";
            }
            if($mf_rep_data[$i]->c_nav_date) {
                $date2 = DateTime::createFromFormat('d/m/Y',$mf_rep_data[$i]->c_nav_date);
                $date2 = $date2->format('d-M-Y');
            } else {
                $date2 = "";
            }

            $html .= '<tr nobr="true">
                <td class="border normal">'.$mf_rep_data[$i]->client_name.'</td>
                <td class="border normal">'.$mf_rep_data[$i]->folio_number.'</td>
                <td class="border normal">'.$date.'</td>';
                if(!empty($mf_rep_data[$i]->scheme_type))
                    $html .= '<td class="border normal">'.$mf_rep_data[$i]->scheme_type.'</td>';
                else
                    $html .= '<td class="border normal">General</td>';
                if(!empty($purAmt))
                    $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($purAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                if(!empty($divAmt))
                    $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($divAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
            $html .= '<td class="border normal amount">'.$mf_rep_data[$i]->p_nav.'</td>
                <td class="border normal amount">'.$mf_rep_data[$i]->live_unit.'</td>
                <td class="border normal amount">'.$mf_rep_data[$i]->transaction_day.'</td>
                <td class="border normal amount">'.$mf_rep_data[$i]->c_nav.'</td>
                <td class="border normal">'.$date2.'</td>';
                $curval = floatval($mf_rep_data[$i]->current_value);
                if(!empty($curval))
                    $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($mf_rep_data[$i]->current_value, 2)).'</td>';
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
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>
                <td class="border normal amount">'.$mf_rep_data[$i]->cagr.'</td>
                <td class="border normal amount">'.$mf_rep_data[$i]->mf_abs.'</td>
                </tr>';
            $schemeName = $mf_rep_data[$i]->mf_scheme_name;
            $clientName = $mf_rep_data[$i]->client_name;
        }
        //last table footer
        $html .= '<tr nobr="true">
            <td class="border normal" colspan="4"></td>';
        if(!empty($sTotalPurAmt))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalPurAmt)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($sTotalDivAmt))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivAmt)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        $html .= '<td class="dataTotal border amount">'.round($sTotalPurNav,2).'</td>
            <td class="dataTotal border amount">'.$sTotalLiveUnit.'</td>
            <td class="border normal" colspan="3"></td>';
        if(!empty($sTotalCurValue))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalCurValue)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($sTotalDivR))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivR)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($sTotalDivPay))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotalDivPay)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sTotal)).'</td>';
        if(!empty($sTotalCagr))
            $html .= '<td class="dataTotal border amount">'.$sTotalCagr.'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($sTotal_abs))
            $html .= '<td class="dataTotal border amount">'.$sTotal_abs.'</td>';
        else
            $html .= '<td class="border normal"></td>';
        $html .= '</tr>
        <tr>
            <td colspan="17" class="no-border info">
                Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia(round($sTotal - $sTotalPurAmt)).'.
            </td>
        </tr>
        <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';

        //last client total footer

        $html .= '<tr nobr="true">
                    <td class="border dataTotal" colspan="4">Client Total</td>
                    <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalPurAmt)).'</td>
                    <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivAmt)).'</td>
                    <td class="dataTotal border amount">'.round($gTotalPurNav,2).'</td>
                    <td class="dataTotal border amount">'.$gTotalLiveUnit.'</td>
                    <td class="border normal" colspan="3"></td>';
                    if(!empty($gTotalCurValue))
                        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalCurValue)).'</td>';
                    else
                        $html .= '<td class="border normal"></td>';
                    if(!empty($gTotalDivR))
                        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivR)).'</td>';
                    else
                        $html .= '<td class="border normal"></td>';
                    if(!empty($gTotalDivPay))
                        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivPay)).'</td>';
                    else
                        $html .= '<td class="border normal"></td>';
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotal)).'</td>
                    <td class="dataTotal border amount">'.$gTotalCagr.'</td>
                    <td class="dataTotal border amount">'.$gTotal_abs.'</td>
                </tr>
                <tr>
                    <td colspan="17" class="no-border info">
                        Weighted Avg. CAGR: '.$gTotalCagr.' | Weighted Avg. Abs Return: '.$gTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia(round($gTotal - $gTotalPurAmt)).'.
                    </td>
                </tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';

        //last family footer
        $html .= '<tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                    <td class="border dataTotal" colspan="4">Family Total</td>
                    <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotalPurAmt)).'</td>
                    <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotalDivAmt)).'</td>
                    <td class="dataTotal border amount"></td>
                    <td class="dataTotal border amount">'.$fTotalLiveUnit.'</td>
                    <td class="border normal" colspan="3"></td>';
        if(!empty($fTotalCurValue))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotalCurValue)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($fTotalDivR))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotalDivR)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        if(!empty($fTotalDivPay))
            $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotalDivPay)).'</td>';
        else
            $html .= '<td class="border normal"></td>';
        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($fTotal)).'</td>
                    <td class="dataTotal border amount">'.$fTotalCagr.'</td>
                    <td class="dataTotal border amount">'.$fTotal_abs.'</td>
                </tr>
                <tr>
                    <td colspan="17" class="no-border info dataTotal">
                        Overall Portfolio Weighted Avg. CAGR: '.$fTotalCagr.' . | Weighted Avg. Abs Return: '.$fTotal_abs.' . | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia(round($fTotal - $fTotalPurAmt)).' .
                    </td>
                </tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            </tbody>
        </table>';

        //typecasting sql objects into array, to check if they are empty
        $inv_sum_arr = (array)$inv_sum;
        $cur_val_sum_arr = (array)$cur_val_sum;

        //temp code to hide summary table
        $inv_sum_arr = null;
        $cur_val_sum_arr = null;

        //var_dump($inv_sum_arr); var_dump($cur_val_sum_arr);
        //if arrays are empty, do not show Portfolio Summary
        if(!empty($inv_sum_arr) && !empty($cur_val_sum_arr)) {
            $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px; width:100%;">
                <tbody>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true">
                        <td colspan="17" class="title border">
                            Portfolio Summary of '.$familyName.' Family
                        </td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr>
                        <td colspan="3"></td>';
            foreach ($inv_sum[0] as $key => $value) {
                $html .= '<td class="dataTotal border" colspan="3">'.$key.'</td>';
            }
            $html .= '</tr>
                    <tr nobr="true">
                    <td class="dataTotal border" colspan="3">Investment Value</td>';
            foreach ($inv_sum[0] as $value) {
                $html .= '<td class="dataTotal border" colspan="3">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
            }
            $html .= '</tr>
                <tr>
                    <td class="dataTotal border" colspan="3">Current Value</td>';
            foreach ($cur_val_sum[0] as $value) {
                $html .= '<td class="dataTotal border" colspan="3">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
            }
        } else {
            //$html .= '<br/>';
            // $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px; width:100%;">
            //     <tbody>
            //     <tr>';
        }

        include 'mf_report_common_family.php';

        $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
        $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
        $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;
    }
}
?>
<div id="page-content" style="margin:0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <?php if($this->session->userdata('head')=='yes'){ 
                      
                       if($this->session->userdata('user_id')==0004 || $this->session->userdata('user_id')=='0009' || $this->session->userdata('user_id')=='0174'  || $this->session->userdata('user_id')=='0196')
                    { ?>
                  <div class="btn-group">
                      
                      <div id="select-family">
                        <select id="select-family" name="right_client_selection" style="width:96%;padding:5px;margin:5px;"  onchange="client_selection_change(this);" class="select2" >
                            <option value="0" selected disabled>Select Family Member</option>
                                <?php foreach($this->session->userdata('clients_list') as $row):
                                if($this->session->userdata('client_id')== $row->client_id && $this->session->userdata('type')=='')
                                {
                                ?>
                                
                                <option value='<?php echo $row->client_id; ?>' selected><?php echo $row->name; ?></option>
                                <?php
                                }
                                else
                                {
                                ?>
                                    <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                <?php    
                                
                                
                                }
                                endforeach; ?>
                        </select>
                      </div>   
                    
                      </div>
                      <?php } } ?>
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
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Mutual Fund Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
function client_selection_change(sel)
{
        document.cookie = "client_select_id_clientwiserpt = " + sel.value;
        window.location.reload();
}
</script>