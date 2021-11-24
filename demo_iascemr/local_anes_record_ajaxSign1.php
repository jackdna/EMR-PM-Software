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
$objManageData = new manageData;		
	//SAVE USER SIGNATURE
		
			$loggedInUserId = $_GET['loggedInUserId'];
			$userIdentity = $_GET['userIdentity'];
			
			$signOnFileStatus = 'Yes';
			$signDateTime = date("Y-m-d H:i:s");
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
											
	}else if($userIdentity == "Anesthesia2") {
		$signUserId = 'signAnesthesia2Id';
		$signUserFirstName = 'signAnesthesia2FirstName'; 
		$signUserMiddleName = 'signAnesthesia2MiddleName';
		$signUserLastName = 'signAnesthesia2LastName'; 
		$signUserStatus = 'signAnesthesia2Status';
		$signUserDateTime = 'signAnesthesia2DateTime';
	
	}else if($userIdentity == "Anesthesia3") {
		$signUserId = 'signAnesthesia3Id';
		$signUserFirstName = 'signAnesthesia3FirstName'; 
		$signUserMiddleName = 'signAnesthesia3MiddleName';
		$signUserLastName = 'signAnesthesia3LastName'; 
		$signUserStatus = 'signAnesthesia3Status';
		$signUserDateTime = 'signAnesthesia3DateTime';

	}else if($userIdentity == "Anesthesia4") {
		$signUserId = 'signAnesthesia4Id';
		$signUserFirstName = 'signAnesthesia4FirstName'; 
		$signUserMiddleName = 'signAnesthesia4MiddleName';
		$signUserLastName = 'signAnesthesia4LastName'; 
		$signUserStatus = 'signAnesthesia4Status';
		$signUserDateTime = 'signAnesthesia4DateTime';

	}else if($userIdentity == "Surgeon1") {
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
				if($delSign=="yes") {
					$localAnesSignBckGroundColor = $_REQUEST['signAnesthesiaIdBackColor'];
					if(!$localAnesSignBckGroundColor) {$localAnesSignBckGroundColor=$chngBckGroundColorAjax;}//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE SIGNATURE
			
			$SaveSignQry = "update `localanesthesiarecord` set 
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
			$callJavaFunPreOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $localAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunPreOp;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunPreOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunPreOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Anesthesia2") {
		if($delSign=="yes") {
			$callJavaFunIntraOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $localAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunIntraOp;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunIntraOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunIntraOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Anesthesia3") {
		if($delSign=="yes") {
			$callJavaFunPostOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3SignatureId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $localAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunPostOp;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunPostOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3NameId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunPostOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Anesthesia4") {
		if($delSign=="yes") {
			$callJavaFunBeforeIntra	= "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia4SignatureId'; return displaySignature('TDanesthesia4NameId','TDanesthesia4SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia4');";
	?>
			<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $localAnesSignBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunBeforeIntra;?>"> Anesthesia Provider Signature </a>
	<?php	
		}else {
			$callJavaFunBeforeIntraDel= "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia4NameId'; return displaySignature('TDanesthesia4NameId','TDanesthesia4SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia4','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunBeforeIntraDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$signUserPreFix." ".$loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signDateTimeFormatNew;?></span>
	<?php
		}
	}else if($userIdentity == "Surgeon1") {
		if($delSign=="yes") {
			$callJavaFunSurgeon = "document.frm_local_anes_rec.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Surgeon1');";
	?>
			<table class="table_collapse" style="border:none;">
                <tr>
                    <td class="text_10b alignLeft valignMiddle nowrap" style=" width:20%;cursor:pointer;<?php echo $localAnesSignBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunSurgeon;?>">
                        Surgeon Signature
                    </td><td>&nbsp;</td>
                </tr>
            </table>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Surgeon1','delSign');";
	?>
			<table style=" border-collapse:collapse; border:none;">
                <tr>
                    <td class="alignLeft text_10 valignMiddle nowrap" style="cursor:pointer; " onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>">
                        <?php echo "<b>Surgeon:</b>"." Dr. ".$loggedInUserName; ?>
                    </td>
                    
                </tr>
                <tr>
                    <td class="alignLeft text_10 valignMiddle nowrap">
                        <b>Electronically Signed :&nbsp;</b>
                        <?php echo $signOnFileStatus;?>
                    </td>
                </tr>
                 <tr>
					<td class="alignLeft text_10 valignMiddle nowrap"><b>Signature Date : </b><?php echo $signDateTimeFormatNew;?></td>
				</tr>
            </table>
	<?php
		}
	}
	?>	
	
<script>
top.setPNotesHeight();
</script>