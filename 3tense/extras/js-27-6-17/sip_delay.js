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

 function delimitNumbersValue(x) {
     /* return (str + "").replace(/\b(\d+)((\.\d+)*)\b/g, function(a, b, c) {
        return (b.charAt(0) > 0 && !(c || ".").lastIndexOf(".") ? b.replace(/(\d)(?=(\d{3})+$)/g, "$1,") : b) + c;
      });*/
    //  alert("gfdgfdg"+x);
     // var x=51015647;
     
    /* //for decimal point
     x=x.toString();
    var afterPoint = '';
    if(x.indexOf('.') > 0)
       afterPoint = x.substring(x.indexOf('.'),x.length);
    x = Math.floor(x);
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;*/
        x=x.toString();
        var lastThree = x.substring(x.length-3);
        var otherNumbers = x.substring(0,x.length-3);
        if(otherNumbers != '')
            lastThree = ',' + lastThree;
        var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;

       // alert(res);
    return res;
    }

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
function ResetSlider_Delay()
        {
            var sliderInvestpv = $("#delayAgeslider").data("ionRangeSlider");
            sliderInvestpv.update({
                from: 1
               });

           $("#delaySIPValue").val("1");

           //expected returns
           $("#delayIncome").data("ionRangeSlider").update({
              from: 1
           });

           $("#delayRateOfInterest").val("1");

           //No.Of years
           $("#delayBalance").data("ionRangeSlider").update({
              from: 1
           });
           $("#delayInstallment").val("1");
           //No.Of delay
           $("#delay").data("ionRangeSlider").update({
              from: 1
           });
           $("#s_delay").val("1");

        }
        
    $(document).ready(function(){
        //alert(base_url);
        $('#btn_calcSIPdelay').click(function(){          
          $('#delay_modal').modal('show');
        });
         $("#btn_ResetDelay").click(function(){
             ResetSlider_Delay();
              $('#resultdelay').css('display','none');
         });
          $('body').on('hidden.bs.modal', '.modal', function () {
           ResetSlider_Delay();
          $('#resultdelay').css('display','none');
       });
       
         $("#btn_calcDelay").click(function(){
                       
            
            /*var txtCroreAmount = parseFloat($("#delaySIPValue").val().replace(/,/g , ""));       
            var tenure =  parseFloat($("#delayInstallment").val());
            var rateofretun = parseFloat($("#delayRateOfInterest")); // for months
            var delay = parseFloat($("#delay").val()); 
            
            var tenure_val = tenure*12;
            alert(tenure_val);
            var FV = parseFloat(Dfuturevalue(rateofretun,1/12,0,-100,1)-100); 
            var PV_amt = parseFloat(-Dfuturevalue(FV,tenure_val,txtCroreAmount,0,1));
            var Difff = (tenure_val - delay);
            var E_value = (-Dfuturevalue(FV,Difff,txtCroreAmount,0,1));
            var F = Math.round(Math.round(PV_amt-E_value)/Math.pow((1+0.06),tenure));*/
            
            
            
            var txtCroreAmount = parseFloat($("#delaySIPValue").val().replace(/,/g , ""));       
            var tenure =  parseFloat($("#delayInstallment").val());
            var rateofretun = parseFloat($("#delayRateOfInterest").val()); // for months
            var delay = parseFloat($("#delay").val()); 
            
            var tenure_val = tenure*12;
            var FV = parseFloat(Dfuturevalue(rateofretun,1/12,0,-100,1)-100); 
            var PV_amt = parseFloat(-Dfuturevalue(FV,tenure_val,txtCroreAmount,0,1));
            var Difff = (tenure_val - delay);
            var E_value = (-Dfuturevalue(FV,Difff,txtCroreAmount,0,1));
            var F = Math.round(Math.round(PV_amt-E_value)/Math.pow((1+0.06),tenure));
            
            
            
          //  var test=delimitNumberstest(PV_amt);
        //  alert(test);
            $('#resultdelay').css('display','table');
            $(".invvaluetoday").text(delimitNumbersValue(Math.round(PV_amt)));
            $(".invvaluedelay").text(delimitNumbersValue(Math.round(E_value)));
            $(".delaycost").text(delimitNumbersValue(Math.round(PV_amt-E_value)));
            $("#lostmoney").text(delimitNumbersValue(Math.round(PV_amt-E_value)));
            $("#lostMonths").text(delay);
            
        });
        
        $Dage = $("#delayAgeslider"),
                //$from = $(".js-from"),
                $Dato = $("#delaySIPValue"),
                Dage,
                Damin = 1,
                Damax = 1000000000, 
                Dafrom=1,
                Dato=100;

        $Dage.ionRangeSlider({
                type: "single",
                min: Damin,
                max: Damax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
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
                grid_num: 1,
                step:0.25,
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
                grid_num: 1,
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
                grid_num: 1,
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
            Dato = +$(this).val().replace(/,/g , "");
            if (Dato > Damax) {
                Dato = Damax;
            }
            DupdateAgeValue();    
            DupdateAgeRange();
        });
        
        $Dito.on("change", function () {
            Dito = +$(this).val().replace(/,/g , "");
            if (Dito > Dimax) {
                Dito = Dimax;
            }
            DupdateincomeValue();    
            DupdateincomeRange();
        });
        
        $Dbto.on("change", function () {
            Dbto = +$(this).val().replace(/,/g , "");
            if (Dbto > Dbmax) {
                Dbto = Dbmax;
            }
            DupdatebalanceValue();    
            DupdatebalanceRange();
        });
        
        $d_to.on("change", function () {
            dto = +$(this).val().replace(/,/g , "");
            if (dto > dmax) {
                dto = dmax;
            }
            DupdateDealyValue();    
            DupdateDealyRange();
        });
        
    });
    
    var DupdateAgeValue = function () {    
        $Dato.val(delimitNumbersValue(Dato));
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
        