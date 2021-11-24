<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<script type="text/javascript" src="js/moocheck.js"></script>
<script>
function checkPassowrd(objElem){
	var objFrm = objElem.form;
	if(objElem.value != ""){
		var objPass = objFrm.elem_newPassword;
		if(objPass.value != objElem.value){
			alert("Please Confirm password again.");
			objElem.value = "";
		}
	}
}
function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}
function countCharFn(obj, prevPass, id){
	var str = obj.value;
	var len = str.length;
	var letterExists ='';
	var specialCharExists ='';
	var numericExists ='';
	var strChar = '';
	
	for(var i=0; i<len; i++){
		strChar = str.charAt(i);
		if(isLetter(strChar)) {
			letterExists = "true";		
		}else if(isNaN(strChar)){
			specialCharExists = "true";
		}else{
			numericExists = "true";
		}
	}
	if((letterExists != "true") || (specialCharExists != "true") || (numericExists != "true")){
		alert("Must contain alphabet, numeric and special characters.")
		obj.value = '';
		return false;		
	}	
	if((prevPass!=str) && (prevPass!='')){
		if(len<8){
			alert("Must be at least 8 characters long.")
			obj.value = '';
		}
	}else{
		if((len<8) && (!id)){
			alert("Must be at least 8 characters long.")
			obj.value = '';
		}
	}
}

function chkSaveFn() {
	var dbCur_pass = document.frmChangePassword.hidd_currPass.value;
	var elem_currentPassword = document.frmChangePassword.elem_currentPassword.value;
	var elem_newPassword = document.frmChangePassword.elem_newPassword.value;
	var elem_confirmPassword = document.frmChangePassword.elem_confirmPassword.value;
	if(!elem_newPassword) {
		alert('Please fill New Password');
		document.frmChangePassword.elem_newPassword.focus();
		return false;
	}else if(!elem_confirmPassword) {
		alert('Please fill Confirm Password');
		document.frmChangePassword.elem_confirmPassword.focus();
		return false;
	}else if(elem_newPassword!=elem_confirmPassword) {
		alert('Invalid Confirm Password');
		document.frmChangePassword.elem_confirmPassword.focus();
		return false;
	
	}else {
		document.frmChangePassword.submit();
		return true;
	}
}
</script>
</head>
<body>
<?php
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['iolink_loginUserId'];
$elem_usersId = $_POST['elem_usersId'];
$save_pass = $_POST['save_pass'];

$elem_currentPassword = $_POST['elem_currentPassword'];
$elem_newPassword = $_POST['elem_newPassword'];
$elem_confirmPassword = $_POST['elem_confirmPassword'];

//if(!$save_pass) {
	$getUserDetails = $objManageData->getRowRecord('users', 'usersId', $loginUser);
	$elem_usersId = $user_id;
	$elem_database_currentPassword = $getUserDetails->user_password;	
	$passCreatedDate = date("Y-m-d");
//}
if($save_pass) {
	$getEncryptedCurrentPassStr = "SELECT PASSWORD('$elem_currentPassword')";
	$getEncryptedCurrentPassQry = imw_query($getEncryptedCurrentPassStr);
	$getEncryptedCurrentPassRow = imw_fetch_array($getEncryptedCurrentPassQry);
	$encryptedCurrentPassword = $getEncryptedCurrentPassRow['0'];
	if($elem_database_currentPassword<>$encryptedCurrentPassword) {
		echo "<script>alert('Invalid Current Password');</script>";
	}else {
	
		$getEncryptedNewPassStr = "SELECT PASSWORD('$elem_newPassword')";
		$getEncryptedNewPassQry = imw_query($getEncryptedNewPassStr);
		$getEncryptedNewPassRow = imw_fetch_array($getEncryptedNewPassQry);
		$elem_newPassword = $getEncryptedNewPassRow['0'];
		
		$getPasswordListStr = "SELECT * FROM lasusedpassword 
								WHERE user_id = '$loginUser' AND  
								(password1 = '$elem_newPassword'
								 OR password2 = '$elem_newPassword'
								 OR password3 = '$elem_newPassword'
								 OR password4 = '$elem_newPassword'
								 OR password5 = '$elem_newPassword'
								 OR password6 = '$elem_newPassword'
								 OR password7 = '$elem_newPassword'
								 OR password8 = '$elem_newPassword'
								 OR password9 = '$elem_newPassword'
								 OR password10 = '$elem_newPassword'
								)";
								
		$getPasswordListQry = imw_query($getPasswordListStr);
		$getPasswordListNumRows = imw_num_rows($getPasswordListQry);
		if($getPasswordListNumRows>0) {
			echo "<script>alert('Password Recently Used');</script>";
		}else {				
			$updateNewPaswordQry = "update users SET 
									 user_password='".$elem_newPassword."',
									 locked='0',
									 loginAttempts='0',
									 passCreatedOn ='".$passCreatedDate."'
									 WHERE usersId = '".$loginUser."'";
			$updateNewPaswordRes = imw_query($updateNewPaswordQry) or die(imw_error());						 
			
			
			// CHANGE STATUS FOR PASSWORD TO AUDIT				
				unset($arrayStatusRecord);
				$arrayStatusRecord['user_id'] = $loginUser;
				$arrayStatusRecord['status'] = 'reset';
				$arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayStatusRecord, 'password_change_reset_audit_tbl');
			// CHANGE STATUS FOR PASSWORD TO AUDIT  
			
			//SAVE NEW PASSWORD IN lasusedpassword TABLE
			$setLastPasswordQry = "SELECT * FROM `lasusedpassword` 
								WHERE user_id = '$loginUser'";
			$setLastPasswordRes = imw_query($setLastPasswordQry) or die(imw_error());					
			$setLastPasswordNumRow = imw_num_rows($setLastPasswordRes);
			if($setLastPasswordNumRow>0) {
				$setLastPasswordRow = imw_fetch_array($setLastPasswordRes);
				if($setLastPasswordRow['password1']=='') {
					$passwordFieldName = 'password1';
				}else if($setLastPasswordRow['password2']=='') {
					$passwordFieldName = 'password2';
				}else if($setLastPasswordRow['password3']=='') {
					$passwordFieldName = 'password3';
				}else if($setLastPasswordRow['password4']=='') {
					$passwordFieldName = 'password4';
				}else if($setLastPasswordRow['password5']=='') {
					$passwordFieldName = 'password5';
				}else if($setLastPasswordRow['password6']=='') {
					$passwordFieldName = 'password6';
				}else if($setLastPasswordRow['password7']=='') {
					$passwordFieldName = 'password7';
				}else if($setLastPasswordRow['password8']=='') {
					$passwordFieldName = 'password8';
				}else if($setLastPasswordRow['password9']=='') {
					$passwordFieldName = 'password9';
				}else if($setLastPasswordRow['password10']=='') {
					$passwordFieldName = 'password10';
				}
				
				if($passwordFieldName) {
					$saveLastPasswordQry = "update `lasusedpassword` set $passwordFieldName = '$elem_newPassword' WHERE user_id = '$loginUser'";
				}else {
					$saveLastPasswordQry = "update `lasusedpassword` set 
											password1 = '".$setLastPasswordRow['password2']."', 
											password2 = '".$setLastPasswordRow['password3']."', 
											password3 = '".$setLastPasswordRow['password4']."', 
											password4 = '".$setLastPasswordRow['password5']."', 
											password5 = '".$setLastPasswordRow['password6']."', 
											password6 = '".$setLastPasswordRow['password7']."', 
											password7 = '".$setLastPasswordRow['password8']."', 
											password8 = '".$setLastPasswordRow['password9']."', 
											password9 = '".$setLastPasswordRow['password10']."', 
											password10 = '".$elem_newPassword."' 
											WHERE user_id = '$loginUser'";
				
				}
				$saveLastPasswordRes = imw_query($saveLastPasswordQry) or die(imw_error());					 
			}else{
				$saveLastPasswordQry = "insert into `lasusedpassword` set password1 = '$elem_newPassword'";
				$saveLastPasswordRes = imw_query($saveLastPasswordQry) or die(imw_error());					 
			}
			//END SAVE NEW PASSWORD IN lasusedpassword TABLE
			echo "<script>alert('Password Changed');</script>";
			echo "<script>top.location.href = 'home.php';</script>";
			
		} 	
	}
}
?>
<form name="frmChangePassword" action="change_password.php" class="wufoo topLabel" method="post" > <!--  onsubmit="return checkForm(this)"-->
    <input type="hidden" name="elem_usersId" value="<?php  echo $elem_usersId;?>">
    <input type="hidden" name="hidd_currPass" value="<?php  echo $elem_currentPassword;?>">
    <input type="hidden" name="save_pass" value="<?php  echo 'yes';?>">
    <table class="table_collapse alignCenter" style="border:none;overflow:auto; background-color:#ECF1EA;">
        <tr>
            <td class="text_10" style="width:100%; padding-left:220px;">
                <table class="table_pad_bdr alignCenter" style="border:none; width:760px;">
                    <tr >
                      <td colspan="3">
                          <table class="table_collapse alignLeft" style=" border:none;">
                              <tr>
                                <td class="padd0"><img alt="" style="border:none;" src="images/left_new.gif"></td>
                                <td class="text_10bAdmin padd0 valignTop" style="width:100%;">
                                    <table class="table_collapse alignCenter" style="border:none;">
                                        <tr style="height:22px;">
                                          <td class="text_10b" style="background-color:#c0aa1e;">Change Password</td>
                                          <td class="text_10b alignLeft" style="background-color:#c0aa1e;"></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="padd0"><img alt="" style="border:none;" src="images/right_new.gif"></td>
                              </tr>
                          </table>
                       </td>
                    </tr>
                    
                    <tr class="valignTop">
                        <td style="width:1px;"><img alt="" style="border:none; height:400px; width:1px;" src="images/line_new.GIF"></td>
                        <td class="alignCenter">
                            <table class="table_collapse" style="border:none;">
                                <tr style="height:50px;">
                                    <td colspan="2"></td>
                                </tr>
                                <tr style="height:22px;">
                                    <td class="text_10b alignRight" style=" padding-left:9px;" >Current Password</td>
                                    <td class="text_10b alignLeft" style=" padding-left:9px;">
                                        <input type="password" name="elem_currentPassword" size="28" value="<?php echo $elem_currentPassword; ?>" class="text_10 all_border" >
                                    </td>
                                </tr>	
                                <tr style="height:22px;">
                                    <td class="text_10b alignRight" style=" padding-left:24px;" >New Password</td>
                                    <td class="text_10b alignLeft" style=" padding-left:9px;">
                                        <input type="password" name="elem_newPassword" size="28" value="<?php echo $elem_newPassword; ?>" class="text_10 all_border" onBlur="return countCharFn(this, '<?php echo $elem_newPassword; ?>', '<?php echo $elem_usersId; ?>')" >
                                    </td>
                                </tr>	
                                <tr style="height:22px;">
                                    <td class="text_10b alignRight" style=" padding-left:29px;" >Confirm Password</td>
                                    <td class="text_10b alignLeft" style=" padding-left:9px;">
                                        <input type="password" size="28" name="elem_confirmPassword" value="<?php echo $elem_confirmPassword; ?>" onChange="checkPassowrd(this)" class="text_10 all_border" onBlur="return countCharFn(this, '<?php echo $elem_newPassword; ?>', '<?php echo $elem_usersId; ?>')" >
                                    </td>
                                </tr>
                                
                                
                            </table>			
                        </td>
                        <td style="width:1px;" ><img alt="" style="border:none; height:400px; width:1px;" src="images/line_new.GIF"></td>
                    </tr>
                    
                    
                    <tr >
                      <td colspan="3">
                          <table class="table_collapse alignLeft" style=" border:none;">
                              <tr>
                                <td class="padd0"><img style="border:none;" alt="" src="images/bottomLeft_new.gif"></td>
                                <td class="text_10bAdmin padd0 valignTop" style="width:100%;">
                                    <table class="table_collapse alignCenter" style="border:none;">
                                        <tr style="height:22px;">
                                          <td class="text_10b" style="background-color:#c0aa1e; width:80px;padding-left:330px;"><a href="#" onClick="MM_swapImage('saveButton','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveButton','','images/save_hover1.jpg',1)"><img src="images/save.jpg" id="saveButton" style="display:block;border:none; "  alt="Save" onClick="return chkSaveFn();" /></a> </td>
                                          <td class="text_10b alignLeft" style="background-color:#c0aa1e;"><a href="#" onClick="MM_swapImage('cancelButton','','images/cancel_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('cancelButton','','images/cancel_hover1.jpg',1)"><img src="images/cancel.jpg" id="cancelButton" style="display:block;width:70px; height:25px; border:none;" alt="Cancel" onClick="javascript:top.location.href='home.php';" /></a></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="padd0"><img style="border:none;" alt="" src="images/bottomLeft_new.gif"></td>
                              </tr>
                          </table>
                       </td>
                    </tr>
                </table>
                            
            </td>
        </tr>
    </table>
</form>
</body>
</html>