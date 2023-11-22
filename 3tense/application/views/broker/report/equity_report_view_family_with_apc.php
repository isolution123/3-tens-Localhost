<?php
if(empty($eq_rep_data) && empty($eq_values_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    
$html = ''; //set html to blank
$familyID = "";
$familyName = "";
$adj = array();
$chart_data=array();
$char_label=array();
$adjCounter = 0;
if(!empty($report_info)) {
    $familyID = $report_info['family_id'];
    $familyName = $report_info['family_name'];

} else {
    $familyID = "";
    $familyName = "";
}
$css = '<style type="text/css">
            table { width:100%; color:#000000; }
            table td {font-size: 12px; padding:2px; text-align:center; }
            table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
            .border {border: 1px solid #4d4d4d;}
            .amount { text-align:right; padding-right:2px; text-indent: 0px; }
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
$title = '<div class="title row">Equity Portfolio of '.$familyName.' Family</div>
                    <br/>';
if($eq_values_data)
{
    $htmlEqVal = '<table border="0" cellpadding="3" style="text-align:center; border-width:0px;">
                    <tbody>';
    $header = true;
    $content = false;
    $footer = false;
    $clientName = "";
    //$clientName = $eq_values_data[0]->name;
    $same_client = false;
    $client_code = $eq_values_data[0]->client_code;
    $ccSum = 0; //total for client_code
    $ccPSum = 0; //total for client_code
    $ccPProfit = 0; //total for client_code
    $ccPercent = 0;
    $totalSum = 0; //overall final total
    $totalPSum = 0; //total for client_code
    $totalPProfit = 0; //total for client_code
    $totalPercent = 0;
    $percent = 0;
    $TotalProfitPer=0;
    if(empty($total_value)) { $total_value = 0; }

    foreach($eq_values_data as $row)
    {
        $percent = 0;
        if($clientName == $row->name) { $footer = false; $same_client = true; } else { $footer = true; $same_client = false; }
        if($client_code == $row->client_code) { $footer = false; } else { $footer = true; }

        if($footer) {

            $htmlEqVal .= '<tr nobr="true">
                        <td class="border" colspan="3"></td>';
                        if(!empty($ccPSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccPSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border">0.00</td>';
                        }
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        
                        if(!empty($ccSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($ccPProfit)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($ccPProfit)).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($TotalProfitPer)) {
                            $htmlEqVal .= '<td class="dataTotal  noWrap border">'.round(((100*$ccPProfit)/$ccPSum),2).'%</td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal  noWrap border"></td>';
                        }
                        
                        
                        if(!empty($ccPercent)) {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"><b>'.round($ccPercent).'%</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"></td>';
                        }
                        $htmlEqVal .= '<td class="dataTotal noWrap border"></td><td class="dataTotal noWrap border"></td></tr>';
                        
            if(!$same_client) {
                $htmlEqVal .= '<tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>';
            }
            $htmlEqVal .= '<tr nobr="true" border="0"><td colspan="5" border="0" class="default no-border"><br/></td></tr>
                    </tbody>';

            $ccSum = 0;
            $ccPSum=0;
            $ccPercent = 0;
            $ccPProfit=0;
            $TotalProfitPer=0;
            
            $footer = false;
            $header = true;
        }

        if($header) {
            if(!$same_client) {
                $htmlEqVal .= '<tr nobr="true">
                                <td class="no-border client-code" colspan="5">'.$row->name.'</td>
                            </tr>';
                $same_client = true;
            }
            $htmlEqVal .= '<tr nobr="true">
                            <td class="no-border client-code " colspan="5">'.$row->client_code . ' - '.$row->trading_broker_name.'</td>
                        </tr>
                        <tr nobr="true" class="head-row">';

                         $htmlEqVal .='<th width="130">Scrip Name</th>
                                <th width="70">Quantity</th>
                                
                                <th width="70">Purchase Price</th>
                                <th width="90">Purchase value</th>
	                            
	                            <th width="70">Market Price</th>
                                <th width="90">Market Value</th>
                                
                                <th width="90">Profit</th>
                                <th width="70">% gain</th>
	
                                <th width="70">% to Portfolio</th>';
                            
                         $htmlEqVal.='<th width="120">Industry</th>
                            <th width="100">Cap</th>';
                            
                        $htmlEqVal .='</tr>
                    </tbody>
                    <tbody>';
            $header = false;
            $content = true;
        }

        if($content) {
            $percent = round(($row->value)*100/$total_value, 2);
            $ccPercent = ($ccPercent + $percent);
            $ccSum = round($ccSum + $row->value);
            $ccPSum= round($ccPSum + $row->purchase_value);
            $totalPercent = ($totalPercent + $percent);
            $totalSum = round($totalSum + $row->value);
            $totalPSum= round($totalPSum + $row->purchase_value);
            
            
            $htmlEqVal .= '<tr nobr="true">
                        <td class="border normal">'.$row->scrip_name.'</td>
                        <td class="border normal amount">'.$row->quantity.'</td>
                        <td class="border normal amount">'.$row->apc.'</td>
                        <td class="border normal amount">'.$row->purchase_value.'</td>';
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
                        $profit=($row->value-$row->purchase_value);
                        if(!empty($profit)) {
                            $ccPProfit=$ccPProfit+$profit;
                            $totalPProfit=$totalPProfit+$profit;
                            if($profit>0)
                            {
                            $htmlEqVal .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($profit)).'</td>';
                            }
                            else
                            {
                            $htmlEqVal .= '<td class="amount noWrap border normal"> - '.$this->common_lib->moneyFormatIndia(round(abs($profit))).'</td>';    
                            }
                        }
                        else
                        {
                            $htmlEqVal .= '<td class="amount noWrap border normal"></td>';
                        }
                        $pval = intval($row->purchase_value);
                        if(!empty($profit) && !empty($pval)) {
                            $profitPer=round(((100*$profit)/$row->purchase_value),2);
                            $TotalProfitPer=$TotalProfitPer+$profitPer;
                            if($profitPer>0)
                            {
                                $htmlEqVal .= '<td class="border normal">'.$profitPer.'%</td>';
                            }
                            else
                            {
                                $htmlEqVal .= '<td class="border normal"> - '.abs($profitPer).'%</td>';
                            }
                    
                        }
                        else
                        {
                               $htmlEqVal .= '<td class=" border normal"></td>';
                        }
                        
                        $htmlEqVal .= '<td class="border normal">'.$percent.'%</td>';
                        
                        $htmlEqVal .= '<td class="border normal">'.$row->industry.'</td>
                        <td class="border normal">'.$row->cap.'</td>';
                        
                        $htmlEqVal .= '</tr>';
        }

        $clientName = $row->name;
        $client_code = $row->client_code;
    }

    //last footer
    $htmlEqVal .= '<tr nobr="true">
                        <td class="border" colspan="3"></td>';
                        if(!empty($ccPSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccPSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                         if(!empty($ccSum)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($ccSum).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        
                        if(!empty($ccPProfit)) {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($ccPProfit)).'</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        if(!empty($TotalProfitPer)) {
                          $htmlEqVal .= '<td class="dataTotal  noWrap border">'.round(((100*$ccPProfit)/$ccPSum),2).'%</td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal  noWrap border"></td>';
                        }
                        
                         
                        if(!empty($ccPercent)) {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"><b>'.round($ccPercent).'%</b></td>';
                        } else {
                            $htmlEqVal .= '<td class="dataTotal noWrap border"></td>';
                        }
                     
                $htmlEqVal .= '<td class="dataTotal noWrap border"></td><td class="dataTotal noWrap border"></td></tr>
                <tr nobr="true" border="0"><td colspan="7" border="0" class="default no-border"><br/></td></tr>
            </tbody>';
                        

    //footer for all the totals
    $htmlEqVal .= '<tr nobr="true">
                    <td class="border dataTotal" colspan="3">Family Total</td>';
                    
                    if(!empty($totalPSum)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalPSum)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border">0.00</td>';
                    }
                    $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    if(!empty($totalSum)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalSum)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    if(!empty($totalPProfit)) {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia(round($totalPProfit)).'</b></td>';
                    } else {
                        $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td>';
                    }
                    
                    $htmlEqVal .= '<td class="dataTotal amount noWrap border"></td><td class="dataTotal amount noWrap border"></td><td class="dataTotal amount noWrap border"></td><td class="dataTotal amount noWrap border"></td>
                    </tr>
                    <tr nobr="true" border="0"><td colspan="7" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="7" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="7" border="0" class="default no-border"><br/></td></tr>
                    <tr nobr="true" border="0"><td colspan="7" border="0" class="default no-border"><br/></td></tr>
                </tbody>
            </table>';
                     

    // variables for below code
    $totalInv = 0;
    $totalWd = 0;
    $totalCurrVal = $totalSum;
    
    $totalLedgerBal = 0;
    $totalVal = 0;

    if($eq_rep_data)
    {
        //var_dump($eq_rep_data)
        //$htmlEqVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $clientName = "";
        //$clientName = $eq_values_data[0]->name;
        if(count(get_object_vars($eq_balance[0])) > 0) {
        //if(!empty((array)$eq_balance)) {
            $totalLedgerBal = $eq_balance[0]->balance;
        }


        if(empty($total_value)) { $total_value = 0; }

        //header for funds table
        $htmlEqVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true" class="head-row">
                                <th width="150">Date</th>
                                <th width="170">Investment</th>
                                <th width="170">Withdrawal</th>
                                <th width="160">Current Value</th>
                                <th width="160">Ledger Balance</th>
                                <th width="160">Total Value</th>
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
                </table><br/>';
    }

}
   if($broker_id=='0004' || $broker_id=='0009' || $broker_id=='0174' ||  $broker_id=='0196'){
       
       
        if($eq_chart_history)
        {
            foreach($eq_chart_history as $row)
            {
                
           
                array_push($chart_data,round($row->value));
                $dob_temp =$row->cur_date;
                
                array_push($char_label,$dob_temp);
                
            }
            
            
            
            $chart_data_json = json_encode($chart_data);
            $char_label_json = json_encode($char_label);
        
        }
                         $total_value_ind=0;  foreach($eq_values_industry_data as $row){ 
                             $total_value_ind=$total_value_ind+round($row->value);
                         }
                        
$htmlEqVal .= '<div  class="col-md-12 col-xs-12 col-sm-12" style="padding: 0px 10px 0px 0px;">
                        
                            <table border="0" cellpadding="4" style="border-width:0px;min-height:100px" width="100%">
                                <thead>
                                    <tr nobr="true" class="head-row">
                                            <th width="50%"> Industry
                                            </th>
                                            <th width="30%"> Value
                                            </th>
                                            <th width="20%"> %
                                            </th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    $i=1;  foreach($eq_values_industry_data as $row){ 
                                    $htmlEqVal .= '<tr>
                                        <td width="50%" class="border normal">';
                                            
                                            if($row->industry!="")
                                            {
                                                $htmlEqVal .=  $row->industry;
                                            }
                                            else
                                            {
                                                $htmlEqVal .= "Not Define";
                                                
                                            }
                                        $htmlEqVal .= '</td>
                                        <td width="30%" class="normal amount border">';
                                        $htmlEqVal .=  $this->common_lib->moneyFormatIndia(round($row->value));
                                            $i=$i+1;
                                            
                                        $htmlEqVal .= '</td>
                                        <td  width="20%" class="normal amount border">';
                                            
                                             $htmlEqVal .=  round(((round($row->value)*100)/$total_value_ind),2)." %";
                                            
                                            
                                        $htmlEqVal .= '</td>
                                    </tr>';
                                    }
                                    for(;$i<13;$i++)
                                    { 
                                    $htmlEqVal .= '<tr>
                                        <td width="50%">
                                        </td>
                                        <td width="30%">
                                        </td>
                                        <td width="20%">
                                        </td>
                                    </tr>';
                                   }
                                   
                                $htmlEqVal .= '<tr>
                                        <td width="50%">
                                        </td>
                                        <td width="30%">
                                        </td>
                                        <td width="20%">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>   ';
$html .= $css;
$html .= $htmlEqVal;
}
?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<script src="../../assets/vendors/CanvasJS/canvas.js"></script>

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
                <div class="panel panel-midnightblue" >
                    <div class="panel-gray panel-body collapse in panel-noborder">
                        <div id="css_data"><?php echo $css; ?></div>
                        <div class="rep-logo" style="margin-bottom: 120px;">
                            <?php if(!empty($logo)) { ?>
                                <img src="<?php echo base_url('uploads/brokers/'.$logo);?>" style="float: right; max-height: 80px;" />
                            <?php } ?>
                        </div>
                        
                        <div id="title_data"><?php echo $title; ?></div>
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $htmlEqVal; ?></div>
                        
                       
                        <div class="row">
                        
                        
                        <div class="col-md-6 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6">
                                <div id="mf-chart"  class="chart_css" style="height:425px"></div>
                        </div> 
                         <div class="col-md-6 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6;padding:0px">
                                    
                                <div class="panel panel-body" style="height:426px;max-height:426px;margin:0px">
                                    <canvas id="eq-summary" style="height:426px;max-height:height:426px;"></canvas>
                               </div>
                            </div>  
                        </div>
                        <?php }?>
                        
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                         <input type="hidden" name="pie_chart" id="pie_chart"  />
                        <input type="hidden" name="line_chart" id="line_chart" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Equity Portfolio" />
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
        var data=<?php echo json_encode($eq_values_cap_data) ?>;
        summary_report_pie_charts(data);
        
        summary_report_line_charts(<?php echo $chart_data_json; ?>,
         <?php echo $char_label_json; ?>);
                                    
    });
    
    
    function summary_report_pie_charts(data)
    {
        var total_val=0;
        var _dataPoints=[];
        var color=["#ed3237","#fbb12f","#03afcd","#0098da", "#804744","#804766","#ed3237","#fbb12f","#03afcd","#0098da", "#804744","#804766"];
        for(var i=0;i<data.length;i++)
        {
           total_val=total_val+(+data[i].value);
        }
        for(var i=0;i<data.length;i++)
        {
           _dataPoints.push({y: Math.round(+data[i].value), label:(data[i].cap||'Not Define'),per:((+data[i].value*100)/total_val).toFixed(2),color: color[i]});
        }
        
 var chart = new CanvasJS.Chart("mf-chart", {
	animationEnabled: true,
	borderColor: "red",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
	text: "Cap Wise Summary"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		startAngle: 100,
			indexLabelMaxWidth: 150,
			indexLabelFontSize:12,
		    indexLabelFontWeight: "bold",
        indexLabelWrap: true ,
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints:_dataPoints
	}]
});

chart.render();

$('.canvasjs-chart-credit').css('display','none');
       setTimeout(function(){
        
 var canvas = $(".canvasjs-chart-canvas").get(0);
    var dataURL = canvas.toDataURL('image/png');
            $('#pie_chart').attr('value',  dataURL.replace('data:image/png;base64,',''));   
            $('#line_chart').attr('value',$('#industry_summary').html());
        
        },2000);
        
    }
    
    function summary_report_line_charts(data,label)
    {
        var mfData = {
            labels:label,
            datasets: [{
                    label:'Summary Report',
                    display:false,
                    data:data,
                    fill: false,
                    
                    backgroundColor: [
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                         "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                         "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd"],
            }]
        };
        var min=Math.min.apply(null, data);
        min=(Math.ceil(min/10000)*10000);
        var max=Math.max.apply(null, data);
        max=(Math.ceil(max/10000)*10000);
        var diff=max-min;
        diff=(Math.ceil(diff/10000)*10000)
        
        var options = {
               title: {
            display: true,
            text: 'AUM Growth',
            fontSize:20,
            fontColor:'black'
        },
                elements: {
                        line: {
                                tension: 0 // disables bezier curves
                        }
                },
                scales: {
                    
                    xAxes: [{
                        ticks: {
                            fontSize: 14,
                              padding: 0,
          fontColor: '#000',
          fontStyle: "bold"
                        }
                    }],
                    yAxes: [{
                        ticks: {
                                 fontSize: 14,
                                  padding: 0,
          fontColor: '#000',
          fontStyle: "bold",
                                max: min+(diff+(diff/2)),
                             min: min-diff,
                             stepSize: diff/2,
                            callback: function(value, index, values) {
                                var    x=value.toString();
                                var lastThree = x.substring(x.length-3);
                                var otherNumbers = x.substring(0,x.length-3);
                                if(otherNumbers != '')
                                    lastThree = ',' + lastThree;
                                var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
                                return 'Rs.'+res;
                           }
                        }
                    }]
                },
                  legend: {
    	display: false
    },
        }; 


        myLineChart = new Chart(document.getElementById("eq-summary"),{
            type: 'bar',
            data: mfData,
            options: options
        });
         
        setTimeout(function()
        {
            var url=myLineChart.toBase64Image();
            $('#line_chart').attr('value',  url.replace('data:image/png;base64,',''));    
        },1000);
        
    }

</script>

