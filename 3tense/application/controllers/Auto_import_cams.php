<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auto_import_cams extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Tradings_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        $this->load->model('Common_model');
        $this->load->model('Doc_model');
        $this->load->model('Reminders_model', 'rem');
        $this->load->model('Mutual_funds_model', 'mf');
         $this->load->model('Sip_import', 'sp');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model', 'bank');
        $this->load->model('Common_model', 'common');
        include('dbf_class.php');
        require ('application/third_party/PHPMailer/class.phpmailer.php');// for sending error reporting mail while import
    }

    function index()
    {
              /* List of all clients */

        //data to pass to header view like page title, css, js
        $header['title']='Client Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',

        );
        //load views

  //  if(!empty($err_data)){
  //    echo"in final error";
  //    echo"<pre>";print_r($err_data);
   //
  //  }else{
  //    echo"Folio Uploaded successfully...!";
  //  }

        set_time_limit(3000);
$search_text="";
        /* connect to gmail with your credentials */
          // $hostname = '{cs9.webhostbox.net:143/notls/norsh/novalidate-cert}INBOX';  /*working hostname string for itechnextgen.com domain*/
        $hostname = "{mail.3tense.com:993/ssl/novalidate-cert}";
        // $username = 'mfautoupload@itechnextgen.com'; # e.g somebody@3Tense.com  //
          $username = 'mfautoupload@3tense.com';
         $password = 'auto@1234';
        /* try to connect */
        $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
   // var_dump($inbox);exit;
            /************Folio File Auto Import********************/

/*Array for the RTA mail subject*/
$cams_auto_import=array();

$cams_auto_import['client_folio']="WBR9. Investor Static details feed - CRMS Format, Request Id";
$cams_auto_import['mf_transact']="WBR2. Investor Transactions for a Period, Request Id";
$cams_auto_import['sip_import']="WBR49. SIP/STP procured for the period, Request Id";

$broker_pwd=array();
foreach($cams_auto_import as $mainkey => $value){
//  echo $cams_auto_import[$key];
//scan mail box only for today and yesterday date for saving Time
$date  = new DateTime();
$interval = new DateInterval('P1D'); //one day intervel for getting yesterday
$date->sub($interval);
$mailDate=$date->format("j F Y");  // this will get yesterdaydate
//echo"Yesterday=".$mailDate."<br>";
/*for CAMS client Folio file auto import*/
         if($cams_auto_import[$mainkey]=="WBR9. Investor Static details feed - CRMS Format, Request Id"){   //cams if of folio master files;
      $emails = imap_search($inbox, 'SUBJECT "WBR9. Investor Static details feed - CRMS Format, Request Id" SINCE "'.$mailDate.'" UNSEEN');
          // echo "<pre>";print_r($emails);exit;
        //  var_dump($emails);
          
          /* if any emails found, iterate through each email */
          if($emails) {
          //  echo " Mails for today";
              $output = '';
              $count = 1;

              /* put the newest emails on top */
              rsort($emails);
              /* for every email... */

              $lcnt=0;
              foreach($emails as $email_number)
              {
                   unset($abscence_id,$read_arr,$to_part,$brokerName,$PW,$pwd,$reqId);
                       echo"\n"."*************************************Folio***************************************************"."\n";
                echo $email_number;
                  /* get information specific to this email */
                  $overview = imap_fetch_overview($inbox,$email_number,0);
                 echo"<pre>";print_r($overview);//exit;
              //  echo $overview[0]->date."<br>";
                  $message_date=new DateTime($overview[0]->date);//Put date result at DateTime constructor

                  $comp_today_date= $message_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format
                      //echo  $comp_today_date;
                    //  echo"<br><br>";
                 // echo  date("Y-m-d");exit;
                  //check for only yesterday's date  mail and get array of them only.
                  $date  = new DateTime();
                  $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
                $date->sub($interval);
                    $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
//if($comp_today_date === $yesterday){
                    echo"date matched for email_number".$email_number."<br>";//exit;
                    $today_mail=$email_number;
                    $to_part=$overview[0]->to; //for mailback service mail array collection
                    $requestId=$overview[0]->subject;
                    $reqId = substr($requestId, strpos($requestId, ":") + 1); 
                    echo "request Id=".$reqId."<br>";
                 // }else{
                //    continue;
                 // }

                    if(isset($today_mail)){
                      $temp=imap_body($inbox,$today_mail);
                      $req_id=trim($this->GetBetween('Report Request No:','This request=',$temp));
                      $final_link=str_replace(array("\n","\r\n","\r"), '', str_replace('=','',$this->GetBetween('Link :','The zip file',$temp)));
                      if(strpos( $final_link,'/mailback_result/')){
                         $temp1=$final_link;
                      }else{
                        //  $problem="false";
                          $problem_message[$today_mail]="file link is not available in the mail with Request ID:".$today_mail."And date:".$comp_today_date;
                        //  echo"no link found for mail no:".$today_mail;
                          continue;
                      }
             //    echo "\n".$temp1."\n";
                    $link=@$temp1;
                    $link=trim($link);
                    $mystring = $link;
                    $findme   = '.zip';
                    $pos = strpos($mystring, $findme);
                    $result = substr($mystring, 10, strpos($mystring, $findme)); // $result = php
                    $result=str_replace('\'>http','',$result);
                    $bc_url = $result;//'http://www.bseindia.com/download/BhavCopy/Equity/';
                    //get the date for which you want bhav_copy - start with todays date
                    $bc_date = $message_date->format("dmy"); //date('dmy');
                    //run till we get the file properly
                    $success = false;
                    $bc_filename = 'CAM_Folio'.$bc_date.'_'.$lcnt.'CSV'.'ZIP';
                    $lcnt++;
                    $local = "downloads/Folio_master_list/cam_folder/"; //local folder path
                    $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
                    
                    
                     //if it returns false, try again with one day before
                    if(!$filepath) {
                         //echo $bc_date.' - Could not download folio file. <br/>';
                         $problem_message[$today_mail]=$bc_date.' - Could not download client folio master file with Mail Number:'.$today_mail;
                       $bc_date = $this->get_yesterday($bc_date);
                       continue;
                     }else{
                       //now we have to extract the zip
                      //   $unzip=true;//comment this line on server and uncomment below line on server
                           $location="downloads/Folio_master_list/cam_folder/";
                            $brokerName=$to_part;
                                 echo"mail back id=".$brokerName."<br>";
                            $PW=$this->Clients_model->get_RTA_import_password($brokerName);
                        //     echo"<pre>";print_r($PW)."<br>";
                             $pwd=$PW[0]['cams_rta_password'];

                            $broker_pwd[$brokerName]=$pwd;
                            echo "folio extract pwd=".$pwd."<br>";//exit;
                      $unzip = $this->extract_archive($filepath,$location,$pwd);
                      
                             if(!$unzip) {
                                    //  echo $bc_date.' - Could not open/extract zip file. <br/>';
                                          $problem_message[$today_mail]=$bc_date.' - Could not open/extract zip file formain number:'.$today_mail."And date:".$comp_today_date;;
                                     //$bc_date = $this->get_yesterday($bc_date);
                                     continue;
                                   }else {
                                   //scan the directory for today's cams folio master dbf file
                                        $today_cam=scandir('downloads/Folio_master_list/cam_folder/');
                                        //yesterday date search***********
                                        $today=$date->format('d-m-Y');//date('d-m-Y');
                                        $searchTemp = DateTime::createFromFormat('d-m-Y',$today);
                                       $SearchDate = $message_date->format('d-m-Y');
                                      $SearchDate=str_replace('-','',$SearchDate);
                                      //today date **********
                                       $today1=new DateTime();//date('d-m-Y');
                                       $today1= $today1->format('d-m-Y');
                                       $searchTemp1 = DateTime::createFromFormat('d-m-Y',$today1);
                                       $SearchDate1 = $searchTemp1->format('d-m-Y');
                                      $SearchDate1=str_replace('-','',$SearchDate1);
                                  //  echo $SearchDate;
                                      foreach($today_cam as $file){
                      //$file = '2013-02-fileNameMightBeAlphaOr1234567890.pdf';
                      if(!is_dir($file)){
                        if(preg_match('#^' . $SearchDate . '.*?\.dbf$#', $file)) {
                          // echo "Matched\n";
                         if (strpos($file,$reqId) !== false) { 
                             $read_arr=$file;
                          }
                             

                        }elseif(preg_match('#^' . $SearchDate . '.*?\.xlsx$#', $file)) {
                          // echo "Matched\n";
                            if (strpos($file,$reqId) !== false) { 
                             $read_arr=$file;
                          }
                          }elseif(preg_match('#^' . $SearchDate . '.*?\.xls$#', $file)) {
                          // echo "Matched\n";
                            if (strpos($file,$reqId) !== false) { 
                             $read_arr=$file;
                          }

                        }
                        // elseif(preg_match('#^' . $SearchDate1 . '.*?\.dbf$#', $file)) {
                        //   // echo "Matched\n";
                        //  if (strpos($file,$reqId) !== false) { 
                        //      $read_arr=$file;
                        //   }
                        // }elseif(preg_match('#^' . $SearchDate1 . '.*?\.xlsx$#', $file)) {
                        //   // echo "Matched\n";
                        //     if (strpos($file,$reqId) !== false) { 
                        //      $read_arr=$file;
                        //   }
                        //   }elseif(preg_match('#^' . $SearchDate1 . '.*?\.xls$#', $file)) {
                        //   // echo "Matched\n";
                        //     if (strpos($file,$reqId) !== false) { 
                        //      $read_arr=$file;
                        //   }

                        // }
                      }
                          }


                                         }
                       }

                                   @$allset[$mainkey]=array();
                                if(isset($read_arr)){
                                //  echo "file passing:";echo $read_arr."<br>";
                                //  echo"main key:".$mainkey."<br>";
                                  $successFlag=true;
                                  $passfileType='cams_excel';
                                  if(strpos(@$read_arr, 'downloads/Folio_master_list/cam_folder/')=== false){
                                     if(in_array(@$read_arr,@$allset[$mainkey])==false){
                                    $read_arr='downloads/Folio_master_list/cam_folder/'.$read_arr;
                                  }
                                  }else{
                                     if(in_array(@$read_arr,@$allset[$mainkey])==false){
                                  $read_arr=$read_arr;
                                }
                                  }
   //echo "Actual file passing:";echo $read_arr."<br>";
                                    //   if(in_array($read_arr,@$allset[$mainkey])==false){
                                    $abscence_id=$PW[0]['id'];
                                    echo"Broker_id:".$abscence_id."<br>";
                                    echo"file extracted".$read_arr."<br>";;
                                  $allset[$mainkey][$today_mail]= $this->clients_import($successFlag,$read_arr,$passfileType,$abscence_id);
                              // }
                              $this->del_uploaded_file($mainkey, $allset[$mainkey][$today_mail]);
                             }else{
                               continue;
                             }





                    }else{    //if end of today mail set
                   continue;
                 }//else of if end of $today_mail
                    unset($abscence_id,$read_arr,$to_part,$brokerName,$PW,$pwd,$reqId);
               }//end of main forach of email
             }else{
               echo "Mail box is empty today for Cams Folio";
               continue;
             }
             if(@$problem_message){
            echo"<pre>Problem Message for Cams Folio:";print_r($problem_message);echo"</pre>";
             }

}//end of cams if of folio master files;

// for mutual fund transaction  import
   if($cams_auto_import[$mainkey]=="WBR2. Investor Transactions for a Period, Request Id"){
     $temails = imap_search($inbox, 'SUBJECT "WBR2. Investor Transactions for a Period, Request Id" SINCE "'.$mailDate.'" UNSEEN');
         //echo "<pre>";print_r($temails);exit;
//$tread_arr=array();
//var_dump($temails);

         /* if any emails found, iterate through each email */
         if($temails) {
         //  echo " Mails for today";
             $output = '';
             $count = 1;

             /* put the newest emails on top */
             rsort($temails);
             /* for every email... */
              $tlcnt=0;
             foreach($temails as $temail_number)
             {
                   unset($abscence_id1,$tread_arr,$to_part_mf,$brokerName,$PW1,$pwd1,$treqId);
                    echo"\n"."********************Mutual Fund***********************************"."\n";
                 echo "mail number:".$temail_number."<br>";
                 /* get information specific to this email */
                 $toverview = imap_fetch_overview($inbox,$temail_number,0);
                  echo"<pre>";print_r($toverview);//exit;
                 $tmessage_date=new DateTime($toverview[0]->date);//Put date result at DateTime constructor
                 $tcomp_today_date= $tmessage_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format

                 //check for only yesterday's date  mail and get array of them only.
                 $date  = new DateTime();
                 $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
                 $date->sub($interval);
                   $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
                // if($tcomp_today_date === $yesterday){
                   echo"date matched for email_number".$email_number."<br>";
                   $t_today_mail=$temail_number;
                   $to_part_mf=$toverview[0]->to;
                   $trequestId=$toverview[0]->subject;
                   $treqId = substr($trequestId, strpos($trequestId, ":") + 1); 
                    echo "trequest Id=".$treqId."<br>";
                 //}else{
                 //  continue;
                // }

                 if(isset($t_today_mail)){
                     $t_temp=imap_body($inbox, $t_today_mail);
                     $req_id=trim($this->GetBetween('Report Request No:','This request was',$t_temp));
                     $tfinal_link=str_replace(array("\n","\r\n","\r"), '', str_replace('=','',$this->GetBetween('https://mailback.camsonline.com/mailback_result/','The zip file',$t_temp)));
                //   echo $tfinal_link;exit;
                   $tpart=strpos($tfinal_link,'https');// strpos("I love php, I love php too!","php");
                   $slen=strlen($tfinal_link);
                   $subpart=substr($tfinal_link,$tpart,$slen);
                     $tfinal_link1= $this->GetBetween('https://','.zip',$subpart);
                     if(strpos($tfinal_link1,'/mailback_result/')){
                           //  echo"in hell";
                              $t_temp1=$tfinal_link1;   //array of final link having zip file for mf transaction of  todays date
                             }else{
                               $tproblem_message[$t_today_mail]="file link is not available in the mail with Request ID:".$t_today_mail."And date:".$comp_today_date;
                              continue;
                           }

                           $link=@$t_temp1;
                         $link=str_replace(array("<a",">","</a>","</a><BR><BR>","</a<BR<BR"),'',$link);
                         $link=trim($link);

                         if(!strpos($tfinal_link1,'https://')){
                           $link='https://'.$link;
                         }
                         if(!strpos($tfinal_link1,'.zip')){
                           $link=$link.'.zip';
                         }
                       //echo $link;exit;
                           //set the url from where to download
                             $bc_url = $link;//'http://www.bseindia.com/download/BhavCopy/Equity/';
                             //get the date for which you want bhav_copy - start with todays date
                             $bc_date = $tmessage_date->format('dmy');//date('dmy');
                             //run till we get the file properly
                             $success = false;
                             $bc_filename = 'CAM_MF'.$bc_date.'_'.$tlcnt.'.ZIP';
                             $tlcnt++;
                             //  echo $bc_filename;
                               //now download the bhav copy from BSE website
                                $local = "downloads/MF_Transaction_list/cam_folder/"; //local folder path
                                 $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
                                 //if it returns false, try again with one day before
                                 if(!$filepath) {
                                  //   echo $bc_date.' - Could not download bhav copy. <br/>';
                                     $problem_message[$t_today_mail]=$bc_date.' - Could not download client MF file with Main number:'.$t_today_mail."And date:".$tcomp_today_date;
                                   $bc_date = $this->get_yesterday($bc_date);
                                     continue;
                                 }else{

                                   //now we have to extract the zip
                              //    $unzip=true;//comment this line on server and uncomment below line on server
                                       $location="downloads/MF_Transaction_list/cam_folder/";
                                       $brokerName=$to_part_mf;
                                        echo"Mail back id=".$brokerName."<br>";
                                       $PW1=$this->Clients_model->get_RTA_import_password($brokerName);
                                      //  echo"<pre>";print_r($PW1)."<br>";
                                        $pwd1=$PW1[0]['cams_rta_password'];
                                       $broker_pwd[$brokerName]=$pwd1;
                                       
                                       echo "MF extract pwd=".$pwd1."<br>";


                                   $unzip = $this->extract_archive($filepath,$location,$pwd1);
                                   if(!$unzip) {
                                    //   echo $bc_date.' - Could not open/extract zip file. <br/>';
                                         $problem_message[$t_today_mail]=$bc_date.' - Could not open/extract zip file for the mail number:'.$t_today_mail."And date:".$tcomp_today_date;;
                                       //$bc_date = $this->get_yesterday($bc_date);
                                       continue;
                                   } else {
                                     //scan the directory for today's cams folio master dbf file
                                     $ttoday_cam=scandir('downloads/MF_Transaction_list/cam_folder/');
                                     //yesterday date search*******
                                     $today=$date->format('d-m-Y');//date('d-m-Y');
                                       $tsearchTemp = DateTime::createFromFormat('d-m-Y',$today);
                                       $tSearchDate = $tmessage_date->format('d-m-Y');
                                     $tSearchDate=str_replace('-','',$tSearchDate);
                                     //today date search*******
                                     $today1=new DateTime();//date('d-m-Y');
                                     $today1= $today1->format('d-m-Y');
                                       $tsearchTemp1 = DateTime::createFromFormat('d-m-Y',$today1);
                                       $tSearchDate1 = $tsearchTemp1->format('d-m-Y');
                                     $tSearchDate1=str_replace('-','',$tSearchDate1);
                                     
                               //  print_r($today_cam);
                                @$allset[$mainkey]=array();
                              foreach($ttoday_cam as $file){
                           //$file = '2013-02-fileNameMightBeAlphaOr1234567890.pdf';
                           if(!is_dir($file)){
                             if(preg_match('#^' . $tSearchDate . '.*?\.dbf$#', $file)) {
                               // echo "Matched\n";
                               if(in_array(@$tread_arr,$allset[$mainkey])==false){
                                   if (strpos($file,$treqId) !== false) { 
                             $tread_arr=$file;
                          }
                            }

                             }elseif(preg_match('#^' . $tSearchDate . '.*?\.xlsx$#', $file)) {
                               // echo "Matched\n";
                               if(in_array(@$tread_arr,$allset[$mainkey])==false){
                                    if (strpos($file,$treqId) !== false) { 
                             $tread_arr=$file;
                          }
                                     }
                              }elseif(preg_match('#^' . $tSearchDate . '.*?\.xls$#', $file)) {
                               // echo "Matched\n";
                               if(in_array(@$tread_arr,$allset[$mainkey])==false){
                                         if (strpos($file,$treqId) !== false) { 
                             $tread_arr=$file;
                          }
                                       }
                             }
                        //      elseif(preg_match('#^' . $tSearchDate1 . '.*?\.dbf$#', $file)) {
                        //       // echo "Matched\n";
                        //       if(in_array(@$tread_arr,$allset[$mainkey])==false){
                        //           if (strpos($file,$treqId) !== false) { 
                        //      $tread_arr=$file;
                        //   }
                        //     }

                        //      }elseif(preg_match('#^' . $tSearchDate1 . '.*?\.xlsx$#', $file)) {
                        //       // echo "Matched\n";
                        //       if(in_array(@$tread_arr,$allset[$mainkey])==false){
                        //             if (strpos($file,$treqId) !== false) { 
                        //      $tread_arr=$file;
                        //   }
                        //              }
                        //       }elseif(preg_match('#^' . $tSearchDate1 . '.*?\.xls$#', $file)) {
                        //       // echo "Matched\n";
                        //       if(in_array(@$tread_arr,$allset[$mainkey])==false){
                        //                  if (strpos($file,$treqId) !== false) { 
                        //      $tread_arr=$file;
                        //   }
                        //               }
                        //      }
                           }

                        }//end of for $ttoday_cam

                                   }//else end of unzip

                                 }//else enf od filepath



                              //   echo"<pre>";print_r($tread_arr)."<br>";
                                                         if(isset($tread_arr)){
                                                          $successFlag=true;
                                                          $passfileType='cams_excel';
                                                          if(strpos($tread_arr, 'downloads/MF_Transaction_list/cam_folder/')=== false){
                                                          $tread_arr='downloads/MF_Transaction_list/cam_folder/'.$tread_arr;
                                                           }else{
                                                                  $tread_arr=$tread_arr;
                                                              }
                                                          // echo "auto file=".$tread_arr;
                                                           $abscence_id1=$PW1[0]['id'];
                                                           echo"mf file name:".$tread_arr."<br>";
                                                           echo"Broker id:".$abscence_id1."<br>";
                                                          try{
                                                             $allset[$mainkey][$t_today_mail]= $this->mf_import($successFlag,$tread_arr,$abscence_id1);
                                                        $this->del_uploaded_file($mainkey, $allset[$mainkey][$t_today_mail]);
                                                          } catch(Exception $e) {
                                                                     //var_dump($e);
                                                                     echo "Exception occured in file read of mail number:".$t_today_mail;
                                                                      $problem_message[$t_today_mail]="Exception occured in file read of mail number:".$t_today_mail;
                                                                     continue;
                                                                 }
                                                     
                                                   }else{
                                                     continue;
                                                   }

                 }else{    //if end of today mail set
                   continue;
                 }
           unset($abscence_id1,$tread_arr,$to_part_mf,$brokerName,$PW1,$pwd1,$treqId);
              }//end of main for loop of mf mail

            }else{
              echo "Mail box is empty today";
              continue;
            }
            if(@$tproblem_message){
            echo"<pre>Problem Message for Cams Mutual Fund:";print_r($tproblem_message);echo"</pre>";
            }


}//end of cams mutual fund import

//Cams SIP import
 if($cams_auto_import[$mainkey]=="WBR49. SIP/STP procured for the period, Request Id"){   //cams if of SIP files import;
 $semails = imap_search($inbox, 'SUBJECT "WBR49. SIP/STP procured for the period, Request Id" SINCE "'.$mailDate.'" UNSEEN');
 //echo "<pre>";print_r($semails);exit;
//var_dump($semails);

  /* if any emails found, iterate through each email */
  if($semails) {
  //  echo " Mails for today";
      $output = '';
      $count = 1;

      /* put the newest emails on top */
      rsort($semails);
      /* for every email... */
      $slcnt=0;
      foreach($semails as $semail_number)
      {
           unset($abscence_id2,$sread_arr,$to_part_sip,$brokerName,$PW2,$pwd2,$sreqId);
            echo"\n"."******************************************SIP**********************************************"."\n";
        echo "mail no:".$semail_number."<br>";
          /* get information specific to this email */
          $soverview = imap_fetch_overview($inbox,$semail_number,0);
           echo"<pre>";print_r($soverview);//exit;
          $smessage_date=new DateTime($soverview[0]->date);//Put date result at DateTime constructor
          $scomp_today_date= $smessage_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format
          //check for only yesterday's date  mail and get array of them only.
          $date  = new DateTime();
          $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
          $date->sub($interval);
            $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
        //  if($scomp_today_date === $yesterday){
            echo"date matched for email_number".$email_number."<br>";
            $s_today_mail=$semail_number;
              $to_part_sip=$soverview[0]->to;
              $srequestId=$soverview[0]->subject;
                   $sreqId = substr($srequestId, strpos($srequestId, ":") + 1); 
                    echo "srequest Id=".$sreqId."<br>";
             // unset($email_number);
          //}else{
           // continue;
          //}
          if(isset($s_today_mail)){
              $s_temp=imap_body($inbox, $s_today_mail);
              $tpart=trim($this->GetBetween(' Download Link :','The zip file is protected',$s_temp));
             $tpart=str_replace('=','',$this->GetBetween('Download Link :','The zip file is protected',$tpart));
                 $sfinal_link=$tpart;
                   if(strpos($sfinal_link,'/mailback_result/')){
                        $s_temp1=$sfinal_link;   //array of final link having zip file for mf transaction of  todays date
                         }else{
                           $sproblem_message[$s_today_mail]="file link is not available in the mail with  main number:".$s_today_mail."and date".$scomp_today_date;
                           continue;
                              }

                              $link=$s_temp1;
                              $link=str_replace(array("<a",">","<ahref3D","</a>","</a><BR><BR>","</a<BR<BR"),'',$link);
                              $link=trim($link);
                              $link = preg_replace('/\s+/', '', $link);
                             //echo strip_tags($link);
                              if(($pos = strpos($link, '.z')) !== false)
                          {
                             $new_str = substr($link, $pos + 1);
                          }
                          else
                          {
                             $new_str = get_last_word($link);
                          }
                          if(($pos = strpos($new_str, "'")) !== false)
                          {
                             $new_str1 = substr($new_str, $pos + 1);
                          }
                          else
                          {
                             $new_str1 = get_last_word($new_str);
                          }
                             //echo $new_str1;

                          $link=$new_str1;
                          //exit;
                          //echo $link;exit;

                                $bc_url = $link;//'http://www.bseindia.com/download/BhavCopy/Equity/';
                                //get the date for which you want bhav_copy - start with todays date
                                $bc_date = $smessage_date->format('dmy'); //date('dmy');
                                //run till we get the file properly
                                $success = false;
                                $bc_filename = 'CAM_SIP'.$bc_date.'_'.$slcnt.'.ZIP';
                                $slcnt++;
                                //  echo $bc_filename;
                                //    echo $bc_url;
                                  // exit;
                                    //now download the bhav copy from BSE website
                                   $local = "downloads/SIP_files_list/cam_folder/"; //local folder path
                                    $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
                                    //if it returns false, try again with one day before
                                    if(!$filepath) {
                                      //  echo $bc_date.' - Could not download bhav copy. <br/>';
                                        $sproblem_message[$s_today_mail]=$bc_date.' - Could not download client folio master SIP file with mail number:'.$s_today_mail."And date:".$comp_today_date;
                                      $bc_date = $this->get_yesterday($bc_date);
                                        continue;
                                    }else{

                                      //now we have to extract the zip
                                    // $unzip=true;//comment this line on server and uncomment below line on server
                                          $location="downloads/SIP_files_list/cam_folder/";
                                          $brokerName=$to_part_sip;
                                          echo"Mail back id=".$brokerName."<br>";
                                          $PW2=$this->Clients_model->get_RTA_import_password($brokerName);
                                          // echo"<pre>";print_r($PW2)."<br>";
                                           $pwd2=$PW2[0]['cams_rta_password'];

                                          $broker_pwd[$brokerName]=$pwd2;
                                            echo "SIP extract pwd=".$pwd2."<br>";//exit;
                                       $unzip = $this->extract_archive($filepath,$location,$pwd2);
                                        if(!$unzip) {
                                          //  echo $bc_date.' - Could not open/extract zip file. <br/>';
                                              $sproblem_message[$s_today_mail]=$bc_date.' - Could not open/extract zip file with mail no:'.$s_today_mail."And date:".$comp_today_date;
                                            //$bc_date = $this->get_yesterday($bc_date);
                                            continue;
                                        } else {
                                          //scan the directory for today's cams folio master dbf file
                                          $stoday_cam=scandir('downloads/SIP_files_list/cam_folder/');
                                          //yesterday date search *********
                                          $today=$date->format('d-m-Y');//date('d-m-Y');
                                            $psearchTemp = DateTime::createFromFormat('d-m-Y',$today);
                                            $pSearchDate = $smessage_date->format('d-m-Y');
                                          $pSearchDate=str_replace('-','',$pSearchDate);
                                          //today date search***********
                                           $today1=new DateTime();//date('d-m-Y');
                                           $today1= $today1->format('d-m-Y');
                                            $psearchTemp1 = DateTime::createFromFormat('d-m-Y',$today1);
                                            $pSearchDate1 = $psearchTemp1->format('d-m-Y');
                                          $pSearchDate1=str_replace('-','',$pSearchDate1);
                                    //  print_r($today_cam);
                                    $allset=array();
                                                foreach($stoday_cam as $file){
                                //$file = '2013-02-fileNameMightBeAlphaOr1234567890.pdf';
                                if(!is_dir($file)){
                                  if(preg_match('#^' . $pSearchDate . '.*?\.dbf$#', $file)) {
                                    if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                                          
                                           if (strpos($file,$sreqId) !== false) { 
                             $sread_arr=$file;
                          }
                                          }

                                  }elseif(preg_match('#^' . $pSearchDate . '.*?\.xlsx$#', $file)) {
                                    // echo "Matched\n";
                                    if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                                              if (strpos($file,$sreqId) !== false) { 
                             $sread_arr=$file;
                          }
                                            }
                                   }elseif(preg_match('#^' . $pSearchDate . '.*?\.xls$#', $file)) {
                                    // echo "Matched\n";
                                    if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                                              if (strpos($file,$sreqId) !== false) { 
                             $sread_arr=$file;
                          }
                                          }
                                  }
                        //           else  if(preg_match('#^' . $pSearchDate1 . '.*?\.dbf$#', $file)) {
                        //             if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                                          
                        //                   if (strpos($file,$sreqId) !== false) { 
                        //      $sread_arr=$file;
                        //   }
                        //                   }

                        //           }elseif(preg_match('#^' . $pSearchDate1 . '.*?\.xlsx$#', $file)) {
                        //             // echo "Matched\n";
                        //             if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                        //                       if (strpos($file,$sreqId) !== false) { 
                        //      $sread_arr=$file;
                        //   }
                        //                     }
                        //           }elseif(preg_match('#^' . $pSearchDate1 . '.*?\.xls$#', $file)) {
                        //             // echo "Matched\n";
                        //             if(in_array(@$sread_arr,@$allset[$mainkey])==false){
                        //                       if (strpos($file,$sreqId) !== false) { 
                        //      $sread_arr=$file;
                        //   }
                        //                   }
                        //           }
                                }
                                            }
                                          // echo"<pre>";print_r($sread_arr);exit;



                                        }//end of else of unzip
                                    }//end of else of filepath

                                      if(isset($sread_arr)){
                                    $successFlag=true;
                                    $passfileType='cams_excel';
                                    if(strpos($sread_arr, 'downloads/SIP_files_list/cam_folder/')=== false){
                                                                    $sread_arr='downloads/SIP_files_list/cam_folder/'.$sread_arr;
                                                                  }else{
                                                                    $sread_arr=$sread_arr;
                                                                  }
                                  //   echo "auto file=".$sread_arr;
                                     $abscence_id2=$PW2[0]['id'];
                                     echo"Sip File Name:".$sread_arr."<br>";
                                    echo"Broker Id:".$abscence_id2."<br>";
                                  $allset[$mainkey][$semail_number]= $this->sip_import($successFlag,$sread_arr,$abscence_id2);
                                    $this->del_uploaded_file($mainkey, $allset[$mainkey][$semail_number]);
                                }else{
                                  continue;
                                }
                                    echo '\n';


          }else{ //if end of today mail
              continue;
          }//end of else of today mail set
    unset($abscence_id2,$sread_arr,$to_part_sip,$brokerName,$PW2,$pwd2,$sreqId);

       }//end of main for loop of sip mail
    //   print_r($s_today_mail);exit;

  }else{
    echo "Mail box is empty today ";
    continue;
  }
  if(@$sproblem_message){
  echo"<pre>Problem Message for Cams SIP:";print_r($sproblem_message);echo"</pre>";
  }

}//end of cams sip import


if(@$allset){
echo"Final Deletion  file Array<pre>";print_r(@$allset);
}
//echo"end of auto import";
/*forward all error occured while importing to the  admin via mail*/
}


     /* Email all the error records in excel format  for cams ,karvy and therir folio,mf transaction and sip records to the admin */
     $rta_type="CAMS";
      //1. for folio records
      $folio_error_data = $this->Clients_model->get_all_folio_record($rta_type);
              /*Mail of karvy Folio file error records backup in excel file */
       if(!empty($folio_error_data)){
      //first clear the content of  temp file if any available previously
      //open file to write
      $fp = fopen("downloads/Folio_master_list/folio_error_backup.csv", "r+");
      // step1- clear content to 0 bits
              ftruncate($fp, 0);
              //close file
             fclose($fp);
      //step-2   define headers for the csv Families_model
      $header = array("TABLE ID","CLIENT NAME","EMAIL ID","DATE OF BIRTH","OCCUPATION","HEAD OF FAMILY",
                   "RELATION W/ HOF","TAX STATUS","SPOUSE_NAME","ANV_DATE","ANV_APP","PAN NUMBER","PASSPORT_NO","ADDRESS #1","ADDRESS #2",
                   "ADDRESS #3","CITY","STATE","PINCODE","PHONE RESIDENCE","MOBILE NUMBER","REPORT DATE","FAMILY ID","CHILDREN_NAME","REPORT ORDER","USER_ID","STATUS",
                   "CATEGORYDESC","ADD_INFO","FOLIO NUMBER","BANK NAME","BRANCH","BANKACCNO","IFSC CODE","ACCOUNT TYPE","BANK ADDRESS #1","BANK ADDRESS #2","BANK ADDRESS #3","BANK CITY","BANK PINCODE",
                   "BANK STATE","BANK COUNTRY","PROUDCT CODE","JOINT NAME 1","JOINT NAME 2","PAN2","PAN3","GUARD_PAN","TAX_STATUS","BROKER CODE","SUBBROKER","OCCU_NAME",
                   "MODEOFHOLD","NOMINEE1","RELATION","NOMINEE2","NOM2_RELAT","GUARDIANNAME","FOLIO_DATE","ERROR MESSAGE","ERROR FILE DOWNLOAD DATE","ERROR COLUMN LIST","ERROR FILE NAME","EMAIL STATUS");

      //step-3  copy the array in csv file
      $fp = fopen("downloads/Folio_master_list/folio_error_backup.csv", "w");
                         fputcsv ($fp, $header, "\t");
                        foreach($folio_error_data as $row){
                         fputcsv($fp, $row, "\t");
                          }
                          fclose($fp);
      //step-4  prepare the file for attachment and send via mail
      $attachment='downloads/Folio_master_list/folio_error_backup.csv';
      $to = "prakash@itechnextgen.com";
         $dtimeForFile=date('Y-m-d-H-i-s');
        $newname="Folio_error_backup-".$dtimeForFile.".xls";
       $eol = PHP_EOL;
       /***** Send Mail*****/
      $email_from="mfautoupload@3tense.com";
      $email_to="pallavi@itechnextgen.com";
      $subject= "CAMS FOLIO FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
      $email_message ="Hello,".$eol;
      $email_message.="Following are the details".$eol;
      $email_message.="TYPE:CAMS FOLIO File ".$eol;
      $email_message.="Original Name of File: CAMS FOLIO File=".$formailFileName.$eol;

      $email_message.="Please check the following attached file for error solving".$eol;
      $file = $attachment;
      $file_size = filesize($file);
      $handle = fopen($file, "r");
      $content = fread($handle, $file_size);
      fclose($handle);
      $content = chunk_split(base64_encode($content));
      $uid = md5(uniqid(time()));
      $name = basename($file);

      // header
      $header = "From: "."<".$email_from.">\r\n";
     $header .= "Reply-To: ".$email_from."\r\n";
      $header .= "MIME-Version: 1.0\r\n";
      $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
      $header .= 'Cc: prakash@itechnextgen.com\r\n';
   
      // message & attachment
      $nmessage = "--".$uid."\r\n";
      $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
      $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
      $nmessage .= $email_message."\r\n\r\n";
      $nmessage .= "--".$uid."\r\n";
      $nmessage .= "Content-Type: application/octet-stream; name=\"".$newname."\"\r\n";
      $nmessage .= "Content-Transfer-Encoding: base64\r\n";
      $nmessage .= "Content-Disposition: attachment; filename=\"".$newname."\"\r\n\r\n";
      $nmessage .= $content."\r\n\r\n";
      $nmessage .= "--".$uid."--";

      $flag=0;
      if (mail($email_to, $subject, $nmessage, $header)) {
      $flag=1; // Or do something here
      //if email sent then update email status of all records to 1 i.e. sent.
      $update=$this->Clients_model->update_folio_email_status($rta_type);
      } else {
      $flag=0;
      }

      echo "Mailing Flag value of folio file=".$flag;
      }
      /*end of mail*/

      //2. for MF transaction records.
      $mf_error_data=$this->mf->get_all_mf_error_record($rta_type);

            /*Mail of karvy Folio file error records backup in excel file */

             if(!empty($mf_error_data)){
          //first clear the content of  temp file if any available previously
          //open file to write
          $fp = fopen("downloads/MF_Transaction_list/MF_error_backup.csv", "r+");
          // step1- clear content to 0 bits
                     ftruncate($fp, 0);
                     //close file
                    fclose($fp);
         //step-2   define headers for the csv Families_model
          $header = array("Table transaction_id","CLIENT ID","FAMILY ID","TRANSACTION DATE","SCHEME NAME","TRN TYPE","TRANSACTION TYPE","FOLIO NO",
                          "TRXN_DATE","UNIT","NAV","AMOUNT","ADJUSTMENT_FLAG","ADJUSTMENT","ADJUSTMENT REF NO",
                          "DPO PER UNIT","Bank Id","Bank Name","Branch","Account Number","Cheque Number","SR NO","TEMP_TD_TRTYPE","TRXN MODE",
                          "REF NO","REJ REF NO","AMC NAME","ARN","SUB_ARN","PAN NO","PRODCODE","BROKER_ID","ERROR MESSAGE","USER_ID",
                          "ERROR FILE DOWNLOAD DATE","ERROR COLUMN LIST","ERROR FILE NAME","RTA TYPE","Email Sent Status");

          //step-3  copy the array in csv file
          $fp = fopen("downloads/MF_Transaction_list/MF_error_backup.csv", "w");
                                fputcsv ($fp, $header, "\t");
                               foreach($mf_error_data as $row){
                                fputcsv($fp, $row, "\t");
                                 }
                                 fclose($fp);
          //step-4  prepare the file for attachment and send via mail
          $attachment='downloads/MF_Transaction_list/MF_error_backup.csv';
          $to = "pallavi@itechnextgen.com";
                $dtimeForFile=date('Y-m-d-H-i-s');
               $newname="MF-Transaction_error_backup-".$dtimeForFile.".xls";
              $eol = PHP_EOL;
              /***** Send Mail*****/
       $email_from="mfautoupload@3tense.com";
       $email_to="pallavi@itechnextgen.com";
       $subject= "CAMS MF FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
       $email_message ="Hello,".$eol;
       $email_message.="Following are the details".$eol;
       $email_message.="TYPE:CAMS MF File ".$eol;
       $email_message.="Original Name of File:CAMS MF File=".$newname.$eol;

       $email_message.="Please check the following attached file for error solving".$eol;
       $file = $attachment;
       $file_size = filesize($file);
       $handle = fopen($file, "r");
       $content = fread($handle, $file_size);
       fclose($handle);
       $content = chunk_split(base64_encode($content));
       $uid = md5(uniqid(time()));
       $name = basename($file);

       // header
       $header = "From: "."<".$email_from.">\r\n";
       
       $header .= "Reply-To: ".$email_from."\r\n";
       $header .= "MIME-Version: 1.0\r\n";
       $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
       $header .= 'Cc: prakash@itechnextgen.com\r\n';

       // message & attachment
       $nmessage = "--".$uid."\r\n";
       $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
       $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
       $nmessage .= $email_message."\r\n\r\n";
       $nmessage .= "--".$uid."\r\n";
       $nmessage .= "Content-Type: application/octet-stream; name=\"".$newname."\"\r\n";
       $nmessage .= "Content-Transfer-Encoding: base64\r\n";
       $nmessage .= "Content-Disposition: attachment; filename=\"".$newname."\"\r\n\r\n";
       $nmessage .= $content."\r\n\r\n";
       $nmessage .= "--".$uid."--";

       $flag=0;
       if (mail($email_to, $subject, $nmessage, $header)) {
       $flag=1; // Or do something here
       //if email sent then update email status of all records to 1 i.e. sent.
       $update=$this->mf->update_mf_email_status($rta_type);
       } else {
       $flag=0;
       }

       echo "Mailing Flag value of CAMS MF error file=".$flag;
             }
          /*end of mail*/
          //3. for SIP error records.
          $sip_error_data=$this->sp->get_all_sip_error_record($rta_type);
          /*Mail of karvy Folio file error records backup in excel file */
          if(!empty($sip_error_data)){
         //first clear the content of  temp file if any available previously
         //open file to write
         $fp = fopen("downloads/SIP_files_list/sip_error_backup.csv", "r+");
         // step1- clear content to 0 bits
                    ftruncate($fp, 0);
                    //close file
                   fclose($fp);
        //step-2   define headers for the csv Families_model
         $header = array("TABLE ID","CLIENT ID","PRODUCT","TYPE ID","FOLIO_NO","COMPANY ID","SCHEME","GOAL","AUTO_TRNO","BANK ACC NO","BANK",
         "FROM_DATE","TO_DATE","REG DATE","CEASE_DATE","FREQUENCY","AUTO_AMOUN","RATE_OF_RETURN","MAT_VALUE","BROKERID","USER_ID","NARRATION","ADDED_ON","UPDATED ON","ERROR FILE NAME"
         ,"ERROR COLUMN LIST","ERROR MESSAGE","EMAIL STATUS");
         //step-3  copy the array in csv file
         $fp = fopen("downloads/SIP_files_list/sip_error_backup.csv", "w");
                               fputcsv ($fp, $header, "\t");
                              foreach($sip_error_data as $row){
                               fputcsv($fp, $row, "\t");
                                }
                                fclose($fp);
         //step-4  prepare the file for attachment and send via mail
         $attachment='downloads/SIP_files_list/sip_error_backup.csv';
         $to = "pallavi@itechnextgen.com";
               $dtimeForFile=date('Y-m-d-H-i-s');
              $newname="SIP_error_backup-".$dtimeForFile.".xls";
             $eol = PHP_EOL;
             /***** Send Mail*****/
  $email_from="mfautoupload@3tense.com";
  $email_to="pallavi@itechnextgen.com";
  $subject= "CAMS SIP FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
  $email_message ="Hello,".$eol;
  $email_message.="Following are the details".$eol;
  $email_message.="TYPE:CAMS SIP File ".$eol;
  $email_message.="Original Name of File:CAMS SIP File=".  $for_mailSIPFileName.$eol;

  $email_message.="Please check the following attached file for error solving".$eol;
  $file = $attachment;
  $file_size = filesize($file);
  $handle = fopen($file, "r");
  $content = fread($handle, $file_size);
  fclose($handle);
  $content = chunk_split(base64_encode($content));
  $uid = md5(uniqid(time()));
  $name = basename($file);

  // header
  $header = "From: "."<".$email_from.">\r\n";
  
  $header .= "Reply-To: ".$email_from."\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
  $header .= 'Cc: prakash@itechnextgen.com\r\n';

  // message & attachment
  $nmessage = "--".$uid."\r\n";
  $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
  $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
  $nmessage .= $email_message."\r\n\r\n";
  $nmessage .= "--".$uid."\r\n";
  $nmessage .= "Content-Type: application/octet-stream; name=\"".$newname."\"\r\n";
  $nmessage .= "Content-Transfer-Encoding: base64\r\n";
  $nmessage .= "Content-Disposition: attachment; filename=\"".$newname."\"\r\n\r\n";
  $nmessage .= $content."\r\n\r\n";
  $nmessage .= "--".$uid."--";

  $flag=0;
  if (mail($email_to, $subject, $nmessage, $header)) {
  $flag=1; // Or do something here
  //if email sent then update email status of all records to 1 i.e. sent.
  $update=$this->sp->update_sip_email_status($rta_type);
  } else {
  $flag=0;
  }

  echo "Mailing Flag value of CAMS SIP error file=".$flag;
          }
         /*end of mail*/

    /*end of mail */

        /* close the connection */
        imap_close($inbox);

        exit;


        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/client/auto_import');
        $this->load->view('broker/common/footer');

    }


function get_yesterday($date)
{
    $temp = DateTime::createFromFormat('dmy',$date);
    $date = $temp->format('Y-m-d');
    $bc_date = date('dmy', strtotime($date.' - 1 days'));
    return $bc_date;
}

function download_bhav_copy($path,$name,$local)
{
   $url = $path;
    $file = $local.$name;
   // echo"in download bhav";
  //  echo "url=".$url."\n"."local file=".$file."\n";
    //create folder if not exists
    if (!is_dir($local)) {
        mkdir($local, 0777, true);
    }
    $src = fopen($url, 'r');
  //  echo $src;
    $dest = fopen($file, 'w');
  //  echo $dest;
    if($src && $dest) {
    //  echo"heaven";
        echo 'File: '.$name.' - ';
        echo stream_copy_to_stream($src, $dest) . " bytes copied. \n<br/>";
      //  echo "file after download=".$file."\n";
        return $file;
    } else {
        echo 'Some issue with fopen()'.$name.' <br/>';
        return false;
    }
    //echo $url;exit;
}

function extract_archive($file,$fromPath,$pwd)
{
//  echo"password required";exit;
//echo"in extract function";
//echo "path=".$fromPath."\n"."local file=".$file."\n"."password=".$pwd."\n";
    $zip = new ZipArchive;
  //  $res = $zip->open($file);
  //  echo $file;
    $zip_status = $zip->open($file);
    if ($zip_status === true)
   {
       if (@$zip->setPassword($pwd))   //franklin:AGBP2803//cam:cams123
       {
           if (!$zip->extractTo($fromPath)){

             echo "Extraction failed (wrong password?)";
             $zip->close();
             return false;
           }else{
             echo "<pre>";
             print_r($zip);//to get the file type
             echo "</pre>";

             echo 'File: '.$file.' \n<br/>';
             $zip->close();
             return true;
           }

      }

       $zip->close();
   }
   else
   {
       die("Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")");
         return false;
   }
}

function GetBetween($var1="",$var2="",$pool){
$temp1 = strpos($pool,$var1)+strlen($var1);
$result = substr($pool,$temp1,strlen($pool));
$dd=strpos($result,$var2);
if($dd == 0){
$dd = strlen($result);
}

return substr($result,0,$dd);
}


function getStringBetween($str,$from,$to)
{
    $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,strpos($sub,$to));
}

//Import Code
function clients_import($flag,$file,$passfileType,$abscence_id)
{
  //echo"hellllllllllllllooooooooooooooo";//exit;
echo"File for folio import:".$file."\n";
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', '2048M');
    ini_set('upload_max_filesize', '150M');
    ini_set('post_max_size', '150M');//50M
    //initailly set broker id and user id
    $brokerID=$abscence_id;  // for client bank details table
    $user_id=$abscence_id;

    $uploadedStatus = 0;
    $message = ""; $impMessage = "";$impErrorCol=""; $insertRow = true;
    $imp_data = array();
    if (isset($flag))                               //mmif
    {

        if (isset($file))
        {                                                          //mif
        //  echo $_FILES["import_clients"]["type"];exit;

            //if there was an error uploading the file
            if ($file== '')
            {
                $message = "No file selected ";
                  break;
                  return false;

            // }elseif($_POST['rta_list']==' '){
            //     $message.="  Also please select Client Data type from dropdown";
              }else{                                            //melse
                //get tmp_name of file
                $file = $file;
              //  echo $file;
                $rta_list=$passfileType;
              //  $fullpath='downloads/Folio_master_list/cam_folder/'.$file;
                $fileType_info = $this->mime_type($file);
              //  echo $fileType_info;
              //  exit;

            //  echo $_FILES["import_clients"]["type"];die();

            //temp variables to hold values  client info
            $famName = ""; $clientName = ""; $panNum = ""; $clientType = ""; $comDate = "";
            $occ = ""; $hof = ""; $rHof = ""; $report = ""; $dob = ""; $ann = "";
            $spouse = ""; $child = ""; $clientId = ""; $familyId = ""; $category = "";
            $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
            $mobile = ""; $tel = ""; $email = ""; $username = "";
            $password = ""; $pan = ""; $passport = "";
            //@pallavi :variables holds the Clients bank details
            $bankName="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";$bank_address_road="";$bank_address_area="";
            $bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$folio_number="";
              //additional details in client bank details table
              $productId="";$jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";
              $broker_code="";$sub_boroker="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";
              $guardian_name="";$folioDate="";


            $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
            $clientAlready=0;$panAlready=0;$notFolio=0;$fresh=0; //for checking client avalability and if flag set to 1 then bypass client insert repeat and based on client Id insert its folio and other details.

            //$brokerID = $this->session->userdata('broker_id');
          //  $user_id = $this->session->userdata('user_id');

            //get data from excel using range

        //  echo"<pre>";  print_r($excelData);exit;
            //stores column names
            $dataColumns = array();
            //stores row data
            $dataRows = array();
            $dataRowsBank=array();//@pallavi
            $countRow = 0; $countErrRow = 0; $countClient = 0; $countRem = 0; $countTrans = 0;

            //check first the extension whether the file is  excel or dbf
if($fileType_info=="application/excel"||$fileType_info=="application/vnd.ms-excel"||$fileType_info=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                {       //for RTA excel import
                 continue;//no import for excel

//                   //load the excel library
//                   $this->load->library('Excel');
//                   //read file from path
//                   $objPHPExcel = PHPExcel_IOFactory::load($file);
//                   //get only the Cell Collection
//                   //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

//                   $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
//                  //print_r($maxCell);
//                   $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);

//                   //check max row for client import limit
//                 //  var_dump($maxCell['row']);
//                 //  $brokerID = $this->session->userdata('user_id');
//                   //$data = $this->Clients_model->get_limit($brokerID);
//                 //  $count = $this->Clients_model->count_client($brokerID);
//                 //  $data=intval($data->client_limit);
//                 //  var_dump($data);exit;
//                 //  $count = intval($count->count);
//                 //  $remaining = $data - $count;
//                   // var_dump($remaining);
//                   // echo $maxCell['row'];
//                   // if(($maxCell['row']-1)>$remaining)
//                   // {
//                   //     $message = "Client data exceeds your client limit! Please contact support if you want to increase your limit.";
//                   // }
//                   // else{
//                 // echo"<pre>";print_r($excelData);
//                   //$tempo=$excelData;
//                   foreach($excelData as $rows)
//                   {
//                     foreach($rows as $key=>$val)
//                     {
//                       //echo $key;
//                       $temp[$val]=$key;
//                     } break;
//                   }
//           // echo"<pre>";print_r($temp);
//                   foreach($excelData as $rows)
//                   {
//                             // print_r($rows);
//                       $countCell = 0;
//                       foreach($rows as $key=>$val)
//                       {

//                           //echo  $rows[$key];exit;
//                           $cell=$rows[$key];
//                           if($countRow == 0)
//                           {

//                              $cell = str_replace(array('.'), '', $cell);
//                           //    echo $cell;
//                                 /*@pallavi code for RTA Column matching*/
//                                 if($rta_list=='cams_excel'){ //for CAMS RTA for fetching columns

//                                                                       if(strtoupper($cell) == 'INV_NAME'){
//                                                                          $vals=$temp[$cell];
//                                                                              $cell='Client Name';
//                                                                               $dataColumns[$vals] = $cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                         }elseif(strtoupper($cell) == 'PAN_NO'){
//                                                                           $vals=$temp[$cell];
//                                                                               $cell='Pan No';
//                                                                               $dataColumns[$vals] = $cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'TAX_STATUS'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Client Type';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'OCCUPATION'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Occupation';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) ==='INV_DOB'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Date Of Birth';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                           }elseif(strtoupper($cell) ==='ADDRESS1'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='House/Flat No';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) ==='ADDRESS2'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Street';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) ==='ADDRESS3'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Area';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) ==='CITY'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='City';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             // }elseif(strtoupper($cell) == 'NOM_STATE'){ //not found reffered NOM_STATE col from excel
//                                                                             //   $vals=$temp[$cell];
//                                                                             //   $cell='State';
//                                                                             //   $dataColumns[$vals]=$cell;
//                                                                             //   $countCell++;
//                                                                             //   $uploadedStatus = 2;
//                                                                             //   continue;
//                                                                           }elseif(strtoupper($cell) === 'PINCODE'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Pincode';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'MOBILE_NO'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Mobile';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'PHONE_RES'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Telephone';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'EMAIL'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Email ID';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'FOLIOCHK'){ // not found
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Folio_Number';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                           }elseif(strtoupper($cell) === 'BROKER_COD'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Broker Code';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'REP_DATE'){  //not confirmed
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Commencement Date';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_Name';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_branch';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_ifsc';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'AC_NO'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_acc_no';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_acc_type';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_building';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_road';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_area';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_city';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_pincode';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }
//                                                                             elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_state';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Bank_address_country';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'PRODUCT'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'JNT_NAME1'){
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Joint_name1';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'JNT_NAME2'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Joint_name2';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'GUARD_NAME'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Guardian Name';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'JOINT1_PAN'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Joint 1 pan';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'JOINT2_PAN'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Joint 2 pan';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Guardian pan';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Sub Broker';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'HOLDING_NA'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Mode Of Holding';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'NOM_NAME'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Nominee1';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Nominee1_Relation';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'NOM2_NAME'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Nominee2';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Nominee2_Relation';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
//                                                                               $vals=$temp[$cell];
//                                                                               $cell='Folio Date';
//                                                                               $dataColumns[$vals]=$cell;
//                                                                               $countCell++;
//                                                                               $uploadedStatus = 1;
//                                                                               continue;
//                                                                             }
//                                                                   }//End of cams file header reading
//                           }
//                           else
//                           {
//       //      echo"<pre>"; print_r($dataColumns);exit;//check-column-headings

//                             if($rta_list=='cams_excel'){  // for CAMS RTA
//                               //  unset($brokerID,$user_id);
//                               if($insertRow)
//                               {
//                                 if(!empty($findkey=array_search('Broker Code',$dataColumns)))
//                                   {
//                                     $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                         $broker_code = trim($cell);

//                                       }else{
//                                         $broker_code="";
//                                       }

//                                       $arn=$broker_code;
//                                       $user=$this->Clients_model->get_userdata($arn);
//                                     // //  echo"<pre>";print_r($user);exit;
//                                     //   if(count($user)>0){
//                                     //     if($user[0]['status']==1 && $user[0]['user_type']=='broker'){
//                                     //       $brokerID=$user[0]['id'];  // for client bank details table
//                                     //       $user_id=$user[0]['id'];  //for clients table
//                                     //     }
//                                     //
//                                     //   }
//                                     // $brokerID=$abscence_id;  // for client bank details table
//                                     // $user_id=$abscence_id;
//                                   }
//                                   //$brokerID=$broker_code;
//                                 if(!empty($findkey=array_search('Family Name',$dataColumns)))
//                                   {
//                                     $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $famName = trim($cell);
//                                   //echo $famName;exit;
//                                           //checks if family exists in Families table
//                                           $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
//                                           if(count($f_info) == 0)
//                                           {
//                                               //$insertRow = false;
//                                               //$impMessage = $famName." Family doesn't exist";
//                                               //family doesn't exist, so add a new family
//                                               $data = array(
//                                                   'name' => $famName,
//                                                   'status' => 1,
//                                                   'broker_id' => $brokerID,
//                                                   'user_id' => $user_id
//                                               );
//                                               $status = $this->Families_model->add($data);
//                                               if(isset($status['code'])) {
//                                                   $insertRow = false;
//                                                   $impMessage = "Could not add new Family for this client";
//                                                   $impErrorCol='family_id';
//                                               } else {
//                                                   $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
//                                                   if(count($f_info) == 0)
//                                                   {
//                                                       $insertRow = false;
//                                                       $impMessage = "Could not find Family for this client";
//                                                       $impErrorCol='family_id';
//                                                   }
//                                                   else
//                                                   {
//                                                       $familyId = $f_info[0]->family_id;
//                                                   }
//                                               }
//                                           }
//                                           else
//                                           {
//                                               $familyId = $f_info[0]->family_id;
//                                           }
//                                       }
//                                       // else
//                                       // {
//                                       //     $insertRow = false;
//                                       //     $impMessage = "Family Name cannot be empty";
//                                       // }
//                                   }else{ // If family name is not found in excel then drop the client in default family

//                                         $famName ="Default family"; //default family for non family client
//                                         $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
//                                         $familyId = $f_info[0]->family_id;
//                                   }


//                                 if(!empty($findkey=array_search('Client Name',$dataColumns)))
//                                   {
//                                     $cell=$rows[$findkey];

//                                       if($cell || $cell != '')
//                                       {
//                                           $clientName = trim($cell);
//                                         //echo $clientName; exit;
//                                           $whereClient = array(
//                                               'c.name'=>$clientName,
//                                               'fam.name'=>$famName,
//                                               'fam.broker_id'=>$brokerID
//                                           );
//                                           //checks if client exists in Clients table
//                                           $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
//                                           if(count($c_info) == 0)
//                                           {
//                                               $clientAlready=0;
//                                               $clientId_obj = $this->Clients_model->get_new_client_id();
//                                               $clientId = $clientId_obj->client_id;
//                                           }
//                                           else
//                                           {
//                                             $clientAlready=1;
//                                               // $insertRow = false;
//                                               // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
//                                           }
//                                       }
//                                       else
//                                       {
//                                           $insertRow = false;
//                                           $impMessage = "Client Name cannot be empty";
//                                           $impErrorCol='Client Name';
//                                       }
//                                   }else{
//                                     $insertRow = false;
//                                     $impMessage = "Client Name cannot be empty";
//                                       $impErrorCol='Client Name';

//                                   }


//                                 if(!empty($findkey=array_search('Pan No',$dataColumns)))
//                                   {
//                                     $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $panNum = trim($cell);
//                                       //  echo $panNum;exit;
//                                           $wherePan = array(
//                                               'c.pan_no'=>$panNum,
//                                               'f.broker_id'=>$brokerID
//                                           );
//                                           //checks if policy exists in policy details table
//                                           $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
//                                           if(count($p_info) == 0)
//                                           {
//                                               //proceed normally
//                                               $panAlready=0;
//                                           }
//                                           else
//                                           {
//                                             $panAlready=1;
//                                               // $insertRow = false;
//                                               // $impMessage = "Pan No. ".$panNum." already exists for another client";

//                                           }
//                                       }
//                                       // else
//                                       // {
//                                       //
//                                       //    $clientId_obj = $this->Clients_model->get_new_client_id();
//                                       //    $dummypan = $clientId_obj->client_id;
//                                       //    //C20160001
//                                       //
//                                       //   $panNum =str_replace('C','P',$dummypan);
//                                       // //  echo "dummy pan".$panNum;exit;
//                                       // }
//                                       $pan_no=$panNum; //for client Bank  details
//                                   }else{
//                                     $panNum=$pan_no="";

//                                   }

//                               if(!empty($findkey=array_search('Client Type',$dataColumns)))
//                                 {
//                                   $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $clientType = trim($cell);
//                                           //  echo $clientType;
//                                           $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
//                                           $type_details = $this->Clients_model->get_client_types($whereCType);
//                                           if(count($type_details) == 0)
//                                           {
//                                               //$clientTypeId=40; //for others type of client Type
//                                               $defaultClientType='Resident Individual';
//                                                 $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
//                                             $ctype= $this->Clients_model->get_client_types($whereCType);
//                                             $clientTypeId =  $ctype[0]->client_type_id;
//                                               // $impMessage = 'Client Type '.$clientType." doesn't exist";
//                                               // $insertRow = false;
//                                           }
//                                           else
//                                           {
//                                               $clientTypeId = $type_details[0]->client_type_id;
//                                           }
//                                       }
//                                       else
//                                       {
//                                         //  $clientTypeId=40; //for others type of client Type
//                                           $defaultClientType='Resident Individual';
//                                             $whereCType = 'client_type_name like "'.$defaultClientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
//                                         $ctype= $this->Clients_model->get_client_types($whereCType);
//                                         $clientTypeId =  $ctype[0]->client_type_id;
//                                           // $insertRow = false;
//                                           // $impMessage = "Client Type cannot be empty";
//                                       }
//                                         $tax_status=$clientType; //for client bank details
//                                         //  echo   $clientTypeId;exit;
//                                   }else{
//                                     // $clientTypeId=40; //for  file not having client type col.
//                                     //     $tax_status=""; //for client bank details
//                                     //       $clientType=40;

//                                           $defaultClientType='Resident Individual';
//                                             $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
//                                         $ctype= $this->Clients_model->get_client_types($whereCType);
//                                         $clientTypeId =  $ctype[0]->client_type_id;

//                                       //  echo   $clientTypeId;exit;
//                                   }

//                                  if(!empty($findkey=array_search('Occupation',$dataColumns)))
//                                   {
//                                      $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $occ = trim($cell);
//                                         //  echo $occ;exit;
//                                           $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
//                                           $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
//                                           if(count($occ_details) == 0)
//                                           {
//                                               $occId=null; //for others type of occupation
//                                               // $impMessage = 'Occupation '.$occ." doesn't exist";
//                                               // $insertRow = false;
//                                           }
//                                           else
//                                           {
//                                               $occId = $occ_details[0]->occupation_id;
//                                           }
//                                       }
//                                       else
//                                       {
//                                         $occId=null;
//                                           // $insertRow = false;
//                                           // $impMessage = "Occupation cannot be empty";
//                                       }
//                                       $occ_name=$occ; //for bank details
//                                   }

//                             if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
//                               {
//                                 $cell=$rows[$findkey];
//                                   if($cell || $cell != '')
//                                       {
//                                           $hof = trim($cell);

//                                           if(strtoupper($hof) == "YES") {
//                                               $hof = 1;
//                                           } else {
//                                               $hof = 0;
//                                           }
//                                       }
//                                       else
//                                       {
//                                           $hof = 0;
//                                       }
//                                       //  echo $hof;exit;
//                                   }

//                                  if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
//                                   {
//                                      $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $rHof = trim($cell);
//                                           //echo $rHof;
//                                       }
//                                       else
//                                       {
//                                           $rHof = "";
//                                       }

//                                   }


//                                   if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                         //echo $cell;
//                                       $cell=trim(str_replace('/','-', $cell));
//                                   //  echo $cell; //exit; //date checked
//                                      if($cell || $cell != '')
//                                      {
//                                       $date = DateTime::createFromFormat('m-d-Y', $cell);
//                                          //var_dump($cell);exit;
//                                         // $date->format('Y-m-d');
//                                          if(is_object($date)){
//                                           $dob=$date->format('Y-m-d');
//                                          }else{
//                                           $date = new DateTime($cell);
//                                             if(is_object($date)){
//                                               $dob=$date->format('Y-m-d');

//                                             }else{

//                                                     $insertRow = false;
//                                                     $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
//                                                       $impErrorCol='Date Of Birth';
//                                                 }

//                                             }

//                                          } else
//                                           {
//                                               $dob = null;
//                                               $dob_app = 0;
//                                           }
//                                           // echo $dob;//exit;
//                                      }else
//                                      {
//                                          $dob = null;
//                                          $dob_app = 0;
//                                      }

//                                   if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $flat = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $flat = "";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('Street',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $street = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $street = "";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('Area',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $area = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $area = "";
//                                       }

//                                   }
//                                   if(!empty($findkey=array_search('City',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $city = trim($cell);
//                                       }
//                                       else
//                                       {
//                                         $city="";
//                                           // $insertRow = false;
//                                           // $impMessage = "City cannot be empty";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('State',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $state = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $state="";
//                                           // $insertRow = false;
//                                           // $impMessage = "State cannot be empty";
//                                       }
//                                   }

//                                   if(!empty($findkey=array_search('Pincode',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $pin = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $pin = "";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('Mobile',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $mobile = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $mobile = "";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('Telephone',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $tel = trim($cell);
//                                       }
//                                       else
//                                       {
//                                           $tel = "";
//                                       }

//                                   }

//                                 if(!empty($findkey=array_search('Email ID',$dataColumns)))
//                                   {
//                                     $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $email = trim($cell);
//                                       }
//                                       else
//                                       {
//                                         $email="";
//                                           // $insertRow = false;
//                                           // $impMessage = "Email ID cannot be empty";
//                                       }

//                                   }

//                                   if(!empty($findkey=array_search('Username',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell  || $cell != '')
//                                       {
//                                           $username = trim($cell);
//                                           //check duplicate username
//                                           //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
//                                           $duplicateConditionUsername = 'c.username = "'.$username.'"';
//                                           $join = array(
//                                               'table' => 'families f',
//                                               'on' => 'c.family_id = f.family_id',
//                                               'type' => 'inner'
//                                           );
//                                           $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

//                                           if($isUsernameDuplicate) {
//                                             //  $insertRow = false; $impMessage = "Username already exists for another client";
//                                           $username=date("Ymd").time().$username.rand(99,99999);
//                                           }
//                                       }
//                                       // else
//                                       // {
//                                       //     $insertRow = false;
//                                       //     $impMessage = "Username cannot be empty";
//                                       // }
//                                   }else{ //if username is not in excel sheet create default username with client name and random number
//                                     $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

//                                   }


//                                   if(!empty($findkey=array_search('Passport No',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != ''){
//                                           $passport = trim($cell);
//                                         }else{
//                                           $passport = "";
//                                         }
//                                       //$broker_code=$passport; //for bank details
//                                   }

//                                   if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
//                                      {
//                                       $cell=$rows[$findkey];
//                                          $cell=trim(str_replace('/','-', $cell));
//                                       if($cell || $cell != '')
//                                       {
//                                         $date = DateTime::createFromFormat('m-d-Y', $cell);
//                                           //var_dump($cell);exit;
//                                           // $date->format('Y-m-d');
//                                           if(is_object($date)){
//                                              $comDate=$date->format('Y-m-d');
//                                           }else{
//                                              $date = new DateTime($cell);
//                                               if(is_object($date)){
//                                                 $comDate=$date->format('Y-m-d');

//                                               }else{

//                                                       $insertRow = false;
//                                                       $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
//                                                       $impErrorCol='Commencement Date';
//                                                   }

//                                               }

//                                           } else
//                                             {
//                                                 $comDate = null;
//                                                 $dob_app = 0;
//                                             }
//                                              // echo $dob;//exit;
//                                       }else
//                                       {
//                                           $comDate = null;
//                                           $dob_app = 0;
//                                       }


//                                   if(($findkey=array_search('Folio_Number',$dataColumns))==0)
//                                     {
//                                       $cell=$rows[$findkey];
//                                       //echo $cell;
//                                         if($cell || $cell != '')
//                                         {
//                                             $folio_number = stripcslashes(trim($cell));
//                                             $folio_number = str_replace("'",'',$folio_number);

//                                         }

//                                   }else{

//                                       //echo "inhellllll";
//                                       $cell=$rows[$findkey];
//                                       $folio_number = stripcslashes(trim($cell));
//                                       $folio_number = str_replace("'",'',$folio_number);

//                                     }
//                                   if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                         if($cell || $cell != '')
//                                         {
//                                             $bankName = trim($cell);

//                                         }
//                                     }
//                                     if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
//                                       {
//                                         $cell=$rows[$findkey];
//                                           if($cell || $cell != '')
//                                           {
//                                               $bankBranch = trim($cell);

//                                           }
//                                       }
//                                       if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
//                                         {
//                                           $cell=$rows[$findkey];
//                                             if($cell || $cell != '')
//                                             {
//                                                 $bank_account_types = trim($cell);

//                                             }
//                                         }
//                                         if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
//                                           {
//                                             $cell=$rows[$findkey];
//                                               if($cell || $cell != '')
//                                               {
//                                                   $bankAccNo = trim($cell);

//                                               }
//                                           }
//                                           if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
//                                             {
//                                               $cell=$rows[$findkey];
//                                                 if($cell || $cell != '')
//                                                 {
//                                                     $bank_address_building = trim($cell);

//                                                 }
//                                             }
//                                             if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
//                                               {
//                                                 $cell=$rows[$findkey];
//                                                   if($cell || $cell != '')
//                                                   {
//                                                       $bank_address_road = trim($cell);

//                                                   }
//                                               }
//                                               if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
//                                                 {
//                                                   $cell=$rows[$findkey];
//                                                     if($cell || $cell != '')
//                                                     {
//                                                         $bank_address_area = trim($cell);

//                                                     }
//                                                 }
//                                                 if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
//                                                   {
//                                                     $cell=$rows[$findkey];
//                                                       if($cell || $cell != '')
//                                                       {
//                                                           $bank_address_city = trim($cell);

//                                                       }
//                                                   }
//                                                   if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
//                                                     {
//                                                       $cell=$rows[$findkey];
//                                                         if($cell || $cell != '')
//                                                         {
//                                                             $bank_pincode = trim($cell);

//                                                         }
//                                                     }
//                                                     if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
//                                                       {
//                                                         $cell=$rows[$findkey];
//                                                           if($cell || $cell != '')
//                                                           {
//                                                               $bank_state = trim($cell);

//                                                           }
//                                                       }
//                                                       if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
//                                                         {
//                                                           $cell=$rows[$findkey];
//                                                             if($cell || $cell != '')
//                                                             {
//                                                                 $bank_country = trim($cell);

//                                                             }
//                                                         }
//                                                       //for other details of bank
//                                                       if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
//                                                         {
//                                                           // $cell=$rows[$findkey];
//                                                           //   if($cell || $cell != '')
//                                                           //   {
//                                                           //     $productId = trim($cell);
//                                                           //
//                                                           //   }else{
//                                                           //     $productId="";
//                                                           //   }
//                                                             $cell=$rows[$findkey];
//                                                               if($cell || $cell != '')
//                                                               {
//                                                                 $productId = trim($cell);
//                                                                       $data=array();
//                                                                       $data['broker_id']=$brokerID;
//                                                                       if($productId){
//                                                                       $data['productId']=$productId;
//                                                                       }

//                                                                      if($folio_number){
//                                                                         $data['folio_number']=$folio_number;
//                                                                      }
//                                                                      if($clientName){
//                                                                             $data['clientName']=$clientName;
//                                                                      }
//                                                                      if($pan_no){
//                                                                       $data['pan_no']=$pan_no;
//                                                                      }

//                                                                       //check product scheme  already available
//                                                                      $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
//                                                               //   print_r($scheme_info);exit;
//                                                                      if(count($scheme_info) == 0)
//                                                                      {
//                                                                          //proceed normally
//                                                                         // $productId=$productId;
//                                                                         $notFolio=0;// client name,folio,scheme,pan combination is not available

//                                                                      }
//                                                                      else
//                                                                      {
//                                                                         $notFolio=1;
//                                                                         //  $insertRow = false;
//                                                                         //  $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

//                                                                      }

//                                                               }

//                                                          }

//                                                         if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
//                                                           {
//                                                             $cell=$rows[$findkey];
//                                                               if($cell || $cell != '')
//                                                               {
//                                                                 $jointName1 = trim($cell);

//                                                               }else{
//                                                                 $jointName1="";
//                                                               }
//                                                           }
//                                                           if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
//                                                             {
//                                                               $cell=$rows[$findkey];
//                                                                 if($cell || $cell != '')
//                                                                 {
//                                                                   $jointName2 = trim($cell);

//                                                                 }else{
//                                                                   $jointName2="";
//                                                                 }
//                                                             }
//                                                             if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
//                                                               {
//                                                                 $cell=$rows[$findkey];
//                                                                   if($cell || $cell != '')
//                                                                   {
//                                                                     $joint1_pan = trim($cell);

//                                                                   }else{
//                                                                     $joint1_pan="";
//                                                                   }
//                                                               }
//                                                               if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
//                                                                 {
//                                                                   $cell=$rows[$findkey];
//                                                                     if($cell || $cell != '')
//                                                                     {
//                                                                       $joint2_pan = trim($cell);

//                                                                     }else{
//                                                                       $joint2_pan="";
//                                                                     }
//                                                                 }
//                                                                 if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
//                                                                   {
//                                                                     $cell=$rows[$findkey];
//                                                                       if($cell || $cell != '')
//                                                                       {
//                                                                         $guard_pan = trim($cell);

//                                                                       }else{
//                                                                         $guard_pan="";
//                                                                       }
//                                                                   }

//                                                                   if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
//                                                                     {
//                                                                       $cell=$rows[$findkey];
//                                                                         if($cell || $cell != '')
//                                                                         {
//                                                                           $sub_boroker = trim($cell);

//                                                                         }else{
//                                                                           $sub_boroker="";
//                                                                         }
//                                                                     }
//                                                                     if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
//                                                                       {
//                                                                         $cell=$rows[$findkey];
//                                                                           if($cell || $cell != '')
//                                                                           {
//                                                                             $mode_holding = trim($cell);

//                                                                           }else{
//                                                                             $mode_holding="";
//                                                                           }
//                                                                       }
//                                                                       if(!empty($findkey=array_search('Nominee1',$dataColumns)))
//                                                                         {
//                                                                           $cell=$rows[$findkey];
//                                                                             if($cell || $cell != '')
//                                                                             {
//                                                                               $nominee_name1 = trim($cell);

//                                                                             }else{
//                                                                               $nominee_name1="";
//                                                                             }
//                                                                         }
//                                                                         if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
//                                                                           {
//                                                                             $cell=$rows[$findkey];
//                                                                               if($cell || $cell != '')
//                                                                               {
//                                                                                 $nom1_relation = trim($cell);

//                                                                               }else{
//                                                                                 $nom1_relation ="";
//                                                                               }
//                                                                           }
//                                                                           if(!empty($findkey=array_search('Nominee2',$dataColumns)))
//                                                                             {
//                                                                               $cell=$rows[$findkey];
//                                                                                 if($cell || $cell != '')
//                                                                                 {
//                                                                                   $nominee_name2 = trim($cell);

//                                                                                 }else{
//                                                                                   $nominee_name2="";
//                                                                                 }
//                                                                             }
//                                                                             if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
//                                                                               {
//                                                                                 $cell=$rows[$findkey];
//                                                                                   if($cell || $cell != '')
//                                                                                   {
//                                                                                     $nom2_relation = trim($cell);

//                                                                                   }else{
//                                                                                     $nom2_relation ="";
//                                                                                   }
//                                                                               }
//                                                                               if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
//                                                                                 {
//                                                                                   $cell=$rows[$findkey];
//                                                                                     if($cell || $cell != '')
//                                                                                     {
//                                                                                       $guardian_name= trim($cell);

//                                                                                     }else{
//                                                                                       $guardian_name="";
//                                                                                     }
//                                                                                 }
//                                                                                 if(!empty($findkey=array_search('Folio Date',$dataColumns)))
//                                                                                   {
//                                                                                     $cell=$rows[$findkey];
//                                                                                     $cell=trim(str_replace('/','-', $cell));
//                                                                                     if($cell || $cell != '')
//                                                                                     {
//                                                                                      $date = DateTime::createFromFormat('m-d-Y', $cell);
//                                                                                         //var_dump($cell);exit;
//                                                                                       // $date->format('Y-m-d');
//                                                                                         if(is_object($date)){
//                                                                                           $folioDate=$date->format('Y-m-d');
//                                                                                         }else{
//                                                                                           $date = new DateTime($cell);
//                                                                                           if(is_object($date)){
//                                                                                              $folioDate=$date->format('Y-m-d');

//                                                                                           }else{

//                                                                                                   // $insertRow = false;
//                                                                                                   // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
//                                                                                               }

//                                                                                           }

//                                                                                         } else
//                                                                                          {
//                                                                                              $folioDate = null;
//                                                                                              $dob_app = 0;
//                                                                                          }
//                                                                                           // echo $dob;//exit;
//                                                                                     }else
//                                                                                     {
//                                                                                         $folioDate = null;
//                                                                                         $dob_app = 0;
//                                                                                     }


//                               } else {


//                                   if(!empty($findkey=array_search('Client Name',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $clientName = trim($cell);
//                                       }
//                                       // else
//                                       // {
//                                       //     $insertRow = false;
//                                       //     $impMessage = "Client Name cannot be empty";
//                                       // }
//                                   }

//                                   if(!empty($findkey=array_search('Pan No',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $panNum = trim($cell);
//                                       }
//                                       // else
//                                       // {
//                                       //     $insertRow = false;
//                                       //     $impMessage = "Pan Number cannot be empty";
//                                       // }
//                                   }
//                                   if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
//                                     {
//                                       $cell=$rows[$findkey];
//                                       if($cell || $cell != '')
//                                       {
//                                           $folio_number = trim($cell);
//                                       }
//                                       // else
//                                       // {
//                                       //     $insertRow = false;
//                                       //     $impMessage = "Folio Number cannot be empty";
//                                       // }
//                                   }


//                               }
//                               $countCell++;
//                             }

//                           }

//                       }
//                       if($countRow != 0)
//                       {
//                           if(!$insertRow)
//                           {
//                               // $imp_data[$countErrRow][1] = $famName;
//                               // $imp_data[$countErrRow][2] = $clientName;
//                               // $imp_data[$countErrRow][3] = $panNum;
//                               // $imp_data[$countErrRow][4] = $impMessage;
//                               $imp_data[$countErrRow]['name'] = $clientName;
//                               $imp_data[$countErrRow]['email_id'] = $email;
//                               $imp_data[$countErrRow]['dob'] = $dob;
//                               $imp_data[$countErrRow]['occupation_id'] = $occId;
//                               $imp_data[$countErrRow]['head_of_family'] = $hof;
//                                 $imp_data[$countErrRow]['relation_HOF'] = $rHof;
//                                  $imp_data[$countErrRow]['client_type'] = $clientTypeId;
//                                   $imp_data[$countErrRow]['spouse_name'] = $spouse;
//                                   $imp_data[$countErrRow]['anv_date'] = $ann;
//                                     $imp_data[$countErrRow]['pan_no'] = $panNum;
//                                      $imp_data[$countErrRow]['passport_no'] = $passport;
//                                       $imp_data[$countErrRow]['add_flat'] = $flat;
//                                       $imp_data[$countErrRow]['add_street'] = $street;
//                                         $imp_data[$countErrRow]['add_area'] = $area;
//                                         $imp_data[$countErrRow]['add_city'] = $city;
//                                         $imp_data[$countErrRow]['add_state'] = $state;
//                                         $imp_data[$countErrRow]['add_pin'] = $pin;
//                                         $imp_data[$countErrRow]['telephone'] = $tel;
//                                         $imp_data[$countErrRow]['mobile'] = $mobile;
//                                         $imp_data[$countErrRow]['date_of_comm'] = $comDate;
//                                         $imp_data[$countErrRow]['family_id'] = $familyId;
//                                       $imp_data[$countErrRow]['children_name'] = $child;
//                                         $imp_data[$countErrRow]['report_order'] = $report;
//                                         $imp_data[$countErrRow]['user_id'] = $user_id;
//                                         $imp_data[$countErrRow]['status'] = 1;
//                                         $imp_data[$countErrRow]['client_category'] = $category;
//                                         $imp_data[$countErrRow]['bank_name'] = $bankName;
//                                         $imp_data[$countErrRow]['bank_branch'] = $bankBranch;
//                                         $imp_data[$countErrRow]['bank_acc_no'] = $bankAccNo;
//                                         $imp_data[$countErrRow]['bank_ifsc'] = $bankIFSC;
//                                         $imp_data[$countErrRow]['bank_account_types'] = $bank_account_types;
//                                         $imp_data[$countErrRow]['bank_address_building'] = $bank_address_building;
//                                         $imp_data[$countErrRow]['bank_address_area'] = $bank_address_area;
//                                           $imp_data[$countErrRow]['bank_address_road'] = $bank_address_road;
//                                         $imp_data[$countErrRow]['bank_address_city'] = $bank_address_city;
//                                         $imp_data[$countErrRow]['bank_pincode'] = $bank_pincode;
//                                         $imp_data[$countErrRow]['bank_state'] = $bank_state;
//                                         $imp_data[$countErrRow]['bank_country'] = $bank_country;
//                                         $imp_data[$countErrRow]['productId'] = $productId;
//                                         $imp_data[$countErrRow]['jointName1'] = $jointName1;
//                                         $imp_data[$countErrRow]['jointName2'] = $jointName2;
//                                         $imp_data[$countErrRow]['joint1_pan'] = $joint1_pan;
//                                         $imp_data[$countErrRow]['joint2_pan'] = $joint2_pan;
//                                          $imp_data[$countErrRow]['guard_pan'] = $guard_pan;
//                                           $imp_data[$countErrRow]['tax_status'] = $tax_status;
//                                           $imp_data[$countErrRow]['broker_code'] = $broker_code;
//                                             $imp_data[$countErrRow]['sub_boroker'] = $sub_boroker;
//                                              $imp_data[$countErrRow]['occ_name'] = $occ_name;
//                                               $imp_data[$countErrRow]['mode_holding'] = $mode_holding;
//                                               $imp_data[$countErrRow]['nominee_name1'] = $nominee_name1;
//                                                 $imp_data[$countErrRow]['nom1_relation'] = $nom1_relation;
//                                                  $imp_data[$countErrRow]['nominee_name2'] = $nominee_name2;
//                                                   $imp_data[$countErrRow]['nom2_relation'] = $nom2_relation;
//                                                   $imp_data[$countErrRow]['guardian_name'] = $guardian_name;
//                                                     $imp_data[$countErrRow]['folioDate'] =$folioDate;
//                                                     $imp_data[$countErrRow]['error_msg'] = $impMessage;
//                                                     $imp_data[$countErrRow]['error_col_list'] = $impErrorCol;//implode(",",$mfErrorCol);
//                                                     $imp_data[$countErrRow]['file_name'] = $file;//implode(",",$mfErrorCol);
//                                                     $imp_data[$countErrRow]['rta_type'] ='CAMS';
//                                                       $imp_data[$countErrRow]['file_download_date'] = date('Y-m-d');


//                               $countErrRow++;
//                               $insertRow = true;
//                               $uploadedStatus = 2;
//                               continue;
//                           }

//                           $dataRows[$countClient] = array(
//                               'name' => $clientName, 'family_id' => $familyId,
//                               'email_id' => $email, 'dob' => $dob, 'dob_app' => $dob_app,
//                               //'password' => sha1($password),
//                               'occupation_id' => $occId, 'head_of_family' => $hof, 'relation_HOF' => $rHof,
//                               'client_type' => $clientTypeId, 'spouse_name' => $spouse,
//                               'anv_date' => $ann, 'anv_app' => $anv_app, 'pan_no' => $panNum,
//                               'passport_no' => $passport, 'add_flat' => $flat, 'add_street' => $street,
//                               'add_area' => $area, 'add_city' => $city, 'add_state' => $state,
//                               'add_pin' => $pin, 'telephone' => $tel, 'mobile' => $mobile,
//                               'date_of_comm' => $comDate, 'children_name' => $child, 'report_order' => $report,
//                               'user_id' => $user_id, 'status' => 1, 'username' => $username,
//                               'client_category' => $category
//                           );
//                           ////for bank details


//     // echo"<pre>";print_r($dataRows);exit;
//   //    echo"<pre>";print_r($dataRowsBank); //echo $clientAlready;
//       unset($inserted,$getdata);
//                           if(empty($clientAlready) && empty($panAlready)){  //both not available
//                         //  echo"fresh client".$clientName;
//                         //     $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
//                         //      $fresh++;

//                         if(empty($notFolio)){  // if client name not available but pan not available also folio,and product id not available
//                         //  echo"fresh client".$clientName.$productId.$folio_number.$pan_no;//exit;
//                           $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
//                           $fresh++;
//                         }else{
//                         //  echo"in  folio"."but no client no pan  but folio and product id avalilable";
//                           $getdata['folio_number']=$folio_number;
//                           $getdata['productId']=$productId;
//                           $getdata['broker_id']=$brokerID;
//                           $getClientId=$this->Clients_model->getClientId($getdata);
//                           $inserted=$getClientId;
//                         }


//                           }elseif(empty($clientAlready) && !empty($panAlready)){ // if pan available and client name not available
//                           //  echo"no client but pan available".$clientName;
//                           //  $getdata['cname']=$clientName;
//                             $getdata['pan']=$panNum;
//                             $getdata['productId']=$productId;
//                             $getdata['broker_id']=$brokerID;
//                             $getClientId=$this->Clients_model->getClientId($getdata);
//                 //       print_r($getClientId);exit;
//                           $inserted=$getClientId;
//                           }elseif(!empty($clientAlready) && empty($panAlready)){   // if client name available and pan not available

//                             if(empty($notFolio)){  /// if client name available but pan not available also folio,and product id also not available
//                             //  echo"in not folio".$clientName.$productId.$folio_number.$pan_no;//exit;
//                               $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
//                               $fresh++;
//                             }else{                             // if client name available but pan not available also folio,and product same
//                             //  echo"in  folio".$clientName;
//                               $getdata['cname']=$clientName;
//                               $getdata['folio_number']=$folio_number;
//                               $getdata['productId']=$productId;
//                               $getdata['broker_id']=$brokerID;
//                               $getClientId=$this->Clients_model->getClientId($getdata);
//                               $inserted=$getClientId;
//                             }

//                 //       print_r($getClientId);exit;

//                           }elseif(!empty($clientAlready) && !empty($panAlready)){ // if both available
//                       //    echo"Client Already available".$clientName;
//                           $getdata['cname']=$clientName;
//                           $getdata['pan']=$panNum;
//                           $getdata['folio_number']=$folio_number;
//                           $getdata['productId']=$productId;
//                           $getdata['broker_id']=$brokerID;
//                           $getClientId=$this->Clients_model->getClientId($getdata);
//               //       print_r($getClientId);exit;
//                           $inserted=$getClientId;
//                         }
//                     //   echo $inserted;var_dump($inserted);//exit;
//                 //           else{
//                 //             $getdata['cname']=$clientName;
//                 //             $getdata['pan']=$panNum;
//                 //             $getClientId=$this->Clients_model->getClientId($getdata,$brokerID);
//                 // //       print_r($getClientId);exit;
//                 //             $inserted=$getClientId;
//                 //
//                 //           }
//   //exit;


//                     //echo"for:".$folio_number;var_dump($inserted);echo"\n";
//                           if(is_array($inserted)) {
//                               $uploadedStatus = 0;
//                               $message = 'Error while inserting records. '.$inserted['message'];
//                              // echo $folio_number;
//                               break;
//                           }else{  // insert clients bank details according to its last inserted id.

//                             $dataRowsBank[$countClient]=array(
//                               'bank_name'=>$bankName,'folio_number'=>$folio_number,'bank_branch'=>$bankBranch,'bank_acc_no'=>$bankAccNo,
//                               'bank_ifsc'=>$bankIFSC,'bank_account_types'=>$bank_account_types,'bank_address_building'=>$bank_address_building,
//                               'bank_address_road'=>$bank_address_road,'bank_address_area'=>$bank_address_area,
//                               'bank_address_city'=>$bank_address_city,'bank_pincode'=>$bank_pincode,
//                               'bank_state'=>$bank_state,'bank_country'=>$bank_country,'productId'=>$productId,'jointName1'=>$jointName1,
//                               'jointName2'=>$jointName2,'pan_no'=>$pan_no,'joint1_pan'=>$joint1_pan,'joint2_pan'=>$joint2_pan,'guard_pan'=>$guard_pan,
//                               'tax_status'=>$tax_status,'broker_code'=>$broker_code,'sub_boroker'=>$sub_boroker,'client_family_broker_id'=>$brokerID,'occ_name'=>$occ_name,
//                               'mode_holding'=>$mode_holding,'nominee_name1'=>$nominee_name1,'nom1_relation'=>$nom1_relation,'nominee_name2'=>$nominee_name2,
//                               'nom2_relation'=>$nom2_relation,'guardian_name'=>$guardian_name,'folioDate'=>$folioDate
//                               );

//                               $dataRowsBank[$countClient]['client_id']=$inserted;

//                             //  if($dataRowsBank[$countClient]['client_id']!=0){
//                                 $inserted = $this->Clients_model->add_client_bank_details($dataRowsBank[$countClient],$brokerID);
//                             //  }

//                               // print_r($inserted);die();



//                           }

//                           $countClient++;

//                           $famName = ""; $clientName = ""; $panNum = ""; $clientType = "";
//                           $comDate = ""; $occ = ""; $hof = ""; $rHof = ""; $report = "";
//                           $dob = ""; $ann = null; $spouse = ""; $child = ""; $clientId = "";
//                           $familyId = ""; $category = "";
//                           $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
//                           $mobile = ""; $tel = ""; $email = ""; $username = "";
//                           $password = ""; $pan = ""; $passport = "";
//                           $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
//                           $clientAlready=0;$panAlready=0;$notFolio=0;
//                           // //bank details variables
//                           // $bankName="";$folio_number="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";
//                           // $bank_address_road="";$bank_address_area="";$bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$productId="";
//                           // $jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";$broker_code="";$sub_boroker="";
//                           // $brokerID="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";$guardian_name="";$folioDate="";
//                       }
//                       if($uploadedStatus == 0)
//                           break;
//                       $countRow++;
//                   }

//                   // Here add the error record in the clients_auto_import_error table for further manual intervention
//                   $client_error_bakcup=$this->Clients_model->add_auto_import_error_client($imp_data);

//                   if($dataRows)
//                   {
//                       //var_dump($dataRows);
//                     //var_dump($inserted);
//                       if(is_array($inserted) && $inserted['code']!=0) {
//                           $uploadedStatus = 0;
//                           $message = 'Error while inserting records';
//                       } else {
//                           $this->Common_model->last_import('Client Details', $brokerID, $file, $user_id);
//                           if($uploadedStatus != 2) {
//                               $uploadedStatus = 1;
//                               $message = $fresh++." Client Details Uploaded Successfully";
//                           }
//                       }
//                   }
//              //echo"<pre>";print_r($dataRows);
//             //  echo"<pre>";print_r($dataRowsBank);
//                   unset($dataColumns, $dataRows,$dataRowsBank,$inserted,$getClientId,$productId,$bankBranch,$bank_address_building,$fpart,$folio_number);//exit;
//                 //}

              }elseif($fileType_info=="application/x-dbf"){  //for DBF
                  $file = $file;
                  $rta_list=$rta_list;
                //   echo $file ;exit;

                      $dbf = new dbf_class($file );
                      if($dbf=='failed'){ //dbf file  size may be zero
                          continue;
                      }
                      $num_rec=$dbf->dbf_num_rec;
                      $field_num=$dbf->dbf_num_field;

                    //  echo $num_rec;exit;
                      $excelData=array(array());
                      for($j=0; $j<$field_num; $j++){
                            array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                      }
                      for($i=0; $i<$num_rec; $i++){
                        $temper=array();
                          if ($row = $dbf->getRow($i)) {

                            for($j=0; $j<$field_num; $j++){
                                array_push($temper,$row[$j]);
                            }

                          }
                          array_push($excelData, $temper);//array of all rows
                      }
                //      echo"<pre>";print_r($excelData);

                      $dataColumns = array();
                      //stores row data
                      $dataRows = array();
                      $dataRowsBank=array();//@pallavi
                      $countRow = 0; $countErrRow = 0; $countClient = 0; $countRem = 0; $countTrans = 0;


                      //check max row for client import limit
                    //  var_dump($maxCell['row']);
                    //  $brokerID = $this->session->userdata('user_id');
                    //   $data = $this->Clients_model->get_limit($brokerID);
                    //   $count = $this->Clients_model->count_client($brokerID);
                    //   $data=intval($data->client_limit);
                    // //  var_dump($data);exit;
                    //   $count = intval($count->count);
                    //   $remaining = $data - $count;
                    //   // var_dump($remaining);
                    //   // echo $maxCell['row'];
                    //   if($num_rec>$remaining)
                    //   {
                    //       $message = "Client data exceeds your client limit! Please contact support if you want to increase your limit.";
                    //   }
                    //   else{
                    //  echo"<pre>";print_r($excelData);
                      //$tempo=$excelData;
                      foreach($excelData as $rows)
                      {
                        foreach($rows as $key=>$val)
                        {
                          //echo $key;
                          $temp[$val]=$key;
                        } break;
                      }
              //   echo"<pre>";print_r($temp);
                      foreach($excelData as $rows)
                      {
                                // print_r($rows);
                          $countCell = 0;
                          foreach($rows as $key=>$val)
                          {

                              //echo  $rows[$key];exit;
                              $cell=$rows[$key];
                              if($countRow == 0)
                              {

                                 $cell = str_replace(array('.'), '', $cell);
                              //    echo $cell;
                                    /*@pallavi code for RTA Column matching*/
                                    if($rta_list=='cams_excel'){ //for CAMS RTA for fetching columns

                                           if(strtoupper($cell) == 'INV_NAME'){
                                             $vals=$temp[$cell];
                                                 $cell='Client Name';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                            }elseif(strtoupper($cell) == 'PAN_NO'){
                                              $vals=$temp[$cell];
                                                  $cell='Pan No';
                                                  $dataColumns[$vals] = $cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'TAX_STATUS'){
                                                  $vals=$temp[$cell];
                                                  $cell='Client Type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'OCCUPATION'){
                                                  $vals=$temp[$cell];
                                                  $cell='Occupation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='INV_DOB'){
                                                  $vals=$temp[$cell];
                                                  $cell='Date Of Birth';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                              }elseif(strtoupper($cell) ==='ADDRESS1'){
                                                  $vals=$temp[$cell];
                                                  $cell='House/Flat No';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='ADDRESS2'){
                                                  $vals=$temp[$cell];
                                                  $cell='Street';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='ADDRESS3'){
                                                  $vals=$temp[$cell];
                                                  $cell='Area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) ==='CITY'){
                                                  $vals=$temp[$cell];
                                                  $cell='City';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                // }elseif(strtoupper($cell) == 'NOM_STATE'){ //not found reffered NOM_STATE col from excel
                                                //   $vals=$temp[$cell];
                                                //   $cell='State';
                                                //   $dataColumns[$vals]=$cell;
                                                //   $countCell++;
                                                //   $uploadedStatus = 2;
                                                //   continue;
                                              }elseif(strtoupper($cell) === 'PINCODE'){
                                                  $vals=$temp[$cell];
                                                  $cell='Pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'MOBILE_NO'){
                                                  $vals=$temp[$cell];
                                                  $cell='Mobile';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'PHONE_RES'){
                                                  $vals=$temp[$cell];
                                                  $cell='Telephone';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'EMAIL'){
                                                  $vals=$temp[$cell];
                                                  $cell='Email ID';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLIOCHK'){ // not found
                                                  $vals=$temp[$cell];
                                                  $cell='Folio_Number';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                              }elseif(strtoupper($cell) === 'BROKER_COD'){
                                                  $vals=$temp[$cell];
                                                  $cell='Broker Code';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'REP_DATE'){  //not confirmed
                                                  $vals=$temp[$cell];
                                                  $cell='Commencement Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BANK_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'BRANCH'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_branch';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'IFSC_CODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_ifsc';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'AC_NO'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_no';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'AC_TYPE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_acc_type';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS1'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_building';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_road';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_ADDRESS3'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_area';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_CITY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_city';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) === 'B_PINCODE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_pincode';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }
                                                elseif(strtoupper($cell) == 'B_STATE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_state';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'B_COUNTRY'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Bank_address_country';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'PRODUCT'){
                                                  $vals=$temp[$cell];
                                                  $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JNT_NAME1'){
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JNT_NAME2'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint_name2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARD_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian Name';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT1_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 1 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'JOINT2_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Joint 2 pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'GUARD_PAN'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Guardian pan';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'SUBBROKER'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Sub Broker';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'HOLDING_NA'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Mode Of Holding';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'RELATION'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee1_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM2_NAME'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'NOM2_RELAT'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Nominee2_Relation';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }elseif(strtoupper($cell) == 'FOLIO_DATE'){  // bank details headers
                                                  $vals=$temp[$cell];
                                                  $cell='Folio Date';
                                                  $dataColumns[$vals]=$cell;
                                                  $countCell++;
                                                  $uploadedStatus = 1;
                                                  continue;
                                                }

                                      }
                              }
                              else
                              {
                  // echo"<pre>"; print_r($dataColumns);exit;//check-column-headings

                                if($rta_list=='cams_excel'){  // for CAMS RTA
                                 // unset($brokerID,$user_id);
                                  if($insertRow)
                                  {

                                    if(!empty($findkey=array_search('Broker Code',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                            $broker_code = trim($cell);

                                          }else{
                                            $broker_code="";
                                          }

                                          $arn=$broker_code;
                                          $user=$this->Clients_model->get_userdata($arn);
                                        // //  echo"<pre>";print_r($user);exit;
                                        //   if(count($user)>0){
                                        //     if($user[0]['status']==1 && $user[0]['user_type']=='broker'){
                                        //       $brokerID=$user[0]['id'];  // for client bank details table
                                        //       $user_id=$user[0]['id'];  //for clients table
                                        //     }
                                        //
                                        //   }
                                        // $brokerID=$abscence_id;  // for client bank details table
                                        // $user_id=$abscence_id;
                                       }


                                    if(!empty($findkey=array_search('Family Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $famName = trim($cell);
                                      //echo $famName;exit;
                                              //checks if family exists in Families table
                                              $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                              if(count($f_info) == 0)
                                              {
                                                  //$insertRow = false;
                                                  //$impMessage = $famName." Family doesn't exist";
                                                  //family doesn't exist, so add a new family
                                                  $data = array(
                                                      'name' => $famName,
                                                      'status' => 1,
                                                      'broker_id' => $brokerID,
                                                      'user_id' => $user_id
                                                  );
                                                  $status = $this->Families_model->add($data);
                                                  if(isset($status['code'])) {
                                                      $insertRow = false;
                                                      $impMessage = "Could not add new Family for this client";
                                                      $impErrorCol='Family Name';
                                                  } else {
                                                      $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                                      if(count($f_info) == 0)
                                                      {
                                                          $insertRow = false;
                                                          $impMessage = "Could not find Family for this client";
                                                          $impErrorCol='Family Name';
                                                      }
                                                      else
                                                      {
                                                          $familyId = $f_info[0]->family_id;
                                                      }
                                                  }
                                              }
                                              else
                                              {
                                                  $familyId = $f_info[0]->family_id;
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Family Name cannot be empty";
                                          // }
                                      }else{ // If family name is not found in excel then drop the client in default family

                                            $famName ="Default family"; //default family for non family client
                                            $f_info = $this->Families_model->get_families_broker($brokerID,$famName);
                                            $familyId = @$f_info[0]->family_id;//21 march 2018 pallavi
                                      }


                                    if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];

                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                            //echo $clientName; exit;
                                              $whereClient = array(
                                                  'c.name'=>$clientName,
                                                  'fam.name'=>$famName,
                                                  'fam.broker_id'=>$brokerID
                                              );
                                              //checks if client exists in Clients table
                                              $c_info = $this->Clients_model->get_clients_broker_dropdown($whereClient);
                                              if(count($c_info) == 0)
                                              {
                                                  $clientId_obj = $this->Clients_model->get_new_client_id();
                                                  $clientId = $clientId_obj->client_id;
                                              }
                                              else
                                              {
                                                $clientAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Client Name ".$clientName." already exists. Please change the Client Name";
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $impMessage = "Client Name cannot be empty";
                                              $impErrorCol='Client Name';
                                          }
                                      }


                                    if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          //  echo $panNum;exit;
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if policy exists in policy details table
                                              $p_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($p_info) == 0)
                                              {
                                                  //proceed normally
                                              }
                                              else
                                              {
                                                $panAlready=1;
                                                  // $insertRow = false;
                                                  // $impMessage = "Pan No. ".$panNum." already exists for another client";

                                              }
                                          }
                                          // else
                                          // {
                                          //
                                          //    $clientId_obj = $this->Clients_model->get_new_client_id();
                                          //    $dummypan = $clientId_obj->client_id;
                                          //    //C20160001
                                          //
                                          //   $panNum =str_replace('C','P',$dummypan);
                                          // //  echo "dummy pan".$panNum;exit;
                                          // }
                                          $pan_no=$panNum; //for client Bank  details
                                      }

                                  if(!empty($findkey=array_search('Client Type',$dataColumns)))
                                    {
                                      $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $clientType = trim($cell);
                                              //  echo $clientType;
                                              $whereCType = 'client_type_name = "'.$clientType.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $type_details = $this->Clients_model->get_client_types($whereCType);
                                              if(count($type_details) == 0)
                                              {
                                                  //$clientTypeId=40; //for others type of client Type
                                                  // $impMessage = 'Client Type '.$clientType." doesn't exist";
                                                  // $insertRow = false;
                                                  $defaultClientType='Resident Individual';
                                                    $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                                $ctype= $this->Clients_model->get_client_types($whereCType);
                                                $clientTypeId =  $ctype[0]->client_type_id;
                                              }
                                              else
                                              {
                                                  $clientTypeId = $type_details[0]->client_type_id;
                                              }
                                          }
                                          else
                                          {
                                              //$clientTypeId=40; //for others type of client Type
                                              // $insertRow = false;
                                              // $impMessage = "Client Type cannot be empty";
                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;
                                          }
                                            $tax_status=$clientType; //for client bank details
                                      }else{

                                              $defaultClientType='Resident Individual';
                                                $whereCType = 'client_type_name like "'.$defaultClientType.'"  AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                            $ctype= $this->Clients_model->get_client_types($whereCType);
                                            $clientTypeId =  $ctype[0]->client_type_id;

                                                $tax_status=""; //for client bank details
                                                  $clientType=$clientTypeId;
                                      }

                                     if(!empty($findkey=array_search('Occupation',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $occ = trim($cell);
                                            //  echo $occ;exit;
                                              $whereOcc = 'occupation_name = "'.$occ.'" AND (broker_id = "'.$brokerID.'" OR broker_id IS NULL)';
                                              $occ_details = $this->Clients_model->get_occupations_dropdown($whereOcc);
                                              if(count($occ_details) == 0)
                                              {
                                                  $occId=null; //for others type of occupation
                                                  // $impMessage = 'Occupation '.$occ." doesn't exist";
                                                  // $insertRow = false;
                                              }
                                              else
                                              {
                                                  $occId = $occ_details[0]->occupation_id;
                                              }
                                          }
                                          else
                                          {
                                            $occId=null;
                                              // $insertRow = false;
                                              // $impMessage = "Occupation cannot be empty";
                                          }
                                           $occ_name=$occ; //for bank details
                                      }

                                if(!empty($findkey=array_search('Head Of Family',$dataColumns)))
                                  {
                                    $cell=$rows[$findkey];
                                      if($cell || $cell != '')
                                          {
                                              $hof = trim($cell);

                                              if(strtoupper($hof) == "YES") {
                                                  $hof = 1;
                                              } else {
                                                  $hof = 0;
                                              }
                                          }
                                          else
                                          {
                                              $hof = 0;
                                          }
                                          //  echo $hof;exit;
                                      }

                                     if(!empty($findkey=array_search('Relation W/ HOF',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $rHof = trim($cell);
                                              //echo $rHof;
                                          }
                                          else
                                          {
                                              $rHof = "";
                                          }

                                      }


                                      if(!empty($findkey=array_search('Date Of Birth',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            //echo $cell;
                                         //  $cell=trim(str_replace('/','-', $cell));
                                      //  echo $cell; //exit; //date checked
                                         if($cell || $cell != '')
                                         {
                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                             //var_dump($cell);exit;
                                            // $date->format('Y-m-d');
                                             if(is_object($date)){
                                               $dob=$date->format('Y-m-d');
                                             }else{
                                               $date = new DateTime($cell);
                                                if(is_object($date)){
                                                  $dob=$date->format('Y-m-d');
                                                }else{

                                                        $insertRow = false;
                                                        $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                        $impErrorCol='Date Of Birth';
                                                    }

                                                }

                                             } else
                                              {
                                                  $dob = null;
                                                  $dob_app = 0;
                                              }

                                         }else
                                         {
                                             $dob = null;
                                             $dob_app = 0;
                                         }

                                      if(!empty($findkey=array_search('House/Flat No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $flat = trim($cell);
                                          }
                                          else
                                          {
                                              $flat = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Street',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $street = trim($cell);
                                          }
                                          else
                                          {
                                              $street = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Area',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $area = trim($cell);
                                          }
                                          else
                                          {
                                              $area = "";
                                          }

                                      }
                                       if(!empty($findkey=array_search('City',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $city = trim($cell);
                                          }
                                          else
                                          {
                                            $city="";
                                              // $insertRow = false;
                                              // $impMessage = "City cannot be empty";
                                          }

                                      }

                                      if(!empty($findkey=array_search('State',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $state = trim($cell);
                                          }
                                          else
                                          {
                                              $state="";
                                              // $insertRow = false;
                                              // $impMessage = "State cannot be empty";
                                          }
                                      }

                                       if(!empty($findkey=array_search('Pincode',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $pin = trim($cell);
                                          }
                                          else
                                          {
                                              $pin = "";
                                          }

                                      }

                                       if(!empty($findkey=array_search('Mobile',$dataColumns)))
                                         {
                                           $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $mobile = trim($cell);
                                          }
                                          else
                                          {
                                              $mobile = "";
                                          }

                                      }

                                      if(!empty($findkey=array_search('Telephone',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $tel = trim($cell);
                                          }
                                          else
                                          {
                                              $tel = "";
                                          }

                                      }

                                    if(!empty($findkey=array_search('Email ID',$dataColumns)))
                                      {
                                        $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $email = trim($cell);
                                          }
                                          else
                                          {
                                            $email="";
                                              // $insertRow = false;
                                              // $impMessage = "Email ID cannot be empty";
                                          }

                                      }

                                      if(!empty($findkey=array_search('Username',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell  || $cell != '')
                                          {
                                              $username = trim($cell);
                                              //check duplicate username
                                              //$duplicateConditionUsername = 'c.username = "'.$username.'" AND f.broker_id = "'.$brokerID.'"';
                                              $duplicateConditionUsername = 'c.username = "'.$username.'"';
                                              $join = array(
                                                  'table' => 'families f',
                                                  'on' => 'c.family_id = f.family_id',
                                                  'type' => 'inner'
                                              );
                                              $isUsernameDuplicate = $this->Clients_model->check_duplicate('clients c',$duplicateConditionUsername, $join);

                                              if($isUsernameDuplicate) {
                                                //  $insertRow = false; $impMessage = "Username already exists for another client";
                                              $username=date("Ymd").time().$username.rand(99,99999);
                                              }
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Username cannot be empty";
                                          // }
                                      }else{ //if username is not in excel sheet create default username with client name and random number
                                        $username=date("Ymd").time().str_replace(' ','',$clientName).rand(99,99999);

                                      }


                                      if(!empty($findkey=array_search('Passport No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != ''){
                                              $passport = trim($cell);
                                            }else{
                                              $passport = "";
                                            }
                                        //  $broker_code=$passport; //for bank details
                                       }

                                       if(!empty($findkey=array_search('Commencement Date',$dataColumns)))
                                       {
                                         $cell=$rows[$findkey];
                                           $cell=trim(str_replace('/','-', $cell));
                                         if($cell || $cell != '')
                                         {
                                          $date = DateTime::createFromFormat('m-d-Y', $cell);
                                             //var_dump($cell);exit;
                                            // $date->format('Y-m-d');
                                             if(is_object($date)){
                                               $comDate=$date->format('Y-m-d');
                                             }else{
                                               $date = new DateTime($cell);
                                                if(is_object($date)){
                                                  $comDate=$date->format('Y-m-d');

                                                }else{

                                                        $insertRow = false;
                                                        $mfMessage = "Commencement Date format is not proper (should be dd/mm/yyyy)";
                                                          $impErrorCol='Commencement Date';
                                                    }

                                                }

                                             } else
                                              {
                                                  $comDate = null;
                                                  $dob_app = 0;
                                              }
                                               // echo $dob;//exit;
                                         }else
                                         {
                                             $comDate = null;
                                             $dob_app = 0;
                                         }
                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                                $folio_number = stripcslashes(trim($cell));
                                                $folio_number = str_replace("'",'',$folio_number);

                                            }
                                        }elseif($findkey==0){
                                          $cell=$rows[$findkey];
                                          $folio_number = stripcslashes(trim($cell));
                                          $folio_number = str_replace("'",'',$folio_number);

                                        }
                                      if(!empty($findkey=array_search('Bank_Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                            if($cell || $cell != '')
                                            {
                                                $bankName = trim($cell);

                                            }
                                        }
                                        if(!empty($findkey=array_search('Bank_branch',$dataColumns)))
                                          {
                                            $cell=$rows[$findkey];
                                              if($cell || $cell != '')
                                              {
                                                  $bankBranch = trim($cell);

                                              }
                                          }
                                          if(!empty($findkey=array_search('Bank_acc_type',$dataColumns)))
                                            {
                                              $cell=$rows[$findkey];
                                                if($cell || $cell != '')
                                                {
                                                    $bank_account_types = trim($cell);

                                                }
                                            }
                                            if(!empty($findkey=array_search('Bank_acc_no',$dataColumns)))
                                              {
                                                $cell=$rows[$findkey];
                                                  if($cell || $cell != '')
                                                  {
                                                      $bankAccNo = trim($cell);

                                                  }
                                              }
                                              if(!empty($findkey=array_search('Bank_address_building',$dataColumns)))
                                                {
                                                  $cell=$rows[$findkey];
                                                    if($cell || $cell != '')
                                                    {
                                                        $bank_address_building = trim($cell);

                                                    }
                                                }
                                                if(!empty($findkey=array_search('Bank_address_road',$dataColumns)))
                                                  {
                                                    $cell=$rows[$findkey];
                                                      if($cell || $cell != '')
                                                      {
                                                          $bank_address_road = trim($cell);

                                                      }
                                                  }
                                                  if(!empty($findkey=array_search('Bank_address_area',$dataColumns)))
                                                    {
                                                      $cell=$rows[$findkey];
                                                        if($cell || $cell != '')
                                                        {
                                                            $bank_address_area = trim($cell);

                                                        }
                                                    }
                                                    if(!empty($findkey=array_search('Bank_address_city',$dataColumns)))
                                                      {
                                                        $cell=$rows[$findkey];
                                                          if($cell || $cell != '')
                                                          {
                                                              $bank_address_city = trim($cell);

                                                          }
                                                      }
                                                      if(!empty($findkey=array_search('Bank_address_pincode',$dataColumns)))
                                                        {
                                                          $cell=$rows[$findkey];
                                                            if($cell || $cell != '')
                                                            {
                                                                $bank_pincode = trim($cell);

                                                            }
                                                        }
                                                        if(!empty($findkey=array_search('Bank_address_state',$dataColumns)))
                                                          {
                                                            $cell=$rows[$findkey];
                                                              if($cell || $cell != '')
                                                              {
                                                                  $bank_state = trim($cell);

                                                              }
                                                          }
                                                          if(!empty($findkey=array_search('Bank_address_country',$dataColumns)))
                                                            {
                                                              $cell=$rows[$findkey];
                                                                if($cell || $cell != '')
                                                                {
                                                                    $bank_country = trim($cell);

                                                                }
                                                            }
                                                          //for other details of bank
                                                          if(!empty($findkey=array_search('Scheme_Id',$dataColumns)))
                                                            {
                                                              // $cell=$rows[$findkey];
                                                              //   if($cell || $cell != '')
                                                              //   {
                                                              //     $productId = trim($cell);
                                                              //
                                                              //   }else{
                                                              //     $productId="";
                                                              //   }

                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $productId = trim($cell);
                                                                              $data=array();
                                                                              if($productId){
                                                                               $data['productId']=$productId;
                                                                              }
                                                                             $data['broker_id']=$brokerID;
                                                                             if($folio_number){
                                                                                $data['folio_number']=$folio_number;
                                                                             }
                                                                             if($clientName){
                                                                                    $data['clientName']=$clientName;
                                                                             }

                                                                             if($pan_no){
                                                                               $data['pan_no']=$pan_no;
                                                                             }

                                                                              //check product scheme  already available
                                                                             $scheme_info = $this->Clients_model->get_client_family_by_scheme($data);
                                                                      //   print_r($scheme_info);exit;
                                                                             if(count($scheme_info) == 0)
                                                                             {
                                                                                 //proceed normally
                                                                                // $productId=$productId;
                                                                                $notFolio=0;// client name,folio,scheme,pan combination is not available

                                                                             }
                                                                             else
                                                                             {
                                                                                $notFolio=1;
                                                                                //  $insertRow = false;
                                                                                //    $impMessage = "Product Code < ".$productId." > and Folio No.<".$folio_number.".> are already exists for another client< ".$clientName." >";

                                                                             }
                                                                      }

                                                            }
                                                            if(!empty($findkey=array_search('Joint_name1',$dataColumns)))
                                                              {
                                                                $cell=$rows[$findkey];
                                                                  if($cell || $cell != '')
                                                                  {
                                                                    $jointName1 = trim($cell);

                                                                  }else{
                                                                    $jointName1="";
                                                                  }
                                                              }
                                                              if(!empty($findkey=array_search('Joint_name2',$dataColumns)))
                                                                {
                                                                  $cell=$rows[$findkey];
                                                                    if($cell || $cell != '')
                                                                    {
                                                                      $jointName2 = trim($cell);

                                                                    }else{
                                                                      $jointName2="";
                                                                    }
                                                                }
                                                                if(!empty($findkey=array_search('Joint 1 pan',$dataColumns)))
                                                                  {
                                                                    $cell=$rows[$findkey];
                                                                      if($cell || $cell != '')
                                                                      {
                                                                        $joint1_pan = trim($cell);

                                                                      }else{
                                                                        $joint1_pan="";
                                                                      }
                                                                  }
                                                                  if(!empty($findkey=array_search('Joint 2 pan',$dataColumns)))
                                                                    {
                                                                      $cell=$rows[$findkey];
                                                                        if($cell || $cell != '')
                                                                        {
                                                                          $joint2_pan = trim($cell);

                                                                        }else{
                                                                          $joint2_pan="";
                                                                        }
                                                                    }
                                                                    if(!empty($findkey=array_search('Guardian pan',$dataColumns)))
                                                                      {
                                                                        $cell=$rows[$findkey];
                                                                          if($cell || $cell != '')
                                                                          {
                                                                            $guard_pan = trim($cell);

                                                                          }else{
                                                                            $guard_pan="";
                                                                          }
                                                                      }

                                                                      if(!empty($findkey=array_search('Sub Broker',$dataColumns)))
                                                                        {
                                                                          $cell=$rows[$findkey];
                                                                            if($cell || $cell != '')
                                                                            {
                                                                              $sub_boroker = trim($cell);

                                                                            }else{
                                                                              $sub_boroker="";
                                                                            }
                                                                        }
                                                                        if(!empty($findkey=array_search('Mode Of Holding',$dataColumns)))
                                                                          {
                                                                            $cell=$rows[$findkey];
                                                                              if($cell || $cell != '')
                                                                              {
                                                                                $mode_holding = trim($cell);

                                                                              }else{
                                                                                $mode_holding="";
                                                                              }
                                                                          }
                                                                          if(!empty($findkey=array_search('Nominee1',$dataColumns)))
                                                                            {
                                                                              $cell=$rows[$findkey];
                                                                                if($cell || $cell != '')
                                                                                {
                                                                                  $nominee_name1 = trim($cell);

                                                                                }else{
                                                                                  $nominee_name1="";
                                                                                }
                                                                            }
                                                                            if(!empty($findkey=array_search('Nominee1_Relation',$dataColumns)))
                                                                              {
                                                                                $cell=$rows[$findkey];
                                                                                  if($cell || $cell != '')
                                                                                  {
                                                                                    $nom1_relation = trim($cell);

                                                                                  }else{
                                                                                    $nom1_relation ="";
                                                                                  }
                                                                              }
                                                                              if(!empty($findkey=array_search('Nominee2',$dataColumns)))
                                                                                {
                                                                                  $cell=$rows[$findkey];
                                                                                    if($cell || $cell != '')
                                                                                    {
                                                                                      $nominee_name2 = trim($cell);

                                                                                    }else{
                                                                                      $nominee_name2="";
                                                                                    }
                                                                                }
                                                                                if(!empty($findkey=array_search('Nominee2_Relation',$dataColumns)))
                                                                                  {
                                                                                    $cell=$rows[$findkey];
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                        $nom2_relation = trim($cell);

                                                                                      }else{
                                                                                        $nom2_relation ="";
                                                                                      }
                                                                                  }
                                                                                  if(!empty($findkey=array_search('Guardian Name',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        if($cell || $cell != '')
                                                                                        {
                                                                                          $guardian_name= trim($cell);

                                                                                        }else{
                                                                                          $guardian_name="";
                                                                                        }
                                                                                    }
                                                                                    if(!empty($findkey=array_search('Folio Date',$dataColumns)))
                                                                                    {
                                                                                      $cell=$rows[$findkey];
                                                                                        $cell=trim(str_replace('/','-', $cell));
                                                                                      if($cell || $cell != '')
                                                                                      {
                                                                                       $date = DateTime::createFromFormat('m-d-Y', $cell);
                                                                                          //var_dump($cell);exit;
                                                                                         // $date->format('Y-m-d');
                                                                                          if(is_object($date)){
                                                                                            $folioDate=$date->format('Y-m-d');
                                                                                          }else{
                                                                                            $date = new DateTime($cell);
                                                                                             if(is_object($date)){
                                                                                               $folioDate=$date->format('Y-m-d');

                                                                                             }else{

                                                                                                     // $insertRow = false;
                                                                                                     // $mfMessage = "Folio Date format is not proper (should be dd/mm/yyyy)";
                                                                                                 }

                                                                                             }

                                                                                          } else
                                                                                           {
                                                                                               $folioDate = null;
                                                                                               $dob_app = 0;
                                                                                           }
                                                                                            // echo $dob;//exit;
                                                                                      }else
                                                                                      {
                                                                                          $folioDate = null;
                                                                                          $dob_app = 0;
                                                                                      }




                                  } else {


                                      if(!empty($findkey=array_search('Client Name',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $clientName = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Client Name cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Pan No',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Pan Number cannot be empty";
                                          // }
                                      }
                                      if(!empty($findkey=array_search('Folio_Number',$dataColumns)))
                                        {
                                          $cell=$rows[$findkey];
                                          if($cell || $cell != '')
                                          {
                                              $folio_number = trim($cell);
                                          }
                                          // else
                                          // {
                                          //     $insertRow = false;
                                          //     $impMessage = "Folio Number cannot be empty";
                                          // }
                                      }


                                  }
                                  $countCell++;

                                }

                              }

                          }
                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                  // $imp_data[$countErrRow][1] = $famName;
                                  // $imp_data[$countErrRow][2] = $clientName;
                                  // $imp_data[$countErrRow][3] = $panNum;
                                  // $imp_data[$countErrRow][4] = $impMessage;

                                  $imp_data[$countErrRow]['name'] = $clientName;
                                  $imp_data[$countErrRow]['email_id'] = $email;
                                  $imp_data[$countErrRow]['dob'] = $dob;
                                  $imp_data[$countErrRow]['occupation_id'] = $occId;
                                   $imp_data[$countErrRow]['head_of_family'] = $hof;
                                    $imp_data[$countErrRow]['relation_HOF'] = $rHof;
                                     $imp_data[$countErrRow]['client_type'] = $clientTypeId;
                                      $imp_data[$countErrRow]['spouse_name'] = $spouse;
                                       $imp_data[$countErrRow]['anv_date'] = $ann;
                                        $imp_data[$countErrRow]['pan_no'] = $panNum;
                                         $imp_data[$countErrRow]['passport_no'] = $passport;
                                          $imp_data[$countErrRow]['add_flat'] = $flat;
                                           $imp_data[$countErrRow]['add_street'] = $street;
                                            $imp_data[$countErrRow]['add_area'] = $area;
                                            $imp_data[$countErrRow]['add_city'] = $city;
                                            $imp_data[$countErrRow]['add_state'] = $state;
                                            $imp_data[$countErrRow]['add_pin'] = $pin;
                                            $imp_data[$countErrRow]['telephone'] = $tel;
                                            $imp_data[$countErrRow]['mobile'] = $mobile;
                                            $imp_data[$countErrRow]['date_of_comm'] = $comDate;
                                            $imp_data[$countErrRow]['family_id'] = $familyId;
                                           $imp_data[$countErrRow]['children_name'] = $child;
                                            $imp_data[$countErrRow]['report_order'] = $report;
                                            $imp_data[$countErrRow]['user_id'] = $user_id;
                                            $imp_data[$countErrRow]['status'] = 1;
                                            $imp_data[$countErrRow]['client_category'] = $category;
                                            $imp_data[$countErrRow]['bank_name'] = $bankName;
                                            $imp_data[$countErrRow]['bank_branch'] = $bankBranch;
                                            $imp_data[$countErrRow]['bank_acc_no'] = $bankAccNo;
                                            $imp_data[$countErrRow]['bank_ifsc'] = $bankIFSC;
                                            $imp_data[$countErrRow]['bank_account_types'] = $bank_account_types;
                                            $imp_data[$countErrRow]['bank_address_building'] = $bank_address_building;
                                            $imp_data[$countErrRow]['bank_address_area'] = $bank_address_area;
                                              $imp_data[$countErrRow]['bank_address_road'] = $bank_address_road;
                                            $imp_data[$countErrRow]['bank_address_city'] = $bank_address_city;
                                            $imp_data[$countErrRow]['bank_pincode'] = $bank_pincode;
                                            $imp_data[$countErrRow]['bank_state'] = $bank_state;
                                            $imp_data[$countErrRow]['bank_country'] = $bank_country;
                                            $imp_data[$countErrRow]['productId'] = $productId;
                                            $imp_data[$countErrRow]['jointName1'] = $jointName1;
                                            $imp_data[$countErrRow]['jointName2'] = $jointName2;
                                            $imp_data[$countErrRow]['joint1_pan'] = $joint1_pan;
                                            $imp_data[$countErrRow]['joint2_pan'] = $joint2_pan;
                                             $imp_data[$countErrRow]['guard_pan'] = $guard_pan;
                                              $imp_data[$countErrRow]['tax_status'] = $tax_status;
                                               $imp_data[$countErrRow]['broker_code'] = $broker_code;
                                                $imp_data[$countErrRow]['sub_boroker'] = $sub_boroker;
                                                 $imp_data[$countErrRow]['occ_name'] = $occ_name;
                                                  $imp_data[$countErrRow]['mode_holding'] = $mode_holding;
                                                   $imp_data[$countErrRow]['nominee_name1'] = $nominee_name1;
                                                    $imp_data[$countErrRow]['nom1_relation'] = $nom1_relation;
                                                     $imp_data[$countErrRow]['nominee_name2'] = $nominee_name2;
                                                      $imp_data[$countErrRow]['nom2_relation'] = $nom2_relation;
                                                       $imp_data[$countErrRow]['guardian_name'] = $guardian_name;
                                                        $imp_data[$countErrRow]['folioDate'] =$folioDate;
                                                        $imp_data[$countErrRow]['error_msg'] = $impMessage;
                                                        $imp_data[$countErrRow]['error_col_list'] = $impErrorCol;//implode(",",$mfErrorCol);
                                                        $imp_data[$countErrRow]['file_name'] = $file;//implode(",",$mfErrorCol);
                                                          $imp_data[$countErrRow]['file_download_date'] = date('Y-m-d');





                                  $countErrRow++;
                                  $insertRow = true;
                                  $uploadedStatus = 2;
                                  continue;
                              }

                              $dataRows[$countClient] = array(
                                  'name' => $clientName, 'family_id' => $familyId,
                                  'email_id' => $email, 'dob' => $dob, 'dob_app' => $dob_app,
                                  //'password' => sha1($password),
                                  'occupation_id' => $occId, 'head_of_family' => $hof, 'relation_HOF' => $rHof,
                                  'client_type' => $clientTypeId, 'spouse_name' => $spouse,
                                  'anv_date' => $ann, 'anv_app' => $anv_app, 'pan_no' => $panNum,
                                  'passport_no' => $passport, 'add_flat' => $flat, 'add_street' => $street,
                                  'add_area' => $area, 'add_city' => $city, 'add_state' => $state,
                                  'add_pin' => $pin, 'telephone' => $tel, 'mobile' => $mobile,
                                  'date_of_comm' => $comDate, 'children_name' => $child, 'report_order' => $report,
                                  'user_id' => $user_id, 'status' => 1, 'username' => $username,
                                  'client_category' => $category
                              );

            //   echo"<pre>";print_r($dataRows);
              //echo"<pre>";print_r($dataRowsBank);exit;
              unset($inserted,$getdata);
              if(empty($clientAlready) && empty($panAlready)){  //both not available
            //  echo"fresh client".$clientName;
            //     $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
            //      $fresh++;

            if(empty($notFolio)){  // if client name not available but pan not available also folio,and product id not available
            //  echo"fresh client".$clientName.$productId.$folio_number.$pan_no;//exit;
              $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
              $fresh++;
            }else{
            //  echo"in  folio"."but no client no pan  but folio and product id avalilable";
              $getdata['folio_number']=$folio_number;
              $getdata['productId']=$productId;
              $getdata['broker_id']=$brokerID;
              $getClientId=$this->Clients_model->getClientId($getdata);
              $inserted=$getClientId;
            }


              }elseif(empty($clientAlready) && !empty($panAlready)){ // if pan available and client name not available
              //  echo"no client but pan available".$clientName;
              //  $getdata['cname']=$clientName;
                $getdata['pan']=$panNum;
                $getdata['productId']=$productId;
                $getdata['broker_id']=$brokerID;
                $getClientId=$this->Clients_model->getClientId($getdata);
    //       print_r($getClientId);exit;
              $inserted=$getClientId;
              }elseif(!empty($clientAlready) && empty($panAlready)){   // if client name available and pan not available

                if(empty($notFolio)){  /// if client name available but pan not available also folio,and product id also not available
                //  echo"in not folio".$clientName.$productId.$folio_number.$pan_no;//exit;
                 $inserted = $this->Clients_model->add_client($dataRows[$countClient]);
                  $fresh++;
                }else{                             // if client name available but pan not available also folio,and product same
                //  echo"in  folio".$clientName;
                  $getdata['cname']=$clientName;
                  $getdata['folio_number']=$folio_number;
                  $getdata['productId']=$productId;
                  $getdata['broker_id']=$brokerID;
                  $getClientId=$this->Clients_model->getClientId($getdata);
                  $inserted=$getClientId;
                }

    //       print_r($getClientId);exit;

              }elseif(!empty($clientAlready) && !empty($panAlready)){ // if both available
          //    echo"Client Already available".$clientName;
              $getdata['cname']=$clientName;
              $getdata['pan']=$panNum;
              $getdata['folio_number']=$folio_number;
              $getdata['productId']=$productId;
              $getdata['broker_id']=$brokerID;
              $getClientId=$this->Clients_model->getClientId($getdata);
  //       print_r($getClientId);exit;
              $inserted=$getClientId;
            }

                            //  $inserted = $this->Clients_model->add_client($dataRows[$countClient]);

                              //print_r($inserted);
                              if(is_array($inserted)) {
                                  $uploadedStatus = 0;
                                  $message = 'Error while inserting records. '.$inserted['message'];
                                  break;
                              }else{  // insert clients bank details according to its last inserted id.

                                $dataRowsBank[$countClient]=array(
                                  'bank_name'=>$bankName,'folio_number'=>$folio_number,'bank_branch'=>$bankBranch,'bank_acc_no'=>$bankAccNo,
                                  'bank_ifsc'=>$bankIFSC,'bank_account_types'=>$bank_account_types,'bank_address_building'=>$bank_address_building,
                                  'bank_address_road'=>$bank_address_road,'bank_address_area'=>$bank_address_area,
                                  'bank_address_city'=>$bank_address_city,'bank_pincode'=>$bank_pincode,
                                  'bank_state'=>$bank_state,'bank_country'=>$bank_country,'productId'=>$productId,'jointName1'=>$jointName1,
                                  'jointName2'=>$jointName2,'pan_no'=>$pan_no,'joint1_pan'=>$joint1_pan,'joint2_pan'=>$joint2_pan,'guard_pan'=>$guard_pan,
                                  'tax_status'=>$tax_status,'broker_code'=>$broker_code,'sub_boroker'=>$sub_boroker,'client_family_broker_id'=>$brokerID,'occ_name'=>$occ_name,
                                  'mode_holding'=>$mode_holding,'nominee_name1'=>$nominee_name1,'nom1_relation'=>$nom1_relation,'nominee_name2'=>$nominee_name2,
                                  'nom2_relation'=>$nom2_relation,'guardian_name'=>$guardian_name,'folioDate'=>$folioDate
                                  );

                                  $dataRowsBank[$countClient]['client_id']=$inserted;

                                    $inserted = $this->Clients_model->add_client_bank_details($dataRowsBank[$countClient]);

                              }

                              $countClient++;

                              $famName = ""; $clientName = ""; $panNum = ""; $clientType = "";
                              $comDate = ""; $occ = ""; $hof = ""; $rHof = ""; $report = "";
                              $dob = ""; $ann = null; $spouse = ""; $child = ""; $clientId = "";
                              $familyId = ""; $category = "";
                              $flat = ""; $street = ""; $area = ""; $city = ""; $state=""; $pin = "";
                              $mobile = ""; $tel = ""; $email = ""; $username = "";
                              $password = ""; $pan = ""; $passport = "";
                              $clientTypeId = ""; $occId = ""; $dob_app = 0; $anv_app = 0;
                                $clientAlready=0;$panAlready=0;
                          //  //bank details variables
                          //  $bankName="";$folio_number="";$bankBranch="";$bankAccNo="";$bankIFSC="";$bank_account_types="";$bank_address_building="";
                          //  $bank_address_road="";$bank_address_area="";$bank_address_city="";$bank_pincode="";$bank_state="";$bank_country="";$productId="";
                          //  $jointName1="";$jointName2="";$pan_no="";$joint1_pan="";$joint2_pan="";$guard_pan="";$tax_status="";$broker_code="";$sub_boroker="";
                          //  $brokerID="";$occ_name="";$mode_holding="";$nominee_name1="";$nom1_relation="";$nominee_name2="";$nom2_relation="";$guardian_name="";$folioDate="";

                          }
                          if($uploadedStatus == 0)
                              break;
                          $countRow++;
                      }

                      // Here add the error record in the clients_auto_import_error table for further manual intervention
                      $client_error_bakcup=$this->Clients_model->add_auto_import_error_client($imp_data);

                      if($dataRows)
                      {

                          if(is_array($inserted)) {
                              $uploadedStatus = 0;
                              $message = 'Error while inserting records';
                          } else {
                              $this->Common_model->last_import('Client Details', $brokerID,$file, $user_id);
                              if($uploadedStatus != 2) {
                                  $uploadedStatus = 1;
                                  $message = $fresh++." Client Details Uploaded Successfully";
                              }
                          }
                      }
                      unset($dataColumns, $dataRows,$dataRowsBank,$inserted,$productId);//exit;
                    //}

                }//end of DBF FILE


            }//////end of  melse
        }else                                   //end of mif
        {
            $message = "No file selected";
        }
        if($uploadedStatus == 1)
        {
            $success = array(
                "title" => "Success!",
                "text" => $message
            );
            $this->session->set_userdata('success', $success);
        }
        else if ($uploadedStatus == 2)
        {
            $info = array(
                "title" => "Info for Import!",
                "text" => 'Few Records were not imported. Please check the table below'
            );
            $this->session->set_userdata('info', $info);
        }
        else
        {
            $error = array(
                "title" => "Error on uploading!",
                "text" => $message
            );
            $this->session->set_userdata('error', $error);
        }
    }                                            // end of mmif
//return $imp_data;
unset($abscence_id);
return $file;
}

// end of Import Code

function mime_type($file) {

    // there's a bug that doesn't properly detect
    // the mime type of css files
    // https://bugs.php.net/bug.php?id=53035
    // so the following is used, instead
    // src: http://www.freeformatter.com/mime-types-list.html#mime-types-list

    $mime_type = array(
      "dbf" => "application/x-dbf",
      "xls"=>"application/vnd.ms-excel",
      "xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application" => "application/x-ms-application",
        // etc...
        // truncated due to Stack Overflow's character limit in posts
    );
    $extension = \strtolower(\pathinfo($file, \PATHINFO_EXTENSION));

    if (isset($mime_type[$extension])) {
        return $mime_type[$extension];
    } else {
        throw new \Exception("Unknown file type");
    }

}

//MF import
  function mf_import($tflag,$tfile,$abscence_id1)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('upload_max_filesize', '50M');//50M
        ini_set('post_max_size', '50M');//50M
   //initailly set broker and user_id
     $brokerID = $abscence_id1; // for client bank details table
   $user_id = $abscence_id1;

    $total_rows=0;
        $uploadedStatus = 0;
        $message = ""; $mfMessage = ""; $insertRow = true;
        $mf_data = array();
        $pip_data = array();
        $val_data = null;
        //variable to delete previous policy records for fund options
        $delPolicyNum = "";
        $delFundOption = "";
        if (isset($tflag))
        {
            //$transactionType = $this->input->post('transaction_type');
            $transactionType = "";
            if (isset($tfile))
            {
                //if there was an error uploading the file
                if ($tfile == '')
                {
                    $message = "No file selected";
                }
                else
                {
                    try {
                    //get tmp_name of file
                    $tfile = $tfile;
                  //  echo "Name FIle:=".$tfile;

             //  $fullpath='downloads/Folio_master_list/cam_folder/'.$file;
               $tfileType_info = $this->mime_type($tfile);
            //  echo $tfileType_info;exit;
               if($tfileType_info=="application/x-dbf"||$tfileType_info=="application/octet-stream"){  //for DBF file
                 $dbf = new dbf_class($tfile );
                 if($dbf=='failed'){ //dbf file  size may be zero
                          continue;
                      }
                 $num_rec=$dbf->dbf_num_rec;
                 $field_num=$dbf->dbf_num_field;
              //  echo $field_num;
                //echo $num_rec;//exit;
                 $excelData=array(array());
                 $total_rows=$num_rec;
                 if($total_rows>0){
                   for($j=0; $j<$field_num; $j++){
                         array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                   }
                   for($i=0; $i<$num_rec; $i++){
                     $temper=array();
                       if ($row = $dbf->getRow($i)) {

                         for($j=0; $j<$field_num; $j++){
                             array_push($temper,$row[$j]);
                         }

                       }
                       array_push($excelData, $temper);//array of all rows
                   }
                 }

            //echo"<pre>";print_r($excelData);
              //exit;
           } elseif($tfileType_info=="application/excel"||$tfileType_info=="application/vnd.ms-excel"||$tfileType_info=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                    echo"no action for excel file";
                   // continue;//no import for excel
            //  //load the excel library
            //  $this->load->library('Excel');
            //  //read file from path
            //  $objPHPExcel = PHPExcel_IOFactory::load($tfile);
            //  //get only the Cell Collection
            //  //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            //  $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
            //  $total_rows=$maxCell;
            // // var_dump($maxCell);
            // //get data from excel using range
            // $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
            // //var_dump($excelData);
            // //die();
            
           }
//echo"<pre>";print_r($excelData);exit;
                    //temp variables to hold values
                    $folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                    $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                    $adj_flag = 0; $tempMFType = "";
					$amc = ""; $arn = ""; $sub_arn = ""; $cheque_no = ""; $cheque_bank = ""; $account_no = ""; $ref_no = ""; $rej_ref_no = ""; $trxn_mode = "";
                    //$brokerID = $this->session->userdata('broker_id');
                  //  $user_id = $this->session->userdata('user_id');
                    $mfErrorCol="";

                    //stores column names
                    $dataColumns = array();
                    //stores row data
                    $dataRows = array(); $remRows = array();
                    $countRow = 0; $countErrRow = 0; $countMF = 0; $countRem = 0; $countTrans = 0;


                    //var_dump($excelData);
                    if($total_rows>0){
                      foreach($excelData as $rows)
                      {
                          $countCell = 0;
                          foreach($rows as $cell)
                          {
                              //var_dump($rows);
                              if($countRow == 0)
                              {
                                  $cell = str_replace(array('.'), '', $cell);
                                  if(strtoupper($cell) == 'SR NO' || strtoupper($cell) == 'TRXNNO' || strtoupper($cell) == 'TD_TRNO' || strtoupper($cell) == 'TRXN_NO' ||
                                      strtoupper($cell) == 'TRANSACTION NUMBER' ||
                                      strtoupper($cell) == 'GROUP NAME' ||
                                      strtoupper($cell) == 'INVESTOR NAME' ||
                                      strtoupper($cell) == 'PAN NO' || strtoupper($cell) == 'PAN' || strtoupper($cell) == 'PAN1' || strtoupper($cell) == 'IT_PAN_NO1' ||
                                      strtoupper($cell) == 'TRN TYPE' || strtoupper($cell) == 'TRXNTYPE' || strtoupper($cell) == 'TD_TRTYPE' || strtoupper($cell) == 'TR_TYPE' || strtoupper($cell) == 'TRANSACTION TYPE' || strtoupper($cell) == 'TRXN_TYPE' ||
                                      strtoupper($cell) == 'DATE' || strtoupper($cell) == 'TRADDATE' || strtoupper($cell) == 'CRDATE' || strtoupper($cell) == 'TRXN_DATE' || strtoupper($cell) == 'NAVDATE' || strtoupper($cell) == 'TRANSACTION DATE' || strtoupper($cell) == 'TD_TRDT' ||
                                      strtoupper($cell) == 'FOLIO NO' || strtoupper($cell) == 'FOLIO_NO' || strtoupper($cell) == 'TD_ACNO' || strtoupper($cell) == 'FOLIO NUMBER' ||
                                      strtoupper($cell) == 'SCHEME NAME' || strtoupper($cell) == 'SCHEME' || strtoupper($cell) == 'FUNDDESC' || strtoupper($cell) == 'PROD_CODE' || strtoupper($cell) == 'FUND DESCRIPTION' || strtoupper($cell) == 'SCHEME_NAME' ||
                                      strtoupper($cell) == 'PRODCODE' || strtoupper($cell) == 'PRODUCT_CODE' || strtoupper($cell) == 'PRODUCT_CO' || strtoupper($cell) == 'FMCODE' || strtoupper($cell) == 'SCHEME_CO0' || strtoupper($cell) == 'PRODUCT CODE' ||
                                      strtoupper($cell) == 'NAV' || strtoupper($cell) == 'PURPRICE' || strtoupper($cell) == 'TD_POP' || strtoupper($cell) == 'PRICE' ||
                                      strtoupper($cell) == 'UNIT' || strtoupper($cell) == 'UNITS' || strtoupper($cell) == 'TD_UNITS' ||
                                      strtoupper($cell) == 'AMOUNT' || strtoupper($cell) == 'TD_AMT' ||
                                      strtoupper($cell) == 'DPO PER UNIT' ||
                                      strtoupper($cell) == 'ADJUSTMENT' ||
                                      strtoupper($cell) == 'ADJUSTMENT REF NO'
  									|| strtoupper($cell) == 'AMC NAME' || strtoupper($cell) == 'TD_FUND' || strtoupper($cell) == 'FUND' || strtoupper($cell) == 'AMC_CODE' || strtoupper($cell) == 'COMP_CODE'
  									|| strtoupper($cell) == 'ARN' || strtoupper($cell) == 'TD_AGENT' || strtoupper($cell) == 'AGENT CODE' || strtoupper($cell) == 'BROKCODE' || strtoupper($cell) == 'BROK_CODE'
  									|| strtoupper($cell) == 'SUB ARN' || strtoupper($cell) == 'TD_BROKER' || strtoupper($cell) == 'SUB-BROKER CODE' || strtoupper($cell) == 'SUBBROK' || strtoupper($cell) == 'SUB_BROKE5'
  									|| strtoupper($cell) == 'CHEQUE NO' || strtoupper($cell) == 'CHQNO' || strtoupper($cell) == 'INSTRUMENT NUMBER' || strtoupper($cell) == 'MICR_NO' || strtoupper($cell) == 'CHECK_NO'
  									|| strtoupper($cell) == 'CHEQUE BANK' || strtoupper($cell) == 'CHQBANK' || strtoupper($cell) == 'INSTRUMENT BANK' || strtoupper($cell) == 'BANK_NAME' || strtoupper($cell) == 'PBANK_NAME'
  									|| strtoupper($cell) == 'BANK A/C NO' || strtoupper($cell) == 'AC_NO' || strtoupper($cell) == 'PERSONAL23'
  									|| strtoupper($cell) == 'REF NO' || strtoupper($cell) == 'USRTRXNO'
  									|| strtoupper($cell) == 'REJ REF NO' || strtoupper($cell) == 'REJTRNOOR2' || strtoupper($cell) == 'REJTRNOORGNO'
  									|| strtoupper($cell) == 'TRXN MODE' || strtoupper($cell) == 'TRXNMODE' )
                                  {
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      $uploadedStatus = 2;
                                      //echo $countCell-'head';
                                      //var_dump($dataColumns[$countCell]);
                                      continue;
                                  }
                                  else
                                  {
                                      $dataColumns[$countCell] = $cell;
                                      $countCell++;
                                      /*$message = 'Columns Specified in Excel is not in correct format';
                                      $uploadedStatus = 0;
                                      break;*/
                                      continue;
                                  }
                              }
                              else
                              {
                                  //var_dump($countCell);
                                  //echo $countCell.'-body';
                                  //echo '<br/><br/><br/><br/><br/><br/>Salmaan<br/><br/><br/><br/><br/><br/>';
                                  //var_dump($dataColumns[$countCell]);

                                  if($insertRow)
                                  {
                                      if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $srNo = trim($cell);
                                          }
                                          else
                                          {
                                              $srNo = "";
                                              /*$insertRow = false;
                                              $mfMessage = "Pan Number cannot be empty";*/
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'ARN' || strtoupper($dataColumns[$countCell]) === 'TD_AGENT' || strtoupper($dataColumns[$countCell]) === 'AGENT CODE' || strtoupper($dataColumns[$countCell]) === 'BROKCODE' || strtoupper($dataColumns[$countCell]) === 'BROK_CODE')
                                                                {
                                                                    if($cell || $cell != ''){
                                                                      $arn = trim($cell);

                                                                    //   //@pallavi: get brokerID and status from the user arn mapping table
                                                                   //    $user=$this->Clients_model->get_userdata($arn);
                                                                    // //  echo"<pre>";print_r($user);exit;
                                                                    //   if(count($user)>0){
                                                                    //     if($user[0]['status']==1 && $user[0]['user_type']=='broker'){
                                                                    //       $brokerID=$user[0]['id'];  // for client bank details table
                                                                    //       $user_id=$user[0]['id'];  //for clients table
                                                                    //     }
                                                                    //
                                                                    //   }
                                                                  //  $user_id = $abscence_id1;
                                                                  //  $brokerID = $abscence_id1;
                                                                    //   if(count($user)>0){
                                                                    //     if($user[0]['status']==1 && $user[0]['user_type']=='broker'){
                                                                    //       if($abscence_id1!=$user[0]['id'])
                                                                    //       $brokerID=$user[0]['id'];  // for client bank details table
                                                                    //       $user_id=$user[0]['id'];  //for clients table
                                                                    //     }else{
                                                                    //       $user_id = $abscence_id1;
                                                                    //       $brokerID = $abscence_id1;
                                                                    //     }

                                                                    //   }

                                                                    } else{
                                                                        $arn = "";
                                                                        $insertRow = false;
                                                                        $mfMessage = "Broker ARN number can not be empty";
                                                                        $mfErrorCol='ARN Number Col';
                                                                    }



                                                                }

                                      else if(strtoupper($dataColumns[$countCell]) == 'SCHEME NAME' || strtoupper($dataColumns[$countCell]) == 'SCHEME' ||
                                          strtoupper($dataColumns[$countCell]) == 'FUNDDESC' || strtoupper($dataColumns[$countCell]) == 'PROD_CODE' ||
                                          strtoupper($dataColumns[$countCell]) == 'FUND DESCRIPTION' || strtoupper($dataColumns[$countCell]) == 'SCHEME_NAME')
                                      {
                                          /*if($cell || $cell != '')
                                          {
                                              $schemeName = trim($cell);
                                              $whereScheme = 'scheme_name = "'.$schemeName.'" AND scheme_status = 1';
                                              $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                              if(count($scheme_details) == 0)
                                              {
                                                  /*$mfMessage = 'Scheme '.$schemeName." Doesn't Exists or is Inactive";
                                                  $insertRow = false;*/
                                              /*}
                                              else
                                              {
                                                  $schemeId = $scheme_details[0]->scheme_id;
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Scheme cannot be empty";
                                          }*/
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                          strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' ||
                                          strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                          strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $prodCode = trim($cell);
                                              $whereScheme = 'prod_code = "'.$prodCode.'" AND scheme_status = 1';
                                              $scheme_details = $this->mf->get_mf_schemes_broker_dropdown($whereScheme);
                                              if(count($scheme_details) == 0)
                                              {
                                                  $mfMessage = 'Product Code (scheme) '.$prodCode." Doesn't Exists or is Inactive";
                                                  $insertRow = false;
                                                  $mfErrorCol='product_id';
                                              }
                                              else
                                              {
                                                  $schemeId = $scheme_details[0]->scheme_id;
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Scheme/Product Code cannot be empty";
                                              $mfErrorCol='product_id';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $folioNum = $cell;
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Folio Number cannot be empty";
                                              $mfErrorCol='folio_number';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                          strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $panNum = trim($cell);
                                              $wherePan = array(
                                                  'c.pan_no'=>$panNum,
                                                  'f.broker_id'=>$brokerID
                                              );
                                              //checks if pan exists in clients table
                                              $c_info = $this->Clients_model->get_client_family_by_pan($wherePan);
                                              if(count($c_info) == 0)
                                              {
                                                  $insertRow = false;
                                                  $mfMessage = "Pan No. ".$panNum." Doesn't exists for any client";
                                                  $mfErrorCol='pan_no';
                                              }
                                              else
                                              {
                                                    if($c_info->merge_ref_id!='')
                                                    {
                                                        $c_info1 = $this->client->get_client_info($c_info->merge_ref_id);
                                                        $clientId = $c_info1->client_id;
                                                        $clientName = $c_info1->name;
                                                        $familyId = $c_info1->family_id;
                                                    }
                                                    else
                                                    {
                                                        $clientId = $c_info->client_id;
                                                        $clientName = $c_info->client_name;
                                                        $familyId = $c_info->family_id;
                                                    }
                                                  
                                              }
                                          }
                                          else
                                          {
                                            //@pallavi//
                                                $wherePan = array(
                                                    'cb.productId'=>$prodCode,
                                                    'cb.folio_number'=>$folioNum,
                                                    'f.broker_id'=>$brokerID
                                                );
                                            $c_info1 = $this->Clients_model->get_client_family_by_withoutpan($wherePan);

                                            //below code modified by Salmaan - 2017-05-26
                                            if(count($c_info1) == 0)
                                            {
                                              $insertRow = false;
                                              $mfMessage = "Transaction information not related to any Client's History";
                                              $mfErrorCol='pan_no';
                                            }
                                            else
                                            {
                                                //now we have to fetch the ref_client_id if already merged
                                                $c_info2 = $this->Clients_model->get_client_family_merge_ref(array('c.client_id'=>$c_info1->client_id));
                                                if(count($c_info2) == 0) {
                                                  $clientId = $c_info1->client_id;
                                                  $clientName = $c_info1->client_name;
                                                  $familyId = $c_info1->family_id;
                                                } else {
                                                  if(isset($c_info2) && !empty($c_info2)) {
                                                    $clientId = $c_info2[0]['client_id'];
                                                    $clientName = $c_info2[0]['name'];
                                                    $familyId = $c_info2[0]['family_id'];
                                                  }
                                                }
                                            }

                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                          strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $mfType = trim(strtoupper($cell));
                                              $tempMFType = $mfType;
                                              if($mfType == "DIV" || $mfType == "DR" || $mfType == "DIR" || $mfType == "BNS" || $mfType == "BNSR" || $mfType == "DIRR") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "DIV";
                                              } elseif($mfType == "NFO") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "NFO";
                                              } elseif($mfType == "PIP" || $mfType == "SIN" || $mfType == "NEW" || $mfType == "ADD" || $mfType == "P" ||
                                                      $mfType == "ADDPUR" || $mfType == "NEWPUR" || $mfType == "SIP" || $mfType == "ADDR" ||
                                                      $mfType == "ADDRR" || $mfType == "NEWR" || $mfType == "SINR" || $mfType == "SINRR" ||
                                                      $mfType == "ADDPURR" || $mfType == "NEWPURR" || $mfType == "PURR" || $mfType == "SIPR" ||
                                                      $mfType == "CNI" || $mfType == "CNIR" || $mfType == "UPLO" || $mfType == "UPLOR" || $mfType == "BON" ||
                                                      $mfType == "BONR") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "PIP";
                                              } elseif($mfType == "IPO" || $mfType == "IPOR" || $mfType == "IPORR" || $mfType == "TRFI" || $mfType == "TRFIR") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "IPO";
                                              } elseif($mfType == "SWI" || $mfType == "SI" || $mfType == "STPA" || $mfType == "LTIA" ||
                                                      $mfType == "STPI" || $mfType == "SWIN" || $mfType == "DSPI" || $mfType == "DTPIN" ||
                                                      $mfType == "LTIN" || $mfType == "STRA" || $mfType == "DSPIR" || $mfType == "LTIAR" ||
                                                      $mfType == "LTIARR" || $mfType == "LTINR" || $mfType == "STPAR" || $mfType == "SWIA" ||
                                                      $mfType == "TMI" || $mfType == "TRMI" || $mfType == "SWINR" || $mfType == "DSPA" ||
                                                      $mfType == "DSPN" || $mfType == "STPN" || $mfType == "STPNR" || $mfType == "STRAR" ||
                                                      $mfType == "STRI" || $mfType == "STRIR" || $mfType == "SWIAR" || $mfType == "TMIR") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "SWI";
                                              } elseif($mfType == "TIN" || $mfType == "TI") {
                                                  $transactionType = "Purchase";
                                                  $mfType = "TIN";
                                              } elseif($mfType == "SWO" || $mfType == "SO" || $mfType == "STPO" || $mfType == "LTOP" ||
                                                      $mfType == "DSPO" || $mfType == "LTOF" || $mfType == "STRO" || $mfType == "LTOFR" ||
                                                      $mfType == "SWOF" || $mfType == "TMO" || $mfType == "TRMO" || $mfType == "DSPOR" ||
                                                      $mfType == "SWOFR" || $mfType == "LTOPR" || $mfType == "STROR" || $mfType == "SWOP" ||
                                                      $mfType == "SWOPR" || $mfType == "STPOR" || $mfType == "TRMOR") {
                                                  $transactionType = "Redemption";
                                                  $mfType = "SWO";
                                              } elseif($mfType == "DP" || $mfType == "DIVIDEND PAYOUT" || $mfType == "DIVR" || $mfType == "DPR") {
                                                  $transactionType = "Redemption";
                                                  $mfType = "DP";
                                              } elseif($mfType == "RED" || $mfType == "FUL" || $mfType == "FULR" || $mfType == "FULRR" || $mfType == "REDR" ||
                                                  $mfType == "R" || $mfType == "TO" || $mfType == "TOCOB" || $mfType == "CNO" || $mfType == "CNOR" ||
                                                  $mfType == "SWD" || $mfType == "SWDR" || $mfType == "TRFO" || $mfType == "TRFOR" || $mfType == "TRMO" ||
                                                  $mfType == "CFI" || $mfType == "CFIR" || $mfType == "DMT" || $mfType == "DMTR") {
                                                  $transactionType = "Redemption";
                                                  $mfType = "RED";
                                              } else {
                                                  //it might be CAMS file, which has trn_type with numbers, so check that
                                                  if(strpos($mfType,"P") === 0) {
                                                      $transactionType = "Purchase";
                                                      $mfType = "PIP";
                                                  } elseif(strpos($mfType,"DR") === 0) {
                                                      $transactionType = "Purchase";
                                                      $mfType = "DIV";
                                                  } elseif(strpos($mfType,"SI") === 0) {
                                                      $transactionType = "Purchase";
                                                      $mfType = "SWI";
                                                  } elseif(strpos($mfType,"TI") === 0) {
                                                      $transactionType = "Purchase";
                                                      $mfType = "TIN";
                                                  } elseif(strpos($mfType,"R") === 0 || strpos($mfType,"TO") === 0) {
                                                      $transactionType = "Redemption";
                                                      $mfType = "RED";
                                                  } elseif(strpos($mfType,"DP") === 0) {
                                                      $transactionType = "Redemption";
                                                      $mfType = "DP";
                                                  } elseif(strpos($mfType,"SO") === 0) {
                                                      $transactionType = "Redemption";
                                                      $mfType = "SWO";
                                                  } else {
                                                      //maybe not CAMS file, Transaction Type is out-of-this-world, so show error
                                                      $transactionType = "Unknown";
                                                  }
                                              }

                                              $where = array('mutual_fund_type' => $mfType, 'use_for' => $transactionType);
                                              $trn_details = $this->mf->get_mf_types_broker_dropdown($where);
                                              if(count($trn_details) == 0)
                                              {
                                                  $mfMessage = 'Trn Type '.$mfType." doesn't exist for ".$transactionType;
                                                  $insertRow = false;
                                                  $mfErrorCol='transaction_type';
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Trn Type cannot be empty";
                                                $mfErrorCol='transaction_type';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'DATE' || strtoupper($dataColumns[$countCell]) === 'TRADDATE' ||
                                          strtoupper($dataColumns[$countCell]) === 'TRXN_DATE' || strtoupper($dataColumns[$countCell]) === 'NAVDATE' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_TRDT' || strtoupper($dataColumns[$countCell]) == 'TRANSACTION DATE')
                                       {
                                        $cell=trim(str_replace('/','-', $cell));
                                    // echo $cell; //exit; //date checked
                                      if($cell || $cell != '')
                                      {
                                       $date = DateTime::createFromFormat('m-d-y', $cell);
                                          //var_dump($cell);exit;
                                          //$date->format('Y-m-d');
                                          if(is_object($date)){
                                            $purDate=$date->format('Y-m-d');
                                          }else{
                                            $date = new DateTime($cell);
                                             if(is_object($date)){
                                               $purDate=$date->format('Y-m-d');
                                             }else{

                                                     $insertRow = false;
                                                     $mfMessage = "Date format is not proper (should be dd/mm/yyyy)";
                                                       $mfErrorCol='transaction_date';
                                                 }

                                             }

                                          } else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Date cannot be empty";
                                              $mfErrorCol='transaction_date';
                                          }
                                          //echo $purDate;exit;
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'NAV' || strtoupper($dataColumns[$countCell]) === 'PURPRICE' ||
                                          strtoupper($dataColumns[$countCell]) === 'TD_POP' || strtoupper($dataColumns[$countCell]) === 'PRICE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $nav = $cell;
                                          }
                                          else
                                          {
                                              //$insertRow = false;
                                              //$mfMessage = "NAV cannot be empty";
                                              $nav = 0;
                                          }
                                      }
                                      /*else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                          strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $unit = $cell;
                                              //Check if we need to make unit negative (in case of Franklin file)
                                              if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                  $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                  $tempMFType == "SWOFR") && ($unit > 0)) {
                                                  $unit = -$unit;
                                              }
                                          }
                                          else
                                          {
                                              //$insertRow = false;
                                              //$mfMessage = "Unit cannot be empty";
                                              $unit = 0;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $amount = $cell;
                                              //Check if we need to make amount negative (in case of Franklin file)
                                              if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                  $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                  $tempMFType == "SWOFR") && ($amount > 0)) {
                                                  $amount = -$amount;
                                              }
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Amount cannot be empty";
                                          }
                                      }*/
                                      else if(strtoupper($dataColumns[$countCell]) === 'UNIT' || strtoupper($dataColumns[$countCell]) === 'UNITS' ||
                                          strtoupper($dataColumns[$countCell]) === 'TD_UNITS')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $unit = floatval($cell);
                                              //Check if we need to make unit negative (in case of Franklin file)
                                              /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                  $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                  $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                                  $unit = -$unit;
                                              }*/
                                          }
                                          else
                                          {
                                              //$insertRow = false;
                                              //$mfMessage = "Unit cannot be empty";
                                              $unit = 0;
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'AMOUNT' || strtoupper($dataColumns[$countCell]) === 'TD_AMT')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $amount = floatval($cell);
                                              //Check if we need to make amount negative (in case of Franklin file)
                                              /*if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                                  $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                                  $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($amount > 0)) {
                                                  $amount = -$amount;
                                              }*/
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Amount cannot be empty";
                                              $mfErrorCol='amount';
                                          }
                                      }
                                      /*else if(strtoupper($dataColumns[$countCell]) === 'BAL UNIT')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $balUnit = $cell;
                                          }
                                          else
                                          {
                                              $balUnit = '0';
                                          }
                                      }*/
                                      else if(strtoupper($dataColumns[$countCell]) === 'DPO PER UNIT')
                                      {
                                          if($cell || $cell != '')
                                              $dpo = $cell;
                                          else
                                              $dpo = 0;

                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $adj = $cell;
                                              $adj_flag = 1;
                                          }
                                          else
                                          {
                                              $adj = "";
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'ADJUSTMENT REF NO')
                                      {
                                          if($cell || $cell != '')
                                              $adjRefNum = $cell;
                                          else
                                              $adjRefNum = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'AMC NAME' || strtoupper($dataColumns[$countCell]) === 'TD_FUND' || strtoupper($dataColumns[$countCell]) === 'FUND' || strtoupper($dataColumns[$countCell]) === 'AMC_CODE' || strtoupper($dataColumns[$countCell]) === 'COMP_CODE')
                                      {
                                          if($cell || $cell != '')
                                              $amc = trim($cell);
                                          else
                                              $amc = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'SUB ARN' || strtoupper($dataColumns[$countCell]) === 'TD_BROKER' || strtoupper($dataColumns[$countCell]) === 'SUB-BROKER CODE' || strtoupper($dataColumns[$countCell]) === 'SUBBROK' || strtoupper($dataColumns[$countCell]) === 'SUB_BROKE5')
                                      {
                                          if($cell || $cell != '')
                                              $sub_arn = trim($cell);
                                          else
                                              $sub_arn = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE NO' || strtoupper($dataColumns[$countCell]) === 'CHQNO' || strtoupper($dataColumns[$countCell]) === 'INSTRUMENT NUMBER' || strtoupper($dataColumns[$countCell]) === 'MICR_NO' || strtoupper($dataColumns[$countCell]) === 'CHECK_NO')
                                      {
                                          if($cell || $cell != '')
                                              $cheque_no = trim($cell);
                                          else
                                              $cheque_no = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'CHEQUE BANK' || strtoupper($dataColumns[$countCell]) === 'CHQBANK' || strtoupper($dataColumns[$countCell]) === 'INSTRUMENT BANK' || strtoupper($dataColumns[$countCell]) === 'BANK_NAME' || strtoupper($dataColumns[$countCell]) === 'PBANK_NAME')
                                      {
                                          if($cell || $cell != '')
                                              $cheque_bank = trim($cell);
                                          else
                                              $cheque_bank = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'BANK A/C NO' || strtoupper($dataColumns[$countCell]) === 'AC_NO' || strtoupper($dataColumns[$countCell]) === 'PERSONAL23')
                                      {
                                          if($cell || $cell != '')
                                              $account_no = trim($cell);
                                          else
                                              $account_no = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'REF NO' || strtoupper($dataColumns[$countCell]) === 'USRTRXNO')
                                      {
                                          if($cell || $cell != '')
                                              $ref_no = trim($cell);
                                          else
                                              $ref_no = "";
                                      }
  					else if(strtoupper($dataColumns[$countCell]) === 'REJ REF NO' || strtoupper($dataColumns[$countCell]) === 'REJTRNOOR2' || strtoupper($dataColumns[$countCell]) === 'REJTRNOORGNO')
                                      {
                                          if($cell || $cell != '')
                                              $rej_ref_no = trim($cell);
                                          else
                                              $rej_ref_no = "";
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) === 'TRXN MODE' || strtoupper($dataColumns[$countCell]) === 'TRXNMODE')
                                      {
                                          if($cell || $cell != '')
                                              $trxn_mode = trim($cell);
                                          else
                                              $trxn_mode = "";
                                      }
                                  } else {
                                  	if(strtoupper($dataColumns[$countCell]) == 'SR NO' || strtoupper($dataColumns[$countCell]) == 'TRXNNO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_TRNO' || strtoupper($dataColumns[$countCell]) == 'TRXN_NO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TRANSACTION NUMBER')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $srNo = trim($cell);
                                          }
                                          else
                                          {
                                              $srNo = "";
                                              /*$insertRow = false;
                                              $mfMessage = "Pan Number cannot be empty";*/
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'PAN NO' || strtoupper($dataColumns[$countCell]) == 'PAN' || strtoupper($dataColumns[$countCell]) == 'PAN1' ||
                                          strtoupper($dataColumns[$countCell]) == 'IT_PAN_NO1')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $panNum = $cell;
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Pan Number cannot be empty";
                                                $mfErrorCol='pan_no';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'FOLIO NO' || strtoupper($dataColumns[$countCell]) == 'FOLIO_NO' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_ACNO' || strtoupper($dataColumns[$countCell]) == 'FOLIO NUMBER')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $folioNum = $cell;
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Folio Number cannot be empty";
                                                $mfErrorCol='folio_number';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'PRODCODE' || strtoupper($dataColumns[$countCell]) == 'PRODUCT_CODE' ||
                                          strtoupper($dataColumns[$countCell]) == 'PRODUCT_CO' || strtoupper($dataColumns[$countCell]) == 'SCHEME CODE' ||
                                          strtoupper($dataColumns[$countCell]) == 'FMCODE' || strtoupper($dataColumns[$countCell]) == 'SCHEME_CO0' ||
                                          strtoupper($dataColumns[$countCell]) == 'PRODUCT CODE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $prodCode = trim($cell);
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Scheme/Product Code cannot be empty";
                                              $mfErrorCol='product_id';
                                          }
                                      }
                                      else if(strtoupper($dataColumns[$countCell]) == 'TRN TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXNTYPE' ||
                                          strtoupper($dataColumns[$countCell]) == 'TD_TRTYPE' || strtoupper($dataColumns[$countCell]) == 'TR_TYPE' ||
                                          strtoupper($dataColumns[$countCell]) == 'TRANSACTION TYPE' || strtoupper($dataColumns[$countCell]) == 'TRXN_TYPE')
                                      {
                                          if($cell || $cell != '')
                                          {
                                              $mfType = trim(strtoupper($cell));
                                              $tempMFType = $mfType;
                                          }
                                          else
                                          {
                                              $insertRow = false;
                                              $mfMessage = "Trn Type cannot be empty";
                                              $mfErrorCol='transaction_type';
                                          }
                                      }
                                  }
                                  $countCell++;
                              }
                          }
                          if($countRow != 0)
                          {
                              if(!$insertRow)
                              {
                                    $mf_data[$countErrRow]['client_id'] = $clientId;
                                    $mf_data[$countErrRow]['family_id'] = $familyId;
                                    $mf_data[$countErrRow]['transaction_date'] = date('Y-m-d');
                                    $mf_data[$countErrRow]['mutual_fund_scheme'] = $schemeId;
                                    $mf_data[$countErrRow]['mutual_fund_type'] = $mfType;
                                    $mf_data[$countErrRow]['transaction_type'] = $transactionType;
                                    $mf_data[$countErrRow]['folio_number'] = $folioNum;
                                    $mf_data[$countErrRow]['purchase_date'] = $purDate;
                                    $mf_data[$countErrRow]['quantity'] = $unit;
                                    $mf_data[$countErrRow]['nav'] = $nav;
                                    $mf_data[$countErrRow]['amount'] = $amount;
                                    $mf_data[$countErrRow]['adjustment_flag'] = $adj_flag;
                                    $mf_data[$countErrRow]['adjustment'] = $adj;
                                    $mf_data[$countErrRow]['adjustment_ref_number'] = $adjRefNum;
                                    $mf_data[$countErrRow]['DPO_units'] = $dpo;
                                    $mf_data[$countErrRow]['orig_trxn_no'] = $srNo;
                                    $mf_data[$countErrRow]['orig_trxn_type'] = $tempMFType;
                                    $mf_data[$countErrRow]['trxn_mode'] = $trxn_mode;
                                    $mf_data[$countErrRow]['ref_no'] = $ref_no;
                                    $mf_data[$countErrRow]['rej_ref_no'] = $rej_ref_no;
                                    $mf_data[$countErrRow]['amc_name'] = $amc;
                                    $mf_data[$countErrRow]['arn'] = $arn;
                                    $mf_data[$countErrRow]['sub_arn'] = $sub_arn;
                                      $mf_data[$countErrRow]['pan_no'] = $panNum;
                                      $mf_data[$countErrRow]['product_id'] = $prodCode;
                                    $mf_data[$countErrRow]['broker_id'] = $brokerID;
                                      $mf_data[$countErrRow]['user_id'] = $user_id;
                                      $mf_data[$countErrRow]['error_msg'] = $mfMessage;
                                      $mf_data[$countErrRow]['error_col_list'] = $mfErrorCol;//implode(",",$mfErrorCol);
                                      $mf_data[$countErrRow]['file_name'] = $tfile;//implode(",",$mfErrorCol);
                                      $mf_data[$countErrRow]['rta_type'] ='CAMS';
                                        $mf_data[$countErrRow]['file_download_date'] = date('Y-m-d');//$tsearchTemp->format('Y-m-d');;//implode(",",$mfErrorCol);

                                  // $mf_data[$countErrRow][1] = $prodCode;
                                  // $mf_data[$countErrRow][2] = $folioNum;
                                  // $mf_data[$countErrRow][3] = $tempMFType;
                                  // $mf_data[$countErrRow][4] = $srNo;
                                  // $mf_data[$countErrRow][5] = $panNum;
                                  // $mf_data[$countErrRow][6] = $mfMessage;



                                  $countErrRow++;
                                  $insertRow = true;
                                  $uploadedStatus = 2;
                                  continue;
                              }

                              //check if trn_type was DIV, because Karvy has named DP as DIV, so we'll check its units
                              if($mfType == "DIV" && floatval($unit) == 0) {
                                  //change type to DP
                                  $mfType = "DP";
                                  $transactionType = "Redemption";
                              }

                              $pip_data = array();

                              if(strtoupper($mfType) == 'PIP' || strtoupper($mfType) == 'IPO')
                              {
                                  /*$pip_data[$countTrans] = array(
                                      /*'action' => '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Add Bank Details"
                                          onclick="add_bank_details('."'".$clientId."', "."'".$folioNum."', "."'".$schemeId."', "."'".$mfType."'".')">
                                          <i class="fa fa-plus"></i> Add Bank Detail</a>',*/ /*
                                      'client_name' => $clientName, 'prod_code' => $prodCode, 'folio_number' => $folioNum, 'trans_type' => $mfType,
                                      'pan_no' => $panNum, 'sr_no' => $srNo
                                  );
                                  $countTrans++;*/
                              }

                              /*if(strtoupper($mfType) == 'DP' && floatval($dpo) == 0) {
                                  $dpo = $amount/$unit;
                              }*/

                              //Check if we need to make amount negative (in case of Franklin file)
                              if(($tempMFType == "ADDPURR" || $tempMFType == "NEWPURR" || $tempMFType == "PURR" ||
                                  $tempMFType == "SIPR" || $tempMFType == "REDR" || $tempMFType == "SWINR" ||
                                  $tempMFType == "SWOFR" || $tempMFType == "DIRR") && ($unit > 0)) {
                                  $unit = -($unit);
                                  $amount = -($amount);
                              }

                              /*if($balUnit == '0' || empty($balUnit)) {
                                  $balUnit = $tempMFType;
                              }
                              if(!empty($srNo) && empty($adjRefNum)) {
                                  $adjRefNum = $srNo;
                              }*/

                              /* in case of Karvy, and maybe FT, if ref_no is not available, use trxn_no($srNo) */
                              if($ref_no == "") {
                              	$ref_no = $srNo;
                              }

  				// to check whether to add rej_ref_no in table or not
  				if($unit < 0) {
  					if($rej_ref_no != "") {
  						$rej_ref_no = $rej_ref_no;
  					} else {
  						$rej_ref_no = $ref_no;
  					}
  				} else {
  					$rej_ref_no = "";
  				}


                              $dataRows[$countMF] = array(
                                  'client_id' => $clientId, 'family_id' => $familyId,  'transaction_date' => date('Y-m-d'),
                                  'mutual_fund_scheme' => $schemeId, 'mutual_fund_type' => $mfType, 'transaction_type' => $transactionType,
                                  'folio_number' => $folioNum, 'purchase_date' => $purDate, 'quantity' => $unit, 'nav' => $nav, 'amount' => $amount,
                                  'adjustment_flag' => $adj_flag, 'adjustment' => $adj, 'adjustment_ref_number' => $adjRefNum,
                                  'orig_trxn_no' => $srNo, 'orig_trxn_type' => $tempMFType, 'trxn_mode' => $trxn_mode,
                                  'ref_no' => $ref_no, 'rej_ref_no' => $rej_ref_no, 'amc_name' => $amc, 'arn' => $arn, 'sub_arn' => $sub_arn,
                                  'DPO_units' => $dpo, 'user_id' => $user_id, 'broker_id' => $brokerID
                              );
                              //var_dump($dataRows[$countMF]);
                              $countMF++;

                              $today = new DateTime();
                              $interval = $date->diff($today);
                              $daydiff = $interval->format('%a');


                              //@pallavi $rem_red_amt calculation parent
                              //echo $rem_redemption_amt,'/';
                              $rem_details = $this->rem->get_reminder_days(array('broker_id' => $brokerID));
                              if($rem_details) {
                                  //var_dump($rem_details);
                                  $rem_redemption_amt = $rem_details[0]->mf_redemption_amount;
                                  $rem_dividend_amt = $rem_details[0]->mf_dpo_amount;
                              }
                              else
                              {
                                  $rem_redemption_amt =1;
                                  $rem_dividend_amt=1 ;
                              }
                              //echo $rem_redemption_amt,'<br/>';
                              $rem_red_amt = $rem_redemption_amt;
                              //echo 'rem',$rem_red_amt;

                              /**** end rem_red_amt   ****/


                              if(strtoupper($mfType) === 'RED' && ($amount >= $rem_red_amt) && $daydiff <= 15)
                              {
                                  //echo $rem_red_amt, '----', $amount,'<br/>';
                                  $rem_message = 'Redemption Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                  $countRem2 = array(
                                      'reminder_type'=>'MF Redemption',
                                      'client_id' => $clientId,
                                      'client_name' => $clientName,
                                      'reminder_date' => $purDate,
                                      'reminder_message' => $rem_message,
                                      'broker_id'=>$brokerID
                                  );

                                  $this->rem->add_reminder($countRem2);
                              }
                              else if(strtoupper($mfType) === 'DP' && ($amount >= $rem_dividend_amt) && $daydiff <= 15)
                              {
                                  $rem_message = 'Dividend Payout of '.$schemeName.', Folio No: '.$folioNum.' amounting Rs.'.round($amount).' will be credited to registered bank.';
                                  $countRem2 = array(
                                      'reminder_type'=>'MF Dividend Payout',
                                      'client_id' => $clientId,
                                      'client_name' => $clientName,
                                      'reminder_date' => $purDate,
                                      'reminder_message' => $rem_message,
                                      'broker_id'=>$brokerID
                                  );

                                  $this->rem->add_reminder($countRem2);
                              }

                             /* $remRows[$countRem] = array(
                                  'reminder_type'=>'MF Redemption',
                                  'client_id' => $clientId,
                                  'client_name' => $clientName,
                                  'reminder_date' => $purDate,
                                  'reminder_message' => $rem_message,
                                  'broker_id'=>$brokerID
                              );*/

                              //$this->rem->add_reminder($remRows[$countRem]);

                              $countRem++;
                              /*var_dump($dataRows);
                              var_dump($remRows);*/

                              $folioNum = ""; $clientName = ""; $panNum = ""; $mfType = ""; $purDate = ""; $unit = ""; $schemeName = ""; $prodCode = "";
                              $nav = ""; $adj = ""; $clientId = ""; $familyId = ""; $adjRefNum = ""; $balUnit = ""; $amount = ""; $dpo = 0; $schemeId="";
                              $adj_flag = 0; $tempMFType = "";
  							$amc = ""; $arn = ""; $sub_arn = ""; $cheque_no = ""; $cheque_bank = ""; $account_no = ""; $ref_no = ""; $rej_ref_no = ""; $trxn_mode = "";
                          }


                          if($uploadedStatus == 0)
                              break;
                          $countRow++;
                      }

                      //at the end of all record reading insert the error record in the error handling table *********/
                    //  echo"<pre>";print_r($mf_data);
                      // if(isset($mf_data['folio_number'])){
                      //   $backup_error=$this->mf->add_auto_import_error($mf_data);
                      // }
                    //  echo"<pre>";print_r($mf_data);
                      $backup_error=$this->mf->add_auto_import_error($mf_data);
                    //  print_r($backup_error);exit;
                      //print_r($backup_error);exit;

                      /*get last transaction_id from mf_transactions*/
                      $trans = $this->mf->get_last_trans();
                      if(!($trans) || empty($trans) || empty($trans->transID)) {
                          $transID = 0;
                      } else {
                          $transID = ($trans->transID + 1);
                      }


                    }else{
                    //  echo"in not exceldata";
                    $mf_data['file_error']="error in file";
                    }

                    if($dataRows)
                    {
                        $countTrans = count($dataRows);
                      //  var_dump($dataRows);  //check mf insert @pallavi
                        /*$result = $this->mf->get_mutual_funds(array('transaction_date' => date('Y-m-d'), 'mf.broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        if($result)
                        {
                            $this->mf->delete_mutual_fund(array('transaction_date' => date('Y-m-d'), 'broker_id' => $brokerID, 'transaction_type' => $transactionType));
                        }*/

                        //we need to sort the data array before inserting - Salmaan - 27/10/2016
                        // Obtain a list of columns
                        foreach ($dataRows as $key => $row) {
                            $purchase_date[$key]  = $row['purchase_date'];
                            $trn_type[$key] = $row['transaction_type'];
                            $trxn_mode[$key] = $row['trxn_mode'];
                            $quantity[$key] = $row['quantity'];
                            $trn_no[$key] = $row['orig_trxn_no'];
                        }

                        // Sort the data with volume descending, edition ascending
                        array_multisort($purchase_date, SORT_ASC, $trn_type, SORT_ASC, $trxn_mode, SORT_ASC, $quantity, SORT_DESC, $trn_no, SORT_ASC, $dataRows);
                    // echo"<pre>";print_r($dataRows);exit;
                      $result = $this->mf->add_import_mutual_funds($dataRows, $transID); //of manual mf batch insert

                      //  $result = $this->mf->auto_add_import_mutual_funds($dataRows, $transID);
                        if(is_array($result)) {
                            $valuation_done = false;
                            $uploadedStatus = 2;
                            $message = "Mutual Fund Details imported! Please click on the Valuation button to continue processing your records.";
                        } else {
                            $valuation_done = true;
                            //var_dump($result);
                            $this->common->last_import('Mutual Fund Details', $brokerID, @$_FILES["import_mf"]["name"], $user_id);
                            $uploadedStatus = 1;
                            $message = "Mutual Fund Details Uploaded Successfully";
                        }
                    } else {
                        $countTrans = 0;
                    }
                    unset($dataColumns, $dataRows);
                } catch(Exception $e) {
                        //var_dump($e);
                    }
                }
            }
            else
            {
                $message = "No file selected";
            }



            if($uploadedStatus == 1)
            {
                //echo "<br/>Inside 1<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));

                $success = array(
                    "title" => "Success!",
                    "text" => $message
                );
                $this->session->set_userdata('success', $success);
				//call mf_valuation procedure
                /*if($countTrans > 9999) {
				    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => $transID
                    );
                }*/
            }
            else if ($uploadedStatus == 2)
            {
                //echo "<br/>Inside 2<br/>";
                $brokerID = $this->session->userdata('broker_id');
                //$this->mf->cal_mf_live_unit(array("brokerID"=>$brokerID));
                $info = array(
                    "title" => "Info for Import!",
                    "text" => 'Few Records were not imported please check the table below'
                );
                $this->session->set_userdata('info', $info);
				//call mf_valuation procedure
				//$this->mf->mf_valuation(array("brokerID"=>$brokerID));
                if(@$valuation_done == false) {
                    $val_data = array(
                        'btn' => true,
                        'brokerID' => $brokerID,
                        'transID' => @$transID
                    );
                }
            }
            else
            {
                //echo "<br/>Inside else<br/>";

                $error = array(
                    "title" => "Error on uploading!",
                    "text" => $message
                );
                $this->session->set_userdata('error', $error);
            }
        }
      //  $this->mutual_fund_import($mf_data, $pip_data, $val_data);
          // print_r($mf_data);exit;
      //  return $mf_data;
      unset($abscence_id1);
        return $tfile;
    }   //end of mf transaction import

    function sip_import($flag,$file,$abscence_id2){    //Sip import
      ini_set('max_execution_time', 0);
      ini_set('memory_limit', '2048M');
      ini_set('upload_max_filesize', '150M');
      ini_set('post_max_size', '150M');//50M
      //initailly set broker and user_id
        $brokerID = $abscence_id2; // for client bank details table
      $user_id = $abscence_id2;
      $uploadedStatus = 0;
      $message = ""; $sipMessage = ""; $sipErrorcol="";$insertRow = true;
      $sip_data = array();
      if (isset($flag))                               //mmif
      {

          if (isset($file))
          {                                                          //mif
          //  echo $_FILES["import_clients"]["type"];exit;

              //if there was an error uploading the file
              if ($file== '')
              {
                  $message = "No file selected ";
                    break;
                    return false;

              }
              else
              {                                            //melse
                  //get tmp_name of file
                  $tfile = $file;
                  //echo $tfile;

                //  $fullpath='downloads/Folio_master_list/cam_folder/'.$file;
                  $tfileType_info = $this->mime_type($tfile);
                  //echo $tfileType_info;

                  if($tfileType_info=="application/x-dbf"||$tfileType_info=="application/octet-stream"){  //for DBF file
                    $dbf = new dbf_class($tfile );
                    if($dbf=='failed'){ //dbf file  size may be zero
                          continue;
                      }
                    $num_rec=$dbf->dbf_num_rec;
                    $field_num=$dbf->dbf_num_field;
                 //  echo $field_num;
                   //echo $num_rec;//exit;
                    $excelData=array(array());
                    if($num_rec>0){
                      for($j=0; $j<$field_num; $j++){
                            array_push($excelData[0],$dbf->dbf_names[$j]['name']);

                      }
                      for($i=0; $i<$num_rec; $i++){
                        $temper=array();
                          if ($row = $dbf->getRow($i)) {

                            for($j=0; $j<$field_num; $j++){
                                array_push($temper,$row[$j]);
                            }

                          }
                          array_push($excelData, $temper);//array of all rows
                      }
                    }

               //echo"<pre>";print_r($excelData);
                 //exit;
              } elseif($tfileType_info=="application/excel"||$tfileType_info=="application/vnd.ms-excel"||$tfileType_info=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                            continue;//no import for excel
            //     //load the excel library
            //     $this->load->library('Excel');
            //     //read file from path
            //     $objPHPExcel = PHPExcel_IOFactory::load($tfile);
            //     //get only the Cell Collection
            //     //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            //     $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
            //   // var_dump($maxCell);
            //   //get data from excel using range
            //   $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
            //   //var_dump($excelData);
            //   //die();
              }

              $scheme_name="";
              $scheme_id="";
              $folio_id="";
              $installment_amt="";
              $added_on="";
              $Start_date="";
              $End_date="";
              $frequency="";
              $Client_id="";
              $account_no="";
    //pallavi:
              $bank="";$Bank_AccountNO="";$reg_date=null;$auto_amoun="";$auto_trno="";$client_id="";$end_date=null;$start_date=null;$auto_amoun="";
              $cease_date=null;

              //  $brokerID = $this->session->userdata('broker_id');
              //  $user_id = $this->session->userdata('user_id');
               //get data from excel using range
               //$excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
               //stores column names
               $dataColumns = array();
               //stores row data
               $dataRows = array();
               $countRow = 0; $countErrRow = 0; $add_FD_list = 0; $countRem = 0; $countTrans = 0;

               //check max row for client import limit

                 foreach($excelData as $rows)
                 {

                                       $countCell = 0;
                                       foreach($rows as $cell)
                                       {
                                          $cell;

                                           if($countRow == 0)
                                           {
                                               $cell = str_replace(array('.'), '', $cell);
                                               if(strtoupper($cell)=='PRODUCT'||strtoupper($cell)=='SCHEME'||strtoupper($cell)=='FOLIO_NO'||
                                               strtoupper($cell)=='INV_NAME'||strtoupper($cell)=='AUT_TRNTYP'||strtoupper($cell)=='AUTO_TRNO'||
                                               strtoupper($cell)=='AUTO_AMOUNT' || strtoupper($cell)=='AUTO_AMOUN' ||strtoupper($cell)=='FROM_DATE'||strtoupper($cell)=='TO_DATE'||
                                               strtoupper($cell)=='CEASE_DATE' || strtoupper($cell)=='PERIODICIT' || strtoupper($cell)=='PERIODICITY'||strtoupper($cell)=='PERIOD_DAY'||
                                               strtoupper($cell)=='INV_IIN' || strtoupper($cell)=='PAYMENT_MO' || strtoupper($cell)=='PAYMENT_MODE' || strtoupper($cell)=='TARGET_SCH'
                                               || strtoupper($cell)=='TARGET_SCHEME'||
                                               strtoupper($cell)=='REG_DATE'||strtoupper($cell)=='SUBBROKER'|| strtoupper($cell)=='REMARKS' ||
                                               strtoupper($cell)=='TOP_UP_FRQ' ||
                                               strtoupper($cell)=='TOP_UP_AMT' ||
                                               strtoupper($cell)=='AC_TYPE' ||
                                               strtoupper($cell)=='BANK' || strtoupper ($cell)=='BRANCH' || strtoupper($cell)=='INSTRM_NO'
                                               || strtoupper($cell)=='CHEQ_MICR_' || strtoupper($cell)=='CHEQ_MICR_NO' || strtoupper($cell)=='AC_HOLDER_'
                                               || strtoupper($cell)=='AC_HOLDER_NAME' || strtoupper($cell)=='PAN'
                                               || strtoupper($cell)=='TOP_UP_PER'
                                               || strtoupper($cell)=='TOP_UP_PERC' || strtoupper($cell)=='EUIN' || strtoupper($cell)=='SUB_ARN_CO' || strtoupper($cell)=='SUB_ARN_CODE'
                                               || strtoupper($cell)=='TER_LOCATI' || strtoupper($cell)=='TER_LOCATION' || strtoupper($cell)=='SCHEME_COD' || strtoupper($cell)=='TARGET_SCH')
                                                     {
                                                       $message='match';
                                                   $dataColumns[$countCell] = $cell;
                                                   $countCell++;
                                                   $uploadedStatus = 2;
                                                     continue;
                                                     //die();
                                               }
                                               else
                                               {
                                                   $message = 'Columns Specified in Excel is not in correct format';
                                                   $uploadedStatus = 0;
                                                   break;
                                                   //die();
                                               }
                                           }
                                           else
                                           {
                                            //echo"<pre>";print_r($dataColumns);

                                                 if($insertRow)
                                                 {


                                               if(strtoupper($dataColumns[$countCell]) === 'INSTRM_NO' || strtoupper($dataColumns[$countCell]) === 'INSTRM_NO')//installment_amt
                                                {
                                                      if($cell || $cell != '')
                                                      {
                                                           $Bank_AccountNO = $cell;
                                                      }
                                                      else
                                                      {
                                                          $Bank_AccountNO = $cell;
                                                      }


                                                  }


                                              elseif(strtoupper($dataColumns[$countCell]) === 'PRODUCT' ||strtoupper($dataColumns[$countCell]) === 'PRODUCT')//product_id
                                                         {

                                                               if($cell || $cell != '')
                                                               {
                                                                   $product_id = $cell;


                                                               }
                                                               else
                                                               {
                                                                   $insertRow = false;
                                                                   $sipMessage = "Scheme cannot be empty";
                                                                   $sipErrorcol="PRODUCT ID";
                                                               }

                                                         }

                                                         elseif(strtoupper($dataColumns[$countCell]) === 'FOLIO_NO' ||strtoupper($dataColumns[$countCell]) === 'FOLIO_NO')
                                                         {

                                                           if($cell || $cell != '')
                                                           {

                                                             $folio_id = $cell;

                                                           /****first fetch arn no,user_id,brokerID  based on folio number and scheme_id i.e.product id
                                                           from the client bank details table  =>pick arn number from it and fetch it's broker id from user_arn_mapping table****/

                                                          // echo $folio_id;
                                                           $client_arn=$this->Clients_model->get_broker_for_sip($folio_id,$product_id);
                                                           //echo"<pre>";print_r($arn);exit;
                                                           if(count($client_arn)>0){
                                                               if($client_arn[0]['status']==1 && $client_arn[0]['user_type']=='broker'){
                                                                 $brokerID=$client_arn[0]['broker_id'];  // for client bank details table
                                                                 $user_id=$client_arn[0]['broker_id'];  //for clients table
                                                             }
                                                           }else{
                                                             $insertRow = false;
                                                            $sipMessage = "Folio details not matching";
                                                            $sipErrorcol="FOLIO NO";

                                                           }
                                                             /*maturity table values  for sip rate*/
                                                             if(!$scheme_id=$this->sp->get_scheme_id($product_id)) {
                                                               $insertRow = false;
                                                              $sipMessage = "Scheme id Is Not Matching";
                                                              $sipErrorcol="PRODUCT ID";
                                                             }
                                                            // echo"<pre>";print_r($scheme_id);exit;
                                                             $scheme_type_id=$scheme_id->scheme_type_id;
                                                              $sc_type= $scheme_id->scheme_type;
                                                            if($sc_type =='EQUITY' || $sc_type =='ARBITRAGE' || $sc_type =='ELSS' || $sc_type =='ETF' || $sc_type =='FOF' || $sc_type =='GOLD FUND')
                                                             {
                                                                 $sc='equity';
                                                                 $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='equity'";
                                                                  $sip_rate=$this->sp->get_sip_rate($condition);

                                                                  if(isset($sip_rate) && !empty($sip_rate)) {
                                                                        $sip_rate=$sip_rate->rate;
                                                                    } else {
                                                                        $sip_rate=10;
                                                                    }


                                                             }
                                                             else if($sc_type == 'DEBT' || $sc_type=='FMP' || $sc_type='CAPITAL PROTECTION')
                                                             {
                                                                 $sc='debt';
                                                                 $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='debt'";
                                                                  $sip_rate=$this->sp->get_sip_rate($condition);
                                                                  if(isset($sip_rate) && !empty($sip_rate)) {
                                                                        $sip_rate=$sip_rate->rate;
                                                                    } else {
                                                                        $sip_rate=10;
                                                                    }
                                                             }
                                                             else if($sc_type=='MIP' || $sc_type=='BALANCED')
                                                             {
                                                                   $sc='hybrid';
                                                                   $condition = "broker_id ='".trim($this->session->userdata('broker_id'))."' AND `scheme_type` ='hybrid'";
                                                                   $sip_rate=$this->sp->get_sip_rate($condition);
                                                                   if(isset($sip_rate) && !empty($sip_rate)) {
                                                                        $sip_rate=$sip_rate->rate;
                                                                    } else {
                                                                        $sip_rate=10;
                                                                    }
                                                             }
                                                             else if($sc_type='')
                                                             {
                                                                 $sip_rate='10';
                                                             }



                                                              $scheme_id=$scheme_id->scheme_id;
                                                              settype($scheme_id,"integer");
                                                                /*end maturity table sip rate*/




                                                         } else
                                                           {
                                                             $insertRow = false;
                                                             $sipMessage="Folio Id cannot be empty";
                                                             $sipErrorcol="FOLIO NO";
                                                           }

                                                         }


                                                         else if(strtoupper($dataColumns[$countCell]) === 'BANK' || strtoupper($dataColumns[$countCell]) === 'ECSBANKNAM')//bank_id
                                                         {

                                                                 $bank = $cell;
                                                                 $bank_id='';
                                                         }


                                                           else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                                           {
                                                             if($cell || $cell != '')
                                                             {
                                                                   $PanNum = trim($cell);
                                                                   $whereClient = array('c.pan_no'=>$PanNum, 'f.broker_id'=>$brokerID);
                                                                    $c_info = $this->Clients_model->get_client_family_by_pan($whereClient);
                                                                   // print_r($c_info);
                                                                    if(count($c_info) == 0)
                                                                    {
                                                                        $insertRow = false;
                                                                        $sipMessage = "Client does not exist";
                                                                        $sipErrorcol="PAN NO";
                                                                     }
                                                                    else
                                                                    {
                                                                        $client_id = $c_info->client_id;
                                                                        $familyId = $c_info->family_id;
                                                                    }
                                                               }
                                                              else
                                                              {
                                                                  $wherePan = array(
                                                                    'cb.productId'=>$product_id,
                                                                    'cb.folio_number'=>$folio_id,
                                                                    'f.broker_id'=>$brokerID
                                                                  );
                                                                  //var_dump($wherePan);
                                                                  if(!$c_info1 = $this->Clients_model->get_client_family_by_withoutpan($wherePan))
                                                                  {

                                                                     //$nopan='set';
                                                                    $insertRow = false;
                                                                    $sipMessage = "Client does not exist";
                                                                    $sipErrorcol="pan no";
                                                                  }
                                                                  else
                                                                  {
                                                                    $client_id = $c_info1->client_id;
                                                                    $familyId = $c_info1->family_id;
                                                                  }

                                                               }

                                                             }
                                                             else if(strtoupper($dataColumns[$countCell]) === 'CEASE_DATE' || strtoupper($dataColumns[$countCell]) === 'CEASE_DATE')//added on date
                                                             {

                                                               /*if($cell || $cell != '')
                                                               {

                                                                     $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                         if(is_object($date)) {
                                                                              $cease_date = $date->format('Y-m-d');
                                                                         }else {
                                                                                $insertRow = false;
                                                                              $sipMessage = "Cease Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                         }
                                                               }
                                                               else
                                                               {
                                                                 $cease_date=null;

                                                               }*/
                                                               $cell=trim(str_replace('/','-',$cell));
                                                                 if($cell || $cell != '')
                                                                 {
                                                                     $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                     //var_dump($cell);exit;
                                                                     // $date->format('Y-m-d');
                                                                     if(is_object($date)){
                                                                         $cease_date=$date->format('Y-m-d');
                                                                     }
                                                                     else
                                                                     {
                                                                         $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                             $cease_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {

                                                                             $insertRow = false;
                                                                             $sipMessage = "Cease Date format is not proper (should be dd/mm/yyyy)";
                                                                             $sipErrorcol="CEASE DATE";
                                                                         }

                                                                     }

                                                                 }
                                                                 else
                                                                 {
                                                                    $cease_date=null;
                                                                 }

                                                               }
                                                               else if(strtoupper($dataColumns[$countCell]) === 'FROM_DATE' ||strtoupper($dataColumns[$countCell]) === 'STARTDATE')//start_data
                                                               {

                                                                  $cell=trim(str_replace('/','-',$cell));

                                                                 if($cell || $cell != '')
                                                                 {
                                                                     $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                     //var_dump($cell);exit;
                                                                     // $date->format('Y-m-d');
                                                                     if(is_object($date)){
                                                                         $start_date=$date->format('Y-m-d');

                                                                     }
                                                                     else
                                                                     {
                                                                         $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                            $start_date=$date->format('Y-m-d');


                                                                         }
                                                                         else
                                                                         {

                                                                             $insertRow = false;
                                                                             $sipMessage = "From Date format is not proper (should be dd/mm/yyyy)";
                                                                              $sipErrorcol="FROM DATE";
                                                                         }

                                                                     }

                                                                 }
                                                                 else
                                                                 {
                                                                   $insertRow = false;
                                                                   $sipMessage="From Date cannot be empty";
                                                                    $sipErrorcol="FROM DATE";
                                                                 }
                                                                 /*if($cell || $cell != '')
                                                                 {


                                                                       $date = DateTime::createFromFormat('d/m/Y', $cell);

                                                                           if(is_object($date)) {
                                                                                 $start_date = $date->format('Y-m-d');

                                                                           } else {
                                                                                  $insertRow = false;
                                                                                $sipMessage = "From Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                           }
                                                                 }
                                                                 else
                                                                 {
                                                                   $insertRow = false;
                                                                   $sipMessage="From date cannot be empty";


                                                                 }*/
                                                               }
                                                               else if(strtoupper($dataColumns[$countCell]) === 'TO_DATE' ||strtoupper($dataColumns[$countCell]) === 'ENDDATE')//last_date
                                                               {

                                                                $cell=trim(str_replace('/','-',$cell));
                                                                 if($cell || $cell != '')
                                                                 {
                                                                     $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                     //var_dump($cell);exit;
                                                                     // $date->format('Y-m-d');
                                                                     if(is_object($date)){
                                                                         $end_date=$date->format('Y-m-d');
                                                                     }
                                                                     else
                                                                     {
                                                                         $date = new DateTime($cell);
                                                                         if(is_object($date))
                                                                         {
                                                                             $end_date=$date->format('Y-m-d');

                                                                         }
                                                                         else
                                                                         {

                                                                             $insertRow = false;
                                                                             $$sipMessage = "End Date format is not proper (should be dd/mm/yyyy)";
                                                                              $sipErrorcol="TO DATE";
                                                                         }

                                                                     }

                                                                 }
                                                                 else
                                                                 {
                                                                   $insertRow = false;
                                                                   $sipMessage="End Date cannot be empty";
                                                                    $sipErrorcol="TO DATE";
                                                                 }
                                                                /* if($cell || $cell != '')
                                                                 {
                                                                       // $date = new DateTime($cell);
                                                                       // $End_date = $edate->format('Y-m-d');
                                                                       $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                           if(is_object($date)) {
                                                                                 $end_date = $date->format('Y-m-d');
                                                                           } else {
                                                                                  $insertRow = false;
                                                                                $sipMessage = "To Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                           }
                                                                 }
                                                                 else
                                                                 {
                                                                   $insertRow = false;
                                                                   $sipMessage="To Date cannot be empty";


                                                                 }*/

                                                               }


                                                         else if(strtoupper($dataColumns[$countCell]) === 'AUTO_AMOUNT' || strtoupper($dataColumns[$countCell])  === 'AUTO_AMOUN' || strtoupper($dataColumns[$countCell]) === 'AMOUNT')//installment_amt
                                                         {
                                                               if($cell || $cell != '')
                                                               {
                                                                    $auto_amoun = $cell;
                                                               }
                                                               else
                                                               {
                                                                   $insertRow = false;
                                                                   $sipMessage = "Auto Amount cannot be empty";
                                                                    $sipErrorcol="AUTO AMOUNT";
                                                               }

                                                           }
                                                         else if(strtoupper($dataColumns[$countCell]) === 'REG_DATE' || strtoupper($dataColumns[$countCell]) === 'REGDATE')//added on date
                                                         {

                                                           $cell=trim(str_replace('/','-',$cell));
                                                             if($cell || $cell != '')
                                                             {
                                                                 $date =DateTime::createFromFormat('m-d-Y',$cell);
                                                                 //var_dump($cell);exit;
                                                                 // $date->format('Y-m-d');
                                                                 if(is_object($date)){
                                                                     $reg_date=$date->format('Y-m-d');
                                                                 }
                                                                 else
                                                                 {
                                                                     $date = new DateTime($cell);
                                                                     if(is_object($date))
                                                                     {
                                                                         $reg_date=$date->format('Y-m-d');

                                                                     }
                                                                     else
                                                                     {

                                                                         $insertRow = false;
                                                                         $$sipMessage = "Registration Date format is notproper (should be dd/mm/yyyy)";
                                                                          $sipErrorcol="REG_DATE";
                                                                     }

                                                                 }

                                                             }
                                                             else
                                                             {
                                                                $insertRow = false;
                                                                $sipMessage="Registration date cannot be empty";
                                                                 $sipErrorcol="REG_DATE";
                                                             }
                                                           /*if($cell || $cell != '')
                                                           {

                                                                 $date = DateTime::createFromFormat('d/m/Y', $cell);
                                                                     if(is_object($date)) {
                                                                           $reg_date = $date->format('Y-m-d');
                                                                     } else {
                                                                            $insertRow = false;
                                                                          $sipMessage = "Reg Date format is not proper (should be dd/mm/yyyy) (error 2)";
                                                                     }
                                                           }
                                                           else
                                                           {
                                                             $insertRow = false;
                                                             $sipMessage="Registration date cannot be empty";

                                                           }*/
                                                           }
                                                         else if(strtoupper($dataColumns[$countCell]) === 'FREQUENCY')//frewuency
                                                         {
                                                              if($cell || $cell != '')
                                                              {
                                                                          $frequency = trim($cell);
                                                                  }
                                                                else
                                                                {
                                                                    $insertRow = false;
                                                                    $sipMessage = "Frequency cannot be empty";
                                                                     $sipErrorcol="FREQUENCY";
                                                                }

                                                         }

                                                         /*else if(strtoupper($dataColumns[$countCell]) === 'PAN_NO' || strtoupper($dataColumns[$countCell]) === 'PAN')//client_id
                                                         {
                                                           if($cell || $cell != '')
                                                           {
                                                                    $PanNum = trim($cell);
                                                                    $whereClient = array('c.pan_no'=>$PanNum,'f.broker_id'=>$this->session->userdata('broker_id'));
                                                                     $c_info = $this->Clients_model->get_client_family_by_pan($whereClient);
                                                                    // print_r($c_info);
                                                                     if(count($c_info) == 0)
                                                                     {
                                                                       $insertRow = false;
                                                                       //$sipMessage = "In ".$famName."Family Client Name".$clientName."  PAN No".$PanNum."   doesn't exist";
                                                                       $sipMessage = " PAN No ".$PanNum." doesn't exist";
                                                                     }
                                                                     else
                                                                     {
                                                                         $client_id = $c_info->client_id;
                                                                         $familyId = $c_info->family_id;
                                                                     }
                                                               }
                                                             else
                                                             {
                                                               $insertRow = false;
                                                               $sipMessage="PAN Number cannot be empty";

                                                             }
                                                           }*/
                                                         else if(strtoupper($dataColumns[$countCell]) === 'ACCOUNT_NO' || strtoupper($dataColumns[$countCell]) === 'ECSACNO')//account_number
                                                         {

                                                             if($cell || $cell != '')
                                                             {
                                                                  //  $dateOfTransaction= trim($cell);
                                                                     $account_no = $cell;

                                                                     //$dateOfTransaction = $date->format('Y-m-d');
                                                             }
                                                             else
                                                             {
                                                               $insertRow = false;
                                                               $sipMessage="Account Number cannot be empty";
                                                                $sipErrorcol="ACCOUNT_NO";
                                                               // $dateOfTransaction=null;
                                                               // $dateOfTransaction = 0;
                                                             }
                                                         }
                                                         else if(strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO' || strtoupper($dataColumns[$countCell]) === 'AUTO_TRNO')//scheme_id
                                                         {
                                                                   if($cell || $cell != '')
                                                                   {
                                                                       $auto_trno = $cell;
                                                                       //var_dump($cell);
                                                                   }
                                                                   else
                                                                   {
                                                                       $insertRow = false;
                                                                       $sipMessage = "AUTO_TRNO cannot be empty";
                                                                        $sipErrorcol="AUTO_TRNO";
                                                                   }
                                                         }
                                                          else if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//scheme_id
                                                         {
                                                                   if($cell || $cell != '')
                                                                   {
                                                                       $invname = trim($cell);
                                                                   }

                                                         }




                                                 $countCell++;
                                                 }
                                                  else {
                                                     if(strtoupper($dataColumns[$countCell]) === 'INV_NAME')//client_id
                                                         {

                                                               if($cell || $cell != '')
                                                               {
                                                                        $invname = trim($cell);

                                                                   }
                                                           }


                                                 }
                                       }
                                     }


                                       if($countRow != 0)
                                       {

                                           /////////////
                                           //  $temp_mat_date =new Datetime($maturityDate);
                                           //  $temp_issue_date =new DateTime($dateOfIssue);

                                        $type = $this->sp->get_type();
                                        $type=$type->type_id;
                                        settype($type,"int");
                                           $end_date_for_insert=$end_date;//get the value of end date before replace with cease date
                                          if(!empty(trim($cease_date)))
                                         {
                                           $end_date=$cease_date;//replace end date for maturity calculation
                                         }
                                         else
                                         {

                                              $cease_date=NULL;
                                         }

                                        $rate_of_return = $sip_rate/400;
                                        $install_amt = $auto_amoun;
                                        $date = new DateTime ($start_date);
                                        $start_date = $date->format('Y-m-d');
                                        $date = new DateTime ($end_date);
                                        $end_date = $date->format('Y-m-d');
                                        $mat_value = 0;

                                        $num_of_days = abs(strtotime($end_date) - strtotime($start_date)); //gives number of days with time
                                        $num_of_days = floor($num_of_days/2592000); //86400 seconds in a month
                                        // var_dump($num_of_days);
                                        $exp1 = floatval(pow((1 + $rate_of_return), $num_of_days/3)) - 1;
                                        $exp2 = 1 + $rate_of_return;
                                        $exp3 = -0.33333;
                                        $exp4 = 1 - (pow($exp2, $exp3));
                                        if($exp4 > 0)
                                            $mat_value = round(($auto_amoun * $exp1) / $exp4);

                $product_id = $this->sp->get_product_id();
                $product_id=$product_id->product_id;
                $added_on = date("Y/m/d");
                $frequency='monthly';//by default
                                           /////////////


                                           if(!$insertRow)
                                           {
                                              //  $sip_data[$countErrRow][1] = $folio_id;
                                              //  $sip_data[$countErrRow][2] = $invname;
                                              //  $sip_data[$countErrRow][3] = $sipMessage;

                                               $sip_data['client_id'] = $client_id;
                                               $sip_data['product_id'] = intval($product_id);
                                               $sip_data['type_id'] = $type;
                                               $sip_data['scheme_id'] = $scheme_id;
                                               $sip_data['folio_no'] = $folio_id;
                                                $sip_data['ref_number'] = $auto_trno;
                                                 $sip_data['start_date'] = date($start_date);
                                                  $sip_data['end_date'] = date($end_date_for_insert);
                                                   $sip_data['frequency'] = $cease_date;
                                                    $sip_data['installment_amount'] = $auto_amoun;
                                                     $sip_data['rate_of_return'] = $sip_rate;
                                                     $sip_data['reg_date'] = $reg_date;
                                                     $sip_data['Bank_AccountNo'] = $Bank_AccountNO;
                                                     $sip_data['Bank'] = $bank;
                                                     $sip_data['expected_mat_value'] = $mat_value;
                                                     $sip_data['broker_id'] = $brokerID;
                                                     $sip_data['user_id'] = $user_id;
                                                     $sip_data['added_on'] =date($added_on);
                                                     $sip_data['error_file_name']=$tfile;
                                                     $sip_data['error_col']=$sipErrorcol;
                                                     $sip_data['error_msg']=$sipMessage;
                                                     $sip_data['rta_type'] ='CAMS';

                                             //echo"<pre>";print_r($sip_data);exit;
                                               $countErrRow++;
                                               $insertRow = true;
                                                $uploadedStatus = 2;
                                               continue;
                                           }

                                          $dataRows['add_SIP_list'] = array (
                                                                            'client_id'=>$client_id,
                                                                            'product_id'=> intval($product_id),
                                                                            'type_id'=>$type,
                                                                            //'company_id'=>intval($bank_id),
                                                                            'scheme_id'=> $scheme_id,
                                                                            'folio_no'=> $folio_id,
                                                                            'ref_number'=> $auto_trno,
                                                                            'start_date'=> date($start_date),
                                                                            'end_date'=> date($end_date_for_insert),
                                                                            'frequency'=>$frequency,//default frequency is monthly
                                                                            'cease_date'=>$cease_date,
                                                                            'installment_amount'=> $auto_amoun,
                                                                            'rate_of_return'=>$sip_rate,
                                                                            'reg_date'=>$reg_date,
                                                                            'Bank_AccountNo'=>$Bank_AccountNO,
                                                                            'Bank'=>$bank,
                                                                            'expected_mat_value'=> $mat_value,
                                                                            'broker_id'=> $brokerID,
                                                                            'user_id'=> $user_id,
                                                                            'added_on'=> date($added_on)
                                                                            );
                                                                           //var_dump($dataRows['add_SIP_list']);


                                                                               $assets_record=array('scheme_id'=>$scheme_id,'start_date'=>$start_date,'folio_no'=>$folio_id,'client_id'=>$client_id);

                                                                           if($isDuplicateSIP=$this->sp->check_duplicate_sip($assets_record))
                                                                            {

                                                                              $client_id=$isDuplicateSIP->client_id;
                                                                              $assets_id=$isDuplicateSIP->asset_id;


                                                                               $inserted =$this->sp->update_duplicate_sip($dataRows['add_SIP_list'],array('client_id'=>$client_id));
                                                                                $d=$this->sp->delete_asset_id(array('asset_id'=>$assets_id));

                                                                                 $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                                $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                              $uploadedStatus = 1;
                                                                            }
                                                                            else
                                                                            {

                                                                          $inserted = $this->sp->add_sip($dataRows['add_SIP_list']);
                                                                            $assets_id=$inserted;


                                                                          $AddInterest = array('start_date'=> date($start_date),'end_date'=> date($end_date),'frequency'=>$frequency,'installment_amt'=> $auto_amoun,'broker_id'=>$brokerID,'asset_id'=>$assets_id);
                                                                         $status=$this->add_sipinterest_details($AddInterest, $assets_id);
                                                                         $uploadedStatus = 1;
                                                                            }





                                           if(is_array($inserted))
                                           {
                                               $uploadedStatus = 0;
                                               $message = 'Error while inserting records. '.$assets_id['message'];
                                               break;
                                           }
                                           $scheme_name="";$scheme_id="";$folio_id="";$installment_amt="";$added_on="";$Start_date="";
                                           $End_date="";$frequency="";$Client_id="";$account_no="";
                                       }
                                       if($uploadedStatus == 0)
                                           break;

                                       $countRow++;
                                   }

                                   // Here add the error record in the clients_auto_import_error table for further manual intervention
                                   $sip_error_bakcup=$this->sp->add_auto_import_error_sip($sip_data);

                 if($dataRows)
                 {
                     if(is_array($inserted))
                     {
                         $uploadedStatus = 0;
                         $message = 'Error while inserting records';
                     } else {
                       // var_dump($brokerID);
                       //  var_dump($_FILES["import_Sip"]["name"]);
                       //   var_dump($user_id);
                      //   $this->common->last_import('SIP Details', $brokerID, $_FILES["import_Sip"]["name"], $user_id);
                         if($uploadedStatus != 2) {
                             $uploadedStatus = 1;
                             $message = "SIP Details Uploaded Successfully";
                         }
                     }
                 }
               unset($dataColumns, $dataRows);

             }
             /*------------- Cams DBF SIP IMPort Ends here---------*/

          }
        }
  // return $sip_data;
    return $tfile;

    }  // end of sip import



private function add_sipinterest_details($data, $transID)
{

$Start_date = $data['start_date'];
$End_date = $data['end_date'];
$installment_amt = $data['installment_amt'];
$brokerID = $data['broker_id'];
$assets_id = $data['asset_id'];
$frequency=$data['frequency']; //by default monthly


    // $issueDate = $data['issued_date'];
    // $mat_date = $data['maturity_date'];
    // $int_mode = $data['interest_mode'];
    // $amt_inv = $data['amount_invested'];
    // $int_rate = $data['interest_rate'];
    // $int_round_off = $data['int_round_off'];
    $total_days = 365;
    //$tempIssueDate = strtotime($issueDate);
    $tempIssueDate = $Start_date;
    $issueDate=$Start_date;
    // $month = date('n', $tempIssueDate);
    // $year = date('Y', $tempIssueDate);
    $Start_date = new DateTime ($Start_date);
    $year = $Start_date->format("Y");
    $month = $Start_date->format("m");
    $tempIssueDate0 = $Start_date->format("m");
    $tempIssueDat = $Start_date->format("Y-m-d");


    if($frequency == "Annually")
    {
        if($month <= 3)
            $int_date = $year.'-03-31';
        else
            $int_date = ($year+1).'-03-31';
        if($Start_date != $int_date) {
            //$num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
            $tempindate = new DateTime($int_date);
            $tempisdate = new DateTime($tempIssueDat);
            $diff=$tempindate->diff($tempisdate);

            $num_of_days = $diff->days;
            // $num_of_days = ($int_date - $tempIssueDat); ///this give number of days with time
            // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
            //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
            $data_int = array(
                'asset_id' => $assets_id,
                'maturity_date' => $int_date,
                'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        }
        $issueDate = $int_date;
        //$int_amt = (floatval($amt_inv) / 100) * (floatval($int_rate));
        //var_dump($issueDate);
        //var_dump($mat_date);
        while($issueDate <= $End_date)
        {
          $issueDate = new DateTime($issueDate);
          $issueDate->modify('+1 year');
          $issueDate = $issueDate->format('Y-m-d');
            //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 1 year'));
            //var_dump($issueDate);
            if($issueDate >= $End_date)
            {


                 $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
                break;
            }
            $data_int = array(
              'asset_id' => $assets_id,
              'maturity_date' => $issueDate,
              'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }

        }
    }
    else if($frequency == "Half-yearly")
    {
        if($month <= 6)
            $int_date = $year.'-06-30';
        else
            $int_date = ($year).'-12-31';

        if($issueDate != $int_date) {
            // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
            // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day

            $tempindate = new DateTime($int_date);
            $tempisdate = new DateTime($tempIssueDat);
            $diff=$tempindate->diff($tempisdate);

            //$num_of_days = $diff->days;
            //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
            $data_int = array(
              'asset_id' => $assets_id,
              'maturity_date' => $issueDate,
              'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        }
        $issueDate = $int_date;

        //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 2;
        while($issueDate <= $End_date)
        {
            // $issueDate0 = new DateTime('@'.strtotime($issueDate));
            // $issueDate0->modify('last day of +6 month');
            // $issueDate = $issueDate0->format('Y-m-d');
            $issueDate = new DateTime($issueDate);
            $issueDate->modify('last day of +6 month');
            $issueDate = $issueDate->format('Y-m-d');
            //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 6 month'));
            if($issueDate >= $End_date)
            {


                 $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
                break;
            }
            $data_int = array(
              'asset_id' => $assets_id,
              'maturity_date' => $issueDate,
              'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        }

    }

    else if($frequency == "Quarterly")
    {
        if($month <= 3)
        {
            $int_date = $year.'-03-31';
        }
        else if ($month <= 6)
        {
            $int_date = $year.'-06-30';
        }
        else if($month <= 9)
        {
            $int_date = $year.'-09-30';
        }
        else
        {
            $int_date = $year.'-12-31';
        }

        if($issueDate != $int_date) {
            // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
            // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
            $tempindate = new DateTime($int_date);
            $tempisdate = new DateTime($tempIssueDat);
            $diff=$tempindate->diff($tempisdate);

          //  $num_of_days = $diff->days;

          //  $int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
            $data_int = array(
              'asset_id' => $assets_id,
              'maturity_date' => $issueDate,
              'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        }
        $issueDate = $int_date;

        //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 4;
        while($issueDate <= $End_date)
        {
            // $issueDate0 = new DateTime('@'.strtotime($issueDate));
            // $issueDate0->modify('last day of +3 month');
            // $issueDate = $issueDate0->format('Y-m-d');
            $issueDate = new DateTime($issueDate);
            $issueDate->modify('last day of +3 month');
            $issueDate = $issueDate->format('Y-m-d');
            //$issueDate = date('Y-m-d', $issueDate0);
            //$issueDate = date('Y-m-d', strtotime($issueDate. ' + 3 month'));
             if($issueDate >= $End_date)
            {


                 $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
                break;
            }
            $data_int = array(
              'asset_id' => $assets_id,
              'maturity_date' => $issueDate,
              'maturity_amount' => $installment_amt
            );
            $status = $this->sp->add_sip_interest($data_int);
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
        }

    }

    else if($frequency == "Monthly" || $frequency == "monthly" ) {

       $int_date_temp = new DateTime($year.'-'.$month.'-1');
       $int_date_temp->modify('last day of this month');
       $int_date = $int_date_temp->format('Y-m-d');

       if($issueDate != $int_date) {
           // $num_of_days = (strtotime($int_date) - $tempIssueDate); ///this give number of days with time
           // $num_of_days = floor($num_of_days/86400); ///86400 seconds in a day
           $tempindate = new DateTime($int_date);
           $tempisdate = new DateTime($tempIssueDat);
           $diff=$tempindate->diff($tempisdate);

           //$num_of_days = $diff->days;

           //$int_amt = ((((floatval($amt_inv) / 100) * (floatval($int_rate))) / $total_days) * $num_of_days);
           $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
           );
           $status = $this->sp->add_sip_interest($data_int);
           //if there is any error
           if(isset($status['code']))
           {
               throw new Custom_exception();
           }
       }
       $issueDate = $int_date;

       //$int_amt = ((floatval($amt_inv) / 100) * (floatval($int_rate))) / 12;
       $cnt = 0;
       while($issueDate <= $End_date)
       {
           // $issueDate0 = new DateTime('@'.strtotime($issueDate));
           // $issueDate0->modify('last day of next month');
           // $issueDate = $issueDate0->format('Y-m-d');

           $issueDate0 = new DateTime($issueDate);
           $issueDate0->modify('last day of next month');
           $issueDate = $issueDate0->format('Y-m-d');




           if($issueDate >= $End_date)
            {


               //echo $issueDate;
                //echo $assets_id;
                 $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
              );
              $status = $this->sp->add_sip_interest($data_int);
                break;
            }
           $data_int = array(
             'asset_id' => $assets_id,
             'maturity_date' => $issueDate,
             'maturity_amount' => $installment_amt
           );


           $status = $this->sp->add_sip_interest($data_int);
           //if there is any error
           if(isset($status['code']))
           {
               throw new Custom_exception();
           }
       }
   }

}

function del_uploaded_file($dkey,$del_file){

  //code for moving the files which are read completed to completed folder
//  foreach($allset as $dkey=>$val){
    if($dkey=='client_folio'){
      $source='downloads/Folio_master_list/cam_folder/';
      $destination='downloads/Completed/Folio_master_list/cam_folder/';


      //   echo"in folio file transfer";
         $mv=$del_file;
         $srcfile=str_replace('downloads/Folio_master_list/cam_folder/','',$mv);
      //   echo $srcfile;
         $files1 =scandir('downloads/Folio_master_list/cam_folder/');
        $flag= in_array($srcfile,$files1);
      //  echo $flag;
        if($flag){
          if (copy($source.$srcfile, $destination.$srcfile)) {
            unlink($mv);
            echo"folio file deleted:".$srcfile;
          }
        }

      // }

    }elseif($dkey=='mf_transact'){
      $source='downloads/MF_Transaction_list/cam_folder/';
      $destination='downloads/Completed/MF_Transaction_list/cam_folder/';
    $mv=$del_file;
       $srcfile=str_replace('downloads/MF_Transaction_list/cam_folder/','',$mv);
     $files1 =scandir('downloads/MF_Transaction_list/cam_folder/');
     $flag= in_array($srcfile,$files1);
     if($flag){
       if (copy($source.$srcfile, $destination.$srcfile)) {
         unlink($mv);
         echo"mf file deleted:".$srcfile;
       }
     }

    }elseif($dkey=='sip_import'){
       $source='downloads/SIP_files_list/cam_folder/';
       $destination='downloads/Completed/SIP_files_list/cam_folder/';
      $mv=$del_file;
        $srcfile=str_replace('downloads/SIP_files_list/cam_folder/','',$mv);
          $files1 =scandir('downloads/SIP_files_list/cam_folder/');
          $flag= in_array($srcfile,$files1);
        //  echo $flag;
          if($flag){
            if (copy($source.$srcfile, $destination.$srcfile)) {
              unlink($mv);
              echo"sip file deleted:".$srcfile;
            }
          }
     }
}


}//class end
?>
