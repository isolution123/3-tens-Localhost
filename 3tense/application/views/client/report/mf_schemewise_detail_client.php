<?php
if(empty($mf_rep_data))
{
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
}
else
{
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 11px; padding:2px}
        .amount {text-align:right;}
        table th {font-size: 11px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; text-align: center; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
$clientName=$mf_rep_data['0']->client_name;
$title = '<div class="title row">Scheme Wise Detail Mutual Fund Portfolio Of '.$clientName.'</div><br/>';
    // add client info to page
$html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;"><tbody>';
// $heading='<div class="title row">Scheme Summary of '.$clientName.' </div><br>';
$data='<br><table border="0" cellpadding="4" style="text-align:center; border-width:100%;">';

            $data.='<tr nobr="true" class="head-row">
                        <th width="70">Scheme Name</th>
                        <th width="60">Folio Number</th>
                        <th width="65">Inception Date</th>
                        <th width="45">Scheme Type</th>
                        <th width="70">Purchase Amount</th>
                        <th width="70">Div Amount</th>
                        <th width="48">Purchase NAV</th>
                        <th width="60">No. of Unit</th>
                        <th width="35">Trans Day</th>
                        <th width="47">Current NAV</th>
                        <th width="65">Current NAV Date</th>
                        <th width="70">Current Value</th>
                        <th width="60">Div R</th>
                        <th width="60">Div Payout</th>
                        <th width="75">Total</th>
                        <th width="40">CAGR</th>
                        <th width="40">ABS</th>
                    </tr>';
      if($mf_rep_data)
      {
        $f_pur_amt=0;$f_div_amount=0;$f_live_unit=0;$f_current_value=0;$f_div_r2=0;$f_div_payout=0;$f_total=0;
        $purAmt=0;$divAmt=0;$cagr1=0;$cagr2=0;$abs1=0;
        $sTotalPurAmt=0;$sTotalDivAmt=0;$sTotalCagr1=0;$sTotalCagr2=0;$sTotalCagr=0;$sTotal_abs=0;$sTotalAbs1=0;
        foreach ($mf_rep_data as $rs)
        {
          $purAmt=$rs->purchase_amount;
          $divAmt=$rs->div_amount;
          //set cagr1(temp)
          if($rs->cagr1 != null)
              $cagr1 = $rs->cagr1;//($purAmt + $divAmt) * $rs->cagr * $rs->transaction_day;
          else
              $cagr1 = 0;
          if($rs->mf_abs != null)
          {
              $cagr2 =  $rs->cagr2;//($purAmt + $divAmt) * $rs->transaction_day;
              $abs1 = ($purAmt + $divAmt) * $rs->mf_abs;
          } else {
              $abs1 = 0;
              $cagr2 = 0;
          }
          //set sub totals
          $sTotalPurAmt = $purAmt + $sTotalPurAmt;
          $sTotalDivAmt = $divAmt + $sTotalDivAmt;
          $sTotalCagr1 = $cagr1 + $sTotalCagr1;
          $sTotalCagr2 = $cagr2 + $sTotalCagr2;
          $sTotalAbs1 = $abs1 + $sTotalAbs1;
          if($sTotalCagr2 != 0) {
              $sTotalCagr = round(($sTotalCagr1 / $sTotalCagr2), 2);
          } else {
              $sTotalCagr = round($sTotalCagr1, 2);
          }

          if($sTotalPurAmt!= 0)
          {
              $sTotal_abs = round(($sTotalAbs1 / ($sTotalPurAmt + $sTotalDivAmt)), 2);
          }
          elseif($sTotalDivAmt!=0)
          {
              $sTotal_abs = round(($sTotalAbs1 / $sTotalDivAmt), 2);
          } else {
              $sTotal_abs = 0;
          }

          if($rs->purchase_date) {
              $date = DateTime::createFromFormat('d/m/Y',$rs->purchase_date);
              $date = $date->format('d-M-Y');
          } else {
              $date = "";
          }
          if($rs->c_nav_date) {
              $date2 = DateTime::createFromFormat('d/m/Y',$rs->c_nav_date);
              $date2 = $date2->format('d-M-Y');
          } else {
              $date2 = "";
          }

          $data.='<tr>
                      <td class="border ">'.$rs->mf_scheme_name.'</td>
                      <td class="border ">'.$rs->folio_number.'</td>
                      <td class="border">'.$date.'</td>
                      <td class="border">'.$rs->scheme_type.'</td>';
                      if(empty($rs->purchase_amount) || $rs->purchase_amount==0)
                        $data.= '<td class="border normal"></td>';
                      else
                        $data.= '<td class="border normal amount">'.$this->common_lib->moneyFormatIndia(round($rs->purchase_amount)).'</td>';
                      if($rs->div_amount==0 ||empty($rs->div_amount))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_amount)).'</td>';
                      }
                    $data.='<td class="border amount">'.sprintf("%.4f",$rs->p_nav).'</td>
                            <td class="border amount">'.sprintf("%.4f",$rs->live_unit).'</td>
                            <td class="border amount">'.$rs->transaction_day.'</td>
                            <td class="border amount">'.sprintf("%.4f",$rs->c_nav).'</td>
                            <td class="border">'.$date2.'</td>
                            <td class="border amount">'.$this->common_lib->moneyFormatIndia(sprintf("%.2f",$rs->current_value)).'</td>';
                      if($rs->div_r2==0 ||empty($rs->div_r2))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_r2)).'</td>';
                      }

                      if($rs->div_payout==0 ||empty($rs->div_payout))
                      {
                        $data.='<td class="border amount"></td>';
                      }else {
                        $data.='<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($rs->div_payout)).'</td>';
                      }
                      $total=0;
                      if($rs->div_payout)
                          $total = $rs->div_payout + $rs->current_value;
                      else
                          $total =$rs->current_value;
         $data.='<td class=" border amount ">'.$this->common_lib->moneyFormatIndia(round($total )).'</td>
                      <td class=" border amount ">'.sprintf("%.2f",$rs->cagr).'</td>
                      <td class=" border amount ">'.sprintf("%.2f",$rs->mf_abs).'</td>
                      </tr>';

                  $f_pur_amt=$f_pur_amt+$rs->purchase_amount;
                  $f_div_amount=$f_div_amount+$rs->div_amount;
                  $f_live_unit=$f_live_unit+$rs->live_unit;
                  $f_current_value=$f_current_value+$rs->current_value;
                  $f_div_r2=$f_div_r2+$rs->div_r2;
                  $f_div_payout=$f_div_payout+$rs->div_payout;
                  $f_total=$f_total+$total;
                  $f_TotalPurNav =(($f_pur_amt + $f_div_amount) / $f_live_unit);
            }
            $data.='<tr>
                        <td class="border dataTotal" colspan="4">Total</td>
                        <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_pur_amt)).'</td>';
                        if($f_div_amount==0 ||empty($f_div_amount))
                        {
                          $data.='<td class="border amount"></td>';
                        }else {
                          $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_amount)).'</td>';
                        }
                        $data.='<td class="border amount dataTotal"></td>
                                <td class="border amount dataTotal">'.sprintf("%.4f",$f_live_unit).'</td>
                                <td class="border dataTotal"></td>
                                <td class="border dataTotal"></td>
                                <td class="border dataTotal"></td>
                                <td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_current_value)).'</td>';
                          if($f_div_r2==0 ||empty($f_div_r2))
                          {
                            $data.='<td class="border"></td>';
                          }else {
                            $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_r2)).'</td>';
                          }

                          if($f_div_payout==0 ||empty($f_div_payout))
                          {
                            $data.='<td class="border amount"></td>';
                          }else {
                            $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_div_payout)).'</td>';
                          }
                        $data.='<td class="border amount dataTotal">'.$this->common_lib->moneyFormatIndia(round($f_total )).'</td>
                        <td class="border amount dataTotal">'.round($sTotalCagr,2).'</td>
                        <td class="border amount dataTotal">'.round($sTotal_abs,2).'</td>
                        </tr></table><br>';
        }

        $html.=$data;
        $fTotal=$f_total;
        $TotalCagr=$sTotalCagr;
        $Total_abs=$sTotal_abs;
         include 'mf_report_common_client.php';
}
?>

<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <?php if($this->session->userdata('head')=='yes'){ 
                      
                       if($this->session->userdata('user_id')==0004 || $this->session->userdata('user_id')=='0009' || $this->session->userdata('user_id')=='0174' || $this->session->userdata('user_id')=='0196')
                    { ?>
                  <div class="btn-group">
                      
                      <div id="select-family">
                        <select id="select-family" name="right_client_selection" style="width:96%;padding:5px;margin:5px;"  onchange="client_selection_change(this);" class="select2" >
                            <option value="0" selected disabled>Select Family Member</option>
                                <?php foreach($this->session->userdata('clients_list') as $row):
                                if($this->session->userdata('client_id')== $row->client_id && $this->session->userdata('type')=='')
                                {
                                ?>
                                
                                <option value='<?php echo $row->client_id; ?>' selected><?php echo $row->name; ?></option>
                                <?php
                                }
                                else
                                {
                                ?>
                                    <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                <?php    
                                
                                
                                }
                                endforeach; ?>
                        </select>
                      </div>   
                    
                      </div>
                      <?php } } ?>
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
                        <input type="hidden" name="name" id="name" value="<?php echo $mf_rep_data[0]->client_name;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $mf_rep_data[0]->client_name;?> Mutual Fund Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
function client_selection_change(sel)
{
        document.cookie = "client_select_id_schemewise_report = " + sel.value;
        window.location.reload();
}
</script>