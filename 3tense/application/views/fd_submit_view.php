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
                                  <span style="font-weight:bold;font-size:150%;text-align:left;padding-left:5px">Individual FD</span> 
                            </th>
                        </tr>
                        <tr>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Field</th>
                            <th align="center" style="background-color:#4f8edc;color:#fff;">Value</th>
                        </tr>
                        
                        <?php
                            if(empty($records)){
                        ?>
                            <tr style="text-align:center">
                                <td align="center" colspan='2'>No Records Found.</td>
                            </tr>
                        <?php
                        }else{
                    
                        ?>
                        
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Company Name</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['companyName']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Rate</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['rate']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Rate Type</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['RateType']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Invester Name</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['InvesterName']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">DOB</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php 
                                if($records['DOB']!='')
                                {
                                  echo date_format(date_create($records['DOB']),"d/m/Y"); 
                                }
                               
                                
                                ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Pancard</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['Pancard']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Address</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['Address']; ?></td>
                            </tr>
                            
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">EmailId</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['EmailId']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">MobileNo</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['MobileNo']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holding</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['Holding']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Name 2</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderName2']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Pancard 2</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderPancard2']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder DOB 2</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php 
                                
                                  if($records['HolderDOB2']!='')
                                {
                                  echo date_format(date_create($records['HolderDOB2']),"d/m/Y"); 
                                }
                               
                              
                                ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Address 2</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderAddress2']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Name 3</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderName3']; ?></td>
                            </tr>
                            
                            
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Pancard 3</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderPancard3']; ?></td>
                            </tr>
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Holder Address 3</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['HolderAddress3']; ?></td>
                            </tr>
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Nominee Name</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['NomineeName']; ?></td>
                            </tr>  <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Nominee DOB</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php 
                                  if($records['NomineeDOB']!='')
                                {
                                  echo date_format(date_create($records['NomineeDOB']),"d/m/Y"); 
                                }
                               
                              
                                ?></td>
                            </tr>
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Gaurdian Name</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['GaurdianName']; ?></td>
                            </tr>
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Gaurdian DOB</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php 
                                if($records['GaurdianNameDOB']!='')
                                {
                                  echo date_format(date_create($records['GaurdianNameDOB']),"d/m/Y"); 
                                }
                                ?></td>
                            </tr>  
                            <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Nominee Relation</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['Relation']; ?></td>
                            </tr>
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">Tenure</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['Tenure']; ?></td>
                            </tr>
                            
                              <tr style="text-align:center">
                                <td align="center" style="border:solid 1px #4f8edc">scheme</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['scheme']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                              <td align="center" style="border:solid 1px #4f8edc">Interest Frequency</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['InterestFrequency']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                              <td align="center" style="border:solid 1px #4f8edc">Auto Renewal/Refund</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php echo $records['renewalType']; ?></td>
                            </tr>
                            <tr style="text-align:center">
                              <td align="center" style="border:solid 1px #4f8edc">Preferred Date and Time for Cheque Collection</td>
                                <td align="center" style="border:solid 1px #4f8edc"><?php 
                           
                                    if($records['chequeCollectiondt']!='')
                                {
                                  echo $records['chequeCollectiondt']; 
                                }
                               
                                ?></td>
                            </tr>
                            
                        <?php
                            }
                        ?>
                        </tbody>
                  </table>
              </td>
          </tr>
      </table>
  </body>
</html>
