<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("../common/commonFunctions.php");
include_once("../common/user_agent.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$isHTML5OK = isHtml5OK();
$frmName = $_POST["frmName"];
$passCreatedOn = date('Y-m-d');
$userPrivileges = $_REQUEST['userPrivileges'];
$elem_privileges = implode(', ', $userPrivileges);
	
if(in_array("Admin", $userPrivileges)){
	$adminOther = $_REQUEST['adminOther'];
	if(count($adminOther)>0){
		$elemAdminPrivileges = implode(', ', $adminOther);
	}
}
	
switch($frmName){
	case "User Registration":
		$frmName=$_POST["frmName"];
		$elem_usersId=$_POST["elem_usersId"];
		$elem_mode=$_POST["elem_mode"];
		$elem_SwitchMode=$_POST["elem_SwitchMode"];
		$userTitle = $_POST["userTitleList"];
		$elem_fname=addslashes($_POST["elem_fname"]);
		$elem_mname=addslashes($_POST["elem_mname"]);
		$elem_lname=addslashes($_POST["elem_lname"]);
		$elem_initial=addslashes($_POST["elem_initial"]);
		$elem_typeTemp=$_POST["elem_type"];
		$elem_coordinator_type=$_POST["elem_coordinator_type"];
		$elem_hiddResetAccount=$_POST["elem_hiddResetAccount"];
		$maxPassExpiresDays = getData("maxPassExpiresDays", "surgerycenter", "surgeryCenterId", '1');	
		//START SET USER TYPE IN CASE OF CRNA -- (Certified Registered Nurse Anesthetist)
		$elem_typeExplode = explode('-',$elem_typeTemp);
		$elem_type=$elem_typeExplode[0];
		$elem_sub_type='';
		if($elem_typeExplode[1]=='CRNA') {
			$elem_sub_type=$elem_typeExplode[1];
		}
		//END SET USER TYPE IN CASE OF CRNA
		
		$elem_coordinator_type='';
		if($elem_type=='Coordinator') {
			$elem_coordinator_type=$_POST["elem_coordinator_type"];
		}
		$elem_iolink_max_booking='';
		if($elem_type=='Surgeon') {
			$elem_iolink_max_booking=$_POST["elem_iolink_max_booking"];
		}
		$elem_user_session_timeout	=	$_POST['elem_user_session_timeout'];
		$elem_practiceNameTempArr=$_POST["elem_practiceName"];
		$elem_practiceNameTemp=implode(",",$elem_practiceNameTempArr);
		$elem_practiceName=$elem_practiceNameTemp;
		/*if($_POST["hidd_practiceNameId"]=='practiceDropDown') {
			$elem_practiceNameTempArrD=$_POST["elem_practiceNameDropDown"];
		}*/
		$elem_specialty_id_multiTempArr=$_POST["elem_specialty_id_multi"];
		$elem_specialty_id_multiTemp=implode(",",$elem_specialty_id_multiTempArr);
		$elem_specialty_id_multi=$elem_specialty_id_multiTemp;
		
		
		$elem_address=addslashes($_POST["elem_address"]);
		$elem_address2=addslashes($_POST["elem_address2"]);
		$elem_city = addslashes($_POST["elem_city"]);
		$elem_state = $_POST["elem_state"];
		$elem_zip = $_POST["elem_zip"];
		$elem_contactName = addslashes($_POST["elem_contactName"]);
		$elem_phone=$_POST["elem_phone"];
		$elem_fax=$_POST["elem_fax"];
		$elem_email=$_POST["elem_email"];
		$elem_npi=$_POST["elem_npi"];
		$elem_lic=$_POST["elem_lic"];
		$elem_federalEin=$_POST["elem_federalEin"];
		$elem_sso_identifier=$_POST["elem_sso_identifier"];
		$elem_signature=$_POST["elem_signature"];
		
		
		$hippaReviewedYes = $_POST["hippaReviewedYes"];
		$hippaReviewedNo = $_POST["hippaReviewedNo"];
		if($hippaReviewedYes=="Yes"){
		?>
				<?php }
		
		if(($elem_signature == '255-0-0:;') || ($elem_signature == '0-0-0:;')){
			$elem_signature = '';
		}		
		$elem_signOnFile=$_POST["elem_signOnFile"];
		$elem_loginName=addslashes($_POST["elem_loginName"]);
		$elem_password=$_POST["elem_password"];
		$elem_confirmPass=$_POST["elem_confirmPass"];
		if($elem_mode == 2){
			$getEncryptedPassStr = "SELECT * FROM users WHERE usersId = '$elem_usersId' AND user_password = '$elem_password'";
			$getEncryptedPassQry = imw_query($getEncryptedPassStr);
			$getEncryptedPassRows = imw_num_rows($getEncryptedPassQry);
			if($getEncryptedPassRows>0){
				$encrypt = false;
				$getEncryptedPassRows = imw_fetch_array($getEncryptedPassQry);
				$elem_password = $getEncryptedPassRows['user_password'];
			}else{
				$encrypt = true;
			}
		}else{			
			$encrypt = true;
		}				
		if($encrypt == true){
			//Password
			$getEncryptedPassStr = "SELECT PASSWORD('$elem_password')";
			$getEncryptedPassQry = imw_query($getEncryptedPassStr);
			$getEncryptedPassRow = imw_fetch_array($getEncryptedPassQry);
			$elem_password = $getEncryptedPassRow['0'];
			//Confirm Pass
			$elem_confirmPass = $elem_password;
			
			// CHANGE STATUS FOR PASSWARD TO AUDIT				
				unset($arrayStatusRecord);
				//$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
				$arrayStatusRecord['user_id'] = $elem_usersId;
				$arrayStatusRecord['status'] = 'reset';
				$arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
				$arrayStatusRecord['operator_id'] = ($userLoginId ? $userLoginId : $_SESSION['loginUserId']);
				$arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
				$arrayStatusRecord['comments'] = 'Change password from admin users';
				$objManageData->addRecords($arrayStatusRecord, 'password_change_reset_audit_tbl');
			// CHANGE STATUS FOR PASSWARD TO AUDIT 
			
		}		
		$elem_lockAcc=$_POST["elem_lockAcc"];
		$getLockedOrNot = $objManageData->getRowRecord('users', 'usersId', $elem_usersId);
		if($elem_lockAcc == 1){
			if($getLockedOrNot->locked==0){
				// CHANGE STATUS FOR PASSWARD TO AUDIT				
					unset($arrayStatusRecord);
					$arrayStatusRecord['user_id'] = $elem_usersId;
					$arrayStatusRecord['status'] = 'locked';
					$arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
					$arrayStatusRecord['operator_id'] = ($userLoginId ? $userLoginId : $_SESSION['loginUserId']);
					$arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
					$arrayStatusRecord['comments'] = 'Locked from admin users';
					$objManageData->addRecords($arrayStatusRecord, 'password_change_reset_audit_tbl');
				// CHANGE STATUS FOR PASSWARD TO AUDIT
			}
		}else{
			if($getLockedOrNot->locked==1){
				// CHANGE STATUS FOR PASSWARD TO AUDIT				
					unset($arrayStatusRecord);
					$arrayStatusRecord['user_id'] = $elem_usersId;
					$arrayStatusRecord['status'] = 'unlocked';
					$arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
					$arrayStatusRecord['operator_id'] = ($userLoginId ? $userLoginId : $_SESSION['loginUserId']);
					$arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
					$arrayStatusRecord['comments'] = 'Unlocked from admin users';
					$objManageData->addRecords($arrayStatusRecord, 'password_change_reset_audit_tbl');
				// CHANGE STATUS FOR PASSWARD TO AUDIT 
			}
		}
		$elem_resetAcc=$_POST["elem_resetAcc"];
		$elem_submit=$_POST["elem_submit"];
		$sigSaveQry=='';
		if(!$isHTML5OK) {
			$sigSaveQry = "signature='$elem_signature',";
		}
		
		switch($elem_mode)
		{
			case "1": //Insert
				$passCreatedDate = date('Y-m-d');
				$sql = "INSERT INTO users SET
					  userTitle = '$userTitle',
					  fname='".trim($elem_fname)."',
					  mname='".trim($elem_mname)."',
					  lname='".trim($elem_lname)."',
						initial='".trim($elem_initial)."',
					  user_type='$elem_type',
					  user_sub_type='$elem_sub_type', 
					  coordinator_type='$elem_coordinator_type', 
					  iolink_max_booking='$elem_iolink_max_booking', 
					  session_timeout='$elem_user_session_timeout',
					  practiceId='$elem_practiceId', 
					  practiceName='$elem_practiceName',
					  specialty_id_multi='$elem_specialty_id_multi',  
					  address='$elem_address',
					  address2='$elem_address2',					  
					  user_city ='$elem_city',
					  user_state ='$elem_state',
					  user_zip ='$elem_zip',
					  contactName = '$elem_contactName',
					  phone='$elem_phone', 
					  fax='$elem_fax', 
					  email='$elem_email', 
					  npi='$elem_npi', 
					  lic='$elem_lic', 
					  federalEin='$elem_federalEin',
					  sso_identifier='$elem_sso_identifier',
					  ".$sigSaveQry."
					  signOnFile='$elem_signOnFile',
					  user_privileges='$elem_privileges', 
					  admin_privileges = '$elemAdminPrivileges',
					  loginName='".trim($elem_loginName)."',
					  user_password='$elem_password',
					  locked='$elem_lockAcc',
					  loginAttempts='$loginAttempts',
					  passCreatedOn ='$passCreatedDate'";
				$sqlQry = imw_query($sql);
				$insertId = imw_insert_id();
				if($sqlQry)
				{
					echo "<script>top.frames[0].alert_msg('success')</script>";
				}
				$opResult = "1";				
				
				if($isHTML5OK) {
					$elem_signature_path = $objManageData->save_user_image($_REQUEST,$insertId,$elem_fname,'user_sign');
					$signatureUpdateQry = "UPDATE users SET signature_path = '".$elem_signature_path."' WHERE usersId='".$insertId."'";
					$signatureUpdateRes = imw_query($signatureUpdateQry);
				}
				//createPasswordRecords
				$insertStr = "INSERT INTO lasusedpassword SET 
								user_id  = '$insertId',
								password1 = '$elem_password'";
				$insertQry = imw_query($insertStr);				
			break;
			case "2": //Update
				//GET PREVIOUS PASSWORD
				
				$maxRecentlyUsedPass = getData("maxRecentlyUsedPass", "surgerycenter", "surgeryCenterId", '1');				
				$getInfoStr = "SELECT user_password, passCreatedOn FROM users WHERE usersId = '$elem_usersId'";
				$getInfoQry = imw_query($getInfoStr);
				$getInfoRow = imw_fetch_array($getInfoQry);
					$presentPass = $getInfoRow['user_password'];
					$passCreatedDate = $getInfoRow['passCreatedOn'];
					if($elem_password!=$presentPass){
						$getPasswordListStr = "SELECT * FROM lasusedpassword WHERE user_id = '$elem_usersId'";
						$getPasswordListQry = imw_query($getPasswordListStr);
						$rows = imw_num_rows($getPasswordListQry);
						if($rows>0){
							$counter = 0;
							$getPasswordListRow = imw_fetch_assoc($getPasswordListQry);
							extract($getPasswordListRow);
							
							if(($password10!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password10==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;							
							}
							if(($password9!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password9==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;							
							}
							
							if(($password8!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password8==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;							
							}
							if(($password7!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password7==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}							
							if(($password6!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password6==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}
							if(($password5!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password5==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}
							if(($password4!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password4==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
	
							}
							if(($password3!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password3==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}
							if(($password2!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password2==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}
							if(($password1!='') && ($counter<=$maxRecentlyUsedPass)){
								if($password1==$elem_password){
									$updatePassword = 'False';
								}
								++$counter;
							}
						}
					}
					if($updatePassword!='False'){
						$updateStr = "UPDATE lasusedpassword SET ";
						if($password1==''){
							$updateStr .= "password1 = '$elem_password' ";
						}else if($password2==''){
							$updateStr .= "password2 = '$elem_password' ";
						}else if($password3==''){
							$updateStr .= "password3 = '$elem_password' ";
						}else if($password4==''){
							$updateStr .= "password4 = '$elem_password' ";
						}else if($password5==''){
							$updateStr .= "password5 = '$elem_password' ";
						}else if($password6==''){
							$updateStr .= "password6 = '$elem_password' ";
						}else if($password7==''){
							$updateStr .= "password7 = '$elem_password' ";
						}else if($password8==''){
							$updateStr .= "password8 = '$elem_password' ";
						}else if($password9==''){
							$updateStr .= "password9 = '$elem_password' ";
						}else if($password10==''){
							$updateStr .= "password10 = '$elem_password' ";
						}else{
							$updateStr .= "password1 = password2,
											password2 = password3,
											password3 = password4,
											password4 = password5,
											password5 = password6,
											password6 = password7,
											password7 = password8,
											password8 = password9,
											password9 = password10,
											password10 = '$elem_password' ";
						}
						$updateStr .= "WHERE user_id  = '$elem_usersId'";
						//echo $updateStr;
						$updateQry = imw_query($updateStr);					
					}
				//GET PREVIOUS PASSWORD
				if($updatePassword=='False'){
					$elem_password = $presentPass;
					$passCreatedDate = $passCreatedDate;
				}else{
					if($elem_password != $presentPass){
						$passCreatedDate = date('Y-m-d');
					}else{
						$passCreatedDate = $passCreatedDate;
					}
				}
				if($elem_hiddResetAccount == 'yes') {
					if(!$maxPassExpiresDays) {
						$maxPassExpiresDays = '1';//1 day
					}
					$passCreatedDate = $objManageData->getDateSubtract(date('Y-m-d'), $maxPassExpiresDays);	
				}
				
				if($elem_lockAcc==0){ $loginAttempts = 0; }
				if($isHTML5OK) {
					$elem_signature_path = $objManageData->save_user_image($_REQUEST,$elem_usersId,$elem_fname,'user_sign');
					$sigSaveQry = "signature_path = '".$elem_signature_path."',";
				}else {
					$sigSaveQry = "signature='".$elem_signature."',";	
				}
				$sql = "UPDATE users SET  
					  userTitle='".$userTitle."',
					  fname='".$elem_fname."', 
					  mname='".$elem_mname."', 
					  lname='".$elem_lname."',
						initial='".$elem_initial."',
					  user_type='".$elem_type."',
					  user_sub_type='".$elem_sub_type."',  
					  coordinator_type='".$elem_coordinator_type."',
					  iolink_max_booking='".$elem_iolink_max_booking."', 
					  session_timeout='".$elem_user_session_timeout."',
					  practiceId='".$elem_practiceId."', 
					  practiceName='".$elem_practiceName."', 
					  specialty_id_multi='".$elem_specialty_id_multi."', 
					  address='".$elem_address."',  
					  address2='".$elem_address2."',  
					  user_city='".$elem_city."',
					  user_state='".$elem_state."',
					  user_zip='".$elem_zip."',
					  contactName = '".$elem_contactName."',
					  phone='".$elem_phone."', 
					  fax='".$elem_fax."', 
					  email='".$elem_email."', 
					  npi='".$elem_npi."',
					  lic='".$elem_lic."',
					  federalEin='".$elem_federalEin."',
					  sso_identifier='".$elem_sso_identifier."',
					  ".$sigSaveQry."
					  signOnFile='".$elem_signOnFile."',
					  user_privileges='".$elem_privileges."',
					  admin_privileges ='".$elemAdminPrivileges."',
					  loginName='".$elem_loginName."',
					  user_password='".$elem_password."',
					  locked='".$elem_lockAcc."',
					  loginAttempts='".$loginAttempts."',
					  passCreatedOn ='".$passCreatedDate."'
					  WHERE usersId = '".$elem_usersId."'";
				
				$row = sqlQuery($sql);
				if($row)
				{
					echo "<script>top.frames[0].alert_msg('update')</script>";
				}

				$opResult = "2";
			break;
			case "3": //Delete
				$sql = "DELETE FROM lasusedpassword ".
					 "WHERE user_id ='".$elem_usersId."' ";
				$row = sqlQuery($sql);
				
				$sql = "DELETE FROM users ".
					 "WHERE usersId='".$elem_usersId."' ";
				$row = sqlQuery($sql);
				$opResult = "3";	
			break;
		}
		//if($elem_SwitchMode=="NewUser") {
			//$location = "userRegistrationForm.php";		
		//}else {
			$location = "listUsers.php";		
		//}	
	break;
}

?>
<script>

	var SwitchMode = '<?php echo $elem_SwitchMode; ?>';
	var updatePass = '<?php echo $updatePassword; ?>';
	if(SwitchMode=='NewUser') {
		top.frames[0].frames[0].frames[0].location = 'userRegistrationForm.php';
	}else {
		top.frames[0].frames[0].location = 'userRegistration.php?updatePassword='+updatePass;
	}
</script>