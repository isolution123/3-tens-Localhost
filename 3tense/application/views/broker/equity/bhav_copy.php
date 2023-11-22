<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Bhav Copy</li>
            </ol>
            <h1>Import Bhav Copy</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Equity/import_bhav_copy') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Bhav Copy</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        Import Bhav Copy for Equity.<br/>
                        Please Note: The Excel Sheet must contain 'SC_CODE', 'SC_NAME' and 'CLOSE' columns as mentioned to import
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import_scrip" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-4" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_scrip" id="import_scrip">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <button type="submit" tabindex="2" id="fund_btn" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <?php if($scrip_data != null):?>
                            <div class="panel panel-midnightblue" style="margin-top: 20px">
                                <div class="panel-heading">
                                    <h4>Bhav Copy Not Imported</h4>
                                </div>
                                <div class="col-md-10  no-border" style="margin-top: 20px;">
                                    <p>
                                        Scrips That Were not Imported.
                                        <br/>
                                        Following are the Scrip Code, Scrip Name and Close Rate not imported successfully.
                                        <br/>
                                    </p>
                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="scriptable">
                                            <thead>
                                            <tr>
                                                <th>Scrip Code</th>
                                                <th>Scrip Name</th>
                                                <th>Close Rate</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($scrip_data as $scrip): ?>
                                            <tr>
                                                <td><?php echo $scrip[1];?></td>
                                                <td><?php echo $scrip[2];?></td>
                                                <td><?php echo $scrip[3];?></td>
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
        table = $("#scriptable").DataTable();

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import new Bhav Copy?", function(result) {
                if (result) {
                    currentForm.submit();
                }
            });
        });
    });
</script>