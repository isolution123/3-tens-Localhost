<?php
if(empty($prem_data) && empty($lapse_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $total = array();
    $nextPremDateTemp = ''; $commenceDateTemp = "";
    $totalCounter = 0; $sumInsuredLap = 0; $TotalPremValue = 0;
    if(!empty($prem_data))
        $clientName = $prem_data[0]->name;
    else
        $clientName = $lapse_rep_data[0]->name;
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
    $title = '<div class="title row">Premium Calendar for '.$clientName.'';
    if(!empty($rep_date)) {
        $title .= '&nbsp;&nbsp;  (for the period of '.$rep_date['rep_date_start'].' to '.$rep_date['rep_date_end'].')';
    }
    $title .= '</div><br/>';

    if(!empty($prem_data))
    {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                    <tr nobr="true" class="head-row">
                        <th width="100">Company Name</th>
                        <th width="100">Policy No.</th>
                        <th width="60">Paid Up</th>';
        $headCounter = 0;
        foreach($prem_data[0] as $key=>$value) {
            if($headCounter > 4)
                $html .= '<th width="60">'.$key.'</th>';
            $headCounter++;
        }
        $html .='</tr>
                </tbody>
            </tbody>';
        foreach($prem_data as $row)
        {
            $headCounter = 0;
            $totalCounter = 0;
            if($row->adjustment == 1) {
                $adj = '<br/>(ADJ)';
            } else {
                $adj = '';
            }
            $html .='<tr nobr="true">';
            foreach($row as $value) {
                if($headCounter != 0 && $headCounter != 3)
                {
                    if($headCounter >= 5)
                    {
                        $val = floatval($value);
                        if(!empty($val)) {
                            $html .= '<td class="border normal" width="60px" height="40px">'.$this->common_lib->moneyFormatIndia(round($value)).$adj.'</td>';
                        } else {
                            $html .= '<td class="border normal" width="60px" height="40px"></td>';
                        }
                        if(!isset($total[$totalCounter]))
                            $total[$totalCounter] = 0;
                        $total[$totalCounter] = round($total[$totalCounter] + $value);
                        $totalCounter++;
                    }
                    else
                    {
                        if(!empty($value)) {
                            if(is_numeric($value) && $headCounter > 2)
                                $html .= '<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($value)).$adj.'</td>';
                            else
                                $html .= '<td class="border normal">'.$value.'</td>';
                        } else {
                            $html .= '<td class="border normal"></td>';
                        }
                    }
                }
                $headCounter++;
            }
            $html .='</tr>';
        }
        $html .= '<tr nobr="true">
                    <td class="border normal" colspan="3"></td>';
        foreach($total as $value)
        {
            $TotalPremValue = round($TotalPremValue + $value);
            $html .= '<td class="border normal dataTotal">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        }
        $html .= '</tr>
            <tr nobr="true" border="0"><td colspan="15" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true" border="0"><td colspan="5" border="0" class="amount default no-border dataTotal">Total Premium To Be Paid: '.$this->common_lib->moneyFormatIndia($TotalPremValue).'</td></tr>
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
                        <td class="no-border client-name" colspan="8">LAPSE POLICY DETAILS</td>
                    </tr>
                    <tr nobr="true" class="head-row">
                        <th width="150">Company Name</th>
                        <th width="160">Plan Name</th>
                        <th width="150">Policy Number</th>
                        <th width="100">Start Date</th>
                        <th width="110">Premium Amount</th>
                        <th width="100">Mode</th>
                        <th width="100">Premium Pending From</th>
                        <th width="110">Sum Assured (Freeze)</th>
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
                <td colspan="7" class="border normal">
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
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Premium Calendar" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
