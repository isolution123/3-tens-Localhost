/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var $range ,
   $from ,
    $to  ,
    range,
    min ,
    max , 
    from,
    to;


//for investmnt ye

var $InvstPeriod , 
    $jstoYr  ,
    rangeyr,
    min_yr ,
    max_yr, 
    from_yr,
    to_yr;

//expected ret
var $rangeRet , 
    $to_ret  ,
    range_ret,
    min_ret ,
    max_ret, 
    from_ret,
    to_ret;






$(document).ready(function(){
   $range = $(".js-range-slider"),
   //$from = $(".js-from"),
    $to = $(".js-to"),
    range,
    min = 1,
    max = 100000, 
    from=1,
    to=1;
    
    
    $InvstPeriod = $('#sldYr'),
   //$from = $(".js-from"),
    $jstoYr = $(".js-toyr"),
    rangeyr,
    min_yr = 1,
    max_yr= 100000, 
    from_yr=1,
    to_yr=1;
    
    
    $rangeRet = $("#ExpRet"),
   //$from = $(".js-from"),
    $to_ret = $(".js-toret"),
    range_ret,
    min_ret = 1,
    max_ret = 100000, 
    from_ret=1,
    to_ret=1;

   

$range.ionRangeSlider({
    type: "single",
    min: min,
    max: max,
    prettify_enabled: false,
    grid: true,
    grid_num: 10,
    onChange: function (data) {
        //from = data.from;
        to = data.from;
        
        updateValues();
    }
});

$('#sldYr').ionRangeSlider({
    type: "single",
    min: 1,
    max: 360,
    prettify_enabled: false,
    grid: true,
    grid_num: 10,
    onChange: function (data) {
        //from = data.from;
        to_yr = data.from;
        
        updateValues_yr();
    }
});



$('#ExpRet').ionRangeSlider({
    type: "single",
    min: 1,
    max: 100,
    prettify_enabled: false,
    grid: true,
    grid_num: 10,
    onChange: function (data) {
        //from = data.from;
        to_ret = data.from;
        
        updateValues_ret();
    }
});
    
    


range = $range.data("ionRangeSlider");


rangeyr = $InvstPeriod.data("ionRangeSlider");
range_ret = $rangeRet.data("ionRangeSlider");


/*$from.on("change", function () {
    from = +$(this).prop("value");
    if (from < min) {
        from = min;
    }
    if (from > to) {
        from = to;
    }

    updateValues();    
    updateRange();
});
*/


$to.on("change", function () {
    to = +$(this).prop("value");
    if (to > max) {
        to = max;
    }
    
    /*if (to < from) {
        to = from;
    }*/

    updateValues();    
    updateRange();
});




$('#jstoYr').on("change", function () {
    to_yr = +$(this).prop("value");
    if (to_yr > max_yr) {
        to_yr = max_yr;
    }
    
    /*if (to < from) {
        to = from;
    }*/

    updateValues_yr();    
    updateRange_yr();
});





$('#jsto-ret').on("change", function () {
    to_ret = +$(this).prop("value");
    if (to_ret > max_ret) {
        to_ret = max_ret;
    }
    
    /*if (to < from) {
        to = from;
    }*/

    updateValues_ret();    
    updateRange_ret();
});



});


var updateValues = function () {
  //$from.prop("value", from);
    $to.prop("value", to);
};



var updateRange = function () {
    range.update({
       from: to
       // to: to
    });
};



var updateValues_yr = function () {
  //$from.prop("value", from);
   // $jstoYr.prop("value", to_yr);
    $('#jstoYr').val(to_yr);
};



var updateRange_yr = function () {
    rangeyr.update({
       from: to_yr
       // to: to
    });
};



var updateValues_ret = function () {
    //$from.prop("value", from);
    // $to_ret.prop("value", to_ret);
    $('#jsto-ret').val(to_ret);
};



var updateRange_ret = function () {
    range_ret.update({
       from: to_ret
       // to: to
    });
};
