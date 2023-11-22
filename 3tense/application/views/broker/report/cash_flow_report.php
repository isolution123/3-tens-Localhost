<div id="page-content" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <h1>Cash Flow Report</h1>
        </div>
        <div class="container">
            <form action="#" id="cash_flow_report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h4>Cash Flow Report Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3  no-border">
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
                            <div class="form-group" style="text-align: center">
                                <label class="col-sm-2 control-label">FROM Date</label>
                                <div class="col-md-3">
                                    <input type="text" tabindex="3" name="from_date" required="required" class="form-control date mask" data-inputmask="'alias':'date'" id="from_date" value="<?php echo date('d/m/Y');?>">
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <label class="col-sm-2 control-label">TO Date</label>
                                <div class="col-md-3">
                                    <input type="text" tabindex="4" name="to_date" required="required" class="form-control date mask" data-inputmask="'alias':'date'" id="to_date" value="<?php echo date('d/m/Y');?>">
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <button type="button" class="btn btn-success ladda-button" tabindex="5" id="cash_flow_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(function() {
        var cur_year = new Date().getFullYear();
        $('#from_date').datepicker({format:"dd/mm/yyyy",startDate:"01/01/"+cur_year});
        $('#to_date').datepicker({format:"dd/mm/yyyy"});
        $('.mask').inputmask();
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
        $("#famName").select2({placeholder: 'Select Family', maximumSelectionSize: 1});
        $("#client_id").select2({placeholder: 'Select Client', maximumSelectionSize: 1});
        $("#cash_flow_submit").click(function(){
            var l = Ladda.create(button);
            l.start();
            var famName = $('#famName').val();
            var client_id = $('#client_id').val();
            var from = $('#from_date').val();
            var to = $('#to_date').val();
            if ((famName != null && famName.length != 0) || (client_id != null && client_id.length != 0)) {
                if((from != null && from.length != 0) && (to != null && to.length != 0)) {
                    $.ajax({
                        url: "<?php echo site_url('broker/Final_reports/get_cash_flow_report');?>",
                        type: 'post',
                        data: {'famName': famName, client_id: client_id, from_date: from, to_date: to},
                        dataType: 'json',
                        success: function(data)
                        {
                            if(data['Status'])
                            {
                                window.open("<?php echo site_url('broker/Reports/get_cash_flow_report');?>", '_blank')
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
                } else {
                    bootbox.alert("Please select both fields - FROM Date and TO Date");
                    l.stop();
                }
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
    });
</script>