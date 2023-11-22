<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Client MF Folio Master</li>
            </ol>
            <h1>Import Client MF Folio Master</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Mutual_fund_schemes/Client_mf_folio_import') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Client MF Folio Master</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Details for Client MF Folio Master.<br/>
                        <!-- <a href="javascript:void(0)" id="importHelp" class="alert-link"><i class="fa fa-lightbulb-o"></i> Need Help in upload of excel?</a> --><br/>
                        <a href="<?=base_url('uploads/imports/MF Folio Master.xlsx?v=1');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                  <!--      Note: Please delete the Row 2 data in the Excel file before adding and importing your data.-->

                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import" class="col-sm-2 control-label">File Upload</label>
                            <div class="col-sm-8" style="float: none; display: inline-block">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group">
                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" tabindex="1" name="import_FDs" id="import_FDs">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>

                                <button type="submit" id="import_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="col-sm-12" style="float: none; display: inline-block">
                            <?php if($import_data != null):?>
                                <div class="panel panel-midnightblue" style="margin-top: 20px">
                                    <div class="panel-heading">
                                        <h4>Client Account Mandate Details Not Imported</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            Client Account Mandate Details That Were not Imported.
                                            <br/>
                                            Please note the Reason and make necessary changes. <a onclick=""></a>
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                                <thead>
                                                <tr>
                                                    <th>MANDATE CODE</th>
                                                    <th>CLIENT CODE</th>
                                                    <th>CLIENT NAME</th>
                                                    <th>MEMBER CODE</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($import_data as $rec): ?>
                                                    <tr>
                                                      <td><?php echo $rec[0];?></td>
                                                        <td><?php echo $rec[1];?></td>
                                                        <td><?php echo $rec[2];?></td>
                                                        <td><?php echo $rec[3];?></td>
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
    </div>
</div>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    $(document).ready(function(){
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        table = $("#table").DataTable();

        $("#import_form").submit(function(e){
            var currentForm = this;
            e.preventDefault();
            bootbox.confirm("Do you want to import new Client Account Mandate Details?", function(result) {
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

    });
</script>
