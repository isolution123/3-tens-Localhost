<?php
if(!defined('BASEPATH')) exit('No direct path access allowed');
class Real_estate_model extends CI_Model{
    var $table = 'property_transactions as proTrans';
    var $column = array('pro_transaction_id, fam.name as family_name, c.name as client_name,
    Date_Format(transaction_date, "%d/%m/%Y") as transaction_date, property_name, pt.property_type_name,
    property_location, property_area, transaction_rate, amount, transaction_type,
    ((current_rate - transaction_rate) * property_area + amount) as market_value, case when rent_applicable = 1 then "Yes" else "No" end as rent_applicable');
    var $order = array('transaction_date' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

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


    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'proTrans.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('property_types as pt', 'proTrans.property_type_id = pt.property_type_id', 'inner');

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

    ////Real Estate
    function get_real_estate($condition)
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

    function get_real_estate_details($condition)
    {
        $this->db->select('pro_transaction_id, fam.name as family_name, fam.family_id, c.name as client_name, proTrans.client_id, property_name,
        property_unit_id, unit_name, Date_Format(transaction_date, "%d/%m/%Y") as transaction_date, proTrans.property_type_id, pt.property_type_name,
        property_location, property_area, transaction_rate, amount, current_rate, Date_Format(property_updated_on, "%d/%m/%Y") as property_updated_on,
        remarks, adv.adviser_id, adviser_name, deposit_amount, gain, total_gain, abs, cagr, transaction_type');
        $this->db->from('property_transactions as proTrans');
        $this->db->join('clients as c', 'proTrans.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('property_types as pt', 'proTrans.property_type_id = pt.property_type_id', 'inner');
        $this->db->join('property_units as u', 'proTrans.property_unit_id = u.unit_id', 'inner');
        $this->db->join('advisers as adv', 'adv.adviser_id = proTrans.adviser_id', 'inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    function get_properties_dropdown($condition)
    {
        $this->db->select('pro_transaction_id, property_name');
        $this->db->from('property_transactions');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_property_id($where)
    {
        $query = $this->db->query("select propertyID(?) as property_id", $where);
        return $query->row();
    }

    function add_real_estate($data)
    {
        if(!($this->db->insert('property_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_real_estate($data, $condition)
    {
        if(!($this->db->update('property_transactions', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function delete_real_estate($condition)
    {
        $error = '';
        if(!($this->db->delete('property_transactions', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_rent_details($condition)
    {
        $this->db->select('pro_rent_id, from_date, to_date, amount');
        $this->db->from('property_rents');
        $this->db->where($condition);
        $result = $this->db->get();
        return $result->result();
    }

    function delete_rent($condition)
    {
        $error = '';
        if(!($this->db->delete('property_rents', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function delete_rent_details($condition)
    {
        $error = '';
        if(!($this->db->delete('property_rent_details', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function add_rent_details($data, $isBatch = false)
    {

        if($isBatch)
        {
            if(!($this->db->insert_batch('property_rents', $data))) {
                $error = $this->db->error();
                return $error;
            } else {
                return true;
            }
        }
        else
        {
            if(!($this->db->insert('property_rents', $data))) {
                $error = $this->db->error();
                return $error;
            } else {
                return $this->db->insert_id();
            }
        }
    }

    function update_rent_details($data, $condition)
    {
        if(!($this->db->update('property_rents', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function getRentAmount($condition)
    {
        $this->db->select('sum(amount) as amount');
        $this->db->from('property_rent_details');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    ////Property Types
    function get_property_types()
    {
        $this->db->select('property_type_id, property_type_name');
        $this->db->from('property_types');
        $query = $this->db->get();
        return $query->result();
    }

    ////Property Units
    function get_property_units()
    {
        $this->db->select('unit_id, unit_name');
        $this->db->from('property_units');
        $query = $this->db->get();
        return $query->result();
    }

    ////Function for Real Estate Related Reports
    function get_real_estate_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_re_by_client_report(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_re_by_family_report(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }
}