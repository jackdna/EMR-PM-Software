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
include_once("common/commonFunctions.php");
	//get chart status to remove edit time for signature option
	$query=imw_query("select finalize_status from patientconfirmation where patientConfirmationId='".$_REQUEST["pConfId"]."'")or die(imw_error());
	$data=imw_fetch_object($query);
	$finalizeStatus=$data->finalize_status;
	//$query.close;	
	//SAVE USER SIGNATURE
		
			$loggedInUserId = $_GET['loggedInUserId'];
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
			
			$NurseName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
		//END GET USER  NAME
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE USER SIGNATURE
				if($delSign=="yes") {
					$genAnesNurseSignBckGroundColor=$chngBckGroundColorAjax;//DEFINED IN  common/commonFunctions.php TO INDICATE MANDATORY FIELD
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE USER SIGNATURE
			
			$SaveSignQry = "update `genanesthesianursesnotes` set 
										signNurseId 			= '".$loggedInUserId."',
										signNurseFirstName 		= '".addslashes($loggedInUserFirstName)."', 
										signNurseMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
										signNurseLastName 		= '".addslashes($loggedInUserLastName)."', 
										signNurseStatus 		= '".$signOnFileStatus."',
										signNurseDateTime 		= '".$signDateTime."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
										
			$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
		
		//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
			$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
		//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
		
			if($delSign=="yes") {
				$callJavaFun = "document.frm_gen_anes_nurse_notes.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','gen_anes_nurse_notes_ajaxSign.php','$loginUserId');";
		?>
				<a  class="sign_link "  href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFun;?>" style="cursor:hand;<?php echo $genAnesNurseSignBckGroundColor;?>  " >Nurse Signature</a>
                
                
		<?php	
			
			}else {
				$callJavaFunDel = "document.frm_gen_anes_nurse_notes.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','gen_anes_nurse_notes_ajaxSign.php','$loginUserId','delSign');";
		?>

				<span class="rob full_width">
                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>">
                    		<?php  echo "<b>Nurse :</b>"." ".$NurseName ?>
              		</a>
                </span>	     
                 <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;  ?></span>
                 <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="genanesthesianursesnotes" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
                                                                                 
                
		<?php
			}
		?>		
