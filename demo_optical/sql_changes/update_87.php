<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="CREATE TABLE  `in_print_header` (
`id` INT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`pid` INT( 4 ) NOT NULL ,
`label` VARCHAR( 50 ) NOT NULL ,
`value` TEXT NOT NULL
) ENGINE = MYISAM ;";

$sql[]="INSERT INTO  `in_print_header` (
`id` ,
`pid` ,
`label` ,
`value`
)
VALUES (
'1001',  '0',  'Master Header',  ''
), (
'1002',  '0',  'Master Footer',  ''
), (
NULL ,  '1',  'Header',  ''
), (
NULL ,  '1',  'Footer',  ''
), (
NULL ,  '3',  'Header',  ''
), (
NULL ,  '3',  'Footer',  ''
);";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 86 run successfully...</div>';	
}

?>