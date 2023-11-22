<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reminders_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /*function get_reminders($conditions)
    {
        $this->db->select('reminder_id, reminder_type, client_id, client_name, DATE_FORMAT(reminder_date, "%d/%m/%Y") as reminder_date,
        reminder_status, reminder_message, remark, concern_user, broker_id');
        $this->db->from('today_reminders');
        $this->db->where($conditions);
        $this->db->order_by('reminder_id desc');
        $query = $this->db->get();
        return $query->result();
    }*/
    // Akshay R - 2017-08-23
    function get_reminders($conditions)
    {
        $this->db->select('reminder_id, reminder_type, client_id, client_name, mail_sent_status, DATE_FORMAT(reminder_date, "%d/%m/%Y") as reminder_date,
        reminder_status, reminder_message, remark, concern_user, broker_id');
        $this->db->from('today_reminders');
        //$this->db->limit(5000);
        $this->db->where($conditions);
        $this->db->order_by('reminder_id desc');
        $query = $this->db->get();
        
        return $query->result();
        
    }

    //function to get complete reminders - for end of day EOD
    function get_complete_reminders($conditions)
    {
        $this->db->select('reminder_id, reminder_type, client_id, client_name, DATE_FORMAT(reminder_date, "%d/%m/%Y") as date_of_reminder,
        reminder_status, reminder_message, next_date, remark, concern_user, broker_id');
        $this->db->from('complete_reminders');
        $this->db->where($conditions);
        $query = $this->db->get();
        return $query->result();
    }

    function dash_reminder_list($conditions, $limit)
    {
        $this->db->select('reminder_id, reminder_type, title,client_id, client_name, DATE_FORMAT(reminder_date, "%d/%m/%Y") as reminder_date,
        reminder_status,  reminder_message ,attachment_url
        concern_user, broker_id');
        $this->db->from('today_reminders');
        $this->db->where($conditions);
        $this->db->limit($limit);
        $this->db->order_by('today_reminders.reminder_date', 'desc');
        $this->db->order_by('today_reminders.reminder_id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }
      function dash_reminder_list_for_api($conditions, $limit)
    {
        $this->db->select('reminder_id, reminder_type, title,client_id, client_name, created_datetime as reminder_date,
        reminder_status,  reminder_message ,attachment_url
        concern_user, broker_id');
        $this->db->from('today_reminders');
        $this->db->where($conditions);
        $this->db->limit($limit);
        $this->db->order_by('today_reminders.reminder_date', 'desc');
        $this->db->order_by('today_reminders.reminder_id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    function get_reminder_days($condition = '1')
    {
        $this->db->from('reminder_days');
        $this->db->order_by('reminder_days_id', 'asc');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function update_reminder_days($data, $condition)
    {
        if(!($this->db->update('reminder_days', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function add_reminder($data)
    {
        if(!($this->db->insert('today_reminders', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function get_rem_details($conditions)
    {   
        $this->db->select('reminder_id, reminder_type, client_id, client_name, DATE_FORMAT(reminder_date, "%d/%m/%Y") as reminder_date,title,
        
         attachment_url,reminder_message, remark');
        $this->db->from('today_reminders');
        $this->db->where($conditions);
        $query = $this->db->get();
        return $query->row();
    }

    function snooze_reminder($data, $condition)
    {
        if(!($this->db->update('today_reminders', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function complete_reminder($data, $condition)
    {
        if(!($this->db->update('today_reminders', $data, $condition))) {
            $error = $this->db->error();
            $status = $error;
        }
        elseif(!($this->db->delete('today_reminders', $condition)))
        {
            $error = $this->db->error();
            $status = $error;
        }
        else {
            $status =  $this->db->affected_rows();
        }
        return $status;
    }

    function get_client_reminder($reminder_id)
    {
        $query = "select reminder_type, tr.client_name, c.email_id, ".
            "Date_Format(reminder_date, '%d/%m/%Y') as 'date_of_reminder', reminder_message from ".
            "today_reminders tr inner join clients c on tr.client_id = c.client_id where reminder_id = ".$reminder_id;
        $result = $this->db->query($query);
        return $result->result();
    }


    /* Reminder analyzer functions */

    /* Using Server-side script for Datatables because data may become large */

    function get_all_reminders($sWhere, $myWhere, $sOrder, $sLimit, $sTable) {
        if($sTable == 'complete_reminders') {
            $sQuery = "
                SELECT SQL_CALC_FOUND_ROWS r.reminder_id, r.reminder_type, r.client_id, r.client_name,
                DATE_FORMAT(r.reminder_date, '%d/%m/%Y') as reminder_date, DATE_FORMAT(r.next_date, '%d/%m/%Y') as next_date,
                r.remark, r.reminder_status, r.reminder_message, r.concern_user, u.name, r.broker_id,
                DATE_FORMAT(r.completed_on, '%d/%m/%Y') as completed_on,
                CASE WHEN r.reminder_date IS NOT NULL AND r.completed_on IS NOT NULL THEN DATEDIFF(r.completed_on,r.reminder_date)
                ELSE 'N/A' END AS turnaround_time
                FROM $sTable r
                LEFT JOIN users u ON u.username = r.concern_user
                $sWhere
                $myWhere
                $sOrder
                $sLimit
            ";
        } else {
            $sQuery = "
                SELECT SQL_CALC_FOUND_ROWS r.reminder_id, r.reminder_type, r.client_id, r.client_name,
                DATE_FORMAT(r.reminder_date, '%d/%m/%Y') as reminder_date, DATE_FORMAT(r.next_date, '%d/%m/%Y') as next_date,
                r.remark, r.reminder_status, r.reminder_message, r.concern_user, u.name, r.broker_id
                FROM $sTable r
                LEFT JOIN users u ON u.username = r.concern_user
                $sWhere
                $myWhere
                $sOrder
                $sLimit
            ";
        }
        $qry = $this->db->query($sQuery);
        $rResult = $qry->result();

        /* Data set length after filtering */
        $sQuery = "
            SELECT FOUND_ROWS()
        ";
        $qry = $this->db->query($sQuery);
        $aResultFilterTotal = (array)$qry->row();
        $iFilteredTotal = $aResultFilterTotal['FOUND_ROWS()'];

        /* Total data set length */
        $sQuery = "
            SELECT COUNT(reminder_id)
            FROM   $sTable r
            WHERE $myWhere
        ";
        $qry = $this->db->query($sQuery);
        $aResultTotal = (array)$qry->row();
        $iTotal = $aResultTotal['COUNT(reminder_id)'];


        //return data we got
        return $data = array(
            "rResult" => $rResult,
            "iFilteredTotal" => $iFilteredTotal,
            "iTotal" => $iTotal
        );
    }
}
