<?php
if(empty($commodity_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
$html = ''; //set html to blank
$clientID = "";
$adj = array();
$adjCounter = 0;
if(!empty($commodity_rep_data)) {
    $familyID = $commodity_rep_data[0]->family_id;
    $familyName = $commodity_rep_data[0]->family_name;
} else {
    $familyID = "";
    $familyName = "";
}
$css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right;padding-right:5px; text-indent: 8px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
$title = '<div class="title row">Commodity Portfolio of '.$familyName.' Family</div>
                    <br/>';
if($commodity_rep_data)
{
    $htmlVal = '';
    $header = true;
    $content = false;
    $footer = false;
    $clientID = "";
    $clientTotalInvestment = 0; //overall investment of client
    $clientTotalMarketVal = 0; //overall market value of client
    $clientTotalGain = 0; //overall unrealized gain of client
    $totalInvestment = 0; //overall investment
    $totalMarketVal = 0; //overall market value
    $totalGain = 0; //overall unrealized gain
    $first_run = true;

    $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';

    foreach($commodity_rep_data as $row)
    {
        if($clientID == $row->client_id) { $footer = false; $header = false; }
        else { $footer = true; $header = true; }

        if($footer && !$first_run) {
            $htmlVal .= '<tr nobr="true">
                            <td class="border" colspan="5"></td>';
                            if(!empty($clientTotalInvestment)) {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalInvestment).'</b></td>';
                            } else {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                            }
                            $htmlVal .= '<td class="border normal"></td>';
                            if(!empty($clientTotalMarketVal)) {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalMarketVal).'</b></td>';
                            } else {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                            }
                            if(!empty($clientTotalGain)) {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalGain).'</b></td>';
                            } else {
                                $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                            }
                            $htmlVal .= '<td class="border" colspan="3"></td>
                        </tr>
                        <tr nobr="true" border="0"><td colspan="12" border="0" class="default no-border"><br/></td></tr>
                    </tbody>';

            $footer = false;
            $header = true;

            $clientTotalInvestment = 0;
            $clientTotalMarketVal = 0;
            $clientTotalGain = 0;
        }

        if($header) {
            $htmlVal .= '<tr nobr="true">
                            <td class="no-border client-name" colspan="12">'.$row->client_name.'</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="80">Commodity</th>
                            <th width="130">Particular</th>
                            <th width="70">Purchase Date</th>
                            <th width="60">Quantity</th>
                            <th width="80">Purchase Rate</th>
                            <th width="80">Investment Amount</th>
                            <th width="80">Current Rate</th>
                            <th width="80">Current Market Value</th>
                            <th width="80">Unrealized Gain</th>
                            <th width="60">CAGR %</th>
                            <th width="60">ABS %</th>
                            <th width="120">Advisor</th>
                        </tr>
                    </tbody>
                    <tbody>';
            $header = false;
            $content = true;
        }

        if($content) {
            $clientTotalInvestment = $clientTotalInvestment + $row->total_amount;
            $clientTotalMarketVal = $clientTotalMarketVal + $row->market_value;
            $clientTotalGain = $clientTotalGain + $row->unrealised_gain;
            $totalInvestment = $totalInvestment + $row->total_amount;
            $totalMarketVal = $totalMarketVal + $row->market_value;
            $totalGain = $totalGain + $row->unrealised_gain;
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->transaction_date);
            $date = $tempDate->format('d-M-Y');
            $htmlVal .= '<tr nobr="true">
                        <td class="border normal">'.$row->item_name.'</td>
                        <td class="border normal">'.$row->quality.'</td>
                        <td class="noWrap border normal">'.$date.'</td>
                        <td class="border normal">'.$row->quantity.' '.$row->unit_name.'</td>';
                        $tr = intval($row->transaction_rate);
                        if(!empty($tr)) {
                            $htmlVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->transaction_rate, 0)).'</td>';
                        } else {
                            $htmlVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $amt = intval($row->total_amount);
                        if(!empty($amt)) {
                            $htmlVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->total_amount, 0)).'</td>';
                        } else {
                            $htmlVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $cr = intval($row->current_rate);
                        if(!empty($cr)) {
                            $htmlVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->current_rate, 0)).'</td>';
                        } else {
                            $htmlVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $mv = intval($row->market_value);
                        if(!empty($mv)) {
                            $htmlVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->market_value, 0)).'</td>';
                        } else {
                            $htmlVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $gain = intval($row->unrealised_gain);
                        if(!empty($gain)) {
                            $htmlVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->unrealised_gain, 0)).'</td>';
                        } else {
                            $htmlVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $cagr = intval($row->cagr);
                        if(!empty($cagr)) {
                            $htmlVal .= '<td class="noWrap border normal">'.round($row->cagr, 2).'</td>';
                        } else {
                            $htmlVal .= '<td class="noWrap border normal"></td>';
                        }
                        $abs = intval($row->abs);
                        if(!empty($abs)) {
                            $htmlVal .= '<td class="noWrap border normal">'.round($row->abs, 2).'</td>';
                        } else {
                            $htmlVal .= '<td class="noWrap border normal"></td>';
                        }
                        $htmlVal .= '<td class="border normal">'.$row->adviser_name.'</td>
                    </tr>';
        }

        $first_run = false;
        $clientID = $row->client_id;
    }

    //footer
    $htmlVal .= '<tr nobr="true" border="0"><td class="border" colspan="5"></td>';
                    if(!empty($clientTotalInvestment)) {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalInvestment).'</b></td>';
                    } else {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    $htmlVal .= '<td class="border normal"></td>';
                    if(!empty($clientTotalMarketVal)) {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalMarketVal).'</b></td>';
                    } else {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    if(!empty($clientTotalGain)) {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($clientTotalGain).'</b></td>';
                    } else {
                        $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    $htmlVal .= '<td class="border" colspan="3"></td>
                </tr>';

    // total family footer
    $htmlVal .= '<tr nobr="true" border="0"><td colspan="12" border="0" class="default no-border"><br/></td></tr>
            <tr nobr="true">
                <td class="border dataTotal" colspan="5">Family Total</td>';
                if(!empty($totalInvestment)) {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totalInvestment).'</b></td>';
                } else {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $htmlVal .= '<td class="border normal"></td>';
                if(!empty($totalMarketVal)) {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totalMarketVal).'</b></td>';
                } else {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                }
                if(!empty($totalGain)) {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totalGain).'</b></td>';
                } else {
                    $htmlVal .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $htmlVal .= '<td class="border" colspan="3"></td>
            </tr>
        </tbody>
    </table>';

}

$html .= $css;
$html .= $htmlVal;
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
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Commodities/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Commodities/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                        <div class="rep-logo"style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $htmlVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Commodity Portfolio" />
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
