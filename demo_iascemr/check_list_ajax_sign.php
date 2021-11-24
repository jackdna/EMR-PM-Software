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
$objManageData 	= new manageData;

	//get chart status to remove edit time for signature option
	$query=imw_query("select finalize_status from patientconfirmation where patientConfirmationId='".$_REQUEST["pConfId"]."'")or die(imw_error());
	$data=imw_fetch_object($query);
	$finalizeStatus=$data->finalize_status;
	//$query.close;
	//SAVE USER SIGNATURE
		
			$loggedInUserId = $_GET['loggedInUserId'];
			$signOnFileStatus = 'Yes';
			$userIdentity = $_GET['userIdentity'];
			$delSign=$_GET['delSign'];
			if($userIdentity=="nurse1" || $delSign=="yes_1"){
				$signNurseId='signNurse1Id';
				$signNurseFirstName='signNurse1FirstName'; 
				$signNurseMiddleName='signNurse1MiddleName';
				$signNurseLastName='signNurse1LastName';   
				$signNurseFileStatus='signNurse1FileStatus';
				$signNurseDateTime='signNurse1DateTime';
			}else if($userIdentity=="nurse2" || $delSign=="yes_2"){
				$signNurseId='signNurse2Id';
				$signNurseFirstName='signNurse2FirstName'; 
				$signNurseMiddleName='signNurse2MiddleName';
				$signNurseLastName='signNurse2LastName';   
				$signNurseFileStatus='signNurse2FileStatus';
				$signNurseDateTime='signNurse2DateTime';
			}
			else if($userIdentity=="nurse3" || $delSign=="yes_3"){
				$signNurseId='signNurse3Id';
				$signNurseFirstName='signNurse3FirstName'; 
				$signNurseMiddleName='signNurse3MiddleName';
				$signNurseLastName='signNurse3LastName';   
				$signNurseFileStatus='signNurse3FileStatus';
				$signNurseDateTime='signNurse3DateTime';
			}
			else if($userIdentity=="nurse4" || $delSign=="yes_4"){
				$signNurseId='signNurse4Id';
				$signNurseFirstName='signNurse4FirstName'; 
				$signNurseMiddleName='signNurse4MiddleName';
				$signNurseLastName='signNurse4LastName';   
				$signNurseFileStatus='signNurse4FileStatus';
				$signNurseDateTime='signNurse4DateTime';
			}	
			
			//GET USER NAME
				$signDateTime="";
				$signDateTime = date("Y-m-d H:i:s");
				$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
				$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
				$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
					
				$loggedInUserFirstName = $ViewUserNameRow["fname"];
				$loggedInUserMiddleName = $ViewUserNameRow["mname"];
				$loggedInUserLastName = $ViewUserNameRow["lname"];
				
				$NurseName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
				//END GET USER  NAME
				$loginUserId = $loggedInUserId;
				$signDateTimeShow = $objManageData->getFullDtTmFormat($signDateTime);
				$sign_nurse1_sign_date=$signDateTimeShow;
				$sign_nurse2_sign_date=$signDateTimeShow;
				$sign_nurse3_sign_date=$signDateTimeShow;
				$sign_nurse4_sign_date=$signDateTimeShow;
				
				if($delSign=="yes_1" ||$delSign=="yes_2" || $delSign=="yes_3" || $delSign=="yes_4"){
					$nurseSignBckGroundColorAjax=$chngBckGroundColorAjax;
					$loggedInUserId='';
					$loggedInUserFirstName=''; 
					$loggedInUserMiddleName='';
					$loggedInUserLastName='';   
					$signOnFileStatus='';
					$signDateTime='';
				}		
				$SaveSignQry = "update `surgical_check_list` set 
									$signNurseId 			= '".$loggedInUserId."',
									$signNurseFirstName 	= '".addslashes($loggedInUserFirstName)."', 
									$signNurseMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
									$signNurseLastName 		= '".addslashes($loggedInUserLastName)."', 
									$signNurseFileStatus 	= '".$signOnFileStatus."',
									$signNurseDateTime 		= '".$signDateTime."'
									WHERE confirmation_id	= '".$_REQUEST["pConfId"]."'";
									
				$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
				
			//see later
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE//
			
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl		
	if($userIdentity=="nurse1"){	
		if($delSign=="yes_1"){
			$callJavaFun = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','check_list_ajax_sign.php','$loginUserId','nurse1');";
	?>
				<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
		<?php	
			}else {
				$callJavaFunDel = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','check_list_ajax_sign.php','$loginUserId','nurse1','delSign1');";
		?>		
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo $NurseName;?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="surgical_check_list" data-id-value="<?=$_REQUEST[pConfId]?>" data-id-name="confirmation_id"> <?php echo $sign_nurse1_sign_date; echo' <span class="fa fa-edit"></span>'; ?></span></span>
		<?php
			}
	}else if($userIdentity=="nurse2"){
		if($delSign=="yes_2"){
			$callJavaFun = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse2SignatureId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','check_list_ajax_sign.php','$loginUserId','nurse2');";
	?>
				<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
		<?php	
			}else {
				$callJavaFunDel = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse2SignatureId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','check_list_ajax_sign.php','$loginUserId','nurse2','delSign2');";
		?>		
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo $NurseName;?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse2DateTime" data-table-name="surgical_check_list" data-id-value="<?=$_REQUEST[pConfId]?>" data-id-name="confirmation_id"> <?php echo $sign_nurse2_sign_date;  echo' <span class="fa fa-edit"></span>'; ?></span></span>
		<?php
			}
	}else if($userIdentity=="nurse3"){
		if($delSign=="yes_3"){
			$callJavaFun = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse3SignatureId'; return displaySignature('TDnurse3NameId','TDnurse3SignatureId','check_list_ajax_sign.php','$loginUserId','nurse3');";
	?>
                
				<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
		<?php	
			}else {
				$callJavaFunDel = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse3SignatureId'; return displaySignature('TDnurse3NameId','TDnurse3SignatureId','check_list_ajax_sign.php','$loginUserId','nurse3','delSign3');";
		?>		
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo $NurseName;?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse3DateTime" data-table-name="surgical_check_list" data-id-value="<?=$_REQUEST[pConfId]?>" data-id-name="confirmation_id"> <?php echo $sign_nurse3_sign_date;  echo' <span class="fa fa-edit"></span>'; ?></span></span>
		<?php
			}
	}else if($userIdentity=="nurse4"){
		if($delSign=="yes_4"){
			$callJavaFun = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse4SignatureId'; return displaySignature('TDnurse4NameId','TDnurse4SignatureId','check_list_ajax_sign.php','$loginUserId','nurse4');";
	?>
                
				<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
		<?php	
			}else {
				$callJavaFunDel = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse4SignatureId'; return displaySignature('TDnurse4NameId','TDnurse4SignatureId','check_list_ajax_sign.php','$loginUserId','nurse4','delSign4');";
		?>		
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo $NurseName;?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse4DateTime" data-table-name="surgical_check_list" data-id-value="<?=$_REQUEST[pConfId]?>" data-id-name="confirmation_id"> <?php echo $sign_nurse4_sign_date; echo' <span class="fa fa-edit"></span>'; ?></span></span>
		<?php
			}
	}

		imw_close();
	?>	 