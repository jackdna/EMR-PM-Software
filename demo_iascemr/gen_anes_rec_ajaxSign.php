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
//include("common/linkfile.php");
include_once("common/commonFunctions.php");
		
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
			$loggedInUserSubType = $ViewUserNameRow["user_sub_type"];
			
			$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
		//END GET USER  NAME
			
//GET FIELD NAME ACCORDING TO USER IDENTITY

	$signUserPreFix = 'Dr.';
	if($loggedInUserSubType=='CRNA') {//PREFIX FOR ANESTHESIOLOGIST/CRNA
		$signUserPreFix = '';
	}

	if($userIdentity == "Anesthesia1") {
		$signUserId = 'signAnesthesia1Id';
		$signUserFirstName = 'signAnesthesia1FirstName'; 
		$signUserMiddleName = 'signAnesthesia1MiddleName';
		$signUserLastName = 'signAnesthesia1LastName'; 
		$signUserStatus = 'signAnesthesia1Status';
		$signUserDateTime = 'signAnesthesia1DateTime';
											
	}
	else if($userIdentity == "Anesthesia2") {
		$signUserId = 'signAnesthesia2Id';
		$signUserFirstName = 'signAnesthesia2FirstName'; 
		$signUserMiddleName = 'signAnesthesia2MiddleName';
		$signUserLastName = 'signAnesthesia2LastName'; 
		$signUserStatus = 'signAnesthesia2Status';
		$signUserDateTime = 'signAnesthesia2DateTime';
											
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE SIGNATURE
				if($delSign=="yes") {
					$genAnesSignBckGroundColor = $_REQUEST['signAnesthesiaIdBackColor'];
					if(!$genAnesSignBckGroundColor) {$genAnesSignBckGroundColor=$chngBckGroundColorAjax;}//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE SIGNATURE
			
			$SaveSignQry = "update `genanesthesiarecord` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());

		//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
			$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
		//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
	
		
	if($userIdentity == "Anesthesia1") {
		if($delSign=="yes") {
			$callJavaFun = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $genAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
	?>
			
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}
	elseif($userIdentity == "Anesthesia2") {
		if($delSign=="yes") {
			$callJavaFun = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia2');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $genAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia2','delSign');";
	?>
			
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}
	
	?>	
	
<script>
top.setPNotesHeight();
</script>