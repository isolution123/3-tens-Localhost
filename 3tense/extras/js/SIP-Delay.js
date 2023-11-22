    var $TargetCorp,
        $TarCrpFrm,
        $TarCorp,
        targetCorp,
        TarCrpMin,
        TarCrpMax,
        TarCrpFrm,
        TarCorpTo;

    var $TimeHorizon,
        TimeHorizon,
        $TimeHoriznFrom,
        $TimeHorizTo,
        TimeHoznMin,
        TimeHoznMax,
        TimeHorizFrm,
        TimeHorizTo;

    var $ROISlider,
        ROISlider,
       $ROIfrom,
        $ROITo,
        ROIMin,
        ROIMax,
        ROIFrm,
        ROITo;

  var $delaySlider,
       delaySlider,
        $Dfrom,
        $Dto,
        DMin,
        DMax,
        DFrm,
        Dto;

//global Variable for SIP Delay Reverse
 var MonthSIPAmount;
 var LumpsumInvestment;
 var SIPDelayedAmount=0;

var LumpsumDelayAmount=0;

  function ResetSliderSIPDelay()
  {
       var sliderInvest = $("#TargetCorpslider").data("ionRangeSlider");
        sliderInvest.update({
         from: 1
       });
       
   $("#TxtTargetCorp").val("1");
   
   //expected returns
   $("#TimeHorizon").data("ionRangeSlider").update({
      from: 1
   });
   
   $("#txtHorizon").val("1");
   
   //No.Of years
   $("#ROISlider").data("ionRangeSlider").update({
      from: 1
   });
   $("#TxtRoi").val("1");
   
    $("#delaySlider").data("ionRangeSlider").update({
      from: 0
   });
   $("#txtDelay").val("0");
      
      
      
   // to set size of the result div according to Viewport   
    $('#resultSIP_Delay').addClass('RemovesmallResultDisplay');   
    $('#resultSIP_Delay').css('display','none');
     
  }

    $(document).ready(function(){
       
       //check for browser version
      
         $('.tooltip').mouseover(function(){
            $(this).children('span').show();
          }).mouseout(function(){
            $(this).children('span').hide();
          });
         
      
       //ends here 

        $('body').on('hidden.bs.modal', '.modal', function () {
           ResetSliderSIPDelay();
           $('#result').css('display','none');
       });

       $('#SipCalNw').click(function(){
            //$('#myModal').modal('show')
            
            $('#SIPoMeter').modal('show');
        });
        
        
         $('#BtnSIPDelay').click(function(){
            //$('#myModal').modal('show')
            
            $('#SIPDelay').modal('show');
        });
        
        
        
        
        // to Calculate SIP Delay
        $("#btn_calcSIP_Delay").click(function(){
            
            if ( $("#TxtTargetCorp").val()=="")
		{
			alert("Enter target amount!")
			$("#TxtTargetCorp").focus();
			return false;
		}
            
             if ( $("#txtHorizon").val()=="")
		{
			alert("Enter time horizon!")
			$("#txtHorizon").focus();
			return false;
		}
                
             if ( $("#TxtRoi").val()=="")
		{
			alert("Enter expected rate of return!")
			$("#TxtRoi").focus();
			return false;
		}
             var TargetCorpus = $("#TxtTargetCorp").val().replace(/,/g , "");;
             var RateOfReturn = $("#TxtRoi").val();
             var N = $("#txtHorizon").val();
             var DelayMonth=$("#txtDelay").val();
            fnCalculateSIP(TargetCorpus,N,RateOfReturn,DelayMonth);
             $('#delayMnth').text(DelayMonth);
             
             $('#delayMnthLumpsum').text(DelayMonth);
             $('#LumpsumPv').text(addCommas2(LumpsumInvestment));
             $('#MonthlySIPInvest').text(addCommas2(Math.round( MonthSIPAmount)));
             $('#sipDelayAmntLoose').text(addCommas2(Math.round(SIPDelayedAmount)));
             $('#LumpsumDelayAmntLoose').text(addCommas2(Math.round(LumpsumDelayAmount)));
             
             $('#hidden_LumpsumPv').val(addCommas2(LumpsumInvestment));
             $('#hidden_MonthlySIPInvest').val(addCommas2(Math.round( MonthSIPAmount)));
             $('#hidden_sipDelayAmntLoose').val(addCommas2(Math.round(SIPDelayedAmount)));
             $('#hidden_LumpsumDelayAmntLoose').val(addCommas2(Math.round(LumpsumDelayAmount)));
             // to check viewport
//             if (win.width() <= 980) {
//               $('#resultSIP_Delay').removeClass('RemovesmallResultDisplay');
//               $('#resultSIP_Delay').addClass('smallResultDisplay');
//                  //$('#resultSIP_Delay').css('display','table');
//                 //$('#resultSIP_Delay').css('margin-left','6%');
//                 
//                 
//              }
//              else
//              {
//                  $('#resultSIP_Delay').removeClass('smallResultDisplay');
//                  //$('#resultSIP_Delay').css('margin-left','0%');
//                 $('#resultSIP_Delay').css('display','block');
//               
//              }
             
             $('#resultSIP_Delay').css('display','block');
             
              //code for client calc Visit strt
               $.ajax({
                        type:'POST',
                        url:'Calculators/SaveCalcVisit',
                        data:{'calcType':'investmentdelay'},
                        success:function(data){
                          alert(data);
                        }
                    });
             
             //end here 
             
             
            
        });
        
        
        $TargetCorp = $("#TargetCorpslider"),
                //$from = $(".js-from"),
                $TarCorp = $("#TxtTargetCorp"),
              //  TargetCorpTargetCorp,
                TarCrpMin = 1,
                TarCrpMax = 1000000000, 
                TarCrpFrm=1,
                TarCorpTo=1000000000;

        $TargetCorp.ionRangeSlider({
                type: "single",
                min: TarCrpMin,
                max: TarCrpMax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    TarCorpTo = data.from;    
                    updateTarCorpValue();
                }
        });
        
        $TimeHorizon = $("#TimeHorizon"),
                //$from = $(".js-from"),
                 $TimeHorizTo = $("#txtHorizon"),
                 income,
                 TimeHoznMin = 1,
                 TimeHoznMax = 100, 
                 TimeHorizFrm=1,
                 TimeHorizTo=100;

        $TimeHorizon.ionRangeSlider({
             type: "single",
                min: TimeHoznMin,
                max: TimeHoznMax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    TimeHorizTo = data.from;

                    updateTimeHorizValue();
                }
        });
        
        $ROISlider = $("#ROISlider"),
                //$from = $(".js-from"),
                 $ROITo = $("#TxtRoi"),
                 balance,
                 ROIMin = 1,
                 ROIMax = 100, 
                 ROIFrm=1,
                 ROITo=100;

        $ROISlider.ionRangeSlider({
             type: "single",
                min: ROIMin,
                max: ROIMax,
                step: 0.5,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    ROITo = data.from;

                    updateROIValue();
                }
        });
        
         $delaySlider = $("#delaySlider"),
                //$from = $(".js-from"),
                 $Dto = $("#txtDelay"),
                 balance,
                 ROIMin = 0,
                 ROIMax = 12, 
                 ROIFrm=0,
                 Dto=12;

        $delaySlider.ionRangeSlider({
             type: "single",
                min: ROIMin,
                max: ROIMax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    Dto = data.from;

                    updateDelayValue();
                }
        });
               
        targetCorp = $TargetCorp.data("ionRangeSlider");
        TimeHorizon = $TimeHorizon.data("ionRangeSlider");
        ROISlider = $ROISlider.data("ionRangeSlider");
        delaySlider= $delaySlider.data("ionRangeSlider");
        $TarCorp.on("change", function () {
            TarCorpTo = +$(this).val().replace(/,/g , "");
            if (TarCorpTo > TarCrpMax) {
                TarCorpTo = TarCrpMax;
            }
            updateTarCorpValue();    
            updateTarCorpRange();
        });
        
        $TimeHorizTo.on("change", function () {
            TimeHorizTo = +$(this).val();
            if (TimeHorizTo > TimeHoznMax) {
                TimeHorizTo = TimeHoznMax;
            }
            updateTimeHorizValue();    
            updateTimeHorizRange();
        });
        
        $ROITo.on("change", function () {
            ROITo = +$(this).val();
            if (ROITo > ROIMax) {
                ROITo = ROIMax;
            }
            updateROIValue();    
            updateROIRange();
        });
        
        
        $Dto.on("change", function () {
            Dto = +$(this).val();
            if (Dto > ROIMax) {
                Dto = ROIMax;
            }
            updateDelayValue();    
            updateDelayRange();
        });
                      
    });
    
    var updateTarCorpValue = function () {    
        $TarCorp.val(addCommas2(TarCorpTo));
       
    };
    
    var updateTarCorpRange = function () {
        targetCorp.update({
           from: TarCorpTo
           
        });
    };
    
    var updateTimeHorizValue = function () {    
        $TimeHorizTo.val(TimeHorizTo);
       
    };
    
    var updateTimeHorizRange = function () {
        TimeHorizon.update({
           from: TimeHorizTo
        });
    };
    
    var updateROIValue = function () {
        $ROITo.val(ROITo);
    };
     
    var updateROIRange = function () {
        ROISlider.update({
            from: ROITo       
        });
    };
    
    
    var updateDelayValue = function () {
        $Dto.val((Dto));
    };
     
    var updateDelayRange = function () {
        delaySlider.update({
            from: Dto       
        });
    };
    
    
    
    //To calculate Monthly SIP
function fnCalculateSIP(TargetCorp,Period,Roi,SipDelay)
{   
       
     var txtCroreAmount = 0.0;
     var txtCroreInfRate = 0.0;     
     var txtCroreMonthInfRate = 0.0;     
     var txtCroreYears = 0;
     var txtCroreMonths = 0;
     var resultVar=0.0;
     var result = 0.0;
     var resultVarMonth=0.0;
     var resultMonth = 0.0;
     txtCroreAmount = parseFloat(TargetCorp);
     txtCroreInfRate =  parseFloat(Roi); // for years
     txtCroreMonthInfRate = parseFloat(Roi); // for months
     if(isNaN(txtCroreInfRate))
     {
        txtCroreInfRate = 0;  
        txtCroreMonthInfRate = 0;      
     }  
     else if(parseFloat(Roi) >= 100.00 || parseFloat(Roi) <= 0.0)
     {
            alert("Rate of Inflation should be between 1 to 99");
            $("#TxtRoi").focus();                        
            return false;
     }        
     txtCroreInfRate = (txtCroreInfRate /100);                  // inflation rate for years
     txtCroreMonthInfRate = (txtCroreMonthInfRate/1200);       // inflation rate for months
     txtCroreYears = parseFloat(Period);      // total no of years  
     txtCroreMonths = parseFloat(Period)*12;  //total no of months 
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
     
      pmt(txtCroreMonthInfRate, txtCroreMonths, 0, txtCroreAmount, 1);
     
     
     
     
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
      /*if(resultMonth > 0)
     {
          MonthSIPAmount = resultMonth.toFixed(0);  
     }    
     else 
     {
        MonthSIPAmount = 0;
     }     */
   
   if(parseInt(SipDelay)!=0 && parseInt(SipDelay)>0 )
   {
       
      
       
       //new logic to calculate delay retuns
     var n1 = Math.pow((1 + txtCroreMonthInfRate), (txtCroreMonths));
      var Originalfuturevalue = (MonthSIPAmount * ((n1 - 1) / (txtCroreMonthInfRate))) * (1 +txtCroreMonthInfRate);
      // years = parseInt(params.num_of_installment / 12);
      //months = parseFloat(params.num_of_installment % 12);
       
       
       //new Delay return
       var d = Math.pow((1 + txtCroreMonthInfRate), (parseInt(txtCroreMonths)-parseInt(SipDelay)));
       var delay = (MonthSIPAmount * ((d - 1) / (txtCroreMonthInfRate))) * (1 +txtCroreMonthInfRate);
       
       
       var delayedMonth=parseInt(txtCroreMonths)-parseInt(SipDelay);
       
       pmt_Delay(txtCroreMonthInfRate, delayedMonth, 0, txtCroreAmount, 1);
       
       
       
       
       
       
       
       
       
       
       //old logic
       // var   monthlySIPAfterDelayVary =((Math.pow(1 + txtCroreMonthInfRate,(txtCroreMonths-parseInt(SipDelay)))-1)/txtCroreMonthInfRate); 
       //var MonthDelaySIP=(txtCroreAmount/monthlySIPAfterDelayVary)  
      
      // SIPDelayedAmount =(parseFloat(Originalfuturevalue)-parseFloat(delay));  //commented for increased sip 06/06/2017
       
       // SIPDelayedAmount = Math.round( delay);
       
   }
   
   //lumpsum amount need to be invested today;
   
   LumpsumInvestment=Math.round((txtCroreAmount/(Math.pow(1+txtCroreInfRate,txtCroreYears))));
   
   var perMonthAmount=LumpsumInvestment/(txtCroreYears*12);
   
    var LumpsumDelayAmountOFDelayedMonths=parseFloat(perMonthAmount)*((txtCroreYears*12)-parseFloat(SipDelay))
   
    LumpsumDelayAmount=Math.round((txtCroreAmount/(Math.pow(1+txtCroreInfRate,(((txtCroreYears*12)-parseInt(SipDelay)))/12))));
   
   
   
    
}

function pmt(rate_per_period, number_of_payments, present_value, future_value, type){
    if(rate_per_period != 0.0){
        // Interest rate exists
        var q = Math.pow(1 + rate_per_period, number_of_payments);
        var resultinmt = (rate_per_period * (future_value + (q * present_value))) / ((-1 + q) * (1 + rate_per_period * (type)));
       MonthSIPAmount = Math.round(resultinmt);

    } else if(number_of_payments != 0.0){
        // No interest rate, but number of payments exists
        return (future_value + present_value) / number_of_payments;
    } 
}

function pmt_Delay(rate_per_period, number_of_payments, present_value, future_value, type){
    if(rate_per_period != 0.0){
        // Interest rate exists
        var q = Math.pow(1 + rate_per_period, number_of_payments);
        var resultinmt = (rate_per_period * (future_value + (q * present_value))) / ((-1 + q) * (1 + rate_per_period * (type)));
     SIPDelayedAmount  = Math.round(resultinmt);

    } else if(number_of_payments != 0.0){
        // No interest rate, but number of payments exists
        return (future_value + present_value) / number_of_payments;
    } 
}
    
    
    
// function add comma

function addCommas2(Num) {
            ////My array

            var Number1 = "" + Num;
            var CommaSeperaterList = '3,2,2,2,2,2,2,2,2';
            var Number = Number1.replace(/\,/g, '');
            var myArray = CommaSeperaterList.split(',');

            var newNum = "";
            var newNum2 = "";
            var count = 0;
            if (Number.indexOf('.') != -1) { ////number ends with a decimal point
                /*if (Number.indexOf('.') == Number.length-1){
                Number += "00";
                }
                if (Number.indexOf('.') == Number.length-2){ ////number ends with a single digit
                Number += "0";
                }*/
                places = Number.length - Number.indexOf('.') - 1;
                if (places >= 3) { ////number ends with a three or more digit
                    num = parseFloat(Number);
                    Number = num.toFixed(2);
                }
                var a = Number.split(".");
                Number = a[0]; ////the part we will commaSeperation
                var end = '.' + a[1] ////the decimal place we will ignore and add back later
            }
            else { var end = ""; }
            var q = 0;
            for (var k = Number.length - 1; k >= 0; k--) {
                ar = myArray[q]

                var oneChar = Number.charAt(k);
                if (count == ar) {
                    newNum += ","
                    newNum += oneChar;
                    count = 1;
                    q++;
                    continue;
                }
                else {
                    newNum += oneChar;
                    count++;
                }
            }
            for (var k = newNum.length - 1; k >= 0; k--) {
                var oneChar = newNum.charAt(k);
                newNum2 += oneChar;
            }
            Num = newNum2 + end;
            return Num
        }
  