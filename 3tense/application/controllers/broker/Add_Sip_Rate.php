<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Add_Sip_Rate extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        //load model Add_sip_model as addsip
        $this->load->model('Add_sip_model','addsip');

        //check if user is logged in by checking his/her session data
        //if user is not logged redirect to login
        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }
    }

    function index()
    {
        $brokerID = $this->session->userdata('broker_id');
        //data to pass to header view like page title, css, js
        $header['title']='Add SIP Rate Of Return';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css',
            'assets/users/plugins/pines-notify/jquery.pnotify.default.css',
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js',
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/form-select2/select2.min.js'
        );
        $condition = "broker_id =".trim($this->session->userdata('broker_id'));
          $data['rate'] = $this->addsip->get_broker_sip_rate($condition);
          
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/sip/add_sip_rate',$data);
        $this->load->view('broker/master/add_ins_comp');
        $this->load->view('broker/common/notif');
        $this->load->view('broker/common/footer');
    }

    //gets all Insurance Plans details from database
    public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $list = $this->plan->get_ins_plans_broker(array('ins_plans.broker_id' => $brokerID));

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $plan)
        {
            $num++;
            $row = array();
            $row['plan_id']=$plan->plan_id;
            $row['plan_name']=$plan->plan_name;
            $row['grace_period']=$plan->grace_period;
            $row['ins_companies']=$plan->ins_companies;
            $row['plan_type']=$plan->plan_type;
            $row['annual_cumm_one']=$plan->annual_cumm_one;
            $row['annual_cumm']=$plan->annual_cumm;
            $row['return_cumm']=$plan->return_cumm;

            //add html for action
            $permissions=$this->session->userdata('permissions');
              if($permissions=="3")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_ins_plan('."'".$plan->plan_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                onclick="delete_ins_plan('."'".$plan->plan_id."'".')">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="2")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                onclick="edit_ins_plan('."'".$plan->plan_id."'".')">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }
              else if($permissions=="1")
              {
                $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
                <i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger disable_btn">
                <i class="fa fa-trash-o"></i></a>';
              }


            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            "recordsTotal"=>$this->plan->count_all($brokerID),
            "recordsFiltered"=>$this->plan->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }

    //add Insurance Plans in database
    public function add_sip_rate()
    {
        
        
        
        if($this->input->post('debt') !=='')
        {
            $data = array(
                'scheme_type' =>'debt',  
                'rate'=>$this->input->post('debt'),
                'broker_id' => $this->session->userdata('broker_id')
            );
             
             $condition = "broker_id =".trim($this->session->userdata('broker_id'))." AND `scheme_type` ='debt'";
             $isDuplicate =  $this->addsip->check_duplicate('sip_rate',$condition);
              
            if($isDuplicate)
            {
                 $status=$this->addsip->update_sip_rate($data,$condition);
                 $text='Edited';
                 
            }
            else
            {
              $status = $this->addsip->add_sip_rate($data);     
               $text='Added';
            }

             
             
            
        }
        if($this->input->post('equity') !=='')
        {
            $data = array(
                'scheme_type' =>'equity',  
                'rate'=>$this->input->post('equity'),
                'broker_id' => $this->session->userdata('broker_id')
            );
            $condition = "broker_id =".trim($this->session->userdata('broker_id'))." AND `scheme_type` ='equity'";
            
            $isDuplicate =  $this->addsip->check_duplicate('sip_rate',$condition);
            if($isDuplicate)
            {
                 $status=$this->addsip->update_sip_rate($data,$condition);
                 $text='Edited';
                 
            }
            else
            {
              $status = $this->addsip->add_sip_rate($data);
              $text='Added';
              
            }
            

           
        }
        if($this->input->post('hybrid') !=='')
        {
            $data = array(
                'scheme_type' =>'hybrid',  
                'rate'=>$this->input->post('hybrid'),
                'broker_id' => $this->session->userdata('broker_id')
            );
            
            $condition = "broker_id =".trim($this->session->userdata('broker_id'))." AND `scheme_type` ='hybrid'";
             $isDuplicate =  $this->addsip->check_duplicate('sip_rate',$condition);
            if($isDuplicate)
            {
                 $status=$this->addsip->update_sip_rate($data,$condition);
                 $text='updated';
                 
            }
            else
            {
              $status = $this->addsip->add_sip_rate($data);
              $text='added';
            }
             
           
        }    
        
        if((isset($status['code'])) AND $status['code']!=='')
        {
            
        }
        else
        {
             $message = array(
                'type' => 'success',
                'title'=> 'SIP Rate Of Return updated!',
                'status' => 1,
                'text' => 'All values have been '.$text.' successfully'
            );
            
        }
              /*$condition = "`plan_name` = '".trim($this->input->post('insPlanRename'))."'
        AND `ins_comp_id` = '".trim($this->input->post('insComp'))."'
        AND (`broker_id` = '".$this->session->userdata('broker_id')."' OR `broker_id` IS NULL)";
        $isDuplicate =  $this->com->check_duplicate('plan_name', 'ins_plans', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->plan->add($data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'New Insurance Plan Added!',
                    'status' => 'new',
                    'text' => 'Insurance Plan "'.$this->input->post('insPlanRename').'" added successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while Adding', 'text' => $e->errorMessage($status['code']));
            }
        }
        else
        {
            $message = array(
                'type' => 'error',
                'title'=> 'Error on Adding Insurance Plan!',
                'status' => 0,
                'text' => 'Insurance Plan "'.$this->input->post('insPlanRename').'" for selected Company already exists'
            );
        }*/
       
        echo json_encode($message);
    }

    //edit insurance plans by their id
    function edit_ins_plan()
    {
        $data = $this->plan->get_ins_plan_by_condition(array('plan_id' => $this->input->post('id')));
        echo json_encode($data);
    }

    //update ins_plan
    function update_ins_plan()
    {
        $brokerID = $this->session->userdata('broker_id');
        $data = array(
            'plan_name' => $this->input->post('insPlanRename'),
            'grace_period' => $this->input->post('planGrace'),
            'ins_comp_id' => $this->input->post('insComp'),
            'plan_type_id' => $this->input->post('insPlanType'),
            '`annual_cumm_one`' => $this->input->post('insLoyal'),
            '`annual_cumm`' => $this->input->post('insBonus'),
            'return_cumm' => $this->input->post('insGrowth'),
            'user_id' => $this->session->userdata('user_id'),
            'policy_id' => $this->input->post('planName')
        );

        $condition = "`plan_name` = '".trim($this->input->post('insPlanRename'))."'
        AND `ins_comp_id` = '".trim($this->input->post('insComp'))."'
        AND `plan_id` != '".$this->input->post('insPlanID')."'
        AND (`broker_id` = '".$this->session->userdata('broker_id')."' OR `broker_id` IS NULL)";
        $isDuplicate =  $this->com->check_duplicate('plan_name', 'ins_plans', $condition);
        if($isDuplicate == null)
        {
            try
            {
                $status = $this->plan->update(array('plan_id' => $this->input->post('insPlanID'), 'broker_id' => $brokerID), $data);
                //if there is any error
                if(isset($status['code']))
                {
                    throw new Custom_exception();
                }
                $message = array(
                    'type' => 'success',
                    'title'=> 'Insurance Plan Updated!',
                    'status' => 'new',
                    'text' => 'Insurance Plan "'.$this->input->post('insPlanRename').'" updated successfully'
                );
            }
            catch(Custom_exception $e)
            {
                //display custom message
                $message = array("status" => 0, 'title' => 'Error while updating', 'text' => $e->errorMessage($status['code']));
            }
        }
        else
        {
            $message = array(
                'type' => 'error',
                'title'=> 'Error on Updating Insurance Plan!',
                'status' => 0,
                'text' => 'Insurance Plan "'.$this->input->post('insPlanRename').'" for selected Company already exists'
            );
        }
        echo json_encode($message);
    }

    function delete_ins_plan()
    {
        $brokerID = $this->session->userdata('broker_id');
        try
        {
            $status = $this->plan->delete(array('plan_id' => $this->input->post('id'), 'broker_id' => $brokerID));
            //if there is any error
            if(isset($status['code']))
            {
                throw new Custom_exception();
            }
            echo json_encode(array("status" => 1));
        }
        catch(Custom_exception $e)
        {
            //display custom message
            echo json_encode(array("status" => 0, 'title' => 'Error while deleting', 'text' => $e->errorMessage($status['code'])));
        }
    }
}
