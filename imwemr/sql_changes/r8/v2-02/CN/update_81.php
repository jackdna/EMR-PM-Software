<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "CREATE TABLE `testing` (
  `id` int(10) NOT NULL,
  `testing_nm` varchar(255) NOT NULL,
  `del_by` int(10) NOT NULL,
  `del_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Testing drop down options in work view';";

$s[] = "ALTER TABLE `testing`
  ADD PRIMARY KEY (`id`);";
  
$s[] = "ALTER TABLE `testing`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;";  

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

//insert--

if(count($msg_info)<=0){
	$s = "INSERT INTO testing(id, testing_nm) VALUES ('1','Color Plates'),('2','Disc Photo'),('3','Empty'),('4','Gonio'),('5','NFA/HRT'),('6','Pachy'),('7','VF')";
	$result = imw_query($s);
}



?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 81</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>