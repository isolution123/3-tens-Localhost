<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
	function __construct()
    {
        parent :: __construct();
				$this->load->library('session');
				$this->load->library('Custom_exception');
				$this->load->library('Common_lib');
				$this->load->helper('url');
				$this->load->model('client/Dashboard_models', 'dash');
				if(empty($this->session->userdata('client_id')))
				{
					redirect('client/Clients_users');
				}
				$this->load->model('Mutual_funds_model', 'mf');
				$this->load->model('Families_model', 'family');
				$this->load->model('Clients_model', 'client');
				$this->load->model('Banks_model', 'bank');
				$this->load->model('Common_model', 'common');
				$this->load->model('Client_reminders_model', 'rem');
				$this->load->model('Insurance_model', 'ins');
        $this->load->model('Premium_types_model', 'prem_type');
        $this->load->model('Insurance_plans_model', 'ins_plans');
        $this->load->model('Insurance_companies_model', 'ins_comp');
        $this->load->model('Advisers_model', 'adv');
				$this->load->model('Fixed_deposits_model', 'fd');
				$this->load->model('Fd_investment_types_model', 'inv');
				$this->load->model('Fd_companies_model', 'comp');
				$this->load->model('Advisers_model', 'adv');
				$this->load->model('Assets_liabilities_model','al');
        $this->load->model('Al_types_model', 'type');
        $this->load->model('Al_companies_model', 'comp');
        $this->load->model('Al_schemes_model', 'sch');
        $this->load->model('Al_products_model', 'pro');
    }
	public	function index()
	{
					$header['title']='Dashboard';
					$this->get_reminder_notify();
					$header['js'] = array(
							'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
							'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
							'assets/users/plugins/form-parsley/parsley.min.js',
							'assets/users/plugins/charts-morrisjs/morris.min.js',
							'assets/users/plugins/charts-morrisjs/raphael.min.js',
							'assets/users/js/common.js',
							'assets/users/plugins/bootbox/bootbox.min.js',
							'assets/users/plugins/charts-flot/jquery.flot.min.js',
							'assets/users/plugins/charts-flot/jquery.flot.resize.min.js',
							'assets/users/plugins/charts-flot/jquery.flot.orderBars.min.js',
							'assets/users/plugins/datatables/js/jquery.dataTables.min.js',

							//'assets/users/plugins/charts-chartjs/Chart.min.js',
							//'assets/users/demo/demo-chartjs.js',
					);
					$header['css'] = array(
							'assets/users/plugins/form-daterangepicker/daterangepicker-bs3.css',
							'assets/users/plugins/charts-morrisjs/morris.css',
							'assets/users/plugins/datatables/css/jquery.dataTables.min.css',

					);
					
					if($this->session->userdata('type')=='head')
					{
									$client_id = $this->session->userdata('client_id');
									$family_id= $this->session->userdata('family_id');
									$brokerID= $this->session->userdata('user_id');
									
							
								
									
							          $data['dash_data']=$this->dash->get_summary_Dashboard_HOF($family_id,$brokerID,$client_id);
									$data['client_list']=$this->dash->getTotalPortFolioModel($family_id,$brokerID);
									$header['cl_list']=$this->dash->getTotalPortFolioModel($family_id,$brokerID);
									$data['mf_list_pur']=$this->ajax_list_purchase();
									$data['mf_list_redm']=$this->ajax_list_redemption();
									$data['ins_new_list']=$this->ajax_ins_list_new();
									$data['ins_mat_list']=$this->ajax_ins_list_mat();
									$data['fd_new_list']=$this->ajax_list_top_new();
									$data['fd_mat_list']=$this->ajax_list_top_int();
									$data['al_new_list']=$this->asset_ajax_list_top();
									$data['al_mat_list']=$this->asset_ajax_list_mat();

								//	$data['mf_pur']=$this->cmf->get_mutual_funds_purchase_red_tab("mf.broker_id='".$brokerID."' and mf.mutual_fund_type IN('PIP','NFO','IPO','TIN') and c.family_id='".$family_id."'");
									//var_dump($data['mf_pur']);
								//$data['mf_pur']=$this->mf->getTotalPortFolioModel($family_id,$brokerID);
					 }
					 else
					 {
						//Client login

						$data['mf_list_pur']=$this->ajax_list_purchase();
						$data['mf_list_redm']=$this->ajax_list_redemption();
						$data['ins_new_list']=$this->ajax_ins_list_new();
						$data['ins_mat_list']=$this->ajax_ins_list_mat();
						$data['fd_new_list']=$this->ajax_list_top_new();
						$data['fd_mat_list']=$this->ajax_list_top_int();
						$data['al_new_list']=$this->asset_ajax_list_top();
						$data['al_mat_list']=$this->asset_ajax_list_mat();
						$client_id = $this->session->userdata('client_id');
						$brokerID = $this->session->userdata('user_id');
					  $data['dash_data']= $this->dash->get_summary_Dashboard_client($client_id,$brokerID);
					  //var_dump($data['dash_data']);

						}
						$data1=array();
						$list=$this->common->get_nfo_detail($brokerID);
						
						foreach($list as $asset)
                		{
                			$row = array();
                		    $row['nfo_description'] = $asset->nfo_description;
                		 $row['desc_color'] = $asset->desc_color;
                		 $row['nfo_image_path'] = $asset->nfo_image_path;
                		 $data1[] = $row;
                		}
						
                	
						$data['nfo_detail']=$data1;
						
				$this->load->view('client/common/header',$header);
				$this->load->view('client/dashboard',$data);

				$this->load->view('client/common/footer');
				unset($data['dash_data']);
				unset($data['mf_list_pur']);
				unset($data['mf_list_redm']);
				unset($data['ins_new_list']);
				unset($data['ins_mat_list']);
				unset($data['fd_new_list']);
				unset($data['fd_mat_list']);
				unset($data['al_new_list']);
				unset($data['al_mat_list']);
	  }
				public	function get_reminder_notify()
					{
							$client_id = $this->session->userdata('client_id');
							//$userName = $this->session->userdata('username');
							$date2 = new DateTime(date('d-m-Y'));
							$date2->modify('-15 day');
							//var_dump( $date2->format('d-m-Y'));
							$date = new DateTime(date('d-m-Y'));
							$date->modify('+15 day');
							//var_dump( $date->format('d-m-Y'));
							$condition = 'reminder_date between "'.$date2->format('Y-m-d').'" and "'.$date->format('Y-m-d').'" AND reminder_type != "Client"	AND client_id = "'.$client_id.'" AND client_view = 0' ;
							//var_dump($condition);
							$rem_data = $this->rem->dash_reminder_list($condition, 50);
							$count_rem = count($rem_data);
							$header_data['reminder'] = $rem_data;
							$header_data['count_reminder'] = $count_rem;
							$this->session->set_userdata('header', $header_data);
					}

				public	function update_notif()
				{
							$count_reminder = 0;
							$reminder = null;
							$this->get_reminder_notify();
							if(isset($this->session->userdata['header']))
							{
											$count_reminder = 0;
											$reminder = null;
											if(isset($this->session->userdata['header']))
											{
													$count_reminder = $this->session->userdata['header']['count_reminder'];
													$reminder = $this->session->userdata['header']['reminder'];
											}
											$html = '
													<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown" ><i class="fa fa-bell" style="padding-top:7px; font-size:15px;"></i>';
										!empty($count_reminder)?$html .= '<span class="badge bg-green">'.$count_reminder.'</span>':$html .= '';
										$html .='</a>
										<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
											<div class="scrollthis" >
											<li >';
																		if($count_reminder>0){
																			foreach($reminder as $rem) {
																					$html .= '<li>
																							<!--<span class="time">4 mins</span>-->';
																							
																					if($rem->reminder_type == 'Notification') {
																					    	$html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
																									<i class="fa fa-eye"></i>
																									<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																									<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																									<br/>
																									<span class="msg">'.$rem->title.'</span>';
																								$html .='<span class="msg">'.$rem->reminder_message.'</span>';
																								if(!empty($rem->attachment_url))
																								{
																								    $html .='<span class="msg">
																								    <img src="'.$rem->attachment_url.'" style="height: 100%;width: 100%;"></span>';
																								}
																							
																									
																					$html .='		</a>';
																					}
																					else if($rem->reminder_type == 'Personal') {
																							$html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
																									<i class="fa fa-eye"></i>
																									<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																									<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																									<br/>
																									<span class="msg">'.$rem->reminder_message.'</span>
																							</a>';
																					}
																					elseif($rem->reminder_type == 'Client') {
																								$html .= '<a href="javascript:void(0)" onclick="Client_view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
																										<i class="fa fa-eye"></i>
																										<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																										<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																										<br/>
																										<span class="msg">'.$rem->reminder_message.'</span>
																								</a>';
																						}
																					 elseif($rem->reminder_type == 'Premium Due' || $rem->reminder_type == 'Grace Date' || $rem->reminder_type == 'Insurance Maturity') {
																							$html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-warning">
																									<i class="fa fa-eye"></i>
																									<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																									<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																									<br/>
																									<span class="msg">'.$rem->reminder_message.'</span>
																							</a>';
																					} elseif($rem->reminder_type == 'Birthday Reminder' || $rem->reminder_type == 'Anniversary Reminder') {
																							$html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-success active">
																									<i class="fa fa-gift"></i>
																									<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																									<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																									<br/>
																									<span class="msg">'.$rem->reminder_message.'</span>
																							</a>';
																					} else {
																							$html .= '<a href="javascript:void(0)" onclick="view_reminder('."'".$rem->reminder_id."'".')" class="notification-danger">
																									<i class="fa fa-crosshairs"></i>
																									<span style="font-weight: bold; padding-left: 10px">'.$rem->reminder_type.'</span> -
																									<span style="font-size: smaller; font-style: italic;">'.$rem->client_name.'</span>
																									<br/>
																									<span class="msg">'.$rem->reminder_message.'</span>
																							</a>';
																					}
																					$html .= '</li>';
																			}}

																			else{
																				$html .= '<a href="javascript:void(0)"  class="notification-danger">
																						<i class="fa fa-crosshairs"></i>
																						<span style="font-weight: bold; padding-left: 10px">No Reminder</span>
																				</a>';
																			}
																			$html .= '</div>
																						</ul>';
																}
							 else {
									$html = '<a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i></a>
									<ul class="dropdown-menu notifications arrow">
											<li class="dd-header">
													<div style="float: left;">
															<button type="button" id="addRemBtn" onclick="add_reminder_dialog()" class="btn-xs btn-success"><i class="fa fa-plus"></i> Add New Reminder</button>
													</div>
													<span>You have <b>0</b> Personal reminder(s)</span>
											</li>
											<div class="scrollthis">Nothing to show...</div>';
							}

							echo $html;
					}

		public function getTotalPortFolio()
		{
			// if($this->session->userdata('type')=='head')
			// {
			// 			$family_id= $this->session->userdata('family_id');
			// 			$brokerID= $this->session->userdata('user_id');
			//		//$data=$this->dash->getTotalPortFolioModel($family_id,$brokerID);
			// 			//	print_r($data);
			// 			//echo json_encode($data);
			//  }
		}

	public function get_summary()
	{
				if($this->session->userdata('type')=='head')
				{
					// HOF login
							$client_id = $this->session->userdata('client_id');
							$family_id= $this->session->userdata('family_id');
							$brokerID= $this->session->userdata('user_id');
							$data=$this->dash->get_summary_Dashboard_HOF($family_id,$brokerID,$client_id);
							//print_r($data);
							return result_array($data);
							//echo json_encode($data);
					  	// $data=$this->dash->get_summary_Dashboard_HOF($family_id);
							// echo json_encode($data);
				 }
				 else
				 {
					//Client login
					  $client_id = $this->session->userdata('client_id');
						$brokerID = $this->session->userdata('user_id');
						$data = $this->dash->get_summary_Dashboard_client($client_id,$brokerID);
						echo json_encode($data);
					  // $data=$this->dash->get_summary_Dashboard_client($client_id);
						// echo json_encode($data);
				}
	}

//gets all mutual fund Purchase from database  --- for dashboard
public function ajax_list_purchase()
{
					if($this->session->userdata('type')=='head')
					{

								$family_id= $this->session->userdata('family_id');
								$brokerID= $this->session->userdata('user_id');
						//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
						//$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type='PIP'");
						$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type IN('PIP','NFO','IPO','TIN') and c.family_id='".$family_id."'");
							//print_r($list);
						$num = 10;
						if(isset ($_POST['start']))
								$num = $_POST['start'];
						$data = array();
						foreach($list as $mf)
						{
								$row = array();
								$num++;
								$row['family_name'] = $mf->family_name;
								$row['client_name'] = $mf->client_name;
								$row['scheme_name'] = $mf->scheme_name;
								$row['mutual_fund_type'] = $mf->mutual_fund_type;
								$row['transaction_type'] = $mf->transaction_type;
								$row['folio_number'] = $mf->folio_number;
								$row['purchase_date'] = $mf->purchase_date;
								$row['quantity'] = $mf->quantity;
								$row['nav'] = $mf->nav;
								$row['amount'] = round($mf->amount);
								$row['adjustment'] = $mf->adjustment;
								$row['adjustment_ref_number'] = $mf->adjustment_ref_number;
								$permissions=$this->session->userdata('permissions');
								if($permissions=="3")
								{
										$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
										onclick="edit_mf('."'".$mf->transaction_id."'".')">
										<i class="fa fa-pencil"></i></a>
										<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
										onclick="delete_mf('."'".$mf->transaction_id."'".')">
										<i class="fa fa-trash-o"></i></a>';
								}
								else if($permissions="2" || $permissions="1")
								{
										$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
										onclick="edit_mf('."'".$mf->transaction_id."'".')">
										<i class="fa fa-pencil"></i></a>
										a class="btn btn-sm btn-danger disable_btn">
										<i class="fa fa-trash-o"></i></a>';
								}


								$data[] = $row;
						}
						$output = array(
								"draw"=>1,
								//"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
								//"recordsFiltered"=>$this->mf->count_filtered(),
								"data"=>$data
						);
						//output to json format
						 return $output;
					}
					else
					{
						$client_id= $this->session->userdata('client_id');
						$brokerID= $this->session->userdata('user_id');
				//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
				//$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type='PIP'");
				$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type IN('PIP','NFO','IPO','TIN') and mf.client_id='".$client_id."'");

				$num = 10;
				if(isset ($_POST['start']))
						$num = $_POST['start'];
				$data = array();
				foreach($list as $mf)
				{
						$row = array();
						$num++;
						$row['family_name'] = $mf->family_name;
						$row['client_name'] = $mf->client_name;
						$row['scheme_name'] = $mf->scheme_name;
						$row['mutual_fund_type'] = $mf->mutual_fund_type;
						$row['transaction_type'] = $mf->transaction_type;
						$row['folio_number'] = $mf->folio_number;
						$row['purchase_date'] = $mf->purchase_date;
						$row['quantity'] = $mf->quantity;
						$row['nav'] = $mf->nav;
						$row['amount'] = round($mf->amount);
						$row['adjustment'] = $mf->adjustment;
						$row['adjustment_ref_number'] = $mf->adjustment_ref_number;
						$permissions=$this->session->userdata('permissions');
						if($permissions=="3")
						{
								$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								onclick="edit_mf('."'".$mf->transaction_id."'".')">
								<i class="fa fa-pencil"></i></a>
								<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
								onclick="delete_mf('."'".$mf->transaction_id."'".')">
								<i class="fa fa-trash-o"></i></a>';
						}
						else if($permissions="2" || $permissions="1")
						{
								$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								onclick="edit_mf('."'".$mf->transaction_id."'".')">
								<i class="fa fa-pencil"></i></a>
								a class="btn btn-sm btn-danger disable_btn">
								<i class="fa fa-trash-o"></i></a>';
						}


						$data[] = $row;
				}
				$output = array(
						"draw"=>1,
						//"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
						//"recordsFiltered"=>$this->mf->count_filtered(),
						"data"=>$data
				);
				//output to json format
				 return $output;

					}
}

//gets all mutual fund redemption from database  --- for dashboard
public function ajax_list_redemption()
{
	if($this->session->userdata('type')=='head')
	{

				$family_id= $this->session->userdata('family_id');
				$brokerID= $this->session->userdata('user_id');
		//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
		$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type in('RED','DP') and c.family_id='".$family_id."'");
		//print_r($list);
		$num = 10;
		if(isset ($_POST['start']))
				$num = $_POST['start'];
		$data = array();
		foreach($list as $mf)
		{
				$row = array();
				$num++;
				$row['family_name'] = $mf->family_name;
				$row['client_name'] = $mf->client_name;
				$row['scheme_name'] = $mf->scheme_name;
				$row['mutual_fund_type'] = $mf->mutual_fund_type;
				$row['transaction_type'] = $mf->transaction_type;
				$row['folio_number'] = $mf->folio_number;
				$row['purchase_date'] = $mf->purchase_date;
				$row['quantity'] = $mf->quantity;
				$row['nav'] = $mf->nav;
				$row['amount'] = round($mf->amount);
				$row['adjustment'] = $mf->adjustment;
				$row['adjustment_ref_number'] = $mf->adjustment_ref_number;
				$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
						onclick="edit_mf('."'".$mf->transaction_id."'".')">
						<i class="fa fa-pencil"></i></a>
						<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
						onclick="delete_mf('."'".$mf->transaction_id."'".')">
						<i class="fa fa-trash-o"></i></a>';

				$data[] = $row;
		}
		$output = array(
				"draw"=>1,
				//"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
				//"recordsFiltered"=>$this->mf->count_filtered(),
				"data"=>$data
		);
		//output to json format
		 return $output;
	}
	else
	{
					$client_id= $this->session->userdata('client_id');
					$brokerID= $this->session->userdata('user_id');
			//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
			$list = $this->mf->get_mutual_funds_purchase_red("mf.broker_id='".$brokerID."' and mf.mutual_fund_type in('RED','DP') and mf.client_id='".$client_id."'");
			//print_r($list);
			$num = 10;
			if(isset ($_POST['start']))
					$num = $_POST['start'];
			$data = array();
			foreach($list as $mf)
			{
					$row = array();
					$num++;
					$row['family_name'] = $mf->family_name;
					$row['client_name'] = $mf->client_name;
					$row['scheme_name'] = $mf->scheme_name;
					$row['mutual_fund_type'] = $mf->mutual_fund_type;
					$row['transaction_type'] = $mf->transaction_type;
					$row['folio_number'] = $mf->folio_number;
					$row['purchase_date'] = $mf->purchase_date;
					$row['quantity'] = $mf->quantity;
					$row['nav'] = $mf->nav;
					$row['amount'] = round($mf->amount);
					$row['adjustment'] = $mf->adjustment;
					$row['adjustment_ref_number'] = $mf->adjustment_ref_number;
					$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
							onclick="edit_mf('."'".$mf->transaction_id."'".')">
							<i class="fa fa-pencil"></i></a>
							<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
							onclick="delete_mf('."'".$mf->transaction_id."'".')">
							<i class="fa fa-trash-o"></i></a>';

					$data[] = $row;
			}
			$output = array(
					"draw"=>1,
					//"recordsTotal"=>$this->mf->count_all(array('mf.broker_id' => $brokerID)),
					//"recordsFiltered"=>$this->mf->count_filtered(),
					"data"=>$data
			);
			//output to json format
			 return $output;
	}
}


//get top 5 new insurance premium due
public function ajax_ins_list_new()
{
								if($this->session->userdata('type')=='head')
								{
											$family_id= $this->session->userdata('family_id');
											$brokerID= $this->session->userdata('user_id');
											
									//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
									$list = $this->ins->get_new_top_new_client("insurances.broker_id='".$brokerID."' and insurances.next_prem_due_date >=curdate() and insurances.status in(1,2,3,4) and cli.family_id='".$family_id."'");
									//echo $list;
									$data = array();

									foreach($list as $ins1)
									{
											$row = array();
											//$num++;
											$source = $ins1->next_prem_due_date;
											$date = new DateTime($source);
										 	//$date->format('d.m.Y'); // 31.07.2012
										 // 31-07-2012
											$row['client_name'] = $ins1->name;
											$row['next_prem_due_date'] =$date->format('d/m/Y'); //date("d/m/Y", date($ins1->next_prem_due_date));
											$row['plan_name'] = $ins1->plan_name;
											$row['policy_num'] = $ins1->policy_num;
											$row['prem_amt'] = round($ins1->prem_amt);
											$permissions=$this->session->userdata('permissions');
												if($permissions=="3")
												{
											$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
													onclick="edit_mf('."'".$ins1->policy_num."'".')">
													<i class="fa fa-pencil"></i></a>
													<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
													onclick="delete_mf('."'".$ins1->policy_num."'".')">
													<i class="fa fa-trash-o"></i></a>';
												}
												else if($permissions=="2")
												{
													$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
															onclick="edit_mf('."'".$ins1->policy_num."'".')">
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
											"data"=>$data
									);
									//output to json format
									return $output;
								}
					else
					{
												$client_id= $this->session->userdata('client_id');
												$brokerID= $this->session->userdata('user_id');
										//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
										$list = $this->ins->get_new_top_new_client("insurances.broker_id='".$brokerID."' and insurances.next_prem_due_date >= curdate() and insurances.status in(1,2,3,4) and insurances.client_id='".$client_id."'");
										//echo $list;
										//print_r($list);
										$data = array();

										foreach($list as $ins1)
										{
												$row = array();
												$source = $ins1->next_prem_due_date;
												$date = new DateTime($source);
												//$num++;
											  $row['client_name'] = $ins1->name;
												$row['next_prem_due_date'] =$date->format('d/m/Y'); //$ins1->next_prem_due_date;
												$row['plan_name'] = $ins1->plan_name;
												$row['policy_num'] = $ins1->policy_num;
												$row['prem_amt'] = round($ins1->prem_amt);
												$permissions=$this->session->userdata('permissions');
													if($permissions=="3")
													{
												$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
														onclick="edit_mf('."'".$ins1->policy_num."'".')">
														<i class="fa fa-pencil"></i></a>
														<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
														onclick="delete_mf('."'".$ins1->policy_num."'".')">
														<i class="fa fa-trash-o"></i></a>';
													}
													else if($permissions=="2")
													{
														$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
																onclick="edit_mf('."'".$ins1->policy_num."'".')">
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
												"data"=>$data
										);
										//output to json format
										return $output;

					}
}

//get top 5  new insurance maturity
public function ajax_ins_list_mat()
{
	if($this->session->userdata('type')=='head')
	{
				$family_id= $this->session->userdata('family_id');
				$brokerID= $this->session->userdata('user_id');

		//$list = $this->mf->get_mutual_funds_purchase(array('mf.broker_id' => $brokerID, 'mf.mutual_fund_type'=>'PIP'));
		//$list = $this->ins->get_new_top_mat("insurances.broker_id='".$brokerID."' and insurances.policy_num in (select pm.policy_num from premium_maturities pm where pm.maturity_date>CURRENT_DATE()) ");
		$list = $this->ins->get_new_top_mat_client("insurances.broker_id='".$brokerID."' and premium_maturities.maturity_date>=CURRENT_DATE() and insurances.status in(1,2,3,4) and clients.family_id='".$family_id."'");
		//print_r($list);
		$data = array();
		foreach($list as $ins1)
		{
				$row = array();
				$source = $ins1->maturity_date;
				$date = new DateTime($source);
				//$num++;
				$row['client_name'] = $ins1->name;
				$row['maturity_date'] = $date->format('d/m/Y');//$ins1->maturity_date;
				$row['plan_name'] = $ins1->plan_name;
				$row['policy_num'] = $ins1->policy_num;
				$row['amount'] = round($ins1->amount);
				$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
						onclick="edit_mf('."'".$ins1->policy_num."'".')">
						<i class="fa fa-pencil"></i></a>
						<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
						onclick="delete_mf('."'".$ins1->policy_num."'".')">
						<i class="fa fa-trash-o"></i></a>';

				$data[] = $row;
		}
		$output = array(
				"data"=>$data
		);
		//output to json format
		return $output;
	}
	else
	{
	$client_id= $this->session->userdata('client_id');
	$brokerID= $this->session->userdata('user_id');
	$list = $this->ins->get_new_top_mat_client("insurances.broker_id='".$brokerID."' and premium_maturities.maturity_date>=CURRENT_DATE() and insurances.status in(1,2,3,4) and insurances.client_id='".$client_id."'");
	//print_r($list);
	//print_r($list);
	$data = array();
	foreach($list as $ins1)
	{
			$row = array();
			$source = $ins1->maturity_date;
			$date = new DateTime($source);

			//$num++;
			$row['client_name'] = $ins1->name;
			$row['maturity_date'] =  $date->format('d/m/Y');//$ins1->maturity_date;
			$row['plan_name'] = $ins1->plan_name;
			$row['policy_num'] = $ins1->policy_num;
			$row['amount'] = round($ins1->amount);
			$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
					onclick="edit_mf('."'".$ins1->policy_num."'".')">
					<i class="fa fa-pencil"></i></a>
					<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
					onclick="delete_mf('."'".$ins1->policy_num."'".')">
					<i class="fa fa-trash-o"></i></a>';

			$data[] = $row;
	}
	$output = array(
			"data"=>$data
	);
	//output to json format
	return $output;

		}
}

//gets all fixed deposit maturity details from database
public function ajax_list_top_new()
{
						if($this->session->userdata('type')=='head')
						{
							$family_id= $this->session->userdata('family_id');
							$brokerID= $this->session->userdata('user_id');
						 $list = $this->fd->get_fixed_deposit_mat("fdt.broker_id ='".$brokerID."' and fdt.maturity_date>=curdate() and c.family_id='". $family_id."' ");
						 //$list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID));
						// print_r($list);

						 $num = 10;
						 if(isset ($_POST['start']))
								 $num = $_POST['start'];
						 $data = array();
						 //print_r($list);
						 foreach($list as $fd)
						 {
								 $row = array();
								 $num++;
								 $row['family_name'] = $fd->family_name;
								 $row['client_name'] = $fd->client_name;
								 $row['transaction_date'] = $fd->transaction_date;
								 $row['fd_inv_type'] = $fd->fd_inv_type;
								 $row['fd_comp_name'] = $fd->fd_comp_name;
								 $row['fd_method'] = $fd->fd_method;
								 $row['ref_number'] = $fd->ref_number;
								 $row['issued_date'] = $fd->issued_date;
								 $row['amount_invested'] = round($fd->amount_invested);
								 $row['interest_rate'] = $fd->interest_rate.'%';
								 $row['maturity_date'] = $fd->maturity_date;
								 $row['maturity_amount'] = round($fd->maturity_amount);
								 $row['nominee'] = $fd->nominee;
								 $row['status'] = $fd->status;
								 $row['adviser_name'] = $fd->adviser_name;
								 $row['adjustment'] = $fd->adjustment;

								 $permissions=$this->session->userdata('permissions');
									 if($permissions=="3")
									 {
										 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
										 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
										 <i class="fa fa-pencil"></i></a>
										 <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
										 onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
										 <i class="fa fa-trash-o"></i></a>';
									 }
									 else if($permissions="2")
									 {
										 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
										 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
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
								 //"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
								 //"recordsFiltered"=>$this->fd->count_filtered(),
								 "data"=>$data
						 );
						 //output to json format
						 return $output;
					 }
					 else
					 {
						 $client_id= $this->session->userdata('client_id');
						 $brokerID= $this->session->userdata('user_id');
					$list = $this->fd->get_fixed_deposit_mat("fdt.broker_id ='".$brokerID."' and fdt.maturity_date>=curdate() and  fdt.client_id='". $client_id."' ");
					//$list = $this->fd->get_fixed_deposit_top(array('fdt.broker_id' => $brokerID));
					//print_r($list);

					$num = 10;
					if(isset ($_POST['start']))
						$num = $_POST['start'];
					$data = array();
					foreach($list as $fd)
					{
						$row = array();
						$num++;
						$row['family_name'] = $fd->family_name;
						$row['client_name'] = $fd->client_name;
						$row['transaction_date'] = $fd->transaction_date;
						$row['fd_inv_type'] = $fd->fd_inv_type;
						$row['fd_comp_name'] = $fd->fd_comp_name;
						$row['fd_method'] = $fd->fd_method;
						$row['ref_number'] = $fd->ref_number;
						$row['issued_date'] = $fd->issued_date;
						$row['amount_invested'] = round($fd->amount_invested);
						$row['interest_rate'] = $fd->interest_rate.'%';
						$row['maturity_date'] = $fd->maturity_date;
						$row['maturity_amount'] = round($fd->maturity_amount);
						$row['nominee'] = $fd->nominee;
						$row['status'] = $fd->status;
						$row['adviser_name'] = $fd->adviser_name;
						$row['adjustment'] = $fd->adjustment;

						$permissions=$this->session->userdata('permissions');
							if($permissions=="3")
							{
								$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
								<i class="fa fa-pencil"></i></a>
								<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
								onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
								<i class="fa fa-trash-o"></i></a>';
							}
							else if($permissions="2")
							{
								$row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
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
						//"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
						//"recordsFiltered"=>$this->fd->count_filtered(),
						"data"=>$data
					);
					//output to json format
					return $output;
					 }
}


//gets top 5 interest  fixed deposit details from database
public function ajax_list_top_int()
{
 					if($this->session->userdata('type')=='head')
						{
						 $family_id= $this->session->userdata('family_id');
						 $brokerID= $this->session->userdata('user_id');
						 $list = $this->fd->get_fixed_deposit_int("fdt.user_id = '".$brokerID."' and fdi.interest_date >= curdate() and fdt.fd_method ='Non-Cumulative' and c.family_id ='". $family_id."'");
           	 //print_r($list);
						 $num = 10;
						 if(isset ($_POST['start']))
								 $num = $_POST['start'];
						 $data = array();
						 foreach($list as $fd)
						 {
								 $row = array();
								 $source = $fd->interest_date;
								 $date = new DateTime($source);

								 $num++;
								 $row['client_name'] = $fd->name;
								 $row['fd_comp_name'] = $fd->fd_comp_name;
								 $row['ref_number'] = $fd->ref_number;
								 $row['interest_date'] =  $date->format('d/m/Y');//$fd->interest_date;
								 $row['interest_amount'] = round($fd->interest_amount);

								//  $permissions=$this->session->userdata('permissions');
								// 	 if($permissions=="3")
								// 	 {
								// 		 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								// 		 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
								// 		 <i class="fa fa-pencil"></i></a>
								// 		 <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
								// 		 onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
								// 		 <i class="fa fa-trash-o"></i></a>';
								// 	 }
								// 	 else if($permissions=="2")
								// 	 {
								// 		 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
								// 		 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
								// 		 <i class="fa fa-pencil"></i></a>
								// 		 <a class="btn btn-sm btn-danger disable_btn">
								// 		 <i class="fa fa-trash-o"></i></a>';
								// 	 }
								// 	 else if($permissions=="1")
								// 	 {
								// 									 $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
								// 									 <i class="fa fa-pencil"></i></a>
								// 									 <a class="btn btn-sm btn-danger disable_btn">
								// 									 <i class="fa fa-trash-o"></i></a>';
								// 	 }
								 $data[] = $row;
						 }
						 $output = array(
								 "draw"=>1,
								 //"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
								 //"recordsFiltered"=>$this->fd->count_filtered(),
								 "data"=>$data
						 );
						 //output to json format
						 return $output;
					}
					else
					{
						$client_id= $this->session->userdata('client_id');
						$brokerID= $this->session->userdata('user_id');
					  $list = $this->fd->get_fixed_deposit_int("fdt.user_id = '".$brokerID."' and fdi.interest_date >= curdate() and fdt.fd_method ='Non-Cumulative' and fdt.client_id ='". $client_id."'");
 					//print_r($list);
					$num = 10;
					if(isset ($_POST['start']))
					 $num = $_POST['start'];
					$data = array();
					foreach($list as $fd)
					{
					 $row = array();
					 $source = $fd->interest_date;
					 $date = new DateTime($source);
					 $num++;
					 $row['client_name'] = $fd->name;
					 $row['fd_comp_name'] = $fd->fd_comp_name;
					 $row['ref_number'] = $fd->ref_number;
					 $row['interest_date'] =$date->format('d/m/Y');// $fd->interest_date;
					 $row['interest_amount'] = round($fd->interest_amount);

					//  $permissions=$this->session->userdata('permissions');
					// 	 if($permissions=="3")
					// 	 {
					// 		 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
					// 		 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
					// 		 <i class="fa fa-pencil"></i></a>
					// 		 <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
					// 		 onclick="delete_fd('."'".$fd->fd_transaction_id."'".')">
					// 		 <i class="fa fa-trash-o"></i></a>';
					// 	 }
					// 	 else if($permissions=="2")
					// 	 {
					// 		 $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
					// 		 onclick="edit_fd('."'".$fd->fd_transaction_id."'".')">
					// 		 <i class="fa fa-pencil"></i></a>
					// 		 <a class="btn btn-sm btn-danger disable_btn">
					// 		 <i class="fa fa-trash-o"></i></a>';
					// 	 }
					// 	 else if($permissions=="1")
					// 	 {
					// 									 $row['action'] = '<a class="btn btn-sm btn-primary  disable_btn">
					// 									 <i class="fa fa-pencil"></i></a>
					// 									 <a class="btn btn-sm btn-danger disable_btn">
					// 									 <i class="fa fa-trash-o"></i></a>';
					// 	 }
					 $data[] = $row;
					}
					$output = array(
					 "draw"=>1,
					 //"recordsTotal"=>$this->fd->count_all(array('fdt.broker_id' => $brokerID)),
					 //"recordsFiltered"=>$this->fd->count_filtered(),
					 "data"=>$data
					);

					return $output;
					}

}

//access top liabiltiy and asset records
 public function asset_ajax_list_top()
{
	if($this->session->userdata('type')=='head')
	{
				$family_id= $this->session->userdata('family_id');
				$brokerID= $this->session->userdata('user_id');
		$list = $this->al->get_asset_list_top_client("c.user_id='".$brokerID."' and c.family_id='".$family_id."' and maturity_date>= curdate()");
    //print_r($list);
		$num = 10;
		if(isset ($_POST['start']))
				$num = $_POST['start'];
		$data = array();
		foreach($list as $asset)
		{
			$row = array();
			$source =$asset->maturity_date;
			$date = new DateTime($source);
			$num++;
			$row['client_name'] = $asset->name;
			$row['product_name'] = $asset->product_name;
			$row['ref_number'] = $asset->ref_number;
			$row['maturity_date'] = $date->format('d/m/Y');//$asset->maturity_date;
			$row['maturity_amount'] = round($asset->maturity_amount);
			$data[] = $row;
		}
		$output = array(
				"draw"=>1,
				"data"=>$data
		);
		//output to json format
		 return $output;
	}
		else
		{
			$client_id= $this->session->userdata('client_id');
			$brokerID= $this->session->userdata('user_id');
	$list = $this->al->get_asset_list_top_client("c.user_id='".$brokerID."' and c.client_id='".$client_id."' and maturity_date>= curdate()");
  // print_r($list);
	$num = 10;
	if(isset ($_POST['start']))
			$num = $_POST['start'];
	$data = array();
	foreach($list as $asset)
	{
			$row = array();
			$source =$asset->maturity_date;
			$date = new DateTime($source);
			$num++;
			$row['client_name'] = $asset->name;
			$row['product_name'] = $asset->product_name;
			$row['ref_number'] = $asset->ref_number;
			$row['maturity_date'] = $date->format('d/m/Y');//$asset->maturity_date;
			$row['maturity_amount'] = round($asset->maturity_amount);
			$data[] = $row;
	}
	$output = array(
			"draw"=>1,
			"data"=>$data
	);
	//output to json format
 return $output;
		}
}

	//access top liabiltiy and asset records
 public function asset_ajax_list_mat()
{
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);
	if($this->session->userdata('type')=='head')
	{
				$family_id= $this->session->userdata('family_id');
				$brokerID= $this->session->userdata('user_id');
		$list = $this->al->get_asset_list_mat_client("c.user_id='".$brokerID."' and end_date>= curdate() and c.family_id='".$family_id."'");
		//print_r($list);
		$num = 10;
		if(isset ($_POST['start']))
				$num = $_POST['start'];
		$data = array();
		foreach($list as $asset)
		{
			$row = array();
			$source =$asset->end_date;
			$date = new DateTime($source);
		 $num++;
		 $row['client_name'] = $asset->name;
		 $row['product_name'] = $asset->product_name;
		 $row['ref_number'] = $asset->ref_number;
		 $row['end_date'] = $date->format('d/m/Y');//$asset->end_date;
		 $row['installment_amount'] = round($asset->installment_amount);
		 $data[] = $row;
		}
		$output = array(
				"draw"=>1,
				"data"=>$data
		);
		//output to json format
		return $output;
	}
	else
	 {

		 $client_id= $this->session->userdata('client_id');
		 $brokerID= $this->session->userdata('user_id');
 $list = $this->al->get_asset_list_mat_client("c.user_id='".$brokerID."' and end_date>= curdate() and c.client_id='".$client_id."'");
 //echo $list;
 $num = 10;
 if(isset ($_POST['start']))
		 $num = $_POST['start'];
 $data = array();
 foreach($list as $asset)
 {
		 $row = array();
		 $source =$asset->end_date;
		 $date = new DateTime($source);
		 $num++;
		 $row['client_name'] = $asset->name;
		 $row['product_name'] = $asset->product_name;
		 $row['ref_number'] = $asset->ref_number;
		 $row['end_date'] =  $date->format('d/m/Y');//$asset->end_date;
		 $row['installment_amount'] = round($asset->installment_amount);
		 $data[] = $row;
 }
 $output = array(
		 "draw"=>1,
		 "data"=>$data
 );
 //output to json format
 return $output;

	}
}

public function view()
{
				$this->load->view('client/view');
}


}
