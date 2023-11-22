<?php
if(empty($ins_rep_data) && empty($gen_ins_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $commenceDateTemp = ''; $nextPremDateTemp = ''; $maturityDateTemp = ""; $clientID = ""; $clientName = "";
    $totalSum = 0; $totalPaid = 0; $totalPaidGen = 0; $fundTotal = 0;
    $adj = array();
    $adjCounter = 0;
    if(!empty($ins_rep_data))
        $clientName = $ins_rep_data[0]->name;
    else
        $clientName = $gen_ins_data[0]->name;
    $css = '<style type="text/css">
        table { width:100% }
        table td {font-size: 11px; padding:2px; text-align:center; }
        table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:left; text-indent: 5px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 10px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Insurance Portfolio of '.$clientName.'</div><br/>';
    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
            <tbody>
                <tr nobr="true" class="head-row">';
                if($report_info['ins_type'] == 1 || $report_info['ins_type'] == 2) {
                    $html .= '<th width="55">Company</th>
                    <th width="65">Plan</th>
                    <th width="65">Policy Number</th>
                    <th width="65">Start Date</th>
                    <th width="65">First Unpaid Date</th>
                    <th width="65">Maturity Date</th>
                    <th width="50">Nominee Name</th>
                    <th width="65">Premium Amount</th>
                    <th width="45">Mode</th>
                    <th width="70">Sum Assured</th>
                    <th width="40">Benefit Term</th>
                    <th width="40">PPT Term</th>
                    <th width="55">Remaining PPT</th>
                    <th width="70">Total Paid</th>
                    <th width="65">Fund Value</th>
                    <th width="40">Status</th>
                    <th width="30">ADJ</th>
                    <th width="50">Advisor Name</th>';
                } else {
                    $html .= '<th width="60">Company</th>
                    <th width="70">Plan</th>
                    <th width="70">Policy Number</th>
                    <th width="65">Start Date</th>
                    <th width="65">First Unpaid Date</th>
                    <th width="65">Maturity Date</th>
                    <th width="60">Nominee Name</th>
                    <th width="60">Premium Amount</th>
                    <th width="50">Mode</th>
                    <th width="80">Sum Assured</th>
                    <th width="40">Benefit Term</th>
                    <th width="40">PPT Term</th>
                    <th width="50">Remaining PPT</th>
                    <th width="70">Total Paid</th>
                    <th width="45">Status</th>
                    <th width="30">ADJ</th>
                    <th width="60">Advisor Name</th>';
                }
    $html .='</tr>
            </tbody>
        <tbody>';
    foreach($ins_rep_data as $row)
    {

        if($row->commence_date) {
            $commenceDateTemp = DateTime::createFromFormat('Y-m-d',$row->commence_date);
            $commenceDateTemp = $commenceDateTemp->format('d-M-Y');
        }
        if($row->next_prem_due_date){
            $nextPremDateTemp = DateTime::createFromFormat('Y-m-d',$row->next_prem_due_date);
            $nextPremDateTemp = $nextPremDateTemp->format('d-M-Y');
        }
        if($row->maturity_date){
            $maturityDateTemp = DateTime::createFromFormat('Y-m-d',$row->maturity_date);
            $maturityDateTemp = $maturityDateTemp->format('d-M-Y');
        }
        if($row->adjustment_flag == 'Yes')
        {
            $adj[$adjCounter]['cli_name'] = $row->name;
            $adj[$adjCounter]['pol_num'] = $row->policy_num;
            $adj[$adjCounter]['adj'] = $row->adjustment;
            $adjCounter++;
        }

        if($report_info['ins_type'] == 1) {
            $fundTotal = round($fundTotal + $row->system_fund);
        }
        else if($report_info['ins_type'] == 2) {
            $fundTotal = round($fundTotal + $row->fund_value);
        }
        $totalPaid = round($totalPaid + $row->prem_paid_till_date);
        $totalSum = round($totalSum + $row->amt_insured);
        $html .= '<tr nobr="true">
                <td class="border normal">'.$row->ins_comp_name.'</td>
                <td class="border normal">'.$row->plan_name.'</td>
                <td class="border normal">'.$row->policy_num.'</td>
                <td class="border normal noWrap" nobr="true">'.$commenceDateTemp.'</td>
                <td class="border normal noWrap" nobr="true">'.$nextPremDateTemp.'</td>
                <td class="border normal noWrap" nobr="true">'.$maturityDateTemp.'</td>
                <td class="border normal">'.$row->nominee.'</td>';
                $prem_amt = intval($row->prem_amt);
                if(!empty($prem_amt)) {
                    $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->prem_amt)).'</td>';
                } else {
                    $html .= '<td class="border normal amount"></td>';
                }
                $html .= '<td class="border normal">'.$row->mode_name.'</td>';
                $amt_ins = intval($row->amt_insured);
                if(!empty($amt_ins)) {
                    $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->amt_insured)).'</td>';
                } else {
                    $html .= '<td class="border normal amount"></td>';
                }

        if(intval($row->benefit_term) > 0) {
            $html .= '<td class="border normal">'.$row->benefit_term.'</td>';
        } else {
            $html .= '<td class="border normal"></td>';
        }
        if(intval($row->PPT) > 0) {
            $html .= '<td class="border normal">'.$row->PPT.'</td>';
        } else {
            $html .= '<td class="border normal"></td>';
        }
        if(intval($row->remaining_PPT) > 0) {
            $html .= '<td class="border normal">'.$row->remaining_PPT.'</td>';
        } else {
            $html .= '<td class="border normal"></td>';
        }

        $prem_paid = intval($row->prem_paid_till_date);
        if(!empty($prem_paid)) {
            $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->prem_paid_till_date)).'</td>';
        } else {
            $html .= '<td class="border normal amount"></td>';
        }

        if($report_info['ins_type'] == 1) {
            $system_fund = intval($row->system_fund);
            if(!empty($system_fund)) {
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->system_fund)).'</td>';
            } else {
                $html .= '<td class="border normal"></td>';
            }
        } elseif($report_info['ins_type'] == 2) {
            $fund_val = intval($row->fund_value);
            if(!empty($fund_val)) {
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->fund_value)).'</td>';
            } else {
                $html .= '<td class="border normal"></td>';
            }
        }
        $html .='<td class="border normal">'.$row->status.'</td>
                    <td class="border normal">'.$row->adjustment_flag.'</td>
                    <td class="border normal">'.$row->adviser_name.'</td>
                </tr>';
    }
    //footer
    $html .= '<tr nobr="true">
                <td class="border normal" colspan="9"></td>
                <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($totalSum).'</td>
                <td class="border normal" colspan="3"></td>
                <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($totalPaid).'</td>';
    if($report_info['ins_type'] == 1 || $report_info['ins_type'] == 2) {
        $html .= '<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fundTotal).'</td>';
    }

    $html .='<td class="border normal" colspan="3"></td>
                </tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            </tbody>
        </table>';

    if($gen_ins_data)
    {
        $html .= '<h4>GENERAL INSURANCE DETAILS</h4>
        <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
            <tbody>
                <tr nobr="true" class="head-row">
                    <th width="70">Company</th>
                    <th width="90">Plan</th>
                    <th width="80">Policy Number</th>
                    <th width="65">Start Date</th>
                    <th width="65">First Unpaid Date</th>
                    <th width="65">Maturity Date</th>
                    <th width="60">Premium Amount</th>
                    <th width="50">Mode</th>
                    <th width="80">Sum Assured</th>
                    <th width="50">Benefit Term</th>
                    <th width="50">PPT Term</th>
                    <th width="50">Remaining PPT</th>
                    <th width="65">Premium Paid</th>
                    <th width="60">Status</th>
                    <th width="30">ADJ</th>
                    <th width="70">Advisor Name</th>
                </tr>
            </tbody>
            <tbody>';
        //var_dump($gen_ins_data);
        foreach($gen_ins_data as $row)
        {
            if($row->commence_date) {
                $commenceDateTemp = DateTime::createFromFormat('Y-m-d',$row->commence_date);
                $commenceDateTemp = $commenceDateTemp->format('d-M-Y');
            }
            if($row->next_prem_due_date){
                $nextPremDateTemp = DateTime::createFromFormat('Y-m-d',$row->next_prem_due_date);
                $nextPremDateTemp = $nextPremDateTemp->format('d-M-Y');
            }
            if($row->maturity_date){
                $maturityDateTemp = DateTime::createFromFormat('Y-m-d',$row->maturity_date);
                $maturityDateTemp = $maturityDateTemp->format('d-M-Y');
            }
            if($row->adjustment_flag == 'Yes')
            {
                $adj[$adjCounter]['cli_name'] = $row->name;
                $adj[$adjCounter]['pol_num'] = $row->policy_num;
                $adj[$adjCounter]['adj'] = $row->adjustment;
                $adjCounter++;
            }
            $totalPaidGen = round($totalPaidGen + $row->prem_paid_till_date);
            $html .= '<tr nobr="true">
                        <td class="border normal">'.$row->ins_comp_name.'</td>
                        <td class="border normal">'.$row->plan_name.'</td>
                        <td class="border normal">'.$row->policy_num.'</td>
                        <td class="border normal">'.$commenceDateTemp.'</td>
                        <td class="border normal">'.$nextPremDateTemp.'</td>
                        <td class="border normal">'.$maturityDateTemp.'</td>';
                        $prem_amt = intval($row->prem_amt);
                        if(!empty($prem_amt)) {
                            $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->prem_amt)).'</td>';
                        } else {
                            $html .= '<td class="border normal amount"></td>';
                        }
                        $html .= '<td class="border normal">'.$row->mode_name.'</td>';
                        $amt_ins = intval($row->amt_insured);
                        if(!empty($amt_ins)) {
                            $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->amt_insured)).'</td>';
                        } else {
                            $html .= '<td class="border normal amount"></td>';
                        }
            if($row->benefit_term > 0) {
                $html .= '<td class="border normal">'.$row->benefit_term.'</td>';
            } else {
                $html .= '<td class="border normal"></td>';
            }
            if($row->PPT > 0) {
                $html .= '<td class="border normal">'.$row->PPT.'</td>';
            } else {
                $html .= '<td class="border normal"></td>';
            }
            if($row->remaining_PPT > 0) {
                $html .= '<td class="border normal">'.$row->remaining_PPT.'</td>';
            } else {
                $html .= '<td class="border normal"></td>';
            }

            $prem_paid = intval($row->prem_paid_till_date);
            if(!empty($prem_paid)) {
                $html .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->prem_paid_till_date)).'</td>';
            } else {
                $html .= '<td class="border normal amount"></td>';
            }
            /*if($report_info['ins_type'] == 1) {
                $html .= '<td>'.$this->common_lib->moneyFormatIndia($row->fund_value).'</td>';
            } elseif($report_info['ins_type'] == 2) {
                if(!empty($row->system_fund)) {
                    $html .= '<td>'.$this->common_lib->moneyFormatIndia($row->system_fund).'</td>';
                } else {
                    $html .= '<td>'.$this->common_lib->moneyFormatIndia(0.00).'</td>';
                }
            }*/
            $html .='   <td class="border normal">'.$row->status.'</td>
                        <td class="border normal">'.$row->adjustment_flag.'</td>
                        <td class="border normal">'.$row->adviser_name.'</td>
                    </tr>';
        }
        $html .= '<tr>
                <td class="border normal" colspan="12"></td>
                <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($totalPaidGen).'</td>';
        /*if($report_info['ins_type'] == 1 || $report_info['ins_type'] == 2)
            $html .= '<td></td>';*/
        $html .='<td class="border normal" colspan="3"></td>
                </tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
            </tbody>
        </table>';
    }

    if(!empty($adj))
    {
        $html .= '<h4>ADJUSTMENT DETAILS</h4>
        <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
            <tbody>
                <tr nobr="true" class="head-row">
                        <th style="width:180">Client Name</th>
                        <th style="width:180">Policy Number</th>
                        <th style="width:620">Adjustment</th>
                    </tr>
                </thead>
            <tbody>';
        foreach($adj as $row)
        {
            $html .= '<tr nobr="true">
                        <td class="border normal">'.$row['cli_name'].'</td>
                        <td class="border normal">'.$row['pol_num'].'</td>
                        <td class="border normal">'.$row['adj'].'</td>
                    </tr>';
        }
        $html .= '</tbody>
            </table>';
    }
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
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Insurances/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Insurances/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Insurance Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
