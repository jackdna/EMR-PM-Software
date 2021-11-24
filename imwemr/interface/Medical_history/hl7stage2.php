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
?>
<?php 
/*	
File: hl7stage2.php
Purpose: Used for HL7
Access Type: Direct
*/
set_include_path(dirname(__FILE__).'/../../../library');
require_once("../../config/globals.php");
//error_reporting(-1);
//ini_set("display_errors",-1);
require_once("../../library/patient_must_loaded.php");
require_once(dirname(__FILE__)."/../../hl7sys/old/CLSHL7_imm.php");
$library_path = $GLOBALS['webroot'].'/library';
$for						= (isset($_REQUEST['for']) && trim($_REQUEST['for'])!='') ? trim($_REQUEST['for']) : 'LAB';
$task						= (isset($_REQUEST['task']) && trim($_REQUEST['task'])!='') ? trim($_REQUEST['task']) : 'Export';
$patient_id					= $_SESSION['patient'];
$hl7text					= '';

if($task=='Incorporate'){
	$IncorporateText = "";
	$activate_q = "";
	$recordId = (isset($_REQUEST['recordid']) && trim($_REQUEST['recordid'])!='') ? trim($_REQUEST['recordid']) : 0;
	if($recordId != '' || $recordId == 0){$IncorporateText .= 'Invalid Record ID.<br>';}
	if($for=='LAB'){
		$activate_q = "UPDATE lab_test_data SET lab_status = '0' WHERE lab_test_data_id = '$recordId'";
		$activate_res = imw_query($activate_q);
		if($activate_res){
			$IncorporateText .= 'Incorporation done. Refreshing records.....<br><script type="text/javascript">window.top.fmain.medical_history.location.reload();</script>';
		}
	}
}else if($task=='Export'){
	$objHL7						= new makeHL7();
	$objHL7->message_for 		= 'LAB';
	$objHL7->order_common_id 	= '1';

	switch($for){
		case 'LAB':
			$Exportext = $objHL7->get_HL7msg($patient_id);
			break;
	}
}else if($task=='Import'){
	$decodedHL7 = '';
	$hl7mu_id = (isset($_REQUEST['hl7mu_id']) && trim($_REQUEST['hl7mu_id'])!='') ? trim($_REQUEST['hl7mu_id']) : 0;
	if(isset($_POST['sub']) || $hl7mu_id>0){
		if($hl7mu_id==0){
			$status_msg = 'Data received...analyzing...<br>';
    		$tmp_name = $_FILES['file1']['tmp_name'];
			$hl7text = file_get_contents($tmp_name);
		}else{
			$hl7mu_q	= "SELECT msg FROM hl7_mu WHERE id = '$hl7mu_id' LIMIT  0,1";
			$hl7mu_res	= imw_query($hl7mu_q);
			$hl7mu_rs	= imw_fetch_assoc($hl7mu_res);
			$hl7text	= $hl7mu_rs['msg'];
		}
		if(strpos($hl7text,'MSH|^~') !== false){
			$objParseHL7				= new parseHL7();
			$objParseHL7->parser($hl7text,'2.5.1');
			$arrHL7msg					= $objParseHL7->HL7segments;
			$arrSegsByLine				= $objParseHL7->getSegmentsByLineNum();
			$doneSegment				= array();
			$decodedHL7 				.= '<div class="m5">';
			$LastSegment				= '';// to compare what the last segment was.
			$orc_cnt=0;$obr_cnt=0;$obx_cnt=0;$spm_cnt=0; $nte_cnt = 0;
			$PID_done = $MSH_done		= false;
			
			/*Arrays to contains DB saveable values*/
			$arrAbnormalFlags					= get_abnormal_flag_fun();
			$lab_main_table 					= array();
			$lab_main_table['lab_name']			= '';
			$lab_main_table['lab_patient_id']	= $patient_id;
			$lab_main_table['lab_entered_by']	= $_SESSION['authId'];
			
			foreach($arrHL7msg as $ln=>$segArr){
				foreach($segArr as $segName=>$ArrVals){
//				foreach($arrUniqueSegs as $segName=>$segCounts){					
					//if(in_array($segName,$doneSegment)) continue;
					$doneSegment[] = $segName;
					//echo $LastSegment.' : '.$segName.'<br>';
					switch($segName){
						case 'MSH':
							if($MSH_done) {$LastSegment = $segName;break;}
							$ARR_MSH2		= explode('^',$objParseHL7->getSegmentVal('MSH',2));
							$msgSender		= $ARR_MSH2['0'];
							$ARR_MSH3		= explode('^',$objParseHL7->getSegmentVal('MSH',3));
							$msgSenderF		= $ARR_MSH3['0'];
							$STR_MSH7		= $objParseHL7->getSegmentVal('MSH',6); //20110531140551
							$msgDtTime		= @substr($STR_MSH7,4,2).'-'.@substr($STR_MSH7,6,2).'-'.@substr($STR_MSH7,0,4).' '.@substr($STR_MSH7,8,2).':'.@substr($STR_MSH7,10,2).':'.@substr($STR_MSH7,12,2);
							$lab_tz			= @substr($STR_MSH7,14,3).':'.@substr($STR_MSH7,17,2);
							$ARR_MSH8		= explode('^',$objParseHL7->getSegmentVal('MSH',8));
							$msgType		= $ARR_MSH8['2'];
							if($hl7mu_id==0){
								$hl7msgId		= $objParseHL7->log_HL7_message($patient_id,$msgType,$for,$task);
							}
							$lab_main_table['hl7_mu_id']			= $hl7msgId;
							$lab_main_table['lab_status']			= 1;
							$lab_main_table['lab_order_date']		= @substr($STR_MSH7,0,4).'-'.@substr($STR_MSH7,4,2).'-'.@substr($STR_MSH7,6,2);
							$lab_main_table['lab_order_time']		= @substr($STR_MSH7,8,2).':'.@substr($STR_MSH7,10,2).':'.@substr($STR_MSH7,12,2);
							$lab_main_table['order_time_zone']		= $lab_tz;
							
							if($msgSender != '' || $msgSenderF != ''){
								$decodedHL7 .= '<div class="sub_head">MESSAGE HEADER</div>';
								$decodedHL7 .= '<div class="section_details">
								<table class="table table-hover table-bordered table-condensed table-striped">
									';if($msgSender != ''){$decodedHL7 .= '
									<tr>
										<td class="bold" style="width:140px;">Sending Application</td>
										<td style="width:10px;">:</td>
										<td style="width:auto;">'.$msgSender.'</td>
									</tr>';}
									
									if($msgSenderF != ''){
									$decodedHL7 .= '
									<tr>
										<td class="bold">Sending Facility</td>
										<td>:</td>
										<td>'.$msgSenderF.'</td>
									</tr>';}
									if(strlen($msgDtTime)>10){
									$decodedHL7 .= '
									<tr>
										<td class="bold">Message Time</td>
										<td>:</td>
										<td>'.$msgDtTime.' (Timezone: '.$lab_tz.')</td>
									</tr>';}
									
									$decodedHL7 .= '
								</table></div>';
							}
							$MSH_done = true;
							$LastSegment = $segName;
							break;
						case 'PID':
							if($PID_done)	{$LastSegment = $segName;break;}
							$ARR_PID3		= explode('^',$objParseHL7->getSegmentVal('PID',3));
							$pat_id			= $ARR_PID3['0'];
							$ARR_PID5		= explode('^',$objParseHL7->getSegmentVal('PID',5));
							$pat_name		= $ARR_PID5['0'].', '.$ARR_PID5['1'].' '.$ARR_PID5['2'].' '.$ARR_PID5['3'];
							$STR_PID7		= $objParseHL7->getSegmentVal('PID',7);
							$pat_dob		= @substr($STR_PID7,4,2).'-'.@substr($STR_PID7,6,2).'-'.@substr($STR_PID7,0,4);
							$pat_dobdb		= @substr($STR_PID7,0,4).'-'.@substr($STR_PID7,4,2).'-'.@substr($STR_PID7,6,2);
							$pat_sex		= $objParseHL7->getGenderByCode($objParseHL7->getSegmentVal('PID',8));
							$ARR_PID8		= explode('^',$objParseHL7->getSegmentVal('PID',10));
							$pat_race		= $ARR_PID8['1'];
							$ptMatch		= $objParseHL7->MatchPatient($ARR_PID5,$pat_dobdb,$pat_sex,$pat_race);
							$decodedHL7 .= '<div class="sub_head">PATIENT DETAILS</div>';
							$decodedHL7 .= '<div class="section_details">
							<table class="table table-hover table-bordered table-condensed table-striped">';
							if(!$ptMatch){
							$decodedHL7 .= '
								<tr>
									<td class="text-warning bg-danger" colspan="3" class="text-center"><b>NOTE:</b> Patient information is not matching. You can not incorporate LAB order information</td>
								</tr>';
							}else{
								if($hl7mu_id==0){
									$new_lab_id		= AddRecords($lab_main_table,'lab_test_data');
								}
							}
							$decodedHL7 .= '
								<tr>
									<td class="bold" style="width:140px;">Patient Name</td>
									<td style="width:10px;">:</td>
									<td style="width:auto;">'.$pat_name.' - '.$pat_id.'.</td>
								</tr>
								<tr>
									<td class="bold">Date of Birth</td>
									<td>:</td>
									<td>'.$pat_dob.'</td>
								</tr>
								<tr>
									<td class="bold">Administrative Sex</td>
									<td>:</td>
									<td>'.$pat_sex.'</td>
								</tr>
								<tr>
									<td class="bold">Race</td>
									<td>:</td>
									<td>'.$pat_race.'</td>
								</tr>
							</table></div>';
							$PID_done = true;
							$LastSegment = $segName;
							break;
						case 'ORC':
							if($LastSegment != 'ORC'){
								if($arrSegsByLine[$ln-1]!='PID'){
									$decodedHL7 .= '
									</table></div><br>';
								}
								$decodedHL7 .= '<div class="sub_head">LAB TEST ORDER DETAILS</div>';
								$decodedHL7 .= '<div class="section_details">
								<table class="table table-hover table-bordered table-condensed table-striped">
								<tr class="bg5">
									<td class="bold alignCenter" style="width:120px;">Order#</td>
									<td class="bold alignCenter" style="width:auto;">Ordering Provider</td>
									<td class="bold alignCenter" style="width:150px;">Filler Order#</td>
								</tr>';
							}
							//for($orc_cnt=0;$orc_cnt<$segCounts;$orc_cnt++){
								$ARR_ORC2		= explode('^',$objParseHL7->getSegmentVal('ORC',2,$orc_cnt));
								$ord_id			= $ARR_ORC2['0'];
								$ord_ehr		= ($ARR_ORC2['1'] != '') ? ' ('.$ARR_ORC2['1'].')' : '';
								$ARR_ORC3		= explode('^',$objParseHL7->getSegmentVal('ORC',3,$orc_cnt));
								$ford_id		= $ARR_ORC3['0'];
								$ford_ehr		= ($ARR_ORC3['1'] != '') ? ' ('.$ARR_ORC3['1'].')' : '';
								$ARR_ORC12		= explode('^',$objParseHL7->getSegmentVal('ORC',12,$orc_cnt));
								$ord_pro		= $ARR_ORC12['5'].' '.$ARR_ORC12['1'].', '.$ARR_ORC12['2'].' '.$ARR_ORC12['3'].' '.$ARR_ORC12['4'];
								$ord_pro		.= ($ARR_ORC12['0'] != '') ? ' (ID# '.$ARR_ORC12['0'].')' : '';
								$decodedHL7 .= '
								<tr class="alignCenter">
									<td>'.$ord_id.$ord_ehr.'</td>
									<td>'.$ord_pro.'</td>
									<td>'.$ford_id.$ford_ehr.'</td>
								</tr>';
						//	}
							if($orc_cnt==0 && $new_lab_id>0){
								$OrderById	= $objParseHL7->MatchProvider(array($ARR_ORC12['1'],$ARR_ORC12['2'],$ARR_ORC12['3']),$ARR_ORC12['0']);
								if(!$OrderById){$OrderById = '';}
								$orcLabData['impoted_order_id'] = trim($ord_id);
								$orcLabData['impoted_from_ehr'] = trim($ARR_ORC2['1']);
								$orcLabData['lab_test_order_by'] = intval($OrderById);
								$orcLabData['lab_test_order_by_name'] = $ord_pro;
								if($hl7mu_id==0){
									UpdateRecords($new_lab_id,'lab_test_data_id',$orcLabData,'lab_test_data');
								}
							}							
							$orc_cnt++;
							$LastSegment = $segName;
							break;
						case 'OBR':
							$altClassOBR==' white';
							if($LastSegment != 'OBR'){
								$decodedHL7 .= '
								</table></div><br>';
								$decodedHL7 .= '<div class="sub_head">OBSERVATION REQUEST</div>';
								$decodedHL7 .= '<div class="section_details">
								<table class="table table-hover table-bordered table-condensed table-striped">
								<tr class="bg5">
									<td class="bold alignCenter" style="width:120px;">Filler Order#</td>
									<td class="bold alignCenter" style="width:280px;">Requested Service</td>
									<td class="bold alignCenter" style="width:auto;">Relevant Info</td>
									<td class="bold alignCenter" style="width:100px;">Result Copies To</td>
									<td class="bold alignCenter" style="width:120px;">Result Handling</td>
									<td class="bold alignCenter" style="width:150px;">Comments</td>
								</tr>';
							}
							$ARR_OBR3		= explode('^',$objParseHL7->getSegmentVal('OBR',3,$obr_cnt));
							$fill_num		= $ARR_OBR3['0'];
							$ARR_OBR4		= explode('^',$objParseHL7->getSegmentVal('OBR',4,$obr_cnt));
							$lab_ser_loinc	= $ARR_OBR4['0'];
							$lab_ser		= $ARR_OBR4['1'];
							$lab_serdb		= $lab_ser;
							$lab_ser		.= ($ARR_OBR4['8']!='') ? '<br>['.$ARR_OBR4['8'].']' : '';
							$STR_OBR7		= $objParseHL7->getSegmentVal('OBR',7); //20110531140551
							$obr_startDT	= @substr($STR_OBR7,0,4).'-'.@substr($STR_OBR7,4,2).'-'.@substr($STR_OBR7,6,2);
							$obr_startTM	= @substr($STR_OBR7,8,2).':'.@substr($STR_OBR7,10,2).':'.@substr($STR_OBR7,12,2);
							$STR_OBR8		= $objParseHL7->getSegmentVal('OBR',8);
							if($STR_OBR8==''){$STR_OBR8 = $STR_OBR7;}
							$obr_endDT		= @substr($STR_OBR8,0,4).'-'.@substr($STR_OBR8,4,2).'-'.@substr($STR_OBR8,6,2);
							$obr_endTM		= @substr($STR_OBR8,8,2).':'.@substr($STR_OBR8,10,2).':'.@substr($STR_OBR8,12,2);
							$ARR_OBR13		= explode('^',$objParseHL7->getSegmentVal('OBR',13,$obr_cnt));
							$rel_info		= $ARR_OBR13['1'];
							$res_status		= $objParseHL7->getSegmentVal('OBR',25,$obr_cnt);//result status.
							
							$ARR_OBR28		= explode('^',$objParseHL7->getSegmentVal('OBR',28,$obr_cnt));
							$copy_to		= $ARR_OBR28['5'].' '.$ARR_OBR28['1'].', '.$ARR_OBR28['2'].' '.$ARR_OBR28['3'].' '.$ARR_OBR28['4'];
							$copy_to		.= ($ARR_OBR28['0']!='' && $ARR_OBR28['12']!='') ? ' (NPI# '.$ARR_OBR28['0'].')' : '';
							if(strlen($copy_to) < 6){$copy_to = '';}
							$ARR_OBR49		= explode('^',$objParseHL7->getSegmentVal('OBR',49,$obr_cnt));
							$res_han		= $ARR_OBR49['1'];//.'/'.$ARR_OBR49['4'].'/'.$ARR_OBR49['8'];
							if($arrSegsByLine[$ln+1]=='NTE'){
								$OBRnotes			= $objParseHL7->getSegmentVal('NTE',3,$nte_cnt);
								if($OBRnotes != false)$nte_cnt++;
							}
							$decodedHL7 .= '
							<tr class="alignCenter'.$altClassOBR.'">
								<td>'.$fill_num.'</td>
								<td>'.$lab_ser.'</td>
								<td>'.$rel_info.'</td>
								<td>'.$copy_to.'</td>
								<td>'.$res_han.'</td>
								<td>'.$OBRnotes.'</td>
							</tr>
							';
							$LastSegment = $segName;
							$lab_observation_request 					= array();
							$lab_observation_request['lab_test_id']		= $new_lab_id;
							$lab_observation_request['service']			= $lab_serdb;
							$lab_observation_request['loinc']			= $lab_ser_loinc;
							$lab_observation_request['start_date']		= $obr_startDT;
							$lab_observation_request['start_time']		= $obr_startTM;
							$lab_observation_request['end_date']		= $obr_endDT;
							$lab_observation_request['end_time']		= $obr_endTM;
							$lab_observation_request['clinical_info']	= $rel_info;
							$lab_observation_request['result_copyto']	= $copy_to;
							$lab_observation_request['result_handling']	= $res_han;
							$lab_observation_request['comments']		= $OBRnotes;
							if($hl7mu_id==0){
								if($new_lab_id>0){
									$new_obr_id		= AddRecords($lab_observation_request,'lab_observation_requested');
								}
							}
							$OBRnotes = '';
							if($altClassOBR==' alt'){$altClassOBR=' white';}else{$altClassOBR=' alt';}
							$obr_cnt++;
							break;
						case 'OBX':
							$altClassOBX=='white';
							if($LastSegment != 'OBX'){
								$decodedHL7 .= '
								</table></div><br>';
								$decodedHL7 .= '<div class="sub_head">OBSERVATION RESULT</div>';
								$decodedHL7 .= '<div class="section_details">
								<table class="table table-hover table-bordered table-condensed table-striped">
									<tr class="bg1"><td class="bold">Test Name: </td><td colspan="7">'.$lab_ser.'</td></tr>
									<tr class="bg1"><td class="bold">Report Date: </td><td colspan="7">'.$resDtTime.' (Timezone: '.$res_tz.')</td></tr>
									<tr class="bg5">
										<td class="bold alignCenter" style="width:150px;">Observation</td>
										<td class="bold alignCenter" style="width:150px;">Result</td>
										<td class="bold alignCenter" style="width:150px;">UOM</td>
										<td class="bold alignCenter" style="width:150px;">Range</td>
										<td class="bold alignCenter" style="width:100px;">Abnormal Flag</td>
										<td class="bold alignCenter" style="width:100px;">Status</td>
										<td class="bold alignCenter" style="width:120px;">Date/Time</td>
										<td class="bold alignCenter" style="width:auto;">Comments</td>
									</tr>
								';
							}
							$OBX_SEG		= $objParseHL7->getSegmentVal('OBX',1);
							$ARR_OBX3		= explode('^',$objParseHL7->getSegmentVal('OBX',3,$obx_cnt));
							$lab_res_loinc	= $ARR_OBX3['0'];
							$lab_res		= $ARR_OBX3['1'];
							$res_val		= $objParseHL7->getSegmentVal('OBX',5,$obx_cnt);
							$res_valdb = $res_val;
							if(strpos($res_val,'^')>=0){
								$ARR_OBX5	= explode('^',$objParseHL7->getSegmentVal('OBX',5,$obx_cnt));
								if(strtoupper($ARR_OBX5['2'])=='SCT'){$ARR_OBX5['0']='';}
								$res_val	= $ARR_OBX5['0'].$ARR_OBX5['1'];
								$res_valdb = $res_val;
								$res_val	.= $ARR_OBX5['8']!='' ? ' ('.$ARR_OBX5['8'].')' : '';
							}
							$ARR_OBX6		= explode('^',$objParseHL7->getSegmentVal('OBX',6,$obx_cnt));
							$val_unit		= $ARR_OBX6['0'];
							$val_unit		.= ($val_unit!='' && $ARR_OBX6['1']!='') ? ' ('.$ARR_OBX6['1'].')' : '';
							$res_range		= $objParseHL7->getSegmentVal('OBX',7,$obx_cnt);
							if(strtoupper($ARR_OBX6['2'])=='SCT'){
							$val_unit		= $ARR_OBX6['1'].' ('.$ARR_OBX6['8'].')';	
							}
							$abnorm_flag	= $objParseHL7->getSegmentVal('OBX',8,$obx_cnt);
							$abnorm_flag_db = $abnorm_flag;
							if($abnorm_flag!=''){
								$TempAbnorm_flag	= $arrAbnormalFlags[$abnorm_flag];
								if($TempAbnorm_flag!=''){
									$abnorm_flag = $abnorm_flag.': '.$TempAbnorm_flag;
								}
							}
							$res_status		= $objParseHL7->getSegmentVal('OBX',11,$obx_cnt);
							$STR_OBX14		= $objParseHL7->getSegmentVal('OBX',19,$obx_cnt); //20110531140551
							$resDtTime		= @substr($STR_OBX14,4,2).'-'.@substr($STR_OBX14,6,2).'-'.@substr($STR_OBX14,0,4).' '.substr($STR_OBX14,8,2).':'.substr($STR_OBX14,10,2).':'.substr($STR_OBX14,12,2);
							$res_tz			= @substr($STR_OBX14,15,2).':'.@substr($STR_OBX14,17,2);
							$resDTdb		= @substr($STR_OBX14,0,4).'-'.@substr($STR_OBX14,4,2).'-'.@substr($STR_OBX14,6,2);
							$resTMdb		= @substr($STR_OBX14,8,2).':'.@substr($STR_OBX14,10,2).':'.@substr($STR_OBX14,12,2);
							$ARR_OBX23		= explode('^',$objParseHL7->getSegmentVal('OBX',23,$obx_cnt));
							$done_at		= $ARR_OBX23['0'];
							$ARR_OBX24		= explode('^',$objParseHL7->getSegmentVal('OBX',24,$obx_cnt));
							$done_adr		= ($ARR_OBX24['0']!='') ? ', '.$ARR_OBX24['0'] : '';
							$done_adr		.= ($ARR_OBX24['1']!='') ? ', '.$ARR_OBX24['1'] : '';
							$done_adr		.= ($ARR_OBX24['2']!='') ? ', '.$ARR_OBX24['2'] : '';
							$done_adr		.= ($ARR_OBX24['3']!='') ? ' ('.$ARR_OBX24['3'].')' : '';
							$done_adr		.= ($ARR_OBX24[	'4']!='') ? ', ZipCode- '.$ARR_OBX24['4'].'.' : '';
							$ARR_OBX25		= explode('^',$objParseHL7->getSegmentVal('OBX',25,$obx_cnt));
							$med_director	= $ARR_OBX25['5'].' '.$ARR_OBX25['1'].', '.$ARR_OBX25['2'].' '.$ARR_OBX25['3'].' '.$ARR_OBX25['4'].' (Id# '.$ARR_OBX25['0'].')';
							$med_director	= (strlen($med_director)>20) ? '<br><u>Medical Director</u>: '.$med_director : '';								
							if($arrSegsByLine[$ln+1]=='NTE'){
								$OBXnotes			= $objParseHL7->getSegmentVal('NTE',3,$nte_cnt);
								if($OBXnotes != false)$nte_cnt++;
							}
							$decodedHL7 .= '
							<tr class="'.$altClassOBX.'">
								<td class="alignCenter">'.$lab_res.'</td>
								<td class="alignCenter">'.$res_val.'</td>
								<td class="alignCenter">'.$val_unit.'</td>
								<td class="alignCenter">'.$res_range.'</td>
								<td class="alignCenter">'.$abnorm_flag.'</td>
								<td class="alignCenter">'.$res_status.'</td>
								<td class="alignCenter">'.$resDtTime.' (Timezone: '.$res_tz.')</td>
								<td>'.$OBXnotes.'</td>
							</tr>
							';
							$lab_observation_result 					= array();
							$lab_observation_result['lab_test_id']		= $new_lab_id;
							$lab_observation_result['observation']		= $lab_res;
							$lab_observation_result['result']			= $res_valdb;
							$lab_observation_result['result_loinc']		= $lab_res_loinc;
							$lab_observation_result['uom']				= $val_unit;
							$lab_observation_result['result_range']		= $res_range;
							$lab_observation_result['abnormal_flag']	= $abnorm_flag_db;
							$lab_observation_result['status']			= $res_status;
							$lab_observation_result['result_date']		= $resDTdb;
							$lab_observation_result['result_time']		= $resTMdb;
							$lab_observation_result['result_comments']	= $OBRnotes;
							if($hl7mu_id==0){
								if($new_lab_id>0){
									$new_obx_id		= AddRecords($lab_observation_result,'lab_observation_result');
								}
							}
							$OBXnotes = '';
							if($altClassOBX==' alt'){$altClassOBX=' white';}else{$altClassOBX=' alt';}

							if($arrSegsByLine[$ln+1]!='OBX'){
							$decodedHL7 .= '
							<tr class="bg5"><td class="bold" style="width:150px;">&nbsp;Test Performed At: </td><td style="width:auto;" colspan="10">'.$done_at.$done_adr.$med_director.'</td></tr>
							';
							}
							$LastSegment = $segName;
							$obx_cnt++;
							break;
						case 'SPM':
							if($LastSegment != 'SPM'){
								$decodedHL7 .= '
								</table></div><br>';
								$decodedHL7 .= '<div class="sub_head">SPECIMEN USED</div>';
								$decodedHL7 .= '<div class="section_details">
								<table class="table table-hover table-bordered table-condensed table-striped">
								';
							}
							$ARR_SPM4		= explode('^',$objParseHL7->getSegmentVal('SPM',4,spm_cnt));
							$spe_type		= $ARR_SPM4['8'];
							$spe_type		= ($spe_type=='' && $ARR_SPM4['4']!='') ? $ARR_SPM4['4'] : $spe_type;
							$spe_type		= ($spe_type=='' && $ARR_SPM4['1']!='') ? $ARR_SPM4['1'] : $spe_type;
							$STR_SPM17		= $objParseHL7->getSegmentVal('SPM',17,spm_cnt);
							$spmDtTime		= @substr($STR_SPM17,4,2).'-'.@substr($STR_SPM17,6,2).'-'.@substr($STR_SPM17,0,4).' '.substr($STR_SPM17,8,2).':'.substr($STR_SPM17,10,2).':'.substr($STR_SPM17,12,2);
							$spmDTdb		= @substr($STR_SPM17,0,4).'-'.@substr($STR_SPM17,4,2).'-'.@substr($STR_SPM17,6,2);
							$spmTMdb		= @substr($STR_SPM17,8,2).':'.@substr($STR_SPM17,10,2).':'.@substr($STR_SPM17,12,2);
							$spm_tz			= @substr($STR_SPM17,15,2).':'.@substr($STR_SPM17,17,2);
							//$spm_final_tm	= $objParseHL7->TimeByZone($spmDtTime,$spm_tz);
							
							$ARR_SPM21		= explode('^',$objParseHL7->getSegmentVal('SPM',21,spm_cnt));//rejection reason.
							$rej_text		= $ARR_SPM21['1'];
							$ARR_SPM24		= explode('^',$objParseHL7->getSegmentVal('SPM',24,spm_cnt));//specimen conditions.
							$spn_cond		= $ARR_SPM24['1'];
							$spn_cond		= ($spn_cond=='' && $ARR_SPM24['4']!='') ? $ARR_SPM24['4'] : $spn_cond;
							$spn_cond		= ($spn_cond=='' && $ARR_SPM24['8']!='') ? $ARR_SPM24['8'] : $spn_cond;			
							$decodedHL7 .= '
								<tr>
									<td class="bold" style="width:140px;">Specimen Type</td>
									<td style="width:10px;">:</td>
									<td style="width:auto;">'.$spe_type.'</td>
								</tr>
								<tr>
									<td class="bold">Collection Date/Time</td>
									<td>:</td>
									<td>'.$spmDtTime.' (Timezone: '.$spm_tz.')</td>
								</tr>';
							if($spn_cond != ''){
							$decodedHL7 .= '
								<tr>
									<td class="bold">Specimen Condition</td>
									<td>:</td>
									<td>'.$spn_cond.'</td>
								</tr>';
							}
							if($rej_text != ''){
							$decodedHL7 .= '
								<tr style="color:#ff0000">
									<td class="bold" style="color:#ff0000">Specimen Rejected</td>
									<td>:</td>
									<td>'.$rej_text.'</td>
								</tr>';
							}
							$lab_specimen  							= array();
							$lab_specimen['lab_test_id']			= $new_lab_id;
							$lab_specimen['collection_type']		= $spe_type;
							$lab_specimen['collection_start_date']	= $spmDTdb;
							$lab_specimen['collection_start_time']	= $spmTMdb;
							$lab_specimen['collection_end_date']	= $spmDTdb;
							$lab_specimen['collection_end_time']	= $spmTMdb;
							$lab_specimen['collection_condition']	= $spn_cond;
							$lab_specimen['collection_rejection']	= $rej_text;
							$lab_specimen['collection_comments']	= '';
							if($hl7mu_id==0){
								if($new_lab_id>0){
									$new_spm_id		= AddRecords($lab_specimen,'lab_specimen');
								}
							}

							$spm_cnt++;
							$LastSegment = $segName;
							break;						
					}
				}
				
			}
			$decodedHL7 .= '
			</table></div>';
			$decodedHL7 .= '</div>';
		}else{
			$status_msg = '<span class="warning clear">Invalid data submitted. Not a valid HL7 message.</span>';
		}
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
   <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Selctpicker CSS -->
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery-ui.min.1.12.1.css"/>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--> 
  	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
    <script src="<?php echo $library_path; ?>/js/jquery-ui.min.1.12.1.js" type="text/javascript" ></script>
    <!-- jQuery's Date Time Picker -->
    <script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
    <!-- Bootstrap -->
    <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
    
    <!-- Bootstrap Selectpicker -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
    <!-- Bootstrap typeHead -->


    <script>
		var JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot'];?>';
	</script>
<style type="text/css">
#upload_status_text_js,#upload_status_text_php{font-family:"Lucida Console", Monaco, monospace;}
#lab_info_decoded table tr td.bold{font-weight:bold; font-family:Calibri, Verdana, Arial;}
</style>
<script type="text/javascript" src="js_medical_history.php"></script>
<script type="text/javascript">
function validate_hl7(){
	d=document; o = $('#upload_status_text');
	o.html('');
	o.append('Checking selected HL7 file....<br>');
	if(d.f.fl.value==''){o.append('<span class="warning clear">No file selected...</span>');}
	else{
		top.show_loading_image('show');
		o.append('Reading HL7 file....<br>');
		return true;
	}
	return false;
}
</script>
</head>
<body><div class="whitebox">
<?php
if($task=='Incorporate'){
	echo $IncorporateText;
}else if($task=='Export'){
	echo $Exportext;
}else if($task=='Import' && $for=='LAB'){?>
	<form style="margin:0px;" onSubmit="return validate_hl7();" name="f" enctype="multipart/form-data" method="post">
   	<div class="row">
    	<div class="col-sm-2"><input type="file" name="file1" class="form-control"></div>
        <div class="col-sm-4"><input type="submit" name="sub" value="Import" class="btn btn-success"></div>
    </div>
    </form>
    <div class="m10" id="upload_status_text_js"></div>
    <div class="m10" id="upload_status_text_php"><?php echo @$status_msg; ?></div>
    <div id="lab_info_decoded"><?php echo $decodedHL7;?></div>
    <?php if($decodedHL7 != ''){?>
    <div class="alignCenter mt10 page_bottom_bar"><input type="button" class="dff_button" value="Close" onClick="window.top.fmain.location.href=window.top.fmain.location.href;"></div>
    <?php }?>
<?php }?>
<script type="text/javascript">	top.show_loading_image('hide');</script>
</div>
</body>
</html>