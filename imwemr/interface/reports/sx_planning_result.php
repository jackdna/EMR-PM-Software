<?php
	require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
	require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");

	include_once $GLOBALS['srcdir']."/classes/work_view/sx_plan.class.php";
	$library_path = $GLOBALS['webroot'].'/library';
	
	//Contains Print Template structure
	require_once("sx_planning_sheet_template.php");
	
	//Request Variables
	$formSubmitted = (isset($_REQUEST['form_submitted']) && empty($_REQUEST['form_submitted']) == false) ? trim($_REQUEST['form_submitted']) : '';
	$selPhy = (is_array($_REQUEST['Physician']) && count($_REQUEST['Physician']) > 0) ? implode("', '",$_REQUEST['Physician']) : '';
	$reqDate = (isset($_REQUEST['apptDate']) && count($_REQUEST['apptDate']) > 0) ? trim($_REQUEST['apptDate']) : '';
	
	//All Physicians Array
	//$phyArr = $CLSCommonFunction->drop_down_providers('','1','1','array');
	
	
	//Format Requested Date
	$curDate = date('Y-m-d', strtotime("now"));
	$startDate = $endDate = '';
	switch(strtolower($reqDate)){
		case 'daily':
			$startDate = $endDate = $curDate;
		break;
		
		case 'weekly':
			$endDate = $curDate;
			$startDate = date('Y-m-d', strtotime("-1 week"));
		break;

		case 'monthly':
			$endDate = $curDate;
			$startDate = date('Y-m-d', strtotime("-1 month"));
		break;
		
		case 'quarterly':
			$endDate = $curDate;
			$startDate = date('Y-m-d', strtotime("-6 month"));
		break;
		
		case 'date':
			//Start Date
			list($mSt, $dSt, $ySt) = explode('-', $_REQUEST['Start_date']);
			$startDate = (checkdate($mSt, $dSt, $ySt) === true) ? date('Y-m-d', strtotime($ySt.'-'.$mSt.'-'.$dSt)) : '';
			
			//End Date
			list($mEt, $dEt, $yEt) = explode('-', $_REQUEST['End_date']);
			$endDate = (checkdate($mEt, $dEt, $yEt) === true) ? date('Y-m-d', strtotime($yEt.'-'.$mEt.'-'.$dEt)) : '';
		break;
	}	
	
	//Validating Dates
	$validate = true;
	$errorMsg = '';
	if(empty($startDate) || empty($endDate)){$errorMsg = '<div class="text-center alert alert-info">Invalid Date values provided. Please try again</div>'; $validate = false;}
	else if(strtotime($startDate) > strtotime($endDate)){$errorMsg = '<div class="text-center alert alert-info">Start Date cannot be greater than End Date.</div>'; $validate = false;};
	
	//If Date is valid continue code
	if($validate === true){
		//Variables Needed
		$ptArr = $tempData = array();
		
		//Get Patient IDs between selected date range
		
		//If any physician selected get only records of that
		if(empty($selPhy) == false) $where = "and surgeon_id IN ('".$selPhy."') ";
		
		//Getting Sx Data
		$Qry = "
			SELECT 
				id,
				patient_id,
				provider_id,
				sx_plan_dos,
				surg_dt
			FROM 
				chart_sx_plan_sheet 
			WHERE 
				(sx_plan_dos BETWEEN '".$startDate."' AND '".$endDate."') AND 
				del_status='0' 
				".$where."
			ORDER BY 
				sx_plan_dos,id DESC
		";
		$sqlQry = imw_query($Qry);
		
		if($sqlQry && imw_num_rows($sqlQry) > 0){
			while($rowFetch = imw_fetch_assoc($sqlQry)){
				$tmpArr = array(
					'ptId' => $rowFetch['patient_id'], 
					'planId' => $rowFetch['id'], 
					'Provider' => $rowFetch['provider_id'], 
					'createDate' => $rowFetch['sx_plan_dos'],
					'surgeryDt' => $rowFetch['surg_dt'],
					'DataType' => 'SxData'
				);
				if(empty($rowFetch['sx_plan_dos']) == false && isset($rowFetch['sx_plan_dos'])){
					if(!isset($ptArr[$rowFetch['sx_plan_dos']])) $ptArr[$rowFetch['sx_plan_dos']] = array();
					array_push($ptArr[$rowFetch['sx_plan_dos']], $tmpArr);
				}
			}
		}
		
		
		//Get IOL Master Data for the given Date Range
		if(empty($where) == false) $where = str_replace('surgeon_id', 'signedById', $where);
		unset($tmpArr);
		$iolMasterQry = imw_query("
			SELECT
				iol_master_id,
				patient_id,
				signedById,
				examDate
			FROM 
				iol_master_tbl
			WHERE
				(DATE(examDate) BETWEEN '".$startDate."' AND '".$endDate."') AND 
				del_status = 0 AND
				purged = 0
				".$where."
			ORDER BY 
				examDate,iol_master_id DESC
		");
		
		if($iolMasterQry && imw_num_rows($iolMasterQry) > 0){
			while($rowFetchIol = imw_fetch_assoc($iolMasterQry)){
				$tmpArr = array(
					'ptId' => $rowFetchIol['patient_id'], 
					'iolMasterId' => $rowFetchIol['iol_master_id'], 
					'Provider' => $rowFetchIol['signedById'], 
					'createDate' => $rowFetchIol['examDate'],
					'DataType' => 'IOL_Master'
				);
				
				if(empty($rowFetchIol['examDate']) == false && isset($rowFetchIol['examDate'])){
					if(!isset($ptArr[$rowFetchIol['examDate']])) $ptArr[$rowFetchIol['examDate']] = array();
					array_push($ptArr[$rowFetchIol['examDate']], $tmpArr);
				}
			}
		}
		
		//If there is any patient for given date continue
		if(count($ptArr) > 0){
			foreach($ptArr as $createDate => &$values){
				if(is_array($values) && count($values) > 0){
					foreach($values as $obj){
						if(isset($obj['DataType']) && empty($obj['DataType']) == false){
							switch(strtoupper($obj['DataType'])){
								case 'SXDATA':
									//Sx Plan Variables
									$sxObj = '';
									
									$patientId = $obj['ptId'];
									$planId = $obj['planId'];
									
									//Sx Plan Object - returns Data for provided ptId & planId
									$sxObj = New Sx_Plan($patientId,$planId);
									
									$printPdf = $sxObj->getSxPlanReportData($obj);
									if($printPdf || is_array($printPdf)) $tempData[] = $printPdf;
								break;
								
								case 'IOL_MASTER':
									//Sx Plan Variables
									$sxObj = '';
									
									$patientId = $obj['ptId'];
									
									//Sx Plan Object - returns Data for provided ptId & planId
									$sxObj = New Sx_Plan($patientId);
									
									$printIolPdf = $sxObj->getIolMasterData($obj);
									if($printIolPdf || is_array($printIolPdf)){
										foreach($printIolPdf as $objectPDF){
											$tempData[] = $objectPDF;
										}
									} 
								break;
							}
						}
					}
				}
			}
			
			if(count($tempData) > 0){
				$finalHtml = implode(' ', $tempData);
				$fileLocation = write_html($finalHtml,'sxPlanHtml.html');
				
				if(empty($fileLocation) == false){
					?>
					<script>
						top.show_loading_image('hide');
						top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
						top.html_to_pdf('<?php echo $fileLocation ?>', 'p');
					</script>
					<?php
					echo '<div class="text-center alert alert-info">Sx Plan sheets printed.</div>';
				}
				
			}else{
				echo '<div class="text-center alert alert-warning">No Sx plan Sheets data found.</div>';
			}
			
		}else{
			echo '<div class="text-center alert alert-warning">No Sx plan Sheets found for given date range.</div>';
		}
		
	}else{
		echo $errorMsg;
	}
?>