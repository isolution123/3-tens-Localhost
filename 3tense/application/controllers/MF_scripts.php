<?php
// error_reporting(0);
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MF_scripts extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        //load models
        $this->load->helper('form');
        $this->load->model('Users_model', 'user');
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Families_model', 'family');
    }

    ///////////////////////////////////////
    //MF Script Start
    //////////////////////////////////////
    function mf_valuation()
    {
      // echo "Demo";
      $data['brokers']=$this->mf->broker_list();
      //print_r($data);
      // echo $data[0]->id;
      $this->load->view('mf_valuation',$data);
    }
    function valuation()
    {
      if(isset($_POST['submit']))
      {

        if(isset($_POST['check_list']))
        {
            foreach($_POST['check_list'] as $id)
            {
              // echo $id."</br>";
            //   foreach($brokers as $broker)
            //   {
                    /*$result_lu = $this->mf->valuation_live_units($id, 0);
                    if($result_lu === true) {
                        echo 'MF Live Units calculation completed for brokerID:'.$id.'<br/>';
                    } else {
                        echo 'Error in MF Live Units calculation - ID:'.$id.'<br/>';
                        var_dump($result_lu);
                    }*/
                    
                    /*$result_temp = $this->mf->valuation_temp_table($id, 0);
                    if($result_temp === true) {
                        echo 'MF temp table created for brokerID:'.$id.'<br/>';
                    } else {
                        echo 'Error in MF temp table creation - ID:'.$id.'<br/>';
                        var_dump($result_temp);
                    }*/

                      //$where_condition=array("brokerID"=>$id);
                      $where_condition=array("brokerID"=>$id, "partialCalc"=>0);
                      $result_divp = $this->mf->mf_calculate_divp($where_condition);
                      if($result_divp === true) {
                          echo 'MF Dividend Payout completed for brokerID:'.$id.'<br/>';
                      } else {
                          echo 'Error in broker MF Dividend Payout - ID:'.$id.'<br/>';
                          var_dump($result_divp);
                      }

                      $result_divr = $this->mf->mf_calculate_divr($where_condition);
                      if($result_divr === true) {
                          echo 'MF Dividend Reinvested completed for brokerID:'.$id.'<br/>';
                      } else {
                          echo 'Error in broker MF Dividend Reinvested - ID:'.$id.'<br/>';
                          var_dump($result_divr);
                      }

                      echo "<br/><br/>";
              }
              
              echo "<br/><br/>";
              
              $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>"all"));
              if($result_c_nav === true) {
                  echo 'MF Update Current Nav completed for all brokers.<br/>';
              } else {
                  echo 'Error in MF Update Current NAV. Running again...<br/>';
                  //var_dump($result_c_nav);
                  $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>"all"));
                  if($result_c_nav === true) {
                      echo 'MF Update Current Nav completed for all brokers.<br/>';
                  } else {
                      echo 'Error in MF Update Current NAV.<br/>';
                      var_dump($result_c_nav);
                  }
              }
              
              echo "<br/><br/>";

        }
        else {
          echo "No Broker Selected";
        }
      }
    }

    function run_broker_valuation($brokerID)
    {
    
         
        /*//$brokerID = $this->input->post('brokerID');
        //first get all families of broker
        $families = $this->family->get_families_broker_dropdown($brokerID);

        $where = array(); $valuation = true;
        //delete existing valuation of broker
        $deleted = $this->mf->delete_mf_valuation(array("broker_id",$brokerID));
        if($deleted === true) {
            //now we'll call mf_valuation_family procedure for each family
            foreach($families as $family) {
                //var_dump($family);
                $where = array(
                    'familyID'=> $family->family_id,
                    'brokerID'=> $brokerID
                );
                $result = $this->mf->mf_valuation($where);
                if(is_array($result) && isset($result['code'])) {
                    $response = array(
                        'error' => 'Error while calculating valuation!',
                        'code' => $deleted['code'],
                        'text' => 'An error occurred in calculating a family valuation.  Family:'.$family->name.'  Error Message:'.$result['message']
                    );
                    $valuation = false;
                    break;
                }
            }
        } else {
            $response = array(
                'error' => 'Error while deleting old valuation!',
                'code' => $deleted['code'],
                'text' => 'An error occurred in deleting valuation.  Error Message:'.$deleted['message']
            );
        }*/

        //run valuation for broker
        $valuation = true;
        $result = $this->mf->mf_valuation(array("brokerID"=>$brokerID, "transID"=>0));
        if(is_array($result) && isset($result['code'])) {
            $response = array(
                'error' => 'Error while calculating valuation!',
                'code' => $result['code'],
                'text' => 'An error occurred in calculating a valuation.  Broker:'.$brokerID.'  Error Message:'.$result['message']
            );
            $valuation = false;
        }

        //check if valuation is complete
        if($valuation) {
            $response = true;
        }

        return $response;
    }
    
    
    function open()
    {
        $nav_url = 'https://www.amfiindia.com/spages/NAVAll.txt?t=';
        $current_time = date('H');
        
        if(intval($current_time) >= 21) {
          $nav_date = date('dmYHis'); //if NAV is updated at night
          $current_date=date('Y-m-d');

        } else {
          $nav_date = date('dmYHis',strtotime("-1 days")); //if NAV is updated in the morning
          $current_date=date('Y-m-d',strtotime("-1 days"));
        }
        
        $nav_cnt = 0; $filepath = null;
        while(empty($filepath) && $nav_cnt < 3) {
            $current_time = date('H');
            if(intval($current_time) >= 21) {
              $nav_date = date('dmYHis'); //if NAV is updated at night
              $current_date=date('Y-m-d');
    
            } else {
              $nav_date = date('dmYHis',strtotime("-1 days")); //if NAV is updated in the morning
              $current_date=date('Y-m-d',strtotime("-1 days"));
            }
            
     //       $filepath = $this->download_nav_copy($nav_url,$nav_date);
            
             $url = $nav_url.$nav_date;
        $local = "downloads/NAV/";
        $file = $local.$nav_date;
        //create folder if not exists

        if (!is_dir($local))
        {
            mkdir($local, 0777, true);
        }
       
        $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  

        $src = file_get_contents($url, false, stream_context_create($arrContextOptions));
       
        if($src )
        {

            file_put_contents($file,$src);
            echo 'File: '.$nav_date.' - '.'Download';
            $filepath= $file;
        }
        else
         {
            echo 'Some issue with fopen() <br/>';
            return false;
        }
               
         //   if(empty($filepath)) { sleep(60); }
        }
        echo $filepath;
        //$filepath='downloads/NAV//02062020090226';
        $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  

        $response = file_get_contents($filepath, false, stream_context_create($arrContextOptions));

        echo $response;
    }
    

    //Dpk 24-01-2017 final complet
    function update_nav()
    {
        $nav_url = 'https://www.amfiindia.com/spages/NAVAll.txt?t=';
        $current_time = date('H');
        //$current_date=date('Y-m-d');
        //$current_date=date('Y-m-d',strtotime("+1 days"));
        if(intval($current_time) >= 21) {
          $nav_date = date('dmYHis'); //if NAV is updated at night
          $current_date=date('Y-m-d');

        } else {
          $nav_date = date('dmYHis',strtotime("-1 days")); //if NAV is updated in the morning
          $current_date=date('Y-m-d',strtotime("-1 days"));
        }
        
        $nav_cnt = 0; $filepath = null;
        while(empty($filepath) && $nav_cnt < 3) {
            $current_time = date('H');
            if(intval($current_time) >= 21) {
              $nav_date = date('dmYHis'); //if NAV is updated at night
              $current_date=date('Y-m-d');
    
            } else {
              $nav_date = date('dmYHis',strtotime("-1 days")); //if NAV is updated in the morning
              $current_date=date('Y-m-d',strtotime("-1 days"));
            }
            $filepath = $this->download_nav_copy($nav_url,$nav_date);
            $nav_cnt++;
            if(empty($filepath)) { sleep(60); }
        }
        
        if(!$filepath)
        {
            echo $nav_date.' - Could not download Net Asset Value copy. <br/><br/>';
        }
        else
        {
         
                $file =$filepath;
                $fopen = fopen($file, 'r');
                $fread = fread($fopen,filesize($file));
                
                
               fclose($fopen);
                $remove = "\n";
                $split = explode($remove, $fread);
                $not_found_isin=array();
                  $data=array();
                $todayCondition = array('scheme_date' =>$current_date);
                $todayDataExists = $this->mf->check_mf_scheme_hist_today($todayCondition);
                //  print_r($todayDataExists);
                if(!$todayDataExists)
                {
                  
               foreach($split as $string)
                {
                    $row = explode(';', $string);
                    if(is_numeric($row['0']))
                    {
                        if (strlen($row['1'])>5)//push ISIN 1 > 5
                        {
                            $scheme_info[]='';
                            $scheme_info = $this->mf->check_scheme_isin_exists($row['1']);
                            if(empty($scheme_info))
                            {
                                array_push($not_found_isin,$row['1']);
                            }
                            else
                            {
                                if(count($scheme_info) > 1)
                                {
                                    for ($j=0; $j <sizeof($scheme_info) ; $j++)
                                    {
                                        if(!empty($scheme_info[$j]->isin))
                                        {
                                            $scheme_id='';
                                            $scheme_id = $scheme_info[$j]->scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                            }
                                            else {
                                                echo $row['4'].$scheme_info[$j]->isin."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=> $current_date));
                                    }
                                }
                            }
                        }
                        if(strlen($row['2'])>5) //push ISIN > 5
                        {
                            $scheme_info[]='';
                            $scheme_info = $this->mf->check_scheme_isin_exists($row['2']);
                            if(empty($scheme_info))
                            {
                                array_push($not_found_isin,$row['2']);
                            }
                            else
                            {
                                if(count($scheme_info) > 1)
                                {
                                    for ($j=0; $j <sizeof($scheme_info) ; $j++)
                                    {
                                        if(!empty($scheme_info[$j]->isin))
                                        {
                                            $scheme_id='';
                                            $scheme_id = $scheme_info[$j]->scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                            }
                                            else {
                                                echo $row['4'].$scheme_info[$j]->isin."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                    }
                                }
                            }
                        }
                    }
                    if(count($data))
                    {
                       /* $rows=[];
                         foreach($data as $d){  //@ pallavi:2017-06-28
                            $rows[]=$this->mf->insert_nav_1($d);  
                          }*/
                        $rows=$this->mf->insert_nav($data);        
                        $data=array();
//                        die();
                    }
                    
                }
               	 
                 echo "<br><h2>Not found ISIN..</h2><br>";

                 echo "<pre>";
                 print_r($not_found_isin);
                 echo "<pre>";
                 /*foreach($not_found_isin as $row)
                 {
                   echo "<center>Scheme Id=".$row['isin']."</center><br>";
                 }*/


            }
              else {
                echo "<h3><i>Today's NAV Already Imported!</i></h3>";
                //break;
              }
              
              // update currrent nav in new table
              $this->mf->update_curr_nav();      
              
        }
    }
   
    
    //Dpk 24-01-2017 final complet
    function update_nav_old()
    {
        $file = 'https://3tense.com/downloads/NAV/28022023235002.txt';
        $local = "downloads/NAV/";
        $file = $local.'28022023235002.txt';
                $current_date='2021-08-03';
                $fopen = fopen($file, 'r');
                $fread = fread($fopen,filesize($file));
                
                fclose($fopen);
                $remove = "\n";
                $split = explode($remove, $fread);
                $not_found_isin=array();
                  $data=array();
                  echo $current_date;
              $todayCondition = array('scheme_date' =>$current_date);
              
              $todayDataExists = $this->mf->check_mf_scheme_hist_today($todayCondition);
              
              if(!$todayDataExists)
              {
               foreach($split as $string)
                {
                    $row = explode(';', $string);
                    if(is_numeric($row['0']))
                    {
                        if (strlen($row['1'])>5)//push ISIN 1 > 5
                        {
                            $scheme_info[]='';
                            $scheme_info = $this->mf->check_scheme_isin_exists($row['1']);
                            if(empty($scheme_info))
                            {
                                array_push($not_found_isin,$row['1']);
                            }
                            else
                            {
                                if(count($scheme_info) > 1)
                                {
                                    for ($j=0; $j <sizeof($scheme_info) ; $j++)
                                    {
                                        if(!empty($scheme_info[$j]->isin))
                                        {
                                            $scheme_id='';
                                            $scheme_id = $scheme_info[$j]->scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                            }
                                            else {
                                                echo $row['4'].$scheme_info[$j]->isin."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=> $current_date));
                                    }
                                }
                            }
                        }
                        if(strlen($row['2'])>5) //push ISIN > 5
                        {
                            $scheme_info[]='';
                            $scheme_info = $this->mf->check_scheme_isin_exists($row['2']);
                            if(empty($scheme_info))
                            {
                                array_push($not_found_isin,$row['2']);
                            }
                            else
                            {
                                if(count($scheme_info) > 1)
                                {
                                    for ($j=0; $j <sizeof($scheme_info) ; $j++)
                                    {
                                        if(!empty($scheme_info[$j]->isin))
                                        {
                                            $scheme_id='';
                                            $scheme_id = $scheme_info[$j]->scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                            }
                                            else {
                                                echo $row['4'].$scheme_info[$j]->isin."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                    }
                                }
                            }
                        }
                    }
                    if(count($data))
                    {
                        $rows=[];
                         /*foreach($data as $d){  //@ pallavi:2017-06-28
                            $rows[]=$this->mf->insert_nav_1($d);  
                          }*/
                        $rows=$this->mf->insert_nav($data);        
                        $data=array();

                    }
                    
                }
                
                 echo "<br><h2>Not found ISIN..</h2><br>";

                 echo "<pre>";
                 print_r($not_found_isin);
                 echo "<pre>";
            }
              else {
                echo "<h3><i>Today's NAV Already Imported!</i></h3>";
              }
    }
    
    
    //Dpk 24-01-2017 final complet
    function download_nav_copy($path,$name)
    {
        $url = $path.$name;
        $local = "downloads/NAV/";
        $file = $local.$name;

        if (!is_dir($local))
        {
            mkdir($local, 0777, true);
        }
        
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  
        
        $src = file_get_contents($url, false, stream_context_create($arrContextOptions));
       
        if($src)
        {
            file_put_contents($file,$src);
            echo 'File: '.$name.' - '.'Download';
            $filepath= $file;
            return $filepath;
        }
        else
         {
            echo 'Some issue with fopen() <br/>';
            return false;
        }
    }
    
    
    /* Salmaan - auto-running of valuation for all brokers - 2017-04-25 */
    function auto_mf_valuation()
    {
        echo "<strong>Running automatic MF Valuation for all brokers...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
        $data = $this->mf->broker_list('status=1');
        //echo "<pre>"; var_dump($data); die();
        if(!empty($data) && is_array($data)) {
            foreach($data as $row)
            {
                $result_lu = $this->mf->valuation_live_units($row->id, 0);
                if($result_lu === true) {
                    echo 'MF Live Units calculation completed for brokerID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Live Units calculation - ID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_lu);
                }
                
                //$where_condition=array("brokerID"=>$row->id);
                $where_condition=array("brokerID"=>$row->id, "partialCalc"=>0);
                $result_divp = $this->mf->mf_calculate_divp($where_condition);
                if($result_divp === true) {
                    echo 'MF Dividend Payout completed for brokerID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Dividend Payout - ID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_divp);
                }
    
                $result_divr = $this->mf->mf_calculate_divr($where_condition);
                if($result_divr === true) {
                    echo 'MF Dividend Reinvested completed for brokerID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Dividend Reinvested - ID:'.$row->id.', Name:'.$row->name.' - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_divr);
                }
    
                echo "<br/>";
          
                //break;
            }
            
            echo "<br/>";
            
            $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>"all"));
            if($result_c_nav === true) {
                echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
            } else {
                echo 'Error in MF Update Current NAV. Running again...<br/>';
                //var_dump($result_c_nav);
                $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>"all"));
                if($result_c_nav === true) {
                    echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Update Current NAV. - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_c_nav);
                }
            }
      
            echo "<br/>";
            
        } else {
            echo "No Brokers Selected/Found! Please run again.<br/>";
            var_dump($data);
        }
    }
    
    /* Salmaan - auto-running of valuation for all brokers - 2017-04-25 */
    function auto_mf_valuation_0004()
    {
        echo "<strong>Running automatic MF Valuation for all brokers...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
        
        
        
                $broker_id='0180';
                $result_lu = $this->mf->valuation_live_units($broker_id, 0);
                
                
                //$where_condition=array("brokerID"=>$row->id);
                $where_condition=array("brokerID"=>$broker_id, "partialCalc"=>0);
                $result_divp = $this->mf->mf_calculate_divp($where_condition);
                
    
                $result_divr = $this->mf->mf_calculate_divr($where_condition);
                
    
                
          
                //break;

            echo "<br/>//test";
            
            $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>$broker_id));
            if($result_c_nav === true) {
                echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
            } else {
                echo 'Error in MF Update Current NAV. Running again...<br/>';
                //var_dump($result_c_nav);
              //  $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>$broker_id));
                if($result_c_nav === true) {
                    echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Update Current NAV. - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_c_nav);
                }
            }
      
            echo "<br/>";
            
        
    }
    
     function auto_mf_valuation_0004_1()
    {
        echo "<strong>Running automatic MF Valuation for all brokers...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
            $broker_id='0180';
                $result_lu = $this->mf->valuation_live_units($broker_id, 0);
                  echo "<strong>valuation_live_units...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
    }
    function auto_mf_valuation_0004_2()
    {
      
            $broker_id='0180';
                $where_condition=array("brokerID"=>$broker_id, "partialCalc"=>0);
                $result_divp = $this->mf->mf_calculate_divp($where_condition);
                echo "<strong>mf_calculate_divp...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
    }
    function auto_mf_valuation_0004_3()
    {
        echo "<strong>Running automatic MF Valuation for all brokers...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
            $broker_id='0180';
                $where_condition=array("brokerID"=>$broker_id, "partialCalc"=>0);
                $result_divr = $this->mf->mf_calculate_divr($where_condition);
                echo "<strong>mf_calculate_divr...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
    }
    function auto_mf_valuation_0004_4()
    {
        echo "<strong>Running automatic MF Valuation for all brokers...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
            $broker_id='0180';
                $where_condition=array("brokerID"=>$broker_id, "partialCalc"=>0);
              
                
            $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>$broker_id));
            if($result_c_nav === true) {
                echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
            } else {
                echo 'Error in MF Update Current NAV. Running again...<br/>';
                //var_dump($result_c_nav);
                $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>$broker_id));
                if($result_c_nav === true) {
                    echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Update Current NAV. - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_c_nav);
                }
            }
            echo "<strong>mf_update_c_nav...</strong> - ".date('Y-m-d H:i:s')."<br/><br/>";
    }
    
    
    



    ///////////////////////////////////////
    //MF Script END
    //////////////////////////////////////


    function auto_mf_valuation_historical()
    {
        echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
     
            $brokerID='0004';
            $reportDate='2019/09/21';
            $familyID='F201600040076';
        $result_lu = $this->mf->valuation_live_units_historical($brokerID, $reportDate,$familyID); 
        if($result_lu === true) {
            echo 'MF Live Units calculation completed<br/>';
        } else {
            echo 'Error in MF Live Units calculation<br/>';
            var_dump($result_lu);
        }
        
        //$where_condition=array("brokerID"=>$row->id);
        $where_condition=array("brokerID"=>$brokerID, "reportDate"=>$reportDate);
        $result_divp = $this->mf->mf_calculate_divp_historical($where_condition);
        if($result_divp === true) {
            echo 'MF Dividend Payout completed <br/>';
        } else {
            echo 'Error in MF Dividend Payout <br/>';
            var_dump($result_divp);
        }

        $result_divr = $this->mf->mf_calculate_divr_historical($where_condition);
        if($result_divr === true) {
            echo 'MF Dividend Reinvested completed <br/>';
        } else {
            echo 'Error in MF Dividend Reinvested <br/>';
            var_dump($result_divr);
        }
            
        $result_c_nav = $this->mf->mf_update_c_nav_historical($where_condition);
        if($result_c_nav === true) {
            echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
        } else {
            
            //var_dump($result_c_nav);
            $result_c_nav = $this->mf->mf_update_c_nav_historical($where_condition);
            if($result_c_nav === true) {
                echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
            } else {
                echo 'Error in MF Update Current NAV. - '.date('Y-m-d H:i:s').'<br/>';
                var_dump($result_c_nav);
            }
        }
      
    }
    

}
