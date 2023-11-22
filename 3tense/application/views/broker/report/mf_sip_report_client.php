  <?php
//echo "<pre>";
//  print_r($sip_data);
if(empty($sip_data))
{
    echo "<script type='text/javascript'>
        alert('Unauthorized Access.Get Out From Here');
        window.top.close();  //close the current tab
      </script>";
}
else
{
    $html = ''; //set html to blank
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right; text-indent: 5px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; text-align: center; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
        .client-name2 { text-align: left; font-size: 16px; font-weight: bold; }
    </style>';

$clientName=$sip_data['0']->client_name;
    $title = '<div class="title row">Client Wise SIP Report of '.$sip_data['0']->client_name.'</div><br/>';
    if($sip_data)
    {
  

        $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
                $html .= '<tr nobr="true" class="head-row">
                            <th width="65">Investor</th>
                            <th width="65">Scheme</th>
                            <th width="60">Folio No</th>
                            <th width="45">Goal</th>
                            <th width="56">Start Date</th>
                            <th width="56">End Date</th>
                            <th width="38">SIP Status</th>
                            <th width="56">Cease Date</th>
                            <th width="45">Frequency</th>
                            
                            <th width="48">SIP Installment Amount</th>
                            <th width="50">Total Invested Value</th>
                            <th width="45">Bal.Units</th>
                            <th width="43">Cur. NAV</th>
                            <th width="49">Current Value</th>
                            <th width="40">Div R.</th>
                            <th width="40">Div P.</th>
                            <th width="54">Total(Rs.)</th>
                            <th width="35">Ann.&nbsp Return (%)</th>
                            <th width="35">Abs &nbsp   Return &nbsp(%)</th>
                            <th width="55">Latest Installment Date</th>


                        </tr>';
                        $sTotalCagr1='0';
                        $sTotalCagr1='0';

                        $cagr1='0';
                        $cagr2='0';
                        $abs1='0';
                        $sTotalCagr1='0';
                        $sTotalCagr1 = $cagr1 + $sTotalCagr1;

                        $sTotalCagr1='0';
                        $gTotalCagr1='0';
                        $fTotalCagr1='0';
                        $sTotalCagr2='0';
                        $gTotalCagr2='0';
                        $fTotalCagr2='0';
                        $sTotalAbs1='0';
                        $gTotalAbs1='0';
                        $fTotalAbs1='0';
                        $sTotalCagr='0';
                        $sTotal_abs='0';
                        $sTotalPurAmt='0';
                        $sTotalDivAmt='0';
                        $total_installment_amt=0;
                        $total_sip=0;
                        $total_invested_value='';
                        $balance_unit='';
                        $current_value='';
                        $tot='';
                        $total_divr='';
                        $total_divp='';
                        //$prev_installment_amt=  $sip_data['0']->installment_amt;
          foreach($sip_data as $fm)
          {

              $total_installment_amt=$total_installment_amt+$fm->installment_amt;
              $total_invested_value=$total_invested_value+$fm->total_invested_value;
              $balance_unit=$balance_unit+$fm->balance_unit;
              $current_value=$current_value+$fm->current_value;
              $tot=$tot+$fm->total;
              $total_divr=$total_divr+$fm->Divr;
              $total_divp=$total_divp+$fm->DivP;
             
 //             $total_of_installment_amt=$prev_installment_amt+$total_of_installment_amt;
            if($fm->start_date)
            {
                       $start_date = DateTime::createFromFormat('d/m/Y',$fm->start_date);
                       $start_date = $start_date->format('d-M-Y');
            }
            else
            {
                       $start_date = "";
            }
            if($fm->end_date)
            {
                       $end_date = DateTime::createFromFormat('d/m/Y',$fm->end_date);
                       $end_date = $end_date->format('d-M-Y');
            }
            else
            {
                       $start_date = "";
            }
            if($fm->last_installment_date)
            {
               
               
               $last_installment_date = DateTime::createFromFormat('Y-m-d',$fm->last_installment_date);
               $last_installment_date = $last_installment_date->format('d-M-Y');
            }           
            else
            {
                $last_installment_date="";
            }
          
            if($cease_date=$fm->cease_date or $fm->cease_date =='0000-00-00' or $fm->cease_date =='' or $fm->cease_date=='NULL')
            {
                if($cease_date =='0000-00-00' or $cease_date =='' or $cease_date=='NULL')
                {
                    
                    $cease_date='';
                    $sip_status='Active';
                }
                else
                {
                 $cease_date = DateTime::createFromFormat('Y-m-d',$fm->cease_date);
                 $cease_date = $cease_date->format('d-M-Y');
                 $sip_status='Ceased';
                }
                
            }           
            else
            {
                
               $sip_status='Active';
                
            }
           
            






           $divAmt=$fm->div_amount;
           $purAmt=$fm->purchase_amount;
           $sTotalPurAmt=$sTotalPurAmt+$purAmt;
           $sTotalDivAmt=$sTotalDivAmt+$divAmt;
            if($fm->cagr1 != null)
              $cagr1 =$fm->cagr1;  // ($purAmt + $divAmt) * $mf_rep_data[$i]->cagr * $mf_rep_data[$i]->transaction_day;
            else
            $cagr1 = 0;

            if($fm->mf_abs != null)
            {
                 $cagr2 = $fm->cagr2;//($purAmt + $divAmt) * $mf_rep_data[$i]->transaction_day;
                $abs1 = ($purAmt + $divAmt) * $fm->mf_abs;
            } else {
                $abs1 = 0;
            }

                      $sTotalCagr1 = $cagr1 + $sTotalCagr1;
                      $gTotalCagr1 = $cagr1 + $gTotalCagr1;
                      $fTotalCagr1 = $cagr1 + $fTotalCagr1;
                      $sTotalCagr2 = $cagr2 + $sTotalCagr2;
                      $gTotalCagr2 = $cagr2 + $gTotalCagr2;
                      $fTotalCagr2 = $cagr2 + $fTotalCagr2;
                      $sTotalAbs1 = $abs1 + $sTotalAbs1;
                      $gTotalAbs1 = $abs1 + $gTotalAbs1;
                      $fTotalAbs1 = $abs1 + $fTotalAbs1;
                      if($sTotalCagr2 != 0)
                          $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
                      else
                          $sTotalCagr = round($sTotalCagr1, 2);

                      if($sTotalPurAmt != 0)
                      {
                          $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt + $sTotalDivAmt)), 2);
                      }
                      elseif($sTotalDivAmt != 0)
                      {
                          /*$sTotal_abs = $sTotalAbs1 + $sTotalDivAmt;
                          $gTotal_abs = $gTotalAbs1 + $gTotalDivAmt;
                          $fTotal_abs = $fTotalAbs1 + $fTotalDivAmt;*/
                          $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt+$sTotalDivAmt)), 2);
                      } else {
                          $sTotal_abs = 0;
                      }
                 


          $html.='<tr><td class="border">'.$fm->client_name.'</td>
          <td class="border">'.$fm->Scheme_Name.'</td>
          <td class="border">'.$fm->folio_no.'</td>
          <td class="border">'.$fm->goal.'</td>
          <td class="border">'.$start_date.'</td>
          <td class="border">'.$end_date.'</td>
          <td class="border">'.$sip_status.'</td>
          <td class="border">'.$cease_date.'</td>
          <td class="border">'.ucfirst($fm->Frequency).'</td>
          
          <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->installment_amt)).'</td>
          <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->total_invested_value)).'</td>
          <td class="border amount">'.$fm->balance_unit.'</td>
          <td class="border">'.sprintf("%.4f",$fm->current_nav).'</td>
          <td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->current_value)).'</td>';
          $html.= !empty($fm->Divr) && !round($fm->Divr)==0 ?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->Divr)).'</td>':'<td class="border"></td>';
          $html.= !empty($fm->DivP) && !round($fm->DivP)==0 ?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->DivP)).'</td>':'<td class="border"></td>';
          $html .= '<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($fm->total)).'</td>
          <td class="border">'.round($fm->cagr,2).'</td>
          <td class="border">'.round($fm->mf_abs,2).'</td>
          <td class="border">'.$last_installment_date.'</td>


          </tr>';


          }
          $html.='<tr><td colspan="9" class="dataTotal border"></td><td class="dataTotal border amount" >'.$this->common_lib->moneyFormatIndia($total_installment_amt).'</td>
          <td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_invested_value)).'</td>
          <td class="border dataTotal amount">'.$balance_unit.'</td>
          <td class="border amount"></td>
          <td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($current_value)).'</td>';
           $html.= !empty($fm->Divr) && !round($fm->Divr)==0 ?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_divr)).'</td>':'<td class="border"></td>';
           $html.= !empty($fm->DivP) && !round($fm->DivP)==0 ?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_divp)).'</td>':'<td class="border"></td>';
          $html .= '<td class="border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($tot)).'</td>
           <td class="border dataTotal">'.$sTotalCagr.'</td>
           <td class="border dataTotal">'.$sTotal_abs.'</td>
          <td class="border"></td></tr></table><br>';

//include "mf_report_common_client.php";
    }
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
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Mutual_funds/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf_sip');?>');">PDF File (*.pdf)</a></li>
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
                        <div class="row" id="report_data" style="overflow-x: auto;"><?php echo $html; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?>  SIP Report" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
