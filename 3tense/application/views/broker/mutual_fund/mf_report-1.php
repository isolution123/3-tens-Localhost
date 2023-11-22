<!-- Done by Dipak
    Date  17-03-2017
    Time  12.10pm
    Live on beta
   -->
<div id="page-content" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <h1>Mutual Fund Report</h1>
        </div>
        <div class="container">
            <form action="#" id="mf_report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h4>Mutual Fund Report Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3 no-border">
                                    <select name="famName" class="populate" multiple="multiple" id="famName" style="width: 100%" tabindex="1">
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-3">
                                    <select name="client_id" class="populate" multiple="multiple" id="client_id" style="width: 100%" tabindex="2">
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                              <div   class="col-md-6">
                                <label class="col-sm-4 control-label"><img src="https://3tense.com/assets/users/images/icon_new.gif" width="25px" height="20px" alt="whats new"/> Detail Reports</label>
                                <div class="col-md-7  no-border">
                                    <select name="type" class="populate" id="type" style="width: 100%" tabindex="3">
                                       <option  selected disabled="true">Select</option>
                                       <option value="clientwise_detail"  >Client-Wise Details</option>
                                       <option value="clientwise_summary">Client-Wise Compressed</option>
                                       <option value="schemewise_detail" selected>Scheme-Wise</option>
                                       <option value="foilowise_summary">Folio-Wise</option>
                                       <option value="sip">SIP </option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-6" id="addon">
                                    <div class="col-md-9 no-border">
                                          <table>
                                            <tr><td><label style="color:blue;">Add-on</label></td><td><input  type="checkbox" id="select_all" value="selet_all"   /> Select All</label></td></tr>
                                            <tr><td style="padding-right:10px;" ><label><input  type="checkbox" id="client_summary" class="select_all" value="client_summary"  name="Client"/> Client Summary</label></td><td style="padding-right:10px;"><label><input  type="checkbox" id="typewise" value="typewise" class="select_all"> Fund  Typewise Summary</label></td></tr>
                                            <tr><td style="padding-right:10px;"><label><input  type="checkbox" id="net_investmet" class="select_all" value="net_investmet"> Net Investment</label></td><td style="padding-right:10px;"><label><input  type="checkbox" id="scheme_summary" value="scheme_summary" class="select_all"> Schemewise Summary</label></tr>
                                          </table>
                                    </div>
                              </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <button type="button" class="btn btn-success ladda-button" tabindex="7" id="mf_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
                            </div>
                            <?php /*<div class="form-group" style="text-align: left">
                                <button type="button" class="btn btn-success ladda-button" tabindex="7" id="mf_aum_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Aum Report</span></button>
                            </div>*/ ?>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<script type="application/javascript">
$('#select_all').on('click', function ()
{
  if (!$(this).hasClass('allChecked'))
  {
    $('.select_all').prop('checked', true);
  }
  else {
    $('.select_all').prop('checked', false);

  }
  $(this).toggleClass('allChecked');
})
    $(function()
    {
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
        /*$("#mf_aum_submit").click(function()
          {
            var ll = Ladda.create(button);
            ll.start();
            $.ajax({
                url: "<?php echo site_url('broker/Mutual_funds/get_mf_aum_report');?>",
                type: 'post',
                dataType: 'json',
                success: function(data)
                {
                    if(data['Status'])
                    {
                        window.open("<?php echo site_url('broker/Reports/get_mf_aum_report');?>", '_blank')
                    }
                    else
                    {
                        bootbox.alert("No Records Found");
                    }
                    ll.stop();
                },
                error: function(jqXRR, textStatus, errorThrown)
                {
                    console.log(jqXRR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });

          })*/
        $("#famName").select2({placeholder: 'Select Family', maximumSelectionSize: 1});
        $("#client_id").select2({placeholder: 'Select Client', maximumSelectionSize: 1});
        $("#mf_submit").click(function(){
          var detail=null;
          var summart=null;
            var l = Ladda.create(button);
            l.start();
            var famName = $('#famName').val();
            var client_id = $('#client_id').val();
            var client_id = $('#client_id').val();
            // var client_summary=$('#client_summary').val();
          var  client_summary;
          var  typewise;
          var  scheme_summary;
          var  net_investmet;
            if($('#client_summary').prop("checked") == true)
            {
               client_summary=$('#client_summary').val();
            }
            if($('#typewise').prop("checked") == true)
            {
             typewise=$('#typewise').val();
            }
            if($('#scheme_summary').prop("checked") == true)
            {
             scheme_summary=$('#scheme_summary').val();
            }
            if($('#net_investmet').prop("checked") == true)
            {
               net_investmet=$('#net_investmet').val();
            }







            var type = $('#type').val();
            // alert(type);
            // if($('#client_wise').is(':checked'))
            // {
            //   var main_type='client';
            //   if($('#detail').is(':checked'))
            //    {  var sub_type="detail"; }
            //    else
            //    { var sub_type="summary";}
            // }
            //
            // else if($('#scheme_wise').is(':checked'))
            //  {
            //     //  var scheme_wise="checked";
            //     var main_type='scheme';
            //      if($('#detail').is(':checked'))
            //       {  var sub_type="detail"; }
            //       else
            //       {var sub_type="summary";}
            //   }
            //
            //  else
            //  {
            //    var main_type='folio';
            //    if($('#detail').is(':checked'))
            //     {  var sub_type="detail"; }
            //     else
            //     {var sub_type="summary";}
            //  }



            if ((famName != null && famName.length != 0) || (client_id != null && client_id.length != 0)) {
                $.ajax({
                    url: "<?php echo site_url('broker/Mutual_funds/get_mf_report');?>",
                    type: 'post',
                    data: {'famName': famName, 'client_id': client_id,'type':type,'client_summary':client_summary,'typewise':typewise,'scheme_summary':scheme_summary,'net_investmet':net_investmet},
                    dataType: 'json',
                    success: function(data)
                    {
                        if(data['Status'])
                        {
                            window.open("<?php echo site_url('broker/Reports/get_mf_report');?>", '_blank')
                        }
                        else
                        {
                            bootbox.alert("No Records Found");
                        }
                        l.stop();
                    },
                    error: function(jqXRR, textStatus, errorThrown)
                    {
                        console.log(jqXRR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            else
            {
                bootbox.alert("Please select either family or client to view Reports");
                l.stop();
            }
        });


        //on family change get clients
        $('#famName').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', 'nominee', "", "");
        });
        
        
        //on change event of report types - 2017-05-26
        $('#type').change(function()
        {
          var $this = $(this);
              value = $this.val();
              /*if(value=='capital_gain')
              {
                $('#capital_gain_date').show();
                var cur_year = new Date().getFullYear();
                $('#from_date').datepicker({format:"dd/mm/yyyy"});
                $('#to_date').datepicker({format:"dd/mm/yyyy"});
                // $('#addon').hide();
              }
              else {
                $('#capital_gain_date').hide();
                $('#addon').show();
              }*/
              
              //added by Akshay Karde - 2017-05-23 - for disabling Add-ons checkboxes for SIP report
               if(value == 'sip') {
                   //$('input[type=checkbox]').attr('disabled','true');
                   $('#addon').hide();
               } else {
                   $('#addon').show();
               }
        });    
    
    });
</script>
