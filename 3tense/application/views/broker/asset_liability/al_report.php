<div id="page-content" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <h1>Asset & Liability Report</h1>
        </div>
        <div class="container">
            <form action="#" id="ins_report_form" method="post" class="form-horizontal row-border">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h4>Asset & Liability Report Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3  no-border">
                                    <select name="famName" class="populate" multiple="multiple" id="famName" style="width: 100%" tabindex="1">
                                         <?php
                                        //show yearwise report option only to iSolutions and V-Financial services
                                        if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0009' || $this->session->userdata('broker_id')=='0174' || $this->session->userdata('broker_id')=='0196')
                                        {
                                            echo '<option value="0">ALL</option>';
                                        }
                                       ?>
                                        
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <button type="button" class="btn btn-success ladda-button" tabindex="3" id="ins_submit" data-style="expand-right"><i class="fa fa-file-text-o"></i> <span class="ladda-label">Generate Report</span></button>
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
        $("#ins_submit").click(function(){
            var l = Ladda.create(button);
            l.start();
            var famName = $('#famName').val();
            if ((famName != null && famName.length != 0)) {
                $.ajax({
                    url: "<?php echo site_url('broker/Assets_liabilities/get_al_report');?>",
                    type: 'post',
                    data: {'famName': famName},
                    dataType: 'json',
                    success: function(data)
                    {
                        if(data['Status'])
                        {
                            window.open("<?php echo site_url('broker/Reports/get_al_report');?>", '_blank')
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
                bootbox.alert("Please select either family to view Reports");
                l.stop();
            }
        });
    });
</script>