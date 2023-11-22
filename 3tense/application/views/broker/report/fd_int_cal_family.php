<?php
if(empty($int_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $familyName = ""; $clientName = "";

    $totalCounter = 0;
    $header = true; $content = false; $footer = false;
    $totCliMonthIntVal = array(); $totFamMonthIntVal = array();
    $totCliIntValue = 0; $totFamIntValue = 0; $subTotalIntValue = 0; $totalInt = 0;
    $totalMonthIntVal = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $grandTotal = 0;

    $totalCounter = 0;
    //$clientName = $int_data[0]->client_name;
    $familyName = $int_data[0]->family_name;
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right; padding-right:5px; text-indent: 10px; }
        .amount-cal { text-align:right; padding-right:5px; text-indent: 5px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Fixed Income Calendar for '.$familyName.' Family';
    if(!empty($rep_date)) {
        $title .= '&nbsp;&nbsp;  (for the period of '.$rep_date['rep_date_start'].' to '.$rep_date['rep_date_end'].')';
    }
    $title .= '</div><br/>';

    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';

    foreach($int_data as $row)
    {
        if($clientName == $row->client_name) { $footer = false; $same_client = true; } else { $footer = true; $same_client = false; }
        if($footer && $clientName != '') {
            $html .= '<tr nobr="true">
                <td class="border normal" colspan="4"></td>';
            foreach($totCliMonthIntVal as $value)
            {
                $totCliIntValue = $totCliIntValue + $value;
                $grandTotal = $grandTotal + $value;
                $val = intval($value);
                if(!empty($val)) {
                    $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
                } else {
                    $html .= '<td class="dataTotal"></td>';
                }
            }
            $tcintval = intval($totCliIntValue);
            if(!empty($tcintval)) {
                $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($totCliIntValue)).'</td>';
            } else {
                $html .= '<td class="dataTotal"></td>';
            }

            $html .= '</tr>';

            if(!$same_client) {
                $totCliMonthIntVal = null;
                $html .= '</tr>
                <tr nobr="true" border="0"><td colspan="17" border="0" class="default no-border"><br/></td></tr>';
            }

            $totCliIntValue = 0;

            $footer = false;
            $header = true;
        }

        if($header) {
            if(!$same_client) {
                $html .= '<tr nobr="true">
                        <td class="no-border client-name" colspan="17">'.$row->client_name.'</td>
                    </tr>';
                $same_client = true;
            }
            $html .= '<tr nobr="true" class="head-row">
                        <th width="70">Company Name</th>
                        <th width="70">Bank Details</th>
                        <th width="60">Ref. No.</th>
                        <th width="70">Amount Invested</th>';
            $headCounter = 0;
            foreach($int_data[0] as $key=>$value) {
                if($headCounter > 5)
                    $html .= '<th width="55">'.$key.'</th>';
                $headCounter++;
            }
            $html .='<th width="70">Total</th></tr>
                    </tr>
                </thead>
            <tbody>';
            $header = false;
            $content = true;
        }
        if($content) {
            $headCounter = 0;
            $totalCounter = 0;
            $html .='<tr nobr="true">';
            foreach($row as $value) {
                if($headCounter > 1 ) {
                    if($headCounter >= 6)
                    {
                        $val = intval($value);
                        if(!empty($val)) {
                            $html .= '<td class="amount-cal border normal" width="55" height="40">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
                        } else {
                            $html .= '<td class="border normal" width="55" height="40"></td>';
                        }
                        if(empty($totCliMonthIntVal[$totalCounter])) {
                            $totCliMonthIntVal[$totalCounter] = 0;
                        }
                        if(empty($totFamMonthIntVal[$totalCounter])) {
                            $totFamMonthIntVal[$totalCounter] = 0;
                        }
                        elseif(empty($totalMonthIntVal[$totalCounter])) {
                            $totalMonthIntVal[$totalCounter] = 0;
                        }
                        $totFamMonthIntVal[$totalCounter] = $totFamMonthIntVal[$totalCounter] + $value;
                        $totCliMonthIntVal[$totalCounter] = $totCliMonthIntVal[$totalCounter] + $value;
                        $totalMonthIntVal[$totalCounter] = $totalMonthIntVal[$totalCounter] + $value;
                        $subTotalIntValue = $subTotalIntValue + $value;
                        $totalCounter++;
                    }
                    else
                    {
                        if($headCounter == 5 && is_numeric($value)) {
                            $html .= '<td class="amount-cal border normal">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
                        } else {
                            $html .= '<td class="border normal">'.$value.'</td>';
                        }
                    }

                    if($headCounter == 17)
                    {
                        $stintval = intval($subTotalIntValue);
                        if(!empty($stintval)) {
                            $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($subTotalIntValue)).'</td>';
                        } else {
                            $html .= '<td class="dataTotal"></td>';
                        }
                        $totalInt = $subTotalIntValue + $totalInt;
                        $subTotalIntValue = 0;
                    }
                }
                $headCounter++;
            }
            $html .='</tr>';
        }
        $clientName = $row->client_name;
    }

    $html .= '<tr nobr="true">
                <td class="border normal" colspan="4"></td>';
    foreach($totCliMonthIntVal as $value)
    {
        $totCliIntValue = $totCliIntValue + $value;
        //$totFamIntValue = $totFamIntValue + $value;
        $grandTotal = $grandTotal + $value;
        $val = intval($value);
        if(!empty($val)) {
            $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        } else {
            $html .= '<td class="dataTotal"></td>';
        }
    }

    $tcintval = intval($totCliIntValue);
    if(!empty($tcintval)) {
        $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($totCliIntValue)).'</td>';
    } else {
        $html .= '<td class="dataTotal"></td>';
    }
    $html .= '</tr>';

    //footer for all the totals
    $html .= '<tr nobr="true" border="0"><td colspan="17" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                <td class="border dataTotal" colspan="4">Family Total</td>';
    foreach($totFamMonthIntVal as $value)
    {
        $totFamIntValue = $totFamIntValue + $value;
        $grandTotal = $grandTotal + $value;
        $val = intval($value);
        if(!empty($val)) {
            $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        } else {
            $html .= '<td class="dataTotal border"></td>';
        }
    }

    $tfintval = intval($totFamIntValue);
    if(!empty($tfintval)) {
        $html .= '<td class="dataTotal amount-cal border">'.$this->common_lib->moneyFormatIndia(round($totFamIntValue)).'</td>';
    } else {
        $html .= '<td class="dataTotal border"></td>';
    }
    $html .= '</tr>
            </tbody>
        </table>';
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
                    <div class="panel-gray panel-body collapse in panel-noborder" >
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
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Fixed Income Calendar" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
