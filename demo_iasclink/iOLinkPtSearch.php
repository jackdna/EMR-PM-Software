<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
$loginUser = $_SESSION['iolink_loginUserId'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<?php
$spec= "
</head>
<body>";
include_once("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("common/iOLinkCommonFunction.php");

$practiceName = getPracticeName($loginUser,'Coordinator');
$coordinatorType = getCoordinatorType($loginUser);
$usrType = getUserType($loginUser);
$txt_iolink_pt_search = trim($_REQUEST['txt_iolink_pt_search']);
$ptrnDate = '/^((0[1-9])|(1[012]))[-\/](0[1-9]|[12][0-9]|3[01])[-\/]((18|19|20|21)?[0-9]{2})$/';
$ptrnPhone = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'; 
$srchByDob=false;
$srchByPh=false;
$genQry="";
$dobQry="";
$qrySrch=" pdt.patient_id 	= '".$txt_iolink_pt_search."' ";
$facQry = "";
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$facQry = " AND piwt.iasc_facility_id IN (".$_SESSION['iolink_iasc_facility_id'].") ";	
}
if(strstr($txt_iolink_pt_search,";")){
	$explodeSrch=$patGen="";
	list($explodeSrch,$patGen)=explode(";",$txt_iolink_pt_search);
	$gender="";
	if(strtolower(trim($patGen))=="m"){
		$gender="m";	
	}
	if(strtolower(trim($patGen))=="f"){
		$gender="f";	
	}
	if($gender){
		$genQry=" AND pdt.sex='".$gender."' ";
	}
	$txt_iolink_pt_search=trim($explodeSrch);
}
if(preg_match($ptrnDate,$txt_iolink_pt_search) && strstr($txt_iolink_pt_search,"-")){
	$patDOB="";
	$patDOB=$objManageData->changeDateYMD($txt_iolink_pt_search);
	$srchByDob=true;
	$qrySrch=" pdt.date_of_birth	= '".$patDOB."' ";	
}
$patPhno="";
if(preg_match($ptrnPhone,$txt_iolink_pt_search) && strstr($txt_iolink_pt_search,"-")){
	$srchByPh=true;
	$patPhno=str_replace("-","",$txt_iolink_pt_search);
	$qrySrch=" (pdt.homePhone= '".$patPhno."' || pdt.homePhone= '".$txt_iolink_pt_search."') ";	
}
//if($practiceName){
	$strQuery1Part=getPracticeUser($practiceName,"","usr");
	$strQuery1Part.= ($strQuery1Part ? " And" : "" ). " usr.deleteStatus <> 'Yes'"; 
//}
if($txt_iolink_pt_search <>"") {
	//START SET DEFAULT QRY FOR ZERO RESULT
	$getPatientSearchQry = "SELECT * FROM patient_data_tbl WHERE patient_id = '0'"; 
	//END SET DEFAULT QRY FOR ZERO RESULT
	if(is_numeric($txt_iolink_pt_search) || $srchByPh==true || $srchByDob==true){
		if($coordinatorType=='Practice' || $usrType=='Surgeon') {
			$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id, piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
											INNER JOIN users usr ON (".$strQuery1Part.")
											INNER JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id = pdt.patient_id 
																					
																					AND piwt.surgeon_fname	= usr.fname 
																					AND piwt.surgeon_mname	= usr.mname 
																					AND piwt.surgeon_lname	= usr.lname
																					".$facQry."
																					) 
										WHERE $qrySrch $genQry 
										ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							
		
		}else if($coordinatorType=='Master') {
			$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id,piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
											LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id =	pdt.patient_id ".$facQry.") 
										WHERE $qrySrch $genQry
										ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							
		
		}
	}else{
		if(strpos($txt_iolink_pt_search, ',')){
			$searchKeywordArr = explode(",", $txt_iolink_pt_search);
			$patientLastName = trim($searchKeywordArr[0]);
			$patientFirstName = trim($searchKeywordArr[1]);
			
			if($coordinatorType=='Practice' || $usrType=='Surgeon') {
				$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id,piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
												INNER JOIN users usr ON (".$strQuery1Part.")
												INNER JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id = pdt.patient_id 
																						
																						AND piwt.surgeon_fname	= usr.fname 
																						AND piwt.surgeon_mname	= usr.mname 
																						AND piwt.surgeon_lname	= usr.lname
																						".$facQry."
																						) 
											WHERE pdt.patient_fname LIKE '$patientFirstName%' 
												AND pdt.patient_lname LIKE '$patientLastName%' $genQry 
											ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							
			
			}else if($coordinatorType=='Master') {
				$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id,piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
												LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id =	pdt.patient_id ".$facQry.") 
											WHERE pdt.patient_fname LIKE '$patientFirstName%' 
												AND pdt.patient_lname LIKE '$patientLastName%' $genQry 
											ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							
			
			}
		}else{
			if($coordinatorType=='Practice' || $usrType=='Surgeon') {
				$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id,piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
												INNER JOIN users usr ON (".$strQuery1Part.")
												INNER JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id = pdt.patient_id 
																						
																						AND piwt.surgeon_fname	= usr.fname 
																						AND piwt.surgeon_mname	= usr.mname 
																						AND piwt.surgeon_lname	= usr.lname
																						".$facQry."
																						) 
											WHERE (pdt.patient_fname LIKE '$txt_iolink_pt_search%' 
												OR pdt.patient_lname LIKE '$txt_iolink_pt_search%') $genQry 
											ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							

			}else if($coordinatorType=='Master') {
				$getPatientSearchQry 	 = "SELECT piwt.patient_in_waiting_id,piwt.patient_status, DATE_FORMAT(piwt.dos,'%m-%d-%y') as showDos, pdt.* FROM patient_data_tbl pdt		
												LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_id =	pdt.patient_id ".$facQry.") 
											WHERE (pdt.patient_fname LIKE '$txt_iolink_pt_search%' 
												OR pdt.patient_lname LIKE '$txt_iolink_pt_search%') $genQry  
											ORDER BY pdt.patient_lname ASC, pdt.patient_fname ASC, piwt.dos DESC";							
			}
		}
	}
	if($getPatientSearchQry) {
		$getPatientSearchRes = imw_query($getPatientSearchQry) or die($getPatientSearchQry.'error1'.imw_error());
		$getPatientSearchNumRow = imw_num_rows($getPatientSearchRes);
	}
	
}	
?>



	<div id="iOLinkPtSearchSubId" style="height:325px; width:755px; overflow:scroll; overflow-x:hidden; ">
		<table style="border:none; padding:2px; width:100%; ">
			<?php
			if($getPatientSearchNumRow>0) {
			?>
			
				<tr class="text_homeb alignLeft valignMiddle" style="cursor:pointer; font-size:12px; ">
					<td class="nowrap" style="width:25%; height:20px; padding-left:3px;background-color:#F8F9F7;border:solid 2px; border-color:#ECF1EA;">Patient Name - ID</td>
					<td class="nowrap" style="width:10%; height:20px; padding-left:3px;background-color:#F8F9F7;border:solid 2px; border-color:#ECF1EA;">DOB</td>
                    <td class="nowrap" style="width:10%; height:20px; padding-left:3px;background-color:#F8F9F7;border:solid 2px; border-color:#ECF1EA;">DOS</a></td>
					<td class="nowrap" style="width:35%; height:20px; padding-left:3px;background-color:#F8F9F7;border:solid 2px; border-color:#ECF1EA;">Address</td>
					<td class="nowrap" style="width:10%; height:20px; padding-left:3px;background-color:#F8F9F7;border:solid 2px; border-color:#ECF1EA;">Gender</a></td>
					<!--<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">ID</a></td>-->
				</tr>
				<?php
				while($getPatientSearchRow = imw_fetch_array($getPatientSearchRes)) {
					
					$patient_id = $getPatientSearchRow['patient_id'];
					$patient_in_waiting_id = $getPatientSearchRow['patient_in_waiting_id'];
					$showDos = $getPatientSearchRow['showDos'];
					$patient_status = $getPatientSearchRow['patient_status'];
					
					$ahref_iOLink_start = "<a class='link_home' style='cursor:pointer;' href='javascript:void(0);' onClick='waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\",\"search_patient\",\"$patient_status\");'>";
					$ahref_iOLink_end="</a>";
					
					//$patient_in_waiting_dos='';
					//$patient_in_waiting_dos_temp = $getPatientSearchRow['dos'];
					//if($patient_in_waiting_dos_temp) { $patient_in_waiting_dos=date('m-d-Y',strtotime($patient_in_waiting_dos_temp));}
					
					$patient_waiting_patient_first_name = $getPatientSearchRow['patient_fname'];
					$patient_waiting_patient_middle_name = $getPatientSearchRow['patient_mname'];
					$patient_waiting_patient_last_name = $getPatientSearchRow['patient_lname'];
					$patient_waiting_patient_name = $patient_waiting_patient_last_name.", ".$patient_waiting_patient_first_name;
					$patient_waiting_patient_dob='';
					$patient_waiting_patient_dob_temp = $getPatientSearchRow['date_of_birth'];
					if($patient_waiting_patient_dob_temp) { $patient_waiting_patient_dob=date('m-d-Y',strtotime($patient_waiting_patient_dob_temp));}
					
					$patient_waiting_patient_street1 = stripslashes($getPatientSearchRow['street1']);
					
					
					//$patient_waiting_patient_primary_procedure = $getPatientSearchRow['patient_primary_procedure'];
					$patient_waiting_patient_sex='';
					$patient_waiting_patient_sex_temp = $getPatientSearchRow['sex'];
					if($patient_waiting_patient_sex_temp=='m') {
						$patient_waiting_patient_sex='Male';
					}else if($patient_waiting_patient_sex_temp=='f') {
						$patient_waiting_patient_sex='Female';
					}
									
				?>
					<tr class="text1 alignLeft valignMiddle capitalize" style="cursor:pointer;  background-color:#FFFFFF;">
						<td class="nowrap" style="height:20px; padding-left:3px;border:solid 2px; border-color:#ECF1EA;"><?php 				$patient_name="";
					$patient_name=$ahref_iOLink_start.$patient_waiting_patient_name.' - '.$patient_id;
					if($patient_status=="Canceled"){
						$patient_name.=" (<span style='color:#F00; font-family:Times New Roman, Times, serif; font-weight:bold'>T</span><i><b>o</b></i>-Do)";	
					}
					echo $patient_name.$ahref_iOLink_end;
						
						?></td>
						<td class="nowrap" style="height:20px; padding-left:3px;border:solid 2px; border-color:#ECF1EA;"><?php echo $ahref_iOLink_start.$patient_waiting_patient_dob.$ahref_iOLink_end;?></td>
                        <td class="nowrap" style="height:20px; padding-left:3px;border:solid 2px; border-color:#ECF1EA;"><?php echo $ahref_iOLink_start.$showDos.$ahref_iOLink_end;?></a></td>
						<td class="capitalize" style="height:20px; padding-left:3px;border:solid 2px; border-color:#ECF1EA;"><?php echo $ahref_iOLink_start.$patient_waiting_patient_street1.$ahref_iOLink_end;?></td>
						<td class="nowrap" style="height:20px; padding-left:3px;border:solid 2px; border-color:#ECF1EA;"><?php echo $ahref_iOLink_start.$patient_waiting_patient_sex.$ahref_iOLink_end;?></a></td>
						<!--<td height="20" nowrap><img src="images/tpixel.gif" width="3" height="1"><?php //echo $ahref_iOLink_start.$showDos.$ahref_iOLink_end;?></a></td>-->
					</tr>
				<?php
				}
			}else {
			?>
				<tr class="text1 alignLeft valignMiddle" style="cursor:pointer; background-color:#FFFFFF;">
					<td class="text1b alignCenter nowrap" style="height:20px; padding-left:5px;">No Search Found</td>
				</tr>
			<?php
			}
			?>
			
		</table>
	</div>
</body>
</html>
