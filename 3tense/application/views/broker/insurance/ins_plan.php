<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li>Insurances</li>
                <li class="active">Insurance Plan Master</li>
            </ol>
            <h1>Insurance Plan Master</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-indigo">
                        <div class="panel-heading">
                            <h4>Insurance Plan Table</h4>

                        </div>
                        <div class="panel-body collapse in">
                            <div class="alert alert-info">
                                Create a new Insurance Plan or simply select a row to edit the data.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                            <button class="btn btn-success" tabindex="1" onclick="add_ins_plan()"><i class="fa fa-plus"></i> Add Insurance Plan</button>
                            <br /><br />
                            <div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th class="action-col action-col-2">Action</th>
                                        <th>Plan ID</th>
                                        <th>Insurance Companies</th>
                                        <th>Plan Name</th>
                                        <th>Plan Type</th>
                                        <th>Grace Period</th>
                                        <th>Annual Bonus</th>
                                        <th>Loyalty Addition</th>
                                        <th>Annual Growth</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th class="action-col action-col-2"></th>
                                        <th>Plan ID</th>
                                        <th>Insurance Companies</th>
                                        <th>Plan Name</th>
                                        <th>Plan Type</th>
                                        <th>Grace Period</th>
                                        <th>Annual Bonus</th>
                                        <th>Loyalty Addition</th>
                                        <th>Annual Growth</th>
                                    </tr>
                                    </tfoot>
                                </table><!--end table-->
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
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="form" class="form-horizontal" data-validate="parsley">
                                <input type="hidden" value="" name="id"/>
                                <div class="form-body">
                                     <div class="form-group">
                                        <label for="insComp" class="col-sm-3 control-label">Insurance Company</label>
                                        <div class="col-sm-6 add-new-btn">
                                            <select name="insComp" id="insComp" style="width: 80%; display: inline-block" required="required">
                                                <option selected disabled value="">Select Insurance Company</option>
                                                <?php foreach($ins_comp as $row):?>
                                                    <option value='<?php echo $row->ins_comp_id; ?>'><?php echo $row->ins_comp_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <a href="javascript:;" class="btn btn-xs btn-inverse-alt" onclick="add_ins_comp(true)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="insPlanType" class="col-sm-3 control-label">Plan Type</label>
                                        <div class="col-sm-6">
                                            <select name="insPlanType" id="insPlanType" class="form-control" required="required">
                                                <option selected disabled value="">Select Plan Type</option>
                                                <?php foreach($plan_type as $row):?>
                                                    <option value='<?php echo $row->plan_type_id; ?>'><?php echo $row->plan_type_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input name="insPlanID" id="insPlanID" type="hidden">
                                        <label class="control-label col-md-3">Plan Name</label>
                                        <div class="col-md-6">
                                            <select name="planName" id="planName" style="width: 100%" required="required">
                                                <option></option>
                                                <?php foreach($policy as $row):?>
                                                    <option value='<?php echo $row->policy_id; ?>'><?php echo $row->policy_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"></label>
                                        <div class="col-sm-6">
                                            <div class="input-group" data-toggle="tooltip" data-placement="bottom" title="Check to Rename this plan." >
                                                <span class="input-group-addon">
                                                    <input type="checkbox" id="chkRename" name="chkRename">
                                                </span>
                                                <input type="text" placeholder="Rename Plan" class="form-control tooltips" id="insPlanRename" name="insPlanRename" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Grace Period</label>
                                        <div class="col-md-3">
                                            <input name="planGrace" id="planGrace" data-type="number" dataplaceholder="Grace Period" required class="form-control" type="text" value="15">
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Annual Bonus</label>
                                        <div class="col-md-3">
                                            <input name="insBonus" id="insBonus" data-type="number" placeholder="Annual Bonus" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Loyalty Addition</label>
                                        <div class="col-md-3">
                                            <input name="insLoyal" id="insLoyal" data-type="number" placeholder="Loyalty Addition" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Annual Growth</label>
                                        <div class="col-md-3">
                                            <input name="insGrowth" id="insGrowth" data-type="number" placeholder="Annual Growth" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!-- End Bootstrap modal -->
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    $(document).ready(function(){
        //check rename checkbox is checked, if yes enable textbox
        $("#chkRename").change(function(){
           if(this.checked)
           {
               if($("#planName").val() != "")
               {
                    $("#insPlanRename").attr('readonly',false);
               }
               else
               {
                  this.checked = false;
                   bootbox.alert('Please select plan to rename');
               }
           }
            else
           {
               $("#insPlanRename").attr('readonly',true);
           }
        });

        //initialize plan name in select2
        $("#insComp").select2();
        $('#planName').select2({
            width: 'resolve',
            placeholder:"Plan Name"
        }).on('change', function(e){
            //on change of plan name update text in insPlanRename
            var data = $('#planName').select2('data');
            $("#insPlanRename").val(data.text);
        });
        //initialize tooltip
        $('[data-toggle="tooltip"]').tooltip();

        table = ajax_ins_plan_list();
        table_initialize();
        $("#insPlanType").change(function(){
            checkPlanType();
        });
    });

    function checkPlanType()
    {
        if($("#insPlanType option:selected").text() == "Traditional")
        {
            $("#insGrowth").attr('readonly', true);
            $("#insLoyal").attr('readonly', false);
            $("#insBonus").attr('readonly', false);
        }
        else if($("#insPlanType option:selected").text() == "Unit Linked")
        {
            $("#insGrowth").attr('readonly', false);
            $("#insLoyal").attr('readonly', true);
            $("#insBonus").attr('readonly', true);
        }
        else
        {
            $("#insGrowth").attr('readonly', true);
            $("#insLoyal").attr('readonly', true);
            $("#insBonus").attr('readonly', true);
        }
    }

    function ajax_ins_plan_list() {
        var oTable = $("#table").DataTable({
            "processing":true,    //Control the processing indicator
            "serverSide":false,    //Control DataTable server process
            "aaSorting": [[1,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 1 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/insurance_plans/ajax_list');?>',
                "type": 'post'
            },
            "columns": [
                { "data": "action" },
                { "data": "plan_id" },
                { "data": "ins_companies" },
                { "data": "plan_name" },
                
                { "data": "plan_type" },
                { "data": "grace_period" },
                { "data": "annual_cumm" },
                { "data": "annual_cumm_one" },
                { "data": "return_cumm" }
            ]

        });
        return oTable;
    }

    function add_ins_plan()
    {
        save_method = 'add';
        //reset form on modals
        $("#form")[0].reset();
        $('[name="insPlanRename"]').attr('readonly', true);
        $('[name="planName"]').select2('val', '');
        $("#insComp").select2('val','');
        $("#insPlanType").select2('val','');
        //show bootstrap modal
        $("#modal_form").modal('show');
        //set title to modal
        $(".modal-title").text('Add Insurance Plan');
    }
    function save()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
        if(save_method == 'add')
            url = "<?php echo site_url('broker/insurance_plans/add_ins_plan');?>";
        else
            url = "<?php echo site_url('broker/insurance_plans/update_ins_plan');?>";
        if($('#form').parsley('validate'))
        {
            $.ajax({
                url: url,
                type:'post',
                data: $("#form").serialize(),
                dataType: 'json',
                success:function(data)
                {
                    if(data['status'] != 0)
                    {
                        $("#modal_form").modal('hide');
                        table.destroy();
                        table = ajax_ins_plan_list();
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
                    l.stop();
                },
                error: function (data)
                {
                    console.log(data);
                    bootbox.alert("Something went terribly wrong");
                    l.stop();
                }
            });
        }
        else
        {
            l.stop();
        }
    }

    function edit_ins_plan(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('broker/Insurance_plans/edit_ins_plan');?>",
            type: "POST",
            data:{id: id},
            dataType: "JSON",
            success: function(data)
            {
                $('[name="insPlanID"]').val(data.plan_id);
                $('[name="insPlanRename"]').val(data.plan_name).attr('readonly', true);
                $('[name="planGrace"]').val(data.grace_period);
                $('[name="insComp"]').select2('val', data.ins_comp_id);
                $('[name="insPlanType"]').val(data.plan_type_id);
                $('[name="insBonus"]').val(data.annual_cumm);
                $('[name="insLoyal"]').val(data.annual_cumm_one);
                $('[name="insGrowth"]').val(data.return_cumm);
                $('[name="planName"]').select2('val', data.policy_id);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Insurance Plan'); // Set title to Bootstrap modal title
                checkPlanType();

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                bootbox.alert('Error get data from ajax');
            }
        });
    }

    function delete_ins_plan(id)
    {
        bootbox.confirm('Are you sure you want to delete this Insurance Plan?', function(result){
            if(result)
            {
                // ajax delete data to database
                $.ajax({
                    url : "<?php echo site_url('broker/Insurance_plans/delete_ins_plan');?>",
                    type: "POST",
                    data:{id: id},
                    dataType: "JSON",
                    success: function(data)
                    {
                        console.log(data);
                        if(data['status'])
                        {
                            $.pnotify({
                                title: 'Plan Deleted!',
                                text: 'Plan Deleted from Insurance',
                                type: 'success',
                                hide: true
                            });

                            //if success reload ajax table
                            $('#modal_form').modal('hide');
                            table.destroy();
                            table = ajax_ins_plan_list();
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
                    error: function (data)
                    {
                        console.log(data);
                    }
                });
            }
        });
    }
</script>