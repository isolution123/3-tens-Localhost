<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Holding</li>
            </ol>
            <h1>Import Holding</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Equity/import_holding') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Holding</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Holding for Equity<br/>
                        Please Note: The Excel Sheet must contain 'Party Code', 'Script Code', 'Script Name', 'Total Quantity', 'Party Name' columns as mentioned to import<br/><br/>
                        <a href="<?=base_url('uploads/imports/Holding-import.xlsx');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import_holding" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-6" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_holding" id="import_holding">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <button type="submit" tabindex="2" id="fund_btn" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <?php if($holding_data != null):?>
                            <div class="panel panel-midnightblue" style="margin-top: 20px">
                                <div class="panel-heading">
                                    <h4>Holdings Not Imported</h4>
                                </div>
                                <div class="col-md-10  no-border" style="margin-top: 20px;">
                                    <p>
                                        Records That Were not Imported.
                                        <br/>
                                        Following are the Holding details which were not imported successfully.
                                        <br/>
                                    </p>
                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="holding_table">
                                            <thead>
                                            <tr>
                                                <th>Party Code</th>
                                                <th>Script Code</th>
                                                <th>Script Name</th>
                                                <th>Total Quantity</th>
                                                <th>Party Name</th>
                                                <th>Error</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($holding_data as $holding): ?>
                                            <tr>
                                                <td><?php echo $holding[1];?></td>
                                                <td><?php echo $holding[2];?></td>
                                                <td><?php echo $holding[3];?></td>
                                                <td><?php echo $holding[4];?></td>
                                                <td><?php echo $holding[5];?></td>
                                                <td><?php echo $holding[6];?></td>
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
        table = $("#holding_table").DataTable();

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import new Holding details?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });
    });
</script>