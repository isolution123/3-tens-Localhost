<?php
// error_reporting(0);
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MF_scripts_manual extends CI_Controller {

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
            $data1=array();
            $todayCondition = array('scheme_date' =>$current_date);
            //$todayDataExists = $this->mf->check_mf_scheme_hist_today($todayCondition);
            //print_r($todayDataExists);
            $todayDataExists=false;
            $counter=0;
            if(!$todayDataExists)
            {
                $sch_id=0;
                foreach($split as $string)
                {
                    $row = explode(';', $string);
                    if(is_numeric($row['0']))
                    {
                        if (strlen($row['1'])>5)//push ISIN 1 > 5
                        {
                            $scheme_info[]='';
                            $scheme_info = $this->mf->check_scheme_isin_exists(trim($row['1']));
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
                                            $sch_id=$scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                                array_push($data1,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date,"isin"=>$scheme_info[0]->isin));
                                                
                                            }
                                            else {
                                                echo $row['4']."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        $sch_id=$scheme_idd;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=> $current_date));
                                        array_push($data1,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=> $current_date,"isin"=>$scheme_info[0]->isin));
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
                                            $sch_id=$scheme_id;
                                            if(is_numeric($row['4']))
                                            {
                                                array_push($data,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                                array_push($data1,$rs[]=array("scheme_id"=>$scheme_id,"current_nav"=>$row['4'],"scheme_date"=>$current_date,"isin"=>$scheme_info[0]->isin));
                                            }
                                            else {
                                                echo $row['4']."  NAV not in numeric";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($scheme_info[0]->isin))
                                    {
                                        $scheme_idd = $scheme_info[0]->scheme_id;
                                        $sch_id=$scheme_idd;
                                        array_push($data,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=>$current_date));
                                        array_push($data1,$rs[]=array("scheme_id"=>$scheme_idd,"current_nav"=>$row['4'],"scheme_date"=>$current_date,"isin"=>$scheme_info[0]->isin));
                                    }
                                }
                            }
                        }
                    }
                  //  print_r($data);
                    if(count($data))
                    {
                        $rows=$this->mf->insert_nav($data);        
                        $data=array();
                        
                    //    die();
                    }
                    $data=array();
                    //$counter++;
                    //if($counter==500)
                    //{
                        //$rows=$this->mf->insert_nav($data);        
                     //   $data=array();
                        //$counter=0;
                    //}
                }
                
                 /* echo "<br><h2>Inserted NAV..</h2><br>";
                foreach($data as $rs)
                {
                   echo "Scheme ID=".$rs['scheme_id']."  "."Current Nav".$rs ['current_nav']."</center><br>";
                }*/
				 
                echo "<br><h2>Not found ISIN..</h2><br>";

                echo "<pre>";
                    print_r($data1);
                echo "<pre>";
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
            }
    }
    
    //Dpk 24-01-2017 final complet
    function download_nav_copy($path,$name)
    {
        $url = $path.$name;
        $local = "downloads/NAV/";
        $file = $local.$name;
        //create folder if not exists

        if (!is_dir($local))
        {
            mkdir($local, 0777, true);
        }
        $src = fopen($url, 'r');
        $dest = fopen($file, 'w');
        if($src && $dest)
        {

            stream_copy_to_stream($src, $dest);
            echo 'File: '.$name.' - '.'Download';
            return $file;
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
       // if(!empty($data) && is_array($data)) {
           // foreach($data as $row)
            //{
                $result_lu = $this->mf->valuation_live_units('0004', 0);
                if($result_lu === true) {
                    echo 'MF Live Units calculation completed for brokerID:0004 - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Live Units calculation - ID:0004 '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_lu);
                }
                
                //$where_condition=array("brokerID"=>$row->id);
                $where_condition=array("brokerID"=>0004, "partialCalc"=>0);
                $result_divp = $this->mf->mf_calculate_divp($where_condition);
                if($result_divp === true) {
                    echo 'MF Dividend Payout completed for brokerID:0004 - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Dividend Payout - ID:0004 - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_divp);
                }
    
                $result_divr = $this->mf->mf_calculate_divr($where_condition);
                if($result_divr === true) {
                    echo 'MF Dividend Reinvested completed for brokerID:0004 - '.date('Y-m-d H:i:s').'<br/>';
                } else {
                    echo 'Error in MF Dividend Reinvested - ID:0004 - '.date('Y-m-d H:i:s').'<br/>';
                    var_dump($result_divr);
                }
    
                echo "<br/>";
          
                //break;
           // }
            
            echo "<br/>";
            
            $result_c_nav = $this->mf->mf_update_c_nav(array("brokerID"=>"0004"));
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
            
        /*} else {
            echo "No Brokers Selected/Found! Please run again.<br/>";
            var_dump($data);
        }*/
    }
    
    
    



    ///////////////////////////////////////
    //MF Script END
    //////////////////////////////////////


    function auto_mf_valuation_historical()
    {
        echo 'MF Update Current Nav completed for all brokers. - '.date('Y-m-d H:i:s').'<br/>';
     
            $brokerID='0004';
            $reportDate='2018/07/14';
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
