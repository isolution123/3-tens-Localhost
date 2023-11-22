    var $ha_age,
        $hafrom,
        $hlv_ato,
        hage,
        hamin,
        hamax,
        hafrom,
        hato;

    var $hlincome,
        $hlifrom,
        $hlito,
        hlincome,
        hlimin,
        hlimax,
        hlifrom,
        hlito;

    var $hbalance,
        $hbfrom,
        $hbto,
        hbalance,
        hbmin,
        hbmax,
        hbfrom,
        hbto;

    var $hexpense,
        $hefrom,
        $heto,
        hexpense,
        hemin,
        hemax,
        hefrom,
        heto;

    var $hsavings,
        $hsfrom,
        $hsto,
        hsavings,
        hsmin,
        hsmax,
        hsfrom,
        hsto;

    var $hcover,
        $hcfrom,
        $hcto,
        hcover,
        hcmin,
        hcmax,
        hcfrom,
        hcto;
    
    var $hret_age,
        $hrafrom,
        $hrato,
        hret_age,
        hramin,
        hramax,
        hrafrom,
        hrato;

    function HLVresetSlider()
    {
        $("#hlv_age").data("ionRangeSlider").update({
         from: 0
        });
        $("#hlvs_age").val("0");
        
        $("#hret_age").data("ionRangeSlider").update({
         from: 18
        });
        $("#s_hret_age").val("18");

        //expected returns
        $("#hincome").data("ionRangeSlider").update({
           from: 100000
        });
        $("#hs_income").val("100000");

        //Outstanding Balance
        $("#hbalance").data("ionRangeSlider").update({
           from: 0
        });
        $("#hs_balance").val("0");
        
        //Montlhy Expense
        $("#expense").data("ionRangeSlider").update({
           from: 0
        });
        $("#s_expense").val("0");
        
        //Total Savings
        $("#overall_savings").data("ionRangeSlider").update({
           from: 0
        });
        $("#s_overall_savings").val("0");
        
        //Insurance Cover
        $("#existinInsuranceCover").data("ionRangeSlider").update({
           from: 0
        });
        $("#s_existinInsuranceCover").val("0");
        
        $(".hlv_result").css('display','none');

    }

    $(document).ready(function(){
        
        
        $('#HLVCalc').click(function(){            
            $('#HLV-Calc').modal('show');
            HLVresetSlider();
        });
        
        $("#hlv_calc").click(function(){
            
            var age     = parseInt($("#hlv_age").val());
            var ret_age = parseInt($("#hret_age").val());
            var income  = parseInt($("#hincome").val().replace(/,/g , ""));
            var balance = parseInt($("#hbalance").val().replace(/,/g , ""));
            var expense = parseInt($("#expense").val().replace(/,/g , ""));
            var savings = parseInt($("#overall_savings").val().replace(/,/g , ""));
            var cover   = parseInt($("#existinInsuranceCover").val().replace(/,/g , ""));
            
            if(ret_age < age){
               alert('Retirement Age should not be less than Your Age');
               return false;
            }
            
            if(age == 0 || balance == 0 || expense == 0){
               alert('Select values for each fields');
               return false; 
            }
            var years = Math.abs(ret_age - age);
            
            var output = hlv_calculator(age,ret_age,income,balance,expense,savings,cover);
            
            if(output < 0){
                output = Math.abs(output);                
            }
            var extra = output - cover - savings;
            
            if(extra < 0){
                extra = 0;
            }
            $("#years").text(years+' Yrs');                    
            $("#hlv_value").text(addCommas2(output));
            $("#extraincome").text(addCommas2(extra));
            $(".hlv_result").css('display','block');
            $("#hidden_yearsHLV").val(years+' Yrs');  
            $("#hidden_hlv_valueHLV").val(addCommas2(output));
            $("#hidden_extraincomeHLV").val(addCommas2(extra));
			
			 //code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'hlv'},
                        success:function(data){
                         // alert(data);
                        }
                    });
             
             //end here 
			
			
			
			
        });
        
        $ha_age = $("#hlv_age"),
                //$from = $(".js-from"),
                $hlv_ato = $("#hlvs_age"),
                hage,
                hamin = 0,
                hamax = 60, 
                hafrom=0,
                hato=60;

        $ha_age.ionRangeSlider({
                type: "single",
                min: hamin,
                max: hamax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hato = data.from;                   
                    updatehAgeValue();
                }
        });
        
        $hret_age = $("#hret_age"),
                //$from = $(".js-from"),
                $hrato = $("#s_hret_age"),
                hret_age,
                hramin = 18,
                hramax = 60, 
                hrafrom=18,
                hrato=60;
        
        

        $hret_age.ionRangeSlider({
                type: "single",
                min: hramin,
                max: hramax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hrato = data.from;
                    
                    updatehretAgeValue();
                }
        });
        
        $hlincome = $("#hincome"),
                //$from = $(".js-from"),
                 $hlito = $("#hs_income"),
                 hlincome,
                 hlimin = 100000,
                 hlimax = 1000000000, 
                 hlifrom=100000,
                 hlito=1000000000;

        $hlincome.ionRangeSlider({
             type: "single",
                min: hlimin,
                max: hlimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hlito = data.from;

                    updatehlincomeValue();
                }
        });
        
        $hbalance = $("#hbalance"),
                //$from = $(".js-from"),
                 $hbto = $("#hs_balance"),
                 hbalance,
                 hbmin = 0,
                 hbmax = 1000000000, 
                 hbfrom=0,
                 hbto=1000000000;

        $hbalance.ionRangeSlider({
             type: "single",
                min: hbmin,
                max: hbmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hbto = data.from;

                    updatehbalanceValue();
                }
        });
        
        $hexpense = $("#expense"),
                //$from = $(".js-from"),
                 $heto = $("#s_expense"),
                 hexpense,
                 hemin = 0,
                 hemax = 1000000000, 
                 hefrom=0,
                 heto=1000000000;

        $hexpense.ionRangeSlider({
             type: "single",
                min: hemin,
                max: hemax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    heto = data.from;

                    updatehexpenseValue();
                }
        });
        
        $hsavings = $("#overall_savings"),
                //$from = $(".js-from"),
                 $hsto = $("#s_overall_savings"),
                 hsavings,
                 hsmin = 0,
                 hsmax = 1000000000, 
                 hsfrom=0,
                 hsto=1000000000;

        $hsavings.ionRangeSlider({
             type: "single",
                min: hsmin,
                max: hsmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hsto = data.from;

                    updatehsavingsValue();
                }
        });
        
        $hcover = $("#existinInsuranceCover"),
                //$from = $(".js-from"),
                 $hcto = $("#s_existinInsuranceCover"),
                 hcover,
                 hcmin = 0,
                 hcmax = 1000000000, 
                 hcfrom=0,
                 hcto=1000000000;

        $hcover.ionRangeSlider({
             type: "single",
                min: hcmin,
                max: hcmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    hcto = data.from;

                    updatehcoverValue();
                }
        });       
        
               
        hage = $ha_age.data("ionRangeSlider");
        hret_age = $hret_age.data("ionRangeSlider");
        hlincome = $hlincome.data("ionRangeSlider");
        hbalance = $hbalance.data("ionRangeSlider");
        hexpense = $hexpense.data("ionRangeSlider");
        hsavings = $hsavings.data("ionRangeSlider");
        hcover = $hcover.data("ionRangeSlider");
        
        $hlv_ato.on("change", function () {
            hato = +$(this).val();
            if (hato > hamax) {
                hato = hamax;
            }
            updatehAgeValue();    
            updatehAgeRange();
        });
        
        $hrato.on("change", function () {
            hrato = +$(this).val();
            if (hrato > hramax) {
                hrato = hramax;
            }
            updatehretAgeValue();    
            updatehretAgeRange();
        });
        
        $hlito.on("change", function () {
            hlito = +$(this).val().replace(/,/g , "");
            if (hlito > hlimax) {
                hlito = hlimax;
            }
            updatehlincomeValue();    
            updatehlincomeRange();
        });
        
        $hbto.on("change", function () {
            hbto = +$(this).val().replace(/,/g , "");
            if (hbto > hbmax) {
                hbto = hbmax;
            }
            updatehbalanceValue();    
            updatehbalanceRange();
        });
        
        $heto.on("change", function () {
            heto = +$(this).val().replace(/,/g , "");
            if (heto > hemax) {
                heto = hemax;
            }
            updatehexpenseValue();
            updatehexpenseRange();
        });
        
        $hsto.on("change", function () {
            hsto = +$(this).val().replace(/,/g , "");
            if (hsto > hsmax) {
                hsto = hsmax;
            }
            updatehsavingsValue();    
            updatehsavingsRange();
        });
        
        $hcto.on("change", function () {
            hcto = +$(this).val().replace(/,/g , "");
            if (hcto > hcmax) {
                hcto = hcmax;
            }
            updatehcoverValue();    
            updatehcoverRange();
        });
        
    });
    
    var updatehAgeValue = function () {  	
     //   alert(hato);
        $('#hlvs_age').val(hato);
        //$ato.val(ato);
    };
    
    var updatehAgeRange = function () {
        hage.update({
           from: hato
           //to: ato
        });
    };
    
    var updatehretAgeValue = function () {    
        $hrato.val(hrato);
        //$ato.val(ato);
    };
    
    var updatehretAgeRange = function () {
        hret_age.update({
           from: hrato
           //to: ato
        });
    };
    
    var updatehlincomeValue = function () {    
        $hlito.val(addCommas2(hlito));
        //$ato.val(ato);
    };
    
    var updatehlincomeRange = function () {
        hlincome.update({
           from: hlito
           //to: ato
        });
    };
    
    var updatehbalanceValue = function () {
        $hbto.val(addCommas2(hbto));
    };
     
    var updatehbalanceRange = function () {
        hbalance.update({
            from: hbto
        });
    };
    
    var updatehexpenseValue = function () {
        $heto.val(addCommas2(heto));
    };
     
    var updatehexpenseRange = function () {
        hexpense.update({
            from: heto       
        });
    };
    
    var updatehsavingsValue = function () {
        $hsto.val(addCommas2(hsto));
    };
     
    var updatehsavingsRange = function () {
        hsavings.update({
            from: hsto       
        });
    };
    
    var updatehcoverValue = function () {
        $hcto.val(addCommas2(hcto));
    };
     
    var updatehcoverRange = function () {
        hcover.update({
            from: hcto       
        });
    };
    
    
    function hlv_calculator(age,ret_age,income,balance,expense,savings,cover){
        
        var retirementYearsLeft = (ret_age - age);
        //console.log(retirementYearsLeft);
            
        //  ((investmentReturnAnnual(7) + 1) / (rateOfInflation(6) + 1)) - 1;
        rateOfReturn = ((7 + 1) / (6 + 1)) - 1; 
        annualSavingIncome = (income - (expense * 12) );

        hlvCovered = (annualSavingIncome * (1 - (1 / Math.pow((1 + rateOfReturn), retirementYearsLeft))) / rateOfReturn); 
        //console.log(hlvCovered);
        //$totalInsuranceCover = hlvCovered + spouseHlv + totalChildCost - $savingsInvestmentValue - $existinInsuranceCover + $this->balance;
        totalInsuranceCover = hlvCovered - savings - cover + balance;
        
        totalInsuranceCover =  parseInt(totalInsuranceCover.toFixed(0));
        return totalInsuranceCover;
    }