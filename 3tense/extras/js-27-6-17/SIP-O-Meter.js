    var $age,
        $afrom,
        $ato,
        age,
        amin,
        amax,
        afrom,
        ato;

    var $income,
        $ifrom,
        $ito,
        imin,
        imax,
        ifrom,
        ito;

    var $balance,
        $bfrom,
        $bto,
        bmin,
        bmax,
        bfrom,
        bto;

  function ResetSlider()
  {
       var sliderInvest = $("#ageslider").data("ionRangeSlider");
       sliderInvest.update({
        from: 1
       });
       
   $("#MonthSIP").val("1");
   
   //expected returns
   $("#income").data("ionRangeSlider").update({
      from: 1
   });
   
   $("#rateOfReturn").val("1");
   
   //No.Of years
   $("#balance").data("ionRangeSlider").update({
      from: 1
   });
   $("#installment").val("1");
      
  }

    $(document).ready(function(){
       
        $('body').on('hidden.bs.modal', '.modal', function () {
          ResetSlider();
            $('#result').css('display','none');
       })

        $('#SipCalNw').click(function(){
            //$('#myModal').modal('show')
            
            $('#SIPoMeter').modal('show');
        });
        
        $("#btn_calcSIP").click(function(){
          
            if ($("#MonthSIP").val()=="0") {
             alert('Monthly Installment can not be zero!');
             $("#MonthSIP").focus();
             return false;
         }
          if ($("#rateOfReturn").val()=="0") {
             alert('Expected rate of return can not be zero!');
             $("#rateOfReturn").focus();
             return false;}
           if ($("#installment").val()=="0") {
             alert('No.of monthly Installment can not be zero!');
             $("#installment").focus();
             return false;
         }
         else{
            var ROI=$("#rateOfReturn").val();
            $('#lblROI').text(ROI+"%");
            var MonthlySIP = $("#MonthSIP").val().replace(/,/g , "");
            var RateOfReturn = $("#rateOfReturn").val();
            var installment = $("#installment").val();
           
            var params = "1";
            var months, years, futurevalue, earnings;
            var sip_amount = MonthlySIP;
            var num_of_installment = installment;
            var sip_exinterest_rate = RateOfReturn;
            switch (params) {
                case "1":

                    var n1 = Math.pow((1 + sip_exinterest_rate / 1200), (num_of_installment));
                    futurevalue = (sip_amount * ((n1 - 1) / (sip_exinterest_rate / 1200))) * (1 + sip_exinterest_rate / 1200);
                    years = parseInt(num_of_installment / 12);
                    months = parseFloat(num_of_installment % 12);
                    break;

                case "2":
                    var res = 0, finalres = 0;

                    for (var i = 1; i <= parseInt(num_of_installment); i++) {
                        var n2 = Math.pow((1 + parseFloat(sip_exinterest_rate) / 1200), (parseInt(num_of_installment) * 3 - (i - 1) * 3));
                        res = parseFloat(sip_amount) * n2;
                        finalres = finalres + res;

                    }
                    futurevalue = finalres;
                    years = parseInt((num_of_installment * 3) / 12);
                    months = parseFloat((num_of_installment * 3) % 12);
                    break;

                default:
                    futurevalue = 0;
                    years = 0;
                    months = 0;
                    break;
            }
            futurevalue =Math.round( futurevalue.toFixed(2));
            investment = Math.round((sip_amount * num_of_installment).toFixed(2));
            earnings = Math.round((futurevalue - investment).toFixed(2));
            
            SIPReturn_charts(investment,earnings);
            
            /* if (win.width() <= 980) {
              $('.mblsz').addClass('SipOmtereAlgin');
             }
             else
             {
                 $('.mblsz').removeClass('SipOmtereAlgin');
             }*/
            
            
            
            
            
            $('#result').css('display','table');
            $('.investment').text(addCommas2(investment));
            $('.earning').text(addCommas2(earnings));
            $('.FV').text(addCommas2(futurevalue));
            
           // $('#TblFutureVal').find('td remv').remove(); 
            $('.remv').remove();
            FunFillXIRR();
        }
        });
        
        
        // XIRR for period starts here 
        function FunFillXIRR()
        {
            var defaultExpRet = [10,12,15,20,1];
            var DefaultPeriod = [60,120,180,240,300,360,1];
            
            var monthlySIP = $('#MonthSIP').val().replace(/,/g , "");
            var Tbl=$('#TblFutureVal');
            for(var i=0;i<=4;i++)
            { 
                var row=document.createElement("tr");
                 row.className="remv";
                if(defaultExpRet[i]!="1")
                {
                 
                  var colOuter=document.createElement("td");
                  colOuter.innerHTML=defaultExpRet[i]+'.0'+'%';
                  colOuter.class="center";
                  colOuter.style="width:15px;text-align:center;";
                  row.appendChild(colOuter);
               }
               else
               {
                   var colOuter=document.createElement("td");
                   colOuter.style="padding-top: 11px;width: 108px;text-align:center;"
                  colOuter.innerHTML="<input type='text' class='cal_textboxsmall' name='cPeriod' id='CustomExptdRet' onkeypress='return isNumberKey(event)'  onblur='calculateSipReturnCustom();' maxlength='10' style='width: 90px;border:1px solid #e0e0e0;text-align:center;'>";
                  colOuter.class="remv";                
                  row.appendChild(colOuter);              
                }
                  
              for( var j=0;j<=6;j++)
              {
                  if (defaultExpRet[i]!="1") {
                    
                            if(DefaultPeriod[j]!="1")
                            {
                              var colOuter=document.createElement("td");
                              colOuter.innerHTML=addCommas2(Math.round( CalculateColReturn(monthlySIP,defaultExpRet[i], DefaultPeriod[j])));
                              colOuter.class="center remv";
                              row.appendChild(colOuter);
                            }
                            else
                            {
                             var colOuter=document.createElement("td");
                              //colOuter.innerHTML="";
                              colOuter.class="center remv";
                              colOuter.id="custom"+i+j;
                              row.appendChild(colOuter);
                            }
                 
                         }
                        else
                        {
                            var colOuter=document.createElement("td");
                               colOuter.innerHTML="";
                               colOuter.class="center remv";
                               colOuter.id="custom"+i+j;
                               row.appendChild(colOuter);
                        }
              }
                       
                Tbl.append(row);
            }
           
        }
        
        $age = $("#ageslider"),
                //$from = $(".js-from"),
                $ato = $("#MonthSIP"),
                age,
                amin = 1,
                amax = 1000000000, 
                afrom=1,
                ato=1000000000;

        $age.ionRangeSlider({
                type: "single",
                min: amin,
                max: amax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    ato = data.from;
                    
                    updateAgeValue();
                }
        });
        
        $income = $("#income"),
                //$from = $(".js-from"),
                 $ito = $("#rateOfReturn"),
                 income,
                 imin = 1,
                 imax = 20, 
                 ifrom=1,
                 ito=20;

        $income.ionRangeSlider({
             type: "single",
                min: imin,
                max: imax,
                prettify_enabled: false,
                grid: true,
                step: 0.5,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    ito = data.from;

                    updateincomeValue();
                }
        });
        
        $balance = $("#balance"),
                //$from = $(".js-from"),
                 $bto = $("#installment"),
                 balance,
                 bmin = 1,
                 bmax = 360, 
                 bfrom=0,
                 bto=360;

        $balance.ionRangeSlider({
             type: "single",
                min: bmin,
                max: bmax,
                prettify_enabled: false,
                grid: true,
                grid_num: 1,
                onChange: function (data) {
                    //from = data.from;
                    bto = data.from;

                    updatebalanceValue();
                }
        });
        
        
        age = $age.data("ionRangeSlider");
        income = $income.data("ionRangeSlider");
        balance = $balance.data("ionRangeSlider");
        
        $ato.on("change", function () {
            ato = +$(this).val().replace(/,/g , "");
            if (ato > amax) {
                ato = amax;
            }
            updateAgeValue();    
            updateAgeRange();
        });
        
        $ito.on("change", function () {
            ito = +$(this).val().replace(/,/g , "");
            if (ito > imax) {
                ito = imax;
            }
            updateincomeValue();    
            updateincomeRange();
        });
        
        $bto.on("change", function () {
            bto = +$(this).val().replace(/,/g , "");
            if (bto > bmax) {
                bto = bmax;
            }
            updatebalanceValue();    
            updatebalanceRange();
        });
        
    });
    
     var updateAgeValue = function () {    
        $ato.val(addCommas2(ato));
        //$ato.val(ato);
    };
    
    
    var updateAgeRange = function () {
        age.update({
           from: ato
           //to: ato
        });
    };
    
    var updateincomeValue = function () {    
        $ito.val(addCommas2(ito));
        //$ato.val(ato);
    };
    
    var updateincomeRange = function () {
        income.update({
           from: ito
           //to: ato
        });
    };
    
    var updatebalanceValue = function () {
        $bto.val(addCommas2(bto));
    };
     
    var updatebalanceRange = function () {
        balance.update({
            from: bto       
        });
    };
    
    //To calculate individual TD Returns
     function CalculateColReturn(MonthlySIP,RateOfReturn,installment)
        {
            var MonthlySIP = MonthlySIP;
            var RateOfReturn = RateOfReturn;
            var installment = installment;
           
            var params = "1";
            var months, years, futurevalue, earnings;
            var sip_amount = MonthlySIP;
            var num_of_installment = installment;
            var sip_exinterest_rate = RateOfReturn;
            switch (params) {
                case "1":

                    var n1 = Math.pow((1 + sip_exinterest_rate / 1200), (num_of_installment));
                    futurevalue = (sip_amount * ((n1 - 1) / (sip_exinterest_rate / 1200))) * (1 + sip_exinterest_rate / 1200);
                    years = parseInt(num_of_installment / 12);
                    months = parseFloat(num_of_installment % 12);
                    break;

                case "2":
                    var res = 0, finalres = 0;

                    for (var i = 1; i <= parseInt(num_of_installment); i++) {
                        var n2 = Math.pow((1 + parseFloat(sip_exinterest_rate) / 1200), (parseInt(num_of_installment) * 3 - (i - 1) * 3));
                        res = parseFloat(sip_amount) * n2;
                        finalres = finalres + res;

                    }
                    futurevalue = finalres;
                    years = parseInt((num_of_installment * 3) / 12);
                    months = parseFloat((num_of_installment * 3) % 12);
                    break;

                default:
                    futurevalue = 0;
                    years = 0;
                    months = 0;
                    break;
            }
            futurevalue = futurevalue.toFixed(2);
            investment = (sip_amount * num_of_installment).toFixed(2);
            earnings = (futurevalue - investment).toFixed(2);
            
            return futurevalue;
            
        }
    
    //for textbox on blur click
        function calculateSipReturnCustom()
        {
              var customExptedRat=$('#CustomExptdRet').val();
              var CustomTenure=$('#CPeriod').val();
             
           //  if (customExptedRat!="" &&CustomTenure!="" ) {               
                var defaultExpRet = [10,12,15,20,1];
                var DefaultPeriod = [60,120,180,240,300,360,1];
            
              // if(customExptedRat!="" && CustomTenure!="" )
               //{
                var monthlySIP = $('#MonthSIP').val().replace(/,/g , "");
                 var Tbl=$('#TblFutureVal');
                    for(var i=0;i<=4;i++)
                    { 
                      for( var j=0;j<=6;j++)
                      {
                          if (defaultExpRet[i]!="1") 
                          {
                                    if(DefaultPeriod[j]=="1")
                                    {
                                          $('#custom'+i+j).text(addCommas2(Math.round(CalculateColReturn( monthlySIP,defaultExpRet[i],CustomTenure))));
                                   }
                          }
                           else
                              {
                                  if(customExptedRat!="")
                                  {
                                        if(DefaultPeriod[j]!="1")
                                        {
                                         $('#custom'+i+j).text(addCommas2(Math.round(CalculateColReturn( monthlySIP,customExptedRat,DefaultPeriod[j]))));
                                       }
                                       else
                                       {
                                         $('#custom'+i+j).text(addCommas2(Math.round(CalculateColReturn( monthlySIP,customExptedRat,CustomTenure))));
                                       }
                                   
                                  }
                                   
                                   
                               }
                      }
                    }
               // }
          
      }
    
    
    //validation for numeric values
    function isNumberKey(evt)
      {
        var charCode = (evt.which) ? evt.which : event.keyCode
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;
 
          return true;
      }
    
    
    
    
   function SIPReturn_charts(val1,val2)
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
        var myPieChart = new Chart(document.getElementById("mf-chart"),{
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