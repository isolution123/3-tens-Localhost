<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Model for Insurances, Insurance Policies, Maturity and Premium
class Insurance_model extends CI_Model{
    var $table = 'insurances as ins';
    var $column = array(
        'ins.policy_num,fam.name, cli.client_id, (cli.name) as client_name, ins.plan_id, plan_name, ins.ins_comp_id, ins_comp_name,
        ins.plan_type_id, plan_type_name, paidup_date, maturity_date, amt_insured, commence_date, ins.mode, mode_name, prem_amt,
        prem_type as prem_type_id, prem_type_name, ins.prem_pay_mode_id, prem_pay_mode, next_prem_due_date, grace_due_date,
        ins.status as status_id, pstat.status, remarks, fund_value, prem_paid_till_date, mat_type, adv_id, adviser_name,
        nominee as nominee_id, nom.name as nominee, adjustment_flag, adjustment, fam.family_id,
      (select fund_option from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_option1,
        (select ROUND(value) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_value1,
        (select fund_option from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 1,1) as fund_option2,
        (select ROUND(value) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 1,1) as fund_value2
        ');
        
        // (select fund_option from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_option1,
        //(select ROUND(value) from fund_options where policy_number = ins.policy_num order by value desc,fund_option asc limit 0,1) as fund_value1,

    var $order = array('ins.commence_date' => 'desc');

    var $client_columns = array(
        'insurance_policies.ins_policy_id', 'ins_companies.ins_comp_name', 'insurance_policies.ins_comp_id', 'ins_plans.plan_name',
        'insurance_policies.plan_id', 'ins_plan_types.plan_type_name','ins_plan_types.plan_type_id', 'insurance_policies.policy_num'
    );
    var $client_order = array('insurance_policies.ins_policy_id' => 'desc');

    function __construct()
    {
        parent:: __construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as cli', 'ins.client_id = cli.client_id', 'inner');
        
        $this->db->join('ins_plans as ipl', 'ins.plan_id = ipl.plan_id', 'inner');
        $this->db->join('ins_companies as icom', 'ins.ins_comp_id = icom.ins_comp_id', 'inner');
        $this->db->join('ins_plan_types as ipt', 'ins.plan_type_id = ipt.plan_type_id', 'inner');
        $this->db->join('premium_modes as pmode', 'ins.mode = pmode.mode_id', 'inner');
        $this->db->join('premium_status as pstat', 'ins.status = pstat.status_id', 'inner');
        $this->db->join('advisers as adv', 'ins.adv_id = adv.adviser_id', 'inner');
        $this->db->join('premium_types as pt', 'ins.prem_type = pt.prem_type_id', 'inner');
        $this->db->join('premium_pay_modes as ppm', 'ins.prem_pay_mode_id = ppm.prem_pay_mode_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = cli.family_id', 'inner');
        $this->db->join('clients as nom', 'ins.nominee = nom.client_id', 'left');
        
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

    function count_filtered()
    {
        $this->get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_new_top_new($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        $this->db->order_by("commence_date desc");
        $this->db->limit(5);
        $query = $this->db->get();
        //var_dump($query);
        if($query)
        return $query->result();
        else
            return false;
    }

    function get_new_top_mat($condition)
    {
        $this->db->select('clients.name,date_format(premium_maturities.maturity_date, "%d/%m/%Y") as maturity_date,ins_plans.plan_name,premium_maturities.amount,insurances.policy_num');
        $this->db->from('insurances');
        $this->db->join('clients', 'clients.client_id = insurances.client_id', 'inner');
        $this->db->join('ins_plans', 'insurances.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('premium_maturities', 'premium_maturities.policy_num = insurances.policy_num', 'inner');
        $this->db->where($condition);
        $this->db->order_by("premium_maturities.maturity_date asc");
        $this->db->limit(5);
        $query = $this->db->get();
        //return $this->db->queries;
        if($query)
        return $query->result();
        else
        return false;
    }

//get upcoming insurances premium due
        function get_new_top_new_client($condition)
        {
            
            
            $this->db->select('cli.name,insurances.next_prem_due_date,insurances.policy_num,ins_plans.plan_name,insurances.prem_amt');
            $this->db->from('insurances');
            $this->db->join('clients as cli', 'insurances.client_id = cli.client_id', 'inner');
            $this->db->join('ins_plans', 'insurances.plan_id = ins_plans.plan_id', 'inner');
            $this->db->where($condition);
            $this->db->order_by("insurances.next_prem_due_date,insurances.prem_amt");
            $this->db->limit(5);
            $query = $this->db->get();
           
            if($query)
            return $query->result();
            else
                return false;
        }

//get upcoming insurances premium Maturity
    function get_new_top_mat_client($condition)
    {
        $this->db->select('clients.name, clients.family_id, premium_maturities.maturity_date as maturity_date, ins_plans.plan_name, premium_maturities.amount, insurances.policy_num');
        $this->db->from('insurances');
        $this->db->join('clients', 'clients.client_id = insurances.client_id', 'inner');
        $this->db->join('ins_plans', 'insurances.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('premium_maturities', 'premium_maturities.policy_num = insurances.policy_num', 'inner');
        $this->db->where($condition);
        $this->db->order_by("premium_maturities.maturity_date asc");
        $this->db->limit(5);
        $query = $this->db->get();
        //return $this->db->queries;
        if($query)
        return $query->result();
        else
        return false;
    }




    public function count_all($condition)
    {
        $this->db->from($this->table);
        $this->db->join('clients as cli', 'ins.client_id = cli.client_id', 'inner');
        $this->db->join('clients as nom', 'ins.nominee = nom.client_id', 'inner');
        $this->db->join('ins_plans as ipl', 'ins.plan_id = ipl.plan_id', 'inner');
        $this->db->join('ins_companies as icom', 'ins.ins_comp_id = icom.ins_comp_id', 'inner');
        $this->db->join('ins_plan_types as ipt', 'ins.plan_type_id = ipt.plan_type_id', 'inner');
        $this->db->join('premium_modes as pmode', 'ins.mode = pmode.mode_id', 'inner');
        $this->db->join('premium_status as pstat', 'ins.status = pstat.status_id', 'inner');
        $this->db->join('advisers as adv', 'ins.adv_id = adv.adviser_id', 'inner');
        $this->db->join('premium_types as pt', 'ins.prem_type = pt.prem_type_id', 'inner');
        $this->db->join('premium_pay_modes as ppm', 'ins.prem_pay_mode_id = ppm.prem_pay_mode_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = cli.family_id', 'inner');
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    ////Insurance Start
    function get_insurances($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        //return $this->db->queries;
        return $query->result();
    }

  function get_insurances_extended($mycondition, $condition, $order="", $limit="")
    {
        if($order == '') { $order = $this->order; }
        //$this->get_datatables_query();
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as cli', 'ins.client_id = cli.client_id', 'inner');
        $this->db->join('clients as nom', 'ins.nominee = nom.client_id', 'left');
        $this->db->join('ins_plans as ipl', 'ins.plan_id = ipl.plan_id', 'inner');
        $this->db->join('ins_companies as icom', 'ins.ins_comp_id = icom.ins_comp_id', 'inner');
        $this->db->join('ins_plan_types as ipt', 'ins.plan_type_id = ipt.plan_type_id', 'inner');
        $this->db->join('premium_modes as pmode', 'ins.mode = pmode.mode_id', 'inner');
        $this->db->join('premium_status as pstat', 'ins.status = pstat.status_id', 'inner');
        $this->db->join('advisers as adv', 'ins.adv_id = adv.adviser_id', 'inner');
        $this->db->join('premium_types as pt', 'ins.prem_type = pt.prem_type_id', 'inner');
        $this->db->join('premium_pay_modes as ppm', 'ins.prem_pay_mode_id = ppm.prem_pay_mode_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = cli.family_id', 'inner');
        
         $qry="SELECT ins.policy_num,fam.name, cli.client_id, (cli.name) as client_name, ins.plan_id, plan_name, ins.ins_comp_id, ins_comp_name,
        ins.plan_type_id, plan_type_name, paidup_date, maturity_date, amt_insured, commence_date, ins.mode, mode_name, prem_amt,
        prem_type as prem_type_id, prem_type_name, ins.prem_pay_mode_id, prem_pay_mode, next_prem_due_date, grace_due_date,
        ins.status as status_id, pstat.status, remarks, fund_value, prem_paid_till_date, mat_type, adv_id, adviser_name,
        nominee as nominee_id, nom.name as nominee, adjustment_flag, adjustment, fam.family_id,
      	ifnull(fv1.fund_option1,'') as fund_option1,
        ifnull(fv1.fund_value1,'') as fund_value1,
        ifnull(fv2.fund_option2,'') as fund_option2,
        ifnull(fv2.fund_value2,'') as fund_value2
FROM  insurances as ins
inner join clients as cli on ins.client_id = cli.client_id

inner join ins_plans as ipl on ins.plan_id = ipl.plan_id
inner join ins_companies as icom on ins.ins_comp_id = icom.ins_comp_id
inner join ins_plan_types as ipt on ins.plan_type_id = ipt.plan_type_id
inner join premium_modes as pmode on ins.mode = pmode.mode_id
inner join premium_status as pstat on ins.status = pstat.status_id
inner join advisers as adv on ins.adv_id = adv.adviser_id
inner join premium_types as pt on ins.prem_type = pt.prem_type_id
inner join premium_pay_modes as ppm on ins.prem_pay_mode_id = ppm.prem_pay_mode_id
inner join families as fam on fam.family_id = cli.family_id
left join clients as nom on ins.nominee = nom.client_id
left join (select * from (SELECT a.policy_number,a.fund_option as fund_option1, a.value as fund_value1, count(*) as row_number FROM fund_options a JOIN fund_options b ON a.policy_number = b.policy_number AND a.value <= b.value GROUP BY a.policy_number, a.value order by a.policy_number desc, a.value desc) t1 where t1.row_number=1) fv1 on fv1.policy_number=ins.policy_num

left join (select * from (SELECT a.policy_number,a.fund_option as fund_option2, a.value as fund_value2, count(*) as row_number FROM fund_options a JOIN fund_options b ON a.policy_number = b.policy_number AND a.value <= b.value GROUP BY a.policy_number, a.value order by a.policy_number desc, a.value desc) t1 where t1.row_number=2) fv2 on fv2.policy_number=ins.policy_num
                            WHERE (".$mycondition.")";
                            
        // $this->db->where('('.$mycondition.')');
        if($condition != "") {
            $qry=$qry.' and ('.$condition.')';
            //$this->db->where('('.$condition.')');
        }
        if($order != "") {
              $qry=$qry.' order by '.$order;
            //$this->db->order_by($order);
        }
     
     
    //    $query = $this->db->get();
      //  $query0 = $this->db->queries[0]; //get query in string format
        
        //add SQL_CAL_FOUND_ROWS in the query after SELECT
        //$query = substr_replace($query0, ' SQL_CALC_FOUND_ROWS ', intval(strpos($query0, 'SELECT'))+6, 0);
      
        $query = substr_replace($qry, ' SQL_CALC_FOUND_ROWS ', intval(strpos($qry, 'SELECT'))+6, 0);
       
        //add limit
        $query .= " LIMIT ".$limit;
      //  print_r($query);die();
        
        $query = $this->db->query($query);
        $rResult = $query->result();
        //return $this->db->queries;
        //return $query->result();
        /* Data set length after filtering */
        $sQuery = "
            SELECT FOUND_ROWS()
        ";
        $qry = $this->db->query($sQuery);
        $aResultFilterTotal = (array)$qry->row();
        $iFilteredTotal = $aResultFilterTotal['FOUND_ROWS()'];

        /* Total data set length */
        $sQuery = "
            SELECT COUNT(policy_num)
            FROM   insurances ins
            INNER JOIN premium_status pstat
            WHERE $mycondition
        ";
        $qry = $this->db->query($sQuery);
        $aResultTotal = (array)$qry->row();
        $iTotal = $aResultTotal['COUNT(policy_num)'];

        //return $this->db->queries;

        //return data we got
        return $data = array(
            "rResult" => $rResult,
            "iFilteredTotal" => $iFilteredTotal,
            "iTotal" => $iTotal
            );
    }


    /* function to check duplicacy of values */
    function check_duplicate($tableName, $condition, $join = null) {
        $this->db->select('*');
        $this->db->from($tableName);
        if($join) {
            $this->db->join($join['table'],$join['on'],$join['type']);
        }
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function add_policy($data)
    {
        if(!($this->db->insert('insurance_policies', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function get_ins_policy_broker_dropdown($condition)
    {
        $this->db->select('ins_policy_id, policy_num');
        $this->db->from('insurance_policies');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_advisor_broker_dropdown($broker_id)
    {
        $this->db->select('adviser_id, adviser_name');
        $this->db->from('advisers');
        $this->db->where('broker_id',$broker_id);
        $this->db->or_where('broker_id',null);
        $query = $this->db->get();
        return $query->result();
    }

    function get_policy_details($condition)
    {
        $this->db->select('ins_companies.ins_comp_name, insurance_policies.ins_comp_id as ins_comp_id, ins_plans.plan_name,
        insurance_policies.plan_id as plan_id, ins_plan_types.plan_type_name, ins_plan_types.plan_type_id as plan_type_id');
        $this->db->from('insurance_policies');
        $this->db->join('ins_companies', 'insurance_policies.ins_comp_id = ins_companies.ins_comp_id', 'inner');
        $this->db->join('ins_plans', 'insurance_policies.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('ins_plan_types', 'ins_plan_types.plan_type_id = ins_plans.plan_type_id', 'inner');

        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_insurance_policy_details($condition)
    {
        $this->db->select('policy_num');
        $this->db->from('insurances');
        $this->db->join('clients', 'insurances.client_id = clients.client_id', 'inner');
        $this->db->join('families', 'clients.family_id = families.family_id', 'inner');

        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function add_insurance($data)
    {
        if(!($this->db->insert('insurances', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function update_insurance($data, $where)
    {
        if(!($this->db->update('insurances', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function delete_insurance($condition)
    {
        $error = '0';
        if(!($this->db->delete('insurances', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }
    //Insurance End

    //Maturity start
    function get_maturity_details($condition)
    {
        $this->db->select('maturity_id, policy_num, maturity_date, amount');
        $this->db->from('premium_maturities');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    function update_maturity_amt($amount, $date, $polNum)
    {
        $query = "update premium_maturities set maturity_date = '$date', amount = $amount
		where policy_num = '$polNum' order by maturity_date asc limit 1";
        if(!($this->db->query($query))) {
            $error = $this->db->error();
            return $error;
        } else {
            return true;
        }
    }

    function add_maturity($row)
    {
        if(!($this->db->insert('premium_maturities', $row))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function delete_maturity($where)
    {
        $this->db->where($where);
        $error = '';
        if(!($this->db->delete('premium_maturities'))) {
            $error = $this->db->error();
        }
        return $error;
    }
    //Maturity end

    ////Premium Details
    function get_premium_details($condition)
    {
        $this->db->select('premium_id, prem.policy_number, cheque_number, date_format(cheque_date, "%d/%m/%Y") as cheque_date, bank.bank_name,
        branch, premium_amount, advisers, adjustment, adjustment_ref_number, narration, prem.bank_id,
        date_format(next_premium_due_date, "%d/%m/%Y") as next_premium_due_date, account_number, prem.client_id');
        $this->db->from('premium_transactions as prem');
        $this->db->join('banks as bank', 'prem.bank_id = bank.bank_id', 'left');
        $this->db->join('clients as client', 'prem.client_id = client.client_id', 'left');
        $this->db->join('families as fam', 'client.family_id = fam.family_id', 'left');
        $this->db->order_by('premium_id', 'desc');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function add_premium($data)
    {
        if(!($this->db->insert('premium_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function get_last_premium_id($condition)
    {
        $this->db->select('premium_id');
        $this->db->from('premium_transactions as prem');
        $this->db->join('clients as client', 'prem.client_id = client.client_id', 'inner');
        $this->db->join('families as fam', 'client.family_id = fam.family_id', 'inner');
        $this->db->where($condition);
        $this->db->order_by('premium_id', 'desc');
        $this->db->limit('1');
        $query = $this->db->get();
        return $query->result();

    }

    function update_premium($data, $condition)
    {
        if(!($this->db->update('premium_transactions', $data, $condition)))
        {
            return $this->db->error();
        }
        else
        {
            return $this->db->affected_rows();
        }
    }

    function delete_premium($where)
    {
        $error = '';
        if(!($this->db->delete('insurance_unit_linked_plans', $where))) {
            $error = $this->db->error();
        }
        elseif(!($this->db->delete('insurance_traditional_plans', $where))){
            $error = $this->db->error();
        }
        elseif(!($this->db->delete('premium_transactions', $where))){
            $error = $this->db->error();
        }
        return $error;
    }
    //premium end

    //temp function
    function temp_generate_premium($where)
    {
        $query = $this->db->query("call temp_premium_add(?, ?, ?, ?, ?, ?)", $where);
        return $query;
    }
    //temp function end

    ////Premium Status
    function get_premium_status()
    {
        $this->db->select('status_id, status');
        $this->db->from('premium_status');
        $query = $this->db->get();
        return $query->result();
    }

    ////Premium Status
    function get_premium_status_for($where)
    {
        $this->db->select('status_id, status');
        $this->db->from('premium_status');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    ////Premium Modes
    function get_premium_modes($whereMode = null)
    {
        $this->db->select('mode_id, mode_name');
        $this->db->from('premium_modes');
        if($whereMode)
            $this->db->where($whereMode);
        $query = $this->db->get();
        return $query->result();
    }

    //update premium_paying_details
    function update_prem_paying($data)
    {
        $query = $this->db->query("call sp_updatePremiumPayingDetails(?, ?, ?, ?, ?, ?, ?)", $data);
        if($query) {
            return true;
        } else {
            return $this->db->error();
        }

    }

    ////Premium Pay Modes
    function get_premium_pay_modes($wherePay = null)
    {
        $this->db->select('prem_pay_mode_id, prem_pay_mode');
        $this->db->from('premium_pay_modes');
        if($wherePay)
            $this->db->where($wherePay);
        $query = $this->db->get();
        return $query->result();
    }

    /* Client Insurance Policies START */
    function get_client_policies($client_id)
    {
        $this->db->select($this->client_columns);
        $this->db->from('insurance_policies');
        $this->db->join('ins_companies', 'insurance_policies.ins_comp_id = ins_companies.ins_comp_id', 'inner');
        $this->db->join('ins_plans', 'insurance_policies.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('ins_plan_types', 'ins_plan_types.plan_type_id = ins_plans.plan_type_id', 'inner');
        $this->db->where('insurance_policies.client_id', $client_id);
        $i=0;
        foreach($this->client_columns  as $col_item)
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
        else if(isset($this->client_order))
        {
            $order = $this->client_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function client_policies_count_filtered($clientID) {
        $this->db->select($this->client_columns);
        $this->db->from('insurance_policies');
        $this->db->join('ins_companies', 'insurance_policies.ins_comp_id = ins_companies.ins_comp_id', 'inner');
        $this->db->join('ins_plans', 'insurance_policies.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('ins_plan_types', 'ins_plan_types.plan_type_id = ins_plans.plan_type_id', 'inner');
        $this->db->where('insurance_policies.client_id', $clientID);
        $i=0;
        foreach($this->client_columns as $col_item)
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
        else if(isset($this->client_order))
        {
            $order = $this->client_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function client_policies_count_all($clientID)
    {
        $this->db->from('insurance_policies');
        $this->db->join('ins_companies', 'insurance_policies.ins_comp_id = ins_companies.ins_comp_id', 'inner');
        $this->db->join('ins_plans', 'insurance_policies.plan_id = ins_plans.plan_id', 'inner');
        $this->db->join('ins_plan_types', 'ins_plan_types.plan_type_id = ins_plans.plan_type_id', 'inner');
        $this->db->where('insurance_policies.client_id', $clientID);
        return $this->db->count_all_results();
    }

    function delete_policy($condition)
    {
        $error = true;
        $this->db->where($condition);
        if(!($this->db->delete('insurance_policies'))) {
            $error = $this->db->error();
        }
        return $error;
    }
    /* Client Insurance Policies END */


    //Imports Start
    //Function for Fund Option
    function delete_fund_value_data($where)
    {
        $error = '';
        $this->db->where($where);
        if(!($this->db->delete('fund_options'))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function add_fund_value($data)
    {
        if(!($this->db->insert('fund_options', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function add_fund_value_import($data)
    {
         foreach($data as $item)
        {
            $this->db->insert('fund_options', $item);
        }
        
       // if(!($this->db->insert_batch('fund_options', $data))) {
       //     $error = $this->db->error();
        //    return $error;
    //    } else {
            return $this->db->insert_id();
    //    }
    }

    function get_fund_option_details($where)
    {
        
        $query = $this->db->query("call sp_get_fund_options(?, ?)", $where);
        return $query->result();
    }

    function get_total_fund_value($where)
    {
        $query = $this->db->query("select getFundValue(?, ?) as fundValue", $where);
        return $query->result();
    }

    //function to get system fund value - Salmaan 10/08/16
    function get_system_fund_value($where) {
        $this->db->select('fund_value');
        $this->db->from('insurances');
        $this->db->where($where);
        $query = $this->db->get();
        if($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //Function for Real Stakes
    function delete_stake_value_data($where)
    {
        $error = '';
        $this->db->where($where);
        if(!($this->db->delete('real_stakes'))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function add_stake_value($data)
    {
        if(!($this->db->insert('real_stakes', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    function get_total_stake_value($where)
    {
        $query = $this->db->query("select getStakeValue(?, ?) as stakeValue", $where);
        return $query->result();
    }

    //added by Salmaan - 28-04-16
    function addImportPolicy($newPolData)
    {
        $result = $this->db->insert_batch('insurance_policies', $newPolData);
        if($result) {
            return true;
        } else {
            return false;
        }
    }

    function addImportIns($insData, $matData, $remData, $premData)
    {
        foreach($insData as $item)
        {
            $this->db->insert('insurances', $item);
        }
        
        //$result = $this->db->insert_batch('insurances', $insData);
        
        
        foreach($premData as $item)
        {
            $this->db->query("call temp_premium_add(?, ?, ?, ?, ?, ?)", $item);
        }
        if($matData != null)
        {
            $this->db->insert_batch('premium_maturities', $matData);
        }
        if($remData != null)
        {
            //$this->db->insert_batch('today_reminders', $remData);
            //commented for time-being, maybe its not required - Salmaan 23-08-16
        }
    }
    //Imports End

    //Reports Start
    //Function for Insurance Related Reports
    function get_insurance_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_insurance_by_client_report(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_insurance_by_family_report(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }

    //report of general insurance, sub-report of insurance report
    function get_general_insurance_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_gen_ins_by_client_report(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_gen_ins_by_family_report(?, ?)", $where);
        }
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;
    }

    //Report for premium calender
    function get_premium_calendar_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_premium_calender_client (?, ?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_premium_calender_family (?, ?, ?)", $where);
        }
        //To execute multiple queries
        if($query) {
            $res = $query->result();
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return false;
        }
    }

    //Report of lapse policy report, sub-report of premium calender report
    function get_lapse_policy_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_lapse_client (?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_lapse_family (?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        $query->next_result();
        $query->free_result();
        return $res;
    }
    //Reports End
}
