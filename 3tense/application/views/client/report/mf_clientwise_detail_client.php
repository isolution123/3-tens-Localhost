<?php
if(empty($mf_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $clientName = ''; $purAmt = 0; $divAmt = 0; $total = 0; $cagr1 = 0; $cagr2 = 0; $abs1 = 0;
    //total of schemes for a particular client
    $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
    $sTotal = 0; $sTotalCagr = 0; $sTotalCagrDivide = 0; $sTotal_abs = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sTotalAbs1 = 0; $sLiveUnit = 0;
    //total of all schemes of a client
    $gTotalPurAmt = 0;  $gTotalDivAmt = 0;  $gTotalPurNav = 0; $gTotalPurNavDivide = 0; $gTotalLiveUnit = 0;  $gTotalCurValue = 0;  $gTotalDivR = 0; $gTotalDivPay = 0;
    $gTotal = 0;  $gTotalCagr = 0;  $gTotal_abs = 0; $gTotalCagr1 = 0; $gTotalCagr2 = 0; $gTotalAbs1 = 0; $gLiveUnit = 0;
    if(!empty($mf_rep_data))
        $clientName = $mf_rep_data[0]->client_name;
    $schemeName = $mf_rep_data[0]->mf_scheme_name;
    $inv_chart = array();
    $cur_chart = array();
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
        .align_right{text-align:right;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Client Wise Detail Mutual Fund Portfolio of '.$clientName.'</div><br/>';
    // add client info to page
    $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
            <tbody>';
    $header = true; $content = false; $footer = false;
    foreach($mf_rep_data as $row)
    {
        $percent = 0;
        if($schemeName == $row->mf_scheme_name) { $footer = false; }
        else { $footer = true; }

        if($footer) {
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
                        <td class="dataTotal border">'.$sTotalLiveUnit.'</td>
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

                    $html .= '<tr>
                        <td colspan="17" class="no-border info">
                            Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($sTotal - $sTotalPurAmt).'
                        </td>
                    </tr>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';

            $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0;
            $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0;
            $sTotalDivPay = 0; $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;
            $sTotalAbs1 = 0; $sTotalCagr1 = 0; $sTotalCagr2 = 0; $sLiveUnit = 0;
            $footer = false;
            $header = true;
        }

        if($header) {
            $html .= '<tr nobr="true">
                            <td class="no-border client-name" colspan="10">'.$row->mf_scheme_name.'</td>
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
            $header = false;
            $content = true;
        }

        if($content)
        {
            //set purchase amount
            if($row->mf_scheme_type != 'DIV') {
                $purAmt = $row->live_unit * $row->p_nav;
                $divAmt = 0;
            }
            else {
                $purAmt = 0;
                $divAmt = $row->live_unit * $row->p_nav;
            }
            //set total value
            if($row->div_payout)
                $total = $row->div_payout + $row->current_value;
            else
                $total = $row->current_value;
            //set cagr1(temp)
            if($row->cagr != null)
                $cagr1 = ($purAmt + $divAmt) * $row->cagr * $row->transaction_day;
            else
                $cagr1 = 0;
            if($row->mf_abs != null)
            {
                $cagr2 = ($purAmt + $divAmt) * $row->transaction_day;
                $abs1 = ($purAmt + $divAmt) * $row->mf_abs;
            } else {
                $abs1 = 0;
                $cagr2 = 0;
            }
            //set sub totals
            $sTotalPurAmt =$purAmt + $sTotalPurAmt;
            $gTotalPurAmt = $purAmt + $gTotalPurAmt;
            $sTotalDivAmt = $divAmt + $sTotalDivAmt;
            $gTotalDivAmt = $divAmt + $gTotalDivAmt;
            $sLiveUnit = $row->live_unit + $sLiveUnit;
            $gLiveUnit = $row->live_unit + $gLiveUnit;
            if($row->live_unit != null && $row->live_unit != 0)
            {
                $sTotalPurNav = round((($sTotalPurAmt + $sTotalDivAmt) / $sLiveUnit), 2);
                $gTotalPurNav = round((($gTotalPurAmt + $gTotalDivAmt) / $gLiveUnit), 2);
                /*$sTotalPurNav = round(($sTotalPurNav + $row->p_nav), 2);
                $sTotalPurNavDivide++;
                $gTotalPurNav = round(($gTotalPurNav + $row->p_nav), 2);
                $gTotalPurNavDivide++;*/
            }
            if(empty($row->div_r2)) {
                $row->div_r2 = 0;
            }
            if(empty($row->div_payout)) {
                $row->div_payout = 0;
            }
            $sTotalCagr1 = $cagr1 + $sTotalCagr1;
            $gTotalCagr1 = $cagr1 + $gTotalCagr1;
            $sTotalCagr2 = $cagr2 + $sTotalCagr2;
            $gTotalCagr2 = $cagr2 + $gTotalCagr2;
            $sTotalAbs1 = $abs1 + $sTotalAbs1;
            $gTotalAbs1 = $abs1 + $gTotalAbs1;
            $sTotalLiveUnit = $row->live_unit + $sTotalLiveUnit;
            $gTotalLiveUnit = $row->live_unit + $gTotalLiveUnit;
            $sTotalCurValue = $row->current_value + $sTotalCurValue;
            $gTotalCurValue = $row->current_value + $gTotalCurValue;
            $sTotalDivR = $row->div_r2 + $sTotalDivR;
            $gTotalDivR = $row->div_r2 + $gTotalDivR;
            $sTotalDivPay =$row->div_payout + $sTotalDivPay;
            $gTotalDivPay = $row->div_payout + $gTotalDivPay;
            $sTotal = $sTotal + $total;
            $gTotal = $total + $gTotal;

            if($sTotalCagr2 != 0) {
                $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
            } else {
                $sTotalCagr = round($sTotalCagr1, 2);
            }
            if($gTotalCagr2 != 0) {
                $gTotalCagr = round(($gTotalCagr1 / $gTotalCagr2), 2);
            } else {
                $gTotalCagr = round($gTotalCagr1, 2);
            }

            if($sTotalPurAmt!= 0)
            {
                $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt + $sTotalDivAmt)), 2);
            }
            elseif($sTotalDivAmt!=0)
            {
                /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
                $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;*/
                $sTotal_abs = round(($sTotalAbs1 / $sTotalDivAmt), 2);
            } else {
                $sTotal_abs = 0;
            }
            if($gTotalPurAmt!= 0)
            {
                $gTotal_abs = round(($gTotalAbs1 / ($gTotalPurAmt + $gTotalDivAmt)), 2);
            }
            elseif($gTotalDivAmt!=0)
            {
                /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
                $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;*/
                $gTotal_abs = round(($gTotalAbs1 / $gTotalDivAmt), 2);
            } else {
                $gTotal_abs = 0;
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
            <td class="border normal">'.$row->client_name.'</td>
            <td class="border normal">'.$row->folio_number.'</td>
            <td class="border normal">'.$date.'</td>';
            if(!empty($row->scheme_type))
                $html .= '<td class="border normal">'.$row->scheme_type.'</td>';
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
            $html .= '<td class="border normal amount">'.$row->p_nav.'</td>
            <td class="border normal amount">'.$row->live_unit.'</td>
            <td class="border normal amount">'.$row->transaction_day.'</td>
            <td class="border normal amount">'.$row->c_nav.'</td>
            <td class="border normal">'.$date2.'</td>';
            $curval = floatval($row->current_value);
            if(!empty($curval))
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->current_value, 2)).'</td>';
            else
                $html .= '<td class="border normal"></td>';
            $divr2 = intval($row->div_r2);
            if(!empty($divr2))
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->div_r2)).'</td>';
            else
                $html .= '<td class="border normal"></td>';
            $divp = intval($row->div_payout);
            if(!empty($divp))
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->div_payout)).'</td>';
            else
                $html .= '<td class="border normal"></td>';
            $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>
            <td class="border normal amount">'.$row->cagr.'</td>
            <td class="border normal amount">'.$row->mf_abs.'</td>
            </tr>';
        }
        $schemeName = $row->mf_scheme_name;
    }
    //last footer
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
                    Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($sTotal - $sTotalPurAmt).'
                </td>
            </tr>';

    //footer for all the totals
    $html .= '<tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            <tr>
                <td class="border dataTotal" colspan = "4">Total</td>';
                if(!empty($gTotalPurAmt))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalPurAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                if(!empty($gTotalDivAmt))
                    $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gTotalDivAmt)).'</td>';
                else
                    $html .= '<td class="border normal"></td>';
                $html .= '<td class="dataTotal border"></td>
                <td class="dataTotal border amount">'.$gTotalLiveUnit.'</td>
                <td class="border normal" colspan = "3"></td>';
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
                <td colspan="17" class="no-border info dataTotal">
                    Overall Portfolio Weighted Avg. CAGR: '.$gTotalCagr.' | Weighted Avg. Abs Return: '.$gTotal_abs.' | Notional (Gain/Loss) '.$this->common_lib->moneyFormatIndia($gTotal - $gTotalPurAmt).'
                </td>
            </tr>
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

    //if arrays are empty, do not show Portfolio Summary
    //var_dump($inv_sum_arr); var_dump($cur_val_sum_arr);
    if(!empty($inv_sum_arr) && !empty($cur_val_sum_arr)) {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px; width:100%;">
        <tbody>
            <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                <td colspan="17" class="title border">
                    Portfolio Summary of '.$clientName.'
                </td>
            </tr>
            <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                <td colspan="3"></td>';
        $chartCount = 0;
        //For Flot Charts change array key from value to data
        //and for SVG Charts change array key from data to value
        foreach ($inv_sum[0] as $key => $value) {
            if($key != 'Total')
            {
                $inv_chart[$chartCount] = array('label' => $key, 'value' => $value);
            }
            $html .= '<td class="dataTotal border" colspan="3">'.$key.'</td>';
            $chartCount++;
        }
        $chartCount = 0;
        foreach ($cur_val_sum[0] as $key => $value) {
            if($key != 'Total')
            {
                $cur_chart[$chartCount] = array('label' => $key, 'value' => $value);
            }
            $chartCount++;
        }
        $html .= '</tr>
                <tr nobr="true">
                <td colspan="3" class="dataTotal border normal">Investment Value</td>';
        $chartCount = 0;
        foreach ($inv_sum[0] as $value) {
            $html .= '<td class="border normal" colspan="3">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        }
        $html .= '</tr>
            <tr>
                <td colspan="3" class="dataTotal border normal">Current Value</td>';
        foreach ($cur_val_sum[0] as $value) {
            $html .= '<td class="border normal" colspan="3">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        }
    } else {
        //$html .= '<br/>';
        // $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px; width:100%;">
        // <tbody>
        // <tr></tr></tbody></table>';
    }
    $fTotal=$gTotal;
    $TotalCagr=$gTotalCagr;
    $Total_abs=$gTotal_abs;
    include 'mf_report_common_client.php';
    $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
    $sTotal = 0; $sTotalCagr = 0; $sTotal_abs = 0;

}
?>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                     <?php if($this->session->userdata('head')=='yes'){ 
                      
                       if($this->session->userdata('user_id')==0004 || $this->session->userdata('user_id')=='0009' || $this->session->userdata('user_id')=='0174' || $this->session->userdata('user_id')=='0196')
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
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
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
function client_selection_change(sel)
{
        document.cookie = "client_select_id_clientwiserpt = " + sel.value;
        window.location.reload();
}
    /*$(document).ready(function(){
        var inv_array = <?php echo json_encode($inv_chart); ?>;
        var cur_array = <?php echo json_encode($cur_chart); ?>;
        console.log(inv_array);
        console.log(cur_array);
        *//*$.plot($("#inv-donut"), inv_array,
        {
            series: {
                pie: {
                    innerRadius: 0.5,
                    show: true
                }
            },
            legend: {
                show: false
            }
        });
        $.plot($("#cur-donut"), cur_array,
            {
                series: {
                    pie: {
                        innerRadius: 0.5,
                        show: true
                    }
                },
                legend: {
                    show: false
                }
        });*//*
        Morris.Donut({
            element: 'inv-donut',
            data: inv_array,
            resize: true
        });
        Morris.Donut({
            element: 'cur-donut',
            data: cur_array,
            resize: true
        });
    });*/
</script>
