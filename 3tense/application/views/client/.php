
<div id="page-content" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <h1>Mutual Fund Report</h1>
        </div>
        <div class="container">
            <form action="#" id="mf_report_form" method="post" class="form-horizontal row-border">
              <!-- Client & Family Report               -->
                <div class="panel panel-midnightblue" >
                    <div class="panel-heading" id="client_header">
                        <h4>Mutual Fund Report Info [ Client & Family ]</h4>
                    </div>
                    <div class="panel-body collapse in" id="client_div">
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
                                <label class="col-sm-4 control-label">Report Type</label>
                                <div class="col-md-7  no-border">
                                    <select name="type" class="populate" id="type" style="width: 100%" tabindex="3" >
                                       <!-- <option  selected disabled="true">Select</option> -->
                                       <option value="clientwise_detail"  >Clientwise Detail</option>
                                       <option value="clientwise_summary">Clientwise Summary</option>
                                       <?php
                                        //show yearwise report option only to iSolutions and V-Financial services
                                        if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174' || $this->session->userdata('broker_id')=='0196')
                                        {
                                            echo '<option value="clientwise_summary_by_year">Yearwise Summary</option>';
                                        }
                                       ?>
                                       
                                       <option value="schemewise_detail" selected>Schemewise Detail</option>
                                       <option value="foilowise_summary">Foliowise Summary</option>
                                       <option value="sip">SIP </option>
                                       <option value="folio_master" id='folio_master'>Folio Details</option>
                                       <!--<option value="capital_gain" id='capital_gain'></option>-->
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-6 addon" id="addon">
                                    <div class="col-md-9 no-border">
                                          <table>
                                            <tr>
                                                <td><label style="color:blue;">Add-on</label></td><td><label><input  type="checkbox" id="select_all" value="selet_all" tabindex="4"   /> Select All</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right:10px;" >
                                                    <label>
                                                        <input  type="checkbox" id="client_summary" class="select_all" value="client_summary"  name="Client"/> Client Summary
                                                    </label>
                                                </td>
                                                <td style="padding-right:10px;">
                                                    <label><input  type="checkbox" id="typewise" value="typewise" class="select_all"> Fund  Typewise Summary</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right:10px;">
                                                    <label><input  type="checkbox" id="net_investmet" class="select_all" value="net_investmet"> Net Investment</label>
                                                </td>
                                                <td style="padding-right:10px;">
                                                    <label>
                                                        <input  type="checkbox" id="scheme_summary" value="scheme_summary" class="select_all"> Scheme / AMC wise Summary
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php 
                                                if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174' || $this->session->userdata('broker_id')=='0196')
                                                {
                                            ?>
                                            <tr>
                                                <td style="padding-right:10px;">
                                                    <label><input  type="checkbox" id="hide_nav_date" class="" value="hide_nav_date"> Hide NAV date</label>
                                                </td>
                                                 <td style="padding-right:10px;">
                                                    <label>
                                                        <input  type="checkbox" id="cap_detail" value="cap_detail" class="select_all"> Cap Detail
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php 
                                                }
                                            ?>
                                          </table>
                                    </div>
                              </div>
                            </div>
                          
                             <div class="form-group" id="nav_date_section">
                                <div class='col-md-8' >
                                  <div class="form-group" style="text-align: center">
                                    <label class="col-md-3 control-label">Date</label>

                                    <div class="col-md-3">
                                        <input type="text"  name="reportDate"  tabindex="5" 
                                          required="required" class="form-control date mask" 
                                          data-inputmask="'alias':'date'" 
                                        id="reportDate" value="<?php echo date('d/m/Y');?>">
                                    </div>
                                  </div>
                                </div>
                              </div>
                          
                            <div class="form-group" id="capital_gain_date">
                              <div class='col-md-8' id="">
                                <div class="form-group" style="text-align: center">
                                    <label class="col-md-3 control-label">FROM Date</label>
                                    <div class="col-md-3">
                                        <input type="text"  name="from_date"  tabindex="5" required="required" class="form-control date mask" data-inputmask="'alias':'date'" id="from_date" value="<?php echo date('d/m/Y');?>">
                                    </div>
                                    <label class="col-sm-3 control-label">TO Date</label>
                                    <div class="col-md-3">
                                        <input type="text"  name="to_date"  tabindex="6" required="required" class="form-control date mask" data-inputmask="'alias':'date'" id="to_date" value="<?php echo date('d/m/Y');?>">
                                    </div>
                                </div>
                            </div>
                          </div>
                            <div class="form-group col-md-6" style="text-align: center">
                                <button type="button" class="btn btn-success ladda-button" tabindex="7" id="mf_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Broker Report -->
                <div class="panel panel-midnightblue">
                    <div class="panel-heading" id="broker_header">
                        <h4>Mutual Fund Report Info [ Broker ] </h4>
                    </div>
                    <div class="panel-body collapse in" id="broker_div">
                        <div class="row">
                            <div class="form-group">
                              <div   class="col-md-6">
                                <label class="col-sm-4 control-label">Report Type</label>
                                <div class="col-md-7 no-border">
                                    <select name="broker_rep_type" class="populate" id="broker_rep_type" style="width: 100%" tabindex="8" >
                                       <option selected value="aum_report">AUM Report</option>
                                       <?php
                                        //show yearwise report option only to iSolutions and V-Financial services
                                        if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174' || $this->session->userdata('broker_id')=='0196')
                                        {
                                            echo '<option value="all_summary_report">Detail AUM Report</option>';
                                            echo '<option value="family_aum_report">Family AUM Report</option>';
                                           echo '<option value="detail_aum_report">RTA AUM Report</option>';
                                        }
                                       ?>
                                        <!--echo '<option value="detail_aum_report">Detail Valuation AUM Report</option>';-->
                                       
                                     </select>
                                </div>
                              </div>
                              <div class="col-md-1">
                                  <div class="form-group" style="text-align: center">
                                    <button type="button" class="btn btn-success ladda-button" tabindex="9" id="broker_submit" data-style="expand-right">
                                    <i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
                                </div>

                              </div>
                            </div>


                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<script type="application/javascript">

    $("#client_header").click(function()
    {
        $("#client_div").slideToggle();
        $("#broker_div").show();
    });


 $('#reportDate').datepicker({
    format:"dd/mm/yyyy", 
    endDate:new Date(),
    startDate:"01/05/2017" });


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


        $("#broker_submit").click(function()
        {

          var report_type=$("#broker_rep_type").val();
            var ll = Ladda.create(button);
            ll.start();
            $.ajax({
                url: "<?php echo site_url('broker/Mutual_funds/get_mf_broker_report');?>",
                data:{'report_type':report_type},
                type: 'post',
                dataType: 'json',
                success: function(data)
                {
                    if(data['Status'])
                    {
                        window.open("<?php echo site_url('broker/Reports/get_mf_broker_report');?>", '_blank')
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
        })
        $("#broker_rep_type").select2({placeholder: 'Select Type', maximumSelectionSize: 1});
        $("#type").select2({placeholder: 'Select Type', maximumSelectionSize: 1});
        $("#famName").select2({placeholder: 'Select Family', maximumSelectionSize: 1});
        $("#client_id").select2({placeholder: 'Select Client', maximumSelectionSize: 1});
        
        $("#mf_submit").click(function()
        {
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
          var hide_nav_date;
          var  net_investmet;
          var  cap_detail;
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
            if($('#hide_nav_date').prop("checked") == true)
            {
               hide_nav_date=$('#hide_nav_date').val();
            }
            if($('#cap_detail').prop("checked") == true)
            {
                cap_detail=$('#cap_detail').val();
            }
            // var from_date = $("#from_date").datepicker("getDate");

            var from_date = $('#from_date').datepicker({ dateFormat: 'dd-mm-yy' }).val();
            var to_date = $('#to_date').datepicker({ dateFormat: 'dd-mm-yy' }).val();
            var type = $('#type').val();

            var reportDate = $('#reportDate').datepicker({ dateFormat: 'dd-mm-yy' }).val();



            if ((famName != null && famName.length != 0) || (client_id != null && client_id.length != 0))
            {

                  $.ajax({
                      url: "<?php echo site_url('broker/Mutual_funds/get_mf_report_historical');?>",
                      type: 'post',
                      data: {'famName': famName, 
                            'client_id': client_id,
                            'type':type,
                            'client_summary':client_summary,
                            'typewise':typewise,
                            'scheme_summary':scheme_summary,
                            'net_investmet':net_investmet,
                            'hide_nav_date':hide_nav_date,
                            'cap_detail':cap_detail,
                            'from_date':from_date,
                            'to_date':to_date,
                            
                      'reportDate':reportDate},
                      dataType: 'json',
                      success: function(data)
                      {
                          if(data['Status'])
                          {
                              window.open("<?php echo site_url('broker/Reports/get_mf_report');?>", '_blank');
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
          //  }
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





        $('#capital_gain_date').hide();
        $('#type').change(function()
        {
          var $this = $(this);
              value = $this.val();

              if(value=='folio_master')
              {
                 $('#addon').hide();
                 $('#nav_date_section').hide();
              }
              else if(value=='sip')
              {
                $('#addon').hide();
                $('#nav_date_section').show();
               }
               else {
                 $('#addon').show();
                 $('#nav_date_section').show();
               }

     
              if(value=='capital_gain')
              {
              $('#nav_date_section').hide();
               $('#capital_gain_date').show();
                var cur_year = new Date().getFullYear();
                $('#from_date').datepicker({format:"dd/mm/yyyy"});
                $('#to_date').datepicker({format:"dd/mm/yyyy"});
                }
              else {
                $('#capital_gain_date').hide();

               }
        });
    });
</script>

