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
		
			$loggedInUserId = $_REQUEST['loggedInUserId'];
			$userIdentity = $_GET['userIdentity'];
			$signOnFileStatus = 'Yes';
			$signDateTime = date("Y-m-d H:i:s");
			$delSign = $_GET['delSign'];
			$signDateTimeShow = $objManageData->getFullDtTmFormat($signDateTime);
			//$signSurgeon1DateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
			$signSurgeon1DateTimeFormatNew = $signDateTimeShow;
			//$signAnesthesia1DateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
			$signAnesthesia1DateTimeFormatNew = $signDateTimeShow;
			//$signNurse1DateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
			$signNurse1DateTimeFormatNew = $signDateTimeShow;
			
		//GET USER SUB TYPE
			$ViewUserNameQry 		= "select * from `users` where  usersId = '".$loggedInUserId."'";
			$ViewUserNameRes 		= imw_query($ViewUserNameQry) or die(imw_error()); 
			$ViewUserNameRow 		= imw_fetch_array($ViewUserNameRes); 
			
			$loggedInUserFirstName 	= $ViewUserNameRow["fname"];
			$loggedInUserMiddleName = $ViewUserNameRow["mname"];
			$loggedInUserLastName 	= $ViewUserNameRow["lname"];
			
			$loggedInUserName 		= $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
			$loggedInUserSubType 	= $ViewUserNameRow["user_sub_type"];
		//END GET USER SUB TYPE
			
		
//GET FIELD NAME ACCORDING TO USER IDENTITY
	$signUserPreFix = 'Dr.';
	if($loggedInUserSubType=='CRNA') {//PREFIX FOR ANESTHESIOLOGIST/CRNA
		$signUserPreFix = '';
	}

	if($userIdentity == "Surgeon1") {
		$signUserId = 'signSurgeon1Id';
		$signUserFirstName = 'signSurgeon1FirstName'; 
		$signUserMiddleName = 'signSurgeon1MiddleName';
		$signUserLastName = 'signSurgeon1LastName'; 
		$signUserStatus = 'signSurgeon1Status';
		$signUserDateTime = 'signSurgeon1DateTime';
	
	}else if($userIdentity == "Anesthesia1") {
		$signUserId = 'signAnesthesia1Id';
		$signUserFirstName = 'signAnesthesia1FirstName'; 
		$signUserMiddleName = 'signAnesthesia1MiddleName';
		$signUserLastName = 'signAnesthesia1LastName'; 
		$signUserStatus = 'signAnesthesia1Status';
		$signUserDateTime = 'signAnesthesia1DateTime';
	
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
				
				$surgeonSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				$anesthesiaSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				$nurseSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				
				$hpSurgeonSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				$hpNurseSignBckGroundColorAjax=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				$loggedInUserId="";
				$loggedInUserFirstName="";
				$loggedInUserMiddleName="";
				$loggedInUserLastName="";
				$signOnFileStatus="";
				$signDateTime="";
			}
			//END CODE TO REMOVE SIGNATURE
			
			
			
			
			$SaveSignQry = "update `history_physicial_clearance` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error().$SaveSignQry);
			
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE

	//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
		$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
	//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE

	
	if($userIdentity == "Surgeon1") {
		if($delSign=="yes") {
			$callJavaFunSurgeon = "document.frm_history_physicial.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Surgeon1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeonSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_history_physicial.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>"." Dr. ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signSurgeon1DateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Anesthesia1") {
		if($delSign=="yes") {
			$callJavaFunAnesthesia = "document.frm_history_physicial.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Anesthesia1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $anesthesiaSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunAnesthesia;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunAnesthesiaDel = "document.frm_history_physicial.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunAnesthesiaDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia1DateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Nurse1") {	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_history_physicial.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Nurse1');";
	?>
                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_history_physicial.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>		
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="history_physicial_clearance" data-id-value="<?php echo $_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signNurse1DateTimeFormatNew;  echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}
		
		imw_close();
	?>	 