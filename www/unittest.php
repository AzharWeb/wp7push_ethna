<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../app/Mpnstest_Controller.php';

Mpnstest_Controller::main('Mpnstest_Controller', array(
    '__ethna_unittest__',
    )
);
?>
