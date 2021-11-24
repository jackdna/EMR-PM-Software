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
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/classes/class.tests.php");
require_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../library/classes/ChartTestPrev.php");
$callFromInterface		= 'admin';
$library_path 			= $GLOBALS['webroot'].'/library';
$objTests				= new Tests;

$elem_test_template_id	= (isset($_GET['tId']) && intval($_GET['tId'])>0) ? intval($_GET['tId']) : 0;

if(isset($_POST['elem_saveForm']) && trim($_POST['elem_saveForm'])=='TemplateTests'){
	$elem_test_template_id 				= intval($_POST['elem_test_template_id']);
	$elem_test_template_version 		= intval($_POST['elem_test_template_version']);
	$elem_test_name 					= trim(strip_tags($_POST['elem_test_name']));
	$elem_test_manager 					= trim(strip_tags($_POST['elem_test_manager']));
	$elem_test_main_options 			= htmlentities(trim($_POST['elem_test_main_options']));
	$elem_test_main_option_mo_counter 	= intval($_POST['elem_test_main_option_mo_counter']);
	$elem_test_main_option_ids 			= htmlentities(trim($_POST['elem_test_main_option_ids']));
	$elem_test_main_option_text			= htmlentities(trim($_POST['elem_test_main_option_text']));
	$elem_test_results 					= htmlentities(trim($_POST['elem_test_results']));
	$elem_test_treatment 				= htmlentities(trim($_POST['elem_test_treatment']));
	$elem_test_treatment_mo_counter 	= intval($_POST['elem_test_treatment_mo_counter']);
	
	if($elem_test_template_id==0){
		$insert_q = "INSERT INTO tests_name SET 
					 test_name			= '$elem_test_name',
					 test_table			= 'test_custom_patient',
					 test_table_pk_id	= 'test_id',
					 patient_key		= 'patientId',
					 phy_id_key			= 'phyName',
					 exam_date_key		= 'examDate',
					 performed_key		= 'performedBy',
					 script_file		= 'test_template_custom_patient.php',
					 test_imaging		= '0',
					 del_status			= '0',
					 t_manager			= '$elem_test_manager',
					 status				= '1',
					 test_type			= '1',
					 version			= '0',
					 temp_name			= '$elem_test_name'
					 ";
		$res1 = imw_query($insert_q);
		if($res1){
			$elem_test_template_id = imw_insert_id();
		
			$insert_q_version = "INSERT INTO tests_version SET 
								 tests_name_id					= '$elem_test_template_id',
								 test_main_options				= '$elem_test_main_options',
								 test_main_option_mo_counter	= '$elem_test_main_option_mo_counter',
								 test_main_options_ids			= '$elem_test_main_options_ids',
								 test_main_options_text			= '$elem_test_main_options_text',
								 test_treatment					= '$elem_test_treatment',
								 test_treatment_mo_counter		= '$elem_test_treatment_mo_counter',
								 test_results					= '$elem_test_results',
								 created_by						= '".$_SESSION['authId']."',
								 created_on						= '".date('Y-m-d H:i:s')."'
								 ";
			$res2 = imw_query($insert_q_version);
			if($res2){//UPDATE BACK IN TABLE tests_name;
				$elem_test_template_version = imw_insert_id();
				$update_test_template = "UPDATE tests_name SET version='$elem_test_template_version' WHERE id='$elem_test_template_id'";
				imw_query($update_test_template);
			}
            
            /*--INSERT ENTRY IN superbill_test TABLE--*/
            $resp2 = imw_query("INSERT INTO superbill_test (test,test_type,tests_name_pkid) VALUES ('".$elem_test_name."','1','".$elem_test_template_id."')");
		}
		
		
	}else{
		//--CHECKING AND GETTING OLD TEST NAME FOR SUPERBILL CPT TABLE
		$superbill_test_id = 0;
		$insert_in_superbill = false;
		$res_get_temp_name = imw_query("SELECT temp_name,test_type FROM tests_name WHERE id='".$elem_test_template_id."' LIMIT 0,1");
		if($res_get_temp_name && imw_num_rows($res_get_temp_name)==1){
			$rs_get_temp_name = imw_fetch_assoc($res_get_temp_name);
			$old_template_name= $rs_get_temp_name['temp_name'];
			$edited_test_type = $rs_get_temp_name['test_type'];
			//GETTING MOST RECENT MATCHING TEST NAME (JUST FOR MORE SAFETY, NOT TO DESTROY ANY OLD DATA);
			if($edited_test_type=='1'){
				$res_get_sup_test_id = imw_query("SELECT id FROM superbill_test WHERE test='".$old_template_name."' AND test_type='1' ORDER BY id DESC LIMIT 0,1");
				if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==1){
					$rs_get_sup_test_id = imw_fetch_assoc($res_get_sup_test_id);
					$superbill_test_id = $rs_get_sup_test_id['id'];
				}else if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==0){
					/*--INSERT IN superbill_test TABLE--*/
					$insert_in_superbill = true;
				}
			}					
		}
		
		$update_q = "UPDATE tests_name SET 
					 t_manager			= '$elem_test_manager',
					 temp_name			= '$elem_test_name' 
					 WHERE id = '$elem_test_template_id'";
		$res1 = imw_query($update_q);
		if($res1){
			if($elem_test_template_version){
				$update_q_version = "UPDATE tests_version SET 
									 test_main_options				= '$elem_test_main_options',
									 test_main_option_mo_counter	= '$elem_test_main_option_mo_counter',
									 test_treatment					= '$elem_test_treatment',
									 test_treatment_mo_counter		= '$elem_test_treatment_mo_counter',
									 test_main_options_ids			= '$elem_test_main_options_ids',
									 test_main_options_text			= '$elem_test_main_options_text',
									 test_results					= '$elem_test_results' 
									 WHERE id = '$elem_test_template_version'";
				$res2 = imw_query($update_q_version);
			}else{
				$insert_q_version = "INSERT INTO tests_version SET 
									 tests_name_id					= '$elem_test_template_id',
									 test_main_options				= '$elem_test_main_options',
									 test_main_option_mo_counter	= '$elem_test_main_option_mo_counter',
									 test_treatment					= '$elem_test_treatment',
									 test_treatment_mo_counter		= '$elem_test_treatment_mo_counter',
									 test_main_options_ids			= '$elem_test_main_options_ids',
									 test_main_options_text			= '$elem_test_main_options_text',
									 test_results					= '$elem_test_results',
									 created_by						= '".$_SESSION['authId']."',
									 created_on						= '".date('Y-m-d H:i:s')."'
									 ";
				$res2 = imw_query($insert_q_version);
				if($res2){//UPDATE BACK IN TABLE tests_name;
					$elem_test_template_version = imw_insert_id();
					$update_test_template = "UPDATE tests_name SET version='$elem_test_template_version' WHERE id='$elem_test_template_id'";
					imw_query($update_test_template);
				}
			}
			
			$res_get_sup_test_id = imw_query("SELECT id FROM superbill_test WHERE test='".$old_template_name."' AND test_type='1' ORDER BY id DESC LIMIT 0,1");
			if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==1){
				$rs_get_sup_test_id = imw_fetch_assoc($res_get_sup_test_id);
				$superbill_test_id = $rs_get_sup_test_id['id'];
			}else if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)>1){
				$res_get_sup_test_id = imw_query("SELECT id FROM superbill_test WHERE tests_name_pkid='".$elem_test_template_id."' AND test_type='1' AND tests_name_pkid != '0' ORDER BY id DESC LIMIT 0,1");
				$rs_get_sup_test_id = imw_fetch_assoc($res_get_sup_test_id);
				$superbill_test_id = $rs_get_sup_test_id['id'];
			}else if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==0){
				/*--INSERT IN superbill_test TABLE--*/
				$insert_in_superbill = true;
			}
			
			if($superbill_test_id>0){
				$res_update_superbill_test = imw_query("UPDATE superbill_test SET test='".$elem_test_name."',tests_name_pkid='".$elem_test_template_id."' WHERE id='".$superbill_test_id."'");
			}else if($insert_in_superbill==true && $superbill_test_id==0){
				$res_update_superbill_test = imw_query("INSERT INTO superbill_test SET test='".$elem_test_name."', test_type='1',tests_name_pkid='".$elem_test_template_id."'");
			}
			
		}
		
	}
}



if($elem_test_template_id){
	$this_test_properties	= $objTests->get_table_cols_by_test_table_name($elem_test_template_id,'id');

	$test_table_name					= $this_test_properties['test_table'];
	$elem_test_name=$elem_testOtherName = $this_test_properties['temp_name'];
	$elem_test_template_version			= $this_test_properties['version'];
	
	$this_version_data					= $objTests->get_template_test_version_data($elem_test_template_id);

	//$elem_test_template_id 				= intval($_POST['test_template_id']);
	//$elem_test_template_version 		= intval($_POST['test_template_version']);
	//$elem_test_name 					= trim(strip_tags($_POST['elem_test_name']));
	$elem_test_main_options 			= $this_version_data['test_main_options'];
	$elem_test_main_options			= str_ireplace('&lt;td&gt;','&lt;td class=&quot;custom_test_main_options&quot;&gt;',$elem_test_main_options);
	//	var_dump($elem_test_main_options);
	$elem_test_main_option_mo_counter 	= $this_version_data['test_main_option_mo_counter'];
	$elem_test_main_options_ids 		= $this_version_data['test_main_options_ids'];
	$elem_test_main_options_text		= $this_version_data['test_main_options_text'];
	$elem_test_results 					= $this_version_data['test_results'];
	$elem_test_treatment 				= $this_version_data['test_treatment'];
	$elem_test_treatment_mo_counter 	= $this_version_data['test_treatment_mo_counter'];
	
}

$elem_examDate = get_date_format(date('Y-m-d'));
$elem_examTime = date('Y-m-d H:i:s'); //time();
$elem_opidTestOrderedDate = ""; // $elem_examDate;

//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

//Assign Chart Notes specific user Type by checking the list
if(in_array($userType,$GLOBALS['arrValidCNPhy'])){
	$userType = 1;
}else if(in_array($userType,$GLOBALS['arrValidCNTech'])){
	$userType = 3;
}

//-----ORDER BY USERS------------
$order_by_users									= $objTests->get_order_by_users('cn');

//--------OPERATOR NAME----
$elem_operatorId = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $_SESSION["authId"] : "";
$elem_operatorName = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $objTests->getPersonnal3($elem_operatorId) : "";

//Performed Id
if(empty($elem_performedBy) && (($userType == 1 || $userType == 12) || ($userType == 3))){
	$elem_performedBy = $logged_user;
}

//Interpreted By
if(empty($elem_physician)){
	if($userType == '1'){
		$elem_phyName_order = $logged_user;
	}
}

//Current Performed by logged in
$elem_performedByCurr = "";
if(($userType == 1 || $userType == 12) || ($userType == 3)){
	$elem_performedByCurr = (empty($elem_performedBy) || (($userType == 3))) ? $logged_user : $elem_performedBy;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $this_test_screen_name;?></title>
<link href="<?php echo $library_path; ?>/css/tests.css?<?php echo filemtime('../../library/css/tests.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css?<?php echo filemtime('../../library/css/common.css');?>" rel="stylesheet" type="text/css">

<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<!-- Messi Plugin for fancy alerts CSS -->
<!-- DateTime Picker CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
<link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/lightbox/lightbox.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]--> 

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<!-- jQuery's Date Time Picker -->
<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
<!-- Bootstrap -->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

<!-- Bootstrap Selectpicker -->
<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
<!-- Bootstrap typeHead -->
<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js"></script> 
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-menu.min.js"></script>
<script type="text/javascript">
var arrTypeAhead 	= new Array(<?php echo $strTypeAhead;?>); //get TH
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var label_hover_is_live	= 0;
var label_edit_is_live	= 0;
var current_new_control_parent_id = '';
var current_context_element = '';

//---variables below contains data saved/data to save.
var test_main_option_mo_counter = <?php echo intval($elem_test_main_option_mo_counter);?>;
var test_prognosis_mo_counter   = <?php echo intval($elem_test_treatment_mo_counter);?>;
var test_template_changed = false;

$(document).ready(function(e) {
	//TEST RESULT EDITABLE CONTROLS
	$(document).on("mouseenter", ".editable_control", function() {
	  	editable_hover($(this));
	});
	$(document).on("mouseleave", ".editable_control", function() {
	  	cancelMask($(this));
	});
	
	//TEST RESULT EDITABLE LABELES (LEFT SIDE)
	$(document).on("mouseenter", ".editable_label", function() {
	  	editable_label_hover($(this));
	});
	$(document).on("mouseleave", ".editable_label", function() {
	  	cancelMask($(this));
		if(label_edit_is_live==0) label_hover_is_live = 0;
	});
	$(document).on("mouseup", ".custom_click_to_add_control", function() {
	  	current_new_control_parent_id = $(this).parent().prop('id');
	});
	$(document).on("mouseup", ".custom_prognosis, .custom_test_main_options", function() {
	  	current_context_element = $(this);
	});
	$(document).on("click", ".custom_click_to_add_test_options", function() {
	  	current_new_control_parent_id = "test_main_options";
		render_new_control_in_testresult('multi_observations');
	});
	$(document).on("click", ".custom_click_to_add_prognosis", function() {
		current_new_control_parent_id = $(this).parent('div').parent('div.row').prop('id');
		render_new_control_in_testresult('multi_observations');
	});
	$(document).on("keyup",".text_multiobser",function(k){
		if($(this).val().trim()!='')
			check_text_multiobser($(this),k.keyCode);
	});
	$(document).on("blur",".text_multiobser",function(e){
		if($(this).val().trim()!='')
			check_text_multiobser($(this),e);
	});
	$(document).on("change",".text_multiobser",function(k){
		$(this).val($(this).val().trim());
	});
	
	
});

function cancelMask(po){
	if(typeof(po)=='undefined'){$('.mainarea').find('.mask_white').each(function(){$(this).remove()})}
	else{po.find('.mask_white').each(function(){$(this).remove()})}
}

function editable_hover(po){
	var mask_html_control 	 = $('#masking_html_control').html();
	
	po_html = po.html();//present content.
	po_h = (parseInt(po.height())+parseInt(po.css('padding-top'))+parseInt(po.css('padding-bottom')))+'px';
	po_w = (parseInt(po.width())+parseInt(po.css('padding-left'))+parseInt(po.css('padding-right')))+'px';
	po_offset = po.offset();
	po.append(mask_html_control);
	
	$(po).find('.mask_white').css({'top':po_offset.top+'px','left':po_offset.left+'px','width':po_w,'height':po_h});
	
	po.find('a.del_control').click(function(t){
		cancelEdit(); //CANCELLING LABEL EDIT INTERFACE.
		add_new_test_result_control(po);
		po.prop('id');
		po.parent('td').prop('id');
		po.remove();
	});
	po.find('a.del_row').click(function(t){
		delete_test_result_row(po);
	});
	po.find('a.new_control_next').click(function(t){
		add_new_test_result_control(po);
	});
	po.find('a.del_option').click(function(t){
		cancelEdit(); //CANCELLING LABEL EDIT INTERFACE.
		po.remove();
		//ManageCellsInTable('table_prognosis',3);
	});
}

function delete_test_result_row(po){
	cancelEdit(); //CANCELLING LABEL EDIT INTERFACE.
	if(po.parent().parent().prop("tagName")=='TR') po.parent().parent().remove();
	if(po.parent().parent().prop("tagName")=='TD') po.parent().parent().parent().remove();
}

function editable_label_hover(po){
	var mask_html_label		 = $('#masking_html_label').html();
	if(label_hover_is_live==0){
		po_html = $.trim(po.text());//present content
		po_h = (parseInt(po.height())+parseInt(po.css('padding-top'))+parseInt(po.css('padding-bottom')))+'px';
		po_w = (parseInt(po.width())+parseInt(po.css('padding-left'))+parseInt(po.css('padding-right')))+'px';
		po_offset = po.offset();
		po.append(mask_html_label);
		$('.mask_white').css({'position':'absolute','top':po_offset.top+'px','left':po_offset.left+'px','width':po_w,'height':po_h});
		po.find('a.edit_label').click(function(t){
			label_html_form  = '<div class="edit_wrapper">';
			label_html_form += '<table class="table"><tr><td colspan="2"><input id="label_edit_textbox" type="text" class="form-control" value="'+po_html+'"></td></tr>';
			label_html_form += '<tr>';
			label_html_form += '<td class"col-sm-6 text-center"><a href="javascript:ConfirmLabelEdit(\''+po.prop('id')+'\')"><span class="glyphicon glyphicon-ok">CONFIRM</span></a></td>';
			label_html_form += '<td class"col-sm-5 text-center"><a href="javascript:cancelEdit()"><span class="glyphicon glyphicon-remove">CANCEL</span></a></td>';
			label_html_form += '</tr>';
			label_html_form += '</table>';
			cancelMask(po);
			po.append(label_html_form);
			label_edit_is_live = 1;
			$('.edit_wrapper').css({'position':'absolute','top':po_offset.top+'px','left':po_offset.left+'px','width':po_w});
			$('#label_edit_textbox').focus().select();
		});
		label_hover_is_live = 1;
	}		
}

function add_new_test_result_control(po){
	parent_cell_id = po.parent('td').prop('id');
	test_result_control_count =  ($('#'+parent_cell_id+' div.editable_control:last').index())+2;
	
	//INCREASING ROW ID IF THIS EXISTS. (overcoming of sequence imbalance due to delete row action)
	while($('#'+parent_cell_id+'-con_'+test_result_control_count).get(0)){test_result_control_count++;}		

	new_tr_html  = '<div id="'+parent_cell_id+'-con_'+test_result_control_count+'" class="remove_me_before_save">';
	new_tr_html += '	<a href="javascript:void(0);" title="Delete this option" onclick="delete_test_result_new_control_option($(this));" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>';
	new_tr_html += '<span class="custom_click_to_add_control" data-row-id="test_category">click to add new control</span>';
	new_tr_html += '</div>';
	po.after(new_tr_html);
}

function delete_test_result_new_control_option(po){
	td = po.parent('div').parent('td');
	if(td.find('div').length > 1){
		po.parent('div').fadeOut(function(){$(this).remove();
									sync_os_cell_with_od(td.prop('id'));
								});
	}else{
		td.parent('tr').fadeOut(function(){$(this).remove();});
	}
}

function add_new_test_result_row(){
	cancelEdit(); //CANCELLING LABEL EDIT INTERFACE.
	test_result_row_count =  $('table#test_result_table tr:last').index();
	
	//INCREASING ROW ID IF THIS EXISTS. (overcoming of sequence imbalance due to delete row action)
	while($('#row_od_'+test_result_row_count).get(0)){test_result_row_count++;}	
	
	new_tr_html  = '<tr>';
	new_tr_html += '<td id="label_row_'+test_result_row_count+'" class="tdlftpan editable_label"></td>';
	new_tr_html += '<td id="row_od_'+test_result_row_count+'" class="pd5 tstbx tstrstopt">';
	new_tr_html += '<div id="row_od_'+test_result_row_count+'-con_1">';
	new_tr_html += '	<a href="javascript:void(0);" title="Delete this Row" onclick="delete_test_result_row($(this));" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>';
	new_tr_html += '	<span class="custom_click_to_add_control" data-row-id="test_category">click to add new control</span>';
	new_tr_html += '</div>';
	new_tr_html += '</td>';
	new_tr_html += '<td id="row_os_'+test_result_row_count+'" class="pd5 tstbx tstrstopt">';
	new_tr_html += '	&nbsp;';
	new_tr_html += '</td>';
	new_tr_html += '</tr>';
	$('#test_result_table').append(new_tr_html);	
}

function ConfirmLabelEdit(po){
	let = $('#label_edit_textbox').val();
	$('#'+po).text($.trim(let));
	cancelEdit();
}

function cancelEdit(){
	$('.edit_wrapper').each(function(){$(this).remove()});
	label_edit_is_live=0;
	label_hover_is_live=0;
}

function getHTMLofControl(el_type){
	con_counter = current_new_control_parent_id.split('_');
	con_counter = con_counter[con_counter.length-1];
	
	html_val = '';
	switch(el_type){
		case 'textbox':{
			new_control_id = current_new_control_parent_id+'-text'+con_counter;
			html_val = '<input type="text" name="'+new_control_id+'" id="'+new_control_id+'" class="form-control">';
			return html_val;
			break;
		}
		case 'textarea':{
			new_control_id = current_new_control_parent_id+'-desc'+con_counter;
			html_val = '<textarea class="form-control" rows="2" cols="30" style="width:100%; height:50px;" id="'+new_control_id+'" name="'+new_control_id+'"></textarea>';
			return html_val;
			break;
		}
		case 'gradeoptions':{
			new_control_id = current_new_control_parent_id+'-grade'+con_counter;
			html_val  = '<div class="row">';
			html_val += '	<div class="col-sm-3"><label><input type="checkbox" id="'+new_control_id+'_A" name="'+new_control_id+'_A" value="1"><span class="label_txt">Absent</span></label></div>';
			html_val += '	<div class="col-sm-2"><label><input type="checkbox" id="'+new_control_id+'_T" name="'+new_control_id+'_T" value="1"><span class="label_txt">T</span></label></div>';
			html_val += '	<div class="col-sm-2"><label><input type="checkbox" id="'+new_control_id+'_1" name="'+new_control_id+'_1" value="1"><span class="label_txt">+1</span></label></div>';
			html_val += '	<div class="col-sm-2"><label><input type="checkbox" id="'+new_control_id+'_2" name="'+new_control_id+'_2" value="1"><span class="label_txt">+2</span></label></div>';
			html_val += '	<div class="col-sm-2"><label><input type="checkbox" id="'+new_control_id+'_3" name="'+new_control_id+'_3" value="1"><span class="label_txt">+3</span></label></div>';
			html_val += '	<div class="col-sm-1"><label><input type="checkbox" id="'+new_control_id+'_4" name="'+new_control_id+'_4" value="1"><span class="label_txt">+4</span></label></div>';
			html_val += '</div>';
			return html_val;
			break;
		}
		case 'numeric_values':{
			new_control_id = current_new_control_parent_id+'-numeric'+con_counter;
			html_val = '<input type="number" name="'+new_control_id+'" id="'+new_control_id+'" class="form-control">';
			return html_val;
			break;
		}
	}
}

function ManageCellsInTable(table_id,required_cells_in_row){
	total_cells =  $('table#'+table_id+' td').length;
	$('table#'+table_id+' tr').each(function(){
		if($(this).children('td').length < required_cells_in_row){			
			next_row_cell = $(this).next('tr').find('td:first');
			if(typeof(next_row_cell.html())!='undefined'){
				$(this).append('<td class="col-sm-4">'+next_row_cell.html()+'</td>');
				next_row_cell.remove();
			}
		}
	});	
}

function render_new_control_in_testresult(cntrol){
	
	if(cntrol=='multi_observations') {
		$('#modal_multiobservations').one('shown.bs.modal', function (e) {// this handler is detached after it has run once
			window.parent.$('#module_buttons2').hide();
			old_content = $('#modal_multiobservations #tb_multi_observation tbody').html();			
			$('#modal_multiobservations tbody .text_multiobser').get(0).focus();
		}).one('hidden.bs.modal', function(e) {// this handler is detached after it has run once
			window.parent.$('#module_buttons2').show();
			$('#modal_multiobservations #tb_multi_observation tbody').html(old_content);
		}).modal({backdrop: 'static'});
		test_template_changed = true;
		return;
	}

	html_val = getHTMLofControl(cntrol);
	$('#'+current_new_control_parent_id).html(html_val);
	$('#'+current_new_control_parent_id).removeClass('remove_me_before_save').addClass('editable_control');
	od_cell_id = $('#'+current_new_control_parent_id).parent('td').prop('id');
	sync_os_cell_with_od(od_cell_id);
}

function sync_os_cell_with_od(od_cell_id){
	//os_cell_id = od_cell_id.replace('_od_','_os_');
	
	os_cell_id = od_cell_id.replace(/_od_/g, '_os_');
	
	od_cell_data = $('#'+od_cell_id).html();
	os_cell_data = od_cell_data.replace(/_od_/g, '_os_');//replace('_od_','_os_');
	//alert(os_cell_data);
	os_html_val = $('#'+os_cell_id).html(os_cell_data);
	$('#'+os_cell_id+' div.editable_control').each(function(index, element) {
        $(this).removeClass('editable_control');
    });
	test_template_changed = true;
}

function check_text_multiobser(ct,kc){
	// ct - current text box
	// kc - keycode on current text box	
	focusout = (kc['type']!='undefined' && kc['type']=='focusout')?true:false;
	if(kc==13 || focusout){//ENTER KEY
		ct.val(ct.val().trim());
		if(ct.val()!=''){
			current_multiobser_cnt =  ($('#modal_multiobservations tbody .text_multiobser').length);
			if(current_multiobser_cnt==1){
				//INCREASING ROW ID IF THIS EXISTS. (overcoming of sequence imbalance due to delete row action)
				if(current_new_control_parent_id=='test_main_options'){
					current_multiobser_cnt = test_main_option_mo_counter+1;
					
				}else if(current_new_control_parent_id=='custom_treatment_prognosis'){
					current_multiobser_cnt = test_prognosis_mo_counter+1;
				}
				tmp_ct_name = current_new_control_parent_id+'-txtmo_'+current_multiobser_cnt;
				while($('#modal_multiobservations txtmo_'+current_multiobser_cnt).get(0)){current_multiobser_cnt++;}
				ct_name = current_new_control_parent_id+'-txtmo_'+current_multiobser_cnt;
				ct.prop({'name':ct_name,'id':ct_name});
				
				ct_row = ct.parent('td').parent('tr');
				temp_ct_row_id = ct_row.prop('id').split('_');
				ct_row_id = parseInt(temp_ct_row_id[1]);
				ct_row.prop('id','row_'+current_multiobser_cnt);
			}
			if(!focusout){
				ct_row = ct.parent('td').parent('tr');
				temp_ct_row_id = ct_row.prop('id').split('_');
				ct_row_id = parseInt(temp_ct_row_id[1]);
								
				while($('#tb_multi_observation tbody tr#row_'+ct_row_id).get(0)){ct_row_id++;}
				
				ct_name_id = current_new_control_parent_id+'-txtmo_'+ct_row_id;
				new_row_id = 'row_'+(ct_row_id);
				new_mo_html  = '<tr id="'+new_row_id+'">';
				new_mo_html += '	<td><input name="'+ct_name_id+'" id="'+ct_name_id+'" type="text" class="form-control text_multiobser" value="" placeholder="type label here.."></td>';
				new_mo_html += '	<td><a title="Delete this label" class="edit_label" href="javascript:void(0);" onClick="delete_mo_editor_row($(this));"><span class="glyphicon glyphicon-remove"></span></a></td>';
				new_mo_html += '</tr>';
				ct_row.after(new_mo_html);
				$('#'+new_row_id+' td input.text_multiobser').focus();
			}
		}
	}	
}

var test_main_options_text_array = new Array();
var test_main_options_id_array = new Array();

function redener_multiobservations_from_modal(){
	//GET VALUES AND IDs.
	txt = $('.text_multiobser');
	arr_multi_obser_data  = new Array();
	arr_multi_obser_names = new Array();
	option_col_width 	  = 3; //DEFAULT FOUR OPTIONS IN A ROW.
	highest_string_length = 0;
	txt.each(function(){
		txt_val = $(this).val().trim();
		if(txt_val!=''){
			tvl = txt_val.length;
			if(tvl>highest_string_length){
				highest_string_length = tvl;
			}
			arr_multi_obser_data[arr_multi_obser_data.length] 	= txt_val;
			arr_multi_obser_names[arr_multi_obser_names.length]	= $(this).prop('name');
		}
	});
	
	if(highest_string_length > 40)	{option_col_width=12;}
	else if(highest_string_length > 20)	{option_col_width=6;}
	else if(highest_string_length > 15)	{option_col_width=4;}
	
	//IF CALL IS FROM TREATMENT/PROGNOSIS FIX COLUMN WIDTH TO 4;
	if(current_new_control_parent_id=='custom_treatment_prognosis') option_col_width=4;

	//MAKE HTML
	mo_html = '';
	
	//new ELEMENT UNIQUE ID COUNTER
	if(current_new_control_parent_id!='custom_treatment_prognosis' && current_new_control_parent_id!='test_main_options'){
		con_counter = current_new_control_parent_id.split('_');
		con_counter = con_counter[con_counter.length-1];
	}
	
	
	for(i=0;i<arr_multi_obser_data.length;i++){		
		curr_val 		= arr_multi_obser_data[i];
		curr_name_id 	= arr_multi_obser_names[i];

		if(current_new_control_parent_id=='test_main_options'){
			mo_html += '<td class="custom_test_main_options"><label><input type="checkbox" id="'+curr_name_id+'" name="'+curr_name_id+'" value="1"><span class="label_txt">'+curr_val+'</span></label></td>';
			test_main_option_mo_counter++;
		}else if(current_new_control_parent_id=='custom_treatment_prognosis'){
			mo_html += '<div class="col-sm-'+option_col_width+' custom_prognosis"><label><input type="checkbox" id="'+curr_name_id+'" name="'+curr_name_id+'" value="1"><span class="label_txt">'+curr_val+'</span></label></div>';
			test_prognosis_mo_counter++;
		}else{
			curr_element_id = current_new_control_parent_id+'mo_'+i;
			mo_html += '<div class="col-sm-'+option_col_width+'"><label><input type="checkbox" id="'+curr_name_id+'" name="'+curr_name_id+'" value="1"><span class="label_txt">'+curr_val+'</span></label></div>'
		}
	}
	if(current_new_control_parent_id=='custom_treatment_prognosis'){
		$('.custom_click_to_add_prognosis').parent('div').before(mo_html);
	}else if(current_new_control_parent_id=='test_main_options'){
		$('.custom_click_to_add_test_options').parent('td').before(mo_html);
	}else{
		mo_html = '<div class="row">'+mo_html+'</div>';
		$('#'+current_new_control_parent_id).html(mo_html);
	}
	
	if(current_new_control_parent_id=='test_main_options' || current_new_control_parent_id=='custom_treatment_prognosis'){
		
	}else{
		$('#'+current_new_control_parent_id).removeClass('remove_me_before_save').addClass('editable_control');
		od_cell_id = $('#'+current_new_control_parent_id).parent('td').prop('id');
		sync_os_cell_with_od(od_cell_id);
	}
	$('#modal_multiobservations').modal('hide');
}

function delete_mo_editor_row(po){
	po.parent('td').parent('tr').remove();
}

function save_test_layout(){
	test_name 			= $.trim($('#elem_testOtherName').val());
	if(test_name==''){
		top.fAlert('Test name not provided.','Warning',$('#elem_testOtherName'));
		return;
	}else if(test_template_changed && parseInt($('#elem_test_template_version').val())>0){
		top.fAlert('Form Layout changed, New interface will be applicable to new patient tests only.','Information');
		$('#elem_test_template_version').val('');
	}
	//FETCHING TEST MAIN OPTIONS
	test_main_options 	= '';
	$('table#test_main_options tr td').each(function(index, element) {
		if(!$(this).find('span.custom_click_to_add_test_options').get(0)){
	        test_main_options += '<td class="custom_test_main_options">'+$(this).html()+'</td>';
			if($(this).find('input[type=checkbox]').get(0)!='undefined'){
			//	alert($(this).html()+"\n\n"+$(this).find('input[type=checkbox]').prop('id')+' :: '+$(this).find('span[class=label_txt]').text());
				test_main_options_id_array[test_main_options_id_array.length] 		= $(this).find('input[type=checkbox]').prop('id');
				test_main_options_text_array[test_main_options_text_array.length] 	= $(this).find('span[class=label_txt]').text();
			}
		}
    });
	test_results		= '';
	$('.remove_me_before_save').each(function(index, element) {
        $(this).remove();
    });
	$('table#test_result_table tr').each(function(index, element) {
		if(index>1){
	        test_results += '<tr>'+$(this).html()+'</tr>';
		}
    });

	test_treatment		= '';//custom_click_to_add_prognosis
	$('div#custom_treatment_prognosis div.custom_prognosis').each(function(index, element) {
        test_treatment += '<div class="col-sm-4 custom_prognosis">'+$(this).html()+'</div>';
    });

	$('#elem_test_name').val(test_name);
	$('#elem_test_manager').val(top.fmain.$('#t_manager1').prop('checked') ? 1 : 0);
	$('#elem_test_main_options').val(test_main_options);
	$('#elem_test_main_option_mo_counter').val(test_main_option_mo_counter);
	$('#elem_test_main_options_ids').val(test_main_options_id_array.join(','));//issue here in edit case.
	$('#elem_test_main_options_text').val(test_main_options_text_array.join(','));
	$('#elem_test_results').val(test_results);
	$('#elem_test_treatment').val(test_treatment);
	$('#elem_test_treatment_mo_counter').val(test_prognosis_mo_counter);
	document.forms.custom_template_tests.submit();
}


</script>
<style type="text/css">
.hide{display:none;}
.mask_white,.mask_black{z-index:1000; position:absolute;}
.mask_white a{font-weight:bold; color:#000;}
.edit_wrapper .glyphicon{font-size:12px; font-weight:bold;} 
.table-noborder tr td{border:0px !important;}
.custom_click_to_add_control, .custom_click_to_add_test_options, .custom_click_to_add_prognosis{
	border:2px dashed #bbb; width:auto; padding:2px; margin:5px 0px 0px 5px; display:inline-block;
	font-weight:bold; background-color:#efefef; cursor:pointer;
}
.editable_control{margin:5px 0px 5px 0px;}
div[id^="row_od_"], div[id*=" row_od_"],div[id^="row_os_"], div[id*=" row_os_"]{margin-top:5px;}
input[type="number"]{width:80px !important;}
</style>
</head>
<body>
<form name="custom_template_tests" method="post" style="margin:0px;">
<input type="hidden" name="elem_saveForm" value="TemplateTests">
<input type="hidden" name="elem_test_template_id" id="elem_test_template_id" value="<?php echo $elem_test_template_id?>">
<input type="hidden" name="elem_test_template_version" id="elem_test_template_version" value="<?php echo $elem_test_template_version;?>">

<input type="hidden" name="elem_test_name" id="elem_test_name" value="<?php echo $elem_test_name;?>">
<input type="hidden" name="elem_test_manager" id="elem_test_manager" value="<?php echo $elem_test_name;?>">
<input type="hidden" name="elem_test_main_options" id="elem_test_main_options" value="<?php echo $elem_test_main_options;?>">
<input type="hidden" name="elem_test_main_option_mo_counter" id="elem_test_main_option_mo_counter" value="<?php echo $elem_test_main_option_mo_counter;?>">
<input type="hidden" name="elem_test_main_options_ids" id="elem_test_main_options_ids" value="<?php echo $elem_test_main_options_ids;?>">
<input type="hidden" name="elem_test_main_options_text" id="elem_test_main_options_text" value="<?php echo $elem_test_main_options_text;?>">
<input type="hidden" name="elem_test_results" id="elem_test_results" value="<?php echo $elem_test_results;?>">
<input type="hidden" name="elem_test_treatment" id="elem_test_treatment" value="<?php echo $elem_test_treatment;?>">
<input type="hidden" name="elem_test_treatment_mo_counter" id="elem_test_treatment_mo_counter" value="<?php echo $elem_test_treatment_mo_counter;?>">
</form>

<input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>" >
<input type="hidden" name="elem_testOtherName" value="<?php echo $elem_testOtherName;?>">
<div class=" container-fluid">
    <div class="mainarea">
        <div class="row">
            <div class="col-sm-12">
                <?php require_once(dirname(__FILE__)."/test_orderby_inc.php");?>
                <div class="clearfix"></div>
                <div class="tstopt">
                    <div class="row">
                        <div class="col-sm-7 sitetab tstrstopt">
                            <table class="table table-noborder" id="test_main_options">
                            	<tr><?php echo html_entity_decode($elem_test_main_options);?><td><span class="custom_click_to_add_test_options">+ Add options</span></td></tr>
                            </table>
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input disabled type="radio" name="elem_topoMeterEye" value="OU"><span class="label_txt">OU</span></label></li>
			                                <li class="odc"><label><input disabled type="radio" name="elem_topoMeterEye" value="OD"><span class="label_txt">OD</span></label></li>
            			                    <li class="osc"><label><input disabled type="radio" name="elem_topoMeterEye" value="OS"><span class="label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="technibox">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Performed By</label>
                                        <input type="text" id="elem_performedByName" name="elem_performedByName" value="<?php echo $objTests->getPersonnal3($elem_performedBy);?>" class="form-control" readonly onDblClick="setOpNameId(this.name)">
                                        <input type="hidden" id="elem_performedBy" name="elem_performedBy" value="<?php echo $elem_performedByCurr;?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis </label>
                                        <select class="form-control minimal" id="elem_diagnosis" name="elem_diagnosis" onChange="checkDiagnosis(this.value)" style="display:<?php echo ($elem_diagnosis == "Other") ? "none" : "block" ;?>;"<?php if(isset($callFromInterface) && $callFromInterface=='admin') echo ' disabled';?>>
                                        <option>--Select--</option>
                                        <?php
                                        $arrDigOpts = $objTests->getDiagOpts('1','custom_template',$test_master_id);
										foreach($arrDigOpts  as $key=>$val){
                                            echo "<option value=\"".$val."\">".$val."</option>";
                                        }
        
                                        ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group pdlft10">
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="techComment" name="techComments" placeholder="Technician Comments"<?php if(isset($callFromInterface) && $callFromInterface=='admin') echo ' disabled';?>></textarea>
                            </div>
                        </div>	
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="corporat">
                    <div class="pdlft10">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul>
                                    <li class="head">Patient Understanding &amp; Cooperation</li>
                                    <li>
                                    <div class="tstrstopt">
                                    <label><input disabled type="radio" name="elem_ptUnderstanding" value="Good"><span class="label_txt">Good</span></label>
                                    <label><input disabled type="radio" name="elem_ptUnderstanding" value="Fair"><span class="label_txt">Fair</span></label>
                                    <label><input disabled type="radio" name="elem_ptUnderstanding" value="Poor"><span class="label_txt">Poor</span></label>
                                    </div>
                                    </li>  
                                </ul>
                            </div>
                            <!--<div class="col-sm-4 text-center">
                                <div class="form-inline mt5">
                                    <label for="">Preference Card</label>
                                    <?php /*echo $objTests->DropDown_Interpretation_Profile($this_test_properties['id']);*/?>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right">
                                <button class="btn-value" type="button" onmouseover="inPrvVal()" onclick="inPrvVal(1)">Previous Values</button>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div>
                    <table class="table table-bordered" id="test_result_table">
                        <tr>
                          <td colspan="4" class="phyintrhead">Physician Interpretation</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><a href="javascript:add_new_test_result_row();"><span class="glyphicon glyphicon-plus pull-right" title="Add a row"></span></a><strong>TEST RESULT</strong></td>
                            <td class="odstrip">
                                <div class="row">
                                    <div class="col-sm-1">OD</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_reliabilityOd" value="Good"><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Fair"><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Poor"><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="100" align="center" valign="middle" class="bltra">&nbsp;</td>
                            <td class="osstrip">
                                <div class="row">
                                    <div class="col-sm-1">OS</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_reliabilityOs" value="Good"><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Fair"><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Poor"><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>		
                        </tr>
                        <?php if($elem_test_results!=''){ echo html_entity_decode($elem_test_results);}else{?>
                        <tr>
                            <td id="label_row_1" class="tdlftpan editable_label">Comments</td>
                            <td id="row_od_1" class="pd5 tstbx tstrstopt">
                                <div id="row_od_1-con_1" class="editable_control"><textarea class="form-control" rows="2" cols="30" style="width:100%; height:50px;" id="row_od_1-con_1-desc1" name="row_od_1-con_1-desc1"></textarea></div>
                                
                            </td>
                            <td id="row_os_1" class="pd5 tstbx tstrstopt">
                                <div id="row_os_1-con_1"><textarea class="form-control" rows="2" cols="30" style="width:100%; height:50px;" id="row_os_1-con_1-desc1" name="row_os_1-con_1-desc1"></textarea></div>
                            </td>	
                        </tr>
                        <tr>
                            <td id="label_row_2" class="tdlftpan editable_label">Interpretation</td>
                            <td id="row_od_2" class="pd5 tstbx tstrstopt">
                                <div id="row_od_2-con_1" class="editable_control"><textarea class="form-control" rows="2" cols="30" style="width:100%; height:50px;" id="row_od_2-con_1-desc1" name="row_od_2-con_1-desc1"></textarea></div>
                                
                            </td>
                            <td id="row_os_2" class="pd5 tstbx tstrstopt">
                                <div id="row_os_2-con_1"><textarea class="form-control" rows="2" cols="30" style="width:100%; height:50px;" id="row_os_2-con_1-desc1" name="row_os_2-con_1-desc1"></textarea></div>
                            </td>	
                        </tr><?php }?>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="tstfot">
					<div class="whitebox">
                        <h2>Treatment/Prognosis</h2>
                        <div class="clearfix"></div>
                            <div id="table_prognosis" class="tstrstopt">
                                <div class="row">
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_stable" value="1"><span class="label_txt">Stable</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contiMeds" value="1"><span class="label_txt">Continue Meds</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_tech2InformPt" value="1"><span class="label_txt">Tech to Inform Pt.</span></label></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_informedPtNv" value="1"><span class="label_txt">Inform Pt result next visit</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_fuApa" value="1"><span class="label_txt">F/U APA</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformed" value="1"><span class="label_txt">Pt informed of results</span></label></div>
                                </div>
                                <div class="row" id="custom_treatment_prognosis">
                                	<?php echo html_entity_decode($elem_test_treatment);?>
                                    <div class="col-sm-4"><span class="custom_click_to_add_prognosis">+ Add options</span></div>
                                </div>
                            </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
	</div>	
</div>

<div id="masking_html_control" class="hide">
    <div class="mask_white">
        <a class="del_control" href="javascript:void(0);"><span class="glyphicon glyphicon-remove"></span><b>Delete Control</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a class="del_row" href="javascript:void(0);"><span class="glyphicon glyphicon-remove"></span><b>Delete Row</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a class="new_control_next" href="javascript:void(0);"><span class="glyphicon glyphicon-plus"></span><b>New control next to it</b></a>
    </div>
</div>


<div id="masking_html_prognosis" class="hide">
    <div class="mask_white text-right">
        <a class="del_option" href="javascript:void(0);"><span class="glyphicon glyphicon-remove"></span><b>Delete</b></a>
    </div>
</div>

<div id="masking_html_label" class="hide">
    <div class="mask_white">
        <a class="edit_label" href="javascript:void(0);"><span class="glyphicon glyphicon-pencil"></span><b>Edit Label</b></a>
    </div>
</div>

<div id="modal_multiobservations" class="modal" role="dialog">
    <div class="modal-dialog modal-sm"> 
        <div class="modal-content">
            <div class="modal-body">
                <table class="table" id="tb_multi_observation">
                    <thead><tr><th>OBSERVATION</th><th colspan="2">&nbsp;</th></tr></thead>
                    <tbody>
                    <tr id="row_1">
                        <td><input type="text" class="form-control text_multiobser" value="" placeholder="type label here.."></td>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-info text-left m0">*Press &lt;Enter&gt; key for Observation options</div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-success" onClick="redener_multiobservations_from_modal()">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function(e) {
	$('[data-toggle="tooltip"]').tooltip()
	$("#content-1").mCustomScrollbar({theme:"minimal"});
//    init_page_display();
});
top.set_header_title('New Test Template');

var menu1 = new BootstrapMenu('.custom_click_to_add_control', {
  menuEvent: 'click', // default value, can be omitted
  menuSource: 'element',
  menuPosition: 'belowLeft', // default value, can be omitted
  actions: [
   {
      name: 'Grade Options',
      onClick: function() {
        render_new_control_in_testresult('gradeoptions');
      }
    }, {
      name: 'Multiple Observations',
      onClick: function() {
        render_new_control_in_testresult('multi_observations');
      }
    }, {
      name: 'Numeric Values',
      onClick: function() {
        render_new_control_in_testresult('numeric_values');
      }
    }, {
      name: 'Text Box (one liner)',
      onClick: function() {
        render_new_control_in_testresult('textbox');		
      }
   }, {
      name: 'Text Area (multi liner)',
      onClick: function() {
        render_new_control_in_testresult('textarea');
      }
   }
   ]  
});


var menu = new BootstrapMenu('.custom_prognosis, .custom_test_main_options', {
  actions: [{
      name: 'Delete this option',
      onClick: function(t) {
        current_context_element.remove();
		test_template_changed = true;
      }
  }]
});
</script>
</body>
</html>