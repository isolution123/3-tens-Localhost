<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require ('application/third_party/PHPMailer/class.phpmailer.php');
/**
 * Created by PhpStorm.
 * User: Karan
 * Date: 3/12/15
 * Time: 3:27 PM
 */

class Mail
{
    //common function for sending mails
    public function send_mail_common($email_to, $subject, $body, $brokerID) {
        $bodyHead = $this->mail_header($brokerID);
        //$bodyHead = $this->mail_header('Astar Securities <sudarsh@astarsecurities.com>');
        $bodyFoot = $this->mail_footer();
        //combine the header, body, and footer
        $message = $bodyHead.$body.$bodyFoot;

        $mail = new PHPMailer();
        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        //$to_address = "amitvadher140@gmail.com";
        //$mail_host = "localhost";  //Change this to your actual Domain name.
        //$from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        //$from_password = "P@ssword1234";  //Change this to the above email addresses password.
        //$name = "3tense";
        $mail_host = "localhost";
        $from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "P@ssword1234";  //Change this to the above email addresses password.
        //$from_address = "amitvadher140@gmail.com";  //Change this to the email address you will use to send and authenticate with.
        //$from_password = "0903@gmail1993";  //Change this to the above email addresses password.
        $name = "3Tense";

        //////////////////////////////////////////
        // DO NOT CHANGE ANYTHING PAST THIS LINE//
        //////////////////////////////////////////

        $from_name = $name;
        $reply_to = $email_to;
        $msg_body = $message;
        $mail->IsSMTP();
        $mail->Host = $mail_host;
       // $mail->SMTPAuth = true;
        $mail->Username = $from_address;
        $mail->Password = $from_password;

        $mail->From = $from_address;
        $mail->FromName = $from_name;
        $mail->AddReplyTo($reply_to);
        $mail->AddAddress($to_address);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg_body;
        
        if(!$mail->Send())
        {
            $msg = "Message could not be sent. <p>";
            $msg = $msg. "Mailer Error: " . $mail->ErrorInfo;
        }
        else
        {
            
            $msg =  "success";
            
        }
        
        return $msg;
    }

    //function for sending reminder mails
    public function send_mail($email_to, $result, $brokerID, $type)
    {
        $bodyHead = $this->mail_header($brokerID);
        $bodyFoot = $this->mail_footer();
        if($type == 'Broker')
            //$bodyMain = $this->reminder_broker($result);
            $bodyMain = $this->reminder_broker_segreg($result); //edited - Akshay R - 2017-08-23
        else
            $bodyMain = $this->reminder_client($result);

        $message = $bodyHead.$bodyMain.$bodyFoot;
        $mail = new PHPMailer();
        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        $mail_host = "localhost";  //Change this to your actual Domain name.
        $from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "P@ssword1234";  //Change this to the above email addresses password.
        $subject = '3Tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.
        $name = "3Tense";

        /*$mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";*/


        //////////////////////////////////////////
        // DO NOT CHANGE ANYTHING PAST THIS LINE//
        //////////////////////////////////////////

        $from_name = $name;
        $body = $message;
        $reply_to = $from_address;
        $msg_body = $body;

        $mail->IsSMTP();
        $mail->Host = $mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = $from_address;
        $mail->Password = $from_password;

        $mail->From = $from_address;
        $mail->FromName = $from_name;
        $mail->AddReplyTo($reply_to);
        $mail->AddAddress($to_address);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg_body;

        if(!$mail->Send())
        {
            $msg = "Message could not be sent. <p>";
            $msg = $msg. "Mailer Error: " . $mail->ErrorInfo;
        }
        else
        {
            $msg =  "success";
        }
        return $msg;
    }


        //function for sending single reminder mails
    public function send_mail2($email_to, $result, $brokerID, $type,$broker_result)
    {
        $bodyHead = $this->mail_header($brokerID);
        $bodyFoot = $this->mail_footer();
        if($type == 'Broker')
            $bodyMain = $this->reminder_broker($result);
        else
            $bodyMain = $this->reminder_client($result);

        $message = $bodyHead.$bodyMain.$bodyFoot;

        $mail = new PHPMailer();
        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        $mail_host = "localhost";  //Change this to your actual Domain name.
        $from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "P@ssword1234";  //Change this to the above email addresses password.
        if($result[0]->reminder_type=="Birthday Reminder")
        {
                $subject = 'Happy Birthday!!';  //Change this to your own email message subject.
        }
        else if($result[0]->reminder_type=="Anniversary Reminder")
        {
                $subject = 'Happy Anniversary!!';  //Change this to your own email message subject.
        }
        else
        {
        $subject = '3Tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.
        }
        $name = "3Tense";

        /*$mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";*/


        //////////////////////////////////////////
        // DO NOT CHANGE ANYTHING PAST THIS LINE//
        //////////////////////////////////////////

        $from_name = $name;
        $body = $message;
        $reply_to = $broker_result->email_id;
        $msg_body = $body;

        $mail->IsSMTP();
        $mail->Host = $mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = $from_address;
        $mail->Password = $from_password;

        $mail->From = $broker_result->email_id;
        $mail->FromName = $broker_result->name;
        $mail->AddReplyTo($reply_to);
        $mail->AddAddress($to_address);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg_body;

        if(!$mail->Send())
        {
            $msg = "Message could not be sent. <p>";
            $msg = $msg. "Mailer Error: " . $mail->ErrorInfo;
        }
        else
        {
            $msg =  "success";
        }
        return $msg;
    }

    function reminder_broker($result)
    {
        //$body = '<div style="border:1px solid #4f8edc; padding:30px">
        $body = '<div>
            <table style="border: solid 1px #4f8edc;  width:100%" cellpadding="6" cellspacing="0">
                <tr>
                    <th style="background-color:#4f8edc; color:#fff; width:8%">Rem ID</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Reminder Type</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Client Name</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Date Of Reminder</th>
                    <th style="background-color:#4f8edc; color:#fff; width:20%">Reminder Message</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Status</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Next Date</th>
                    <th style="background-color:#4f8edc; color:#fff; width:12%">Remark</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">User</th>
                </tr>';
        foreach($result as $row)
        {
            $body = $body.'
                <tr style="text-align:center">
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_id.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_type.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->client_name.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->date_of_reminder.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_message.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_status.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->next_date.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->remark.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->concern_user.'</td>
                </tr>';
        }
        $body .= '</div>';
        return $body;
    }

    function reminder_client($result)
    {
        //$body = '<div style="border:1px solid #4f8edc; padding:30px">\
	if(!(trim($result[0]->reminder_type)=="Birthday Reminder" || trim($result[0]->reminder_type)=="Anniversary Reminder"))
          {
        $body = '<div>
            <table style="border: solid 1px #4f8edc; width:100%" cellpadding="6">
                <tr>
                    <th style="background-color:#4f8edc; color:#fff; width:20%">Reminder Type</th>
                    <th style="background-color:#4f8edc; color:#fff; width:20%">Client Name</th>
                    <th style="background-color:#4f8edc; color:#fff; width:20%">Date Of Reminder</th>
                    <th style="background-color:#4f8edc; color:#fff; width:40%">Reminder Message</th>
                </tr>';
        foreach($result as $row)
        {
            $body = $body.'
                <tr style="text-align:center">
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_type.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->client_name.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->date_of_reminder.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_message.'</td>
                </tr>';
	}
        }
           else
           {
           	$body = '
           	<h2 style="text-align: center; color: linear-gradient(to right, #871dc7 20%, #ff9800 40%, #1c19a7 60%, #ffc107 80%);   background-size: 200% auto;  color: #000;  background-clip: text;  text-fill-color: transparent;  -webkit-background-clip: text;  -webkit-text-fill-color: transparent; ">'.$result[0]->reminder_message.'</h2>';
           }
        
        $body .= '</div>';
        return $body;
    }

    function mail_header($brokerID)
    {
        $logo = "";
        /*if((glob("uploads/brokers/".$brokerID."/*.*"))) {
            $logo = glob("uploads/brokers/".$brokerID."/*.*")[0];
        }*/
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }
            $logo=base_url("uploads/brokers/".$logo);
        $header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title></title>
                </head>
                <body>
                    <div style="width:100%"><img src = "'.$logo.'" alt="Logo" style="float:right; max-height: 80px;"/><br /><br /></div>
                    <div style = "margin-top:80px !important; border-top:3px solid #22BCE5; font-family:Arial;font-size:10pt">&nbsp;</div>';

        return $header;
    }

    public function mail_footer()
    {
        $footer = '<div >
            <p>';
                //For queries: <a style="color: #22BCE5" href="mailto:customercare@3tense.com">customercare@3tense.com</a>
                $footer .= '<br />
            </p>
        </div>
    </body>
</html>';

        return $footer;
    }
    
    
    
    
    
    /* edited AkshayR - 2017-08-23 */
    public function sendMailTemplates($email_to, $result, $brokerID, $type, $broker_result){

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        $mail_host = "localhost";  //Change this to your actual Domain name.
        $from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "P@ssword1234";  //Change this to the above email addresses password.
        
        $bodyHead = $this->mail_header($brokerID);
        $bodyFoot = $this->mail_footer();
        if($type == 'Broker' && $result[0]->reminder_type !== "Birthday Reminder" && $result[0]->reminder_type !== "Anniversary Reminder"  && $result[0]->reminder_type !== "Personal")
        {
            // $bodyMain = $this->reminder_broker($result); //Commented edited
        }
        else
        {
            // $bodyMain = $this->reminder_client($result); //Commented edited
        } //Commented edited

        if($result[0]->reminder_type === "Birthday Reminder") //change template asper reminder type
        {
            include "./application/views/broker/templatesEmail/emailTemplateGreeting.php"; //Birthday Template
            $bodyMain =  $emailTemplateGreetingOut;  /*|'<div><img src="cid:HB_img" alt="Birthday Greeting is Loading...."></div>';*/
        }

        elseif($result[0]->reminder_type === "Anniversary Reminder") //change template asper reminder type
        {
            include "./application/views/broker/templatesEmail/emailTemplateAnniversary.php"; //Anniversary Template
            $bodyMain =  $TemplateAnniversaryOut;  /*|'<div><img src="cid:HB_img" alt="Anniversary Greeting is Loading...."></div>';*/
        }
        elseif ($result[0]->reminder_type !== "Birthday Reminder" && $result[0]->reminder_type !== "Anniversary Reminder")
        {

            include "./application/views/broker/templatesEmail/emailTemplateReminders.php"; //Template WIProgress
            $bodyMain = $emailTemplateReminderOut; /*$emailTemplateReminderOut;*/  /*|'<div><img src="cid:HB_img" alt="Anniversary Greeting is Loading...."></div>';*/ 

            /*Not able to  get value of $result[0]->next_date*/
        }        

        $message = $bodyHead.$bodyMain.$bodyFoot; /*"<pre>".print_r($result)."</pre>".*/ /*"<div>Testing this mail functionality. Please do not reply</div>".*/



        $mail = new PHPMailer(); // create a new object
        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

//         $to_address = /*$email_to*/ "sandmehta2014@gmail.com";  //Change this to the email address you will be receiving your notices.
//         $mail_host = "localhost" /*"smtp.gmail.com"*/;  //Change this to your actual Domain name.
//         $from_address = "no-reply@3tense.com" /*"sandmehta2014@gmail.com"*/;  //Change this to the email address you will use to send and authenticate with.
//         $from_password = "P@ssword1234" /*"Server#1234"*/;  //Change this to the above email addresses password.
        

// /*        $subject = '3Tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.*/
//         if($result[0]->reminder_type === "Birthday Reminder") //change template asper reminder type
//         {
//             $subject = 'Our Birthday Greeting to you.'/*.date('d/m/Y').*/;  //Change this to your own email message subject.
//         }
//         elseif($result[0]->reminder_type === "Anniversary Reminder")
//         {
//             $subject = 'Our Anniversary Greeting to you.'/*.date('d/m/Y').*/;  //Change this to your own email message subject.
//         }           
//         else
//         {
//             if (stripos($result[0]->reminder_type, "reminder") !== false) {
//                  $subject = $result[0]->reminder_type.' update from 3tense.'; 
//             }
//             else{
//                 $subject = $result[0]->reminder_type.' Reminder update from 3tense.';  //Change this to your own email message subject. "'for date - '.date('d/m/Y').'"            
//             }
//         }
//         $name = "3Tense";

//         /*$mail_host = "itechnextgen.com";
//         $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
//         $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
//         $name = "itechnextgen";*/


//         //////////////////////////////////////////
//         // DO NOT CHANGE ANYTHING PAST THIS LINE//
//         //////////////////////////////////////////

//         $from_name = $name;
//         $body = $message;
// /*        $body.= '<img src="cid:HB_img" alt="Birthday-Greeting">';*/
//         $reply_to = /*$broker_result->email_id*/ $from_address;
//         $msg_body = $body;


//         $mail->IsSMTP(); // enable SMTP
//         /*$mail->SMTPDebug = 1;*/ // debugging: 1 = errors and messages, 2 = messages only //can cause json data failure
//         $mail->SMTPAuth = true; // authentication enabled
// /*        $mail->Port = 465;*/ // or 587

//         /*$mail->SetFrom("example@gmail.com");*/


//         // $mail->Host = $mail_host;
//         // $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
//       // $mail->Host = "ssl://smtp.gmail.com";  // secure transfer enabled REQUIRED for Gmail
//         //   $mail_host = "itechnextgen.com";
//         $mail->Username = $from_address;
//         $mail->Password = $from_password;


        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        $mail_host = "localhost";  //Change this to your actual Domain name.
        $from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "P@ssword1234";  //Change this to the above email addresses password.
        if($result[0]->reminder_type=="Birthday Reminder")
        {
                $subject = 'Happy Birthday!!';  //Change this to your own email message subject.
        }
        else if($result[0]->reminder_type=="Anniversary Reminder")
        {
                $subject = 'Happy Anniversary!!';  //Change this to your own email message subject.
        }
        else
        {
            $subject = '3Tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.
        }
        $name = "3Tense";

        /*$mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";*/


        //////////////////////////////////////////
        // DO NOT CHANGE ANYTHING PAST THIS LINE//
        //////////////////////////////////////////

        $from_name = $name;
        $body = $message;
        $reply_to = $broker_result->email_id;
        $msg_body = $body;

        $mail->IsSMTP();
        $mail->Host = $mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = $from_address;
        $mail->Password = $from_password;

        $mail->From = $broker_result->email_id;
        $mail->FromName = $broker_result->name;
        $mail->AddReplyTo($reply_to);
        $mail->AddAddress($to_address);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg_body;
        
        if($result[0]->reminder_type === "Birthday Reminder")   //change template asper reminder type
        {
            $mail->AddEmbeddedImage('./assets/users/wishMail/HBday.jpg', 'HB_img'); //Add inline files/images in the mail.
        }
        elseif($result[0]->reminder_type === "Anniversary Reminder")    //change template asper reminder type
        {
            $mail->AddEmbeddedImage('./assets/users/wishMail/happy-annivers.jpg', 'HA_img'); //Add inline files/images in the mail.
        }

        if(!$mail->Send())
        {
            $msg = "Message could not be sent. <p>";
            $msg = $msg. "Mailer Error: " . $mail->ErrorInfo;
        }
        else
        {
            $msg =  "success";
        }
        return $msg;
        
    }
    /* edited */


    /*edited 2 - Akshay R - 2017-08-23 */                
   function reminder_broker_segreg($result)
    {
        $previousReminderType="";
        $body="";

        foreach($result as $row)
        {
            if ($row->reminder_type != $previousReminderType ) {
                $body = $body. '</table ><br/><br/><br/><table style="border: solid 1px #4f8edc;  width:100%;"  align="center" cellpadding="6" cellspacing="0"> <tr style="text-align:center;"><th colspan="9" width = "100%" style="text-align:left;"> <span style="font-weight:bold; font-size:150%; text-align:left; padding-left:5px  ">'.$row->reminder_type.'</span> </th> </tr> 
                <tr>
                   
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Client Name</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Date Of Reminder</th>
                    <th style="background-color:#4f8edc; color:#fff; width:20%">Reminder Message</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Status</th>
                    <th style="background-color:#4f8edc; color:#fff; width:10%">Next Date</th>
                    <th style="background-color:#4f8edc; color:#fff; width:12%">Remark</th>
                   
                </tr>
                ';
            }

            $body = $body.'
                <tr style="text-align:center">
               
                    <td style="border: solid 1px #4f8edc;">'.$row->client_name.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->date_of_reminder.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_message.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->reminder_status.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->next_date.'</td>
                    <td style="border: solid 1px #4f8edc;">'.$row->remark.'</td>
               
                </tr>';

                $previousReminderType= $row->reminder_type;

        }
        $body .= '</div>';
        return $body;
    }
    /*edited 2*/

}
