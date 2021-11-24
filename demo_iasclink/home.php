<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
set_time_limit(500);
include_once("common/conDb.php");
include_once("logout.php");
unset($privilegesArr);
unset($admin_privilegesArr);
$privileges = $_SESSION['iolink_userPrivileges'];
	$privilegesArr = explode(', ', $privileges);
$admin_privileges = $_SESSION['iolink_admin_privileges'];
	if($admin_privileges){
		$admin_privilegesArr = explode(', ', $admin_privileges);
	}
$cancelConfirm = isset($_REQUEST['cancelConfirm']) ? $_REQUEST['cancelConfirm'] : ''; // ON CANCEL OF PATIENT CONFIRM SCREEN 
$saveConfirm = isset($_REQUEST['saveConfirm']) ? $_REQUEST['saveConfirm'] : '';  	 // ON SAVE OF PATIENT CONFIRM SCREEN
include_once("admin/classObjectFunction.php");
//include_once("unfinalize_message.php");  //RUN THIS FILE TO SEND WARNING MESSAGE TO (SURGEON) OR (USER WITH SUPER PRIVILLIGES) REGARDING FINALIZE PURPOSE.
//include_once("finalize_message.php");  	 //RUN THIS FILE TO SEND MESSAGE TO (SURGEON) OR (USER WITH SUPER PRIVILLIGES) THAT CHARTNOTES ARE FINALIZED.
$sessLogUserId =  $_SESSION['iolink_loginUserId'];
?>
<!DOCTYPE HTML>
<html>
<head>
	<link rel="stylesheet" href="css/theme.css" type="text/css" />
	<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
	<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
	<link rel="stylesheet" href="css/form.css" type="text/css" />
    <link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
	<script type="text/javascript" src="js/wufoo.js"></script>
	<script type="text/javascript" src="js/mootools.v1.11.js"></script>
	<script type="text/javascript" src="js/moocheck.js"></script>
	<script type="text/javascript" src="js/jsFunction.js"></script>
	<script type="text/javascript" src="js/cur_timedate.js"></script>
	<script type="text/javascript" src="js/simpletreemenu.js"></script>
<title>iOLink EMR</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; } 
</style>
<script>
	function showAppointments(){ 
		
		
		document.getElementById('okCancelTr').style.display = 'none';
		
		document.getElementById('admin_audit_report_id').style.display = 'inline-block';
		
		if(document.getElementById('search_display_id')) {
			document.getElementById('search_display_id').style.display = 'inline-block';
		}
		var objFrame = document.getElementById("iframeHome");
		if(objFrame) { objFrame.height='680'; }
		
		<?php
		if($_REQUEST['hippaReviewedStatus']!='Showed'){
			$today = date('Y-m-d g:i:s A');
			$sqlUpdateUser="UPDATE users SET hippaReviewedYes = 'Yes', hippaReviewedStatus = 'Showed',hippaReviewedDateTime = '$today' where usersId = $sessLogUserId";
			$sqlQryUpdateUser = imw_query($sqlUpdateUser);			
			if( $sqlQryUpdateUser ) {
				echo 'if( window.history.replaceState ) {
						window.history.replaceState("","","home.php?hippaReviewedStatus=Showed&hippaReviewedYes=Yes");
				}';
			}
		}
		?>
				
		if(objFrame) {
			objFrame.src = 'iOLink.php';
		}
		//top.frames[0].location.href = 'iOLink.php';
		
		/*
		if(document.getElementById('messg_unread_id')) {
			document.getElementById('messg_unread_id').style.display = 'inline-block';
		}
		var objFrame = document.getElementById("iframeHome");
		if(objFrame) { objFrame.height='590'; }
		<?php		
		if((in_array('Admin', $privilegesArr) && in_array('EMR', $admin_privilegesArr)) || in_array('Super User', $privilegesArr) || in_array('Nursing Record', $privilegesArr) || in_array('Anesthesia', $privilegesArr) || in_array('Surgeon', $privilegesArr) || in_array('Staff', $privilegesArr) || in_array('Coordinator', $privilegesArr)){
			?>
			top.frames[0].location.href = 'iOLink.php';
			<?php
		}
		
		?>
		*/
	}
	function changePasswardFn(id){
		document.getElementById(id).style.display = 'none';
		top.frames[0].location.href = 'admin/index.php';		
	}
	
	function changePasswardFnNew(id){
		document.getElementById(id).style.display = 'none';
		document.getElementById('okCancelTr').style.display = 'none';
		document.getElementById('admin_audit_report_id').style.display = 'none';
		if(document.getElementById('search_display_id')) {
			document.getElementById('search_display_id').style.display = 'none';
		}
		top.frames[0].location.href = 'change_password.php';		
	}
	
	var LoginUserId='';
	function openTab(id,LoginUserId){		
		
		var objFrame = document.getElementById("iframeHome");
		document.getElementById('okCancelTr').style.display = 'none';
		if(id=="Admin"){
			objFrame.src = "admin/index.php";	
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_slidNew";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background='#3232F0';
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
			}	
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
		}else if(id=="Audit"){
			objFrame.src = "audit/audit.php";
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_slidNew";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background='#3232F0';
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
			}	
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";			
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
		
		}else if(id=="Reports"){
			objFrame.src = "report.php";
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_slidNew";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background='#3232F0';
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
		
			}
		}else if(id=="MessageDisplay"){
			objFrame.src = "message_display.php?msg_user_id="+LoginUserId;
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";			
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			//objFrame.location.href = "message_display.php";  
		
		}else if(id=="Anesthesia"){
			var sessLogUserId = '<?php echo $sessLogUserId;?>';
			objFrame.src = "admin/index.php?anesthesiologistList="+sessLogUserId;			
			//top.frames[0].frames[0].frames[0].location='anesthesia_profile_save.php?anesthesiologistList=135';
			
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("anesthesiaTab")) {
				document.getElementById("anesthesiaTab").className="link_slidNew";
				document.getElementById("TDanesthesiaTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDanesthesiaMiddleTab").style.background='#3232F0';
				document.getElementById("TDanesthesiaBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
			}	
				
		}else if(id=="iOLink"){
			objFrame.src = "iOLink.php";			
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			
			}
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("anesthesiaTab")) {
				document.getElementById("anesthesiaTab").className="link_slidNew";
				document.getElementById("TDanesthesiaTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDanesthesiaMiddleTab").style.background='#3232F0';
				document.getElementById("TDanesthesiaBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
			}
			if(document.getElementById("iOLinkTab")) {
				document.getElementById("iOLinkTab").className="link_slidNew";
				document.getElementById("TDiOLinkTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDiOLinkMiddleTab").style.background='#3232F0';
				document.getElementById("TDiOLinkBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" width="3" height="30">';
			}	
		
		}else{
			//top.iframeHome.iframe_home_inner_front.location = "home_inner_front.php?txt_patient_search_id="+redirect_txt_patient_search_value+"&display_cal=none&display_patient_sch=none&display_patient_search=display";
			//objFrame.location.href = "home_front.php";
			objFrame.src = "home_front.php";
		}
	}
	//FUNCTIONS BY SURINDER
	function openSearchTab(){		
		var txt_patient_search_value = eval(document.getElementById("txt_patient_search_id"));
		if(txt_patient_search_value.value == "") {
			alert("Enter search box for patient ");
			txt_patient_search_value.focus();
			return false;
		}else{
			//CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}	
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
			}
			//END CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
			
			top.frames[0].location = "home_inner_front.php?txt_patient_search_id="+txt_patient_search_value.value+"&display_cal=none&display_patient_sch=none&display_patient_search=display";			
		}	
		
	}
	
	
	function LTrim( value ){
		var re = /\s*((\S+\s*)*)/;
		return value.replace(re, "$1");
	}
	// Removes ending whitespaces
	function RTrim(value){
		var re = /((\s*\S+)*)\s*/;
		return value.replace(re, "$1");
	}
	// Removes leading and ending whitespaces
	function trim(value){
		return LTrim(RTrim(value));
	}	
	//FUNCTIONS END BY SURINDER

	function KeyCheck(evt) {
		evt = (evt) ? evt : ((event) ? event : null);
		var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
		var keynumber = evt.keyCode;
		if(keynumber==13){
			search_link_id.click();  //IF PRESS ENTER THEN SEARCH LINK AUTOMATICALLY BE CLICKED
		}
	}
	window.focus();
	

//START FUNCTION TO OPEN 'NEW CHARTNOTE WINDOW' OR SET FOCUS ON 'EXISTING CHATNOTE WINDOW'
	
	//variable to store uniqueId for new popups
	var uniqueIdArray = new Array(50);
	//variable to store window objects as the history of popup windows
	var windowObject =new Array(50);
	var tools = 'resizable, top=10, width=1020, height=650, location=yes,status=yes';
	function setwin(winName,stbId,url) {
		//alert(winName+stbId+uniqueIdArray[url]);
		if(windowObject[winName+stbId+uniqueIdArray[url]]!=null && !windowObject[winName+stbId+uniqueIdArray[url]].closed) {
			//alert('window is already opened for this link and will set the focus to that window');
			windowObject[winName+stbId+uniqueIdArray[url]].focus();
		}
		else {
			//alert('New window will be opened for this link');
			//windowObject[winName+stbId+uniqueIdArray[url]] = window.open(url, winName+stbId+uniqueIdArray[url], tools);
			
			windowObject[winName+stbId+uniqueIdArray[url]] = top.iframeHome.window.open(url, winName+stbId+uniqueIdArray[url], tools);
			windowObject[winName+stbId+uniqueIdArray[url]].focus();
		
			//alert(top.opener.document.forms[0].chkOpenedChildWind);
			/*
			if(top.opener.document.forms[0].chkOpenedChildWind) {
				if(top.opener.document.forms[0].chkOpenedChildWind.value) {
					top.opener.document.forms[0].chkOpenedChildWind.value=top.opener.document.forms[0].chkOpenedChildWind.value+','+winName+stbId+uniqueIdArray[url];
				}else {
					top.opener.document.forms[0].chkOpenedChildWind.value=winName+stbId+uniqueIdArray[url];
				}	
			}	
			*/
		
		}
		
	}
	
//END FUNCTION TO OPEN 'NEW CHARTNOTE WINDOW' OR SET FOCUS ON 'EXISTING CHATNOTE WINDOW'	

</script>
</head>
<body onload="startTime()" style="overflow-y:hidden;">
<?php

$objManageData = new manageData;
//UPDATE USER LOGIN ATTEMPTS
	$arrayAttempts['loginAttempts'] = 0;
	$objManageData->updateRecords($arrayAttempts, "users", "usersId", $userLoginId);
//UPDATE USER LOGIN ATTEMPTS

$passwardExpiers = false;
$daysLeft = false;
//GET USERS EXPIRES ALERT//
	$maxExpireDays = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
	$facQuery=imw_query("select fac_name from facility_tbl where fac_id='".$_SESSION["iolink_facility_id"]."'")or die(imw_error());
	$facdata=imw_fetch_object($facQuery);
	$maxDaysToExpire = $maxExpireDays->maxPassExpiresDays;	
	$surgerycenter_name = $facdata->fac_name;
	$userDetails = $objManageData->getRowRecord('users', 'usersId', $userLoginId);
	$passChangedLatDate = $userDetails->passCreatedOn;
	if($today!=$passChangedLatDate){
		$differanceBetween = $objManageData->getDateDifferance($today, $passChangedLatDate);
		$expireDaysLeft = $maxDaysToExpire-$differanceBetween;
		if($differanceBetween>=$maxDaysToExpire){
		//echo $differanceBetween.' => '.$maxDaysToExpire;
			$arrayRecord['locked'] = 1;
			$objManageData->updateRecords($arrayRecord, "users", "usersId", $userLoginId);
			$passwardExpiers = "true";
		}else if($expireDaysLeft <= 7){
			$daysLeft = 'true';
		}
	}
//GET USERS EXPIRES ALERT//

// GETTING LOGIN USER NAME
	$str = "SELECT CONCAT_WS(' ', fname, mname, lname) 
			as userName, user_type FROM users
			WHERE usersId = '$userLoginId'";
	$qry = imw_query($str);
	$fetchRows = imw_fetch_array($qry);
	$userName = $fetchRows['userName'];
	$user_type = $fetchRows['user_type'];
// GETTING LOGIN USER NAME



//GET COUNT OF ALL MESSAGES OF LOGGED IN USER
$msgTblCountQry = "select * from msg_tbl where msg_user_id = '".$userLoginId."'";
$msgTblCountRes = imw_query($msgTblCountQry) or die(imw_error());
$msgTblCountNumRow = imw_num_rows($msgTblCountRes);
//GET COUNT OF ALL MESSAGES OF LOGGED IN USER

//GET COUNT OF UNREAD MESSAGES FROM msg_tbl
$getMsgQry = "select * from msg_tbl where read_status='' AND msg_user_id = '".$userLoginId."'";
$getMsgRes = imw_query($getMsgQry) or die(imw_error());
$getMsgNumRow = imw_num_rows($getMsgRes);
if($getMsgNumRow>0) {
	$getCountMsg = '('.$getMsgNumRow.')';
}
//END GET COUNT OF UNREAD MESSAGES FROM msg_tbl


$prdtVrsnDt = 'Ver R5.2  Jan 03, 2013';
if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }

?>

<table class="alignCenter" style="height:100%; width:1250px; background-color:#ECF1EA; border:none; padding:0px;">
	<tr >
		<td class="valignTop" style="width:5px;background-color:#FFFFFF; padding:0px;"><img alt="" src="images/top_left.jpg" style="width:5px; height:43px; border:none;"></td>
		<td class="top_bg valignMiddle" style="width:985px;padding:0px; background-color:#FFFFFF;">
			<table class="table_collapse alignCenter" style="border:none;">
				<tr>
					<td style="font-size:14px; width:14%; padding-left:5px;background-color:#FFFFFF;" class="text_10b alignLeft valignMiddle nowrap" ><?php echo $userName; ?></td>
					<td class="valignTop" style="width:26%;background-color:#FFFFFF;">
						<!-- <div><img src="images/tpixel.gif" height="10"></div> -->
						<table id="admin_audit_report_id" style="display:none; padding:0px;">							
							<tr>
								<td class="alignCenter nowrap">
									<span class="text_10b" style="font-weight:bold; color:#CB6B43; white-space:nowrap;">
										<?php echo $surgerycenter_name;?>
									</span>
								</td>
							</tr>
					  </table>
					</td>
					<td class="alignLeft valignMiddle nowrap" style="width:14%;font-size:12px;background-color:#FFFFFF;"><div id="dt_tm" style="font-weight:normal;"></div></td>
					<td class="alignLeft valignMiddle nowrap" style="width:6%;background-color:#FFFFFF;">
						<a href="#" onClick="location.href='switchUser.php';" class="link_top" title="Switch User" style="font-weight:bold; color:#cc0000;font-size:15px; padding-left:2px; ">
							Switch User
						</a>
                        
                        <a href="index.php?logout=true" class="link_top" title="Logout" style="font-weight:bold; color:#cc0000;font-size:15px; padding-left:10px; ">
							Log Out
						</a>
					</td>
				</tr>
		  </table>
	  </td>
		<td class="valignTop" style="width:5px;background-color:#FFFFFF; padding:0px;"><img alt="" src="images/top_right.jpg" style="width:28px;padding:0px; height:43px; border:none;"></td>
	</tr>
	<?php 
	if($passwardExpiers=="true"){ 
		?>
		<tr id="trExpirePassword">
			<td colspan="3" class="text_10b alignCenter nostyle" style="color:#FF0000; font-size:12px;">
				. Password Expired. User must change password. <a class="black" href="javascript:changePasswardFnNew('trExpirePassword');">Click Here to Change.</a>
			</td>
		</tr>
		<?php
	}else if($daysLeft=='true'){
		?>
		<tr id="trExpired">
			<td colspan="3" class="text_10b alignCenter nostyle" style="color:#FF0000; font-size:12px;">
				. Password will Expire <?php  if($expireDaysLeft==0)echo 'today'; else echo ' in '.$expireDaysLeft.'day(s)'; ?>. <a class="black" href="javascript:changePasswardFnNew('trExpired');">Click Here to Change.</a>
			</td>
		</tr>
		<?php
	}	
	?>
	<tr>
		<td colspan="3" class="valignTop" style="height:675px;" >
			<iframe id="iframeHome" name="iframeHome" src="home_front.php" style="width:100%; height:675px;" frameborder="0" scrolling="no"></iframe>
		 </td>
	</tr>
	<tr id="okCancelTr"> 
		<td colspan="3" class="alignCenter">
			<table class="table_collapse alignCenter" style="border:none;">
				<tr>
					<td class="alignRight valignTop" style="width:80px; padding-right:5px;">
						<a href="javascript:showAppointments();" onClick="MM_swapImage('okBtn','','images/ok.jpg',1);" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('okBtn','','images/ok_hover.jpg',1)"><img src="images/ok.jpg" style="border:none;" id="okBtn" alt="ok" onClick=""/></a>
					</td>
					
					<td style="width:80px;" class="valignTop alignLeft"><a href="javascript:window.close();" onClick="MM_swapImage('CancelBtn','','images/cancel_onclick1.jpg',1)" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('CancelBtn','','images/cancel_hover1.jpg',1)"><img src="images/cancel.jpg" style="border:none; width:70px; height:25px;" id="CancelBtn" alt="Cancel" onClick="" /></a></td>
					
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td  colspan="3" class="top_bg valignTop">
			<table class="top_bg table_collapse" style="border:none;">
				<tr>
					<td class="alignLeft">		
						<table class="table_collapse" style="border:none;">
                        	<tr>
                            	<td style="width:90px;"><img alt="" src="images/iolink.jpg" style="border:none; cursor:pointer; " onClick="showAppointments();" /></td>
                                <td class="text_smallb" ><!--V 3.17--></td>
                            </tr>
                         </table>       
					</td>
					<td>&nbsp;&nbsp;</td>
					<td class="text1 alignLeft">		
						Copyrights &copy;  - <?php echo substr($prdtVrsnDt,-4,4);?>. imwemr &reg; All rights reserved. <a class="version" onClick="showReleaseNote();"><?php echo $prdtVrsnDt;?></a>
					</td>
					<td class="alignRight">		
						<img alt="" src="images/logo_iascemr.png" style="width:150px; height:30px; border:none;">
					</td>
					<td>&nbsp;&nbsp;</td>
				</tr>
			</table>
		 </td>
	</tr> 
</table>

<script>
var tempvar = '<?php echo $_REQUEST["pageclose"];?>';
if(tempvar=='y') {
	document.getElementById('okCancelTr').style.display = 'none';
	document.getElementById('admin_audit_report_id').style.display = 'inline-block';
	if(document.getElementById('search_display_id')) {
		document.getElementById('search_display_id').style.display = 'inline-block';
	}
	<?php
	//if((in_array('Admin', $privilegesArr) && in_array('EMR', $admin_privilegesArr)) || in_array('Super User', $privilegesArr) || in_array('Anesthesia', $privilegesArr) || in_array('Nursing Record', $privilegesArr) || in_array('Surgeon', $privilegesArr) || in_array('Staff', $privilegesArr)){
		?>
		var objFrameTmp = document.getElementById("iframeHome");
		if(objFrameTmp) {
			objFrameTmp.src = 'iOLink.php';
		}
		//top.frames[0].location.href = 'home_inner_front.php';
		<?php
	//}
	?>
}
</script>
<?php
if($cancelConfirm || $saveConfirm){
	?>
	<script>showAppointments();</script>
	<?php
}
?>
<script>
	<?php
		if($_REQUEST['hippaReviewedStatus']=='Showed'){
		?>
			showAppointments();
	<?php
		}
	?>
	
	function showReleaseNote()
	{
		window.open('release_note.php','Release Notes ','width=900, height=492, top=100,left=200, resizable=no, scrollbars=no');	
	}
</script>
</body>
</html>