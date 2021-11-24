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
<meta name="viewport" content="width= device-width, initial-scale=1"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />

<title>Surgery Center EMR</title>
<!--RESPONSIVE CSS AND JAVASCRIPT-->
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="css/ion.calendar.css" />
<link rel="stylesheet" type="text/css" href="css/datepicker.css" />

<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-select.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>    
<script type="text/javascript" src="js/ion.calendar.js"></script>
<!--<script type="text/javascript" src="js/overflow.js"></script>-->
<script type="text/javascript" src="js/front_page.js"></script>
<script type="text/javascript" src="js/list-item.js"></script>
<script type="text/javascript" src="js/alert_file.js"></script>
<!--RESPONSIVE CSS AND JAVASCRIPT-->

<script>
$(document).ready(function() {
	window.moveTo(0, 0);
	window.resizeTo(screen.availWidth, screen.availHeight+10);
});
function chkLogin(){
	var flag = 0;
	var password = document.loginFrm.password.value;
	var iolink_facility_id= document.loginFrm.iolink_facility_id.value;	
	if(password=='' || iolink_facility_id==''){
		alert("Please fill required information to login.")
		return false;
	}
	document.loginFrm.submit();
	return true;
}
function capLock(e){
	kc = e.keyCode?e.keyCode:e.which;
	sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
	if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
		if(document.getElementById('divCaps')){
			document.getElementById('divCaps').style.visibility = 'visible';
		}
	else
		if(document.getElementById('divCaps')){
			document.getElementById('divCaps').style.visibility = 'hidden';
		}
}
function showFac(obj){
	var val=obj.options[obj.selectedIndex].innerHTML;
	document.getElementById('fac_name').innerHTML= 'iASCLink - ' + val;
}
</script>

<?php
$spec= "
</head>
<body style=\"margin:0px;\" onLoad=\"document.loginFrm.password.focus();\">";

//include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

$objManageData = new manageData;

$logout = $_REQUEST['logout'];
$toDayDate = date('Y-m-d H:i:s');
if($logout=='true'){
	$loginLogoutIdSess = $_SESSION['iolink_login_logout_id'];
	unset($arrayRecord);
	$arrayRecord['logout_date_time'] = $toDayDate;	
	$objManageData->updateRecords($arrayRecord, 'login_logout_audit_tbl', 'login_logout_id', $loginLogoutIdSess);
	unset($arrayRecord);
	session_destroy();
}
if($_POST['password']){
	//$userName = $_POST['userName'];
	$password = $_POST['password'];
	
	//Encrypted Password	
	$imw_passQry = imw_query("select PASSWORD('$password')");
	$imw_passRow = imw_fetch_array($imw_passQry);
	$password = $imw_passRow['0'];
	//Encrypted Password
	
	// 
	//$maxRecentlyUsedPass = getData("maxRecentlyUsedPass", "surgerycenter", "surgeryCenterId", '1');
	$maxLoginAttempts = getData("maxLoginAttempts", "surgerycenter", "surgeryCenterId", '1');
	//
	
	//$getLoginDetailsStr = "SELECT * FROM users WHERE loginName = '$userName' AND user_password = '$password' AND deleteStatus <> 'Yes'";
	$getLoginDetailsStr = "SELECT * FROM users WHERE user_password = '$password' AND deleteStatus <> 'Yes' limit 0,1";
	$getLoginDetailsQry = imw_query($getLoginDetailsStr);
	$getLoginRowCount = imw_num_rows($getLoginDetailsQry);
	$getLoginDetailsRow = imw_fetch_array($getLoginDetailsQry);	
	if($getLoginRowCount>0){
		$locked = $getLoginDetailsRow['locked'];
		$usersId = $getLoginDetailsRow['usersId'];
		$privileges = $getLoginDetailsRow['user_privileges'];	
		$admin_privileges = $getLoginDetailsRow['admin_privileges'];
		$hippaReviewedStatus  = $getLoginDetailsRow['hippaReviewedStatus'];	
		$loginUserType  = $getLoginDetailsRow['user_type'];	
		$_SESSION['iolink_loginUserName'] = $userName;
		$_SESSION['iolink_loginUserId'] = $usersId;
		$_SESSION['iolink_userPrivileges'] = $privileges;
		$_SESSION['iolink_admin_privileges'] = $admin_privileges;
		$_SESSION['iolink_loginUserType'] = $loginUserType;	
		$_SESSION['iolink_facility_id'] = $_POST['iolink_facility_id'];
		
		//start get facility name from facility_tbl table
		$getFacilityDetails = $objManageData->getRowRecord('facility_tbl', 'fac_id', $_SESSION["iolink_facility_id"]);
		if($getFacilityDetails) {
			$_SESSION['iolink_loginUserFacilityName']	= $getFacilityDetails->fac_name;
			$_SESSION['iolink_iasc_facility_id'] = $getFacilityDetails->fac_idoc_link_id;
		}
		//end get facility name from facility_tbl table
		
		$hippaReviewedYes = $getLoginDetailsRow['hippaReviewedYes'];		
		$hippaReviewedNo = $getLoginDetailsRow['hippaReviewedNo'];		
		if($hippaReviewedYes=="" || $hippaReviewedNo=="No"){
			$hippaReviewedStatus ="";
		}
		
		if($locked=='1'){
			?>
			<script>alert("Account is locked see your local Administrator.")</script>
			<?php
		}
		else
		{
			// MAKE LOGIN DETAILS FOR AUDITING
				unset($arrayRecord);
				$arrayRecord['user_id'] = $_SESSION['iolink_loginUserId'];
				$arrayRecord['login_date_time'] = date('Y-m-d H:i:s');
				
				$auditLoginInsertId = $objManageData->addRecords($arrayRecord, 'login_logout_audit_tbl');
				$_SESSION['iolink_login_logout_id'] = $auditLoginInsertId;
			// MAKE LOGIN DETAILS FOR AUDITING
			
			$chkOpenedChildWind = $_POST['chkOpenedChildWind'];
			?>
			<script>
				//window.close();
				homeWinObj=window.open('home.php?hippaReviewedStatus=<?php echo $hippaReviewedStatus; ?>&hippaReviewedYes=<?php echo $hippaReviewedYes ?>','iOLinkEMR','location=0,status=1,resizable=1,left=1,top=1,scrollbars=0,width=1280,height=760');
				homeWinObj.resizeTo(1260, 830);
				homeWinObj.moveTo(0,0);
				
			</script>			
			<?php
			
			
		}
	}else{
		$loginUserMisMatch = true;		
	}
}

$prdtVrsnDt = 'Ver R5.2  Jan 03, 2013';
if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }

/*$surgerycenter_name_qry = "select `name` from surgerycenter where surgeryCenterId=1";
$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
$surgerycenter_name 	= $surgerycenter_name_row["name"];*/

$whereQry = "fac_head_quater=1";
if($_SESSION['iolink_facility_id'])
{
	$whereQry = " fac_id = '".$_SESSION['iolink_facility_id']."' ";
}
$surgerycenter_name_qry = "select `fac_name`, fac_id from facility_tbl where ".$whereQry."";
$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
$surgerycenter_name 	= $surgerycenter_name_row["fac_name"];
$surgerycenter_name		=($surgerycenter_name)?$surgerycenter_name:'Surgery Center';
$surgerycenter_id		=$surgerycenter_name_row["fac_id"];


?>
<div class="main_wrapper ">
    <div class="header_full_wrap navbar navbar-fixed-top " >        
        <div class="header_wrap">
        </div>
    
        <div class="header_wrap_2 drop_header login_changes text-center ">
            <div class="container-fluid">
                <div class="inner_surg_head">&nbsp;
                   <!-- <a href="javascript:void();">
                        <img src="images/logo_surg.svg"   />             
                    </a>-->
                </div>
                <a class="rob login_practice" id="fac_name">
            		<span>
             			iASCLink - <?php echo $surgerycenter_name;?>   
                    </span>
                 </a>        
            </div>
        </div>
	</div>
    <!-- Middle -->
    <form name="loginFrm" method="post" action="switchUser.php" onSubmit="return chkLogin();">
        <div class="middle_wrap margin_bottom_mid_adjustment ">
            <div class="container">
                <div class="inner_surg_middle">
                    <div class="col-lg-offset-3 col-md-offset-2 col-sm-offset-2 col-md-8 col-sm-8 col-lg-6 col-xs-12">
                        <div class="login_wrap">
                            <div class="head_login">
                                <h4 class="trap_head">
                                     <span class="glyphicon glyphicon-log-in"></span> Switch User 
                                </h4>	                        
                            </div>
                            <div class="login_inner">
                                <div class="circular_styled">
                                    <!--<span class="fa fa-user-md"> </span>-->
                                    <img src="images/medical_icon_iolink.png" />	
                                </div>	
                                <div class="wrap_style_login">
                                   
									<?php
                                    if($loginUserMisMatch == true){?>
                                        <label style=" margin: 0px;float:left; width:100%; text-align:center;color: #F00;">Please enter correct password.</label>
                                        <?php
									  }else{?>
                                        <label id="divCaps" for="" style="visibility:hidden;  margin: 0px;float:left; width:100%; text-align:center;">Caps Lock is on</label>
                                        <?php
                                    }?>
                                   
                                    <div class="row">
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
                                        	<select tabindex="3" name="iolink_facility_id" class="selectpicker form-control" onChange="showFac(this);"  required title="Select ASC" data-header="Select ASC" data-title="Select ASC">
                                            
                                                  
                                                <?php
                                                foreach($facList as $facData) {
                                                ?>
                                                    <option value="<?php echo $facData->fac_id;?>" 
													<?php 
														if($facData->fac_id == $surgerycenter_id) {echo 'selected';}
													?>
                                                    >
													<?php echo $facData->fac_name;?></option>
                                                <?php
                                                }
                                                ?>
                                                </select>
                                        </div>
                                        <?php }?>
                                        <div class="clearfix"></div>
                                        <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8 col-md-offset-4 col-sm-offset-4 col-lg-offset-4">
                                                <a href="javascript:void(0);" class="Login_btn" onClick="return chkLogin();"> Login   </a>
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
            	<span class="footer_span">MIT License - <?php echo substr($prdtVrsnDt,-4,4);?>. imwemr ® All rights reserved. </span>
                <p class="footer_span"> <a style="cursor:none;" href="javascript:void(0)"> Our Privacy Statement </a> <span class="to_hide_319">|</span>
                	<a style="cursor:none;" href="javascript:void(0)"> Copyright Notice </a>|<a style="cursor:none;" href="javascript:void(0)"> <?php echo $prdtVrsnDt;?> </a> 
              	</p>
			</div>		
            
        </div>
	</div>    
</div>
</body>
</html>