<?php
if(empty($ledger_rep_inflow_data) && empty($ledger_rep_outflow_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    if(!empty($report_info['id']) && !empty($report_info['name'])) {
        $clientID = $report_info['id'];
        $clientName = $report_info['name'];
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
            </style>';
    $title = '<div class="title row">Ledger Report of '.$clientName.'</div>
                    <br/>';

    $totalInflow = 0.00;
    $totalOutflow = 0.00;
    $htmlVal = '';
    if($ledger_rep_inflow_data)
    {
        $header = true;
        $content = false;
        $footer = false;
        $totalInflow = 0.00;

        //header of table
        $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true">
                            <td colspan="3" class="no-border client-name">INFLOW DETAILS</th>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="80">Date</th>
                            <th width="780">Particular</th>
                            <th width="120">Amount</th>
                        </tr>
                    </tbody>
                    <tbody>';

        foreach($ledger_rep_inflow_data as $row)
        {
            $tempDate = DateTime::createFromFormat('Y-m-d',$row->comp_date);
            $date = $tempDate->format('d-M-Y');
            $htmlVal .= '<tr>';
                    $htmlVal .= '<td class="border normal">'.$date.'</td>';
                    $htmlVal .= '<td class="border normal" style="text-align: left;">'.$row->Particular.'</td>';
                    $amt = intval($row->amount);
                    if(!empty($amt)) {
                        $htmlVal .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->amount)).'</td>';
                    } else {
                        $htmlVal .= '<td class="border normal amount"></td>';
                    }
            $htmlVal .= '</tr>';

            $totalInflow = round($totalInflow + $row->amount);
        }

        //footer for total
        $htmlVal .= '<tr>
                        <td colspan="2" class="border">
                        <td colspan="1" class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($totalInflow).'</td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                </tbody>
            </table>';
    }

    if($ledger_rep_outflow_data)
    {
        $header = true;
        $content = false;
        $footer = false;
        $totalOutflow = 0.00;

        //header of table
        $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true">
                            <td colspan="8" class="no-border client-name">OUTFLOW DETAILS</th>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="80">Date</th>
                            <th width="780">Particular</th>
                            <th width="120">Amount</th>
                        </tr>
                    </tbody>
                    <tbody>';

        foreach($ledger_rep_outflow_data as $row)
        {
            $tempDate = DateTime::createFromFormat('Y-m-d',$row->comp_date);
            $date = $tempDate->format('d-M-Y');
            $htmlVal .= '<tr>';
            $htmlVal .= '<td class="border normal">'.$date.'</td>';
            $htmlVal .= '<td class="border normal" style="text-align: left;">'.$row->Particular.'</td>';
            $amt = intval($row->amount);
            if(!empty($amt)) {
                $htmlVal .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($row->amount)).'</td>';
            } else {
                $htmlVal .= '<td class="border normal amount"></td>';
            }
            $htmlVal .= '</tr>';

            $totalOutflow = round($totalOutflow + $row->amount);
        }

        //footer for total
        $htmlVal .= '<tr>
                        <td colspan="2" class="border">
                        <td colspan="1" class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($totalOutflow).'</td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
                </tbody>
            </table>';
    }

    $htmlVal .= '<p class="dataTotal">Net Investment :  '.$this->common_lib->moneyFormatIndia(round($totalInflow - $totalOutflow)).'</p>';

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
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $htmlVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Ledger Report" />
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
