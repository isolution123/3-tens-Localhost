<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Custom_exception extends Exception{
    function errorMessage($code){
        $errorMsg = '';
        //in case of foreign key deletion
        if($code == 1451)
        {
            $errorMsg = 'You cannot delete this record as it is referenced/used by an other module';
        }
        //in case of addition or updating foreign key reference
        else if($code == 1452)
        {
            $errorMsg = 'You cannot add/update this record as it is referenced/used by an other module';
        }
        //in case of primary key violation
        else if($code == 1062)
        {
            $errorMsg = 'You cannot add/update this record as all records must be unique for this module';
        }
        //in case of null is entered as primary key value
        else if($code == 1171)
        {
            $errorMsg = 'You cannot add null/empty value for this record as it is not allowed';
        }
        else
        {
            $errorMsg = $this->getMessage();
        }
        return $errorMsg;
    }
} 