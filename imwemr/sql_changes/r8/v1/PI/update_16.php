<?php
//  Update to remove duplicate address of patients 
$ignoreAuth = true;
set_time_limit(600);
include("../../../../config/globals.php");

$start_time = microtime(true);

$backup = array('patient_multi_address','patient_data');

$q = "SELECT GROUP_CONCAT(`mul`.id) AS 'duplicate_ids', `mul`.`patient_id` 
							FROM `patient_multi_address` `mul` 
							GROUP BY `mul`.`patient_id`, `mul`.`street`, `mul`.`street2`, `mul`.`postal_code`, `mul`.`zip_ext`, 
							`mul`.`city`, `mul`.`state`, `mul`.`country_code`, `mul`.`del_status`, `mul`.`county` 
							HAVING COUNT(`mul`.id) > 1";
$s = imw_query($q);
$c = imw_num_rows($s);

$results = 0;
if( $c ) {
	
	//Take Backup of tables  
	foreach($backup as $tblName) {
		$bkTbl = $tblName."_".date("d_m_Y");
		imw_query("CREATE TABLE ".$bkTbl." LIKE ".$tblName);
		imw_query("INSERT ".$bkTbl." SELECT * FROM ".$tblName."");
	}
	// Create array of default address from patient_data
	$defAdd = array();
	$qry = "Select id, default_address From patient_data Where default_address > 0 ";
	$sql = imw_query($qry);
	while($row = imw_fetch_object($sql)){
		$defAdd[$row->id] = $row->default_address;
	}
	
	$idsToRemove = array();
	// Start processing on Multiple addresses
	while( $r = imw_fetch_assoc($s) ) {
		$address_ids = $r['duplicate_ids'];
		$patient_id = $r['patient_id'];
		
		$defAddressID = isset($defAdd[$patient_id]) ? $defAdd[$patient_id] : '';
		
		$addressArr = array_filter(explode(",",$address_ids));
		sort($addressArr);
		
		$addressArr = array_combine($addressArr,$addressArr);
		
		//if( ($key = array_search($defAddressID, $addressArr)) !== false ) {
		if( array_key_exists($defAddressID, $addressArr) ) {
			unset($addressArr[$defAddressID]);
		}else {
			array_shift($addressArr);
		}
		
		$idsToRemove[] = implode(',',$addressArr);
				
		//$rQry = "Delete From patient_multi_address Where id IN (".$idsToRemove."); ";
		//$rSql = imw_query($rQry) or $msg_info[] = 'Error for patient ID - '.$patient_id.' -- '.imw_error() . ' -- ' . $rQry;
		//$rAff = imw_affected_rows();
		//$results[] = $rAff .' rows deleted for Patient ID - '.$patient_id ;
	}
	
	if( count($idsToRemove) > 0 && is_array($idsToRemove) ) { 
		$tmpStr = implode(",",$idsToRemove);
		$tmpArr = explode(",",$tmpStr);
		$tmpArr = array_chunk($tmpArr,1000);
		
		foreach($tmpArr as $ids ) {
			$tmpIDS = implode(",",$ids);
			$rQry = "Delete From patient_multi_address Where id IN (".$tmpIDS."); ";
			$rSql = imw_query($rQry) or $msg_info[] = imw_error() . ' -- ' . $rQry;
			$rAff = imw_affected_rows();
			$results += $rAff;
		}
	}	
	$results = $results .' Records Deleted';
}
else {
	$results = "No record found with duplicate address.";
}

$end_time = microtime(true);
$diff = $end_time - $start_time

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 16 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
		<font face="Arial, Helvetica, sans-serif" color="green" size="2">
				Process executed in <?php echo number_format($diff,2); ?> Second(s);
				<?php echo '<br>'.$results;?>
		</font>
		<font face="Arial, Helvetica, sans-serif" color="red" size="2">
				<?php echo(implode("<br>",$msg_info));?>
		</font>
</body>
</html>