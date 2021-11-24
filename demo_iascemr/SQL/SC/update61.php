<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$curDate=date("d_m_Y");
$icd10_data="icd10_data_X".$curDate;

//create a copy of a table
imw_query("CREATE TABLE ".$icd10_data." AS (SELECT * FROM icd10_data)")or $msg_info[] = imw_error();

//get list of ICD9 codes
$cid9Query=imw_query("SELECT diag_code FROM  `diagnosis_tbl` where diag_code!=''")or $msg_info[] = imw_error();
while($icd9data=imw_fetch_object($cid9Query))
{
	list($code,$descr)=explode(', ',$icd9data->diag_code);
	$Icd9[]=$code;	
}
//$cid9Query.close;
$icd9str=implode("','",$Icd9);
//delete icd10 code data that do not have existing icd9 codes
imw_query("delete from icd10_data where icd9 NOT IN('$icd9str')")or $msg_info[] = imw_error();
imw_query("delete from icd10_data where icd9 =''")or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 61 run OK";

?>

<html>
<head>
<title>Update 61</title>
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







