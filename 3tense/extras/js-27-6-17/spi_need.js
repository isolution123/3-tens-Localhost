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
    $('#calNeed').click(function () {
        $('#SIPneed').modal('show');
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
        
    $("#btn_calcSIPNeed").click(function () {

        var txtCroreAmount = 0.0;
        var txtCroreInfRate = 0.0;
        var txtCroreMonthInfRate = 0.0;
        var txtCroreYears = 0;
        var txtCroreMonths = 0;
        var resultVar = 0.0;
        var result = 0.0;
        var resultVarMonth = 0.0;
        var resultMonth = 0.0;
        txtCroreAmount = parseFloat($("#needSIPValue").val().replace(/,/g , ""));
        txtCroreInfRate = parseFloat($("#needrateOfInterest").val()); // for years
        txtCroreMonthInfRate = parseFloat($("#needrateOfInterest").val()); // for months
        
        txtCroreMonthInfRate = (txtCroreMonthInfRate / 1200);       // inflation rate for months
       
        txtCroreMonths = parseFloat($("#needinstallment").val().replace(/,/g , ""));  //total no of months 
        
        resultMonth=need_pmt(txtCroreMonthInfRate,$("#needinstallment").val().replace(/,/g , ""),0,txtCroreAmount,1);       
        
        var investment = (resultMonth * txtCroreMonths);

        var str1 = "<table class='table table-bordered' id='TblFutureVal'><thead>\n\
                            <tr><th colspan='6' class='center'>Expected Maturity Value of Your Investments</th></tr>\n\
                        \n\<tr> <th rowspan='2' class='center subheader' style='width:150px;'> Investment Period <br> (in Months)</th><th colspan='6' class='center subheader'> Expected Returns</th>\n\
                            <tr >\n\
                                <th class='center subheader' style='width:15px;'>8.00%  <input type='hidden' value='" + txtCroreAmount + "' id='sip_value'></th>\n\
                                <th class='center subheader' style='width:15px;'>10.00% </th>\n\
                                <th class='center subheader' style='width:15px;'>12.00% </th>\n\
                                <th class='center subheader' style='width:15px;'>15.00% </th>\n\
                                 \n\<th class='center subheader' style='width:15px;'><input  name='YourSIPInterest' id='YourSIPInterest' value='' class='cal_textboxsmall'  style='color: black;width: 90px;text-align: center;' type='text'> </th>\n\
                            </tr></thead><tbody>";
        var result_val8;
        var result_val10;
        var result_val12;
        var result_val15;
        var result_val20=0;
        var result_year;
        var new_SIPInterest_value =0;
        
        for (var i = 0; i < 7; i++) {
            if (i == 0) {
                result_year = '<input  name="YourSIPDuration" id="YourSIPDuration" value=""  maxlength="3" onblur="return calculate_table_val1();" class="cal_textboxsmall" style="color: black;width: 90px;text-align: center;" type="text">';
            } else if (i == 1) {
                result_year = 60;
            } else if (i == 2) {
                result_year = 120;
            } else if (i == 3) {
                result_year = 180;
            } else if (i == 4) {
                result_year = 240;
            } else if (i == 5) {
                result_year = 300;
            } else if (i == 6) {
                result_year = 360;
            }

            //  var needMonths = parseFloat(result_year)*12;
            var needMonths = parseFloat(result_year);
            if (i == 0) {
                result_val8 = '<span id="col1"> </span>';
                result_val10 = '<span id="col2"> </span>';
                result_val12 = '<span id="col3"> </span>';
                result_val15 = '<span id="col4"> </span>';
                result_val20 = '<span id="col5"> </span>';
            } else {
                 new_SIPInterest_value = $('#YourSIPInterest').val();

                result_val8 = calculate_table_val(txtCroreAmount, result_year, 8);
                result_val10 = calculate_table_val(txtCroreAmount, result_year, 10);
                result_val12 = calculate_table_val(txtCroreAmount, result_year, 12);
                result_val15 = calculate_table_val(txtCroreAmount, result_year, 15);
                if(new_SIPInterest_value >0){
                     result_val20 = calculate_table_val(txtCroreAmount, result_year, new_SIPInterest_value);
                }
            }
            var year_val= (i != 0)? result_year/12: '';
            if(i != 0){
                var yer_val= "("+year_val+" Yrs)";
            }else{
                 var yer_val='';
            }
            str1 += "<tr class='remv' >\n\
                        <td  width='55px' align='center'>"  + delimitNumbersValue(result_year) +yer_val + "</td>\n\
                        <td id='custom_1' align='center'>" + delimitNumbersValue(result_val8) + "</td> \n\
                        <td id='custom_2' align='center'>" + delimitNumbersValue(result_val10) + "</td>\n\
                        <td id='custom_3' align='center'>" + delimitNumbersValue(result_val12) + "</td>\n\
                        <td id='custom_4' align='center'>" + delimitNumbersValue(result_val15) + "</td>\n\
                        \n\  <td id='custom_5" + i + "' align='center'>" + delimitNumbersValue(result_val20) + "</td>\n\
                    </tr>";
        }

        str1 += "</tbody></table>";

        needSIPReturn_charts(investment, txtCroreAmount - investment);

        $('#needresult').css('display', 'block');
        $('#needtargetValue').text(delimitNumbersValue(Math.round(txtCroreAmount)));
        $('#needSIPValueYear').text(delimitNumbersValue(Math.round(txtCroreMonths)));
        $('.needFV').text(delimitNumbersValue(Math.round(resultMonth)));
        $('.needprinciple').text(delimitNumbersValue(Math.round(txtCroreMonths)));
        $('.needinterest').text(delimitNumbersValue(Math.round(investment)));
        $('#showTable').find('table').remove();
        $('#showTable').append(str1);
       
    });

    $need_age = $("#need_ageslider"),
            //$from = $(".js-from"),
            $need_ato = $("#needSIPValue"),
            need_age,
            need_amin = 1,
            need_amax = 1000000000,
            need_afrom = 1,
            need_ato = 1000000000000;

    $need_age.ionRangeSlider({
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

    $need_income = $("#need_income"),
            //$from = $(".js-from"),
            $need_ito = $("#needrateOfInterest"),
            need_income,
            need_imin = 1,
            need_imax = 20,
            need_ifrom = 1,
            need_ito = 20;

    $need_income.ionRangeSlider({
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

    $need_balance = $("#need_balance"),
            //$from = $(".js-from"),
            $need_bto = $("#needinstallment"),
            need_balance,
            need_bmin = 1,
            need_bmax = 360,
            need_bfrom = 0,
            need_bto = 360;

    $need_balance.ionRangeSlider({
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
        need_ito = +$(this).val().replace(/,/g , "");
        if (need_ito > need_imax) {
            need_ito = need_imax;
        }
        need_updateincomeValue();
        need_updateincomeRange();
    });

    $need_bto.on("change", function () {
        need_bto = +$(this).val().replace(/,/g , "");
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
    $need_ito.val(delimitNumbersValue(need_ito));
    //$ato.val(ato);
};

var need_updateincomeRange = function () {
    need_income.update({
        from: need_ito
                //to: ato
    });
};

var need_updatebalanceValue = function () {
    $need_bto.val(delimitNumbersValue(need_bto));
};

var need_updatebalanceRange = function () {
    need_balance.update({
        from: need_bto
    });
};



function needSIPReturn_charts(val1, val2)
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
    var myPieChart = new Chart(document.getElementById("need_mf-chart"), {
        type: 'pie',
        data: propData,
        options: options,
        series: {
            pie: {
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 3 / 4,
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


function calculate_table_val(SIPValue, installment, rateOfInterest) {
    
    var  txtCroreMonthInfRate_val = (rateOfInterest / 1200);       // inflation rate for months               
  
    var txtCroreMonths_val = parseFloat(installment);
       
    var  resultMonth_val=need_pmt(txtCroreMonthInfRate_val,txtCroreMonths_val,0,SIPValue,1);
    
    return resultMonth_val;
}


function calculate_table_val1() {
    if ($('#YourSIPInterest').val() != '') {
        var txtCroreAmount_calNeedVal=0;
        var installmentNeed=0;
        var new_SIPInterest_value=0;
       var resultMonth5 =0;
 
         installmentNeed = $('#YourSIPDuration').val();
         txtCroreAmount_calNeedVal = $('#sip_value').val();
         new_SIPInterest_value = ($('#YourSIPInterest').val()=='')? 0 : $('#YourSIPInterest').val();

        var txtCroreMonthInfRate1 = (8/1200);       // inflation rate for months    
        var txtCroreMonthInfRate2 = (10/1200);
        var txtCroreMonthInfRate3 = (12/1200);
        var txtCroreMonthInfRate4 = (15/1200);
        var  txtCroreMonthInfRate5 = (new_SIPInterest_value / 1200);
       
        //var txtCroreMonths = parseFloat(installment)*12;  //total no of months 
        var txtCroreMonths_installment = parseFloat(installmentNeed);
      
 
    
        var resultMonth1 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal),parseFloat(installmentNeed),8);
        var resultMonth2 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal),parseFloat(installmentNeed),10);
        var resultMonth3 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal),parseFloat(installmentNeed),12);
        var resultMonth4 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal),parseFloat(installmentNeed),15);
        if(new_SIPInterest_value!=''){
         resultMonth5 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal),parseFloat(installmentNeed),new_SIPInterest_value);
         }

        
        $('#col1').text(delimitNumbersValue(resultMonth1));
        $('#col2').text(delimitNumbersValue(resultMonth2));
        $('#col3').text(delimitNumbersValue(resultMonth3));
        $('#col4').text(delimitNumbersValue(resultMonth4));
        // $('#col5').text(resultMonth5.toFixed(0));
        //    alert(txtCroreMonths+"  "+new_SIPInterest_value);
        if(new_SIPInterest_value!=''){
            var col_val = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal), txtCroreMonths_installment, new_SIPInterest_value);
        }else{
             var col_val =0; 
        }
     //   var col_val = need_pmt(txtCroreMonthInfRate5,txtCroreMonths,0,SIPValue,1);
        $('#custom_50').text(delimitNumbersValue(col_val));

        var result_val8;
        var result_val10;
        var result_val12;
        var result_val15;
        var result_val20;
        var result_year;
        for (var i = 0; i < 7; i++) {
            if (i == 0) {
                result_year = $('#YourSIPDuration').val();
            } else if (i == 1) {
                result_year = 60;
            } else if (i == 2) {
                result_year = 120;
            } else if (i == 3) {
                result_year = 180;
            } else if (i == 4) {
                result_year = 240;
            } else if (i == 5) {
                result_year = 300;
            } else if (i == 6) {
                result_year = 360;
            }

            //  var needMonths = parseFloat(result_year)*12;
            var needMonths = parseFloat(result_year);
            // if(i!=0){
            var new_SIPInterest_value =($('#YourSIPInterest').val()=='')? 0 : $('#YourSIPInterest').val() ;
            //  alert(needMonths+"  "+new_SIPInterest_value);
            result_val20 = calculate_table_val(parseFloat(txtCroreAmount_calNeedVal), needMonths, parseFloat(new_SIPInterest_value));            
           
            // }          
             
            $('#custom_5' + i).text(delimitNumbersValue(result_val20));
        }

    } else {
        alert("Please enter expected interest rate");
    }
}
/*
    function delimitNumbersValue(x) {
     
        x=x.toString();
        var lastThree = x.substring(x.length-3);
        var otherNumbers = x.substring(0,x.length-3);
        if(otherNumbers != '')
            lastThree = ',' + lastThree;
        var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;

       // alert(res);
    return res;
    }
*/


      