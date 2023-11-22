/****************************************************************************************/

var $hloan_amt,
    $hafrom,
    $hato,
    hloan_amt,
    hamin,
    hamax,
    hafrom,
    hato;

var $hr_rate,
    $hrfrom,
    $hrto,
    hr_rate,
    hrmin,    
    hrmax,
    hrfrom,
    hrto;

var $hp_time,
    $hpfrom,
    $hpto,    
    hp_time,
    hpmin,
    hpmax,
    hpfrom,
    hpto;
    
var $hi_inf,
    $hifrom,
    $hito,    
    hi_inf,
    himin,
    himax,
    hifrom,
    hito;
    
    
    function homeresetSlider()
    {
        $("#h_amount").data("ionRangeSlider").update({
         from: 0
        });
        $("#s_h_amount").val("0");

        //expected returns
        $("#h_return").data("ionRangeSlider").update({
           from: 6
        });
        $("#s_h_return").val("6");
        
        //expected inflation
        $("#h_inflation").data("ionRangeSlider").update({
           from: 5
        });
        $("#s_h_inflation").val("5");

        //purchase years
        $("#h_period").data("ionRangeSlider").update({
           from: 1
        });
        $("#s_h_period").val("1");
        
        $(".home_result").css('display','none');

    }
    
    $(document).ready(function(){
        //alert(5858);
        $('#homeCalc').click(function(){
            
            $('#home_modal').modal('show');
            homeresetSlider();
        });
        
        $("#home_calc").click(function(){
            
            var home_cost        = parseInt($("#h_amount").val().replace(/,/g , ""));
            var home_year        = parseInt($("#h_period").val());
            var home_inflation   = parseFloat($("#h_inflation").val());
            var home_interest    = parseFloat($("#h_return").val());
            //alert(home_cost);
            
            if(home_cost == 0){
                alert('Please specify your home cost.');
                return false;
            }
            
            //Car calcultaion
            fv = home_cost*(Math.pow(1 + (home_inflation/100),home_year));
            hrate = home_interest/12/100;
            
            hnumber_of_payments = home_year * 12;
            
            home_value = home_calc(hrate,hnumber_of_payments,0,fv,1);
                          
            //Lumpsum Value
            hLumpsumInvestment=Math.round((fv/(Math.pow(1+(home_interest/100),home_year))));
                      
            $("#hcmonthly_amount").html(addCommas2(Math.round(home_value)));
            $("#hcfuture_amount").html(addCommas2(Math.round(fv)));
            $("#hclumpsum_amount").html(addCommas2(Math.round(hLumpsumInvestment)));
            $(".home_result").css('display','block');
            
            
        });
        
        
        //---------------------------- Slider Code ----------------------------------//
       
        //---------- Home Amount ---------------
        
        $hloan_amt = $("#h_amount"),
                //$from = $(".js-from"),
                $hato = $("#s_h_amount"),
                hloan_amt,
                hamin = 0,
                hamax = 1000000000, 
                hafrom=0,
                hato=1000000000;

        $hloan_amt.ionRangeSlider({
                type: "single",
                min: hamin,
                max: hamax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hato = data.from;
                    
                    updatehAmountValue();
                }
        });
        
        hloan_amt = $hloan_amt.data("ionRangeSlider");
        
        $hato.on("change", function () {
            hato = +$(this).val().replace(/,/g , "");
            if (hato > hamax) {
                hato = hamax;
            }
            updatehAmountValue();    
            updatehAmountRange();
        });
        
        //---------- Interst Rate ---------------
        
        $hr_rate = $("#h_return"),
                //$from = $(".js-from"),
                $hrto = $("#s_h_return"),
                hrmin = 1,
                hrmax = 15, 
                hrfrom=1,
                hrto=15;

        $hr_rate.ionRangeSlider({
                type: "single",
                min: hrmin,
                max: hrmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    hrto = data.from;
                    
                    updatehintrateValue();
                }
        });
        
        hr_rate = $hr_rate.data("ionRangeSlider");
        
        $hrto.on("change", function () {
            hrto = +$(this).val();
            if (hrto > hrmax) {
                hrto = hrmax;
            }
            updatehintrateValue();    
            updatehintrateRange();
        });
        
        
        //---------- years for purchase ---------------
        
        $hp_time = $("#h_period"),
                //$from = $(".js-from"),
                $hpto = $("#s_h_period"),
                hpmin = 1,
                hpmax = 50, 
                hpfrom=1,
                hpto=50;

        $hp_time.ionRangeSlider({
                type: "single",
                min: hpmin,
                max: hpmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hpto = data.from;
                    
                    updatehperiodValue();
                }
        });
        
        hp_time = $hp_time.data("ionRangeSlider");
        
        $hpto.on("change", function () {
            hpto = +$(this).val();
            if (hpto > hpmax) {
                hpto = hpmax;
            }
            updatehperiodValue();    
            updatehperiodRange();
        });
        
        
        //---------- Inflation Rate ---------------
        
        $hi_inf = $("#h_inflation"),
                //$from = $(".js-from"),
                $hito = $("#s_h_inflation"),
                himin = 1,
                himax = 15, 
                hifrom=1,
                hito=15;

        $hi_inf.ionRangeSlider({
                type: "single",
                min: himin,
                max: himax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    hito = data.from;
                    
                    updatehinfrateValue();
                }
        });
        
        hi_inf = $hi_inf.data("ionRangeSlider");
        
        $hito.on("change", function () {
            hito = +$(this).val();
            if (hito > himax) {
                hito = himax;
            }
            updatehinfrateValue();    
            updatehinfrateRange();
        });
    //-------------------------------------------------
    
    });
    
    
    //Slider Value Updates
    
    var updatehAmountValue = function () {    
        $hato.val(addCommas2(hato));
        //$ato.val(ato);
    };
    
    var updatehAmountRange = function () {
        hloan_amt.update({
           from: hato
           //to: ato
        });
    };
    
    var updatehintrateValue = function () {    
        $hrto.val(hrto);
        //$ato.val(ato);
    };
    
    var updatehintrateRange = function () {
        hr_rate.update({
           from: hrto
           //to: ato
        });
    };
    
    var updatehinfrateValue = function () {    
        $hito.val(hito);
        //$ato.val(ato);
    };
    
    var updatehinfrateRange = function () {
        hi_inf.update({
           from: hito
           //to: ato
        });
    };
    
    var updatehperiodValue = function () {    
        $hpto.val(hpto);
        //$ato.val(ato);
    };
    
    var updatehperiodRange = function () {
        hp_time.update({
           from: hpto
           //to: ato
        });
    };

    

    function home_calc(rate_per_period,number_of_payments,present_value,future_value,type){
        
        if(rate_per_period != 0.0){
            // Interest rate exists
            var q = Math.pow(1 + rate_per_period, number_of_payments);
            
            var resultinmt = (rate_per_period * (future_value + (q * present_value))) / ((-1 + q) * (1 + rate_per_period * (type)));
            
            return MonthSIPAmount= Math.round(resultinmt);

        } else if(number_of_payments != 0.0){
            // No interest rate, but number of payments exists
            return (future_value + present_value) / number_of_payments;
        } 
    }