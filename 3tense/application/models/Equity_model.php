<?php
if(!defined('BASEPATH')) exit('No direct path access allowed');
class Equity_model extends CI_Model{
    var $table = 'equities as eq';
    var $column = array(" equity_transaction_id, eq.client_id, c.name as client_name, c.family_id, fam.name as family_name,
     tb.trading_broker_id, tb.trading_broker_name, eq.client_code,
     Date_Format(transaction_date, '%d/%m/%Y') as transaction_date, sr.scrip_name, sr.scrip_code, eq.quantity, acquiring_rate, tracking, initial_investment, (case when ifnull(eqapc.apc,0)=0 then 0 else (ifnull(eqapc.apc,0)* eq.quantity) end) as purchase_value,(sr.close_rate*eq.quantity) as current_value,sr.close_rate,ifnull(eqapc.apc,0) as apc");
    var $order = array('transaction_date' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'eq.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('trading_brokers as tb', 'eq.trading_broker_id = tb.trading_broker_id', 'inner');
        $this->db->join('scrip_rates as sr', 'eq.scrip_code = sr.scrip_code', 'inner');
        $this->db->join('equities_apc as eqapc', 'eqapc.client_id = eq.client_id and eqapc.scrip_code = eq.scrip_code and eqapc.quantity = eq.quantity', 'left');

        $i=0;
        foreach($this->column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) :
                    $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    ////Equity
    function get_equity($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    ////Equity negative balance
    function get_equity_negative($condition)
    {
        $qry = 'select c.name, tb.trading_broker_name, cb.client_code, cb.balance from
client_brokers cb inner join clients c
on c.client_id=cb.client_id inner join trading_brokers tb
on tb.trading_broker_id=cb.broker inner join families f
on c.family_id=f.family_id
            where '.$condition.
            ' order by cb.balance asc limit 5';

        $query = $this->db->query($qry);
        //return $this->db->queries[0];

        return $query->result();
    }

    //for adding form data
    function add_equity_form($data)
    {
        if(!($this->db->insert('equities', $data))) {
        //if(!($this->db->insert_batch('equities', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    //function to add equity
    function add_equity($data)
    {
        //if(!($this->db->insert('equities', $data))) {
        if(!($this->db->insert_batch('equities', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }
    
      //function to add equity apc
    function add_equity_apc($data)
    {
        if(!($this->db->insert_batch('equities_apc', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }

    //function to update equity data
    function update_equity($data, $where)
    {
        if(!($this->db->update('equities', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['equity_transaction_id'];
        }
    }
    
      //function to update equity data
    function update_equity_apc($data, $where)
    {
        if(!($this->db->update('equities_apc', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['client_id'];
        }
    }

    function delete_equity($condition)
    {
        $this->db->where($condition);
        $this->db->delete('equities');
        return true;
    }
     public function check_equity_apc($condition)
    {
        $this->db->select('*');
        $this->db->from('equities_apc apc');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }


    //Function while importing Bhav Copy
    function delete_scrip_data($where = '1=1')
    {
        $this->db->where($where);
        if(!($this->db->delete('scrip_rates'))) {
            return $error = $this->db->error();
        } else {
            return true;
        }
    }

    function add_scrip_data($data)
    {
        /*if($this->db->insert_batch('scrip_rates', $data)) {
            $insertId = $this->db->insert_id();
            return $insertId;
        } else {
            return false;
        }*/
        $error = false;
        foreach($data as $row) {
            $sql = $this->db->insert_string('scrip_rates', $row) . ' ON DUPLICATE KEY UPDATE close_rate='.$row['close_rate'];
            if(!$this->db->query($sql)) {
                $error = true;
            }
        }
        if($error) {
            return $this->db->error();
            return array('code'=>'000','message'=>'All/Some records were not imported.');
        } else {
            return true;
        }
        /*if(!($this->db->insert_batch('scrip_rates', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            //return $this->db->queries;
            return true;
        }*/
    }


    //Function for Equity Report
    function get_equity_values($where)
    {
        $query = $this->db->query("Select c.name, sr.scrip_name, SUM(e.quantity) as quantity, AVG(sr.close_rate) AS close_rate,
        SUM(e.quantity*sr.close_rate) AS value, e.client_code,sr.industry,sr.cap,t.trading_broker_name,
        sum(ifnull(eqapc.apc,0)) as apc,
        sum(ifnull(eqapc.apc,0)* e.quantity) as purchase_value
        from equities e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join trading_brokers t  on e.trading_broker_id=t.trading_broker_id
        inner join clients c on c.client_id = e.client_id 
        left join equities_apc as eqapc on eqapc.client_id = e.client_id and eqapc.scrip_code = e.scrip_code and eqapc.quantity = e.quantity 
        ".$where. "
        group by c.name, e.client_code, sr.scrip_name,sr.industry, sr.cap
        having SUM(e.quantity)>0
        order by c.report_order, c.name, e.client_code, sr.scrip_name");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
     function get_equity_values_history($where)
    {
        $query = $this->db->query("Select c.name, sr.scrip_name, SUM(e.quantity) as quantity, AVG(e.close_rate) AS close_rate,
        SUM(e.quantity*e.close_rate) AS value, e.client_code,sr.industry,sr.cap,t.trading_broker_name, sum(ifnull(e.apc,0)) as apc,
        sum(ifnull(e.apc,0)* e.quantity) as purchase_value
        from equities_history e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join `client_brokers`  cb on cb.client_id=e.client_id and e.client_code=cb.client_code
		inner join trading_brokers t  on cb.broker=t.trading_broker_id
        inner join clients c on c.client_id = e.client_id ".$where. "
        group by c.name, e.client_code, sr.scrip_name,sr.industry, sr.cap
        having SUM(e.quantity)>0
        order by c.report_order, c.name, e.client_code, sr.scrip_name");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
    
     function get_equity_values_cap_wise($where)
    {
        $query = $this->db->query("Select SUM(e.quantity*sr.close_rate) AS value,sr.cap
        from equities e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join clients c on c.client_id = e.client_id ".$where. "
        group by sr.cap
        order by c.report_order, c.name, e.client_code, sr.scrip_name");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
      function get_equity_values_cap_wise_history($where)
    {
        $query = $this->db->query("Select SUM(e.quantity*e.close_rate) AS value,sr.cap
        from equities_history e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join clients c on c.client_id = e.client_id ".$where. "
        group by sr.cap
        order by c.report_order, c.name, e.client_code, sr.scrip_name");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
     function get_equity_values_industry_wise($where)
    {
        $query = $this->db->query("Select SUM(e.quantity*sr.close_rate) AS value,sr.industry
        from equities e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join clients c on c.client_id = e.client_id ".$where. "
        group by sr.industry
        order by SUM(e.quantity*sr.close_rate) Desc");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
     function equities_monthly_summary_for_chart($where)
    {
        $query = $this->db->query("Select DATE_FORMAT(e.CreatedDTStamp, '%d-%m-%Y')as cur_date,sum(e.value)as value
        from equities_monthly_summary e
        inner join clients c on c.client_id = e.client_id ".$where. " 
        group by DATE_FORMAT(e.CreatedDTStamp, '%d-%m-%Y')
        order by e.CreatedDTStamp");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
     function get_equity_values_industry_wise_history($where)
    {
        $query = $this->db->query("Select SUM(e.quantity*e.close_rate) AS value,sr.industry
        from equities_history e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join clients c on c.client_id = e.client_id ".$where. "
        group by sr.industry
        order by SUM(e.quantity*e.close_rate) Desc");

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }

    function get_equity_report($where)
    {
        $query = "";
        $query = $this->db->query("call sp_funds_info(?, ?, ?, ?)", $where);

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return false;
        }
    }
    function get_equity_report_history($where)
    {
        $query = "";
        $query = $this->db->query("call sp_funds_info_history(?, ?, ?, ?,?)", $where);

        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return false;
        }
    }

    function get_xirr_data($where)
    {
        $query = "";
        $query = $this->db->query("call sp_funds_xirr(?, ?, ?)", $where);
        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return false;
        }
    }

    function get_equity_broker_balance($where)
    {
        $query = $this->db->query("Select SUM(balance) AS `balance` from client_brokers c ".$where);
        //return $this->db->queries;
        if($query) {
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
     function get_equity_broker_balance_history($where)
    {
        $query = $this->db->query("Select SUM(balance) AS `balance` from client_brokers_history c ".$where);
        //return $this->db->queries;
        if($query) {
            $res = $query->result();
            return $res;
        } else {
            return false;
        }
    }
       function add_equity_history()
    {
    
          $query = $this->db->query("INSERT INTO `equities_history` (`client_id`, `client_code`, `transaction_date`, `scrip_code`,`quantity`, `close_rate`,apc) 
                                select e.client_id,e.client_code,e.transaction_date,e.scrip_code,e.quantity,sr.close_rate,ifnull(eqapc.apc,0)
                                from equities e
                                inner join scrip_rates sr on sr.scrip_code = e.scrip_code           
                                left join equities_history eh on eh.client_id=e.client_id 
                                left join equities_apc as eqapc on eqapc.client_id = e.client_id and eqapc.scrip_code = e.scrip_code and eqapc.quantity = e.quantity
                                and e.client_code=eh.client_code
                                and eh.scrip_code=e.scrip_code
                                and date(eh.DTStamp)= date(now()) 
                                where e.broker_id in ('0004','0009','0174') AND
                                eh.equities_history_id is null");
    }
    function add_client_brokers_history()
    {
    
          $query = $this->db->query("INSERT INTO `client_brokers_history` ( `client_id`, `broker`, `client_code`, `balance`, `user_id`, `held_type`) 
                                select e.client_id, e.broker, e.client_code, e.balance, e.user_id, e.held_type
                                from client_brokers e
                                left join client_brokers_history eh on eh.client_id=e.client_id 
                                and e.client_code=eh.client_code
                                and date(eh.DTStamp)= date(now()) 
                                where e.user_id in  ('0004','0009','0174')  AND
                                eh.Id is null");
        
    }
}
