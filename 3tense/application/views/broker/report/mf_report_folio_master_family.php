<?php
//
error_reporting(E_ALL);
// echo"<pre>";
// print_r($folio_master_data);
// echo"</pre>";


 if(empty($folio_master_data))
 {
     echo "<script type='text/javascript'>
         alert('Unauthorized Access. Get Outta Here!');
         window.top.close();  //close the current tab
       </script>";
 }
 else
 {

     $css = '<style type="text/css">
         table { width:99.99%; color:#000000;}
         table td {font-size: 11px; padding:1px;}
         .amount{text-align:right;}
         table th {font-size: 11px; padding:1px; text-align:center; border: 1px solid #4d4d4d;}
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
         .client-name2 { text-align: left; font-size: 16px; font-weight: bold; }
           .hidecolumn { display:none !important;}
     </style>';
     $clientName=$folio_master_data[0]->name;
      $familyName=$folio_master_data[0]->family_name;

     $title = '<div class="title row">Folio Master Mutual Fund Portfolio of '.$familyName.' Family</div><br/>';
     // add client info to page
     $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
             <tbody>';
$newClient=true;
$html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">';
foreach ($folio_master_data as $rs)
 {


   if($clientName!=$rs->name)
   {
               $newClient=true;
    }
    if($newClient)
    {
      $html .= '<tr> <td style="padding:10px;margin:10px;"></td></tr><tr nobr="true">
                  <td class="no-border client-name2" colspan="12">'.$rs->name.'</td>
              </tr>';
              $newClient=false;

                   $html .= '<tr nobr="true" class="head-row" >
                                  <th width="140px">Scheme Name</th>
                                  <th width="50px">Folio Number</th>
                                  <th width="160px"> Address</th>
                                  <th width="70px">City</th>
                                  <th width="40px">Pin code</th>
                                  <th width="80px">State</th>
                                  <th width="60px">Mobile Number</th>
                                  <th width="100px">Email</th>
                                  <th width="100px">Bank Name</th>
                                  <th width="80px">Account Number</th></th>
                                  <th width="100px">Mode Of Holding</th>
                                  
                                 <th class="no-border" style="display:none; color:#fff; background-color:#fff; border:none;" >Bank Branch</th>
                                  <th class="no-border" style="display:none; color:#fff; background-color:#fff; border:none;">jointName1</th>
                                  <th class="no-border" style="display:none; color:#fff; background-color:#fff; border:none;">jointName2</th>
                                  <th class="no-border" style="display:none; color:#fff; background-color:#fff; border:none;">Nominee</th>
                                  </tr>';



    }
     $clientName=$rs->name;

     $html.='<tr>
              <td class="border">'.$rs->scheme_name.'</td>
              <td class="border">'.$rs->folio_number.'</td>
              <td class="border">'.$rs->add_flat.' '.$rs->add_street.' '.$rs->add_area.'</td>
              <td class="border">' .$rs->add_city.'</td>
              <td class="border">' .$rs->add_pin.'</td>
              <td class="border">' .$rs->add_state.'</td>
              <td class="border">'.$rs->mobile.'</td>
              <td class="border">'.$rs->email_id.'</td>
              <td class="border">'.$rs->bank_name.'</td>
              <td class="border">'.$rs->bank_acc_no.'</td>
              <td class="border">'.$rs->mode_holding.'</td>
              
              <td class="hidecolumn" style="display:none; color:#fff; background-color:#fff; border:none;">'.$rs->bank_branch.'</td>
              <td class="hidecolumn" style="display:none; color:#fff; background-color:#fff; border:none;">'.$rs->jointName1.'</td>
              <td class="hidecolumn" style="display:none; color:#fff; background-color:#fff; border:none;">'.$rs->jointName2.'</td>
              <td class="hidecolumn" style="display:none; color:#fff; background-color:#fff; border:none;">'.$rs->nominee_name1.'</td>

              </tr>';

               
}
$html.'</tbody></table><br>';
          // $html.=($purAmt!=0 && !empty($purAmt) )?'<td class="border amount">'.$this->common_lib->moneyFormatIndia(round($purAmt)).'</td>':'<td class="border"></td>';
include 'mf_report_common_family.php';
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
                         <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                         <input type="hidden" name="titleData" id="titleData" />
                         <input type="hidden" name="htmlData" id="htmlData" />
                         <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Folio Master" />
                     </div>
                 </div>
             </form>
         </div>
     </div>
 </div>
