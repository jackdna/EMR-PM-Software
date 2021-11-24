<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
include_once(dirname(__FILE__)."/../../library/classes/print_pt_key.php");
$library_path = $GLOBALS['webroot'].'/library';
$pg_title = 'Consult Letter';
$blClientBrowserIpad = false;
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$blClientBrowserIpad = true;
}

$consentScroll=' scrolling="no" ';
if($blClientBrowserIpad == true){
	$consentScroll = ' scrolling="yes" ';	
}
//-----  Get data from remote server -------------------

$zRemotePageName = "consult_letter_page";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");

//-----  Get data from remote server -------------------

require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");

//$objManageData = new ManageData;
$obj_print_pt_key=new print_pt_key;

$OBJCommonFunction = new CLSCommonFunction;

//$queryEmailCheck=imw_query("select g.* from groups_new as g join users ON(g.gro_id=users.default_group and g.del_status='0') where users.id='$_SESSION[authId]'")or die(imw_error());
$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(imw_error());
if(imw_num_rows($queryEmailCheck)>=1)
{
	$dEmailCheck=imw_fetch_object($queryEmailCheck);
	$groupEmailConfig['email']=$dEmailCheck->config_email;
	$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
	$groupEmailConfig['host']=$dEmailCheck->config_host;
	$groupEmailConfig['header']=$dEmailCheck->config_header;
	$groupEmailConfig['footer']=$dEmailCheck->config_footer;
	$groupEmailConfig['port']=$dEmailCheck->config_port;
}
imw_free_result($queryEmailCheck);
//$queryEmailCheck.close;
	
//--- Get Patient Name ------
$patient_id = $_SESSION['patient'];
$qry = "select id,lname,fname,mname,erx_patient_id 
		from patient_data where id = '$patient_id'";
$qryRes = get_array_records_query($qry);
$erx_patient_id = $qryRes[0]['erx_patient_id'];
$patName = $qryRes[0]['lname'].', ';
$patName .= $qryRes[0]['fname'].' ';
$patName .= $qryRes[0]['mname'];
$patName = ucwords(trim($patName));
if($patName[0] == ','){
	$patName = substr($patName,1);
}
$patName .= ' - '.$qryRes[0]['id'];
//---- Start Get the Reffer Physician for type ahead ------	
	$refPhyXMLFileExits = false;
	$refPhyXMLFile = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/Referring_Physicians.xml";
	if(file_exists($refPhyXMLFile)){
		$refPhyXMLFileExits = true;
	}
	else{
		$OBJCommonFunction -> create_ref_phy_xml();	
		if(file_exists($refPhyXMLFile)){
			$refPhyXMLFileExits = true;	
		}	
	}
	$refPhyFaxArr= array(); $refPhyAll = array(); $refPhyIdAll = array();
	$stringAllPhy = '';
	$stringAllPhyId = '';
	if(count($refPhyAll)>0){
		$stringAllPhy=implode(',',$refPhyAll);
		$stringAllPhyId=implode(',',$refPhyIdAll);
	}	
	
//-- End-------	
$getPCIP=$_SESSION["authId"];			
$getPCIP=str_ireplace("::","_",$getPCIP);
$getIP=str_ireplace(".","_",$getPCIP);
$setNameFaxPDF="savedFax_".$getIP;
?>
<!DOCTYPE html>
<html>
<head>
<title>Consult Letter</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/style.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>


<script>
    var REF_PHY_FORMAT = '<?php $GLOBALS['REF_PHY_FORMAT'];?>';
	var phone_min_length = '<?php echo interPhoneMinLength() ?>';
</script>
<script type="text/javascript">
	var arrRefIdNameFax=new Array(); //array to get phy fax for "Other Ref Phy(Last, First Name)" text field
	var arrRefIdNameEmail=new Array(); //array to get phy email for "Other Ref Phy(Last, First Name)" text field
	
	function show_consult_fax_div() {
		top.show_loading_image("show", "150", "");
		var pat_temp_id = $('#pat_temp_id').val();
		// Condition is commemnted, because it's conflicting with existing data on popup load.
		// if(document.getElementById("send_fax_number")) {
		// 	document.getElementById("send_fax_number").value = '';	
		// }
		if(document.getElementById("send_fax_name")) {
			document.getElementById("send_fax_name").value = '';
		}
		$('#modal_title').html("Send Fax - Consult Letter");
		top.fmain.document.getElementById("consult_data_id").src = "sendsavedfax.php?show_fax_popup=yes&pat_temp_id="+pat_temp_id;
	}
	function show_fax_log() {
		var wo=top.JS_WEB_ROOT_PATH;
		var ht = '<?php echo ($_SESSION['wn_height']-250);?>';
		var wdt = jQuery(window).width();
		wdt = parseInt(wdt/1.3);
		window.open(wo+'/interface/chart_notes/send_fax_log.php', 'fax_log', 'width='+wdt+',height='+ht+',scrollbars=no,resizable=no');	
	}
	
	function sendSavedFax(){
		var getFaxNo=$('#send_fax_number').val();
		var getFaxRecipentName=$('#selectReferringPhy').val();
		var getFaxName=$('#sendSaveFaxName').val();
		var pat_template_id=$("#pat_temp_id").val()
		var ref_phy_id=$("#hiddselectReferringPhy").val();
		if($('#send_fax_number').val()==''){
			var msgFax = 'Please enter fax number for Referring Physician';
			if(typeof(top.fAlert)!="undefined") {
				top.fAlert(msgFax);
			}else if(typeof(fancyAlert)!="undefined") {
				alert(msgFax);
			}			
			$('#send_fax_number').focus();
			return false;
		}else{
				//$('#div_load_image').show();
				top.show_loading_image("show", "150", "");
				
				getFaxNo = getFaxNo.replace(/[^0-9+]/g,"");
				
				faxNoCc1 = $('#send_fax_numberCc1').val();
				faxNoCc1 = faxNoCc1.replace(/[^0-9+]/g,"");
				
				faxNoCc2 = $('#send_fax_numberCc2').val();
				faxNoCc2 = faxNoCc2.replace(/[^0-9+]/g,"");
				
				faxNoCc3 = $('#send_fax_numberCc3').val();
				faxNoCc3 = faxNoCc3.replace(/[^0-9+]/g,"");
				
				
				url_hold_sig = "sendsavedfax.php?send_fax=yes&txtFaxName="+getFaxRecipentName+"&txtFaxNo="+getFaxNo+"&txtFaxPdfName="+getFaxName+"&txtFaxNameCc1="+$('#selectReferringPhyCc1').val()+"&txtFaxNoCc1="+faxNoCc1+"&txtFaxNameCc2="+$('#selectReferringPhyCc2').val()+"&txtFaxNoCc2="+faxNoCc2+"&txtFaxNameCc3="+$('#selectReferringPhyCc3').val()+"&txtFaxNoCc3="+faxNoCc3+"&pat_temp_id="+pat_template_id+"&ref_phy_id="+ref_phy_id;;
				$.ajax({
					type: "POST",
					url: url_hold_sig,
					success: function(resp){
						
						resp = $.trim(resp);
						resp = $.parseJSON(resp);
						var alertSuccess = '';
						var alertError = '';
						//=================UPDOX FAX WORKS GET DATA FROM SENDFAX.PHP FILE=================
						
						//PRIMARY RECIPENT DATA
						if( typeof(resp.primary.fax_id) !== 'undefined' )
							alertSuccess += 'Primary: '+resp.primary.fax_id+'<br>';
						else if( typeof(resp.primary.error) !== 'undefined' ) alertError += 'primary: '+resp.primary.error+'<br>';
						
						//CC1 RECIPENT DATA
						if( typeof(resp.cc1.fax_id) !== 'undefined' )
							alertSuccess += 'CC1: '+resp.cc1.fax_id+'<br>';
						else if( typeof(resp.cc1.error) !== 'undefined' ) alertError += 'CC1: '+resp.cc1.error+'<br>';
						
						//CC2 RECIPENT DATA
						if( typeof(resp.cc2.fax_id) !== 'undefined' )
							alertSuccess += 'CC2: '+resp.cc2.fax_id+'<br>';
						else if( typeof(resp.cc2.error) !== 'undefined' ) alertError += 'CC2: '+resp.cc2.error+'<br>';
						
						//CC3 RECIPENT DATA	
						if( typeof(resp.cc3.fax_id) !== 'undefined' )
							alertSuccess += 'CC3: '+resp.cc3.fax_id+'<br>';
						else if( typeof(resp.cc3.error) !== 'undefined' ) alertError += 'CC3: '+resp.cc3.error+'<br>';
						
						var alertMsg = '';
						if(alertSuccess!=='')
							alertMsg += 'Fax sent successfully:  <br>'+alertSuccess+'<br>';  //<br />'+alertSuccess+"<br />"
						if(alertError!=='')
							alertMsg += 'Fax sending Failed: <br>'+alertError+'<br>';   // <br />'+alertError+"<br />"
						
						top.show_loading_image("hide");
						if(typeof(top.fAlert)!="undefined") {
							top.fAlert(alertMsg);
						}else if(typeof(fancyAlert)!="undefined") {
							alert(alertMsg);
						}
						
						if(alertSuccess!=''){
							$(".btn-danger").click();
						}
					}
				});
			}
		}
		
	function sendSavedEmail(){
		var getEmailId=$('#send_email_id').val();
		var getEmailName=$('#sendSaveEmailName').val();
		var pat_template_id=$("#pat_temp_id").val()
		var ref_phy_id=$("#hiddselectReferringPhy").val();
		if($('#send_email_id').val()==''){
			alert('Please enter email id for Referring Physician');
			$('#send_email_id').focus();
			return false;
		}else{
				$('#div_load_image_e').show();
				url_hold_sig = "sendsavedemail.php?txtEmailId="+getEmailId+"&txtEmailPdfName="+getEmailName+"&txtEmailIdCc1="+$('#send_email_idCc1').val()+"&txtEmailIdCc2="+$('#send_email_idCc2').val()+"&txtEmailIdCc3="+$('#send_email_idCc3').val()+"&pat_temp_id="+pat_template_id+"&ref_phy_id="+ref_phy_id;
				$.ajax({
					type: "POST",
					url: url_hold_sig,
					success: function(r){
						$('#div_load_image_e').hide();
						alert(r.trim());
						if(r.trim()=="Mail sent successfully"){
							$("#send_email_div").hide();
							$("#consult_tree_id").attr("src","tree4consult_letter.php");
						}
					}
				});
			}
		}
		
	function setFaxNumber(faxObj,Cc1Obj,Cc2Obj,Cc3Obj){
		var faxName = faxNo = ccName = ccFax = "";
		if(faxObj.value!=0 && faxObj.value!='') {
			var faxDetail = arrRefIdNameFax[faxObj.value];
			var faxDetailArr = new Array();
			if(faxDetail!="" && faxDetail!="-" && faxDetail!="undefined"){
				faxDetailArr= faxDetail.split("@@");
				faxName 	= faxDetailArr[0];
				faxNo 		= faxDetailArr[1];
				document.getElementById("send_fax_number").value=faxNo;	
			}
		}
		for(var i=1;i<=3;i++) {
			if(i==1) 		{ CcObj = Cc1Obj;
			}else if(i==2) 	{ CcObj = Cc2Obj;
			}else if(i==3) 	{ CcObj = Cc3Obj;
			}
			if(CcObj.value!=0 && CcObj.value!='') {
				var ccDetail = arrRefIdNameFax[CcObj.value];
				var ccDetailArr = new Array();
				if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
					ccDetailArr = ccDetail.split("@@");
					ccName 		= ccDetailArr[0];
					ccFax 		= ccDetailArr[1];
					document.getElementById("send_fax_numberCc"+i).value=ccFax;	
				}
			}
		}
			
	}
	
	function setEmailId(emailObj,Cc1Obj,Cc2Obj,Cc3Obj){
		var emailName = emailNo = ccName = ccEmail = "";
		if(emailObj.value!=0 && emailObj.value!='') {
			var emailDetail = arrRefIdNameEmail[emailObj.value];
			var emailDetailArr = new Array();
			if(emailDetail!="" && emailDetail!="-" && emailDetail!="undefined"){
				emailDetailArr= emailDetail.split("@@");
				emailName 	= emailDetailArr[0];
				emailNo 	= emailDetailArr[1];
				document.getElementById("send_email_id").value=emailNo;	
			}
		}
		for(var i=1;i<=3;i++) {
			if(i==1) 		{ CcObj = Cc1Obj;
			}else if(i==2) 	{ CcObj = Cc2Obj;
			}else if(i==3) 	{ CcObj = Cc3Obj;
			}
			if(CcObj.value!=0 && CcObj.value!='') {
				var ccDetail = arrRefIdNameEmail[CcObj.value];
				var ccDetailArr = new Array();
				if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
					ccDetailArr = ccDetail.split("@@");
					ccName 		= ccDetailArr[0];
					ccEmail 	= ccDetailArr[1];
					document.getElementById("send_email_idCc"+i).value=ccFax;	
				}
			}
		}
			
	}

	/**
	 * Function copied form R7 and modified as per the requirement.
	 * Reason: Need this function because it's called from the templatepri.php to load the existing ref.phy as per the previous fax records.
	 */
	function loadFaxRefPhy(RefId,Cc1RefId,Cc2RefId,Cc3RefId,preferred_fax,objFrame){
		var refName = refFax = ccName = ccFax = "";
		var globalPhnFormat = '<?php echo $GLOBALS['phone_format']; ?>';
		objFrame.document.getElementById("selectReferringPhy").value="";
		objFrame.document.getElementById("send_fax_number").value="";

		if(RefId) {
			//var refDetail = arrRefIdNameFax[RefId];
			var refDetailArr = new Array();
			refDetailArr= RefId.split("@@");
			refName 	= refDetailArr[0];
			refFax 		= refDetailArr[1];
			objFrame.document.getElementById("selectReferringPhy").value=refName;
			objFrame.document.getElementById("send_fax_number").value=refFax;	
			if(objFrame.document.getElementById("send_fax_number").value!=''){
				set_phone_format(document.getElementById("send_fax_number"), globalPhnFormat);
			}
		}
		if(preferred_fax){
			var refPerDetailArr = new Array();
			refPerDetailArr=preferred_fax.split("~||~");
			refName=refPerDetailArr[0];
			refFax=refPerDetailArr[1].trim();
			objFrame.document.getElementById("selectReferringPhy").value=refName;
			objFrame.document.getElementById("send_fax_number").value=refFax;	
			if(objFrame.document.getElementById("send_fax_number").value!=''){
				set_phone_format(document.getElementById("send_fax_number"), globalPhnFormat);
			}
		}
		if(Cc1RefId){
			ccName=ccFax="";
			document.getElementById("selectReferringPhyCc1").value="";
			document.getElementById("send_fax_numberCc1").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc1RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccFax 		= ccDetailArr[1];
			document.getElementById("selectReferringPhyCc1").value=ccName;
			document.getElementById("send_fax_numberCc1").value=ccFax;
			set_phone_format(document.getElementById("send_fax_numberCc1"), globalPhnFormat);
		}
		if(Cc2RefId){
			ccName=ccFax="";
			document.getElementById("selectReferringPhyCc2").value="";
			document.getElementById("send_fax_numberCc2").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc2RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccFax 		= ccDetailArr[1];
			document.getElementById("selectReferringPhyCc2").value=ccName;
			document.getElementById("send_fax_numberCc2").value=ccFax;
			set_phone_format(document.getElementById("send_fax_numberCc2"), globalPhnFormat);
		}

		if(Cc3RefId){
			ccName=ccFax="";
			document.getElementById("selectReferringPhyCc3").value="";
			document.getElementById("send_fax_numberCc3").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc3RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccFax 		= ccDetailArr[1];
			document.getElementById("selectReferringPhyCc3").value=ccName;
			document.getElementById("send_fax_numberCc3").value=ccFax;
			set_phone_format(document.getElementById("send_fax_numberCc3"), globalPhnFormat);
		}
	}
	
	
	function loadEmailRefPhy(RefId,Cc1RefId,Cc2RefId,Cc3RefId,preferred_email){
		var refName = refFax = ccName = ccEmail = "";
		document.getElementById("selectReferringPhyEmail").value="";
		document.getElementById("send_email_id").value="";	
		if(RefId) {
			//var refDetail = arrRefIdNameFax[RefId];
			var refDetailArr = new Array();
			refDetailArr= RefId.split("@@");
			refName 	= refDetailArr[0];
			refEmail 		= refDetailArr[1];
			document.getElementById("selectReferringPhyEmail").value=refName;
			document.getElementById("send_email_id").value=refEmail;	
		}
		if(preferred_email){
			var refPerDetailArr = new Array();
			refPerDetailArr=preferred_email.split("~||~");
			refName=refPerDetailArr[0];
			refEmail=refPerDetailArr[1];
			document.getElementById("selectReferringPhyEmail").value=refName;
			document.getElementById("send_email_id").value=refEmail;	
		}
		if(Cc1RefId){
			ccName=ccEmail="";
			document.getElementById("selectReferringPhyEmailCc1").value="";
			document.getElementById("send_email_idEmailCc1").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc1RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccEmail		= ccDetailArr[1];
			document.getElementById("selectReferringPhyEmailCc1").value=ccName;
			document.getElementById("send_email_idCc1").value=ccEmail;	
		}
		if(Cc2RefId){
			ccName=ccEmail="";
			document.getElementById("selectReferringPhyEmailCc2").value="";
			document.getElementById("send_email_idCc2").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc2RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccEmail		= ccDetailArr[1];
			document.getElementById("selectReferringPhyEmailCc2").value=ccName;
			document.getElementById("send_email_idCc2").value=ccEmail;	
		}
		if(Cc3RefId){
			ccName=ccEmail="";
			document.getElementById("selectReferringPhyEmailCc3").value="";
			document.getElementById("send_email_idCc3").value="";	
			var ccDetailArr = new Array();
			ccDetailArr = Cc3RefId.split("@@");
			ccName 		= ccDetailArr[0];
			ccEmail		= ccDetailArr[1];
			document.getElementById("selectReferringPhyEmailCc3").value=ccName;
			document.getElementById("send_email_idCc3").value=ccEmail;	
		}
	}
	
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
<?php
if(is_updox('fax')){
?>	
	top.btn_show("CL1");
<?php
}
?>	
</script>
</head>
<body topmargin="0" leftmargin="0" onUnload="top.btn_show('');">
<div id="divCommonAlertMsgProcTemplate"></div>
  	<?php
			$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 300)) ;
		?>
  	<div class="col-xs-12 bg-white">
    	<div class="row">
        <div class=" col-xs-2 " style="height:<?php echo $col_height;?>px; max-height:100%; overflow:scroll">
          <?php include_once 'tree4consult_letter.php'; ?>


            <table width="100%">
                <tr>
                    <td colspan="2" style="width:100%;">
                        <table style="width:100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:25%; text-align:right; padding-right:5px;"></td>
                                <td style="width:25%; text-align:right; padding-right:5px;">
                                <?php if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host']){?>
                                    <input type="button" value="Send Email" name="sendEmailBtn" id="sendEmailBtn" onclick="script:document.getElementById('send_email_div').style.display='inline-block';" style="cursor:pointer;display:none;" onMouseOver="button_over('sendEmailBtn')" onMouseOut="button_over('sendEmailBtn','')" class="dff_button"  />&nbsp;
                                    <? }?>
                                    <input type="button" value="Send Fax" name="sendFaxBtn" id="sendFaxBtn" onclick="script:document.getElementById('send_fax_div').style.display='inline-block';" style="display:none; cursor:pointer;" onMouseOver="button_over('sendFaxBtn')" onMouseOut="button_over('sendFaxBtn','')" class="dff_button"  />
                                    <?php if(is_updox('fax')){ ?>
                                     <!--<input type="button" name="send_fax_Log" id="send_fax_Log" onClick="window.open('send_fax_log.php', 'fax_log', 'width=800,height=500,scrollbars=yes');" class="dff_button" value="Fax Log">-->
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="sendSaveFaxName" id="sendSaveFaxName" value="<?php echo $setNameFaxPDF;?>">	
                        <input type="hidden" name="sendSaveEmailName" id="sendSaveEmailName" value="<?php echo $setNameFaxPDF;?>">		
                        <input type="hidden" id="pat_temp_id" name="pat_temp_id">
                    </td>
                </tr>
            </table>
            <div id="div_load_image" style="left:50px;top:0px; width:200px; position:absolute; display:none; z-index:1000; ">
            	<img src="../../images/loading_image.gif">
        	</div>
            <div id="send_fax_div" class="modal" role="dialog" >
                <div class="modal-dialog modal-xs">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal_title">Send Fax - Consult Letter</h4>
                    </div>
                  
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Ref. Phy:</label>
                                <input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
                                <input type="text" name="selectReferringPhy" id="selectReferringPhy" class="form-control" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number','','','send_fax_number','','',top.fmain);" 
								autocomplete = "off"> <!--onKeyUp="loadPhysicians(this,'hiddselectReferringPhy','<?php //echo $web_root; ?>/xml/refphy','send_fax_number');"-->  <!--top.loadPhysicians(this,'hiddselectReferringPhy');
								onFocus="top.loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number','','','','','',top.fmain);"
								-->
                            </div>
                        
                            <div class="col-xs-6">
                                <label>Fax No.</label>
                                <input type="text"  name="send_fax_number" id="send_fax_number" class="form-control" onchange="set_phone_format(this,'<?php echo $GLOBALS['phone_format']; ?>','','fax','no_class');" autocomplete = "off">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Cc1:</label>
                                    <input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1">
                                    <input type="text" name="selectReferringPhyCc1"  id="selectReferringPhyCc1" class="form-control" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc1','','send_fax_numberCc1','','','send_fax_number','','',top.fmain);" autocomplete = "off">
                            </div>
                        
                            <div class="col-xs-6">
                                <label>Fax No.</label>
                                <input type="text"  name="send_fax_numberCc1" id="send_fax_numberCc1" class="form-control" onchange="set_phone_format(this,'<?php echo $GLOBALS['phone_format']; ?>','','fax','no_class');" autocomplete = "off">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Cc2:</label>
                                <input type="hidden" name="hiddselectReferringPhyCc2" id="hiddselectReferringPhyCc2">
                                <input type="text" name="selectReferringPhyCc2"  id="selectReferringPhyCc2" class="form-control" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc2','','send_fax_numberCc2','','','send_fax_number','','',top.fmain);" autocomplete = "off">
                            </div>
                        
                            <div class="col-xs-6">
                                <label>Fax No.</label>
                                <input type="text"  name="send_fax_numberCc2" id="send_fax_numberCc2" class="form-control" onchange="set_phone_format(this,'<?php echo $GLOBALS['phone_format']; ?>','','fax','no_class');" autocomplete = "off">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Cc3:</label>
                                <input type="hidden" name="hiddselectReferringPhyCc3" id="hiddselectReferringPhyCc3">
                                <input type="text" name="selectReferringPhyCc3"  id="selectReferringPhyCc3" class="form-control" onKeyUp="top.loadPhysicians(this,'hiddselectReferringPhyCc3','','send_fax_numberCc3','','','send_fax_number','','',top.fmain);" autocomplete = "off">
                            </div>
                        
                            <div class="col-xs-6">
                                <label>Fax No.</label>
                                <input type="text"  name="send_fax_numberCc3" id="send_fax_numberCc3" class="form-control" onchange="set_phone_format(this,'<?php echo $GLOBALS['phone_format']; ?>','','fax','no_class');" autocomplete = "off">
                            </div>
                        </div>                                                                     
                    </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-info"  id="send_close_btn" onclick="return sendSavedFax();" >Send Fax</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="$('#div_load_image').hide();" >Close</button>
        
                  </div>
                  
                </div>
              </div>
            </div>

			   <div class="div_popup border bg1" id="send_email_div" style="top:100px; position:absolute; width:400px;z-index:900; display:none;">
                <div class="page_block_heading_patch pt4 boxhead" style="cursor:move;text-align:left;">
                    <span class="closeBtn" onClick="$('#send_email_div').hide();"></span>
                    <b style="font-size:14px; ">Send Email</b>
                </div>
            <script type="text/javascript">
            function getEmail_Id(){
                timer_refPhyFax = setTimeout("return setEmailId(document.getElementById('hiddselectReferringPhyEmail'),document.getElementById('hiddselectReferringPhyEmailCc1'),document.getElementById('hiddselectReferringPhyEmailCc2'),document.getElementById('hiddselectReferringPhyEmailCc3'))",1000);
            }
            </script>
        
                <div class="m5 alignCenter"  id="faxDiv" >
                    <table border="0" align="left" cellpadding="0" cellspacing="0" style="width:300px; height:100px;">
                        <tr>    
                            <td style="padding-bottom:2px; width:120px;" class="txt_11b alignRight">Ref.&nbsp;Phy:</td>
                            <td style="padding-bottom:2px; width:110px;">
                            <input type="hidden" name="hiddselectReferringPhyEmail" id="hiddselectReferringPhyEmail">
                            <input type="text" name="selectReferringPhyEmail"  id="selectReferringPhyEmail" style="width:110px;" onKeyUp="loadPhysiciansEmail(this,'hiddselectReferringPhyEmail','<?php echo $web_root; ?>/xml/refphy','send_email_id');" autocomplete="off">
                            </td>
                           
                            <td style="padding-left:10px; padding-bottom:5px;" class="txt_11b alignRight">Email&nbsp;ID.:</td>
                            <td style="padding-bottom:5px;"><input type="text"  name="send_email_id" id="send_email_id" style="width:100px;" class="text_10"></td>
                       </tr>
                       <tr>    
                            <td style="padding-bottom:2px; width:120px;" class="txt_11b alignRight">Cc1:</td>
                            <td style="padding-bottom:2px; width:110px;">
                            <input type="hidden" name="hiddselectReferringPhyEmailCc1" id="hiddselectReferringPhyEmailCc1">
                            <input type="text" name="selectReferringPhyEmailCc1"  id="selectReferringPhyEmailCc1" style="width:110px;" onKeyUp="loadPhysiciansEmail(this,'hiddselectReferringPhyEmailCc1','<?php echo $web_root; ?>/xml/refphy','send_email_idCc1');"  autocomplete="off">
                            </td>
                           
                            <td style="padding-left:10px; padding-bottom:5px;" class="txt_11b alignRight">Email&nbsp;ID.:</td>
                            <td style="padding-bottom:5px;">
                                <input type="text"  name="send_email-idCc1" id="send_email_idCc1" style="width:100px;" class="text_10" >
                            </td>
                       </tr>
                       <tr>    
                            <td style="padding-bottom:2px; width:120px;" class="txt_11b alignRight">Cc2:</td>
                            <td style="padding-bottom:2px; width:110px;">
                            <input type="hidden" name="hiddselectReferringPhyEmailCc2" id="hiddselectReferringPhyEmailCc2">
                            <input type="text" name="selectReferringPhyEmailCc2"  id="selectReferringPhyEmailCc2" style="width:110px;" onKeyUp="loadPhysiciansEmail(this,'hiddselectReferringPhyEmailCc2','<?php echo $web_root; ?>/xml/refphy','send_email_idCc2');"  autocomplete="off">
                            </td>
                           
                            <td style="padding-left:10px; padding-bottom:5px;" class="txt_11b alignRight">Email&nbsp;ID.:</td>
                            <td style="padding-bottom:5px;">
                                <input type="text"  name="send_email_idCc2" id="send_email_idCc2" style="width:100px;" class="text_10" >
                            </td>
                       </tr>
                       <tr>    
                            <td style="padding-bottom:2px; width:120px;" class="txt_11b alignRight">Cc3:</td>
                            <td style="padding-bottom:2px; width:110px;">
                            <input type="hidden" name="hiddselectReferringPhyEmailCc3" id="hiddselectReferringPhyEmailCc3">
                            <input type="text" name="selectReferringPhyEmailCc3"  id="selectReferringPhyEmailCc3" style="width:110px;" onKeyUp="loadPhysiciansEmail(this,'hiddselectReferringPhyEmailCc3','<?php echo $web_root; ?>/xml/refphy','send_email_idCc3');"  autocomplete="off">
                            </td>
                           
                            <td style="padding-left:10px; padding-bottom:5px;" class="txt_11b alignRight">Email&nbsp;ID.:</td>
                            <td style="padding-bottom:5px;">
                                <input type="text"  name="send_email_idCc3" id="send_email_idCc3" style="width:100px;" class="text_10">
                            </td>
                       </tr>	
                       
                       <tr>
                            <td colspan="4" style="padding-left:5px; padding-bottom:5px;">
                                <table border="0" align="left" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:170px; text-align:right; padding-right:10px;"><input type="button" class="dff_button hold" value="Send Email" id="send_close_btn" onclick="return sendSavedEmail();" onMouseOver="button_over('send_close_btn')" onMouseOut="button_over('send_close_btn','');"></td>
                                        <td class="alignLeft"><input type="button" class="dff_button cancel" value="Close" onClick="$('#div_load_image_e').hide();$('#send_email_div').hide();" id="email_cancel_btn" onMouseOver="button_over('email_cancel_btn')" onMouseOut="button_over('email_cancel_btn','');"></td>
                                    </tr>
                               </table>     
                            </td>
                        </tr>	
                    </table> 
                    
                </div>
                <div id="div_load_image_e" style="left:150px;top:0px; width:150px; position:absolute;  z-index:901; display:none;  ">
                <img src="../../images/loading_image.gif">
                </div>
            </div>

        
        </div>
      
        <div class="col-xs-10 ">
            <div class="row">
                <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
                <iframe name="consent_data" id="consult_data_id" <?php echo $consentScroll;?>  style="width:100%; height:<?php echo $col_height;?>px;" frameborder="0"></iframe>
                </div>   
            </div>
        </div>
        
      </div>
    </div>

</body>
</html>