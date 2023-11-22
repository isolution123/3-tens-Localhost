<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scripts_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_reminder_days()
    {
/*        $this->db->from('reminder_days');
        $this->db->order_by('reminder_days_id', 'asc');
        $query = $this->db->get();
        return $query->result();
  */      
          $query = "select rm.* from reminder_days rm inner join users u on u.id=rm.broker_id and u.status=1 order by reminder_days_id asc "; //edited while segregation. - Raul - 2017-08-23
        $result = $this->db->query($query);
        return $result->result();
    }
        function get_reminder_days_Without_0174()
    {
/*        $this->db->from('reminder_days');
        $this->db->order_by('reminder_days_id', 'asc');
        $query = $this->db->get();
        return $query->result();
  */      
          $query = "select rm.* from reminder_days rm inner join users u on u.id=rm.broker_id and u.status=1 and u.Id!=0174 order by reminder_days_id asc "; //edited while segregation. - Raul - 2017-08-23
        $result = $this->db->query($query);
        return $result->result();
    }
    
      function get_reminder_days_0174()
    {
/*        $this->db->from('reminder_days');
        $this->db->order_by('reminder_days_id', 'asc');
        $query = $this->db->get();
        return $query->result();
  */      
          $query = "select rm.* from reminder_days rm inner join users u on u.id=rm.broker_id and u.status=1 and u.Id=0174 order by reminder_days_id asc "; //edited while segregation. - Raul - 2017-08-23
        $result = $this->db->query($query);
        return $result->result();
    }

    function reminder_counter()
    {
        $this->db->from('reminder_script_counter');
        $query = $this->db->get();
        return $query->row();
    }

    function get_reminder_details($brokerID)
    {
        $query = "select reminder_id, reminder_type, client_name, ".
            "DATE_FORMAT(reminder_date, '%d/%m/%Y') as 'date_of_reminder', reminder_message, reminder_status, ".
            "next_date, remark, concern_user from today_reminders where broker_id = '".$brokerID."' and ".
            "(client_id is not null OR client_name IS NOT NULL) and reminder_type != 'Shares Negative Balance' and ".
            "(DateDiff(reminder_date, CURRENT_DATE()) <=0 or reminder_date is NULL) and ".
            "((reminder_status != 'Snooze' or reminder_status is NULL) or ".
            "(reminder_status = 'Snooze' and DateDiff(next_date, CURRENT_DATE()) <= 0)) ".
            "order by reminder_type, reminder_date desc"; //edited while segregation. - Raul - 2017-08-23
        $result = $this->db->query($query);
        return $result->result();
    }

    function broker_email($broker_id)
    {
        $this->db->select('email_id');
        $this->db->from('users');
        $this->db->where('id', $broker_id);
        $this->db->where('status', '1'); //added by Salmaan - 2018-02-19
        $query = $this->db->get();
        if($query) {
            return $query->row();
        } else {
            return false;
        }
    }

    function update_reminder_counter($date)
    {
        $data = array('script_date' => $date, 'script_status' => 1);
        $this->db->update('reminder_script_counter', $data);
    }

    function update_reminder($remParam)
    {
        $this->db->query("call sp_reminder(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)", $remParam);
    }

    function delete_complete_reminder($where)
    {
        $this->db->delete('complete_reminders', $where);
    }

    function get_maturity_details_for_reminder($scriptDate, $broker_id)
    {
        $query = "select psm.status, im.policy_num, max(pm.maturity_date) as 'maturity_date', im.mat_type, ".
            "plan_name from insurances as im inner join ins_plans as ipn on ipn.plan_id = im.plan_id inner join ".
            "premium_status as psm on im.status = psm.status_id inner join premium_maturities as pm ".
            "on im.policy_num = pm.policy_num where psm.status ".
            "NOT IN ('Grace','Lapsed','Matured','Surrender','Paid Up Cancellation') group by ".
            "im.policy_num, psm.status, im.mat_type, ipn.plan_name, im.broker_id having ".
            "DATEDIFF('".$scriptDate."', max(pm.maturity_date)) >= 0 and im.broker_id = '".$broker_id."'";
        $result = $this->db->query($query);
        return $result->result();
    }

    function update_insurance($data, $condition)
    {
        $this->db->update('insurances', $data, $condition);
    }

    function get_fund_mat_value($policy_num, $brokerID)
    {
        $query = "select fund_value, max(pm.Amount) as mat_amt from insurances im inner join ".
            "premium_maturities pm on im.policy_num = pm.policy_num where ".
            "im.policy_num = '".$policy_num."' AND im.broker_id = '".$brokerID."' group by fund_value";
        $result = $this->db->query($query);
        //$result->next_result();
        //$result->free_result();
        if($result) {
            return $result->result();
        } else {
            return $this->db->error;
        }
    }

    function update_prem_maturity($dataPrem, $conditionPrem) {
        $query = "UPDATE premium_maturities SET amount = ".$dataPrem." WHERE ".$conditionPrem;
        if(!$this->db->query($query)) {
            return $this->db->error();
        } else {
            return true;
        }
    }

    function insert_reminders($dataQuery)
    {
        $query = "insert into today_reminders (reminder_type, client_id, client_name, ".
            "reminder_date, reminder_message, broker_id) ".$dataQuery;
        $this->db->query($query);
    }

    function complete_insurance($where)
    {
        $result = $this->db->query("call completeInsurance (?)", $where);
        $result->next_result();
        $result->free_result();
    }

    function get_policy_paidUp($scriptDate, $brokerID)
    {
        $query = "select policy_num, mode_name, paidup_date from insurances im inner join premium_status psm on ".
            "im.status = psm.status_id inner join premium_modes pm on im.mode = pm.mode_id where ".
            "psm.status not in ('Grace','Lapsed','Matured','Paid Up','Surrender','Paid Up Cancellation')".
            "and DATEDIFF('". $scriptDate."', paidup_date) >= 0 AND paidup_date <= grace_due_date ".
            "and paidup_date < next_prem_due_date and im.broker_id = '".$brokerID."'";
        $result = $this->db->query($query);
        return $result->result();
    }

    function get_policy_grace($scriptDate, $brokerID)
    {
        $query = "select policy_num from insurances im inner join premium_status psm on ".
            "im.status = psm.status_id where psm.status ".
            "not in ('Grace','Lapsed','Matured','Paid Up','Surrender','Paid Up Cancellation')".
            "and DATEDIFF('". $scriptDate."', next_prem_due_date) >= 0 and im.broker_id = '".$brokerID."'";
        $result = $this->db->query($query);
        return $result->result();
    }

    function get_policy_lapse($scriptDate, $brokerID)
    {
        $query = "select policy_num from insurances im inner join premium_status psm on ".
            "im.status = psm.status_id where psm.status ".
            "not in ('Lapsed','Matured','Paid Up','Surrender','Paid Up Cancellation') ".
            "and im.mode != 5 ".
            "and DATEDIFF('". $scriptDate."', grace_due_date) > 0 and im.broker_id = '".$brokerID."'";
        $result = $this->db->query($query);
        return $result->result();
    }

    function get_status_id($status)
    {
        $this->db->select('status_id');
        $this->db->where('status', $status);
        $this->db->from('premium_status');
        $query = $this->db->get();
        if($query) {
            return $query->row();
        } else {
            return $this->db->error();
        }
    }
} 