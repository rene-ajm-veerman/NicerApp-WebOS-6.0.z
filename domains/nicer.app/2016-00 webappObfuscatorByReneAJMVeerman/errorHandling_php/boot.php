<?php 
require_once (dirname(__FILE__).'/functions__basicErrorHandling.php');
require_once (dirname(__FILE__).'/functions__internalErrorHandling.php');

error_reporting (E_ALL);
set_error_handler ('woBasicErrorHandler');
?>