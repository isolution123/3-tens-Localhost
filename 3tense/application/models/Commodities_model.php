<?php
if(!defined('BASEPATH')) exit('No direct path access allowed');
class Commodities_model extends CI_Model{
    var $table = 'commodity_transactions as ct';
    var $column = array('ct.commodity_trans_id, fam.family_id, fam.name as family_name, ct.client_id, c.name as client_name,
    Date_Format(ct.transaction_date, "%d/%m/%Y") as transaction_date, ct.commodity_item_id, ci.item_name, ct.transaction_rate,
    ct.quantity, ct.commodity_unit_id, cu.unit_name, ct.quality, ct.transaction_type, ct.adviser_id, a.adviser_name, ct.total_amount,
    ct.initial_investment, Date_Format(ct.added_on, "%d/%m/%Y") as added_on, Date_Format(ct.updated_on, "%d/%m/%Y") as updated_on,
    cr.current_rate, ct.sale_ref');
    var $order = array('transaction_date' => 'desc', 'commodity_trans_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'ct.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('commodity_items as ci', 'ci.item_id = ct.commodity_item_id', 'inner');
        $this->db->join('commodity_units as cu', 'cu.unit_id = ct.commodity_unit_id', 'inner');
        $this->db->join('commodity_rates as cr', 'cr.item_id = ct.commodity_item_id and cr.unit_id = ct.commodity_unit_id and cr.broker_Id=fam.broker_Id', 'inner');
        $this->db->join('advisers as a', 'a.adviser_id = ct.adviser_id', 'inner');

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

    // Get commodities
    function get_commodities($condition=array('1'=>'1'))
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

    function get_commodity_trans_id($where)
    {
        $query = $this->db->query("select commodityID(?) as commodity_trans_id", $where);
        return $query->row();
    }

    function add_commodity($data)
    {
        if(!$this->db->insert('commodity_transactions', $data))
        {
            $error = $this->db->error();
            return $error;
        }
        else
        {
            return true;
        }
    }

    function update_commodity($data, $condition)
    {
        if(!$this->db->update('commodity_transactions', $data, $condition)) {
            $error = $this->db->error();
            return $error;
        }
        else
        {
            return true;
        }
    }

    function delete_commodity($condition)
    {
        $this->db->where($condition);
        if(!$this->db->delete('commodity_transactions')) {
            return $this->db->error();
        } else {
            return true;
        }
    }

    ////Commodity Items
    function get_commodity_items($condition=array('1'=>'1'))
    {
        $this->db->select('item_id, item_name, broker_id');
        $this->db->from('commodity_items');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    ////Commodity Units
    function get_commodity_units($condition=array('1'=>'1'))
    {
        $this->db->select('cu.unit_id, cu.unit_name');
        $this->db->from('commodity_units cu');
        $this->db->join('commodity_rates cr','cr.unit_id = cu.unit_id','inner');
        $this->db->join('commodity_items ci','ci.item_id = cr.item_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    ////Commodity Rates
    function get_commodity_rates($condition=array('1'=>'1'))
    {
        $this->db->select('current_rate');
        $this->db->from('commodity_rates');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    ////Function for Commodity Related Reports
    function get_commodity_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_commodity_by_client_report(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_commodity_by_family_report(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }



    /* function to check duplicacy of values */
    function check_duplicate($tableName, $condition) {
        $this->db->select('*');
        $this->db->from($tableName);
        $this->db->where($condition);
        //$this->db->where(' OR broker_id is null');
        $query = $this->db->get();
        //$var = $this->db->queries;
        //echo $var[0];
        return $query->result();
    }


    /* Commodity Items functions START */
    public function add_commodity_item($data)
    {
        if(!($this->db->insert('commodity_items', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_commodity_item($data, $where)
    {
        if(!($this->db->update('commodity_items', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['item_id'];
        }
    }

    public function delete_commodity_item($id)
    {
        if(!($this->db->delete('commodity_items', array('item_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Commodity Items functions END */

    /* Commodity Units functions START */
    function get_commodity_units_list($condition=array('1'=>'1'))
    {
        $this->db->select('unit_id, unit_name, broker_id');
        $this->db->from('commodity_units');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_commodity_unit($data)
    {
        if(!($this->db->insert('commodity_units', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_commodity_unit($data, $where)
    {
        if(!($this->db->update('commodity_units', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['unit_id'];
        }
    }

    public function delete_commodity_unit($id)
    {
        if(!($this->db->delete('commodity_units', array('unit_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Commodity Units functions END */

    /* Commodity Rates functions START */
    function get_commodity_rates_list($condition=array('1'=>'1'))
    {
        $this->db->select('cr.commodity_rate_id, cr.item_id, ci.item_name, cr.unit_id, cu.unit_name, cr.current_rate, cr.broker_id');
        $this->db->from('commodity_rates cr');
        $this->db->join('commodity_items ci','ci.item_id = cr.item_id','inner');
        $this->db->join('commodity_units cu','cu.unit_id = cr.unit_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_commodity_rate($data)
    {
        if(!($this->db->insert('commodity_rates', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_commodity_rate($data, $where)
    {
        if(!($this->db->update('commodity_rates', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['unit_id'];
        }
    }

    public function delete_commodity_rate($id)
    {
        if(!($this->db->delete('commodity_rates', array('commodity_rate_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Commodity Rates functions END */


    /* Commodity Script functions - IBJA api */
    public function update_commodity_rates_script($data, $key) {
        if(!$this->db->update_batch('commodity_rates',$data,$key)) {
            return $error = $this->db->error();
        } else {
            return true;
        }
    }
}