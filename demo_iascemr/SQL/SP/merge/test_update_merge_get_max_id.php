<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
set_time_limit(0);
include_once("../../../common/conDb.php");  //MYSQL CONNECTION
$scemrPatientExistTableNew = array();
$shTblRes = imw_query("show tables") or $msg_info[] = imw_error(); // run the query and assign the result to $result
while($shTblRow = imw_fetch_array($shTblRes)) { // go through each row that was returned in $result
    $scemrPatientExistTableNew[] = $shTblRow[0];
}

$colArr = array();
foreach($scemrPatientExistTableNew as $key => $tableName){
	$colTblRes = imw_query("SHOW FIELDS FROM ".$tableName." WHERE `Key` = 'PRI' ") or $msg_info[] = imw_error(); // run the query and assign the result to $result
	while($colTblRow = imw_fetch_array($colTblRes)) { // go through each row that was returned in $result
		$colArr[$tableName] = $colTblRow[0];
	}	
}
$maxColArr = array();
foreach($colArr as $tableNameNew => $colName){
	$maxRes = imw_query("SELECT MAX(".$colName.") AS ".$tableNameNew."_max_".$colName." FROM ".$tableNameNew) or $msg_info[] = imw_error();
	while($maxRow = imw_fetch_array($maxRes)) { // go through each row that was returned in $result
		$maxColArr[$tableNameNew] = $maxRow[0];
		//echo "<br>".$tableNameNew."_max_".$colName." = ".$maxRow[0];
	}
}
arsort($maxColArr);
foreach($maxColArr as $tableNameFinal => $colNameFinal){
	echo "<br>Max id of ".$tableNameFinal." = ".$colNameFinal;
}

$msg_info[] = "<br><br><b> Max Column Completed</b>";
?>
<html>
<head>
<title>Merge User Data </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>