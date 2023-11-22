
<style>
.hidden{display:none;}
.subject-info-box-1,
.subject-info-box-2 {
    float: left;
    width: 30%;
     padding-left:50px;

    select {
        height: 400px;
        padding: 0;
        option {
            padding: 10px 10px 4px 10px;
        }
        option:hover {
            background: #EEEEEE;
        }
    }
}
.subject-info-arrows {
    float: left;
    width: 10%;
    padding-left:40px;
    input {
        width: 50%;
        margin-bottom: 5px;
    }
}
</style>
<style type="text/css">

    @media screen and (min-width: 768px) {

        .modal-dialog {

          width: 1083px; /* New width for default modal */

        }

        .modal-sm {

          width: 350px; /* New width for small modal */

        }

    }

    @media screen and (min-width: 992px) {

        .modal-lg {

          width: 950px; /* New width for large modal */

        }

    }

</style>
<div id="page-content" xmlns="http://www.w3.org/1999/html">
    <div id='wrap'>
        <div id="page-heading">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('broker/Dashboard')?>">Dashboard</a></li>
                <li><a href="<?php echo base_url('broker/Clients');?>">Client Master</a></li>
                <li class="active">Merge Clients</li>
            </ol>
            <h1>Merge Clients</h1>

        </div>
        <div class="container">
            <input type="hidden" id="transID" value="0">
            <form action="<?php echo base_url('broker/Clients/clients_merge') ?>" id="mf_form" method="post" class="form-horizontal row-border" data-validate="parsley" >
                <div class="panel panel-midnightblue control-form">
                    <div class="panel-heading">
                        <h4>Client Information</h4>
                    </div>
                    <div class="panel-body collapse in">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Family Name</label>
                                <div class="col-md-3">
                                  <input type="hidden" name="for_merge" id="for_merge" value="merge">
                                    <input type="hidden" name="pre_merge_with" id="pre_merge_with" value="merge">
                                    <select name="family_id" class="populate"  id="family_id" style="width: 100%" tabindex="1">
                                        <option disabled selected value="">Select Family</option>
                                        <?php foreach($families as $row):?>
                                            <option value='<?php echo $row->family_id; ?>'><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>
                        </div>
                        <div class="row">
                          <table cellspacing="5" cellpadding="5" border="0">
                          <tbody><tr>
  <td><a id="byfname" tabindex="4" class="btn btn-success" style="margin-top: 20px;">FirstName + DOB </a></td>

                                </tr>
                          </tbody></table>
                             <table class="table table-striped table-bordered"  id="example"  cellspacing="0" width="100%">
     <thead>
         <tr>
               <th class="action-col-3">Action</th>
             <!-- <th class="action-col-3"><input name="select_all" value="1" id="example-select-all" type="checkbox"></th>-->
             <!-- <th>ID</th> -->
             <th>Name</th>
             <th>dob</th>
             <th>pan</th>
         </tr>
        </thead>
        <tfoot>
        <tr>
          <th class="action-col-3"></th>
          <th>Name</th>
          <th>dob</th>
          <th>pan</th>
        </tr>
          </tfoot>
 </table>
                        </div>

                      <div style="padding-left:250px">
<!--
                        <a data-toggle="modal" data-target="#myModal" data-Id="<?php echo "hello";?>" type="submit" id="import_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                            <i class="fa fa-upload"></i> Merge Client
                        </a> -->
                        <a id="import_btn" tabindex="4" class="btn btn-success" style="margin-top: 20px;">
                            <i class="fa fa-upload"></i> Merge Client
                        </a>
                      </div>
                    </div>

                </div>
            </form>

        </div> <!-- container -->

          <div class="container">
            <div class="panel panel-midnightblue control-form">
            <div class="panel-heading">
                <h4>Clients Merge History</h4>
            </div>
            <div class="panel-body collapse in">

        <div class="table-responsive">
            <table class="table table-striped table-bordered " id="table">
                <thead>
                  <tr>
                      <!--<th class="action-col-3">Action</th>-->
                      <th>Client ID</th>
                      <th>Family</th>
                      <th>Name</th>
                      <th>Merged With Client Name</th>
                  </tr>
                  </thead>
                  <tfoot>
                  <tr>
                    <!--  <th class="action-col"></th>-->
                      <th>Client ID</th>
                      <th>Family</th>
                      <th>Name</th>
                      <th>Merged With Client Name</th>
                  </tr>
                    </tfoot>

            </table><!--end table-->
            <div class="loading-data"><img src="<?php echo base_url('assets/users/img/load.gif');?>"><span>Please wait, loading data...</span></div>
        </div>
      </div>
        </div>
          </div>

    </div> <!--wrap -->
</div> <!-- page-content -->


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel">Client Merging List</h4>

            </div>
            <div class="modal-body">
                <!--<label style="color:red">Note:Be careful! while selecting a record,as system will only keep record of the selected client like(PAN NO,DOB,ADDRESS,MOBILE etc). </label>-->
                <p style="color:red">The following clients will be merged. Please select <strong>only one client with maximum records</strong> as base client. (PAN No., DOB., Mobile No., Latest Address to keep etc.).</p> <p style="color:red"><strong>Records of the selected client will only be saved.</strong></p><br/>
           <!-- <select name="clist" id="clist">
          <option value="">select clients</option>
           </select> -->
           <form action="<?php echo base_url('broker/Clients/clients_merge') ?>" id="final_merge" method="post" class="form-horizontal row-border" data-validate="parsley"  >
           <div class="table-responsive">
               <table class="table table-striped table-bordered table-full-width" id="modal_table">
                   <thead>
                   <tr>
                       <th class="action-col-3">Action</th>
                       <!--<th>Client ID</th> -->
                       <th>Family</th>
                       <th>Name</th>
                       <th>Date of Birth </th>
                       <th>PAN No</th>
                       <th>Mobile No</th>
                       <th>Email ID</th>
                       <th>Address</th>
                       <!--<th>Category</th>-->
                       <!--<th>Commence</th>-->
                   </tr>
                   </thead>

               </table><!--end table-->
               <div class="loading-data"><img src="<?php echo base_url('assets/users/img/load.gif');?>"><span>Please wait, loading data...</span></div>
           </div>


            </div>

            <div class="modal-footer">
                <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
                <a  onclick="save_final_merge()" id="btn_save"  class="btn btn-primary">Save changes</a>
            </div>
             </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var table;
    $(function() {
        //keyboard shortcut for merging ctrl+m
      Mousetrap.bind(['ctrl+m'], function(e) {
  $("#import_btn").trigger("click");
    return false;
});
  //keyboard shortcut for client merging save  press enter button
Mousetrap.bind(['enter'], function(e) {
save_final_merge();
return false;
});
        
      table = ajax_list();
      table=ajax_list1();
    table_initialize();
 
      $('#example tbody').on( 'click', 'tr', function () {
    //  $(this).toggleClass('selected');
      if($(this).toggleClass('selected').hasClass('selected')){
           $(this).find('input[type="checkbox"]').prop('checked', true);
         }else{
          $(this).find('input[type="checkbox"]').prop('checked', false);
         }


  } );
      //  $("#client_id").multiselect();
      // $("#client_id").multiselect({
      // // maxHeight: 200,
      // // enableCaseInsensitiveFiltering: true,
      //  includeSelectAllOption: true
      //               });
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
        $('#family_id').change(function()
        {
            ajax_familywise_client();
        });

        function ajax_familywise_client(){
          var mr=$('#for_merge').val();
          familyValue=$('#family_id').val();
         //alert(mr);
            var url = "<?php echo site_url('broker/Clients/get_clients_familywise');?>";
          //  getClientsFamilywise(url, this.value);

          $.ajax({
              url: url,
              type:'post',
              data: {familyID: familyValue},
              dataType: 'json',
              success:function(data)
              {
                assignToEventsColumns(data);

              },
              error: function(jqXHR, textStatus, errorThrown){
                    alert('error: ' + textStatus + ': ' + errorThrown);
                }
              });
        }

        function assignToEventsColumns(data) {
                    var table = $('#example').dataTable({
                        "bAutoWidth" : false,
                        "bDestroy": true,
                        "aaData" : data,
                        'columnDefs': [{
                 'targets': 0,
                 'searchable': false,
                 'orderable': false,
                 'className': 'dt-body-center',
                 'render': function (data, type, full, meta){
                     return '<input type="checkbox" name="cid[]" class="call-checkbox" id="' + $('<div/>').text(data).html() + '" value="' + $('<div/>').text(data).html() + '">';
                 }
              }],
               'order': [[1, 'asc']],
                        "columns" : [

                           {
                            "data" : "client_id"
                           },
                          {
                            "data" : "name"
                           },
                           {
                            "data" : "dob"
                           },
                          {
                            "data" : "pan_no"
                        }
                      ]
                    })
        }

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
                        $("#family_id").select2("val",data['family_id']);
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


        $("#import_btn").click(function(){
        //  alert('hiiii');
var favorite = [];
var oTable = $('#example').dataTable();
var rowcollection =  oTable.$(".call-checkbox:checked", {"page": "all"});
rowcollection.each(function(index,elem){
var checkbox_value = $(elem).val();
//alert(checkbox_value);
//Do something with 'checkbox_value'
favorite.push(checkbox_value);
});

          var url = "<?php echo site_url('broker/Clients/get_details_clients');?>";
        $.ajax({
            url: url,
            type:'post',
            data: {clientIDs:JSON.stringify(favorite) },
            dataType: 'json',
            success:function(data)
            {
            // alert(data);
          var table = $('#modal_table').dataTable({
              "bAutoWidth" : false,
              "bDestroy": true,
              "bPaginate": false,
              "bFilter": false,
              "bInfo": false,
              "aaData" : data,
              'columnDefs': [{
       'targets': 0,
       'searchable': true,
       'orderable': false,
       'className': 'dt-body-center',
       'render': function (data, type, full, meta){
           return '<input type="radio" name="cid[]" class="call-checkbox" id="' + $('<div/>').text(data).html() + '" value="' + $('<div/>').text(data).html() + '">';
       }
    }],
     'order': [[1, 'asc']],
              "columns" : [

                { "data": "client_id" },
                { "data": "f_name" },
                { "data": "c_name" },
                { "data": "dob" , "type": "date-uk"},
                { "data": "pan_no" },
                { "data": "mobile" },
                { "data": "email_id" },
                // { "data": "client_category" },
                // { "data": "date_of_comm" }
                { "data": getAddress }
            ]
          });

            function getAddress(data, type, dataToSet){
          return data.add_flat + "," + data.add_street+","+data.add_area+","+data.add_city+" "+data.add_state+"-"+data.add_pin;
    }


            $('#myModal').modal('show');
            },
            // error: function(jqXHR, textStatus, errorThrown){
            //       alert('error: ' + textStatus + ': ' + errorThrown);
            //   }
            });


        });

    });

    /////////Merging Data Table part////////////////

     $('#byfname').on('click',function(){

      var  familyValue=$('#family_id').val();
        //alert(mr);
           var url = "<?php echo site_url('broker/Clients/get_clients_familywise_summery');?>";
         $.ajax({
             url: url,
             type:'post',
             data: {familyID: familyValue},
             dataType: 'json',
             success:function(data)
             {
               var table = $('#example').dataTable({
                   "bAutoWidth" : false,
                   "bDestroy": true,
                   "aaData" : data,
                   'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" name="cid[]" class="call-checkbox" id="' + $('<div/>').text(data).html() + '" value="' + $('<div/>').text(data).html() + '">';
            }
         }],
          'order': [[1, 'asc']],
                   "columns" : [

                      {
                       "data" : "client_id"
                      },
                     {
                       "data" : "name"
                      },
                      {
                       "data" : "dob"
                      },
                     {
                       "data" : "pan_no"
                   }
                 ]
               });

             },
            //  error: function(jqXHR, textStatus, errorThrown){
            //       alert('error: ' + textStatus + ': ' + errorThrown);
            //   }
             });

     });


    $('#example-select-all').on('click', function(){
         // Get all rows with search applied
         var table = $('#example').DataTable();
         var rows = table.rows({ 'search': 'applied' }).nodes();

         // Check/uncheck checkboxes for all rows in the table
         $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });
      $('#example tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
   });
   ///////// End Merging Data Table part////////////////

    function ajax_list()
    {
        var oTable = $("#table").DataTable({
          "processing":true,    //Control the processing indicator
          "serverSide":false,    //Control DataTable server process
          "aaSorting": [[1,'desc']],
          "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
              { "bSearchable": false, "aTargets": [ 0 ] }],
              "ajax": {
                  //Load data for the table through ajax
                  "url": '<?php echo site_url('broker/clients/ajax_list_merged_clients');?>',
                  "type": 'post'


              },
              "columns": [
                  { "data": "client_id" },
                  { "data": "f_name" },
                  { "data": "c_name" },
                //  { "data": "dob" , "type": "date-uk"},
                //  { "data": "pan_no" },
                //  { "data": "mobile" },
                //  { "data": "email_id" },
                //  { "data": "client_category" },
                  { "data": "merge_ref_id" }
              ]


        });
      //  alert(oTable);
          return oTable;
    }

    function ajax_list1(){
      var oTable = $("#example").DataTable({
        "processing":true,    //Control the processing indicator
        "serverSide":false,    //Control DataTable server process
        "aaSorting": [[1,'desc']],

        "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] },
            { "bSearchable": false, "aTargets": [ 0 ] }],
            "ajax": {
                //Load data for the table through ajax
                "url": '<?php echo site_url('broker/clients/get_clients_familywise');?>',
                "type": 'post'


            },

            "columns": [
                { "data": "client_id" },
                { "data": "name" },
                { "data": "dob" },
                { "data": "pan_no" }
            ]
      });
    //  alert(oTable);
        return oTable;
    }
</script>
<script>
function save_final_merge(){
  //alert('hellooooo');
  var checked_arr = [];
  var unchecked_arr = [];
  var oTable = $('#modal_table').dataTable();

  var rowcollection =  oTable.$(".call-checkbox:checked", {"page": "all"});
  rowcollection.each(function(index,elem){
  var checkbox_value = $(elem).val();
  checked_arr.push(checkbox_value);
  });

  var rowcollection1 =  oTable.$(".call-checkbox:unchecked", {"page": "all"});
  rowcollection1.each(function(index,elem){
  var checkbox_value1 = $(elem).val();
  unchecked_arr.push(checkbox_value1);
  });
    //alert("checked list"+checked_arr);
    //  alert("unchecked"+unchecked_arr);
      var url = "<?php echo site_url('broker/Clients/clients_merge');?>";
      //
     // if (confirm('Are you sure to merge other clients with selected client,beacuase it will result to loss of ther clients data?')) {
       $.ajax({
             url: url,
            type:'post',
            data: {checkedclient:JSON.stringify(checked_arr),uncheckedclient:JSON.stringify(unchecked_arr)},
          //  dataType: 'json',
            success:function(data)
           {

             $('#myModal').modal('hide');
            alert('Clients merged successfully!');
             window.location.reload();

            }
            // ,
            // error: function (jqXHR, textStatus, errorThrown)
            // {
            //     console.log(jqXHR);
            //     bootbox.alert('Error while client merging');
            // }
         });
      // }

}

</script>
