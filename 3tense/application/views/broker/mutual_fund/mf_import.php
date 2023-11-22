<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Mutual Fund Details</li>
            </ol>
            <h1>Import Mutual Fund Details</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Mutual_funds/mf_import') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Mutual Fund Details</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Details for Mutual Fund.<br/>
                        <a href="javascript:void(0)" id="importHelp" class="alert-link"><i class="fa fa-lightbulb-o"></i> Need Help in upload of excel?</a><br/><br/>
                        <a href="<?=base_url('uploads/imports/Mutual-fund-import.xlsx');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                        <?php 
                          if($this->session->userdata('broker_id')=='0004' || $this->session->userdata('broker_id')=='0204'  
                          || $this->session->userdata('broker_id')=='0009' || 
                          $this->session->userdata('broker_id')=='0174' || $this->session->userdata('broker_id')=='0196'
                          || $this->session->userdata('broker_id')=='0063'|| $this->session->userdata('broker_id')=='0180')
                                        {
                        ?>
                          
                            <label class="col-sm-2 control-label">Select Type</label>
                            <div id="file_type" class="col-sm-8">
                          <select class="form-control select2" name="file_type" id="file_type" required="required">
                              <option disabled selected value=" ">Select File Type </option>
                              <option value="Cams">Import Cams</option>
                              <option value="Karvy">Import Karvy</option>
                              <option value="Franklin">Import Franklin</option>
                              <option value="Sundaram">Import Sundaram</option>
                              <option value="NJ">Import NJ</option>
                              <option value="NH">Import NH</option>
                          </select>
                        </div><br><br>
                        <?php
                                        }
                        ?>
                      
                            <label for="import_stake" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-8" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_mf" id="import_mf">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                                <?php /*<div class="radio col-sm-12 no-border">
                                    <label style="margin-right: 50px">
                                        <input type="radio" name="transaction_type" id="transaction_type1" tabindex="2" value="Purchase" checked style="padding-right: 20px">
                                        Purchase
                                    </label>
                                    <label>
                                        <input type="radio" name="transaction_type" id="transaction_type2" tabindex="3" value="Redemption">
                                        Redemption
                                    </label>
                                </div>*/ ?>
                                <div class="col-sm-12">
                                    <button type="submit" id="fund_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                                <?php if(isset($val_data) && !empty($val_data)) { ?>
                                <div class="col-sm-6">
                                    <input type="hidden" name="brokerID" id="brokerID" value="<?=$val_data['brokerID']?>" />
                                    <input type="hidden" name="transID" id="transID" value="<?=$val_data['transID']?>" />
                                    <button type="button" id="fund_val_btn" tabindex="4" class="btn btn-danger ladda-button" data-style="expand-right" style="margin-top: 20px;">
                                        <i class="fa fa-gears"></i> Valuation
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <br/><br/>
                                    <span id="note" class="text-danger"></span>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-12" style="float: none; display: inline-block">
                            <?php if($import_data != null):?>
                                <div class="panel panel-midnightblue" style="margin-top: 20px">
                                    <div class="panel-heading">
                                        <h4>Mutual Fund Details Not Imported</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            Mutual Fund Details That Were not Imported.
                                            <br/>
                                            Following are the Folio Number, PAN Number which were not imported.
                                            <br/>
                                            Please note the Reason and make necessary changes. <a onclick=""></a>
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                                <thead>
                                                <tr>
                                                    <th>Scheme/Product Code</th>
                                                    <th>Folio Number</th>
                                                    <th>Trn Type</th>
                                                    <th>Trn No.</th>
                                                    <th>PAN Number</th>
                                                    <th>Reason</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($import_data as $mf): ?>
                                                    <tr>
                                                        <td><?php echo $mf[1];?></td>
                                                        <td><?php echo $mf[2];?></td>
                                                        <td><?php echo $mf[3];?></td>
                                                        <td><?php echo $mf[4];?></td>
                                                        <td><?php echo $mf[5];?></td>
                                                        <td><?php echo $mf[6];?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;?>
                            </div>
                            <div class="col-sm-12" style="float: none; display: inline-block">
                            <?php if($pip_or_ipo_data != null):?>
                                <div class="panel panel-midnightblue" style="margin-top: 20px">
                                    <div class="panel-heading">
                                        <h4>Mutual Fund Details With PIP or IPO</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            Following are the Client Name & PAN Number whose transaction type is PIP or IPO.
                                            To enter bank details of records, go to their respective records in Mutual Funds and edit the records.
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="pip_table">
                                                <thead>
                                                <tr>
                                                    <?php //<th>Action</th>?>
                                                    <th>Client Name</th>
                                                    <th>Scheme/Product Code</th>
                                                    <th>Folio Number</th>
                                                    <th>Transaction Type</th>
                                                    <th>PAN</th>
                                                    <th>Trn No.</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($pip_or_ipo_data as $data):?>
                                                    <tr>
                                                        <?php /*<td><?php echo $data['action'];?></td>*/ ?>
                                                        <td><?php echo $data['client_name'];?></td>
                                                        <td><?php echo $data['prod_code'];?></td>
                                                        <td><?php echo $data['folio_number'];?></td>
                                                        <td><?php echo $data['trans_type'];?></td>
                                                        <td><?php echo $data['pan_no'];?></td>
                                                        <td><?php echo $data['sr_no'];?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- Bootstrap modal -->
        <div class="modal fade" id="importModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title"></h3>
                    </div>
                    <div class="modal-body form">
                        <p>Make sure Excel is in proper format before importing</p>
                        <p>Make sure your excel has following columns, please note the spacing in between the words and date formats must be in <b><i>'dd/mm/yyyy' i.e (21/01/2016)</i></b></p>
                        <b>Column Names</b>
                        <table class="table-bordered help-table">
                            <tr>
                                <td>Sr No.</td>
                                <td>Group Name (M)</td>
                            </tr>
                            <tr>
                                <td>Investor Name (M)</td>
                                <td>Pan No. (M)</td>
                            </tr>
                            <tr>
                                <td>Trn Type (M)</td>
                                <td>Date (M)</td>
                            </tr>
                            <tr>
                                <td>Folio No (M)</td>
                                <td>Scheme Name (M)</td>
                            </tr>
                            <tr>
                                <td>NAV (M)</td>
                                <td>Unit (M)</td>
                            </tr>
                            <tr>
                                <td>Amount (M)</td>
                                <td>Bal. Unit (M)</td>
                            </tr>
                            <tr>
                                <td>DPO Per Unit</td>
                                <td>Adjustment</td>
                            </tr>
                            <tr>
                                <td>Adjustment Ref. No.</td>
                            </tr>
                        </table>
                        <p>Note: (M) These fields are Mandatory and please don't mention (M) in column name of your excel</p>
                        <p>Make Sure the data you are entering in the excel must exists in the application</p>
                        <p>Mutual Fund Types must be according to transaction type i.e. Purchase or Redemption.</p>
                        <p>Eg: DIV(Purchase), IPO(Purchase), SWO(Redemption), RED(Redemption)</p>
                        <p>
                            All the Records will be imported. In Case of error only those records will be shown in the below table with error, rest has been imported.
                            Please make necessary changes and upload again.
                        </p><br/>
                        <p>If you have downloaded our Demo Excel File, please delete the data in Row 2 before adding and uploading your data.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">OK, Got It!</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Bootstrap modal -->
    </div>
    <div class="modal fade" id="bank_modal_form" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"></h3>
                </div>
                <div class="modal-body form">
                    <form action="#" id="add_bank_form" class="form-horizontal" data-validate="parsley">
                        <div class="form-body">
                            <div class="form-group">
                                <input type="hidden" name="client_id" id="client_id" value="">
                                <input type="hidden" name="scheme_id" id="scheme_id" value="">
                                <label class="control-label col-md-3">Type</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="mf_type" id="mf_type" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Folio Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="folio_number" id="folio_number" value="" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Bank Name</label>
                                <div class="col-md-8">
                                    <select class="populate" name="bank_id" id="bank_id" style="width: 100%" required="required">
                                        <option disabled selected value="">Select Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Branch</label>
                                <div class="col-md-8">
                                    <select class="populate" name="branch" id="branch" style="width: 100%" required="required">
                                        <option disabled selected value="">Select Branch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Account Number</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="account_number" id="account_number" style="width: 100%" required="required">
                                        <option disabled selected value="">Select Account Number</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Cheque Number</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="cheque_number" id="cheque_number" required="required">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="save_bank_details()" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    $(document).ready(function(){
        //for MF Valuation button
        var button;
        $('.ladda-button').click(function(e){
            button = this;
        });

        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});

        <?php if($import_data != null) { ?>
            table = $("#table").DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, 1000, -1], [10, 25, 50, 100, 500, 1000, "All"]]
            });
        <?php } ?>

        <?php if($pip_or_ipo_data != null) { ?>
            $('#pip_table').DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, 1000, -1], [10, 25, 50, 100, 500, 1000, "All"]],
                "aoColumnDefs": [{ "bSortable": false, "aTargets": [  ] },
                    { "bSearchable": false, "aTargets": [  ] }]
            });
        <?php } ?>

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import new Mutual Fund Details?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });

        $("#importHelp").click(function(){
            //show bootstrap modal
            $("#importModal").modal('show');
            //set title to modal
            $(".modal-title").text('Import Help');
        });
        $('#bank_id').change(function(){
            $.ajax({
                url: '<?php echo site_url("broker/Banks/get_branch");?>',
                type: 'post',
                data: {bankID: this.value, clientID: $('#client_id').val()},
                dataType: 'json',
                success:function(data)
                {
                    var option = '<option disabled selected value="">Select Branch</option>';
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
                    var option = '<option disabled selected value="">Select Account Number</option>';
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


        //for MF Valuation
        $("#fund_val_btn").on("click", function() {
            bootbox.confirm("Proceed with Valuation of Mutual Funds? This will take some time to finish, so please be patient.", function(result) {
                if(result) {
                    var l = Ladda.create(button);
                    l.start();
                    $("#note").addClass("text-success");
                    $("#note").html("Note: Mutual Fund Valuation takes some time to process and calculate. Please be patient, we'll notify you when it's complete.")
                    $.ajax({
                        url: "<?php echo site_url('broker/Mutual_funds/mf_valuation');?>",
                        type: 'post',
                        data: {'brokerID':$("#brokerID").val(), 'transID':$("#transID").val()},
                        dataType: 'json',
                        success: function(data)
                        {
                            $.pnotify({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                hide: true
                            });
                            l.stop();
                            $("#note").removeClass("text-danger");
                            $("#note").removeClass("text-warning");
                            $("#note").removeClass("text-primary");
                            if(data['type'] == 'success') {
                                $("#fund_val_btn").hide();
                                $("#note").addClass("text-primary");
                                $("#note").html("Valuation is completed successfully!");
                            } else {
                                $("#note").addClass("text-danger");
                                $("#note").html("Sorry! Valuation did not complete successfully. Please click on the Valuation button to try again.");
                            }
                        },
                        error: function(jqXRR, textStatus, errorThrown)
                        {
                            console.log(jqXRR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            bootbox.alert('Something went wrong. Please try again.');
                            l.stop();
                            $("#note").html("");
                        }
                    });
                }
            });

        });
    });

    //popup to add policy details
    /*function add_bank_details(clientID, folioNum, schemeID, mfType)
    {
        //reset form on modals
        $("#add_bank_form")[0].reset();
        $("#folio_number").val(folioNum);
        $("#mf_type").val(mfType);
        $("#client_id").val(clientID);
        $("#scheme_id").val(schemeID);
        $.ajax({
            url: '<?php //echo site_url('broker/Banks/get_client_banks'); ?>',
            dataType: 'json',
            type: 'post',
            data: {clientID: clientID},
            success: function(data)
            {
                var option = '<option selected disabled value="">Select Bank</option>';
                $.each(data['banks'], function(i, item){
                    option = option + "<option value = '" + item.bank_id + "'>" + item.bank_name + "</option>";
                });
                $('#bank_id').html(option);
            },
            error: function(data)
            {
                console.log(data);
            }
        });
        $("#bank_id").select2("val","");
        $("#branch").select2("val","");
        $("#account_number").val("");
        //show bootstrap modal
        $("#bank_modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Bank Details');
    }*/

    function save_bank_details()
    {
        var valid = $('#add_bank_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/mutual_funds/save_bank_details'); ?>',
                dataType: 'json',
                type: 'post',
                data: $('#add_bank_form').serialize(),
                success: function(data)
                {
                    $.pnotify({
                        title: data['title'],
                        text: data['text'],
                        type: 'success',
                        hide: true
                    });
                },
                error: function(data)
                {
                    console.log(data);
                }
            });
        }
    }
</script>