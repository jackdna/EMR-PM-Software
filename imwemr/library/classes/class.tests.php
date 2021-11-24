<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
Coded in PHP 7
Purpose: Providing required data to main interface of login (header/footer).
Access Type: include file.
*/
require_once(dirname(__FILE__).'/common_function.php');
require_once(dirname(__FILE__).'/class.imedicmonitor.php');
//require_once(dirname(__FILE__).'/class.security.php');
require_once(dirname(__FILE__).'/class.app_base.php');
require_once(dirname(__FILE__).'/SaveFile.php');

class Tests
{
	public $logged_user, $logged_user_type, $logged_user_name, $res_fellow_sess, $login_facility;
	public $patient_id;
	//private $objSecurity; 
	private $objAppBase,$objSaveFile;
	
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct()
	{
		$this->session			= $_SESSION;
		$this->logged_user		= (isset($this->session["authId"]) 				&& $this->session["authId"] != "") 				? $this->session["authId"] 				: 0;
		$this->logged_user_type	= (isset($this->session["logged_user_type"]) 	&& $this->session["logged_user_type"] != "") 	? $this->session["logged_user_type"]	: 0;
		$this->logged_user_name	= (isset($this->session["authProviderName"]) 	&& $this->session["authProviderName"] != "") 	? $this->session["authProviderName"]	: '';
		$this->res_fellow_sess 	= (isset($this->session["res_fellow_sess"]) 	&& $this->session["res_fellow_sess"] != "") 	? $this->session["res_fellow_sess"] 	: '';
		$this->login_facility 	= (isset($this->session["login_facility"]) 		&& $this->session["login_facility"] != "") 		? $this->session["login_facility"] 		: 0;
		//$this->objSecurity		= new security();
		$this->objAppBase		= new app_base();
		$this->objSaveFile		= new SaveFile();
		$this->patient_id		= isset($this->session["patient"]) ? $this->session["patient"] : '';
	}
	
	/******GET ENABLED TESTS FROM ADMIN, RETURN RESULTSET AS ARRAY*******/
	function get_active_tests($from=''){
		$arr_all_test = false;
		$q_where = " AND status=1";
		if($from=='savedtests') $q_where = "";// for patient saved tests, removing active test condition.
		if($from=='tmanager') $q_where .= " AND t_manager=1";
		$q_tests = "SELECT * FROM tests_name WHERE del_status=0 ".$q_where." ORDER BY temp_name";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)>0){
			$arr_all_test = array();
			while($rs_test=imw_fetch_assoc($res_tests)){
				$arr_all_test[$rs_test['id']] = $rs_test;
			}
		}
		return $arr_all_test;
	}
	
	/******GET ENABLED TESTS FROM ADMIN, RETURN RESULTSET AS ARRAY*******/
	function get_table_cols_by_test_table_name($tbl,$tbl_type=''){
		$q_tests = "SELECT * FROM tests_name WHERE del_status=0 AND test_table='$tbl' LIMIT 1";
		if($tbl_type=='id') $q_tests = "SELECT * FROM tests_name WHERE id='$tbl' LIMIT 1";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)>0){
			$rs_tests = imw_fetch_assoc($res_tests);
			$formid_key = $this->get_test_table_formid_column($rs_tests['test_table']);
			$rs_tests['formid_key'] = $formid_key;
			return $rs_tests;
		}
		return false;
	}
	
	/********GET TABLE FORM ID COLUMN********/
	function get_test_table_formid_column($test_table_name){
		$formid_key = 'formId';
		if(in_array($test_table_name,array('nfa','oct','oct_rnfl','ivfa','icg','surgical_tbl','iol_master_tbl','test_gdx',''))){
			$formid_key = 'form_id';
		}
		return $formid_key;
	}
	
	/******GET MOST RECENT OR SPECIFIC VERSION DETAIL FOR TEMPLATE BASED TESTS*******/
	function get_template_test_version_data($tests_name_pk_id,$tests_version_pk_id=''){
		$q_tests = "SELECT * FROM tests_version WHERE tests_name_id='$tests_name_pk_id' ORDER BY id DESC LIMIT 1";
		$tests_version_pk_id = intval($tests_version_pk_id);
		if($tests_version_pk_id>0) $q_tests = "SELECT * FROM tests_version WHERE tests_name_id='$tests_name_pk_id' AND id='$tests_version_pk_id' LIMIT 1";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)==1){
			return imw_fetch_assoc($res_tests);
		}
		return false;
	}
	
	/*******GET PATIENT SPECIFIC SAVED TESTS LIST FROM THE ENABLED TESTS*************/
	function get_patient_saved_tests($patient_id,$ActiveTests=false,$todays=false,$selectiveTable=''){
		$return = false;
		if(!is_array($ActiveTests)) $ActiveTests = $this->get_active_tests('savedtests');
		$today_sql = "";
		
		foreach($ActiveTests as $thisTest){
			if(!empty($selectiveTable) && $selectiveTable != $thisTest['test_table']){
				continue;
			}
			
			$select_part = ""; $where_part = "";
			if($thisTest['test_table']=='test_other' || $thisTest['test_table']=='test_custom_patient'){
				$where_part = " AND test_template_id = '0'";
				$select_part= ", test_other ";
				if($thisTest['test_type']=='1'){$where_part = " AND test_template_id = '".$thisTest['id']."'";}
			}
			
			if($todays){
				$today_sql = " AND DATE_FORMAT(".$thisTest['exam_date_key'].",'%Y-%m-%d')='".date('Y-m-d')."' ";
			}

			$q = "SELECT ".$thisTest['test_table_pk_id']." AS tId, DATE_FORMAT(".$thisTest['exam_date_key'].", '".get_sql_date_format()."') AS dt,
						".$thisTest['performed_key']." AS prfBy, ".$thisTest['phy_id_key']." as phy, purged".$select_part."
						FROM ".$thisTest['test_table']." WHERE ".$thisTest['patient_key']."='".$patient_id."' AND del_status='0' 
						".$where_part.$today_sql."	ORDER BY ".$thisTest['exam_date_key']." DESC, ".$thisTest['test_table_pk_id']." DESC";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){//echo $q.'<br>'.$thisTest['id'].'<hr>';
				if(!$return) $return = array();
				while($rs = imw_fetch_assoc($res)){
					$return[$thisTest['id']]['test_id'] 	= $thisTest['id'];
					$return[$thisTest['id']]['test_type'] 	= $thisTest['test_type'];
					$return[$thisTest['id']]['test_table'] 	= $thisTest['test_table'];
					$return[$thisTest['id']]['temp_name'] 	= $thisTest['temp_name'];
					$return[$thisTest['id']]['test_name'] 	= $thisTest['test_name'];
					$return[$thisTest['id']]['t_manager']	= $thisTest['t_manager'];
					$return[$thisTest['id']]['show_name'] 	= $thisTest['temp_name'];
					if($thisTest['test_table']=='test_other' && $thisTest['test_type']=='0'){
						$return[$thisTest['id']]['show_name']= $rs['test_other'];
					}
					$this_rs_flag_status					= $this->get_test_flag_status($rs,$thisTest['test_name']);
					$rs['test_flag_color']					= $this_rs_flag_status;
					$return[$thisTest['id']]['test_rs'][]	= $rs;
				}
			}
			
		}
		return $return;		
	}
	
	/******GET RED/GREEN/YELLOW FLAG STATUS FOR PATIENT SPECIFIC SAVED TESTS********/
	function get_test_flag_status($test_rs,$test){
		$zeissMapVals = $this->ZeissTestVals();
		$flag_color = 'red-flag';//'hide';
		if(!empty($test_rs['phy'])){
			$flag_color = "green-flag";
		}else if(!empty($test_rs['prfBy'])){
			$flag_color = "red-flag";
		}
		//pre($test_rs,1);
		/*code, Purpose: Hl7 message Flag for Zeiss*/
		if(constant("ZEISS_FORUM")=="YES"){
			$sqlD = imw_query("SELECT `msg_type`, `sent`, `status` FROM `hl7_sent_forum` WHERE `test_id`='".$test_rs['tId']."' AND `test_name`='".$zeissMapVals[$test]."' ORDER BY `id` DESC LIMIT 1");
			if($sqlD && imw_num_rows($sqlD)>0){
				$data = imw_fetch_assoc($sqlD);
				if($data['sent']==1 && $data['status']=="Y"){
					if($data['msg_type']!="FORUM_DELETE"){
						$sqlResp = imw_query("SELECT `id` FROM `hl7_received_forum` WHERE `test_id`='".$test_rs['tId']."' AND `test_name`='".$zeissMapVals[$test]."' ORDER BY `id` DESC LIMIT 1");
						if($sqlResp && imw_num_rows($sqlResp)>0){
							$data1 = imw_fetch_assoc($sqlResp);
							if(!empty($test_rs['phy'])){
								$flag_color = "green-flag";
							}
							else{
								$flag_color = "yellow-flag";
							}
						}
						else{
							$flag_color = "red-flag";
						}
					}
				}
				else{
					if($data['msg_type']!="FORUM_DELETE"){
						$flag_color = "red-flag";
					}
				}
			}
		}
		/*End code*/
		return $flag_color;
	}
	
	/******GET TOOLTIP TEXT FOR RED/GREEN/YELLOW FLAG************/
	function get_test_flag_title($flag_color){
		$return = '';
		switch($flag_color){
			case 'red-flag': 	$return=' title="Un-interpreted Test"'; 	break;
			case 'green-flag': 	$return=' title="Interpreted Test"'; 		break;
			case 'yellow-flag':	$return=' title="Test Results Received"'; 	break;
		}
		return $return;
	}
	
	/******GET SCAN TEST NAME COLUMN VALUE BY DB_TEST_NAME****/
	function get_scan_upload_test_name($test_table_name,$test_type=0){
		$return = '';
		if($test_table_name=='test_other') $test_table_name .= $test_type;
		switch($test_table_name){
			case 'surgical_tbl':		{$return = 'Ascan';			break;}
			case 'test_bscan':			{$return = 'BScan';			break;}
			case 'test_labs':			{$return = 'TestLabs';		break;}
			case 'test_cellcnt':		{$return = 'CellCount';		break;}
			case 'icg':					{$return = 'ICG';			break;}
			case 'nfa':					{$return = 'NFA';			break;}
			case 'vf':					{$return = 'VF';			break;}
			case 'vf_gl':				{$return = 'VF-GL';			break;}
			case 'oct':					{$return = 'OCT';			break;}
			case 'oct_rnfl':			{$return = 'OCT-RNFL';		break;}
			case 'test_gdx':			{$return = 'GDX';			break;}
			case 'pachy':				{$return = 'Pacchy';		break;}
			case 'ivfa':				{$return = 'IVFA';			break;}
			case 'disc':				{$return = 'Disc';			break;}
			case 'disc_external':		{$return = 'discExternal';	break;}
			case 'topography':			{$return = 'Topogrphy';		break;}
			case 'iol_master_tbl':		{$return = 'IOL_Master';	break;}
			case 'test_other0':			{$return = 'TestOther';		break;}
			case 'test_other1':			{$return = 'TemplateTests';	break;}
			case 'test_custom_patient':	{$return = 'CustomTests';	break;}
			default:	 				{$return = '';				break;}
		}
		return $return;
	}
	
	/********GET PARTICULAR TEST SPECIFIC SCAN/UPLOAD IMAGES***********/
	function get_test_images($patient_id,$testTable,$test_id,$test_type=0,$check_create_thumb=false){
		$testName = $this->get_scan_upload_test_name($testTable,$test_type);
		$q = "SELECT 
				DATE_FORMAT(doc_upload_date, '%m-%d-%Y %H:%i:%s') docUploadDate, 
				DATE_FORMAT(rename_date, '%m-%d-%Y') reNamedDate, 
				file_type, 
				scan_id, 
				file_path, 
				multi_doc_upload_comment AS cmnts, 
				DATE_FORMAT(created_date, '%m-%d-%Y %H:%i:%s') created_date, 
				testing_docscan AS cmnts2, 
				scan_or_upload, 
				image_form, 
				test_id, 
				image_name AS fileName, site AS imgSite 
			  FROM ".constant("IMEDIC_SCAN_DB").".scans 
			  WHERE LOWER(image_form) = '".$testName."' 
			  AND test_id='".$test_id."' 
			  AND patient_id = '".$patient_id."' 
			  ORDER BY CASE LOCATE('DICOM_FILES',file_path) WHEN 0 THEN 0  ELSE created_date END ASC, doc_upload_date ASC, scan_id DESC 
			  LIMIT 0,30";
		$res = imw_query($q);
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				if($check_create_thumb)	$rs['scan_uploads'] = $this->test_image_thumbs($rs);				
				$return[] = $rs;
			}
		}
		return $return;
	}
	
	function get_test_images_by_id($sid){		
		$rs=array();
		$q = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id='".$sid."' ";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);			
			$rs['scan_uploads'] = $this->test_image_thumbs($rs);			
		}
		return $rs;
	}
	
	/***IT WILL CHECK IF THUMBS AVAILABLE OR NOT; IF NOT AVAILABLE, THEN IT WILL CREATE****/
	function test_image_thumbs($rs){
		global $oSaveFile;
		$images_arr = array();
		$doc_date 		= $rs['docUploadDate'];
		$doc_file_type	= $rs['file_type'];
		$doc_scan_id	= $rs['scan_id'];
		$doc_file_path	= $rs['file_path'];
		$doc_file_name	= $rs['fileName'];

		$disc_full_path	= $oSaveFile->getFilePath($doc_file_path,'i');
		if(is_file($disc_full_path) && file_exists($disc_full_path)){
			$path_info 			= pathinfo($disc_full_path);
			$path_basename 		= $path_info['basename'];
			$path_dir	  		= $path_info['dirname'];
			$path_name	  		= $path_info['filename'];
			$path_extension		= $path_info['extension'];
			$path_thumbnail_dest= $path_dir."/thumbnail";
			$path_thumb_dest	= $path_dir."/thumb";
			
			if(is_dir($path_thumb_dest) == false){
				mkdir($path_thumb_dest, 0777, true);
			}
			if(is_dir($path_thumbnail_dest) == false){
				mkdir($path_thumbnail_dest, 0777, true);
			}
			
			$images_arr['original'] = $oSaveFile->getFilePath($doc_file_path,'w');
			$images_arr['extension']= $path_extension;
			$images_arr['file_name']= $path_name.".".$path_extension;
			$source = realpath($path_dir."/".$path_basename);
			
			if(strtolower($doc_file_type) == "application/pdf" || strtolower($path_extension) == 'pdf' || strtoupper($path_extension) == 'PDF'){
				//USE CONVERT COMMAND
				$pdf_jpg_dest			= $path_dir."/".$path_name.".jpg";//BIG IMAGE USED IN ZOOM IN TEST IMAGE MANAGER.
				$pdf_jthumbnail_dest	= $path_thumbnail_dest."/".$path_name.".jpg";
				$pdf_jthumb_dest		= $path_thumb_dest."/".$path_name.".jpg";

				$source = $source.'[0]';
				$exe_path = $GLOBALS['IMAGE_MAGIC_PATH'];
				if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}
				if(constant("STOP_CONVERT_COMMAND")!="YES"){
					if (!file_exists($pdf_jpg_dest)){//BIG IMAGE; USED IN COMPARISION IN TEST IMAGE MANAGER.
						exec($exe_path.'convert -density 300 -flatten "'.$source.'" -quality 95 -thumbnail 1500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jpg_dest.'"');
					}
					//IF ABOVE CONVERT COMMAND SUCCESSFULLY CREATED JPG OF FIRST PAGE.
					//CREATE REST OF THE THUMB IMAGES FROM THE CREATED JPG INSTEAD OF PDF (FOR BETTER SPEED).
					$NEWsource = realpath($pdf_jpg_dest);
					if(!file_exists($pdf_jthumbnail_dest))
						exec($exe_path.'convert -flatten "'.$NEWsource.'" -quality 95 -thumbnail 78 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumbnail_dest.'"');
					if (!file_exists($pdf_jthumb_dest))
						exec($exe_path.'convert -flatten "'.$NEWsource.'" -quality 95 -thumbnail 500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumb_dest.'"');
				}
				
				/*******MAKE FINAL WEB URL AND SEND BACK******/
				if(!file_exists($pdf_jpg_dest)) 		$pdf_jpg_dest 			= $GLOBALS['webroot'].'/library/images/pdfimg.png';
				if(!file_exists($pdf_jthumbnail_dest)) 	$pdf_jthumbnail_dest 	= $GLOBALS['webroot'].'/library/images/pdfimg.png';
				if(!file_exists($pdf_jthumb_dest)) 		$pdf_jthumb_dest 		= $GLOBALS['webroot'].'/library/images/pdfimg.png';
				$images_arr['large'] 	= $oSaveFile->getFilePath($pdf_jpg_dest,'w2');
				$images_arr['mid'] 		= $oSaveFile->getFilePath($pdf_jthumb_dest,'w2');
				$images_arr['small'] 	= $oSaveFile->getFilePath($pdf_jthumbnail_dest,'w2');
			}else{
				$thumbnailPath = $path_thumbnail_dest."/".$path_basename;
				if(!file_exists($thumbnailPath)){
					$oSaveFile->createThumbs($source,$thumbnailPath);
				}
				$thumbPath = $path_thumb_dest."/".$path_basename;
				if(!file_exists($thumbPath)){
					$oSaveFile->createThumbs($source,$thumbPath,500,500);
				}
				$images_arr['large'] 	= $images_arr['original'];
				$images_arr['mid'] 		= $oSaveFile->getFilePath($thumbPath,'w2');
				$images_arr['small'] 	= $oSaveFile->getFilePath($thumbnailPath,'w2');
			}
			
			//RESET DIRECTORY PERMISSIONS
		//	chmod($path_thumb_dest, 0644);
		//	chmod($path_thumbnail_dest, 0644);

			return $images_arr;
			//pre($images_arr,1);
		}
	}
	
	/********ZEISS FORUM RELATED TEST NAMES******/
	function ZeissTestVals(){
		return array("A/Scan"=>"A/SCAN",
					"B-Scan"=>"BSCAN",
					"Cell Count"=>"CELLCOUNT",
					"External/Anterior"=>"DISCEXTERNAL",
					"Fundus"=>"DISC",
					"ICG"=>"ICG",
					"IVFA"=>"IVFA",
					"IOL Master"=>"IOL-MASTER",
					"OCT"=>"OCT",
					"OCT-RNFL"=>"OCT-RNFL",
					"Topography"=>"TOPOGRAPHY",
					"VF"=>"VF",
					"VF-GL"=>"VF-GL");
	}
	
	/********DISPLAYABLE PATIENT NAME - ID AFTER PASSING PATIENT ID.****/
	function show_patient_name_id($pid){
		$q = "SELECT CONCAT(lname,', ',fname,SUBSTR(mname,1,1),' - ',id) AS pt_name_id FROM patient_data WHERE id = '".$pid."' LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['pt_name_id'];
		}
		return false;
	}
	
	/********GET USER ID WHO ORDEREDED THIS TEST***********/
	function get_order_by_users($cn=""){
		$utId = "1";
		if($cn == "cn") $utId = implode(",",$GLOBALS['arrValidCNPhy']);
		$arr=array();
		$q = "SELECT lname,fname,mname,id FROM users WHERE user_type IN (".$utId.") AND delete_status = 0 ORDER BY lname,fname";
		$res = imw_query($q);
		while($rs = imw_fetch_assoc($res))
		{
			$pn = $rs['lname'].", ".$rs['fname']." ".$rs['mname'];
			$pn = (strlen($pn) > 30) ? substr($pn,0,28).".." : $pn;
			$id = $rs['id'];
			$arr[$id] = $pn;
		}
		return $arr;
	}
	
	/*******GET CHART FORM ID*** EITHER CURRENT OR LAST FINALIZED*******/
	function get_chart_form_id($patient_id){
		if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
			$form_id = $_SESSION["form_id"];
			$finalize_flag = 0;
		}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
			$form_id = $_SESSION["finalize_id"];
			$finalize_flag = 1;
		}
		if(isset($_GET["finalize_id"]) && !empty($_GET["finalize_id"])){
			$form_id = $_GET["finalize_id"];
			$finalize_flag = 1;
		}
	
		if((isset($_GET["pat_id"]) && !empty($_GET["pat_id"])) && (isset($_GET["testFormId"]) && !empty($_GET["testFormId"]))){
			$form_id = $_GET["testFormId"];
			$currFormId = $this->isChartOpened($patient_id);
			if( ($currFormId != false) && ($currFormId == $form_id)){
				$finalize_flag = 0;
			}else{
				$finalize_flag = 1;
			}	
		}
		//Now Tests can be independent of chart notes //so form id can be zero
		if(!isset($form_id) || empty($form_id)){$form_id = 0; $finalize_flag = 1;}
		if(isset($_GET['force_new_test'])) $form_id= 0;
		return array($form_id,$finalize_flag);
	}
	
	/*******CHECK IF ANY IMAGING ORDER DONE FOR SAME TEST**THEN MAKE IT TO SELECT order_by DROP-DOWN*********/
	function get_chart_order_info($test,$pid,$form_id){
		$opid=$opdt="";
		$q = "SELECT c1.logged_provider_id,DATE_FORMAT(c1.created_date,'".get_sql_date_format()."') AS opdt  
				FROM order_set_associate_chart_notes c1
				LEFT JOIN order_set_associate_chart_notes_details c2 ON (c2.order_set_associate_id=c1.order_set_associate_id) 
				LEFT JOIN order_details c3 ON (c3.id = c2.order_id) 
				WHERE c1.form_id = '".$form_id."' AND c1.patient_id='".$pid."'
				AND LOWER(c3.name) = '".strtolower($test)."'
				ORDER BY c1.created_date LIMIT 0,1 ";
		$res = imw_query($q);
		if($res && imw_num_rows($res) == 1){
			$rs = imw_fetch_assoc($res);
			if(!empty($rs["logged_provider_id"])){
				$opid = $rs["logged_provider_id"];
				$opdt = $rs["opdt"];
			}
		}
		return array($opid,$opdt);
	}
	
	/******TO CHECK IF CHART IS OPENED******/
	function isChartOpened($pid){
		$q = "SELECT id FROM chart_master_table 
				WHERE patient_id='".$pid."' AND finalize='0' AND delete_status='0'
				ORDER BY id DESC LIMIT 0,1 ";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs["id"];
		}
		return false;
	}
	
	function get_saved_test_data($tests_name_rs){
		$test_type 			= $tests_name_rs['test_type'];
		$test_table			= $tests_name_rs['test_table'];
		$id_column			= $tests_name_rs['test_table_pk_id'];
		$pt_column			= $tests_name_rs['patient_key'];
		$phy_column			= $tests_name_rs['phy_id_key'];
		$exam_column		= $tests_name_rs['exam_date_key'];
		$performed_column	= $tests_name_rs['performed_key'];
		$temp_name			= $tests_name_rs['temp_name'];
		$t_manager			= $tests_name_rs['t_manager'];
		
		$main_q		= "SELECT * FROM $test_table WHERE $id_column='".$id_column."' LIMIT 0,1";
		$res		= imw_query($main_q);
		if($res && imw_num_rows($res)==1){
			
		}		
	}
	
	function valuesNewRecordsTests($test_tbl_name, $patient_id, $sel=" * ",$dt=""){
		$this_table = $this->get_table_cols_by_test_table_name($tbl);
		$row = false;
		$dt= (empty($dt) || ($dt == "0000-00-00")) ? date("Y-m-d") : $dt;
		$q = "SELECT ".$sel." FROM ".$this_table['test_table']." ".
				"WHERE ".$this_table['patient_key']." = '".$patient_id."' ".
				"AND ".$this_table['exam_date_key']." < '".$dt."' ".
				"AND purged='0' AND del_status='0' ".
				"ORDER BY ".$this_table['exam_date_key']." DESC, ".$this_table['test_table_pk_id']." DESC ";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
		}
		return $row;
	}
	
	/*****************Getting patient status w.r.t room where chart note opened *******/
	function patient_whc_room($chartopnd=0,$dos=''){
		if($chartopnd==1 && $dos!=date('Y-m-d')){return;}
		$clientPCName = 'N/A';
		if($_COOKIE['clientPCName']!=""  && empty($_COOKIE['clientPCName']) == false){
			$clientPCName=$_COOKIE['clientPCName'];
		}
		if($this->patient_id != "" && $clientPCName != ""){
			$curDate = date('Y-m-d');
			$curTime = date('H:i:s');
			$loggedUserType = $this->logged_user_type;
			$room = 'N/A';
			$q="SELECT room_no FROM mac_room_desc WHERE mac_address = '".imw_real_escape_string($clientPCName)."' LIMIT 1";
			$ress=imw_query($q);
			if(imw_num_rows($ress)>0){
				$rs_room = imw_fetch_array($ress);
				$room = $rs_room['room_no'];
			}

			$ObjiMedicMonitor	= new imedicmonitor();
			
			$checedInApptDetail = $ObjiMedicMonitor->pt_checked_in_appts_today($this->patient_id);
			if(is_array($checedInApptDetail)){
			foreach($checedInApptDetail as $sch_id=>$sch_rs){
				$sch_id = $sch_id;
			}
			}
			$q2="SELECT * FROM patient_location WHERE patientId='".$this->patient_id."' AND cur_date =CURDATE() AND sch_id='".$sch_id."'";
			$res=imw_query($q2);
			if(imw_num_rows($res)<=0){
				$qr="INSERT INTO patient_location SET 
					patientId='".$this->patient_id."',
					app_room='".imw_real_escape_string($room)."',
					doctor_mess='', 
					chart_opened='yes',
					app_room_time='".$curTime."', doctor_id='".$_SESSION['authId']."', cur_date ='".$curDate."', cur_time ='".$curTime."', 
					sch_id='".$sch_id."', pt_with=0";
				imw_query($qr);
			}else{
				$existingRow = imw_fetch_assoc($res);
				$existingMsg = $existingRow['doctor_mess'];
				$existingSch_id = $existingRow['sch_id'];
				if($room == 'N/A') {$existingRoom = $existingRow['app_room'];} else{$existingRoom = $room;}
				if($existingMsg=='A/P Dilated' && ($loggedUserType=='3' || $loggedUserType=='11' || $loggedUserType=='19' || $loggedUserType=='1')){//TECH/phy GRABBED THE PATIENT AFTER DIALATION.
					$existingMsg = '';	
				}
				$qr="UPDATE patient_location SET patientId='".$this->patient_id."', app_room='".imw_real_escape_string($existingRoom)."', chart_opened='yes', doctor_id='".$_SESSION['authId']."', app_room_time='".$curTime."', cur_date ='".$curDate."',cur_time ='".$curTime."', sch_id='".$sch_id."', pt_with=0, doctor_mess='".imw_real_escape_string($existingMsg)."' WHERE patientId='".$this->patient_id."' AND sch_id = '".$existingSch_id."' AND cur_date ='".$curDate."'";
					imw_query($qr);
			}
		}
	}
	
	
	function getCnCom($fid){
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$opt=$dod=$dos="";

		$sql = "	SELECT
				c3.optic_nerve_od,c3.optic_nerve_os,
				c6.macula_od,c6.macula_os,
				c7.vitreous_od,c7.vitreous_os,
				c5.periphery_od,c5.periphery_os,
				c4.blood_vessels_od,c4.blood_vessels_os,
				c2.retinal_od,c2.retinal_os
				FROM chart_master_table c1
				LEFT JOIN chart_retinal_exam c2 ON c1.id = c2.form_id
				LEFT JOIN chart_vitreous c7 ON c1.id = c7.form_id
				LEFT JOIN chart_blood_vessels c4 ON c1.id = c4.form_id
				LEFT JOIN chart_periphery c5 ON c1.id = c5.form_id
				LEFT JOIN chart_macula c6 ON c1.id = c6.form_id
				LEFT JOIN chart_optic c3 ON c1.id = c3.form_id
				WHERE c1.id = '".$fid."'
				";
		$row = sqlQuery($sql);
		if($row != false){
			extract($row);
			$arr = array($optic_nerve_od,$optic_nerve_os,
						 $macula_od,$macula_os,
						 $vitreous_od,$vitreous_os,
						 $periphery_od,$periphery_os,
						 $blood_vessels_od,$blood_vessels_os,
						 $retinal_od,$retinal_os
						 );

			for($i=0;$i<12;$i++){
				if(!empty($arr[$i])){
					$doc->loadXML($arr[$i]);
					$od = $doc->getElementsByTagname("advanceoptions");
					$t = "".$od->item(0)->firstChild->nodeValue;

					if(!empty($t)){
						if($i%2==0){
							if(!empty($dod)) $dod .= ", ";
						}else{
							if(!empty($dos)) $dos .= ", ";
						}

						if($i==0 || $i==1){
							//Optic
							if($i==0){
								$dod .= $t;
							}else{
								$dos .= $t;
							}
							$opt = "1";

						}else if($i==2 || $i==3){
							//Macula
							if($i==2){
								$dod .= $t;
							}else{
								$dos .= $t;
							}
							$opt = "2";


						}else{
							//Sle
							if($i%2==0){
							$dod .= $t;
							}else{
							$dos .= $t;
							}
							$opt = "3";
						}
					}

				}

			}

		}

		if(!empty($dod))$dod .= "\n ";
		if(!empty($dos))$dos .= "\n ";
		return array($opt, $dod, $dos);
	}
	
	function getDiagOpts($val="1",$tests='common',$tmid=0){
		if(empty($val)){ $val=1; }
		if(!empty($tmid)){ //check in DB
			
			$flg_sub=0;
			$arr = array();
			$sql = "select * from test_diagnosis where test_id='".$tmid."' AND del_by='0' 
				Order by test_sub_type, diag_nm ";				
			$res = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($res);$i++){
				$sub_type = $row["test_sub_type"];
				if(!empty($sub_type)){
					$c=0;
					if($sub_type == "Disc Photos" || $sub_type == "ES (External)" || $sub_type == "Optic Nerve" || $sub_type == "Topography"){$c=1;}
					else if($sub_type == "Macula Photos" || $sub_type == "ASP (Anterior Segment Photos)" || $sub_type == "Retina" || $sub_type == "Treatment"){$c=2;}
					else if($sub_type == "Retina Photos" || $sub_type == "" || $sub_type == "Anterior Segment"){$c=3;}
					
					if(!empty($c)){
						$arr[$c-1][] = $row["diag_nm"];
					}
					$flg_sub=1;
				}else{
					$arr[] = $row["diag_nm"];	
				}
			}
			if(count($arr)>0){
				
				if(empty($flg_sub)){					
					if(!in_array("Other",$arr)){ $arr[] = "Other";  }					
					return $arr;
				}else{
					if($val=="JS"){
					
						if(count($arr)>0){
							foreach($arr as $k => $af){
								if(!in_array("Other",$af)){ $arr[$k][] = "Other";  }
							}
						}
					
						return json_encode($arr);
					}else{
						
						if(!in_array("Other",$arr[$val-1])){ $arr[$val-1][] = "Other";  }

						return $arr[$val-1];
					}
				}
			}	
		}
	
		//Default--
		
		$arr= array(array("COAG","COAG suspect","Chronic Angle Closure","Dye Eye Syndrome","Field Defect","Inflammatory","Macular degeneration",
						"NAG","Ocular Hypertension","Retinal Toxicity (Plaquenil)","Steroid Responders","Other"),
					array("Macula degeneration, dry","Macula degeneration, wet","Drusen",
						"RPE changes","Epiretinal membrane","Nevus","Macula hemorrhage","Macular degeneration","Other"),
					array("Hypertensive retinopathy","Diabetic retinopathy","Retinal hemorrhage",
						"CRVO/ BRVO","CRA/BRAO","Macular degeneration","Nevus","Drusen","Retinal/choroidal mass, undetermined",
						"Peripheral retinal degeneration","Other"));
		if($tests=='oct_rnfl'){
			
			$arr= array(array(
					"AngleClosureG",
					"Childhood OAG",
                    "Dye Eye Syndrome",
					"GL-susp, narrow angle",
					"GL-susp, open angle",
					"GL w/ other ocular dx",
					"ICE Syndrom",
					"Inflammatory G",
					"Low TG",
					"Normal TG",
					"NVG",
                    "Ocular Hypertension",
					"Other 2degrees GL",
					"Pigmentary G",
					"POAG",
					"PXFG/PXE",
					"Steroid G",
					"Other")
				);
				
		}else if($tests=='oct'){
			$arr= array(array("COAG","COAG suspect","Chronic Angle Closure",
						"Field Defect","Inflammatory","NAG",
						"Retinal Toxicity (Plaquenil)","Steroid Responders",
						"Macular degeneration",
						"Other"),
				array("Macula edema","Macula thickening","Epiretinal membrane",
						"Vitreo-macula traction syndrome","Macula hole","Macula hemorrhage","Drusen","Retinal Toxicity (Plaquenil)",
						"Sub-retinal neovascularization","Macular degeneration","Other"),
				array("COAG","COAG suspect","Chronic Angle Closure",
						"Field Defect","Inflammatory","NAG",
						"Retinal Toxicity (Plaquenil)","Steroid Responders",
						"Macular degeneration",
						"Other"));
		}else	if($tests=='bscan'){
			$arr = array('Advanced Cataract','Intraocular Mass','Vitreous Hemorrhage','Suspected retinal detachment','Optic nerve abnormality','Other');
			return $arr;
		}else if($tests=='cellcount'){
			$arr = array("SLE evidence of corneal guttata or fuch's dystrophy","Evidence of corneal disease, corneal dystrophy or trauma","Complicated Cataract Surgery","Presence of corneal swelling, edema or decomposition","Presence of intraocular lens implant or prior ocular surgery","Other");
			return $arr;
		}else if($tests=='pacchy'){
			$arr = array("COAG","COAG suspect","Chronic Angle Closure","Field Defect","NAG","Corneal edema","Corneal thickening","Laser vision correction exam","Other");
			return $arr;
		}else if($tests=='discexternal'){
			$arr = array("Pterygium","Eyelid Lesion","Corneal Lesion","Corneal Scar","Conjunctival Lesion","Nevus","Iris Abnormality","Ptosis", "Meibomian gland dysfunction","Ocular Hypertension", "Dye Eye Syndrome", "Other");
			return $arr;	
		}else if($tests=='gdx'){
			$arr = array("Glaucoma Suspect","Open Angle Glaucoma","Narrow Angle Glaucoma","Normal Tension Glaucoma","Other");
			return $arr;	
		}else if($tests=='icg'){
			$arr = array("Neovascularization of the disc","Neovascularization of the retina","Diabetic retinopathy","Choroidal nevus/tumor","Retinal tumor","Retinal macroaneurysm","Hemorrhage","Macular degeneration","Other");
			return $arr;	
		}else if($tests=='vfgl'){
			$arr = array("Angle Closure G","Childhood OAG","Dye Eye Syndrome","GL-susp, narrow angle","GL w/ other ocular dx","GL-susp, open angle","ICE Syndrom","Inflammatory G","Low TG","Normal TG","NVG","Ocular Hypertension","Other 2degrees GL","Pigmentary G","POAG","PXFG/PXE","Steroid G","Other");
			return $arr;	
		}else if($tests=='topo'){
			$arr = array("Normal Exam","Regular Astigmatism","Irregular Astigmatism","Keratoconus","Forme Fruste Keratoconus","Pellucid Degeneration","Post OP LASIK","Post OP PRK","Pterygium","Corneal Scarring","Other Corneal Disorder/Dystrophy","Other");
			return $arr;	
		}else if($tests=='topo_treat'){
			$arr = array("No treatment needed","Laser Vision Correction Candidate","Not a candidate for laser vision correction","Toric Lens implant","Limbal relaxing incision","Contact Lenses","Corneal Transplant","Other Corneal Surgery","Other");
			return $arr;	
		}
		
		
		if($val=="JS"){
			return json_encode($arr);
		}else{
			return $arr[$val-1];
		}
	}
	
	function getCdValues($patient_id, $form_id){
		$q = "SELECT od_text,os_text,cd_val_od,cd_val_os FROM chart_optic  WHERE form_id = '$form_id'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			$cdOd = trim($row["cd_val_od"]." ".$row["od_text"]);
			$cdOs = trim($row["cd_val_os"]." ".$row["os_text"]);
			return array($cdOd, $cdOs);
		}
		return false;		
	}
	
	//START FUNCTION TO GET OPERATOR-ID FROM SCHEDULE TABLE
	function whoOrderedOperatorIdFun($ptId,$frmID,$field1Name,$field1Value1,$field1Value2=''){	
		$andP = "";
		if(!empty($field1Value2) && count($field1Value2) > 0){
			foreach($field1Value2 as $key => $val){
				$andP .= (empty($andP)) ? " AND ( " : " OR ";
				$andP .= "$field1Name = '".$val."' ";
			}
			$andP .=")";
		}
		$q = "SELECT operators_info FROM `schedule`
				WHERE patient_id = '$ptId' AND form_id = '$frmID'
				AND $field1Name != ''
				$andP
				ORDER BY id DESC
				LIMIT 0,1";
		$res = imw_query($sql);
		if($res){
			while($rs = imw_fetch_assoc($res)){
				$arrTmp = unserialize($rs["operators_info"]);
				$arrTestInfo = $arrTmp["Testing"][$field1Value1];
				$opId = $arrTestInfo[0];
			}
		}
		return $opId;
	}
	
	//get type ahead
	function getTypeAheadStr($providerId)
	{
		$retStr = "";
		$sql = "SELECT distinct(phrase) FROM common_phrases ".
			   "WHERE providerID = '".$providerId."' || providerID = 0 ORDER BY providerID ";
		$res = imw_query($sql);
		while($rs = imw_fetch_assoc($res)){
			$retStr .= "'".trim($this->removeLineBreaks(str_replace(array("'","\n","\r"),array("\'","\\n","\\r"),trim($rs["phrase"]))))."',";
		}
        return substr($retStr,0,-1);
	}
	
	//Function Remove line breaks
	function removeLineBreaks($str){
		return preg_replace("(\r\n|\n|\r)", " ", $str);
	}
	
	//DROP DOWN FOR TEST PROFILES
	function DropDown_Interpretation_Profile($test_id){//$test_id from table  'tests_name';
		$proid=(!empty($_SESSION['res_fellow_sess']))?$_SESSION['res_fellow_sess']:$_SESSION['authId'];
		$return = $js = '';
		$res = imw_query("SELECT id,profile_name,profile_data,favorite FROM interpretation_profiles WHERE physician_id='".$proid."' AND test_id='".$test_id."' AND deleted=0 ORDER BY profile_name");
			$return = '<select name="sel_interpretation_profile" id="sel_interpretation_profile" onChange="fillInterpretationProfileData(this.value);" class="form-control minimal" style="width:100px !important;"><option value="">--SELECT--</option>';
		if($res && imw_num_rows($res)>0){
			$arr_interpretation_profiles = array();
			$js = '<script type="text/javascript">var arr_interpretation_pofiles = new Array();';
			while($rs = imw_fetch_assoc($res)){
				$selected = '';
				if($rs['favorite']==1 && (!isset($_GET["tId"]) || trim($_GET["tId"])=='')){$selected=' selected';}
				$rs['profile_data'] = str_replace(array("\r\n","\n","\r"),"<br>",$rs['profile_data']);
				$js .= 'arr_interpretation_pofiles["'.$rs['id'].'"]=\''.html_entity_decode(addslashes($rs['profile_data'])).'\';';
				$return .= '<option value="'.$rs['id'].'"'.$selected.'>'.$rs['profile_name'].'</option>';
			}
			/*$return .= '</select>'.$js.'</script>';*/
		}
		$return .= '</select>';
		if($js!=''){$return .= $js;if(!isset($_GET["tId"]) || trim($_GET["tId"])==''){$return.= 'var reset_form_if_no_inter_pro_selected=true;';} $return.= '</script>';}
		return $return;
	}
	
	/*code, Purpose: Function to get list of procedures for Zeiss Hl7 messages for test*/
	function zeissProcOpts($mapVAl){
		$options = array();
		if(is_int($mapVAl)){
			$opts = imw_query("SELECT `id`, `order_type` FROM `zeiss_forum_order_type` WHERE `map_code` RLIKE '[[:<:]]".$mapVAl."[[:>:]]' ORDER BY `order_type` ASC");
			if($opts){
				while($opt = imw_fetch_assoc($opts)){
					$options[$opt['id']] = $opt['order_type'];
				}
			}
		}
		return($options);
	}
	/*End code*/
	
	//---get first,middle,last name of given operator id
	function getPersonnal3($id){
		$nm = getUserFirstName($id,2);
		if(is_array($nm) && count($nm)>3) return $nm[3];
		return '';
	}
	
	function getFutureApp($pid)
	{
		$data="";
		$q = "SELECT DATE_FORMAT(schedule_appointments.sa_app_start_date,'".get_sql_date_format()."') as start_date,
				TIME_FORMAT(schedule_appointments.sa_app_starttime,'%h:%i %p') as start_time,
				 users . fname,users . lname , facility . name ,slot_procedures.acronym as procName
				FROM schedule_appointments
				LEFT JOIN users ON users.id = schedule_appointments.sa_doctor_id
				LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
				LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
				WHERE schedule_appointments.sa_patient_id = '$pid' and
				schedule_appointments.sa_patient_app_status_id NOT IN(201,18,19,20,203) and 
				CONCAT(schedule_appointments.sa_app_start_date ,' ',
				schedule_appointments.sa_app_starttime)
				> CONCAT(CURDATE(),' ',
				CURTIME())
				ORDER BY sa_app_start_date, sa_app_starttime
				";
		$res = imw_query($q);
		while($row = imw_fetch_assoc($res)){
			$name = explode(' ',$row['name']);
			$n = '';
			for($j = 0;$j<count($name);$j++){
				if(count($name) > 1) $n .= substr($name[$j],0,1);
				else $n .= substr($name[$j],0,2);
			}
			$data .= "<b>".$row['start_date']."</b>" .' '.
			$row['start_time'].'&nbsp;&nbsp;'.
			$row['fname'].' '.substr($row['lname'],0,1).'&nbsp;&nbsp;'.
			$row['procName'].'&nbsp;&nbsp;'.
			strtoupper($n).', <br>';
		}
		
		if($data == ''){
			$data = "No Future Appointments";
		}else{
			$data =  substr_replace($data,"",-6);
		}
		return $data;
	}
	
	function get_tests_VO_access_status(){
		// Check View Only Permission
		if(core_check_privilege(array("priv_vo_clinical")) == true){
			$elem_per_vo = 1;
		}else if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
			//Check User Type for Valid CN User ----------
			if(!in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNPhy']) && !in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNTech'])){
				$elem_per_vo = 1;
			}
		}
	}
	
	function get_zeiss_forum_button($test_name,$test_id,$patient_id){
		/*code, Purpose: Action buttons to HL7 message for zeiss*/
		$return = '';
		if(constant("ZEISS_FORUM") == "YES"){
			$buttons = false;
			$sql = "SELECT `msg_type` FROM `hl7_sent_forum` WHERE `test_id`='".$test_id."' AND `test_name`='".$test_name."' AND `patient_id`='".$patient_id."' ORDER BY `id` DESC LIMIT 1";
			$resp = imw_query($sql);
			if($resp){
				$buttons_rs = imw_fetch_assoc($resp);
				$buttons 	= $buttons_rs['msg_type'];
			}
			if(!$buttons || $buttons=="FORUM_DELETE"){
				$return = '<input type="button"  class="btn btn-info"  value="Forum Add" id="btnForumAdd" onClick="sendToZeiss(\''.$test_name.'\', 1)" />';
			}else if($buttons=="FORUM_ADD" || $buttons=="FORUM_UPDATE"){
				$return = '<input type="button" class="btn btn-warning"  value="Forum Delete" id="btnForumDelete" onClick="sendToZeiss(\''.$test_name.'\', 3)" />';
			} 
		}
		if($return != ''){$return = '<div class="text-center">'.$return.'</div><div claass="clearfix"></div>';}
		return $return;
	}
	
	function getHQfacility(){
		$res = imw_query("SELECT * FROM facility WHERE facility_type='1'");
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs;
			}
		}
		return $return;
	}
	
	function getEncounterId(){
		$HQfacRes = $this->getHQfacility();
		if($HQfacRes!==false){	
		$encounterId = $HQfacRes[0]["encounterId"];
		$facilityId = $HQfacRes[0]["id"];
		}
		
		$ArBilPolicies = $this->objAppBase->get_copay_policies();
		$encounterId_2 = $ArBilPolicies["Encounter_ID"];
		
		//bigg
		if($encounterId<$encounterId_2){
			$encounterId = $encounterId_2;
		}
		
		//--
		$counter=0; //check only 100 times
		do{
		
		$flgbreak=1;
		//check in superbill
		if($flgbreak==1){
			$sql = "select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}	
		}
		
		//check in chart_master_table--
		if($flgbreak==1){
			$sql = "select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}
		}
		
		//check in Accounting
		if($flgbreak==1){
			$sql = "select count(*) as num FROM patient_charge_list WHERE encounter_id='".$encounterId."'";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}
		}
		
		if($flgbreak==0) {$encounterId=$encounterId+1;}
		$counter++;
		}while($flgbreak==0 && $counter<100);
		if($counter>=100){ exit("Error: encounter Id counter needs to reset."); }
		//--
		
		$sql = "UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ";
		$row = sqlQuery($sql);
		
	
		$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ";
		$row = sqlQuery($sql);
		return $encounterId;
	}
	
	function getTestSumm($arrValues,$strExam){
		$strRet="";
		$len = count($arrValues);
		if($len > 0){
			foreach($arrValues as $key => $val){
				if(!empty($val)){
					$strRet .=$key." ";
				}
			}
			if(!empty($strRet)){
				$strRet = $strRet." ".$strExam.";";
			}
		}
		return $strRet;
	}
	
	//START FUNCTION TO GET SCAN/UPLOAD EXISTS and log interpretation FOR PARTICULAR TEST OR NOT....
	function interpret_if_scan_exists($patient_id, $testname, $testid) {
		//global $CLS_notify_iconbar;
		$scan_id_res = $this->is_scan_exists_for_test($patient_id,$testname, $testid);
		if($scan_id_res){
			while($scan_id_rs = imw_fetch_array($scan_id_res)){
				$scan_doc_id = $scan_id_rs['scan_id'];
				$provider_id = $_SESSION['authId'];
				$section_name = 'tests';
				$this->providerViewLogFun($scan_doc_id,$provider_id,$patient_id,$section_name);
			}
			/*if(in_array($testname,array("VF","NFA","OCT","GDX","Pacchy","Topogrphy","IVFA","Disc","discExternal"))){
				$CLS_notify_iconbar->set_notification_status('testseye');//updating testeye icon status
			}*/
		}
	}
	
	//START FUNCTION TO GET SCAN/UPLOAD EXISTS FOR PARTICULAR TEST OR NOT....
	function is_scan_exists_for_test($patient_id,$testname, $testid) {
		$chk_sql= "SELECT scan_id FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE image_form='".$testname."' AND test_id ='".$testid."' AND patient_id='".$patient_id."' AND status=0";
		$chk_res=imw_query($chk_sql);
		if($chk_res && imw_num_rows($chk_res)>0) {
			return $chk_res;
		}
		return false;
	}
	
	//START FUNCTION TO CREATE LOG OF PROVIDER FOR SCAN/TEST....
	function providerViewLogFun($scan_doc_id,$provider_id,$patient_id,$section_name) {
		if(isset($section_name) && $section_name!=''){$add_query = " AND section_name='".$section_name."'";}else{$add_query='';}
		$chk_sql= "SELECT id FROM provider_view_log_tbl where scan_doc_id='".$scan_doc_id."' AND provider_id='".$provider_id."' AND patient_id='".$patient_id."'".$add_query;
		$chk_res=imw_query($chk_sql);
		if(imw_num_rows($chk_res)<=0) {
			$insrtScnQry = "INSERT INTO provider_view_log_tbl SET 
							  scan_doc_id 	= '".$scan_doc_id."', 
							  patient_id 	= '".$patient_id."',
							  provider_id 	= '".$provider_id."', 
							  section_name 	= '".$section_name."',
							  date_time 	= '".date('Y-m-d H:i:s')."'";
			$insrtScnRes=imw_query($insrtScnQry);// or die(mysql_error());				  
		}
	}
	
	function del_test_scan_upload($scan_doc_id){
		$chk_sql= "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id='".$scan_doc_id."' LIMIT 0,1";
		$chk_res=imw_query($chk_sql);
		if($chk_res && imw_num_rows($chk_res)==1) {
			$rs = imw_fetch_assoc($chk_res);
			if(!empty($rs["file_path"])){				
				$doc_file_type	= $rs['file_type'];
				
				/****REMOVE THUMB AND THUMNAIL ALSO***/
				$disc_full_path		= $this->objSaveFile->getFilePath($rs["file_path"],'i');
				$path_info 			= pathinfo($disc_full_path);
				$path_basename 		= $path_info['basename'];
				$path_dir	  		= $path_info['dirname'];
				$path_name	  		= $path_info['filename'];
				$path_extension		= $path_info['extension'];
				$path_thumbnail_dest= $path_dir."/thumbnail";
				$path_thumb_dest	= $path_dir."/thumb";
				
				/***IF ORIGINAL IMAGE IS pdf, THEN DELETE jpg BIG IMAGE ALSO**/
				if(strtolower($doc_file_type) == "application/pdf" || strtolower($path_extension) == 'pdf'){
					$pdf_jpg_dest			= $path_dir."/".$path_name.".jpg";//BIG IMAGE USED IN ZOOM IN TEST IMAGE MANAGER.
					if(file_exists($pdf_jpg_dest)){
						unlink($pdf_jpg_dest);
					}
				}
				
				if(is_dir($path_thumb_dest) != false){
					$pdf_jthumb_dest		= $path_thumb_dest."/".$path_name.".jpg";
					if(file_exists($pdf_jthumb_dest)){
						unlink($pdf_jthumb_dest);
					}
				}
				if(is_dir($path_thumbnail_dest) != false){
					$pdf_jthumbnail_dest	= $path_thumbnail_dest."/".$path_name.".jpg";
					if(file_exists($pdf_jthumbnail_dest)){
						unlink($pdf_jthumbnail_dest);
					}
				}				
				$this->objSaveFile->unlinkfile($rs["file_path"]);
								
				$sql = "DELETE FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in ($scan_doc_id)";
				$res=imw_query($sql);
				//DELETE SCAN LOG FOR PROVIDER
				$deleteQ = "DELETE FROM provider_view_log_tbl WHERE scan_doc_id IN($scan_doc_id) AND section_name='tests'";
				$deletePVRes = imw_query($deleteQ);				
			}	
		}
	}
	
	function saveTestPdfExe_2($pid,$tid,$tnm){
		//Test
		$z_pt_id = $pid;
		$z_test_id = $tid;
		$z_test_name = $tnm;
		$z_save_pth = $GLOBALS['webroot']."/data/tmp/pttest_".$z_pt_id."_".$z_test_id."_".$z_test_name."_tests.pdf";
		//Debugging
		//include($GLOBALS['incdir']."/chart_notes/pdf/tests/index.php");
		//Debugging
		/*
		//Test Merge All Tests
		$oTPdf = new TestPdf($pid,$tid,$tnm);
		$oTPdf->savePdfAll();
		*/
	}
	
	function get_template_test_id($tId,$table=''){
		//$_GET["tId"]
		$q = "SELECT id FROM `tests_name` 
				JOIN test_other ON (test_other.test_template_id=tests_name.id) 
				WHERE test_other.test_other_id='".$tId."' 
					AND tests_name.status=1 
					AND tests_name.test_type='1' LIMIT 1";
		if($table=='custom'){
			$q = "SELECT id FROM `tests_name` 
				JOIN test_custom_patient ON (test_custom_patient.test_template_id=tests_name.id) 
				WHERE test_custom_patient.test_id='".$tId."' 
				AND tests_name.status=1 
				AND tests_name.test_type='1' LIMIT 1";	
		}
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['id'];
		}
		return false;
			
			
	}
	
	function saveCorrectionValues($arr){
		//Check
		$sql = "SELECT cor_id FROM chart_correction_values WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
		$row = imw_query($sql);
		if($row != false){
			//Update
			$sql = "UPDATE chart_correction_values SET ".
				 "reading_od = '".$arr["elem_od_readings"]."', ".
				 "avg_od = '".$arr["elem_od_average"]."', ".
				 "cor_val_od = '".$arr["elem_od_correction_value"]."', ".
				 "reading_os = '".$arr["elem_os_readings"]."', ".
				 "avg_os='".$arr["elem_os_average"]."', ".
				 "cor_val_os = '".$arr["elem_os_correction_value"]."', ".
				 "cor_date = '".getDateFormatDB($arr["elem_cor_date"])."', ".
				 "uid='".$_SESSION["authId"]."' ".
				 "WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
			$row = imw_query($sql);
		}else{
			if(!empty($arr["elem_od_readings"]) || !empty($arr["elem_od_correction_value"]) ||
				!empty($arr["elem_os_readings"]) || !empty($arr["elem_os_correction_value"]) ){
				//Insert
				$sql= "INSERT INTO chart_correction_values ".
					"(cor_id, patient_id, form_id, cor_date, reading_od, avg_od, cor_val_od, reading_os, avg_os, cor_val_os,uid) ".
					"VALUES ".
					"(NULL, '".$arr["patientid"]."', '".$arr["elem_formId"]."', '".getDateFormatDB($arr["elem_cor_date"])."', '".$arr["elem_od_readings"]."', '".$arr["elem_od_average"]."', ".
					"'".$arr["elem_od_correction_value"]."', '".$arr["elem_os_readings"]."', '".$arr["elem_os_average"]."', '".$arr["elem_os_correction_value"]."','".$_SESSION["authId"]."' ) ";
				$row = imw_query($sql);
			}
		}
	}
	
	function getIopTrgtVals($patientId){
		$targetTpOd = $targetTpOs = $targetTaOd = $targetTaOs = "";
		$sql = "SELECT
			  chart_master_table.id,
			  chart_iop.iop_id,
			  chart_iop.puff_trgt_od,
			  chart_iop.puff_trgt_os,
			  chart_iop.app_trgt_od,
			  chart_iop.app_trgt_os,
			  chart_iop.trgtOd,
			  chart_iop.trgtOs,
			  chart_iop.exam_date
			  FROM chart_master_table
			  INNER JOIN chart_iop ON chart_iop.form_id = chart_master_table.id  AND chart_iop.purged = '0'
			  WHERE chart_master_table.patient_id='".$patientId."'
			  AND ((chart_iop.puff_trgt_od != '') OR (chart_iop.puff_trgt_os != '') OR
				  (chart_iop.app_trgt_od != '') OR (chart_iop.app_trgt_os != '') OR
				  (chart_iop.trgtOd != '') OR (chart_iop.trgtOs != '') )
			  ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC
			  LIMIT 0,1
			";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$targetTpOd = !empty($row["puff_trgt_od"]) ? $row["puff_trgt_od"] :$targetTpOd ;
			$targetTpOs = !empty($row["puff_trgt_os"]) ? $row["puff_trgt_os"] : $targetTpOs;
			$targetTaOd = !empty($row["app_trgt_od"]) ? $row["app_trgt_od"] : $targetTaOd;
			$targetTaOs = !empty($row["app_trgt_os"]) ? $row["app_trgt_os"] : $targetTaOs;
			$trgtOd = !empty($row["trgtOd"]) ? $row["trgtOd"] : $targetTaOd;
			$trgtOs = !empty($row["trgtOs"]) ? $row["trgtOs"] : $targetTaOs;
		}
	
		//return array($targetTaOd,$targetTaOs,$targetTpOd,$targetTpOs);
		return array($trgtOd,$trgtOs);
	}
	
	function getGlucomaTargetIop($patient_id){
		$tOd=$tOs="";
		$sql = "SELECT iopTrgtOd,iopTrgtOs FROM glucoma_main WHERE patientId='".$patient_id."' ORDER BY glucomaId DESC ";
		$row = sqlQuery($sql);
		if($row != false){
			$tOd = $row["iopTrgtOd"];
			$tOs = $row["iopTrgtOs"];
		}
		return array($tOd,$tOs);
	}
	
	function getIopTrgtDef($pId,$formId=0,$strict=0){
		$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='".$formId."' ";
		$row = sqlQuery($sql);
		if( ($row == false) && ($formId != 0) && ($strict == 0) ){
			$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='0' ";
			$row = sqlQuery($sql);
		}
		return $row;
	}
	
	function saveIopTrgt($trgtOd,$trgtOs,$pId,$formId=0){
		if( !empty($pId) ){
			$row = $this->getIopTrgtDef($pId,$formId,1);
			if($row != false){
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);
			}else{
				if(!empty($trgtOd) || !empty($trgtOs)){
					$sql = "INSERT INTO tbl_def_val(tbl_def_val_id, iopTrgtOd, iopTrgtOs, ptId, form_id)  ".
						 "VALUES(NULL, '".$trgtOd."', '".$trgtOs."', '".$pId."', '".$formId."' ) ";
					$res = sqlQuery($sql);
				}	 
			}
			
			//Update zero
			if(!empty($formId)){
				saveIopTrgt($trgtOd,$trgtOs,$pId,0);
			}
		}
	}
	
	function setGlucomaStageGFS($stage_od , $stage_os){
		$a=""; // && $stage_od != "Unspecified"
		if(!empty($stage_od)){ $a = " staging_code_od='".$stage_od."' ";  }
		if(!empty($stage_os)){ if(!empty($a)){ $a.=","; }  $a .= " staging_code_os='".$stage_os."' ";  }	
		
		if(!empty($a)){
		$sql = "UPDATE  glucoma_main 
				SET ".$a."
			  WHERE patientId = '".$_SESSION["patient"]."' 
			  AND activate = '1' ";	
		$row=sqlQuery($sql);	  
		}
	}
	
	function remIopTrgtDefVal($trgtOd,$trgtOs,$pId,$formId=0){
		$trgtOd = trim($trgtOd);
		$trgtOs = trim($trgtOs);
		if(!empty($pId) && empty($trgtOd) && empty($trgtOs)){
			$flgEmp=1;
			if(!empty($formId)){
				//check in IOP
				$cQry = "select trgtOd, trgtOs FROM chart_iop 
							WHERE form_id='".$formId."' AND patient_id='".$pId."' AND purged='0' ";
				$row = sqlQuery($cQry);
				if($row != false){
					if(trim($row["trgtOd"])!="" || trim($row["trgtOs"])!=""){
						//do not empty def values;
						$flgEmp=0;
					}
				}
			}
			//VF
			$sql="SELECT iopTrgtOd, iopTrgtOs FROM vf WHERE formId = '".$formId."' AND patientId='".$pId."'";
			$row = sqlQuery($sql);
			if($row != false){
				if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
					//do not empty def values;
					$flgEmp=0;
				}
			}
			
			//NFA
			$sql="SELECT iopTrgtOd, iopTrgtOs FROM nfa WHERE form_id = '".$formId."' AND patient_id='".$pId."'";
			$row = sqlQuery($sql);
			if($row != false){
				if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
					//do not empty def values;
					$flgEmp=0;
				}
			}
	
			if($flgEmp==1){
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);
				
				//Update zero
				if(!empty($formId)){
					$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='0' ";
					$res = sqlQuery($sql);
				}
			}
		}
	}
	
	//--------------	FUNCTION TO GET K HEADING NAMES ID	---------------//
	function getKheadingId($str){
		$newStr = $str;
		$str = trim($str);
		if(strpos($str, "K[")!==false){$str = str_replace('K[',"",$str);}
		if(strpos($str, "]")!==false){$str = str_replace(']',"",$str);}
		$q1 = "SELECT * FROM kheadingnames WHERE kheadingName = '".imw_real_escape_string($str)."' ORDER BY kheadingId ";
		$res1 = imw_query($q1);
		if(imw_num_rows($res1)>0){
			$getKreadingNameRows = imw_fetch_assoc($res1);
			$kReadingHeadingID = $getKreadingNameRows['kheadingId'];
		}else{
			if(!empty($str)){
			$q2 = "INSERT INTO kheadingnames SET kheadingName = '".imw_real_escape_string($str)."' ";
			$res2 = imw_query($q2);
			$kReadingHeadingID = imw_insert_id();
			}
		}
		return $kReadingHeadingID;
	}
	
	//--------------	FUNCTION TO GET LENSES FORMULA HEADING	---------------//
	function getFormulaHeadingId($str){
		$q1 = "SELECT * FROM formulaheadings WHERE formula_heading_name = '$str'";
		$getFormulaheadingsIdQry = imw_query($q1);
		$countFormulaRows = imw_num_rows($getFormulaheadingsIdQry);
		if($countFormulaRows>0){
			$getFormulaheadingsIdRows = imw_fetch_array($getFormulaheadingsIdQry);
			$formulaHeadingId = $getFormulaheadingsIdRows['formula_id'];
		}else{
			$q2 = "INSERT INTO  formulaheadings SET formula_heading_name = '$str'";
			$insertFormulaheadingsQry = imw_query($q2);
			$formulaHeadingId = imw_insert_id();
		}
		return $formulaHeadingId;
	}
	
	//================= FUNCTION TO GET LENSE ID
	function getLenseId($lenseType){
		$q1 = "SELECT * FROM lenses_iol_type WHERE lenses_iol_type = '$lenseType'";
		$res1 = imw_query($q1);
		$rs = imw_fetch_array($res1);
		$iol_type_id = $rs['iol_type_id'];
		return $iol_type_id;
	}
	
	//================= FUNCTION TO GET LENSE TYPE
	function getLenseName($lenseID){
		$q = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
		$getLenseTypeQry = imw_query($q);
		$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
		$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
		return $lenses_iol_type;
	}
	
	function mk_print_folder($test_name,$date_f,$root_folder){
		if(!is_dir($root_folder.$test_name)){
			$oct_dir=mkdir($root_folder.$test_name,0777,true);
		}
		if(!is_dir($root_folder.$test_name.'/'.$date_f)){
			$oct_c_dir=mkdir($root_folder.$test_name.'/'.$date_f,0777,true);	
		}
		if(is_dir($root_folder.$test_name)){
			foreach(glob($root_folder.$test_name.'/*') as $octhtmlfilename) {
				$curDate=explode("/",$octhtmlfilename);
				$foldername=end($curDate);
				if($foldername==$date_f){continue;}
				foreach(glob($root_folder.$test_name.'/'.$foldername.'/*.html') as $octhtmlfilehtmlname) {
					@unlink($octhtmlfilehtmlname);
				}
				@rmdir($root_folder.$test_name.'/'.$foldername);
			}
			
		}
	}
	
	/*******GET UN-INTERPRETED TESTS LIST*************/
	function un_interpreted_tests($callFrom=''){
		$return = false;
		$ActiveTests = $this->get_active_tests();
		
		foreach($ActiveTests as $thisTest){
			$where_part = "";
			if($thisTest['test_table']=='test_other' || $thisTest['test_table']=='test_custom_patient'){
				$where_part = " AND test_template_id = '0'";
				if($thisTest['test_type']=='1'){$where_part = " AND test_template_id = '".$thisTest['id']."'";}
			}
			
			if($callFrom=='iconbar'){
				$where_part .= " AND ".$thisTest['patient_key']." = '".$this->patient_id."' ";
			}
			
			$thisTest['show_test_table']	= $thisTest['test_table'];
			
			$thisTest['comment_col'] 	 	= 'comments';
			if($thisTest['test_table']=='ivfa') $thisTest['comment_col'] = 'ivfaComments';
			else if(in_array($thisTest['test_table'],array('disc','disc_external'))) $thisTest['comment_col'] = 'discComments';
			else if(in_array($thisTest['test_table'],array('test_bscan','test_labs','test_other','test_cellcnt','test_custom_patient'))) $thisTest['comment_col'] = 'techComments';
			else if($thisTest['test_table']=='icg') $thisTest['comment_col'] = 'comments_icg';
			else if(in_array($thisTest['test_table'],array('surgical_tbl','iol_master_tbl'))) $thisTest['comment_col'] = '""';
			
			$thisTest['orderby_col']		= 'ordrby';
			
			$yr1backtime = strtotime("-1 year", time());
			$yr1backdate = date("Y-m-d", $yr1backtime);
			$q = "SELECT '".$thisTest['test_table']."' AS testName, 
				 '".$thisTest['temp_name']."' AS testDesc, 
				  ".$thisTest['test_table_pk_id']." AS main_id, 
				  ".$thisTest['phy_id_key']." AS phyName, 
				  DATE_FORMAT(".$thisTest['exam_date_key'].", '".get_sql_date_format()."') AS taskDate, 
				  ".$thisTest['comment_col']." AS comments,
				  ".$thisTest['orderby_col']." AS ordrby, 
				  ".$thisTest['patient_key']." AS patient_id, 
				  ".$thisTest['test_type']." AS test_type 
				  FROM ".$thisTest['test_table']." 
				  WHERE del_status='0' AND purged = '0' AND finished = '0' 
				  	AND (".$thisTest['phy_id_key']."='' || ".$thisTest['phy_id_key']."='0') 
					AND (".$thisTest['orderby_col']."='".$this->logged_user."' || ".$thisTest['orderby_col']."=0) 
					AND (".$thisTest['exam_date_key'].">'".$yr1backdate."') ".
					$where_part." ORDER BY ".$thisTest['exam_date_key']." DESC";
			$res = imw_query($q);
			//if(imw_error()!='') echo imw_error().'<br>'.$q.'<hr>';
			//continue;
			
			if($res && imw_num_rows($res)>0){//echo $q.'<br>'.$thisTest['id'].'<hr>';
				if(!$return) $return = array();
				while($rs = imw_fetch_assoc($res)){
					/*****IF ORDER BY IS BLANK, THEN CHECK IF LOGGED IN USER IS PRIMARY PHY FOR PATIENT, IF NOT SKIP THIS RECORD**/
					$pt_id = $rs['patient_id'];
					$pt_res = imw_query("SELECT id,providerID FROM patient_data WHERE id = '".$pt_id."' LIMIT  0,1");
					if($pt_res && imw_num_rows($pt_res)==1){
						$pt_rs = imw_fetch_assoc($pt_res);
						$pt_pro_id = $pt_rs['providerID'];
						if(($rs['ordrby']=='' || $rs['ordrby']=='0') && $pt_pro_id != $this->logged_user) {
						//	echo $pt_pro_id.' != '.$this->logged_user.'<br>';
							continue;
						}
						unset($pt_id); imw_free_result($pt_res); unset($pt_rs); unset($pt_pro_id);
					}else{
						continue;
					}
					
					/*****RECORD SKIP LOGIC END******/
					
					$test_case_key = $thisTest['test_table'];
					if($test_case_key=='test_other') $test_case_key .= $thisTest['test_type'];
					if($thisTest['test_table']=='test_other' && $thisTest['test_type']=='0'){
						$rs['show_name']	= isset($rs['test_other']) ? $rs['test_other'] : '';
					}
					$rs['test_js_key'] = $test_case_key;
					$return[] = $rs;
				}
			}
			
		}
		return $return;		
	}
	
} //END CLASS
?>