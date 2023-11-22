<?php
error_reporting(E_ALL);

if(empty($mf_rep_data))
{
  echo "<script type='text/javascript'>
      alert('Unauthorized Access. Get Outta Here!');
      window.top.close();  //close the current tab
    </script>";
} else {
    $css = '<style type="text/css">
      table { width:100%; color:#000000; }
      table td {font-size: 11px; padding:2px; text-align:center; }
      table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
      .border {border: 1px solid #4d4d4d;}
      .noWrap { white-space: nowrap; }
      .amount { text-align:right; padding-right:5px; text-indent: 5px;}
      .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
      .info { font-size: 12px; text-align: center; }
      .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
      .normal {font-weight: normal;}
      .dataTotal {font-weight: bold; color:#4f8edc;text-align:right;}
      .no-border {border-width: 0px; border-color:#fff;}
      .client-name { text-align: left; font-size: 14px; font-weight: bold; }
      .client-name2 { text-align: left; font-size: 16px; font-weight: bold; }
  </style>';
    if($mf_rep_data)
    {

        $family_portfolio='<div class="title">RTA AUM Report</div><br/>';
        $summary_portfolio='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;width:99.9%">';
        $total=0;$profit=0;$amt_totals=0;$units_totals=0;$cur_value_total=0;
        $div_red_total=0;$div_payout_total=0;$profit_total=0;
        //$cagr_total=0;$abs_total=0;
$total_pur_amt=0;
//$total_p_nav=0;
$total_divAmt=0;
$gtotal=0;
/*$cagr1=0;

$abs1=0;
$cagr2=0;
$abs2=0;
$gTotalCagr1=0;
$gTotalCagr2=0;
$gTotalCagr=0;
$gTotal_abs=0;
$gTotalAbs1=0;
*/
                           //<th width="40">Scheme Type</th>
                           //<th width="50">Purchase Date</th>
                           //<th width="40">Div Amount</th>
                           //<th width="48">Purchase NAV</th>
                           //<th width="35">Trans Day</th>
                           //<th width="75">Total</th>
        $summary_portfolio.='<tr><td colspan="20"  >'.$family_portfolio.'</td></tr>';
        $summary_portfolio.='<tr nobr="true" class="head-row">
                            <th width="135">Family Name</th>
                            <th width="135">Client Name</th>
                            <th width="135">Scheme Name</th>
                            
                            <th width="50">Folio Number</th>
                            
                            <th width="60">Purchase Amount</th>
                            
                            
                            <th width="50">No. of Unit</th>
                            
                            <th width="47">Current NAV</th>
                            <th width="65">Current NAV Date</th>
                            <th width="70">Current Value</th>
                            <th width="60">Div R</th>
                            <th width="60">Div P</th>
                            <th width="40">RTA NAME</th
                            
                            </tr>';
                /*<th width="40">CAGR</th>
                            <th width="40">ABS</th>
                            >*/
        $num_rows=sizeof ($mf_rep_data);
        $i=0;
        $mf_rep_data_summary=[];
        foreach ($mf_rep_data as $rows)
        {
            
            
            
            $i=$i+1;
            
             if($rows->mf_scheme_type != 'DIV')
            {
                $purAmt = $rows->p_amount;
                $divAmt = 0;
            }
            else {
                $divAmt = $rows->p_amount;
                $purAmt = 0;
            }
            //set total value
            if($rows->div_payout)
                $total = round($rows->div_payout +$rows->current_value, 2);
            else
                $total = round($divAmt->current_value, 2);          
            
            //set cagr1(temp)
        /*    if($rows->cagr != null){
                $cagr1 = ($purAmt + $divAmt) * $rows->cagr * $rows->transaction_day;}
            else{
                $cagr1 = 0;}
                
            if($rows->mf_abs != null)
            {
                $cagr2 = ($purAmt + $divAmt) * $rows->transaction_day;
                $abs1 = ($purAmt + $divAmt) *$rows->mf_abs;
            } else {
                $abs1 = 0;
            }*/
                                        
            $units_totals=$units_totals+$rows->live_unit;
            $total_pur_amt = $purAmt + $total_pur_amt;
            $total_divAmt=$divAmt+$total_divAmt;
            $cur_value_total=$cur_value_total+$rows->current_value;
           // $total_p_nav =$rows->p_nav+$total_p_nav;
            $div_red_total= $div_red_total+$rows->div_r2  ;
            $div_payout_total=$div_payout_total + $rows->div_payout;
            $gtotal=$total+$gtotal;
            
         /*   $gTotalCagr1 = $cagr1 + $gTotalCagr1;
            $gTotalCagr2 = $cagr2 + $gTotalCagr2;
            $gTotalAbs1 = $abs1 + $gTotalAbs1;
            
              if($gTotalCagr2 != 0)
                $gTotalCagr = round(($gTotalCagr1 / $gTotalCagr2), 2);
            else
                $gTotalCagr = round($gTotalCagr1, 2);
                            
               if($total_pur_amt != 0)
            {
                $gTotal_abs = round(($gTotalAbs1 / ($total_pur_amt + $total_divAmt)), 2);
            }              
           */                 
            
          /*  if($rows->purchase_date) {
                $date = DateTime::createFromFormat('d/m/Y',$rows->purchase_date);
                $date = $date->format('d-M-Y');
            } else {
                $date = "";
            }*/
            if($rows->c_nav_date) {
                $date2 = DateTime::createFromFormat('d/m/Y',$rows->c_nav_date);
                $date2 = $date2->format('d-M-Y');
            } else {
                $date2 = "";
            }
            
            //<td class="border" style="text-align:left" >'.$rows->mf_scheme_type.'</td>
            //<td class="border" style="text-align:left" >'.$date.'</td>';
              //if(!empty($divAmt))
                 //  $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($divAmt).'</td>';
                //else
                  //  $summary_portfolio .= '<td class="border normal"></td>';
                 //<td class="border normal amount">'.$rows->transaction_day.'</td>
            $summary_portfolio.='<tr>
                        <td class="border" style="text-align:left" >'.$rows->family_name.'</td>
                        <td class="border" style="text-align:left" >'.$rows->client_name.'</td>
                        
                        <td class="border" style="text-align:left" >'.$rows->mf_scheme_name.'</td>
                        <td class="border" style="text-align:left" >'.$rows->folio_number.'</td>';
                        
            if(!empty($purAmt)){
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($purAmt)).'</td>';}
                else{
                    $summary_portfolio .= '<td class="border normal"></td>';
                }
            //$summary_portfolio.='<td class="border normal amount">'.$rows->p_nav.'</td>
                $summary_portfolio.='<td class="border normal amount">'.$rows->live_unit.'</td>
                
                <td class="border normal amount">'.$rows->c_nav.'</td>
                <td class="border normal">'.$date2.'</td>';
              $curval = floatval($rows->current_value);
                if(!empty($curval))
                    $summary_portfolio.='<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rows->current_value, 2)).'</td>';
                else
                    $summary_portfolio.='<td class="border normal"></td>';
                
            $divr2 = intval($rows->div_r2);
                if(!empty($divr2))
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2)).'</td>';
                else
                    $summary_portfolio .= '<td class="border normal"></td>';
                $divp = intval($rows->div_payout);
                if(!empty($divp))
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_payout)).'</td>';
                else
                    $summary_portfolio .= '<td class="border normal"></td>';
                //$summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($total)).'</td>';
                //$summary_portfolio .='<td class="border normal amount">'.$rows->cagr.'</td>
                //<td class="border normal amount">'.$rows->mf_abs.'</td>
                $summary_portfolio .='<td class="border normal amount">'.$rows->from_file.'</td>
                                </tr>';
            $summary_flag=0;             
            if($rows->from_file !='')
            {
                
                foreach($mf_rep_data_summary as $sum_row)
                {
                    
                    
                    if($sum_row->from_file==$rows->from_file)
                    {
                    
        
                        $sum_row->p_amount+=$purAmt;
                        $sum_row->current_value+=round($rows->current_value, 2);
                        $sum_row->div_r2+=round($rows->div_r2);
                        $sum_row->div_payout+=round($rows->div_payout);
                        $summary_flag=1;
                    
                        break;
                    }
                }
                if($summary_flag==0 && $rows->from_file!='')
                {
                    $mf_rep_data_summary[]=(object) array("p_amount" =>$purAmt,
                                                "current_value"=>round($rows->current_value, 2),
                                                "div_r2"=>round($rows->div_r2),
                                                "div_payout"=>round($rows->div_payout),
                                               "from_file"=>$rows->from_file );
                }
            }
                
            if($i==$num_rows)
            {
                //<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_divAmt)).'</td>
                  //              <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_p_nav)).'</td>
                $summary_portfolio.='<tr>
                     <td class=" border dataTotal"></td>
                      <td class=" border dataTotal"></td>
                      <td class=" border dataTotal"></td>
                <td class=" border dataTotal"   style="text-align:right">Total :-</td>
                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_pur_amt)).'</td>
                                
                                <td class=" border dataTotal amount">'.$units_totals.'</td>
                                <td class=" border " colspan="2"  ></td>                
                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($cur_value_total)).'</td>';
               
                    $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_red_total)).'</td>';
               
                
               
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_payout_total)).'</td>';
               
                //$summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($gtotal)).'</td>
                                //$summary_portfolio.='<td class=" border dataTotal amount">'.$gTotalCagr.'</td>
                                //<td class=" border dataTotal amount">'.$gTotal_abs.'</td>
                                $summary_portfolio.='<td class="border"></td>
                                </tr>
                            </table><br>';
            }
            
         
            //.sprintf("%.2f",$fTotalCagr).
            //sprintf("%.2f",$fTotal_abs)
        }
        
        $i=0;
        $num_rows=sizeof ($mf_rep_data_summary);
        $summary_portfolio.='<br><div style="width:600px" class="title">RTA Wise Summary</div><br/>';
        $summary_portfolio.='<br><table border="0" cellpadding="4" style="text-align:center; border-width:0px;width:600px">';
        $summary_portfolio.='<tr nobr="true" class="head-row">
                            <th width="40" style="text-align=left">RTA NAME</th>
                            <th width="60">Purchase Amount</th>
                            <th width="70">Current Value</th>
                            <th width="60">Div R</th>
                            <th width="60">Div P</th>
                            </tr>';
                          $total_pur_amt_summ=0;  
                          $cur_value_total_summ=0;
                          $div_red_total_summ=0;
                          $div_payout_total_summ=0;
        foreach ($mf_rep_data_summary as $rows)
        {
            
            
            
            $i=$i+1;
            
            
                                        
            
            $total_pur_amt_summ = $total_pur_amt_summ + $rows->p_amount;
            $cur_value_total_summ=$cur_value_total_summ+$rows->current_value;
            $div_red_total_summ= $div_red_total_summ+$rows->div_r2  ;
            $div_payout_total_summ=$div_payout_total_summ + $rows->div_payout;
            
            $summary_portfolio.='<tr><td class="border normal amount">'.$rows->from_file.'</td>';
                        
            if(!empty($rows->p_amount)){
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($rows->p_amount).'</td>';}
                else{
                    $summary_portfolio .= '<td class="border normal"></td>';
                }
           
                
              $curval = floatval($rows->current_value);
                if(!empty($curval))
                    $summary_portfolio.='<td class="border normal amount">'.$this->common_lib->moneyFormatIndia($rows->current_value).'</td>';
                else
                    $summary_portfolio.='<td class="border normal"></td>';
                
            $divr2 = intval($rows->div_r2);
                if(!empty($divr2))
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_r2)).'</td>';
                else
                    $summary_portfolio .= '<td class="border normal"></td>';
                $divp = intval($rows->div_payout);
                if(!empty($divp))
                    $summary_portfolio .= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rows->div_payout)).'</td></tr>';
                else
                    $summary_portfolio .= '<td class="border normal"></td></tr>';
            
                
            if($i==$num_rows)
            {
                //<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_divAmt)).'</td>
                  //              <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_p_nav)).'</td>
                $summary_portfolio.='<tr><td class=" border dataTotal"   style="text-align:right">Total :-</td>
                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($total_pur_amt_summ)).'</td>
                                <td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($cur_value_total_summ)).'</td>';
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_red_total_summ)).'</td>';
                $summary_portfolio.='<td class=" border dataTotal amount">'.$this->common_lib->moneyFormatIndia(round($div_payout_total_summ)).'</td>';
                $summary_portfolio.='</tr></table><br>';
            }
            
        }
        
        $amc_wise_summary = array();
        $mf_scheme_name="";
        $scheme_group="";
        foreach ($mf_rep_data as $rs)
        {
            $scheme_parts = explode(' ',$rs->mf_scheme_name);
            $scheme = strtoupper($rs->mf_scheme_name);
            //echo '<pre>';print_r($rs);
            $scheme_group = strtoupper($scheme_parts[0]);
           
            if($scheme_group == 'ADITYA' || $scheme_group == 'BSR' || $scheme_group == 'BSL' || $scheme_group == 'ABSL')
            {
                $scheme_group = 'BIRLA';
            }
       
            if($scheme == 'L&T' || $scheme == 'L & T' || strpos($scheme, 'L & T') !== false || strpos($scheme, 'L&T') !== false)
            {
                $scheme_group = 'L&T';
            }
       
            if($scheme == 'FIXED TENURE' || $scheme == 'FT FIXED' || $scheme_group == 'TEMPLETON')
            {
                $scheme_group = 'FRANKLIN';
            }
       
            //var_dump(array_key_exists($scheme_group,$amc_wise_summary));
            if(array_key_exists($scheme_group,$amc_wise_summary))
            {
               if($rs->p_amount != '')
               {
                   $amc_wise_summary[$scheme_group]['purchase_amount'] = $amc_wise_summary[$scheme_group]['purchase_amount'] + $rs->p_amount;
               }
               if($rs->current_value != '')
               {
                   $amc_wise_summary[$scheme_group]['current_value'] = $amc_wise_summary[$scheme_group]['current_value'] + $rs->current_value;
               }
               if($rs->div_r2 != '')
               {
                   $amc_wise_summary[$scheme_group]['div_r2'] = $amc_wise_summary[$scheme_group]['div_r2'] + $rs->div_r2;
               }
               if($rs->div_payout != '')
               {
                   $amc_wise_summary[$scheme_group]['div_payout'] = $amc_wise_summary[$scheme_group]['div_payout'] + $rs->div_payout;
               }
               
            }
            else
            {
                $div_r2=0;
                $div_payout=0;
                $purchase_amount =  $rs->p_amount;
                $current_value = $rs->current_value;
                $div_r2 = $rs->div_r2;
                $div_payout = $rs->div_payout;
               
               $amc_wise_summary[$scheme_group] = array('purchase_amount'=>$purchase_amount,
                                                        'current_value'=>$current_value,
                                                        'div_r2'=>$div_r2,
                                                        'div_payout'=>$div_payout);
           
            }
        }
        $total_purchase_amount=0;
        $total_current_value = 0;
        $total_div_r2=0;
        $total_div_payout=0;
   
        //echo '<pre>';print_r($amc_wise_summary);die;
        $heading='<br/><div class="title col-md-4 border" style="margin-bottom:10px;" >AMC Wise Summary</div><br>';
        $common_scheme_summ='<br><table border="0" cellpadding="4" style="text-align:center; width:99.99%; border-width:100%;" width="370px">';
        $common_scheme_summ.='<tr><td colspan="17">'.$heading.'</td></tr>';
        $common_scheme_summ.='<tr  nobr="true" class="head-row">
                                <th width="50">AMC Name</th>
                                <th width="100">Purchase Amount</th>
                                <th width="100">Current Value</th>
                                <th width="100">Div R</th>
                                <th width="80">Div P</th>
                            </tr>';

        $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
        foreach ($amc_wise_summary as $key => $rs)
        {
            $common_scheme_summ.='<tr>
                                    <td class=" border"  style="text-align:left">'.$key.'</td>';
            if(empty($rs['purchase_amount']) || $rs['purchase_amount']==0)
            {
                $common_scheme_summ.='<td class="border amount"></td>';
            } else {
                $total_purchase_amount = $total_purchase_amount + round($rs['purchase_amount']);
                $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['purchase_amount'])).'</td>';
            }
                        
            if(empty($rs['current_value']) || $rs['current_value']==0)
            {
                $common_scheme_summ.='<td class="border amount"></td>';
            } else {
                $total_current_value = $total_current_value + round($rs['current_value']);
                $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['current_value'])).'</td>';
            }
            
            if($rs['div_r2']==0 || empty($rs['div_r2']))
            {
                $common_scheme_summ.='<td class="border amount"></td>';
            }
            else {
                $total_div_r2 = $total_div_r2 + $rs['div_r2'];
                $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['div_r2'])).'</td>';
            }
            if($rs['div_payout']==0 || empty($rs['div_payout']))
            {
                $common_scheme_summ.='<td class="border amount"></td></tr>';
            }
            else {
                $total_div_payout = $total_div_payout + $rs['div_payout'];
                $common_scheme_summ.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs['div_payout'])).'</td></tr>';
            }
        }
        $common_scheme_summ.='<tr>
                                <td class=" border dataTotal" >Total</td>
                                <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_purchase_amount)).'</td>
                                <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_current_value)).'</td>
                                <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_div_r2)).'</td>
                                <td class=" dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($total_div_payout)).'</td></tr></table>';
             

       
        $html=$summary_portfolio;
        $html.=$common_scheme_summ;
    }

    $sTotalPurAmt = 0; $sTotalDivAmt = 0; $sTotalPurNav = 0; $sTotalPurNavDivide = 0; $sTotalLiveUnit = 0; $sTotalCurValue = 0; $sTotalDivR = 0; $sTotalDivPay = 0;
    $sTotal = 0;
    //$sTotalCagr = 0; $sTotal_abs = 0;
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
                          <!-- <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf');?>');">PDF File (*.pdf)</a></li> -->
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
                      <!-- <div id="title_data"><?php echo $title; ?></div> -->
                      <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                      <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                      <input type="hidden" name="name" id="name" value="Family wise aum report" />
                      <input type="hidden" name="titleData" id="titleData" />
                      <input type="hidden" name="htmlData" id="htmlData" />
                      <!-- <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Mutual Fund Portfolio" /> -->
                  </div>
              </div>
          </form>
      </div>
  </div>
</div>
