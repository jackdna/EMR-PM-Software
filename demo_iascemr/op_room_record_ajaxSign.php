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
			$signDateTimeFormatNew = $objManageData->getFullDtTmFormat(date($signDateTime));
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
	
	}else if($userIdentity == "Nurse2") {
		$signUserId = 'signNurse1Id';
		$signUserFirstName = 'signNurse1FirstName'; 
		$signUserMiddleName = 'signNurse1MiddleName';
		$signUserLastName = 'signNurse1LastName'; 
		$signUserStatus = 'signNurse1Status';
		$signUserDateTime = 'signNurse1DateTime';
	
	}else if($userIdentity == "Surgeon1") {
		$signUserId = 'signSurgeon1Id';
		$signUserFirstName = 'signSurgeon1FirstName'; 
		$signUserMiddleName = 'signSurgeon1MiddleName';
		$signUserLastName = 'signSurgeon1LastName'; 
		$signUserStatus = 'signSurgeon1Status';
		$signUserDateTime = 'signSurgeon1DateTime';
	
	}else if($userIdentity == "Surgeon2") {
		$signUserId = 'signSurgeon2Id';
		$signUserFirstName = 'signSurgeon2FirstName'; 
		$signUserMiddleName = 'signSurgeon2MiddleName';
		$signUserLastName = 'signSurgeon2LastName'; 
		$signUserStatus = 'signSurgeon2Status';
		$signUserDateTime = 'signSurgeon2DateTime';
	
	}else if($userIdentity == "Surgeon3") {
		$signUserId = 'signSurgeon3Id';
		$signUserFirstName = 'signSurgeon3FirstName'; 
		$signUserMiddleName = 'signSurgeon3MiddleName';
		$signUserLastName = 'signSurgeon3LastName'; 
		$signUserStatus = 'signSurgeon3Status';
		$signUserDateTime = 'signSurgeon3DateTime';
	
	}else if($userIdentity == "Anesthesia1") {
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
	
	}else if($userIdentity == "ScrubTech1") {
		$signUserId = 'signScrubTech1Id';
		$signUserFirstName = 'signScrubTech1FirstName'; 
		$signUserMiddleName = 'signScrubTech1MiddleName';
		$signUserLastName = 'signScrubTech1LastName'; 
		$signUserStatus = 'signScrubTech1Status';
		$signUserDateTime = 'signScrubTech1DateTime';
	
	}else if($userIdentity == "ScrubTech2") {
		$signUserId = 'signScrubTech2Id';
		$signUserFirstName = 'signScrubTech2FirstName'; 
		$signUserMiddleName = 'signScrubTech2MiddleName';
		$signUserLastName = 'signScrubTech2LastName'; 
		$signUserStatus = 'signScrubTech2Status';
		$signUserDateTime = 'signScrubTech2DateTime';
	
	}
//END GET FIELD NAME ACCORDING TO USER IDENTITY			
			
			$loginUserId = $loggedInUserId;
			//CODE TO REMOVE USER SIGNATURE
				if($delSign=="yes") {
					$loggedInUserId="";
					$loggedInUserFirstName="";
					$loggedInUserMiddleName="";
					$loggedInUserLastName="";
					$signOnFileStatus="";
					$signDateTime="";
				}
			//END CODE TO REMOVE USER SIGNATURE
			
			$SaveSignQry = "update `operatingroomrecords` set 
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
			$callJavaFun = "document.frm_op_room.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1');";
	?>
			
      <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
	<?php	
		
		}else {
			$callJavaFunDel = "document.frm_op_room.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	?>
			
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="operatingroomrecords" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?></span></span>

	<?php
		}
	}else if($userIdentity == "Nurse2") {
		if($delSign=="yes") {
			$callJavaFunNurse1 = "document.frm_op_room.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2');";
	?>
			
            <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunNurse1;?>">Nurse Signature</a>
	<?php	
		
		}else {
			$callJavaFunNurse1Del = "document.frm_op_room.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2','delSign');";
	?>
			
            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurse1Del;?>"> <?php echo "<b>Nurse:</b>". " ". $loggedInUserName; ?> </a></span>	     
            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="operatingroomrecords" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew; echo' <span class="fa fa-edit"></span>'; ?> </span></span>
	<?php
		}
	}else if($userIdentity == "Surgeon1") {
		if($delSign=="yes") {
			$callJavaFunSurgeon = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1');";
	?>
			<table class="table_pad_bdr">
				<tr>
					<td style="cursor:pointer;" class="text_10b nowrap" onClick="javascript:<?php echo $callJavaFunSurgeon;?>">Surgeon Signature</td>
				</tr>
			</table>
	<?php	
		}else {
			$callJavaFunSurgeonDel = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
			<table class="table_collapse">
				<tr>
					<td style="width:20%;" class="text_10 nowrap"><?php echo "<b>Surgeon : </b>"; ?></td>
					<td class="text_10 nowrap" style="width:55%; cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"><?php echo " Dr. ".$loggedInUserName;?></td>
					<td style="width:5%;" class="text_10">&nbsp;</td>
					<td style="width:20%;" class="text_10 nowrap">
						<b>Electronically Signed :&nbsp;</b>
						<?php echo $signOnFileStatus;?>
					</td>
				</tr>
			</table>
	<?php
		}
	}else if($userIdentity == "Surgeon2") {
		if($delSign=="yes") {
			$callJavaFunSurgeon2 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2SignatureId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2');";
	?>
			<table class="table_collapse">
				<tr>
					<td style="cursor:pointer;" class="text_10b nowrap" onClick="javascript:<?php echo $callJavaFunSurgeon2;?>">
						<img src="images/tpixel.gif" style="width:3px;"/>Surgeon1 Signature
					</td>
				</tr>
			</table>
	<?php	
		
		}else {
			$callJavaFunSurgeon2Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2NameId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2','delSign');";
	?>
			<table class="table_collapse">
				<tr>
					<td class="text_10 nowrap" style="width:70%; cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeon2Del;?>">
						<img src="images/tpixel.gif" style="width:3px;"/><b>Surgeon1 :</b><?php echo "Dr. ".$loggedInUserName; ?>
					</td>
					<td colspan="2" class="text_10 nowrap"><b>Electronically Signed :</b><img src="images/tpixel.gif" style="width:3px;"/><?php echo $signOnFileStatus;?></td>
				</tr>
			</table>
	
	<?php
		}
	}else if($userIdentity == "Surgeon3") {
		if($delSign=="yes") {
			$callJavaFunSurgeon3 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3SignatureId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3');";
	?>
			<table class="table_collapse">
				<tr>
					<td style="cursor:pointer;" class="text_10b nowrap" onClick="javascript:<?php echo $callJavaFunSurgeon3;?>">
						<img src="images/tpixel.gif" style="width:3px;">Surgeon2 Signature
					</td>
				</tr>
			</table>
	<?php	
		
		}else {
			$callJavaFunSurgeon3Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3NameId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3','delSign');";
	?>
			<table class="table_collapse">
				<tr>
					<td class="text_10 nowrap" style="width:70%; cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeon3Del;?>">
						<img src="images/tpixel.gif" style="width:3px;"/><b>Surgeon2 :</b><?php echo "Dr. ".$loggedInUserName; ?>
					</td>
					<td colspan="2" class="text_10 nowrap"><b>Electronically Signed :</b><img src="images/tpixel.gif" style="width:3px;"/><?php echo $signOnFileStatus;?></td>
				</tr>
			</table>

	<?php
		}
	}else if($userIdentity == "Anesthesia1") {
		if($delSign=="yes") {
			$callJavaFunAnes = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1');";
	?>
			<table class="table_pad_bdr">
				<tr>
					<td style="cursor:pointer;" class="text_10b nowrap" onClick="javascript:<?php echo $callJavaFunAnes;?>">Anesthesiologist Signature</td>
				</tr>
			</table>
	<?php	
		
		}else {
			$callJavaFunAnesDel = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
	?>
			<table class="table_collapse">
				<tr>
					<td style="width:20%;" class="text_10 nowrap"><?php echo "<b>Anesthesiologist : </b>"; ?></td>
					<td class="text_10 nowrap" style="width:55%; cursor:pointer;" onClick="javascript:<?php echo $callJavaFunAnesDel;?>"><?php echo " Dr. ".$loggedInUserName;?></td>
					<td style="width:5%;" class="text_10 nowrap">&nbsp;</td>
					<td style="width:20%;" class="text_10 nowrap">
						<b>Electronically Signed :&nbsp;</b>
						<?php echo $signOnFileStatus;?>
					</td>
				</tr>
			</table>
	<?php
		}
	}else if($userIdentity == "Anesthesia2") {
		if($delSign=="yes") {
			$callJavaFunAnes2 = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2');";
	?>
			<table class="table_collapse">
				<tr>
					<td style="cursor:pointer;" class="text_10b nowrap" onClick="javascript:<?php echo $callJavaFunAnes2;?>">
						<img src="images/tpixel.gif" style="width:3px;"/>Anesthesiologist Signature
					</td>
				</tr>
			</table>
	<?php	
		
		}else {
			$callJavaFunAnes2Del = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2','delSign');";
	?>
			<table class="table_collapse">
				<tr>
					<td class="text_10 nowrap" style="width:70%; cursor:pointer;" onClick="javascript:<?php echo $callJavaFunAnes2Del;?>">
						<img src="images/tpixel.gif" style="width:3px;"><b>Anesthesiologist :</b><?php echo "Dr. ".$loggedInUserName; ?>
					</td>
					<td colspan="2" class="text_10 nowrap"><b>Electronically Signed :</b><img src="images/tpixel.gif" style="width:3px;"/><?php echo $signOnFileStatus;?></td>
				</tr>
			</table>
	
	<?php
		}
	}else if($userIdentity == "ScrubTech1") {
	?>
		<!-- <div id="TDscrubTech1SignatureId" style="display:<?php //echo $TDscrubTech1SignatureIdDisplay;?>; "> -->			
			<table class="table_collapse">
				<tr>
					<td style="width:70%;" class="text_10 nowrap">
						<img src="images/tpixel.gif" style="width:3px;"/><b>Scrub Tech1 :</b><?php echo $loggedInUserName; ?>
					</td>
					<td colspan="2" class="text_10 nowrap"><b>Electronically Signed :</b><img src="images/tpixel.gif" style="width:3px;"/><?php echo $signOnFileStatus;?></td>
				</tr>
			</table>
		<!-- </div> -->
	<?php
	}else if($userIdentity == "ScrubTech2") {
	?>
		<!-- <div id="TDscrubTech2SignatureId" style="display:<?php //echo $TDscrubTech2SignatureIdDisplay;?>; ">			 -->
			<table class="table_collapse">
				<tr>
					<td style="width:70%;" class="text_10 nowrap">
						<img src="images/tpixel.gif" style="width:3px;"/><b>Scrub Tech2 :</b><?php echo $loggedInUserName; ?>
					</td>
					<td colspan="2" class="text_10 nowrap"><b>Electronically Signed :</b><img src="images/tpixel.gif" style="width:3px;"/><?php echo $signOnFileStatus;?></td>
				</tr>
			</table>
		<!-- </div> -->
	
	<?php
	}
	?>
<script>
top.setPNotesHeight();
</script>