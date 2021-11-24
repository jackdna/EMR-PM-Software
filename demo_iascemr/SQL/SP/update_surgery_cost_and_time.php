<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;

$start_date='2016-12-01';
if(!$_REQUEST['total_rec'])
{
	
	$curDate=date("d_m_Y");
	$pt_data="patientconfirmation_X".$curDate;
	imw_query("CREATE TABLE ".$pt_data." AS (SELECT * FROM patientconfirmation)")or $msg_info[] = imw_error();
	$sg_data="surgery_cost_X".$curDate;
	imw_query("CREATE TABLE ".$sg_data." AS (SELECT * FROM surgery_cost)")or $msg_info[] = imw_error();
	$vs_data="vision_success_X".$curDate;
	imw_query("CREATE TABLE ".$vs_data." AS (SELECT * FROM vision_success)")or $msg_info[] = imw_error();
	//clear existing data
	imw_query("TRUNCATE TABLE  `surgery_cost`");

	$q=imw_query("select patientConfirmationId from patientconfirmation where finalize_status='true' and dos>='$start_date'");
	$total_records=imw_num_rows($q);	
}
else
{
	$total_records=$_REQUEST['total_rec'];	
}
$limit=($_REQUEST['limit'])?$_REQUEST['limit']:'0';


//get records from 1st july
$q=imw_query("select patientConfirmationId from patientconfirmation where finalize_status='true' and dos>='$start_date' LIMIT $limit,100");

while($data=imw_fetch_object($q))
{
	$objManageData->calculateCost($data->patientConfirmationId);
	$limit++;
}

// To update cost_procedure_id into vision suvvess table
$q = imw_query("UPDATE vision_success,patientconfirmation SET vision_success.procedure = patientconfirmation.cost_procedure_id Where patientconfirmation.patientConfirmationId = vision_success.confirmation_id");

if($limit>=$total_records)
{
	$msg_info[] = "<br><br><b>Update cost and time Successful. $limit records updated.</b><br>".$message;
	$color = "green";			
}
else
{
	echo"$limit/$total_records Records Updated.
		<script>window.location.href=\"update_surgery_cost_and_time.php?total_rec=$total_records&limit=$limit\";</script>";
}

?>

<html>
<head>
<title>Update surgery cost and time</title>
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