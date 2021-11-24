<!DOCTYPE HTML>
<html>
<head>	
	<title><?php echo $pg_title;?></title>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet" type="text/css">
	<style>
		#FinalHtmlContainer_ORG{width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;display:none;}
		#FinalHtmlContainer{width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;}
		#finalHtmlForPrinting{display:none;}
		#div_smart_tags_options{top:200px;left:400px; width:300px; z-index:999;}
	</style>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js" type="text/javascript"></script>
    <script language="javascript" type="text/javascript">
	var WRP = '<?php echo $GLOBALS['webroot']; ?>';	
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
<body class="body_c" style="background:#ffffff" topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false;">
	<form name="printMRForm" id="printMRForm" method="post" action="<?php echo $pg_submit_uri; ?>">
	<input type="hidden" name="printOptionType" value="<?php echo($printOptionType);?>">
	<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
	<input type="hidden" value="0" name="faxSubmit" id="faxSubmit">
	<input type="hidden" value="" name="selectedReferringPhy" id="selectedReferringPhy">
	<input type="hidden" value="" name="sendFaxNumber" id="sendFaxNumber">
	<input type="hidden" name="faxchartIdPRS" value="<?php echo($_REQUEST['chartIdPRS']);?>">
	<input type="hidden" name="ChartNoteImagesStringFinal" value="<?php if(count($ChartNoteImagesString_Org)>0){ echo implode(",",$ChartNoteImagesString_Org); } ?>">		
	<div id='FinalHtmlContainer_ORG' ><?php echo $getFinalHTMLForGivenMR_Org;?></div>
	<table cellpadding="2"  cellspacing="2" width="100%" border="0">
		<tr>
			<td align="left" colspan='4'>
				<div id='FinalHtmlContainer' >
					<?php echo($getFinalHTMLForGivenMR);?>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" colspan='4' height="15">
			<!-- To Store Final HTMl FOR Printing-->
				<textarea name="finalHtmlForPrinting" id="finalHtmlForPrinting" rows="20" cols="40" >
				
				</textarea>
			<!-- To Store Final HTMl FOR Printing-->
			</td>
		</tr>
		<tr height="15">
			<td width="15%" align="left"></td>
			<td width="12%" align="right"></td>
			<td width="26%" align="left" ><input type="submit" class="dff_button btn btn-success" id="directPrint" name="directPrint" title="Print"  value="Print" style="height:32px;" onClick="javascript: return submitPrintRequest();">
				<?php
					//SEND FAX BUTTON ADDED
					if((is_updox('fax') || is_interfax()) && $_REQUEST['chartIdPRS']){
				?>
					<input type="button" class="btn btn-success" style="margin-left:30px;height:32px;" id="butIdCancel" title="Send Fax" value="Send Fax" onclick="sendFaxFun();" autocomplete="off">
				<?php } ?>
			</td>
			<td width="37%" align="left" class="text_10b" ><input type="button" class="dff_button btn btn-danger" id="butIdCancel" style="height:32px;" title="Cancel"   value="Cancel" onClick="window.close();"></td>

		</tr>
	</table>
	</form>
	<!-- SMART TAG MODEL STARTS HERE-->
	<div id="div_smart_tags_options" class="modal" role="dialog">
		<div class="modal-dialog modal-sm">
	  	<!-- Modal content-->
	    <div class="modal-content">
	    
	    	<div class="modal-header bg-primary">
	      	<button type="button" class="close" onclick="$('#div_smart_tags_options').hide();">X</button>
	        <h4 class="modal-title" id="modal_title">Smart Tag Options</h4>
	     	</div>
	      
	      <div class="modal-body pd0" style="min-height:250px; max-height:400px; overflow:hidden; overflow-y:auto;">
	      	<div class="loader"></div>
	      </div>
	      	
	      <div class="modal-footer pd5"></div>
	      
	  	</div>
	  </div>
	</div>
	<!-- SEND FAX MODEL STARTS HERE-->
	<div id="send_fax_div" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative">
			<div class="modal-content" style="z-index:99999999">
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
		if(typeof(win_op)=='undefined'){var win_op='';}
		if(win_op==''){
			$('.cls_smart_tags_link').mouseup(function(e){
				if(e.button==0 || e.button==2){ //click event.
					$('#smartTag_parentId').val($(this).attr('id'));
					smart_tag_current_object = $(this);
					display_tag_options(e);
				}			
			});
		}else if(win_op==1){
			$('.cls_smart_tags_link').click(function(e){
					$('#smartTag_parentId').val($(this).attr('id'));
					smart_tag_current_object = $(this);
					display_tag_options(e);
				}
			);
		}
	});
	
	function display_tag_options(e){
		//css({'left':e.pageX,'top':e.pageY})
		$('#div_smart_tags_options').show();
		
		var parentId = $('#smartTag_parentId').val();
		/*
		ArrtempParentId = parentId.split('_');
		parentId = ArrtempParentId[0];
		*/
		var x = (top.imgPath) ? top.imgPath : top.JS_WEB_ROOT_PATH;
		$.ajax({
			type: "GET",
			url: "<?php echo $GLOBALS['webroot']; ?>/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId+'&is_return=1',
			dataType:"json",
			success: function(resp){
				$('#div_smart_tags_options .modal-title').html(resp.title);
				$('#div_smart_tags_options .modal-body').html(resp.data);
				$('#div_smart_tags_options .modal-footer').html(resp.footer_btn);
				$("object").hide();
			}
		});
		
		$('.close').on('click', function (e) {
			 $("object").show();
	    });
		
	}
	
	
	function replace_tag_with_options(){
		var strToReplace = '';
		var parentId = $('#smartTag_parentId').val();
		
		var arrSubTags = document.all.chkSmartTagOptions;
		$(arrSubTags).each(function (){
			if( $(this).is(':checked') ){
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
			var hiddclass = $(smart_tag_current_object).attr('id');
			$('.'+hiddclass).val($(smart_tag_current_object).text());
	/*		
			RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
			var strippedData = $('#hold_temp_smarttag_data').html();
			strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
	*/		
			$('#div_smart_tags_options').hide();
			
			//Enabling Object Signature
			$("object").show();
			
			
			$("object").css({"visibility":"visible"});
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
				$("#div_loading_image").show(); 
			 	var selectedRefPhy = $('#selectReferringPhy').val();
				var sendFaxNo = $('#send_fax_number').val();
				$('#selectedReferringPhy').val(selectedRefPhy);
				$('#sendFaxNumber').val(sendFaxNo);
				document.getElementById("faxSubmit").value=1;
				document.getElementById("directPrint").click();
			} 
		});
	}	
	
	window.moveTo(0,0);
	window.resizeTo(1200,screen.height);
	</script>
	</body>
</html>