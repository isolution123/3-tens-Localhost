<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">BSE Password</li>
            </ol>
            <h1>Update BSE Password</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Update BSE Password</h4>
                        </div>
                        <div class="panel-body collapse in">
                            
                            <?php
                            if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){
                            ?>
                            <button class="btn btn-success" onclick="add_bsc_password(false)" tabindex="1"><i class="fa fa-plus"></i> Edit BSE Password</button>
<?php
}
else{
?>
                          <button class="btn btn-success disable_btn" tabindex="1"><i class="fa fa-plus"></i> Edit BSE Password</button>
<?php
}
?>

                            <br /><br />
                            <div>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                      <span class="input-group-btn">
                                        <p class="btn btn-default">BSE Password: </p>
                                      </span>
                                      <input type="text" class="form-control" value="<?php echo $data->BSCPassword; ?>" readonly>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->