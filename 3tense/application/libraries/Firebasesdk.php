<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/firebase-php-5.x/src/Firebase/Factory";

class Firebasesdk extends Factory{
    public function __construct()
    {
        parent::__construct();
    }
} 