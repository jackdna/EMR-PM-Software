<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*****************/
/*TO PARSE EDI 271 DATA
/****************/
require_once(dirname(__FILE__)."/class.electronic_billing.php");
//$objEBilling = new ElectronicBilling();

class RTEparser extends ElectronicBilling{
	
	function __construct(){
		
		
	}
	
	function readRTEresponse($EDIData){
		$EDIData	= trim($EDIData);
		$response = array('error'=>'','result'=>'');
		
		if($EDIData!=''){
			$TA3 = $this->FindSegmentInX12EDI($EDIData,'TA3');
			$TA3 = $this->FindSegmentInX12EDI($EDIData,'TA3');
			if($TA3==false){//NO TA3 FOUND,
				$ST 		= $this->FindSegmentInX12EDI($EDIData,'ST');
				$EDI_type 	= trim($this->FindSegmentValue($ST,1));
				if($EDI_type=='271'){
					$EDIDataLines = explode('~',$EDIData);
					$EDIDataLines = preg_split('/~/', $EDIData, -1, PREG_SPLIT_NO_EMPTY);
					$response_summary 	= array();
					$checked_segments	= array();
					$checked_loops		= array();
					$HL_1 = $HL_2 = $HL_3 = $HL_4 = $current_loop = $EB_loop = $LS_loop = $LE_loop = false;
					$PrevsegmentName = $PrevsegmentInitials = '';
					$EB_loop_counter = 0;
					for($i=0; $i<count($EDIDataLines);$i++){
						
						$Lines			= $EDIDataLines[$i];
						$segmentName	= $this->FindSegmentValue($Lines,0);
						$segmentInitials= strtoupper($this->FindSegmentValue($Lines,1));
						
						/******NEXT SEGMENT AND INITIALS**********/
						$NextLines			= $EDIDataLines[$i+1];
						$NextsegmentName	= $this->FindSegmentValue($NextLines,0);
						$NextsegmentInitials= strtoupper($this->FindSegmentValue($NextLines,1));
						

						if($segmentName=='HL'){
							$hl_array = array();
							//HL*2*1*21*1~
							$hl02 = trim($this->FindSegmentValue($Lines,2)); //parent HL segment id (HL01).
							$hl03 = $this->HL_entity_idenfication_qualifier(trim($this->FindSegmentValue($Lines,3))); //HL entity idenfication code.
							$hl04 = trim($this->FindSegmentValue($Lines,4)); //
							
							if($segmentInitials=='1'){
								$HL_1 = true;
								$current_loop = '2100A';
								//$hl_array[$current_loop] = 'Payer Information Level.';	
							}else if($segmentInitials=='2'){
								$HL_2 = true;
								$current_loop = '2100B';
							//	$hl_array[$current_loop] = 'Provider Information Level.';	
							}else if($segmentInitials=='3'){
								$HL_3 = true;
								$current_loop = '2100C';
								//$hl_array[$current_loop] = 'Subscriber Information Level.';	
							}else if($segmentInitials=='4'){
								$HL_4 = true;
								$current_loop = '2100D';
								//$hl_array[$current_loop] = 'Dependent Information Level.';	
							}
							$hl_array['Level'] = $hl03;
							$hl_array['Parent'] = $hl02;
							$hl_array['Child'] = $hl04;
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['HL']	= $hl_array;
						}else if($segmentName=='AAA'){
							$aaa_array = array();
							$aaa01	= strtoupper(trim($this->FindSegmentValue($Lines,1)));
							$aaa03	= trim($this->FindSegmentValue($Lines,3));
							$aaa04	= trim($this->FindSegmentValue($Lines,4));
							if($aaa01=='Y'){
								$aaa_array['Valid_Request'] = 'Yes';
							}else if($aaa01!='Y'){
								$aaa_array['Valid_Request'] = 'No';
							}
							if($aaa03!=''){
								$aaa_array['Validation_Message'] = $this->getAAAmsgByCode($aaa03);
							}
							if($aaa04!=''){
								$aaa_array['Followup_Action'] = $this->getAAAactionByCode($aaa04);
							}
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['AAA']	= $aaa_array;
						}else if($segmentName=='TRN'){
							//TRN*2*98175-012547*9877281234*RADIOLOGY~
							$TRN_array = array();
							$TRN01	= trim($this->FindSegmentValue($Lines,1))=='1' ? 'Current Transaction Trace Numbers' : 'Referenced Transaction Trace Numbers';//TRACE TYPE CODE
							$TRN02	= trim($this->FindSegmentValue($Lines,2));//REFERENCE IDENTIFICATION
							$TRN03	= trim($this->FindSegmentValue($Lines,3));//ORIGINATING COMPANY IDENTIFER
							$TRN04	= trim($this->FindSegmentValue($Lines,4));//REFERENCE IDENTIFICATION
							$TRN_array['Trace_Type_Code'] = $TRN01;
							$TRN_array['Trace_Identification'] = $TRN02;
							$TRN_array['Originating_Company'] = $TRN03;
							$TRN_array['Identification'] = $TRN04;
							
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['TRN']	= $TRN_array;
						}else if($segmentName=='NM1' && in_array('HL',$checked_segments)){
							if(($EB_loop && !$LS_loop) || ($EB_loop && $LS_loop && $LE_loop)){
								$response_summary[$current_loop][$EB_type]['EB_'.$EB_loop_counter][] = $EB_array;		
								unset($EB_array);						
							}
							//if($current_loop=='2110C' || $current_loop=='2115C'){$current_loop = '2120C';}
							
							$NM1_ENTITY_CATEGORY= $this->get_entity_category(trim($this->FindSegmentValue($Lines,1)));
							//if($current_loop == '2120C') var_dump($this->get_entity_category(trim($this->FindSegmentValue($Lines,1))));
							$NM1_ENTITY_TYPE	= trim($this->FindSegmentValue($Lines,2));
							$NM1_ENTITY_NAME 	= trim($this->FindSegmentValue($Lines,3));
							$NM1_ID_TYPE 		= strtoupper($this->FindSegmentValue($Lines,8));
							$NM1_ENTITY_ID		= strtoupper($this->FindSegmentValue($Lines,9));
							
							$NM1_ID_TYPE = $this->identification_code_qualifier($NM1_ID_TYPE);
							
							
							/********IF ENTITY IS PERSON, READ OTHER NAME ELEMENTS*******/
							if($NM1_ENTITY_TYPE=='1'){//1=PERSON;2 = NON-PERSON ENTITY
								$NM1_ENTITY_NAME 	= trim($this->FindSegmentValue($Lines,3));
								if(trim($this->FindSegmentValue($Lines,4))!=''){
									$NM1_ENTITY_NAME 	= trim($this->FindSegmentValue($Lines,4)).', '.$NM1_ENTITY_NAME;
								}
								if(trim($this->FindSegmentValue($Lines,5))!=''){
									$NM1_ENTITY_NAME 	= $NM1_ENTITY_NAME.' '.trim($this->FindSegmentValue($Lines,5));
								}
							}
							
							$nm1_array = array();
							$nm1_array['ENTITY_CATEGORY'] = $NM1_ENTITY_CATEGORY;
							$nm1_array['ENTITY_TYPE'] = $NM1_ENTITY_TYPE=='1' ? 'PERSON' : 'NON-PERSON ENTITY';
							if($segmentInitials=='PR' || $segmentInitials=='PRP'){ //Payer info
								$nm1_array['Payer_Name'] = strtoupper($NM1_ENTITY_NAME);
								$nm1_array['Payer_ID_Type'] = $NM1_ID_TYPE;
								$nm1_array['Payer_ID'] = strtoupper($NM1_ENTITY_ID);
							}else if($segmentInitials=='1P'){ //Billing Service Provider info
								$nm1_array['Billing_Provider_Name'] = strtoupper($NM1_ENTITY_NAME);
								$nm1_array['Billing_Provider_ID_Type'] = $NM1_ID_TYPE;
								$nm1_array['Billing_Provider_ID'] = strtoupper($NM1_ENTITY_ID);
							}else if($segmentInitials=='IL'){ //Subscriber info
								$nm1_array['Subscriber_Name'] = strtoupper($NM1_ENTITY_NAME);
								$nm1_array['Billing_Provider_ID_Type'] = $NM1_ID_TYPE;
								$nm1_array['Subscriber_ID'] = strtoupper($NM1_ENTITY_ID);
							}
							//$response_summary[$current_loop][]	= $Lines;
							if($EB_loop && $LS_loop && !$LE_loop){
								$EB_array['Additional_Payer_Info']['NM1'] = $nm1_array;
							}else{
								$response_summary[$current_loop]['NM1']	= $nm1_array;
							}
							
						}else if($segmentName=='N3' && in_array('NM1',$checked_segments)){
							$n3_array = array();
							$n3_array['Street1']	= trim($this->FindSegmentValue($Lines,1));
							$n3_array['Street2']	= trim($this->FindSegmentValue($Lines,2));
							//$response_summary[$current_loop][]	= $Lines;
							if($EB_loop && $LS_loop && !$LE_loop){
								$EB_array['Additional_Payer_Info']['N3'] = $n3_array;
							}else{
								$response_summary[$current_loop]['N3']	= $n3_array;
							}
						}else if($segmentName=='N4' && in_array('NM1',$checked_segments)){
							$n4_array = array();
							$n4_array['City']			= trim($this->FindSegmentValue($Lines,1));
							$n4_array['State/Province']	= trim($this->FindSegmentValue($Lines,2));
							$n4_array['Zip_Code']			= trim($this->FindSegmentValue($Lines,3));
							//$response_summary[$current_loop][]	= $Lines;
							if($EB_loop && $LS_loop && !$LE_loop){
								$EB_array['Additional_Payer_Info']['N4'] = $n4_array;
							}else{
								$response_summary[$current_loop]['N4']	= $n4_array;
							}
						}else if($segmentName=='PER' && in_array('NM1',$checked_segments)){
							$PER_array = array();
							$comm_qualifier_arr = array('ED'=>'EDI Access Number',
														'EM'=>'Email',
														'FX'=>'FAX',
														'TE'=>'Telephone',
														'WP'=>'Work Phone',
														'EX'=>'Extension',
														'UR'=>'Website',
														'IC'=>'Information Contact');
							if(trim($this->FindSegmentValue($Lines,2))!=''){
								$per1 = $comm_qualifier_arr[trim($this->FindSegmentValue($Lines,1))];
								$PER_array[str_replace(' ','_',$per1)]	= trim($this->FindSegmentValue($Lines,2));
							}
							if(trim($this->FindSegmentValue($Lines,4))!=''){
								$per3 = $comm_qualifier_arr[trim($this->FindSegmentValue($Lines,3))];
								$PER_array[str_replace(' ','_',$per3)]	= trim($this->FindSegmentValue($Lines,4));
							}
							if(trim($this->FindSegmentValue($Lines,6))!=''){
								$per5 = $comm_qualifier_arr[trim($this->FindSegmentValue($Lines,5))];
								$PER_array[str_replace(' ','_',$per5)]	= trim($this->FindSegmentValue($Lines,6));
							}
							if(trim($this->FindSegmentValue($Lines,8))!=''){
								$per7 = $comm_qualifier_arr[trim($this->FindSegmentValue($Lines,7))];
								$PER_array[str_replace(' ','_',$per7)]	= trim($this->FindSegmentValue($Lines,8));
							}
							
							//$response_summary[$current_loop][]	= $Lines;
							if($EB_loop && $LS_loop && !$LE_loop){
								$EB_array['Additional_Payer_Info']['PER'] = $PER_array;
							}else{
								$response_summary[$current_loop]['PER']	= $PER_array;
							}
						}else if($segmentName=='PRV' && in_array('NM1',$checked_segments)){
							$PRV_array = array();
							if(trim($this->FindSegmentValue($Lines,3))!=''){
								$prv1 = $this->provider_code(trim($this->FindSegmentValue($Lines,1))).' '.$this->provider_code(trim($this->FindSegmentValue($Lines,2)));
								$PRV_array[str_replace(' ','_',$prv1)]	= trim($this->FindSegmentValue($Lines,3));
							}
						
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['PRV']	= $PRV_array;
						}else if($segmentName=='DMG' && in_array('NM1',$checked_segments)){
							$dmg_array 	= array();
							$dmg1 		= trim($this->FindSegmentValue($Lines,1));
							$dmg2 		= trim($this->FindSegmentValue($Lines,2));
							$dmg3 		= strtolower(trim($this->FindSegmentValue($Lines,3)));
							$dmg_dob = $dmg_sex = '';
							if(strtoupper($dmg1)=='D8' && strlen($dmg2)==8){//YYYYMMDD
								$dmg_dob = substr($dmg2,4,2).'-'.substr($dmg2,6,2).'-'.substr($dmg2,0,4);
							}
							if(strlen($dmg3)==1){//YYYYMMDD
								$dmg_sex = ($dmg3=='f') ? 'Female' : (($dmg3=='m') ? 'Male' : '');
							}
							$dmg_array['DOB']	= $dmg_dob;
							$dmg_array['Sex']	= $dmg_sex;
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['DMG']	= $dmg_array;
						}else if($segmentName=='INS' && in_array('NM1',$checked_segments)){//SUBSCRIBER RELATIONSHIP
						//INS*Y*18*001*25~
							$INS_array = array();
							$INS_array['Subscriber_Relationship']	= trim($this->FindSegmentValue($Lines,2))=='18' ? 'Self' : 'Not-Self';
							//$response_summary[$current_loop][]	= $Lines;
							$response_summary[$current_loop]['INS'] = $INS_array;
						}else if($segmentName=='REF' && in_array('HL',$checked_segments)){
							$ref_array = array();
							$ref_array['Qualifier']			= $this->additional_info_code_qualifier(trim($this->FindSegmentValue($Lines,1)));
							$ref_array['Qualifier_Value']	= trim($this->FindSegmentValue($Lines,2));
							$ref_array['Qualifier_Description']	= trim($this->FindSegmentValue($Lines,3));
							
							if($EB_loop && is_array($EB_array)){
								$EB_array['REF_'.trim($this->FindSegmentValue($Lines,1))] = $ref_array;
							}else{
								//$response_summary[$current_loop][]	= $Lines;
								$response_summary[$current_loop]['REF_'.trim($this->FindSegmentValue($Lines,1))] = $ref_array;
							}
						}else if($segmentName=='DTP' && in_array('NM1',$checked_segments)){
						//DTP*346*D8*19950818~
							$dtp_array 	= array();
							if($EB_loop && is_array($EB_array)){
								$dtp1 		= $this->EB_DTP_types(trim($this->FindSegmentValue($Lines,1)));
							}
							if($dtp1==NULL || !$EB_loop){
								$dtp1 		= $this->DTP_types(trim($this->FindSegmentValue($Lines,1)));
							}						
							
							$dtp2 		= strtoupper(trim($this->FindSegmentValue($Lines,2)));
							$dtp3 		= trim(trim($this->FindSegmentValue($Lines,3)));
							
							if(strtoupper($dtp2)=='D8' && strlen($dtp3)==8){//YYYYMMDD
								$dtp3= substr($dtp3,4,2).'-'.substr($dtp3,6,2).'-'.substr($dtp3,0,4);
							}else if(strtoupper($dtp2)=='RD8' && strlen($dtp3)==17){//Range of Dates Expressed in Format CCYYMMDD-CCYYMMDD	
								$dtp3= substr($dtp3,4,2).'-'.substr($dtp3,6,2).'-'.substr($dtp3,0,4).' to '.substr($dtp3,13,2).'-'.substr($dtp3,15,2).'-'.substr($dtp3,9,4);
							}
							
							
							if($EB_loop && is_array($EB_array)){
								$EB_array['DTP'][str_replace(' ','_',$dtp1)] = $dtp3;
							}else{
								$dtp_array[str_replace(' ','_',$dtp1)]	= $dtp3;
								//$response_summary[$current_loop][$SegmentHead][]	= $Lines;
								$response_summary[$current_loop][str_replace(' ','_',$dtp1).'_Date']	= $dtp_array;
							}
						}else if($segmentName=='EB'){
						//EB*6**30~
						//EB*1*FAM*96*GP~ EB*B**98***27*10**VS*1~ EB*C*IND****23*200~ EB*C*FAM****23*600~ EB*A**A6*****.50~
						//EB*1*IND**MA~DTP*307*D8*19901101~EB*1*IND**MB~DTP*307*D8*19911001~EB*C**96*MB**29*0~
						//EB*D*IND**MB*********HC|G0101|TC
							if($EB_loop){
								$response_summary[$current_loop][$EB_type]['EB_'.$EB_loop_counter][] = $EB_array;		
								unset($EB_array);						
							}					
							
							//if($current_loop!='2120C')
								$current_loop	= '2110C';
							
							$EB_loop		= true;
							$EB_array = array();
							$eb1	= trim($this->FindSegmentValue($Lines,1));
							$EB_type	= $this->EB_values($eb1);
							//$EB_array['Eligibilty_Type']	= $EB_type;
							
				//			if(!in_array($eb1,array('6','7','8'))){
								$EB_coverage = $this->EB_coverage(trim($this->FindSegmentValue($Lines,2)));
								$EB_array['Coverage_Level']				= $EB_coverage;
								$EB_array['Service_Type']				= $this->EB_service_type(trim($this->FindSegmentValue($Lines,3)));
								$EB_array['Insurance_Type']				= $this->EB_insurance_type(trim($this->FindSegmentValue($Lines,4)));
								$EB_array['Plan_Coverage_Description']	= trim($this->FindSegmentValue($Lines,5));
								$EB_array['Time_Period_Qualifier']		= $this->EB_time_period(trim($this->FindSegmentValue($Lines,6)));
								$EB_array['Benefit_Amount']				= numberFormat(trim($this->FindSegmentValue($Lines,7)),2);
								
								
								$EB_array['Benefit_Percentage']			= trim($this->FindSegmentValue($Lines,8))!=''? ((float)trim($this->FindSegmentValue($Lines,8)) * 100).'%' : '';//(float)$strEBPercent * 100
								$EB_array['Quantity_Qualifier']			= $this->EB_quantity_qualifier(trim($this->FindSegmentValue($Lines,9)));
								$EB_array['Quantity']					= trim($this->FindSegmentValue($Lines,10));
								//if(is_array($dtp_array)) foreach($dtp_array as $k=>$v){$EB_array[$k] = $v;}
								
								$EB_11									= strtoupper(trim($this->FindSegmentValue($Lines,11)));
								$EB_array['Auth_Cert_Indicator']		= $EB_11=='N'?'No':$EB_11=='Y'?'Yes':$EB_11=='U'?'Unknown':$EB_11;
								$EB_12									= strtoupper(trim($this->FindSegmentValue($Lines,12)));
								$EB_array['Benefits_In_Plan_Network']	= $EB_12=='N'?'No':$EB_12=='Y'?'Yes':$EB_12=='U'?'Unknown':$EB_12=='W'?'Not Applicable':$EB_12;
								$EB_13									= strtoupper(trim($this->FindSegmentValue($Lines,13)));
								if($EB_13!=''){
									$EB_13_ar = explode('|',$EB_13);
									if(count($EB_13_ar)>1){
										$EB_array['Medical_Procedure_Qualifier']= $this->EB_service_id_qualifier($EB_13_ar[0]);
										$EB_array['Medical_Procedure_Value']= $EB_13_ar[1];
										if($EB_13_ar[2]!=''){
											$EB_array['Medical_Procedure_Modifiers']= implode(':',array_slice($EB_13_ar,2));
										}
									}else{
										$EB_array['Medical_Procedure_Identifier']= $EB_13;
									}
								}
								
								/******MAKING STRING TO SAVE RTE_AMOUNT*******/
								//EBInsTypeID-EBServiceTypeID-EBCoverageLevel-EBPlanCoverageDescrip-BenefitAmt-EBTimePeriodQualifier-EBBenefitPercent-
								//EBQuantity-EBQuantityQualifier-EBConditionRespID(Blank)-EBPlanConditionRespID(Blank)
								$EB_array['Set_RTE_Amt'] = trim($this->FindSegmentValue($Lines,4)).'-'.trim($this->FindSegmentValue($Lines,3)).'-'.trim($this->FindSegmentValue($Lines,2)).'-'.trim($this->FindSegmentValue($Lines,5)).'-'.trim($this->FindSegmentValue($Lines,7)).'-'.trim($this->FindSegmentValue($Lines,6)).'-'.trim($this->FindSegmentValue($Lines,8)).'-'.trim($this->FindSegmentValue($Lines,10)).'-'.trim($this->FindSegmentValue($Lines,9)).'-'.trim($this->FindSegmentValue($Lines,11)).'-'.trim($this->FindSegmentValue($Lines,12));
								
				//			}
							$EB_loop_counter++;
						//	$response_summary[$current_loop]['EB'][$EB_type][]	= $Lines;
						//	$response_summary[$current_loop]['EB'][$EB_type][] = $EB_array;
						
						}else if($segmentName=='HSD'){
						//HSD*VS*30***22~ Thirty visits per service year 
						//HSD*VS*12*WK*3*34*1~ Twelve visits, three visits per week, for 1 month.
							$HSD_array = array();
							$hsd1_vals = array('DY'=>'Days','FL'=>'Units','HS'=>'Hours','MN'=>'Month','VS'=>'Visits');
							$hsd1	= $hsd1_vals[trim($this->FindSegmentValue($Lines,1))];
							$hsd2	= trim($this->FindSegmentValue($Lines,2));
							$hsd3_vals = array('DA'=>'Days','MO'=>'Months','VS'=>'Visit','WK'=>'Week','YR'=>'Years');
							$hsd3	= $hsd3_vals[trim($this->FindSegmentValue($Lines,3))];
							$hsd4	= trim($this->FindSegmentValue($Lines,4));
							$hsd5	= $this->EB_time_period(trim($this->FindSegmentValue($Lines,5)));
							$hsd6	= trim($this->FindSegmentValue($Lines,6));
							$HSD_text = '';
							if($hsd1!='' && $hsd2!=''){
								$HSD_text = $hsd2.' '.$hsd1;
								
							}
							if($hsd3!='' && $hsd4!=''){
								$HSD_text .= ', '.$hsd4.' '.$hsd1.' per '.$hsd3;
								if($hsd5!='' && $hsd6=='') $HSD_text .= ', per '.$hsd5;
								if($hsd5!='' && $hsd6!='') $HSD_text .= ', for '.$hsd6.' '.$hsd5;
							}
							if($hsd5!='' && $hsd6=='') $HSD_text .= ' per '.$hsd5;
							if($hsd5!='' && $hsd6!='') $HSD_text .= ', for '.$hsd6.' '.$hsd5;	
							
							if(($hsd1=='' && $hsd2=='' && $hsd4=='') && ($hsd3!='' && $hsd5!='' && $hsd6!='')){
								$HSD_text = $hsd3.' '.$hsd5.' '.$hsd6;
							}
							if(($hsd1=='' && $hsd2=='' && $hsd3=='' && $hsd4=='') && ($hsd5!='' && $hsd6!='')){
								$HSD_text = 'For '.$hsd6.' '.$hsd5;
							}
							if(($hsd3=='' && $hsd4=='') && ($hsd1!='' && $hsd2!='' && $hsd5!='' && $hsd6==NULL)){
								$HSD_text = $hsd2.' '.$hsd1.' '.$hsd5;
							}
							
							$HSD_array['Health_Care_Service_Delivery'] = $HSD_text;
							//$response_summary[$current_loop]['HSD'][]	= $Lines;
							//$response_summary[$current_loop]['HSD'][] = $HSD_array;
							if($EB_loop && is_array($EB_array)){
								$EB_array['Health_Care_Service_Delivery'] = $HSD_text;
							}
						}else if($segmentName=='MSG'){
						//MSG*Free form text is discouraged~							
							if($EB_loop && is_array($EB_array)){
								
								//IF MULTIPLE MSG segments found sequentially.
								if(isset($EB_array['Comments']))
									$EB_array['Comments'] .= '<br>'.trim($this->FindSegmentValue($Lines,1));
								else
									$EB_array['Comments'] = trim($this->FindSegmentValue($Lines,1));
							}
						}else if($segmentName=='III'){
						//III*BK*486~ III*ZZ*21~
							//$current_loop	= '2115C';
							$arr_III1 = array('BF'=>'Diagnosis','BK'=>'Principal Diagnosis','ZZ'=>'Facility Type');						
							if($EB_loop && is_array($EB_array)){
								$III_ar = array();
								$III_ar[str_replace(' ','_',$arr_III1[trim($this->FindSegmentValue($Lines,1))])] = trim($this->FindSegmentValue($Lines,2));
								$EB_array['Additional_Information'][] = $III_ar;
							}
						}else if($segmentName=='LS'){
						//III*BK*486~ III*ZZ*21~
							$LS_loop = true;
							$LE_loop = false;
						
						}else if($segmentName=='LE'){
						//III*BK*486~ III*ZZ*21~
							$LE_loop = true;
							if($EB_loop){
								$response_summary[$current_loop][$EB_type]['EB_'.$EB_loop_counter][] = $EB_array;		
								unset($EB_array);						
							}
						}else if(in_array($segmentName,array('SE','GE','IEA'))){
						//III*BK*486~ III*ZZ*21~
							if($EB_loop && is_array($EB_array)){
								$response_summary[$current_loop][$EB_type]['EB_'.$EB_loop_counter][] = $EB_array;		
								unset($EB_array);						
							}
							break;
						}else{
							//$response_summary[$current_loop][]	= $Lines;
						}
						//$response_summary[$current_loop][]	= $Lines;
						$checked_segments[]		= $segmentName;
						$PrevsegmentName		= $segmentName;
						$PrevsegmentInitials	= $segmentInitials;
					}
					$response['result'] = $response_summary;
				}else{
					$response['error'] = 'Not a valid X12 271 EDI (RTE) response.';
				}				
			}else{//READ TA3.
				$ta3RejectionCode = $this->FindSegmentValue($TA3,3);
				switch($ta3RejectionCode){
					case '28': $response['error'] = '28 - Time Out. Not Delivered.'; break;
					case '29': $response['error'] = '29 - Time Out. Deliverd.'; break;
					case '31': $response['error'] = '31 - Receiver Not On-Line.'; break;
					case '32': $response['error'] = '32 - Abnormal Conditions.'; break;			
				}
			}
		}else{
			$response['error'] = 'Not a valid X12 278 EDI (RTE) response.';
		}		
		return $response;
		
	}
	
	function get_entity_category($c){
		$entity = array();
		$entity['1P'] 	= 'Provider';
		$entity['2B'] 	= 'Third-Party Administrator';
		$entity['36'] 	= 'Employer';
		$entity['80'] 	= 'Hospital';
		$entity['FA'] 	= 'Facility';
		$entity['GP'] 	= 'Gateway Provider';
		$entity['P5'] 	= 'Plan Sponsor';
		$entity['PR'] 	= 'Payer';
		$entity['PRP'] 	= 'Payer';
		return $entity[$c];
	}
	
	function identification_code_qualifier($c){
		$qualitifer = array();
		$qualitifer['24']		= 'Employer\'s Identification Number';
		$qualitifer['34']		= 'Social Security Number';
		$qualitifer['46']		= 'Electronic Transmitter Identification Number';
		$qualitifer['FI']		= 'Federal Taxpayer\'s Identification Number';
		$qualitifer['NI']		= 'NAIC Identification';
		$qualitifer['PI']		= 'Payer Identification';
		$qualitifer['PP']		= 'Pharmacy Processor Number';
		$qualitifer['SV']		= 'Service Provider Number';
		$qualitifer['XV']		= 'National Payer Identification Number';
		$qualitifer['XX']		= 'NPI';
		$qualitifer['MI']		= 'Member Identification';
		$qualitifer['ZZ']		= 'Mutually Defined';
		return $qualitifer[$c];		
	}
	
	function additional_info_code_qualifier($c){
		$qualitifer = array();
		$qualitifer['18']		= 'Plan Number';
		$qualitifer['49']		= 'Family Unit Number';
		$qualitifer['55']		= 'Sequence Number';
		$qualitifer['0B']		= 'State License Number';
		$qualitifer['1C']		= 'Medicare Provider Number';
		$qualitifer['1D']		= 'Medicaid Provider Number';
		$qualitifer['1J']		= 'Facility ID Number';
		$qualitifer['1L']		= 'Group or Policy Number';
		$qualitifer['1W']		= 'Member Identification Number';
		$qualitifer['3H']		= 'Case Number';
		$qualitifer['4A']		= 'Personal Identification Number (PIN)';
		$qualitifer['6P']		= 'Group Number';
		$qualitifer['9F']		= 'Referral Number';
		$qualitifer['A6']		= 'Employee Identification Number';
		$qualitifer['CT']		= 'Contract Number';
		$qualitifer['EA']		= 'Medical Record Identification Number';
		$qualitifer['EJ']		= 'Patient Account Number';
		$qualitifer['EL']		= 'Electronic device pin number';
		$qualitifer['EO']		= 'Submitter Identification Number';
		$qualitifer['F6']		= 'Health Insurance Claim (HIC) Number';
		$qualitifer['G1']		= 'Prior Authorization Number';
		$qualitifer['GH']		= 'Identification Card Serial Number';
		$qualitifer['HJ']		= 'Identity Card Number';
		$qualitifer['IF']		= 'Issue Number';
		$qualitifer['IG']		= 'Insurance Policy Number';
		$qualitifer['JD']		= 'User Identification';
		$qualitifer['ML']		= 'Military Rank/Civilian Pay Grade Number';
		$qualitifer['N5']		= 'Provider Plan Network Identification Number';
		$qualitifer['N6']		= 'Plan Network Identification Number';
		$qualitifer['N7']		= 'Facility Network Identification Number';
		$qualitifer['NQ']		= 'Medicaid Recipient Identification Number';
		$qualitifer['Q4']		= 'Prior Identifier Number';
		$qualitifer['SY']		= 'Social Security Number';
		$qualitifer['TJ']		= 'Federal Taxpayer\'s Identification Number';
		$qualitifer['HPI']		= 'Health Care Financing Administration National Provider Identifier';
		//$qualitifer['']		= '';
		
		return $qualitifer[$c];
	}
	
	function HL_entity_idenfication_qualifier($c){
		$qualitifer = array();
		$qualitifer['20']		= 'Information Source (Payer)';
		$qualitifer['21']		= 'Information Receiver (Group)';
		$qualitifer['22']		= 'Subscriber';
		//$qualitifer['']		= '';
		return $qualitifer[$c];
	}
	
	function DTP_types($c){
		$DTPs = array();
		$DTPs['102'] 	= 'Issue';
		$DTPs['152'] 	= 'Effective Date of Change';
		$DTPs['290'] 	= 'Effective Date';
		$DTPs['291'] 	= 'Plan';
		$DTPs['292'] 	= 'Calendar Year';	
		$DTPs['307'] 	= 'Eligibility';
		$DTPs['318'] 	= 'Added';
		$DTPs['340'] 	= 'Consolidated Omnibus Budget Reconciliation Act (COBRA) Begin';
		$DTPs['341'] 	= 'Consolidated Omnibus Budget Reconciliation Act (COBRA) End';
		$DTPs['342'] 	= 'Premium Paid to Date Begin';
		$DTPs['343'] 	= 'Premium Paid to Date End';
		$DTPs['346'] 	= 'Plan Begin';
		$DTPs['347'] 	= 'Plan End';
		$DTPs['356'] 	= 'Eligibility Begin';
		$DTPs['357'] 	= 'Eligibility End';
		$DTPs['382'] 	= 'Enrollment';
		$DTPs['435'] 	= 'Admission';
		$DTPs['442'] 	= 'Date of Death';
		$DTPs['458'] 	= 'Certification';
		$DTPs['472'] 	= 'Service';
		$DTPs['539'] 	= 'Policy Effective';
		$DTPs['540'] 	= 'Policy Expiration';
		$DTPs['636'] 	= 'Date of Last Update';
		$DTPs['771'] 	= 'Status';
		return $DTPs[$c];
	}
	
	function EB_DTP_types($c){
		$DTPs = array();
		$DTPs['193'] 	= 'Period Start';
		$DTPs['194'] 	= 'Period End';
		$DTPs['198'] 	= 'Completion';
		$DTPs['290'] 	= 'Coordination of Benefits';
		$DTPs['292'] 	= 'Benefit';
		$DTPs['295'] 	= 'Primary Care Provider';	
		$DTPs['304'] 	= 'Latest Visit of Consultation';
		$DTPs['307'] 	= 'Eligibility';
		$DTPs['318'] 	= 'Added';
		$DTPs['348'] 	= 'Benefit Begin';
		$DTPs['349'] 	= 'Benefit End';
		$DTPs['356'] 	= 'Eligibility Begin';
		$DTPs['357'] 	= 'Eligibility End';
		$DTPs['435'] 	= 'Admission';
		$DTPs['472'] 	= 'Service';
		$DTPs['636'] 	= 'Date of Last Update';
		return $DTPs[$c];
	}
	
	function EB_values($c){
		$EB_code = array();
		$EB_code['1']	= 'Active Coverage';
		$EB_code['2']	= 'Active - Full Risk Capitation';
		$EB_code['3']	= 'Active - Services Capitated';
		$EB_code['4']	= 'Active - Services Capitated to Primary Care Physician';
		$EB_code['5']	= 'Active - Pending Investigation';
		$EB_code['6']	= 'Inactive';
		$EB_code['7']	= 'Inactive - Pending Eligibility Update';
		$EB_code['8']	= 'Inactive - Pending Investigation';
		$EB_code['A']	= 'Co-Insurance';
		$EB_code['B']	= 'Co-Payment';
		$EB_code['C']	= 'Deductible';
		$EB_code['D']	= 'Benifit Description';
		$EB_code['E']	= 'Exclusions';
		$EB_code['F']	= 'Limitations';
		$EB_code['G']	= 'Out of Pocket (Stop Loss)';
		$EB_code['H']	= 'Unlimited';
		$EB_code['I']	= 'Non-Covered';
		$EB_code['J']	= 'Cost Containment';
		$EB_code['K']	= 'Reserve';
		$EB_code['L']	= 'Primary Care Provider';
		$EB_code['M']	= 'Pre-existing Condition';
		$EB_code['N']	= 'Services Restricted to Following Provider';
		$EB_code['O']	= 'Not Deemed a Medical Necessity';
		$EB_code['P']	= 'Benefit Disclaimer';
		$EB_code['Q']	= 'Second Surgical Opinion Required';
		$EB_code['R']	= 'Other or Additional Payor';
		$EB_code['S']	= 'Prior Year(s) History';
		$EB_code['T']	= 'Card(s) Reported Lost/Stolen';
		$EB_code['U']	= 'Contact Following Entity for Eligibility or Benefit Information';
		$EB_code['V']	= 'Cannot Process';
		$EB_code['W']	= 'Other Source of Data';
		$EB_code['X']	= 'Health Care Facility';
		$EB_code['Y']	= 'Spend Down';
		$EB_code['CB']	= 'Coverage Basis';
		$EB_code['MC']	= 'Managed Care Coordinator';
		//$EB_code['']	= '';
		//$EB_code['']	= '';
		return $EB_code[$c];
	}
	
	function EB_coverage($c){
		$EB_code = array();
		$EB_code['CHD']	= 'Children Only';
		$EB_code['DEP']	= 'Dependents Only';
		$EB_code['ECH']	= 'Employee and Children';
		$EB_code['EMP']	= 'Employee Only';
		$EB_code['ESP']	= 'Employee and Spouse';
		$EB_code['FAM']	= 'Family';
		$EB_code['IND']	= 'Individual';
		$EB_code['SPC']	= 'Spouse and Children';
		$EB_code['SPO']	= 'Spouse Only';
		//$EB_code['']	= '';
		return $EB_code[$c];	
	}
	
	function EB_service_type($c){
		$EB_code = array();
		$EB_code['1']	= 'Medical Care';
		$EB_code['2']	= 'Surgical';
		$EB_code['3']	= 'Consultation';
		$EB_code['4']	= 'Diagnostic X-Ray';
		$EB_code['5']	= 'Diagnostic Therapy';
		$EB_code['6']	= 'Radiation Therapy';
		$EB_code['7']	= 'Anesthesia';
		$EB_code['8']	= 'Surgical Assistance';
		$EB_code['9']	= 'Other Medical';
		$EB_code['10']	= 'Blood Charges';
		$EB_code['11']	= 'Used Durable Medical Equipment';
		$EB_code['12']	= 'Durable Medical Equipment Purchase';
		$EB_code['13']	= 'Ambulatory Service Center Facility';
		$EB_code['14']	= 'Renal Supplies in the Home';
		$EB_code['15']	= 'Alternate Method Dialysis';
		$EB_code['16']	= 'Chronic Renal Disease (CRD) Equipment';
		$EB_code['17']	= 'Pre-Admission Testing';
		$EB_code['18']	= 'Durable Medical Equipment Rental';
		$EB_code['19']	= 'Pneumonia Vaccine';
		$EB_code['20']	= 'Second Surgical Opinion';
		$EB_code['21']	= 'Third Surgical Opinion';
		$EB_code['22']	= 'Social Work';
		$EB_code['23']	= 'Diagnostic Dental';
		$EB_code['24']	= 'Periodontics';
		$EB_code['25']	= 'Restorative';
		$EB_code['26']	= 'Endodontics';
		$EB_code['27']	= 'Maxillofacial Prosthetics';
		$EB_code['28']	= 'Adjunctive Dental Services';
		$EB_code['30']	= 'Health Benefit Plan Coverage';
		$EB_code['32']	= 'Plan Waiting Period';
		$EB_code['33']	= 'Chiropractic';
		$EB_code['34']	= 'Chiropractic Office Visits';
		$EB_code['35']	= 'Dental Care';
		$EB_code['36']	= 'Dental Crowns';
		$EB_code['37']	= 'Dental Accident';
		$EB_code['38']	= 'Orthodontics';
		$EB_code['39']	= 'Prosthodontics';
		$EB_code['40']	= 'Oral Surgery';
		$EB_code['40']	= 'Routine (Preventive) Dental';
		$EB_code['42']	= 'Home Health Care';
		$EB_code['43']	= 'Home Health Prescriptions';
		$EB_code['44']	= 'Home Health Visits';
		$EB_code['45']	= 'Hospice';
		$EB_code['46']	= 'Respite Care';
		$EB_code['47']	= 'Hospital';
		$EB_code['48']	= 'Hospital - Inpatient';
		$EB_code['49']	= 'Hospital - Room and Board';
		$EB_code['50']	= 'Hospital - Outpatient';
		$EB_code['51']	= 'Hospital - Emergency Accident';
		$EB_code['52']	= 'Hospital - Emergency Medical';
		$EB_code['53']	= 'Hospital - Ambulatory Surgical';
		$EB_code['54']	= 'Long Term Care';
		$EB_code['55']	= 'Major Medical';
		$EB_code['56']	= 'Medically Related Transportation';
		$EB_code['57']	= 'Air Transportation';
		$EB_code['58']	= 'Cabulance';
		$EB_code['59']	= 'Licensed Ambulance';
		$EB_code['60']	= 'General Benefits';
		$EB_code['61']	= 'In-vitro Fertilization';
		$EB_code['62']	= 'MRI/CAT Scan';
		$EB_code['63']	= 'Donor Procedures';
		$EB_code['64']	= 'Acupuncture';
		$EB_code['65']	= 'Newborn Care';
		$EB_code['66']	= 'Pathology';
		$EB_code['67']	= 'Smoking Cessation';
		$EB_code['68']	= 'Well Baby Care';
		$EB_code['69']	= 'Maternity';
		$EB_code['70']	= 'Transplants';
		$EB_code['71']	= 'Audiology Exam';
		$EB_code['72']	= 'Inhalation Therapy';
		$EB_code['73']	= 'Diagnostic Medical';
		$EB_code['74']	= 'Private Duty Nursing';
		$EB_code['75']	= 'Prosthetic Device';
		$EB_code['76']	= 'Dialysis';
		$EB_code['77']	= 'Otological Exam';
		$EB_code['78']	= 'Chemotherapy';
		$EB_code['79']	= 'Allergy Testing';
		$EB_code['80']	= 'Immunizations';
		$EB_code['81']	= 'Routine Physical';
		$EB_code['82']	= 'Family Planning';
		$EB_code['83']	= 'Infertility';
		$EB_code['84']	= 'Abortion';
		$EB_code['85']	= 'AIDS';
		$EB_code['86']	= 'Emergency Services';
		$EB_code['87']	= 'Cancer';
		$EB_code['88']	= 'Pharmacy';
		$EB_code['89']	= 'Free Standing Prescription Drug';
		$EB_code['90']	= 'Mail Order Prescription Drug';
		$EB_code['91']	= 'Brand Name Prescription Drug';
		$EB_code['92']	= 'Generic Prescription Drug';
		$EB_code['93']	= 'Podiatry';
		$EB_code['94']	= 'Podiatry - Office Visits';
		$EB_code['95']	= 'Podiatry - Nursing Home Visits';
		$EB_code['96']	= 'Professional (Physician)';
		$EB_code['97']	= 'Anesthesiologist';
		$EB_code['98']	= 'Professional (Physician) Visit - Office';
		$EB_code['99']	= 'Professional (Physician) Visit - Inpatient';
		$EB_code['A0']	= 'Professional (Physician) Visit - Outpatient';
		$EB_code['A1']	= 'Professional (Physician) Visit - Nursing Home';
		$EB_code['A2']	= 'Professional (Physician) Visit - Skilled Nursing Facility';
		$EB_code['A3']	= 'Professional (Physician) Visit - Home';
		$EB_code['A4']	= 'Psychiatric';
		$EB_code['A5']	= 'Psychiatric - Room and Board';
		$EB_code['A6']	= 'Psychotherapy';
		$EB_code['A7']	= 'Psychotherapy - Inpatient';
		$EB_code['A8']	= 'Psychotherapy - Outpatient';
		$EB_code['A9']	= 'Rehabilitation';
		$EB_code['AA']	= 'Rehabilitation - Room and Board';
		$EB_code['AB']	= 'Rehabilitation - Inpatient';
		$EB_code['AC']	= 'Rehabilitation - Outpatient';
		$EB_code['AD']	= 'Occupational Therapy';
		$EB_code['AE']	= 'Physical Medicine';
		$EB_code['AF']	= 'Speech Therapy';
		$EB_code['AG']	= 'Skilled Nursing Care';
		$EB_code['AH']	= 'Skilled Nursing Care - Room and Board';
		$EB_code['AI']	= 'Substance Abuse';
		$EB_code['AJ']	= 'Alcoholism';
		$EB_code['AK']	= 'Drug Addiction';
		$EB_code['AL']	= 'Vision (Optometry)';
		$EB_code['AM']	= 'Frames';
		$EB_code['AN']	= 'Routine Exam';
		$EB_code['AO']	= 'Lenses';
		$EB_code['AQ']	= 'Nonmedically Necessary Physical';
		$EB_code['AR']	= 'Experimental Drug Therapy';
		$EB_code['BA']	= 'Independent Medical Evaluation';
		$EB_code['BB']	= 'Partial Hospitalization (Psychiatric)';
		$EB_code['BC']	= 'Day Care (Psychiatric)';
		$EB_code['BD']	= 'Cognitive Therapy';
		$EB_code['BE']	= 'Massage Therapy';
		$EB_code['BF']	= 'Pulmonary Rehabilitation';
		$EB_code['BG']	= 'Cardiac Rehabilitation';
		$EB_code['BH']	= 'Pediatric';
		$EB_code['BI']	= 'Nursery';
		$EB_code['BJ']	= 'Skin';
		$EB_code['BK']	= 'Orthopedic';
		$EB_code['BL']	= 'Cardiac';
		$EB_code['BM']	= 'Lymphatic';
		$EB_code['BN']	= 'Gastrointestinal';
		$EB_code['BP']	= 'Endocrine';
		$EB_code['BQ']	= 'Neurology';
		$EB_code['BR']	= 'Eye';
		$EB_code['BS']	= 'Invasive Procedures';
		
		$arr_STCI = array();
		$arr_STCI['1']	= 'Medical Care';
		$arr_STCI['2']	= 'Surgical';
		$arr_STCI['3']	= 'Consultation';
		$arr_STCI['4']	= 'Diagnostic X-Ray';
		$arr_STCI['5']	= 'Diagnostic Lab';
		$arr_STCI['6']	= 'Radiation Therapy';
		$arr_STCI['7']	= 'Anesthesia';
		$arr_STCI['8']	= 'Surgical Assistance';
		// index 9 not available.
		$arr_STCI['10']	= 'Blood';
		$arr_STCI['11']	= 'Durable Medical Equipment Used';
		$arr_STCI['12']	= 'Durable Medical Equipment Purchased';
		// index 13 not available.
		$arr_STCI['14']	= 'Renal Supplies';
		// index 15, 16 not available.
		$arr_STCI['17']	= 'Pre-Admission Testing';
		$arr_STCI['18']	= 'Durable Medical Equipment Rental';
		$arr_STCI['19']	= 'Pneumonia Vaccine';
		$arr_STCI['20']	= 'Second Surgical Opinion';
		$arr_STCI['21']	= 'Third Surgical Opinion';
		$arr_STCI['22']	= 'Social Work';
		$arr_STCI['23']	= 'Diagnostic Dental';
		$arr_STCI['24']	= 'Periodontics';
		$arr_STCI['25']	= 'Restorative';
		$arr_STCI['26']	= 'Endodontics';
		$arr_STCI['27']	= 'Maxillofacial Prosthetics';
		$arr_STCI['28']	= 'Adjunctive Dental Services';
		// index 29 not available.
		$arr_STCI['30']	= 'Health Benefit Plan Coverage';
		// index 31 not available.
		$arr_STCI['32']	= 'Plan Waiting Period';
		$arr_STCI['33']	= 'Chiropractic';
		$arr_STCI['34']	= 'Chiropractic Modality';
		$arr_STCI['35']	= 'Dental Care';
		$arr_STCI['36']	= 'Dental Crowns';
		$arr_STCI['37']	= 'Dental Accident';
		$arr_STCI['38']	= 'Orthodontics';
		$arr_STCI['39']	= 'Prosthodontics';
		$arr_STCI['40']	= 'Oral Surgery';
		$arr_STCI['41']	= 'Preventive Dental';
		$arr_STCI['42']	= 'Home Health Care';
		$arr_STCI['43']	= 'Home Health Prescriptions';
		// index 44 not available.
		$arr_STCI['45']	= 'Hospice';
		$arr_STCI['46']	= 'Respite Care';
		$arr_STCI['47']	= 'Hospitalization';
		// index 48 not available.
		$arr_STCI['49']	= 'Hospital - Room and Board';
		// index 50,51,52,53 not available.
		$arr_STCI['54']	= 'Long Term Care';
		$arr_STCI['55']	= 'Major Medical';
		$arr_STCI['56']	= 'Medically Related Transportation';
		// index 57,58,59 not available.
		$arr_STCI['60']	= 'General Benefits';
		$arr_STCI['61']	= 'In-vitro Fertilization';
		$arr_STCI['62']	= 'MRI Scan';
		$arr_STCI['63']	= 'Donor Procedures';
		$arr_STCI['64']	= 'Acupuncture';
		$arr_STCI['65']	= 'Newborn Care';
		$arr_STCI['66']	= 'Pathology';
		$arr_STCI['67']	= 'Smoking Cessation';
		$arr_STCI['68']	= 'Well Baby Care';
		$arr_STCI['69']	= 'Maternity';
		$arr_STCI['70']	= 'Transplants';
		$arr_STCI['71']	= 'Audiology';
		$arr_STCI['72']	= 'Inhalation Therapy';
		$arr_STCI['73']	= 'Diagnostic Medical';
		$arr_STCI['74']	= 'Private Duty Nursing';
		$arr_STCI['75']	= 'Prosthetic Device';
		$arr_STCI['76']	= 'Dialysis';
		$arr_STCI['77']	= 'Otology';
		$arr_STCI['78']	= 'Chemotherapy';
		$arr_STCI['79']	= 'Allergy Testing';
		$arr_STCI['80']	= 'Immunizations';
		$arr_STCI['81']	= 'Routine Physical';
		$arr_STCI['82']	= 'Family Planning';
		$arr_STCI['83']	= 'Infertility';
		$arr_STCI['84']	= 'Abortion';
		$arr_STCI['85']	= 'HIV - AIDS Treatment';
		$arr_STCI['86']	= 'Emergency Services';
		$arr_STCI['87']	= 'Cancer Treatment';
		$arr_STCI['88']	= 'Pharmacy';
		$arr_STCI['89']	= 'Free Standing Prescription Drug';
		$arr_STCI['90']	= 'Mail Order Prescription Drug';
		$arr_STCI['91']	= 'Brand Name Prescription Drug';
		$arr_STCI['92']	= 'Generic Prescription Drug';
		$arr_STCI['93']	= 'Podiatry';
		// Alphabetic code start.
		$arr_STCI['A4']	= 'Psychiatric';
		$arr_STCI['A6']	= 'Psychotherapy';
		$arr_STCI['A7']	= 'Psychiatric - Inpatient';
		$arr_STCI['A8']	= 'Psychiatric - Outpatient';
		$arr_STCI['A9']	= 'Rehabilitation';
		$arr_STCI['AB']	= 'Rehabilitation - Inpatient';
		$arr_STCI['AC']	= 'Rehabilitation - Outpatient';
		$arr_STCI['AD']	= 'Occupational Therapy';
		$arr_STCI['AE']	= 'Physical Medicine';
		$arr_STCI['AF']	= 'Speech Therapy';
		$arr_STCI['AG']	= 'Skilled Nursing Care';
		$arr_STCI['AI']	= 'Substance Abuse';
		$arr_STCI['AJ']	= 'Alcoholism Treatment';
		$arr_STCI['AK']	= 'Drug Addiction';
		$arr_STCI['AL']	= 'Optometry';
		$arr_STCI['AM']	= 'Frames';
		$arr_STCI['AO']	= 'Lenses';
		$arr_STCI['AP']	= 'Routine Eye Exam';
		$arr_STCI['AQ']	= 'Nonmedically Necessary Physical';
		$arr_STCI['AR']	= 'Experimental Drug Therapy';
		$arr_STCI['B1']	= 'Burn Care';
		$arr_STCI['B2']	= 'Brand Name Prescription Drug - Formulary';
		$arr_STCI['B3']	= 'Brand Name Prescription Drug - Non-Formulary';
		$arr_STCI['BA']	= 'Independent Medical Evaluation';
		$arr_STCI['BB']	= 'Psychiatric Treatment Partial Hospitalization';
		$arr_STCI['BC']	= 'Day Care (Psychiatric)';
		$arr_STCI['BD']	= 'Cognitive Therapy';
		$arr_STCI['BE']	= 'Massage Therapy';
		$arr_STCI['BF']	= 'Pulmonary Rehabilitation';
		$arr_STCI['BG']	= 'Cardiac Rehabilitation';
		$arr_STCI['BH']	= 'Pediatric';
		$arr_STCI['BI']	= 'Nursery Room and Board';
		$arr_STCI['BK']	= 'Orthopedic';
		$arr_STCI['BL']	= 'Cardiac';
		$arr_STCI['BM']	= 'Lymphatic';
		$arr_STCI['BN']	= 'Gastrointestinal';
		$arr_STCI['BP']	= 'Endocrine';
		$arr_STCI['BQ']	= 'Neurology';
		$arr_STCI['BT']	= 'Gynecological';
		$arr_STCI['BU']	= 'Obstetrical';
		$arr_STCI['BV']	= 'Obstetrical/Gynecological';
		$arr_STCI['BW']	= 'Mail Order Prescription Drug: Brand Name';
		$arr_STCI['BX']	= 'Mail Order Prescription Drug: Generic';
		$arr_STCI['BY']	= 'Physician Visit - Sick';
		$arr_STCI['BZ']	= 'Physician Visit - Well';
		$arr_STCI['C1']	= 'Coronary Care';
		$arr_STCI['CK']	= 'Screening X-ray';
		$arr_STCI['CL']	= 'Screening laboratory';
		$arr_STCI['CM']	= 'Mammogram, High Risk Patient';
		$arr_STCI['CN']	= 'Mammogram, Low Risk Patient';
		$arr_STCI['CO']	= 'Flu Vaccination';
		$arr_STCI['CP']	= 'Eyewear Accessories';
		$arr_STCI['CQ']	= 'Case Management';
		$arr_STCI['DG']	= 'Dermatology';
		$arr_STCI['DM']	= 'Durable Medical Equipment';
		$arr_STCI['DS']	= 'Diabetic Supplies';
		$arr_STCI['E0']	= 'Allied Behavioral Analysis Therapy';
		$arr_STCI['E1']	= 'Non-Medical Equipment (non DME)';
		$arr_STCI['E2']	= 'Psychiatric Emergency';
		$arr_STCI['E3']	= 'Step Down Unit';
		$arr_STCI['E4']	= 'Skilled Nursing Facility Head Level of Care';
		$arr_STCI['E5']	= 'Skilled Nursing Facility Ventilator Level of Care';
		$arr_STCI['E6']	= 'Level of Care 1';
		$arr_STCI['E7']	= 'Level of Care 2';
		$arr_STCI['E8']	= 'Level of Care 3';
		$arr_STCI['E9']	= 'Level of Care 4';
		$arr_STCI['E10']= 'Radiographs';
		$arr_STCI['E11']= 'Diagnostic Imaging';
		$arr_STCI['E12']= 'Basic Restorative - Dental';
		$arr_STCI['E13']= 'Major Restorative - Dental';
		$arr_STCI['E14']= 'Fixed Prosthodontics';
		$arr_STCI['E15']= 'Removable Prosthodontics';
		$arr_STCI['E16']= 'Intraoral Images - Complete Series';
		$arr_STCI['E17']= 'Oral Evaluation';
		$arr_STCI['E18']= 'Dental Prophylaxis';
		$arr_STCI['E19']= 'Panoramic Images';
		$arr_STCI['E20']= 'Sealants';
		$arr_STCI['E21']= 'Flouride Treatments';
		$arr_STCI['E22']= 'Dental Implants';
		$arr_STCI['E23']= 'Temporomandibular Joint Dysfunction';
		$arr_STCI['E24']= 'Retail Pharmacy Prescription Drug';
		$arr_STCI['E25']= 'Long Term Care Pharmacy';
		$arr_STCI['E26']= 'Comprehensive Medication Therapy Management Review';
		$arr_STCI['E27']= 'Targeted Medication Therapy Management Review';
		$arr_STCI['E28']= 'Dietary/Nutritional Services';
		$arr_STCI['E29']= 'Technical Cardiac Rehabilitation Services Component';
		$arr_STCI['E30']= 'Professional Cardiac Rehabilitation Services Component';
		$arr_STCI['E31']= 'Professional Intensive Cardiac Rehabilitation Services Component';
		$arr_STCI['EA']	= 'Preventive Services';
		$arr_STCI['EB']	= 'Specialty Pharmacy';
		$arr_STCI['EC']	= 'Durable Medical Equipment New';
		$arr_STCI['ED']	= 'CAT Scan';
		$arr_STCI['EE']	= 'Ophthalmology';
		$arr_STCI['EF']	= 'Contact Lenses';
		$arr_STCI['GF']	= 'Generic Prescription Drug - Formulary';
		$arr_STCI['GN']	= 'Generic Prescription Drug - Non-Formulary';
		$arr_STCI['GY']	= 'Allergy';
		$arr_STCI['IC']	= 'Intensive Care';
		$arr_STCI['MH']	= 'Mental Health';
		$arr_STCI['NI']	= 'Neonatal Intensive Care';
		$arr_STCI['ON']	= 'Oncology';
		$arr_STCI['PE']	= 'Positron Emission Tomography (PET) Scan';
		$arr_STCI['PT']	= 'Physical Therapy';
		$arr_STCI['PU']	= 'Pulmonary';
		$arr_STCI['RN']	= 'Renal';
		$arr_STCI['RT']	= 'Residential Psychiatric Treatment';
		$arr_STCI['SMH']= 'Serious Mental Health';
		$arr_STCI['TC']	= 'Transitional Care';
		$arr_STCI['TN']	= 'Transitional Nursery Care';
		$arr_STCI['UC']	= 'Urgent Care';

		//$EB_code['']	= '';
		$c_Arr = explode('^',$c);
		$str = array();
		foreach($c_Arr as $c){$str[] = $EB_code[$c]==NULL ? $arr_STCI[$c] : $EB_code[$c];}
		return implode(', ',$str);
	}
	
	function EB_insurance_type($c){
		$EB_code = array();
		$EB_code['D']	= 'Disability';
		$EB_code['12']	= 'Medicare Secondary Working Aged Beneficiary or Spouse with Employer Group Health Plan';
		$EB_code['13']	= 'Medicare Secondary End-Stage Renal Disease Beneficiary in the 12 month coordination period with an employer\'s group health plan';
		$EB_code['14']	= 'Medicare Secondary, No-fault Insurance including Auto is Primary';
		$EB_code['15']	= 'Medicare Secondary Worker\'s Compensation';
		$EB_code['16']	= 'Medicare Secondary Public Health Service (PHS)or Other Federal Agency';
		$EB_code['41']	= 'Medicare Secondary Black Lung';
		$EB_code['42']	= 'Medicare Secondary Veteran\'s Administration';
		$EB_code['43']	= 'Medicare Secondary Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)';
		$EB_code['47']	= 'Medicare Secondary, Other Liability Insurance is Primary';
		$EB_code['AP']	= 'Auto Insurance Policy';
		$EB_code['C1']	= 'Commercial';
		$EB_code['CO']	= 'Consolidated Omnibus Budget Reconciliation Act (COBRA)';
		$EB_code['CP']	= 'Medicare Conditionally Primary';
		$EB_code['DB']	= 'Disability Benefits';
		$EB_code['EP']	= 'Exclusive Provider Organization';
		$EB_code['FF']	= 'Family or Friends';
		$EB_code['GP']	= 'Group Policy';
		$EB_code['HM']	= 'Health Maintenance Organization (HMO)';
		$EB_code['HN']	= 'Health Maintenance Organization (HMO) - Medicare Risk';
		$EB_code['HS']	= 'Special Low Income Medicare Beneficiary';
		$EB_code['IN']	= 'Indemnity';
		$EB_code['IP']	= 'Individual Policy';
		$EB_code['LC']	= 'Long Term Care';
		$EB_code['LD']	= 'Long Term Policy';
		$EB_code['LI']	= 'Life Insurance';
		$EB_code['LT']	= 'Litigation';
		$EB_code['MA']	= 'Medicare Part A';
		$EB_code['MB']	= 'Medicare Part B';
		$EB_code['MC']	= 'Medicaid';
		$EB_code['MH']	= 'Medigap Part A';
		$EB_code['MI']	= 'Medigap Part B';
		$EB_code['MP']	= 'Medicare Primary';
		$EB_code['OT']	= 'Other';
		$EB_code['PE']	= 'Property Insurance - Personal';
		$EB_code['PL']	= 'Personal';
		$EB_code['PP']	= 'Personal Payment (Cash - No Insurance)';
		$EB_code['PR']	= 'Preferred Provider Organization (PPO)';
		$EB_code['PS']	= 'Point of Service (POS)';
		$EB_code['QM']	= 'Qualified Medicare Beneficiary';
		$EB_code['RP']	= 'Property Insurance - Real';
		$EB_code['SP']	= 'Supplemental Policy';
		$EB_code['TF']	= 'Tax Equity Fiscal Responsibility Act (TEFRA)';
		$EB_code['WC']	= 'Workers Compensation';
		$EB_code['WU']	= 'Wrap Up Policy';
		//$EB_code['']	= '';
		return $EB_code[$c]==NULL ? $c : $EB_code[$c];	
	}
	
	function EB_time_period($c){
		$EB_code = array();
		$EB_code['6']	= 'Hours';
		$EB_code['7']	= 'Day';
		$EB_code['13']	= '24 Hours';
		$EB_code['21']	= 'Years';
		$EB_code['22']	= 'Service Year';
		$EB_code['23']	= 'Calendar Year';
		$EB_code['24']	= 'Year to Date';
		$EB_code['25']	= 'Contract';
		$EB_code['26']	= 'Episode';
		$EB_code['27']	= 'Visit';
		$EB_code['28']	= 'Outlier';
		$EB_code['29']	= 'Remaining';
		$EB_code['30']	= 'Exceeded';
		$EB_code['31']	= 'Not Exceeded';
		$EB_code['32']	= 'Lifetime';
		$EB_code['33']	= 'Lifetime Remaining';
		$EB_code['34']	= 'Month';
		$EB_code['35']	= 'Week';
		$EB_code['36']	= 'Admission';
		//$EB_code['']	= '';
		//$EB_code['']	= '';
		return $EB_code[$c];
	}
	
	function EB_quantity_qualifier($c){
		$EB_code = array();
		$EB_code['99']	= 'Quantity Used';
		$EB_code['CA']	= 'Covered - Actual';
		$EB_code['CE']	= 'Covered - Estimated';
		$EB_code['DB']	= 'Deductible Blood Units';
		$EB_code['DY']	= 'Days';
		$EB_code['HS']	= 'Hours';
		$EB_code['LA']	= 'Life-time Reserve - Actual';
		$EB_code['LE']	= 'Lefe-time Reserve - Estimated';
		$EB_code['MN']	= 'Month';
		$EB_code['P6']	= 'Number of Services or Procedures';
		$EB_code['QA']	= 'Quantity Approved';
		$EB_code['S7']	= 'Age, High Value';
		$EB_code['S8']	= 'Age, Low Value';
		$EB_code['VS']	= 'Visits';
		$EB_code['YY']	= 'Years';
		//$EB_code['']	= '';
		return $EB_code[$c];
	}
	
	function EB_service_id_qualifier($c){
		$EB_code = array();
		$EB_code['AD']	= 'American Dental Association Codes';
		$EB_code['CJ']	= 'CPT Codes';
		$EB_code['HC']	= 'HCPCS Codes';
		$EB_code['ID']	= 'ICD-9-CM Procedure';
		$EB_code['IV']	= 'Home Infusion EDI Coalition (HIEC) Product/Service Code';
		$EB_code['N4']	= 'Natinal Drug Code in 5-4-2 Format';
		$EB_code['ZZ']	= 'Mutually Defined';
		$EB_code['']	= '';
		return $EB_code[$c];
	}
	
	function provider_code($c){
		$PV_code = array();
		$PV_code['H']	= 'Hospital';
		$PV_code['R']	= 'Rural Health Clinic';
		$PV_code['AD']	= 'Admitting';
		$PV_code['AT']	= 'Attending';
		$PV_code['BI']	= 'Billing';
		$PV_code['CO']	= 'Consulting';
		$PV_code['CV']	= 'Covering';
		$PV_code['HH']	= 'Home Health Care';
		$PV_code['LA']	= 'Laboratory';
		$PV_code['OT']	= 'Other Physician';
		$PV_code['P1']	= 'Pharmacist';
		$PV_code['P2']	= 'Pharmacy';
		$PV_code['PC']	= 'Primary Care Physician';
		$PV_code['PE']	= 'Performing';
		$PV_code['RF']	= 'Referring';
		$PV_code['SB']	= 'Submitting';
		$PV_code['SK']	= 'Skilled Nursing Facility';
		$PV_code['SU']	= 'Supervising';
		
		$PV_code['9K']	= 'Servicer';
		$PV_code['D3']	= 'National Association of Boards of Pharmacy Number';
		$PV_code['EI']	= 'EIN';
		$PV_code['SY']	= 'SSN';
		$PV_code['TJ']	= 'Taxonomy';
		$PV_code['ZZ']	= 'Mutually Defined';
		$PV_code['HPI']	= 'Health Care Financing Administration National Provider Identifier';
		$PV_code['']	= '';
		return $PV_code[$c]==NULL ? $c: $PV_code[$c];
	}
	
	public function pos_facility_codes($c){
		$r = false;
		$q = "SELECT pos_description FROM pos_tbl WHERE pos_code = '".strtoupper($c)."' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			$r = $rs['pos_description'];
		}else{
			if($c=='49') $r = 'Independent Clinic';
			else $r = $c;
		}
		return $r;
	}
}

?>