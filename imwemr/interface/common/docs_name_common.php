<?php
include_once($GLOBALS['fileroot']."/library/classes/work_view/PnReports.php");
$oPnRepInit = new PnReports;

$pgTlFavourite 					= "Favorite";
$pgTlSigned 					= "Signed";
$pgTlSignedPackage 				= "Signed Package";
$pgTlConsultLetter 				= "Consult Letter";
$pgTlCCDA	 					= "CCDA";
$pgTlFaxOutbox 					= "Fax Outbox";
$pgTlFaxInbox 					= "Fax Inbox";
$pgTlPtDocs 					= "Pt. Docs";
$pgTlScanDocs 					= "Scan Docs";
$pgTlOperativeNote 				= "Operative Notes";
$pgTlPtInstructionDocs 			= "Pt. Instruction Docs";
$pgTlMultiUpload 				= "Multi Upload";
$pgTlConsentTemplates 			= "Consent Templates";
$pgTlPackageTemplate 			= "Package Templates";
$pgTlSurgeryConsentTemplates 	= "Surgery Consent Templates";
$pgTlPtDocsTemplate 			= "Pt. Docs Templates";
$pgTlPtInstructionTemplate 		= "Pt. Instruction Templates";

$pgTitleMainArr = array($pgTlFavourite=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=favourite_docs');",
						$pgTlSigned=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_consent');",
						$pgTlSignedPackage=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_package');",
						$pgTlConsultLetter=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/consult_letter_page.php?doc_name=view_consult');",
						$pgTlCCDA=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=view_ccda');",
						$pgTlFaxOutbox=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_outbox');",
						$pgTlFaxInbox=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_inbox');",
						$pgTlPtDocs=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=view_pt_docs');",
						$pgTlScanDocs=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs');",
						$pgTlOperativeNote=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/progress_notes/pt_prog_note.php?doc_name=view_operative_note');",
						$pgTlPtInstructionDocs=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/pt_instructions/index_view.php?doc_name=view_pt_instruction_docs');",
						$pgTlMultiUpload=>"javascript:top.show_loading_image('show','-60','Please Wait');top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/multi_upload.php?doc_name=multi_upload');",
						$pgTlConsentTemplates=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=consent_template');",
						$pgTlPackageTemplate=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=package_template');",
						$pgTlSurgeryConsentTemplates=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/surgery_consent_forms/index.php?doc_name=surgery_consent_template');",
						$pgTlPtDocsTemplate=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=pt_docs_template');"
					);


if($_REQUEST["from"]=="checkin") {
	$pgTitleMainArr = array();
	$pgTitleMainArr = array($pgTlSigned=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_consent');",
							$pgTlSignedPackage=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_package');",
							$pgTlFaxOutbox=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_outbox');",
							$pgTlFaxInbox=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_inbox');",
							$pgTlConsentTemplates=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=consent_template');",
							$pgTlPackageTemplate=>"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=package_template');"
							);
}

$docNotExistClass = "icon-folder";
$docExistClass = "icon-folder-filled";
$docExistArray = array();

// Favourite Docs
$favDocsCount = getScanDocsCount($_SESSION['patient'],'',true);
$initFavDocsClass =  ($favDocsCount) ? $docExistClass : $docNotExistClass;
$docExistArray[$pgTlFavourite] = $initFavDocsClass;


//SIGNED CONSENT
$initSignedConsentClass = $docNotExistClass;
$initSignedConsentQry = "SELECT form_information_id FROM patient_consent_form_information WHERE patient_id = '".$_SESSION['patient']."' AND package_category_id = 0 and movedToTrash = 0 LIMIT 0,1";
$initSignedConsentRes = imw_query($initSignedConsentQry) or die($initSignedConsentQry.imw_error());
if(imw_num_rows($initSignedConsentRes)>0) {
	$initSignedConsentClass = $docExistClass;	
	$docExistArray[$pgTlSigned] = $initSignedConsentClass;
}

//SIGNED SURGERY CONSENT FILLED FORM
$initSignedConsentQry = "SELECT surgery_consent_id from surgery_consent_filled_form where patient_id='".$_SESSION['patient']."' and movedToTrash = 0 LIMIT 0,1" ;
$initSignedConsentRes = imw_query($initSignedConsentQry) or die($initSignedConsentQry.imw_error());
if(imw_num_rows($initSignedConsentRes)>0) {
	$initSignedConsentClass = $docExistClass;	
	$docExistArray[$pgTlSigned] = $initSignedConsentClass;
}

//SIGNED PACKAGE
$initSignedPackageClass = $docNotExistClass;
$initSignedPackageQry = "SELECT form_information_id FROM patient_consent_form_information WHERE patient_id = '".$_SESSION['patient']."' AND package_category_id != 0  AND movedToTrash = 0 LIMIT 0,1";
$initSignedPackageRes = imw_query($initSignedPackageQry) or die($initSignedPackageQry.imw_error());
if(imw_num_rows($initSignedPackageRes)>0) {
	$initSignedPackageClass = $docExistClass;
	$docExistArray[$pgTlSignedPackage] = $initSignedPackageClass;
}

//CONSULT LETTER
function makeUniqueInfoConsult($array){
	$dupes=array();
	foreach($array as $values){
		if(!in_array($values,$dupes))
			$dupes[]=$values;
	}
	return $dupes;
}
$initConsultLetterClass = $docNotExistClass;
$qry = "SELECT distinct DATE_FORMAT(date, '".get_sql_date_format()."') sortDate, DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."') formCreatedDate from patient_consult_letter_tbl  where patient_id='".$_SESSION['patient']."' and status = '0' ORDER BY `date` desc" ;
$patientConsultLetterCreatedDate = get_array_records_query($qry);

$qryScanUploadConsultLetter = "SELECT distinct DATE_FORMAT(created_date, '".get_sql_date_format('','Y','/')."') formCreatedDate 
		from ".constant("IMEDIC_SCAN_DB").".scans  where patient_id=$patient_id 
		AND image_form='consultLetter' and status != '1' ORDER BY created_date desc" ;
$ptScanUploadConsultLetterCreatedDate = get_array_records_query($qryScanUploadConsultLetter);
$patientConsultLetterCreatedDate = array_merge($patientConsultLetterCreatedDate,$ptScanUploadConsultLetterCreatedDate);
$patientConsultLetterCreatedDate = makeUniqueInfoConsult($patientConsultLetterCreatedDate);
if(count($patientConsultLetterCreatedDate)>0) {
	$initConsultLetterClass = $docExistClass;
	$docExistArray[$pgTlConsultLetter] = $initConsultLetterClass;
}

//CCDA
$initCcdaClass = $docNotExistClass;
$ccdaQry = "SELECT cd.id FROM `ccda_docs` cd LEFT JOIN users u ON(u.id = cd.operator_id) WHERE `patient_id`='".$_SESSION['patient']."' AND `deletedBy`=0";
$ccdaRes = imw_query($ccdaQry);
if($ccdaRes && imw_num_rows($ccdaRes)>0){
	$initCcdaClass = $docExistClass;
	$docExistArray[$pgTlCCDA] = $initCcdaClass;
}

//FAX OUTBOX
$initFaxOutBoxClass = $docNotExistClass;
$andConsentOutboxQry = " AND section_name in('consent_form','consent_package','consult') ";
$faxOutBoxQry = "SELECT date_format(`cur_date_time`, '".get_sql_date_format('','Y','-')."') AS 'formCreatedDate' FROM `send_fax_log_tbl` WHERE `patient_id`=".$_SESSION['patient']." AND `status`=0 ".$andConsentOutboxQry." GROUP BY date_format(`cur_date_time`, '%m-%d-%Y') ORDER BY `cur_date_time` DESC" ;
$faxOutBoxRes = imw_query($faxOutBoxQry);
if($faxOutBoxRes && imw_num_rows($faxOutBoxRes)>0){
	$initFaxOutBoxClass = $docExistClass;
	$docExistArray[$pgTlFaxOutbox] = $initFaxOutBoxClass;
}

//FAX INBOX
$initFaxInBoxClass = $docNotExistClass;
$faxInBoxQry = "SELECT id FROM inbound_fax WHERE patient_id='".$_SESSION['patient']."' AND del_status=0 AND fax_folder IN('consent_forms', 'consult_letters', 'pt_docs') ORDER BY received_at DESC"; 
$faxInBoxRes = imw_query($faxInBoxQry);
if($faxInBoxRes && imw_num_rows($faxInBoxRes)>0){
	$initFaxInBoxClass = $docExistClass;
	$docExistArray[$pgTlFaxInbox] = $initFaxInBoxClass;
}

//PT. DOCS - ptdocs
$initPtDocClass = $docNotExistClass;
$initPtDocScanClass = $docNotExistClass;
$initPtDocQry = "select pd.pt_doc_primary_template_id FROM pt_docs_patient_templates pd 
					JOIN pt_docs_template pdt ON(pdt.pt_docs_template_id = pd.pt_doc_primary_template_id)
					LEFT JOIN users u ON(u.id = pd.operator_id)
					WHERE pd.patient_id = '".$_SESSION['patient']."' 
					AND pd.delete_status = '0'
					ORDER BY pd.pt_docs_patient_templates_id DESC";
$initPtDocRes = imw_query($initPtDocQry);
if($initPtDocRes && imw_num_rows($initPtDocRes)>0){
	$initPtDocClass = $docExistClass;
	$initPtDocScanClass = $docExistClass;
	$docExistArray[$pgTlPtDocs] = $initPtDocClass;
}

//PT. DOCS - collection letter
$initPtDocCollectionClass = $docNotExistClass;
$initPtDocCollectionLetterQry="SELECT pd.id FROM pt_docs_collection_letters pd
								JOIN collection_letter_template clt ON (clt.id= pd.template_id)
								LEFT JOIN users u ON(u.id = pd.operator_id)
								WHERE pd.patient_id='".$_SESSION['patient']."' 
								AND pd.delete_status = '0' 
								ORDER BY pd.created_date DESC";
$initPtDocCollectionLetterRes=imw_query($initPtDocCollectionLetterQry);
if($initPtDocCollectionLetterRes && imw_num_rows($initPtDocCollectionLetterRes)>0){
	$initPtDocClass = $docExistClass;
	$initPtDocCollectionClass = $docExistClass;
	$docExistArray[$pgTlPtDocs] = $initPtDocClass;
}

//PT. DOCS - Insurance Cards
$initPtDocInsClass = $docNotExistClass;
$initPtDocInsQry="SELECT ins.id FROM insurance_data ins
								JOIN insurance_case inc ON (ins.ins_caseid= inc.ins_caseid and inc.del_status = 0  )
								WHERE ins.pid=".(int)$_SESSION['patient']." 
								AND (ins.scan_card <> '' OR ins.scan_card2 <> '')
								ORDER BY inc.ins_caseid Desc";
$initPtDocInsRes=imw_query($initPtDocInsQry);
if($initPtDocInsRes && imw_num_rows($initPtDocInsRes)>0){
	$initPtDocClass = $docExistClass;
	$initPtDocInsClass = $docExistClass;
	$docExistArray[$pgTlPtDocs] = $initPtDocClass;
}

//PT. DOCS - Interpretation
$initPtDocIntrClass = $docNotExistClass;	
$initPtDocInsQry= "SELECT 
        c1.patient_id,c1.form_id,c1.exam_name,
        c2.id,
        c2.order_by,
        c2.order_on,
        c2.test_type,
        c2.assessment,
        c2.dx, c2.dxid,
        c2.plan,
        c3.drawing_image_path
          FROM chart_drawings c1 
                INNER JOIN chart_draw_inter_report c2 ON c1.id = c2.id_chart_draw
                INNER JOIN ".constant("IMEDIC_SCAN_DB").".idoc_drawing c3 ON c3.id = c1.idoc_drawing_id
                WHERE c1.patient_id = '".$_SESSION['patient']."' AND c1.purged='0'
                AND c1.exam_name='FundusExam' AND c2.del_by='0'
                ORDER BY c2.order_on DESC";	
$initPtDocInsRes=imw_query($initPtDocInsQry);
if($initPtDocInsRes && imw_num_rows($initPtDocInsRes)>0){
	$initPtDocClass = $docExistClass;
	$initPtDocIntrClass = $docExistClass;
	$docExistArray[$pgTlPtDocs] = $initPtDocClass;
}

//PT. DOCS - Patient Orders
$initPtDocPtOrderClass = $docNotExistClass;
$initPtDocPtOrderQry ="SELECT po.print_orders_data_id FROM print_orders_data po
						LEFT JOIN users u ON(u.id = po.created_by)
						WHERE po.patient_id = '".$_SESSION['patient']."'
						AND po.delete_status = '0'";
$initPtDocPtOrderRes=imw_query($initPtDocPtOrderQry);
if($initPtDocPtOrderRes && imw_num_rows($initPtDocPtOrderRes)>0){
	$initPtDocClass = $docExistClass;
	$initPtDocPtOrderClass = $docExistClass;
	$docExistArray[$pgTlPtDocs] = $initPtDocClass;
}

//OPERATIVE NOTES
$initOpNotesClass = $docNotExistClass;
list($arrTempInt,$arrTrashInt) = $oPnRepInit->getPtReports($_SESSION['patient']);
foreach($arrTempInt as $keyInt => $valInt){
	$initOpNotesClass = $docExistClass;
	$docExistArray[$pgTlOperativeNote] = $initOpNotesClass;
}

//INSTURCTION SHEET
$initInstructionClass = $docNotExistClass;
$initInstructionQry = "SELECT distinct DATE_FORMAT(A.date_time, '".get_sql_date_format('','Y','-')."') date_time from document_patient_rel A where A.p_id ='".$patient_id."' AND A.status = '0' AND A.doc_id!='0' ORDER BY A.date_time desc" ;
$initInstructionRes=imw_query($initInstructionQry);
if($initInstructionRes && imw_num_rows($initInstructionRes)>0){
	$initInstructionClass = $docExistClass;
	$docExistArray[$pgTlPtInstructionDocs] = $initInstructionClass;
}

//MULTIUPLOAD
$initMultiUploadClass = $docNotExistClass;
$initFilePath 	= $GLOBALS['fileroot'].'/data/'.constant("PRACTICE_PATH").'/pdfSplit';
if ($initHandle = @opendir($initFilePath)){
	while (false !== ($initFile = readdir($initHandle))) {
		$arrFiles[] = $initFile;
	}
	foreach($arrFiles as $initNewfile){	
		if ($initNewfile != "." && $initNewfile != ".." && $initNewfile != "tmp" && stristr($initNewfile,".pdf") ){
			$initMultiUploadClass = $docExistClass;
			$docExistArray[$pgTlMultiUpload] = $initMultiUploadClass;
		}
	}

}

// Scan Docs
$scanDocsCount = getScanDocsCount($_SESSION['patient']);
$initScanDocsClass =  ($scanDocsCount) ? $docExistClass : $docNotExistClass;
$docExistArray[$pgTlScanDocs] = $initScanDocsClass;

?>