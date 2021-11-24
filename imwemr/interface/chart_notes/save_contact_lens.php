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
include_once("../../config/globals.php");
include_once("cl_functions.php");


$arrForm=$_POST;
$sheetscount=$arrForm['sheetscount'];
$patient_id=$_SESSION["patient"];
$provider_id=$_SESSION['authUserID'];
$arrEyes=array('OD','OS');
$arrCLWS_ID=array();
$arrReturn=array();

//DELETE SUB SHEETS
if($arrForm['delSubSheets']!=''){
	$arrTemp=explode(',', $arrForm['delSubSheets']);
	foreach($arrTemp as $id){
		if($id>0){
			$arrDelIds[$id]=$id;
		}
	}unset($arrTemp);
	if(sizeof($arrDelIds)>0){
		$strDelIds=implode(',', $arrDelIds);
		$qry="Delete FROM contactlensworksheet_det WHERE id IN(".$strDelIds.")";
		$rs=imw_query($qry);
		unset($rs);
	}
}
if($arrForm['odOSame'] == "false"){
    /* $newCLMakeId = ((int)$arrForm['maxCLMakeId']) + 1;
    $contactLensMakeIdQuery = "insert into contactlensemake
    (make_id, manufacturer_id, style, cpt_fee_id, del_status, source) value(".$newCLMakeId.",'0','".$arrForm['elemMakeOS1']."','51','0','1')";
    imw_query($contactLensMakeIdQuery);
    $arrForm['elemMakeOS1ID'] = $newCLMakeId; */
}

$commentSheetArray = array();
// For updating contact lens comments
for($i=1; $i<=$sheetscount; $i++){
	
	if($arrForm["dos".$i]!=''){ //THIS IS ALSO VERIFYING IF SHEET EXIST(IT MAY BE DELETED) OR NOT AT FRONT END. 
		$where=$qryPart='';
		$clws_id=$arrForm['clws_id'.$i];
	
		$insertUpdate="Insert INTO ";
		$qryPart="
		patient_id='".$patient_id."',
		form_id='".$arrForm['form_id']."',
		provider_id='".$provider_id."',	
		";
	
		if($clws_id>0){
			$qryPart='';
			$insertUpdate="Update";
			$where=" WHERE clws_id='".$clws_id."'";

			//UPDATE PROVIDER ID ONLY IF SHEET FORM ID AND CHARTNOTE FORM ID IS SAME.
			$current_form_id = ($_SESSION['finalize_id']!='')? $_SESSION['finalize_id'] :$_SESSION['form_id'] ;
			$rs=imw_query("Select form_id FROM contactlensmaster WHERE clws_id='".$clws_id."'");
			$res=imw_fetch_assoc($rs);
			$sheet_from_id=$res['form_id'];

			if($current_form_id==$sheet_from_id){
				$qryPart="provider_id='".$provider_id."',";	
			}
		}

		//echo "----------------------------------<br />";
		//pre($clws_id);
		//echo "----------------------------------<br />";

		$dos = getDateFormatDB($arrForm["dos".$i]);

		/******* Get appointment provider *******/
		$prescribedBy = 0;
		$providerQuery = "select sa_doctor_id from schedule_appointments where sa_patient_id='".$patient_id."' and sa_app_start_date='".$dos."' limit 0, 1";
		$providerResult = imw_query($providerQuery) or die(imw_error()." - ".$providerQuery);
		if($providerResult && imw_num_rows($providerResult) > 0){
			$providerRow = imw_fetch_assoc($providerResult);
			$prescribedBy = $providerRow['sa_doctor_id'];
		}else{
			$prescribedBy = $_SESSION['authId'];
		}

		//UPDATE CL-REQ CHECKBOX VALUE IN chart_master_table
		$cl_order = (isset($arrForm['cl_order'.$i]) && $arrForm['cl_order'.$i]!='') ? 1 : 0;
		imw_query("UPDATE chart_master_table SET cl_order = '".$cl_order ."' WHERE id = '".$arrForm['form_id']."'");
			
		//MASTER DATA
		$qry= $insertUpdate." contactlensmaster SET ".
		$qryPart."
		dos='".$dos."',
		clws_savedatetime='".date('Y-m-d H:i:s')."',
		clGrp='OU',
		clws_type='".addslashes($arrForm['clws_types'.$i])."',
		clws_trial_number='".$arrForm['clws_trial_number'.$i]."',
		cpt_evaluation_fit_refit='".$arrForm['cpt_evaluation_fit_refit'.$i]."',
		cl_comment='".addslashes($arrForm['comments'.$i])."',
		charges_id='".$arrForm['charges_id'.$i]."',
		AverageWearTime='".addslashes($arrForm['AverageWearTime'.$i])."',
		Solutions='".addslashes($arrForm['Solutions'.$i])."',
		Age='".$arrForm['Age'.$i]."',
		DisposableSchedule='".addslashes($arrForm['DisposableSchedule'.$i])."',
		usage_val='".addslashes($arrForm['usage_val'.$i])."',
		allaround='".addslashes($arrForm['allaround'.$i])."',
		wear_scheduler='".addslashes($arrForm['wear_scheduler'.$i])."',
		replenishment='".addslashes($arrForm['replenishment'.$i])."',
		disinfecting='".addslashes($arrForm['disinfecting'.$i])."',
		CLHXDOS	='".$dos."'";
		if(trim(strtolower($insertUpdate)) !== "update"){
			$qry .= ",prescribed_by='".$prescribedBy."'";
		}

		$qry .= " ".$where;

		$rs=imw_query($qry);
		if($clws_id<=0){
			$clws_id=imw_insert_id();
		}
		$arrCLWS_ID[$i]=$clws_id;
		
		/*********NEW HL7 ENGINE START************/
		$hl7_res_clm = imw_query("SELECT form_id FROM contactlensmaster WHERE clws_id = '".$clws_id."' AND clws_type LIKE '%Final%' LIMIT 0,1");
		if($hl7_res_clm && imw_num_rows($hl7_res_clm)==1){
			$hl7_rs_clm = imw_fetch_assoc($hl7_res_clm);
			if(!empty($hl7_rs_clm['form_id'])){
				require_once(dirname(__FILE__)."/../../hl7sys/api/class.HL7Engine.php");
				$objHL7Engine = new HL7Engine();
				$objHL7Engine->application_module = 'workview';
				if(!$objHL7Engine->check_new_old_msg_for_same_sourceid($clws_id,'ZMS'))$objHL7Engine->msgSubType = 'add_prescription'; else $objHL7Engine->msgSubType = 'update_prescription';
				$objHL7Engine->source_id = $clws_id;
				$objHL7Engine->ZMSgivenMR = 'CL';
				$objHL7Engine->generateHL7();
				unset($objHL7Engine);
			}
		}
		/*********NEW HL7 ENGINE END*************/
		
		//$commentSheetArray[$i][$clws_id][] = ;

		//FILL IN DETAILS
		if($rs && $clws_id > 0){
			// Delete all reocrds before procedding
			imw_query("delete from cl_comments where cl_sheet_id='".$clws_id."' and delete_status = 0 ");

			// Add Records
			if(count($_REQUEST["comment_new_column".$i]) > 0){
				foreach($_REQUEST["comment_new_column".$i] as $n){
					$tempComment = addslashes(trim($n));
					if(strlen($tempComment) > 0){
						$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment, created_by) values('".$clws_id."', '".$tempComment."', '".$_SESSION['authId']."')";
						imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
					}
				}
			}


			if(count($_REQUEST["comment_update_".$i]) > 0){
				foreach($_REQUEST["comment_update_".$i] as $n){
					$tempComment = addslashes(trim($n));
					if(strlen($tempComment) > 0){
						$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment, created_by) values('".$clws_id."', '".$tempComment."', '".$_SESSION['authId']."')";
						imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
					}
				}
			}

			// $arrMerge = array_merge(array_filter($_REQUEST["comment_new_column".$i]), array_filter($_REQUEST["comment_update_".$i]));
			// pre($arrMerge);
			
			// if(count($arrMerge) > 0){
			// 	array_unique($arrMerge);
			// 	pre($arrMerge);
			// 	foreach($arrMerge as $n){
			// 		$tempComment = addslashes(trim($n));
			// 		if(strlen($tempComment) > 0){
			// 			$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment, created_by) values('".$clws_id."', '".$tempComment."', '".$_SESSION['authId']."')";
			// 			imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
			// 		}
			// 	}
			// }

			// foreach($_REQUEST as $x => $y){
			// 	if(strpos($x, "comment_update_") !== false){
			// 		$valArr = explode("_", $y);
			// 		$tempSheetId = $valArr[2];
			// 		$tempCommentId = $valArr[3];
			// 		$tempComment = addslashes(trim($y));
			// 		if(strlen($tempComment) > 0){
			// 			$clCommentsUpdateQuery = "update cl_comments set comment = '".$tempComment."' where id = '".$tempCommentId."' and cl_sheet_id = '".$clws_id."')";
			// 			imw_query($clCommentsUpdateQuery) or die(imw_error()." - ".$clCommentsUpdateQuery);
			// 			//echo $clCommentsUpdateQuery."\n\n";
			// 		}
			// 	}
			// 	//die;
			// }

			/* if($_REQUEST["recordSave" . $i] == ""){
				foreach($_REQUEST["comment_new_column".$i] as $x => $y){
					$tempComment = addslashes(trim($y));
					if(strlen($tempComment) > 0){
						$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment) values('".$arrCLWS_ID[$i]."', '".$tempComment."')";
						imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
					}
				}
				if(is_array($_REQUEST["comment_new_column".$i])){
					foreach($_REQUEST["comment_new_column".$i] as $n){
						$tempComment = addslashes(trim($n));
						if(strlen($tempComment) > 0){
							$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment) values('".$clws_id."', '".$tempComment."')";
							imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
							$insertedCommentId = imw_insert_id();
							$commentSheetArray[$i][$clws_id][] = $insertedCommentId;
						}
					}
				}
			}
			else if($_REQUEST["recordSave" . $i] == "saveTrue")
			{
				imw_query("delete from cl_comments where cl_sheet_id='".$clws_id."'");
				if(is_array($_REQUEST["comment_new_column".$i])){
					foreach($_REQUEST["comment_new_column".$i] as $n){
						$tempComment = addslashes(trim($n));
						if(strlen($tempComment) > 0){
							$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment) values('".$clws_id."', '".$tempComment."')";
							imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
						}
					}
				}
				foreach($_REQUEST as $x => $y){
					if(strpos($x, "comment_update_") !== false){
						$valArr = explode("_", $y);
						$tempSheetId = $valArr[2];
						$tempCommentId = $valArr[3];
						$tempComment = addslashes(trim($y));
						if(strlen($tempComment) > 0){
							$clCommentsInsertQuery = "insert into cl_comments(cl_sheet_id, comment) values('".$clws_id."', '".$tempComment."')";
							imw_query($clCommentsInsertQuery) or die(imw_error()." - ".$clCommentsInsertQuery);
						}
					}
				}
			} */
			$tempCheckOdOs=array();
			$arrDet_ids=array();
			foreach($arrEyes as $eye){
	
				$where=$qryPart='';
				$eyeL=strtolower($eye);
				$eye1Cap=ucfirst($eyeL);
			
				$subSheets=$arrForm['subsheets'.$eye.$i];

				if(empty($subSheets)===true){
					$subSheets=1;
				}
	
				for($s=1; $s<=$subSheets; $s++){
					$id=0;
					$sub=$where='';
					if($s>1){ $sb=$s-1; $sub='_'.$sb; }
	
					$insertUpdate="Insert INTO ";
					if($arrForm['detId'.$eye.$i.$sub]>0){
						$insertUpdate="Update";
						$where=" WHERE id='".$arrForm['detId'.$eye.$i.$sub]."'";
						$id=$arrForm['detId'.$eye.$i.$sub];
						$arrDet_ids[$id]=$id;
					}
					
					if($arrForm['clType'.$eye.$i.$sub]=='scl'){	
						//if(!empty($arrForm['sig_dataapp_scl_'.$eyeL.'_drawing'.$i.$sub])){
						//	$arrForm['elem_SCL'.$eye1Cap.'DrawingPath'.$i.$sub] = saveCLDrwing($arrForm['sig_dataapp_scl_'.$eyeL.'_drawing'.$i.$sub], $arrForm['sig_imgapp_scl_'.$eyeL.'_drawing'.$i.$sub], $eyeL);
						//}
						if(($arrForm['elemMake'.$eye.$i.$sub.'ID']=='' || $arrForm['elemMake'.$eye.$i.$sub.'ID']==0) && strlen(trim($arrForm['elemMake'.$eye.$i.$sub]))>0){
							$arrForm['elemMake'.$eye.$i.$sub.'ID']=getManufID(trim($arrForm['elemMake'.$eye.$i.$sub]));
						}
						$qryPart="
						SclBcurve".$eye."='".addslashes($arrForm['elemBc'.$eye.$i.$sub])."',
						SclDiameter".$eye."='".addslashes($arrForm['elemDiameter'.$eye.$i.$sub])."',
						Sclsphere".$eye."='".addslashes($arrForm['elemSphere'.$eye.$i.$sub])."',
						SclCylinder".$eye."='".addslashes($arrForm['elemCylinder'.$eye.$i.$sub])."',
						Sclaxis".$eye."='".addslashes($arrForm['elemAxis'.$eye.$i.$sub])."',
               			Sclcolor".$eye."='".addslashes($arrForm['elemColor'.$eye.$i.$sub])."',
						SclAdd".$eye."='".addslashes($arrForm['elemAdd'.$eye.$i.$sub])."',
						SclDva".$eye."='".addslashes($arrForm['elemDva'.$eye.$i.$sub])."',
						SclNva".$eye."='".addslashes($arrForm['elemNva'.$eye.$i.$sub])."',
						SclType".$eye."='".addslashes($arrForm['elemMake'.$eye.$i.$sub])."',
						SclType".$eye."_ID='".addslashes($arrForm['elemMake'.$eye.$i.$sub.'ID'])."',";

						
						
/*						if($eye=='OD' || $eye=='OS'){
							$qryPart.=",
							elem_SCL".$eye1Cap."Drawing='".$arrForm['elem_SCL'.$eye1Cap.'Drawing'.$i.$sub]."',
							hdSCL".$eye1Cap."DrawingOriginal='".$arrForm['hdSCL'.$eye1Cap.'DrawingOriginal'.$i.$sub]."',
							elem_SCL".$eye1Cap."DrawingPath='".$arrForm['elem_SCL'.$eye1Cap.'DrawingPath'.$i.$sub]."',
							corneaSCL_".$eyeL."_desc='".$arrForm['corneaSCL_'.$eyeL.'_desc'.$i.$sub]."'";
						}*/

}else if($arrForm['clType'.$eye.$i.$sub]=='rgp' || $arrForm['clType'.$eye.$i.$sub]=='rgp_soft' || $arrForm['clType'.$eye.$i.$sub]=='rgp_hard'){
						
						

						if($arrForm['elemMake'.$eye.$i.$sub.'ID']=='' && strlen(trim($arrForm['elemMake'.$eye.$i.$sub]))>0){
							$arrForm['elemMake'.$eye.$i.$sub.'ID']=getManufID(trim($arrForm['elemMake'.$eye.$i.$sub]));
						}
						$qryPart="
						RgpBC".$eye."='".addslashes($arrForm['elemBc'.$eye.$i.$sub])."',
						RgpDiameter".$eye."='".addslashes($arrForm['elemDiameter'.$eye.$i.$sub])."',
						RgpCylinder".$eye."='".addslashes($arrForm['elemCylinder'.$eye.$i.$sub])."',
						RgpAxis".$eye."='".addslashes($arrForm['elemAxis'.$eye.$i.$sub])."',
						RgpOZ".$eye."='".addslashes($arrForm['elemOZ'.$eye.$i.$sub])."',
						RgpCT".$eye."='".addslashes($arrForm['elemCT'.$eye.$i.$sub])."',
						RgpPower".$eye."='".addslashes($arrForm['elemSphere'.$eye.$i.$sub])."',
						RgpColor".$eye."='".addslashes($arrForm['elemColor'.$eye.$i.$sub])."',
						RgpAdd".$eye."='".addslashes($arrForm['elemAdd'.$eye.$i.$sub])."',
						RgpDva".$eye."='".addslashes($arrForm['elemDva'.$eye.$i.$sub])."',
						RgpNva".$eye."='".addslashes($arrForm['elemNva'.$eye.$i.$sub])."',
						RgpType".$eye."='".addslashes($arrForm['elemMake'.$eye.$i.$sub])."',
						RgpType".$eye."_ID='".addslashes($arrForm['elemMake'.$eye.$i.$sub.'ID'])."',";

					}else if($arrForm['clType'.$eye.$i.$sub]=='cust_rgp'){
						if($arrForm['elemMake'.$eye.$i.$sub.'ID']=='' && strlen(trim($arrForm['elemMake'.$eye.$i.$sub]))>0){
							$arrForm['elemMake'.$eye.$i.$sub.'ID']=getManufID(trim($arrForm['elemMake'.$eye.$i.$sub]));
						}
						$qryPart="
						RgpCustomBC".$eye."='".addslashes($arrForm['elemBc'.$eye.$i.$sub])."',
						RgpCustomDiameter".$eye."='".addslashes($arrForm['elemDiameter'.$eye.$i.$sub])."',
						RgpCustomCylinder".$eye."='".addslashes($arrForm['elemCylinder'.$eye.$i.$sub])."',
						RgpCustomAxis".$eye."='".addslashes($arrForm['elemAxis'.$eye.$i.$sub])."',
						RgpCustomOZ".$eye."='".addslashes($arrForm['elemOZ'.$eye.$i.$sub])."',
						RgpCustomCT".$eye."='".addslashes($arrForm['elemCT'.$eye.$i.$sub])."',
						RgpCustomPower".$eye."='".addslashes($arrForm['elemSphere'.$eye.$i.$sub])."',
						RgpCustom2degree".$eye."='".addslashes($arrForm['elemTwoDegree'.$eye.$i.$sub])."',
						RgpCustom3degree".$eye."='".addslashes($arrForm['elemThreeDegree'.$eye.$i.$sub])."',
						RgpCustomPCW".$eye."='".addslashes($arrForm['elemPCW'.$eye.$i.$sub])."',
						RgpCustomColor".$eye."='".addslashes($arrForm['elemColor'.$eye.$i.$sub])."',
						RgpCustomBlend".$eye."='".addslashes($arrForm['elemBlend'.$eye.$i.$sub])."',
						RgpCustomEdge".$eye."='".addslashes($arrForm['elemEdge'.$eye.$i.$sub])."',
						RgpCustomAdd".$eye."='".addslashes($arrForm['elemAdd'.$eye.$i.$sub])."',
						RgpCustomDva".$eye."='".addslashes($arrForm['elemDva'.$eye.$i.$sub])."',
						RgpCustomNva".$eye."='".addslashes($arrForm['elemNva'.$eye.$i.$sub])."',
						RgpCustomType".$eye."='".addslashes($arrForm['elemMake'.$eye.$i.$sub])."',
						RgpCustomType".$eye."_ID='".addslashes($arrForm['elemMake'.$eye.$i.$sub.'ID'])."',";
					
					}else if($arrForm['clType'.$eye.$i.$sub]=='prosthesis' || $arrForm['clType'.$eye.$i.$sub]=='no-cl'){
						//THEN DO NOTHING
					}

					//DRAWING DATA FOR FIRST OD/OS
					if(!$tempCheckOdOs[$eyeL]){
						$qryPart.="
						idoc_drawing_id='".$arrForm['idoc_drawing_id_'.$eyeL.$i]."',
						corneaSCL_od_desc='".addslashes($arrForm['description_A_'.$eyeL.$i])."',
						corneaSCL_os_desc='".addslashes($arrForm['description_B_'.$eyeL.$i])."',";
						$tempCheckOdOs[$eyeL]=$eyeL;
					}
					//FILL OU VALUE
					if($s==1){
						$qryPart.="
						SclNvaOU='".$arrForm['elemNvaOU'.$i]."',
						SclDvaOU='".$arrForm['elemDvaOU'.$i]."',";
					}
					
					$qry= $insertUpdate." contactlensworksheet_det SET ".$qryPart." clws_id='".$clws_id."', clEye='".$eye."', clType='".$arrForm['clType'.$eye.$i.$sub]."' ".$where;
					$rs=imw_query($qry);
					
					if($id<=0){
						$id=imw_insert_id();
						$arrDet_ids[$id]=$id;
					}
					
					$i_temp=$i-1;
					$s_temp=$s-1;
					$arrReturn[$i_temp][$eye][$s_temp]=$id;
				}
			}

			//DELETE OLD PEVIOUS ADDED SUB SHEETS OF THIS CL SHEET. THIS IS ADDED TO AVOID ISSUE IN CASE OF "COPY FROM" CASE WHERE NUMBER OF OD/OS DOES NOT MATCH.
			if(sizeof($arrDet_ids)>0){
				$arrPreviousIds=array();
				$strDet_ids=implode(',',$arrDet_ids);
				$qry="Select id FROM contactlensworksheet_det WHERE id NOT IN(".$strDet_ids.") AND clws_id='".$clws_id."'";
				$rs=imw_query($qry);
				while($res=imw_fetch_assoc($rs)){
					$arrPreviousIds[$res['id']]=$res['id'];
				}
				unset($rs);
				
				if(sizeof($arrPreviousIds)>0){
					$strPreviousIds=implode(',', $arrPreviousIds);
					$qry="Delete FROM contactlensworksheet_det WHERE id IN(".$strPreviousIds.")";
					$rs=imw_query($qry);
					unset($rs);								
				}
			}			
		}
		//FILL EVALUATIONS
		$qry=$qryPart=$where='';
		$insertUpdate="Insert INTO";
		$rs=imw_query("Select id FROM contactlens_evaluations WHERE clws_id='".$clws_id."'");
		if(imw_num_rows($rs)>0){
			$insertUpdate="Update";
			$where=" WHERE clws_id='".$clws_id."'";		
		}	
		foreach($arrEyes as $eye){
			$eyeL=strtolower($eye);
			$eye1Cap=ucfirst($eyeL);

			if($arrForm['clType'.$eye.$i]=='scl'){
				if(trim($arrForm['elemPositionOther'.$eye.$i])=='Other')$arrForm['elemPositionOther'.$eye.$i]='';
				$qryPart.="
				CLSLCEvaluationSphere".$eye."='".addslashes($arrForm['elemDvaSphere'.$eye.$i])."',
				CLSLCEvaluationCylinder".$eye."='".addslashes($arrForm['elemDvaCylinder'.$eye.$i])."',
				CLSLCEvaluationAxis".$eye."='".addslashes($arrForm['elemDvaAxis'.$eye.$i])."',
				CLSLCEvaluationDVA".$eye."='".addslashes($arrForm['elemEvalDva'.$eye.$i])."',
				CLSLCEvaluationSphereNVA".$eye."='".addslashes($arrForm['elemNvaSphere'.$eye.$i])."',
				CLSLCEvaluationCylinderNVA".$eye."='".addslashes($arrForm['elemNvaCylinder'.$eye.$i])."',
				CLSLCEvaluationAxisNVA".$eye."='".addslashes($arrForm['elemNvaAxis'.$eye.$i])."',
				CLSLCEvaluationNVA".$eye."='".addslashes($arrForm['elemEvalNva'.$eye.$i])."',
				CLSLCEvaluationComfort".$eye."='".addslashes($arrForm['elemComfort'.$eye.$i])."',
				CLSLCEvaluationMovement".$eye."='".addslashes($arrForm['elemMovement'.$eye.$i])."',
				CLSLCEvaluationCondtion".$eye."='".addslashes($arrForm['elemCondition'.$eye.$i])."',
				CLSLCEvaluationPosition".$eye."='".addslashes($arrForm['elemPosition'.$eye.$i])."',
				CLSLCEvaluationPositionOther".$eye."='".addslashes($arrForm['elemPositionOther'.$eye.$i])."',";
			}
			if($arrForm['clType'.$eye.$i]=='rgp' || $arrForm['clType'.$eye.$i]=='rgp_soft' || $arrForm['clType'.$eye.$i]=='rgp_hard' || $arrForm['clType'.$eye.$i]=='cust_rgp'){
				if(trim($arrForm['elemPositionBOther'.$eye.$i])=='Other')$arrForm['elemPositionBOther'.$eye.$i]='';
				if(trim($arrForm['elemPositionAOther'.$eye.$i])=='Other')$arrForm['elemPositionAOther'.$eye.$i]='';
				$qryPart.="
				CLRGPEvaluationSphere".$eye."='".addslashes($arrForm['elemDvaSphere'.$eye.$i])."',
				CLRGPEvaluationCylinder".$eye."='".addslashes($arrForm['elemDvaCylinder'.$eye.$i])."',
				CLRGPEvaluationAxis".$eye."='".addslashes($arrForm['elemDvaAxis'.$eye.$i])."',
				CLRGPEvaluationDVA".$eye."='".addslashes($arrForm['elemEvalDva'.$eye.$i])."',
				CLRGPEvaluationSphereNVA".$eye."='".addslashes($arrForm['elemNvaSphere'.$eye.$i])."',
				CLRGPEvaluationCylinderNVA".$eye."='".addslashes($arrForm['elemNvaCylinder'.$eye.$i])."',
				CLRGPEvaluationAxisNVA".$eye."='".addslashes($arrForm['elemNvaAxis'.$eye.$i])."',
				CLRGPEvaluationNVA".$eye."='".addslashes($arrForm['elemEvalNva'.$eye.$i])."',
				CLRGPEvaluationComfort".$eye."='".addslashes($arrForm['elemComfort'.$eye.$i])."',
				CLRGPEvaluationMovement".$eye."='".addslashes($arrForm['elemMovement'.$eye.$i])."',
				CLRGPEvaluationPosBefore".$eye."='".addslashes($arrForm['elemPositionB'.$eye.$i])."',
				CLRGPEvaluationPosBeforeOther".$eye."='".addslashes($arrForm['elemPositionBOther'.$eye.$i])."',
				CLRGPEvaluationPosAfter".$eye."='".addslashes($arrForm['elemPositionA'.$eye.$i])."',
				CLRGPEvaluationPosAfterOther".$eye."='".addslashes($arrForm['elemPositionAOther'.$eye.$i])."',
				CLRGPEvaluationFluoresceinPattern".$eye."='".addslashes($arrForm['elemFLPatter'.$eye.$i])."',
				CLRGPEvaluationInverted".$eye."='".addslashes($arrForm['elemInverted'.$eye.$i])."',";
				
				//FILL DRAWING OVER REFRACTION
/*				$qryPart.="
				elem_conjunctiva".$eye1Cap."Drawing='".$arrForm['elem_conjunctiva'.$eye1Cap.'Drawing'.$i]."',
				hdConjunctiva".$eye1Cap."DrawingOriginal='".$arrForm['hdConjunctiva'.$eye1Cap.'DrawingOriginal'.$i]."',
				elem_conjunctiva".$eye1Cap."DrawingPath='".$arrForm['elem_conjunctiva'.$eye1Cap.'DrawingPath'.$i]."',
				cornea_".$eyeL."_desc='".$arrForm['cornea_'.$eyeL.'_desc'.$i]."',";
*/				
				//$qryPart.="
				//idoc_drawing_id_".$eyeL."='".$arrForm['rgp_idoc_drawing_id_'.$eyeL.$i]."',";
				//DESCRIPTION
				//if($eye=='OD'){
				//	$qryPart.="
				//	cornea_od_desc='".$arrForm['rgp_description_od_A'.$i]."',
				//	cornea_os_desc='".$arrForm['rgp_description_od_B'.$i]."',";
				//}
			}
			$qryPart.="
			EvaluationRotation".$eye."='".addslashes($arrForm['elemRotation'.$eye.$i])."',";
		}
		//FILL OU VALUES
		$qryPart.="CLSLCEvaluationDVAOU='".$arrForm['elemEvalDvaOU'.$i]."',
				   CLSLCEvaluationNVAOU='".$arrForm['elemEvalNvaOU'.$i]."',";
				   
		$qryPart=substr($qryPart, 0, strlen($qryPart)-1);
		$qry=$insertUpdate." contactlens_evaluations SET clws_id='".$clws_id."',".$qryPart.$where;
		$rs=imw_query($qry);
	}
}



$arrCLWS_IDVsCol=$arrCLWS_ID;
rsort($arrCLWS_ID);
echo json_encode(array('arrCLWS_IDVsCol'=>$arrCLWS_IDVsCol, 'latestClws_id'=>$arrCLWS_ID[0], 'arrReturn'=>$arrReturn));

?>