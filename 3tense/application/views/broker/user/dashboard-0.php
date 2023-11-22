<div id="page-content">
    <div id='wrap'>
        <!--<div id="page-heading">
            <ol class="breadcrumb">
                <li class="active">Dashboard</li>
            </ol>
            <h1>Dashboard</h1>
        </div>-->
        <div class="container">
                    <div class="row">
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel-body">
                                    <div class="text-center"><h4>Clients</h4></div>
                                    <canvas id="client-chart"></canvas>
                               </div>
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel  panel-body">
                                    <div class="text-center"><h4>Insurance</h4></div>
                                    <canvas id="insurance-chart"></canvas>
                               </div>
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel panel-body">
                                    <div class="text-center"><h4>Mutual Funds</h4></div>
                                    <canvas id="mf-chart"></canvas>
                               </div>
                            </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel panel-body">
                                    <div class="text-center"><h4>Fixed Deposit</h4></div>
                                    <canvas id="fd-chart"></canvas>
                               </div>
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel panel panel-body">
                                    <div class="text-center"><h4>Equity</h4></div>
                                    <canvas id="equity-chart"></canvas>
                               </div>
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="panel panel-body">
                                    <div class="text-center"><h4>Real Estate</h4></div>
                                    <canvas id="property-chart"></canvas>
                               </div>
                            </div>
                    </div>

                    <div class="row">
                        <div class="panel panel-green">
                        <div class="panel-heading">
                            <h4><i class="fa fa-bullseye"></i> Focus 5</h4>
                        </div>
                        <div class="panel-body">

                                <div id="accordioninpanel" class="accordion-group">
                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinOne"><h4>Mutual Funds</h4></a>
                                    <div id="collapseinOne" class="collapse">
                                       <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Purchase</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="mf_pur" width="100%">
                                                <thead>
                                                    <tr>
                                                       <!-- <th>Action</th> -->
                                                        <th>Client Name</th>
                                                        <th>Scheme Name</th>
                                                        <th>Folio No.</th>
                                                        <th>Trans. Date</th>
                                                        <th>Amount</th>
                                        <!-- <th>Adjustment</th>
                                        <th>Adjustment Ref. No.</th> -->
                                                     </tr>
                                                </thead>
                                            </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Redemption</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="mf_red">
                                                <thead>
                                                  <tr>
                                                        <th>Client Name</th>
                                                        <th>Scheme Name</th>
                                                        <th>Folio Number</th>
                                                        <th>Transaction Date</th>
                                                        <th>Amount</th>
                                                  </tr>
                                                </thead>
                                                </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                </div>
                            </div>

                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinTwo"><h4>Insurance Policies</h4></a>
                                    <div id="collapseinTwo" class="collapse">
                                                                               <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>New Policy</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="ins_new">
                                                <thead>
                                                     <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Plan Name</th>
                                                    <th>Policy Number</th>
                                                    <?php //<th>Sum Assured</th>*/ ?>
						    <th>Premium Amt</th>

                                                     </tr>
                                                </thead>

                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Mature</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="ins_mat">
                                               <thead>
                                                     <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Plan Name</th>
                                                    <th>Policy Number</th>
                                                    <th>Maturity Amt</th>
                                                     </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel" href="#collapseinThree"><h4>Fixed Deposit</h4></a>
                                    <div id="collapseinThree" class="collapse">
                                         <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>New (Held)</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped"  id="fd_new">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Company Name</th>
                                                    <th>Ref No.</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Mature</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="fd_mat">
                                                <thead>
                                                   <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Company Name</th>
                                                    <th>Ref No.</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="accordioninpanel2" class="accordion-group">
                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpane2" href="#collapseinFour"><h4>Asset & Liability</h4></a>
                                    <div id="collapseinFour" class="collapse">
                                                                                 <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>New</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="al_new">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Product Name</th>
                                                    <th>Ref No</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Close</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="al_mat">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Product Name</th>
                                                    <th>Ref No</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel2" href="#collapseinFive"><h4>Equity</h4></a>
                                    <div id="collapseinFive" class="collapse">
                                                                                 <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Funds Added</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="add_equity">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Details</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Funds Withdrawal</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="withdraw_equity">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Details</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <a class="accordion-title" data-toggle="collapse" data-parent="#accordioninpanel2" href="#collapseinSix"><h4>Highest Negative Balance - Equity</h4></a>
                                    <div id="collapseinSix" class="collapse">
                                                                                 <div class="panel panel panel-body">
                                            <div class="accordion-body">
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Highest Negative Balance</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped" id="negative_equity" style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client</th>
                                                    <th>Broker Name</th>
                                                    <th>Client Code</th>
                                                    <th>Balance</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>BSE Chart</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped">
                                                <tbody>
                                                  <tr>
                                                    <td><img src="https://chart.finance.yahoo.com/t?s=%5eBSESN&amp;lang=en-IN&amp;region=IN&amp;width=480&amp;height=300" alt="S&amp;P BSE SENSEX (^BSESN)" width="100%" height="100%"></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </div>
                            </div>
                    </div>

                    <!--<div class="row">
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-toyo" href="<?php echo site_url('broker/clients');?>">
                                <div class="tiles-heading">Clients</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-group"></i>
                                    <div class="text-center"><?php echo $get_info['num_clients'];?></div>
                                    <small>Clients</small>
                                </div>
                                <div class="tiles-footer">Manage Clients</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-success" href="<?php echo site_url('broker/insurances');?>">
                                <div class="tiles-heading">Insurance</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <div class="text-center"><?php echo $get_info['num_ins'];?></div>
                                    <small>Matured Insurances till date</small>
                                </div>
                                <div class="tiles-footer">Go to Insurances</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-orange" href="<?php echo site_url('broker/insurances');?>">
                                <div class="tiles-heading">Insurance</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-anchor"></i>
                                    <div class="text-center"><?php echo $get_info['num_ins_lapsed'];?></div>
                                    <small>Lapsed Insurances till date</small>
                                </div>
                                <div class="tiles-footer">Go to Insurances</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-alizarin" href="<?php echo site_url('broker/fixed_deposits');?>">
                                <div class="tiles-heading">Fixed Deposit</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-shopping-cart"></i>
                                    <div class="text-center"><?php echo $get_info['num_fd'];?></div>
                                    <small>Active Fixed Deposits</small>
                                </div>
                                <div class="tiles-footer">Manage Fixed Deposit</div>
                            </a>
                        </div>
                    </div>-->

                    <div class="row">
                        <div class="col-md-6">
                             <div class="panel panel-grape">
                                <div class="panel-heading">
                                    <h4><i class="icon-highlight fa fa-check"></i> Reminder List</h4>
                                </div>
                                <div class="panel-body">
                                    <ul class="panel-tasks">
                                        <?php
                                        foreach($get_info['rem_list'] as $rem):
                                            $rem_date = $rem->reminder_date;
                                            $date = DateTime::createFromFormat('d/m/Y', $rem_date);
                                            $rem_date = $date->format('Y-m-d');
                                            $diff = strtotime(date('Y-m-d')) - strtotime($rem_date);
                                            $num_days = floor($diff/86400);
                                            if($num_days <= 14)
                                            {
                                                if($num_days == 0)
                                                    $rem_days = '<span class="label label-success">Today</span>';
                                                else
                                                    $rem_days = '<span class="label label-info">'.$num_days.' Days</span>';
                                            }
                                            else
                                            {
                                                $num_weeks = floor($diff / 604800);
                                                if($num_weeks <= 5)
                                                {
                                                    $rem_days = '<span class="label label-warning">'.$num_weeks.' Weeks</span>';
                                                }
                                                else
                                                {
                                                    $num_months = floor($diff / 2678400);
                                                    $rem_days = '<span class="label label-danger">'.$num_months.' Months</span>';
                                                }
                                            }
                                            ?>
                                            <li class="item-primary">
                                                <label>
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <span class="task-description"><?php echo $rem->reminder_message; ?></span>
                                                    <br/>
                                                    <?php echo $rem_days;?>
                                                </label>
                                                <div class="options todooptions">
                                                    <div class="btn-group" style="">
                                                        <a class="btn btn-default btn-xs" href="javascript:void(0)" title="View Details"
                                                           onclick="view_reminder('<?php echo $rem->reminder_id; ?>')">
                                                            <i class="fa fa-eye"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                </div>
                            </div>
                           <!-- <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4>Current Commodity Price in grams</h4>
                                </div>
                                <div class="panel-body">
                                    <div id="com-bar"></div>
                                </div>
                            </div>-->
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4>Insurance Status Till Date</h4>
                                </div>
                                <div class="panel-body">
                                    <div id="ins-donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-6">
                            <!--<div class="panel panel-grape">
                                <div class="panel-heading">
                                    <h4>Top 3 Investors</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12 clearfix">
                                            <h4 id="chart_head" class="pull-left" style="margin: 0 0 20px;">Insurance </h4>
                                            <div class="btn-group pull-right">
                                                <a href="javascript:void(0);" id="btn-ins" class="btn btn-default btn-sm active">Insurance</a>
                                                <a href="javascript:void(0);" id="btn-fd" class="btn btn-default btn-sm ">Fixed Deposit</a>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="ins_inv" style="height:250px;"></div>
                                            <div id="fd_inv" style="height:250px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="rem_modal_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="remID" id="remID" type="hidden">
                            <label class="control-label col-md-3">Date</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remDate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Subject</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remSub">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Client Name</label>
                            <div class="col-md-9">
                                <input type="text" disabled class="form-control" id="remClientName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Message</label>
                            <div class="col-md-9">
                                <textarea disabled class="form-control" id="remMsg"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSnooze" onclick="snooze()" class="btn btn-primary"> <i class="fa fa-clock-o"></i> Snooze</button>
                <button type="button" id="btnComp" class="btn btn-success"><i class="fa fa-check"></i> Complete</button>
                <button type="button" id="btnMail" class="btn btn-sky"><i class="fa fa-envelope"></i> Send Mail</button>
                <button type="button" id="btnSMS" class="btn btn-midnightblue"><i class="fa fa-mobile"></i> Send SMS</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban"></i> Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Bootstrap modal -->
<div class="modal fade" id="assign_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="assign_modal_title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_assign" class="form-horizontal" data-validate="parsley">
                    <div class="form-body">
                        <div class="form-group">
                            <input name="remAssignID" id="remAssignID" type="hidden">
                            <label class="control-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea id="remRemark" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Snooze Date</label>
                            <div class="col-md-9">
                                <input type="text" id="remSnoozeDate" class="form-control date" data-inputmask="'alias':'date'"  required="required">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<!-- End Bootstrap modal -->
<script type="application/javascript">




    $(document).ready(function(){
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();


        get_ins_donut(); // lol what a name!
        charts();
        mf_pur1 = ajax_mf_list_purchase();
        mf_red1 = ajax_mf_list_redemption();
        ins_new1=ajax_ins_list_new();
        ins_mat1=ajax_ins_list_mat();
        fd_new1=ajax_fd_list_new();
        fd_mat1=ajax_fd_list_mat();
        al_new1=ajax_al_list_new();
        al_mat1=ajax_al_list_mat();
        ajax_equity_funds_new();
        ajax_equity_withdraw_funds_new();
        ajax_equity_withdraw_negative();


        /*$('#btnSave').click(function(){
            if($('#form_assign').parsley('validate'))
            {
                $.ajax({
                    url: '<?php echo site_url('broker/Reminders/snooze')?>',
                    type:'post',
                    data: {'remID': $('#remAssignID').val(), 'remarks': $("#remRemark").val(), 'snoozeDate': $("#remSnoozeDate").val(), 'remDate': $("#remDate").val()},
                    dataType: 'json',
                    success:function(data)
                    {
                        $(".modal").modal('hide');
                        bootbox.alert('Reminder is snoozed!!');
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        console.log(jqXHR);
                        bootbox.alert('Error adding / update data');
                    }
                });
            }
        });

        $('#btn-fd').click(function(){
            $('#btn-fd').addClass('active');
            $('#chart_head').html('Fixed Deposit');
            $('#ins_inv').hide();
            $('#fd_inv').show();
            $('#btn-ins').removeClass('active');
        });

        $('#btn-ins').click(function(){
            $('#btn-ins').addClass('active');
            $('#chart_head').html('Insurance');
            $('#ins_inv').show().addClass('active');
            $('#fd_inv').hide().removeClass('active');
            $('#btn-fd').removeClass('active');
        });

        $('#btnComp').click(function(){
            bootbox.confirm('Are you sure you want to complete this reminder?', function(result){
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/Reminders/complete')?>',
                        type:'post',
                        data: {'remID': $('#remID').val()},
                        dataType: 'json',
                        success:function(data)
                        {
                            $(".modal").modal('hide');
                            bootbox.alert('Reminder is deleted!!');
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            console.log(jqXHR);
                            bootbox.alert('Error adding / update data');
                        }
                    });
                }
            });
        });

        $('#btnMail').click(function(){
            bootbox.confirm('Are you sure you want to send mail to the client?', function(result){
                if(result)
                {
                    $.ajax({
                        url: '<?php echo site_url('broker/Reminders/send_mail')?>',
                        type:'post',
                        data: {'remID': $('#remID').val()},
                        dataType: 'json',
                        success:function(data)
                        {
                            $(".modal").modal('hide');
                            //debugger;
                            var msg = 'EmailID of the client is not valid';
                            if(data.status == true)
                                msg = 'Mail sent successfully';
                            bootbox.alert(msg);
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            console.log(jqXHR);
                            bootbox.alert('Error adding / update data');
                        }
                    });
                }
            });
        });

        $('#btnSMS').click(function(){
            bootbox.alert('Not Integrated Yet?');
        });*/
    });

    /*function snooze()
    {
        //reset form on modals
        $("#form_assign")[0].reset();
        $('#remAssignID').val($('#remID').val());
        //show bootstrap modal
        $("#assign_form").modal('show');
        //set title to modal
        $("#assign_modal_title").text('Snooze Window');
    }*/

    function ajax_mf_list_purchase() {
        oTable = $("#mf_pur").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[3,'desc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/mutual_funds/ajax_list_purchase');?>',
                "type": 'post'
            },
            "columns": [
                /*{ "data": "action" },*/
                { "data": "client_name" },
                { "data": "scheme_name" },
                { "data": "folio_number" },
                { "data": "purchase_date", "type": "date-uk" },
                { "data": "amount" },
                /*{ "data": "adjustment" },
                { "data": "adjustment_ref_number" }*/
            ]

        });
        return oTable;
    }

     function ajax_mf_list_redemption() {
        oTable1 = $("#mf_red").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[3,'desc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/mutual_funds/ajax_list_redemption');?>',
                "type": 'post'
            },
            "columns": [
                /*{ "data": "action" },*/
                { "data": "client_name" },
                { "data": "scheme_name" },
                { "data": "folio_number" },
                { "data": "purchase_date", "type": "date-uk" },
                { "data": "amount" },
                /*{ "data": "adjustment" },
                { "data": "adjustment_ref_number" }*/
            ]

        });
        return oTable1;
    }

    function ajax_ins_list_new() {
            var oTable = $("#ins_new").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/insurances/ajax_ins_list_new');?>',
                "type": 'post'
                /*success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "commence_date", "type": "date-uk"},
                { "data": "plan_name" },
                { "data": "policy_num" },
                //{ "data": "amt_insured"},
		{ "data": "prem_amt"},
            ]
            /*drawCallback: function () {
                var api = this.api();
                $( "#amt-ins" ).val(api.column( 6, {search:'applied'} ).data().sum());
                $( "#prem-amt" ).val(api.column( 8, {search:'applied'} ).data().sum());
                $( "#fund-val" ).val(api.column( 16, {search:'applied'} ).data().sum());
            }*/

        });
        return oTable;
    };

        function ajax_ins_list_mat() {
            var oTable = $("#ins_mat").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/insurances/ajax_ins_list_mat');?>',
                "type": 'post'
                /*success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "maturity_date", "type": "date-uk"},
                { "data": "plan_name" },
                { "data":  "policy_num"},
                { "data": "amount" },
            ]
            /*drawCallback: function () {
                var api = this.api();
                $( "#amt-ins" ).val(api.column( 6, {search:'applied'} ).data().sum());
                $( "#prem-amt" ).val(api.column( 8, {search:'applied'} ).data().sum());
                $( "#fund-val" ).val(api.column( 16, {search:'applied'} ).data().sum());
            }*/

        });
        return oTable;
    };

    function ajax_fd_list_new() {
        oTable = $("#fd_new").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/fixed_deposits/ajax_list_top_new');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk" },
                { "data": "fd_comp_name" },
                { "data": "ref_number" },
                { "data": "amount_invested" }
                //{ "data": "adjustment" }
            ]

        });
        return oTable;
    }

    function ajax_fd_list_mat() {
        oTable = $("#fd_mat").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/fixed_deposits/ajax_list_top_mat');?>',
                "type": 'post'
                /*success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "maturity_date", "type": "date-uk" },
                { "data": "fd_comp_name" },
                { "data": "ref_number" },
                { "data": "maturity_amount" }
                //{ "data": "adjustment" }
            ]

        });
        return oTable;
    }

    function ajax_al_list_new() {

        oTable = $("#al_new").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Assets_liabilities/asset_ajax_list_top');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "client_name" },
                { "data": "start_date", "type": "date-uk" },
                { "data": "product_name"},
                { "data": "ref_number" },
                { "data": "installment_amount" }
            ]

        });
        return oTable;
    }

    function ajax_al_list_mat() {

        oTable = $("#al_mat").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Assets_liabilities/asset_ajax_list_mat');?>',
                "type": 'post'
                /*success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "maturity_date", "type": "date-uk" },
                { "data": "product_name"},
                { "data": "ref_number" },
                { "data": "maturity_amount" }
            ]

        });
        return oTable;
    }

    function ajax_equity_funds_new() {

        oTable = $("#add_equity").DataTable({
           "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Add_funds/ajax_list_new');?>',
                "type": 'post'/*,
                success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk"},
                { "data": "amount" },
                /*{ "data": "client_code" },*/
                { "data": "add_notes" }
            ]

        });
        return oTable;
    }




    function ajax_equity_withdraw_funds_new() {

        oTable = $("#withdraw_equity").DataTable({
           "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aaSorting": [[1,'asc']],
            "bAutoWidth": false,
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/Withdraw_funds/ajax_list_equity');?>',
                "type": 'post'/*,
                success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "transaction_date", "type": "date-uk"},
                { "data": "amount" },
                /*{ "data": "client_code" },*/
                { "data": "add_notes" }
            ]

        });
        return oTable;
    }

     function ajax_equity_withdraw_negative() {

        oTable = $("#negative_equity").DataTable({
            "order": [[3, "asc"]],
           "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "aoColumnDefs": [{ "bSortable": true, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/equity/get_negative_equity');?>',
                "type": 'post'/*,
                success: function(data){

                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }*/
            },
            "columns": [
                { "data": "client_name" },
                { "data": "broker_name"},
                { "data": "client_code" },
                /*{ "data": "client_code" },*/
                { "data": "balance" }
            ]

        });
        return oTable;
    }


    function get_ins_donut()
    {
        $.ajax({
            url: '<?php echo site_url('broker/Dashboard/get_dash_chart'); ?>',
            type: 'post',
            dataType: 'json',
            success: function(data){
                Morris.Donut({
                    element: 'ins-donut',
                    data: data['ins'],
                    resize: true
                });
                Morris.Bar({
                    element: 'com-bar',
                    data: data['com'],
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Rupees'],
                    resize: true
                });
                Morris.Bar({
                    element: 'ins_inv',
                    data: data['ins_inv'],
                    xkey: 'label',
                    ykeys: ['value'],
                    resize: true,
                    barColors: ['#85c744'],
                    labels: ['Insurances']
                });
                //console.log(data);
                Morris.Bar({
                    element: 'fd_inv',
                    data: data['fd_inv'],
                    xkey: 'label',
                    ykeys: ['value'],
                    labels: ['Fixed Deposit'],
                    resize: true
                });


                $('#fd_inv').hide();
            },
            error: function(data){
                console.log(data);
            }
        });
    }

    function charts()
    {

        $.ajax({
            url: '<?php echo site_url('broker/Dashboard/get_pie_chart'); ?>',
            type: 'post',
            dataType: 'json',
            success: function(data){
                console.log(data);
                client_charts(data.varActiveClient,data.varInActiveClient);
                insurance_charts(data.varTraditionalPaid,data.varUnitLikedPaid,data.varGeneralPaid);
                fd_charts(data.varHeldFD,data.varNonHeldFD);
                property_charts(data.varPropertyPurchase,data.varPropertyCurrent);
                equity_charts(data.varHeldInvested,data.varNonHeldInvested);
                mf_charts(data.varDebt,data.varEquity,data.varHybrid);

            },
            error: function(data){
                console.log(data);
            }
        });


        mf_charts();


    }

   function client_charts(val1, val2)
    {
        var clientData = {
            labels: [
                "Active",
                "Inactive"
            ],
            datasets: [
                {
                    data: [val1, val2],
                    backgroundColor: [
                        "#FF6384",
                        "#36A2EB"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true,
    multiTooltipTemplate: "<%= labels %> - <%= value %>"
}


        var myPieChart = new Chart(document.getElementById("client-chart"),{
            type: 'pie',
            data: clientData,
            options: options
        });

}

function insurance_charts(val1,val2,val3)
    {
        var insuranceData = {
            labels: [
                "Traditional",
                "Unit Link",
                "General"
            ],
            datasets: [
                {
                    data: [val1, val2, val3],
                    backgroundColor: [
                        "#BCE784",
                        "#5DD39E",
                        "#348AA7"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true
}


        var myPieChart = new Chart(document.getElementById("insurance-chart"),{
            type: 'pie',
            data: insuranceData,
            options: options
        });

}

function mf_charts(val1,val2,val3)
    {
        var mfData = {
            labels: [
                "Debt",
                "Equity",
                "Hybrid"
            ],
            datasets: [
                {
                    data: [val1, val2, val3],
                    backgroundColor: [
                        "#34344A",
                        "#80475E",
                        "#CC5A71"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true
}


        var myPieChart = new Chart(document.getElementById("mf-chart"),{
            type: 'pie',
            data: mfData,
            options: options
        });


}


function fd_charts(val1,val2)
    {
        var fdData = {
            labels: [
                "Held",
                "Non-Held"
            ],
            datasets: [
                {
                    data: [val1, val2],
                    backgroundColor: [
                        "#73BFB8",
                        "#FEC601"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true
}


        var myPieChart = new Chart(document.getElementById("fd-chart"),{
            type: 'pie',
            data: fdData,
            options: options
        });

}

function equity_charts(val1, val2)
    {
        var equityData = {
            labels: [
                "Held",
                "Non-Held"
            ],
            datasets: [
                {
                    data: [val1, val2],
                    backgroundColor: [
                        "#E5D4ED",
                        "#6D72C3"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true
}


        var myPieChart = new Chart(document.getElementById("equity-chart"),{
            type: 'pie',
            data: equityData,
            options: options
        });

}

function property_charts(val1,val2)
    {
        var propData = {
            labels: [
                "Purchase Price",
                "Current Price"
            ],
            datasets: [
                {
                    data: [val1, val2],
                    backgroundColor: [
                        "#EF946C",
                        "#C4A77D"
                    ],
                    hoverBackgroundColor: [
                        "#324e9f",
                        "#324e9f"
                    ]
                }]
        };
        var options = {
    responsive: true,
    maintainAspectRatio: true
}


        var myPieChart = new Chart(document.getElementById("property-chart"),{
            type: 'pie',
            data: propData,
            options: options
        });

}


    /*function view_reminder(id)
    {
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Reminders/get_reminder_details');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('#remID').val(data.reminder_id);
                $('#remDate').val(data.reminder_date);
                $('#remSub').val(data.reminder_type);
                $('#remClientName').val(data.client_name);
                $('#remMsg').val(data.reminder_message);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('#rem_modal_title').text('View '); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                bootbox.alert('Error get data from ajax');
            }
        });
    }*/

    //below function for testing
    function delete_donut() {
        $("#ins-donut").html("");
        $("#ins-donut").innerHTML = "";
        $("#com-bar").html("");
        $("#com-bar").innerHTML = "";
        $("#ins_inv").html("");
        $("#ins_inv").innerHTML = "";
        $("#fd_inv").html("");
        $("#fd_inv").innerHTML = "";
    }

    //call morris charts again when window size changes - testing
    window.onresize = function(event) {
        resizeFunc();
    }

    function resizeFunc() {
        delete_donut();
        get_ins_donut();
    }

    $('#leftmenu-trigger').on('click', function() {
        delete_donut();
        get_ins_donut();
    });

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function ( a ) {
            var ukDatea = a.split('/');
            return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        },

        "date-uk-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "date-uk-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });




</script>
