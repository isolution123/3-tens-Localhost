<?php
class Soap_client extends CI_controller {

          function __construct() {
                parent::__construct();

               $this->load->library(“Nusoap_lib”);
              $this->load->helper(“url”);

         }

         function index() {

               $this->soapclient = new soapclient(site_url(‘Bills_WS/index/wsdl’), true);

              $err = $this->soapclient->getError();
             if ($err) {
                   echo ‘<h2>Constructor error</h2><pre>’ . $err . ‘</pre>’;

             }

            $result = $this->soapclient->call(‘hello’, array(‘name’ => ‘Scott’));
           // Check for a fault
           if ($this->soapclient->fault) {
                   echo ‘<h2>Fault</h2><pre>’;
                   print_r($result);
                  echo ‘</pre>’;
            } else {
                // Check for errors
                $err = $this->soapclient->getError();
                if ($err) {
                          // Display the error
                      echo ‘<h2>Error</h2><pre>’ . $err . ‘</pre>’;
                } else {
                        // Display the result
                    echo ‘<h2>Result</h2><pre>’;
                   print_r($result);
                   echo ‘</pre>’;
              }
        }
  }

}
?>