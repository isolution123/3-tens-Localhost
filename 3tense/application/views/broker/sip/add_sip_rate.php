

<div id="page-content">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard');?>">Dashboard</a></li>
                <li>Masters</li>
                <li class="active">Add SIP Rate Of Return</li>
            </ol>
            <h1>SIP</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading" style="background-color:#324e9f;padding:0px 0px 0px 10px;">
                            <h4 style="color:white;">SIP Rate Of Return</h4>
                        </div>
                        <div class="panel-body collapse in">
                            <div class="modal-content" style="width: 50%;margin: 0% 25% 0% 25%;-webkit-box-shadow:none;box-shadow:none;">
                                <div class="modal-header">
                                   
                                    <h3 class="modal-title">Add/Edit SIP Rate Of Return</h3>
                                </div>
                                <div class="modal-body form">
                                    <form action="#" id="form" class="form-horizontal" data-validate="parsley">
                                        <input type="hidden" value="" name="id">
                                        <div class="form-body">
                                             
                                          <?php if(isset($rate) && !empty($rate)) { ?>
                                           <?php foreach($rate as $tr) { ?>
                                            <?php if(strtoupper($tr->scheme_type) == 'DEBT') { ?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Debt</label>
                                                    <div class="col-md-8">
                                                        <input name="debt" id="debt" data-type="number" value="<?php echo (isset($tr->rate) && !empty($tr->rate))?$tr->rate:'10'; ?>" placeholder="Debt" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                                <?php } elseif(strtoupper($tr->scheme_type) == 'EQUITY') { ?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Equity</label>
                                                    <div class="col-md-8">
                                                        <input name="equity" id="equity" data-type="number" value="<?php echo (isset($tr->rate) && !empty($tr->rate))?$tr->rate:'10'; ?>"  placeholder="Equity" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                                <?php } elseif(strtoupper($tr->scheme_type) == 'HYBRID') { ?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Hybrid</label>
                                                    <div class="col-md-8">
                                                        <input name="hybrid" id="hybrid" data-type="number" value="<?php echo (isset($tr->rate) && !empty($tr->rate))?$tr->rate:'10'; ?>"  placeholder="Hybrid" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            <?php } ?>
                                           <?php } else { ?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Debt</label>
                                                    <div class="col-md-8">
                                                        <input name="debt" id="debt" data-type="number" value="10" placeholder="Debt" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Equity</label>
                                                    <div class="col-md-8">
                                                        <input name="equity" id="equity" data-type="number" value="10"  placeholder="Equity" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Hybrid</label>
                                                    <div class="col-md-8">
                                                        <input name="hybrid" id="hybrid" data-type="number" value="10"  placeholder="Hybrid" class="form-control parsley-validated" type="text">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                                    
                                </div>
                        
                               </div>
                        
                            <!--<button class="btn btn-success" tabindex="1" onclick="add_ins_plan()"><i class="fa fa-plus"></i> Add SIP Rate</button>-->
                            <br /><br />
                            
                           
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Bootstrap modal For SIP Rate Of Return---->
            <div class="modal fade" id="modal_form" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"></h3>
                        </div>
                        <div class="modal-body form">
                            <form action="#" id="form1" class="form-horizontal" data-validate="parsley">
                                <input type="hidden" value="" name="id"/>
                                <div class="form-body">
                                     
                                  
                                   
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Debt</label>
                                        <div class="col-md-8">
                                            <input name="debt" id="debt" data-type="number" placeholder="Debt" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Equity</label>
                                        <div class="col-md-8">
                                            <input name="equity" id="equity" data-type="number" placeholder="Equity" class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Hybrid</label>
                                        <div class="col-md-8">
                                            <input name="hybrid" id="hybrid" data-type="number" placeholder="Hybrid" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnSave1" onclick="save()" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                        
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>    <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    $(document).ready(function(){
        //check rename checkbox is checked, if yes enable textbox
        
        
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
        $(".modal-title").text('Add/Edit SIP Rate Of Return');
    }
    function save()
    {
        var url;
        var button = $('#btnSave');
        var l = Ladda.create(button[0]);
        l.start();
    
       
            url = "<?php echo site_url('broker/Add_Sip_Rate/add_sip_rate');?>";
        
        if($('#form').parsley('validate'))
        {
            var data=$("#form").serialize();
          
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