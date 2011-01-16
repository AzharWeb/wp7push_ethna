<?php
/**
 *  {$action_name}.php
 *
 *  @author     {$author}
 *  @package    Mpnstest
 *  @version    $Id$
 */
chdir(dirname(__FILE__));
require_once '{$dir_app}/Mpnstest_Controller.php';

ini_set('max_execution_time', 0);

Mpnstest_Controller::main_CLI('Mpnstest_Controller', '{$action_name}');
?>
