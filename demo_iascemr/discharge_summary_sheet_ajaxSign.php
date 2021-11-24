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
include("common/linkfile.php");
include_once("common/commonFunctions.php"); 
		
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
	
	$SaveSignQry = "update `dischargesummarysheet` set 
								$signUserId 			= '".$loggedInUserId."',
								$signUserFirstName 		= '".addslashes($loggedInUserFirstName)."', 
								$signUserMiddleName 	= '".addslashes($loggedInUserMiddleName)."',
								$signUserLastName 		= '".addslashes($loggedInUserLastName)."', 
								$signUserStatus 		= '".$signOnFileStatus."',
								$signUserDateTime 		= '".$signDateTime."'
								WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
								
	$SaveSignRes = imw_query($SaveSignQry) or die(imw_error());
		
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
	
	if($userIdentity == "Surgeon1") {
		if($delSign=="yes") {
			$callJavaFunSurgeon = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','discharge_summary_sheet_ajaxSign.php','$loginUserId','Surgeon1');";
	?>
    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColorAjax?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
	<?php	
		
		}else {
			$callJavaFunSurgeonDel = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','discharge_summary_sheet_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
	?>
    
     <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>"." Dr. ".$loggedInUserName; ?> </a></span>	     
    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="dischargesummarysheet" data-id-value="<?=$_REQUEST["pConfId"]?>" data-id-name="confirmation_id"> <?php echo $signDateTimeFormatNew;  echo' <span class="fa fa-edit fa-editsurg"></span>'; ?> </span></span></span>
     
	<?php
		}
	}
	?>
	
<script>
top.setPNotesHeight();
</script>