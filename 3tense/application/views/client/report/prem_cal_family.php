<?php
if(empty($prem_data) && empty($lapse_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    //to change date to current format
    $commenceDateTemp = ''; $nextPremDateTemp = ''; $maturityDateTemp = "";
    $familyName = ""; $clientName = "";

    $totalCounter = 0;
    $totCliMonthPremVal = array();
    $totCliPremValue = 0;
    $totalMonthPremVal = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $grandTotal = 0;
    $header = true; $content = false; $footer = false;
    $nextPremDateTemp = ''; $commenceDateTemp = "";
    $totalCounter = 0; $sumInsuredLap = 0;
    if(!empty($prem_data)) {
        //$clientName = $prem_data[0]->name;
        $familyName = $prem_data[0]->fam_name;
    } else {
        //$clientName = $lapse_rep_data[0]->name;
        $familyName = $lapse_rep_data[0]->fam_name;
    }
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right;padding-right:5px; text-indent: 15px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Premium Calendar for '.$familyName.' Family';
    if(!empty($rep_date)) {
        $title .= '&nbsp;&nbsp;  (for the period of '.$rep_date['rep_date_start'].' to '.$rep_date['rep_date_end'].')';
    }
    $title .= '</div><br/>';

    if(!empty($prem_data)) {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';

        foreach($prem_data as $row)
        {
            $percent = 0;
            if($row->adjustment == 1) {
                $adj = '<br/>(ADJ)';
            } else {
                $adj = '';
            }
            if($clientName == $row->name) { $footer = false; $same_client = true; } else { $footer = true; $same_client = false; }
            if($footer && $clientName != '') {
                $html .= '<tr>
                    <td class="border normal" colspan="3"></td>';
                foreach($totCliMonthPremVal as $value)
                {
                    $totCliPremValue = $totCliPremValue + $value;
                    $grandTotal = $grandTotal + $value;

                    $html .= '<td class="border normal dataTotal">'.$this->common_lib->moneyFormatIndia($value).'</td>';
                }
                $html .= '</tr>
                <tr nobr="true" border="0"><td colspan="13" border="0" class="default no-border"><br/></td></tr>';

                // reset the client premium values
                unset($totCliMonthPremVal);
                $totCliMonthPremVal = array();
                $totCliPremValue = 0;
                $footer = false;
                $header = true;
            }

            if($header) {
                if(!$same_client) {
                    $html .= '<tr nobr="true">
                        <td class="no-border client-name" colspan="15">'.$row->name.'</td>
                    </tr>';
                    $same_client = true;
                }
                $html .= '<tr nobr="true" class="head-row">
                        <th width="100">Company Name</th>
                        <th width="100">Policy No.</th>
                        <th width="60">Paid Up</th>';
                $headCounter = 0;
                foreach($prem_data[0] as $key=>$value) {
                    if($headCounter > 5)
                        $html .= '<th width="60">'.$key.'</th>';
                    $headCounter++;
                }
                $html .='</tr>
                </tbody>
                <tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $headCounter = 0;
                $totalCounter = 0;
                $html .='<tr>';
                foreach($row as $value) {
                    if($headCounter != 0 && $headCounter != 1 && $headCounter != 4)
                    {
                        if(!empty($value) && $value != '0.00') {
                            if(is_numeric($value) && $headCounter > 3)
                                $html .= '<td class="border normal" width="60px" height="40px">'.$this->common_lib->moneyFormatIndia(round($value)).$adj.'</td>';
                            else
                                $html .= '<td class="border normal">'.$value.'</td>';
                        } else {
                            $html .= '<td class="border normal" width="60px" height="40px"></td>';
                        }
                        if($headCounter >= 6)
                        {
                            if(!isset($totCliMonthPremVal[$totalCounter])) {
                                $totCliMonthPremVal[$totalCounter] = 0;
                            }
                            elseif(!isset($totalMonthPremVal[$totalCounter])) {
                                $totalMonthPremVal[$totalCounter] = 0;
                            }
                            $totCliMonthPremVal[$totalCounter] = round($totCliMonthPremVal[$totalCounter] + $value);
                            $totalMonthPremVal[$totalCounter] = round($totalMonthPremVal[$totalCounter] + $value);
                            $totalCounter++;
                        }
                    }
                    $headCounter++;
                }
                $html .='</tr>';
            }
            $clientName = $row->name;
        }
        $html .= '<tr nobr="true">
                    <td class="border normal" colspan="3"></td>';
        foreach($totCliMonthPremVal as $value)
        {
            $totCliPremValue = round($totCliPremValue + $value);
            $grandTotal = round($grandTotal + $value);
            $html .= '<td class="border normal dataTotal">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        }

        $html .= '<tr nobr="true" border="0"><td colspan="13" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true">
                    <td class="border dataTotal" colspan="3">Family Total</td>';
        foreach($totalMonthPremVal as $value)
        {
            //$totalMonthPremVal[$totalCounter] = $totalMonthPremVal[$totalCounter] + $value;
            $html .= '<td class="border normal dataTotal">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        }
        $html .='</tr>
                <tr nobr="true" border="0"><td colspan="15" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="5" border="0" class="amount default no-border dataTotal">Total Premium To Be Paid (Family): '.$this->common_lib->moneyFormatIndia($grandTotal).'</td></tr>
                <tr nobr="true" border="0"><td colspan="15" border="0" class="default no-border"><br/></td></tr>
                <tr nobr="true" border="0"><td colspan="15" border="0" class="default no-border"><br/></td></tr>
            </tbody>
        </table>';
    }
    if(!empty($lapse_rep_data))
    {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                <tbody>
                    <tr nobr="true">
                        <td class="no-border client-name" colspan="9">LAPSE POLICY DETAILS</td>
                    </tr>
                    <tr nobr="true" class="head-row">
                        <th width="110">Client Name</th>
                        <th width="130">Company Name</th>
                        <th width="130">Plan Name</th>
                        <th width="130">Policy Number</th>
                        <th width="90">Start Date</th>
                        <th width="100">Premium Amount</th>
                        <th width="100">Mode</th>
                        <th width="90">Premium Pending From</th>
                        <th width="100">Sum Assured (Freeze)</th>
                    </tr>
                </tbody>
            <tbody>';

        foreach($lapse_rep_data as $row)
        {
            if($row->commence_date) {
                $commenceDateTemp = DateTime::createFromFormat('Y-m-d',$row->commence_date);
                $commenceDateTemp = $commenceDateTemp->format('d-M-Y');
            }
            if($row->next_prem_due_date){
                $nextPremDateTemp = DateTime::createFromFormat('Y-m-d',$row->next_prem_due_date);
                $nextPremDateTemp = $nextPremDateTemp->format('d-M-Y');
            }
            $sumInsuredLap = round($sumInsuredLap + $row->amt_insured);
            $html .= '<tr nobr="true">
                        <td class="border normal">'.$row->name.'</td>
                        <td class="border normal">'.$row->ins_comp_name.'</td>
                        <td class="border normal">'.$row->plan_name.'</td>
                        <td class="border normal">'.$row->policy_num.'</td>
                        <td class="border normal">'.$commenceDateTemp.'</td>
                        <td class="amount border normal">'.$this->common_lib->moneyFormatIndia(round($row->prem_amt)).'</td>
                        <td class="border normal">'.$row->mode_name.'</td>
                        <td class="border normal">'.$nextPremDateTemp.'</td>
                        <td class="amount border normal">'.$this->common_lib->moneyFormatIndia(round($row->amt_insured)).'</td>
                    </tr>';
        }
        $html .= '<tr>
                <td colspan="8" class="border dataTotal">Family Total</td>
                <td class="amount dataTotal border">'.$this->common_lib->moneyFormatIndia(round($sumInsuredLap)).'</td>
                </tr>
            </tbody>
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
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Premium Calendar" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
