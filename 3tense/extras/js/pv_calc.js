    var $pvage,
        $pvafrom,
        $pvato,
        pvage,
        pvamin,
        pvamax,
        pvafrom,
        pvato;

    var $pvincome,
        $pvifrom,
        $pvito,
        pvimin,
        pvimax,
        pvifrom,
        pvincome,
        pvito;

    var $pvbalance,
        pvbalance,
        $pvbfrom,
        $pvbto,
        pvbmin,
        pvbmax,
        pvbfrom,
        pvbto;
    function ResetSliderPV()
    {
        var sliderInvestpv = $("#pvageslider").data("ionRangeSlider");
        sliderInvestpv.update({
            from: 1
           });

       $("#MonthSIPPV").val("1");

       //expected returns
       $("#pv_income").data("ionRangeSlider").update({
          from: 1
       });

       $("#pv_rateOfReturn").val("1");

       //No.Of years
       $("#pv_balance").data("ionRangeSlider").update({
          from: 1
       });
       $("#pv_installment").val("1");

    }
  
    $(document).ready(function(){
        //alert(base_url);
        $('#calpv').click(function(){          
            $('#pv_modal').modal('show');
        });
        
         $("#btn_ResetPV").click(function(){
             ResetSliderPV();
             $('#pvresult').css('display','none');
         });
         
          $('body').on('hidden.bs.modal', '.modal', function () {
            ResetSliderPV();
             $('#pvresult').css('display','none');
        });
       
         $("#btn_calcPV").click(function(){
            var MonthlySIP = $("#MonthSIPPV").val().replace(/,/g , "");
            var RateOfReturn = $("#pv_rateOfReturn").val();
            var installment = $("#pv_installment").val();
            
            var params = $('.freq:checked').val();
            var months, years, futurevalue, earnings,present_value;
            var sip_amount = MonthlySIP;
            var num_of_installment = installment;
            var sip_exinterest_rate = RateOfReturn;
          
            switch (params) {
                case "1":

                      /*  $interest_val=1+($_POST['interest']/100);
                        $future_val=$_POST['amount'] *((pow($interest_val,$_POST['year'])-1) / ($_POST['interest']/100));
                        $interest_val_new=1/$interest_val;
                        $p_val=$future_val*(pow($interest_val_new,$_POST['year']));
                        $data['future_val']=round($future_val,2);
                        $data['present_val']=round($p_val,2);*/


                    var n1 = 1 + (sip_exinterest_rate  /100);
                    futurevalue = sip_amount * (( Math.pow(n1, num_of_installment)- 1) / (sip_exinterest_rate / 100));
                    
                    
                    var n2 = 1/n1;
                    
                   

                  present_value=  sip_amount/ Math.pow(n1, num_of_installment);



                   // present_value=futurevalue *( Math.pow(n2, num_of_installment));
                    years = parseInt(num_of_installment / 12);
                    months = parseFloat(num_of_installment % 12);
                    break;

                default:
                    futurevalue = 0;
                    present_value = 0;
                    years = 0;
                    months = 0;
                    break;
            }
            
            futurevalue = futurevalue.toFixed(2);
            var investment = (sip_amount * num_of_installment).toFixed(2);
            earnings = (futurevalue - investment).toFixed(2);
            //alert(MonthlySIP);
          //  pvSIPReturn_charts(investment,earnings);
            $('#pvresult').css('display','block');
            //$('.pvinvestment').text(delimitNumbersValue(Math.round(futurevalue)));           
            $('.FVPV').text(delimitNumbersValue(Math.round(present_value)));
            $('#PV_hidden').val(delimitNumbersValue(Math.round(present_value)));
           // $('.pvprinciple').text(delimitNumbersValue(Math.round(investment)));
           // $('.pvinterest').text(delimitNumbersValue(Math.round(earnings)));
            
			
			
			 //code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'pv'},
                        success:function(data){
                          //alert(data);
                        }
                    });
             
             //end here 
			
			
			
			
			
        });
        
                $pvage = $("#pvageslider"),
                //$from = $(".js-from"),
                $pvato = $("#MonthSIPPV"),
                pvage,
                pvamin = 1,
                pvamax = 1000000000, 
                pvafrom=1,
                pvato=1000000000;

        $pvage.ionRangeSlider({
                type: "single",
                min: pvamin,
                max: pvamax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    pvato = data.from;
                    
                    pvupdateAgeValue();
                }
        });
        
        $pvincome = $("#pv_income"),
                //$from = $(".js-from"),
                 $pvito = $("#pv_rateOfReturn"),
                 pvincome,
                 pvimin = 1,
                 pvimax = 20, 
                 pvifrom=1,
                 pvito=20;

        $pvincome.ionRangeSlider({
             type: "single",
                min: pvimin,
                max: pvimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    pvito = data.from;

                    pvupdateincomeValue();
                }
        });
        
        $pvbalance = $("#pv_balance"),
                //$from = $(".js-from"),
                 $pvbto = $("#pv_installment"),
                 pvbalance,
                 pvbmin = 1,
                 pvbmax = 360, 
                 pvbfrom=0,
                 pvbto=360;

        $pvbalance.ionRangeSlider({
             type: "single",
                min: pvbmin,
                max: pvbmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    pvbto = data.from;

                    pvupdatebalanceValue();
                }
        });
        
        
        pvage = $pvage.data("ionRangeSlider");
        pvincome = $pvincome.data("ionRangeSlider");
        pvbalance = $pvbalance.data("ionRangeSlider");
        
        $pvato.on("change", function () {
            pvato = +$(this).val().replace(/,/g , "");
            if (pvato > pvamax) {
                pvato = pvamax;
            }
            pvupdateAgeValue();    
            pvupdateAgeRange();
        });
        
        $pvito.on("change", function () {
            pvito = +$(this).val();
            if (pvito > pvimax) {
                pvito = pvimax;
            }
            pvupdateincomeValue();    
            pvupdateincomeRange();
        });
        
        $pvbto.on("change", function () {
            pvbto = +$(this).val();
            if (pvbto > pvbmax) {
                pvbto = pvbmax;
            }
            pvupdatebalanceValue();    
            pvupdatebalanceRange();
        });
        
    });
    
    var pvupdateAgeValue = function () {    
       //$('').val(pvato);
         $pvato.val(delimitNumbersValue(pvato));
        //$ato.val(ato);
    };
    
    var pvupdateAgeRange = function () {
        pvage.update({
           from: pvato
           //to: ato
        });
    };
    
    var pvupdateincomeValue = function () {  
        
        $pvito.val(pvito);
        //$ato.val(ato);
    };
    
    var pvupdateincomeRange = function () {
        pvincome.update({
           from: pvito
           //to: ato
        });
    };
    
    var pvupdatebalanceValue = function () {
        $pvbto.val(pvbto);
    };
     
    var pvupdatebalanceRange = function () {
        pvbalance.update({
            from: pvbto       
        });
    };
    
    
    
    
    
   function pvSIPReturn_charts(val1,val2)
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
        var myPieChart = new Chart(document.getElementById("mf-chart_pv"),{
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
     
  /*  function delimitNumbersValue(x) {
    
       x=x.toString();
       var lastThree = x.substring(x.length-3);
       var otherNumbers = x.substring(0,x.length-3);
       if(otherNumbers != '')
           lastThree = ',' + lastThree;
       var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
      
        return res;
   } */