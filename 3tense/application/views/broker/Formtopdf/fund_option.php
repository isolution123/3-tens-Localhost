<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-midnightblue">
            <div class="form-group">
                <div class="table-vertical table-responsive">
                    <table border="0" class="table table-striped table-bordered" id="fundTable">
                        <thead>
                        <tr>
                            <th>Policy Number</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if(target == '#fund')
        {
            fund_list();
        }
    });

    //get maturity details in datatable
    function fund_list()
    {
        var policyNum = $('#policy_num').val();
        if(policyNum != '')
            $.ajax({
                //Load data for the table through ajax
                url: '<?php echo site_url('broker/Insurances/get_fund_options');?>',
                type: 'post',
                data: {prem_policy_num: policyNum},
                dataType: 'json',
                success:function(data)
                {
                    $("#fundTable tbody tr").remove();
                    //debugger;
                    if(data.length != 0)
                    {
                        $("#fundTable thead th").remove();
                        var header = "", rows ="" ;
                        header = header+'<th>Fund Option</th>';
                        header = header+'<th>Value</th>';
                        $.each(data, function(key, value) {
                            rows = rows+'<tr><td data-title="fund_option">'+value['fund_option']+'</td><td data-title="value">'+value['value']+'</td></tr>';
                        });
                        
                        $("#fundTable thead tr").append(header);
                    }
                    else
                    {
                        rows = "<tr><td>No Fund Options Available</td></tr>";
                    }
                    $("#fundTable tbody").append(rows);
                    $("#fundTable").DataTable();
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        else
            fundTable.fnClearTable();
    }
</script>