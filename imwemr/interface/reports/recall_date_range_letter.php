<?php
set_time_limit(900);
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');
$patientIdArr 			= array();
$months 				= $_REQUEST['months'];
$years 					= $_REQUEST['years'];
$last_nam_frm			= $_REQUEST['last_nam_frm'];
$last_nam_to			= $_REQUEST['last_nam_to'];
$recall_date_from		= $_REQUEST['recall_date_from'];
$recall_date_to			= $_REQUEST['recall_date_to'];
$pat_id_imp				= $_REQUEST['pat_id_imp'];
$recallTemplatesListId	= $_REQUEST['recallTemplatesListId'];

$cptCode	 			= $_REQUEST['cptCodeId'];
$dxCode 				= $_REQUEST['dxCodeId'];
$dxCode10 				= $_REQUEST['dxCodeId10'];
$add_testsNme	 		= $_REQUEST['add_tests'];

$elemImmunizNme = $_REQUEST['elemImmunizId'];
if($_REQUEST['medications']) {
	$medications		= trim(nl2br($_REQUEST['medications']));
	$medicationsNmeArr	= explode('<br />',$medications);
	foreach($medicationsNmeArr as $keyMeds =>$valMeds) {
		$medicationsNmeArr[$keyMeds] = trim($valMeds);	
	}
}

if($_REQUEST['allergies']) {
	$allergies			= trim(nl2br($_REQUEST['allergies']));
	$allergiesNmeArr	= explode('<br />',$allergies);
	foreach($allergiesNmeArr as $keyAlrg =>$valAllrg) {
		$allergiesNmeArr[$keyAlrg] = trim($valAllrg);	
	}
}

if($medicationsNmeArr)	{$medicationsNme= implode("','",$medicationsNmeArr);}
if($allergiesNmeArr)	{$allergiesNme	= implode("','",$allergiesNmeArr);	}

$where = "WHERE 1=1";
$andCptQry=$andDxCodeQry=$andImmunizQry='';
if($cptCode) 		{  $andCptQry 		= " AND pi.cptCode IN('".$cptCode."') ";}

/* if($dxCode)  		{  $andDxCodeQry 	= " AND (pi.dx1 IN('".$dxCode."') || pi.dx2 IN('".$dxCode."') || pi.dx3 IN('".$dxCode."') || pi.dx4 IN('".$dxCode."')) ";} */

if($dxCode || $dxCode10){
	$andDxCodeQry="AND (";
	$andOR='';
	if($dxCode){
		$andDxCodeQry.="(pi.dx1 IN('".$dxCode."') || pi.dx2 IN('".$dxCode."') || pi.dx3 IN('".$dxCode."') || pi.dx4 IN('".$dxCode."'))";
		$andOR=' OR ';
	}
	if($dxCode10){
		$andDxCodeQry.=$andOR."(pi.dx1 IN(".$dxCode10.") ||  pi.dx2 IN(".$dxCode10.") ||  pi.dx3 IN(".$dxCode10.") ||  pi.dx4 IN(".$dxCode10."))";
	}	
	$andDxCodeQry.=")";
}

if($elemImmunizNme) {  $andImmunizQry 	= " AND immunization_id IN('".$elemImmunizNme."') ";}
if($medicationsNme) {  $andMedicQry 	= " AND title IN('".$medicationsNme."') AND (type='1' OR type='4') ";}
if($allergiesNme) 	{  $andAllergQry 	= " AND title IN('".$allergiesNme."') AND type='7' ";}

$andDtRangeCptDxQry=$andDtRangeImmunizQry=$andExamDateRangeQry=$andExam_DateRangeQry=$andMedicationRangeQry=$andAllergiesRangeQry= '';
if($recall_date_from!='' || $recall_date_to!='') {
	if($recall_date_from!='') 	{ 
		$dtFromExplode 			= explode('-',$recall_date_from);
		$recall_date_fromNew 	= date('Y-m-d',mktime(0,0,0,$dtFromExplode[0],$dtFromExplode[1],$dtFromExplode[2]));
		$andDtRangeCptDxQry    .= " AND sb.dateOfService>='".$recall_date_fromNew."' ";	
		$andDtRangeImmunizQry  .= " AND administered_date>='".$recall_date_fromNew."' ";	
		$andExamDateRangeQry   .= " AND examDate>='".$recall_date_fromNew."' ";	
		$andExam_DateRangeQry  .= " AND exam_date>='".$recall_date_fromNew."' ";	
		$andMedicationRangeQry .= " AND date>='".$recall_date_fromNew."' ";	
		$andAllergiesRangeQry  .= " AND date>='".$recall_date_fromNew."' ";	
		
	}
	if($recall_date_to!='') 	{ 
		$dtToExplode 			= explode('-',$recall_date_to);
		$recall_date_toNew 		= date('Y-m-d',mktime(0,0,0,$dtToExplode[0],$dtToExplode[1],$dtToExplode[2]));
		$andDtRangeCptDxQry    .= " AND sb.dateOfService < '".$recall_date_toNew."' ";		
		$andDtRangeImmunizQry  .= " AND administered_date < '".$recall_date_toNew."' ";
		$andExamDateRangeQry   .= " AND examDate < '".$recall_date_toNew."' ";
		$andExam_DateRangeQry  .= " AND exam_date < '".$recall_date_toNew."' ";
		$andMedicationRangeQry .= " AND date < '".$recall_date_toNew."' ";
		$andAllergiesRangeQry  .= " AND date < '".$recall_date_toNew."' ";
	}
}

if($recallTemplatesListId) {
	$recallTemplateData			= '';
	$recallTemplateQry 			= "SELECT * FROM recalltemplate WHERE recallLeter_id='".$recallTemplatesListId."'";
	$recallTemplateRes 			= @imw_query($recallTemplateQry);
	$recallTemplateNumRow 		= @imw_num_rows($recallTemplateRes);
	if($recallTemplateNumRow>0) {
		$recallTemplateRow 		= @imw_fetch_array($recallTemplateRes);
		$recallTemplateData 	= stripslashes($recallTemplateRow['recallTemplateData']);	
	}
}

$patientIdArrCptDx=$patientIdArrImmuniz=$patientIdArrMedication=$patientIdArrTest=$patientIdArrAllergy=array();

//START CODE TO GET PATIENT-ID FROM CPT-CODE AND DX-CODE
if($cptCode || $dxCode || $dxCode10) {
	$unfullQry ='';
	if($pat_id_imp !='') { $unfullQry = "AND sb.patientId IN(".$pat_id_imp.")"; }
	
	$cptDxQry = "SELECT sb.patientId FROM superbill as sb,procedureinfo as pi 
				 WHERE pi.idSuperBill=sb.idSuperBill $andDtRangeCptDxQry $andCptQry $andDxCodeQry $unfullQry
				 AND pi.delete_status='0'
				 ";
	$cptDxRes=imw_query($cptDxQry) or die(imw_error());
	if(imw_num_rows($cptDxRes)>0) {
		while($cptDxRow=imw_fetch_array($cptDxRes)){
			$patientIdArrCptDx[]=$cptDxRow['patientId'];
		}
	}else {
		$patientIdArrCptDx[]='-1';
	}
}
//END CODE TO GET PATIENT-ID FROM CPT-CODE AND DX-CODE

//START CODE TO GET PATIENT-ID FROM IMMUNIZATION
if($elemImmunizNme) {
	$unfullQry ='';
	if($pat_id_imp !='') { $unfullQry = "AND patient_id IN(".$pat_id_imp.")"; }
	
	$immunizQry = "SELECT patient_id FROM immunizations WHERE 1=1 $andImmunizQry $andDtRangeImmunizQry $unfullQry";
	$immunizRes=imw_query($immunizQry) or die(imw_error());
	if(imw_num_rows($immunizRes)>0) {
		while($immunizRow=imw_fetch_array($immunizRes)){
			$patientIdArrImmuniz[]=$immunizRow['patient_id'];
		}
	}else {
		$patientIdArrImmuniz[]='-1';	
	}
	
}
//END CODE TO GET PATIENT-ID FROM IMMUNIZATION

//START CODE TO GET PATIENT-ID FROM MEDICATION
if($medicationsNme) {
	$unfullQry ='';
	if($pat_id_imp !='') { $unfullQry = "AND pid IN(".$pat_id_imp.")"; }
	
	$medicationQry = "SELECT pid FROM lists WHERE 1=1 $andMedicQry $andMedicationRangeQry $unfullQry";
	$medicationRes=imw_query($medicationQry) or die(imw_error());
	if(imw_num_rows($medicationRes)>0) {
		while($medicationRow=imw_fetch_array($medicationRes)){
			$patientIdArrMedication[]=$medicationRow['pid'];
		}
	}else {
		$patientIdArrMedication[]='-1';	
	}
}
//END CODE TO GET PATIENT-ID FROM MEDICATION

//START CODE TO GET PATIENT-ID FROM ALLERGIES
if($allergiesNme) {
	$unfullQry ='';
	if($pat_id_imp !='') { $unfullQry = "AND pid IN(".$pat_id_imp.")"; }

	$allergyQry = "SELECT pid FROM lists WHERE 1=1 $andAllergQry $andAllergiesRangeQry $unfullQry";
	$allergyRes=imw_query($allergyQry) or die(imw_error());
	if(imw_num_rows($allergyRes)>0) {
		while($allergyRow=imw_fetch_array($allergyRes)){
			$patientIdArrAllergy[]=$allergyRow['pid'];
		}
	}else {
		$patientIdArrAllergy[]='-1';	
	}
}
//END CODE TO GET PATIENT-ID FROM ALLERGIES


//START CODE TO GET PATIENT-ID FROM TESTS
if($add_testsNmeArr) {
	$unfullQry ='';
	if($pat_id_imp !='') { $unfullQry = "AND patientId IN(".$pat_id_imp.")"; }

	$unfullQry1 ='';
	if($pat_id_imp !='') { $unfullQry1 = "AND patient_id IN(".$pat_id_imp.")"; }
	
	foreach($add_testsNmeArr as $add_testsNme)	{
		if(strtoupper($add_testsNme)=='VF') {
			$vfQry = "SELECT patientId FROM vf WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$vfRes=imw_query($vfQry) or die(imw_error());
			if(imw_num_rows($vfRes)>0) {
				while($vfRow=imw_fetch_array($vfRes)){
					$patientIdArrTest[]=$vfRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='HRT') {
			$hrtQry = "SELECT patient_id FROM nfa WHERE 1=1 $andExamDateRangeQry $unfullQry1";
			$hrtRes=imw_query($hrtQry) or die(imw_error());
			if(imw_num_rows($hrtRes)>0) {
				while($hrtRow=imw_fetch_array($hrtRes)){
					$patientIdArrTest[]=$hrtRow['patient_id'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='OCT') {
			$octQry = "SELECT patient_id FROM oct WHERE 1=1 $andExamDateRangeQry $unfullQry1";
			$octRes=imw_query($octQry) or die(imw_error());
			if(imw_num_rows($octRes)>0) {
				while($octRow=imw_fetch_array($octRes)){
					$patientIdArrTest[]=$octRow['patient_id'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='PACHY') {
			$pachyQry = "SELECT patientId FROM pachy WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$pachyRes=imw_query($pachyQry) or die(imw_error());
			if(imw_num_rows($pachyRes)>0) {
				while($pachyRow=imw_fetch_array($pachyRes)){
					$patientIdArrTest[]=$pachyRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='IVFA') {
			$ivfaQry = "SELECT patient_id FROM ivfa WHERE 1=1 $andExam_DateRangeQry $unfullQry1";
			$ivfaRes=imw_query($ivfaQry) or die(imw_error());
			if(imw_num_rows($ivfaRes)>0) {
				while($ivfaRow=imw_fetch_array($ivfaRes)){
					$patientIdArrTest[]=$ivfaRow['patient_id'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='FUNDUS') {
			$fundusQry = "SELECT patientId FROM disc WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$fundusRes=imw_query($fundusQry) or die(imw_error());
			if(imw_num_rows($fundusRes)>0) {
				while($fundusRow=imw_fetch_array($fundusRes)){
					$patientIdArrTest[]=$fundusRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='EXTERNAL PHOTOS') {
			$extPhotoQry = "SELECT patientId FROM disc_external WHERE 1=1 $andExamDateRangeQry AND fundusDiscPhoto='1' $unfullQry";
			$extPhotoRes=imw_query($extPhotoQry) or die(imw_error());
			if(imw_num_rows($extPhotoRes)>0) {
				while($extPhotoRow=imw_fetch_array($extPhotoRes)){
					$patientIdArrTest[]=$extPhotoRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='ANTERIOR PHOTOS') {
			$antPhotoQry = "SELECT patientId FROM disc_external WHERE 1=1 $andExamDateRangeQry AND fundusDiscPhoto='2' $unfullQry";
			$antPhotoRes=imw_query($antPhotoQry) or die(imw_error());
			if(imw_num_rows($antPhotoRes)>0) {
				while($antPhotoRow=imw_fetch_array($antPhotoRes)){
					$patientIdArrTest[]=$antPhotoRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='B-SCAN') {
			$bScanQry = "SELECT patientId FROM test_bscan WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$bScanRes=imw_query($bScanQry) or die(imw_error());
			if(imw_num_rows($bScanRes)>0) {
				while($bScanRow=imw_fetch_array($bScanRes)){
					$patientIdArrTest[]=$bScanRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='CELL COUNT') {
			$cellCntQry = "SELECT patientId FROM test_cellcnt WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$cellCntRes=imw_query($cellCntQry) or die(imw_error());
			if(imw_num_rows($cellCntRes)>0) {
				while($cellCntRow=imw_fetch_array($cellCntRes)){
					$patientIdArrTest[]=$cellCntRow['patientId'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='OPHTHALMOSCOPY') {
			$ophthaQry = "SELECT patient_id FROM ophtha WHERE 1=1 $andExam_DateRangeQry $unfullQry1";
			$ophthaRes=imw_query($ophthaQry) or die(imw_error());
			if(imw_num_rows($ophthaRes)>0) {
				while($ophthaRow=imw_fetch_array($ophthaRes)){
					$patientIdArrTest[]=$ophthaRow['patient_id'];
				}
			}
		}
		if(strtoupper($add_testsNme)=='TOPOGRAPHY') {
			$topogrpQry 				= "SELECT patientId FROM topography WHERE 1=1 $andExamDateRangeQry $unfullQry";
			$topogrpRes					= imw_query($topogrpQry) or die(imw_error());
			if(imw_num_rows($topogrpRes)>0) {
				while($topogrpRow		= imw_fetch_array($topogrpRes)){
					$patientIdArrTest[]	= $topogrpRow['patientId'];
				}
			}
		}
	}
/*	if(is_array($patientIdArrTest) && count($patientIdArrTest)==0) {
		$patientIdArrTest[]='-1';	
	}*/
}
//END CODE TO GET PATIENT-ID FROM TESTS

$data_arr 			= array();
if(count($patientIdArrCptDx) > 0) 		{$data_arr[] = $patientIdArrCptDx;		}
if(count($patientIdArrImmuniz) > 0) 	{$data_arr[] = $patientIdArrImmuniz;	}
if(count($patientIdArrMedication) > 0) 	{$data_arr[] = $patientIdArrMedication;	}
if(count($patientIdArrTest) > 0) 		{$data_arr[] = $patientIdArrTest;		}
if(count($patientIdArrAllergy) > 0) 	{$data_arr[] = $patientIdArrAllergy;	}
$patientIdArr 		= $data_arr[0];
if(count($data_arr) == 2){$patientIdArr = array_intersect($data_arr[0],$data_arr[1]);}
if(count($data_arr) == 3){$patientIdArr = array_intersect($data_arr[0],$data_arr[1],$data_arr[2]);}
if(count($data_arr) == 4){$patientIdArr = array_intersect($data_arr[0],$data_arr[1],$data_arr[2],$data_arr[3]);}
if(count($data_arr) == 5){$patientIdArr = array_intersect($data_arr[0],$data_arr[1],$data_arr[2],$data_arr[3],$data_arr[4]);}

//echo $data_arr[0];
//$patientIdArr = array_intersect($patientIdArrCptDx,$patientIdArrTest,$patientIdArrMedication,$patientIdArrImmuniz,$patientIdArrAllergy);
$patientIdArr = array_unique($patientIdArr);

ob_start();
if(count($patientIdArr)>0 && $patientIdArr[0]!='-1'){
	$strHTML = '
			<style>
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
			</style>
			';
	$strHTML .= '<page backtop="0mm" backbottom="0mm">';
	$strHTML .= '<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';

	$r = 0;

	foreach($patientIdArr as $chkPatientid) {
		if($chkPatientid != "-1"){ 
			$qu=imw_query("Select hipaa_mail from patient_data where id='".$chkPatientid."'");
			$chk_patient_deta= imw_fetch_assoc($qu);			
			
			if($chk_patient_deta['hipaa_mail']=='1') {
				$r++;
			}
		}  
	}
	
	$i = 1;
	$j = 1;
	$t=0;
	foreach($patientIdArr as $patientid) {
		if($patientid != "-1"){
			//$patient_deta		= patient_data($patientid);
			$qu=imw_query("select * from patient_data where id='".$patientid."'");
			$patient_deta			= imw_fetch_assoc($qu);			
			$recallApptInfoArr 	= getRecallApptInfo($patientid);
			

			if($patient_deta['hipaa_mail']=='1') {

				$t++;
				$patientRefTo	= '';
				$recallData 	= $recallTemplateData;

				//PATIENT VARIABLE
				$PtDOB 					= $patient_deta['DOB'];
				if($PtDOB && $PtDOB!='0000-00-00') { $PtDOB = date('m-d-Y',strtotime($PtDOB));}
				$recallData = str_ireplace("{DOB}",$PtDOB,$recallData);
				$recallData = str_ireplace("{DATE}",date('m-d-Y'),$recallData);	
				$recallData = str_ireplace("{ETHNICITY}",$patient_deta['ethnicity'],$recallData);	
				$recallData = str_ireplace("{LANGUAGE}",$patient_deta['language'],$recallData);
				$recallData = str_ireplace("{ADDRESS1}",$patient_deta['street'],$recallData);
				$recallData = str_ireplace("{ADDRESS2}",$patient_deta['street2'],$recallData);
				$recallData = str_ireplace("{PATIENT CITY}",$patient_deta['city'],$recallData);
				$recallData = str_ireplace("{PATIENT FIRST NAME}",$patient_deta['fname'],$recallData);
				$recallData = str_ireplace("{HOME PHONE}",$patient_deta['phone_home'],$recallData);
				$recallData = str_ireplace("{PatientID}",$patient_deta['id'],$recallData);
				$recallData = str_ireplace("{LAST NAME}",$patient_deta['lname'],$recallData);
				$recallData = str_ireplace("{PATIENT MRN}",$patient_deta['External_MRN_1'],$recallData);
				$recallData = str_ireplace("{PATIENT MRN2}",$patient_deta['External_MRN_2'],$recallData);
				$recallData = str_ireplace("{MIDDLE NAME}",$patient_deta['mname'],$recallData);
				$recallData = str_ireplace("{MOBILE PHONE}",$patient_deta['phone_cell'],$recallData);
				$recallData = str_ireplace("{PATIENT NAME TITLE}",$patient_deta['title'],$recallData);
				//$arrStateZip = explode(" ",$patient_deta[14]);
				$recallData = str_ireplace("{PATIENT STATE}",$patient_deta['state'],$recallData);
				$recallData = str_ireplace("{WORK PHONE}",$patient_deta['phone_biz'],$recallData);
				$recallData = str_ireplace("{PATIENT ZIP}",$patient_deta['postal_code'],$recallData);
				$recallData = str_ireplace("{RACE}",$patient_deta['race'],$recallData);
				$recallData = str_ireplace("{PT-KEY}",$patient_deta['temp_key'],$recallData);

				//RECALL INFO
				$recallData = str_ireplace("{APPT DATE}",$recallApptInfoArr[0],$recallData);
				$recallData = str_ireplace("{APPT DATE_F}",$recallApptInfoArr[1],$recallData);
				$recallData = str_ireplace("{APPT FACILITY}",$recallApptInfoArr[2],$recallData);
				$recallData = str_ireplace("{APPT FACILITY PHONE}",$recallApptInfoArr[3],$recallData);
				$recallData = str_ireplace("{APPT PROC}",$recallApptInfoArr[4],$recallData);
				$recallData = str_ireplace("{APPT PROVIDER}",$recallApptInfoArr[5],$recallData);
				$recallData = str_ireplace("{APPT PROVIDER LAST NAME}",$recallApptInfoArr[6],$recallData);
				$recallData = str_ireplace("{APPT TIME}",$recallApptInfoArr[8],$recallData);


				$strHTML .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text" valign="top"  style="margion:0px;">
										'.$recallData.'
									</td>
								</tr>
							</table>
							</page>		
							';
				if($r>$t) {
					$strHTML .= '<page pageset="old">';
				}
			}
		}
	}
}
$bl_printed = true;
$file_location = '';
if(trim($strHTML) != ""){
	$file_location = write_html($strHTML);
}else{
	$bl_printed = false;
}
if($bl_printed == false){
	?>
    <html>
		<body>		
		<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">No Record Found.</td>
			</tr>
		</table>
        </body>
    </html>
	<?php
}else{
	?>
	<form name="frmRecallLetterPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" >
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
	</form>	
	<script>
		document.frmRecallLetterPDF.submit();
	</script>
	<?php
	}
?>