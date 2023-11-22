<?php
if(empty($asset_rep_data) && empty($liability_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $totalInsAmt = 0; $totalExpMat = 0; $totalLiabInsAmt = 0; $totalLoan = 0;
    $html = ''; $htmlCss=''; //set html to blank
    if(!empty($asset_rep_data))
        $familyName = $asset_rep_data[0]->family_name;
    else
        $familyName = $liability_rep_data[0]->family_name;
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
    $title = '<div class="title row" >Asset and Liability of '.$familyName.' Family</div><br/>';
    if($asset_rep_data)
    {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';
        $html .= '<tr nobr="true">
                    <td class="no-border client-name" colspan="12">Assets Details</td>
                </tr>';
        $html .= '<tr nobr="true" class="head-row" style="overflow-x: scroll;">
                    <th width="100">Client Name</th>
                    <th width="100">Product</th>
                    <th width="100">Company</th>
                    <th width="110">Scheme</th>
                    <th width="80">Ref. No.</th>
                    <th width="80">Type</th>
                    <th width="70">Start Date</th>
                    <th width="70">End Date</th>
                    <th width="80" class="noWrap">Installment Amount</th>
                    <th width="80" class="noWrap">Expected Maturity Value</th>
                    <th width="110">Goal</th>
            </tr>
        </tbody>
        <tbody>';
        foreach($asset_rep_data as $row)
        {
            $totalInsAmt = round($totalInsAmt + $row->installment_amount);
            $totalExpMat = round($totalExpMat + $row->expected_mat_value);
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->start_date);
            $date = $tempDate->format('d-M-Y');
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->end_date);
            $date2 = $tempDate2->format('d-M-Y');
            $html .= '<tr>
                <td class="border normal">'.$row->client_name.'</td>
                <td class="border normal">'.$row->product_name.'</td>
                <td class="border normal">'.$row->company_name.'</td>
                <td class="border normal">'.$row->scheme_name.'</td>
                <td class="border normal">'.$row->ref_number.'</td>
                <td class="border normal">'.$row->type_name.'</td>
                <td class="noWrap border normal">'.$date.'</td>
                <td class="noWrap border normal">'.$date2.'</td>';
                $inst = intval($row->installment_amount);
                if(!empty($inst)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->installment_amount, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $mat = intval($row->expected_mat_value);
                if(!empty($mat)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->expected_mat_value), 0).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $html .= '<td class="border normal">'.$row->goal.'</td>
                </tr>';
        }
        $html .= '<tr>
                <td class="border normal" colspan="8"></td>';
                $tins = intval($totalInsAmt);
                if(!empty($tins)) {
                    $html .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalInsAmt).'</td>';
                } else {
                    $html .= '<td></td>';
                }
                $tmat = intval($totalExpMat);
                if(!empty($tmat)) {
                    $html .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalExpMat).'</td>';
                } else {
                    $html .= '<td></td>';
                }
                $html .= '<td class="border normal"></td>
                </tr>
            </tbody>
        </table><br/><br/><br/>';
    }
    if($liability_rep_data)
    {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                    <tr nobr="true">
                    <td class="no-border client-name" colspan="12">Liabilities Details</td>
                </tr>';
        $html .= '<tr nobr="true" class="head-row" style="overflow-x: scroll;">
                    <th width="90">Client Name</th>
                    <th width="90">Product</th>
                    <th width="90">Company</th>
                    <th width="90">Scheme</th>
                    <th width="80">Ref. No.</th>
                    <th width="80">Type</th>
                    <th width="70">Start Date</th>
                    <th width="70">End Date</th>
                    <th width="50">Interest Rate</th>
                    <th width="70" class="noWrap">Installment Amount</th>
                    <th width="80" class="noWrap">Loan Amount</th>
                    <th width="100">Goal</th>
            </tr>
        </tbody>
        <tbody>';
        foreach($liability_rep_data as $row)
        {
            $totalLiabInsAmt = round($totalLiabInsAmt + $row->installment_amount);
            $totalLoan = round($totalLoan + $row->total_liability);
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->start_date);
            $date = $tempDate->format('d-M-Y');
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->end_date);
            $date2 = $tempDate2->format('d-M-Y');
            $html .= '<tr>
                <td class="border normal">'.$row->client_name.'</td>
                <td class="border normal">'.$row->product_name.'</td>
                <td class="border normal">'.$row->company_name.'</td>
                <td class="border normal">'.$row->scheme_name.'</td>
                <td class="border normal">'.$row->ref_number.'</td>
                <td class="border normal">'.$row->type_name.'</td>
                <td class="noWrap border normal">'.$date.'</td>
                <td class="noWrap border normal">'.$date2.'</td>
                <td class="noWrap border normal">'.$row->interest_rate.'%</td>';
                $inst = intval($row->installment_amount);
                if(!empty($inst)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->installment_amount, 0)).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $tliab = intval($row->total_liability);
                if(!empty($tliab)) {
                    $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->total_liability), 0).'</td>';
                } else {
                    $html .= '<td class="amount noWrap border normal"></td>';
                }
                $html .= '<td class="border normal">'.$row->particular.'</td>
                </tr>';
        }
        $html .= '<tr>
                    <td class="border normal" colspan="9"></td>';
                    $tlins = intval($totalLiabInsAmt);
                    if(!empty($tlins)) {
                        $html .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalLiabInsAmt).'</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $tloan = intval($totalLoan);
                    if(!empty($tloan)) {
                        $html .= '<td class="dataTotal amount noWrap border">'.$this->common_lib->moneyFormatIndia($totalLoan).'</td>';
                    } else {
                        $html .= '<td></td>';
                    }
        $html .= '<td class="border normal"></td>
                </tr>
            </tbody>';
    }
}
?>
<div id="page-content" style="margin: 0;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group hidden-xs">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-sm"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Assets_liabilities/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Assets_liabilities/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
