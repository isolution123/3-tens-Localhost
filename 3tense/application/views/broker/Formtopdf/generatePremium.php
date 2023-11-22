<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div class="container">
        <div class="row">
            <table>
                <tr>
                    <td>Policy Number</td>
                    <td><input type="text" Id="policyNo" name="policyNo"/></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td><input type="text" Id="sDate" name="sDate"/></td>
                    <td>dd/MM/YYYY</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td><input type="text" Id="eDate" name="eDate"/></td>
                    <td>dd/MM/YYYY</td>
                </tr>
                <tr>
                    <td>Premium Mode</td>
                    <td><input type="text" Id="pMode" name="pMode"/></td>
                    <td>Anually, Half-Yearly, Quarter, Monthy</td>
                </tr>
                <tr>
                    <td>Premium Amt</td>
                    <td><input type="text" Id="pAmt" name="pAmt"/></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="button" id="premSubmit" text="Submit" value="Submit" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div Id="ajaxDiv"></div>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(function(){
        $('#premSubmit').click(function(){
            if($('#policyNo').val() != '' && $('#sDate').val() != '' && $('#eDate').val() != '' && $('#pMode').val() != '' && $('#pAmt').val() != '')
            {
                $.ajax({
                    url: "<?php echo site_url('broker/Insurances/temp_premium_add')?>",
                    type:'post',
                    data:{pol_num: $('#policyNo').val(), s_date: $('#sDate').val(), e_date: $('#eDate').val(), pMode: $('#pMode').val(), pAmt: $("#pAmt").val()},
                    dataType: 'json',
                    success:function(data)
                    {
                        $("#ajaxDiv").html('Premium Inserted');
                    },
                    error:function(data)
                    {
                        console.log(data);
                    }
                });
            }
            else
            {
                bootbox.alert("Enter Proper Values");
            }
        });
    });
</script>

