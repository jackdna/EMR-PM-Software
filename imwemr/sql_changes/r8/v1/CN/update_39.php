<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);






$q = array();
$q[] = 'ALTER TABLE `chart_exam_ext` ADD `grade` TEXT NOT NULL AFTER `full_obsrv`, ADD `location` TEXT NOT NULL AFTER `grade`, ADD `comments` INT(1) NOT NULL AFTER `location`';
  
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 39 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 39 completed successfully. </b>";
	$color = "green";
	
	
}
?>
<html>
<head>
<title>Update 39</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>