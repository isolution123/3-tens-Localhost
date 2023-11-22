<?php
/*foreach($records as $rs){
echo "<pre>";    
print_r($rs);    
}

exit();*/

?>
<html>
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      
      <title></title>
      <style></style>
  </head>
  <body>
      <table border="0" cellpadding="0" cellspacing="0"  width="100%" id="bodyTable">
          <tr>
              <td align="center" >
                  <table cellpadding="6" cellspacing="0" id="emailContainer" style="border:solid 1px #4f8edc;width:100%">
                      <tbody>
                        <tr style="text-align:center">
                            <th colspan="9" width="100%" style="text-align:left">
                                  <span style="font-weight:bold;font-size:150%;text-align:left;padding-left:5px">CAGR Trigger Report</span> 
                            </th>
                        </tr>
                        <tr>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">No.</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Family Name</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Client Name</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Scheme Type</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Scheme Name</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Transaction Type</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Folio No.</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Transaction Date</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Purchase Amount</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Current Value</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Dividend Payout</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Total CAGR</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">ABS</th>
                        </tr>
                        
                        <?php
                            if(empty($records)){
                        ?>
                            <tr style="text-align:center">
                                <td align="center" colspan='11'>No Records Found.</td>
                            </tr>
                        <?php
                                
                            }else{
                                $i=1;
                                foreach($records as $rs){
                        ?>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $i; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['family_name']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['client_name']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['scheme_type']; ?></td>                                
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['scheme_name']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['mutual_fund_type']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['folio_number']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo date("d-m-Y", strtotime($rs['purchase_date'])); ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo round($rs['purchase_amount']); ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo round($rs['current_value']); ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo round($rs['div_payout']); ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['mf_cagr']; ?></td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $rs['mf_abs']; ?></td>
                            </tr>
                        <?php
                                $i++;
                                }
                            }
                        ?>
                        </tbody>
                  </table>
              </td>
          </tr>
      </table>
  </body>
</html>
