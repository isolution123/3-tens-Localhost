<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Insurance Real Stake</li>
            </ol>
            <h1>Import Insurance Real Stake</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Insurances/real_stake') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Real Stake</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        Import Real Stake for insurance.<br/>
                        Please Note: The Excel Sheet must contain 'Policy Number', 'Year', 'Bonus' and 'Amount' columns as mention to import
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import_stake" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-6" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_stake" id="import_stake">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <button type="submit" id="fund_btn" tabindex="2" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <!--<div class="col-md-8  no-border" style="float: none">
                                <input type="file" name="import_stake" id="import_stake" size="150">
                                <p class="help-block">Only Excel File Import.</p>
                                <button type="submit" id="fund_btn" class="btn btn-success" style="margin-left: 20px;">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                            </div>-->
                            <?php if($stake_data != null):?>
                                <div class="panel panel-midnightblue" style="margin-top: 20px">
                                    <div class="panel-heading">
                                        <h4>Real Stake Not Imported</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            Real Stake That Were not Imported.
                                            <br/>
                                            Following are the Policy Number, Year, Bonus and Amount not imported successfully.
                                            <br/>
                                            Please first enter the policy number of the client and then import
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                                <thead>
                                                <tr>
                                                    <th>Policy Number</th>
                                                    <th>Year</th>
                                                    <th>Bonus</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($stake_data as $fund): ?>
                                                    <tr>
                                                        <td><?php echo $fund[1];?></td>
                                                        <td><?php echo $fund[2];?></td>
                                                        <td><?php echo $fund[3];?></td>
                                                        <td><?php echo $fund[4];?></td>
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
            bootbox.confirm("Do you want to import new Real Stake Details?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });
    });
</script>