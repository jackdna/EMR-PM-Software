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
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;

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
			$delSign = $_GET['delSign'];
			
			$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
			$signNurse1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
			
		//GET USER NAME
			$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
			$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
			$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
			
			$loggedInUserFirstName = $ViewUserNameRow["fname"];
			$loggedInUserMiddleName = $ViewUserNameRow["mname"];
			$loggedInUserLastName = $ViewUserNameRow["lname"];
			
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
	
	}else if($userIdentity == "Nurse1") {
		$signUserId = 'signNurseId';
		$signUserFirstName = 'signNurseFirstName'; 
		$signUserMiddleName = 'signNurseMiddleName';
		$signUserLastName = 'signNurseLastName'; 
		$signUserStatus = 'signNurseStatus';
		$signUserDateTime = 'signNurseDateTime';
	
	}else if($userIdentity == "Nurse2") {
		$signUserId = 'signNurse1Id';
		$signUserFirstName = 'signNurse1FirstName'; 
		$signUserMiddleName = 'signNurse1MiddleName';
		$signUserLastName = 'signNurse1LastName'; 
		$signUserStatus = 'signNurse1Status';
		$signUserDateTime = 'signNurse1DateTime';
	
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE SIGNATURE
				
				if($delSign=="yes") {
					
					$surgeonSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$nurseSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE SIGNATURE
			
			$SaveSignQry = "update `preopphysicianorders` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
										
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
			$callJavaFunSurgeon = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1');";
	?>	
    	<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeonSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
             <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>"." Dr. ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="preopphysicianorders" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="patient_confirmation_id"> <?php echo $signSurgeon1DateTimeFormatNew;  echo' <span class="fa fa-edit fa-editsurg"></span>'; ?> </span></span>
	<?php
		}
	}else if($userIdentity == "Nurse1") {
	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1');";
	?>
				  <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
			<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="preopphysicianorders" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="patient_confirmation_id"> <?php echo $signNurse1DateTimeFormatNew;  echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}else if($userIdentity == "Nurse2") {
	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseSignatureId1'; return displaySignature('TDnurseNameId1','TDnurseSignatureId1','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse2');";
	?>
				  <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseNameId1'; return displaySignature('TDnurseNameId1','TDnurseSignatureId1','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse2','delSign1');";
	?>
			<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="preopphysicianorders" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="patient_confirmation_id"> <?php echo $signNurse1DateTimeFormatNew;  echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}
	?>
	
<script>
top.setPNotesHeight();
</script>