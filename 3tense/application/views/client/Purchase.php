

<?php include "header-focused.php"; ?>
<br/><br/>
<style>
   .card-body 
   {
       border:1px solid;
       
   }
   .card-title
   {
      border-bottom:1px solid;
       padding:15px;
       margin:0px;
   }
   .card-text
   {
       padding:10px;
       margin:0px;
   }
   .first-card-title
   {
       background:#ffa300;
       border-color:#fbb12f!important;
       color:white;
       font-size: 18px;
       font-weight: bold;
   }
    .first-card-body
   {
       
       border-color:#ffa300!important;
       border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    min-height: 192px;
    height: 192px;
        margin-top: 10px
   }
   .second-card-title
   {
       background:#0099d9;
       border-color:#0099d9!important;
       color:white;
       font-size: 18px;
       font-weight: bold;
   }
    .second-card-body
   {
       
       border-color:#0099d9!important;
       border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    min-height: 192px;
    height: 192px;
        margin-top: 10px;
   }
    .third-card-title
   {
       background:#ed3337;
       border-color:#ed3337!important;
       color:white;
       font-size: 18px;
       font-weight: bold;
   }
    .third-card-body
   {
       
       border-color:#ed3337!important;
       border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    min-height: 192px;
    height: 192px;
        margin-top: 10px
   }
   .four-card-title
   {
       background:#b7337c;
       border-color:#b7337c!important;
       color:white;
       font-size: 18px;
       font-weight: bold;
   }
    .four-card-body
   {
       
       border-color:#b7337c!important;
       border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    min-height: 192px;
    height: 192px;
        margin-top: 10px
   }
     .five-card-title
   {
       background:#589e22;
       border-color:#589e22!important;
       color:white;
       font-size: 18px;
       font-weight: bold;
   }
    .five-card-body
   {
       
       border-color:#589e22!important;
       border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
    min-height: 192px;
    height: 192px;
        margin-top: 10px
   }
   .com-logo
   {
       border: 1px solid #0b7dc0;
    padding: 10px;
    border-radius: 10px;
    margin-right:10px;
   }
  @media (min-width: 768px) {
        .hidden-mobile {
          display: none;
        }
      }
</style>

<div class="container body">
    <!-- MultiStep Form -->

    <div  class="row right_col" role="main" >
        <div class="card-deck row">
            
             <div class="card col-md-6 col-sm-6 col-xs-12">
            
            <div class="card-body first-card-body ">
              <h5 class="card-title first-card-title">Insurance</h5>
              <p class="card-text">Combination of Risk Cover and Investment made easy. Get assured returns and secure your future</p>
              <p class="card-text">
                   
                  <a class="btn btn-primary" onclick="openInsuranceModel()">Invest Now</a>
                  <!-- href="https://www.bajajallianzlife.com/securepolicy.html" -->
              </p>
            </div>
          </div>
            <div class="card col-md-6 col-sm-6 col-xs-12">
            
            <div class="card-body second-card-body">
              <h5 class="card-title second-card-title">Mutual Fund</h5>
              <p class="card-text">Complex Investments made simple. Diversify your investments and get benefit from market returns</p>
              <p class="card-text">
                   <a class="btn btn-primary" onclick="openMFModel()">Invest Now</a>

              </p>
            </div>
          </div>
          <div class="card col-md-6 col-sm-6 col-xs-12" style="display:none">
            
            <div class="card-body third-card-body">
              <h5 class="card-title third-card-title"> Equity</h5>
              <p class="card-text">Invest directly into businesses and leverage your investments</p>
              <p class="card-text">
                   <!-- <a class="btn btn-primary" href="#" target="_blank">Click Here</a> -->
                  Coming Soon...
              </p>
            </div>
          </div>
          </div>
          <Br><br>  
          <div class="card-deck  row">
          
          <div class="card col-md-6 col-sm-6 col-xs-12">
            <div class="card-body four-card-body">
              <h5 class="card-title four-card-title">Fixed Deposit </h5>
              <p class="card-text">Traditional investment which promises Fixed returns</p>
              <p class="card-text">
                 
                  <a class="btn btn-primary" href="<?php echo site_url('client/PurchaseMutualFund/FixDeposit')?>" >Invest Now</a>
              </p>
            </div>
          </div>
          <div class="card col-md-6 col-sm-6 col-xs-12">
            
            <div class="card-body five-card-body">
              <h5 class="card-title five-card-title">Commodity (Gold/Silver)</h5>
              <p class="card-text">Invest in Metal (Gold/Silver) through Digital route. Assured of purity and store metal in Digital locker</p>
              <p class="card-text">
                   <a class="btn btn-primary" href="https://isolutions.augmont.com/home" target="_blank">Invest Now</a>
              </p>
            </div>
          </div>
          
          
        </div>
        
    </div>
  </div>  
<div id="Insurancepopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Insurance Company</h4>
            </div>
            <div class="modal-body">
                Please click on Company:
                <div class="row">
                    <div class="col-md-4">
                    <a href="https://www.bajajallianzlife.com/securepolicy.html" target="_blank">
                        <img class="com-logo" src="https://3tense.com/uploads/bajaj.png" height="90px"/>
                    </a>
                    </div>
                    <div class="col-md-3">
                    <a href="https://onlinepayments.hdfclife.com/HDFCLife/quick_pay.html" target="_blank">
                        <img class="com-logo" src="https://3tense.com/uploads/hdfc.png" height="90px" />
                    </a>
                    </div>
                    <div class="col-md-4">
                     <a href="https://ebiz.licindia.in/D2CPM/#DirectPay" target="_blank">
                        <img class="com-logo" src="https://3tense.com/uploads/lic.png" height="90px" />
                    </a>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
           
                <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
         
<div id="MFTypePopup" class="modal fade" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Purchase Mutual Fund</h4>
            </div>
            <div class="modal-body">
                Select Type:&nbsp;&nbsp;
               <input type="radio" id="html" name="r_FD_Type" value="Lumpsum">
                <label for="html">Lumpsum</label>&nbsp;
                <input type="radio" id="css" name="r_FD_Type" value="sip">
                <label for="css">SIP</label>
                <input type="hidden" id='hndId'>
                <input type="hidden" id='hndRateType'>
            </div>
            <div class="modal-footer">
               
                <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="button" onclick='GoMF()' style="float:right" class="btn btn-primary" >Go</button>
            </div>
        </div>
    </div>
</div>       

<script type="text/javascript">
    function openInsuranceModel()
    {
        $('#Insurancepopup').modal('show');
    }
    function openMFModel()
    {
        $('#MFTypePopup').modal('show');
    }
    function GoMF()
    {
        if($('input[name="r_FD_Type"]:checked').val()=='Lumpsum')
        {
            var win = window.location.href ='<?php echo site_url('client/PurchaseMutualFund')?>';
            $('#Insurancepopup').modal('hide');
        }
        else if($('input[name="r_FD_Type"]:checked').val()=='sip')
        {
            var win = window.location.href ='<?php echo site_url('client/PurchaseMutualFund/sip')?>';
            $('#Insurancepopup').modal('hide');
        }
        else
        {
         alert('Please select any one type.');
        }
        
    }
</script>
