<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li class="active">Send Push Notification</a></li>
            </ol>
            <h1>Send Push Notification</h1>
        </div>
        <div class="container">
            
            <form action="#" id="notification_form" method="post" class="form-horizontal row-border" onsubmit="return submit_form_info();" enctype="multipart/form-data">
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Notification Details</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Send To</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="send_to" class="populate" required="required" id="send_to" style="width: 80%" tabindex="1">
                                        <option>All Clients</option>
                                        <option>Selected Categories</option>
                                        <option>Selected Clients</option>
                                    </select>
                                </div> 
                            </div>
                        </div>
                       <div class="row" style="display: none;" id="cat_list">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Categories</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="categories[]" class="select2" id="categories" style="width: 80%" tabindex="1"multiple>
                                       
                                       <?php
                                       foreach ($categories as $cat)
                                       {
                                           echo '<option>'.$cat->client_category.'</option>';
                                           //echo '<pre>';print_r($cat);die;
                                       }
                                       ?>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="row" style="display: none;" id="client_list">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Clients</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <select name="clients[]" class="select2" id="clients" style="width: 80%" tabindex="1" multiple>
                                       
                                       <?php
                                       foreach ($clients as $client)
                                       {
                                           echo '<option value="'.$client->client_id.'">'.$client->name.'</option>';
                                           //echo '<pre>';print_r($client);die;
                                       }
                                       ?>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Title</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="text" name="title" id="title" required style="width:180% !important"/>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Message Body</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <textarea name="body" id="body" cols="35" rows="5" required></textarea>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Image</label>
                                <div class="col-md-4 no-border add-new-btn">
                                    <input type="file" name="image" id="image" accept="image/*"/>
                                </div> 
                            </div>
                        </div>
                        <div class="col-sm-12 bottom-col">
                            <input type="submit" class="btn btn-primary" title="Submit" id="submit_btn"/>
                            <div class="loading-data"><img src="<?php echo base_url('assets/users/img/load.gif');?>"><span>Please wait, processing request...</span></div>
                        </div>
                    </div>
                </div>
            </form>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<script type="text/javascript">
    
$('.select2').select2();
$('#send_to').on('change', function() {
    if(this.value == 'Selected Categories')
    {
        $("#client_list").hide();
        $("#cat_list").show();
    }
    else if(this.value == 'Selected Clients')
    {
        $("#client_list").show();
        $("#cat_list").hide();
    }
    else
    {
        $("#client_list").hide();
        $("#cat_list").hide();
    }
});

function submit_form_info()
{
    var send_to = $("#send_to").val();
    var cats = $("#categories").val();
    var clients = $("#clients").val();
    
    if(send_to == 'Selected Categories' && !cats)
    {
        bootbox.alert("Please select categories");
        return false;
    }
    
    if(send_to == 'Selected Clients' && !clients)
    {
        bootbox.alert("Please select clients");
        return false;
    }
    var frm = $('#notification_form')[0];
    
    var fields = new FormData(frm);
    
    $(".loading-data").show();
    $("#submit_btn").hide();
    
    $.ajax({
            url: "<?php echo base_url('broker/Notifications/send')?>",
            data: fields,
            enctype: 'multipart/form-data',
            type: "POST",
            processData: false,
            contentType: false,
            cache:false,
            success: function(data) {
                bootbox.alert("Notifications sent successfully");
                $("#notification_form").trigger('reset');
                $(".select2").each(function () { //added a each loop here
                    $(this).select2('val', '')
                });
                
                $("#send_to").trigger('change');
                $(".loading-data").hide();
                $("#submit_btn").show();
                
            },
            error: function(data) {
                bootbox.alert("Error while sending notifications.");
                $(".loading-data").hide();
                $("#submit_btn").show();
                console.log(data);
            }
        });

    return false;
}
</script>