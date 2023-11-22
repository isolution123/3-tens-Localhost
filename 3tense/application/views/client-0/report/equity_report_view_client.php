<?php
if(empty($eq_rep_data) && empty($eq_values_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    //var_dump($report_info);
    $html = ''; //set html to blank
    $clientID = "";
    $adj = array();
    $adjCounter = 0;
    if(!empty($report_info['client_id'])) {
        $clientID = $report_info['client_id'];
        $clientName = $report_info['client_name'];
    } else {
        $clientID = $eq_rep_data[0]->client_id;
        $clientName = $eq_rep_data[0]->client_id;
    }
    $css = '<style type="text/css">
                table { width:100% }
                table td {font-size: 12px; padding:2px; text-align:center; }
                table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                .border {border: 1px solid #4d4d4d;}
                .amount { text-align:left; text-indent: 20px; }
                .noWrap { white-space: nowrap; }
                .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 12px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .normal {font-weight: normal;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .no-border {border-width: 0px; border-color:#fff;}
                .client-code { text-align: left; font-size: 14px; font-weight: bold; }
                .client-name { text-align: left; font-size: 16px; font-weight: bold; }
            </style>';
    $title = '<div class="title row">Equity Portfolio of '.$clientName.'</div>
                    <br/>';
    if($eq_values_data)
    {
        $htmlEqVal = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';
        $header = true;
        $content = false;
        $footer = false;
        $client_code = $eq_values_data[0]->client_code;
        $ccSum = 0; //total for client_code
        $ccPercent = 0;
        $totalSum = 0; //overall final total
        $totalPercent = 0;
        $percent = 0;
        if(empty($total_value)) { $total_value = 0; }

        foreach($eq_values_data as $row)
        {
            $percent = 0;
            if($client_code == $row->client_code) { $footer = false; }
            else { $footer = true; }

            if($footer) {
                $htmlEqVal .= '<tr nobr="true">
                        <td class="border" colspan="3"></td>';
                        if(!empty($ccSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($ccPercent)) {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"><b>'.round($ccPercent).'%</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"></td>';
                        }
                        $htmlEqVal .= '
                        </tr>
                        <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    </tbody>';

                $ccSum = 0;
                $ccPercent = 0;

                $footer = false;
                $header = true;
            }

            if($header) {
                $htmlEqVal .= '<tr nobr="true">
                            <td class="no-border client-code" colspan="5">'.$row->client_code.'</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="210">Scrip Name</th>
                            <th width="100">Quantity</th>
                            <th width="120">Market Price</th>
                            <th width="120">Market Value</th>
                            <th width="120">% to Portfolio</th>
                        </tr>
                    </tbody>
                    <tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $percent = round(($row->value)*100/$total_value, 2);
                $ccPercent = $ccPercent + $percent;
                $ccSum = round($ccSum + $row->value);
                $totalPercent = $totalPercent + $percent;
                $totalSum = round($totalSum + $row->value);
                $htmlEqVal .= '<tr nobr="true">
                        <td class="border normal">'.$row->scrip_name.'</td>
                        <td class="border normal">'.$row->quantity.'</td>';
                        $cr = intval($row->close_rate);
                        if(!empty($cr)) {
                            $htmlEqVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->close_rate,2)).'</td>';
                        } else {
                            $htmlEqVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $val = intval($row->value);
                        if(!empty($val)) {
                            $htmlEqVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->value)).'</td>';
                        } else {
                            $htmlEqVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $htmlEqVal .= '<td class="border normal">'.$percent.'%</td>
                        </tr>';
            }

            $client_code = $row->client_code;
        }

        //last footer
        $htmlEqVal .= '<tr nobr="true">
                        <td class="border" colspan="3"></td>';
                        if(!empty($ccSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($ccPercent)) {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"><b>'.round($ccPercent).'%</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"></td>';
                        }
                $htmlEqVal .= '</tr>
                <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
            </tbody>';

        //footer for all the totals
        $htmlEqVal .= '<tr nobr="true">
                        <td class="border" colspan="3"></td>';
                        if(!empty($totalSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalSum)).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                </tbody>
            </table>';


        //set default values to variables
        $totalInv = 0;
        $totalWd = 0;
        $totalCurrVal = $totalSum;
        if(count(get_object_vars($eq_balance[0])) > 0) {
        //if(!empty((array)$eq_balance[0])) {
            $totalLedgerBal = $eq_balance[0]->balance;
        } else {
            $totalLedgerBal = 0;
        }
        $totalVal = 0;

        if($eq_rep_data)
        {
            //$htmlEqVal = '';
            $header = true;
            $content = false;
            $footer = false;
            //$clientName = "";
            //$clientName = $eq_values_data[0]->name;
            $same_client = false;
            $client_code = $eq_values_data[0]->client_code;
            $ccSum = 0; //total for client_code
            $ccPercent = 0;

            if(empty($total_value)) { $total_value = 0; }

            //header for funds table
            $htmlEqVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true" class="head-row">
                                <th width="100">Date</th>
                                <th width="120">Investment</th>
                                <th width="120">Withdrawal</th>
                                <th width="110">Current Value</th>
                                <th width="110">Ledger Balance</th>
                                <th width="110">Total Value</th>
                            </tr>
                        </tbody>
                        <tbody>';

            foreach($eq_rep_data as $row)
            {

                $temp_date = DateTime::createFromFormat('Y-m-d',$row->transaction_date);
                $transaction_date = $temp_date->format('d-M-Y');
                $totalInv = round($totalInv + $row->add);
                $totalWd = round($totalWd + $row->withdraw);
                $htmlEqVal .= '<tr nobr="true">
                        <td class="noWrap border normal">'.$transaction_date.'</td>';
                        $add = intval($row->add);
                        if(!empty($add)) {
                            $htmlEqVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->add)).'</td>';
                        } else {
                            $htmlEqVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $wdr = intval($row->withdraw);
                        if(!empty($wdr)) {
                            $htmlEqVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->withdraw)).'</td>';
                        } else {
                            $htmlEqVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $htmlEqVal .= '<td class="border normal"></td>
                        <td class="border normal"></td>
                        <td class="border normal"></td>
                    </tr>';

                //$clientName = $row->name;
                //$client_code = $row->client_code;
            }

            //calculate Total Value last column depending on conditions
            if(empty($totalWd)) {
                if(!empty($totalLedgerBal)) {
                    $totalVal = round($totalSum + $totalLedgerBal);
                } else {
                    $totalVal = round($totalSum);
                }
            } else {
                if(!empty($totalLedgerBal)) {
                    $totalVal = round($totalSum + $totalWd + $totalLedgerBal);
                } else {
                    $totalVal = round($totalSum + $totalWd);
                }
            }

            //footer for withdraw/add funds table
            $htmlEqVal .= '<tr nobr="true">
                    <td class="border normal"></td>';
                    if(!empty($totalInv)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalInv)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    if(!empty($totalWd)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalWd)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    if(!empty($totalCurrVal)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalCurrVal)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }

                    //var_dump($totalLedgerBal); var_dump($totalCurrVal); var_dump($totalSum);
                    if(!empty($totalLedgerBal)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalLedgerBal).'</td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal border"></td>';
                    }
                    if(!empty($totalVal)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalVal).'</td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal border"></td>';
                    }
                    $htmlEqVal .= '</tr>
                        <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                        <tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    </tbody>
                </table>';


            // Net Gain & Abs Gain
            if(empty($totalInv)) {
                $netGain = $totalVal;
                $absGain = 0;
            } else {
                $netGain = $totalVal - $totalInv;
                $absGain = ($netGain/$totalInv)*100;
            }


            $htmlEqVal .= '<table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';
            if(!empty($netGain)) {
                $htmlEqVal .= '<tr nobr="true">
                <td class="border" style="width: 25%;"><b>Net Gain</b></td>
                <td class="noWrap border" style="width: 25%;"><b>'.$this->common_lib->moneyFormatIndia($netGain).'</b></td>
                <td colspan="4" border="0" class="default no-border" style="padding:15px"></td>
            </tr>';
            }
            if(!empty($absGain)) {
                $htmlEqVal .= '<tr nobr="true">
                <td class="border" style="width: 25%;"><b>Absolute Gain (%)</b></td>
                <td class="noWrap border" style="width: 25%;"><b>'.round($absGain,2).' %</b></td>
                <td colspan="4" border="0" class="default no-border" style="padding:15px"></td>
            </tr>';
            }
            if(isset($xirr) && !empty($xirr)) {
                $htmlEqVal .= '<tr nobr="true">
                <td class="border" style="width: 25%;"><b>XIRR</b></td>
                <td class="noWrap border" style="width: 25%;"><b>'.round($xirr*100,2).' %</b></td>
                <td colspan="4" border="0" class="default no-border" style="padding:15px"></td>
            </tr>';
            }
            $htmlEqVal .= '</tbody>
                </table>';

        }
    }

    $html .= $css;
    $html .= $htmlEqVal;
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
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Equity/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Equity/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <form action="#" id="report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue" style="margin-left: 20%; margin-right: 20%; width: 60%;">
                    <div class="panel-gray panel-body collapse in panel-noborder">
                        <div id="css_data"><?php echo $css; ?></div>
                        <div class="rep-logo" style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $htmlEqVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Equity Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(function() {
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
    });

</script>
