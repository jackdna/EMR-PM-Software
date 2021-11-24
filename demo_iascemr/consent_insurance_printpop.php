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
	$lable="Insurance Card Consent";
	$pConfId = $_SESSION['pConfId'];
    $get_http_path=$_REQUEST['get_http_path'];
	include "header_print.php";

//GET PATIENT DETAIL

	$Insur_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$Insur_patientName_tblRes = imw_query($Insur_patientName_tblQry) or die(imw_error());
	$Insur_patientName_tblRow = imw_fetch_array($Insur_patientName_tblRes);
	$Insur_patientName = $Insur_patientName_tblRow["patient_lname"].", ".$Insur_patientName_tblRow["patient_fname"]." ".$Insur_patientName_tblRow["patient_mname"];

	$Insur_patientNameDobTemp = $Insur_patientName_tblRow["date_of_birth"];
		$Insur_patientNameDob_split = explode("-",$Insur_patientNameDobTemp);
		$Insur_patientNameDob = $Insur_patientNameDob_split[1]."-".$Insur_patientNameDob_split[2]."-".$Insur_patientNameDob_split[0];
	
	$Insur_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Insur_patientConfirm_tblRes = imw_query($Insur_patientConfirm_tblQry) or die(imw_error());
	$Insur_patientConfirm_tblRow = imw_fetch_array($Insur_patientConfirm_tblRes);
	$Insur_patientConfirmDosTemp = $Insur_patientConfirm_tblRow["dos"];
	$finalizeStatus = $Insur_patientConfirm_tblRow["finalize_status"];

	$Insur_patientConfirmDos_split = explode("-",$Insur_patientConfirmDosTemp);
	$Insur_patientConfirmDos = $Insur_patientConfirmDos_split[1]."-".$Insur_patientConfirmDos_split[2]."-".$Insur_patientConfirmDos_split[0];
	$Insur_patientConfirmSurgeon = $Insur_patientConfirm_tblRow["surgeon_name"];
	$Insur_patientConfirmSiteTemp = $Insur_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Insur_patientConfirmSiteTemp == 1) {
			$Insur_patientConfirmSite = "Left Eye";  //OD
		}else if($Insur_patientConfirmSiteTemp == 2) {
			$Insur_patientConfirmSite = "Right Eye";  //OS
		}else if($Insur_patientConfirmSiteTemp == 3) {
			$Insur_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Insur_patientConfirmPrimProc = $Insur_patientConfirm_tblRow["patient_primary_procedure"];
	$Insur_patientConfirmSecProc = $Insur_patientConfirm_tblRow["patient_secondary_procedure"];

//END GET PATIENT DETAIL


//VIEW RECORD FROM DATABASE
	$ViewConsentInsuranceQry = "select * from `insurance_consent_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewConsentInsuranceRes = imw_query($ViewConsentInsuranceQry) or die(imw_error()); 
	$ViewConsentInsuranceNumRow = imw_num_rows($ViewConsentInsuranceRes);
	$ViewConsentInsuranceRow = imw_fetch_array($ViewConsentInsuranceRes); 
	
	$consentInsurance_patient_sign = $ViewConsentInsuranceRow["insurance_consent_sign"];
	$insurance_consent_data = stripslashes($ViewConsentInsuranceRow["insurance_consent_data"]);

	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
		if(trim($insurance_consent_data)=="") {
			$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 4";
			$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
			$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
			$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
				
			$insurance_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
		}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
$preid=$pConfId;
$pertable = 'insurance_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'insurance_consent_sign';
$qry = "select insurance_consent_sign from insurance_consent_form where confirmation_id = $pConfId";
$pixleRes = imw_query($qry);
list($insurance_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($insurance_consent_sign,$imgName,'123.jpg');	
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
		$insurance_consent_data= str_replace("{Patient first Name}","<b>".$Insur_patientName_tblRow["patient_fname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Middle Initial}","<b>".$Insur_patientName_tblRow["patient_mname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Last Name}","<b>".$Insur_patientName_tblRow["patient_lname"]."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{DOB}","<b>".$Insur_patientNameDob."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{DOS}","<b>".$Insur_patientConfirmDos."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Surgeon Name}","<b>".$Insur_patientConfirmSurgeon."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Site}","<b>".$Insur_patientConfirmSite."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Procedure}","<b>".$Insur_patientConfirmPrimProc."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace("{Secondary Procedure}","<b>".$Insur_patientConfirmSecProc."</b>",$insurance_consent_data);
		$insurance_consent_data= str_replace('------------------------------------------------','<img src="123.jpg">',$insurance_consent_data);
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE


	$table='<html>
			<head><title>HIPPA Consent Form</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			</head><body>';
	$table.=$head_table."\n";	
	$table.='<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				   <tr>
						<td  align="center"><strong>'.$lable.'</strong></td>
				   </tr>
				   <tr><td  height="7">&nbsp;</td></tr>
				   <tr>
					<td>'.strip_tags($insurance_consent_data,'<br> <img>').'</td>
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