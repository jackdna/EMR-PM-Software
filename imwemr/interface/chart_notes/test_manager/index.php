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
 Purpose: Patient Test Image Manager
 Access Type: Popup.
 
*/
require_once("../../../config/globals.php");

//To check pt logged in or not
require_once("../../../library/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';

include_once($GLOBALS['srcdir'].'/classes/common_function.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
include_once($GLOBALS['srcdir'].'/classes/SaveFile.php');

$oSaveFile = New SaveFile;
$session_patient = $_SESSION['patient'];
$pt_name_arr = core_get_patient_name($session_patient);

$arr_all_test = array();
$q_tests = "SELECT test_name, temp_name, test_table, test_type,id,version FROM tests_name WHERE del_status=0 AND status=1 AND t_manager=1";
$res_tests = imw_query($q_tests);
if($res_tests && imw_num_rows($res_tests)>0){
	while($rs_test=imw_fetch_assoc($res_tests)){
		$test_name = $rs_test['temp_name']=='' ? $rs_test['test_name'] : $rs_test['temp_name'];
		$arr_all_test[$rs_test['test_type'].$test_name.'~|~'.$rs_test['test_name'].'~|~'.$rs_test['id'].'~|~'.$rs_test['version']]=$rs_test['test_table'];
	}
}

$arr_scan_site = array('','OS','OD','OU');
$qry_tests = array();

foreach($arr_all_test as $key => $this_test){
	$test_name = $this_test;
	$patientId = 'patient_id';
	if($test_name=='vf' || $test_name=='vf_gl' || $test_name=='pachy' || $test_name=='disc' || $test_name=='disc_external' || $test_name=='topography' || $test_name=='test_bscan' || $test_name == "test_other" || $test_name == "test_labs" || $test_name == "test_cellcnt" || $test_name=='test_custom_patient'){
		$patientId = 'patientId';
	}
	$phyName = 'phyName';
	if($test_name=='ivfa' || $test_name=='icg'){
		$phyName = 'phy';
	}
	if(in_array($test_name,array('surgical_tbl','iol_master_tbl'))){
		$phyName = 'signedById';
	}
	$pkIdCol = $test_name."_id";
	$examDate = "examDate";
	if($test_name=='icg')					{$examDate 	= "exam_date";}
	else if($test_name=='ivfa')				{$pkIdCol 	= "vf_id";	$examDate = "exam_date";}
	else if($test_name=='disc_external')	{$pkIdCol 	= "disc_id";}
	else if($test_name=='topography')		{$pkIdCol 	= "topo_id";}
	else if($test_name=='test_gdx')			{$pkIdCol 	= "gdx_id";}
	else if($test_name=='surgical_tbl')		{$pkIdCol 	= "surgical_id";}
	else if($test_name=='iol_master_tbl')	{$pkIdCol 	= "iol_master_id";}
	else if($test_name=='test_custom_patient'){$pkIdCol 	= "test_id";}
	
	/***THIS PART WRITTEN TO MANAGE TEMPLATE BASED TESTS*****/
	$testKeyType=substr($key,0,1);
	if($testKeyType=='1' && ($test_name=='test_other' || $test_name=='test_custom_patient')){
		$tempKeyArr = explode('~|~',$key);
		$where_part=" AND ".$test_name.".test_template_id='".$tempKeyArr[2]."'";
	}
	else if($testKeyType=='0' && $test_name=='test_other'){
		$where_part=" AND ".$test_name.".test_template_id=0";
	}
	else{
		$where_part='';
	}
	/********************************************************/
	
	
	$qry_tests[$key] = "SELECT '".$test_name."' AS test_name, ".$test_name.".".$pkIdCol." AS testId, DATE_FORMAT(".$test_name.".".$examDate.",'".get_sql_date_format('','y')."') AS examDate1, ".$test_name.".ordrby, ".$test_name.".".$phyName." AS phyName, pd.providerID, ".$test_name.".examTime FROM ".$test_name." 
						JOIN patient_data pd ON (pd.id = ".$test_name.".".$patientId.") 
						WHERE ".$test_name.".del_status = '0' AND ".$test_name.".purged = '0' AND ".$patientId."='".$session_patient."' 
						".$where_part." 
						ORDER BY ".$examDate." DESC";
	$ARR_sorted_tests = array();
	$recent_exam_date = 0;
	foreach($qry_tests as $testDname=>$q){
		$temp_res = imw_query($q);//if(!$temp_res){echo $q.'<hr>'.imw_error().'<br>';}
		$temp_rs = imw_fetch_assoc($temp_res);
		$str_examtime = str_replace('-','',$temp_rs['examTime']);
		$str_examtime = str_replace(':','',$str_examtime);
		$str_examtime = floatval(str_replace(' ','',$str_examtime));
		if($str_examtime > $recent_exam_date){
			$recent_exam_date = $str_examtime;
			array_unshift($ARR_sorted_tests,array($testDname=>$q));
		}else{
			array_push($ARR_sorted_tests,array($testDname=>$q));
		}
	}	
}

unset($qry_tests);$qry_tests = array();
foreach($ARR_sorted_tests as $i=>$arr){
	foreach($arr as $t=>$q){
		$qry_tests[$t] = $q;
	}
}

unset($ARR_sorted_tests);unset($recent_exam_date);

$big_img_file_name_new = '';
if(constant("STOP_CONVERT_COMMAND")=="YES") {
	$big_img_file_name_new = $GLOBALS['srcdir'].'/images/pdfimg.png';
}

//Topcon configuration file
include_once('topcon_configuration.php');
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Test Image Manager</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/colorbox.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.iviewer.css?<?php echo filemtime('../../../library/css/jquery.iviewer.css');?>"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<script src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/work_view/work_view.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/jquery.colorbox-min.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/jquery.iviewer.min.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
		.HscrollDiv{
			overflow-x:hidden;  
			overflow-y:auto;  
			/*
			min-width: 500px; 
			position: relative;
			white-space: nowrap; 
			text-wrap:none;
			*/
			max-height:400px;			
		}
		.scanDate{cursor:pointer; font-weight:bold; color:#fff; text-align:center; width:100%; padding:0px 0px; background:#000; opacity:.5; filter:alpha(opacity=50); margin-top:-3px;}
		.scanDateSel{font-weight:bold; color:#fff; text-align:center; width:100%; padding:0px 0px; background:#000; opacity:.9; filter:alpha(opacity=90); margin-top:-3px;}
		.filmstrip td span.site{    float: right;z-index: 9;position: absolute;right: 0;}
		.scanDate .check{display:none;}
		.scanDateSel .check{display:inline-block;}
		.filmstrip div{position:relative; margin:0px 5px 5px 0px;}
		.filmstrip div a img{height:100px; margin:0px;}
		.border2{border:2px solid #333;}
		.iviewer{ position: relative;}
        .wrapper{overflow: hidden;}
		
		.test_tabs{
			width:90%!important;
		}
		
		.test_tabs ul {
			padding: 3px;
		}
		
		.dropdown-menu{
			max-height: 100px;
			overflow-y: scroll;
			position:absolute;
		}
		.mainwhtbox { padding-left:10px; padding-right:10px;}
		.purple_bar_thin {padding:0px 5px; font-size:13px; font-weight:bold;}
		table td {border:none !important;}
		#div_right_preview_close{position:absolute; z-index:1000;}
	</style>
	</head>
	<body>
  	
  	<div class="container-fluid">
   	<div class="mainwhtbox">
			<div class="row">		
				<div class="col-sm-12 purple_bar" id="header_row">
					
						<div class="col-sm-3">
							<label>Eye Test Manager</label>
						</div>	
						<div class="col-sm-4 text-center">
							<label><?php echo $pt_name_arr['2'].', '.$pt_name_arr['1'].' '.substr($pt_name_arr['3'],0,1).' - '.$pt_name_arr['0'];?></label>
						</div>	
						<div class="col-sm-5">
							<div class="row">
								<div class="col-sm-7">
									<div class="checkbox nowrap">
										<!--
                                        <input type="checkbox" name="chk_loadBigImage" id="chk_loadBigImage">
										<label for="chk_loadBigImage"><strong>Startup Image Preview</strong></label>
                                        
                                        -->
									</div>
								</div>
								<div class="col-sm-3">
									<?php if($topcon_enable != 0){ ?>
										<button class="btn btn-success" onClick="open_topcon_window()">Synergy</button>	
									<?php } ?>	
								</div>
								<div class="col-sm-2 text-right">
									<!--<button class="btn btn-success" onClick="loadDefaultPreview();" title="Reset View">
										<i class="glyphicon glyphicon-refresh"></i>
									</button>-->
								</div>
							</div>
						</div>	
						
				</div>
				
        <div id="page_body" class="col-sm-12 pt5"></div>
        
			</div>	
  	</div>
    
    <div class="mainwhtbox" id="div_film_strip">
    	<div class="row">			
				<div class="col-sm-12">
					<div class="row">
						<div class="HscrollDiv">
							<?php 
								$noTest = true;
								$scanImgArr = $TestWiseImgs = $TestsSeq = array();
								foreach($qry_tests as $testDname=>$q){
									/***THIS PART WRITTEN TO MANAGE TEMPLATE BASED TESTS*****/
									$testShownameTemp = explode('~|~',substr($testDname,1));
									$testShowname = $testShownameTemp[0];
									$testPERMAname = $testShownameTemp[1];
									$testDnameType = substr($testDname,0,1);
									$testVersionId	= $testShownameTemp[3];
									if($testDnameType == '1' && $testVersionId=='0'){$testPERMAname=$testPopName='TemplateTests'; $testDname='TemplateTests'.$testShownameTemp[1];}
									if($testDnameType == '1' && $testVersionId!='0'){$testPERMAname=$testPopName='Customtests'; $testDname='Customtests'.$testShownameTemp[1];}
									else{$testDname = $testPopName = $testPERMAname; }
									$testDname = str_replace('/','',$testDname);
									$testPopName = str_replace('/','',$testPopName);
									/********************************************************/
									
									$res = imw_query($q);
									$totalTests = imw_num_rows($res);
									if($res && imw_num_rows($res)>0){
										$noTest = false;
										$str = '';
										$count = 1;
									?>
									<div id="div_<?php echo $testDname;?>" class="subheading" style="display:block; padding:0px 5px; margin:0px;">
										<div class="row">
											<div class="purple_bar purple_bar_thin" style="width:99%">
												<span ><?php echo $testShowname;?></span>
											</div>
											<div class="clearfix"></div>
											<div class="test_tabs">
												<ul class="nav nav-pills list-inline">
												<?php
													while($rs = imw_fetch_assoc($res)){
														$examDate 	= $rs['examDate1'];
														$test_name	= $rs['test_name'];
														$TestsSeq[]	= $testDname.'@'.$examDate;
														$test_id	= $rs['testId'];
														$priProID	= $rs['providerID']; //patient Primary Physician
														$TestOrdrBy	= $rs['ordrby']; 	// Test order By.
														if(intval($TestOrdrBy)==0){$TestOrdrBy = $priProID;}
														$SeenByPhy	= $rs['phyName'];	//Test interpreted by.
														if(intval($TestOrdrBy)>=0 && (empty($SeenByPhy) || $SeenByPhy == 0)){
															$testUnreadImg = '<li><a href="javascript:void(0)" style="padding:5px!important;"><i class="glyphicon glyphicon-exclamation-sign text-danger" ></i></a></li>';
															
														}else{
															$testUnreadImg="";
														}
														$anchor_class = 'style="padding:2px 5px!important;"';
														$active_class = ($count == "1")?"active":"";
																/*
																if($totalTests > 4){
																	//if($count < 3){
																		echo $testUnreadImg.'<li class="'.$active_class.'"><a href="javascript:void(0)" class="a_clr1" onClick="show_images(\''.$testDname.'\',\''."tab_".$testDname."_".$test_id.'\',this,\''.$test_id.'\',\''.$examDate.'\',\''.$testDnameType.'\');" onDblClick="openTest(\''.$testPopName.'\',\''.$test_id.'\');" '.$anchor_class.'>'.$examDate.'</a></li>';
																	//}
																	else if($count == 3){
																		 echo $testUnreadImg.'<li class="'.$active_class.'"><a href="javascript:void(0)" class="a_clr1" onClick="show_images(\''.$testDname.'\',\''."tab_".$testDname."_".$test_id.'\',this,\''.$test_id.'\',\''.$examDate.'\',\''.$testDnameType.'\');" onDblClick="openTest(\''.$testPopName.'\',\''.$test_id.'\');" '.$anchor_class.'>'.$examDate.'</a></li>';
																		 echo '<li class="'.$active_class.' dropdown" ><span class="btn btn-default dropdown-toggle glyphicon glyphicon-triangle-bottom" data-toggle="dropdown"></span>';
																		 echo ' <ul class="dropdown-menu">';
																		 echo '<li onClick="show_images(\''.$testDname.'\',\''."tab_".$testDname."_".$test_id.'\',this,\''.$test_id.'\',\''.$examDate.'\',\''.$testDnameType.'\');" onDblClick="openTest(\''.$testPopName.'\',\''.$test_id.'\');"><a href="javascript:void(0)">'.$examDate.'</a></li>';
																	 }else if($count>3 &&  $count < $totalTests){
																		 echo '<li onClick="show_images(\''.$testDname.'\',\''."tab_".$testDname."_".$test_id.'\',this,\''.$test_id.'\',\''.$examDate.'\',\''.$testDnameType.'\');" onDblClick="openTest(\''.$testPopName.'\',\''.$test_id.'\');"><a href="javascript:void(0)">'.$examDate.'</a></li>';
																	 }else if($count == $totalTests){
																		 echo "</ul>";
																		 echo "</li>";
																	}
																} 
																else if($totalTests <= 4){
																	*/
																	$testDnameSpan = str_replace(" ","_",$testDname);
																	echo $testUnreadImg.'<li class="'.$active_class.'"><a href="javascript:void(0)" class="a_clr1" onClick="show_images(\''.$testDname.'\',\''."tab_".$testDname."_".$test_id.'\',this,\''.$test_id.'\',\''.$examDate.'\',\''.$testDnameType.'\',\''.'#span_'.$testDnameSpan.'_'.$test_id.'\');" onDblClick="openTest(\''.$testPopName.'\',\''.$test_id.'\');" '.$anchor_class.'>'.$examDate.'</a></li>';
															//	}//openTest(\''.$testDname.'\',\''.$test_id.'\');
															$testDname1 = $testPERMAname;
															if($testPERMAname=='A/Scan')						{$testDname1='Ascan';}
															else if($testPERMAname=='B-Scan')					{$testDname1='BScan';}
															else if($testPERMAname=='Cell Count')				{$testDname1='CellCount';}
															else if($testPERMAname=='External/Anterior')		{$testDname1='discExternal';}
															else if($testPERMAname=='Fundus')					{$testDname1='Disc';}
															else if($testPERMAname=='HRT')						{$testDname1='NFA';}
															else if($testPERMAname=='IOL Master')				{$testDname1='IOL_Master';}
															else if($testPERMAname=='Laboratories')				{$testDname1='TestLabs';}
															else if($testPERMAname=='Other')					{$testDname1='TestOther';}
															else if($testPERMAname=='Pachy')					{$testDname1='Pacchy';}
															else if($testPERMAname=='Topography')				{$testDname1='Topogrphy';}
															$display = ($str == "")?"block":"none";
															
															$str .= '<span id="span_'.$testDnameSpan.'_'.$test_id.'" class="table_collapse_autoW filmstrip" style="display:'.$display.'; background">';
															if($count == 1){
																$q2 = "SELECT *, DATE_FORMAT(modi_date,'".get_sql_date_format('','y')."') AS modi_date1, DATE_FORMAT(created_date,'".get_sql_date_format('','y')."') AS created_date1 
																		FROM ".constant('IMEDIC_SCAN_DB').".scans s 
																		WHERE s.image_form='".$testDname1."' 
																			  AND  s.test_id='".$test_id."' 
																			  AND s.status='0' 
																			  AND s.patient_id='".$session_patient."' 
																		ORDER BY site DESC";
																$res2 = imw_query($q2);//echo $q2;
																if($res2 && imw_num_rows($res2)>0){
																   //$display = ($str == "")?"block":"none";	
																   $str .= '<div id="tab_'.$testDnameSpan.'_'.$test_id.'" class="table_collapse_autoW filmstrip" style="display:'.$display.';">';
																   while($rs2 = imw_fetch_assoc($res2)){
																		$scan_id		= $rs2["scan_id"];
																		$scanType 		= $rs2["file_type"];
																		$scanDt_cr 		= $rs2["created_date"];
																		$scanDt_up 		= $rs2["modi_date1"]!='00-00-00' ? $rs2["modi_date1"] : $rs2["created_date1"];
																		$scanPth 		= $rs2['file_path'];//str_replace(".PDF",".pdf",$rs2['file_path']);
																		$image_form 	= $rs2["image_form"];
																		$scan_site		= intval($rs2['site']);
																		$fileInfoArr 	= pathinfo($scanPth);
																		$link_type_class= "jpg_colorbox";
																		$link_file_path = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$scanPth;
																		$disc_full_path	= $oSaveFile->getFilePath($scanPth,'i');
																		
																		if(strtolower(substr($link_file_path, -3))=='pdf'){$link_type_class="pdf_colorbox";}
																		if(strtolower($scanType) == "application/pdf" || strtolower($fileInfoArr['extension']) == 'pdf' || strtoupper($fileInfoArr['extension']) == 'PDF'){
																			$imgSrc_file = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$scanPth;
																			if(file_exists($disc_full_path)){
																				$pdf_info = pathinfo($disc_full_path);
																				$pdf_basename 	= $pdf_info['basename'];
																				$pdf_dir	  	= $pdf_info['dirname'];
																				$pdf_name	  	= $pdf_info['filename'];
																				$pdf_thumbnail_dest	= $pdf_dir."/thumbnail";
																				$pdf_thumb_dest	= $pdf_dir."/thumb";
																				$pdf_jpg_dest	= $pdf_dir."/".$pdf_name.".jpg";
																				
																				$pdf_jthumbnail_dest= $pdf_thumbnail_dest."/".$pdf_name.".jpg";
																				$pdf_jthumb_dest= $pdf_thumb_dest."/".$pdf_name.".jpg";
																				
																				if(is_dir($pdf_thumb_dest) == false){
																					mkdir($pdf_thumb_dest, 0777, true);
																				}
																				if(is_dir($pdf_thumbnail_dest) == false){
																					mkdir($pdf_thumbnail_dest, 0777, true);
																				}
																				$source = realpath($pdf_dir."/".$pdf_basename).'[0]';
																				$exe_path = $GLOBALS['IMAGE_MAGIC_PATH'];
																				if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}
																				$pdf_to_jpg_fail = true;
																				if(file_exists($pdf_jpg_dest)){$pdf_to_jpg_fail = false;}
																				if (!file_exists($pdf_jpg_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
																					exec($exe_path.'convert -density 300 -flatten "'.$source.'" -quality 95 -thumbnail 1500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jpg_dest.'"');
																					if(file_exists($pdf_jpg_dest)){$pdf_to_jpg_fail = false;}
																				}else if(!file_exists($pdf_jpg_dest)){
																					$pdf_jpg_dest = $GLOBALS['webroot'].'/library/images/pdfimg.png';
																				}
																				
																				if (!$pdf_to_jpg_fail && !file_exists($pdf_jthumbnail_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
																					exec($exe_path.'convert -flatten "'.$pdf_jpg_dest.'" -quality 95 -thumbnail 78 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumbnail_dest.'"');
																				}else if(!file_exists($pdf_jthumbnail_dest)){
																					$pdf_jthumbnail_dest = $GLOBALS['webroot'].'/library/images/pdfimg.png';
																				}
																				
																				if (!$pdf_to_jpg_fail && !file_exists($pdf_jthumb_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
																					exec($exe_path.'convert -flatten "'.$pdf_jpg_dest.'" -quality 95 -thumbnail 500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumb_dest.'"');
																				}else if(!file_exists($pdf_jthumb_dest)){
																					$pdf_jthumb_dest = $GLOBALS['webroot'].'/library/images/pdfimg.png';
																				}
																				
																				$http_pdf_path  = substr($link_file_path,0,strlen($link_file_path)-strlen($pdf_basename));
																				$imgSrc 		= $http_pdf_path."/thumbnail/".$pdf_name.".jpg";
																				if(!is_file($pdf_thumbnail_dest."/".$pdf_name.".jpg") || !file_exists($pdf_thumbnail_dest."/".$pdf_name.".jpg")){
																					$imgSrc 		= $GLOBALS['webroot'].'/library/images/pdfimg.png';
																				}
																				
																				$link_file_path	= $http_pdf_path.$pdf_basename;
																				$thumb_img = '<a class="'.$link_type_class.' cboxElement" href="'.$link_file_path.'" title="'.$testDname.' - '.$examDate.'" target="_blank"><img id="imgId'.$scanId.'" src="'.$imgSrc.'" style="cursor:hand;"  alt="Pdf File" class="border thumb_img"></a>';
																			}else {
																				continue;
																			}
																		}else{
																			$tempDir = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$_SESSION["patient"]."/tmp";
																			if(is_dir($tempDir) == false){
																				mkdir($tempDir, 0777, true);
																			}
																			
																			$pathThumb = $imgSrc = "";
																			$imgPath = $oSaveFile->getFilePath($scanPth,"i");
																			$imgPathInfo = pathinfo($imgPath);
																			$imgDir 	= $oSaveFile->getFileDir($imgPath);
																			$imgName 	= $imgPathInfo['basename'];
																			$imgDirNameARR	= explode($GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$_SESSION["patient"]."/",$imgPathInfo['dirname']);
																			$imgDirName		= $imgDirNameARR[1];
																			unset($imgDirNameARR);
																			if(!is_dir($imgDir."/thumbnail")){
																				mkdir($imgDir."/thumbnail",0700);
																			}
																			$thumbPath = realpath($imgDir."/thumbnail")."/".$imgName;//die();
																			if(!file_exists($thumbPath)){
																				$oSaveFile->createThumbs($imgPath,$thumbPath);
																			}
																			if(!is_dir($imgDir."/thumb")){
																				mkdir($imgDir."/thumb",0700);
																			}
																			$mthumbPath = realpath($imgDir."/thumb")."/".$imgName;//die();
																			if(!file_exists($mthumbPath)){
																				$oSaveFile->createThumbs($imgPath,$mthumbPath,500,500);
																			}
																			$thumb_img = '<a class="iviewer '.$link_type_class.' cboxElement" href="'.$link_file_path.'" title="'.$testDname.' - '.$examDate.'" target="_blank"><img src="'.$GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$_SESSION["patient"].'/'.$imgDirName."/thumbnail/".$imgName.'" class="border thumb_img">
																			</a>';
																		}
																		
																		//arrays to use in JS
																		$link_file_path = str_replace('\\', '/', $link_file_path);
																		$scanImgArr['a'.$scan_id] 	= $link_file_path;
																		$TestWiseImgs[$testDname.'@'.$examDate][]	= $link_file_path;
																		$pic_site = $arr_scan_site[$scan_site];
																		$site_hide='';
																		if($scan_site==0){$site_hide=' hide';}
																   
																   $str .= '<div style="display:inline-block">
																		<span class="badge badge-primary site'.$site_hide.'">'.$pic_site.'</span>'.$thumb_img.'
																		<div class="scanDate" onClick="SelectMe(this,\''.$testDname.'\',\''.$test_id.'\',\''.$scan_id.'\')"><span class="check">&#10003; </span>'.$scanDt_up.'</div>
																	</div>';
																   }
																   $str .= '</div>';
																}
															}
															$str .= '</span>';
															$count++;
													} ?>	
												</ul>	
											</div>	
										</div>
										<?php echo $str; ?>
									</div>
									<?php
									}
								}
								$TestsSeq = array_unique($TestsSeq);
								if($noTest){
									echo '<div class="alert alert-info f-bold text-center">No test done.</div>';
								}
							?>	
						</div>
					</div>	
				</div>
			</div>
   	</div>
    
    <div class="mainwhtbox mt0" id="div_module_buttons">
			<div class="row">   				
				<div class="col-sm-12 ad_modal_footer" id="module_buttons">
					<div class="row">
						<div class="text-center">
							<input type="button" class="btn btn-success" value="Images Only" onClick="ShowSelected();"> &nbsp;&nbsp;
             	<input type="button" class="btn btn-success" onClick="ShowSelected(1);" value="Images &amp; Interpretation" > &nbsp;&nbsp;
		<input type="button" id="btn_sel_all_images" class="btn btn-success" onClick="selectAllImages();" value="Select All Images" > &nbsp;&nbsp;
							<input type="button" class="btn btn-danger" value="Close" onClick="window.close()">	
						</div>
					</div>	
				</div>
    	</div>
  	</div>
    
    <div id="temp_container" style="display:none; width:auto; position:absolute;top:0px; left:0px; z-index:6000;"></div>
    
    </div>
	</body>
	<?php 
		$global_js_arr = array();
		$global_js_arr['root_dir'] = $GLOBALS['rootdir'];
		$global_js_arr['big_new_img'] = $big_img_file_name_new;
		$global_js_arr['height'] = $_SESSION['wn_height'];
		$global_js_arr['strScanArr'] = $scanImgArr;
		$global_js_arr['strTestWiseImg'] = $TestWiseImgs;
		$global_js_arr['strTestsSeq'] = $TestsSeq;
		$js_php_arr = json_encode($global_js_arr);
	?>
	<script>
		window.focus();
		window.onload =function()
		{
			var avail_w_90 = (screen.availWidth * 0.99 ); //99%
			var avail_h_90 = (screen.availHeight * 0.99 ); //99%
			var parWidth = (screen.availWidth > 1500) ? 1500 :  avail_w_90;
			var parHeight = (screen.availHeight > 850) ? 850 :  avail_h_90;
			
			window.resizeTo(parWidth,parHeight);
			
			var t = parseInt((screen.availHeight - window.outerHeight) / 2)
			var l = parseInt((screen.availWidth - window.outerWidth) / 2)
			window.moveTo(l,t);
		}
		
		var js_php_arr = JSON.parse('<?php echo $js_php_arr; ?>');
	</script>
	<script src="<?php echo $library_path; ?>/js/eye_test_manager.js?<?php echo filemtime('../../../library/js/eye_test_manager.js');?>" type="text/javascript"></script>
</html>