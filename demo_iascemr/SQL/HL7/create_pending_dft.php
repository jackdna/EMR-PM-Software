<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

include_once("../../common/conDb.php");
if(!in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman'))){die('This update is not for this server.');}

$st = isset($_GET['st']) ? intval($_GET['st']) : 0;

	
$q = "SELECT DISTINCT(patient_confirmation_id) 
	FROM `finalize_history` 
	WHERE `finalize_action_script` LIKE 'auto'
	AND `finalize_action_datetime` >= '2016-09-01 00:00:00'
	ORDER BY finalize_history_id 
	LIMIT $st,1";
error_reporting(-1);
ini_set("display_errors",-1);
$res = imw_query($q);
if($res && imw_num_rows($res)>0){
	$rs = imw_fetch_assoc($res);
	$pConfId = $rs['patient_confirmation_id'];
	/*******HL7- DFT GENERATION***********/
	if(constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman'))){
		include_once(dirname(__FILE__)."/../../dft_hl7_generate.php");
		
	}
	/*******DFT GENERATION END************/
}else{
	die(imw_error().'<br>All pending DFT created.');
}

//if($st==2) die('test done');	
?>
<script type="text/javascript">

window.location.href = 'create_pending_dft.php?st=<?php echo ($st+1);?>';

</script>
