<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Calculators extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        //load library, helper
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        if(empty($this->session->userdata('client_id')))
        {
          redirect('client/Clients_users');
        }
        $this->load->model('Broker_pdf_model', 'broker_pdf');
    }

    function index()
    {
        
        //data to pass to header view like page title, css, js
        $header['title']='Calculators';
        $header['css'] = array(
            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-parsley/parsley.min.js',
            'assets/users/plugins/bootbox/bootbox.min.js',
            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',
            'assets/users/js/dataTables.js'
        );
        //load views
        $this->load->view('client/common/header', $header);
        $this->load->view('client/FrmFinCalc');
        $this->load->view('client/common/notif');
        $this->load->view('client/common/footer');
    }
       
         public function pdf_calculatorPV(){            
                $MonthSIPPV =$this->input->post('MonthSIPPV');
                $pv_rateOfReturn =$this->input->post('pv_rateOfReturn');
                $pv_installment =$this->input->post('pv_installment');
                $PV_hidden =$this->input->post('PV_hidden');
                
              
                          
              //echo  $client_name = $result_client->name; 
                
                $css_data = '<style type="text/css">
                         table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
              //  $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'/pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                 $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                $contents=  str_replace("{{title}}",'Present Value',$contents);
                $contents=  str_replace("{{info}}",'This calculator helps you to determine the present value of a certain amount  which is expected in the future.',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style="" > 
                                                <th width="700" align="center"  colspan="2" bgcolor="#73B5EF">Present Value </th>                                            
                                            </tr>                                            
                                            <tr >                                                                                 
                                                <td align="left" >Amount to be received in future:</td>
                                                <td align="center" >'.$MonthSIPPV.'</td>                                              
                                            </tr>
                                            <tr>                                         
                                                <td align="left">Rate of Interest per annum(%):</td>
                                                <td align="center" >'.$pv_rateOfReturn.'</td>                                              
                                            </tr>
                                            <tr >                                               
                                                <td align="left">Duration:</td>
                                                <td align="center" >'.$pv_installment.'</td>                                                
                                            </tr>                                             
                                            
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2"  bgcolor="#73B5EF"> </th>                                            
                                            </tr>
                                            <tr >
                                                <td align="left"><strong style="font-weight: bold;">Current Value of your Investment:</strong></td>
                                                <td align="center"><strong style="font-weight: bold;">'.$PV_hidden.'</strong></td>
                                            </tr>   
                                            <tr >
                                                <td align="left"></td>
                                                <td align="center"></td>
                                            </tr>  
                                        </table>',$contents);
                
                $eq_data = $contents; 
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'present value';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                 $title = '';
                // set default header data
                //$pdf->SetHeaderData($logo, 20, PDF_HEADER_TITLE, PDF_HEADER_STRING);
            //    $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorNeed(){            
                $needSIPValue =$this->input->post('needSIPValue');
                $needrateOfInterest =$this->input->post('needrateOfInterest');
                $hidden_needFV =$this->input->post('hidden_needFV');
                $needinstallment =$this->input->post('needinstallment');
                $hidden_showTablePV =$this->input->post('hidden_showTablePV');
                $hidden_chartX =$this->input->post('hidden_chartX');
                $hidden_chartY =$this->input->post('hidden_chartY');
                $hidden_chartXper =($hidden_chartX/($hidden_chartX+$hidden_chartY))*100;
                $hidden_chartYper =($hidden_chartY/($hidden_chartX+$hidden_chartY))*100;
                
                $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff;font-family:Source Sans Pro; }
                        table td {font-size: 9px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 10px; padding: 2px; text-align: font-weight: bold; center; border: 0.5px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 10px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                        .toptable {border: 1px solid black; border-collapse: collapse;}    
                        .resulttable, .resultth, .resulttd {
                            border: 0.5px solid black;
                            border-collapse: collapse;
                            text-align: center;
                        } 
                    </style>';
                
               // $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'/pdf_html/pdf_with_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);
                
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
               // echo "<pre>".$hidden_showTablePV."</pre>";
                $contents=  str_replace("{{title}}",'SIP Need',$contents);
                $contents=  str_replace("{{info}}",'Monthly Saving Calculator help you to determine the monthly saving required to achieve your target amount goal.',$contents);
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th colspan="2" bgcolor="#73B5EF" align="center">Target Amount Need </th>                                            
                                            </tr>                                            
                                            <tr >                                                                                 
                                                <td align="left" style="border: 0.5px solid #727272;">Target Amount Needed :</td>
                                                <td align="center" style="border: 0.5px solid #727272;">'.$needSIPValue.'</td>                                              
                                            </tr>
                                            <tr>                                         
                                                <td align="left" style="border: 0.5px solid #727272;">Expected Rate of Interest(%):</td>
                                                <td align="center" style="border: 0.5px solid #727272;">'.$needrateOfInterest.'</td>                                              
                                            </tr>
                                            <tr>                                               
                                                <td align="left" style="border: 0.5px solid #727272;">Number of Installment(Months):</td>
                                                <td align="center" style="border: 0.5px solid #727272;">'.$needinstallment.'</td>                                                
                                            </tr>                                            
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" > 
                                                <th  colspan="2"  align="center" bgcolor="#73B5EF">Monthly Saving Required</th>                                            
                                            </tr>
                                            <tr >
                                                <td align="left" style="border: 0.5px solid #727272;" >Monthly savings Required To achieve <strong style="font-weight: bold;">Rs.'.($hidden_chartX + $hidden_chartY).'/-</strong> after <strong> '.$needinstallment.' Months </strong></td>  
                                                <td  align="center" style="border: 0.5px solid #727272;vertical-align: central;">'.$hidden_needFV.'</td>
                                            </tr>                                            
                                        </table>',$contents);
                $img_url='https://chart.googleapis.com/chart?chs=300x100&chd=t:'.round($hidden_chartXper).','.round($hidden_chartYper).'&cht=p3&chl=Investment|Earning&chco=182E87,559DD3'; 
                
                $contents =  str_replace("{{out_graph}}",'<img src="'.$img_url.'">' , $contents);
                $contents =  str_replace("{{out_graph_table}}",$hidden_showTablePV , $contents);
                
                $eq_data = $contents; 
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'sip need';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                  $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
                //$pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }      
                
        public function pdf_calculatorHLV(){             
                $hlvs_age =$this->input->post('hlvs_age');
                $s_hret_age =$this->input->post('s_hret_age');
                $hs_income =$this->input->post('hs_income');
                $hs_balance =$this->input->post('hs_balance');
                $s_expense =$this->input->post('s_expense');
                $s_overall_savings =$this->input->post('s_overall_savings');
                $s_existinInsuranceCover =$this->input->post('s_existinInsuranceCover');
                $hidden_yearsHLV =$this->input->post('hidden_yearsHLV');
                $hidden_hlv_valueHLV =$this->input->post('hidden_hlv_valueHLV');
                $hidden_extraincomeHLV =$this->input->post('hidden_extraincomeHLV');
                
                $css_data = '<style type="text/css">
                       table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
                //$url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'/pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);
                   
               // echo "<pre>".$contents."</pre>";
                 $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
                $contents=  str_replace("{{title}}",'Human Life Value Calculation',$contents);
                $contents=  str_replace("{{info}}",'The Human Life Value (HLV) Calculator helps you identify your life insurance needs on basis of income expenses, liabilities and investments and secure your family\'s future.',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2" align="center" bgcolor="#73B5EF">Human Life Cover Analysis</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left">Your Present Age:</td>
                                                <td align="left">'.$hlvs_age.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Your desired Retirement Age:</td>
                                                <td align="left">'.$s_hret_age.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Current Annual Income:</td>
                                                <td align="left">'.$hs_income.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Total Outstanding Liabilities:</td>
                                                <td align="left">'.$hs_balance.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Total Expenses on Family (Monthly):</td>
                                                <td align="left">'.$s_expense.'</td>                                              
                                            </tr>                                            
                                             <tr>                                                                                 
                                                <td align="left">Total Savings as of Today:</td>
                                                <td align="left">'.$s_overall_savings.'</td>                                              
                                            </tr>
                                            <tr>                                         
                                                <td align="left">Existing Insurance Cover (Personal):</td>
                                                <td align="left">'.$s_existinInsuranceCover.'</td>                                              
                                            </tr>
                                             
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable"  > 
                                                <th width="700" align="center" colspan="3" bgcolor="#73B5EF">Human Life Cover Result</th>                                            
                                            </tr>
                                            <tr >
                                                <td align="center" width="100%"  colspan="3" >Years Remaining for Your Retirement:</td>                                                 
                                            </tr>  
                                            <tr> 
                                                <td width="15%"></td>
                                                <td width="70%" align="center" style="border-bottom: 0.5px solid #727272;">'.$hidden_yearsHLV.'</td>
                                                <td width="15%"></td>
                                            </tr>
                                            <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr> 
                                            <tr >
                                                <td align="center" width="100%"  colspan="3" >To maintain your family current life style You need a total life Cover:</td>                                                
                                            </tr>  
                                            <tr>   
                                                <td width="15%"></td>
                                                <td width="70%" align="center" style="border-bottom: 0.5px solid #727272;">'.$hidden_hlv_valueHLV.'</td>
                                                <td width="15%"></td>
                                            </tr> 
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr> 
                                            <tr>
                                                <td align="center" width="100%"  colspan="3" >Considering your current savings and existing Life cover you need to take a fresh life cover of:</td>                                               
                                            </tr> 
                                            <tr> 
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center">'.$hidden_extraincomeHLV.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'HLV';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                 $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

               // $title = '<div>client name=Human Life Value Calculation</div>';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
              //  $pdf->SetHeaderData($logo, 10, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();
//echo $eq_data;exit;
                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
        
        public function pdf_calculatorMarriage(){            
                $s_m_age =$this->input->post('s_m_age');
                $s_marg_age =$this->input->post('s_marg_age');
                $s_m_cost =$this->input->post('s_m_cost');
                $s_m_savings =$this->input->post('s_m_savings');
                $s_m_inflation =$this->input->post('s_m_inflation');
                $s_m_return =$this->input->post('s_m_return');
                $hidden_mrfuture_amount =$this->input->post('hidden_mrfuture_amount');
                $hidden_mrmonthly_amount =$this->input->post('hidden_mrmonthly_amount');
                $hidden_mryearly_amount =$this->input->post('hidden_mryearly_amount');
                
                $css_data = '<style type="text/css">
                       table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
               // $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);
                
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
               // echo "<pre>".$contents."</pre>";
                $contents=  str_replace("{{title}}",'Marriage Planning',$contents);
                $contents=  str_replace("{{info}}",'',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th  width="700" align="center" colspan="2" bgcolor="#73B5EF">Marriage Planning Analysis</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left" >Present Age:</td>
                                                <td align="left" >'.$s_m_age.' yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left" >Desired age of Mrriage:</td>
                                                <td align="left">'.$s_marg_age.' yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Cost of Marriage as of today:</td>
                                                <td align="left">'.$s_m_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Total Savings as on today:</td>
                                                <td align="left">'.$s_m_savings.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Inflation Rate(%):</td>
                                                <td align="left">'.$s_m_inflation.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$s_m_return.'</td>                                              
                                            </tr>
                                                                                                                               
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th  width="700" align="center" colspan="3"  bgcolor="#73B5EF">Marriage Planning Result</th>                                            
                                            </tr>
                                            <tr >
                                                <td width="15%" ></td>
                                                <td width="80%" align="center">Total Amount Required at the time of Marriage will be:</td>
                                                <td width="15%"></td>
                                            </tr>
                                            <tr >
                                                 <td width="15%"  ></td>
                                                <td width="70%" align="center" style="border-bottom: 0.5px solid #727272;">'.$hidden_mrfuture_amount.'</td>
                                                <td width="15%" ></td>   
                                            </tr>
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr> 
                                            <tr >
                                                <td width="15%" ></td>
                                                <td width="70%" align="center">To achieve through Monthly Savings:</td>
                                                <td width="15%" align="left" ></td>
                                            </tr> 
                                            <tr >
                                                <td width="15%" align="center" ></td>
                                                <td align="center" width="70%">'.$hidden_mrmonthly_amount.'</td>
                                                <td width="15%" align="center" ></td>
                                            </tr> 
                                             <tr>                                             
                                                <td align="center" colspan="3">-------OR-------</td>
                                            </tr>
                                            <tr >
                                                <td width="15%" ></td>
                                                <td width="70%" align="center" >To achieve through Lumsum:</td>
                                                <td width="15%"></td>
                                            </tr> 
                                            <tr > 
                                                <td width="15%" ></td>
                                                <td width="70%" align="center" >'.$hidden_mryearly_amount.'</td>
                                                <td width="15%" ></td>
                                            </tr> 
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Marriage Planning';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                 $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
              //  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
               // $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorDelayRev(){            
                $TxtTargetCorp =$this->input->post('TxtTargetCorp');
                $txtHorizon =$this->input->post('txtHorizon');
                $TxtRoi =$this->input->post('TxtRoi');
                $txtDelay =$this->input->post('txtDelay');
                $hidden_LumpsumPv =$this->input->post('hidden_LumpsumPv');
                $hidden_MonthlySIPInvest =$this->input->post('hidden_MonthlySIPInvest');
                $hidden_sipDelayAmntLoose =$this->input->post('hidden_sipDelayAmntLoose');
                $hidden_LumpsumDelayAmntLoose =$this->input->post('hidden_LumpsumDelayAmntLoose');
                
                $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
                
                //$url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);
                
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);

               // echo "<pre>".$contents."</pre>";
                $contents=  str_replace("{{title}}",'Investment Delay',$contents);
                $contents=  str_replace("{{info}}",'Usually, we often tend to delay investing even in absence of any strong reasons. And we do happily thinking that how a few months would not matter much. But the reality is that delay in small savings even by few months can create a big hole in your future Wealth creation goal. Below analysis will help you to take decision why you should start savings right now!!!!',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" align="center"  colspan="2" bgcolor="#73B5EF">Investment Delay required</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left" >Target Corpus:</td>
                                                <td align="left" >'.$TxtTargetCorp.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Time horizon (Years):</td>
                                                <td align="left">'.$txtHorizon.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$TxtRoi.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Delay in starting saving from Today(Months):</td>
                                                <td align="left">'.$txtDelay.' </td>                                              
                                            </tr>
                                                                                                                     
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="3" align="center" bgcolor="#73B5EF">Investment Delay Analysis</th>                                            
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">Amount required achive Target Corpus:</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%" >'.$hidden_LumpsumPv.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td align="center" colspan="3">-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>    
                                                <td align="center" width="70%" >Monthly saving required to achieve Target Corpus:</td>                                                
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                             <tr>
                                                <td width="15%" align="center"></td>                                                    
                                                <td align="center" width="70%" style="border-bottom: 0.5px solid #727272;">'.$hidden_MonthlySIPInvest.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr>
                                            <tr>
                                            <td width="15%" align="center"></td> 
                                                <td align="center" width="70%" >Monthly saving delayed by '.$txtDelay.' Months , saving to save amount required:</td>
                                               <td width="15%" align="center"></td> 
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td> 
                                                <td align="center" width="70%" style="border-bottom: 0.5px solid #727272;">'.$hidden_sipDelayAmntLoose.'</td>
                                                    <td width="15%" align="center"></td> 
                                            </tr>
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td> 
                                                <td align="center" width="70%">Saving delayed Lumpsum investment by '.$txtDelay.' Months,Than you have to invest below amount as Lumpsum saving amount required:</td>
                                                <td width="15%" align="center"></td> 
                                            </tr> 
                                             <tr>
                                                <td width="15%" align="center"></td> 
                                                <td align="center"  width="70%">'.$hidden_LumpsumDelayAmntLoose.'</td>
                                                <td width="15%" align="center"></td> 
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Investment Delay';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                 $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
               // $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorSIPO(){            
                $MonthSIP =$this->input->post('MonthSIP');
                $rateOfReturn =$this->input->post('rateOfReturn');
                $installment =$this->input->post('installment');
                $hidden_FV =$this->input->post('hidden_FV');
                $hidden_investment =$this->input->post('hidden_investment');
                $hidden_earning =$this->input->post('hidden_earning');
                $hidden_sip_table =$this->input->post('hidden_sip_table');  
                
                $css_data = '<style type="text/css">
                         table { width:100%; border:0px solid #fff;font-family:Source Sans Pro; }
                        table td {font-size: 9px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 10px; padding: 2px; text-align: font-weight: bold; center; border: 0.5px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 10px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                        .toptable {border: 1px solid black; border-collapse: collapse;}    
                        .resulttable, .resultth, .resulttd {
                            border: 0.5px solid black;
                            border-collapse: collapse;
                            text-align: center;
                        } 
                        table.hiddenTable td {
                            border: 0.5px solid 4d4d4d;
                            border-collapse: collapse;
                            text-align: center;
                            font-size: 10px !important;
                        } 
                        table.hiddenTableHead th {
                            border: 0.5px solid 4d4d4d;
                            border-collapse: collapse;
                            text-align: center;
                            font-size: 12px !important;
                        } 
                    </style>';
                
                
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_with_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
                $contents=  str_replace("{{title}}",'Wealth Creation',$contents);
                $contents=  str_replace("{{info}}",'This Analysis helps you to determine the wealth you will accumulate over a period of time through systematic monthly investment.',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 0.5px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th colspan="2" bgcolor="#73B5EF" align="center">SIP Planning</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left" style="border: 0.5px solid #727272;">Monthly Installment:</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$MonthSIP.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left" style="border: 0.5px solid #727272;">Expected Rate of Interest(%):</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$rateOfReturn.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left" style="border: 0.5px solid #727272;">Tenure(Month):</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$installment.' </td>                                              
                                            </tr>
                                                                                                                                                                 
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 0.5px solid black;">
                                            <tr class="trtable" style="margin:1000px;padding-left: 100px;" > 
                                                <th colspan="2"  bgcolor="#73B5EF" align="center" >Your SIP Accumilation And Growth</th>                                            
                                            </tr>
                                            <tr>
                                                <td align="left" style="border: 0.5px solid #727272;">Wealth accumulated will be:</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_FV.'</td>
                                            </tr>                                            
                                            <tr>
                                                <td align="left" style="border: 0.5px solid #727272;">Amount investment by you:</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_investment.'</td>
                                            </tr>                                              
                                            <tr>
                                                <td align="left"style="border: 0.5px solid #727272;">Amount your earned: </td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_earning.'</td>
                                            </tr>                                                                                          
                                        </table>',$contents);
                
               $img_url='https://chart.googleapis.com/chart?chs=300x100&chd=t:'.round($hidden_investment).','.round($hidden_earning).'&cht=p3&chl=Investment|Earning&chco=182E87,559DD3';
                
                $contents =  str_replace("{{out_graph}}",'<img src="'.$img_url.'">' , $contents);
                $contents =  str_replace("{{out_graph_table}}",'<table style="width:100%;border: 0.5px solid black;" class="hiddenTable">
                                                <tr>
                                                    <th colspan="7"  bgcolor="#73B5EF" align="center">Expected Maturity Value of Your Investments</th>
                                                </tr>
                                                <tr> 
                                                    <th rowspan="2" class="hiddenTableHead" bgcolor="#A8CFFF" align="center">Expected<br> Returns</th>
                                                    <th colspan="6"  class="hiddenTableHead" bgcolor="#A8CFFF" align="center">Investment Period (in Months)</th>
                                                </tr>
                                                <tr bgcolor="#A8CFFF">
                                                    <th>60(5Yrs)</th>
                                                    <th>120(10Yrs)</th>
                                                    <th>180(15Yrs)</th>
                                                    <th>240(20Yrs)</th>
                                                    <th>300(25Yrs)</th>
                                                    <th>360(30Yrs)</th>   
                                                    
                                                </tr> '.$hidden_sip_table.'</table>' , $contents);
                
              $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'SIP O Meter';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                //  $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
               // $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorDelayPre(){            
                $delaySIPValue =$this->input->post('delaySIPValue');
                $delayRateOfInterest =$this->input->post('delayRateOfInterest');
                $delayInstallment =$this->input->post('delayInstallment');
                $s_delay =$this->input->post('s_delay');
                $hidden_invvaluetoday =$this->input->post('hidden_invvaluetoday');
                $hidden_invvaluedelay =$this->input->post('hidden_invvaluedelay');
                $hidden_lostmoney =$this->input->post('hidden_lostmoney');
                
                $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
               // $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents1 = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents1=  str_replace("{{img}}", $img_name,$contents1);
                $contents1=  str_replace("{{name}}", $result_client->name,$contents1);
                $contents1=  str_replace("{{email}}",$result_client->email_id,$contents1);
                $contents1=  str_replace("{{mobile}}",$result_client->mobile,$contents1);
                
                $contents1=  str_replace("{{title}}",'SIP-Delay',$contents1);
                $contents1=  str_replace("{{info}}",'Usually, we often tend to delay investing even in absence of any strong reasons. And we do happily thinking that how a few months would not matter much. But the reality is that delay in small savings even by few months can create a big hole in your future Wealth creation goal. Below analysis will help you to take decision why you should start savings right now!!!!',$contents1);
              
                $contents1=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style="padding-left:100px;" > 
                                                <th width="700" colspan="2" align="center" bgcolor="#73B5EF">Monthly Saving Required if delay</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left" >Monthly Investment:</td>
                                                <td align="left">'.$delaySIPValue.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$delayRateOfInterest.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Number of Installment(year):</td>
                                                <td align="left">'.$delayInstallment.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Delay in starting SIP from today(Enter in Months):</td>
                                                <td align="left">'.$s_delay.' </td>                                              
                                            </tr>
                                                                                                                     
                                        </table>',$contents1);
                
                $contents1 =  str_replace("{{out_table}}",'<table  width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style="padding-left:100px;" > 
                                               <th width="700" colspan="3" bgcolor="#73B5EF" align="center">Monthly Saving required if delayed analysis</th>                                         
                                            </tr>     
                                            <tr>   
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">Maturity Value, If savings are delayed:</td>
                                                <td width="15%" align="center"></td>                                              
                                            </tr>
                                            <tr>   
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%" style="border-bottom: 0.5px solid #727272;">'.$hidden_invvaluetoday.' </td>  
                                                <td width="15%" align="center"></td>    
                                            </tr>
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr> 
                                            <tr>   
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">Maturity Value, If you Delay your Investments:</td>
                                                <td width="15%" align="center"></td>                                              
                                            </tr>
                                            <tr>   
                                                 <td width="15%" align="center"></td>                                               
                                                <td align="center" width="70%" style="border-bottom:0.5px solid #727272;">'.$hidden_invvaluedelay.'</td>    
                                                <td width="15%" align="center"></td>   
                                            </tr>
                                             <tr >
                                                <td align="center" width="100%"  colspan="3" ></td>                                                 
                                            </tr> 
                                            <tr>     
												 <td width="15%" align="center"></td>  
                                                <td align="center" colspan="3">If you delay monthly saving by '.$s_delay.' Months, You are loosing Rs.'.$hidden_lostmoney.'/- in Maturity Value of your Investment !! </td>
												<td width="15%" align="center"></td>  
										   </tr>
                                        </table>',$contents1);
                
                $eq_data = $contents1;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Investment Delay Pre';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
                //$pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorVacation(){            
                $s_v_amount =$this->input->post('s_v_amount');
                $s_v_period =$this->input->post('s_v_period');
                $s_v_inflation =$this->input->post('s_v_inflation');
                $s_v_return =$this->input->post('s_v_return');
                $hidden_vcmonthly_amount =$this->input->post('hidden_vcmonthly_amount');
                $hidden_vclumpsum_amount =$this->input->post('hidden_vclumpsum_amount');
                $hidden_vcfuture_amount =$this->input->post('hidden_vcfuture_amount');
                
                $css_data = '<style type="text/css">
                       table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
                //$url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
                $contents=  str_replace("{{title}}",'Vacation Planning',$contents);
                $contents=  str_replace("{{info}}",'',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2" bgcolor="#73B5EF" align="center">Vacation Planning Analysis</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left" >Present Cost of Vacation:</td>
                                                <td align="left">'.$s_v_amount.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Years left for trip:</td>
                                                <td align="left">'.$s_v_period.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Inflation Rate(%):</td>
                                                <td align="left" >'.$s_v_inflation.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$s_v_return.'</td>                                              
                                            </tr>
                                                                                                                     
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border:1px solid black;">
                                            <tr class="trtable"  > 
                                                <th width="700" align="center" colspan="3" bgcolor="#73B5EF">Investment Requirment</th>                                            
                                            </tr>
                                             <tr>
                                                <td width="15%"></td>
                                                <td align="center" width="70%">The Future Value for your Vacation will be:</td>
                                                <td width="15%"></td>
                                            </tr> 
                                              <tr>
                                                 <td width="15%"></td>
                                                 <td width="70%" align="center" style="border-bottom: 0.5px solid #727272;">'.$hidden_vcfuture_amount.'</td>
                                                 <td width="15%"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%"></td>
                                                <td align="center" width="70%">Monthly Savings should be:</td>
                                                <td width="15%"></td>
                                            </tr> 
                                              <tr>
                                                 <td width="15%"></td>
                                                 <td align="center" width="70%">'.$hidden_vcmonthly_amount.'</td>
                                                 <td width="15%"></td>
                                            </tr> 
                                            <tr>
                                                <td align="center" colspan="3" >-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td width="15%"></td>
                                                <td align="center" width="70%">One Time Investment:</td>
                                                <td width="15%"></td>
                                            </tr>  
                                             <tr>
                                                <td width="15%"></td>
                                                <td align="center" width="70%">'.$hidden_vclumpsum_amount.'</td>
                                                <td width="15%"></td>   
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Vacation Planning';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                 $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
                //$pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
    
        public function pdf_calculatorCar(){            
                $s_c_amount =$this->input->post('s_c_amount');
                $s_c_period =$this->input->post('s_c_period');
                $s_c_inflation =$this->input->post('s_c_inflation');
                $s_c_return =$this->input->post('s_c_return');
                $hidden_crmonthly_amount =$this->input->post('hidden_crmonthly_amount');
                $hidden_crlumpsum_amount =$this->input->post('hidden_crlumpsum_amount');
                 $hidden_crfuture_amount =$this->input->post('hidden_crfuture_amount');
                
                $css_data = '<style type="text/css">
                       table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
               // $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
                $contents=  str_replace("{{title}}",'Car Planning',$contents);
                $contents=  str_replace("{{info}}",' ',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2" align="center" bgcolor="#73B5EF">Car Planning</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left">Present Cost of Car:</td>
                                                <td align="left">'.$s_c_amount.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Years to purchase car:</td>
                                                <td align="left">'.$s_c_period.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Inflation Rate(%):</td>
                                                <td align="left">'.$s_c_inflation.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$s_c_return.'</td>                                              
                                            </tr>
                                                                                                                     
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="3" align="center"  bgcolor="#73B5EF">Car Planning Requirment</th>                                            
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">The Future Value for your Car will be:</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" style="border-bottom: 0.5px solid #727272;" width="70%">'.$hidden_crfuture_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">Monthly Savings should be:</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">'.$hidden_crmonthly_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td align="center" colspan="3">-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">One Time Investment:</td>
                                                <td width="15%" align="center"></td>
                                            </tr>  
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td align="center" width="70%">'.$hidden_crlumpsum_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>  
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Car Planning';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
               // $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
        
        public function pdf_calculatorHome(){            
                $s_h_amount =$this->input->post('s_h_amount');
                $s_h_period =$this->input->post('s_h_period');
                $s_h_inflation =$this->input->post('s_h_inflation');
                $s_h_return =$this->input->post('s_h_return');
                $hidden_hcmonthly_amount =$this->input->post('hidden_hcmonthly_amount');
                $hidden_hclumpsum_amount =$this->input->post('hidden_hclumpsum_amount');
                $hidden_hcfuture_amount =$this->input->post('hidden_hcfuture_amount');
                
                $css_data = '<style type="text/css">
                       table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
               // $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                $contents=  str_replace("{{title}}",'Home Planning',$contents);
                $contents=  str_replace("{{info}}",'This analysis will help you to determine the monthly saving required to achieve home planning goal.',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th  width="700" align="center" colspan="2" bgcolor="#73B5EF">Home Planning</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left">Present Cost of Home:</td>
                                                <td align="left">'.$s_h_amount.' </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Years left to buy Home:</td>
                                                <td align="left">'.$s_h_period.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Inflation Rate(%):</td>
                                                <td align="left">'.$s_h_inflation.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Rate of Interest(%):</td>
                                                <td align="left">'.$s_h_return.'</td>                                              
                                            </tr>
                                                                                                                     
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th  width="700" colspan="3" align="center" bgcolor="#73B5EF">Home Planning Result</th>                                            
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center">The Future Value for your Home will be:</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center" style="border-bottom: 0.5px solid #727272;">'.$hidden_hcfuture_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"> Monthly Savings should be:</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center">'.$hidden_hcmonthly_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                            <tr>
                                                <td align="center" colspan="3">-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center">One Time Investment:</td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                            <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center">'.$hidden_hclumpsum_amount.'</td>
                                                <td width="15%" align="center"></td>
                                            </tr>
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Home Planning';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                 $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
               // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
                //$pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        
             
        public function pdf_calculatorEducation(){            
                $s_ec_age =$this->input->post('s_ec_age');
                $e_inflation =$this->input->post('e_inflation');
                $e_return =$this->input->post('e_return');
                $sch_year =$this->input->post('sch_year');
                $sch_cost =$this->input->post('sch_cost');
                $sch_fut_cost =$this->input->post('sch_fut_cost');
                $grd_year =$this->input->post('grd_year');
                $grd_cost =$this->input->post('grd_cost');
                $grd_fut_cost =$this->input->post('grd_fut_cost');
                $pgrd_year =$this->input->post('pgrd_year');
                $pgrd_cost =$this->input->post('pgrd_cost');
                $pgrd_fut_cost =$this->input->post('pgrd_fut_cost');
                $carr_year =$this->input->post('carr_year');
                $carr_cost =$this->input->post('carr_cost');
                $carr_fut_cost =$this->input->post('carr_fut_cost');
                $marg_year =$this->input->post('marg_year');
                $marg_cost =$this->input->post('marg_cost');
                $marg_fut_cost =$this->input->post('marg_fut_cost');
                $hidden_edfuture_amount =$this->input->post('hidden_edfuture_amount');
                $hidden_edhlv_amount =$this->input->post('hidden_edhlv_amount');
                $hidden_edmonthly_amount =$this->input->post('hidden_edmonthly_amount');
                $hidden_edyearly_amount =$this->input->post('hidden_edyearly_amount');
                
                $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: font-weight: bold; center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#000000; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 6px; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { font-size: 14px; padding-bottom:15px;font-family: "Courier New", Times, serif;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
              //  $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, base_url().'pdf_html/pdf_eduction.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $result_client = $this->broker_pdf->get_broker_info($this->session->userdata['broker_id']); 
                $img_name= glob("uploads/brokers/".$this->session->userdata['broker_id']."/*.*")[0];
                $contents=  str_replace("{{img}}", $img_name,$contents);
                $contents=  str_replace("{{name}}", $result_client->name,$contents);
                $contents=  str_replace("{{email}}",$result_client->email_id,$contents);
                $contents=  str_replace("{{mobile}}",$result_client->mobile,$contents);
                
                $contents=  str_replace("{{title}}",'Child Education Planning',$contents);
                $contents=  str_replace("{{info}}",'This analysis will help you to determine the monthly saving required to achieve Education planning goal.',$contents);
              
               $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                            <tr class="trtable" bgcolor="#73B5EF">
                                <th  rowspan="2">child eduction planning</th>
                                <th style="border: 0.5px solid black;">Child\'s DOB</th>
                                <th style="border: 0.5px solid black;">Expected Inflation rate(%)</th>
                                <th style="border: 0.5px solid black;">Expected Return rate(%)</th>
                            </tr>
                            <tr>
                                <td style="border: 0.5px solid #727272;">'.$s_ec_age.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$e_inflation.'</td>
                                <td style="border: 0.5px solid #727272;">'.$e_return.'</td>
                            </tr>
                            <tr>
                                <td  rowspan="2" bgcolor="#73B5EF" style="border: 0.5px solid black;color:#FFF;"> Schooling Info </td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF" >Years Remaining</td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF" >Present Annual Cost</td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF" >Total Cost for Schooling</td>
                            </tr>
                            <tr>
                                <td style="border: 0.5px solid #727272;">'.$sch_year.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$sch_cost.'</td>
                                <td style="border: 0.5px solid #727272;">'.$sch_fut_cost.'</td>
                            </tr>
                             <tr>
                                <td  rowspan="2" style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF"> Graduation Info </td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF">At What Age</td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF">Present Cost</td>
                                <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF">Future Cost</td>
                            </tr>
                            <tr>
                                <td style="border: 0.5px solid #727272;">'.$grd_year.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$grd_cost.'</td>
                                <td style="border: 0.5px solid #727272;">'.$grd_fut_cost.'</td>
                            </tr>
                             <tr>
                                  <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF"> Post Graduation Info</td>
                                <td style="border: 0.5px solid #727272;">'.$pgrd_year.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$pgrd_cost.'</td>
                                <td style="border: 0.5px solid #727272;">'.$pgrd_fut_cost.'</td>
                            </tr>
                            <tr>
                                  <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF">Career</td>
                                <td style="border: 0.5px solid #727272;">'.$carr_year.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$carr_cost.'</td>
                                <td style="border: 0.5px solid #727272;">'.$carr_fut_cost.'</td>
                            </tr>
                            <tr>
                                  <td style="border: 0.5px solid black;color:#FFF;" bgcolor="#73B5EF">Marriage</td>
                                <td style="border: 0.5px solid #727272;">'.$marg_year.' Yrs</td>
                                <td style="border: 0.5px solid #727272;">'.$marg_cost.'</td>
                                <td style="border: 0.5px solid #727272;">'.$marg_fut_cost.'</td>
                            </tr>
                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th  colspan="2" bgcolor="#73B5EF">Child Education Planning Result</th>                                            
                                            </tr>
                                            <tr>
                                                <td align="left" style="border: 0.5px solid #727272;" >Total Cost of Upbringing your child is:</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_edfuture_amount.'</td>
                                            </tr> 
                                             <tr>
                                                <td align="left" style="border: 0.5px solid #727272;" >Life Cover needed to protect your child\'s future:</td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_edhlv_amount.'</td>
                                            </tr> 
                                             <tr>
                                                <td align="left" style="border: 0.5px solid #727272;" >Your Monthly Savings should be: </td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_edmonthly_amount.'</td>
                                            </tr> 
                                            <tr>
                                                <td align="center" colspan="2">-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td align="left" style="border: 0.5px solid #727272;">One Time Lumpsum Investment: </td>
                                                <td align="left" style="border: 0.5px solid #727272;">'.$hidden_edyearly_amount.'</td>
                                            </tr>  
                                             <tr>
                                                <td width="15%" align="center"></td>
                                                <td width="70%" align="center"></td>
                                                <td width="15%" align="center"></td>
                                            </tr> 
                                        </table>',$contents);
               
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Child Education';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
               //  $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
               // $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, 15, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }      
        
        
        public function pdf_calculatorEducationOld(){            
                $s_ec_age =$this->input->post('s_ec_age');
                $e_inflation =$this->input->post('e_inflation');
                $e_return =$this->input->post('e_return');
                $sch_year =$this->input->post('sch_year');
                $sch_cost =$this->input->post('sch_cost');
                $sch_fut_cost =$this->input->post('sch_fut_cost');
                $grd_year =$this->input->post('grd_year');
                $grd_cost =$this->input->post('grd_cost');
                $grd_fut_cost =$this->input->post('grd_fut_cost');
                $pgrd_year =$this->input->post('pgrd_year');
                $pgrd_cost =$this->input->post('pgrd_cost');
                $pgrd_fut_cost =$this->input->post('pgrd_fut_cost');
                $carr_year =$this->input->post('carr_year');
                $carr_cost =$this->input->post('carr_cost');
                $carr_fut_cost =$this->input->post('carr_fut_cost');
                $marg_year =$this->input->post('marg_year');
                $marg_cost =$this->input->post('marg_cost');
                $marg_fut_cost =$this->input->post('marg_fut_cost');
                $hidden_edfuture_amount =$this->input->post('hidden_edfuture_amount');
                $hidden_edhlv_amount =$this->input->post('hidden_edhlv_amount');
                $hidden_edmonthly_amount =$this->input->post('hidden_edmonthly_amount');
                $hidden_edyearly_amount =$this->input->post('hidden_edyearly_amount');
                
                $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff; font-size: 14px;}
                        table td {font-size: 14px; padding:10px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 15px; padding: 2px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse;color:#FFF; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:18px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { text-align: left; font-size: 18px; padding-bottom:15px;  }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
                
                $url='http://localhost/finance_tools';
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url.'/pdf_html/pdf_without_graph.html');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $contents = curl_exec($ch);

               // echo "<pre>".$contents."</pre>";
                $contents=  str_replace("{{title}}",'Child Education Planning',$contents);
                $contents=  str_replace("{{info}}",'This present value calculator can be used to calculate the present value of a certain amount of money in the future or periodical annuity payments.',$contents);
              
                $contents=  str_replace("{{input_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" style=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2" bgcolor="#3071AA">Child Education Planning</th>                                            
                                            </tr>                                            
                                            <tr>                                                                                 
                                                <td align="left">Child\'s DOB:</td>
                                                <td align="left">'.$s_ec_age.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Inflation rate:</td>
                                                <td align="left">'.$e_inflation.'%</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Expected Return rate:</td>
                                                <td align="left">'.$e_return.'%</td>                                              
                                            </tr>
                                            <tr > 
                                                <td align="center" colspan="2" style="border: 1px solid black;"  bgcolor="#DDFEFC"> Schooling Info </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Years Remaining</td>
                                                <td align="left">'.$sch_year.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Present Annual Cost</td>
                                                <td align="left">'.$sch_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Total Cost for Schooling</td>
                                                <td align="left">'.$sch_fut_cost.'</td>                                              
                                            </tr>
                                            <tr > 
                                                <td align="center" colspan="2" style="border: 1px solid black;"  bgcolor="#DDFEFC"> Graduation Info  </td>                                              
                                            </tr> 
                                             <tr>                                                                                 
                                                <td align="left">At What Age</td>
                                                <td align="left">'.$grd_year.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Present Cost</td>
                                                <td align="left">'.$grd_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Future Cost</td>
                                                <td align="left">'.$grd_fut_cost.'</td>                                              
                                            </tr>
                                            <tr> 
                                                <td align="center" colspan="2" style="border: 1px solid black;" bgcolor="#DDFEFC"> Post Graduation Info  </td>                                              
                                            </tr>
                                             <tr>                                                                                 
                                                <td align="left">At What Age</td>
                                                <td align="left">'.$pgrd_year.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Present Cost</td>
                                                <td align="left">'.$pgrd_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Future Cost</td>
                                                <td align="left">'.$pgrd_fut_cost.'</td>                                              
                                            </tr>
                                            <tr> 
                                                <td align="center" colspan="2" style="border: 1px solid black;" bgcolor="#DDFEFC"> Career  </td>                                              
                                            </tr>
                                             <tr>                                                                                 
                                                <td align="left">At What Age</td>
                                                <td align="left">'.$carr_year.' Yrs</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Present Cost</td>
                                                <td align="left">'.$carr_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Future Cost</td>
                                                <td align="left">'.$carr_fut_cost.'</td>                                              
                                            </tr>
                                            <tr> 
                                                <td align="center" colspan="2" style="border: 1px solid black;" bgcolor="#DDFEFC"> Marriage  </td>                                              
                                            </tr>
                                             <tr>                                                                                 
                                                <td align="left">At What Age</td>
                                                <td align="left">'.$marg_year.' Yrs </td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Present Cost</td>
                                                <td align="left">'.$marg_cost.'</td>                                              
                                            </tr>
                                            <tr>                                                                                 
                                                <td align="left">Future Cost</td>
                                                <td align="left">'.$marg_fut_cost.'</td>                                              
                                            </tr>
                                        </table>',$contents);
                
                $contents =  str_replace("{{out_table}}",'<table width="100%" style="border: 1px solid black;">
                                            <tr class="trtable" tyle=" margin: 1000px;padding-left: 100px;" > 
                                                <th width="700" colspan="2"  bgcolor="#3071AA">Child Education Planning Result</th>                                            
                                            </tr>
                                            <tr>
                                                <td align="left" width="450" >Total Cost of Upbringing your child is</td>
                                                <td align="left">'.$hidden_edfuture_amount.'</td>
                                            </tr> 
                                             <tr>
                                                <td align="left" width="450" >Life Cover needed to protect your child\'s future</td>
                                                <td align="left">'.$hidden_edhlv_amount.'</td>
                                            </tr> 
                                             <tr>
                                                <td align="left" width="450" >Your Monthly Savings should be : </td>
                                                <td align="left">'.$hidden_edmonthly_amount.'</td>
                                            </tr> 
                                            <tr>
                                                <td align="center" colspan="2">-------OR-------</td>
                                            </tr>
                                            <tr>
                                                <td align="left" width="450">One Time Lumpsum Investment : </td>
                                                <td align="left">'.$hidden_edyearly_amount.'</td>
                                            </tr>  
                                        </table>',$contents);
                
                $eq_data = $contents;
                
                $logo = '0001/logo-big-1.png';
                $report_name = 'Child Education';

                /** Error reporting */
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);

                // Include the main TCPDF library (search for installation path).
                require_once('extras/tcpdf/tcpdf.php');

                // create new PDF document
                //  $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Broker');
                $pdf->SetTitle($report_name);
                $pdf->SetSubject($report_name);
                $pdf->SetKeywords($report_name);

                $title = 'client name';
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
                $pdf->SetHeaderData($logo, 20, $title, '');

                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT+15, PDF_MARGIN_TOP+5, PDF_MARGIN_RIGHT+15);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // set font
                $pdf->SetFont('sourcesanspro', '', 12);

                $pdf->AddPage();

                // output the HTML content
                $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');

                // reset pointer to the last page
                $pdf->lastPage();

                //Close and output PDF document
                $pdf->Output($report_name.'.pdf', 'D');
                //$pdf->Output('Equity Portfolio.pdf', 'I');

                //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
           
        }        

    //gets all occupations of admin & current broker from database
    /*public function ajax_list()
    {
        $brokerID = $this->session->userdata('broker_id');
        $lastImportData = $this->common->get_last_imports("li.import_type = 'Mutual Fund NAV' AND (li.broker_id = '".$brokerID."' or li.broker_id is null)");
        /* changed the below code to show only last imported data - Salmaan - 12-05-2016
        if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d");
        }

        $lastSeventhDay = date('Y-m-d', strtotime($lastDay.' - 7 days'));
        $list = $this->mf->get_mf_schemes_hist('sh.scheme_date BETWEEN "'.$lastSeventhDay.'" AND "'.$lastDay.'"');*/

        /*if($lastImportData) {
            $lastImportDay = DateTime::createFromFormat('d/m/Y H:i:s A',$lastImportData[0]->last_import_date);
            $lastImportDay->modify('-1 days');
            $lastDay = $lastImportDay->format('Y-m-d');
        } else {
            $lastDay = date("Y-m-d", strtotime('-1 days'));
        }
        $list = $this->mf->get_mf_schemes_hist('sh.scheme_date = "'.$lastDay.'"');

        $data = array();
        $num = 10;
        if(isset ($_POST['start']))
            $num = $_POST['start'];
        foreach($list as $scheme)
        {
            $num++;
            $row = array();
            //$row['scheme_history_id']=$scheme->scheme_history_id;  use this if needed
            $row['scheme_id']=$scheme->scheme_id;
            $row['scheme_name']=$scheme->scheme_name;
            $row['scheme_type_id']=$scheme->scheme_type_id;
            $row['scheme_type']=$scheme->scheme_type;
            $row['current_nav']=$scheme->current_nav;
            $row['scheme_date']=$scheme->scheme_date;

            //add html for action
            /*if(!($occupation->broker_id == null || $occupation->broker_id == '')) {
                $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"
                    onclick="edit_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete"
                    onclick="delete_occupation('."'".$occupation->occupation_id."'".')">
                    <i class="fa fa-trash-o"></i></a>';
            } else {
                $row['action'] = '';
            }*/
/*
            $data[] = $row;
        }
        $output = array(
            "draw"=>1,
            //"recordsTotal"=>$this->family->count_all($brokerID),
            //"recordsFiltered"=>$this->family->count_filtered(),
            "data"=>$data
        );
        //output to json format
        echo json_encode($output);
    }*/
} 