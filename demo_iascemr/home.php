<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(900);
include_once("common/conDb.php");
$winHght = $_REQUEST['winHght'];
$winWidt = $_REQUEST['winWidt'];
$_SESSION['winHght'] = $winHght;
$_SESSION['winWidt'] = $winWidt;
include_once("logout.php");
//include_once("common/user_agent.php");
unset($privilegesArr);
unset($admin_privilegesArr);
$privileges = $_SESSION['userPrivileges'];
$privilegesArr = explode(', ', $privileges);
$admin_privileges = $_SESSION['admin_privileges'];
	if($admin_privileges){
		$admin_privilegesArr = explode(', ', $admin_privileges);
	}
$cancelConfirm = $_REQUEST['cancelConfirm']; // ON CANCEL OF PATIENT CONFIRM SCREEN 
$saveConfirm = $_REQUEST['saveConfirm'];  	 // ON SAVE OF PATIENT CONFIRM SCREEN
include_once("admin/classObjectFunction.php");
//include_once("unfinalize_message.php");  //RUN THIS FILE TO SEND WARNING MESSAGE TO (SURGEON) OR (USER WITH SUPER PRIVILLIGES) REGARDING FINALIZE PURPOSE.
include_once("finalize_message.php");  	 //RUN THIS FILE TO SEND MESSAGE TO (SURGEON) OR (USER WITH SUPER PRIVILLIGES) THAT CHARTNOTES ARE FINALIZED.
$sessLogUserId =  $_SESSION['loginUserId'];

$hipaRvwStatus = "";
$hipaRvwQry = "SELECT hippaReviewedStatus,user_type,coordinator_type FROM users WHERE usersId = '".$sessLogUserId."'";
$hipaRvwRes = imw_query($hipaRvwQry) or die(imw_error());
if(imw_num_rows($hipaRvwRes)>0) {
	$hipaRvwRow = imw_fetch_array($hipaRvwRes);	
	$hipaRvwStatus = $hipaRvwRow["hippaReviewedStatus"];
	$loggedInUserType	=	$hipaRvwRow["user_type"];	
	$loggedInCoordinatorType = $hipaRvwRow["coordinator_type"];	
}

$authVisionCompTab	=	false;
if( $loggedInUserType == 'Surgeon' || ($loggedInUserType == 'Coordinator' && $loggedInCoordinatorType == 'Master') )
{
	$authVisionCompTab	=	true;	
}

//GET SURGERY CENTER NAME
	$surgerycenter_name_qry = "select 'name' from surgerycenter where surgeryCenterId=1";
	$surgerycenter_name_res = imw_query($surgerycenter_name_qry) or die(imw_error());
	$surgerycenter_name_row = imw_fetch_array($surgerycenter_name_res);
	$surgerycenter_name = $surgerycenter_name_row["name"];
//END GET SURGERY CENTER NAME


?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">-->
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width= device-width, initial-scale=1"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">		
<title>Surgery Center EMR</title>

    <!--<link rel="stylesheet" href="css/form.css" type="text/css" />
	<link rel="stylesheet" href="css/theme.css" type="text/css" />
	<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
	<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
	<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
	<script type="text/javascript" src="js/wufoo.js"></script>
	<script type="text/javascript" src="js/mootools.v1.11.js"></script>-->
	<script type="text/javascript" src="js/moocheck.js"></script>
	<script type="text/javascript" src="js/jsFunction.js"></script>
	<script type="text/javascript" src="js/cur_timedate.js"></script>
	<script type="text/javascript" src="js/simpletreemenu.js"></script>
    
	<link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap-select.css" />
	<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/bootstrap-select.js"></script>
  <!--  <script type="text/javascript" src="js/overflow.js"></script> -->
    <script>
		window.focus();
		$.xhrPool = [];
		var homeWinName	;
		var log_sec_timer;
		var auto_sess_timer;
		var ajax_sess_timer;
		var auto_sess_timeout;
		var auto_sess_timeout_warning_time
		
			
	</script>
	<?PHP
		include_once ("autoSessionTimeout.php");
		include_once ("autoSessionTimeoutN.php");
		
	?>
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
</style>

<script>

function resize_that()
{
	var SW	=	screen.availWidth;
	var SH	=	screen.availHeight
	var RW	=	parseInt(SW * 0.97) ;
	var RH	=	SH;
	var LP	=	(SW - RW ) / 2;
	var TP	=	(SH - RH ) / 2;	
	window.moveTo(0,TP);
	window.resizeTo(RW,RH);
		
	//$(document).ready(function() {
		var hippa =	$('.all_admin_content_agree');
		var hippa_win = $(window).height();
		var header_height = $('.header_full_wrap').outerHeight(true);
		var footer_height = $('.footer_wrap ').outerHeight(true);
		var bottom_btns_h = $('.btn-footer-slider').outerHeight(true);
		var height_custom_scroll = $('#iframeHome').contents().find('.scrollable_yes');
		var height_custom = hippa_win - (header_height+footer_height+bottom_btns_h);
		var sub_head = $('#iframeHome').contents().find('.subtracting-head').outerHeight(true);
		var height_custom2  = height_custom - (sub_head);
		hippa.css({ 'min-height' : height_custom , 'max-height': height_custom });
		height_custom_scroll.css({ 'min-height' : height_custom2 , 'max-height': height_custom2});
	//});
}
$(window).load(function() { resize_that(); });
$(window).resize(function() { resize_that(); });


$(document).ready(function() {
	
	
	  $(".dropdown").click(            
		function() {
			$(this).children().closest('.dropdown-menu', this).stop( true, true ).slideToggle("fast");
			$(this).siblings().removeClass('active');
			$(this).siblings().children('.dropdown-menu').hide();						
			$(this).addClass('active');
			$(this).children().closest('.dropdown-menu').children('li').on('click',function(){
				
				$(this).parent().closest('.dropdown-menu').slideUp('fast');
				
			 });
			$(this).children().closest('.dropdown-menu').children('li a').on('click',function(){
				
				$(this).parent().parent().closest('.dropdown-menu').slideUp('fast');
				
			 });
		});
		
		$(".dropdown-menu #a_allactivecancelled").click(function(){
			$("#allactivecancelled").hide();	
		});
	
		$(".dropdown-menu .radioFilter").prop('checked','true',function(){
			$("#allactivecancelled").hide();	
		});						
		
	  $(".selectpicker").selectpicker();	
	  $('.edit_btn').on('click',function(){
			
		  $('#my_modal').modal({
				show: true,
				backdrop: true,
				keyboard: false
			});
		  
		  });
	  $("#search_by_ui li").click(function(){
		
		var v = $(this).find('a').data('val');
		var t = $(this).find('a').data('txt');
		
		$("#search_by_txt").html(t);
		$("#findBy").val(v);
		//$("#search_by_txt").parent().trigger('click');
	}); 
});
	
	var okCancelTrHT = 0;
	function showAppointments(){ 
		if(document.getElementById('okCancelTr')) {
			okCancelTrHT = $('#okCancelTr').outerHeight();
			document.getElementById('okCancelTr').style.display = 'none';
			$('#okCancelTr').removeClass('btn-footer-slider');
		}
		$('.middle_wrap').removeClass('agreement_margin_adjustment');
		$('#iframeHome').addClass('padding_0');
		
		if(document.getElementById('admin_audit_report_id')) {
			document.getElementById('admin_audit_report_id').style.display = 'inline-block';
		}
		if(document.getElementById('search_display_id')) {
			document.getElementById('search_display_id').style.display = 'inline-block';	
		}
		
		
		if(document.getElementById('messg_unread_id')) {
			document.getElementById('messg_unread_id').style.display = 'inline-block';
		}
		var objFrame = document.getElementById("iframeHome");
		<?php		
		//if((in_array('Admin', $privilegesArr) && in_array('EMR', $admin_privilegesArr)) || in_array('Super User', $privilegesArr) || in_array('Nursing Record', $privilegesArr) || in_array('Anesthesia', $privilegesArr) || in_array('Surgeon', $privilegesArr) || in_array('Staff', $privilegesArr) || in_array('Coordinator', $privilegesArr)){
			if($_REQUEST['hippaReviewedStatus']!='Showed'){
				$today = date('Y-m-d g:i:s A');
				$sqlUpdateUser="UPDATE users SET hippaReviewedYes = 'Yes', hippaReviewedStatus = 'Showed',hippaReviewedDateTime = '$today' where usersId = $sessLogUserId";
				$sqlQryUpdateUser = imw_query($sqlUpdateUser);			
			}
			?>
			if(objFrame) { 
				objFrame.src = 'home_inner_front.php';
			}
			//top.frames[0].location.href = 'home_inner_front.php';
			<?php
		//}
		?>
	}
	function changePasswardFn(id){
		document.getElementById(id).style.display = 'none';
		top.frames[0].location.href = 'admin/index.php';		
	}
	
	var showCancelButton = '';
	function changePasswardFnNew(id,showCancelButton){
		if(document.getElementById(id)) {
			document.getElementById(id).style.display = 'none';
		}
		document.getElementById('okCancelTr').style.display = 'none';
		document.getElementById('admin_audit_report_id').style.display = 'none';
		document.getElementById('search_display_id').style.display = 'none';
		top.frames[0].location.href = 'change_password.php?showCancelButton='+showCancelButton;		
	}
	
	var LoginUserId='';
	function openTab(id,LoginUserId){		
		var objFrame = document.getElementById("iframeHome");
		document.getElementById('okCancelTr').style.display = 'none';
		if(id=="Admin"){
			objFrame.src = "admin/index.php";	
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_slidNew";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background='#3232F0';
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
			}	
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px; border:none;">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
		}else if(id=="Audit"){
			objFrame.src = "audit/audit.php";
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg"style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_slidNew";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background='#3232F0';
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
			}	
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";			
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
		
		}else if(id=="Reports"){
			objFrame.src = "report.php";
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_slidNew";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background='#3232F0';
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
		
			}
		}else if(id=="MessageDisplay"){
			objFrame.src = "message_display.php?msg_user_id="+LoginUserId;
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("auditTab")) {
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";			
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			//objFrame.location.href = "message_display.php";  
		
		}else if(id=="Anesthesia"){
			var sessLogUserId = '<?php echo $sessLogUserId;?>';
			objFrame.src = "admin/index.php?anesthesiologistList="+sessLogUserId;			
			//top.frames[0].frames[0].frames[0].location='anesthesia_profile_save.php?anesthesiologistList=135';
			
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("anesthesiaTab")) {
				document.getElementById("anesthesiaTab").className="link_slidNew";
				document.getElementById("TDanesthesiaTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDanesthesiaMiddleTab").style.background='#3232F0';
				document.getElementById("TDanesthesiaBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
			}	
				
		}else if(id=="iOLink"){
			objFrame.src = "iOLink.php";			
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			
			}
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("anesthesiaTab")) {
				document.getElementById("anesthesiaTab").className="link_slidNew";
				document.getElementById("TDanesthesiaTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDanesthesiaMiddleTab").style.background='#3232F0';
				document.getElementById("TDanesthesiaBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("iOLinkTab")) {
				document.getElementById("iOLinkTab").className="link_slidNew";
				document.getElementById("TDiOLinkTopTab").innerHTML='<img src="images/bg_tableftBlue.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDiOLinkMiddleTab").style.background='#3232F0';
				document.getElementById("TDiOLinkBottomTab").innerHTML='<img src="images/bg_tabrightBlue.jpg" style=" width:3px; height:27px;">';
			}	
		
		}else{
			//top.iframeHome.iframe_home_inner_front.location = "home_inner_front.php?txt_patient_search_id="+redirect_txt_patient_search_value+"&display_cal=none&display_patient_sch=none&display_patient_search=display";
			//objFrame.location.href = "home_front.php";
			objFrame.src = "home_front.php";
		}
	}
	//FUNCTIONS BY SURINDER
	var adminLoginUserId='';
	function openAdminTab(tab_name,adminLoginUserId){	
		var objFrame = document.getElementById("iframeHome");
		document.getElementById('hidTab').value = tab_name;
		objFrame.src = "admin/index.php";
	}
	
	
	
	function openReports(tab_name,adminLoginUserId){	
		top.document.getElementById('crsBtnId').style.display = 'inline-block';
		var objFrame = document.getElementById("iframeHome");
		//document.getElementById('hidTab').value = tab_name;
		objFrame.src = tab_name;
	}
	
	
	function openSearchTab()
	{
				
		var txt_patient_search_value = eval(document.getElementById("txt_patient_search_id"));
		if(txt_patient_search_value.value == "") {
			//alert("Enter search box for patient ");
			alert("Please enter value to search ");
			txt_patient_search_value.focus();
			return false;
		}else{
			$(".loader").fadeIn('fast').show('fast'); 
			//CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
			if(document.getElementById("adminTab")) {
				document.getElementById("adminTab").className="link_a";
				document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}	
			if(document.getElementById("auditTab"))	{
				document.getElementById("auditTab").className="link_a";
				document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
				document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			if(document.getElementById("reportsTab")) {
				document.getElementById("reportsTab").className="link_a";
				document.getElementById("TDreportsTopTab").innerHTML='<img src="images/bg_tableft.jpg" style=" width:3px; height:27px; border:none;">';
				document.getElementById("TDreportsMiddleTab").style.background="url(images/bg_tab.jpg)";
				document.getElementById("TDreportsBottomTab").innerHTML='<img src="images/bg_tabright.jpg" style=" width:3px; height:27px;">';
			}
			//END CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
			var findByObj = eval(document.getElementById("findBy"));
			findByVal = "";
			if(findByObj) {
				findByVal = findByObj.value;	
			}
			
			top.frames[0].location = "home_inner_front.php?txt_patient_search_id="+txt_patient_search_value.value+"&display_cal=none&display_patient_sch=none&display_patient_search=display&findBy="+findByVal;			
		
			
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
	function changeSearchType(stObj,find_by) {
		if(isNaN(stObj.value)) {
			if(document.getElementById(find_by)) {
				$("#search_by_ui li:eq(1)").trigger('click');
			}
		}else if(!isNaN(stObj.value) && stObj.value) {
			if(document.getElementById(find_by)) {
				if(document.getElementById(find_by).value == "Patient_Name") {
					$("#search_by_ui li:eq(0)").trigger('click');	
				}
			}
		}
	}
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
	var windowObject ={};
	var winHghtNew = jQuery(window).height();
	var winWidtNew = jQuery(window).width();
	var winLeft	   = (screen.availWidth - winWidtNew ) / 2;
	var winTop	   = (screen.availHeight - winHghtNew ) / 2;		
	//var tools = 'resizable, top=10, width=1020, height=650, location=yes,status=yes';
	var tools = 'resizable, top=10, width='+winWidtNew+', height='+winHghtNew+', location=yes,status=yes,left='+winLeft+',top='+winTop+'';
	function setwin(winName,stbId,url) {
		//alert(winName+stbId+url);
		
		if(windowObject[winName+stbId]!=null && !windowObject[winName+stbId].closed) {
			//alert('window is already opened for this link and will set the focus to that window');
			windowObject[winName+stbId].focus();
		}
		else {
			//alert('New window will be opened for this link');
			//windowObject[winName+stbId+url] = window.open(url, winName+stbId+url, tools);
			//top.iframeHome.
			//alert(winName+stbId+url);
			windowObject[winName+stbId] = window.open(url, winName+stbId, tools);
			windowObject[winName+stbId].focus();
		
			//alert(top.opener.document.forms[0].chkOpenedChildWind);
			/*
			if(top.opener.document.forms[0].chkOpenedChildWind) {
				if(top.opener.document.forms[0].chkOpenedChildWind.value) {
					top.opener.document.forms[0].chkOpenedChildWind.value=top.opener.document.forms[0].chkOpenedChildWind.value+','+winName+stbId+url;
				}else {
					top.opener.document.forms[0].chkOpenedChildWind.value=winName+stbId+url;
				}	
			}	
			*/
		
		}
		
	}
	
//END FUNCTION TO OPEN 'NEW CHARTNOTE WINDOW' OR SET FOCUS ON 'EXISTING CHATNOTE WINDOW'	

	var LD	=	function()
	{
		var WH	=	$(document).height();
		var HH	=	parent.top.$(".header_full_wrap").outerHeight();
		var FH	=	parent.top.$(".footer_wrap").outerHeight();
		var MH	=	50;
		if(document.getElementById('okCancelTr').style.display == 'none') {
			$('#okCancelTrOuterHeight').val(okCancelTrHT);
			FH = parseFloat(document.getElementById('okCancelTrOuterHeight').value);
			MH = 0;
		}
		
		if(document.getElementById('trExpirePassword'))
		{
			if(document.getElementById('trExpirePassword').style.display == 'block') {
				FH = FH + parseInt($("#trExpirePassword").outerHeight());
			}
		}
		
		if(document.getElementById('trExpired'))
		{
			if(document.getElementById('trExpired').style.display == 'block') {
				FH = FH + parseInt($("#trExpired").outerHeight());
			}
		}
		
		
		//if(GH)
		//var OKC = 	parseFloat(top.document.getElementById('okCancelTrOuterHeight').value);
		var BH	=	WH - (HH + FH + MH);
		//alert('Window Height : ' + WH + '\n Header Height : ' + HH + '\n Footer Height : ' + FH + '\nBody Height : ' + BH);
		parent.top.$("#iframeHome").attr('height',BH)
		
	};
	
	$(window).load(function() { LD(); });
	$(window).resize(function(){ LD(); });
	
	function closFun() {
		top.frames[0].location.href	 = 'home_inner_front.php';
		top.document.getElementById('crsBtnId').style.display = 'none';
	}
</script>
</head>
<?php

$objManageData = new manageData;
//UPDATE USER LOGIN ATTEMPTS
	$arrayAttempts['loginAttempts'] = 0;
	$objManageData->updateRecords($arrayAttempts, "users", "usersId", $userLoginId);
//UPDATE USER LOGIN ATTEMPTS

//GET USERS EXPIRES ALERT//
	$maxExpireDays = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
	$maxDaysToExpire = $maxExpireDays->maxPassExpiresDays;	
	$userDetails = $objManageData->getRowRecord('users', 'usersId', $userLoginId);
	$passChangedLatDate = $userDetails->passCreatedOn;
	if($today!=$passChangedLatDate){
		$differanceBetween = $objManageData->getDateDifferance($today, $passChangedLatDate);
		$expireDaysLeft = $maxDaysToExpire-$differanceBetween;
		if($differanceBetween>=$maxDaysToExpire){
		//echo $differanceBetween.' => '.$maxDaysToExpire;
			$arrayRecord['locked'] = 1;
			//$objManageData->updateRecords($arrayRecord, "users", "usersId", $userLoginId);
			unset($arrayStatusRecord);
			$arrayStatusRecord['user_id'] = $userLoginId;
			$arrayStatusRecord['status'] = 'locked';
			$arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
			$arrayStatusRecord['operator_id'] = $userLoginId;
			$arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
			$arrayStatusRecord['comments'] = 'Login attempt';
			//$objManageData->addRecords($arrayStatusRecord, 'password_change_reset_audit_tbl');
			
			$passwardExpiers = "true";
		}else if($expireDaysLeft <= 7){
			$daysLeft = 'true';
		}
	}
//GET USERS EXPIRES ALERT//

// GETTING LOGIN USER NAME
	$str = "SELECT CONCAT(lname,', ', fname,' ', mname) 
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
?>
<body onload="startTime()" style="overflow:hidden" >
<!--START-->
<div class="main_wrapper">
    <!-- Loader -->
    <div class="loader">
    	<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
	</div>
    <!-- Loader-->
    
    <div id="dialogBoxScreen" ></div>
    <div id="auto_sess_timeout_span"></div>
    <div class="header_full_wrap navbar navbar-fixed-top to_get_full_width" >        
        <div class="header_wrap"></div>
        <div class="header_wrap_2 login_changes">
            <div class="container-fluid">
            	<div class="col-md-3 col-lg-3 col-sm-4 text-center col-xs-12 text-center to_get_full_820">
                			<Div class="admin_head_2" style="width:400px;">
                            	<a href="javascript:window.location.reload();">
                            		<img src="images/logo_iascemr.png">             
                        		</a>	
                              <div class="wrap_user_n text-center">
                                            <p class="rob">
                                              <!--<Span class="glyphicon glyphicon-user"></Span>	-->
                                              <span> <?php echo $userName; ?> </span>
                                            </p>
                               </div> 
                            </Div>              
              	 </div>  <!-- IST col Ends HEAd-->    
                 <div class="col-md-5 col-lg-5 col-sm-4 col-xs-12  to_get_full_in480 "> 
                 
                        	<Div class="row">	
                            	<div class="col-md-7 col-lg-8 col-sm-7 col-xs-6 text-center" style="white-space:nowrap;">
                                    <a class="rob after_login_practice">
                                        <span>
                                            <?php echo $_SESSION['loginUserFacilityName'];?>    
                                        </span>
                                     </a>
                                    
                                 </div>     
                                 <div class="col-md-5 col-lg-4 col-sm-5 col-xs-6 text-left padding_0 style_2_head">
                                      <div class="date_wrap text-center "style=""> 	
                                             <span class="rob" id="dt_tm"><!--SHOW DATE/TIME--></span>
                                      </div> 
                                   
                                </div> 
                             </Div>
                 </div>
          		 
                 <div class="col-md-4 col-lg-4 col-sm-4 col-xs-12 to_get_full_in480 style_2_head forsign_buttons padding_0 ">
						<Div class="row">
                            
                            <div class=" col-md-7 col-xs-7 col-lg-7 col-sm-7">     
                                <div class="search_wrap" id="search_display_id" style="overflow:unset!important; display:none;">
                                  	<div class="input-group" style="position:static;">
                                  		<div class="input-group-addon pd0">
                                            <div  style="min-width:120px; cursor:pointer;"> <!--class=" btn btn-info dropdown "-->
                                                <div class="dropdown-toggle" data-toggle="dropdown">
                                                    <div id="search_by_txt" style="display:inline-block" class="bold">ASC ID</div>
                                                    <i class="caret"></i>	 
                                                </div>
                                                <input type="hidden" id="findBy" value="ASC_ID" />
                                                <ul class="dropdown-menu text-left" id="search_by_ui">
                                                    <li><a href="javascript:void(0);" data-val="ASC_ID" data-txt="ASC ID">ASC-ID</a></li>
                                                    <li><a href="javascript:void(0);" data-val="Patient_Name" data-txt="Patient Name">Patient Name</a></li>
                                                    <li><a href="javascript:void(0);" data-val="External_MRN" data-txt="External MRN">External MRN</a></li>
                                                </ul>
                                            </div>
                                      	</div>
                                      <input name="txt_patient_search_id" id="txt_patient_search_id" onKeyPress="KeyCheck(event);" onKeyUp="changeSearchType(this,'findBy');" type="text" class="form-control" placeholder="" aria-describedby="search_link_id">
                                      <span class="input-group-addon" id="search_link_id" onclick="return openSearchTab();"><b class="fa fa-search"></b></span>
                                  	</div>
                               </div>
                            </div>  
                            
                            <div class="col-md-5 col-xs-5 col-lg-5 col-sm-5"> 
                            	<div class="abs_change_pwd">
                                	<a href="javascript:void(0)" class="hvr-bounce-in"> <img src="images/change_pwd.png" alt="change password" title="Change Password" onClick="javascript:changePasswardFnNew('trExpired');" /> </a>   
                                </div>
                            	<div class="wrap_top_btns" style="margin-left:10px;">
                                    <a href="javascript:void(0)" onClick="location.href='switchUser.php';" class="btn btn-info">  <b class="fa fa-user-md"></b> Switch User  </a>   
                                    <a href="javascript:void(0)" onClick="location.href='index.php?logout=true';" class="btn btn-info">  <b class="fa fa-sign-out"></b> Logout</a>   
                                </div>
                                
                            </div>
                                     
  						</Div>                                     
                        </div>   
            </div>
        
              
            
        </div>
    	<div id="admin_audit_report_id" style=" display:none;" class="Menu_yup gradient navbar navbar-default" role="navigation">
        <a id="crsBtnId"  onClick="closFun();" class="btn btn-danger abs_cross" style=" display:none;z-index:9999"> X </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
            </button> 
        	<div class="container-fluid">
           	     <div id="bs-megadropdown-tabs" class="navbar-collapse collapse">
             	   <ul class="nav navbar-nav nav-tabs nav-justified">
                        <input type="hidden" name="hidTab" id="hidTab" value="">
						<?php if((in_array('Admin', $privilegesArr)) || (in_array('Super User', $privilegesArr))){ ?>
                        <li  class="dropdown mega-dropdown ">
                            <a class="dropdown-toggle" href="#" data-target=".dropdown-menu"> Admin <span class="caret"></span></a>				
                            <div class="dropdown-menu mega-dropdown-menu" style="">
                                <div class="container-fluid">
                                    <!-- Tab panes -->
                                    <ul class="nav-list list-inline nav-justified sub-menu text-left">
                                     	<?PHP
											if(in_array("Super User", $privilegesArr) || in_array("Admin", $privilegesArr))
											{ 
										?>
                                        		 <li onClick="openAdminTab('facility_list');"> <a href="javascript:void(0)"> ASC</a></li>
                                        <?PHP
											}if(in_array("Super User", $privilegesArr) || in_array("Admin", $privilegesArr))
											{ 
										?>
                                        		<li onClick="openAdminTab('admin_asc');"> <a href="javascript:void(0)" > Settings    </a></li>	
                                               
                                        <?PHP
											}
											if(  in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("User", $admin_privilegesArr))
											  )
											
											{
										?>		
                                        		<li onClick="openAdminTab('admin_users');"> <a href="javascript:void(0)" > Users  </a></li>	
                                        <?PHP
											}
											
											if(	 in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("Pre-Op Med", $admin_privilegesArr))
											  )
											
											{
										?>
                                        		<li onClick="openAdminTab('admin_pre_op_med_order');"> <a href="javascript:void(0)"> Pre Op. Med Order </a></li>	
                                      	<?PHP
											}
											
											if(	 in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("Surgeon profile", $admin_privilegesArr))
											  )
											  
											{
										?>
                                        		<li onClick="openAdminTab('surgeon_profile');"> <a href="javascript:void(0)"> Surgeon Preference Card </a></li>	
                                       	<?PHP
											}
											
											if(	 in_array("Super User", $privilegesArr) ||
												(in_array("Admin", $privilegesArr) && in_array("Predefines", $admin_privilegesArr))
											  )
											
											{
										?>
                                        		<li onClick="openAdminTab('pre-define');"> <a href="javascript:void(0)"> Pre-Define</a></li>
                                      	<?PHP
											}
											
											if(	 in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("Operative Reports", $admin_privilegesArr))
											  )
											  
											{
										?>
                                        		<li onClick="openAdminTab('op-report');"> <a href="javascript:void(0)"> Op-Report</a></li>
                                      	<?PHP
											}
											if(	 in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("Instruction Sheet", $admin_privilegesArr))
											  )
											{
										?>
                                        		<li onClick="openAdminTab('instruct-sheet');"> <a href="javascript:void(0)"> Instruct Sheet </a></li>	
                                       	<?PHP
											}
                                        ?>
                                        
                                  	</ul>
                                    
                                    <ul class="nav-list list-inline nav-justified sub-menu text-left">
										<?PHP
                                        	if(	 in_array("Super User", $privilegesArr) || 
												(in_array("Admin", $privilegesArr) && in_array("EMR", $admin_privilegesArr))
											  )
											{
										?>
                                        		
                                                <li onClick="openAdminTab('consentFormMultiple');"><a href="javascript:void(0)">Consent Form</a></li>
                                                <li onClick="openAdminTab('anesthesia_profile');"><a href="javascript:void(0)"> Ans. Preference Card</a></li>
                                      			<!--<li onClick="openAdminTab('nurseQuestion');"> <a href="javascript:void(0)"> Pre-Op Nurse </a></li>	-->
												<li onClick="openAdminTab('laser_procedure_admin');"> <a href="javascript:void(0)"> Laser</a></li>	
												<li onClick="openAdminTab('anes_ekg_admin');"> <a href="javascript:void(0)"> Anes EKG</a></li>	
                                                <li onClick="openAdminTab('supplies');"><a href="javascript:void(0)">Procedure Supplies</a></li>
										<?PHP
											} 
											
											elseif( in_array("Anesthesia", $privilegesArr) && !in_array("EMR", $admin_privilegesArr) )
											{
										?>          
												<li onClick="openAdminTab('Anesthesia');"><a href="javascript:void(0)"> Ans. Preference Card</a></li>
                                        
                                        <?PHP
										
											}
											
										?>
                                        
										<li onClick="openAdminTab('procedureprofile');"><a href="javascript:void(0)">Procedure Preference Card </a></li>
                    					<li onClick="openAdminTab('injectionMisc');"> <a href="javascript:void(0)">Injection/Misc.</a></li>		
                    					<li onClick="openAdminTab('nurse_profile');"><a href="javascript:void(0)"> Nurse Preference Card</a></li>
                    
                                       
                                    </ul>
                                </div>
                            </div>	
                        </li>
                        <?php 
						}
						else if($user_type =='Anesthesiologist') {  
						?>
                        
                        	<li>	
                             	<a href="javascript:void(0);" onclick="openAdminTab('Anesthesia')" > Admin </a>	
                           	</li>
                            
						<?PHP
                        }
						if((in_array('Audit', $privilegesArr))){ ?>	  
						
                        <li  class="dropdown mega-dropdown "> 
                           <a class="dropdown-toggle" href="#" data-target=".dropdown-menu"> Audit <span class="caret"></span></a>				
                            <div class="dropdown-menu mega-dropdown-menu" style="">
                                <div class="container-fluid">
                                    <!-- Tab panes -->
                                     <ul class="nav-list list-inline nav-justified sub-menu">
                                       	<li onClick="javascript:openReports('audit/login.php');"> <a href="javascript:void(0)"> Login/Logout </a></li>	
                                    	<li onClick="javascript:openReports('audit/pass.php');"> <a href="javascript:void(0)"> Password Change/Reset/Lockouts </a></li>	
                                    	<li onClick="javascript:openReports('audit/chart_note.php');"> <a href="javascript:void(0)"> Patient Chart Notes </a></li>	
                                    	<li >&nbsp;</li>	
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <?php 
						}
						if((in_array('Report', $privilegesArr))){ ?>
                        <li  class="dropdown mega-dropdown">
                         <a class="dropdown-toggle" href="#" data-target=".dropdown-menu"> Reports <span class="caret"></span></a>				
                            <div class="dropdown-menu mega-dropdown-menu" style="">
                                <div class="container-fluid">
                                    <!-- Tab panes -->
                                     <ul class="nav-list list-inline nav-justified sub-menu text-left">
                                        <li onClick="javascript:openReports('day_report.php');"> <a href="javascript:void(0);">Day Report</a></li>	
                                    	<li onClick="javascript:openReports('discharge_summary_report.php');"> <a href="javascript:void(0)">Discharge Summary Report</a></li>	
                                    	<li onClick="javascript:openReports('procedural_report.php');"> <a href="javascript:void(0)">Procedural Report</a></li>	
                                    	<li onClick="javascript:openReports('physician_report.php');"> <a href="javascript:void(0)">Physician Report</a></li>	
                                    	<li onClick="javascript:openReports('unfinalizedpatient_report.php');"> <a href="javascript:void(0)">Un-Finalized Patient Report</a></li>	
                                        <li onClick="javascript:openReports('day_anesthesia_chart_report.php');"> <a href="javascript:void(0)">Day Anesthesia Chart</a></li>
                                        <li onClick="javascript:openReports('day_surgeon_op_notes_report.php');"> <a href="javascript:void(0)">Day Surgeon OP Notes</a></li>	
                                    </ul>
                                    
                                    <ul class="nav-list list-inline nav-justified sub-menu text-left">
                                    	<li onClick="javascript:openReports('iol_report.php');"> <a href="javascript:void(0)">IOL Report</a></li>	
                                    	<li onClick="javascript:openReports('supply_used_report.php');"> <a href="javascript:void(0)">Supply Used Report</a></li>	
                                    	<li onClick="javascript:openReports('proc_phy_report.php');"> <a href="javascript:void(0)">Proc CSV Report</a></li>	
                                    	<li onClick="javascript:openReports('narcotics_report.php');"> <a href="javascript:void(0)">Narcotics Report</a></li>
                                        <li onClick="javascript:openReports('patient_monitor_report.php');"> <a href="javascript:void(0)">Patient Monitor</a></li>
                                        <li onClick="javascript:openReports('incomplete_chart_report.php');"> <a href="javascript:void(0)">Incomplete Chart</a></li>
                                        <li onClick="javascript:openReports('appointment_report.php');"> <a href="javascript:void(0)">Appointment Report</a></li>		
                                    </ul>
                                    <ul class="nav-list list-inline nav-justified sub-menu text-left">
                                    	<li onClick="javascript:openReports('proc_cost_ana_report.php');"> <a href="javascript:void(0)">Procedure Cost Analysis</a></li>	
                                    	<li onClick="javascript:openReports('supply_cost_ana_report.php');"> <a href="javascript:void(0)">Supplies Cost Analysis</a></li>	
                                        <li onClick="javascript:openReports('labor_cost_ana_report.php');"> <a href="javascript:void(0)">Labor Cost Analysis</a></li>		
                                        <li onClick="javascript:openReports('or_time_report.php');"> <a href="javascript:void(0)">OR Time: Surgeons</a></li>
                                        <li onClick="javascript:openReports('vision_success_report.php');"> <a href="javascript:void(0)">Vision Success</a></li>
                                         <li onClick="javascript:openReports('complication_report.php');"> <a href="javascript:void(0)">Complication</a></li>
                                       <?php 
																			 
										if($authVisionCompTab) { 
											$onClickVisionCompTab	=	"javascript:openReports('vision_complication_report.php');";
										}
										else
										{
											$onClickVisionCompTab	=	"javascript:modalAlert('Only Surgeon and ASC Coordinator are authorised to access this report');";
										}
										 ?>
                                       	<li onClick="<?=$onClickVisionCompTab?>">
                                        	<a href="javascript:void(0)">Vision Success & Complication</a>
                                       	</li>
                                   	</ul>
                                    
                                    <ul class="nav-list list-inline nav-justified sub-menu text-left">
										<!-- Tab Narcotics Report-->
                                      <li onClick="javascript:openReports('nc_state_report.php');"> <a href="javascript:void(0)">NC State Report</a></li>
                                      <li onClick="javascript:openReports('charges_posted_report.php');"> <a href="javascript:void(0)">Charges Posted Report</a></li>
                                      <li onClick="javascript:openReports('surgery_log_book.php');"> <a href="javascript:void(0)">Surgery Log Book</a></li>
									  
										<!--Tab AMD Charges Posted Report-->
									  <?php
											$liSpaceCnt = 0;
											if(defined('AMD_POST_CHARGES') && AMD_POST_CHARGES == "YES" ) {?>
                                      	  		<li onClick="javascript:openReports('amd_charges_report.php');"> <a href="javascript:void(0)">AMD Charges</a></li>
												<li onClick="javascript:openReports('direct_visit_report.php');"><a href="javascript:void(0)">Missing Visit ID Report</a></li>
                                      <?php }else {$liSpaceCnt+=2; }
											
											if(DISPLAY_PRESS_GANEY_REPORT=='YES'){ ?>					
    	                                  		<li onClick="javascript:openReports('press_ganey_report.php');"> <a href="javascript:void(0)">Press Ganey Report</a></li>	
                                      <?php }else{ $liSpaceCnt++;}?>
	                                      
                                      <li>&nbsp;</li>
                                      <?php for($spcnt=0; $spcnt<$liSpaceCnt; $spcnt++ ) {?>
                                      			<li>&nbsp;</li>
                                      <?php	}?>
                                  	</ul>    
                                        
                                </div>
                            </div>	
                         </li>
                         <?php
						}
						?>
                    </ul>
		        <!---  NAvigation Custom    ---->
                </div>	
            </div>
        </div>
        
        
        
	</div>
    <!-- Middle -->
  	<div id="div_middle" class="middle_wrap margin_bottom_mid_adjustment scheduler_margins_head agreement_margin_adjustment clear">
    	<?php 
			if($passwardExpiers=="true")
			{ 
		?>
				<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center " id="trExpirePassword" style=" font-size:12px; font-weight:bold; margin-bottom:0; padding:1px; color:#CC0017; background-color: #f1f1f1;">
                	• Password Expired. User must change password. <a class="black" href="javascript:changePasswardFnNew('trExpirePassword');" style="font-weight:bold">Click Here to Change.</a>
              	</div>
                
		<?php
			}
			else if($daysLeft=='true')
			{
		?>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center " id="trExpired" style=" font-size:12px; margin-bottom:0; padding:1px; color:#CC0017; background-color: #f1f1f1; ">
                	• Password will Expire <?php  if($expireDaysLeft==0)echo 'today'; else echo ' in '.$expireDaysLeft.'day(s)'; ?>. <a class="black" href="javascript:changePasswardFnNew('trExpired');" style="font-weight:bold" >Click Here to Change.</a>
              	</div>
       	<?php
			}
		?>
        	
	<div class="container-fluid padding_0 clear">
        <div class="inner_surg_middle clear ">
            	<div class="all_content1_slider clear ">
	            	<div id="div_ovrflow" class="wrap_inside_admin all_admin_content_agree clear" >
                		<iframe class="embed-responsive-item" scrolling="no" id="iframeHome" name="iframeHome" src="home_front.php" ></iframe>
                    </div>
               	</div>
                
                <div id="okCancelTr" class="btn-footer-slider" >
                    <a id="saveBtn" title="OK" href="javascript:void(0)" class="btn btn-info" onClick="javascript:showAppointments();">  OK  </a>
                    <a id="CancelBtn" title="Cancel" href="javascript:void(0)" onClick="javascript:window.close();" class="btn btn-danger">Cancel</a>
                </div>
                
           		<div class="push"><input type="hidden" name="okCancelTrOuterHeight" id="okCancelTrOuterHeight" value="0"></div>
                <div id="admin_audit_report_id" style="display:none; margin-top:5px;"></div>
			
		</div>
	</div>
  	</div>                                        	
    
	<div class="footer_wrap navbar navbar-default navbar-fixed-bottom">
        <div class="container">
        
        <?php 
				if($passwardExpiers=="true"){}else if($daysLeft=='true'){}	
	
				$ipadscroll='';
				if($browserPlatform == "iPad") { $ipadscroll='scrolling="no"'; }
				$prdtVrsnDt = 'Ver R5.2  Jan 03, 2013';
				if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }
		?>
        
        
            <span class="footer_span">Under &copy; MIT License - <?php echo substr($prdtVrsnDt,-4,4);?>. IMWEMR &reg; All rights reserved. <a class="version"><?php echo $prdtVrsnDt;?></a></span>
           
        </div>
    </div>    
</div>

<!--END-->
<!--<table class="table_collapse alignCenter" style="width:990px; background-color:#ECF1EA;" >
	<tr>
		<td colspan="3" class="valignTop nowrap">
			
		 </td>
	</tr>
	<tr id="okCancelTr"> 
		<td class="alignCenter" colspan="3">
			<table class="table_collapse">
				<tr>
					<td class="alignRight valignTop" id="okButtonId">						
						<a id="saveBtn" title="OK" href="javascript:void(0)" class="btn btn-primary" onClick="javascript:showAppointments();">  <b class="fa fa-save"></b>  OK  </a>
					</td>
					<td style="width:10px;"></td>
					<td class="alignLeft valignTop">
                    	<a id="CancelBtn" title="Cancel" href="javascript:void(0)" onClick="javascript:window.close();" class="btn btn-danger " id="">Cancel</a>
                    </td>
					<td style="width:10px;"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>-->
<script type="text/javascript" src="js/mools.v1.11.js"></script>
<script type="text/javascript" src="js/moocheck.js"></script>

<script>
var tempvar = '<?php echo $_REQUEST["pageclose"];?>';
if(tempvar=='y') {
	document.getElementById('okCancelTr').style.display = 'none';
	document.getElementById('admin_audit_report_id').style.display = 'inline-block';
	document.getElementById('search_display_id').style.display = 'inline-block';
	<?php
	if((in_array('Admin', $privilegesArr) && in_array('EMR', $admin_privilegesArr)) || in_array('Super User', $privilegesArr) || in_array('Anesthesia', $privilegesArr) || in_array('Nursing Record', $privilegesArr) || in_array('Surgeon', $privilegesArr) || in_array('Staff', $privilegesArr)){
	
		?>
		top.frames[0].location.href = 'home_inner_front.php';
		<?php
	}
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
		if($_REQUEST['hippaReviewedStatus']=='Showed' || $hipaRvwStatus=='Showed'){
		?>
			showAppointments();
	<?php
		}
	?>
	//document.getElementById("iframeHome").style.height='565px';
	
	$(function(){ 
		$('.version').click(function(){
			var modalObj	=	$('#releaseNoteModal');
			modalObj.modal({show:true,backdrop:false});	
		});
		
		var passwardExpiers = '<?php echo $passwardExpiers;?>';
		if(passwardExpiers=="true"){ changePasswardFnNew('trExpirePassword','no');	}
	});
</script>

<?php 
	include 'release_note.php';
	include 'no_record.php';
?>
</body>
</html>