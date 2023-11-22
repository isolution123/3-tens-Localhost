<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ini_set("allow_url_fopen", 1);

class Commodity_scripts extends CI_Controller {



    function __construct()

    {

        parent::__construct();



        //load model reminder_model rem is the object

        $this->load->model('Commodities_model', 'comm');

        $this->load->model('Common_model', 'common');

        $this->load->library('Mail', 'mail');

    }

	

	

	/**************************************************************************

     * *********** CRON JOB - Importing Bhav Copy from BSE India *************

     **************************************************************************/



    //function to be called from CRON JOB - Hosting

    function get_rates()

    {

        //set the url from where to get rate

        $bc_url = 'http://ibjarates.com/ibjaratesservices/Service1.svc/';



        //run till we get the file properly - for Gold

        $success_gold = false;

        $cnt_gold = 0;

        while($success_gold == false && $cnt_gold<5) {

            $cnt_gold++;

            //now get the data from IBJA url for Gold

            $gold_data = @file_get_contents($bc_url.'goldrate');

            //if it returns false, try again with one day before

            if($gold_data === false) {

                echo 'Gold - Could not fetch file contents from url <br/>\n';

                continue;

            }



            //now we have to read the json

            $gold_arr = json_decode($gold_data);

            //var_dump($gold_arr);

            $count_gold = count((array)$gold_arr);

            if($count_gold == 0 || empty($gold_arr)) {

                echo 'Gold - Empty array <br/>\n';

                continue;

            } elseif($count_gold <= 3) {

                $updated = $this->update_rates('Gold',$gold_arr);

                if($updated && !is_array($updated)) {

                    echo '-------------- Gold - Successfully updated rates!!! ------------ <br/>\n';

                } else {

                    if(isset($updated['code']) && $updated['code'] == 0 && isset($updated['message']) && $updated['message'] == "") {

                        echo 'Gold - Error while updating, most probably because the same rates are getting updated <br/>\n';

                        break;

                    } else {

                        echo 'Gold - Error while updating <br/>\n';

                        var_dump($updated); echo '<br/>\n';

                        continue;

                    }

                }

            } else {

                $new_gold_arr = array();

                $new_gold_arr_6 = array();

                //get rate of only 6PM, not 12AM as 6PM is latest

                foreach($gold_arr as $row) {

                    $goldRate = $row->GoldRate;

                    if(empty($goldRate)) {

                        continue;

                    } else {

                        if($row->RateTime == '6PM') {

                            $new_gold_arr_6[] = $row;

                        } else {

                            $new_gold_arr[] = $row;

                        }

                    }

                }

                if(count($new_gold_arr_6) == 0) {

                    echo 'Gold - Issue with gold array 6PM (rates blank), switching to 12AM. See below 12AM array <br/>\n';

                    var_dump($new_gold_arr); echo '<br/>\n';

                    //continue;

                } else {

                    $new_gold_arr = $new_gold_arr_6;

                }



                if(count($new_gold_arr) == 0) {

                    echo 'Gold - Issue with gold array, new array is empty. See below <br/>\n';

                    var_dump($gold_arr);

                    continue;

                } else {

                    $updated = $this->update_rates('Gold',$new_gold_arr);

                    if($updated && !is_array($updated)) {

                        echo '-------------- Gold - Successfully updated rates!!! ------------ <br/>\n';

                    } else {

                        if(isset($updated['code']) && $updated['code'] == 0 && isset($updated['message']) && $updated['message'] == "") {

                            echo 'Gold - Error while updating, most probably because the same rates are getting updated <br/>\n';

                            break;

                        } else {

                            echo 'Gold - Error while updating <br/>\n';

                            var_dump($updated); echo '<br/>\n';

                            continue;

                        }

                    }

                    break;

                }

            }

        }



        echo "<br/></br>\n\n";



        //run till we get the file properly - for Silver

        $success_silver = false;

        $cnt_silver = 0;

        while($success_silver == false && $cnt_silver<5) {

            $cnt_silver++;

            //now get the data from IBJA url for silver

            $silver_data = @file_get_contents($bc_url.'silverrate');

            //if it returns false, try again with one day before

            if($silver_data === false) {

                echo 'Silver - Could not fetch file contents from url <br/>\n';

                continue;

            }



            //now we have to read the json

            $silver_arr = json_decode($silver_data);

            //var_dump($silver_arr);

            $count_silver = count((array)$silver_arr);

            if($count_silver == 0 || empty($silver_arr)) {

                echo 'Silver - Empty array <br/>\n';

                continue;

            } elseif($count_silver == 1) {

                $updated = $this->update_rates('Silver',$silver_arr);

                if($updated && !is_array($updated)) {

                    echo '-------------- Silver - Successfully updated rates!!! ------------ <br/>\n';

                    break;

                } else {

                    if(isset($updated['code']) && $updated['code'] == 0 && isset($updated['message']) && $updated['message'] == "") {

                        echo 'Silver - Error while updating, most probably because the same rates are getting updated <br/>\n';

                        break;

                    } else {

                        echo 'Silver - Error while updating <br/>\n';

                        var_dump($updated); echo '<br/>\n';

                        continue;

                    }

                }

            } else {

                $new_silver_arr = array();

                $new_silver_arr_6 = array();

                //get rate of only 6PM, not 12AM as 6PM is latest

                foreach($silver_arr as $row) {

                    $silverRate = $row->SilverRate;

                    if(empty($silverRate)) {

                        continue;

                    } else {

                        if($row->RateTime == '6PM') {

                            $new_silver_arr_6[] = $row;

                        } else {

                            $new_silver_arr[] = $row;

                        }

                    }

                }

                if(count($new_silver_arr_6) == 0) {

                    echo 'Silver - Issue with silver array 6PM (rates blank), switching to 12AM. See below 12AM array <br/>\n';

                    var_dump($new_silver_arr); echo '<br/>\n';

                    //continue;

                } else {

                    $new_silver_arr = $new_silver_arr_6;

                }



                if(count($new_silver_arr) == 0) {

                    echo 'Silver - Issue with silver array, new array is empty. See below <br/>\n';

                    var_dump($silver_arr);

                    continue;

                } else {

                    $updated = $this->update_rates('Silver',$new_silver_arr);

                    if($updated && !is_array($updated)) {

                        echo '-------------- Silver - Successfully updated rates!!! ------------ <br/>\n';

                    } else {

                        if(isset($updated['code']) && $updated['code'] == 0 && isset($updated['message']) && $updated['message'] == "") {

                            echo 'Silver - Error while updating, most probably because the same rates are getting updated <br/>\n';

                            break;

                        } else {

                            echo 'Silver - Error while updating <br/>\n';

                            var_dump($updated); echo '<br/>\n';

                            continue;

                        }

                    }

                    break;

                }

            }

        }

    }



    function update_rates($type,$data) {

        //fetch Admin-defined commodities and rates

        if($type == 'Gold') {

            $condition = " AND cu.unit_name = 'Tola (10 GMS)'";

        } elseif($type == 'Silver') {

            $condition = " AND cu.unit_name = 'KG'";

        } else {

            $condition = "";

        }

        $comm_rates = $this->comm->get_commodity_rates_list("ci.item_name LIKE '%".$type."%' AND cr.broker_id IS NULL".$condition);

        $upd_arr = array();

        //var_dump($comm_rates);

        //var_dump($data);

        foreach($comm_rates as $rate) {

            $purity = preg_replace("/[^0-9]/","",$rate->item_name);

            foreach($data as $row) {

                if(intval($purity) == intval($row->Purity)) {

                    $arr['broker_id'] = null;

                    $arr['commodity_rate_id'] = $rate->commodity_rate_id;

                    if($type == 'Gold')

                        $arr['current_rate'] = floatval($row->GoldRate);

                    elseif($type == 'Silver')

                        $arr['current_rate'] = floatval($row->SilverRate);



                    //add this array to main update_array

                    $upd_arr[] = $arr;

                }

            }

        }

        //var_dump($upd_arr);



        //now update commodity rates

        $updated = $this->comm->update_commodity_rates_script($upd_arr,'commodity_rate_id');

        if($updated === true) {

            return true;

        } elseif(is_array($updated)) {

            return $updated;

        } else {

            return false;

        }

    }



    /****************************************************

     * ************* -- END  CRON JOB --  **************

     ****************************************************/

	 

 }