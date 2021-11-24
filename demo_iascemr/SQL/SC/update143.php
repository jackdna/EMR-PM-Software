<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql = array();
$sql[] = "ALTER TABLE `insurance_data` ADD `ins_in_house_code` VARCHAR( 255 ) NOT NULL AFTER `ins_provider` ";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

imw_query("CREATE TABLE insurance_data_bak_".date("d_m_Y")." LIKE insurance_data") or $msg_info[] = imw_error();
imw_query("INSERT INTO  insurance_data_bak_".date("d_m_Y")." (SELECT *  FROM insurance_data)") or $msg_info[] = imw_error();


include("../../connect_imwemr.php");
//START CODE TO GET DEFAULT CASE
$defaultCaseId	='';
$defaultCaseName='';
$defaultCaseTypeQry 	= "SELECT case_id FROM insurance_case_types WHERE normal = '1'";
$defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry)or die($defaultCaseTypeQry.imw_error());
if($defaultCaseTypeRes){
	if(imw_num_rows($defaultCaseTypeRes)>0){
		$defaultCaseTypeNumRow 	= imw_num_rows($defaultCaseTypeRes);
		$defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
		$defaultCaseId 		= $defaultCaseTypeRow['case_id'];
		$defaultCaseName 	= $defaultCaseTypeRow['case_name'];
	}
}
//END CODE TO GET DEFAULT CASE

//START CODE TO GET INSURANCE COMPANY NAME
$insProviderNameArr = $insInHouseCodeArr = array();
$insCompQry = "select id, name,in_house_code from insurance_companies order by id";
$insCompRes = imw_query($insCompQry);
if(imw_num_rows($insCompRes)) {
	while($insCompRow = imw_fetch_array($insCompRes)) {
		$insProviderId = $insCompRow["id"];
		$insProviderNameArr[$insProviderId] = $insCompRow["name"];
		$insInHouseCodeArr[$insProviderId] = $insCompRow["in_house_code"];
	}
}
//END CODE TO GET INSURANCE COMPANY NAME

$schAppQry			= "SELECT id, iolinkPatientWtId, iolink_iosync_waiting_id, iolinkPatientId FROM schedule_appointments WHERE (iolinkPatientWtId != '0' AND iolinkPatientWtId != '') OR (iolink_iosync_waiting_id != '0' AND iolink_iosync_waiting_id != '') ORDER BY id ";
$schAppRes 			= imw_query($schAppQry) or die($schAppQry.imw_error());		
				
if(imw_num_rows($schAppRes)>0) {
	while($schAppRow = imw_fetch_array($schAppRes)) {
		$schedule_id 		= $schAppRow["id"];
		$iolinkPatientId 	= $schAppRow["iolinkPatientId"];
		$iolinkPatientWtId 	= $schAppRow["iolinkPatientWtId"];
		if(!$iolinkPatientWtId) {
			$iolinkPatientWtId = $schAppRow["iolink_iosync_waiting_id"];	
		}
		$strQryGetPatientInsuranceData="SELECT insData.provider, insData.type 
										FROM schedule_appointments sa
										INNER JOIN insurance_data insData ON insData.pid = sa.sa_patient_id
										AND (
										insData.type = 'primary'
										OR insData.type = 'secondary'
										OR insData.type = 'tertiary'
										)
										AND insData.actInsComp = '1'
										INNER JOIN insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
										AND insCase.case_status = 'Open'
										AND insCase.ins_caseid = sa.case_type_id
										LEFT JOIN patient_auth ptAuth ON ptAuth.ins_data_id = insData.id
										AND ptAuth.patient_id = insData.pid
										WHERE sa.id = ".$schedule_id;
		
		$rsQryGetPatientInsuranceData = imw_query($strQryGetPatientInsuranceData) or die($strQryGetPatientInsuranceData.imw_error());
		if(imw_num_rows($rsQryGetPatientInsuranceData)<=0){	
			//GET RECORD FROM DEFAULT CASE
			$strQryGetPatientInsuranceData="SELECT insData.provider, insData.type 
											FROM schedule_appointments sa
											INNER JOIN insurance_data insData ON insData.pid = sa.sa_patient_id
											AND (
											insData.type = 'primary'
											OR insData.type = 'secondary'
											OR insData.type = 'tertiary'
											)
											AND insData.actInsComp = '1'
											INNER JOIN insurance_case insCase ON insCase.ins_caseid = insData.ins_caseid
											AND insCase.case_status = 'Open'
											AND insCase.ins_case_type = '".$defaultCaseId."'
											LEFT JOIN patient_auth ptAuth ON ptAuth.ins_data_id = insData.id
											AND ptAuth.patient_id = insData.pid
											WHERE sa.id = ".$schedule_id;
			
			$rsQryGetPatientInsuranceData = imw_query($strQryGetPatientInsuranceData) or die($strQryGetPatientInsuranceData.imw_error());
		}
		if(!$rsQryGetPatientInsuranceData){
			echo ("<br>Error : ". imw_error()."<br>".$strQryGetPatientInsuranceData);
		}
		else{//echo '<br><br>'.$strQryGetPatientInsuranceData;
			if(imw_num_rows($rsQryGetPatientInsuranceData)>0){		
				while ($row = imw_fetch_array($rsQryGetPatientInsuranceData)) {
					$provider = $row['provider'];
					$type = $row['type'];
					if($insProviderNameArr[$provider]) {
						$insProviderName = $insProviderNameArr[$provider];
						$inHouseCode = $insInHouseCodeArr[$provider];
						include("../../common/conDb.php");
						$andUpdtQry = " AND ins_provider = '".addslashes($insProviderName)."' ";
						$updateQry = "UPDATE insurance_data SET ins_in_house_code = '".addslashes($inHouseCode)."'
									  WHERE ins_in_house_code = '' AND type = '".$type."'
									  AND patient_id = '".$iolinkPatientId."' AND waiting_id = '".$iolinkPatientWtId."' ";
						$updateQryNew = $updateQry.$andUpdtQry;
						//echo '<br>'.$updateQryNew;
						$updateResNew = imw_query($updateQryNew) or die($updateQryNew.imw_error());
						if(imw_affected_rows() <= 0) {
							//echo '<br>'.$updateQry;
							$updateRes = imw_query($updateQry) or die($updateQry.imw_error());
						}
						include("../../connect_imwemr.php");
					}
				}
			}
		}
	}
}
$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 143 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 143 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 143</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>