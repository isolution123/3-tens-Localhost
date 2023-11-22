<div id="page-content">

    <div id='wrap'>

        <div id="page-heading">

            <ol class="breadcrumb">

                <li><a href="index.php">Dashboard</a></li>

                <li>Mutual Funds</li>

                <li class="active">Update NAV</li>

            </ol>

            <h1>Update NAV</h1>

        </div>

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-indigo">

                        <div class="panel-heading">

                            <h4>Update NAV</h4>



                        </div>

                        <div class="panel-body collapse in">

                            <?php
                            //echo '<pre>';print_r($this->session->userdata);die;
                            //if($this->session->userdata['broker_id'] == '0180')
                            if($this->session->userdata('user_id') =='0201' ||$this->session->userdata('user_id') =='0202' ||$this->session->userdata['user_id'] =='0199' || $this->session->userdata['user_id'] =='0006' ||($this->session->userdata['user_id']==$this->session->userdata['broker_id'] && ($this->session->userdata['broker_id'] == '0004'|| $this->session->userdata['broker_id'] == '0204')))
                        
                            {
                                echo '<a class="btn btn-success" id="btnUpdateNAV" onclick="updatenav()" ><i class="fa fa-plus"></i> Update NAV</a><br/><br/>';
                            }
                            ?>
                            
                        

                        </div>

                    </div>

                </div>

            </div>

            <?php //include 'occupation_add.php'; ?>

        </div>    <!-- container -->

    </div> <!--wrap -->

</div> <!-- page-content -->

<script type="text/javascript">

    var table;

var iFlag=0;

    $(document).ready(function(){

    });    
        
   function updatenav(){
       if(iFlag==0)
       {
           iFlag=1;
       $('#btnUpdateNAV').css('disabled','disabled');
       
       alert('valuation run Started wait for 10 to 15 min');
       
        $.ajax({
            url: '<?php echo site_url('broker/Mutual_funds/auto_mf_valuation_0004'); ?>',
            type: 'post',
            dataType: 'json',
            success:function(data)
            {
                alert('valuation run completed');
                iFlag=0;
                $('#btnUpdateNAV').css('disabled','');
                console.log(data);
            },
            error:function(jqXHR, textStatus, errorThrown)
            {
                alert('valuation run completed');
                iFlag=0;
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
       }
       

    }


</script>