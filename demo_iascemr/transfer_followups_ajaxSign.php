<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include("common/link_new_file.php");
include_once("common/commonFunctions.php");
//get chart status to remove edit time for signature option
	$query=imw_query("select finalize_status from patientconfirmation where patientConfirmationId='".$_REQUEST["pConfId"]."'")or die(imw_error());
	$data=imw_fetch_object($query);
	$finalizeStatus=$data->finalize_status;
	//$query.close;		
//SAVE USER SIGNATURE
		
$loggedInUserId = $_GET['loggedInUserId'];
$userIdentity = $_GET['userIdentity'];
			
$signOnFileStatus = 'Yes';
$signDateTime = date("Y-m-d H:i:s");
//$signDateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
$signDateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
$delSign = $_GET['delSign'];

//GET USER NAME
$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserFirstName = $ViewUserNameRow["fname"];
$loggedInUserMiddleName = $ViewUserNameRow["mname"];
$loggedInUserLastName = $ViewUserNameRow["lname"];
$loggedInUserType		=	$ViewUserNameRow['user_type'];

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
//END GET USER  NAME
			
//GET FIELD NAME ACCORDING TO USER IDENTITY
	$signUserPreFix = '';
	/*
	if($loggedInUserType == 'Surgeon')
	{
		$signUserPreFix = 'Dr.';
	}
	*/
	
	
	if($userIdentity == "Nurse") 
	{
		$signUserId = 'signNurseId';
		$signUserFirstName = 'signNurseFirstName'; 
		$signUserMiddleName = 'signNurseMiddleName';
		$signUserLastName = 'signNurseLastName'; 
		$signUserStatus = 'signNurseStatus';
		$signUserDateTime = 'signNurseDateTime';
											
	}else if($userIdentity == "Nurse1") {
		$signUserId = 'signNurse1Id';
		$signUserFirstName = 'signNurse1FirstName'; 
		$signUserMiddleName = 'signNurse1MiddleName';
		$signUserLastName = 'signNurse1LastName'; 
		$signUserStatus = 'signNurse1Status';
		$signUserDateTime = 'signNurse1DateTime';
	
	}
	else if($userIdentity == "Surgeon") {
		$signUserId = 'signSurgeon1Id';
		$signUserFirstName = 'signSurgeon1FirstName'; 
		$signUserMiddleName = 'signSurgeon1MiddleName';
		$signUserLastName = 'signSurgeon1LastName'; 
		$signUserStatus = 'signSurgeon1Status';
		$signUserDateTime = 'signSurgeon1DateTime';
	
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
$loginUserId = $loggedInUserId;
//CODE TO REMOVE SIGNATURE
if($delSign=="yes") 
{
	$signBackGroundColor	= $chngBckGroundColorAjax;
	$loggedInUserId="";
	$loggedInUserFirstName="";
	$loggedInUserMiddleName="";
	$loggedInUserLastName="";
	$signOnFileStatus="";
	$signDateTime="";
}
//END CODE TO REMOVE SIGNATURE
$SaveSignQry = "update `transfer_followups` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());

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
	
	
	if($userIdentity == "Nurse") 
	{
		if($delSign=="yes") 
		{
			$callJavaFunNurse	=	"document.frm_transfer_followups.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse');";
	?>
				<a href="javascript:void(0);" class="sign_link" style=" cursor:pointer;<?php echo $signBackGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse;?>"> Nurse Signature </a>
	<?php	
		}
		else
		{
			$callJavaFunNurseDel = "document.frm_transfer_followups.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurseDel;?>"> <?php echo "<b>Nurse:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width" > <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width" > <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="transfer_followups" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}
	else if($userIdentity == "Nurse1") 
	{
		if($delSign=="yes") 
		{
			$callJavaFunNurse1	=	"document.frm_transfer_followups.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse1');";
	?>
				<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signBackGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse1;?>"> Nurse Signature </a>
	<?php	
		}
		else
		{
			$callJavaFunNurse1Del = "document.frm_transfer_followups.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurse1Del;?>"> <?php echo "<b>Nurse:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="transfer_followups" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}
	else if($userIdentity == "Surgeon") {
		if($delSign=="yes") {
			$callJavaFunSurgeon1	=	"document.frm_transfer_followups.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Surgeon');";
	?>
    		<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signBackGroundColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon1;?>"> Surgeon Signature </a>
   	<?php	
		}
		else
		 {
			$callJavaFunSurgeon1Del = "document.frm_transfer_followups.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Surgeon','delSign');";
	?>
			
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeon1Del;?>"> <?php echo "<b>Surgeon:</b> ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width" > <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="transfer_followups" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit fa-editsurg"></span>'; ?> </span></span>
            
   	<?php
		}
	}
	?>	
	
<script>
top.setPNotesHeight();
</script>