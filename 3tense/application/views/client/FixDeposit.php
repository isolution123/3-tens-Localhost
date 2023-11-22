

<?php include "header-focused.php"; ?>
<br/><br/>
<style>
    table tr th
    {
        background-color:#0ab1cf;
        color:white;
        text-align:center;
    }
    table tr td
    {
        text-align:Center;
    }
    .panel-primary>.panel-heading
    {
        background-color:#fbb63d;
        border-color:#fbb63d;
    }
    .radiobutton
    {
         -ms-transform: scale(1.5); /* IE 9 */
    -webkit-transform: scale(1.5); /* Chrome, Safari, Opera */
    transform: scale(1.5);
    }
    .ratelink
    {
        cursor:pointer;
         text-decoration: underline;
    }
</style>

<div class="container body">
    <!-- MultiStep Form -->

                        
                
    <div  class="row right_col" role="main" style="margin-top:15px;padding:10px 10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center p-0 mt-3 mb-2">
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                <div class="panel panel-primary">
                <div class="panel-heading">
                    
                    <input type="radio" id="html" name="fav_language" value="Individual" class="radiobutton" checked="checked"> &nbsp;
                    <label for="html">Individual And Non-Individual</label>&nbsp;&nbsp;
                    <input type="radio" id="css" name="fav_language" value="Senior" class="radiobutton">&nbsp;
                    <label for="css">Senior Citizen</label>
                    
                    <a class="btn btn-primary" href="<?php echo site_url('client/Purchase')?>"  style='float:right'>Back</a><br>
                </div>
            </div>
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3" id="SeniorCity" style="display:none">
                <table class="display table table-striped table-bordered table-full-width dataTable">
                 
                   <tr>
                       <th style="width:30%">
                           COMPANY NAME
                       </th>
                       <th style="width:10%">
                           PERIOD (Months)
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE MONTHLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE QUARTERLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE HALF YEARLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE YEARLY
                       </th>
                       <th style="width:10%">
                           CUMULATIVE
                       </th>
                   </tr>
                    <?php foreach($SeniorCity as $row):?>
                        <tr>
                       <td>
                           <?php echo $row->CompanyName; ?>
                       </td>
                       <td>
                           <?php echo $row->Period; ?>
                       </td>
                       <td>
                            <a class="ratelink" title="Click Here To Invest " href="<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id='.$row->Id.'&RateType=NonCumulativeMonthly'); ?>" > <?php echo $row->NonCumulativeMonthly; ?>% </a>
                       </td>
                       <td>
                           <a class="ratelink" title="Click Here" href="<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id='.$row->Id.'&RateType=NonCumulativeQuatuerly'); ?>" > <?php echo $row->NonCumulativeQuatuerly; ?>% </a>
                           
                       </td>
                       <td>
                           
                            <a class="ratelink" href="<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id='.$row->Id.'&RateType=NonCumulativeHalfYearly'); ?>" > <?php echo $row->NonCumulativeHalfYearly; ?>% </a>
                       </td>
                        <td>
                           
                           <a class="ratelink" href="<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id='.$row->Id.'&RateType=NonCumulativeYearly'); ?>" > <?php echo $row->NonCumulativeYearly; ?>% </a>
                       </td>
                       <td>
                           <a class="ratelink" href="<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id='.$row->Id.'&RateType=Cumulative'); ?>" > <?php echo $row->Cumulative; ?>% </a>
                           
                       </td>
                   </tr>            
                    <?php endforeach; ?>
            
               </table>
            </div>
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3" id="NonSeniorCity" style="display:none">
                <table class="display table table-striped table-bordered table-full-width dataTable">
                 
                   <tr>
                       <th style="width:30%">
                           COMPANY NAME
                       </th>
                       <th style="width:10%">
                           PERIOD (Months)
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE MONTHLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE QUARTERLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE HALF YEARLY
                       </th>
                       <th style="width:10%">
                           NON CUMULATIVE YEARLY
                       </th>
                       <th style="width:10%">
                           CUMULATIVE
                       </th>
                   </tr>
                    <?php foreach($NonSeniorCity as $row):?>
                        <tr>
                       <td>
                           <?php echo $row->CompanyName; ?>
                       </td>
                       <td>
                           <?php echo $row->Period; ?>
                       </td>
                       <td>
                             <a class="ratelink" onClick="openFdPopup(<?php echo $row->Id; ?>,'NonCumulativeMonthly')"> <?php echo $row->NonCumulativeMonthly; ?>% </a>
                           
                       </td>
                       <td>
                           <a class="ratelink" onClick="openFdPopup(<?php echo $row->Id; ?>,'NonCumulativeQuatuerly')"> <?php echo $row->NonCumulativeQuatuerly; ?>% </a>
                          
                       </td>
                       <td>
                           <a class="ratelink" onClick="openFdPopup(<?php echo $row->Id; ?>,'NonCumulativeHalfYearly')"> <?php echo $row->NonCumulativeHalfYearly; ?>% </a>
                          
                       </td>
                        <td>
                            <a  class="ratelink" onClick="openFdPopup(<?php echo $row->Id; ?>,'NonCumulativeYearly')"> <?php echo $row->NonCumulativeYearly; ?>% </a>
                          
                       </td>
                       <td>
                           <a class="ratelink" onClick="openFdPopup(<?php echo $row->Id; ?>,'Cumulative')"> <?php echo $row->Cumulative; ?>% </a>
                          
                       </td>
                   </tr>            
                    <?php endforeach; ?>
            
               </table>
            </div>
        </div>
    </div>
          </div>
  </div>  
  <div id="FDpopup" class="modal fade" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Fixed Deposit</h4>
            </div>
            <div class="modal-body">
                Select FD Type:&nbsp;&nbsp;
               <input type="radio" id="html" name="r_FD_Type" value="Individual">
                <label for="html">Individual</label>&nbsp;
                <input type="radio" id="css" name="r_FD_Type" value="Non-Individual">
                <label for="css">Non-Individual</label>
                <input type="hidden" id='hndId'>
                <input type="hidden" id='hndRateType'>
            </div>
            <div class="modal-footer">
               
                <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="button" onclick='Go()' style="float:right" class="btn btn-primary" >Go</button>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
   
    $(function(){
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
      
        $('input[type=radio][name=fav_language]').change(function() {
            if (this.value == 'Individual') {
                $('#SeniorCity').hide();
                $('#NonSeniorCity').show();
            }
            else {
                $('#SeniorCity').show();
                $('#NonSeniorCity').hide();
            }
        });
      $('input[type=radio][name=fav_language][value=Individual]').change();
        
    });
    function Go()
    {
        if(!$("input[name='r_FD_Type']:checked").val())
        {
            alert('Please select FD Type');
            return 0;
        }
        else
        {
            var Type=$("input[name='r_FD_Type']:checked").val();
            var Id= $('#hndId').val();
            var RateType=$('#hndRateType').val();
            if(Type=='Individual')
            {
                var win = window.location.replace('<?php echo site_url('client/PurchaseMutualFund/FDIndivisual?Id=');?>'+Id+'&RateType='+RateType);
            }
            else
            {
                var win = window.location.replace('<?php echo site_url('client/PurchaseMutualFund/FDNonIndividual?Id=');?>'+Id+'&RateType='+RateType);
            }
            $('#FDpopup').modal('hide');
        }
    }
    function openFdPopup(Id,ratetype)
    {
        $('#hndId').val(Id);
        $('#hndRateType').val(ratetype);
        
        $('#FDpopup').modal('show');
    }

</script>
          
