<?php
if(isset($client_list))
{
$colorCodeArray =array(
          "#9B59B6","#E74C3C","#26B99A","#3498DB","#CFD4D8","#B370CF","#E95E4F","#36CAAB","#49A9EA","#FFFFFF",
          "#C0C0C0","#808080","#000000","#FF0000","#800000","#FFFF00","#808000","#00FF00","#008000","#00FFFF",
          "#008080","#0000FF","#000080","#FF00FF","#800080","#F0F8FF","#FAEBD7","#00FFFF","#7FFFD4","#F5F5DC",
          "#000000","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E","#FF7F50","#6495ED",
          "#FFF8DC","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#006400","#BDB76B","#556B2F",
          "#FF8C00","#8B0000","#E9967A","#8FBC8F","#483D8B","#2F4F4F","#00CED1","#FF1493","#696969","#1E90FF",
          "#FF69B4","#D3D3D3","#000080","#808000","#FF4500","#DA70D6","#DB7093","#DDA0DD","#CD853F","#FF0000",
          "#BC8F8F");
          $i=0;
          $total_cl_portfolio=0;
          $percentClient=array();
          $len=sizeof($client_list);
      foreach ($client_list as $rs)
      {
        $total_cl_portfolio=$total_cl_portfolio+intval($rs['TotalPortfolio']);
      }

      foreach ($client_list as $rs)
      {
          array_push($percentClient,((intval($rs['TotalPortfolio'])/$total_cl_portfolio)*100));

      }
       $labels = array();
       $datasets=array();
      foreach ($client_list as $rs)
      {
          array_push($labels,$rs['client_name']);
          array_push($datasets,intval($rs['TotalPortfolio']));
      }
} ?>
<?php
if(empty($summary_rep_data)) {
    echo "<script type='text/javascript'>
        alert('Unauthorized Access. Get Outta Here!');
        window.top.close();  //close the current tab
      </script>";
} else {
    $html = ''; //set html to blank
    $clientID = "";
    
      $chart_data=array();
    $char_label=array();
    if(!empty($summary_rep_data)) {
        $familyID = $summary_rep_data[0]->family_id;
        $familyName = $summary_rep_data[0]->family_name;
    } else {
        $clientID = "";
        $clientName = "";
    }
    $css = '<style type="text/css">
                table { width:100%; color:#000000; }
                body {color:black;}
                table td {font-size: 12px; padding:2px; text-align:center; }
                table th {font-size: 12px; padding:2px; text-align:center; border: 1px solid #4d4d4d;}
                .border {border: 1px solid #4d4d4d;}
                .amount { text-align:right;text-indent: 15px; }
                .noWrap { white-space: nowrap; }
                .title { line-height:28px; background-color: #f4a817; color: #000; font-size:15px; font-weight:bold; text-align:center; border:2px double #4d4d4d; }
                .info { font-size: 12px; font-weight: lighter; border:none; }
                .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                .normal {font-weight: normal;}
                .dataTotal {font-weight: bold; color:#4f8edc;}
                .no-border {border-width: 0px; border-color:#fff;}
                .client-name { text-align: left; font-size: 14px; font-weight: bold; }
                .bigger {font-weight: bold; font-size:14px;}
            </style>';
    $title = '<div class="title row">Summary Report of '.$familyName.'</div>
                    <br/>';
    if($summary_rep_data)
    {
        $htmlVal = '';
        $header = true;
        $content = false;
        $footer = false;
        $clientTotal = 0; //overall amount
        //family totals
        $famInsTotal = 0;
        $famFDTotal = 0;
        $famMFTotal = 0;
        $famEquityTotal = 0;
        $famRETotal = 0;
        $famCommodityTotal = 0;
        $familyTotal = 0;

   $cfamInsTotal = 0;
        $cfamFDTotal = 0;
        $cfamMFTotal = 0;
        $cfamEquityTotal = 0;
        $cfamRETotal = 0;
        $cfamCommodityTotal = 0;

        foreach($summary_rep_data as $row)
        {
            $clientTotal = round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity);
           
            
            if($header) {
                $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                    <tbody>
                        <tr nobr="true" class="head-row">
                            <th width="90">Client Name</th>
                            <th width="110">Insurance</th>
                            <th width="110">Fixed Income</th>
                            <th width="110">Mutual Funds</th>
                            <th width="110">Equity</th>
                            <th width="110">Real Estate</th>
                            <th width="110">Commodity</th>
                            <th width="120">Total Portfolio</th>
                            <th width="110">Life Cover</th>
                        </tr>
                    </tbody>';
                $header = false;
                $content = true;
            }

            if($content) {
                $htmlVal .= '<tr nobr="true">';
                        $htmlVal .= '<td class="border normal">'.$row->client_name.'</td>';

                        $ins = intval($row->insurance_inv);
                        $htmlVal .= !empty($ins)?'<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->insurance_inv,0)).'</td>':'<td class="border normal"></td>';
                        // $htmlVal .= !empty($row->insurance_fund)?'<td>'.$this->common_lib->moneyFormatIndia(round($row->insurance_fund,2)).'</td>':'<td></td>';
                        $fd = intval($row->fixed_deposit);
                        $htmlVal .= !empty($fd)?'<td class="border  amount normal">'.$this->common_lib->moneyFormatIndia(round($row->fixed_deposit,0)).'</td>':'<td class="border normal"></td>';
                        $mf = intval($row->mutual_fund);
                        $htmlVal .= !empty($mf)?'<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->mutual_fund,0)).'</td>':'<td class="border normal"></td>';
                        $eq = intval($row->equity);
                        $htmlVal .= !empty($eq)?'<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->equity,0)).'</td>':'<td class="border normal"></td>';
                        $prop = intval($row->property);
                        $htmlVal .= !empty($prop)?'<td class="border  amount normal">'.$this->common_lib->moneyFormatIndia(round($row->property,0)).'</td>':'<td class="border normal"></td>';
                        $com = intval($row->commodity);
                        $htmlVal .= !empty($com)?'<td class="border  amount normal">'.$this->common_lib->moneyFormatIndia(round($row->commodity,0)).'</td>':'<td class="border normal"></td>';
                        $htmlVal .=  !empty($clientTotal)?'<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($clientTotal,0)).'</td>':'<td class="border normal"></td>';
                        // $htmlVal .= '<td class="border">'.$this->common_lib->moneyFormatIndia($clientTotal).'</td>';

                $lc = intval($row->life_cover);
                $htmlVal .= !empty($lc)?'<td class="border amount normal">'.$this->common_lib->moneyFormatIndia(round($row->life_cover,0)).'</td>':'<td class="border normal"></td>';
                    $htmlVal .= '</tr>';

                $famInsTotal = round($famInsTotal + $row->insurance_inv);
                $famFDTotal = round($famFDTotal + $row->fixed_deposit);
                $famMFTotal = round($famMFTotal + $row->mutual_fund);
                $famEquityTotal = round($famEquityTotal + $row->equity);
                $famRETotal = round($famRETotal + $row->property);
                $famCommodityTotal = round($famCommodityTotal + $row->commodity);
                $familyTotal = round($familyTotal + $clientTotal);
                
                  $cfamInsTotal = $famInsTotal;
                $cfamFDTotal = $famFDTotal;
                $cfamMFTotal = $famMFTotal;
                $cfamEquityTotal = $famEquityTotal;
                $cfamRETotal = $famRETotal;
                $cfamCommodityTotal =$famCommodityTotal;
            }
        }
        //footer with totals
        $htmlVal .= '<tr nobr="true">';
            $htmlVal .= '<td class="dataTotal border">Total</td>';
            $htmlVal .= !empty($famInsTotal)?'<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($famInsTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famFDTotal)?'<td class="dataTotal  amount border">'.$this->common_lib->moneyFormatIndia($famFDTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famMFTotal)?'<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($famMFTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famEquityTotal)?'<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($famEquityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famRETotal)?'<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($famRETotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= !empty($famCommodityTotal)?'<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($famCommodityTotal).'</td>':'<td class="dataTotal border"></td>';
            $htmlVal .= '<td class="dataTotal amount border">'.$this->common_lib->moneyFormatIndia($familyTotal).'</td>';
            $htmlVal .= '<td class="border"></td>
                </tr>
        </table>';
    }
      if($brokerID=='0004' || $brokerID=='0009' || $brokerID=='0174' || $brokerID=='0196'){
         
         
        if($summary_rep_previous_1)
        {   
            foreach($summary_rep_previous_1 as $row)
            {
                
           
                array_push($chart_data,round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity));
                $dob_temp = DateTime::createFromFormat('Y-m-d', $row->curr_date)->format('d/m/Y');
                
                array_push($char_label,$dob_temp);
                
            }
            
            array_push($chart_data,round($total));
            array_push($char_label,date('d/m/Y'));
            
            $chart_data_json = json_encode($chart_data);
            $char_label_json = json_encode($char_label);
        
        }
    
        
      /*  if($summary_rep_previous_2)
        {
        $htmlVal.='<br><br><div class="title row">Summary Report Pervious Day 2 </div><br/>';
            $header = true;
            $content = false;
            $footer = false;
            $clientTotal = 0; //overall amount
    
            foreach($summary_rep_previous_2 as $row)
            {
                $total = round($row->insurance_inv + $row->fixed_deposit + $row->mutual_fund + $row->equity + $row->property + $row->commodity);
                if($header) {
                    $htmlVal .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true" class="head-row">
                                <th width="120">Insurance</th>
                                <th width="120">Fixed Income</th>
                                <th width="120">Mutual Funds</th>
                                <th width="120">Equity</th>
                                <th width="120">Real Estate</th>
                                <th width="120">Commodity</th>
                                <th width="140">Total Portfolio</th>
                                <th width="120">Life Cover</th>
                            </tr>
                        </tbody>
                        <tbody>';
                    $header = false;
                    $content = true;
                }
    
                if($content) {
                    $htmlVal .= '<tr nobr="true">';
                            $ins = intval($row->insurance_inv);
                            $htmlVal .= !empty($ins)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->insurance_inv,0)).'</td>':'<td class="border normal"></td>';
                           // $htmlVal .= !empty($row->insurance_fund)?'<td>'.$this->common_lib->moneyFormatIndia(round($row->insurance_fund,2)).'</td>':'<td></td>';
                            $fd = intval($row->fixed_deposit);
                            $htmlVal .= !empty($fd)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->fixed_deposit,0)).'</td>':'<td class="border normal"></td>';
                            $mf = intval($row->mutual_fund);
                            $htmlVal .= !empty($mf)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->mutual_fund,0)).'</td>':'<td class="border normal"></td>';
                            $eq = intval($row->equity);
                            $htmlVal .= !empty($eq)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->equity,0)).'</td>':'<td class="border normal"></td>';
                            $prop = intval($row->property);
                            $htmlVal .= !empty($prop)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->property,0)).'</td>':'<td class="border normal"></td>';
                            $com = intval($row->commodity);
                            $htmlVal .= !empty($com)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->commodity,0)).'</td>':'<td class="border normal"></td>';
    
                            $htmlVal .= '<td class="dataTotal border">'.$this->common_lib->moneyFormatIndia(round($total,0)).'</td>';
    
                            $lc = intval($row->life_cover);
                            $htmlVal .= !empty($lc)?'<td class="border normal">'.$this->common_lib->moneyFormatIndia(round($row->life_cover,0)).'</td>':'<td class="border normal"></td>';
                        $htmlVal .= '</tr>';
                }
            }
            $htmlVal .= '</tbody>
                    </table>';
        }
        */
    }
    $html .= $css;
    $html .= $htmlVal;
    
}
?>
 
<div class="container" id="page-content" style="margin: 0; margin-top: -54px;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
     
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<script src="../../assets/vendors/CanvasJS/canvas.js"></script>
  <div>
    <input type="hidden" id="demo_id">
      <div id="page-heading">
            <div class="options">
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href='#' class="btn btn-default dropdown-toggle" data-toggle='dropdown'><i class="fa fa-cloud-download"></i><span class="hidden-xs"> Export as  </span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <?php $var=1;
                            $var=$var+1;?>
                            <li><a href="#" onclick="export_to_excel('<?php echo base_url('broker/Final_reports/export_to_excel');?>');">Excel File (*.xlsx)</a></li>
                            <li  id="l1"><a href="#" onclick="export_to_pdf('<?php echo base_url('client/Mutual_funds/export_to_pdf');?>');">PDF File (*.pdf)</a></li>
                            
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
                        <div class="row" id="report_data"  style="overflow-x : auto;"><?php echo $htmlVal; ?>
                        </div>
                                <?php  if($brokerID=='0004' || $brokerID=='0009' || $brokerID=='0174' ||  $brokerID=='0196'){?>
                        <br><br>
                        <div class="row">
                        
                        <div class="col-md-6 col-xs-12 col-sm-12" style="height:425px;max-height:height:425px">
                            
                                <div class="panel panel-body" style="height:425px;max-height:height:425px">
                                    <canvas id="mf-line-chart" style="height:425px;max-height:height:425px"></canvas>
                               </div>
                        </div>    
                        <div class="col-md-6 col-xs-12 col-sm-12"  style="    border: 1px solid #d2d3d6">
                                <!--<div class="panel panel-body" style="height:387px">
                                    <div class="text-center"><h4>Mutual Funds</h4></div>
                                    <canvas id="mf-chart"></canvas>
                               </div>-->
                                <div id="mf-chart"  class="chart_css" style="height:425px"></div>
                        </div>  
                        </div>
                        <?php }?>
                        <input type="hidden" name="logo" id="logo" value="<?php echo $logo;?>" />
                        <input type="hidden" name="name" id="name" value="<?php echo $familyName;?>" />
                        <input type="hidden" name="titleData" id="titleData" />
                        <input type="hidden" name="htmlData" id="htmlData" />
                          <input type="hidden" name="line_chart" id="line_chart"  />
                        <input type="hidden" name="pie_chart" id="pie_chart"  />
                        <input type="hidden" name="report_name" id="reportName" value="<?php echo $familyName;?> Family Summary Report" />
                    </div>
                </div>
            </form>
          </div>

    

    </div>
  </div>
  </div>

<script>
  


$(document).ready(function()
{
   

  summary_report_line_charts(<?php echo $chart_data_json; ?>,
         <?php echo $char_label_json; ?>);
         
        summary_report_pie_charts(<?php echo isset($cfamInsTotal)?intval($cfamInsTotal):intval(0); ?>,
                                <?php echo isset($cfamFDTotal)?intval($cfamFDTotal):intval(0); ?>,
                                <?php echo isset($cfamMFTotal)?intval($cfamMFTotal):intval(0); ?>,
                                <?php echo isset($cfamEquityTotal)?intval($cfamEquityTotal):intval(0); ?>,
                                <?php echo isset($cfamRETotal)?intval($cfamRETotal):intval(0); ?>,
                                <?php echo isset($cfamCommodityTotal)?intval($cfamCommodityTotal):intval(0); ?>);


});
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
                        "#03afcd",
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd"
                        
                        ],
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
                            fontSize: 14
                        }
                    }],
                    yAxes: [{
                        ticks: {
                                 fontSize: 14,
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


        myLineChart = new Chart(document.getElementById("mf-line-chart"),{
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
    
    function summary_report_pie_charts(val1,val2,val3,val4,val5,val6)
    {
      /*  var mfData = {
            labels: [
                "Insurance",
                "Fixed Income",
                "Mutual Funds",
                "Equity",
                "Real Estate",
                "Commodity"
            ],
            datasets: [
                {
                    data: [val1, val2, val3,val4,val5,val6],
                    backgroundColor: [
                        "#ed3237",
                        "#fbb12f",
                        "#03afcd",
                        "#0098da",
                        "#804744",
                        "#804766"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f",
                        "#324e9f",
                        "#324e9f",
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
         var options = {
          responsive: true,
          maintainAspectRatio: true
                        }


        var myPieChart = new Chart(document.getElementById("mf-chart"),{
            type: 'pie',
            data: mfData,
            options: options
        });
        */
           var total_val=val1+val2+val3+val4+val5+val6;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        var per3=((val3*100)/total_val).toFixed(2);
        var per4=((val4*100)/total_val).toFixed(2);
        var per5=((val5*100)/total_val).toFixed(2);
        var per6=((val6*100)/total_val).toFixed(2);
        
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
		dataPoints: [
			{y: val1, label: "Insurance",per:per1,color: "#ed3237"},
			{y: val2, label: "Fixed Income",per:per2,color: "#fbb12f"},
			{y: val3, label: "Mutual Funds",per:per3,color: "#03afcd"},
			{y: val4, label: "Equity",per:per4,color: "#0098da"},
			{y: val5, label: "Real Estate",per:per5,color: "#804744"},
			{y: val6, label: "Commodity",per:per6,color: "#804766"}
	
		]
	}]
});

chart.render();

$('.canvasjs-chart-credit').css('display','none');
       setTimeout(function(){
        
 var canvas = $(".canvasjs-chart-canvas").get(0);
    var dataURL = canvas.toDataURL('image/png');
            $('#pie_chart').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        
        },2000);
        
    }
    
</script>
