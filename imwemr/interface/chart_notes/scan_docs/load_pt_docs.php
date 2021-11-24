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

//-----------LOAD PT DOCS TEMPLATES
//-----------FILES INCLUSION--------------------------------
include_once("../../../config/globals.php");
include_once($GLOBALS['fileroot']."/interface/chart_notes/chart_globals.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/PnTempParser.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/wv_functions.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Printer.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartNote.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartDraw.php");
include_once($GLOBALS['fileroot']."/library/classes/Functions.php");

//-----------EDITOR FILE INCLUDED----------------------------
include_once($GLOBALS['fileroot']."/library/ckeditor.php");

//-----------OBJECTS-----------------------------------------
$OBJsmart_tags 	= new SmartTags;
$objManageData 	= new ManageData;
$objParser 		= new PnTempParser;

$form_id = $_SESSION['form_id']; //DATE OF SERVICE

//-----------MED HX FUNCTION CALL----------------------------
$medHx=$objParser->getMedHx_public($_SESSION['patient']);

//-----------MED HX -> OCULAR OTHER FIELD DATA---------------
$ocularother=$objParser->getMedHx_public($_SESSION['patient'],"","Ocular_Other");

//-----------GET FORM ID FROM CHART MASTER TABLE ------------
if(!$form_id)
{
	$formIdQry ="SELECT 
					id
				FROM 
					`chart_master_table`
				WHERE 
					patient_id='".$_SESSION['patient']."' 
				ORDER BY 
					date_of_service 
				DESC 
					LIMIT 0,1";
	$formIdRes = get_array_records_query($formIdQry);
	$form_id   = $formIdRes[0]['id'];
}

//-----------DIFFERENT PRINTING MODE------------------------

if( $pth && $mode == "ins" ) 
{
	$docPath = base64_decode($pth);
}

if( $pth && $mode == "intrprttns" ) 
{
	$id = base64_decode($_GET["pth"]);
	$pid = base64_decode($_GET["pid"]);
	$fid = base64_decode($_GET["fid"]);	
	$exam_name = base64_decode($_GET["exam_name"]);
	$oChartDraw = new ChartDraw($pid, $fid, $exam_name);
	$oChartDraw->print_report_interp($id);
	exit();
}

//-----------CALL FROM DOCS TAB => PT-DOCS------------------
if(empty($temp_id) === false and $mode == 'load')
{
	$insCaseTypeId="";  
	
	//-------GET PT DOCS TEMPLATE--------------------------
	$templQry = "SELECT 
					pt_docs_template_name,
					pt_docs_template_content,
					enable_footer 
				FROM
					`pt_docs_template` 
				WHERE 
					pt_docs_template_status = '0' 
				AND
					pt_docs_template_id = '$temp_id'
				";
	$templQryRes = get_array_records_query($templQry);
	$pt_docs_template_name 		= $templQryRes[0]['pt_docs_template_name'];
	$pt_docs_template_content 	= $templQryRes[0]['pt_docs_template_content'];
	$enable_footer 				= $templQryRes[0]['enable_footer'];
	
	//-------GET PATIENT DETAILS--------------------------
	$patientId	= $_SESSION['patient'];
	$patQry = 	"SELECT 
					patient_data.*, 
					pos_facilityies_tbl.facilityPracCode,
					heard_about_us.heard_options , 
					employer_data.name emp_name, 
					employer_data.street as emp_street, 
					employer_data.street2 as emp_street2, 
					employer_data.state as emp_state,
					employer_data.postal_code as emp_postal_code, 
					employer_data.city as emp_city,
					users.lname as users_lname, 
					users.fname as users_fname, 
					users.mname as users_mname, 
					users.pro_suffix as users_suffix,
					date_format(patient_data.date, '".get_sql_date_format()."') as reg_date,
					date_format(patient_data.DOB, '".get_sql_date_format()."') as patient_dob
				FROM
					`patient_data`
					LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = default_facility
					LEFT JOIN heard_about_us ON patient_data.heard_abt_us = heard_about_us.heard_id
					LEFT JOIN employer_data ON employer_data.pid = patient_data.id
					LEFT JOIN users ON users.id = patient_data.providerID
				WHERE 
					patient_data.id = '$patientId'";
	$patQryRes = get_array_records_query($patQry);
	
	//-------BELOW CODE USED TO DISPLAY THE PATIENT INSURANCE FOR VARIABLES BASED ON PATIENT APPOINTMENT -DOCS TAB => PT-DOCS TEMPLATES
	$schDataQry = "	SELECT 
						id as schId,
						case_type_id as insCaseTypeId 
					FROM 
						`schedule_appointments` 
					WHERE 
						sa_patient_id = '".$patientId."'
					AND 
						sa_app_start_date >= current_date()
					AND 
						sa_patient_app_status_id NOT IN(203,201,18,19,20,3)
					ORDER BY 
						sa_app_start_date ASC
					LIMIT 0,1";
	$schDataRes = imw_query($schDataQry);
	if(imw_num_rows($schDataRes)>0)
	{
		$schDataRow 	= imw_fetch_assoc($schDataRes);
		$insCaseTypeId	= $schDataRow['insCaseTypeId'];
	}
	
	$templateData = $objParser->getPtDocsAssPlan($pt_docs_template_content,$patQryRes[0]['pid'],$form_id);
	
	//-------getDataParsed => fromDocsTabPTDoc IDENTIFIER TO IDENTIFY REQUEST IS RECIEVING FROM DOCS TAB => PT-DOCS
	//-------$insCaseTypeId => GET INSURANCE CASE ID BASED ON PATIENT APPOINTMENT
	$templateData = $objParser->getDataParsed($pt_docs_template_content,$patientId,$form_id,'','','','','','','pt_docs','fromDocsTabPTDoc',$insCaseTypeId);
	$templateData = str_ireplace('{MED HX}',$medHx,$templateData);
	
	if(strstr($templateData,"{ASSESSMENT OD}") || strstr($templateData,"{ASSESSMENT OS}") || strstr($templateData,"{PLAN OD}") || strstr($templateData,"{PLAN OS}"))
	{
		
		$ap_val_arr = $objParser->getPtDocsAssPlanODOSOUVals($pt_docs_template_content,$patQryRes[0]['pid'],$form_id);	
		
		$ass_od_val=$ap_val_arr[0];
		$ass_os_val=$ap_val_arr[1];
		$plan_od_val=$ap_val_arr[2];
		$plan_os_val=$ap_val_arr[3];
		
		$templateData = str_ireplace('{ASSESSMENT OD}',$ass_od_val,$templateData);
		$templateData = str_ireplace('{ASSESSMENT OS}',$ass_os_val,$templateData);
		$templateData = str_ireplace('{PLAN OD}',$plan_od_val,$templateData);
		$templateData = str_ireplace('{PLAN OS}',$plan_os_val,$templateData);
	}
	
	//-------CALL TO REPLACE VARIABLES USED IN TEMPLATES----
	$templateData = $objManageData->__loadTemplateData($templateData,$patQryRes[0], '0','','', $read_from_database=1);
	
	//-------REPLACING SMART TAGS (IF FOUND) WITH LINKS----
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	if($arr_smartTags)
	{
		foreach($arr_smartTags as $key=>$val)
		{
			$templateData = str_ireplace("[".$val."]",'<a id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$templateData);	
		}	
	}
	//-------SMART TAG REPLACEMENT END---------------------
}

$ptDocsPrint = '';

//-----------SECTION PRINT MODE CALL FROM DOCS->PT-DOCS/COLLECTIONS SAVED TEMPLATES
//-----------SECTION FACESHEET MODE CALL FROM SCHEDULER -> FACESHEET
if(empty($temp_id) === false && ($mode == 'print' || $mode=="facesheet"))
{
	//-------GET SAVED TEMPLATE DATA----------------------
	$apptId="";
	$patientId = $_SESSION['patient'];
	
	global $phpServerIP;
	global $phpHTTPProtocol;
	
	//-------APPT ID FROM SCHEDULER FACEHSHEET PRINT POP-UP THROUGH URL
	if($_REQUEST['apptId']){ $apptId = $_REQUEST['apptId'];}
	
	if($mode=="print")
	{
		//---COLLECTION SAVED LETTER PRINTS--------------
		if($type=="collection")
		{
			$templQry = "SELECT 
							template_content 
						FROM 
							`pt_docs_collection_letters`
						WHERE 
							id ='$temp_id'";
			$templQryRes = get_array_records_query($templQry);
			$templateData 	= $templQryRes[0]['template_content'];
			$templateData = preg_replace("/[{}]/", "" ,$templateData);
			preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$templateData);						
		}
		else
		{
			$templQry = "SELECT 
							template_content,
							pt_enable_footer 
						FROM
							`pt_docs_patient_templates`
						WHERE 
							pt_docs_patient_templates_id = '$temp_id'";
			$templQryRes = get_array_records_query($templQry);
			
			//$pt_docs_template_name 	= $templQryRes[0]['pt_docs_template_name'];
			$pt_docs_template_content 	= $templQryRes[0]['template_content'];
			$pt_enable_footer 			= $templQryRes[0]['pt_enable_footer'];
		}
	}
	else if($mode=="facesheet") 
	{
		$templQry = "SELECT
						pt_docs_template_name, 
						pt_docs_template_content,
						enable_footer 
					FROM
						`pt_docs_template`
					WHERE	
						pt_docs_template_status = '0' 
					AND 
						pt_docs_template_id = '$temp_id'
					";
		$templQryRes = get_array_records_query($templQry);
		
		$pt_docs_template_name 		= $templQryRes[0]['pt_docs_template_name'];
		$pt_docs_template_content 	= $templQryRes[0]['pt_docs_template_content'];
		$enable_footer 				= $templQryRes[0]['enable_footer'];
		
		//-------OCULAR OTHER VARIABLE REPLACEMENT-------------
		$ocularother=$objParser->getMedHx_public($_SESSION['patient'],"","Ocular_Other");
		$pt_docs_template_content = str_ireplace("{OCULAR_OTHER}",$ocularother,$pt_docs_template_content);
		$pt_docs_template_content = $objParser->getPtDocsAssPlan($pt_docs_template_content,$patientId,$form_id);	
		$pt_docs_template_content = $objParser->getVisionPriIns($pt_docs_template_content,$patientId,$type='primary');	
		$pt_docs_template_content = $objParser->getVisionSecIns($pt_docs_template_content,$patientId,$type='secondary');	
		$pt_docs_template_content = $objParser->__getApptInfo($pt_docs_template_content,$patientId);	
	}
	
	//----------GET PATIENT DETAILS---------------------------
	if($type!="collection")
	{
		$patQry = "	SELECT 
						patient_data.*, 
						pos_facilityies_tbl.facilityPracCode,
						heard_about_us.heard_options , 
						heard_about_us_desc.heard_desc, 
						employer_data.name emp_name, 
						employer_data.street as emp_street, 
						employer_data.street2 as emp_street2, 
						employer_data.state as emp_state,
						employer_data.postal_code as emp_postal_code, 
						employer_data.city as emp_city,
						users.lname as users_lname, 
						users.fname as users_fname, 
						users.mname as users_mname,
						date_format(patient_data.date, '".get_sql_date_format()."') as reg_date,
						date_format(patient_data.DOB, '".get_sql_date_format()."') as patient_dob
					FROM 
						`patient_data`
						LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = default_facility
						LEFT JOIN heard_about_us ON patient_data.heard_abt_us = heard_about_us.heard_id
						LEFT JOIN heard_about_us_desc ON heard_about_us_desc.heard_id = heard_about_us.heard_id
						LEFT JOIN employer_data ON employer_data.pid = patient_data.id
						LEFT JOIN users ON users.id = patient_data.providerID
					WHERE 
						patient_data.id = '$patientId'";
		$patQryRes = get_array_records_query($patQry);
		//-------------REPLACE VARIABLES USED INTO TEMPLATES------
		
		$templateData = str_ireplace("{PATIENT_NICK_NAME}",ucwords($patQryRes[0]['nick_name']),$pt_docs_template_content);
		
		if($mode=="facesheet" && (!empty($apptId) && $apptId>0))
		{
			$pt_arrival_time = "";
			//---GET patient actual arrival time ---
			$sql_appt_time="SELECT 
								DATE_FORMAT(dateTime,'%h:%i %p') as app_arrival_time
							FROM 
								previous_status
							WHERE 
								sch_id = '".$apptId."'
							AND 
								status = 4
							ORDER BY 
								dateTime ASC LIMIT 0,1";
			$exe_qry = imw_query($sql_appt_time);
			if(imw_num_rows($exe_qry) > 0 )
			{
				$result = imw_fetch_assoc($exe_qry);
				$pt_arrival_time = $result['app_arrival_time'];
			}
			$templateData = str_ireplace("{ARRIVAL_TIME}",$pt_arrival_time,$templateData);			
		}
		
		$templateData = $objManageData->__loadTemplateData($templateData,$patQryRes[0], '0','','', $read_from_database=1,$apptId);
		
		$footerAdd='';
		$footerPageNum = '<tr><td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td></tr>';
		
		if($pt_enable_footer=="yes") 
		{
			//--------FIXED CODE FOR CEC SERVER-------------------
			$footerAdd ='<tr>
							<td style="text-align:center;width:100%" class="text_value">
								7001 S Edgerton Rd, Suite B&nbsp;&nbsp;&nbsp; Brecksville, OH&nbsp; 44141&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 440-526-1974&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 800-875-0300
							</td>
						</tr>';
			$footerPageNum = '';
		}
		else if($pt_enable_footer=="disable_page_no")
		{
			$footerPageNum = '';
		}
		if($enable_footer=="disable_page_no")
		{
			$footerPageNum = '';
		}
		
		//--------TEMPLATE DATA SET TO SAVE INTO DATABASE FOR SAVING FRONT DESK FACESHEET RECORD
		$template_content ="";
		if($mode=="facesheet" && (!empty($apptId) && $apptId>0))
		{
			$template_content = $templateData;	
		}
		
		//--------ADD PAGE SETTING FOR PDF PRINTING ----
		$templateData = <<<DATA
			<page backtop="-3mm" backbottom="1mm"  backleft="-2mm"  backright="0mm">
			<page_footer>
			<table style="width: 100%;">
				$footerAdd 
				$footerPageNum
			</table>
			</page_footer>$templateData</page>
DATA;
	}
    
	//-----------FACESHEET DATA INSERT TO SAVE FACESHEET PRINT RECORD FROM FRONT DESK SECTION FOR IMEDIC MONITOR
	if($mode=="facesheet" && (!empty($apptId) && $apptId>0) && !empty($temp_id))
	{	
		$temp_id=0;
		$templateDataArr = array();
		$templateDataArr['patient_id'] = $patientId;
		$templateDataArr['pt_doc_primary_template_id'] = $temp_id;
		$templateDataArr['pt_enable_footer'] = $enable_footer;
		$templateDataArr['template_content'] = $template_content;
		
		//-------REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. */
		$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
		$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
		$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
		$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
		
		//-------SMART TAG REPLACEMENT END--------------------------
		$templateDataArr['created_date'] = date('Y-m-d h:i:s');
		$templateDataArr['operator_id'] = $_SESSION['authId'];
		$templateDataArr['template_delete_status'] = 0;
		$templateDataArr['print_from'] = 'scheduler';
		$templateDataArr['appt_id'] = $apptId;
		
		AddRecords($templateDataArr, 'pt_docs_patient_templates');
	}
	//----------END------------------------------------------------
	
	//----------CREATE HTML FILE FOR PDF PRINTING------------------
	$fileName = 'pt_docs_print'.$_SESSION['authId'];
	
	//----------IMAGES REPLACEMENT WORK STARTS HERE----------------
	$templateData = str_ireplace($protocol.$phpServerIP,'',$templateData);
	$templateData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$templateData);
	$templateData = str_ireplace($GLOBALS['webroot'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$templateData);
	$templateData = str_ireplace('/'.$GLOBALS['php_server'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$templateData);
	$templateData = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$templateData);
	$templateData = str_ireplace('../../interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$templateData);
	$templateData = str_ireplace($protocol.$phpServerIP.$GLOBALS['webroot']."/library/redactor/images/",$GLOBALS['webroot']."/redactor/images/",$templateData);
	$templateData = str_ireplace(rtrim($webServerRootDirectoryName,'/'),'',$templateData);
	$templateData = str_ireplace($webroot."/interface/reports/new_html2pdf/","",$templateData);
	$templateData = str_ireplace($webroot."/interface/common/new_html2pdf/","",$templateData);
	$templateData = rawurldecode($templateData); //FOR DECODING %## CODES LIKE %20 => ' '
	$templateData = str_ireplace("&nbsp;","&amp;nbsp;",$templateData);
	$templateData = str_ireplace("&amp;nbsp;"," ",$templateData);
	
	//----------REPLACE FONT-FAMILY INTO TEMPLATE DATA-----------
	$templateData = preg_replace('/font-family.+?;/', "", $templateData);
	
	if(empty($GLOBALS['webroot']))
	{ 
		$templateData = str_ireplace('/../../data/','../../data/',$templateData);
	}
	//----------DATA WRITE INTO HTML FILE HERE------------------
	$file_path = write_html(html_entity_decode($templateData));
	$ptDocsPrint = 'yes';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pt.Docs</title>
<!-------------CSS FILES INCLUSION------------------------------->
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/redactor/redactor.css" rel="stylesheet" type="text/css">
<!-------------JS FILES INCLUSION------------------------------->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
<script src="<?php echo $library_path; ?>/redactor/redactor.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	var ptDocsPrint = '<?php echo $ptDocsPrint;?>';
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	if(ptDocsPrint == 'yes') {
		html_to_pdf('<?php echo $file_path; ?>','p','',true);
	}
	
	function saveTemplateData(){
		if(top.fmain.ifrm_FolderContent) {
			top.fmain.ifrm_FolderContent.document.template_frm.submit();
		}
	}	
	
	function set_elem_height(){
		var header_position = $('#first_toolbar',top.document).position();
		var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight());
		window_height = parseInt(window_height + 20);
		
		if(document.getElementById('pt_docs_template_content')) {
			CKEDITOR.replace( 'pt_docs_template_content', { width:'100%'} );
			CKEDITOR.config.height = window_height;
		}
		
		if( document.getElementById('ins-card-div')){
			document.getElementById('ins-card-div').style.height=window_height+'px';
		}
	}
	
	var smart_tag_current_object = new Object;
	
	//Display Options available for theselected smart tag in editor
	function display_tag_options(anchorObj){
		var anchorid = anchorObj.attr('id');
		var parentId = $('#smartTag_parentId').val();
		var selSmartTag = anchorObj.text();
		
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/admin/documents/ajax_handler.php',
			type:'GET',
			data:{'call_from':'get_editor_tags', 'current_tab':'smart_tags', 'smart_id':parentId, 'tag_name':selSmartTag},
			dataType:'JSON',
			beforeSend:function(){
				top.show_loading_image('show');
			},
			success:function(response){
				if(response || response !== false){
					if(Object.keys(response).length){
						var strHtml = '';
						var headerStr = '';
						var contentStr = '';
						var footerStr = '';
						
						if(response.tagName) headerStr = 'Select Options for :- '+response.tagName;
						if(response.tagValues){
							contentStr += '<div class="row">';
							contentStr += 	'<table class="table table-bordered table-condensed">'
								$.each(response.tagValues, function(id, val){
									contentStr += '<tr>';
									contentStr += 	'<td>';
									contentStr += 		'<div class="checkbox">';
									contentStr += 			'<input type="checkbox" class="editor_tags" id="check_editor_'+val+'_'+id+'" value="'+val+'" />';
									contentStr += 			'<label for="check_editor_'+val+'_'+id+'">'+val+'</label>';	
									contentStr += 		'</div>';
									contentStr += 	'</td>';
									contentStr += '</tr>';
								});
							contentStr += 	'</table>';
							contentStr += '</div>';
							
							footerStr += '<button type="button" class="btn btn-success" data-id="editor_tags_modal" id="editor_tag_done_btn" data-anchor="'+anchorid+'">Done</button>';
						}
						
						show_modal('editor_tags_modal',headerStr,contentStr,footerStr);
					}
				}
			},
			complete:function(){
				top.show_loading_image('hide');
			}
		});
	}
	
	function setSmartTagOpts(btnObj){
		var DataArr = btnObj.data();
		var modalObj = '';
		if(DataArr.id && $('#'+DataArr.id).length) modalObj = $('#'+DataArr.id);
		
		if(modalObj){
			var valArr = [];
			modalObj.find('input[class="editor_tags"]:checked').each(function(id, elem){
				var value = $(elem).val();
				if(value) valArr.push(value);
			});
			var strValue = '';
			if(valArr.length) strValue = valArr.join(', ');
			else{top.fAlert('Please select a option');return false;}
			
			if(strValue){
				var ckEditor = CKEDITOR.instances['pt_docs_template_content'].document.$;
				var anchorObj = $(ckEditor).find('.cls_smart_tags_link[id="'+DataArr.anchor+'"]');
				if(anchorObj){
					anchorObj.text(strValue);
				}
			}
		}
		
		modalObj.modal('hide');
	}
		
	$(document).ready(function(){
		top.show_loading_image('hide');
		set_elem_height();
		if(document.getElementById('pt_docs_template_content'))
		{
			top.btn_show("PTD");
		
			var editor = CKEDITOR.instances['pt_docs_template_content'];
			editor.on( 'instanceReady', function(e) { 
				editor.addCommand("showTags", {
					exec : function( editor )
					{		
						sel = editor.getSelection();
						var node = editor.document.getBody().getFirst();
						var parent = node.getParent();
						var sellink = CKEDITOR.plugins.link.getSelectedLink(editor);
						
						//var selection = editor.getSelection();
						var selectedElement = $(sellink.$);
						document.getElementById('smartTag_parentId').value = sellink.getAttribute("id");
						display_tag_options(selectedElement);
					}
				});
				var showTags = {
					label : "Show Tag Options",
					command : 'showTags',
					group : 'anchor'
				};
				editor.contextMenu.addListener( function( element, selection ) {
					return { 
					showTags : CKEDITOR.TRISTATE_OFF 
					};
				});
				editor.addMenuItems({
					showTags : {
					label : "Show Tag Options",
					command : 'showTags',
					group : 'anchor',
					order : 1
					}
				});
			}); 		
			
			$('body').on('click', '#editor_tag_done_btn', function(){
				setSmartTagOpts($(this));
			});
		}
	});
	
	$(window).resize(function(){
		set_elem_height();
	});
		
</script>
<style>
	#editor_tags_modal .modal-footer{padding:5px;text-align: center;}
</style>
</head>
<body onUnload="top.btn_show('');">
<?php if( $mode == 'ins' && $docPath) { 
	echo '
	<div style="width:100%; overflow:auto;" id="ins-card-div">
		<img src="'.$docPath.'" style="max-width:100%;" id="ins-card" />
	</div>';

}
else { ?>
<form name="template_frm" action="save_pt_docs.php" method="post">
	<input type="hidden" name="template_id" value="<?php echo $temp_id;?>" />
	<input type="hidden" name="enable_footer" value="<?php echo $enable_footer;?>" />
	<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
	<input type="hidden" name="doc_name" id="doc_name" value="<?php echo $_REQUEST["doc_name"];?>">
	<div class="col-xs-12 bg-white">
		<textarea id="pt_docs_template_content" name="pt_docs_template_content" class=""><?php echo stripslashes($templateData); ?></textarea>	
	</div>
</form>
<div id="hold_temp_smarttag_data" class="hide"></div>
<?php } ?>
</body>
</html>