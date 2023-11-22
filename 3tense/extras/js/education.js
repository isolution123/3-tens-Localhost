    
    
    function educationResetSlider(){
        $("#ec_age").val('');
        $("#s_ec_age").val('');
        $("#sch_year").val('');
        $("#sch_cost").val('');
        $("#sch_fut_cost").val('');
        $("#grd_year").val('');
        $("#grd_cost").val('');
        $("#grd_fut_cost").val('');
        $("#pgrd_year").val('');
        $("#pgrd_cost").val('');
        $("#pgrd_fut_cost").val('');
        $("#carr_year").val('');
        $("#carr_cost").val('');
        $("#carr_fut_cost").val('');
        $("#marg_year").val('');
        $("#marg_cost").val('');
        $("#marg_fut_cost").val('');
        $("#e_return").val('6');
        $("#e_inflation").val('6');
    }
    
    
    $(document).ready(function(){        
        
        $('#education_calc').click(function(){            
            $('#Education').modal('show');
            $(".edu_result").css('display','none');
            educationResetSlider();
        }); 
        
                
        $(document).on('change','#ec_age',function(){
           
            var date=$("#ec_age").val().split('-')[0];
            var mnth =$("#ec_age").val().split('-')[1];
            var yr =$("#ec_age").val().split('-')[2];
            dt=yr+'-'+mnth+'-'+date;
            var dob = new Date(dt); 
                   
            var today = new Date();
            var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
            
            if(isNaN(age) || age < 0){
                $('#s_ec_age').val('');
                return false;                
            }else{
                $('#s_ec_age').val(age);
            }
            

        });
        
        //$(document).on('blur','#sch_cost',function(){
        $('#sch_cost').on('blur',function(){
            
            var sch_year    = parseInt($("#sch_year").val());
            var sch_cost    = parseInt($("#sch_cost").val().replace(/,/g , ""));
            var cur_age     = parseInt($("#s_ec_age").val());
            //alert(sch_cost);
            var interest    = parseFloat($("#e_inflation").val());

            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#sch_cost').val('');
                return false;                
            }
            
            //New Formula
            var i;
            var new_value = 0;
            var total = 0;
            age = sch_year + 1;
            total = cur_age + sch_year;
            for(i = 1;i <= sch_year;i++){
                //console.log('--'+age+'--'+total+'--'+i+'--');
                var ans = fv(i,sch_cost,interest);
                //console.log(ans);
                new_value += ans;
            }
          
            //console.log(new_value);
            new_value+=sch_cost;
            
            if(isNaN(sch_cost)){
                $('#sch_cost').val('');
            }else{
                $('#sch_cost').val(addCommas2(sch_cost));
            }
            
            if(isNaN(new_value)){
                $('#sch_fut_cost').val('');
            }else{
                $('#sch_fut_cost').val(addCommas2(new_value));
            }           
            
        });
        
        $('#sch_year').on('blur',function(){
            
            var sch_year    = parseInt($("#sch_year").val());
            var sch_cost    = parseInt($("#sch_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
//              alert(sch_year);
            //New Formula
            var i;
            var new_value = 0;
            for(i = 1;i <= sch_year;i++){
                
                var ans = fv(i,sch_cost,interest);
                //console.log(ans);
                new_value += ans;
            }
          
            //console.log(new_value);
            new_value+=50000;
            
            if(isNaN(sch_year)){
                $('#sch_fut_cost').val('');
                return false;
            }
//            else{
//                $('#sch_fut_cost').val('');
//            }
            
            if(isNaN(new_value)){
                $('#sch_fut_cost').val('');
            }else{
                $('#sch_fut_cost').val(addCommas2(new_value));
            } 
            
        });
        
        $(document).on('blur','#grd_cost',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var grd_year    = parseInt($("#grd_year").val());
            var grd_cost    = parseInt($("#grd_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
            
            if(isNaN(grd_cost)){
                $('#grd_cost').val('');
            }else{
                $('#grd_cost').val(addCommas2(grd_cost));
            }
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#grd_cost').val('');
                return false;                
            }
            
            var year = grd_year - cur_age; 
            var ans = fv(year,grd_cost,interest);
           
            if(isNaN(ans)){
                $('#grd_fut_cost').val('');
            }else{
                $('#grd_fut_cost').val(addCommas2(ans));
            }
        });
        
        $('#grd_year').on('blur',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var grd_year    = parseInt($("#grd_year").val());
            var grd_cost    = parseInt($("#grd_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#grd_year').val('');
                return false;                
            }
            
            var year = grd_year - cur_age; 
            var ans = fv(year,grd_cost,interest);
            
            if(isNaN(ans)){
                $('#grd_fut_cost').val('');
            }else{
                $('#grd_fut_cost').val(addCommas2(ans));
            } 
        });
        
        $(document).on('blur','#pgrd_cost',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var pgrd_year   = parseInt($("#pgrd_year").val());            
            var pgrd_cost   = parseInt($("#pgrd_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
            
            if(isNaN(pgrd_cost)){
                $('#pgrd_cost').val('');
            }else{
                $('#pgrd_cost').val(addCommas2(pgrd_cost));
            }
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#pgrd_cost').val('');
                return false;                
            }
            
            var year = pgrd_year - cur_age;
            var ans = fv(year,pgrd_cost,interest);
           
            if(isNaN(ans)){
                $('#pgrd_fut_cost').val('');
            }else{
                $('#pgrd_fut_cost').val(addCommas2(ans));
            }
        });
        
        $('#pgrd_year').on('blur',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var pgrd_year   = parseInt($("#pgrd_year").val());
            var pgrd_cost   = parseInt($("#pgrd_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());

            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#pgrd_year').val('');
                return false;                
            }
            
            var year = pgrd_year - cur_age;
            var ans = fv(year,pgrd_cost,interest);
            
            if(isNaN(ans)){
                $('#pgrd_fut_cost').val('');
            }else{
                $('#pgrd_fut_cost').val(addCommas2(ans));
            } 
        });        
        
        $(document).on('blur','#carr_cost',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var carr_year   = parseInt($("#carr_year").val());
            var carr_cost   = parseInt($("#carr_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#carr_cost').val('');
                return false;                
            }
            
            if(isNaN(carr_cost)){
                $('#carr_cost').val('');
            }else{
                $('#carr_cost').val(addCommas2(carr_cost));
            }
            
            var year = carr_year - cur_age;
            var ans = fv(year,carr_cost,interest);
            
            if(isNaN(ans)){
                $('#carr_fut_cost').val('');
            }else{
                $('#carr_fut_cost').val(addCommas2(ans));
            }
            
        });
        
        $('#carr_year').on('blur',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var carr_year   = parseInt($("#carr_year").val());
            var carr_cost   = parseInt($("#carr_cost").val().replace(/,/g , ""));
            var interest    = parseFloat($("#e_inflation").val());
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#carr_year').val('');
                return false;                
            }
            
            var year = carr_year - cur_age;
            var ans = fv(year,carr_cost,interest);
            
            if(isNaN(ans)){
                $('#carr_fut_cost').val('');
            }else{
                $('#carr_fut_cost').val(addCommas2(ans));
            } 
        });
        
        $(document).on('blur','#marg_cost',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var marg_year   = parseInt($("#marg_year").val());
            var marg_cost   = parseInt($("#marg_cost").val().replace(/,/g , ""));
            var inflation   = parseFloat($("#e_inflation").val());
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#marg_cost').val('');
                return false;                
            }
            
            if(isNaN(marg_cost)){
                $('#marg_cost').val('');
            }else{
                $('#marg_cost').val(addCommas2(marg_cost));
            }
            
            var year = marg_year - cur_age;           
            var ans = fv(year,marg_cost,inflation);
            
            if(isNaN(ans)){
                $('#marg_fut_cost').val('');
            }else{                
                $('#marg_fut_cost').val(addCommas2(Math.round(ans)));
            }
        });
        
        $('#marg_year').on('blur',function(){
            
            var cur_age     = ($("#s_ec_age").val());
            var marg_year   = parseInt($("#marg_year").val());
            var marg_cost   = parseInt($("#marg_cost").val().replace(/,/g , ""));
            var inflation   = parseFloat($("#e_inflation").val());
            
            if(isNaN(cur_age)){
                alert('Current age should not be blank');
                $('#marg_year').val('');
                return false;                
            }
            
            var year = marg_year - cur_age;
            var ans = fv(year,marg_cost,inflation);
            
            if(isNaN(ans)){
                $('#marg_fut_cost').val('');
            }else{
                $('#marg_fut_cost').val(addCommas2(ans));
            } 
        });
            
        $("#edu_calc").click(function(){
            
            var cur_age         = ($("#s_ec_age").val());
            
            var sch_year        = parseInt($("#sch_year").val());
            var sch_cost        = parseInt($("#sch_cost").val());
            var sch_fut_cost    = parseInt($("#sch_fut_cost").val().replace(/,/g , ""));
            
            var grd_year        = parseInt($("#grd_year").val());
            var grd_cost        = parseInt($("#grd_cost").val());
            var grd_fut_cost    = parseInt($("#grd_fut_cost").val().replace(/,/g , ""));
            
            var pgrd_year       = parseInt($("#pgrd_year").val());
            var pgrd_cost       = parseInt($("#pgrd_cost").val());
            var pgrd_fut_cost   = parseInt($("#pgrd_fut_cost").val().replace(/,/g , ""));
            
            var carr_year       = parseInt($("#carr_year").val());
            var carr_cost       = parseInt($("#carr_cost").val());
            var carr_fut_cost   = parseInt($("#carr_fut_cost").val().replace(/,/g , ""));
            
            var marg_year       = parseInt($("#marg_year").val());
            var marg_cost       = parseInt($("#marg_cost").val());
            var marg_fut_cost   = parseInt($("#marg_fut_cost").val().replace(/,/g , ""));
           
            var inflation   = parseFloat($("#e_inflation").val());
            var interest    = parseFloat($("#e_return").val());
           
            if(cur_age == '' || isNaN(cur_age)){
                alert("Select the Age of Child");
                return false;
            }
            
            if(isNaN(sch_fut_cost)){
                var sch_fut_cost = 0;                
            }
            if(isNaN(grd_fut_cost)){
                var grd_fut_cost = 0;
            }
            if(isNaN(pgrd_fut_cost)){
                var pgrd_fut_cost = 0;
            }
            if(isNaN(carr_fut_cost)){
                var carr_fut_cost = 0;
            }
            if(isNaN(marg_fut_cost)){
                var marg_fut_cost = 0;
            }
            //alert(marg_year);
            var edu_future = sch_fut_cost + grd_fut_cost + pgrd_fut_cost + carr_fut_cost + marg_fut_cost;
            
            var start_year = 0;
            
            if(isNaN(marg_year)){                
                if(!isNaN(carr_year)){
                    start_year = carr_year;
                }else{
                    if(!isNaN(pgrd_year)){
                        start_year = pgrd_year;
                    }else{
                        if(!isNaN(grd_year)){
                            start_year = grd_year;
                        }else{
                            if(!isNaN(sch_year)){
                                start_year = sch_year;
                            }
                        }
                    }
                }
            }else{
                start_year = marg_year;
            }
            
            var years = start_year - cur_age;
            years = Math.abs(years);
            
            var rate_per_period = interest/12/100;
            var number_of_payments = years * 12;
            var month = vacation_calc(rate_per_period,number_of_payments,0,edu_future,1);
            //var value=Math.pow((1+(inflation/100)),years);
            if(isNaN(month)){
                month = 0;
            }
            //Lumpsum Value
            var educationLumpsum=Math.round((edu_future/(Math.pow(1+(interest/100),years))));
            
            if(isNaN(educationLumpsum)){
                educationLumpsum = 0;
            }
            
            var hlv_amt = month * years * 12;
            // console.log(hlv_amt);
            if(isNaN(hlv_amt) || hlv_amt == ''){
                hlv_amt = 0;
            }
                    
            $('#edmonthly_amount').text(addCommas2(month));
            //$('#edyearly_amount').html(op*12);
            $('#edyearly_amount').html(addCommas2(educationLumpsum));
            $('#edfuture_amount').html(addCommas2(edu_future));
            $('#edhlv_amount').html(addCommas2(hlv_amt));
            
            $('#hidden_edmonthly_amount').val(addCommas2(month));
            $('#hidden_edyearly_amount').val(addCommas2(educationLumpsum));
            $('#hidden_edfuture_amount').val(addCommas2(edu_future));
            $('#hidden_edhlv_amount').val(addCommas2(hlv_amt));
            
            $(".edu_result").css('display','block');
			
			
			 //code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'education'},
                        success:function(data){
                          //alert(data);
                        }
                    });
             
             //end here 
			
			
            
        });
        
        
    });  //  Jquery Ends
    
    
    function fv(year,cost,interest){
        //FV=PMT[((1+i)^n-1)/i]
        
        rate = interest/100;
        number=Math.pow((1+rate),year);
        //number = (Math.pow(1 + rate,year) - 1) / rate;
        FV = cost * number;
        
        return Math.round(FV);
        
//            rate = interest/100;
//            number = (Math.pow(1 + rate,sch_year) - 1) / rate;
//            FV = sch_cost * number;
//        
//            ans = Math.round(FV);
    }
    
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
    
    
    
    //----------- education cost calculation --------------//
    
  /*  function edu_calc(cur_age,fut_age,cost_opt,edu_cost,approx_cost,course,saved_amt,inflation,interest){
        
//        console.log(cur_age);
//        console.log(fut_age);
//        console.log(cost_opt);
//        console.log(approx_cost);
//        console.log(edu_cost);
//        console.log(saved_amt);
        
        
        var years = Math.abs(fut_age - cur_age);
        if (cost_opt == 1) { // if cost-estimated-yes selected

          // Education cost after (child education start age - child age) years
          futureEducationCost = edu_cost * Math.pow((1 + (inflation / 100)), (years));

          // Future value of your savings
          futureSavingValue = saved_amt * Math.pow((1 + (interest / 100)), (years));

            // Future amount required at the time of your child's education
          futureAmountForEducation = futureEducationCost - interest;

          // Amount you should save monthly
          amountToSave = (futureAmountForEducation * (Math.pow(((100 + interest) / 100), (1 / 12)) - 1) / (Math.pow(((100 + (Math.pow(((100 + interest) / 100), (1 / 12)) - 1) * 100) / 100), ((years) * 12)) - 1));

        } else { // if cost-estimated-no selected

          // Cost of education
          //childEducationCost2 = getEducationCost(course, 1000000);
          
          // Education cost after (E7-D4) years
          futureEducationCost = approx_cost * Math.pow((1 + (inflation / 100)), (years));

          // Future value of your savings
          futureSavingValue = saved_amt * Math.pow((1 + (interest / 100)), (years));

          // Future amount required at the time of your child's education
          futureAmountForEducation = futureEducationCost - futureSavingValue;

          // Amount you should save monthly
          amountToSave = (futureAmountForEducation * (Math.pow(((100 + interest) / 100), (1 / 12)) - 1) / (Math.pow(((100 + (Math.pow(((100 + interest) / 100), (1 / 12)) - 1) * 100) / 100), ((years) * 12)) - 1));
        }

        //console.log("amountToSave", amountToSave);

        if (amountToSave > 0) {
          amountToSave = amountToSave.toFixed(0);

        } else {
          amountToSave = 0;
        }
        return amountToSave;
    }*/