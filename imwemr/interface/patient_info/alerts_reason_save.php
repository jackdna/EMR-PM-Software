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
File: alerts_reason_save.php
Purpose: Saves patient alerts
Access Type: Direct 
*/
//include_once("../../main/Functions.php");
include_once("../../config/globals.php");
$allElementArray = $_REQUEST["reason_ids"];
$allElementCount=count($_REQUEST["reason_ids"]);
$patientId = $_SESSION['patient'];
$currentOprator = $_SESSION['authId'];
$rqAdminButtonType = $_REQUEST["adminButtonType"];
$rqCancelAlert=$_REQUEST['cancel_pt_alert'];
$bl = false;
//die("hello".$rqAdminButtonType);
if($allElementCount>0){
	
	foreach($allElementArray as $keyFieldName=>$Fieldvalue){
		
		/*if($_POST["reason_text_".$Fieldvalue]!=""){
			$insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
			 form_id='".$_POST['patientCurrentFormId']."',reason='".addslashes($_POST["reason_text_".$Fieldvalue])."',
			 alert_date=now(),alertId='".$Fieldvalue."',reason_from='".$_POST['frm']."' 
			 where patient_id='".$patientId."' and alertId='".$Fieldvalue."'";
			$res=imw_query($insertQuery);
		}*/		
		if(($_REQUEST["reason_sel_".$Fieldvalue]!="" && $_REQUEST["reason_text_".$Fieldvalue]!="") || ($rqAdminButtonType == "Administered") || ($rqAdminButtonType == "Decline") || ($rqAdminButtonType == "InsertRecall") || ($rqAdminButtonType == "InsertReschedule" && $_REQUEST['adminPatstrGetAtFirst'] == "yes")){			
			$resonSelVal = $scpResonId = $scpResonCode = $actionPerform = $intAdminPatFrequenctId = "";
			$arrResonSelVal = array();
			$resonSelVal = $_REQUEST["reason_sel_".$Fieldvalue];
			if($resonSelVal!="other"){
				$arrResonSelVal = explode("-",$resonSelVal);
				$scpResonId = $arrResonSelVal[0];
				$scpResonCode = $arrResonSelVal[1];
				$actionPerform = "Decline";
			}
			else{
				$scpResonCode = "other";
				$actionPerform = "Decline";
			}
			$strReason = "";
			if($rqAdminButtonType == "Decline"){
				//$strReason = "Decline";
				//$strReason = $_REQUEST['txtAreaCommentDecline'];
				$strReason = addslashes($_REQUEST["reason_text_".$Fieldvalue]);
				$intAdminPatFrequenctId = $_REQUEST["adminPatFrequenctId_".$Fieldvalue];
				$actionPerform = "Decline";
			}
			elseif($rqAdminButtonType == "InsertRecall"){
				$strReason = "InsertRecall";
				$intAdminPatFrequenctId = $_REQUEST["adminPatFrequenctId_".$Fieldvalue];
				$actionPerform = "InsertRecall";
			}
			elseif($rqAdminButtonType == "InsertReschedule"){
				$strReason = "InsertReschedule";
				$intAdminPatFrequenctId = $_REQUEST["adminPatFrequenctId_".$Fieldvalue];
				$actionPerform = "InsertReschedule";
			}
			elseif($rqAdminButtonType == "Administered"){
				$strReason = "Administered";
				$intAdminPatFrequenctId = $_REQUEST["adminPatFrequenctId_".$Fieldvalue];
				$actionPerform = "Administered";
			}
			 $insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
			 form_id='".$_REQUEST['patientCurrentFormId']."',reason='".addslashes($strReason)."',
			 scp_reson_id='".addslashes($scpResonId)."',scp_reson_code='".addslashes($scpResonCode)."',
			 alert_date='".date("Y-m-d H:i:s")."',alertId='".$Fieldvalue."',reason_from='".$_REQUEST['frm']."' 
			 where patient_id='".$patientId."' and alertId='".$Fieldvalue."' and patient_frequency_id = '".$intAdminPatFrequenctId."'";
			$res=imw_query($insertQuery);
			
			##########
			$next_frequency_date = $boolReschedule = $beforeRescheduleDate = "";
			if($rqAdminButtonType == "InsertReschedule" && $_REQUEST['txtRescheduleDate'] != ""){
				$rescheduleDate = "";
				$rescheduleDate = $_REQUEST['txtRescheduleDate'];
				list($month, $day, $year) =  explode("-",trim($rescheduleDate));
				$rescheduleDate = $year."-".$month."-".$day;
				$next_frequency_date = "'".$rescheduleDate."'";
				$alert_shown_status = "0";
				$boolReschedule = "1";
				$beforeRescheduleDate = date("Y-m-d");
			}
			else{
				$next_frequency_date = "CURDATE()";
				$alert_shown_status = "1";
				$boolReschedule = "0";
				$beforeRescheduleDate = "0000-00-00";
			}
			#########333
			$qryGetPatFrequency = "select id from patient_frequency where alert_id='".$Fieldvalue."' and patient_id='".$patientId."' and alert_shown_status = '0' ORDER BY id LIMIT 1";
			$rsGetPatFrequency = imw_query($qryGetPatFrequency);
			if($rsGetPatFrequency){
				if(imw_num_rows($rsGetPatFrequency) > 0){
					$rowGetPatFrequency = imw_fetch_array($rsGetPatFrequency);
					$patFrequencyId = $rowGetPatFrequency['id'];
					if($patFrequencyId && $resonSelVal){
						$qryUpdatePatFrequency = "update patient_frequency set alert_shown_status = '".$alert_shown_status."', date_time = NOW(), next_frequency_date = ".$next_frequency_date.", action_perform = '".$actionPerform."', bool_reschedule = '".$boolReschedule."', before_reschedule_date = '".$beforeRescheduleDate."' where id = '".$patFrequencyId."'";	
						imw_query($qryUpdatePatFrequency);
					}
				}
			}
			
			
			$qryGetPatFrequency = "select id,frequency,frequency_type,alert_shown_status,next_frequency_date,bool_reschedule from patient_frequency where alert_id = '".$Fieldvalue."' and patient_id = '".$patientId."' ORDER BY id";												
			$rsGetPatFrequency = imw_query($qryGetPatFrequency);
			if($rsGetPatFrequency){				
				if(imw_num_rows($rsGetPatFrequency) > 0){					
					$nextAlertToShow = "";						
					$intMainLoopCounter = 1;
					$totRow = imw_num_rows($rsGetPatFrequency);
					$arrFreqDate = array();
					$blDBFreqGet = false;
					
					while($rowGetPatFrequency = imw_fetch_array($rsGetPatFrequency)){						
						$patFreqId = $patFreqAlertShowSta =	$patFreqVal = $patFreqType = $patFreqNxtDate = $patFreqResStatus = "";
						
						$patFreqId = (int)$rowGetPatFrequency['id'];
						$patFreqAlertShowSta = (int)$rowGetPatFrequency['alert_shown_status'];
						$patFreqVal = (int)$rowGetPatFrequency['frequency'];
						$patFreqType = (int)$rowGetPatFrequency['frequency_type'];	
						$patFreqResStatus = (int)$rowGetPatFrequency['bool_reschedule'];	
											
						//die($patFreqVal."sssssssss");
						if($patFreqAlertShowSta == 1 && $blDBFreqGet == false){
							$patFreqNxtDate = $rowGetPatFrequency['next_frequency_date'];														
							$nextAlertToShow = $patFreqNxtDate;
							$blDBFreqGet = true;
							$intMainLoopCounter++;
							continue;
						}
						elseif($patFreqAlertShowSta == 0 && $blDBFreqGet == false && $patFreqResStatus == 1){
							$patFreqNxtDate = $rowGetPatFrequency['next_frequency_date'];														
							$nextAlertToShow = $patFreqNxtDate;
							$blDBFreqGet = true;
							$intMainLoopCounter++;
							continue;
						}
						//var_dump($blDBFreqGet);
						//echo $intMainLoopCounter;
						if($patFreqNxtDate == ""){
							$patFreqNxtDate = $nextAlertToShow;
						}
						if($patFreqNxtDate){
							list($year, $month, $day) = explode('-',$patFreqNxtDate);														
							if($patFreqType == 1){								
								$nextAlertToShow = "";
								$nextAlertToShow = date('Y-m-d', mktime(0, 0, 0, $month + $patFreqVal, $day, $year));								
							}
							elseif($patFreqType == 2){																		
								$nextAlertToShow = "";
								$nextAlertToShow = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year + $patFreqVal));
							}					
						}
						/*for($intCounter = 1; $intCounter <= $totRow; $intCounter++){
							if(trim($patFreqNxtDate) != ""){			
								if($intCounter > 2){
									$patFreqNxtDate = $arrFreqDate[$intCounter-1];
								}													
								
								list($year, $month, $day) = explode('-',$patFreqNxtDate);														
								if($patFreqType == 1){								
									$nextAlertToShow = "";
									$nextAlertToShow = date('Y-m-d', mktime(0, 0, 0, $month + $patFreqVal, $day, $year));								
								}
								elseif($patFreqType == 2){																		
									$nextAlertToShow = "";
									$nextAlertToShow = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year + $patFreqVal));
								}
								
								if($intCounter == 1){
									$arrFreqDate[$intCounter] = $patFreqNxtDate;	 
								}
								else{
									$arrFreqDate[$intCounter] = $nextAlertToShow;	 	
								}
							}								
						}*/
						//echo '<pre>';
						//print_r($arrFreqDate);
						//die;
						//$nextAlertToShow = $arrFreqDate[$intMainLoopCounter];
						if($patFreqId && $nextAlertToShow){
							$qryUpdatePatFrequency = "update patient_frequency set next_frequency_date = '".$nextAlertToShow."' where id = '".$patFreqId."' and alert_shown_status = '0' and next_frequency_date = '0000-00-00'";																	
							imw_query($qryUpdatePatFrequency);
						}							
						$intMainLoopCounter++;
					}
				}
			}	
			##########
			if($rqAdminButtonType == "InsertRecall"){
				$procid = $recall_m = $descs = $recall_date = $procedureName = $strReason = "";
				$procid 		= trim($_REQUEST['cbProcedure']);	
				$recall_m 		= trim($_REQUEST['cbRecallMonth']);	
				$descs 			= trim(nl2br($_REQUEST['txtAreaCommentRecall']));
				$recall_date 	= date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));
				$procedureName 	= trim($_REQUEST["adminPatSiteCarename_".$Fieldvalue]);	
				$strReason		= "Insert Recall for site care plan ".trim($_REQUEST["adminPatSiteCarename_".$Fieldvalue]);			
				$qryInsertRecall = "insert into patient_app_recall set 
									descriptions='".$descs."',
									recall_months='".$recall_m."',
									operator='".$_SESSION['authId']."',
									procedure_id='".$procid."',
									patient_id='".$patientId."',
									recalldate='".$recall_date."',
									procedure_name = '".$procedureName."',
									reason = '".$strReason."',
									current_date1=NOW()";
									
				$rsInsertRecall = imw_query($qryInsertRecall);		
			}
		}		
		elseif($rqAdminButtonType == "Cancel"){
			//$_SESSION['alertShowForThisSession'] = "Cancel";
			$_SESSION['alertShowForThisSession'] .= ",".$rqCancelAlert;
		}	
		elseif($rqAdminButtonType == "InsertReschedule" && $_REQUEST['txtRescheduleDate'] != "" && $_REQUEST['adminPatstrGetAtFirst'] == "no"){
			$rescheduleDate = "";
			$rescheduleDate = $_REQUEST['txtRescheduleDate'];
			list($month, $day, $year) =  explode("-",trim($rescheduleDate));
			$rescheduleDate = $year."-".$month."-".$day;
			$actionPerform = "InsertReschedule";
			$qryUpdateSCPNxtFreq = "update patient_frequency set before_reschedule_date = next_frequency_date, next_frequency_date = '".$rescheduleDate."',bool_reschedule = '1',action_perform = '".$actionPerform."' where id = '".$intAdminPatFrequenctId."'";
			$rsUpdateSCPNxtFreq = imw_query($qryUpdateSCPNxtFreq);
			
			$resonSelVal = $scpResonId = $scpResonCode = "";
			$arrResonSelVal = array();
			$resonSelVal = $_REQUEST["reason_sel_".$Fieldvalue];
			if($resonSelVal!="other"){
				$arrResonSelVal = explode("-",$resonSelVal);
				$scpResonId = $arrResonSelVal[0];
				$scpResonCode = $arrResonSelVal[1];
			}
			else{
				$scpResonCode = "other";
			}
			$strReason = "";
			if($rqAdminButtonType == "Decline"){
				$strReason = "Decline";
			}
			elseif($rqAdminButtonType == "InsertRecall"){
				$strReason = "InsertRecall";
			}
			elseif($rqAdminButtonType == "InsertReschedule"){
				$strReason = "InsertReschedule";
			}			
			else{
				$strReason = addslashes($_REQUEST["reason_text_".$Fieldvalue]);	
			}
			$insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
			 form_id='".$_REQUEST['patientCurrentFormId']."',reason='".$strReason."',
			 scp_reson_id='".addslashes($scpResonId)."',scp_reson_code='".addslashes($scpResonCode)."',
			 alert_date=now(),alertId='".$Fieldvalue."',reason_from='".$_REQUEST['frm']."' 
			 where patient_id='".$patientId."' and alertId='".$Fieldvalue."'";
			//$res=imw_query($insertQuery);
		}		
		
	}
}
$patientSpecificFrm = "";
if($_REQUEST["disablePatAlertThisSession"] == 'yes'){
	//$_SESSION['alertShowForThisSession']="Cancel";
	$_SESSION['alertShowForThisSession'] .= ",".$rqCancelAlert;
}
else if($_REQUEST["patientSpecificFrm"]){
	$patientSpecificFrm = $_REQUEST["patientSpecificFrm"];
	$currentOpratorInt = "";
	$dateTimeWtOpInt = "";
	$quyGetCurrentOpratorInt = "select CONCAT_WS('',substr(fname,1,1),substr(lname,1,1)) as operatorInitial from users where id = '".$currentOprator."'";
	$rsGetCurrentOpratorInt  = imw_query($quyGetCurrentOpratorInt);
	if($rsGetCurrentOpratorInt){
		if(imw_num_rows($rsGetCurrentOpratorInt)){
			$rowGetCurrentOpratorInt = imw_fetch_array($rsGetCurrentOpratorInt);
			$currentOpratorInt = $rowGetCurrentOpratorInt['operatorInitial'];
			$dateTimeWtOpInt = get_date_format(date("Y-m-d")).' '.date("h:i A").' - '.$currentOpratorInt;			
		}
	}
	$quySelectAlert = "select alertId,alert_to_show_under,alert_showed,alert_disable_date_time_initial from alert_tbl where patient_id = '".$patientId."' and is_deleted = '0'";
	$rsSelectAlert = imw_query($quySelectAlert);
	if($rsSelectAlert){
		while($row = imw_fetch_array($rsSelectAlert)){
			$alertId = "";
			$alertToShowUnder = "";
			$alertToShowdFor = "";
			$alertDisableDateTimeInitial = "";
			$arrAlertToShowUnder = array();
			$arrAlertToShowdFor = array();
			$arrAlertDisableDateTimeInitial = array();
			$alertId = $row['alertId'];
			$alertToShowUnder = $row['alert_to_show_under'];
			$alertToShowdFor = $row['alert_showed'];
			$alertDisableDateTimeInitial = $row['alert_disable_date_time_initial'];
			$arrAlertToShowUnder = explode(",",$alertToShowUnder);
			$arrAlertToShowdFor = explode(",",$alertToShowdFor);
			$arrAlertDisableDateTimeInitial = explode(",",$alertDisableDateTimeInitial);
			if(count($arrAlertToShowUnder)>0){
				if($arrAlertToShowUnder[0] == 1 && $patientSpecificFrm == "CN"){
					$Comefrm = "Chart Note";
					$FDAlertToShowdFor = ($arrAlertToShowdFor[1]) ? $arrAlertToShowdFor[1] : "0";
					$ACAlertToShowdFor = ($arrAlertToShowdFor[2]) ? $arrAlertToShowdFor[2] : "0";
					$alertToShowdFor = "1,".$FDAlertToShowdFor.",".$ACAlertToShowdFor;
					
					$FDAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[1]) ? $arrAlertDisableDateTimeInitial[1] : "";
					$ACAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[2]) ? $arrAlertDisableDateTimeInitial[2] : "";					
					$alertDisableDateTimeInitial = "MD ".$dateTimeWtOpInt.",".$FDAlertDisableDateTimeInitial.",".$ACAlertDisableDateTimeInitial;
					
					$quyUpdateAlert = "Update alert_tbl set alert_showed = '".$alertToShowdFor."',alert_disable_date_time_initial = '".$alertDisableDateTimeInitial."', is_deleted='1' where alertId = '".$alertId."'";
					$rsUpdateAlert = imw_query($quyUpdateAlert);
					$insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
								 form_id='".$_SESSION['form_id']."',reason='OK',
								 alert_date=now(),alertId='".$alertId."',reason_from='".$Comefrm."' 
								 where patient_id='".$patientId."' and alertId='".$alertId."'";
					$res=imw_query($insertQuery);

				}
				elseif($arrAlertToShowUnder[2] == 1 && $patientSpecificFrm == "AC"){
					$Comefrm = "Accounting";
					$CNAlertToShowdFor = ($arrAlertToShowdFor[0]) ? $arrAlertToShowdFor[0] : "0";
					$FDAlertToShowdFor = ($arrAlertToShowdFor[1]) ? $arrAlertToShowdFor[1] : "0";
					$alertToShowdFor = $CNAlertToShowdFor.",".$FDAlertToShowdFor.",1";
					
					$CNAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[0]) ? $arrAlertDisableDateTimeInitial[0] : "";
					$FDAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[1]) ? $arrAlertDisableDateTimeInitial[1] : "";					
					$alertDisableDateTimeInitial = $CNAlertDisableDateTimeInitial.",".$FDAlertDisableDateTimeInitial.",AC ".$dateTimeWtOpInt;
					
					$quyUpdateAlert = "Update alert_tbl set alert_showed = '".$alertToShowdFor."',alert_disable_date_time_initial = '".$alertDisableDateTimeInitial."', is_deleted='1' where alertId = '".$alertId."'";
					$rsUpdateAlert = imw_query($quyUpdateAlert);
					$insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
								 form_id='".$_SESSION['form_id']."',reason='OK',
								 alert_date=now(),alertId='".$alertId."',reason_from='".$Comefrm."' 
								 where patient_id='".$patientId."' and alertId='".$alertId."'";
					$res=imw_query($insertQuery);

				}
				elseif($arrAlertToShowUnder[1] == 1 && $patientSpecificFrm == "SCH"){
					$Comefrm = "Scheduler";
					$CNAlertToShowdFor = ($arrAlertToShowdFor[0]) ? $arrAlertToShowdFor[0] : "0";					
					$ACAlertToShowdFor = ($arrAlertToShowdFor[2]) ? $arrAlertToShowdFor[2] : "0";
					$alertToShowdFor = $CNAlertToShowdFor.",1".",".$ACAlertToShowdFor;
					
					$CNAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[0]) ? $arrAlertDisableDateTimeInitial[0] : "";
					$ACAlertDisableDateTimeInitial = ($arrAlertDisableDateTimeInitial[2]) ? $arrAlertDisableDateTimeInitial[2] : "";					
					$alertDisableDateTimeInitial = $CNAlertDisableDateTimeInitial.",FD ".$dateTimeWtOpInt.",".$ACAlertDisableDateTimeInitial;
					
					$quyUpdateAlert = "Update alert_tbl set alert_showed = '".$alertToShowdFor."',alert_disable_date_time_initial = '".$alertDisableDateTimeInitial."', is_deleted='1' where alertId = '".$alertId."'";
					$rsUpdateAlert = imw_query($quyUpdateAlert);
					$insertQuery="update  alert_tbl_reason set patient_id='".$patientId."',operator='".$_SESSION['authId']."',
								 form_id='".$_SESSION['form_id']."',reason='OK',
								 alert_date=now(),alertId='".$alertId."',reason_from='".$Comefrm."' 
								 where patient_id='".$patientId."' and alertId='".$alertId."'";
					$res=imw_query($insertQuery);

				}
			}
		}
	}
}
?>
<script language="javascript">
<?php if($_REQUEST["disablePatAlertThisSession"] != 'yes'){?>if(typeof(top.update_toolbar_icon)!='undefined'){top.update_toolbar_icon();}<?php }?>
//parent.parent.parent.top.document.getElementById('alert_mask').style.display="none";
</script>