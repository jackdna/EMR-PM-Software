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
File: AssessPlan.php
Purpose: This file provides chartnote Assessment and plans of a patient visit.
Access Type : Include file
*/
?>
<?php
//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');

class AssessPlan extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;		
	}
	
	public function getAssessPlan(){
		
		//?reqModule=chart_notes&service=getAssessPlan&phyId=209&patId=55603&form_id=40171
		include_once($GLOBALS['srcdir']."/classes/work_view/ChartAP.php");
		include_once($GLOBALS['srcdir']."/classes/work_view/Fu.php");
		/* require_once $GLOBALS["incdir"]."/chart_notes/common/ChartAP.php";
		require_once $GLOBALS["incdir"]."/chart_notes/common/functions.php";
		require_once $GLOBALS["incdir"]."/chart_notes/fu_functions.php"; */
		
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		$chartApObj = New ChartAP($patient_id, $form_id);
		$fuObj = New Fu($patient_id, $form_id);
		
		// Chart Assessment and plan -----

		$arrMainRet=array();
		$lenAssess=5; //default value

		//$strPrvPlanPop="";//

		//Follow Up ------------
		$arrFuVals = array();
		$lenFu = 0;
		//Follow Up ------------

		$sql = "SELECT * FROM chart_assessment_plans WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
		$row = sqlQuery($sql);	
		
		if(($row == false)){ //&& ($finalize_flag == 0)
			// New
			$elem_assessId = 0;
			$elem_editModeAssess = "0";
			//New Records
			$row = $chartApObj->valuesNewRecordsAssess($patient_id," * ", "0", 1);
			$new_row = true;
		}else{
			// Update
			$elem_assessId = $row["id"];
			$elem_editModeAssess = "1";			
		}

		if($row != false){
		
			$elem_followUpNumber = $row["follow_up_numeric_value"];			
			$elem_followUp = $row["follow_up"];
			$elem_retina = $row["retina"];
			$elem_neuroOptha = $row["neuro_ophth"];
			$elem_doctorName = $row["doctor_name"];	
			$elem_sign       = $row["sign_path"];		
			$elem_idPrecautions = $row["id_precation"];
			$elem_rdPrecautions = $row["rd_precation"];
			$elem_lidScrubs = $row["lid_scrubs_oint"];
			$elem_patientUnderstands = $row["patient_understands"];
			$elem_patientUnderstandsSelect = $row["patient_understands_select"];
			$elem_notes = stripslashes($row["plan_notes"]);
			$elem_continue_meds = $row["continue_meds"];
			$elem_followUpVistType  = $row["followUpVistType"];
			$elem_scribedBy = $row["scribedBy"];
			$elem_follow_up = $row["followup"];
			$commentsForPatient = stripslashes($row["commentsForPatient"]);
			$strXml = stripslashes($row["assess_plan"]);
			
			$elem_resiHxReviewd=$row["resiHxReviewd"];
			$elem_rxhandwritten=$row["rxhandwritten"];
			$elem_labhandwritten=$row["labhandwritten"];
			$elem_radhandwritten=$row["imagehandwritten"];
			$elem_modi_note_Asses=$row["modi_note_Asses"];
			$modi_note_AssesArr=$row["modi_note_AssesArr"];
			$elem_consult_reason=$row["consult_reason"];		
			$surgical_ocular_hx =($row['surgical_ocular_hx'] == NULL)?"":$row['facility_name'];
			$surgical_ocular_hx = utf8_decode($surgical_ocular_hx);
			//Set Assess Plan Resolve NE Value FROM xml ---
			//$oChartApXml = new ChartAp($patient_id,$form_id);
			//$arrApVals = $chartApObj->getVal_Str($strXml);
			$arrApVals = $chartApObj->getVal();
			$arrAp = $arrApVals["data"]["ap"];				
			$lenAssess = count($arrAp);
			$arrApTmp=array();
			//*
			for($i=0;$i<$lenAssess;$i++){
				$j=$i+1;
				$elem_assessment = "elem_assessment".$j;
				$$elem_assessment = $vAssess = $arrAp[$i]["assessment"];
				$elem_plan = "elem_plan".$j;
				$vPlan = $$elem_plan = $arrAp[$i]["plan"];
				$vPlan = utf8_decode($vPlan);
				$elem_resolve = "elem_resolve".$j;
				$vResolve = $$elem_resolve = $arrAp[$i]["resolve"];
				$no_change_Assess = "no_change_".$j;
				$vNE = $$no_change_Assess = $arrAp[$i]["ne"];
				$elem_apConMeds = "elem_apConMeds".$j; //Continue meds
				//$arrApTmp["conmed"][] = $$elem_apConMeds = $arrAp[$i]["conmed"];
				$vEye = $arrAp[$i]["eye"];
				if(empty($vEye)){ $vEye="NA"; }
				
				//--
				$dxcode = '';$dx_class = '';
				if(!empty($vAssess)){
					$lastChar = substr(trim($vAssess), -1);					
					if($lastChar == ")"){
						preg_match("/\([^\(]*$/",trim($vAssess),$arr_match);
						$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
						$arrAssSplit = preg_split("/\([^\(]*$/",$vAssess);
						$vAssess = $arrAssSplit[0];
						$laterlity = substr(trim($dxcode),-1);
						/*
						if($dxcode != "" && $laterlity == '-'){
							$dx_class = 'mandatory';
						}
						*/
					}
				}	
				if(empty($vPlan) || $vPlan=NULL){ $vPlan= array();}
				//--				
				
				$arrApTmp[]=array("assessment"=>"".$vAssess, "dxcode"=>"".$dxcode, "plan"=>"".$vPlan, 
								"resolve"=>"".$vResolve, "NE"=>"".$vNE, "eye"=>"".$vEye );
				
			}
			//*/

			//--- Set Assess Plan Resolve NE Value FROM xml

			/// Empty Plan and Signature
			if($elem_editModeAssess == "0"){

				//Empty AP elements---
				$elem_sign = "";
				$elem_planAll = "";		
				$elem_followUpNumber = $elem_followUp = $elem_retina = $elem_neuroOptha = $elem_doctorName = "";
				$elem_idPrecautions = $elem_rdPrecautions = $elem_lidScrubs = $elem_patientUnderstands = $elem_continue_meds = $elem_patientUnderstandsSelect = "";		
				$elem_followUpVistType= "";
				$elem_scribedBy = "";
				$elem_follow_up = "";
				$commentsForPatient="";
				$elem_resiHxReviewd = "";
				$elem_labhandwritten="";
				$elem_radhandwritten="";
				$elem_rxhandwritten = "";
				$elem_modi_note_Asses="";
				$elem_consult_reason="";
				//17-09-2112: Comments from the comment box  at bottom left corner of WorkView should be carry forward.
				//$elem_notes = "";
				
				//---Empty AP elements
				
				//Set Values
				$arrApTmp=array();
				$cntrLenAssess = 1;
				$text_dataTmp = "";
				for($i=0,$j=1;$i<$lenAssess;$i++){

					//Check for empty records---
					if(empty($arrAp[$i]["assessment"]) && empty($arrAp[$i]["plan"]) &&
						empty($arrAp[$i]["resolve"]) && empty($arrAp[$i]["ne"])
						){
						continue;
					}
					//---Check for empty records

					if(($arrAp[$i]["resolve"] != 1) || ($arrAp[$i]["ne"] == 1)){
						$varAssessment = "elem_assessment".($j);
						$vAssess = $$varAssessment = $arrAp[$i]["assessment"];
						$varNotes = "elem_plan".($j);
						$vPlan = $$varNotes = ($arrAp[$i]["ne"] == 1) ? $arrAp[$i]["plan"] : "" ;
						$vPlan = utf8_decode($vPlan);
						$varPlanResolve = "elem_resolve".($j);
						$vResolve = $$varPlanResolve = $arrAp[$i]["resolve"]; 
						$varNe = "no_change_".($j);
						$vNE = $$varNe = $arrAp[$i]["ne"];
						$varCFNotes = "elem_CFPlan".($j);
						$$varCFNotes = $arrAp[$i]["plan"];
						$varapConMeds = "elem_apConMeds".$j; //Continue meds
						//$arrApTmp["conmed"][] = $$varapConMeds = $arrAp[$i]["conmed"]; 	
						$vEye = $arrAp[$i]["eye"];
						if(empty($vEye)){ $vEye="NA"; }
						
						//--
						$dxcode = '';$dx_class = '';
						if(!empty($vAssess)){
							$lastChar = substr(trim($vAssess), -1);					
							if($lastChar == ")"){
								preg_match("/\([^\(]*$/",trim($vAssess),$arr_match);
								$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
								$arrAssSplit = preg_split("/\([^\(]*$/",$vAssess);
								$vAssess = $arrAssSplit[0];
								$laterlity = substr(trim($dxcode),-1);
								/*
								if($dxcode != "" && $laterlity == '-'){
									$dx_class = 'mandatory';
								}
								*/
							}
						}	
						//--
						
						$arrApTmp[]=array("assessment"=>"".$vAssess, "dxcode"=>"".$dxcode, "plan"=>"".$vPlan, 
										"resolve"=>"".$vResolve, "NE"=>"".$vNE, "eye"=>"".$vEye );
						
						
						//Need to Set Eye values: working here --
						/*
						$varOu = "elem_apOu".($j);
						$varOd = "elem_apOd".($j);
						$varOs = "elem_apOs".($j);
						$$varOu = $$varOd = $$varOs = "";
						if($arrAp[$i]["eye"]=="OU"){
							$$varOu = "1";
						}else if($arrAp[$i]["eye"]=="OD"){
							$$varOd = "1";
						}else if($arrAp[$i]["eye"]=="OS"){
							$$varOs = "1";
						}			
						*/
						
						//if($arrNE[$i] == 1){
						//	$varScheduleId = "elem_scheduleId".($j); //This is replaced by order set
							//$varTSxSumm = "divTSxSumm".($j);
							//Insert Into Schedule carried Forward SX/TEST
						//$$varScheduleId = getPrevScheduleId($j,$i+1,$patient_id,$form_id,$arrAp[$i]["ne"]); // replaced by order set
						//}
						//echo "<br>"."I: ".$i.",J: ".$j.",".$varNotes.": ".$$varNotes;
						//echo ", ".$varCFNotes." : ".$$varCFNotes;
						/*
						if(!empty($arrAp[$i]["assessment"])){
							$tmpAssess = chk2remdx($arrAp[$i]["assessment"]);
							if(!empty($elem_ccHx) && strpos($elem_ccHx,$tmpAssess)!==false){
								//if Already in CC Hx and do not add again
							}else{					
								$text_dataTmp .= "\n".$tmpAssess." "; // Add assessments
							}
						}
						*/

						//reset plan num in order //$patient_id,$form_id
						$this->ap_resetOrderNum($i+1,$j, $patient_id,$form_id);
						
						//reset plan num in order
						
						$j++;
						$cntrLenAssess++;						
						
					}
				}

				/*
				//Add Assemsnt in CCHx
				$text_dataTmp = trim(substr($text_dataTmp,0,-1));//trim(substr($text_dataTmp,0,-2));
				//$text_data .= ($text_dataTmp != "") ? "\r\n".$text_dataTmp : "";				

				//Decrease in LenAssess so that less assessments are made
				//Only done in New Chart
				//Plus one for empty row - will do later
				$lenAssess = ($cntrLenAssess>=5) ? $cntrLenAssess : 5;
				*/
			}			

			//Follow Up ------------
			if(!empty($elem_follow_up)){
				list($lenFu, $arrFuVals) = $fuObj->fu_getXmlValsArr($elem_follow_up);
				for($x=0; $x<$lenFu;$x++){						
					if(!empty($arrFuVals[$x]["provider"])){
						$arrFuVals[$x]["provider"]="".$this->getPersonnal3($arrFuVals[$x]["provider"]);
					}				
				}				
			}
			//Follow Up ------------
			
			
			if(count($arrApTmp)==0){
				$arrApTmp="";
			}
			if(count($arrFuVals) ==0){
				$arrFuVals="";
			}
			
			//
			$arrMainRet["AssessPlan"]=$arrApTmp;
			$arrMainRet["FU"]=$arrFuVals;
			$arrMainRet["Other"]=array("Transition_of_care"=>"".$elem_doctorName, 
									"Retina"=>"".$elem_retina, 
									"Neuro"=>"".$elem_neuroOptha, 
									"Consult_Reason"=>"".$elem_consult_reason,
									"ScribedBy"=>"".$this->getPersonnal3($elem_scribedBy),
									"Comments"=>"".$elem_notes,
									"Comments_requested_by_Patient"=>$commentsForPatient,
									"Surgical_Ocular_Hx"=>$surgical_ocular_hx,
									"Sign"=>$elem_sign);

		}
		
		if(!isset($arrMainRet["AssessPlan"])){$arrMainRet["AssessPlan"]="";}
		if(!isset($arrMainRet["FU"])){$arrMainRet["FU"]="";}
		if(!isset($arrMainRet["Other"])){$arrMainRet["Other"]=array();}		
		
		//
		return $arrMainRet; 
		
	}
	
	function getPersonnal3($id){
		$nm = getUserFirstName($id,2);
		if(is_array($nm) && count($nm)>3) return $nm[3];
		return '';
	}
	
	function ap_resetOrderNum($oldNum,$newNum,$patient_id,$form_id){
		$sql = "SELECT * FROM `order_set_associate_chart_notes` WHERE plan_num='".$oldNum."' AND form_id='".$form_id."' AND patient_id = '".$patient_id."'";
		$row = sqlQuery($sql);
		if($row != false){						
			$sql = "UPDATE order_set_associate_chart_notes SET plan_num='".$newNum."' WHERE plan_num='".$oldNum."' AND form_id='".$form_id."' AND patient_id = '".$patient_id."' ";
			$row = sqlQuery($sql);
		}				
	}
}

?>