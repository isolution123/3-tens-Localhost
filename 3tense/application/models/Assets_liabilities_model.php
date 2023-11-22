<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Assets_liabilities_model extends CI_Model{
    var $asset_table = 'asset_transactions as at';
    /*var $asset_column = array("asset_id, at.client_id, c.name as client_name, c.family_id, fam.name as family_name, at.product_id, product_name,
    at.company_id, company_name, at.scheme_id, scheme_name, at.type_id, type_name, goal, ref_number, Date_Format(start_date, '%d/%m/%Y') as start_date,
    Date_Format(end_date, '%d/%m/%Y') as end_date, installment_amount, rate_of_return, expected_mat_value, narration");*/
    //modifed by Akshay Karde - 2017-05-26
    var $asset_column = array("asset_id, at.client_id,Date_Format(cease_date,'%d/%m/%Y') as cease_date,at.frequency as frequency,folio_no as folio_no,pro.product_name as product_name,c.name as client_name, c.family_id, fam.name as family_name, at.product_id, product_name,
    at.company_id, company_name, at.scheme_id, scheme_name, at.type_id, type_name, goal, ref_number, Date_Format(start_date, '%d/%m/%Y') as start_date,
    Date_Format(end_date, '%d/%m/%Y') as end_date, installment_amount, rate_of_return, expected_mat_value, narration");
    var $asset_order = array('asset_id' => 'desc');

    var $liability_table = 'liability_transactions as lt';
    var $liability_column = array("liability_id, lt.client_id, c.name as client_name, c.family_id, fam.name as family_name, lt.product_id,
    product_name, lt.company_id, company_name, lt.scheme_id, scheme_name, lt.type_id, type_name, particular, ref_number,
    Date_Format(start_date, '%d/%m/%Y') as start_date, Date_Format(end_date, '%d/%m/%Y') as end_date,
    case when pre_payment = 1 then 'Yes' else 'No' end as pre_payment, installment_amount, interest_rate, total_liability, narration");
    var $liability_order = array('liability_id' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query($type)
    {
        if($type == 'asset')
        {
            $this->db->select($this->asset_column);
            $this->db->from($this->asset_table);
            $order = $this->asset_order;
            $this->db->join('clients as c', 'at.client_id = c.client_id', 'inner');
            $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
            $this->db->join('clients as nom', 'nom.client_id = c.client_id', 'inner');
            $this->db->join('al_products as pro', 'at.product_id = pro.product_id', 'left');
            $this->db->join('al_companies as comp', 'at.company_id = comp.company_id', 'left');
            //$this->db->join('al_schemes as sch', 'at.scheme_id = sch.scheme_id', 'inner');
            //modifed by Akshay Karde - 2017-05-26
            $this->db->join('mutual_fund_schemes as sch', 'at.scheme_id = sch.scheme_id', 'left');
            $this->db->join('al_types as t', 'at.type_id = t.type_id', 'inner');
            $i=0;
            foreach($this->asset_column as $col_item)
            {
                if(isset($_POST['search']['value']))
                    ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) :
                        $this->db->or_like($col_item, $_POST['search']['value']);
                $column[$i] = $col_item;
                $i++;
            }
        }
        else
        {
            $this->db->select($this->liability_column);
            $this->db->from($this->liability_table);
            $order = $this->liability_order;
            $this->db->join('clients as c', 'lt.client_id = c.client_id', 'inner');
            $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
            $this->db->join('clients as nom', 'nom.client_id = c.client_id', 'inner');
            $this->db->join('al_products as pro', 'lt.product_id = pro.product_id', 'left');
            $this->db->join('al_companies as comp', 'lt.company_id = comp.company_id', 'left');
            $this->db->join('al_schemes as sch', 'lt.scheme_id = sch.scheme_id', 'left');
            $this->db->join('al_types as t', 'lt.type_id = t.type_id', 'left');
            $i=0;
            foreach($this->liability_column as $col_item)
            {
                if(isset($_POST['search']['value']))
                    ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) :
                        $this->db->or_like($col_item, $_POST['search']['value']);
                $column[$i] = $col_item;
                $i++;
            }
        }

        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($order))
        {
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    ////Asset
    function get_asset_list($condition)
    {
        $this->get_datatables_query('asset');
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    //asset and libility data
    function get_asset_list_top($condition)
    {
        $qry = 'select * from 
        (select c.name,a.start_date,ap.product_name,a.ref_number,a.installment_amount,a.broker_id as broker_id 
            from asset_transactions a inner join al_products ap on a.product_id=ap.product_id 
            inner join clients c on c.client_id=a.client_id 
            union 
            select c.name,l.start_date,ap.product_name,l.ref_number,l.installment_amount,l.broker_id as broker_id 
            from liability_transactions l inner join al_products ap on l.product_id=ap.product_id 
            inner join clients c on c.client_id=l.client_id)  as abc
             where '.$condition.
            ' order by start_date desc limit 5';

        $query = $this->db->query($qry);
        //return $this->db->queries[0];
        return $query->result();
    }

        //asset and libility maturity data
    function get_asset_list_mat($condition)
    {
        /*$qry = 'select * from (select c.name,am.maturity_date,ap.product_name,a.ref_number,am.maturity_amount,a.broker_id as broker_id
            from asset_transactions a inner join al_products ap on a.product_id=ap.product_id 
            inner join clients c on c.client_id=a.client_id 
            inner join asset_maturity am on am.asset_id=a.asset_id 
            union 
            select c.name,lm.maturity_date,ap.product_name,l.ref_number,lm.maturity_amount,l.broker_id as broker_id 
            from liability_transactions l 
            inner join al_products ap on l.product_id=ap.product_id 
            inner join clients c on c.client_id=l.client_id 
            inner join liability_maturity lm on lm.liability_id=l.liability_id ) as abc
            where '.$condition.
            ' order by maturity_date asc limit 5';*/

        $qry = 'select * from (
                select c.name,a.end_date as maturity_date,ap.product_name,a.ref_number,a.expected_mat_value as maturity_amount,a.broker_id as broker_id
                from asset_transactions a inner join al_products ap on a.product_id=ap.product_id
                inner join clients c on c.client_id=a.client_id
                union
                select c.name,l.end_date as maturity_date,ap.product_name,l.ref_number,l.total_liability as maturity_amount,l.broker_id as broker_id
                from liability_transactions l
                inner join al_products ap on l.product_id=ap.product_id
                inner join clients c on c.client_id=l.client_id
            ) as abc
            where '.$condition.
            ' order by maturity_date asc limit 5';

        $query = $this->db->query($qry);
        //return $this->db->queries[0];
        return $query->result();
    }

 //asset and libility data
    function get_asset_list_top_client($condition)
    {


        $qry = 'select * from
        (select c.name,am.maturity_date,ap.product_name,at.ref_number ,am.maturity_amount
            from asset_transactions at inner join al_products ap on at.product_id=ap.product_id
            inner join clients c on c.client_id=at.client_id inner join asset_maturity am on am.asset_id=at.asset_id
            where '.$condition.'
            union
            select c.name,lm.maturity_date,lp.product_name,lt.ref_number ,lm.maturity_amount
            from liability_transactions lt inner join al_products lp on lt.product_id=lp.product_id
            inner join clients c on c.client_id=lt.client_id inner join liability_maturity lm on lm.liability_id=lt.liability_id
            where '.$condition.'
           )  as abc
              order by maturity_date  asc limit 5';



        $query = $this->db->query($qry);
        //return $this->db->queries;
        return $query->result();
    }

 //upcoming 5 asset and libility interest
function get_asset_list_mat_client($condition)
{

    $qry = 'select * from ( select c.name,at.end_date as end_date,ap.product_name,at.ref_number,at.installment_amount
            from asset_transactions at inner join al_products ap on at.product_id=ap.product_id
            inner join clients c on c.client_id=at.client_id
            where '.$condition.'
				         union
               select c.name,lt.end_date as end_date,ap.product_name,lt.ref_number,lt.installment_amount
            from liability_transactions lt
            inner join al_products ap on lt.product_id=ap.product_id
            inner join clients c on c.client_id=lt.client_id
            where '.$condition.' )as abc order by end_date asc limit 5';

    $query = $this->db->query($qry);
    //return $this->db->queries[0];
    return $query->result();
}


    //for extended search
    function get_asset_list_extended($condition)
    {
        $this->get_datatables_query('asset');
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
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

    function get_asset_details($condition)
    {
        /*$this->db->select('asset_id, fam.name as family_name, fam.family_id, c.name as client_name, at.client_id, at.client_id, at.product_id,
        product_name, at.type_id, type_name, at.company_id, company_name, at.scheme_id, scheme_name, goal, ref_number, Date_Format(start_date, "%d/%m/%Y") as start_date, Date_Format(end_date, "%d/%m/%Y") as end_date,
        installment_amount, rate_of_return, expected_mat_value, narration');*/
        $this->db->select($this->asset_column);
        $this->db->from($this->asset_table);
        //$this->db->from('asset_transactions as at');
        $this->db->join('clients as c', 'at.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('al_products as pro', 'at.product_id = pro.product_id', 'left');
        $this->db->join('al_companies as comp', 'at.company_id = comp.company_id', 'left');
        //$this->db->join('al_schemes as sch', 'at.scheme_id = sch.scheme_id', 'inner');
        //modifed by Akshay Karde - 2017-05-26
        $this->db->join('mutual_fund_schemes as sch', 'at.scheme_id = sch.scheme_id', 'left');
        $this->db->join('al_types as t', 'at.type_id = t.type_id', 'inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    function add_asset($data)
    {
        if(!($this->db->insert('asset_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_assets($data, $condition)
    {
        if(!($this->db->update('asset_transactions', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    ////Asset Maturity
    function add_asset_maturity($data)
    {
        if(!($this->db->insert('asset_maturity', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function delete_asset_maturity($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete('asset_maturity', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function delete_asset_details($matWhere, $transWhere)
    {
        $error = '';
        if(!($this->db->delete('asset_maturity', $matWhere))) {
            $error = $this->db->error();
        }
        else if(!($this->db->delete('asset_transactions', $transWhere)))
        {
            $error = $this->db->error();
        }
        return $error;
    }

    ////Liability
    function get_liability_list($condition)
    {
        $this->get_datatables_query('liability');
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function get_liability_details($condition)
    {
        $this->db->select('liability_id, fam.name as family_name, fam.family_id, c.name as client_name, lt.client_id, lt.product_id, product_name,
        lt.type_id, type_name, lt.company_id, company_name, lt.scheme_id, scheme_name, particular, ref_number,
        Date_Format(start_date, "%d/%m/%Y") as start_date, Date_Format(end_date, "%d/%m/%Y") as end_date, pre_payment, installment_amount,
        interest_rate, total_liability, narration');
        $this->db->from('liability_transactions as lt');
        $this->db->join('clients as c', 'lt.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('al_products as pro', 'lt.product_id = pro.product_id', 'left');
        $this->db->join('al_companies as comp', 'lt.company_id = comp.company_id', 'left');
        $this->db->join('al_schemes as sch', 'lt.scheme_id = sch.scheme_id', 'left');
        $this->db->join('al_types as t', 'lt.type_id = t.type_id', 'left');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    function add_liability($data)
    {
        if(!($this->db->insert('liability_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function add_liability_maturity($data)
    {
        if(!($this->db->insert('liability_maturity', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_liability_maturity($data, $condition)
    {
        if(!($this->db->update('liability_maturity', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function update_liability($data, $condition)
    {
        if(!($this->db->update('liability_transactions', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function delete_liability_details($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete('liability_histories', $condition))) {
            $error = $this->db->error();
        }
        else if(!($this->db->delete('liability_maturity', $condition))){
            $error = $this->db->error();
        }
        else if(!($this->db->delete('liability_transactions', $condition))){
            $error = $this->db->error();
        }
        return $error;
    }

    //Payment Details
    function add_liability_payment($data)
    {
        if(!$this->db->insert('liability_histories', $data)) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function delete_payment($condition)
    {
        $error = '';
        $this->db->where($condition);
        if(!($this->db->delete('liability_histories', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_payment_list($condition)
    {
        $this->db->select('liability_history_id, amount, Date_format(payment_date, "%d/%m/%Y") as payment_date, narration');
        $this->db->from('liability_histories');
        $this->db->where($condition);
        $result = $this->db->get();
        return $result->result();
    }

    function liability_pre_payment($data)
    {
        $query = $this->db->query("call sp_liability_prepayment(?, ?, ?, ?, ?)", $data);
        return true;
    }

    ////Function for Asset Liability Related Reports
    //Asset Report
    function get_asset_report($where)
    {
        $query = $this->db->query("call sp_asset_report(?, ?)", $where);
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }

    //Asset Report
    function get_liability_report($where)
    {
        $query = $this->db->query("call sp_liability_report(?, ?)", $where);
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }
}
