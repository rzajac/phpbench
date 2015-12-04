<?php

define('UNIT_TEST_YOURAPPLICATION_TESTSUITE', 'yes');

error_reporting(E_ALL|E_STRICT);

// The project root folder
define('PROJECT_PATH', realpath(__DIR__.'/..'));

require_once PROJECT_PATH.'/vendor/autoload.php';
