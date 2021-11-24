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
require_once($GLOBALS['srcdir']."/classes/admin/documents/document.class.php");
$doc_obj = new Documents('consult');

//Direct Message Notification
$alertClass = $directErrMsg = $sendPhy = $notSendPhy = $directSendTrue = '';

//Get Ref. Physician names
$refPhyArr = array();
$sqlQry = imw_query(" SELECT physician_Reffer_id,`LastName`, `MiddleName`, `FirstName` FROM refferphysician ");

if($sqlQry && imw_num_rows($sqlQry) > 0){
	while($row = imw_fetch_assoc($sqlQry)){
		$refPhyArr[$row['physician_Reffer_id']] = core_name_format($row['LastName'], $row['FirstName'], $row['MiddleName']);
	}
}

//Check which received id's have direct email
$refPhyDirectIdChkArr = array();
if($ref_primary_care_id && is_numeric($ref_primary_care_id)) $refPhyDirectIdChkArr[] = $ref_primary_care_id;
if($pcp_primary_care_phy_id && is_numeric($pcp_primary_care_phy_id)) $refPhyDirectIdChkArr[] = $pcp_primary_care_phy_id;
if($co_man_physician_id && is_numeric($co_man_physician_id)) $refPhyDirectIdChkArr[] = $co_man_physician_id;
if($transitionPhyName && is_numeric($transitionPhyName)) $refPhyDirectIdChkArr[] = $transitionPhyName;
if($hidd_AddresseeOther && is_numeric($hidd_AddresseeOther)) $refPhyDirectIdChkArr[] = $hidd_AddresseeOther;
if($rfPcpId && is_numeric($rfPcpId)) $refPhyDirectIdChkArr[] = $rfPcpId;
if($rfId && is_numeric($rfId)) $refPhyDirectIdChkArr[] = $rfId;
if($coManPhyId && is_numeric($coManPhyId)) $refPhyDirectIdChkArr[] = $coManPhyId;

$refPhyDirectIdChkArr = array_unique($refPhyDirectIdChkArr);

if(count($refPhyDirectIdChkArr) > 0){
	foreach($refPhyDirectIdChkArr as $directChkKey => $refIDD){
		//Chck for direct message
		$sqlQry = imw_query(" SELECT direct_email FROM refferphysician WHERE physician_Reffer_id = ".$refIDD." ");
		
		$recFalse = false;
		$globalTrue = false;

		if($sqlQry && imw_num_rows($sqlQry) > 0){
			$rowfe = imw_fetch_assoc($sqlQry);
			//pre($rowfe);
			if(empty($rowfe['direct_email']) == false) $recFalse = true;
		}

		if($recFalse == false){
			//Chk multiple direct mail table
			$sqlQrySec = imw_query(" SELECT count(id) as Count FROM ref_multi_direct_mail WHERE ref_id = ".$refIDD." AND del_status = 0 ");
			
			if($sqlQrySec && imw_num_rows($sqlQrySec) > 0){
				$rowfe1 = imw_fetch_assoc($sqlQrySec);
				//pre($rowfe1);
				if(empty($rowfe1['Count']) == false) $globalTrue = true;
			}
		}else{
			$globalTrue = true;
		}

		//pre(var_dump($globalTrue));
		if($globalTrue == false) unset($refPhyDirectIdChkArr[$directChkKey]);
	}
}

if(isset($_REQUEST['hid_send_direct_bool']) && $_REQUEST['hid_send_direct_bool'] == 1){
	$directErrMsg = 'Direct Message not successfull';
	$alertClass = 'danger';
	
	if($flg_after_save_op && $flg_after_save_op == 2 && empty($flg_after_save_op) == false){
		$directErrMsg = 'Direct Message successfull';
		$alertClass = 'success';
	}
	
	if(is_array($arrDirectEmail) && count($arrDirectEmail) > 0){
		$sendPhy = implode("\\n\t- ",$arrDirectEmail);
	}
	if(is_array($arrDirectEmailNot) && count($arrDirectEmailNot) > 0){
		$notSendPhy = implode("\\n\t- ",$arrDirectEmailNot);
	}
	$directSendTrue = 1;
	//$directNotification = '<div class="alert alert-'.$alertClass.'">'.$directErrMsg.'</div>';
}

$panel_data = $doc_obj->get_template_data('document_panels');

$js_php_arr = array();
$js_php_arr['temp_panels'] = $panel_data[0];	//Used in JS file
$js_php_arr['billing_server'] = $billing_global_server_name;

if($flg_after_save_op == 2){
	if(empty($sendPhy) == false) $js_php_arr['direct_send_phy'] = $sendPhy;
	if(empty($notSendPhy) == false) $js_php_arr['direct_not_send_phy'] = $notSendPhy;
}elseif($flg_after_save_op == 1) $js_php_arr['direct_error'] = implode(',', $error_msg);

if(empty($directSendTrue) == false && $directSendTrue == 1) $js_php_arr['direct_init'] = 1;
if(count($refPhyArr) > 0) $js_php_arr['refPhyArr'] = $refPhyArr;
if(empty($_REQUEST['saveBtn']) == false && isset($_REQUEST['saveBtn'])) $js_php_arr['save_call'] = 1;
$js_php_arr = json_encode($js_php_arr);

$js_vars =  '
<script>
	var js_array = '.$js_php_arr.';
</script>';
echo $js_vars;

$patientId=$_SESSION["patient"];
$headerPtDetails=core_get_patient_name($patientId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

	

<!-- CSS SHEET -->
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet">


<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<link href="../../library/css/workview.css?<?php echo filemtime('../../library/css/workview.css');?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/redactor/redactor.css" />

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<!-- Redactor is here -->
<!-- Lastest JQuery -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<!-- Bootstrap Selectpicker -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js" type="text/javascript"></script>
<!-- Reactor JS SOURCE -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/redactor.js?<?php echo filemtime('../../library/redactor/redactor.js');?>"></script>
<!-- Plug Ins -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fontcolor.js"></script>
<!-- <script src="<?php echo $GLOBALS['webroot']; ?>/redactor/plugins/fontfamily.js"></script> -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fontsize.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/imagemanager.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/table.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/redactor/plugins/fullscreen.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js?<?php echo filemtime('../../library/js/common.js');?>"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/consult_letters.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>

<!--
<script src="<?php echo $GLOBALS['webroot']; ?>/interface/common/record_av/dictation_consult.js"></script>
-->

<!--<script type="text/javascript" src="js_consult_letter.php"></script>-->
<script type="text/javascript">
REF_PHY_FORMAT = "<?php echo $GLOBALS['REF_PHY_FORMAT']?>";
var phone_min_length = '<?php echo !empty($GLOBALS['phone_min_length'])?$GLOBALS['phone_min_length']:10; ?>';
WRP = opener.WRP;
var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var arrRefIdNameFax=new Array(); //array to get phy fax for "Other Ref Phy(Last, First Name)" text field
var elem_per_vo = '<?php echo $elem_per_vo; ?>';
var form_id = '<?php echo $formId; ?>'; 
var fax_coverLetter='<?php echo $faxCoverLetter;?>';

function directNotify(sendArr, notsendArr, directError){
	var direct_email = sendArr;
	var direct_email_not = notsendArr;
	//directMsgNotify
	if(typeof(direct_email) == 'undefined' || direct_email == 'undefined') direct_email = '';
	if(typeof(direct_email_not) == 'undefined' || direct_email_not == 'undefined') direct_email_not = '';
	var msg = '';
	var className = '';
	
	var directErrorFlag = false;
	if(directError){
		directErrorFlag = true;
	}
	
	if(direct_email != "" || direct_email_not != "" && directErrorFlag == false){	
		if(direct_email != ""){
			msg = "Direct message sent successfully to : \n\t- "+direct_email;
			className = 'success';
		}
		
		if(direct_email_not != ""){
			if(msg != "")msg += "\n\n";
			msg += "Direct message not send to : \n\t- "+direct_email_not;
			className = 'warning';
			
			if(direct_email == '') className = 'danger';
			
		}
	}else{
		msg = 'No Direct mail sent for selected referring physicians';
		if(directError) msg += ' <br /> Error - '+directError;
		className = 'danger';
	}
	
	if(msg){
		var htmlString = '<div class="alert alert-'+className+' alert-dismissible fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+msg+'</div>';
		//console.log($(document).contents().find('#directMsgNotify'));
		$('#patientConsultLetter #directMsgNotify', document).html(htmlString);
		
		setTimeout(function(){
			$('#patientConsultLetter #directMsgNotify .alert-dismissible .close', document).trigger('click');
		},10000);
		
		if(className == '' || className == 'success'){
			setTimeout(function(){
				window.close();
			},5000);
		}
	}
}

function checkDirectMail(hiddenObj){
	var refPhyArr = [];
	var value = '';
	$('#AddresseeOther').unbind('change');
	$('input#AddresseeOther').unbind('blur');
	
	//Getting Addresse Value
	var selObj = $('select#Addressee');
	var selVal = selObj.val();
	var selHiddenField = selObj.data('hiddenid');
	
	if(selVal == "Other" && selHiddenField !== ''){
		var hiddenObj = $('#'+selHiddenField);
		$('#AddresseeOther').on('change', function(){
			checkDirectMail(hiddenObj);
			return false;
		});
	}else{
		if($.isNumeric(selVal)) refPhyArr.push(selVal);
	}
	
	if(hiddenObj && typeof(hiddenObj) == 'object'){
		if($.isNumeric($(hiddenObj).val())) refPhyArr.push($(hiddenObj).val());
	}
	
	//Getting Check box value
	var chkBox = '';
	$('[data-mail="direct"]').each(function(id, elem){
		var dataCheck = $(elem).data('check');
		if(dataCheck == true) chkBox = $(elem);
	});
	
	if(chkBox && typeof(chkBox) == 'object'){
		var dataArr = chkBox.data();
		var hiddenChkObj = '';
		
		if(dataArr.hiddenfield && dataArr.hiddenfield !== '') hiddenChkObj = $('#'+dataArr.hiddenfield)
		
		if(dataArr.input && dataArr.input !== ''){
			var inputObj = $('#'+dataArr.input);
			inputObj.unbind('change');
			
			if(inputObj.length && hiddenChkObj && hiddenChkObj.length){
				inputObj.on('change', function(){
					checkDirectMail(hiddenChkObj);
					return false;
				});
			}	
		}
		
		var chkVal = '';
		if(hiddenChkObj.length && hiddenChkObj.val() !== '') chkVal = hiddenChkObj.val();
		if(chkVal && $.isNumeric(chkVal)) refPhyArr.push(chkVal);
	}
	
	if(refPhyArr.length){
		refPhyArr = $.unique(refPhyArr);
		refPhyArr = refPhyArr.join(',');
		
		$.ajax({
			url:zPath+'/chart_notes/onload_wv.php',
			data:{phyId : refPhyArr, elem_action : 'Check_Direct', 'checkLogic' : 'new'},
			type:'GET',
			dataType:'JSON',
			success:function(response){
				var btnObj = $('#send_direct_btn');
				if(btnObj.length){

					var clickedUsers = JSON.parse(sessionStorage.getItem("clickedUsers"));
					
					if(!clickedUsers) var userId = [];
					else userId = clickedUsers;

					userId.push(refPhyArr);
					sessionStorage.setItem("clickedUsers", JSON.stringify($.unique(userId)));

					if(response === true){
						//chkMultiDirect(refPhyArr, hiddenChkObj);
						if(btnObj.prop('disabled') == true) btnObj.prop('disabled', false);
					}else{
						if(btnObj.prop('disabled') == false) btnObj.prop('disabled', true);
					}
				}
			}
		});
		return false;
	}
	
	if($('#send_direct_btn').prop('disabled') == false) $('#send_direct_btn').prop('disabled', true);

}

function askForMailSelection(arrData){
	if(Object.keys(arrData).length == 0) return;
	
	var htmlStr = [];
	
	$.each(arrData, function(refId, val){
		var RefName = js_array.refPhyArr[refId];
		if(RefName && Object.keys(val).length > 0){
			var string = '<div class="col-sm-12">';
			string += '<label>Select Direct Email for '+RefName+'</label>';
			
			var optString = '';
			var defaultEmail = val.default;
			$.each(val, function(valId, vlValues){
				var optVal = vlValues['id'];
				var optEmail = vlValues['email'];
				
				if(vlValues['default']) defaultEmail = vlValues['default'];
				if(optEmail == defaultEmail) optEmail = '';
				if(optVal && optEmail) optString += '<option value='+optVal+'>'+optEmail+'</option>';
			});

			if(optString || defaultEmail){
				string += '<select class="form-control minimal" data-width="100%" data-size="5" id="selectDirectFor'+refId+'" onChange="setDirectMail(this);" data-ref='+refId+'>';
				if(defaultEmail == '') string += '<option value="">Please select</option>'+optString;
				else string += '<option value="">'+defaultEmail+'</option>'+optString;

				string += '</select>';
			}else{
				string += '<span>No Direct mail Found</span>';
			}
			
			string += '</div>';
			if(string) htmlStr.push(string);
		}
	});

	if(htmlStr.length){
		modalStr = '<div class="row">'+htmlStr.join(' ')+'</div>';
		show_modal('direct_modal_select', 'Select a direct email :', modalStr, '<button type="button" class="btn btn-success" id="multiDirectBtn">Done</button>', '');
		$('#direct_modal_select').on('show.bs.modal', function(){
			//$('.selectpicker').selectpicker('refresh');
		});

		$('#multiDirectBtn').on('click', function(){
			var storedNames = JSON.parse(sessionStorage.getItem("names"));
			if(storedNames){
				storedNames = JSON.stringify(storedNames);
				$('#hidden_direct_email_id').val(storedNames);
			}
			$('#hidden_direct_email_id').data('check', 'true');
			send_direct();
		});
	}
}


//Set the direct mail selected by user
function setDirectMail(obj){
	var refPhy = $(obj).data('ref');
	var selVal = $(obj).val();

	var storedNames = JSON.parse(sessionStorage.getItem("names"));
	
	if(!storedNames) var names = {};
	else names = storedNames;
	
	names[refPhy] = selVal;
	sessionStorage.setItem("names", JSON.stringify(names));
}


//===SHOW PATIENT PREVIOUS CONSULT IN POP-UP=======
function get_prev_consult_pdf(patient_prev_consult_id){
	
	var patient_prev_consult_id = $.trim(patient_prev_consult_id);
	var u = zPath+"/chart_notes/templatepri.php?tempId="+patient_prev_consult_id;
	if(patient_prev_consult_id!==''){
		window.open(""+u, 'Patient Previous Consult', 'width=800,height=700,scrollbars=yes');
	}
}
//======xxxxxxxxxxxxxxxxxxxxxxxxxxxxx============
//handle Send Direct Button
// function checkDirect(obj){
// 	$('#AddresseeOther').unbind('change');
// 
// 	var objType = obj[0].type;
// 	var selVal = '';
// 
// 	if(objType && typeof(objType) !== 'undefined'){
// 		if(objType == 'select-one'){
// 			selVal = obj.val();
// 			var selHiddenField = obj.data('hiddenid');
// 
// 			if(selVal && selVal !== '' && selVal == 'Other' && selHiddenField !== ''){
// 				var hiddenObj = $('#'+selHiddenField);
// 				$('#AddresseeOther').on('change', function(){
// 					checkDirect(hiddenObj);
// 					return false;
// 				});
// 			}
// 		}else{
// 			selVal = obj.val();
// 		}
// 	}
// 
// 	if(selVal !== '' && $.isNumeric(selVal)){
// 		$.ajax({
// 			url:zPath+'/chart_notes/onload_wv.php',
// 			data:{phyId : selVal, elem_action : 'Check_Direct'},
// 			type:'GET',
// 			dataType:'JSON',
// 			success:function(response){
// 				var btnObj = $('#send_direct_btn');
// 				if(btnObj.length){
// 					if(response === true){
// 						if(btnObj.prop('disabled') == true) btnObj.prop('disabled', false);
// 					}else{
// 						if(btnObj.prop('disabled') == false) btnObj.prop('disabled', true);
// 					}
// 				}
// 			}
// 		});
// 	}else{
// 		if($('#send_direct_btn').prop('disabled') == false) $('#send_direct_btn').prop('disabled', true);
// 	}
// }

$(document).ready(function () {
if(sessionStorage.getItem("names")) sessionStorage.removeItem("names");	
if(sessionStorage.getItem("clickedUsers")) sessionStorage.removeItem("clickedUsers");	


if($('#txtOther').val() == '') $('#hiddtxtOther').val('');
if($('#AddresseeOther').val() == '') $('#hidd_AddresseeOther').val('');
<?php
	if(isset($_REQUEST['saveBtn']) && $_REQUEST['saveBtn'] == 'Save and Print'){
?>
		var u = zPath+"/chart_notes/templatepri.php?tempId=<?php echo $patient_consult_id; ?>";
		window.open(""+u, 'Save and Print', 'width=800,height=700,scrollbars=yes');
<?php 
		unset($_REQUEST['saveBtn']);
	}
?>

	$("#saveBtn").click(function(){
		if($("#templateList").val()==""){
			top.fAlert("Please select the template first",'',$("#templateList"));
			return false;	
		}
	});
	$("#savePrintPDFBtn").click(function(){
		if($("#templateList").val()==""){
			top.fAlert("Please select the template first",'',$("#templateList"));
			return false;	
		}
	});
	
	$("#div_loading_image").css({ "z-index":"1000000" }).hide();	
	//top.popup_resize(screen.availWidth,screen.availHeight,0.91);   
	
	if(js_array.direct_init && js_array.direct_init == 1){
		<?php 
		if(isset($flg_after_save_op)){ //After save operations
			if($flg_after_save_op==2){
		?>			
				//Not sure what this code does ?
				<?php if($_REQUEST["elem_saveClose"] == "1") { ?>
					//opener.window.location = 'main_page.php';
					//opener.window.location.reload();
				<?php } ?>
				directNotify(js_array.direct_send_phy, js_array.direct_not_send_phy, js_array.direct_error);
				if(typeof(window.opener.top.update_toolbar_icon) != "undefined"){ window.opener.top.update_toolbar_icon('consult');} 
				
				if($('#directMsgNotify').find('.alert-danger') === false || $('#directMsgNotify').find('.alert-warning') === false){
					setTimeout(function(){window.close()},5000);
				}
		<?php 
			}else if($flg_after_save_op==1){
		?>
				directNotify(js_array.direct_send_phy, js_array.direct_not_send_phy, js_array.direct_error);
		<?php 
				//exit();
			} 
		} ?> 
	}else{
		if(js_array.save_call && js_array.save_call == 1){
			setTimeout(function(){window.close()},5000);
		}
	}
	
	
	//Checking Direct Address for selected addresse
	$('[data-mail="direct"]').on('change', function(){
		var chkObj = $(this);
		
		if(chkObj.prop('checked') === true){
			chkObj.data('check', true);
			$('[data-mail="direct"]').not(chkObj).data('check', false);
		}else{
			$('[data-mail="direct"]').data('check', false);
		}
		
		if(chkObj.data('hiddenfield') && chkObj.data('hiddenfield') !== '' && chkObj.data('input') !== ''){
			if(chkObj.attr('id') == 'cbkOther'){
				var inputField = chkObj.data('input');
				$('input#'+inputField).unbind('blur');
				
				//Trigger Direct Mail check on input blue
				$('input#'+inputField).on('blur', function(){
					if($(this).val() == '') $('#'+chkObj.data('hiddenfield')).val('');
					checkDirectMail($(this));
					return false;
				});
			}
		}
		
		//Trigger Check Direct Mail 
		checkDirectMail();
	});
	
	
	$('#Addressee').on('change', function(){
		var selObj = $(this);
		checkDirectMail();
	});
	
	$('input#AddresseeOther').on('blur', function(){
		var selObj = $('#hidd_AddresseeOther');
		if($(this).val() == '') selObj.val('');
		checkDirectMail(selObj);
	});
	
	//Check whether request is their , if yes thn trigger all the events
	$('[data-mail="direct"], #Addressee, input#AddresseeOther').trigger('change');

	$('body').on('shown.bs.modal', '#direct_modal_select', function(){
		$(this).find('input[name=direct_email_single]').each(function(id, elem){
			$(elem).unbind();
			$(elem).on('click', function(){
				setDirectMail($(elem));
			});
		});
	});	

}); 
</script> 	
<style type="text/css">
	select {height:26px!important; padding:0 5px; max-width:100%;} 
	.consultltr{ height: 600px; }
	#directMsgNotify{position:absolute;z-index:9}
	.physDropDowns{width:99%!important;}
	table tr td{cursor:pointer;}
    label.text_purple{color:#ffcc00;}
</style>
</head>
<body style="overflow:hidden;">
<form name="patientConsultLetter" id="patientConsultLetter" method="post" action="<?php echo $GLOBALS['rootdir'];?>/chart_notes/onload_wv.php?elem_action=Consult_letters">
<input type="hidden" name="templateId" id="templateId" value="<?php echo $consultTemplate; ?>">
<input type="hidden" name="patient_consult_id" id="patient_consult_id" value="">
<input type="hidden" name="tempId" id="tempId" value="<?php echo $tempId; ?>">
<input type="hidden" name="elem_saveClose" id="elem_saveClose" value="0">
<input type="hidden" name="header_panel" id="header_panel" value="<?php echo htmlentities($header_panel);?>" />
<input type="hidden" name="footer_panel" id="footer_panel" value="<?php echo htmlentities($footer_panel);?>" />
<input type="hidden" name="leftpanel_panel" id="leftpanel_panel" value="<?php echo htmlentities($leftpanel_panel);?>" />			
<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
<textarea class="hidden"  name="hidTextAreaFax" id="hidTextAreaFax"><?php echo $faxCoverLetter; ?></textarea>
<input type="hidden" name="faxStatus" id="faxStatus">
<input type="hidden" name="emailStatus" id="emailStatus">
<input type="hidden" name="footer_status" id="footer_status" value="<?php echo $consultTemplatefooter; ?>">
<input type="hidden" name="hidd_sigProvId" id="hidd_sigProvId" value="<?php echo $sigProvId;?>">
<input type="hidden" name="hidd_sigProvExist" id="hidd_sigProvExist" value="<?php echo $sigProvExist;?>">
<input type="hidden" name="hiddTemplateTopMargin" id="hiddTemplateTopMargin" value="<?php echo  $consultTemplateTopMargin;?>">
<input type="hidden" name="hiddTemplateLeftMargin" id="hiddTemplateLeftMargin" value="<?php echo  $consultTemplateLeftMargin;?>">
<input type="hidden" name="hid_send_direct_bool" id="hid_send_direct_bool" value="0" />
<input type="hidden" name="ccdDocumentOptions" id="ccdDocumentOptions" value="" />
<input type="hidden" name="leftpanel" id="leftpanel" value="<?php echo $leftpanel_val; ?>" />
<input type="hidden" name="fax_type_primary" id="fax_type_primary">
<input type="hidden" name="fax_type_cc1" id="fax_type_cc1">
<input type="hidden" name="hiddfirstSelected" id="hiddfirstSelected" value="<?php echo $_REQUEST['hiddfirstSelected'];?>">
<input type="hidden" name="fax_type_cc2" id="fax_type_cc2">
<input type="hidden" name="fax_type_cc3" id="fax_type_cc3">
<input type="hidden" name="hidden_direct_email_id" id="hidden_direct_email_id" data-checked="false">
<input type="hidden" name="fax_log_id" id="fax_log_id" value="">


<div class=" container-fluid">
<div class="whtbox exammain">

<div class="clearfix"></div>
<div class="consbord">
	<div class="row">
		<div class="col-sm-5"><span class="consulthead" style="margin-bottom:8px"><figure><img src="../../library/images/consul_letter.png" alt=""/></figure><h2>Consult Letters </h2></span></div>
		<!--<div class="col-sm-2 "><div class="consicon"><img src="../../library/images/video_icon1.png" alt=""/> <img 
		src="../../library/images/video_play1.png" alt=""/> <img src="../../library/images/close_icon1.png" alt=""/></div></div>
		--> 
		<div class="col-sm-5"><h4><?php echo $headerPtDetails[4]; ?></h4></div>	
		<div class="prevptcons col-sm-2">
			<button class="btn btn-default" type="button" data-toggle="modal" data-target="#prev_consult_modal">Previous Consult</button>
		</div>	
	</div>
</div>
<div class="clearfix"></div>
<!--Panel Content-->
<?php 
if($tempId){
?>	
<div class="row">
	<div class="form-group">
		<label class="control-label col-sm-2" ><b>Template Selected:</b></label>
		<div class="col-sm-10">
			<p class="form-control-static"><?php echo $templateName; ?></p>
			<input type="hidden" name="templateList" id="templateList" value="<?php echo $tempId; ?>">
		</div>
	</div>
</div>
<?php
}else{
?>
<div class="row consultltr">
	<div id="directMsgNotify" class="col-sm-8 col-sm-offset-2"></div>
	<div class="col-sm-3">
        <div class="consltr">
        
        <div class="clearfix"></div>	
        <div class="pd5">
        <div class="row">
        
        <!--======ADDRESS AND OTHER ADRESSEE WORK==================	-->
        <div class="col-sm-6">
            <label for="Addressee">Addressee</label>
            <select name="Addressee" id="Addressee" onChange="javascript:selAddrCCFun(this,'TDAddresseeOtherId','AddresseeOther','hidd_AddresseeOther');" class="select minimal" data-hiddenid = "hidd_AddresseeOther">
                <option value="">Select</option>
                <?php if($ptRefPhyNme) {?><option data-adrese="<?php echo $ref_primary_care_id;?>" value="<?php echo $ref_primary_care_id;?>" <?php if($Addressee==$ref_primary_care_id) { echo "selected"; }?> <?php if(in_array($ref_primary_care_id,$refPhyDirectIdChkArr)) { echo 'class="text_purple"'; }?> ><?php echo $ptRefPhyNme;?></option><?php } ?>
                <?php if($ptPcpPhyNme) {?><option data-adrese="<?php echo $pcp_primary_care_phy_id;?>" value="<?php echo $pcp_primary_care_phy_id;?>" <?php if($Addressee==$pcp_primary_care_phy_id) { echo "selected"; }?> <?php if(in_array($pcp_primary_care_phy_id,$refPhyDirectIdChkArr)) { echo 'class="text_purple"'; }?> ><?php echo $ptPcpPhyNme;?></option><?php } ?>
                <?php if($ptCoManPhyNme) {?><option data-adrese="<?php echo $co_man_physician_id;?>" value="<?php echo $co_man_physician_id;?>" <?php if($Addressee==$co_man_physician_id) { echo "selected"; }?> <?php if(in_array($co_man_physician_id,$refPhyDirectIdChkArr)) { echo 'class="text_purple"'; }?> ><?php echo $ptCoManPhyNme;?></option><?php } ?>
                <?php if($transitionPhyName) {?><option data-adrese="<?php echo $transitionPhyId;?>" value="<?php echo $transitionPhyId;?>" <?php if($Addressee==$transitionPhyName) { echo "selected"; }?>  data-type = 4 ><?php echo $transitionPhyName;?></option><?php } ?>
                <option value="Other" <?php if(($Addressee=='Other') && ($templateId)){ echo 'Selected';  }?>>Other</option>
            </select>
        </div>
        <?php $AddresseeOthrDisp = "hidden";if(($Addressee=='Other') && ($templateId)){ $AddresseeOthrDisp = "";  }?>
        <div id="TDAddresseeOtherId" class="col-sm-6 <?php echo $AddresseeOthrDisp;?>">
            <label for="AddresseeOther">Other Addressee	</label>

			<!-- 
				
				Updating loadPhysicians function, adding extra params to fix popup open and close issue.
			-->    
            <input type="text" name="AddresseeOther" id="AddresseeOther" value="<?php echo $AddresseeOther;?>"  class="form-control <?php if(in_array($hidd_AddresseeOther,$refPhyDirectIdChkArr)) echo " text_purple"; ?>" onkeyup="loadPhysicians(this,'hidd_AddresseeOther','','send_fax_number','','','send_fax_number','','',top.fmain);">
            <!-- <input type="text" name="AddresseeOther" id="AddresseeOther" value="<?php //echo $AddresseeOther;?>"  class="form-control <?php //if(in_array($hidd_AddresseeOther,$refPhyDirectIdChkArr)) echo " text_purple"; ?>" onKeyUp="loadPhysicians(this,'hidd_AddresseeOther');" onFocus="loadPhysicians(this,'hidd_AddresseeOther');" > -->

            <input type="hidden" name="hidd_AddresseeOther" id="hidd_AddresseeOther" value="<?php echo $hidd_AddresseeOther;?>">
        </div>
        <div class="clearfix"></div>
        <div class=" pdlr5 mt5">
        <!--======PRIMARY CARE PROVIDER WORK========================= -->
        <?php if($gmPCP){
         $class = "col-sm-6";
         $class1 = "col-sm-6";
        
        } else {
            $class = "hide";
            $class1 = "col-sm-12";
        }  
		
		//To Show Ref Phy block even if patient has not PCP but a ref phy
		if($class == 'hide' && $rfFName) $class = 'col-sm-6'; 
		?>
        
		
		<?php 
			$gmPCPoption_str='';
			$rfFNameoption_str='';
			$coManPhyoption_str='';
			$tmpArr=array();
			if(empty($mutiRefPhy)== false) {
				if(isset($_REQUEST['items']) && empty($_REQUEST['items']) == false){ 
					$tmpArr = explode(',', $_REQUEST['items']);
				}
				foreach($mutiRefPhy as $phy_type => $row) {
					switch($phy_type) {
						case 1:
							//$rfFName_arr[]
							if(count($mutiRefPhy[$phy_type]) >1) {
								foreach($row as $value) {
                                    $addrs=$value['Address1'];
                                    if($value['Address1']=='')$addrs=$value['Address2'];
									$selected='';
									if(in_array($value['physician_Reffer_id'],$tmpArr))$selected='selected';
                                    if($rfFName)
									$rfFNameoption_str.='<option '.$selected.' value="'.$value['physician_Reffer_id'].'">'.$value['refName'].' ('.$addrs.')'.'</option>';
								}
							}
						break;
						case 2:
							//$coManPhy_arr[]
							if(count($mutiRefPhy[$phy_type]) >1) {
								foreach($row as $value) {
                                    $addrs=$value['Address1'];
                                    if($value['Address1']=='')$addrs=$value['Address2'];
									$selected='';
									if(in_array($value['physician_Reffer_id'],$tmpArr))$selected='selected';
                                    if($rfCoManFName)
									$coManPhyoption_str.='<option  '.$selected.' value="'.$value['physician_Reffer_id'].'">'.$value['refName'].' ('.$addrs.')'.'</option>';
								}
							}
						break;
						case 3:
						case 4:
							//$gmPCP_arr[]
							if(count($mutiRefPhy[$phy_type]) >1) {
								foreach($row as $value) {
                                    $addrs=$value['Address1'];
                                    if($value['Address1']=='')$addrs=$value['Address2'];
									$selected='';
									if(in_array($value['physician_Reffer_id'],$tmpArr))$selected='selected';
                                    if($gmPCP)
									$gmPCPoption_str.='<option  '.$selected.' value="'.$value['physician_Reffer_id'].'">'.$value['refName'].' ('.$addrs.')'.'</option>';
								}
							}
						break;
					}
				}
			}

		?>
		
		<?php if($gmPCPoption_str!='') {?>
			<div class="<?php echo $class1; ?> primcareprov">
			<div><?php echo "Primary Care Provider"; ?></div>
            <div class="clearfix"></div>
				<select name="chkPCP[]" id="chkPCP" class="selectpicker physDropDowns" data-actions-box="true"  style="color:black" onChange="addFaxSelectedPhy(this)" multiple>
					
					<?php echo $gmPCPoption_str;?>
				</select>
			</div>
		<?php } else { ?>
			<div class="<?php echo $class1; ?> primcareprov">
            <div><?php if($gmPCP){ echo "Primary Care Provider"; }?></div>
            <div class="clearfix"></div>
            <?php $chkPCPCheck = ""; if($_REQUEST['chkPCP']){$chkPCPCheck = "checked";} if($gmPCP){  ?> 
            <div class="checkbox">
            <input type="checkbox" name="chkPCP" id="chkPCP" onClick="javascript: selDeSelAllChkBox(this,'<?php echo $gmPCP."@@".$pcpFax; ?>');" value="<?php echo $gmPCP; ?>" <?php echo $chkPCPCheck; ?> data-mail="direct" data-check = "false" data-hiddenfield = "chkPCP_hidd"/>
            <label for="chkPCP" class="text-nowrap <?php if(in_array($rfPcpId,$refPhyDirectIdChkArr)) echo "text_purple"; ?>"   >
            <?php echo $gmPCP; ?>
            </label>
            </div>
            <input type="hidden" name="chkPCP_hidd" id="chkPCP_hidd" value="<?php echo $rfPcpId; ?>"/>
                <?php } ?>
        </div>
		<?php } ?>
		
		<?php if($rfFNameoption_str!='') {?>
			<div class="<?php echo $class; ?> refpys">
			<div><?php echo "Referring Physician"; ?></div>
            <div class="clearfix"></div>
				<select name="chkRefPhy[]" id="chkRefPhy"  class="selectpicker physDropDowns"  data-actions-box="true"   style="color:black" onChange="addFaxSelectedPhy(this)" multiple>
					
					<?php echo $rfFNameoption_str;?>
				</select>
			</div>		
		<?php 
         } else {
			 
        if($rfFName){
        ?>
        <!--=======REFERRING PHYSICIAN WORK========================== -->
            
        <div class="<?php echo $class; ?> refpys">
            <div><?php if($rfFName){ echo "Referring Physician"; }?>	</div>
            <div class="clearfix"></div>
            <?php $chkrefPhyCheck = ""; if($_REQUEST['chkRefPhy']){$chkrefPhyCheck = "checked";} if($rfFName){ 
                $ref_phy_status = $OBJCommonFunction->get_ref_phy_del_status($rfId);
                $ref_phy_class = " ";
                if($ref_phy_status) {
                    $ref_phy_class = " red-font ";
                }
                ?> 
            <div class="checkbox" >
            
            <input type="checkbox" id="chkRefPhy" name="chkRefPhy"  onClick="javascript: selDeSelAllChkBox(this,'<?php echo $rfLName.", ".$rfFName."@@".$rfFax; ?>');" value="<?php echo $OBJCommonFunction->getRefPhyName($rfId); //echo $rfTitle." ".$rfLName.", ".$rfFName." ".$rfMName; ?>" <?php echo $chkrefPhyCheck; ?> data-mail="direct" data-check="false"  data-hiddenfield = "chkRefPhy_hidd" />		
            <label for="chkRefPhy" class="text-nowrap <?php echo $ref_phy_class;?> <?php if(in_array($rfId,$refPhyDirectIdChkArr)) echo "text_purple"; ?>"><?php echo $OBJCommonFunction->getRefPhyName($rfId);?></label>
            </div>
            <input type="hidden" name="chkRefPhy_hidd" id="chkRefPhy_hidd" value="<?php echo $rfId; ?>" />
            <?php }?>
        </div>
        <?php }
		 }
	  echo '</div>';
		?>
		<?php if($coManPhyoption_str!='') {?>
			<div class="<?php echo $class; ?> refpys"  style="background-color:#8C84EB">
			<div><?php echo "Co-Managed Physician"; ?></div>
            <div class="clearfix"></div>
				<select name="chkCoManPhy[]" id="chkCoManPhy"  class="selectpicker physDropDowns"  data-actions-box="true"  style="color:black" onChange="addFaxSelectedPhy(this)" multiple>
					
					<?php echo $coManPhyoption_str;?>
				</select>
			</div>		
		<?php 
         } else {
		
	    if($coManPhyId){
        ?>
        <!-- =======CO MANAGE PHYSICIAN WORK========================== -->
        <div class="col-sm-12 refpys" style="background-color:#8C84EB">		
                <?php if($coManPhyId){ echo "Co-Managed Physician"; }?>
                <?php $chkCoManPhyCheck = ""; if($_REQUEST['chkCoManPhy']){$chkCoManPhyCheck = "checked";} if($rfCoManFName){ ?> 
                <div class="checkbox">			
                <input type="checkbox" name="chkCoManPhy" id="chkCoManPhy" onClick="javascript: selDeSelAllChkBox(this,'<?php echo $rfCoManLName.", ".$rfCoManFName."@@".$rfCoManFax; ?>');" value="<?php echo $OBJCommonFunction->getRefPhyName($coManPhyId); //echo $rfTitle." ".$rfLName.", ".$rfFName." ".$rfMName; ?>" <?php echo $chkCoManPhyCheck; ?> data-mail="direct" data-check = "false" data-hiddenfield="chkCoManPhy_hidd"/>
                <label for="chkCoManPhy" class="text-nowrap <?php if(in_array($coManPhyId,$refPhyDirectIdChkArr)) echo "text_purple"; ?>" ><?php echo $OBJCommonFunction->getRefPhyName($coManPhyId);?></label>					
                </div><input type="hidden" name="chkCoManPhy_hidd" id="chkCoManPhy_hidd" value="<?php echo $coManPhyId?>" />	
                <?php 
                }
                ?>	
        </div>	
        <?php }
		 }
		 $chkOtherCheck = $chkOtherVal = ""; if($_REQUEST['cbkOther']){$chkOtherCheck = "checked"; $chkOtherVal = $_REQUEST['txtOther'];}
		?>
      
      <div class="clearfix mt5"></div>
        <!--=======OTHER REFERRING PHYSICIAN WORK====================-->
        <div class="col-sm-12">
            <div class="checkbox">
            <input type="checkbox" name="cbkOther" id="cbkOther" onClick="javascript: selDeSelAllChkBox(this);" value="cbkOther" <?php echo $chkOtherCheck; ?> data-mail="direct" data-hiddenfield="hiddtxtOther" data-check = "false" data-input = "txtOther">  <label for="cbkOther">Other Ref Phy(Last, First Name)</label>
            </div>
        </div>
        <div class="col-sm-12">
            <input type="hidden" name="hiddtxtOther" id="hiddtxtOther" value="<?php echo $_REQUEST['hiddtxtOther']; ?>">
            <input type="text" class="form-control" name="txtOther" id="txtOther" value="<?php echo $chkOtherVal; ?>"  onKeyUp="loadPhysicians(this,'hiddtxtOther','','send_fax_number','','hiddselectReferringPhy','send_fax_number','selectReferringPhy','hiddtxtOtherFax');" onFocus="loadPhysicians(this,'hiddtxtOther','','send_fax_number','','hiddselectReferringPhy','send_fax_number','selectReferringPhy');" placeholder="Last, First Name" >
            <input type="hidden" name="hiddtxtOtherFax" id="hiddtxtOtherFax" value="<?php echo $_REQUEST['hiddtxtOtherFax']; ?>">
        </div>
        
        <!--=======CC1 WORK====================-->		
        <div class="col-sm-4"><label for="cc1">Cc1</label>
            <select name="cc1" id="cc1" onChange="javascript:selAddrCCFun(this,'TDcc1OtherId','cc1Other','hidd_cc1Other');"  class="select minimal">
                <option value="" selected >Select</option>
                <?php if($ptRefPhyNme) {?><option data-cc1ref="<?php echo $ref_primary_care_id;?>" value="<?php echo $ref_primary_care_id.'@@'.$refPhysicianFaxArr[$ref_primary_care_id].'@@'.$ptRefPhyNme;?>" <?php if($cc1==$ref_primary_care_id) { echo "selected"; }?> <?php if($refPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptRefPhyNme;?></option><?php }?>
               
			   <?php if($ptPcpPhyNme) {?><option data-cc1ref="<?php echo $pcp_primary_care_phy_id;?>" value="<?php echo $pcp_primary_care_phy_id.'@@'.$refPhysicianFaxArr[$pcp_primary_care_phy_id].'@@'.$ptPcpPhyNme;?>" <?php if($cc1==$pcp_primary_care_phy_id) { echo "selected"; }?> <?php if($pcpPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptPcpPhyNme;?></option><?php } ?>
				
                <?php if($ptCoManPhyNme) {?><option data-cc1ref="<?php echo $co_man_physician_id;?>" value="<?php echo $co_man_physician_id.'@@'.$refPhysicianFaxArr[$co_man_physician_id].'@@'.$ptCoManPhyNme;?>" <?php if($cc1==$co_man_physician_id) { echo "selected"; }?> <?php if($coPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptCoManPhyNme;?></option><?php } ?>
                <option value="Other" <?php if(($cc1=='Other') && ($templateId)){ echo 'Selected';  }?>>Other</option>
            </select> 
        
            <?php $cc1OthrDisp = "hidden";if(($cc1=='Other') && ($templateId)){ $cc1OthrDisp = "";  }?>
            <div class="form-group col-sm-12 <?php echo $cc1OthrDisp;?>" id="TDcc1OtherId" >
                <label for="cc1Other">Other Cc1</label>	
                <input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1" value="<?php echo $_REQUEST['hiddselectReferringPhyCc1'];?>">
                <input type="text" name="cc1Other" id="cc1Other" value="<?php echo $cc1Other;?>" class="form-control" onKeyUp="loadPhysicians(this,'hidd_cc1Other','','send_fax_numberCc1','','hiddselectReferringPhyCc1','send_fax_numberCc1','selectReferringPhyCc1');" onFocus="loadPhysicians(this,'hidd_cc1Other','','send_fax_numberCc1','','hiddselectReferringPhyCc1','send_fax_numberCc1','selectReferringPhyCc1');">
                <input type="hidden" name="hidd_cc1Other" id="hidd_cc1Other" value="<?php echo $hidd_cc1Other;?>" >
            </div>
        </div>
        <!--=======CC2 WORK====================-->		
        <div class="col-sm-4"> <label for="cc2">Cc2</label>
            <select  name="cc2" id="cc2"  onChange="javascript:selAddrCCFun(this,'TDcc2OtherId','cc2Other','hidd_cc2Other');" class="select minimal">
                <option value="" selected >Select</option>
                <?php if($ptRefPhyNme) {?><option data-cc2ref="<?php echo $ref_primary_care_id;?>" value="<?php echo $ref_primary_care_id.'@@'.$refPhysicianFaxArr[$ref_primary_care_id].'@@'.$ptRefPhyNme;?>" <?php if($cc2==$ref_primary_care_id) { echo "selected"; }?>  <?php if($refPhyDirectEmailID) { echo 'class="text_purple"'; }?> ><?php echo $ptRefPhyNme;?></option><?php } ?>
                <?php if($ptPcpPhyNme) {?><option data-cc2ref="<?php echo $pcp_primary_care_phy_id;?>" value="<?php echo $pcp_primary_care_phy_id.'@@'.$refPhysicianFaxArr[$pcp_primary_care_phy_id].'@@'.$ptPcpPhyNme;?>" <?php if($cc2==$pcp_primary_care_phy_id) { echo "selected"; }?>  <?php if($pcpPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptPcpPhyNme;?></option><?php } ?>
                <?php if($ptCoManPhyNme) {?><option data-cc2ref="<?php echo $co_man_physician_id;?>" value="<?php echo $co_man_physician_id.'@@'.$refPhysicianFaxArr[$co_man_physician_id].'@@'.$ptCoManPhyNme;?>" <?php if($cc2==$co_man_physician_id) { echo "selected"; }?>  <?php if($coPhyDirectEmailID) { echo 'class="text_purple"'; }?> ><?php echo $ptCoManPhyNme;?></option><?php } ?>
                <option value="Other" <?php if(($cc2=='Other') && ($templateId)){ echo 'Selected';  }?>>Other</option>
            </select>
        
            <?php $cc2OthrDisp = "hidden";if(($cc2=='Other') && ($templateId)){ $cc2OthrDisp = "";  }?>
            <div class="form-group col-sm-12 <?php echo $cc2OthrDisp;?>" id="TDcc2OtherId">
                <label for="cc2Other">Other Cc2</label>
                <input type="text" name="cc2Other" id="cc2Other" value="<?php echo $cc2Other;?>"  class="form-control" onKeyUp="loadPhysicians(this,'hidd_cc2Other','','send_fax_numberCc2','','hiddselectReferringPhyCc2','send_fax_numberCc2','selectReferringPhyCc2');" onFocus="loadPhysicians(this,'hidd_cc2Other','','send_fax_numberCc2','','hiddselectReferringPhyCc2','send_fax_numberCc2','selectReferringPhyCc2');">
                <input type="hidden" name="hidd_cc2Other" id="hidd_cc2Other" value="<?php echo $hidd_cc2Other;?>" >
            </div>
        </div>	
        <!--=======CC3 WORK====================-->	
        <div class="col-sm-4"><label for="cc3">Cc3</label>
            <select  name="cc3" id="cc3"  onChange="javascript:selAddrCCFun(this,'TDcc3OtherId','cc3Other','hidd_cc3Other');" class="select minimal">
            <option value="" selected >Select</option>
            <?php if($ptRefPhyNme) {?><option data-cc3ref="<?php echo $ref_primary_care_id;?>" value="<?php echo $ref_primary_care_id.'@@'.$refPhysicianFaxArr[$ref_primary_care_id].'@@'.$ptRefPhyNme;?>" <?php if($cc3==$ref_primary_care_id) { echo "selected"; }?>  <?php if($refPhyDirectEmailID) { echo 'class="text_purple"'; }?> ><?php echo $ptRefPhyNme;?></option><?php } ?>
            <?php if($ptPcpPhyNme) {?><option data-cc3ref="<?php echo $pcp_primary_care_phy_id;?>" value="<?php echo $pcp_primary_care_phy_id.'@@'.$refPhysicianFaxArr[$pcp_primary_care_phy_id].'@@'.$ptPcpPhyNme;?>" <?php if($cc3==$pcp_primary_care_phy_id) { echo "selected"; }?>  <?php if($pcpPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptPcpPhyNme;?></option><?php } ?>
            <?php if($ptCoManPhyNme) {?><option data-cc3ref="<?php echo $co_man_physician_id;?>" value="<?php echo $co_man_physician_id.'@@'.$refPhysicianFaxArr[$co_man_physician_id].'@@'.$ptCoManPhyNme;?>" <?php if($cc3==$co_man_physician_id) { echo "selected"; }?>  <?php if($coPhyDirectEmailID) { echo 'class="text_purple"'; }?>  ><?php echo $ptCoManPhyNme;?></option><?php } ?>
            <option value="Other" <?php if(($cc3=='Other') && ($templateId)){ echo 'Selected';  }?>>Other</option>
            </select>
        
            <?php $cc3OthrDisp = "hidden";if(($cc3=='Other') && ($templateId)){ $cc3OthrDisp = "";  }?>
            <div class="form-group col-sm-12 <?php echo $cc3OthrDisp;?>" id="TDcc3OtherId">		
                <label for="cc3Other">Other Cc3</label>
                <input type="text" name="cc3Other" id="cc3Other" value="<?php echo $cc3Other;?>"  class="form-control" onKeyUp="loadPhysicians(this,'hidd_cc3Other','','send_fax_numberCc3','','hiddselectReferringPhyCc3','send_fax_numberCc3','selectReferringPhyCc3');" onFocus="loadPhysicians(this,'hidd_cc3Other','','send_fax_numberCc3','','hiddselectReferringPhyCc3','send_fax_numberCc3','selectReferringPhyCc3');">
                <input type="hidden" name="hidd_cc3Other" id="hidd_cc3Other" value="<?php echo $hidd_cc3Other;?>" >
            </div>
        </div>
      
		<div class="clearfix mt5"></div>
      
        <!--=======CONSULT TEMPLATE DROPDOWN WORK====================-->
        <div class="col-sm-4">
			<label for="templateList">Template</label>
            <select name="templateList" id="templateList" onChange="return selectTempFn();" class="select minimal">
                <option value="" selected >Select</option> 
                <?php
                    unset($conditionArr);
                    $getConsultTempQry = imw_query("SELECT * FROM ".$table." order by consultTemplateName");
                    while($getConsultTempRow = imw_fetch_assoc($getConsultTempQry)){
                    $consultLeter_id = $getConsultTempRow['consultLeter_id'];
                    $consultTemplateName = $getConsultTempRow['consultTemplateName'];
                ?>
                    <option value="<?php echo $consultLeter_id.'!~!'.$consultTemplateName; ?>" 
                <?php
    
                    if($templateId){ 
                    if($consultLeter_id == $templateId) 
                        echo "SELECTED";
                    }
                ?> >
                <?php echo $consultTemplateName; ?></option>
                <?php
                }
            ?>	
            <option value="Other" <?php if(($selectedList=='Other') && ($patient_consult_id)){ echo 'Selected';  }?>>Other</option>
            </select> 
            <div class="form-group col-sm-12 <?php if(($selectedList=='Other') && ($patient_consult_id)){ echo ''; }else{ echo 'hidden'; }?>" id="tempNameTd" >
                <label for="patientTempName">Template</label>
                <input type="text" name="patientTempName" id="patientTempName" <?php //echo //$templateName; ?>  class="form-control">			
            </div>
        </div>
        
        <!--=======CCDA OPTION================-->
        <div class="col-sm-4">
			<label>&nbsp;</label>
			<div class="checkbox ">
				<input type="checkbox" name="ccda" id="ccda" value="1">
				<label for="ccda">CCDA</label>
			</div>
			<div class="checkbox ">
				<input type="checkbox" name="communication" id="communication" value="1">
				<label for="communication">Communications</label>
			</div>
        </div>	
        <div class="clearfix"></div>
        <div class="col-sm-8 pd5">
         <!-- <button type="button" class="btn btn-primary">Voice 2 Text</button> -->
         <button type="button" id="preview_sel_template" class="btn btn-success hide" value="Preview" onClick="get_template_preview()">Preview</button>
        </div>
        <div class="clearfix"></div>
        <!--=======PREVIOUS CONSULT================-->		
        <?php
		$rowPrevConsult = $patient_consult_id = $templateName = $cur_date = $patient_consult_letter_to = "";
		$patientId = $_SESSION['patient'];
		$getPrevConsultTempQry = "SELECT patient_consult_id, templateName, templateData, status,operator_id, cur_date, patient_consult_letter_to FROM `patient_consult_letter_tbl` WHERE patient_id=$patientId AND status=0 ORDER BY cur_date desc";
		//echo $getPrevConsultTempQry;
		$resPrevConsultTempQry = imw_query($getPrevConsultTempQry);
		if(imw_num_rows($resPrevConsultTempQry)>0){
		?>
			<div>
				<div>
					<!--<button class="btn btn-default" type="button" data-toggle="modal" data-target="#prev_consult_modal">
						Previous Consult
					</button> -->
					
					<!-- Previous Consult Modal -->
					<div id="prev_consult_modal" class="modal" role="dialog">
					  <div class="modal-dialog">

						<!-- Modal content-->
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"></h4>
						  </div>
						  <div class="modal-body">
							<div class="row" style="max-height:400px;overflow-y:auto;overflow-x:hidden;">
							<table class="table table-bordered">
								<tr>
									<th class="text-nowrap" >Date</th>
									<th>Template Name</th>
									<th class="text-nowrap">Referring Physician</th>
								</tr>
								<?php 		
								while($rowPrevConsult= imw_fetch_array($resPrevConsultTempQry)){
										$patient_consult_id = $rowPrevConsult['patient_consult_id'];
										$patient_previous_consult_id = $rowPrevConsult['patient_consult_id'];
										$templateName = $rowPrevConsult['templateName'];
										$cur_date = trim(date('m-d-y'.' h:i',strtotime($rowPrevConsult['cur_date'])));
										$patient_consult_letter_to = $rowPrevConsult['patient_consult_letter_to'];
								?>
								<tr onClick="get_prev_consult_pdf('<?php echo $patient_previous_consult_id; ?>');">
									<td class="text-nowrap"><?php echo $cur_date; ?></td>
									<td><?php echo $templateName; ?></td>
									<td class="text-nowrap"><?php echo $patient_consult_letter_to; ?></td>
								</tr>
							   <?php } ?>
							</table>
						   </div>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			</div>
		<?php
		}			
		?>
        </div>
        </div>
        </div>
	</div>
	
    <div class="col-sm-9 pt10">
		<!--<div class="text-center">-->
		<div class="text-center-bkp">
			<textarea id="FCKeditor1" name="FCKeditor1" class="ckeditor_textarea">
				<?php  if(!strtoupper(substr(PHP_OS, 0, 3))=='LIN') { echo utf8_decode($consultTemplateData); }else{ echo $consultTemplateData; } ?>
			</textarea>
		</div>
	</div>
    </div>	
</div>
</div>
	<footer id="module_buttons"  class="footer text-center" style="padding: 0px;background: #eaeff5; paddding-bottom:10px;" >
	<div class="row" id="module_buttons">
		<center>
		<input type="submit" class="btn btn-success" name="saveBtn" id="saveBtn"  value="<?php if(!$tempId)echo "Done"; else echo "Update" ?>">
		<?php if($tempId){ ?><td><input onClick="document.pdf_file.submit();" type="button" class="btn btn-success" name="PrintBtn" id="PrintBtn"  value="Print" ></td><?php } ?>
		
		<input type="button" class="btn btn-success" id="refreshTempalate"  value="Refresh"  onClick="selectTempFn();">
		<input type="button" name="hold_btn" id="hold_btn" onClick="hold_dr_sig();" class="btn btn-success" value="On Hold for:" >
		<?php
			if(is_updox('fax') || is_interfax()) {
		?>
		<input type="button" name="send_fax_btn" id="send_fax_btn" onClick="return sendFaxFun('<?php echo addslashes($_REQUEST["templateList"]);?>');" class="btn btn-success" value="Send Fax" >
		<?php
		}
		if(is_updox('fax')) {
		?>
		<input type="button" name="send_fax_Log" id="send_fax_Log" onClick="show_cl_popup('L')" class="btn btn-success" value="Fax Log">
		<?php
		}
		?>
		<input type="hidden" name="template_id_fax" id="template_id_fax" value="'<?php echo addslashes($_REQUEST["templateList"]);?>'">
		<?php if($direct_exist == 1){?>
			<input type="button" name="send_direct_btn" id="send_direct_btn" class="btn btn-success" value="Send Direct" onClick="validate_direct();" disabled>
		<?php } ?>
		<input type="submit" class="btn btn-success" name="saveBtn" id="savePrintPDFBtn" data-id="<?php echo $patient_consult_id; ?>" value="<?php echo "Save and Print"; ?>">
		
		<input type="button" class="btn btn-danger" id="cancel"  value="<?php if(!$tempId) echo "Cancel"; else echo "Close"; ?>"  onClick="window.close();">
		</center>
	</div>	
	</footer>
<?php
	}
?>
<!-- Pop up send fax -->
<div id="send_fax_divModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
   
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Send Fax</h4>
      </div>
      <div class="modal-body" >
	
	<!-- content -->
	<div   id="faxDiv"  >
		<?php
		$refPhyName=$refPhyFax="";
		if($_REQUEST['chkPCP']){
		$refPhyName=$gmPCP;
		$refPhyFax=$pcpFax; 
		}else if($_REQUEST['chkRefPhy']){
		$refPhyName=$rfLName.", ".$rfFName;
		$refPhyFax=$rfFax; 
		}else if($_REQUEST['chkCoManPhy']){
		$refPhyName=$rfCoManLName.", ".$rfCoManFName;
		$refPhyFax=$rfCoManFax; 	
		}else if($_REQUEST['cbkOther']){
		$refPhyName=$_REQUEST['txtOther'];
		$refPhyFax=$_REQUEST['hiddtxtOtherFax']; 
		}else{
		if($rfLName || $rfFName)
		$refPhyName=$rfLName.", ".$rfFName;
		$refPhyFax=$rfFax; 
		}
		
		$fax_subject="";
		if(defined("fax_subject")){ $fax_subject=constant("fax_subject"); }
		
		?>
		<input type="hidden" name="preffered_reff_fax" id="preffered_reff_fax" value="<?php echo $refPhyName."~||~".$refPhyFax; ?>"/>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Subject:</label>
				<div class="col-sm-10">			
				<input type="text"  name="send_fax_subject" id="send_fax_subject" class="form-control" placeholder="Subject" value="<?php echo trim($fax_subject." ".$patName); ?>"/>
				</div>
			</div>
		</div>
		
    <div class="clearfix mb5"></div>
		<div class="multiPhyHtml">
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Referring&nbsp;Phy:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy" value="<?php echo $_REQUEST['hiddselectReferringPhy'];?>">
				<input type="text" name="selectReferringPhy"  id="selectReferringPhy" onKeyUp="loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number','','','send_fax_number');" value="<?php echo $refPhyName; ?>" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
				<div class="col-sm-4">			
				<input type="text"  name="send_fax_number" id="send_fax_number" class="form-control" value="<?php echo $refPhyFax; ?>" onchange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Cc1:</label>
				<div class="col-sm-4">			
				<input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1" value="<?php echo $_REQUEST['hiddselectReferringPhyCc1'];?>">
				<input type="text" name="selectReferringPhyCc1"  id="selectReferringPhyCc1" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc1','','send_fax_numberCc1','','','send_fax_numberCc1');" value="<?php echo $_REQUEST['selectReferringPhyCc1'];?>" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc1" id="send_fax_numberCc1" class="form-control" value="<?php echo $_REQUEST['send_fax_numberCc1'];?>" onchange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Cc2:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyCc2" id="hiddselectReferringPhyCc2" value="<?php echo $_REQUEST['hiddselectReferringPhyCc2'];?>">
				<input type="text" name="selectReferringPhyCc2"  id="selectReferringPhyCc2" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc2','','send_fax_numberCc2','','','send_fax_numberCc2');" value="<?php echo $_REQUEST['selectReferringPhyCc2'];?>" class="form-control">
				</div>
			</div>
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc2" id="send_fax_numberCc2" class="form-control" value="<?php echo $_REQUEST['send_fax_numberCc2'];?>" onChange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Cc3:</label>
				<div class="col-sm-4">
				<input type="hidden" name="hiddselectReferringPhyCc3" id="hiddselectReferringPhyCc3" value="<?php echo $_REQUEST['hiddselectReferringPhyCc3'];?>">
				<input type="text" name="selectReferringPhyCc3"  id="selectReferringPhyCc3" onKeyUp="loadPhysicians(this,'hiddselectReferringPhyCc3','','send_fax_numberCc3','','','send_fax_numberCc3');" value="<?php echo $_REQUEST['selectReferringPhyCc3'];?>" class="form-control">
				</div>
			</div>	
		
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
				<div class="col-sm-4">
				<input type="text"  name="send_fax_numberCc3" id="send_fax_numberCc3"  class="form-control" value="<?php echo $_REQUEST['send_fax_numberCc3'];?>" onChange="set_fax_format(this,'<?php echo $GLOBALS['phone_format']; ?>');" autocomplete="off">
				</div>
			</div>
		</div>
		</div>
		
	</div>
	<!-- content -->
	
      </div>
      <div class="modal-footer">		
	<input type="button" class="btn btn-success" value="Send Fax" id="send_close_btn" >
	<input type="button" class="btn btn-danger" value="Close" id="fax_cancel_btn" data-dismiss="modal">
      </div>
    </div>

  </div>
</div>
<!-- Pop up send fax -->

<!-- pop ups hold to physician -->
<div id="hold_to_phy_divModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
   
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Physician for Hold</h4>
      </div>
      <div class="modal-body" >
	
	<!-- content -->
	<center>
		<select name="hold_to_physician" id="hold_to_physician" class="form-control" >
			<option value="">--SELECT--</option>
			<?php echo $OBJCommonFunction->dropDown_providers('','');?>
		</select>
		<input type="hidden" name="hidd_hold_to_physician" id="hidd_hold_to_physician">		
	</center>
	<!-- content -->
	
      </div>
      <div class="modal-footer">		
	<input type="button" class="btn btn-success" value="Save &amp; Close" id="hold_phy_save_btn" >
	<input type="button" class="btn btn-danger" value="Close" data-dismiss="modal">
      </div>
    </div>

  </div>
</div>
<!-- pop ups hold to physician -->

<!-- pop ups send direct -->
<div id="div_send_directModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

   
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Options to exclude</h4>
      </div>
    <div class="modal-body" >
	
	<!-- content -->
	<fieldset>
	<legend class="mb5">
	<!--<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions[]"  value="mu_data_set" onClick="select_all_mu(this)"/>-->Common MU Data Set
	</legend>
	
	<div class="row">
		<div class="col-sm-3">
			<div class="checkbox"><input type="checkbox" title="Medications" id="ccdDocumentOptions_med" name="ccdDocumentOptions[]" value="mu_data_set_medications"  class="mudata" ><label for="ccdDocumentOptions_med">Medications</label></div>

			<div class="checkbox"><input type="checkbox" title="Allergies" id="ccdDocumentOptions_al" name="ccdDocumentOptions[]" value="mu_data_set_allergies"   class="mudata" ><label for="ccdDocumentOptions_al">Allergies List</label></div>

			<div class="checkbox"><input type="checkbox" title="Problem List" id="ccdDocumentOptions_pl" name="ccdDocumentOptions[]" value="mu_data_set_problem_list"  class="mudata" ><label for="ccdDocumentOptions_pl">Problem List</label></div>

		</div>
		<div class="col-sm-3">
			<div class="checkbox"><input type="checkbox" title="Smoking Status" id="ccdDocumentOptions_ss" name="ccdDocumentOptions[]" value="mu_data_set_smoking" class="mudata"><label for="ccdDocumentOptions_ss">Smoking Status</label></div>

			<div class="checkbox"><input type="checkbox" title="Care Plan Field" id="ccdDocumentOptions_cpf" name="ccdDocumentOptions[]" value="mu_data_set_ap" class="mudata"><label for="ccdDocumentOptions_cpf">Care Plan Field</label></div>

			<div class="checkbox"><input type="checkbox" title="Procedures" id="ccdDocumentOptions_proc" name="ccdDocumentOptions[]" value="mu_data_set_superbill" class="mudata"><label for="ccdDocumentOptions_proc">Procedures</label></div>

		</div>
		<div class="col-sm-4">
			<div class="checkbox"><input type="checkbox" id="ccdDocumentOptions_vs" title="Vital Sign" name="ccdDocumentOptions[]" value="mu_data_set_vs" ><label for="ccdDocumentOptions_vs">Vital Sign</label></div>

			<div class="checkbox"><input type="checkbox" id="ccdDocumentOptions_ctm" title="Care Team Members" name="ccdDocumentOptions[]" value="mu_data_set_care_team_members" class="mudata"><label for="ccdDocumentOptions_ctm">Care Team Members</label></div>

			<div class="checkbox"><input type="checkbox" id="ccdDocumentOptions_lab" title="Lab" name="ccdDocumentOptions[]" value="mu_data_set_lab"  class="mudata"><label for="ccdDocumentOptions_lab">Lab</label></div>
		</div>
	</div>	
	</fieldset>	
	
	<fieldset class="mt20">
	<legend class="mb5 ">Other</legend>	
		<div class="row">
			<div class="col-sm-5">
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_pi" value="provider_info" /><label for="ccdDocumentOptions_pi">Provider's Information</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_dlv" value="location_info" /><label for="ccdDocumentOptions_dlv">Date and Location of visit</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_rv"  value="reason_for_visit" /><label for="ccdDocumentOptions_rv">Reason for visit</label></div>
				<div class="checkbox">  <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_fa" value="future_appointment" /><label for="ccdDocumentOptions_fa">Future Appointments</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_ci"  value="clinical_instruc" /><label for="ccdDocumentOptions_ci">Clinical Instructions</label></div>
			</div>
			<div class="col-sm-7">
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_rop" value="provider_referrals" /><label for="ccdDocumentOptions_rop">Referrals to other providers</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_imdv"  value="visit_medication_immu" /><label for="ccdDocumentOptions_imdv">Immunizations and Medications during visit</label></div>
				<div class="checkbox"><input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_fst"  value="future_sch_test" /><label for="ccdDocumentOptions_fst">Future Scheduled Tests</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_dtp"  value="diagnostic_tests_pending" /><label for="ccdDocumentOptions_dtp">Diagnostioc Tests Pending</label></div>
				<div class="checkbox"> <input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_rpda"  value="recommended_patient_decision_aids" /><label for="ccdDocumentOptions_rpda">Recommended patient decision aids</label></div>
			</div>
		</div>
	</fieldset>
	
	<!-- content -->
    </div>
    <div class="modal-footer">		
		<input type="button" class="btn btn-success" value="Submit" onClick="send_direct();" id="btn_submit_sd" name="btn_submit_sd" >
		<input type="button" class="btn btn-danger" value="Close" data-dismiss="modal">
    </div>
    </div>
  </div>
</div>
<!-- pop ups send direct -->
<!-- loading -->
<div id="div_loading_image" class="text-center">
	<div class="loading_container">
		<div class="process_loader"></div>
    	<div id="div_loading_text" class="text-info"></div>
	</div>
</div>
<!-- loading -->
<script>
  $(function () {
	$('[data-toggle="tooltip"]').tooltip();
	//CKEDITOR.config.height = $('.consltr').height()-88;  
	
	
	$('body').on('change','#templateList',function(){
		if($(this).val() == ''){
			$('#preview_sel_template').toggleClass('hide');
		}
	});
	
	if($('#templateList').val() != ''){
		$('#preview_sel_template').toggleClass('hide');
	}
	
	$('.consltr').height($('.exammain').height()-53);
	hh = $('.consltr').height();
	$('#FCKeditor1').redactor({
		buttonSource: true,
		imageUpload: '<?php echo $GLOBALS['webroot']; ?>/library/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: hh-80, 
		maxHeight: hh-80,

	});
  
  });

 </script> 
</body>
</html> 