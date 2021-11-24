<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/html_to_pdf/html2pdf.class.php");

$updir=substr(data_path(), 0, -1);
$srcdir=substr(data_path(1), 0, -1);
$CurrDt = date("Y_m_d");
$crRes=imw_query("CREATE  TABLE surgery_consent_filled_form_".$CurrDt."  LIKE surgery_consent_filled_form ");
$insRes=imw_query("INSERT INTO surgery_consent_filled_form_".$CurrDt."  (SELECT *  FROM surgery_consent_filled_form) ");
$qry = "select surgery_consent_id, patient_id, iolink_pdf_path from surgery_consent_filled_form where iolink_pdf_path !='' order by form_created_date desc, patient_id asc";
$res=imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0){
	$a = 0;
	$totalDone = 0;
	while($row =imw_fetch_assoc($res)) {
		$a++;
		$surgery_consent_id  	= $row["surgery_consent_id"];
		$patient_id  			= $row["patient_id"];
		$iolink_pdf_path  		= $row["iolink_pdf_path"];
		if(strstr($iolink_pdf_path,"addons/iOLink")){
			list($iolink_path1,$iolink_path2) = explode("addons/iOLink",$iolink_pdf_path);
			//echo '@@'.$iolink_path1.'@@'.$iolink_path2;
			$iolink_path1_new = $iolink_pdf_path_new = "";
			if(trim($iolink_path1)!="") {
				$iolink_path1_new = str_ireplace($iolink_path1,$updir.'/iOLink',$iolink_path1);
				$iolink_pdf_path_new = $iolink_path1_new.$iolink_path2;
				$savQry = "UPDATE surgery_consent_filled_form SET iolink_pdf_path = '".$iolink_pdf_path_new."' WHERE surgery_consent_id = '".$surgery_consent_id."' ";
				echo('<br>'.$savQry);
				$savRes=imw_query($savQry) or die(imw_error());
				if($savRes) {
					$totalDone++;	
				}
				if(!file_exists($iolink_pdf_path_new) && file_exists($iolink_pdf_path)) {
					$file_content = file_get_contents($iolink_pdf_path);
					//$iolink_pdf_path_new = urlencode($iolink_pdf_path_new);
					file_put_contents($iolink_pdf_path_new,$file_content);
				}
				//if($a==2) {die('<br>hlo '.$savQry." @@ ".$iolink_pdf_path);}
			}
		}
	}
}
die("<br><br>Done ".$totalDone);
//echo $savQry.'<br>';

echo "<br>DB entries done. Now adding pdf files in folder<br><hr>";
//die();

function refineContent($consent_form_content2,$patient_id, $practicePath) {
	$qry = "select *, date_format(DOB,'".get_sql_date_format()."') as pat_dob, 
										date_format(date,'".get_sql_date_format()."') as reg_date
										from patient_data where id = '". $patient_id."' ";
	$patientDetails = get_array_records_query($qry);

	$consent_form_content2 = html_entity_decode(html_entity_decode($consent_form_content2));
	$consent_form_content2 = str_ireplace('{PATIENT NAME TITLE}',ucwords($patientDetails[0]['title']),$consent_form_content2);
	
	//$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace("interface/common/html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot']."/library/common/html_to_pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/new_html2pdf/','',$consent_form_content2);
	if($GLOBALS['webroot']!=''){
		$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/data/'.$practicePath.'/PatientId_'.$patient_id.'/','../../data/'.$practicePath.'/PatientId_'.$patient_id.'/',$consent_form_content2);
	}
	$consent_form_content2 = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($webServerRootDirectoryName.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/redactor/images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/redactor/images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($protocol.$http_host.$web_root.'/interface/common/new_html2pdf/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/'.$htmlFolder.'/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/html2pdf/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/interface/common/new_html2pdf/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/html2pdf/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('interface/common/new_html2pdf/','../../data/'.$practicePath.'/',$consent_form_content2);
	//$consent_form_content2 = str_ireplace($web_root.'/data/'.$practicePath.'/PatientId_'.$patient_id.'/consent_forms/','../../data/'.$practicePath.'/PatientId_'.$patient_id.'/consent_forms/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/SigPlus_images/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace($GLOBALS['webroot'].'/interface/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('common/html2pdf/','',$consent_form_content2);
	$consent_form_content2 = str_ireplace('common/new_html2pdf/','',$consent_form_content2);
	
	$consent_form_content2 = str_ireplace('%20',' ',$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT FIRST NAME}',ucwords($patientDetails[0]['fname']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MIDDLE NAME}',ucwords($patientDetails[0]['mname']),$consent_form_content2);
	
	$consent_form_content2 = str_ireplace("{MIDDLE INITIAL}",ucwords($patientDetails[0]['mname']),$consent_form_content2);

	$consent_form_content2 = str_ireplace('{LAST NAME}',ucwords($patientDetails[0]['lname']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{SEX}',ucwords($patientDetails[0]['sex']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{DOB}',ucwords($patientDetails[0]['pat_dob']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT SS}',ucwords($patientDetails[0]['ss']),$consent_form_content2);
	
	//=============START WORK TO SHOW THE LAST 4 DIGIT OF PATIENT SS==========================
	if(trim($patientDetails[0]['ss'])!=''){
		$consent_form_content2 = str_ireplace('{PATIENT_SS4}',ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)),$consent_form_content2);
	}else{
		$consent_form_content2 = str_ireplace('{PATIENT_SS4}','',$consent_form_content2);
	}
	//===========================END WORK===================================================
	$consent_form_content2 = str_ireplace('{SURGEON NAME}',ucwords($phy_name),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PHYSICIAN NAME}',ucwords($phy_name_demo),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MARITAL STATUS}',ucwords($patientDetails[0]['status']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{ADDRESS1}',ucwords($patientDetails[0]['street']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{ADDRESS2}',ucwords($patientDetails[0]['street2']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{HOME PHONE}',ucwords($patientDetails[0]['phone_home']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{EMERGENCY CONTACT}',ucwords($patientDetails[0]['contact_relationship']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{EMERGENCY CONTACT PH}',ucwords($patientDetails[0]['phone_contact']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{MOBILE PHONE}',ucwords($patientDetails[0]['phone_cell']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{WORK PHONE}',ucwords($patientDetails[0]['phone_biz']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT CITY}',ucwords($patientDetails[0]['city']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT STATE}',ucwords($patientDetails[0]['state']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PATIENT ZIP}',ucwords($patientDetails[0]['postal_code']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{REGISTRATION DATE}',ucwords($patientDetails[0]['reg_date']),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PatientID}',$_SESSION['patient'],$consent_form_content2);
	
	$consent_form_content2 = str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{SITE}","<b>".$patientSite."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{PROCEDURE}","<b>".$patientProcedure."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$consent_form_content2);
	$consent_form_content2 = str_ireplace("{DATE}","<b>".get_date_format(date('Y-m-d'))."</b>",$consent_form_content2);
	
	$srgnSig = addslashes("{Surgeon's Signature}");
	$consent_form_content2= str_ireplace($srgnSig,"{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Surgeon's Signature}","{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Nurse's Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Anesthesiologist's Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Witness's Signature}"," ",$consent_form_content2);
	
	$consent_form_content2= str_ireplace("{Surgeon's&nbsp;Signature}","{SIGNATURE}",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Nurse's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Anesthesiologist's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Witness's&nbsp;Signature}"," ",$consent_form_content2);
	$consent_form_content2= str_ireplace("{Signature}","{SIGNATURE}",$consent_form_content2);
	

	$consent_form_content2 = str_ireplace('{PATIENT INITIAL}',ucwords($patient_initial),$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_XSMALL}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_SMALL}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_MEDIUM}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{TEXTBOX_LARGE}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('<p> <br />','',$consent_form_content2);
	$consent_form_content2 = stripslashes($consent_form_content2);
	
	$consent_form_content2 = str_ireplace('{SIGNATURE}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{PHYSICIAN SIGNATURE}',"",$consent_form_content2);
	$consent_form_content2 = str_ireplace('{WITNESS SIGNATURE}',"",$consent_form_content2);
	

	$inputValTextarea = explode('<textarea rows="2" cols="100" name="large',$consent_form_content2);
	//pre($inputValTextarea, 1);
	if(is_array($inputValTextarea)){
		for($i=1;$i<count($inputValTextarea);$i++){
			$consent_form_content2 = str_ireplace('<textarea rows="2" cols="100" name="large">','',$consent_form_content2);
			$consent_form_content2 = str_ireplace('<textarea rows="2" cols="100" name="large'.$i.'">','',$consent_form_content2);
			$consent_form_content2 = str_ireplace('</textarea>','',$consent_form_content2);
		}
	}
	
	$consent_form_content2 = str_ireplace($web_root.'/data/'.$practicePath.'/','../../data/'.$practicePath.'/',$consent_form_content2);
	$consent_form_content2 = str_ireplace($web_root.'/library/images/','../../library/images/',$consent_form_content2);
	$consent_form_content2 = str_ireplace('../../../library/images/','../../library/images/',$consent_form_content2);

	return $consent_form_content2;
}

$qry = "select surgery_consent_id, patient_id, iolink_pdf_path,form_created_date as patientCreatedDateTime,surgery_consent_name as patientConsentFormName,surgery_consent_data from surgery_consent_filled_form order by form_created_date desc, patient_id asc";
$res=imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0){
	$a = 0;
	$onePage=($_REQUEST['onePage'])?$_REQUEST['onePage']:false;
	$op = ($_REQUEST['op'])?$_REQUEST['op']:'p';
	$html2pdf = new HTML2PDF($op,'A4','en');
	while($row =imw_fetch_assoc($res)) {
		$a++;
		
		$surgery_consent_id  		= $row["surgery_consent_id"];
		$patient_id  				= $row["patient_id"];
		$savePdfFileName			= $row["iolink_pdf_path"];
		$patientCreatedDateTime		= $row["patientCreatedDateTime"];
		$patientConsentFormName		= $row["patientConsentFormName"];
		$consent_form_content2		= $row["surgery_consent_data"];
		$consent_form_content2 		= refineContent($consent_form_content2,$patient_id,constant('PRACTICE_PATH'));
		//../..../../data/shoreline
		$consent_form_content2 = str_ireplace('../..../../data/'.constant('PRACTICE_PATH'),$srcdir.'/',$consent_form_content2);
		$consent_form_content2 = str_ireplace('../../../data/'.constant('PRACTICE_PATH'),$srcdir.'/',$consent_form_content2);
		$consent_form_content2 = str_ireplace('../../data/'.constant('PRACTICE_PATH'),$srcdir.'/',$consent_form_content2);
		if(strncasecmp(PHP_OS, 'WIN', 3) == 0) {	//IF OPERATING SYSTEM IS WINDOW THEN
			$consent_form_content2 = utf8_decode($consent_form_content2);
		}
		$strContent			   		= $consent_form_content2;
		$oSaveFile 					= new SaveFile($patient_id);
		$iolinkDirPath 				= data_path().'iOLink/'. substr($oSaveFile->pDir,1);
		if(!is_dir($iolinkDirPath)) {
			mkdir($iolinkDirPath);	
		}
		if($savePdfFileName=="") {
			$patientCreatedDateTime = str_ireplace(" ","_",$patientCreatedDateTime);
			$patientCreatedDateTime = str_ireplace(":","-",$patientCreatedDateTime);			
			$patientConsentFormName = str_ireplace("&","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("\\","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("'","",$patientConsentFormName);
			$patientConsentFormName = str_ireplace("/","_",$patientConsentFormName);
			$pdfFileName 			= $patientConsentFormName.'_'.$patient_id.'_'.$patientCreatedDateTime.'.pdf';
			$pdfFilePath 			= urldecode($iolinkDirPath.'\\'.$pdfFileName);
			$pdfFilePath 			= str_ireplace("\\","/",$pdfFilePath);
			$pdfFilePath 			= str_ireplace(' ','_',$pdfFilePath);		
			$savePdfFileName 		= $pdfFilePath;
			$savQry 				= "UPDATE surgery_consent_filled_form SET iolink_pdf_path = '".$savePdfFileName."' WHERE surgery_consent_id = '".$surgery_consent_id."' ";
			$savRes					=imw_query($savQry) or die(imw_error());
			
		}die($strContent);

		if(!file_exists($savePdfFileName)) {
			//die($iolinkDirPath.' @@ '.$savePdfFileName);
			$html2pdf->setTestTdInOnePage($onePage);
			$html2pdf->WriteHTML(html_entity_decode(html_entity_decode($strContent)), isset($_GET['vuehtml']));
			$html2pdf->Output($savePdfFileName,'F');
			die('<br>PatientID = '.$patient_id.' @@ surgery_consent_id = '.$surgery_consent_id.' @@ Path = '.$savePdfFileName);
		}
		
	}
}


$msg_info[] = "<br><b>Release :<br> Update Surgery Consent Path Replaced.</b>";

$color = "green";	

?>
<html>
<head>
<title>Update Surgery Consent Path Replaced</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>