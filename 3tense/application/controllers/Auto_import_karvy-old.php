<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auto_import_karvy extends CI_Controller{

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

    function karvy_auto_import()
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
    //  $hostname = '{cs9.webhostbox.net:143/notls/norsh/novalidate-cert}INBOX';  /*working hostname string for itechnextgen.com domain*/
          $hostname = "{mail.3tense.com:143/novalidate-cert}";
      //  $username = 'mfautoupload@itechnextgen.com'; # e.g somebody@3Tense.com
          $username = 'mfautoupload@3tense.com';
        $password = 'auto@1234';
        /* try to connect */
        $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
    //    var_dump($inbox);
            /************Folio File Auto Import********************/

/*Array for the RTA mail subject*/
$karvy_auto_import=array();

$karvy_auto_import['client_folio']="Subscribed Master AUM/Investor Master Details Report for Reference No";
$karvy_auto_import['mf_transact']="Subscribed Transaction Feeds Report for Reference No";
$karvy_auto_import['sip_import']="SUBSCRIPTION SIP REGISTRATION Report for Ref. No";

 /*Array for the Broker username and their passwords*/
$broker_pwd=array();
/*$broker_pwd['ARN-30395']='karvy123';samor108@gmail.com//broker-Kiranbhai Mulchand Shah  for folio K is capital letter and for mf transaction k is small letter
 $broker_pwd['ARN-17227']='Super@03'; relationship14@gmail.com  //broker-RELATION-SHIP LIFE TIME SERVICES
 $broker_pwd['ARN-102037']='stc123456'; astarsec@gmail.com//broker - Yatin Bid */


foreach($karvy_auto_import as $mainkey => $value){
  //  echo"hellllloooooo";
//  echo $karvy_auto_import[$key];
/*for CAMS client Folio file auto import*/
         if($karvy_auto_import[$mainkey]=="Subscribed Master AUM/Investor Master Details Report for Reference No"){   //karvy if of folio master files;
      $emails = imap_search($inbox, 'SUBJECT "Subscribed Master AUM/Investor Master Details Report for Reference No" ');
       // echo "<pre>";print_r($emails);exit;

          /* if any emails found, iterate through each email */
          if($emails) {
          //  echo " Mails for today";
              $output = '';
              $count = 1;

              /* put the newest emails on top */
              rsort($emails);
              /* for every email... */
                 $toaday_karvy_mail=array();
                 $to_part=array();
              foreach($emails as $email_number)
              {
                  /* get information specific to this email */
                  $overview = imap_fetch_overview($inbox,$email_number,0);
                  $message_date=new DateTime($overview[0]->date);//Put date result at DateTime constructor
                //  echo "####".$message_date;
                  $comp_today_date= $message_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format
                  //check for only yesterday's date  mail and get array of them only.
                  $date  = new DateTime();
                  $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
                  $date->sub($interval);
                    $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
                  if($comp_today_date === $yesterday){
                    ///echo"date matched for email_number".$email_number;
                     array_push($toaday_karvy_mail,$email_number);
                      array_push($to_part,$overview[0]->to); //for mailback service mail array collection
                  }

               }

             }else{
               echo "Mail box is empty";
             }
             if(isset($toaday_karvy_mail)){
               foreach($toaday_karvy_mail as $tm){
                 $temp[]=imap_body($inbox, $tm);
               }
             }else{
               echo"No Mails for today";
             }
  $temp1=array();
//echo"<pre>";print_r($temp);exit;
  foreach($temp as $t){
    $broker_name=$this->GetBetween('Broker Code','Frequency',$t);
  $final_link=str_replace(array("\n","\r\n","\r"), '', str_replace('=','',$this->GetBetween('following URL,','Please feel free to',$t)));
//echo $final_link;
            if(strpos( $final_link,'/karvy/Subreports/')){
               $temp1[]=$final_link;
               $broker[]=trim($broker_name);
            }else{
              //  $problem="false";
                $problem_message[]="file link is not available in the mail with Request ID:".$req_id."And date:".$comp_today_date;
            }
unset($username,$broker_name,$finaluser,$tempuser);
  }
 //echo"<pre>";print_r($broker);
//echo"<pre>";print_r($temp1);//exit;
    $lcnt=0;
  foreach($temp1 as $key=>$val){
    $link=$temp1[$key];
  //$link=str_replace(array("<a",">","</a>","href3D'https://mailback2.camsonline.com/mailback_result/03313356710260528Z4FGBMPOH1HFGLB8Q9O3JKK9L9148273753BMB73126439R9.zip'",")",">","</a<BR<BR"),'',$link);
  $link=trim($link);
  $bc_url = $link;//'http://www.bseindia.com/download/BhavCopy/Equity/';
      //get the date for which you want bhav_copy - start with todays date
      $bc_date = $date->format("dmy");//date('dmy');
      //run till we get the file properly
      $success = false;
      $bc_filename = 'Karvy_Folio'.$bc_date.'_'.$lcnt.'CSV.ZIP';
      //  echo $bc_filename;
      //    echo $bc_url;
        // exit;
          //now download the bhav copy from BSE website
         $local = "downloads/Folio_master_list/karvy_folder/"; //local folder path
          $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
          //if it returns false, try again with one day before
          if(!$filepath) {
              echo $bc_date.' - Could not download folio file. <br/>';
              $problem_message[]=$bc_date.' - Could not download client folio master file with Request ID:'.$req_id."And date:".$comp_today_date;
            $bc_date = $this->get_yesterday($bc_date);

              continue;
          }

          //now we have to extract the zip
         //$unzip=true;//comment this line on server and uncomment below line on server
              $location="downloads/Folio_master_list/karvy_folder/";
          /*Note:for karvy passwords are different for every broker so from $key first fetch broker name from broker
           array and based on broker name fetch its password from the array*/
             $brokerName=$to_part[$key];
             echo $brokerName;
              if($brokerName==='samor108@gmail.com'){
                  //echo "K k";
                        $pwd='Karvy123'; //for karvy folio  capital
                       }else{
                          //$pwd=$broker_pwd[$brokerName];
                        //based on the broker name i.e. ARN number fetch its karvy rta password  from user table of database
                        $pwd=$this->Clients_model->get_Karvy_RTA_import_password($brokerName);
                        $broker_pwd[$brokerName]=$pwd;
                       }

               echo "folio extract pwd=".$pwd;//exit;
         $unzip = $this->extract_archive($filepath,$location,$pwd);
          if(!$unzip) {
              echo $bc_date.' - Could not open/extract zip file. <br/>';
                $problem_message[]=$bc_date.' - Could not open/extract zip file for the request id:'."with Request ID:".$req_id."And date:".$comp_today_date;;
              //$bc_date = $this->get_yesterday($bc_date);
              continue;
          } else {
            //scan the directory for today's cams folio master dbf file
            $today_karvy=scandir('downloads/Folio_master_list/karvy_folder/');
            $today=$date->format("d-m-Y");//date('d-m-Y');
              $searchTemp = DateTime::createFromFormat('d-m-Y',$today);
              $SearchDate = $searchTemp->format('dmy');
     //  echo $SearchDate;
    //   echo"<pre>";print_r($today_karvy);
       $read_arr2=array();
                  foreach($today_karvy as $file2){
   if(!is_dir($file2)){
  //echo $file2;
    $extracted_file=str_replace('https://www.karvymfs.com/karvy/Subreports/','',$link);
//  echo $extracted_file;exit;
     $extracted_file=$extracted_file.'.dbf';
    // echo $extracted_file;exit;
                            //if($file2!=$extracted_file)
                           if($file2!=$extracted_file)
                            {
                             if(!stripos($file2,'.zip')){

                                  $read_arr[]=$file2;
                              }

                            }
                          }
                          unset($file2);


                  }
                  }
          //  print_r($read_arr);exit;
                for($i=0;$i<count($read_arr);$i++){
                $successFlag=true;
                $passfileType='karvy_excel';
                if(strpos($read_arr[$i], 'downloads/Folio_master_list/karvy_folder/')=== false){
                                                $read_arr[$i]='downloads/Folio_master_list/karvy_folder/'.$read_arr[$i];
                                              }else{
                                                $read_arr[$i]=$read_arr[$i];
                                              }

              $allset[$mainkey][$i]= $this->clients_import($successFlag,$read_arr[$i],$passfileType);
            }


          echo '\n';
    //  }
    $lcnt++;
  }//end of foreach link
  unset($broker);
}//end of Karvy if of folio master files;


// for mutual fund transaction  import
   if($karvy_auto_import[$mainkey]=="Subscribed Transaction Feeds Report for Reference No"){
     $temails = imap_search($inbox, 'SUBJECT "Subscribed Transaction Feeds Report for Reference No" ');
        //echo "<pre>";print_r($temails);exit;

         /* if any emails found, iterate through each email */
         if($temails) {
         //  echo " Mails for today";
             $output = '';
             $count = 1;

             /* put the newest emails on top */
             rsort($temails);
             /* for every email... */
             foreach($temails as $temail_number)
             {
                 /* get information specific to this email */
                 $toverview = imap_fetch_overview($inbox,$temail_number,0);
                 $tmessage_date=new DateTime($toverview[0]->date);//Put date result at DateTime constructor
                 $tcomp_today_date= $tmessage_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format

                 //check for only yesterday's date  mail and get array of them only.
                 $date  = new DateTime();
                 $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
                 $date->sub($interval);
                   $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
                 if($tcomp_today_date === $yesterday){
                   ///echo"date matched for email_number".$email_number;
                   $t_today_mail[]=$temail_number;
                   $to_part_mf[]=$toverview[0]->to;
                 }

              }
              //print_r($t_today_mail);

            }else{
              echo "Mail box is empty";
            }
            if(isset($t_today_mail)){
                foreach($t_today_mail as $tm){
                $t_temp[]=imap_body($inbox, $tm);
              }
            }else{
              echo"No Mails for today";
            }
            //print_r($t_temp);

         $t_temp1=array();//exit;

         foreach($t_temp as $tt){
           $broker_name=$this->GetBetween('Broker Code','Frequency',$tt);
           $broker_name=str_replace(array("('","')"), '',$broker_name);
          //echo $broker_name;exit;
         $final_link=str_replace(array("\n","\r\n","\r"), '', str_replace('=','',$this->GetBetween('following URL,','Please feel free to',$tt)));
       //echo $final_link;
                   if(strpos( $final_link,'/karvy/Subreports/')){
                      $t_temp1[]=$final_link;
                        if(isset($final_link)){
                        $broker[]=trim($broker_name);
                      }
                   }else{
                     //  $problem="false";
                       $problem_message[]="file link is not available in the mail with And date:".$tcomp_today_date;
                   }
       unset($username,$broker_name,$finaluser,$tempuser,$final_link);
         }
      //echo"<pre>";print_r($broker);
      // echo"<pre>";print_r($t_temp1);//exit;

           $tlcnt=0;
           foreach($t_temp1 as $key=>$val){
            $link=$t_temp1[$key];
            $link=trim($link);
            $bc_url = $link;
             //get the date for which you want bhav_copy - start with todays date
               $bc_date =$date->format("dmy");// date('dmy');
               //run till we get the file properly
               $success = false;
               $bc_filename = 'KARVY_MF'.$bc_date.'_'.$tlcnt.'.ZIP';
               //  echo $bc_filename;
                 //now download the bhav copy from BSE website
                  $local = "downloads/MF_Transaction_list/karvy_folder/"; //local folder path
                   $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
                   //if it returns false, try again with one day before
                   if(!$filepath) {
                       echo $bc_date.' - Could not download bhav copy. <br/>';
                       $problem_message[]=$bc_date.' - Could not download client folio master file with Request ID:'.$req_id."And date:".$comp_today_date;
                     $bc_date = $this->get_yesterday($bc_date);
                       continue;
                   }

                   //now we have to extract the zip
               //   $unzip=true;//comment this line on server and uncomment below line on server
                       $location="downloads/MF_Transaction_list/karvy_folder/";
                       $brokerName=$to_part_mf[$key];
                       echo $brokerName;
                       if($brokerName=='samor108@gmail.com'){
                        $pwd1='Karvy123'; //for karvy mf transcation  small
                       }else{
                       //$pwd1=$broker_pwd[$brokerName];
                         //based on the broker name i.e. ARN number fetch its karvy rta password  from user table of database
                         $pwd1=$this->Clients_model->get_Karvy_RTA_import_password($brokerName);
                         $broker_pwd[$brokerName]=$pwd1;
                       }


                   $unzip = $this->extract_archive($filepath,$location,$pwd1);
                    echo "MF extract pwd=".$pwd1;
                   if(!$unzip) {
                       echo $bc_date.' - Could not open/extract zip file. <br/>';
                         $problem_message[]=$bc_date.' - Could not open/extract zip file for the request id:'."with Request ID:".$req_id."And date:".$comp_today_date;;
                       //$bc_date = $this->get_yesterday($bc_date);
                       continue;
                   } else {
                     //scan the directory for today's cams folio master dbf file
                     $today_karvy=scandir('downloads/MF_Transaction_list/karvy_folder/');
                     $today=$date->format("d-m-Y");//date('d-m-Y');
                       $tsearchTemp = DateTime::createFromFormat('d-m-Y',$today);
                       $tSearchDate = $tsearchTemp->format('d-m-Y');
                     $tSearchDate=str_replace('-','',$tSearchDate);
               //  print_r($today_cam);
               $tread_arr=array();
                          foreach($today_karvy as $file2){
           if(!is_dir($file2)){
          //echo $file2;
            $extracted_file=str_replace('https://www.karvymfs.com/karvy/Subreports/','',$link);
        //  echo $extracted_file;exit;
             $extracted_file=$extracted_file.'.dbf';
            // echo $extracted_file;exit;
                                    //if($file2!=$extracted_file)
                                   if($file2!=$extracted_file)
                                    {
                                     if(!stripos($file2,'.zip')){

                                          $tread_arr[]=$file2;
                                      }

                                    }
                                  }
                                  unset($file2);
                          }
                          // print_r($tread_arr);//exit;

                         for($i=0;$i<count($tread_arr);$i++){
                         $successFlag=true;
                         $passfileType='karvy_excel';
                         if(strpos($tread_arr[$i], 'downloads/MF_Transaction_list/karvy_folder/')=== false){
                                                         $tread_arr[$i]='downloads/MF_Transaction_list/karvy_folder/'.$tread_arr[$i];
                                                      }else{
                                                         $tread_arr[$i]=$tread_arr[$i];
                                                      }
                          echo "auto file=".$tread_arr[$i];
                      $allset[$mainkey][$i]= $this->mf_import($successFlag,$tread_arr[$i]);
                     }

                   }
                   echo '\n';
                   unset($pwd1);
             //  }
             $tlcnt++;
           }//end of foreach link
          // exit;
          unset($broker);
}//end of karvy mutual fund import


//Karvy SIP import
 if($karvy_auto_import[$mainkey]=="SUBSCRIPTION SIP REGISTRATION Report for Ref. No"){   //Karvy if of SIP files import;
 $semails = imap_search($inbox, 'SUBJECT "SUBSCRIPTION SIP REGISTRATION Report for Ref. No" ');
 //echo "<pre>";print_r($semails);exit;

  /* if any emails found, iterate through each email */
  if($semails) {
  //  echo " Mails for today";
      $output = '';
      $count = 1;

      /* put the newest emails on top */
      rsort($semails);
      /* for every email... */
      foreach($semails as $semail_number)
      {
          /* get information specific to this email */
          $soverview = imap_fetch_overview($inbox,$semail_number,0);
          $smessage_date=new DateTime($soverview[0]->date);//Put date result at DateTime constructor
          $scomp_today_date= $smessage_date->format("Y-m-d");//Formating the DateTime value to MySQL DATETIME format
          //check for only yesterday's date  mail and get array of them only.
          $date  = new DateTime();
          $interval = new DateInterval('P1D'); //one day intervel for getting yesterday
          $date->sub($interval);
            $yesterday=$date->format("Y-m-d");  // this will get yesterdaydate
          if($scomp_today_date === $yesterday){
            ///echo"date matched for email_number".$email_number;
            $s_today_mail[]=$semail_number;
            $to_part_sip[]=$soverview[0]->to;
          }

       }
  // print_r($s_today_mail);//exit;

  }else{
    echo "Mail box is empty";
  }
  if(isset($s_today_mail)){
    foreach($s_today_mail as $sp){
      $s_temp[]=imap_body($inbox, $sp);
    }
  }else{
    echo"No Mails for today";
  }

  $s_temp1=array();

  foreach($s_temp as $ss){
    $broker_name=$this->GetBetween('Broker Code','Fund(s) Selected',$ss);
    $broker_name=str_replace(array("('","')"), '',$broker_name);
   //echo $broker_name;exit;
   $ll=$this->GetBetween('following URL,','Please feel free to',$ss);
   //echo $ll;
  //$final_link=str_replace(array("\n","\r\n","\r"), '', str_replace('=','',$this->GetBetween('following URL,','Please feel free to',$ss)));
   $final_link=str_replace(array("\n","\r\n","\r"), '', $ll);
//echo $final_link;//exit;
           if(strpos( $final_link,'karvy/Subreports/')){
              $s_temp1[]=$final_link;
              $broker[]=trim($broker_name);
            }else{
               $sproblem_message[]="file link is not available in the mail with date:".$scomp_today_date;
            }

  }
//echo"<pre>";print_r($s_temp1);//exit;
    $slcnt=0;
    foreach($s_temp1 as $key=>$val){
  $link=$s_temp1[$key];
  $link=trim($link);
  $bc_url = $link;
  // echo $link;exit;

        $bc_url = $link;//'http://www.bseindia.com/download/BhavCopy/Equity/';
        //get the date for which you want bhav_copy - start with todays date
        $bc_date =$date->format("dmy");// date('dmy');
        //run till we get the file properly
        $success = false;
        $bc_filename = 'KARVY_SIP'.$bc_date.'_'.$slcnt.'.ZIP';
        //  echo $bc_filename;
        //    echo $bc_url;
          // exit;
            //now download the bhav copy from BSE website
           $local = "downloads/SIP_files_list/karvy_folder/"; //local folder path
            $filepath = $this->download_bhav_copy($bc_url,$bc_filename,$local);
            //if it returns false, try again with one day before
            if(!$filepath) {
                echo $bc_date.' - Could not download bhav copy. <br/>';
                $sproblem_message[]=$bc_date.' - Could not download client folio master file with Request ID:'.$req_id."And date:".$comp_today_date;
              $bc_date = $this->get_yesterday($bc_date);

                continue;
            }

            //now we have to extract the zip
          //  $unzip=true;//comment this line on server and uncomment below line on server
                $location="downloads/SIP_files_list/karvy_folder/";
                $brokerName=$to_part_sip[$key];
                echo $brokerName;
                if($brokerName=='samor108@gmail.com'){
                        $pwd2='karvy123'; //for karvy mf transcation  small
                       }else{
                        //$pwd2=$broker_pwd[$brokerName];
                         //based on the broker name i.e. ARN number fetch its karvy rta password  from user table of database
                         $pwd2=$this->Clients_model->get_Karvy_RTA_import_password($brokerName);
                         $broker_pwd[$brokerName]=$pwd2;
                       }
                      echo "SIP extract pwd=".$pwd2;//exit;
           $unzip = $this->extract_archive($filepath,$location,$pwd2);
            if(!$unzip) {
                echo $bc_date.' - Could not open/extract zip file. <br/>';
                  $sproblem_message[]=$bc_date.' - Could not open/extract zip file for the request id:'."with Request ID:".$req_id."And date:".$comp_today_date;;
                //$bc_date = $this->get_yesterday($bc_date);
                continue;
            } else {
              //scan the directory for today's cams folio master dbf file
              $today_karvy=scandir('downloads/SIP_files_list/karvy_folder/');
              $today=$date->format("d-m-Y");//date('d-m-Y');
                $tsearchTemp = DateTime::createFromFormat('d-m-Y',$today);
                $tSearchDate = $tsearchTemp->format('d-m-Y');
              $tSearchDate=str_replace('-','',$tSearchDate);
         // echo"<pre>";print_r($today_karvy);exit;
        $sread_arr=array();
                   foreach($today_karvy as $file2){
    if(!is_dir($file2)){
   //echo $file2;
     $extracted_file=str_replace('https://www.karvymfs.com/karvy/Subreports/','',$link);
  //  echo $extracted_file;exit;
      $extracted_file=$extracted_file.'.dbf';
     // echo $extracted_file;exit;
                             //if($file2!=$extracted_file)
                            if($file2!=$extracted_file)
                             {
                              if(!stripos($file2,'.zip')){

                                   $sread_arr[]=$file2;
                               }

                             }
                           }
                           unset($file2);
                   }
                   print_r($sread_arr);

                  for($i=0;$i<count($sread_arr);$i++){
                  $successFlag=true;
                  $passfileType='karvy_excel';
                  if(strpos($sread_arr[$i], 'downloads/SIP_files_list/karvy_folder/')=== false){
                                                  $sread_arr[$i]='downloads/SIP_files_list/karvy_folder/'.$sread_arr[$i];
                                                }else{
                                                  $sread_arr[$i]=$sread_arr[$i];
                                                }
                  echo "auto file=".$sread_arr[$i];
                $allset[$mainkey][$i]= $this->sip_import($successFlag,$sread_arr[$i]);
              }

            }
            echo '\n';
      //  }
      $slcnt++;
    }//end of foreach link
     unset($broker);
 }
 echo"<pre>";print_r(@$allset);//exit;


echo"end of auto import";
/*forward all error occured while importing to the  admin via mail*/


}

//code for moving the files which are read completed to completed folder
foreach($allset as $dkey=>$val){
  if($dkey=='client_folio'){
    $source='downloads/Folio_master_list/karvy_folder/';
    $destination='downloads/Completed/Folio_master_list/karvy_folder/';


  //   echo"in folio";
    $mv=array_unique($allset[$dkey]);
  //   print_r($mv);
  //  $dest="/opt/lampp/htdocs/3tense_broker_new1/downloads/completed/Folio_master_list/karvy_folder/";  ///opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/cam_folder
  // for($i=0;$i<count($mv);$i++){
  //   $srcfile=str_replace('/opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/karvy_folder/','',$mv[$i]);
  //    rename($mv[$i],$dest.$srcfile);
     //unlink($mv[$i]);
     $files1 =scandir('downloads/Folio_master_list/karvy_folder/');
     print_r($files1);
      foreach($mv as $key=>$val){
       $srcfile=str_replace('downloads/Folio_master_list/karvy_folder/','',$mv[$key]);
       echo $srcfile;
      $flag= in_array($srcfile,$files1);
      echo $flag;
      if($flag){
        if (copy($source.$srcfile, $destination.$srcfile)) {
            $delete[] = $mv[$key];
        }
      }

     }

  }elseif($dkey=='mf_transact'){
    $source='downloads/MF_Transaction_list/karvy_folder/';
    $destination='downloads/Completed/MF_Transaction_list/karvy_folder/';


  //   echo"in folio";
    $mv=array_unique($allset[$dkey]);
  //   print_r($mv);
  //  $dest="/opt/lampp/htdocs/3tense_broker_new1/downloads/completed/Folio_master_list/karvy_folder/";  ///opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/cam_folder
  // for($i=0;$i<count($mv);$i++){
  //   $srcfile=str_replace('/opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/karvy_folder/','',$mv[$i]);
  //    rename($mv[$i],$dest.$srcfile);
     //unlink($mv[$i]);
     $files1 =scandir('downloads/MF_Transaction_list/karvy_folder/');
     print_r($files1);
     foreach($mv as $key=>$val){
       $srcfile=str_replace('downloads/MF_Transaction_list/karvy_folder/','',$mv[$key]);
       echo $srcfile;
      $flag= in_array($srcfile,$files1);
      echo $flag;
      if($flag){
        if (copy($source.$srcfile, $destination.$srcfile)) {
            $delete[] = $mv[$key];
        }
      }

     }
   }elseif($dkey=='sip_import'){
     $source='downloads/SIP_files_list/karvy_folder/';
     $destination='downloads/Completed/SIP_files_list/karvy_folder/';


   //   echo"in folio";
     $mv=array_unique($allset[$dkey]);
   //   print_r($mv);
   //  $dest="/opt/lampp/htdocs/3tense_broker_new1/downloads/completed/Folio_master_list/karvy_folder/";  ///opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/cam_folder
   // for($i=0;$i<count($mv);$i++){
   //   $srcfile=str_replace('/opt/lampp/htdocs/3tense_broker_new1/downloads/Folio_master_list/karvy_folder/','',$mv[$i]);
   //    rename($mv[$i],$dest.$srcfile);
      //unlink($mv[$i]);
      $files1 =scandir('downloads/SIP_files_list/karvy_folder/');
      print_r($files1);
      foreach($mv as $key=>$val){
        $srcfile=str_replace('downloads/SIP_files_list/karvy_folder/','',$mv[$key]);
        echo $srcfile;
       $flag= in_array($srcfile,$files1);
       echo $flag;
       if($flag){
         if (copy($source.$srcfile, $destination.$srcfile)) {
             $delete[] = $mv[$key];
         }
       }

      }
   }


}
// Delete all successfully-copied files
    foreach ( $delete as $file ) {
        unlink( $file );
    }


    /* Email all the error records in excel format  for cams ,karvy and therir folio,mf transaction and sip records to the admin */
    $rta_type="KARVY";
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
      $to = "pallavi@itechnextgen.com";
         $dtimeForFile=date('Y-m-d-H-i-s');
        $newname="Folio_error_backup-".$dtimeForFile.".xls";
       $eol = PHP_EOL;
       /***** Send Mail*****/
      $email_from="mfautoupload@3tense.com";
      $email_to="pallavi@itechnextgen.com";
      $subject= "FOLIO FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
      $email_message ="Hello,".$eol;
      $email_message.="Following are the details".$eol;
      $email_message.="TYPE:FOLIO File ".$eol;
      $email_message.="Original Name of File: FOLIO File=".$formailFileName.$eol;

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
       $subject= "KARVY MF FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
       $email_message ="Hello,".$eol;
       $email_message.="Following are the details".$eol;
       $email_message.="TYPE:Karvy MF File ".$eol;
       $email_message.="Original Name of File:Karvy  MF File=".$for_mailMfFileName.$eol;

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

       echo "Mailing Flag value of karvy MF error file=".$flag;
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
  $subject= "KARVY SIP FILE WITH ERROR Dated on:".date('Y-m-d-H:i:s');
  $email_message ="Hello,".$eol;
  $email_message.="Following are the details".$eol;
  $email_message.="TYPE:Karvy MF File ".$eol;
  $email_message.="Original Name of File:Karvy  MF File=".  $for_mailSIPFileName.$eol;

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

  echo "Mailing Flag value of karvy SIP error file=".$flag;
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
    echo $file;
    //create folder if not exists
    if (!is_dir($local)) {
        mkdir($local, 0777, true);
    }
    $src = fopen($url, 'r');
    echo $src;
    $dest = fopen($file, 'w');
    echo $dest;
    if($src && $dest) {
      echo"heaven";
        echo 'File: '.$name.' - ';
        echo stream_copy_to_stream($src, $dest) . " bytes copied. \n<br/>";
        return $file;
    } else {
      echo 'hell';
        echo 'Some issue with fopen() <br/>';
        return false;
    }
    //echo $url;exit;
}

function extract_archive($file,$fromPath,$pwd)
{
echo"password=".$pwd;
    $zip = new ZipArchive;
  //  $res = $zip->open($file);
    echo $file;
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
function clients_import($flag,$file,$passfileType)
{
  //echo"hellllllllllllllooooooooooooooo";//exit;

    ini_set('max_execution_time', 0);
    ini_set('memory_limit', '2048M');
    ini_set('upload_max_filesize', '150M');
    ini_set('post_max_size', '150M');//50M

    $uploadedStatus = 0;
    $message = ""; $impMessage = "";$impErrorCol=""; $insertRow = true;
    $imp_data = array();
    if (isset($flag))                               //mmif
    {
        $formailFolioFileName=$file;
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
                echo $file;
                $rta_list=$passfileType;
              //  $fullpath='downloads/Folio_master_list/karvy_folder/'.$file;
                $fileType_info = $this->mime_type($file);
                echo $fileType_info;
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

                  //load the excel library
                  $this->load->library('Excel');
                  //read file from path
                  $objPHPExcel = PHPExcel_IOFactory::load($file);
                  //get only the Cell Collection
                  //$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

                  $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
                 //print_r($maxCell);
                   $excelData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'. $maxCell['column'].$maxCell['row']);
                 }elseif($fileType_info=="application/x-dbf"){  //for DBF
                     $file = $file;
                     $rta_list=$rta_list;
                   //   echo $file ;exit;

                         $dbf = new dbf_class($file );
                          $excelData=array(array());
                         if(is_array($dbf)){
                         $num_rec=$dbf->dbf_num_rec;
                         $field_num=$dbf->dbf_num_field;

                       //  echo $num_rec;exit;

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
                       }

                  //check max row for client import limit
                //  var_dump($maxCell['row']);
                //  $brokerID = $this->session->userdata('user_id');
                  //$data = $this->Clients_model->get_limit($brokerID);
                //  $count = $this->Clients_model->count_client($brokerID);
                //  $data=intval($data->client_limit);
                //  var_dump($data);exit;
                //  $count = intval($count->count);
                //  $remaining = $data - $count;
                  // var_dump($remaining);
                  // echo $maxCell['row'];
                  // if(($maxCell['row']-1)>$remaining)
                  // {
                  //     $message = "Client data exceeds your client limit! Please contact support if you want to increase your limit.";
                  // }
                  // else{
              //  echo"<pre>";print_r($excelData);exit;
                  //$tempo=$excelData;
                  foreach($excelData as $rows)
                  {
                    foreach($rows as $key=>$val)
                    {
                      //echo $key;
                      $temp[$val]=$key;
                    } break;
                  }
          // echo"<pre>";print_r($temp);
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
                                if($rta_list=='karvy_excel'){ //for Karvy RTA for fetching columns

                                  if(strtoupper($cell) == 'INVESTOR NAME'||strtoupper($cell) == 'INVNAME'){
                                    $vals=$temp[$cell];
                                  //  echo $vals;
                                           $cell='Client Name';

                                           $dataColumns[$vals] = $cell;
                                           $countCell++;
                                           $uploadedStatus = 1;
                                           continue;

                                      }elseif(strtoupper($cell) == 'PAN NUMBER'||strtoupper($cell) == 'PANGNO'){
                                        $vals=$temp[$cell];
                                            $cell='Pan No';
                                            $dataColumns[$vals] = $cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'TAX STATUS'||strtoupper($cell) == 'STATUS'){
                                            $vals=$temp[$cell];
                                            $cell='Client Type';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'OCCUPATION DESCRIPTION'|| strtoupper($cell) == 'OCCP_DESC'){
                                            $vals=$temp[$cell];
                                            $cell='Occupation';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                        }elseif(strtoupper($cell) == 'DATE OF BIRTH'||strtoupper($cell) == 'DOB'){
                                            $vals=$temp[$cell];
                                            $cell='Date Of Birth';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                        }elseif(strtoupper($cell) == 'CATEGORYDESC'){
                                            $vals=$temp[$cell];
                                            $cell='Client Category';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'ADDRESS #1'||strtoupper($cell) == 'ADD1'){
                                            $vals=$temp[$cell];
                                            $cell='House/Flat No';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'ADDRESS #2'||strtoupper($cell) == 'ADD2'){
                                            $vals=$temp[$cell];
                                            $cell='Street';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'ADDRESS #3'||strtoupper($cell) == 'ADD3'){
                                            $vals=$temp[$cell];
                                            $cell='Area';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'CITY'){
                                            $vals=$temp[$cell];
                                            $cell='City';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'STATE'){
                                            $vals=$temp[$cell];
                                            $cell='State';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'PINCODE'||strtoupper($cell) == 'PIN'){
                                            $vals=$temp[$cell];
                                            $cell='Pincode';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'MOBILE NUMBER'|| strtoupper($cell) == 'MOBILE'){
                                            $vals=$temp[$cell];
                                            $cell='Mobile';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'PHONE RESIDENCE'||strtoupper($cell) == 'RPHONE'){
                                            $vals=$temp[$cell];
                                            $cell='Telephone';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'EMAIL'){
                                            $vals=$temp[$cell];
                                            $cell='Email ID';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'BROKER CODE'||strtoupper($cell) == 'BROKCODE'){
                                            $vals=$temp[$cell];
                                            $cell='Broker Code';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'FOLIO'||strtoupper($cell) == 'ACNO'){
                                            $vals=$temp[$cell];
                                            $cell='Folio_Number';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'REPORT DATE'|| strtoupper($cell) === 'CRDATE'){  //not confirmed
                                            $vals=$temp[$cell];
                                            $cell='Commencement Date';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK NAME'||strtoupper($cell) === 'BNAME'){  // bank details headers
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
                                          }elseif(strtoupper($cell) === 'IFSC CODE'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_ifsc';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANKACCNO'|| strtoupper($cell) === 'BNKACNO' ){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_acc_no';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'ACCOUNT TYPE'|| strtoupper($cell) === 'BNKACTYPE'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_acc_type';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK ADDRESS #1'||strtoupper($cell) === 'BADD1'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_building';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK ADDRESS #2'|| strtoupper($cell) === 'BADD2'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_road';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK ADDRESS #3'||strtoupper($cell) === 'BADD3'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_area';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK CITY'||strtoupper($cell) === 'BCITY'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_city';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) === 'BANK PINCODE'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_pincode';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }
                                          elseif(strtoupper($cell) == 'BANK STATE'||strtoupper($cell) == 'BSTATE'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_state';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'BANK COUNTRY'||strtoupper($cell) == 'BCOUNTRY'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Bank_address_country';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'Product Code'||strtoupper($cell) == 'PRCODE'){
                                            $vals=$temp[$cell];
                                            $cell='Scheme_Id';// product Id related to the scheme ID of scheme that client have taken
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'Joint Name 1'||strtoupper($cell) == 'JTNAME1'){
                                            $vals=$temp[$cell];
                                            $cell='Joint_name1';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'Joint Name 2'||strtoupper($cell) == 'JTNAME2'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Joint_name2';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'GuardianName'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Guardian Name';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'PAN2'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Joint 1 pan';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'PAN3'){  // bank details headers
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
                                          }elseif(strtoupper($cell) == 'MODEOFHOLD'||strtoupper($cell) == 'Mode of Holding Description'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Mode Of Holding';
                                            $dataColumns[$vals]=$cell;
                                            $countCell++;
                                            $uploadedStatus = 1;
                                            continue;
                                          }elseif(strtoupper($cell) == 'nominee1'){  // bank details headers
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
                                          }elseif(strtoupper($cell) == 'Nominee2'){  // bank details headers
                                            $vals=$temp[$cell];
                                            $cell='Nominee2';
                                            $dataColumns[$vals]=$c