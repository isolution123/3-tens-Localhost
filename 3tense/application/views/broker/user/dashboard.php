<?php 
/* Akshay Karde - 2017-04-22 - for showing system update/maintenance Popup modal */
/*if(!isset($_SESSION['popup']))
{
  ?>
  <div class="modal fade in" id="modal_family_form_sys" role="dialog" aria-hidden="false" style="display:block;">
      <div class="modal-dialog" style="width:60%">
          <div class="modal-content">
              <div class="modal-header alert-danger">
                  <button type="button" class="close" onclick="closepop()" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                  <h3 class="modal-title">New Updates!!</h3>
              </div>
              <div class="modal-body form">
                 <table id="what-new" class="table table-striped" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<p align="center"><strong>S.No.</strong></p>
</td>
<td>
<p align="center"><strong>Particular</strong></p>
</td>
<td>
<p align="center"><strong>Path</strong></p>
</td>
<td>
<p align="center"><strong>Benefits</strong></p>
</td>
</tr>
<tr>
<td valign="top">
<p align="center">1</p>
</td>
<td valign="top">Mutual Fund AUM Report</td>
<td valign="top">Go to Reports - &gt; Mutual Fund details -&gt; Select Report Type "AUM" in Bottom Section [Broker]</td>
<td valign="top">Exclusive report for IFA only. Scheme wise detail report of total live AUM under an IFA ARN with CAGR And ABS.</td>
</tr>
<tr>
<td valign="top">
<p align="center">2</p>
</td>
<td valign="top">Mutual Fund Folio Details</td>
<td valign="top">Go to Import -&gt; Client Details -&gt; Upload RTA File<br/><br/>
Following are the file names for Folio Details import: <br/>
CAMS: WBR9 (DBF) <br/>
KARVY: MFSD211 (DBF) <br/>
Franklin: Investor Folio details (DBF) <br/>
Sundaram:ER04 (DBF) <br/>
</td>
<td valign="top">For fast and hassle client/folio details data entry</td>
</tr>
<tr>
<td valign="top">
<p align="center">3</p>
</td>
<td valign="top">Client Merger</td>
<td valign="top">Go to Client - &gt; Merge Client button</td>
<td valign="top">All duplicate clients can be identified and be merged with one correct client. All existing data/reports will be merged into one correct single client.</td>
</tr>
<tr>
<td valign="top">
<p align="center">4</p>
</td>
<td valign="top">Client Transfer</td>
<td valign="top">Go to Client - &gt; Transfer Clients Family button</td>
<td valign="top">Existing Client can be tranfered from one family name to other family name with entire existing details</td>
</tr>
</tbody>
</table>
<p style="font-size:6px;"></p>* For more details you can contact our Relationship Manager Mr. Prakash Sumbe on +91-9920680184.
              </div>

          </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
  </div>
<?php 
$_SESSION['popup']='popup';
}
/* Popup Modal code END */
?>
<style>
    .padding10 > div
    {
        padding :5px;
    }
    .chart_css
    {
        height: 260px; 
        width: 100%;
        border:1px solid #dddddd;
        
    }
</style>
<div id="page-content">
    <div id='wrap'>
        <!--<div id="page-heading">
            <ol class="breadcrumb">
                <li class="active">Dashboard</li>
            </ol>
            <h1>Dashboard</h1>
        </div>-->
        <div class="container">
            
            
                    <div class="row padding10">
                            <div class="col-md-4 col-xs-12 col-sm-6">
                           
                           
                                    
                                    <div id="client-chart"  class="chart_css"></div>
                           
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                
                                    
                                    <div id="chartContainer" class="chart_css"></div>
                                    
                               
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                
                                    
                                    <div id="mf-chart" class="chart_css"></div>
                              
                            </div>
                    </div>
                    <div class="row padding10">
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                
                                    
                                         <div id="fd-chart" class="chart_css"></div>
                               
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div id="equity-chart" class="chart_css"></div>
                                
                            </div>
                            <div class="col-md-4 col-xs-12 col-sm-6">
                                <div id="property-chart" class="chart_css"></div>

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
                                              <table class="table table-striped data_table_sorting"  id='mf_new_purchase' style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Client Name</th>
                                                        <th>Scheme Name</th>
                                                        <th>Folio No.</th>
                                                        <th>Trans. Date</th>
                                                        <th>Amount</th>
                                                     </tr>
                                                  </thead>
                                                       <?php
                                                         foreach ($mf_pur_list['data'] as $rs)
                                                         {
                                                           echo "<tr><td>".$rs['client_name']."</td><td>".$rs['scheme_name']."</td><td>".$rs['folio_number']."</td><td>".$rs['purchase_date']."</td><td>".$rs['amount']."</td></tr>" ;
                                                         }
                                                        ?>
                                            </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Redemption</h4></div>
                                            <div class="panel-body">
                                            <table class="table table-striped data_table_sorting" id='mf_new_redm'  style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Client Name</th>
                                                        <th>Scheme Name</th>
                                                        <th>Folio No.</th>
                                                        <th>Transaction Date</th>
                                                        <th>Amount</th>
                                                     </tr>
                                                    </thead>
                                                       <?php
                                                         foreach ($mf_redm_list['data'] as $rs)
                                                         {
                                                           echo "<tr><td>".$rs['client_name']."</td><td>".$rs['scheme_name']."</td><td>".$rs['folio_number']."</td><td>".$rs['purchase_date']."</td><td>".$rs['amount']."</td></tr>" ;
                                                         }
                                                        ?>

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
                                              <table class="table table-striped data_table_sorting" id="ins_new_policy" style="width: 100%;" >
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Plan Name</th>
                                                    <th>Policy Number</th>
						                                        <th>Premium Amt</th>
                                                   </tr>
                                                </thead>
                                                <?php
                                                  foreach ($ins_new_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['commence_date']."</td><td>".$rs['plan_name']."</td><td>".$rs['policy_num']."</td><td>".$rs['prem_amt']."</td></tr>" ;
                                                  }
                                                 ?>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Mature</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped data_table_sorting" id="ins_new_mat" style="width: 100%;">
                                               <thead>
                                                     <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Plan Name</th>
                                                    <th>Policy Number</th>
                                                    <th>Maturity Amt</th>
                                                     </tr>
                                                </thead>
                                                <?php
                                                  foreach ($ins_mat_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['plan_name']."</td><td>".$rs['policy_num']."</td><td>".$rs['amount']."</td></tr>" ;
                                                  }
                                                 ?>
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
                                              <table class="table table-striped data_table_sorting" id="fd_new_held" style="width:100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Company Name</th>
                                                    <th>Ref No.</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                                <?php
                                                  foreach ($fd_new_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['transaction_date']."</td><td>".$rs['fd_comp_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['amount_invested']."</td></tr>";
                                                  }
                                                 ?>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Mature</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped data_table_sorting" id="fd_new_mat" style="width: 100%;">
                                                <thead>
                                                   <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Company Name</th>
                                                    <th>Ref No.</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                                <?php
                                                  foreach ($fd_mat_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['fd_comp_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['maturity_amount']."</td></tr>" ;
                                                  }
                                                 ?>
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
                                              <table class="table table-striped data_table_sorting" id="al_new" style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Start Date</th>
                                                    <th>Product Name</th>
                                                    <th>Ref No</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                                <?php
                                                  foreach ($al_new_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['start_date']."</td><td>".$rs['product_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['installment_amount']."</td></tr>" ;
                                                  }
                                                 ?>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>About to Close</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped data_table_sorting" id='al_new_close' style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Maturity Date</th>
                                                    <th>Product Name</th>
                                                    <th>Ref No</th>
                                                    <th>Amount</th>
                                                  </tr>
                                                </thead>
                                                <?php
                                                  foreach ($al_mat_list['data'] as $rs)
                                                  {
                                                    echo "<tr><td>".$rs['client_name']."</td><td>".$rs['maturity_date']."</td><td>".$rs['product_name']."</td><td>".$rs['ref_number']."</td><td>".$rs['maturity_amount']."</td></tr>" ;
                                                  }
                                                 ?>
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
                                              <table class="table table-striped data_table_sorting" id='eq_added' style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Details</th>
                                                  </tr>
                                                </thead>
                                            <?php
                                              foreach ($eq_fund_added['data'] as $rs)
                                              {
                                                echo "<tr><td>".$rs['client_name']."</td><td>".$rs['transaction_date']."</td><td>".$rs['amount']."</td><td>".$rs['add_notes']."</td></tr>" ;
                                              }
                                             ?>
                                              </table>
                                            </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-6">
                                            <div><h4>Funds Withdrawal</h4></div>
                                            <div class="panel-body">
                                              <table class="table table-striped data_table_sorting" id='eq_withdraw' style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client Name</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Details</th>
                                                  </tr>
                                                </thead>
                                               <?php
                                                 foreach ($eq_fund_withdraw['data'] as $rs)
                                                 {
                                                   echo "<tr><td>".$rs['client_name']."</td><td>".$rs['transaction_date']."</td><td>".$rs['amount']."</td><td>".$rs['add_notes']."</td></tr>" ;
                                                 }
                                                ?>
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
                                              <table class="table table-striped data_table_sorting " id='high_negetive_bal' style="width: 100%;">
                                                <thead>
                                                  <tr>
                                                    <th>Client</th>
                                                    <th>Broker Name</th>
                                                    <th>Client Code</th>
                                                    <th>Balance</th>
                                                  </tr>
                                                </thead>
                                            <?php
                                              foreach ($high_neg_balance['data'] as $rs)
                                              {
                                                echo "<tr><td>".$rs['client_name']."</td><td>".$rs['broker_name']."</td><td>".$rs['client_code']."</td><td>".$rs['balance']."</td></tr>" ;
                                              }
                                             ?>
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
                            <a class="info-tiles tiles-toyo" href="<?php //echo site_url('broker/clients');?>">
                                <div class="tiles-heading">Clients</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-group"></i>
                                    <div class="text-center"><?php //echo $get_info['num_clients'];?></div>
                                    <small>Clients</small>
                                </div>
                                <div class="tiles-footer">Manage Clients</div>
                            </a>
                        </div>
                        <dFocus 5iv class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-success" href="<?php //echo site_url('broker/insurances');?>">
                                <div class="tiles-heading">Insurance</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-bar-chart-o"></i>
                                    <div class="text-center"><?php //echo $get_info['num_ins'];?></div>
                                    <small>Matured Insurances till date</small>
                                </div>
                                <div class="tiles-footer">Go to Insurances</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-orange" href="<?php //echo site_url('broker/insurances');?>">
                                <div class="tiles-heading">Insurance</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-anchor"></i>
                                    <div class="text-center"><?php //echo $get_info['num_ins_lapsed'];?></div>
                                    <small>Lapsed Insurances till date</small>
                                </div>
                                <div class="tiles-footer">Go to Insurances</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6">
                            <a class="info-tiles tiles-alizarin" href="<?php //echo site_url('broker/fixed_deposits');?>">
                                <div class="tiles-heading">Fixed Deposit</div>
                                <div class="tiles-body-alt">
                                    <i class="fa fa-shopping-cart"></i>
                                    <div class="text-center"><?php //echo $get_info['num_fd'];?></div>
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
                        <?php if($brokerId==='0004'){?>
                        <div class="col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4>SIP Book</h4>
                                </div>
                                <div class="panel-body">
                                    <div id="mf_summary_chart"  class="" style="width:500px;height:365px" ></div>
                                </div>
                            </div>
                        </div>
                        <?php } else{ ?>
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
                        <?php } ?>
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
<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script src="../assets/vendors/CanvasJS/canvas.js"></script>



<!-- End Bootstrap modal -->

<!-- Akshay Karde - 2017-04-22 - for close button of Popup Modal -->
<script type="text/javascript">
    function closepop()
    {
        document.getElementById('modal_family_form_sys').style.display='none';
    }
</script>
<!-- Popup Modal close button Code END -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="application/javascript">
    $(document).ready(function(){
         $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
        //get_ins_donut(); // lol what a name!
        charts();


            $('.data_table_sorting').DataTable({
             "processing":true,
             "serverSide":false,
              "bPaginate": false,
              "bFilter": false,
              "bInfo": false,
             "aaSorting": [[1,'asc']],
              "bAutoWidth": false
            });


});

    // function get_ins_donut()
    // {
    //     $.ajax({
    //         url: '<?php echo site_url('broker/Dashboard/get_dash_chart'); ?>',
    //         type: 'post',
    //         dataType: 'json',
    //         success: function(data){
    //             Morris.Donut({
    //                 element: 'ins-donut',
    //                 data: data['ins'],
    //                 resize: true
    //             });
    //             // Morris.Bar({
    //             //     element: 'com-bar',
    //             //     data: data['com'],
    //             //     xkey: 'y',
    //             //     ykeys: ['a'],
    //             //     labels: ['Rupees'],
    //             //     resize: true
    //             // });
    //             // Morris.Bar({
    //             //     element: 'ins_inv',
    //             //     data: data['ins_inv'],
    //             //     xkey: 'label',
    //             //     ykeys: ['value'],
    //             //     resize: true,
    //             //     barColors: ['#85c744'],
    //             //     labels: ['Insurances']
    //             // });
    //             //console.log(data);
    //             // Morris.Bar({
    //             //     element: 'fd_inv',
    //             //     data: data['fd_inv'],
    //             //     xkey: 'label',
    //             //     ykeys: ['value'],
    //             //     labels: ['Fixed Deposit'],
    //             //     resize: true
    //             // });
    //
    //
    //             $('#fd_inv').hide();
    //         },
    //         error: function(data)
    //         {
    //             console.log(data);
    //          }
    //     });
    // }


    function charts()
    {
               client_charts(<?php echo isset($pie_chart['varActiveClient'])?intval($pie_chart['varActiveClient']):intval(0);?>,<?php echo isset($pie_chart['varInActiveClient'])?intval($pie_chart['varInActiveClient']):intval(0);?>);
               insurance_charts(<?php echo isset($pie_chart['varTraditionalPaid'])?intval($pie_chart['varTraditionalPaid']):intval(0); ?>,<?php echo isset($pie_chart['varUnitLikedPaid'])?intval($pie_chart['varUnitLikedPaid']):intval(0); ?>,<?php echo isset($pie_chart['varGeneralPaid'])?intval($pie_chart['varGeneralPaid']):intval(0); ?>);
               fd_charts(<?php echo isset($pie_chart['varHeldFD'])?intval($pie_chart['varHeldFD']):intval(0); ?>,<?php echo isset($pie_chart['varNonHeldFD'])?intval($pie_chart['varNonHeldFD']):intval(0); ?>);
               property_charts(<?php echo isset($pie_chart['varPropertyPurchase'])?intval($pie_chart['varPropertyPurchase']):intval(0); ?>,<?php echo isset($pie_chart['varPropertyCurrent'])?intval($pie_chart['varPropertyCurrent']):intval(0); ?>);
               equity_charts(<?php echo isset($pie_chart['varHeldInvested'])?intval($pie_chart['varHeldInvested']):intval(0); ?>,<?php echo isset($pie_chart['varNonHeldInvested'])?intval($pie_chart['varNonHeldInvested']):intval(0); ?>);
               mf_charts(<?php echo isset($pie_chart['varDebt'])?intval($pie_chart['varDebt']):intval(0); ?>,<?php echo isset($pie_chart['varEquity'])?intval($pie_chart['varEquity']):intval(0); ?>,<?php echo isset($pie_chart['varHybrid'])?intval($pie_chart['varHybrid']):intval(0); ?>);
               
              <?php if($brokerId!=='0004'){?>
              
              var dataa =<?php echo json_encode($ins_data);?>;
                Morris.Donut({
                    element: 'ins-donut',
                    data: dataa,
                    resize: true
                  });
<?php } else { ?>
   
            var arr_val =<?php echo json_encode($sip_book_chart);?>;
            var arr_current_value=[];
           
            $.each(arr_val, function(key, value) {
                arr_current_value.push([new Date(value.sip_date),
                +value.amount,
                "Date:"+ new Date(value.sip_date).toLocaleDateString('en-GB', {month: 'short', year: 'numeric'
}).replace(/ /g, '-') + " Amount:" + (+value.amount)
                ]);
            });
            
            
            mf_summary_chart(arr_current_value);
            
            <?php } ?>
    }

    function mf_summary_chart(arr_current_value)
    {
        debugger;
           google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn('date', 'Year');
        dataTable.addColumn('number', 'Sales');
        dataTable.addColumn({type: 'string', role: 'tooltip'});
       
        dataTable.addRows(arr_current_value);

        var options = { title: 'SIP Book',
          curveType: 'function',
          legend: { position: 'bottom' },
          legend: 'none',
        pointSize: 5,
         vAxis: {
             format:'' 
             
         }
            
        };
        var chart = new google.visualization.LineChart(document.getElementById('mf_summary_chart'));
        chart.draw(dataTable, options);
      }
        
    /*google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
          
        var data = google.visualization.arrayToDataTable(arr_current_value);

        var options = {
          title: 'SIP Book',
          curveType: 'function',
          legend: { position: 'bottom' },
          legend: 'none',
        pointSize: 5,
         vAxis: {
             format:'' 
             
         }
        };

        var chart = new google.visualization.LineChart(document.getElementById('mf_summary_chart'));

        chart.draw(data, options);
      }*/

    }
    function mf_summary_chart1(arr_current_value)
    {
     
             
        var chart2 = new CanvasJS.Chart("mf_summary_chart", {
                animationEnabled: true,
    	        theme: "light2",
            	title:{
            		text: "SIP Book",
            		 fontFamily: "Arial",
                fontWeight: "bolder",
                fontSize: 20
        	},
        	axisX:{
        		valueFormatString: "MMM-YYYY",
        		crosshair: {
        			enabled: true,
        			snapToDataPoint: true
        		}
        	},
        	axisY: {
        		title: "",
        		 valueFormatString: "##,##,##,###.#",
        		includeZero: true,
        		crosshair: {
        			enabled: true
        		}
        	},
        	toolTip:{
        		shared:true,
        		contentFormatter: function (e) {
    				var content = " ";
    				for (var i = 0; i < e.entries.length; i++) {
    				    
    				    var x=e.entries[i].dataPoint.y.toString();
                        var lastThree = x.substring(x.length-3);
                        var otherNumbers = x.substring(0,x.length-3);
                        if(otherNumbers != '')
                            lastThree = ',' + lastThree;
                        var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    			
    					content += "<strong>Rs." + res + "</strong>";
    					content += "<br/>";
    				}
    				return content;
    			}
        	},
        	legend:{
        		cursor:"pointer",
        		verticalAlign: "bottom",
        		horizontalAlign: "left",
        		dockInsidePlotArea: true,
        		itemclick: toogleDataSeries
        	},
        	data: [
        	{
        		type: "line",
        		showInLegend: true,
        		markerType: "square",
        		name: "Live Value",
        		xValueFormatString: "MMM-YYYY",
        		color: "#814766",
        		dataPoints: arr_current_value
        	}]
        });
        chart2.render();
        
        function toogleDataSeries(e){
        	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        		e.dataSeries.visible = false;
        	} else{
        		e.dataSeries.visible = true;
        	}
        	chart2.render();
        }
        
        $('.canvasjs-chart-credit').css('display','none');
        
        setTimeout(function(){
            var canvas = $("#mf_summary_chart").find(".canvasjs-chart-canvas").get(0);
            var dataURL = canvas.toDataURL('image/png');
            $('#chart_3').attr('value',  dataURL.replace('data:image/png;base64,',''));   
        },2500);
        
    }



   function client_charts(val1, val2)
    {
       
    /*    var clientData = {
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
        });*/
     
        var total_val=val1+val2;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        
        
         var chart1 = new CanvasJS.Chart("client-chart", {
        	animationEnabled: true,
        	backgroundColor: "#f7f8fa",
        	title: {
        	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
        		text: "Clients"
        	},
        	data: [{
        		type: "pie",
        		startAngle: 10,
        		 indexLabelFormatter: function(e){		
        			return e.dataPoint.label + " (" + e.dataPoint.per + "%)                 " + e.dataPoint.y;				
        		},
        		dataPoints: [
        			{y: val1, label: "Active",per:per1,color: "#FF6384"},
        			{y: val2, label: "Inactive",per:per2,color: "#36A2EB"}
        		]
        	}]
        });

        chart1.render();
$('.canvasjs-chart-credit').css('display','none');
    }   	

    function insurance_charts(val1,val2,val3)
    {
 /*        var insuranceData = {
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
   */     
        var total_val=val1+val2+val3;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        var per3=((val3*100)/total_val).toFixed(2);
        
 var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	backgroundColor: "#f7f8fa",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
		text: "Insurance"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		startAngle: 110,
		
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints: [
			{y: val1, label: "Traditional",per:per1,color: "#BCE784"},
			{y: val2, label: "Unit Link",per:per2,color: "#5DD39E"},
			{y: val3, label: "General",per:per3,color: "#348AA7"}
	
		]
	}]
});

chart.render();

$('.canvasjs-chart-credit').css('display','none');


     }

   function mf_charts(val1,val2,val3)
    {
       /* var mfData = {
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
        });*/
        <?php if($brokerId==='0004'){?>
        val1=val1*4;
        val2=val2*4;
        val3=val3*4;
        <?php } ?>
        var total_val=val1+val2+val3;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        var per3=((val3*100)/total_val).toFixed(2);
        
 var chart = new CanvasJS.Chart("mf-chart", {
	animationEnabled: true,
	backgroundColor: "#f7f8fa",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
		text: "Mutual Funds"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		startAngle: 110,
		
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)  Rs." + res;				
		},
		dataPoints: [
			{y: val1, label: "Debt",per:per1,color: "#34344A"},
			{y: val2, label: "Equity",per:per2,color: "#80475E"},
			{y: val3, label: "Hybrid",per:per3,color: "#CC5A71"}
		]
		
	}]
});

chart.render();
$('.canvasjs-chart-credit').css('display','none');
    }

   function fd_charts(val1,val2)
    {
        /*var fdData = {
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
        */
        var total_val=val1+val2;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        
        
 var chart = new CanvasJS.Chart("fd-chart", {
	animationEnabled: true,
	backgroundColor: "#f7f8fa",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
		text: "Fixed Deposit"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		startAngle: 10,
		
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints: [
			{y: val1, label: "Held",per:per1,color: "#73BFB8"},
			{y: val2, label: "Non-Held",per:per2,color: "#FEC601"}
		
		]
		
	}]
});

chart.render();
$('.canvasjs-chart-credit').css('display','none');
setTimeout(function(){ $('.canvasjs-chart-credit').css('display','none'); }, 500);
    }

    function equity_charts(val1, val2)
    {
        /*var equityData = {
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
          });*/
          var total_val=val1+val2;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        
        
 var chart = new CanvasJS.Chart("equity-chart", {
	animationEnabled: true,
	backgroundColor: "#f7f8fa",
	title: {
	    fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
		text: "Equity"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		startAngle: 10,
		
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints: [
			{y: val1, label: "Held",per:per1,color: "#E5D4ED"},
			{y: val2, label: "Non-Held",per:per2,color: "#6D72C3"}
		 
		]
		
	}]
});

chart.render();
setTimeout(function(){ $('.canvasjs-chart-credit').css('display','none'); }, 500);
$('.canvasjs-chart-credit').css('display','none');
      }

    function property_charts(val1,val2)
    {
        /*var propData = {
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
        });*/
        var total_val=val1+val2;
        var per1=((val1*100)/total_val).toFixed(2);
        var per2=((val2*100)/total_val).toFixed(2);
        
        
 var chart = new CanvasJS.Chart("property-chart", {
	animationEnabled: true,
	backgroundColor: "#f7f8fa",
	title: {
        fontFamily: "Arial",
        fontWeight: "bolder",
        fontSize: 20,
		text: "Real Estate"
	},
		toolTip: {
		
			contentFormatter: function (e) {
			    
				var content = " ";
				for (var i = 0; i < e.entries.length; i++) {
				    
				    var x=e.entries[i].dataPoint.y.toString();
                    var lastThree = x.substring(x.length-3);
                    var otherNumbers = x.substring(0,x.length-3);
                    if(otherNumbers != '')
                        lastThree = ',' + lastThree;
                    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			
					content += "<strong>Rs." + res + "</strong>";
					content += "<br/>";
				}
				return content;
			}
		},
	data: [{
		type: "pie",
		
		startAngle: 10,
		 indexLabelFormatter: function(e){		
		    var x=e.dataPoint.y.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			return e.dataPoint.label + " (" + e.dataPoint.per + "%)   Rs." + res;				
		},
		dataPoints: [
			{y: val1, label: "Purchase Price",per:per1,color: "#EF946C"},
			{y: val2, label: "Current Price",per:per2,color: "#C4A77D"}
		  
		]
		
	}]
});

chart.render();
$('.canvasjs-chart-credit').css('display','none');
setTimeout(function(){ $('.canvasjs-chart-credit').css('display','none'); }, 500);


     }

    //below function for testing
    function delete_donut()
    {
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
    window.onresize = function(event)
     {
        resizeFunc();
      }

    function resizeFunc()
    {
        delete_donut();
        //get_ins_donut();
    }

    $('#leftmenu-trigger').on('click', function()
    {
        delete_donut();
        //get_ins_donut();
    });

    jQuery.extend( jQuery.fn.dataTableExt.oSort,
      {
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

<!-- css for modal box -->

              <style type="text/css">
                  #what-new p
                  {
                    margin:0px !important;
                    padding:0px !important;
                    font:size:12px !important;
                  }
                 
                #what-new tbody > tr > td
                {
                    padding:3px !important;
                    font:size:12px !important;
                }
              </style>
