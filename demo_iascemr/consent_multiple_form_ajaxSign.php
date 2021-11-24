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
//include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData 	= new manageData;

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
			$consentMultipleId = $_REQUEST['consentMultipleId'];
			//$signDateTimeFormatNew = date("m-d-Y h:i A",strtotime($signDateTime));
			$signDateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
			$surgeon_date_time_sign=date("m-d-Y h:i A",strtotime($signDateTime));
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
	
	}else if($userIdentity == "Anesthesia1") {
		$signUserId = 'signAnesthesia1Id';
		$signUserFirstName = 'signAnesthesia1FirstName'; 
		$signUserMiddleName = 'signAnesthesia1MiddleName';
		$signUserLastName = 'signAnesthesia1LastName'; 
		$signUserStatus = 'signAnesthesia1Status';
		$signUserDateTime = 'signAnesthesia1DateTime';
											
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
			//CODE TO REMOVE SIGNATURE
				
				if($delSign=="yes") {
					$consentSignBckGroundColor=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					
					$consentAnesSignBckGroundColor = $_REQUEST['signAnesthesiaIdBackColor'];
					if(!$consentAnesSignBckGroundColor) {$consentAnesSignBckGroundColor=$chngBckGroundColorAjax;}//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE SIGNATURE
			
			$SaveSignQry = "update `consent_multiple_form` set 
										$signUserId 			= '".$loggedInUserId."',
										$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
										$signUserStatus 		= '".$signOnFileStatus."',
										$signUserDateTime 		= '".$signDateTime."'
										WHERE confirmation_id	= '".$_REQUEST["pConfId"]."'
										AND consent_template_id	= '".$consentMultipleId."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl		
		
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
			$callJavaFunSurgeon = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Surgeon1');";
	?>
            <table class="table_collapse">
                <tr>
                    <td class="text_10b alignLeft valignMiddle nowrap" style=" width:20%;cursor:pointer;<?php echo $consentSignBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunSurgeon;?>">
                        Surgeon Signature
                    </td><td>&nbsp;</td>
                </tr>
            </table>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
            <table style="border:none; border-collapse:collapse;">
                <tr>
                    <td class="text_10 alignLeft valignMiddle nowrap" style="cursor:pointer; padding-right:7px;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>">
                        <?php echo "<b>Surgeon:</b>". " Dr. ". $loggedInUserName; ?>
                    </td>
                    
                </tr>
                <tr>
                    <td class="text_10 alignLeft valignMiddle nowrap">
                        <b>Electronically Signed:&nbsp;</b>
                        <?php echo $signOnFileStatus;?>
                    </td>
                </tr>
                <tr>
                    <td class="text_10 alignLeft valignMiddle nowrap">
                        <b>Signature Date:&nbsp;</b>
                        <?php echo $signDateTimeFormatNew;?>
                    </td>
                </tr>
            </table>
	<?php
		}
	}else if($userIdentity == "Nurse1") {
	
		if($delSign=="yes") {
			$callJavaFun = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Nurse1');";
	?>
                <table style="border:none; border-collapse:collapse;">
                    <tr>
                        <td class="text_10b nowrap"   style="cursor:pointer;<?php echo $consentSignBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</td>
                    </tr>
                </table>	
	<?php	
		}else {
			$callJavaFunDel = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
                <table class="table_collapse">
                    <tr>
                        <td class="text_10 nowrap" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"><?php echo "<b>Nurse:</b>"." ".$loggedInUserName; ?></td>
                    </tr>
                    <tr>
                        <td class="text_10 nowrap">
                            <b>Electronically Signed :&nbsp;</b>
                            <?php echo $signOnFileStatus;?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text_10 nowrap">
                            <b>Signature Date:&nbsp;</b>
                            <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="consent_multiple_form" data-id-value="<?=$_REQUEST[pConfId]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span>
                        </td>
                    </tr>
                </table>
	<?php
		}
	}else if($userIdentity == "Anesthesia1") {
		if($delSign=="yes") {
			$callJavaFunAnesthesia = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Anesthesia1');";
	?>
            <table class="table_collapse" style="border:none;">
                <tr>
                    <td class="text_10b alignLeft valignMiddle nowrap" style=" width:20%;cursor:pointer;<?php echo $consentAnesSignBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunAnesthesia;?>">
                        Anesthesia Provider Signature
                    </td><td>&nbsp;</td>
                </tr>
            </table>
	<?php	
		}else {
			$callJavaFunAnesthesiaDel = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
	?>
            <table style=" border-collapse:collapse; border:none;">
                <tr>
                    <td class="alignLeft text_10 valignMiddle nowrap" style="cursor:pointer; padding-right:7px;" onClick="javascript:<?php echo $callJavaFunAnesthesiaDel;?>">
                        <?php echo "<b>Anesthesia Provider:</b>". " $signUserPreFix ". $loggedInUserName; ?>
                    </td>
                    
                </tr>
                <tr>
                    <td class="alignLeft text_10 valignMiddle nowrap">
                        <b>Electronically Signed :&nbsp;</b>
                        <?php echo $signOnFileStatus;?>
                    </td>
                </tr>
                <tr>
                    <td class="alignLeft text_10 valignMiddle nowrap">
                        <b>Signature Date:&nbsp;</b>
                        <?php echo $signDateTimeFormatNew;?>
                    </td>
                </tr>
            </table>
	<?php
		}
	}else if($userIdentity == "Witness1") {
	
		if($delSign=="yes") {
			$callJavaFunWitness = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Witness1');";
	?>	
                <table style="border:none; border-collapse:collapse;">
                    <tr>
                        <td class="text_10b nowrap" style="cursor:pointer;<?php echo $consentSignBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunWitness;?>">Witness Signature</td>
                    </tr>
                </table>
	<?php	
		}else {
			$callJavaFunWitnessDel = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1NameId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Witness1','delSign');";
	?>
                <table style="border:none; border-collapse:collapse;">
                    <tr>
                        <td class="text_10 nowrap" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"><?php echo "<b>Witness:</b>"." ".$loggedInUserName; ?></td>
                    </tr>
                    <tr>
                        <td class="text_10 nowrap" >
                            <b>Electronically Signed :&nbsp;</b>
                            <?php echo $signOnFileStatus;?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text_10 nowrap" >
                            <b>Signature Date:&nbsp;</b>
                            <?php echo $signDateTimeFormatNew;?>
                        </td>
                    </tr>
                </table>
	<?php
		}
	}
	?>
	
<script>
top.setPNotesHeight();
</script>