<?php
// error_reporting(0);
// echo"<pre>";
// print_r($capital_gain);
// echo"</pre>";

 if(empty($capital_gain))
 {
     echo "<script type='text/javascript'>
         alert('Unauthorized Access. Get Outta Here!');
         window.top.close();  //close the current tab
       </script>";
 }
 else
 {

     $css = '<style type="text/css">
         table { width:100%;color:#000000;}
         table td {font-size: 10px; padding:1px;}
         .amount{text-align:right;}
         table th {font-size: 10px; padding:1px; text-align:center; border: 1px solid #4d4d4d;}
         .border {border: 1px solid #4d4d4d;}
         .noWrap { white-space: nowrap; }
         .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d;}
         .info { font-size: 12px; text-align: center; }
         .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
         .normal {font-weight: normal;}
         .dataTotal {font-weight: bold; color:#4f8edc;}
         .align_right{text-align:right;}
         .no-border {border-width: 0px; border-color:#fff;}
         .client-name { text-align: left; font-size: 14px; font-weight: bold; }
     </style>';
    //  $familyName=$capital_gain[0]->name;
     $clientName = $capital_gain[0]->client_name;
     $schemeName = $capital_gain[0]->scheme_name;
     $title = '<div class="title row">Capital Gain Mutual Fund Portfolio of '.$clientName.'  </div><br/>';
     
     
     $purAmt=0;$divAmt;$unit;$sale_amt='';
        $sPur_amt='';$sDiv_amt='';$sUnit='';$sSale_amt='';$sDiv_r2='';$sPayout='';$sST_profit='';$sST_loss='';$sLT_profit='';$sLT_loss='';
        $cagr1=0;$cagr2=0;$abs1=0;$sTotalCagr1=0;$sTotalAbs1=0;$sTotalCagr2=0;$sTotalCagr=0;$sTotal_abs=0;
        $gPur_amt='';$gDiv_amt='';$gUnit='';$gSale_amt='';$gDiv_r2='';$gPayout='';$gST_profit='';$gST_loss='';$gLT_profit='';$gLT_loss='';
        $gTotalCagr1=0;$gTotalAbs1=0;$gTotalCagr2=0;$gTotalCagr=0;$gTotal_abs=0;

        $newScheme = false; $schemeFoot = false; $familyFoot = false;
        $i = 0;
     
     
     // add client info to page
     $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
             <tbody>';

     /*$html .= '<tr nobr="true">
                     <td class="no-border client-name" colspan="10"></td>
                 </tr>
                 <tr nobr="true" class="head-row">
                     <th width="60px">Folio Number</th>
                     <th width="150px">Scheme Name</th>
                     <th width="40px">Scheme Type</th>
                     <th width="52px">Purchase Date</th>
                     <th width="30px">Transaction Type</th>
                     <th width="40px">NAV</th>
                     <th width="55px">Purchase Amount</th>
                     <th width="50px" >Div Amount</th>
                     <th width="52px">Sale Date</th>
                     <th width="30px">Sale NAV</th>
                     <th width="50px">No Of UNit</th>
                     <th width="50px">Sale Amount</th>
                     <th width="30px">Transaction Days</th>
                     <th width="40px">ST Gain</th>
                     <th width="40px">ST Loss</th>
                     <th width="40px">LT Gain</th>
                     <th width="40px">LT Loss</th>
                     <th width="40px">Div R</th>
                     <th width="40px">Div Payout</th>
                     <th width="40px">CAGR</th>
                     <th width="40px">ABS</th>
                 </tr>
             </tbody>';*/

          /* name	client_name	folio_number	scheme_name	scheme_type	purchase_date		mutual_fund_type	p_nav
             purchase_amount	div_amount	sale_date sale_nav units	sale_amount STT	transaction_day ST_gain Lt_loss ST_gain Lt_loss
             div_r2	payout	mf_cagr	mf_abs */

/*$purAmt=0;$divAmt;$unit;$sale_amt='';
$sPur_amt='';$sDiv_amt='';$sUnit='';$sSale_amt='';$sDiv_r2='';$sPayout='';$sST_profit='';$sST_loss='';$sLT_profit='';$sLT_loss='';
$cagr1=0;$cagr2=0;$abs1=0;$sTotalCagr1=0;$sTotalAbs1=0;$sTotalCagr2=0;$sTotalCagr=0;$sTotal_abs=0;*/
   foreach($capital_gain as $row)
   {
        if($i == 0)
        {
            $newScheme = true;
        }
        if($schemeName != $row->scheme_name)
        {
            $newScheme = true;
            $schemeFoot = true;
        }
        
        if($schemeFoot)
        {
         $schemeFoot=false;
         
         $html.='<tr><td class="border" colspan="6"></td>';
                 $html.=!empty($sPur_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPur_amt)).'</td>':'<td class="border"></td>';
                 $html.=!empty($sDiv_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_amt)).'</td>':'<td class="border"></td>';
                 $html.='<td class="dataTotal border amount" colspan="2"></td>';
                 $html.=!empty($sUnit)?'<td class="dataTotal border amount">'.$sUnit.'</td>':'<td class="border"></td>';
                 $html.=!empty($sSale_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sSale_amt)).'</td>':'<td class="border"></td>';
                 $html.='
                 <td class="dataTotal border amount"></td>
                 <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_profit)).'</td>
                 <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_loss)).'</td>
                 <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_profit)).'</td>
                 <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_loss)).'</td>';
                 $html.=!empty($sDiv_r2)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_r2)).'</td>':'<td class="border"></td>';
                 $html.=!empty($sPayout)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPayout)).'</td>':'<td class="border"></td>';
                 $html.='<td class="dataTotal border amount">'.round($sTotalCagr,2).'</td>
                 <td class="dataTotal border amount">'.round($sTotal_abs,2).'</td>
                 </tr>
                 <tr>
                     <td colspan="22" class="no-border info dataTotal">
                        Overall Portfolio Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.'
                     </td>
                 </tr>';
                //  <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';
                //  <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';   
                
                
                
               $purAmt=0;$divAmt;$unit;$sale_amt='';
                $sPur_amt='';$sDiv_amt='';$sUnit='';$sSale_amt='';$sDiv_r2='';$sPayout='';$sST_profit='';$sST_loss='';$sLT_profit='';$sLT_loss='';
                $cagr1=0;$cagr2=0;$abs1=0;$sTotalCagr1=0;$sTotalAbs1=0;$sTotalCagr2=0;$sTotalCagr=0;$sTotal_abs=0;
        }
        
        if($newScheme)
        {
           
           $html .= '<tr> <td style="padding:5px;margin:5px;"></td></tr><tr nobr="true">
                      <td class="no-border client-name" colspan="12">'.$row->scheme_name.'</td>
                  </tr>';
            $newScheme = false;
             $html .= '<tr nobr="true">
                         <td class="no-border client-name" colspan="10"></td>
                     </tr>
                     <tr nobr="true" class="head-row">
                         <th width="60px">Folio Number</th>
                         <th width="95px">Scheme Name</th>
                         <th width="40px">Scheme Type</th>
                         <th width="62px">Purchase Date</th>
                         <th width="30px">Transaction Type</th>
                         <th width="50px">NAV</th>
                         <th width="50px">Purchase Amount</th>
                         <th width="50px" >Div Amount</th>
                         <th width="62px">Sale Date</th>
                         <th width="40px">Sale NAV</th>
                         <th width="50px">No Of UNit</th>
                         <th width="50px">Sale Amount</th>
                         
                         <th width="30px">Transaction Days</th>
                         <th width="40px">ST Gain</th>
                         <th width="40px">ST Loss</th>
                         <th width="40px">LT Gain</th>
                         <th width="40px">LT Loss</th>
                         <th width="40px">Div R</th>
                         <th width="40px">Div Payout</th>
                         <th width="40px">CAGR</th>
                         <th width="40px">ABS</th>
                     </tr>';
            
        }
        
        $purAmt=$row->purchase_amount;
        $divAmt=$row->div_amount;
        $unit=$row->units;
        $sPur_amt=$sPur_amt+$row->purchase_amount;
        $sDiv_amt=$sDiv_amt+$row->div_amount;
        $sUnit=$sUnit+$row->units;
        $sSale_amt=$sSale_amt+$row->sale_amount;
        $sDiv_r2=$sDiv_r2+$row->div_r2;
        $sPayout=$sPayout+$row->payout;
        
        $gPur_amt=$gPur_amt+$row->purchase_amount;
        $gDiv_amt=$gDiv_amt+$row->div_amount;
        $gUnit=$gUnit+$row->units;
        $gSale_amt=$gSale_amt+$row->sale_amount;
        $gDiv_r2=$gDiv_r2+$row->div_r2;
        $gPayout=$gPayout+$row->payout;

        if($row->mf_cagr != null)
            $cagr1 = (($purAmt+$divAmt) * $row->mf_cagr) * $row->transaction_day;
        else
            $cagr1 = 0;
        if($row->mf_abs != null)
        {
            $cagr2 =($purAmt+$divAmt)  * $row->transaction_day;
            $abs1 = ($purAmt+$divAmt)  * $row->mf_abs;
        } else {
            $abs1 = 0;
            $cagr2 = 0;
        }
        $sTotalCagr1 = $cagr1 + $sTotalCagr1;
        $sTotalCagr2 = $cagr2 + $sTotalCagr2;
        $sTotalAbs1 = $abs1 + $sTotalAbs1;
        
        $gTotalCagr1 = $cagr1 + $gTotalCagr1;
        $gTotalCagr2 = $cagr2 + $gTotalCagr2;
        $gTotalAbs1 = $abs1 + $gTotalAbs1;
        
        
        if($sTotalCagr2 != 0) {
            $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
        } else {
            $sTotalCagr = round($sTotalCagr1, 2);
        }

        if($sPur_amt!= 0)
        {
            $sTotal_abs = round(($sTotalAbs1 / ($sPur_amt+$sDiv_amt)), 2);
        }
       else {
            $sTotal_abs = 0;
        }
        
        if($gTotalCagr2 != 0) {
            $gTotalCagr = round(($gTotalCagr1 / $gTotalCagr2), 2);
        } else {
            $gTotalCagr = round($gTotalCagr1, 2);
        }

        if($gPur_amt!= 0)
        {
            $gTotal_abs = round(($gTotalAbs1 / ($gPur_amt+$gDiv_amt)), 2);
        }
       else {
            $gTotal_abs = 0;
        }
        

        if($row->purchase_date) {
            $date = DateTime::createFromFormat('d/m/Y',$row->purchase_date);
            $date = $date->format('d-M-Y');
        } else {
            $date = "";
        }
        if($row->sale_date) {
            $date2 = DateTime::createFromFormat('d/m/Y',$row->sale_date);
            $date2 = $date2->format('d-M-Y');
        } else {
            $date2 = "";
        }
        
        $html.='
        
              <td class="border">'.$row->folio_number.'</td>
              <td class="border">'.$row->scheme_name.'</td>
              <td class="border">'.$row->scheme_type.'</td>
              <td class="border">'.$date.'</td>
              <td class="border">'.$row->mutual_fund_type.'</td>
              <td class="border amount">'.$row->p_nav.'</td>';
              $html.=($purAmt!=0 && !empty($purAmt) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($purAmt)).'</td>':'<td class="border"></td>';
              $html.=($divAmt!=0 && !empty($divAmt) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($divAmt)).'</td>':'<td class="border"></td>';
              $html.='<td class="border">'.$date2.'</td>';
              $html.=($row->sale_nav!=0 && !empty($row->sale_nav) )?'<td class="border amount">'.round($row->sale_nav,4).'</td>':'<td class="border"></td>';
              // $html.='<td class="border"></td>';
              $html.=($unit!=0 && !empty($unit) )?'<td class="border amount">'.$unit.'</td>':'<td class="border"></td>';
              $html.=!empty($row->sale_amount)?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->sale_amount)).'</td>':'<td class="border"></td>';
              $html.=($row->transaction_day!=0 && !empty($row->transaction_day) )?'<td class="border amount">'.$row->transaction_day.'</td>':'<td class="border"></td>';
              if($row->transaction_day<365)
              {
                if($row->gain>0)
                {
                    $sST_profit=$sST_profit+$row->gain;
                    $gST_profit=$gST_profit+$row->gain;
                }
                else {
                    $sST_loss=$sST_loss+$row->gain;
                    $gST_loss=$gST_loss+$row->gain;
                }
                $html.=$row->gain>0?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td><td class="border" ></td>':'<td class="border"></td><td class="border amount" >'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td>';
                $html.='<td class="border amount"></td>
                        <td class="border amount"></td>';
              }
              else{
                if($row->gain>0)
                {
                    $sLT_profit=$sLT_profit+$row->gain;
                    $gLT_profit=$gLT_profit+$row->gain;
                  }
                  else {
                    $sLT_loss=$sLT_loss+$row->gain;
                    $gLT_loss=$gLT_loss+$row->gain;
                  }
                $html.='<td class="border amount"></td>
                        <td class="border amount"></td>';
                $html.=$row->gain>0?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td><td class="border" ></td>':'<td class="border"></td><td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td>';
              }
              $html.=($row->div_r2!=0 && !empty($row->div_r2) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->div_r2)).'</td>':'<td class="border"></td>';
              $html.=($row->payout!=0 && !empty($row->payout) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->payout)).'</td>':'<td class="border"></td>';
              $html.='<td class="border amount">'.round($row->mf_cagr,2).'</td>
              <td class="border amount">'.round($row->mf_abs,2).'</td>
              </tr>';
              
              $i++;
              $schemeName = $row->scheme_name;
   }
   
   $html.='<tr><td class="border" colspan="6"></td>';
             $html.=!empty($sPur_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPur_amt)).'</td>':'<td class="border"></td>';
             $html.=!empty($sDiv_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_amt)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount" colspan="2"></td>';
             $html.=!empty($sUnit)?'<td class="dataTotal border amount">'.$sUnit.'</td>':'<td class="border"></td>';
             $html.=!empty($sSale_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sSale_amt)).'</td>':'<td class="border"></td>';
             $html.='
             <td class="dataTotal border amount"></td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_loss)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_loss)).'</td>';
             $html.=!empty($sDiv_r2)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_r2)).'</td>':'<td class="border"></td>';
             $html.=!empty($sPayout)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPayout)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount">'.round($sTotalCagr,2).'</td>
             <td class="dataTotal border amount">'.round($sTotal_abs,2).'</td>
             </tr>
             <tr>
                 <td colspan="22" class="no-border info dataTotal">
                    Overall Portfolio Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.'
                 </td>
             </tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>';
             
    //last client footer
    $html.='<tr nobr="true"><td class="border dataTotal" colspan="6">Client Total</td>';
             $html.=!empty($gPur_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gPur_amt)).'</td>':'<td class="border"></td>';
             $html.=!empty($gDiv_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gDiv_amt)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount" colspan="2"></td>';
             $html.=!empty($gUnit)?'<td class="dataTotal border amount">'.$gUnit.'</td>':'<td class="border"></td>';
             $html.=!empty($gSale_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gSale_amt)).'</td>':'<td class="border"></td>';
             $html.='
             <td class="dataTotal border amount"></td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gST_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gST_loss)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gLT_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gLT_loss)).'</td>';
             $html.=!empty($gDiv_r2)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gDiv_r2)).'</td>':'<td class="border"></td>';
             $html.=!empty($gPayout)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($gPayout)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount">'.round($gTotalCagr,2).'</td>
             <td class="dataTotal border amount">'.round($gTotal_abs,2).'</td>
             </tr>
             <tr>
                 <td colspan="22" class="no-border info dataTotal">
                    Overall Portfolio Weighted Avg. CAGR: '.$gTotalCagr.' | Weighted Avg. Abs Return: '.$gTotal_abs.'
                 </td>
             </tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
             </tbody>
    </table>';
       
        /*$purAmt=$row->purchase_amount;
        $divAmt=$row->div_amount;
        $unit=$row->units;
        $sPur_amt=$sPur_amt+$row->purchase_amount;
        $sDiv_amt=$sDiv_amt+$row->div_amount;
        $sUnit=$sUnit+$row->units;
        $sSale_amt=$sSale_amt+$row->sale_amount;
        $sDiv_r2=$sDiv_r2+$row->div_r2;
        $sPayout=$sPayout+$row->payout;

        if($row->mf_cagr != null)
            $cagr1 =(($purAmt+$divAmt) * $row->mf_cagr) * $row->transaction_day;
        else
            $cagr1 = 0;
        if($row->mf_abs != null)
        {
            $cagr2 =($purAmt+$divAmt)  * $row->transaction_day;
            $abs1 = ($purAmt+$divAmt)  * $row->mf_abs;
        } else {
            $abs1 = 0;
            $cagr2 = 0;
        }
        $sTotalCagr1 = $cagr1 + $sTotalCagr1;
        $sTotalCagr2 = $cagr2 + $sTotalCagr2;
        $sTotalAbs1 = $abs1 + $sTotalAbs1;
  if($sTotalCagr2 != 0) {
            $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
        } else {
            $sTotalCagr = round($sTotalCagr1, 2);
        }


        if($sPur_amt!= 0)
        {
            $sTotal_abs = round(($sTotalAbs1 / ($sPur_amt+$sDiv_amt)), 2);
        }
       else {
            $sTotal_abs = 0;
        }

        if($row->purchase_date) {
            $date = DateTime::createFromFormat('d/m/Y',$row->purchase_date);
            $date = $date->format('d-M-Y');
        } else {
            $date = "";
        }
        if($row->sale_date) {
            $date2 = DateTime::createFromFormat('d/m/Y',$row->sale_date);
            $date2 = $date2->format('d-M-Y');
        } else {
            $date2 = "";
        }*/
        /* name	client_name	folio_number	scheme_name	scheme_type	purchase_date		transaction_type	p_nav
           purchase_amount	div_amount	sale_date sale_nav units	sale_amount STT	transaction_day ST_gain Lt_loss
           ST_gain Lt_loss div_r2	payout	mf_cagr	mf_abs */
          //  <td class="border">'.$row->client_name.'</td>
        /*$html.='<tr>
              <td class="border">'.$row->folio_number.'</td>
              <td class="border">'.$row->scheme_name.'</td>
              <td class="border">'.$row->scheme_type.'</td>
              <td class="border">'.$date.'</td>
              <td class="border">'.$row->mutual_fund_type.'</td>
              <td class="border amount">'.$row->p_nav.'</td>';
              $html.=($purAmt!=0 && !empty($purAmt) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($purAmt)).'</td>':'<td class="border"></td>';
              $html.=($divAmt!=0 && !empty($divAmt) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($divAmt)).'</td>':'<td class="border"></td>';
              $html.='<td class="border">'.$date2.'</td>';
              $html.=($row->sale_nav!=0 && !empty($row->sale_nav) )?'<td class="border amount">'.round($row->sale_nav,4).'</td>':'<td class="border"></td>';
              // $html.='<td class="border"></td>';
              $html.=($unit!=0 && !empty($unit) )?'<td class="border amount">'.$unit.'</td>':'<td class="border"></td>';
              $html.=!empty($row->sale_amount)?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->sale_amount)).'</td>':'<td class="border"></td>';
              
              $html.=($row->transaction_day!=0 && !empty($row->transaction_day) )?'<td class="border amount">'.$row->transaction_day.'</td>':'<td class="border"></td>';
              if($row->transaction_day<365)
              {
                if($row->gain>0)
                {
                $sST_profit=$sST_profit+$row->gain;
                }
                else {
                  $sST_loss=$sST_loss+$row->gain;
                }
                $html.=$row->gain>0?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td><td class="border" ></td>':'<td class="border"></td><td class="border amount" >'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td>';
                $html.='<td class="border amount"></td>
                        <td class="border amount"></td>';
              }
              else{
                if($row->gain>0)
                {
                    $sLT_profit=$sLT_profit+$row->gain;
                  }
                  else {
                    $sLT_loss=$sLT_loss+$row->gain;
                  }
                $html.='<td class="border amount"></td>
                        <td class="border amount"></td>';
                $html.=$row->gain>0?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td><td class="border" ></td>':'<td class="border"></td><td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->gain)).'</td>';
              }
              $html.=($row->div_r2!=0 && !empty($row->div_r2) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->div_r2)).'</td>':'<td class="border"></td>';
              $html.=($row->payout!=0 && !empty($row->payout) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($row->payout)).'</td>':'<td class="border"></td>';
              $html.='<td class="border amount">'.round($row->mf_cagr,2).'</td>
              <td class="border amount">'.round($row->mf_abs,2).'</td>
              </tr>';
     }
     $html.='<tr><td class="border" colspan="6"></td>';
             $html.=!empty($sPur_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPur_amt)).'</td>':'<td class="border"></td>';
             $html.=!empty($sDiv_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_amt)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount" colspan="2"></td>';
             $html.=!empty($sUnit)?'<td class="dataTotal border amount">'.$sUnit.'</td>':'<td class="border"></td>';
             $html.=!empty($sSale_amt)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sSale_amt)).'</td>':'<td class="border"></td>';
             $html.='
             <td class="dataTotal border amount"></td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sST_loss)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_profit)).'</td>
             <td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sLT_loss)).'</td>';
             $html.=!empty($sDiv_r2)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sDiv_r2)).'</td>':'<td class="border"></td>';
             $html.=!empty($sPayout)?'<td class="dataTotal border amount">'.$this->common_lib->moneyFormatIndia(round($sPayout)).'</td>':'<td class="border"></td>';
             $html.='<td class="dataTotal border amount">'.round($sTotalCagr,2).'</td>
             <td class="dataTotal border amount">'.round($sTotal_abs,2).'</td>
             </tr>
             <tr>
                 <td colspan="22" class="no-border info dataTotal">
                    Overall Portfolio Weighted Avg. CAGR: '.$sTotalCagr.' | Weighted Avg. Abs Return: '.$sTotal_abs.'
                 </td>
             </tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
             <tr nobr="true" border="0"><td colspan="3" border="0" class="default no-border"><br/></td></tr>
      </table>';*/


      // $html='';
             include "mf_report_common_client.php";

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
                             <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Mutual_funds/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
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
                         <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                         <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                         <input type="hidden" name="name" id="name" value="<?php echo $clientName;?>" />
                         <input type="hidden" name="titleData" id="titleData" />
                         <input type="hidden" name="htmlData" id="htmlData" />
                         <input type="hidden" name="report_name" id="reportName" value="<?php echo $clientName;?> Capital Gain  Portfolio" />
                     </div>
                 </div>
             </form>
         </div>
     </div>
 </div>
