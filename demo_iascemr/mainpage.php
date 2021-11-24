<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once('common/conDb.php');
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once('common/user_agent.php');

$patient_conf_status=1;
//check patient status in pt comfirmation
$qCheckCancel=imw_query("select patient_status from stub_tbl  WHERE patient_confirmation_id='".$_REQUEST['pConfId']."' and (patient_status='Canceled' OR patient_status='No Show' OR patient_status='Aborted Surgery')");
if(imw_num_rows($qCheckCancel)>=1)$patient_conf_status=0;

$frameHref		=	$_REQUEST['frameHref'];
$frameHrefTempr =	$frameHref;
$privileges		=	$_SESSION['userPrivileges'];
$loginUser		=	$_SESSION['loginUserId'];
$preOpHealthQuesId=	$_REQUEST['preOpHealthQuesId'];
$thisId 		=	$_REQUEST['thisId'];
$innerKey		=	$_REQUEST['innerKey'];
$SaveForm_alert	=	$_REQUEST["SaveForm_alert"];
$preColor		=	"#999999";
$Save_Print_alert=	$_REQUEST["Save_Print_alert"];
$multiwin		=	$_REQUEST['multiwin'];
$consentMultipleId=	$_REQUEST['consentMultipleId'];
$stub_id 		=	$_REQUEST['stub_id'];
$myPage			=	$_REQUEST['myPage'];

$priviliges		=	explode(",",$_SESSION['userPrivileges']);
$priviliges		=	array_map('trim',$priviliges);
$priviligesTo	=	array('Admin','Super User');
$hasAdminPrv	=	(count(array_intersect($priviligesTo,$priviliges)) > 0 ) ? 1 : 0;

if($consentMultipleId) {
	$consentMultipleIdLink = '&consentMultipleId='.$consentMultipleId;
}


//PURGE
$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
if($consentMultipleAutoIncrId) {
	$consentMultipleAutoIncrId = '&consentMultipleAutoIncrId='.$consentMultipleAutoIncrId;
}

$hiddPurgestatus = $_REQUEST['hiddPurgestatus'];
if($hiddPurgestatus) {
	$hiddPurgestatus = '&hiddPurgestatus='.$hiddPurgestatus;
}
//PURGE

// Get Surgeon Practice Match Result if Peer Review Option is on
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width= device-width, initial-scale=1"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="ie=edge" />		
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">		
<script>
//window.moveTo(0, 0); 
//window.resizeTo(screen.availWidth, screen.availHeight);

//OPEN WINDOW IN FRONT OF SCHEDULER WINDOW
if(opener) {
	//window.focus();
}
//OPEN WINDOW IN FRONT OF SCHEDULER WINDOW

//FUNCTION TO MAKE ALL SING OF A LOGGED-IN SURGEON IN CURRENT CHARTNOTE
	var loggedInUserId;
	var signCheck='true';
	function getSignAll(patient_id,pConfId,loggedInUserId,chkDisplaySurgeonSign,assignedSurgeonId,assignedSurgeonName) {
		if(loggedInUserId!=assignedSurgeonId) {
			
			/*
			var r = confirmVbScript("This patient is registered to Dr. "+assignedSurgeonName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
			if(r==false) {
				signCheck='false';
			}else {
				signCheck='true';
			}*/
			
		}
		if(signCheck == 'true') {
			if(top.document.forms[0].hidd_chkDisplaySurgeonSign.value=='true') {
				
				var cType		=	$("#confirmType").val();
				var modalId		=	(cType == 'discharge')	?	'dischargeConfirmModal'	:	'signAllConfirmModal'	;
				var cBtnId		=	(cType == 'discharge')	?	'confirmBtnR'			:	'confirmBtn'	;
				var HeadMsg		=	(cType == 'discharge')	?	'Review Discharge Summary Sheet - Please Confirm'	:	'Confirmation'	;
				HeadMsg			=	'<i class="fa fa-question-circle"></i>&nbsp;'+HeadMsg;
				var modalObj	=	$('#'+modalId);
				var cBtnObj		=	$('#'+cBtnId);
				
				var url			=	'sign_all_surgeon.php?patient_id='+patient_id+'&pConfId='+pConfId+'&loggedInUserId='+loggedInUserId;
				
				cBtnObj.show(50);
				modalObj.find('.modal-header h4 span ').html(HeadMsg);
				modalObj.find('p').html('Please confirm you have reviewed all the documents!');
				modalObj.modal({show:true,backdrop:false});
				cBtnObj.click(function(){ 
					
					if(cType === 'discharge')
					{
						//$("#frm_discharge_review").submit();
						$.ajax({
							url : 'ajax/discharge_summary_review_ajax.php',
							type:'POST',
							data:$("#frm_discharge_review").serialize(),
							dataType:"json",
							beforeSend: function()
							{
								top.$(".loader").show(50);
							},
							complete:function(){
								top.$(".loader").hide(50);
							},
							success:function(data)
							{
								if(data.success == '1')
								{
									top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'false';
									top.mainFrame.main_frmInner.location=url;	
									return true;
								}
								else if(data.success == '2')
								{
									top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'false';
									top.mainFrame.main_frmInner.location=url;	
									return true;
								}
								else
								{
									alert('Error!!! Please try again');
									return false;	
								}
										
							}
							
						});
						return false; 
					}
					else
					{
						
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'false';
						top.mainFrame.main_frmInner.location=url;
						return true;
					}
					
						
				});
				
				/*if(confirm('Please confirm you have reviewed all the documents!')) {
					//var frmObj = top.mainFrame.main_frmInner.location.href='sign_all_surgeon.php?patient_id='+patient_id+'&amp;pConfId='+pConfId+'&loggedInUserId='+loggedInUserId;
				}else {
					return false;
				}*/
				
			}
			else if(top.document.forms[0].hidd_chkDisplaySurgeonSign.value=='false') {
				//alert('You have already reviewed and signed all the documents!'); 
				//alert('');
				var modalObj	=	$('#signAllConfirmModal');
				var cBtnObj		=	$('#confirmBtn');
				var HeadMsg		=	'<i class="fa fa-warning"></i>&nbsp;Alert'	;
				cBtnObj.hide(50);
				modalObj.find('.modal-header h4 span ').html(HeadMsg);
				modalObj.find('p').html('All the documents have already been reviewed and signed!');
				modalObj.modal({show:true,backdrop:false});
				return false;
			}
		}
		
	}
//END FUNCTION TO MAKE ALL SING OF A LOGGED-IN SURGEON IN CURRENT CHARTNOTES

//FUNCTION TO MAKE SIGN ALL ANESTHESIA FORM
	var signAnesthesiaCheck = 'true';
	function getAnesthesiaSignAll(patient_id,pConfId,loggedInUserId,chkDisplayAnesthesiaSign,assignedAnesthesiaId,assignedAnesthesiaName,userSubType) {
		
		if(loggedInUserId!=assignedAnesthesiaId) {
			
			var prefix ='Dr.';
			if(userSubType=='CRNA') { prefix =''; }
			/*
			var t = confirmAnesVbScript("This patient is registered to "+prefix+" "+assignedAnesthesiaName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
			if(t==false) {
				signAnesthesiaCheck='false';
			}else {
				signAnesthesiaCheck='true';
			}*/
			
		}
		if(signAnesthesiaCheck=='true') {
			var innerKey = top.mainFrame.main_frmInner.document.getElementById('innerKey').value; 
			if(top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value=='true') {
				
				var modalObj	=	$('#signAllConfirmModal');
				var cBtnObj		=	$('#confirmBtn');
				var HeadMsg		=	'<i class="fa fa-question-circle"></i>&nbsp;Confirmation'	;
				cBtnObj.show(50);
				modalObj.find('.modal-header h4 span ').html(HeadMsg);
				modalObj.find('p').html('Please confirm you have reviewed all the documents!');
				modalObj.modal({show:true,backdrop:false});
				
				
				cBtnObj.click(function(){ 
					top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value = 'false';
					top.mainFrame.main_frmInner.location.href='sign_all_anesthesia.php?patient_id='+patient_id+'&pConfId='+pConfId+'&loggedInUserId='+loggedInUserId+'&innerKey='+innerKey;
					return true;
				});
				
				return false;
				/*
				if(confirm('P')) {
					top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value = 'false';
					//var frmObj = top.mainFrame.main_frmInner.location.href='sign_all_anesthesia.php?patient_id='+patient_id+'&amp;pConfId='+pConfId+'&loggedInUserId='+loggedInUserId;
					top.mainFrame.main_frmInner.location.href='sign_all_anesthesia.php?patient_id='+patient_id+'&pConfId='+pConfId+'&loggedInUserId='+loggedInUserId+'&innerKey='+innerKey;
					return true;
				}else {
					return false;
				}*/
				
			}else if(top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value=='false') {
				var modalObj	=	$('#signAllConfirmModal');
				var cBtnObj		=	$('#confirmBtn');
				var HeadMsg		=	'<i class="fa fa-warning"></i>&nbsp;Alert'	;
				cBtnObj.hide(50);
				modalObj.find('.modal-header h4 span ').html(HeadMsg);
				modalObj.find('p').html('All the documents have already been reviewed and signed!');
				modalObj.modal({show:true,backdrop:false});
				return false;
				
				//alert('You have already reviewed and signed all the documents!');
				//alert('All the documents have already been reviewed and signed!');
				//return false;
			}
		}
		
	}
//END FUNCTION TO MAKE SIGN ALL ANESTHESIA FORM

<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function abc(){
var ultags=document.getElementById('treemenu1').getElementsByTagName("ul");
for(i=0;i<ultags.length;i++)
ddtreemenu.expandSubTree('treemenu1',ultags[i]);
}
//START FUNCTION TO SHOW LOADING IMAGE
var shwHidLoadImg='none';
function show_loading_image(shwHidLoadImg) {
	if(document.getElementById('ajaxLoadId')) {
		document.getElementById('ajaxLoadId').style.display=shwHidLoadImg;
	}
}
//END FUNCTION TO SHOW LOADING IMAGE

function GetXmlHttpObjectChart() {
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
	objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
	objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
}	

//START FUNCTION SAVE OPEN AND CLOSE TIME OF CHART
function chart_log_save(chartVal) {
	var chartLogIdVal = "";
	if(document.getElementById('chartLogId')) {
		chartLogIdVal = document.getElementById('chartLogId').value;
	}
	if(chartLogIdVal) {
		xmlHttp=GetXmlHttpObjectChart();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		} 
		var url="chart_log_ajax.php";
		url=url+"?chartVal="+chartVal;
		url=url+"&chartLogId="+chartLogIdVal;
		/*
		xmlHttp.onreadystatechange=function() {
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
	
			} 
		};*/
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}
//END FUNCTION SAVE OPEN AND CLOSE TIME OF CHART

//-->

		
</script>
<div class="alert alert-success alert-msg " id="alert_success" > <strong>Record(s) Saved Successfully</strong></div>
<div class="alert alert-success alert-msg " id="alert_finalize" > <strong>Chart Finalized Successfully</strong></div>
<!--</head>
<body style="overflow-y:hidden; " topmargin="0" leftmargin="0" rightmargin="0" onLoad="MM_preloadImages('images/save_hover.jpg','images/cancel_hover.jpg','images/print_hover.jpg','images/finalize_hover.jpg','images/save_n_print_hover.jpg','images/finalize_hover.jpg');startTime();">
--><?php
//style='overflow-y:hidden;margin-top:0px; margin-left:0px; margin-right:0px; ' 
$spec_new = "
</head>
<body onLoad='MM_preloadImages(\"images/save_hover.jpg\",\"images/cancel_hover.jpg\",\"images/print_hover.jpg\",\"images/finalize_hover.jpg\",\"images/save_n_print_hover.jpg\",\"images/finalize_hover.jpg\");startTime();' >
";	
	if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
    }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	 

$patient_idNew = $patient_id;
if(!$patient_id || $patient_id=="0") { //FIRST CASE
	$patient_id = $_REQUEST['patient_id'];
}	
if(!$patient_id || $patient_id=="0") { //SECOND CASE
	$patient_id = $_SESSION['patient_id'];
}	

if(($patient_id=="" || $patient_id=="0") &&($pConfId<>"")) { //THIRD CASE 
	$patient_idGetQry = "select * from patientconfirmation where patientConfirmationId = '$pConfId'";
	$patient_idGetRes = imw_query($patient_idGetQry) or die(imw_error());
	$patient_idGetRow = imw_fetch_array($patient_idGetRes);
	$patient_id = $patient_idGetRow['patient_id'];
}


$spec= "</head><body onLoad=\"MM_preloadImages('images/save_hover.jpg','images/cancel_hover.jpg','images/print_hover.jpg','images/finalize_hover.jpg','images/save_n_print_hover.jpg','images/finalize_hover.jpg');abc()\" >
";

include ("common/linkfile.php");
include("no_record.php");
?>

<script>
		var LOD	=	function()
		{
			
			var WW	=	$(window).width();
				
			var WH	=	$(window).height();
			var F1	=	$("#footerBar")	?	$("#footerBar").height()	:	0;
			var F2	=	($("#footer_button_id").css('display') !== 'none')			?	$("#footer_button_id").outerHeight(true)			:	0;
			var F3	=	($("#footer_signall_button_id").css('display') !== 'none')	?	$("#footer_signall_button_id").outerHeight(true)	:	0;
			var F4	=	($("#footer_print_button_id").css('display') !== 'none')	?	$("#footer_print_button_id").outerHeight(true)		:	0;
			//alert(F1 + '--' + F2  + '--' + '--'+ F3 + '--' + F4);
			
			
			var FH		=	F1 + F2 + F3 + F4 ; //(( WW < 768) ? (68+38) : ( ( WW < 479) ? (68*2) : 68 ));
			FH			=	(F2 == 0 && F3 == 0 && F4 == 0) ? FH + 54 : FH
			var HH		=	$("#mainHeader").height();
				
			var AH		=	WH	-	(FH + HH );
			AH			=	(AH < 0 )		?	0	:	AH;
			
			//alert('Window Height : '+WH + '\n Footer Height : ' + FH + '\n Header Height :  ' + HH + '\nAvail Height : '  + AH) 
				
			$("#main_frm").attr({'height': AH, 'width' : WW});
			
			
		};
		
		var setScreen	=	function()
		{
				var SW	=	screen.availWidth;
				var SH	=	screen.availHeight
				var RW	=	parseInt(SW * 0.97) ;
				var RH	=	SH;
				var LP	=	parseInt((SW - RW ) / 2);
				var TP	=	parseInt((SH - RH ) / 2);	
				
				window.moveTo(LP,TP);
				window.resizeTo(RW,RH);
		}
		
		$(window).on('load',function(){
				
				//document.getElementById("footer_button_id").style.display 		= "none";
				//document.getElementById("footer_signall_button_id").style.display	= "none";
				//document.getElementById("footer_print_button_id").style.display 	= "none";
				setScreen();
				LOD();
				
		});
		
		$(window).on('resize',function(){
		
				LOD();
		
		});
</script>

	<!-- Loader -->
    <div class="loader">
    	<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
	</div>
    <!-- Loader-->
    
    <div id="dialogBoxScreen" ></div>
    
    <?PHP
		
		include_once 'unfinalizeHistoryModal.php';
	
	?>
    
<div class="main_wrapper">
	<!-- Middle -->
  	<div class="middle_wrap  margin-top-in-minus margin-bottom-adjustment">
    
    	<div class="container-fluid padding_0">
        	
            <div class="inner_surg_middle slider_slider_margins slider_margins_Adjustment">
            
            	<?PHP	include("common/header.php");	?>
                
                <!--<Div class="bg_bricks">&nbsp;</Div>-->
				
                <div class="border-shadow-bottom scheduler_table_Complete toggle_AGAIN" id="content_toggle1" style="">
                
                <script>
                    //START CODE TO CHANGE THE TITLE OF DOCUMENT
                    var mainpgeTitle = '<?php echo $patientHeaderName;?>'; //FROM common/header.php
                    if(top.document) {top.document.title=mainpgeTitle+' - Surgery Center EMR';}
                    //END CODE TO CHANGE THE TITLE OF DOCUMENT
                </script>

				<?php
	
					$chk = $_GET['chk'] == true ? '?' : '&';
					if(!$frameHref){
						$frameHref = 'blank_mainform.php';
					}else {
						$frameHref = $frameHref.''.$chk.'thisId='.$thisId.'&innerKey='.$innerKey.'&patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;stub_id='.$stub_id.'&amp;ascId='.$ascId.'&amp;SaveForm_alert='.$SaveForm_alert.'&amp;Save_Print_alert='.$Save_Print_alert.'&amp;multiwin='.$multiwin.$consentMultipleIdLink.$consentMultipleAutoIncrId;
					}
					
					//DELETE IMAGE UPLOADED
					$delImg = $_REQUEST['delImg'];
					$objManageData->delRecord('scan_upload_tbl', 'scan_upload_id', $delImg);
			
					if($pConfId=="") {
						$pConfId = $_REQUEST['pConfId'];
					}

					//START CODE TO CREATE LOG WHEN OPENING A CHART 
					$addChartLogQry = "INSERT INTO chart_log SET 
										confirmation_id = '".$pConfId."',
										patient_id 		= '".$patient_id."', 
										operator_type 	= '".$_SESSION['loginUserType']."', 
										operator_id 	= '".$_SESSION['loginUserId']."',
										chart_open_date = '".date("Y-m-d")."',
										chart_open_time = '".date("H:i:s")."'";
					$addChartLogRes = imw_query($addChartLogQry) or die(imw_error());					
					$chartLogId = imw_insert_id();
					//END CODE TO CREATE LOG WHEN OPENING A CHART

					//GET FINALIZE STATUS
						$patientMainFinalizeGetQry = "select * from patientconfirmation where patientConfirmationId = '$pConfId'";
						$patientMainFinalizeGetRes = imw_query($patientMainFinalizeGetQry) or die(imw_error());
						$patientMainFinalizeGetRow = imw_fetch_array($patientMainFinalizeGetRes);
						$patientMainFinalizeStatus = $patientMainFinalizeGetRow['finalize_status'];
						$patientMainAmendment_finalize_status = $patientMainFinalizeGetRow['amendment_finalize_status'];
						
						// Get ASC ID 
						$patientConfirmationAscId = $patientMainFinalizeGetRow['ascId'];
						
						//GET ASSIGNED SURGEON ID OR NAME
							$assignedSurgeonId = $patientMainFinalizeGetRow['surgeonId'];
							$assignedSurgeonName = $patientMainFinalizeGetRow['surgeon_name'];
						//GET ASSIGNED SURGEON ID OR NAME
					
						//GET ASSIGNED Anesthesiologist ID OR NAME
							$assignedAnesthesiaId = $patientMainFinalizeGetRow['anesthesiologist_id'];
							$assignedAnesthesiaName = $patientMainFinalizeGetRow['anesthesiologist_name'];
						//GET ASSIGNED Anesthesiologist ID OR NAME
					
						//START GET ASSIGNED SUB USER TYPE
						$assignedAnesthesiaSubType='';
						if($assignedAnesthesiaId) {
							$assignedAnesthesiaSubTypeQry = "SELECT  * FROM `users` WHERE usersId = '".$assignedAnesthesiaId."'";
							$assignedAnesthesiaSubTypeRes = imw_query($assignedAnesthesiaSubTypeQry) or die(imw_error());
							$assignedAnesthesiaSubTypeNumRow = imw_num_rows($assignedAnesthesiaSubTypeRes);
							if($assignedAnesthesiaSubTypeNumRow>0) {
								$assignedAnesthesiaSubTypeRow = imw_fetch_array($assignedAnesthesiaSubTypeRes);
								$assignedAnesthesiaSubType = $assignedAnesthesiaSubTypeRow['user_sub_type'];
							}
						}	
						//END GET ASSIGNED SUB USER TYPE
					
						$footerDisplay = "none";
						$footerPrintButtonIdDisplay = "inline-block";
						if($patientMainFinalizeStatus!="true") {
							$footerDisplay = "inline-block";
							$footerPrintButtonIdDisplay = "none";
						}
					//END GET FINALIZE STATUS
		
		
					//CHECK ALL SIGN OF SURGEON
						
						$patientMainCatQry = "select pc.patient_primary_procedure_id, pc.prim_proc_is_misc, p.catId as procedure_category_id, pct.isMisc, pct.isInj from patientconfirmation pc join procedures p on pc.patient_primary_procedure_id = p.procedureId Join procedurescategory pct on p.catId = pct.proceduresCategoryId  where pc.patientConfirmationId = '$pConfId' ";
						$patientMainCatRes = imw_query($patientMainCatQry) or die(imw_error());
						$patientMainCatRow = imw_fetch_array($patientMainCatRes);
						$patientMainPrimaryProcedureId = $patientMainCatRow['patient_primary_procedure_id'];
						$patientMainProcedureCatId 	= $patientMainCatRow['procedure_category_id'];
						$patientMainProcedureIsMisc = $patientMainCatRow['prim_proc_is_misc'];
						$adminMainProcedureIsMisc		=	$patientMainCatRow['isMisc'];
						$adminMainProcedureIsInj		=	$patientMainCatRow['isInj'];
						
						// Checking if Procedure is injection 
						if($patientMainProcedureCatId <> '2' )
						{
							if($patientMainProcedureIsMisc == '')
							{
								if($adminMainProcedureIsInj) 				$patientMainProcedureIsMisc	=	$adminMainProcedureIsInj;
								elseif($adminMainProcedureIsMisc) 	$patientMainProcedureIsMisc	=	$adminMainProcedureIsMisc;	
							}
						}
						else
						{
								$patientMainProcedureIsMisc	=	'';	
						}
						// Checking if Procedure is injection 
						
						if($patientMainProcedureCatId == '2')
						{
							$chkSignArr = array('laser_procedure_patient_table','dischargesummarysheet');
						}
						elseif($patientMainProcedureCatId <> '2' && $patientMainProcedureIsMisc)
						{
							//$chkSignArr = array('injection','operativereport','dischargesummarysheet');
							$chkSignArr = array('injection','operativereport','dischargesummarysheet');
						}
						else
						{
							$chkSignArr = array('preopphysicianorders','postopphysicianorders','operativereport', 'dischargesummarysheet');	
						}
						
						$chkDisplaySurgeonSign = 'false';
						foreach($chkSignArr as $chkSignArrTableName)
						{
					
							$signUserId = 'signSurgeon1Id';
					
							if($chkSignArrTableName == "preopphysicianorders" || $chkSignArrTableName == "postopphysicianorders") {
								$signUserconfirmation_id = 'patient_confirmation_id';
							}
							else if($chkSignArrTableName == "laser_procedure_patient_table" || $chkSignArrTableName == "operativereport" || $chkSignArrTableName == "dischargesummarysheet" || $chkSignArrTableName == "injection")
							{
								$signUserconfirmation_id = 'confirmation_id';
							}
							
							$chkSignQry = "SELECT * FROM $chkSignArrTableName WHERE ($signUserId='0' OR $signUserId='') AND $signUserconfirmation_id='".$_REQUEST["pConfId"]."'";
							$chkSignRes = imw_query($chkSignQry) or die(imw_error());
							$chkSignNumRow = imw_num_rows($chkSignRes);
							if($chkSignNumRow>0) {
								$chkDisplaySurgeonSign = 'true';
							}
						}
						
						// Start Check Surgeon Signature in Charts Which are saved once.
						//'history_physicial_clearance',
						$chkUsedFormSignArr	=		array('patient_medication_reconciliation_sheet','transfer_followups');	
						foreach($chkUsedFormSignArr as 	$chkSignArrTableName)
						{
								$signUserId = 'signSurgeon1Id';
								$signUserconfirmation_id = 'confirmation_id';
								
								$chkUsedFormSignQry = "SELECT * FROM $chkSignArrTableName WHERE ($signUserId='0' OR $signUserId='') AND $signUserconfirmation_id='".$_REQUEST["pConfId"]."' AND (form_status = 'not completed' OR form_status = 'completed') ";
								$chkUsedFormSignRes = imw_query($chkUsedFormSignQry) or die(imw_error());
								$chkUsedFormSignNumRow = imw_num_rows($chkUsedFormSignRes);
								if($chkUsedFormSignNumRow>0) {
									$chkDisplaySurgeonSign = 'true';
								}
							
						}
						// End Check Surgeon Signature in Charts Which are saved once.
						
						//CHECK SIGN OF SURGEON IN CONSENT FORMS(EITHER SAVED OR NOT)
						$chkConsentSignQry = "SELECT * FROM consent_multiple_form WHERE (signSurgeon1Activate = 'yes' And (signSurgeon1Id='0' OR signSurgeon1Id='')) AND confirmation_id='".$_REQUEST["pConfId"]."'";
						$chkConsentSignRes = imw_query($chkConsentSignQry) or die($chkConsentSignQry.imw_error());
						$chkConsentSignNumRow = imw_num_rows($chkConsentSignRes);
						if($chkConsentSignNumRow>0) {
							$chkDisplaySurgeonSign = 'true';
						}
						
						//END CHECK SIGN OF SURGEON IN CONSENT FORMS(EITHER SAVED OR NOT)
			
						//CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT
						if($patientMainProcedureCatId <> '2' && !$patientMainProcedureIsMisc)
						{
							$chkOproomSurgeonCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE verifiedbySurgeon='' AND confirmation_id='".$_REQUEST["pConfId"]."'";
							$chkOproomSurgeonCheckMarkRes = imw_query($chkOproomSurgeonCheckMarkQry) or die(imw_error());
							$chkOproomSurgeonCheckMarkNumRow = imw_num_rows($chkOproomSurgeonCheckMarkRes);
							if($chkOproomSurgeonCheckMarkNumRow>0) {
								$chkDisplaySurgeonSign = 'true';
							}
						}
						
						//END CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT
			
					//END CHECK ALL SIGN OF SURGEON
		
				
				
					//CHECK ALL SIGN OF ANESTHESIOLOGIST
			
						$chkDisplayAnesthesiaSign = 'false';
						
						//START CHECK RECORD EXIST FOR LOCALANESTHESIA
						$chkAnesthesiaRecordQry = "SELECT * FROM localanesthesiarecord WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
						$chkAnesthesiaRecordRes = imw_query($chkAnesthesiaRecordQry) or die(imw_error());
						$chkAnesthesiaRecordNumRow = imw_num_rows($chkAnesthesiaRecordRes);
						if($chkAnesthesiaRecordNumRow==0){ 
							$chkDisplayAnesthesiaSign = 'true';
						}
						//END CHECK RECORD EXIST FOR LOCAL ANESTHESIA
						if($chkAnesthesiaRecordNumRow>0)
						{	
							//START CHECK SIGN EXIST FOR LOCAL ANESTHESIA 	
							if($chkAnesthesiaRecordNumRow>0){
								//OR signAnesthesia3Id=''
								$chkAnesthesiaSignQry = "SELECT * FROM localanesthesiarecord WHERE (signAnesthesia1Id='0' OR signAnesthesia1Id='' OR signAnesthesia2Id='0' OR signAnesthesia2Id='' OR signAnesthesia3Id='0' OR ((signAnesthesia4Id='0' OR signAnesthesia4Id='') && (version_num = 2 OR (version_num = 0 And form_status = '') ))) AND confirmation_id='".$_REQUEST["pConfId"]."'";
								$chkAnesthesiaSignRes = imw_query($chkAnesthesiaSignQry) or die(imw_error());
								$chkAnesthesiaSignNumRow = imw_num_rows($chkAnesthesiaSignRes);
								if($chkAnesthesiaSignNumRow>0) {
									$chkDisplayAnesthesiaSign = 'true';
								}
							}
							//END CHECK SIGN EXIST FOR LOCAL ANESTHESIA 
						}
						
						//START CHECK RECORD EXIST FOR OPERATING ROOM 
						$chkOproomAnesthesiaRecordQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
						$chkOproomAnesthesiaRecordRes = imw_query($chkOproomAnesthesiaRecordQry) or die(imw_error());
						$chkOproomAnesthesiaRecordNumRow = imw_num_rows($chkOproomAnesthesiaRecordRes);
						//END CHECK RECORD EXIST FOR OPERATING ROOM 
				
				
						if($chkOproomAnesthesiaRecordNumRow==0){
							$chkDisplayAnesthesiaSign = 'true';
						}
				
				
						if($chkOproomAnesthesiaRecordNumRow>0)
						{
							
							//CHECK IF SURGEON VERIFIED THE OPERATING ROOM RECORD OR NOT
							$chkOproomAnesthesiaCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE verifiedbyAnesthesiologist='' AND confirmation_id='".$_REQUEST["pConfId"]."'";
							$chkOproomAnesthesiaCheckMarkRes = imw_query($chkOproomAnesthesiaCheckMarkQry) or die(imw_error());
							$chkOproomAnesthesiaCheckMarkNumRow = imw_num_rows($chkOproomAnesthesiaCheckMarkRes);
							if($chkOproomAnesthesiaCheckMarkNumRow>0) {
								$chkDisplayAnesthesiaSign = 'true';
							}
							//END CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT
						}
					
					//END CHECK ALL SIGN OF ANESTHESIOLOGIST
		
					//GET LOGGED IN USER TYPE 
						$LoggedInUserDetails = $objManageData->getRowRecord('users', 'usersId', $loginUser);
						$user_type = $LoggedInUserDetails->user_type;
						//$user_sub_type = $LoggedInUserDetails->user_sub_type;
					
						$surgeonSignAllDisplay = "none";
						if($user_type=='Surgeon' && $patient_conf_status==1 && $practiceNameMatch <> 'yes' && $patientConfirmationAscId > 0) {
							$surgeonSignAllDisplay = "inline-block";
						}
					
						$anesthesiaSignAllDisplay = "none";
						if($user_type=='Anesthesiologist' && $patient_conf_status==1) {
							$anesthesiaSignAllDisplay = "inline-block";
							//$anesthesiaSignAllDisplay = "none";  //JUST FOR THE TIME IT IS HIDDEN
						}
					
					//END GET LOOGED IN USER TYPE
			
					//Check for is patient has primary procedure as laser procedure
						$detailConfirmation_laser_procedure = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
						$laserprocedure_Id_get = $detailConfirmation_laser_procedure->patient_primary_procedure_id;
						$laser_procedure_check = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '$laserprocedure_Id_get'";
						$qry_laser_procedure_check = imw_query($laser_procedure_check);
						$laser_procedure_check_tblNumRow = imw_num_rows($qry_laser_procedure_check);
					//Check for is patient has primary procedure as laser procedure
		
					//SET PRIVILIGES TO EDIT/SAVE FORM
						$accessDeniedFn = "return accessDeniedFn();";
						$accessSaveDeniedFn = "return accessSaveDeniedFn();";
		
				?>

				<script>
                    //FUNCTIONS USED IN (finalize_form.php AND js/moocheck.js)
                    function myTimerSurgeonSignAll(){
                        <?php
                        echo "getSignAll('".$patient_id."','".$_REQUEST['pConfId']."','".$loginUser."',document.getElementById('hidd_chkDisplaySurgeonSign').value,'".$assignedSurgeonId."','".addslashes($assignedSurgeonName)."');";
                        ?>
                    }
                    
                    function myTimerAnesSignAll(){
                        <?php
						echo "getAnesthesiaSignAll('".$patient_id."','".$_REQUEST['pConfId']."','".$loginUser."',document.getElementById('hidd_chkDisplayAnesthesiaSign').value,'".$assignedAnesthesiaId."','".addslashes($assignedAnesthesiaName)."','".$assignedAnesthesiaSubType."');";
                        ?>
                    }
                    
                    //FUNCTIONS USED IN (finalize_form.php AND js/moocheck.js)
                    function svNdPr() { 
                        var frmObj = top.frames[0].frames[0].document.forms[0];
                        if(frmObj.hiddSaveAndPrintId && typeof(frmObj.hiddSaveAndPrintId)!='undefined') {
                            frmObj.hiddSaveAndPrintId.value='yes';
                            return getFormSave();
                        }else {
                            <?php
                                echo "return getFormSave(),sav_print_pdf('".$_REQUEST['pConfId']."','".$get_http_path."');";
                            ?>		
                        }
                        
                    }
                </script>
                    
                    
                    
				<?php
					$saveFormMainFn = "return getFormSave();";
                    $cancelFormMainFn = "return getFormCancel();";
                    $printFormMainFn = "sav_print_pdf('".$_REQUEST['pConfId']."','".$get_http_path."');";
                    //$saveAndPrintFormMainFn = "return getFormSave(),sav_print_pdf('".$_REQUEST['pConfId']."','".$get_http_path."');";
                    $saveAndPrintFormMainFn = "return svNdPr();";
                    $printEmrFormMainFn = "emr_print('".$_REQUEST['pConfId']."');";
                    $amendmentFinalizeFormMainFn = "return finalizeAmendment();";
                    $printMedsFn = "print_meds('".$_REQUEST['pConfId']."','".$get_http_path."');";

                    //SIGN ALL FUNCTION IS CALL IN finalize_form.php FILE AS (myTimerSurgeonSignAll() AND myTimerAnesSignAll())
                    $getSignAll = "document.getElementById('hidd_SaveOnSingAllSurgeon').value='true'; getFormSave();";
                    $getAnesthesiaSignAll = "document.getElementById('hidd_SaveOnSingAllAnes').value='true'; getFormSave();";
                    //SIGN ALL FUNCTION IS CALL IN finalize_form.php FILE AS (myTimerSurgeonSignAll() AND myTimerAnesSignAll())

										//
                    if($patientMainProcedureCatId == '2' ) {
                        $finalizeFormMainFn = "return finalize_laser_procedure();";
                    }
										elseif($patientMainProcedureCatId <> '2' && $patientMainProcedureIsMisc)
										{
                        $finalizeFormMainFn = "return finalize_injection_procedure();";
                    }
                    else {
                        $finalizeFormMainFn = "return finalize();";
                    }
                    //PURGE
                    $purgeFormMainFn = "return getFormPurge();";
                    //PURGE
                    
                    //RESET
                    $resetFormMainFn = "return getFormReset();";
                    //RESET

                    //IF USER HAS ONLY STAFF PRIVILLIGES THEN
                    if($privileges == "Staff") {
                        $saveFormMainFn = $accessDeniedFn;
                        $cancelFormMainFn = $accessDeniedFn;
                        //$printFormMainFn = $accessDeniedFn;
                        $saveAndPrintFormMainFn = $accessDeniedFn;
                        $printEmrFormMainFn = $accessDeniedFn;
                        $finalizeFormMainFn = $accessDeniedFn;
                        $amendmentFinalizeFormMainFn = $accessDeniedFn;
                        $getSignAll = $accessDeniedFn;
                        $getAnesthesiaSignAll = $accessDeniedFn;
                    }
                    //END IF USER HAS ONLY STAFF PRIVILLIGES THEN

                    $privilegesArr = explode(', ',$privileges);
                        //IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES THEN
                        if(is_array($privilegesArr)) {
                            if(($privilegesArr[0]=='Staff' || $privilegesArr[0]=='Billing') && ($privilegesArr[1]=='Billing' || $privilegesArr[1]=='Staff')) {
                                $saveFormMainFn = $accessSaveDeniedFn;
                                $cancelFormMainFn = $accessDeniedFn;
                                //$printFormMainFn = $accessDeniedFn;
                                $saveAndPrintFormMainFn = $accessDeniedFn;
                                $printEmrFormMainFn = $accessDeniedFn;
                                $finalizeFormMainFn = $accessDeniedFn;
                                $amendmentFinalizeFormMainFn = $accessDeniedFn;
                                $getSignAll = $accessDeniedFn;
                                $getAnesthesiaSignAll = $accessDeniedFn;
                            }
                        }
                        //IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES THEN
    
                //END SET PRIVILIGES TO EDIT/SAVE FORM
                
                $prdtVrsnDt = 'Ver R5.0  Jan 03, 2013';
                if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }

                ?>
                                
                <?php
                        $mainFrmScroll='no';
                        if($browserPlatform == "iPad") { $mainFrmScroll='yes'; }
                ?>
                                             
               	<iframe name="mainFrame" id="main_frm" style=" width:100%;" frameborder="0" src="blankform.php?pConfId=<?php echo $pConfId; ?>&amp;patient_id=<?php echo $patient_id; ?>&amp;stub_id=<?php echo $stub_id; ?>&amp;frameHref=<?php echo $frameHrefTempr;?>&amp;thisId=<?php echo $thisId;?>&amp;innerKey=<?php echo $innerKey;?>&amp;multiwin=<?php echo $multiwin;?><?php echo $consentMultipleIdLink.$consentMultipleAutoIncrId;?>&amp;myPage=<?php echo $myPage;?>" scrolling="<?php echo $mainFrmScroll;?>"></iframe>				 
                 
                
                </div>
                	
                <Div class="btn-footer-slider" id="footer_button_id" style=" display:<?php echo $footerDisplay;?>;">
                 		
                       	<a id="Finalized" class="btn btn-info" style="float:left; margin-left:50px; " onClick="<?php echo $finalizeFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-th"></b>&nbsp;Finalize</a>
                        <button type="button" id="saveBtn" class="btn btn-success" onClick="<?php echo $saveFormMainFn;?> "><b class="fa fa-save"></b>&nbsp;Save</button>
                        <a id="CancelBtn" class="btn btn-danger" onClick="<?php echo $cancelFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-times"></b>&nbsp;Cancel</a>
                        <a id="PrintBtn" class="btn btn-info" onClick="<?php echo $printFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-print"></b>&nbsp;Print</a>
                        <a id="SavePrintBtn" class="btn btn-success" onClick="<?php echo $saveAndPrintFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-save"></b>&nbsp;<b class="fa fa-print"></b>&nbsp;Save & Print</a>
                        
                        <a id="PrintEmrBtn" class="btn btn-info" onClick="<?php echo $printEmrFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-th"></b>&nbsp;Print EMR</a>
                        <a id="PurgeBtn" class="btn btn-warning" onClick="<?php echo $purgeFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-th"></b>&nbsp;Purge</a>
                        <a id="ResetBtn" class="btn btn-danger" onClick="<?php echo $resetFormMainFn;?>" href="javascript:void(0)"><b class="fa fa-th"></b>&nbsp;Reset</a>
                        
                        <a id="AmendmentFinalized" class="btn btn-info" onClick="<?php echo $amendmentFinalizeFormMainFn;?>" href="javascript:void(0)" style="display:none;"><b class="fa fa-th"></b>&nbsp;Amendment Finalize</a>
                        <a id="SignAllBtn" class="btn btn-info" onClick="document.getElementById('confirmType').value='';<?php echo $getSignAll;?>" href="javascript:void(0)" style="display:<?php echo $surgeonSignAllDisplay;?>; "><b class="fa fa-th"></b>&nbsp;Sign All By Surgeon</a>
                        <a id="SignAllAnesthesiaBtn" class="btn btn-success" onClick="<?php echo $getAnesthesiaSignAll;?>" href="javascript:void(0)" style="display:<?php echo $anesthesiaSignAllDisplay;?>; "><b class="fa fa-th"></b>&nbsp;Sign All By Anesthesia Provider</a>
                        <a id="PrintMedsBtn" class="btn btn-info" onClick="<?php echo $printMedsFn;?>" href="javascript:void(0)"><b class="fa fa-print"></b>&nbsp;Print Medications</a>
                         
				</Div>
                
                <Div class="btn-footer-slider" id="footer_signall_button_id" style=" display:<?php echo $surgeonSignAllDisplay;?>; height:35px;">
                	<a id="SignAllBtnNew" class="btn btn-success" onClick="<?php echo $getSignAll;?>" href="javascript:void(0)" style="display:<?php echo $surgeonSignAllDisplay;?>; "><b class="fa fa-th"></b>&nbsp;Sign All By Surgeon</a>
                </Div>
                
                
                <Div class="btn-footer-slider" id="footer_print_button_id" style=" display:<?php echo $footerPrintButtonIdDisplay;?>;">
                	<a id="PrintBtnNew" class="btn btn-info" onClick="<?php echo $printFormMainFn;?>" href="javascript:void(0)" ><b class="fa fa-print"></b>&nbsp;Print</a>
                    <a id="PrintMedsBtnNew" class="btn btn-info" onClick="<?php echo $printMedsFn;?>" href="javascript:void(0)" ><b class="fa fa-print"></b>&nbsp;Print Medications</a>
                </Div>
			
            
            </div>	  <!-- inner_surg_middle  -->
				 
		</div>	<!-- Container -->
        
	</div> <!-- Middle Wrap -->
    
    
    <div class="footer_wrap navbar navbar-default navbar-fixed-bottom min-height-auto" id="footerBar">
        <div class="container">
            <span class="footer_span">Copyrights &copy; 2007 - <?php echo substr($prdtVrsnDt,-4,4);?>. imwemr &reg; All rights reserved. <a class="version"><?php echo $prdtVrsnDt;?></a></span>
           <!-- <p class="footer_span"> <a href="javascript:void(0)"> Copyright Notice </a> <span class="to_hide_319">|</span>
                     <a href="javascript:void(0)"> Our Privacy Statement </a> 
            </p>-->
        </div>
    </div>    
    
    
    <form name="frmMainPage" action="mainpage.php?patient_id=<?php echo $patient_id;?>&amp;pConfId=<?php echo $pConfId;?>&amp;ascId=<?php echo $ascId;?>&amp;SaveForm_alert=<?php echo $SaveForm_alert;?>&amp;Save_Print_alert=<?php echo $Save_Print_alert;?>" method="post">
        <input type="hidden" name="frameHref" id="frameHref" value="">	
        <input type="hidden" name="patient_id" value="">
        <input type="hidden" name="pConfId" value="">
        <input type="hidden" name="preOpHealthQuesId" id="preOpHealthQuesId" value="">
        <input type="hidden" name="ascId" value="">
        <input type="hidden" name="thisId" id="thisId" value="">
        <input type="hidden" name="innerKey" id="innerKey" value="">
        <input type="hidden" name="preColor" id="preColor" value="">
        <input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="">
        <input type="hidden" name="Save_Print_alert"  id="Save_Print_alert" value="">
        <input type="hidden" name="delImg" value="">
        <input type="hidden" name="multiwin" value="">
        <input type="hidden" name="consentMultipleId" value="">
        <input type="hidden" name="hidd_chkDisplaySurgeonSign" id="hidd_chkDisplaySurgeonSign" value="<?php echo $chkDisplaySurgeonSign;?>">
        <input type="hidden" name="hidd_chkDisplayAnesthesiaSign" id="hidd_chkDisplayAnesthesiaSign" value="<?php echo $chkDisplayAnesthesiaSign;?>">
        <input type="hidden" name="hidd_SaveOnSingAllSurgeon" id="hidd_SaveOnSingAllSurgeon" value="">
        <input type="hidden" name="hidd_SaveOnSingAllAnes" id="hidd_SaveOnSingAllAnes" value="">
        <input type="hidden" name="consentMultipleAutoIncrId" value="">
        <input type="hidden" name="hiddScrollTop" id="hiddScrollTop" value="">
        <input type="hidden" name="browserPlatform" id="browserPlatform" value="<?php echo $browserPlatform;?>"/>
        <input type="hidden" name="chartLogId" id="chartLogId" value="<?php echo $chartLogId;?>">
        <input type="hidden" id="confirmType" value="" />
        <input type="hidden" name="show_military_time" id="show_military_time" value="<?php echo constant('SHOW_MILITARY_TIME');?>">
        <input type="hidden" name="finalize_without_mandatory_charts" id="finalize_without_mandatory_charts" value="<?php echo constant('FINALIZE_WITHOUT_MANDATORY_CHARTS');?>">
        
    </form>
</div> <!-- Main Wrapper -->

<?php include 'discharge_summary_sheet_review.php'; ?>
<?php include 'release_note.php';?>
<script>
	show_loading_image('none');
	
	$(document).ready(function() {
			
			
			$('.version').click(function(){
				var modalObj	=	$('#releaseNoteModal');
				modalObj.modal({show:true,backdrop:false});	
			});
		
			$('#patient_form_m').click(function(){
			
				$('#patient_form').modal({show:true,backdrop:false});
				$('#pt_ocular_sx_hx').modal('hide');
				$('#unfinalizeHistoryModal').modal('hide');
			
			});
			
			$('#pt_ocular_sx_hx_m').click(function(){
			
				$('#pt_ocular_sx_hx').modal({show:true,backdrop:false});
				$('#patient_form').modal('hide');
				$('#unfinalizeHistoryModal').modal('hide');
			
			});
			
			$(document).on('click','#toggle_btn1',function(event){
				 event.stopPropagation();
				$('.toggled_1').toggleClass('toggle_AGAIN').first().stop().delay('slow');
			});
			
			$(document).on('click','#toggle_btn3',function(){
				$(this).hide('fast');
				$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
			});	
			
			$(document).on('click','#toggle_btn2',function(){ 
				$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
				$('#toggle_btn3').show('fast');
			});	
			
			
			$(document).on("click","#left ul.nav li.parent > a ", function(){          
				$(this).find('i:first').toggleClass("fa-minus");      
			}); 
			
			$('#left li.item-1 a').on('click',function(){
				$(this).parent().children('ul').first().stop(true, true).slideToggle();
			});
			
			//The actual plugin
			$.fn.outsideClick = function(callback) {
				var subject = this;
				$(document).click(function(event) {
				
					if(!$(event.target).closest(subject).length) {
						callback.call(subject);
					}
				});
				
				return this;
			};


			$("#sidebar-wrapper,#patient_form_m,#patient_form,#unfinalizeHistoryModal,#pt_ocular_sx_hx_m,#pt_ocular_sx_hx").outsideClick(function()
			{
				if($("#sidebar-wrapper").hasClass('toggle_AGAIN'))
				{
					$("#toggle_btn1").trigger('click');
				}
				
				$('#patient_form,#unfinalizeHistoryModal,#pt_ocular_sx_hx').modal('hide');
				
				
			});
			
			$("#post").outsideClick(function()
			{
				$(this).html('').hide();
				
			});
			
			
						
			// Open Le current menu
			//$("#left ul.nav li.parent.active > a > span.sign").find('i:first').addClass("fa-minus");
			//$("#left ul.nav li.current").parents('ul.children').addClass("in");
						
		});
	
								
			//$('[data-toggle="tooltip"]').tooltip()
			
			
</script>
<script>
	$(function(){
		$('body').on('click','.ajaxDir',function(){
			$.ajax({
				url : 'getDirList.php',
				type:'post',
				data :{ 'cid':$(this).attr('data-confirmation-id'), 'dirName':encodeURI($(this).text())},
				dataType:'json',
				success:function(data){
					for(var i = 0; i < data.length; i++)
					{
						top.openImage(data[i].scanUploadId,data[i].imageType,data[i].pdfFilePath)	
					}
				},
				
				
			});
		});
	});
	window.onbeforeunload = function() {
		chart_log_save("chart_close");
		chart_log_del();	
	}
	
</script>


<div class="panel panel-default bg_panel_sum" id="confirmDialogueBox" style="width:250px; position: absolute; top:0;  left:0; right:0; margin:auto; display:none; background:white; border-color:#DDD; z-index:99; ">
		
        <div class=" haed_p_clickable " style="background-color:#d9534f; min-height:25px;">
        		<h3 class="panel-title rob" style="padding:5px; color:#FFF; font-weight:600;">Alert</h3>
      	</div><!--panel-body-->
        
        <div class=" text-center" style=" width:100%; border-collapse:collapse; padding:10px; font-weight:600;">
        		
                	Are you sure to delete. ? <br /><br />
                
                	<a class="btn btn-info" id="confirmYes"  href="javascript:void(0)">Yes </a>
                	<a class="btn btn-danger" id="confirmNo" href="javascript:void(0)"> No </a>
    	</div>
</div>


</body>
</html>