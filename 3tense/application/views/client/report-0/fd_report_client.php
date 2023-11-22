<?php
if(empty($fd_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    $adj = array();
    $adjCounter = 0; $totalMatAmt = 0; $totalAmtInv=0;
    if(!empty($report_info)) {
        $clientName = $fd_rep_data[0]->client_name;
    }
    $css = '<style type="text/css">
        table { width:100% }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:left; text-indent: 10px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Fixed Income Portfolio of '.$clientName.'</div>
                    <br/>';
    if($fd_rep_data)
    {
        $header = true;
        $content = false;
        $footer = false;
        $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
        <tbody>
            <tr nobr="true" class="head-row">
                <th width="70">Asset Class</th>
                <th width="80">Investment Date</th>
                <th width="80">Company</th>
                <th width="80">Ref. No.</th>
                <th width="60">Interest Rate</th>
                <th width="60">Term (Months)</th>
                <th width="100">Amount Invested</th>
                <th width="80">Interest Payment</th>
                <th width="80">Maturity Date</th>
                <th width="100">Maturity Amount</th>
                <th width="90">Nominee Name</th>
                <th width="100">Advisor Name</th>
            </tr>
        </tbody>
        <tbody>';
        foreach($fd_rep_data as $row)
        {
            if($row->adjustment_flag == 'Yes')
            {
                $adj[$adjCounter]['ref_number'] = $row->ref_number;
                $adj[$adjCounter]['adjustment_ref_number'] = $row->adjustment_ref_number;
                $adj[$adjCounter]['adj'] = $row->adjustment;
                $adjCounter++;
            }
            $totalMatAmt = round($totalMatAmt + $row->maturity_amount);
            $totalAmtInv = round($totalAmtInv + $row->amount_invested);
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->issued_date);
            $date = $tempDate->format('d-M-Y');
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->maturity_date);
            $date2 = $tempDate2->format('d-M-Y');
            $html .= '<tr nobr="true">
                <td class="border normal">'.$row->type_of_investment.'</td>
                <td class="noWrap border normal">'.$date.'</td>
                <td class="border normal">'.$row->issuing_authority.'</td>
                <td class="border normal">'.$row->ref_number.'</td>
                <td class="noWrap border normal">'.$row->interest_rate.'</td>
                <td class="noWrap border normal">'.$row->Term.'</td>';
                $amtinv = intval($row->amount_invested);
                if(!empty($amtinv)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->amount_invested, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $html .= '<td class="border normal">'.$row->fd_method.'</td>
                <td class="noWrap border normal">'.$date2.'</td>';
                $matamt = intval($row->maturity_amount);
                if(!empty($matamt)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->maturity_amount, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $html .= '<td class="border normal">'.$row->nominee.'</td>
                <td class="border normal">'.$row->broker_details.'</td>
                </tr>';
        }
        //footer for all the totals
        $html .= '<tr nobr="true">
                    <td class="border normal" colspan="6"></td>';
                    $tamtinv = intval($totalAmtInv);
                    if(!empty($tamtinv)) {
                        $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totalAmtInv).'</b></td>';
                    } else {
                        $html .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    $html .= '<td class="border normal" colspan="2"></td>';
                    $tmatamt = intval($totalMatAmt);
                    if(!empty($tmatamt)) {
                        $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totalMatAmt).'</b></td>';
                    } else {
                        $html .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    $html .= '<td class="border normal" colspan="2"></td>
                    </tr>
                    <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
                    <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
                    <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
                </tbody>
            </table>';
        }
        if(!empty($adj))
        {
            $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true">
                            <td class="no-border client-name" colspan="12">ADJUSTMENT DETAILS</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="250">Reference Number</th>
                            <th width="250">Target Reference Number</th>
                            <th width="480">Adjustment</th>
                        </tr>
                    </tbody>
                <tbody>';
            foreach($adj as $row)
            {
                $html .= '<tr nobr="true">
                            <td class="border normal">'.$row['ref_number'].'</td>
                            <td class="border normal">'.$row['adjustment_ref_number'].'</td>
                            <td class="border normal">'.$row['adj'].'</td>
                        </tr>';
            }
            $html .= '</tbody>
                        </table>';
        }
    }
    $html .= $css;
?>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Fixed_deposits/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Fixed_deposits/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Fixed Income Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
    /*$html .= '<style type="text/css">
                    table td, table th {font-size: 8px;}
                    .title { line-height:28px; font-size:15px; font-weight:bold; text-align:center; border:2px double black; }
                    .info { font-size: 12px; font-weight: lighter; border:none; }
                    .head-row { background-color: #003F7D; color: #fff; font-weight:bold}
                    .dataTotal {font-weight: bold}
                </style>
                <div class="title">Fixed Deposit Portfolio of '.$clientID.'</div>
                <br/><br/>';
    if($fd_rep_data)
    {
        $pdf->AddPage();
        // add client info to page
        $htmlFD = '';
        $htmlFD .= '<table border="1" cellpadding="4" style="text-align:center;">
            <thead>
                <tr class="head-row">
                    <th>Asset Class</th>
                    <th>Investment Date</th>
                    <th>Company</th>
                    <th>Reference Number</th>
                    <th>Interest Rate</th>
                    <th>Term (Months)</th>
                    <th>Amount Invested</th>
                    <th>Interest Payment</th>
                    <th>Maturity Date</th>
                    <th>Maturity Amount</th>
                    <th>Nominee Name</th>
                    <th>Advisor Name</th>
                </tr>
            </thead>
        <tbody>';
        foreach($fd_rep_data as $row)
        {
            if($row->adjustment_flag == 'Yes')
            {
                $adj[$adjCounter]['ref_number'] = $row->ref_number;
                $adj[$adjCounter]['adjustment_ref_number'] = $row->adjustment_ref_number;
                $adj[$adjCounter]['adj'] = $row->adjustment;
                $adjCounter++;
            }
            $totalMatAmt = $totalMatAmt + $row->maturity_amount;
            $totalAmtInv = $totalAmtInv + $row->amount_invested;
            $htmlFD .= '<tr>
                <td>'.$row->type_of_investment.'</td>
                <td>'.$row->issued_date.'</td>
                <td>'.$row->issuing_authority.'</td>
                <td>'.$row->ref_number.'</td>
                <td>'.$row->interest_rate.'</td>
                <td>'.$row->Term.'</td>
                <td>'.$row->amount_invested.'</td>
                <td>'.$row->fd_method.'</td>
                <td>'.$row->maturity_date.'</td>
                <td>'.$row->maturity_amount.'</td>
                <td>'.$row->nominee.'</td>
                <td>'.$row->broker_details.'</td>
                </tr>';
        }
        $htmlFD .= '<tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="dataTotal">'.$totalAmtInv.'</td>
                <td></td>
                <td></td>
                <td class="dataTotal">'.$totalMatAmt.'</td>
                <td></td>
                <td></td>
        </tr>';
        $htmlFD .= '</tbody>
                    </table>';
        $pdf->writeHTML($html.$htmlFD, true, false, true, false, '');
        $pdf->lastPage();
    }

    if(!empty($adj))
    {
        $pdf->AddPage();
        $htmlAdj = '<h3>ADJUSTMENT DETAILS</h3><br/>
            <table border="1" cellpadding="4" style="text-align:center;">
                <thead>
                    <tr class="head-row">
                        <th style="width:20%">Reference Number</th>
                        <th style="width:20%">Target Reference Number</th>
                        <th style="width:60%">Adjustment</th>
                    </tr>
                </thead>
            <tbody>';
        foreach($adj as $row)
        {
            $htmlAdj .= '<tr>
                        <td style="width:20%">'.$row['ref_number'].'</td>
                        <td style="width:20%">'.$row['adjustment_ref_number'].'</td>
                        <td style="width:60%">'.$row['adj'].'</td>
                    </tr>';
        }
        $htmlAdj .= '</tbody>
                    </table>
                    <br/><br/>';
        // output the HTML content
        $pdf->writeHTML($html.$htmlAdj, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();
    }*/
?>
