<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Insurance Fund Option</li>
            </ol>
            <h1>Import Insurance Fund Option</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Insurances/import_fund') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Fund Option</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Fund Option for insurance.<br/>
                        Please Note: The Excel Sheet must contain 'Policy Number', 'Fund Option' and 'Value' columns as mention to import<br/><br/>
                        <a href="<?=base_url('uploads/imports/Fund-value-import.xlsx');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import_fund" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-4" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_fund" id="import_fund">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <button type="submit" tabindex="2" id="fund_btn" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <?php if($fund_data != null):?>
                            <div class="panel panel-midnightblue" style="margin-top: 20px">
                                <div class="panel-heading">
                                    <h4>Fund Option Not Imported</h4>
                                </div>
                                <div class="col-md-10  no-border" style="margin-top: 20px;">
                                    <p>
                                        Fund Option That Were not Imported.
                                        <br/>
                                        Following are the policy number, fund option and fund value not imported successfully.
                                        <br/>
                                        Please first enter the policy number of the client and then import
                                    </p>
                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="fundtable">
                                            <thead>
                                            <tr>
                                                <th>Policy Number</th>
                                                <th>Fund Option</th>
                                                <th>Fund Value</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($fund_data as $fund): ?>
                                            <tr>
                                                <td><?php echo $fund[1];?></td>
                                                <td><?php echo $fund[2];?></td>
                                                <td><?php echo $fund[3];?></td>
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
        table = $("#table").DataTable();

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import new Fund Option Details?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });
    });
</script>