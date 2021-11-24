<?php


require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//to get save location
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
$current_patient = $_SESSION['patient'];
$current_provider = $_SESSION['authId'];
$sx_planning_sheet_id = $_GET['sx_planning_sheet_id'];

$upload_dir = data_path();	
$task = trim($_GET['task']);
switch($task){
	case 'del_unassigned_image':
		$path 				= $_GET['path'];
		if(is_file($path) && file_exists($path)) {unlink($path);echo 'Image Deleted.';}
		else{'File not found.';}
		break;
	case 'log_toric_open':
		$chk_q = "SELECT id FROM toric_open_log WHERE 
						provider_id='".$current_provider."' AND 
						patient_id='".$current_patient."' AND 
						DATE_FORMAT(dt_time,'%Y-%m-%d')='".date('Y-m-d')."' AND 
						request_status='open' 
						ORDER BY id DESC";
		$chk_res = imw_query($chk_q);
		$num_open_request = imw_num_rows($chk_res);
		if($chk_res && $num_open_request>=1){
			$chk_rs = imw_fetch_assoc($chk_res);
			$log_id = $chk_rs['id'];
			$query = "UPDATE toric_open_log SET dt_time = '".date('Y-m-d H:i:s')."' WHERE id = '$log_id'";
			if(imw_query($query)){echo 'SAVED_'.$log_id; $_SESSION['toric_loadtm'] = date('Y-m-d H:i:s');}//else{echo imw_error();}
			if($num_open_request>1){
				while($chk_rs2 = imw_fetch_assoc($chk_res)){
					$log_id2 = $chk_rs2['id'];
					$query2 = "UPDATE toric_open_log SET request_status='closed' WHERE id = '$log_id2'";
					imw_query($query2);
				}
			}
		}else if($chk_res && $num_open_request==0){
			$ins_q = "INSERT INTO toric_open_log 
					SET provider_id='".$current_provider."', patient_id='".$current_patient."', dt_time='".date('Y-m-d H:i:s')."', request_status='open'";
			if(imw_query($ins_q)){$log_id = imw_insert_id(); echo 'SAVED_'.$log_id; $_SESSION['toric_loadtm'] = date('Y-m-d H:i:s');}//else{echo imw_error();}	
		}
		break;
	
	case 'AssignImage2Pt':
		$img 				= $_GET['path'];
		$opened_request_id	= $_GET['open_log_id'];
		if($opened_request_id=='0'){die('Error');}
		if(is_file($path) && file_exists($path)){
			$oSaveFile = new SaveFile($current_patient);
			$pt_dir			= $upload_dir.'PatientId_'.$current_patient;
			$dest_dir		= $pt_dir.'/screenshots';
			$dest_thumb_dir	= $pt_dir.'/screenshots/thumb';
			
			//IF ABOVE DIRECTORIES NOT FOUND, THEN CREATE IT FIRST.
			if(!is_dir($pt_dir))		{mkdir($pt_dir);}
			if(!is_dir($dest_dir))		{mkdir($dest_dir);}
			if(!is_dir($dest_thumb_dir)){mkdir($dest_thumb_dir);}
			
			$s_img 			= dirname(__FILE__).'/screenshots'.trim(strrchr($img,'/'));
			$d_img 			= $dest_dir.trim(strrchr($img,'/'));
			$d_img_thumb 	= $dest_thumb_dir.trim(strrchr($img,'/'));
			$db_image_val	= substr(trim(strrchr($img,'/')),1); //FILE NAME TO SAVE IN TABLE.
			
			$sx_img_thumb	= data_path(1)."PatientId_".$current_patient."/screenshots/thumb".trim(strrchr($img,'/'));
			
			$cp = copy($s_img,$d_img);
			if($cp){
				unlink($s_img); //DELETING SOURCE IMAGE; AFTER SUCCESSFULL COPY DONE.
				
				//Add entry in patient toric images table
				$ins_q = "INSERT INTO toric_pt_images SET 
						chart_sx_plan_sheet_id = '".$sx_planning_sheet_id."', 
						patient_id='".$current_patient."', 
						provider_id='".$current_provider."', 
						dt_time='".date('Y-m-d H:i:s')."', 
						toric_open_log_id = '".$opened_request_id."', 
						img_path = '".$db_image_val."'
						";
				$ins_res = imw_query($ins_q);
				
				//IF IMAGE ATTACHED TO PATIENT; CLOSE THE TORIC OPENED REQUEST.
				if($ins_res){
					imw_query("UPDATE toric_open_log SET request_status='closed' WHERE id='$opened_request_id'");
					
					//RESETTING SESSION.
					$_SESSION['toric_loadtm'] = NULL;
					unset($_SESSION['toric_loadtm']);
					
					//CREATE THUMBNAIL......
					$oSaveFile->createThumbs($d_img,$d_img_thumb,200,200);
					if(file_exists($d_img_thumb)){
						$thumb_images[] = $d_img_thumb;
					}
					
					echo 'assigned~~'.$sx_img_thumb;
				}
			}
			
		}
		else{'File not found.';}
		break;
		
	case 'show_recent_unlinked_img':
		$toric_open_time = $_SESSION['toric_loadtm'];//trim($_GET['toric_open_time']);
		$recent_images = array();
		
		//GETTIGN UNASSIGNED IMAGES.
		$filesjpg = glob("screenshots/*jpg");
		$filespng = glob("screenshots/*png");
		$img_files = array();
		foreach($filesjpg as $jpg){$img_files[] = $jpg;}
		foreach($filespng as $png){$img_files[] = $png;}
		
		//SORTING FILE LIST DESCENDING ACCORDING TO DATE MODIFIED
		usort($img_files, create_function('$a,$b', 'return filemtime($b)>filemtime($a);'));
		
		//FILTERING ONLY THOSE IMAGES WHICH AS SAVED AFTER TORIC OPENED
		foreach($img_files as $img){
			$imgMtime = filemtime($img);
			if($imgMtime > strtotime($toric_open_time)){
				$recent_images[] = $img;
			}else{
				break;
			}
		}
		
		//IF IMAGES FOUND, SAVED AFTER TORIC OPEN TIME
		$matched_images = count($recent_images);
		$thumb_images = array();
		$new_toric_image_id = 0;
		if($matched_images>0) {			
			//IF ONLY ONE IMAGE FOUND; AND ONLY ONE REQUEST OPENED FOUND.
			$opened_logs = get_open_requests();
			if(is_array($opened_logs) && count($opened_logs)==1 && $matched_images==1){
				$opened_request_id 	= $opened_logs[0]['id'];
				$request_pt_id		= $opened_logs[0]['patient_id'];
				$request_prov_id	= $opened_logs[0]['provider_id']; 
				
				//CHECK OPENED REQUEST; THEN VERIFY IS THIS FOR CURRENT PATIENT.
				if($request_pt_id==$current_patient && $request_prov_id==$current_provider){
					$oSaveFile = new SaveFile($current_patient);
					
					foreach($recent_images as $img){
						$pt_dir			= $upload_dir.'PatientId_'.$current_patient;
						$dest_dir		= $pt_dir.'/screenshots';
						$dest_thumb_dir	= $pt_dir.'/screenshots/thumb';
						
						//IF ABOVE DIRECTORIES NOT FOUND, THEN CREATE IT FIRST.
						if(!is_dir($pt_dir))		{mkdir($pt_dir);}
						if(!is_dir($dest_dir))		{mkdir($dest_dir);}
						if(!is_dir($dest_thumb_dir)){mkdir($dest_thumb_dir);}
						
						$s_img 			= dirname(__FILE__).'/screenshots'.trim(strrchr($img,'/'));
						$d_img 			= $dest_dir.trim(strrchr($img,'/'));
						$d_img_thumb 	= $dest_thumb_dir.trim(strrchr($img,'/'));
						$sx_img_thumb	= data_path(1)."PatientId_".$current_patient."/screenshots/thumb".trim(strrchr($img,'/'));
						
						$db_image_val	= substr(trim(strrchr($img,'/')),1); //FILE NAME TO SAVE IN TABLE.
						$cp = copy($s_img,$d_img);
						if($cp){
							unlink($s_img); //DELETING SOURCE IMAGE; AFTER SUCCESSFULL COPY DONE.
							
							//Add entry in patient toric images table
							$ins_q = "INSERT INTO toric_pt_images SET 
									chart_sx_plan_sheet_id = '".$sx_planning_sheet_id."', 
									patient_id='".$current_patient."', 
									provider_id='".$current_provider."', 
									dt_time='".date('Y-m-d H:i:s')."', 
									toric_open_log_id = '".$opened_request_id."', 
									img_path = '".$db_image_val."'
									";
							$ins_res = imw_query($ins_q);
							$new_toric_image_id = imw_insert_id();
							//IF IMAGE ATTACHED TO PATIENT; CLOSE THE TORIC OPENED REQUEST.
							if($ins_res){
								imw_query("UPDATE toric_open_log SET request_status='closed' WHERE id='$opened_request_id'");
								
								//RESETTING SESSION.
								$_SESSION['toric_loadtm'] = NULL;
								unset($_SESSION['toric_loadtm']);
								
								//CREATE THUMBNAIL.
								$oSaveFile->createThumbs($d_img,$d_img_thumb,200,200);
								if(file_exists($d_img_thumb)){
									$thumb_images[] = $d_img_thumb;
								}
							}
						}
					}
					if(count($thumb_images)==1 && $new_toric_image_id>0){
						echo 'IMAGE_ALLOCATED~~'.$new_toric_image_id.'~~'.$sx_img_thumb;//trim(strrchr($d_img_thumb,'/interface/'));
						die;
					}
				}
			
			}
			echo json_encode($recent_images);
		}else {
			echo 'EMPTY';
		}
		break;
}


function get_open_requests(){
	$r = imw_query("SELECT id,patient_id,provider_id FROM toric_open_log WHERE DATE_FORMAT(dt_time,'%Y-%m-%d')='".date('Y-m-d')."' AND request_status='open'");
	if($r && imw_num_rows($r)>0){
		$return = array();
		while($rs = imw_fetch_assoc($r)){
			$return[] = $rs;
		}
		return $return;
	}else{
		return false;
	}
}
?>