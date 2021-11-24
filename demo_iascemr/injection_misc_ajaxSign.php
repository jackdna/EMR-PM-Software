<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

include_once("common/commonFunctions.php");

//get chart status to remove edit time for signature option
	$query=imw_query("select finalize_status from patientconfirmation where patientConfirmationId='".$_REQUEST["pConfId"]."'")or die(imw_error());
	$data=imw_fetch_object($query);
	$finalizeStatus=$data->finalize_status;
	//$query.close;
//SAVE USER SIGNATURE
		
$loggedInUserId = $_REQUEST['loggedInUserId'];
$userIdentity 	= $_REQUEST['userIdentity'];
			
$signOnFileStatus = 'Yes';
$signDateTime = date("Y-m-d H:i:s");
//$signDateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
$signDateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
$delSign = $_GET['delSign'];
			
$formName				=	"frm_injection_misc";
$ajaxSignFile		=	'injection_misc_ajaxSign.php';
$tblName				=	'injection';
$idFieldName		=	'confirmation_id';
$idFieldValue		=	$_REQUEST['pConfId'];

			
//GET USER NAME
	$ViewUserNameQry = "Select * From `users` Where  usersId = '".$loggedInUserId."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserFirstName	= $ViewUserNameRow["fname"];
	$loggedInUserMiddleName = $ViewUserNameRow["mname"];
	$loggedInUserLastName		= $ViewUserNameRow["lname"];
	$loggedInUserIdAdd 			= $ViewUserNameRow["usersId"];
	$loggedInUserName 			= $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
	$loggedInUserType 			= $ViewUserNameRow["user_type"];
	$loggedInUserSubType 		= $ViewUserNameRow["user_sub_type"];
		
//END GET USER  NAME

	$signUserPreFix = '';
	if($loggedInUserType == 'Surgeon' || ($loggedInUserType == 'Anesthesiologist' && $loggedInUserSubType <> 'CRNA'))
	{
		$signUserPreFix = 'Dr.';
	}
	
//GET FIELD NAME ACCORDING TO USER IDENTITY
		
	$signUserId 				= 'sign'.$userIdentity.'Id';
	$signUserFirstName	= 'sign'.$userIdentity.'FirstName'; 
	$signUserMiddleName = 'sign'.$userIdentity.'MiddleName';
	$signUserLastName 	= 'sign'.$userIdentity.'LastName'; 
	$signUserStatus 		= 'sign'.$userIdentity.'Status';
	$signUserDateTime 	= 'sign'.$userIdentity.'DateTime';
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			
			
//CODE TO REMOVE SIGNATURE

if($delSign=="yes")
{
		$loggedInUserId					=	"";
		$loggedInUserFirstName	=	"";
		$loggedInUserMiddleName	=	"";
		$loggedInUserLastName		=	"";
		$signOnFileStatus				=	"";
		$signDateTime						=	"";
		$loggedInUserName				=	"";
}

//END CODE TO REMOVE SIGNATURE

$SaveSignQry = "Update ".$tblName." Set 
										$signUserId 					= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 			= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());

//END SAVE signature in Table


//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE


//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE


$userIdentityS	=	strtolower($userIdentity);
$userTitle			=	"Surgeon";
if(stristr($userIdentity,'Nurse'))
		$userTitle	=	'Nurse';
else if(stristr($userIdentity,'Anesthesia'))
		$userTitle	=	'Anesthesia Provider';


if($delSign=="yes")
{
	$callJavaFun = "document.".$formName.".hiddSignatureId.value='TD".$userIdentityS."SignatureId'; return displaySignature('TD".$userIdentityS."NameId','TD".$userIdentityS."SignatureId','".$ajaxSignFile."','".$loggedInUserIdAdd."','".$userIdentity."');";
	
	$html	=	'<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;'.$chngBckGroundColorAjax.';" onClick="javascript:'.$callJavaFun.'"> '.$userTitle.' Signature </a>';

}
else
{
	
	$callJavaFunDel = "document.".$formName.".hiddSignatureId.value='TD".$userIdentityS."NameId'; return displaySignature('TD".$userIdentityS."NameId','TD".$userIdentityS."SignatureId','".$ajaxSignFile."','".$loggedInUserIdAdd."','".$userIdentity."','delSign');";	
	
	
	$html		=	"";
	$html	 .=	'<span class="rob full_width">';
	$html	 .=	'<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:'.$callJavaFunDel.'"><b>'.$userTitle.': </b>'.$signUserPreFix.'&nbsp;'.$loggedInUserName.'</a>';
	$html	 .=	'</span>';
	
	$html	 .=	'<span class="rob full_width"> <b> Electronically Signed :</b> '.$signOnFileStatus.'</span>';

	$html	 .=	'<span class="rob full_width">';
	$html	 .=	'<b> Signature Date :</b> ';
	
	if($userTitle === 'Nurse')
	{
			$html	 .=	'<span class="dynamic_sig_dt" data-field-name="sign'.$userIdentity.'DateTime" data-table-name="'.$tblName.'" data-id-value="'.$idFieldValue.'" data-id-name="'.$idFieldName.'"> '.$signDateTimeFormatNew.' <span class="fa fa-edit"></span></span></span>';
	}
	else
	{
			$html	 .=	$signDateTimeFormatNew;
	}
	$html	 .=	'</span>';
}

echo $html;
?>
<script>
top.setPNotesHeight();
</script>