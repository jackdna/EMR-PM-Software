<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

/*
 * File: amd_post_charges.php
 * Coded in PHP7
 * Purpose: Post Charges to advanced MD
 * Access Type: Include
*/
ini_set('max_execution_time', 600);

$rethrow = false;
try{
	require_once(dirname(__FILE__).'/amd_patient.php');
	
	if( !defined( 'AMD_POST_CHARGES' ) || AMD_POST_CHARGES !== "YES" )
		throw new amdException( 'Configuration Error', 'AMD Integration functionality is not enabled for the practice. Please contact support.' );
	
	$amdPtObj = new amd_patient();
	
	/*Pull AMD patient*/
	$amdPatientId = false;
	$amdInsOrder = '';
	$sqlPtId = 'SELECT `amd_patient_id`, `amd_ins_order` FROM `patient_data_tbl` WHERE `patient_id`='.$apptPatientId;
	$respPtId = imw_query($sqlPtId);
	if( $respPtId && imw_num_rows($respPtId) > 0 )
	{
		$respPtId = imw_fetch_assoc($respPtId);
		$amdPatientId = $respPtId['amd_patient_id'];
		$amdInsOrder = $respPtId['amd_ins_order'];
	}
	
	if( $amdPatientId != true )
		throw new amdException( 'Patient Mapping Error', 'AMD Patient ID does not exists for the patient.' );
	
/*Pull AMD Visit Details*/
	$amdVisitId = false;
	$amdVisitDate = false;
	$amdVisitProvider = false;
	$amdFacId = false;
	$amdFinancialClass = false;
	$amdRespParty = false;
	$sqlVisitId = "SELECT `wt`.`amd_visit_id`, `wt`.`amd_facility_code`, `wt`.`amd_user_id`, `wt`.`amd_finclasscode`, `wt`.`amd_respparty`, DATE_FORMAT(`wt`.`dos`, '%m/%d/%Y') AS 'dos' FROM `stub_tbl` `st` INNER JOIN `patient_in_waiting_tbl` `wt` ON (`st`.`iolink_patient_in_waiting_id` = `wt`.`patient_in_waiting_id`) WHERE `st`.`patient_confirmation_id`=".$apptConfirmationID." AND `wt`.`amd_visit_id`!=0 AND `st`.`patient_id_stub`=".$apptPatientId;
	
	$respVisittId = imw_query($sqlVisitId);
	if( $respVisittId )
	{
		if( imw_num_rows($respVisittId) == 1 )
		{
			$respVisittId = imw_fetch_assoc($respVisittId);
			$amdVisitId = $respVisittId['amd_visit_id'];
			$amdFacId = $respVisittId['amd_facility_code'];
			$amdVisitProvider = $respVisittId['amd_user_id'];
			$amdVisitDate = $respVisittId['dos'];
			$amdFinancialClass = $respVisittId['amd_finclasscode'];
			$amdRespParty = $respVisittId['amd_respparty'];
		}
		elseif( imw_num_rows($respVisittId) > 1 )
			throw new amdException( 'Visit MappingError', 'Multiple entries found for the same Visit Confirmation ID.' );
	}
	
	if( $amdVisitId === false )
		throw new amdException( 'Visit Mapping Error', 'AMD Visit ID does not exists for the Appointment.' );
	
	$amdProviderIds = array('surgeon'=>'', 'anesthesia'=>'', 'facility'=>'');
	
	/*Pull AMD Provider ID for Surgeon* /
	$sqlSurgeon = "SELECT `u`.`amd_user_id` FROM `patientconfirmation` `pc` INNER JOIN `users` `u` ON(`pc`.`surgeonId`=`u`.`usersId`) WHERE `pc`.`patientConfirmationId`=".$apptConfirmationID." AND TRIM(`u`.`amd_user_id`) != ''";
	$respSurgeon = imw_query($sqlSurgeon);
	
	if( $respSurgeon )
	{
		if( imw_num_rows($respSurgeon) > 0 )
		{
			$respSurgeon= imw_fetch_assoc($respSurgeon);
			$amdProviderIds['surgeon'] = explode(',', $respSurgeon['amd_user_id']);
			$amdProviderIds['surgeon'] = trim($amdProviderIds['surgeon'][0]);
		}
	}/**/
	$amdProviderIds['surgeon'] = $amdVisitProvider;
	
	/*Pull AMD Provider ID for Anesthesiologist*/
	$sqlAnes = "SELECT `u`.`amd_user_id`, `u`.`usersId` AS 'provider_id' FROM `patientconfirmation` `pc` INNER JOIN `users` `u` ON(`pc`.`anesthesiologist_id`=`u`.`usersId`) WHERE `pc`.`patientConfirmationId`=".$apptConfirmationID." AND TRIM(`u`.`amd_user_id`) != ''";
	$respAnes = imw_query($sqlAnes);
	$anesIMWId = 0;
	if( $respAnes )
	{
		if( imw_num_rows($respAnes) > 0 )
		{
			$respAnes = imw_fetch_assoc($respAnes);
			//$amdProviderIds['anesthesia'] = explode(',', $respAnes['amd_user_id']);
			//$amdProviderIds['anesthesia'] = trim($amdProviderIds['anesthesia'][0]);
			$anesIMWId = trim($respAnes['provider_id']);
		}
	}/**/
	
	$anesProviders = array();

	if(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'SCC_INDIANA') {
	
		/*CACCHILLO, PAUL F*/
		$anesProviders[17][1] = '12';
		$anesProviders[17][1] = '147';

		/*HOPEN, PATRICK*/
		$anesProviders[18][1] = '14';
		$anesProviders[18][1] = '149';

		/*LOMBARDO, ANTHONY J*/
		$anesProviders[19][1] = '9';
		$anesProviders[19][1] = '152';

		/*ORR, MICHAEL G*/
		$anesProviders[7][1] = '10';
		$anesProviders[7][1] = '153';

	} elseif (isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTE') {

		/*PENDYALA, SANDEEP*/
		$anesProviders[13][1] = '752';
		$anesProviders[13][1] = '818';

		/*FISCHER, CASEY*/
		$anesProviders[118][1] = '22';

		/*REDDY, AMARNADH*/
		$anesProviders[16][1] = '753';

	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'TOMOKA') {

		/*COX, KYLE FITZGERALD*/
		$anesProviders[22][2] = '8';

		/*KENNEDY, MARK EDWARD*/
		$anesProviders[44][2] = '10';

		/*MAKOWSKI, MICHAEL KEVIN*/
		$anesProviders[19][2] = '11';

		/*MYER, RORY ALEXANDER*/
		$anesProviders[23][2] = '12';

		/*ROOT, TIMOTHY DAVID*/
		$anesProviders[21][2] = '14';

		/*SPERTUS, ALAN DAVID*/
		$anesProviders[20][2] = '15';

	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'NWES') {

		// NWES Provider Mapping
		/*Bailey, Kristi L*/
		$anesProviders[36][2] = '204';

		/*Cadera, Werner*/
		$anesProviders[37][4] = '234';

		/*Cameron, Bruce*/
		$anesProviders[38][4] = '262';
		$anesProviders[38][3] = '309';

		/*Chin, Victor M*/
		$anesProviders[40][4] = '281';

		/*Griggs, Paul*/
		$anesProviders[41][3] = '215';
		$anesProviders[41][4] = '224';

		/*Huh, Sara*/ // Deleted marked in the database
		$anesProviders[43][4] = '268';
		$anesProviders[43][2] = '264';

		/*Kuzin, Aaron*/
		$anesProviders[44][2] = '211';

		/*Lu, Meng*/ // Deleted marked in the database
		$anesProviders[45][4] = '227';
		$anesProviders[45][3] = '219';

		/*Niemeyer, Matthew*/
		$anesProviders[46][3] = '1845';
		$anesProviders[46][5] = '243';
			
		/*Osgood, Thomas*/
		$anesProviders[47][2] = '252';

		/*Talley Rostov, Audrey*/
		$anesProviders[48][4] = '253';

		/*Fedan, Ashley*/
		$anesProviders[49][4] = '295';
		$anesProviders[49][3] = '294';
		$anesProviders[49][2] = '291';
		$anesProviders[49][5] = '297';

		/*Velazquez, Robert*/
		$anesProviders[50][5] = '277';
		$anesProviders[50][3] = '270';
		$anesProviders[50][4] = '274';
		$anesProviders[50][2] = '266';

		/*Pham, Thanh*/	
		$anesProviders[51][3] = '330';
		$anesProviders[51][4] = '331';

		/*NICKLESON,JAMES*/
		$anesProviders[108][2] = '322';
		$anesProviders[108][3] = '323';
		$anesProviders[108][4] = '325';
		$anesProviders[108][5] = '324';

		/*RICHEY, LIANA*/
		$anesProviders[109][4] = '329';
		$anesProviders[109][3] = '327';
		$anesProviders[109][2] = '326';
		$anesProviders[109][5] = '328';

		/*Klimczyk, Patrick*/
		$anesProviders[112][4] = '288';
		$anesProviders[112][2] = '283';
		$anesProviders[112][5] = '289';
		$anesProviders[112][3] = '285';

		/*MCCANN, DAVID*/
		$anesProviders[123][4] = '321';
		$anesProviders[123][3] = '319';
		$anesProviders[123][2] = '318';
		$anesProviders[123][5] = '320';

		/*WESNER,HEATHER*/
		$anesProviders[148][4] = '340';
		$anesProviders[148][2] = '341';
		$anesProviders[148][3] = '339';
		$anesProviders[148][5] = '342';

		/*KAUSHIK,NITYA*/
		$anesProviders[149][4] = '317';
		$anesProviders[149][3] = '315';
		$anesProviders[149][2] = '314';
		$anesProviders[149][5] = '316';
			
		/*Hoki, Susan*/
		$anesProviders[194][4] = '256';
		$anesProviders[194][3] = '254';

		/*Huang, Agnes*/
		$anesProviders[213][3] = '1833';

		/*Carlson, Ingrid A*/
		$anesProviders[220][2] = '275';

		/*Lee, Brian*/
		$anesProviders[235][3] = '1856';
			
		/*Wilson, Faye*/
		$anesProviders[234][4] = '1860';
		$anesProviders[234][3] = '1859';
		$anesProviders[234][2] = '1858';
		$anesProviders[234][5] = '1861';

	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTEGRITY_DMEO') {
		
		$anesProviders[9][1] = '250'; // CROW,TOM
		$anesProviders[10][1] = '248'; // HAYDEN,MORAN

	} else {
		// Sandbox
	}
	
	
	if( isset($anesProviders[(int)$anesIMWId]) )
		$amdProviderIds['anesthesia'] = trim($anesProviders[(int)$anesIMWId][(int)$_SESSION['facility']]);
	/** /$amdProviderIds['anesthesia'] = '18';	/*Temp Anes - Local Testing*/
	/*Pull AMD Provider ID for Facility Charges* /
	$sqlFac = "SELECT `amd_user_id` FROM `users` WHERE LOWER(`fname`) = 'facility' AND TRIM(`amd_user_id`) != ''";
	$respFac = imw_query($sqlFac);
	
	if( $respFac )
	{
		if( imw_num_rows($respFac) > 0 )
		{
			$respFac = imw_fetch_assoc($respFac);
			$amdProviderIds['facility'] = explode(',', $respFac['amd_user_id']);
			$amdProviderIds['facility'] = trim($amdProviderIds['facility'][0]);
		}
	}/**/
	
	if(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'SCC_INDIANA') {
		$facilityIds = array(1=>16);
	} elseif (isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTE') {
		$facilityIds = array(1=>752);
	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'TOMOKA') {
		$facilityIds = array(2=>61);
	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'NWES') {
		$facilityIds = array(2=>195,3=>198,4=>197,5=>196);
	} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTEGRITY_DMEO') {
		$facilityIds = array(1=>250);
	}else {
		// sandbox
	}
	$amdProviderIds['facility'] = $facilityIds[(int)$_SESSION['facility']];
	/** /$amdProviderIds['facility'] = '19';	/*Temp Fac - Local Testing*/
	
	/*Pull charges by using appointment confirmation ID - for the superbill*/
	$sqlCharges = "SELECT 
						`sb`.`superbill_id`, 
						`sb`.`cpt_code`, 
						`sb`.`dxcode_icd10` AS 'diagnosis_code', 
						TRIM(CONCAT(`sb`.`modifier1`, ' ', `sb`.`modifier2`, ' ', `sb`.`modifier3`)) AS 'modifiers', 
						`sb`.`quantity`, 
						`sb`.`bill_user_type`, 
						`proc`.`codeFacility`, 
						`proc`.`codePractice`, 
						`proc`.`catId`,
						`procat`.`isMisc`, 
						`procat`.`isInj`	
					FROM 
						`patientconfirmation` `pc` 
						INNER JOIN `superbill_tbl` `sb` ON(`pc`.`patientConfirmationId` = `sb`.`confirmation_id`) 
						INNER JOIN `procedures` `proc` ON(`sb`.`cpt_id` = `proc`.`procedureId`) 
						INNER JOIN `procedurescategory` `procat` ON(`procat`.`proceduresCategoryId` = `proc`.`catId`)
					WHERE 
						`pc`.`patientConfirmationId` = ".$apptConfirmationID." 
						AND `sb`.`deleted` = '0' 
					ORDER BY 
						IF(
							FIELD(
								`sb`.`cpt_id`, `pc`.`patient_primary_procedure_id`, 
								`pc`.`patient_secondary_procedure_id`, 
								`pc`.`patient_tertiary_procedure_id`
							) = 0, 
							1, 
							0
						), 
						FIELD(`proc`.`catId`, 1, 21, 20)";
	$respCharges = imw_query($sqlCharges);
	
	
	/*Get aNESTHESIAStartTime and EndTime*/
	$anesTime = array('start'=>array(), 'stop'=>array());
	$sqlAnesTime = "SELECT `startTime`, `stopTime`, newStartTime2, newStopTime2, newStartTime3, newStopTime3 FROM `localanesthesiarecord` WHERE `confirmation_id`='".$apptConfirmationID."'";
	$respAnesTime = imw_query($sqlAnesTime);
	if( $respAnesTime && imw_num_rows($respAnesTime) > 0 )
	{
		$respAnesTime = imw_fetch_assoc($respAnesTime);
		//$anesTime['start'] = $respAnesTime['startTime'];
		//$anesTime['stop'] = $respAnesTime['stopTime'];
		
		array_push($anesTime['start'], $respAnesTime['startTime']);
		array_push($anesTime['start'], $respAnesTime['newStartTime2']);
		array_push($anesTime['start'], $respAnesTime['newStartTime3']);
		
		array_push($anesTime['stop'], $respAnesTime['stopTime']);
		array_push($anesTime['stop'], $respAnesTime['newStopTime2']);
		array_push($anesTime['stop'], $respAnesTime['newStopTime3']);
	}
	
	/*Set Flag to process Charges*/
	$processCharges = ( $respCharges && imw_num_rows( $respCharges ) > 0 );
	
	/*Build Charges Array for Advanced MD API Call, categorized by provider/professional, facility and anesthesia*/
	$amdCharges = array('surgeon'=>array(), 'facility'=>array(), 'anesthesia'=>array());
	
	
	$holdAnesStart = '';
	$holdAnesEnd = '';
	$holdAnesDuration = '';

	// check Anesthesia charges global setting, If set to no then skipped anesthesia charges
	$ANESTHESIA_CHARGES = constant('AMD_POST_ANESTHESIA_CHARGES');
	
	include('connect_imwemr.php');
	if( $processCharges ){
		while( $row = imw_fetch_assoc($respCharges) )
		{
			$tempCharge = array();
			//$amdChargesSlot = false;
			
			//$tempCharge['@proccode'] = $row['cpt_code'];
			$tempCharge['@units'] = $row['quantity'];
			$tempCharge['@diagcodes'] = $row['diagnosis_code'];
			$tempCharge['@modcodes'] = $row['modifiers'];
			$tempCharge['@begindate'] = $amdVisitDate;
			$tempCharge['@enddate'] = $amdVisitDate;
			
			$tempCharge['@pos'] = "24";
			$tempCharge['@tos'] = "02";
			$tempCharge['@billins'] = "1";
			$tempCharge['@finclasscode'] = $amdFinancialClass;
			
			/*$chargeTypeFac = '';
			$chargeTypeAnes = '';
			$chargeTypePhy = '';*/
			$chargeType = '';
			
			$cptFeeMultiplier = 1;
			
			if( $row['codePractice'] != '' )
			{
				$tempCharge['@proccode'] = $row['codePractice'];
				
				/*Add Anesthesia Charge*/
				/* excluding category 2 (Laser procedure) and category which marked as Miscellaneous and Injection */
				if( (int)$row['bill_user_type'] == 1 && $row['catId'] != 2 && $row['isMisc'] != 1 && $row['isInj'] != 1 && ($ANESTHESIA_CHARGES == NULL || $ANESTHESIA_CHARGES != "NO") )
				{
					$tempCharge['@tos'] = "07";
					
					if($holdAnesStart =='')
					{
						foreach( $anesTime['start'] as $key=>$time )
							$anesTime['start'][$key] = new DateTime($time);
						
						foreach( $anesTime['stop'] as $key=>$time )
							$anesTime['stop'][$key] = new DateTime($time);
						
						$tempCharge['@begintime'] = $anesTime['start'][0];
						$tempCharge['@endtime'] = $anesTime['stop'][0];
						
						foreach( $anesTime['start'] as $time )
							$tempCharge['@begintime'] = ( $tempCharge['@begintime'] > $time && $time->format('H:i:s') != '00:00:00' ) ? $time : $tempCharge['@begintime'];
						
						$tempDuration = array();
						
						foreach( $anesTime['stop'] as $key=>$time )
						{
							$tempCharge['@endtime'] = ( $tempCharge['@endtime'] < $time && $time->format('H:i:s') != '00:00:00' ) ? $time : $tempCharge['@endtime'];
							
							if( $anesTime['start'][$key]->format('H:i:s') != '00:00:00' && $time->format('H:i:s') != '00:00:00' )
							{
								$timeStart = $anesTime['start'][$key];
								$timeEnd = $time;
								$timDiff = $timeStart->diff($timeEnd);
								
								$diffDays = (int)$timDiff->format('%d');
								$diffHours = ($diffDays * 24) + (int)$timDiff->format('%H');	/*Convert Days to hours and add remaining hours*/
								$diffMinutes = ($diffHours * 60) + (int)$timDiff->format('%I');	/*Convert Hours to minutes and add remaining minutes*/
								array_push($tempDuration, $diffMinutes);
							}
							else
								array_push($tempDuration, 0);
						}
						
						$tempCharge['@begintime'] =$tempCharge['@begintime']->format('H:i:s');
						$tempCharge['@endtime'] = $tempCharge['@endtime']->format('H:i:s');
						$tempCharge['@duration'] = array_sum($tempDuration);
						
						$holdAnesStart = $tempCharge['@begintime'];
						$holdAnesEnd = $tempCharge['@endtime'];
						$holdAnesDuration = $tempCharge['@duration'];
					}
					else
					{
						$tempCharge['@begintime'] = $holdAnesStart;
						$tempCharge['@endtime'] = $holdAnesEnd;
						$tempCharge['@duration'] = $holdAnesDuration;
					}
					
					/*Dynamic Unit and Fee Calculation for the Anesthesia charge*/
					$additionalUnits = (float)$tempCharge['@duration'] / 15;
					$additionalUnits = round($additionalUnits, 1);
					$tempCharge['@units'] = $additionalUnits;
					
					$chargeType = 'anesthesia';
				}
				elseif( (int)$row['bill_user_type'] == 2 )
				{	
					$chargeType = 'surgeon';	/*Add Professional Charge*/
					$modifiersList = explode(' ',$tempCharge['@modcodes']);
					$cptFeeMultiplier = ( in_array('50', $modifiersList) ) ? 2 : 1;
				}
			}
			
			/*Add Facility Charge*/
			if( $row['codeFacility'] != '' && (int)$row['bill_user_type'] == 3 )
			{
				$tempCharge['@proccode'] = $row['codeFacility'];
				$chargeType = 'facility';
			}
			
			if($chargeType!='')
				array_push($amdCharges[$chargeType], $tempCharge);
			/*if($chargeTypeAnes!='')
				array_push($amdCharges[$chargeTypeAnes], $tempCharge);
			if($chargeTypePhy!='')
				array_push($amdCharges[$chargeTypePhy], $tempCharge);*/
		}
	}
	include("common/conDb.php");
	
	if( count($amdCharges['surgeon']) < 1 && count($amdCharges['facility']) < 1 && count($amdCharges['anesthesia']) < 1 )
		throw new amdException( 'Charges Error', 'Charges not found for posting to AMD' );
	
	/*POST Charges to Advanced MD*/
	$chargesResp = $amdPtObj->saveCharge($amdPatientId, $amdRespParty, $amdVisitId, $amdVisitDate, $amdVisitProvider, $amdProviderIds, $amdFacId, $amdCharges, $amdInsOrder);
	
	//print_r(json_decode($chargesResp));
	//print_r($amdCharges);
}
catch(amdException $e){
	$_SESSION['amd_error'] = '<strong>'.$e->getErrorType().'</strong><br />'.$e->getErrorText();
	
	$sqlLog = "INSERT INTO `amd_charges_log` SET
				`pt_id`='".$amdPatientId."',
				`amd_visit_id`='".$amdVisitId."',
				`anes_status`='',
				`fac_status`='',
				`prov_status`='',
				`anes_reason`='',
				`fac_reason`='',
				`prov_reason`='',
				`date_posted`='".date('Y-m-d H:i:s')."',
				`m_amd_visit_id`='".$amdVisitId."',
				`type`='1'";
	imw_query($sqlLog);
}

/*Unset the object*/
if( isset($amdPtObj) && is_object($amdPtObj) )
	unset($amdPtObj);