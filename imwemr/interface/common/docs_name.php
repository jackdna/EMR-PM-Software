<?php
if($pg_title != $pgTlFavourite && !in_array($pgTlFavourite,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fav_docs');";
	$tree->addToArray($p,$pgTlFavourite,0,$redirectScript,"",$initFavDocsClass);
}
if($pg_title != $pgTlSigned && !in_array($pgTlSigned,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_consent');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=signed_consent&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlSigned,0,$redirectScript,"",$initSignedConsentClass);
}
if($pg_title != $pgTlSignedPackage && !in_array($pgTlSignedPackage,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_package');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=signed_package&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlSignedPackage,0,$redirectScript,"",$initSignedPackageClass);
}

if($pg_title != $pgTlConsultLetter && !in_array($pgTlConsultLetter,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlConsultLetter,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/consult_letter_page.php?doc_name=view_consult');","",$initConsultLetterClass);
}
if($pg_title != $pgTlCCDA && !in_array($pgTlCCDA,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlCCDA,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=view_ccda');","",$initCcdaClass);
}
if($pg_title != $pgTlFaxOutbox && !in_array($pgTlFaxOutbox,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_outbox');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=fax_outbox&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlFaxOutbox,0,$redirectScript,"",$initFaxOutBoxClass);
}
if($pg_title != $pgTlFaxInbox && !in_array($pgTlFaxInbox,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_inbox');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=fax_inbox&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlFaxInbox,0,$redirectScript,"",$initFaxInBoxClass);
}

if($pg_title != $pgTlPtDocs && !in_array($pgTlPtDocs,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlPtDocs,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=view_pt_docs');","",$initPtDocClass);
}
if($pg_title != $pgTlScanDocs && !in_array($pgTlScanDocs,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	//$tree->addToArray($p,$pgTlScanDocs,0,"javascript:top.popup_win('".$GLOBALS['webroot']."/interface/chart_notes/scan_docs/index.php');");
	$tree->addToArray($p,$pgTlScanDocs,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs');","",$initScanDocsClass);
}
if($pg_title != $pgTlOperativeNote && !in_array($pgTlOperativeNote,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlOperativeNote,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/progress_notes/pt_prog_note.php?doc_name=view_operative_note');","",$initOpNotesClass);
}
if($pg_title != $pgTlPtInstructionDocs && !in_array($pgTlPtInstructionDocs,$tmpTitleArr) && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlPtInstructionDocs,0,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/pt_instructions/index_view.php?doc_name=view_pt_instruction_docs');","",$initInstructionClass);
}
if($pg_title != $pgTlMultiUpload && !in_array($pgTlMultiUpload,$tmpTitleArr) && $_REQUEST["from"]!="checkin")  {
	$p++;
	$tree->addToArray($p,$pgTlMultiUpload,0,"javascript:top.show_loading_image('show','-60','Please Wait');top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/multi_upload.php?doc_name=multi_upload');","",$initMultiUploadClass);
}

$p++;
if(!$subTemplateCnt) {
	$subTemplateCnt = $p;
}
if(!in_array($pgTlConsentTemplates,$tmpTitleArr) && !in_array($pgTlPackageTemplate,$tmpTitleArr) && !in_array($pgTlSurgeryConsentTemplates,$tmpTitleArr) && !in_array($pgTlPtDocsTemplate,$tmpTitleArr)) {
	$tree->addToArray($subTemplateCnt,"Template",0,"");
}
if($pg_title != $pgTlConsentTemplates && !in_array($pgTlConsentTemplates,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=consent_template');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=consent_template&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlConsentTemplates,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPackageTemplate && !in_array($pgTlPackageTemplate,$tmpTitleArr)) {
	$p++;
	$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=package_template');";
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=package_template&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlPackageTemplate,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlSurgeryConsentTemplates && !in_array($pgTlSurgeryConsentTemplates,$tmpTitleArr)  && $_REQUEST["from"]!="checkin")  {
	$p++;
	$tree->addToArray($p,$pgTlSurgeryConsentTemplates,$subTemplateCnt,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/surgery_consent_forms/index.php?doc_name=surgery_consent_template');","","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPtDocsTemplate && !in_array($pgTlPtDocsTemplate,$tmpTitleArr)  && $_REQUEST["from"]!="checkin") {
	$p++;
	$tree->addToArray($p,$pgTlPtDocsTemplate,$subTemplateCnt,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/scan_docs/pt_docs.php?doc_name=pt_docs_template');","","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPtInstructionTemplate && !in_array($pgTlPtInstructionTemplate,$tmpTitleArr)  && $_REQUEST["from"]!="checkin") {
	//$p++;
	//$tree->addToArray($p,$pgTlPtInstructionTemplate,$subTemplateCnt,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/pt_instructions/index_view.php?doc_name=pt_instruction_template');","","icon-folder","","","","","","","","","","","","","active");
}
?>
