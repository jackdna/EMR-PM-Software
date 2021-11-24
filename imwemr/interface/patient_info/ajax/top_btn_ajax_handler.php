<?php
include_once('../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/merge_patients_class.php");
include_once($GLOBALS['srcdir']."/classes/complete_pt_record.class.php");
include_once($GLOBALS['srcdir']."/classes/AES.class.php");

//Merge patients object
$merge_obj = new Merge_patient();

//Complete Pt. Record object
$cpr = new CPR($_SESSION['patient']);


//Merge patient request

	//Returns patient search results
	if(isset($_REQUEST['fld']) && isset($_REQUEST['val'])){
		$patient_data = $merge_obj->get_patient_details($_REQUEST);
		$data = '<table class="table table-bordered table-striped">
					<tr class="grythead">
						<th align="center">Name</th>
						<th align="center">ID</th>		
						<th align="center">Address</th>
						<th align="center">Phone</th>
					</tr>';
		foreach($patient_data as $obj){
			$data .= '<tr>
				<td><a href="#" onclick="getName(\''.$obj['name2'].'\','.$obj['id'].');">'.$obj['id'].'</a></td>
				<td><a href="#" onclick="getName(\''.$obj['name2'].'\','.$obj['id'].');">'.ucwords(trim($obj['name'])).'</a></td>
				<td><a href="#" onclick="getName(\''.$obj['name2'].'\','.$obj['id'].');">'.$obj['address'].'</a></td>
				<td><a href="#" onclick="getName(\''.$obj['name2'].'\','.$obj['id'].');">'.$obj['phone_home'].'</a></td>
			</tr>';
		}
		$data .= '</table>';
		if(count($patient_data) > 0){
			echo $data;
		}else{
			echo $data = '';
		}
		exit();
	}
	
	//Returns Second patient full data
	if(isset($_REQUEST['get_pat_name']) && isset($_REQUEST['id'])){
		$merge_obj->extract_arr = '';	
		$merge_obj->__construct($_REQUEST['id']);
		echo json_encode($merge_obj->extract_arr);
		exit();
	}
	
	
	//merge the requested patients
	if(isset($_REQUEST['merge_patients'])){
		$save_arr = $merge_obj->merge_requested_patients($_REQUEST);
		echo json_encode($save_arr);
		exit();
	}
	
//Complete Pt. records requests
	//Generating XML and hash files
	if(isset($_REQUEST['electronicDOSCCD'])){
		//Setting Headers 
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
		header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
		header("Cache-control: private, no-cache"); 
		header("Pragma: no-cache");
		
		//Creating XML Files
		$return_val = $cpr->get_ccd_xml($_REQUEST);
		echo $return_val;
		exit();
	}
	
	if(isset($_REQUEST['ccdDocumentOptionsXml']) && $_REQUEST['ccdDocumentOptionsXml'] == 'yes'){
		$xml_data = $cpr->get_wv_xl_data($_REQUEST);
		echo $xml_data;
	}
	
	//Downloading Plain txt and hash  files
	if(isset($_REQUEST['download_ccd']) && trim($_REQUEST['download_ccd']) !== ''){
		$file_details = $cpr->download_ccd($_REQUEST);
		$content_type = "text/xml";
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'zip'){
			header('Content-Type: application/zip');
		}else{
			header('Content-Type: application/octet-stream; charset=utf-8');
		}
		header("Cache-Control: private",false);
		header("Content-Description: File Transfer");
		header("Content-disposition:attachment; filename=\"".$file_details['download_name']."\"");

		header("Content-Length: ".@filesize($file_details['filename']));
		@readfile($file_details['filename']) or die("File not found.");
		exit;
	}

?>