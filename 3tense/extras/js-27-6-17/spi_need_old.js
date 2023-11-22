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



    $(document).ready(function(){
        //alert(base_url);
        $('#calNeed').click(function(){          
          $('#SIPneed').modal('show');
        });
         $("#btn_calcSIPNeed").click(function(){       	
   	
                var txtCroreAmount = 0.0;
                var txtCroreInfRate = 0.0;     
                var txtCroreMonthInfRate = 0.0;     
                var txtCroreYears = 0;
                var txtCroreMonths = 0;
                var resultVar=0.0;
                var result = 0.0;
                var resultVarMonth=0.0;
                var resultMonth = 0.0;
                txtCroreAmount = parseFloat($("#needSIPValue").val());
                txtCroreInfRate =  parseFloat($("#needrateOfInterest").val()); // for years
                txtCroreMonthInfRate = parseFloat($("#needrateOfInterest").val()); // for months
                if(isNaN(txtCroreInfRate))
                {
                   txtCroreInfRate = 0;  
                   txtCroreMonthInfRate = 0;      
                }  
                else if(parseFloat( $("#needrateOfInterest").val()) >= 100.00 || parseFloat( $("#needrateOfInterest").val()) <= 0.0)
                {
                       alert("Rate of Inflation should be between 1 to 99");
                       $("#rateOfInterest").val();                        
                       return false;
                }        
                txtCroreInfRate = (txtCroreInfRate /100);                  // inflation rate for years
                txtCroreMonthInfRate = (txtCroreMonthInfRate/1200);       // inflation rate for months
                txtCroreYears = parseFloat($("#needinstallment").val());      // total no of years  
              // txtCroreMonths = parseFloat($("#installment").val())*12;  //total no of months 
                txtCroreMonths = parseFloat($("#needinstallment").val());  //total no of months 
                if(isNaN(txtCroreYears))
                {
                   txtCroreYears = 1;        
                } 
                if(isNaN(txtCroreMonths))
                {
                   txtCroreMonths = 1;        
                }        
                resultVar = ((Math.pow(1 + txtCroreInfRate,txtCroreYears)) -1)/txtCroreInfRate;        //calculation for years
                resultVarMonth = ((Math.pow(1 + txtCroreMonthInfRate,txtCroreMonths))-1)/txtCroreMonthInfRate;  //calculation for months
                result = (txtCroreAmount/resultVar)                //for years
                resultMonth = (txtCroreAmount/resultVarMonth)      //for months
                if(result > 0)
                {
                     result = result.toFixed(0);  
                }    
                else 
                {
                   result = 0;
                }
                 if(resultMonth > 0)
                {
                     resultMonth = resultMonth.toFixed(0);   
                }    
                else 
                {
                   resultMonth = 0;
                }            
            var investment = (resultMonth * txtCroreMonths).toFixed(2);
           
            var str1 = "<table class='table table-bordered' id='TblFutureVal'><thead>\n\
                            <tr><th colspan='6' class='center'>Expected Maturity Value of Your Investments</th></tr>\n\
                        \n\<tr> <th rowspan='2' class='center subheader' style='width:150px;'>Expected<br> Returns</th><th colspan='6' class='center subheader'>Investment Period (in Months)</th>\n\
                            <tr >\n\
                                <th class='center subheader' style='width:15px;'>8.00%  <input type='hidden' value='"+ txtCroreAmount +"' id='sip_value'></th>\n\
                                <th class='center subheader' style='width:15px;'>10.00% </th>\n\
                                <th class='center subheader' style='width:15px;'>12.00% </th>\n\
                                <th class='center subheader' style='width:15px;'>15.00% </th>\n\
                                 \n\<th class='center subheader' style='width:15px;'><input  name='YourSIPInterest' id='YourSIPInterest' value='' class='cal_textboxsmall'  style='color: black;width: 90px;text-align: center;' type='text'> </th>\n\
                            </tr></thead><tbody>";
            var result_val8;
            var result_val10; 
            var result_val12;
            var result_val15;
            var result_val20;
            var result_year;
             for(var i=0;i<7;i++){
                 if(i==0){
                     result_year='<input  name="YourSIPDuration" id="YourSIPDuration" value=""  maxlength="3" onblur="return calculate_table_val1();" class="cal_textboxsmall" style="color: black;width: 90px;text-align: center;" type="text">';                   
                 }else if(i==1){
                     result_year=60;  
                 }else if(i==2){
                     result_year=120;
                 }else if(i==3){
                     result_year=180;
                 }else if(i==4){
                     result_year=240;
                 }else if(i==5){
                      result_year=300;
                 }else if(i==6){
                     result_year=360;
                 }
                 
                //  var needMonths = parseFloat(result_year)*12;
                var needMonths = parseFloat(result_year);
                if(i==0){  
                    result_val8='<span id="col1"> </span>';
                    result_val10='<span id="col2"> </span>';
                    result_val12='<span id="col3"> </span>';
                    result_val15='<span id="col4"> </span>';
                     result_val20='<span id="col5"> </span>';
                }else{
                    var new_SIPInterest_value = $('#YourSIPInterest').val();
                    
                     result_val8= calculate_table_val(txtCroreAmount,result_year,8);
                     result_val10=calculate_table_val(txtCroreAmount,result_year,10);
                     result_val12=calculate_table_val(txtCroreAmount,result_year,12);
                     result_val15=calculate_table_val(txtCroreAmount,result_year,15);
                     result_val20=calculate_table_val(txtCroreAmount,result_year,new_SIPInterest_value);
                }
                 str1 += "<tr class='remv' >\n\
                        <td  width='55px' align='center'>"+ result_year +"</td>\n\
                        <td id='custom_1' align='center'>"+  result_val8 +"</td> \n\
                        <td id='custom_2' align='center'>"+ result_val10  +"</td>\n\
                        <td id='custom_3' align='center'>"+ result_val12  +"</td>\n\
                        <td id='custom_4' align='center'>"+ result_val15  +"</td>\n\
                        \n\  <td id='custom_5"+i+"' align='center'>"+ result_val20  +"</td>\n\
                    </tr>";
             }
                
                str1 += "</tbody></table>";         
           
            needSIPReturn_charts(investment,txtCroreAmount-investment);
            
            $('#needresult').css('display','block');
            $('#needtargetValue').text(txtCroreAmount);  
            $('#needSIPValueYear').text(txtCroreYears);
            $('.needFV').text(resultMonth);
            $('.needprinciple').text(txtCroreMonths);
            $('.needinterest').text(investment);
           $('#showTable').find('table').remove(); 
            $('#showTable').append(str1);
           
            
        });
        
        $need_age = $("#need_ageslider"),
                //$from = $(".js-from"),
                $need_ato = $("#needSIPValue"),
                need_age,
                need_amin = 1,
                need_amax = 1000000, 
                need_afrom=1,
                need_ato=1000000;

        $need_age.ionRangeSlider({
                type: "single",
                min: need_amin,
                max: need_amax,
                prettify_enabled: false,
                grid: true,
                grid_num: 10,
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
                 need_ifrom=1,
                 need_ito=20;

        $need_income.ionRangeSlider({
             type: "single",
                min: need_imin,
                max: need_imax,
                prettify_enabled: false,
                grid: true,
                grid_num: 10,
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
                 need_bfrom=0,
                 need_bto=360;

        $need_balance.ionRangeSlider({
             type: "single",
                min: need_bmin,
                max: need_bmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 40,
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
            need_ato = +$(this).val();
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
        $need_ato.val(need_ato);
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
    
    
    
   function needSIPReturn_charts(val1,val2)
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
        var myPieChart = new Chart(document.getElementById("need_mf-chart"),{
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
     
     
     function calculate_table_val(SIPValue,installment,rateOfInterest){
                var txtCroreAmount = 0.0;
                var txtCroreInfRate = 0.0;     
                var txtCroreMonthInfRate = 0.0;     
                var txtCroreYears = 0;
                var txtCroreMonths = 0;
                var resultVar=0.0;
                var result = 0.0;
                var resultVarMonth=0.0;
                var resultMonth = 0.0;
                txtCroreAmount =SIPValue;              
                txtCroreMonthInfRate = parseFloat(rateOfInterest); // for months
                      
               
                txtCroreMonthInfRate = (txtCroreMonthInfRate/1200);       // inflation rate for months               
                //txtCroreMonths = parseFloat(installment)*12;  //total no of months 
                txtCroreMonths = parseFloat(installment);
                if(isNaN(txtCroreYears))
                {
                   txtCroreYears = 1;        
                } 
                if(isNaN(txtCroreMonths))
                {
                   txtCroreMonths = 1;        
                }        
           
                resultVarMonth = ((Math.pow(1 + txtCroreMonthInfRate,txtCroreMonths))-1)/txtCroreMonthInfRate;  //calculation for months
        //  alert(txtCroreMonthInfRate+"-----"+resultVarMonth);
                resultMonth = (txtCroreAmount/resultVarMonth)      //for months
                
                 if(resultMonth > 0)
                {
                     resultMonth = resultMonth.toFixed(0);   
                }    
                else 
                {
                   resultMonth = 0;
                }   
                return resultMonth;
            }
            
            
            function calculate_table_val1(){
                if($('#YourSIPInterest').val()!=''){                    
               
                    var installment=$('#YourSIPDuration').val();
                    var SIPValue=$('#sip_value').val();
                    var new_SIPInterest_value=$('#YourSIPInterest').val();

                    var txtCroreAmount =SIPValue;              
                    var txtCroreMonthInfRate1 = parseFloat(8); // for months
                    var txtCroreMonthInfRate2 = parseFloat(10);
                    var txtCroreMonthInfRate3 = parseFloat(12);
                    var txtCroreMonthInfRate4 = parseFloat(15);
                    var txtCroreMonthInfRate5 = parseFloat(new_SIPInterest_value);

                    var txtCroreMonthInfRate1 = (txtCroreMonthInfRate1/1200);       // inflation rate for months    
                    var txtCroreMonthInfRate2 = (txtCroreMonthInfRate2/1200);
                    var txtCroreMonthInfRate3 = (txtCroreMonthInfRate3/1200);
                    var txtCroreMonthInfRate4 = (txtCroreMonthInfRate4/1200);
                    var txtCroreMonthInfRate5 = (txtCroreMonthInfRate5/1200);
                  // var txtCroreMonths = parseFloat(installment)*12;  //total no of months 
                    var txtCroreMonths = parseFloat(installment);

                    if(isNaN(txtCroreMonths))
                    {
                       txtCroreMonths = 1;        
                    }        

                    var resultVarMonth1 = ((Math.pow(1 + txtCroreMonthInfRate1,txtCroreMonths))-1)/txtCroreMonthInfRate1; 
                    var resultVarMonth2 = ((Math.pow(1 + txtCroreMonthInfRate2,txtCroreMonths))-1)/txtCroreMonthInfRate2;
                    var resultVarMonth3 = ((Math.pow(1 + txtCroreMonthInfRate3,txtCroreMonths))-1)/txtCroreMonthInfRate3;
                    var resultVarMonth4 = ((Math.pow(1 + txtCroreMonthInfRate4,txtCroreMonths))-1)/txtCroreMonthInfRate4;//calculation for months
                    var resultVarMonth5 = ((Math.pow(1 + txtCroreMonthInfRate5,txtCroreMonths))-1)/txtCroreMonthInfRate5; 

                    var resultMonth1 = (txtCroreAmount/resultVarMonth1);     //for months
                    var resultMonth2 = (txtCroreAmount/resultVarMonth2);
                    var resultMonth3 = (txtCroreAmount/resultVarMonth3);
                    var resultMonth4 = (txtCroreAmount/resultVarMonth4);
                    var resultMonth5 = (txtCroreAmount/resultVarMonth5);

                        $('#col1').text(resultMonth1.toFixed(0));
                        $('#col2').text(resultMonth2.toFixed(0));
                        $('#col3').text(resultMonth3.toFixed(0));
                        $('#col4').text(resultMonth4.toFixed(0));
                       // $('#col5').text(resultMonth5.toFixed(0));
                     //    alert(txtCroreMonths+"  "+new_SIPInterest_value);
                        var col_val= calculate_table_val(SIPValue,txtCroreMonths,new_SIPInterest_value);

                        $('#custom_50').text(col_val);

                         var result_val8;
                        var result_val10; 
                        var result_val12;
                        var result_val15;
                        var result_val20;
                        var result_year;
                 for(var i=0;i<7;i++){
                      if(i==0){
                         result_year=$('#YourSIPDuration').val();  
                     }else if(i==1){
                         result_year=60;  
                     }else if(i==2){
                         result_year=120;
                     }else if(i==3){
                         result_year=180;
                     }else if(i==4){
                         result_year=240;
                     }else if(i==5){
                          result_year=300;
                     }else if(i==6){
                         result_year=360;
                     }

                    //  var needMonths = parseFloat(result_year)*12;
                    var needMonths = parseFloat(result_year);
                   // if(i!=0){
                        var new_SIPInterest_value = $('#YourSIPInterest').val();         
                      //  alert(needMonths+"  "+new_SIPInterest_value);
                         result_val20=calculate_table_val(SIPValue,needMonths,new_SIPInterest_value);
                   // }                
                        $('#custom_5'+i).text(result_val20);
                 }

             
              }else{
                    alert("Please enter expected interest rate");
                }
             
                  
            }
            
            
            
      