

<?php include "header-focused.php"; ?>
<br/><br/>
<style>
    table tr th
    {
        text-align:center;
    }
</style>

<div class="container body">
    <div  class="row right_col" role="main" style="margin-top:15px;padding:10px 10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center p-0 mt-3 mb-2">
            <div class="row col-md-12">
                     <img src="<?php echo site_url('uploads/thankyou.png"'); ?>" />
                </div>
               <table  class="display table table-striped table-bordered table-full-width dataTable" width="100%">
                    <tr>
                        <th>Order Number</th>
                        <th>Order Ref No</th>
                        <th>Order Amount</th>
                    </tr>
                    <tr>
                        <td>
                            <?php print_r($transcation_data[0]->OrderId); ?> 
                        </td>
                        <td>
                            <?php print_r($transcation_data[0]->UniqueReferenceNumber); ?>
                        </td>
                        <td>
                            <?php print_r($transcation_data[0]->Amount); ?>
                        </td>
                    </tr>
                </table>
            <br>
            <br>
                    
                 <a  href="<?php echo site_url('client/PurchaseMutualFund/sip'); ?>"  class="btn btn-primary" >Place New order</a> 
        </div>
    </div>
</div>  

