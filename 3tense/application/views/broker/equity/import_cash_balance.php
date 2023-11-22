<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Cash Balance</li>
            </ol>
            <h1>Import Cash Balance</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Equity/import_cash_balance') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Cash Balance</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Cash Balance for Equity/Trading<br/>
                        Please Note: The Excel Sheet must contain 'Client Code', 'Ledger Dr/Cr' and 'Party Name' columns as mentioned to import<br/><br/>
                        <a href="<?=base_url('uploads/imports/Ledger-Balance-import.xlsx');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import_cash_bal" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-6" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_cash_bal" id="import_cash_bal">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <button type="submit" tabindex="2" id="fund_btn" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <?php if($cash_bal_data != null):?>
                            <div class="panel panel-midnightblue" style="margin-top: 20px">
                                <div class="panel-heading">
                                    <h4>Cash Balance Not Imported</h4>
                                </div>
                                <div class="col-md-10  no-border" style="margin-top: 20px;">
                                    <p>
                                        Records That Were not Imported.
                                        <br/>
                                        Following are the Client Code, Ledger Dr/Cr and Party Name not imported successfully.
                                        <br/>
                                    </p>
                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="cash_bal_table">
                                            <thead>
                                            <tr>
                                                <th>Client Code</th>
                                                <th>Ledger Dr/Cr</th>
                                                <th>Party Name</th>
                                                <th>Error</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($cash_bal_data as $cash_bal): ?>
                                            <tr>
                                                <td><?php echo $cash_bal[1];?></td>
                                                <td><?php echo $cash_bal[2];?></td>
                                                <td><?php echo $cash_bal[3];?></td>
                                                <td><?php echo $cash_bal[4];?></td>
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
        </form>
    </div>
</div>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    $(document).ready(function(){
        table = $("#cash_bal_table").DataTable();

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import Cash Balance details?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });
    });
</script>