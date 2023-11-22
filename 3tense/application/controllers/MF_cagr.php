<?php
// error_reporting(0);
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MF_cagr extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        //load models
        $this->load->helper('form');
        $this->load->model('Users_model', 'user');
        $this->load->model('Mutual_funds_model', 'mf');
        $this->load->model('Families_model', 'family');
        //$this->load->library('Mail', 'mail');
    }
    
    function send_cagr_mail()
    {
        $broker_email_1 = $this->db->query("SELECT email_id FROM `users` where id = '0004'"); 
        $broker_email_2 = $this->db->query("SELECT email_id FROM `users` where id = '0009'");
        
        $email1 =$broker_email_1->result_array();
        $email2 =$broker_email_2->result_array();
    
        $this->send_cagr_mail_by_broker("0004",$email1[0]['email_id']);
        $this->send_cagr_mail_by_broker("0009",$email2[0]['email_id']);
       
       
    }
    function send_cagr_mail_update_data()
    {
    
        $query1 = $this->db->query("TRUNCATE TABLE `mutual_fund_valuation_cagr`");
        $query2 = $this->db->query("INSERT INTO `mutual_fund_valuation_cagr` (transaction_id, cagr_date, mf_cagr) 
                                 SELECT mfv.transaction_id,mfv.c_nav_date,mfv.mf_cagr 
                                 FROM `mutual_fund_valuation` mfv 
                                 INNER JOIN `mutual_fund_transactions` mft 
                                     ON mfv.transaction_id = mft.transaction_id 
                                 INNER JOIN `mutual_fund_schemes` mfs 
                                     ON mft.mutual_fund_scheme = mfs.scheme_id 
                                 INNER JOIN `mf_scheme_types` mfst 
                                     ON mfs.scheme_type_id = mfst.scheme_type_id 
                                 WHERE mfst.scheme_target_value <= mfv.mf_cagr AND 
                                     mfst.scheme_target_value <=mfv.mf_abs AND 
                                     mft.mutual_fund_type <> 'DIV' AND
                                     (mfv.broker_id = '0004' OR mfv.broker_id = '0009') 
                                     ORDER BY transaction_id ASC");
    
    }

    function send_cagr_mail_by_broker($broker_id,$email_id){
        
        ini_set('memory_limit', '-1');
        
        $data = $this->mf->get_mf_cagr_mail_data($broker_id);
        
        $res['records'] = $data;
        
        
        $this->load->library('email');
        
        $this->email->from('no-reply@3tense.com');
        
        $mail_data = $this->load->view('mf_cagr_mail',$res,true);
        $message = $mail_data;
        
        $this->email->set_mailtype("html");
        
        // replace email by $email_id after test
        $this->email->to($email_id);

    echo $message; 
        
        $this->email->subject('CAGR Trigger Report');
        $this->email->message($message);
             
        $this->email->send();
         echo $email_id;  

    }
    
}
