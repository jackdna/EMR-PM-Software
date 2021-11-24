<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
$elem_usersId = $_POST['elem_usersId'];
$save_pass = $_POST['save_pass'];
$showCancelButton = $_REQUEST['showCancelButton'];
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
								WHERE user_id = '".$loginUser."' AND  
								(password1 = '".$elem_newPassword."'
								 OR password2 = '".$elem_newPassword."'
								 OR password3 = '".$elem_newPassword."'
								 OR password4 = '".$elem_newPassword."'
								 OR password5 = '".$elem_newPassword."'
								 OR password6 = '".$elem_newPassword."'
								 OR password7 = '".$elem_newPassword."'
								 OR password8 = '".$elem_newPassword."'
								 OR password9 = '".$elem_newPassword."'
								 OR password10 = '".$elem_newPassword."'
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
				$arrayStatusRecord['operator_id'] = $loginUser;
				$arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
				$arrayStatusRecord['comments'] = 'Change Password';
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
				$saveLastPasswordQry = "insert into `lasusedpassword` set password1 = '$elem_newPassword', user_id = '$loginUser'";
				$saveLastPasswordRes = imw_query($saveLastPasswordQry) or die(imw_error());					 
			}
			//END SAVE NEW PASSWORD IN lasusedpassword TABLE
			echo "<script>alert('Password Changed');</script>";
			echo "<script>top.location.href = 'home.php';</script>";
			
		} 	
	}
}
?>
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
	if(!str) return;
	var len = str.length;
	var letterExists ='';
	var specialCharExists ='';
	var numericExists ='';
	for(var i=0; i<len; i++){
		var strChar = str.charAt(i);
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
	/*
	if(dbCur_pass!=elem_currentPassword || elem_currentPassword=='') {
		alert('Current Password is invalid');
		document.frmChangePassword.elem_currentPassword.focus();
		return false;
	}else 
	*/
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
		//document.frmChangePassword.submit();
		return true;
	}
}
</script>

<div class="main_wrapper"  style="margin-top:-5px;">	
		
        <form name="frmChangePassword" id="frmChangePassword" action="change_password.php" class="wufoo topLabel" method="post" onsubmit="return checkForm()" > 
        		
                <input type="hidden" name="elem_usersId" value="<?php  echo $elem_usersId;?>" />
				<input type="hidden" name="hidd_currPass" value="<?php  echo $elem_currentPassword;?>"/>
                <input type="hidden" name="save_pass" value="<?php  echo 'yes';?>" />
                <input type="hidden" name="showCancelButton" value="<?php  echo $showCancelButton;?>"/>
                                    
                <div class="container-fluid padding_0">
                		
                        <div class="inner_surg_middle ">
                        
                        		<div class="all_content1_slider ">
                                		
                                        <div class="wrap_inside_admin">
                                        
                                        		<div class=" subtracting-head">
                                                
                                                		<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                                        		<span>Change Password</span>
                                                       	</div>
                                                        
                                               	</div>
                                                
                                        		<Div class="wrap_inside_admin  scrollable_yes">
                                                
                                                		<div class="col-md-2 visible-md"></div>
                                                        <div class="col-lg-3 visible-lg"></div>
                                                        
                                                        <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                                        
                                                        	<div class="audit_wrap">
                                                            
                                                            		<div class="form_outer">
                                                                    <!----------------------- Full Inout col-12    ------------------------------>
                                                                    
                                                                    	<div class="clearfix margin_adjustment_only"></div>
                                                                        
                                                                        <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                                                        	
                                                                            <div class="form_reg">
                                                                            
                                                                            	<div class="col-md-4 col-lg-4 col-xs-4 col-sm-3 text-center">
                                                                                		<label class="" for="elem_currentPassword"> Current Password</label>
                                                                               	</div>
                                                                                
                                                                                <div class="col-md-8 col-lg-8 col-xs-8 col-sm-9">
                                                                                		<input type="password" class="form-control" name="elem_currentPassword" id="elem_currentPassword" value="<?php echo $elem_currentPassword; ?>" />
                                                                               	</div> 
                                                                        	
                                                                            </div>
                                                                            
                                                                            <div class="clearfix margin_adjustment_only"></div>
                                                                            <div class="col-md-12 col-lg-12 col-xs-12 clearfix hidden-sm"></div>
                                                                            
                                                                            <div class="form_reg">
                                                                            
                                                                            	<div class="col-md-4 col-lg-4 col-xs-4 col-sm-3 text-center">
                                                                                		<label class="" for="elem_newPassword"> New Password</label>
                                                                               	</div>
                                                                                
                                                                                <div class="col-md-8 col-lg-8 col-xs-8 col-sm-9">
                                                                                		<input type="password" name="elem_newPassword" value="<?php echo $elem_newPassword; ?>" class="form-control" onBlur="return countCharFn(this, '<?php echo $elem_newPassword; ?>', '<?php echo $elem_usersId; ?>')" />
                                                                               	</div> 
                                                                        	
                                                                            </div>
                                                                            
                                                                            <div class="clearfix margin_adjustment_only"></div>
                                                                            <div class="col-md-12 col-lg-12 col-xs-12 clearfix hidden-sm"></div>
                                                                            
                                                                            
                                                                            <div class="form_reg">
                                                                            
                                                                            	<div class="col-md-4 col-lg-4 col-xs-4 col-sm-3 text-center">
                                                                                		<label class="" for="elem_confirmPassword"> Confirm Password</label>
                                                                               	</div>
                                                                                
                                                                                <div class="col-md-8 col-lg-8 col-xs-8 col-sm-9">
                                                                                		<input type="password" size="28" name="elem_confirmPassword" value="<?php echo $elem_confirmPassword; ?>" onChange="checkPassowrd(this)" class="form-control" onBlur="return countCharFn(this, '<?php echo $elem_newPassword; ?>', '<?php echo $elem_usersId; ?>')" />
                                                                               	</div> 
                                                                        	
                                                                            </div>
                                                                  		
                                                                        
                                                                        </div>
                                                
                                          							<!----------------------- Full Inout col-12    ------------------------------>
                                                                    </div> <!-- Form Outer -->
                                                                    
                                                    		</div> 
                                                            <!-- Audit Wrap -->
                                    
                                    
                                   							<div class="btn-footer-slider">
                                                            		
                                                                    <button type="submit" class="btn btn-success" id="saveButton" name="saveButton" onClick="return chkSaveFn();" >
                                                                    		<b class="fa fa-save"></b> Save
                                                                  	</button>
                                                                    <?php 
																	if($showCancelButton!='no') {
																	?>
                                                                        <a class="btn btn-danger" href="javascript:void(0)" id="cancelButton" onClick="javascript:top.location.href='home.php';" >
                                                                                <b class="fa fa-times"></b>	Cancel
                                                                        </a>
                                                                    <?php
																	}
																	?>
                                                                    
                                                       		</div>
                                                            
                                                                        
                               							</div>
                                
                       							</Div>
                            
                        				</div> 
                        
                 				</div>
                                <!-- NEcessary PUSH     -->	 
                                <Div class="push"></Div>
                                <!-- NEcessary PUSH     -->
                    
           				</div>
        		</div>
	
    </form>	
</div>