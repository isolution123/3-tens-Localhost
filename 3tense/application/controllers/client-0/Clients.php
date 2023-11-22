<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');
        $this->load->model('Families_model');
        $this->load->model('Clients_model');
        $this->load->model('Banks_model');
        $this->load->model('Bank_accounts_model');
        $this->load->model('Demat_accounts_model');
        $this->load->model('Demat_providers_model');
        $this->load->model('Tradings_model');
        $this->load->model('Insurance_model');
        $this->load->model('Insurance_companies_model');
        $this->load->model('Insurance_plans_model');
        $this->load->model('Common_model');

        if(empty($this->session->userdata('client_id')))
        {
          redirect('client/Clients_users');
        }
    }

    function index()
    {
        /* List of all clients

        //data to pass to header view like page title, css, js
        $header['title']='Client Master';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/common/notif');
        $this->load->view('broker/client/index');
        $this->load->view('broker/common/footer'); */

    }

    function list_all_files($path) {
        if(file_exists($path)) {
            $files = array();
            $i = 0;
            $fileinfos = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            );
            foreach($fileinfos as $pathname => $fileinfo) {
                if (!$fileinfo->isFile()) continue;
                //$files[$i][] = $fileinfo->getFilename();
                $type = str_replace('/'.$fileinfo->getFilename(),'', str_replace($path.'/','', str_replace('\\','/',$pathname)));
                $files[$i]['type'] = $type;
                $files[$i]['path'] = '<a target="_blank" title="Click to view/download" href="'.base_url(str_replace('\\','/',$pathname)).'">'.str_replace('\\','/',$pathname).'</a>';
                //$files[$i]['filename'] = $fileinfo->getFilename();
                $files[$i]['filename'] = '<a target="_blank" title="Click to view/download" href="'.base_url(str_replace('\\','/',$pathname)).'">'.$fileinfo->getFilename().'</a>';

                //add delete button for action
                $permissions=$this->session->userdata('permissions');

               if($permissions == "3")
               {
                $files[$i]['action'] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_document('."'".str_replace('\\','/',$pathname)."'".')">
                    <i class="fa fa-trash-o"></i></a>';
                }
                else {
                  $files[$i]['action'] = '<a class="btn btn-sm btn-danger disable_btn" href="javascript:void(0)" title="Delete">
                      <i class="fa fa-trash-o"></i></a>';
                }
                if($type == $fileinfo->getFilename()) { unset($files[$i]); }   /* if the names are same, then its a photo, so delete the array */

                $i++;
            }
        } else {
            // client folder does not exist OR path not found
            return 0;
        }
        return $files;
    }

    public function report()
    {
        if(!(isset($_GET['id'])) || (empty($_GET['id']))) {
            echo "<script type='text/javascript'>
                    bootbox.alert('No client ID to show report!');
                    window.top.close();  //close the current tab
                  </script>";
        } else {
            /** Error reporting */
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            //get broker logo
            $brokerID = $this->session->userdata('user_id');
            if((glob("uploads/brokers/".$brokerID."/*.png*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.png*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } elseif((glob("uploads/brokers/".$brokerID."/*.jpeg*"))) {
                $logo = basename(glob("uploads/brokers/".$brokerID."/*.jpeg*")[0]);
                $logo = $brokerID.'/'.$logo;
            } else {
                $logo = "";
            }

            $res=$this->Clients_model->get_clients_broker_dropdown("fam.broker_id='".$brokerID."' and c.client_id='".$_GET['id']."'");
            //var_dump($res);
            if(!is_array($res) || empty($res))
            {
                echo "<script type='text/javascript'>
                    bootbox.alert('No client ID to show report!');
                    window.top.close();  //close the current tab
                  </script>";
                  die();
            }


            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/tcpdf/tcpdf.php');

            // create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle('Client Report');
            $pdf->SetSubject('Client Report');
            $pdf->SetKeywords('client, report');

            $title = '';
            // set default header data
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, '');
            $pdf->SetHeaderData($logo, 40, $title, '');

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-5);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('sourcesanspro', '', 12);

            //get all client info
            $clientID = $_GET['id'];
            $info = $this->Clients_model->get_client_info($clientID);
            $bank_accounts = $this->Bank_accounts_model->get_client_bank_accounts('ba.client_id = "'.$clientID.'"');
            $demat_accounts = $this->Demat_accounts_model->get_client_demat_accounts($clientID);
            $policies = $this->Insurance_model->get_client_policies($clientID);
            $tradings = $this->Clients_model->get_client_tradings($clientID);
            $contacts = $this->Clients_model->get_client_contacts($clientID);
            $documents = $this->list_all_files('uploads/clients/'.$clientID);

            $html = ''; //set html to blank

            if($info)
            {
                if($info->dob) {
                    $dobTemp = DateTime::createFromFormat('Y-m-d',$info->dob); $dob = $dobTemp->format('d/m/Y');
                } else {
                    $dob = '';
                }

                $pdf->AddPage();
                // add client info to page
                $html .= '<style type="text/css">
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 10px; padding:2px; color:#4d4d4d; text-align:center; color:#4d4d4d; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 10px; padding:2px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse; }
                        .amount { text-align:left; padding:10px; text-indent: 5px; font-weight: bold; }
                        .noWrap { white-space: nowrap; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:14px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight:bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .normal2 {font-weight: normal; text-align:left;}
                        .bold2 {font-weight: bold; font-size:10px; text-align:left;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                    </style>

                <div class="title">Client Report</div>

                <table class="info" cols="30">
                    <tr>
                        <td colspan="3">
                            <p class="client-name">Name: </label>
                        </td>
                        <td colspan="7">
                            <p class="client-name">'.$info->name.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Pan No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->pan_no.'</span>
                        </td>
                        <td colspan="6">
                            <p class="client-name">Relation with HOF: </label>
                        </td>
                        <td colspan="4">
                            <p class="client-name">'.$info->relation_HOF.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p class="client-name">DOB: </label>
                        </td>
                        <td colspan="7">
                            <p class="client-name">'.$dob.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Passport No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->passport_no.'</span>
                        </td>
                        <td colspan="4">
                            <p class="client-name">Mobile No.: </label>
                        </td>
                        <td colspan="6">
                            <p class="client-name">'.$info->mobile.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p class="client-name">Address: </label>
                        </td>
                        <td colspan="11">
                            <p class="client-name">'.$info->add_flat.' '.$info->add_street.' '.$info->add_area.' '.$info->add_city.' '.$info->add_state.' - '.$info->add_pin.'</span>
                        </td>
                    </tr>
                </table>
                <br/><br/>
                <div style="border-top:1px solid black;"></div>';

                if($bank_accounts)
                {
                    $html .= '<table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="5">Bank Account Details</td>
                            </tr>
                            <tr nobr="true" class="head-row">
                                <th>Bank Name</th>
                                <th>Branch</th>
                                <th>IFSC</th>
                                <th>Account Number</th>
                                <th>Account Type</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($bank_accounts as $account) {
                        $html .= '<tr>
                                <td class="border normal">'.$account->bank_name.'</td>
                                <td class="border normal">'.$account->branch.'</td>
                                <td class="border normal">'.$account->IFSC.'</td>
                                <td class="border normal">'.$account->account_number.'</td>
                                <td class="border normal">'.$account->account_type_name.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($demat_accounts)
                {
                    $html .= '<br/><br/>
                    <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="4">Demat Account Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>DP Name</th>
                                <th>Type of Account</th>
                                <th>DP ID</th>
                                <th>Account Number</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($demat_accounts as $account) {
                        $html .= '<tr>
                                <td class="border normal">'.$account->demat_provider.'</td>
                                <td class="border normal">'.$account->type_of_account.'</td>
                                <td class="border normal">'.$account->demat_id.'</td>
                                <td class="border normal">'.$account->account_number.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($policies)
                {
                    /*$html .= '<br/><br/>
                    <h3>Policy Details</h3><br/>
                    <table border="1" cellpadding="4" style="text-align:center;">
                        <thead>
                            <tr class="head-row">
                                <th>Company Name</th>
                                <th>Plan Name</th>
                                <th>Policy No.</th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach($policies as $policy) {
                        $html .= '<tr>
                                <td>'.$policy->ins_comp_name.'</td>
                                <td>'.$policy->plan_name.'</td>
                                <td>'.$policy->policy_num.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';*/
                }

                if($tradings)
                {
                    $html .= '<br/><br/>
                    <table border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="3">Trading Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>Broker</th>
                                <th>Client Code</th>
                                <th>Balance</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($tradings as $trading) {
                        $html .= '<tr>
                                <td class="border normal">'.$trading->trading_broker_name.'</td>
                                <td class="border normal">'.$trading->client_code.'</td>
                                <td class="border normal">'.$trading->balance.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }

                if($documents)
                {
                    $html .= '<br/><br/>
                    <table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="1">Document Details</td>
                            </tr>
                            <tr class="head-row">
                                <th>Document</th>
                            </tr>
                        </tbody>
                        <tbody>';

                    foreach($documents as $document) {
                        $html .= '<tr>
                                <td class="border normal">'.$document['type'].' for '.$document['filename'].'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';
                }


                if($contacts)
                {
                    $html .= '<table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td>';

                    /*foreach($contacts as $contact) {
                        $html .= '<tr>
                                <td class="border normal">'.$contact->contact_category_name.'</td>
                                <td class="border normal">'.$contact->flat.'</td>
                                <td class="border normal">'.$contact->street.'</td>
                                <td class="border normal">'.$contact->area.'</td>
                                <td class="border normal">'.$contact->city.'</td>
                                <td class="border normal">'.$contact->state.'</td>
                                <td class="border normal">'.$contact->pin.'</td>
                                <td class="border normal">'.$contact->mobile.'</td>
                                <td class="border normal">'.$contact->telephone.'</td>
                                <td class="border normal">'.$contact->email_id.'</td>
                            </tr>';
                    }

                    $html .= '</tbody>
                    </table>';*/

                    $html .= '<br/><br/><br/>
                    <table nobr="true" border="0" cellpadding="4" style="text-align:center; border-width:0px;">
                        <tbody>
                            <tr nobr="true">
                                <td class="no-border client-name" colspan="8">Additional Contact Details</td>
                            </tr>
                        </tbody>
                    </table>';

                    foreach($contacts as $contact) {
                        $html .= '<table class="info" cols="30">
                            <tr>
                                <td colspan="3">
                                    <p class="bold2">Category: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->contact_category_name.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Mobile No.: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->mobile.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Telephone: </p>
                                </td>
                                <td colspan="4">
                                    <p class="normal2">'.$contact->telephone.'</p>
                                </td>
                                <td colspan="3">
                                    <p class="bold2">Email ID: </p>
                                </td>
                                <td colspan="6">
                                    <p class="normal2">'.$contact->email_id.'</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p class="bold2">Address: </p>
                                </td>
                                <td colspan="25">
                                    <p class="normal2">'.
                            $contact->flat.' '.$contact->street.' '.$contact->area.' '.
                            $contact->city.' '.$contact->state.' - '.$contact->pin.'</p>
                                </td>
                            </tr>
                        </table>
                        <br/>';
                    }
                    $html .= '</td>
                            </tr>
                        </tbody>
                    </table>';
                }
            }

            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();

            // Footer
            /* $html .= '<br/><br/><span style="text-align:center;">All Rights Reserved.
                        <a href=="http://freebzaar.com">freeBZaar.com</a></span>'; */

            //Close and output PDF document
            $pdf->Output('Flog Engagement Report - Admin.pdf', 'I');


            //============================================================+
            // END OF FILE
            //============================================================+
        }
    }


}
