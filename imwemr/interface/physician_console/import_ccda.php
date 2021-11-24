<?php
/***This is the main interface file */
	include_once("../../config/globals.php");
	include_once($GLOBALS['srcdir']."/classes/common_function.php");
	include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_hx_import.class.php");
	include_once($GLOBALS['srcdir']."/classes/msgConsole.php");
	
	function get_next_appt_id($pt_id){
		$q = "SELECT id FROM schedule_appointments 
				WHERE sa_patient_id = '".$pt_id."' 
					AND sa_app_start_date >= '".date('Y-m-d')."' 
					AND sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['id'];
		}
		return false;
	}
	
	$msgConsoleObj = new msgConsole();
	$imp_pt_id = (isset($_REQUEST['pt_id']) && empty($_REQUEST['pt_id']) == false) ? $_REQUEST['pt_id'] : '';
	$xml_file	= (isset($_REQUEST['xml_file']) && empty($_REQUEST['xml_file']) == false) ? $_REQUEST['xml_file'] : false; 
	
	/*******LOGGING PATIENT ID WTH ATTACHMENT*****/
	$direct_message_id = $_REQUEST['direct_message_id'];
	$file_name = (!$xml_file && !empty($_REQUEST['file_name'])) ? trim($_REQUEST['file_name']) : $xml_file;
	$file_name = str_replace('/users/','/',$file_name);
	if(!empty($direct_message_id) && !empty($file_name)){
		imw_query("UPDATE direct_messages_attachment SET patient_id = '".$imp_pt_id."' WHERE direct_message_id='".$direct_message_id."' AND complete_path LIKE '".$file_name."'");
		/****UPDATING APPOINTMENT ID ALSO, IF AVAILABLE*/
		$next_appt_id = get_next_appt_id($imp_pt_id);
		imw_query("UPDATE direct_messages_attachment SET sch_id = '".$next_appt_id."' WHERE direct_message_id='".$direct_message_id."' AND complete_path LIKE '".$file_name."' AND (sch_id='' OR sch_id='0')");
		
	}
	
	//if xml record id not provided and file path provided. create record and use further.
	if($xml_file){
		$xml_id = $msgConsoleObj->get_ccd_xml_id($imp_pt_id,$xml_file);
		/****UPDATING APPOINTMENT ID ALSO, IF AVAILABLE*/
		$next_appt_id = get_next_appt_id($imp_pt_id);
		imw_query("UPDATE ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl SET sch_id = '".$next_appt_id."' WHERE scan_doc_id='".$xml_id."' AND (sch_id='' OR sch_id='0')");
		
		die('<script type="text/javascript">window.location.href="import_ccda.php?pt_id='.$imp_pt_id.'&xml_id=,'.$xml_id.'";</script>');
	}else if($xml_id){
		$next_appt_id = get_next_appt_id($imp_pt_id);
		imw_query("UPDATE ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl SET sch_id = '".$next_appt_id."' WHERE scan_doc_id IN (0".$xml_id.") AND (sch_id='' OR sch_id='0')");
	}
	
	
	$pt_details = core_get_patient_name($imp_pt_id);
	$import_obj = New Medical_Import($tab,$imp_pt_id);
	$library_path = $GLOBALS['webroot'].'/library';
	
	$direct = 'yes';
	
	if(isset($_REQUEST['chk_ccda']) && empty($_REQUEST['chk_ccda']) == false){
		$upload_status = $import_obj->import_direct_xml($_REQUEST);
		echo json_encode($upload_status);
		exit;
	}
	
	if(isset($_REQUEST['save_ccda']) && empty($_REQUEST['save_ccda']) == false){
		$save_status = $import_obj->import_direct_xml($_REQUEST);
		echo json_encode($save_status);
		exit;
	}
	
	if(!isset($_REQUEST['showpage']) && empty($_REQUEST['showpage'])){
		$_REQUEST['showpage'] = 'medications';
	}
	
	//PHP value array to be used in jquery
	$ret_array = array();
	$ret_array['curr_tab']	= $_REQUEST['showpage'];
	$ret_array['xml_id'] 	= $_REQUEST['xml_id'];
	$ret_array['page_request'] 	= $_REQUEST['page_request'];
	$ret_array['direct_save'] 	= $_REQUEST['direct_save'];
	$ret_array['pt_id'] 	= $imp_pt_id;
	$global_js_arr =  json_encode($ret_array);
?>
<html>
	 <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr :: Import CCDA</title>

        <!-- Bootstrap -->

		<!--<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/font-awesome.css" rel="stylesheet" type="text/css">-->
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/core.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
            <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
        <![endif]-->
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
    </head>
	<?php 
		echo '<script>$("#div_loading_image").hide();</script>';
	?>
	<body onUnload="unload_fun();">
		<div class="mainwhtbox">
			<div class="row">
				<div class="main_content_wrapper">
					<div class="col-sm-12 purple_bar">
						<div class="row">
							<div class="col-sm-3">
								<span><b>Import Medical History</b></span>
							</div>
							<div class="col-sm-6 text-center">
								<span>
								<?php 
									echo $pt_details[2].', '.$pt_details[1].' '.$pt_details[3].' - '.$pt_details[0];
								?>
								</span>
							</div>
						</div>
					</div>	
					<div class="col-sm-12 pt10">
						<ul class="nav nav-tabs">
							<li class="pointer <?php echo ($_REQUEST['showpage'] == 'medications') ? 'active' : ''; ?>" data-tab="medications" data-xml_id ="<?php echo $_REQUEST['xml_id'] ?>"><a>Medications</a></li>	
							<li class="pointer <?php echo ($_REQUEST['showpage'] == 'allergies') ? 'active' : ''; ?>" data-tab="allergies" data-xml_id ="<?php echo $_REQUEST['xml_id'] ?>"><a>Allergies</a></li>	
							<li class="pointer <?php echo ($_REQUEST['showpage'] == 'problem_list') ? 'active' : ''; ?>" data-tab="problem_list" data-xml_id ="<?php echo $_REQUEST['xml_id'] ?>"><a>Problem List</a></li>	
							<li class="pointer <?php echo ($_REQUEST['showpage'] == 'sx_proc') ? 'active' : ''; ?>" data-tab="sx_proc" data-xml_id ="<?php echo $_REQUEST['xml_id'] ?>"><a>Sx Procedures</a></li>	
						</ul>
					</div>
					
					<div id="content_div" class="col-sm-12 pt10">
						<?php
							$folder = $_REQUEST['showpage'];
							$path = $GLOBALS['fileroot'].'/interface/Medical_history/import/'.$folder.'/index.php';
							include_once $path;
						?>	
					</div>
				</div>
				<!-- Footer Buttons -->
				<div id="core_buttons_bar" class="col-sm-12 pt10 text-center">
					<div id="page_buttons">
						<input type="button" value="Consolidate" name="consolidate"  id="consolidate" class="btn btn-success" onClick="submit_frame()"> 
						<input type="button" value="Merge" name="merge" id="merge" class="btn btn-success" onClick="submit_frame()">
						<input type="button" value="Close" name="close" id="close" class="btn btn-danger" onClick="window.close();">
					</div>
				</div>
			</div>	
		</div>

		 <!--HTML Loader-->
      <div id="div_loading_image" class="text-center" style="position:absolute;width:100%">
            <div class="loading_container">
                <div class="process_loader"></div>
                <div id="div_loading_text" class="text-info"></div>
            </div>
        </div>
	<script>
		var global_php_var = $.parseJSON('<?php echo $global_js_arr; ?>');
		if(global_php_var.direct_save != '' && global_php_var.direct_save == 'yes'){
			$('.mainwhtbox').addClass('hide');
			fAlert('Import Successfull','','window.location.href=\'import_ccda.php?showpage=<?php echo $_REQUEST['showpage'];?>&pt_id=<?php echo $pt_id;?>&xml_id=<?php echo $xml_id;?>\';');
			//window.opener.$('#div_ccda_main').modal('hide');
		}
		// --- Allergies Func() ---
		function select_xml_chk(class_name, obj){
			if($(obj).is(":checked")){
				$('.'+class_name).each(function(id, elem) {
					$(elem).prop('checked',true);
				});
			}else{
				$('.'+class_name).each(function(id, elem) {
					$(elem).prop('checked',false);
				});
			}
		}
		
		function submit_frame(){
			$('#frm').submit();
		}
		
		function check_form(form_name){
			var checked_arr = new Array;
			$('input[type=checkbox]').not('[name^="compliant"],[name^="chk_ocular"]').each(function(id,elem){
				if($(elem).is(':checked')){
					var elem_id = $(elem).attr('id');
					checked_arr.push($(elem).val());
				}
			});
			if(checked_arr.length == 0){
				top.fAlert('Please select a record to continue');
				return false;		
			}else{
				return true;
			}
		}
		
		function get_sec_data(obj){
			$("#div_loading_image").show();
			var data = $(obj).data();
			var xml_id = data.xml_id;
			var tab = data.tab;
			window.location.href = 'import_ccda.php?xml_id='+xml_id+'&showpage='+tab+'&pt_id='+global_php_var.pt_id;
		}
		
		function set_elem_height(){
			var height_diff = $('#content_div').position();
			var window_height = (screen.availHeight - ($('#page_buttons').height() + height_diff.top + 50));
			$('.main_content_wrapper').css({
				'height':window_height,
				'overflowY':'auto'
			});
			/*var main_height = '';
			if($('#content_div table').length > 1){
				main_height = (window_height / $('#content_div table').length);
			}else{
				main_height = (window_height - 50);
			}
			$('#content_div table').each(function(id,elem){
				$(elem).wrap('<div class="col-sm-12 table_'+id+'"></div>');
				$('.table_'+id+'').css({
					'height':(main_height - 50),
					'max-height':(main_height - 50),
					'overflowY':'auto'
				});
			});*/
		}
		
		$(document).ready(function(){
			set_elem_height();
			$('#merge').hide();
			$('.nav-tabs').on('click', 'li', function() {
				$('.nav-tabs li.active').removeClass('active');
				$(this).addClass('active');
				get_sec_data($(this));
			});
			$(".panel button.close span").on('click',function (e) {
				$('#upload_err').slideUp('slow');
			});
			
			$('.nav-tabs #'+global_php_var.curr_tab+'').addClass('active');
			$('.datepicker').datetimepicker({timepicker:false,format:'m-d-Y',autoclose: true,scrollInput:false});
			if(global_php_var.curr_tab == 'allergies' || global_php_var.curr_tab == 'problem_list'){
				$('#consolidate').show();
			}
			
			if(global_php_var.page_request == 'merge'){
				if(global_php_var.curr_tab == 'problem_list'){
					global_php_var.curr_tab = 'Problem List';
				}
				
				if(global_php_var.curr_tab == 'sx_proc'){
					global_php_var.curr_tab = 'Sx Procedures';
				}
				var btn_val = 'Merge '+global_php_var.curr_tab;	
				$('#merge').val(btn_val).show();
				$('#consolidate').hide();
			}
		});
		$(window).load(function(){
			$("#div_loading_image").hide();
		});
		
	</script>
	</body>
</html>