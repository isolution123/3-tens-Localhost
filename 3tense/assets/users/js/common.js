$(function(){
    Ladda.bind( 'input[type=button]' );
});

function addNewForm()
{
    disableBtn();
    $("input[type=text], input[type=hidden], textarea").val("");
    $("select").val("");
    $(".populate").select2("val", "");
}

function disableBtn()
{
    $(".control-form :input").prop("disabled", false);
    $(".control-form .btn").attr("disabled", false);
    $(".dataTables_wrapper .btn").attr('disabled', false);
    $("#add").attr('disabled', true);
    $("#edit").attr('disabled', true);
    $("#delete").attr('disabled', true);
    $("#save").attr('disabled', false);
    $("#cancel").attr('disabled', false);
    //added by Salmaan - 16/02/16
    $(".disabled").attr('disabled',true);
}

function enableBtn()
{
    $(".control-form :input").prop("disabled", true);
    $(".control-form .btn").attr("disabled", true);
    $(".dataTable .btn").attr('disabled', true);
    $(".dataTables_wrapper input, .dataTables_wrapper select").prop("disabled", false);
    $("#add").attr('disabled', false);
    $("#edit").attr('disabled', false);
    $("#delete").attr('disabled', false);
    $("#save").attr('disabled', true);
    $("#cancel").attr('disabled', true);
    //added by Salmaan - 16/02/16
    $(".disabled").attr('disabled',true);
}

//parse date in jquery default format. Helpful for comparing dates
function process(date){
    var parts = date.split("/");
    return new Date(parts[2], parts[1] - 1, parts[0]);
}

//add years to any date - used for PPT in Insurance
function addYears(startDate, years, type) {
    if( /^\d{2}\/\d{2}\/\d{4}$/i.test( startDate ) ) {

        var parts = startDate.split("/");

        var day = parts[0] && parseInt( parts[0], 10 );
        var month = parts[1] && parseInt( parts[1], 10 );
        var year = parts[2] && parseInt( parts[2], 10 );
        var duration = parseInt( years, 10);

        if( day <= 31 && day >= 1 && month <= 12 && month >= 1 ) {

            var expiryDate = new Date( year, month - 1, day );
            expiryDate.setFullYear( expiryDate.getFullYear() + duration );

            var day = ( '0' + expiryDate.getDate() ).slice( -2 );
            var month = ( '0' + ( expiryDate.getMonth() + 1 ) ).slice( -2 );
            if(type == "ppt") {
                var year = (expiryDate.getFullYear()-1); //subtract 1 year for premium calculations
            } else {
                var year = (expiryDate.getFullYear());
            }

            //$("#paidup_date").val( day + "/" + month + "/" + year );
            return day + "/" + month + "/" + year;

        } else {
            // display error message or return false
            return false;
        }
    }
}

function getYearDiff(startDate, endDate, type)
{
    if( (/^\d{2}\/\d{2}\/\d{4}$/i.test( startDate )) && (/^\d{2}\/\d{2}\/\d{4}$/i.test( endDate )) ) {
        //parse the startDate
        var parts1 = startDate.split("/");
        var day1 = parts1[0] && parseInt( parts1[0], 10 );
        var month1 = parts1[1] && parseInt( parts1[1], 10 );
        var year1 = parts1[2] && parseInt( parts1[2], 10 );
        var newStartDate = new Date( year1, month1 - 1, day1 );
        startYear = newStartDate.getFullYear();
        startMonth = newStartDate.getMonth();
        startDay = newStartDate.getDate();

        //parse the endDate
        var parts2 = endDate.split("/");
        var day2 = parts2[0] && parseInt( parts2[0], 10 );
        var month2 = parts2[1] && parseInt( parts2[1], 10 );
        var year2 = parts2[2] && parseInt( parts2[2], 10 );
        if(type == "ppt") {
            var newEndDate = new Date( year2+1, month2 - 1, day2 ); //add 1 year for premium calculations
            //var newEndDate = new Date( year2, month2 - 1, day2 ); //add 1 year for premium calculations
        } else {
            var newEndDate = new Date( year2, month2 - 1, day2 );
        }
        endYear = newEndDate.getFullYear();
        endMonth = newEndDate.getMonth();
        endDay = newEndDate.getDate();

        diff = endYear - startYear;

        if (endMonth < startMonth - 1)
        {
            diff--;
        }

        if (startMonth - 1 == endMonth && endDay < startDay)
        {
            diff--;
        }
        return diff;
    } else {
        return false;
    }
}

function addForm(url)
{
    location.href = url;
}

//add month to date
function addMonth(date, month)
{
    /*var d = new Date(date);
    d.setMonth(d.getMonth() + month);
    return d.getDate() + '/' + (d.getMonth() + month) + '/' + d.getFullYear();*/
    var dString = date.split('/');

    var dt = new Date(dString[2],dString[1]-1,dString[0]);
    dt.setMonth(dt.getMonth()+month);

    return pad(dt.getDate(),2) + "/" + pad(dt.getMonth()+1,2) + "/" + dt.getFullYear();
}

function pad(number, length) {

    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}

function editForm(url, id)
{
    if(id != '' && id != 0)
    {
        location.href = url+'?id='+id;
    }
    else
    {
        $.pnotify({
            title: 'Error!',
            text: 'Please Save Your Data first then Edit',
            type: 'error',
            hide: true
        });
    }
}

function errorSaveFirst()
{
    $.pnotify({
        title: 'Error!',
        text: 'Please Save Your Data first.',
        type: 'error',
        hide: true
    });
}

function getFamilies(url)
{
    $.ajax({
        url: url,
        type:'post',
        data: {},
        dataType: 'json',
        success:function(data)
        {
            var option = '<option disabled selected value="">Select Family</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].family_id+">"+data[i].name+"</option>";
            });
            $("#family_id").html(option).select2("val", "");
            $("[name='family_id']").html(option).select2("val", "");
            /*if($("[name='family_id']").is("select")) {
                $("[name='family_id']").select2('val',$("[name='family_id'] option:last-child").val());
            }*/
            //Pallavi - 2017-06-12
            if($("[name='family_id']").is("select")) {
                $("[name='family_id']").select2('val',$("[name='family_id'] option:first-child").val());
            }
            $("#select-family").html(option).select2("val", "");
            $("[name='tansfer_family_id']").html(option).select2("val", "");
            if($("[name='tansfer_family_id']").is("select")) {
                $("[name='tansfer_family_id']").select2('val',$("[name='tansfer_family_id'] option:first-child").val());
            }
        },
        error: function (data)
        {
            console.log(data);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details from family',
                type: 'error',
                hide: true
            });
        }
    });
}

function getAdvisers(url)
{
    $.ajax({
        url: url,
        type:'post',
        data: {},
        dataType: 'json',
        success:function(data)
        {
            var option = '<option disabled selected value="">Select Advisor</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].adviser_id+">"+data[i].adviser_name+"</option>";
            });
            $("#adv_id").html(option).select2("val", "");
        },
        error: function (data)
        {
            console.log(data);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details from advisers',
                type: 'error',
                hide: true
            });
        }
    });
}

function getPremTypes(url)
{
    $.ajax({
        url: url,
        type:'post',
        data: {},
        dataType: 'json',
        success:function(data)
        {
            var option = '<option disabled selected value="">Select Asset/Premium</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].prem_type_id+">"+data[i].prem_type_name+"</option>";
            });
            $("#prem_type").html(option).select2("val", "");
        },
        error: function (data)
        {
            console.log(data);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details from advisers',
                type: 'error',
                hide: true
            });
        }
    });
}

function getClients(url, familyValue, clientID, nomineeID, clientValue, nomineeValue)
{
    $.ajax({
        url: url,
        type:'post',
        data: {familyID: familyValue},
        dataType: 'json',
        beforeSend:function()
        {
            var option = '<option disabled selected value="">Select Client</option>';
            //$("#client_id").html(option);
        },
        success:function(data)
        {
            var option = '<option disabled selected value="">Select Client</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].client_id+">"+data[i].name+"</option>";
            });
            if(clientID != "")
            {
                $("#"+clientID).html(option);
                if(clientValue != "")
                {
                    $("#"+clientID).val(clientValue).select2({maximumSelectionSize: 1});
                } else {
                    $("#client_id").select2("val","");
                }
            }
            if(nomineeID != "")
            {
                $("#"+nomineeID).html(option);
                $("#"+nomineeID+" option[value='']").remove();
                $("#"+nomineeID).prepend("<option disabled selected value='' selected>Select Nominee</option>");
                if(nomineeValue != "")
                {
                    $("#"+nomineeID).val(nomineeValue);
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details from family',
                type: 'error',
                hide: true
            });
        }
    });
}

/* By:pallavi*/
function getClientmerge(url, familyValue, clientID, mr)
{

    $.ajax({
        url: url,
        type:'post',
        data: {familyID: familyValue,merge:mr},
        dataType: 'json',
        beforeSend:function()
        {
            var option = '<option disabled  value="">Select Client</option>';
            //$("#client_id").html(option);
        },
        success:function(data)
        {
          //  alert(data);
            var option = '<option disabled value="">Select Client</option>';
            $.each(data, function(i, item){
                option = option + "<option value="+data[i].client_id+">"+data[i].name+"</option>";
            });
             $("#client_id").html(option);
               $("#client_id").select2("val","");
          },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            $.pnotify({
                title: 'Error!',
                text: 'Error getting details from family',
                type: 'error',
                hide: true
            });
        }
    });
}

//for merging
// function getClientsFamilywise(url, familyValue){
//    //alert(familyValue);
//     //alert(url);
// }

function getClientmergebyclient(url, familyValue, clientID, mr){
//  alert(familyValue);
  $.ajax({
      url: url,
      type:'post',
      data: {familyID: familyValue,clientID: clientID,merge:mr},
      dataType: 'json',
      beforeSend:function()
      {
          var option = '<option disabled  value="">Select Client</option>';
          //$("#client_id").html(option);
      },
      success:function(data)
      {
        //  console.log(data);

         var temp='',tt='',merged_client='';
         var option = '<option disabled value="">Select Client</option>';
          $.each(data, function(i, item){
           if(data[i].merge_ref_id!=null){
        //     //  alert(data[i].merge_ref_id);
                temp="selected";
               tt=tt+','+data[i].name;
               merged_client=data[i].merge_ref_id;
             }else{
               temp="";
             }

               option = option + "<option "+temp+" value="+data[i].client_id+">"+data[i].name+"</option>";
          });
        // //  alert(merged_client);
         tt = tt.replace(',','');//for removing first comma.
           $("#client_id").html(option);
          $("#mts").val(tt);
        //   $("#merg_client_id").select2().select2('val',merged_client);
        //   $("#pre_merge_with").val(merged_client);


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
          $.pnotify({
              title: 'Error!',
              text: 'Error getting details from family',
              type: 'error',
              hide: true
          });
      }
  });
}
/* end pallavi */

//function to export report to pdf
function export_to_pdf(actionUrl)
{
    var titleData = $("#title_data").html();
    var htmlData = $("#report_data").html();
    var logo = $("#logo").val();

    $("#titleData").val(titleData);
    $("#htmlData").val(htmlData);

    $("#report_form").attr("action", actionUrl);
    $("#report_form").submit();
}

//function to export report to excel file
function export_to_excel(actionUrl)
{
    var titleData = $("#title_data").html();
    var htmlData = $("#report_data").html();
    var name = $("#name").val();
    console.log(titleData);
    console.log(htmlData);
    console.log(name);

    $("#titleData").val(titleData);
    $("#htmlData").val(htmlData);
    $("#report_form").attr("action", actionUrl);
    $("#report_form").submit();
}