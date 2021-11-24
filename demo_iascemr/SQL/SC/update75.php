<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="UPDATE icd10_data SET icd10 = 'H35.37-' WHERE icd10 ='H35.379-'"; 
imw_query($sql1)or $msg_info[] = imw_error();

imw_query("CREATE TABLE dischargesummarysheet_bak AS (SELECT * FROM dischargesummarysheet)")or $msg_info[] = imw_error();	

for($i=0;$i<=4;$i++){
	if($i==0){
		$match_code="H35.379-";
		$repl_code="H35.37-";
	}elseif($i>3){
		$match_code="H35.3799";
		$repl_code="H35.379";
	}else{
		$match_code="H35.379".$i;
		$repl_code="H35.37".$i;
	}

	$sql1 = "UPDATE dischargesummarysheet SET icd10_code = replace(icd10_code, '".$match_code."', '".$repl_code."') where icd10_code like '%$match_code%'";	
	$updtRes = imw_query($sql1) or $msg_info[] = imw_error();	

}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 75 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 75 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 75</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>