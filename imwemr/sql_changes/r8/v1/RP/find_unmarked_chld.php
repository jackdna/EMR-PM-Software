<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();

$q1 = "SELECT `Batch_file_submitte_id` , delete_status, `pcld_id`, `Interchange_control`, `create_date` FROM `batch_file_submitte` WHERE `delete_status` != '1' ORDER BY `batch_file_submitte`.`Batch_file_submitte_id` DESC";
$res1 = imw_query($q1);
$tabular_data = array();
if($res1 && imw_num_rows($res1)>0){
	while($rs1 = imw_fetch_assoc($res1)){
		$interchange_control 	= $rs1['Interchange_control']; 
		$pcld_ids				= $rs1['pcld_id']; 
		$create_date			= $rs1['create_date'];
		$q2 = "SELECT GROUP_CONCAT(charge_list_detail_id) AS pcld_ids FROM patient_charge_list_details WHERE charge_list_detail_id IN ($pcld_ids) AND claim_status != '1' AND posted_status = '1' AND del_status='0' GROUP BY (claim_status)";
		$res2 = imw_query($q2);
		if($res2 && imw_num_rows($res2)>0){
			$rs2 = imw_fetch_assoc($res2);
			$pcld_ids = $rs2['pcld_ids'];
			if(isset($_GET['correct']) && $_GET['correct']=='1'){
				check_pcld_and_correct($pcld_ids,$create_date);
			}
			$tabular_data[$interchange_control] = array('created_on'=>$create_date,'pcld'=>$pcld_ids);
			
		}
	}
}

function check_pcld_and_correct($pcld,$create_date){
	/*$q = "SELECT DISTINCT(pcl.encounter_id) AS encounter_id FROM patient_charge_list pcl 
			JOIN patient_charge_list_details pcld ON (pcl.charge_list_id = pcld.charge_list_id) 
	 		WHERE pcld.charge_list_detail_id IN ($pcld) AND pcl.void_notify = '0' AND pcl.del_status='0' AND pcl.primarySubmit='1'";*/
	$q = "SELECT charge_list_detail_ids AS pcld_id FROM posted_record WHERE charge_list_detail_ids IN ($pcld) AND posted_date > '$create_date'";
	$res = imw_query($q);
	$temp_pcld_arr = array();
	if($res && imw_num_rows($res)>0){
		while($rs = imw_fetch_assoc($res)){
			$temp_pcld_arr[] = $rs['pcld_id'];
		}
	}
	
	$main_pcld_id_arr = explode(',',$pcld);
	$remain_pcld_id_arr = array_diff($main_pcld_id_arr,$temp_pcld_arr);
	$remain_pcld_id_str = implode(',',$remain_pcld_id_arr);
	imw_query("UPDATE patient_charge_list_details SET claim_status='1' WHERE charge_list_detail_id IN ($remain_pcld_id_str) AND claim_status='0'");
}
?>
<html>
<head>
<title>Release 8 Special Update</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table cellpadding="2" cellspacing="0" border="1">
<?php
foreach($tabular_data as $ic=>$data){
	echo '<tr>';
	echo '	<td>';
	echo '<b>'.$ic.'</b> :: '.$data['created_on'].'<br>';
	echo $data['pcld'];
	echo '	</td>';
	echo '</tr>';
}

?>
</table>
</body>
</html>