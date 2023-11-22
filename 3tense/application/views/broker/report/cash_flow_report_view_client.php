<?php
if(empty($cash_flow_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    
    $html = ''; //set html to blank
    $clientID = "";
    if(!empty($cash_flow_rep_data)) {
        $clientID = $cash_flow_rep_data[0]->client_id;
        $clientName = $cash_flow_rep_data[0]->client_name;
    } else {
        $clientID = "";
        $clientName = "";
    }
    $css = '<style type="text/css">
                table { width:100%; color:#000000; }
                table td {font-size: 12px; padding:2px; text-align:center; }
                table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                .border {border: 1px solid #4d4d4d;}
                .amount { text-align:right; padding-right:5px; text-indent: 12px; }
                .noWrap { white-space: nowrap; }
                .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 12px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .normal {font-weight: normal;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .no-border {border-width: 0px; border-color:#fff;}
                .client-name { text-align: left; font-size: 14px; font-weight: bold; }
            </style>';
    $title = '<div class="title row">Cash Flow Report of '.$clientName.'</div>
                    <br/>';
    if($cash_flow_rep_data)
    {
        $htmlVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $fdInt = 0.00;
        $fdMat = 0.00;
        $rent = 0.00;
        $insMat = 0.00;
        $insPrem = 0.00;
        $commit = 0.00;
        $lifeCover = 0.00;
        $inflow = 0.00;
        $outflow = 0.00;
        $netOutflow = 0.00;
        $fdIntTotal = 0.00;
        $fdMatTotal = 0.00;
        $rentTotal = 0.00;
        $insMatTotal = 0.00;
        $inflowTotal = 0.00;
        $insPremTotal = 0.00;
        $commitTotal = 0.00;
        $outflowTotal = 0.00;
        $netOutflowTotal = 0.00;
        $lifeCoverTotal = 0.00;

        //$htmlVal .= '<table border="1" cellpadding="4" style="text-align:center; border-width:0px;">';
        foreach($cash_flow_rep_data as $row)
        {
            if($footer) {
                $htmlVal .= '<tr nobr="true" border="0"><td colspan="10" border="0" class="default no-border"><br/></td></tr>';
                $header = true;
            }

            if($header) {
                $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true">
                            <td class="no-border dataTotal client-name">'.$row->year.'</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="90">Fixed Income Interest</th>
                            <th width="90">Fixed Income Maturity</th>
                            <th width="90">Rent Receivable</th>
                            <th width="90">Insurance Maturity</th>
                            <th width="110">Total Inflow</th>
                            <th width="90">Insurance Premium</th>
                            <th width="90">Commitments</th>
                            <th width="110">Total Outflow</th>
                            <th width="110">Net Outflow</th>
                            <th width="110">Life Cover</th>
                        </tr>';
                $header = false;
                $footer = false;
                $content = true;
            }

            if($content) {
                if(isset($row->FD)) {
                    $fd = intval($row->FD);
                    if(!empty($fd)) { $fdInt = round($row->FD,0); } else { $fdInt = 0; }
                } else {
                    $fdInt = 0;
                }
                if(isset($row->FD_Maturity)) {
                    $fd2 = intval($row->FD_Maturity);
                    if(!empty($fd2)) { $fdMat = round($row->FD_Maturity,0); } else { $fdMat = 0; }
                } else {
                    $fdMat = 0;
                }
                if(isset($row->Rent_Amount)) {
                    $r = intval($row->Rent_Amount);
                    if(!empty($r)) { $rent = round($row->Rent_Amount,0); } else { $rent = 0; }
                } else {
                    $rent = 0;
                }
                if(isset($row->Insurance)) {
                    $ins = intval($row->Insurance);
                    if(!empty($ins)) { $insMat = round($row->Insurance,0); } else { $insMat = 0; }
                } else {
                    $insMat = 0;
                }
                if(isset($row->Insurance_Premium)) {
                    $ins2 = intval($row->Insurance_Premium);
                    if(!empty($ins2)) { $insPrem = round($row->Insurance_Premium,0); } else { $insPrem = 0; }
                } else {
                    $insPrem = 0;
                }
                if(isset($row->Commitments)) {
                    $com = intval($row->Commitments);
                    if(!empty($com)) { $commit = round($row->Commitments,0); } else { $commit = 0; }
                } else {
                    $commit = 0;
                }
                if(isset($row->Life_Cover)) {
                    $lc = intval($row->Life_Cover);
                    if(!empty($lc)) { $lifeCover = round($row->Life_Cover,0); } else { $lifeCover = 0; }
                } else {
                    $lifeCover = 0;
                }

                //get the total inflow, outflow, net_outflow
                $inflow = round($fdInt + $fdMat + $rent + $insMat);
                $outflow = round($insPrem + $commit);
                $netOutflow = round($inflow - $outflow);

                //add to totals
                $fdIntTotal = round($fdIntTotal + $fdInt);
                $fdMatTotal = round($fdMatTotal + $fdMat);
                $rentTotal = round($rentTotal + $rent);
                $insMatTotal = round($insMatTotal + $insMat);
                $inflowTotal = round($inflowTotal + $inflow);
                $insPremTotal = round($insPremTotal + $insPrem);
                $commitTotal = round($commitTotal + $commit);
                $outflowTotal = round($outflowTotal + $outflow);
                $netOutflowTotal = round($netOutflowTotal + $netOutflow);
                $lifeCoverTotal = round($lifeCoverTotal + $lifeCover);

                $htmlVal .= '<tr>';
                        $htmlVal .= !empty($fdInt)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($fdInt).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($fdMat)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($fdMat).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($rent)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($rent).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($insMat)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($insMat).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($inflow)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($inflow).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($insPrem)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($insPrem).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($commit)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($commit).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($outflow)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($outflow).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($netOutflow)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($netOutflow).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= !empty($lifeCover)?'<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($lifeCover).'</td>':'<td class="border normal"></td>';
                $htmlVal .= '</tr>';
                //</table>';
                $footer = true;
                $header = true;
            }
        }

        //footer for all the totals
        $htmlVal .= '<tr nobr="true" border="0"><td colspan="10" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true">';
        $htmlVal .= !empty($fdIntTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fdIntTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($fdMatTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($fdMatTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($rentTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($rentTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($insMatTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($insMatTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($inflowTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($inflowTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($insPremTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($insPremTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($commitTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($commitTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($outflowTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($outflowTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($netOutflowTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($netOutflowTotal).'</td>':'<td class="border normal"></td>';
        $htmlVal .= !empty($lifeCoverTotal)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia($lifeCoverTotal).'</td>':'<td class="border normal"></td>';

        $htmlVal .= '</tr>
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
                    <div class="panel-gray panel-body collapse in panel-noborder" >
                        <div id="css_data"><?php echo $css; ?></div>
                        <div class="rep-logo" style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data" style="overflow-x: auto;"><?php echo $htmlVal; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Cash Flow Report" />
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
