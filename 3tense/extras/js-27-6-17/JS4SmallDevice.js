/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var win;

$(window).on('resize', function() {
   win = $(this);
  if (win.width() <= 980) {

    $('div.mblSize').removeClass('col-sm-3');
    $('div.mblSize').addClass('col-sm-6');
    
     $('div.mblsm2').removeClass('col-sm-2');
    $('div.mblsm2').addClass('col-sm-3');
   // $('a.tooltipFilter').addClass('resizeTextbox');
    
    $('#resultSIP_Delay').addClass('smallResultDisplay');
    
    
    $('#DIVresult').removeClass('setBigMargnResultDiv');
    $('#DIVresult').addClass('setSmallMargnResultDiv');
    

  }
  else
  {
    $('div.mblSize').removeClass('col-sm-6');
    $('div.mblSize').addClass('col-sm-3');
     $('div.mblsm2').removeClass('col-sm-3');
    $('div.mblsm2').addClass('col-sm-3');
   
     $('a.tooltipFilter').removeClass('resizeTextbox');
    
    $('#resultSIP_Delay').removeClass('smallResultDisplay');
    
    
    $('#DIVresult').removeClass('setSmallMargnResultDiv');
     $('#DIVresult').addClass('setBigMargnResultDiv');
    
    
  }
  
  
});