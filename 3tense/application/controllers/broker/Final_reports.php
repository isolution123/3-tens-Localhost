<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Final_reports extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        //load libraries, helpers
        $this->load->library('session');
        $this->load->library('Custom_exception');
        $this->load->helper('url');

        if(empty($this->session->userdata['user_id']))
        {
            redirect('broker');
        }

        //load model
        $this->load->model('Families_model', 'family');
        $this->load->model('Clients_model', 'client');
        $this->load->model('Reports_model', 'report');
    }

    //Summary Report
    function summary_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Summary Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/report/summary_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_summary_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID,
                
            );
            $where1 = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-1 days"))
            );
            $where2 = array(
                'clientID'=> $client_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-2 days"))
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID
            );
            $where1 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-1 days"))
            );
            $where2 = array(
                'familyID'=> $family_id,
                'brokerID'=> $brokerID,
                'reportDate'=>date('Y-m-d',strtotime("-2 days"))
            );
        }
        $logo = "";
        $status = false;
        $summary_rep = $this->report->get_summary_report($type, $where);
        
        $summary_rep_previous_1 = $this->report->get_summary_report_previous($type, $where1);
        $summary_rep_previous_2 = $this->report->get_summary_report_previous($type, $where2);
        if(!empty($summary_rep))
        {
            unset($_SESSION['summary_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $summary_rep_array = array(
                                        'summary_rep_data' => $summary_rep,
                                        'summary_rep_previous_1'=>$summary_rep_previous_1,
                                        'summary_rep_previous_2'=>$summary_rep_previous_2,
                                        'brokerID'=>$brokerID, 
                                        'report_info'=>$rep_info);
                                        
            $this->session->set_userdata('summary_report', $summary_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }

    //Cash Flow Report
    function cash_flow_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Cash Flow Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/report/cash_flow_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_cash_flow_report()
    {
        $family_id = $this->input->post('famName');
        if(is_array($family_id)) {
            $family_id = $family_id[0];
        }
        $client_id = $this->input->post('client_id');
        $from_date = $this->input->post('from_date');
        $fDate = DateTime::createFromFormat('d/m/Y',$from_date);
        $from_date = $fDate->format('Y-m-d');
        $to_date = $this->input->post('to_date');
        $tDate = DateTime::createFromFormat('d/m/Y',$to_date);
        $to_date = $tDate->format('Y-m-d');
        $brokerID = $this->session->userdata('broker_id');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '')
        {
            $where = array(
                'clientID'=> $client_id,
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
            );
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id,
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
            );
        }
        $logo = "";
        $status = false;
        $fam_info = $this->family->get_family_by_id($family_id);
        $cash_flow_rep = $this->report->get_cash_flow_report($type, $where);
        //print_r($cash_flow_rep);
        if(!empty($cash_flow_rep))
        {
            unset($_SESSION['cash_flow_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type);
            $cash_flow_rep_array = array('cash_flow_rep_data' => $cash_flow_rep, 'report_info'=>$rep_info, 'fam_info'=>$fam_info);
            $this->session->set_userdata('cash_flow_report', $cash_flow_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
    }


    //Ledger Report
    function ledger_report()
    {
        //data to pass to header view like page title, css, js
        $header['title']='Ledger Report';
        $header['css'] = array(
            'assets/users/plugins/form-select2/select2.css'
        );
        $header['js'] = array(
            'assets/users/plugins/form-select2/select2.min.js',
            'assets/users/js/common.js',
            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',
            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',
            'assets/users/plugins/bootbox/bootbox.min.js'
        );

        $brokerID = $this->session->userdata('broker_id');
        $data['families'] = $this->family->get_families_broker_dropdown($brokerID);
        $cli_condition = array('c.status' => '1', 'fam.broker_id' => $brokerID);
        $data['clients'] = $this->client->get_clients_broker_dropdown($cli_condition);

        //load views
        $this->load->view('broker/common/header', $header);
        $this->load->view('broker/report/ledger_report', $data);
        $this->load->view('broker/common/footer');
    }

    function get_ledger_report()
    {
        $family_id = $this->input->post('famName');
        $client_id = $this->input->post('client_id');
        $from_date = $this->input->post('from_date');
        $fDate = DateTime::createFromFormat('d/m/Y',$from_date);
        $from_date = $fDate->format('Y-m-d');
        $to_date = $this->input->post('to_date');
        $tDate = DateTime::createFromFormat('d/m/Y',$to_date);
        $to_date = $tDate->format('Y-m-d');
        $brokerID = $this->session->userdata('broker_id');
        $InvestmentType = $this->input->post('investment_type');
        $type = 'client';
        $where = "";
        if($client_id != null && $client_id != '' && !empty($client_id))
        {
            $where = array(
                'clientID'=> $client_id[0],
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
                
            );
            $id = $client_id[0];
            $nameRes = $this->client->get_client_info($client_id[0]);
            $name = $nameRes->name;
            $where = array(
                'clientID'=> $client_id[0],
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID,
                'InvestmentType'=>$InvestmentType
            );
            
        }
        else
        {
            $type = 'family';
            $where = array(
                'familyID'=> $family_id[0],
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID
            );
            $id = $family_id[0];
            $famRes = $this->family->get_family_by_id($family_id[0]);
            $name = $famRes->name;
              $where = array(
                'familyID'=> $family_id[0],
                'fromDate'=> $from_date,
                'toDate'=> $to_date,
                'brokerID'=> $brokerID,
                'InvestmentType'=>$InvestmentType
            );
        }
        $logo = "";
        $status = false;
        
        
        $ledger_rep_inflow = $this->report->get_ledger_report_inflow($type, $where);
        
        $ledger_rep_outflow = $this->report->get_ledger_report_outflow($type, $where);
        
        $ledger_rep_dividend = $this->report->get_ledger_report_dividend($type, $where);
    
        if(!empty($ledger_rep_inflow) || !empty($ledger_rep_outflow))
        {
            unset($_SESSION['ledger_report']);
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
            $rep_info = array('logo' => $logo, 'report_type' => $type, 'id' => $id, 'name' => $name);
            $ledger_rep_array = array(
                'ledger_rep_inflow_data' => $ledger_rep_inflow,
                'ledger_rep_outflow_data' => $ledger_rep_outflow,
                'ledger_rep_dividend_data' => $ledger_rep_dividend,
                'report_info'=>$rep_info);
            $this->session->set_userdata('ledger_report', $ledger_rep_array);
            $status = true;
        }
        echo json_encode(array('Status'=> $status));
        //echo json_encode($ledger_rep_array);
    }



    /* Export Functions */
    function export_to_pdf()
    {
        ini_set('memory_limit', '512M');
        //echo html_entity_decode($_POST['htmlData']);
        if(!(isset($_POST['htmlData']) && !empty($_POST['htmlData']))) {
            echo "<script type='text/javascript'>
                alert('Unauthorized Access. Get Outta Here!');
                window.top.close();  //close the current tab
              </script>";

        } else {
            /*$css_data = '<style type="text/css">
                    table td, table th {font-size: 8px;}
                    .title { width:100%; line-height:28px; font-size:15px; font-weight:bold; text-align:center; border:2px double black; }
                    .info { font-size: 12px; font-weight: lighter; border:none; }
                    .head-row { background-color: #003F7D; color: #fff; font-weight:bold}
                    .dataTotal {font-weight: bold}
                    .no-border {border-width: 0px;}
                </style>';*/
            $css_data = '<style type="text/css">
                        table { width:100%; border:0px solid #fff; }
                        table td {font-size: 10px; padding:2px; color:#4d4d4d; text-align:center; }
                        .border {border: 1px solid #4d4d4d; border-collapse: collapse;}
                        table th { font-size: 10px; padding: 2px; text-align: center; border: 1px solid #4d4d4d; border-collapse: collapse; }
                        .amount { text-align:left; padding: 10px; text-indent: 15px; }
                        .amount-cf { text-align:left; text-indent: 8px; }
                        .noWrap { white-space: nowrap; }
                        .title { width:100%; line-height:28px; background-color: #f4a817; color: #000; font-size:14px; text-align:center; border:2px double #4d4d4d; }
                        .info { font-size: 10px; font-weight: lighter; border:none; }
                        .head-row { background-color: #4f8edc; color: #fff; font-weight: bold;}
                        .dataTotal {font-weight: bold; color:#4f8edc;}
                        .normal {font-weight: normal;}
                        .no-border {border-width: 0px; border-color: #fff;}
                        .client-name { text-align: left; font-size: 12px; font-weight: bold; }
                        .bigger {font-weight: bold; font-size:11px;}
                    </style>';
            $title_data = $this->input->post('titleData');
            $eq_data = $this->input->post('htmlData');
            
            
            $logo = $this->input->post('logo');
            $report_name = $this->input->post('report_name');

            /** Error reporting */
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);

            // Include the main TCPDF library (search for installation path).
            require_once('application/third_party/tcpdf/tcpdf.php');

            // create new PDF document
            
            $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Broker');
            $pdf->SetTitle($report_name);
            $pdf->SetSubject($report_name);
            $pdf->SetKeywords($report_name);

            $title = '';
            // set default header data
            //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' -Admin', PDF_HEADER_STRING);
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

            $pdf->AddPage();

            $pdf->writeHTML($css_data.$title_data, true, false, true, false, '');
            // reset pointer to the last page
            $x=10;$y=50;
            if(isset($_POST['line_chart']))
            {
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                $imgdata = base64_decode($_POST['line_chart']);    
             
            $pdf->SetXY($x, $y);
            $pdf->Image('@'.$imgdata, '', '', 130, 80, '', '', 'T', false, 300, '', false, false, 2, false, false, false);
            
            }
            if(isset($_POST['pie_chart']))
            {
                if(isset($_POST['line_chart']))
                {
                    $x+=160;    
                }
            $imgdata = base64_decode($_POST['pie_chart']);    
            $pdf->SetXY($x, $y+2);
            $pdf->Image('@'.$imgdata, '', '', 120, 80, '', '', 'T', false, 300, '', false, false, 2, false, false, false);
            $y+=85;
            }
            // output the HTML content
            $pdf->SetXY(10, $y);
            $pdf->writeHTML($css_data.$eq_data, true, false, true, false, '');
            
            $pdf->lastPage();
              ob_end_clean();
            
            //Close and output PDF document
            $pdf->Output($report_name.'.pdf', 'D');
            //$pdf->Output('Equity Portfolio.pdf', 'I');

            //echo header("Content-Disposition: attachment; filename=Equity Portfolio.pdf;");
        }
    }

    function PHPExcel_UnicodeFix($value) {

        //data seems to be UTF-8, despite internal encoding
        // $iconv = iconv_get_encoding();
        // $it = $iconv['internal_encoding'];
        //such that $it == 'ISO-8859-1'

        //but iconv($it,"ASCII//TRANSLIT",$value) doesn't work (data is already UTF-8?)

        //Excel does not accept UTF-8?
        $value_fixed = iconv("UTF-8","ASCII//TRANSLIT",$value);

        return $value_fixed;
    }

    function export_to_excel()
    {
        ini_set('memory_limit', '512M');
        if(!(isset($_POST['htmlData']) && !empty($_POST['htmlData']))) {
            echo "<script type='text/javascript'>
                alert('Unauthorized Access. Get Outta Here!');
                window.top.close();  //close the current tab
              </script>";

        } else {
            ob_start();
            $htmlData = $this->input->post('htmlData');
            $sheetName = $this->input->post('name');
            $report_name = $this->input->post('report_name');

            //remove all rupee symbols from data, as it does not display properly in Excel
            $htmlData = str_replace("₹","",$htmlData);
            //$htmlData = mb_convert_encoding($htmlData, 'UTF-16LE', 'UTF-8');
            //$htmlData = "\xEF\xBB\xBF".$htmlData;

            //header('Content-type: text/html; charset=utf-8');
            //echo $htmlData.'<br/>'; die();
            //print chr(255) . chr(254) . mb_convert_encoding($htmlData, 'UTF-16LE', 'UTF-8');

            //load the excel library
            $this->load->library('Excel');

            // Load the table view into a variable
            //$html = $this->load->view('broker/report/equity_report_view_family', $htmlData, true);

            // Put the html into a temporary file
            $tmpfile = time().'.html';
            file_put_contents($tmpfile, $htmlData);

            //echo '<br/></br>';
            //echo file_get_contents($tmpfile);
            //die();

            // Read the contents of the file into PHPExcel Reader class
            $objPHPExcel = new PHPExcel();
            $reader = PHPExcel_IOFactory::createReader('HTML');
            $reader->loadIntoExisting($tmpfile, $objPHPExcel);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName); // Change sheet's title if you want

            // Auto size columns for each worksheet
            for($col = 'A'; $col !== 'Z'; $col++) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }

            // Set headers for Excel file type
            //header('Content-type: text/html; charset=utf-8');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            //header('Content-Type: application/vnd.ms-excel'); // header for .xlxs file
            header('Content-Disposition: attachment;filename='.$report_name.'.xlsx'); // specify the download file name
            header('Cache-Control: max-age=0');

            // Pass to writer and output as needed
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();


            $objWriter->save('php://output');
            //$objWriter->save('Equity Portfolio.xlsx');

            // Delete temporary file
            unlink($tmpfile);
            //ob_clean();
            //ob_flush();
            exit;


            /*$htmlData = $this->input->post('htmlData');
            $sheetName = $this->input->post('name');
            $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        
                        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
                        <title>TESTING</title>
                        </head>
                        <style type="text/css">
                            body {
                                font-family:Verdana, Arial, Helvetica, sans-serif;
                                font-size:12px;
                                margin:0px;
                                padding:0px;
                            }
                        </style>
                        <html>
                        <body>';
                            $content .= $htmlData;
                        $content .= '</body>
                        </html>';
            //header("Content-type: application/x-msdownload");
            //header('Content-Disposition: attachment; filename="filename.xls"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            header('Content-Disposition: attachment;filename=Equity Portfolio.xlsx'); // specify the download file name
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $content;*/
        }
    }

}