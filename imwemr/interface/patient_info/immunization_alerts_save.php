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
File: immunization_alerts_save.php
Purpose: Save immunization alerts
Access Type: Direct 
*/
//include_once("../../main/Functions.php");
include_once("../../config/globals.php");
$allElementArray = $_POST["immnzn_ids"];

$allElementCount=count($_POST["immnzn_ids"]);

$currentOprator = $_SESSION['authId'];
$rqImmButtonType = $_REQUEST["immButtonType"];

if($allElementCount>0){
	foreach($allElementArray as $keyFieldName=>$Fieldvalue){
		//if($_POST["imnznReason_".$Fieldvalue]!=""){
		if(($_POST["reason_sel_".$Fieldvalue]!="" && $_POST["imnznReason_".$Fieldvalue]!="") || ($rqImmButtonType == "Decline") || ($rqImmButtonType == "InsertRecall")){	
			$resonSelVal = $scpResonId = $scpResonCode = "";
			$arrResonSelVal = array();
			$resonSelVal = $_POST["reason_sel_".$Fieldvalue];
			if($resonSelVal!="other"){
				$arrResonSelVal = explode("-",$resonSelVal);
				$scpResonId = $arrResonSelVal[0];
				$scpResonCode = $arrResonSelVal[1];
			}
			else{
				$scpResonCode = "other";
			}
			
			$strReason = $strReasonTxt = $strImmStatus = "";
			if($rqImmButtonType == "Decline"){
				//$strReasonTxt = $_REQUEST['immTxtAreaCommentDecline'];
				$resonSelVal = $_POST["reason_sel_".$Fieldvalue];
				if($resonSelVal!="other"){
					$arrResonSelVal = explode("-",$resonSelVal);
					$scpResonId = $arrResonSelVal[0];
					$scpResonCode = $arrResonSelVal[1];
				}
				else{
					$scpResonCode = "other";
				}
				$strReasonTxt = addslashes($_POST["imnznReason_".$Fieldvalue]);	
				$strImmStatus = "Decline";
			}
			elseif($rqImmButtonType == "InsertRecall"){
				$strReasonTxt = "InsertRecall";	
				$strImmStatus = "InsertRecall";				
			}
			else{				
				$strReasonTxt = addslashes($_POST["imnznReason_".$Fieldvalue]);	
				$strImmStatus = "Administered";
			}
			$intNewPatImmId = 0;
			$ins = "patient_id='".$_SESSION['patient']."',administered_date=now(),immunization_id='".addslashes($_POST["immnznName_".$Fieldvalue])."',manufacturer='".addslashes($_POST["immnznManufacturer_".$Fieldvalue])."',lot_number='NA',administered_by_id='".$_SESSION['authId']."',education_date=now(),note='".addslashes($_POST["imnznReason_".$Fieldvalue])."',create_date=now(),update_date=now(),created_by='".$_SESSION['authId']."',updated_by='".$_SESSION['authId']."',immzn_type='".addslashes($_POST["immnzntype_".$Fieldvalue])."',immzn_dose='".addslashes($_POST["immnznDose_".$Fieldvalue])."',immzn_route_site='NA',expiration_date='',consent_date='',adverse_reaction='NA',imnzn_id ='".$Fieldvalue."',immzn_dose_id ='".$_POST["doseid_".$Fieldvalue]."',status='NotGiven',scpStatus='".$strImmStatus."',scp_alert_id='".addslashes($_POST["scpAlertId_".$Fieldvalue])."',administered_time = CURTIME() ";
			$immuznSaveQry = imw_query("insert into immunizations SET " .$ins	);
			$intNewPatImmId = imw_insert_id();
			
			$insertQuery="insert into immunizations_alerts set patient_id='".$_SESSION['patient']."',operator_id='".$_SESSION['authId']."',
							form_id='".$_SESSION['form_id']."', alert_reason='".$strReasonTxt."',alert_status='NotGiven', 
							alert_date=now(),immnzn_id='".$Fieldvalue."',dose_id='".$_POST["doseid_".$Fieldvalue]."',dose_due_date='".$_POST["doseduedate_".$Fieldvalue]."',
							scp_reson_id='".addslashes($scpResonId)."',scp_reson_code='".addslashes($scpResonCode)."',pat_Immu_Id='".$intNewPatImmId."'
							";
			$res=imw_query($insertQuery);
			
			if($rqImmButtonType == "InsertRecall"){
				$procid = $recall_m = $descs = $recall_date = $procedureName = $strReason = "";
				$procid 	= trim($_REQUEST['immCbProcedure']);	
				$recall_m 	= trim($_REQUEST['immCbRecallMonth']);	
				$descs 		= trim(nl2br($_REQUEST['immTxtAreaCommentRecall']));
				$recall_date = date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));
				$procedureName 	= trim($_REQUEST["immPatSiteCarename_".$Fieldvalue]);	
				$strReason		= "Insert Recall for site care plan ".trim($_REQUEST["immPatSiteCarename_".$Fieldvalue]);	
				$qryInsertRecall = "insert into patient_app_recall set 
									descriptions='".$descs."',
									recall_months='".$recall_m."',
									operator='".$_SESSION['authId']."',
									procedure_id='".$procid."',
									patient_id='".$_SESSION['patient']."',
									recalldate='".$recall_date."',
									procedure_name = '".$procedureName."',
									reason = '".$strReason."',
									current_date1=NOW()";
									
				$rsInsertRecall = imw_query($qryInsertRecall);					
			}
		}
		elseif($rqImmButtonType == "Cancel"){
			$_SESSION['alertImmShowForThisSession'] = "Cancel";
		}		
	}
}
?>