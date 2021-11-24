<?php


require_once("../../config/globals.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';

include_once $GLOBALS['srcdir'].'/classes/common_function.php';
include_once $GLOBALS['srcdir'].'/classes/saveFile.php';

$patient_id = $_SESSION['patient'];
$upload_dir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";	
if(isset($_POST['hidd_selected_imgs']) && trim($_POST['hidd_selected_imgs'])!=''){
	//pre($_POST,1);
	$mode=='';
	if($_POST['assign_images']) $mode='add';
	if($_POST['del_images']) $mode='del';
	
	$img_array = json_decode(urldecode(trim($_POST['hidd_selected_imgs'])));
	if(is_array($img_array)){
		$pt_dir			= $upload_dir.'PatientId_'.$patient_id;
		$dest_dir		= $pt_dir.'/screenshots';
		$dest_thumb_dir	= $pt_dir.'/screenshots/thumb';
		if(!is_dir($pt_dir)){mkdir($pt_dir);}
		if(!is_dir($dest_dir)){mkdir($dest_dir);}
		if(!is_dir($dest_thumb_dir)){mkdir($dest_thumb_dir);}

		foreach($img_array as $img){
			$s_img 			= 'screenshots'.trim(strrchr($img,'/'));
			$d_img 			= $dest_dir.trim(strrchr($img,'/'));
			$d_img_thumb 	= $dest_thumb_dir.trim(strrchr($img,'/'));
			if($mode=='add'){
				$cp = copy($s_img,$d_img);
				if($cp) {
					$oSaveFile = new SaveFile($current_patient);
					$oSaveFile->createThumbs($d_img,$d_img_thumb,200,200);
					unlink($s_img);
				}
			}else if($mode=='del'){
				if(strpos($img,'/addons/toric/')){ //delete unassigned image.
					unlink($s_img);
				}else if(strpos($img,"/".constant('PRACTICE_PATH')."/PatientId_")){ //delete patient assigned image.
					unlink($d_img);
				}
			}
		}
	}
}

$filesjpg = glob("screenshots/*jpg");
$filespng = glob("screenshots/*png");
$img_files = array();
foreach($filesjpg as $jpg){$img_files[] = $jpg;}
foreach($filespng as $png){$img_files[] = $png;}

//SORTING FILE LIST DESCENDING ACCORDING TO DATE MODIFIED
usort($img_files, create_function('$a,$b', 'return filemtime($b)>filemtime($a);'));

//GETTING PATIENT SCREENSHOTS
$pt_dir			= $upload_dir.'PatientId_'.$patient_id;
$dest_dir		= $pt_dir.'/screenshots';
$dest_thumb_dir	= $pt_dir.'/screenshots/thumb';

$filesjpg = glob($dest_thumb_dir."/*jpg");
$filespng = glob($dest_thumb_dir."/*png");
$img_files_PT = array();
foreach($filesjpg as $jpg){$img_files_PT[] = $jpg;}
foreach($filespng as $png){$img_files_PT[] = $png;}
//SORTING FILE LIST DESCENDING ACCORDING TO DATE MODIFIED
usort($img_files_PT, create_function('$a,$b', 'return filemtime($b)>filemtime($a);'));
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Toric Calculator</title>
	<!-- Bootstrap -->
	<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
	<!-- Bootstrap Selctpicker CSS -->
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
	<!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<!-- DateTime Picker CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]--> 
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
	<!-- jQuery's Date Time Picker -->
	<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
	<!-- Bootstrap -->
	<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
	
	<!-- Bootstrap Selectpicker -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
	<script type="text/javascript" src="toric.js?<?php echo filemtime('toric.js');?>"></script>
    <script type="text/javascript">
		$(document).ready(function(e) {
			if(typeof(window.opener.top.innerDim)=='function'){
				var innerDim = window.opener.top.innerDim();
				if(innerDim['w'] > 1600) innerDim['w'] = 1600;
				if(innerDim['h'] > 900) innerDim['h'] = 900;
				window.resizeTo(innerDim['w'],innerDim['h']);
				brows	= get_browser();
				if(brows!='ie') innerDim['h'] = innerDim['h']-35;
				var result_div_height = innerDim['h']-210;
				$('.adminbox').height(result_div_height+'px');
			}
		});

		$(window).resize(function() {
			if(typeof(window.opener.top.innerDim)=='function'){
				var innerDim = window.opener.top.innerDim();
				if(innerDim['w'] > 1600) innerDim['w'] = 1600;
				if(innerDim['h'] > 900) innerDim['h'] = 900;
				window.resizeTo(innerDim['w'],innerDim['h']);
				brows	= get_browser();
				if(brows!='ie') innerDim['h'] = innerDim['h']-35;
				var result_div_height = innerDim['h']-210;
				$('.adminbox').height(result_div_height+'px');
			}
		});
	
	var selected_images = new Array();
	var flag_images_selected = false;
	
	</script>
    <style type="text/css">
		#div_unassigned_images,#div_unassigned_images_pt{overflow-x:hidden; overflow-y:auto;}
		.image_container{margin:10px; border:5px solid #efefef; padding:1px; display:inline-block; cursor:pointer;}
		.selected_image_container{border:5px solid #666666; }
	</style>
</head>
<body>
	<div class="mainwhtbox">
		<div class="row">
			<div class="col-sm-12 purple_bar">
				<div class="row">
					<div class="col-sm-6">
						<label>Toric Calculator Images</label>	
					</div>
					<div class="col-sm-6 text-right">
						<input type="button" class="btn btn-primary fr" value="Launch Toric Calculator" onClick="openToric();">	
					</div>	
				</div>
			</div>
			<div class="col-sm-12">
				<div class="row adminbox">
					<div id="div_unassigned_images" class="col-sm-7">
						<div class="row">
							<div class="headinghd col-sm-12">
								<h4>Un-Assigned Images</h4>	
							</div>
							<div class="col-sm-12 pt10">
								<div class="row">
									<?php
										if(count($img_files)>0){
											foreach($img_files as $img){?>
												<div class="image_container col-sm-12" title="Click to Select/Deselect Image">
													<img src="<?php echo $img;?>" onClick="SelectImg(this);">
												</div>
										<?php }	
										}else{
											echo '<div class="alert alert-info col-sm-4 col-sm-offset-4 text-center">
												   No image found.
												 </div>';
										}
									?>
								</div>
							</div>	
						</div>	
					</div>
					<div id="div_unassigned_images_pt" class="col-sm-5">
						<div class="row">
							<div class="headinghd col-sm-12">
								<h4>Patient Assigned Images</h4>	
							</div>
							<div class="col-sm-12 pt10">
								<div class="row">
									<?php
										if(count($img_files_PT)>0){
											foreach($img_files_PT as $imgPt){?>
												<div class="image_container col-sm-12" title="Click to Select/Deselect Image">
													<img src="<?php echo $imgPt;?>" onClick="SelectImg(this);">
												</div>
										<?php }	
										}else{
											echo '<div class="alert alert-info col-sm-4 col-sm-offset-4 text-center">
												   No image found.
												 </div>';
										}
									?>	
								</div>
							</div>	
						</div>	
					</div>	
				</div>	
			</div>
			<div class="page_bottom_bar col-sm-12 pt10 text-center ad_modal_footer" id="module_buttons">
				<form name="f1" method="post" onSubmit="return validForm();">
					<input type="hidden" name="hidd_selected_imgs" id="hidd_selected_imgs" value="">
					<input type="submit" class="btn btn-success" name="assign_images" value="Assign to Current Patient"> 
					<input type="submit" class="btn btn-primary" name="del_images" value="Delete Images">
					<input type="button" class="btn btn-danger" value="Close" onclick="window.close()">
				</form>
			</div>
		</div>	
	</div>
</body>
</html>