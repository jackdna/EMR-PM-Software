<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	include('connect_sqlserver.php'); 

	$sql1="select name from syscolumns where id=object_id('appointments')";
	$sql2="select name from syscolumns where id=object_id('provider_mstr')";
	$sql3="select name from syscolumns where id=object_id('person')";
	$sql4="select name from syscolumns where id=object_id('events')";
	$sql5="select name from syscolumns where id=object_id('practice')";

	$rs1 = $db->Execute($sql1);
	$rs2 = $db->Execute($sql2);
	$rs3 = $db->Execute($sql3);
	$rs4 = $db->Execute($sql4);
	$rs5 = $db->Execute($sql5);

	print '<pre>';
	echo "<font color = 'red'>TABLE NAME = appointments</font><br><br>";
	print_r($rs1);
	
	echo "<font color = 'red'><br><br><br>TABLE NAME = provider_mstr</font><br><br>";
	print_r($rs2);
	echo "<font color = 'red'><br><br><br>TABLE NAME = person</font><br><br>";
	print_r($rs3);
	echo "<font color = 'red'><br><br><br>TABLE NAME = events</font><br><br>";
	print_r($rs4);
	echo "<font color = 'red'><br><br><br>TABLE NAME = practice</font><br><br>";
	print_r($rs5);
	
	$db->close();
	
?>