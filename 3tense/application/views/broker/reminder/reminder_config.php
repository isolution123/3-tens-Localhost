<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Reminders');?>">Reminders</a></li>
                <li class="active">Reminder Config</li>
            </ol>
            <h1>Reminder Config</h1>
        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="#" id="rem_con_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Reminder Config</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <table id="rem_table" class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Days</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><label class="control-label">Insurance Premium: </label></td>
                                        <td>
                                            <input type="hidden" id="reminder_days_id" name="reminder_days_id"
                                                value="<?php echo isset($reminder->reminder_days_id)?$reminder->reminder_days_id: ''; ?>">
                                            <input type="text" name="ins_premium_reminder" required="required" class="form-control"
                                                id="ins_premium_reminder" tabindex="1"
                                                value="<?php echo isset($reminder->ins_premium_reminder)?$reminder->ins_premium_reminder: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="ins_premium_amount" required="required" class="form-control"
                                                id="ins_premium_amount" tabindex="2"
                                                value="<?php echo isset($reminder->ins_premium_amount)?$reminder->ins_premium_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Insurance Maturity: </label></td>
                                        <td>
                                            <input type="text" name="ins_maturity_reminder" required="required" class="form-control"
                                                id="ins_maturity_reminder" tabindex="3"
                                                value="<?php echo isset($reminder->ins_maturity_reminder)?$reminder->ins_maturity_reminder: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="ins_maturity_amount" required="required" class="form-control"
                                                id="ins_maturity_amount" tabindex="4"
                                                value="<?php echo isset($reminder->ins_maturity_amount)?$reminder->ins_maturity_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Insurance Grace: </label></td>
                                        <td>
                                            <input type="text" name="ins_grace_reminder" required="required" class="form-control"
                                                id="ins_grace_reminder" tabindex="5"
                                                value="<?php echo isset($reminder->ins_grace_reminder)?$reminder->ins_grace_reminder: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="ins_grace_amount" required="required" class="form-control"
                                                id="ins_grace_amount" tabindex="6"
                                                value="<?php echo isset($reminder->ins_grace_amount)?$reminder->ins_grace_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Fixed Deposit Interest: </label></td>
                                        <td>
                                            <input type="text" name="fd_interest" required="required" class="form-control" id="fd_interest"
                                                tabindex="7" value="<?php echo isset($reminder->fd_interest)?$reminder->fd_interest: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="fd_interest_amount" required="required" class="form-control"
                                                id="fd_interest_amount" tabindex="8"
                                                value="<?php echo isset($reminder->fd_interest_amount)?$reminder->fd_interest_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Fixed Deposit Maturity: </label></td>
                                        <td>
                                            <input type="text" name="fd_maturity_reminder" required="required" class="form-control"
                                                id="fd_maturity_reminder" tabindex="9"
                                                value="<?php echo isset($reminder->fd_maturity_reminder)?$reminder->fd_maturity_reminder: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="fd_maturity_amount" required="required" class="form-control"
                                                id="fd_maturity_amount" tabindex="10"
                                                value="<?php echo isset($reminder->fd_maturity_amount)?$reminder->fd_maturity_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Asset and Liability: </label></td>
                                        <td>
                                            <input type="text" name="assets_reminder" required="required" class="form-control" id="assets_reminder"
                                                tabindex="11" value="<?php echo isset($reminder->assets_reminder)?$reminder->assets_reminder: ''; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="assets_amount" required="required" class="form-control" id="assets_amount"
                                                tabindex="12" value="<?php echo isset($reminder->assets_amount)?$reminder->assets_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Personal: </label></td>
                                        <td>
                                            <input type="text" name="personal_reminder" required="required" class="form-control"
                                                id="personal_reminder" tabindex="13"
                                                value="<?php echo isset($reminder->personal_reminder)?$reminder->personal_reminder: ''; ?>">
                                        </td>
                                        <td><label class="control-label">N/A </label></td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Rent: </label></td>
                                        <td><label class="control-label">N/A </label></td>
                                        <td>
                                            <input type="text" name="rent_amount" required="required" class="form-control" id="fd_int_amt"
                                                tabindex="14" value="<?php echo isset($reminder->rent_amount)?$reminder->rent_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Equity Share Negative: </label></td>
                                        <td><label class="control-label">N/A </label></td>
                                        <td>
                                            <input type="text" name="share_negative" required="required" class="form-control" id="share_negative"
                                                tabindex="15" value="<?php echo isset($reminder->share_negative)?$reminder->share_negative: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">MF DPO: </label></td>
                                        <td><label class="control-label">N/A </label></td>
                                        <td>
                                            <input type="text" name="mf_dpo_amount" required="required" class="form-control" id="mf_dpo_amount"
                                                tabindex="16" value="<?php echo isset($reminder->mf_dpo_amount)?$reminder->mf_dpo_amount: ''; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">MF Redemption: </label></td>
                                        <td><label class="control-label">N/A </label></td>
                                        <td>
                                            <input type="text" name="mf_redemption_amount" required="required" class="form-control"
                                                id="mf_redemption_amount" tabindex="17"
                                                value="<?php echo isset($reminder->mf_redemption_amount)?$reminder->mf_redemption_amount: ''; ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-16" style="text-align: center">
                                      <?php
                                        if( $this->session->userdata('permissions')=="3" ||  $this->session->userdata('permissions')=="2"){ ?>
                                        <button type="button" id="save" tabindex="18" onclick="rem_config_submit()"  data-style="expand-left" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Save</button>
                                        <?php
                                        }
                                          else {
                                            ?>
                                            <button type="button" id="save" tabindex="18" onclick="rem_config_submit()"  data-style="expand-left" class="btn btn-primary ladda-button disable_btn"><i class="fa fa-save"></i> Save</button>
                                          <?php
                                        }
                                         ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function rem_config_submit()
    {
        var valid = $('#rem_con_form').parsley('validate');
        if(valid)
        {
            $.ajax({
                url: '<?php echo site_url('broker/reminders/edit_reminder_days');?>',
                type: 'post',
                data: $('#rem_con_form').serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] == 'success')
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'success',
                            hide: true
                        });
                    }
                    else
                    {
                        $.pnotify({
                            title: data['title'],
                            text: data['text'],
                            type: 'error',
                            hide: true
                        });
                    }
                },
                error:function(data)
                {
                    console.log(data);
                    bootbox.alert("Something went terribly wrong");
                }
            });
        }
    }
</script>
