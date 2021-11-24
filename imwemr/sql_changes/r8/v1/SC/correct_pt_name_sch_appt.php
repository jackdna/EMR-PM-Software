<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$startFrom = 0;
if((isset($_REQUEST['st']) == true) && (empty($_REQUEST['st']) == false)){
	$startFrom = $_REQUEST['st'];
}else{
	$startFrom = 0;
}

//end
$endTo = 1000;

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as totRec FROM patient_data";
	$rsGetTotRec = imw_query($getTotRec) or die(imw_error());
	if($rsGetTotRec){
		if(imw_num_rows($rsGetTotRec)>0){
			$rowGetTotRec = imw_fetch_array($rsGetTotRec);
			$totrec= $rowGetTotRec["totRec"];	
		}
	}
}
?>
<html>
	<head>
		<title>Correct patient name in schedule appointment table</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<style>
			.failureMsg{
				font-family:"verdana";
				font-size:10px; 
				font-weight:bold;
				color:FF0000;
			}
		</style>
	</head>
	<body>
<?php 
if($totrec == 0){
	?>
		<font face="Arial, Helvetica, sans-serif" size="2">
			<span align='center' class='failureMsg'>No appointments Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b>Names have been corrected added appointments.</b>
		</font>
	<?php	
}else{
	$msg = "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $msg;
	
	
	//get patient names from patient table
	$ptQ=imw_query("select CONCAT(lname,', ',fname,' ',mname) as pt_name, id from patient_data ORDER BY id LIMIT ".$startFrom.", ".$endTo);
	while($ptD=imw_fetch_assoc($ptQ))
	{
		imw_query("update schedule_appointments set sa_patient_name='".$ptD['pt_name']."' where sa_patient_id=".$ptD['id']);
			
	}
	
	?>
		<form action="" method="get" name="frmApptRecord">
			<input type="hidden" name="st" value="<?php echo intval($startFrom)+$endTo; ?>"/>			
			<input type="hidden" name="totRec" value="<?php echo intval($totrec); ?>"/>			
		</form>
		<script language="javascript">
			document.frmApptRecord.submit();
		</script>
	<?php
}
?>
	</body>
</html>