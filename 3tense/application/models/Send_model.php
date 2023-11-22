<?php
class Send_model extends CI_Model{
    public function __construct()
    {
        $this->load->database();
        //$this->load->library('Mail', 'mail');
    }

    function reset_member(){

        $name = $this->input->post('name');
        $mail=$this->input->post('email');
        $this->db->where('username',$name);
        $this->db->where('email_id',$mail);
        $result=$this->db->get('users');
        //print_r($result->row());

        if($result->num_rows()>0)
        {
            $row=$result->row();

            $id=$row->id;
            $username=$row->username;
            $hash=sha1($id.'3tense');
            $value=array('pwd_reset'=>'1');
            $this->db->where('username',$name);
            if( $this->db->update('users',$value))
            {
                /*$Ci = get_instance();
                $Ci->load->library('email');
                $config['protocol'] = "smtp";
                $config['smtp_host'] = "ssl://smtp.gmail.com";
                $config['smtp_port'] = "465";
                $config['smtp_user'] = "pradipbhagat11155@gmail.com";
                $config['smtp_pass'] = "Ashabhagat11155";
                $config['charset'] = "utf-8";
                $config['mailtype'] = "html";
                $config['newline'] = "\r\n";
                $Ci->email->initialize($config);
                $Ci->email->from('pradipbhagat11155@gmail.com', 'Pradip');
                $list = array($mail);
                $Ci->email->to($list);
                $this->email->reply_to('pradipbhagat11155@gmail.com', 'Explendid Videos');
                $Ci->email->subject('This is an email test ');
                //$Ci->email->message(' Userid is '.$id.'<br><a href="'.site_url('broker/forget/reset').'?hash='.$hash.'">Link</a><br>'.$id );
                $Ci->email->message(' Userid is '.$id.'<br><a href="'.site_url('client/Clients_forget/reset').'?hash='.$hash.'">Link</a><br>'.$id );
                //$Ci->email->send();*/
                
                //send mail to user
                 $subject = "Password Reset";
                 $body = "<p>We got your request for resetting your 3Tense account password</p>";
                 $body .= "<p>Please click on the link below to reset your password </p>";
                 $body .= "<a href=".base_url('broker/forget/reset').'?hash='.$hash.">".base_url('broker/forget/reset').'?hash='.$hash."</a></p>";
                 $body .= "<p><br>If you haven't requested password reset of your Account,<br>Please contact our Team </p>";
                 //$this->mail->send_mail_common($row->email_id, $subject, $body, $id);
                 $result = $this->mail->send_mail_common($row->email_id, $subject, $body, $id);
                
                //$result == "success"
                if($result == "success") {
                    return true;
                } else {
                    return $result;
                }
                
                /*if($Ci->email->send()) {
                    return true;
                } else {
                    var_dump("Mail not sent");
                }*/
                
            } else {
                return $this->db->error();
            }
        }
        else{
            return false;
        }
    }

     function authentication_mail($email,$username,$authcode,$broker_id){
                //send mail to user
                  $now = new DateTime();
                $timestring = $now->format('d-m-Y h:i');
                 $subject = "Login Auth: ".$username;
                 $body = "<p>We got your request for login in 3Tense account</p>";
                 $body .= "<p>User Name : <b>".$username ."</b></p>";
                 $body .= "<p>Authenication Password : <b>".$authcode."</b></p>";
                 
                
                 $body .= "<p>Datetime : <b>".$timestring."</b></p>";
                 
                 //$this->mail->send_mail_common($row->email_id, $subject, $body, $id);
                 $result = $this->mail->send_mail_common($email, $subject, $body, $broker_id);
                
                //$result == "success"
                if($result == "success") {
                    
                    return true;
                } else {
                    return $result;
                }

    }

    function resetpass($hash=''){
        //$hash = $this->uri->segment('3');
        #$hash = $this->input->get('hash');  //Codeigniter
        //$hash = $_GET['hash'];   -- Core PHP
        # $this->reset_pass($hash);
        $this->db->where('sha1(CONCAT(id,"3tense")) = "'.$hash.'"');
        $this->db->where('pwd_reset',1);
        $result=$this->db->get('users');
        //print_r($this->db->queries);
        if($result->num_rows()>0)
        {

            return $result->row();
        }
        else
        {
            return false;
        }
    }

    function reset_pass($id,$pass){
        $value=array('pwd_reset'=>'0','password'=>sha1($pass));
        $this->db->where('id',$id);
        if($this->db->update('users',$value))
        {
            return true;
        }
        else{
            return false;
        }
    }

}
