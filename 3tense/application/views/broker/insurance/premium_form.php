<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-midnightblue">
            <div class="form-group">
                <button class="btn btn-success" style="margin-bottom: 10px" href="javascript:;" tabindex="1" onclick="check_ins()"><i class="fa fa-plus"></i> Add New Premium</button>
                <div class="table-responsive">
                    <table border="0" class="table table-striped table-bordered" id="premTable">
                        <thead>
                        <tr>
                            <th>Action</th>
                            <th>Premium ID</th>
                            <th>Bank Name</th>
                            <th>Branch</th>
                            <th>Account Number</th>
                            <th>Cheque Number</th>
                            <th>Cheque Date</th>
                            <th>Amount</th>
                            <th>Adjustment</th>
                            <th>Adj (Source)</th>
                            <th>Advisor</th>
                            <th>Narration</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <button class="btn btn-sm btn-danger" onclick="delete_prem()" tabindex="2" style="margin-top: 20px">
                    <i class="fa fa-trash-o"></i> Delete Last Premium
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap modal -->
<div class="modal fade" id="premium_modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="add_prem_form" class="form-horizontal" data-validate="parsley">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input name="premClientID" id="premClientID" type="hidden">
                                <input name="premium_id" id="premium_id" type="hidden">
                                <input name="premium_mode" id="premium_mode" type="hidden">
                                <input name="branch" id="branch" type="hidden">
                                <label class="control-label col-md-3">Policy Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="policy_number" id="policy_number" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Cheque Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="cheque_number" id="cheque_number">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Cheque Date</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control date" name="cheque_date" data-inputmask="'alias':'date'" required="required" id="cheque_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Bank Name</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="bank_id" id="bank_id">
                                        <option disabled selected>Select Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Branch Name</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="premBranch" id="premBranch">
                                        <option disabled selected>Select Branch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Account Number</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="account_number" id="account_number">
                                        <option disabled selected>Select Account Number</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-3">Amount</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="premium_amount" required="required" id="premium_amount">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Advisor</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="advisers" required="required" id="advisers">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Adjustment</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="premAdjust" id="premAdjust">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Adjustment Ref. No.</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="adjustment_ref_number" id="adjustment_ref_number">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Narration</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="narration" id="narration">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Next Premium Due Date</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control date" name="next_premium_due_date" data-inputmask="'alias':'date'" required="required" id="next_premium_due_date">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border:0px !important;">
                <button type="button" id="btnSave" onclick="save_prem()" class="btn btn-primary">Save Premium</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="application/javascript">
    var premTable = null;
    var save_method = null;
    var graceDueDate = $("#grace_due_date").val();
    $(document).ready(function() {
        /*if(premTable != null)
            premTable.destroy();
        premTable = premium_list();*/
        //$('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
        //$('#next_premium_due_date').datepicker('destroy');
        //$('#next_premium_due_date').datepicker({format: 'dd/mm/yyyy', yearRange: '-0:+100', minDate: new Date()});
        //$('#next_premium_due_date').datepicker('option', 'minDate', new Date());
        //commented out the below line and added new below that, to allow brokers to set next date even as past date - Salmaan
        //$( "#next_premium_due_date" ).datepicker({format: 'dd/mm/yyyy', startDate: '+1d'});
        $( "#next_premium_due_date" ).datepicker({format: 'dd/mm/yyyy', startDate: $("#commence_date").val()});
        $( "#cheque_date" ).datepicker({format: 'dd/mm/yyyy', startDate: $("#commence_date").val()});


        $('#bank_id').change(function(){
            $.ajax({
                url: '<?php echo site_url("broker/Banks/get_branch");?>',
                type: 'post',
                data: {bankID: this.value, clientID: $('#client_id').val()},
                dataType: 'json',
                success:function(data)
                {
                    var option = '<option disabled selected>Select Branch</option>';
                    $.each(data['branches'], function(i, item){
                        option = option + "<option value='"+item.branch+"'>"+item.branch+"</option>";
                    });
                    $("#premBranch").html(option);
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    bootbox.alert("Something went terribly wrong");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });

        $('#premBranch').change(function(){
            $.ajax({
                url: '<?php echo site_url("broker/Banks/get_account_num");?>',
                type: 'post',
                data: {bankID: $('#bank_id').val(), clientID: $('#client_id').val(), branch: this.value},
                dataType: 'json',
                success:function(data)
                {
                    var option = '<option disabled selected>Select Account Number</option>';
                    $.each(data['acc_num'], function(i, item){
                        //debugger;
                        option = option + "<option value='"+item.account_number+"'>"+item.account_number+"</option>";
                    });
                    $("#account_number").html(option);
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        });
    });
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if(target == '#premium')
        {
            if(premTable != null)
                premTable.destroy();
            premTable = premium_list();
        }
    });
    function add_prem_form(data)
    {
        save_method = 'add';
        var mode = $('#mode option:selected').text();
        var nextPremiumDate = $('#next_prem_due_date').val();
        graceDueDate = $('#grace_due_date').val();
        if(mode == 'Annually') {
            nextPremiumDate = addMonth(nextPremiumDate, 12);
            graceDueDate = addMonth(graceDueDate, 12);
        }
        else if(mode == 'Half-Yearly') {
            nextPremiumDate = addMonth(nextPremiumDate, 6);
            graceDueDate = addMonth(graceDueDate, 6);
        }
        else if(mode == 'Quarterly') {
            nextPremiumDate = addMonth(nextPremiumDate, 3);
            graceDueDate = addMonth(graceDueDate, 3);
        }
        else if(mode == 'Monthly') {
            nextPremiumDate = addMonth(nextPremiumDate, 1);
            graceDueDate = addMonth(graceDueDate, 1);
        }

        //reset form on modals
        $("#add_prem_form")[0].reset();
        $("#premClientID").val($('#client_id').val());
        $('#advisers').val($('#adv_id option:selected').text());
        $('#policy_number').val($('#policy_num').val());
        $('#next_premium_due_date').datepicker('setDate', nextPremiumDate);
        $('#grace_due_date').val(graceDueDate);
        $('#premium_amount').val($('#prem_amt').val());
        $('#cheque_date').datepicker('setDate', $('#next_prem_due_date').val());
        $('#premium_mode').val(mode);
        var option = '<option disabled selected>Select Bank</option>';
        $.each(data, function(i, item){
            option = option + "<option value="+data[i].bank_id+">"+data[i].bank_name+"</option>";
        });
        $("#bank_id").html(option);
        //show bootstrap modal
        $("#premium_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Premium');
    }

    //popup to add Premium details
    function check_ins()
    {
        var policyNum = $('#policy_num').val();
        var clientID = $('#client_id').val();
        if(policyNum != '' && policyNum != null)
        {
            $.ajax({
                //Load data for the table through ajax
                url: '<?php echo site_url('broker/Insurances/check_insurance_status');?>',
                type: 'post',
                data: {prem_client_id: clientID, prem_policy_num: policyNum},
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'TRUE')
                    {
                        var status = $('#status option:selected').text();
                        /*if(status == 'Grace')
                        {
                            bootbox.confirm("This policy is in grace period. Do you want to add premium entry?", function(result) {
                                if(result)
                                    add_prem_form(data['banks']);
                            });
                        }
                        else */if(status == 'Lapsed')
                        {
                            bootbox.confirm("Status of this policy is LAPSE. Do you want to add premium entry?", function(result) {
                                if(result)
                                    add_prem_form(data['banks']);
                            });
                        }
                        else
                            add_prem_form(data['banks']);
                    }
                    else
                    {
                        $.pnotify({
                            title: 'Error!',
                            text: data['message'],
                            type: 'error',
                            hide: true
                        });
                    }
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    bootbox.alert("Something went terribly wrong");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
        else
        {
            $.pnotify({
                title: 'Error!',
                text: 'No Policy is selected',
                type: 'error',
                hide: true
            });
        }
    }

    //get premium details in datatable
    function premium_list() {
        var clientID = $('#client_id').val();
        var policyNum = $('#policy_num').val();
        var oTable = $("#premTable").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[0,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bVisible": false, "aTargets": [ 1 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Insurances/premium_list');?>',
                "type": 'post',
                "data":{prem_client_id: clientID, prem_policy_num: policyNum}
            },
            "columns": [
                { "data": "action" },
                { "data": "premium_id" },
                { "data": "bank_name" },
                { "data": "branch" },
                { "data": "account_number" },
                { "data": "cheque_number" },
                { "data": "cheque_date", "type": "date-uk" },
                { "data": "premium_amount" },
                { "data": "adjustment" },
                { "data": "adjustment_ref_number" },
                { "data": "advisers" },
                { "data": "narration" }
            ]

        });
        return oTable;
    }

    //save premium details in database
    function save_prem()
    {
        if($('#add_prem_form').parsley('validate'))
        {
            var url = '';
            if(save_method == 'add')
                url = "<?php echo site_url('broker/Insurances/save_premium');?>";
            else
                url = "<?php echo site_url('broker/Insurances/update_premium');?>";
            $.ajax({
                url: url,
                type:'post',
                data: $("#add_prem_form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'success')
                    {
                        $("#premium_modal_form").modal('hide');
                        premTable.destroy();
                        premTable = premium_list();
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                        $('#next_prem_due_date').val($("#next_premium_due_date").val());
                        $('#grace_due_date').val(graceDueDate);
                        var prem_paid = parseFloat($("#prem_paid_till_date").val());
                        prem_paid = prem_paid + parseFloat($("#premium_amount").val());
                        $("#prem_paid_till_date").val(prem_paid);
                        $("#add_prem_form")[0].reset();
                        //change policy status to In Force if status is Lapsed
                        /*if($("#status").select2("data").text == "Lapsed") {
                            $("#status option").each(function()
                            {
                                if($(this).text() == "In Force") {
                                    $("#status").select2("val", $(this).val());
                                }
                            });
                        }*/
                        if($("#status").select2("data").text != "In Force") {
                            $.pnotify({
                                title: "Change Status to `In Force`",
                                text: "Please change the status of this Insurance to `In Force` as you have added a new Premium",
                                type: 'info',
                                hide: true
                            });
                        }

                        //get system fund value and update its field
                        get_fund_value($('#policy_num').val());
                    }
                    else
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'error',
                            hide: true
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
    }

    //open premium dialog for editing
    function edit_premium(id)
    {
        save_method = 'update';
        $('#add_prem_form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Insurances/get_premium');?>",
            type: "POST",
            data:{id: id},
            dataType: "json",
            success: function(data)
            {
                var prem_data = data['premium'][0];
                var banks_data = data['banks'];
                var branch_data = data['branch'];
                var account_data = data['account'];
                //reset form on modals
                $("#add_prem_form")[0].reset();
                $("#premClientID").val(prem_data['client_id']);
                $("#premium_id").val(prem_data['premium_id']);
                if(prem_data['advisers'] != null)
                    $('#advisers').val(prem_data['advisers']);
                else
                    $('#advisers').val($('#adv_id option:selected').text());
                $('#policy_number').val(prem_data['policy_number']);
                $('#next_premium_due_date').datepicker('setDate', prem_data['next_premium_due_date']);
                $('#premium_amount').val(prem_data['premium_amount']).attr('readonly', true);
                if(prem_data['cheque_date'] != null)
                    $('#cheque_date').datepicker('setDate', prem_data['cheque_date']);
                else
                    $('#cheque_date').datepicker('setDate', $('#next_prem_due_date').val());
                if(prem_data['premium_mode'] != null)
                    $('#premium_mode').val(prem_data['premium_mode']);
                else
                    $('#premium_mode').val($('#mode option:selected').text());

                //set all banks of clients
                var option = '<option disabled selected>Select Bank</option>';
                $.each(banks_data, function(i, item){
                    option = option + "<option value="+banks_data[i].bank_id+">"+banks_data[i].bank_name+"</option>";
                });
                $("#bank_id").html(option).val(prem_data['bank_id']);

                //set all branch of clients
                option = '<option disabled selected>Select Branch</option>';
                $.each(branch_data, function(i, item){
                    option = option + "<option value="+branch_data[i].branch+">"+branch_data[i].branch+"</option>";
                });
                $("#premBranch").html(option).val(prem_data['branch']);
                $("#branch").val(prem_data['branch']);
                $("#cheque_number").val(prem_data['cheque_number']);
                $("#premAdjust").val(prem_data['adjustment']);
                $("#adjustment_ref_number").val(prem_data['adjustment_ref_number']);
                $("#narration").val(prem_data['narration']);

                //set all account number of clients
                option = '<option disabled selected>Select Account Number</option>';
                $.each(account_data, function(i, item){
                    option = option + "<option value="+account_data[i].account_number+">"+account_data[i].account_number+"</option>";
                });
                $("#account_number").html(option).val(prem_data['account_number']);
                //show bootstrap modal
                $("#premium_modal_form").modal('show');
                //set title to modal
                $(".modal-title").text('Edit Premium');

            },
            error: function (data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }

    //delete premium details in datatable
    function delete_prem()
    {
        bootbox.confirm("Are you sure you want to delete this Premium Details?", function(result) {
            if(result)
            {
                var clientID = $('#client_id').val();
                var policyNum = $('#policy_num').val();
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Insurances/delete_prem');?>",
                    type: "POST",
                    data: {prem_client_id: clientID, prem_policy_num: policyNum},
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data['status'] == 'success')
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: 'success',
                                hide: true
                            });
                            //if success reload ajax table
                            premTable.destroy();
                            premTable = premium_list();
                        }
                        else
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: 'error',
                                hide: true
                            });
                        }

                        var mode = $('#mode option:selected').text();
                        var nextPremiumDate = $('#next_prem_due_date').val();
                        graceDueDate = $("#grace_due_date").val();
                        if(mode == 'Annually') {
                            nextPremiumDate = addMonth(nextPremiumDate, -12);
                            graceDueDate = addMonth(graceDueDate, -12);
                        }
                        else if(mode == 'Half-Yearly') {
                            nextPremiumDate = addMonth(nextPremiumDate, -6);
                            graceDueDate = addMonth(graceDueDate, -6);
                        }
                        else if(mode == 'Quarterly') {
                            nextPremiumDate = addMonth(nextPremiumDate, -3);
                            graceDueDate = addMonth(graceDueDate, -3);
                        }
                        else if(mode == 'Monthly') {
                            nextPremiumDate = addMonth(nextPremiumDate, -1);
                            graceDueDate = addMonth(graceDueDate, -1);
                        }

                        $('#next_prem_due_date').val(nextPremiumDate);
                        $('#grace_due_date').val(graceDueDate);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        bootbox.alert("Something went terribly wrong");
                    }
                });
            }
        });
    }


    //function to get system fund value on premium add - Salmaan 10/08/16
    function get_fund_value(policy_num)
    {
        $.ajax({
            url : "<?php echo site_url('broker/Insurances/get_fund_value');?>",
            type: "POST",
            data:{policy_num: policy_num},
            dataType: "json",
            success: function(data)
            {
                if(data) {
                    $("#fund_value").val(data);
                }
            },
            error: function(data)
            {
                console.log(data);
                bootbox.alert("Something went terribly wrong");
            }
        });
    }
</script>