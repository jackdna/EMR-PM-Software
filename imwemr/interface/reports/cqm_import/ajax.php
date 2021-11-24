<?php 
	include_once(dirname(__FILE__)."/../../../config/globals.php");
	$library_path = $GLOBALS['webroot'].'/library';
	include_once($library_path.'/classes/common_function.php');
	require_once($GLOBALS['fileroot'].'/library/classes/cqm_import.php');
	
	$cqmObj = New CQIMPORT();
	
	$task = (isset($_REQUEST['task']) && empty($_REQUEST['task']) == false) ? $_REQUEST['task'] : false;
	$returnVal = '';
	switch($task){
		//Check Zip file validation
		case 'uploadZip':
			$status = $cqmObj->uploadZip($_REQUEST,$_FILES);
		break;
		
		case 'importPatient':
			$ptId = (isset($_REQUEST['pt_id']) && empty($_REQUEST['pt_id']) == false) ? $_REQUEST['pt_id'] : '';
			$zipName = (isset($_REQUEST['zipName']) && empty($_REQUEST['zipName']) == false) ? $_REQUEST['zipName'] : '';
			$provId = (isset($_REQUEST['provId']) && empty($_REQUEST['provId']) == false) ? $_REQUEST['provId'] : '';
			$facId = (isset($_REQUEST['facilityId']) && empty($_REQUEST['facilityId']) == false) ? $_REQUEST['facilityId'] : '';
			$ptNm = (isset($_REQUEST['ptNm']) && empty($_REQUEST['ptNm']) == false) ? $_REQUEST['ptNm'] : '';
			$fileName = (isset($_REQUEST['fileName']) && empty($_REQUEST['fileName']) == false) ? $_REQUEST['fileName'] : '';
			
			$returnVal1 = $cqmObj->handlePtImport($ptId, $zipName, $provId, $facId, $ptNm, $fileName);
			$importStatus = json_encode(array('ptId' => $_REQUEST['pt_id'], 'import' => $returnVal1), JSON_PRETTY_PRINT);
			
			$returnVal = array('ptId' => $_REQUEST['pt_id'], 'status' => $returnVal1['globalStatus']);
		break;
		
		case 'delPt':
			$ptId = (isset($_REQUEST['pt_id']) && empty($_REQUEST['pt_id']) == false) ? $_REQUEST['pt_id'] : '';
			
			$counter = 0;
			$chkQuery = imw_query('SELECT id FROM patient_data where id = "'.$ptId.'"');
			if(imw_num_rows($chkQuery) > 0){
				$row = imw_fetch_assoc($chkQuery);
				$delQry = imw_query('DELETE FROM patient_data where id = "'.$row['id'].'"');
				if($delQry) $counter++;
			}
			
			$returnVal = $counter;
		break;
		
		default:
			$returnVal = false;
	}
	
	
	
	echo json_encode($returnVal);
	
	// Saving Status for Each Patient Imported
	$filePath = $cqmObj->dirPath.'importStatus_'.$_REQUEST['zipName'].'.txt';
	
	//Appending Import Status to file
	file_put_contents($filePath, $importStatus.'!~~~~~~~~~~~~~~~~~~~~~~~~~~!', FILE_APPEND | LOCK_EX);
?>