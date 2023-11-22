<?php
if(empty($fd_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $html1='';
    //to change date to current format
    $familyName = ""; $clientName = "";
    //to calculate Fixed Deposit amt invested and maturity amt of particular client
    $totCliInvAmt = 0; $totCliMatAmt = 0;
    //to calculate amt invested and maturity amt of all clients
    $totInvAmt = 0; $totMatAmt = 0;
    $adj = array();
    $chart_data = array();
    $color=array('#099ad9','#fbb232','#16afce','#ef4446');
    $adjCounter = 0;
    $header = true; $content = false; $footer = false;
    //check if ins data is empty then get family name from gen ins data
    if(!empty($fd_rep_data))
        $familyName = $fd_rep_data[0]->family_name;
    $css = '<style type="text/css">
        table { width:100%; color:#000000; }
        table td {font-size: 12px; padding:2px; text-align:center; }
        table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
        .border {border: 1px solid #4d4d4d;}
        .amount { text-align:right;padding-right:5px; text-indent: 10px; }
        .noWrap { white-space: nowrap; }
        .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
        .info { font-size: 12px; font-weight: lighter; border:none; }
        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
        .normal {font-weight: normal;}
        .dataTotal {font-weight: bold; color:#4f8edc;}
        .no-border {border-width: 0px; border-color:#fff;}
        .client-name { text-align: left; font-size: 14px; font-weight: bold; }
    </style>';
    $title = '<div class="title row">Fixed Income Portfolio of '.$familyName.' Family</div><br/>';
    $html = '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>';
    foreach($fd_rep_data as $row)
    {
        
        
         $matchFoundflag=0;
               foreach($chart_data as $key => $val){
                if($val->label==$row->issuing_authority)
                {
                    $matchFoundflag=1;
                    $val->y=$val->y+$row->amount_invested;
                }
             }
             if($matchFoundflag==0)
             {
                 
                  $chart_data[] = (object) ['label' => $row->issuing_authority,
                                        'y'=>round($row->amount_invested),
                                        'per'=>0,
                                        'color'=> $color[$colorcounter]
                                        ];
                    $colorcounter=$colorcounter+1;
                    if($colorcounter==4)
                    {
                        $colorcounter=0;
                    }
             }
        
        
        
        $percent = 0;
        if($clientName == $row->client_name) { $footer = false; $same_client = true; } else { $footer = true; $same_client = false; }
        if($footer && $clientName != "") {
            $html .= '<tr>
                        <td class="border normal" colspan="6"></td>';
                        $tcinv = intval($totCliInvAmt);
                        if(!empty($tcinv)) {
                            $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliInvAmt).'</b></td>';
                        } else {
                            $html .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        $html .= '<td class="border normal" colspan="2"></td>';
                        $tcmat = intval($totCliMatAmt);
                        if(!empty($tcmat)) {
                            $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliMatAmt).'</b></td>';
                        } else {
                            $html .= '<td class="dataTotal amount noWrap border"></td>';
                        }
                        $html .= '<td class="border normal" colspan="2"></td>
                    </tr>';

            if(!$same_client) {
                $html .= '<tr nobr="true" border="0"><td colspan="17" border="0" class="default no-border"><br/></td></tr>';
            }

            $totCliInvAmt = 0; $totCliMatAmt = 0;
            $footer = false;
            $header = true;
        }
        if($header) {
            if(!$same_client) {
                $html .= '<tr nobr="true">
                            <td class="no-border client-name" colspan="12">'.$row->client_name.'</td>
                        </tr>';
                $same_client = true;
            }
            $html .= '<tr nobr="true" class="head-row">
                        <th width="70">Asset Class</th>
                        <th width="80">Investment Date</th>
                        <th width="80">Company</th>
                        <th width="80">Ref. No.</th>
                        <th width="60">Interest Rate</th>
                        <th width="60">Term (Months)</th>
                        <th width="100">Amount Invested</th>
                        <th width="80">Interest Payment</th>
                        <th width="80">Maturity Date</th>
                        <th width="100">Maturity Amount</th>
                        <th width="90">Nominee Name</th>
                        <th width="100">Advisor Name</th>
                    </tr>
            </tbody>
            <tbody>';
            $header = false;
            $content = true;
        }

        if($content) {
            if($row->adjustment_flag == 'Yes')
            {
                $adj[$adjCounter]['ref_number'] = $row->ref_number;
                $adj[$adjCounter]['adjustment_ref_number'] = $row->adjustment_ref_number;
                $adj[$adjCounter]['adj'] = $row->adjustment;
                $adjCounter++;
            }
            $totCliMatAmt = round($totCliMatAmt + $row->maturity_amount);
            $totCliInvAmt = round($totCliInvAmt + $row->amount_invested);
            $totInvAmt = round($totInvAmt + $row->amount_invested);
            $totMatAmt = round($totMatAmt + $row->maturity_amount);
            $tempDate = DateTime::createFromFormat('d/m/Y',$row->issued_date);
            $date = $tempDate->format('d-M-Y');
            $tempDate2 = DateTime::createFromFormat('d/m/Y',$row->maturity_date);
            $date2 = $tempDate2->format('d-M-Y');
            $html .= '<tr nobr="true">
                <td class="border normal">'.$row->type_of_investment.'</td>
                <td class="noWrap border normal">'.$date.'</td>
                <td class="border normal">'.$row->issuing_authority.'</td>
                <td class="border normal">'.$row->ref_number.'</td>
                <td class="noWrap border normal">'.$row->interest_rate.'</td>
                <td class="noWrap border normal">'.$row->Term.'</td>';
            $inv = intval($row->amount_invested);
            if(!empty($inv)) {
                $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->amount_invested, 0)).'</td>';
            } else {
                $html .= '<td class="amount noWrap border normal"></td>';
            }
            $html .= '<td class="border normal">'.$row->fd_method.'</td>
                <td class="noWrap border normal">'.$date2.'</td>';
            $mat = intval($row->maturity_amount);
            if(!empty($mat)) {
                $html .= '<td class="amount noWrap border normal">'.$this->common_lib->moneyFormatIndia(round($row->maturity_amount, 0)).'</td>';
            } else {
                $html .= '<td class="amount noWrap border normal"></td>';
            }
            $html .= '<td class="border normal">'.$row->nominee.'</td>
                <td class="border normal">'.$row->broker_details.'</td>
                </tr>';
        }
        $clientName = $row->client_name;
    }
    $html .= '<tr nobr="true">
                <td class="border normal" colspan="6"></td>';
                $tcinv = intval($totCliInvAmt);
                if(!empty($tcinv)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliInvAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border normal" colspan="2"></td>';
                $tcmat = intval($totCliMatAmt);
                if(!empty($tcmat)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totCliMatAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border normal" colspan="2"></td>
            </tr>
            <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>

            <tr nobr="true">
                <td class="border dataTotal" colspan="6">Family Total</td>';
                $tinv = intval($totInvAmt);
                if(!empty($tinv)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totInvAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border normal" colspan="2"></td>';
                $tmat = intval($totMatAmt);
                if(!empty($tmat)) {
                    $html .= '<td class="dataTotal amount noWrap border"><b>'.$this->common_lib->moneyFormatIndia($totMatAmt).'</b></td>';
                } else {
                    $html .= '<td class="dataTotal amount noWrap border"></td>';
                }
                $html .= '<td class="border normal" colspan="2"></td>
            </tr>
            <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
            <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
            <tr nobr="true"><td colspan="12" class="default no-border"><br/></td></tr>
        </tbody>
    </table>';

    if(!empty($adj))
    {
        $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true">
                            <td class="no-border client-name" colspan="12">ADJUSTMENT DETAILS</td>
                        </tr>
                        <tr nobr="true" class="head-row">
                            <th width="250">Reference Number</th>
                            <th width="250">Target Reference Number</th>
                            <th width="480">Adjustment</th>
                        </tr>
                    </tbody>
                <tbody>';
        foreach($adj as $row)
        {
            $html .= '<tr nobr="true">
                            <td class="border normal">'.$row['ref_number'].'</td>
                            <td class="border normal">'.$row['adjustment_ref_number'].'</td>
                            <td class="border normal">'.$row['adj'].'</td>
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
    }
    
}
if($top5_maturity)
    {
        
         $html1 .='
                    <table class="col-md-6 col-xs-12 col-sm-12" id="report_data1" border="1"  style="width:50%">
                        <tbody>
                         <tr nobr="true" >
                            <td class="no-border client-name" colspan="5">Five Upcoming Maturities</td>
                        </tr>
                            <tr nobr="true" class="head-row">
                            <th>Client Name</th>
                            <th>Maturity Date</th>
                            <th>Company Name</th>
                            <th>Ref No.</th>
                            <th>Amount</th>
                          </tr>
                        </thead>';

          foreach ($top5_maturity as $rs)
          {
            $html1 .= '<tr><td class="border normal">'.$rs["client_name"].'</td><td class="border normal">'.$rs["maturity_date"].'</td><td class="border normal"> '.$rs["fd_comp_name"].'</td><td class="border normal">'.$rs["ref_number"].'</td><td class="border normal">'.$rs["maturity_amount"].'</td></tr>';
          }
        $html1 .= '</table>';
    }
    foreach($chart_data as $key => $val){
        $val->per=round((($val->y*100)/$totInvAmt),2);
    }
?>
<div id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<script src="../../assets/vendors/CanvasJS/canvas.js"></script>
    <div id='wrap'>
        <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Fixed_deposits/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li><a href="#" onclick="export_to_pdf('<?php echo base_url('broker/Fixed_deposits/export_to_pdf_with_chart_and_table');?>');">PDF File (*.pdf)</a></li>
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
                         <?php  if($brokerID=='0004' || $brokerID=='0009' || $brokerID=='0174' ||  $brokerID=='0196'){?>
                        <br><br>
                        <div class="row">
                        
                        
                             <?php echo $html1; ?>
                           
                        <div class="col-md-6 col-xs-12 col-sm-12"  style="border: 1px solid #d2d3d6" >
                                <div id="mf-chart"  class="chart_css" style="height:325px"></div>
                        </div>  
                        </div>
                        <?php }?>
                        <div class="row" id="report_data"  style="overflow-x: auto;"><?php echo $html; ?></div>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                        <input type="hidden" name="summary_data" id="summary_data" value='<?php echo $html1; ?>'/>
                        <input type="hidden" name="chart_2" id="chart_2"  />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Fixed Income Portfolio" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/javascript">
   var myLineChart='';
  $(function() {
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
        
         summary_report_pie_charts(<?php  echo json_encode($chart_data); ?>);
         
                                    
    });
    
    
    function summary_report_pie_charts(data)
    {
      
  
        
 var chart = new CanvasJS.Chart("mf-chart", {
	animationEnabled: true,
	borderColor: "red",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
	text: "Productwise Asset Allocation"
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
		startAngle: 140,
			indexLabelMaxWidth: 150,
			indexLabelFontSize: 15,
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
		dataPoints: data
	}]
});

chart.render();

$('.canvasjs-chart-credit').css('display','none');
       setTimeout(function(){
        
 var canvas = $(".canvasjs-chart-canvas").get(0);
    var dataURL = canvas.toDataURL('image/png');
            $('#chart_2').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        
        },2000);
        
    }
    

</script>