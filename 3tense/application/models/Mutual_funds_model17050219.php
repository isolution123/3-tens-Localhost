<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mutual_funds_model extends CI_Model{
    var $table = 'mutual_fund_transactions as mf';
    var $column = array("transaction_id, mf.client_id, c.name as client_name, c.family_id, fam.name as family_name, scheme_name,
    mfs.scheme_type_id, mft.scheme_type,
    mutual_fund_type, transaction_type, folio_number, Date_Format(purchase_date, '%d/%m/%Y') as purchase_date, quantity, nav, mf.amount, adjustment,balance_unit,
    orig_trxn_no, orig_trxn_type, adjustment_ref_number, adjustment_flag, mf.bank_id, b.bank_name, branch, account_number, cheque_number,mfs.prod_code");
    var $order = array('mf.purchase_date' => 'desc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'mf.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('mutual_fund_schemes as mfs', 'mfs.scheme_id = mf.mutual_fund_scheme', 'inner');
        $this->db->join('mf_scheme_types as mft', 'mft.scheme_type_id = mfs.scheme_type_id', 'inner');
        $this->db->join('banks as b', 'mf.bank_id = b.bank_id', 'left');

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

    public function count_all($condition)
    {
        $this->db->from($this->table);
        $this->db->join('clients as c', 'mf.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('mutual_fund_schemes as mfs', 'mfs.scheme_id = mf.mutual_fund_scheme', 'inner');
        $this->db->join('banks as b', 'mf.bank_id = b.bank_id', 'left');
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    ////Mutual Funds
    function get_mutual_funds($condition)
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

    ////Mutual Funds Start - server side datatables
    function get_mutual_funds_extended($mycondition, $condition, $order="", $limit="")
    {
        //if($order == '') { $order = $this->order; }
        //$this->get_datatables_query();
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('clients as c', 'mf.client_id = c.client_id', 'inner');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->join('mutual_fund_schemes as mfs', 'mfs.scheme_id = mf.mutual_fund_scheme', 'inner');
        $this->db->join('mf_scheme_types as mft', 'mft.scheme_type_id = mfs.scheme_type_id', 'inner');
        $this->db->join('banks as b', 'mf.bank_id = b.bank_id', 'left');
        $this->db->where('('.$mycondition.')');
        if($condition != "") {
            $this->db->where('('.$condition.')');
        }
        if($order != "") {
            $this->db->order_by($order);
        }

        //$query = $this->db->get();
        //$query0 = $this->db->queries[0]; //get query in string format
        $query0 = $this->db->get_compiled_select();  //get query in string format without executing
        //return $query0;
        //add SQL_CAL_FOUND_ROWS in the query after SELECT
        $query = substr_replace($query0, ' SQL_CALC_FOUND_ROWS ', intval(strpos($query0, 'SELECT'))+6, 0);
        $query = substr_replace($query, ' FORCE INDEX (PRIMARY) ', intval(strpos($query, 'INNER')), 0);
        //add limit
        $query .= " LIMIT ".$limit;
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
            SELECT COUNT(transaction_id)
            FROM   mutual_fund_transactions mf
            WHERE $mycondition
        ";
        $qry = $this->db->query($sQuery);
        $aResultTotal = (array)$qry->row();
        $iTotal = $aResultTotal['COUNT(transaction_id)'];

        //return $this->db->queries;

        //return data we got
        return $data = array(
            "rResult" => $rResult,
            "iFilteredTotal" => $iFilteredTotal,
            "iTotal" => $iTotal
        );
    }

    //Mutual Funds Valuation Table
    /*function get_mutual_funds_valuation($condition) {
        $this->db->select("f.name as family_name, c.name as client_name, v.client_id, v.mf_scheme_name, v.scheme_id,
            v.folio_number, DATE_FORMAT(v.purchase_date,'%d/%m/%Y') as purchase_date, v.mf_scheme_type as trn_type,
            v.p_amount, v.p_nav, v.c_nav, DATE_FORMAT(v.c_nav_date,'%d/%m/%Y') as c_nav_date, v.live_unit, v.div_r2, v.div_payout,
            v.div_amount, v.transaction_day, v.mf_abs, v.cagr, v.scheme_type, v.current_value");
        $this->db->from("mf_valuation_reports v");
        $this->db->join("clients c","c.client_id=v.client_id","inner");
        $this->db->join("families f","f.family_id=c.family_id","inner");
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }*/
	function get_mutual_funds_valuation($condition) {
        $this->db->select("f.name as family_name, c.name as client_name, t.client_id, s.scheme_name as mf_scheme_name, t.mutual_fund_scheme as scheme_id,
            t.folio_number, DATE_FORMAT(t.purchase_date,'%d/%m/%Y') as purchase_date, t.mutual_fund_type as trn_type,
            round(v.p_amount) as p_amount, t.nav as p_nav, v.c_nav, DATE_FORMAT(v.c_nav_date,'%d/%m/%Y') as c_nav_date, v.live_unit, round(v.div_r2) as div_r2, round(v.div_payout) as div_payout,
            round(v.div_amount) as div_amount, v.transaction_day, v.mf_abs, v.mf_cagr as cagr, st.scheme_type, round(v.c_nav * v.live_unit) as current_value");
        $this->db->from("mutual_fund_valuation v");
		$this->db->join("mutual_fund_transactions t","t.transaction_id=v.transaction_id","inner");
		$this->db->join("mutual_fund_schemes s","t.mutual_fund_scheme = s.scheme_id","inner");
		$this->db->join("mf_scheme_types st","s.scheme_type_id = st.scheme_type_id","inner");
        $this->db->join("clients c","c.client_id=t.client_id","inner");
        $this->db->join("families f","f.family_id=c.family_id","inner");
        $this->db->where($condition);
        $query = $this->db->get();
		//return $this->db->queries;
        if($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    function get_mutual_funds_valuation_extended($mycondition, $condition, $order="", $limit="") {
        $this->db->select("s.prod_code,f.name as family_name, c.name as client_name, t.client_id, s.scheme_name as mf_scheme_name, t.mutual_fund_scheme as scheme_id,
            t.folio_number, DATE_FORMAT(t.purchase_date,'%d/%m/%Y') as purchase_date, t.mutual_fund_type as trn_type,
            round(v.p_amount) as p_amount, t.nav as p_nav, v.c_nav, DATE_FORMAT(v.c_nav_date,'%d/%m/%Y') as c_nav_date, v.live_unit, round(v.div_r2) as div_r2, round(v.div_payout) as div_payout,
            round(v.div_amount) as div_amount, v.transaction_day, v.mf_abs, v.mf_cagr as cagr, st.scheme_type, round(v.c_nav * v.live_unit, 2) as current_value");
        $this->db->from("mutual_fund_valuation v");
		$this->db->join("mutual_fund_transactions t","t.transaction_id=v.transaction_id","inner");
		$this->db->join("mutual_fund_schemes s","t.mutual_fund_scheme = s.scheme_id","inner");
		$this->db->join("mf_scheme_types st","s.scheme_type_id = st.scheme_type_id","inner");
        $this->db->join("clients c","c.client_id=t.client_id","inner");
        $this->db->join("families f","f.family_id=c.family_id","inner");
        $this->db->where('('.$mycondition.')');
        if($condition != "") {
            $this->db->where('('.$condition.')');
        }
        if($order != "") {
            $this->db->order_by($order);
        }

        //$query = $this->db->get();
        //$query0 = $this->db->queries[0]; //get query in string format
        $query0 = $this->db->get_compiled_select();  //get query in string format without executing
        //return $query0;
        //add SQL_CAL_FOUND_ROWS in the query after SELECT
        $query = substr_replace($query0, ' SQL_CALC_FOUND_ROWS ', intval(strpos($query0, 'SELECT'))+6, 0);
        //add limit
        $query .= " LIMIT ".$limit;
        $query = $this->db->query($query);
        $rResult = $query->result_array();
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
            SELECT COUNT(v.transaction_id)
            FROM mutual_fund_valuation v
            INNER JOIN mutual_fund_transactions t ON v.transaction_id = t.transaction_id
            WHERE $mycondition
        ";
        $qry = $this->db->query($sQuery);
        $aResultTotal = (array)$qry->row();
        $iTotal = $aResultTotal['COUNT(v.transaction_id)'];

        //return $this->db->queries;

        //return data we got
        return $data = array(
            "rResult" => $rResult,
            "iFilteredTotal" => $iFilteredTotal,
            "iTotal" => $iTotal
        );
    }


    //get last transaction of mf
    function get_last_trans() {
        $this->db->select('max(transaction_id) as transID');
        $this->db->from('mutual_fund_transactions');
        $query = $this->db->get();
        if($query) {
            return $query->row();
        } else {
            return false;
        }
    }


     ////Mutual Funds
    function get_mutual_funds_purchase_red($condition)
    {
        $this->get_datatables_query();
        $this->db->where($condition);
        //$this->db->order_by("mf.purchase_date","desc");
        $this->db->limit(5);
        $query = $this->db->get();
        //var_dump($query);
        return $query->result();
    }

    /*function add_mutual_fund($data)
    {
        if(!($this->db->insert('mutual_fund_transactions', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            //return $this->db->insert_id();
            $transID = $this->db->insert_id();
            //now insert the latest records into mf_temp
            if(!empty($transID) || $transID === 0) {
                $qry = "INSERT INTO mutual_fund_temp
                        SELECT * FROM mutual_fund_transactions
                        WHERE transaction_id = ".$transID."
                        AND broker_id = '".$this->session->userdata('broker_id')."' 
                        ORDER BY transaction_id LIMIT 1";
                $result2 = $this->db->query($qry);
                if($result2) {
                    //return true;
                    $res_val = $this->valuation_temp_table($this->session->userdata('broker_id'), ($transID-1));
    			    if($res_val == true) {
        			    $partial = 1;
        			} else {
        			    $partial = 0;
        			}
        			
                    $res_val = $this->valuation_proc($this->session->userdata('broker_id'), $partial);
    			    if($res_val == true) {
        			    return $transID;
        			} else {
        			    return $this->db->error();
        			}
                } else {
                    return $this->db->error();
                }
            }
            //return $transID;
            return true;
        }
    }*/
    
    function add_mutual_fund($data)      //@pallavi:2017-06-29
    {
        $data['mutual_fund_scheme']=$data['scheme_id'];
        $data['orig_trxn_no']='manual';
        unset($data['scheme_id']);
        echo"<pre>";print_r($data);//exit;
        
        $table1='mutual_fund_transactions';
        foreach ($data as $key => $val) {
            $columns[]    = $this->db->escape_identifiers($key);
            $val = $this->db->escape($val);
            $values[]     = $val;
            if($key!='client_id'){
                $upd_values[] = $key.'='.$val;
            }

        }
        $sql = "INSERT INTO ". $this->db->dbprefix($table1) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
        $result2= $this->db->query($sql);
        // echo $this->db->last_query();
        //   $my_id = mysql_insert_id();
        if(!($result2)) {
            /*$error = $this->db->error();
            return $error;*/
        } else {
            //return $this->db->insert_id();
            $transID = $this->db->insert_id();
            //now insert the latest records into mf_temp
            if(!empty($transID) || $transID === 0) {
                $qry = "INSERT INTO mutual_fund_temp
                        SELECT * FROM mutual_fund_transactions
                        WHERE transaction_id = ".$transID."
                        AND broker_id = '".$this->session->userdata('broker_id')."' 
                        ORDER BY transaction_id LIMIT 1";
                $result2 = $this->db->query($qry);
                if($result2) {
                    //return true;
                    $res_val = $this->valuation_temp_table($this->session->userdata('broker_id'), ($transID-1));
    			    if($res_val == true) {
        			    $partial = 1;
        			} else {
        			    $partial = 0;
        			}
        			
                    $res_val = $this->valuation_proc($this->session->userdata('broker_id'), $partial);
    			    if($res_val == true) {
        			    return $transID;
        			} else {
        			    return $this->db->error();
        			}
                } else {
                    return $this->db->error();
                }
            }
            //return $transID;
            return true;
        }
    }

    function update_mutual_fund($data, $condition)
    {
        if(!($this->db->update('mutual_fund_transactions', $data, $condition))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->affected_rows();
        }
    }

    function delete_mutual_fund($condition)
    {
        $error = '';
        if(!($this->db->delete('mutual_fund_transactions', $condition))) {
            $error = $this->db->error();
        }
        return $error;
    }

    function get_folio_number($condition)
    {
        $this->db->select('folio_number');
        $this->db->from('mutual_fund_transactions');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    ////Mutual Fund Types
    function get_mf_types_broker_dropdown($condition)
    {
        $this->db->select('mutual_fund_type');
        $this->db->from('mutual_fund_types');
        $this->db->where($condition);
        $this->db->order_by('mutual_fund_type', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    ////Mutual Fund Schemes
    function get_mf_schemes_broker_dropdown($condition)
    {
        $query = $this->db->query("SELECT DISTINCT `scheme_id`, `scheme_name` FROM `mutual_fund_schemes`
                            WHERE ".$condition." ORDER BY `scheme_name` ASC");
        /*$this->db->select('scheme_id, scheme_name');
        $this->db->distinct(true);
        $this->db->from('mutual_fund_schemes');
        $this->db->where($condition);
        $this->db->order_by('scheme_name', 'asc');
        $query = $this->db->get();*/
        //return $this->db->queries;
        return $query->result();
    }

    ////Mutual Fund Schemes
    function get_mf_redemption_schemes($condition)
    {
        $this->db->select('scheme_id, scheme_name');
        $this->db->distinct(true);
        $this->db->from('mutual_fund_schemes as mfs');
        $this->db->join('mutual_fund_transactions as mf', 'mfs.scheme_id = mf.mutual_fund_scheme', 'inner');
        $this->db->where($condition);
        $this->db->order_by('scheme_name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    ////Mutual Fund Scheme Types
    function get_mf_scheme_types_dropdown($condition)
    {
        $this->db->select('scheme_type_id, scheme_type');
        $this->db->distinct(true);
        $this->db->from('mf_scheme_types');
        $this->db->where($condition);
        $this->db->order_by('scheme_type', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    ////Mutual Fund Schemes List/History
    function get_mf_schemes_hist($condition)
    {
        $this->db->select('sh.scheme_history_id, sh.scheme_id, s.scheme_name, s.prod_code,s.isin,s.scheme_type_id, st.scheme_type, sh.current_nav,
                            Date_format(sh.scheme_date,"%d/%m/%Y") AS scheme_date');
        //$this->db->distinct(true);
        $this->db->from('mf_schemes_histories sh');
        $this->db->join('mutual_fund_schemes s','s.scheme_id = sh.scheme_id','inner');
        $this->db->join('mf_scheme_types st','st.scheme_type_id = s.scheme_type_id','left');
        $this->db->where($condition);
        $this->db->order_by('sh.scheme_date', 'desc');
        $this->db->order_by('s.scheme_name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    //added by Salmaan - 3-5-16
    function get_scheme_nav($condition) {
        $this->db->select('scheme_history_id, scheme_id, current_nav, scheme_date');
        $this->db->from('mf_schemes_histories');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    //Import Start
    //Function for mutual fund import
    function add_import_mutual_funds($mfData, $transID)
    {
        $this->db->query("DELETE FROM mutual_fund_temp WHERE broker_id = '".$this->session->userdata('broker_id')."'");
        //$this->db->query("START TRANSACTION");
        //$this->db->trans_start();
        //$this->db->trans_begin();
        //$this->db->query("SET autocommit = 0;");
        //$this->db->query("SET foreign_key_checks = 0;");
        
        //$result = $this->db->insert_batch('mutual_fund_transactions', $mfData);
        foreach($mfData as $mf){  //@ pallavi:2017-06-28
            $sql = $this->db->insert_string('mutual_fund_transactions', $mf) . ' ON DUPLICATE KEY UPDATE added_on = added_on';
            $this->db->query($sql);
            $result = $this->db->insert_id();
        }
        
        //$this->db->query("SET foreign_key_checks = 1;");
        //$this->db->query("COMMIT");
        //$this->db->trans_complete();
        /*if ($this->db->trans_status() === FALSE)
        {
            //$this->db->trans_rollback();
            return false;
        }
        else
        {
            //$this->db->trans_commit();
            return true;
        }*/
        /*if($result) {
            //return true;
            //now insert the latest records into mf_temp
            if(!empty($transID) || $transID === 0) {
                $qry = "INSERT INTO mutual_fund_temp
                        SELECT * FROM mutual_fund_transactions
                        WHERE transaction_id >= ".$transID."
                        AND broker_id = '".$this->session->userdata('broker_id')."'
                        ORDER BY purchase_date asc, transaction_type asc, quantity desc, adjustment_ref_number asc, transaction_id asc";
                $result2 = $this->db->query($qry);
                if($result2) {
                    return true;
                } else {
                    return $this->db->error();
                }
            } else {
                return $transID;
            }

        } else {
            return $this->db->error();
        }
       /* if($remData != null)
        {
            //$this->db->insert_batch('today_reminders', $remData);
        }*/
       
        if($result) {
        		
			//call procedure for live_units calculation
			$query1 = $this->db->query("call sp_mf_calculate_live_units(?, ?)", array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID));
			
			//call procedure for creating temp_trans table for dividend calculations
			$query2 = $this->db->query("call sp_mf_calculate_div_temp_trans(?, ?)", array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID));
			
			$res_val = $this->valuation_proc($this->session->userdata('broker_id'), 1);
			if($res_val == true) {
			    return true;
			} else {
			    $this->valuation_proc($this->session->userdata('broker_id'), 1);
			    if($res_val == true) {
    			    return true;
    			} else {
    			    return array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID);  
    			}
			}
			
            
			//now insert the latest records into mf_temp
            if(!empty($transID) || $transID === 0) {
                $qry = "INSERT INTO mutual_fund_temp
                        SELECT * FROM mutual_fund_transactions
                        WHERE transaction_id >= ".$transID."
                        AND broker_id = '".$this->session->userdata('broker_id')."'
                        ORDER BY purchase_date asc, transaction_type asc, quantity desc, adjustment_ref_number asc, transaction_id asc";
                $result2 = $this->db->query($qry);
                if($result2) {
                    return true;
                } else {
                    return $this->db->error();
                }
            } else {
                return $transID;
            }

        } else {
            return $this->db->error();
        }
        
    }
    
    function valuation_live_units($brokerID, $transID = 0) {
        $query1 = $this->db->query("call sp_mf_calculate_live_units(?, ?)", array('brokerID'=>$brokerID, 'transID'=>$transID));
        if(!$query1) {
            return $this->db->error();
        } else {
            return true;
        }
    }
     function valuation_live_units_historical_f($brokerID, $reportDate,$familyID) {
        $query1 = $this->db->query("call sp_mf_calculate_live_units_historical(?, ?,?)", array('brokerID'=>$brokerID, 'reportDate'=>$reportDate,'familyID'=>$familyID));
        if(!$query1) {
            return $this->db->error();
        } else {
            return true;
        }
    }
     function valuation_live_units_historical_c($brokerID, $reportDate,$clientID) {
        $query1 = $this->db->query("call sp_mf_calculate_live_units_historical_c(?, ?,?)", array('brokerID'=>$brokerID, 'reportDate'=>$reportDate,'clientID'=>$clientID));
        if(!$query1) {
            return $this->db->error();
        } else {
            return true;
        }
    }
    
    //function for creating temp table for running dividend calculations
    function valuation_temp_table($brokerID, $transID = 0) {
        $query1 = $this->db->query("call sp_mf_calculate_div_temp_trans(?, ?)", array('brokerID'=>$brokerID, 'transID'=>$transID));
        if(!$query1) {
            return $this->db->error();
        } else {
            return true;
        }
    }
    
    //function for import transaction procedure - Salmaan - 2017-04-20
    function valuation_proc($brokerID, $partialCalc = 0) {
        //start transaction
        $this->db->trans_start();
		
		/*
		//call procedure for divr
		$query2 = $this->db->query("call sp_mf_calculate_divr(?)", array('brokerID'=>$this->session->userdata('broker_id')));
		
		//call procedure for divp
		$query3 = $this->db->query("call sp_mf_calculate_divp(?)", array('brokerID'=>$this->session->userdata('broker_id')));
		*/
		
		//call procedure for divr
		$query2 = $this->db->query("call sp_mf_calculate_divr(?, ?)", array('brokerID'=>$brokerID, 'partialCalc'=>$partialCalc));
		
		//call procedure for divp
		$query3 = $this->db->query("call sp_mf_calculate_divp(?, ?)", array('brokerID'=>$brokerID, 'partialCalc'=>$partialCalc));
		
		//call procedure for current nav update
		$query4 = $this->db->query("call sp_mf_update_c_nav(?)", array('brokerID'=>$this->session->userdata('broker_id')));
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return $this->db->error();
        }
        else
        {
                $this->db->trans_commit();
                return true;
        }
    }
    
    

	function addImportNAV($navData)
    {
        // modified by Salmaan - will be used only for importing Dummy NAVs - 2017-04-24
        /*$qry = "INSERT INTO `mf_schemes_histories`(scheme_id, current_nav, scheme_date)
                VALUES
                    (".$navData['scheme_id'].", ".$navData['current_nav'].", '".$navData['scheme_date']."')
                ON DUPLICATE KEY UPDATE
                    current_nav = VALUES(current_nav);";
        if(!$this->db->query($qry)) {
            return $this->db->error();
        } else {
            return true;
        }*/
        
        if(!$this->db->insert_batch('mf_schemes_histories', $navData)) {
            return $this->db->error();
        } else {
            return true;
        }
    }

    //check if mf_scheme exists or not
    function check_scheme_name_exists($scheme_name)
    {
        $query = $this->db->query('SELECT DISTINCT `scheme_id`, `scheme_name` FROM `mutual_fund_schemes`
        WHERE `scheme_name` = "'.$scheme_name.'"');
        return $query->result();
    }
    //check if mf_scheme prod_code exists or not
    function check_scheme_code_exists($scheme_code)
    {
        $query = $this->db->query('SELECT DISTINCT `scheme_id`, `scheme_name`, `prod_code` FROM `mutual_fund_schemes`
        WHERE `prod_code` = "'.$scheme_code.'"');
        return $query->result();
    }
    //check if mf_scheme prod_code exists or not
    function check_scheme_isin_exists($isin)
    {
        $query = $this->db->query('SELECT DISTINCT `scheme_id`, `isin` FROM `mutual_fund_schemes_isin`
        WHERE `isin` = "'.$isin.'"');
        return $query->result();
    }

    //check if data exists for today's date
    function check_mf_scheme_hist_today($condition) {
        $this->db->select('*');
        $this->db->from('mf_schemes_histories');
        $this->db->where($condition);
        $query = $this->db->get();
        if($query) {
            if(count($query->result()) == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //delete mf_scheme_hist data for today's date
    function delete_mf_scheme_hist_today($condition) {
        $this->db->delete('mf_schemes_histories', $condition);
    }

    //Add mutual fund scheme
    function add_scheme($schemeData)
    {
        if(!$this->db->insert('mutual_fund_schemes',$schemeData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    //Import End

    //Reports Start
    //Function for Mutual Fund Related Reports
    
    // Dipak 2017-04-22 - Multiple reports
    function get_mutual_fund_family_summary_portfolio($type,$where)
    {
      $query="";
      if($type=='family')
      {
        $query = $this->db->query("call sp_mf_schemewise_summary_report_clientwise(?,?,?)", $where);
      }
      else {
        $query = $this->db->query("call sp_mf_schemewise_summary_report_clientwise(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    // Dipak 2017-04-22 - Multiple reports
    function get_mutual_fund_family_summary_portfolio_historical($type,$where)
    {
      
        $query = $this->db->query("call sp_mf_schemewise_summary_report_clientwise_historical(?,?,?,?)", $where);

          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    function get_mutual_fund_family_summary_typewise($type,$where)
    {
      $query="";
      if($type=='family')
      {
        $query = $this->db->query("call sp_mf_schemewise_summary_report_typewise(?,?,?)", $where);
      }
      else{
        $query = $this->db->query("call sp_mf_schemewise_summary_report_typewise(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
     function get_mutual_fund_family_summary_typewise_historical($type,$where)
    {
      $query="";
      
        $query = $this->db->query("call sp_mf_schemewise_summary_report_typewise_historical(?,?,?,?)", $where);
      
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    function get_mutual_fund_family_detail_schemewise($type,$where)
    {
      $query="";
      if($type=='family')
      {
          $query = $this->db->query("call sp_mf_schemewise_detail(?,?,?)", $where);
      } else {
        $query = $this->db->query("call sp_mf_schemewise_detail(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    function get_mutual_fund_family_detail_schemewise_historical($type,$where)
    {
      $query="";
      
        $query = $this->db->query("call sp_mf_schemewise_detail_historical(?,?,?,?)", $where);
      
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    function get_mutual_fund_family_summary_schemewise_net_investment($type,$where)
    {
      $query="";
      if($type=='family')
      {
        $query=$this->db->query("call sp_mf_schemewise_summary_net_invest(?,?,?)", $where);
      }
      else {
        $query=$this->db->query("call sp_mf_schemewise_summary_net_invest(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    function get_mutual_fund_family_summary_schemewise_net_investment_historical($type,$where)
    {
      $query="";
      
        $query=$this->db->query("call sp_mf_schemewise_summary_net_invest_historical(?,?,?,?)", $where);
      
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    //Dipak 24 march 07-03-2017
    function get_clientwise_summary($type,$where)
    {
      $query="";
      //echo '<pre>';print_r($where);die;
      if($type=='family')
      {
        $query = $this->db->query("call sp_mf_clientwise_summary(?,?,?)", $where);
      }
      else {
        $query=$this->db->query("call sp_mf_clientwise_summary(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    function get_clientwise_summary_historical($type,$where)
    {
        $query="";
        //echo '<pre>';print_r($where);die;
        $query = $this->db->query("call sp_mf_clientwise_summary_historical(?,?,?,?)", $where);
        if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    //Dipak 4 April
    function mf_comman_scheme_summary($type,$where)
    {
      $query="";
      // print_r($type);
      if($type=='family')
      {
        $query = $this->db->query("call sp_mf_comman_scheme_summary(?,?,?)",$where);
      }
      else {
          $query=$this->db->query("call sp_mf_comman_scheme_summary(?,?,?)", $where);
      }
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
          // print_r($query);
    }
    function mf_comman_scheme_summary_historical($type,$where)
    {
      $query="";
      
          $query=$this->db->query("call sp_mf_comman_scheme_summary_historical(?,?,?,?)", $where);
      
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
          // print_r($query);
    }
    
    //Dipak Client portal Mutual fund report 24 April 2017
    function get_clientwise_details($type,$where)
    {
        $query="";
        $query = $this->db->query("call sp_mf_clientwise_details(?,?,?)", $where);
        if($query)
            {
            $res = $query->result();
            $query->next_result();
            $query->free_result();
            return $res;
            } else {
            return $this->db->error();
        }
    }
    
    
    //Dipak 05 April
    function  folio_wise_summary($type,$where)
    {
        $query="";
        if($type=='family')
        {
            $query = $this->db->query("call sp_mf_folio_wise(?,?,?)",$where);
        }
        else {
            $query=$this->db->query("call sp_mf_folio_wise(?,?,?)", $where);
        }
        if($query)
        {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
        } 
        else {
              return $this->db->error();
        }
    }
      function  folio_wise_summary_historical($type,$where)
    {
        $query="";
            $query = $this->db->query("call sp_mf_folio_wise_historical(?,?,?,?)",$where);
        
        if($query)
        {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
        } 
        else {
              return $this->db->error();
        }
    }
    // Dipak Report part END
    
    
    
    
    // Akshay karde (Editied Dipak) - 2017-05-26
    function get_sip_report($type,$where)
    {
        $query="";
        $query = $this->db->query("call sp_mf_sip(?,?,?)", $where);
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }

    function get_sip_report_historical($type,$where)
    {
        $query="";
        $query = $this->db->query("call sp_mf_sip_historical(?,?,?,?)", $where);
          if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }

    
    
    function get_mutual_fund_report($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            //$query = $this->db->query("call sp_mf_valuation_client(?, ?)", $where);
            $query = $this->db->query("call sp_mf_report_client(?, ?)", $where);
        }
        else
        {
            //$query = $this->db->query("call sp_mf_valuation_family(?, ?)", $where);
            $query = $this->db->query("call sp_mf_report_family(?, ?)", $where);
        }
        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return $this->db->error();
        }
    }
    function get_mutual_fund_report_historical($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            //$query = $this->db->query("call sp_mf_valuation_client(?, ?)", $where);
            $query = $this->db->query("call sp_mf_report_client_historical(?, ?, ?)", $where);
        }
        else
        {
            //$query = $this->db->query("call sp_mf_valuation_family(?, ?)", $where);
            $query = $this->db->query("call sp_mf_report_family_historical(?, ?, ?)", $where);
        }
        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return $this->db->error();
        }
    }


    function get_net_investment($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("select calculateNetInvestmentClient(?, ?) as net_Investment", 
                $where);
        }
        else
        {
            $query = $this->db->query("select calculateNetInvestmentFamily(?, ?) as net_Investment", 
                $where);
        }
        $res = $query->result();
        return $res;
    }
     function get_net_investment_historical($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("select calculateNetInvestmentClient_historical(?, ?, ?) as net_Investment", $where);
        }
        else
        {
            $query = $this->db->query("select calculateNetInvestmentFamily_historical(?, ?, ?) as net_Investment", $where);
        }
        $res = $query->result();
        return $res;
    }

    function get_investment_summary_historical($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_investment_summary_client_historical(?, ?,?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_investment_summary_family_historical(?, ?,?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;

        /*if($query) {
            $result = $query->result();
            //To execute multiple queries
            //$res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();

            $sql = $result[0]->sql;
            if(!empty($sql)) {
                $q2 = $this->db->query($sql);
                if($q2) {
                    $res = $q2->result();
                } else {
                    $res = false;
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        return $res;*/
    }
    function get_investment_summary($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_investment_summary_client(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_investment_summary_family(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;

        /*if($query) {
            $result = $query->result();
            //To execute multiple queries
            //$res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();

            $sql = $result[0]->sql;
            if(!empty($sql)) {
                $q2 = $this->db->query($sql);
                if($q2) {
                    $res = $q2->result();
                } else {
                    $res = false;
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        return $res;*/
    }

    ////calculate mutual fund liveunit for all the records separately
    function cal_mf_live_unit($brokerId)
    {
        $this->db->query("call sp_calculate_live_units_mf(?)", $brokerId);
    }

    function get_current_value_summary($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_current_value_summary_client(?, ?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_current_value_summary_family(?, ?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;

        /*if($query) {
            $result = $query->result();
            //To execute multiple queries
            //$res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();

            $sql = $result[0]->sql;
            if(!empty($sql)) {
                $q2 = $this->db->query($sql);
                if($q2) {
                    $res = $q2->result();
                } else {
                    $res = false;
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        return $res;*/
    }
     function get_current_value_summary_historical($type, $where)
    {
        $query = "";
        if($type == 'client')
        {
            $query = $this->db->query("call sp_get_current_value_summary_client_historical(?, ?,?)", $where);
        }
        else
        {
            $query = $this->db->query("call sp_get_current_value_summary_family_historical(?, ?,?)", $where);
        }
        //To execute multiple queries
        $res = $query->result();
        // changes in system/database/drivers/mysqli/mysqli_result.php
        // added a new functon called next_result()
        $query->next_result();
        $query->free_result();
        return $res;

        /*if($query) {
            $result = $query->result();
            //To execute multiple queries
            //$res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();

            $sql = $result[0]->sql;
            if(!empty($sql)) {
                $q2 = $this->db->query($sql);
                if($q2) {
                    $res = $q2->result();
                } else {
                    $res = false;
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        return $res;*/
    }
    //Report End


    //function for MF Valuation
    /*function mf_valuation($condition) {
        //unset($query);
        $query = $this->db->query("call sp_mf_valuation_broker_family(?, ?)", $condition);
        if($query) {
            //To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;
        } else {
            return $this->db->error();
        }
    }*/
	function mf_valuation($condition) {
        $query = $this->db->query("call sp_mf_valuation(?,?)", $condition);
        //return $this->db->queries;
        if($query) {
            /*//To execute multiple queries
            $res = $query->result();
            // changes in system/database/drivers/mysqli/mysqli_result.php
            // added a new functon called next_result()
            $query->next_result();
            $query->free_result();
            return $res;*/
            return true;
        } else {
            return $this->db->error();
        }
    }

    function delete_mf_valuation($condition) {
        if(!$this->db->delete("mf_valuation_reports", $condition)) {
            return $this->db->error();
        } else {
            return true;
        }
    }
    
    function mf_calculate_divp($condition)
    {
          $query = $this->db->query("call sp_mf_calculate_divp(?, ?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }

    function mf_calculate_divp_historical($condition)
    {
          $query = $this->db->query("call sp_mf_calculate_divp_historical(?, ?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }
    function mf_calculate_divr($condition)
    {
          $query = $this->db->query("call sp_mf_calculate_divr(?, ?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }
      function mf_calculate_divr_historical($condition)
    {
          $query = $this->db->query("call sp_mf_calculate_divr_historical(?, ?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }

    function mf_update_c_nav($condition)
    {
          $query = $this->db->query("call sp_mf_update_c_nav(?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }
    function mf_update_c_nav_historical($condition)
    {
          $query = $this->db->query("call sp_mf_update_c_nav_historical(?,?)", $condition);
          if($query)
          {
              return true;
          } else {
              return $this->db->error();
          }
    }

    function broker_list($condition = '1=1')
    {
      $this->db->select('id,name');
      $this->db->where($condition);
      //$this->db->where('user_type','broker' );
      //$this->db->or_where('broker_id',null);
      $this->db->where('(user_type="broker" OR broker_id IS NULL)');
      $query = $this->db->get('users');
      //var_dump($this->db->queries);
      return $query->result();
    }
    
    public function insert_nav($data)
    {
      $this->db->insert_batch('mf_schemes_histories', $data);
      return ($this->db->affected_rows());
    }
    
    //Dipak MF reports - 2017-06-12
    //Dipak 26 April
    function  get_aum_report($where)
    {
        $query="";
        $query = $this->db->query("call sp_mf_aum_report(?)",$where);
        if($query)
           {
              $res = $query->result();
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }
    
    //Dipak 11 may
      function capital_gain($type,$where)
      {
        // print_r($where);
          $query="";
          $query = $this->db->query("call sp_mf_capitalgain_report(?,?,?,?,?)", $where);
            if($query)
             {
                $res = $query->result();
                $query->next_result();
                $query->free_result();
                return $res;
              } else {
                return $this->db->error();
            }
      }
    
     // Dipak 7 June 2016 
    function follio_master($type,$where)
    {
      $query="";
      $query=$this->db->query('call sp_mf_folio_master(?,?,?)',$where);
      if($query)
      {
        $res=$query->result();
        $query->next_result();
        $query->free_result();
        return $res;
      }
      else {
      return $this->db->error();
      }
    }
    
    //Akshay - 2017-06-22
    function get_mf_schemes_broker_dropdown_ajax($condition)
    {
        $query = $this->db->query("SELECT  `scheme_id` as `id`, CONCAT(`scheme_name`,' - [',`prod_code`,']') as `text` FROM `mutual_fund_schemes`
                            WHERE ".$condition." ORDER BY `scheme_name` ASC");

        return $query->result();
    }
    
    
    ////function to mf import for auto   @pallavi:2017-06-21  //Manual Intervention
  function add_auto_import_error($data){
  //  $result = $this->db->insert_batch('mutual_fund_transactions_auto_error', $mfData);
    $table='mutual_fund_transactions_auto_error';
    $columns    = array();
     $values     = array();
     $upd_values = array();
     foreach ($data as $key1 => $val1){
      //  echo"<pre>";print_r($val1);//exit;
           foreach ($val1 as $key => $val) {
             //echo $key;
               $columns[]    = $this->db->escape_identifiers($key);
               $val = $this->db->escape($val);
               $values[]     = $val;
               if($key!='client_id'){
                 $upd_values[] = $key.'='.$val;
               }

           //       echo"<pre>";print_r($columns);echo"<br>";
           //         echo"<pre>";print_r($values);echo"<br>";
           //           echo"<pre>";print_r($upd_values);echo"<br>";
           // }

         }
         $sql = "INSERT INTO ". $this->db->dbprefix($table) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
         $result= $this->db->query($sql);
          // echo $this->db->last_query();//exit;
         unset($columns,$values,$upd_values);
   }
return true;
  }
    
    
    // //function to mf import for auto   @pallavi:2017-06-21
function auto_add_import_mutual_funds($mfData, $transID){
//  echo"<pre>";print_r($mfData);exit;
     $table='mutual_fund_transactions';
     $columns    = array();
      $values     = array();
      $upd_values = array();
  foreach ($mfData as $key1 => $val1){
  //   echo"<pre>";print_r($val1);//exit;
        foreach ($val1 as $key => $val) {
          //echo $key;
            $columns[]    = $this->db->escape_identifiers($key);
            $val = $this->db->escape($val);
            $values[]     = $val;
            if($key!='client_id'){
              $upd_values[] = $key.'='.$val;
            }

        //       echo"<pre>";print_r($columns);echo"<br>";
        //         echo"<pre>";print_r($values);echo"<br>";
        //           echo"<pre>";print_r($upd_values);echo"<br>";
        // }

      }
      $sql = "INSERT INTO ". $this->db->dbprefix($table) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
      $result= $this->db->query($sql);
       // echo $this->db->last_query();//exit;
      unset($columns,$values,$upd_values);
}
          if($result) {

  			//call procedure for live_units calculation
  			$query1 = $this->db->query("call sp_mf_calculate_live_units(?, ?)", array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID));

  			//call procedure for creating temp_trans table for dividend calculations
  			$query2 = $this->db->query("call sp_mf_calculate_div_temp_trans(?, ?)", array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID));

  			$res_val = $this->valuation_proc($this->session->userdata('broker_id'), 1);
  			if($res_val == true) {
  			    return true;
  			} else {
  			    $this->valuation_proc($this->session->userdata('broker_id'), 1);
  			    if($res_val == true) {
      			    return true;
      			} else {
      			    return array('brokerID'=>$this->session->userdata('broker_id'), 'transID'=>$transID);
      			}
  			}
  			//now insert the latest records into mf_temp
              if(!empty($transID) || $transID === 0) {
                  $qry = "INSERT INTO mutual_fund_temp
                          SELECT * FROM mutual_fund_transactions
                          WHERE transaction_id >= ".$transID."
                          AND broker_id = '".$this->session->userdata('broker_id')."'
                          ORDER BY purchase_date asc, transaction_type asc, quantity desc, adjustment_ref_number asc, transaction_id asc";
                  $result2 = $this->db->query($qry);
                  if($result2) {
                      return true;
                  } else {
                      return $this->db->error();
                  }
              } else {
                  return $transID;
              }

          } else {
              print_r($this->db->error());
              return $this->db->error();
          }
}

   //@pallavi:2017-07-17 manual intervension  for email
  function get_all_mf_error_record($rta_type){
    $this->db->select('*');
    $this->db->from('mutual_fund_transactions_auto_error');
    $this->db->where('email_status','0');
    $this->db->where('rta_type',$rta_type);
    $query=$this->db->get();
    if($query) {
        $result=$query->result_array();
    } else {
        $result = false;
    }
    return $result;
  }
  //@pallavi:2017-07-17 manual intervension  for update email status
  function update_mf_email_status($rta_type){
    $this->db->set('email_status','1');
    $this->db->where('email_status','0');
    $this->db->where('rta_type',$rta_type);
    $this->db->update('mutual_fund_transactions_auto_error');
    return true;
  }
  
  function  get_mf_cagr_mail_data($broker_id)
    {
        $query="";
        $query = $this->db->query("call sp_mf_cagr_mail(?)",array('brokerID'=>$broker_id));
        if($query)
           {
              $res = $query->result_array();;
              $query->next_result();
              $query->free_result();
              return $res;
            } else {
              return $this->db->error();
          }
    }

    
}
