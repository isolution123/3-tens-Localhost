<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Clients');?>">Client Master</a></li>
                <li class="active">Family Transfer Of Clients</li>
            </ol>
            <h1>Family Transfer Of Clients</h1>

        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="<?php echo base_url('broker/Clients/client_to_family_transfer') ?>" id="mf_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Family & Clients List</h4>
                    </div>
                    <div class="panel-body collapse in" style="height:auto; !important;">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select Family Name</label>
                                <div class="col-md-3" >
                                  <!--<input type="hidden" name="for_merge" id="for_merge" value="merge">
                                    <input type="hidden" name="pre_merge_with" id="pre_merge_with" value="merge">-->
                                    <select name="sel_family_id" class="populate" required="required" id="sel_family_id" style="width: 100%" tabindex="1">    <!--//pallavi -2017-06-13 -->
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>
                        </div>

                        <div class="row" >
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select Clients  Name</label>
                                <div class="col-md-3" id="chk_val">
                                    <select name="sel_client_id[]" style="width: 100%"  id='client_id' class="js-example-basic-multiple" multiple="multiple">

                                    </select>
                                </div>

                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Transfer To The Family</label>
                                <div class="col-sm-4 add-new-btn" >

                                    <select id="select-family" name="tansfer_family_id" class="populate" required="required"  style="width: 80%" tabindex="1">
                                        <option disabled selected value="0">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:;" title="Add new family" tabindex="2" class="btn btn-xs btn-inverse-alt" onclick="add_family(true)"><i class="fa fa-plus"></i></a>


                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div style="padding-left:250px" >  
                                <button type="submit" id="import_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                                    <i class="fa fa-upload"></i>Transfer Client
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div> <!-- container -->
    </div> <!--wrap -->
</div> <!-- page-content -->
<script type="text/javascript">
    $(function() {
        
          $('#mf_form').submit(function(e) {
             var currentForm = this;
             e.preventDefault();
             bootbox.confirm("Would you like to Still continue with Family Transfer of clients having Family Head also?", function(result) {
                 if (result) {
                     currentForm.submit();
                 }
             });
         });

        
$(".js-example-basic-multiple").select2({
  placeholder: "Select a Client",
allowClear: true
});
    //   $("#client_id").multiselect();
    //   $("#client_id").multiselect({
    //     buttonWidth: '240px',
    //   maxHeight:260,
    //   maxWidth: 250,
    // // enableCaseInsensitiveFiltering: true, //for serch
    //    includeSelectAllOption: true,
    //                 });
        //initialize tooltip
        $('[data-toggle="tooltip"]').tooltip();
        //on load disable controls
        disableBtn();
        $('.populate').select2({width: 'resolve', maximumSelectionSize: 1});
        $('.date').datepicker({format: 'dd/mm/yyyy'}).inputmask();
        $("#adj_flag").change(function(){
            if(this.checked)
            {
                $("#adjustment_flag").val("1");
                $("#adjustment").attr('readonly',false);
                $("#adjustment_ref_number").attr('readonly',false);
            }
            else
            {
                $("#adjustment_flag").val("0");
                $("#adjustment").attr('readonly',true);
                $("#adjustment_ref_number").attr('readonly',true);
            }
        });
        //on family change get clients
          var count=0;
        $('#sel_family_id').change(function()
        {
           count=count+1;
          var mr=$('#for_merge').val();
        //  alert(mr);
            var url = "<?php echo site_url('broker/Clients/get_clients_broker_dropdown_refered');?>";
            getClientmerge(url, this.value,"",mr);
        });

        $('#chk_val').click(function(){
           if(count==0){
             if (!$('#client_id').val()) {
                 alert("Please Select Family First to load clients!!");
                 count=count+1;
                    }
                  }

        });

        function displayVals(){
        //  alert('hiiii');
          var rr = [];
          $('.selectpicker :selected').each(function(i, selected){
          rr[i] = $(selected).text();
             });
           $('#mts').val(rr);
        }

        $('#client_id').change(function()
        {

             displayVals();
          //  get_folios();
            $.ajax({
                url: '<?php echo site_url('broker/Banks/get_client_banks'); ?>',
                dataType: 'json',
                type: 'post',
                data: {clientID: this.value},
                success: function(data)
                {
                    var option = '<option selected disabled>Select Bank</option>';
                    $.each(data['banks'], function(i, item){
                        option = option + "<option value = " + item.bank_id + ">" + item.bank_name + "</option>";
                    });
                    $('#bank_id').html(option);
                },
                error: function(data)
                {
                    console.log(data);
                }
            });

            //on client change get family
            $.ajax({
                url: '<?php echo site_url('broker/Clients/get_client_family');?>',
                type: 'post',
                data: {clientID: this.value},
                dataType: 'json',
                success: function(data) {
                    if(data != 'fail') {
                        $("#sel_family_id").select2("val",data['family_id']); //pallavi: 2017-06-13
                    } else {
                        console.log("Unable to load family data! No clientID passed");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $.pnotify({
                        title: 'Error!',
                        text: 'Error getting family name by client',
                        type: 'error',
                        hide: true
                    });
                }
            });
        });

    });
function getvarify(){
  var selected = $("#client_id option:selected");
          var message = "";
        //  message+="Client Id"+"       "+"Client Name"+"\n";
          selected.each(function () {
               //check client id => for head of family flag set
               //on client change get family
               $id=$(this).val() ;
               $.ajax({
                   url: '<?php echo site_url('broker/Clients/check_family_head');?>',
                   type: 'post',
                   data: {clientID:$id },
                   dataType: 'json',
                   success: function(data) {
                    // alert(data);
                       if(data != 'fail') {
                           //$("#family_id").select2("val",data['family_id']);
                           if(data['head_of_family']==1){
                              message += data['client_id']+" "+data['name']  + "\n";
                              var tmsg='Selected clients  have following Family Head  also for  tarnsfer family'+'\n'+'Client Id'+'       '+'Client Name'+'\n'+message;
                                alert(tmsg);
                           }

                       } else {
                           console.log("Unable to get head of family flag");
                       }
                   },
                 });
            //  message += $(this).val() + "   " + $(this).text() + "\n";
          });

        //  alert('Selected clients  have following Family Head  also for  tarnsfer family'+"\n"+message);


          var user_choice = window.confirm('Would you like to Still continue with Family Transfer of clients having Family Head also?');


      if(user_choice==true) {


      window.location='base_url';  // you can also use element.submit() if your input type='submit'


      } else {


      return false;


      }
}

</script>
