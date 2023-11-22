    var $Dage,
        $Dafrom,
        $Dato,
        Dage,
        Damin,
        Damax,
        Dafrom,
        Dato;

    var $Dincome,
        $Difrom,Dincome,
        $Dito,
        Dimin,
        Dimax,
        Difrom,
        Dito;

    var $Dbalance,
        $Dbfrom,
        $Dbto,Dbalance,
        Dbmin,
        Dbmax,
        Dbfrom,
        Dbto;

var $delay,
        $dfrom,
        $d_to,
        delay,
        dmin,
        dmax,
        dfrom,
        dto;

 

function CommaSeparated(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];    
    x2 = x.length > 1 ? '.' + x[1] : '';
    if(x1.length>3)
    {
        var last3digits = x1.substring(x1.length,x1.length-3);  
        var remainingdigits = x1.substring(0,x1.length-3);        
        var commaseparateddigits = addCommas(remainingdigits);   
        return commaseparateddigits+","+last3digits + x2;
    }
    else
    {
        return x1 + x2;
    }
}

    $(document).ready(function(){
        //alert(base_url);
        $('#btn_calcSIPdelay').click(function(){          
          $('#delay_modal').modal('show');
        });
        
         $("#btn_calcDelay").click(function(){
             /* hid.style.display = "inline";
	    var cl_moninst = (document.cl_tools.moninst.value);
	    cl_moninst = cl_moninst.replace(/,/,"")
	    var cl_tenure = (document.cl_tools.tenure.value); 
	    cl_tenure = cl_tenure.replace(/,/,"")
	    var cl_rateofretun = (document.cl_tools.rateofretun.value); 
	    cl_rateofretun = cl_rateofretun.replace(/,/,"")
	    var delay = (document.cl_tools.delay.value); 
	    delay = delay.replace(/,/,"")
	    var A = (document.cl_tools.tenure.value*12);
	    var B = parseFloat(futurevalue(cl_rateofretun,1/12,0,-100,1)-100);
	    var C = parseFloat(-futurevalue(B,A,cl_moninst,0,1));
	    document.cl_tools.invvaluetoday.value = Math.round(C);
	    D = (A - delay);
	    E = (-futurevalue(B,D,cl_moninst,0,1));
	    document.cl_tools.invvaluedelay.value = Math.round(E);
	    document.cl_tools.delaycost.value = Math.round(C-E);
	    F = Math.round(Math.round(C-E)/Math.pow((1+0.06),cl_tenure));
	    document.cl_tools.lostmoney.value = (F); */
            
            
            var txtCroreAmount = parseFloat($("#delaySIPValue").val());       
            var tenure =  parseFloat($("#delayInstallment").val());
            var rateofretun = parseFloat($("#delayRateOfInterest").val()); // for months
            var delay = parseFloat($("#delay").val()); 
            
            var tenure_val = tenure*12;
            var FV = parseFloat(Dfuturevalue(rateofretun,1/12,0,-100,1)-100); 
            var PV_amt = parseFloat(-Dfuturevalue(FV,tenure_val,txtCroreAmount,0,1));
            var Difff = (tenure_val - delay);
            var E_value = (-Dfuturevalue(FV,Difff,txtCroreAmount,0,1));
            var F = Math.round(Math.round(PV_amt-E_value)/Math.pow((1+0.06),tenure));
          
            $('#resultdelay').css('display','block');
            $(".invvaluetoday").text(Math.round(PV_amt));
            $(".invvaluedelay").text(Math.round(E_value));
            $(".delaycost").text(Math.round(PV_amt-E_value));
            $("#lostmoney").text(Math.round(F));
            
        });
        
        $Dage = $("#delayAgeslider"),
                //$from = $(".js-from"),
                $Dato = $("#delaySIPValue"),
                Dage,
                Damin = 1,
                Damax = 1000000, 
                Dafrom=1,
                Dato=1000000;

        $Dage.ionRangeSlider({
                type: "single",
                min: Damin,
                max: Damax,
                prettify_enabled: false,
                grid: true,
                grid_num: 10,
                onChange: function (data) {
                    //from = data.from;
                    Dato = data.from;                    
                    DupdateAgeValue();
                }
        });
        
        $Dincome = $("#delayIncome"),
                //$from = $(".js-from"),
                 $Dito = $("#delayRateOfInterest"),
                 Dincome,
                 Dimin = 1,
                 Dimax = 20, 
                 Difrom=1,
                 Dito=20;

        $Dincome.ionRangeSlider({
             type: "single",
                min: Dimin,
                max: Dimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 10,
                onChange: function (data) {
                    //from = data.from;
                    Dito = data.from;

                    DupdateincomeValue();
                }
        });
        
        $Dbalance = $("#delayBalance"),
                //$from = $(".js-from"),
                 $Dbto = $("#delayInstallment"),
                 Dbalance,
                 Dbmin = 1,
                 Dbmax = 360, 
                 Dbfrom=0,
                 Dbto=360;

        $Dbalance.ionRangeSlider({
             type: "single",
                min: Dbmin,
                max: Dbmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 40,
                onChange: function (data) {
                    //from = data.from;
                    Dbto = data.from;

                    DupdatebalanceValue();
                }
        });
        
        $delay = $("#delay"),
                //$from = $(".js-from"),
                 $d_to = $("#s_delay"),
                 delay,
                 dmin = 1,
                 dmax = 360, 
                 dfrom=0,
                 dto=360;

        $delay.ionRangeSlider({
             type: "single",
                min: dmin,
                max: dmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 40,
                onChange: function (data) {
                    //from = data.from;
                    dto = data.from;

                    DupdateDealyValue();
                }
        });
        
        
        Dage = $Dage.data("ionRangeSlider");
        Dincome = $Dincome.data("ionRangeSlider");
        Dbalance = $Dbalance.data("ionRangeSlider");
         delay = $delay.data("ionRangeSlider");
        
        $Dato.on("change", function () {
            Dato = +$(this).val();
            if (Dato > Damax) {
                Dato = Damax;
            }
            DupdateAgeValue();    
            DupdateAgeRange();
        });
        
        $Dito.on("change", function () {
            Dito = +$(this).val();
            if (Dito > Dimax) {
                Dito = Dimax;
            }
            DupdateincomeValue();    
            DupdateincomeRange();
        });
        
        $Dbto.on("change", function () {
            Dbto = +$(this).val();
            if (Dbto > Dbmax) {
                Dbto = Dbmax;
            }
            DupdatebalanceValue();    
            DupdatebalanceRange();
        });
        
        $d_to.on("change", function () {
            dto = +$(this).val();
            if (dto > dmax) {
                dto = dmax;
            }
            DupdateDealyValue();    
            DupdateDealyRange();
        });
        
    });
    
    var DupdateAgeValue = function () {    
        $Dato.val(Dato);
        //$ato.val(ato);
    };
    
    var DupdateAgeRange = function () {
        Dage.update({
           from: Dato
           //to: ato
        });
    };
    
    var DupdateincomeValue = function () {    
        $Dito.val(Dito);
        //$ato.val(ato);
    };
    
    var DupdateincomeRange = function () {
        Dincome.update({
           from: Dito
           //to: ato
        });
    };
    
    var DupdatebalanceValue = function () {
        $Dbto.val(Dbto);
    };
     
    var DupdatebalanceRange = function () {
        Dbalance.update({
            from: Dbto       
        });
    };
    
     var DupdateDealyValue = function () {
        $d_to.val(dto);
    };
     
    var DupdateDealyRange = function () {
        delay.update({
            from: dto       
        });
    };
    
    
    
   function DSIPReturn_charts(val1,val2)
    {
        var propData = {
            labels: [
                "Your Investment",
                "Your Earning"
            ],
            datasets: [
                {
                    data: [val1, val2],
                    backgroundColor: [
                        "#419641",
                        "#f0ad4e"
                    ],
                    hoverBackgroundColor: [
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
            data: propData,
            options: options,
            series: {
            pie: {
              show: true,
              radius: 1,
             label: {
                show: true,
                radius: 3/4,
                formatter: 'pieLabe',
                background: {
                    opacity: 0.5
                }
            }
        }
    },
    legend: {
        show: false
    }
            
            
        });

     }            
            
    function Dfuturevalue(returnspercent,nper,PMT,pv,type)
    {
      var rate = returnspercent/100;
          var fv;
          fv =(PMT*(1+rate*type)*(1- Math.pow(1+ rate,nper))/rate)-pv*Math.pow(1+rate,nper);
          return (fv);
    } 
        