<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once("common/conDb.php");
	include("common_functions.php");
	//include("common/linkfile.php");
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	$get_http_path=$_REQUEST['get_http_path'];
	include "header_print.php";
	$lable="Patient Consent";
	$pConfId = $_REQUEST['pConfId'];
	
	//GET PATIENT DETAIL
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_SESSION['patient_id']."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
		$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
		$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
	$finalizeStatus = $Consent_patientConfirm_tblRow["finalize_status"];
	$Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow["dos"];
		$Consent_patientConfirmDos_split = explode("-",$Consent_patientConfirmDosTemp);
		$Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1]."-".$Consent_patientConfirmDos_split[2]."-".$Consent_patientConfirmDos_split[0];

	$Consent_patientConfirmSurgeon = $Consent_patientConfirm_tblRow["surgeon_name"];
	$Consent_patientConfirmSiteTemp = $Consent_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Consent_patientConfirmSiteTemp == 1) {
			$Consent_patientConfirmSite = "Left Eye";  //OD
		}else if($Consent_patientConfirmSiteTemp == 2) {
			$Consent_patientConfirmSite = "Right Eye";  //OS
		}else if($Consent_patientConfirmSiteTemp == 3) {
			$Consent_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow["patient_primary_procedure"];
	$Consent_patientConfirmSecProc = $Consent_patientConfirm_tblRow["patient_secondary_procedure"];
	//END GET PATIENT DETAIL
	
	//VIEW RECORD FROM DATABASE
	$ViewConsentSurgeryQry = "select * from `surgery_consent_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = imw_num_rows($ViewConsentSurgeryRes);
	$ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes); 
	
	$consentSurgery_patient_sign = $ViewConsentSurgeryRow["surgery_consent_sign"];
	$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
	$form_status = $ViewConsentSurgeryRow["form_status"];
	$saveLink = $saveLink."&form_status=".$form_status;
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
	if(trim($surgery_consent_data)=="") {
		$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 1";
		$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
		$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
		$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
			
		$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
	}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		

//image for signature
$preid=$pConfId;
$pertable = 'surgery_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'surgery_consent_sign';
$qry = "select surgery_consent_sign from surgery_consent_form where confirmation_id = $pConfId";
$pixleRes = imw_query($qry);
list($surgery_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($surgery_consent_sign,$imgName,'123.jpg');
/*******End of signature*****/

	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
		$surgery_consent_data= str_replace("{Patient first Name}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Middle Initial}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Last Name}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Surgeon Name}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Site}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Procedure}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{Secondary Procedure}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace('------------------------------------------------','<img src="123.jpg" width="200" height="80">',$surgery_consent_data);


//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE
	
	
		
	$table='<html>
			<head><title>Consent for Surgery & Anesthesia</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			</head><body>';
	$table.=$head_table."\n";
	$table.='<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				   <tr>
						<td  align="center"><strong>'.$lable.'</strong></td>
				   </tr>
				   <tr><td  height="7">&nbsp;</td></tr>
				   <tr>
					<td>'.strip_tags($surgery_consent_data,'<br> <img>').'</td>
				   </tr>
			   </table>';
	$table.='</body></html>';   
			   
//echo $table;			   
$fileOpen = fopen('testPdf.html','w+');
$filePut = fputs(fopen('testPdf.html','w+'),$table);
fclose($fileOpen);
//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
?>	
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table style="font:vetrdana; font-size:14;" width="100%" height="100%" bgcolor="#FFFFFF">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/pdf_load_img.gif"></td> 
	</tr>
</table>
<body >
<form name="printFrm"  action="html2pdf/index.php?AddPage=P" method="post">
</form>		
<script type="text/javascript">
 submitfn();
</script>
</body>