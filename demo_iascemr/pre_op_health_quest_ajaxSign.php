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
include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData 	= new manageData;

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
			
			$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
		//END GET USER  NAME
			
//GET FIELD NAME ACCORDING TO USER IDENTITY

	if($userIdentity == "Nurse1") {
		$signUserId = 'signNurseId';
		$signUserFirstName = 'signNurseFirstName'; 
		$signUserMiddleName = 'signNurseMiddleName';
		$signUserLastName = 'signNurseLastName'; 
		$signUserStatus = 'signNurseStatus';
		$signUserDateTime = 'signNurseDateTime';
	
	}else if($userIdentity == "Witness1") {
		$signUserId = 'signWitness1Id';
		$signUserFirstName = 'signWitness1FirstName'; 
		$signUserMiddleName = 'signWitness1MiddleName';
		$signUserLastName = 'signWitness1LastName'; 
		$signUserStatus = 'signWitness1Status';
		$signUserDateTime = 'signWitness1DateTime';
											
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE USER SIGNATURE
				if($delSign=="yes") {
					$healthQuestSignBckGroundColor=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE USER SIGNATURE
			
			$SaveSignQry = "update `preophealthquestionnaire` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());

	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	if($userIdentity == "Nurse1") {
	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_health_ques.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Nurse1');";
	?>
            <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $healthQuestSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_health_ques.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>"." ".$loggedInUserName; ?>  </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Witness1") {
	
		if($delSign=="yes") {
			$callJavaFunWitness = "document.frm_health_ques.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Witness1');";
	?>		
                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitness;?>"> Witness Signature </a>
	<?php	
		}else {
			$callJavaFunWitnessDel = "document.frm_health_ques.hiddSignatureId.value='TDwitness1NameId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Witness1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"> <?php echo "<b>Witness:</b>"." ".$loggedInUserName; ?>  </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}
	
	?>
	
<script>
top.setPNotesHeight();
</script>