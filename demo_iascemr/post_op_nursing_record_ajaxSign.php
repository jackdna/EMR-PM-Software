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
		$_GET['preColor'] = "#".$_GET['preColor'];
			$loggedInUserId = $_GET['loggedInUserId'];
			$signOnFileStatus = 'Yes';
			$signDateTime = date("Y-m-d H:i:s");
			$delSign = $_GET['delSign'];
			
		//GET USER NAME
			$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
			$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
			$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
			
			$loggedInUserFirstName = $ViewUserNameRow["fname"];
			$loggedInUserMiddleName = $ViewUserNameRow["mname"];
			$loggedInUserLastName = $ViewUserNameRow["lname"];
			//$sign_postop_nurse_date_time=date("m-d-Y h:i A",strtotime($signDateTime));
			$sign_postop_nurse_date_time = $objManageData->getFullDtTmFormat($signDateTime);
			$NurseName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
		//END GET USER  NAME
			$loginUserId = $loggedInUserId;
			
			if($delSign=="yes") {

				$postNurseSignBckGroundColor=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
				$loggedInUserId="";
				$loggedInUserFirstName="";
				$loggedInUserMiddleName="";
				$loggedInUserLastName="";
				$signOnFileStatus="";
				$signDateTime="";
			}
			
			$SaveSignQry = "update `postopnursingrecord` set 
										signNurseId 			= '".$loggedInUserId."',
										signNurseFirstName		= '".addslashes($loggedInUserFirstName)."', 
										signNurseMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										signNurseLastName 		= '".addslashes($loggedInUserLastName)."', 
										signNurseStatus 		= '".$signOnFileStatus."',
										signNurseDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl		
		
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE

		if($delSign=="yes") {
			$callJavaFun = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId');";
	?>
			<a data-target="#TDnurseSignatureId" data-toggle="collapse" class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</a>
	<?php	
		}else {
			$callJavaFunDel = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId','delSign');";
	?>
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $NurseName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="postopnursingrecord" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $sign_postop_nurse_date_time; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
            
	<?php
		}
	?>
