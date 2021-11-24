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
$library_path = $GLOBALS['webroot'].'/library';
?><!DOCTYPE HTML>
<html>
<head>
	<title>Print CL Prescriptions For Patient</title>
	<link href="<?php echo $library_path;?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="<?php echo (constant("REMOTE_SYNC") != 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]['webroot'] : $GLOBALS['webroot'];?>/library/css/common.css">	
	<!-- LOADER CSS -->
	<link href="<?php echo $library_path;?>/css/workview.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
	  <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
// Function Set Value Attribute For All Inputs To Resolve Issues in IE9 Safari//
	
	function setTouchInputs() { 
	var everything = document.getElementsByTagName('input'); 
	var everythinglength = everything.length; 
	for(var i = 0;i<everythinglength;i++) {
		try{
			everything[i].setAttribute('value',everything[i].value);
			}
			catch(e){
				alert(e.message); 
				} 
			}
	} 	
	
// Function Set Value Attribute For All Inputs To Resolve Issues in IE9 Safari//	
// Function Set InnerHTML of Final Output In TextArea And Submit Form To Print//	
function submitPrintRequest(){ 
		setTouchInputs();// Set Value Attribute
		
		<?php 
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		?>			
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer_ORG") ){
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer_ORG").innerHTML;
			return true;
		}else{
		return false;
		
		<?php }else{ ?>
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer") ){
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer").innerHTML;
			return true;
		}else{
		
		return false;
		
		<?php } ?>
	}
}
// Function Set InnerHTML of Final Output In TextArea And Submit Form To Print//	
	</script>
</head>	
<body class="body_c"  bgcolor="#ffffff" topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false;">
	<form name="printMRForm" id="printMRForm" method="post" action="print_patient_contact_lenses.php">
	<input type="hidden" name="printOptionType" value="<?php echo($printOptionType);?>">
	<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
	<input type="hidden" name="ChartNoteImagesStringFinal" value="<?php echo implode(",",$ChartNoteImagesString_Org);?>">		
	<!--SEND FAX HIDDEN FIELDS---------->
	<input type="hidden" value="0" name="faxSubmit" id="faxSubmit">
	<input type="hidden" value="" name="selectedReferringPhy" id="selectedReferringPhy">
	<input type="hidden" value="" name="sendFaxNumber" id="sendFaxNumber">
	<input type="hidden" name="faxworkSheetId" value="<?php echo($_REQUEST['workSheetId']);?>">
	
	<div id='FinalHtmlContainer_ORG' style="width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;display:none;"><?php echo $getFinalHTMLForGivenMR_Org;?></div>
	<table cellpadding="2"  cellspacing="2" width="100%" border="0">
		<tr>
			<td align="left" colspan='4'>
				<div id='FinalHtmlContainer' style="width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;">
						<?php
						    if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
						    $prescriptionTemplateContentData=str_ireplace($webServerRootDirectoryName,$protocol.$myExternalIP."/",$prescriptionTemplateContentData);
							$prescriptionTemplateContentData=html_entity_decode($prescriptionTemplateContentData);
    						echo($prescriptionTemplateContentData);
						?>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" colspan='4' height="15">
				<!-- To Store Final HTMl FOR Printing-->
				<textarea name="finalHtmlForPrinting" id="finalHtmlForPrinting" rows="20" cols="40" style="display:none;">
				
				</textarea>
				<!-- To Store Final HTMl FOR Printing-->
			</td>
		</tr>
	</table>
	<!--<div class="row" id="module_buttons"> -->
	<footer id="module_buttons"  class="footer text-center" style="padding: 0px;paddding-bottom:10px; position:fixed; bottom:20px; left:35%;" >
		<center>
			<input type="submit" class="btn btn-success" id="directPrint" name="directPrint" title="Print"  value="Print" onClick="javascript: return submitPrintRequest();"> &nbsp; &nbsp;
			<?php
				//SEND FAX BUTTON ADDED
				if((is_updox('fax') || is_interfax()) && $_REQUEST['workSheetId']){
			?>
			<input type="button" class="btn btn-success" style="margin-left:30px;height:32px;" id="butIdCancel" title="Send Fax" value="Send Fax" onclick="sendFaxFun();" autocomplete="off"> &nbsp; &nbsp;
			<?php } ?>
			<input type="button" class="btn btn-danger" id="butIdCancel" title="Cancel"   value="Cancel" onClick="window.close();"></td>
		</center>		
	</footer>		
	<!--</div>	-->
	</form>
	<!-- SEND FAX MODEL STARTS HERE-->
	<div id="send_fax_div" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative;">
			<div class="modal-content" style="z-index:99999999;">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Send Fax</h4>
				</div>
				<div class="modal-body">
					<div id="faxDiv" class="row">
						<div class="col-sm-6">
							<label>Referring Physician :</label>
							<input type="text" name="selectReferringPhy" id="selectReferringPhy" class="form-control" 
								onkeyup="loadPhysicians(this,'hiddselectReferringPhy','<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>','send_fax_number','','','send_fax_number','','',top.fmain);">
							<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
						</div>
						<div class="col-sm-6">
							<label>Fax Number :</label>
							<input type="text"  name="send_fax_number" id="send_fax_number" class="form-control">	
						</div>	
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="button" id="send_close_btn" class="btn btn-success">Send Fax</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- SEND FAX LOADER IMAGE WHEN HIT SEND FAX BUTTON-->
	<div id="div_loading_image" class="text-center " style="position:absolute;top:200px; z-index:99999999;">
		<div class="loading_container">
			<div class="process_loader"></div>
			<div id="div_loading_text" class="text-info text-center">Please wait, while system is sending fax...</div>
		</div>
	</div>	
	<script type="text/javascript"> 
	var smart_tag_current_object = new Object;
	$(document).ready(function(){
		$("#div_loading_image").hide();
		$('.cls_smart_tags_link').mouseup(function(e){
			if(e.button==2){
				$('#smartTag_parentId').val($(this).attr('id'));
				smart_tag_current_object = $(this);
				display_tag_options(e);
			//	document.oncontextmenu="return false;"    		
			}
			
		});
	});
	
	function display_tag_options(e, obj){
		$('#div_smart_tags_options').css('left',e.pageX);
		$('#div_smart_tags_options').css('top',e.pageY);
		$('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="../../images/ajax-loader.gif">');
		$('#div_smart_tags_options').show();
		var parentId = $('#smartTag_parentId').val();
		$.ajax({
			type: "GET",
			url: "../../library/classes/functions.smart_tags.php?do=getTagOptions&id="+parentId,
			success: function(resp){
				$('#div_smart_tags_options').html(resp);
			}
		});
	}
	
	function replace_tag_with_options(){
		var strToReplace = '';
		var parentId = $('#smartTag_parentId').val();
		
		var arrSubTags = document.all.chkSmartTagOptions;
		$(arrSubTags).each(function (){
			if($(this).attr('checked')){
				if(strToReplace=='')
					strToReplace +=  $(this).val();
				else
					strToReplace +=  ', '+$(this).val();
			}
		});
		//alert(strToReplace);
		
		/*--GETTING FCK EDITOR TEXT--*/
		if(strToReplace!='' && smart_tag_current_object){
			$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
			//$(smart_tag_current_object).html(strToReplace);
	/*		
			RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
			var strippedData = $('#hold_temp_smarttag_data').html();
			strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
	*/		
			$('#div_smart_tags_options').hide();
		}else{
			alert('Select Options');
		}
	}
	//ACTION ONCLICK SEND FAX BUTTON
	function sendFaxFun(){
	$('#send_fax_div').modal('show');
	var getFaxNo = $('#send_fax_number').val();
		$('#send_fax_div #send_close_btn').click(function(){
			if($('#selectReferringPhy').val()==''){
				alert('Please enter referring physician name');
				$('#selectReferringPhy').focus();
				return false;
			}else if($('#send_fax_number').val()==''){
				alert('Please enter fax number');
				$('#send_fax_number').focus();
				return false;
			}else{
				//LOADER
				$("#div_loading_image").show(); 
			 	//SET HIDDEN FIELDS VALUES
				var selectedRefPhy = $('#selectReferringPhy').val();
				var sendFaxNo = $('#send_fax_number').val();
				$('#selectedReferringPhy').val(selectedRefPhy);
				$('#sendFaxNumber').val(sendFaxNo);
				document.getElementById("faxSubmit").value=1;
				//SUBMIT PRINT BUTTON
				document.getElementById("directPrint").click();
			} 
	
		});
	}	
	window.moveTo(0,0);
	window.resizeTo(1200,screen.height);
	</script>
	</body>
</html>