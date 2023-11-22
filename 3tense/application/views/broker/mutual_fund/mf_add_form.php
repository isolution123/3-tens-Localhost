<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Mutual_funds');?>">Mutual Fund</a></li>
                <li class="active">Add Mutual Fund</li>
            </ol>
            <h1>Add Mutual Fund</h1>

        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="#" id="mf_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
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
                                <label class="col-sm-2 control-label">Folio Number</label>
                                <div class="col-md-4  no-border">
                                    <div id="divPur">
                                        <input type="text" name="folio_number" tabindex="4" value="" class="form-control" id="folio_number">
                                    </div>
                                    <div id="divRed" style="display: none">
                                        <select name="red_fol_num" class="populate" id="red_fol_num" style="width: 100%;" tabindex="4">
                                            <option disabled selected value="">Select Folio Number</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Client Name</label>
                                <div class="col-md-4">
                                    <select name="client_id" class="populate" required="required" id="client_id" style="width: 100%" tabindex="3">
                                        <option disabled selected value="">Select Client</option>
                                        <?php foreach($clients as $row):?>
                                            <option value='<?php echo $row->client_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Mutual Fund Info</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Type of Transaction</label>
                                <div class="radio col-md-4 no-border">
                                    <label style="margin-right: 50px">
                                        <input type="radio" name="transaction_type" id="transaction_type1" tabindex="5" value="Purchase" checked style="padding-right: 20px">
                                        Purchase
                                    </label>
                                    <label>
                                        <input type="radio" name="transaction_type" id="transaction_type2" tabindex="6" value="Redemption">
                                        Redemption
                                    </label>
                                </div>
                                <label class="col-sm-2 control-label">Purchase/Redemption Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="purchase_date" required="required" tabindex="9" class="form-control date" id="purchase_date" data-inputmask="'alias':'date'" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mutual Fund Type</label>
                                <div class="col-md-4 add-new-btn">
                                    <select name="mutual_fund_type" class="populate" required="required" id="mutual_fund_type" style="width: 100%" tabindex="7">
                                        <option disabled selected value="">Select Mutual Fund Type</option>
                                        <?php foreach($mfTypes as $row):?>
                                            <option value='<?php echo $row->mutual_fund_type; ?>'><?php echo $row->mutual_fund_type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Number of Units</label>
                                <div class="col-md-2 no-border">
                                    <input type="number" min="0" name="quantity" required="required" tabindex="10" class="form-control" id="quantity">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mutual Fund Scheme</label>
                                <div class="col-md-4 add-new-btn">
                                   <select name="mutual_fund_scheme" class="populate_scheme" id="mutual_fund_scheme" style="width: 80%; display:none;" tabindex="8">
                                        <!-- <option disabled selected value="">Select Scheme</option> -->
                                      
                                    </select>
                                    
                                    <input type="text" name="scheme_id" id="scheme_id" required="required" placeholder="Select a scheme" value="Loading schemes..." tabindex="10" style="width: 80%;" />
                                </div>
                                <label class="col-sm-2 control-label">NAV</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="nav" required="required" tabindex="11" class="form-control" id="nav">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-md-4 no-border">
                                    <input type="text" name="amount" class="form-control" id="amount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Adjustment</label>
                                <div class="col-sm-4">
                                    <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Add Adjustment." >
                                        <span class="input-group-addon">
                                            <input type="checkbox" id="adj_flag" name="adj_flag" tabindex="12">
                                        </span>
                                        <input type="hidden" value="0" name="adjustment_flag" id="adjustment_flag">
                                        <textarea placeholder="Adjustment" class="form-control" id="adjustment" tabindex="13" name="adjustment" readonly></textarea>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">Adjustment Ref Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" value="" name="adjustment_ref_number" id="adjustment_ref_number" tabindex="14" readonly class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">
                        <h4>Mutual Fund Bank Details</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Bank Name</label>
                                <div class="col-md-4  no-border">
                                    <select name="bank_id" class="populate" required="required" id="bank_id" style="width: 100%" tabindex="15">
                                        <option disabled selected value="">Select Bank</option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Account Number</label>
                                <div class="col-md-4">
                                    <select class="form-control" name="account_number" id="account_number" tabindex="17">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Branch Name</label>
                                <div class="col-md-4  no-border">
                                    <select name="branch" class="populate" required="required" id="branch" style="width: 100%" tabindex="16">
                                        <option disabled selected value="">Select Branch</option>
                                        <?php /*foreach($bank as $row):?>
                                            <option value='<?php echo $row->branch; ?>'><?php echo $row->branch; ?></option>
                                        <?php endforeach;*/ ?>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Cheque Number</label>
                                <div class="col-md-4  no-border">
                                    <input type="text" name="cheque_number" class="form-control" tabindex="18" id="cheque_number">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="bottom-row navbar-fixed-bottom">
                <div class="col-sm-12 bottom-col">
                    <button type="button" id="add" tabindex="34" onclick="addNewForm()" data-style="expand-left" class="btn btn-success bottom-btn ladda-button"><i class="fa fa-plus"></i> Add</button>
                    <button type="button" id="edit" tabindex="35" onclick="editForm('<?php echo base_url("broker/mutual_funds/edit_form");?>', $('#transID').val())"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" id="delete" tabindex="36" onclick="" data-style="expand-left" class="btn btn-danger bottom-btn ladda-button"><i class="fa fa-trash-o"></i> Delete</button>
                    <button type="button" id="save" tabindex="37" onclick="mf_submit()"  data-style="expand-left" class="btn btn-primary bottom-btn ladda-button"><i class="fa fa-save"></i> Save</button>
                    <button type="button" id="cancel" tabindex="38" onclick="enableBtn()"  data-style="expand-left" class="btn btn-warning bottom-btn ladda-button"><i class="fa fa-ban"></i> Cancel</button>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<!-- This below library is used for Schemes Select2 filtering -->
<script data-require="lodash-underscore@3.7.0" data-semver="3.7.0" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/3.7.0/lodash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>
<script type="text/javascript">
    var button;
    $(document).ready(function() {
        $('.ladda-button').click(function(e){
            button = this;
        }); 
    });

    $(function() {
              /* Select2 paginated list for schemes - Starts here  */
        var options_scheme = new Array();
        $(".populate_scheme option").each(function() {
          var opt = new Object();
          opt.id = $(this).val();
          opt.text = $(this).text();
          options_scheme.push(opt);
        });

      $("#scheme_id").select2({
        data : options_scheme,
          // init selected from elements value
          initSelection    : function (element, callback) {
            var initialData = [];
            $(element.val().split(",")).each(function () {
              initialData.push({
                id  : this,
                text: this
              });
            });
            callback(initialData);
          },
        // NOT NEEDED: text for loading more results
          //formatLoadMore   : 'Loading more...',

          // query with pagination
          query : function (q) {
            var pageSize,
              results;
            pageSize = 200; // or whatever pagesize
            results  = [];
            if (q.term && q.term !== "") {
              // HEADS UP; for the _.filter function i use underscore (actually lo-dash) here
              results = _.filter(this.data, function (e) {
                return (e.text.toUpperCase().indexOf(q.term.toUpperCase()) >= 0);
              });
            } else if (q.term === "") {
              results = this.data;
            }
            q.callback({
              results: results.slice((q.page - 1) * pageSize, q.page * pageSize),
              more   : results.length >= q.page * pageSize
            });
          }
      }).val('');


      $("#s2id_scheme_id .select2-chosen").html("Loading Schemes...");
    /* Select2 paginated list for schemes - Ends here  */
    function load_scheme_with_ajax(scheme_data)
    {
      /* Select2 paginated list for schemes - Starts here  */
     var options_scheme = new Array();
     $("#scheme_id").select2({

     data : scheme_data,
       // init selected from elements value
       initSelection    : function (element, callback) {
         var initialData = [];
         $(element.val().split(",")).each(function () {
           initialData.push({
             id  : this,
             text: this
           });
         });
         callback(initialData);
       },


       // query with pagination
       query : function (q) {
         var pageSize,
           results;
         pageSize = 200; // or whatever pagesize
         results  = [];
         if (q.term && q.term !== "") {
           // HEADS UP; for the _.filter function i use underscore (actually lo-dash) here
           results = _.filter(this.data, function (e) {
             return (e.text.toUpperCase().indexOf(q.term.toUpperCase()) >= 0);
           });
         } else if (q.term === "") {
           results = this.data;
         }
         q.callback({
           results: results.slice((q.page - 1) * pageSize, q.page * pageSize),
           more   : results.length >= q.page * pageSize
         });
       }
    }).val('');


    $("#s2id_scheme_id .select2-chosen").html("Select Scheme");
    /* Select2 paginated list for schemes - Ends here  */


    }

    /*------ fetch All schemes with ajax starts here ------------*/
    $.ajax({
          url: "<?php echo base_url('broker/Assets_liabilities/get_al_schemes_ajax'); ?>",
          type: "POST",
          dataType: "JSON",
          success: function(status)
          {

            //$.each(status.data, function (index, value) {
              //console.log(status.data[index].scheme_id);
              //console.log(status.data[index].scheme_name);
               load_scheme_with_ajax(status.data);
               //console.log(status);
              //$('#scheme_id_select').append($('<option>').text(status.data[index].scheme_name).attr('value', status.data[index].scheme_id));
             //});
        //alert  (status);

         },
          error: function (jqXHR,textStatus,errorThrown)
          {
              console.log(jqXHR);
          }
        });
    /*------ fetch All schemes with ajax ends here ------------*/
        
        //initialize tooltip
        $('[data-toggle="tooltip"]').tooltip();
        //on load disable controls
        disableBtn();
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
        $("#adj_flag").change(function(){
            if(this.checked)
            {
                $("#adjustment_flag").val("1");
                $("#adjustment").attr('readonly',false);
                $("#adjustment_ref_number").attr('readonly',false);
            }
            else
            {
                $("#adjustment_flag").val("0");
                $("#adjustment").attr('readonly',true);
                $("#adjustment_ref_number").attr('readonly',true);
            }
        });
        //on family change get clients
        $('#family_id').change(function()
        {
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown');?>";
            getClients(url, this.value, 'client_id', 'nominee', "", "");
        });

        $('#client_id').change(function()
        {
            get_folios();
            $.ajax({
                url: '<?php echo site_url('broker/Banks/get_client_banks'); ?>',
                dataType: 'json',
                type: 'post',
                data: {clientID: this.value},
                success: function(data)
                {
                    var option = '<option selected disabled>Select Bank</option>';
                    $.each(data['banks'], function(i, item){
                        option = option + "<option value = " + item.bank_id + ">" + item.bank_name + "</option>";
                    });
                    $('#bank_id').html(option);
                },
                error: function(data)
                {
                    console.log(data);
                }
            });

            //on client change get family
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
        });

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
                    $("#branch").html(option);
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });

        $('#branch').change(function(){
            $.ajax({
                url: '<?php echo site_url("broker/Banks/get_account_num");?>',
                type: 'post',
                data: {bankID: $('#bank_id').val(), clientID: $('#client_id').val(), branch: $('#branch').val()},
                dataType: 'json',
                success:function(data)
                {
                    var option = '<option disabled selected>Select Account Number</option>';
                    $.each(data['acc_num'], function(i, item){
                        option = option + "<option value='"+item.account_number+"'>"+item.account_number+"</option>";
                    });
                    $("#account_number").html(option);
                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });

        $('#red_fol_num').change(function(){
            get_mf_schemes('Redemption');
        });

        /*$('#folio_number, #scheme_id').change(function(){
            var fol_num = $('#folio_number').val();
            var mf_scheme = $('#scheme_id').val();
            var client_id = $('#client_id').val();
            if(fol_num != '' && mf_scheme != '' && client_id != '')
            {
                $.ajax({
                    url: '<?php echo site_url("broker/Mutual_funds/check_folio_number");?>',
                    type: 'post',
                    data: {folio_num: fol_num, mf_scheme: mf_scheme, client_id: client_id},
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data['status'] == 'error')
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
        });*/

        $('[name="transaction_type"]').click(function(){
            if(this.value == 'Redemption')
            {
                $('#divPur').hide();
                $('#divRed').show();
                get_folios();
                get_mf_types('Redemption');
            }
            else
            {
                $('#divPur').show();
                $('#divRed').hide();
                get_mf_types('Purchase');
                get_mf_schemes('All');
            }
        });

        $("#quantity, #nav").change(function(){
            $('#amount').val($('#quantity').val() * $('#nav').val());
        });

        //on selection of mf scheme, get the NAV value
        $("#scheme_id, #purchase_date").change(function() {
            var schemeID = $("#scheme_id").val();
            var purDate = $("#purchase_date").val();
            if(purDate == "") {
                $.pnotify({
                    title: "Cannot get NAV!",
                    text: "Please enter a Purchase Date to get NAV, OR enter your own NAV",
                    type: 'error',
                    hide: true
                });
            } else {
                $.ajax({
                    url: '<?php echo site_url('broker/Mutual_funds/get_scheme_nav');?>',
                    type: 'post',
                    data: {'scheme_id': schemeID, 'purchase_date': purDate},
                    dataType: 'json',
                    success:function(data)
                    {
                        if(data) {
                            $("#nav").val(data);
                        } else {
                            $("#nav").val("");
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
        });
    });

    function get_folios()
    {
        var client_id = $('#client_id').val();
        if(client_id != '')
        {
            $.ajax({
                url: '<?php echo site_url('broker/Mutual_funds/get_folio_numbers'); ?>',
                dataType: 'json',
                type: 'post',
                data: {clientID: client_id},
                success: function(data)
                {
                    var option = '<option selected disabled>Select Folio Number</option>';
                    $.each(data['folio'], function(i, item){
                        option = option + "<option value = " + item.folio_number + ">" + item.folio_number + "</option>";
                    });
                    $('#red_fol_num').html(option);
                },
                error: function(data)
                {
                    console.log(data);
                }
            });
        }
    }

    function get_mf_types(type)
    {
        $.ajax({
            url: '<?php echo site_url('broker/Mutual_funds/get_mf_types'); ?>',
            dataType: 'json',
            type: 'post',
            data: {useFor: type},
            success: function(data)
            {
                var option = '<option selected disabled>Select Mutual Fund Type</option>';
                $.each(data['mf_types'], function(i, item){
                    option = option + "<option value = " + item.mutual_fund_type + ">" + item.mutual_fund_type + "</option>";
                });
                $('#mutual_fund_type').html(option);
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    function get_mf_schemes(type)
    {
        $.ajax({
            url: '<?php echo site_url('broker/Mutual_funds/get_mf_scheme'); ?>',
            dataType: 'json',
            type: 'post',
            data: {fol_num: $("#red_fol_num").val(), type: type},
            success: function(data)
            {
                var option = '<option selected disabled>Select Mutual Fund Scheme</option>';
                $.each(data['mf_schemes'], function(i, item){
                    option = option + "<option value = " + item.scheme_id + ">" + item.scheme_name + "</option>";
                });
                $('#mutual_fund_scheme').html(option);
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    //insert mutual fund details in database
    function mf_submit()
    {
        var mf_type = $('[name="transaction_type"]:checked').val();
        if(mf_type == 'Purchase')
        {
            if($('#folio_number').val() == '')
            {
                $.pnotify({
                    title: 'Error on Mutual Fund Purchase',
                    text: 'Please Enter Folio Number',
                    type: 'error',
                    hide: true
                });
                return;
            }
        }
        else if(mf_type == 'Redemption')
        {
            var fol_num = $('#red_fol_num').val();
            if(fol_num == '' || fol_num == null)
            {
                $.pnotify({
                    title: 'Error on Mutual Fund Redemption',
                    text: 'Please Select Folio Number',
                    type: 'error',
                    hide: true
                });
                return;
            }
        }
        var valid = $('#mf_form').parsley('validate');
        if(valid)
        {
            var l = Ladda.create(button);
            l.start();
            
            $.ajax({
                url: '<?php echo site_url('broker/Mutual_funds/add_mf');?>',
                type: 'post',
                data: $('#mf_form').serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'success')
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                        enableBtn();
                        $('#transID').val(data['transID']);
                        l.stop();
                    }
                    else
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'error',
                            hide: true
                        });
                        l.stop();
                    }
                },
                error:function(data)
                {
                    console.log(data);
                }
            });
        }
    }
</script>