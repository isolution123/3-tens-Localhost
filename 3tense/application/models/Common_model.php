<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function last_import($importType, $brokerId, $fileName, $userID)
    {
        $query = "INSERT INTO last_imports (broker_id, import_type, file_name, user_id) ".
            "VALUES (".$this->db->escape($brokerId).", ".$this->db->escape($importType).", ".
            "".$this->db->escape($fileName).", ".$this->db->escape($userID).") ".
            "ON DUPLICATE KEY UPDATE broker_id = ".$this->db->escape($brokerId).", ".
            "import_type = ".$this->db->escape($importType).", ".
            "file_name = ".$this->db->escape($fileName).", user_id = ".$this->db->escape($userID)."";

        $this->db->query($query);
        return $this->db->insert_id();
    }

    // function will be run for imports via Cron Jobs(might also be with admin)
    function last_import_for_null($importType, $fileName)
    {
        $where = array(
            'import_type' => $importType,
            'broker_id' => null,
            'user_id' => null
        );
        $this->db->select('*');
        $this->db->from('last_imports');
        $this->db->where($where);
        $query = $this->db->get();
        $row = $query->row();
        if($row) {
            $data = array(
                'import_type' => $importType,
                'file_name' => $fileName,
                'last_import_date' => date('Y-m-d H:i:s')
            );
            if(!$this->db->update('last_imports', $data, $where)) {
                return $error = $this->db->error();
            } else {
                return true;
            }
        } else {
            $data = array(
                'import_type' => $importType,
                'file_name' => $fileName,
                'broker_id' => null,
                'user_id' => null
            );
            if(!$this->db->insert('last_imports', $data)) {
                return $error = $this->db->error();
            } else {
                return true;
            }
        }
    }

    //Salmaan - 3/3/2016
    function get_last_imports($condition) {
        $this->db->select('li.import_type, date_format(li.last_import_date,"%d/%m/%Y %h:%i:%s %p") as last_import_date, li.file_name, li.user_id, li.broker_id, u.name');
        $this->db->from('last_imports li');
        $this->db->join('users u','u.id = li.user_id','left');
        $this->db->where($condition);
        $this->db->order_by('last_import_date','desc');
        $query = $this->db->get();
        return $query->result();
    }

    function error_logs($data)
    {
        $this->db->insert('error_logs', $data);
    }

    function check_duplicate($column, $table, $condition)
    {
        $this->db->select($column);
        $this->db->from($table);
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    // function to load all scrip name, code and close_rate
    function get_scrips($condition=array('1'=>'1'))
    {
        $this->db->select('scrip_code, scrip_name, close_rate');
        $this->db->from('scrip_rates');
        $this->db->where($condition);
        $query = $this->db->get();
        //return $this->db->queries;
        return $query->result();
    }

    // function to load single name, code and close_rate
    function get_scrip_detail($condition=array('1'=>'1'))
    {
        $this->db->select('scrip_code, scrip_name, close_rate');
        $this->db->from('scrip_rates');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }
    
    function logApiOperation($data)
    {
        $this->db->insert('api_logs', $data);
        //echo '<pre>';print_r($this->db->error());die;
    }
    
    function getBrokerLogo($brokerId)
    {
        if (glob("uploads/brokers/" . $brokerId . "/*.png*")) {
            $logo = basename(glob("uploads/brokers/" . $brokerId . "/*.png*")[0]);
            $logo = "uploads/brokers/" . $brokerId . '/' . $logo;
        } elseif (glob("uploads/brokers/" . $brokerId . "/*.jpg*")) {
            $logo = basename(glob("uploads/brokers/" . $brokerId . "/*.jpg*")[0]);
            $logo = "uploads/brokers/" . $brokerId . '/' . $logo;
        } elseif (glob("uploads/brokers/" . $brokerId . "/*.jpeg*")) {
            $logo = basename(glob("uploads/brokers/" . $brokerId . "/*.jpeg*")[0]);
            $logo = "uploads/brokers/" . $brokerId . '/' . $logo;
        } else {
            $logo = "assets/users/img/logo.png";
        }
        
        return $logo;
    }


    function createNFODetail($data)
    {
        $this->db->insert('nfo_detail', $data);
    }
    function get_nfo_detail($broker_id)
    {
        
     $query = $this->db->query("SELECT  * FROM `nfo_detail` where broker_id=".$broker_id." ORDER BY `id` desc limit 1");
        
        //return $this->db->queries;
        return $query->result();
    }
     function deleteNFODetail($broker_id)
    {
        $this->db->delete('nfo_detail',array('broker_id'=>$broker_id));
    }
    

} 