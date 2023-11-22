<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class All_combine extends CI_Controller{

    function index()
    {
        require_once('/home/threetense/public_html/application/controllers/Auto_import_cams.php'); //include cams controller
        require_once('/home/threetense/public_html/application/controllers/Auto_import_karvy.php'); //include karvy controller
        require_once('/home/threetense/public_html/application/controllers/MF_scripts.php'); //include Mf_script controller
        
        //try {
            //Cams objects 
            $camAuto = new Auto_import_cams();  //create object 
            $camAuto->index(); //call function
            //karvy objects
            $karvyAuto = new Auto_import_karvy();  //create object 
            $karvyAuto->karvy_auto_import(); //call function
        //}
        //catch(Exception $e) {
        //    echo "Exception in auto-import";
        //    print_r($e);
        //}
        //finally {
            //MF_Scripts  objects
            /*$mf_scriptAuto = new MF_scripts();  //create object 
            $mf_scriptAuto->update_nav(); //call function for NAV auto-update
            $mf_scriptAuto->auto_mf_valuation(); //call function for auto-valuation*/
        //}
        //MF_Scripts  objects
            $mf_scriptAuto = new MF_scripts();  //create object 
            $mf_scriptAuto->update_nav(); //call function for NAV auto-update
            $mf_scriptAuto->auto_mf_valuation(); //call function for auto-valuation

    }

}//class end
?>
