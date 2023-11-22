<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Equity');?>">Equity</a></li>
                <li class="active">Add Equity</li>
            </ol>
            <h1>Add Equity</h1>
        </div>
        <div class="container">
            <input type="hidden" id="transID" value="">
            <form action="#" id="equity_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="family_id" class="populate" required="required" id="family_id" style="width: 80%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Date of Transaction</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="transaction_date" required="required" tabindex="4" value="<?php echo date('d/m/Y')?>" class="form-control mask datepicker" id="transaction_date" data-inputmask="'alias':'date'" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 80%" tabindex="3">
                                        <option disabled selected value="">Select Client</option>
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="<?php echo base_url("broker/Clients/add");?>" tabindex="2" class="btn btn-xs btn-inverse-alt"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Investment Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Broker Name</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="hidden" id="trading_broker_name" name="trading_broker_name">
                                    <select name="trading_broker_id" class="populate" required="required" id="trading_broker_id" style="width: 80%" tabindex="5">
                                        <option disabled selected value="">Select Broker Name</option>
                                    </select>
                                    <a href="javascript:;" tabindex="6" class="btn btn-xs btn-inverse-alt" onclick="add_trading(true)"><i class="fa fa-plus"></i></a>
                                </div>
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="eq_amt" readonly class="form-control" id="eq_amt">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Code</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="client_code" tabindex="7" required="required" id="client_code" class="form-control" style="width: 100%" tabindex="7">
                                        <option disabled selected value="">Select Client Code</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Script Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Scrip Name</label>
                                <div class="col-md-4">
                                    <!--<select name="scrip_name" class="populate" required="required" id="scrip_name" style="width: 100%" tabindex="5">
                                        <option disabled selected value="">Select Scrip Name</option>
                                        <?php /*foreach($scrip as $row):?>
                                            <option value='<?php echo $row->scrip_name; ?>'><?php echo $row->scrip_name; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>-->
                                    <input type="" name="scrip_name" tabindex="8" required="required" id="scrip_name" style="width: 100%" placeholder="Select a scrip name" />
                                </div>
                                <label class="col-sm-2 control-label">Acquiring Rate</label>
                                <div class="col-md-4">
                                    <input type="text" name="acquiring_rate" tabindex="13" class="form-control" id="acquiring_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Scrip Code</label>
                                <div class="col-md-4">
                                    <!--<select name="scrip_code" class="populate" required="required" id="scrip_code" style="width: 100%" tabindex="5">
                                        <option disabled selected value="">Select Scrip Code</option>
                                        <?php /*foreach($scrip as $row):?>
                                            <option value='<?php echo $row->scrip_code; ?>'><?php echo $row->scrip_code; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>-->
                                    <input type="" name="scrip_code" tabindex="9" required="required" id="scrip_code" style="width: 100%" placeholder="Select a scrip code" />
                                </div>
                                <label class="col-sm-2 control-label">Current Rate</label>
                                <div class="col-md-4">
                                    <input type="text" readonly name="eq_curr_rate" class="form-control" id="eq_curr_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Quantity</label>
                                <div class="col-md-4  no-border">
                                    <input type="number" min="1" name="quantity" required="required" tabindex="10" class="form-control" id="quantity" value="1">
                                </div>
                                <label class="col-sm-2 control-label">Current Amount</label>
                                <div class="col-md-4">
                                    <input type="text" readonly name="eq_curr_amt" class="form-control" id="eq_curr_amt" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-md-4 no-border">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="1" tabindex="11" id="initial_inv" name="initial_inv">
                                        Initial Investment
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="hidden" name="initial_investment" id="initial_investment" value="0" />
                                        <input type="checkbox" value="1" tabindex="12" id="eq_track" name="eq_track">
                                        Tracking
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" id="add" tabindex="34" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" id="edit" tabindex="35" onclick="editForm('<?php echo base_url("broker/Equity/edit_form");?>', $('#transID').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" id="delete" tabindex="36" onclick="" data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" id="save" tabindex="37" onclick="equity_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" id="cancel" tabindex="38" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<script type="text/javascript">
    $(function() {
        disableBtn();
        $('.populate').select2({width: 'resolve'});
        $('.datepicker').datepicker({format:"dd/mm/yyyy"});
        $('.mask').inputmask();

        ajax_load_scrips(); //load scrip names and scrip codes into select box
    });

    function ajax_load_scrips()
    {
        $.ajax({
            url: '<?php echo base_url('broker/Equity/get_scrips')?>',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                $("#scrip_name").attr("disabled", true);
                $("#scrip_code").attr("disabled", true);
            },
            success: function(data) {
                var names = [];
                var codes = [];
                //get values of scrip names and scrip codes in arrays
                $.each(data, function() {
                    names.push({id: this.scrip_name, text: this.scrip_name});
                    codes.push({id: this.scrip_code, text: this.scrip_code});
                });

                $("#scrip_name").attr("disabled", false);
                $("#scrip_name").select2({
                    initSelection: function(element, callback) {
                        var selection = _.find(names, function(metric){
                            return metric.id === element.val();
                        })
                        callback(selection);
                    },
                    query: function(options){
                        var pageSize = 300;
                        var startIndex  = (options.page - 1) * pageSize;
                        var filteredData = names;

                        if( options.term && options.term.length > 0 ){
                            if( !options.context ){
                                var term = options.term.toLowerCase();
                                options.context = names.filter( function(metric){
                                    return ( metric.text.toLowerCase().indexOf(term) !== -1 );
                                });
                            }
                            filteredData = options.context;
                        }

                        options.callback({
                            context: filteredData,
                            results: filteredData.slice(startIndex, startIndex + pageSize),
                            more: (startIndex + pageSize) < filteredData.length
                        });
                    },
                    placeholder: "Select a scrip name"
                });
                $("#scrip_name").attr("required",true);

                $("#scrip_code").attr("disabled", false);
                $("#scrip_code").select2({
                    initSelection: function(element, callback) {
                        var selection = _.find(codes, function(metric){
                            return metric.id === element.val();
                        })
                        callback(selection);
                    },
                    query: function(options){
                        var pageSize = 300;
                        var startIndex  = (options.page - 1) * pageSize;
                        var filteredData = codes;

                        if( options.term && options.term.length > 0 ){
                            if( !options.context ){
                                var term = options.term.toLowerCase();
                                options.context = codes.filter( function(metric){
                                    return ( metric.text.toLowerCase().indexOf(term) !== -1 );
                                });
                            }
                            filteredData = options.context;
                        }

                        options.callback({
                            context: filteredData,
                            results: filteredData.slice(startIndex, startIndex + pageSize),
                            more: (startIndex + pageSize) < filteredData.length
                        });
                    },
                    placeholder: "Select a scrip code"
                });
                $("#scrip_code").attr("required",true);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting scrip details',
                    type: 'error',
                    hide: true
                });
            }
        });
    }

    //on family change get clients
    $('#family_id').change(function()
    {
        var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
        getClients(url, this.value, 'client_id', 'nominee', "", "");

        $("#client_code").html('<option disabled selected value="">Select Client Code</option>');
        $("#trading_broker_id").html('<option disabled selected value="">Select Broker Name</option>');
        $("#trading_broker_id").select2("val","");
        $("#client_id").select2("val","");
        $("#eq_amt").val("");
    });

    //on client change get trading_brokers
    $('#client_id').change(function()
    {
        $.ajax({
            url: '<?php echo site_url('broker/Clients/get_client_family');?>',
            type: 'post',
            data: {clientID: this.value},
            dataType: 'json',
            success: function(data) {
                if(data != 'fail') {
                    $("#family_id").select2("val",data['family_id']);
                } else {
                    console.log("Unable to load family data! No clientID passed");
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting family name by client',
                    type: 'error',
                    hide: true
                });
            }
        });

        $.ajax({
            url: '<?php echo site_url('broker/Clients/get_client_trading_brokers');?>',
            type: 'post',
            data: {clientID: this.value},
            dataType: 'json',
            success: function(data) {
                $("#client_code").html('<option disabled selected value="">Select Client Code</option>')
                $("#trading_broker_id").select2("val","");
                $("#eq_amt").val("");

                var option = '<option disabled selected value="">Select Broker Name</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].trading_broker_id+">"+data[i].trading_broker_name+"</option>";
                });
                $("#trading_broker_id").html(option);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details of broker by client',
                    type: 'error',
                    hide: true
                });
            }
        });
    });

    //on trading_broker change get client codes
    $('#trading_broker_id').change(function()
    {
        var clientID = $("#client_id").val();
        $.ajax({
            url: '<?php echo site_url('broker/Clients/get_trading_broker_client_code');?>',
            type: 'post',
            data: {trading_brokerID: this.value, clientID: clientID},
            dataType: 'json',
            success: function(data) {
                $("#client_code").html('<option disabled selected value="">Select Client Code</option>');
                $("#eq_amt").val("");

                var option = '<option disabled selected value="">Select Client Code</option>';
                $.each(data, function(i, item){
                    option = option + "<option value="+data[i].client_code+">"+data[i].client_code+"</option>";
                });
                $("#client_code").html(option);
                $("#trading_broker_name").val($('#trading_broker_id').select2('data').text);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from broker',
                    type: 'error',
                    hide: true
                });
            }
        });
    });

    $("#client_code").change(function() {
        var clientID = $("#client_id").val();
        $.ajax({
            url: '<?php echo site_url('broker/Clients/get_client_code_balance');?>',
            type: 'post',
            data: {clientCode: this.value, clientID: clientID},
            dataType: 'json',
            success: function(data) {
                $("#eq_amt").val(data.balance);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting details from broker',
                    type: 'error',
                    hide: true
                });
            }
        });
    });

    //on scrip_name change get scrip_code
    $('#scrip_name').change(function()
    {
        $.ajax({
            url: '<?php echo site_url('broker/Equity/match_scrips');?>',
            type: 'post',
            data: {scrip_name: this.value},
            dataType: 'json',
            success: function(data) {
                $("#scrip_code").select2("val",data.scrip_code);
                $("#eq_curr_rate").val(data.close_rate);
                //$("#acquiring_rate").val(data.close_rate);
                get_curr_amt();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting scrips data',
                    type: 'error',
                    hide: true
                });
            }
        });
    });
    //on scrip_code change get scrip_name
    $('#scrip_code').change(function()
    {
        $.ajax({
            url: '<?php echo site_url('broker/Equity/match_scrips');?>',
            type: 'post',
            data: {scrip_code: this.value},
            dataType: 'json',
            success: function(data) {
                $("#scrip_name").select2("val",data.scrip_name);
                $("#eq_curr_rate").val(data.close_rate);
                $("#acquiring_rate").val(data.close_rate);
                get_curr_amt();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $.pnotify({
                    title: 'Error!',
                    text: 'Error getting scrips data',
                    type: 'error',
                    hide: true
                });
            }
        });
    });

    $("#quantity").on("input", function() {
        get_curr_amt();
    });

    function get_curr_amt() {
        var qty = $("#quantity").val();
        var rate = $("#eq_curr_rate").val();
        //check if textboxes have only numbers/decimal or not
        if (qty.match(/^[0-9.]*$/) && rate.match(/^[0-9.]*$/)) {
            var amount = qty*rate;
        } else {
            var amount = 0;
        }
        $("#eq_curr_amt").val(amount);
    }


    // change initial investment and tracking checkboxes checked status
    $("#initial_inv").on("click", function() {
        if(this.checked) {
            $("#eq_track").attr("checked",false);
            $("#initial_investment").val("0");
        }
    });
    $("#eq_track").on("click", function() {
        if(this.checked) {
            $("#initial_inv").attr("checked",false);
            $("#initial_investment").val("1");
        } else {
            $("#initial_investment").val("0");
        }
    });

    //insert equity details in database
    function equity_submit()
    {
        var valid = $("#equity_form").parsley("validate");
        if(valid)
        {
            var form_data = $('#equity_form').serialize();
            if($("#initial_inv").prop("checked")) {
                bootbox.confirm("Are you sure as this selection will automatically add funds in the system.", function(result) {
                    $.ajax({
                        url: '<?php echo site_url('broker/Equity/add_funds');?>',
                        type: 'post',
                        data: form_data,
                        dataType: 'json',
                        success:function(data)
                        {
                            if(data['type'] == 'success')
                            {
                                $.pnotify({
                                    title: data['title'],
                                    text: data['text'],
                                    type: 'success',
                                    hide: true
                                });
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
                        error:function(jqXHR, textStatus, errorThrown)
                        {
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        }
                    });
                });
            }

            $.ajax({
                url: '<?php echo site_url('broker/Equity/add_equity');?>',
                type: 'post',
                data: form_data,
                dataType: 'json',
                success:function(data)
                {
                    if(data['type'] == 'success')
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                        $("#transID").val(data['id']);
                        enableBtn();
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
                error:function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }
</script>