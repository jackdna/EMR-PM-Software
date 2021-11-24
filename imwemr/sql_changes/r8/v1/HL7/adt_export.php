<?php
$ignoreAuth = true;
set_time_limit(0);

require_once("../../../../config/globals.php");
if(constant('HL7_ADT_GENERATION_OLD')!=true){die('ADT Generation not enabled.');}
require_once("../../../../hl7sys/old/CLS_makeHL7.php");
set_time_limit(0);
$st = isset($_GET['st']) ? intval($_GET['st']) : 0;
$upto = 100;

$msg_info=array();

$q	=	"SELECT id FROM patient_data WHERE patientStatus = 'Active'	ORDER BY id LIMIT $st,$upto";
$res = imw_query($q);

$rs_opr = false;
$q_opr = "SELECT id,username FROM users WHERE LOWER(fname) = 'hl7' AND LOWER(lname) = 'hl7' LIMIT 0,1";
$res_opr = imw_query($q_opr);
if($res_opr && imw_num_rows($res_opr)==1){
	$rs_opr = imw_fetch_assoc($res_opr);
//	 $this->operator_id = $rs['id'];
//	 $this->operator_username = $rs['username'];
}
if($res && imw_num_rows($res)>0){
	$makeHL7		= new makeHL7;
	while($rs = imw_fetch_assoc($res)){
		$pat_id 		= $rs['id'];
		//------------ADT---------(start)---------------
		$makeHL7->patient_id = $pat_id;
		if($rs_opr){
			$makeHL7->authId 	 = $rs_opr['id'];
		}else{
			$makeHL7->authId 	 = 1;
		}
		$makeHL7->log_HL7_message($pat_id,'Update_Patient');
			
	}
	echo 'Process End';
}else{
	$msg_info[] = 'NO record found for given conditions.';
}

?><script type="text/javascript">
window.onload = function(){
	alert('done');
	//window.location.href = 'adt_export.php?st=<?php echo ($st+$upto);?>'
	
}
</script>
