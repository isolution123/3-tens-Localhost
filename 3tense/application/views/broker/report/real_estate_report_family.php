<?php
if(empty($re_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    //to change date to current format
    $familyName = ""; $clientName = "";
    //to calculate Real Estate amt invested and maturity amt of particular client
    $totCliInvAmt = 0; $totCliMarVal = 0; $totCliRent = 0;
    //to calculate amt invested and maturity amt of all clients
    $totInvAmt = 0; $totMarVal = 0; $totRent = 0;
    $header = true; $content = false; $footer = false;
    //check if ins data is empty then get family name from gen ins data
    if(!empty($re_rep_data))
        $familyName = $re_rep_data[0]->family_name;
    #6b6b7d
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right; padding-right:5px; text-indent: 8px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';

    $title = '<div class="title row">Real Estate Portfolio of '.$familyName.' Family</div><br/>';
    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';
    foreach($re_rep_data as $row)
    {
        $percent = 0;
        if($clientName == $row->client_name) { $footer = false; $same_client = true; } else { $footer = true; $same_client = false; }

        if($footer && $clientName != '') {
            $html .= '<tr nobr="true">
                        <td class="border normal" colspan="5"></td>';
                        if(!empty($totCliInvAmt)) {
                            $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliInvAmt).'</b></td>';
                        } else {
                            $html .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($totCliMarVal)) {
                            $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliMarVal).'</b></td>';
                        } else {
                            $html .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($totCliRent)) {
                            $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliRent).'</b></td>';
                        } else {
                            $html .= '<td class="dataTotal amount noWrap border"></td>';
                        }

                    $html .= '<td class="border normal" colspan="4"></td>
                    </tr>
                    <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>';


            $totCliInvAmt = 0; $totCliMarVal = 0; $totCliRent = 0;
            $footer = false;
            $header = true;
        }

        if($header) {
            $html .= '';
            if(!$same_client) {
                $html .= '<tr nobr="true">
                            <td class="no-border client-name" colspan="12">'.$row->client_name.'</td>
                        </tr>';
                $same_client = true;
            }
            $html .= '<tr nobr="true" class="head-row">
                    <th width="100"><b>Property Name</b></th>
                    <th width="70" class="noWrap"><b>Purchase Date</b></th>
                    <th width="70" class="noWrap"><b>Type</b></th>
                    <th width="130"><b>Location</b></th>
                    <th width="80"><b>Area</b></th>
                    <th width="80" class="noWrap"><b>Amount Invested</b></th>
                    <th width="80" class="noWrap"><b>Market Value</b></th>
                    <th width="80" class="noWrap"><b>Rent Amount</b></th>
                    <th width="70" class="noWrap"><b>Updated On</b></th>
                    <th width="60" class="noWrap"><b>CAGR %</b></th>
                    <th width="60" class="noWrap"><b>ABS %</b></th>
                    <th width="100"><b>Advisor</b></th>
                </tr>
            </tbody>
            <tbody>';
            $header = false;
            $content = true;
        }

        if($content) {
            //$appreciation = ((($row->current_rate - $row->transaction_rate) * $row->property_area) + $row->amount);
            $appreciation = $row->current_rate * $row->property_area;
            $totCliMarVal = round($totCliMarVal + $appreciation);
            $totCliInvAmt = round($totCliInvAmt + $row->amount);
            $totCliRent = round($totCliRent + $row->rent_amount);
            $totInvAmt = round($totInvAmt + $row->amount);
            $totMarVal = round($totMarVal + $appreciation);
            $totRent = round($totRent + $row->rent_amount);
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->transaction_date);
            $date = $tempDate->format('d-M-Y');
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->property_updated_on);
            $date2 = $tempDate2->format('d-M-Y');
            $html .= '<tr nobr="true">
                <td class="border normal">'.$row->property_name.'</td>
                <!--<td>'.$row->transaction_date.'</td>-->
                <td class="noWrap border normal">'.$date.'</td>
                <td class="border normal">'.$row->property_type_name.'</td>
                <td class="border normal">'.$row->property_location.'</td>
                <td class="noWrap border normal">'.round($row->property_area, 0).' '.$row->unit_name.'</td>';
                $amt = intval($row->amount);
                if(!empty($amt)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->amount, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $appr = intval($appreciation);
                if(!empty($appr)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($appreciation, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $rent = intval($row->rent_amount);
                if(!empty($rent)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->rent_amount, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $html .= '<td class="noWrap border normal">'.$date2.'</td>';
                $cagr = intval($row->cagr);
                if(!empty($cagr)) {
                    $html .= '<td class="noWrap border normal">'.$row->cagr.'</td>';
                } else {
                $html .= '<td class="noWrap border normal"></td>';
                }
                $abs = intval($row->abs);
                if(!empty($abs)) {
                    $html .= '<td class="noWrap border normal">'.$row->abs.'</td>';
                } else {
                    $html .= '<td class="noWrap border normal"></td>';
                }
                $html .= '<td class="border normal">'.$row->adviser_name.'</td>
            </tr>';
        }
        $clientName = $row->client_name;
    }
    $html .= '<tr nobr="true">
                <td class="border" colspan="5"></td>';
                if(!empty($totCliInvAmt)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliInvAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                if(!empty($totCliMarVal)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliMarVal).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                if(!empty($totCliRent)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliRent).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border normal" colspan="4"></td>
            </tr>';
    $html .= '<tr nobr="true" border="0"><td colspan="12" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                <td class="dataTotal border" colspan="5">Family Total</td>';
                if(!empty($totInvAmt)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totInvAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                if(!empty($totMarVal)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totMarVal).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                if(!empty($totRent)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totRent).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border" colspan="4"></td>
            </tr>
        </tbody>
    </table>';
}
?>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <!--<h1>Real Estate Report (Family)</h1>-->
            <!--<img src="<?/*=base_url('uploads/brokers/0001/logo.png')*/?>" />-->
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Real_estate/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Real_estate/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <form action="#" id="report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <!--<div class="panel-heading">
                        <!--<h4>Real Estate Report View (Family)</h4>--
                    </div>-->
                    <div class="panel-gray panel-body collapse in panel-noborder">
                        <!--<img src="<?/*=base_url('uploads/brokers/0001/logo.png')*/?>" /><br/><br/>-->

                        <div id="css_data" name="cssData"><?php echo $css; ?></div>
                        <div class="rep-logo" style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?> Family" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
