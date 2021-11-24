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

class RVS extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;		
	}
	
	public function getRVS(){		
		
		
		
		require_once $GLOBALS["incdir"]."/chart_notes/common/functions.php";
		require_once $GLOBALS["incdir"]."/main/main_functions.php";
		
		
		
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		
		//--
		$sql = "SELECT * FROM chart_left_provider_issue WHERE patient_id = '".$this->patient_id."' AND form_id = '".$this->form_id."' ";
		
		$row = sqlQuery($sql);
		if(($row == false)){ //&& ($finalize_flag == 0)
			// New
			$elem_prIsId = "0";
			$elem_editModePrIs = "0";
			// Past
			$row = valNewRecordLeftView($patient_id);
		}else{
			// Update
			$elem_prIsId = $row["pr_is_id"];
			$elem_editModePrIs = "1";
		}
		if($row != false){
			//Rvs
			if($elem_editModePrIs == "1"){
				$complaint1StrDB = $row["complaint1Str"];
				$complaint2StrDB = $row["complaint2Str"];
				$complaint3StrDB = $row["complaint3Str"];
				$complaintHeadDB = $row["complaintHead"];
				$selectedHeadDB = $row["selectedHeadText"];
				$titleHeadDB = $row["titleHead"];
				$lidsYesNo = trim($row["lidsYesNo"]);
				$noFlashing = $row["noFlashing"];
				$noFloaters = $row["noFloaters"];
				$vpDistance = $row["vpDistance"];
				
				$THAYesNo = $row["THAYesNo"];

				$vpDistance = separateOtherRvs($vpDistance,1);
				//list($arr_vpDis,$elem_vpDisOther) = separateOtherRvs($vpDistance);

				$vpMidDistance = $row["vpMidDistance"];
				$vpMidDistance = separateOtherRvs($vpMidDistance,1);
				//list($arr_vpMidDis,$elem_vpMidDisOther) = separateOtherRvs($vpMidDistance);

				$vpNear = $row["vpNear"];
				$vpNear = separateOtherRvs($vpNear,1);
				//list($arr_vpNear,$elem_vpNearOther) = separateOtherRvs($vpNear);

				$vpGlare = $row["vpGlare"];
				$arr_vpGlare = explode(",",$row["vpGlare"]);

				$vpOther = $row["vpOther"];
				$vpOther = separateOtherRvs($vpOther,1);
				//list($arr_vpOther,$elem_vpOtherOther) = separateOtherRvs($vpOther);

				$irrLidsExternal = $row["irrLidsExternal"];
				$irrLidsExternal = separateOtherRvs($irrLidsExternal,1);
				//list($arr_irrLidsExt,$elem_irrLidsExtOther) = separateOtherRvs($irrLidsExternal);

				$sepType="<+type%$+>";
				$irrOcular = $row["irrOcular"];
				$arrTemp = explode($sepType,$irrOcular);
				$irrOcularTemp=separateOtherRvs($arrTemp[0],1);
				$elem_irrOcuItchingType=$arrTemp[1];
				$elem_irrOcuPresSensType=$arrTemp[2];
				if(!empty($elem_irrOcuItchingType)){  $irrOcularTemp = str_replace("Itching", "Itching ".$elem_irrOcuItchingType.",",  $irrOcularTemp);  }
				if(!empty($elem_irrOcuPresSensType)){ $irrOcularTemp = str_replace("Pressure sensation", "Pressure sensation ".$elem_irrOcuPresSensType.",",  $irrOcularTemp);  }				
				//list($arr_irrOcu,$elem_irrOcuOther) = separateOtherRvs($irrOcularTemp);				
				

				$psSpots = $row["psSpots"];
				$elem_postSegSpots = $psSpots;

				$sepFloat="<+Float*&+>";
				$psFloaters = $row["psFloaters"];
				$arrTemp = explode($sepFloat,$psFloaters);
				$psFloatTemp = $arrTemp[0];
				$arr_postSegFloat=explode(",",$arrTemp[0]);
				$elem_postSegFloatCobwebs=$arrTemp[1];
				$elem_postSegFloatBlackSpots=$arrTemp[2];
				if(!empty($elem_postSegFloatCobwebs)){ $psFloatTemp = str_replace("Cobwebs", "Cobwebs ".$elem_postSegFloatCobwebs.",",  $psFloatTemp);  }
				if(!empty($elem_postSegFloatBlackSpots)){ $psFloatTemp = str_replace("Black spots", "Black spots ".$elem_postSegFloatBlackSpots.",",  $psFloatTemp);  }
				

				$sepFL = "<+FL@^+>";
				$psFlashingLights = $row["psFlashingLights"];
				$arrTemp = explode($sepFL,$psFlashingLights);
				$psFLTemp=$arrTemp[0];
				$arr_postSegFL=explode(",",$psFLTemp);
				$elem_postSegFLSparks=$arrTemp[1];
				$elem_postSegFLBolts=$arrTemp[2];
				$elem_postSegFLArcs=$arrTemp[3];
				$elem_postSegFLStrobe=$arrTemp[4];
				if(!empty($elem_postSegFLSparks)){$psFLTemp=str_replace("Sparks,", "Sparks ".$elem_postSegFLSparks.",",  $psFLTemp); }
				if(!empty($elem_postSegFLBolts)){$psFLTemp=str_replace("Lightening Bolts,", "Lightening Bolts ".$elem_postSegFLBolts.",",  $psFLTemp); }
				if(!empty($elem_postSegFLArcs)){$psFLTemp=str_replace("Arcs lasting seconds,", "Arcs lasting seconds ".$elem_postSegFLArcs.",",  $psFLTemp); }
				if(!empty($elem_postSegFLStrobe)){$psFLTemp=str_replace("Strobe lights many minutes or longer,", "Strobe lights many minutes or longer ".$elem_postSegFLStrobe.",",  $psFLTemp); }
				
				

				$psAmslerGrid = $row["psAmslerGrid"];
				$psAmslerGrid = separateOtherRvs($psAmslerGrid,1);
				//list($arr_postSegAmsler,$elem_postSegAmslerOther) = separateOtherRvs($psAmslerGrid);

				$neuroDblVision = $row["neuroDblVision"];
				$arr_neuroDblVis = explode(",",$neuroDblVision);

				$neuroTempArtSymp = $row["neuroTempArtSymp"];
				$neuroTempArtSymp =separateOtherRvs($neuroTempArtSymp,1);
				//list($arr_neuroTAS,$elem_neuroTASOther) = separateOtherRvs($neuroTempArtSymp);
				
				$neuroVisionLoss = $row["neuroVisionLoss"];
				$neuroVisionLoss = separateOtherRvs($neuroVisionLoss,1);
				//list($arr_neuroVisLoss,$elem_neuroVisLossOther) = separateOtherRvs($neuroVisionLoss);
				
				$neuroHeadaches = $row["neuroHeadaches"];
				$arr_neuroHeadaches = explode(",",$neuroHeadaches);
				
				
				$sepMig = "<+Mig&$+>";
				$neuroMigHead = $row["neuroMigHead"];
				$arrTemp = explode($sepMig,$neuroMigHead);
				$neuroMigHead = separateOtherRvs($arrTemp[0],1);
				$neuroMigHeadAura = separateOtherRvs($arrTemp[1],1);
				//list($arr_neuroMigHead,$elem_neuroMigHeadOther) =  separateOtherRvs($arrTemp[0]);
				//list($arr_neuroMigHeadAura,$elem_neuroMigHeadAuraOther) =  separateOtherRvs($arrTemp[1]);

				$rvspostop = $row["rvspostop"];
				$rvspostop = separateOtherRvs($rvspostop,1);
				///list($arr_fuPostOp,$elem_fuPostOp_other) = separateOtherRvs($rvspostop);
				$rvsfollowup = $row["rvsfollowup"];
				$rvsfollowup = separateOtherRvs($rvsfollowup,1);
				//list($arr_fuFollowUp,$elem_fuFollowUp_other) = separateOtherRvs($rvsfollowup);

				$neuroOther = $row["neuroOther"];
				$elem_neuroOther = $neuroOther;

				//PtInfo Desc
				//$elem_ptinfoDesc = $row["ptinfoDesc"];
				$vpComment = stripslashes($row["vpComment"]);
				$vpDate = FormatDate_show($row["vpDate"]);				
				
			}
		}		
		//--	
		
		$arrMainRet["Vision Problem"]["Distance"]=$vpDistance;
		$arrMainRet["Vision Problem"]["Near"]=$vpNear;
		$arrMainRet["Vision Problem"]["Glare"]="".$vpGlare;
		$arrMainRet["Vision Problem"]["Mid Distance"]=$vpMidDistance;
		$arrMainRet["Vision Problem"]["Other"]=$vpOther;
		$arrMainRet["Vision Problem"]["Patient Comments"]=$vpComment;
		
		$arrMainRet["Irritation"]["Lids - External"]=$irrLidsExternal;
		$arrMainRet["Irritation"]["Ocular"]=$irrOcularTemp;
		
		$arrMainRet["Post Segment"]["Spots"]=$elem_postSegSpots;
		$arrMainRet["Post Segment"]["Flashing Lights:"]=$psFLTemp;
		$arrMainRet["Post Segment"]["Floaters:"]=$psFloatTemp;
		$arrMainRet["Post Segment"]["Amsler Grid:"]=$psAmslerGrid;
		
		
		$arrMainRet["Neuro"]["Double vision:"]=$neuroDblVision;
		$arrMainRet["Neuro"]["Temporal Arteritis symptoms:"]=$neuroTempArtSymp;
		$arrMainRet["Neuro"]["Loss of vision:"]=$neuroVisionLoss;
		$arrMainRet["Neuro"]["Headaches:"]=$neuroHeadaches;
		$arrMainRet["Neuro"]["Migraine Headaches:"]=$neuroMigHead.",".$neuroMigHeadAura;
		
		$arrMainRet["Follow-Up"]["Post op:"]=$rvspostop;
		$arrMainRet["Follow-Up"]["Follow-up:"]=$rvsfollowup;
		
		//complaint
		if(!empty($titleHeadDB)){
			$arrTitleHeadDb = explode(",", $titleHeadDB);
			foreach($arrTitleHeadDb as $key=> $val){
				if(!empty($val)){
					$tmpvar = "complaint".($key+1)."StrDB";
					$arrMainRet["Complaint"][$key] = array("Title"=>$val, "Desc"=>"".$$tmpvar);
				}						
			}
		}		
		
		return $arrMainRet; 		
	}	
}

?>