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
	$lable="Assign Benefits Consent";
	$pConfId = $_SESSION['pConfId'];
	 $get_http_path=$_REQUEST['get_http_path'];
	 include "header_print.php";
	// end set surgerycenter detail
	
//GET PATIENT DETAIL

	$Benefit_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$Benefit_patientName_tblRes = imw_query($Benefit_patientName_tblQry) or die(imw_error());
	$Benefit_patientName_tblRow = imw_fetch_array($Benefit_patientName_tblRes);
	$Benefit_patientName = $Benefit_patientName_tblRow["patient_lname"].", ".$Benefit_patientName_tblRow["patient_fname"]." ".$Benefit_patientName_tblRow["patient_mname"];

	$Benefit_patientNameDobTemp = $Benefit_patientName_tblRow["date_of_birth"];
		$Benefit_patientNameDob_split = explode("-",$Benefit_patientNameDobTemp);
		$Benefit_patientNameDob = $Benefit_patientNameDob_split[1]."-".$Benefit_patientNameDob_split[2]."-".$Benefit_patientNameDob_split[0];
	
	$Benefit_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Benefit_patientConfirm_tblRes = imw_query($Benefit_patientConfirm_tblQry) or die(imw_error());
	$Benefit_patientConfirm_tblRow = imw_fetch_array($Benefit_patientConfirm_tblRes);
	$Benefit_patientConfirmDosTemp = $Benefit_patientConfirm_tblRow["dos"];
	$finalizeStatus = $Benefit_patientConfirm_tblRow["finalize_status"];

	$Benefit_patientConfirmDos_split = explode("-",$Benefit_patientConfirmDosTemp);
	$Benefit_patientConfirmDos = $Benefit_patientConfirmDos_split[1]."-".$Benefit_patientConfirmDos_split[2]."-".$Benefit_patientConfirmDos_split[0];
	$Benefit_patientConfirmSurgeon = $Benefit_patientConfirm_tblRow["surgeon_name"];
	$Benefit_patientConfirmSiteTemp = $Benefit_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Benefit_patientConfirmSiteTemp == 1) {
			$Benefit_patientConfirmSite = "Left Eye";  //OD
		}else if($Benefit_patientConfirmSiteTemp == 2) {
			$Benefit_patientConfirmSite = "Right Eye";  //OS
		}else if($Benefit_patientConfirmSiteTemp == 3) {
			$Benefit_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Benefit_patientConfirmPrimProc = $Benefit_patientConfirm_tblRow["patient_primary_procedure"];
	$Benefit_patientConfirmSecProc = $Benefit_patientConfirm_tblRow["patient_secondary_procedure"];

//END GET PATIENT DETAIL


//VIEW RECORD FROM DATABASE
	$ViewConsentBenefitQry = "select * from `benefit_consent_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewConsentBenefitRes = imw_query($ViewConsentBenefitQry) or die(imw_error()); 
	$ViewConsentBenefitNumRow = imw_num_rows($ViewConsentBenefitRes);
	$ViewConsentBenefitRow = imw_fetch_array($ViewConsentBenefitRes); 
	
	$consentBenefit_patient_sign = $ViewConsentBenefitRow["benefit_consent_sign"];
	$benefit_consent_data = stripslashes($ViewConsentBenefitRow["benefit_consent_data"]);

	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
		if(trim($benefit_consent_data)=="") {
			$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 3";
			$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
			$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
			$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
				
			$benefit_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
		}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
	
//image for signature
$preid=$pConfId;
$pertable = 'benefit_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'benefit_consent_sign';
$qry = "select benefit_consent_sign from benefit_consent_form where confirmation_id = $pConfId";
$pixleRes = imw_query($qry);
list($benefit_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($benefit_consent_sign,$imgName,'123.jpg');
/*******End of signature*****/	
		
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
		$benefit_consent_data= str_replace("{Patient first Name}","<b>".$Benefit_patientName_tblRow["patient_fname"]."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Middle Initial}","<b>".$Benefit_patientName_tblRow["patient_mname"]."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Last Name}","<b>".$Benefit_patientName_tblRow["patient_lname"]."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{DOB}","<b>".$Benefit_patientNameDob."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{DOS}","<b>".$Benefit_patientConfirmDos."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Surgeon Name}","<b>".$Benefit_patientConfirmSurgeon."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Site}","<b>".$Benefit_patientConfirmSite."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Procedure}","<b>".$Benefit_patientConfirmPrimProc."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace("{Secondary Procedure}","<b>".$Benefit_patientConfirmSecProc."</b>",$benefit_consent_data);
		$benefit_consent_data= str_replace('------------------------------------------------','<img src="123.jpg">',$benefit_consent_data);
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE


	$table='<html>
			<head><title>HIPPA Consent Form</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			</head><body>';
	$table.=$head_table."\n";			
	$table.='<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				   <tr>
						<td align="center"><strong>'.$lable.'</strong></td>
				   </tr>
				   <tr><td height="7">&nbsp;</td></tr>
				   <tr>
					<td>'.strip_tags($benefit_consent_data,'<br> <img>').'</td>
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