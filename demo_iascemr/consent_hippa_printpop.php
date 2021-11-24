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
	$lable="HIPPA Consent";
	$pConfId = $_SESSION['pConfId'];
	  $get_http_path=$_REQUEST['get_http_path'];
	  include "header_print.php";
	//GET PATIENT DETAIL
	$HIPPA_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_SESSION['patient_id']."'";
	$HIPPA_patientName_tblRes = imw_query($HIPPA_patientName_tblQry) or die(imw_error());
	$HIPPA_patientName_tblRow = imw_fetch_array($HIPPA_patientName_tblRes);
	$HIPPA_patientName = $HIPPA_patientName_tblRow["patient_lname"].", ".$HIPPA_patientName_tblRow["patient_fname"]." ".$HIPPA_patientName_tblRow["patient_mname"];

	$HIPPA_patientNameDobTemp = $HIPPA_patientName_tblRow["date_of_birth"];
		$HIPPA_patientNameDob_split = explode("-",$HIPPA_patientNameDobTemp);
		$HIPPA_patientNameDob = $HIPPA_patientNameDob_split[1]."-".$HIPPA_patientNameDob_split[2]."-".$HIPPA_patientNameDob_split[0];
	
	$HIPPA_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$HIPPA_patientConfirm_tblRes = imw_query($HIPPA_patientConfirm_tblQry) or die(imw_error());
	$HIPPA_patientConfirm_tblRow = imw_fetch_array($HIPPA_patientConfirm_tblRes);
	$finalizeStatus = $HIPPA_patientConfirm_tblRow["finalize_status"];
	$HIPPA_patientConfirmDosTemp = $HIPPA_patientConfirm_tblRow["dos"];

	$HIPPA_patientConfirmDos_split = explode("-",$HIPPA_patientConfirmDosTemp);
	$HIPPA_patientConfirmDos = $HIPPA_patientConfirmDos_split[1]."-".$HIPPA_patientConfirmDos_split[2]."-".$HIPPA_patientConfirmDos_split[0];
	$HIPPA_patientConfirmSurgeon = $HIPPA_patientConfirm_tblRow["surgeon_name"];
	$HIPPA_patientConfirmSiteTemp = $HIPPA_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($HIPPA_patientConfirmSiteTemp == 1) {
			$HIPPA_patientConfirmSite = "Left Eye";  //OD
		}else if($HIPPA_patientConfirmSiteTemp == 2) {
			$HIPPA_patientConfirmSite = "Right Eye";  //OS
		}else if($HIPPA_patientConfirmSiteTemp == 3) {
			$HIPPA_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$HIPPA_patientConfirmPrimProc = $HIPPA_patientConfirm_tblRow["patient_primary_procedure"];
	$HIPPA_patientConfirmSecProc = $HIPPA_patientConfirm_tblRow["patient_secondary_procedure"];

//END GET PATIENT DETAIL
	
//VIEW RECORD FROM DATABASE
	$ViewConsentHippaQry = "select * from `hippa_consent_form` where  confirmation_id = '".$_SESSION["pConfId"]."'";
	$ViewConsentHippaRes = imw_query($ViewConsentHippaQry) or die(imw_error()); 
	$ViewConsentHippaNumRow = imw_num_rows($ViewConsentHippaRes);
	$ViewConsentHippaRow = imw_fetch_array($ViewConsentHippaRes); 
	
	$consentHippa_patient_sign = $ViewConsentHippaRow["hippa_consent_sign"];
	$hippa_consent_data = stripslashes($ViewConsentHippaRow["hippa_consent_data"]);
	
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
		if(trim($hippa_consent_data)=="") {
			$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 2";
			$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
			$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
			$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
				
			$hippa_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
		}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE
 $pConfId = $_SESSION['pConfId'];		
 $preid=$pConfId;
$pertable = 'hippa_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'hippa_consent_sign';
$qry = "select hippa_consent_sign from hippa_consent_form where confirmation_id = $pConfId";
$pixleRes = imw_query($qry);
list($hippa_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($hippa_consent_sign,$imgName,'123.jpg');
//echo "<div style='display:none;'>";
//echo getAppletImage($preid,$pertable,$preidName,$predocSign,$signImage,$alt,"123.jpg");
//echo"</div>";		
		
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 		
		$hippa_consent_data= str_replace("{Patient first Name}","<b>".$HIPPA_patientName_tblRow["patient_fname"]."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Middle Initial}","<b>".$HIPPA_patientName_tblRow["patient_mname"]."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Last Name}","<b>".$HIPPA_patientName_tblRow["patient_lname"]."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{DOB}","<b>".$HIPPA_patientNameDob."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{DOS}","<b>".$HIPPA_patientConfirmDos."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Surgeon Name}","<b>".$HIPPA_patientConfirmSurgeon."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Site}","<b>".$HIPPA_patientConfirmSite."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Procedure}","<b>".$HIPPA_patientConfirmPrimProc."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace("{Secondary Procedure}","<b>".$HIPPA_patientConfirmSecProc."</b>",$hippa_consent_data);
		$hippa_consent_data= str_replace('------------------------------------------------','<img src="123.jpg">',$hippa_consent_data);
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
				   <tr>
					<td>'.strip_tags($hippa_consent_data,'<br> <img>').'</td>
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