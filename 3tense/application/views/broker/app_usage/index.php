<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="index.php">Dashboard</a></li>
                <li>Reports</li>
                <li class="active">App Usage</li>
            </ol>
            <h1>App Usage Report</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>App Usage</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                App Usage Report will show data for last 45 days only.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            
                            <form action="AppUsage" id="app_usage_filters" method="post">
                                <div class="form-group row">
                                    <!-- Default input -->
                                    <label class="col-sm-1 col-form-label">Client : </label>
                                   <div class="col-sm-3">
                                        <select id="client_id" name="client_id" class="select2" style="width: 80%">
                                            <option value=''>--Select--</option>
                                            <?php
                                            foreach ($clients as $client)
                                            {
                                                //echo '<pre>';print_r($client);die;
                                                if($sel_client == $client->client_id)
                                                {
                                                    echo '<option value="'.$client->client_id.'" selected>'.$client->name.'</option>';
                                                }
                                                else
                                                {
                                                    echo '<option value="'.$client->client_id.'" >'.$client->name.'</option>';
                                                }
                                                
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <label for="operation" class="col-sm-2 col-form-label">Operation : </label>
                                    <div class="col-sm-3">
                                        <select id="operation" name="operation" class="select2" style="width: 80%">
                                            <option value=''>--Select--</option>
                                            <?php
                                            foreach ($operations as $operation)
                                            {
                                                if($sel_operation == $operation)
                                                {
                                                    echo '<option value="'.$operation.'" selected>'.$operation.'</option>';
                                                }
                                                else
                                                {
                                                    echo '<option value="'.$operation.'" >'.$operation.'</option>';
                                                }
                                                
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                     <div class="col-sm-3">
                                      <button type="submit" class="btn btn-primary">Search</button>
                                      <!--<button type='reset' class="btn btn-primary" onclick='document.getElementById("app_usage_filters").reset();'>Reset</button>-->
                                    </div>
                                  </div>
                                  <!-- Grid row -->

                            </form>
                            
                            <br /><br />
                            
                            
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" style="width:100%;" id="table">
                                    <thead>
                                    <tr>
                                        <th>Client ID</th>
                                        <th>Client Name</th>
                                        <th>Operation</th>
                                        <th>Access Datetime</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($usage as $log)
                                        {
                                            //echo '<pre>';print_r($log);die;
                                            echo '<tr>';
                                            echo '<td>'.$log->client_id.'</td>';
                                            echo '<td>'.$log->name.'</td>';
                                            echo '<td>'.$log->operation.'</td>';
                                            echo '<td>'.$log->created_datetime.'</td>';
                                            echo '</td>';
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Client ID</th>
                                            <th>Client Name</th>
                                            <th>Operation</th>
                                            <th>Access Datetime</th>
                                        </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
   $(document).ready(function(){
        $("#table").DataTable({
             "ordering": false
        });
        
        $('.select2').select2();
    });
</script>
