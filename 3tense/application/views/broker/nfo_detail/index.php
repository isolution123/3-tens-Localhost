<div id="page-content">

    <div id='wrap'>

        <div id="page-heading">

            <ol class="breadcrumb">

                <li><a href="index.php">Dashboard</a></li>

                <li>Masters</li>

                <li class="active">NFO Details</li>

            </ol>

            <h1>NFO Details</h1>

        </div>

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-indigo">

                        <div class="panel-heading">
                            <h4>NFO Details</h4>
                        </div>
                        <div class="panel-body collapse in">
                            <form action="<?php echo base_url('broker/Nfo_detail/nfo_import') ?>" id="import_form" method="post" class="form-horizontal row-border" enctype="multipart/form-data">
                                <div class="row">
                                    <input type="hidden" name="Import" value="Import">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">NFO Description</label>
                                        <div id="file_type" class="col-sm-8">
                                             <textarea name="nfo_description" id="nfo_description" placeholder="NFO Description" required class="form-control"></textarea>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Text color</label>
                                        <div id="file_type" class="col-sm-8">
                                           <input type="color" name="desc_color" id="desc_color"  required class="form-control"/>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label for="import_stake" class="col-sm-2 control-label">File Upload</label>
                                        <div class="col-sm-8" style="float: none; display: inline-block">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group">
                                                    
                                                        <input type="file" tabindex="1" name="import_mf" id="import_mf" accept="image/png, image/gif, image/jpeg" >
                                                    
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <button type="submit" id="fund_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                                                    <i class="fa fa-upload"></i> Upload
                                                </button>
                                                
                                                
                                                    <a href="<?php echo base_url('broker/Nfo_detail/nfo_delete') ?>" class="btn btn-success" style="margin-top: 20px;">
                                                        <i class="fa fa-trash"></i>  Delete
                                                </a>
                                            </div>
                                            
                                            <div class="col-sm-12">
                                                <br/><br/>
                                                <span id="note" class="text-danger"></span>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                                    
                                
                            </form>
                          
                        </div>

                    </div>

                </div>

            </div>

            <?php //include 'occupation_add.php'; ?>

        </div>    <!-- container -->

    </div> <!--wrap -->

</div> <!-- page-content -->
