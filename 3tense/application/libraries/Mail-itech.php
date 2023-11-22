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
        $bodyFoot = $this->mail_footer();
        //combine the header, body, and footer
        $message = $bodyHead.$body.$bodyFoot;

        $mail = new PHPMailer();
        ////////////////////////////////////////////////////////////////
        // Customize the following 5 lines with your own information. //
        ////////////////////////////////////////////////////////////////

        $to_address = $email_to;  //Change this to the email address you will be receiving your notices.
        //$mail_host = "localhost";  //Change this to your actual Domain name.
        //$from_address = "no-reply@3tense.com";  //Change this to the email address you will use to send and authenticate with.
        //$from_password = "P@ssword1234";  //Change this to the above email addresses password.
        //$name = "3tense";
        $mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";

        //////////////////////////////////////////
        // DO NOT CHANGE ANYTHING PAST THIS LINE//
        //////////////////////////////////////////

        $from_name = $name;
        $reply_to = $email_to;
        $msg_body = $message;

        $mail->IsSMTP();
        $mail->Host = $mail_host;
        //$mail->SMTPAuth = true;
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
        $subject = '3tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.
        $name = "3tense";

        $mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";


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
        $subject = '3tense Reminder Updates '.date('d/m/Y');  //Change this to your own email message subject.
        $name = "3tense";

        $mail_host = "itechnextgen.com";
        $from_address = "admin@itechnextgen.com";  //Change this to the email address you will use to send and authenticate with.
        $from_password = "itechAdmin!!12";  //Change this to the above email addresses password.
        $name = "itechnextgen";


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
        $body = '<div style="border:1px solid #4f8edc; padding:30px">
            <table style="border: solid 1px #4f8edc;" cellpadding="6">
                <tr>
                    <th style="background-color:#4f8edc; color:#fff; width:150px">Reminder Type</th>
                    <th style="background-color:#4f8edc; color:#fff; width:150px">Client Name</th>
                    <th style="background-color:#4f8edc; color:#fff; width:150px">Date Of Reminder</th>
                    <th style="background-color:#4f8edc; color:#fff; width:300px">Reminder Message</th>
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
                <head>
                    <title></title>
                </head>
                <body>
                    <img src = "'.$logo.'" alt="Logo" style="float:right;"/><br /><br />
                    <div style = "border-top:3px solid #22BCE5; font-family:Arial;font-size:10pt">&nbsp;</div>';

        return $header;
    }

    public function mail_footer()
    {
        $footer = '<div >
            <p>
                <br />
                <br />';
                //For queries: <a style="color: #22BCE5" href="mailto:customercare@3tense.com">customercare@3tense.com</a>
                $footer .= '<br />
                <br />
            </p>
        </div>
    </body>
</html>';

        return $footer;
    }
}
