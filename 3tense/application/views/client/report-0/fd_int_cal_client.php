<?php
if(empty($int_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $header = true; $content = false; $footer = false;
    $clientID = ""; $clientName = "";
    $adj = array(); $total = array();
    $adjCounter = 0; $subTotalIntValue = 0; $totalInt = 0;
    $clientName = $int_data[0]->client_name;
    $css = '<style type="text/css">
        table { width:100% }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:left; text-indent: 10px; }
        .amount-cal { text-align:left; text-indent: 5px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Fixed Income Calendar for '.$clientName.'';
    if(!empty($rep_date)) {
        $title .= '&nbsp;&nbsp;  (for the period of '.$rep_date['rep_date_start'].' to '.$rep_date['rep_date_end'].')';
    }
    $title .= '</div><br/>';

    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                <tbody>
                    <tr nobr="true" class="head-row">
                        <th width="70">Company Name</th>
                        <th width="70">Bank Details</th>
                        <th width="60">Ref. No.</th>
                        <th width="70">Amount Invested</th>';
    $headCounter = 0;
    foreach($int_data[0] as $key=>$value) {
        if($headCounter > 4)
            $html .= '<th width="55">'.$key.'</th>';
        $headCounter++;
    }
    $html .= '<th width="70">Total</th>';
    $html .='</tr>
            </tbody>
        <tbody>';
    foreach($int_data as $row)
    {
        $headCounter = 0;
        $totalCounter = 0;
        $html .='<tr nobr="true">';
        foreach($row as $value) {

            if($headCounter >= 5)
            {
                $val = floatval($value);
                if(!empty($val)) {
                    $html .= '<td class="amount-cal border normal" width="55px" height="40px">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
                } else {
                    $html .= '<td class="border normal" width="55px" height="40px"></td>';
                }
                if(!isset($total[$totalCounter]))
                    $total[$totalCounter] = 0;
                $total[$totalCounter] = round($total[$totalCounter] + $value);
                $subTotalIntValue = round($subTotalIntValue + $value);
                $totalCounter++;
            }
            elseif($headCounter != 0)
            {
                if($headCounter == 4 && is_numeric($value)) {
                    $html .= '<td class="amount-cal border normal">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
                } else {
                    $html .= '<td class="border normal">'.$value.'</td>';
                }
            }
            $headCounter++;
        }
        if($headCounter == 17)
        {
            $stintval = intval($subTotalIntValue);
            if(!empty($stintval)) {
                $html .= '<td class="amount-cal dataTotal border">'.$this->common_lib->moneyFormatIndia(round($subTotalIntValue)).'</td>';
            } else {
                $html .= '<td class="amount-cal dataTotal border"></td>';
            }
            $totalInt = round($subTotalIntValue + $totalInt);
            $subTotalIntValue = 0;
        }
        $html .='</tr>';
    }

    $html .= '<tr nobr="true">
                <td class="border normal" colspan="4"></td>';
    foreach($total as $value)
    {
        $totalIntValue = round($subTotalIntValue + $value);
        $val = intval($value);
        if(!empty($val)) {
            $html .= '<td class="amount-cal dataTotal border">'.$this->common_lib->moneyFormatIndia(round($value)).'</td>';
        } else {
            $html .= '<td class="dataTotal border"></td>';
        }
    }
    $tint = intval($totalInt);
    if(!empty($tint)) {
        $html .= '<td class="amount-cal dataTotal border">'.$this->common_lib->moneyFormatIndia(round($totalInt)).'</td>';
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
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Fixed Income Calendar" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
