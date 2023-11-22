<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients_model extends CI_Model {
    var $table = 'clients';
    var $column = array('client_id, clients.name as c_name, clients.family_id, families.name as f_name, relation_HOF, client_type, client_type_name, email_id,date_format(dob,"%d/%m/%Y") as dob, pan_no, mobile, username, date_format(date_of_comm,"%d/%m/%Y") as date_of_comm, client_category, (case when clients.status = 1 then "Active" else "Inactive" end) as status');
    var $merging_col=array('client_id, clients.name as c_name,families.name as f_name,merge_ref_id');
    var $order = array('client_id' => 'desc');

    var $trading_column = array('id','broker', 'trading_broker_name', 'client_code','balance','held_type');
    var $trading_order = array('id' => 'desc');

    //var $contact_column = array('client_contact_id','contact_category_id','contact_category_name','client_id','flat','street','area','city','state','pin','telephone','mobile','email_id');
    //var $contact_order = array('id' => 'desc');

    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }

    /*function get_client_types_dropdown()
    {
        $this->db->select('client_type_id, client_type_name');
        $this->db->from('client_types');
        $query = $this->db->get();
        return $query->result();
    }*/

    function get_limit($brokerID)
   {
       $this->db->select('client_limit');
       $this->db->from('users');
       $this->db->where('id',$brokerID);
       $query = $this->db->get();
       return $query->row();
   }

   function count_client($brokerID)
    {
      $this->db->select('count(client_id) as count');
      $this->db->from('clients');
      $this->db->where('user_id',$brokerID);
      $query = $this->db->get();
      return $query->row();
    }

    function get_client_types_dropdown($condition='1=1')
    {
        $this->db->select('client_type_id, client_type_name');
        $this->db->from('client_types');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_occupations_dropdown($condition='1=1')
    {
        $this->db->select('occupation_id, occupation_name');
        $this->db->from('occupations');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_document_types_dropdown($condition='1=1')
    {
        $this->db->select('document_type_id, document_type');
        $this->db->from('document_types');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    /*function get_document_types_dropdown()
    {
        $this->db->select('document_type_id, document_type');
        $this->db->from('document_types');
        $query = $this->db->get();
        return $query->result();
    }*/

    function get_new_client_id()
    {
        $this->db->select('clientID() as client_id');
        $query = $this->db->get();
        return $query->row();
    }

    function add_client($data)
    {
        if($this->db->insert('clients', $data)) {
            //return true;
            //Pallavi - 2017-06-12
            //for fetching last inserted client Id for storing its bank details
            $this->db->select('*');
             $this->db->from('clients');
             $this->db->order_by('date_created','desc');
             $this->db->order_by('client_id','desc');
             $result = $this->db->get()->result();
             $last_id = $result[0]->client_id;//This is the last client ID of the table
             //echo $this->db->last_query();
            return $last_id;
          //  return $this->db->insert_id();
        } else {
           // echo"error while insert add client";
            return $this->db->error();
        }
        //return $this->db->queries;
    }

    function update_client($data)
    {
        $this->db->where('client_id', $data['client_id']);
        $this->db->update('clients', $data);
        return true;
    }

    function update_hof($where, $data) {
        $this->db->where($where);
        if(!$this->db->update('clients',$data)) {
            return $this->db->error();
        } else {
            return true;
        }
    }

    function delete_client($id)
    {
        $this->db->where('client_id', $id);
        if(!$this->db->delete('clients')) {
            return false;
        } else {
            return true;
        }
    }

    function get_client_info($clientID)
    {
        $this->db->select('*');
        $this->db->from('clients');
        $this->db->where('client_id', $clientID);
        $query = $this->db->get();
        return $query->row();
    }

    function get_client_family_info_by_code($condition)
    {
        $this->db->select('c.client_id, c.name, c.family_id, f.name as famName');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_brokers cb','cb.client_id = c.client_id','inner');
        $this->db->join('users u','u.id = cb.user_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    //karan 10/02/2016
    function get_client_family_by_pan($condition)
    {
        $this->db->select('c.client_id, c.name as client_name, c.family_id,c.merge_ref_id, f.name as family_name');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }
    
    // Akshay Karde - 10/05/2017
     function get_client_family_by_inv_name($condition)
   {
       $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name');
       $this->db->from('clients c');
       $this->db->join('families f','f.family_id = c.family_id','inner');
       $this->db->where($condition);
       $query = $this->db->get();
       return $query->row();
   }

	function get_client_family($clientID)
    {
        $this->db->select('f.family_id, f.name');
        $this->db->from('families f');
        $this->db->join('clients c','f.family_id = c.family_id','inner');
        $this->db->where('client_id', $clientID);
        $query = $this->db->get();
        return $query->row();
    }

	function get_clients_broker_dropdown($condition)
    {
        //$this->db->select('client_id, c.name');
        $this->db->select('client_id, c.name,date_format(c.dob,"%d/%m/%Y")as dob2,c.pan_no'); //Pallavi - 2017-06-12
        $this->db->from('clients as c');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->where($condition);
        //Pallavi - 2017-06-12
        $this->db->where('c.merge_ref_id',null); //added by Pallavi for client merging.
        $this->db->order_by('c.name','asc');
        
        $query = $this->db->get();
        return $query->result();
    }

    function get_client_trading_brokers($condition)
    {
        $this->db->distinct();
        $this->db->select('trading_broker_id, trading_broker_name');
        $this->db->from('trading_brokers tb');
        $this->db->join('client_brokers cb', 'cb.broker = tb.trading_broker_id', 'inner');
        $this->db->join('clients c', 'c.client_id = cb.client_id', 'inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_trading_broker_client_code($condition)
    {
        $this->db->select('client_code');
        $this->db->from('client_brokers');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    function get_client_code_balance($condition)
    {
        $this->db->select('balance');
        $this->db->from('client_brokers');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->row();
    }

    private function get_datatables_query()
    {
        $this->db->select($this->column);
        $this->db->from($this->table);
        $this->db->join('families','clients.family_id = families.family_id','inner');
        $this->db->join('client_types','clients.client_type = client_types.client_type_id','inner');
        
        //Pallavi - 2017-06-12
        $this->db->where('clients.merge_ref_id',null); //added by Pallavi for client merging.
        $i=0;
        foreach($this->column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
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
    
    //Pallavi - 2017-06-12
    private function get_datatables_query1()
    {

        $this->db->select($this->$merging_col);
        $this->db->from($this->table);
        $this->db->join('families','clients.family_id = families.family_id','inner');
      //  $this->db->join('client_types','clients.client_type = client_types.client_type_id','inner');
      //  $this->db->where('clients.merge_ref_id ',null); //added by Pallavi for client merging.
        $i=0;
        foreach($this->column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
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

    /* below 3 functions for Client List data */
    function count_filtered($brokerID)
    {
        $this->get_datatables_query();
        $this->db->where('families.broker_id', $brokerID);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($brokerID)
    {
        $this->db->from($this->table);
        $this->db->join('families','families.family_id = clients.family_id','inner');
        $this->db->where('broker_id', $brokerID);
        return $this->db->count_all_results();
    }

    function get_clients_broker($brokerID)
    {
        $this->get_datatables_query();
        $this->db->where('families.broker_id', $brokerID);
        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }


    /* below functions for Client Trading details */
    function get_client_tradings($clientID) {
        $this->db->select($this->trading_column);
        $this->db->from('client_brokers c');
        $this->db->join('trading_brokers t', 'c.broker = t.trading_broker_id', 'inner');
        $this->db->where('client_id', $clientID);
        $i=0;
        foreach($this->trading_column  as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->trading_order))
        {
            $order = $this->trading_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        if(isset($_POST['length']) && $_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        //return $this->db->queries;
        return $query->result();
    }

    function client_tradings_count_filtered($clientID) {
        $this->db->select($this->trading_column);
        $this->db->from('client_brokers c');
        $this->db->join('trading_brokers t', 'c.broker = t.trading_broker_id', 'inner');
        $this->db->where('client_id', $clientID);
        $i=0;
        foreach($this->trading_order as $col_item)
        {
            if(isset($_POST['search']['value']))
                ($i===0) ? $this->db->like($col_item, $_POST['search']['value']) : $this->db->or_like($col_item, $_POST['search']['value']);
            $column[$i] = $col_item;
            $i++;
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        else if(isset($this->trading_order))
        {
            $order = $this->trading_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function client_tradings_count_all($clientID)
    {
        $this->db->from('client_brokers');
        $this->db->where('client_id', $clientID);
        return $this->db->count_all_results();
    }

    public function add_trading($data)
    {
        if($this->db->insert('client_brokers',$data))
        {
            $insert_id = $this->db->insert_id();
            $this->db->select('trading_broker_id, trading_broker_name');
            $this->db->from('client_brokers c');
            $this->db->join('trading_brokers t', 'c.broker = t.trading_broker_id', 'inner');
            $this->db->where('c.id', $insert_id);
            $query = $this->db->get();
            return $query->row();
        }
        //return true;
    }

    function delete_trading($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('client_brokers');
        return true;
    }
    /* Trading functions END */



    public function get_contact_categories() {
        $this->db->from('contact_categories');
        $query =  $this->db->get();
        return $query->result();
    }

    /* below functions for Client Contact details */
    function get_client_contacts($clientID) {
        $this->db->select('*');
        $this->db->from('client_contact_details');
        $this->db->join('contact_categories','client_contact_details.contact_category_id = contact_categories.contact_category_id','inner');
        $this->db->where('client_id', $clientID);

        $query = $this->db->get();
        return $query->result();
    }

    public function add_contact($data)
    {
        $this->db->insert('client_contact_details',$data);
        return true;
    }

    public function delete_contact($id)
    {
        $this->db->where('client_contact_id', $id);
        $this->db->delete('client_contact_details');
        return true;
    }
    /* Contact functions END */


    /* function to check duplicacy of values */
    function check_duplicate($tableName, $condition, $join = null) {
        $this->db->select('*');
        $this->db->from($tableName);
        if($join) {
            $this->db->join($join['table'],$join['on'],$join['type']);
        }
        $this->db->where($condition);
        //$this->db->where(' OR broker_id is null');
        $query = $this->db->get();
        //$var = $this->db->queries;
        //echo $var[0];
        return $query->result();
    }

    function check_duplicate_trading($tableName, $condition) {
        $this->db->select('*');
        $this->db->from($tableName);
        $this->db->join('clients','client_brokers.client_id = clients.client_id','inner');
        $this->db->where($condition);
        //$this->db->where(' OR broker_id is null');
        $query = $this->db->get();
        //$var = $this->db->queries;
        //echo $var[0];
        return $query->result();
    }



    /* Occupation functions START */
    function get_occupations($condition = '1=1') {
        $this->db->select('occupation_id, occupation_name, broker_id');
        $this->db->from('occupations');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_occupation($data)
    {
        if(!($this->db->insert('occupations', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_occupation($data, $where)
    {
        if(!($this->db->update('occupations', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['occupation_id'];
        }
    }

    public function delete_occupation($id)
    {
        if(!($this->db->delete('occupations', array('occupation_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Occupation functions END */


    /* Client Type functions START */
    function get_client_types($condition = '1=1') {
        $this->db->select('client_type_id, client_type_name, broker_id');
        $this->db->from('client_types');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_client_type($data)
    {
        if(!($this->db->insert('client_types', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_client_type($data, $where)
    {
        /*if(!($this->db->update('client_types', $data, $where))) {
            $error = $this->db->error();
            if ($error['code'] == '1062' || $error['code'] == '1586') {
                // Duplicate Key
                return 'duplicate';
            } else {
                // Some other error.
                return $error;
            }
        } else {
            return true;
        }*/
        if(!($this->db->update('client_types', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['client_type_id'];
        }
    }

    public function delete_client_type($id)
    {
        if(!($this->db->delete('client_types', array('client_type_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Client Type functions END */


    /* Document Type functions START */
    function get_document_types($condition = 1) {
        $this->db->select('document_type_id, document_type, broker_id');
        $this->db->from('document_types');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    public function add_document_type($data)
    {
        if(!($this->db->insert('document_types', $data))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $this->db->insert_id();
        }
    }

    public function update_document_type($data, $where)
    {
        if(!($this->db->update('document_types', $data, $where))) {
            $error = $this->db->error();
            return $error;
        } else {
            return $data['document_type_id'];
        }
    }

    public function delete_document_type($id)
    {
        if(!($this->db->delete('document_types', array('document_type_id' => $id)))) {
            return $error = $this->db->error();
        } else {
            return true;
        }

    }
    /* Document Type functions END */


    //Function for mutual fund import
    function add_import_clients($clientData)
    {
        $result = $this->db->insert_batch('clients', $clientData);
        if(!$result) {
            return false;
        } else {
            return true;
        }
        /* if($remData != null)
         {
             //$this->db->insert_batch('today_reminders', $remData);
         }*/
    }
    
    
    /* new functions - 2017-06-12 */
    function check_client_id($condition)
    {
      $this->db->select('client_id');
      $this->db->from('clients');
      $this->db->where($condition);
      $query = $this->db->get();
      if(!$query)
      {
        return false;
      }
      else {
      return $query->row();
      }
    
    }
    
    ////Access Limit For client
    function get_access_limit($brokerID)
   {
       $this->db->select('client_access');
       $this->db->from('users');
       $this->db->where('id',$brokerID);
       $query = $this->db->get();
       return $query->row();
   }

   function count_access_client($brokerID)
    {
      $this->db->select('count(client_id) as count');
      $this->db->from('clients');
      $this->db->where('port_access',1);
      $this->db->where('user_id',$brokerID);
      $query = $this->db->get();
      return $query->row();
    }
    
    // Function to remove folders and files
        // function rrmdir($dir) {
        //     if (is_dir($dir)) {
        //         $files = scandir($dir);
        //         foreach ($files as $file)
        //             if ($file != "." && $file != "..") $this->rrmdir("$dir/$file");
        //         rmdir($dir);
        //     }
        //     else if (file_exists($dir)) unlink($dir);
        // }

        // Function to Copy folders and files
        function rcopy($src, $dst) {
        //  echo "source file:".$src."******Destination file:".$dst."\n";
      //    echo "in file transfer";
          //  if (file_exists ( $dst ))
            //  $this->rrmdir ( $dst );
            if (is_dir ( $src )) {
                //mkdir ( $dst );
                $files = scandir ( $src );
              //  print_r($files);
                foreach ( $files as $file )
                    if ($file != "." && $file != "..")
                       $this->rcopy ( "$src/$file", "$dst/$file" );
            }else if (file_exists ( $src )==true){
               $xmlFile1 = pathinfo($src);
               $xmlFile2 = pathinfo($dst);
               $sfile=$xmlFile1['basename'];//fetch source file name
               $exfile=$xmlFile1['extension'];//extension of source file
               $nmfile=$xmlFile1['filename'];//name of source file
               $destpath=$xmlFile1['dirname']; //get source file path
                ///////////////////
                $sfile2=$xmlFile2['basename'];//fetch source file name
                $exfile2=$xmlFile2['extension'];//extension of source file
                $nmfile2=$xmlFile2['filename'];//name of source file
                $destpath2=$xmlFile2['dirname']; //get source file path
                //echo $destpath2."\n";
                //echo $destpath2.$sfile."\n";
               //check source file is present at destination
               if (file_exists($destpath2."/".$sfile)) {
                //  echo"in if";
                      $newname=$nmfile.rand(0,9)."copy".".".$exfile;
                    //  echo $newname."\n";
                      $s1=$destpath2."/".$newname;  // new copy creation of file for destination with destination folder path
                      $d1=$destpath2."/";
                 copy ( $src, $s1);
               }else{
                 //echo"in else";
                   copy ( $src, $dst );
               }

            }

        }
        
        // function for merging client name   new:@pallavi on 12 jun 2017
 function merge_client_name($check,$uncheck,$brokerID){
//echo"<pre>"; print_r($data);//exit;
/* complete all the info fields  of merged client i.e. if some info fields values are not present with it,then take it from its dummy client info fileds  */
/*array for data to be checked for empty and should be taken from the dummy client data  array*/
$check_index=array('email_id','dob','occupation_id','relation_HOF','client_type','spouse_name','anv_date','pan_no','passport_no','add_flat','add_street','add_area','add_city','add_state','add_pin','telephone','mobile','children_name','client_category');
/*first fetch main merged clients all fields info*/

$arr_1=array();
$this->db->select('*');
$this->db->from($this->table);
$this->db->where('client_id',$check[0]);
$query1=$this->db->get();
$main_client=$query1->result_array();

foreach($uncheck as $temp){
/* first fecth dummy client data record*/
$this->db->select('*');
$this->db->from($this->table);
$this->db->where('client_id',$temp);
  $query=$this->db->get();
  $cl_arr=$query->result_array();
  $temp1=array_filter($main_client[0]);
// echo"<pre>";print_r($temp1);
  $arr_1 = array_diff_assoc($main_client[0],$temp1);
//echo"<pre>";print_r($arr_1);
  //fetch the values macthing to the arra_1 index from temp2 array
  if(!empty($arr_1)){
  foreach ($arr_1 as $key => $value) {
    # code...
    if(in_array($key,$check_index)){
    if(array_key_exists($key, $cl_arr[0])){
    $main_client[0][$key]=$cl_arr[0][$key];
    }
  }
  }

}else{
  break;
 }
 }

//echo"<pre>";print_r($main_client[0]);

$this->db->where('client_id', $check[0]);
$this->db->update('clients', $main_client[0]);
//echo $this->db->last_query();
if ($this->db->affected_rows() >= 0){
  //echo "client having id=".$check[0]."have been updated";
  foreach($uncheck as $key=>$val){

    if($uncheck[$key]!=$check[0]){
       $search_id=$uncheck[$key];
      //first check the client id  for refered as  merge_ref_id  by any other dummy clients(Recursive merge)
      $this->db->select('client_id');
      $this->db->from('clients');
      $this->db->where('merge_ref_id',$search_id);
      $query=$this->db->get();
      $cl_arr=$query->result_array();
     // echo $this->db->last_query();
      $cl_update=array();
      if(!empty($cl_arr)){
        foreach($cl_arr as $key=>$val){
          array_push($cl_update,$cl_arr[$key]['client_id']);

        }
      }

       array_push($cl_update,$search_id);
      // print_r($cl_update);//exit;
 for($i=0;$i<count($cl_update);$i++){
 $this->db->where('client_id',$cl_update[$i]);
 $this->db->set('merge_ref_id',$check[0]);
 $this->db->update('clients');
  // echo $this->db->last_query();//die();

     /*copy all files from merging clients to merged clients folder*/
 $src = 'uploads/clients/'.$cl_update[$i].'';
 $dst = 'uploads/clients/'.$check[0].'';
 //echo $src."\n".$dst;
 $doc[$key]= $this->rcopy($src, $dst);  // Call function // Function to Copy folders and files
 }
   }
           }
                 return true;

}
 }
 
 
 //pallavi
    // function fUsername($data){
    //   echo $data;
    //   $temp=array();
    //   //$temp['uname']=$data;
    // //  $this->db->select("findUsername($data) ");
    //   //$query = $this->db->query(@findUsername,array('uname'=>$data));
    //   // call the stored procedure
    //         $query = $this->db->query("CALL findUsername(?)", array('uname'=>$data));
    //
    //
    //   //$query = $this->db->get();
    //   print_r($query->result());
    // //return $query;
    //     }
     function check_for_folio($folio,$productId,$brokerId){
       $this->db->select('*');
       $this->db->from('client_bank_details');
       $this->db->where('folio_number',$folio);
       if(!empty($productId)){
        $this->db->where('productId',$productId);
       }
       $this->db->where('client_family_broker_id',$brokerId);
         $result = $this->db->get()->result_array();
         return $result;
     }
     
     function add_client_bank_details($data){
      /*  //working code
      if($this->db->insert('client_bank_details', $data)) {
          return true;
      } else {
          return $this->db->error();
      }*/
    //   $sql = $this->db->insert_string('client_bank_details', $data) . ' ON DUPLICATE KEY UPDATE duplicate=LAST_INSERT_ID(".implode(', ', $data).")');
    //   $this->db->query($sql);
    //  $id = $this->db->insert_id();
    $table='client_bank_details';
    $columns    = array();
        $values     = array();
        $upd_values = array();
        foreach ($data as $key => $val) {
            $columns[]    = $this->db->escape_identifiers($key);
            $val = $this->db->escape($val);
            $values[]     = $val;
            $upd_values[] = $key.'='.$val;
        }
        $sql = "INSERT INTO ". $this->db->dbprefix($table) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
         $this->db->query($sql);

      // $sql = $this->db->insert_string('client_bank_details',$data) . ' ON DUPLICATE KEY UPDATE value=' .implode(', ', $data);
      //  $this->db->query($sql);
     // echo $this->db->last_query();//exit;
       /*$id = $this->db->insert_id();
       if($id){
         return true;
       }else{
         return $this->db->error();
       }*/
       return true;


    }
    
    
    function get_selected_client_info($data,$brokerID){
      $this->db->select($this->column);
      $this->db->select('add_flat,add_street,add_area,add_city,add_state,add_pin');
      $this->db->from($this->table);
      $this->db->join('families','clients.family_id = families.family_id','inner');
      $this->db->join('client_types','clients.client_type = client_types.client_type_id','inner');
      $this->db->where_in('clients.client_id',$data);
      $this->db->where('families.broker_id', $brokerID);
      $query = $this->db->get();
    // echo $this->db->last_query();
      return $query->result();
    }
    
    function get_client_family_by_withoutpan($condition)
    {
        $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row();
    }
    function get_client_family_by_withoutpan1($condition)
    {
        $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where($condition);
        $query = $this->db->get();
        echo $this->db->last_query();
        //return $query->row();
    }


      /*function get_client_family_by_scheme($data){
    //  echo"in query";
if(!empty($data['productId'])){

      if(!empty($data['folio_number']) && !empty($data['pan_no'])){
      $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
      $this->db->from('clients c');
      $this->db->join('families f','f.family_id = c.family_id','inner');
      $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
      $this->db->where('cb.productId',$data['productId']);
      $this->db->where('cb.folio_number',$data['folio_number']);
      $this->db->where('f.broker_id',$data['broker_id']);
      $this->db->where('c.pan_no',$data['pan_no']);
    }elseif(!empty($data['folio_number']) && !empty($data['clientName'])){
      $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
      $this->db->from('clients c');
      $this->db->join('families f','f.family_id = c.family_id','inner');
      $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
      $this->db->where('cb.productId',$data['productId']);
      $this->db->where('cb.folio_number',$data['folio_number']);
      $this->db->where('f.broker_id',$data['broker_id']);
         $this->db->where('cb.folio_number',$data['folio_number']);
          $this->db->where('c.name',$data['clientName']);
        }elseif(!empty($data['folio_number']) && !empty($data['clientName']) && !empty($data['pan_no'])){
          $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
          $this->db->from('clients c');
          $this->db->join('families f','f.family_id = c.family_id','inner');
          $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
          $this->db->where('cb.productId',$data['productId']);
          $this->db->where('f.broker_id',$data['broker_id']);
             $this->db->where('cb.folio_number',$data['folio_number']);
            //  $this->db->where('c.name',$data['clientName']);
              $this->db->where('c.pan_no',$data['pan_no']);
        }
      }else{
        //if product code not present--->codition in franklin file
        if(!empty($data['folio_number']) && !empty($data['clientName']) && !empty($data['pan_no'])){              //with pan condition
          $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
          $this->db->from('clients c');
          $this->db->join('families f','f.family_id = c.family_id','inner');
          $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        //  $this->db->where('cb.productId',$data['productId']);
          $this->db->where('f.broker_id',$data['broker_id']);
             $this->db->where('cb.folio_number',$data['folio_number']);
            //  $this->db->where('c.name',$data['clientName']);
              $this->db->where('c.pan_no',$data['pan_no']);
          }elseif(!empty($data['folio_number']) && !empty($data['clientName']) && empty($data['pan_no'])){             ////without pan condition
            $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
            $this->db->from('clients c');
            $this->db->join('families f','f.family_id = c.family_id','inner');
            $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
          //  $this->db->where('cb.productId',$data['productId']);
            $this->db->where('cb.folio_number',$data['folio_number']);
            $this->db->where('f.broker_id',$data['broker_id']);
               $this->db->where('cb.folio_number',$data['folio_number']);
                $this->db->where('c.name',$data['clientName']);
          }

      }
      $query = $this->db->get();
  //echo $this->db->last_query();
  //exit;
    //print_r($query->row());
      return $query->row();


    }*/
    
    function get_client_family_by_scheme($data){   //@pallavi:2017-06-16
    //  echo"in query";
if(!empty($data['productId'])){

      if(!empty($data['pan_no'])){                  //with pan
      $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
      $this->db->from('clients c');
      $this->db->join('families f','f.family_id = c.family_id','inner');
      $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
      $this->db->where('cb.productId',$data['productId']);
      $this->db->where('cb.folio_number',$data['folio_number']);
      $this->db->where('f.broker_id',$data['broker_id']);
      $this->db->where('c.pan_no',$data['pan_no']);
  //  }elseif(!empty($data['folio_number']) && !empty($data['clientName']) && empty($data['pan_no'])){            //without pan  //@pallavi   2017-06-15
   }elseif(empty($data['pan_no'])){            //without pan  //@pallavi   2017-06-15
      $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
      $this->db->from('clients c');
      $this->db->join('families f','f.family_id = c.family_id','inner');
      $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
      $this->db->where('cb.productId',$data['productId']);
      $this->db->where('cb.folio_number',$data['folio_number']);
      $this->db->where('f.broker_id',$data['broker_id']);
     // $this->db->where('c.name',$data['clientName']);
        }
        // elseif(!empty($data['folio_number']) && !empty($data['clientName']) && !empty($data['pan_no'])){
        //   $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
        //   $this->db->from('clients c');
        //   $this->db->join('families f','f.family_id = c.family_id','inner');
        //   $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        //   $this->db->where('cb.productId',$data['productId']);
        //   $this->db->where('f.broker_id',$data['broker_id']);
        //      $this->db->where('cb.folio_number',$data['folio_number']);
        //     //  $this->db->where('c.name',$data['clientName']);
        //       $this->db->where('c.pan_no',$data['pan_no']);
        // }
      }else{
        //if product code not present--->codition in franklin file
        if(!empty($data['folio_number']) && !empty($data['clientName']) && !empty($data['pan_no'])){              //with pan condition
          $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
          $this->db->from('clients c');
          $this->db->join('families f','f.family_id = c.family_id','inner');
          $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        //  $this->db->where('cb.productId',$data['productId']);
          $this->db->where('f.broker_id',$data['broker_id']);
             $this->db->where('cb.folio_number',$data['folio_number']);
            //  $this->db->where('c.name',$data['clientName']);
              $this->db->where('c.pan_no',$data['pan_no']);
          }elseif(!empty($data['folio_number']) && !empty($data['clientName']) && empty($data['pan_no'])){             ////without pan condition
            $this->db->select('c.client_id, c.name as client_name, c.family_id, f.name as family_name,cb.productId');
            $this->db->from('clients c');
            $this->db->join('families f','f.family_id = c.family_id','inner');
            $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
          //  $this->db->where('cb.productId',$data['productId']);
            $this->db->where('cb.folio_number',$data['folio_number']);
            $this->db->where('f.broker_id',$data['broker_id']);
               $this->db->where('cb.folio_number',$data['folio_number']);
                $this->db->where('c.name',$data['clientName']);
          }

      }
      $query = $this->db->get();
 // echo $this->db->last_query();
  //exit;
    //print_r($query->row());

      return $query->row();


    }
    
    
    function get_client_family_mergd_names($clientID)
      {
          $this->db->select('f.family_id, f.name,c.merge_ref_id');
          $this->db->from('families f');
          $this->db->join('clients c','f.family_id = c.family_id','inner');
          $this->db->where('client_id', $clientID);
          $query = $this->db->get();
          $result=$query->result_array();
        //  print_r($result);
          if($result[0]['merge_ref_id']==null){
            $this->db->select('client_id');
            $this->db->from('clients');
            $this->db->where('merge_ref_id',$clientID);
          //  echo $this->db->last_query();
          //  $this->db->group_by('merge_ref_id');
            $query1 = $this->db->get();
            $result1=$query1->result_array();
            $id_arr=array();
            foreach($result1 as $res){
              //$id_arr
              array_push($id_arr,$res['client_id']);
            }

          }
      // print_r($id_arr);
           $result[0]['merged_client_list']=$id_arr;
    // print_r($result);
        return $result;
      }


      //function to get original/merged client_id & family_id from merge_ref_id - Salmaan - 2017-05-26
      function get_client_family_merge_ref($condition)
        {
          $this->db->select('c.client_id, c.name, c.family_id, f.name,c.merge_ref_id');
          $this->db->from('families f');
          $this->db->join('clients c','f.family_id = c.family_id','inner');
        //  $this->db->where('c.client_id', $clientID);
        $this->db->where($condition);
          $query = $this->db->get();
            if($query) {
              $result=$query->result_array();
        //  print_r($result);
              if(!empty($result)) {
                if(isset($result[0]['merge_ref_id']) && !empty($result[0]['merge_ref_id'])) {
                  $this->db->select('client_id, name, family_id');
                  $this->db->from('clients');
                  $this->db->where('client_id', $result[0]['merge_ref_id']);
                //  echo $this->db->last_query();
                //  $this->db->group_by('merge_ref_id');
                  $query1 = $this->db->get();
                  if($query1) {
                    $result1=$query1->result_array();
                    return $result1;
                  } else {
                    return $result;
                  }
                } else {
                  return $result;//if merge ref_id==null then client is itself original no need to find it's original client info.
                }
             } else {
               return false;
             }
          } else {
            return false;
          }
        }


    /*function getClientId($data,$brokerId){

      if(!empty($data['cname']) && empty($data['pan'])){ //if only name available
        $this->db->select('client_id');
        $this->db->from('clients');
        $this->db->where('name',$data['cname']);

      }elseif(!empty($data['pan']) && empty($data['cname'])){ //if only pan available
        $this->db->select('client_id');
        $this->db->from('clients');
        $this->db->where('pan_no',$data['pan']);
      }elseif(!empty($data['cname']) && !empty($data['pan'])){ //if both available
        $this->db->select('client_id');
        $this->db->from('clients');
      //  $this->db->where('name',$data['cname']);
          $this->db->where('pan_no',$data['pan']);
      }
      $this->db->where('user_id',$brokerId);
      $query=$this->db->get();
    //echo $this->db->last_query();echo"\n";
       $result=$query->result_array();
       if( $query->num_rows()>0){
      //
       //  print_r($result);//exit;
          return $result[0]['client_id'];


      }else{
        return false;
         }

    }*/
    
    /*function getClientId($data){                    //@pallavi:2017-06-16

      if(!empty($data['cname']) && empty($data['pan'])){ //if only name available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('cb.productId',$data['productId']);
        $this->db->where('cb.folio_number',$data['folio_number']);
        $this->db->where('f.broker_id',$data['broker_id']);
        $this->db->where('c.name',$data['cname']);
      }elseif(!empty($data['pan']) && empty($data['cname'])){ //if only pan available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('f.broker_id',$data['broker_id']);
          $this->db->where('c.pan_no',$data['pan']);
      }elseif(!empty($data['cname']) && !empty($data['pan'])){ //if both available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('f.broker_id',$data['broker_id']);
        $this->db->where('c.pan_no',$data['pan']);
      }elseif(empty($data['cname'])&&empty($data['pan'])){   // if both name and pan not available but folio and  product id available in  bank details under the boker  table,then fetch  client id against it.
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('cb.productId',$data['productId']);
        $this->db->where('cb.folio_number',$data['folio_number']);
        $this->db->where('f.broker_id',$data['broker_id']);
      }

      $query=$this->db->get();
    //echo $this->db->last_query();echo"\n";
       $result=$query->result_array();
       if( $query->num_rows()>0){
      //
       //  print_r($result);//exit;
          return $result[0]['client_id'];


      }else{
        return false;
         }

    }*/
    
    function getClientId($data){                    //@pallavi:2017-06-16

      if(!empty($data['cname']) && empty($data['pan'])){ //if only name available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('cb.productId',$data['productId']);
        $this->db->where('cb.folio_number',$data['folio_number']);
        $this->db->where('f.broker_id',$data['broker_id']);
       // $this->db->where('c.name',$data['cname']);  pallavi@:23 Aug 2017 commented for karvy dbf file kriti shah 
      }elseif(!empty($data['pan']) && empty($data['cname'])){ //if only pan available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        //$this->db->join('client_bank_details cb','cb.client_id = c.client_id','left');
        $this->db->where('f.broker_id',$data['broker_id']);
        $this->db->where('c.pan_no',$data['pan']);
      }elseif(!empty($data['cname']) && !empty($data['pan'])){ //if both available
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        //$this->db->join('client_bank_details cb','cb.client_id = c.client_id','left');
        $this->db->where('f.broker_id',$data['broker_id']);
        $this->db->where('c.pan_no',$data['pan']);
      }elseif(empty($data['cname'])&&empty($data['pan'])){   // if both name and pan not available but folio and  product id available in  bank details under the boker  table,then fetch  client id against it.
        $this->db->select('c.client_id');
        $this->db->from('clients c');
        $this->db->join('families f','f.family_id = c.family_id','inner');
        $this->db->join('client_bank_details cb','cb.client_id = c.client_id','inner');
        $this->db->where('cb.productId',$data['productId']);
        $this->db->where('cb.folio_number',$data['folio_number']);
        $this->db->where('f.broker_id',$data['broker_id']);
      }

      $query=$this->db->get();
  //  echo $this->db->last_query();echo"\n";
       $result=$query->result_array();
       if( $query->num_rows()>0){
      //
       //  print_r($result);//exit;
          return $result[0]['client_id'];


      }else{
          //echo"error in client insert";
        return $this->db->error();
         }

    }
    
    
    function get_clients_familywise_summary($data){
    //  $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID, 'c.family_id' => $familyID);
     $where="";
     $where=" where c.status=1 and c.merge_ref_id is null";
     if($data['family_id']!=''){
       $where.=" and c.family_id='".$data['family_id']."'";
     }

    //  $sql1="select c.client_id,c.name,c.dob,c.pan_no from clients as c inner join(
    //        select SUBSTRING_INDEX( `name` , ' ', 1 ) as fname ,dob from clients
    //         group by  SUBSTRING_INDEX( `name` , ' ', 1 ),dob  having count(SUBSTRING_INDEX( `name` , ' ', 1 ))>1) as c1
    //       on c1.fname=SUBSTRING_INDEX( `name` , ' ', 1 ) and c1.dob = c.dob ".$where." order by SUBSTRING_INDEX( `name` , ' ', 1 ) asc";

          $sql1="select * from clients as c inner join(
select SUBSTRING_INDEX( c2.`name` , ' ', 1 ) as fname ,dob from clients as c2
inner join families as fam on fam.family_id = c2.family_id  where fam.broker_id='".$data['broker_id']."' and c2.status='1' and c2.merge_ref_id is null 
group by SUBSTRING_INDEX( c2.`name` , ' ', 1 ),c2.dob having count(SUBSTRING_INDEX( c2.`name` , ' ', 1 ))>1)
as c1 on c1.fname=SUBSTRING_INDEX(c. `name` , ' ', 1 ) and c1.dob = c.dob ".$where." order by SUBSTRING_INDEX( c.`name` , ' ', 1 ) asc";
      //  echo $sql1;//exit;
        $query=$this->db->query($sql1);
        $res=$query->result();
        return $res;
       print_r($res);exit;

   }


    function get_clients_broker_dropdown_for_merge($condition){
     $this->db->select('client_id,c.name');
        $this->db->from('clients as c');
        $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
   function get_referd_client_family_list($condition){  //change 0n 01 jun 2017 by Pallavi
      $this->db->select('client_id,c.name,c.merge_ref_id,fam.name as family_name');
      $this->db->from('clients as c');
      $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
      $this->db->where($condition);
      $this->db->where('c.merge_ref_id is null');//change 0n 01 jun 2017 by Pallavi
      $this->db->order_by('c.name','asc');
    //  echo $this->db->last_query();
      $query = $this->db->get();
      return $query->result();
    }
    function get_referd_client_duplicate_list($condition){
// first fetch family id of the client
$this->db->select('c.family_id');
$this->db->from('clients as c');
$this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
$this->db->where($condition);
$query1 = $this->db->get();
$res1=$query1->result_array();

      $this->db->select('client_id,c.name,c.merge_ref_id');
      $this->db->from('clients as c');
      $this->db->join('families as fam', 'fam.family_id = c.family_id', 'inner');
    //  $this->db->where($condition);
      $this->db->where('c.family_id',$res1[0]['family_id']);
      $this->db->order_by('c.name','asc');
      $query = $this->db->get();
      return $query->result();
    }

     function update_client_family($data){
  //   print_r($data);//exit;
      foreach($data['sel_client_id'] as $key=>$val){
            $this->db->where('client_id',$data['sel_client_id'][$key]);
            $this->db->set('family_id',$data['tansfer_family_id']);
            $this->db->update('clients');
          //echo $this->db->last_query();die();
          if ($this->db->affected_rows() > 0){
            // get all dummy client for this original client and then change their family id also
            $this->db->select('client_id');
            $this->db->from('clients');
            $this->db->where('merge_ref_id ',$data['sel_client_id'][$key]);
              //echo $this->db->last_query();
            $query1=$this->db->get();
              $res=$query1->result_array();
        //  print_r($res);
          if(count($res)>0){
            foreach($res as $k){
                  $this->db->where('client_id',$k['client_id']);
                  $this->db->set('family_id',$data['tansfer_family_id']);
                  $this->db->update('clients');
                }
          }


          }
               }
              //  if ($this->db->affected_rows() > 0)
              //   return TRUE;
              //   else
              //   return FALSE;
    return TRUE;
    }
    function check_family_head($id){
      $this->db->select('client_id,name,head_of_family');
      $this->db->from('clients');
      $this->db->where('client_id',$id);
     $query= $this->db->get();
     return  $query->row();

    }
    
    /*pallavi*/
    function get_merged_clients_details($brokerID)
    {

    //  $this->get_datatables_query();
    $this->db->select($this->column);
    $this->db->select('clients.merge_ref_id');
    $this->db->from($this->table);
    $this->db->join('families','clients.family_id = families.family_id','inner');
    $this->db->join('client_types','clients.client_type = client_types.client_type_id','inner');
    $this->db->where('clients.merge_ref_id is not',null); //added by Pallavi for client merging.
      $this->db->where('families.broker_id', $brokerID);
      if(isset($_POST['length']) && $_POST['length'] != -1)
      {
          $this->db->limit($_POST['length'], $_POST['start']);
      }
      $query = $this->db->get();

      return $query->result();

    }

    function fetch_client_nameByID($id){
      $this->db->select('name');
      $this->db->from($this->table);
      $this->db->where('client_id',$id);
      $query=$this->db->get();
      $res=$query->result_array();
      if($query->num_rows()>0){
          return $res[0]['name'];
      }else{
        return false;
      }

    }
    
    
    //Pallavi - 2017-06-14
    function get_product_code_from_mf_scheme($code){   //pallavi: 2017-06-13
        $this->db->select('scheme_name,prod_code');
        $this->db->from('mutual_fund_schemes');
        $this->db->where('other2',$code);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
        
        // if( $query->num_rows()>0){
        //   $result=$query->result_array();
        //    return $result;
        // }else{
        //  return false;
        //   }
    }
    
    /* end pallavi*/
    
    //@ pallavi:get User id based on ARN number  2017-06-27
    function get_userdata($arn)
    {
      $this->db->select('u.id,u.status,u.user_type');
      $this->db->from('users as u');
      $this->db->join('user_arn_mapping as ua','ua.user_id=u.id','left');
      $this->db->where('ua.arn',$arn);
      $query=$this->db->get();
    //  echo $this->db->last_query();
    //below code changed - Salmaan - 2017-10-12
    if($query) {
      $res=$query->result_array();
      return $res;
    } else {
        return false;
    }
    }
    //@pallavi 2017-09-06
    function get_Karvy_RTA_import_password($mailbackid){
      $this->db->select('u.karvy_rta_password');
      $this->db->from('users as u');
      $this->db->where('u.mailback_mail',$mailbackid);
      $query=$this->db->get();
  //   echo $this->db->last_query();
      $res=$query->result_array();
      //echo"<pre>";print_r($res);exit;
      //below code added by Salmaan - 2017-11-01
      if(!empty($res)) {
        return $res[0]['karvy_rta_password'];
      } else {
          return false;
      }
    }
    //@pallavi 2017-09-06
      function get_cams_RTA_import_password($mailbackid){
      $this->db->select('u.cams_rta_password');
      $this->db->from('users as u');
      $this->db->where('u.mailback_mail',$mailbackid);
      $query=$this->db->get();
  //   echo $this->db->last_query();
      $res=$query->result_array();
      //echo"<pre>";print_r($res);exit;
      //below code added by Salmaan - 2017-11-01
      if(!empty($res)) {
        return $res[0]['cams_rta_password'];
      } else {
          return false;
      }
    }
     //@pallavi 2017-12-06
    function get_RTA_import_password($mailbackid){
      $this->db->select('u.*');
      $this->db->from('users as u');
      $this->db->where('u.mailback_mail',$mailbackid);
      $query=$this->db->get();
  //   echo $this->db->last_query();
      $res=$query->result_array();
      //echo"<pre>";print_r($res);exit;

      if(!empty($res)) {
          return $res;
      } else {
          return false;
      }
    }
    
    
    //@ pallavi:get User/broker id for sip  2017-06-28
    function get_broker_for_sip($folio,$product_id){
      $this->db->select('cb.client_family_broker_id as broker_id,u.status,u.user_type');
      $this->db->from('client_bank_details as cb');
      $this->db->join('users as u','u.arn=cb.broker_code');
      $this->db->where('cb.folio_number',$folio);
      $this->db->where('cb.productID',$product_id);
      $query=$this->db->get();
    // echo $this->db->last_query();
      
      //below code added by Salmaan - 2017-11-01
      //$res=$query->result_array();
      if(!empty($query)) {
        return $res=$query->result_array();
      } else {
          return false;
      }

    }
    
    //pallavi 2017-07-17 //Manual intervension  for mail
    function get_all_folio_record($rta_type){
      $this->db->select('*');
      $this->db->from('clients_auto_import_error');
      $this->db->where('email_status','0');
      $this->db->where('rta_type',$rta_type);
      $query=$this->db->get();
      
      //below code added by Salmaan - 2017-11-01
      //$result=$query->result_array();
      if(!empty($query)) {
        return $result=$query->result_array();
      } else {
          return false;
      }
    }
    
    //Pallavi:@2017-06-23   //Manual intervention
    function add_auto_import_error_client($data1){
      //echo"<pre>";print_r($data1);
      $table='clients_auto_import_error';
      $columns    = array();
          $values     = array();
          $upd_values = array();
          foreach($data1 as $data){
            foreach ($data as $key => $val) {
                $columns[]    = $this->db->escape_identifiers($key);
                $val = $this->db->escape($val);
                $values[]     = $val;
                if($key!='client_id'){
                  $upd_values[] = $key.'='.$val;
                }

            }
            $sql = "INSERT INTO ". $this->db->dbprefix($table) ."(".implode(",", $columns).")values(".implode(', ', $values).")ON DUPLICATE KEY UPDATE ".implode(",", $upd_values);
             $this->db->query($sql);
             //echo $this->db->last_query();
              unset($columns,$values,$upd_values);
          }
        // echo"<pre>";print_r($columns);
        //   echo"<pre>";print_r($values);
        //     echo"<pre>";print_r($upd_values);exit;
        return true;
    }
    
}
