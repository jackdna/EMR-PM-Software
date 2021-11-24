<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width= device-width, initial-scale=1"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">		
<title>Surgery Center EMR</title>
<?php
$spec= "
</head>
<body class=\"background_slider2\" onLoad=\"document.loginFrm.userName.focus();\">";

include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

$objManageData = new manageData;

$logout = $_REQUEST['logout'];
$toDayDate = date('Y-m-d H:i:s');
if($logout=='true'){
	$loginLogoutIdSess = $_SESSION['login_logout_id'];
	unset($arrayRecord);
	$arrayRecord['logout_date_time'] = $toDayDate;	
	$objManageData->updateRecords($arrayRecord, 'login_logout_audit_tbl', 'login_logout_id', $loginLogoutIdSess);
	unset($arrayRecord);
	unset($_SESSION['loginUserName']); 
	unset($_SESSION['loginUserId']);
	//session_destroy();
}
if($_POST['userName']){
	$userName 				= imw_real_escape_string($_POST['userName']);
	$password 				= $_POST['password'];
	
	//Encrypted Password	
	$imw_passQry 			= imw_query("select PASSWORD ('$password')");
	$imw_passRow 			= imw_fetch_array($imw_passQry);
	$password 				= $imw_passRow['0'];
	//Encrypted Password
	
	// 
	//$maxRecentlyUsedPass = getData("maxRecentlyUsedPass", "surgerycenter", "surgeryCenterId", '1');
	$maxLoginAttempts 		= getData("maxLoginAttempts", "surgerycenter", "surgeryCenterId", '1');
	//
	
	$getLoginDetailsStr 	= "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes' ORDER BY usersId ASC limit 0,1";
	$getLoginDetailsQry 	= imw_query($getLoginDetailsStr);
	$getLoginRowCount 		= imw_num_rows($getLoginDetailsQry);
	$getLoginDetailsRow 	= imw_fetch_array($getLoginDetailsQry);	
	
	if($getLoginRowCount>0){
		$locked 								= $getLoginDetailsRow['locked'];
		$usersId 								= $getLoginDetailsRow['usersId'];
		$privileges 							= $getLoginDetailsRow['user_privileges'];	
		$admin_privileges 						= $getLoginDetailsRow['admin_privileges'];		
		$hippaReviewedStatus  					= $getLoginDetailsRow['hippaReviewedStatus'];		
		$loginUserType  						= $getLoginDetailsRow['user_type'];
		$loginUserSessionTimeout  				= $getLoginDetailsRow['session_timeout'];
		$loginUserSessionTimeout  				= ($loginUserSessionTimeout ) ?  $loginUserSessionTimeout : (30*60) ;
		$_SESSION['loginUserName'] 				= $userName;
		$_SESSION['loginUserId'] 				= $usersId;
		$_SESSION['userPrivileges'] 			= $privileges;
		$_SESSION['admin_privileges'] 			= $admin_privileges;		
		$_SESSION['loginUserType'] 				= $loginUserType;
		$_SESSION['loginUserSessionTimeout']	= $loginUserSessionTimeout;
		$_SESSION['session_last_update']		= time();
		$_SESSION['facility']					= $_POST['facility'];
		
		//start get facility name from facility_tbl table
		$getFacilityDetails = $objManageData->getRowRecord('facility_tbl', 'fac_id', $_SESSION["facility"]);
		if($getFacilityDetails) {
			$_SESSION['loginUserFacilityName']	= $getFacilityDetails->fac_name;
			$_SESSION['iasc_facility_id'] 		= $getFacilityDetails->fac_idoc_link_id;
		}
		//end get facility name from facility_tbl table

		$hippaReviewedYes 						= $getLoginDetailsRow['hippaReviewedYes'];		
		$hippaReviewedNo 						= $getLoginDetailsRow['hippaReviewedNo'];		
		if($hippaReviewedYes=="" || $hippaReviewedNo=="No"){
			$hippaReviewedStatus 				= "";
		}
		if($locked=='1'){
			?>
			<!--<script>alert("Account is locked see your local Administrator.")</script>-->
			<?php
		}
		else
		{
			// MAKE LOGIN DETAILS FOR AUDITING
				unset($arrayRecord);
				$arrayRecord['user_id'] 		= $_SESSION['loginUserId'];
				$arrayRecord['login_date_time'] = date('Y-m-d H:i:s');
				
				$auditLoginInsertId = $objManageData->addRecords($arrayRecord, 'login_logout_audit_tbl');
				$_SESSION['login_logout_id'] 	= $auditLoginInsertId;
			// MAKE LOGIN DETAILS FOR AUDITING
			
			?>
			<script>
				//window.close();
				var winHght = jQuery(window).height();
				var winWidt = jQuery(window).width();
				winWidt	=	winWidt * 0.97;
				//var name='SurgerycenterEMR'+Math.random();
				var homeWinObj = window.open('home.php?hippaReviewedStatus=<?php echo $hippaReviewedStatus; ?>&hippaReviewedYes=<?php echo $hippaReviewedYes ?>&winHght='+winHght+'&winWidt='+winWidt,"SurgerycenterEMR",'location=0,status=1,resizable=1,left=1,top=1,scrollbars=0,width='+winWidt+',height='+winHght);
				//homeWinObj.focus();
				homeWinObj.moveTo(0,0);
				if(self.name != "SurgerycenterEMR"){
					closeIT();
				}
				//window.open('home.php?hippaReviewedStatus=<?php echo $hippaReviewedStatus; ?>&hippaReviewedYes=<?php echo $hippaReviewedYes ?>&winHght='+winHght+'&winWidt='+winWidt,'SurgerycenterEMR','location=0,status=1,resizable=1,left=1,top=1,scrollbars=0,');
				
				 function closeIT()
				 {
					!(window.ActiveXObject) && 'ActiveXObject'
					function isIE11(){
						return !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
					}
					var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
					var ie11 = isIE11();
					if (ie7 || ie11){
					window.open('','_parent',''); 
					window.close(); 
					}else{
					this.focus();
					self.opener = this;
					self.close();
					}
				}

			</script>			
			<?php
			//close further processing here 
			die();
			
		}
	}
	else
	{
		$invalidUser = true;
		// DEFAULT MAX LOGIN ATTEMPTS 
			$maxLoginAttempts = getData("maxLoginAttempts", "surgerycenter", "surgeryCenterId", '1');
		// DEFAULT MAX LOGIN ATTEMPTS 
		
		// LOGIN ATTEMPTS
		$getUserDetailsStr 			= "SELECT loginAttempts,locked FROM users WHERE loginName = '$userName' AND deleteStatus <> 'Yes'";
		$getUserDetailsQry 			= imw_query($getUserDetailsStr);
		$getUserRows 				= imw_num_rows($getUserDetailsQry);
		if($getUserRows>0){
			$getUserDetailsRow 		= imw_fetch_array($getUserDetailsQry);
			$loginAttempts 			= $getUserDetailsRow['loginAttempts'];
			$loginAttempts 			= $loginAttempts + 1;			
			if($getUserDetailsRow['locked']=='1') {
				$disablePass 	= "True";
			?>
	            <script>alert("Account is locked see your local Administrator.")</script>
            <?php		
			}else if($loginAttempts<=$maxLoginAttempts){
				if($loginAttempts==$maxLoginAttempts){
					$disablePass 	= "True";
					$lock = 1;
		?>
						<script>alert("Account is locked see your local Administrator.")</script>
		<?php
				}else{
					?>
					<!--<script>
						alert("Login attempt with wrong password.")
					</script>-->
				<?php
						$loginUserMisMatch = true;
				}
				$updateStr 			= "UPDATE users SET loginAttempts = '$loginAttempts', locked = '$lock' WHERE loginName = '$userName'";
				$updateQry 			= imw_query($updateStr);				
			}
		}
		else{
			$loginUserMisMatch = true;
		}
		// LOGIN ATTEMPTS
	}
}

$surgerycenter_name_qry = "select `fac_name`, fac_id from facility_tbl where fac_head_quater=1";
$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
$surgerycenter_name 	= $surgerycenter_name_row["fac_name"];
$surgerycenter_name		=($surgerycenter_name)?$surgerycenter_name:'Surgery Center';
$surgerycenter_id		=$surgerycenter_name_row["fac_id"];

?>

<script>
$(document).ready(function() {
	window.moveTo(0, 0);
	window.resizeTo(screen.availWidth, screen.availHeight+10);
});
function chkLogin(){
	var flag = 0;
	var user = document.loginFrm.userName.value;
	var password = document.loginFrm.password.value;
	var facility = document.loginFrm.facility.value;
	if((user=='') || (password=='') || (facility=='')){
		alert("Please fill required information to login.")
		return false;
	}
	document.loginFrm.submit();
	return true;
}
function capLock(e){
	kc = e.keyCode?e.keyCode:e.which;
	sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
	if(document.getElementById('divCaps')) {
		if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk)) {
			document.getElementById('divCaps').style.visibility = 'visible';
		}else {
			document.getElementById('divCaps').style.visibility = 'hidden';
		}
	}
}
function showFac(obj){
	var val=obj.options[obj.selectedIndex].innerHTML;	
	document.getElementById('fac_name').innerHTML='iASC - '+val;
}
</script>
<?php
$loginPadd = '430px;';
if($_REQUEST['logout']=='true') { $loginPadd = '350px;'; }
$prdtVrsnDt = 'Ver R5.2  Jan 03, 2013';
if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }
?>
<div class="main_wrapper ">
    <div class="header_full_wrap navbar navbar-fixed-top " >        
        <div class="header_wrap">
        </div>
    
        <div class="header_wrap_2 drop_header login_changes text-center ">
            <div class="container-fluid">
                <div class="inner_surg_head">&nbsp;</div>
                
					 <a class="rob login_practice" id="fac_name">
            		<span>iASC - <?=$surgerycenter_name?></span>
                 </a>  
					 <div style=" width:100px; position:absolute; top:0px; right:30px;"><img src="images/imwemr_logo.png" /></div>
					
				  
            </div>
        </div>
	</div>
    <!-- Middle -->
  	<form name="loginFrm" method="post" action="index.php" onSubmit="return chkLogin();">
        <div class="middle_wrap margin_bottom_mid_adjustment ">
            <div class="container">
                <div class="inner_surg_middle">
                    <div class="col-lg-offset-3 col-md-offset-2 col-sm-offset-2 col-md-8 col-sm-8 col-lg-6 col-xs-12">
                        <div class="login_wrap">
                            <div class="head_login">
                                <h4 class="trap_head">
                                     <span class="glyphicon glyphicon-log-in"></span> Login here 
                                </h4>	                        
                            </div>
                            <div class="login_inner">
                                <div class="circular_styled">
                                    <!--<span class="fa fa-user-md"> </span>-->
                                    <img src="images/medical-icon.png" />
                                    
                                </div>	
                                <div class="wrap_style_login">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 textCenter">
									<?php
                                    if($loginUserMisMatch == true){?>
                                        <label style="color:#F00">Please enter correct username or password .</label>
                                        <?php
                                    } elseif($locked == 1) {?>
                                    	<label style="color:#F00">Account is locked see your local Administrator.</label>
                                    <?php 
									}else{?>
                                        <label id="divCaps" style="visibility:hidden;color:#F00">Caps Lock is on</label>
                                        <?php
                                    }?>
                                    </div>
                                    <div class="row">
                                        <div class="clearfix"></div>
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <label for="userName"> Username </label>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
                                            
                                            <input tabindex="1" type="text" name="userName" id="userName" class="form-control" onKeyPress="if(event.keyCode==13) { return chkLogin();}capLock(event);">
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <label for="password"> Password </label>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
                                            <input tabindex="2" <?php  if($disablePass == "True") echo "disabled"; ?>  type="password" name="password" id="password" class="form-control" onKeyPress="if(event.keyCode==13) { return chkLogin();}capLock(event);">
                                        </div>
                                        <?php
												
											$conditionArr['fac_del_status']=0;
											//$facList = $objManageData->getArrayRecords('facility_tbl', '', '','fac_name','ASC');	
											$facList = $objManageData->getMultiChkArrayRecords('facility_tbl', $conditionArr, 'fac_name','ASC', $extraCondition)	;
											if($facList)
											{
											?>
                                         <div class="clearfix"></div>
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <label for="facility"> ASC </label>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
                                        	<select tabindex="3" name="facility" class="selectpicker form-control" onChange="showFac(this);"  required title="Select ASC" data-header="Select ASC" data-title="Select ASC">
                                            
                                                  
                                                <?php
                                                foreach($facList as $facData) {
                                                ?>
                                                    <option value="<?php echo $facData->fac_id;?>" <?php if($facData->fac_head_quater==1)echo' selected';?>><?php echo $facData->fac_name;?></option>
                                                <?php
                                                }
                                                ?>
                                                </select>
                                        </div>
                                        <?php }?>
                                        <div class="clearfix"></div>
                                        <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8 col-md-offset-4 col-sm-offset-4 col-lg-offset-4">
                                                <a tabindex="4" href="javascript:void(0);" class="Login_btn" onClick="return chkLogin();"> Login   </a>
                                        </div>
                                    </div>
                                </div>                     	                            
                            </div>
                        </div>
                    </div>
                    <!-- NEcessary PUSH     -->	 
                    <Div class="push"></Div>
                    <!-- NEcessary PUSH     -->
                </div>
            </div>   
        </div>
    </form>
    <!-- Middle -->
    <div class="footer_wrap navbar navbar-default navbar-fixed-bottom">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
           	<div class=" col-lg-3 col-md-3 col-sm-3 col-xs-12 footer_logo">
            	<span><img src="images/logo_iascemr.png" /></span>
          	</div>   
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 footer_content">
            	<span class="footer_span">Copyrights © 2007 - <?php echo substr($prdtVrsnDt,-4,4);?>. imwemr ® All rights reserved. </span>
                <p class="footer_span"> <a style="cursor:none;" href="javascript:void(0)"> Our Privacy Statement </a> <span class="to_hide_319">|</span>
                	<a style="cursor:none;" href="javascript:void(0)"> Copyright Notice </a>|<a style="cursor:none;" href="javascript:void(0)"> <?php echo $prdtVrsnDt;?> </a> 
              	</p>
			</div>		
            
        </div>
	</div>    
</div>
</body>
</html>