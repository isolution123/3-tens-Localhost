/****************************************************************************************/

var $vloan_amt,
    $vafrom,
    $vato,
    vloan_amt,
    vamin,
    vamax,
    vafrom,
    vato;

var $vr_rate,
    $vrfrom,
    $vrto,
    vr_rate,
    vrmin,    
    vrmax,
    vrfrom,
    vrto;

var $vp_time,
    $vpfrom,
    $vpto,    
    vp_time,
    vpmin,
    vpmax,
    vpfrom,
    vpto;
    
var $vi_inf,
    $vifrom,
    $vito,    
    vi_inf,
    vimin,
    vimax,
    vifrom,
    vito;
    
    
    function vacationresetSlider()
    {
        $("#v_amount").data("ionRangeSlider").update({
         from: 0
        });
        $("#s_v_amount").val("0");

        //expected returns
        $("#v_return").data("ionRangeSlider").update({
           from: 6
        });
        $("#s_v_return").val("6");
        
        //expected inflation
        $("#v_inflation").data("ionRangeSlider").update({
           from: 5
        });
        $("#s_v_inflation").val("5");

        //purchase years
        $("#v_period").data("ionRangeSlider").update({
           from: 1
        });
        $("#s_v_period").val("1");
        
        $(".vac_result").css('display','none');

    }
    
    $(document).ready(function(){
        
        $('#vacation').click(function(){
            
            $('#vacation_calc').modal('show');
            vacationresetSlider();
        });
        
        $("#vac_calc").click(function(){
            
            var vac_cost        = parseInt($("#v_amount").val().replace(/,/g , ""));
            var vacation_year   = parseInt($("#v_period").val());
            var vac_inflation   = parseFloat($("#v_inflation").val());
            var vac_interest    = parseFloat($("#v_return").val());
            var vac_name    = $("#v_name").val();
            var vac_mobile    = $("#v_mobileno").val();
            var vac_opt    = $("#v_otp").val();
            //alert(car_cost);
            
            if(vac_name == ''){
                alert('Please enter your name.');
                return false;
            }
             if(vac_mobile == 0){
                alert('Please enter your mobile no.');
                return false;
            }
             if(vac_opt == 0){
                alert('Please enter mobile opt.');
                return false;
            }
             if(vac_cost == 0){
                alert('Please specify your vacation cost.');
                return false;
            }
            
            //Car calcultaion
            fv = vac_cost*(Math.pow(1 + (vac_inflation/100),vacation_year));
            vrate = vac_interest/12/100;
            
            vnumber_of_payments = vacation_year * 12;
            
            vac_value = vacation_calc(vrate,vnumber_of_payments,0,fv,1);
                          
            //Lumpsum Value
            vLumpsumInvestment=Math.round((fv/(Math.pow(1+(vac_interest/100),vacation_year))));
                      
            $("#vcmonthly_amount").html(addCommas2(Math.round(vac_value)));
            $("#vcfuture_amount").html(addCommas2(Math.round(fv)));
            $("#vclumpsum_amount").html(addCommas2(Math.round(vLumpsumInvestment)));
            $(".vac_result").css('display','block');
            $("#hidden_vcmonthly_amount").val(addCommas2(Math.round(vac_value)));
            $("#hidden_vclumpsum_amount").val(addCommas2(Math.round(vLumpsumInvestment)));
            $("#hidden_vcfuture_amount").val(addCommas2(Math.round(fv)));
			
			
			//code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'vacation'},
                        success:function(data){
                          //alert(data);
                        }
                    });
             
             //end here 
			
			
			
			
        });
        
        
        //---------------------------- Slider Code ----------------------------------//
       
        //---------- vacation Amount ---------------
        
        $vloan_amt = $("#v_amount"),
                //$from = $(".js-from"),
                $vato = $("#s_v_amount"),
                vloan_amt,
                vamin = 0,
                vamax = 1000000000, 
                vafrom=0,
                vato=1000000000;

        $vloan_amt.ionRangeSlider({
                type: "single",
                min: vamin,
                max: vamax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    vato = data.from;
                    
                    updatevAmountValue();
                }
        });
        
        vloan_amt = $vloan_amt.data("ionRangeSlider");
        
        $vato.on("change", function () {
            vato = +$(this).val().replace(/,/g , "");
            if (vato > vamax) {
                vato = vamax;
            }
            updatevAmountValue();    
            updatevAmountRange();
        });
        
        //---------- Interst Rate ---------------
        
        $vr_rate = $("#v_return"),
                //$from = $(".js-from"),
                $vrto = $("#s_v_return"),
                vrmin = 1,
                vrmax = 15, 
                vrfrom=1,
                vrto=15;

        $vr_rate.ionRangeSlider({
                type: "single",
                min: vrmin,
                max: vrmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    vrto = data.from;
                    
                    updatevintrateValue();
                }
        });
        
        vr_rate = $vr_rate.data("ionRangeSlider");
        
        $vrto.on("change", function () {
            vrto = +$(this).val();
            if (vrto > vrmax) {
                vrto = vrmax;
            }
            updatevintrateValue();    
            updatevintrateRange();
        });
        
        
        //---------- years for purchase ---------------
        
        $vp_time = $("#v_period"),
                //$from = $(".js-from"),
                $vpto = $("#s_v_period"),
                vpmin = 1,
                vpmax = 50, 
                vpfrom=1,
                vpto=50;

        $vp_time.ionRangeSlider({
                type: "single",
                min: vpmin,
                max: vpmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    vpto = data.from;
                    
                    updatevperiodValue();
                }
        });
        
        vp_time = $vp_time.data("ionRangeSlider");
        
        $vpto.on("change", function () {
            vpto = +$(this).val();
            if (vpto > vpmax) {
                vpto = vpmax;
            }
            updatevperiodValue();    
            updatevperiodRange();
        });
        
        
        //---------- Inflation Rate ---------------
        
        $vi_inf = $("#v_inflation"),
                //$from = $(".js-from"),
                $vito = $("#s_v_inflation"),
                vimin = 1,
                vimax = 15, 
                vifrom=1,
                vito=15;

        $vi_inf.ionRangeSlider({
                type: "single",
                min: vimin,
                max: vimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    vito = data.from;
                    
                    updatevinfrateValue();
                }
        });
        
        vi_inf = $vi_inf.data("ionRangeSlider");
        
        $vito.on("change", function () {
            vito = +$(this).val();
            if (vito > vimax) {
                vito = vimax;
            }
            updatevinfrateValue();    
            updatevinfrateRange();
        });
    //-------------------------------------------------
    
    });
    
    
    //Slider Value Updates
    
    var updatevAmountValue = function () {    
        $vato.val(addCommas2(vato));
        //$ato.val(ato);
    };
    
    var updatevAmountRange = function () {
        vloan_amt.update({
           from: vato
           //to: ato
        });
    };
    
    var updatevintrateValue = function () {    
        $vrto.val(vrto);
        //$ato.val(ato);
    };
    
    var updatevintrateRange = function () {
        vr_rate.update({
           from: vrto
           //to: ato
        });
    };
    
    var updatevinfrateValue = function () {    
        $vito.val(vito);
        //$ato.val(ato);
    };
    
    var updatevinfrateRange = function () {
        vi_inf.update({
           from: vito
           //to: ato
        });
    };
    
    var updatevperiodValue = function () {    
        $vpto.val(vpto);
        //$ato.val(ato);
    };
    
    var updatevperiodRange = function () {
        vp_time.update({
           from: vpto
           //to: ato
        });
    };

    

    function vacation_calc(rate_per_period,number_of_payments,present_value,future_value,type){
        
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
    