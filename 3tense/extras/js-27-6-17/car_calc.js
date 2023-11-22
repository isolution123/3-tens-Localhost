/****************************************************************************************/

var $cloan_amt,
    $clfrom,
    $clto,
    cloan_amt,
    clmin,
    clmax,
    clfrom,
    clto;

var $cr_rate,
    $crfrom,
    $crto,
    cr_rate,
    crmin,    
    crmax,
    crfrom,
    crto;

var $cp_time,
    $cpfrom,
    $cpto,    
    cp_time,
    cpmin,
    cpmax,
    cpfrom,
    cpto;
    
var $ci_inf,
    $cifrom,
    $cito,    
    ci_inf,
    cimin,
    cimax,
    cifrom,
    cito;
    
    
    function CarresetSlider()
    {
        $("#c_amount").data("ionRangeSlider").update({
         from: 0
        });
        $("#s_c_amount").val("0");

        //expected returns
        $("#c_return").data("ionRangeSlider").update({
           from: 6
        });
        $("#s_c_return").val("6");
        
        //expected inflation
        $("#c_inflation").data("ionRangeSlider").update({
           from: 5
        });
        $("#s_c_inflation").val("5");

        //purchase years
        $("#c_period").data("ionRangeSlider").update({
           from: 1
        });
        $("#s_c_period").val("1");
        
        $(".car_result").css('display','none');

    }
    
    $(document).ready(function(){
        
        $('#carCalc').click(function(){
            
            $('#Car_calc').modal('show');
            CarresetSlider();
        });
        
        $("#car_calc").click(function(){
            
            var car_cost        = ($("#c_amount").val().replace(/,/g , ""));
            //car_cost = car_costreplace(/,/g , "");
            var purchase_year   = parseInt($("#c_period").val());
            var car_inflation   = parseFloat($("#c_inflation").val());
            var car_interest    = parseFloat($("#c_return").val());
            //alert(car_cost);
            
            if(car_cost == 0){
                alert('Please specify your car cost.');
                return false;
            }
            
            //Car calcultaion
            
            //future value
            
            fv = car_cost*(Math.pow(1 + (car_inflation/100),purchase_year));
            rate = car_interest/12/100;
            
            number_of_payments = purchase_year * 12;
            
            car_value = car_calc(rate,number_of_payments,0,fv,0);
                          
            //Lumpsum Value
            LumpsumInvestment=Math.round((fv/(Math.pow(1+(car_interest/100),purchase_year))));
                      
            $("#crmonthly_amount").html(addCommas2(Math.round(car_value)));
            $("#crfuture_amount").html(addCommas2(Math.round(fv)));
            $("#crlumpsum_amount").html(addCommas2(Math.round(LumpsumInvestment)));
            $(".car_result").css('display','block');
            
            
        });
        
        
        //---------------------------- Slider Code ----------------------------------//
       
        //---------- Car Amount ---------------
        
        $cloan_amt = $("#c_amount"),
                //$from = $(".js-from"),
                $clto = $("#s_c_amount"),
                cloan_amt,
                clmin = 0,
                clmax = 1000000000, 
                clfrom=0,
                clto=1000000000;

        $cloan_amt.ionRangeSlider({
                type: "single",
                min: clmin,
                max: clmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    clto = (data.from);
                    
                    updatecAmountValue();
                }
        });
        
        cloan_amt = $cloan_amt.data("ionRangeSlider");
        
        $clto.on("change", function () {
            clto = +$(this).val().replace(/,/g , "");;
            if (clto > clmax) {
                clto = clmax;
            }
            updatecAmountValue();    
            updatecAmountRange();
        });
        
        //---------- Interst Rate ---------------
        
        $cr_rate = $("#c_return"),
                //$from = $(".js-from"),
                $crto = $("#s_c_return"),
                crmin = 1,
                crmax = 15, 
                crfrom=1,
                crto=15;

        $cr_rate.ionRangeSlider({
                type: "single",
                min: crmin,
                max: crmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    crto = data.from;
                    
                    updatecintrateValue();
                }
        });
        
        cr_rate = $cr_rate.data("ionRangeSlider");
        
        $crto.on("change", function () {
            crto = +$(this).val();
            if (crto > crmax) {
                crto = crmax;
            }
            updatecintrateValue();    
            updatecintrateRange();
        });
        
        
        //---------- years for purchase ---------------
        
        $cp_time = $("#c_period"),
                //$from = $(".js-from"),
                $cpto = $("#s_c_period"),
                cpmin = 1,
                cpmax = 50, 
                cpfrom=1,
                cpto=50;

        $cp_time.ionRangeSlider({
                type: "single",
                min: cpmin,
                max: cpmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    cpto = data.from;
                    
                    updatecperiodValue();
                }
        });
        
        cp_time = $cp_time.data("ionRangeSlider");
        
        $cpto.on("change", function () {
            cpto = +$(this).val();
            if (cpto > cpmax) {
                cpto = cpmax;
            }
            updatecperiodValue();    
            updatecperiodRange();
        });
        
        
        //---------- Inflation Rate ---------------
        
        $ci_inf = $("#c_inflation"),
                //$from = $(".js-from"),
                $cito = $("#s_c_inflation"),
                cimin = 1,
                cimax = 15, 
                cifrom=1,
                cito=15;

        $ci_inf.ionRangeSlider({
                type: "single",
                min: cimin,
                max: cimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    cito = data.from;
                    
                    updatecinfrateValue();
                }
        });
        
        ci_inf = $ci_inf.data("ionRangeSlider");
        
        $cito.on("change", function () {
            cito = +$(this).val();
            if (cito > cimax) {
                cito = cimax;
            }
            updatecinfrateValue();    
            updatecinfrateRange();
        });
    //-------------------------------------------------
    
    });
    
    
    //Slider Value Updates
    
    var updatecAmountValue = function () {    
        $clto.val(addCommas2(clto));
        //$ato.val(ato);
    };
    
    var updatecAmountRange = function () {
        cloan_amt.update({
           from: clto
           //to: ato
        });
    };
    
    var updatecintrateValue = function () {    
        $crto.val(crto);
        //$ato.val(ato);
    };
    
    var updatecintrateRange = function () {
        cr_rate.update({
           from: crto
           //to: ato
        });
    };
    
    var updatecinfrateValue = function () {    
        $cito.val(cito);
        //$ato.val(ato);
    };
    
    var updatecinfrateRange = function () {
        ci_inf.update({
           from: cito
           //to: ato
        });
    };
    
    var updatecperiodValue = function () {    
        $cpto.val(cpto);
        //$ato.val(ato);
    };
    
    var updatecperiodRange = function () {
        cp_time.update({
           from: cpto
           //to: ato
        });
    };

    

    function car_calc(rate_per_period,number_of_payments,present_value,future_value,type){
        
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
    