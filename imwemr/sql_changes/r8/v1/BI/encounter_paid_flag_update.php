<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$end = 1;

//---- UPDATE PATIENT CHARGE LIST FOR PATIENT DUE AND INSURANCE DUE -----

$qry = imw_query("select encounter_id from patient_charge_list order by encounter_id desc limit $start_val , $end");
$qryRes = imw_fetch_array($qry);
echo $encounter_id = $qryRes['encounter_id'];
updateEncounterFlags($encounter_id);
$msg_info[] = "<br><b>".($start_val + $end)." Records Updates encounter payment run Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Encounter Payment Paid Flag</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
	</form>
	<?php
	if(imw_num_rows($qry) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>