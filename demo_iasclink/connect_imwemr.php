<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
//START PHP 7 ENVIRONMENT VARIABLES
$imw_db_name = 'demo_imw';
$imw_host = 'localhost';
$imw_port = '3306';
$imw_login = 'imw';
$imw_pass = 'P@ssw0rd1';
$sqlconf = array("host"=>$imw_host, "login"=>$imw_login, "pass"=>$imw_pass, "ascdb"=>$imw_db_name, "port"=>$imw_port);
include_once('common/db.php');
$link_imwemr = imw_connect($imw_host, $imw_login, $imw_pass,$imw_db_name,$imw_port) or die('Could not connect: ' . imw_error());
$GLOBALS['dbh'] = $link_imwemr;
imw_select_db($imw_db_name,$link_imwemr);// or die(imw_error().'Could not select database');
//END PHP 7 ENVIRONMENT VARIABLES


?>