<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/dashboard');?>">Dashboard</a></li>
                <li class="active">App Images</li>
            </ol>
            <h1>App Images</h1>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>App Images Table</h4>

                        </div>
                        <div class="panel-body collapse in table-responsive">
                           

                            <button class="btn btn-success" onclick="javascript:location.href='<?php echo base_url(); ?>broker/AppImages/add';"><i class="fa fa-plus"></i> Add Image</button>


                            <br /><br />

                            <form id="report_form" method="get"></form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-full-width" id="table">
                                    <thead>
                                    <tr>
                                        <th class="action-col-3">Action</th>
                                        <!--<th>Client ID</th> -->
                                        <th>Filename</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($images as $k=>$v)
                                        {?>
                                        <tr>
                                            <td>
                                                <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>broker/AppImages/delete?filename=<?php echo $v['filename'];?>" onclick="return confirm('Are you sure to delete this app image?');">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                            <td><?php echo $v['filename'];?></td>
                                            <td><a href="<?php echo $v['path'];?>" target="_blank"><image src="<?php echo $v['path'];?>" width="150px" height="150px"/></a></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table><!--end table-->
                                <div class="loading-data"><img src="<?php echo base_url('assets/users/img/load.gif');?>"><span>Please wait, loading data...</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    
</script>
