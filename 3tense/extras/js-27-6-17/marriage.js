    var $mage,
        $mafrom,
        $mato,
        mage,
        mamin,
        mamax,
        mafrom,
        mato;

    var $mfut_age,
        $mifrom,
        $mito,
        mimin,
        mfut_age,
        mimax,
        mifrom,
        mito;

    var $mcost,
        $mfrom,
        $mto,
        mmin,
        mcost,
        mmax,
        mfrom,
        mto;

    var $msavings,
        $msfrom,
        $msto,
        msavings,
        msmin,        
        msmax,
        msfrom,
        msto;

    var $minf_rate,
        $mbfrom,
        $mbto,
        minf_rate,
        mbmin,
        mbmax,
        mbfrom,
        mbto;

    var $mint_rate,
        $mrfrom,
        $mrto,
        mint_rate,
        mrmin,
        mrmax,
        mrfrom,
        mrto;

    var childAge;    
    var marriageAge;
    var marriageExpenditure;
    var currentSavings;
    var rateOfInflation;
    var rateOfReturn;
    var n1;
    var futurevalue;
    var op = 0;
    
    function MarraigeresetSlider()
    {
        $("#m_age").data("ionRangeSlider").update({
         from: 0
        });
        $("#s_m_age").val("0");

        //expected returns
        $("#marg_age").data("ionRangeSlider").update({
           from: 18
        });
        $("#s_marg_age").val("18");

        //Outstanding Balance
        $("#m_cost").data("ionRangeSlider").update({
           from: 0
        });
        $("#s_m_cost").val("0");
        
        //Montlhy Expense
        $("#m_savings").data("ionRangeSlider").update({
           from: 0
        });
        $("#s_m_savings").val("0");
        
        //Total Savings
        $("#m_inflation").data("ionRangeSlider").update({
           from: 5
        });
        $("#s_m_inflation").val("5");
        
        //Insurance Cover
        $("#m_return").data("ionRangeSlider").update({
           from: 8
        });
        $("#s_m_return").val("8");
        
        $(".mrg_result").css('display','none');

    }
    
    $(document).ready(function(){
        
        $('#marriage_calc').click(function(){            
            $('#Marriage').modal('show');
            MarraigeresetSlider();
        });
        
        $("#marg_calc").click(function(){  
            
            childAge            = parseInt($('#m_age').val());    
            marriageAge         = parseInt($('#marg_age').val());
            marriageExpenditure = parseInt($('#m_cost').val().replace(/,/g , ""));
            currentSavings      = parseInt($('#m_savings').val().replace(/,/g , ""));
            rateOfInflation     = parseFloat($('#m_inflation').val());
            rateOfReturn        = parseFloat($('#m_return').val());
            
//            console.log('age',childAge);
            
            if( (marriageExpenditure) == 0 || (rateOfInflation) == 0 || (rateOfReturn) == 0){
                alert("Specify Marriage Cost");
                return false;
            }
            
            
            //http://www.calculator.net/present-value-calculator.html
            //http://seattlecentral.edu/faculty/moneil/A230/AppxC/Ch15AppxA.htm
            
            var years = marriageAge - childAge;
            years = Math.abs(years);
            n1 = 1 + (rateOfReturn  /100);
            
            // FV=PMT[((1+i)^n-1)/i]
            //futurevalue1 = (marriageExpenditure/12) * (( Math.pow(n1, years)- 1) / (rateOfReturn / 100));
            
            // FV=PV(1+r)^n;            
            //futurevalue = marriageExpenditure * Math.pow(n1,years);
            
            // http://indianmoney.com/websiteNew/childgoal-planner.php
            var value=Math.pow((1+(rateOfInflation/100)),years);
            futurevalue = value*marriageExpenditure;            
            
            op = marg_calc(childAge,marriageAge,marriageExpenditure,currentSavings,rateOfInflation,rateOfReturn);
            
            //Lumpsum Value
            marriageLumpsum=Math.round((futurevalue/(Math.pow(1+(rateOfReturn/100),years))));
            
            if( op < 0 || op == -0){
                op = 0;
                marriageLumpsum = 0;
            }
               
            $('#mrmonthly_amount').html(addCommas2(op));
            //$('#mryearly_amount').html(op*12);
            $('#mryearly_amount').html(addCommas2(marriageLumpsum));
            $('#mrfuture_amount').html(addCommas2(futurevalue.toFixed(0)));
            $(".mrg_result").css('display','block');
            
        });
        
        
    //---------------------------- Slider Code ----------------------------------//
       
        //---------- Current Age ---------------
        
        $mage = $("#m_age"),
                //$from = $(".js-from"),
                $mato = $("#s_m_age"),
                mage,
                mamin = 0,
                mamax = 60, 
                mafrom=0,
                mato=60;

        $mage.ionRangeSlider({
                type: "single",
                min: mamin,
                max: mamax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    mato = data.from;
                    
                    updatemAgeValue();
                }
        });
        
        mage = $mage.data("ionRangeSlider");
        
        $mato.on("change", function () {
            mato = +$(this).val();
            if (mato > mamax) {
                mato = mamax;
            }
            updatemAgeValue();    
            updatemAgeRange();
        });
        
        //---------- Future Age ---------------
        
        $mfut_age = $("#marg_age"),
                //$from = $(".js-from"),
                $mito = $("#s_marg_age"),
                mfut_age,
                mimin = 18,
                mimax = 60, 
                mifrom=18,
                mito=60;

        $mfut_age.ionRangeSlider({
                type: "single",
                min: mimin,
                max: mimax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    mito = data.from;
                    
                    updatemfutAgeValue();
                }
        });
        
        mfut_age = $mfut_age.data("ionRangeSlider");
        
        $mito.on("change", function () {
            mito = +$(this).val();
            if (mito > mimax) {
                mito = mimax;
            }
            updatemfutAgeValue();    
            updatemfutAgeRange();
        });
        
        
        //---------- Future Marriage Cost ---------------
        
        $mcost = $("#m_cost"),
                //$from = $(".js-from"),
                $mto = $("#s_m_cost"),
                mcost,
                mmin = 0,
                mmax = 1000000000, 
                mfrom=0,
                mto=1000000000;

        $mcost.ionRangeSlider({
                type: "single",
                min: mmin,
                max: mmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    mto = data.from;
                    
                    updatemcostValue();
                }
        });
        
        mcost = $mcost.data("ionRangeSlider");
        
        $mto.on("change", function () {
            mto = +$(this).val().replace(/,/g , "");
            if (mto > mmax) {
                mto = mmax;
            }
            updatemcostValue();    
            updatemcostRange();
        });
        
        
        //---------- Total Savings ---------------
        
        $msavings = $("#m_savings"),
                //$from = $(".js-from"),
                $msto = $("#s_m_savings"),
                msavings,
                msmin = 0,
                msmax = 1000000000, 
                msfrom=0,
                msto=100000000;

        $msavings.ionRangeSlider({
                type: "single",
                min: msmin,
                max: msmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    msto = data.from;
                    updatemsavingsValue();
                }
        });
        
        msavings = $msavings.data("ionRangeSlider");
        
        $msto.on("change", function () {
            msto = +$(this).val().replace(/,/g , "");
            if (msto > msmax) {
                msto = msmax;
            }
            updatemsavingsValue();    
            updatemsavingsRange();
        });
        
        
        //---------- Inflation Rate ---------------
        
        $minf_rate = $("#m_inflation"),
                //$from = $(".js-from"),
                $mbto = $("#s_m_inflation"),
                minf_rate,
                mbmin = 0,
                mbmax = 15, 
                mbfrom=0,
                mbto=0;

        $minf_rate.ionRangeSlider({
                type: "single",
                min: mbmin,
                max: mbmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    mbto = data.from;
                    updateminflationValue();
                }
        });
        
        minf_rate = $minf_rate.data("ionRangeSlider");
        
        $mbto.on("change", function () {
            mbto = +$(this).val();
            if (mbto > mbmax) {
                mbto = mbmax;
            }
            updateminflationValue();    
            updateminflationRange();
        });
        
        
        //---------- Interest Rate ---------------
        
        $mint_rate = $("#m_return"),
                //$from = $(".js-from"),
                $mrto = $("#s_m_return"),
                mint_rate,
                mrmin = 0,
                mrmax = 15, 
                mrfrom=0,
                mrto=0;

        $mint_rate.ionRangeSlider({
                type: "single",
                min: mrmin,
                max: mrmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                step:0.25,
                onChange: function (data) {
                    //from = data.from;
                    mrto = data.from;
                    updateminterestValue();
                }
        });
        
        mint_rate = $mint_rate.data("ionRangeSlider");
        
        $mrto.on("change", function () {
            mrto = +$(this).val();
            if (mrto > mrmax) {
                mrto = mrmax;
            }
            updateminterestValue();    
            updateminterestRange();
        });
        
        
        
        
    });  //  Jquery Ends
    
    
    
    
    
    //----------- Marriage cost calculation --------------//
    
    function marg_calc(childAge,marriageAge,marriageExpenditure,currentSavings,rateOfInflation,rateOfReturn){
        
        var diff = marriageAge - childAge;
        diff = Math.abs(diff);
        
        // Amount required at the time of wedding
        amountForWedding = marriageExpenditure * (Math.pow((1 + (rateOfInflation / 100)), (diff)));
        
        // Future value of your savings after (child marriage age - child age) years
        futureSavingValue = currentSavings * (Math.pow((1 + (rateOfReturn / 100)), (diff)));
        
        // Future amount required after at the time of marriage
        futureMarriageAmount = amountForWedding - futureSavingValue;

        // Amount you should save monthly
        amountToSave = (futureMarriageAmount * (Math.pow(((100 + rateOfReturn) / 100), (1 / 12)) - 1) / (Math.pow(((100 + (Math.pow(((100 + rateOfReturn) / 100), (1 / 12)) - 1) * 100) / 100), ((diff) * 12)) - 1));
        //console.log('o/p',amountToSave);
        if (amountToSave) {
          amountToSave = amountToSave.toFixed(0);
        } else {
          amountToSave = 0;
        }
        //console.log(amountToSave);
        return amountToSave;
        
    }
    
    var updatemAgeValue = function () {    
        $mato.val(mato);
        //$ato.val(ato);
    };
    
    var updatemAgeRange = function () {
        mage.update({
           from: mato
           //to: ato
        });
    };
    
    var updatemfutAgeValue = function () {    
        $mito.val(mito);
        //$ato.val(ato);
    };
    
    var updatemfutAgeRange = function () {
        mfut_age.update({
           from: mito
           //to: ato
        });
    };
    
    var updatemcostValue = function () {    
        $mto.val(addCommas2(mto));
        //$ato.val(ato);
    };
    
    var updatemcostRange = function () {
        mcost.update({
           from: mto
           //to: ato
        });
    };
    
    var updatemsavingsValue = function () {    
        $msto.val(addCommas2(msto));
        //$ato.val(ato);
    };
    
    var updatemsavingsRange = function () {
        msavings.update({
           from: msto
           //to: ato
        });
    };
    
    var updateminflationValue = function () {    
        $mbto.val(mbto);
        //$ato.val(ato);
    };
    
    var updateminflationRange = function () {
        minf_rate.update({
           from: mbto
           //to: ato
        });
    };
     
    var updateminterestValue = function () {    
        $mrto.val(mrto);
        //$ato.val(ato);
    };
    
    var updateminterestRange = function () {
        mint_rate.update({
           from: mrto
           //to: ato
        });
    };