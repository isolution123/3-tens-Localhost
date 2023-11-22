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

    $(document).ready(function(){
        //alert(base_url);
        
        
        $("#btn_calcSIP").click(function(){
            var MonthlySIP = $("#MonthSIP").val();
            var RateOfReturn = $("#rateOfReturn").val();
            var installment = $("#installment").val();
           
            //console.log(age+'<--->'+income+'<--->'+balance);
            
            /*$.ajax({
                type:'post',
                url:base_url+'index.php/welcome/call',
                data:'age='+age+'&income='+income+'&balance='+balance+'&expense='+expense+'&savings='+savings+'&cover='+cover,
                success:function(response){
                    alert(response);
                }
            });*/
            
            
            
            var params = $('.freq:checked').val();
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
            
            SIPReturn_charts(investment,earnings);
            $('#result').css('display','block');
            $('.investment').text(investment);
            $('.earning').text(earnings);
            $('.FV').text(futurevalue);
            
        });
        
        // XIRR for period starts here 
        
        function FunFillXIRR()
        {
            
            var defaultExpRet = [10,12,15,20];
            var DefaultPeriod = [60,120,180,240,300,360];
            
            var monthlySIP = $('#MonthSIP').val();
            
            
            $.each(DefaultPeriod,function(index,value){
                
                
                
                
                
                
            });
            
            
            
            
            
        }
        
        
        
        
        
        $age = $("#ageslider"),
                //$from = $(".js-from"),
                $ato = $("#MonthSIP"),
                age,
                amin = 1,
                amax = 100000, 
                afrom=1,
                ato=100000;

        $age.ionRangeSlider({
                type: "single",
                min: amin,
                max: amax,
                prettify_enabled: false,
                grid: true,
                grid_num: 10,
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
                grid_num: 10,
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
                grid_num: 40,
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
            ato = +$(this).val();
            if (ato > amax) {
                ato = amax;
            }
            updateAgeValue();    
            updateAgeRange();
        });
        
        $ito.on("change", function () {
            ito = +$(this).val();
            if (ito > imax) {
                ito = imax;
            }
            updateincomeValue();    
            updateincomeRange();
        });
        
        $bto.on("change", function () {
            bto = +$(this).val();
            if (bto > bmax) {
                bto = bmax;
            }
            updatebalanceValue();    
            updatebalanceRange();
        });
        
    });
    
    var updateAgeValue = function () {    
        $ato.val(ato);
        //$ato.val(ato);
    };
    
    var updateAgeRange = function () {
        age.update({
           from: ato
           //to: ato
        });
    };
    
    var updateincomeValue = function () {    
        $ito.val(ito);
        //$ato.val(ato);
    };
    
    var updateincomeRange = function () {
        income.update({
           from: ito
           //to: ato
        });
    };
    
    var updatebalanceValue = function () {
        $bto.val(bto);
    };
     
    var updatebalanceRange = function () {
        balance.update({
            from: bto       
        });
    };
    
    
    
    
    
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