<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Imports</li>
                <li class="active">Import SIP Details</li>
            </ol>
            <h1>Import SIP Details</h1>
        </div>
    </div>
    <div class="container">
        
        <form action="<?php echo base_url('broker/SIP/SIP_details_import') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
            <div class="panel panel-midnightblue">
                <div class="panel-heading">
                    <h4>Import SIP Details</h4>
                </div>
                <div class="panel-body collapse in">
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Import Details for SIP.<br/>
                        <a href="javascript:void(0)" id="importHelp" class="alert-link"><i class="fa fa-lightbulb-o"></i> Need Help in upload of excel?</a><br/><br/>
                        <a href="<?=base_url('uploads/imports/SIP_other_import.xlsx');?>" id="demoFile" class="alert-link"><i class="fa fa-file-excel-o"></i> Download our Demo Import File (for Other option)</a><br/>
                        Note: Please delete the Row 2 data in the Excel file before adding and importing your data.

                    </div>
                    <div class="row">
                        <input type="hidden" name="Import" value="Import">
                        <div class="form-group">
                            <label for="import" class="col-sm-2 control-label">Select RTA</label>
                            <div id="rta_list" class="col-sm-4">
                          <select class="form-control select2" name="rta_list" id="rta_list" required="required">
                              <option disabled selected value=" ">Select Type of RTA file</option>
                              <!--<option value="cl_excel">Imoprt Client Details Excel</option>-->
                              <option value="cams_excel">Import SIP Details From Cams</option>
                              <option value="karvy_excel">Import SIP Details From Karvy</option>
                              <option value="frank_excel">Import SIP Details From Franklin</option>
                              <option value="sundaram_excel">Import SIP Details From Sundaram</option>
                              <option value="other_import">Import SIP Details From Other</option>
                          </select>
                        </div><br><br>
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
                                            <input type="file" tabindex="1" name="import_Sip" id="import_Sip">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <button type="submit" id="import_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                </div>
                                
                                 
                            </div>
                            <div class="col-sm-12" style="float: none; display: inline-block">
                            <?php if($import_data != null):?>
                                <div class="panel panel-midnightblue" style="margin-top: 20px">
                                    <div class="panel-heading">
                                        <h4>SIP Details Not Imported</h4>
                                    </div>
                                    <div class="col-md-10  no-border" style="margin-top: 20px;">
                                        <p>
                                            FD Details That Were not Imported.
                                            <br/>
                                            Following are the Folio ID, Client Name  of records which were not imported.
                                            <br/>
                                            Please note the Reason and make necessary changes. <a onclick=""></a>
                                        </p>
                                        <div class="table-responsive">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table">
                                                <thead>
                                                <tr>
                                                    <th>Folio ID</th>
                                                    <th>Client Name</th>
                                                    <th>Reason</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($import_data as $rec): ?>
                                                    <tr>
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
                              <td>PRODUCT_CODE (M)</td>
                              <td>SCHEME_NAME (M)</td>
                            </tr>
                            <tr>
                              <td>FOLIO_ID (M)</td>
                              <td>INV_NAME</td>
                            </tr>
                            <tr>
                              <td>TXN_TYPE</td>
                              <td>SIP_AMOUNT (M)</td>
                            </tr>
                            <tr>
                              <td>TXN_DATE (M)</td>
                              <td>START_DATE (M)</td>
                            </tr>
                            <tr>
                                <td>END_DATE (M)</td>
                                <td>FREQUENCY (M)</td>
                            </tr>
                            <tr>
                                <td>SIP_REG_DATE</td>
                                <td>BANK_NAME (M)</td>
                            </tr>
                            <tr>
                                <td>BANK_ACC_NO</td>
                                <td>PAN_NO (M)</td>
                            </tr>
                            <tr>
                                <td>KYC_STATUS</td>
                                <td>MAN_DOCUMENT</td>
                            </tr>
                            <tr>
                                <td>DOC_ID_NUM</td>
                                <td>ACCOUNT_NO (M)</td>
                            </tr>
                            <tr><td>SIP_DATE</td></tr>


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
            bootbox.confirm("Do you want to import new SIP Details?", function(result) {
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
