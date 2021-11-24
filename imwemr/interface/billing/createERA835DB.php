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
*/
$date_time = date('Y-m-d H:i:s'); 
$CLPStringsArray = array();
$getFileContentsStr = "SELECT id, file_contents, file_name FROM electronicfiles_tbl WHERE id = '$electronicFilesTblId'";
$getFileContentsQry = imw_query($getFileContentsStr);
$getFileContentsRow = imw_fetch_array($getFileContentsQry);
	$eFileId = $getFileContentsRow['id'];
	$file_contents = era_separator_replace($getFileContentsRow['file_contents']);	
	$file_name = $getFileContentsRow['file_name'];
	$strLength = strlen($file_contents);	
	$content = explode('~',$file_contents);
	$contents = array();
	for($i=0;$i<count($content);$i++){
		$arr = explode('*',$content[$i]);
		$arr[0] = preg_replace('/\s+/','',$arr[0]);
		$arr[1] = preg_replace('/\s+/','',$arr[1]);
		$contents[] = implode('*',$arr);
	}
	$file_contents = implode('~',$contents);
	$BPRStrArr = explode('~BPR*', $file_contents);
	//--------------- GET BPR STRING LINE
	$BPRStringArr = array();	
	foreach($BPRStrArr as $key => $strValue){
		if(strpos($strValue, '*')==1){
			$strValue = 'BPR*'.$strValue;
		}
		if($key==0){ 
			$headerStr = $strValue; 
			$BPRStringArr[] = $strValue; 
		}else{
			$BPRStringArr[] = $strValue;
		}
	}
	
	$lxStrArray = array();
	foreach($BPRStringArr as $BPRStringKey => $BPRStringValue){
		$lxStrArray[] = explode('~LX', $BPRStringValue);
	}

	for($i=0; $i<count($lxStrArray); $i++){
		foreach($lxStrArray[$i] as $LXStringKey => $LXStringValue){
			if($LXStringKey==1){
				$lxStrArray[$i][$LXStringKey] = 'LX'.$LXStringValue;
			}
		}
	}
	for($i=0; $i<count($lxStrArray); $i++){
		for($j=0; $j<count($lxStrArray[$i]); $j++){
			if($j>0){
				$CLPStringsArray[$i][$j] = explode('~CLP', $lxStrArray[$i][$j]);
			}else{
				$CLPStringsArray[$i][$j] = $lxStrArray[$i][$j];
			}
		}
	}
	
	for($i=0; $i<count($CLPStringsArray); $i++){
		for($j=0; $j<count($CLPStringsArray[$i]); $j++){
			if(is_array($CLPStringsArray[$i][$j])){
				foreach($CLPStringsArray[$i][$j] as $clpStrKey => $clpStrValue){
					if($clpStrKey>0){
						$CLPStringsArray[$i][$j][$clpStrKey]='CLP'.$clpStrValue;
					}
				}
			}
		}
	}
	$BPRSEGMENT = array();
	for($i=0; $i<count($CLPStringsArray); $i++){
		for($j=0; $j<count($CLPStringsArray[$i]); $j++){
			if(($i>0) && ($j==0)){
				//BPR SEGMENT
					$BPRSEGMENT[$i] = explode("~", $CLPStringsArray[$i][$j]);
				//
			}
		}
	}
	$Arr = array();
	$arrCLP = $CLPStringsArray;
	for($i=0; $i<count($CLPStringsArray); $i++){
		if(is_array($CLPStringsArray[$i])){
			for($j=0; $j<count($CLPStringsArray[$i]); $j++){
				if(is_array($CLPStringsArray[$i][$j])){
					for($k=0; $k<count($CLPStringsArray[$i][$j]); $k++){
						$Arr[$i][] = explode("~SVC*", $CLPStringsArray[$i][$j][$k]);
						$Arr_key_chk = $i;						
					}
				}
			}
		}
	}
	$BPRSEGMENT2=array();
	for($i=0; $i<=count($BPRSEGMENT); $i++){
		if(is_array($BPRSEGMENT[$i])){
			$BPRSEGMENT_imp=implode(',',$BPRSEGMENT[$i]);
			if(stripos($BPRSEGMENT_imp,'REF*F2')>0 || stripos($BPRSEGMENT_imp,'REF*EO')>0){
				$bpr_i=0;
				for($jk=0; $jk<=count($BPRSEGMENT[$i]); $jk++){
					if(stripos($BPRSEGMENT[$i][$jk],'REF*F2') === false && stripos($BPRSEGMENT[$i][$jk],'REF*EO') === false){
						$BPRSEGMENT[$i][$bpr_i] = $BPRSEGMENT[$i][$jk];
						$bpr_i++;
					}
				}
			}	
			for($j=0; $j<=count($BPRSEGMENT[$i]); $j++){
				$ref_pos1=stripos($BPRSEGMENT[$i][2],'REF*');
				$segmentValue=$BPRSEGMENT[$i][$j];
				if($j==2 && $ref_pos1 === false){
					$BPRSEGMENT2[$i][]="REF*EV*";
				}
				if($ref_pos1 === false){
					$dtm_pos1=stripos($BPRSEGMENT[$i][2],'DTM*');
					if($j==2 && $dtm_pos1 === false){
						$BPRSEGMENT2[$i][]="DTM*";
					}
					$per_pos1=stripos($BPRSEGMENT[$i][7],'PER*');
					if($j==7 && $per_pos1 === false){
						$BPRSEGMENT2[$i][]="PER*CX*";
					}
				}else{
					$dtm_pos1=stripos($BPRSEGMENT[$i][3],'DTM*');
					if($j==3 && $dtm_pos1 === false){
						$BPRSEGMENT2[$i][]="DTM*";
					}
					$per_pos1=stripos($BPRSEGMENT[$i][8],'PER*');
					if($j==8 && $per_pos1 === false){
						$BPRSEGMENT2[$i][]="PER*CX*";
					}
				}
				$second_ref=stripos($segmentValue,'REF*EO*');
				$second_per=stripos($segmentValue,'PER*BL*');
				if($segmentValue!="" && $second_ref === false && $second_per === false){
					$BPRSEGMENT2[$i][]=$segmentValue;
				}
			}
			
		}
	}
	$BPRSEGMENT=$BPRSEGMENT2;
	//print '<pre>';
	//print_r($BPRSEGMENT);
	//exit;
	$tbl835IdArr = array();
	for($i=0; $i<=count($BPRSEGMENT); $i++){
		if(is_array($BPRSEGMENT[$i])){			
			for($j=0; $j<=count($BPRSEGMENT[$i]); $j++){
				$segmentValue = $BPRSEGMENT[$i][$j];
				if(count($BPRSEGMENT[$i])==14){
					$chk_cont=13;
				}else{
					$chk_cont=12;
				}
				switch ($j){
					case 0:
						$BPRElementsArr = explodeFunction($segmentValue);
						$insertStr = "INSERT INTO era_835_details SET 835_Era_Id = NULL, electronicFilesTblId = '$eFileId'";
						$insertQry = imw_query($insertStr);
						$tbl835Id = imw_insert_id();
						$tbl835IdArr[] = $tbl835Id;
						$BPR_segment_Line = $BPRElementsArr[1].' '.$BPRElementsArr[2].' '.$BPRElementsArr[3].' '.$BPRElementsArr[4].' '.$BPRElementsArr[5].' '.$BPRElementsArr[16];
						$update835TblStr = "UPDATE era_835_details SET BPR_segment = '$BPR_segment_Line' WHERE 835_Era_Id = '$tbl835Id'";
						$update835TblQry = imw_query($update835TblStr);
							$updateRecord835Tbl['action_code'] = $BPRElementsArr[1];
							$updateRecord835Tbl['provider_payment_amount'] = $BPRElementsArr[2];
							$updateRecord835Tbl['flag_code'] = $BPRElementsArr[3];
							$updateRecord835Tbl['payment_method_code'] = $BPRElementsArr[4];
							$effectiveDate = $BPRElementsArr[16];
							if($BPRElementsArr[16]=="DA"){
								$effectiveDate = $BPRElementsArr[18].$BPRElementsArr[19].$BPRElementsArr[20];
							}
							if($effectiveDate=="" or $effectiveDate<=0){
								$effectiveDate=substr(trim($segmentValue),-8);
							}
								$yy = substr($effectiveDate,0,4);
								$mm = substr($effectiveDate,4,2);
								$dd = substr($effectiveDate,6,2);	
								$effectiveDate = $yy.'-'.$mm.'-'.$dd;					 
							$updateRecord835Tbl['chk_issue_EFT_Effective_date'] = $effectiveDate;
							$updateRecord835Tbl['sender_bank_acc'] = $BPRElementsArr[9];
					break;
					case 1:
						$TRNElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['TRN_trace_numbers'] = $TRNElementsArr[1];
						$chk_issue_EFT_Effective_date_rep=str_replace('-','',trim($updateRecord835Tbl['chk_issue_EFT_Effective_date']));
						$updateRecord835Tbl['TRN_payment_type_number'] = str_replace($chk_issue_EFT_Effective_date_rep,'',$TRNElementsArr[2]);
						++$transactionCount;	
						$updateRecord835Tbl['TRN_orignating_company_id'] = $TRNElementsArr[3];						
					break;
					case 2:
						$REFElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['REF_receiver_reference_id']= $REFElementsArr[1].', '.$REFElementsArr[2] ;
					break;
					case 3:
						$DTMElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['DTM']= $DTMElementsArr[1];
						$production_date = $DTMElementsArr[2];
							$yy = substr($production_date,0,4);
							$mm = substr($production_date,4,2);
							$dd = substr($production_date,6,2);
							$production_date = $yy.'-'.$mm.'-'.$dd;
						$updateRecord835Tbl['DTM_production_date'] = $production_date;
					break;
					case 4:
						$N1ElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['N1_payer_name'] = $N1ElementsArr[2];
					break;					
					case 5:
						$N3ElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['N3_payer_address']= $N3ElementsArr[1];
					break;
					case 6:
						$N4ElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['N4_payer_city'] = $N4ElementsArr[1];
						$updateRecord835Tbl['N4_payer_state'] = $N4ElementsArr[2];
						$updateRecord835Tbl['N4_payer_zip'] = $N4ElementsArr[3];				
					break;
					case 7:
						//REF
						$REFElementsArr = explodeFunction($segmentValue);
						$updateRecord835Tbl['REF_provider_ref_id'] = $REFElementsArr[2];
					break;
					case 8:
						if(stristr($segmentValue,'PER*')){
							//PER
							$PERElementsArr = explodeFunction($segmentValue);					
							$updateRecord835Tbl['contact_fn_code'] = $PERElementsArr[1];
							$updateRecord835Tbl['PER_payer_office_name'] = $PERElementsArr[2];
							$updateRecord835Tbl['PER_payer_office_phone'] = $PERElementsArr[4];
						}
						if(stristr($segmentValue,'N1*')){
							//N1Payee
							$N1PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N1_payee_name'] = $N1PayeeElementsArr[2];
							$updateRecord835Tbl['N1_payee_id'] = $N1PayeeElementsArr[4];	
						}
					break;
					case 9:
						if(stristr($segmentValue,'N1*')){
							//N1Payee
							$N1PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N1_payee_name'] = $N1PayeeElementsArr[2];
							$updateRecord835Tbl['N1_payee_id'] = $N1PayeeElementsArr[4];
						}
						if(stristr($segmentValue,'N3*')){
							//N3Payee
							$N3PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N3_payee_address'] = $N3PayeeElementsArr[1].', '.$N3PayeeElementsArr[2];
						}
					break;
					case 10:
						if(stristr($segmentValue,'N3*')){
							//N3Payee
							$N3PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N3_payee_address'] = $N3PayeeElementsArr[1].', '.$N3PayeeElementsArr[2];
						}
						if(stristr($segmentValue,'N4*')){
							//N4Payee
							$N4PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N4_payee_city'] = $N4PayeeElementsArr[1];
							$updateRecord835Tbl['N4_payee_state'] = $N4PayeeElementsArr[2];
							$updateRecord835Tbl['N4_payee_zip'] = $N4PayeeElementsArr[3];
						}
					break;
					case 11:
						if(stristr($segmentValue,'N4*')){
							//N4Payee
							$N4PayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['N4_payee_city'] = $N4PayeeElementsArr[1];
							$updateRecord835Tbl['N4_payee_state'] = $N4PayeeElementsArr[2];
							$updateRecord835Tbl['N4_payee_zip'] = $N4PayeeElementsArr[3];
						}
						if(stristr($segmentValue,'REF*')){
							//REFPayeeAddInfo
							$REFPayeeAddInfoPayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['REF_payee_add_info'] = $REFPayeeAddInfoPayeeElementsArr[1].', '.$REFPayeeAddInfoPayeeElementsArr[2];
						}
					break;
					case 12:
						if(stristr($segmentValue,'REF*')){
							//REFPayeeAddInfo
							$REFPayeeAddInfoPayeeElementsArr = explodeFunction($segmentValue);
							$updateRecord835Tbl['REF_payee_add_info'] = $REFPayeeAddInfoPayeeElementsArr[1].', '.$REFPayeeAddInfoPayeeElementsArr[2];
						}
					break;
				}
				if(count($updateRecord835Tbl)>0){
					foreach($updateRecord835Tbl as $fieldName => $fieldValue){
						$update835TblStr = "UPDATE era_835_details SET $fieldName = '$fieldValue' WHERE `835_Era_Id` = '$tbl835Id'";
						$update835TblQry = imw_query($update835TblStr);
					}
				}
			}
		}
	}
	unset($updateRecord835Tbl);
	$for_arr = count($Arr);
	if($Arr_key_chk>count($Arr)){
		$for_arr=$Arr_key_chk;
	}
	for($i=1; $i<=$for_arr; $i++){
		$x = $i-1;
		$tbl835Id = $tbl835IdArr[$x];
		if(is_array($Arr[$i])){
			for($j=1; $j<=count($Arr[$i]); $j++){
				$h = $j-1;
				$segmentValue = $BPRSEGMENT[$i][$h];
				//======================================================================================================================
					$headerStrARR = explode("~", $headerStr);
					foreach($headerStrARR as $headerStrKey => $headerStrSegmentStr){
						switch ($headerStrKey){
							case 0:
								// ISA
								$ISAElementsArr = explodeFunction($headerStrSegmentStr);
								$updateRecord835Tbl['method_of_code_stu'] = $ISAElementsArr[5];
								$updateRecord835Tbl['Interchange_sender_id'] = $ISAElementsArr[6];
								$updateRecord835Tbl['id_qualifier'] = $ISAElementsArr[7];				
								$updateRecord835Tbl['submitter_id'] = $ISAElementsArr[8];
									$interchange_date = $ISAElementsArr[9];
										$yy = substr($interchange_date,0,2);
										$mm = substr($interchange_date,2,2);
										$dd = substr($interchange_date,4,2);
										$interchange_date = $yy.'-'.$mm.'-'.$dd;
									$interchange_time = $ISAElementsArr[10];
										$hh = substr($interchange_time,0,2);
										$mm = substr($interchange_time,2,2);
										$interchange_time = $hh.':'.$mm;
								$updateRecord835Tbl['interchange_date_time'] = $interchange_date." ".$interchange_time;
								$updateRecord835Tbl['standard_Identifier_control_code'] = $ISAElementsArr[11];
								$updateRecord835Tbl['version_number_of_segments'] = $ISAElementsArr[12];
								$updateRecord835Tbl['interchange_sender_no'] = $ISAElementsArr[13];
								$updateRecord835Tbl['interchange_sender_control_no'] = $ISAElementsArr[14];
								$updateRecord835Tbl['usage_indicator'] = $ISAElementsArr[15];
								break;
							case 1:
								//GS
								$GSElementsArr = explodeFunction($headerStrSegmentStr);
								$updateRecord835Tbl['receiver_code'] = $GSElementsArr[2];
								$updateRecord835Tbl['submitter_code'] = $GSElementsArr[3];
									$batch_date = $GSElementsArr[4];
										$yy = substr($batch_date,0,2);
										$mm = substr($batch_date,2,2);
										$dd = substr($batch_date,4,2);	
										$batch_date = $mm.'-'.$dd.'-'.$yy;
									$batch_time = $GSElementsArr[5];
										$hh = substr($batch_time,0,2);
										$mm = substr($batch_time,2,2);
										$batch_time = $hh.':'.$mm;
									$updateRecord835Tbl['batch_date_time'] = $batch_date.' '.$batch_time;
									$updateRecord835Tbl['unique_control_identifier'] = $GSElementsArr[6];
									$X12_standard1 = $GSElementsArr[7];
									$X12_standard2 = $GSElementsArr[8];
									$updateRecord835Tbl['X12_standard'] = $X12_standard1.", ".$X12_standard2;				
								break;
							case 2:
								//ST
								$STElementsArr = explodeFunction($headerStrSegmentStr);
								$updateRecord835Tbl['835_Identifying_control_number'] = $STElementsArr[1].", ".$STElementsArr[2];
								break;
						}
					}
				//======================================================================================================================				
				if(count($updateRecord835Tbl)>0){
					foreach($updateRecord835Tbl as $fieldName => $fieldValue){
						$update835TblStr = "UPDATE era_835_details SET $fieldName = '$fieldValue' WHERE 835_Era_Id = '$tbl835Id'";
						$update835TblQry = imw_query($update835TblStr);
					}
				}
				if(is_array($Arr[$i][$j])){
					for($k=0; $k<count($Arr[$i][$j]); $k++){
						if(($j>0) && ($k == 0)){
							//$Arr[$i][$j][$k] = @preg_replace('/\s+/','',$Arr[$i][$j][$k]);
							$clpTiledExplode = explode("~", $Arr[$i][$j][$k]);
							foreach($clpTiledExplode as $clpTiledExplodeKey => $clpTiledExplodeVal){
								if(substr($clpTiledExplodeVal, 0, 6) != 'NM1*TT'){
									$clpTiledExplode[$clpTiledExplodeKey] = preg_replace('/\s+/','',$clpTiledExplodeVal);									
								}else{
									$clpTiledExplode[$clpTiledExplodeKey] = $clpTiledExplodeVal;
								}
							}
							foreach($clpTiledExplode as $segKey => $segStr){
								$segStr = trim($segStr);
								if($segKey==0){
									$starSepratedCLPValArr = explodeFunction($segStr);
									$clpInsertStr = "INSERT INTO era_835_patient_details SET
													835_Era_Id = '$tbl835Id',
													CLP_claim_submitter_id = '$starSepratedCLPValArr[1]',
													CLP_claim_status = '$starSepratedCLPValArr[2]',
													CLP_total_claim_charge = '$starSepratedCLPValArr[3]',
													CLP_claim_payment_amount = '$starSepratedCLPValArr[4]',
													CLP_claim_patient_res_amt = '$starSepratedCLPValArr[5]',
													CLP_payer_claim_control_number = '$starSepratedCLPValArr[7]'";
									$clpInsertQry = imw_query($clpInsertStr);
									$clpInsertID = imw_insert_id();
								}else{
									$starSepratedCLPDetailsValArr = explodeFunction($segStr);
									//print '<pre>';
									//print_r($starSepratedCLPDetailsValArr);
									foreach($starSepratedCLPDetailsValArr as $segCLPDetailsKey => $segCLPDetailsStr){
										
										if($segCLPDetailsKey==0){
											switch($segCLPDetailsStr){
												case 'CAS':													
													$ClpCasReasonType = $starSepratedCLPDetailsValArr[1];
													$ClpCasReasonCode = $starSepratedCLPDetailsValArr[2];
													$ClpCasReasonAmt = $starSepratedCLPDetailsValArr[3];
													//echo $clpInsertID.' = '.$ClpCasReasonType.' = '.$ClpCasReasonCode.' = '.$ClpCasReasonAmt.'<br>';
													$updateClpCasStr = "INSERT INTO era835clpcas SET
																		ERAPatientdetailsId = '$clpInsertID',
																		era835Id = '$tbl835Id',
																		casReasonType = '$ClpCasReasonType',
																		casReasonCode = '$ClpCasReasonCode',
																		casReasonAmt = '$ClpCasReasonAmt'";
													$updateClpCasQry = imw_query($updateClpCasStr);
													break;
												case 'NM1':
													$NM1Type = $starSepratedCLPDetailsValArr[1];
													$NM1PatCount = $starSepratedCLPDetailsValArr[2];
													$patientLastName = addslashes($starSepratedCLPDetailsValArr[3]);
													$patientFirstName = addslashes($starSepratedCLPDetailsValArr[4]);
													$patientMiddleName = addslashes($starSepratedCLPDetailsValArr[5]);
													$patientSuffixName = $starSepratedCLPDetailsValArr[7];
													$codeStru = $starSepratedCLPDetailsValArr[8];
													$patientAccountNo = $starSepratedCLPDetailsValArr[9];
													$patientName = $patientFirstName.' '.$patientMiddleName.' '.$patientLastName;
													$insertStr = "INSERT INTO era_835_nm1_details SET
																	ERA_patient_details_id = '$clpInsertID',
																	NM1_type = '$NM1Type',
																	NM1_entity_type_qualifier = '$NM1PatCount',
																	NM1_last_name = '$patientLastName',
																	NM1_first_name = '$patientFirstName',
																	NM1_middle_name = '$patientMiddleName',
																	NM1_name_suffix = '$patientSuffixName',
																	NM1_method_code_stru = '$codeStru',
																	NM1_patient_id = '$patientAccountNo'";
													$insertQry = imw_query($insertStr);
												break;
												case 'MOA':
													$MOA1 = $starSepratedCLPDetailsValArr[3];
													$MOA2 = $starSepratedCLPDetailsValArr[4];
													$MOA3 = $starSepratedCLPDetailsValArr[5];
													if($MOA2){
														$MOAQualifier = $MOA1.', '.$MOA2;
													}else{
														$MOAQualifier = $MOA1;
													}
													if($MOA3){
														$MOAQualifier = $MOAQualifier.', '.$MOA3;
													}
													$updateMOAStr = "UPDATE era_835_patient_details SET
																		MOA_qualifier = '$MOAQualifier'
																		WHERE ERA_patient_details_id = '$clpInsertID'";
													$updateMOAQry = imw_query($updateMOAStr);
												break;
												case 'DTM':
													$DTMType = $starSepratedCLPDetailsValArr[1];
													$DTMDate = $starSepratedCLPDetailsValArr[2];
														$yy = substr($DTMDate,0,4);
														$mm = substr($DTMDate,4,2);
														$dd = substr($DTMDate,6,2);
													$DTMDate = $yy.'-'.$mm.'-'.$dd;
													$updateClpTblStr = "UPDATE era_835_patient_details SET
																		DTM_qualifier = '$DTMType',
																		DTM_date = '$DTMDate'
																		WHERE ERA_patient_details_id = '$clpInsertID' and DTM_date='0000-00-00' and DTM_qualifier=''";
													$updateClpTblQry = imw_query($updateClpTblStr);	
													if($DTMType=="232" && $DTMDate!="" && $DTMDate!="0000-00-00"){
														$svc_dtm_arr[$clpInsertID]=$DTMDate;
													}												
												break;
												case 'SVC':
													// GET CHECH IS INFORMATIONAL OR NOT
													$getChkDetailsQry = imw_query("SELECT TRN_payment_type_number FROM era_835_details WHERE `835_Era_Id` = '$tbl835Id'");
													$getChkDetailsRow = imw_fetch_assoc($getChkDetailsQry);
													$TRNPaymentTypeNumber = $getChkDetailsRow['TRN_payment_type_number'];
													$insertProcInfoStr = "INSERT INTO era_835_proc_details SET
																			835_Era_Id = '$tbl835Id',
																			ERA_patient_details_id = '$clpInsertID',
																			SVC_product_qualifier = '$product_qualifier',
																			SVC_proc_code = '$cpt_code',
																			unit = '$unit',
																			SVC_mod_code = '$mod',
																			SVC_proc_charge = '$cpt_charges',
																			SVC_provider_pay_amt = '$cpt_pay_charges',
																			units_service_paid = '$unit_ser_paid',
																			SVC_proc_unit = '$cpt_units'";

													if((substr($TRNPaymentTypeNumber, 0, 3) == '353')){
														//$insertProcInfoStr.= ", postedStatus = 'Informational'";										
													}
													$insertProcInfoQry = imw_query($insertProcInfoStr);
													$procInsertId = imw_insert_id();												

													// GET PROC ID FROM CPT4_CODE
													$getCptFeeQry = imw_query("SELECT * FORM cpt_fee_tbl WHERE cpt_prac_code = '$cpt_code'");
													$getCptFeeRows = imw_fetch_assoc($getCptFeeQry);
													$cptCatId = $getCptFeeRows['cpt_cat_id'];
														$getCptCatPQRIQry = imw_query("SELECT * FORM cpt_category_tbl WHERE cpt_cat_id='$cptCatId'");
														$getCptCatPQRIRows = imw_fetch_assoc($getCptCatPQRIQry);
														$cptCategory = $getCptCatPQRIRows['cpt_category'];
														if($cptCategory == 'PQRI'){
															$proc_insert_id[] = $procInsertId;
														}
													// GET PROC ID FROM CPT4_CODE
												break;												
											}
										}
									}
								}
							}							
						}else if($k>0){
							$Arr[$i][$j][$k] = @preg_replace('/\s+/','',$Arr[$i][$j][$k]);							
							$Arr[$i][$j][$k] = 'SVC*'.$Arr[$i][$j][$k];
							$svcTiledExplode = explode("~", $Arr[$i][$j][$k]);
							foreach($svcTiledExplode as $svcTiledExplodeKey => $svcTiledExplodeval){
								$svcTiledExplode[$svcTiledExplodeKey] = @preg_replace('/\s+/','',$svcTiledExplodeval);
							}
							$CAS_type = '';
							$CAS_amount = '';
							$CAS_reason_code = '';
							$REF_type = '';
							$REF_prov_id = '';
							foreach($svcTiledExplode as $segSVCKey => $segSVCStr){
								$segSVCStr = trim($segSVCStr);
								if($segSVCKey==0){
									$starSepratedSCVValArr = explodeFunction($segSVCStr);
									$procInfoStr = $starSepratedSCVValArr[1];
									$cpt_charges = $starSepratedSCVValArr[2];
									$cpt_pay_charges = $starSepratedSCVValArr[3];
									$unit = $starSepratedSCVValArr[5];
									$unit_ser_paid = $starSepratedSCVValArr[5];
									$cpt_units = $starSepratedSCVValArr[7];
									if(strstr($procInfoStr,"|")){
										$procInfoStrArr = explode("|", $procInfoStr);
									}else{
										$procInfoStrArr = explode(":", $procInfoStr);
									}
									$product_qualifier = $procInfoStrArr[0];
									$cpt_code = $procInfoStrArr[1];
									if($procInfoStrArr[2]){
										if($procInfoStrArr[3]){
											$mod = $procInfoStrArr[2].', '.$procInfoStrArr[3];
										}else{
											$mod = $procInfoStrArr[2];
										}
									}else{
										$mod = '';
									}
									// GET CHECH IS INFORMATIONAL OR NOT
									$getChkDetailsQry = imw_query("SELECT TRN_payment_type_number FROM era_835_details WHERE `835_Era_Id` = '$tbl835Id'");
									$getChkDetailsRow = imw_fetch_assoc($getChkDetailsQry);
									$TRNPaymentTypeNumber = $getChkDetailsRow['TRN_payment_type_number'];
									$insertProcInfoStr = "INSERT INTO era_835_proc_details SET
															835_Era_Id = '$tbl835Id',
															ERA_patient_details_id = '$clpInsertID',
															SVC_product_qualifier = '$product_qualifier',
															SVC_proc_code = '$cpt_code',
															unit = '$unit',
															SVC_mod_code = '$mod',
															SVC_proc_charge = '$cpt_charges',
															SVC_provider_pay_amt = '$cpt_pay_charges',
															units_service_paid = '$unit_ser_paid',
															SVC_proc_unit = '$cpt_units'";
									/*
									if((substr($TRNPaymentTypeNumber, 0, 3) == '353')){
										$insertProcInfoStr.= ", postedStatus = 'Informational'";
									}
									*/
									$insertProcInfoQry = imw_query($insertProcInfoStr);
									$procInsertId = imw_insert_id();
										// GET PROC ID FROM CPT4_CODE
										$getCptFeeQry = imw_query("SELECT * FROM cpt_fee_tbl WHERE cpt_prac_code = '$cpt_code' AND delete_status = '0'");
										$getCptFeeRows = imw_fetch_assoc($getCptFeeQry);
										$cptCatId = $getCptFeeRows['cpt_cat_id'];
											$getCptCatPQRIQry = imw_query("SELECT * FROM cpt_category_tbl WHERE cpt_cat_id='$cptCatId'");
											$getCptCatPQRIRows = imw_fetch_assoc($getCptCatPQRIQry);
											$cptCategory = $getCptCatPQRIRows['cpt_category'];
											if($cptCategory == 'PQRI'){
												$proc_insert_id[] = $procInsertId;
											}
										// GET PROC ID FROM CPT4_CODE
									
								}else{
									$starSepratedSVCDetailsValArr = explodeFunction($segSVCStr);
									foreach($starSepratedSVCDetailsValArr as $segSVCDetailsKey => $segSVCDetailsStr){										
										if($segSVCDetailsKey==0){
											switch($segSVCDetailsStr){
												case 'DTM':
													$DTMType = $starSepratedSVCDetailsValArr[1];
													$DTMDate = $starSepratedSVCDetailsValArr[2];
														$yy = substr($DTMDate,0,4);
														$mm = substr($DTMDate,4,2);
														$dd = substr($DTMDate,6,2);
													$DTMDate = $yy.'-'.$mm.'-'.$dd;
													$updateSVCTblStr = "UPDATE era_835_proc_details SET
																		DTM_type = '$DTMType',
																		DTM_date = '$DTMDate'
																		WHERE 835_Era_proc_Id = '$procInsertId'";
													$updateSVCTblQry = imw_query($updateSVCTblStr);
												break;
												
												case 'CAS':												
													if($starSepratedSVCDetailsValArr[1] == 'CO' && $starSepratedSVCDetailsValArr[2] == '45'){														
														$CASAMOUNT = $starSepratedSVCDetailsValArr[3];
														$pat[$starSepratedCLPValArr[1]] = $CASAMOUNT;
														$proc_insert_id[] = $procInsertId;
													}
													if($CAS_type==''){
														$CAS_type = $starSepratedSVCDetailsValArr[1];
													}else{
														$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[1];
													}
													if($CAS_reason_code==''){
														$CAS_reason_code = $starSepratedSVCDetailsValArr[2];
													}else{
														$CAS_reason_code = $CAS_reason_code.', '.$starSepratedSVCDetailsValArr[2];
													}
													if($CAS_amount==''){
														$CAS_amount = $starSepratedSVCDetailsValArr[3];
													}else{
														$CAS_amount = $CAS_amount.', '.$starSepratedSVCDetailsValArr[3];
													}
													if($starSepratedSVCDetailsValArr[5]>0){
														if($starSepratedSVCDetailsValArr[4]==""){
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[1];
														}else{
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[4];
														}
														$CAS_reason_code = $CAS_reason_code.', '.$starSepratedSVCDetailsValArr[5];
														$CAS_amount = $CAS_amount.', '.$starSepratedSVCDetailsValArr[6];
													}
													if($starSepratedSVCDetailsValArr[8]>0){
														if($starSepratedSVCDetailsValArr[7]==""){
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[1];
														}else{
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[7];
														}
														$CAS_reason_code = $CAS_reason_code.', '.$starSepratedSVCDetailsValArr[8];
														$CAS_amount = $CAS_amount.', '.$starSepratedSVCDetailsValArr[9];
													}
													if($starSepratedSVCDetailsValArr[11]>0){
														if($starSepratedSVCDetailsValArr[10]==""){
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[1];
														}else{
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[10];
														}
														$CAS_reason_code = $CAS_reason_code.', '.$starSepratedSVCDetailsValArr[11];
														$CAS_amount = $CAS_amount.', '.$starSepratedSVCDetailsValArr[12];
													}
													if($starSepratedSVCDetailsValArr[14]>0){
														if($starSepratedSVCDetailsValArr[13]==""){
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[1];
														}else{
															$CAS_type = $CAS_type.', '.$starSepratedSVCDetailsValArr[13];
														}
														$CAS_reason_code = $CAS_reason_code.', '.$starSepratedSVCDetailsValArr[14];
														$CAS_amount = $CAS_amount.', '.$starSepratedSVCDetailsValArr[15];
													}
													if($starSepratedSVCDetailsValArr[6]!=''){
														$coinsAmt = 0;
													}else{
														$coinsAmt = 0;
													}
													$updateSVCCASTblStr = "UPDATE era_835_proc_details SET
																			CAS_type = '$CAS_type',
																			CAS_reason_code = '$CAS_reason_code',
																			CAS_amt = '$CAS_amount',
																			CAS_CoinsAmt = '$coinsAmt'
																			WHERE 835_Era_proc_Id = '$procInsertId'";
													$updateSVCCASTblQry = imw_query($updateSVCCASTblStr);
												break;
												case 'REF':
													if($starSepratedSVCDetailsValArr[1]=="6R" || $starSepratedSVCDetailsValArr[1]=="RB"){
														$REF_type = $starSepratedSVCDetailsValArr[1];
														$REF_prov_id = $starSepratedSVCDetailsValArr[2];
														$updateSVCREFTblStr = "UPDATE era_835_proc_details SET
																			REF_type = '$REF_type',
																			REF_prov_identifier = '$REF_prov_id'
																			WHERE 835_Era_proc_Id = '$procInsertId'";
														$updateSVCREFTblQry = imw_query($updateSVCREFTblStr);
													}
												break;
												case 'AMT':
													$AMT_type = $starSepratedSVCDetailsValArr[1];
													$AMT_Amount = $starSepratedSVCDetailsValArr[2];
													$updateSVCAMTTblStr = "UPDATE era_835_proc_details SET
																		AMT_type = '$AMT_type',
																		AMT_amount = '$AMT_Amount'
																		WHERE 835_Era_proc_Id = '$procInsertId'";
													$updateSVCAMTTblQry = imw_query($updateSVCAMTTblStr);
												break;
												case 'LQ':
													if($starSepratedSVCDetailsValArr[1]=="HE"){
														$old_rem_code="";
														$rem_sel_qry=imw_query("select rem_code from era_835_proc_details where rem_code!='' and 835_Era_proc_Id = '$procInsertId'");
														if(imw_num_rows($rem_sel_qry)>0){
															$rem_sel_row=imw_fetch_array($rem_sel_qry);
															$old_rem_code=$rem_sel_row['rem_code'].', ';
														}
														$rem_code = $old_rem_code.$starSepratedSVCDetailsValArr[2];
														$updateSVCAMTTblStr = "UPDATE era_835_proc_details SET
																				rem_code = '$rem_code'
																				WHERE 835_Era_proc_Id = '$procInsertId'";
														$updateSVCAMTTblQry = imw_query($updateSVCAMTTblStr);
													}
												break;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	if(count($svc_dtm_arr)>0){
		foreach($svc_dtm_arr as $dtm_key => $dtm_val){
			$pat_lev_dtm_qry="UPDATE era_835_proc_details SET DTM_type = '232',DTM_date = '$dtm_val' WHERE ERA_patient_details_id = '$dtm_key' and DTM_date='0000-00-00'";
			imw_query($pat_lev_dtm_qry);
		}
	}
	imw_query("update electronicfiles_tbl set read_status=1,read_on='$date_time' where id='$electronicFilesTblId'");
?>