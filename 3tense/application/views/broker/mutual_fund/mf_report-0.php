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
                                <button type="button" class="btn btn-success ladda-button" tabindex="3" id="mf_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
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
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });
        $("#famName").select2({placeholder: 'Select Family', maximumSelectionSize: 1});
        $("#client_id").select2({placeholder: 'Select Client', maximumSelectionSize: 1});
        $("#mf_submit").click(function(){
            var l = Ladda.create(button);
            l.start();
            var famName = $('#famName').val();
            var client_id = $('#client_id').val();
            if ((famName != null && famName.length != 0) || (client_id != null && client_id.length != 0)) {
                $.ajax({
                    url: "<?php echo site_url('broker/Mutual_funds/get_mf_report');?>",
                    type: 'post',
                    data: {'famName': famName, client_id: client_id},
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
    });
</script>