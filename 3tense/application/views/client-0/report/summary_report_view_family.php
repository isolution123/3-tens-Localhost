<?php
if(empty($summary_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    if(!empty($summary_rep_data)) {
        $familyID = $summary_rep_data[0]->family_id;
        $familyName = $summary_rep_data[0]->family_name;
    } else {
        $clientID = "";
        $clientName = "";
    }
    $css = '<style type="text/css">
                table { width:100% }
                table td {font-size: 12px; padding:2px; text-align:center; }
                table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                .border {border: 1px solid #4d4d4d;}
                .amount { text-align:left; text-indent: 15px; }
                .noWrap { white-space: nowrap; }
                .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 12px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .normal {font-weight: normal;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .no-border {border-width: 0px; border-color:#fff;}
                .client-name { text-align: left; font-size: 14px; font-weight: bold; }
                .bigger {font-weight: bold; font-size:14px;}
            </style>';
    $title = '<div class="title row">Summary Report of '.$familyName.'</div>
                    <br/>';
    if($summary_rep_data)
    {
        $htmlVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $clientTotal = 0; //overall amount
        //family totals
        $famInsTotal = 0;
        $famFDTotal = 0;
        $famMFTotal = 0;
        $famEquityTotal = 0;
        $famRETotal = 0;
        $famCommodityTotal = 0;
        $familyTotal = 0;


        foreach($summary_rep_data as $row)
        {
            $clientTotal = round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity);
            if($header) {
                $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true" class="head-row">
                            <th width="90">Client Name</th>
                            <th width="110">Insurance</th>
                            <th width="110">Fixed Income</th>
                            <th width="110">Mutual Funds</th>
                            <th width="110">Equity</th>
                            <th width="110">Real Estate</th>
                            <th width="110">Commodity</th>
                            <th width="120">Total Portfolio</th>
                            <th width="110">Life Cover</th>
                        </tr>
                    </tbody>
                    <tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $htmlVal .= '<tr nobr="true">';
                        $htmlVal .= '<td class="border normal">'.$row->client_name.'</td>';

                        $ins = intval($row->insurance_inv);
                        $htmlVal .= !empty($ins)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->insurance_inv,0)).'</td>':'<td class="border normal"></td>';
                        // $htmlVal .= !empty($row->insurance_fund)?'<td>'.$this->common_lib->moneyFormatIndia(round($row->insurance_fund,2)).'</td>':'<td></td>';
                        $fd = intval($row->fixed_deposit);
                        $htmlVal .= !empty($fd)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->fixed_deposit,0)).'</td>':'<td class="border normal"></td>';
                        $mf = intval($row->mutual_fund);
                        $htmlVal .= !empty($mf)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->mutual_fund,0)).'</td>':'<td class="border normal"></td>';
                        $eq = intval($row->equity);
                        $htmlVal .= !empty($eq)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->equity,0)).'</td>':'<td class="border normal"></td>';
                        $prop = intval($row->property);
                        $htmlVal .= !empty($prop)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->property,0)).'</td>':'<td class="border normal"></td>';
                        $com = intval($row->commodity);
                        $htmlVal .= !empty($com)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->commodity,0)).'</td>':'<td class="border normal"></td>';

                        $htmlVal .= '<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($clientTotal).'</td>';

                $lc = intval($row->life_cover);
                $htmlVal .= !empty($lc)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->life_cover,0)).'</td>':'<td class="border normal"></td>';
                    $htmlVal .= '</tr>';

                $famInsTotal = round($famInsTotal + $row->insurance_inv);
                $famFDTotal = round($famFDTotal + $row->fixed_deposit);
                $famMFTotal = round($famMFTotal + $row->mutual_fund);
                $famEquityTotal = round($famEquityTotal + $row->equity);
                $famRETotal = round($famRETotal + $row->property);
                $famCommodityTotal = round($famCommodityTotal + $row->commodity);
                $familyTotal = round($familyTotal + $clientTotal);
            }
        }
        //footer with totals
        $htmlVal .= '<tr nobr="true">';
            $htmlVal .= '<td class="dataTotal border">Total</td>';
            $htmlVal .= !empty($famInsTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famInsTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famFDTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famFDTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famMFTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famMFTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famEquityTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famEquityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famRETotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famRETotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famCommodityTotal)?'<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia($famCommodityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= '<td class="dataTotal border bigger">'.$this->common_lib->moneyFormatIndia($familyTotal).'</td>';
            $htmlVal .= '<td class="border"></td>
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
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Final_reports/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Final_reports/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                        <div class="row" id="report_data"  style="overflow-x : auto;"><?php echo $htmlVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Summary Report" />
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
