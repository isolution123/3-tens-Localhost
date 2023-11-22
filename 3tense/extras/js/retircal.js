var $need_age,
        $need_afrom,
        $need_ato,
        need_age,
        need_amin,
        need_amax,
        need_afrom,
        need_ato;

var $need_income,
        $need_ifrom,
        $need_ito,
        need_imin,
        need_imax,
        need_ifrom,
        need_ito;

var $need_balance,
        $need_bfrom,
        $need_bto,
        need_bmin,
        need_bmax,
        need_bfrom,
        need_bto;

    function need_pmt(rate_per_period, number_of_payments, present_value, future_value, type){
        if(rate_per_period != 0.0){
            // Interest rate exists
            var needq = Math.pow(1 + rate_per_period, number_of_payments);
            var need_resultinmt = (rate_per_period * (future_value + (needq * present_value))) / ((-1 + needq) * (1 + rate_per_period * (type)));
           var  needMonthSIPAmount= Math.round(need_resultinmt);
            return needMonthSIPAmount;
        } else if(number_of_payments != 0.0){
            // No interest rate, but number of payments exists
            return (future_value + present_value) / number_of_payments;
        } 
    }
    function ResetSliderNeed()
        {
            var sliderInvestpv = $("#need_ageslider").data("ionRangeSlider");
            sliderInvestpv.update({
                from: 1
               });

           $("#needSIPValue").val("1");

           //expected returns
           $("#need_income").data("ionRangeSlider").update({
              from: 1
           });

           $("#needrateOfInterest").val("1");

           //No.Of years
           $("#need_balance").data("ionRangeSlider").update({
              from: 1
           });
           $("#needinstallment").val("1");

        }
$(document).ready(function () {
    //alert(base_url);
    $('#retirmentCal').click(function () {
        $('#RetirmentPlaning').modal('show');
    });
    $("#btn_ResetSIPNeed").click(function(){
             ResetSliderNeed();
              $('#showTable').find('table').remove();
             $('#needresult').css('display','none');
         });
     
     $('body').on('hidden.bs.modal', '.modal', function () {
           ResetSliderNeed();
             $('#showTable').find('table').remove();
             $('#needresult').css('display','none');
        });
        
    $("#btn_calcRetirment").click(function () {

        var retir_current_age_value = 0.0;
        var retir_age_value = 0.0;
        var retir_life_expectancy_value = 0.0;
        var retir_current_monthly_expenses_value = 0.0;
        var retir_assumed_inflation_value = 0.0;
        var retir_post_retirement_return_value = 0.0;
        var retir_existing_investment_linked_value = 0.0;
        var retir_return_on_existing_investment_value = 0.0;
        var retir_pre_retirement_return_value = 0.0;
        var retir_planned_lumspum_investment_value=0.0;
        
        retir_current_age_value = parseFloat($("#retir_current_age_value").val());
        retir_age_value = parseFloat($("#retir_age_value").val()); // for years
        retir_life_expectancy_value = parseFloat($("#retir_life_expectancy_value").val()); // for months
        retir_current_monthly_expenses_value= parseFloat($("#retir_current_monthly_expenses_value").val()); // for months
        retir_assumed_inflation_value= parseFloat($("#retir_assumed_inflation_value").val()); // for months
        retir_post_retirement_return_value= parseFloat($("#retir_post_retirement_return_value").val()); // for months
        retir_existing_investment_linked_value= parseFloat($("#retir_existing_investment_linked_value").val()); // for months
        retir_return_on_existing_investment_value= parseFloat($("#retir_return_on_existing_investment_value").val()); // for months
        retir_pre_retirement_return_value= parseFloat($("#retir_pre_retirement_return_value").val()); // for months
        retir_planned_lumspum_investment_value=parseFloat($("#retir_planned_lumspum_investment_value").val()); // for months
      /*  
          retir_current_age_value = parseFloat(37);
        retir_age_value = parseFloat(60); // for years
        retir_life_expectancy_value = parseFloat(84); // for months
        retir_current_monthly_expenses_value= parseFloat(50000); // for months
        retir_assumed_inflation_value= parseFloat(7); // for months
        retir_post_retirement_return_value= parseFloat(10); // for months
        retir_existing_investment_linked_value= parseFloat(200000); // for months
        retir_return_on_existing_investment_value= parseFloat(14); // for months
        retir_pre_retirement_return_value= parseFloat(14); // for months
        retir_planned_lumspum_investment_value=parseFloat(0); // for months */
        
        
        var getFvValue=fv((retir_assumed_inflation_value/100),(retir_age_value-retir_current_age_value),0,-(retir_current_monthly_expenses_value),false);
        var fvval=(getFvValue*12);
        
        var rate=(((1+retir_post_retirement_return_value/100)/(1+retir_assumed_inflation_value/100))-1)*100;
        
        
        
       
        var pvval=Math.abs( PV((rate/100), (retir_life_expectancy_value-retir_age_value), fvval, 0, 1));
        var fvForPMT=fv((retir_return_on_existing_investment_value/100),(retir_age_value-retir_current_age_value),0,-(retir_existing_investment_linked_value),0);
        
        
        
            var NetCorpusRequired=Math.abs((pvval*(-1)))-fvForPMT;
            
        var pmtval=PMT((retir_pre_retirement_return_value/100), (retir_age_value-retir_current_age_value), 0, -(NetCorpusRequired), 1);
         
         
    
        var str1 = "<table class='table table-bordered' id='TblFutureVal'><thead>\n\
                            <tr >\n\
                                <th class='center subheader' style='width:15px;'>Expenses @ Retirement</th>\n\
                                <th class='center subheader' style='width:15px;'>Retirement Corpus</th>\n\
                                <th class='center subheader' style='width:15px;'>Monthly Savings </th>\n\
                            </tr></thead><tbody>";
    
    
            str1 += "<tr class='remv' >\n\
                        <td  width='55px' align='center'>"  +  addCommas2(fvval)+ "</td>\n\
                        <td id='custom_1' align='center'>" + addCommas2(pvval)+ "</td> \n\
                        <td id='custom_2' align='center'>" + addCommas2(pmtval/12)+ "</td>\n\
                    </tr>";

        str1 += "</tbody></table>";
        $('#retirementshowTable').find('table').remove();
        $('#retirementshowTable').append(str1);
         //code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'retirment-planing'},
                        success:function(data){
                          //alert(data);
                        }
                    });
             
             //end here 
			
		
		
		
    });
    
    $retir_current_age = $("#retir_current_age"),
            //$from = $(".js-from"),
            $need_ato = $("#retir_current_age_value"),
            need_age,
            need_amin = 1,
            need_amax = 100,
            need_afrom = 1,
            need_ato = 100;

    $retir_current_age.ionRangeSlider({
        type: "single",
        min: need_amin,
        max: need_amax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_ato = data.from;
            need_updateAgeValue();
        }
    });

    $retir_age = $("#retir_age"),
            //$from = $(".js-from"),
            $need_ito = $("#retir_age_value"),
            need_income,
            need_imin = 1,
            need_imax = 100,
            need_ifrom = 1,
            need_ito = 100;

    $retir_age.ionRangeSlider({
        type: "single",
        min: need_imin,
        max: need_imax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        step:0.25,
        onChange: function (data) {
            //from = data.from;
            need_ito = data.from;

            need_updateincomeValue();
        }
    });

    $retir_life_expectancy = $("#retir_life_expectancy"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_life_expectancy_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 200,
            need_bfrom = 0,
            need_bto = 200;

    $retir_life_expectancy.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
    
    $retir_current_monthly_expenses = $("#retir_current_monthly_expenses"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_current_monthly_expenses_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 1000000000,
            need_bfrom = 0,
            need_bto = 1000000000;

    $retir_current_monthly_expenses.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
    $retir_assumed_inflation = $("#retir_assumed_inflation"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_assumed_inflation_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 100,
            need_bfrom = 0,
            need_bto = 100;

    $retir_assumed_inflation.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
     $retir_post_retirement_return = $("#retir_post_retirement_return"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_post_retirement_return_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 100,
            need_bfrom = 0,
            need_bto = 100;

    $retir_post_retirement_return.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
  
    $retir_existing_investment_linked = $("#retir_existing_investment_linked"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_existing_investment_linked_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 1000000000,
            need_bfrom = 0,
            need_bto = 1000000000;

    $retir_existing_investment_linked.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
    $retir_return_on_existing_investment = $("#retir_return_on_existing_investment"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_return_on_existing_investment_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 100,
            need_bfrom = 0,
            need_bto = 100;

    $retir_return_on_existing_investment.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
    $retir_pre_retirement_return = $("#retir_pre_retirement_return"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_pre_retirement_return_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 100,
            need_bfrom = 0,
            need_bto = 100;

    $retir_pre_retirement_return.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });
    $retir_planned_lumspum_investment = $("#retir_planned_lumspum_investment"),
            //$from = $(".js-from"),
            $need_bto = $("#retir_planned_lumspum_investment_value"),
            need_balance,
            need_bmin = 1,
            need_bmax = 1000000000,
            need_bfrom = 0,
            need_bto = 1000000000;

    $retir_planned_lumspum_investment.ionRangeSlider({
        type: "single",
        min: need_bmin,
        max: need_bmax,
        prettify_enabled: false,
        grid: true,
        grid_num: 1,
        onChange: function (data) {
            //from = data.from;
            need_bto = data.from;

            need_updatebalanceValue();
        }
    });


    need_age = $need_age.data("ionRangeSlider");
    need_income = $need_income.data("ionRangeSlider");
    need_balance = $need_balance.data("ionRangeSlider");

    $need_ato.on("change", function () {
        need_ato = +$(this).val().replace(/,/g , "");
        if (need_ato > need_amax) {
            need_ato = need_amax;
        }
        need_updateAgeValue();
        need_updateAgeRange();
    });

    $need_ito.on("change", function () {
        need_ito = +$(this).val();
        if (need_ito > need_imax) {
            need_ito = need_imax;
        }
        need_updateincomeValue();
        need_updateincomeRange();
    });

    $need_bto.on("change", function () {
        need_bto = +$(this).val();
        if (need_bto > need_bmax) {
            need_bto = need_bmax;
        }
        need_updatebalanceValue();
        need_updatebalanceRange();
    });

});

var need_updateAgeValue = function () {
    $need_ato.val(delimitNumbersValue(need_ato));
    //$ato.val(ato);
};

var need_updateAgeRange = function () {
    need_age.update({
        from: need_ato
                //to: ato
    });
};

var need_updateincomeValue = function () {
    $need_ito.val(need_ito);
    //$ato.val(ato);
};

var need_updateincomeRange = function () {
    need_income.update({
        from: need_ito
                //to: ato
    });
};

var need_updatebalanceValue = function () {
    $need_bto.val(need_bto);
};

var need_updatebalanceRange = function () {
    need_balance.update({
        from: need_bto
    });
};

 function fv( $rate,  $periods,  $payment,  $present_value,  $beginning )
    {
        $when = $beginning ? 1 : 0;
    
        if ($rate == 0) {
            $fv = -($present_value + ($payment * $periods));
            return $fv;
        }
    
        $initial = 1 + ($rate * $when);
        $compound = Math.pow(1 + $rate, $periods);
        $fv = -(($present_value * $compound) + (($payment * $initial * ($compound - 1)) / $rate));
    
        return $fv;
    }




/*    function PV($R,$n,$pmt,$m=1) {

$Z = 1 / (1 + ($R/$m));

return ($pmt * $Z * (1 - Math.pow($Z,$n)))/(1 - $Z);

}*/

function PV(rate, periods, payment, future, type) {
  // Initialize type
  var type = (typeof type === 'undefined') ? 0 : type;

  // Evaluate rate and periods (TODO: replace with secure expression evaluator)
  rate = eval(rate);
  periods = eval(periods);

  // Return present value
  if (rate === 0) {
    return - payment * periods - future;
  } else {
    return (((1 - Math.pow(1 + rate, periods)) / rate) * payment * (1 +rate * type) - future) / Math.pow(1 + rate, periods);
  }
}

function PMT(ir, np, pv, fv, type) {
    /*
     * ir   - interest rate per month
     * np   - number of periods (months)
     * pv   - present value
     * fv   - future value
     * type - when the payments are due:
     *        0: end of the period, e.g. end of month (default)
     *        1: beginning of period
     */
    var pmt, pvif;

    fv || (fv = 0);
    type || (type = 0);

    if (ir === 0)
        return -(pv + fv)/np;

    pvif = Math.pow(1 + ir, np);
    pmt = - ir * (pv * pvif + fv) / (pvif - 1);

    if (type === 1)
        pmt /= (1 + ir);

    return pmt;
}