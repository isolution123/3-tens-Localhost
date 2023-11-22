<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import Client Details</li>
            </ol>
            <h1>Import Client Details</h1>
        </div>
    </div>
    <div class="container">
        <form action="<?php echo base_url('broker/Clients/clients_import') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import Client Details</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Details for Clients.<br/>
                        <a href="javascript:void(0)" id="importHelp" class="alert-link"><i class="fa fa-lightbulb-o"></i> Need Help in upload of excel?</a><br/><br/>
                        <a href="<?=base_url('uploads/imports/Client-import.xls');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.

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
                                            <input type="file" tabindex="1" name="import_clients" id="import_clients">
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
                                        <h4>Client Details Not Imported</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            Client Details That Were not Imported.
                                            <br/>
                                            Following are the Family Name, Client Name and PAN Number of records which were not imported.
                                            <br/>
                                            Please note the Reason and make necessary changes. <a onclick=""></a>
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                                <thead>
                                                <tr>
                                                    <th>Family Name</th>
                                                    <th>Client Name</th>
                                                    <th>PAN Number</th>
                                                    <th>Reason</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($import_data as $rec): ?>
                                                    <tr>
                                                        <td><?php echo $rec[1];?></td>
                                                        <td><?php echo $rec[2];?></td>
                                                        <td><?php echo $rec[3];?></td>
                                                        <td><?php echo $rec[4];?></td>
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
                                <td>Family Name (M)</td>
                                <td>House/Flat No</td>
                            </tr>
                            <tr>
                                <td>Client Name (M)</td>
                                <td>Street</td>
                            </tr>
                            <tr>
                                <td>Pan No (M)</td>
                                <td>Area</td>
                            </tr>
                            <tr>
                                <td>Client Type (M)</td>
                                <td>City (M)</td>
                            </tr>
                            <tr>
                                <td>Occupation (M)</td>
                                <td>State (M)</td>
                            </tr>
                            <tr>
                                <td>Head Of Family (M)</td>
                                <td>Pincode</td>
                            </tr>
                            <tr>
                                <td>Relation W/ HOF</td>
                                <td>Mobile</td>
                            </tr>
                            <tr>
                                <td>Report Order</td>
                                <td>Telephone</td>
                            </tr>
                            <tr>
                                <td>Date Of Birth</td>
                                <td>Email ID (M)</td>
                            </tr>
                            <tr>
                                <td>Anniversary</td>
                                <td>Username (M)</td>
                            </tr>
                            <tr>
                                <td>Spouse Name</td>
                                <td>Password (M)</td>
                            </tr>
                            <tr>
                                <td>Children Name</td>
                                <td>Passport No</td>
                            </tr>
                            <tr>
                                <td>Client Category</td>
                                <td>Commencement Date</td>
                            </tr>
                        </table>
                        <p>Note: (M) These fields are Mandatory and please don't mention (M) in column name of your excel</p>
                        <p>Make sure the data you are entering in the excel must exist in the application</p>
                        <p>
                            All the Records will be imported. In case of error, only those records will be shown in the below table with error, rest has been imported.
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
            bootbox.confirm("Do you want to import new Client Details?", function(result) {
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