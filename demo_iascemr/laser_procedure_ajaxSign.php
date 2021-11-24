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
//include("common/linkfile.php");
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
			$loggedInUserIdAdd = $ViewUserNameRow["usersId"];
			$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
		

		//END GET USER  NAME
			
//GET FIELD NAME ACCORDING TO USER IDENTITY
		
	if($userIdentity == "Surgeon1") {
		$signUserId = 'signSurgeon1Id';
		$signUserFirstName = 'signSurgeon1FirstName'; 
		$signUserMiddleName = 'signSurgeon1MiddleName';
		$signUserLastName = 'signSurgeon1LastName'; 
		$signUserStatus = 'signSurgeon1Status';
		$signUserDateTime = 'signSurgeon1DateTime';
		$verifiedUserId = 'verified_surgeon_Id';
		$verifiedUserName = 'verified_surgeon_Name';
		$verifiedUserTime = 'verified_surgeon_timeout';

	}else if($userIdentity == "Nurse1") {
		$signUserId = 'signNurseId';
		$signUserFirstName = 'signNurseFirstName'; 
		$signUserMiddleName = 'signNurseMiddleName';
		$signUserLastName = 'signNurseLastName'; 
		$signUserStatus = 'signNurseStatus';
		$signUserDateTime = 'signNurseDateTime';
		
	
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE SIGNATURE
				
				if($delSign=="yes") {
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
					$loggedInUserName="";
				}
	$surgeonFields='';
	if($userIdentity == "Surgeon1"){
		$surgeonFields=", $verifiedUserId 	= '".$loggedInUserId."',
						  $verifiedUserName = '".addslashes($loggedInUserName)."', 
						  $verifiedUserTime = '".$signDateTime."'";
	}
			//END CODE TO REMOVE SIGNATURE
			$SaveSignQry = "update `laser_procedure_patient_table` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										$surgeonFields
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl		
	
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


	if($userIdentity == "Surgeon1") {
		if($delSign=="yes") {
			$callJavaFunSurgeon = "document.frm_laser_procedure.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','laser_procedure_ajaxSign.php','$loginUserId','Surgeon1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_laser_procedure.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','laser_procedure_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>"." Dr. ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed :</b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
			
	<?php
		}
	}else if($userIdentity == "Nurse1") {
	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_laser_procedure.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','laser_procedure_ajaxSign.php','$loginUserId','Nurse1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>

	<?php	
		}else {
			$callJavaFunDel = "document.frm_laser_procedure.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','laser_procedure_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed :</b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="laser_procedure_patient_table" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}
	?>
	
<script>
top.setPNotesHeight();
</script>