<?php

if( !isset($hl7) && !is_object($hl7) )
	exit('Object does not exists');
	
	require_once( dirname(__FILE__).'/../../library/classes/work_view/Facility.php' );

	$time = date('H:i:s');	/*24 hour format*/
	$date = date('Y-m-d');	/*YYY-MM-DD*/

	$patientId = (int)$hl7->PID('patient_external_id');		//02
	if ( $patientId === 0)
	{
		throw new Exception('IMW Patient Id not found');
	}

	$lastName = imw_real_escape_string( $hl7->PID('patient_name', 'family_name') );
	$firstName = imw_real_escape_string( $hl7->PID('patient_name', 'given_name') );

	$appointmentId =  (int)$hl7->PV1('visit_number');	//19
	if ( $appointmentId == 0)
	{
		throw new Exception('IMW Appointment ID doe not exists');
	}

	/*Get Physician ID from appointment*/
	$sql = 'SELECT
				`sa`.`sa_doctor_id`,
				DATE_FORMAT(
					`sa`.`sa_app_start_date`, \'%Y-%m-%d\'
				) AS \'sa_app_start_date\',
				DATE_FORMAT(
					`sa`.`sa_app_starttime`, \'%H:%i:%s\'
				) AS \'sa_app_start_time\',
				`sa`.`sa_facility_id`,
				`sa`.`case_type_id` AS \'insurance_case\',
				GROUP_CONCAT(`ins`.`provider`) AS \'insurance_provider\',
				GROUP_CONCAT(`ins`.`type`) AS \'insurance_type\'
			FROM
				`schedule_appointments` `sa`
				LEFT JOIN `insurance_data` `ins` ON(
					`sa`.`sa_patient_id` = `ins`.`pid`
					AND `sa`.`case_type_id` = `ins`.`ins_caseid`
					AND `ins`.`actInsComp` = 1
					AND `ins`.`type` IN(\'primary\', \'secondary\')
				)
			WHERE
				`sa`.`id` = '.$appointmentId.'
			GROUP BY
				`ins`.`ins_caseid`';
	$resp = imw_query($sql);
	if( !$resp && imw_num_rows($resp) != 1 )
	{
		throw new  Exception('Unable to locate appointment id in imwemr');
	}

	$apptData = imw_fetch_assoc($resp);
	$physicianId = (int)$apptData['sa_doctor_id'];
	$apptDate = trim( $apptData['sa_app_start_date'] );
	$apptTime = trim( $apptData['sa_app_start_time'] );
	$apptFacilityId = (int)$apptData['sa_facility_id'];
	$insuranceCaseId = (int)$apptData['insurance_case'];
	$insuranceProvider = explode(',', $apptData['insurance_provider']);
	$insuranceType = explode(',', $apptData['insurance_type']);

	if( $physicianId == 0)
	{
		throw new  Exception('Physician Id not found in imwemr for the appointment Id supplied', 2);
	}

	if( $apptDate == '' )
	{
		throw new  Exception('Appointment start date / DOS not found in imwemr', 2);
	}

	$dob = $hl7->PID('dob');
	$dob = preg_replace('/[^0-9]/', '', $dob);	/*Remove non numeric values from date of birth*/

	if( strlen($dob) < 8)
	{
		throw new Exception('DOB is not in correct format');
	}
	/*Arrange DOB in correct format to be Queried from DB*/
	$dob = substr($dob, 0, 4).'-'.substr($dob, 4, 2).'-'.substr($dob, 6, 2);

	/*Validate PatientId and appointment ID*/
	$sql = 'SELECT 
				`sa`.`id`
			FROM 
				`schedule_appointments` `sa`
			INNER JOIN
				`patient_data` `pd`
				ON(`sa`.`sa_patient_id` = `pd`.`id`)
			WHERE `sa`.`id` = '.$appointmentId.' AND `sa`.`sa_patient_id` = '.$patientId.'
					AND LOWER(`pd`.`lname`) = \''.imw_real_escape_string(strtolower($lastName)).'\'
					AND `pd`.`fname` = \''.imw_real_escape_string(strtolower($firstName)).'\' AND `pd`.`DOB` = \''.$dob.'\'';
	
	$resp = imw_query($sql);
	if( !$resp || imw_num_rows($resp) != 1 )
	{
		throw new Exception('Patient demographics/Appointment does not match in IMW');
	}

	/*Check if charges entry exists in the message*/
	$cptCount = ( array_key_exists('FT1', $hl7->message) ) ?  count($hl7->message['FT1']) : 0;
	if( $cptCount < 1 )
	{
		throw new  Exception('No Charges detected in the messge.');
	}

	/*Transaction batch ID from first FT1 segment in the message to Create Superbill in imwemr*/
	$transactionBatchId = trim( $hl7->FT1('transaction_batch_id') );
	if( $transactionBatchId == '' )
	{
		throw new Exception('Please provide transaction batch ID');
	}
	$transactionBatchId = imw_real_escape_string($transactionBatchId);

	/*Check if we have already processed the transaction batch supplied and superbill is created for the same Transaction Batch ID. Reject if it is already exists*/
	$sql = 'SELECT
				`idSuperBill`
			FROM
				`superbill`
			WHERE
				`hl7_batch_id` = \''.$transactionBatchId.'\'';
	$resp = imw_query($sql);
	if( $resp && imw_num_rows($resp) > 0 )
	{
		throw new Exception('Transaction batch already processed');
	}

	$encounterId = '';
	/*Generate Encounter Id to crete Superbill*/
	$objFacility = new Facility();
	do{
		$encounterId = $objFacility->getEncounterId();
		$encounterId = preg_replace('/[^0-9]/', '', $encounterId);
		$qry=imw_query("SELECT `idSuperBill` FROM `superbill` WHERE `del_status`='0' AND `encounterId` = ".$encounterId);
		$getMatchFound=imw_fetch_object($qry);
	}while($getMatchFound);
	unset($getMatchFound);

	if( empty($encounterId) === true )
	{
		throw new Exception('Unable to create encounter in imwemr', 2);
	}
	$encounterId = (int)$encounterId;

	// $transactionDateTime = $hl7->FT1('transaction_date_time');
	// $transactionDateTime = preg_replace('/[^0-9]/', '', $transactionDateTime);
	// $transactionDateTime = str_pad($transactionDateTime, 14, 0);
	// $transactionDateTime = trim($transactionDateTime);

	// $date = preg_split('/([0-9]{4})([0-9]{2})([0-9]{2})(?:.*)/', $transactionDateTime, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	// $date = implode('-', $date);

	// $time = preg_split('/(?:.{8})([0-9]{2})([0-9]{2})([0-9]{2})(?:.*)/', $transactionDateTime, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	// $time = implode(':', $time);

	/*Extract Charges Data*/
	$charges = array();
	$cptOrder = array();	/*Container to hold CPT codes in serial order to be entered in superbill table*/
	$cptDiagnosis = array();	/*Container to hold diagnosis codes from FT1 segment*/
	for( $i = 0; $i < $cptCount; $i++ )
	{
		$charge = array();

		$transactionType		= $hl7->{'FT1'.$i}('transaction_type');
		$transactionType		= trim($transactionType);
		if( strtolower($transactionType) !== 'cg')
		{
			continue;
		}
		unset($transactionType);

		$charge['transactionId']	= imw_real_escape_string( $hl7->{'FT1'.$i}('transaction_id') );

		/*Check if FT1 segment with same transaction id is already processed*/
		$sql = 'SELECT `id`
				FROM
					`procedureinfo`
				WHERE
					`hl7_transaction_id` = \''.$charge['transactionId'].'\'';
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) > 0 )
		{
			throw new Exception('FT1 segment with transactin id `'.$charge['transactionId'].'` is already processed');
		}
		/*End transaction id check*/

		$charge['cpt_code']			= imw_real_escape_string( trim($hl7->{'FT1'.$i}('transaction_code')) );
		$charge['quantity']			= imw_real_escape_string( $hl7->{'FT1'.$i}('transaction_quantity') );
		$charge['diagnosisCodes']	= $hl7->{'FT1'.$i}('diagnosis_code', false, false, true);
		$charge['modifiers']		= $hl7->{'FT1'.$i}('procedure_code_modifier', false, false, true);

		if( is_array($charge['diagnosisCodes']) && array_key_exists('repeat', $charge['diagnosisCodes']) )
		{
			$charge['diagnosisCodes'] = $charge['diagnosisCodes']['repeat'];
			array_splice($charge['diagnosisCodes'], 12);	/*Maximum 12 diagnosis codes are allowed in imwemr*/
		}
		else
		{
			$charge['diagnosisCodes'] = array($charge['diagnosisCodes']);
		}

		/*Repeat Modifiers*/
		if( is_array($charge['modifiers']) && array_key_exists('repeat', $charge['modifiers']) )
		{
			$charge['modifiers'] = $charge['modifiers']['repeat'];
			array_splice($charge['modifiers'], 3);	/*Maximum 3 modifier codes are allowed in imwemr*/
			$charge['modifiers'] = array_values($charge['modifiers']);
		}
		else
		{
			$charge['modifiers'] = array($charge['modifiers']);
		}

		/*Get Procedure Name from imwemr*/
		$charge['cpt_description'] = '';
		$sql = 'SELECT 
					`cpt`.`cpt_desc`,
					`fee`.`cpt_fee`
				FROM
					`cpt_fee_tbl` `cpt`
				LEFT JOIN
					`cpt_fee_table` `fee`
				ON(
					`fee`.`cpt_fee_id` = `cpt`.`cpt_fee_id`
					AND `fee`.`fee_table_column_id` = 1 
				)
				WHERE
					`cpt`.`cpt_prac_code` = \''.$charge['cpt_code'].'\'
					AND `cpt`.`status` = \'Active\'
					AND `cpt`.`delete_status` = 0';
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) > 0 )
		{
			$resp = imw_fetch_assoc($resp);
			$charge['cpt_description'] = imw_real_escape_string( $resp['cpt_desc'] );
			$charge['fee_amount'] = preg_replace('/[^0-9\.]/', '', $resp['cpt_fee']);
			$charge['fee_amount'] = (double)$charge['fee_amount'];
		}
		else
		{
			throw new Exception('CPT Code `'.$charge['cpt_code'].'` does not exists in imwemr');
		}

		array_push($charges, $charge);
		array_push($cptOrder, $charge['cpt_code']);
		array_push($cptDiagnosis, $charge['diagnosisCodes'] );
	}
	if( isset($charge) )
		unset($charge);

	if( count($charges) < 1 )
	{
		throw new  Exception('No Charges detected with `transaction type` `CG` in the messge.');
	}

	$cptDiagnosis = array_reduce( $cptDiagnosis, 'array_merge', array() );
	$cptDiagnosis = array_unique($cptDiagnosis);
	/*End Charges Exrtraction*/

	/*Extract Diagnisis Codes List from DG1 segment*/
	$counDg1 = count($hl7->message['DG1']);
	$diagnosisCodes = array();
	for( $i=0; $i < $counDg1; $i++) { 
		
		$diagnosisCode = $hl7->{'DG1'.$i}('diagnosis_code');
		array_push($diagnosisCodes, $diagnosisCode);
	}
	unset($diagnosisCode);
	$diagnosisCodes = array_unique($diagnosisCodes);
	/*End Diagnisis Extraction*/

	/*Get POS*/
	$qry = 'SELECT `pos_tbl`.`pos_prac_code`
					FROM `facility`
					JOIN 
						`pos_facilityies_tbl`
						ON 
							`pos_facilityies_tbl`.`pos_facility_id` = `facility`.`fac_prac_code`
					JOIN
						`pos_tbl`
						ON
							`pos_tbl`.`pos_id` = `pos_facilityies_tbl`.`pos_id`
					WHERE
						`facility`.`id` = '.$apptFacilityId.' LIMIT 0,1';
	$resp = imw_query($qry);
	$pos = '';	/*For SuperBill entry*/
	if( $resp && imw_num_rows($resp) > 0 )
	{
		$pos = imw_fetch_assoc($resp);
		$pos = $pos['pos_prac_code'];
	}
	unset($resp);

	/*Get Group Id*/
	$gro_id = 0;
	if($apptFacilityId>0)
	{
		$qry = 'SELECT default_group
				FROM 
					facility 
				WHERE
					id = '.$apptFacilityId;
		
		$resp = imw_query($qry);

		if( $resp && imw_num_rows($resp) > 0 )
		{
			$gro_id = imw_fetch_assoc($resp);
			$gro_id = $gro_id['default_group'];
		}
		unset($resp, $qry);
	}

	if( $gro_id <= 0 )
	{
		$qry = 'SELECT default_group
				FROM
					users
				WHERE
					default_group > 0 AND id = '.$physicianId;

		$resp = imw_query($qry);

		if( $resp && imw_num_rows($resp) > 0 )
		{
			$gro_id = imw_fetch_assoc($resp);
			$gro_id = $gro_id['default_group'];
		}
		else
		{
			$qry = 'SELECT gro_id
					FROM
						groups_new
					WHERE
						group_institution = \'0\' AND del_status = 0 ORDER BY gro_id ASC';
			
			if( $resp && imw_num_rows($resp) > 0 )
			{
				$gro_id = imw_fetch_assoc($resp);
				$gro_id = $gro_id['default_group'];
			}
		}
	}

	/*Diagnosis Codes from DG1 segments, which are not present in any of FT1 segments*/
	$ftDgDiagnosisDiff = array_diff($diagnosisCodes, $cptDiagnosis);

	/*Merge diagnosis codes list from both FT1 and DG1 segments in CPT diagnosis codes list*/
	$diagnosisCodes = array_merge($cptDiagnosis, $ftDgDiagnosisDiff);	/*Final list of diagnosis codes*/
	$diagnosisCodesTemp = array_combine(range(1, count($diagnosisCodes)), array_values($diagnosisCodes));
	$diagnosisCodes = $diagnosisCodesTemp;
	unset($cptDiagnosis, $diagnosisCodesTemp);

	/*Complete List of diagnosis codes with maximum 12 indexes to be used superbill entry as serialized*/
	$diagnosisSerialized = $diagnosisCodes;
	array_splice($diagnosisSerialized, 12);
	$diagnosisSerialized = array_pad($diagnosisSerialized, 12, '');

	$diagnosisSerializedTemp = array_combine(range(1, count($diagnosisSerialized)), array_values($diagnosisSerialized));
	$diagnosisSerialized = $diagnosisSerializedTemp;
	unset($diagnosisSerializedTemp);

	$diagnosisSerialized = serialize($diagnosisSerialized);
	$diagnosisSerialized = imw_real_escape_string($diagnosisSerialized);

	/*Crate New SuperBill*/
	$sql = 'INSERT INTO 
				`superbill`
			SET
				`patientId` = '.$patientId.', `physicianId` = '.$physicianId.',
				`encounterId` = '.$encounterId.', `timeSuperBill` = \''.$apptTime.'\', `dateOfService` = \''.$apptDate.'\',
				`patientStatus` = \'Active\', `sch_app_id` = '.$appointmentId.',
				`primary_provider_id_for_reports` = \''.$physicianId.'\', `hl7_batch_id` = \''.$transactionBatchId.'\',
				`pos` = \''.$pos.'\', `arr_dx_codes` = \''.$diagnosisSerialized.'\', `gro_id` = \''.$gro_id.'\', `sup_icd10`=1,
				 `insuranceCaseId` = '.$insuranceCaseId;
	$insColsAdded = array();    /*Container to keep record of insurance columns already added to query*/
	/*Insurance Fields for the superbill*/
	foreach ( $insuranceProvider as $index=>$id )
	{
		$colName = ( $insuranceType[$index] == 'primary' ) ? 'pri_ins_id' : 'sec_ins_id';
		if( in_array($colName, $insColsAdded) )
		{
			continue;
		}

		$sql .= ', `'.$colName.'` = '.(int)$id;
		array_push($insColsAdded, $colName);
	}
	unset($index, $id, $insColsAdded);

	/*End Insurance Fields for the superbill*/

	$resp = imw_query($sql);
	$superBillId = false;
	if( $resp )
	{
		$superBillId = imw_insert_id();
	}

	if( $superBillId === false )
	{
		throw new Exception('Unable to create superbill in imwemr', 2);
	}
	unset($physicianId, $encounterId, $appointmentId, $transactionBatchId, $pos, $diagnosisSerialized);
	// `procOrder`

	$procedureOrder = array();	/*Container to hold appearence order of the CPT codes*/
	$tos = false;	/*Container for TOS value*/
	$cptFee = array();	/*Container to hold fee value for the CPT code*/
	/*Add Procedures/Charges - Tranverse through each transaction code and entry in procedureinfo table against the superbill id*/
	foreach( $charges as $charge )
	{
		$sql = 'INSERT INTO
					`procedureinfo`
				SET
					`description` = \''.$charge['cpt_description'].'\', `procedureName` = \''.$charge['cpt_description'].'\',
					`cptCode` = \''.$charge['cpt_code'].'\', `idSuperBill` = '.$superBillId.', `units` = \''.$charge['quantity'].'\',
					`hl7_transaction_id` = \''.$charge['transactionId'].'\'';
		$fieldsAdded = array();

		/*Add Modifiers*/
		for( $i = 0; $i < 3; $i++ )
		{
			if( array_key_exists($i, $charge['modifiers']) )
			{
				$sql .= ', `modifier'.($i+1).'` = \''.($charge['modifiers'][$i]).'\'';
			}
		}

		foreach ( $charge['diagnosisCodes'] as $daignosisCode )
		{
			$diagnosisPosition = array_search($daignosisCode, $diagnosisCodes);
			$diagnosisPosition = $diagnosisPosition;

			if( in_array('dx'.$diagnosisPosition, $fieldsAdded) )
			{
				continue;
			}
			
			$sql .= ', `dx'.$diagnosisPosition.'` = \''.$daignosisCode.'\'';

			array_push($fieldsAdded, 'dx'.$diagnosisPosition);
		}
		imw_query($sql);
		unset($fieldsAdded);

		/*Data to be updated on superbill table*/
		array_push($procedureOrder, $charge['cpt_code']);

		/*Get TOS based on CPT code*/
		if( $tos === false )
		{
			$sql = 'SELECT
						TRIM(`tos`.`tos_prac_cod`) AS \'tos_prac_cod\'
					FROM
						`tos_tbl` `tos`
					INNER JOIN
						`cpt_fee_tbl` `fee`
					ON(
						`tos`.`tos_id` = `fee`.`tos_id`
					)
					WHERE 
						`fee`.`cpt_prac_code` = \''.$charge['cpt_code'].'\'
						AND `fee`.`delete_status` = 0
						AND `fee`.`status` = \'Active\'
						AND TRIM(`tos`.`tos_prac_cod`) != \'\'
					LIMIT 1';
			$resp = imw_query($sql);
			if( $resp && imw_num_rows($resp) > 0 )
			{
				$tos = imw_fetch_assoc($resp);
				$tos = $tos['tos_prac_cod'];
			}
			unset($resp);
		}
		array_push($cptFee, $charge['fee_amount']);	/*Collect CPT Fee value to update in superbill table*/
	}

	/*Get TOS on the basis of Headquarter TOS - if unable to locate on the basis of CPT code*/
	if( $tos === false )
	{
		$sql = 'SELECT `tos_prac_cod` FROM `tos_tbl` WHERE `headquarter` = 1';
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) > 0 )
		{
			$tos = imw_fetch_assoc($resp);
			$tos = $tos['tos_prac_cod'];
		}
		unset($resp);
	}

	/*Set default value for TOS - if unable to locate on the basis of CPT Code and Headquarter*/
	if( $tos === false )
	{
		$tos = '1';
	}

	$cptFee = array_sum($cptFee);	/*Sum CPT fee to calculate total charges*/

	/*Update TOS and procedure order in superbill*/
	$procedureOrder = implode(',', $procedureOrder);
	$sql = 'UPDATE
				`superbill`
			SET 
				`procOrder` = \''.$procedureOrder.'\',
				`tos` = \''.$tos.'\',
				`todaysCharges` = \''.$cptFee.'\'
			WHERE
				`idSuperBill` = '.$superBillId;
	imw_query($sql);
	unset($charges, $tos, $superBillId, $cptFee, $sql);

	if( isset($charge) )
		unset($charge);
