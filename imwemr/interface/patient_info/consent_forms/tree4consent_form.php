<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
	
	File: tree4consent_form.php
	Purpose: Show tree for consent form
	Access Type: Include 
*/
set_time_limit(0);
require_once("../../../config/globals.php");
require_once("../../../library/classes/dhtmlgoodies_tree.class.php");
require_once("../../../library/classes/Mobile_Detect.php");
$library_path = $GLOBALS['webroot'].'/library';
set_time_limit(0);
// Exclude tablets.
$detect = new Mobile_Detect;
$patient_id = $_SESSION['patient'];
$this_device = "frontend";
if( $detect->isMobile() && !$detect->isTablet() ){
$patient_id = $_SESSION['patient'];
//if( $detect->isMobile()  || $detect->isTablet() ){
$this_device = "mobile";
}
$sessOptId = $_SESSION['authId'];
$tree = new dhtmlgoodies_tree();
$coming_from = isset($_GET['from']) ? trim($_GET['from']) : '';$addToqry1 = '';
$_SESSION['authId'];

$consQry = "SELECT consent_form_id,consent_form_name,cat_id FROM consent_form ORDER BY consent_form_id";
$consRes = imw_query($consQry);
while($consRow = imw_fetch_assoc($consRes)) {
	$consFormId = $consRow['consent_form_id'];
	$consFormName = stripslashes($consRow['consent_form_name']);
	$consFormArr[$consFormId] = $consFormName;
}

//START CODE TO MOVE FORM TO TRASH OR SIGNED FOLDER
if($_REQUEST['moveToTrashConsentId'] || $_REQUEST['moveToSignedConsentId'] || $_REQUEST['moveToTrashSurgeryConsentId'] || $_REQUEST['moveToSignedSurgeryConsentId']){
	$moveToTrashSignedConsentId = '';
	$moveToTrashSignedConsentValue='';
	$consentTableName = "patient_consent_form_information";
	$consentFieldName = "form_information_id";
	if($_REQUEST['moveToTrashConsentId']) {
		$moveToTrashSignedConsentId = $_REQUEST['moveToTrashConsentId'];
		$moveToTrashSignedConsentValue = '1';
	}else if($_REQUEST['moveToSignedConsentId']) {
		$moveToTrashSignedConsentId = $_REQUEST['moveToSignedConsentId'];
		$moveToTrashSignedConsentValue = '0';
	}else if($_REQUEST['moveToTrashSurgeryConsentId']) {
		$moveToTrashSignedConsentId = $_REQUEST['moveToTrashSurgeryConsentId'];
		$moveToTrashSignedConsentValue = '1';
		$consentTableName = "surgery_consent_filled_form";
		$consentFieldName = "surgery_consent_id";
	}else if($_REQUEST['moveToSignedSurgeryConsentId']) {
		$moveToTrashSignedConsentId = $_REQUEST['moveToSignedSurgeryConsentId'];
		$moveToTrashSignedConsentValue = '0';
		$consentTableName = "surgery_consent_filled_form";
		$consentFieldName = "surgery_consent_id";
	}
	if($moveToTrashSignedConsentId) {
		$qry = "UPDATE ".$consentTableName." SET 
					movedToTrash 				= '".$moveToTrashSignedConsentValue."', 
					modified_operator_id 		= '".$_SESSION['authId']."',
					modified_form_created_date 	= '".date('Y-m-d H:i:s')."' 
					WHERE ".$consentFieldName." = '".$moveToTrashSignedConsentId."'";
		$rsQry = imw_query($qry);

	}
}
//END CODE TO MOVE FORM TO TRASH OR SIGNED FOLDER



/*Move back to pending Fax*/
if($moveToPendingFax){
	
	if(!$cnfrm){
		echo '<script>top.fancyConfirm("Sure! you want to move the fax to pending ?","top.fmain.restoreFax('.$moveToPendingFax.',true)");</script>';
	}
	else{
		$sqlDel = 'UPDATE `inbound_fax` SET `patient_id`=0, `pending_by`='.((int)$_SESSION['authId']).', `moved_pending_at`=\''.date('Y-m-d H:i:s').'\', `fax_folder`=\'\' WHERE `id`='.$moveToPendingFax;
		imw_query($sqlDel);
	}
}
include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
if(!$p) { $p=1;}
if($_REQUEST["doc_name"]=="favourite_docs") {
	$a=$p;
	$tree->addToArray($a,"Favorite",0,"","",$initFavDocsClass);
	$p=$a;
	
	$patient_id = $_SESSION['patient'];
	
	$fields = "sdt.doc_type, fc.folder_categories_id,sdt.doc_title,sdt.pdf_url,sdt.scan_doc_id as doc_id,
						 DATE_FORMAT(if(sdt.doc_upload_type = 'scan',sdt.upload_date,sdt.upload_docs_date),'".get_sql_date_format()." %H:%i') as upload_date_time,
						 if(sdt.doc_upload_type = 'scan',sdt.scandoc_operator_id,sdt.upload_operator_id) as doc_operator,
						 concat(u.lname,', ',u.fname,' ',u.mname) AS doc_operator_name ";
	$qry = "Select ".$fields." From ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt 
												Inner Join ".constant("IMEDIC_SCAN_DB").".folder_categories fc on (fc.folder_categories_id = sdt.folder_categories_id)
												LEFT JOIN users u ON(u.id = if(sdt.doc_upload_type = 'scan',sdt.scandoc_operator_id,sdt.upload_operator_id))
												Where sdt.patient_id='".$patient_id."' And fc.folder_status = 'active' And fc.favourite = 1 
												Order By fc.folder_name ASC ";
	$sql = imw_query($qry) or die(imw_error());
	$cnt = imw_num_rows($sql);
	$showInfo = "";
	
	$b = $p;
	while( $row = imw_fetch_assoc($sql) ){
		$p++;
		$pdf_url = $row['pdf_url'];
		$doc_title = $row['doc_title'];
		$doc_name = $doc_title ? $doc_title : $pdf_url;
		$doc_id = $row['doc_id'];
		$f_id = $row['folder_categories_id'];
		$showInfo		 = "yes";
		$doc_date_time = $row['upload_date_time'];
		$doc_operator_name = $row['doc_operator_name']; 
		$ext = $row['doc_type'];
		if( empty($doc_title) ) {
			if( (strpos($pdf_url,'.jpg') === false && $pdf_url != '') )
				$ext = 'pdf';
		}
		
		$popup_url = $GLOBALS['webroot']."/interface/chart_notes/show_image.php?ext=".$ext."&id=".$doc_id;
		
		//$scnDocUnReadImage = scnDocUnReadImageFun($patient_id,'scan',$_SESSION['authId'],$doc_id,$f_id);
		$spanUnReadImg =  "<span id=\"spnUnreadDocNaviId".$row['scan_doc_id']."\">".$scnDocUnReadImage."</span>";
		
		$tree->addToArray($p,$doc_name,$b,$GLOBALS['webroot']."/interface/chart_notes/show_image.php?from=fav_doc&noZoom=1&hide_close_btn=1&ext=".$ext."&id=".$doc_id,"consent_data","pdf-icon","","","","","","","",$showInfo,$doc_date_time,$doc_operator_name,"","","",true,$popup_url,true,$spanUnReadImg);
	}
	$b=$p;
	
}
if(!$p) { $p=1;}
if($_REQUEST["doc_name"]=="signed_consent") {
	$a=$p;
	$tree->addToArray($a,"Signed",0,"","",$initSignedConsentClass);
	$patient_id = $_SESSION['patient'];
	//---- Get Patient Consent Forms Signed Date(s)-------
	$qry = "SELECT DATE_FORMAT(CASE WHEN pcf.chart_procedure_id >0 THEN cp.exam_date ELSE pcf.form_created_date END, '".get_sql_date_format('','Y','-')."') as formCreatedDate, 
			pcf.chart_procedure_id,GROUP_CONCAT(pcf.chart_procedure_id)as c_proc,
			DATE_FORMAT(cp.exam_date, '".get_sql_date_format('','Y','-')."') as proc_exam_date_format   
			from patient_consent_form_information pcf 
			left join chart_procedures cp ON(cp.id = pcf.chart_procedure_id)
			where pcf.patient_id=$patient_id 
			AND pcf.movedToTrash = 0 
			AND pcf.package_category_id = 0 
			GROUP BY formCreatedDate
			ORDER BY CASE WHEN pcf.chart_procedure_id >0
			THEN cp.exam_date
			ELSE pcf.form_created_date
			END DESC
			
			" ;//ORDER BY pcf.form_created_date desc
	$patientSignConsentFormCreatedDate = get_array_records_query($qry);
	//$p=$w;
	//$p++;
	//START GET RECORDS OF INNER LOOP BASED ON DATE
	$patientSignConsentFormTmpArr = array();
	$qrynewTmp = "SELECT DATE_FORMAT(CASE WHEN pcfi.chart_procedure_id >0 THEN cp.exam_date ELSE pcfi.form_created_date END, '".get_sql_date_format('','Y','-')."') as formCreatedDateInner,
				pcfi.consent_form_id,pcfi.consent_form_name,
				CASE WHEN pcfi.chart_procedure_id >0 THEN cp.exam_date ELSE pcfi.form_created_date END AS form_created_date, 
				pcfi.form_information_id,pcfi.operator_id,pcfi.modified_operator_id,pcfi.modified_form_created_date, pcfi.chart_procedure_id, 
				DATE_FORMAT(CASE WHEN pcfi.chart_procedure_id >0 THEN cp.exam_date ELSE pcfi.form_created_date END, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consent_date_time,
				concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
				FROM patient_consent_form_information pcfi 
				LEFT JOIN users u ON(u.id = pcfi.operator_id)
				left join chart_procedures cp ON(cp.id = pcfi.chart_procedure_id)
				WHERE pcfi.patient_id=$patient_id 
				AND pcfi.movedToTrash = 0 
				AND pcfi.package_category_id = '0' 
				ORDER BY pcfi.form_created_date desc  " ;
	
	$patientSignConsentFormTmp = get_array_records_query($qrynewTmp);
	for($xt=0;$xt<count($patientSignConsentFormTmp);$xt++){
		$formCreatedDateInner = $patientSignConsentFormTmp[$xt]['formCreatedDateInner'];
		$patientSignConsentFormTmpArr[$formCreatedDateInner][] = $patientSignConsentFormTmp[$xt]; 
	}
	//END GET RECORDS OF INNER LOOP BASED ON DATE
	$p=$a;
	$p++;
	$consentFolderClass = (count($patientSignConsentFormCreatedDate) > 0) ? "icon-folder-filled" : "icon-folder";
	$tree->addToArray($p,"Consent",$a,"","",$consentFolderClass);
	$b=$p;
	$showInfo = "";
	for($z=0;$z<count($patientSignConsentFormCreatedDate);$z++){
	
		//Check procedure notes related consent form
		$chart_procedure_id = $patientSignConsentFormCreatedDate[$z]['chart_procedure_id'];
		$chart_proc_ids_arr = explode(",",$patientSignConsentFormCreatedDate[$z]['c_proc']);
		
		if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id) && !in_array("0",$chart_proc_ids_arr)){
			//continue;
			//COMMENTED ABOVE LINE. iT WAS SKIPPING TO SHOW PENDING LETTERS UNTIL CHART IS FINALIZED.
		}
		//--
	
		$p++;
		$f = $p;
		$formCreatedDate=$patientSignConsentFormCreatedDate[$z]['formCreatedDate'];
		$a_pack = $p-1;
		$tree->addToArray($p,$formCreatedDate,$b,"","","icon-folder-filled");	
		
		//---- Get Patient Signed Consent Forms Created Date(s)-------
		$patientSignConsentForm = $patientSignConsentFormTmpArr[$formCreatedDate];
		$c=$p;
		for($x=0;$x<count($patientSignConsentForm);$x++){
		
			//Check procedure notes related consent form
			$chart_procedure_id = $patientSignConsentForm[$x]['chart_procedure_id'];
			if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
				//continue;
				//COMMENTED ABOVE LINE. iT WAS SKIPPING TO SHOW PENDING LETTERS UNTIL CHART IS FINALIZED.
			}
			//--
		
			$consentFormId = $patientSignConsentForm[$x]['consent_form_id'];
			$consentFormInfoId = $patientSignConsentForm[$x]['form_information_id'];
			$form_created_time=date("g:i A",strtotime($patientSignConsentForm[$x]['form_created_date']));
			
			$modified_operator_id = $patientSignConsentForm[$x]['modified_operator_id'];
			$modified_form_created_date = date("g:i A",strtotime($patientSignConsentForm[$x]['modified_form_created_date']));
			if($modified_operator_id) {
				$form_created_time 		= $modified_form_created_date; 
			}
			$consentFormName = $patientSignConsentForm[$x]['consent_form_name'];
			$showInfo		 = "yes";
			$consentDateTime = $patientSignConsentForm[$x]['consent_date_time'];
			$operatorName 	 = stripslashes($patientSignConsentForm[$x]['operator_name']);
			$p++;
			$consentFormName = trim(ucwords($consentFormName));
			$tree->addToArray($p,$consentFormName,$c,"print_consent_form.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId","consent_data","pdf-icon","remove-icon",$GLOBALS['webroot']."/interface/patient_info/consent_forms/index.php?from=".$coming_from."&moveToTrashConsentId=$consentFormInfoId&doc_name=".$_REQUEST['doc_name'],"","Move To Trash","","","",$showInfo,$consentDateTime,$operatorName,"","","",true,"","","",true);
		}
		$c=$p;
	}
	$b=$p;

	//START SIGNED SURGERY CONSENT FORM
	if($_REQUEST["from"]!="checkin") {
		$p++;	
		$patient_id = $_SESSION['patient'];
		//---- Get Patient Consent Forms Signed Date(s)-------
		$qry = "SELECT distinct DATE_FORMAT(form_created_date, '".get_sql_date_format('','Y','/')."') formCreatedDate from surgery_consent_filled_form where patient_id=$patient_id and movedToTrash = 0 ORDER BY form_created_date desc" ;
		$patientSignConsentFormCreatedDate = get_array_records_query($qry);
		$surgeryConsentFolderClass = (count($patientSignConsentFormCreatedDate) > 0) ? "icon-folder-filled" : "icon-folder";
		$tree->addToArray($p,"Surgery Consent",$a,"","",$surgeryConsentFolderClass);
		$b=$p;

		for($z=0;$z<count($patientSignConsentFormCreatedDate);$z++)
		{
			$p++;
			$formCreatedDate=$patientSignConsentFormCreatedDate[$z]['formCreatedDate'];
			$tree->addToArray($p,$formCreatedDate,$b,"","","icon-folder-filled");	
			$c=$p;
			//---- Get Patient Signed Consent Forms Created Date(s)-------
			
			$qrynew = "SELECT scff.consent_template_id, scff.surgery_consent_name, scff.form_created_date, scff.surgery_consent_id, scff.modified_operator_id, scff.modified_form_created_date, scff.appt_id, 
						DATE_FORMAT(scff.form_created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consent_date_time, DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format('','Y','-')."') AS apptDt  
						FROM surgery_consent_filled_form scff 
						LEFT JOIN schedule_appointments sa ON(sa.id = scff.appt_id)
						WHERE scff.patient_id=$patient_id and DATE_FORMAT(scff.form_created_date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' and scff.movedToTrash = 0 
						ORDER BY scff.form_created_date desc" ;
				$patientSignConsentForm = get_array_records_query($qrynew);
				
				for($x=0;$x<count($patientSignConsentForm);$x++)
				{
					$consentFormId = $patientSignConsentForm[$x]['consent_template_id'];
					$consentFormInfoId = $patientSignConsentForm[$x]['surgery_consent_id'];
					
					$form_created_time=date("g:i A",strtotime($patientSignConsentForm[$x]['form_created_date']));
			
					$modified_operator_id = $patientSignConsentForm[$x]['modified_operator_id'];
					$modified_form_created_date = date("g:i A",strtotime($patientSignConsentForm[$x]['modified_form_created_date']));
					if($modified_operator_id) {
						$form_created_time 		= $modified_form_created_date; 
					}
			
					$consentFormName = $patientSignConsentForm[$x]['surgery_consent_name'];
					$p++;
					$consentFormName = trim(ucwords(stripslashes($consentFormName)));
					$showInfo		 = "yes";
					$consentDateTime = $patientSignConsentForm[$x]['consent_date_time'];
					$apptDt = $patientSignConsentForm[$x]['apptDt'];
					//$operatorName 	 = stripslashes($patientSignConsentForm[$x]['operator_name']);
					
					$tree->addToArray($p,$consentFormName,$c,$GLOBALS['webroot']."/interface/patient_info/surgery_consent_forms/print_consent_form_surgery.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId&formcreated=".$formCreatedDate."&uniqueid=".rand(),"consent_data","pdf-icon","remove-icon",$GLOBALS['webroot']."/interface/patient_info/consent_forms/index.php?from=".$coming_from."&moveToTrashSurgeryConsentId=$consentFormInfoId&doc_name=".$_REQUEST['doc_name'],"","Move To Trash","","","",$showInfo,$consentDateTime,"","",$apptDt,"",true,"","","",true);
				}
				$c=$p;
		}
		$b=$p;
	}
	//$w = $p;
	//$p = $w;
	//START TRASH CONSENT FOLDER
	$p = $p+1;
	$tree->addToArray($p,"Trash Consent",$a);
	$b=$p;
	$qryTrash = "SELECT distinct DATE_FORMAT(CASE WHEN pcf.chart_procedure_id >0 THEN cp.exam_date ELSE pcf.form_created_date END, '".get_sql_date_format('','Y','-')."') AS formCreatedDate 
					from patient_consent_form_information pcf
					left join chart_procedures cp ON(cp.id = pcf.chart_procedure_id)
					where pcf.patient_id=$patient_id AND pcf.movedToTrash = 1 
					ORDER BY CASE WHEN pcf.chart_procedure_id >0 THEN cp.exam_date ELSE pcf.form_created_date END desc" ;
	$patientSignConsentFormCreatedDateTrash = get_array_records_query($qryTrash);
	$h_trash=$p_trash+1;
	for($z=0;$z<count($patientSignConsentFormCreatedDateTrash);$z++){
		$p++;
		$formCreatedDate=$patientSignConsentFormCreatedDateTrash[$z]['formCreatedDate'];
		$tree->addToArray($p,$formCreatedDate,$b);	
		$c=$p;
		


		$qrynew = "SELECT pcfi.consent_form_id,pcfi.consent_form_name,
					CASE WHEN pcfi.chart_procedure_id >0 THEN cp.exam_date ELSE pcfi.form_created_date END AS form_created_date,
					pcfi.form_information_id,pcfi.operator_id,pcfi.modified_operator_id, pcfi.modified_form_created_date,
					DATE_FORMAT(pcfi.modified_form_created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS removed_consent_date_time,   
					concat(u.lname,', ',u.fname,' ',u.mname) AS removed_by_operator_name
					FROM patient_consent_form_information pcfi
					LEFT JOIN users u ON(u.id = pcfi.modified_operator_id)
					left join chart_procedures cp ON(cp.id = pcfi.chart_procedure_id)
					WHERE pcfi.patient_id=$patient_id and DATE_FORMAT(CASE WHEN pcfi.chart_procedure_id >0 THEN cp.exam_date ELSE pcfi.form_created_date END, '".get_sql_date_format('','Y','-')."')='$formCreatedDate' 
					AND pcfi.movedToTrash = 1 ORDER BY pcfi.form_created_date desc" ;
		$patientSignConsentForm = get_array_records_query($qrynew);
		for($x=0;$x<count($patientSignConsentForm);$x++){
			$p++;
			$consentFormId = $patientSignConsentForm[$x]['consent_form_id'];
			$consentFormInfoId = $patientSignConsentForm[$x]['form_information_id'];
			$patientSignConsentForm[$x]['form_created_date'];
			$form_created_time=date("g:i A",strtotime($patientSignConsentForm[$x]['form_created_date']));
			
			//START GET OPERATOR INITIAL
			$consentFormOperatorId = $patientSignConsentForm[$x]['operator_id'];
			$modified_operator_id = $patientSignConsentForm[$x]['modified_operator_id'];
			$patientSignConsentForm[$x]['modified_form_created_date'];
			$modified_form_created_date = date("g:i A",strtotime($patientSignConsentForm[$x]['modified_form_created_date']));
			if($modified_operator_id) {
				$consentFormOperatorId 	= $modified_operator_id; 
				$form_created_time 		= $modified_form_created_date; 
			}
			//END GET OPERATOR INITIAL
			
			$consentFormName = $patientSignConsentForm[$x]['consent_form_name'];
			$consentFormName = trim(ucwords($consentFormName));
			$showInfo		 = "yes";
			$removedConsentDateTime = $patientSignConsentForm[$x]['removed_consent_date_time'];
			$removedByOperatorName 	 = stripslashes($patientSignConsentForm[$x]['removed_by_operator_name']);
			$tree->addToArray($p,$consentFormName,$c,"print_consent_form.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId","consent_data","pdf-icon","restore-icon",$GLOBALS['webroot']."/interface/patient_info/consent_forms/index.php?from=$coming_from&moveToSignedConsentId=$consentFormInfoId&doc_name=".$_REQUEST['doc_name'],"","Move To Signed Forms","","","",$showInfo,$removedConsentDateTime,$removedByOperatorName,"","","",true);
		}
		$c=$p;
	}
	$b=$p;
	//END TRASH CONSENT FOLDER
	
	//START TRASH FOLDER
	if($_REQUEST["from"]!="checkin") {
		$p = $p+1;
		$tree->addToArray($p,"Trash Surgery Consent",$a);
		$b=$p;
		$qryTrash = "SELECT distinct DATE_FORMAT(form_created_date, '".get_sql_date_format('','Y','/')."') formCreatedDate from surgery_consent_filled_form where patient_id=$patient_id and movedToTrash = 1 ORDER BY form_created_date desc" ;
		$patientSignConsentFormCreatedDateTrash = get_array_records_query($qryTrash);
		$h_trash=$p_trash+1;
		for($z=0;$z<count($patientSignConsentFormCreatedDateTrash);$z++){
			$p++;
			$formCreatedDate=$patientSignConsentFormCreatedDateTrash[$z]['formCreatedDate'];
			$tree->addToArray($p,$formCreatedDate,$b);	
			$c=$p;
			$qrynew = "SELECT scff.consent_template_id,scff.surgery_consent_name,scff.form_created_date,scff.surgery_consent_id,scff.modified_operator_id,scff.modified_form_created_date,scff.appt_id,  
						DATE_FORMAT(scff.modified_form_created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS modified_consent_date_time,
						DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format('','Y','-')."') AS apptDt,   
						concat(u.lname,', ',u.fname,' ',u.mname) AS modified_operator_name
						FROM surgery_consent_filled_form scff
						LEFT JOIN schedule_appointments sa ON(sa.id = scff.appt_id)
						LEFT JOIN users u ON(u.id = scff.modified_operator_id)
						WHERE scff.patient_id='".$patient_id."' and DATE_FORMAT(scff.form_created_date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' and scff.movedToTrash = 1 
						ORDER BY scff.form_created_date DESC" ;
			$patientSignConsentForm = get_array_records_query($qrynew);
			for($x=0;$x<count($patientSignConsentForm);$x++){
				$p++;
				$consentFormId = $patientSignConsentForm[$x]['consent_template_id'];
				$consentFormInfoId = $patientSignConsentForm[$x]['surgery_consent_id'];
				$showInfo		 = "yes";
				$consentDateTime = $patientSignConsentForm[$x]['modified_consent_date_time'];
				$operatorName 	 = stripslashes($patientSignConsentForm[$x]['modified_operator_name']);
				$apptDt 		 = $patientSignConsentForm[$x]['apptDt'];
				
				$consentFormName = $patientSignConsentForm[$x]['surgery_consent_name'];
				$consentFormName = trim(ucwords($consentFormName));
				$tree->addToArray($p,$consentFormName,$c,$GLOBALS['webroot']."/interface/patient_info/surgery_consent_forms/print_consent_form_surgery.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId&uniqueid=".rand(),"consent_data","pdf-icon","restore-icon",$GLOBALS['webroot']."/interface/patient_info/consent_forms/index.php?from=$coming_from&moveToSignedSurgeryConsentId=$consentFormInfoId&doc_name=".$_REQUEST['doc_name'],"","Move To Signed Surgery Consent Forms","","","",$showInfo,$consentDateTime,$operatorName,"",$apptDt,"",true);
			}
			$c=$p;
		}
		$b=$p;
	}
	$p++;
	//END TRASH FOLDER
	//END SIGNED SURGERY CONSENT FORM
}
if(!$p) { $p=1;}
if($_REQUEST["doc_name"]=="signed_package") {
	
	//START PACKAGE FORMS
	$p_pack = $p+1;
	$tree->addToArray($p_pack,"Signed Package",0,"","",$initSignedPackageClass);
	$qry = "SELECT distinct DATE_FORMAT(form_created_date, '".get_sql_date_format('','Y','-')."') formCreatedDate, chart_procedure_id  from patient_consent_form_information where patient_id=$patient_id AND movedToTrash = 0 AND package_category_id!='0' ORDER BY form_created_date desc" ;
	$patientSignConsentFormCreatedDate = get_array_records_query($qry);
	$h_pack=$p_pack+1;
	for($z=0;$z<count($patientSignConsentFormCreatedDate);$z++){
		$r_pack = $h_pack;
		$formCreatedDate=$patientSignConsentFormCreatedDate[$z]['formCreatedDate'];
		$tree->addToArray($h_pack,$formCreatedDate,$p_pack,"","","icon-folder-filled");	
		$h_pack++;
		$dbCreatedDate = getDateFormatDB($formCreatedDate,'yyyy-mm-dd');
		$qryPackId = "SELECT DISTINCT pcfi.package_category_id,cp.package_category_name
						FROM patient_consent_form_information pcfi 
						LEFT JOIN consent_package cp ON (cp.package_category_id = pcfi.package_category_id)
						LEFT JOIN users u ON(u.id = pcfi.operator_id)
						WHERE pcfi.patient_id=$patient_id 
						AND DATE_FORMAT(pcfi.form_created_date, '".get_sql_date_format('','Y','-')."')='$formCreatedDate' 
						AND pcfi.movedToTrash = '0' 
						AND pcfi.package_category_id!='0' 
						ORDER BY pcfi.form_created_date desc  " ;
		$qryPackIdDetail = get_array_records_query($qryPackId);
		for($a=0;$a<count($qryPackIdDetail);$a++){
			$s_pack = $h_pack;
			$package_category_id 	= $qryPackIdDetail[$a]['package_category_id'];
			$package_category_name 	= $qryPackIdDetail[$a]['package_category_name'];
			//$tree->addToArray($h_pack,$package_category_name,$r_pack);
			$fax_image = "";
			$showInfo  = "";	
			if(is_updox('fax') || is_interfax()) {
				$fax_image = "sendfax-icon";
				$showInfo  = "yes";	
			}
			
			$tree->addToArray($h_pack,$package_category_name.' ',$r_pack,"","","icon-folder-filled",$fax_image,"consent_send_fax.php?show_fax_popup=yes&package_category_id=".$package_category_id."&db_form_created_date=".$dbCreatedDate,"consent_data","Send Fax","","","",$showInfo,"","");
			$h_pack++;
		
			$qrynew_pack = "SELECT pcfi.consent_form_id,pcfi.consent_form_name,pcfi.form_created_date,pcfi.form_information_id,pcfi.operator_id,pcfi.modified_operator_id,pcfi.modified_form_created_date, pcfi.chart_procedure_id, pcfi.package_category_id, DATE_FORMAT(pcfi.form_created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consent_date_time,  
						concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
						FROM patient_consent_form_information pcfi 
						LEFT JOIN users u ON(u.id = pcfi.operator_id)
						WHERE pcfi.patient_id=$patient_id 
						AND DATE_FORMAT(pcfi.form_created_date, '".get_sql_date_format('','Y','-')."')='$formCreatedDate' 
						AND pcfi.movedToTrash = '0' 
						AND pcfi.package_category_id!='0' 
						AND pcfi.package_category_id='".$package_category_id."' 
						ORDER BY pcfi.form_created_date DESC  " ;
			
			$patientSignPackageForm = get_array_records_query($qrynew_pack);
			for($b=0;$b<count($patientSignPackageForm);$b++){
				
				//Check procedure notes related consent form
				$chart_procedure_id = $patientSignPackageForm[$a]['chart_procedure_id'];
				if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
					//continue;
					//COMMENTED ABOVE LINE. iT WAS SKIPPING TO SHOW PENDING LETTERS UNTIL CHART IS FINALIZED.
				}
				//--
				
				$consentFormId = $patientSignPackageForm[$b]['consent_form_id'];
				$consentFormInfoId = $patientSignPackageForm[$b]['form_information_id'];
				$form_created_time=date("g:i A",strtotime($patientSignPackageForm[$b]['form_created_date']));
	
				$modified_operator_id = $patientSignPackageForm[$a]['modified_operator_id'];
				$modified_form_created_date = date("g:i A",strtotime($patientSignPackageForm[$a]['modified_form_created_date']));
				if($modified_operator_id) {
					$form_created_time 		= $modified_form_created_date; 
				}
							
				$consentFormName = $patientSignPackageForm[$b]['consent_form_name'];
				$showInfo		 = "yes";
				$consentDateTime = $patientSignPackageForm[$b]['consent_date_time'];
				$operatorName 	 = stripslashes($patientSignPackageForm[$b]['operator_name']);
				
				$tree->addToArray($h_pack,$consentFormName,$s_pack,"print_consent_form.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId","consent_data","pdf-icon","remove-icon",$GLOBALS['webroot']."/interface/patient_info/consent_forms/index.php?from=".$coming_from."&moveToTrashConsentId=$consentFormInfoId&doc_name=".$_REQUEST['doc_name'],"","Move To Trash","","","",$showInfo,$consentDateTime,$operatorName,"","","",true,"","","",true);
				$h_pack++;
				
			}
			$s_pack=$h_pack;
		}
		$r_pack=$h_pack;
	}
	$p_pack=$h_pack;
	$p=$p_pack;
	//END PACKAGE FORMS
}

//START CODE FROM CHECK-IN SCREEN
$andConsentOutboxQry = "";
if($_REQUEST["from"]!="checkin") {
	$andConsentOutboxQry = " AND section_name in('consent_form','consent_package','consult','complete_patient_record','savedconsult','prs_gl_rx','prs_cl_rx') ";
}
//END CODE FROM CHECK-IN SCREEN

if($_REQUEST["doc_name"]=="fax_outbox") {
	$p = $p+1;
	$h_pack=$p;
	//START Outgoing Fax
	$f_pack = $h_pack+1;
	$tree->addToArray($f_pack,"Fax Outbox",0,"","",$initFaxOutBoxClass);
	$patient_id = $_SESSION['patient'];
	//---- Get Patient Consent Forms Signed Date(s)-------
	$qry = "";
	$qry = "SELECT date_format(`cur_date_time`, '".get_sql_date_format('','Y','-')."') AS 'formCreatedDate' FROM `send_fax_log_tbl` WHERE `patient_id`=".$patient_id." AND `status`=0 ".$andConsentOutboxQry." GROUP BY date_format(`cur_date_time`, '%m-%d-%Y') ORDER BY `cur_date_time` DESC" ; //AND `section_name` IN('consent_form', 'consent_package')
	$patientSignConsentFormCreatedDate = get_array_records_query($qry);
	$f1_pack=$f_pack+1;
	for($z=0;$z<count($patientSignConsentFormCreatedDate);$z++){
		
		$f1_pack++;
		$f = $f1_pack;
		$formCreatedDate=$patientSignConsentFormCreatedDate[$z]['formCreatedDate'];
		$a_pack = $f1_pack-1;
		$tree->addToArray($f1_pack,$formCreatedDate,$f_pack,"","","icon-folder-filled");	
		//---- Get Patient Signed Consent Forms Created Date(s)-------
		$qrynew = "SELECT sf.id, sf.template_name, sf.updox_id, sf.updox_status, sf.file_name, sf.operator_id, sf.cur_date_time AS 'formCreatedDate', sf.fax_number, LOWER(sf.updox_status) AS 'delivery_status', sf.section_name, DATE_FORMAT(sf.cur_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consent_date_time,
					concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name,sf.template_id, sf.section_pk_id, sf.section_name
					FROM `send_fax_log_tbl` sf
					LEFT JOIN users u ON(u.id = sf.operator_id)
					WHERE sf.patient_id=".$patient_id."
					AND DATE_FORMAT(sf.cur_date_time, '".get_sql_date_format('','Y','-')."')='".$formCreatedDate."' 
					AND sf.status=0
					".$andConsentOutboxQry."
					GROUP BY sf.updox_id
					ORDER BY sf.id DESC"; //AND sf.section_name IN('consent_form', 'consent_package')
		
		$patientSignConsentForm = get_array_records_query($qrynew);
		for($x=0;$x<count($patientSignConsentForm);$x++){
		
			$form_created_time=date("g:i A",strtotime($patientSignConsentForm[$x]['formCreatedDate']));
			$consentFormName = $patientSignConsentForm[$x]['template_name'];
			$consentFormName = ($patientSignConsentForm[$x]['section_name']=='consent_package')?'Signed Package':$consentFormName;
			$template_id 	 = $patientSignConsentForm[$x]['template_id'];
			$section_pk_id 	 = $patientSignConsentForm[$x]['section_pk_id'];
			$section_name 	 = $patientSignConsentForm[$x]['section_name'];
			$receiving_fax_no = explode(',', $patientSignConsentForm[$x]['fax_number']);
			$receiving_fax_no = array_map('core_phone_format', $receiving_fax_no);
			$receiving_fax_no = implode(', ', $receiving_fax_no);
			//$consentFormName = ($receiving_fax_no!=='')? $consentFormName.' - '.$receiving_fax_no : $consentFormName;
			$consentFormName = ($consentFormName)? $consentFormName : $receiving_fax_no;
			
			$showInfo  		 = "yes";	
			$consentDateTime = $patientSignConsentForm[$x]['consent_date_time'];
			$operatorName 	 = stripslashes($patientSignConsentForm[$x]['operator_name']);
			$confirm_img = ($patientSignConsentForm[$x]['updox_status']==='success')? 'glyphicon-ok': 'outgoing-fax';
			$link = data_path(1).'PatientId_'.$patient_id.'/Fax_log/'.$patientSignConsentForm[$x]['file_name'].'?hidebtn';
			if(!file_exists(data_path(1).'PatientId_'.$patient_id.'/Fax_log/'.$patientSignConsentForm[$x]['file_name']))
			{
				$link = data_path(1).'PatientId_'.$patient_id.'/fax_log/'.$patientSignConsentForm[$x]['file_name'].'?hidebtn';
			}
			//$link = $GLOBALS['webroot'].'/interface/main/uploaddir/PatientId_'.$patient_id.'/fax_log/'.$patientSignConsentForm[$x]['file_name'].'?hidebtn';
			if($section_name=='consent_form') {
				$link = "print_consent_form.php?consent_form_id=$template_id&consent=yes&form_information_id=$section_pk_id&hidebtn";
			}
			
			
			$f1_pack++;
			$consentFormName = trim(ucwords($consentFormName));
			$tree->addToArray($f1_pack,$consentFormName,$f, $link,"consent_data","pdf-icon", $confirm_img,"javascript:void(0)","","","","","",$showInfo,$consentDateTime,$operatorName,$receiving_fax_no,"","",true);
		}
		$f = $f1_pack;
	}
	$f_pack = $f1_pack;
	$p = $f_pack;
	//END Outgoing Fax
}

if($_REQUEST["doc_name"]=="fax_inbox") {
	$faxDocs = array();
	$faxFolderNames = array('pt_docs' => 'Pt. Docs', 'consent_forms' => 'Consent Form', 'consult_letters' => 'Consult Letter');

    $qry = "SELECT id, from_number, files, message, DATE_FORMAT(received_at, '%m-%d-%Y') AS 'date_received', 
            DATE_FORMAT(received_at, '%h:%i %p') AS 'time_received', 
            DATE_FORMAT(received_at, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consent_date_time,
            fax_folder  
            FROM inbound_fax 
            WHERE patient_id=".$patient_id." 
            AND del_status=0 
            AND fax_folder IN('consent_forms', 'consult_letters', 'pt_docs')
            ORDER BY received_at DESC"; //AND fax_folder='pt_docs'
    $faxes = imw_query($qry);
    if($faxes && imw_num_rows($faxes)>0){

        while($fax = imw_fetch_assoc($faxes)){
            $data 						= array();
            $data['id'] 				= $fax['id'];
            $data['from'] 				= core_phone_format($fax['from_number']);
            $data['link'] 				= data_path(1).'fax_files/'.$fax['files'];
            $data['files'] 				= $fax['files'];
            $data['time'] 				= $fax['time_received'];
            $data['consent_date_time'] 	= $fax['consent_date_time'];

            if(!isset($faxDocs[$fax['date_received']]))
                $faxDocs[$fax['date_received']] = array();
	        if(!isset($faxDocs[$fax['date_received']][$fax['fax_folder']]))
		        $faxDocs[$fax['date_received']][$fax['fax_folder']] = array();

            array_push($faxDocs[$fax['date_received']][$fax['fax_folder']], $data);
        }
    }
    $p++;
    $tree->addToArray($p,"Fax Inbox",0,"","",$initFaxInBoxClass);
    $a=$p;
    foreach($faxDocs as $fax_date => $faxFolders) {
	    $a++;
	    $tree->addToArray( $a, $fax_date, $p, "", "", "icon-folder-filled" );
	    $b = $a;

        foreach ( $faxFolders as $faxFolder => $faxes ) {
	        $b++;
	        $tree->addToArray( $b, $faxFolderNames[$faxFolder], $a, "", "", "icon-folder-filled" );
	        $c = $b;
	        foreach ( $faxes as $keyCnt => $fax ) {
		        $c++;
		        $fax_id          = $fax["id"];
		        $fax_files       = $fax["files"];
		        $fax_link        = $fax["link"];
		        $showInfo        = "yes";
		        $consentDateTime = $fax['consent_date_time'];
		        $faxNumber       = $fax['from'];

		        $operatorName = "";

		        $tree->addToArray( $c, $fax_files, $b, $fax_link, "consent_data", "pdf-icon", "restore-icon", $GLOBALS['webroot'] . "/interface/patient_info/consent_forms/index.php?moveToPendingFax=" . $fax_id . "&doc_name=" . $_REQUEST['doc_name'], "", "Move to Pending", "", "", "", $showInfo, $consentDateTime, "", $faxNumber,"","",true);
	        }
	        $b = $c;
        }
	    $a = $b;
    }
    $p=$a;
}

if($_REQUEST["doc_name"]=="consent_template") {		
	$p++;
	if(!$subTemplateCnt) {
		$subTemplateCnt = $p;
	}	
	$p++;
	$a=$p;
	$tree->addToArray($p,"Consent Templates",$subTemplateCnt,"","","icon-folder","","","","","","","","","","","","","active");
	//---- get consent forms -------
	$qry1 = "SELECT cat_id,category_name from consent_category";
	if($coming_from=='checkin'){
		$addToqry1 = " WHERE section='".$coming_from."'";
	}
	$qry1 .= $addToqry1." order by category_name";
	$consentCatName = get_array_records_query($qry1);
	$c = $p;
	$d = 0;
	for($i=0;$i<count($consentCatName);$i++){
		$consentCategoryNameId = $consentCatName[$i]['cat_id'];
		$consentCategoryName = trim(ucwords($consentCatName[$i]['category_name']));
		$c++;
		$tree->addToArray($c,$consentCategoryName,$p,"");
		$d = $c;
		$qry2 = "select consent_form_id,consent_form_name from consent_form where consent_form_status = 'Active' and cat_id = '$consentCategoryNameId' order by consent_form_name";
		$consentDetail = get_array_records_query($qry2);
		for($a=0;$a<count($consentDetail);$a++){
			$consentFormId = $consentDetail[$a]['consent_form_id'];
			$consentFormName = trim(ucwords($consentDetail[$a]['consent_form_name']));
			$d++;
			$tree->addToArray($d,$consentFormName,$c,"consentFormDetails.php?consent_form_id=$consentFormId","consent_data","glyphicon-open-file");
		}
		$c=$d;
	}
	$p=$c;
}
if($_REQUEST["doc_name"]=="package_template") {			
	if(!$subTemplateCnt) {
		$subTemplateCnt = $p;
	}	
	$p++;
	$p_pack=$p+1;
	$qryPackage = "SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE delete_status!='yes' AND package_consent_form!='' ORDER BY package_category_name" ;
	$resPackage = get_array_records_query($qryPackage);
	$tree->addToArray($p_pack,"Package Templates",$subTemplateCnt,"","","icon-folder","","","","","","","","","","","","","active");
	$h_pack=$p_pack+1;
	for($z_pack=0;$z_pack<count($resPackage);$z_pack++){
		$packageCategoryId = $resPackage[$z_pack]['package_category_id'];
		$packageName = $resPackage[$z_pack]['package_category_name'];
		$packageConsentForm = trim($resPackage[$z_pack]['package_consent_form']);
		$h_cons = $h_pack;
		$showInfo = "yes";
		$tree->addToArray($h_pack,$packageName." ",$p_pack,"","","icon-folder","print-icon-doc","print_package.php?package_category_id=$packageCategoryId","consent_data","Print Package","","","",$showInfo,"","");
		$h_pack++;
		$packageConsentFormArr = array();
		if($packageConsentForm) { $packageConsentFormArr = explode(",",$packageConsentForm);}
		for($z_cons=0;$z_cons<count($packageConsentFormArr);$z_cons++){
			$packageConsentFormId = $packageConsentFormArr[$z_cons];
			$packageConsentFormName = $consFormArr[$packageConsentFormId];
			$tree->addToArray($h_pack,$packageConsentFormName,$h_cons,"consentFormDetails.php?consent_form_id=$packageConsentFormId&package_category_id=$packageCategoryId","consent_data","glyphicon-open-file");
			$h_pack++;
		}
		$h_cons = $h_pack;
	}
	$p_pack = $h_pack;
	$p=$p_pack;
}

include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
$p++;
?>
<script type="text/javascript">
	
	$(document).ready(function(e) {
		$('[data-toggle="tooltip"]').tooltip({container:'body'});  
		$("#scroll_bottom").click(function(){//alert($('#consent_form_div')[0].scrollHeight)
			var sc_top=$("#scroll_val").val();
			sc_top=parseInt(sc_top)+parseInt(50);
			if(sc_top>0)$("#scroll_val").val(sc_top);
			$('#consent_form_div').scrollTop(sc_top);
		});
		
		$("#scroll_top").click(function(){
			var sc_top=$("#scroll_val").val();
			sc_top=parseInt(sc_top)-parseInt(50);
			if(sc_top>0)$("#scroll_val").val(sc_top);
			$('#consent_form_div').scrollTop(sc_top);
		});
		
		
		//START CODE FOR CONSENT FORM AT CHECKIN SCREEN - HIDE/SHOW BUTTONS
		var coming_from = "<?php echo $_REQUEST["from"];?>";
		var doc_name_new = "<?php echo $_REQUEST["doc_name"];?>";
		top.$('#module_buttons_new').show();
		if(coming_from=='checkin' && top.all_data && doc_name_new !="consent_template" && doc_name_new !="package_template"){
			top.$('#module_buttons_new').hide();
		}
		//END CODE FOR CONSENT FORM AT CHECKIN SCREEN - HIDE/SHOW BUTTONS
		
		collapseAll();
	});
	
	function restoreFax(fax_id, cnfrm){
		var doc_name = '<?php echo $_REQUEST["doc_name"];?>';
		if (typeof(cnfrm)!="undefined" && cnfrm===true) {
			window.location.href = top.JS_WEB_ROOT_PATH +"/interface/patient_info/consent_forms/index.php?moveToPendingFax="+fax_id+"&cnfrm=true&doc_name="+doc_name;
		}
	}
	
</script>
<?php
$tree->writeCSS();
$tree->writeJavascript();
?>
<div id="consent_form_div">
<?php if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true || $this_device=="mobile") { ?>
<input type="button"  id="scroll_bottom" value="&dArr;" class="btn btn-primary pull-right" style="-webkit-appearance: none; position:absolute; right:10px; top:35px; font-weight:bold;">
<input type="button"  id="scroll_top" value="&uArr;" class="btn btn-primary pull-right" style="-webkit-appearance: none; position:absolute; right:10px; top:0; font-weight:bold;">
<input type="hidden" size="2" id="scroll_val" value="10">
<?php
}
$tree->drawTree();
if($coming_from=='checkin'){
//$tab_color = check_consent_tab_color();
?>
<script type="text/javascript">
	top.change_tab_color('Consent_Forms','tab_<?php echo $tab_color;?>');
</script>
<?php }?>
</div>