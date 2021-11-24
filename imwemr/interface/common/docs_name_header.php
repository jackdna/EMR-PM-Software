<?php
include_once($GLOBALS['fileroot']."/interface/common/docs_name_common.php");

switch($_REQUEST["doc_name"]) {
	case "favourite_docs":	
		$pg_title = $pgTlFavourite;
	break;	
	case "signed_consent":	
		$pg_title = $pgTlSigned;
	break;
	case "signed_package":	
		$pg_title = $pgTlSignedPackage;
	break;
	case "view_consult":	
		$pg_title = $pgTlConsultLetter;
	break;
	case "view_ccda":	
		$pg_title = $pgTlCCDA;
	break;
	case "fax_outbox":	
		$pg_title = $pgTlFaxOutbox;
	break;
	case "fax_inbox":	
		$pg_title = $pgTlFaxInbox;
	break;
	case "view_pt_docs":	
		$pg_title = $pgTlPtDocs;
	break;
	case "scan_docs":	
		$pg_title = $pgTlScanDocs;
	break;
	case "view_operative_note":	
		$pg_title = $pgTlOperativeNote;
	break;
	case "view_pt_instruction_docs":	
		$pg_title = $pgTlPtInstructionDocs;
	break;
	case "multi_upload":	
		$pg_title = $pgTlMultiUpload;
	break;
	case "consent_template":	
		$pg_title = $pgTlConsentTemplates;
	break;
	case "package_template":	
		$pg_title = $pgTlPackageTemplate;
	break;
	case "surgery_consent_template":	
		$pg_title = $pgTlSurgeryConsentTemplates;
	break;
	case "pt_docs_template":	
		$pg_title = $pgTlPtDocsTemplate;
	break;
	case "pt_instruction_template":	
		$pg_title = $pgTlPtInstructionTemplate;
	break;
}
$showLoadingImageScript = " top.$('#div_loading_image').show(); ";

$pgTitleMainExists = false;
$tmpTitleArr = array();
foreach($pgTitleMainArr as $pgTitleMainKey => $pgTitleMainVal) {
	if($pg_title == $pgTitleMainKey) {
		$pgTitleMainExists = true;
	}
	if($pgTitleMainExists == false) {
		$tmpTitleArr[] = $pgTitleMainKey;
		$tmpTitleRedirectScriptArr[$pgTitleMainKey] = $pgTitleMainVal;
	}
}

if($pg_title != $pgTlFavourite && in_array($pgTlFavourite,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlFavourite];
	$tree->addToArray($p,$pgTlFavourite,0,$redirectScript,"",$initFavDocsClass);
}
if($pg_title != $pgTlSigned && in_array($pgTlSigned,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_consent');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlSigned];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=signed_consent&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlSigned.$initSignedConsentExists,0,$redirectScript,"",$initSignedConsentClass);
}
if($pg_title != $pgTlSignedPackage && in_array($pgTlSignedPackage,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=signed_package');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlSignedPackage];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=signed_package&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlSignedPackage,0,$redirectScript,"",$initSignedPackageClass);
}
if($pg_title != $pgTlConsultLetter && in_array($pgTlConsultLetter,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlConsultLetter];
	$tree->addToArray($p,$pgTlConsultLetter,0,$redirectScript,"",$initConsultLetterClass);
}
if($pg_title != $pgTlCCDA && in_array($pgTlCCDA,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlCCDA];
	$tree->addToArray($p,$pgTlCCDA,0,$redirectScript,"",$initCcdaClass);
}
if($pg_title != $pgTlFaxOutbox && in_array($pgTlFaxOutbox,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_outbox');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlFaxOutbox];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=fax_outbox&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlFaxOutbox,0,$redirectScript,"",$initFaxOutBoxClass);
}
if($pg_title != $pgTlFaxInbox && in_array($pgTlFaxInbox,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=fax_inbox');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlFaxInbox];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=fax_inbox&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlFaxInbox,0,$redirectScript,"",$initFaxInBoxClass);
}
if($pg_title != $pgTlPtDocs && in_array($pgTlPtDocs,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlPtDocs];
	$tree->addToArray($p,$pgTlPtDocs,0,$redirectScript,"",$initPtDocClass);
}
if($pg_title != $pgTlScanDocs && in_array($pgTlScanDocs,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlScanDocs];
	$tree->addToArray($p,$pgTlScanDocs,0,$redirectScript,"",$initScanDocsClass);
}
if($pg_title != $pgTlOperativeNote && in_array($pgTlOperativeNote,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlOperativeNote];
	$tree->addToArray($p,$pgTlOperativeNote,0,$redirectScript,"",$initOpNotesClass);
}
if($pg_title != $pgTlPtInstructionDocs && in_array($pgTlPtInstructionDocs,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlPtInstructionDocs];
	$tree->addToArray($p,$pgTlPtInstructionDocs,0,$redirectScript,"",$initInstructionClass);
}
if($pg_title != $pgTlMultiUpload && in_array($pgTlMultiUpload,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlMultiUpload];
	$tree->addToArray($p,$pgTlMultiUpload,0,$redirectScript,"",$initMultiUploadClass);
}
$p++;
if(in_array($pgTlConsentTemplates,$tmpTitleArr) || in_array($pgTlPackageTemplate,$tmpTitleArr) || in_array($pgTlSurgeryConsentTemplates,$tmpTitleArr) || in_array($pgTlPtDocsTemplate,$tmpTitleArr) || in_array($pgTlPtInstructionTemplate,$tmpTitleArr)) {
	$tree->addToArray($p,"Template",0,"");
}
$subTemplateCnt=$p;
if($pg_title != $pgTlConsentTemplates && in_array($pgTlConsentTemplates,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=consent_template');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlConsentTemplates];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=consent_template&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlConsentTemplates,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPackageTemplate && in_array($pgTlPackageTemplate,$tmpTitleArr)) {
	$p++;
	//$redirectScript = "javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../patient_info/consent_forms/index.php?doc_name=package_template');";
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlPackageTemplate];
	if($_REQUEST["from"]=="checkin") {
		$redirectScript = "javascript:".$showLoadingImageScript."top.all_data.location.href='../consent_forms/index.php?doc_name=package_template&from=".$_REQUEST["from"]."';";	
	}
	$tree->addToArray($p,$pgTlPackageTemplate,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlSurgeryConsentTemplates && in_array($pgTlSurgeryConsentTemplates,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlSurgeryConsentTemplates];
	$tree->addToArray($p,$pgTlSurgeryConsentTemplates,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPtDocsTemplate && in_array($pgTlPtDocsTemplate,$tmpTitleArr)) {
	$p++;
	$redirectScript = $tmpTitleRedirectScriptArr[$pgTlPtDocsTemplate];
	$tree->addToArray($p,$pgTlPtDocsTemplate,$subTemplateCnt,$redirectScript,"","icon-folder","","","","","","","","","","","","","active");
}
if($pg_title != $pgTlPtInstructionTemplate && in_array($pgTlPtInstructionTemplate,$tmpTitleArr)) {
	//$p++;
	//$tree->addToArray($p,$pgTlPtInstructionTemplate,$subTemplateCnt,"javascript:top.core_set_pt_session(top.fmain,'".$_SESSION['patient']."','../chart_notes/pt_instructions/index_view.php?doc_name=pt_instruction_template');","","icon-folder","","","","","","","","","","","","","active");
}
$p++;
?>
