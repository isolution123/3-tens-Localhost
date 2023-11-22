<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Equity_scripts extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        //load model reminder_model rem is the object
        $this->load->helper('form');
        $this->load->model('Equity_model', 'eq');
        $this->load->model('Common_model', 'common');
        $this->load->library('Mail', 'mail');
    }
	
	
	/**************************************************************************
     * *********** CRON JOB - Importing Bhav Copy from BSE India *************
     **************************************************************************/
    /*
    //function to be called from CRON JOB - Hosting
    function update_bhav_copy_latest()
    {
        //set the url from where to download
        $bc_url = 'http://www.bseindia.com/download/BhavCopy/Equity/';
        //get the date for which you want bhav_copy - start with todays date
        $bc_date = date('dmy');

        //run till we get the file properly
        $success = false;
        $cnt = 0;
        while($success == false && $cnt<5) {
            $cnt++;
            //make the filename to download
            $bc_filename = 'EQ'.$bc_date.'_CSV.ZIP';
            //now download the bhav copy from BSE website
            $filepath = $this->download_bhav_copy($bc_url,$bc_filename);
            //if it returns false, try again with one day before
            if(!$filepath) {
                echo $bc_date.' - Could not download bhav copy. <br/>';
                $bc_date = $this->get_yesterday($bc_date);
                continue;
            }

            //now we have to extract the zip
            $unzip = $this->extract_archive($filepath);
            if(!$unzip) {
                echo $bc_date.' - Could not open/extract zip file. <br/>';
                $bc_date = $this->get_yesterday($bc_date);
                continue;
            } else {
                $csv_file = 'EQ'.$bc_date.'.CSV';
                //now we will read csv file and put it into array
                $all_data = $this->read_csv('downloads/bhav_copy/'.$csv_file);
                if($all_data) {
                    //get data of only columns that we require
                    $data = array();
                    $temp_row = array();
                    foreach($all_data as $row) {
                        $temp_row['scrip_code'] = $row['SC_CODE'];
                        $temp_row['scrip_name'] = $row['SC_NAME'];
                        $temp_row['close_rate'] = $row['CLOSE'];
                        $data[] = $temp_row;
                    }
                    //delete and insert into scrip_rates table
                    //$deleted = $this->eq->delete_scrip_data();
                    $deleted = true;
                    if($deleted === true) {
                        $inserted = $this->eq->add_scrip_data($data);
                        /*echo '<pre>';
                        print_r($data);
                        echo '</pre>';*/
                        /*if(is_array($inserted)) {
                            $message = $inserted['code'].' - '.$inserted['message'];
                            echo $bc_date.' - '.$message.' \n<br/>';
                            continue;
                        } else {
                            echo 'Successfully inserted';
                            $imported = $this->common->last_import_for_null('Bhav Copy', $csv_file);
                            $success = true;
                            break;
                        }
                    } else {
                        echo $bc_date.' - Could not delete from scrips table. '.$deleted['code'].$deleted['message'].' \n<br/>';
                        continue;
                    }

                } else {
                    echo $bc_date.' - Error in reading CSV, quitting now. \n<br/>';
                }
            }
            echo '\n';
        }
    }

    function get_yesterday($date)
    {
        $temp = DateTime::createFromFormat('dmy',$date);
        $date = $temp->format('Y-m-d');
        $bc_date = date('dmy', strtotime($date.' - 1 days'));
        return $bc_date;
    }*/
    
    
    
    
    //updated code - Salmaan - 2017-11-30
    
    /**************************************************************************
     * *********** CRON JOB - Importing Bhav Copy from BSE India *************
     **************************************************************************/

    //function to be called from CRON JOB - Hosting
    function update_bhav_copies() {
        $this->update_bhav_copy_latest();
        $this->update_bhav_copy_nse_latest();
    }
    
    //function for BSE bhav copy
    function update_bhav_copy_latest()
    {
        echo "-------------------------------------------------------------<br/>Downloading latest available BSE Bhav Copy....<br/>-------------------------------------------------------------<br/>";
        //set the url from where to download
        $bc_url = 'http://www.bseindia.com/download/BhavCopy/Equity/';
        //get the date for which you want bhav_copy - start with todays date
        $bc_date = date('dmy');

        //run till we get the file properly
        $success = false;
        $cnt = 0;
        while($success == false && $cnt<5) {
            $cnt++;
            //make the filename to download
            $bc_filename = 'EQ'.$bc_date.'_CSV.ZIP';
            //now download the bhav copy from BSE website
            $filepath = $this->download_bhav_copy($bc_url,$bc_filename);
            //if it returns false, try again with one day before
            if(!$filepath) {
                echo $bc_date.' - Could not download bhav copy. <br/>';
                $bc_date = $this->get_yesterday($bc_date, 'dmy');
                continue;
            }

            //now we have to extract the zip
            $unzip = $this->extract_archive($filepath);
            if(!$unzip) {
                echo $bc_date.' - Could not open/extract zip file. <br/>';
                $bc_date = $this->get_yesterday($bc_date, 'dmy');
                continue;
            } else {
                $csv_file = 'EQ'.$bc_date.'.CSV';
                //now we will read csv file and put it into array
                $all_data = $this->read_csv('downloads/bhav_copy/'.$csv_file);
                if($all_data) {
                    //get data of only columns that we require
                    $data = array();
                    $temp_row = array();
                    foreach($all_data as $row) {
                        $temp_row['scrip_code'] = $row['SC_CODE'];
                        $temp_row['scrip_name'] = $row['SC_NAME'];
                        $temp_row['close_rate'] = $row['CLOSE'];
                        $data[] = $temp_row;
                    }
                    //delete and insert into scrip_rates table
                    //$deleted = $this->eq->delete_scrip_data();
                    $deleted = true;
                    if($deleted === true) {
                        $inserted = $this->eq->add_scrip_data($data);
                        /*echo '<pre>';
                        print_r($data);
                        echo '</pre>';*/
                        if(is_array($inserted)) {
                            $message = $inserted['code'].' - '.$inserted['message'];
                            echo $bc_date.' - '.$message.' \n<br/>';
                            continue;
                        } else {
                            echo 'Successfully inserted';
                            $imported = $this->common->last_import_for_null('Bhav Copy', $csv_file);
                            $success = true;
                            break;
                        }
                    } else {
                        echo $bc_date.' - Could not delete from scrips table. '.$deleted['code'].$deleted['message'].' \n<br/>';
                        continue;
                    }

                } else {
                    echo $bc_date.' - Error in reading CSV, quitting now. \n<br/>';
                }
            }
            echo '\n';
        }
    }
    
    //function for NSE bhav copy
    function update_bhav_copy_nse_latest()
    {
        echo "<br/><br/><br/><br/>-------------------------------------------------------------<br/>Downloading latest available NSE Bhav Copy....<br/>-------------------------------------------------------------<br/>";
        //set the url from where to download
        $bc_url = 'https://www.nseindia.com/content/historical/EQUITIES/';
        //2017/NOV/cm28NOV2017bhav.csv.zip
        //get the date for which you want bhav_copy - start with todays date
        $bc_date = strtoupper(date('dMY'));
        $bc_year = date('Y');
        $bc_monthname = strtoupper(date('M'));
        //add month and year to the url path
        $bc_url .= $bc_year.'/'.$bc_monthname.'/';
        
        //run till we get the file properly
        $success = false;
        $cnt = 0;
        while($success == false && $cnt<5) {
            $cnt++;
            //make the filename to download
            $bc_filename = 'cm'.$bc_date.'bhav.csv.zip';
            //now download the bhav copy from BSE website
            $filepath = $this->download_bhav_copy($bc_url,$bc_filename);
            //if it returns false, try again with one day before
            if(!$filepath) {
                echo $bc_date.' - Could not download bhav copy. <br/>';
                $bc_date = $this->get_yesterday($bc_date, 'dMY', true);
                continue;
            }

            //now we have to extract the zip
            $unzip = $this->extract_archive($filepath);
            if(!$unzip) {
                echo $bc_date.' - Could not open/extract zip file. <br/>';
                $bc_date = $this->get_yesterday($bc_date, 'dMY', true);
                continue;
            } else {
                $csv_file = 'cm'.$bc_date.'bhav.csv';
                //now we will read csv file and put it into array
                $all_data = $this->read_csv('downloads/bhav_copy/'.$csv_file);
                if($all_data) {
                    //var_dump($all_data); die();
                    //get data of only columns that we require
                    $data = array();
                    $temp_row = array();
                    foreach($all_data as $row) {
                        $temp_row['scrip_code'] = $row['SYMBOL'];
                        $temp_row['scrip_name'] = $row['SYMBOL'];
                        $temp_row['close_rate'] = $row['CLOSE'];
                        $data[] = $temp_row;
                    }
                    //delete and insert into scrip_rates table
                    //$deleted = $this->eq->delete_scrip_data();
                    $deleted = true;
                    if($deleted === true) {
                        $inserted = $this->eq->add_scrip_data($data);
                        /*echo '<pre>';
                        print_r($data);
                        echo '</pre>';*/
                        if(is_array($inserted)) {
                            $message = $inserted['code'].' - '.$inserted['message'];
                            echo $bc_date.' - '.$message.' \n<br/>';
                            continue;
                        } else {
                            echo 'Successfully inserted';
                            $imported = $this->common->last_import_for_null('Bhav Copy', $csv_file);
                            $success = true;
                            break;
                        }
                    } else {
                        echo $bc_date.' - Could not delete from scrips table. '.$deleted['code'].$deleted['message'].' \n<br/>';
                        continue;
                    }

                } else {
                    echo $bc_date.' - Error in reading CSV, quitting now. \n<br/>';
                }
            }
            echo '\n';
        }
    }

    function get_yesterday($date, $format, $upper = false)
    {
        $temp = DateTime::createFromFormat($format, $date);
        $date = $temp->format('Y-m-d');
        $bc_date = date($format, strtotime($date.' - 1 days'));
        if($upper) {
            $bc_date = strtoupper($bc_date);
        }
        return $bc_date;
    }
    

    function download_bhav_copy($path,$name)
    {
        $url = $path.$name;
        $local = "downloads/bhav_copy/";
        $file = $local.$name;
        //create folder if not exists
        if (!is_dir($local)) {
            mkdir($local, 0777, true);
        }
        $src = fopen($url, 'r');
        $dest = fopen($file, 'w');
        if($src && $dest) {
            echo 'File: '.$name.' - ';
            echo stream_copy_to_stream($src, $dest) . " bytes copied. \n<br/>";
            return $file;
        } else {
            echo 'Some issue with fopen() <br/>';
            return false;
        }
    }

    function extract_archive($file)
    {
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if($res === TRUE)
        {
            if($zip->extractTo("downloads/bhav_copy/")) {
                echo "<pre>";
                print_r($zip);//to get the file type
                echo "</pre>";
                echo 'File: '.$file.' \n<br/>';
                $zip->close();
                return true;
            } else {
                echo 'Unable to extract zip. \n<br/>';
                $zip->close();
                return false;
            }
        }
        else
        {
            echo 'Unable to open zip. \n<br/>';
            return false;
        }
    }

    function read_csv($file='', $delimiter=',') {
        if(!file_exists($file) || !is_readable($file)) {
            echo 'File does not exist - '.$file;
            return FALSE;
        }

        $header = NULL;
        $data = array();
        if (($handle = fopen($file, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header) {
                    $header = $row;
                }
                else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;

    }

    /****************************************************
     * ************* -- END  CRON JOB --  **************
     ****************************************************/
     
    function add_equity_history()
    {
    
        
       $rs=  $this->eq->add_equity_history();
       $rs=  $this->eq->add_client_brokers_history();
       
    
    }
	 
 }